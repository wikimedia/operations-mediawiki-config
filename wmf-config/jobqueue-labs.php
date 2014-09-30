<?php
# WARNING: This file is publically viewable on the web. Do not put private data here.

if( $wmfRealm == 'labs' ) {  # safe guard

$jobRedisServer = 'deployment-redis01.eqiad.wmflabs';

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
