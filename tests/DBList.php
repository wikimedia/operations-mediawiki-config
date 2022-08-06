<?php
/**
 * Helpers for DbListTests.
 *
 * @license GPL-2.0-or-later
 * @author Antoine Musso <hashar at free dot fr>
 * @copyright Copyright © 2012, Antoine Musso <hashar at free dot fr>
 * @file
 */

class DBList {

	/**
	 * @return array<string,string[]>
	 */
	public static function getLists() {
		static $lists;
		if ( !$lists ) {
			$lists = MWWikiversions::getAllDbListsForCLI();
		}
		return $lists;
	}

	/**
	 * @param string $dbname
	 * @return bool
	 */
	public static function isWikiFamily( $dbname ) {
		return in_array( $dbname, MWMultiVersion::SUFFIXES );
	}

	/**
	 * Checks if given dbname is in dblist.
	 *
	 * @param string $dbname
	 * @param string $dblist
	 * @return bool
	 */
	public static function isInDblist( $dbname, $dblist ) {
		// Optimization: Use getLists() instead of readDbListFile()
		// to benefit caching during the many calls from data-provided tests.
		$list = self::getLists()[$dblist];
		return in_array( $dbname, $list );
	}

	/**
	 * Get list of dblist names loaded in CommonSettings.php.
	 *
	 * @return string[]
	 */
	public static function getDblistsUsedInSettings() {
		return MWMultiVersion::DB_LISTS;
	}
}
