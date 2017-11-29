<?php
/**
 * This file is a copy of https://github.com/wikimedia/mediawiki-extensions-Wikidata/blob/master/Wikidata.php
 * This file has been created as part of https://phabricator.wikimedia.org/T176948 (Killing the Wikidata build)
 * Various things have been changed (with the originals left commented)
 */

$wgWikidataBuildBaseDir = $IP . '/extensions/Wikidata';

if ( $wmgUseWikidataBuild ) {
	if ( file_exists( $wgWikidataBuildBaseDir . '/vendor/autoload.php' ) ) {
		include_once $wgWikidataBuildBaseDir . '/vendor/autoload.php';
	}

	if ( !empty( $wmgUseWikibaseRepo ) ) {
		include_once "$wgWikidataBuildBaseDir/extensions/Wikibase/repo/Wikibase.php";
		include_once "$wgWikidataBuildBaseDir/extensions/Wikidata.org/WikidataOrg.php";
		include_once "$wgWikidataBuildBaseDir/extensions/PropertySuggester/PropertySuggester.php";
		include_once "$wgWikidataBuildBaseDir/extensions/Quality/WikibaseQuality.php";
		include_once "$wgWikidataBuildBaseDir/extensions/Constraints/WikibaseQualityConstraints.php";
	}

	if ( !empty( $wmgUseWikibaseClient ) ) {
		include_once "$wgWikidataBuildBaseDir/extensions/Wikibase/client/WikibaseClient.php";
		wfLoadExtension( 'WikimediaBadges', "$wgWikidataBuildBaseDir/extensions/WikimediaBadges/extension.json" );
		if ( !empty( $wmgUseArticlePlaceholder ) ) {
			wfLoadExtension( 'ArticlePlaceholder', "$wgWikidataBuildBaseDir/extensions/ArticlePlaceholder/extension.json" );
		}
	}

	// This should be kept until the Wikidata build extension is actually turned off
	// so that we can keep track of the version deployed on Special:Version
	require_once "$wgWikidataBuildBaseDir/Wikidata.credits.php";

} else {

	if ( !empty( $wmgUseWikibaseRepo ) ) {
		include_once "$IP/extensions/Wikibase/repo/Wikibase.php";
		include_once "$IP/extensions/Wikidata.org/WikidataOrg.php";
		include_once "$IP/extensions/PropertySuggester/PropertySuggester.php";
		include_once "$IP/extensions/Quality/WikibaseQuality.php";
		include_once "$IP/extensions/Constraints/WikibaseQualityConstraints.php";
	}

	if ( !empty( $wmgUseWikibaseClient ) ) {
		include_once "$IP/extensions/Wikibase/client/WikibaseClient.php";
		wfLoadExtension( 'WikimediaBadges' );
		if ( !empty( $wmgUseArticlePlaceholder ) ) {
			wfLoadExtension( 'ArticlePlaceholder' );
		}
	}

}
