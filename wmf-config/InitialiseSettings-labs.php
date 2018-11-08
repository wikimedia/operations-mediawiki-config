<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

# This file holds per-wiki overrides specific to Beta Cluster.
# For overrides common to all wikis, see CommonSettings-labs.php.
#
# This for BETA and MUST NOT be loaded for production.
#
# Usage:
# - Prefix a setting key with '-' to override all values from
#   production InitialiseSettings.php.
# - Please wrap your code in functions to avoid tainting the global scope.
#
# Effective load order:
# - multiversion
# - mediawiki/DefaultSettings.php
# - wmf-config/*Services.php
# - wmf-config/InitialiseSettings.php
# - wmf-config/InitialiseSettings-labs.php [THIS FILE]
# - wmf-config/CommonSettings.php
# - wmf-config/CommonSettings-labs.php
#
# Included from: wmf-config/InitialiseSettings.php.
#

/**
 * Main entry point to override production settings. Supports key beginning with
 * a dash to completely override a setting.
 * Settings are fetched through wmfLabsSettings() defined below.
 *
 * @return array
 */
function wmfLabsOverrideSettings() {
	global $wmfConfigDir, $wgConf;

	// Override (or add) settings that we need within the labs environment,
	// but not in production.
	$betaSettings = wmfLabsSettings();

	// Set configuration string placeholder 'variant' to 'beta'.
	// Add a wikitag of 'beta' that can be used to merge beta specific and
	// default settings by using `'+beta' => array(...),`
	$wgConf->siteParamsCallback = function ( $conf, $wiki ) {
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
 *
 * @return array
 */
function wmfLabsSettings() {
	global $wmfUdp2logDest;

	return [

		'wgParserCacheType' => [
			'default' => CACHE_MEMCACHED,
		],

		'wgLanguageCode' => [
			'deploymentwiki' => 'en',
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
		// 'wikimania2005wiki' => 'http://upload..org/wikipedia/wikimania', // back compat
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
				'JobExecutor' => [ 'logstash' => 'debug' ],
				'runJobs' => [ 'logstash' => 'info' ],
				'squid' => 'debug',
				'MessageCache' => 'debug',
				'PageRandomLookup' => 'debug', // T208796
			],
		],

		'wmgApplyGlobalBlocks' => [ // T123936
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

		'-wmgEnableCaptcha' => [
			'default' => true,
		],

		// T205040
		'wgMediaInTargetLanguage' => [
			'default' => true,
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

		'wmgUseGuidedTour' => [
			'default' => true,
			'loginwiki' => false,
			'votewiki' => false,
		],

		// T39852
		'wmgUseWikimediaShopLink' => [
			'default'    => false,
			'enwiki'     => true,
			'simplewiki' => true,
		],

		'-wgKartographerEnableMapFrame' => [
			'default'	=> true,
		],
		'-wgKartographerUsePageLanguage' => [
			'default' => true,
		],
		'wgWMECitationUsagePopulationSize' => [
			'enwiki' => 1  // 100% — T191086
		],
		'wgMobileUrlTemplate' => [
			'default' => '%h0.m.%h1.%h2.%h3.%h4',
			'wikidatawiki' => 'm.%h0.%h1.%h2.%h3', // T87440
		],
		'wgMFMobileFormatterHeadings' => [
			'default' => [ 'h2', 'h3', 'h4', 'h5', 'h6' ], // T110436, T110837
		],

		'wgMinervaShowShareButton' => [
			'default' => [
				'base' => false,
				'beta' => true
			]
		],
		'wgMFRemovableClasses' => [
			'default' => [
				'base' => [ '.navbox' ],
				'beta' => [ '.navbox' ],
				'HTML' => [],
			],
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
		'wgMinervaErrorLogSamplingRate' => [
			'default' => 1,
		],
		'wgMinervaCountErrors' => [
			'default' => true
		],
		# Do not run any A/B tests on beta cluster (T206179)
		'-wgMinervaABSamplingRate' => [
			'default' => 0,
		],

		'wmgULSPosition' => [
			# Beta-specific
			'deploymentwiki' => 'personal',
		],

		'wmgCommonsMetadataForceRecalculate' => [
			'default' => true,
		],

		// Don't use an http/https proxy
		'-wgCopyUploadProxy' => [
			'default' => false,
		],

		///
		/// ----------- BetaFeatures start ----------
		///

		// Enable all Beta Features in Beta Labs, even if not in production whitelist
		'wgBetaFeaturesWhitelist' => [
			'default' => false,
		],

		'wmgVisualEditorUseSingleEditTab' => [
			'enwiki' => true,
		],
		'wmgVisualEditorTransitionDefault' => [
			'default' => false,
		],
		'wmgVisualEditorEnableWikitext' => [
			'default' => true,
			'commonswiki' => false,
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

		'wmgContentTranslationCluster' => [
			'default' => false,
		],
		'wmgContentTranslationCampaigns' => [
			'default' => [ 'newarticle' ],
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

		'wgSearchSuggestCacheExpiry' => [
			'default' => 300,
		],

		'wmgUseFlow' => [
			// 'flow-labs' is full set applicable on Beta Cluster.
			'flow_only_labs' => true,
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
			'hewiki' => true,
		],

		'wgWPBBannerProperty' => [
			'default' => 'P751',
		],

		'wmgUseRelatedArticles' => [
			'default' => true,
		],

		// Enable anonymous editor acquisition experiment across labs
		'wmgGettingStartedRunTest' => [
			'default' => true,
		],

		'wmgExtraLanguageNames' => [
			'default' => [ 'en-rtl' => 'English (rtl)' ],
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
		'wgSentryDsn' => [
			'default' => 'https://c357be0613e24340a96aeaa28dde08ad@sentry-beta.wmflabs.org/4',
		],

		'-wmgScorePath' => [
			'default' => "//upload.beta.wmflabs.org/score",
		],

		'wgRateLimitsExcludedIPs' => [
			'default' => [
				'198.73.209.0/24', // T87841 Office IP
				'162.222.72.0/21', // T126585 Sauce Labs IP range for browser tests
				'66.85.48.0/21', // also Sauce Labs
				'10.68.16.0/21', // Jenkins and Sauce labs T167432
			],
		],

		'wmgUseCapiunto' => [
			'default' => true,
		],

		'wmgUseCheckUser' => [
			'default' => false,
		],

		'wgMediaViewerNetworkPerformanceSamplingFactor' => [
			'default' => 1,
		],

		'wgMediaViewerUseThumbnailGuessing' => [
			'default' => false, // T69651
		],

		'wmgUseArticlePlaceholder' => [
			'default' => false,
			'wikidataclient' => true,
		],

		'wgArticlePlaceholderRepoApiUrl' => [
			'default' => 'https://wikidata.beta.wmflabs.org/w/api.php',
		],

		'wmgWikibaseMaxItemIdForNewItemIdHtmlFormatter' => [
			'default' => 0,
			'wikidatawiki' => 10000000,
		],

		'wgLexemeLanguageCodePropertyId' => [
			'default' => null,
			'wikidatawiki' => 'P218',
		],

		'-wmgWikibaseSearchIndexProperties' => [
			'default' => []
		],

		'-wmgWikibaseSearchIndexPropertiesExclude' => [
			'default' => []
		],

		'-wmgWikibaseSearchStatementBoosts' => [
			'default' => []
		],

		// ###### Structured Data on Commons testing installation ######
		'wmgUseWikibaseRepo' => [
			'commonswiki' => true,
		],

		'wmgUseWikibaseMediaInfo' => [
			'commonswiki' => true,
		],

		'wgMediaInfoEnable' => [
			'commonswiki' => true,
		],

		// Test the extension Collection in other languages for book creator,
		// which avoids the bugs related to the PDF generator.
		'wmgUseCollection' => [
			'zhwiki' => true, // T128425
		],

		'wmgUseElectronPdfService' => [
			'default' => true,
		],

		// Affects URL uploads and chunked uploads (experimental).
		// Limit on other web uploads is enforced by PHP.
		'wgMaxUploadSize' => [
			'default' => 1024 * 1024 * 4096, // 4 GB (i.e. equals 2^31 - 1)
			'ptwiki' => 1024 * 500, // 500 KB - T25186
		],

		'wmgUseTemplateWizard' => [
			'default' => true, // T202545
		],

		'wmgUseNewsletter' => [
			'default' => true,  // T127297
		],

		'wmgUseLoginNotify' => [
			'default' => true, // T158878
			'nonecho' => false,
		],

		'-wmgUseCodeMirror' => [
			'default' => true,
		],

		'wmgUseArticleCreationWorkflow' => [
			'default' => false,
			'enwiki' => true,
		],

		'wgArticleCreationWorkflows' => [
			'default' => [
				[
					'namespaces' => [ 0 ],
					'excludeRight' => 'autoconfirmed'
				],
			],
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

		// Test PerformanceInspector
		'wmgUsePerformanceInspector' => [
			'default' => true,
		],

		// The LOWER the value, the MORE requests will be logged.
		// 100 = 1 of every 100 (1%), 1 = 1 of every 1 (100%).
		'wgNavigationTimingSamplingFactor' => [
			// Beta Cluster: 10% of page loads
			'default' => 10,
		],

		'wmgUseAdvancedSearch' => [
			'default' => true,
		],
		'wgAdvancedSearchBetaFeature' => [
			'default' => false,
		],

		'wmgUseFileImporter' => [
			'default' => false,
			'commonswiki' => 'FileImporter-WikimediaSitesTableSite',
			'testwiki' => 'FileImporter-Site-DefaultMediaWiki',
		],

		'wmgUseFileExporter' => [
			'default' => false,
			'metawiki' => true,
			'deploymentwiki' => true,
			'wikipedia' => true,
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
			'fawiki' => 'uca-fa',
		],

		// Test Quiz. See T142692
		'wmgUseQuiz' => [
			'cawiki' => true,
		],

		// Test gallery settings; see T141349
		'wmgGalleryOptions' => [
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

		'wmgUsePageViewInfo' => [
			'default' => true,
		],
		'wgPageViewInfoWikimediaDomain' => [
			'default' => 'en.wikipedia.org',
		],

		'wmgUseLinter' => [
			'default' => true,
		],
		'wgLinterSubmitterWhitelist' => [
			'default' => [
				'10.68.20.142' => true // deployment-parsoid09.deployment-prep.eqiad.wmflabs
			]
		],
		'wgLinterStatsdSampleFactor' => [
			'default' => 10,
		],

		// T152115
		'wgPageImagesLeadSectionOnly' => [
			'default' => true,
		],

		// Probably no point in blocking Tool Labs edits to Beta Labs
		'wmgAllowLabsAnonEdits' => [
			'default' => true,
		],

		// Enable page previews for everyone in labs (T162672)
		//
		// Note well that the Popups extension is only loaded when either
		// wmgUsePopups or wmgPopupsBetaFeature is truthy.
		'-wmgUsePopups' => [
			'default' => true,
		],

		// T191888: Enable page previews configuration for newly created accounts
		'wgPopupsOptInStateForNewAccounts' => [
			'default' => '1',
		],

		// T171853: Enable PP EventLogging instrumentation on enwiki only so that we
		// can prove the killswitch works.
		'-wgPopupsEventLogging' => [
			'default' => false,
			'enwiki' => true,
		],
		// T184793: Enable the VirtualPageViews on beta so we can easily test and track
		// Popups visible for more than 1s as a virtual page views
		'wgPopupsVirtualPageViews' => [
			'default' => true
		],

		// T165018: Make Page Previews (Popups) use RESTBase's page summary endpoint
		// and consume the HTML returned.
		'-wgPopupsGateway' => [
			'default' => 'restbaseHTML',
		],

		'-wgPopupsStatsvSamplingRate' => [
			'default' => 1,
		],

		'wmgUseCollaborationKit' => [
			'default' => false,
			'enwiki' => true,
		],

		'wmgEnableDashikiData' => [
			'default' => true,
		],

		'wmgUseTemplateStyles' => [
			'default' => true,
		],

		'wgCognateReadOnly' => [
			'default' => false,
		],

		'wgEchoMaxMentionsInEditSummary' => [
			'default' => 5,
		],

		'wmgUseNewWikiDiff2Extension' => [
			'default' => true,
		],

		'wgReadingListsCluster' => [
			'default' => false,
		],
		'wgReadingListsDatabase' => [
			'default' => 'metawiki',
		],

		'-wgPageCreationLog' => [
			'default' => true, // T196400
		],

		// Use a constant MLR model for all wikis. It's not ideal, but
		// no models were trained specifically for data in labs anyways.
		'-wmgCirrusSearchMLRModel' => [
			'default' => '20171023_enwiki_v1'
		],

		'wgActorTableSchemaMigrationStage' => [
			'default' => SCHEMA_COMPAT_WRITE_BOTH | SCHEMA_COMPAT_READ_OLD,
		],

		'wgCommentTableSchemaMigrationStage' => [
			'default' => MIGRATION_WRITE_NEW,
		],

		'wgChangeTagsSchemaMigrationStage' => [
			'default' => MIGRATION_WRITE_BOTH,
		],

		'wgMultiContentRevisionSchemaMigrationStage' => [
			'default' => SCHEMA_COMPAT_WRITE_BOTH | SCHEMA_COMPAT_READ_NEW,
		],

		'wgTagStatisticsNewTable' => [
			'default' => true,
		],

		'wmgUseJADE' => [
			'default' => true,
		],

		'wgOresUiEnabled' => [
			'default' => true,
			'wikidatawiki' => false,
		],
		'wgOresModels' => [
			'default' => [
				'damaging' => [ 'enabled' => true ],
				'goodfaith' => [ 'enabled' => true ],
				'reverted' => [ 'enabled' => false ],
				'articlequality' => [ 'enabled' => false, 'namespaces' => [ 0 ], 'cleanParent' => true ],
				'draftquality' => [ 'enabled' => false, 'namespaces' => [ 0 ], 'types' => [ 1 ] ],
			],
			'enwiki' => [
				'damaging' => [ 'enabled' => true, 'excludeBots' => true ],
				'goodfaith' => [ 'enabled' => true, 'excludeBots' => true ],
				'reverted' => [ 'enabled' => false ],
				'articlequality' => [ 'enabled' => true, 'namespaces' => [ 0, 118 ], 'cleanParent' => true ],
				'draftquality' => [ 'enabled' => true, 'namespaces' => [ 0, 118 ], 'types' => [ 1 ], 'excludeBots' => true, 'cleanParent' => true ],
			],
		],
		'wgOresExcludeBots' => [
			'default' => true,
			'enwiki' => false,
		],
		'wgWMEAICaptchaEnabled' => [
			'default' => true,
		],
		// T184668
		'wmgUseGlobalPreferences' => [
			// Explicitly disabled on non-CentralAuth wikis in CommonSettings.php
			'default' => true,
		],
		'wmgLocalAuthLoginOnly' => [
			// Explicitly disabled on non-CentralAuth wikis in CommonSettings.php
			'default' => true,
		],
		'wgPageTriageDraftNamespaceId' => [
			'enwiki' => 118,
		],
		'wgPageTriageEnableOresFilters' => [
			'default' => false,
			'enwiki' => true,
		],
		'wgPageTriageEnableCopyvio' => [
			'default' => false,
			'enwiki' => true,
		],
		'wmgUseSkinPerPage' => [
			'default' => false,
			'metawiki' => true,
		],
		'wmgUseEUCopyrightCampaign' => [
			'default' => false,
			'metawiki' => true,
		],
		'wmgWikibaseCachePrefix' => [
			'commonswiki' => 'commonswiki',
		],
		'wgWMEUnderstandingFirstDay' => [
			'default' => false,
			'enwiki' => true,
			'kowiki' => true,
		],
		'wgEnablePartialBlocks' => [
			'default' => true,
		],
		'wgPropertySuggesterClassifyingPropertyIds' => [
			'wikidatawiki' => [ 694 ],
		],
		'wgPropertySuggesterInitialSuggestions' => [
			'wikidatawiki' => [ 694 ],
		],
		'wgPropertySuggesterDeprecatedIds' => [
			'wikidatawiki' => [ 107 ],
		],
		'wmgWikibaseRepoBadgeItems' => [
			'wikidatawiki' => [
				'Q49444' => 'wb-badge-goodarticle',
				'Q49447' => 'wb-badge-featuredarticle',
				'Q49448' => 'wb-badge-recommendedarticle', // T72268
				'Q49449' => 'wb-badge-featuredlist', // T72332
				'Q49450' => 'wb-badge-featuredportal', // T75193
				'Q98649' => 'wb-badge-notproofread', // T97014 - Wikisource badges
				'Q98650' => 'wb-badge-problematic',
				'Q98658' => 'wb-badge-proofread',
				'Q98651' => 'wb-badge-validated'
			],
		],
		'wmgWikibaseClientBadgeClassNames' => [
			'default' => [
				'Q49444' => 'badge-goodarticle',
				'Q49447' => 'badge-featuredarticle',
				'Q49448' => 'badge-recommendedarticle', // T72268
				'Q49449' => 'badge-featuredlist', // T72332
				'Q49450' => 'badge-featuredportal', // T75193
				'Q98649' => 'badge-notproofread', // T97014 - Wikisource badges
				'Q98650' => 'badge-problematic',
				'Q98658' => 'badge-proofread',
				'Q98651' => 'badge-validated'
			],
		],
		'wgWikimediaBadgesCommonsCategoryProperty' => [
			'default' => 'P725',
			'commonswiki' => null,
		],
		'wmgWBRepoFormatterUrlProperty' => [
			'wikidatawiki' => 'P9094',
		],
		'wmgWBRepoPreferredGeoDataProperties' => [
			'wikidatawiki' => [
				'P740',
				'P477',
			],
		],
		'wmgWBRepoPreferredPageImagesProperties' => [
			'wikidatawiki' => [
				'P448',
				'P715',
				'P723',
				'P733',
				'P964',
			],
		],
		'wgArticlePlaceholderImageProperty' => [
			'default' => 'P964',
		],
		'wmgWikibaseClientEchoIcon' => [
			'default' => [ 'path' => '/static/images/wikibase/echoIcon.svg' ],
		],
		'wmgWBRepoCanonicalUriProperty' => [
			'default' => null,
			'wikidata' => 'P174944',
		],
		'wmgWikibaseClientUseTermsTableSearchFields' => [
			'default' => true,
		],
		'wmgWikibaseClientPropertyOrderUrl' => [
			'default' => null,
		],
		'wmgWikibaseClientRepoUrl' => [
			'default' => 'https://wikidata.beta.wmflabs.org',
		],
		'wmgWikibaseClientRepositories' => [
			'default' => [
				'' => [
					'repoDatabase' => 'wikidatawiki',
					'entityNamespaces' => [
						'item' => 0,
						'property' => 120,
						'lexeme' => 146,
					],
					'baseUri' => 'https://wikidata.beta.wmflabs.org/entity/',
					'prefixMapping' => [ '' => '' ],
				]
			],
		],

		// T208763
		//
		'wmgWikibaseClientPageSchemaNamespaces' => [
			// All sampled pages in the main namespace will be tested.
			'default' => [
				0,
			]
		],
		'wmgWikibaseClientPageSchemaSplitTestSamplingRatio' => [
			// 50% of pages on the Beta Cluster will be sampled. Half of those (25%) will receive the new
			// treatment.
			'default' => 0.5,
		],
		'wmgWikibaseClientPageSchemaSplitTestBuckets' => [
			// Pages are bucketed in [0, .5) for control and [.5, 1) for treatment. If a page is sampled
			// and bucketed in treatment, it will contain the new schema changes. Otherwise, it will have
			// no changes.
			'default' => [
				'control',
				'treatment',
			]
		],

	];
} # wmflLabsSettings()
