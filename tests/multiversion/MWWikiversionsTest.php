<?php

use Wikimedia\MWConfig\WmfConfig;

class MWWikiversionsTest extends PHPUnit\Framework\TestCase {

	/**
	 * @coversNothing
	 */
	public function testWikiversionsFileComplete() {
		$wikiversions = MWWikiversions::readWikiVersionsFile( __DIR__ . '/../../wikiversions.json' );
		$allDbs = WmfConfig::readDbListFile( 'all' );

		$missingVersionKeys = array_diff( $allDbs, array_keys( $wikiversions ) );
		$this->assertEquals( [], $missingVersionKeys );
	}
}
