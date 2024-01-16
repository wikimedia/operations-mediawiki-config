<?php

use PHPUnit\Framework\TestCase;

class MobileFrontendTest extends TestCase {

	public static function setUpBeforeClass(): void {
		parent::setUpBeforeClass();
		require_once __DIR__ . '/../wmf-config/MobileUrlCallback.php';
	}

	/**
	 * Tests for the mobile domain URL generation ($wgMobileUrlCallback)
	 * @dataProvider provideMobileUrlCallback
	 * @covers wmfMobileUrlCallback
	 *
	 * @param string $desktopDomain
	 * @param string $expectedMobileDomain
	 * @return void
	 */
	public function testMobileUrlCallback( $desktopDomain, $expectedMobileDomain ) {
		$mobileDomain = wmfMobileUrlCallback( $desktopDomain );
		$this->assertSame( $expectedMobileDomain, $mobileDomain );
	}

	public function provideMobileUrlCallback() {
		return [
			// desktop domain, expected mobile domain
			[ 'en.wikipedia.org', 'en.m.wikipedia.org' ],
			[ 'www.wikidata.org', 'm.wikidata.org' ],
			[ 'wikisource.org', 'm.wikisource.org' ],
			[ 'wikitech.wikimedia.org', 'wikitech.wikimedia.org' ],
			[ 'en.wikipedia.beta.wmflabs.org', 'en.m.wikipedia.beta.wmflabs.org' ],
			[ 'wikidata.beta.wmflabs.org', 'm.wikidata.beta.wmflabs.org' ],
			[ 'wikifunctions.beta.wmflabs.org', 'm.wikifunctions.beta.wmflabs.org' ],
		];
	}

}
