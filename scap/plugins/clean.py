# -*- coding: utf-8 -*-
"""For cleaning up old MediaWiki."""
import os
import shutil
import subprocess

from scap import cli
from scap import git
from scap import log
from scap import main
from scap import ssh
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
    @cli.argument('--delete-gerrit-branch', action='store_true',
                  help='Delete the branch on gerrit as well.')
    def main(self, *extra_args):
        """Clean old branches from the cluster for space savings."""
        self.arguments.message = 'Pruned MediaWiki: %s' % self.arguments.branch
        if not self.arguments.delete:
            self.arguments.message += ' [keeping static files]'
        self.arguments.force = False
        self.branch_stage_dir = os.path.join(
            self.config['stage_dir'],
            'php-%s' % self.arguments.branch
        )
        self.branch_deploy_dir = os.path.join(
            self.config['deploy_dir'],
            'php-%s' % self.arguments.branch
        )
        return super(Clean, self).main(*extra_args)

    def _before_cluster_sync(self):
        if self.arguments.branch in self.active_wikiversions():
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
        if not os.path.isdir(self.branch_stage_dir):
            raise ValueError('No such branch exists, aborting')

        with log.Timer('clean-l10nupdate-cache', self.get_stats()):
            utils.sudo_check_call(
                'www-data',
                'rm -fR /var/lib/l10nupdate/caches/cache-%s' % branch
            )

        with log.Timer('clean-l10nupdate-owned-files', self.get_stats()):
            utils.sudo_check_call(
                'l10nupdate',
                'find %s -user l10nupdate -delete' % self.branch_stage_dir
            )

        with log.Timer('clean-ExtensionMessages'):
            ext_msg = os.path.join(self.config['stage_dir'], 'wmf-config',
                                   'ExtensionMessages-%s.php' % branch)
            self._maybe_delete(ext_msg)

        logger = self.get_logger()

        if delete:
            # Moved behind a feature flag until T218750 is resolved
            if self.arguments.delete_gerrit_branch:
                git_prune_cmd = [
                    'git', 'push', 'origin', '--quiet', '--delete',
                    'wmf/%s' % branch
                ]
                with log.Timer('prune-git-branches', self.get_stats()):
                    # Prune all the submodules' remote branches
                    with utils.cd(self.branch_stage_dir):
                        submodule_cmd = 'git submodule foreach "{} ||:"'\
                            .format(' '.join(git_prune_cmd))
                        subprocess.check_output(submodule_cmd, shell=True)
                        if subprocess.call(git_prune_cmd) != 0:
                            logger.info('Failed to prune core branch')
            with log.Timer('removing-local-copy'):
                self._maybe_delete(self.branch_stage_dir)
            with log.Timer('cleaning-unused-patches', self.get_stats()):
                patch_base_dir = '/srv/patches'
                self._maybe_delete(os.path.join(patch_base_dir, branch))
                srv_patches_git_message = 'Scap clean for "{}"'.format(branch)
                git.add_all(patch_base_dir, message=srv_patches_git_message)
        else:
            with log.Timer('cleaning-unused-files', self.get_stats()):
                for rmdir in DELETABLE_DIRS:
                    self._maybe_delete(
                        os.path.join(self.branch_stage_dir, rmdir)
                    )

    def _after_cluster_sync(self):
        """
        Need to remove cache dir manually after sync
        """
        branch_cache = os.path.join(self.branch_deploy_dir, 'cache')
        target_hosts = self._get_target_list()
        cmd = ['/bin/rm', '-rf', branch_cache]
        with log.Timer('clean-remote-caches', self.get_stats()):
            remove_remote_dirs = ssh.Job(
                target_hosts,
                user=self.config['ssh_user'],
                key=self.get_keyholder_key()
            )

            remove_remote_dirs.command(cmd)
            remove_remote_dirs.progress(
                log.reporter(
                    'clean-remote-caches',
                    self.config['fancy_progress']
                )
            )
            success, fail = remove_remote_dirs.run()
            if fail:
                self.get_logger().warning(
                    '%d hosts failed to remove cache', fail
                )
                self.soft_errors = True

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
