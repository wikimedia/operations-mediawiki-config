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
	function testRealmFilenames( $expectedDB, $serverName, $docRoot = '', $msg = '' ) {
		$version = MWMultiversion::initializeForWiki( $serverName, $docRoot );

		$this->assertEquals( $expectedDB, $version->getDatabase() );
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

			array( 'advisorywiki', 'advisory.wikimedia.org', "$root/advisory" ),
			array( 'arbcom_dewiki', 'arbcom.de.wikipedia.org', "$root/wikipedia.org" ),
			array( 'arbcom_enwiki', 'arbcom.en.wikipedia.org', "$root/wikipedia.org" ),
			array( 'arbcom_fiwiki', 'arbcom.fi.wikipedia.org', "$root/wikipedia.org" ),
			array( 'arbcom_nlwiki', 'arbcom.nl.wikipedia.org', "$root/wikipedia.org" ),
			array( 'arwikimedia', 'ar.wikimedia.org', "$root/wikimedia.org" ),
			array( 'auditcomwiki', 'auditcom.wikimedia.org', "$root/auditcom" ),
			array( 'boardgovcomwiki', 'boardgovcom.wikimedia.org', "$root/boardgovcom" ),
			array( 'boardwiki', 'board.wikimedia.org', "$root/board" ),
			array( 'brwikimedia', 'br.wikimedia.org', "$root/wikimedia.org" ),
			array( 'chairwiki', 'chair.wikimedia.org', "$root/chair" ),
			array( 'chapcomwiki', 'chapcom.wikimedia.org', "$root/chapcom" ),
			array( 'checkuserwiki', 'checkuser.wikimedia.org', "$root/checkuser" ),
			array( 'collabwiki', 'collab.wikimedia.org', "$root/collab" ),
			array( 'commonswiki', 'commons.wikimedia.org', "$root/commons" ),
			array( 'donatewiki', 'donate.wikimedia.org', "$root/donate" ),
			array( 'execwiki', 'exec.wikimedia.org', "$root/exec" ),
			array( 'fdcwiki', 'fdc.wikimedia.org', "$root/fdc" ),
			array( 'foundationwiki', 'wikimediafoundation.org', "$root/foundation" ),
			array( 'grantswiki', 'grants.wikimedia.org', "$root/grants" ),
			array( 'iegcomwiki', 'iegcom.wikimedia.org', "$root/iegcom" ),
			array( 'incubatorwiki', 'incubator.wikimedia.org', "$root/incubator" ),
			array( 'internalwiki', 'internal.wikimedia.org', "$root/internal" ),
			array( 'loginwiki', 'login.wikimedia.org', "$root/login" ),
			array( 'mediawikiwiki', 'www.mediawiki.org', "$root/mediawiki" ),
			array( 'metawiki', 'meta.wikimedia.org', "$root/meta" ),
			array( 'movementroleswiki', 'movementroles.wikimedia.org', "$root/movementroles" ),
			array( 'mxwikimedia', 'mx.wikimedia.org', "$root/wikimedia.org" ),
			array( 'noboard_chapterswikimedia', 'noboard.chapters.wikimedia.org', "$root/wikimedia.org" ),
			array( 'nomcomwiki', 'nomcom.wikimedia.org', "$root/nomcom" ),
			array( 'nycwikimedia', 'nyc.wikimedia.org', "$root/wikimedia.org" ),
			array( 'officewiki', 'office.wikimedia.org', "$root/office" ),
			array( 'ombudsmenwiki', 'ombudsmen.wikimedia.org', "$root/ombudsmen" ),
			array( 'otrs_wikiwiki', 'otrs-wiki.wikimedia.org', "$root/otrs-wiki" ),
			array( 'outreachwiki', 'outreach.wikimedia.org', "$root/outreach" ),
			array( 'pa_uswikimedia', 'pa.us.wikimedia.org', "$root/wikimedia.org" ),
			array( 'qualitywiki', 'quality.wikimedia.org', "$root/quality" ),
			array( 'searchcomwiki', 'searchcom.wikimedia.org', "$root/searchcom" ),
			array( 'sourceswiki', 'wikisource.org', "$root/sources" ),
			array( 'spcomwiki', 'spcom.wikimedia.org', "$root/spcom" ),
			array( 'specieswiki', 'species.wikimedia.org', "$root/species" ),
			array( 'stewardwiki', 'steward.wikimedia.org', "$root/steward" ),
			array( 'strategywiki', 'strategy.wikimedia.org', "$root/strategy" ),
			array( 'tenwiki', 'ten.wikipedia.org', "$root/wikipedia.org" ),
			array( 'testwiki', 'test.wikipedia.org', "$root/wikipedia.org" ),
			array( 'testwikidatawiki', 'test.wikidata.org', "$root/testwikidata" ),
			array( 'transitionteamwiki', 'transitionteam.wikimedia.org', "$root/transitionteam" ),
			array( 'usabilitywiki', 'usability.wikimedia.org', "$root/usability" ),
			array( 'votewiki', 'vote.wikimedia.org', "$root/vote" ),
			array( 'vewikimedia', 've.wikimedia.org', "$root/wikimedia.org" ),
			array( 'wg_enwiki', 'wg.en.wikipedia.org', "$root/wikipedia.org" ),
			array( 'wikidatawiki', 'www.wikidata.org', "$root/wikidata" ),
			array( 'wikimania2005wiki', 'wikimania2005.wikimedia.org', "$root/wikimania2005" ),
			array( 'wikimania2006wiki', 'wikimania2006.wikimedia.org', "$root/wikimania2006" ),
			array( 'wikimania2007wiki', 'wikimania2007.wikimedia.org', "$root/wikimania2007" ),
			array( 'wikimania2008wiki', 'wikimania2008.wikimedia.org', "$root/wikimania2008" ),
			array( 'wikimania2009wiki', 'wikimania2009.wikimedia.org', "$root/wikimania2009" ),
			array( 'wikimania2010wiki', 'wikimania2010.wikimedia.org', "$root/wikimania2010" ),
			array( 'wikimania2011wiki', 'wikimania2011.wikimedia.org', "$root/wikimania2011" ),
			array( 'wikimania2012wiki', 'wikimania2012.wikimedia.org', "$root/wikimania2012" ),
			array( 'wikimania2013wiki', 'wikimania2013.wikimedia.org', "$root/wikimania2013" ),
			array( 'wikimania2014wiki', 'wikimania2014.wikimedia.org', "$root/wikimania2014" ),
			array( 'wikimaniateamwiki', 'wikimaniateam.wikimedia.org', "$root/wikimaniateam" ),

			// labs stuffs
			array( 'enwiki', 'en.wikipedia.beta.wmflabs.org', "$root/en" ),
			array( 'wikidatawiki', 'wikidata.beta.wmflabs.org', "$root/wikidata" ),
		);
	}

}
