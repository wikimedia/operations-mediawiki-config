<?php

# WARNING: This file is publically viewable on the web.
# Do not put private data here.

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

$wmgThrottlingExceptions[] = [ // T178689 - Working Class Movement Library (Salford)
	'from' => '2017-11-19T09:00 -0:00',
	'to' => '2017-11-19T17:00 -0:00',
	'IP' => '212.121.214.227',
	'dbname' => [ 'enwiki', 'commonswiki' ],
	'value' => 50,
];

$wmgThrottlingExceptions[] = [ // T180441 - Medicine Faculty campus
	'from'   => '2017-11-14T00:00 +0:00',
	'to'     => '2017-11-22T00:00 +0:00',
	'IP'     => '84.89.157.249',
	'dbname' => [ 'cawiki' ],
	'value'  => 50 // Just in case value is 50
];

## Add throttling definitions above.
