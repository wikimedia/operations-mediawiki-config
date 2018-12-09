<?php
/**
 * Structure tests for wmf-config/ProductionServices.php.
 */

class WmfConfigServicesTest extends PHPUnit\Framework\TestCase {

	public static function provideServicesFiles() {
		$wmfConfigDir = dirname( __DIR__ ) . '/wmf-config';

		yield 'ProductionServices' => [
			"$wmfConfigDir/ProductionServices.php",
			[ 'eqiad', 'codfw' ],
		];

		yield 'LabsServices' => [
			"$wmfConfigDir/LabsServices.php",
			[ 'eqiad' ],
		];
	}

	public static function getServicesFiles() {
		$wmfConfigDir = dirname( __DIR__ ) . '/wmf-config';

		return [
			'production' => "$wmfConfigDir/ProductionServices.php",
			'labs' => "$wmfConfigDir/LabsServices.php",
		];
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
	public function testIntraRealmCompatibility( $file ) {
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
	 * Verify that each DC key contains the same set of services.
	 */
	public function testCrossRealmCompatibility() {
		$allSubkeys = [];
		$realms = [];
		foreach ( self::getServicesFiles() as $label => $file ) {
			$realm = require $file;
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
