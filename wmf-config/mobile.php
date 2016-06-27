<?php

# WARNING: This file is publically viewable on the web.
# # Do not put private data here.

if ( $wmgMobileFrontend ) {
	require_once( "$IP/extensions/MobileFrontend/MobileFrontend.php" );
	$wgMFMobileHeader = 'X-Subdomain';
	$wgMFNoindexPages = false;
	$wgMFNearby = $wmgMFNearby && $wmgEnableGeoData;
	$wgMFPhotoUploadEndpoint = $wmgMFPhotoUploadEndpoint;
	$wgMFUseCentralAuthToken = $wmgMFUseCentralAuthToken;
	$wgMFPhotoUploadWiki = $wmgMFPhotoUploadWiki;
	$wgMFContentNamespace = $wmgMFContentNamespace;
	$wgMFPhotoUploadAppendToDesc = $wmgMFPhotoUploadAppendToDesc;
	$wgMFUseWikibaseDescription = $wmgMFUseWikibaseDescription;
	$wgMFMobileFormatterHeadings = $wmgMFMobileFormatterHeadings;

	if ( $wmgMobileFrontendLogo ) {
		$wgMobileFrontendLogo = $wmgMobileFrontendLogo;
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

	$wgMFAutodetectMobileView = $wmgMFAutodetectMobileView;

	if ( $wmgZeroBanner && !$wmgZeroPortal ) {
		require_once( "$IP/extensions/JsonConfig/JsonConfig.php" );
		require_once( "$IP/extensions/ZeroBanner/ZeroBanner.php" );

		$wgJsonConfigs['JsonZeroConfig']['remote'] = array(
			'url' => 'https://zero.wikimedia.org/w/api.php',
			'username' => $wmgZeroPortalApiUserName,
			'password' => $wmgZeroPortalApiPassword,
		);

		// @TODO: which group(s) on all wikies should Zero allow to flush cache?
		$wgGroupPermissions['sysop']['jsonconfig-flush'] = true;
		// $wgZeroBannerFont = '/usr/share/fonts/truetype/ttf-dejavu/DejaVuSans.ttf';
		// $wgZeroBannerFontSize = '10';
	}

	// Enable loading of desktop-specific resources from MobileFrontend
	if ( $wmgMFEnableDesktopResources ) {
		$wgMFEnableDesktopResources = true;
	}

	// Enable appending of TM (text) / (R) (icon) on site name in footer.
	$wgMFTrademarkSitename = $wmgMFTrademarkSitename;

	// Enable X-Analytics logging
	$wgMFEnableXAnalyticsLogging = $wmgMFEnableXAnalyticsLogging;

	// Blacklist some pages
	$wgMFNoMobileCategory = $wmgMFNoMobileCategory;
	$wgMFNoMobilePages = $wmgMFNoMobilePages;

	$wgHooks['EnterMobileMode'][] = function() {
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

	$wgMFEnableSiteNotice = $wmgMFEnableSiteNotice;
	$wgMFCollapseSectionsByDefault = $wmgMFCollapseSectionsByDefault;
	$wgMFTidyMobileViewSections = false; // experimental

	// Link to help Google spider associate pages on wiki with our Android app.
	// They originally special-cased us but would like it done the normal way now. :)
	$wgMFAppPackageId = $wmgMFAppPackageId;
	$wgMFNearbyRange = $wmgMaxGeoSearchRadius;

	// restrict access to mobile Uploads to users with minimum editcount T64598
	$wgMFUploadMinEdits = $wmgMFUploadMinEdits;

	// Disable mobile uploads per T64598
	$wgGroupPermissions['autoconfirmed']['mf-uploadbutton'] = false;
	$wgGroupPermissions['sysop']['mf-uploadbutton'] = false;

	$wgMFEnableBeta = true;

	// enable editing for unregistered users
	$wgMFEditorOptions = $wmgMFEditorOptions;

	$wgMFQueryPropModules = $wmgMFQueryPropModules;
	$wgMFSearchAPIParams = $wmgMFSearchAPIParams;
	$wgMFSearchGenerator = $wmgMFSearchGenerator;

	// Turn on volunteer recruitment
	$wgMFEnableJSConsoleRecruitment = true;

	// Brute-force bandwidth optimization by stripping srcset (T119797)
	$wgMFStripResponsiveImages = true;
}

if ( $wmfRealm === 'labs' ) {
	require_once( __DIR__ . '/mobile-labs.php' );
}
