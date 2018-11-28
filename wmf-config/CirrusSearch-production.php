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

$wgCirrusSearchClusters = [
	'eqiad-chi' => $wmfAllServices['eqiad']['search-chi'] + [ 'group' => 'chi', 'replica' => 'eqiad' ],
	'codfw-chi' => $wmfAllServices['codfw']['search-chi'] + [ 'group' => 'chi', 'replica' => 'codfw' ],
	'eqiad-psi' => $wmfAllServices['eqiad']['search-psi'] + [ 'group' => 'psi', 'replica' => 'eqiad' ],
	'codfw-psi' => $wmfAllServices['codfw']['search-psi'] + [ 'group' => 'psi', 'replica' => 'codfw' ],
	'eqiad-omega' => $wmfAllServices['eqiad']['search-omega'] + [ 'group' => 'omega', 'replica' => 'eqiad' ],
	'codfw-omega' => $wmfAllServices['codfw']['search-omega'] + [ 'group' => 'omega', 'replica' => 'codfw' ],
];

// TODO: Remove once running elastic 6.x
// ref: https://github.com/elastic/elasticsearch/issues/26833
$wgCirrusSearchElasticQuirks['cross_cluster_single_shard_search'] = true;

$wgCirrusSearchCrossClusterSearch = true;
$wgCirrusSearchReplicaGroup = $wmgCirrusSearchReplicaGroup;

if ( isset( $wgCirrusSearchExtraIndexes[NS_FILE] ) ) {
	$wgCirrusSearchExtraIndexes[NS_FILE] = [ 'chi:commonswiki_file' ];
}

# Limit the sanitity check to eqiad&codfw
$wgCirrusSearchSanityCheck = [ 'eqiad', 'codfw' ];

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
