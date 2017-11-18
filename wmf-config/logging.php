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
 *   - channel => [ 'udp2log'=>level, 'logstash'=>level, 'kafka'=>level, 'sample'=>rate ]
 *   Defaults: [ 'udp2log'=>'debug', 'logstash'=>'info', 'kafka'=>false, 'sample'=>false ]
 *   Valid levels: 'debug', 'info', 'warning', 'error', false
 *   Note: sampled logs will not be sent to Logstash
 *   Note: Udp2log events are sent to udp://{$wmgUdp2logDest}/{$channel}
 * - $wmgUdp2logDest : udp2log host:port
 * - $wmgLogAuthmanagerMetrics : Controls additional authmanager logging
 */

use MediaWiki\Logger\LoggerFactory;

if ( getenv( 'MW_DEBUG_LOCAL' ) ) {
	// Route all log messages to a local file
	$wgDebugLogFile = '/tmp/wiki.log';
	$wmgDefaultMonologHandler = 'wgDebugLogFile';
	$wmgLogstashServers = false;
	$wmgMonologChannels = [];
	$wgDebugDumpSql = true;
} elseif ( isset( $_SERVER['HTTP_X_WIKIMEDIA_DEBUG'] ) &&
	preg_match( '/\blog\b/i', $_SERVER['HTTP_X_WIKIMEDIA_DEBUG'] )
) {
	// Forward all log messages to logstash for debugging.
	// See <https://wikitech.wikimedia.org/wiki/X-Wikimedia-Debug>.
	$wgDebugLogFile = "udp://{$wmgUdp2logDest}/XWikimediaDebug";
	$wmgDefaultMonologHandler = [ 'wgDebugLogFile' ];
	if ( $wmgLogstashServers ) {
		$wmgDefaultMonologHandler[] = 'logstash-debug';
	}
	$wmgMonologChannels = [];
}

// Monolog logging configuration

// T124985: The Processors listed in $wmgMonologProcessors are applied to
// a message in reverse list order (bottom to top). The normalized_message
// processor needs to be listed *after* the psr processor to work as expected.
$wmgMonologProcessors = [
	'wiki' => [
		'class' => '\\MediaWiki\\Logger\\Monolog\\WikiProcessor',
	],
	'psr' => [
		'class' => '\\Monolog\\Processor\\PsrLogMessageProcessor',
	],
	'web' => [
		'class' => '\\Monolog\\Processor\\WebProcessor',
	],
	'normalized_message' => [
		'factory' => function () {
			/**
			 * Add a "normalized_message" field to log records.
			 *
			 * Field is the first 255 chars of 'message' after stripping out
			 * anchor tags which are sometimes inserted in error and warning
			 * messages by PHP. If applied before PsrLogMessageProcessor,
			 * placeholders in the message will be left unexpanded which can
			 * reduce variance of messages that expand to include per-request
			 * details such as session ids.
			 */
			return function ( array $record ) {
				$nm = $record['message'];
				if ( strpos( $nm, '<a href=' ) !== false ) {
					// Remove documentation anchor tags
					$nm = preg_replace(
						"|<a href='[^']*'>[^<]*</a>|",
						'',
						$nm
					);
				}
				// Trim to <= 255 chars
				$record['extra']['normalized_message'] = substr( $nm, 0, 255 );
				return $record;
			};
		}
	],
	'shard' => [
		'factory' => function () {
			/** Adds the database shard name (e.g. s1, s2, ...) */
			return function ( array $record ) {
				global $wgLBFactoryConf, $wgDBname;

				$record['extra']['shard'] = isset( $wgLBFactoryConf['sectionsByDB'][$wgDBname] )
					? $wgLBFactoryConf['sectionsByDB'][$wgDBname]
					: 's3';
				return $record;
			};
		}
	]
];

$wmgMonologHandlers = [
	'blackhole' => [
		'class' => '\\Monolog\\Handler\\NullHandler',
	],
	'wgDebugLogFile' => [
		'class'     => '\\MediaWiki\\Logger\\Monolog\\LegacyHandler',
		'args'      => [ $wgDebugLogFile ],
		'formatter' => 'line',
	],
];

