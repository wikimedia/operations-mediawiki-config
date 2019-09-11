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

$wmgThrottlingExceptions[] = [ // T227059
	'from' => '2019-07-03T0:00 +1:00',
	'to' => '2019-07-05T22:00 +1:00',
	'IP' => '131.175.147.29',
	'dbname' => [ 'enwiki', 'itwiki', 'commonswiki', 'wikidatawiki' ],
	'value' => 60 // 50 requested
];

$wmgThrottlingExceptions[] = [ // T230313
	'from' => '2019-08-22T10:00 +1:00',
	'to' => '2019-08-22T18:00 +1:00',
	'IP' => '185.142.12.73',
	'dbname' => 'cawiki',
	'value' => 30 // 20 requested
];

$wmgThrottlingExceptions[] = [ // T232596
	'from' => '2019-09-10T0:00 UTC',
	'to' => '2019-09-30T0:00 UTC',
	'range' => '194.214.218.20/30',
	'dbname' => 'frwiki',
	'value' => 200 // 178 enrolled students
];

## Add throttling definitions above.
