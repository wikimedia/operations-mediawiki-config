# -*- coding: utf-8 -*-
"""
    scap.plugins.updateinterwikicache
    ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    For updating + syncing the interwiki cache
"""
import os
import subprocess

import scap.cli as cli
import scap.main as main
import scap.tasks as tasks
import scap.utils as utils


@cli.command('update-interwiki-cache')
class UpdateInterwikiCache(main.SyncFile):
    """ Scap sub-command to update and sync the interwiki cache """

    def main(self, *extra_args):
        """ Update the latest interwiki cache! """
        self.arguments.message = 'Updating interwiki cache'
        return super(UpdateInterwikiCache, self).main(*extra_args)

    def _before_cluster_sync(self):
        interwikifile = os.path.join(self.config['stage_dir'], 'wmf-config',
                                     'interwiki.php')
        self.include = os.path.relpath(interwikifile, self.config['stage_dir'])

        with open(interwikifile) as outfile:
            subprocess.check_call(
                ['/usr/local/bin/mwscript',
                 'extensions/WikimediaMaintenance/dumpInterwiki.php'],
                stdout=outfile
            )

        # This shouldn't happen, but let's be safe
        tasks.check_valid_syntax(interwikifile)

        subprocess.check_call(['/usr/bin/git', 'commit', '-q', '-m',
                               self.arguments.message, self.arguments.include])

        subprocess.check_call(['/usr/bin/git', 'push', '-q', 'origin',
                               'HEAD:refs/for/master%l=Code-Review+2'])

        if not utils.confirm('Has Jenkins merged your gerrit change yet?'):
            subprocess.check_call(['/usr/bin/git', 'reset', '--hard',
                                   'origin/master'])
            raise RuntimeError('Aborting, you should not sync unmerged code')

        subprocess.check_call('/usr/bin/git', 'pull')