if ( $wmgLogstashServers ) {
	shuffle( $wmgLogstashServers );
	foreach ( [ 'debug', 'info', 'warning', 'error' ] as $logLevel ) {
		$wmgMonologHandlers[ "logstash-$logLevel" ] = [
			'class'     => '\\MediaWiki\\Logger\\Monolog\\SyslogHandler',
			'formatter' => 'logstash',
			'args'      => [
				'mediawiki',             // tag
				$wmgLogstashServers[0],  // host
				10514,                   // port
				LOG_USER,                // facility
				$logLevel,               // log level threshold
			],
		];
	}
}

// Post construction calls to make for new Logger instances
$wmgMonologLoggerCalls = [
	// T116550 - Requires Monolog > 1.17.2
	'useMicrosecondTimestamps' => [ false ],
];

$wmgMonologConfig = [
	'loggers' => [
		// Template for all undefined log channels
		'@default' => [
			'handlers' => (array)$wmgDefaultMonologHandler,
			'processors' => array_keys( $wmgMonologProcessors ),
			'calls' => $wmgMonologLoggerCalls,
		],
	],

	'processors' => $wmgMonologProcessors,

	'handlers' => $wmgMonologHandlers,

	'formatters' => [
		'line' => [
			'class' => '\\MediaWiki\\Logger\\Monolog\\LineFormatter',
			'args' => [
				"%datetime% [%extra.reqId%] %extra.host% %extra.wiki% %extra.mwversion% %channel% %level_name%: %message% %context% %exception%\n",
				'Y-m-d H:i:s',
				true, // allowInlineLineBreaks
				true, // ignoreEmptyContextAndExtra
				true, // includeStacktraces
			],
		],
		'logstash' => [
			'class' => '\\MediaWiki\\Logger\\Monolog\\LogstashFormatter',
			'args'  => [ 'mediawiki', php_uname( 'n' ), null, '', 1 ],
		],
		'avro' => [
			'class' => '\\MediaWiki\\Logger\\Monolog\\AvroFormatter',
			'args' => [ $wmgMonologAvroSchemas ],
		],
	],
];

if (
	$wmgLogAuthmanagerMetrics
	&& $wmgUseWikimediaEvents // T160490
) {
	// authmanager is the old name, but it has been repurposed
	// to be a more generic log channel; authevents is the new name
	$wmgMonologConfig['loggers']['authmanager'] = [
		'handlers' => [ 'authmanager-statsd' ],
		'calls' => $wmgMonologLoggerCalls,
	];
	$wmgMonologConfig['loggers']['authevents'] = [
		'handlers' => [ 'authmanager-statsd' ],
		'calls' => $wmgMonologLoggerCalls,
	];
	$wmgMonologConfig['handlers']['authmanager-statsd'] = [
		// defined in WikimediaEvents
		'class' => 'AuthManagerStatsdHandler',
	];
}

