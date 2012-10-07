<?php

# WARNING: This file is publically viewable on the web.
# Do not put private data here.

## Add throttling definitions below,
# The helper functions takes an array of parameters:
#  'from'  => date/time to start raising account creation throttle
#  'to'    => date/time to stop
#
# Optional arguments can be added to set the value or restrict by client IP
# or project dbname. Options are:
#  'value'  => new value for $wgAccountCreationThrottle (default: 50)
#  'IP'     => client IP as given by wfGetIP() (default: any IP)
#  'dbname' => a $wgDBname or array of dbnames to compare to
#             (eg. enwiki, metawiki, frwikibooks, eswikiversity)
#             (default: any project)

# Initialize the array. Append to that array to add a throttle
$wmgThrottlingExceptions = array();

## Add throttling definition below

# bug 40575
$wmfThrottlingExceptions[] = array(
	'from'   => '2012-09-27T18:00 +0:00',
	'to'     => '2012-09-28T02:00 +0:00',
	'IP'     => '177.32.49.25',
	'dbname' => array( 'ptwikiversity' ),
	'value'  => 50,
);

# bug 40669
$wmfThrottlingExceptions[] = array(
	'from'   => '2012-10-04T00:00 +0:00',
	'to'     => '2012-10-05T23:59 +0:00',
	'IP'     => '12.183.19.7',
	'dbname' => array( 'enwiki', 'commonswiki' ),
	'value'  => '50',
);
$wmfThrottlingExceptions[] = array(
	'from'   => '2012-10-06T00:00 +0:00',
	'to'     => '2012-10-07T23:59 +0:00',
	'IP'     => '206.205.237.10',
	'dbname' => array( 'enwiki', 'commonswiki' ),
	'value'  => '100',
);

# bug 40736
$wmfThrottlingExceptions[] = array(
	'from'   => '2012-10-07T03:30 +0:00',
	'to'     => '2012-10-07T15:30 +0:00',
	'IP'     => array( '14.140.227.85', '14.140.227.65' ),
	'dbname' => array( 'enwiki', 'commonswiki' ),
	'value'  => 200,
);

## Add throttling defintion above.

# Will eventually raise value when MediaWiki is fully initialized:
$wgExtensionFunctions[] = 'efRaiseAccountCreationThrottle';

/**
 * Helper to easily add a throttling request.
 */
function efRaiseAccountCreationThrottle() {
	global $wmgThrottlingExceptions, $wgDBname;

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
		if ( isset( $options['IP'] ) ) {
			if ( is_array( $options['IP'] ) && !in_array( wfGetIP(), $options['IP'] ) ) {
				continue;
			} elseif ( wfGetIP() != $options['IP'] ) {
				continue;
			}
		}

		# Finally) set up the throttle value
		global $wgAccountCreationThrottle;
		if( isset( $options['value'] ) && is_numeric( $options['value'] ) ) {
			$wgAccountCreationThrottle = $options['value'];
		} else {
			$wgAccountCreationThrottle = 50; // Provide some sane default
		}
		return; # No point in proceeding to another entry
	}
}



// Added throttle for account creations on zh due to mass registration attack 2005-12-16
// might be useful elesewhere. --brion
// disabled temporarily due to tugela bug -- Tim

if ( false /*$lang == 'zh' || $lang == 'en'*/ ) {
	require( "$IP/extensions/UserThrottle/UserThrottle.php" );
	$wgGlobalAccountCreationThrottle = array(
/*
		'min_interval' => 30,   // Hard minimum time between creations (default 5)
		'soft_time'    => 300, // Timeout for rolling count
		'soft_limit'   => 5,  // 5 registrations in five minutes (default 10)
*/
		'min_interval' => 0,   // Hard minimum time between creations (default 5)
		'soft_time'    => 60, // Timeout for rolling count (default 5 minutes)
		'soft_limit'   => 2,  // 2 registrations in one minutes (default 10)
	);
}



