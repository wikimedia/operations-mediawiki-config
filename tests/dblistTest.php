<?php
/**
 * Various tests made to test Wikimedia Foundation .dblist files.
 *
 * @license GPLv2 or later
 * @author Antoine Musso <hashar at free dot fr>
 * @copyright Copyright © 2012, Antoine Musso <hashar at free dot fr>
 * @file
 */


class DbListTests extends PHPUnit_Framework_TestCase {

	public static function provideProjectDbnames() {
		$cases = array();
		foreach ( DBList::getLists() as $projectname => $databases ) {
			if ( !DBlist::isWikiProject( $projectname ) ) {
				// Skip files such as s1, private ...
				continue;
			}
			foreach ( $databases as $database ) {
				$cases[] = array(
					$projectname, $database
				);
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
			: $projectname
		;

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
		$skip = array(
			// No point in checking all includes itself
			'all',

			// 'all-labs' and 'flow_only_labs' are for beta.wmflabs.org only,
			// which may have wikis not (yet) in production.
			'all-labs',
			'flow_computed_labs',
			'flow_only_labs',

			'closed',
			'deleted',
			'new_wiktionaries',
			'news',
			'private',
			'special',
			'todo',
		);

		foreach ( $lists as $dbfile => $dbnames ) {
			if ( in_array( $dbfile, $skip ) ) {
				continue;
			}

			$this->assertEquals(
				array(),
				array_diff( $dbnames, $all ),
				"'{$dbfile}.dblist' contains names not in 'all.dblist'"
			);
		}
	}

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
}

