<?php

if ( !defined( 'MEDIAWIKI_DEPLOYMENT_DIR' ) ) {
	define(	'MEDIAWIKI_DEPLOYMENT_DIR', '/voo/bar/');
}

//require_once __DIR__ . '/../../multiversion/MWRealm.php';
require_once __DIR__ . '/../../multiversion/MWMultiVersion.php';

if ( !class_exists( 'HashBagOStuff' ) ) {
	class HashBagOStuff {}
}

if ( !class_exists( 'EtcdConfig' ) ) {
	class EtcdConfig {
		public function getModifiedIndex() {
			return 'xxx';
		}

		public function get( $name ) {
			return '';
		}
	}
}

class WikidatawikiWgGlobalsTest extends /*WgConfTestCase*/ PHPUnit\Framework\TestCase {

	private $globalsToUnset = [];
	private $removeSrvMediaWikiVersionsFile = false;

	public function setUp() {
		$globals = [
			'wgReadOnly',
			'wmfLocalServices',
			'wmfMasterDatacenter',
			'wmfEtcdLastModifiedIndex',

			'wgConf',
			'wmfConfigDir',
			'wmfUdp2logDest',
			'wmfHostnames',
			'wmfAllServices',
			'wmfMasterServices',
		];
		foreach ( $globals as $var ) {
			if ( !array_key_exists( $var, $GLOBALS ) ) {
				$this->globalsToUnset[] = $var;
				$GLOBALS[$var] = null;
			}
		}
	}

	public function tearDown() {
		foreach ( $this->globalsToUnset as $var ) {
			unset( $GLOBALS[$var] );
		}
	}

	public function testFoo() {
		$IP = __DIR__ . '/../';

//		$wgConf = $this->loadWgConf( 'production' );

		$wmfRealm = 'production';
		$wmfDatacenter = 'eqiad';
		$wmfMasterDatacenter = 'eqiad';

		$wmfConfigDir = __DIR__ . '/../../wmf-config';

		require "{$wmfConfigDir}/wgConf.php";

		MWMultiVersion::initializeFromDBName( 'wikidatawiki' );

		require_once "{$wmfConfigDir}/CommonSettings.php";

		$this->assertTrue(false);
	}

}
