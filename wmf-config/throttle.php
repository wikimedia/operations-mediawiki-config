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
#	'from'   => '2016-01-01T00:00 +0:00',
#	'to'     => '2016-02-01T00:00 +0:00',
#	'IP'     => '123.456.78.90',
#	'dbname' => [ 'xxwiki', etc. ],
#	'value'  => xx
# ];
## Add throttling definitions below.

$wmgThrottlingExceptions[] = [ // T157504
	'from' => '2017-01-09T00:00:00 UTC',
	'to' =>   '2017-06-31T23:59:59 UTC',
	'IP' => [
		'79.58.14.240',
		'46.226.205.23',
	],
	'dbname' => [ 'itwikiversity' ],
	'value' => 200,
];

$wmgThrottlingExceptions[] = [ // T158312
	'from' => '2017-03-08T09:00 +0:00',
	'to' => '2017-03-08T18:00 +0:00',
	'IP' => '159.92.230.64',
	'dbname' => [ 'enwiki', 'commonswiki' ],
	'value' => 50 // 40 expected
];

$wmgThrottlingExceptions[] = [ // T158762
	'from' => '2017-03-11T13:00 +0:00',
	'to' => '2017-03-11T21:00 +0:00',
	'IP' => '200.72.159.4',
	'dbname' => [ 'eswiki', 'enwiki' ],
	'value' => 40 // 30 expected
];

$wmgThrottlingExceptions[] = [ // T159454
	'from' => '2017-03-05T13:30 +0:00',
	'to' => '2017-03-05T17:30 +0:00',
	'IP' => '86.53.246.93',
	'dbname' => [ 'enwiki', 'commonswiki' ],
	'value' => 50 // 40 expected
];

$wmgThrottlingExceptions[] = [ // T159461
	'from' => '2017-03-08T12:00 +0:00',
	'to' => '2017-03-08T19:00 +0:00',
	'IP' => '131.111.204.8',
	'dbname' => [ 'enwiki', 'commonswiki' ],
	'value' => 100 // 70 expected
];

$wmgThrottlingExceptions[] = [ // T159803
	'from' => '2017-03-10T18:00 +1:00',
	'to' => '2017-03-10T23:090 +1:00',
	'IP' => '195.76.195.27',
	'dbname' => [ 'cawiki', 'commonswiki' ],
	'value' => 40 // +20 expected
];


## Add throttling definitions above.

/**
 * Helper to easily add a throttling request.
 */
$wgExtensionFunctions[] = function() {
	global $wmgThrottlingExceptions, $wgDBname, $wgRequest;

	foreach ( $wmgThrottlingExceptions as $options ) {
		# Validate entry, skip when it does not apply to our case

		# 1) skip when it does not apply to our database name

		if ( isset( $options['dbname'] ) && !in_array( $wgDBname, (array) $options['dbname'] ) ) {
			continue;
		}

		# 2) skip expired entries
		$inTimeWindow = time() >= strtotime( $options['from'] )
				&& time() <= strtotime( $options['to'] );

		if ( !$inTimeWindow ) {
			continue;
		}

		# 3) skip when throttle does not apply to the client IP
		$ip = $wgRequest->getIP();
		if ( isset ( $options['IP'] ) ) {
			$throttleIP = $options['IP'];
		}
		if ( isset( $throttleIP ) && !in_array( $ip, (array) $throttleIP ) ) {
			continue;
		}
		if ( isset ( $options['range'] ) && !IP::isInRanges( $ip, (array) $options['range'] ) ) {
			continue;
		}

		# Finally) set up the throttle value
		global $wgAccountCreationThrottle, $wgRateLimits;
		if ( isset( $options['value'] ) && is_numeric( $options['value'] ) ) {
			$wgAccountCreationThrottle = $options['value'];
		} else {
			$wgAccountCreationThrottle = 50; // Provide some sane default
		}
		$wgRateLimits['badcaptcha']['ip'] = [ 1000, 86400 ];
		$wgRateLimits['badcaptcha']['newbie'] = [ 1000, 86400 ];
		return; # No point in proceeding to another entry
	}
};
