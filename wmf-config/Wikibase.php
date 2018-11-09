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

// Define the namespace indexes for repos and clients
// NOTE: do *not* define WB_NS_ITEM and WB_NS_ITEM_TALK when using a core namespace for items!!..
define( 'WB_NS_PROPERTY', 120 );
define( 'WB_NS_PROPERTY_TALK', 121 );
define( 'WB_NS_LEXEME', 146 );
define( 'WB_NS_LEXEME_TALK', 147 );
// TODO the query namespace is not actually used in prod. Remove me?
define( 'WB_NS_QUERY', 122 );
define( 'WB_NS_QUERY_TALK', 123 );

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
	if ( $wgDBname === 'wikidatawiki' ) {
		// Disable Special:ItemDisambiguation on wikidata.org T195756
		$wgSpecialPages['ItemDisambiguation'] = 'SpecialBlankpage';
	}

	if ( $wgDBname === 'wikidatawiki' || $wgDBname === 'testwikidatawiki' ) {
		// Don't try to let users answer captchas if they try to add links
		// on either Item or Property pages. T86453
		$wgCaptchaTriggersOnNamespace[NS_MAIN]['addurl'] = false;
		$wgCaptchaTriggersOnNamespace[WB_NS_PROPERTY]['addurl'] = false;

		// T53637 and T48953
		$wgGroupPermissions['*']['property-create'] = ( $wgDBname === 'testwikidatawiki' );

		// Load search settings only on wikidata repos for now
		require_once "{$wmfConfigDir}/WikibaseSearchSettings.php";
	}

	// Calculate the client Db lists based on our wikiversions db lists
	if ( $wgDBname === 'testwikidatawiki' ) {
		$wgWBRepoSettings['clientDbList'] = [ 'testwiki', 'test2wiki', 'testwikidatawiki' ];
	} elseif ( $wgDBname === 'wikidatawiki' ) {
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
	} elseif ( $wgDBname === 'commonswiki' ) {
		$wgWBRepoSettings['clientDbList'] = [];
	}

	$wgWBRepoSettings['localClientDatabases'] = array_combine(
		$wgWBRepoSettings['clientDbList'],
		$wgWBRepoSettings['clientDbList']
	);

	$wgWBRepoSettings['entityNamespaces'] = $wmgWikibaseRepoEntityNamespaces;
	$wgWBRepoSettings['idBlacklist'] = $wmgWikibaseIdBlacklist;
	$wgWBRepoSettings['disabledDataTypes'] = $wmgWikibaseDisabledDataTypes;
	$wgWBRepoSettings['tmpMaxItemIdForNewItemIdHtmlFormatter'] = $wmgWikibaseMaxItemIdForNewItemIdHtmlFormatter;
	$wgWBRepoSettings['entityDataFormats'] = $wmgWikibaseEntityDataFormats;
	$wgWBRepoSettings['maxSerializedEntitySize'] = $wmgWikibaseMaxSerializedEntitySize;
	$wgWBRepoSettings['siteLinkGroups'] = $wmgWBSiteLinkGroups;
	$wgWBRepoSettings['specialSiteLinkGroups'] = $wmgWikibaseRepoSpecialSiteLinkGroups;
	$wgWBRepoSettings['statementSections'] = $wmgWikibaseRepoStatementSections;
	$wgWBRepoSettings['badgeItems'] = $wmgWikibaseRepoBadgeItems;
	$wgWBRepoSettings['preferredGeoDataProperties'] = $wmgWBRepoPreferredGeoDataProperties;
	$wgWBRepoSettings['preferredPageImagesProperties'] = $wmgWBRepoPreferredPageImagesProperties;

	// Various settings have null / no setting yet for various sites,
	// so we need to check they are set before trying to use them to avoid warnings.
	if ( isset( $wmgWBRepoSettingsSparqlEndpoint ) ) {
		$wgWBRepoSettings['sparqlEndpoint'] = $wmgWBRepoSettingsSparqlEndpoint;
	}
	if ( isset( $wmgWBRepoFormatterUrlProperty ) ) {
		$wgWBRepoSettings['formatterUrlProperty'] = $wmgWBRepoFormatterUrlProperty;
	}
	if ( isset( $wmgWBRepoCanonicalUriProperty ) ) {
		$wgWBRepoSettings['canonicalUriProperty'] = $wmgWBRepoCanonicalUriProperty; // T178180
	}

	$wgWBRepoSettings['normalizeItemByTitlePageNames'] = true;

	$wgWBRepoSettings['dataRightsText'] = 'Creative Commons CC0 License';
	$wgWBRepoSettings['dataRightsUrl'] = 'https://creativecommons.org/publicdomain/zero/1.0/';

	$wgWBRepoSettings['dataSquidMaxage'] = 1 * 60 * 60;
	$wgWBRepoSettings['sharedCacheDuration'] = 60 * 60 * 24;
	$wgWBRepoSettings['sharedCacheKeyPrefix'] = $wmgWBSharedCacheKey;

	// Cirrus usage for wbsearchentities is on
	$wgWBRepoSettings['entitySearch']['useCirrus'] = true;

	// T189776, T189777
	$wgWBRepoSettings['useTermsTableSearchFields'] = false;

	// These settings can be overridden by the cron parameters in operations/puppet
	$wgWBRepoSettings['dispatchingLockManager'] = $wmgWikibaseDispatchingLockManager;
	$wgWBRepoSettings['dispatchDefaultDispatchInterval'] = $wmgWikibaseDispatchInterval;
	$wgWBRepoSettings['dispatchMaxTime'] = $wmgWikibaseDispatchMaxTime;
	$wgWBRepoSettings['dispatchDefaultBatchSize'] = $wmgWikibaseDispatchDefaultBatchSize;
	$wgWBRepoSettings['dispatchLagToMaxLagFactor'] = 60;

	$wgWBRepoSettings['unitStorage'] = [
		'class' => '\\Wikibase\\Lib\\Units\\JsonUnitStorage',
		'args' => [ __DIR__ . '/unitConversionConfig.json' ]
	];

	if ( isset( $wmgWikibaseUseSSRTermbox ) && isset( $wmgWikibaseUseSSRTermbox ) ) {
		$wgWBRepoSettings['termboxEnabled'] = $wmgWikibaseUseSSRTermbox;
		$wgWBRepoSettings['ssrServerUrl'] = $wmgWikibaseSSRTermboxServerUrl;
	}
}

