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

$wgCirrusSearchBackup['backups'] = array(
	'type' => 'swift',
	'swift_url' => 'http://deployment-saio:8080/auth/v1.0',
	'swift_container' => 'global-data-elastic-backups',
	'swift_username' => 'test:tester',
	'swift_password' => 'testing',
	'max_snapshot_bytes_per_sec' => '10mb',
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
