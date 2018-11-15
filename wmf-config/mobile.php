<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

if ( $wmgMobileFrontend ) {
	wfLoadExtension( 'MobileFrontend' );

	// Load skin
	if ( $wmgMinervaNeue ) {
		wfLoadSkin( 'MinervaNeue' );
	}

	$wgMFMobileHeader = 'X-Subdomain';
	// https://phabricator.wikimedia.org/T206497
	// This results in <link rel="alternate"> tags being inserted onto the
	// desktop version of these wikis, pointing to the mobile version that lives
	// on the .m. URL.  This setting is intended to live only long enough to
	// evaluate the effectiveness of the setting, and then it will become either
	// true or false globally.
	$wgMFNoindexPages = in_array( $wgDBName, [ 'itwiki', 'nlwiki', 'kowiki',
		'arwiki', 'zhwiki', 'hiwiki' ] );
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

if ( $wmfRealm === 'labs' ) {
	require_once __DIR__ . '/mobile-labs.php';
}
