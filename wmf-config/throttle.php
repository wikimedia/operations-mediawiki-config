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

$wmgThrottlingExceptions[] = [ // T188034
	'from' => '2018-03-06T0:00 UTC',
	'to' => '2018-03-11T0:00 UTC',
	'IP' => '93.242.192.25',
	'dbname' => [ 'enwiki', 'frwiki', 'eswiki', 'arwiki', 'ruwiki', 'zhwiki' ],
	'value' => 250, // 200 expected
];

$wmgThrottlingExceptions[] = [ // T188529
	'from' => '2018-03-11T0:00 -5:00',
	'to' => '2018-03-11T8:00 -5:00',
	'IP' => '72.43.130.34',
	'dbname' => [ 'enwiki' ],
	'value' => 60 // 50 expected
];

$wmgThrottlingExceptions[] = [ // T188626
	'from' => '2018-03-06T11:30 -6:00',
	'to' => '2018-03-23T13:00 -6:00',
	'range' => '139.169.111.0/24',
	'value' => 160, // 150 expected
];

$wmgThrottlingExceptions[] = [ //
	'from' => '2018-03-28T10:00 -7:00',
	'to' => '2018-03-28T14:00 -7:00',
	'IP' => '129.107.76.40',
	'value' => 70 // 60 expected
];

## Add throttling definitions above.
