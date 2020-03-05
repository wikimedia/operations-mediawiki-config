<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

# This for PRODUCTION.
#
# Effective load order:
# - multiversion
# - mediawiki/DefaultSettings.php
# - wmf-config/CommonSettings.php
# - wmf-config/wgConf.php [THIS FILE]
#
# Included from: wmf-config/CommonSettings.php.

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
