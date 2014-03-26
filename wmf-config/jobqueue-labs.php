<?php
# WARNING: This file is publically viewable on the web. Do not put private data here.

if( $wmfRealm == 'labs' ) {  # safe guard

if( $wmfDatacenter === 'pmtpa' ) {
	$jobRedisServer = '10.4.0.83';  # deployment-redisdb
} else {  # eqiad
	$jobRedisServer = '10.68.16.146';  # deployment-redis01
}

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
