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
	global $wgWMFConfigDir, $wgConf;

	// Override (or add) settings that we need within the labs environment,
	// but not in production.
	$betaSettings = wmfLabsSettings();

	// Set configuration string placeholder 'variant' to 'beta'.
	// Add a wikitag of 'beta' that can be used to merge beta specific and
	// default settings by using `'+beta' => array(...),`
	$wgConf->siteParamsCallback = function( $conf, $wiki ) {
		$wikiTags = [ 'beta' ];

		$betaDblistTags = [ 'flow_only_labs' ];
		foreach ( $betaDblistTags as $tag ) {
			$dblist = MWWikiversions::readDbListFile( $tag );
			if ( in_array( $wiki, $dblist ) ) {
				$wikiTags[] = $tag;
			}
		}

		return [
			'params' => [ 'variant' => 'beta' ],
			'tags' => $wikiTags
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
	global $wgWMFUdp2logDest;

	return [

		'wgParserCacheType' => [
			'default' => CACHE_MEMCACHED,
		],

		'wgSitename' => [
			'deploymentwiki' => 'Deployment',
			'wikivoyage'     => 'Wikivoyage',
		],

		'-wgServer' => [
			'wiktionary'	=> 'https://$lang.wiktionary.$variant.wmflabs.org',
			'wikipedia'     => 'https://$lang.wikipedia.$variant.wmflabs.org',
			'wikiversity'	=> 'https://$lang.wikiversity.$variant.wmflabs.org',
			'wikisource'	=> 'https://$lang.wikisource.$variant.wmflabs.org',
			'wikiquote'	=> 'https://$lang.wikiquote.$variant.wmflabs.org',
			'wikinews'	=> 'https://$lang.wikinews.$variant.wmflabs.org',
			'wikibooks'     => 'https://$lang.wikibooks.$variant.wmflabs.org',
			'wikivoyage'    => 'https://$lang.wikivoyage.$variant.wmflabs.org',

			'commonswiki'   => 'https://commons.wikimedia.$variant.wmflabs.org',
			'deploymentwiki'      => 'https://deployment.wikimedia.$variant.wmflabs.org',
			'loginwiki'     => 'https://login.wikimedia.$variant.wmflabs.org',
			'metawiki'      => 'https://meta.wikimedia.$variant.wmflabs.org',
			'testwiki'      => 'https://test.wikimedia.$variant.wmflabs.org',
			'zerowiki'      => 'https://zero.wikimedia.$variant.wmflabs.org',
			'wikidatawiki'  => 'https://wikidata.$variant.wmflabs.org',
			'en_rtlwiki' => 'https://en-rtl.wikipedia.$variant.wmflabs.org',
		],

		'-wgCanonicalServer' => [
			'wikipedia'     => 'https://$lang.wikipedia.$variant.wmflabs.org',
			'wikibooks'     => 'https://$lang.wikibooks.$variant.wmflabs.org',
			'wikiquote'	=> 'https://$lang.wikiquote.$variant.wmflabs.org',
			'wikinews'	=> 'https://$lang.wikinews.$variant.wmflabs.org',
			'wikisource'	=> 'https://$lang.wikisource.$variant.wmflabs.org',
			'wikiversity'     => 'https://$lang.wikiversity.$variant.wmflabs.org',
			'wiktionary'     => 'https://$lang.wiktionary.$variant.wmflabs.org',
			'wikivoyage'    => 'https://$lang.wikivoyage.$variant.wmflabs.org',

			'metawiki'      => 'https://meta.wikimedia.$variant.wmflabs.org',
			'commonswiki'	=> 'https://commons.wikimedia.$variant.wmflabs.org',
			'deploymentwiki'      => 'https://deployment.wikimedia.$variant.wmflabs.org',
			'loginwiki'     => 'https://login.wikimedia.$variant.wmflabs.org',
			'testwiki'      => 'https://test.wikimedia.$variant.wmflabs.org',
			'zerowiki'      => 'https://zero.wikimedia.$variant.wmflabs.org',
			'wikidatawiki'  => 'https://wikidata.$variant.wmflabs.org',
			'en_rtlwiki' => 'https://en-rtl.wikipedia.$variant.wmflabs.org',
		],

		'-wgUploadPath' => [
			'default' => 'https://upload.$variant.wmflabs.org/$site/$lang',
			'private' => '/w/img_auth.php',
		//	'wikimania2005wiki' => 'http://upload..org/wikipedia/wikimania', // back compat
			'commonswiki' => 'https://upload.$variant.wmflabs.org/wikipedia/commons',
			'metawiki' => 'https://upload.$variant.wmflabs.org/wikipedia/meta',
			'testwiki' => 'https://upload.$variant.wmflabs.org/wikipedia/test',
		],

		'-wgThumbnailBuckets' => [
			'default' => [ 256, 512, 1024, 2048, 4096 ],
		],

		'-wgThumbnailMinimumBucketDistance' => [
			'default' => 32,
		],

		'-wmgMathPath' => [
			'default' => 'https://upload.$variant.wmflabs.org/math',
		],

		'-wgDebugLogFile' => [
			'default' => "udp://{$wgWMFUdp2logDest}/wfDebug",
		],

		'-wgWMFDefaultMonologHandler' => [
			'default' => 'wgDebugLogFile',
		],

		// Additional log channels for beta cluster
		'wgWMFMonologChannels' => [
			'+beta' => [
				'CentralAuthVerbose' => 'debug',
				'dnsblacklist' => 'debug',
				'EventBus' => 'debug',
				'runJobs' => [ 'logstash' => 'info' ],
				'squid' => 'debug',
				'MessageCache' => 'debug',
			],
		],

		'wgWMFApplyGlobalBlocks' => [ // T123936
			'default' => true,
			'metawiki' => true,
			'deploymentwiki' => false
		],

		'-wgLogo' => [
			'default' => '/static/images/project-logos/betawiki.png',
			'commonswiki' => '/static/images/project-logos/betacommons.png',
			'metawiki' => '/static/images/project-logos/betametawiki.png', // T125942
			'wikibooks' => '/static/images/project-logos/betacommons.png',
			'wikidata' => '/static/images/project-logos/betawikidata.png',
			'wikinews' => '/static/images/project-logos/betacommons.png',
			'wikiquote' => '/static/images/project-logos/betacommons.png',
			'wikisource' => '/static/images/project-logos/betacommons.png',
			'wikiversity' => '/static/images/project-logos/betawikiversity.png',
			'wikivoyage' => '/static/images/project-logos/betacommons.png',
			'wiktionary' => '/static/images/project-logos/betacommons.png',
		],

		'wgFavicon' => [
			'dewiki' => 'https://upload.wikimedia.org/wikipedia/commons/1/14/Favicon-beta-wikipedia.png',
		],

		'-wgWMFEnableCaptcha' => [
			'default' => true,
		],

		'-wgWMFEchoCluster' => [
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
		'-wgWMFAbuseFilterCentralDB' => [
			'default' => 'deploymentwiki',
		],
		'-wgWMFUseGlobalAbuseFilters' => [
			'default' => true,
		],

		// T39852
		'wgWMFUseWikimediaShopLink' => [
			'default'    => false,
			'enwiki'     => true,
			'simplewiki' => true,
		],

		'-wgKartographerEnableMapFrame' => [
			'default'	=> true,
		],

		'wgMobileUrlTemplate' => [
			'default' => '%h0.m.%h1.%h2.%h3.%h4',
			'wikidatawiki' => 'm.%h0.%h1.%h2.%h3', // T87440
		],
		'wgMFMobileFormatterHeadings' => [
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
		'wgMFPhotoUploadEndpoint' => [
			'default' => 'https://commons.wikimedia.$variant.wmflabs.org/w/api.php',
		],
		'wgMFSpecialCaseMainPage' => [
			'default' => true,
			'enwiki' => false,
		],
		'wgMFExperiments' => [
			'default' => [
			],
		],

		'wgWMFULSPosition' => [
			# Beta-specific
			'deploymentwiki' => 'personal',
		],

		'wgWMFCommonsMetadataForceRecalculate' => [
			'default' => true,
		],

		// Don't use an http/https proxy
		'-wgCopyUploadProxy' => [
			'default' => false,
		],

		'-wgDisableUserGroupExpiry' => [
			'default' => false,
		],

		///
		/// ----------- BetaFeatures start ----------
		///

		// Enable all Beta Features in Beta Labs, even if not in production whitelist
		'wgBetaFeaturesWhitelist' => [
			'default' => false,
		],

		'wgWMFMediaViewerNetworkPerformanceSamplingFactor' => [
			'default' => 1,
		],

		'wgWMFVisualEditorUseSingleEditTab' => [
			'enwiki' => true,
		],
		'wgWMFVisualEditorTransitionDefault' => [
			'default' => false,
		],
		'wgWMFVisualEditorEnableWikitext' => [
			'default' => true,
		],

		///
		/// ------------ BetaFeatures end -----------
		///

		'wgWMFUseRSSExtension' => [
			'dewiki' => true,
		],
		'wgWMFRSSUrlWhitelist' => [
			'dewiki' => [ 'http://de.planet.wikimedia.org/atom.xml' ],
		],

		'wgWMFContentTranslationCluster' => [
			'default' => false,
		],
		'wgWMFContentTranslationCampaigns' => [
			'default' => [ 'newarticle' ],
		],
		'wgWMFContentTranslationEnableSuggestions' => [
			'default' => true,
		],

		// Whether Compact Links is Beta feature
		'wgWMFULSCompactLanguageLinksBetaFeature' => [
			'default' => false,
		],

		// Whether Compact Links is enabled for new accounts *by default*
		'wgWMFULSCompactLinksForNewAccounts' => [
			'default' => true,
		],

		// Whether Compact Links is enabled for anonymous users *by default*
		'wgWMFULSCompactLinksEnableAnon' => [
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

		'wgWMFUseFlow' => [
			// 'flow_computed_labs' is full set applicable on Beta Cluster.
			'flow_only_labs' => true,
		],
		# No separate Flow DB or cluster (yet) for labs.
		'-wgWMFFlowDefaultWikiDb' => [
			'default' => false,
		],
		'-wgWMFFlowCluster' => [
			'default' => false,
		],
		'wgWMFFlowEnableOptInBetaFeature' => [
			'enwiki' => true,
			'hewiki' => true,
		],

		'wgWPBBannerProperty' => [
			'default' => 'P751',
		],

		'wgWMFUseRelatedArticles' => [
			'default' => true,
		],
		'wgRelatedArticlesEnabledSamplingRate' => [
			'default' => 1,
			'enwiki' => 1,
		],

		// Enable anonymous editor acquisition experiment across labs
		'wgWMFGettingStartedRunTest' => [
			'default' => true,
		],

		'wgWMFExtraLanguageNames' => [
			'default' => [],
			'en_rtlwiki' => [ 'en-rtl' => 'English (rtl)' ],
		],

		'wgWMFUsePetition' => [
			'default' => false,
			'metawiki' => true,
		],

		'wgWMFUseQuickSurveys' => [
			'default' => true,
		],

		'wgWMFUseSentry' => [
			'default' => true,
		],
		'wgWMFSentryDsn' => [
			'default' => 'https://c357be0613e24340a96aeaa28dde08ad@sentry-beta.wmflabs.org/4',
		],

		'wgUploadThumbnailRenderHttpCustomHost' => [
			'default' => 'upload.beta.wmflabs.org',
		],
		'wgUploadThumbnailRenderHttpCustomDomain' => [
			'default' => 'deployment-cache-upload04.eqiad.wmflabs',
		],

		'-wgWMFScorePath' => [
			'default' => "//upload.beta.wmflabs.org/score",
		],

		'wgRateLimitsExcludedIPs' => [
			'default' => [
				'198.73.209.0/24', // T87841 Office IP
				'162.222.72.0/21', // T126585 Sauce Labs IP range for browser tests
				'66.85.48.0/21', // also Sauce Labs
			],
		],

		'wgWMFUseCapiunto' => [
			'default' => true,
		],

		'wgWMFUseCheckUser' => [
			'default' => false,
		],

		'wgWMFMediaViewerUseThumbnailGuessing' => [
			'default' => false, // T69651
		],

		'wgWMFUseArticlePlaceholder' => [
			'default' => false,
			'wikidataclient' => true,
		],

		'wgWMFUseORES' => [
			'default' => false,
			'wikipedia' => true, // T127661
		],
		'wgOresExtensionStatus' => [
			'cawiki' => 'on',
			'ruwiki' => 'on',
		],
		'wgOresModels' => [
			'default' => [
				'damaging' => true,
				'goodfaith' => false,
				'reverted' => true,
				'wp10' => false,
			],
			// This is separate because
			// it uses the real wgOresWikiId
			// below.
			'enwiki' => [
				'damaging' => true,
				'goodfaith' => true,
				'reverted' => false,
				'wp10' => false,
			],
			'wikipedia' => [
				'damaging' => true,
				'goodfaith' => false,
				'reverted' => false,
				'wp10' => false,
			], // T127661
		],
		'wgOresDamagingThresholds' => [
			'default' => [ 'hard' => 0.5, 'soft' => 0.7 ],
			'wikipedia' => [ 'hard' => 0.6, 'soft' => 0.7 ], // T127661
		],
		'wgOresEnabledNamespaces' => [
			'default' => [],
			'enwiki' => [ 0 => true ],
		],
		'wgOresWikiId' => [
			'default' => 'testwiki',
			'enwiki' => 'enwiki',
		],

		'wgWMFWikibaseAllowDataAccessInUserLanguage' => [
			'default' => false,
			'wikidatawiki' => true,
		],

		// Test the extension Collection in other languages for book creator,
		// which avoids the bugs related to the PDF generator.
		'wgWMFUseCollection' => [
			'zhwiki' => true, // T128425
		],

		'wgWMFUseElectronPdfService' => [
			'default' => true,
		],

		// Affects URL uploads and chunked uploads (experimental).
		// Limit on other web uploads is enforced by PHP.
		'wgMaxUploadSize' => [
			'default' => 1024 * 1024 * 4096, // 4 GB (i.e. equals 2^31 - 1)
			'ptwiki' => 1024 * 500, // 500 KB - T25186
		],

		'wgWMFUseNewsletter' => [
			'default' => true,  // T127297
		],

		'wgWMFUseLoginNotify' => [
			'default' => true, // T158878
			'nonecho' => false,
		],

		'wgWMFUseRevisionSlider' => [
			'default' => true,  // T134770
		],

		'wgWMFRevisionSliderBetaFeature' => [
			'default' => false, // T149725
		],

		// T160410 can be removed when the new view is the default
		'wgRevisionSliderAlternateSlider' => [
		        'default' => true,
		],

		// Ensure ?action=credits isn't break and allow to work
		// to cache this information. See T130820.
		'wgMaxCredits' => [
			'default' => -1,
		],

		// Test PageAssessments. See T125551.
		'wgWMFUsePageAssessments' => [
			'default' => true,
		],

		// Test PerformanceInspector
		'wgWMFUsePerformanceInspector' => [
			'default' => true,
		],

		// Test Wikidata descriptions on mobile: T127250
		'wgMFDisplayWikibaseDescriptions' => [
			'enwiki' => [
				'tagline' => true, 'search' => true, 'nearby' => true, 'watchlist' => true
			]
		],

		// Test numeric sorting. See T8948.
		'wgCategoryCollation' => [
			'default' => 'uppercase',
			'dewiki' => 'uca-de-u-kn', // T128806
			'enwiki' => 'uca-default-u-kn',
			'fawiki' => 'xx-uca-fa', // T139110
		],

		// Test Quiz. See T142692
		'wgWMFUseQuiz' => [
			'cawiki' => true,
		],

		// Test gallery settings; see T141349
		'wgWMFGalleryOptions' => [
			'default' => [
				'imagesPerRow' => 0,
				'imageWidth' => 120,
				'imageHeight' => 120,
				'captionLength' => true,
				'showBytes' => true,
				'mode' => 'traditional',
			],
			'+enwiki' => [
				'mode' => 'packed', // T141349
			],
		],

		'wgWMFUsePageViewInfo' => [
			'default' => true,
		],
		'wgPageViewInfoWikimediaDomain' => [
			'default' => 'en.wikipedia.org',
		],

		'wgWMFUseEmailAuth' => [
			'default' => 'true',
		],
		'wgWMFUseLinter' => [
			'default' => true,
		],
		'wgLinterSubmitterWhitelist' => [
			'default' => [
				'10.68.20.142' => true // deployment-parsoid09.deployment-prep.eqiad.wmflabs
			]
		],
		'wgWMFUseTwoColConflict' => [
			'default' => true,
		],
		'wgLinterStatsdSampleFactor' => [
			'default' => 10,
		],
		'wgWMFUseInterwikiSorting' => [
			'default' => false,
			'wikidataclient' => true,
			'wiktionary' => true,
		],
		'wgWMFUseCognate' => [
			'default' => false,
			'wiktionary' => 'wiktionary', // T156241
		],

		// T152115
		'wgPageImagesLeadSectionOnly' => [
			'default' => true,
		],

		// Probably no point in blocking Tool Labs edits to Beta Labs
		'wgWMFAllowLabsAnonEdits' => [
			'default' => true,
		],

		// T156800: Make Page Prevews (Popups) use RESTBase's page summary endpoint.
		'wgPopupsAPIUseRESTBase' => [
			'default' => true,
		],

		'wgEnableRcFiltersBetaFeature' => [
			'default' => true,
		],

		'wgWMFUseCollaborationKit' => [
			'default' => false,
			'enwiki' => true,
		],

		'wgWMFEnableDashikiData' => [
			'default' => true,
		],

		'wgWMFUseTimeless' => [
			'default' => true,
		],

		'wgWMFUse3d' => [
			'default' => true,
		],

		'wg3dProcessEnviron' => [
			'default' => [ 'DISPLAY' => ':99' ],
		],
	];
} # wmflLabsSettings()
