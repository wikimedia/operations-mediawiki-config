<?php
# WARNING: This file is publically viewable on the web. Do not put private data here.

if( $wmgRealm == 'labs' ) {  # safe guard

$wgJobTypeConf['default'] = array(
	'class'       => 'JobQueueRedis',
	'redisServer' => '10.4.0.83', # deployment-redisdb
	'redisConfig' => array(
		'connectTimeout' => 1,
		'password' => $wmgRedisPassword,
	)
);

$wgJobQueueAggregator = array(
	'class'       => 'JobQueueAggregatorRedis',
	'redisServer' => '10.4.0.83', # deployment-redisdb
	'redisConfig' => array(
		'connectTimeout' => 1,
		'password' => $wmgRedisPassword,
	)
);

} # end safe guard
