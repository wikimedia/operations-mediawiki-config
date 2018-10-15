<?php

class InitialiseSettingsTest extends WgConfTestCase {

	///
	/// wgLogoHD
	///
	public function testLogoHD() {
		$requiredKeys = $this->getRequiredLogoHDKeys();
		$wgConf = $this->loadWgConf( 'unittest' );

		foreach ( $wgConf->settings['wgLogoHD'] as $db => $entry ) {
			$keys = array_keys( $entry );
			$this->assertEquals( $requiredKeys, $keys, "Unexpected keys for $db", 0.0, 10, true ); // canonicalize
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
			foreach ( $entry as $nb => $ns ) {
				$this->assertFalse( strpos( $ns, ' ' ), "Unexpected spaces in '$ns' namespace title for $db, use underscores instead" );
			}
		}
	}

	///
	/// only existing wikis or dblists may be referenced in IS.php
	///
	public function testOnlyExistingWikis() {
		$wgConf = $this->loadWgConf( 'unittest' );
		$dblistNames = array_keys( DBList::getLists() );
		foreach ($wgConf->settings as $config) {
			foreach ($config as $db => $entry) {
				$dbNormalized = str_replace( "+", "", $db );
				echo $dbNormalized; // debug
				$this->assertTrue( in_array( $dbNormalized , $dblistNames ) || DBList::isInDblist( $dbNormalized, "all" ) || $db == "default", "$db is referenced, but it isn't either a wiki or a dblist" );
			}
		}
	}
}
