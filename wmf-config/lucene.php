<?php
# See: http://wikitech.leuksman.com/view/LuceneSearch
# Contact Brion for advanced support.
# WARNING: This file is publically viewable on the web. Do not put private data here.

$wgDisableTextSearch = false;

// Allow nagios configuration queries without requiring MediaWiki environment
if ( defined( 'MEDIAWIKI' ) ) {
	$wgSearchType = 'LuceneSearch';
	require( $IP . '/extensions/MWSearch/MWSearch.php' );
}

$wgLuceneCacheExpiry = 12 * 3600; // 12 hours
$wgLuceneSearchVersion = 2.1;
$wgLuceneSearchTimeout = 10;

# default host for mwsuggest backend
$wgEnableLucenePrefixSearch = true;
$wgLucenePrefixHost = '10.2.1.15'; # LVS search-prefix pool

$wgLucenePort = 8123;
if ( in_array( $wgDBname, array( 'enwiki' ) ) ) {
	# Big RAM pool 1, via LVS
	#$wgLuceneHost = '10.2.2.11'; # eqiad
	$wgLuceneHost = '10.2.1.11'; # pmtpa
} elseif ( in_array( $wgDBname, array( 'dewiki', 'frwiki', 'jawiki' ) ) ) {
	# Big RAM pool 2, via LVS
	#$wgLuceneHost = '10.2.2.12'; # eqiad
	$wgLuceneHost = '10.2.1.12'; # pmtpa
} elseif ( in_array( $wgDBname, array( 'itwiki', 'ptwiki', 'plwiki', 'nlwiki', 'ruwiki', 'svwiki', 'zhwiki', 'eswiki'  ) ) ) {
	# Pool 3 LVS
	#$wgLuceneHost = '10.2.2.13'; # eqiad
	$wgLuceneHost = '10.2.1.13'; # pmtpa
} else {
	# Pool 4 LVS
	#$wgLuceneHost = '10.2.2.14'; # eqiad
	$wgLuceneHost = '10.2.1.14'; # pmtpa
}
