<?php

use Wikimedia\Config\DatabaseLists;

require_once __DIR__ . '/../multiversion/MWMultiVersion.php';
require_once __DIR__ . '/SiteConfiguration.php';

class cirrusTests extends PHPUnit_Framework_TestCase {
	public function testClusterConfigurationForProdTestwiki() {
		$wmfDatacenter = 'unittest';
		$config = $this->loadCirrusConfig( 'production', 'testwiki', 'wiki' );
		$this->assertArrayNotHasKey( 'wgCirrusSearchServers', $config );
		$this->assertArrayHasKey( 'wgCirrusSearchClusters', $config );
		$this->assertArrayHasKey( 'wgCirrusSearchDefaultCluster', $config );
		$this->assertEquals( 'unittest', $config['wgCirrusSearchDefaultCluster'] );
		$this->assertCount( 2, $config['wgCirrusSearchClusters'] );

		// testwiki writes to eqiad and codfw
		$this->assertCount( 2, $config['wgCirrusSearchWriteClusters'] );

		foreach ( $config['wgCirrusSearchWriteClusters'] as $writeCluster ) {
			$this->assertArrayHasKey(
				$writeCluster,
				$config['wgCirrusSearchClusters']
			);
		}
	}

	public function testClusterConfigurationForProdEnwiki() {
		$config = $this->loadCirrusConfig( 'production', 'enwiki', 'wiki' );
		$this->assertArrayNotHasKey( 'wgCirrusSearchServers', $config );
		$this->assertArrayHasKey( 'wgCirrusSearchClusters', $config );
		$this->assertArrayHasKey( 'wgCirrusSearchDefaultCluster', $config );
		$this->assertCount( 2, $config['wgCirrusSearchClusters'] );
		$this->assertCount( 2, $config['wgCirrusSearchShardCount'] );
		$this->assertCount( 2, $config['wgCirrusSearchReplicas'] );
		$this->assertCount( 2, $config['wgCirrusSearchClientSideConnectTimeout'] );

		foreach ( array_keys ( $config['wgCirrusSearchClusters'] ) as $cluster ) {
			$this->assertArrayHasKey( $cluster, $config['wgCirrusSearchShardCount'] );
			$this->assertArrayHasKey( $cluster, $config['wgCirrusSearchReplicas'] );
			$this->assertArrayHasKey( $cluster, $config['wgCirrusSearchClientSideConnectTimeout'] );
		}

		$this->assertCount( 2, $config['wgCirrusSearchWriteClusters'] );
		foreach ( $config['wgCirrusSearchWriteClusters'] as $cluster ) {
			$this->assertArrayHasKey(
				$cluster,
				$config['wgCirrusSearchClusters']
			);
		}
	}

	public function testSubphraseCompletion() {
		// never ever enable subphrases completion for enwiki
		$config = $this->loadCirrusConfig( 'production', 'enwiki', 'wiki' );
		$this->assertFalse( $config['wgCirrusSearchCompletionSuggesterSubphrases']['build'] );
		$this->assertFalse( $config['wgCirrusSearchCompletionSuggesterSubphrases']['use'] );

		$config = $this->loadCirrusConfig( 'production', 'frwikisource', 'wiki' );
		$this->assertTrue( $config['wgCirrusSearchCompletionSuggesterSubphrases']['build'] );

		$config = $this->loadCirrusConfig( 'production', 'mediawikiwiki', 'wiki' );
		$this->assertTrue( $config['wgCirrusSearchCompletionSuggesterSubphrases']['build'] );

		$config = $this->loadCirrusConfig( 'production', 'wikitech', 'wiki' );
		$this->assertTrue( $config['wgCirrusSearchCompletionSuggesterSubphrases']['build'] );
	}

	public function testLanguageMatrix() {
		$config = $this->loadCirrusConfig( 'production', 'enwiki', 'wiki' );
		$lists = DBList::getLists();

		foreach( $config['wgCirrusSearchLanguageToWikiMap'] as $lang => $wiki ) {
			$this->assertArrayHasKey( $wiki, $config['wgCirrusSearchWikiToNameMap'] );
			$wikiName = $config['wgCirrusSearchWikiToNameMap'][$wiki];
			$this->assertContains( $wikiName, $lists['wikipedia'] );
		}
	}

	private function loadWgConf( $wmfRealm ) {
		// Variables required for wgConf.php
		$wmfConfigDir = __DIR__ . "/../wmf-config";

		require "{$wmfConfigDir}/wgConf.php";

		// InitialiseSettings.php explicitly declares these as global, so we must too
		$GLOBALS['wmfUdp2logDest'] = 'localhost';
		$GLOBALS['wmfDatacenter'] = 'unittest';
		$GLOBALS['wmfMasterDatacenter'] = 'unittest';
		$GLOBALS['wmfRealm'] = $wmfRealm;
		$GLOBALS['wmfConfigDir'] = $wmfConfigDir;
		$GLOBALS['wgConf'] = $wgConf;

		require __DIR__ . '/TestServices.php';
		require "{$wmfConfigDir}/InitialiseSettings.php";

		return $wgConf;
	}

