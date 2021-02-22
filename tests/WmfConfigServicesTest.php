<?php
/**
 * Structure tests for wmf-config/ProductionServices.php.
 */

class WmfConfigServicesTest extends PHPUnit\Framework\TestCase {

	public static function getServicesFiles() {
		$wmfConfigDir = dirname( __DIR__ ) . '/wmf-config';

		return [
			'production' => [
				'file' => "$wmfConfigDir/ProductionServices.php",
				'dcs' => [ 'eqiad', 'codfw' ],
			],
			'labs' => [
				'file' => "$wmfConfigDir/LabsServices.php",
				'dcs' => [ 'eqiad' ],
			],
		];
	}

	public static function provideServicesFiles() {
		foreach ( self::getServicesFiles() as $label => $info ) {
			yield $label => [ $info['file'], $info['dcs'] ];
		}
	}

	/**
	 * Verify that the file parses correctly, without warnings,
	 * and returns an array with exactly the keys we expect,
	 * nothing less, nothing more.
	 *
	 * @dataProvider provideServicesFiles
	 */
	public function testDcKeys( $file, array $dcs ) {
		$allServices = require $file;

		$this->assertSameValues(
			$dcs,
			array_keys( $allServices ),
			'dcs'
		);
	}

	/**
	 * Verify that each DC key contains the same set of services.
	 *
	 * @dataProvider provideServicesFiles
	 */
	public function testCrossDcCompatibility( $file ) {
		$allSubkeys = [];

		$realm = require $file;
		foreach ( $realm as $dc => $dcServices ) {
			$allSubkeys = array_merge(
				$allSubkeys,
				array_keys( $dcServices )
			);
		}

		// Normalize
		$allSubkeys = array_values( array_unique( $allSubkeys ) );

		foreach ( $realm as $dc => $dcServices ) {
			$this->assertSameValues(
				$allSubkeys,
				array_keys( $dcServices ),
				"service keys for $dc"
			);
		}
	}

	/**
	 * Verify that each realm contains the same set of services.
	 *
	 * Regression tests for T211526.
	 *
	 * If you run into a failure from this test and are absolutely
	 * sure that the service is only conditionally referenced in wmf-config
	 * when in the same realm and not elsewhere, then:
	 *
	 * 1. Add a comment over any and all code that uses the config to point
	 *    out that this key is only available in realm X.
	 *
	 * 2. Add the missing key to other realm(s) with null as the
	 *    placeholder value, to satisfy this unit test.
	 */
	public function testCrossRealmCompatibility() {
		$allSubkeys = [];
		$realms = [];
		foreach ( self::getServicesFiles() as $label => $info ) {
			$realm = require $info['file'];
			foreach ( $realm as $dc => $services ) {
				$allSubkeys = array_merge(
					$allSubkeys,
					array_keys( $services )
				);
			}
			$realms[$label] = $realm;
		}

		// Normalize
		$allSubkeys = array_values( array_unique( $allSubkeys ) );

		foreach ( $realms as $label => $realm ) {
			foreach ( $realm as $dc => $dcServices ) {
				$this->assertSameValues(
					$allSubkeys,
					array_keys( $dcServices ),
					"service keys for $label/$dc"
				);
			}
		}
	}

	/* Verify that ServiceConfig methods operate as expected */
	public function testServiceConfig() {
		$expectations = [
			'eqiad' => [
				'expected_realm' => 'production',
				'expected_dc' => 'eqiad',
				'expected_dcs' => [ "eqiad", "codfw" ],
				'expected_statsd' => "10.64.16.149",
			],
			'labs' => [
				'expected_realm' => 'labs',
				'expected_dc' => 'eqiad',
				'expected_dcs' => [ "eqiad" ],
				'expected_statsd' => 'cloudmetrics1001.eqiad.wmnet',
			],
		];

		foreach ( $expectations as $cluster => $expectations ) {
			try {
				$GLOBALS['mockWmgClusterFile'] = tempnam( "/tmp", "testServiceConfig-wikimedia-cluster" );
				$handle = fopen( $GLOBALS['mockWmgClusterFile'], "w" );
				fwrite( $handle, $cluster );
				fclose( $handle );

				Wikimedia\MWConfig\ServiceConfig::reset();
				$sc = Wikimedia\MWConfig\ServiceConfig::getInstance();

				$this->assertSame( $expectations['expected_realm'], $sc->getRealm(), "cluster: $cluster, checking getRealm()" );
				$this->assertSame( $expectations['expected_dc'], $sc->getDatacenter(), "cluster: $cluster, checking getDatacenter()" );
				$this->assertSame( $expectations['expected_dcs'], $sc->getDatacenters(), "cluster: $cluster, checking getDatacenters()" );
				$this->assertSame( $expectations['expected_statsd'], $sc->getLocalService( "statsd" ), "cluster: $cluster, checking getLocalService('statsd')" );
			} finally {
				// Cleanup
				unlink( $GLOBALS['mockWmgClusterFile'] );
				Wikimedia\MWConfig\ServiceConfig::reset();
				unset( $GLOBALS['mockWmgClusterFile'] );
			}
		}
	}

	protected function assertSameValues( array $expected, array $actual, $message ) {
		// Normalize
		sort( $expected );
		sort( $actual );

		// Compare as new-line delimited string so that failure diff is useful.
		//
		// assertSame() outputs: Failed asserting that Array &0 (
		//     0 => 'etcd'
		//     1 => 'irc'
		//     2 => 'poolcounter'
		//     3 => 'statsd'
		// ) is identical to Array &0 (
		//     0 => 'etcd'
		//     1 => 'poolcounter'
		//     2 => 'statsd'
		// ).
		//
		// assertEquals() outputs: Failed asserting that two arrays are equal.
		// --- Expected | +++ Actual
		//     0 => 'etcd'
		// -   1 => 'irc'
		// -   2 => 'poolcounter'
		// -   3 => 'statsd'
		// +   1 => 'poolcounter'
		// +   2 => 'statsd'
		//
		// assertSameValues outputs: Failed asserting that two strings are identical.
		// --- Expected | +++ Actual
		//  etcd
		// -irc
		//  poolcounter
		$this->assertSame(
			implode( "\n", $expected ),
			implode( "\n", $actual ),
			$message
		);
	}
}
