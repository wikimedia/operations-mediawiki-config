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

$wmgThrottlingExceptions[] = [ // T235493
	'from' => '2019-10-15T0:00 +2:00',
	'to' => '2019-10-18T0:00 +2:00',
	'IP' => '195.113.145.2',
	'dbname' => [ 'cswiki' ],
	'value' => 30
];
$wmgThrottlingExceptions[] = [ // T235693
	'from' => '2019-10-25T16:00 UTC',
	'to' => '2019-10-25T22:00 UTC',
	'range' => '200.14.67.0/25',
	'dbname' => [ 'eswiki' ],
	'value' => 40, // 30 expected
];
$wmgThrottlingExceptions[] = [ // T236955
	'from' => '2019-11-04T0:00 UTC',
	'to' => '2019-11-16T0:00 UTC',
	'range' => [ '192.83.253.0/24', '192.76.239.0/24', '192.246.235.0/24', '192.246.234.0/24', '192.246.233.0/24', '192.246.224.0/21', '173.46.96.0/19' ],
	'value' => 100
];

## Add throttling definitions above.