	private function loadCirrusConfig( $wmfRealm, $wgDBname, $dbSuffix ) {
		global $wmfRealm;
		$wmfConfigDir = __DIR__ . "/../wmf-config";
		require __DIR__ . '/TestServices.php';
		$wgConf = $this->loadWgConf( $wmfRealm );

		list( $site, $lang ) = $wgConf->siteFromDB( $wgDBname );
		$wikiTags = DatabaseLists::getTagsListsFor( $wgDBname, $wmfRealm );

		$dbSuffix = ( $site === 'wikipedia' ) ? 'wiki' : $site;
		$confParams = [
			'lang'    => $lang,
			'docRoot' => $_SERVER['DOCUMENT_ROOT'],
			'site'    => $site,
			'stdlogo' => "//" ,
		];
		// Add a per-language tag as well
		$wikiTags[] = $wgConf->get( 'wgLanguageCode', $wgDBname, $dbSuffix, $confParams, $wikiTags );
		$globals = $wgConf->getAll( $wgDBname, $dbSuffix, $confParams, $wikiTags );
		extract( $globals );

		// variables that would have been setup elsewhere, perhaps in mediawiki
		// default settings or by CommonSettings.php, or by CirrusSearch.php,
		// but none of those are a part of this repository
		$wgCirrusSearchRescoreProfiles = array();
		$wgCirrusSearchRescoreFunctionScoreChains = array();
		$wgCirrusSearchFullTextQueryBuilderProfiles = array();
		$wgCirrusSearchMaxShardsPerNode = [];
		$wgJobTypeConf = array( 'default' => array() );
		$wgCirrusSearchWeights = array();
		$wgCirrusSearchNamespaceWeights = array();
		$wmfSwiftEqiadConfig = array(
			'cirrusAuthUrl' => '',
			'cirrusUser' => '',
			'cirrusKey' => '',
		);
		$wmfDatacenter = 'unittest';
		$wgCirrusSearchPoolCounterKey = 'unittest:poolcounter:blahblahblah';
		// not used for anything, just to prevent undefined variable
		$IP = '/dev/null';

		require "{$wmfConfigDir}/CirrusSearch-common.php";

		return compact( array_keys( get_defined_vars() ) );
	}

	private static function resolveConfig( $config, $key ) {
		if ( isset( $config[$key] ) ) {
			return $config[$key];
		}
		$key = '+' . $key;
		if ( isset( $config[$key] ) ) {
			return $config[$key] + $config['default'];
		}
		return $config['default'];
	}

	private static function resolveClusterConfig( $config, $clusterName ) {
		if ( isset( $config[$clusterName] ) ) {
			return $config[$clusterName];
		}
		return $config;
	}

	public function providePerClusterShardsAndReplicas() {
		$wgConf = $this->loadWgConf( 'unittest' );
		$shards = $wgConf->settings['wmgCirrusSearchShardCount'];
		$replicas = $wgConf->settings['wmgCirrusSearchReplicas'];
		$maxShardPerNode = $wgConf->settings['wmgCirrusSearchMaxShardsPerNode'];
		$wikis = array_merge( array_keys( $shards ), array_keys( $replicas ), array_keys( $maxShardPerNode ) );
		foreach ( $wikis as $idx => $wiki ) {
			if ( $wiki[0] === '+' ) {
					$wikis[$idx] = substr( $wiki, 1 );
			}
		}
		$wikis = array_unique( $wikis );
		$indexTypes = array( 'content', 'general', 'titlesuggest', 'file' );
		$clusters = array( 'eqiad' => 31, 'codfw' => 24 );

		// restrict wgConf to only the settings we care about
		$wgConf->settings = array(
			'shards' => $shards,
			'replicas' => $replicas,
			'max_shards_per_node' => $maxShardPerNode,
		);
		$tests = array();
		foreach ( $wikis as $wiki ) {
			list( $site, $lang ) = $wgConf->siteFromDB( $wiki );
			foreach ( $wgConf->suffixes as $altSite => $suffix ) {
				if ( substr( $wiki, -strlen( $suffix ) ) === $suffix ) {
					break;
				}
			}
			$config = $wgConf->getAll( $wiki, $suffix, array(
				'lang' => $lang,
				'docRoot' => '/dev/null',
				'site' => $site,
				'stdlogo' => 'file://dev/null',
			) );
			foreach ( $indexTypes as $indexType ) {
				// only commonswiki has the file index
				if ( $indexType === 'file' && $wiki !== 'commonswiki' ) {
					continue;
				}
				// wikidata doesn't have completion suggester
				if ( $wiki === 'wikidatawiki' && $indexType === 'titlesuggest' ) {
					continue;
				}
				foreach ( $clusters as $clusterName => $numServers ) {
					$tests["$clusterName {$wiki}_{$indexType}"] = array(
						$wiki,
						$indexType,
						$numServers,
						self::resolveClusterConfig( $config['shards'], $clusterName ),
						self::resolveClusterConfig( $config['replicas'], $clusterName ),
						self::resolveClusterConfig( $config['max_shards_per_node'], $clusterName ),
					);
				}
			}
		}

		return $tests;
	}

