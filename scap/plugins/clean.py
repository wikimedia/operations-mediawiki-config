import os
import socket

import scap.cli as cli
import scap.log as log
import scap.main as main
import scap.ssh as ssh


@cli.command('clean')
class Clean(main.AbstractSync):
    """ Scap sub-command to clean old branches """

    @cli.argument('branch', help='The name of the branch to clean.')
    @cli.argument('--l10n-only', action='store_true',
                  help='Only prune old l10n cache files.')
    def main(self, *extra_args):
        """ Clean old branches from the cluster for space savings! """

        # Update masters
        self.execute_remote(
            'clean-masters',
            self._get_master_list(),
            self.clean_command(self.config['stage_dir'])
        )

        # Update apaches
        self.execute_remote(
            'clean-apaches',
            self._get_target_list(),
            self.clean_command(self.config['deploy_dir'])
        )

    def execute_remote(self, description, targets, command):
        with log.Timer(description, self.get_stats()):
            clean_job = ssh.Job(targets, user=self.config['ssh_user'])
            clean_job.shuffle()
            clean_job.command(command)
            clean_job.progress(log.reporter(description,
                               self.config['fancy_progress']))
            succeeded, failed = clean_job.run()
            if failed:
                self.get_logger().warning('%d had clean errors', failed)

    def clean_command(self, location):
        path = os.path.join(location, 'php-%s' % self.arguments.branch)
        if self.arguments.l10n_only:
            path = os.path.join(path, 'cache', 'l10n', '*.cdb')
        return 'rm -fR %s' % path
