<?php

namespace Wikimedia\Config;

use MWWikiversions;

class DatabaseLists {

	/**
	 * @return string[]
	 */
	public static function getAllTagsLists () {
		return [
			// Special lists
			'private', 'fishbowl', 'special', 'closed', 'nonglobal',

			// Shard size lists
			'small', 'medium', 'large',

			// MediaWiki train deployment targets
			'group0', 'group1', 'group2',

			// Families lists
			'wikimania', 'wikidata', 'wikipedia', 'wikitech',

			// Features lists
			'compact-language-links', 'commonsuploads', 'flaggedrevs', 'flow',
			'mobilemainpagelegacy', 'nonecho',
			'nowikidatadescriptiontaglines', 'visualeditor-nondefault',
			'wikidataclient',

			// Features targets
			'nonbetafeatures', 'top6-wikipedia',
		];
	}

	/**
	 * @return string[]
	 */
	public static function getTagsListsFor ( $databaseName ) {
		$tags = [];

		foreach ( self::getAllTagsLists() as $tag ) {
			$databaseList = MWWikiversions::readDbListFile( $databaseName );
			if ( in_array( $databaseName, $databaseList ) ) {
				$tags[] = $tag;
			}
		}

		return $tags;
	}

}
