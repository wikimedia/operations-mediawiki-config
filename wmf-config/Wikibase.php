<?php

require_once( "$IP/extensions/DataValues/DataValues.php" );

// DataTypes is being moved out of the DataValues git repo.
// this allows DataTypes in DataValues to be used if it exists, such as older code.
// if it is not found in DataValues, then load it below.
//
// For localisation update, there is an empty "dummy" branch of DataTypes
// so that localisation update can run okay.
if ( !defined( 'DataTypes_VERSION' ) ) {
	require_once( "$IP/extensions/DataTypes/DataTypes.php" );
}

require_once( "$IP/extensions/Diff/Diff.php" );
require_once( "$IP/extensions/WikibaseDataModel/WikibaseDataModel.php" );
require_once( "$IP/extensions/Wikibase/lib/WikibaseLib.php" );

if ( $wmgUseWikibaseRepo ) {
	require_once( "$IP/extensions/Wikibase/repo/Wikibase.php" );

	$baseNs = 120;

	// Define the namespace indexes
	define( 'WB_NS_PROPERTY', $baseNs );
	define( 'WB_NS_PROPERTY_TALK', $baseNs + 1 );
	define( 'WB_NS_QUERY', $baseNs + 2 );
	define( 'WB_NS_QUERY_TALK', $baseNs + 3 );

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

	$wgWBRepoSettings['specialSiteLinkGroups'] = array( 'commons' );

	$wgWBRepoSettings['siteLinkGroups'] = array(
		'wikipedia',
		'wikivoyage',
		'commons'
	);

	$wgWBRepoSettings['usePropertyInfoTable'] = true;

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
	require_once( "$IP/extensions/Wikibase/client/WikibaseClient.php" );

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
		'wikivoyage',
		'commons'
	);

	$wgWBClientSettings['withoutTermWeight'] = false;
	$wgWBClientSettings['usePropertyInfoTable'] = true;
	$wgWBClientSettings['sharedCacheDuration'] = 60 * 60 * 24;
	$wgWBClientSettings['enableSiteLinkWidget'] = true;


	if ( $wgDBname === 'commonswiki' ) {
		$wgWBClientSettings['languageLinkSiteGroup'] = 'wikipedia';
		$wgWBClientSettings['allowDataTransclusion'] = false;

		// per bug 54497, widget does not work yet for commons
		$wgWBClientSettings['enableSiteLinkWidget'] = false;
	}

	$wgWBClientSettings['siteGroup'] = $wmgWikibaseSiteGroup;

	$wgHooks['SetupAfterCache'][] = 'wmfWBClientExcludeNS';

	function wmfWBClientExcludeNS() {
		global $wgWBClientSettings;

		$wgWBClientSettings['excludeNamespaces'] = array_merge(
			MWNamespace::getTalkNamespaces(),
			array( NS_USER, NS_MEDIAWIKI )
		);

		return true;
	};

	foreach( $wmgWikibaseClientSettings as $setting => $value ) {
		$wgWBClientSettings[$setting] = $value;
	}

	$wgWBClientSettings['allowDataTransclusion'] = true;
	$wgWBClientSettings['sharedCacheKeyPrefix'] = "$wmgWikibaseCachePrefix/WBL-$wmfExtendedVersionNumber";
}
