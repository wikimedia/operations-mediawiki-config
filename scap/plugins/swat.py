
import json
import os.path

import re
import requests
import subprocess
import sys

import scap.cli as cli
import scap.utils as utils
from scap.plugins.gerrit import GerritChanges, GerritChangeDetail, dump_json, parse_gerrit_uri


@cli.command('swat', help='Mediawiki SWAT deployment helper.')
class Swat(cli.Application):
    ''' scap swat: cherry pick and deploy patches for mediawiki swat.
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
    '''
    @cli.argument('-p', '--pick', nargs='+',
        help='cherry-pick patches to release branches')
    @cli.argument('-b', '--branch', nargs="?",
        help='One or more branches to merge into. Default: active wmf/branches.')
    @cli.argument('--changeid', nargs="+",
        help='The ChangeId of a patch to merge.')
    @cli.argument('--search', nargs=1,
        help='Search for patches using Gerrit query terms.')
    def main(self, *extra_args):
        if not self.arguments.branch:
            self.arguments.branch = self.active_wikiversions().keys()
        elif type(self.arguments.branch) is not list:
            self.arguments.branch = [self.arguments.branch]

        if self.arguments.pick:
            return self.cherrypick(self.arguments.pick)

        if self.arguments.search:
            return self.query(self.arguments.search)

        if self.arguments.changeid:
            return self.changes(self.arguments.changeid)


    def cherrypick(self, changeids):
        ''' cherry pick one or more changes to the active wiki branches '''
        branches = self.arguments.branch
        for changeid in changeids:
            print('Cherry-picking %s' % changeid)
            changeid = parse_gerrit_uri(changeid)
            change = GerritChange(changeid)
            for branch in branches:
                branch = 'wmf/%s' % branch
                print(' to %s' % branch)
                data = {
                    'message': 'Cherry-pick for SWAT deployment.',
                    'destination': branch
                }
                res = change.actions.cherrypick.post(data=data)
                print(res.text)

    def query(self, query):\
        ''' execute a gerrit query '''
        changes = GerritChanges()
        res = changes.query(query)
        dump_json(res)

    def changes(self, changeids):
        ''' get change details '''
        for changeid in changeids:
            changeid = parse_gerrit_uri(changeid)
            change = GerritChangeDetail(changeid)
            res = change.get()
            dump_json(res)
            res = change.actions.get()
            dump_json(res)


    def _process_arguments(self, args, extra_args):
        ''' extra command line arguments get treated as change-ids'''
        if len(extra_args):
            args.changeid=[]
            for arg in extra_args:
                args.changeid.append(parse_gerrit_uri(arg))
            extra_args = []

        return args, extra_args
