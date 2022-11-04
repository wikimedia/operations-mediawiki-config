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
	'eqiad-chi' => $wmgAllServices['eqiad']['search-chi'] + [ 'group' => 'chi', 'replica' => 'eqiad' ],
	'codfw-chi' => $wmgAllServices['codfw']['search-chi'] + [ 'group' => 'chi', 'replica' => 'codfw' ],
	'cloudelastic-chi' => $wmgAllServices['eqiad']['cloudelastic-chi'] + [ 'group' => 'chi', 'replica' => 'cloudelastic' ],
	'eqiad-psi' => $wmgAllServices['eqiad']['search-psi'] + [ 'group' => 'psi', 'replica' => 'eqiad' ],
	'codfw-psi' => $wmgAllServices['codfw']['search-psi'] + [ 'group' => 'psi', 'replica' => 'codfw' ],
	'cloudelastic-psi' => $wmgAllServices['eqiad']['cloudelastic-psi'] + [ 'group' => 'psi', 'replica' => 'cloudelastic' ],
	'eqiad-omega' => $wmgAllServices['eqiad']['search-omega'] + [ 'group' => 'omega', 'replica' => 'eqiad' ],
	'codfw-omega' => $wmgAllServices['codfw']['search-omega'] + [ 'group' => 'omega', 'replica' => 'codfw' ],
	'cloudelastic-omega' => $wmgAllServices['eqiad']['cloudelastic-omega'] + [ 'group' => 'omega', 'replica' => 'cloudelastic' ],
];

if ( $wmgPrivateWiki ) {
	unset(
		$wgCirrusSearchClusters['cloudelastic-chi'],
		$wgCirrusSearchClusters['cloudelastic-psi'],
		$wgCirrusSearchClusters['cloudelastic-omega']
	);
}

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

$wgCirrusSearchDropDelayedJobsAfter = [
	'eqiad' => $wgCirrusSearchDropDelayedJobsAfter,
	'codfw' => $wgCirrusSearchDropDelayedJobsAfter,
	// cloudelastic jobs are dropped after 15 minutes, it is not as important as prod services
	// and can be backfilled as necessary.
	'cloudelastic' => 900,
];

// T295705#7719071 Reduce write isolation to only cloudelastic to reduce job queue rates
$wgCirrusSearchWriteIsolateClusters = [ 'cloudelastic' ];
$wgCirrusSearchElasticaWritePartitionCounts = [ 'cloudelastic' => 3 ];
