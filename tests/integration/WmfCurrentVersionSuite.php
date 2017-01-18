<?php
class WmfCurrentVersionSuite extends PHPUnit_Framework_TestSuite {
	public static function suite() {
		return new IntegrationTestSuite( IntegrationTestSuite::VER_CURRENT );
	}
}
