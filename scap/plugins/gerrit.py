from __future__ import division, absolute_import, print_function, unicode_literals
import six
from json import JSONEncoder
import requests
from requests import Request, Session
from requests.auth import HTTPDigestAuth
from requests.utils import get_netrc_auth
from string import Template
import json
from pygments import highlight
from pygments.lexers import JsonLexer
from pygments.formatters import TerminalFormatter
import re


gerrit_uri = 'https://gerrit.wikimedia.org'
api_uri = "%s/r/a" % gerrit_uri
(user, password) = get_netrc_auth(api_uri)
session = Session()
session.auth = HTTPDigestAuth(user, password)


def parse_gerrit_uri(text):
    pattern = "%s/.*/([0-9]+)/.*" % gerrit_uri
    parsed = re.split(pattern, text)
    if len(parsed) > 1:
        return parsed[1]
    return text


class GerritEndpoint(object):

    def _url(self, **kwargs):
        uri = Template("/".join((api_uri, self._path)))
        uri = uri.safe_substitute(self.__dict__, **kwargs)
        return uri

    def get(self, *args, **kwargs):
        uri = self._url(**kwargs)
        res = session.get(uri)
        data = res.text.splitlines()
        data = "".join(data[1:])
        return json.loads(data)

    def post(self, data={}, **kwargs):
        uri = self._url()
        print('POST %s' % uri)
        res = session.post(uri, data=data)
        return res


class GerritChange(GerritEndpoint):
    _path = "changes/${changeid}/detail"
    changeid = None
    revisionid = "current"

    def __init__(self, changeid):
        self.changeid = changeid
        self.actions = GerritActions(changeid)


class GerritActions(GerritEndpoint):
    _path = 'changes/${changeid}/revisions/${revisionid}/actions'
    revisionid  = 'current'

    def __init__(self, changeid, revisionid='current'):
        self.changeid = changeid
        self.revisionid = revisionid
        self.cherrypick = GerritCherryPick(changeid, revisionid)


class GerritCherryPick(GerritEndpoint):
    _path = 'changes/${changeid}/revisions/${revisionid}/cherrypick'
    revisionid  = 'current'

    def __init__(self, changeid, revisionid='current'):
        self.changeid = changeid
        self.revisionid = revisionid
        self._path = GerritCherryPick._path


def dump_json(data):
    ''' dump an object to the console as pretty-printed json'''
    json_str = gerrit_encoder(indent=2).encode(data)
    print(highlight(json_str, JsonLexer(), TerminalFormatter()))


class gerrit_encoder(JSONEncoder):
    def default(self, o):
        if (hasattr(o, '__dump__')):
            return o.__dump__()
        if (hasattr(o, '__dict__')):
            return o.__dict__
        return JSONEncoder.default(self, o)
