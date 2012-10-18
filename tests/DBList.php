<?php
/**
 * Helpers for DbListTests.
 *
 * @license GPLv2 or later
 * @author Antoine Musso <hashar at free dot fr>
 * @copyright Copyright © 2012, Antoine Musso <hashar at free dot fr>
 * @file
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

	/**
	 * Return an array of dblist name as key and array of dbname as value.
	 * Result is cached.
	 */
	public static function getMap() {
		// Caching
		static $list = null;
		if ( $list ) return $list;

		$objects = scandir(  dirname( __FILE__ ) . '/..'  );
		foreach ( $objects as $filename ) {
			if ( substr( $filename, -7, 7 ) == '.dblist' ) {
				$projectname = substr( $filename, 0, -7 );
				# Happilly prefetch the files content
				$list[$projectname] = file( $filename, FILE_IGNORE_NEW_LINES );
			}
		}

		return $list;
	}

	/**
	 * @param string $dblistname Filename of a dblist file without .dblist
	 * suffix.
	 */
	public static function get( $dblistname ) {
		$map = self::getMap();
		if( !array_key_exists($dblistname, $map) ) {
			throw new Exception( "No such .dblist file '$dblistname.dblist'\n" );
		}
		return $map[$dblistname];
	}


	public static function getAll () {
		return self::get( 'all' );
	}

	public static function isWikiProject( $dbname ) {
		return in_array( $dbname, self::$wiki_projects );
	}
}
