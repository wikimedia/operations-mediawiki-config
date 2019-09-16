<?php
// Ensure that we're not casting any types
// TODO: Uncomment once we're not testing in HHVM.
// declare( strict_types = 1 );

class StaticSettingsTest extends PHPUnit\Framework\TestCase {

	protected $variantSettings = [];

	public function setUp() {
		// HACK: Establish globals still used in VariantSettings
		$GLOBALS['wmfUdp2logDest'] = 'testvalue';
		$GLOBALS['wmfDatacenter'] = 'testvalue';
		$GLOBALS['wmfHostnames'] = [ 'upload' => 'testvalue' ];
		$GLOBALS['wmfLocalServices'] = [ 'irc' => 'testvalue', 'urldownloader' => 'testvalue', 'upload' => 'testvalue' ];

		$configDir = __DIR__ . "/../../wmf-config";
		require_once "{$configDir}/VariantSettings.php";
		$this->variantSettings = wmfGetVariantSettings();
	}

	public function tearDown() {
		// HACK: Unset globals still used in VariantSettings
		unset( $GLOBALS['wmfUdp2logDest'] );
		unset( $GLOBALS['wmfDatacenter'] );
		unset( $GLOBALS['wmfHostnames'] );
		unset( $GLOBALS['wmfLocalServices'] );
	}

	public function testConfigIsScalar() {
		foreach ( $this->variantSettings as $variantSettting => $settingsArray ) {
			$this->assertTrue( is_array( $settingsArray ), "Each variant setting set must be an array, but $variantSettting is not" );

			foreach ( $settingsArray as $wiki => $value ) {
				$this->assertTrue(
					is_scalar( $value ) || is_array( $value ) || $value === null,
					"Each variant setting must be scalar, an array, or null, but $variantSettting is not for $wiki."
				);
			}
		}
	}
}
