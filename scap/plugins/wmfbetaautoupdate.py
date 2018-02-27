# -*- coding: utf-8 -*-
"""
Updates MediaWiki core and extensions on the Beta cluster.

MUST be run as the `mwdeploy` user although that is not enforced by the script.
"""

import os
import subprocess

import scap.cli as cli
import scap.git as git
import scap.utils as utils


@cli.command('wmf-beta-autoupdate')
class BetaUpdate(cli.Application):
    """Scap subcommand for auto-updating a beta cluster."""

    def main(self, *extra_args):
        """Do all kinds of weird stuff for beta."""
        pull_paths = [
            '',
            'php-master',
            'php-master/extensions',
            'php-master/skins',
            'php-master/vendor',
        ]
        stage_dir = self.config['stage_dir']

        for path in pull_paths:
            path = os.path.join(self.config['stage_dir'], path)
            with utils.cd(os.path.join(stage_dir, path)):
                subprocess.check_call(['/usr/bin/git', 'pull'])
                subprocess.check_call(['/usr/bin/git', 'submodule', 'update',
                                       '--init', '--recursive', '--remote'])

        for submodule in ['extensions', 'skins']:
            path = os.path.join(stage_dir, 'php-master', submodule)
            git.update_submodules(path, use_upstream=True)
