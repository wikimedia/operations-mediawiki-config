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
# - $wmgDefaultMonologHandlers: default handlers for log channels not
#   explicitly configured in $wmgMonologChannels.
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
#   Note: Udp2log events are sent to udp://{$wmgUdp2logDest}/{$channel}.
#   Ultimately they end up in logfiles on mwlog1001.
#
# - $wmgUdp2logDest: udp2log host and port.
# - $wmgLogAuthmanagerMetrics: Controls additional authmanager logging.
#

use MediaWiki\Logger\LoggerFactory;
use Wikimedia\MWConfig\XWikimediaDebug;

// Logstash servers running syslog endpoint to collect log events.
// Use false to disable all Logstash logging
$wmgEnableLogstash = true;

if ( getenv( 'MW_DEBUG_LOCAL' ) ) {
	// Route all log messages to a local file
	$wgDebugLogFile = '/tmp/wiki.log';
	$wmgDefaultMonologHandlers = 'wgDebugLogFile';
	$wmgEnableLogstash = false;
	$wmgMonologChannels = [];
	$wgDebugDumpSql = true;
} elseif ( XWikimediaDebug::getInstance()->hasOption( 'log' ) ) {
	// Forward all log messages to logstash for debugging.
	// See <https://wikitech.wikimedia.org/wiki/X-Wikimedia-Debug>.
	$wgDebugLogFile = "udp://{$wmgUdp2logDest}/XWikimediaDebug";
	$wmgDefaultMonologHandlers = [ 'wgDebugLogFile', 'logstash-debug' ];
	$wmgMonologChannels = [];
	$wgDebugDumpSql = true;
}

// Monolog logging configuration

