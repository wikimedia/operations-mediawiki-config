import argparse
import os
import re
import subprocess

import scap.cli as cli
import scap.git as git
import scap.utils as utils


def VersionParser(v):
    try:
        return re.match("(\d+\.\d+(\.\d+-)?wmf\.?\d+|master)", v).group(0)
    except:
        raise argparse.ArgumentTypeError(
            "Branch '%s' does not match required format" % (v,))


@cli.command('prep', help='Checkout MediaWiki version to staging')
class CheckoutMediaWiki(cli.Application):
    gerrit = 'https://gerrit.wikimedia.org/r/p/'
    dest_dir = ''

    """ Scap sub-command to manage checkout new MediaWiki versions """
    @cli.argument('-p', '--prefix', nargs=1, required=False,
                  default='php-', metavar='PREFIX',
                  help='Directory prefix to checkout version to.')
    @cli.argument('branch', metavar='BRANCH', type=VersionParser,
                  help='The name of the branch to operate on.')
    def main(self, *extra_args):
        """ Checkout next MediaWiki """

        self.branch = self.arguments.branch
        self.dest_dir = os.path.join(
            self.config['stage_dir'],
            '{}{}'.format(self.arguments.prefix, self.branch)
        )

        if os.path.isdir(self.dest_dir):
            self.get_logger().info('Version already checked out')
            return 0

        git.fetch(self.dest_dir, self.gerrit + 'mediawiki/core')

        with utils.cd(self.dest_dir):
            if subprocess.call(['/usr/bin/git', 'config',
                                'branch.autosetuprebase', 'always']) != 0:
                self.get_logger().warn('Unable to setup auto-rebase')

        checkout_version = 'master'
        if self.branch != 'master':
            checkout_version = 'wmf/%s' % self.branch

        git.checkout(self.dest_dir, checkout_version)

        if checkout_version == 'master':
            self.master_stuff()
        else:
            git.update_submodules(self.dest_dir, use_upstream=True)
            self.update_submodule_update_strategy(self.dest_dir)

        self.write_localsettings()
        self.create_startprofiler_symlink()

        cache_dir = os.path.join(self.dest_dir, 'cache')
        os.chmod(cache_dir, 0o777)
        utils.sudo_check_call('l10nupdate',
                              'mkdir "%s"' % os.path.join(cache_dir, 'l10n'))

        self.get_logger().info('MediaWiki %s successfully checked out.' %
                               checkout_version)

    def create_startprofiler_symlink(self):
        path = os.path.join(self.dest_dir, 'StartProfiler.php')
        log = self.get_logger()
        if not os.path.exists(path):
            os.symlink('../wmf-config/StartProfiler.php', path)
            log.info('Created StartProfiler symlink')
        else:
            log.warning('StartProfiler symlink already exists')

    def write_localsettings(self):
        ls_file = os.path.join(self.dest_dir, 'LocalSettings.php')
        cs_file = os.path.join(self.config['deploy_dir'],
                               'wmf-config', 'CommonSettings.php')
        ls_stub = (
            '<?php\n# WARNING: This file is publically viewable on the web.' +
            'Do not put private data here.\n' +
            'include_once( "%s" );' % cs_file
        )

        with open(ls_file, 'w+') as f:
            f.write(ls_stub)

    def update_submodule_update_strategy(self, path):
        with utils.cd(path):
            base_cmd = '/usr/bin/git -C %s config ' % path
            base_cmd += 'submodule.$name.update rebase'
            cmd = "/usr/bin/git submodule foreach --recursive '{}'".format(
                base_cmd)
            subprocess.call(cmd, shell=True)

    def master_stuff(self):
        repos = {
            'extensions': 'mediawiki/extensions',
            'vendor': 'mediawiki/vendor',
            'skins': 'mediawiki/skins',
        }

        for dest, upstream in repos.items():
            path = os.path.join(self.dest_dir, dest)
            url = self.gerrit + upstream

            if os.path.exists(path):
                with utils.cd(path):
                    subprocess.check_call(
                        ['/usr/bin/git', 'init'],
                        shell=True
                    )
                    subprocess.check_call(
                        ['/usr/bin/git', 'remote', 'add', 'origin', url],
                        shell=True
                    )

            git.fetch(path, url)
            git.checkout(path, 'master')
            git.update_submodules(path, use_upstream=True)
            self.update_submodule_update_strategy(path)
