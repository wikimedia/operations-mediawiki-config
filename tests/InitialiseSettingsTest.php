<?php

class InitialiseSettingsTest extends WgConfTestCase {

	///
	/// wgLogoHD
	///
	public function testLogoHD() {
		$requiredKeys = $this->getRequiredLogoHDKeys();
		$wgConf = $this->loadWgConf( 'unittest' );

		foreach ( $wgConf->settings['wgLogoHD'] as $db => $entry ) {
			// Test if all logos exist
			foreach ( $entry as $size => $logo ) {
				$this->assertFileExists( __DIR__ . '/..' . $logo, "$db has nonexistent $size logo" );
			}

			// Test if only 1.5x and 2x keys are used
			$keys = array_keys( $entry );
			$this->assertEquals( $requiredKeys, $keys, "Unexpected keys for $db", 0.0, 10, true ); // canonicalize
		}
	}

	public function getRequiredLogoHDKeys() {
		return [ '1.5x', '2x' ];
	}

	///
	/// wgLogo
	///
	public function testLogo() {
		$wgConf = $this->loadWgConf( 'unittest' );

		foreach ( $wgConf->settings['wgLogo'] as $db => $logo ) {
			// Test if all logos exist
			$this->assertFileExists( __DIR__ . '/..' . $logo, "$db has nonexistent 1x logo" );
		}
	}

	///
	/// wgExtraNamespaces
	///
	public function testwgExtraNamespaces() {
		$wgConf = $this->loadWgConf( 'unittest' );
		foreach ( $wgConf->settings['wgExtraNamespaces'] as $db => $entry ) {
			foreach ( $entry as $nb => $ns ) {
				$this->assertFalse( strpos( $ns, ' ' ), "Unexpected spaces in '$ns' namespace title for $db, use underscores instead" );
			}
		}
	}

	///
	/// wgCanonicalServer & wgServer
	///
	public function testwgServer() {
		$wgConf = $this->loadWgConf( 'unittest' );

		// Test if wgCanonicalServer start with https://
		foreach ($wgConf->settings['wgCanonicalServer'] as $db => $entry) {
			$this->assertStringStartsWith( "https://", $entry, "wgCanonicalServer for $db doesn't start with https://" );
		}

		foreach ($wgConf->settings['wgServer'] as $db => $entry) {
			// Test if wgServer start with //
			$this->assertStringStartsWith( "//", $entry, "wgServer for $db doesn't start with //" );

			// Test if wgServer is same like wgCanonicalServer, but protocol-relative
			$this->assertEquals( "https:" . $entry, $wgConf->settings['wgCanonicalServer'][$db], "wgServer isn't protocol-relative version of wgCanonicalServer for $db" );
		}
	}
}
