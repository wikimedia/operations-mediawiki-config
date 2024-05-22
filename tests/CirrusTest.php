<?php

/**
 * @covers wmf-config/CirrusSearch-common.php
 */
class CirrusTest extends WgConfTestCase {
	public function testClusterConfigurationForProdTestwiki() {
		$config = $this->loadCirrusConfig( 'production', 'testwiki', 'wiki' );
		$this->assertArrayNotHasKey( 'wgCirrusSearchServers', $config );
		$this->assertArrayHasKey( 'wgCirrusSearchClusters', $config );
		$this->assertArrayHasKey( 'wgCirrusSearchDefaultCluster', $config );
		// This is transformed from 'local' to 'unittest', but if it was set
		// to a specific cluster and not 'local' this fails.
		// $this->assertEquals( 'unittest', $config['wgCirrusSearchDefaultCluster'] );
		// (2 DCs + 1 cloudelastic) * 3 ES clusters per
		$this->assertCount( 3 * 3, $config['wgCirrusSearchClusters'] );

		// testwiki writes only via SUP, cirrus must not send writes.
		$this->assertCount( 0, $config['wgCirrusSearchWriteClusters']['default'] );
		// archives still write via cirrus
		$this->assertCount( 2, $config['wgCirrusSearchWriteClusters']['archive'] );

		foreach ( $config['wgCirrusSearchWriteClusters']['default'] as $writeCluster ) {
			$groups = $config['wgCirrusSearchReplicaGroup'];
			if ( is_array( $groups ) ) {
				$groups = $groups['groups'];
			} else {
				$groups = [ $groups ];
			}

			foreach ( $groups as $group ) {
				$replicaGroup = $group === 'default' ? '' : "-$group";
				$replicaGroup = $writeCluster . $replicaGroup;
				$this->assertArrayHasKey(
					$replicaGroup,
					$config['wgCirrusSearchClusters']
				);

				if ( $group !== 'default' ) {
					$servers = $config['wgCirrusSearchClusters'][$replicaGroup];
					$this->assertArrayHasKey( 'group', $servers );
					$this->assertEquals( $group, $servers['group'] );
				}
			}
		}
	}

	public function testClusterConfigurationForProdEnwiki() {
		$config = $this->loadCirrusConfig( 'production', 'enwiki', 'wiki' );
		$this->assertArrayNotHasKey( 'wgCirrusSearchServers', $config );
		$this->assertArrayHasKey( 'wgCirrusSearchClusters', $config );
		$this->assertArrayHasKey( 'wgCirrusSearchDefaultCluster', $config );
		// (2 DCs + 1 cloudelastic) * 3 ES clusters per
		$this->assertCount( 3 * 3, $config['wgCirrusSearchClusters'] );
		$this->assertCount( 3, $config['wgCirrusSearchShardCount'] );
		$this->assertCount( 3, $config['wgCirrusSearchReplicas'] );

		$dc_config_tested = 0;
		foreach ( $config['wgCirrusSearchClusters'] as $key => $clusterConf ) {
			$this->assertArrayHasKey( 'replica', $clusterConf );
			$this->assertArrayHasKey( 'group', $clusterConf );
			if ( $clusterConf['group'] !== 'chi' ) {
				// enwiki is chi, the test would pass but it seems
				// weird to test unrelated groups.
				continue;
			}
			$dc = $clusterConf['replica'];
			$dc_config_tested += 1;
			$this->assertArrayHasKey( $dc, $config['wgCirrusSearchShardCount'] );
			$this->assertArrayHasKey( $dc, $config['wgCirrusSearchReplicas'] );
		}
		// Test that we scanned the 2 dc's + cloudelastic for the group chi
		$this->assertEquals( 3, $dc_config_tested );
		// Writes to eqiad. cloudelastic and codfw are in sup
		$this->assertCount( 1, $config['wgCirrusSearchWriteClusters']['default'] );
		// archives still write to eqiad and codfw
		$this->assertCount( 2, $config['wgCirrusSearchWriteClusters']['archive'] );
		// Verify all the clusters we want to write to exist in configuration
		foreach ( $config['wgCirrusSearchWriteClusters'] as $updateGroup => $writeClusters ) {
			foreach ( $writeClusters as $replica ) {
				$groups = $config['wgCirrusSearchReplicaGroup'];
				if ( is_array( $groups ) ) {
					$groups = $groups['groups'];
				} else {
					$groups = [ $groups ];
				}
				foreach ( $groups as $group ) {
					$group = $config['wgCirrusSearchReplicaGroup'];
					$replicaGroup = $group === 'default' ? '' : "-$group";
					$replicaGroup = $replica . $replicaGroup;
					$this->assertArrayHasKey(
						$replicaGroup,
						$config['wgCirrusSearchClusters']
					);
				}
			}
		}
	}

