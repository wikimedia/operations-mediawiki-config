from __future__ import division, absolute_import
from __future__ import print_function, unicode_literals

import scap.cli as cli
import scap.utils as utils
from scap.plugins.gerrit import Changes, Change, ChangeDetail
from scap.plugins.gerrit import dump_json, parse_gerrit_uri


@cli.command('swat', help='Mediawiki SWAT deployment helper.')
class Swat(cli.Application):
    '''
    scap swat: cherry-pick and deploy patches for Mediawiki SWAT.

    Usage Examples:
    ----------------
    Query changes from gerrit and display the results:
        scap swat --search 'status:open'

    Cherry-pick a patch to active deployment branches:
        scap swat --pick 123456

    Specify a specific branch target:
        scap swat --pick 123456 --branch 1.28.0-wmf.1

    Diplay detailed information about a change in gerrit:
        scap swat --changeid 123456

    More info: https://wikitech.wikimedia.org/wiki/SWAT_deploys
    '''
    @cli.argument('-p', '--pick', nargs='+',
                  help='cherry-pick patches to release branches')
    @cli.argument('-b', '--branch', nargs="?",
                  help='One or more branches to merge into.'
                  + ' Default: active wmf/branches.')
    @cli.argument('--changeid', nargs="+",
                  help='The ChangeId of a patch to merge.')
    @cli.argument('--rebase', action='store_true',
                  help='Rebase on tip of branch.')
    @cli.argument('--revert', action='store_true',
                  help='Revert a change.')
    @cli.argument('--search', nargs=1,
                  help='Search for patches using Gerrit query terms.')
    @cli.argument('-m', '--message', nargs=1,
                  help='Include a message or comment with your action.')
    def main(self, *extra_args):
        if not self.arguments.branch:
            self.branches = self.active_wikiversions().keys()
        elif type(self.arguments.branch) is not list:
            self.branches = [self.arguments.branch]
        else:
            self.branches = self.arguments.branch

        if self.arguments.pick:
            return self.cherrypick(self.arguments.pick)

        if self.arguments.revert:
            return self.revert(self.arguments.changeid)

        if self.arguments.search:
            return self.query(self.arguments.search)

        if self.arguments.changeid:
            return self.changes(self.arguments.changeid)

        print(self.__doc__)

    def cherrypick(self, changeids):
        ''' cherry pick one or more changes to the active wiki branches '''

        for changeid in changeids:
            print('Cherry-picking %s' % changeid)
            changeid = parse_gerrit_uri(changeid)
            change = ChangeDetail(changeid)
            change.get()

            for branch in self.branches:
                branch = 'wmf/%s' % branch
                print(' to %s' % branch)
                data = {
                    'message': 'Cherry-pick for SWAT deployment.',
                    'destination': branch
                }
                res = change.revision('cherrypick').post(data=data)
                print(res.text)

    def query(self, query):
        ''' execute a gerrit query '''
        changes = Changes()
        res = changes.query(query)
        dump_json(res)

    def revert(self, changeids):
        ''' revert a change in gerrit '''
        message = self.arguments.message
        for changeid in changeids:
            change = Change(changeid)
            try:
                res = change('revert').post(data={"message": message})
                dump_json(res)
            except Exception as e:
                print(e.message)

    def changes(self, changeids):
        ''' get change details '''
        for changeid in changeids:
            changeid = parse_gerrit_uri(changeid)
            change = ChangeDetail(changeid)
            change.get()
            code_review = change.data.labels['Code-Review']
            del change.data.messages
            del change.data.removable_reviewers
            del change.data.labels
            del change.data.permitted_labels
            dump_json(change.data)

            print("Votes:")
            for vote in code_review.all:
                value = vote.value
                if value > 0:
                    value = "+%s" % value
                print("  {:<22}{:<2}".format(vote.name, value))
            actions = change.revision('actions').get()
            if not change.data.submittable and actions.rebase.enabled:
                print('This change is not based on the current branch tip.')
                if not self.arguments.rebase:
                    print("\n")
                    prompt = 'Rebase on top of {}?'.format(change.data.branch)
                    if not utils.ask(prompt, 'n', '[y/n]') == 'y':
                        print('Cannot merge without rebase.')
                        return 1

                # Rebase on top of the target branch
                res = change.revision('rebase').post(data={"base": ''})
                dump_json(res)

    def _process_arguments(self, args, extra_args):
        ''' extra command line arguments get treated as change-ids'''
        if len(extra_args):
            args.changeid = []
            for arg in extra_args:
                args.changeid.append(parse_gerrit_uri(arg))
            extra_args = []

        return args, extra_args
