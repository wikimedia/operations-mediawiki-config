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

$wmgThrottlingExceptions[] = [ // T192827
	'from' => '2018-04-28T9:00 -7:00',
	'to' => '2018-04-28T16:00 -7:00',
	'IP' => '205.234.62.142',
	'dbname' => [ 'enwiki', 'commonswiki' ],
	'value' => 40 // 30 expected
];

$wmgThrottlingExceptions[] = [ // T192898
	'from' => '2018-05-03T14:00 +2:00',
	'to' => '2018-05-03T18:00 +2:00',
	'range' => '195.113.154.0/24',
	'dbname' => [ 'cswiki', 'commonswiki' ],
	'value' => 15 // 10 expected
];

## Add throttling definitions above.
