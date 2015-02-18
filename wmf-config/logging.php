<?php
/**
 * Shared logging configuration
 *
 * Uses globals set by InitializeSettings:
 * - $wgDebugLogFile : udp2log destination for 'wgDebugLogFile' handler
 * - $wmgDefaultMonologHandler : default handler for log channels not
 *   explicitly configured in $wmgMonologChannels
 * - $wmgLogstashServers : Logstash syslog servers
 * - $wmgMonologChannels : per-channel logging config
 *   - channel => false  == ignore all log events on this channel
 *   - channel => level  == record all events of this level or higher
 *   - channel => array( 'level'=>level, 'logstash'=>level, 'sample'=>rate )
 *   Defaults: array( 'level'=>'debug', 'logstash'=>'debug', 'sample'=>false )
 *   Valid levels: 'debug', 'info', 'warning', 'error'
 *   Note: sampled logs will not be sent to Logstash
 *   Note: Udp2log events are sent to udp://{$wmfUdp2logDest}/{$channel}
 * - $wmfUdp2logDest : udp2log host:port
 */

use MediaWiki\Logger\LoggerFactory;

if ( getenv( 'WIKIDEBUG' ) ) {
	// Route all log messages to a local file
	$wgDebugLogFile = '/tmp/wiki.log';
	$wmgDefaultMonologHandler = 'wgDebugLogFile';
	$wmgLogstashServers = false;
	$wmgMonologChannels = array();
	$wgDebugDumpSql = true;
}

// Monolog logging configuration
$wmgMonologConfig =  array(
	'loggers' => array(
		// Template for all undefined log channels
		'@default' => array(
			'handlers' => array( $wmgDefaultMonologHandler ),
			'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
		),
	),

	'processors' => array(
		'wiki' => array(
			'class' => '\\MediaWiki\\Logger\\Monolog\\WikiProcessor',
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
		'blackhole' => array(
			'class' => '\\Monolog\\Handler\\NullHandler',
		),
		'wgDebugLogFile' => array(
			'class' => '\\MediaWiki\\Logger\\Monolog\\LegacyHandler',
			'args' => array( $wgDebugLogFile ),
			'formatter' => 'line',
		),
	),

	'formatters' => array(
		'line' => array(
			'class' => '\\Monolog\\Formatter\\LineFormatter',
			'args' => array(
				"%datetime% %extra.host% %extra.wiki% %channel% %level_name%: %message% %context%\n",
				'Y-m-d H:i:s',
				true, // allowInlineLineBreaks
				true, // ignoreEmptyContextAndExtra
			),
		),
		'logstash' => array(
			'class' => '\\Monolog\\Formatter\\LogstashFormatter',
			'args'  => array( 'mediawiki', php_uname( 'n' ), null, '', 1 ),
		),
	),
);

// Add logging channels defined in $wmgMonologChannels
foreach ( $wmgMonologChannels as $channel => $opts ) {
	if ( $opts === false ) {
		// Log channel disabled on this wiki
		$wmgMonologConfig['loggers'][$channel] = array(
			'handlers' => 'blackhole',
		);
		continue;
	}

	$opts = is_array( $opts ) ? $opts : array( 'level' => $opts );
	$opts = array_merge(
		array(
			'level' => 'debug',
			'logstash' => isset( $opts['level'] ) ?  $opts['level'] : 'debug',
			'sample' => false,
		),
		$opts
	);

	$handlers = array();

	// Configure udp2log handler
	$udp2logHandler = "udp2log-{$opts['level']}";
	if ( !isset( $wmgMonologConfig['handlers'][$udp2logHandler] ) ) {
		// Register handler that will only pass events of the given
		// log level
		$wmgMonologConfig['handlers'][$udp2logHandler] = array(
			'class' => '\\MediaWiki\\Logger\\Monolog\\LegacyHandler',
			'args' => array(
				"udp://{$wmfUdp2logDest}/{channel}", false, $opts['level']
			),
			'formatter' => 'line',
		);
	}
	if ( $opts['sample'] ) {
		$sample = $opts['sample'];
		$sampledHandler = "{$udp2logHandler}-sampled-{$sample}";
		if ( !isset( $wmgMonologConfig['handlers'][$sampledHandler] ) ) {
			// Register a handler that will sample the event stream and
			// pass events on to $udp2logHandler for storage
			$wmgMonologConfig['handlers'][$sampledHandler] = array(
				'class' => '\\Monolog\\Handler\\SamplingHandler',
				'args' => array(
					function () use ( $udp2logHandler ) {
						return LoggerFactory::getProvider()->getHandler(
							$udp2logHandler
						);
					},
					$sample,
				),
			);
		}
		$handlers[] = $sampledHandler;
	} else {
		$handlers[] = $udp2logHandler;
	}

	// Configure Logstash handler
	// Note: sampled logs are never passed to logstash
	if ( $$opts['sample'] === false &&
		$opts['logstash'] &&
		$wmgLogstashServers
	) {
		$level = $opts['logstash']['level'];
		$logstashHandler = "logstash-{$level}";
		if ( !isset( $wmgMonologConfig['handlers'][$logstashHandler] ) ) {
			// Register handler that will only pass events of the given
			// log level
			$wmgMonologConfig['handlers'][$logstashHandler] = array(
				'class' => '\\MediaWiki\\Logger\\Monolog\\SyslogHandler',
				'args' => array(
					'mediawiki',    // syslog appname
					function () use ( $wmgLogstashServers ) {
						$idx = mt_rand( 0, count( $wmgLogstashServers ) - 1 );
						return $wmgLogstashServers[$idx];
					},              // randomly chose server
					10514,          // logstash syslog listener port
					LOG_USER,       // syslog facility
					$level,         // minimum log level to pass to logstash
				),
				'formatter' => 'logstash',
			);
		}
		$handlers[] = $logstashHandler;
	}

	$wmgMonologConfig['loggers'][$channel] = array(
		'handlers' => $handlers,
		'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
	);
}

$wgMWLoggerDefaultSpi = array(
	'class' => '\\MediaWiki\\Logger\\MonologSpi',
	'args' => array( $wmgMonologConfig ),
);
