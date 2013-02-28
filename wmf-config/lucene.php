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

$wmfLucenePrefixVips = array(
	'10.2.2.15', # eqiad LVS search-prefix pool
	'10.2.1.15', # pmtpa LVS search-prefix pool
);

$wgLucenePrefixHost = $wmfLucenePrefixVips[ wfPickRandom( $wmfLucenePrefixVips ) ];

$wgLucenePort = 8123;
if ( in_array( $wgDBname, array( 'enwiki' ) ) ) {
	# Pool 1 LVS
	$wmfLuceneHostVips = array(
		'10.2.2.11', # eqiad
		'10.2.1.11', # pmtpa
	);
} elseif ( in_array( $wgDBname, array( 'dewiki', 'frwiki', 'jawiki' ) ) ) {
	# Pool 2 LVS
	$wmfLuceneHostVips = array(
		'10.2.2.12', # eqiad
		'10.2.1.12', # pmtpa
	);
} elseif ( in_array( $wgDBname, array( 'itwiki', 'ptwiki', 'plwiki', 'nlwiki', 'ruwiki', 'svwiki', 'zhwiki', 'eswiki'  ) ) ) {
	# Pool 3 LVS
	$wmfLuceneHostVips = array(
		'10.2.2.13', # eqiad
		'10.2.1.13', # pmtpa
	);
} else {
	# Pool 4 LVS
	$wmfLuceneHostVips = array(
		'10.2.2.14', # eqiad
		'10.2.1.14', # pmtpa
	);
}

$wgLuceneHost = $wmfLuceneHostVips[ wfPickRandom( $wmfLuceneHostVips ) ];
