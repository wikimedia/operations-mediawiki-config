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

$wgLucenePrefixVips = array(
	'10.2.2.15', # eqiad LVS search-prefix pool
	'10.2.1.15', # pmtpa LVS search-prefix pool
);
$wgLucenePrefixHost = $wgLucenePrefixVips[abs( crc32( $wgDBname ) ) % count( $wgLucenePrefixVips )];

$wgLucenePort = 8123;
if ( in_array( $wgDBname, array( 'enwiki' ) ) ) {
	# Pool 1 LVS
	$wgLuceneHostVips = array(
		'10.2.1.11', # pmtpa
		'10.2.2.11', # eqiad
	);
} elseif ( in_array( $wgDBname, array( 'dewiki', 'frwiki', 'jawiki' ) ) ) {
	# Pool 2 LVS
	$wgLuceneHostVips = array(
		'10.2.1.12', # pmtpa
		'10.2.2.12', # eqiad
	);
} elseif ( in_array( $wgDBname, array( 'itwiki', 'ptwiki', 'plwiki', 'nlwiki', 'ruwiki', 'svwiki', 'zhwiki', 'eswiki'  ) ) ) {
	# Pool 3 LVS
	$wgLuceneHostVips = array(
		'10.2.1.13', # pmtpa
		'10.2.2.13', # eqiad
	);
} else {
	# Pool 4 LVS
	$wgLuceneHostVips = array(
		'10.2.1.14', # pmtpa
		'10.2.2.14', # eqiad
	);
}

$wgLuceneHost = $wgLuceneHostVips[abs( crc32( $wgDBname ) ) % count( $wgLuceneHostVips )];

