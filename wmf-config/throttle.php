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
	'from' => '2018-04-04T10:00 -5:00',
	'to' => '2018-04-04T11:00 -5:00',
	'range' => [ '24.75.223.0/24' ],
	'dbname' => [ 'enwiki' ],
	'value' => 40 // 30 excepted
];

$wmgThrottlingExceptions[] = [ // T191168
	'from' => '2018-04-04T8:00 +2:00',
	'to' => '2018-04-04T13:00 +2:00',
	'range' => [ '185.153.192.0/23' ],
	'dbname' => [ 'cswiki', 'commonswiki' ],
	'value' => 20 // up to 20 expected
];

## Add throttling definitions above.
