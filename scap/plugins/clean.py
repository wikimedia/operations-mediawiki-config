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
    @cli.argument('--keep-static', action='store_true',
                  help='Only keep static assets (CSS/JS and the like).')
    def main(self, *extra_args):
        """ Clean old branches from the cluster for space savings! """

        stage_dir = os.path.join(
            self.config['stage_dir'], 'php-%s' % self.arguments.branch)
        deploy_dir = os.path.join(
            self.config['deploy_dir'], 'php-%s' % self.arguments.branch)
        gerrit_prune_cmd = ['git', 'push', '--force', 'origin',
                            ':wmf/%s' % self.arguments.branch]
        logger = self.get_logger()

        with log.Timer('prune-git-branches'):
            # Prune all the submodules' remote branches
            for submodule in git.list_submodules(stage_dir):
                with utils.cd(os.path.join(stage_dir, submodule)):
                    if subprocess.call(cmd) != 0:
                        logger.info('Failed to prune submodule branch for %s' %
                                    submodule)

            # Prune core last
            with utils.cd(stage_dir):
                if subprocess.call(cmd) != 0:
                    logger.info('Failed to prune core branch')

        # Update masters
        with log.Timer('clean-masters', self.get_stats()):
            clean_job = ssh.Job(self._get_master_list(),
                                user=self.config['ssh_user'])
            clean_job.exclude_hosts([socket.getfqdn()])
            clean_job.shuffle()
            clean_job.command(self._clean_command(stage_dir))
            clean_job.progress(log.reporter('clean-masters',
                               self.config['fancy_progress']))
            succeeded, failed = clean_job.run()
            if failed:
                self.get_logger().warning(
                    '%d masters had clean errors', failed)

        # Update apaches
        with log.Timer('clean-apaches', self.get_stats()):
            clean_job = ssh.Job(self._get_target_list(),
                                user=self.config['ssh_user'])
            clean_job.shuffle()
            clean_job.command(self._clean_command(deploy_dir))
            clean_job.progress(log.reporter('clean-apaches',
                               self.config['fancy_progress']))
            succeeded, failed = clean_job.run()
            if failed:
                self.get_logger().warning(
                    '%d apaches had clean errors', failed)

    def _clean_command(self, path):
        regex = '.*\.(gif|jpe?g|png|css|js|json|woff|woff2|svg|eot|ttf|ico)'
        if self.arguments.keep_static:
            return ['find', path, '-regextype', 'posix-extended', '-not',
                    '-regex', regex, '-delete']
        else:
            return ['rm', '-fR', path]
