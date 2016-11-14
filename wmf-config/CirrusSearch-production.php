<?php
# WARNING: This file is publically viewable on the web. Do not put private data here.

# This file holds the MediaWiki CirrusSearch configuration which is specific
# to the 'production' realm.
# It should be loaded AFTER CirrusSearch-common.php

// Our cluster often has issues completing master actions
// within the default 30s timeout. Upgrade that to 2m to
// help things along.
$wgCirrusSearchMasterTimeout = '2m';

$wgCirrusSearchClusters = [
	'eqiad' => $wmfAllServices['eqiad']['search'],
	'codfw' => $wmfAllServices['codfw']['search'],
];
if ( defined( 'HHVM_VERSION' ) ) {
	$wgCirrusSearchClusters['eqiad'] = array_map( function ( $host ) {
		return [
			'transport' => 'CirrusSearch\\Elastica\\PooledHttps',
			'port' => '9243',
			'host' => $host,
			'config' => [
				'pool' => 'cirrus-eqiad',
			],
		];
	}, $wgCirrusSearchClusters['eqiad'] );
	$wgCirrusSearchClusters['codfw'] = array_map( function ( $host ) {
		return [
			'transport' => 'CirrusSearch\\Elastica\\PooledHttps',
			'port' => '9243',
			'host' => $host,
			'config' => [
				'pool' => 'cirrus-codfw',
			],
		];
	}, $wgCirrusSearchClusters['codfw'] );
}

if ( $wgDBname === 'labswiki' || $wgDBname === 'labtestwiki' ) {
	$wgCirrusSearchClusters = [
		'eqiad' => $wmfAllServices['eqiad']['search'],
		'codfw' => $wmfAllServices['codfw']['search'],
	];
}

$wgCirrusSearchConnectionAttempts = 3;

$wgCirrusSearchBackup['backups'] = [
	'type' => 'swift',
	'swift_url' => $wmfSwiftEqiadConfig['cirrusAuthUrl'],
	'swift_container' => 'global-data-elastic-backups',
	'swift_username' => $wmfSwiftEqiadConfig['cirrusUser'],
	'swift_password' => $wmfSwiftEqiadConfig['cirrusKey'],
	'max_snapshot_bytes_per_sec' => '10mb',
	'compress' => false,
	'chunk_size' => '1g',
];

$projectsOkForInterwiki = [
	'itwiki' => 'w',
	'itwiktionary' => 'wikt',
	'itwikibooks' => 'b',
	'itwikinews' => 'n',
	'itwikiquote' => 'q',
	'itwikisource' => 's',
	'itwikivoyage' => 'voy',
	'itwikiversity' => 'v',
];

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

// The default configuration is a single-cluster configuration, expand
// that here into the necessary multi-cluster config
$wgCirrusSearchShardCount = [
	'eqiad' => $wmgCirrusSearchShardCount,
	'codfw' => $wmgCirrusSearchShardCount,
];

if ( ! isset( $wmgCirrusSearchReplicas['eqiad'] ) ) {
	$wgCirrusSearchReplicas = [
		'eqiad' => $wmgCirrusSearchReplicas,
		'codfw' => $wmgCirrusSearchReplicas,
	];
}

// 5 second timeout for local cluster, 10 seconds for remote.
$wgCirrusSearchClientSideConnectTimeout = [
	'eqiad' => $wmfDatacenter === 'eqiad' ? 5 : 10,
	'codfw' => $wmfDatacenter === 'codfw' ? 5 : 10,
];

$wgCirrusSearchDropDelayedJobsAfter = [
	'eqiad' => $wgCirrusSearchDropDelayedJobsAfter,
	'codfw' => $wgCirrusSearchDropDelayedJobsAfter,
];

$wgCirrusSearchRecycleCompletionSuggesterIndex = $wmgCirrusSearchRecycleCompletionSuggesterIndex;

// cache morelike queries to ObjectCache for 24 hours
$wgCirrusSearchMoreLikeThisTTL = 86400;

// This was causing race conditions and is a temporary fix. A better fix is coming soon (T133793)
$wgCirrusSearchCreateFrozenIndex = false;
