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
$wgMWLoggerDefaultSpi = array(
	'class' => 'MWLoggerMonologSpi',
	'args' => array( array(
		'loggers' => array(
			// Beta logs everything, in prod this would be a null logger
			'@default' => array(
				'handlers' => array( 'wgDebugLogFile' ),
				'processors' => array( 'psr' ),
			),
			'404' => array(
				'handlers' => array( '404', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'antispoof' => array(
				'handlers' => array( 'antispoof', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'api' => array(
				'handlers' => array( 'api', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'api-feature-usage' => array(
				'handlers' => array( 'api-feature-usage', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'badpass' => array(
				'handlers' => array( 'badpass', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'BounceHandler' => array(
				'handlers' => array( 'BounceHandler', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'Bug40009' => array(
				'handlers' => array( 'Bug40009', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'bug46577' => array(
				'handlers' => array( 'bug46577', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'Bug54847' => array(
				'handlers' => array( 'Bug54847', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'Bug58676' => array(
				'handlers' => array( 'Bug58676', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'captcha' => array(
				'handlers' => array( 'captcha', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'CentralAuth' => array(
				'handlers' => array( 'CentralAuth', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'CentralAuth-Bug39996' => array(
				'handlers' => array( 'CentralAuth-Bug39996', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'CentralAuthRename' => array(
				'handlers' => array( 'CentralAuthRename', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'CentralAuthUserMerge' => array(
				'handlers' => array( 'CentralAuthUserMerge', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'CentralAuthVerbose' => array(
				'handlers' => array( 'CentralAuthVerbose', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'CirrusSearch' => array(
				'handlers' => array( 'CirrusSearch', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'CirrusSearchChangeFailed' => array(
				'handlers' => array( 'CirrusSearchChangeFailed', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'CirrusSearchRequests' => array(
				'handlers' => array( 'CirrusSearchRequests', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'CirrusSearchSlowRequests' => array(
				'handlers' => array( 'CirrusSearchSlowRequests', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'cite' => array(
				'handlers' => array( 'cite', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'collection' => array(
				'handlers' => array( 'collection', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'DBPerformance' => array(
				'handlers' => array( 'DBPerformance', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'dnsblacklist' => array(
				'handlers' => array( 'dnsblacklist', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'Echo' => array(
				'handlers' => array( 'Echo', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'es-hit' => array(
				'handlers' => array( 'es-hit', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'EventLogging' => array(
				'handlers' => array( 'EventLogging', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'exception' => array(
				'handlers' => array( 'exception', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'exception-json' => array(
				'handlers' => array( 'exception-json', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'exec' => array(
				'handlers' => array( 'exec', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'ExternalStoreDB' => array(
				'handlers' => array( 'ExternalStoreDB', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'FileOperation' => array(
				'handlers' => array( 'FileOperation', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'Flow' => array(
				'handlers' => array( 'Flow', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'FSFileBackend' => array(
				'handlers' => array( 'FSFileBackend', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'generated-pp-node-count' => array(
				'handlers' => array( 'generated-pp-node-count', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'GettingStarted' => array(
				'handlers' => array( 'GettingStarted', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'GlobalTitleFail' => array(
				'handlers' => array( 'GlobalTitleFail', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'jobqueue' => array(
				'handlers' => array( 'jobqueue', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'JobQueueRedis' => array(
				'handlers' => array( 'JobQueueRedis', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'lc-recache' => array(
				'handlers' => array( 'lc-recache', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'logging' => array(
				'handlers' => array( 'logging', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'MassMessage' => array(
				'handlers' => array( 'MassMessage', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'memcached-serious' => array(
				'handlers' => array( 'memcached-serious', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'mobile' => array(
				'handlers' => array( 'mobile', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'mwsearch' => array(
				'handlers' => array( 'mwsearch', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'oai' => array(
				'handlers' => array( 'oai', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'OutputBuffer' => array(
				'handlers' => array( 'OutputBuffer', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'poolcounter' => array(
				'handlers' => array( 'poolcounter', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'query' => array(
				'handlers' => array( 'query', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'ratelimit' => array(
				'handlers' => array( 'ratelimit', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'recursion-guard' => array(
				'handlers' => array( 'recursion-guard', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'redis' => array(
				'handlers' => array( 'redis', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'resourceloader' => array(
				'handlers' => array( 'resourceloader', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'runJobs' => array(
				'handlers' => array( 'runJobs', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'session' => array(
				'handlers' => array( 'session', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'SimpleAntiSpam' => array(
				'handlers' => array( 'SimpleAntiSpam', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'slow-parse' => array(
				'handlers' => array( 'slow-parse', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'SpamBlacklistHit' => array(
				'handlers' => array( 'SpamBlacklistHit', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'SpamRegex' => array(
				'handlers' => array( 'SpamRegex', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'SQLBagOStuff' => array(
				'handlers' => array( 'SQLBagOStuff', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'squid' => array(
				'handlers' => array( 'squid', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'SwiftBackend' => array(
				'handlers' => array( 'SwiftBackend', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'temp-debug' => array(
				'handlers' => array( 'temp-debug', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'texvc' => array(
				'handlers' => array( 'texvc', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'thumbnail' => array(
				'handlers' => array( 'thumbnail', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'torblock' => array(
				'handlers' => array( 'torblock', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'ts_badpass' => array(
				'handlers' => array( 'ts_badpass', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'updateTranstagOnNullRevisions' => array(
				'handlers' => array( 'updateTranstagOnNullRevisions', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'upload' => array(
				'handlers' => array( 'upload', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'wikibase-debug' => array(
				'handlers' => array( 'wikibase-debug', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'Wikibase\UpdateRepoOnMoveJob' => array(
				'handlers' => array( 'Wikibase\UpdateRepoOnMoveJob', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
			),
			'zero' => array(
				'handlers' => array( 'zero', 'logstash' ),
				'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
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
					function() {
						$redis = new Redis();
						$redis->connect( '127.0.0.1', 6379 );
						return $redis;
					},
					'logstash'
				),
				'formatter' => 'logstash',
			),
			'wgDebugLogFile' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( $wgDebugLogFile, true ),
				'formatter' => 'legacy',
			),
			// Custom log channels
			'404' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/four-oh-four", true ),
				'formatter' => 'legacy',
			),
			'antispoof' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/antispoof", true ),
				'formatter' => 'legacy',
			),
			'api' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/api", true ),
				'formatter' => 'legacy',
			),
			'api-feature-usage' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/api-feature-usage", true ),
				'formatter' => 'legacy',
			),
			'badpass' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/badpass", true ),
				'formatter' => 'legacy',
			),
			'BounceHandler' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/bouncehandler", true ),
				'formatter' => 'legacy',
			),
			'Bug40009' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/Bug40009", true ),
				'formatter' => 'legacy',
			),
			'bug46577' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/bug46577", true ),
				'formatter' => 'legacy',
			),
			'Bug54847' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/Bug54847", true ),
				'formatter' => 'legacy',
			),
			'Bug58676' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/Bug58676", true ),
				'formatter' => 'legacy',
			),
			'captcha' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/captcha", true ),
				'formatter' => 'legacy',
			),
			'CentralAuth' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/centralauth", true ),
				'formatter' => 'legacy',
			),
			'CentralAuth-Bug39996' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/centralauth-bug39996", true ),
				'formatter' => 'legacy',
			),
			'CentralAuthRename' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/centralauthrename", true ),
				'formatter' => 'legacy',
			),
			'CentralAuthUserMerge' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/centralauthusermerge", true ),
				'formatter' => 'legacy',
			),
			'CentralAuthVerbose' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/centralauth", true ),
				'formatter' => 'legacy',
			),
			'CirrusSearch' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/CirrusSearch", true ),
				'formatter' => 'legacy',
			),
			'CirrusSearchChangeFailed' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/CirrusSearch-failed", true ),
				'formatter' => 'legacy',
			),
			'CirrusSearchRequests' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/CirrusSearch-all", true ),
				'formatter' => 'legacy',
			),
			'CirrusSearchSlowRequests' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/CirrusSearch-slow", true ),
				'formatter' => 'legacy',
			),
			'cite' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/cite", true ),
				'formatter' => 'legacy',
			),
			'collection' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/collection", true ),
				'formatter' => 'legacy',
			),
			'DBPerformance' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/dbperformance", true ),
				'formatter' => 'legacy',
			),
			'dnsblacklist' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/dnsblacklist", true ),
				'formatter' => 'legacy',
			),
			'Echo' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/echo", true ),
				'formatter' => 'legacy',
			),
			'es-hit' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/es-hit", true ),
				'formatter' => 'legacy',
			),
			'EventLogging' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/eventlogging", true ),
				'formatter' => 'legacy',
			),
			'exception' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/exception", true ),
				'formatter' => 'legacy',
			),
			'exception-json' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/exception-json", true ),
				'formatter' => 'legacy',
			),
			'exec' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/exec", true ),
				'formatter' => 'legacy',
			),
			'ExternalStoreDB' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/external", true ),
				'formatter' => 'legacy',
			),
			'FileOperation' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/filebackend-ops", true ),
				'formatter' => 'legacy',
			),
			'Flow' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/Flow", true ),
				'formatter' => 'legacy',
			),
			'FSFileBackend' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/fsfilebackend", true ),
				'formatter' => 'legacy',
			),
			'generated-pp-node-count' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/generated-pp-node-count", true ),
				'formatter' => 'legacy',
			),
			'GettingStarted' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/gettingstarted", true ),
				'formatter' => 'legacy',
			),
			'GlobalTitleFail' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/globaltitlefail", true ),
				'formatter' => 'legacy',
			),
			'jobqueue' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/jobqueue/web", true ),
				'formatter' => 'legacy',
			),
			'JobQueueRedis' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/redis-jobqueue", true ),
				'formatter' => 'legacy',
			),
			'lc-recache' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/lc-recache", true ),
				'formatter' => 'legacy',
			),
			'logging' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/logging", true ),
				'formatter' => 'legacy',
			),
			'MassMessage' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/MassMessage", true ),
				'formatter' => 'legacy',
			),
			'memcached-serious' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/memcached-serious", true ),
				'formatter' => 'legacy',
			),
			'mobile' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/mobile", true ),
				'formatter' => 'legacy',
			),
			'mwsearch' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/mwsearch", true ),
				'formatter' => 'legacy',
			),
			'oai' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/oai", true ),
				'formatter' => 'legacy',
			),
			'OutputBuffer' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/buffer", true ),
				'formatter' => 'legacy',
			),
			'poolcounter' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/poolcounter", true ),
				'formatter' => 'legacy',
			),
			'query' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/botquery", true ),
				'formatter' => 'legacy',
			),
			'ratelimit' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/limiter", true ),
				'formatter' => 'legacy',
			),
			'recursion-guard' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/recursion-guard", true ),
				'formatter' => 'legacy',
			),
			'redis' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/redis", true ),
				'formatter' => 'legacy',
			),
			'resourceloader' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/resourceloader", true ),
				'formatter' => 'legacy',
			),
			'runJobs' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/runJobs", true ),
				'formatter' => 'legacy',
			),
			'session' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/session", true ),
				'formatter' => 'legacy',
			),
			'SimpleAntiSpam' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/spam", true ),
				'formatter' => 'legacy',
			),
			'slow-parse' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/slow-parse", true ),
				'formatter' => 'legacy',
			),
			'SpamBlacklistHit' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/spam", true ),
				'formatter' => 'legacy',
			),
			'SpamRegex' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/spam", true ),
				'formatter' => 'legacy',
			),
			'SQLBagOStuff' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/sql-bagostuff", true ),
				'formatter' => 'legacy',
			),
			'squid' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/squid", true ),
				'formatter' => 'legacy',
			),
			'SwiftBackend' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/swift-backend", true ),
				'formatter' => 'legacy',
			),
			'temp-debug' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/temp-debug", true ),
				'formatter' => 'legacy',
			),
			'texvc' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/texvc", true ),
				'formatter' => 'legacy',
			),
			'thumbnail' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/thumbnail", true ),
				'formatter' => 'legacy',
			),
			'torblock' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/torblock", true ),
				'formatter' => 'legacy',
			),
			'ts_badpass' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/ts_badpass", true ),
				'formatter' => 'legacy',
			),
			'updateTranstagOnNullRevisions' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/updateTranstagOnNullRevisions", true ),
				'formatter' => 'legacy',
			),
			'upload' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/upload", true ),
				'formatter' => 'legacy',
			),
			'wikibase-debug' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/wikibase-debug", true ),
				'formatter' => 'legacy',
			),
			'Wikibase\UpdateRepoOnMoveJob' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/updateRepoOnMove", true ),
				'formatter' => 'legacy',
			),
			'zero' => array(
				'class' => 'MWLoggerMonologHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/zero", true ),
				'formatter' => 'legacy',
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
	) ),
);
