<?php
/**
 * Verify configuration of wmf-config/db.php
 *
 * @license GPLv2 or later
 * @author Antoine Musso <hashar at free dot fr>
 * @copyright Copyright © 2012, Antoine Musso <hashar at free dot fr>
 * @file
 */

require __DIR__ . '/../multiversion/MWRealm.php';

class dbconfigTests extends PHPUnit_Framework_TestCase {

	public static function provideRealmDatacenter() {
		return listAllRealmsAndDatacenters();
	}

	function loadDbFile( $realm, $datacenter ) {
		global $wmfRealm, $wmfDatacenter;

		list( $oldRealm, $oldDatacenter ) = array( $wmfRealm, $wmfDatacenter );
		list( $wmfRealm, $wmfDatacenter ) = array( $realm, $datacenter );

		# "properly" load db.php in local context:
		$wgDBname     = 'testwiki';
		$wgDBuser     = 'sqladmin';
		$wgDBpassword = 'secretpass';
		if( !defined( 'DBO_DEFAULT' ) ) {
			define( 'DBO_DEFAULT', 16 );
		}
		include( getRealmSpecificFilename( __DIR__ . '/../wmf-config/db.php' ) );

		list( $wmfRealm, $wmfDatacenter ) = array( $oldRealm, $oldDatacenter );

		return $wgLBFactoryConf;
	}

	/**
	 * Lame safeguard to raise attention of ops whenever they alter
	 * the number of hosts set in hostsByName. If you really know what
	 * you are doing when editing that configuration section, then increment or
	 * decrement the first argument to assertEquals() below.
	 *
	 * @dataProvider provideRealmDatacenter
	 */

	/* This fails when adding new databases - not a good assertion!
	function testDoNotRemoveLinesInHostsbyname( $realm, $datacenter ) {
		$lb = $this->loadDbFile( $realm, $datacenter );

		$this->assertEquals(  78
			, count( $lb['hostsByName'] )
			, "You shall never remove hosts from hostsByName :-D"
		);


	}
	*/

	/**
	 * Each database in 'sectionsByDB' must point to an existing cluster
	 *
	 * @dataProvider provideRealmDatacenter
	 */
	function testDbAssignedToAnExistingCluster( $realm, $datacenter ) {
		$lb = $this->loadDbFile( $realm, $datacenter );
		foreach( $lb['sectionsByDB'] as $dbname => $cluster) {
			$this->assertArrayHasKey( $cluster,
				$lb['sectionLoads'],
				"In sectionsByDB, Database $dbname must points to an existing cluster. Unfortunately, cluster '$cluster' is not defined in 'sectionLoads'."
			);
		}
	}

}
