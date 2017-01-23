<?php

require_once( "$IP/extensions/Wikidata/Wikidata.php" );

// The version number now comes from the Wikidata build,
// included above, so that cache invalidations can be in sync
// extension changes when there is a new extension branch or
// otherwise needed to change the cache key.
$wgWBSharedCacheKey = '-' . $wmgWikibaseCachePrefix;

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
		// there is no cronjob dispatcher yet, this will do nothing
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
	}

	$wgWBRepoSettings['localClientDatabases'] = array_combine(
		$wgWBRepoSettings['clientDbList'],
		$wgWBRepoSettings['clientDbList']
	);

	// T53637 and T48953
	$wgGroupPermissions['*']['property-create'] = ( $wgDBname === 'testwikidatawiki' );

	$wgCacheEpoch = '20160817192300';

	$wgWBRepoSettings['dataSquidMaxage'] = 1 * 60 * 60;
	$wgWBRepoSettings['sharedCacheDuration'] = 60 * 60 * 24;
	$wgWBRepoSettings['sharedCacheKeyPrefix'] .= $wgWBSharedCacheKey;

	$wgPropertySuggesterMinProbability = 0.069;

	// T72346
	$wgPropertySuggesterDeprecatedIds = [
		143, // imported from
		/**
		 * Deprecated properties
		 * @see https://www.wikidata.org/wiki/Special:WhatLinksHere/Q18644427
		 */
		357, // (OBSOLETE) title (use P1476, "title")
		513, // (OBSOLETE) birth name (use P1477)
		/**
		 * @see https://www.wikidata.org/w/index.php?oldid=335040857
		 */
		646, // Freebase ID
		/**
		 * Sandbox properties
		 * @see https://www.wikidata.org/wiki/Special:WhatLinksHere/Q18720640
		 */
		368,  // commonsMedia
		369,  // wikibase-item
		370,  // string
		578,  // time
		626,  // globe-coordinate
		855,  // url
		1106, // quantity
		1450, // monolingualtext
		2368, // wikibase-property
		2535, // math
		2536, // external-id
	];

	// Don't try to let users answer captchas if they try to add links
	// on either Item or Property pages. T86453
	$wgCaptchaTriggersOnNamespace[NS_MAIN]['addurl'] = false;
	$wgCaptchaTriggersOnNamespace[WB_NS_PROPERTY]['addurl'] = false;
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

	$wgWBClientSettings['languageLinkSiteGroup'] = $wmgWikibaseSiteGroup;

	if ( in_array( $wgDBname, [ 'commonswiki', 'mediawikiwiki', 'metawiki', 'specieswiki' ] ) ) {
		$wgWBClientSettings['languageLinkSiteGroup'] = 'wikipedia';
	}

	$wgWBClientSettings['siteGroup'] = $wmgWikibaseSiteGroup;
	$wgWBClientSettings['otherProjectsLinksByDefault'] = true;

	$wgWBClientSettings['excludeNamespaces'] = function() {
		// @fixme 102 is LiquidThread comments on wikinews and elsewhere?
		// but is the Extension: namespace on mediawiki.org, so we need
		// to allow wiki-specific settings here.
		return array_merge(
			MWNamespace::getTalkNamespaces(),
			// 90 => LiquidThread threads
			// 92 => LiquidThread summary
			// 118 => Draft
			// 1198 => NS_TRANSLATE
			// 2600 => Flow topic
			[ NS_USER, NS_FILE, NS_MEDIAWIKI, 90, 92, 118, 1198, 2600 ]
		);
	};

	$wgWBClientSettings['interwikiSortOrders']
		= include( "$wmfConfigDir/InterwikiSortOrders.php" );

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

	foreach( $wmgWikibaseClientSettings as $setting => $value ) {
		$wgWBClientSettings[$setting] = $value;
	}

	$wgWBClientSettings['allowDataTransclusion'] = $wmgWikibaseEnableData;
	$wgWBClientSettings['allowDataAccessInUserLanguage'] = $wmgWikibaseAllowDataAccessInUserLanguage;
	$wgWBClientSettings['entityAccessLimit'] = $wmgWikibaseEntityAccessLimit;

	$wgWBClientSettings['enableStatementsParserFunction'] = true;
	$wgWBClientSettings['enableLuaEntityFormatStatements'] = true;

	$wgWBClientSettings['sharedCacheKeyPrefix'] .= $wgWBSharedCacheKey;
	$wgWBClientSettings['sharedCacheDuration'] = 60 * 60 * 24;
}

require_once "{$wmfConfigDir}/Wikibase-{$wmfRealm}.php";