	/**
	 * @dataProvider providePerClusterShardsAndReplicas
	 */
	public function testShardAndReplicasCountsAreSane( $wiki, $indexType, $numServers, $shards, $replicas, $totalShardPernode ) {
		$primaryShards = $shards[$indexType];
		// parse 0-3 into 3
		$pieces = explode( '-', $replicas[$indexType] );
		$numReplicas = end( $pieces );

		// +1 is for the primary.
		$totalShards = $primaryShards * (1 + $numReplicas);

		if ( array_key_exists( $indexType, $totalShardPernode ) ) {
			$this->assertLessThanOrEqual( $numServers * $totalShardPernode[$indexType], $totalShards );
		}

		// For our busiest wikis we want to make sure we are using most of the
		// cluster for the indices. This was guesstimated by running the following query
		// in hive and choosing wikis with > 100M queries/week:
		//   select wikiid, count(1) as count from wmf_raw.cirrussearchrequestset where year = 2016
		//   and month = 1 and day >= 2 and day < 9 group by wikiid order by count desc limit 10;
		$busyWikis = array( 'enwiki', 'dewiki' );
		if ( in_array( $wiki, $busyWikis ) && $indexType == 'content' ) {

			// For busy indices ensure we are using most of the cluster to serve them
			$this->assertGreaterThanOrEqual( $numServers - 3, $totalShards );
		}
	}

	public static function provideConfigByLanguage() {
		return [
			'zhwiki' => [ 'zhwiki', 'wiki',
				[
					'wmgCirrusSearchSimilarityProfile' => 'default',
					'wmgCirrusSearchRescoreProfile' => 'classic',
					'wmgCirrusSearchFullTextQueryBuilderProfile' => 'default',
				],
			],
			'zh_min_nanwikisource' => [ 'zh_min_nanwikisource', 'wikisource',
				[
					'wmgCirrusSearchSimilarityProfile' => 'default',
					'wmgCirrusSearchRescoreProfile' => 'classic',
					'wmgCirrusSearchFullTextQueryBuilderProfile' => 'default',
				],
			],
			'zh_classicalwiki' => [ 'zh_classicalwiki', 'wiki',
				[
					'wmgCirrusSearchSimilarityProfile' => 'default',
					'wmgCirrusSearchRescoreProfile' => 'classic',
					'wmgCirrusSearchFullTextQueryBuilderProfile' => 'default',
				],
			],
			'thwiktionary' => [ 'thwiktionary', 'wiktionary',
				[
					'wmgCirrusSearchSimilarityProfile' => 'default',
					'wmgCirrusSearchRescoreProfile' => 'classic',
					'wmgCirrusSearchFullTextQueryBuilderProfile' => 'default',
				],
			],
			'zh_yuewiki' => [ 'zh_yuewiki', 'wiki',
				[
					'wmgCirrusSearchSimilarityProfile' => 'default',
					'wmgCirrusSearchRescoreProfile' => 'classic',
					'wmgCirrusSearchFullTextQueryBuilderProfile' => 'default',
				],
			],
			'enwiki' => [ 'enwiki', 'wiki',
				[
					'wmgCirrusSearchSimilarityProfile' => 'wmf_defaults',
					'wmgCirrusSearchRescoreProfile' => 'wsum_inclinks_pv',
					'wmgCirrusSearchFullTextQueryBuilderProfile' => 'perfield_builder',
				],
			],
			'frwiktionary' => [ 'frwiktionary', 'wiktionary',
				[
					'wmgCirrusSearchSimilarityProfile' => 'wmf_defaults',
					'wmgCirrusSearchRescoreProfile' => 'wsum_inclinks',
					'wmgCirrusSearchFullTextQueryBuilderProfile' => 'perfield_builder',
				],
			],
		];
	}
	/**
	 * @dataProvider provideConfigByLanguage
	 */
	public function testConfigByLanguage( $wiki, $type, array $expectedConfValues ) {
		$config = $this->loadCirrusConfig( 'production', $wiki, $type );
		foreach( $expectedConfValues as $key => $val ) {
			$this->assertEquals( $config[$key], $val );
		}
	}
}
