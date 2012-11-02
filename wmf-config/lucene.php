<?php
# See: http://wikitech.leuksman.com/view/LuceneSearch
# Contact Brion for advanced support.
# WARNING: This file is publically viewable on the web. Do not put private data here.

# $wgDisableInternalSearch = true;
$wgDisableTextSearch = false;

// Allow nagios configuration queries without requiring MediaWiki environment
if ( defined( 'MEDIAWIKI' ) ) {
	// Test out migrating back to internal search
	// with backend plugin only
	# $wgDisableInternalSearch = false;
	$wgSearchType = 'LuceneSearch';
	require_once( $IP . '/extensions/MWSearch/MWSearch.php' );
}

$wgLuceneCacheExpiry = 12 * 3600; // 12 hours
$wgLuceneSearchVersion = 2.1;
$wgLuceneSearchTimeout = 10;

# default host for mwsuggest backend
$wgEnableLucenePrefixSearch = true;
$wgLucenePrefixHost = '10.2.2.15'; # LVS search-prefix pool

$wgLucenePort = 8123;
if ( in_array( $wgDBname, array( 'enwiki' ) ) ) {
	# Big RAM pool 1, via LVS
	$wgLuceneHost = '10.2.2.11';
	$wgLucenePrefixHost = '10.2.2.15'; #kept in case we need to flip back to pmtpa
	// $wmgUseTitleKey = false; // Breaks go matching: https://bugzilla.wikimedia.org/show_bug.cgi?id=19882
} elseif ( in_array( $wgDBname, array( 'dewiki', 'frwiki', 'jawiki' ) ) ) {
	# Big RAM pool 2, via LVS
	$wgLuceneHost = '10.2.2.12';
} elseif ( in_array( $wgDBname, array( 'itwiki', 'ptwiki', 'plwiki', 'nlwiki', 'ruwiki', 'svwiki', 'zhwiki', 'eswiki'  ) ) ) {
	# Pool 3 LVS
	$wgLuceneHost = '10.2.2.13';
} else {
	# Pool 4 LVS
	$wgLuceneHost = '10.2.2.14';
}

$wgLuceneCSSPath = '/w/extensions/LuceneSearch/lucenesearch.css';
# $wgLuceneUseSearchJS = false;
$wgLuceneDisableSuggestions = true;
$wgLuceneDisableTitleMatches = true;
$wgLuceneSearchExactCase = !$wgCapitalLinks;

# Server updater

// off due to intermittent breakage which hangs saves -- brion 2005-09-14
if ( getenv( 'MWSEARCH' ) ) {
	if ( defined( 'MEDIAWIKI' ) ) {
		require_once( $IP . '/extensions/MWSearch/MWSearchUpdateHook.php' );
	}
	$mwSearchUpdateHost = '10.0.0.16'; // maurus
}

