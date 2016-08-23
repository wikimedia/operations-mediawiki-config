"""
    scap.plugins.gerrit
    ~~~~~~~~~~~~~~~~~~~
    Provides Gerrit REST api support for the `scap swat` plugin
"""

from __future__ import print_function, unicode_literals

from json import JSONEncoder
from requests import Session
from requests.auth import HTTPDigestAuth
from requests.utils import get_netrc_auth
from string import Template
from pygments import highlight
from pygments.lexers import JsonLexer
from pygments.formatters import TerminalFormatter

import json
import logging
import re

gerrit_session = None
gerrit_uri = 'https://gerrit.wikimedia.org'
api_uri = '%s/r/a' % gerrit_uri


def session(requests_session=None, uri=None):
    global gerrit_session
    global api_uri

    if requests_session:
        gerrit_session = requests_session

    if uri:
        api_uri = uri

    if gerrit_session is None:
        gerrit_session = Session()
        # get credentials from .netrc:
        (user, password) = get_netrc_auth(api_uri)
        # Gerrit uses HTTP Digest authentication instead of basic:
        gerrit_session.auth = HTTPDigestAuth(user, password)

    return gerrit_session


def parse_gerrit_uri(text):
    pattern = "%s/.*/([0-9]+)/.*" % gerrit_uri
    parsed = re.split(pattern, text)
    if len(parsed) > 1:
        return parsed[1]
    return text


def debug_log(msg, *args):
    logger = logging.getLogger()
    logger.debug(msg, *args)


def error_log(msg, *args):
    logger = logging.getLogger()
    logger.error(msg, *args)


class GerritEndpoint(object):
    ''' base class for gerrit api endpoints '''

    # derived classes override the path section of the uri for each endpoint
    _path = "/"

    def __init__(self, path='/'):
        '''
        Create a generic endpoint instance with the specified path.
        The path template should be a string with placeholder ${variables}
        for the dynamic parts of the path component of the api url.
        '''
        self._path = path

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

    def get(self,  **kwargs):
        ''' Call the api with a http get request '''
        params = kwargs.pop('params', None)
        uri = self._url(**kwargs)

        debug_log("uri: %s", uri)
        debug_log("_path: %s", self._path)
        res = session().get(uri, params=params, timeout=30)
        return self.load(res)

    def load(self, res):
        if res.status_code == 200:
            try:
                # gerrit prepends junk to the response, strip it:
                data = res.text.lstrip(")]}'")
                json_str = "".join(data[1:])
                self.data = json.loads(json_str, object_hook=AttrDict)
                return self.data
            except Exception as e:
                error_log('Could not decode response: %s',  res.text)
                print(json_str)
                raise e
        else:
            error_log("Status: %s" % res.status_code)
            error_log(res.text)
            raise Exception('Request Failed: %s %s %s' % (res.url,
                            res.status_code, res.text))

    def post(self, data={}, **kwargs):
        ''' make a http post request to this api endpoint '''
        uri = self._url()
        debug_log('POST to: %s', uri)
        debug_log('POST Data: %s', data)
        dump_json(data)
        res = session().post(uri, json=data, timeout=30)
        debug_log('Response: %s', res.text)
        return self.load(res)

    def __call__(self, path, **kwargs):
        '''
        Clone this endpoint instance, append the path and return the modified
        instance.
        This allows us to chain method calls instead of creating a subclass
        for every gerrit api method.

        Example:
        change = ChangeDetail(changeid)
        actions = change('actions').get()
        '''
        _path = self._path
        _path = "/".join((_path, path))
        new_instance = GerritEndpoint(_path)
        for k in self.__dict__.keys():
            if k[0] == '_':
                continue
            new_instance.__dict__[k] = self.__dict__[k]

        new_instance._parent = self
        return new_instance

    def __repr__(self):
        ''' return a string representation of this object's data '''
        return gerrit_encoder(indent=2).encode(self.data)


class Changes(GerritEndpoint):
    ''' Query Gerrit changes '''

    def __init__(self):
        self._path = "changes/"

    def query(self, q='status:open', n=10):
        return self.get(params={'q': q, 'n': n})


class Change(GerritEndpoint):
    ''' get details for a gerrit change '''
    _path = "changes/${changeid}"
    changeid = None
    revisionid = "current"

    def __init__(self, changeid, revisionid='current'):
        self.changeid = changeid
        self.revision = ChangeRevisions(changeid, revisionid)


class ChangeDetail(GerritEndpoint):
    ''' get details for a gerrit change '''
    _path = "changes/${changeid}/detail"
    changeid = None
    revisionid = "current"

    def __init__(self, changeid, revisionid='current'):
        self.changeid = changeid
        self.revision = ChangeRevisions(changeid, revisionid)


class ChangeRevisions(GerritEndpoint):
    _path = 'changes/${changeid}/revisions/${revisionid}'
    revisionid = 'current'

    def __init__(self, changeid, revisionid='current'):
        self.changeid = changeid
        self.revisionid = revisionid


def dump_json(data):
    ''' dump an object to the console as pretty-printed json'''
    try:
        json_str = gerrit_encoder(indent=2).encode(data)
        output = highlight(json_str, JsonLexer(), TerminalFormatter())
        print(output)
    except Exception as e:
        print(e)
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
