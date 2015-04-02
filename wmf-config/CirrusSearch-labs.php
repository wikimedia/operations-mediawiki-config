<?php
# WARNING: This file is publically viewable on the web. Do not put private data here.

# This file holds the MediaWiki CirrusSearch configuration which is specific
# to the 'labs' realm which in most of the cases means the beta cluster.
# It should be loaded AFTER CirrusSearch-common.php

$wgCirrusSearchServers = array(
	'deployment-elastic05',
	'deployment-elastic06',
	'deployment-elastic07',
	'deployment-elastic08',
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

$wgCirrusSearchAllFields = array( 'build' => true, 'use' => true );
$wgCirrusSearchWikimediaExtraPlugin = array(
	'regex' => array(
		'build',
		'use',
	),
);

# We don't have enough nodes to support these settings in beta so just turn
# them off.
$wgCirrusSearchMaxShardsPerNode = array();

# Use the safer query from the extra extension that is currently only deployed
# in beta.
$wgCirrusSearchWikimediaExtraPlugin[ 'safer' ] = array(
	'phrase' => array(
	)
);
