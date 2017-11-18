<?php

# WARNING: This file is publically viewable on the web.
# # Do not put private data here.

if ( $wmgMobileFrontend ) {
	wfLoadExtension( 'MobileFrontend' );

	// Load skin
	if ( $wmgMinervaNeue ) {
		wfLoadSkin( 'MinervaNeue' );
	}

	$wgMFMobileHeader = 'X-Subdomain';
	$wgMFNoindexPages = false;
	if ( !$wmgEnableGeoData ) {
		$wgMFNearby = false;
	}

	if ( $wmgZeroBanner && !$wmgZeroPortal ) {
		wfLoadExtension( 'ZeroBanner' );

		if ( !isset( $wgJsonConfigs ) ) {
			$wgJsonConfigs = [];
		}
		if ( !isset( $wgJsonConfigs['JsonZeroConfig'] ) ) {
			$wgJsonConfigs['JsonZeroConfig'] = [];
		}
		$wgJsonConfigs['JsonZeroConfig']['isLocal'] = false;
		$wgJsonConfigs['JsonZeroConfig']['remote'] = [
			'url' => 'https://zero.wikimedia.org/w/api.php',
			'username' => $wmgZeroPortalApiUserName,
			'password' => $wmgZeroPortalApiPassword,
		];

		$wgGroupPermissions['sysop']['jsonconfig-flush'] = true;
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

	$wgMFEnableBeta = true;

	// Turn on volunteer recruitment
	$wgMFEnableJSConsoleRecruitment = true;

	// Brute-force bandwidth optimization by stripping srcset (T119797)
	$wgMFStripResponsiveImages = true;
}

if ( $wmgRealm === 'labs' ) {
	require_once __DIR__ . '/mobile-labs.php';
}
