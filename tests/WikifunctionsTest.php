<?php

use Wikimedia\MWConfig\MWConfigCacheGenerator;

/**
 * @coversNothing
 */
class WikifunctionsTest extends PHPUnit\Framework\TestCase {

	public function testCheckParsoidConfig() {
		$productionConfig = MWConfigCacheGenerator::getStaticConfig();

		$wikifunctionsClientWikis = DBList::getLists()[ 'wikifunctionsclient' ];

		$defaultSetting = $productionConfig['wgParserMigrationEnableParsoidArticlePages']['default'];

		foreach ( $wikifunctionsClientWikis as $key => $val ) {
			if ( $val === 'testwiki' ) {
				// Temporarily allowed
				continue;
			}

			$this->assertTrue(
				$productionConfig['wgParserMigrationEnableParsoidArticlePages'][$val] ?? $defaultSetting,
				'Wikis with Wikifunctions client mode enabled must be Parsoid read-mode, but "' . $val . '" is not.'
			);
		}
	}
}
