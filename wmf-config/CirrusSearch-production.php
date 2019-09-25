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
// within the default 30s timeout.
$wgCirrusSearchMasterTimeout = '5m';

$cirrusConfigUseHhvmPool = function ( $hosts, $pool ) {
	if ( !defined( 'HHVM_VERSION' ) ) {
		return $hosts;
	}
	if ( !is_array( $hosts ) ) {
		return $hosts;
	}
	return array_map( function ( $hostConfig ) use ( $pool ) {
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
	}, $hosts );
};

$wgCirrusSearchClusters = [
	'eqiad-chi' => $cirrusConfigUseHhvmPool( $wmfAllServices['eqiad']['search-chi'], 'cirrus-eqiad' ) + [ 'group' => 'chi', 'replica' => 'eqiad' ],
	'codfw-chi' => $cirrusConfigUseHhvmPool( $wmfAllServices['codfw']['search-chi'], 'cirrus-codfw' ) + [ 'group' => 'chi', 'replica' => 'codfw' ],
	// cloudelastic servers talk directly, with no connection pool, as only jobrunners ever communicate with them. Additionally cloudelastic
	// is only available in the 'production' realm, which this file services.
	'cloudelastic-chi' => $wmfAllServices['eqiad']['cloudelastic-chi'] + [ 'group' => 'chi', 'replica' => 'cloudelastic' ],
	'eqiad-psi' => $cirrusConfigUseHhvmPool( $wmfAllServices['eqiad']['search-psi'], 'cirrus-eqiad' ) + [ 'group' => 'psi', 'replica' => 'eqiad' ],
	'codfw-psi' => $cirrusConfigUseHhvmPool( $wmfAllServices['codfw']['search-psi'], 'cirrus-codfw' ) + [ 'group' => 'psi', 'replica' => 'codfw' ],
	'cloudelastic-psi' => $wmfAllServices['eqiad']['cloudelastic-psi'] + [ 'group' => 'psi', 'replica' => 'cloudelastic' ],
	'eqiad-omega' => $cirrusConfigUseHhvmPool( $wmfAllServices['eqiad']['search-omega'], 'cirrus-eqiad' ) + [ 'group' => 'omega', 'replica' => 'eqiad' ],
	'codfw-omega' => $cirrusConfigUseHhvmPool( $wmfAllServices['codfw']['search-omega'], 'cirrus-codfw' ) + [ 'group' => 'omega', 'replica' => 'codfw' ],
	'cloudelastic-omega' => $wmfAllServices['eqiad']['cloudelastic-omega'] + [ 'group' => 'omega', 'replica' => 'cloudelastic' ],
];

unset( $cirrusConfigUseHhvmPool );

// Limit archive indices to our internal search clusters (and not cloudelastic)
$wgCirrusSearchPrivateClusters = [ 'eqiad', 'codfw' ];

// TODO: Remove once running elastic 6.x
// ref: https://github.com/elastic/elasticsearch/issues/26833
$wgCirrusSearchElasticQuirks = [
	'cross_cluster_single_shard_search' => true
];

$wgCirrusSearchCrossClusterSearch = true;

// wgCirrusSearchExtraIndexes is set in CirrusSearch-common.php
if ( isset( $wgCirrusSearchExtraIndexes[NS_FILE] ) ) {
	$wgCirrusSearchExtraIndexes[NS_FILE] = [ 'chi:commonswiki_file' ];
}

$wgCirrusSearchSanityCheck = true;

$wgCirrusSearchConnectionAttempts = 3;

if ( $wgDBname == 'enwiki' ) {
	$wgCirrusSearchPoolCounterKey = '_elasticsearch_enwiki';
}

$wgCirrusSearchEnableSearchLogging = true;

// The default configuration is a single-cluster configuration, expand
// that here into the necessary multi-cluster config
$wgCirrusSearchShardCount = [
	'eqiad' => $wmgCirrusSearchShardCount,
	'codfw' => $wmgCirrusSearchShardCount,
	'cloudelastic' => $wmgCirrusSearchShardCount,
];

// 5 second timeout for local cluster, 10 seconds for remote.
$wgCirrusSearchClientSideConnectTimeout = [
	'eqiad' => $wmfDatacenter === 'eqiad' ? 5 : 10,
	'codfw' => $wmfDatacenter === 'codfw' ? 5 : 10,
	'cloudelastic' => $wmfDatacenter === 'eqiad' ? 5 : 10,
];

$wgCirrusSearchDropDelayedJobsAfter = [
	'eqiad' => $wgCirrusSearchDropDelayedJobsAfter,
	'codfw' => $wgCirrusSearchDropDelayedJobsAfter,
	// cloudelastic jobs are dropped after 15 minutes, it is not as important as prod services
	// and can be backfilled as necessary.
	'cloudelastic' => 900,
];

// cache morelike queries to ObjectCache for 24 hours
$wgCirrusSearchMoreLikeThisTTL = 86400;

// Internal WDQS endpoint
$wgCirrusSearchCategoryEndpoint = 'http://wdqs-internal.discovery.wmnet/bigdata/namespace/categories/sparql';
