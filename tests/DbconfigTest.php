<?php

use PHPUnit\Framework\TestCase;

class DbconfigTest extends TestCase {

	public static function setUpBeforeClass(): void {
		parent::setUpBeforeClass();
		require_once __DIR__ . '/../src/etcd.php';
	}

	public function setUp(): void {
		parent::setUp();
	}

	public function tearDown(): void {
		unset(
			$GLOBALS['wmgRemoteMasterDbConfig'],
			$GLOBALS['wmgPCServers'],
			$GLOBALS['wmgMainStashServers'],
			$GLOBALS['wgDefaultExternalStore']
		);
		parent::tearDown();
	}

	/**
	 * @covers wmfApplyEtcdDBConfig
	 * @dataProvider provideApplyEtcd
	 */
	public function testApplyEtcd(
		array $dbconfig,
		array $expectedLBFC,
		array $expectedPC = [],
		array $expectedMS = [],
		array $defES = [ 'set' => [], 'expect' => [] ]
	) {
		$GLOBALS['wgDefaultExternalStore'] = $defES['set'];
		$lbFactoryConf = [];
		wmfApplyEtcdDBConfig( $dbconfig, $lbFactoryConf );

		$this->assertEquals( $expectedLBFC, $lbFactoryConf, 'wgLFactoryConf' );
		$this->assertSame( $expectedPC, $GLOBALS['wmgPCServers'] ?? null, 'wmgPCServers' );
		$this->assertSame( $expectedMS, $GLOBALS['wmgMainStashServers'] ?? null, 'wmgMainStashServers' );
		$this->assertSame( $defES['expect'], $GLOBALS['wgDefaultExternalStore'], 'wgDefaultExternalStore' );
	}

	public static function provideApplyEtcd() {
		yield 'minimal' => [
			[
				'hostsByName' => [],
				'sectionLoads' => [
					'DEFAULT' => [ [ 'db0001' => 0 ], [ 'db0002' => 20, 'db0003' => 10 ] ],
					's1' => [ [ 'db0015' => 0 ], [ 'db0016' => 20 ] ],
				],
				'groupLoadsBySection' => [],
				'externalLoads' => [
					'es4' => [ [ 'es0042' => 0 ], [ 'es0043' => 100, 'es0045' => 100 ] ],
					'es6' => [ [ 'es0061' => 100 ], [ 'es0064' => 100 ] ],
				],
				'readOnlyBySection' => [],
			],
			[
				'hostsByName' => [],
				'sectionLoads' => [
					'DEFAULT' => [ 'db0001' => 0, 'db0002' => 20, 'db0003' => 10 ],
					's1' => [ 'db0015' => 0, 'db0016' => 20 ],
				],
				'groupLoadsBySection' => [],
				'externalLoads' => [
					'cluster26' => [ 'es0042' => 0, 'es0043' => 100, 'es0045' => 100 ],
					'cluster28' => [ 'es0042' => 0, 'es0043' => 100, 'es0045' => 100 ],
					'cluster30' => [ 'es0061' => 100, 'es0064' => 100 ],
				],
				'readOnlyBySection' => [],
			],
		];
		yield 'ParserCache and MainStash' => [
			[
				'hostsByName' => [],
				'sectionLoads' => [],
				'groupLoadsBySection' => [],
				'externalLoads' => [
					'pc1' => [ [ 'pc0010' => 1 ], [] ],
					'pc2' => [ [ 'pc0020' => 1 ], [] ],
					'ms1' => [ [ 'db0110' => 1 ], [] ],
					'ms2' => [ [ 'db0120' => 1 ], [] ],
				],
				'readOnlyBySection' => [],
			],
			[
				'hostsByName' => [],
				'groupLoadsBySection' => [],
				'readOnlyBySection' => [],
			],
			[
				 'pc1' => 'pc0010',
				 'pc2' => 'pc0020',
			],
			[
				'ms1' => 'db0110',
				'ms2' => 'db0120',
			]
		];

		// This example depends on /src/etcd.php mapping cluster30 to es6
		yield 'automatically depool read-only ES cluster' => [
			[
				'hostsByName' => [],
				'sectionLoads' => [],
				'groupLoadsBySection' => [],
				'externalLoads' => [
					'es4' => [ [ 'es0042' => 0 ], [ 'es0043' => 100, 'es0045' => 100 ] ],
					'es6' => [ [ 'es0061' => 100 ], [ 'es0064' => 100 ] ],
				],
				'readOnlyBySection' => [
					'es6' => 'T395696',
				],
			],
			[
				'hostsByName' => [],
				'groupLoadsBySection' => [],
				'externalLoads' => [
					'cluster26' => [ 'es0042' => 0, 'es0043' => 100, 'es0045' => 100 ],
					'cluster28' => [ 'es0042' => 0, 'es0043' => 100, 'es0045' => 100 ],
					'cluster30' => [ 'es0061' => 100, 'es0064' => 100 ],
				],
				'readOnlyBySection' => [
					'es6' => 'T395696',
				],
			],
			[],
			[],
			[
				'set' => [ 'DB://clusterTEST1', 'DB://cluster30' ],
				'expect' => [ 'DB://clusterTEST1' ],
			]
		];
		yield 'keep ES cluster pooled if no other writable cluster' => [
			[
				'hostsByName' => [],
				'sectionLoads' => [],
				'groupLoadsBySection' => [],
				'externalLoads' => [
					'es6' => [ [ 'es0061' => 100 ], [ 'es0064' => 100 ] ],
				],
				'readOnlyBySection' => [
					'es6' => 'T395696',
				],
			],
			[
				'hostsByName' => [],
				'groupLoadsBySection' => [],
				'externalLoads' => [
					'cluster30' => [ 'es0061' => 100, 'es0064' => 100 ],
				],
				'readOnlyBySection' => [
					'es6' => 'T395696',
				],
			],
			[],
			[],
			[
				'set' => [ 'DB://cluster30' ],
				'expect' => [ 'DB://cluster30' ],
			]
		];
	}

}
