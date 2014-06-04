<?php

# WARNING: This file is publically viewable on the web.
# # Do not put private data here.

// Reuse most of production settings
require_once( __DIR__ . '/mobile.php' );

if ( $wmgMobileFrontend ) {
	if ( $wmgZeroBanner ) {
		require_once( "$IP/extensions/JsonConfig/JsonConfig.php" );
		require_once( "$IP/extensions/ZeroBanner/ZeroBanner.php" );
		$wgJsonConfigs['JsonZeroConfig'] = array(
			'namespace' => NS_ZERO,
			'nsname' => 'Zero',
			'islocal' => false,
			'url' => 'https://zero.wikimedia.org/w/api.php',
			'username' => $wmgZeroRatedMobileAccessApiUserName,
			'password' => $wmgZeroRatedMobileAccessApiPassword,
		);
	}
}

$wgMFForceSecureLogin = false;
$wgMFUseCentralAuthToken = $wmgMFUseCentralAuthToken;

// Zero extension
$wgEnableZeroRatedMobileAccessTesting = true;  // Delete once ZRMA extension is removed
$wgZeroEnableTesting = true;


// Keep Going experiments
$wgMFKeepGoing = true;