// T124985: The Processors listed in $wmgMonologProcessors are applied to
// a message list order (top to bottom) since 1.41.0-wmf.30 (19b97fd575).
//
// The `wmfconfig` processor injects `normalized_message` which must be listed
// *before* the psr processor since we want to retain the log placeholders for
// log deduplication (T349086).
$wmgMonologProcessors = [
	// Injects wiki, MediaWiki version, request id etc
	'wiki' => [
		'class' => \MediaWiki\Logger\Monolog\WikiProcessor::class,
	],
	// Additional fields injected before processing eg placeholders in log
	// messages.
	'wmfconfig' => [
		'factory' => static function () {
			return static function ( array $record ) {
				// Like Monolog\Processor\WebProcessor, but without 'unique_id' (per T253677).
				// And without 'ip' (per T114700).
				//
				// Ref <https://github.com/Seldaek/monolog/issues/1470>
				// Ref <https://github.com/Seldaek/monolog/blob/1.5.0/src/Monolog/Processor/WebProcessor.php>
				if ( isset( $_SERVER['REQUEST_URI'] ) ) {
					$record['extra'] += [
						'url' => $_SERVER['REQUEST_URI'] ?? null,
						'http_method' => $_SERVER['REQUEST_METHOD'] ?? null,
						'server' => $_SERVER['SERVER_NAME'] ?? null,
						'referrer' => $_SERVER['HTTP_REFERER'] ?? null,
					];
				}

				// T215350: add PHP version information
				$record['extra']['phpversion'] = phpversion();

				// T255627: add label for the server group the current server belongs to.
				// This is exposed by Apache configuration defined in Puppet profile::mediawiki::httpd.
				// Its value is that of the Hiera 'cluster' key in Puppet (e.g. "appserver", "parsoid", etc).
				// For pods running on Kubernetes, the servergroup is determined by the value of
				// the php.servergroup Helm setting and will be prefixed by "kube-" (e.g.
				// operations/deployment-charts:helmfile.d/services/mwdebug/values.yaml).
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
				 *
				 * We rely on the placeholders not being expanded to deduplicate
				 * log messages (T349086).
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
	'psr' => [
		'class' => \Monolog\Processor\PsrLogMessageProcessor::class,
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

$wmgSupportedLogLevels = [ 'debug', 'info', 'warning', 'error' ];

foreach ( $wmgSupportedLogLevels as $logLevel ) {
	$wmgMonologHandlers[ "udp2log-$logLevel" ] = [
		'class' => \MediaWiki\Logger\Monolog\LegacyHandler::class,
		'args' => [
			"udp://{$wmgUdp2logDest}/{channel}",
			false,
			$logLevel
		],
		'formatter' => 'line',
	];
}

if ( $wmgEnableLogstash ) {
	foreach ( $wmgSupportedLogLevels as $logLevel ) {
		$wmgMonologHandlers[ "logstash-$logLevel" ] = [
			'class'     => \MediaWiki\Logger\Monolog\SyslogHandler::class,
			'formatter' => 'cee',
			'args'      => [
				// tag
				'mediawiki',
				// host
				'127.0.0.1',
				// port
				10514,
				// facility
				LOG_USER,
				// log level threshold
				$logLevel,
			],
		];
	}
}

$wmgMonologConfig = [
	'loggers' => [
		// Template for all undefined log channels
		'@default' => [
			'handlers' => (array)$wmgDefaultMonologHandlers,
			'processors' => array_keys( $wmgMonologProcessors ),
		],
	],

	'processors' => $wmgMonologProcessors,

	'handlers' => $wmgMonologHandlers,

	'formatters' => [
		'line' => [
			'class' => \MediaWiki\Logger\Monolog\LineFormatter::class,
			'args' => [
				"%datetime% [%extra.reqId%] %extra.host% %extra.wiki% %extra.mwversion% %channel% %level_name%: %message% %context% %exception%\n",
				'Y-m-d H:i:s.u',
				// allowInlineLineBreaks
				true,
				// ignoreEmptyContextAndExtra
				true,
				// includeStacktraces
				true,
			],
		],
		'cee' => [
			'class' => \MediaWiki\Logger\Monolog\CeeFormatter::class,
			'args'  => [ 'mediawiki', php_uname( 'n' ), '', '', 1 ],
		],
	],
];

// Add logging channels defined in $wmgMonologChannels
foreach ( $wmgMonologChannels as $channel => $opts ) {
	if ( $opts === false ) {
		// Log channel disabled on this wiki
		$wmgMonologConfig['loggers'][$channel] = [
			'handlers' => [ 'blackhole' ],
		];
		continue;
	}

	$opts = is_array( $opts ) ? $opts : [ 'udp2log' => $opts ];

	// Defaults
	$opts += [
		'udp2log' => 'debug',
		'eventbus' => false,
		'sample' => false,
		'buffer' => false,
	];
	$opts += [
		'logstash' => ( $opts['udp2log'] !== 'debug' ) ? $opts['udp2log'] : 'info',
	];

	// Sampled logs are never passed to logstash
	if ( $opts['sample'] !== false ) {
		$opts['logstash'] = false;
	}

	$handlers = [];

	if ( $opts['udp2log'] ) {
		$handlers[] = "udp2log-{$opts['udp2log']}";
		if ( $wmgDefaultMonologHandlers === 'wgDebugLogFile' ) {
			// T117019: Send messages to default handler location as well
			// This is for messages from regular traffic to testwikis (WikimediaDebug is off).
			// When WikimediaDebug is used, $wmgMonologChannels is cleared and this code
			// is never reached.
			$handlers[] = $wmgDefaultMonologHandlers;
		}
	}

	if ( $opts['eventbus'] && $wmgUseEventBus ) {
		$eventBusHandler = "eventbus-{$opts['eventbus']}";
		if ( !isset( $wmgMonologConfig['handlers'][$eventBusHandler] ) ) {
			// Register handler that will only pass events of the given log level
			$wmgMonologConfig['handlers'][$eventBusHandler] = [
				'class' => \MediaWiki\Extension\EventBus\Adapters\Monolog\EventBusMonologHandler::class,
				'args' => [
					// EventServiceName
					'eventgate-analytics'
				]
			];
		}
		$handlers[] = $eventBusHandler;
	}

	// Configure Logstash handler
	if ( $opts['logstash'] && $wmgEnableLogstash ) {
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
						static function () use ( $handlerName ) {
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
						static function () use ( $handlerName ) {
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
					static function () use ( $handlers ) {
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
		];
	}
}

if (
	$wmgLogAuthmanagerMetrics
	// T160490
	&& $wmgUseWikimediaEvents
) {
	$wmgMonologConfig['loggers']['authevents']['handlers'][] = 'authmanager-statsd';
	$wmgMonologConfig['loggers']['captcha']['handlers'][] = 'authmanager-statsd';
	$wmgMonologConfig['handlers']['authmanager-statsd'] = [
		// defined in WikimediaEvents
		'class' => WikimediaEvents\AuthManagerStatsdHandler::class,
	];
}

$wgMWLoggerDefaultSpi = [
	'class' => \MediaWiki\Logger\MonologSpi::class,
	'args' => [ $wmgMonologConfig ],
];
