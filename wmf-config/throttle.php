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

$wmgThrottlingExceptions[] = [ // T203909
	'from' => '2018-09-10T09:00 +2:00',
	'to' => '2018-10-10T09:00 +2:00',
	'IP' => '93.91.145.154',
	'dbname' => [ 'cswiki', 'cswikiversity', 'commonswiki', 'wikidatawiki' ],
	'value' => 120 // 100 expected
];

$wmgThrottlingExceptions[] = [ // T204829
	'from' => '2018-10-11T12:00 -5:00',
	'to' => '2018-10-11T16:00 -5:00',
	'IP' => [ '64.71.80.0/24', '64.71.95.0/24' ],
	'dbname' => [ 'enwiki', 'commonswiki', 'wikidatawiki' ],
	'value' => 60 // 40 expected
];

$wmgThrottlingExceptions[] = [ // T205529
	'from' => '2018-09-26 14:30 +2:00',
	'to' => '2018-09-26 20:00 +2:00',
	'range' => [ '195.113.180.192/26', '2001:718:9::/48' ],
	'dbname' => [ 'cswiki', 'commonswiki' ],
	'value' => 20 // 12 expected
];

$wmgThrottlingExceptions[] = [ // T206119
	'from' => '2018-10-04T16:00 +2:00',
	'to' => '2018-10-04T18:00 +2:00',
	'IP' => '195.113.145.2',
	'dbname' => [ 'cswiki', 'commonswiki', 'wikidatawiki' ],
	'value' => 15 // no more than 15 expected
];

## Add throttling definitions above.
