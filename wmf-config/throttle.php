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

$wmgThrottlingExceptions[] = [ // T188626
	'from' => '2018-03-06T11:30 -6:00',
	'to' => '2018-03-23T13:00 -6:00',
	'range' => '139.169.111.0/24',
	'value' => 160, // 150 expected
];

$wmgThrottlingExceptions[] = [ // T189241
	'from' => '2018-03-28T10:00 -7:00',
	'to' => '2018-03-28T14:00 -7:00',
	'IP' => '129.107.76.40',
	'value' => 70 // 60 expected
];

$wmgThrottlingExceptions[] = [ // T190206
	'from' => '2018-03-24T11:00 -5:00',
	'to' => '2018-03-24T16:00 -5:00',
	'IP' => [ '129.21.255.128/26', '129.21.176.0/24', '129.21.179.0/24', '129.21.180.0/24' ],
	'value' => 100 // This should be enough
];

$wmgThrottlingExceptions[] = [
	'from' => '2018-04-04T10:00 -5:00',
	'to' => '2018-04-04T11:00 -5:00',
	'range' => [ '24.75.223.0/24' ],
	'dbname' => [ 'enwiki' ],
	'value' => 40 // 30 excepted
];

$wmgThrottlingExceptions[] = [ // T189796
	'from' => '2018-03-22T11:00:00 UTC',
	'to' => '2018-03-22T13:00:00 UTC',
	'range' => [ '193.1.98.0/24', '193.1.100.0/24', '193.1.104.0/28' ],
	'value' => 30 // 22 expected
];

## Add throttling definitions above.
