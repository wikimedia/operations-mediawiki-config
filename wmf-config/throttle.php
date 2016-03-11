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
# Example:
# $wmgThrottlingExceptions[] = array(
#	'from'   => '2016-01-01T00:00 +0:00',
#	'to'     => '2016-02-01T00:00 +0:00',
#	'IP'     => '123.456.78.90',
#	'dbname' => array ( 'xxwiki', etc. ),
#	'value'  => xx
# );
## Add throttling definitions below.


$wmgThrottlingExceptions[] = array( // T128847 - Ateneo de Manila University workshops
	'from'   => '2016-03-10T00:00 +8:00',
	'to'     => '2016-03-10T23:59 +8:00',
	'IP'     => array( '202.125.102.33', '121.58.232.35' ),
	'dbname' => array( 'tlwiki', 'enwiki', 'commonswiki' ),
	'value'  => 100 // 60-80 expected
);

$wmgThrottlingExceptions[] = array( // T129342 - Wikipedia while at Women of the World Festival
	'from'   => '2016-03-13T11:00 +0:00',
	'to'     => '2016-03-13T13:00 +0:00',
	'IP'     => array( '5.148.129.61' ),
	'dbname' => array( 'enwiki' ),
	'value'  => 30 // 20 expected
);

$wmgThrottlingExceptions[] = array( // T129574 - Procomuns Viquimarató - Barcelona
	'from'   => '2016-03-13T10:00 +1:00',
	'to'     => '2016-03-13T19:00 +1:00',
	'IP'     => array( '94.229.206.251' ),
	'dbname' => array( 'cawiki', 'enwiki', 'eswiki', 'frwiki', 'commonswiki' ),
	'value'  => 70 // 20-50 expected
);

$wmgThrottlingExceptions[] = array( // T129018 - Workshop for cawiki and frwiki
	'from'   => '2016-03-16T00:00 +0:00',
	'to'     => '2016-03-16T23:59 +0:00',
	'IP'     => array ( '194.167.137.246', '194.167.137.11', '194.167.137.29' ),
	'dbname' => array ( 'frwiki', 'cawiki' ),
	'value'  => 70 // 30-60 expected
);

$wmgThrottlingExceptions[] = array( // T129490 - Taller d'iniciació a la Viquipèdia, Montserrat
	'from'   => '2016-04-17T15:30 +2:00',
	'to'     => '2016-04-17T17:30 +2:00',
	'ip'     => '80.32.80.220',
	'dbname' => array( 'cawiki', 'commonswiki' ),
	'value'  => 20 // 15 expected
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
		if ( isset( $options['IP'] ) && !in_array( $ip, (array) $options['IP'] ) ) {
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
		$wgRateLimits['badcaptcha']['ip'] = array( 1000, 86400 );
		$wgRateLimits['badcaptcha']['newbie'] = array( 1000, 86400 );
		return; # No point in proceeding to another entry
	}
};
