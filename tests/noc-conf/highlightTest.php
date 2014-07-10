<?php

class NocConfHighlightTest extends PHPUnit_Framework_TestCase {
	private $created = array();

	protected function setUp() {
		parent::setUp();

		$common = dirname( dirname( __DIR__ ) );
		$wmfConfigDir =  "$common/wmf-config";
		$this->nocConfDir = "$common/docroot/noc/conf";

		// Created various files to test with
		if ( !file_exists( "$common/private/PrivateSettings.php" ) ) {
			$this->created[] = "$common/private/PrivateSettings.php";
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
			unlink( $created );
		}
		parent::tearDown();
	}

	public static function provideValidCases() {
		return array(
			array( 'langlist', 'zh-classical', 'From root, without extension' ),
			array( 'all.dblist', 'enwiki', 'From root, dblist file' ),
			array( 'ExampleValid.php', 'smoigel', 'From wmf-config, dblist file' ),
		);
	}

	/**
	 * @dataProvider provideValidCases
	 */
	public function testValidCases( $q, $expect, $msg) {
		$this->assertContains(
			$expect,
			$this->runHighlight( $q ),
			"file=$q should work ($msg)"
		);
	}

	public static function provideInvalidCases() {
		return array(
			array( 'search-redirect.php' ),
			array( 'robots.txt' ),
			array( 'README' ),
			array( 'private/PrivateSettings.php' ),
			array( 'wmf-config/PrivateSettings.php' ),
			array( 'wmf-config/ExampleFile.php' ),
			array( 'PrivateSettings.php' ),
			array( 'ExampleInvalid.php' ),
			array( 'ExampleContent.php', 'must only contain symlinks' ),
			array( 'ExampleContent', 'must only contain symlinks' ),
			array( 'ExampleContent.txt' ),
		);
	}

	/**
	 * @dataProvider provideInvalidCases
	 */
	public function testInvalidCases( $q, $expect = 'No valid, whitelisted filename' ) {
		$this->assertContains(
			$expect,
			$this->runHighlight( $q ),
			"file=$q should not work"
		);
	}

	/** @return string Page output */
	protected function runHighlight( $q ) {
		$_GET = array(
			'file' => $q
		);
		ob_start();
		require( $this->nocConfDir . '/highlight.php' );
		$out = ob_get_clean();
		return $out;
	}
}
