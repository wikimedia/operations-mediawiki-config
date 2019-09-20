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
	'eqiad' => array_map( function ( $host ) {
		return [
			'transport' => 'Https',
			'port' => '9243',
			'host' => $host,
		];
	}, $wmfAllServices['eqiad']['search-chi'] ),
];
