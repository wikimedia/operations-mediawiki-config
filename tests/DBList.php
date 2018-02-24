<?php
/**
 * Helpers for DbListTests.
 *
 * @license GPLv2 or later
 * @author Antoine Musso <hashar at free dot fr>
 * @copyright Copyright © 2012, Antoine Musso <hashar at free dot fr>
 * @file
 */
require_once __DIR__ . '/../multiversion/MWWikiversions.php';

class DBList {
	# List of project names. This array is used to verify that the various
	# dblist project files only contains names of databases that belong to them
	private static $wiki_projects = [
		'wikibooks',
		'wikinews',
		'wikipedia',
		'wikiquote',
		'wikisource',
		'wikiversity',
		'wiktionary',
	];

	/**
	 * @return array
	 */
	public static function getLists() {
		static $list = null;
		if ( !$list ) {
			$list = [];
			$filenames = scandir( dirname( __DIR__ ) . '/dblists' );
			foreach ( $filenames as $filename ) {
				if ( substr( $filename, -7, 7 ) == '.dblist' ) {
					$basename = substr( $filename, 0, -7 );
					$list[$basename] = MWWikiversions::readDbListFile( $filename );
				}
			}
		}
		return $list;
	}

	/**
	 * @param string $dbname
	 * @return bool
	 */
	public static function isWikiProject( $dbname ) {
		return in_array( $dbname, self::$wiki_projects );
	}
}
