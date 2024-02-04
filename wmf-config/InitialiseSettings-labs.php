<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

# This file holds per-wiki overrides specific to Beta Cluster.
# For overrides common to all wikis, see CommonSettings-labs.php.
#
# This for BETA and MUST NOT be loaded for production.
#
# Usage:
# - By default, the array for each configuration variable is merged with the array
#   from InitialiseSettings.php. The individual values are not merged.
#   E.g. if InitialiseSettings has
#     [ 'default' => [ 'x' ], 'enwiki' => [ 'y' ] ]
#   and this file has
#     [ 'default' => [ 'v' ], 'wikipedia' => [ 'w' ] ]
#   then the effective value will be
#     [ 'default' => [ 'v' ], 'enwiki' => [ 'y' ], 'wikipedia' => [ 'w' ] ]
# - Prefix a setting key with '-' to disable merging (ignore InitialiseSettings.php
#   values entirely).
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
# Included from: wmf-config/CommonSettings.php.
#

// Inline comments are often used for noting the task(s) associated with specific configuration
// and requiring comments to be on their own line would reduce readability for this file
// phpcs:disable MediaWiki.WhiteSpace.SpaceBeforeSingleLineComment.NewLineComment

use MediaWiki\Extension\WikimediaEditorTasks\WikipediaAppCaptionEditCounter;
use MediaWiki\Extension\WikimediaEditorTasks\WikipediaAppDescriptionEditCounter;
use MediaWiki\Extension\WikimediaEditorTasks\WikipediaAppImageDepictsEditCounter;

/**
 * Get overrides for Beta Cluster settings. This is applied in MWConfigCacheGenerator.
 *
 * Keys that start with a hyphen will completely override the prodution settings
 * from InitializeSettings.php.
 *
 * Keys that don't start with a hyphen will have their settings merged with
 * the production settings.
 *
 * @return array
 */
