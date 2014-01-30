<?php

if ( !$wmgUseWikibaseBuild ) {
	require_once( "$IP/extensions/DataValues/DataValues.php" );
	require_once( "$IP/extensions/DataTypes/DataTypes.php" );
	require_once( "$IP/extensions/Diff/Diff.php" );
	require_once( "$IP/extensions/WikibaseDataModel/WikibaseDataModel.php" );
	require_once( "$IP/extensions/Wikibase/lib/WikibaseLib.php" );
} else {
	require_once( "$IP/extensions/Wikidata/Wikidata.php" );
}

if ( $wmgUseWikibaseRepo ) {
	if ( !$wmgUseWikibaseBuild ) {
		require_once( "$IP/extensions/Wikibase/repo/Wikibase.php" );
	}

	if ( $wmgUseWikibaseBuild ) {
		// bump for wikidatawiki and test wikidata
		// @todo: can move to InitialiseSettings later, but having here
		// helps with timing issues to have this switched same time as
		// wikidata gets switched to 1.23wmf12
		$wgCacheEpoch = '20140130000000';
	}

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

	$wgWBRepoSettings['dataSquidMaxage'] = 1 * 60 * 60;
	$wgWBRepoSettings['sharedCacheDuration'] = 60 * 60 * 24;

	// Assigning the correct content models to the namespaces
	$wgWBRepoSettings['entityNamespaces'][CONTENT_MODEL_WIKIBASE_ITEM] = NS_MAIN;
	$wgWBRepoSettings['entityNamespaces'][CONTENT_MODEL_WIKIBASE_PROPERTY] = WB_NS_PROPERTY;

	$wgWBRepoSettings['normalizeItemByTitlePageNames'] = true;

	$wgWBRepoSettings['datalicensetext'] = 'Creative Commons CC0 License';
	$wgWBRepoSettings['datalicenseurl'] = 'https://creativecommons.org/publicdomain/zero/1.0/';

	$wgWBRepoSettings['specialSiteLinkGroups'] = array( 'commons' );

	$wgWBRepoSettings['siteLinkGroups'] = array(
		'wikipedia',
		'wikisource',
		'wikivoyage',
		'commons'
	);

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

	$wgWBRepoSettings['sharedCacheKeyPrefix'] = "$wmgWikibaseCachePrefix/WBL-$wmfExtendedVersionNumber";
}

if ( $wmgUseWikibaseClient ) {

	if ( !$wmgUseWikibaseBuild ) {
		require_once( "$IP/extensions/Wikibase/client/WikibaseClient.php" );
	}

	$wgWBClientSettings['changesDatabase'] = 'wikidatawiki';
	$wgWBClientSettings['repoDatabase'] = 'wikidatawiki';

	// to be safe, keeping this here although $wgDBname is default setting
	$wgWBClientSettings['siteGlobalID'] = $wgDBname;
	$wgWBClientSettings['repoUrl'] = "//{$wmfHostnames['wikidata']}";

	$wgWBClientSettings['repoNamespaces'] = array(
		'wikibase-item' => '',
		'wikibase-property' => 'Property'
	);

	$wgWBClientSettings['siteLinkGroups'] = array(
		'wikipedia',
		'wikisource',
		'wikivoyage',
		'commons'
	);

	$wgWBClientSettings['sharedCacheDuration'] = 60 * 60 * 24;

	if ( $wgDBname === 'commonswiki' ) {
		$wgWBClientSettings['languageLinkSiteGroup'] = 'wikipedia';
	}

	$wgWBClientSettings['siteGroup'] = $wmgWikibaseSiteGroup;

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

	foreach( $wmgWikibaseClientSettings as $setting => $value ) {
		$wgWBClientSettings[$setting] = $value;
	}

	$wgWBClientSettings['allowDataTransclusion'] = $wmgWikibaseEnableData;
	$wgWBClientSettings['sharedCacheKeyPrefix'] = "$wmgWikibaseCachePrefix/WBL-$wmfExtendedVersionNumber";
}
