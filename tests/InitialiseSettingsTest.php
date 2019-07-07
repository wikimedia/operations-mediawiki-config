<?php

class InitialiseSettingsTest extends WgConfTestCase {
	///
	/// wgLogoHD and wgLogo
	///
	public function testLogos() {
		$requiredKeys = $this->getRequiredLogoHDKeys();
		$wgConf = $this->loadWgConf( 'unittest' );

		// Build an array of logos to test everything in one cycle
		$logos = [];
		foreach ( $wgConf->settings['wgLogo'] as $db => $logo ) {
			$logos[$db]['1x'] = $logo;
		}
		foreach ( $wgConf->settings['wgLogoHD'] as $db => $entry ) {
			// Test if only 1.5x and 2x keys are used
			// Only relevant to wgLogoHD, so can stay here
			$keys = array_keys( $entry );
			$this->assertEquals( $requiredKeys, $keys, "Unexpected keys for $db", 0.0, 10, true ); // canonicalize

			foreach ( $entry as $size => $logo ) {
				$logos[$db][$size] = $logo;
			}
		}

		// Really test stuff
		foreach ( $logos as $db => $entry ) {
			// 1x must be defined
			$this->assertArrayHasKey( '1x', $entry, "$db has HD logo defined, but no 1x logo" );

			foreach ( $entry as $size => $logo ) {
				// Test if all logos exist
				$this->assertFileExists( __DIR__ . '/..' . $logo, "$db has nonexistent $size logo" );
			}
		}
	}

	public function getRequiredLogoHDKeys() {
		return [ '1.5x', '2x' ];
	}

	///
	/// wgExtraNamespaces
	///
	public function testwgExtraNamespaces() {
		$wgConf = $this->loadWgConf( 'unittest' );

		foreach ( $wgConf->settings['wgExtraNamespaces'] as $db => $entry ) {
			foreach ( $entry as $number => $namespace ) {
				// Test for invalid spaces
				$this->assertFalse( strpos( $namespace, ' ' ), "Unexpected space in '$number' namespace title for $db, use underscores instead" );

				// Test for invalid colons
				$this->assertFalse( strpos( $namespace, ':' ), "Unexpected colon in '$number' namespace title for $db, final colon is not needed and can be removed" );

				// Test namespace numbers
				if ( $number < 100 || in_array( $number, [ 828, 829 ] ) ) {
					continue; // It's not an extra namespace, do not test
				}
				if ( $number % 2 == 0 ) {
					$this->assertTrue( array_key_exists( $number + 1, $entry ), "Namespace $namespace (ID $number) for $db doesn't have corresponding talk namespace set" );
				} else {
					$this->assertTrue( array_key_exists( $number - 1, $entry ), "Namespace $namespace (ID $number) for $db doesn't have corresponding non-talk namespace set" );
				}
			}
		}
	}

	///
	/// wgMetaNamespace
	///
	public function testMetaNamespaces() {
		$wgConf = $this->loadWgConf( 'unittest' );

		foreach ( $wgConf->settings['wgMetaNamespace'] as $db => $namespace ) {
			// Test for invalid spaces
			$this->assertFalse( strpos( $namespace, ' ' ), "Unexpected space in meta namespace title for $db, use underscores instead" );

			// Test for invalid colons
			$this->assertFalse( strpos( $namespace, ':' ), "Unexpected colon in meta namespace title for $db, final colon is not needed and should be removed" );
		}

		foreach ( $wgConf->settings['wgMetaNamespaceTalk'] as $db => $namespace ) {
			// Test for invalid spaces
			$this->assertFalse( strpos( $namespace, ' ' ), "Unexpected space in meta talk namespace title for $db, use underscores instead" );

			// Test for invalid colons
			$this->assertFalse( strpos( $namespace, ':' ), "Unexpected colon in meta talk namespace title for $db, final colon is not needed and should be removed" );
		}
	}

	///
	/// only existing wikis or dblists may be referenced in IS.php
	///
	public function testOnlyExistingWikis() {
		$wgConf = $this->loadWgConf( 'unittest' );
		$dblistNames = array_keys( DBList::getLists() );
		$langs = file( __DIR__ . "/../langlist", FILE_IGNORE_NEW_LINES );
		foreach ( $wgConf->settings as $config ) {
			foreach ( $config as $db => $entry ) {
				$dbNormalized = str_replace( "+", "", $db );
				$this->assertTrue(
					in_array( $dbNormalized, $dblistNames ) ||
					DBList::isInDblist( $dbNormalized, "all" ) ||
					in_array( $dbNormalized,  $langs ) ||
					in_array( $dbNormalized, [ "default", "lzh", "yue", "nan" ] ), // TODO: revert back to $db == "default"
					"$dbNormalized is referenced, but it isn't either a wiki or a dblist" );
			}
		}
	}
}
