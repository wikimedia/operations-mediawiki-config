# -*- coding: utf-8 -*-
"""Scap plugin for setting up a new version of MediaWiki for deployment."""
import argparse
import multiprocessing
import os
import re
import shutil
import subprocess

from scap import cli
from scap import git
from scap import utils

SOURCE_URL = 'https://gerrit.wikimedia.org/r/'


def version_parser(ver):
    """Validate our version number formats."""
    try:
        return re.match(r"(1\.\d\d\.\d+-wmf\.\d+|master)", ver).group(0)
    except re.error:
        raise argparse.ArgumentTypeError(
            "Branch '%s' does not match required format" % ver)


def update_update_strategy(path):
    """For all submodules, update the merge strategy."""
    with utils.cd(path):
        base_cmd = '/usr/bin/git -C %s config ' % path
        base_cmd += 'submodule.$name.update rebase'
        cmd = "/usr/bin/git submodule foreach --recursive '%s'" % base_cmd
        subprocess.call(cmd, shell=True)


def write_settings_stub(dest):
    """Write a silly little PHP file that includes another."""
    file_stub = (
        '<?php\n' +
        '# Managed by scap (mediawiki-config:/scap/plugins/prep.py)\n' +
        '# WARNING: This file is publically viewable on the web. ' +
        'Do not put private data here.\n' +
        'require __DIR__ . "/../wmf-config/CommonSettings.php";'
    )
    with open(dest, 'w+') as destfile:
        destfile.write(file_stub)


def master_stuff(dest_dir):
    """If we're operating on a master branch, do some extra weird stuff."""
    repos = {
        'extensions': 'mediawiki/extensions',
        'vendor': 'mediawiki/vendor',
        'skins': 'mediawiki/skins',
    }

    for dest, upstream in repos.items():
        path = os.path.join(dest_dir, dest)
        url = SOURCE_URL + upstream

        if os.path.exists(path):
            with utils.cd(path):
                subprocess.check_call(
                    ['/usr/bin/git', 'init']
                )
                subprocess.check_call(
                    ['/usr/bin/git', 'remote', 'add', 'origin', url]
                )

        git.fetch(path, url)
        git.checkout(path, 'master')
        git.update_submodules(path, use_upstream=True)
        update_update_strategy(path)


@cli.command('prep', help='Checkout MediaWiki version to staging')
class CheckoutMediaWiki(cli.Application):
    """Scap sub-command to manage checkout new MediaWiki versions."""

    @cli.argument('-p', '--prefix', nargs=1, required=False,
                  default='php-', metavar='PREFIX',
                  help='Directory prefix to checkout version to.')
    @cli.argument('branch', metavar='BRANCH', type=version_parser,
                  help='The name of the branch to operate on.')
    def main(self, *extra_args):
        """Checkout next MediaWiki."""
        dest_dir = os.path.join(
            self.config['stage_dir'],
            '{}{}'.format(self.arguments.prefix, self.arguments.branch)
        )

        checkout_version = 'master'
        if self.arguments.branch != 'master':
            checkout_version = 'wmf/%s' % self.arguments.branch

        reference_dir = None
        if checkout_version != 'master':
            # active_wikiversions() is already sorted by loose-version number,
            # we want the latest version if there's more than 1
            old_branch = self.active_wikiversions().keys()[-1]
            old_branch_dir = os.path.join(
                self.config['stage_dir'],
                '{}{}'.format(self.arguments.prefix, old_branch)
            )
            reference_dir = None
            if (os.path.exists(old_branch_dir)):
                reference_dir = old_branch_dir
            patch_path = os.path.join('/srv/patches', self.arguments.branch)
            if not os.path.exists(patch_path):
                if os.path.exists(os.path.join('/srv/patches', old_branch)):
                    shutil.copytree(
                        os.path.join('/srv/patches', old_branch),
                        os.path.join(patch_path)
                    )

        if os.path.isdir(dest_dir):
            self.get_logger().info('Version already checked out')
            return 0

        self.get_logger().info('Fetching core to {}'.format(dest_dir))
        git.fetch(dest_dir, SOURCE_URL + 'mediawiki/core', reference_dir)

        with utils.cd(dest_dir):
            if subprocess.call(['/usr/bin/git', 'config',
                                'branch.autosetuprebase', 'always']) != 0:
                self.get_logger().warn('Unable to setup auto-rebase')

            num_procs = str(max(multiprocessing.cpu_count() / 2, 1))
            if subprocess.call(['/usr/bin/git', 'config',
                                'submodule.fetchJobs', num_procs]) != 0:
                self.get_logger().warn('Unable to setup submodule fetch jobs')

        self.get_logger().info('Checkout {} in {}'.format(checkout_version, dest_dir))
        git.checkout(dest_dir, checkout_version)

        if checkout_version == 'master':
            # Specific to Beta Cluster
            master_stuff(dest_dir)
        else:
            # Specific to production
            self.get_logger().info('Update submodules for {}'.format(dest_dir))
            git.update_submodules(dest_dir, use_upstream=True)
            update_update_strategy(dest_dir)

        self.get_logger().info('Creating LocalSettings.php stub')
        write_settings_stub(os.path.join(dest_dir, 'LocalSettings.php'))

        self.get_logger().info('Creating l10n cache dir')
        cache_dir = os.path.join(dest_dir, 'cache')
        os.chmod(cache_dir, 0o777)
        utils.sudo_check_call('l10nupdate',
                              'mkdir "%s"' % os.path.join(cache_dir, 'l10n'))

        self.get_logger().info('MediaWiki %s successfully checked out.' %
                               checkout_version)
