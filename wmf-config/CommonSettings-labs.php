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

use MediaWiki\Content\FallbackContentHandler;
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
		'wikipedia.beta.wmcloud.org',
		'wiktionary.beta.wmcloud.org',
		'wikiquote.beta.wmcloud.org',
		'wikibooks.beta.wmcloud.org',
		'wikiquote.beta.wmcloud.org',
		'wikinews.beta.wmcloud.org',
		'wikisource.beta.wmcloud.org',
		'wikiversity.beta.wmcloud.org',
		'wikivoyage.beta.wmcloud.org',
		'www.wikidata.beta.wmcloud.org',
		'api.wikimedia.beta.wmcloud.org',
		'commons.wikimedia.beta.wmcloud.org',
		'login.wikimedia.beta.wmcloud.org',
		'meta.wikimedia.beta.wmcloud.org',
		'auth.wikimedia.beta.wmcloud.org',
		// T289318: Legacy compat
		'wikipedia.beta.wmflabs.org',
		'wiktionary.beta.wmflabs.org',
		'wikibooks.beta.wmflabs.org',
		'wikiquote.beta.wmflabs.org',
		'wikinews.beta.wmflabs.org',
		'wikisource.beta.wmflabs.org',
		'wikiversity.beta.wmflabs.org',
		'wikivoyage.beta.wmflabs.org',
		'commons.wikimedia.beta.wmflabs.org',
		'api.wikimedia.beta.wmflabs.org',
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

	if ( $wmgUseCentralNotice ) {
		// Same as production CommonSettings.php, but adding *.wikimedia.beta.wmflabs.org
		// and *.wikimedia.beta.wmcloud.org (T289318) for meta, upload, and commons in Beta Cluster.
		$wgCentralNoticeContentSecurityPolicy =
			"script-src 'unsafe-eval' blob: 'self' meta.wikimedia.org *.wikimedia.org *.wikipedia.org *.wikinews.org *.wiktionary.org *.wikibooks.org *.wikiversity.org *.wikisource.org wikisource.org *.wikiquote.org *.wikidata.org *.wikifunctions.org *.wikivoyage.org *.mediawiki.org 'unsafe-inline' *.wikimedia.beta.wmflabs.org *.wikimedia.beta.wmcloud.org; "
			. "default-src 'self' data: blob: upload.wikimedia.org https://commons.wikimedia.org meta.wikimedia.org *.wikimedia.org *.wikipedia.org *.wikinews.org *.wiktionary.org *.wikibooks.org *.wikiversity.org *.wikisource.org wikisource.org *.wikiquote.org *.wikidata.org *.wikifunctions.org *.wikivoyage.org *.mediawiki.org wikimedia.org www.pages04.net *.wikimedia.beta.wmflabs.org *.wikimedia.beta.wmcloud.org; "
			. "style-src 'self' data: blob: upload.wikimedia.org https://commons.wikimedia.org meta.wikimedia.org *.wikimedia.org *.wikipedia.org *.wikinews.org *.wiktionary.org *.wikibooks.org *.wikiversity.org *.wikisource.org wikisource.org *.wikiquote.org *.wikidata.org *.wikifunctions.org *.wikivoyage.org *.mediawiki.org wikimedia.org 'unsafe-inline' *.wikimedia.beta.wmflabs.org *.wikimedia.beta.wmcloud.org; "
			. "connect-src 'self' data: blob: upload.wikimedia.org https://commons.wikimedia.org meta.wikimedia.org *.wikimedia.org *.wikipedia.org *.wikinews.org *.wiktionary.org *.wikibooks.org *.wikiversity.org *.wikisource.org wikisource.org *.wikiquote.org *.wikidata.org *.wikifunctions.org *.wikivoyage.org *.mediawiki.org wikimedia.org www.pages04.net app.goacoustic.com *.wikimedia.beta.wmflabs.org *.wikimedia.beta.wmcloud.org;";
	}

	if ( $wmgUseCite ) {
		// Temporary until we deploy to production, T236894
		$wgCiteSubReferencing = true;
		// Temporary while developing feature, T378807
		$wgCiteBacklinkCommunityConfiguration = true;
	}

	// Labs override for GlobalCssJs
	if ( $wmgUseGlobalCssJs && $wmgUseCentralAuth ) {
		// Load from betalabs metawiki
		$wgResourceLoaderSources['metawiki'] = [
			'apiScript' => '//meta.wikimedia.beta.wmcloud.org/w/api.php',
			'loadScript' => '//meta.wikimedia.beta.wmcloud.org/w/load.php',
		];
	}

	if ( $wmgUseGlobalUserPage && $wmgUseCentralAuth ) {
		// Labs override
		$wgGlobalUserPageAPIUrl = 'https://meta.wikimedia.beta.wmcloud.org/w/api.php';
		$wgGlobalUserPageDBname = 'metawiki';
	}

	if ( $wmgUseUrlShortener ) {
		// Labs overrides
		$wgUrlShortenerReadOnly = false;
		$wgUrlShortenerServer = 'w.beta.wmcloud.org';
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
			// T289318
			'(.*\.)?wikipedia\.beta\.wmcloud\.org',
			'(.*\.)?wiktionary\.beta\.wmcloud\.org',
			'(.*\.)?wikibooks\.beta\.wmcloud\.org',
			'(.*\.)?wikinews\.beta\.wmcloud\.org',
			'(.*\.)?wikiquote\.beta\.wmcloud\.org',
			'(.*\.)?wikisource\.beta\.wmcloud\.org',
			'(.*\.)?wikiversity\.beta\.wmcloud\.org',
			'(.*\.)?wikivoyage\.beta\.wmcloud\.org',
			'(.*\.)?wikimedia\.beta\.wmcloud\.org',
			'(.*\.)?wikidata\.beta\.wmcloud\.org',
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
			// T289318
			'*.wikipedia.beta.wmcloud.org',
			'*.wiktionary.beta.wmcloud.org',
			'*.wikibooks.beta.wmcloud.org',
			'*.wikinews.beta.wmcloud.org',
			'*.wikiquote.beta.wmcloud.org',
			'*.wikisource.beta.wmcloud.org',
			'*.wikiversity.beta.wmcloud.org',
			'*.wikivoyage.beta.wmcloud.org',
			'*.wikimedia.beta.wmcloud.org',
			'*.wikidata.beta.wmcloud.org',
		];
	}

	// Labs override for BounceHandler
	if ( $wmgUseBounceHandler ) {
		unset( $wgVirtualDomainsMapping['virtual-bouncehandler'] );
		// deployment-mx03.deployment-prep.eqiad1.wikimedia.cloud
		$wgBounceHandlerInternalIPs = [ '127.0.0.1', '::1', '172.16.6.221' ];
		$wgVERPdomainPart = 'beta.wmflabs.org';
	}

	if ( $wmgUseTimedMediaHandler ) {
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

	if ( $wmgUseJsonConfig ) {
		// T374746 cache invalidation issues for globaljsonlinks;
		// globaljsonlinks* shared tables are not live on beta cluster
		// as of 2024-11-14.
		// Override the enabling for testwiki and testcommonswiki
		// so their beta equivalents can run without breaking links
		// updates and cache invalidation propagation.
		$wgTrackGlobalJsonLinks = false;
		$wgTrackGlobalJsonLinksNamespaces = false;
	}

	if ( $wmgUseMath ) {
		$wgMathValidModes = [ 'source', 'mathml', 'native', 'mathjax' ];
		$wgDefaultUserOptions[ 'math' ] = 'native';
		$wgMathEnableFormulaLinks = true;
		$wgMathWikibasePropertyIdHasPart = 'P253104';
		$wgMathWikibasePropertyIdDefiningFormula = 'P253105';
		$wgMathWikibasePropertyIdQuantitySymbol = 'P253106';
		$wgMathWikibasePropertyIdInDefiningFormula = 'P253157';
		$wgMathWikibasePropertyIdSymbolRepresents = 'P253158';
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

	// Enabling article-reminder on beta for testing T166973. Still disabled in prod.
	$wgAllowArticleReminderNotification = true;

	if ( $wmgUseGraph ) {
		// **** THIS LIST MUST MATCH puppet/blob/production/hieradata/cloud/eqiad1/deployment-prep/common.yaml ****
		// See https://www.mediawiki.org/wiki/Extension:Graph#External_data
		$wgGraphAllowedDomains = [
			// TODO: Is 'http' needed in Beta specifically? Prod doesn't allow it at all.
			'http' => [
				'wmflabs.org',
			],
			'https' => [
				'beta.wmflabs.org',
				// T289318
				'beta.wmcloud.org',
			],
			'wikirawupload' => [
				'upload.wikimedia.org',
				'upload.wikimedia.beta.wmflabs.org',
				// T289318
				'upload.wikimedia.beta.wmcloud.org',
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
		// In production, all wikis fetch configuration centrally from mediawiki.org.
		// In the Beta Cluster, the extenison is enabled only on Commons and testwiki:
		// - Beta commons hosts its configuration files locally instead, to allow testing
		//   new/custom configuration changes.
		// - Beta testwiki fetches configuration from production mediawiki.org, to allow
		//   testing software changes against the always-current production config.
		if ( $wgDBname == 'commonswiki' ) {
			$wgFileImporterCommonsHelperServer = 'https://commons.wikimedia.beta.wmflabs.org';
			$wgFileImporterCommonsHelperBasePageName = 'Extension:FileImporter/Data/';
			$wgFileImporterCommonsHelperHelpPage = 'https://commons.wikimedia.beta.wmflabs.org/wiki/Extension:FileImporter/Data';
			$wgFileImporterWikidataEntityEndpoint = 'https://www.wikidata.beta.wmcloud.org/wiki/Special:EntityData/';
			$wgFileImporterWikidataNowCommonsEntity = 'Q531650';
		}

		$wgFileImporterCodexMode = true;
	}

	$wgEventBusEnableRunJobAPI = true;

	if ( $wmgUseStopForumSpam ) {
		wfLoadExtension( 'StopForumSpam' );
		$wgSFSIPListLocation = 'https://www.stopforumspam.com/downloads/listed_ip_90_ipv46_all.gz';
		$wgSFSValidateIPListLocationMD5 = 'https://www.stopforumspam.com/downloads/listed_ip_90_ipv46_all.gz.md5';
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
		$wgWikimediaApiPortalOAuthMetaApiURL = 'https://meta.wikimedia.beta.wmcloud.org/w/api.php';
		$wgWikimediaApiPortalOAuthMetaRestURL = 'https://meta.wikimedia.beta.wmcloud.org/w/rest.php';
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
		$wgWikimediaCampaignEventsSparqlEndpoint = 'https://query-main.wikidata.org/sparql';
		// Re-add rights removed in the production config
		$wgGroupPermissions['user']['campaignevents-enable-registration'] = true;
		$wgGroupPermissions['user']['campaignevents-organize-events'] = true;
		$wgGroupPermissions['user']['campaignevents-email-participants'] = true;
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

	// T372527
	if ( $wmgUseCommunityRequests ) {
		wfLoadExtension( 'CommunityRequests' );
	}

	if ( !$wmgUseCheckUser ) {
		unset( $wgGroupPermissions['checkuser'] );
	}

	// IP Masking / Temporary accounts
	// Revert the changes made by CommonSettings.php, as some temporary accounts on betawikis start with '*'.
	$wgAutoCreateTempUser['matchPattern'] = [ '*$1', '~2$1' ];

	if ( !$wmgUseCheckUser ) {
		// Remove any references to the temporary-account-viewer group, as this group is only present when CheckUser is
		// installed which it is not on the beta clusters. This means removing the group definition and the auto-promotion
		// conditions for the group.
		unset( $wgGroupPermissions['temporary-account-viewer'] );

		// Remove assignment of the 'checkuser-temporary-account' and 'checkuser-temporary-account-no-preference' rights
		// done in core-Permissions.php. This is because these rights do not exist on the beta clusters.
		$rightsToRemoveOnBeta = [ 'checkuser-temporary-account', 'checkuser-temporary-account-no-preference' ];
		foreach ( $wgGroupPermissions as $group => $permissions ) {
			foreach ( $rightsToRemoveOnBeta as $rightToCheck ) {
				if ( array_key_exists( $rightToCheck, $permissions ) ) {
					$wgGroupPermissions[$group][$rightToCheck] = false;
				}
			}
		}
	}

	// Jade was undeployed as part of T281430, and content is being cleaned up as part of T345874
	$wgContentHandlers['JadeEntity'] = FallbackContentHandler::class;
	$wgContentHandlers['JadeJudgment'] = FallbackContentHandler::class;

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

	// T377988
	if (
		$wgDBname === 'commonswiki' &&
		$wmgUseUploadWizard &&
		$wmgUseWikibaseMediaInfo
	) {
		$wgUploadWizardConfig['wikibase']['properties'] = [
			'date' => 'P253152',
			'source' => 'P253153',
			'operator' => 'P253154',
			'described_at_url' => 'P253095',
		];
		$wgUploadWizardConfig['wikibase']['items'] = [
			'file_available_on_the_internet' => 'Q631353',
		];
		$wgUploadWizardConfig['sourceStringToWikidataIdMapping'] = [
			'facebook' => 'Q631354',
			'google' => 'Q631355',
			'youtube' => 'Q631356',
		];
	}

	// T385592
	$wgVirtualDomainsMapping['virtual-wikibase-terms'] = [ 'db' => 'wikidatawiki' ];
}
// end safeguard
