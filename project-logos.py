# -*- coding: utf-8 -*-
import os
import re
import shutil
import sys

from requests import Session


logoDir = './static/images/project-logos'
logoSession = None

logoUrls = {
	# This is *not* the same as https://upload.wikimedia.org/wikipedia/commons/thumb/b/b3/Wikipedia-logo-v2-en.svg/135px-Wikipedia-logo-v2-en.svg.png
	# This PNG was tuned (long ago) for improved legibility at 135px
	'enwiki': 'https://upload.wikimedia.org/wikipedia/commons/d/d6/Wikipedia-logo-v2-en.png',
	'abwiki': 'https://upload.wikimedia.org/wikipedia/commons/5/5a/Wikipedia-logo-v2-ab.png'
}

logo15Urls = {}

logo20Urls = {}

print '\n#### wgLogo'
logoSession = Session()
for dbname, url in logoUrls.items():
	filename = '%s.png' % dbname
	print '[%s] %s %s' % (dbname, filename, url)
	res = logoSession.get(url, timeout=3)
	if not res.ok:
		print '[%s] Error: Failed to fetch %s' % (dbname, url)
		break
	path = os.path.join(logoDir, filename)
	with open(path, 'wb') as f:
		res.raw.decode_content = True
		shutil.copyfileobj(res.raw, f)
	# TODO
	# - Optimise PNG

print '\n#### wgLogoHD["1.5x"]'
for dbname, url in logo15Urls.items():
	filename = '%s.png' % dbname
	print '[%s] %s %s' % (dbname, filename, url)
	# TODO


print '\n#### wgLogoHD["2x"]'
for dbname, url in logo20Urls.items():
	filename = '%s.png' % dbname
	print '[%s] %s %s' % (dbname, filename, url)
	# TODO

print '\n#### Clean up'
if False:
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
