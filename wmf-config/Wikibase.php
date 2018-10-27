<?php

// Load the Repo, and Repo extensions
if ( !empty( $wmgUseWikibaseRepo ) ) {
	include_once "$IP/extensions/Wikibase/repo/Wikibase.php";
	if ( !empty( $wmgUseWikibaseWikidataOrg ) ) {
		wfLoadExtension( 'Wikidata.org' );
	}
	if ( !empty( $wmgUseWikibasePropertySuggester ) ) {
		wfLoadExtension( 'PropertySuggester' );
	}
	if ( !empty( $wmgUseWikibaseQuality ) ) {
		wfLoadExtension( 'WikibaseQuality' );
		wfLoadExtension( 'WikibaseQualityConstraints' );
	}
	if ( !empty( $wmgUseWikibaseLexeme ) ) {
		wfLoadExtension( 'WikibaseLexeme' );
	}
	if ( !empty( $wmgUseWikibaseMediaInfo ) ) {
		wfLoadExtension( 'WikibaseMediaInfo' );
	}
}

// Load the Client, and Client extensions
if ( !empty( $wmgUseWikibaseClient ) ) {
	include_once "$IP/extensions/Wikibase/client/WikibaseClient.php";
	if ( !empty( $wmgUseWikibaseWikimediaBadges ) ) {
		wfLoadExtension( 'WikimediaBadges' );
	}
	if ( !empty( $wmgUseArticlePlaceholder ) ) {
		wfLoadExtension( 'ArticlePlaceholder' );
	}
	if ( !empty( $wmgUseWikibaseLexeme ) ) {
		wfLoadExtension( 'WikibaseLexeme' );
	}
}

// This allows cache invalidations to be in sync with deploys
// and not shared across different versions of wikibase.
// e.g. wikibase_shared/1_31_0-wmf_2-testwikidatawiki0 for test wikis
// and wikibase_shared/1_31_0-wmf_2-wikidatawiki for all others.
$wmgWBSharedCacheKey = 'wikibase_shared/' . str_replace( '.', '_', $wmgVersionNumber ) . '-' . $wmgWikibaseCachePrefix;

if ( defined( 'HHVM_VERSION' ) ) {
	// Split the cache up for hhvm. T73461
	$wmgWBSharedCacheKey .= '-hhvm';
}

// Lock manager config must use the master datacenter
// Use a TTL of 15 mins, no script will run for longer than this
$wgLockManagers[] = [
	'name'         => 'wikibaseDispatchRedisLockManager',
	'class'        => 'RedisLockManager',
	'lockTTL'      => 900, // 15 mins ( 15 * 60 )
	'lockServers'  => $wmfMasterServices['redis_lock'],
	'domain'       => $wgDBname,
	'srvsByBucket' => [
		0 => $redisLockServers
	],
	'redisConfig'  => [
		'connectTimeout' => 2,
		'readTimeout'    => 2,
		'password'       => $wmgRedisPassword
	]
];

if ( $wmgUseWikibaseRepo ) {
	$wgWBRepoSettings['idBlacklist'] = $wmgWikibaseIdBlacklist;
	$wgWBRepoSettings['disabledDataTypes'] = $wmgWikibaseDisabledDataTypes;
	$wgWBRepoSettings['tmpMaxItemIdForNewItemIdHtmlFormatter'] = $wmgWikibaseMaxItemIdForNewItemIdHtmlFormatter;
	$wgWBRepoSettings['entityDataFormats'] = $wmgWikibaseEntityDataFormats;
}

if ( $wmgUseWikibaseClient ) {
	$wgWBClientSettings['disabledAccessEntityTypes'] = $wmgWikibaseDisabledAccessEntityTypes;
}

$baseWikidataNs = 120;

// Define the namespace indexes for repo (and client wikis also need to be aware of these,
// thus entityNamespaces need to be a shared setting).
//
// NOTE: do *not* define WB_NS_ITEM and WB_NS_ITEM_TALK when using a core namespace for items!
define( 'WB_NS_PROPERTY', $baseWikidataNs );
define( 'WB_NS_PROPERTY_TALK', $baseWikidataNs + 1 );
// TODO is the query namespace used? Can we remove this?
define( 'WB_NS_QUERY', $baseWikidataNs + 2 );
define( 'WB_NS_QUERY_TALK', $baseWikidataNs + 3 );
define( 'WB_NS_LEXEME', 146 );
define( 'WB_NS_LEXEME_TALK', 147 );

