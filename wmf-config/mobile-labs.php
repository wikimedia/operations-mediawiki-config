<?php

# WARNING: This file is publically viewable on the web.
# # Do not put private data here.

// Reuse most of production settings
require_once( __DIR__ . '/mobile.php' );

if ( $wmgMobileFrontend ) {
	if ( $wmgZeroBanner && !$wmgZeroPortal ) {
		require_once( "$IP/extensions/JsonConfig/JsonConfig.php" );
		require_once( "$IP/extensions/ZeroBanner/ZeroBanner.php" );

		$wgZeroEnableTesting = true; // BETA ONLY!

		$wgJsonConfigs['JsonZeroConfig'] = array(
			'namespace' => NS_ZERO,
			'nsname' => 'Zero',
			'islocal' => false,
			'url' => 'http://zero.wikimedia.beta.wmflabs.org/w/api.php',
			'username' => $wmgZeroPortalApiUserName,
			'password' => $wmgZeroPortalApiPassword,
		);
		unset( $wgGroupPermissions['zeroadmin'] );
		unset( $wgGroupPermissions['zeroscript'] );
		unset( $wgGroupPermissions['zeroscriptips'] );
	}
}

$wgMFForceSecureLogin = false;
$wgMFUseCentralAuthToken = $wmgMFUseCentralAuthToken;

// Keep Going experiments
$wgMFKeepGoing = true;
