<?php

require_once( __DIR__ . '/../../multiversion/MWMultiVersion.php' );

class MWMultiVersionTests extends PHPUnit_Framework_TestCase {
	/**
	 * @dataProvider provideServerNameAndDocRoot
	 */
	function testRealmFilenames( $expectedDB, $serverName, $docRoot='', $msg='' ) {
		$version = MWMultiversion::initializeForWiki($serverName, $docRoot );

		$this->assertEquals( $expectedDB, $version->getDatabase() );

		MWMultiversion::destroySingleton();
	}

	function provideServerNameAndDocRoot() {
		$root = '/usr/local/apache/common/docroot';

		return array(
			// (expected DB, server name, [doc root[, message]]
			array( 'enwiki', 'en.wikipedia.org', "$root/en" ),
			array( 'enwiki', 'en.wikipedia.beta.wmflabs.org', "$root/en" ),
			array( 'wikidatawiki', 'wikidata.beta.wmflabs.org', "$root/wikidata" ),
		);
	}

}
