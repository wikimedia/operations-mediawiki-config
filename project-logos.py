# -*- coding: utf-8 -*-
import os
import re
import sys

import requests


logoDir = './static/images/project-logos'

logoUrls = {
	# This is *not* the same as https://upload.wikimedia.org/wikipedia/commons/thumb/b/b3/Wikipedia-logo-v2-en.svg/135px-Wikipedia-logo-v2-en.svg.png
	# This PNG was tuned (long ago) for improved legibility at 135px
	'enwiki': 'https://upload.wikimedia.org/wikipedia/commons/d/d6/Wikipedia-logo-v2-en.png',
	'abwiki': 'https://upload.wikimedia.org/wikipedia/commons/5/5a/Wikipedia-logo-v2-ab.png'
}

logo15Urls = {}

logo20Urls = {}

print '\n#### wgLogo'
for key, value in logoUrls.items():
	filename = '%s.png' % key
	print '[%s] %s %s' % (key, filename, value)
	# TODO
	# - Download url, save to filename in logoDir
	# - Optimise PNG

print '\n#### wgLogoHD["1.5x"]'
for key, value in logo15Urls.items():
	filename = '%s.png' % key
	print '[%s] %s %s' % (key, filename, value)
	# TODO


print '\n#### wgLogoHD["2x"]'
for key, value in logo20Urls.items():
	filename = '%s.png' % key
	print '[%s] %s %s' % (key, filename, value)
	# TODO

print '\n#### Clean up'
for entry in os.listdir(logoDir):
	if os.path.isfile(os.path.join(logoDir, entry)):
		dbname = re.sub(r'(-(2|1.5)x)?\.png$', '', entry)
		if entry.endswith('-2x.png'):
			if dbname not in logo20Urls:
				print 'Warning: Untracked %s (logo20Urls[%s] not set)' % (entry, dbname)
		elif entry.endswith('-1.5x.png'):
			if dbname not in logo15Urls:
				print 'Warning: Untracked %s (logo15Urls[%s] not set)' % (entry, dbname)
		elif dbname not in logoUrls:
				print 'Warning: Untracked %s (logoUrls[%s] not set)' % (entry, dbname)
