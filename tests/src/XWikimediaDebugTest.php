<?php

use Wikimedia\MWConfig\XWikimediaDebug;

class XWikimediaDebugTest extends PHPUnit\Framework\TestCase {

	public static function provider() {
		yield 'no attributes' => [
			'backend=debug1.example.net',
			[ 'backend' => 'debug1.example.net' ],
		];
		yield 'profile attribute' => [
			'backend=debug1.example.net; profile',
			[ 'profile' => true ],
		];
		yield 'readonly attribute' => [
			'backend=debug1.example.net; readonly',
			[ 'readonly' => true ],
		];
		yield 'log attribute' => [
			'backend=debug1.example.net; log',
			[ 'log' => true ],
		];
		yield 'readonly and log' => [
			'backend=debug1.example.net; readonly; log',
			[ 'readonly' => true, 'log' => true ],
		];
		yield 'all three' => [
			'backend=debug1.example.net; profile; log; readonly',
			[ 'profile' => true, 'readonly' => true, 'log' => true ],
		];
	}

	/**
	 * @covers \Wikimedia\MWConfig\XWikimediaDebug::getOption
	 * @dataProvider provider
	 */
	public function testGetOption( $header, $expected ) {
		$expected += [
			'profile' => null,
			'readonly' => null,
			'log' => null,
			'forceprofile' => null,
		];
		$xwd = new XWikimediaDebug( $header, null );
		foreach ( $expected as $key => $value ) {
			$this->assertSame( $value, $xwd->getOption( $key ) );
		}
	}

	/**
	 * @covers \Wikimedia\MWConfig\XWikimediaDebug::hasOption
	 * @dataProvider provider
	 */
	public function testHasOption( $header, $expected ) {
		$expected += [
			'profile' => false,
			'readonly' => false,
			'log' => false,
			'forceprofile' => false,
		];
		$xwd = new XWikimediaDebug( $header, null );
		foreach ( $expected as $key => $value ) {
			$this->assertSame( $value !== false, $xwd->hasOption( $key ) );
		}
	}

	/**
	 * @covers \Wikimedia\MWConfig\XWikimediaDebug
	 */
	public function testCookie() {
		$present = 1700000000;

		// expire field must be present and in the acceptable range
		$xwd = $this->getXwdWithMockClock( null, 'backend=debug1.example.net', $present );
		$this->assertNull( $xwd->getOption( 'backend' ) );

		$expire = $present - 100;
		$xwd = $this->getXwdWithMockClock( null, "backend=debug1.example.net; expire=$expire", $present );
		$this->assertNull( $xwd->getOption( 'backend' ) );

		$expire = $present + 60 * 60 * 26;
		$xwd = $this->getXwdWithMockClock( null, "backend=debug1.example.net; expire=$expire", $present );
		$this->assertNull( $xwd->getOption( 'backend' ) );

		$expire = $present + 60 * 60 * 22;
		$xwd = $this->getXwdWithMockClock( null, "backend=debug1.example.net; expire=$expire", $present );
		$this->assertNotNull( $xwd->getOption( 'backend' ) );

		// cookie fields are parsed properly
		$xwd = $this->getXwdWithMockClock( null, "backend=debug1.example.net; expire=$expire; log", $present );
		$this->assertSame( 'debug1.example.net', $xwd->getOption( 'backend' ) );
		$this->assertTrue( $xwd->hasOption( 'log' ) );
		$this->assertFalse( $xwd->hasOption( 'readonly' ) );
		$xwd = $this->getXwdWithMockClock( null, "backend=debug1.example.net;expire=$expire;log", $present );
		$this->assertSame( 'debug1.example.net', $xwd->getOption( 'backend' ) );
		$this->assertTrue( $xwd->hasOption( 'log' ) );
		$this->assertFalse( $xwd->hasOption( 'readonly' ) );

		// header takes priority
		$xwd = $this->getXwdWithMockClock(
			'backend=debug2.example.net; readonly',
			"backend=debug1.example.net, expire=$expire, log",
			$present
		);
		$this->assertSame( 'debug2.example.net', $xwd->getOption( 'backend' ) );
		$this->assertTrue( $xwd->hasOption( 'readonly' ) );
		$this->assertFalse( $xwd->hasOption( 'log' ) );

		// Test URL encoding. This is how real cookie strings will look but urlencoding is ignored above for readability.
		$xwd = $this->getXwdWithMockClock( null, "backend%3Ddebug1.example.net%3B%20expire%3D$expire%3B%20log", $present );
		$this->assertSame( 'debug1.example.net', $xwd->getOption( 'backend' ) );
		$this->assertTrue( $xwd->hasOption( 'log' ) );
	}

	protected function getXwdWithMockClock( $header, $cookie, $time ): XWikimediaDebug {
		$xwd = $this->getMockBuilder( XWikimediaDebug::class )
			->onlyMethods( [ 'time' ] )
			->disableOriginalConstructor()
			->getMock();
		$xwd->method( 'time' )->willReturn( $time );
		$xwd->__construct( $header, $cookie );
		return $xwd;
	}
}
