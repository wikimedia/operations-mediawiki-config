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
#
## If you are adding a throttle exception with a 'from' time that is less than
## 72 hours in advance, you will also need to manually clear a cache after
## deploying your change to this file!
## https://wikitech.wikimedia.org/wiki/Increasing_account_creation_threshold

$wmgThrottlingExceptions[] = [ //T246832
	'from' => '2020-04-01T15:59 +1:00',
	'to' => '2020-04-01T21:01 +1:00',
	'range' => [ '134.155.0.0/16', '2001:7C0:2900::/40' ],
	'dbname' => [ 'dewiki', 'commonswiki', 'enwiki', 'frwiki' ],
	'value' => 50
];

$wmgThrottlingExceptions[] = [ // T259352
	'from' => '2020-08-06T12:00 +2:00',
	'to' => '2020-08-06T20:00 +2:00',
	'IP' => [ '193.86.79.202' ],
	'dbname' => [ 'cswiki', 'commonswiki' ],
	'value' => 30 // 20 expected
];

$wmgThrottlingExceptions[] = [ // T261882
	'from' => '2020-09-08T14:00 +2:00',
	'to' => '2020-09-14T17:00 +2:00',
	'range' => [ '195.113.180.192/26', '2001:718:9::/48' ],
	'dbname' => [ 'cswiki', 'commonswiki' ],
	'value' => 20
];

$wmgThrottlingExceptions[] = [
	'from' => '2020-09-14T13:00 +2:00',
	'to' => '2020-09-14T19:00 +2:00',
	'range' => '185.153.192.0/26',
	'dbname' => [ 'cswiki', 'commonswiki' ],
	'value' => 15 // 12 expected
];

## Add throttling definitions above.
