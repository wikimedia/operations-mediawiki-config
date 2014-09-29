<?php
# WARNING: This file is publically viewable on the web. Do not put private data here.

if( $wmfRealm == 'labs' ) {  # safe guard

$jobRedisServer = '10.68.16.177';  # deployment-redis01

$wgJobTypeConf['default'] = array(
	'class'       => 'JobQueueRedis',
	'redisServer' => $jobRedisServer,
	'redisConfig' => array(
		'connectTimeout' => 1,
		'password' => $wmgRedisPassword,
	)
);

$wgJobQueueAggregator = array(
	'class'       => 'JobQueueAggregatorRedis',
	'redisServer' => $jobRedisServer,
	'redisConfig' => array(
		'connectTimeout' => 1,
		'password' => $wmgRedisPassword,
	)
);
unset($jobRedisServer);

} # end safe guard
