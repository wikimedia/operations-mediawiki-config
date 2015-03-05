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
	 * @dataProvider Provide::ProjectsDatabases
	 */
	function testDatabaseNamesUseProjectNameAsSuffix( $projectname, $database ) {

		# Override suffix for wikipedia project
		$dbsuffix = ( $projectname === 'wikipedia' )
			? 'wiki'
			: $projectname
		;

		# Sadly, we end up with an exception because hysterical raisins
		# sourceswiki is the original Wikisource and is still active
		if( $database === 'sourceswiki' ) {
			$this->assertEquals( $projectname, 'wikisource' );
			return;
		}

		# Verifiy the databasename suffix
		$this->assertStringEndsWith( $dbsuffix, $database,
			"Database name $database lacks db suffix $dbsuffix of $projectname"
		);
	}


	/**
	 * FIXME we want to keep continuing showing errors
	 */
	function testDblistAllContainsAllDatabaseNames() {
		$dbs = DBList::getall();

		# Content of all.dblist
		$all = $dbs['all'];

		# No point in checking that the db listed in 'all' are contained
		# in 'all':
		unset( $dbs['all']);

		# dblist files we are just ignoring/skipping
		# FIXME ideally we want to clean those files from any old dbnames
		$skip = array(

			# 'all-labs' is for the 'beta' project which has wikis not yet
			# available in production ('all'). So we do not verify it.
			'all-labs',

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

