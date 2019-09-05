<?php

namespace Wikimedia\MWConfig;

use MWWikiversions;

/**
 * Wrapper for config caching code.
 */
class MWConfigCacheGenerator {
	# When updating list please run ./docroot/noc/createTxtFileSymlinks.sh
	# Expand computed dblists with ./multiversion/bin/expanddblist

	public static $dbLists = [
		'private',
		'fishbowl',
		'special',
		'closed',
		'flow',
		'flaggedrevs',
		'small',
		'medium',
		'large',
		'wikimania',
		'wikidata',
		'wikibaserepo',
		'wikidataclient',
		'wikidataclient-test',
		'visualeditor-nondefault',
		'commonsuploads',
		'nonbetafeatures',
		'group0',
		'group1',
		'group2',
		'wikipedia',
		'nonglobal',
		'wikitech',
		'nonecho',
		'mobilemainpagelegacy',
		'wikipedia-cyrillic',
		'wikipedia-e-acute',
		'wikipedia-devanagari',
		'wikipedia-english',
		'nowikidatadescriptiontaglines',
		'top6-wikipedia',
		'rtl',
		'pp_stage0',
		'pp_stage1',
		'cirrussearch-big-indices',
	];

	/**
	 * Create a MultiVersion config object for a wiki
	 *
	 * @param string $wikiDBname The wiki's database name, e.g. 'enwiki' or  'zh_min_nanwikisource'
	 * @param object $site The wiki's site type family, e.g. 'wikipedia' or 'wikisource'
	 * @param object $lang The wiki's MediaWiki language code, e.g. 'en' or 'zh-min-nan'
	 * @param object $wgConf The global MultiVersion wgConf object
	 * @return object The wiki's config object
	 */
	public static function getMWConfigForCacheing( $wikiDBname, $site, $lang, $wgConf ) {
		# Collect all the dblist tags associated with this wiki
		$wikiTags = [];

		foreach ( self::$dbLists as $tag ) {
			$dblist = MWWikiversions::readDbListFile( $tag );
			if ( in_array( $wikiDBname, $dblist ) ) {
				$wikiTags[] = $tag;
			}
		}

		$dbSuffix = ( $site === 'wikipedia' ) ? 'wiki' : $site;
		$confParams = [
			'lang'    => $lang,
			'docRoot' => $_SERVER['DOCUMENT_ROOT'],
			'site'    => $site,
		];

		// Add a per-language tag as well
		$wikiTags[] = $wgConf->get( 'wgLanguageCode', $wikiDBname, $dbSuffix, $confParams, $wikiTags );
		$globals = $wgConf->getAll( $wikiDBname, $dbSuffix, $confParams, $wikiTags );

		return $globals;
	}

	/**
	 * Read a cached MultiVersion object from disc based on a filename, if current
	 *
	 * @param string $confCacheFile The full filepath for the wiki's cached config object
	 * @param string $confActualMtime The expected mtime for the cached config object
	 * @return object|null The wiki's config object, or null if not yet cached or stale
	 */
	public static function readFromSerialisedCache( $confCacheFile, $confActualMtime ) {
		// Ignore file warnings (file may be inaccessible, or deleted in a race)
		$confCacheStr = @file_get_contents( $confCacheFile );
		$confCacheData = $confCacheStr !== false ? unserialize( $confCacheStr ) : false;

		// Ignore non-array and array offset warnings (file may be in an older format)
		if ( @$confCacheData['mtime'] === $confActualMtime ) {
			return $confCacheData['globals'];
		}

		return null;
	}

	/**
	 * Write a MultiVersion object to disc cache
	 *
	 * @param string $cacheDir The filepath for cached multiversion config storage
	 * @param string $cacheShard The filename for the cached multiversion config object
	 * @param object $configObject The config object for this wiki
	 */
	public static function writeToSerialisedCache( $cacheDir, $cacheShard, $configObject ) {
		@mkdir( $cacheDir );
		$tmpFile = tempnam( '/tmp/', $cacheShard );

		$serialisedCacheObject = serialize( $configObject );

		if ( $tmpFile && file_put_contents( $tmpFile, $serialisedCacheObject ) ) {
			if ( !rename( $tmpFile, $cacheDir . '/' . $cacheShard ) ) {
				// T136258: Rename failed, cleanup temp file
				unlink( $tmpFile );
			};
		}
	}

}
