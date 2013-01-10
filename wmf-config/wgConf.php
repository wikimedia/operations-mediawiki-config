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

switch( $wmfRealm ) {
	case 'production':
		$wgConf->localVHosts = array(
			'wikipedia.org',
			'wiktionary.org',
			'wikiquote.org',
			'wikibooks.org',
			'wikiquote.org',
			'wikinews.org',
			'wikisource.org',
			'wikiversity.org',
			'wikivoyage.org',
			// 'wikimedia.org' // Removed 2008-09-30 by brion -- breaks codereview-proxy.wikimedia.org
			'meta.wikimedia.org', // Presumably needed to load meta spam list. Any others?
			'commons.wikimedia.org',
		);
		break;

	case 'labs':
		$wgConf->localVHosts = array(
			'wikipedia.beta.wmflabs.org',
			'wiktionary.beta.wmflabs.org',
			'wikibooks.beta.wmflabs.org',
			'wikiquote.beta.wmflabs.org',
			'wikinews.beta.wmflabs.org',
			'wikisource.beta.wmflabs.org',
			'wikiversity.beta.wmflabs.org',
			'wikivoyage.beta.wmflabs.org',
			// 'wikimedia.beta.wmflabs.org' // Removed 2008-09-30 by brion -- breaks codereview-proxy.wikimedia.org
			'meta.wikimedia.beta.wmflabs.org', // Presumably needed to load meta spam list. Any others?
			'incubator.wikimedia.beta.wmflabs.org',
			'commons.wikimedia.beta.wmflabs.org',
		);
		break;

	default:
}

$wgConf->wikis = array_map( 'trim', file( getRealmSpecificFilename( MULTIVER_COMMON . '/dblists/all.dblist' ) ) );

$wgConf->fullLoadCallback = 'wmfLoadInitialiseSettings';

$wgLocalDatabases =& $wgConf->getLocalDatabases();
