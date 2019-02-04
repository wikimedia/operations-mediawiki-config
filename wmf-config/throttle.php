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

$wmgThrottlingExceptions[] = [ // T213311
	'from' => '2019-01-15T13:00 -5:00',
	'to' => '2019-01-15T15:00 -5:00',
	'IP' => '192.136.22.4',
	'dbname' => 'enwiki',
	'value' => 30 // 20 expected
];

$wmgThrottlingExceptions[] = [ // T212917
	'from' => '2019-01-15T11:00 -8:00',
	'to' => '2019-01-15T14:00 -8:00',
	'range' => [ '128.125.146.0/24', '128.125.148.0/24', '207.151.52.0/24', '207.151.53.0/26' ],
	'dbname' => 'enwiki',
	'value' => 70 // 60 expected
];

$wmgThrottlingExceptions[] = [ // T213848
	'from' => '2019-01-17T08:00 +0:00',
	'to' => '2019-01-17T10:00 +0:00',
	'IP' => [ '80.82.239.53', '185.86.90.141' ],
	'dbname' => [ 'frwiki' ],
	'value' => 50 // ? expected
];

$wmgThrottlingExceptions[] = [ // T213848
	'from' => '2019-01-23T08:00 +0:00',
	'to' => '2019-01-23T10:00 +0:00',
	'IP' => [ '80.82.239.53', '185.86.90.141' ],
	'dbname' => [ 'frwiki' ],
	'value' => 50 // ? expected
];

$wmgThrottlingExceptions[] = [ // T212226
	'from' => '2019-01-29T08:00 UTC',
	'to' => '2019-01-29T14:00 UTC',
	'IP' => '195.194.178.1',
	'dbname' => 'enwiki',
	'value' => 15 // 12 expected
];
$wmgThrottlingExceptions[] = [ // T215069
	'from' => '2019-02-14T09:00 -5:00',
	'to' => '2019-02-14T16:00 -5:00',
	'IP' => ['134.140.225.0/24, 134.140.164.0/22,134.140.116.0/22,134.140.26.0/23'],
	'dbname' => 'enwiki',
	'value' => 80 // 70 expected
];

## Add throttling definitions above.
