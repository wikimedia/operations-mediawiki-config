<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

# This file holds the CirrusSearch configuration for the Beta Cluster.
#
# This MUST NOT be loaded for production.
#
# Load tree:
#  |-- wmf-config/CommonSettings.php
#      |
#      `-- wmf-config/CirrusSearch-common.php
#          |
#          `-- wmf-config/CirrusSearch-labs.php
#

$wgCirrusSearchClusters = [
	'eqiad' => array_map( static function ( $host ) {
		return [
			'transport' => 'Https',
			'port' => '9243',
			'host' => $host,
		];
	}, $wmfAllServices['eqiad']['search-chi'] ),
];

// wgCirrusSearchShardCount is still handled through wmg vars for no good reasons
$wgCirrusSearchShardCount = $wmgCirrusSearchShardCount;
