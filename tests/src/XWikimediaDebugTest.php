<?php

use Wikimedia\MWConfig\XWikimediaDebug;

/**
 * @covers \Wikimedia\MWConfig\XWikimediaDebug
 */
class XWikimediaDebugTest extends PHPUnit\Framework\TestCase {

	public static function provideHeader() {
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
	 * @dataProvider provideHeader
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
	 * @dataProvider provideHeader
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

	public function provideCookie() {
		$now = 1700000000;

		// expiry must be in the future and within the acceptable range
		yield 'missing expiry' => [ null, 'backend=debug1.example.net', $now,
			[ 'backend' => null ]
		];

		$expire = $now - 100;
		yield 'past expiry' => [ null, "backend=debug1.example.net; expire=$expire", $now,
			[ 'backend' => null ]
		];

		$expire = $now + 60 * 60 * 26;
		yield '26H future expiry' => [ null, "backend=debug1.example.net; expire=$expire", $now,
			[ 'backend' => null ]
		];

		$expire = $now + 60 * 60 * 22;
		yield '22H future expiry' => [ null, "backend=debug1.example.net; expire=$expire", $now,
			[ 'backend' => 'debug1.example.net' ]
		];

		yield 'spaced fields' => [ null, "backend=debug1.example.net; expire=$expire; log", $now,
			[ 'backend' => 'debug1.example.net', 'log' => true, 'readonly' => null ]
		];

		yield 'compact fields' => [ null, "backend=debug1.example.net;expire=$expire;log", $now,
			[ 'backend' => 'debug1.example.net', 'log' => true, 'readonly' => null ]
		];

		yield 'header takes priority' => [
			'backend=debug2.example.net; readonly',
			"backend=debug1.example.net, expire=$expire, log",
			$now,
			[ 'backend' => 'debug2.example.net', 'log' => null, 'readonly' => true ]
		];

		// This is how real cookie strings will look but urlencoding is ignored above for readability.
		yield 'URL encoding' => [ null, "backend%3Ddebug1.example.net%3B%20expire%3D$expire%3B%20log", $now,
			[ 'backend' => 'debug1.example.net', 'log' => true, 'readonly' => null ]
		];
	}

	/**
	 * @dataProvider provideCookie
	 */
	public function testCookie( $header, $cookie, $now, $expected ) {
		$xwd = $this->getXwdWithMockClock( $header, $cookie, $now );
		foreach ( $expected as $key => $value ) {
			$this->assertSame( $value, $xwd->getOption( $key ), $key );
		}
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
