<?php
# WARNING: This file is publically viewable on the web. Do not put private data here.

# This file holds the MediaWiki CirrusSearch configuration which is specific
# to the 'production' realm.
# It should be loaded AFTER CirrusSearch-common.php

// Our cluster often has issues completing master actions
// within the default 30s timeout. Upgrade that to 2m to
// help things along.
$wgCirrusSearchMasterTimeout = '2m';

$wgCirrusSearchClusters = array(
	'eqiad' => array_map( function ( $host ) {
		return array(
			'transport' => 'CirrusSearch\\Elastica\\PooledHttps',
			'port' => '9243',
			'host' => $host,
			'config' => array(
				'pool' => 'cirrus-eqiad',
			),
		);
	}, $wmfAllServices['eqiad']['search'] ),
	'codfw' => array_map( function ( $host ) {
		return array(
			'transport' => 'CirrusSearch\\Elastica\\PooledHttps',
			'port' => '9243',
			'host' => $host,
			'config' => array(
				'pool' => 'cirrus-codfw',
			),
		);
	}, $wmfAllServices['codfw']['search'] ),
	'labsearch' => array( '10.64.37.14' ), // nobelium.eqiad.wmnet
);

if ( $wgDBname === 'labswiki' || $wgDBname === 'labtestwiki' ) {
	$wgCirrusSearchClusters = array(
		'eqiad' => $wmfAllServices['eqiad']['search'],
		'codfw' => $wmfAllServices['codfw']['search'],
		'labsearch' => array( '10.64.37.14' ), // nobelium.eqiad.wmnet
	);
}

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

// The default configuration is a single-cluster configuration, expand
// that here into the necessary multi-cluster config
$wgCirrusSearchShardCount = array(
	'eqiad' => $wmgCirrusSearchShardCount,
	'codfw' => $wmgCirrusSearchShardCount,
	'labsearch' => array_map( function() { return 1; }, $wmgCirrusSearchShardCount ),
);

// Disable replicas for the labsearch cluster, it's only a single machine
if ( isset( $wmgCirrusSearchReplicas['eqiad'] ) ) {
	$wgCirrusSearchReplicas = $wmgCirrusSearchReplicas + array(
		'labsearch' => array_map( function() { return 'false'; }, $wmgCirrusSearchReplicas['eqiad'] ),
	);
} else {
	$wgCirrusSearchReplicas = array(
		'eqiad' => $wmgCirrusSearchReplicas,
		'codfw' => $wmgCirrusSearchReplicas,
		'labsearch' => array_map( function() { return 'false'; }, $wmgCirrusSearchReplicas ),
	);
}

// 5 second timeout for local cluster, 10 seconds for remote. 2 second timeout
// for the labsearch cluster.
$wgCirrusSearchClientSideConnectTimeout = array(
	'eqiad' => $wmfDatacenter === 'eqiad' ? 5 : 10,
	'codfw' => $wmfDatacenter === 'codfw' ? 5 : 10,
	'labsearch' => 2,
);

// Drop delayed jobs for the labsearch cluster after only 10 minutes to keep them
// from filling up the job queue.
$wgCirrusSearchDropDelayedJobsAfter = array(
	'eqiad' => $wgCirrusSearchDropDelayedJobsAfter,
	'codfw' => 0, // Temporary hack to drop failing codfw jobs
	'labsearch' => 10 * 60, // ten minutes
);

$wgCirrusSearchRecycleCompletionSuggesterIndex = $wmgCirrusSearchRecycleCompletionSuggesterIndex;

// repoint morelike queries to codfw
$wgCirrusSearchMoreLikeThisCluster = 'eqiad';

// cache morelike queries to ObjectCache for 24 hours
$wgCirrusSearchMoreLikeThisTTL = 86400;
