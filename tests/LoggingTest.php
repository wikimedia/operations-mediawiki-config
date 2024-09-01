<?php
/**
 * Verify configuration of wmf-config/db.php
 *
 * @license GPL-2.0-or-later
 * @author Erik Bernhardson <ebernhardson@wikimedia.org>
 * @copyright Copyright © 2015, Erik Bernhardson <ebernhardson@wikimedia.org>
 * @file
 */
use Wikimedia\MWConfig\MWConfigCacheGenerator;

/**
 * @covers wmf-config/logging.php
 */
class LoggingTest extends PHPUnit\Framework\TestCase {

	public static function setUpBeforeClass(): void {
		parent::setUpBeforeClass();
		$GLOBALS['wmgDefaultMonologHandlers'] = 'blackhole';
		$GLOBALS['wmgExtraLogFile'] = false;
		$GLOBALS['wmgLogAuthmanagerMetrics'] = false;
		$GLOBALS['wmgUseWikimediaEvents'] = false;
		$GLOBALS['wmgUdp2logDest'] = 'localhost';
		$GLOBALS['wmgEnableLogstash'] = true;
		$GLOBALS['wmgUseEventBus'] = true;
		$GLOBALS['wmgMonologChannels'] = [];
		require_once __DIR__ . '/../wmf-config/logging.php';
	}

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
				// Nothing get configured
				null,
			],

			'Logstash can be enabled when udp2log is disabled' => [
				[ 'udp2log' => false, 'logstash' => 'info' ],
				[ 'failuregroup|logstash-info' ]
			],

			'can enable only eventbus' => [
				[ 'eventbus' => 'debug', 'logstash' => false, 'udp2log' => false ],
				[ 'failuregroup|eventbus-debug' ],
			],

			'can enable buffering' => [
				[ 'buffer' => true ],
				[ 'failuregroup|udp2log-debug-buffered|logstash-info-buffered' ],
			],

			'can enable sampling, which disables logstash' => [
				[ 'sample' => 1000 ],
				[ 'failuregroup|udp2log-debug-sampled-1000' ],
			],

			'false yields blackhole' => [
				false,
				[ 'blackhole' ],
			],
		];
	}

	/**
	 * @dataProvider provideHandlerSetup
	 */
	public function testHandlerSetup( $channelConfig, $expectHandlers ) {
		$GLOBALS['wmgMonologChannels'] = [ 'test' => $channelConfig ];
		$config = wmfGetLoggingConfig();
		$GLOBALS['wmgMonologChannels'] = [];

		if ( $expectHandlers ) {
			foreach ( $expectHandlers as $handlerName ) {
				$this->assertArrayHasKey( $handlerName, $config['handlers'] );
			}

			$this->assertEquals(
				$expectHandlers,
				$config['loggers']['test']['handlers']
			);
		} else {
			$this->assertArrayHasKey( 'loggers', $config );
			$this->assertArrayNotHasKey( 'test', $config['loggers'] );
		}
	}

	public function provideConfiguredProductionChannels() {
		$settings = MWConfigCacheGenerator::getStaticConfig();
		foreach ( $settings['wmgMonologChannels'] as $wiki => $channels ) {
			foreach ( $channels as $name => $config ) {
				$tests["\$wmgMonologChannels['$wiki']['$name']"] = [ $config ];
			}
		}
		return $tests;
	}

	public function provideConfiguredBetaClusterChannels() {
		$settings = MWConfigCacheGenerator::getStaticConfig( 'labs' );
		foreach ( $settings['wmgMonologChannels'] as $wiki => $channels ) {
			foreach ( $channels as $name => $config ) {
				$tests["\$wmgMonologChannels['$wiki']['$name']"] = [ $config ];
			}
		}
		return $tests;
	}

	/**
	 * @dataProvider provideConfiguredProductionChannels
	 * @dataProvider provideConfiguredBetaClusterChannels
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
		$this->assertContains(
			$level, [ 'debug', 'info', 'warning', 'error', false ],
			"$level must be one of: debug, info, warning, error, false"
		);
	}

	public function assertChannelConfig( $config ) {
		$allowed = [ 'udp2log', 'logstash', 'eventbus', 'sample', 'buffer' ];
		$extra = array_diff( array_keys( $config ), $allowed );
		$this->assertEquals( [], $extra, 'Expect config keys limited to: ' . implode( ', ', $allowed ) );
		if ( isset( $config['buffer'] ) ) {
			$this->assertIsBool( $config['buffer'], 'Buffer must be boolean' );
		}
		if ( isset( $config['sample'] ) ) {
			self::assertThat(
				$config['sample'],
				self::logicalOr( self::equalTo( false ), self::greaterThan( 0 ) ),
				'Sample must be either false or integer > 0'
			);
		}
		foreach ( [ 'udp2log', 'logstash', 'eventbus' ] as $handler ) {
			if ( isset( $config[$handler] ) ) {
				$this->assertValidLogLevel( $config[$handler] );
			}
		}
	}
}
