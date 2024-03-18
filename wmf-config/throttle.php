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

// T356654
$wmgThrottlingExceptions[] = [
	'from' => '2024-03-20T10:00 -5:00',
	'to' => '2024-03-20T17:00 -5:00',
	'IP' => [ '69.43.66.37', '69.43.65.27' ],
	'dbname' => [ 'commonswiki', 'enwiki' ],
	'value' => 200,
];

// T357978
$wmgThrottlingExceptions[] = [
	'from' => '2024-03-21T12:00 +1:00',
	'to' => '2024-03-21T20:00 +1:00',
	'IP' => [ '194.228.196.245' ],
	'dbname' => [ 'cswiki', 'commonswiki', 'enwiki' ],
	'value' => 65,
];

// T360103
$wmgThrottlingExceptions[] = [
	'from' => '2024-03-25T15:00 +1:00',
	'to' => '2024-03-25T20:00 +1:00',
	'IP' => [ '78.128.203.244', '78.128.203.245' ],
	'dbname' => [ 'cswiki', 'commonswiki' ],
	'value' => 40,
];

// T360145
$wmgThrottlingExceptions[] = [
	'from' => '2024-03-21T12:00 -4:00',
	'to' => '2024-03-21T17:00 -4:00',
	'range' => '206.167.136.0/24',
	'dbname' => 'frwiki',
	'value' => 100,
];

// T360357
$wmgThrottlingExceptions[] = [
	'from' => '2024-04-05T00:00 -3:00',
	'to' => '2024-04-05T23:00 -3:00',
	'IP' => [ '163.10.36.3', '163.10.36.4' ],
	'dbname' => [ 'commonswiki', 'eswiki', 'wikidatawiki' ],
	'value' => 40,
];

## Add throttling definitions above.
