<?php
/**
 * Shared logging configuration
 *
 * Uses globals set by InitializeSettings:
 * - $wgDebugLogFile : udp2log destination for 'wgDebugLogFile' handler
 * - $wmgDefaultMonologHandler : default handler for log channels not
 *   explicitly configured in $wmgMonologChannels
 * - $wmgLogstashServers : Logstash syslog servers
 * - $wmgKafkaServers : Kafka logging servers
 * - $wmgMonologAvroSchemas: Map from monolog channel name to json
 *   string containing avro schema
 * - $wmgMonologChannels : per-channel logging config
 *   - channel => false  == ignore all log events on this channel
 *   - channel => level  == record all events of this level or higher to udp2log and logstash
 *   - channel => array( 'udp2log'=>level, 'logstash'=>level, 'kafka'=>level, 'sample'=>rate )
 *   Defaults: array( 'udp2log'=>'debug', 'logstash'=>'info', 'kafka'=>false, 'sample'=>false )
 *   Valid levels: 'debug', 'info', 'warning', 'error', false
 *   Note: sampled logs will not be sent to Logstash
 *   Note: Udp2log events are sent to udp://{$wmfUdp2logDest}/{$channel}
 * - $wmfUdp2logDest : udp2log host:port
 * - $wmgLogAuthmanagerMetrics : Controls additional authmanager logging
 */

use MediaWiki\Logger\LoggerFactory;

if ( getenv( 'MW_DEBUG_LOCAL' ) ) {
	// Route all log messages to a local file
	$wgDebugLogFile = '/tmp/wiki.log';
	$wmgDefaultMonologHandler = 'wgDebugLogFile';
	$wmgLogstashServers = false;
	$wmgMonologChannels = array();
	$wgDebugDumpSql = true;
}

// Monolog logging configuration
$wmgMonologProcessors = array(
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
);

$wmgMonologConfig =  array(
	'loggers' => array(
		// Template for all undefined log channels
		'@default' => array(
			'handlers' => array( $wmgDefaultMonologHandler ),
			'processors' => array_keys( $wmgMonologProcessors ),
		),
	),

	'processors' => $wmgMonologProcessors,

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
			'class' => '\\MediaWiki\\Logger\\Monolog\\LineFormatter',
			'args' => array(
				"%datetime% %extra.host% %extra.wiki% %channel% %level_name%: %message% %context% %exception%\n",
				'Y-m-d H:i:s',
				true, // allowInlineLineBreaks
				true, // ignoreEmptyContextAndExtra
				true, // includeStacktraces
			),
		),
		'logstash' => array(
			'class' => '\\Monolog\\Formatter\\LogstashFormatter',
			'args'  => array( 'mediawiki', php_uname( 'n' ), null, '', 1 ),
		),
		'avro' => array(
			'class' => '\\MediaWiki\\Logger\\Monolog\\AvroFormatter',
			'args' => array( $wmgMonologAvroSchemas ),
		),
	),
);

if ( $wmgLogAuthmanagerMetrics ) {
	$wmgMonologConfig['loggers']['authmanager'] = array(
		'handlers' => array( 'authmanager-statsd' ),
	);
	$wmgMonologConfig['handlers']['authmanager-statsd'] = array(
		// defined in WikimediaEvents
		'class' => 'AuthManagerStatsdHandler',
	);
}