// Tell Wikibase which namespace to use for which type of entities
// @note when we enable WikibaseRepo on commons, then having NS_MAIN for items
// will be a problem, though commons should be aware that Wikidata items are in
// the main namespace. (see T137444)
$wmgWBNamespaceSettings = [
	'wikidata' => [
		'item' => NS_MAIN,
		'property' => WB_NS_PROPERTY,
		'lexeme' => WB_NS_LEXEME,
	],
];

if ( ( $wgDBname === 'wikidatawiki' || $wgDBname === 'testwikidatawiki' ) && $wmgUseWikibaseRepo ) {
	$wgWBRepoSettings['maxSerializedEntitySize'] = $wmgWikibaseMaxSerializedEntitySize;

	$wgWBRepoSettings['siteLinkGroups'] = $wmgWBSiteLinkGroups;
	$wgWBRepoSettings['specialSiteLinkGroups'] = $wmgWikibaseRepoSpecialSiteLinkGroups;

	$wgWBRepoSettings['entityNamespaces'] = $wmgWBNamespaceSettings['wikidata'];

	// These settings can be overridden by the cron parameters in operations/puppet
	$wgWBRepoSettings['dispatchDefaultDispatchInterval'] = 30;
	if ( $wgDBname === 'wikidatawiki' ) {
		// Current puppet cron is every 3 mins
		$wgWBRepoSettings['dispatchMaxTime'] = 360; // 6 mins
		$wgWBRepoSettings['dispatchDefaultBatchSize'] = 420;
		$wgWBRepoSettings['dispatchDefaultDispatchInterval'] = 25;
	}
	if ( $wgDBname === 'testwikidatawiki' ) {
		// Current puppet cron is every 15 mins
		$wgWBRepoSettings['dispatchMaxTime'] = 900; // 15 mins
		$wgWBRepoSettings['dispatchDefaultBatchSize'] = 200;
	}

	$wgWBRepoSettings['statementSections'] = [
		'item' => [
			'statements' => null,
			'identifiers' => [
				'type' => 'dataType',
				'dataTypes' => [ 'external-id' ],
			],
		],
	];

	$wgWBRepoSettings['normalizeItemByTitlePageNames'] = true;

	$wgWBRepoSettings['dataRightsText'] = 'Creative Commons CC0 License';
	$wgWBRepoSettings['dataRightsUrl'] = 'https://creativecommons.org/publicdomain/zero/1.0/';

	if ( $wgDBname === 'testwikidatawiki' ) {
		$wgWBRepoSettings['clientDbList'] = [ 'testwiki', 'test2wiki', 'testwikidatawiki' ];
	} else {
		$wgWBRepoSettings['clientDbList'] = array_diff(
			MWWikiversions::readDbListFile( 'wikidataclient' ),
			[ 'testwikidatawiki', 'testwiki', 'test2wiki' ]
		);
		// Exclude closed wikis
		$wgWBRepoSettings['clientDbList'] = array_diff(
			$wgWBRepoSettings['clientDbList'],
			MWWikiversions::readDbListFile( $wmfRealm === 'labs' ? 'closed-labs' : 'closed' )
		);
		// Exclude non-existent wikis in labs
		if ( $wmfRealm === 'labs' ) {
			$wgWBRepoSettings['clientDbList'] = array_intersect(
				$wgWBRepoSettings['clientDbList'],
				MWWikiversions::readDbListFile( 'all-labs' )
			);
		}
	}

	$wgWBRepoSettings['localClientDatabases'] = array_combine(
		$wgWBRepoSettings['clientDbList'],
		$wgWBRepoSettings['clientDbList']
	);

	// T53637 and T48953
	$wgGroupPermissions['*']['property-create'] = ( $wgDBname === 'testwikidatawiki' );

	$wgWBRepoSettings['dataSquidMaxage'] = 1 * 60 * 60;
	$wgWBRepoSettings['sharedCacheDuration'] = 60 * 60 * 24;
	$wgWBRepoSettings['sharedCacheKeyPrefix'] = $wmgWBSharedCacheKey;

	$wgPropertySuggesterMinProbability = 0.069;

	// Don't try to let users answer captchas if they try to add links
	// on either Item or Property pages. T86453
	$wgCaptchaTriggersOnNamespace[NS_MAIN]['addurl'] = false;
	$wgCaptchaTriggersOnNamespace[WB_NS_PROPERTY]['addurl'] = false;

	$wgWBRepoSettings['dispatchingLockManager'] = $wmgWikibaseDispatchingLockManager;
	// Cirrus usage for wbsearchentities is on
	$wgWBRepoSettings['entitySearch']['useCirrus'] = true;

	// T178180
	$wgWBRepoSettings['canonicalUriProperty'] = 'P1921';
	require_once "{$wmfConfigDir}/WikibaseSearchSettings.php";
}

