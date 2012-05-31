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
	private $initDone = false;

	# Contains the db list filenames (ex: foobar.dblist) as key and an array of
	# lines as values.
	# Never modify it outside of ->init()
	private $db;

	/**
	 * Projects dblist should only contains databasenames which
	 * belongs to them.
	 *
	 * @dataProvider provideProjectsDatabases
	 */
	function testDatabaseNamesUseProjectNameAsSuffix( $projectname, $database ) {

		# Override suffix for wikipedia project
		$dbsuffix = ( $projectname === 'wikipedia' )
			? 'wiki'
			: $projectname
		;

		# Verifiy the databasename suffix
		$this->assertStringEndsWith( $dbsuffix, $database,
			"Database name $database lacks db suffix $dbsuffix of $projectname"
		);
	}

	function provideProjectsDatabases() {
		$cases=array();
		foreach( DBList::getall() as $projectname => $databases ) {
			if( !DBlist::isWikiProject( $projectname ) ) {
				# Skip files such as s1, private ...
				continue;
			}
			foreach( $databases as $database ) {
				$cases[] = array(
					$projectname, $database
				);
			}
		}
		return $cases;
	}

	/**
	 * FIXME we want to keep continuing showing errors
	 */
	function testDblistAllContainsAllDatabaseNames() {
		$dbs = DBList::getall();

		# Content of all.dblist
		$all = $dbs['all'];

		unset( $dbs['all']);

		# dblist files we are just ignoring/skipping
		# FIXME ideally we want to clean those files from any old dbnames
		$skip = array(
			'closed',
			'deleted',
			'new_wiktionaries',
			'news',
			'private',
			'special',
			'todo',
		);

		foreach( $dbs as $dbfile => $dbnames ) {
			if( in_array( $dbfile, $skip ) ) {
				continue;
			}

			$this->assertEquals( array()
				, array_diff( $dbnames, $all )
				, "'{$dbfile}.dblist' contains names not in 'all.dblist'"
			);
		}

	}

}

/**
 * Helpers for DbListTests.
 */
class DBList {
	# List of project names. This array is used to verify that the various
	# dblist project files only contains names of databases that belong to them
	static $wiki_projects = array(
		'wikibooks',
		'wikinews',
		'wikipedia',
		'wikiquote',
		'wikisource',
		'wikiversity',
		'wiktionary',
	);

	public static function getall() {
		static $list = null;
		if( $list ) return $list;

		$objects = scandir(  dirname( __FILE__ ) . '/..'  );
		foreach( $objects as $filename ) {
			if( substr( $filename, -7, 7 ) == '.dblist' ) {
				$projectname = substr( $filename, 0, -7 );
				# Happilly prefetch the files content
				$list[$projectname] = file( $filename, FILE_IGNORE_NEW_LINES);
			}
		}

		return $list;
	}

	public static function isWikiProject( $dbname ) {
		return in_array( $dbname, self::$wiki_projects );
	}
}
