<?php
/**
 * This file is a copy of https://github.com/wikimedia/mediawiki-extensions-Wikidata/blob/master/Wikidata.php
 * This file has been created as part of https://phabricator.wikimedia.org/T176948 (Killing the Wikidata build)
 * Various things have been changed (with the originals left commented)
 */

//if ( !defined( 'MEDIAWIKI' ) ) {
//	die( 'Not an entry point.' );
//}
//// Jenkins stuff part1
//if ( isset( $wgWikimediaJenkinsCI ) && $wgWikimediaJenkinsCI == true ) {
//	$wmgUseWikibaseRepo = true;
//	$wmgUseWikibaseClient = true;
//	$wmgUseWikibaseQuality = true;
//	$wmgUseWikibaseMediaInfo = true;
//	$wmgUseArticlePlaceholder = true;
//}

// no magic, use wmf configs instead to control which entry points to load
$wgEnableWikibaseRepo = false;
$wgEnableWikibaseClient = false;

//$wgWikidataBaseDir = $IP;
$wgWikidataBaseDir = $IP . '/extensions/Wikidata';

//if ( file_exists(  __DIR__ . '/vendor/autoload.php' ) ) {
//	include_once __DIR__ . '/vendor/autoload.php';
//	$wgWikidataBaseDir = __DIR__;
//}
if ( file_exists(  $wgWikidataBaseDir . '/vendor/autoload.php' ) ) {
	include_once $wgWikidataBaseDir . '/vendor/autoload.php';
}

if ( !empty( $wmgUseWikibaseRepo ) ) {
	include_once "$wgWikidataBaseDir/extensions/Wikibase/repo/Wikibase.php";
	include_once "$wgWikidataBaseDir/extensions/Wikidata.org/WikidataOrg.php";
	include_once "$wgWikidataBaseDir/extensions/PropertySuggester/PropertySuggester.php";
	if ( !empty( $wmgUseWikibaseQuality ) ) {
		include_once "$wgWikidataBaseDir/extensions/Quality/WikibaseQuality.php";
		include_once "$wgWikidataBaseDir/extensions/Constraints/WikibaseQualityConstraints.php";
	}
//	// @note wikibase/media-info is removed from composer.json for
//	// deployment builds, during the 'branch' grunt command. (pending security review)
//	// jenkins builds for deployment branches have this set to true, but then we remove
//	// the code, so need to check the extension is there even on jenkins.
//	if (
//		!empty( $wmgUseWikibaseMediaInfo )
//		&& is_readable( "$wgWikidataBaseDir/extensions/MediaInfo/extension.json" )
//	) {
//		wfLoadExtension( 'WikibaseMediaInfo', "$wgWikidataBaseDir/extensions/MediaInfo/extension.json" );
//	}
}
if ( !empty( $wmgUseWikibaseClient ) ) {
	include_once "$wgWikidataBaseDir/extensions/Wikibase/client/WikibaseClient.php";
	wfLoadExtension( 'WikimediaBadges', "$wgWikidataBaseDir/extensions/WikimediaBadges/extension.json" );
	if ( !empty( $wmgUseArticlePlaceholder ) ) {
		wfLoadExtension( 'ArticlePlaceholder', "$wgWikidataBaseDir/extensions/ArticlePlaceholder/extension.json" );
	}
}
//if ( file_exists(  __DIR__ . '/vendor/autoload.php' ) ) {
if ( file_exists(  $wgWikidataBaseDir . '/vendor/autoload.php' ) ) {
	// @fixme generating these settings with composer doesn't work with
	// the composer-merge-plugin. We would need to fix that and probably
	// handle this somewhat differently.  For now, this way at least
	// works with the current Wikidata build process, with composer run
	// here and the results committed to gerrit.
	//
	// see T95663 for details on migrating and supporting install with
	// composer-merge-plugin
//	if ( !empty( $wmgUseWikibaseRepo ) ) {
//		include_once __DIR__ . '/WikibaseRepo.settings.php';
//	}
//	if ( !empty( $wmgUseWikibaseClient ) ) {
//		include_once __DIR__ . '/WikibaseClient.settings.php';
//	}
	if ( !empty( $wmgUseWikibaseRepo ) ) {
		$wgWBRepoSettings["sharedCacheKeyPrefix"] = "wikibase_shared/_mediawiki_extension_wikidata";
	}
	if ( !empty( $wmgUseWikibaseClient ) ) {
		$wgWBClientSettings["sharedCacheKeyPrefix"] = "wikibase_shared/_mediawiki_extension_wikidata";
	}
}
//$wgHooks['UnitTestsList'][] = '\Wikidata\WikidataHooks::onUnitTestsList';
require_once "$wgWikidataBaseDir/Wikidata.credits.php";
//// Jenkins stuff part2
//if ( isset( $wgWikimediaJenkinsCI ) && $wgWikimediaJenkinsCI == true ) {
//	//Jenkins always loads both so no need to check if they are loaded before getting settings
//	require_once __DIR__ . '/extensions/Wikibase/repo/ExampleSettings.php';
//	require_once __DIR__ . '/extensions/Wikibase/client/ExampleSettings.php';
//}
