<?php
# WARNING: This file is publically viewable on the web. Do not put private data here.

// Note: on server failure, this should be switched to the slave
$wgJobTypeConf['default'] = array(
	'class'       => 'JobQueueRedis',
	'redisServer' => '10.64.32.76', # rdb1001 (master)
	#'redisServer' => '10.64.32.77', # rdb1002 (slave)
	'redisConfig' => array(
		'connectTimeout' => 1,
		'password' => $wmgRedisPassword,
	)
);
// Note: on server failure, this should be changed to any other redis server
$wgJobQueueAggregator = array(
	'class'       => 'JobQueueAggregatorRedis',
	'redisServer' => '10.64.0.180', # mc1001
	'redisConfig' => array(
		'connectTimeout' => 1,
		'password' => $wmgRedisPassword,
	)
);
# vim: set sts=4 sw=4 et :

$wgJobQueueMigrationConfig = array(
	'db'    => array(
		'class' => 'JobQueueDB'
	),
	'redis' => array(
		'class'       => 'JobQueueRedis',
		'redisServer' => '10.64.32.76',
		'redisConfig' => array(
			'connectTimeout' => 1,
			'password' => $wmgRedisPassword,
		)
	)
);
