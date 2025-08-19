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
	'dnsdisc-chi' => $wmgLocalServices['search-chi-dnsdisc'] + [ 'group' => 'chi', 'replica' => 'dnsdisc' ],
	'eqiad-chi' => $wmgAllServices['eqiad']['search-chi'] + [ 'group' => 'chi', 'replica' => 'eqiad' ],
	'codfw-chi' => $wmgAllServices['codfw']['search-chi'] + [ 'group' => 'chi', 'replica' => 'codfw' ],
	'cloudelastic-chi' => $wmgAllServices['eqiad']['cloudelastic-chi'] + [ 'group' => 'chi', 'replica' => 'cloudelastic' ],

	'dnsdisc-psi' => $wmgLocalServices['search-psi-dnsdisc'] + [ 'group' => 'psi', 'replica' => 'dnsdisc' ],
	'eqiad-psi' => $wmgAllServices['eqiad']['search-psi'] + [ 'group' => 'psi', 'replica' => 'eqiad' ],
	'codfw-psi' => $wmgAllServices['codfw']['search-psi'] + [ 'group' => 'psi', 'replica' => 'codfw' ],
	'cloudelastic-psi' => $wmgAllServices['eqiad']['cloudelastic-psi'] + [ 'group' => 'psi', 'replica' => 'cloudelastic' ],

	'dnsdisc-omega' => $wmgLocalServices['search-omega-dnsdisc'] + [ 'group' => 'omega', 'replica' => 'dnsdisc' ],
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

// CirrusSearch internal user allowed to bypass cirrusbuilddoc PoolCounter protection (T401220)
// (This user is declared in the private settings)
$wgCirrusSearchStreamingUpdaterUsername = 'CirrusSearch Streaming Updater';
