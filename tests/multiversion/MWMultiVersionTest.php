<?php

require_once( __DIR__ . '/../../multiversion/MWMultiVersion.php' );

class MWMultiVersionTests extends PHPUnit_Framework_TestCase {

	protected function tearDown() {
		MWMultiversion::destroySingleton();
		parent::tearDown();
	}

	/**
	 * @dataProvider provideServerNameAndDocRoot
	 */
	function testRealmFilenames( $expectedDB, $serverName ) {
		$version = MWMultiversion::initializeForWiki( $serverName );
		$this->assertEquals( $expectedDB, $version->getDatabase() );
	}

	function provideServerNameAndDocRoot() {
		$root = '/usr/local/apache/common/docroot';

		return array(
			// (expected DB, server name
			array( 'enwiki', 'en.wikipedia.org' ),
			array( 'enwiktionary', 'en.wiktionary.org' ),
			array( 'enwikibooks', 'en.wikibooks.org' ),
			array( 'enwikinews', 'en.wikinews.org'  ),
			array( 'enwikiquote', 'en.wikiquote.org' ),
			array( 'enwikisource', 'en.wikisource.org' ),
			array( 'enwikiversity', 'en.wikiversity.org' ),
			array( 'enwikivoyage', 'en.wikivoyage.org' ),

			array( 'wikidatawiki', 'www.wikidata.org' ),
			array( 'specieswiki', 'species.wikimedia.org' ),
			array( 'sourceswiki', 'wikisource.org' ),
			array( 'mediawikiwiki', 'www.mediawiki.org' ),

			array( 'pa_uswikimedia', 'pa.us.wikimedia.org' ),

			array( 'wikimaniateamwiki', 'wikimaniateam.wikimedia.org' ),
			array( 'wikimania2005wiki', 'wikimania2005.wikimedia.org' ),

			// labs stuffs
			array( 'enwiki', 'en.wikipedia.beta.wmflabs.org' ),
			array( 'wikidatawiki', 'wikidata.beta.wmflabs.org' ),
		);
	}

}
