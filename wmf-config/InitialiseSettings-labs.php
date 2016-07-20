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
		return [
			'params' => [ 'variant' => 'beta' ],
			'tags' => [ 'beta' ],
		];
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

	return [

		'wgParserCacheType' => [
			'default' => CACHE_MEMCACHED,
		],

		'wgSitename' => [
			'deploymentwiki' => 'Deployment',
			'wikivoyage'     => 'Wikivoyage',
		],

		'-wgServer' => [
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
		],

		'-wgCanonicalServer' => [
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
		],

		'wmgUsabilityPrefSwitch' => [
			'default' => ''
		],

		'-wgUploadPath' => [
			'default' => 'http://upload.$variant.wmflabs.org/$site/$lang',
			'private' => '/w/img_auth.php',
		//	'wikimania2005wiki' => 'http://upload..org/wikipedia/wikimania', // back compat
			'commonswiki' => 'http://upload.$variant.wmflabs.org/wikipedia/commons',
			'metawiki' => 'http://upload.$variant.wmflabs.org/wikipedia/meta',
			'testwiki' => 'http://upload.$variant.wmflabs.org/wikipedia/test',
		],

		'-wgThumbnailBuckets' => [
			'default' => [ 256, 512, 1024, 2048, 4096 ],
		],

		'-wgThumbnailMinimumBucketDistance' => [
			'default' => 32,
		],

		'-wmgMathPath' => [
			'default' => 'http://upload.$variant.wmflabs.org/math',
		],

		'wmgNoticeProject' => [
			'deploymentwiki' => 'meta',
		],

		'-wgDebugLogFile' => [
			'default' => "udp://{$wmfUdp2logDest}/wfDebug",
		],

		'-wmgDefaultMonologHandler' => [
			'default' => 'wgDebugLogFile',
		],

		// Additional log channels for beta cluster
		'wmgMonologChannels' => [
			'+beta' => [
				'CentralAuthVerbose' => 'debug',
				'dnsblacklist' => 'debug',
				'EventBus' => 'debug',
				'squid' => 'debug',
			],
		],

		'wmgApplyGlobalBlocks' => [ // T123936
			'default' => true,
			'metawiki' => true,
			'deploymentwiki' => false
		],

		'-wgJobLogFile' => [],

		'wmgUseWebFonts' => [
			'mywiki' => true,
		],

		'-wgLogo' => [
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
		],

		'wgFavicon' => [
			'dewiki' => 'http://upload.wikimedia.org/wikipedia/commons/1/14/Favicon-beta-wikipedia.png',
		],

		'wgVectorResponsive' => [
			'default' => true,
		],
		'-wmgUsePoolCounter' => [
			'default' => false, // T38891
		],

		'-wmgEnableCaptcha' => [
			'default' => true,
		],

		'wmgUseEcho' => [
			'enwiki' => true,
			'en_rtlwiki' => true,
		],
		'-wmgEchoCluster' => [
			'default' => false,
		],

		# FIXME: make that settings to be applied
		'-wgShowExceptionDetails' => [
			'default' => true,
		],

		'-wgUseContributionTracking' => [
			'default' => false,
		],

		# To help fight spam, makes rules maintained on deploymentwiki
		# to be available on all beta wikis.
		'-wmgAbuseFilterCentralDB' => [
			'default' => 'deploymentwiki',
		],
		'-wmgUseGlobalAbuseFilters' => [
			'default' => true,
		],

		// T39852
		'wmgUseWikimediaShopLink' => [
			'default'    => false,
			'enwiki'     => true,
			'simplewiki' => true,
		],

		'wmgUseKartographer' => [
			'default'	=> true,
		],

		// Enable TimedMediaHandler and MwEmbedSupport for testing on commons and enwiki
		// TimedMediaHandler requires MwEmbedSupport.
		'wmgUseMwEmbedSupport' => [
			'commonswiki'	=> true,
			'enwiki'	=> true,
		],
		'wmgUseTimedMediaHandler' => [
			'commonswiki'	=> true,
			'enwiki'	=> true,
		],

		'wmgMobileUrlTemplate' => [
			'default' => '%h0.m.%h1.%h2.%h3.%h4',
			'wikidatawiki' => 'm.%h0.%h1.%h2.%h3', // T87440
		],
		'wmgMFMobileFormatterHeadings' => [
			'default' => [ 'h2', 'h3', 'h4', 'h5', 'h6' ], // T110436, T110837
		],

		'wgMFRemovableClasses' => [
			'default' => [
				'base' => [ '.navbox' ],
				'beta' => [ '.navbox' ],
				'HTML' => [],
			],
		],
		'wgMFAllowNonJavaScriptEditing' => [
			'default' => true,
		],
		'wmgMFPhotoUploadEndpoint' => [
			'default' => 'http://commons.wikimedia.$variant.wmflabs.org/w/api.php',
		],
		'wmgMFSpecialCaseMainPage' => [
			'default' => true,
			'enwiki' => false,
		],
		'wgMFExperiments' => [
			'default' => [
			],
		],
		'wgMFSchemaMobileWebLanguageSwitcherSampleRate' => [
			'default' => [
				'beta' => 1,
				'stable' => 1
			]
		],

		'wmgULSPosition' => [
			# Beta-specific
			'deploymentwiki' => 'personal',
		],

		'wmgUseCommonsMetadata' => [
			'default' => true,
		],
		'wmgCommonsMetadataForceRecalculate' => [
			'default' => true,
		],

		'wmgUseGWToolset' => [
			'default' => false,
			'commonswiki' => true,
		],

		// Don't use an http/https proxy
		'-wgCopyUploadProxy' => [
			'default' => false,
		],

		///
		/// ----------- BetaFeatures start ----------
		///

		'wgUseBetaFeatures' => [
			'default' => true,
		],

		// Enable all Beta Features in Beta Labs, even if not in production whitelist
		'wgBetaFeaturesWhitelist' => [
			'default' => false,
		],

		'wmgUseMultimediaViewer' => [
			'default' => true,
		],
		'wmgMediaViewerNetworkPerformanceSamplingFactor' => [
			'default' => 1,
		],

		'wmgUseImageMetrics' => [
			'default' => true,
		],

		'wmgImageMetricsSamplingFactor' => [
			'default' => 1,
		],
		'wmgImageMetricsCorsSamplingFactor' => [
			'default' => 1,
		],

		'wmgUseRestbaseVRS' => [
			'default' => true,
		],

		'wmgVisualEditorUseSingleEditTab' => [
			'enwiki' => true,
		],
		'wmgVisualEditorAccessRESTbaseDirectly' => [
			'default' => true,
		],
		'wmgVisualEditorTransitionDefault' => [
			'default' => false,
		],

		///
		/// ------------ BetaFeatures end -----------
		///

		'wmgUseRSSExtension' => [
			'dewiki' => true,
		],
		'wmgRSSUrlWhitelist' => [
			'dewiki' => [ 'http://de.planet.wikimedia.org/atom.xml' ],
		],

		'wmgUseCampaigns' => [
			'default' => true,
		],

		'wmgContentTranslationCluster' => [
			'default' => false,
		],
		'wmgContentTranslationCampaigns' => [
			'default' => [ 'newarticle' ],
		],
		'wmgContentTranslationEnableSuggestions' => [
			'default' => true,
		],

		// Whether Compact Links is Beta feature
		'wmgULSCompactLanguageLinksBetaFeature' => [
			'default' => false,
		],

		// Whether Compact Links is enabled for new accounts *by default*
		'wmgULSCompactLinksForNewAccounts' => [
			'default' => true,
		],

		// Whether Compact Links is enabled for anonymous users *by default*
		'wmgULSCompactLinksEnableAnon' => [
			'default' => true,
		],

		'wmgUseNavigationTiming' => [
			'default' => true,
		],

		'wgSecureLogin' => [
			// Setting false throughout Labs for now due to untrusted SSL certificate
			// T50501
			'default' => false,
			'loginwiki' => false,
		],

		'wgSearchSuggestCacheExpiry' => [
			'default' => 300,
		],

		'wmgUseFlow' => [
			'enwiki' => true,
			'en_rtlwiki' => true,
		],
		# No separate Flow DB or cluster (yet) for labs.
		'-wmgFlowDefaultWikiDb' => [
			'default' => false,
		],
		'-wmgFlowCluster' => [
			'default' => false,
		],
		'wmgFlowEnableOptInBetaFeature' => [
			'enwiki' => true,
		],

		'wmgUseWPB' => [
			'default' => false,
			'enwiki' => true,
		],
		'wgWPBBannerProperty' => [
			'default' => 'P751',
		],

		'wmgUseGuidedTour' => [
			'wikidatawiki' => true,
		],

		'wmgUseRelatedArticles' => [
			'default' => true,
		],
		'wmgRelatedArticlesShowInFooter' => [
			'default' => true,
		],

		// Enable anonymous editor acquisition experiment across labs
		'wmgGettingStartedRunTest' => [
			'default' => true,
		],

		'+wmgExtraLanguageNames' => [
			'default' => [],
			'en_rtlwiki' => [ 'en-rtl' => 'English (rtl)' ],
		],

		'wmgUsePetition' => [
			'default' => false,
			'metawiki' => true,
		],

		'wmgUseQuickSurveys' => [
			'default' => true,
		],

		'wmgUseSentry' => [
			'default' => true,
		],
		'wmgSentryDsn' => [
			'default' => 'http://c357be0613e24340a96aeaa28dde08ad@sentry-beta.wmflabs.org/4',
		],

		// Thumbnail chaining
		'wgThumbnailBuckets' => [
			'default' => [ 2880 ],
		],
		'wgThumbnailMinimumBucketDistance' => [
			'default' => 100,
		],

		'wgUploadThumbnailRenderHttpCustomHost' => [
			'default' => 'upload.beta.wmflabs.org',
		],
		'wgUploadThumbnailRenderHttpCustomDomain' => [
			'default' => 'deployment-cache-upload04.eqiad.wmflabs',
		],

		'-wmgScorePath' => [
			'default' => "//upload.beta.wmflabs.org/score",
		],

		'wgRateLimitsExcludedIPs' => [
			'default' => [
				'198.73.209.0/24', // T87841 Office IP
				'162.222.72.0/21', // T126585 Sauce Labs IP range for browser tests
				'66.85.48.0/21', // also Sauce Labs
			],
		],

		'wmgUseCapiunto' => [
			'default' => true,
		],

		'wmgUsePopups' => [
			'default' => true,
		],
		'wgEnablePopupsMobile' => [
			'default' => true,
		],

		'wmgUseCheckUser' => [
			'default' => false,
		],

		'wmgMediaViewerUseThumbnailGuessing' => [
			'default' => false, // T69651
		],

		'wmgUseArticlePlaceholder' => [
			'default' => false,
			'wikidataclient' => true,
		],

		'wmgUseORES' => [
			'default' => false,
			'wikipedia' => true, // T127661
		],
		'wgOresModels' => [
			'default' => [
				'damaging' => true,
				'reverted' => true,
				'goodfaith' => true,
				'wp10' => false,
			],
			'wikipedia' => [
				'damaging' => true,
				'reverted' => false,
				'goodfaith' => false,
				'wp10' => false,
			], // T127661
		],
		'wgOresDamagingThresholds' => [
			'default' => [ 'hard' => 0.5, 'soft'=> 0.7 ],
			'wikipedia' => [ 'hard' => 0.6, 'soft'=> 0.7 ], // T127661
		],
		'wgOresEnabledNamespaces' => [
			'default' => [],
			'enwiki' => [ 0 => true ],
		],

		'wmgWikibaseAllowDataAccessInUserLanguage' => [
			'default' => false,
			'wikidatawiki' => true,
		],

		// Test the extension Collection in other languages for book creator,
		// which avoids the bugs related to the PDF generator.
		'wmgUseCollection' => [
			'zhwiki' => true, // T128425
		],

		// Affects URL uploads and chunked uploads (experimental).
		// Limit on other web uploads is enforced by PHP.
		'wgMaxUploadSize' => [
			'default' => 1024 * 1024 * 4096, // 4 GB (i.e. equals 2^31 - 1)
			'ptwiki' => 1024 * 500, // 500 KB - T25186
		],

		'wmgUseNewsletter' => [
			'default' => true,  // T127297
		],

		'wmgUseRevisionSlider' => [
			'default' => true,  // T134770
		],

		// Ensure ?action=credits isn't break and allow to work
		// to cache this information. See T130820.
		'wgMaxCredits' => [
			'default' => -1,
		],

		// Test PageAssessments. See T125551.
		'wmgUsePageAssessments' => [
			'default' => true,
		],

		// Test Wikidata descriptions on mobile: T127250
		'wgMFDisplayWikibaseDescriptionsAsTaglines' => [
			'default' => true,
		],
	];
} # wmflLabsSettings()
