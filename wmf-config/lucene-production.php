<?php
# WARNING: This file is publically viewable on the web. Do not put private data here.

# This file hold the MediaWiki lucene configuration which is specific
# to the 'production' realm.
# It should be loaded AFTER lucene-common.php

# default host for mwsuggest backend
$wgEnableLucenePrefixSearch = true;
$wgLucenePrefixHost = '10.2.2.15'; # eqiad LVS search-prefix pool

$wgLucenePort = 8123;
if ( in_array( $wgDBname, array( 'enwiki' ) ) ) {
	# Big RAM pool 1, via LVS
	$wgLuceneHost = '10.2.2.11'; # eqiad
} elseif ( in_array( $wgDBname, array( 'nlwiki', 'ruwiki'  ) ) ) {
	# Pool 3 LVS
	$wgLuceneHost = '10.2.2.13'; # eqiad
}
