<?php
# This file is used by commandLine.inc and CommonSettings.php to initialise $wgConf
# WARNING: This file is publically viewable on the web. Do not put private data here.

$wgConf = new SiteConfiguration;

# Read wiki lists

$wgConf->suffixes = array(
	// 'wikipedia',
	'wiki',
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

$wgConf->wikis = array_map( 'trim', file( getRealmSpecificFilename( "$wmfCommonDir/all.dblist" ) ) );

$wgConf->fullLoadCallback = 'wmfLoadInitialiseSettings';

$wgLocalDatabases =& $wgConf->getLocalDatabases();
