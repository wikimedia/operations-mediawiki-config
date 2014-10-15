<?php

require_once( "$IP/extensions/Wikidata/Wikidata.php" );

$wgWBSharedCacheKeyPrefix = "$wmgWikibaseCachePrefix/WBL-$wmfVersionNumber";

if ( defined( 'HHVM_VERSION' ) ) {
	// Split the cache up for hhvm. Bug 71461
	$wgWBSharedCacheKeyPrefix .= '-hhvm';
}

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

	// Assigning the correct content models to the namespaces
	$wgWBRepoSettings['entityNamespaces'][CONTENT_MODEL_WIKIBASE_ITEM] = NS_MAIN;
	$wgWBRepoSettings['entityNamespaces'][CONTENT_MODEL_WIKIBASE_PROPERTY] = WB_NS_PROPERTY;

	$wgWBRepoSettings['normalizeItemByTitlePageNames'] = true;

	$wgWBRepoSettings['dataRightsText'] = 'Creative Commons CC0 License';
	$wgWBRepoSettings['dataRightsUrl'] = 'https://creativecommons.org/publicdomain/zero/1.0/';

	$wgWBRepoSettings['siteLinkGroups'] = array(
		'wikipedia',
		'wikinews',
		'wikiquote',
		'wikisource',
		'wikivoyage',
		'special'
	);

	$wgWBRepoSettings['specialSiteLinkGroups'] = array( 'commons' );

	if ( $wgDBname === 'testwikidatawiki' ) {
		$wgWBRepoSettings['specialSiteLinkGroups'][] = 'testwikidata';
	} else {
		$wgWBRepoSettings['specialSiteLinkGroups'][] = 'wikidata';
	}

	if ( $wgDBname === 'testwikidatawiki' ) {
		$wgWBRepoSettings['badgeItems'] = array(
			'Q608' => 'wb-badge-goodarticle',
			'Q609' => 'wb-badge-featuredarticle'
		);
	} else {
		$wgWBRepoSettings['badgeItems'] = array(
			'Q17437798' => 'wb-badge-goodarticle',
			'Q17437796' => 'wb-badge-featuredarticle',
			'Q17559452' => 'wb-badge-recommendedarticle', // bug 70268
			'Q17506997' => 'wb-badge-featuredlist' // bug 70332
		);
	}

	if ( $wgDBname === 'testwikidatawiki' ) {
		// there is no cronjob dispatcher yet, this will do nothing
		$wgWBRepoSettings['clientDbList'] = array( 'test2wiki' );
	} else {
		$wgWBRepoSettings['clientDbList'] = array_map(
			'trim',
			file( getRealmSpecificFilename( "$IP/../wikidataclient.dblist" ) )
		);
	}

	$wgWBRepoSettings['localClientDatabases'] = array_combine(
		$wgWBRepoSettings['clientDbList'],
		$wgWBRepoSettings['clientDbList']
	);

	// Bug 51637 and 46953
	$wgGroupPermissions['*']['property-create'] = ( $wgDBname === 'testwikidatawiki' );

	$wgCacheEpoch = $wgDBname === 'testwikidatawiki' ? '20140925181800' : '20140930180500';

	$wgWBRepoSettings['dataSquidMaxage'] = 1 * 60 * 60;
	$wgWBRepoSettings['sharedCacheDuration'] = 60 * 60 * 24;
	$wgWBRepoSettings['sharedCacheKeyPrefix'] = $wgWBSharedCacheKeyPrefix;

	$wgPropertySuggesterMinProbability = 0.071;

	// Bug 70346
	$wgPropertySuggesterDeprecatedIds = array(
		45, // (OBSOLETE) grandparent
		70, // (OBSOLETE) order
		71, // (OBSOLETE) family
		74, // (OBSOLETE) genus
		107, // (OBSOLETE) main type (GND)
		143, // imported from
		643, // Genloc Chr (deprecated, use P1057)
		741, // (OBSOLETE) playing hand
		1384, // deleted
	);
}

if ( $wmgUseWikibaseClient ) {

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

	// used by the sites module
	$wgWBClientSettings['siteLinkGroups'] = array(
		'wikipedia',
		'wikinews',
		'wikiquote',
		'wikisource',
		'wikivoyage',
		'special'
	);

	$wgWBClientSettings['specialSiteLinkGroups'] = array( 'commons' );

	if ( $wgDBname === 'testwikidatawiki' ) {
		$wgWBClientSettings['specialSiteLinkGroups'][] = 'testwikidata';
	} elseif ( $wgDBname === 'wikidatawiki' ) {
		$wgWBClientSettings['specialSiteLinkGroups'][] = 'wikidata';
	}

	if ( $wgDBname === 'commonswiki' ) {
		$wgWBClientSettings['languageLinkSiteGroup'] = 'wikipedia';
	}

	$wgWBClientSettings['siteGroup'] = $wmgWikibaseSiteGroup;
	$wgWBClientSettings['otherProjectsLinksBeta'] = true;

	$wgWBClientSettings['badgeClassNames'] = array(
		'Q17437796' => 'badge-featuredarticle',
		'Q17437798' => 'badge-goodarticle',
		'Q17559452' => 'badge-recommendedarticle', // bug 70268
		'Q17506997' => 'badge-featuredlist' // bug 70332
	);

	$wgHooks['SetupAfterCache'][] = 'wmfWBClientExcludeNS';

	function wmfWBClientExcludeNS() {
		global $wgWBClientSettings;

		$wgWBClientSettings['excludeNamespaces'] = array_merge(
			MWNamespace::getTalkNamespaces(),
			// 1198 => NS_TRANSLATE
			array( NS_USER, NS_FILE, NS_MEDIAWIKI, 1198 )
		);

		return true;
	};

	$wgWBClientSettings['allowArbitraryDataAccess'] = false;

	if ( $wgDBname === 'wikidatawiki' || $wgDBname === 'testwikidatawiki' ) {
		$wgWBClientSettings['allowArbitraryDataAccess'] = true;

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

	$wgWBClientSettings['sharedCacheKeyPrefix'] = $wgWBSharedCacheKeyPrefix;
	$wgWBClientSettings['sharedCacheDuration'] = 60 * 60 * 24;
}
