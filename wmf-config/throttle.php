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

$wmgThrottlingExceptions[] = [ // T217492
	'from' => '2019-03-07T16:00 CST',
	'to' => '2019-03-07T21:00 CST',
	'IP' => [ '131.212.251.13', '131.212.251.62' ],
	'dbname' => 'enwiki',
	'value' => 50
];

$wmgThrottlingExceptions[] = [ // T216642
	'from' => '2019-03-07T13:00 -6:00',
	'to' => '2019-03-07T20:00 -6:00',
	'IP' => '24.137.221.250',
	'dbname' => [ 'enwiki', 'frwiki', 'commonswiki', 'wikidatawiki' ],
	'value' => 50
];

$wmgThrottlingExceptions[] = [ // T217063
	'from' => '2019-03-07T16:00 UTC',
	'to' => '2019-03-09T22:00 UTC',
	'IP' => '185.85.80.119',
	'value' => 80 // 75 expected
];

$wmgThrottlingExceptions[] = [ // T217485
	'from' => '2019-03-08T11:00',
	'to' => '2019-03-03T20:00',
	'IP' => [ '2a01:388:3ec:150::1:69', '31.220.243.2' ],
	'value' => 30 // 25 expected
];

$wmgThrottlingExceptions[] = [ // T217270
	'from' => '2019-03-08T13:00 +1:00',
	'to' => '2019-03-08T19:00 +1:00',
	'IP' => '194.228.196.244',
	'dbname' => [ 'cswiki', 'commonswiki', 'wikidatawiki' ],
	'value' => 30
];

$wmgThrottlingExceptions[] = [ // T217311
	'from' => '2019-03-08T10:00 UTC',
	'to' => '2019-03-08T22:00 UTC',
	'IP' => '86.83.7.133',
	'value' => 30 // 25 expected
];

$wmgThrottlingExceptions[] = [ // T217676
	'from' => '2019-03-08T10:00 UTC',
	'to' => '2019-03-08T23:59 UTC',
	'IP' => '37.0.85.138',
	'value' => 25 // max 25 expected
];

$wmgThrottlingExceptions[] = [ // T217155
	'from' => '2019-03-26T08:30 -5:00',
	'to' => '2019-03-26T21:00 -5:00',
	'IP' => '192.154.64.244',
	'dbname' => [ 'enwiki', 'commonswiki', 'wikidatawiki' ],
	'value' => 220 // 200 expected
];

$wmgThrottlingExceptions[] = [ // T217929
	'from' => '2019-03-25T09:00 -8:00',
	'to' => '2019-03-25T18:00 -8:00',
	'IP' => [ '157.242.0.0', '157.242.127.255' ],
	'dbname' => 'enwiki',
	'value' => 120 // 100 expected
];

$wmgThrottlingExceptions[] = [ // T213869
	'from' => '2019-05-15T0:00 UTC',
	'to' => '2019-05-20T0:00 UTC',
	'range' => '195.113.241.0/24',
	'value' => 400,
];

## Add throttling definitions above.
