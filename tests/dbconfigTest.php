<?php
/**
 * Verify configuration of wmf-config/db.php
 *
 * @license GPLv2 or later
 * @author Antoine Musso <hashar at free dot fr>
 * @copyright Copyright © 2012, Antoine Musso <hashar at free dot fr>
 * @file
 */

require_once __DIR__ . '/../multiversion/MWRealm.php';

class dbconfigTests extends PHPUnit_Framework_TestCase {

	public static function provideRealmDatacenter() {
		return array(
			array( 'production', 'eqiad', 'eqiad' ),
			array( 'production', 'eqiad', 'codfw' ),
			array( 'production', 'codfw', 'eqiad' ),
			array( 'production', 'codfw', 'codfw' ),
			array( 'labs', 'eqiad', 'eqiad' ),
		);
	}

	function loadDbFile( $realm, $datacenter, $masterdatacenter ) {
		global $wmfRealm, $wmfDatacenter, $wmfMasterDatacenter;

		list( $oldRealm, $oldDatacenter, $oldMasterDatacenter ) =
			array( $wmfRealm, $wmfDatacenter, $wmfMasterDatacenter );
		list( $wmfRealm, $wmfDatacenter, $wmfMasterDatacenter ) =
			array( $realm, $datacenter, $masterdatacenter );

		# "properly" load db.php in local context:
		$wgDBname     = 'testwiki';
		$wgDBuser     = 'sqladmin';
		$wgDBpassword = 'secretpass';
		if( !defined( 'DBO_DEFAULT' ) ) {
			define( 'DBO_DEFAULT', 16 );
		}

		include( getRealmSpecificFilename( __DIR__ . '/../wmf-config/db.php' ) );

		list( $wmfRealm, $wmfDatacenter, $wmfMasterDatacenter ) =
			array( $oldRealm, $oldDatacenter, $oldMasterDatacenter );

		return $wgLBFactoryConf;
	}

	/**
	 * Each host in 'sectionLoads' must be listed in 'hostsByName'
	 *
	 * @dataProvider provideRealmDatacenter
	 */
	function testSectionLoadsInHostsbyname( $realm, $datacenter, $masterdatacenter ) {
		$ok = true;
		$lb = $this->loadDbFile( $realm, $datacenter, $masterdatacenter );
		foreach ( $lb['sectionLoads'] as $clusterName => $cluster ) {
			foreach ( $cluster as $host => $weight ) {
				if ( !array_key_exists( $host, $lb['hostsByName'] ) ) {
					$ok = false;
					$this->fail(
						"$host is listed in sectionLoads as a server in the $clusterName cluster," .
						" but it is not listed in 'hostsByName'. All hosts must be listed with" .
						" their IP address in 'hostsByName'."
					);
				}
			}
		}
		$this->assertTrue( $ok );
	}

	/**
	 * Each database in 'sectionsByDB' must point to an existing cluster
	 *
	 * @dataProvider provideRealmDatacenter
	 */
	function testDbAssignedToAnExistingCluster( $realm, $datacenter, $masterdatacenter ) {
		$ok = true;
		$lb = $this->loadDbFile( $realm, $datacenter, $masterdatacenter );
		foreach ( $lb['sectionsByDB'] as $dbname => $cluster) {
			if ( !array_key_exists( $cluster, $lb['sectionLoads'] ) ) {
				$ok = false;
				$this->fail(
					"In sectionsByDB, Database $dbname must points to an existing cluster. " .
					"Unfortunately, cluster '$cluster' is not defined in 'sectionLoads'."
				);
			}
		}
		$this->assertTrue( $ok );
	}

}
