<?php
# This file is used by commandLine.inc and CommonSettings.php to initialise $wgConf
# WARNING: This file is publically viewable on the web. Do not put private data here.

$wgConf = new SiteConfiguration;

# Read wiki lists

$wgConf->wikis = array_map( 'trim', file( "$IP/../all.dblist" ) );
$wgConf->suffixes = array(
	'wikipedia',
	'wiki',
	'wiktionary',
	'wikiquote',
	'wikibooks',
	'wikiquote',
	'wikinews',
	'wikisource',
	'wikiversity',
	'wikimedia'
);

if ( $cluster == 'pmtpa' ) {
    $wgConf->localVHosts = array(
            'wikipedia.org',
            'wiktionary.org',
            'wikiquote.org',
            'wikibooks.org',
            'wikiquote.org',
            'wikinews.org',
            'wikisource.org',
            'wikiversity.org',
            // 'wikimedia.org' // Removed 2008-09-30 by brion -- breaks codereview-proxy.wikimedia.org
            'meta.wikimedia.org', // Presumably needed to load meta spam list. Any others?
            'commons.wikimedia.org',
    );
}

$wgConf->fullLoadCallback = 'wmfLoadInitialiseSettings';

$wgLocalDatabases =& $wgConf->getLocalDatabases();

