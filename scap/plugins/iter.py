# Coding: utf-8
""" Iterators / Generators """

from os import getcwd, path, chdir
import scap.log as log


class ProgressIterator(log.ProgressReporter):

    def __iter__(self):
        raise NotImplementedError('Subclass should override __iter__')


class SubdirectoryIterator(ProgressIterator):
    """
    Wraps a list of (relative) paths to filesystem directories, providing a
    generator which will visit each directory in turn when you iterate over
    the list.
    """

    def __init__(self, paths=[], on_missing=None, ignore_invalid=False):
        self.paths_completed = []
        self.paths_failed = []
        self.paths = paths
        self._on_missing = on_missing
        self.ignore_invalid = ignore_invalid
        super(SubdirectoryIterator, self).__init__(name='...',
                                                   expect=len(paths))

    def isvalid(self, dirname):
        valid = path.isdir(dirname) and not path.isabs(dirname)
        if valid:
            return True
        if self.ignore_invalid:
            return False
        else:
            msg = 'Expected a relative path to a directory.'
            raise ValueError('Invalid path: "%s" (%s)' % (dirname, msg))

    def add_success(self, item=None):
        self.paths_completed.append(item)
        super(SubdirectoryIterator, self).add_success()

    def add_failure(self, item=None):
        self.paths_failed.append(item)
        super(SubdirectoryIterator, self).add_failure()

    def __iter__(self):
        orig_cwd = getcwd()

        for dirname, data in self.paths:
            if self._on_missing and not path.isdir(dirname):
                self._on_missing(dirname, data)

            if not self.isvalid(dirname):
                continue

            item = (dirname, data)

            try:
                chdir(dirname)
                yield item
            except Exception as e:
                self.add_failure(item)
                raise e
            else:
                self.add_success(item)
            finally:
                chdir(orig_cwd)
