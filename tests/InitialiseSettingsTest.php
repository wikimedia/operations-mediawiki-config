<?php

class InitialiseSettingsTest extends WgConfTestCase {

	///
	/// wgLogoHD
	///
	public function testLogoHD () {
		$requiredKeys = $this->getRequiredLogoHDKeys();
		$wgConf = $this->loadWgConf( 'unittest' );

		foreach ( $wgConf->settings['wgLogoHD'] as $db => $entry ) {
			$keys = array_keys( $entry );
			$this->assertEquals( $requiredKeys, $keys, "Unexpected keys for $db", 0.0, 10, true ); // canonicalize
		}
	}

	public function getRequiredLogoHDKeys () {
		return [ '1.5x', '2x' ];
	}

}
