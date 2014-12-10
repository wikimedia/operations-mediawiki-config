<?php
// Monolog logging configuration
// Note: the legacy handlers still use $wgDebugLogGroups and other legacy
// logging config variables to determine logging output.
$wmgMonologConfig = array(
	'loggers' => array(
		// Nothing is logged unless a specific channel config is added
		'@default' => array(
			'handlers' => array( 'null' ),
		),
	),

	'processors' => array(
		'wiki' => array(
			'class' => 'MWLoggerMonologProcessor',
		),
		'psr' => array(
			'class' => '\\Monolog\\Processor\\PsrLogMessageProcessor',
		),
		'pid' => array(
			'class' => '\\Monolog\\Processor\\ProcessIdProcessor',
		),
		'uid' => array(
			'class' => '\\Monolog\\Processor\\UidProcessor',
		),
		'web' => array(
			'class' => '\\Monolog\\Processor\\WebProcessor',
		),
	),

	'handlers' => array(
		'null' => array(
			'class' => '\\Monolog\\Handler\\NullHandler',
		),
		'logstash' => array(
			'class' => '\\Monolog\\Handler\\RedisHandler',
			'args' => array(
				function() use ( $wmgLogstashPassword ) {
					$redis = new Redis();
					// Spread connections randomly across all logstash
					// hosts.
					$server = array_rand( array(
						'10.64.32.138', // logstash1001.eqiad.wmnet
						'10.64.32.137', // logstash1002.eqiad.wmnet
						'10.64.32.136', // logstash1003.eqiad.wmnet
					) );
					$redis->connect( $server, 6379, .25 );
					$redis->auth( $wmgLogstashPassword );
					return $redis;
				},
				'logstash'
			),
			'formatter' => 'logstash',
		),
	),

	'formatters' => array(
		'legacy' => array(
			'class' => 'MWLoggerMonologLegacyFormatter',
		),
		'logstash' => array(
			'class' => '\\Monolog\\Formatter\\LogstashFormatter',
			'args'  => array( 'mediawiki', php_uname( 'n' ), null, '', 1 ),
		),
	),
);

// Add logging channels defined in $wgDebugLogGroups
foreach ( $wgDebugLogGroups as $group => $dest ) {
	$wmgMonologConfig['loggers'][$group] = array(
		'handlers' => array( $group, 'logstash' ),
		'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
	);
	$wmgMonologConfig['handlers'][$group] = array(
		'class' => 'MWLoggerMonologHandler',
		'args' => array( $dest, true ),
		'formatter' => 'legacy',
	);
}

$wgMWLoggerDefaultSpi = array(
	'class' => 'MWLoggerMonologSpi',
	'args' => array( $wmgMonologConfig ),
);
