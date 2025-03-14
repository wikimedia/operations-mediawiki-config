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
#  'value'  => new value for $wgAccountCreationThrottle (default: 50 per day)
#  'tempaccountvalue' => new value for $wgTempAccountCreationThrottle
#    (default: 6 per day)
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
# 'value'  => 100,
# 'tempaccountvalue' => 50,
# ];
## Add throttling definitions below.
#
## If you are adding a throttle exception with a 'from' time that is less than
## 72 hours in advance, you will also need to manually clear a cache after
## deploying your change to this file!
## https://wikitech.wikimedia.org/wiki/Increasing_account_creation_threshold

// T386126
$wmgThrottlingExceptions[] = [
	'from' => '2025-03-10T08:00 -3:00',
	'to' => '2025-03-10T13:30 -3:00',
	'IP' => '186.190.157.228',
	'dbname' => [ 'eswiki', 'commonswiki', 'wikidatawiki' ],
	'value' => 80,
];

// T387181
$wmgThrottlingExceptions[] = [
	'from' => '2025-03-05T16:00 -8:00',
	'to' => '2025-03-05T20:00 -8:00',
	'range' => [
		'128.97.0.0/16',
		'131.179.0.0/16',
		'149.142.0.0/16',
		'164.67.0.0/16',
		'169.232.0.0/16',
		'192.35.210.0/24',
		'192.35.221.0/24',
		'192.35.225.0/24',
		'192.35.226.0/24',
		'192.154.2.0/24',
		'216.41.228.0/24',
		'2607:f010::/32'
	],
	'dbname' => [ 'enwiki' ],
	'value' => 100,
];

// T387568
$wmgThrottlingExceptions[] = [
	'from' => '2024-03-12T11:00 -5:00',
	'to' => '2024-03-12T18:00 -5:00',
	'range' => [
		'64.131.96.0 - 64.131.127.255',
		'104.194.96.0 - 104.194.127.255',
		'216.47.128.0 - 216.47.159.255',
		'198.37.16.0 - 198.37.23.255',
		'198.37.24.0 - 198.37.27.255',
		'192.42.83.144 - 192.42.83.159'
	],
	'dbname' => 'enwiki',
	'value' => 50,
];

// T388637
$wmgThrottlingExceptions[] = [
	'from' => '2025-03-19T12:00 +0:00',
	'to' => '2025-03-19T17:00 +0:00',
	'IP' => '194.80.232.21',
	'dbname' => [ 'enwiki' ],
	'value' => 20,
];

## Add throttling definitions above.