// Add logging channels defined in $wmgMonologChannels
foreach ( $wmgMonologChannels as $channel => $opts ) {
	if ( $opts === false ) {
		// Log channel disabled on this wiki
		$wmgMonologConfig['loggers'][$channel] = [
			'handlers' => [ 'blackhole' ],
			'calls' => $wmgMonologLoggerCalls,
		];
		continue;
	}

	$opts = is_array( $opts ) ? $opts : [ 'udp2log' => $opts ];
	$opts = array_merge(
		[
			'udp2log' => 'debug',
			'logstash' => ( isset( $opts['udp2log'] ) && $opts['udp2log'] !== 'debug' ) ? $opts['udp2log'] : 'info',
			'kafka' => false,
			'sample' => false,
			'buffer' => false,
		],
		$opts
	);
	// Note: sampled logs are never passed to logstash
	if ( $opts['sample'] !== false ) {
		$opts['logstash'] = false;
	}

	$handlers = [];

	if ( $opts['udp2log'] ) {
		// Configure udp2log handler
		$udp2logHandler = "udp2log-{$opts['udp2log']}";
		if ( !isset( $wmgMonologConfig['handlers'][$udp2logHandler] ) ) {
			// Register handler that will only pass events of the given
			// log level
			$wmgMonologConfig['handlers'][$udp2logHandler] = [
				'class' => '\\MediaWiki\\Logger\\Monolog\\LegacyHandler',
				'args' => [
					"udp://{$wmgUdp2logDest}/{channel}", false, $opts['udp2log']
				],
				'formatter' => 'line',
			];
		}
		$handlers[] = $udp2logHandler;
		if ( $wmgDefaultMonologHandler === 'wgDebugLogFile' ) {
			// T117019: Send events to default handler location as well
			$handlers[] = $wmgDefaultMonologHandler;
		}
	}

	// Configure kafka handler
	if ( $opts['kafka'] && $wmgKafkaServers ) {
		$kafkaHandler = "kafka-{$opts['kafka']}";
		if ( !isset( $wmgMonologConfig['handlers'][$kafkaHandler] ) ) {
			// Register handler that will only pass events of the given
			// log level
			$wmgMonologConfig['handlers'][$kafkaHandler] = [
				'factory' => '\\MediaWiki\\Logger\\Monolog\\KafkaHandler::factory',
				'args' => [
					$wmgKafkaServers,
					[
						'alias' => [],
						'swallowExceptions' => true,
						'logExceptions' => 'wfDebugLogFile',
						'sendTimeout' => 0.01,
						'recvTimeout' => 0.5,
						'requireAck' => 1,
					],
				],
				'formatter' => 'avro'
			];
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
		if ( isset( $wmgMonologHandlers[ $logstashHandler ] ) ) {
			$handlers[] = $logstashHandler;
		}
	}

	if ( $opts['sample'] ) {
		$sample = $opts['sample'];
		foreach ( $handlers as $idx => $handlerName ) {
			$sampledHandler = "{$handlerName}-sampled-{$sample}";
			if ( !isset( $wmgMonologConfig['handlers'][$sampledHandler] ) ) {
				// Register a handler that will sample the event stream and
				// pass events on to $handlerName for storage
				$wmgMonologConfig['handlers'][$sampledHandler] = [
					'class' => '\\Monolog\\Handler\\SamplingHandler',
					'args' => [
						function () use ( $handlerName ) {
							return LoggerFactory::getProvider()->getHandler(
								$handlerName
							);
						},
						$sample,
					],
				];
			}
			$handlers[$idx] = $sampledHandler;
		}
	}

	if ( $opts['buffer'] ) {
		foreach ( $handlers as $idx => $handlerName ) {
			$bufferedHandler = "{$handlerName}-buffered";
			if ( !isset( $wmgMonologConfig['handlers'][$bufferedHandler] ) ) {
				// Register a handler that will buffer the event stream and
				// pass events to the nested handler after closing the request
				$wmgMonologConfig['handlers'][$bufferedHandler] = [
					'class' => '\\MediaWiki\\Logger\\Monolog\\BufferHandler',
					'args' => [
						function () use ( $handlerName ) {
							return LoggerFactory::getProvider()->getHandler(
								$handlerName
							);
						},
					],
				];
			}
			$handlers[$idx] = $bufferedHandler;
		}
	}

	if ( $handlers ) {
		// T118057: wrap the collection of handlers in a WhatFailureGroupHandler
		// to swallow any exceptions that might leak out otherwise
		$failureGroupHandler = 'failuregroup|' . implode( '|', $handlers );
		if ( !isset( $wmgMonologConfig['handlers'][$failureGroupHandler] ) ) {
			$wmgMonologConfig['handlers'][$failureGroupHandler] = [
				'class' => '\\Monolog\\Handler\\WhatFailureGroupHandler',
				'args' => [
					function () use ( $handlers ) {
						$provider = LoggerFactory::getProvider();
						return array_map(
							[ $provider, 'getHandler' ],
							$handlers
						);
					}
				],
			];
		}

		$wmgMonologConfig['loggers'][$channel] = [
			'handlers' => [ $failureGroupHandler ],
			'processors' => array_keys( $wmgMonologProcessors ),
			'calls' => $wmgMonologLoggerCalls,
		];

	} else {
		// No handlers configured, so use the blackhole route
		$wmgMonologConfig['loggers'][$channel] = [
			'handlers' => [ 'blackhole' ],
			'calls' => $wmgMonologLoggerCalls,
		];
	}
}

$wgMWLoggerDefaultSpi = [
	'class' => '\\MediaWiki\\Logger\\MonologSpi',
	'args' => [ $wmgMonologConfig ],
];

// Bug: T99581 - force logger timezone to UTC
// Guard condition needed for Jenkins; class from mediawiki/vendor
if ( method_exists( '\\Monolog\\Logger', 'setTimezone' ) ) {
	\Monolog\Logger::setTimezone( new DateTimeZone( 'UTC' ) );
}
