<?php

# WARNING: This file is publically viewable on the web.
# Do not put private data here.

function efRaiseThrottle() {
	global $wmgAccountCreationThrottlers;
	foreach( $wmgThorttling as $throttle ) {
		if(    time() >= $throttle['from']
		    && time() <= $throttle['to']
		) {
			if( wfGetIP() == $throttle['IP'] ) {
				global $wgAccountCreationThrottle;
				$wgAccountCreationThrottle = $throttle['value'];
			}
		}
	}
}

/**
 * Array of throttling requests.
 */
$wmgAccountCreationThrottlers = array();

## Add throttling definition below:

# bug 37740
efRaiseAccountCreationThrottle(
	'2012-06-20T13:00 +0:00',
	'2012-06-20T19:00 +0:00',
	'192.114.7.2', 50
);

## Add throttling defintion above.

# Enable throttling if we had something defined above.
if( !empty( $wmgAccountCreationThrottlers ) ) {
	$wgExtensionFunctions[] = 'efRaiseThrottle';
}


/**
 * Helper to easily add a throttling request.
 */
function efRaiseAccountCreationThrottle( $from, $to, $ipAddress, $upto ) {
	global $wmgAccountCreationThrottlers;
	$wmgThorttling[] = array(
		'from'  => strtotime( $from ),
		'to'    => strtotime( $to   ),
		'IP'    => $ipAddress,
		'value' => $upto,
	);
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



