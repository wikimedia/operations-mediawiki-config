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
			'transport' => 'CirrusSearch\\Elastica\\PooledHttps',
			'port' => '9243',
			'host' => $host,
			'config' => [
				'pool' => 'cirrus-eqiad',
			],
		];
	}, $wmfAllServices['eqiad']['search'] ),
];

if ( $wgDBname == 'enwiki' ) {
	$wgCirrusSearchEnableCrossProjectSearch = true;
}

# We don't have enough nodes to support these settings in beta so just turn
# them off.
$wgCirrusSearchMaxShardsPerNode = [];

# Override prod configuration, there is only one cluster in beta
$wgCirrusSearchDefaultCluster = 'eqiad';
# Don't specially configure cluster for more like queries in beta
$wgCirrusSearchFullTextClusterOverrides = [];
# write to all configured clusters, there should only be one in labs
$wgCirrusSearchWriteClusters = null;

$wgCirrusSearchEnableSearchLogging = true;

$wgCirrusSearchLanguageToWikiMap = [
	'ar' => 'ar',
	'de' => 'de',
	'en' => 'en',
	'es' => 'es',
	'fa' => 'fa',
	'he' => 'he',
	'hi' => 'hi',
	'ja' => 'ja',
	'ko' => 'ko',
	'ru' => 'ru',
	'sq' => 'sq',
	'uk' => 'uk',
	'zh-cn' => 'zh',
	'zh-tw' => 'zh',
];

# Force the number of replicas to 1 max for the beta cluster
$wgCirrusSearchReplicas = '0-1';

# Quirks for elasticsearch >5 <5.3
$wgCirrusSearchElasticQuirks['query_string_max_determinized_states'] = true;
