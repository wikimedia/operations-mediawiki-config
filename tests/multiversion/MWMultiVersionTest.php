<?php

require_once( __DIR__ . '/../../multiversion/MWMultiVersion.php' );

class MWMultiVersionTests extends PHPUnit_Framework_TestCase {
	/**
	 * @dataProvider provideServerNameAndDocRoot
	 */
	function testRealmFilenames( $expectedDB, $serverName, $docRoot = '', $msg = '' ) {
		$version = MWMultiversion::initializeForWiki( $serverName, $docRoot );

		$this->assertEquals( $expectedDB, $version->getDatabase() );

		MWMultiversion::destroySingleton();
	}

	function provideServerNameAndDocRoot() {
		$root = '/usr/local/apache/common/docroot';

		return array(
			// (expected DB, server name, [doc root[, message]]
			array( 'enwiki', 'en.wikipedia.org', "$root/wikipedia.org" ),
			array( 'enwiktionary', 'en.wiktionary.org', "$root/wiktionary.org" ),
			array( 'enwikibooks', 'en.wikibooks.org', "$root/wikibooks.org" ),
			array( 'enwikinews', 'en.wikinews.org', "$root/wikinews.org" ),
			array( 'enwikiquote', 'en.wikiquote.org', "$root/wikiquote.org" ),
			array( 'enwikisource', 'en.wikisource.org', "$root/wikisource.org" ),
			array( 'enwikiversity', 'en.wikiversity.org', "$root/wikiversity.org" ),
			array( 'enwikivoyage', 'en.wikivoyage.org', "$root/wikivoyage.org" ),

			array( 'wikidatawiki', 'wikidata.wikimedia.org', "$root/wikidata" ),
			array( 'specieswiki', 'species.wikimedia.org', "$root/species" ),
			array( 'sourceswiki', 'wikisource.org', "$root/sources" ),
			array( 'mediawikiwiki', 'www.mediawiki.org', "$root/mediawiki" ),

			array( 'wikimaniateamwiki', 'wikimaniateam.wikimedia.org', "$root/wikimaniateam" ),
			array( 'wikimania2005wiki', 'wikimania2005.wikimedia.org', "$root/wikimania2005" ),

			// labs stuffs
			array( 'enwiki', 'en.wikipedia.beta.wmflabs.org', "$root/en" ),
			array( 'wikidatawiki', 'wikidata.beta.wmflabs.org', "$root/wikidata" ),
		);
	}

}
