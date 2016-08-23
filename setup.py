#!/usr/bin/env python2

from glob import glob
from distutils.core import setup

authors = [('Mukunda Modell', 'mmodell@wikimedia.org')]

setup(name='mediawiki-config',
      version='0.1.0',
      description='Wikimedia hosting configuration',
      author=', '.join([name for name, _ in authors]),
      author_email=', '.join([email for _, email in authors]),
      license='GNU GPLv3',
      maintainer='Wikimedia Foundation Release Engineering',
      maintainer_email='releng@wikimedia.org',
      url='https://gerrit.wikimedia.org/r/p/operations/mediawiki-config',
      packages=['scap.plugins'],
      package_dir={'scap.plugins': 'scap/plugins'},
      scripts=[s for s in glob('bin/*')],
      requires=[line.strip() for line in open('requirements.txt')],
      classifiers=['Operating System :: POSIX :: Linux',
                   'Programming Language :: Python',
                   'Programming Language :: Python :: 2',
                   'Programming Language :: Python :: 2.7'])