function wmfGetOverrideSettings() {
	return [

		// All settings with $lang, $site, etc. for replacement during initialisation must be listed here
		'@replaceableSettings' => [
		],

		'wgParserCacheType' => [
			// Like production, but without the mysql fallback behind it
			// See $wgObjectCaches['mysql-multiwrite'] in CommonSettings.php
			'default' => 'mcrouter',
		],

		'wgParsoidCacheConfig' => [
			'default' => [
				'StashType' => null, // defaults to using MainStash
				'StashDuration' => 2 * 60 * 60, // 24h in production
				'CacheThresholdTime' => 0.0, // 0 means cache all
				'WarmParsoidParserCache' => true, // T320535: enable cache warming
			],
		],

		'wgLanguageCode' => [
			'votewiki' => 'en', // T295242
		],

		'wgSitename' => [
			'wikivoyage'     => 'Wikivoyage',
		],

		'-wgServer' => [
			'wiktionary'	=> 'https://$lang.wiktionary.beta.wmflabs.org',
			'wikipedia'     => 'https://$lang.wikipedia.beta.wmflabs.org',
			'wikiversity'	=> 'https://$lang.wikiversity.beta.wmflabs.org',
			'wikisource'	=> 'https://$lang.wikisource.beta.wmflabs.org',
			'wikiquote'	=> 'https://$lang.wikiquote.beta.wmflabs.org',
			'wikinews'	=> 'https://$lang.wikinews.beta.wmflabs.org',
			'wikibooks'     => 'https://$lang.wikibooks.beta.wmflabs.org',
			'wikivoyage'    => 'https://$lang.wikivoyage.beta.wmflabs.org',

			'apiportalwiki'       => 'https://api.wikimedia.beta.wmflabs.org',
			'commonswiki'   => 'https://commons.wikimedia.beta.wmflabs.org',
			'foundationwiki' => 'https://foundation.wikimedia.beta.wmflabs.org',
			'incubatorwiki' => 'https://incubator.wikimedia.beta.wmflabs.org',
			'loginwiki'     => 'https://login.wikimedia.beta.wmflabs.org',
			'metawiki'      => 'https://meta.wikimedia.beta.wmflabs.org',
			'votewiki'      => 'https://vote.wikimedia.beta.wmflabs.org',
			'wikidatawiki'  => 'https://wikidata.beta.wmflabs.org',
			'wikifunctionswiki' => 'https://wikifunctions.beta.wmflabs.org',
			'en_rtlwiki' => 'https://en-rtl.wikipedia.beta.wmflabs.org',
		],

		'-wgCanonicalServer' => [
			'wikipedia'     => 'https://$lang.wikipedia.beta.wmflabs.org',
			'wikibooks'     => 'https://$lang.wikibooks.beta.wmflabs.org',
			'wikiquote'	=> 'https://$lang.wikiquote.beta.wmflabs.org',
			'wikinews'	=> 'https://$lang.wikinews.beta.wmflabs.org',
			'wikisource'	=> 'https://$lang.wikisource.beta.wmflabs.org',
			'wikiversity'     => 'https://$lang.wikiversity.beta.wmflabs.org',
			'wiktionary'     => 'https://$lang.wiktionary.beta.wmflabs.org',
			'wikivoyage'    => 'https://$lang.wikivoyage.beta.wmflabs.org',

			'apiportalwiki'       => 'https://api.wikimedia.beta.wmflabs.org',
			'metawiki'      => 'https://meta.wikimedia.beta.wmflabs.org',
			'commonswiki'	=> 'https://commons.wikimedia.beta.wmflabs.org',
			'foundationwiki' => 'https://foundation.wikimedia.beta.wmflabs.org',
			'loginwiki'     => 'https://login.wikimedia.beta.wmflabs.org',
			'votewiki'      => 'https://vote.wikimedia.beta.wmflabs.org',
			'wikidatawiki'  => 'https://wikidata.beta.wmflabs.org',
			'wikifunctionswiki' => 'https://wikifunctions.beta.wmflabs.org',
			'en_rtlwiki' => 'https://en-rtl.wikipedia.beta.wmflabs.org',
		],

		'-wgUploadPath' => [
			'default' => 'https://upload.wikimedia.beta.wmflabs.org/$site/$lang',
			'private' => '/w/img_auth.php',
			'commonswiki' => 'https://upload.wikimedia.beta.wmflabs.org/wikipedia/commons',
			'metawiki' => 'https://upload.wikimedia.beta.wmflabs.org/wikipedia/meta',
			'testwiki' => 'https://upload.wikimedia.beta.wmflabs.org/wikipedia/test',
		],

		'-wgThumbnailBuckets' => [
			'default' => [ 256, 512, 1024, 2048, 4096 ],
		],

		'-wgThumbnailMinimumBucketDistance' => [
			'default' => 32,
		],

		'-wmgDefaultMonologHandler' => [
			'default' => 'wgDebugLogFile',
		],

		// Stream config default settings.
		// The EventStreamConfig extension will add these
		// settings to each entry in wgEventStreams if
		// the entry does not already have the setting.
		// Beta only has eqiad. prefixed topics.
		'wgEventStreamsDefaultSettings' => [
			'default' => [
				'topic_prefixes' => [ 'eqiad.' ],
			],
		],

		// EventStreamConfig overrides for beta sites.
		//
		// NOTE: It is not valid to use '+' with 'default'.
		// Keys for group/wiki here replace those from prod. The result is interpreted by
		// SiteConfiguration. There can only be one 'default' in the end. The order is the same
		// as within prod settings (default>group>wikiid), with later ones replacing earlier ones,
		// unless '+' is used on a later one in which case the values are merged.
		'wgEventStreams' => [
			'+wikipedia' => [
				// Override default settings to enable
				// rc0.mediawiki.page_change on selective wikis as we roll it out and test it.
				// https://phabricator.wikimedia.org/T311129
				'rc0.mediawiki.page_change' => [
					'producers' => [
						'mediawiki_eventbus' => [
							'enabled' => true,
						],
					],
				],
			],
			'+enwiki' => [
				'mediawiki.ipinfo_interaction' => [
					'schema_title' => 'analytics/mediawiki/ipinfo_interaction',
					'destination_event_service' => 'eventgate-analytics-external',
				],
				// See https://phabricator.wikimedia.org/T310852
				'desktop_mobile_link_clicks' => [
					'schema_title' => '/analytics/mediawiki/client/metrics_event',
					'destination_event_service' => 'eventgate-analytics-external',
					'producers' => [
						'metrics_platform_client' => [
							'events' => [
								'mediawiki.desktop_link.click', // T310852
							],
							'provide_values' => [
								'page_title',
								'page_namespace',
								'performer_edit_count_bucket',
								'performer_groups',
								'performer_is_logged_in',
							],
						],
					],
				],
			]
		],

		// EventLogging will POST events to this URI.
		'wgEventLoggingServiceUri' => [
			// Configured in profile::trafficserver::backend::mapping_rules
			// in Horizon hiera prefixpuppet for deployment-cache-text.
			'default' => 'https://intake-analytics.wikimedia.beta.wmflabs.org/v1/events?hasty=true',
		],

		// Historically, EventLogging would register Schemas and revisions it used
		// via the EventLoggingSchemas extension attribute like in
		// https://gerrit.wikimedia.org/g/mediawiki/extensions/WikimediaEvents/+/master/extension.json#51
		//
		// It also overrides these with the $wgEventLoggingSchemas global.
		// While in the process of migrating legacy EventLogging events to event platform,
		// use the wgEventLoggingSchemas to override the extension attribute for an incremental rollout.
		// This will be removed from mediawiki-config once all schemas have been successfully migrated
		// and the EventLoggingSchemas extension attributes are all set to Event Platform schema URIs.
		// ONLY Legacy EventLogging schemas need to go here.  This is the switch that tells
		// EventLogging to POST to EventGate rather than GET to EventLogging beacon.
		// https://phabricator.wikimedia.org/T238230
		'wgEventLoggingSchemas' => [
			'+group2' => [
				// Add beta only migrated wgEventLoggingSchemas configs here.  Production
				// wgEventLoggingSchemas are merged in, so if your settings for
				// production and beta are the same, you can omit also adding an entry here.
			],
		],

		'wgEventLoggingStreamNames' => [
			'+enwiki' => [
				'mediawiki.ipinfo_interaction',
				'desktop_mobile_link_clicks',
			],
		],

		// Log channels for beta cluster
		// See detailed comments on 'wmgMonologChannels' in InitializeSettings.php
		// Note: logstash won't go below info level, unless logstash=>debug is specified
		'wmgMonologChannels' => [
			'default' => [
				// Copied from default production
				// For channels where there is production configuration that labs
				// overrides, the relevant line from the production config is commented out
				// rather than removed, so that it is clear what has changed
				'404' => 'debug',
				'AbuseFilter' => 'debug',
				'AdHocDebug' => 'debug', // for temp live debugging
				'antispoof' => 'debug',
				'api' => [ 'logstash' => false ],
				'api-feature-usage' => 'debug',
				'api-readonly' => 'debug',
				'api-request' => [ 'udp2log' => false, 'logstash' => false, 'eventbus' => 'debug', 'buffer' => true ],
				'api-warning' => 'debug',
				'authentication' => 'info',
				'authevents' => 'info',
				'badpass' => 'debug',
				'badpass-priv' => 'debug',
				'BlockManager' => 'info',
				'BounceHandler' => 'debug',
				'Bug58676' => 'debug', // Invalid message parameter
				'cache-cookies' => 'debug',
				'captcha' => 'debug',
				'CampaignEvents' => 'debug',
				'CentralAuth' => 'debug',
				'CentralNotice' => 'debug',
				// 'CirrusSearch' => 'debug',
				'cirrussearch-request' => [ 'udp2log' => false, 'logstash' => false, 'eventbus' => 'debug', 'buffer' => true, ],
				'CirrusSearchChangeFailed' => 'debug',
				'CirrusSearchSlowRequests' => 'debug',
				'cite' => 'debug',
				'Cognate' => 'debug', // WMDE & Addshore
				'collection' => 'debug', // -cscott for T73675
				// 'csp' => [ 'logstash' => 'info', 'udp2log' => 'info' ],
				// 'csp-report-only' => [ 'logstash' => 'info', 'udp2log' => 'info' ],
				'rdbms' => 'warning',
				'DeferredUpdates' => 'error',
				'deprecated' => 'debug',
				'diff' => 'debug',
				'editpage' => 'warning', // T251023
				'Echo' => 'debug',
				'Elastica' => 'info',
				'error' => 'debug',
				// 'EventBus' => [ 'logstash' => 'error' ],
				'EventLogging' => 'debug',
				'exception' => 'debug',
				'exception-json' => [ 'logstash' => false ],
				'exec' => 'debug',
				'export' => 'debug',
				'ExtensionDistributor' => 'error', // T225243
				'ExternalStore' => 'debug',
				'fatal' => 'debug',
				'FileImporter' => 'debug',
				'FileOperation' => 'debug',
				'Flow' => 'debug', // -erikb 2014/03/08
				'formatnum' => 'info', // - cscott 2020/11/09 for T267587/T267370
				'FSFileBackend' => 'debug', // - gilles for T75229
				'generated-pp-node-count' => 'debug',
				'GlobalTitleFail' => [ 'sample' => 10000 ], // chad hates $wgTitle
				'GlobalWatchlist' => [ 'logstash' => 'debug', 'udp2log' => 'debug' ], // T268181
				'goodpass' => 'debug',
				'goodpass-priv' => 'debug',
				'GrowthExperiments' => 'info',
				'headers-sent' => 'debug',
				'HttpError' => 'error', // Only log http errors with a 500+ code T85795
				// 'JobExecutor' => [ 'logstash' => 'warning' ],
				'ldap' => 'warning',
				'Linter' => 'debug',
				'LocalFile' => 'debug',
				'localhost' => [ 'logstash' => false ],
				'LockManager' => 'warning',
				'logging' => 'debug',
				'LoginNotify' => 'debug',
				'MassMessage' => 'debug', // for 59464 -legoktm 2013/12/15
				'Math' => 'info', // mobrovac for T121445
				'mediamoderation' => 'debug', // for T303312 changed from warning
				'memcached' => 'error', // -aaron 2012/10/24
				'message-format' => [ 'logstash' => 'warning' ],
				'MessageCacheError' => 'debug',
				'mobile' => 'debug',
				'NewUserMessage' => 'debug',
				'OAuth' => 'info', // T244185
				'objectcache' => 'warning',
				'OutputBuffer' => 'debug',
				'ORES' => 'info',
				'PageTriage' => 'debug',
				'PageViewInfo' => 'info',
				'ParserCache' => 'warning',
				'Parsoid' => 'warning',
				'poolcounter' => 'debug',
				'preferences' => 'info',
				'purge' => 'debug',
				'query' => 'debug',
				'ratelimit' => 'debug',
				'readinglists' => 'warning',
				'recursion-guard' => 'debug',
				'RecursiveLinkPurge' => 'debug',
				'redis' => 'info', // -asher 2012/10/12
				'Renameuser' => 'debug',
				'resourceloader' => 'info',
				'ResourceLoaderImage' => 'debug', // - demon, matmarex
				'RevisionStore' => 'info',
				// 'runJobs' => [ 'logstash' => 'warning' ], // - bd808, T113571
				'SaveParse' => 'debug',
				'security' => 'debug',
				'session' => [ 'udp2log' => false, 'logstash' => 'info' ],
				'session-ip' => [ 'udp2log' => false, 'logstash' => 'info' ],
				'SimpleAntiSpam' => 'debug',
				'slow-parse' => 'debug',
				'slow-parsoid' => 'info',
				'SpamBlacklistHit' => 'debug',
				'SpamRegex' => 'debug',
				'SQLBagOStuff' => 'debug',
				'StashEdit' => 'debug',
				'StopForumSpam' => 'debug',
				'SwiftBackend' => 'debug', // -aaron 5/15/12
				'texvc' => 'debug',
				'throttler' => 'info',
				'thumbnail' => 'debug',
				'thumbnailaccess' => 'debug', // T106323
				'TitleBlacklist-cache' => 'debug', // For T85428
				'torblock' => 'debug',
				'TranslationNotifications.Jobs' => 'debug',
				'Translate.Jobs' => 'debug',
				'Translate.MessageBundle' => 'debug',
				'Translate.TtmServerUpdates' => 'debug',
				'Translate' => 'debug',
				'UpdateRepo' => 'debug',
				'updateTranstagOnNullRevisions' => 'debug',
				'upload' => 'debug',
				'VisualEditor' => 'debug',
				'wfLogDBError' => 'debug', // Former $wgDBerrorLog
				'Wikibase' => [ 'udp2log' => 'info', 'logstash' => 'warning', 'sample' => false, ],
				'WikibaseQualityConstraints' => 'debug',
				'WikimediaEvents' => 'error', // For T205754 & T208233
				'WikiLambda' => 'warning',
				'WikitechGerritBan' => 'debug',
				'WikitechPhabBan' => 'debug',
				'WMDE' => 'debug', // WMDE & Addshore T174948 & T191500
				'xff' => [ 'logstash' => false ],
				'XMP' => 'warning', // T89532

				// Additional logging for labs
				'CentralAuthVerbose' => 'debug',
				'CirrusSearch' => 'info',
				'csp' => 'info',
				'csp-report-only' => 'info',
				'dnsblacklist' => 'debug',
				'EventBus' => 'debug',
				'JobExecutor' => [ 'logstash' => 'debug' ],
				'MessageCache' => 'debug',
				'runJobs' => [ 'logstash' => 'info' ],
				'squid' => 'debug',
				'Phonos' => 'debug',
			],
		],

		'-wmgSiteLogo1x' => [
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
			'wikifunctionswiki' => '/static/images/project-logos/betawikifunctions.png',
		],
		'-wmgSiteLogo1_5x' => [],
		'-wmgSiteLogo2x' => [],
		'-wmgSiteLogoIcon' => [
			'default' => '/static/images/project-logos/wikimedia-cloud-services.svg'
		],
		'-wmgSiteLogoVariants' => [
			'default' => null,
		],
		'-wmgSiteLogoWordmark' => [
			'default' => null,
		],
		'-wmgSiteLogoTagline' => [
			'default' => null,
		],

		'wgFavicon' => [
			'dewiki' => 'https://upload.wikimedia.org/wikipedia/commons/1/14/Favicon-beta-wikipedia.png',
			'enwiki' => 'https://upload.wikimedia.org/wikipedia/commons/1/14/Favicon-beta-wikipedia.png',
		],

		'-wmgEnableCaptcha' => [
			'default' => true,
		],

		'-wmgEchoCluster' => [
			'default' => false,
		],

		'wgEchoPollForUpdates' => [
			'enwiki' => 60,
			'cawiki' => 60,
		],

		'wmgEchoEnablePush' => [
			'default' => true,
		],

		'wgEchoPushServiceBaseUrl' => [
			'default' => 'http://deployment-push-notifications01.deployment-prep.eqiad1.wikimedia.cloud:8900/v1/message',
		],

		'wgEchoPushMaxSubscriptionsPerUser' => [
			'default' => 10,
		],

		'wgMaxExecutionTimeForExpensiveQueries' => [
			'default' => 30000,
		],

		'wgEchoWatchlistNotifications' => [
			'default' => true,
		],

		# FIXME: make that settings to be applied
		'-wgShowExceptionDetails' => [
			'default' => true,
		],

		'wgSignatureValidation' => [
			'default' => 'new', // T248632
		],

		# To help fight spam, makes rules maintained on metawiki
		# to be available on all beta wikis.
		'-wmgUseGlobalAbuseFilters' => [
			'default' => true,
		],

		// T39852
		'wmgUseWikimediaShopLink' => [
			'default'    => false,
			'enwiki'     => true,
			'simplewiki' => true,
		],

		'wgWMEDesktopWebUIActionsTracking' => [
			'default' => 1,
		],

		'wgWMEPageSchemaSplitTestSamplingRatio' => [
			'default' => 1,
		],

		'wgWMESessionTick' => [
			'default' => true,
		],

		// Enable Mediawiki client side (browser) Javascript error logging.
		// This is the publicly accessible endpoint for eventgate-logging-external.
		'wgWMEClientErrorIntakeURL' => [
			'default' => 'https://intake-logging.wikimedia.beta.wmflabs.org/v1/events?hasty=true'
		],

		'wgWMEMobileWebUIActionsTracking' => [
			'default' => 1, // T294738
			'enwiki' => 1, // T346106
		],

		// T353388
		'wgMFUseDesktopSpecialHistoryPage' => [
			'default' => [
				'base' => true,
				'amc' => true,
			],
		],
		// T350181
		'wgMFUseDesktopDiffPage' => [
			'default' => [
				'base' => true,
				'amc' => true,
			],
		],

		'wgMFAmcOutreach' => [
			'default' => true
		],

		'wgMFAmcOutreachMinEditCount' => [
			'default' => 0
		],

		# Do not run any A/B tests on beta cluster (T206179)
		'-wgMinervaABSamplingRate' => [
			'default' => 0,
		],

		//
		//
		// Skins
		//
		//
		'wgDefaultSkin' => [
			'default' => 'vector-2022',
		],
		'wgSkipSkins' => [
			'default' => [ 'cologneblue', 'contenttranslation', 'modern' ], // T263093, T287616, T223824
		],

		// ResourceLoader
		'wgResourceLoaderClientPreferences' => [
			'default' => true,
		],

		'-wmgCentralAuthCookieDomain' => [
			'default' => '',
			// wiki families
			'wikipedia' => '.wikipedia.beta.wmflabs.org',
			'wikibooks' => '.wikibooks.beta.wmflabs.org',
			'wikinews' => '.wikinews.beta.wmflabs.org',
			'wikiquote' => '.wikiquote.beta.wmflabs.org',
			'wikisource' => '.wikisource.beta.wmflabs.org',
			'wikiversity' => '.wikiversity.beta.wmflabs.org',
			'wikivoyage' => '.wikivoyage.beta.wmflabs.org',
			'wiktionary' => '.wiktionary.beta.wmflabs.org',
			// wikis which are not Wikipedias but use *.wikipedia.beta.wmflabs.org
			'testwiki' => '.wikipedia.beta.wmflabs.org',
		],

		'-wmgCentralAuthAutoLoginWikis' => [
			'default' => [
				'.wikipedia.beta.wmflabs.org' => 'enwiki',
				'.wikibooks.beta.wmflabs.org' => 'enwikibooks',
				'.wikinews.beta.wmflabs.org' => 'enwikinews',
				'.wikiquote.beta.wmflabs.org' => 'enwikiquote',
				'.wikisource.beta.wmflabs.org' => 'enwikisource',
				'.wikiversity.beta.wmflabs.org' => 'enwikiversity',
				'.wikivoyage.beta.wmflabs.org' => 'enwikivoyage',
				'.wiktionary.beta.wmflabs.org' => 'enwiktionary',
				'api.wikimedia.beta.wmflabs.org' => 'apiportalwiki',
				'commons.wikimedia.beta.wmflabs.org' => 'commonswiki',
				'meta.wikimedia.beta.wmflabs.org' => 'metawiki',
				'wikidata.beta.wmflabs.org' => 'wikidatawiki',
				'wikifunctions.beta.wmflabs.org' => 'wikifunctionswiki',
			],
		],

		//
		// Vector
		//
		// Skin versions are strings not numbers. See skins/Vector/skin.json.
		'wgVectorLanguageInMainPageHeader' => [
			'default' => [
				'logged_in' => true,
				'logged_out' => true
			],
		],
		'wgVectorZebraDesign' => [
			'default' => [
				'logged_in' => true,
				'logged_out' => true,
			]
		],
		'wgVectorClientPreferences' => [
			'default' => [
				'logged_in' => false,
				'logged_out' => false,
				'beta' => true,
			],
		],
		'wgVectorPromoteAddTopic' => [
			'default' => true
		],
		'wmgCommonsMetadataForceRecalculate' => [
			'default' => true,
		],

		///
		/// ----------- BetaFeatures start ----------
		///

		// Enable all Beta Features in Beta Labs, even if not in production allow list
		'wgBetaFeaturesAllowList' => [
			'default' => false,
		],

		'wgVisualEditorRebaserURL' => [
			'default' => false,
			'metawiki' => 'https://visualeditor-realtime.wmflabs.org/',
		],

		///
		/// ------------ BetaFeatures end -----------
		///

		// https://www.mediawiki.org/wiki/Edit_check
		'wgVisualEditorEditCheck' => [
			'default' => false,
			// T345658
			'enwiki' => true,
			'frwiki' => true,
		],

		'wgVisualEditorEditCheckTagging' => [
			'default' => false,
			// T345658
			'enwiki' => true,
			'frwiki' => true,
		],

		// Whether to enable true section editing. false, true, 'mobile', or 'mobile-ab'
		'wmgVisualEditorEnableVisualSectionEditing' => [
			'default' => 'mobile',
		],

		'wmgUseReportIncident' => [
			'default' => true,
			'loginwiki' => false,
		],

		'wgReportIncidentRecipientEmails' => [
			'default' => [ 'incident-report-system-beta@wikimedia.org' ]
		],

		'wgReportIncidentEmailFromAddress' => [
			// Same as $wgPasswordSender in CommonSettings-labs.php
			'default' => 'wiki@wikimedia.beta.wmflabs.org'
		],

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

		'wgSearchSuggestCacheExpiry' => [
			'default' => 300,
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

		'wmgExtraLanguageNames' => [
			'default' => [ 'en-rtl' => 'English (rtl)' ],
			'wikidata' => [],
			'commonswiki' => [
				'smn' => 'anarâškielâ', // T222309
				'sms' => 'sääʹmǩiõll', // T222309
			],
		],

		'wgQuickSurveysConfig' => [
			'default' => [
				'internal example' => [
					'name' => 'internal example survey',
					'type' => 'internal',
					'layout' => 'single-answer',
					'question' => 'ext-quicksurveys-example-internal-survey-question',
					'answers' => [
						'ext-quicksurveys-example-internal-survey-answer-positive',
						'ext-quicksurveys-example-internal-survey-answer-neutral',
						'ext-quicksurveys-example-internal-survey-answer-negative',
					],
					'enabled' => true,
					'coverage' => 0,
					'platforms' => [
						'desktop' => [ 'stable' ],
						'mobile' => [ 'stable', 'beta' ],
					],
				],
				'internal example with freeform text' => [
					'name' => 'internal example survey with description and freeform text',
					'type' => 'internal',
					'layout' => 'single-answer',
					'question' => 'ext-quicksurveys-example-internal-survey-question',
					'answers' => [
						'ext-quicksurveys-example-internal-survey-answer-positive',
						'ext-quicksurveys-example-internal-survey-answer-neutral',
						'ext-quicksurveys-example-internal-survey-answer-negative',
					],
					'description' => 'ext-quicksurveys-example-internal-survey-description',
					'freeformTextLabel' => 'ext-quicksurveys-example-internal-survey-freeform-text-label',
					'enabled' => true,
					'coverage' => 0,
					'platforms' => [
						'desktop' => [ 'stable' ],
						'mobile' => [ 'stable', 'beta' ],
					],
				],
				'internal example with multiple ansers' => [
					'name' => 'internal multiple answer example survey',
					'type' => 'internal',
					'layout' => 'multiple-answer',
					'question' => 'ext-quicksurveys-example-internal-survey-question',
					'answers' => [
						'ext-quicksurveys-example-internal-survey-answer-positive',
						'ext-quicksurveys-example-internal-survey-answer-neutral',
						'ext-quicksurveys-example-internal-survey-answer-negative',
					],
					'enabled' => true,
					'coverage' => 0,
					'platforms' => [
						'desktop' => [ 'stable' ],
						'mobile' => [ 'stable', 'beta' ],
					],
					'shuffleAnswersDisplay' => true,
				],
				'internal example with multiple answers and freeform text' => [
					'name' => 'internal multiple answer example survey with description and freeform text',
					'type' => 'internal',
					'layout' => 'multiple-answer',
					'question' => 'ext-quicksurveys-example-internal-survey-question',
					'answers' => [
						'ext-quicksurveys-example-internal-survey-answer-positive',
						'ext-quicksurveys-example-internal-survey-answer-neutral',
						'ext-quicksurveys-example-internal-survey-answer-negative',
					],
					'description' => 'ext-quicksurveys-example-internal-survey-description',
					'freeformTextLabel' => 'ext-quicksurveys-example-internal-survey-freeform-text-label',
					'enabled' => true,
					'coverage' => 0,
					'platforms' => [
						'desktop' => [ 'stable' ],
						'mobile' => [ 'stable', 'beta' ],
					],
					'shuffleAnswersDisplay' => true,
				],
				'external example' => [
					'name' => 'external example survey',
					'type' => 'external',
					'question' => 'ext-quicksurveys-example-external-survey-question',
					'description' => 'ext-quicksurveys-example-external-survey-description',
					'link' => 'ext-quicksurveys-example-external-survey-link',
					'privacyPolicy' => 'ext-quicksurveys-example-external-survey-privacy-policy',
					'coverage' => 0,
					'enabled' => true,
					'platforms' => [
						'desktop' => [ 'stable' ],
						'mobile' => [ 'stable', 'beta' ],
					],
				],
			],
			'+enwiki' => [
				'tst-safety-survey' => [
					// T293798
					'name' => 'tst-safety-survey',
					'type' => 'internal',
					'layout' => 'single-answer',
					'question' => 'ext-quicksurveys-tst-internal-survey-question',
					'answers' => [
						'ext-quicksurveys-tst-internal-survey-answer-positive',
						'ext-quicksurveys-tst-internal-survey-answer-negative'
					],
					'audience' => [
						// User:EssexIgyan/sandbox & User:Erayfield/sandbox
						'pageIds' => [ 265894, 265895 ]
					],
					'privacyPolicy' => 'ext-quicksurveys-example-external-survey-privacy-policy',
					'enabled' => true,
					'coverage' => 1,
					'platforms' => [
						'desktop' => [ 'stable' ],
						'mobile' => [ 'stable' ]
					],
				],
				// T294363: QA internal survey custom confirmation and additional info
				[
					'name' => 'T294363-1',
					'type' => 'internal',
					'layout' => 'single-answer',
					'question' => 'ext-quicksurveys-tst-internal-survey-question',
					'answers' => [
						'ext-quicksurveys-tst-internal-survey-answer-positive',
						'ext-quicksurveys-tst-internal-survey-answer-negative',
					],
					'shuffleAnswersDisplay' => true,
					'privacyPolicy' => 'ext-quicksurveys-tst-internal-survey-privacy-policy',
					'additionalInfo' => 'ext-quicksurveys-tst-internal-survey-additional-info',
					'confirmMsg' => 'ext-quicksurveys-tst-internal-survey-confirm-msg',
					'enabled' => true,
					'coverage' => 0,
					'platforms' => [
						'desktop' => [ 'stable' ],
						'mobile' => [ 'stable' ]
					],
				],
				// T294363: QA internal survey custom confirmation
				[
					'name' => 'T294363-2',
					'type' => 'internal',
					'layout' => 'single-answer',
					'question' => 'ext-quicksurveys-tst-internal-survey-question',
					'answers' => [
						'ext-quicksurveys-tst-internal-survey-answer-positive',
						'ext-quicksurveys-tst-internal-survey-answer-negative',
					],
					'shuffleAnswersDisplay' => true,
					'privacyPolicy' => 'ext-quicksurveys-tst-internal-survey-privacy-policy',
					'confirmMsg' => 'ext-quicksurveys-tst-internal-survey-confirm-msg',
					'enabled' => true,
					'coverage' => 0,
					'platforms' => [
						'desktop' => [ 'stable' ],
						'mobile' => [ 'stable' ]
					],
				],
				// T294363: QA internal survey additional info
				[
					'name' => 'T294363-3',
					'type' => 'internal',
					'layout' => 'single-answer',
					'question' => 'ext-quicksurveys-tst-internal-survey-question',
					'answers' => [
						'ext-quicksurveys-tst-internal-survey-answer-positive',
						'ext-quicksurveys-tst-internal-survey-answer-negative',
					],
					'shuffleAnswersDisplay' => true,
					'privacyPolicy' => 'ext-quicksurveys-tst-internal-survey-privacy-policy',
					'additionalInfo' => 'ext-quicksurveys-tst-internal-survey-additional-info',
					'enabled' => true,
					'coverage' => 0,
					'platforms' => [
						'desktop' => [ 'stable' ],
						'mobile' => [ 'stable' ]
					],
				],
				// T294363: QA internal survey
				[
					'name' => 'T294363-4',
					'type' => 'internal',
					'layout' => 'single-answer',
					'question' => 'ext-quicksurveys-tst-internal-survey-question',
					'answers' => [
						'ext-quicksurveys-tst-internal-survey-answer-positive',
						'ext-quicksurveys-tst-internal-survey-answer-negative',
					],
					'shuffleAnswersDisplay' => true,
					'privacyPolicy' => 'ext-quicksurveys-tst-internal-survey-privacy-policy',
					'enabled' => true,
					'coverage' => 0,
					'platforms' => [
						'desktop' => [ 'stable' ],
						'mobile' => [ 'stable' ]
					],
				],
				// T294363: QA external survey custom confirmation and additional info
				[
					'name' => 'T294363-5',
					'type' => 'external',
					'question' => 'ext-quicksurveys-example-external-survey-question',
					'description' => 'ext-quicksurveys-example-external-survey-description',
					'link' => 'ext-quicksurveys-example-external-survey-link',
					'instanceTokenParameterName' => 'parameterName',
					'privacyPolicy' => 'ext-quicksurveys-tst-internal-survey-privacy-policy',
					'additionalInfo' => 'ext-quicksurveys-tst-internal-survey-additional-info',
					'confirmMsg' => 'ext-quicksurveys-tst-internal-survey-confirm-msg',
					'enabled' => true,
					'coverage' => 0,
					'platforms' => [
						'desktop' => [ 'stable' ],
						'mobile' => [ 'stable' ]
					],
				],
				// T294363: QA external survey custom confirmation
				[
					'name' => 'T294363-6',
					'type' => 'external',
					'question' => 'ext-quicksurveys-example-external-survey-question',
					'description' => 'ext-quicksurveys-example-external-survey-description',
					'link' => 'ext-quicksurveys-example-external-survey-link',
					'instanceTokenParameterName' => 'parameterName',
					'privacyPolicy' => 'ext-quicksurveys-tst-internal-survey-privacy-policy',
					'confirmMsg' => 'ext-quicksurveys-tst-internal-survey-confirm-msg',
					'enabled' => true,
					'coverage' => 0,
					'platforms' => [
						'desktop' => [ 'stable' ],
						'mobile' => [ 'stable' ]
					],
				],
				// T294363: QA external survey additional info
				[
					'name' => 'T294363-7',
					'type' => 'external',
					'question' => 'ext-quicksurveys-example-external-survey-question',
					'description' => 'ext-quicksurveys-example-external-survey-description',
					'link' => 'ext-quicksurveys-example-external-survey-link',
					'instanceTokenParameterName' => 'parameterName',
					'privacyPolicy' => 'ext-quicksurveys-tst-internal-survey-privacy-policy',
					'additionalInfo' => 'ext-quicksurveys-tst-internal-survey-additional-info',
					'enabled' => true,
					'coverage' => 0,
					'platforms' => [
						'desktop' => [ 'stable' ],
						'mobile' => [ 'stable' ]
					],
				],
				// T294363: QA external survey
				[
					'name' => 'T294363-8',
					'type' => 'external',
					'question' => 'ext-quicksurveys-example-external-survey-question',
					'description' => 'ext-quicksurveys-example-external-survey-description',
					'link' => 'ext-quicksurveys-example-external-survey-link',
					'instanceTokenParameterName' => 'parameterName',
					'privacyPolicy' => 'ext-quicksurveys-tst-internal-survey-privacy-policy',
					'enabled' => true,
					'coverage' => 0,
					'platforms' => [
						'desktop' => [ 'stable' ],
						'mobile' => [ 'stable' ]
					],
				],
			],
			'cawiki' => [
				// T187299
				[
					'enabled' => true,
					'type' => 'internal',
					'name' => 'perceived-performance-survey',
					'question' => 'ext-quicksurveys-performance-internal-survey-question',
					'answers' => [
						'ext-quicksurveys-example-internal-survey-answer-positive',
						'ext-quicksurveys-example-internal-survey-answer-neutral',
						'ext-quicksurveys-example-internal-survey-answer-negative',
					],
					'coverage' => 0.0,
					'platforms' => [
						'desktop' => [ 'stable' ],
					],
					'privacyPolicy' => 'ext-quicksurveys-performance-internal-survey-privacy-policy',
					'shuffleAnswersDisplay' => true,
				],
			],
			'metawiki' => [
				// T351353
				[
					'enabled' => true,
					'type' => 'external',
					'name' => 'annual-plan-core-metrics-feedback-2023',
					'question' => 'ext-quicksurveys-example-external-survey-question',
					'description' => 'ext-quicksurveys-example-external-survey-description',
					'coverage' => 1,
					'audience' => [
						'anons' => false,
						// User:DDesouza/sandbox
						'pageIds' => [ 9475 ],
					],
					'platforms' => [
						'desktop' => [ 'stable' ],
						'mobile' => [ 'stable' ]
					],
					'link' => 'ext-quicksurveys-example-external-survey-link',
					'privacyPolicy' => 'ext-quicksurveys-example-external-survey-privacy-policy',
				],
			],
		],

		'-wgScorePath' => [
			'default' => "//upload.wikimedia.beta.wmflabs.org/score",
		],

		'-wgPhonosPath' => [
			'default' => "//upload.wikimedia.beta.wmflabs.org/phonos",
		],

		'wgRateLimits' => [
			'default' => [
				// T351299: Relax rate limiting on beta labs for community feedback period.
				'reportincident' => [
					'user' => [ 20, 86400 ],
					'newbie' => [ 10, 86400 ],
				],
			],
		],

		'wgRateLimitsExcludedIPs' => [
			'default' => [
				'198.73.209.0/24', // T87841 Office IP
				'162.222.72.0/21', // T126585 Sauce Labs IP range for browser tests
				'66.85.48.0/21', // also Sauce Labs
				'172.16.0.0/12', // browser tests run by Jenkins instances - T167432
			],
		],

		'wmgUseCapiunto' => [
			'default' => true,
		],

		'wmgUseCheckUser' => [
			'default' => false,
		],

		'wgSecurePollUseLogging' => [
			'default' => true,
		],

		'wgSecurePollSingleTransferableVoteEnabled' => [
			'default' => true,
			'eswiki' => false,
		],

		'wmgUseIPInfo' => [
			'default' => true,
			'loginwiki' => false,
		],

		'wgIPInfoGeoLite2Prefix' => [
			'default' => '/usr/share/GeoIP/GeoLite2-',
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

		'wmgUseEntitySchema' => [
			'default' => false,
			'wikidatawiki' => true,
		],

		'wgEntitySchemaEnableDatatype' => [
			'default' => false,
			'wikidatawiki' => true, // T332725
		],

		'wgLexemeLanguageCodePropertyId' => [
			'default' => null,
			'wikidatawiki' => 'P218',
		],

		'wgLexemeLexicalCategoryItemIds' => [
			'default' => null,
			'wikidatawiki' => [
				'Q593111', // noun
				'Q487091', // adjective
				'Q486787', // adverb
			],
		],

		'-wmgWikibaseSearchIndexProperties' => [
			'default' => []
		],

		'-wmgWikibaseSearchIndexPropertiesExclude' => [
			'default' => []
		],

		'wmgWikibaseTmpItemTermsMigrationStage' => [
			'default' => [
				'max' => MIGRATION_NEW,
			],
			'wikidatawiki' => [
				'max' => MIGRATION_NEW,
			],
		],

		'-wgWBCSStatementBoost' => [
			'default' => []
		],
		'-wgWBCSLanguageSelectorStatementBoost' => [
			'default' => []
		],

		'-wmgWikibaseDisabledDataTypes' => [
			'default' => []
		],

		// For better testing of Structured Data on Commons edits
		'wmgEnhancedRecentChanges' => [
			'commonswiki' => true,
		],

		// … and $wgUploadWizardConfig
		'wmgMediaInfoEnableUploadWizardStatements' => [
			'commonswiki' => true,
		],

		// Depicts-related Beta Cluster Commons configuration
		'wgMediaInfoProperties' => [
			'commonswiki' => [
				'depicts' => 'P245962',
			]
		],

		'wgMediaInfoExternalEntitySearchBaseUri' => [
			'commonswiki' => 'https://wikidata.beta.wmflabs.org/w/api.php',
		],

		'wgMediaInfoHelpUrls' => [
			'commonswiki' => [
				'P245962' => 'https://commons.wikimedia.org/wiki/Special:MyLanguage/Commons:Depicts',
			],
		],

		// Extension for Special:MediaSearch
		'wmgUseMediaSearch' => [
			'commonswiki' => true,
		],

		'wgMediaSearchExternalEntitySearchBaseUri' => [
			'commonswiki' => 'https://wikidata.beta.wmflabs.org/w/api.php',
		],

		// switch 'audio' and 'video' mediasearch tabs for testing
		'wgMediaSearchTabOrder'  => [
			'commonswiki' => [ 'image', 'video', 'audio', 'other', 'page' ]
		],

		'wgSearchMatchRedirectPreference' => [
			'commonswiki' => true,
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
			'default' => true, // T127297
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

		// The LOWER the value, the MORE requests will be logged.
		// 100 = 1 of every 100 (1%), 1 = 1 of every 1 (100%).
		'wgNavigationTimingSamplingFactor' => [
			// Beta Cluster: 10% of page loads
			'default' => 10,
		],

		'wmgUseFileImporter' => [
			'default' => false,
			'commonswiki' => 'FileImporter-WikimediaSitesTableSite',
			'testwiki' => 'FileImporter-Site-DefaultMediaWiki',
		],

		'wmgUseFileExporter' => [
			'default' => false,
			'metawiki' => true,
			'wikipedia' => true,
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

		'wmgEnableDashikiData' => [
			'default' => true,
		],

		'wgCognateReadOnly' => [
			'default' => false,
		],

		'wgReadingListsCluster' => [
			'default' => false,
		],
		'wgReadingListsDatabase' => [
			'default' => 'metawiki',
		],
		'wgReadingListsWebAuthenticatedPreviews' => [
			'default' => true,
		],
		'wgReadingListsAnonymizedPreviews' => [
			'default' => false,
		],

		'-wgPageCreationLog' => [
			'default' => true, // T196400
		],

		'wgDisambiguatorNotifications' => [
			'default' => true, // T291303
		],

		// ***************************************************
		// Cirrus-related tweaks specific for the Beta cluster
		// ***************************************************

		// Use a constant MLR model for all wikis. It's not ideal, but
		// no models were trained specifically for data in labs anyways.
		'-wmgCirrusSearchMLRModel' => [
			'default' => [ 'model' => '20171023_enwiki_v1' ],
		],

		'-wgCirrusSearchFallbackProfile' => [
			'default' => 'phrase_suggest',
		],

		'wgCirrusSearchEnableCrossProjectSearch' => [
			'enwiki' => true,
		],

		# We don't have enough nodes to support these settings in beta so just turn
		# them off.
		'-wgCirrusSearchMaxShardsPerNode' => [
			'default' => []
		],

		# nothing in beta cluster is large enough to need multiple shards
		'-wmgCirrusSearchShardCount' => [
			// Most wikis are too small to be worth sharding
			'default' => [ 'content' => 1, 'general' => 1, 'titlesuggest' => 1, 'archive' => 1, 'file' => 1 ],
		],

		# Override prod configuration, there is only one cluster in beta
		'-wgCirrusSearchDefaultCluster' => [
			'default' => 'eqiad'
		],

		# Don't specially configure cluster for more like queries in beta
		'-wgCirrusSearchClusterOverrides' => [
			'default' => []
		],

		# write to all configured clusters, there should only be one in labs
		'-wgCirrusSearchWriteClusters' => [
			'default' => [ 'eqiad' ],
		],

		'-wgCirrusSearchLanguageToWikiMap' => [
			'default' => [
				'ar' => 'ar',
				'de' => 'de',
				'en' => 'en',
				'es' => 'es',
				'fa' => 'fa',
				'he' => 'he',
				'hi' => 'hi',
				'ja' => 'ja',
				'ko' => 'ko',
				'ru' => 'ru',
				'sq' => 'sq',
				'uk' => 'uk',
				'zh-cn' => 'zh',
				'zh-tw' => 'zh',
			]
		],

		# Force the number of replicas to 1 max for the beta cluster
		'-wgCirrusSearchReplicas' => [
			'default' => '0-1'
		],

		// Restore to default behaviour, unlike production config
		'-wgCirrusSearchPrivateClusters' => [
			'default' => null
		],

		'-wgTemplateLinksSchemaMigrationStage' => [
			'default' => SCHEMA_COMPAT_WRITE_NEW | SCHEMA_COMPAT_READ_NEW,
		],

		'-wgExternalLinksSchemaMigrationStage' => [
			'default' => SCHEMA_COMPAT_WRITE_NEW | SCHEMA_COMPAT_READ_NEW,
		],

		'-wgPageLinksSchemaMigrationStage' => [
			'default' => SCHEMA_COMPAT_WRITE_BOTH | SCHEMA_COMPAT_READ_NEW,
			'fawiki' => SCHEMA_COMPAT_WRITE_NEW | SCHEMA_COMPAT_READ_NEW,
			'simplewiki' => SCHEMA_COMPAT_WRITE_NEW | SCHEMA_COMPAT_READ_NEW,
		],

		'-wgAbuseFilterActorTableSchemaMigrationStage' => [
			'default' => SCHEMA_COMPAT_WRITE_BOTH | SCHEMA_COMPAT_READ_NEW,
		],

		'-wgAbuseFilterEnableBlockedExternalDomain' => [
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
				'revertrisklanguageagnostic' => [ 'enabled' => true ],
			],
			'wikidatawiki' => [
				'damaging' => [ 'enabled' => true ],
				'goodfaith' => [ 'enabled' => true ],
				'reverted' => [ 'enabled' => false ],
				'itemquality' => [ 'enabled' => true, 'namespaces' => [ 0 ], 'cleanParent' => true ],
			],
		],
		'wgOresExcludeBots' => [
			'default' => true,
			'enwiki' => false,
		],
		'wgOresModelClasses' => [
			'wikidatawiki' => [
				'itemquality' => [
					'A' => 5,
					'B' => 4,
					'C' => 3,
					'D' => 2,
					'E' => 1
				],
			],
		],
		'-wgOresUseLiftwing' => [
			'default' => true
		],
		'-wgOresLiftWingAddHostHeader' => [
			'default' => false
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
		'wmgEnablePageTriage' => [
			'zhwiki' => true, // T323378
		],
		'wgPageTriageEnableCurationToolbar' => [
			'zhwiki' => true, // T323378
		],
		'wgPageTriageDraftNamespaceId' => [
			'zhwiki' => 118, // T323378
		],
		'wgPageTriageEnableCopyvio' => [
			'zhwiki' => true, // T323378
		],
		'wgPageTriageEnableExtendedFeatures' => [
			'default' => false, // T321922
			'test2wiki' => true,
			'enwiki' => true,
		],
		'-wmgWikibaseCachePrefix' => [
			'default' => 'wikidatawiki',
			'commonswiki' => 'commonswiki',
		],
		'wgGELevelingUpFeaturesEnabled' => [
			'default' => true,
		],
		'wgGEUseNewImpactModule' => [
			'default' => true,
		],
		'wgGERefreshUserImpactDataMaintenanceScriptEnabled' => [
			'default' => true,
		],
		'wgGEImageRecommendationApiHandler' => [
			// Can be changed to 'production' when T306349 is resolved.
			'default' => 'actionapi',
		],
		// $wgGEImageRecommendationServiceAccessToken is in private/PrivateSettings.php
		'-wgGEDatabaseCluster' => [
			'default' => false,
		],
		'wgGEDeveloperSetup' => [
			'default' => true,
		],
		'wgGENewcomerTasksImageRecommendationsEnabled' => [
			'default' => true,
		],
		'wgGENewcomerTasksSectionImageRecommendationsEnabled' => [
			'default' => true,
		],
		'wgGENewcomerTasksLinkRecommendationsEnabled' => [
			'enwiki' => true,
		],
		'wgGELinkRecommendationsFrontendEnabled' => [
			'default' => true,
		],
		'wgGEImageRecommendationServiceUseTitles' => [
			'default' => true,
		],
		'wgGEHomepageDefaultVariant' => [
			'default' => 'control',
		],
		'wgGEHomepageNewAccountVariantsByPlatform' => [
			'default' => [
				'control' => [
					'mobile' => 50,
					'desktop' => 50,
				],
				'oldimpact' => [
					'mobile' => 50,
					'desktop' => 50
				]
			]
		],
		'wgGELinkRecommendationServiceTimeout' => [
			'default' => 30,
		],
		'wgGELinkRecommendationsUseEventGate' => [
			'default' => false,
		],
		'wgGELevelingUpGetStartedNotificationSendAfterSeconds' => [
			'default' => 300,
		],
		'wgGELevelingUpKeepGoingNotificationSendAfterSeconds' => [
			'default' => 300,
		],
		'wgGEHelpPanelHelpDeskTitle' => [
			'enwiki' => 'Wikipedia:Help_desk',
		],
		'wgGEHelpPanelViewMoreTitle' => [
			'enwiki' => 'Help:Contents',
		],
		'wgGEHelpPanelSearchForeignAPI' => [
			'default' => 'https://en.wikipedia.org/w/api.php',
			'arwiki' => 'https://ar.wikipedia.org/w/api.php',
			'bnwiki' => 'https://bn.wikipedia.org/w/api.php',
			'cswiki' => 'https://cs.wikipedia.org/w/api.php',
			'kowiki' => 'https://ko.wikipedia.org/w/api.php',
			'srwiki' => 'https://sr.wikipedia.org/w/api.php',
			'viwiki' => 'https://vi.wikipedia.org/w/api.php',
		],
		'-wgGEMentorDashboardEnabledModules' => [
			'default' => [
				'mentee-overview' => true,
				'mentor-tools' => true,
				'resources' => true,
				'personalized-praise' => true,
			],
		],
		'-wgGEPersonalizedPraiseNotificationsEnabled' => [
			'default' => true,
		],
		'-wgGEPersonalizedPraiseBackendEnabled' => [
			'default' => true,
		],
		'wgGENewcomerTasksRemoteApiUrl' => [
			'enwiki' => null,
			'arwiki' => null,
			'bnwiki' => null,
			'cswiki' => null,
			'fawiki' => 'https://fa.wikipedia.org/w/api.php',
			'kowiki' => 'https://ko.wikipedia.org/w/api.php',
			'srwiki' => 'https://sr.wikipedia.org/w/api.php',
			'viwiki' => null,
		],
		'wgWelcomeSurveyExperimentalGroups' => [
			'default' => [
				'control' => [
					'percentage' => 100,
				],
			],
			'arwiki' => [
				'control' => [
					'percentage' => 0,
				],
				'T342353_user_research' => [
					'percentage' => 100,
					'questions' => [
						'reason',
						'edited',
						'email',
						'languages',
						'user-research',
					],
				],
			],
			'enwiki' => [
				'control' => [
					'percentage' => 0,
				],
				'T342353_user_research' => [
					'percentage' => 100,
					'questions' => [
						'reason',
						'edited',
						'email',
						'languages',
						'user-research',
					],
				],
			],
			'eswiki' => [
				'control' => [
					'percentage' => 0,
				],
				'T342353_user_research' => [
					'percentage' => 100,
					'questions' => [
						'reason',
						'edited',
						'email',
						'languages',
						'user-research',
					],
				],
			],
		],
		'wgEnableSpecialMute' => [
			'default' => true,
			'eswiki' => false,
		],
		'wgEnablePartialActionBlocks' => [
			'default' => true,
			'dewiki' => false,
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
		'wgPropertySuggesterSchemaTreeUrl' => [
			'wikidatawiki' => 'https://recommender.wmcloud.org/recommender',
		],
		'wgPropertySuggesterABTestingState' => [
			'wikidatawiki' => true,
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
				'Q98651' => 'wb-badge-validated',
				'Q612803' => 'wb-badge-redirect-sitelink', // T313896
				'Q612804' => 'wb-badge-redirect-intentional-sitelink', // T313896
			],
		],
		'wmgWikibaseRepoRedirectBadgeItems' => [
			'wikidatawiki' => [
				'Q612803', // T313896
				'Q612804', // T313896
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
		'wgWikimediaBadgesTopicsMainCategoryProperty' => [
			'default' => 'P750',
			'commonswiki' => null,
		],
		'wgWikimediaBadgesCategoryRelatedToListProperty' => [
			'default' => 'P253133',
			'commonswiki' => null,
		],
		'wgWikimediaBadgesCommonsCategoryProperty' => [
			'default' => 'P725',
			'commonswiki' => null,
		],
		'-wmgWikibaseEntitySources' => [
			'default' => [
				'wikidata' => [
					'entityNamespaces' => [
						'item' => 0,
						'property' => 120,
						'lexeme' => 146,
					],
					'repoDatabase' => 'wikidatawiki',
					'baseUri' => 'https://wikidata.beta.wmflabs.org/entity/',
					'rdfNodeNamespacePrefix' => 'wd',
					'rdfPredicateNamespacePrefix' => '',
					'interwikiPrefix' => 'd',
				],
			],
			'commonswiki' => [
				'wikidata' => [
					'entityNamespaces' => [
						'item' => 0,
						'property' => 120,
						'lexeme' => 146,
					],
					'repoDatabase' => 'wikidatawiki',
					'baseUri' => 'https://wikidata.beta.wmflabs.org/entity/',
					'rdfNodeNamespacePrefix' => 'wd',
					'rdfPredicateNamespacePrefix' => '',
					'interwikiPrefix' => 'd',
				],
				'commons' => [
					'entityNamespaces' => [
						'mediainfo' => '6/mediainfo',
					],
					'repoDatabase' => 'commonswiki',
					'baseUri' => 'https://commons.wikimedia.beta.wmflabs.org/entity/',
					'rdfNodeNamespacePrefix' => 'sdc',
					'rdfPredicateNamespacePrefix' => 'sdc',
					'interwikiPrefix' => 'c',
				],
			],
		],
		'-wmgWikibaseRepoLocalEntitySourceName' => [
			'default' => 'wikidata',
			'commonswiki' => 'commons',
		],
		'-wmgWikibaseClientItemAndPropertySourceName' => [
			'default' => 'wikidata',
		],
		'-wmgWikibaseClientSpecialSiteLinkGroups' => [
			'default' => [
				'commons',
				'mediawiki',
				'meta',
				'species',
				'wikidata',
				'wikimania',
			],
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
		'-wgArticlePlaceholderImageProperty' => [
			'default' => 'P964',
		],
		'wmgWikibaseRepoEnableRefTabs' => [
			'wikidatawiki' => true,
		],
		'wmgWBRepoCanonicalUriProperty' => [
			'default' => null,
			'wikidata' => 'P174944',
		],
		'wmgWikibaseClientPropertyOrderUrl' => [
			'default' => null,
		],
		'-wmgWikibaseClientRepoUrl' => [
			'default' => 'https://wikidata.beta.wmflabs.org',
		],

		'-wmgWikibaseSSRTermboxServerUrl' => [
			'default' => null, // T304328
		],

		// T232191
		'wmgWikibaseTaintedReferencesEnabled' => [
			'default' => false,
			'wikidatawiki' => true,
		],

		// T302959
		'wmgWikibaseRestApiEnabled' => [
			'default' => false,
			'wikidatawiki' => true,
		],

		// T326313
		'wmgWikibaseRestApiDevelopmentEnabled' => [
			'default' => false,
			'wikidatawiki' => true,
		],

		// T235033
		'wmgWikibaseRepoDataBridgeEnabled' => [
			'default' => false,
			'wikidatawiki' => true,
		],

		// T226816
		'wmgWikibaseClientDataBridgeEnabled' => [
			'default' => true,
		],

		'wmgWikibaseClientWellKnownReferencePropertyIds' => [
			'default' => [
				'referenceUrl' => 'P709',
				'title' => 'P160020',
				'statedIn' => 'P764',
				'author' => 'P253075',
				'publisher' => 'P760',
				'publicationDate' => 'P766',
				'retrievedDate' => 'P761',
			],
		],

		'wmgWikibaseClientDataBridgeHrefRegExp' => [
			'default' => '^https://wikidata\.beta\.wmflabs\.org/wiki/((Q[1-9][0-9]*)).*#(P[1-9][0-9]*)$',
		],

		// T232582
		'wmgWikibaseClientDataBridgeEditTags' => [
			'default' => [ 'Data Bridge' ],
		],

		'wmgWikibaseEntityDataFormats' => [
			'default' => [
				'json',
				'php',
				'rdfxml',
				'n3',
				'turtle',
				'ntriples',
				'html',
				'jsonld',
			],
		],

		// If set to "false": No alternate links will be added to desktop pages,
		// and MobileFrontend won't add a canonical tag
		// If set to "true": Alternate link will be added, MF will add a canonical tag
		'wgMFNoindexPages' => [
			'default' => true
		],

		// Needed by browser tests for Minerva otherwise those will fail
		'wgMFDisplayWikibaseDescriptions' => [
			'enwiki' => [
				'search' => true, 'watchlist' => true, 'tagline' => true
			]
		],

		'wmgUseWikibaseQuality' => [
			'commonswiki' => true,
		],

		'wgWBQualityConstraintsEnableConstraintsCheckJobsRatio' => [
			'default' => 100, // 100% of edits trigger post edit job run constraint checks
		],

		// T209957 configure WikibaseQualityConstraints extension on beta
		// the default values are entity IDs on production Wikidata,
		// the custom values are corresponding entities on Beta Wikidata
		// (most of them were automatically imported from production Wikidata)
		'wgWBQualityConstraintsInstanceOfId' => [
			'wikibaserepo' => 'P694',
		],
		'wgWBQualityConstraintsSubclassOfId' => [
			'wikibaserepo' => 'P279',
		],
		'wgWBQualityConstraintsPropertyConstraintId' => [
			'wikibaserepo' => 'P241048',
		],
		'wgWBQualityConstraintsExceptionToConstraintId' => [
			'wikibaserepo' => 'P241049',
		],
		'wgWBQualityConstraintsConstraintStatusId' => [
			'wikibaserepo' => 'P241050',
		],
		'wgWBQualityConstraintsMandatoryConstraintId' => [
			'wikibaserepo' => 'Q505095',
		],
		'wgWBQualityConstraintsSuggestionConstraintId' => [
			'wikibaserepo' => 'Q524851',
		],
		'wgWBQualityConstraintsDistinctValuesConstraintId' => [
			'wikibaserepo' => 'Q505096',
		],
		'wgWBQualityConstraintsMultiValueConstraintId' => [
			'wikibaserepo' => 'Q505097',
		],
		'wgWBQualityConstraintsUsedAsQualifierConstraintId' => [
			'wikibaserepo' => 'Q505098',
		],
		'wgWBQualityConstraintsSingleValueConstraintId' => [
			'wikibaserepo' => 'Q505099',
		],
		'wgWBQualityConstraintsSymmetricConstraintId' => [
			'wikibaserepo' => 'Q505100',
		],
		'wgWBQualityConstraintsTypeConstraintId' => [
			'wikibaserepo' => 'Q505101',
		],
		'wgWBQualityConstraintsValueTypeConstraintId' => [
			'wikibaserepo' => 'Q505102',
		],
		'wgWBQualityConstraintsInverseConstraintId' => [
			'wikibaserepo' => 'Q505103',
		],
		'wgWBQualityConstraintsItemRequiresClaimConstraintId' => [
			'wikibaserepo' => 'Q505104',
		],
		'wgWBQualityConstraintsValueRequiresClaimConstraintId' => [
			'wikibaserepo' => 'Q505105',
		],
		'wgWBQualityConstraintsConflictsWithConstraintId' => [
			'wikibaserepo' => 'Q505106',
		],
		'wgWBQualityConstraintsOneOfConstraintId' => [
			'wikibaserepo' => 'Q505107',
		],
		'wgWBQualityConstraintsMandatoryQualifierConstraintId' => [
			'wikibaserepo' => 'Q505108',
		],
		'wgWBQualityConstraintsAllowedQualifiersConstraintId' => [
			'wikibaserepo' => 'Q505109',
		],
		'wgWBQualityConstraintsRangeConstraintId' => [
			'wikibaserepo' => 'Q505110',
		],
		'wgWBQualityConstraintsDifferenceWithinRangeConstraintId' => [
			'wikibaserepo' => 'Q505111',
		],
		'wgWBQualityConstraintsCommonsLinkConstraintId' => [
			'wikibaserepo' => 'Q505112',
		],
		'wgWBQualityConstraintsContemporaryConstraintId' => [
			'wikibaserepo' => 'Q505113',
		],
		'wgWBQualityConstraintsFormatConstraintId' => [
			'wikibaserepo' => 'Q505114',
		],
		'wgWBQualityConstraintsUsedForValuesOnlyConstraintId' => [
			'wikibaserepo' => 'Q505115',
		],
		'wgWBQualityConstraintsUsedAsReferenceConstraintId' => [
			'wikibaserepo' => 'Q505116',
		],
		'wgWBQualityConstraintsNoBoundsConstraintId' => [
			'wikibaserepo' => 'Q505117',
		],
		'wgWBQualityConstraintsAllowedUnitsConstraintId' => [
			'wikibaserepo' => 'Q505118',
		],
		'wgWBQualityConstraintsSingleBestValueConstraintId' => [
			'wikibaserepo' => 'Q505119',
		],
		'wgWBQualityConstraintsAllowedEntityTypesConstraintId' => [
			'wikibaserepo' => 'Q505120',
		],
		'wgWBQualityConstraintsCitationNeededConstraintId' => [
			'wikibaserepo' => 'Q505121',
		],
		'wgWBQualityConstraintsPropertyScopeConstraintId' => [
			'wikibaserepo' => 'Q505122',
		],
		'wgWBQualityConstraintsClassId' => [
			'wikibaserepo' => 'P241051',
		],
		'wgWBQualityConstraintsRelationId' => [
			'wikibaserepo' => 'P241052',
		],
		'wgWBQualityConstraintsInstanceOfRelationId' => [
			'wikibaserepo' => 'Q505123',
		],
		'wgWBQualityConstraintsSubclassOfRelationId' => [
			'wikibaserepo' => 'Q505124',
		],
		'wgWBQualityConstraintsInstanceOrSubclassOfRelationId' => [
			'wikibaserepo' => 'Q505125',
		],
		'wgWBQualityConstraintsPropertyId' => [
			'wikibaserepo' => 'P694',
		],
		'wgWBQualityConstraintsQualifierOfPropertyConstraintId' => [
			'wikibaserepo' => 'P241054',
		],
		'wgWBQualityConstraintsMinimumQuantityId' => [
			'wikibaserepo' => 'P241055',
		],
		'wgWBQualityConstraintsMaximumQuantityId' => [
			'wikibaserepo' => 'P241056',
		],
		'wgWBQualityConstraintsMinimumDateId' => [
			'wikibaserepo' => 'P241057',
		],
		'wgWBQualityConstraintsMaximumDateId' => [
			'wikibaserepo' => 'P241058',
		],
		'wgWBQualityConstraintsNamespaceId' => [
			'wikibaserepo' => 'P241059',
		],
		'wgWBQualityConstraintsFormatAsARegularExpressionId' => [
			'wikibaserepo' => 'P241060',
		],
		'wgWBQualityConstraintsSyntaxClarificationId' => [
			'wikibaserepo' => 'P241061',
		],
		'wgWBQualityConstraintsConstraintScopeId' => [
			'wikibaserepo' => 'P241062',
		],
		'wgWBQualityConstraintsSeparatorId' => [
			'wikibaserepo' => 'P241063',
		],
		'wgWBQualityConstraintsConstraintCheckedOnMainValueId' => [
			'wikibaserepo' => 'Q505126',
		],
		'wgWBQualityConstraintsConstraintCheckedOnQualifiersId' => [
			'wikibaserepo' => 'Q505127',
		],
		'wgWBQualityConstraintsConstraintCheckedOnReferencesId' => [
			'wikibaserepo' => 'Q505128',
		],
		'wgWBQualityConstraintsNoneOfConstraintId' => [
			'wikibaserepo' => 'Q505129',
		],
		'wgWBQualityConstraintsIntegerConstraintId' => [
			'wikibaserepo' => 'Q505130',
		],
		'wgWBQualityConstraintsWikibaseItemId' => [
			'wikibaserepo' => 'Q505131',
		],
		'wgWBQualityConstraintsWikibasePropertyId' => [
			'wikibaserepo' => 'Q505132',
		],
		'wgWBQualityConstraintsWikibaseLexemeId' => [
			'wikibaserepo' => 'Q505133',
		],
		'wgWBQualityConstraintsWikibaseFormId' => [
			'wikibaserepo' => 'Q505134',
		],
		'wgWBQualityConstraintsWikibaseSenseId' => [
			'wikibaserepo' => 'Q505135',
		],
		'wgWBQualityConstraintsPropertyScopeId' => [
			'wikibaserepo' => 'P241064',
		],
		'wgWBQualityConstraintsAsMainValueId' => [
			'wikibaserepo' => 'Q505136',
		],
		'wgWBQualityConstraintsAsQualifiersId' => [
			'wikibaserepo' => 'Q505137',
		],
		'wgWBQualityConstraintsAsReferencesId' => [
			'wikibaserepo' => 'Q505138',
		],

		// T215684: Load WikibaseCirrusSearch on Beta Cluster
		'wmgUseWikibaseCirrusSearch' => [
			'wikidatawiki' => true,
			'commonswiki' => true,
		],
		// T215684: Enable WikibaseCirrusSearch on Beta Cluster
		'wmgNewWikibaseCirrusSearch' => [
			'wikidatawiki' => true,
			'commonswiki' => true,
		],

		// T216206: Enable WikibaseLexemeCirrusSearch on Beta Cluster
		'wmgUseWikibaseLexemeCirrusSearch' => [
			'wikidatawiki' => true,
		],

		// T216206: Enable WikibaseLexemeCirrusSearch on Beta Cluster
		'wmgNewWikibaseLexemeCirrusSearch' => [
			'wikidatawiki' => true,
		],

		'wgMusicalNotationEnableWikibaseDataType' => [
			'default' => false,
			'wikidatawiki' => true,
		],
		'wgScoreTrim' => [
			'default' => true
		],
		'wgWikibaseMusicalNotationLineWidthInches' => [
			'default' => 8,
			'wikidatawiki' => 3,
		],
		'wmgUseWikisource' => [
			'wikisource' => true,
		],
		'wgWikisourceWikibaseEditionProperty' => [
			'wikisource' => 'P253108'
		],
		'wgWikisourceWikibaseEditionOfProperty' => [
			'wikisource' => 'P253107'
		],
		'wgWikisourceEnableOcr' => [
			'wikisource' => true,
		],
		'wgWikisourceOcrUrl' => [
			'wikisource' => 'https://ocr-test.wmcloud.org',
		],
		'wgProofreadPageBookNamespaces' => [
			'wikisource' => [ NS_MAIN, NS_USER ]
		],
		'wgProofreadPageUseStatusChangeTags' => [
			'wikisource' => true,
		],
		'wgProofreadPageEnableEditInSequence' => [
			'wikisource' => true,
		],
		'wmgUseWikimediaEditorTasks' => [
			'default' => false,
			'wikidatawiki' => true,
			'commonswiki' => true,
		],
		'wgWikimediaEditorTasksUserCountsCluster' => [
			'default' => false,
		],
		'wgWikimediaEditorTasksUserCountsDatabase' => [
			'default' => false,
		],
		'wgWikimediaEditorTasksEnableEditStreaks' => [
			'default' => true,
		],
		'wgWikimediaEditorTasksEnableRevertCounts' => [
			'default' => true,
		],
		'wgWikimediaEditorTasksEnabledCounters' => [
			'default' => [
				[
					'class' => WikipediaAppDescriptionEditCounter::class,
					'counter_key' => 'app_description_edits',
				],
				[
					'class' => WikipediaAppCaptionEditCounter::class,
					'counter_key' => 'app_caption_edits',
				],
				[
					'class' => WikipediaAppImageDepictsEditCounter::class,
					'counter_key' => 'app_depicts_edits',
				],
			],
		],
		'wmgUseMachineVision' => [
			'default' => false,
			'commonswiki' => true,
		],
		'wgMachineVisionTestersOnly' => [
			'default' => false,
		],
		'wgMachineVisionShowUploadWizardCallToAction' => [
			'default' => true,
		],
		'wgMachineVisionNewUploadLabelingJobDelay' => [
			'default' => 0,
		],
		'wgMachineVisionRequestLabelsFromWikidataPublicApi' => [
			'default' => true,
		],
		// T319240
		'wgSpecialContributeSkinsEnabled' => [
			'default' => [ "minerva", "vector-2022" ],
		],
		'-wgSpecialSearchFormOptions' => [
			'wikidatawiki' => [ 'showDescriptions' => true ],
		],
		'wmgUseTheWikipediaLibrary' => [
			'default' => true,
		],
		'wgTwlEditCount' => [
			'default' => 100,
		],
		'wgTwlRegistrationDays' => [
			'default' => 1,
		],
		'wgWBCitoidFullRestbaseURL' => [
			'wikidatawiki' => 'https://en.wikipedia.beta.wmflabs.org/api/rest_',
		],

		'wgRestAPIAdditionalRouteFiles' => [
			'default' => [
				'includes/Rest/coreDevelopmentRoutes.json',
			],
		],

		'wgAllowRequiringEmailForResets' => [
			'default' => true,
		],

		'wmgUseCSP' => [
			'default' => true,
		],

		'-wgForceHTTPS' => [
			'default' => true,
		],

		// Domains that go in script-src and default-src for CSP.
		// This also includes the prod domains for the sole purpose
		// of allowing users to test gadgets at beta.
		// beta itself should never call prod.
		'wmgApprovedContentSecurityPolicyDomains' => [
			'default' => [
				'*.wikimedia.beta.wmflabs.org',
				'*.wikipedia.beta.wmflabs.org',
				'*.wikinews.beta.wmflabs.org',
				'*.wiktionary.beta.wmflabs.org',
				'*.wikibooks.beta.wmflabs.org',
				'*.wikiversity.beta.wmflabs.org',
				'*.wikisource.beta.wmflabs.org',
				// No multilingual wikisource on beta.
				'*.wikiquote.beta.wmflabs.org',
				// Unlike production, wikidata does not use www. on beta
				'wikidata.beta.wmflabs.org',
				'm.wikidata.beta.wmflabs.org',
				'*.wikivoyage.beta.wmflabs.org',
				'*.mediawiki.beta.wmflabs.org',
				'wikifunctions.beta.wmflabs.org',
				'm.wikifunctions.beta.wmflabs.org',

				// Prod domains, to allow easier gadget testing
				'*.wikimedia.org',
				'*.wikipedia.org',
				'*.wikinews.org',
				'*.wiktionary.org',
				'*.wikibooks.org',
				'*.wikiversity.org',
				'*.wikisource.org',
				'wikisource.org',
				'*.wikiquote.org',
				'*.wikidata.org',
				'*.wikifunctions.org',
				'*.wikivoyage.org',
				'*.mediawiki.org',

				// Special entry for VE-RealTime until it can be done properly
				'ws://visualeditor-realtime.wmflabs.org',

				// T322323, CX Server in Beta Cluster, recommendation service
				'cxserver-beta.wmcloud.org',
				'recommend.wmflabs.org',

			],
		],

		'-wgDiscussionToolsABTest' => [
		],

		'-wgDiscussionTools_visualenhancements' => [
			'default' => 'default',
		],

		'-wgDiscussionTools_visualenhancements_reply' => [
			'default' => 'default',
		],

		'-wgDiscussionTools_visualenhancements_pageframe' => [
			'default' => 'default',
		],

		'-wgDiscussionToolsEnablePermalinksBackend' => [
			'default' => true,
		],

		'-wgDiscussionToolsEnablePermalinksFrontend' => [
			'default' => true,
		],

		'wgWatchlistExpiry' => [
			'default' => true,
		],

		'wgAbuseFilterEmergencyDisableThreshold' => [
			'default' => [ 'default' => 0.05 ],
			'zhwiki' => [ 'default' => 0.25 ], // T230305
		],
		'wgAbuseFilterEmergencyDisableCount' => [
			'default' => [
				'default' => 2,
				'flow' => 50,
			],
			'zhwiki' => [
				'default' => 25,
				'flow' => 50,
			],
		],

		// T332521: Mark some global AbuseFilter actions as locally disabled
		'wgAbuseFilterLocallyDisabledGlobalActions' => [
			'default' => [
				'blockautopromote' => true,
				'block' => true,
				'rangeblock' => true,
			],
		],

		// T246493
		'wmgUseNearbyPages' => [
			'default' => true,
		],
		'wgNearbyPagesUrl' => [
			'default' => 'https://en.wikipedia.org/w/api.php',
			'wikidatawiki'  => 'https://www.wikidata.org/w/api.php',
			'testwikidatawiki' => 'https://www.wikidata.org/w/api.php',
		],

		// Deploy GlobalWatchlist to the beta cluster - T268181
		'wmgUseGlobalWatchlist' => [
			'default' => false,
			'metawiki' => true,
		],
		'wgGlobalWatchlistDevMode' => [
			'default' => true,
		],
		'wgGlobalWatchlistWikibaseSite' => [
			'default' => 'wikidata.beta.wmflabs.org',
		],

		// T253271 Don't deploy Cirrus AB tests to beta
		'wgCirrusSearchUserTesting' => [
			'default' => [],
		],

		// T123582
		'wgImagePreconnect' => [
			'default' => true,
		],

		'wmgUseStopForumSpam' => [
			'default' => true,
		],
		'wgSFSReportOnly' => [
			'default' => false,
		],
		'wmgWikibaseTmpSerializeEmptyListsAsObjects' => [
			'default' => true,
		],

		'wmgWikibaseTmpEnableMulLanguageCode' => [
			'default' => true,
		],

		// Temporary, added in T339104, to be removed in T330217
		'wmgWikibaseTmpAlwaysShowMulLanguageCode' => [
			'default' => null,
			'wikidatawiki' => true,
		],

		'wmgUseChessBrowser' => [
			'default' => true,
		],

		// T303004
		'wmgUseWikistories' => [
			'enwiki' => true,
		],

		// Wikistories discovery module config
		'wgWikistoriesDiscoveryMode' => [
			'default' => 'off',
			'enwiki' => 'beta',
		],

		// T294363: QA Surveys on enwiki beta
		'wmgUseQuickSurveys' => [
			'enwiki' => true,
			'metawiki' => true,
		],

		'-wgMultiShardSiteStats' => [
			'default' => false,
			'enwiki' => true,
		],

		'wmgUseImageSuggestions' => [
			'default' => false,
		],

		'wmgUseSearchVue' => [
			'default' => false,
			'enwiki' => true,
			'ruwiki' => true,
		],
		'wgQuickViewDataRepositoryApiBaseUri' => [
			'default' => 'https://wikidata.beta.wmflabs.org/w/api.php',
		],
		'wgQuickViewMediaRepositoryApiBaseUri' => [
			'default' => 'https://commons.wikimedia.beta.wmflabs.org/w/api.php',
		],
		'wgQuickViewMediaRepositorySearchUri' => [
			'default' => 'https://commons.wikimedia.beta.wmflabs.org/wiki/Special:MediaSearch?search=%s',
		],
		'wgQuickViewMediaRepositoryUri' => [
			'default' => 'https://commons.wikimedia.beta.wmflabs.org',
		],
		'wgQuickViewSearchFilterForQID' => [
			'default' => 'haswbstatement:P245962=%s',
		],

		// See T311752
		'wmgUseCampaignEvents' => [
			'default' => true,
			'loginwiki' => false,
		],
		'wgCampaignEventsEnableParticipantQuestions' => [
			'default' => true,
		],
		// T314294
		'-wmgUsePhonos' => [
			'default' => true, // T336763
		],

		// T332787
		'-wgPhonosInlineAudioPlayerMode' => [
			'default' => true, // T336763
		],

		'wmgUseVueTest' => [
			'default' => true,
		],

		// T299612
		'wgLinterWriteNamespaceColumnStage' => [
			'default' => true,
		],

		// T299612
		'wgLinterUseNamespaceColumnStage' => [
			'default' => true,
		],

		// T175177
		'wgLinterWriteTagAndTemplateColumnsStage' => [
			'default' => true,
		],

		// T175177
		'wgLinterUserInterfaceTagAndTemplateStage' => [
			'default' => true,
		],

		'wmgUseContentTranslation' => [
			'wikivoyage' => true, // T322325
		],

		'wgOATHAuthMultipleDevicesMigrationStage' => [
			'default' => SCHEMA_COMPAT_READ_NEW | SCHEMA_COMPAT_WRITE_BOTH,
		],

		// T334895 - testing
		'-wmgUseGraph' => [
			'default' => true,
		],
		'-wmgHideGraphTags' => [
			'default' => false,
		],
		'wmgEnableIPMasking' => [
			'default' => false,
			'loginwiki' => true,
			'cswiki' => true,
			'dewiki' => true,
			'commonswiki' => true, // T342067
			'wikidatawiki' => true, // T343980
			'fawiki' => true,
		],

		// T342858
		'-wgEnableEditRecovery' => [
			'default' => true,
		],

		// T61245
		'wmgUsePageNotice' => [
			'enwiktionary' => true,
		],

		// T341000 (just disable the prod value)
		'-wgExternalLinksDomainGaps' => [],

		'wmgUseParserMigration' => [
			'default' => true,
		],

		// T348487
		'-wgUrlShortenerEnableQrCode' => [
			'default' => true,
		],
	];
} # wmfGetOverrideSettings()
