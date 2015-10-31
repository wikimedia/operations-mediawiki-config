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

if ( $wmfRealm === 'labs' ) {
	$wgConf->localVHosts = require( "{$wmfConfigDir}/wgConfVHosts-labs.php" );
	$dbList = 'all-labs';
} else {
	$wgConf->localVHosts = require( "{$wmfConfigDir}/wgConfVHosts.php" );
	$dbList = 'all';
}

$wgConf->wikis = MWWikiversions::readDbListFile( $dbList );

$wgConf->fullLoadCallback = 'wmfLoadInitialiseSettings';

$wgLocalDatabases =& $wgConf->getLocalDatabases();
