# -*- coding: utf-8 -*-
"""
    scap.plugins.clean
    ~~~~~~~~~~~~~~~~~~
    For cleaning up old MediaWiki
"""
import os
import subprocess

import scap.cli as cli
import scap.git as git
import scap.log as log
import scap.main as main
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
        return super(Clean, self).main(*extra_args)

    def _before_cluster_sync(self):
        if self.arguments.branch in self.active_wikiversions().keys():
            raise ValueError('Branch "%s" is still in use, aborting' %
                             self.arguments.branch)
        self.cleanup_branch(self.arguments.branch, self.arguments.delete)

    def cleanup_branch(self, branch, delete):
        """
        Given a branch, go through the cleanup proccess on the master:

        (1) Prune git branches [if deletion]
        (2) Remove l10nupdate cache
        (3) Remove l10n cache
        (4) Remove l10n bootstrap file
        (5) Remove some branch files [all if deletion]
        (6) Remove security patches [if deletion]
        """
        stage_dir = os.path.join(self.config['stage_dir'], 'php-%s' % branch)
        if not os.path.isdir(stage_dir):
            raise ValueError('No such branch exists, aborting')

        commands = {
            'clean-l10nupdate-cache':
                ['sudo', '-u', 'www-data', 'rm', '-fR',
                 '/var/lib/l10nupdate/caches/cache-%s' % branch],
            'clean-l10nupdate-owned-files':
                ['sudo', '-u', 'l10nupdate', 'find', stage_dir,
                 '-user', 'l10nupdate', '-delete'],
            'clean-l10n-bootstrap':
                ['rm', '-fR', os.path.join(
                    self.config['stage_dir'], 'wmf-config',
                    'ExtensionMessages-%s.php' % branch)],
        }

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
            commands['cleaning-branch'] = ['rm', '-fR', stage_dir]
            commands['cleaning-patches'] = [
                'rm' '-fR', os.path.join('/srv/patches', branch)]
        else:
            regex = r'".*\.?({0})$"'.format('|'.join(DELETABLE_TYPES))
            commands['cleaning-branch'] = ['find', stage_dir, '-type', 'f',
                                           '-regextype', 'posix-extended',
                                           '-regex', regex, '-delete']

        for name, command in commands.iteritems():
            with log.Timer(name + '-' + branch, self.get_stats()):
                subprocess.check_call(command)

    def _after_lock_release(self):
        announce = 'Pruned MediaWiki: %s' % self.arguments.branch
        if not self.arguments.delete:
            announce += ' [keeping static files]'

        self.announce(announce + ' (duration: %s)' %
                      utils.human_duration(self.get_duration()))
