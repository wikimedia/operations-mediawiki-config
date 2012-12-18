<?php

require_once( __DIR__ . '/../../multiversion/MWRealm.php' );

class MWRealmTests extends PHPUnit_Framework_TestCase {

	static $fixturesDir;
	static $fixturesFiles = array();

	static function setupBeforeClass() {

		self::$fixturesDir = sys_get_temp_dir() . "/" . __CLASS__;
		if( !is_dir( self::$fixturesDir ) ) {
			mkdir( self::$fixturesDir."/" );
		}

		$filenames = array(
			'general.ext',
			'dc-pmtpa.ext',
			'realm-production.ext',
			'dc_and_realm-production-pmtpa.ext',
		);
		foreach( $filenames as $filename ) {
			$fullname = self::$fixturesDir . "/$filename";
			touch( $fullname );
		}
	}

	static function tearDownAfterClass() {
		// TODO delete fixtures
	}


	/**
	 * @dataProvider provideFilenames
	 */
	function testRealmFilenames( $expected, $filename, $realm = null, $datacenter = null ) {
		global $wmfRealm, $wmfDatacenter;

		// save globals
		$old['realm']      = $wmfRealm;
		$old['datacenter'] = $wmfDatacenter;

		if( $realm      !== null ) { $wmfRealm      = $realm;      }
		if( $datacenter !== null ) { $wmfDatacenter = $datacenter; }

		$this->assertEquals( $expected,
			getRealmSpecificFilename( $filename )
		);

		// restore globals
		$wmfRealm      = $old['realm'];
		$wmfDatacenter = $old['datacenter'];
	}

	function provideFilenames() {
		return array(

			// (expected, filename [, realm[, datacenter]])

			// General file is common to any realm and datacenter
			array( 'general.ext', 'general.ext' ),
			array( 'general.ext', 'general.ext', 'production' ),
			array( 'general.ext', 'general.ext', 'production', 'eqiad' ),
			array( 'general.ext', 'general.ext', 'labs' ),
			array( 'general.ext', 'general.ext', 'labs', 'tmtpa' ),

			array( '', 'realm.ext'),

			array( '', 'dc.ext' ),

		);
	}

}
