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
		'logstash' => array(
			'class' => '\\Monolog\\Handler\\RedisHandler',
			'args' => array(
				function() use ( $wmgLogstashPassword ) {
					$redis = new Redis();
					// deployment-logstash1.eqiad.wmflabs
					$redis->connect( '10.68.16.134', 6379, .25 );
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
	if ( is_array( $dest ) ) {
		$dest = $dest['destination'];
	}
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
