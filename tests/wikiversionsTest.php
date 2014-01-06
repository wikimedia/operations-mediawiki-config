<?php
/**
 *
 * @author Antoine Musso <hashar at free dot fr>
 * @copyright Copyright © 2014, Antoine "hashar" Musso
 * @copyright Copyright © 2014, Wikimedia Foundation Inc.
 * @license GPLv2
 * @file
 */

define( 'GIT_MEDIAWIKI', 'https://gerrit.wikimedia.org/r/p/mediawiki/core.git' );
define( 'GIT_REF_WMF_BRANCHES', 'refs/heads/wmf/*' );
define( 'GIT_REF_REGEX', 'refs/heads/wmf/(.*)' );

class wikiversionsTest extends PHPUnit_Framework_TestCase {

	protected static $wmfBranches = array();

	function testMultiverEnv() {
		$this->assertNotEquals( '/a/common', MULTIVER_COMMON_HOME );
	}

	/**
	 * @depends testMultiverEnv
	 */
	function testGettingWmfReferences() {
		$output = '';
		$exitCode = 0;
		exec(
			join( ' ', array(
				'git ls-remote',
				GIT_MEDIAWIKI,
				GIT_REF_WMF_BRANCHES, )
			),
			$output,
			$exitCode
		);
		$this->assertEquals( 0, $exitCode, "git ls-remote should exit 0" );
		self::$wmfBranches= preg_replace(
			'%.*\s+' .GIT_REF_REGEX . '%', '$1', $output );
	}

	/**
	 * @depends testGettingWmfReferences
	 */
	function testRefreshWikiVersion() {

		$this->assertTrue( is_dir( MULTIVER_COMMON_HOME ),
			"MULTIVER_COMMON_HOME (" . MULTIVER_COMMON_HOME . ") should exist."
		);
		foreach( self::$wmfBranches as $wmfBranch ) {
			$dir = MULTIVER_COMMON_HOME . '/php-' . $wmfBranch;
			if( ! is_dir( $dir ) ) {
				mkdir( $dir );
			}
		}

		exec( dirname( __DIR__ ) . '/multiversion/refreshWikiversionsCDB',
			$output,
			$exitCode
		);
		$this->assertEquals( 0, $exitCode,
			"refreshWikiversionsCDB should exit 0\n"
			. "Output: " . join( "\n", $output )
		);
	}
}
