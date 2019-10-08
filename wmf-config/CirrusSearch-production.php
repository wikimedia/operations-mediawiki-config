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


$wgCirrusSearchClusters = [
	'eqiad-chi' => $wmfAllServices['eqiad']['search-chi'] + [ 'group' => 'chi', 'replica' => 'eqiad' ],
	'codfw-chi' => $wmfAllServices['codfw']['search-chi'] + [ 'group' => 'chi', 'replica' => 'codfw' ],
	'cloudelastic-chi' => $wmfAllServices['eqiad']['cloudelastic-chi'] + [ 'group' => 'chi', 'replica' => 'cloudelastic' ],
	'eqiad-psi' => $wmfAllServices['eqiad']['search-psi'] + [ 'group' => 'psi', 'replica' => 'eqiad' ],
	'codfw-psi' => $wmfAllServices['codfw']['search-psi'] + [ 'group' => 'psi', 'replica' => 'codfw' ],
	'cloudelastic-psi' => $wmfAllServices['eqiad']['cloudelastic-psi'] + [ 'group' => 'psi', 'replica' => 'cloudelastic' ],
	'eqiad-omega' => $wmfAllServices['eqiad']['search-omega'] + [ 'group' => 'omega', 'replica' => 'eqiad' ],
	'codfw-omega' => $wmfAllServices['codfw']['search-omega'] + [ 'group' => 'omega', 'replica' => 'codfw' ],
	'cloudelastic-omega' => $wmfAllServices['eqiad']['cloudelastic-omega'] + [ 'group' => 'omega', 'replica' => 'cloudelastic' ],
];

// wgCirrusSearchExtraIndexes is set in CirrusSearch-common.php
if ( isset( $wgCirrusSearchExtraIndexes[NS_FILE] ) ) {
	$wgCirrusSearchExtraIndexes[NS_FILE] = [ 'chi:commonswiki_file' ];
}

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
