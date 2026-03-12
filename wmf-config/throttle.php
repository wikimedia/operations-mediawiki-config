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
#  'value'  => new value for $wgAccountCreationThrottle (default: 50 per day)
#  'tempaccountvalue' => new value for $wgTempAccountCreationThrottle
#    (default: 6 per day)
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
# 'value'  => 100,
# 'tempaccountvalue' => 50,
# ];
## Add throttling definitions below.
#
## If you are adding a throttle exception with a 'from' time that is less than
## 72 hours in advance, you will also need to manually clear a cache after
## deploying your change to this file!
## https://wikitech.wikimedia.org/wiki/Increasing_account_creation_threshold

## Add throttling definitions above.

// T419109 - Women's History Month Edit-a-thon Cambridgeshire Central Library, 14 March 2026
$wmgThrottlingExceptions[] = [
	'from'   => '2026-03-14T13:00 +0:00',
	'to'     => '2026-03-14T18:00 +0:00',
	'range'     => [ '185.111.131.206/31' ],
	'dbname' => 'enwiki',
	'value'  => 40,
];
// T419899 - CEE Women Campaign 2026 editathon for azwiki, 14 March 2026
$wmgThrottlingExceptions[] = [
	'from'   => '2026-03-14T10:00 +0:00',
	'to'     => '2026-03-14T16:00 +0:00',
	'IP'     => '185.233.35.68',
	'dbname' => 'azwiki',
	'value'  => 30,
];
