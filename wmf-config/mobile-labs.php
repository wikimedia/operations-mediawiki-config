<?php

# WARNING: This file is publically viewable on the web.
# # Do not put private data here.


if ( $wmgMobileFrontend ) {
	if ( $wmgZeroBanner ) {
		$wgZeroBannerClusterDomain = 'beta.wmflabs.org'; // need a better way to calc this
		if ( !$wmgZeroPortal ) {
			$wgJsonConfigs['JsonZeroConfig']['remote']['url'] = 'https://zero.wikimedia.beta.wmflabs.org/w/api.php';
		}
	}
}

// T114552
$wgMobileFrontendLogo = $wgLogo;

$wgMFForceSecureLogin = false;
$wgMFUseCentralAuthToken = $wmgMFUseCentralAuthToken;
$wgMFSpecialCaseMainPage = $wmgMFSpecialCaseMainPage;

$wgMFMobileFormatterHeadings = $wmgMFMobileFormatterHeadings;

// T49647
$wgHooks['EnterMobileMode'][] = function() {
	global $wgCentralAuthCookieDomain, $wgHooks;

	if ( preg_match( '\.wikimedia\.org$', $wgCentralAuthCookieDomain ) ) {
		$wgCentralAuthCookieDomain = preg_replace( '\.wikimedia\.org$', '.m.wikimedia.org',
			$wgCentralAuthCookieDomain );
	}
	$wgHooks['WebResponseSetCookie'][] = function ( &$name, &$value, &$expire, &$options ) {
		if ( isset( $options['domain'] ) && preg_match( '\.wikimedia\.org$', $options['domain'] ) ) {
			$options['domain'] = preg_replace( '\.wikimedia\.org$', '.m.wikimedia.org',
				$options['domain'] );
		}
	};
};

