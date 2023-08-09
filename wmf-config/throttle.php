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

// Wikimania 2023, main event
$wmgThrottlingExceptions[] = [
	'from'  => '2023-08-14T00:00 +8:00',
	'to'    => '2023-08-20T23:59 +8:00',
	'IP'    => '101.127.250.66',
	// this is just a guess, feel free to increase or clear throttles
	// if it's not enough
	'value' => 250,
];

// Wikimania 2023 'Knowledge Beyond Boundaries' side event
// https://wikimania.wikimedia.org/wiki/2023:Related_events/Knowledge_Beyond_Boundaries
$wmgThrottlingExceptions[] = [
	'from'  => '2023-08-20T09:00 +8:00',
	'to'    => '2023-08-20T16:59 +8:00',
	'IP'    => '129.126.8.35',
	// 60 is the stated max attendee count, plus a bit of extra
	'value' => 75,
];

## Add throttling definitions above.
