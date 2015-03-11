<?php

# WARNING: This file is publically viewable on the web.
# Do not put private data here.

# Initialize the array. Append to that array to add a throttle
$wmgThrottlingExceptions = array();

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
## Add throttling definitions below.

$wmgThrottlingExceptions[] = array( // T88203
	'from'   => '2015-02-26T00:00 +1:00',
	'to'     => '2015-02-27T00:00 +1:00',
	'IP'  => array( '195.113.132.25' ),
	'dbname' => array( 'cswiki', 'commonswiki' ),
	'value'  => 25,
);

$wmgThrottlingExceptions[] = array( // T90778
	'from'   => '2015-03-13T17:00 +0:00',
	'to'     => '2015-03-14T23:00 +0:00',
	'IP'  => array( '186.67.30.194' ),
	'dbname' => array( 'eswiki' ),
	'value'  => 60,
);

$wmgThrottlingExceptions[] = array( // T91936
	'from'   => '2015-03-08T12:00 -5:00',
	'to'     => '2015-03-08T16:00 -5:00',
	'IP'     => array( '65.116.184.106' ),
	'dbname' => array( 'enwiki', 'commonswiki' ),
	'value'  => 100,
);

## Add throttling definitions above.

/**
 * Helper to easily add a throttling request.
 */
$wgExtensionFunctions[] = function() {
	global $wmgThrottlingExceptions, $wgDBname, $wgRequest;

	foreach ( $wmgThrottlingExceptions as $options ) {
		# Validate entry, skip when it does not apply to our case

		# 1) skip when it does not apply to our database name

		if( isset( $options['dbname'] ) ) {
			if ( is_array( $options['dbname'] ) ) {
				if ( !in_array( $wgDBname, $options['dbname'] ) ) {
					continue;
				}
			} elseif ( $wgDBname != $options['dbname'] ) {
				continue;
			}
		}

		# 2) skip expired entries
		$inTimeWindow = time() >= strtotime( $options['from'] )
				&& time() <= strtotime( $options['to'] );

		if( !$inTimeWindow ) {
			continue;
		}

		# 3) skip when throttle does not apply to the client IP
		$ip = $wgRequest->getIP();
		if ( isset( $options['IP'] ) ) {
			if ( is_array( $options['IP'] ) && !in_array( $ip, $options['IP'] ) ) {
				continue;
			} elseif ( $ip != $options['IP'] ) {
				continue;
			}
		}
		if ( isset ( $options['range'] ) ) {
			//Checks if the IP is in range
			if ( is_array( $options['range'] ) && !IP::isInRanges( $ip, $options['range'] ) ) {
				continue;
			} elseif ( !IP::isInRange( $ip, $options['range'] ) ) {
				continue;
			}
		}

		# Finally) set up the throttle value
		global $wgAccountCreationThrottle, $wgRateLimits;
		if( isset( $options['value'] ) && is_numeric( $options['value'] ) ) {
			$wgAccountCreationThrottle = $options['value'];
		} else {
			$wgAccountCreationThrottle = 50; // Provide some sane default
		}
		$wgRateLimits['badcaptcha']['ip'] = array( 1000, 86400 );
		$wgRateLimits['badcaptcha']['newbie'] = array( 1000, 86400 );
		return; # No point in proceeding to another entry
	}
};

