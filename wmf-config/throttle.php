<?php

# WARNING: This file is publically viewable on the web.
# Do not put private data here.

## Add throttling definition below by calling efRaiseAccountCreationThrottle(),
# The helper functions takes an array of parameters:
#  'from'  => date/time to start raising account creation throttle
#  'to'    => date/time to stop
#  'value' => new value for $wgAccountCreationThrottle
#
# Additionally, one can restrict by client IP / dbname project:
#  'IP'     => client IP as given by wfGetIP()
#  'dbname' => a $wgDBname to compare to
#

# bug 37740
efRaiseAccountCreationThrottle( array(
	'from'  => '2012-06-20T13:00 +0:00',
	'to'    => '2012-06-20T19:00 +0:00',
	'IP'    => '192.114.7.2',
	'value' => 50,
) );

## Add throttling defintion above.

# Will eventually raise value when fully initialized:
$wgExtensionFunctions[] = 'efRaiseAccountCreationThrottle';

/**
 * Helper to easily add a throttling request.
 *
 */
function efRaiseAccountCreationThrottle( $options ) {
	# Validate entry, skip when it does not apply to our case

	# 1) skip when it does not apply to our database name
	global $wgDBname;
	if( isset( $options['dbname'] ) && $wgDBname != $options['dbname'] ) {
		return;
	}

	# 2) skip expired entries
	$inTimeWindow = time() >= strtotime( $options['from'] )
	   	&& time() <= strtotime( $options['to'] );
	if( !$inTimeWindow ) {
		return;
	}

	# 3) skip when it does not apply to the client IP
	if( isset( $options['IP'] ) && wfGetIP() != $throttle['IP'] ) {
		return;
	}

	if( is_numeric( $throttle['value'] ) ) {
		global $wgAccountCreationThrottle;
		$wgAccountCreationThrottle = $throttle['value'];
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



