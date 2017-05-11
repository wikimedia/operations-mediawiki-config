import os
import socket
import subprocess

import scap.cli as cli
import scap.git as git
import scap.log as log
import scap.main as main
import scap.ssh as ssh
import scap.utils as utils

DELETABLE_TYPES = [
    'arcconfig',
    'arclint',
    'cdb',
    'COPYING',
    'CREDITS',
    'FAQ',
    'Gemfile',
    'HISTORY',
    'ini',
    'inc',
    'jshintignore',
    'jscsrc',
    'jshintrc',
    'lock',
    'md',
    'md5',
    'mailmap',
    'Makefile'
    'ml',
    'mli',
    'php',
    'py',
    'rb',
    'README',
    'sample',
    'sh',
    'sql',
    'stylelintrc',
    'txt',
    'xsd',
]


@cli.command('clean')
class Clean(main.AbstractSync):
    """ Scap sub-command to clean old branches """

    @cli.argument('branch', help='The name of the branch to clean.')
    @cli.argument('--delete', action='store_true',
                  help='Delete everything (not just static assets).')
    def main(self, *extra_args):
        """ Clean old branches from the cluster for space savings! """
        if self.arguments.branch in self.active_wikiversions():
            raise ValueError('Branch "%s" is still in use, aborting' %
                             self.arguments.branch)
        self.cleanup_branch(self.arguments.branch, self.arguments.delete)

    def cleanup_branch(self, branch, delete):
        stage_dir = os.path.join(self.config['stage_dir'], 'php-%s' % branch)
        deploy_dir = os.path.join(self.config['deploy_dir'], 'php-%s' % branch)

        if delete:
            gerrit_prune_cmd = ['git', 'push', 'origin', '--quiet', '--delete',
                                'wmf/%s' % branch]
            logger = self.get_logger()
            with log.Timer('prune-git-branches', self.get_stats()):
                # Prune all the submodules' remote branches
                for submodule in git.list_submodules(stage_dir):
                    submodule_path = submodule.lstrip(' ').split(' ')[1]
                    with utils.cd(os.path.join(stage_dir, submodule_path)):
                        if subprocess.call(gerrit_prune_cmd) != 0:
                            logger.info(
                                'Failed to prune submodule branch for %s' %
                                submodule)

                # Prune core last
                with utils.cd(stage_dir):
                    if subprocess.call(gerrit_prune_cmd) != 0:
                        logger.info('Failed to prune core branch')

        # Prune cache junk from masters used by l10nupdate
        self.execute_remote(
            'clean-masters-l10nupdate-cache',
            self._get_master_list(),
            ['sudo', '-u', 'www-data', 'rm', '-fR',
             '/var/lib/l10nupdate/caches/cache-%s' % branch]
        )

        # Prune junk from masters owned by l10nupdate
        self.execute_remote(
            'clean-masters-l10nupdate',
            self._get_master_list(),
            ['sudo', '-u', 'l10nupdate', 'find', stage_dir,
             '-user', 'l10nupdate', '-delete']
        )

        # Update active master (passive gets it on next sync)
        master_command = ' '.join(self.clean_command(stage_dir, delete))
        subprocess.check_call(master_command, shell=True)

        # Update apaches
        self.execute_remote(
            'clean-apaches',
            self._get_target_list(),
            self.clean_command(deploy_dir, delete)
        )

        announce = 'Pruned MediaWiki: %s' % branch
        if not delete:
            announce += ' [keeping static files]'

        self.announce(announce + ' (duration: %s)' %
                      utils.human_duration(self.get_duration()))

    def execute_remote(self, description, targets, command):
        with log.Timer(description, self.get_stats()):
            clean_job = ssh.Job(targets, user=self.config['ssh_user'])
            clean_job.shuffle()
            clean_job.command(command)
            clean_job.progress(log.reporter(description,
                               self.config['fancy_progress']))
            succeeded, failed = clean_job.run()
            if failed:
                self.get_logger().warning('%d had clean errors', failed)

    def clean_command(self, path, delete):
        regex = '".*\.?(%s)$"' % ('|'.join(DELETABLE_TYPES))
        if delete:
            return ['rm', '-fR', path]
        else:
            return ['find', path, '-type', 'f', '-regextype', 'posix-extended',
                    '-regex', regex, '-delete']
