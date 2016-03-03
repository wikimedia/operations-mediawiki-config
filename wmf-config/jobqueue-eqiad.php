<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

/** @var string $wmgRedisPassword From PrivateSettings.php */

$wmgRedisQueueBaseConfig = array(
	'class' => 'JobQueueRedis',
	'redisConfig' => array(
		'connectTimeout' => .200,
		'password' => $wmgRedisPassword,
		'compression' => 'gzip',
		'persistent' => defined( 'MEDIAWIKI_JOB_RUNNER' )
	),
	'daemonized' => true
);

// Note: on server failure, partition masters should be switched to the slave
// Note: MediaWiki will fail-over to other shards when one is down. On master
// failure, it is best to either do nothing or just disable the whole shard
// until the master is fixed or full fail-over is done. Proper fail over
// requires changing the slave to stop slaving the old master before updating
// the MediaWiki config to direct traffic there.
$wgJobTypeConf['default'] = array(
	'class' => 'JobQueueFederated',
	'configByPartition' => array(
		# rdb 1

		'rdb1-6379' => array(
				'redisServer' => 'rdb1001.eqiad.wmnet:6379', # master
				#'redisServer' => 'rdb1002.eqiad.wmnet:6379', # slave
			) + $wmgRedisQueueBaseConfig,
		'rdb1-6380' => array(
				'redisServer' => 'rdb1001.eqiad.wmnet:6380', # master
				#'redisServer' => 'rdb1002.eqiad.wmnet:6380', # slave
			) + $wmgRedisQueueBaseConfig,
		'rdb1-6381' => array(
				'redisServer' => 'rdb1001.eqiad.wmnet:6381', # master
				#'redisServer' => 'rdb1002.eqiad.wmnet:6381', # slave
			) + $wmgRedisQueueBaseConfig,

		# rdb2

		'rdb2-6379' => array(
				'redisServer' => 'rdb1003.eqiad.wmnet:6379', # master
				#'redisServer' => 'rdb1004.eqiad.wmnet:6379', # slave
			) + $wmgRedisQueueBaseConfig,
		'rdb2-6380' => array(
				'redisServer' => 'rdb1003.eqiad.wmnet:6380', # master
				#'redisServer' => 'rdb1004.eqiad.wmnet:6380', # slave
			) + $wmgRedisQueueBaseConfig,
		'rdb2-6381' => array(
				'redisServer' => 'rdb1003.eqiad.wmnet:6381', # master
				#'redisServer' => 'rdb1004.eqiad.wmnet:6381', # slave
			) + $wmgRedisQueueBaseConfig,

		# rdb3

		'rdb3-6379' => array(
				'redisServer' => 'rdb1007.eqiad.wmnet:6379', # master
				#'redisServer' => 'rdb1008.eqiad.wmnet:6379', # slave
			) + $wmgRedisQueueBaseConfig,
		'rdb3-6380' => array(
				'redisServer' => 'rdb1007.eqiad.wmnet:6380', # master
				#'redisServer' => 'rdb1008.eqiad.wmnet:6380', # slave
			) + $wmgRedisQueueBaseConfig,
		'rdb3-6381' => array(
				'redisServer' => 'rdb1007.eqiad.wmnet:6381', # master
				#'redisServer' => 'rdb1008.eqiad.wmnet:6381', # slave
			) + $wmgRedisQueueBaseConfig,

		# rdb4

		'rdb4-6379' => array(
				'redisServer' => 'rdb1005.eqiad.wmnet:6379', # master
				#'redisServer' => 'rdb1006.eqiad.wmnet:6379', # slave
			) + $wmgRedisQueueBaseConfig,
		'rdb4-6380' => array(
				'redisServer' => 'rdb1005.eqiad.wmnet:6380', # master
				#'redisServer' => 'rdb1006.eqiad.wmnet:6380', # slave
			) + $wmgRedisQueueBaseConfig,
		'rdb4-6381' => array(
				'redisServer' => 'rdb1005.eqiad.wmnet:6381', # master
				#'redisServer' => 'rdb1006.eqiad.wmnet:6381', # slave
			) + $wmgRedisQueueBaseConfig,
	),
	'sectionsByWiki' => array(), // default
	'partitionsBySection' => array( // weights for partitions in use
		'default' => array(
			'rdb1-6379' => 50,
			'rdb1-6380' => 50,
			'rdb1-6381' => 50,
			'rdb2-6379' => 50,
			'rdb2-6380' => 50,
			'rdb2-6381' => 50,
			'rdb3-6379' => 50,
			'rdb3-6380' => 50,
			'rdb3-6381' => 50,
			'rdb4-6379' => 50,
			'rdb4-6380' => 50,
			'rdb4-6381' => 50,
		),
	),
	'maxPartitionsTry' => 5 // always covers 2+ servers
);
// Note: on server failure, this should be changed to any other redis server
$wgJobQueueAggregator = array(
	'class' => 'JobQueueAggregatorRedis',
	'redisServers' => array(
		'rdb1001.eqiad.wmnet:6378', // preferred
		'rdb1003.eqiad.wmnet:6378', // fallback
		'rdb1005.eqiad.wmnet:6378', // fallback
		'rdb1007.eqiad.wmnet:6378', // fallback
	),
	'redisConfig' => array(
		'connectTimeout' => 0.5,
		'password' => $wmgRedisPassword,
	)
);

$wgJobSerialCommitThreshold = .100; // 100 ms
