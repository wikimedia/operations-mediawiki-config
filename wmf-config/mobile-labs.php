<?php

# WARNING: This file is publically viewable on the web.
# # Do not put private data here.

// Reuse most of production settings
require_once( __DIR__ . '/mobile.php' );

if ( $wmgMobileFrontend ) {
	if ( $wmgZeroBanner ) {
		$wgZeroBannerClusterDomain = 'beta.wmflabs.org'; // need a better way to calc this
		if ( !$wmgZeroPortal ) {
			$wgJsonConfigs['JsonZeroConfig']['remote']['url'] = 'http://zero.wikimedia.beta.wmflabs.org/w/api.php';
		}
	}

	if ( $wmgUseGather ) {
		require_once "$IP/extensions/Gather/Gather.php";
	}
}

$wgMFForceSecureLogin = false;
$wgMFUseCentralAuthToken = $wmgMFUseCentralAuthToken;
$wgMFSpecialCaseMainPage = $wmgMFSpecialCaseMainPage;

$wgMFWikiDataEndpoint = $wmgMFWikiDataEndpoint;
$wgWikiBasePropertyConfig = $wmgWikiBasePropertyConfig;
$wgMFInfoboxConfig = $wmgMFInfoboxConfig;

$wgMFIsBrowseEnabled = $wmgMFIsBrowseEnabled;
$wgMFBrowseTags = $wmgMFBrowseTags;
