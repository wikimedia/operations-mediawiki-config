<?php

class NocConfHighlightTest extends PHPUnit_Framework_TestCase {
	private $created = array();

	protected function setUp() {
		parent::setUp();

		$wmfConfigDir = dirname( dirname( __DIR__ ) ) . '/wmf-config';
		$this->nocConfDir = dirname( dirname( __DIR__ ) ) . '/docroot/noc/conf';

		// Created various files to test with
		if ( !file_exists( "$wmfConfigDir/AdminSettings.php" ) ) {
			$this->created[] = "$wmfConfigDir/AdminSettings.php";
			file_put_contents( "$wmfConfigDir/AdminSettings.php", '<?php $forbiddenFruit = "a";' );
		}

		if ( !file_exists( "$wmfConfigDir/PrivateSettings.php" ) ) {
			$this->created[] = "$wmfConfigDir/PrivateSettings.php";
			file_put_contents( "$wmfConfigDir/PrivateSettings.php", '<?php $forbiddenFruit = "p";' );
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
			array( 'langlist', 'zh-classical', '' ),
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
			array( 'wmf-config/AdminSettings.php' ),
			array( 'wmf-config/PrivateSettings.php' ),
			array( 'wmf-config/ExampleFile.php' ),
			array( 'AdminSettings.php' ),
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
	public function testInvalidCases( $q, $expect = 'secretSitePassword' ) {
		$this->assertContains(
			$expect,
			$this->runHighlight( $q ),
			"file=$q should be 404"
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
