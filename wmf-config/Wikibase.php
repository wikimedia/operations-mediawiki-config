<?php

// Load the Repo extensions
if ( !empty( $wmgUseWikibaseRepo ) ) {
	include_once "$IP/extensions/Wikibase/repo/Wikibase.php";
	wfLoadExtension( 'Wikidata.org' );
	wfLoadExtension( 'PropertySuggester' );
	wfLoadExtension( 'WikibaseQuality' );
	wfLoadExtension( 'WikibaseQualityConstraints' );
	if ( !empty( $wmgUseWikibaseLexeme ) ) {
		wfLoadExtension( 'WikibaseLexeme' );
	}
}

// Load the Client extensions
if ( !empty( $wmgUseWikibaseClient ) ) {
	include_once "$IP/extensions/Wikibase/client/WikibaseClient.php";
	wfLoadExtension( 'WikimediaBadges' );
	if ( !empty( $wmgUseArticlePlaceholder ) ) {
		wfLoadExtension( 'ArticlePlaceholder' );
	}
	if ( !empty( $wmgUseWikibaseLexeme ) ) {
		wfLoadExtension( 'WikibaseLexeme' );
	}
}

if ( $wmgUseWikibaseRepo ) {
	$wgWBRepoSettings['idBlacklist'] = $wmgWikibaseIdBlacklist;
	$wgWBRepoSettings['disabledDataTypes'] = $wmgWikibaseDisabledDataTypes;
	$wgWBRepoSettings['disabledRdfExportEntityTypes'] = $wmgWikibaseDisabledRdfExportEntityTypes;
}

if ( $wmgUseWikibaseClient ) {
	$wgWBClientSettings['disabledAccessEntityTypes'] = $wmgWikibaseDisabledAccessEntityTypes;
}

// This allows cache invalidations to be in sync with deploys
// and not shared across different versions of wikibase.
// e.g. wikibase_shared/1_31_0-wmf_2-testwikidatawiki0 for test wikis
// and wikibase_shared/1_31_0-wmf_2-wikidatawiki for all others.
$wgWBSharedCacheKey = 'wikibase_shared/' . str_replace( '.', '_', $wmgVersionNumber ) . '-' . $wmgWikibaseCachePrefix;

if ( defined( 'HHVM_VERSION' ) ) {
	// Split the cache up for hhvm. T73461
	$wgWBSharedCacheKey .= '-hhvm';
}

$wgWBSharedSettings = [];

$wgWBSharedSettings['maxSerializedEntitySize'] = 2500;

$wgWBSharedSettings['siteLinkGroups'] = [
	'wikipedia',
	'wikibooks',
	'wikinews',
	'wikiquote',
	'wikisource',
	'wikiversity',
	'wikivoyage',
	'wiktionary',
	'special'
];

$wgWBSharedSettings['specialSiteLinkGroups'] = [
	'commons',
	'mediawiki',
	'meta',
	'species'
];

$baseNs = 120;

// Define the namespace indexes for repo (and client wikis also need to be aware of these,
// thus entityNamespaces need to be a shared setting).
//
// NOTE: do *not* define WB_NS_ITEM and WB_NS_ITEM_TALK when using a core namespace for items!
define( 'WB_NS_PROPERTY', $baseNs );
define( 'WB_NS_PROPERTY_TALK', $baseNs + 1 );
define( 'WB_NS_QUERY', $baseNs + 2 );
define( 'WB_NS_QUERY_TALK', $baseNs + 3 );

// Tell Wikibase which namespace to use for which type of entities
// @note when we enable WikibaseRepo on commons, then having NS_MAIN for items
// will be a problem, though commons should be aware that Wikidata items are in
// the main namespace. (see T137444)
$wgWBSharedSettings['entityNamespaces'] = [
	'item' => NS_MAIN,
	'property' => WB_NS_PROPERTY
];

if ( in_array( $wgDBname, [ 'test2wiki', 'testwiki', 'testwikidatawiki' ] ) ) {
	$wgWBSharedSettings['specialSiteLinkGroups'][] = 'testwikidata';
	$wgWBSharedSettings['specialSiteLinkGroups'][] = 'test';
	$wgWBSharedSettings['specialSiteLinkGroups'][] = 'test2';
} else {
	$wgWBSharedSettings['specialSiteLinkGroups'][] = 'wikidata';
}

