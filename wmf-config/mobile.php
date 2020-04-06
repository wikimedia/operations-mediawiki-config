<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

wfLoadExtension( 'MobileFrontend' );
wfLoadSkin( 'MinervaNeue' );

$wgMFMobileHeader = 'X-Subdomain';
if ( !$wmgEnableGeoData ) {
	$wgMFNearby = false;
}

$wgHooks['EnterMobileMode'][] = function () {
	global $wgCentralAuthCookieDomain, $wgHooks, $wgIncludeLegacyJavaScript;

	// Disable loading of legacy wikibits in the mobile web experience
	$wgIncludeLegacyJavaScript = false;

	// Hack for T49647
	if ( $wgCentralAuthCookieDomain == 'commons.wikimedia.org' ) {
		$wgCentralAuthCookieDomain = 'commons.m.wikimedia.org';
	} elseif ( $wgCentralAuthCookieDomain == 'meta.wikimedia.org' ) {
		$wgCentralAuthCookieDomain = 'meta.m.wikimedia.org';
	}

	// Better hack for T49647
	$wgHooks['WebResponseSetCookie'][] = function ( &$name, &$value, &$expire, &$options ) {
		if ( isset( $options['domain'] ) ) {
			if ( $options['domain'] == 'commons.wikimedia.org' ) {
				$options['domain'] = 'commons.m.wikimedia.org';
			} elseif ( $options['domain'] == 'meta.wikimedia.org' ) {
				$options['domain'] = 'meta.m.wikimedia.org';
			}
		}
	};

	return true;
};

$wgMFNearbyRange = $wgMaxGeoSearchRadius;

// Turn on volunteer recruitment
$wgMFEnableJSConsoleRecruitment = true;

// Brute-force bandwidth optimization by stripping srcset (T119797)
$wgMFStripResponsiveImages = true;

if ( $wmgMFDefaultEditor ) {
	$wgMFDefaultEditor = $wmgMFDefaultEditor;
}
