<?php
use PHPUnit\Framework\TestCase;
use Wikimedia\MWConfig\DNSSRVRecord;

/**
 * @covers \Wikimedia\MWConfig\DNSSRVRecord
 */
class DNSSRVRecordTest extends TestCase {
	/** @var DNSSRVRecord */
	private $record;

	public function setUp(): void {
		$this->record = new DNSSRVRecord( 'target.example.com', 10, 20, 30, 10 );
	}

	public function testGetInstanceLabel() {
		$this->assertEquals( 'target.example.com:10', $this->record->getInstanceLabel() );
	}

	public function testGetHostame() {
		$this->assertEquals( 'target.example.com', $this->record->getHostname() );
	}

	public function testGetIPPort() {
		$record = $this->getMockBuilder( DNSSRVRecord::class )
			->setConstructorArgs( [ 'target.example.com', 10, 20, 30, 15 ] )
			->onlyMethods( [ 'getHostByName' ] )
			->getMock();

		$record->expects( $this->once() )
			->method( 'getHostByName' )
			->with( 'target.example.com' )
			->willReturn( '192.0.2.1' );

		$this->assertEquals( '192.0.2.1:10', $record->getIPPort() );
	}

	public function testIsExpired() {
		$this->assertFalse( $this->record->isExpired() );
		$record = new DNSSRVRecord( 'example.com', 10, 20, 30, -1 );
		$this->assertTrue( $record->isExpired() );
	}

	public function testBadHostname() {
		$this->expectException( RuntimeException::class );
		$record = $this->getMockBuilder( DNSSRVRecord::class )
			->setConstructorArgs( [ 'target.example.com', 10, 20, 30, 100 ] )
			->onlyMethods( [ 'getHostByName' ] )
			->getMock();

		// Upon failure, gethostbyname() returns the hostname unmodified.
		$record->expects( $this->once() )
			->method( 'getHostByName' )
			->with( 'target.example.com' )
			->willReturn( 'target.example.com' );

		$record->getIPPort();
	}
}
