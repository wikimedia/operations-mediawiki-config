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

///
/// Maharashtra 'Edit Wikipedia…' workshops
///

$wmgMaharashtraEventsWikis = [
        "mrwiki",
        "mrwiktionary",
        "mrwikisource",
        "mrwikibooks",
        "mrwikiquote",
        "enwiki",
        "commonswiki",
];

$wmgThrottlingExceptions[] = [ // T154312 - Maharashtra 'Edit Wikipedia…' workshops (BAMU)
	'from'   => '2017-01-20T07:00+05:30',
	'to'     => '2017-01-20T20:00+05:30',
	'IP'  => '117.232.125.2',
	'dbname' => $wmgMaharashtraEventsWikis,
	'value'  => 100 // expected participants are unknown
];

///
/// Other events
///

$wmgThrottlingExceptions[] = [ // T154245
	'from' => '2017-01-20T0:00 +0:00',
	'to' => '2017-01-20T23:59 +0:00',
	'range' => [ '161.23.0.0/16', '138.37.0.0/16', '2a01:56c0::/32' ],
	'dbname' => 'enwiki',
	'value' => 50 // 40 expected
];

$wmgThrottlingExceptions[] = [ // T155493
	'from' => '2017-01-23T00:00 +0:00',
	'to' => '2017-01-25T00:00 +0:00',
	'range' => '134.190.0.0/16',
	'dbname' => [ 'enwiki', 'frwiki' ],
	'value' => 30 // 20 expected
];

$wmgThrottlingExceptions[] = [ // T155877
	'from'   => '2017-01-23T09:00 +05:30',
	'to'     => '2017-01-23T18:00 +05:30',
	'IP'     => '117.200.183.66',
	'dbname' => [ 'mrwiki', 'enwiki', 'commonswiki' ],
	'value'  => 70 // 50 expected
];

$wmgThrottlingExceptions[] = [ // T156258
	'from' => '2017-02-09T17:00 -5:00',
	'to' => '2017-02-09T20:00 -5:00',
	'range' => ["152.12.0.0/16", "152.13.0.0/16", "152.14.0.0/16", "152.15.0.0/16", "152.16.0.0/16", "152.17.0.0/16"],
	'dbname' => [ 'enwiki', 'commonswiki' ],
	'value' => 30 // max 20 expected
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
