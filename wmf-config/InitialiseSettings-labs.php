<?php
# WARNING: This file is publically viewable on the web.
#          Do not put private data here.

/**
 * This file is for overriding the default InitialiseSettings.php with our own
 * stuff. Prefixing a setting key with '-' to override all values from
 * InitialiseSettings.php
 *
 * Please wrap your code in functions to avoid tainting the global namespace.
 */

/**
 * Main entry point to override production settings. Supports key beginning with
 * a dash to completely override a setting.
 * Settings are fetched through wmfLabsSettings() defined below.
 */
function wmfLabsOverrideSettings() {
	global $wmfConfigDir, $wgConf;

	// Override (or add) settings that we need within the labs environment,
	// but not in production.
	$betaSettings = wmfLabsSettings();

	// Set configuration string placeholder 'variant' to 'beta'.
	// Add a wikitag of 'beta' that can be used to merge beta specific and
	// default settings by using `'+beta' => array(...),`
	$wgConf->siteParamsCallback = function( $conf, $wiki ) {
		return array(
			'params' => array( 'variant' => 'beta' ),
			'tags' => array( 'beta' ),
		);
	};

	foreach ( $betaSettings as $key => $value ) {
		if ( substr( $key, 0, 1 ) == '-' ) {
			// Settings prefixed with - are completely overriden
			$wgConf->settings[substr( $key, 1 )] = $value;
		} elseif ( isset( $wgConf->settings[$key] ) ) {
			$wgConf->settings[$key] = array_merge( $wgConf->settings[$key], $value );
		} else {
			$wgConf->settings[$key] = $value;
		}
	}
}

/**
 * Return settings for wmflabs cluster. This is used by wmfLabsOverride().
 * Keys that start with a hyphen will completely override the regular settings
 * in InitializeSettings.php. Keys that don't start with a hyphen will have
 * their settings combined with the regular settings.
 */
