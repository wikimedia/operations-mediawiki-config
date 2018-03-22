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

        # These are all joined with stage_dir, so '' becomes /srv/mediawiki-staging/, etc.
        # php-master is like php-1.xx.y-wmf.z we use in production but is tracking...master ;-)
        pull_paths = [
            '',
            'php-master',
            'php-master/extensions',
            'php-master/skins',
            'php-master/vendor',
        ]
        stage_dir = self.config['stage_dir']

        for path in pull_paths:
            with utils.cd(os.path.join(stage_dir, path)):
                subprocess.check_call(['/usr/bin/git', 'pull'])
                subprocess.check_call(['/usr/bin/git', 'submodule', 'update',
                                       '--init', '--recursive', '--jobs', '8',
                                       '--rebase'])
        subprocess.check_call(['/usr/bin/git', 'submodule', 'update',
                               '--remote', 'portals'])
