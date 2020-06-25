<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

# logging.php holds the shared logging configuration.
#
# This for PRODUCTION.
#
# Effective load order:
# - multiversion
# - mediawiki/DefaultSettings.php
# - wmf-config/*Services.php
# - wmf-config/etcd.php
# - wmf-config/InitialiseSettings.php
# - wmf-config/logging.php [THIS FILE]
#
# Included from: wmf-config/CommonSettings.php.
#

#
# The following globals from InitialiseSettings are used:
#
# - $wgDebugLogFile: udp2log destination for 'wgDebugLogFile' handler.
# - $wmgDefaultMonologHandler: default handler for log channels not
#   explicitly configured in $wmgMonologChannels.
# - $wmgLogstashServers: Logstash syslog servers.
# - $wmgMonologChannels: per-channel logging config
#   - `channel => false`: ignore all log events on this channel.
#   - `channel => level`: record all events of this level or higher to udp2log and logstash.
#     Special case: `channel => debug` will not log to logstash.
#   - `channel => [
#           'udp2log'=>level,
#           'logstash'=>level,
#           'eventbus'=>level,
#           'sample'=>rate,
#           'buffer'=>buffer
#     ]`
# - $wmgUseEventBus: Whether EventBus extension is enabled on the wiki
#
#   Default for all channels for fields not otherwise specified:
#   ```
#   [
#       'udp2log' = >'debug',
#       'logstash' = >'info',
#       'eventbus' => false,
#       'sample' => false,
#       'buffer' => false,
#   ]
#   ```
#
#   Valid levels: 'debug', 'info', 'warning', 'error', false.
#
#   Note: Sampled logs will not be sent to Logstash!
#
#   Note: Udp2log events are sent to udp://{$wmfUdp2logDest}/{$channel}.
#   Ultimately they end up in logfiles on mwlog1001.
#
# - $wmfUdp2logDest: udp2log host and port.
# - $wmgLogAuthmanagerMetrics: Controls additional authmanager logging.
#

use MediaWiki\Logger\LoggerFactory;
use Wikimedia\MWConfig\XWikimediaDebug;

if ( getenv( 'MW_DEBUG_LOCAL' ) ) {
	// Route all log messages to a local file
	$wgDebugLogFile = '/tmp/wiki.log';
	$wmgDefaultMonologHandler = 'wgDebugLogFile';
	$wmgLogstashServers = false;
	$wmgMonologChannels = [];
	$wgDebugDumpSql = true;
} elseif ( XWikimediaDebug::getInstance()->hasOption( 'log' ) ) {
	// Forward all log messages to logstash for debugging.
	// See <https://wikitech.wikimedia.org/wiki/X-Wikimedia-Debug>.
	$wgDebugLogFile = "udp://{$wmfUdp2logDest}/XWikimediaDebug";
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
		'class' => \MediaWiki\Logger\Monolog\WikiProcessor::class,
	],
	'psr' => [
		'class' => \Monolog\Processor\PsrLogMessageProcessor::class,
	],
	'wmfconfig' => [
		'factory' => function () {
			return function ( array $record ) {
				// T253677: Like Monolog\Processor\WebProcessor, but without unique_id
				// Ref <https://github.com/Seldaek/monolog/issues/1470>.
				// Ref <https://github.com/Seldaek/monolog/blob/1.5.0/src/Monolog/Processor/WebProcessor.php>
				if ( isset( $_SERVER['REQUEST_URI'] ) ) {
					$record['extra'] += [
						'url' => $_SERVER['REQUEST_URI'] ?? null,
						'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
						'http_method' => $_SERVER['REQUEST_METHOD'] ?? null,
						'server' => $_SERVER['SERVER_NAME'] ?? null,
						'referrer' => $_SERVER['HTTP_REFERER'] ?? null,
					];
				}

				// T215350: add PHP version information
				$record['extra']['phpversion'] = phpversion();

				// T255627: add label for the server group the current server belongs to.
				// This is exposed by Apache configuration. Its value is that of the Hiera
				// 'cluster' key in Puppet (e.g. "appserver", "parsoid", etc).
				//
				// This is not set on CLI (e.g. deploy, maint, snapshot).
				//
				// Ref <https://wikitech.wikimedia.org/wiki/MediaWiki_at_WMF#App_servers>
				$record['extra']['servergroup'] = $_SERVER['SERVERGROUP'] ?? 'other';

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

				// Adds the database shard name (e.g. s1, s2, ...)
				global $wgLBFactoryConf, $wgDBname;
				$record['extra']['shard'] = $wgLBFactoryConf['sectionsByDB'][$wgDBname] ?? 's3';

				return $record;
			};
		},
	],
];

