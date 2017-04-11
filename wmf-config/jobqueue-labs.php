<?php
# WARNING: This file is publically viewable on the web. Do not put private data here.

if ( $wgWMFRealm == 'labs' ) {  # safe guard

$jobRedisServer = 'deployment-redis01.eqiad.wmflabs';

$wgJobTypeConf['default'] = [
	'class'       => 'JobQueueRedis',
	'redisServer' => $jobRedisServer,
	'redisConfig' => [
		'connectTimeout' => 1,
		'password' => $wgWMFRedisPassword,
	],
	'daemonized' => true
];

$wgJobQueueAggregator = [
	'class'       => 'JobQueueAggregatorRedis',
	'redisServer' => $jobRedisServer,
	'redisConfig' => [
		'connectTimeout' => 1,
		'password' => $wgWMFRedisPassword,
	],
	'daemonized' => true
];
unset( $jobRedisServer );

} # end safe guard
