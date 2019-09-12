<?php
/**
 * Verify configuration of wmf-config/db.php
 *
 * @license GPL-2.0-or-later
 * @author Antoine Musso <hashar at free dot fr>
 * @copyright Copyright © 2012, Antoine Musso <hashar at free dot fr>
 * @file
 */

require_once __DIR__ . '/../multiversion/MWRealm.php';

class DbconfigTest extends WgConfTestCase {

	public static function provideRealmDatacenter() {
		return [
			[ 'production', 'eqiad', 'eqiad' ],
			[ 'production', 'eqiad', 'codfw' ],
			[ 'production', 'codfw', 'eqiad' ],
			[ 'production', 'codfw', 'codfw' ],
			[ 'labs', 'eqiad', 'eqiad' ],
		];
	}

	public function loadDbFile( $realm, $datacenter, $masterdatacenter ) {
		$this->setGlobals( [
			'wmfRealm' => $realm,
			'wmfDatacenter' => $datacenter,
		] );

		# "properly" load db.php in local context:
		$wgDBname     = 'testwiki';
		$wgDBuser     = 'sqladmin';
		$wgDBpassword = 'secretpass';
		if ( !defined( 'DBO_DEFAULT' ) ) {
			define( 'DBO_DEFAULT', 16 );
		}

		// intentionally not marked as global
		$wmfMasterDatacenter = $masterdatacenter;
		$wmfRealm = $realm;
		$wmfDatacenter = $datacenter;
		// Copied from CommonSettings.php
		if ( $wmfRealm === 'labs' ) {
			require __DIR__ . "/../wmf-config/db-labs.php";
		} else {
			require __DIR__ . "/../wmf-config/db-{$wmfDatacenter}.php";
		}

		$this->restoreGlobals();
		return $wgLBFactoryConf;
	}

	/**
	 * Each host in 'sectionLoads' must be listed in 'hostsByName'
	 *
	 * @dataProvider provideRealmDatacenter
	 */
	public function testSectionLoadsInHostsbyname( $realm, $datacenter, $masterdatacenter ) {
		if ( !defined( 'HHVM_VERSION' ) ) {
			$this->markTestSkipped( 'DB config tests only pass on HHVM, not PHP72.' );
		}

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
	public function testDbAssignedToAnExistingCluster( $realm, $datacenter, $masterdatacenter ) {
		if ( !defined( 'HHVM_VERSION' ) ) {
			$this->markTestSkipped( 'DB config tests only pass on HHVM, not PHP72.' );
		}

		$ok = true;
		$lb = $this->loadDbFile( $realm, $datacenter, $masterdatacenter );
		foreach ( $lb['sectionsByDB'] as $dbname => $cluster ) {
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
