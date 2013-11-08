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
		global $wmgRealm, $wmgDatacenter;

		list( $oldRealm, $oldDatacenter ) = array( $wmgRealm, $wmgDatacenter );
		list( $wmgRealm, $wmgDatacenter ) = array( $realm, $datacenter );

		# "properly" load db.php in local context:
		$wgDBname     = 'testwiki';
		$wgDBuser     = 'sqladmin';
		$wgDBpassword = 'secretpass';
		if( !defined( 'DBO_DEFAULT' ) ) {
			define( 'DBO_DEFAULT', 16 );
		}

		include( getRealmSpecificFilename( __DIR__ . '/../wmf-config/db.php' ) );

		list( $wmgRealm, $wmgDatacenter ) = array( $oldRealm, $oldDatacenter );

		return $wgLBFactoryConf;
	}

	/**
	 * Each host in 'sectionLoads' must be listed in 'hostsByName'
	 *
	 * @dataProvider provideRealmDatacenter
	 */
	function testSectionLoadsInHostsbyname( $realm, $datacenter ) {
		$lb = $this->loadDbFile( $realm, $datacenter );
		foreach( $lb['sectionLoads'] as $clusterName => $cluster ) {
			foreach( $cluster as $host => $weight ) {
				$this->assertArrayHasKey(
					$host,
					$lb['hostsByName'],
					"$host is listed in sectionLoads as a server in the $clusterName cluster, but it is not listed in 'hostsByName'. " .
						" All hosts must be listed with their IP address in 'hostsByName'."
				);
			}
		}
	}

	/**
	 * Each database in 'sectionsByDB' must point to an existing cluster
	 *
	 * @dataProvider provideRealmDatacenter
	 */
	function testDbAssignedToAnExistingCluster( $realm, $datacenter ) {
		$lb = $this->loadDbFile( $realm, $datacenter );
		foreach( $lb['sectionsByDB'] as $dbname => $cluster) {
			$this->assertArrayHasKey(
				$cluster,
				$lb['sectionLoads'],
				"In sectionsByDB, Database $dbname must points to an existing cluster. Unfortunately, cluster '$cluster' is not defined in 'sectionLoads'."
			);
		}
	}

}
