from __future__ import print_function, unicode_literals
import scap.cli as cli
import os.path
import scap.utils as utils
import subprocess

@cli.command('patch', subcommands=True)
@cli.argument('-b', '--branch', nargs=1, required=True,
              help='The name of the branch to operate on.')
class SecurityPatchManager(cli.Application):
    """ Scap sub-command to manage mediawiki security patches """

    def _process_arguments(self, args, extra_args):
        return args, extra_args

    def checkdir(self, name, path):
        if not os.path.isdir(path):
            msg = '%s directory "%s" does not exist.' % (name, path)
            self.get_logger.error(msg)
            raise ValueError(msg)

    @cli.argument('-c', action='store_true', help='Patch mediawiki/core')
    @cli.argument('-e', action='store_true', help='Patch extensions')
    def apply(self, *extra_args):
        """ Apply security patches """

        logger = self.get_logger()

        branch = self.arguments.branch
        if isinstance(branch, list):
            branch = branch.pop()

        self.patchdir = os.path.join('/srv/patches/', branch)
        self.checkdir('patch', self.patchdir)

        self.branchdir = './php-%s' % branch
        self.checkdir('branch', self.branchdir)

        with utils.cd(self.branchdir):
            for base, path, filename in self.get_patches(''):
                if (path == 'core'):
                    repo_dir="./"
                else:
                    repo_dir=os.path.join('./', path)
                patchfile = os.path.join(base,path,filename)

                if not self.apply_patch(patchfile, repo_dir):
                    logger.error('error applying patch, aborting')
                    return 1


    def get_patches(self, path):
        patchdir = os.path.join(self.patchdir, path)
        prefix_length = len(self.patchdir)+1
        for folder, subdirs, files in os.walk(patchdir):
            for filename in files:
                #filename = os.path.join(folder, filename)[prefix_length:]
                yield self.patchdir, folder[prefix_length:], filename

    def check_patch(self, patch):
        return subprocess.call('git', 'apply', '--check', '-3', patch) == 0

    def apply_patch(self, patch, repo_dir='./'):
        with utils.cd(repo_dir):
            if subprocess.call('git', 'apply', '--check', '-3', patch) > 0:
                return False
            return subprocess.call('git', 'am', '-3', patch)
