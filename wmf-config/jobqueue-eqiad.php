<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

// Note: on server failure, partition masters should be switched to the slave
// Note: MediaWiki will fail-over to other shards when one is down. On master
// failure, it is best to either do nothing or just disable the whole shard
// until the master is fixed or full fail-over is done. Proper fail over
// requires changing the slave to stop slaving the old master before updating
// the MediaWiki config to direct traffic there.
$wgJobTypeConf['default'] = array(
	'class'               => 'JobQueueFederated',
	'configByPartition'   => array(
		'rdb1' => array(
			'class'       => 'JobQueueRedis',
			'redisServer' => 'rdb1001.eqiad.wmnet', # master
			#'redisServer' => 'rdb1002.eqiad.wmnet', # slave
			'redisConfig' => array(
				'connectTimeout' => 2,
				'password' => $wmgRedisPassword,
				'compression' => 'gzip'
			),
			'daemonized' => true
		),
		'rdb2' => array(
			'class'       => 'JobQueueRedis',
			'redisServer' => 'rdb1003.eqiad.wmnet', # master
			#'redisServer' => 'rdb1004.eqiad.wmnet', # slave
			'redisConfig' => array(
				'connectTimeout' => 2,
				'password' => $wmgRedisPassword,
				'compression' => 'gzip'
			),
			'daemonized' => true
		),
		'rdb3' => array(
			'class'       => 'JobQueueRedis',
			'redisServer' => 'rdb1007.eqiad.wmnet', # master
			#'redisServer' => 'rdb1008.eqiad.wmnet', # slave
			'redisConfig' => array(
				'connectTimeout' => 2,
				'password' => $wmgRedisPassword,
				'compression' => 'gzip'
			),
			'daemonized' => true
		),
	),
	'sectionsByWiki'      => array(), // default
	'partitionsBySection' => array(
		'default' => array( 'rdb1' => 50, 'rdb2' => 50, 'rdb3' => 50 ),
	)
);
// Note: on server failure, this should be changed to any other redis server
$wgJobQueueAggregator = array(
	'class'        => 'JobQueueAggregatorRedis',
	'redisServers' => array( // all after the first are fallbacks
		'rdb1001.eqiad.wmnet',
		'rdb1003.eqiad.wmnet',
		'rdb1007.eqiad.wmnet',
	),
	'redisConfig'  => array(
		'connectTimeout' => 2,
		'password' => $wmgRedisPassword,
	)
);

$wgJobSerialCommitThreshold = .100; // 100 ms