	public function testPrivateWikisNotWritingToCloudElastic() {
		$config = $this->loadCirrusConfig( 'production', 'officewiki', 'wiki' );
		$this->assertEquals( [ 'eqiad', 'codfw' ],
			$config['wgCirrusSearchWriteClusters'] );
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

		$config = $this->loadCirrusConfig( 'production', 'labswiki', 'wiki' );
		$this->assertTrue( $config['wgCirrusSearchCompletionSuggesterSubphrases']['build'] );
	}

	public function testSiteMatrixCanLoad() {
		$config = $this->loadCirrusConfig( 'production', 'itwiki', 'wiki' );
		$lists = DBList::getLists();
		// Make sure that these config vars are empty so the SiteMatrix integration
		// can be loaded.
		$this->assertArrayNotHasKey( 'wgCirrusSearchInterwikiSources', $config );
		$this->assertArrayNotHasKey( 'wgCirrusSearchWikiToNameMap', $config );
	}

	private function loadCirrusConfig( $wmgRealm, $dbName, $dbSuffix ) {
		require __DIR__ . '/../private/readme.php';
		require __DIR__ . '/data/TestServices.php';

		$configuration = $this->loadWgConf( $wmgRealm );

		[ $site, $lang ] = $configuration->siteFromDB( $dbName );
		$dbSuffix = ( $site === 'wikipedia' ) ? 'wiki' : $site;
		$confParams = [
			'lang'    => $lang,
			'site'    => $site,
		];

		// Add a per-language tag as well
		$wikiTags = MWMultiversion::getTagsForWiki( $dbName, 'production' );
		$wikiTags[] = $configuration->get( 'wgLanguageCode', $dbName, $dbSuffix, $confParams, $wikiTags );

		$globals = $configuration->getAll( $dbName, $dbSuffix, $confParams, $wikiTags );

		// we want to use globals
		// phpcs:ignore MediaWiki.Usage.ForbiddenFunctions.extract
		extract( $globals );

		// variables that would have been setup elsewhere, perhaps in mediawiki
		// default settings or by CommonSettings.php
		// these correspond to the globals
		// phpcs:disable MediaWiki.VariableAnalysis.MisleadingGlobalNames
		$wgJobTypeConf = [ 'default' => [] ];
		$wmgDatacenter = 'unittest';
		$wgCirrusSearchPoolCounterKey = 'unittest:poolcounter:blahblahblah';
		// not used for anything, just to prevent undefined variable
		$IP = '/dev/null';

		require __DIR__ . '/../wmf-config/CirrusSearch-common.php';

		// we want to use globals
		// phpcs:ignore MediaWiki.Usage.ForbiddenFunctions.compact
		$ret = compact( array_keys( get_defined_vars() ) );
		return $ret;

		// phpcs:enable MediaWiki.VariableAnalysis.MisleadingGlobalNames
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
		return $config[$clusterName] ?? $config;
	}

	public function provideUserTestingBuckets() {
		$allConfig = $this->loadWgConf( 'production' );
		$conf = $allConfig->settings['wgCirrusSearchUserTesting']['default'];
		$tests = [];
		foreach ( $conf as $name => $testConfig ) {
			$tests[$name] = [ $testConfig ];
		}
		return $tests;
	}

	private function assertArrayKeys( $required, $optional, $array ) {
		$this->assertIsArray( $array );

		$found = array_keys( $array );
		$missingRequired = array_diff( $required, $found );
		$this->assertEquals( [], $missingRequired );

		$allowed = array_merge( $required, $optional );
		$extraKeys = array_diff( $found, $allowed );
		$this->assertEquals( [], $extraKeys );
	}

	/**
	 * @dataProvider provideUserTestingBuckets
	 */
	public function testUserTestingBucketConfiguration( $config ) {
		// Sanity check that they are formatted correctly.
		$this->assertArrayKeys( [ 'buckets' ], [ 'globals' ], $config );

		$triggers = [];
		foreach ( $config['buckets'] as $name => $bucketConf ) {
			$this->assertArrayKeys( [ 'trigger' ], [ 'globals' ], $bucketConf );
			$this->assertIsString( $bucketConf['trigger'] );
			$triggers[] = $bucketConf['trigger'];
		}
		$this->assertSameSize( $triggers, array_unique( $triggers ) );
	}

