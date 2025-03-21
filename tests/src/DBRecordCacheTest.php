<?php
use PHPUnit\Framework\TestCase;
use Wikimedia\MWConfig\DBRecordCache;
use Wikimedia\MWConfig\DNSSRVRecord;

/**
 * @covers \Wikimedia\MWConfig\DBRecordCache
 */
class DBRecordCacheTest extends TestCase {
	/** @var DBRecordCache */
	private $cache;
	/** @var DBRecordCache */
	private $cacheRepopulate;

	public function setUp(): void {
		// Create a mock of the DBRecordCache class
		// Here we just mock the network calls, so we can test the cache logic
		$this->cache = $this->getMockBuilder( DBRecordCache::class )
			->disableOriginalConstructor()
			->onlyMethods( [ 'resolveSRV', 'getInstance' ] )
			->getMock();
		$this->cache->expects( $this->any() )
			->method( 'resolveSRV' )
			->willReturn( [
				[
					'target' => 'target.example.com',
					'port' => 3306,
					'pri' => 10,
					'weight' => 5,
					'ttl' => 3600
				]
			] );
		$this->cache->expects( $this->any() )
			->method( 'getInstance' )
			->willReturnSelf();
		$this->cache->reset();

		// This is another mock of DBRecordCache, but it returns a mock of DNSSRVRecord
		// from the fetch method, so we can test the db repopulation logic
		$record = $this->getMockBuilder( DNSSRVRecord::class )
			->setConstructorArgs( [ 'target.example.com', 3306, 10, 5, 3600 ] )
			->onlyMethods( [ 'getHostByName' ] )
			->getMock();

		$record->expects( $this->any() )
			->method( 'getHostByName' )
			->with( 'target.example.com' )
			->willReturn( '192.0.2.1' );
		$this->cacheRepopulate = $this->getMockBuilder( DBRecordCache::class )
			->disableOriginalConstructor()
			->onlyMethods( [ 'fetch', 'getInstance' ] )
			->getMock();
		$this->cacheRepopulate->expects( $this->any() )
			 ->method( 'fetch' )
			 ->willReturn( [ $record ] );

		$this->cacheRepopulate->expects( $this->any() )
			 ->method( 'getInstance' )
			 ->willReturnSelf();
		 $this->cacheRepopulate->reset();
	}

	public function testCacheSingleton() {
		$cache1 = DBRecordCache::getInstance();
		$cache2 = DBRecordCache::getInstance();
		$this->assertSame( $cache1, $cache2 );
	}

	public function testCacheUpdate() {
		$section = 'test-section';

		$this->assertTrue( $this->cache->update( $section ) );
		$records = $this->cache->get( $section );
		$this->assertCount( 1, $records );
		$this->assertEquals( 'target.example.com:3306', $records[0]->getInstanceLabel() );
		$this->assertFalse( $this->cache->needsUpdate( $section ) );
	}

	public function testRepopulateDbConf() {
		$lbFactoryConf = [
			'sectionLoads' => [
				'DEFAULT' => [
					'master' => 0,
					'replica' => 12
				]
			],
			'groupLoadsBySection' => [
				'DEFAULT' => [
					'group1' => [ 'db1' => 0 ],
					'group2' => [ 'db2' => 0, 'db3' => 0 ]
				]
			],
			'hostsByName' => []
		];
		$this->cacheRepopulate->setDefaultSectionName( 'test-section' );
		$this->cacheRepopulate->repopulateDbConf( $lbFactoryConf );
		$this->assertEquals( '192.0.2.1:3306', $lbFactoryConf['hostsByName']['target.example.com:3306'] );

		$this->assertEquals( [ 'master' => 0, 'target.example.com:3306' => 5 ], $lbFactoryConf['sectionLoads']['DEFAULT'] );
	}
}
