from bs4 import BeautifulSoup
from datetime import datetime
import dateutil.parser
from dateutil.tz import tzlocal
from dateutil.relativedelta import relativedelta
import json
import os.path

import re
import requests
import subprocess
import sys

import scap.cli as cli
import scap.cmd as cmd
import scap.utils as utils
from scap.plugins.gerrit import GerritChange, GerritActions, GerritCherryPick, dump_json


@cli.command('swat', help='Mediawiki SWAT deployment helper.')
class Swat(cli.Application):
    ''' scap swat: cherry pick and deploy patches for mediawiki swat. '''
    gerrit_uri = 'https://gerrit.wikimedia.org'
    @cli.argument('--start', action='store_true',
        help='Enqueue patches from the deployment calendar and begin SWAT '
            + 'deployment.')
    @cli.argument('-b', '--branch', nargs="?",
        help='One or more branches to merge into. Default: active wmf/branches.')
    @cli.argument('--changeid', nargs="+",
        help='The ChangeId of a patch to merge.')
    @cli.argument('--list', action='store_true',
        help='List patches in the queue.')
    @cli.argument('--search', nargs=1,
        help='Search for patches using Gerrit query terms.')
    @cli.argument('pick', nargs='+',
        help='cherry-pick patches to release branches')
    def main(self, *extra_args):
        queuedir = os.path.expanduser('~/.swat')
        if not os.path.isdir(queuedir):
            os.mkdir(queuedir)

        if self.arguments.start == True:
            swat_patches = self.scrape_deployment_calendar()
            for patch in swat_patches:
                patchfile = os.path.join(queuedir, patch[0])
                with open(patchfile, mode='w') as f:
                    for line in patch:
                        f.write(line.encode('UTF8'))
                        f.write("\n")
            return

        if self.arguments.list == True:
            for patch in os.listdir(queuedir):
                patchfile = os.path.join(queuedir, patch)
                with open(patchfile, mode='r') as f:
                    print(f.read())
            return

        if not self.arguments.branch:
            self.arguments.branch = self.active_wikiversions().keys()
        elif type(self.arguments.branch) is not list:
            self.arguments.branch = [self.arguments.branch]

        if self.arguments.pick:
            return self.cherrypick(self.arguments.pick[1:])

        if self.arguments.search:
            return self.query(self.arguments.search)

        if self.arguments.changeid:
            return self.query(self.arguments.changeid)

    def cherrypick(self, changeids):
        ''' cherry pick one or more changes to the active wiki branches '''
        branches = self.arguments.branch
        for changeid in changeids:
            print('Cherry-picking %s' % changeid)
            changeid = self.parse_gerrit_uri(changeid)
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

    def query(self, changeids):
        ''' execute a gerrit query '''
        for changeid in changeids:
            changeid = self.parse_gerrit_uri(changeid)
            change = GerritChange(changeid)
            res = change.get()
            dump_json(res)
            res = change.actions.get()
            dump_json(res)
            print(res.text)



    def _process_arguments(self, args, extra_args):
        ''' extra command line arguments get treated as change-ids'''
        if len(extra_args):
            args.changeid=[]
            for arg in extra_args:
                args.changeid.append(self.parse_gerrit_uri(arg))
            extra_args = []

        return args, extra_args

    def parse_gerrit_uri(self, text):
        pattern = "%s/.*/([0-9]+)/.*" % self.gerrit_uri
        parsed = re.split(pattern, text)
        if len(parsed) > 1:
            return parsed[1]
        return text

    def scrape_deployment_calendar(self):
        ''' scrape the deployment calendar (h.wikimedia.org/wiki/Deployments)
        and return the list of patches for the current swat window.
        '''
        def match_gerrit_link(tag):
            return (
                tag.name == 'a'
                and tag.has_attr('href')
                and tag['href'].startswith(self.gerrit_uri))
        NOW = datetime.now(tzlocal())

        patchlist = []

        r = requests.get('https://wikitech.wikimedia.org/wiki/Deployments')
        soup = BeautifulSoup( r.text, 'lxml' )
        # find all swat deploy windows on the deployment calendar
        for tag in soup.find_all(title="SWAT deploys"):
            # look up 3 levels to find the TR tag
            row = tag.parent.parent.parent
            if row.name != 'tr':
                continue
            # get the timestamp from the TR tag's id attribute
            datestring = row['id'].rsplit('-', 1)[1] + "Z"
            # parse the date string
            window_start = dateutil.parser.parse(datestring)
            window_end = window_start + relativedelta(hours=+1)
            # if the current time is not within this window, continue to next
            if NOW <= window_start or NOW > window_end:
                continue
            # get all the names for developers with patches to deploy
            people = row.find_all(class_='ircnick-container')

            for person in people:
                name = person.text
                cell = person.find_parent('td')

                if person.parent.name == 'p':
                    # All but the first developers' names are nested inside a <p>
                    r = person.parent
                else:
                    r = person
                # get the list immediately following the developer name
                nextTag = r.find_next_sibling('ul')
                if not nextTag:
                    continue
                # find all gerrit links in this list
                links = nextTag.find_all(match_gerrit_link)
                if not links:
                    continue

                for link in links:
                    patchlist.append((link.text, name, link['href']))

        return patchlist
