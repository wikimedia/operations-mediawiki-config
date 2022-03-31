<?php

/**
 * Structure tests related to Growth team settings
 */
class GrowthTest extends WgConfTestCase {

	/**
	 * Test that the growthexperiments dblist is accurate. This is important as
	 * the list controls PII retention, and omissions in it do not have any
	 * easy-to-notice effect.
	 */
	public function testDblist() {
		$dbList = MWWikiversions::readDbListFile( 'growthexperiments' );
		$wgConf = $this->loadWgConf( 'production' );
		foreach ( $wgConf->wikis as $wiki ) {
			$enabled = $wgConf->get( 'wmgUseGrowthExperiments', $wiki );
			if ( $enabled ) {
				$this->assertContains( $wiki, $dbList );
			} else {
				$this->assertNotContains( $wiki, $dbList );
			}
		}
	}

}
