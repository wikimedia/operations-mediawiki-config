# -*- coding: utf-8 -*-
"""For updating + syncing the interwiki cache."""
import errno
import os
import subprocess

import scap.cli as cli
import scap.lint as lint
import scap.main as main
import scap.utils as utils


@cli.command('update-interwiki-cache')
class UpdateInterwikiCache(main.SyncFile):
    """Scap sub-command to update and sync the interwiki cache."""

    @cli.argument('--force', action='store_true', help='Skip canary checks')
    def main(self, *extra_args):
        """Update the latest interwiki cache."""
        self.arguments.message = 'Update interwiki cache'
        self.arguments.file = os.path.join('wmf-config', 'interwiki.php')
        return super(UpdateInterwikiCache, self).main(*extra_args)

    def _before_cluster_sync(self):
        interwikifile = os.path.join(
            self.config['stage_dir'], self.arguments.file)
        if not os.path.exists(interwikifile):
            raise IOError(
                errno.ENOENT, 'File/directory not found', interwikifile)

        relpath = os.path.relpath(interwikifile, self.config['stage_dir'])
        self.include = relpath

        with open(interwikifile, 'w') as outfile:
            subprocess.check_call(
                ['/usr/local/bin/mwscript',
                 'extensions/WikimediaMaintenance/dumpInterwiki.php'],
                stdout=outfile
            )

        # This shouldn't happen, but let's be safe
        lint.check_valid_syntax(interwikifile)

        subprocess.check_call(['/usr/bin/git', 'add', interwikifile])
        subprocess.check_call(['/usr/bin/git', 'commit', '-q', '-m',
                               self.arguments.message])

        subprocess.check_call(['/usr/bin/git', 'push', '-q', 'origin',
                               'HEAD:refs/for/master%l=Code-Review+2'])

        if not utils.confirm('Has your change merged yet?'):
            subprocess.check_call(['/usr/bin/git', 'reset', '--hard',
                                   'origin/master'])
            raise RuntimeError('Aborting, you should not sync unmerged code')

        subprocess.check_call(['/usr/bin/git', 'pull', '-q'])
