<?php

/**
 * @covers wmf-config/docroot/noc/conf/highlight.php
 */
class NocHighlightTest extends PHPUnit\Framework\TestCase {
	private $nocConfDir;
	/**
	 * @var string[] names of files created, so that they can be removed on tearDown
	 */
	private $created = [];

	protected function setUp(): void {
		parent::setUp();

		$common = dirname( dirname( __DIR__ ) );
		$configDir = "$common/wmf-config";
		$this->nocConfDir = "$common/docroot/noc/conf";

		// Created various files to test with
		if ( !file_exists( "$common/private/PrivateSettings.php" ) ) {
			$this->created[] = "$common/private/PrivateSettings.php";
			file_put_contents( "$common/private/PrivateSettings.php", '<?php $forbiddenFruit = "p";' );
		}

		$this->created[] = "$configDir/ExampleInvalid.php";
		file_put_contents( "$configDir/ExampleInvalid.php", '<?php $forbiddenFruit = "x";' );

		$this->created[] = "{$this->nocConfDir}/ExampleContent.php.txt";
		file_put_contents( "{$this->nocConfDir}/ExampleContent.php.txt", '<?php forbiddenFruit = "content";' );

		$this->created[] = "{$this->nocConfDir}/ExampleContent.txt";
		file_put_contents( "{$this->nocConfDir}/ExampleContent.txt", 'forbiddenFruit=txt-content' );

		$this->created[] = "{$this->nocConfDir}/ExampleContent";
		file_put_contents( "{$this->nocConfDir}/ExampleContent", 'forbiddenFruit=content' );
	}

	protected function tearDown(): void {
		foreach ( $this->created as $created ) {
			unlink( $created );
		}
		parent::tearDown();
	}

	public static function provideValidCases() {
		return [
			[ 'langlist', 'zh-classical', 'From root, without extension' ],
			[ 'dblists/all.dblist', 'enwiki', 'From root, dblist file' ],
			[ 'CommonSettings.php', 'Do not put private data here.', 'From wmf-config, php file' ],
		];
	}

	/**
	 * @dataProvider provideValidCases
	 */
	public function testValidCases( $q, $expect, $msg ) {
		$this->assertStringContainsString(
			$expect,
			$this->runHighlight( $q ),
			"file=$q should work ($msg)"
		);
	}

	public static function provideInvalidCases() {
		return [
			[ 'search-redirect.php' ],
			[ 'robots.txt' ],
			[ 'README' ],
			[ 'private/PrivateSettings.php' ],
			[ 'wmf-config/PrivateSettings.php' ],
			[ 'wmf-config/ExampleFile.php' ],
			[ 'PrivateSettings.php' ],
			[ 'ExampleInvalid.php' ],
			[ 'ExampleContent.txt' ],
		];
	}

	/**
	 * @dataProvider provideInvalidCases
	 */
	public function testInvalidCases( $q, $expect = 'Invalid filename given' ) {
		$this->assertStringContainsString(
			$expect,
			$this->runHighlight( $q ),
			"file=$q should not work"
		);
	}

	/**
	 * @param string $q value of file parameter to set
	 * @return string Page output
	 */
	protected function runHighlight( $q ) {
		$_GET = [
			'file' => $q
		];
		$_SERVER['REQUEST_URI'] = '/conf/highlight.php?file=' . $q;
		try {
			ob_start();
			require $this->nocConfDir . '/highlight.php';
			$out = ob_get_clean();
		} finally {
			// make sure we never pollute the global namespace
			unset( $_GET );
			unset( $_SERVER['REQUEST_URI'] );
		}
		return $out;
	}
}
