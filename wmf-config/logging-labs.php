<?php

// tweak to logs
//

if ( $wgCommandLineMode || PHP_SAPI == 'cli' ) {
	$wgDebugLogFile = "udp://$wmfUdp2logDest/cli";
} else {
	$wgDebugLogFile = "udp://$wmfUdp2logDest/web";
}

// stream recent changes to redis
$wgRCFeeds['redis'] = array(
	'formatter' => 'JSONRCFeedFormatter',
	'uri'       => "redis://deployment-stream.eqiad.wmflabs:6379/rc.$wgDBname",
);


// udp2log logging for beta:
$wgDebugLogGroups['CentralAuthVerbose'] = "udp://$wmfUdp2logDest/centralauth";
$wgDebugLogGroups['dnsblacklist'] = "udp://$wmfUdp2logDest/dnsblacklist";
$wgDebugLogGroups['exception-json'] = "udp://$wmfUdp2logDest/exception-json";
$wgDebugLogGroups['mwsearch'] = "udp://$wmfUdp2logDest/mwsearch";
$wgDebugLogGroups['squid'] = "udp://$wmfUdp2logDest/squid";

$wgUDPProfilerHost = 'deployment-fluoride.eqiad.wmflabs';  // OL, 2013-11-14
$wgAggregateStatsID = "$wgVersion-labs";

// Ugly code to create a random hash and put it in logs
// temporary --Petrb
$randomHash = $wgDBname . '-' . substr( md5(uniqid()), 0, 8 );
function insertToken($debug) {
	global $wgOut;
	$wgOut->addHTML( "<!-- Debug token: $randomHash //-->");
}
#insertToken($randomHash);
if ( $_SERVER['SCRIPT_NAME'] != "/w/index.php" ) {
	// skip
} else {
# insertToken($randomHash);
#	echo "<!-- Debug token: $randomHash $script //-->";
}

$wgDebugLogPrefix = $randomHash . ": ";

/**
 * Create a config array for a \Monolog\Handler\RedisHandler instance.
 * @param int $level Minimum logging level at which this handler will be
 * triggered
 */
function wmMonologRedisConfigFactory( $level ) {
	global $wmgLogstashPassword;
	return array(
		'class' => '\\Monolog\\Handler\\RedisHandler',
		'args' => array(
			function() use ( $wmgLogstashPassword ) {
				$redis = new Redis();
				// deployment-logstash1.eqiad.wmflabs
				$redis->connect( '10.68.16.134', 6379, .25 );
				$redis->auth( $wmgLogstashPassword );
				return $redis;
			},
			'logstash',
			$level
		),
		'formatter' => 'logstash',
	);
}

// Monolog logging configuration
// Note: the legacy handlers still use $wgDebugLogGroups and other legacy
// logging config variables to determine logging output.
$wmgMonologConfig =  array(
	'loggers' => array(
		// Beta logs everything, in prod this would be a null logger
		'@default' => array(
			'handlers' => array( 'wgDebugLogFile' ),
			'processors' => array( 'psr' ),
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
		'wgDebugLogFile' => array(
			'class' => 'MWLoggerMonologHandler',
			'args' => array( $wgDebugLogFile, true ),
			'formatter' => 'legacy',
		),
		'logstash' => wmMonologRedisConfigFactory( \Monolog\Logger::DEBUG ),
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
	$sample = false;
	$level = false;
	$logstashHandler = 'logstash';
	if ( is_array( $dest ) ) {
		// NOTE: sampled logs are not guaranteed to store the same events in
		// logstash and via udp2log since the two event handlers independently
		// check the probability of emitting.
		$sample = isset( $dest['sample'] ) ? $dest['sample'] : $sample;
		$level = isset( $dest['level'] ) ? $dest['level'] : $level;
		$dest = $dest['destination'];
	}

	if ( $level !== false ) {
		$logstashHandler = "filtered-{$group}";
		$wmgMonologConfig['handlers'][$logstashHandler] =
			wmMonologRedisConfigFactory( $level );
	}

	if ( $sample === false ) {
		$handlers = array( $group, $logstashHandler );
	} else {
		$wmgMonologConfig['handlers']["sampled-{$group}"] = array(
			'class' => 'MWLoggerMonologSamplingHandler',
			'args' => array(
				function() use ( $logstashHandler ) {
					return MWLogger::getProvider()->getHandler(
						$logstashHandler
					);
				},
				$sample,
			),
		);
		$handlers = array( $group, "sampled-{$group}" );
	}

	$wmgMonologConfig['loggers'][$group] = array(
		'handlers' => $handlers,
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