// Add logging channels defined in $wmgMonologChannels
foreach ( $wmgMonologChannels as $channel => $opts ) {
	if ( $opts === false ) {
		// Log channel disabled on this wiki
		$wmgMonologConfig['loggers'][$channel] = array(
			'handlers' => 'blackhole',
		);
		continue;
	}

	$opts = is_array( $opts ) ? $opts : array( 'udp2log' => $opts );
	$opts = array_merge(
		array(
			'udp2log' => 'debug',
			'logstash' => ( isset( $opts['udp2log'] ) && $opts['udp2log'] !== 'debug' ) ? $opts['udp2log'] : 'info',
			'kafka' => false,
			'sample' => false,
			'buffer' => false,
		),
		$opts
	);
	// Note: sampled logs are never passed to logstash
	if ( $opts['sample'] !== false ) {
		$opts['logstash'] = false;
	}

	$handlers = array();

	if ( $opts['udp2log'] ) {
		// Configure udp2log handler
		$udp2logHandler = "udp2log-{$opts['udp2log']}";
		if ( !isset( $wmgMonologConfig['handlers'][$udp2logHandler] ) ) {
			// Register handler that will only pass events of the given
			// log level
			$wmgMonologConfig['handlers'][$udp2logHandler] = array(
				'class' => '\\MediaWiki\\Logger\\Monolog\\LegacyHandler',
				'args' => array(
					"udp://{$wmfUdp2logDest}/{channel}", false, $opts['udp2log']
				),
				'formatter' => 'line',
			);
		}
		$handlers[] = $udp2logHandler;
	}

	// Configure kafka handler
	if ( $opts['kafka'] && $wmgKafkaServers ) {
		$kafkaHandler = "kafka-{$opts['kafka']}";
		if ( !isset( $wmgMonologConfig['handlers'][$kafkaHandler] ) ) {
			// Register handler that will only pass events of the given
			// log level
			$wmgMonologConfig['handlers'][$kafkaHandler] = array(
				'factory' => '\\MediaWiki\\Logger\\Monolog\\KafkaHandler::factory',
				'args' => array(
					$wmgKafkaServers,
					array(
						'alias' => array(),
						'swallowExceptions' => true,
						'logExceptions' => 'wfDebugLogFile',
					)
				)
			);
		}
		// include an alias to prefix this channel with
		// 'mediawiki_' in kafka
		$wmgMonologConfig['handlers'][$kafkaHandler]['args'][1]['alias'][$channel] = "mediawiki_$channel";
		$handlers[] = $kafkaHandler;
	}

	// Configure Logstash handler
	if ( $opts['logstash'] && $wmgLogstashServers ) {
		$level = $opts['logstash'];
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


	if ( $opts['sample'] ) {
		$sample = $opts['sample'];
		foreach ( $handlers as $idx => $handlerName ) {
			$sampledHandler = "{$handlerName}-sampled-{$sample}";
			if ( !isset( $wmgMonologConfig['handlers'][$sampledHandler] ) ) {
				// Register a handler that will sample the event stream and
				// pass events on to $udp2logHandler for storage
				$wmgMonologConfig['handlers'][$sampledHandler] = array(
					'class' => '\\Monolog\\Handler\\SamplingHandler',
					'args' => array(
						function () use ( $handlerName ) {
							return LoggerFactory::getProvider()->getHandler(
								$handlerName
							);
						},
						$sample,
					),
				);
			}
			$handlers[$idx] = $sampledHandler;
		}
	}
	if ( $opts['buffer'] ) {
		foreach ( $handlers as $idx => $handlerName ) {
			$bufferedHandler = "$handlerName-buffered";
			if ( !isset( $wmgMonologConfig['handlers'][$bufferedHandler] ) ) {
				// Register a handler that will buffer the event stream and
				// pass events to the nested handler after closing the request
				$wmgMonologConfig['handlers'][$bufferedHandler] = array(
					'class' => '\\MediaWiki\\Logger\\Monolog\\BufferHandler',
					'args' => array(
						function () use ( $handlerName ) {
							return LoggerFactory::getProvider()->getHandler(
								$handlerName
							);
						},
					),
				);
			}
			$handlers[$idx] = $bufferedHandler;
		}
	}

	$wmgMonologConfig['loggers'][$channel] = array(
		'handlers' => $handlers,
		'processors' => array_keys( $wmgMonologProcessors ),
	);
}

$wgMWLoggerDefaultSpi = array(
	'class' => '\\MediaWiki\\Logger\\MonologSpi',
	'args' => array( $wmgMonologConfig ),
);

// Bug: T99581 - force logger timezone to UTC; requires Monolog >= 1.14.0
if ( method_exists( '\\Monolog\\Logger', 'setTimezone' ) ) {
	\Monolog\Logger::setTimezone( new DateTimeZone( 'UTC' ) );
}
