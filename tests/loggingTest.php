<?php
/**
 * Verify configuration of wmf-config/db.php
 *
 * @license GPLv2 or later
 * @author Antoine Musso <hashar at free dot fr>
 * @copyright Copyright © 2012, Antoine Musso <hashar at free dot fr>
 * @file
 */

require_once __DIR__ . '/../multiversion/MWRealm.php';

class loggingTests extends PHPUnit_Framework_TestCase {

	private static $defaults = array(
		'wmgDefaultMonologHandler' => 'blackhole',
		'wgDebugLogFile' => false,
		'wmgMonologAvroSchemas' => array(),
		'wmgLogAuthmanagerMetrics' => false,
		'wmfUdp2logDest' => 'localhost',
		'wmgLogstashServers' => array( 'localhost' ),
		'wmgKafkaServers' => array( 'localhost' ),
		'wmgMonologChannels' => array(),
	);

	public function testAppliesChannelDefaults() {
		extract( self::$defaults );
		$wmgMonologChannels = array(
			'test' => 'debug',
		);
		
		include __DIR__ . '/../wmf-config/logging.php';

		$this->assertCount( 4, $wmgMonologConfig['handlers'] );
		$this->assertArrayHasKey( 'udp2log-debug', $wmgMonologConfig['handlers'] );
		$this->assertArrayHasKey( 'logstash-info', $wmgMonologConfig['handlers'] );
		$this->assertEquals(
			array( 'udp2log-debug', 'logstash-info' ),
			$wmgMonologConfig['loggers']['test']['handlers']
		);
	}

	public function handlerSetupProvider() {
		return array(
			array(
				'Setting only a level sends to udp2log and logstash',
				// configuration for 'test' channel in wmgMonologChannels
				'debug',
				// number of expected handlers
				4,
				// handlers expected on the 'test' channel
				array( 'udp2log-debug', 'logstash-info' )
			),

			array(
				'Can disable logstash',
				array( 'logstash' => false ),
				3,
				array( 'udp2log-debug' ),
			),

			array(
				'Disabling udp2log also disables logstash',
				array( 'udp2log' => false ),
				2,
				array(),
			),

			array(
				'Logstash can be enabled when udp2log is disabled',
				array( 'udp2log' => false, 'logstash' => 'info' ),
				3,
				array( 'logstash-info' )
			),

			array(
				'can enable only kafka',
				array( 'kafka' => 'debug', 'logstash' => false, 'udp2log' => false ),
				3,
				array( 'kafka-debug' ),
			),

			array(
				'can enable buffering',
				array( 'buffer' => true ),
				6,
				array( 'udp2log-debug-buffered', 'logstash-info-buffered' ),
			),

			array(
				'can enable sampling, which disables logstash',
				array( 'sample' => 1000 ),
				4,
				array( 'udp2log-debug-sampled-1000' ),
			),
		);
	}

	/**
	 * @dataProvider handlerSetupProvider
	 */
	public function testHandlerSetup( $message, $channelConfig, $expectCount, $expectHandlers, $expectLoggers = null ) {
		extract( self::$defaults );
		$wmgMonologChannels = array( 'test' => $channelConfig );
		include __DIR__ . '/../wmf-config/logging.php';

		$this->assertCount( $expectCount, $wmgMonologConfig['handlers'] );
		foreach ( $expectHandlers as $handlerName ) {
			$this->assertArrayHasKey( $handlerName, $wmgMonologConfig['handlers'], $message );
		}
		$this->assertEquals(
			$expectLoggers === null ? $expectHandlers : $expectLoggers,
			$wmgMonologConfig['loggers']['test']['handlers'],
			$message
		);
	}

	public function configuredChannelsProvider() {
		global $wgConf;
		$wgConf = new stdClass();
		include __DIR__ . '/../wmf-config/InitialiseSettings.php';
		$tests = array();
		foreach ( $wgConf->settings['wmgMonologChannels'] as $wiki => $channels ) {
			foreach ( $channels as $name => $config ) {
				$tests[] = array( $wiki, $name, $config );
			}
		}
		return $tests;
	}

	/**
	 * @dataProvider configuredChannelsProvider
	 */
	public function testConfiguredChannels( $wiki, $name, $config ) {
		if ( is_bool( $config ) ) {
			$this->assertFalse( $config );
		} elseif ( is_string( $config ) ) {
			$this->assertIsLogLevel( $config );
		} elseif ( is_array( $config ) ) {
			$this->assertChannelConfig( $config );
		} else {
			$this->fail( "Unknown config" );
		}
	}

	public function assertIsLogLevel( $level ) {
		$this->assertTrue(
			in_array( $level, array( 'debug', 'info', 'warning', 'error', false ), true ),
			"$level must be one of: debug, info, warning, error, false"
		);
	}

	public function assertChannelConfig( $config ) {
		$allowed = array( 'udp2log', 'logstash', 'kafka', 'sample', 'buffer' );
		$extra = array_diff( array_keys( $config ), $allowed );
		$this->assertEquals( array(), $extra, 'Expected only udp2log, logstash, kafka, sample or buffer' );
		if ( isset( $config['buffer'] ) ) {
			$this->assertTrue( is_bool( $config['buffer'] ), 'Buffer must be boolean true/false' );
		}
		if ( isset( $config['sample'] ) ) {
			self::assertThat(
				$config['sample'],
				$this->logicalOr( self::equalTo( false ), self::greaterThan(0) ),
				'Sample must be either false or integer > 0'
			);
		}
		foreach ( array( 'udp2log', 'logstash', 'kafka' ) as $handler ) {
			if ( isset( $config[$handler] ) ) {
				$this->assertIsLogLevel( $config[$handler] );
			}
		}
	}			
}
