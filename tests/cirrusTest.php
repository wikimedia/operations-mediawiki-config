<?php

require_once __DIR__ . '/../multiversion/MWMultiVersion.php';
require_once __DIR__ . '/SiteConfiguration.php';

/**
 * @backupGlobals enabled
 */
class cirrusTests extends PHPUnit_Framework_TestCase {
	public static function provideShardCount() {
		return array(
			array( 'eqiad', 'itwiki', array(
				'content' => 7,
				'general' => 9,
			) ),
			array( 'codfw', 'itwiki', array(
				'content' => 7,
				'general' => 7,
			) ),
			array( 'eqiad', 'enwiki', array(
				'content' => 7,
				'general' => 10,
				'titlesuggest' => 4,
			) ),
			array( 'codfw', 'enwiki', array(
				'content' => 7,
				'general' => 7,
				'titlesuggest' => 4,
			) ),
		);
	}

	/**
	 * @dataProvider provideShardCount
	 */
	public function testPerClusterShardCountIsRespected( $cluster, $wiki, $expect ) {
		$config = $this->loadCirrusConfig( 'production', $wiki, 'wiki', 'en', 'wikipedia', $cluster );
		$this->assertEquals( $expect, $config['wgCirrusSearchShardCount'] );
	}

	public function testClusterConfigurationForProdTestwiki() {
		$config = $this->loadCirrusConfig( 'production', 'testwiki', 'wiki', 'en', 'wikipedia' );
		$this->assertArrayNotHasKey( 'wgCirrusSearchServers', $config );
		$this->assertArrayHasKey( 'wgCirrusSearchClusters', $config );
		$this->assertArrayHasKey( 'wgCirrusSearchDefaultCluster', $config );
		$this->assertEquals( 'eqiad', $config['wgCirrusSearchDefaultCluster'] );
	}

	public function testClusterConfigurationForProdEnwiki() {
		$config = $this->loadCirrusConfig( 'production', 'enwiki', 'wiki', 'en', 'wikipedia' );
		$this->assertArrayHasKey( 'wgCirrusSearchServers', $config );
		$this->assertArrayHasKey( 'wgCirrusSearchClusters', $config );
		$this->assertCount( 1, $config['wgCirrusSearchClusters'] );
		$this->assertEquals(
			$config['wgCirrusSearchServers'],
			reset( $config['wgCirrusSearchClusters'] )
		);
		$this->assertEquals(
			$config['wgCirrusSearchDefaultCluster'],
			reset( array_keys( $config['wgCirrusSearchClusters'] ) )
		);
	}

	private function loadCirrusConfig( $wmfRealm, $wgDBname, $dbSuffix, $lang, $site, $dataCenter = 'eqiad' ) {
		// Variables rqeuired for wgConf.php
		$wmfConfigDir = __DIR__ . "/../wmf-config";
		$IP = __DIR__ .'/../php/';

		require "{$wmfConfigDir}/wgConf.php";

		// InitialiseSettings.php explicitly declares these as global, so we must too
		$GLOBALS['wmfUdp2logDest'] = 'localhost';
		$GLOBALS['wmfDatacenter'] = $dataCenter;
		$GLOBALS['wmfRealm'] = $wmfRealm;
		$GLOBALS['wmfConfigDir'] = $wmfConfigDir;
		$GLOBALS['wgConf'] = $wgConf;

		require "{$wmfConfigDir}/InitialiseSettings.php";

		$globals = $wgConf->getAll( $wgDBname, $dbSuffix, array(
			'lang' => $lang,
			'docRoot' => '/dev/null',
			'site' => $site,
			'stdlogo' => 'file://dev/null',
		), array() );

		extract( $globals );

		// variables that would have been setup elsewhere, perhaps in mediawiki
		// default settings or by CommonSettings.php, or by CirrusSearch.php,
		// but none of those are a part of this repository
		$wgJobTypeConf = array( 'default' => array() );
		$wgCirrusSearchWeights = array();
		$wgCirrusSearchNamespaceWeights = array();
		$wmfSwiftEqiadConfig = array(
			'cirrusAuthUrl' => '',
			'cirrusUser' => '',
			'cirrusKey' => '',
		);
		$wgCirrusSearchPoolCounterKey = 'unittest:poolcounter:blahblahblah';

		require "{$wmfConfigDir}/CirrusSearch-common.php";

		return get_defined_vars();
	}
}
