<?php
class StaticSettingsGenerationTest extends PHPUnit\Framework\TestCase {

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

		$calculatedSettings_enwiki = Wikimedia\MWConfig\MWConfigCacheGenerator::getCachableMWConfig(
			'enwiki', $inputSettings, 'production'
		);

		$calculatedSettings_frwikt = Wikimedia\MWConfig\MWConfigCacheGenerator::getCachableMWConfig(
			'frwiktionary', $inputSettings, 'production'
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
}
