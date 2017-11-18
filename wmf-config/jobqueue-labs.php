<?php
# WARNING: This file is publically viewable on the web. Do not put private data here.

if ( $wmgRealm == 'labs' ) {  # safe guard
$jobRedisServer = 'deployment-redis01.eqiad.wmflabs';

$jobQueueRedisConfig = [
	'class'       => 'JobQueueRedis',
	'redisServer' => $jobRedisServer,
	'redisConfig' => [
		'connectTimeout' => 1,
		'password' => $wmgRedisPassword,
		'persistent' => defined( 'MEDIAWIKI_JOB_RUNNER' ),
	],
	'daemonized' => true
];

if ( $wmgUseEventBus ) {
	$wgJobTypeConf['default'] = [
		'class' => 'JobQueueEventBus'
	];
} else {
	$wgJobTypeConf['default'] = $jobQueueRedisConfig;
}

$wgJobQueueAggregator = [
	'class'       => 'JobQueueAggregatorRedis',
	'redisServer' => $jobRedisServer,
	'redisConfig' => [
		'connectTimeout' => 1,
		'password' => $wmgRedisPassword,
	],
	'daemonized' => true
];
unset( $jobRedisServer );
unset( $jobQueueRedisConfig );

} # end safe guard
