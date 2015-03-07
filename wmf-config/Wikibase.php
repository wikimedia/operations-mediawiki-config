<?php

if ( $wmfRealm === 'labs' ) {
	define( 'WB_EXPERIMENTAL_FEATURES', 1 );
}

require_once( "$IP/extensions/Wikidata/Wikidata.php" );

// The version number now comes from the Wikidata build,
// included above, so that cache invalidations can be in sync
// extension changes when there is a new extension branch or
// otherwise needed to change the cache key.
$wgWBSharedCacheKey = '-' . $wmgWikibaseCachePrefix;

if ( defined( 'HHVM_VERSION' ) ) {
	// Split the cache up for hhvm. Bug 71461
	$wgWBSharedCacheKey .= '-hhvm';
}

$wgWBSharedSettings = array();

$wgWBSharedSettings['siteLinkGroups'] = array(
	'wikipedia',
	'wikibooks',
	'wikinews',
	'wikiquote',
	'wikisource',
	'wikivoyage',
	'special'
);

$wgWBSharedSettings['specialSiteLinkGroups'] = array( 'commons' );
if ( in_array( $wgDBname, array( 'test2wiki', 'testwiki', 'testwikidatawiki' ) ) ) {
	$wgWBSharedSettings['specialSiteLinkGroups'][] = 'testwikidata';
} else {
	$wgWBSharedSettings['specialSiteLinkGroups'][] = 'wikidata';
}

$wgWBSharedSettings['useLegacyChangesSubscription'] = true;

if ( $wmgUseWikibaseRepo ) {
	$baseNs = 120;

	// Define the namespace indexes
	define( 'WB_NS_PROPERTY', $baseNs );
	define( 'WB_NS_PROPERTY_TALK', $baseNs + 1 );
	define( 'WB_NS_QUERY', $baseNs + 2 );
	define( 'WB_NS_QUERY_TALK', $baseNs + 3 );

	$wgNamespaceAliases['Item'] = NS_MAIN;
	$wgNamespaceAliases['Item_talk'] = NS_TALK;

	// Define the namespaces
	$wgExtraNamespaces[WB_NS_PROPERTY] = 'Property';
	$wgExtraNamespaces[WB_NS_PROPERTY_TALK] = 'Property_talk';
	$wgExtraNamespaces[WB_NS_QUERY] = 'Query';
	$wgExtraNamespaces[WB_NS_QUERY_TALK] = 'Query_talk';

	$wgWBRepoSettings = $wgWBSharedSettings + $wgWBRepoSettings;

	// Assigning the correct content models to the namespaces
	$wgWBRepoSettings['entityNamespaces'][CONTENT_MODEL_WIKIBASE_ITEM] = NS_MAIN;
	$wgWBRepoSettings['entityNamespaces'][CONTENT_MODEL_WIKIBASE_PROPERTY] = WB_NS_PROPERTY;

	$wgWBRepoSettings['normalizeItemByTitlePageNames'] = true;

	$wgWBRepoSettings['dataRightsText'] = 'Creative Commons CC0 License';
	$wgWBRepoSettings['dataRightsUrl'] = 'https://creativecommons.org/publicdomain/zero/1.0/';

	if ( $wgDBname === 'testwikidatawiki' ) {
		// there is no cronjob dispatcher yet, this will do nothing
		$wgWBRepoSettings['clientDbList'] = array( 'testwiki', 'test2wiki', 'testwikidatawiki' );
	} else {
		$wgWBRepoSettings['clientDbList'] = array_diff(
			array_map(
				'trim',
				file( getRealmSpecificFilename( "$IP/../wikidataclient.dblist" ) )
			),
			array( 'testwikidatawiki', 'testwiki', 'test2wiki' )
		);
		// Exclude closed wikis
		$wgWBRepoSettings['clientDbList'] = array_diff(
			$wgWBRepoSettings['clientDbList'],
			array_map(
				'trim',
				file( getRealmSpecificFilename( "$IP/../closed.dblist" ) )
			)
		);
	}

	$wgWBRepoSettings['localClientDatabases'] = array_combine(
		$wgWBRepoSettings['clientDbList'],
		$wgWBRepoSettings['clientDbList']
	);

	// Bug 51637 and 46953
	$wgGroupPermissions['*']['property-create'] = ( $wgDBname === 'testwikidatawiki' );

	$wgCacheEpoch = '20150224222223';

	$wgWBRepoSettings['dataSquidMaxage'] = 1 * 60 * 60;
	$wgWBRepoSettings['sharedCacheDuration'] = 60 * 60 * 24;
	$wgWBRepoSettings['sharedCacheKeyPrefix'] .= $wgWBSharedCacheKey;

	$wgPropertySuggesterMinProbability = 0.069;

	// Bug 70346
	$wgPropertySuggesterDeprecatedIds = array(
		107, // (OBSOLETE) main type (GND)
		143, // imported from
		357, // (OBSOLETE) title (use P1476)
		392, // (OBSOLETE) subtitle (use P1680)
		438, // (OBSOLETE) inscription (use P1684)
		513, // birth name (Deprecated)
		741, // (OBSOLETE) playing hand
		1134, // (OBSOLETE) located in place (use P276)
	);

	// Don't try to let users answer captchas if they try to add links
	// on either Item or Property pages. Bug T86453
	$wgCaptchaTriggersOnNamespace[NS_MAIN]['addurl'] = false;
	$wgCaptchaTriggersOnNamespace[WB_NS_PROPERTY]['addurl'] = false;
}

