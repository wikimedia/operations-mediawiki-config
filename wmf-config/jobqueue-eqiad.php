<?php
# WARNING: This file is publically viewable on the web. Do not put private data here.

// Note: on server failure, partition masters should be switched to the slave
// Note: MediaWiki will fail-over to other shards when one is down. On master
// failure, it is best to either do nothing or just disable the whole shard
// until the master is fixed or full fail-over is done. Proper fail over
// requires changing the slave to stop slaving the old master before updating
// the MediaWiki config to direct traffic there.
if ( $wgDBname === 'labswiki' ) {
	// Don't set up the job queue for labswiki, which is misconfigured at the
	// moment and spewing errors. -- Ori, 13-Feb-2015.
	return;
}

$wgJobTypeConf['default'] = array(
	'class'               => 'JobQueueFederated',
	'configByPartition'   => array(
		'rdb1' => array(
			'class'       => 'JobQueueRedis',
			'redisServer' => '10.64.32.76', # rdb1001 (master)
			#'redisServer' => '10.64.32.77', # rdb1002 (slave)
			'redisConfig' => array(
				'connectTimeout' => 2,
				'password' => $wmgRedisPassword,
				'compression' => 'gzip'
			),
			'daemonized' => true
		),
		'rdb2' => array(
			'class'       => 'JobQueueRedis',
			'redisServer' => '10.64.0.201', # rdb1003 (master)
			#'redisServer' => '10.64.16.183', # rdb1004 (slave)
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
		'10.64.32.76', # rdb1001
		'10.64.0.201', # rdb1003
	),
	'redisConfig'  => array(
		'connectTimeout' => 2,
		'password' => $wmgRedisPassword,
	)
);
