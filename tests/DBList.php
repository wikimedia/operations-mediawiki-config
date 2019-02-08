<?php
/**
 * Helpers for DbListTests.
 *
 * @license GPL-2.0-or-later
 * @author Antoine Musso <hashar at free dot fr>
 * @copyright Copyright © 2012, Antoine Musso <hashar at free dot fr>
 * @file
 */
require_once __DIR__ . '/../multiversion/MWWikiversions.php';

class DBList {

	/**
	 * List of project names. This array is used to verify that the various
	 * dblist project files only contains names of databases that belong to them.
	 */
	private static $wikiProjects = [
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
		return in_array( $dbname, self::$wikiProjects );
	}

	/**
	 * Checks if given dbname is in dblist.
	 *
	 * @param string $dbname
	 * @param string $dblist
	 * @return bool
	 */
	public static function isInDblist( $dbname, $dblist ) {
		return in_array( $dbname, self::getLists()[$dblist] );
	}

	/**
	 * Get list of dblist names loaded in CommonSettings.php.
	 *
	 * @return string[]
	 * @throws Exception
	 */
	public static function getDblistsUsedInSettings() {
		$dblists = [];

		// Hacky hack hack:
		// Extract an array from the code that reads dblists at run-time.
		$content = file_get_contents( dirname( __DIR__ ) . '/wmf-config/CommonSettings.php' );
		$matches = null;
		preg_match_all( '/\/bin\/expanddblist.+?\]/ms', $content, $matches );
		if ( isset( $matches[0][0] ) ) {
			$strLiteralPattern = "/^[^']*'(.+)'[^']*$/";
			foreach ( explode( "\n", $matches[0][0] ) as $line ) {
				if ( preg_match( $strLiteralPattern, $line ) === 1 ) {
					$dblists[] = preg_replace( $strLiteralPattern, '$1', $line );
				}
			}
		}

		if ( !$dblists ) {
			throw new Exception( 'Failed to extract dblists from CommonSettings' );
		}

		return $dblists;
	}
}
