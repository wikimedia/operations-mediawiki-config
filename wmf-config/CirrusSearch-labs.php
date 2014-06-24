<?php
# WARNING: This file is publically viewable on the web. Do not put private data here.

# This file holds the MediaWiki CirrusSearch configuration which is specific
# to the 'labs' realm which in most of the cases means the beta cluster.
# It should be loaded AFTER CirrusSearch-common.php

$wgCirrusSearchServers = array(
	'deployment-elastic01',
	'deployment-elastic02',
	'deployment-elastic03',
	'deployment-elastic04',
);

if ( $wgDBname == 'enwiki' ) {
	$wgCirrusSearchInterwikiSources = array(
		'wiktionary' => 'enwiktionary',
		'wikibooks' => 'enwikibooks',
		'wikinews' => 'enwikinews',
		'wikiquote' => 'enwikiquote',
		'wikisource' => 'enwikisource',
		'wikiversity' => 'enwikiversity',
	);
}

$wgSearchTypeAlternatives = array();
