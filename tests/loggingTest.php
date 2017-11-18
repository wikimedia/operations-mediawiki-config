<?php
/**
 * Verify configuration of wmf-config/db.php
 *
 * @license GPLv2 or later
 * @author Erik Bernhardson <ebernhardson@wikimedia.org>
 * @copyright Copyright © 2015, Erik Bernhardson <ebernhardson@wikimedia.org>
 * @file
 */

class loggingTests extends WgConfTestCase {

	public function provideHandlerSetup() {
		return [
			'Setting only a level sends to udp2log and logstash' => [
				// configuration for 'test' channel in wmgMonologChannels
				'debug',
				// handlers expected on the 'test' channel
				[ 'failuregroup|udp2log-debug|logstash-info' ]
			],

			'Can disable logstash' => [
				[ 'logstash' => false ],
				[ 'failuregroup|udp2log-debug' ],
			],

			'Disabling udp2log also disables logstash' => [
				[ 'udp2log' => false ],
				[ 'blackhole' ],
			],

			'Logstash can be enabled when udp2log is disabled' => [
				[ 'udp2log' => false, 'logstash' => 'info' ],
				[ 'failuregroup|logstash-info' ]
			],

			'can enable only kafka' => [
				[ 'kafka' => 'debug', 'logstash' => false, 'udp2log' => false ],
				[ 'failuregroup|kafka-debug' ],
			],

			'can enable buffering' => [
				[ 'buffer' => true ],
				[ 'failuregroup|udp2log-debug-buffered|logstash-info-buffered' ],
			],

			'can enable sampling, which disables logstash' => [
				[ 'sample' => 1000 ],
				[ 'failuregroup|udp2log-debug-sampled-1000' ],
			],

			'false yields backhole' => [
				false,
				[ 'blackhole' ],
			],
		];
	}

	/**
	 * @dataProvider provideHandlerSetup
	 */
	public function testHandlerSetup( $channelConfig, $expectHandlers ) {
		// logging.php does not explicitly declare anything global, so it will
		// only read from the local scope defined here.
		$wmgDefaultMonologHandler = 'blackhole';
		$wgDebugLogFile = false;
		$wmgMonologAvroSchemas = [];
		$wmgLogAuthmanagerMetrics = false;
		$wmgUdp2logDest = 'localhost';
		$wmgLogstashServers = [ 'localhost' ];
		$wmgKafkaServers = [ 'localhost' ];
		$wmgMonologChannels = [ 'test' => $channelConfig ];
		$wmgRealm = 'production';

		include __DIR__ . '/../wmf-config/logging.php';

		foreach ( $expectHandlers as $handlerName ) {
			$this->assertArrayHasKey( $handlerName, $wmgMonologConfig['handlers'] );
		}
		$this->assertEquals(
			$expectHandlers,
			$wmgMonologConfig['loggers']['test']['handlers']
		);
	}

	public function provideAvroSchemas() {
		$wgConf = $this->loadWgConf( 'production' );

		$tests = [];
		foreach ( $wgConf->settings['wmgMonologAvroSchemas']['default'] as $name => $schemaConfig ) {
			$tests[$name] = [ $schemaConfig ];
		}
		return $tests;
	}

	/**
	 * @dataProvider provideAvroSchemas
	 */
	public function testAvroSchemasIsValidJson( $schemaConfig ) {
		$this->assertArrayHasKey( 'schema', $schemaConfig );
		$this->assertArrayHasKey( 'revision', $schemaConfig );
		json_decode( $schemaConfig['schema'] );
		$this->assertEquals( JSON_ERROR_NONE, json_last_error() );
	}

	public function provideConfiguredChannels() {
		foreach ( [ 'production', 'labs' ] as $realm ) {
			$wgConf = $this->loadWgConf( $realm );
			foreach ( $wgConf->settings['wmgMonologChannels'] as $wiki => $channels ) {
				foreach ( $channels as $name => $config ) {
					$tests["\$wmgMonologChannels['$wiki']['$name']"] = [ $config ];
				}
			}
		}
		return $tests;
	}

	/**
	 * @dataProvider provideConfiguredChannels
	 */
	public function testChannelConfigurationIsValid( $config ) {
		if ( is_bool( $config ) ) {
			$this->assertFalse( $config );
		} elseif ( is_string( $config ) ) {
			$this->assertValidLogLevel( $config );
		} elseif ( is_array( $config ) ) {
			$this->assertChannelConfig( $config );
		} else {
			$this->fail( "Unknown config. Must be a string, array or false" );
		}
	}

	public function assertValidLogLevel( $level ) {
		$this->assertTrue(
			in_array( $level, [ 'debug', 'info', 'warning', 'error', false ], true ),
			"$level must be one of: debug, info, warning, error, false"
		);
	}

	public function assertChannelConfig( $config ) {
		$allowed = [ 'udp2log', 'logstash', 'kafka', 'sample', 'buffer' ];
		$extra = array_diff( array_keys( $config ), $allowed );
		$this->assertEquals( [], $extra, 'Expect config keys limited to: ' . implode( ', ', $allowed ) );
		if ( isset( $config['buffer'] ) ) {
			$this->assertInternalType( 'bool', $config['buffer'], 'Buffer must be boolean' );
		}
		if ( isset( $config['sample'] ) ) {
			self::assertThat(
				$config['sample'],
				self::logicalOr( self::equalTo( false ), self::greaterThan( 0 ) ),
				'Sample must be either false or integer > 0'
			);
		}
		foreach ( [ 'udp2log', 'logstash', 'kafka' ] as $handler ) {
			if ( isset( $config[$handler] ) ) {
				$this->assertValidLogLevel( $config[$handler] );
			}
		}
	}
}
