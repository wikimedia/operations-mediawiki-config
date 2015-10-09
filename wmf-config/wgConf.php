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

$wgConf->localVHosts = require( getRealmSpecificFilename( "$wmfConfigDir/wgConfVHosts.php" ) );

$wgConf->wikis = MWWikiversions::readDbListFile( getRealmSpecificFilename( MEDIAWIKI_DBLIST_DIR . '/all.dblist' ) );

$wgConf->fullLoadCallback = 'wmfLoadInitialiseSettings';

$wgLocalDatabases =& $wgConf->getLocalDatabases();
