<?php

class NocConfHighlightTest extends PHPUnit\Framework\TestCase {
	private $created = [];

	protected function setUp() {
		parent::setUp();

		$common = dirname( dirname( __DIR__ ) );
		$wmfConfigDir = "$common/wmf-config";
		$this->nocConfDir = "$common/docroot/noc/conf";

		// Created various files to test with
		if ( !file_exists( "$common/private/PrivateSettings.php" ) ) {
			$this->created[] = "$common/private/PrivateSettings.php";
			if ( !is_dir( "$common/private" ) ) {
				mkdir( "$common/private" );
				$this->created[] = "$common/private";
			}
			file_put_contents( "$common/private/PrivateSettings.php", '<?php $forbiddenFruit = "p";' );
		}

		$this->created[] = "$wmfConfigDir/ExampleValid.php";
		file_put_contents( "$wmfConfigDir/ExampleValid.php", '<?php $smoigel = "v";' );

		$this->created[] = "$wmfConfigDir/ExampleInvalid.php";
		file_put_contents( "$wmfConfigDir/ExampleInvalid.php", '<?php $forbiddenFruit = "x";' );

		$this->created[] = "{$this->nocConfDir}/ExampleContent.php.txt";
		file_put_contents( "{$this->nocConfDir}/ExampleContent.php.txt", '<?php forbiddenFruit = "content";' );

		$this->created[] = "{$this->nocConfDir}/ExampleContent.txt";
		file_put_contents( "{$this->nocConfDir}/ExampleContent.txt", 'forbiddenFruit=txt-content' );

		$this->created[] = "{$this->nocConfDir}/ExampleContent";
		file_put_contents( "{$this->nocConfDir}/ExampleContent", 'forbiddenFruit=content' );

		$this->created[] = "{$this->nocConfDir}/ExampleValid.php.txt";
		symlink( "$wmfConfigDir/ExampleValid.php", "{$this->nocConfDir}/ExampleValid.php.txt" );
	}

	protected function tearDown() {
		foreach ( $this->created as $created ) {
			if ( !is_dir( $created ) ) {
				unlink( $created );
			} else {
				rmdir( $created );
			}
		}
		parent::tearDown();
	}

	public static function provideValidCases() {
		return [
			[ 'langlist', 'zh-classical', 'From root, without extension' ],
			[ 'dblists/all.dblist', 'enwiki', 'From root, dblist file' ],
			[ 'ExampleValid.php', 'smoigel', 'From wmf-config, dblist file' ],
		];
	}

	/**
	 * @dataProvider provideValidCases
	 */
	public function testValidCases( $q, $expect, $msg ) {
		$this->assertContains(
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
		$this->assertContains(
			$expect,
			$this->runHighlight( $q ),
			"file=$q should not work"
		);
	}

	/** @return string Page output */
	protected function runHighlight( $q ) {
		$_GET = [
			'file' => $q
		];
		try {
			ob_start();
			require $this->nocConfDir . '/highlight.php';
			$out = ob_get_clean();
		} finally {
			// make sure we never pollute the global namespace
			unset( $_GET );
		}
		return $out;
	}
}