if ( $wmgUseWikibaseClient ) {
	$wbSiteGroup = isset( $wmgWikibaseSiteGroup ) ? $wmgWikibaseSiteGroup : null;
	$wgWBClientSettings['languageLinkSiteGroup'] = $wbSiteGroup;

	if ( in_array( $wgDBname, [ 'commonswiki', 'mediawikiwiki', 'metawiki', 'specieswiki' ] ) ) {
		$wgWBClientSettings['languageLinkSiteGroup'] = 'wikipedia';
	}

	$wgWBClientSettings['siteGroup'] = $wbSiteGroup;

	if ( $wgDBname === 'wikidatawiki' || $wgDBname === 'testwikidatawiki' ) {
		$wgWBClientSettings['languageLinkSiteGroup'] = 'wikipedia';
		$wgWBClientSettings['showExternalRecentChanges'] = false;
	}
	// TODO clean up slightly messy client only config in this condition above this line

	// to be safe, keeping this here although $wgDBname is default setting
	$wgWBClientSettings['siteGlobalID'] = $wgDBname;

	$wgWBClientSettings['entityNamespaces'] = $wmgWikibaseClientEntityNamespaces;
	$wgWBClientSettings['repoNamespaces'] = $wmgWikibaseClientRepoNamespaces;
	$wgWBClientSettings['namespaces'] = $wmgWikibaseClientNamespacesWithRepoAccess;

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

	// IS-labs.php has null as the default beta doesn't have a defined property order url.
	// To avoid an undefined variable wrap this in a condition.
	if ( isset( $wmgWikibaseClientPropertyOrderUrl ) ) {
		$wgWBClientSettings['propertyOrderUrl'] = $wmgWikibaseClientPropertyOrderUrl;
	}

	$wgWBClientSettings['changesDatabase'] = $wmgWikibaseClientChangesDatabase;
	$wgWBClientSettings['repoDatabase'] = $wmgWikibaseClientRepoDatabase;
	$wgWBClientSettings['repoUrl'] = $wmgWikibaseClientRepoUrl;
	$wgWBClientSettings['repoConceptBaseUri'] = $wmgWikibaseClientRepoConceptBaseUri;
	$wgWBClientSettings['repositories'] = $wmgWikibaseClientRepositories;
	$wgWBClientSettings['wikiPageUpdaterDbBatchSize'] = 20;

	$wgWBClientSettings['disabledAccessEntityTypes'] = $wmgWikibaseDisabledAccessEntityTypes;
	$wgWBClientSettings['maxSerializedEntitySize'] = $wmgWikibaseMaxSerializedEntitySize;

	$wgWBClientSettings['siteLinkGroups'] = $wmgWBSiteLinkGroups;
	$wgWBClientSettings['specialSiteLinkGroups'] = $wmgWikibaseClientSpecialSiteLinkGroups;
	$wgWBClientSettings['badgeClassNames'] = $wmgWikibaseClientBadgeClassNames;

	$wgWBClientSettings['allowLocalShortDesc'] = $wmgWikibaseAllowLocalShortDesc;
	$wgWBClientSettings['allowDataTransclusion'] = $wmgWikibaseEnableData;
	$wgWBClientSettings['allowDataAccessInUserLanguage'] = $wmgWikibaseAllowDataAccessInUserLanguage;
	$wgWBClientSettings['entityAccessLimit'] = $wmgWikibaseEntityAccessLimit;
	$wgWBClientSettings['injectRecentChanges'] = $wmgWikibaseClientInjectRecentChanges;

	$wgWBClientSettings['sharedCacheKeyPrefix'] = $wmgWBSharedCacheKey;
	$wgWBClientSettings['sharedCacheDuration'] = 60 * 60 * 24;

	// T142103
	$wgWBClientSettings['sendEchoNotification'] = true;
	$wgWBClientSettings['echoIcon'] = $wmgWikibaseClientEchoIcon;

	$wgWBClientSettings['useTermsTableSearchFields'] = $wmgWikibaseClientUseTermsTableSearchFields;

	$wgWBClientSettings['disabledUsageAspects'] = $wmgWikibaseDisabledUsageAspects;
	$wgWBClientSettings['fineGrainedLuaTracking'] = $wmgWikibaseFineGrainedLuaTracking;
	$wgWBClientSettings['entityUsageModifierLimits'] = [ 'D' => 10, 'L' => 10, 'C' => 33 ];

	// T208763
	if ( isset( $wmgWikibaseClientPageSchemaNamespaces ) ) {
		$wgWBClientSettings['pageSchemaNamespaces'] = $wmgWikibaseClientPageSchemaNamespaces;
	}

	if ( isset( $wmgWikibaseClientPageSchemaSplitTestSamplingRatio ) ) {
		$wgWBClientSettings['pageSchemaSplitTestSamplingRatio'] = $wmgWikibaseClientPageSchemaSplitTestSamplingRatio;
	}

	if ( isset( $wmgWikibaseClientPageSchemaSplitTestBuckets ) ) {
		$wgWBClientSettings['pageSchemaSplitTestBuckets'] = $wmgWikibaseClientPageSchemaSplitTestBuckets;
	}
}

unset( $wmgWBSharedCacheKey );
