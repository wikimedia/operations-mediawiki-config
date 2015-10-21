<?php

require_once __DIR__ . '/../multiversion/MWMultiVersion.php';
require_once __DIR__ . '/SiteConfiguration.php';

class cirrusTests extends PHPUnit_Framework_TestCase {
	public function testClusterConfigurationForProdTestwiki() {
		$config = $this->loadCirrusConfig( 'production', 'testwiki', 'wiki', 'en', 'wikipedia' );
		$this->assertArrayNotHasKey( 'wgCirrusSearchServers', $config );
		$this->assertArrayHasKey( 'wgCirrusSearchClusters', $config );
		$this->assertArrayHasKey( 'wgCirrusSearchDefaultCluster', $config );
		$this->assertEquals( 'eqiad', $config['wgCirrusSearchDefaultCluster'] );
		$this->assertCount( 3, $config['wgCirrusSearchClusters'] );

		// testwiki writes to eqiad and the lab replica
		$this->assertCount( 2, $config['wgCirrusSearchWriteClusters'] );

		$this->assertArrayHasKey(
			$config['wgCirrusSearchDefaultCluster'],
			$config['wgCirrusSearchClusters']
		);

		foreach ( $config['wgCirrusSearchWriteClusters'] as $writeCluster ) {
			$this->assertArrayHasKey(
				$writeCluster,
				$config['wgCirrusSearchClusters']
			);
		}
	}

	public function testClusterConfigurationForProdEnwiki() {
		$config = $this->loadCirrusConfig( 'production', 'enwiki', 'wiki', 'en', 'wikipedia' );
		$this->assertArrayNotHasKey( 'wgCirrusSearchServers', $config );
		$this->assertArrayHasKey( 'wgCirrusSearchClusters', $config );
		$this->assertArrayHasKey( 'wgCirrusSearchDefaultCluster', $config );
		$this->assertCount( 3, $config['wgCirrusSearchClusters'] );
		$this->assertCount( 3, $config['wgCirrusSearchShardCount'] );
		$this->assertCount( 3, $config['wgCirrusSearchReplicas'] );
		$this->assertCount( 3, $config['wgCirrusSearchClientSideConnectTimeout'] );

		foreach ( array_keys ( $config['wgCirrusSearchClusters'] ) as $cluster ) {
			$this->assertArrayHasKey( $cluster, $config['wgCirrusSearchShardCount'] );
			$this->assertArrayHasKey( $cluster, $config['wgCirrusSearchReplicas'] );
			$this->assertArrayHasKey( $cluster, $config['wgCirrusSearchClientSideConnectTimeout'] );
		}

		// Only eqiad for now
		$this->assertCount( 1, $config['wgCirrusSearchWriteClusters'] );
		$this->assertArrayHasKey(
			$config['wgCirrusSearchDefaultCluster'],
			$config['wgCirrusSearchClusters']
		);

		$this->assertArrayHasKey(
			reset( $config['wgCirrusSearchWriteClusters'] ),
			$config['wgCirrusSearchClusters']
		);
	}

	private function loadCirrusConfig( $wmfRealm, $wgDBname, $dbSuffix, $lang, $site ) {
		// Variables rqeuired for wgConf.php
		$wmfConfigDir = __DIR__ . "/../wmf-config";

		require "{$wmfConfigDir}/wgConf.php";

		// InitialiseSettings.php explicitly declares these as global, so we must too
		$GLOBALS['wmfUdp2logDest'] = 'localhost';
		$GLOBALS['wmfDatacenter'] = 'unittest';
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

		return compact( array_keys( get_defined_vars() ) );
	}
}