$wmgMonologHandlers = [
	'blackhole' => [
		'class' => \Monolog\Handler\NullHandler::class,
	],
	'wgDebugLogFile' => [
		'class'     => \MediaWiki\Logger\Monolog\LegacyHandler::class,
		'args'      => [ $wgDebugLogFile ],
		'formatter' => 'line',
	],
];

if ( $wmgLogstashServers ) {
	shuffle( $wmgLogstashServers );
	foreach ( [ 'debug', 'info', 'warning', 'error' ] as $logLevel ) {
		$wmgMonologHandlers[ "logstash-$logLevel" ] = [
			'class'     => \MediaWiki\Logger\Monolog\SyslogHandler::class,
			'formatter' => 'cee',
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
			'class' => \MediaWiki\Logger\Monolog\LineFormatter::class,
			'args' => [
				"%datetime% [%extra.reqId%] %extra.host% %extra.wiki% %extra.mwversion% %channel% %level_name%: %message% %context% %exception%\n",
				'Y-m-d H:i:s',
				true, // allowInlineLineBreaks
				true, // ignoreEmptyContextAndExtra
				true, // includeStacktraces
			],
		],
		'cee' => [
			'class' => \MediaWiki\Logger\Monolog\CeeFormatter::class,
			'args'  => [ 'mediawiki', php_uname( 'n' ), null, '', 1 ],
		],
	],
];

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
			'eventbus' => false,
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
				'class' => \MediaWiki\Logger\Monolog\LegacyHandler::class,
				'args' => [
					"udp://{$wmfUdp2logDest}/{channel}", false, $opts['udp2log']
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

	if ( $opts['eventbus'] && $wmgUseEventBus ) {
		$eventBusHandler = "eventbus-{$opts['eventbus']}";
		if ( !isset( $wmgMonologConfig['handlers'][$eventBusHandler] ) ) {
			// Register handler that will only pass events of the given log level
			$wmgMonologConfig['handlers'][$eventBusHandler] = [
				'class' => \MediaWiki\Extension\EventBus\Adapters\Monolog\EventBusMonologHandler::class,
				'args' => [
					'eventgate-analytics' // EventServiceName
				]
			];
		}
		$handlers[] = $eventBusHandler;
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
					'class' => \Monolog\Handler\SamplingHandler::class,
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
					'class' => \MediaWiki\Logger\Monolog\BufferHandler::class,
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
				'class' => \Monolog\Handler\WhatFailureGroupHandler::class,
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

if (
	$wmgLogAuthmanagerMetrics
	&& $wmgUseWikimediaEvents // T160490
) {
	$wmgMonologConfig['loggers']['authevents']['handlers'][] = 'authmanager-statsd';
	$wmgMonologConfig['loggers']['authevents']['calls'] = $wmgMonologLoggerCalls;
	$wmgMonologConfig['loggers']['captcha']['handlers'][] = 'authmanager-statsd';
	$wmgMonologConfig['loggers']['captcha']['calls'] = $wmgMonologLoggerCalls;
	$wmgMonologConfig['handlers']['authmanager-statsd'] = [
		// defined in WikimediaEvents
		'class' => WikimediaEvents\AuthManagerStatsdHandler::class,
	];
}

$wgMWLoggerDefaultSpi = [
	'class' => \MediaWiki\Logger\MonologSpi::class,
	'args' => [ $wmgMonologConfig ],
];

// Bug: T99581 - force logger timezone to UTC
// Guard condition needed for Jenkins; class from mediawiki/vendor
if ( method_exists( \Monolog\Logger::class, 'setTimezone' ) ) {
	\Monolog\Logger::setTimezone( new DateTimeZone( 'UTC' ) );
}
