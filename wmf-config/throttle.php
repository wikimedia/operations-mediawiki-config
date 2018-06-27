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

$wmgThrottlingExceptions[] = [ // T194630
	'from' => '2018-05-17T00:00 UTC',
	'to' => '2018-05-19T00:00 UTC',
	'range' => [ '46.140.254.0/28', '46.140.254.16/28', '46.140.254.32/28' ],
	'value' => 100 // 70 expected
];

$wmgThrottlingExceptions[] = [ // T194392 - WMF Hackhathon
	'from' => '2018-05-17T05:00 UTC',
	'to' => '2018-05-21T10:00 UTC',
	'range' => '158.109.94.208/28',
	'value' => 250
];

$wmgThrottlingExceptions[] = [ // T194888
	'from' => '2018-05-19 05:00 UTC',
	'to' => '2018-05-19 23:00 UTC',
	'IP' => '80.113.4.226',
	'dbname' => [ 'enwiki', 'nlwiki', 'commonswiki', 'wikidatawiki' ],
	'value' => 300 // 250 expected
];

$wmgThrottlingExceptions[] = [ // T198288
	'from' => '2018-07-18T09:00 UTC',
	'to' => '2018-07-22T17:00 UTC',
	'IP' => '197.101.76.134',
	'value' => 300 // 250 expected
];

## Add throttling definitions above.
