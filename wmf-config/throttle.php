<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

# Initialize the array. Append to that array to add a throttle
$wmgThrottlingExceptions = [];

# $wmgThrottlingExceptions is an array of arrays of parameters:
#  'from'  => date/time to start raising account creation throttle
#  'to'    => date/time to stop
#
# Optional arguments can be added to set the value or restrict by client IP
# or project dbname. Options are:
#  'value'  => new value for $wgAccountCreationThrottle (default: 50)
#  'IP'     => client IP as given by $wgRequest->getIP() or array (default: any IP)
#  'range'  => alternatively, the client IP CIDR ranges or array (default: any range)
#  'dbname' => a $wgDBname or array of dbnames to compare to
#             (eg. enwiki, metawiki, frwikibooks, eswikiversity)
#             Note that the limit is for the total number of account
#             creations on all projects. (default: any project)
# Example:
# $wmgThrottlingExceptions[] = [
# 'from'   => '2016-01-01T00:00 +0:00',
# 'to'     => '2016-02-01T00:00 +0:00',
# 'IP'     => '123.456.78.90',
# 'dbname' => [ 'xxwiki', etc. ],
# 'value'  => xx
# ];
## Add throttling definitions below.
#
## If you are adding a throttle exception with a 'from' time that is less than
## 72 hours in advance, you will also need to manually clear a cache after
## deploying your change to this file!
## https://wikitech.wikimedia.org/wiki/Increasing_account_creation_threshold

## Add throttling definitions above.

$wmgThrottlingExceptions[] = [ // T275237
	'from' => '2021-02-24T00:01 -5:00',
	'to' => '2021-05-03T23:59 -5:00',
	'range' => [
		'72.50.143.32/27',
		'157.182.72.32/28',
		'157.182.72.80/28',
		'157.182.75.128/30',
		'157.182.75.132/31',
		'157.182.79.128/26',
		'157.182.79.192/27',
		'157.182.79.224/30',
		'157.182.128.2/31',
		'157.182.128.4/30',
		'157.182.128.8/29',
		'157.182.128.16/28',
		'157.182.128.32/27',
		'157.182.128.64/26',
		'157.182.128.128/25',
		'157.182.129.2/31',
		'157.182.129.4/30',
		'157.182.129.8/29',
		'157.182.129.16/28',
		'157.182.129.32/27',
		'157.182.129.64/26',
		'157.182.129.128/25',
		'157.182.148.0/24',
		'157.182.253.0/24'
	],
	'dbname' => [ 'enwiki' ],
	'value' => 130 // 75-100 expected
];