if ( $wmgUseWikibaseRepo ) {
	$wgNamespaceAliases['Item'] = NS_MAIN;
	$wgNamespaceAliases['Item_talk'] = NS_TALK;

	// Define the namespaces
	$wgExtraNamespaces[WB_NS_PROPERTY] = 'Property';
	$wgExtraNamespaces[WB_NS_PROPERTY_TALK] = 'Property_talk';
	$wgExtraNamespaces[WB_NS_QUERY] = 'Query';
	$wgExtraNamespaces[WB_NS_QUERY_TALK] = 'Query_talk';

	$wgWBRepoSettings = $wgWBSharedSettings + $wgWBRepoSettings;

	if ( $wgDBname === 'wikidatawiki' ) {
		// These settings can be overridden by the cron parameters in operations/puppet
		$wgWBRepoSettings['dispatchMaxTime'] = 720;
		$wgWBRepoSettings['dispatchDefaultBatchSize'] = 420;
		$wgWBRepoSettings['dispatchDefaultDispatchInterval'] = 25;
		$wgWBRepoSettings['dispatchDefaultDispatchRandomness'] = 15;
	}
	if ( $wgDBname === 'testwikidatawiki' ) {
		// These settings can be overridden by the cron parameters in operations/puppet
		$wgWBRepoSettings['dispatchMaxTime'] = 900;
		$wgWBRepoSettings['dispatchDefaultBatchSize'] = 200;
		$wgWBRepoSettings['dispatchDefaultDispatchInterval'] = 30;
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

	$wgCacheEpoch = '20170724130500';

	$wgWBRepoSettings['dataSquidMaxage'] = 1 * 60 * 60;
	$wgWBRepoSettings['sharedCacheDuration'] = 60 * 60 * 24;
	$wgWBRepoSettings['sharedCacheKeyPrefix'] = $wgWBSharedCacheKey;

	$wgPropertySuggesterMinProbability = 0.069;

	// Don't try to let users answer captchas if they try to add links
	// on either Item or Property pages. T86453
	$wgCaptchaTriggersOnNamespace[NS_MAIN]['addurl'] = false;
	$wgCaptchaTriggersOnNamespace[WB_NS_PROPERTY]['addurl'] = false;
	if ( $wgDBname === 'testwikidatawiki' ) {
		// These are for testing only, one is Item and one is ExternalId
		$wgWBRepoSettings['searchIndexProperties'] = [ 'P7', 'P700' ];
	} else {
		// Index: instance of, subclass of
		$wgWBRepoSettings['searchIndexProperties'] = [ 'P31', 'P279' ];
	}
	// T163642, T99899
	$wgWBRepoSettings['searchIndexTypes'] = [ 'string', 'external-id' ];

	$wgWBRepoSettings['dispatchingLockManager'] = $wmgWikibaseDispatchingLockManager;
	// Cirrus usage for wbsearchentities is on
	$wgWBRepoSettings['entitySearch']['useCirrus'] = true;

	// T178180
	$wgWBRepoSettings['canonicalUriProperty'] = 'P1921';
	// T176903, T180169
	$wgWBRepoSettings['entitySearch']['useStemming'] = [
		'ar' => [ 'index' => true, 'query' => true ],
		'bg' => [ 'index' => true, 'query' => true ],
		'ca' => [ 'index' => true, 'query' => true ],
		'ckb' => [ 'index' => true, 'query' => true ],
		'cs' => [ 'index' => true, 'query' => true ],
		'da' => [ 'index' => true, 'query' => true ],
		'de' => [ 'index' => true, 'query' => true ],
		'el' => [ 'index' => true, 'query' => true ],
		'en' => [ 'index' => true, 'query' => true ],
		'en-ca' => [ 'index' => true, 'query' => true ],
		'en-gb' => [ 'index' => true, 'query' => true ],
		'es' => [ 'index' => true, 'query' => true ],
		'eu' => [ 'index' => true, 'query' => true ],
		'fa' => [ 'index' => true, 'query' => true ],
		'fi' => [ 'index' => true, 'query' => true ],
		'fr' => [ 'index' => true, 'query' => true ],
		'ga' => [ 'index' => true, 'query' => true ],
		'gl' => [ 'index' => true, 'query' => true ],
		'he' => [ 'index' => true, 'query' => true ],
		'hi' => [ 'index' => true, 'query' => true ],
		'hu' => [ 'index' => true, 'query' => true ],
		'hy' => [ 'index' => true, 'query' => true ],
		'id' => [ 'index' => true, 'query' => true ],
		'it' => [ 'index' => true, 'query' => true ],
		'ja' => [ 'index' => true, 'query' => true ],
		'ko' => [ 'index' => true, 'query' => true ],
		'lt' => [ 'index' => true, 'query' => true ],
		'lv' => [ 'index' => true, 'query' => true ],
		'nb' => [ 'index' => true, 'query' => true ],
		'nl' => [ 'index' => true, 'query' => true ],
		'nn' => [ 'index' => true, 'query' => true ],
		'pl' => [ 'index' => true, 'query' => true ],
		'pt' => [ 'index' => true, 'query' => true ],
		'pt-br' => [ 'index' => true, 'query' => true ],
		'ro' => [ 'index' => true, 'query' => true ],
		'ru' => [ 'index' => true, 'query' => true ],
		'simple' => [ 'index' => true, 'query' => true ],
		'sv' => [ 'index' => true, 'query' => true ],
		'th' => [ 'index' => true, 'query' => true ],
		'tr' => [ 'index' => true, 'query' => true ],
		'uk' => [ 'index' => true, 'query' => true ],
		'zh' => [ 'index' => true, 'query' => true ],
	];

	if ( is_array( $wgCirrusSearchSimilarityProfiles ) ) {
		// TODO: have proper profile management in cirrus
		$wgCirrusSearchSimilarityProfiles['wikibase_similarity'] = [
			'similarity' => [
				'default' => [
					'type' => 'BM25',
				],
				'descriptions' => [
					'type' => 'BM25',
				],
				// This is a bit verbose to redefine always the same settings
				// but the advantage is that you can re-tune and specialize
				// these on an existing index (requires closing the index).
				// "labels" here means the label + aliases
				'labels' => [
					'type' => 'BM25',
					'k1' => 1.2,
					'b' => 0.3,
				],
				// We consider all as being very similar to an array field
				// as it is a simple concatenation of all the item data
				'all' => [
					'type' => 'BM25',
					'k1' => 1.2,
					'b' => 0.3,
				]
			],
			'fields' => [
				'__default__' => 'default',
				'labels' => 'labels',
				'descriptions' => 'descriptions',
				'all' => 'all',
			]
		];
	}
}

if ( $wmgUseWikibaseClient ) {
	$wgWBClientSettings = $wgWBSharedSettings + $wgWBClientSettings;

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
		$wgWBClientSettings['injectRecentChanges'] = false;
		$wgWBClientSettings['showExternalRecentChanges'] = false;
	}

	foreach ( $wmgWikibaseClientSettings as $setting => $value ) {
		$wgWBClientSettings[$setting] = $value;
	}

	$wgWBClientSettings['allowLocalShortDesc'] = $wmgWikibaseAllowLocalShortDesc;
	$wgWBClientSettings['allowDataTransclusion'] = $wmgWikibaseEnableData;
	$wgWBClientSettings['allowDataAccessInUserLanguage'] = $wmgWikibaseAllowDataAccessInUserLanguage;
	$wgWBClientSettings['entityAccessLimit'] = $wmgWikibaseEntityAccessLimit;

	$wgWBClientSettings['sharedCacheKeyPrefix'] = $wgWBSharedCacheKey;
	$wgWBClientSettings['sharedCacheDuration'] = 60 * 60 * 24;

	$wgWBClientSettings['entityUsageModifierLimits'] = [ 'D' => 10, 'L' => 10, 'C' => 33 ];
}

require_once "{$wmfConfigDir}/Wikibase-{$wmfRealm}.php";
