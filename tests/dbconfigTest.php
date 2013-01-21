<?php
/**
 * Verify configuration of wmf-config/db.php
 *
 * @license GPLv2 or later
 * @author Antoine Musso <hashar at free dot fr>
 * @copyright Copyright © 2012, Antoine Musso <hashar at free dot fr>
 * @file
 */
abstract class DBConfigTestCase extends PHPUnit_Framework_TestCase {
	public $cfgPath;

	/** Array: will contains the load balancer configuration */
	protected $lb;

	/**
	 * @param $suffix string
	 */
	function __construct( $suffix ) {
		parent::__construct();
		$this->cfgPath = dirname( __FILE__ ). '/../wmf-config' ;

		# "properly" load db.php in local context:
		$wgDBname     = 'testwiki';
		$wgDBuser     = 'sqladmin';
		$wgDBpassword = 'secretpass';
		if( !defined( 'DBO_DEFAULT' ) ) {
			define( 'DBO_DEFAULT', 16 );
		}
		include( "{$this->cfgPath}/db-{$suffix}.php" );

		$this->lb = $wgLBFactoryConf;
	}


	/**
	 * Lame safeguard to raise attention of ops whenever they alter
	 * the number of hosts set in hostsByName. If you really know what
	 * you are doing when editing that configuration section, then increment or
	 * decrement the first argument to assertEquals() below.
	 */

	/* This fails when adding new databases - not a good assertion!
	function testDoNotRemoveLinesInHostsbyname() {

		$this->assertEquals(  78
			, count( $this->lb['hostsByName'] )
			, "You shall never remove hosts from hostsByName :-D"
		);


	}
	*/

	/**
	 * Each database in 'sectionsByDB' must point to an existing cluster
	 */
	function testDbAssignedToAnExistingCluster() {
		if ( isset( $this->lb['sectionsByDB'] ) ) {

		foreach( $this->lb['sectionsByDB'] as $dbname => $cluster) {
			$this->assertArrayHasKey( $cluster,
				$this->lb['sectionLoads'],
				"In sectionsByDB, Database $dbname must points to an existing cluster. Unfortunately, cluster '$cluster' is not defined in 'sectionLoads'."
			);
		}
		}
	}

}

class pmtpaDBConfigTests extends DBConfigTestCase {
	function __construct() {
		parent::__construct( 'pmtpa' );
	}
}

class eqiadDBConfigTests extends DBConfigTestCase {
	function __construct() {
		parent::__construct( 'eqiad' );
	}
}


class labsDBConfigTests extends DBConfigTestCase {
	function __construct() {
		parent::__construct( 'labs' );
	}
}