function wmfLabsSettings() {
	global $wmfUdp2logDest;
	return array(
		'wgParserCacheType' => array(
			'default' => CACHE_MEMCACHED,
		),

		'wgSitename' => array(
			'deploymentwiki' => 'Deployment',
			'wikivoyage'    => 'Wikivoyage',
		),

		'-wgServer' => array(
			'wiktionary'	=> 'http://$lang.wiktionary.$variant.wmflabs.org',
			'wikipedia'     => 'http://$lang.wikipedia.$variant.wmflabs.org',
			'wikiversity'	=> 'http://$lang.wikiversity.$variant.wmflabs.org',
			'wikisource'	=> 'http://$lang.wikisource.$variant.wmflabs.org',
			'wikiquote'	=> 'http://$lang.wikiquote.$variant.wmflabs.org',
			'wikinews'	=> 'http://$lang.wikinews.$variant.wmflabs.org',
			'wikibooks'     => 'http://$lang.wikibooks.$variant.wmflabs.org',
			'wikivoyage'    => 'http://$lang.wikivoyage.$variant.wmflabs.org',

			'commonswiki'   => 'http://commons.wikimedia.$variant.wmflabs.org',
			'deploymentwiki'      => 'http://deployment.wikimedia.$variant.wmflabs.org',
			'loginwiki'     => 'http://login.wikimedia.$variant.wmflabs.org',
			'metawiki'      => 'http://meta.wikimedia.$variant.wmflabs.org',
			'testwiki'      => 'http://test.wikimedia.$variant.wmflabs.org',
			'zerowiki'      => 'http://zero.wikimedia.$variant.wmflabs.org',
			'wikidatawiki'  => 'http://wikidata.$variant.wmflabs.org',
			'en_rtlwiki' => 'http://en-rtl.wikipedia.$variant.wmflabs.org',
		),

		'-wgCanonicalServer' => array(
			'wikipedia'     => 'http://$lang.wikipedia.$variant.wmflabs.org',
			'wikibooks'     => 'http://$lang.wikibooks.$variant.wmflabs.org',
			'wikiquote'	=> 'http://$lang.wikiquote.$variant.wmflabs.org',
			'wikinews'	=> 'http://$lang.wikinews.$variant.wmflabs.org',
			'wikisource'	=> 'http://$lang.wikisource.$variant.wmflabs.org',
			'wikiversity'     => 'http://$lang.wikiversity.$variant.wmflabs.org',
			'wiktionary'     => 'http://$lang.wiktionary.$variant.wmflabs.org',
			'wikivoyage'    => 'http://$lang.wikivoyage.$variant.wmflabs.org',

			'metawiki'      => 'http://meta.wikimedia.$variant.wmflabs.org',
			'commonswiki'	=> 'http://commons.wikimedia.$variant.wmflabs.org',
			'deploymentwiki'      => 'http://deployment.wikimedia.$variant.wmflabs.org',
			'loginwiki'     => 'http://login.wikimedia.$variant.wmflabs.org',
			'testwiki'      => 'http://test.wikimedia.$variant.wmflabs.org',
			'zerowiki'      => 'http://zero.wikimedia.$variant.wmflabs.org',
			'wikidatawiki'  => 'http://wikidata.$variant.wmflabs.org',
			'en_rtlwiki' => 'http://en-rtl.wikipedia.$variant.wmflabs.org',
		),

		'wmgUsabilityPrefSwitch' => array(
			'default' => ''
		),

		'-wgUploadDirectory' => array(
			'default'      => '/data/project/upload7/$site/$lang',
			'private'      => '/data/project/upload7/private/$lang',
		),

		/* 'wmgUseOnlineStatusBar' => array( */
		/* 	'default' => false, */
		/* ), */

		'-wgUploadPath' => array(
			'default' => 'http://upload.$variant.wmflabs.org/$site/$lang',
			'private' => '/w/img_auth.php',
		//	'wikimania2005wiki' => 'http://upload..org/wikipedia/wikimania', // back compat
			'commonswiki' => 'http://upload.$variant.wmflabs.org/wikipedia/commons',
			'metawiki' => 'http://upload.$variant.wmflabs.org/wikipedia/meta',
			'testwiki' => 'http://upload.$variant.wmflabs.org/wikipedia/test',
		),

		'-wgThumbnailBuckets' => array(
			'default' => array( 256, 512, 1024, 2048, 4096 ),
		),

		'-wgThumbnailMinimumBucketDistance' => array(
			'default' => 32,
		),

		'-wmgMathPath' => array(
			'default' => 'http://upload.$variant.wmflabs.org/math',
		),

		'wmgNoticeProject' => array(
			'deploymentwiki' => 'meta',
		),

		'-wgDebugLogFile' => array(
			'default' => "udp://{$wmfUdp2logDest}/wfDebug",
		),

		'-wmgDefaultMonologHandler' => array(
			'default' => 'wgDebugLogFile',
		),

		// Additional log channels for beta cluster
		'wmgMonologChannels' => array(
			'+beta' => array(
				'CentralAuthVerbose' => 'debug',
				'dnsblacklist' => 'debug',
				'EventBus' => 'debug',
				'squid' => 'debug',
			),
		),

		'wmgApplyGlobalBlocks' => array( // T123936
			'default' => true,
			'metawiki' => true,
			'deploymentwiki' => false
		),

		//'-wgDebugLogGroups' => array(),
		'-wgJobLogFile' => array(),

		// No IRC feed T128006
		'-wmgUseRC2UDP' => array(
			'default' => false,
		),
		// T62013, T58758
		'-wmgRC2UDPPrefix' => array(
			'default' => false,
		),

		'wmgUseWebFonts' => array(
			'mywiki' => true,
		),

		'-wgLogo' => array(
			'default' => '/static/images/project-logos/betawiki.png',
			'commonswiki' => '/static/images/project-logos/betacommons.png',
			'wikibooks' => '/static/images/project-logos/betacommons.png',
			'wikidata' => '/static/images/project-logos/betacommons.png',
			'wikidata' => '/static/images/project-logos/betawikidata.png',
			'wikinews' => '/static/images/project-logos/betacommons.png',
			'wikiquote' => '/static/images/project-logos/betacommons.png',
			'wikisource' => '/static/images/project-logos/betacommons.png',
			'wikiversity' => '/static/images/project-logos/betawikiversity.png',
			'wikivoyage' => '/static/images/project-logos/betacommons.png',
			'wiktionary' => '/static/images/project-logos/betacommons.png',
		),

		'wgFavicon' => array(
			'dewiki' => 'http://upload.wikimedia.org/wikipedia/commons/1/14/Favicon-beta-wikipedia.png',
		),

		'wgVectorResponsive' => array(
			'default' => true,
		),

		// Editor Engagement stuff
		'-wmfUseArticleCreationWorkflow' => array(
			'default' => false,
		),
		'wmgUseEcho' => array(
			'enwiki' => true,
			'en_rtlwiki' => true,
		),

		'-wmgUsePoolCounter' => array(
			'default' => false, // T38891
		),
		'-wmgEnableCaptcha' => array(
			'default' => true,
		),
		'-wmgEchoCluster' => array(
			'default' => false,
		),
		'-wmgEchoUseCrossWikiBetaFeature' => array(
			'default' => false,
		),
		'-wmgEchoShowFooterNotice' => array(
			'default' => true
		),
		'-wmgEchoFooterNoticeURL' => array(
			'default' => 'http://example.org',
		),
		# FIXME: make that settings to be applied
		'-wgShowExceptionDetails' => array(
			'default' => true,
		),
		'-wgUseContributionTracking' => array(
			'default' => false,
		),
		'-wmgUseContributionReporting' => array(
			'default' => false,
		),

		# To help fight spam, makes rules maintained on deploymentwiki
		# to be available on all beta wikis.
		'-wmgAbuseFilterCentralDB' => array(
			'default' => 'deploymentwiki',
		),
		'-wmgUseGlobalAbuseFilters' => array(
			'default' => true,
		),

		'wgRCWatchCategoryMembership' => array(
			'default' => true,
		),

		// T39852
		'wmgUseWikimediaShopLink' => array(
			'default'    => false,
			'enwiki'     => true,
			'simplewiki' => true,
		),

		'wmgUseKartographer' => array(
			'default'	=> true,
		),

		//enable TimedMediaHandler and MwEmbedSupport for testing on commons and enwiki
		'wmgUseMwEmbedSupport' => array(
			'commonswiki'	=> true,
			'enwiki'	=> true,
		),
		// NOTE: TMH *requires* MwEmbedSupport to function
		'wmgUseTimedMediaHandler' => array(
			'commonswiki'	=> true,
			'enwiki'	=> true,
		),
		'wmgMobileUrlTemplate' => array(
			'default' => '%h0.m.%h1.%h2.%h3.%h4',
			'wikidatawiki' => 'm.%h0.%h1.%h2.%h3', // T87440
		),

		'wmgMFMobileFormatterHeadings' => array(
			'default' => array( 'h2', 'h3', 'h4', 'h5', 'h6' ), // T110436, T110837
		),

		'wgMFRemovableClasses' => array(
			'default' => array(
				'base' => array( '.navbox' ),
				'beta' => array( '.navbox' ),
				'HTML' => array(),
			),
		),

		'wgMFAllowNonJavaScriptEditing' => array(
			'default' => true,
		),

		'wmgMFPhotoUploadEndpoint' => array(
			'default' => 'http://commons.wikimedia.$variant.wmflabs.org/w/api.php',
		),
		'wmgMFUseCentralAuthToken' => array(
			'default' => true,
		),
		'wmgMFEnableBetaDiff' => array(
			'default' => true,
		),
		'wmgMFSpecialCaseMainPage' => array(
			'default' => true,
			'enwiki' => false,
		),
		'wgMFExperiments' => array(
			'default' => array(
			),
		),
		'wgMFSchemaMobileWebLanguageSwitcherSampleRate' => array(
			'default' => array(
				'beta' => 1,
				'stable' => 1
			)
		),

		// T97704
		'wmgGatherAutohideFlagLimit' => array(
			'default' => 3,
		),

		'wmgEnableGeoData' => array(
			'wikidatawiki' => true,
		),

		'wmgGeoDataDebug' => array(
			'default' => true,
		),

		'wmgULSPosition' => array(
			# Beta-specific
			'deploymentwiki' => 'personal',
		),

		// (T41653) The plan is to enable it for testing on labs first, so add
		// the config hook to be able to do that.
		'wmgUseCodeEditorForCore' => array(
			'default' => true,
		),

		'wmgUseCommonsMetadata' => array(
			'default' => true,
		),
		'wmgCommonsMetadataForceRecalculate' => array(
			'default' => true,
		),

		'wmgUseGWToolset' => array(
			'default' => false,
			'commonswiki' => true,
		),

		// Don't use an http/https proxy
		'-wgCopyUploadProxy' => array(
			'default' => false,
		),

		// ----------- BetaFeatures start ----------
		'wgUseBetaFeatures' => array(
			'default' => true,
		),

		// Enable all Beta Features in Beta Labs, even if not in production whitelist
		'wgBetaFeaturesWhitelist' => array(
			'default' => false,
		),

		'wmgUseMultimediaViewer' => array(
			'default' => true,
		),

		'wmgMediaViewerNetworkPerformanceSamplingFactor' => array(
			'default' => 1,
		),

		'wmgUseImageMetrics' => array(
			'default' => true,
		),

		'wmgImageMetricsSamplingFactor' => array(
			'default' => 1,
		),

		'wmgImageMetricsCorsSamplingFactor' => array(
			'default' => 1,
		),

		'wmgUseRestbaseVRS' => array(
			'default' => true,
		),
		'wmgVisualEditorUseSingleEditTab' => array(
			'enwiki' => true,
		),
		'wmgVisualEditorAccessRESTbaseDirectly' => array(
			'default' => true,
		),
		'wmgVisualEditorTransitionDefault' => array(
			'default' => false,
		),
		// ------------ BetaFeatures end -----------

		'wmgUseRSSExtension' => array(
			'dewiki' => true,
		),

		'wmgRSSUrlWhitelist' => array(
			'dewiki' => array( 'http://de.planet.wikimedia.org/atom.xml' ),
		),

		'wmgUseCampaigns' => array(
			'default' => true,
		),

		'wmgUseEventLogging' => array(
			'default' => true,
		),

		'wmgContentTranslationCluster' => array(
			'default' => false,
		),

		'wmgContentTranslationCampaigns' => array(
			'default' => array( 'newarticle' ),
		),

		'wmgContentTranslationEnableSuggestions' => array(
			'default' => true,
		),

		'wmgUseNavigationTiming' => array(
			'default' => true,
		),

		'wgSecureLogin' => array(
			// Setting false throughout Labs for now due to untrusted SSL certificate
			// T50501
			'default' => false,
			'loginwiki' => false,
		),

		'wgSearchSuggestCacheExpiry' => array(
			'default' => 300,
		),

		'wmgUseFlow' => array(
			'enwiki' => true,
			'en_rtlwiki' => true,
		),
		# No separate Flow DB or cluster (yet) for labs.
		'-wmgFlowDefaultWikiDb' => array(
			'default' => false,
		),
		'-wmgFlowCluster' => array(
			'default' => false,
		),
		'wmgFlowEnableOptInBetaFeature' => array(
			'enwiki' => true,
		),
		'wmgUseGather' => array(
			'default' => true,
		),
		'wmgUseWPB' => array(
			'default' => false,
			'enwiki' => true,
		),
		'wgWPBBannerProperty' => array(
			'default' => 'P751',
		),
		'wmgUseGuidedTour' => array(
			'wikidatawiki' => true,
		),
		'wmgUseRelatedArticles' => array(
			'default' => true,
		),
		'wmgRelatedArticlesShowInFooter' => array(
			'default' => true,
		),
		// Enable anonymous editor acquisition experiment across labs
		'wmgGettingStartedRunTest' => array(
			'default' => true,
		),
		'+wmgExtraLanguageNames' => array(
			'default' => array(),
			'en_rtlwiki' => array( 'en-rtl' => 'English (rtl)' ),
		),
		'wmgUseContentTranslation' => array(
			'default' => false,
			'wikipedia' => true,
		),

		'wmgUsePetition' => array(
			'default' => false,
			'metawiki' => true,
		),

		'wmgUseQuickSurveys' => array(
			'default' => true,
		),

		'wmgUseSentry' => array(
			'default' => true,
		),
		'wmgSentryDsn' => array(
			'default' => 'http://c357be0613e24340a96aeaa28dde08ad@sentry-beta.wmflabs.org/4',
		),

		'wmgUseEventBus' => array(
			'default' => true,
		),

		// Thumbnail chaining

		'wgThumbnailBuckets' => array(
			'default' => array( 2880 ),
		),

		'wgThumbnailMinimumBucketDistance' => array(
			'default' => 100,
		),

		// Thumbnail prerendering at upload time
		'wgUploadThumbnailRenderMap' => array(
			'default' => array( 320, 640, 800, 1024, 1280, 1920, 2560, 2880 ),
		),

		'wgUploadThumbnailRenderMethod' => array(
			'default' => 'http',
		),

		'wgUploadThumbnailRenderHttpCustomHost' => array(
			'default' => 'upload.beta.wmflabs.org',
		),

		'wgUploadThumbnailRenderHttpCustomDomain' => array(
			'default' => 'deployment-cache-upload04.eqiad.wmflabs',
		),

		'wmgUseApiFeatureUsage' => array(
			'default' => true,
		),

		'wmgUseBounceHandler' => array(
			'default' => true,
		),

		'-wmgScorePath' => array(
			'default' => "//upload.beta.wmflabs.org/score",
		),

		'wgRateLimitsExcludedIPs' => array(
			'default' => array(
				'198.73.209.0/24', // T87841 Office IP
				'162.222.72.0/21', // T126585 Sauce Labs IP range for browser tests
				'66.85.48.0/21', // also Sauce Labs
			),
		),

		'wmgUseCapiunto' => array(
			'default' => true,
		),

		'wmgUsePopups' => array(
			'default' => true,
		),

		'wgEnablePopupsMobile' => array(
			'default' => true,
		),

		'wmgUseCheckUser' => array(
			'default' => false,
		),

		'wmgLogAuthmanagerMetrics' => array(
			'default' => true,
		),

		'wmgMediaViewerUseThumbnailGuessing' => array(
			'default' => false, # T69651
		),

		'wmgUseUploadsLink' => array(
			'default' => false,
			'commonswiki' => true,
		),

		'wmgUseUrlShortener' => array(
			'default' => true,
			'private' => false,
		),

		'wmgUseArticlePlaceholder' => array(
			'default' => false,
			'wikidataclient' => true,
		),
		'wmgUseORES' => array(
			'default' => false,
			'wikipedia' => true, // T127661
		),
		'wgOresModels' => array(
			'default' => array(
				'damaging' => true,
				'reverted' => true,
				'goodfaith' => true,
				'wp10' => false,
			),
			'wikipedia' => array(
				'damaging' => true,
				'reverted' => false,
				'goodfaith' => false,
				'wp10' => false,
			), // T127661
		),
		'wgOresDamagingThresholds' => array(
			'default' => array( 'hard' => 0.7, 'soft'=> 0.5 ),
			'wikipedia' => array( 'hard' => 0.8, 'soft'=> 0.7 ), // T127661
		),
		// test completion suggester default everywhere in preperation
		// for rollout to production
		'wmgCirrusSearchUseCompletionSuggester' => array(
			'default' => 'yes',
			'wikidatawiki' => 'no',
		),
		'+wmgWikibaseEnableArbitraryAccess' => array(
			'commonswiki' => true,
		),
		'wmgWikibaseAllowDataAccessInUserLanguage' => array(
			'default' => false,
			'wikidatawiki' => true,
			'commonswiki' => true,
		),
		// Test the extension Collection in other languages for book creator,
		// which avoids the bugs related to the PDF generator.
		'wmgUseCollection' => array(
			'zhwiki' => true, // T128425
		),
		'wgMaxUploadSize' => array(
		// Affects URL uploads and chunked uploads (experimental).
		// Limit on other web uploads is enforced by PHP.
			'default' => 1024 * 1024 * 4096, // 4 GB (i.e. equals 2^31 - 1)
			'ptwiki' => 1024 * 500, // 500 KB - T25186
		),
		'wmgUseNewsletter' => array(
			'default' => true,  // T127297
		),
		// Test enabling OATH for 2FA
		'wmgUseOATHAuth' => array(
			'default' => true,
		),
	);
} # wmflLabsSettings()
