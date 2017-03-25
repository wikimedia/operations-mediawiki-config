<?php

require_once __DIR__ . '/SiteConfiguration.php';

class InitialiseSettingsTest extends PHPUnit_Framework_TestCase {

	protected $settings;

	protected function setUp() {
		$this->settings = $this->loadSettings( 'unittest' );
	}

	private function loadSettings( $wmfRealm ) {
		// Variables required for wgConf.php
		$wmfConfigDir = __DIR__ . "/../wmf-config";

		require "{$wmfConfigDir}/wgConf.php";

		// InitialiseSettings.php explicitly declares these as global, so we must too
		$GLOBALS['wmfUdp2logDest'] = 'localhost';
		$GLOBALS['wmfDatacenter'] = 'unittest';
		$GLOBALS['wmfMasterDatacenter'] = 'unittest';
		$GLOBALS['wmfRealm'] = $wmfRealm;
		$GLOBALS['wmfConfigDir'] = $wmfConfigDir;
		$GLOBALS['wgConf'] = $wgConf;

		require __DIR__ . '/TestServices.php';
		require "{$wmfConfigDir}/InitialiseSettings.php";

		return $wgConf->settings;
	}

	///
	/// wgLogoHD
	///

	public function testLogoHD () {
		$requiredKeys = $this->getRequiredLogoHDKeys();

		foreach ( $this->settings[ 'wgLogoHD' ] as $db => $entry ) {
			$keys = array_keys( $entry );
			$this->assertEquals( $requiredKeys, $keys, "Unexpected keys for $db", 0.0, 10, true ); // canonicalize
		}
	}

	public function getRequiredLogoHDKeys () {
		return [ '1.5x', '2x' ];
	}

}
