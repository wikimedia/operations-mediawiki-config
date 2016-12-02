<?php

require_once __DIR__ . '/../../multiversion/MWRealm.php';

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
			'realm-production.ext',
			'realm.ext',
			'dc-eqiad.ext',
			'dc.ext',
			'dc_and_realm-production-eqiad.ext',
			'dc_and_realm.ext',
			# Real life use case:
			'mc-eqiad.php',
			'mc-production.php',
			'mc.php',
			'langlist',
			'langlist-labs',
		);
		foreach( $filenames as $filename ) {
			$fullname = self::$fixturesDir . "/$filename";

			# Record filename for future deletion
			self::$fixturesFiles[] = $fullname;
			touch( $fullname );
		}
	}

	static function tearDownAfterClass() {
		foreach( self::$fixturesFiles as $fixture ) {
			unlink( $fixture );
		}
		@rmdir( self::$fixturesDir );
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

		# The function requires a real path:
		$basePath = self::$fixturesDir . "/";
		$specific = getRealmSpecificFilename( $basePath . $filename );
		# Which we strip before comparaison
		$specific = str_replace( $basePath, '', $specific );

		$this->assertEquals( $expected, $specific );

		// restore globals
		$wmfRealm      = $old['realm'];
		$wmfDatacenter = $old['datacenter'];
	}

	function provideFilenames() {
		return array(

			// (expected, filename [, realm[, datacenter]])

			// general file is common to any realm and datacenter
			array( 'general.ext', 'general.ext' ),
			array( 'general.ext', 'general.ext', 'production' ),
			array( 'general.ext', 'general.ext', 'production', 'eqiad' ),
			array( 'general.ext', 'general.ext', 'labs' ),
			array( 'general.ext', 'general.ext', 'labs', 'tmtpa' ),

			/**
			 * realm file only vary per realm
			 */
			array( 'realm-production.ext', 'realm.ext', 'production' ),
			array( 'realm-production.ext', 'realm.ext', 'production', 'eqiad' ),
			array( 'realm-production.ext', 'realm.ext', 'production', 'UNKNOWN_DC' ),

			array( 'realm.ext', 'realm.ext', 'UNKNOWN_REALM' ),
			array( 'realm.ext', 'realm.ext', 'UNKNOWN_REALM', 'eqiad' ),
			# realm-labs.ext is not in the fixtures, so should fallback to default
			array( 'realm.ext', 'realm.ext', 'labs' ),

			/**
			 * dc file only vary per datacenter
			 */
			// array( 'dc.ext', 'dc.ext', 'labs', 'eqiad' ), // -'dc.ext' +'dc-eqiad.ext'
			// array( 'dc.ext', 'dc.ext', 'production', 'eqiad' ), // -'dc.ext' +'dc-eqiad.ext'
			array( 'dc-eqiad.ext', 'dc.ext', 'labs', 'eqiad' ),
			array( 'dc-eqiad.ext', 'dc.ext', 'production', 'eqiad' ),

			array( 'dc-eqiad.ext', 'dc.ext', 'production', 'eqiad' ),

			/**
			 * dc_and_realm vary by both realm and datacenter
			 */
			array( 'dc_and_realm.ext', 'dc_and_realm.ext', 'labs', 'eqiad' ),
			//array( 'dc_and_realm.ext', 'dc_and_realm.ext', 'production', 'eqiad' ), // -'dc_and_realm.ext' +'dc_and_realm-production-eqiad.ext'

			# Get the filename when both realm and prod are set :)
			array( 'dc_and_realm-production-eqiad.ext', 'dc_and_realm.ext',
				'production', 'eqiad' ),

			# datacenter takes precedence over realm
			array( 'mc-eqiad.php', 'mc.php', 'labs', 'eqiad' ),
			array( 'mc.php', 'mc.php', 'labs', 'UNKNOWN_DC' ),

			array( 'mc-eqiad.php', 'mc.php', 'UNKNOWN_REALM', 'eqiad' ),

			array( 'mc-production.php', 'mc.php', 'production', 'UNKNOWN_DC' ),
			array( 'mc-production.php', 'mc.php', 'production', 'eqiad' ),

			array( 'langlist-labs', 'langlist', 'labs', 'eqiad' ),
			array( 'langlist', 'langlist', 'production', 'eqiad' ),
		);
	}

}
