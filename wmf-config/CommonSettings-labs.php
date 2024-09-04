<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

# This file holds common overrides specific to Beta Cluster.
# For per-wiki overrides, see InitialiseSettings-labs.php.
#
# This for BETA and MUST NOT be loaded for production.
#
# Effective load order:
# - multiversion
# - mediawiki/DefaultSettings.php
# - wmf-config/*Services.php
# - wmf-config/InitialiseSettings.php
# - wmf-config/InitialiseSettings-labs.php
# - wmf-config/CommonSettings.php
# - wmf-config/CommonSettings-labs.php [THIS FILE]
#
# Included from: wmf-config/CommonSettings.php.
#

use MediaWiki\Extension\Notifications\Push\PushNotifier;

// safe guard
if ( $wmgRealm == 'labs' ) {

	// temporary workaround for T349944
	$wgStatsdServer = false;

	$wmgAddWikiNotify = false;

	# Use a different address from the production one - T192686
	$wgPasswordSender = 'wiki@wikimedia.beta.wmflabs.org';

	# Use a different site name (T181908)
	$wgSitename = "Beta $wgSitename";

	// Password policies; see https://meta.wikimedia.org/wiki/Password_policy
	$wmgPrivilegedPolicy = [
		'MinimalPasswordLength' => [ 'value' => 10, 'suggestChangeOnLogin' => true ],
		'MinimumPasswordLengthToLogin' => [ 'value' => 1, 'suggestChangeOnLogin' => true ],
		'PasswordNotInCommonList' => [ 'value' => true, 'suggestChangeOnLogin' => true ],
	];
	foreach ( $wmgPrivilegedGroups as $group ) {
		// On non-SUL wikis this is the effective password policy. On SUL wikis, it will be overridden
		// in the PasswordPoliciesForUser hook, but still needed for Special:PasswordPolicies
		if ( $group === 'user' ) {
			// For e.g. private and fishbowl wikis; covers 'user' in password policies
			$group = 'default';
		}
		$wgPasswordPolicy['policies'][$group] = array_merge(
			$wgPasswordPolicy['policies'][$group] ?? [],
			$wmgPrivilegedPolicy
		);
	}

	$wgPasswordPolicy['policies']['default']['MinimalPasswordLength'] = [
		'value' => 8,
		'suggestChangeOnLogin' => false,
	];

	if ( $wmgUseCentralAuth ) {
		// Enforce password policy when users login on other wikis; also for sensitive global groups
		// FIXME does this just duplicate the global policy checks down in the main $wmgUseCentralAuth block?
		$wgHooks['PasswordPoliciesForUser'][] = static function ( User $user, array &$effectivePolicy ) use ( $wmgPrivilegedPolicy ) {
			$privilegedGroups = wmfGetPrivilegedGroups( $user );
			if ( $privilegedGroups ) {
				$effectivePolicy = UserPasswordPolicy::maxOfPolicies( $effectivePolicy, $wmgPrivilegedPolicy );
				if ( in_array( 'staff', $privilegedGroups, true ) ) {
					$effectivePolicy['MinimumPasswordLengthToLogin'] = [
						'value' => 10,
						'suggestChangeOnLogin' => true,
					];
				}
			}
			return true;
		};

		// Allows automatic account vanishing (for qualifying users)
		$wgCentralAuthAutomaticVanishPerformer = 'AccountVanishRequests';
		$wgCentralAuthRejectVanishUserNotification = 'AccountVanishRequests';

		// Configuration for guidance given to blocked users when requesting vanishing
		$wgCentralAuthBlockAppealWikidataIds = [ "Q13360396", "Q175291" ];
		$wgCentralAuthWikidataApiUrl = "https://www.wikidata.org/w/api.php";
		$wgCentralAuthFallbackAppealUrl = "https://en.wikipedia.org/wiki/Wikipedia:Appealing_a_block";
		$wgCentralAuthFallbackAppealTitle = "Wikipedia:Appealing a block";
	}

	$wgLocalVirtualHosts = [
		'wikipedia.beta.wmflabs.org',
		'wiktionary.beta.wmflabs.org',
		'wikibooks.beta.wmflabs.org',
		'wikiquote.beta.wmflabs.org',
		'wikinews.beta.wmflabs.org',
		'wikisource.beta.wmflabs.org',
		'wikiversity.beta.wmflabs.org',
		'wikivoyage.beta.wmflabs.org',
		'meta.wikimedia.beta.wmflabs.org',
		'commons.wikimedia.beta.wmflabs.org',
		'api.wikimedia.beta.wmflabs.org',
		'sso.wikimedia.beta.wmflabs.org',
		'wikifunctions.beta.wmflabs.org',

		// new wmcloud instances
		'wikipedia.beta.wmcloud.org',
		'wikivoyage.beta.wmcloud.org',
	];

	# Attempt to auto block users using faulty servers
	# See also http://www.us.sorbs.net/general/using.shtml
	$wgEnableDnsBlacklist = true;
	$wgDnsBlacklistUrls   = [
		'proxies.dnsbl.sorbs.net.',
	];

	if ( $wmgUseGlobalPreferences ) {
		// Allow global preferences for email-blacklist to be auto-set where it is overridden
		// T231577
		$wgGlobalPreferencesAutoPrefs = [
			'email-blacklist',
			'echo-notifications-blacklist'
		];
	}

	if ( $wmgUseFlow ) {
		// Override CommonSettings.php, which has:
		// $wgFlowExternalStore = $wgDefaultExternalStore;
		$wgFlowExternalStore = [
			'DB://flow_cluster1',
		];

		$wgExtraNamespaces += [
			190 => 'Flow_test',
			191 => 'Flow_test_talk',
		];

		$wgNamespacesWithSubpages += [
			190 => true,
			191 => true,
		];

		// CONTENT_MODEL_FLOW_BOARD
		$wgNamespaceContentModels[ 191 ] = 'flow-board';
	}

	if ( $wmgUseFileExporter ) {
		$wgFileExporterTarget = 'https://commons.wikimedia.beta.wmflabs.org/wiki/Special:ImportFile';
	}

	if ( $wmgUseContentTranslation ) {
		$wgContentTranslationSiteTemplates['cx'] = 'https://cxserver-beta.wmcloud.org/v1';
		$wgContentTranslationSiteTemplates['cookieDomain'] = false;
		$wgContentTranslationTranslateInTarget = false;
	}

	if ( $wmgUseIPInfo ) {
		// This allows admins on beta to test the feature.
		$wgGroupPermissions['sysop']['ipinfo'] = true;
		$wgGroupPermissions['sysop']['ipinfo-view-basic'] = true;
	}

	if ( $wmgUseCentralNotice ) {
		// Emit CSP headers on banner previews. This can go away when full CSP
		// support (T135963) is deployed.
		// www.pages04.net is used by Wikimedia Fundraising to enable 'remind me later' banner functionality, which submits email addresses to our email campaign vendor
		$wgCentralNoticeContentSecurityPolicy = "script-src 'unsafe-eval' blob: 'self' meta.wikimedia.beta.wmflabs.org *.wikimedia.org *.wikipedia.org *.wikinews.org *.wiktionary.org *.wikibooks.org *.wikiversity.org *.wikisource.org wikisource.org *.wikiquote.org *.wikidata.org *.wikivoyage.org *.mediawiki.org 'unsafe-inline'; default-src 'self' data: blob: https://upload.wikimedia.beta.wmflabs.org upload.wikimedia.beta.wmflabs.org https://commons.wikimedia.beta.wmflabs.org https://upload.wikimedia.org https://commons.wikimedia.org meta.wikimedia.beta.wmflabs.org wikifunctions.beta.wmflabs.org *.wikimedia.org *.wikipedia.org *.wikinews.org *.wiktionary.org *.wikibooks.org *.wikiversity.org *.wikisource.org wikisource.org *.wikiquote.org *.wikidata.org *.wikivoyage.org *.mediawiki.org wikimedia.org www.pages04.net; style-src 'self' data: blob: https://upload.wikimedia.beta.wmflabs.org upload.wikimedia.beta.wmflabs.org https://commons.wikimedia.beta.wmflabs.org https://wikifunctions.beta.wmflabs.org https://upload.wikimedia.org https://commons.wikimedia.org meta.wikimedia.beta.wmflabs.org *.wikimedia.org *.wikipedia.org *.wikinews.org *.wiktionary.org *.wikibooks.org *.wikiversity.org *.wikisource.org wikisource.org *.wikiquote.org *.wikidata.org *.wikivoyage.org *.wikifunctions.org *.mediawiki.org wikimedia.org 'unsafe-inline';";
	}

	if ( $wmgUseCite ) {
		// Temporary until we deploy to production, T236894
		$wgCiteBookReferencing = true;
	}

	// Labs override for GlobalCssJs
	if ( $wmgUseGlobalCssJs && $wmgUseCentralAuth ) {
		// Load from betalabs metawiki
		$wgResourceLoaderSources['metawiki'] = [
			'apiScript' => '//meta.wikimedia.beta.wmflabs.org/w/api.php',
			'loadScript' => '//meta.wikimedia.beta.wmflabs.org/w/load.php',
		];
	}

	if ( $wmgUseGlobalUserPage && $wmgUseCentralAuth ) {
		// Labs override
		$wgGlobalUserPageAPIUrl = 'https://meta.wikimedia.beta.wmflabs.org/w/api.php';
		$wgGlobalUserPageDBname = 'metawiki';
	}

	if ( $wmgUseUrlShortener ) {
		// Labs overrides
		$wgUrlShortenerReadOnly = false;
		$wgUrlShortenerServer = 'w-beta.wmflabs.org';
		$wgUrlShortenerDBCluster = false;
		$wgUrlShortenerDBName = 'wikishared';
		$wgUrlShortenerEnableSidebar = true;
		$wgUrlShortenerAllowedDomains = [
			'(.*\.)?wikipedia\.beta\.wmflabs\.org',
			'(.*\.)?wiktionary\.beta\.wmflabs\.org',
			'(.*\.)?wikibooks\.beta\.wmflabs\.org',
			'(.*\.)?wikinews\.beta\.wmflabs\.org',
			'(.*\.)?wikiquote\.beta\.wmflabs\.org',
			'(.*\.)?wikisource\.beta\.wmflabs\.org',
			'(.*\.)?wikiversity\.beta\.wmflabs\.org',
			'(.*\.)?wikivoyage\.beta\.wmflabs\.org',
			'(.*\.)?wikimedia\.beta\.wmflabs\.org',
			'(.*\.)?wikidata\.beta\.wmflabs\.org',
			// wmcloud.org domains
			'(.*\.)?wikipedia\.beta\.wmcloud\.org',
			'(.*\.)?wikivoyage\.beta\.wmcloud\.org',
		];
		$wgUrlShortenerApprovedDomains = [
			'*.wikipedia.beta.wmflabs.org',
			'*.wiktionary.beta.wmflabs.org',
			'*.wikibooks.beta.wmflabs.org',
			'*.wikinews.beta.wmflabs.org',
			'*.wikiquote.beta.wmflabs.org',
			'*.wikisource.beta.wmflabs.org',
			'*.wikiversity.beta.wmflabs.org',
			'*.wikivoyage.beta.wmflabs.org',
			'*.wikimedia.beta.wmflabs.org',
			'*.wikidata.beta.wmflabs.org',
			// wmcloud.org domains
			'*.wikipedia.beta.wmcloud.org',
			'*.wikivoyage.beta.wmcloud.org',
		];
	}

	// Labs override for BounceHandler
	if ( $wmgUseBounceHandler ) {
		// $wgVERPsecret = ''; // This was set in PrivateSettings.php by Legoktm
		$wgBounceHandlerCluster = false;
		$wgBounceHandlerSharedDB = false;
		// deployment-mx03.deployment-prep.eqiad1.wikimedia.cloud
		$wgBounceHandlerInternalIPs = [ '127.0.0.1', '::1', '172.16.6.221' ];
		$wgBounceHandlerUnconfirmUsers = true;
		$wgBounceRecordLimit = 5;
		$wgVERPdomainPart = 'beta.wmflabs.org';
	}

	if ( $wmgUseTimedMediaHandler ) {
		$wgMwEmbedModuleConfig[ 'MediaWiki.ApiProviders' ] = [
			"commons" => [ 'url' => '//commons.wikimedia.beta.wmflabs.org/w/api.php' ]
		];
		// enable transcoding on labs
		$wgEnableTranscode = true;
		// use new ffmpeg build w/ VP9 & Opus support
		$wgFFmpegLocation = '/usr/bin/ffmpeg';
	}

	// Enable Flickr uploads on commons beta T86120
	if ( $wgDBname == 'commonswiki' ) {
		$wgGroupPermissions['user']['upload'] = true;
		$wgGroupPermissions['user']['upload_by_url'] = true;
	} else {
		// Use InstantCommons on all betawikis except commonswiki
		$wgUseInstantCommons = true;
	}

	if ( $wmgEnableJsonConfigDataMode && $wgDBname !== 'commonswiki' ) {

		// Enable Tabular data namespace on Commons - T148745
		$wgJsonConfigs['Tabular.JsonConfig']['remote']['url'] = 'https://commons.wikimedia.beta.wmflabs.org/w/api.php';

		// Enable Map (GeoJSON) data namespace on Commons - T149548
		$wgJsonConfigs['Map.JsonConfig']['remote']['url'] = 'https://commons.wikimedia.beta.wmflabs.org/w/api.php';
	}

	if ( $wmgUseMath ) {
		$wgMathValidModes = [ 'source', 'mathml', 'native', 'mathjax' ];
		$wgDefaultUserOptions[ 'math' ] = 'native';
		$wgMathEnableFormulaLinks = true;
		$wgMathWikibasePropertyIdHasPart = 'P253104';
		$wgMathWikibasePropertyIdDefiningFormula = 'P253105';
		$wgMathWikibasePropertyIdQuantitySymbol = 'P253106';
	}

	// CORS (cross-domain AJAX, T22814)
	// This lists the domains that are accepted as *origins* of CORS requests
	// DO NOT add domains here that aren't WMF wikis unless you really know what you're doing
	if ( $wmgUseCORS ) {
		$wgCrossSiteAJAXdomains = [
			'*.beta.wmflabs.org',
			'*.beta.wmcloud.org',
		];
	}

	// Temporary override to test T68699 on Beta Cluster.  Remove when in production.
	$wgExtendedLoginCookieExpiration = 365 * 86400;

	if ( $wmgUseCollection ) {
		$wgCollectionPortletFormats[] = 'rdf2text';
		// Don't use production proxy to reach PediaPress
		$wgCollectionCommandToServeURL[ 'zip_post' ] = 'https://pediapress.com/wmfup/';
	}

	if ( $wmgUseEcho ) {
		$wgEchoNotifiers['push'] = [ PushNotifier::class, 'notifyWithPush' ];
		$wgDefaultNotifyTypeAvailability['push'] = true;
		$wgNotifyTypeAvailabilityByCategory['system']['push'] = false;
		$wgNotifyTypeAvailabilityByCategory['system-noemail']['push'] = false;

		// Temporarily enable conditional defaults for Echo properties (T353225)
		$wgConditionalUserOptions['echo-subscriptions-web-reverted'] = [
			[
				false,
				[ CUDCOND_AFTER, '20130501000000' ]
			]
		];
		$wgConditionalUserOptions['echo-subscriptions-web-article-linked'] =
			$wgConditionalUserOptions['echo-subscriptions-email-mention'] =
			$wgConditionalUserOptions['echo-subscriptions-email-article-linked'] = [
				[
					true,
					[ CUDCOND_AFTER, '20130501000000' ]
				]
			];
	}

	if ( $wmgUseEcho && $wmgUseCentralAuth ) {
		$wgEchoSharedTrackingDB = 'wikishared';
		// Set cluster back to false, to override CommonSettings.php setting it to 'extension1'
		$wgEchoSharedTrackingCluster = false;
	}

	// Enabling thank-you-edit on beta for testing T128249. Still disabled in prod.
	$wgEchoNotifications['thank-you-edit']['notify-type-availability']['web'] = true;

	// Enabling article-reminder on beta for testing T166973. Still disabled in prod.
	$wgAllowArticleReminderNotification = true;

	if ( $wmgUseGraph ) {
		// **** THIS LIST MUST MATCH puppet/blob/production/hieradata/cloud/eqiad1/deployment-prep/common.yaml ****
		// See https://www.mediawiki.org/wiki/Extension:Graph#External_data
		$wgGraphAllowedDomains = [
			'http' => [
				'wmflabs.org',
			],
			'https' => [
				'beta.wmflabs.org',
			],
			'wikirawupload' => [
				'upload.wikimedia.beta.wmflabs.org',
				'upload.wikimedia.org',
			],
			'wikidatasparql' => [
				'wdqs-test.wmflabs.org',
				'query.wikidata.org',
			],
			'geoshape' => [
				'maps.wikimedia.org',
			],
		];
	}

	if ( $wmgUseMediaModeration ) {
		$wgVirtualDomainsMapping['virtual-mediamoderation'] = [ 'db' => false ];
	}

	if ( $wmgUseORES ) {
		$wgOresBaseUrl = 'https://ores-beta.wmflabs.org/';
		$wgOresLiftWingBaseUrl = 'https://api.wikimedia.org/service/lw/inference/';
	}

	if ( $wmgUseUniversalLanguageSelector ) {
		$wgDefaultUserOptions['compact-language-links'] = 0;
	}

	$wgLoginNotifyAttemptsKnownIP = 10;
	$wgLoginNotifyAttemptsNewIP = 1;

	if ( $wmgUseTwoColConflict ) {
		$wgTwoColConflictBetaFeature = false;
	}

	if ( $wmgUseFileImporter ) {
		// Beta commons references configuration files hosted locally.
		// Note that beta testwiki will continue to fetch its configuration from production mw.org .
		if ( $wgDBname == 'commonswiki' ) {
			$wgFileImporterCommonsHelperServer = 'https://commons.wikimedia.beta.wmflabs.org';
			$wgFileImporterCommonsHelperBasePageName = 'Extension:FileImporter/Data/';
			$wgFileImporterCommonsHelperHelpPage = 'https://commons.wikimedia.beta.wmflabs.org/wiki/Extension:FileImporter/Data';
			$wgFileImporterWikidataEntityEndpoint = 'https://wikidata.beta.wmflabs.org/wiki/Special:EntityData/';
			$wgFileImporterWikidataNowCommonsEntity = 'Q531650';
		}

		$wgFileImporterCodexMode = true;
	}

	if ( $wmgUseEventBus ) {
		$wgEventBusEnableRunJobAPI = true;
	}

	if ( $wmgUseStopForumSpam ) {
		wfLoadExtension( 'StopForumSpam' );
		$wgSFSIPListLocation = 'https://www.stopforumspam.com/downloads/listed_ip_90_ipv46_all.gz';
		$wgSFSIPListLocationMD5 = 'https://www.stopforumspam.com/downloads/listed_ip_90_ipv46_all.gz.md5';
	}

	$wgMessageCacheType = CACHE_ACCEL;

	// This will work for most wikis, which is considered good enough.
	$wgPageViewInfoWikimediaDomain = "$lang.$site.org";
	$wgPageViewInfoWikimediaEndpoint = 'https://wikimedia.org/api/rest_v1';

	if ( $wmgUseGrowthExperiments ) {
		$wgGERestbaseUrl = $wgGERestbaseUrl ?: "https://$lang.$site.org/api/rest_v1";
		// Overrides CommonSettings.php which would use LabsServices.php,
		// but we can't use variables there.
		$wgGEImageRecommendationServiceUrl = "https://$lang.$site.org/w/api.php";
	}

	// Let Beta Cluster Commons do upload-from-URL from production Commons.
	if ( $wgDBname == 'commonswiki' ) {
		$wgCopyUploadsDomains[] = 'upload.wikimedia.org';
	}

	// Turn off exact search match redirects on beta commons
	if ( $wgDBname == 'commonswiki' ) {
		$wgDefaultUserOptions['search-match-redirect'] = false;
	}

	if ( $wmgUseWikimediaApiPortalOAuth ) {
		$wgWikimediaApiPortalOAuthMetaApiURL = 'https://meta.wikimedia.beta.wmflabs.org/w/api.php';
		$wgWikimediaApiPortalOAuthMetaRestURL = 'https://meta.wikimedia.beta.wmflabs.org/w/rest.php';
	}

	// Test of new import source configuration on labs cluster
	$wgImportSources = [];
	require_once __DIR__ . '/import.php';
	$wgHooks['ImportSources'][] = 'wmfImportSources';

	wfLoadExtension( 'Parsoid', "$IP/vendor/wikimedia/parsoid/extension.json" );

	// Enable ChessBrowser extension, see T244075
	if ( $wmgUseChessBrowser ) {
		wfLoadExtension( 'ChessBrowser' );
	}

	// SecurePoll -- do not let users to view PII
	if ( $wmgUseSecurePoll ) {
		foreach ( $wgGroupPermissions as $group => $permissions ) {
			if ( array_key_exists( 'securepoll-view-voter-pii', $permissions ) ) {
				$wgGroupPermissions[$group]['securepoll-view-voter-pii'] = false;
			}
		}
	}

	// Point to the deployment-prep kartotherian server, see T310150.
	$wgKartographerMapServer = 'https://maps-beta.wmflabs.org';

	// Temporary feature flag for the Parsoid support for Kartographer (T340134)
	$wgKartographerParsoidSupport = true;

	// Enable max-width for editing. T307725.
	$wgVectorMaxWidthOptions['exclude']['querystring']['action'] = '(history|edit)';

	// T360098 - change Vector font-size for anons, existing named users or newly created users.
	$wgDefaultUserOptions['vector-font-size'] = 1;
	$wgConditionalUserOptions['vector-font-size'] = [
		[ 1, [ CUDCOND_AFTER, '20240409000000' ] ],
		[ 0, [ CUDCOND_NAMED ] ],
	];

	if ( $wmgUseCampaignEvents ) {
		// Use wikishared for all wikis, unlike production
		$wgVirtualDomainsMapping['virtual-campaignevents'] = [
			'cluster' => 'extension1',
			'db' => 'wikishared',
		];
		$wgCampaignEventsProgramsAndEventsDashboardInstance = 'staging';
		$wgWikimediaCampaignEventsFluxxBaseUrl = 'https://wmf.preprod.fluxxlabs.com/api/rest/v2/';
		$wgWikimediaCampaignEventsFluxxOauthUrl = 'https://wmf.preprod.fluxxlabs.com/oauth/token';
		// Re-add rights removed in the production config
		$wgGroupPermissions['user']['campaignevents-enable-registration'] = true;
		$wgGroupPermissions['user']['campaignevents-organize-events'] = true;
		$wgGroupPermissions['user']['campaignevents-email-participants'] = true;
		// This group is not needed in beta. Redundant entries in wgAddGroups and
		// wgRemoveGroups are harmless.
		unset( $wgGroupPermissions['event-organizer'] );
	}

	// Ignore parameter order when matching request URLs to CDN URLs (T314868)
	$wgCdnMatchParameterOrder = false;

	// T314294
	if ( $wmgUsePhonos ) {
		wfLoadExtension( 'Phonos' );
		// $wgPhonosApiKeyGoogle in PrivateSettings
		$wgPhonosEngine = 'google';
		$wgPhonosFileBackend = 'global-multiwrite';
	}

	// T364034
	if ( $wmgUseAutoModerator ) {
		$wgAutoModeratorLiftWingBaseUrl = 'https://api.wikimedia.org/service/lw/inference/v1/models/';
		$wgAutoModeratorLiftWingAddHostHeader = false;
	}

	if ( $wmgUseReportIncident ) {
		wfLoadExtension( 'ReportIncident' );
	}

	// T369945
	if ( $wmgUseChart ) {
		wfLoadExtension( 'Chart' );
		$wgChartServiceUrl = $wmgLocalServices['chart-renderer'];

		// Set up chart pages with JsonConfig
		$wgJsonConfigModels['Chart.JsonConfig'] = 'MediaWiki\Extension\Chart\JCChartContent';
		$wgJsonConfigs['Chart.JsonConfig'] = [
			'namespace' => 486,
			'nsName' => 'Data',
			// page name must end in ".chart", and contain at least one symbol
			'pattern' => '/.\.chart$/',
			'license' => 'CC0-1.0',
			// allows the cache keys to be shared between wikis
			'isLocal' => false,
		];

		if ( $wgDBname === 'commonswiki' ) {
			$wgJsonConfigs['Chart.JsonConfig']['store'] = true;
		} else {
			$wgJsonConfigs['Chart.JsonConfig']['remote'] = [
				'url' => 'https://commons.wikimedia.beta.wmflabs.org/w/api.php'
			];
		}

		// Tabular data pages are already set up with JsonConfig through $wmgEnableJsonConfigDataMode
	}

	// T372527
	if ( $wmgUseCommunityRequests ) {
		wfLoadExtension( 'CommunityRequests' );
	}

	// IP Masking / Temporary accounts
	// Revert the changes made by CommonSettings.php, as some temporary accounts on betawikis start with '*'.
	$wgAutoCreateTempUser['matchPattern'] = [ '*$1', '~2$1' ];

	// Jade was undeployed as part of T281430, and content is being cleaned up as part of T345874
	$wgContentHandlers['JadeEntity'] = 'FallbackContentHandler';
	$wgContentHandlers['JadeJudgment'] = 'FallbackContentHandler';

	$wgBlockTargetMigrationStage = SCHEMA_COMPAT_NEW;

	// No restrictions in test environment to facilitate testing.
	$wgMinervaNightModeOptions['exclude']['querystring'] = [];
	$wgMinervaNightModeOptions['exclude']['namespaces'] = [];
	$wgMinervaNightModeOptions['exclude']['pagetitles'] = [];
	$wgVectorNightModeOptions = $wgMinervaNightModeOptions;

	// show new donate link in beta for QA and testing
	$wgWikimediaMessagesAnonDonateLink = true;

	if ( $wmgUseNetworkSession ) {
		$wgNetworkSessionProviderUsers = [
			[
				'username' => 'networksession_testuser',
				'ip_ranges' => [
					// Derived from modules/network/data/data.yaml in
					// operations/puppet for cloud networks. This is not protecting
					// anything, all anon's have read access anyways, this is to
					// demonstrate it working in one place but not another.
					'172.16.0.0/12',
					'127.0.0.0/8',
					'::1/128',
					'10.64.20.0/24',
					'2620:0:861:118::/64',
					'10.64.148.0/24',
					'2620:0:861:11c::/64',
					'10.64.149.0/24',
					'2620:0:861:11d::/64',
					'10.64.150.0/24',
					'2620:0:861:11e::/64',
					'10.64.151.0/24',
					'2620:0:861:11f::/64',
					'10.192.20.0/24',
					'2620:0:860:118::/64',
				],
				// In normal operation token would be secret, but for labs
				// it only allows reading via api, which is already possible
				// with anon, meaning this protects nothing. It does still
				// demonstrate the functionality.
				'token' => 'networksession-testuser-token',
			],
		];
	}

	// Community configuration
	if ( $wmgUseCommunityConfiguration ) {
		$wgCommunityConfigurationCommonsApiURL = 'https://commons.wikimedia.beta.wmflabs.org/w/api.php';
	}
}
// end safeguard
