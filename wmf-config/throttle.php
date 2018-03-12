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

$wmgThrottlingExceptions[] = [ // T189442
	'from' => '2018-03-16T8:00 +5:30',
	'to' => '2018-03-17T19:00 +5:30',
	'IP' => [ '103.25.155.186', '103.216.147.34' ],
	'dbname' => [ 'mrwiki', 'enwiki', 'commonswiki' ],
	'value' => 50 // 35 expected
];

## Add throttling definitions above.
