<?php

class ProfilerTest extends PHPUnit_Framework_TestCase {

	public static function provideParseXmlHeader() {
		yield 'no attributes' => [
			'backend=debug1.example.net',
			[],
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
	 * See https://wikitech.wikimedia.org/wiki/X-Wikimedia-Debug
	 *
	 * @dataProvider provideParseXmlHeader
	 */
	public function testParseXmdHeader( $input, $keys ) {
		$keys += [
			'profile' => false,
			'readonly' => false,
			'log' => false,
			'forceprofile' => false,
		];
		$ret = $this->parseXmlHeader( $input );
		$this->assertInternalType( 'array', $ret );
		foreach ( $keys as $key => $value ) {
			$this->assertSame( $value, isset( $ret[$key] ), $key );
		}
	}

	/**
	 * Copy of logic in wmf-config/profiler.php.
	 */
	protected function parseXmlHeader( $input ) {
		$xwd = [];
		$matches = null;
		preg_match_all( '/;\s*(\w+)/', $input, $matches );
		if ( !empty( $matches[1] ) ) {
			$xwd = array_fill_keys( $matches[1], true );
		}
		unset( $matches );
		return $xwd;
	}
}
