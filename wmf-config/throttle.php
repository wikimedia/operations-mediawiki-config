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

$wmgThrottlingExceptions[] = [ // T232596
	'from' => '2019-09-10T0:00 UTC',
	'to' => '2019-09-30T0:00 UTC',
	'range' => '194.214.218.20/30',
	'dbname' => 'frwiki',
	'value' => 200 // 178 enrolled students
];

$wmgThrottlingExceptions[] = [ // T232831
	'from' => '2019-10-02T13:00 +2:00',
	'to' => '2019-10-02T20:00 +2:00',
	'range' => [ '195.113.180.192/26', '2001:718:9::/48' ],
	'dbname' => [ 'cswiki', 'commonswiki', 'wikidatawiki' ],
	'value' => 30 // 25 expected
];

$wmgThrottlingException[] = [ // T233199
	'from' => '2019-10-01T0:00 +2:00',
	'to' => '2019-10-04T0:00 +2:00',
	'IP' => [ '88.100.221.84', '90.176.155.12', '80.188.128.54' ],
	'dbname' => [ 'cswiki', 'commonswiki', 'wikidatawiki' ],
	'value' => 70 // 50 expected
];

$wmgThrottlingExceptions[] = [ // T233378
	'from' => '2019-09-27T17:00 UTC',
	'to' => '2019-09-27T23:00 UTC',
	'range' => '200.72.159.0/26',
	'dbname' => [ 'eswiki', 'enwiki', 'commonswiki', 'wikidatawiki' ],
	'value' => 70 // 50 expected
];

$wmgThrottlingExceptions[] = [ // T234024¨
	'from' => '2019-09-30T0:00 +2:00',
	'to' => '2019-09-31T0:00 +2:00',
	'IP' => '89.177.52.35',
	'dbname' => [ 'cswiki' ],
	'value' => 10
];

## Add throttling definitions above.
