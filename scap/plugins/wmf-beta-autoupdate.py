"""
Updates MediaWiki core and extensions on the Beta cluster

MUST be run as the `mwdeploy` user although that is not enforced by the script.
"""

import os
import subprocess

import scap.cli as cli
import scap.git as git
import scap.utils as utils


@cli.command('wmf-beta-autoupdate')
class BetaUpdate(cli.Application):
    """ Scap subcommand for auto-updating a beta cluster"""

    def main(self, *extra_args):
        pull_paths = [
            'portal-master',
            'php-master',
            'php-master/extensions',
            'php-master/skins',
            'php-master/vendor',
        ]

        for path in pull_paths:
            path = os.path.join(self.config['stage_dir'], path)
            with utils.cd(path):
                subprocess.check_call('/usr/bin/git pull', shell=True)

        for submodule in ['extensions', 'skins']:
            path = os.path.join(self.config['stage_dir'], 'php-master',
                                submodule)
            git.update_submodules(path, use_upstream=True),
