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

// T391999 - 7 Edit-a-thons at Universidad Nacional de La Plata
$wmgThrottlingExceptions[] = [
	'from' => '2025-06-17T12:45 -3:00',
	'to' => '2025-06-17T19:15 -3:00',
	'IP' => '163.10.23.201',
	'dbname' => [ 'commonswiki', 'eswiki', 'wikidatawiki' ],
	'value' => 60,
];

// T391764 - 4 Edit-a-thons at University in Japan
$wmgThrottlingExceptions[] = [
	'from' => '2025-06-16T14:30 +9:00',
	'to' => '2025-06-16T17:30 +9:00',
	'IP' => '202.25.155.253',
	'dbname' => [ 'jawiki' ],
	'value' => 30,
];

$wmgThrottlingExceptions[] = [
	'from' => '2025-06-23T14:30 +9:00',
	'to' => '2025-06-23T17:30 +9:00',
	'IP' => '202.25.155.253',
	'dbname' => [ 'jawiki' ],
	'value' => 30,
];

$wmgThrottlingExceptions[] = [
	'from' => '2025-07-10T10:00 +9:00',
	'to' => '2025-07-10T13:00 +9:00',
	'IP' => '202.25.155.253',
	'dbname' => [ 'jawiki' ],
	'value' => 30,
];

$wmgThrottlingExceptions[] = [
	'from' => '2025-07-17T10:00 +9:00',
	'to' => '2025-07-17T13:00 +9:00',
	'IP' => '202.25.155.253',
	'dbname' => [ 'jawiki' ],
	'value' => 30,
];

// T396128
$wmgThrottlingExceptions[] = [
	'from' => '2025-06-16T08:00 +0:00',
	'to' => '2025-06-16T18:15 +0:00',
	'IP' => '82.219.32.157',
	'dbname' => [ 'enwiki' ],
	'value' => 40,
];

// T396980 - wikipedia workshop - cs.wikipedia on 19June2025
$wmgThrottlingExceptions[] = [
	'from' => '2025-06-19T13:00 +2:00',
	'to' => '2025-06-19T20:30 +2:00',
	'IP' => '78.128.191.240',
	'dbname' => [ 'commonswiki', 'cswiki', 'enwiki' ],
	'value' => 60,
];
## Add throttling definitions above.
