<?php

# WARNING: This file is publically viewable on the web.
# # Do not put private data here.

if ( $wmgMobileFrontend ) {
	require_once( "$IP/extensions/MobileFrontend/MobileFrontend.php" );
	$wgMFNoindexPages = false;
	$wgMFNearby = $wmgMFNearby && $wmgEnableGeoData;
	$wgMFPhotoUploadEndpoint = $wmgMFPhotoUploadEndpoint;
	$wgMFPhotoUploadWiki = $wmgMFPhotoUploadWiki;
	$wgMFPhotoUploadAppendToDesc = $wmgMFPhotoUploadAppendToDesc;

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
}


// If a URL template is set for MobileFrontend, use it.
if ( $wmgMobileUrlTemplate ) {
	$wgMobileUrlTemplate = $wmgMobileUrlTemplate;
}

if ( $wmgZeroRatedMobileAccess ) {
	require_once( "$IP/extensions/ZeroRatedMobileAccess/ZeroRatedMobileAccess.php" );
}

if ( $wmgZeroDisableImages ) {
	if ( isset( $_SERVER['HTTP_X_SUBDOMAIN'] ) && strtoupper( $_SERVER['HTTP_X_SUBDOMAIN'] ) == 'ZERO' ) {
		$wgZeroDisableImages = $wmgZeroDisableImages;
	}
}

// Enable loading of desktop-specific resources from MobileFrontend
if ( $wmgMFEnableDesktopResources ) {
	$wgMFEnableDesktopResources = true;
}

// Enable appending of TM (text) / (R) (icon) on site name in footer.
// See bug 41141 though, we may wish to disable on some sites.
$wgMFTrademarkSitename = true;

$wgMFLogEvents = $wmgMFLogEvents;

// Enable Schemas for event logging (jdlrobson; 07-Feb-2012)
if ( $wgMFLogEvents && $wmgUseEventLogging ) {
	$wgResourceModules['mobile.watchlist.schema'] = array(
		'class' => 'ResourceLoaderSchemaModule',
		'schema' => 'MobileBetaWatchlist',
		'revision' => 5281061,
		'targets' => 'mobile',
	);

	$wgResourceModules['mobile.uploads.schema'] = array(
		'class' => 'ResourceLoaderSchemaModule',
		'schema' => 'MobileWebUploads',
		'revision' => 5383883,
		'targets' => 'mobile',
	);

	$wgHooks['EnableMobileModules'][] = function( $out, $mode ) {
		$modules = array(
			'mobile.uploads.schema',
			'mobile.watchlist.schema',
		);
		// add regardless of mode
		$out->addModules( $modules );
		return true;
	};
}

// Force HTTPS for login/account creation
$wgMFForceSecureLogin = $wmgMFForceSecureLogin;

// Point to Common's Special:LoginHandshake page
$wgMFLoginHandshakeUrl = $wmgMFLoginHandshakeUrl;

// Enable X-Analytics logging
$wgMFEnableXAnalyticsLogging = $wmgMFEnableXAnalyticsLogging;

// Enable $wgMFVaryResources only if there's a mobile site (otherwise we'll end up
// looking for X-WAP headers in requests coming from Squid
if ( $wmgMFVaryResources && $wgMobileUrlTemplate !== '' ) {
	$wgMFVaryResources = true;
	// Point mobile load.php requests to a special path on bits that gets X-Device headers
	$wgHooks['EnterMobileMode'][] = function() {
		global $wgDBname, $wgLoadScript;
		if ( $wgDBname === 'testwiki' ) {
			// testwiki's resources aren't loaded from bits, it just needs a mobile domain
			$wgLoadScript = '//test.m.wikipedia.org/w/load.php';
		} else {
			$wgLoadScript = str_replace( 'bits.wikimedia.org/', 'bits.wikimedia.org/m/', $wgLoadScript );
		}
		return true;
	};
}
