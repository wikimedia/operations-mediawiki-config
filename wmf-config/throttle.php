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

$wmgThrottlingExceptions[] = [
	'from' => '2018-10-25T05:00 UTC',
	'to' => '2018-10-28T10:00',
	'range' => '194.171.184.0/24',
	'dbname' => [ 'enwiki', 'nlwiki', 'commonswiki', 'wikidatawiki' ],
	'value' => 140, // 120 expected
];

$wmgThrottlingExceptions[] = [ // T206408
	'from' => '2018-11-29T18:00 +1:00',
	'to' => '2018-11-29T23:00 +1:00',
	'IP' => [ '195.76.195.27' ],
	'dbname' => [ 'cawiki', 'commonswiki' ],
	'value' => 50 // 30 expected
];

$wmgThrottlingExceptions[] = [ // T207714
	'from' => '2018-10-28T09:00 +1:00',
	'to' => '2018-10-28T19:00 +1:00',
	'IP' => [ '212.185.234.107', '212.185.235.173', '212.185.235.30', '212.185.232.1', '212.185.235.65', '212.185.235.152', '212.185.234.204' ],
	'dbname' => [ 'dewiki', 'wikidatawiki', 'commonswiki' ],
	'value' => 40 // 30 expected
];

$wmgThrottlingExceptions[] = [ // T207742
	'from' => '2018-10-27T09:00 GMT+2',
	'to' => '2018-10-27T18:00 GMT+2',
	'IP' => '197.157.93.50',
	'dbname' => [ 'enwiki', 'afwiki', 'nsowiki', 'tswiki', 'sswiki', 'stwiki', 'tnwiki', 'vewiki', 'xhwiki', 'zuwiki' ],
	'value' => 150, // 110 expected
];

$wmgThrottlingExceptions[] = [ // T208236
	'from' => '2018-10-31T00:00 GMT',
	'to' => '2018-10-31T23:59 GMT',
	'range' => '192.41.128.0/22',
	'dbname' => [ 'wikidatawiki' ],
	'value' => 25 // 20 expected
];

## Add throttling definitions above.
