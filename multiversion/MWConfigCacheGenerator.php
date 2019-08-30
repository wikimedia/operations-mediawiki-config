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
}
