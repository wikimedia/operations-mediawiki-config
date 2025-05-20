<?php

use Wikimedia\MWConfig\WmfConfig;

/**
 * @coversNothing
 */
class WikifunctionsTest extends PHPUnit\Framework\TestCase {

	public function testCheckParsoidConfig() {
		$productionConfig = WmfConfig::getStaticConfig();

		$wikifunctionsClientWikis = DBList::getLists()[ 'wikifunctionsclient' ];

		$defaultSetting = $productionConfig['wgParserMigrationEnableParsoidArticlePages']['default'];
		$dbListSetting = $productionConfig['wgParserMigrationEnableParsoidArticlePages']['parsoidrendered'];

		foreach ( $wikifunctionsClientWikis as $key => $val ) {
			if ( $val === 'testwiki' ) {
				// Temporarily allowed
				continue;
			}

			if ( DBList::isInDblist( $val, 'parsoidrendered' ) ) {
				// This wiki is in the parsoidrendered dblist, so check that setting
				$this->assertTrue(
					$dbListSetting,
					'Wikis with Wikifunctions client mode enabled must be Parsoid read-mode, but "' . $val . '" is in parsoidrendered but it is not enabled.'
				);
			} else {
				// This wiki is not in the parsoidrendered dblist, so check its direct setting (or default)
				$actualSetting = $productionConfig['wgParserMigrationEnableParsoidArticlePages'][$val] ?? $defaultSetting;
				$this->assertTrue(
					$actualSetting,
					'Wikis with Wikifunctions client mode enabled must be Parsoid read-mode, but "' . $val . '" is not.'
				);
			}
		}
	}
}
