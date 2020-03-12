<?php
/**
 * Structure test for WMF's dblist files.
 *
 * @license GPL-2.0-or-later
 * @author Antoine Musso <hashar at free dot fr>
 * @copyright Copyright © 2012, Antoine Musso <hashar at free dot fr>
 * @file
 */

class DbListTest extends PHPUnit\Framework\TestCase {

	public static function provideFamilyDbnames() {
		foreach ( DBList::getLists() as $family => $databases ) {
			if ( !DBlist::isWikiFamily( $family ) ) {
				// Skip non-family files such as "s1", "private", etc.
				continue;
			}
			foreach ( $databases as $database ) {
				yield [ $family, $database ];
			}
		}
	}

	/**
	 * Validate wiki family dblists contents.
	 *
	 * They must only contain wikis of which the database suffix
	 * matches the dblist.
	 *
	 * @dataProvider provideFamilyDbnames
	 */
	public function testDatabaseSuffixMatchFamily( $family, $database ) {
		// Legacy suffix for wikipedia family
		$dbsuffix = ( $family === 'wikipedia' ) ? 'wiki' : $family;

		// Verifiy the databasename suffix
		$this->assertStringEndsWith( $dbsuffix, $database,
			"Database name $database lacks db suffix $dbsuffix of $family"
		);
	}

	public function testDblistAllContainsEverything() {
		$lists = DBList::getLists();

		// Content of all.dblist
		$all = $lists['all'];

		$skip = [
			// No point in checking that all includes itself
			'all',

			// Labs wikis (beta.wmflabs.org) might not (yet) exist in production.
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
				"'{$dbfile}.dblist' only contains names in 'all.dblist'"
			);
		}
	}

	public static function provideWikisAreIncluded() {
		return [
			'section' => [
				'all',
				// If you're adding a new section, make sure it's widely announced
				// so all the people who do things per section know about it!
				[ 's1', 's2', 's3', 's4', 's5', 's6', 's7', 's8', 's10', 's11', ],
			],

			'size' => [
				'all',
				[ 'small', 'medium', 'large', ],
			],

			'multiversion' => [
				'all',
				[ 'group0', 'group1', 'group2', ],
			],

			'family' => [
				'all',
				[
					'special',
					'wikibooks',
					'wikimedia',
					'wikinews',
					'wikipedia',
					'wikiquote',
					'wikisource',
					'wikiversity',
					'wikivoyage',
					'wiktionary',
				]
			],
		];
	}

	/**
	 * @dataProvider provideWikisAreIncluded
	 * @param string $input Which dblist to read for these assertions
	 * @param string[] $dbLists DBList names that should collectively contain all wikis
	 */
	public function testWikisAreIncluded( string $input, array $dbLists ) {
		$lists = DBList::getLists();

		$all = array_fill_keys( MWWikiversions::evalDbListExpression( $input ), [] );

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

	/**
	 * Production code that is web-facing MUST NOT load unnecessary dblists.
	 * Loading these from disk on every web request takes time.
	 */
	public function testNoUnusedDblistsLoaded() {
		$unusedDblists = array_flip( DBList::getDblistsUsedInSettings() );

		$prodSettings = wmfGetVariantSettings();
		$labsSettings = wmfApplyLabsOverrideSettings( $prodSettings );

		foreach ( $prodSettings as $settingName => $settingsArray ) {
			foreach ( $settingsArray as $wiki => $settingValue ) {
				if ( $wiki[0] === '+' || $wiki[0] === '-' ) {
					$wiki = substr( $wiki, 1 );
				}
				// If it's a dblist name, unset it if not already unset.
				// If it's a wiki or 'default', it will have never been set
				// here but that's fine.
				unset( $unusedDblists[ $wiki ] );
			}
		}

		// The diff will report dblist names that are unused,
		// and also mention the array offset in MWConfigCacheGenerator::$dbLists.
		$this->assertEquals(
			[],
			$unusedDblists,
			'Dblist files loaded by all web requests but not used'
		);
	}

	/**
	 * Production code that is web-facing MUST NOT use dblists
	 * that contain expressions because these have a significant performance cost.
	 */
	public function testNoExpressionListUsedInSettings() {
		$dblists = DBList::getDblistsUsedInSettings();

		$actual = [];
		foreach ( $dblists as $file ) {
			$content = file_get_contents( dirname( __DIR__ ) . "/dblists/$file.dblist" );
			if ( strpos( $content, '%' ) !== false ) {
				$actual[] = $file;
			}
		}

		$this->assertEquals(
			[],
			$actual,
			'Dblist files used in web requests must not contain lazy expressions'
		);
	}

	/**
	 * This test ensures that all dblists are alphasorted
	 */
	public function testListsAreSorted() {
		$lists = DBList::getLists();
		foreach ( $lists as $listname => $dbnames ) {
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
