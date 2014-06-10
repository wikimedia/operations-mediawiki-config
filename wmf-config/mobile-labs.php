<?php

# WARNING: This file is publically viewable on the web.
# # Do not put private data here.

// Reuse most of production settings
require_once( __DIR__ . '/mobile.php' );

if ( $wmgMobileFrontend ) {
	if ( $wmgZeroBanner && !$wmgZeroPortal ) {
		require_once( "$IP/extensions/JsonConfig/JsonConfig.php" );
		require_once( "$IP/extensions/ZeroBanner/ZeroBanner.php" );

		$wgJsonConfigs['JsonZeroConfig']['remote'] = array(
			'url' => 'http://zero.wikimedia.beta.wmflabs.org/w/api.php',
			'username' => $wmgZeroPortalApiUserName,
			'password' => $wmgZeroPortalApiPassword,
		);


		// @TODO: which group(s) on all wikies should have this right?
		$wgGroupPermissions['sysop']['jsonconfig-flush'] = true;

		// LABS only:
		$wgZeroEnableTesting = true; // BETA ONLY!
		// These are set in mobile.php, unsetting
		unset( $wgGroupPermissions['zeroadmin'] );
		unset( $wgGroupPermissions['zeroscript'] );
		unset( $wgGroupPermissions['zeroscriptips'] );
	}
}

$wgMFForceSecureLogin = false;
$wgMFUseCentralAuthToken = $wmgMFUseCentralAuthToken;

// Keep Going experiments
$wgMFKeepGoing = true;
