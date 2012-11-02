<?php
/**
 * Tests related to namespaces.
 *
 * @license GPLv2 or later
 * @author Antoine Musso <hashar at free dot fr>
 * @copyright Copyright © 2012, Antoine Musso <hashar at free dot fr>
 * @file
 */
include_once( 'wgConfTestCase.php' );
class NamespaceTests extends wgConfTestCase {

	/**
	 * @dataProvider Provide::ProjectsDatabases
	 * @group Disabled
	 */
	function testFoo( $projectname, $database ) {
		$this->assertTrue(true);
		global $wgConf;
	}

	function testWgrc2udpaddressInBeta() {
		$this->givenRealm( 'labs' )
			->givenDBname( 'commonswiki' )
			->assertGlobal( 'wgRC2UDPAddress', '208.80.152.178' );
	}

	function testWgrc2udpaddressInProduction() {
		$this->givenRealm( 'production' )
			->givenDBname( 'commonswiki' )
			->assertGlobal( 'wgRC2UDPAddress', '10.4.0.0' );
	}
}
