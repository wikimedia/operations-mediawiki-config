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

$wmgThrottlingExceptions[] = [ // T149063 - Nashville Architecture edit-a-thon (Vanderbilt library)
	'from'   => '2016-10-25T10:30 -5:00',
	'to'     => '2016-10-25T16:00 -5:00',
	'IP'     => '129.59.122.18',
	'dbname' => [ 'enwiki', 'commonswiki' ],
	'value'  => 50 // 20 to 30 expected
];

$wmgThrottlingExceptions[] = [ // T148852 - Edit-a-thon BDA (Poitiers)
	'from'   => '2016-11-15T08:00 -1:00',
	'to'     => '2016-11-18T19:00 -1:00',
	'IP'     => '193.55.161.2',
	'dbname' => [ 'enwiki', 'frwiki', 'commonswiki' ],
	'value'  => 50 // 40 expected
];

// December 2nd
$wmgThrottlingExceptions[] = [ // T146600
	'from' => '2016-12-02T12:30 -6:00',
	'to' => '2016-12-02T13:00 -6:00',
	'ramge' => '199.17.0.0/16',
	'dbname' => 'enwiki',
	'value' => 40 // 35 expected
];

//Nov 02
$wmgThrottlingExceptions[] = [ //T149200
	'from' => '2016-11-01T00:00 -0:00',
	'to' => '2016-11-03T00:00 -0:00',
	'range' => '140.254.110.0/23',
	'dbname' => [ 'eswiki', 'enwiki', 'ptwiki'],
	'value' => 30 //25 expected
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
