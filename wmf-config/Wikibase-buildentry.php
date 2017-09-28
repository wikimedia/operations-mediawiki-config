<?php
/**
 * This file is a copy of https://github.com/wikimedia/mediawiki-extensions-Wikidata/blob/master/Wikidata.php
 * This file has been created as part of https://phabricator.wikimedia.org/T176948 (Killing the Wikidata build)
 * Various things have been changed (with the originals left commented)
 */

// no magic, use wmf configs instead to control which entry points to load
$wgEnableWikibaseRepo = false;
$wgEnableWikibaseClient = false;

$wgWikidataBuildBaseDir = $IP . '/extensions/Wikidata';

if ( file_exists( $wgWikidataBuildBaseDir . '/vendor/autoload.php' ) ) {
	include_once $wgWikidataBuildBaseDir . '/vendor/autoload.php';
}

if ( !empty( $wmgUseWikibaseRepo ) ) {
	include_once "$wgWikidataBuildBaseDir/extensions/Wikibase/repo/Wikibase.php";
	include_once "$wgWikidataBuildBaseDir/extensions/Wikidata.org/WikidataOrg.php";
	include_once "$wgWikidataBuildBaseDir/extensions/PropertySuggester/PropertySuggester.php";
	if ( !empty( $wmgUseWikibaseQuality ) ) {
		include_once "$wgWikidataBuildBaseDir/extensions/Quality/WikibaseQuality.php";
		include_once "$wgWikidataBuildBaseDir/extensions/Constraints/WikibaseQualityConstraints.php";
	}
}

if ( !empty( $wmgUseWikibaseClient ) ) {
	include_once "$wgWikidataBuildBaseDir/extensions/Wikibase/client/WikibaseClient.php";
	wfLoadExtension( 'WikimediaBadges', "$wgWikidataBuildBaseDir/extensions/WikimediaBadges/extension.json" );
	if ( !empty( $wmgUseArticlePlaceholder ) ) {
		wfLoadExtension( 'ArticlePlaceholder', "$wgWikidataBuildBaseDir/extensions/ArticlePlaceholder/extension.json" );
	}
}

if ( file_exists( $wgWikidataBuildBaseDir . '/vendor/autoload.php' ) ) {
	/**
	 * Create a cache key per version based on wgVersion.
	 * wgVersion returns strings such as '1.31.0-wmf.1'
	 * We end up with a string like 'wikibase_shared/wikidata_1_31_0_wmf_1'
	 */
	$wgWikibaseSharedCacheKeyprefix = 'wikibase_shared/wikidata_' .
		str_replace( '.', '_', str_replace( '-', '_', $wgVersion ) );
	if ( !empty( $wmgUseWikibaseRepo ) ) {
		// wikibase_shared/wikidata_1_31_0_wmf_1
		$wgWBRepoSettings["sharedCacheKeyPrefix"] = $wgWikibaseSharedCacheKeyprefix;
	}
	if ( !empty( $wmgUseWikibaseClient ) ) {
		// wikibase_shared/wikidata_1_31_0_wmf_1
		$wgWBClientSettings["sharedCacheKeyPrefix"] = $wgWikibaseSharedCacheKeyprefix;
	}
}

// This should be kept until the Wikidata build extension is actually turned off
// so that we can keep track of the version deployed on Special:Version
require_once "$wgWikidataBuildBaseDir/Wikidata.credits.php";
