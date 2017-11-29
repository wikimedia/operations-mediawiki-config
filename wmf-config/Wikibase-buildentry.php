<?php
/**
 * This file is a copy of https://github.com/wikimedia/mediawiki-extensions-Wikidata/blob/master/Wikidata.php
 * This file has been created as part of https://phabricator.wikimedia.org/T176948 (Killing the Wikidata build)
 * Various things have been changed (with the originals left commented)
 */

$wgWikidataBuildBaseDir = $IP . '/extensions/Wikidata';

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
