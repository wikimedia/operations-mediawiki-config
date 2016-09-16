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

# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #
# Woman in Science
#

$wmgThrottlingExceptions[] = [ // T145253 - Vancouver, British Columbia Institute of Technology
	'from'   => '2016-09-21T09:30 -7:00',
	'to'     => '2016-09-21T16:00 -7:00',
	'range'  => '142.232.0.0/16',
	'dbname' => [ 'enwiki', 'frwiki', 'commonswiki' ],
	'value'  => 70 // 50 expected
];

$wmgThrottlingExceptions[] = [ // T143951 - Vancouver, University of British Columbia
	'from' => '2016-09-21T10:00 -8:00',
	'to' => '2016-09-21T16:00 -8:00',
	'range' => [ '128.189.64.0/19', '128.189.192.0/18', '206.12.64.0/21', '206.87.112.0/21', '206.87.120.0/21', '206.87.128.0/19', '206.87.192.0/21', '206.87.208.0/21', '206.87.216.0/21', '137.82.104.0/24', '137.82.79.0/24', '137.82.82.128/25' ],
	'dbname' => [ 'enwiki' ],
	'value' => 70 //50 expected
];

$wmgThrottlingExceptions[] = [ // T145115 - Montréal, Concordia Library and McGill University
	'from' => '2016-09-21T13:00 -4:00',
	'to' => '2016-09-21T17:00 -4:00',
	'range' => '132.205.228.0/24',
	'dbname' => [ 'enwiki', 'frwiki' ],
	'value' => 50 //40 expected
];

# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #
# Other rules
#

$wmgThrottlingExceptions[] = [ // https://www.mediawiki.org/wiki/Wikimedia_Hackathon_Amrita_University
	'from' => '2016-09-07T00:00 +5:30',
	'to' => '2016-10-03T00:00 +5:30',
	'IP' => '182.19.48.18',
	'dbname' => 'labswiki',
	'value' => 60 //50 expected
];

$wmgThrottlingExceptions[] = [
	'from' => '2016-09-17T9:00 +12:00',
	'to' => '2016-09-18T16:00 +12:00',
	'IP' => '49.224.252.88',
	'dbname' => [ 'enwiki' ],
	'value' => 60 //50 expected
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
		} elseif ( isset ( $options['ip'] ) ) {
			$throttleIP = $options['ip']; // Allow frequent case typo
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
