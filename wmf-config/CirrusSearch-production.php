<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

# This file holds the CirrusSearch configuration which is specific
# to the 'production' realm.
#
# NOTE: Included for all wikis.
#
# Load tree:
#  |-- wmf-config/CommonSettings.php
#      |
#      `-- wmf-config/CirrusSearch-common.php
#          |
#          `-- wmf-config/CirrusSearch-production.php
#

// Our cluster often has issues completing master actions
// within the default 30s timeout. Upgrade that to 2m to
// help things along.
$wgCirrusSearchMasterTimeout = '2m';

$cirrusConfigUseHhvmPool = function ( $hostConfig, $pool ) {
	if ( !defined( 'HHVM_VERSION' ) ) {
		return $hostConfig;
	}
	if ( is_array( $hostConfig ) ) {
		if ( isset( $hostConfig['transport'] ) && $hostConfig['transport'] === 'Https' ) {
			$hostConfig['transport'] = 'CirrusSearch\\Elastica\\PooledHttps';
			$hostConfig['config'] = [
				'pool' => $pool
			];
		}
		return $hostConfig;
	}
	return [
		'transport' => 'CirrusSearch\\Elastica\\PooledHttps',
		'port' => '9243',
		'host' => $hostConfig,
		'config' => [
			'pool' => $pool,
		],
	];
};

$wgCirrusSearchClusters = [
	'eqiad' => $cirrusConfigUseHhvmPool( $wmfAllServices['eqiad']['search'], 'cirrus-eqiad' ),
	'codfw' => $cirrusConfigUseHhvmPool( $wmfAllServices['codfw']['search'], 'cirrus-codfw' ),
];

unset( $cirrusConfigUseHhvmPool );

$wgCirrusSearchConnectionAttempts = 3;

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

// Index deletes into archive
$wgCirrusSearchIndexDeletes = $wmgCirrusSearchIndexDeletes;
// Enable searching archive
$wgCirrusSearchEnableArchive = $wmgCirrusSearchEnableArchive;
// Internal WDQS endpoint
$wgCirrusSearchCategoryEndpoint = 'http://wdqs-internal.discovery.wmnet/bigdata/namespace/categories/sparql';
