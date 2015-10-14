<?php
# WARNING: This file is publically viewable on the web. Do not put private data here.

# This file holds the MediaWiki CirrusSearch configuration which is specific
# to the 'production' realm.
# It should be loaded AFTER CirrusSearch-common.php


$wgCirrusSearchClusters = array(
	'eqiad' => array( '10.2.2.30' ), // search.svc.eqiad.wmnet
	'codfw' => array( '10.2.1.30' ), // search.svc.codfw.wmnet
	'labsearch' => array( '10.64.37.14' ), // nobelium.eqiad.wmnet
);

$wgCirrusSearchConnectionAttempts = 3;

$wgCirrusSearchBackup['backups'] = array(
	'type' => 'swift',
	'swift_url' => $wmfSwiftEqiadConfig['cirrusAuthUrl'],
	'swift_container' => 'global-data-elastic-backups',
	'swift_username' => $wmfSwiftEqiadConfig['cirrusUser'],
	'swift_password' => $wmfSwiftEqiadConfig['cirrusKey'],
	'max_snapshot_bytes_per_sec' => '10mb',
	'compress' => false,
	'chunk_size' => '1g',
);

$projectsOkForInterwiki = array(
	'itwiki' => 'w',
	'itwiktionary' => 'wikt',
	'itwikibooks' => 'b',
	'itwikinews' => 'n',
	'itwikiquote' => 'q',
	'itwikisource' => 's',
	'itwikivoyage' => 'voy',
	'itwikiversity' => 'v',
);

if ( isset( $projectsOkForInterwiki[ $wgDBname ] ) ) {
	unset( $projectsOkForInterwiki[$wgDBname] );
	$interwikiSearchConf = array_flip( $projectsOkForInterwiki );
	$wgCirrusSearchInterwikiSources = $interwikiSearchConf;
	$wgCirrusSearchInterwikiCacheTime = 60;
}

if ( $wgDBname == 'enwiki' ) {
	$wgCirrusSearchPoolCounterKey .= '_enwiki';
}

$wgCirrusSearchEnableSearchLogging = true;

// If there is a local search cluster use it, otherwise default to eqiad
$wgCirrusSearchDefaultCluster = isset( $wgCirrusSearchClusters[$wmfDatacenter] ) ? $wmfDatacenter : 'eqiad';

// The default configuration is a single-cluster configuration, expand
// that here into the necessary multi-cluster config
$wgCirrusSearchShardCount = array(
	'eqiad' => $wgCirrusSearchShardCount,
	'codfw' => array_map( function($x) { return min( 7, $x ); }, $wgCirrusSearchShardCount ),
	'labsearch' => array_map( function() { return 1; }, $wgCirrusSearchShardCount ),
);

$wgCirrusSearchReplicas = array(
	'eqiad' => $wgCirrusSearchReplicas,
	'codfw' => $wgCirrusSearchReplicas,
	'labsearch' => array_map( function() { return 'false'; }, $wgCirrusSearchReplicas ),
);

$wgCirrusSearchClientSideConnectTimeout = array(
	'eqiad' => $wmfDatacenter === 'eqiad' ? 5 : 10,
	'codfw' => $wmfDatacenter === 'codfw' ? 5 : 10,
	'labsearch' => 2,
);
