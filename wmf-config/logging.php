<?php
/**
 * Shared logging configuration
 *
 * Uses globals set by InitializeSettings:
 * - $wgDebugLogFile : udp2log destination for 'wgDebugLogFile' handler
 * - $wgWMFDefaultMonologHandler : default handler for log channels not
 *   explicitly configured in $wgWMFMonologChannels
 * - $wgWMFLogstashServers : Logstash syslog servers
 * - $wgWMFKafkaServers : Kafka logging servers
 * - $wgWMFMonologAvroSchemas: Map from monolog channel name to json
 *   string containing avro schema
 * - $wgWMFMonologChannels : per-channel logging config
 *   - channel => false  == ignore all log events on this channel
 *   - channel => level  == record all events of this level or higher to udp2log and logstash
 *   - channel => [ 'udp2log'=>level, 'logstash'=>level, 'kafka'=>level, 'sample'=>rate ]
 *   Defaults: [ 'udp2log'=>'debug', 'logstash'=>'info', 'kafka'=>false, 'sample'=>false ]
 *   Valid levels: 'debug', 'info', 'warning', 'error', false
 *   Note: sampled logs will not be sent to Logstash
 *   Note: Udp2log events are sent to udp://{$wgWMFUdp2logDest}/{$channel}
 * - $wgWMFUdp2logDest : udp2log host:port
 * - $wgWMFLogAuthmanagerMetrics : Controls additional authmanager logging
 */

use MediaWiki\Logger\LoggerFactory;

if ( getenv( 'MW_DEBUG_LOCAL' ) ) {
	// Route all log messages to a local file
	$wgDebugLogFile = '/tmp/wiki.log';
	$wgWMFDefaultMonologHandler = 'wgDebugLogFile';
	$wgWMFLogstashServers = false;
	$wgWMFMonologChannels = [];
	$wgDebugDumpSql = true;
} elseif ( isset( $_SERVER['HTTP_X_WIKIMEDIA_DEBUG'] ) &&
	preg_match( '/\blog\b/i', $_SERVER['HTTP_X_WIKIMEDIA_DEBUG'] )
) {
	// Forward all log messages to logstash for debugging.
	// See <https://wikitech.wikimedia.org/wiki/X-Wikimedia-Debug>.
	$wgDebugLogFile = "udp://{$wgWMFUdp2logDest}/XWikimediaDebug";
	$wgWMFDefaultMonologHandler = [ 'wgDebugLogFile' ];
	if ( $wgWMFLogstashServers ) {
		$wgWMFDefaultMonologHandler[] = 'logstash-debug';
	}
	$wgWMFMonologChannels = [];
}



// Monolog logging configuration

// T124985: The Processors listed in $wgWMFMonologProcessors are applied to
// a message in reverse list order (bottom to top). The normalized_message
// processor needs to be listed *after* the psr processor to work as expected.
$wgWMFMonologProcessors = [
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
			return function( array $record ) {
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
];

if ( $wgWMFRealm === 'labs' ) {
	$wgWMFMonologProcessors['shard'] = [
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
	];
}

$wgWMFMonologHandlers = [
	'blackhole' => [
		'class' => '\\Monolog\\Handler\\NullHandler',
	],
	'wgDebugLogFile' => [
		'class'     => '\\MediaWiki\\Logger\\Monolog\\LegacyHandler',
		'args'      => [ $wgDebugLogFile ],
		'formatter' => 'line',
	],
];

if ( $wgWMFLogstashServers ) {
	shuffle( $wgWMFLogstashServers );
	foreach ( [ 'debug', 'info', 'warning', 'error' ] as $logLevel ) {
		$wgWMFMonologHandlers[ "logstash-$logLevel" ] = [
			'class'     => '\\MediaWiki\\Logger\\Monolog\\SyslogHandler',
			'formatter' => 'logstash',
			'args'      => [
				'mediawiki',             // tag
				$wgWMFLogstashServers[0],  // host
				10514,                   // port
				LOG_USER,                // facility
				$logLevel,               // log level threshold
			],
		];
	}
}


// Post construction calls to make for new Logger instances
$wgWMFMonologLoggerCalls = [
	// T116550 - Requires Monolog > 1.17.2
	'useMicrosecondTimestamps' => [ false ],
];

$wgWMFMonologConfig =  [
	'loggers' => [
		// Template for all undefined log channels
		'@default' => [
			'handlers' => (array) $wgWMFDefaultMonologHandler,
			'processors' => array_keys( $wgWMFMonologProcessors ),
			'calls' => $wgWMFMonologLoggerCalls,
		],
	],

	'processors' => $wgWMFMonologProcessors,

	'handlers' => $wgWMFMonologHandlers,

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
			'args' => [ $wgWMFMonologAvroSchemas ],
		],
	],
];

