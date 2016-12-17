import json
import sh
import time

import scap.cli as cli
import scap.terminal as terminal
import scap.utils as utils
import scapext.gerrit as gerrit

from scapext.iter import SubdirectoryIterator


git = sh.git

with open('./multiversion/submodules.json') as f:
    submodules = json.load(f)


@cli.command('branch', subcommands=True)
class WmfBranch(cli.Application):
    """
    Manage wmf/ branches
    """

    def project_branches(self, project, pattern='wmf/.*'):
        """
        get a mapping of { branch: sha1 } for all branches matching the regex
        `pattern` in a given gerrit `project`
        """
        branches = gerrit.ProjectBranches(project)
        return {branch.ref[11:]: branch.revision for (branch) in
                branches.get(params={'r': pattern})}

    @cli.argument('project', help='Gerrit project name')
    @cli.subcommand('list')
    def list(self, extra_args):
        """ List wmf branches in project, sorted by version """
        project_branches = self.project_branches(self.arguments.project)
        sorted_branches = sorted(project_branches)
        sorted_branches = [{key: project_branches[key]} for key in
                           sorted_branches]
        gerrit.dump_json(sorted_branches)

    @cli.argument('-o', '--old', default='master')
    @cli.argument('-n', '--new', required=True)
    @cli.subcommand('foreach')
    def foreach(self, extra_args):
        term = terminal.term
        # paths = [m for m in git.list_submodules()]
        paths = sorted(submodules.items())

        old = self.arguments.old
        branch = self.arguments.new
        branch_dir = "php-" + branch

        def checkout_submodule(path, data):
            action = data.get('action')
            ref = data.get('ref', '.')
            if (ref == '.'):
                ref = old

            repo = '../' + path
            project = "mediawiki/" + path

            if action == "checkout-latest":
                # get /projects/$path/branches/?m=$ref
                branches = self.project_branches(project, pattern=ref)
                branches = sorted(branches)
                ref = branches[-1]
                try:
                    git('submodule', 'add', '--branch', ref, repo, path)
                except:
                    utils.mkdir_p(path)
            elif action == 'branch':
                oldbranch = gerrit.ProjectBranch(project, ref)
                rev = oldbranch.get()
                branch_api = gerrit.ProjectBranches(project)
                branch_api.create(branch, rev.revision)
                return git('submodule', 'add', '--branch', branch, repo, path)
            elif action == 'checkout':
                return git('submodule', 'add', '--branch', ref, repo, path)

        with utils.cd(branch_dir):
            dlist = SubdirectoryIterator(paths, on_missing=checkout_submodule)

            term.nl()

            for progress in dlist:
                term.writeln(repr(progress))
                # res = gitcmd.run('describe', '--all', '--always')
                # res = gitcmd.run('log', '--oneline', '-2')
                res = git.log('--oneline', '-1')
                term.write(res)


        # print('Completed: %s' % self.completed)
