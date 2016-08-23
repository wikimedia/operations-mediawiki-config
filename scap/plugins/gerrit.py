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

api_uri = 'https://gerrit.wikimedia.org/r/a'
(user, password) = get_netrc_auth(api_uri)
session = Session()
session.auth = HTTPDigestAuth(user, password)

class GerritEndpoint(object):
    path = ""

    def __init__(self, *args, **kwargs):
        for key, val in six.iteritems(kwargs):
            setattr(self, key, val)
        self._path = "/".join(args).replace('//','/')

    def __call__(self, *args, **kwargs):
        uri = Template("/".join((api_uri, self._path)))
        uri = uri.safe_substitute(self.__dict__, **kwargs)
        res = session.get(uri)
        data = res.text.splitlines()
        data = "".join(data[1:])
        return json.loads(data, object_hook=json_object_hook)

GerritChange = GerritEndpoint('changes/${changeid}/detail')
GerritActions = GerritEndpoint('changes/${changeid}/revisions/${revisionid}/actions')

def dump_json(data):
    ''' dump an object to the console as pretty-printed json'''
    json_str = gerrit_encoder(indent=2).encode(data)
    print(highlight(json_str, JsonLexer(), TerminalFormatter()))

def json_object_hook(obj):
    keys = [k for k in obj if k[0] != '_' ]
    key = ",".join(keys)
    if key in types:
        cls = types[key]
        constructor = globals()[cls]
        return constructor(**obj)
    else:
        return obj

class gerrit_encoder(JSONEncoder):
    def default(self, o):
        if (hasattr(o, '__dump__')):
            return o.__dump__()
        if (hasattr(o, '__dict__')):
            return o.__dict__
        return JSONEncoder.default(self, o)

types = {
    "username,email,name": "person",
    "status,by,label": "label",
    "status,label": "label",
    "username,name,value,date,email": "vote",
    "username,email,name,value": "vote"
}


class atr(object):
    def __init__(self, *args, **kwargs):
        for k, v in six.iteritems(kwargs):
            setattr(self, k, v)


class gerrit_object(object):
    def __init__(self, *args, **kwargs):
        for k, v in six.iteritems(kwargs):
            setattr(self, k, v)

    def __str__(self):
        return self.__dump__()


class label(gerrit_object):
      by = atr(default='')
      status = atr(default='')
      label = atr(default='')

      def __dump__(self):
          return "{}: {} ({})".format(self.label, self.status, self.by)


class vote(gerrit_object):
    username = atr(default='')
    name = atr(default='')
    value = atr(default='')
    date = atr(default='')
    email = atr(default='')

    def __dump__(self):
        return {self.name: self.value}


class person(gerrit_object):
    username = atr(default='')
    name = atr(default='')
    email = atr(default='')
    _account_id = atr(default='')

    def __dump__(self):
        return "{} <{}>".format(self.name, self.email)
