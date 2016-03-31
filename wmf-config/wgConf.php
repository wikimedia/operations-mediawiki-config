<?php
# This file is used by commandLine.inc and CommonSettings.php to initialise $wgConf
# WARNING: This file is publically viewable on the web. Do not put private data here.

$wgConf = new SiteConfiguration;

# Read wiki lists

$wgConf->suffixes = array(
	// 'wikipedia',
	'wikipedia' => 'wiki',
	'wiktionary',
	'wikiquote',
	'wikibooks',
	'wikiquote',
	'wikinews',
	'wikisource',
	'wikiversity',
	'wikimedia',
	'wikivoyage',
);

$dbList = $wmfRealm === 'labs' ? 'all-labs' : 'all';
$wgConf->wikis = MWWikiversions::readDbListFile( $dbList );
if ( $wgDBname === 'labswiki' ) {
	$wgConf->wikis = [ 'labswiki' ];
} else if ( $wgDBname === 'labtestwiki' ) {
	$wgConf->wikis = [ 'labtestwiki' ];
} else {
	$key = array_search( 'labswiki', $wgConf->wikis );
	if ( $key !== false ) {
		unset( $wgConf->wikis[$key] );
	}
	$key = array_search( 'labtestwiki', $wgConf->wikis );
	if ( $key !== false ) {
		unset( $wgConf->wikis[$key] );
	}
	$wgConf->wikis = array_values( $wgConf->wikis );
}

$wgConf->fullLoadCallback = 'wmfLoadInitialiseSettings';

$wgLocalDatabases =& $wgConf->getLocalDatabases();
