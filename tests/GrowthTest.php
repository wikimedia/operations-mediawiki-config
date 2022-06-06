<?php

/**
 * Structure tests related to Growth team settings
 *
 * @covers dblists/growthexperiments.dblist
 */
class GrowthTest extends WgConfTestCase {

	/**
	 * Test that the growthexperiments dblist is accurate. This is important as
	 * the list controls PII retention, and omissions in it do not have any
	 * easy-to-notice effect.
	 */
	public function testDblist() {
		$dbList = MWWikiversions::readDbListFile( 'growthexperiments' );
		$configuration = $this->loadWgConf( 'production' );
		foreach ( $configuration->wikis as $wiki ) {
			$enabled = $configuration->get( 'wmgUseGrowthExperiments', $wiki );
			if ( $enabled ) {
				$this->assertContains( $wiki, $dbList );
			} else {
				$this->assertNotContains( $wiki, $dbList );
			}
		}
	}

}
