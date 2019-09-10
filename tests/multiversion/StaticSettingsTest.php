<?php
class StaticSettingsTest extends PHPUnit\Framework\TestCase {

	protected $variantSettings = [];

	public function setUp() {
		$wmfConfigDir = __DIR__ . "/../../wmf-config";
		require_once "{$wmfConfigDir}/VariantSettings.php";
		$variantSettings = wmfGetVariantSettings();
	}

	public function testConfigIsScalar() {
		foreach ( $this->$variantSettings as $variantSettting => $value ) {
			$this->assertTrue( is_scalar( $value ), "Each variant setting must be scalar" );
		}
	}
}
