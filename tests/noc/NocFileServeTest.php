<?php

/**
 * @covers wmf-config/docroot/noc/conf/fileserve.php
 */
class NocFileServeTest extends PHPUnit\Framework\TestCase {
	/**
	 * @var string[] names of files created, so that they can be removed on tearDown
	 */
	private $created = [];

	protected function setUp(): void {
		parent::setUp();

		$common = dirname( dirname( __DIR__ ) );
		$this->nocConfDir = "$common/docroot/noc/conf";
	}

	protected function tearDown(): void {
		parent::tearDown();
	}

	public static function provideValidCases() {
		return [
			[ '/conf/langlist', 'zh-classical', 'From root, without extension' ],
			[ '/conf/dblists/all.dblist', 'enwiki', 'From root, dblist file' ],
			[ '/conf/CommonSettings.php.txt', 'Do not put private data here.', 'From wmf-config, php file' ],
		];
	}

	/**
	 * @dataProvider provideValidCases
	 */
	public function testValidCases( $q, $expect, $msg ) {
		$this->assertStringContainsString(
			$expect,
			$this->runFileserve( $q ),
			"$q should work ($msg)"
		);
	}

	public static function provideInvalidCases() {
		return [
			[ '/conf/search-redirect.php' ],
			[ '/conf/robots.txt' ],
			[ '/conf/README' ],
			[ '/conf/index.php' ]
		];
	}

	/**
	 * @dataProvider provideInvalidCases
	 */
	public function testInvalidCases( $q, $expect = 'File not found' ) {
		$this->assertStringContainsString(
			$expect,
			$this->runFileServe( $q ),
			"$q should not work"
		);
	}

	/** Test redirects */
	public function testRedirect() {
		$this->assertEquals(
			"Redirect to /conf/langlist\n",
			$this->runFileServe( '/conf/langlist?cache=no' ),
			"Redirects should happen if there are query strings"
		);
	}

	/**
	 * @param string $q url to test
	 * @return string Page output
	 */
	protected function runFileserve( $q ) {
		$_SERVER['REQUEST_URI'] = $q;
		$_SERVER['HTTP_HOST'] = 'noc.wikimedia.org';
		try {
			ob_start();
			require $this->nocConfDir . '/fileserve.php';
			$out = ob_get_clean();
		} finally {
			// make sure we never pollute the global namespace
			unset( $_SERVER['REQUEST_URI'], $_SERVER['HTTP_HOST'] );
		}
		return $out;
	}
}
