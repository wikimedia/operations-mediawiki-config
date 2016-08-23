import attr
from json import JSONEncoder

def json_object_hook(obj):
    key = ",".join(obj.keys())
    if types.has_key(key):
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
    "username,name,email": "person",
    "status,by,label": "label",
    "status,label": "label",
}

class gerrit_object(object):
    def __str__(self):
        return self.__dump__()

@attr.s
class label(gerrit_object):
      by = attr.ib(default='')
      status = attr.ib(default='')
      label = attr.ib(default='')

      def __dump__(self):
          return "{}: {} ({})".format(self.label, self.status, self.by)

@attr.s
class person(gerrit_object):
    username = attr.ib(default='')
    name = attr.ib(default='')
    email = attr.ib(default='')

    def __dump__(self):
        return "{} <{}>".format(self.name, self.email)