if ( $wmgUseWikibaseClient ) {
	$wgWBClientSettings = $wgWBSharedSettings + $wgWBClientSettings;

	// to be safe, keeping this here although $wgDBname is default setting
	$wgWBClientSettings['siteGlobalID'] = $wgDBname;

	if ( in_array( $wgDBname, array( 'test2wiki', 'testwiki', 'testwikidatawiki' ) ) ) {
		$wgWBClientSettings['changesDatabase'] = 'testwikidatawiki';
		$wgWBClientSettings['repoDatabase'] = 'testwikidatawiki';
		$wgWBClientSettings['repoUrl'] = "//test.wikidata.org";
	} else {
		$wgWBClientSettings['changesDatabase'] = 'wikidatawiki';
		$wgWBClientSettings['repoDatabase'] = 'wikidatawiki';
		$wgWBClientSettings['repoUrl'] = "//{$wmfHostnames['wikidata']}";
	}

	$wgWBClientSettings['repoNamespaces'] = array(
		'wikibase-item' => '',
		'wikibase-property' => 'Property'
	);

	$wgWBClientSettings['languageLinkSiteGroup'] = $wmgWikibaseSiteGroup;

	if ( $wgDBname === 'commonswiki' ) {
		$wgWBClientSettings['languageLinkSiteGroup'] = 'wikipedia';
	}

	$wgWBClientSettings['siteGroup'] = $wmgWikibaseSiteGroup;
	$wgWBClientSettings['otherProjectsLinksBeta'] = true;

	$wgWBClientSettings['excludeNamespaces'] = function() {
		return array_merge(
			MWNamespace::getTalkNamespaces(),
			// 1198 => NS_TRANSLATE
			array( NS_USER, NS_FILE, NS_MEDIAWIKI, 1198 )
		);
	};

	if ( $wgDBname === 'wikidatawiki' || $wgDBname === 'testwikidatawiki' ) {
		$wgWBClientSettings['namespaces'] = array(
			NS_CATEGORY,
			NS_PROJECT,
			NS_TEMPLATE,
			NS_HELP
		);

		$wgWBClientSettings['languageLinkSiteGroup'] = 'wikipedia';
		$wgWBClientSettings['injectRecentChanges'] = false;
		$wgWBClientSettings['showExternalRecentChanges'] = false;
	}

	foreach( $wmgWikibaseClientSettings as $setting => $value ) {
		$wgWBClientSettings[$setting] = $value;
	}

	$wgWBClientSettings['allowDataTransclusion'] = $wmgWikibaseEnableData;
	$wgWBClientSettings['allowArbitraryDataAccess'] = $wmgWikibaseEnableArbitraryAccess;
	$wgWBClientSettings['useLegacyUsageIndex'] = $wmgWikibaseUseLegacyUsageIndex;

	$wgWBClientSettings['sharedCacheKeyPrefix'] .= $wgWBSharedCacheKey;
	$wgWBClientSettings['sharedCacheDuration'] = 60 * 60 * 24;
}

require_once( getRealmSpecificFilename( "$wmfConfigDir/Wikibase.php" ) );
