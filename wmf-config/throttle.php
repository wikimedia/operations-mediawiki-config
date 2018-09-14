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

$wmgThrottlingExceptions[] = [ // T203392
	'from' => '2018-09-15T09:00 +3:00',
	'to' => '2018-09-15T17:00 +3:00',
	'IP' => '94.187.48.185',
	'dbname' => [ 'enwiki', 'arwiki', 'frwiki', 'wikidatawiki' ],
	'value' => 30 // no more than 30 expected
];

$wmgThrottlingExceptions[] = [ // T203392
	'from' => '2018-09-22T09:00 +3:00',
	'to' => '2018-09-22T17:00 +3:00',
	'IP' => '94.187.48.185',
	'dbname' => [ 'enwiki', 'arwiki', 'frwiki', 'wikidatawiki' ],
	'value' => 30 // no more than 30 expected
];

$wmgThrottlingExceptions[] = [ // T203909
	'from' => '2018-09-10T09:00 +2:00',
	'to' => '2018-10-10T09:00 +2:00',
	'IP' => '93.91.145.154',
	'dbname' => [ 'cswiki', 'cswikiversity', 'commonswiki', 'wikidatawiki' ],
	'value' => 120 // 100 expected
];

$wmgThrottlingExceptions[] = [ // T204243
	'from' => '2018-09-17T10:00 UTC',
	'to' => '2018-09-17T13:00 UTC',
	'IP' => '82.141.221.77',
	'dbname' => [ 'enwiki', 'commonswiki', 'wikidatawiki' ],
	'value' => 20 // 12 expected
];

## Add throttling definitions above.
