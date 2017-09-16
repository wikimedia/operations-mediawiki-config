<?php

# WARNING: This file is publically viewable on the web.
# Do not put private data here.

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
#             (default: any project)
# Example:
# $wmgThrottlingExceptions[] = [
# 'from'   => '2016-01-01T00:00 +0:00',
# 'to'     => '2016-02-01T00:00 +0:00',
# 'IP'     => '123.456.78.90',
# 'dbname' => [ 'xxwiki', etc. ],
# 'value'  => xx
# ];
## Add throttling definitions below.

$wmgThrottlingExceptions[] = [ // T175113
	'from' => '2017-09-05T0:00 +2:00',
	'to' => '2017-09-30T0:00 +2:00',
	'IP' => '93.91.145.154',
	'dbname' => [ 'cswiki', 'cswikiversity' ],
	'value' => 70, // 50 expected
];

$wmgThrottlingExceptions[] = [
	'from' => '2017-09-29T12:00 UTC',
	'to' => '2017-09-30T02:00 UTC',
	'range' => [ '186.67.125.0/24', '163.247.67.20/30', '163.247.67.24/29', '163.247.67.32/28', '163.247.67.48/29', '163.247.67.56/30', '163.247.67.60/31', '163.247.67.62/32' ],
	'dbname' => [ 'eswiki', 'enwiki', 'commonswiki', 'wikidatawiki' ],
	'value' => 120 // 100 expected
];

$wmgThrottlingExceptions[] = [ // T175534 (2)
	'from' => '2017-09-17T09:00 +8:00',
	'to' => '2017-09-17T16:00 +8:00',
	'IP' => '140.114.51.248',
	'dbname' => [ 'commonswiki', 'zhwiki' ],
	'value' => 80, // 50 expected
];

$wmgThrottlingExceptions[] = [ // T175700
	'from'   => '2017-09-15T10:00 +1:00',
	'to'     => '2017-09-15T18:00 +1:00',
	'range'  => '130.88.64.0/24',
	'dbname' => [ 'enwiki' ],
	'value'  => 30 // 20 expected
];

$wmgThrottlingExceptions[] = [ // T176037
	'from' => '2017-09-16T16:00 +1:00',
	'to' => '2018-06-16T00:00 +1:00',
	'IP' => '46.226.205.23',
	'dbname' => [ 'it.wikiversity' ],
	'value' => 200,
];

$wmgThrottlingExceptions[] = [ // T176038
	'from' => '2017-09-16T16:00 +1:00',
	'to' => '2018-06-16T00:00 +1:00',
	'IP' => '79.58.14.240',
	'dbname' => [ 'it.wikiversity' ],
	'value' => 200,
];

## Add throttling definitions above.
