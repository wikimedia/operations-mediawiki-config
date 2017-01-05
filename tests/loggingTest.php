<?php
/**
 * Verify configuration of wmf-config/db.php
 *
 * @license GPLv2 or later
 * @author Erik Bernhardson <ebernhardson@wikimedia.org>
 * @copyright Copyright © 2015, Erik Bernhardson <ebernhardson@wikimedia.org>
 * @file
 */

class loggingTests extends PHPUnit_Framework_TestCase {

	private $globals = array();

	protected function tearDown() {
		foreach ( $this->globals as $key => $value ) {
			$GLOBALS[$key] = $value;
		}
		$this->globals = array();
	}

	public function provideHandlerSetup() {
		return array(
			'Setting only a level sends to udp2log and logstash' => array(
				// configuration for 'test' channel in wmgMonologChannels
				'debug',
				// handlers expected on the 'test' channel
				array( 'failuregroup|udp2log-debug|logstash-info' )
			),

			'Can disable logstash' => array(
				array( 'logstash' => false ),
				array( 'failuregroup|udp2log-debug' ),
			),

			'Disabling udp2log also disables logstash' => array(
				array( 'udp2log' => false ),
				array( 'blackhole' ),
			),

			'Logstash can be enabled when udp2log is disabled' => array(
				array( 'udp2log' => false, 'logstash' => 'info' ),
				array( 'failuregroup|logstash-info' )
			),

			'can enable only kafka' => array(
				array( 'kafka' => 'debug', 'logstash' => false, 'udp2log' => false ),
				array( 'failuregroup|kafka-debug' ),
			),

			'can enable buffering' => array(
				array( 'buffer' => true ),
				array( 'failuregroup|udp2log-debug-buffered|logstash-info-buffered' ),
			),

			'can enable sampling, which disables logstash' => array(
				array( 'sample' => 1000 ),
				array( 'failuregroup|udp2log-debug-sampled-1000' ),
			),

			'false yields backhole' => array(
				false,
				array( 'blackhole' ),
			),
		);
	}

	/**
	 * @dataProvider provideHandlerSetup
	 */
	public function testHandlerSetup( $channelConfig, $expectHandlers ) {
		// logging.php does not explicitly declare anything global, so it will
		// only read from the local scope defined here.
		$wmgDefaultMonologHandler = 'blackhole';
		$wgDebugLogFile = false;
		$wmgMonologAvroSchemas = array();
		$wmgLogAuthmanagerMetrics = false;
		$wmfUdp2logDest = 'localhost';
		$wmgLogstashServers = array( 'localhost' );
		$wmgKafkaServers = array( 'localhost' );
		$wmgMonologChannels = array( 'test' => $channelConfig );
		$wmfRealm = 'production';

		include __DIR__ . '/../wmf-config/logging.php';

		foreach ( $expectHandlers as $handlerName ) {
			$this->assertArrayHasKey( $handlerName, $wmgMonologConfig['handlers'] );
		}
		$this->assertEquals(
			$expectHandlers,
			$wmgMonologConfig['loggers']['test']['handlers']
		);
	}

	public static function provideAvroSchemas() {
		$wgConf = new stdClass;
		$wmfConfigDir = __DIR__ . '/../wmf-config';

		$GLOBALS['wmfUdp2logDest'] = 'localhost';
		$GLOBALS['wmfDatacenter'] = 'unittest';
		$GLOBALS['wmfRealm'] = 'production';
		$GLOBALS['wmfConfigDir'] = $wmfConfigDir;
		$GLOBALS['wgConf'] = $wgConf;

		require __DIR__ . "/TestServices.php";
		require "{$wmfConfigDir}/InitialiseSettings.php";

		$tests = array();
		foreach ( $wgConf->settings['wmgMonologAvroSchemas']['default'] as $name => $schemaConfig ) {
			$tests[$name] = array( $schemaConfig );
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
		// InitializeSettings.php explicitly declares these as global, so we need
		// to as well.
		$this->setGlobals( array(
			'wmfUdp2logDest' => 'localhost',
			'wmfDatacenter' => 'test',
			'wmfConfigDir' => __DIR__ . '/../wmf-config',
		) );
		global $wgConf;
		foreach ( array( 'production', 'labs' ) as $realm ) {
			$this->setGlobals( array(
				'wgConf' => new stdClass(),
				'wmfRealm' => $realm,
			) );
			include __DIR__ . '/../wmf-config/InitialiseSettings.php';
			foreach ( $wgConf->settings['wmgMonologChannels'] as $wiki => $channels ) {
				foreach ( $channels as $name => $config ) {
					$tests["\$wmgMonologChannels['$wiki']['$name']"] = array( $config );
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
			in_array( $level, array( 'debug', 'info', 'warning', 'error', false ), true ),
			"$level must be one of: debug, info, warning, error, false"
		);
	}

	public function assertChannelConfig( $config ) {
		$allowed = array( 'udp2log', 'logstash', 'kafka', 'sample', 'buffer' );
		$extra = array_diff( array_keys( $config ), $allowed );
		$this->assertEquals( array(), $extra, 'Expect config keys limited to: ' . implode( ', ', $allowed ) );
		if ( isset( $config['buffer'] ) ) {
			$this->assertInternalType( 'bool', $config['buffer'], 'Buffer must be boolean' );
		}
		if ( isset( $config['sample'] ) ) {
			self::assertThat(
				$config['sample'],
				self::logicalOr( self::equalTo( false ), self::greaterThan(0) ),
				'Sample must be either false or integer > 0'
			);
		}
		foreach ( array( 'udp2log', 'logstash', 'kafka' ) as $handler ) {
			if ( isset( $config[$handler] ) ) {
				$this->assertValidLogLevel( $config[$handler] );
			}
		}
	}

	protected function setGlobals( $pairs, $value = null ) {
		if ( is_string( $pairs ) ) {
			$pairs = array( $pairs => $value );
		}
		foreach ( $pairs as $key => $value ) {
			// only set value in $this->globals on first call
			if ( !array_key_exists( $key, $this->globals ) ) {
				if ( isset( $GLOBALS[$key] ) ) {
					// break any object references
					try {
						$this->globals[$key] = unserialize( serialize( $GLOBALS[$key] ) );
					} catch ( \Exception $e ) {
						$this->globals[$key] = $GLOBALS[$key];
					}
				} else {
					$this->globals[$key] = null;
				}
			}
			$GLOBALS[$key] = $value;
		}
	}
}
