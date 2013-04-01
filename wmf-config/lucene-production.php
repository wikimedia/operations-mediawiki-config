<?php
# WARNING: This file is publically viewable on the web. Do not put private data here.

# This file hold the MediaWiki lucene configuration which is specific
# to the 'production' realm.
# It should be loaded AFTER lucene-common.php

# default host for mwsuggest backend
#$wgEnableLucenePrefixSearch = true;
$wgLucenePrefixHost = '10.2.1.15'; # pmtpa LVS search-prefix pool
$wgLucenePrefixHost = '10.2.2.15'; # eqiad LVS search-prefix pool

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
} elseif ( in_array( $wgDBname, array( 'commonswiki', 'wikidatawiki', 'metawiki', 'enwiktionary' ) ) ) {
	# Pool 5 LVS
	#$wgLuceneHost = '10.2.2.16'; # eqiad
	$wgLuceneHost = '10.2.1.16'; # pmtpa
} else {
	# Pool 4 LVS
	#$wgLuceneHost = '10.2.2.14'; # eqiad
	$wgLuceneHost = '10.2.1.14'; # pmtpa
}
