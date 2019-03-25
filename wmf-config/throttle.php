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

$wmgThrottlingExceptions[] = [ // T213869 - Wikimedia Hackathon 2019, Prague
	'from' => '2019-05-15T0:00 UTC',
	'to' => '2019-05-20T0:00 UTC',
	'range' => '195.113.241.0/24',
	'value' => 400,
];

$wmgThrottlingExceptions[] = [ // T219291
	'from' => '2019-03-30T0:00 UTC',
	'to' => '2019-03-30T23:59 UTC',
	'IP' => '80.250.17.189',
	'dbname' => 'cswiki',
	'value' => 50, // 40 expected
];

$wmgThrottlingExceptions[] = [ // T219113
	'from' => '2019-03-31T08:30 +8:00',
	'to' => '2019-03-31T19:30 +8:00',
	'IP' => '60.251.41.169',
	'dbname' => 'zhwiki',
	'value' => 70 // 50 expected
];

## Add throttling definitions above.
