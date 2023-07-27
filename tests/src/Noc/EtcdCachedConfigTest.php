<?php
require_once __DIR__ . '/TestableCachedConfig.php';

class EtcdCachedConfigTest extends PHPUnit\Framework\TestCase {
	protected function setUp(): void {
		parent::setUp();
		putenv( 'WMF_DATACENTER=testdc' );
		$this->etcdConfig = TestableCachedConfig::getInstance();
		putenv( 'WMF_DATACENTER' );
	}

	public static function provideValidCases() {
		return [
			[ 'foo', [ 'dc' => 'bar' ] ],
			[ 'bar/zar', 'astring' ]
		];
	}

	public static function provideInvalidCases() {
		return [
			[ 'foo/bar', false ],
			[ 'bar/baz', '{"invalid": "data"}' ]
		];
	}

	/**
	 * @covers EtcdCachedConfig::getValue
	 *
	 * @dataProvider provideValidCases
	 */
	public function testValidCase( $key, $etcdPayload ) {
		// If the download succeeds, what we get from getValue should be equal to the payload.
		$this->assertEquals(
			$etcdPayload,
			$this->runEtcd( $key, self::generateEtcdResponse( $etcdPayload ) )
		);

		$this->assertCount( 1, $this->etcdConfig->calls );
		$this->assertEquals( self::generateExpectedUri( $key ), $this->etcdConfig->calls[0] );
	}

	/**
	 * Test we handle bad responses correctly
	 *
	 * @covers EtcdCachedConfig::getValue
	 *
	 * @dataProvider provideInvalidCases
	 */
	public function testBadResponse( $key, $etcdPayload ) {
		$this->assertNull( $this->runEtcd( $key, $etcdPayload ) );
	}

	/**
	 * Test that caching works
	 *
	 * @covers EtcdCachedConfig::getValue
	 */
	public function testCache() {
		$this->assertEquals(
			'astring',
			$this->runEtcd( 'test', self::generateEtcdResponse( 'astring' ) )
		);

		// Now the value has been loaded in cache, we should not call
		// etcd again, and the result should be what was cached.
		$this->assertEquals(
			'astring',
			$this->runEtcd( 'test', self::generateEtcdResponse( 'anotherstring' ) )
		);
		$this->assertCount( 0, $this->etcdConfig->calls );
	}

	private function runEtcd( $key, $response ) {
		$this->etcdConfig->resetCalls();
		$this->etcdConfig->setDownloadResults( [ $response ] );
		return $this->etcdConfig->getValue( $key );
	}

	protected static function generateEtcdResponse( $payload ) {
		$val = json_encode( [ 'val' => $payload ] );
		return json_encode( [ 'node' => [ 'value' => $val ] ] );
	}

	protected static function generateExpectedUri( $key ) {
		return 'https://test.local:2379/v2/keys/conftool/v1/mediawiki-config/' . $key;
	}
}
