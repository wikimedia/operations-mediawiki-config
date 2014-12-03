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
	$wgMFEnableWikiGrok = $wmgMFEnableWikiGrok;
	$wgMFPhotoUploadWiki = $wmgMFPhotoUploadWiki;
	$wgMFContentNamespace = $wmgMFContentNamespace;
	$wgMFPhotoUploadAppendToDesc = $wmgMFPhotoUploadAppendToDesc;
	$wgMFUseWikibaseDescription = $wmgMFUseWikibaseDescription;

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

	// Hack to work around https://bugzilla.wikimedia.org/show_bug.cgi?id=35215
	$wgHooks['EnterMobileMode'][] = function() {
		global $wgCentralHost, $wgCentralPagePath, $wgCentralBannerDispatcher, $wgCentralBannerRecorder, $wgCentralAuthCookieDomain;

		$wgCentralHost = str_replace( 'meta.wikimedia.org', 'meta.m.wikimedia.org', $wgCentralHost );
		$wgCentralPagePath = str_replace( 'meta.wikimedia.org', 'meta.m.wikimedia.org', $wgCentralPagePath );
		$wgCentralBannerDispatcher = str_replace( 'meta.wikimedia.org', 'meta.m.wikimedia.org', $wgCentralBannerDispatcher );
		$wgCentralBannerRecorder = str_replace( 'meta.wikimedia.org', 'meta.m.wikimedia.org', $wgCentralBannerRecorder );

		// Hack for bug https://bugzilla.wikimedia.org/show_bug.cgi?id=47647
		if ( $wgCentralAuthCookieDomain == 'commons.wikimedia.org' ) {
			$wgCentralAuthCookieDomain = 'commons.m.wikimedia.org';
		} elseif ( $wgCentralAuthCookieDomain == 'meta.wikimedia.org' ) {
			$wgCentralAuthCookieDomain = 'meta.m.wikimedia.org';
		}

		return true;
	};

	$wgMFEnableSiteNotice = $wmgMFEnableSiteNotice;
	$wgMFCollapseSectionsByDefault = $wmgMFCollapseSectionsByDefault;
	$wgMFTidyMobileViewSections = false; // experimental

	// Link to help Google spider associate pages on wiki with our Android app.
	// They originally special-cased us but would like it done the normal way now. :)
	$wgMFAppPackageId = $wmgMFAppPackageId;
	$wgMFNearbyRange = $wmgMaxGeoSearchRadius;

	$wgMFPageActions = array_diff( $wgMFPageActions, $wmgMFRemovePageActions );

	// restrict access to mobile Uploads to users with minimum editcount https://bugzilla.wikimedia.org/show_bug.cgi?id=62598
	$wgMFUploadMinEdits = $wmgMFUploadMinEdits;

	// Disable mobile uploads per https://bugzilla.wikimedia.org/62598
	$wgGroupPermissions['autoconfirmed']['mf-uploadbutton'] = false;
	$wgGroupPermissions['sysop']['mf-uploadbutton'] = false;

	$wgMFEnableBeta = true;

	$wgMFUseWikibaseDescription = true; // Alpha experiment

	if ( $wmgUseWikiGrok ) {
		require_once( "$IP/extensions/WikiGrok/WikiGrok.php" );
		
		$wgWikiGrokSlowCampaigns = array(
			// https://www.mediawiki.org/wiki/Extension:MobileFrontend/WikiGrok/Claim_suggestions#Writer
			'author' => array(
				'type' => 'Simple',
				'property' => 'P106'/* occupation */,
				'ifAll' => array( 'P31'/* instance of */ => 'Q5'/* human */, 'P106'/* occupation */ => 'Q36180'/* writer */ ),
				'ifNotAny' => array( 'P106'/* occupation */ => 'Q482980'/* author */ ),
				'suggestions' => array( 'Q482980'/* author */ ),
			),
			// https://www.mediawiki.org/wiki/Extension:MobileFrontend/WikiGrok/Claim_suggestions#Types_of_actors
			// After production testing is complete, split this campaign into 2 separate
			// campaigns which exclude items that already have the respective claims made.
			'actor' => array(
				'type' => 'Simple',
				'property' => 'P106'/* occupation */,
				'ifAll' => array( 'P31'/* instance of */ => 'Q5'/* human */, 'P106'/* occupation */ => 'Q33999'/* actor */ ),
				'suggestions' => array( 'Q10798782'/* television actor */, 'Q10800557'/* film actor */ ),
			),
			// https://www.mediawiki.org/wiki/Extension:MobileFrontend/WikiGrok/Claim_suggestions#Album
			'album' => array(
				'type' => 'Simple',
				'property' => 'P31'/* instance of */,
				'ifAll' => array( 'P31'/* instance of */ => 'Q482994'/* album */ ),
				'suggestions' => array( 'Q208569'/* studio album */, 'Q209939'/* live album */ ),
			),
		);

		// These are deprecated. Remove after change I3ec0190a is fully deployed.
		$wgMFWikiGrokAbTestStartDate = 1416272400; // Tue, 18 Nov 2014 01:00:00 GMT
		$wgMFWikiGrokAbTestEndDate = 1417482000; // Tue, 02 Dec 2014 01:00:00 GMT
	}
}
