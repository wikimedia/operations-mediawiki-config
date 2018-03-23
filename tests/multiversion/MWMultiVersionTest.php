<?php

require_once __DIR__ . '/../../multiversion/MWMultiVersion.php';
require_once __DIR__ . '/../../multiversion/MWWikiversions.php';

class MWMultiVersionTests extends PHPUnit\Framework\TestCase {

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
		$root = '/srv/mediawiki/docroot';

		return [
			// (expected DB, server name [, message]]
			[ 'enwiki', 'en.wikipedia.org' ],
			[ 'enwiktionary', 'en.wiktionary.org' ],
			[ 'enwikibooks', 'en.wikibooks.org' ],
			[ 'enwikinews', 'en.wikinews.org' ],
			[ 'enwikiquote', 'en.wikiquote.org' ],
			[ 'enwikisource', 'en.wikisource.org' ],
			[ 'enwikiversity', 'en.wikiversity.org' ],
			[ 'enwikivoyage', 'en.wikivoyage.org' ],

			[ 'advisorywiki', 'advisory.wikimedia.org' ],
			[ 'arbcom_dewiki', 'arbcom-de.wikipedia.org' ],
			[ 'arbcom_enwiki', 'arbcom-en.wikipedia.org' ],
			[ 'arbcom_fiwiki', 'arbcom-fi.wikipedia.org' ],
			[ 'arbcom_nlwiki', 'arbcom-nl.wikipedia.org' ],
			[ 'arwikimedia', 'ar.wikimedia.org' ],
			[ 'auditcomwiki', 'auditcom.wikimedia.org' ],
			[ 'boardgovcomwiki', 'boardgovcom.wikimedia.org' ],
			[ 'boardwiki', 'board.wikimedia.org' ],
			[ 'brwikimedia', 'br.wikimedia.org' ],
			[ 'chairwiki', 'chair.wikimedia.org' ],
			[ 'chapcomwiki', 'chapcom.wikimedia.org' ],
			[ 'checkuserwiki', 'checkuser.wikimedia.org' ],
			[ 'collabwiki', 'collab.wikimedia.org' ],
			[ 'commonswiki', 'commons.wikimedia.org' ],
			[ 'donatewiki', 'donate.wikimedia.org' ],
			[ 'execwiki', 'exec.wikimedia.org' ],
			[ 'fdcwiki', 'fdc.wikimedia.org' ],
			[ 'foundationwiki', 'wikimediafoundation.org' ],
			[ 'grantswiki', 'grants.wikimedia.org' ],
			[ 'iegcomwiki', 'iegcom.wikimedia.org' ],
			[ 'incubatorwiki', 'incubator.wikimedia.org' ],
			[ 'internalwiki', 'internal.wikimedia.org' ],
			[ 'legalteamwiki', 'legalteam.wikimedia.org' ],
			[ 'loginwiki', 'login.wikimedia.org' ],
			[ 'mediawikiwiki', 'www.mediawiki.org' ],
			[ 'metawiki', 'meta.wikimedia.org' ],
			[ 'movementroleswiki', 'movementroles.wikimedia.org' ],
			[ 'mxwikimedia', 'mx.wikimedia.org' ],
			[ 'noboard_chapterswikimedia', 'noboard-chapters.wikimedia.org' ],
			[ 'nycwikimedia', 'nyc.wikimedia.org' ],
			[ 'officewiki', 'office.wikimedia.org' ],
			[ 'ombudsmenwiki', 'ombudsmen.wikimedia.org' ],
			[ 'otrs_wikiwiki', 'otrs-wiki.wikimedia.org' ],
			[ 'outreachwiki', 'outreach.wikimedia.org' ],
			[ 'pa_uswikimedia', 'pa-us.wikimedia.org' ],
			[ 'qualitywiki', 'quality.wikimedia.org' ],
			[ 'searchcomwiki', 'searchcom.wikimedia.org' ],
			[ 'sourceswiki', 'wikisource.org' ],
			[ 'spcomwiki', 'spcom.wikimedia.org' ],
			[ 'specieswiki', 'species.wikimedia.org' ],
			[ 'stewardwiki', 'steward.wikimedia.org' ],
			[ 'strategywiki', 'strategy.wikimedia.org' ],
			[ 'tenwiki', 'ten.wikipedia.org' ],
			[ 'testwiki', 'test.wikipedia.org' ],
			[ 'testwikidatawiki', 'test.wikidata.org' ],
			[ 'transitionteamwiki', 'transitionteam.wikimedia.org' ],
			[ 'usabilitywiki', 'usability.wikimedia.org' ],
			[ 'votewiki', 'vote.wikimedia.org' ],
			[ 'wg_enwiki', 'wg-en.wikipedia.org' ],
			[ 'wikidatawiki', 'www.wikidata.org' ],
			[ 'wikimania2005wiki', 'wikimania2005.wikimedia.org' ],
			[ 'wikimania2006wiki', 'wikimania2006.wikimedia.org' ],
			[ 'wikimania2007wiki', 'wikimania2007.wikimedia.org' ],
			[ 'wikimania2008wiki', 'wikimania2008.wikimedia.org' ],
			[ 'wikimania2009wiki', 'wikimania2009.wikimedia.org' ],
			[ 'wikimania2010wiki', 'wikimania2010.wikimedia.org' ],
			[ 'wikimania2011wiki', 'wikimania2011.wikimedia.org' ],
			[ 'wikimania2012wiki', 'wikimania2012.wikimedia.org' ],
			[ 'wikimania2013wiki', 'wikimania2013.wikimedia.org' ],
			[ 'wikimania2014wiki', 'wikimania2014.wikimedia.org' ],
			[ 'wikimaniateamwiki', 'wikimaniateam.wikimedia.org' ],
			[ 'zerowiki', 'zero.wikimedia.org' ],

			[ 'arwikimedia', 'ar.wikimedia.org' ],
			[ 'bdwikimedia', 'bd.wikimedia.org' ],
			[ 'bewikimedia', 'be.wikimedia.org' ],
			[ 'brwikimedia', 'br.wikimedia.org' ],
			[ 'cawikimedia', 'ca.wikimedia.org' ],
			[ 'cowikimedia', 'co.wikimedia.org' ],
			[ 'dkwikimedia', 'dk.wikimedia.org' ],
			[ 'etwikimedia', 'et.wikimedia.org' ],
			[ 'fiwikimedia', 'fi.wikimedia.org' ],
			[ 'ilwikimedia', 'il.wikimedia.org' ],
			[ 'mkwikimedia', 'mk.wikimedia.org' ],
			[ 'mxwikimedia', 'mx.wikimedia.org' ],
			[ 'nlwikimedia', 'nl.wikimedia.org' ],
			[ 'nowikimedia', 'no.wikimedia.org' ],
			[ 'nycwikimedia', 'nyc.wikimedia.org' ],
			[ 'nzwikimedia', 'nz.wikimedia.org' ],
			[ 'plwikimedia', 'pl.wikimedia.org' ],
			[ 'rswikimedia', 'rs.wikimedia.org' ],
			[ 'ruwikimedia', 'ru.wikimedia.org' ],
			[ 'sewikimedia', 'se.wikimedia.org' ],
			[ 'trwikimedia', 'tr.wikimedia.org' ],
			[ 'uawikimedia', 'ua.wikimedia.org' ],

			// labs stuffs taken from /wikiversions-labs.dat
			[ 'aawiki', 'aa.wikipedia.beta.wmflabs.org' ],
			[ 'arwiki', 'ar.wikipedia.beta.wmflabs.org' ],
			[ 'commonswiki', 'commons.wikimedia.beta.wmflabs.org' ],
			[ 'deploymentwiki', 'deployment.wikimedia.beta.wmflabs.org' ],
			[ 'dewiki', 'de.wikipedia.beta.wmflabs.org' ],
			[ 'dewikivoyage', 'de.wikivoyage.beta.wmflabs.org' ],

			[ 'enwiki', 'en.wikipedia.beta.wmflabs.org' ],
			[ 'en_rtlwiki', 'en-rtl.wikipedia.beta.wmflabs.org' ],
			[ 'enwikibooks', 'en.wikibooks.beta.wmflabs.org' ],
			[ 'enwikinews', 'en.wikinews.beta.wmflabs.org' ],
			[ 'enwikiquote', 'en.wikiquote.beta.wmflabs.org' ],
			[ 'enwikisource', 'en.wikisource.beta.wmflabs.org' ],
			[ 'enwikiversity', 'en.wikiversity.beta.wmflabs.org' ],
			[ 'enwikivoyage', 'en.wikivoyage.beta.wmflabs.org' ],
			[ 'enwiktionary', 'en.wiktionary.beta.wmflabs.org' ],

			[ 'eowiki', 'eo.wikipedia.beta.wmflabs.org' ],
			[ 'hewiki', 'he.wikipedia.beta.wmflabs.org' ],

			[ 'loginwiki', 'login.wikimedia.beta.wmflabs.org' ],
			[ 'metawiki', 'meta.wikimedia.beta.wmflabs.org' ],

			[ 'simplewiki', 'simple.wikipedia.beta.wmflabs.org' ],
			[ 'sqwiki', 'sq.wikipedia.beta.wmflabs.org' ],
			[ 'testwiki', 'test.wikipedia.beta.wmflabs.org' ],

			[ 'wikidatawiki', 'wikidata.beta.wmflabs.org' ],
		];
	}
}
