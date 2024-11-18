<?php

/**
 * @covers MWMultiversion
 */
class MWMultiVersionTest extends PHPUnit\Framework\TestCase {

	protected function tearDown(): void {
		MWMultiversion::destroySingleton();
		parent::tearDown();
	}

	/**
	 * @dataProvider provideServerNameAndDocRoot
	 */
	public function testRealmFilenames( $expectedDB, $serverName, $msg = '' ) {
		$version = MWMultiversion::initializeForWiki( $serverName );

		$this->assertEquals( $expectedDB, $version->getDatabase() );
	}

	public function provideServerNameAndDocRoot() {
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
			[ 'foundationwiki', 'foundation.wikimedia.org' ],
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
			[ 'ombudsmenwiki', 'ombuds.wikimedia.org' ],
			[ 'otrs_wikiwiki', 'otrs-wiki.wikimedia.org' ],
			[ 'otrs_wikiwiki', 'vrt-wiki.wikimedia.org' ],
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
			[ 'wikifunctionswiki', 'www.wikifunctions.org' ],
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
			[ 'wikifunctionswiki', 'wikifunctions.beta.wmflabs.org' ],

			// wikis hosted on new wmcloud.org domain
			[ 'test2wiki', 'test2.wikipedia.beta.wmcloud.org' ],
			[ 'plwikivoyage', 'pl.wikivoyage.beta.wmcloud.org' ],
		];
	}

	/**
	 * @dataProvider provideInitializeFromServerData
	 */
	public function testInitializeFromServerData( $serverName, $scriptName, $pathInfo, $requestUri, $expectedDb ) {
		try {
			$multiversion = MWMultiversion::initializeFromServerData( $serverName, $scriptName, $pathInfo, $requestUri );
			$this->assertSame( $expectedDb, $multiversion->getDatabase() );
		} catch ( MWMultiVersionException $e ) {
			$this->assertFalse( $expectedDb );
		}
	}

	public function provideInitializeFromServerData() {
		return [
			[ 'en.wikipedia.org', '/w/index.php', '', '/wiki/Main_Page', 'enwiki' ],
			[ 'en.wikipedia.org', '/w/api.php', '', '/w/api.php', 'enwiki' ],
			[ 'en.wiktionary.org', '/w/index.php', '', '/wiki/Main_Page', 'enwiktionary' ],
			[ 'boardgovcom.wikimedia.org', '/w/index.php', '', '/wiki/Main_Page', 'boardgovcomwiki' ],
			[ 'en.wikipedia.beta.wmflabs.org', '/w/index.php', '', '/wiki/Main_Page', 'enwiki' ],
			[ 'example.org', '/w/index.php', '', '/wiki/Main_Page', false ],

			[ 'upload.wikimedia.org', '/w/thumb.php', '/wikipedia/commons/thumb/8/84/Example.svg/240px-Example.svg.png',
				'/wikipedia/commons/thumb/8/84/Example.svg/240px-Example.svg.png', 'commonswiki' ],
			[ 'upload.wikimedia.org', '/w/thumb.php', '/wikipedia/en/thumb/8/84/Example.svg/240px-Example.svg.png',
				'/wikipedia/en/thumb/8/84/Example.svg/240px-Example.svg.png', 'enwiki' ],
			[ 'upload.wikimedia.beta.wmflabs.org', '/w/thumb.php', '/wikipedia/en/thumb/8/84/Example.svg/240px-Example.svg.png',
				'/wikipedia/en/thumb/8/84/Example.svg/240px-Example.svg.png', 'enwiki' ],
			[ 'upload.wikimedia.org', '/w/thumb.php', '/', '/', false ],

			[ 'auth.wikimedia.org', '/w/index.php', '', '/en.wikipedia.org/wiki/Special:Userlogin', 'enwiki' ],
			[ 'auth.wikimedia.org', '/w/index.php', '', '/de.wiktionary.org/wiki/Special:Userlogin', 'dewiktionary' ],
			[ 'auth.wikimedia.beta.wmflabs.org', '/w/index.php', '',
				'/en.wikipedia.beta.wmflabs.org/wiki/Special:Userlogin', 'enwiki' ],
			[ 'auth.wikimedia.org', '/w/index.php', '', '/', false ],
		];
	}

}
