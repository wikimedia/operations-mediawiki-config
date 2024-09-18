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
// T373468
$wmgThrottlingExceptions[] = [
	'from' => '2024-09-17T13:00 -3:00',
	'to' => '2024-09-17T20:00 -3:00',
	'IP' => '163.10.39.132',
	'dbname' => [ 'eswiki', 'commonswiki', 'wikidatawiki' ],
	'value' => 100,
];
// T373468
$wmgThrottlingExceptions[] = [
	'from' => '2024-09-24T13:00 -3:00',
	'to' => '2024-09-24T20:00 -3:00',
	'IP' => '163.10.39.132',
	'dbname' => [ 'eswiki', 'commonswiki', 'wikidatawiki' ],
	'value' => 100,
];
// T374621
$wmgThrottlingExceptions[] = [
	'from' => '2024-09-27T09:00 -3:00',
	'to' => '2024-09-27T20:00 -3:00',
	'IP' => '186.0.208.20',
	'dbname' => [ 'eswiki', 'commonswiki', 'wikidatawiki' ],
	'value' => 80,
];
$wmgThrottlingExceptions[] = [
	'from' => '2024-09-27T09:00 -3:00',
	'to' => '2024-09-27T20:00 -3:00',
	'IP' => '200.49.237.208',
	'dbname' => [ 'eswiki', 'commonswiki', 'wikidatawiki' ],
	'value' => 80,
];
$wmgThrottlingExceptions[] = [
	'from' => '2024-09-28T09:00 -3:00',
	'to' => '2024-09-28T20:00 -3:00',
	'IP' => '186.0.208.20',
	'dbname' => [ 'eswiki', 'commonswiki', 'wikidatawiki' ],
	'value' => 80,
];
$wmgThrottlingExceptions[] = [
	'from' => '2024-09-28T09:00 -3:00',
	'to' => '2024-09-28T20:00 -3:00',
	'IP' => '200.49.237.208',
	'dbname' => [ 'eswiki', 'commonswiki', 'wikidatawiki' ],
	'value' => 80,
];

// T374964
$wmgThrottlingExceptions[] = [
	'from' => '2024-10-07T14:00 -3:00',
	'to' => '2024-10-08T23:00 -3:00',
	'IP' => '190.244.121.251',
	'dbname' => [ 'eswiki', 'commonswiki', 'wikidatawiki' ],
	'value' => 80,
];

## Add throttling definitions above.
