"""
    scap.plugins.swat
    ~~~~~~~~~~~~~~~~~

    Implements the `scap swat` command which is a helper
    to automate some of the process for deploying mediawiki
    hotfixes.
"""
from __future__ import print_function, unicode_literals

import sys
import scap.cli as cli
import scap.utils as utils
import scap.ansi as ansi
import scap.plugins.gerrit as gerrit


@cli.command('swat', help='Mediawiki SWAT deployment helper.',
             subcommands=True)
@cli.argument('-b', '--branch', nargs=1,
              help='One or more branches to merge into.' +
              'Default: active wmf/branches.')
@cli.argument('-m', '--message', nargs="+", default="",
              help='Include a message or comment with your action.')
class Swat(cli.Application):
    '''
    scap swat: cherry-pick and deploy patches for Mediawiki SWAT.

    Usage Examples:
    ----------------
    Query changes from gerrit and display the results:
        scap swat search 'status:open'

    Cherry-pick a patch to active deployment branches:
        scap swat pick 123456

    Specify a specific branch target:
        scap swat pick 123456 --branch 1.28.0-wmf.1

    Diplay detailed information about a change in gerrit:
        scap swat show 123456

    Merge a patch
        scap swat merge 123456

    More info: https://wikitech.wikimedia.org/wiki/SWAT_deploys
    '''
    @cli.argument('changeid', nargs='+', default=[],
                  help='Change-id or gerrit patch url')
    def merge(self, *extra_args):
        """ scap swat """
        logger = self.get_logger()
        for changeid in self.changeids:
            change = gerrit.ChangeDetail(changeid)
            data = change.get()
            self.display_change(change)
            actions = change.revision('actions').get()
            if actions.rebase.enabled:
                # if the change is not submittable, attempt to rebase
                prompt = 'This change must be rebased on top of {}, continue?'
                prompt = prompt.format(change.data.branch)
                bail = 'Cannot merge without rebase.'
                self.confirm_if_flag_missing('rebase', prompt, False, bail)
                # Rebase on top of the target branch
                res = change.revision('rebase').post(data={"base": ''})
                gerrit.dump_json(res)
            # refresh change details
            change.get()

            def submit():

                if utils.confirm('Submit the change?', True):
                    change.revision('submit').post()

            def approve():
                logger.debug("Approval Message: %s", self.message)
                if utils.confirm('Vote +2 and submit?', True):
                    reviewinput={
                        "message": self.message,
                        "labels": {
                            "Code-Review": "+2"
                        },
                        "comments": {},
                        "drafts": "KEEP"
                    }
                    change.revision('review').post(data=reviewinput)
                    submit()

            if change.data.submittable:
                submit()
            else:
                approve()

    def arg_val(self, name, default=None):
        """ return a single value for the named cli argument """
        arg = getattr(self.arguments, name, default)
        if isinstance(arg, list) and len(arg) == 1:
            return arg.pop()
        return arg

    def arg_values(self, name, default=[]):
        """ return a list of values for the named cli argument """
        arg = getattr(self.arguments, name, default)
        return arg if isinstance(arg, list) else [arg]

    def _process_arguments(self, args, extra_args):
        self.arguments = args
        if 'branch' not in args or not args.branch:
            try:
                self.branches = self.active_wikiversions().keys()
            except Exception:
                self.branches = []
        elif type(args.branch) is not list:
            self.branches = [args.branch]
        else:
            self.branches = args.branch

        self.changeids = [gerrit.parse_gerrit_uri(changeid)
                          for changeid in self.arg_values('changeid')]
        self.message = ' '.join(self.arg_values('message'))
        return args, extra_args

    def confirm_if_flag_missing(self, flag, prompt, default=False, bail=None):
        """
        Get confirmation from the user if a specified cli flag was omitted.
        """

        if (flag in self.arguments):
            return True
        if bail:
            bail = Exception(bail)
        utils.confirm(prompt, default, on_rejected=bail)

    def display_change(self, change):
        data = change.data
        code_review = data.labels['Code-Review']
        del data.messages
        del data.removable_reviewers
        del data.labels
        del data.permitted_labels
        mergeable = change.revision('mergeable')
        display = DataDisplay(data['owner'])
        display.show('username', 'name')
        display = DataDisplay(data)
        display.show('status', 'subject', 'topic','updated',
                     'change_id', 'project', 'branch')
        other_branches = mergeable.get(params={'other-branches':''})
        gerrit.dump_json(other_branches)

        print("Votes:")
        for vote in code_review.all:
            value = vote.value
            if value > 0:
                value = "+%s" % value
            print("  {:<22}{:<2}".format(vote.name, value))

    @cli.argument('changeid', nargs=1, default=[],
                  help='The ChangeId of a patch to merge.')
    def pick(self, command):
        '''Cherry-pick one or more changes to all active wiki branches.

        Once the changes have been cherry-picked, merge them and deploy.
        '''
        changeid = gerrit.parse_gerrit_uri(self.arg_val('changeid'))
        print('Cherry-picking %s' % changeid)
        change = gerrit.ChangeDetail(changeid)
        change.get()

        for branch in self.branches:
            branch = 'wmf/%s' % branch if '/' not in branch else branch
            print(' to %s' % branch)
            data = {
                'message': self.message,
                'destination': branch
            }
            res = change.revision('cherrypick').post(data=data)
            print(res.text)

    @cli.argument('terms', nargs='*', default=['status:open owner:self'],
                  help='Gerrit search term(s). E.g: status:open owner:self')
    @cli.argument('-n', type=int, nargs=1, default='10',
                  help='Number of results to show. Default: 10')
    def search(self, args):
        ''' execute a gerrit query '''

        query = ' '.join(self.arguments.terms)

        print ("Searching for: %s" % query)
        changes = gerrit.Changes()
        res = changes.query(query, n=self.arguments.n)
        gerrit.dump_json(res)

    @cli.argument('changeid', nargs="+",
                  help='The ChangeIds of one or more patches to display.')
    def show(self, command):
        ''' Display details about the given changeids '''
        for changeid in self.changeids:
            change = gerrit.ChangeDetail(changeid)
            data = change.get()
            self.display_change(change)

    @cli.argument('changeid', nargs=1,
                  help='The ChangeId of a patch to revert.')
    def revert(self, changeids):
        ''' revert a change in gerrit '''
        message = self.arguments.message
        changeids = self.arguments.changeid
        for changeid in changeids:
            change = gerrit.Change(changeid)
            try:
                res = change('revert').post(data={"message": message})
                gerrit.dump_json(res)
            except Exception as e:
                print(e.message)


class DataDisplay(object):
    def __init__(self, data):
        self.data = data
        self.longest_label = 0

    def show(self, *fields):
        col = []
        for field in fields:
            if (len(field) > self.longest_label):
                self.longest_label = len(field)
            row = (field, self.data.get(field, ''))
            col.append(row)

        color = [ansi.esc(ansi.FG_BLUE, ansi.BRIGHT), ansi.esc(ansi.FG_WHITE)]

        for row in col:
            pad = self.longest_label + 1
            format_str = "{}{:<" + str(pad) + "} {}{:<40}"
            print(format_str.format(color[0], row[0], color[1], row[1]))

        print(ansi.reset())
