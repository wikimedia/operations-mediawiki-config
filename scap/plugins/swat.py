from __future__ import division, absolute_import
from __future__ import print_function, unicode_literals

import sys
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
            if actions.rebase.enabled:
                # if the change is not submittable, attempt to rebase
                prompt = 'This change must be rebased on top of {}, continue?'
                prompt = prompt.format(change.data.branch)
                bail = 'Cannot merge without rebase.'
                self.confirm_if_flag_missing('rebase', prompt, False, bail)
                # Rebase on top of the target branch
                res = change.revision('rebase').post(data={"base": ''})
                dump_json(res)
            # refresh change details
            change.get()

            def submit():
                change.revision('submit').post()

            def approve():
                change.revision('review').post(data={
                    "tag": "swat",
                    "message": "Approved for SWAT.",
                    "labels": {
                        "Code-Review": 2
                    }
                })

            if change.data.submittable:
                if self.confirm('Submit the change?', True):
                    submit()
            else:
                if self.confirm('Vote +2 and submit?', True):
                    approve()

    def confirm_if_flag_missing(self, flag, prompt, default=False, bail=None):
        """Get confirmation from the user if a specified cli flag was omitted."""
        if (flag in self.arguments):
            return True
        if bail:
            bail = Exception(bail)
        self.confirm(prompt, default, on_rejected=bail)

    @staticmethod
    def confirm(question='Continue?', default=False, on_fulfilled=None,
                on_rejected=None):
        """
        Ask for confirmation from the user if possible, otherwise return default
        when stdin is not attached to a terminal.

        The confirmation is fullfilled when the user types an affirming response
        which can be either 'y' or 'yes', otherwise the default choice is assumed

        The confirmation is rejected when default=False and the user types anything
        other than affirmative.

        :param question: prompt text to show to the user
        :param default: boolean default choice, True [Y/n] or False [y/N]. This is
                        the value that is returned when a tty is not attached to
                        stdin or the user presses enter without typing a response.
        :param on_fullfilled: optional callback function which is called before
                              returning True
        :param on_rejected: optional, either a callback function or an exception
                            to be raised when we fail to get confirmation. This
                            can be used to let the user bail out of a workflow or
                            to bail when execution is not attached to a terminal.

        """
        yes = ['y', 'yes']
        no = ['n', 'no']

        if default:
            choices = '[Y/n]'
        else:
            choices = '[y/N]'

        # in case stdin is not a tty or the user accepts the default answer, then
        # the result will be default.
        result = default

        if sys.stdout.isatty():
            ans = raw_input('{} {}: '.format(question, choices)).strip().lower()
            if ans in yes:
                result = True
            elif ans in no:
                result = False

        if result:
            # yes
            if callable(on_fulfilled):
                on_fulfilled()
        else:
            # no
            if isinstance(on_rejected, Exception):
                raise on_rejected
            elif callable(on_rejected):
                on_rejected()

        return result

    def _process_arguments(self, args, extra_args):
        ''' extra command line arguments get treated as change-ids'''
        if len(extra_args):
            args.changeid = []
            for arg in extra_args:
                args.changeid.append(parse_gerrit_uri(arg))
            extra_args = []

        return args, extra_args
