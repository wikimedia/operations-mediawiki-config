<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

# This file is used by commandLine.inc and CommonSettings.php to initialise $wgConf.

$wgConf = new SiteConfiguration;

# Read wiki lists

$wgConf->suffixes = [
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
];

$dbList = $wmfRealm === 'labs' ? 'all-labs' : 'all';
$wgConf->wikis = MWWikiversions::readDbListFile( $dbList );

$wgConf->fullLoadCallback = 'wmfLoadInitialiseSettings';

$wgLocalDatabases =& $wgConf->getLocalDatabases();
