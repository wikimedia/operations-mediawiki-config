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

$wmgThrottlingExceptions[] = [ // T185930
	'from' => '2018-03-10T11:00:00 -5:00',
	'to' => '2018-03-10T16:00:00 -5:00',
	'range' => '38.125.10.42',
	'dbname' => [ 'enwiki' ],
	'value' => 30 // 20 expected
];

$wmgThrottlingExceptions[] = [ // T185794
	'from' => '2017-03-04T11:00 -5:00',
	'to' => '2017-03-04T18:00 -5:00',
	'IP' => '198.179.69.250',
	'dbname' => [ 'enwiki', 'eswiki' ],
	'value' => 70 // 50 expected
];

$wmgThrottlingExceptions[] = [ // T185794
	'from' => '2017-02-08T16:00 -7:00',
	'to' => '2017-03-04T20:00 -7:00',
	'IP' => '66.99.86.39',
	'dbname' => [ 'enwiki' ],
	'value' => 70 // 50 expected
];

## Add throttling definitions above.
