<?php
# WARNING: This file is publically viewable on the web. Do not put private data here.

if( $wmfRealm == 'labs' ) {  # safe guard

$wgJobTypeConf['default'] = array(
	'class'       => 'JobQueueRedis',
	'redisServer' => '10.4.0.83', # deployment-redisdb
	'redisConfig' => array(
		'connectTimeout' => 1,
		'password' => $wmgRedisPassword,
	)
);

if( $wmfDatacenter === 'pmtpa' ) {
	$jobRedisServer = '10.4.0.83';  # deployment-redisdb
} else {  # eqiad
	$jobRedisServer = '10.68.16.146';  # deployment-redis01
}
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
