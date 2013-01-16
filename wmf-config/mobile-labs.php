<?php

# WARNING: This file is publically viewable on the web.
# # Do not put private data here.

if( $wmgMobileFrontend ) {

	require_once( "$IP/extensions/MobileFrontend/MobileFrontend.php" );

	if ( $wmgMobileFrontendLogo ) {
		$wgMobileFrontendLogo = $wmgMobileFrontendLogo;
	}
	if ( $wmgMFRemovableClasses ) {
		$wgMFRemovableClasses = $wmgMFRemovableClasses;
	}
	if ( $wmgMFCustomLogos ) {
		if ( isset( $wmgMFCustomLogos['copyright'] ) ) {
			$wmgMFCustomLogos['copyright'] = str_replace( '{wgExtensionAssetsPath}', $wgExtensionAssetsPath, $wmgMFCustomLogos['copyright'] );
		}
		$wgMFCustomLogos = $wmgMFCustomLogos;
	}
	// If a URL template is set for MobileFrontend, use it.
	if ( $wmgMobileUrlTemplate ) {
		$wgMobileUrlTemplate = $wmgMobileUrlTemplate;
	}

	#if ( $wmgZeroRatedMobileAccess ) {
	#	require_once( "$IP/extensions/ZeroRatedMobileAccess/ZeroRatedMobileAccess.php" );
	#}

	#if ( $wmgZeroDisableImages ) {
	#	if ( isset( $_SERVER['HTTP_X_SUBDOMAIN'] ) && strtoupper( $_SERVER['HTTP_X_SUBDOMAIN'] ) == 'ZERO' ) {
	#		$wgZeroDisableImages = $wmgZeroDisableImages;
	#	}
	#}

	// Enable loading of desktop-specific resources from MobileFrontend
	if ( $wmgMFEnableDesktopResources ) {
		$wgMFEnableDesktopResources = true;
	}

	// Enable appending of TM (text) / (R) (icon) on site name in footer.
	// See bug 41141 though, we may wish to disable on some sites.
	$wgMFTrademarkSitename = true;

	// Disabled on deployment-prep
	$wgMFLogEvents = false;

	// Force HTTPS for login/account creation
	$wgMFForceSecureLogin = $wmgMFForceSecureLogin;

} # safeguard
