<?php
/**
 * Generic providers for the 'WMF MediaWiki configuration' test suite.
 *
 * Inspired by MediaWiki tests/includes/Providers.php
 *
 * @license GPPLv2 or later
 * @author Antoine Musso
 * @copyright Copyright © 2012, Antoine Musso <hashar at free dot fr>
 * @file
 */

class Provide {
	function ProjectsDatabases() {
		$cases =[];
		foreach ( DBList::getall() as $projectname => $databases ) {
			if ( !DBlist::isWikiProject( $projectname ) ) {
				# Skip files such as s1, private ...
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

}
