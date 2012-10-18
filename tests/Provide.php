<?php
/**
 * Generic providers for the 'WMF MediaWiki configuration' test suite.
 *
 * It also provides variables allowing to access these list.
 *
 * Inspired by MediaWiki tests/includes/Providers.php
 *
 * @license GPLv2 or later
 * @author Antoine Musso
 * @copyright Copyright © 2012, Antoine Musso <hashar at free dot fr>
 * @file
 */

class Provide {
	/**
	 * Initializes the class
	 */
	static function Initialize () {
		self::$projectsDatabases = self::GetProjectsDatabases();
		self::$fullProjectsDatabases = DBList::getAll();
	}

	//
	// Helper methods
	//

	/**
	 * Gets projects databases
	 *
	 * @param Boolean $includeAll includes every wiki
	 * @return Array the projects database array
	 */
	static private function GetProjectsDatabases($includeAll = false) {
		$cases=array();
		foreach( DBList::buildList() as $projectname => $databases ) {
			if( !$includeAll && !DBlist::isWikiProject( $projectname ) ) {
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

	//
	// PHPUnit data providers
	//

	/**
	 * Data provider to get databases list
	 */
	static function ProjectsDatabases() {
		return self::$projectsDatabases;
	}

	//
	// Public data members
	//

	/**
	 * The projects databases, excluding s1 or privates wikis
	 *
	 * @var Array
	 */
	public static $projectsDatabases;

	/**
	 * The projects databases, including every wiki
	 *
	 * @var Array
	 */
	public static $fullProjectsDatabases;

}
