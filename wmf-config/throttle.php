<?php

# WARNING: This file is publically viewable on the web.
# Do not put private data here.

function efRaiseThrottle() {
	global $wgAccountCreationThrottle;
	if ( wfGetIP() == '192.114.7.2' || wfGetIP() == '115.112.231.108' ) { // 192.114.7.2 -> bug 37740, 115.112.231.208 -> bug 37741
		$wgAccountCreationThrottle = 50;
	}
}

if (   ( time() >= strtotime( '2012-06-20T13:00 +0:00' )
	&& time() <= strtotime( '2012-06-20T19:00 +0:00' ) )
	|| ( $wgDBname == "enwiki" &&
		time() >= strtotime( '2012-06-23T04:30 +0:00' ) &&
		time() <= strtotime( '2012-06-23T10:30 +0:00' ) )
) {
	$wgExtensionFunctions[] = 'efRaiseThrottle';
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



