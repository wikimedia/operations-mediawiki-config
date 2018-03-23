<?php
/**
 * Various tests made to test Wikimedia Foundation .dblist files.
 *
 * @license GPLv2 or later
 * @author Antoine Musso <hashar at free dot fr>
 * @copyright Copyright © 2012, Antoine Musso <hashar at free dot fr>
 * @file
 */

class DbListTests extends PHPUnit\Framework\TestCase {

	public static function provideProjectDbnames() {
		$cases = [];
		foreach ( DBList::getLists() as $projectname => $databases ) {
			if ( !DBlist::isWikiProject( $projectname ) ) {
				// Skip files such as s1, private ...
				continue;
			}
			foreach ( $databases as $database ) {
				$cases[] = [
					$projectname, $database
				];
			}
		}
		return $cases;
	}

	/**
	 * Projects dblist should only contains databasenames which
	 * belongs to them.
	 *
	 * @dataProvider provideProjectDbnames
	 */
	public function testDatabaseNamesUseProjectNameAsSuffix( $projectname, $database ) {
		// Override suffix for wikipedia project
		$dbsuffix = ( $projectname === 'wikipedia' )
			? 'wiki'
			: $projectname;

		// Verifiy the databasename suffix
		$this->assertStringEndsWith( $dbsuffix, $database,
			"Database name $database lacks db suffix $dbsuffix of $projectname"
		);
	}

	public function testDblistAllContainsEverything() {
		$lists = DBList::getLists();

		// Content of all.dblist
		$all = $lists['all'];

		// dblist files that are exceptions
		$skip = [
			// No point in checking all includes itself
			'all',

			// 'all-labs' and 'flow_only_labs' are for beta.wmflabs.org only,
			// which may have wikis not (yet) in production.
			'all-labs',
			'flow-labs',
			'flow_only_labs',

			'closed',
			'deleted',
			'new_wiktionaries',
			'news',
			'private',
			'special',
			'todo',
		];

		foreach ( $lists as $dbfile => $dbnames ) {
			if ( in_array( $dbfile, $skip ) ) {
				continue;
			}

			$this->assertEquals(
				[],
				array_diff( $dbnames, $all ),
				"'{$dbfile}.dblist' contains names not in 'all.dblist'"
			);
		}
	}

	/**
	 * This test ensures that any dblists that use expressions,
	 * are either not used in production, or are pre-computed.
	 */
	public function testExpressionListsMustBeComputed() {
		// Based on DBList::getLists()
		$files = glob( dirname( __DIR__ ) . '/dblists/*.dblist' );
		$suffix = '-computed.dblist';
		// The following array should only contain dblists that:
		// 1) use expressions, and
		// 2) only exist as convenience preset for command-line usage,
		// and are NOT read by wmf-config/CommonSettings.php or otherwise
		// needed by wmf-config.
		$notUsedFromWeb = [
			'echo.dblist',
			'open.dblist',
			'group1.dblist', // FIXME: Used in wmf-config
			'group2.dblist', // FIXME: Used in wmf-config
		];
		foreach ( $files as $file ) {
			$name = basename( $file );
			if ( strpos( $name, 'labs' ) !== false
				|| in_array( $name, $notUsedFromWeb )
			) {
				continue;
			}
			if ( strpos( file_get_contents( $file ), '%%' ) !== false ) {
				$this->assertEquals(
					$suffix,
					substr( $name, -strlen( $suffix ) ),
					"Computed list '$name' must end its name with '$suffix'"
				);
			} else {
				$this->assertFalse(
					strpos( $name, 'computed' ),
					"Keyword 'computed' found in non-computed list '$name'"
				);
			}
		}
	}

	/**
	 * This test ensures that:
	 *
	 * 1. Computed lists use a specific naming convention.
	 * 2. Computed lists are up to date.
	 */
	public function testComputedListsFreshness() {
		$lists = DBList::getLists();
		foreach ( $lists as $listname => $dbnames ) {
			if ( strpos( $listname, 'computed' ) !== false ) {
				if ( strpos( $listname, 'labs' ) !== false ) {
					continue;
				}
				$suffix = '-computed';
				$expandedListName = str_replace( $suffix, '', $listname );
				$this->assertEquals(
					$suffix,
					substr( $listname, -strlen( $suffix ) ),
					"Computed list name '$listname' must end with '$suffix'"
				);
				$expandedList = MWWikiversions::readDbListFile( $expandedListName );
				$this->assertEquals(
					$expandedList,
					$dbnames,
					"Contents of '$expandedListName' must match expansion of '$listname'"
				);
			}
		}
	}

	/**
	 * @covers MWWikiversions::evalDbListExpression
	 */
	public function testEvalDbListExpression() {
		$allDbs = MWWikiversions::readDbListFile( 'all' );
		$allLabsDbs = MWWikiversions::readDbListFile( 'private' );
		$exprDbs = MWWikiversions::evalDbListExpression( 'all - private' );
		$expectedDbs = array_diff( $allDbs, $allLabsDbs );
		sort( $exprDbs );
		sort( $expectedDbs );
		$this->assertEquals( $exprDbs, $expectedDbs );
	}

	/**
	 * @note Does not support special wikis in RTL languages, luckily there are none currently
	 */
	public function testRtlDblist() {
		ini_set( 'user_agent', 'mediawiki-config tests' );
		$siteMatrix = file_get_contents( 'https://meta.wikimedia.org/w/api.php?action=sitematrix&format=json&smtype=language&smlangprop=dir%7Ccode%7Csite&smsiteprop=dbname&formatversion=2' );
		if ( !$siteMatrix ) {
			$this->fail( 'Error retrieving site matrix!' );
		}
		$siteMatrix = json_decode( $siteMatrix, true );

		$rtl = array_flip( MWWikiversions::readDbListFile( 'rtl' ) );
		$shouldBeRtl = [];

		foreach ( $siteMatrix['sitematrix'] as $key => $lang ) {
			if ( !is_numeric( $key )
				|| $lang['dir'] !== 'rtl'
			) {
				continue;
			}
			foreach ( $lang['site'] as $site ) {
				$dbname = $site['dbname'];
				if ( !isset( $rtl[$dbname] ) ) {
					$shouldBeRtl[] = $dbname;
				}
				unset( $rtl[$dbname] );
			}
		}
		$this->assertEquals( [], array_keys( $rtl ), 'All entries in rtl.dblist should correspond to RTL wikis' );
		$this->assertEquals( [], $shouldBeRtl, 'All RTL wikis should be registered in rtl.dblist' );
	}
}
