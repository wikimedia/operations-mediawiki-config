<?php
/**
 * Various tests made to test Wikimedia Foundation .dblist files.
 *
 * @license GPL-2.0-or-later
 * @author Antoine Musso <hashar at free dot fr>
 * @copyright Copyright © 2012, Antoine Musso <hashar at free dot fr>
 * @file
 */

class DbListTest extends PHPUnit\Framework\TestCase {

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

			'deleted',
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

	public static function provideAllWikisAreIncluded() {
		return [
			'section' => [
				// If you're adding a new section, make sure it's widely announced
				// so all the people who do things per section know about it!
				[ 's1', 's2', 's3', 's4', 's5', 's6', 's7', 's8', 'wikitech', ],
			],

			'size' => [
				[ 'small', 'medium', 'large', ],
			],
		];
	}

	/**
	 * @dataProvider provideAllWikisAreIncluded
	 * @param string[] $dbLists DBList names that should collectively contain all wikis
	 */
	public function testAllWikisAreIncluded( array $dbLists ) {
		$lists = DBList::getLists();

		$all = array_fill_keys( $lists['all'], [] );

		foreach ( $dbLists as $list ) {
			foreach ( $lists[$list] as $name ) {
				$all[$name][] = $list;
			}
		}

		$all = array_filter( $all, function ( $v ) {
			return count( $v ) !== 1;
		} );

		$this->assertSame( [], $all,
			"All names in 'all.dblist' are in exactly one of the lists" );
	}

	public function testExpressionListsNaming() {
		// Based on DBList::getLists()
		$files = glob( dirname( __DIR__ ) . '/dblists/*.dblist' );

		// The following are dblists that do use expressions but aren't
		// named as "-computed" because they're only used on the
		// command-line and don't need an expanded variant for wmf-config.
		$cliOnly = [
			'echo.dblist',
			'open.dblist',
			'group1.dblist', // FIXME: Used in wmf-config
			'group2.dblist', // FIXME: Used in wmf-config
		];
		foreach ( $files as $file ) {
			$name = basename( $file );
			if ( strpos( $name, 'labs' ) !== false ) {
				continue;
			}
			if ( strpos( file_get_contents( $file ), '%%' ) !== false && !in_array( $name, $cliOnly ) ) {
				$suffix = '-computed.dblist';
				$expected = preg_replace( '/(-computed)?\.dblist$/', $suffix, $name );
				$this->assertEquals(
					$expected,
					$name,
					"Computed list '$name' must end its name with '$suffix'"
				);
			} else {
				$this->assertFalse(
					strpos( $name, 'computed' ),
					"Keyword 'computed' must be absent from non-computed list '$name'"
				);
			}
		}
	}

	/**
	 * Production code that is web-facing MUST NOT use dblists
	 * that contain expressions because these have performance cost.
	 *
	 * Assert that any dblist that uses expressions has its name end in "-computed".
	 * These may be used as-is on the command-line. If they are needed in
	 * InitialiseSettings or CommonSettings, then the expression must be
	 * expanded and saved as its own dblist file.
	 */
	public function testNoExpressionListUsedInSettings() {
		$dblists = DBList::getDblistsUsedInSettings();

		$actual = [];
		foreach ( $dblists as $file ) {
			$content = file_get_contents( dirname( __DIR__ ) . "/dblists/$file.dblist" );
			if ( strpos( $content, '%%' ) !== false ) {
				$actual[] = $file;
			}
		}

		// FIXME
		$grandgathered = [ 'group1', 'group2' ];
		$actual = array_diff( $actual, $grandgathered );

		$this->assertEquals( [], $actual, 'Unexpanded dblists must not be used in production' );
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
	 * This test ensures that all dblists are alphasorted
	 */
	public function testListsAreSorted() {
		$lists = DBList::getLists();
		foreach ( $lists as $listname => $dbnames ) {
			if ( strpos( $listname, 'computed' ) !== false ) {
				continue;
			}

			$origdbnames = $dbnames;
			sort( $dbnames );

			$this->assertEquals(
				$origdbnames,
				$dbnames,
				"{$listname}.dblist is not alphasorted"
			);
		}
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