if ( $wmgUseWikibaseClient ) {
	$wgWBClientSettings['maxSerializedEntitySize'] = $wmgWikibaseMaxSerializedEntitySize;

	$wgWBClientSettings['siteLinkGroups'] = $wmgWBSiteLinkGroups;
	$wgWBClientSettings['specialSiteLinkGroups'] = $wmgWikibaseClientSpecialSiteLinkGroups;

	$wgWBClientSettings['entityNamespaces'] = $wmgWBNamespaceSettings['wikidata'];

	// to be safe, keeping this here although $wgDBname is default setting
	$wgWBClientSettings['siteGlobalID'] = $wgDBname;

	// Note: Wikibase-production.php overrides this for the test wikis
	$wgWBClientSettings['changesDatabase'] = 'wikidatawiki';
	$wgWBClientSettings['repoDatabase'] = 'wikidatawiki';

	$wgWBClientSettings['repoNamespaces'] = [
		'item' => '',
		'property' => 'Property'
	];

	$wbSiteGroup = isset( $wmgWikibaseSiteGroup ) ? $wmgWikibaseSiteGroup : null;
	$wgWBClientSettings['languageLinkSiteGroup'] = $wbSiteGroup;

	if ( in_array( $wgDBname, [ 'commonswiki', 'mediawikiwiki', 'metawiki', 'specieswiki' ] ) ) {
		$wgWBClientSettings['languageLinkSiteGroup'] = 'wikipedia';
	}

	$wgWBClientSettings['siteGroup'] = $wbSiteGroup;

	$wgWBClientSettings['excludeNamespaces'] = function () {
		global $wgDBname;

		// @fixme 102 is LiquidThread comments on wikinews and elsewhere?
		// but is the Extension: namespace on mediawiki.org, so we need
		// to allow wiki-specific settings here.
		$excludeNamespaces = array_merge(
			MWNamespace::getTalkNamespaces(),
			// 90 => LiquidThread threads
			// 92 => LiquidThread summary
			// 118 => Draft
			// 1198 => NS_TRANSLATE
			// 2600 => Flow topic
			[ NS_USER, NS_FILE, NS_MEDIAWIKI, 90, 92, 118, 1198, 2600 ]
		);

		if ( in_array( $wgDBname, MWWikiversions::readDbListFile( 'wiktionary' ) ) ) {
			$excludeNamespaces[] = NS_MAIN;
			$excludeNamespaces[] = 114; // citations ns
		}

		return $excludeNamespaces;
	};

	if ( $wgDBname === 'wikidatawiki' || $wgDBname === 'testwikidatawiki' ) {
		$wgWBClientSettings['namespaces'] = [
			NS_CATEGORY,
			NS_PROJECT,
			NS_TEMPLATE,
			NS_HELP,
			828 // NS_MODULE
		];

		$wgWBClientSettings['languageLinkSiteGroup'] = 'wikipedia';
		$wgWBClientSettings['showExternalRecentChanges'] = false;
	}

	$wgWBClientSettings['allowLocalShortDesc'] = $wmgWikibaseAllowLocalShortDesc;
	$wgWBClientSettings['allowDataTransclusion'] = $wmgWikibaseEnableData;
	$wgWBClientSettings['allowDataAccessInUserLanguage'] = $wmgWikibaseAllowDataAccessInUserLanguage;
	$wgWBClientSettings['entityAccessLimit'] = $wmgWikibaseEntityAccessLimit;
	$wgWBClientSettings['injectRecentChanges'] = $wmgWikibaseClientInjectRecentChanges;

	$wgWBClientSettings['sharedCacheKeyPrefix'] = $wmgWBSharedCacheKey;
	$wgWBClientSettings['sharedCacheDuration'] = 60 * 60 * 24;

	$wgWBClientSettings['entityUsageModifierLimits'] = [ 'D' => 10, 'L' => 10, 'C' => 33 ];
}

// On commons do not yet register any entity types.
if ( $wgDBname === 'commonswiki' && $wmgUseWikibaseMediaInfo ) {
	$wgWBRepoSettings['entityNamespaces'] = [];
}

unset( $wmgWBSharedCacheKey );

require_once "{$wmfConfigDir}/Wikibase-{$wmfRealm}.php";
