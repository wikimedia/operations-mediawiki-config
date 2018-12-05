<?php

use Wikimedia\MWConfig\XWikimediaDebug;

require_once __DIR__ . '/../../src/XWikimediaDebug.php';

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
	 * Test of XWikimediaDebug::getOption
	 *
	 * @dataProvider provider
	 */
	public function testGetOption( $input, $expected ) {
		$expected += [
			'profile' => null,
			'readonly' => null,
			'log' => null,
			'forceprofile' => null,
		];
		$xwd = new XWikimediaDebug( $input );
		foreach ( $expected as $key => $value ) {
			$this->assertSame( $value, $xwd->getOption( $key ) );
		}
	}

	/**
	 * Test of XWikimediaDebug::hasOption
	 *
	 * @dataProvider provider
	 */
	public function testHasOption( $input, $expected ) {
		$expected += [
			'profile' => false,
			'readonly' => false,
			'log' => false,
			'forceprofile' => false,
		];
		$xwd = new XWikimediaDebug( $input );
		foreach ( $expected as $key => $value ) {
			$this->assertSame( $value !== false, $xwd->hasOption( $key ) );
		}
	}
}
