<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

/** @var string $wmgRedisPassword From PrivateSettings.php */

$wmgRedisQueueBaseConfig = [
	'class' => 'JobQueueRedis',
	'redisConfig' => [
		'connectTimeout' => .300,
		'password' => $wmgRedisPassword,
		'compression' => 'gzip',
		# 'persistent' => defined( 'MEDIAWIKI_JOB_RUNNER' )
	],
	'daemonized' => true
];

function wmfRedisConfigByPartition( $base, $partitions ) {
	$result = [];
	foreach ( $partitions as $partition => $addr ) {
		$result[$partition] = ['redisServer' => $addr] + $base;
	}
	return $result;
}

$wgJobTypeConf['default'] = [
	'class' => 'JobQueueFederated',
	'configByPartition' => wmfRedisConfigByPartition(
		$wmgRedisQueueBaseConfig,
		$wmfLocalServices['jobqueue_redis']
	),
	'sectionsByWiki' => [], // default
	// Weights for partitions in use: use this to depool redis masters
	'partitionsBySection' => [
		'default' => array_fill_keys( array_keys( $wmfLocalServices['jobqueue_redis'] ), 50 )
	],
	'maxPartitionsTry' => 5 // always covers 2+ servers
];
// Note: on server failure, this should be changed to any other redis server
$wgJobQueueAggregator = [
	'class' => 'JobQueueAggregatorRedis',
	'redisServers' => $wmfLocalServices['jobqueue_aggregator'],
	'redisConfig' => [
		'connectTimeout' => 0.5,
		'password' => $wmgRedisPassword,
	]
];

$wgJobSerialCommitThreshold = .100; // 100 ms
