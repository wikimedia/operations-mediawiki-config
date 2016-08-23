from bs4 import BeautifulSoup
from datetime import datetime
import dateutil.parser
from dateutil.tz import tzlocal
from dateutil.relativedelta import relativedelta
import re
import requests
import scap.cli as cli
import os.path


@cli.command('swat', help='Mediawiki SWAT deployment helper.')
class Swat(cli.Application):

    @cli.argument('--cherry-pick', action='store_true',
        help='Cherry-pick the patches instead of merging.')
    @cli.argument('-b', '--branch', nargs="+",
        help='One or more branches to merge into. Default: current branch.')
    @cli.argument('--base', nargs=1,
        help='Rebase the patch against this commit before merging.')
    @cli.argument('--changeid', nargs="+",
        help='The ChangeId of a patch to merge.')
    @cli.argument('--start', action='store_true',
        help='Enqueue patches from the deployment calendar and begin SWAT '
            + 'deployment.')
    @cli.argument('--list', action='store_true',
        help='List patches in the queue.')
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

        if self.arguments.list == True:
            for patch in os.listdir(queuedir):
                patchfile = os.path.join(queuedir, patch)
                with open(patchfile, mode='r') as f:
                    print(f.read())

    def scrape_deployment_calendar(self):
        gerrit_uri = 'https://gerrit.wikimedia.org'
        def match_gerrit_link(tag):
            return (
                tag.name == 'a'
                and tag.has_attr('href')
                and tag['href'].startswith(gerrit_uri))
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
