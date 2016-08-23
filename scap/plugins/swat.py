from __future__ import division, absolute_import
from __future__ import print_function, unicode_literals

from inspect import cleandoc
import inspect
import sys
import scap.arg
import scap.cli as cli
import scap.utils as utils
from scap.plugins.gerrit import Changes, Change, ChangeDetail
from scap.plugins.gerrit import dump_json, parse_gerrit_uri
import scap.plugins.dep as dep
from scap.utils import confirm

@cli.command('swat', help='Mediawiki SWAT deployment helper.', subcommands=True)
@cli.argument('-b', '--branch', nargs=1,
              help='One or more branches to merge into.'
              + ' Default: active wmf/branches.')
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
        scap swat --changeid 123456

    More info: https://wikitech.wikimedia.org/wiki/SWAT_deploys
    '''
    @cli.argument('changeid', nargs='+', default=[],
                  help='Change-id or gerrit patch url')
    def merge(self, *extra_args):
        """ scap swat """
        changeids = self.arguments.changeid if 'changeid' in self.arguments else []
        message = " ".join(self.arguments.message)
        logger = self.get_logger()
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
                if confirm('Submit the change?', True):
                    change.revision('submit').post()

            def approve(message):
                logger.debug("Approval Message: %s", message)
                if confirm('Vote +2 and submit?', True):
                    reviewinput={
                        "message": message,
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
                approve(message)


    def _process_arguments(self, args, extra_args):
        if 'branch' not in args or not args.branch:
            try:
                self.branches = self.active_wikiversions().keys()
            except Exception:
                self.branches = []
        elif type(args.branch) is not list:
            self.branches = [args.branch]
        else:
            self.branches = args.branch

        self.message = args.get('message','')
        ''' extra command line arguments get passed to workflows'''
        return args, extra_args


    def confirm_if_flag_missing(self, flag, prompt, default=False, bail=None):
        """Get confirmation from the user if a specified cli flag was omitted."""
        if (flag in self.arguments):
            return True
        if bail:
            bail = Exception(bail)
        confirm(prompt, default, on_rejected=bail)


    @cli.argument('changeid', nargs=1, default=[],
                  help='The ChangeId of a patch to merge.')
    def pick(self, command):
        '''Cherry-pick one or more changes to all active wiki branches.

        Once the changes have been cherry-picked, merge them and deploy.
        '''
        changeid = self.arguments.changeid
        print('Cherry-picking %s' % changeid)
        changeid = parse_gerrit_uri(changeid)
        change = ChangeDetail(changeid)
        change.get()

        for branch in self.branches:
            branch = 'wmf/%s' % branch if '/' not in branch
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
        changes = Changes()
        res = changes.query(query, n=self.arguments.n)
        dump_json(res)

    @cli.argument('changeid', nargs=1,
                  help='The ChangeId of a patch to revert.')
    def revert(self, changeids):
        ''' revert a change in gerrit '''
        message = self.arguments.message
        changeids = self.arguments.changeid
        for changeid in changeids:
            change = Change(changeid)
            try:
                res = change('revert').post(data={"message": message})
                dump_json(res)
            except Exception as e:
                print(e.message)
