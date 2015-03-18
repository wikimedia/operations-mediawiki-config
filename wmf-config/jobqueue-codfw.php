<?php
# WARNING: This file is publically viewable on the web. Do not put private data here.

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
			'redisServer' => '10.192.0.119', # rdb2001 (master)
			#'redisServer' => '10.192.16.122', # rdb2003 (slave)
			'redisConfig' => array(
				'connectTimeout' => 2,
				'password' => $wmgRedisPassword,
				'compression' => 'gzip'
			),
			'daemonized' => true
		),
		'rdb2' => array(
			'class'       => 'JobQueueRedis',
			'redisServer' => '10.192.0.120', # rdb2002 (master)
			#'redisServer' => '10.192.16.123', # rdb2004 (slave)
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
		'default' => array( 'rdb1' => 50, 'rdb2' => 50 ),
	)
);
// Note: on server failure, this should be changed to any other redis server
$wgJobQueueAggregator = array(
	'class'        => 'JobQueueAggregatorRedis',
	'redisServers' => array( // all after the first are fallbacks
		'10.192.0.199', # rdb2001
		'10.192.0.120', # rdb2002
	),
	'redisConfig'  => array(
		'connectTimeout' => 2,
		'password' => $wmgRedisPassword,
	)
);
