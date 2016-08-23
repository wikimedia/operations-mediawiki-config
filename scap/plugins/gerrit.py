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
# get credentials from .netrc:
(user, password) = get_netrc_auth(api_uri)
# use a single global requests.Session instance:
session = Session()
# Gerrit uses HTTP Digest authentication instead of basic:
session.auth = HTTPDigestAuth(user, password)


def parse_gerrit_uri(text):
    pattern = "%s/.*/([0-9]+)/.*" % gerrit_uri
    parsed = re.split(pattern, text)
    if len(parsed) > 1:
        return parsed[1]
    return text


class GerritEndpoint(object):
    ''' base class for gerrit api endpoints '''

    # derived classes override the path section of the uri for each endpoint
    _path = "/"

    def _url(self, **kwargs):
        ''' Builds the url for http requests to this endpoint.
        This is done by combining api_uri with self._path and then replacing
        variable placeholders in the url with values from self.__dict__
        Variables can be overridden by calling this method with arbitrary
        keyword arguments which will take precedence over values from __dict__
        '''
        uri = Template("/".join((api_uri, self._path)))
        uri = uri.safe_substitute(self.__dict__, **kwargs)
        return uri

    def get(self, **kwargs):
        ''' Call the api with a http get request '''
        uri = self._url(**kwargs)
        params = kwargs.get('params', None)
        res = session.get(uri, params=params)
        # print(res.url)
        data = res.text.splitlines()
        # gerrit prepends junk to the first line of the response, strip it:
        json_str = "".join(data[1:])
        try:
            data = json.loads(json_str, object_hook=AttrDict)
            self.load(data)
            return data
        except Exception as e:
            print(e)
            print(json_str)

    def load(self, data):
        self.data = data

    def post(self, data={}, **kwargs):
        ''' make a http post request to this api endpoint '''
        uri = self._url()
        print('POST %s' % uri)
        res = session.post(uri, data=data)
        return res

    def __repr__(self):
        ''' return a string representation of this object's data '''
        return gerrit_encoder(indent=2).encode(self.data)


class GerritChanges(GerritEndpoint):
    ''' Query Gerrit changes '''

    _path = "changes/"

    def query(self, q='status:open', n=10):
        return self.get(params={'q': q, 'n': n})


class GerritChangeDetail(GerritEndpoint):
    ''' get details for a gerrit change '''
    _path = "changes/${changeid}/detail"
    changeid = None
    revisionid = "current"

    def __init__(self, changeid):
        self.changeid = changeid
        self.get()
        self.actions = GerritActions(changeid)
        self.review = GerritReviewDetails(changeid)

class GerritActions(GerritEndpoint):
    ''' get actions available (and enabled state) for a given change '''
    _path = 'changes/${changeid}/revisions/${revisionid}/actions'
    revisionid  = 'current'

    def __init__(self, changeid, revisionid='current'):
        self.changeid = changeid
        self.revisionid = revisionid
        self.cherrypick = GerritCherryPick(changeid, revisionid)


class GerritReviewDetails(GerritEndpoint):
    ''' get code review details for a given change '''
    _path = 'changes/${changeid}/revisions/${revisionid}/review'

    def __init__(self, changeid, revisionid='current'):
        self.changeid = changeid
        self.revisionid = revisionid
        self.get()

class GerritCherryPick(GerritEndpoint):
    ''' Cherry pick a revision from one branch to another '''
    _path = 'changes/${changeid}/revisions/${revisionid}/cherrypick'
    revisionid  = 'current'

    def __init__(self, changeid, revisionid='current'):
        self.changeid = changeid
        self.revisionid = revisionid
        self._path = GerritCherryPick._path


def dump_json(data):
    ''' dump an object to the console as pretty-printed json'''
    try:
        json_str = gerrit_encoder(indent=2).encode(data)
        print(highlight(json_str, JsonLexer(), TerminalFormatter()))
    except Exception as e:
        print(data)


class gerrit_encoder(JSONEncoder):
    ''' encode python objects to json '''
    def default(self, o):
        if (hasattr(o, '__dump__')):
            return o.__dump__()
        if (hasattr(o, 'data')):
            return o.data
        if (hasattr(o, '__dict__')):
            return o.__dict__
        return JSONEncoder.default(self, o)


class AttrDict(dict):
    ''' A class for accessing dict keys as attributes.
        The gerrit api returns json object trees which are decoded into python
        dictionary objects, then wrapped in AttrDict to allow easy access to
        nested attributes within the data structure.

        For example, this allows the following:
            change.data.labels.Verified
        Instead of:
            change.data['labels']['Verified']
     '''
    def __init__(self, *args, **kwargs):
        super(AttrDict, self).__init__(*args, **kwargs)
        self.__dict__ = self

    def __getattr__(self, key):
        if key in self:
            return self[key]
        # avoid key errors
        return None
