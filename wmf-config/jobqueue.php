<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

/** @var string $wmgRedisPassword From PrivateSettings.php */

$wmgRedisQueueBaseConfig = array(
	'class' => 'JobQueueRedis',
	'redisConfig' => array(
		'connectTimeout' => .300,
		'password' => $wmgRedisPassword,
		'compression' => 'gzip',
		#'persistent' => defined( 'MEDIAWIKI_JOB_RUNNER' )
	),
	'daemonized' => true
);

function wmfRedisConfigByPartition( $base, $partitions ) {
	$result = array();
	foreach ( $partitions as $partition => $addr ) {
		$result[$partition] = array('redisServer' => $addr) + $base;
	}
	return $result;
}

$wgJobTypeConf['default'] = array(
	'class' => 'JobQueueFederated',
	'configByPartition' => wmfRedisConfigByPartition(
		$wmgRedisQueueBaseConfig,
		$wmfLocalServices['jobqueue_redis']
	),
	'sectionsByWiki' => array(), // default
	// Weights for partitions in use: use this to depool redis masters
	'partitionsBySection' => array(
		'default' => array_fill_keys( array_keys( $wmfLocalServices['jobqueue_redis'] ), 50 )
	),
	'maxPartitionsTry' => 5 // always covers 2+ servers
);
// Note: on server failure, this should be changed to any other redis server
$wgJobQueueAggregator = array(
	'class' => 'JobQueueAggregatorRedis',
	'redisServers' => $wmfLocalServices['jobqueue_aggregator'],
	'redisConfig' => array(
		'connectTimeout' => 0.5,
		'password' => $wmgRedisPassword,
	)
);

$wgJobSerialCommitThreshold = .100; // 100 ms
