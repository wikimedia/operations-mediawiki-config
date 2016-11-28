import os

import scap.cli as cli
import scap.log as log
import scap.ssh as ssh


@cli.command('clean')
class Clean(cli.Application):
    """ Scap sub-command to clean old branches """

    @cli.argument('branch', help='The name of the branch to clean.')
    def main(self, *extra_args):
        """ Clean old branches from the cluster for space savings! """
        # Update apaches
        with log.Timer('clean-apaches', self.get_stats()):
            clean_job = ssh.Job(self._get_target_list(),
                                user=self.config['ssh_user'])
            clean_job.shuffle()
            clean_job.command(self._clean_command())
            clean_job.progress('clean-apaches')
            succeeded, failed = clean_job.run()
            if failed:
                self.get_logger().warning(
                    '%d apaches had clean errors', failed)

    def _clean_command(self):
        return 'rm -fR %s' % os.path.join(
            self.config['deploy_dir'], 'php-%s' % self.arguments.branch)
