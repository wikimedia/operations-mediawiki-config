<?php
use MediaWiki\Logger\LoggerFactory;

if ( $wmgUseMonologLogger ) {
	/**
	* Configure a MediaWiki\Logger\Monolog\SyslogHandler instance.
	* @param int $level Minimum logging level that will trigger this handler
	* @return array \MediaWiki\Logger\MonologSpi handler configuration
	*/
	function wmMonologSyslogConfigFactory( $level ) {
		static $servers = array(
			'10.64.32.138', // logstash1001.eqiad.wmnet
			'10.64.32.137', // logstash1002.eqiad.wmnet
			'10.64.32.136', // logstash1003.eqiad.wmnet
		);
		return array(
			'class' => '\\MediaWiki\\Logger\\Monolog\\SyslogHandler',
			'args' => array(
				'mediawiki',    // syslog appname
				function () use ( $servers ) {
					return $servers[ mt_rand( 0, count( $servers ) - 1 ) ];
				},              // randomly chose server
				10514,          // logstash syslog listener port
				LOG_USER,       // syslog facility
				$level,         // minimum log level to pass to logstash
			),
			'formatter' => 'logstash',
		);
	}

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
			'null' => array(
				'class' => '\\Monolog\\Handler\\NullHandler',
			),
			'udp2log' => array(
				'class' => '\\MediaWiki\\Logger\\Monolog\\LegacyHandler',
				'args' => array( "udp://{$wmfUdp2logDest}/{channel}", true ),
				'formatter' => 'legacy',
			),
		),

		'formatters' => array(
			'legacy' => array(
				'class' => '\\MediaWiki\\Logger\\Monolog\\LegacyFormatter',
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
		$level = 'debug';
		$sendToLogstash = true;
		if ( is_array( $dest ) ) {
			// NOTE: sampled logs are not guaranteed to store the same events
			// in logstash and via udp2log since the two event handlers
			// independently check the probability of emitting.
			$sample = isset( $dest['sample'] ) ? $dest['sample'] : $sample;
			$level = isset( $dest['level'] ) ? $dest['level'] : $level;
			$sendToLogstash = isset( $dest['logstash'] ) ? $dest['logstash'] : $sendToLogstash;
			$dest = $dest['destination'];
		}

		if ( $sendToLogstash ) {
			$logstashHandler = "logstash-{$level}";
			if ( !isset( $wmgMonologConfig['handlers'][$logstashHandler] ) ) {
				// Register handler that will only pass events of the given
				// log level
				$wmgMonologConfig['handlers'][$logstashHandler] =
					wmMonologSyslogConfigFactory( $level );
			}

			if ( $sample === false ) {
				$handlers = array( 'udp2log', $logstashHandler );
			} else {
				$sampledHandler = "{$logstashHandler}-sampled-{$sample}";
				if ( !isset( $wmgMonologConfig['handlers'][$sampledHandler] ) ) {
					// Register a handler that will sample the event stream and
					// pass events on to $logstashHandler for storage
					$wmgMonologConfig['handlers'][$sampledHandler] = array(
						'class' => '\\Monolog\\Handler\\SamplingHandler',
						'args' => array(
							function () use ( $logstashHandler ) {
								return LoggerFactory::getProvider()->getHandler(
									$logstashHandler
								);
							},
							$sample,
						),
					);
				}
				$handlers = array( 'udp2log', $sampledHandler );
			}
		} else {
			$handlers = array( 'udp2log' );
		}

		$wmgMonologConfig['loggers'][$group] = array(
			'handlers' => $handlers,
			'processors' => array( 'wiki', 'psr', 'pid', 'uid', 'web' ),
		);
	}

	$wgMWLoggerDefaultSpi = array(
		'class' => '\\MediaWiki\\Logger\\MonologSpi',
		'args' => array( $wmgMonologConfig ),
	);
}
