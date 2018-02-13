# -*- coding: utf-8 -*-
"""For cleaning up old MediaWiki."""
import os
import shutil
import subprocess

from scap import cli
from scap import git
from scap import log
from scap import main
from scap import utils

# basically everything except extensions, languages, resources and skins
DELETABLE_DIRS = [
    'cache',
    'docs',
    'images',
    'includes',
    'maintenance',
    'mw-config',
    'serialized',
    'tests',
    'vendor',
]


@cli.command('clean')
class Clean(main.AbstractSync):
    """Scap sub-command to clean old branches."""

    @cli.argument('branch', help='The name of the branch to clean.')
    @cli.argument('--delete', action='store_true',
                  help='Delete everything (not just static assets).')
    def main(self, *extra_args):
        """Clean old branches from the cluster for space savings."""
        self.arguments.message = 'Pruned MediaWiki: %s' % self.arguments.branch
        if not self.arguments.delete:
            self.arguments.message += ' [keeping static files]'
        self.arguments.force = False
        return super(Clean, self).main(*extra_args)

    def _before_cluster_sync(self):
        if self.arguments.branch in self.active_wikiversions().keys():
            raise ValueError('Branch "%s" is still in use, aborting' %
                             self.arguments.branch)
        self.cleanup_branch(self.arguments.branch, self.arguments.delete)

    def cleanup_branch(self, branch, delete):
        """
        Given a branch, go through the cleanup proccess on the master.

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

        with log.Timer('clean-l10nupdate-cache', self.get_stats()):
            utils.sudo_check_call(
                'www-data',
                'rm -fR /var/lib/l10nupdate/caches/cache-%s' % branch
            )

        with log.Timer('clean-l10nupdate-owned-files', self.get_stats()):
            utils.sudo_check_call(
                'l10nupdate', 'find %s -user l10nupdate -delete' % stage_dir)

        with log.Timer('clean-ExtensionMessages'):
            ext_msg = os.path.join(self.config['stage_dir'], 'wmf-config',
                                   'ExtensionMessages-%s.php' % branch)
            self._maybe_delete(ext_msg)

        logger = self.get_logger()

        if delete:
            gerrit_prune_cmd = ['git', 'push', 'origin', '--quiet', '--delete',
                                'wmf/%s' % branch]
            with log.Timer('prune-git-branches', self.get_stats()):
                # Prune all the submodules' remote branches
                with utils.cd(stage_dir):
                    subprocess.check_output('git submodule foreach "git push origin --quiet --delete wmf/%s ||:"' % branch, shell=True)
                    if subprocess.call(gerrit_prune_cmd) != 0:
                        logger.info('Failed to prune core branch')
            with log.Timer('removing-local-copy'):
                self._maybe_delete(stage_dir)
            with log.Timer('cleaning-unused-patches', self.get_stats()):
                self._maybe_delete(os.path.join('/srv/patches', branch))
        else:
            with log.Timer('cleaning-unused-files', self.get_stats()):
                for rmdir in DELETABLE_DIRS:
                    self._maybe_delete(os.path.join(stage_dir, rmdir))

    def _maybe_delete(self, path):
        if os.path.exists(path):
            if os.path.isdir(path):
                shutil.rmtree(path)
            else:
                os.remove(path)
        else:
            self.get_logger().info(
                'Unable to delete %s, already missing', path)

    def _after_lock_release(self):
        self.announce(self.arguments.message + ' (duration: %s)' %
                      utils.human_duration(self.get_duration()))
