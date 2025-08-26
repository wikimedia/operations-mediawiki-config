<?php
use Wikimedia\MWConfig\WmfConfig;

/**
 * @covers \Wikimedia\MWConfig\WmfConfig
 */
class WmfConfigTest extends PHPUnit\Framework\TestCase {

	public function testInheritance() {
		$inputSettings = [
			'wgLanguageCode' =>
			[
				'default' => 'wgLanguageCodeHasNotBeenSet!',
				'enwiki' => 'en',
				'frwiktionary' => 'fr',
			],
			'foo' => [
				'default' => 'foo_value',
			],
			'bar' => [
				'default' => 'bar_value',
				'enwiki' => 'bar_value_enwiki',
				'frwiktionary' => 'bar_value_frwiktionary',
			],
			'baz' => [
				'default' => 'baz_value',
				'wikipedia' => 'baz_value_wikipedia',
				'wiktionary' => 'baz_value_wiktionary',
				'enwiki' => 'baz_value_enwiki',
				'frwiktionary' => 'baz_value_frwiktionary',
			],
			'bang' => [
				'wikipedia' => 'bang_value_wikipedia',
				'wiktionary' => 'bang_value_wiktionary',
			],
			'boom' => [
				'enwiki' => 'boom_value_enwiki',
			],
		];
		$conf = new SiteConfiguration();
		$conf->suffixes = WmfConfig::SUFFIXES;
		$conf->settings = $inputSettings;

		$calculatedSettings_enwiki = WmfConfig::getConfigGlobals(
			'enwiki', $conf, 'production'
		);

		$calculatedSettings_frwikt = WmfConfig::getConfigGlobals(
			'frwiktionary', $conf, 'production'
		);

		$this->assertEquals(
			'foo_value',
			$calculatedSettings_enwiki['foo'],
			"Values only set to 'default' inherit that value."
		);
		$this->assertEquals(
			'foo_value',
			$calculatedSettings_frwikt['foo'],
			"Values only set to 'default' inherit that value."
		);

		$this->assertEquals(
			'bar_value_enwiki',
			$calculatedSettings_enwiki['bar'],
			"Values set directly and to 'default' use the direct value."
		);
		$this->assertEquals(
			'bar_value_frwiktionary',
			$calculatedSettings_frwikt['bar'],
			"Values set directly and to 'default' use the direct value."
		);

		$this->assertEquals(
			'baz_value_enwiki',
			$calculatedSettings_enwiki['baz'],
			"Values set directly, to 'wikipedia', and to 'default' use the direct value."
		);
		$this->assertEquals(
			'baz_value_frwiktionary',
			$calculatedSettings_frwikt['baz'],
			"Values set directly, to 'wiktionary', and to 'default' use the direct value."
		);

		$this->assertEquals(
			'bang_value_wikipedia',
			$calculatedSettings_enwiki['bang'],
			"Values set to 'wikipedia' and not to 'default' inherit correctly."
		);
		$this->assertEquals(
			'bang_value_wiktionary',
			$calculatedSettings_frwikt['bang'],
			"Values set to 'wiktionary' and not to 'default' inherit correctly."
		);

		$this->assertEquals(
			'boom_value_enwiki',
			$calculatedSettings_enwiki['boom'],
			"Directly-set values apply."
		);
		$this->assertNull(
			$calculatedSettings_frwikt['boom'] ?? null,
			"Settings neither set nor inherited are null."
		);
	}

	public function testEvalDbExpressionBasic() {
		$allDbs = WmfConfig::readDbListFile( 'all' );
		$allPrivateDbs = WmfConfig::readDbListFile( 'private' );
		$exprDbs = WmfConfig::evalDbExpressionForCli( 'all - private' );
		$expectedDbs = array_diff( $allDbs, $allPrivateDbs );
		sort( $exprDbs );
		sort( $expectedDbs );
		$this->assertEquals( $expectedDbs, $exprDbs );
	}

	public function testEvalDbExpressionAdvanced() {
		$this->assertEquals(
			[ 'enwiki' ],
			WmfConfig::evalDbExpressionForCli( '../../../dblists/s1.dblist' )
		);
		$this->assertEquals(
			[ 'commonswiki' ],
			WmfConfig::evalDbExpressionForCli( '/some/path/to/dblists/s4.dblist & large' )
		);
		$this->assertEquals(
			[ 'commonswiki', 'enwiki' ],
			WmfConfig::evalDbExpressionForCli( 'group0 + group1.dblist + /a/path/group2.dblist & /else/where/s1.dblist + /some/path/to/dblists/s4.dblist & large' ),
			'tokens are evaluated from left to right'
		);
	}

	public function testEvalDbExpressionNotFound() {
		$this->expectExceptionMessage( 'Unable to read whatever' );
		WmfConfig::evalDbExpressionForCli( 's1 + whatever' );
	}
}