if (
	$wgWMFLogAuthmanagerMetrics
	&& $wgWMFUseWikimediaEvents // T160490
) {
	// authmanager is the old name, but it has been repurposed
	// to be a more generic log channel; authevents is the new name
	$wgWMFMonologConfig['loggers']['authmanager'] = [
		'handlers' => [ 'authmanager-statsd' ],
		'calls' => $wgWMFMonologLoggerCalls,
	];
	$wgWMFMonologConfig['loggers']['authevents'] = [
		'handlers' => [ 'authmanager-statsd' ],
		'calls' => $wgWMFMonologLoggerCalls,
	];
	$wgWMFMonologConfig['handlers']['authmanager-statsd'] = [
		// defined in WikimediaEvents
		'class' => 'AuthManagerStatsdHandler',
	];
}

// Add logging channels defined in $wgWMFMonologChannels
foreach ( $wgWMFMonologChannels as $channel => $opts ) {
	if ( $opts === false ) {
		// Log channel disabled on this wiki
		$wgWMFMonologConfig['loggers'][$channel] = [
			'handlers' => [ 'blackhole' ],
			'calls' => $wgWMFMonologLoggerCalls,
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
		if ( !isset( $wgWMFMonologConfig['handlers'][$udp2logHandler] ) ) {
			// Register handler that will only pass events of the given
			// log level
			$wgWMFMonologConfig['handlers'][$udp2logHandler] = [
				'class' => '\\MediaWiki\\Logger\\Monolog\\LegacyHandler',
				'args' => [
					"udp://{$wgWMFUdp2logDest}/{channel}", false, $opts['udp2log']
				],
				'formatter' => 'line',
			];
		}
		$handlers[] = $udp2logHandler;
		if ( $wgWMFDefaultMonologHandler === 'wgDebugLogFile' ) {
			// T117019: Send events to default handler location as well
			$handlers[] = $wgWMFDefaultMonologHandler;
		}
	}

	// Configure kafka handler
	if ( $opts['kafka'] && $wgWMFKafkaServers ) {
		$kafkaHandler = "kafka-{$opts['kafka']}";
		if ( !isset( $wgWMFMonologConfig['handlers'][$kafkaHandler] ) ) {
			// Register handler that will only pass events of the given
			// log level
			$wgWMFMonologConfig['handlers'][$kafkaHandler] = [
				'factory' => '\\MediaWiki\\Logger\\Monolog\\KafkaHandler::factory',
				'args' => [
					$wgWMFKafkaServers,
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
		$wgWMFMonologConfig['handlers'][$kafkaHandler]['args'][1]['alias'][$channel] = "mediawiki_$channel";
		$handlers[] = $kafkaHandler;
	}

	// Configure Logstash handler
	if ( $opts['logstash'] && $wgWMFLogstashServers ) {
		$level = $opts['logstash'];
		$logstashHandler = "logstash-{$level}";
		if ( isset( $wgWMFMonologHandlers[ $logstashHandler ] ) ) {
			$handlers[] = $logstashHandler;
		}
	}


	if ( $opts['sample'] ) {
		$sample = $opts['sample'];
		foreach ( $handlers as $idx => $handlerName ) {
			$sampledHandler = "{$handlerName}-sampled-{$sample}";
			if ( !isset( $wgWMFMonologConfig['handlers'][$sampledHandler] ) ) {
				// Register a handler that will sample the event stream and
				// pass events on to $handlerName for storage
				$wgWMFMonologConfig['handlers'][$sampledHandler] = [
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
			if ( !isset( $wgWMFMonologConfig['handlers'][$bufferedHandler] ) ) {
				// Register a handler that will buffer the event stream and
				// pass events to the nested handler after closing the request
				$wgWMFMonologConfig['handlers'][$bufferedHandler] = [
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
		if ( !isset( $wgWMFMonologConfig['handlers'][$failureGroupHandler] ) ) {
			$wgWMFMonologConfig['handlers'][$failureGroupHandler] = [
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

		$wgWMFMonologConfig['loggers'][$channel] = [
			'handlers' => [ $failureGroupHandler ],
			'processors' => array_keys( $wgWMFMonologProcessors ),
			'calls' => $wgWMFMonologLoggerCalls,
		];

	} else {
		// No handlers configured, so use the blackhole route
		$wgWMFMonologConfig['loggers'][$channel] = [
			'handlers' => [ 'blackhole' ],
			'calls' => $wgWMFMonologLoggerCalls,
		];
	}
}

$wgMWLoggerDefaultSpi = [
	'class' => '\\MediaWiki\\Logger\\MonologSpi',
	'args' => [ $wgWMFMonologConfig ],
];

// Bug: T99581 - force logger timezone to UTC
// Guard condition needed for Jenkins; class from mediawiki/vendor
if ( method_exists( '\\Monolog\\Logger', 'setTimezone' ) ) {
	\Monolog\Logger::setTimezone( new DateTimeZone( 'UTC' ) );
}
