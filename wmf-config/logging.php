<?php
if ( $wmgUseMonologLogger ) {
	/**
	* Create a config array for a \Monolog\Handler\RedisHandler instance.
	* @param int $level Minimum logging level at which this handler will be
	* triggered
	* @return array
	*/
	function wmMonologRedisConfigFactory( $level ) {
		global $wmgLogstashPassword;
		return array(
			'class' => '\\Monolog\\Handler\\RedisHandler',
			'args' => array(
				function () use ( $wmgLogstashPassword ) {
					$servers = array(
						'10.64.32.138', // logstash1001.eqiad.wmnet
						'10.64.32.137', // logstash1002.eqiad.wmnet
						'10.64.32.136', // logstash1003.eqiad.wmnet
					);
					// Connect to a random logstash host
					$server = $servers[ mt_rand( 0, count($servers) - 1 ) ];

					$redis = new Redis();
					$redis->connect( $server, 6379, .25 );
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
	$wmgMonologConfig = array(
		'loggers' => array(
			// Nothing is logged unless a specific channel config is added
			'@default' => array(
				'handlers' => array( 'null' ),
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
				'formatter' => 'line',
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
			'line' => array(
				'class' => '\\Monolog\\Formatter\\LineFormatter',
			),
		),
	);

	// Add logging channels defined in $wgDebugLogGroups
	foreach ( $wgDebugLogGroups as $group => $dest ) {
		$sample = false;
		$level = false;
		$sendToLogstash = true;
		$logstashHandler = 'logstash';
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
			if ( $level !== false ) {
				$logstashHandler = "filtered-{$group}";
				$wmgMonologConfig['handlers'][$logstashHandler] =
					wmMonologRedisConfigFactory( $level );
			}

			if ( $sample === false ) {
				$handlers = array( $group, $logstashHandler );
			} else {
				$wmgMonologConfig['handlers']["sampled-{$group}"] = array(
					'class' => '\\Monolog\\Handler\\SamplingHandler',
					'args' => array(
						function () use ( $logstashHandler ) {
							return MWLogger::getProvider()->getHandler(
								$logstashHandler
							);
						},
						$sample,
					),
				);
				$handlers = array( $group, "sampled-{$group}" );
			}
		} else {
			$handlers = array( $group );
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
}