	public function providePerClusterShardsAndReplicas() {
		$allConfig = $this->loadWgConf( 'production' );
		$shards = $allConfig->settings['wmgCirrusSearchShardCount'];
		$replicas = $allConfig->settings['wgCirrusSearchReplicas'];
		$maxShardPerNode = $allConfig->settings['wgCirrusSearchMaxShardsPerNode'];
		$wikis = array_diff( array_merge(
			array_keys( $shards ),
			array_keys( $replicas ),
			array_keys( $maxShardPerNode )
		), [ 'default' ] );
		foreach ( $wikis as $idx => $wiki ) {
			if ( $wiki[0] === '+' ) {
					$wikis[$idx] = substr( $wiki, 1 );
			}
		}
		$wikis = array_unique( $wikis );
		$indexTypes = [ 'content', 'general', 'titlesuggest', 'file' ];
		$clusters = [ 'eqiad' => 50, 'codfw' => 50 ];

		// restrict wgConf to only the settings we care about
		$allConfig->settings = [
			'shards' => $shards,
			'replicas' => $replicas,
			'max_shards_per_node' => $maxShardPerNode,
		];
		$tests = [];
		foreach ( $wikis as $wiki ) {
			[ $site, $lang ] = $allConfig->siteFromDB( $wiki );
			foreach ( $allConfig->suffixes as $altSite => $suffix ) {
				if ( substr( $wiki, -strlen( $suffix ) ) === $suffix ) {
					break;
				}
			}
			$config = $allConfig->getAll( $wiki, $suffix, [
				'lang' => $lang,
				'site' => $site,
			] );
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
					$tests["$clusterName {$wiki}_{$indexType}"] = [
						$wiki,
						$indexType,
						$numServers,
						self::resolveClusterConfig( $config['shards'], $clusterName ),
						self::resolveClusterConfig( $config['replicas'], $clusterName ),
						self::resolveClusterConfig( $config['max_shards_per_node'], $clusterName ),
					];
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
		$totalShards = $primaryShards * ( 1 + $numReplicas );

		$this->assertGreaterThanOrEqual( 2, $numReplicas );
		$this->assertLessThanOrEqual( 3, $numReplicas );

		# Check that the index's total (primary+replica) shards fit based off perNode shard limit
		# Note that we arbitrarily subtract 2 as a hacky way to account for row awareness
		# and other possible factors that might cause shards to not schedule
		# Passing this test is not a guarantee shards will be assigned,
		# But failing it (ignoring the arbitrary -2 we added) is a guarantee they won't
		if ( array_key_exists( $indexType, $totalShardPernode ) ) {
			$this->assertLessThanOrEqual( ( $numServers * $totalShardPernode[$indexType] ) - 2, $totalShards );
		}

		// For our busiest wikis we want to make sure we are using most of the
		// cluster for the indices. In the past we
		// guesstimated this by running the following query
		// in hive and choosing wikis with > 100M queries/week:
		// select wikiid, count(1) as count from wmf_raw.cirrussearchrequestset where 'year' = 2016
		// and 'month' = 1 and 'day' >= 2 and 'day' < 9 group by wikiid order by count desc limit 10;
		// However since then we've decided to exclude dewiki, leaving just enwiki.
		$busyWikis = [ 'enwiki' ];
		if ( in_array( $wiki, $busyWikis ) && $indexType == 'content' ) {
			// For busy indices ensure we are using most of the cluster to serve them
			$this->assertGreaterThanOrEqual( $numServers - 5, $totalShards );
		}
	}

	public static function provideConfigByLanguage() {
		return [
			'zhwiki' => [ 'zhwiki', 'wiki',
				[
					'wgCirrusSearchSimilarityProfile' => 'wmf_defaults',
					'wgCirrusSearchRescoreProfile' => 'wsum_inclinks',
					'wgCirrusSearchFullTextQueryBuilderProfile' => 'perfield_builder',
					'wgCirrusSearchMaxPhraseTokens' => 10,
				],
			],
			'zh_min_nanwikisource' => [ 'zh_min_nanwikisource', 'wikisource',
				[
					'wgCirrusSearchSimilarityProfile' => 'wmf_defaults',
					'wgCirrusSearchRescoreProfile' => 'wsum_inclinks',
					'wgCirrusSearchFullTextQueryBuilderProfile' => 'perfield_builder',
				],
			],
			'enwiki' => [ 'enwiki', 'wiki',
				[
					'wgCirrusSearchSimilarityProfile' => 'wmf_defaults',
					'wgCirrusSearchRescoreProfile' => 'mlr-1024rs',
					'wgCirrusSearchFullTextQueryBuilderProfile' => 'perfield_builder',
				],
			],
			'frwiktionary' => [ 'frwiktionary', 'wiktionary',
				[
					'wgCirrusSearchSimilarityProfile' => 'wmf_defaults',
					'wgCirrusSearchRescoreProfile' => 'wsum_inclinks',
					'wgCirrusSearchFullTextQueryBuilderProfile' => 'perfield_builder',
				],
			],
		];
	}

	/**
	 * @dataProvider provideConfigByLanguage
	 */
	public function testConfigByLanguage( $wiki, $type, array $expectedConfValues ) {
		$config = $this->loadCirrusConfig( 'production', $wiki, $type );
		foreach ( $expectedConfValues as $key => $val ) {
			$this->assertEquals( $config[$key], $val );
		}
	}
}
