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
	function testRealmFilenames( $expectedDB, $serverName, $msg = '' ) {
		$version = MWMultiversion::initializeForWiki( $serverName );

		$this->assertEquals( $expectedDB, $version->getDatabase() );
	}

	function provideServerNameAndDocRoot() {
		$root = '/usr/local/apache/common/docroot';

		return array(
			// (expected DB, server name [, message]]
			array( 'enwiki', 'en.wikipedia.org' ),
			array( 'enwiktionary', 'en.wiktionary.org' ),
			array( 'enwikibooks', 'en.wikibooks.org' ),
			array( 'enwikinews', 'en.wikinews.org' ),
			array( 'enwikiquote', 'en.wikiquote.org' ),
			array( 'enwikisource', 'en.wikisource.org' ),
			array( 'enwikiversity', 'en.wikiversity.org' ),
			array( 'enwikivoyage', 'en.wikivoyage.org' ),

			array( 'advisorywiki', 'advisory.wikimedia.org' ),
			array( 'arbcom_dewiki', 'arbcom-de.wikipedia.org' ),
			array( 'arbcom_enwiki', 'arbcom-en.wikipedia.org' ),
			array( 'arbcom_fiwiki', 'arbcom-fi.wikipedia.org' ),
			array( 'arbcom_nlwiki', 'arbcom-nl.wikipedia.org' ),
			array( 'arwikimedia', 'ar.wikimedia.org' ),
			array( 'auditcomwiki', 'auditcom.wikimedia.org' ),
			array( 'boardgovcomwiki', 'boardgovcom.wikimedia.org' ),
			array( 'boardwiki', 'board.wikimedia.org' ),
			array( 'brwikimedia', 'br.wikimedia.org' ),
			array( 'chairwiki', 'chair.wikimedia.org' ),
			array( 'chapcomwiki', 'chapcom.wikimedia.org' ),
			array( 'checkuserwiki', 'checkuser.wikimedia.org' ),
			array( 'collabwiki', 'collab.wikimedia.org' ),
			array( 'commonswiki', 'commons.wikimedia.org' ),
			array( 'donatewiki', 'donate.wikimedia.org' ),
			array( 'execwiki', 'exec.wikimedia.org' ),
			array( 'fdcwiki', 'fdc.wikimedia.org' ),
			array( 'foundationwiki', 'wikimediafoundation.org' ),
			array( 'grantswiki', 'grants.wikimedia.org' ),
			array( 'iegcomwiki', 'iegcom.wikimedia.org' ),
			array( 'incubatorwiki', 'incubator.wikimedia.org' ),
			array( 'internalwiki', 'internal.wikimedia.org' ),
			array( 'legalteamwiki', 'legalteam.wikimedia.org' ),
			array( 'loginwiki', 'login.wikimedia.org' ),
			array( 'mediawikiwiki', 'www.mediawiki.org' ),
			array( 'metawiki', 'meta.wikimedia.org' ),
			array( 'movementroleswiki', 'movementroles.wikimedia.org' ),
			array( 'mxwikimedia', 'mx.wikimedia.org' ),
			array( 'noboard_chapterswikimedia', 'noboard-chapters.wikimedia.org' ),
			array( 'nycwikimedia', 'nyc.wikimedia.org' ),
			array( 'officewiki', 'office.wikimedia.org' ),
			array( 'ombudsmenwiki', 'ombudsmen.wikimedia.org' ),
			array( 'otrs_wikiwiki', 'otrs-wiki.wikimedia.org' ),
			array( 'outreachwiki', 'outreach.wikimedia.org' ),
			array( 'pa_uswikimedia', 'pa-us.wikimedia.org' ),
			array( 'qualitywiki', 'quality.wikimedia.org' ),
			array( 'searchcomwiki', 'searchcom.wikimedia.org' ),
			array( 'sourceswiki', 'wikisource.org' ),
			array( 'spcomwiki', 'spcom.wikimedia.org' ),
			array( 'specieswiki', 'species.wikimedia.org' ),
			array( 'stewardwiki', 'steward.wikimedia.org' ),
			array( 'strategywiki', 'strategy.wikimedia.org' ),
			array( 'tenwiki', 'ten.wikipedia.org' ),
			array( 'testwiki', 'test.wikipedia.org' ),
			array( 'testwikidatawiki', 'test.wikidata.org' ),
			array( 'transitionteamwiki', 'transitionteam.wikimedia.org' ),
			array( 'usabilitywiki', 'usability.wikimedia.org' ),
			array( 'votewiki', 'vote.wikimedia.org' ),
			array( 'wg_enwiki', 'wg-en.wikipedia.org' ),
			array( 'wikidatawiki', 'www.wikidata.org' ),
			array( 'wikimania2005wiki', 'wikimania2005.wikimedia.org' ),
			array( 'wikimania2006wiki', 'wikimania2006.wikimedia.org' ),
			array( 'wikimania2007wiki', 'wikimania2007.wikimedia.org' ),
			array( 'wikimania2008wiki', 'wikimania2008.wikimedia.org' ),
			array( 'wikimania2009wiki', 'wikimania2009.wikimedia.org' ),
			array( 'wikimania2010wiki', 'wikimania2010.wikimedia.org' ),
			array( 'wikimania2011wiki', 'wikimania2011.wikimedia.org' ),
			array( 'wikimania2012wiki', 'wikimania2012.wikimedia.org' ),
			array( 'wikimania2013wiki', 'wikimania2013.wikimedia.org' ),
			array( 'wikimania2014wiki', 'wikimania2014.wikimedia.org' ),
			array( 'wikimaniateamwiki', 'wikimaniateam.wikimedia.org' ),
			array( 'zerowiki', 'zero.wikimedia.org' ),

			array( 'arwikimedia', 'ar.wikimedia.org' ),
			array( 'bdwikimedia', 'bd.wikimedia.org' ),
			array( 'bewikimedia', 'be.wikimedia.org' ),
			array( 'brwikimedia', 'br.wikimedia.org' ),
			array( 'cowikimedia', 'co.wikimedia.org' ),
			array( 'dkwikimedia', 'dk.wikimedia.org' ),
			array( 'etwikimedia', 'et.wikimedia.org' ),
			array( 'fiwikimedia', 'fi.wikimedia.org' ),
			array( 'ilwikimedia', 'il.wikimedia.org' ),
			array( 'mkwikimedia', 'mk.wikimedia.org' ),
			array( 'mxwikimedia', 'mx.wikimedia.org' ),
			array( 'nlwikimedia', 'nl.wikimedia.org' ),
			array( 'nowikimedia', 'no.wikimedia.org' ),
			array( 'nycwikimedia', 'nyc.wikimedia.org' ),
			array( 'nzwikimedia', 'nz.wikimedia.org' ),
			array( 'plwikimedia', 'pl.wikimedia.org' ),
			array( 'rswikimedia', 'rs.wikimedia.org' ),
			array( 'ruwikimedia', 'ru.wikimedia.org' ),
			array( 'sewikimedia', 'se.wikimedia.org' ),
			array( 'trwikimedia', 'tr.wikimedia.org' ),
			array( 'uawikimedia', 'ua.wikimedia.org' ),
			array( 'ukwikimedia', 'uk.wikimedia.org' ),

			// labs stuffs taken from /wikiversions-labs.dat
			array( 'aawiki', 'aa.wikipedia.beta.wmflabs.org' ),
			array( 'arwiki', 'ar.wikipedia.beta.wmflabs.org' ),
			array( 'commonswiki', 'commons.wikimedia.beta.wmflabs.org' ),
			array( 'labswiki', 'deployment.wikimedia.beta.wmflabs.org' ),
			array( 'dewiki', 'de.wikipedia.beta.wmflabs.org' ),
			array( 'dewikivoyage', 'de.wikivoyage.beta.wmflabs.org' ),

			array( 'enwiki', 'en.wikipedia.beta.wmflabs.org' ),
			array( 'en_rtlwiki', 'en-rtl.wikipedia.beta.wmflabs.org' ),
			array( 'enwikibooks', 'en.wikibooks.beta.wmflabs.org' ),
			array( 'enwikinews', 'en.wikinews.beta.wmflabs.org' ),
			array( 'enwikiquote', 'en.wikiquote.beta.wmflabs.org' ),
			array( 'enwikisource', 'en.wikisource.beta.wmflabs.org' ),
			array( 'enwikiversity', 'en.wikiversity.beta.wmflabs.org' ),
			array( 'enwikivoyage', 'en.wikivoyage.beta.wmflabs.org' ),
			array( 'enwiktionary', 'en.wiktionary.beta.wmflabs.org' ),

			array( 'eowiki', 'eo.wikipedia.beta.wmflabs.org' ),
			array( 'hewiki', 'he.wikipedia.beta.wmflabs.org' ),

			array( 'labswiki', 'deployment.wikimedia.beta.wmflabs.org' ),

			array( 'loginwiki', 'login.wikimedia.beta.wmflabs.org' ),
			array( 'metawiki', 'meta.wikimedia.beta.wmflabs.org' ),

			array( 'simplewiki', 'simple.wikipedia.beta.wmflabs.org' ),
			array( 'sqwiki', 'sq.wikipedia.beta.wmflabs.org' ),
			array( 'testwiki', 'test.wikipedia.beta.wmflabs.org' ),

			array( 'wikidatawiki', 'wikidata.beta.wmflabs.org' ),
		);
	}

}
