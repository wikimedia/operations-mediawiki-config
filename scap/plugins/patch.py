from __future__ import unicode_literals

import os.path
import subprocess

import scap.cli as cli
import scap.utils as utils


class PatchError(Exception):
    """Exception raised for errors applying a patch.

    Attributes:
        msg  -- explanation of the error.
        patch -- the name of the patch that failed.
    """

    def __init__(self, msg, patch):
        self.msg = msg
        self.patch = patch

    def __str__(self):
        return self.msg


@cli.command('patch')
class SecurityPatchManager(cli.Application):
    """ Scap sub-command to manage mediawiki security patches """

    def _process_arguments(self, args, extra_args):
        return args, extra_args

    @cli.argument('--check-only', action='store_true',
                  help='Just check if patches apply cleanly.')
    @cli.argument('branch', help='The name of the branch to operate on.')
    def main(self, *extra_args):
        """ Apply security patches """

        logger = self.get_logger()

        branch = self.arguments.branch

        if branch.startswith('php-'):
            branch = branch[4:]

        branch = branch.rstrip('/')
        stage_dir = self.config.get('stage_dir', './')

        self.patchdir = os.path.join('/srv/patches/', branch)
        self.checkdir('patch', self.patchdir)

        self.branchdir = os.path.join(stage_dir, 'php-%s' % branch)
        self.checkdir('branch', self.branchdir)

        with utils.cd(self.branchdir):
            for base, path, filename in self.get_patches():
                repo_dir = './' if path == 'core' else os.path.join('./', path)
                patchfile = os.path.join(base, path, filename)
                try:
                    self.apply_patch(patchfile, repo_dir)
                except PatchError as e:
                    os.rename(e.patch, e.patch + ".failed")
                    logger.exception(e)

    def checkdir(self, name, path):
        if not os.path.isdir(path):
            msg = '%s directory "%s" does not exist.' % (name, path)
            self.get_logger().error(msg)
            raise ValueError(msg)

    def get_patches(self, path=''):
        patchdir = os.path.join(self.patchdir, path)
        prefix_length = len(self.patchdir) + 1
        for folder, subdirs, files in os.walk(patchdir):
            for filename in files:
                if filename.endswith('.failed'):
                    self.get_logger().warning('Skipping failed patch: %s/%s',
                                              folder, filename)
                else:
                    yield self.patchdir, folder[prefix_length:], filename

    def check_patch(self, patch):
        try:
            subprocess.check_call(['git', 'apply', '--check', '-3', patch])
        except subprocess.CalledProcessError as ex:
            msg = 'Patch %s failed to apply: %s' % (patch, ex.output)
            raise PatchError(msg, patch)

    def apply_patch(self, patch, repo_dir='./'):
        with utils.cd(repo_dir):
            self.check_patch(patch)
            if not self.arguments.check_only:
                self.get_logger().info(
                    'In %s, Applying patch: %s' % (repo_dir, patch))
                try:
                    subprocess.check_call(['git', 'am', '-3', patch])
                except subprocess.CalledProcessError as ex:
                    msg = 'Patch %s failed to apply: %s' % (patch, ex.output)
                    raise PatchError(msg, patch)
