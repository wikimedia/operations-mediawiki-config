import os
import socket
import subprocess

import scap.cli as cli
import scap.git as git
import scap.log as log
import scap.main as main
import scap.ssh as ssh
import scap.utils as utils

STATIC_TYPES = [
    'gif',
    'jpg',
    'jpeg',
    'png',
    'css',
    'js',
    'json',
    'woff',
    'woff2',
    'svg',
    'eot',
    'ttf',
    'ico'
]


@cli.command('clean')
class Clean(main.AbstractSync):
    """ Scap sub-command to clean old branches """

    @cli.argument('branch', help='The name of the branch to clean.')
    @cli.argument('--keep-static', action='store_true',
                  help='Only keep static assets (CSS/JS and the like).')
    def main(self, *extra_args):
        """ Clean old branches from the cluster for space savings! """
        if self.arguments.branch in self.active_wikiversions():
            raise ValueError('Branch "%s" is still in use, aborting' %
                             self.arguments.branch)
        self.cleanup_branch(self.arguments.branch, self.arguments.keep_static)

    def cleanup_branch(self, branch, keep_static):
        stage_dir = os.path.join(self.config['stage_dir'], 'php-%s' % branch)
        deploy_dir = os.path.join(self.config['deploy_dir'], 'php-%s' % branch)

        if not keep_static:
            gerrit_prune_cmd = ['git', 'push', 'origin', '--delete',
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
            ['sudo', '-u', 'l10nupdate', 'rm', '-fR',
             '/var/lib/l10nupdate/caches/cache-%s' % branch]
        )

        # Prune junk from masters owned by l10nupdate
        self.execute_remote(
            'clean-masters-l10nupdate',
            self._get_master_list(),
            ['sudo', '-u', 'l10nupdate', 'find', stage_dir,
             '-user', 'l10nupdate', '-delete']
        )

        # Update masters
        self.execute_remote(
            'clean-masters',
            self._get_master_list(),
            self.clean_command(stage_dir, keep_static)
        )

        # Update apaches
        self.execute_remote(
            'clean-apaches',
            self._get_target_list(),
            self.clean_command(deploy_dir, keep_static)
        )

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

    def clean_command(self, path, keep_static):
        regex = '".*\.(%s)"' % ('|'.join(STATIC_TYPES))
        if keep_static:
            return ['find', path, '-type', 'f', '-regextype', 'posix-extended',
                    '-not', '-regex', regex, '-delete']
        else:
            return ['rm', '-fR', path]
