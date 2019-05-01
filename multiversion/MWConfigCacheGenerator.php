<?php

class MWConfigCacheGenerator {

	# When updating list please run ./docroot/noc/createTxtFileSymlinks.sh
	# Expand computed dblists with ./multiversion/bin/expanddblist
	public const DB_LISTS = [
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
		'related-articles-footer-blacklisted-skins',
		'top6-wikipedia',
		'rtl',
		'pp_stage0',
		'pp_stage1',
		'cirrussearch-big-indices',
	];

	/**
	 * Create a multiversion object based on a dbname
	 * @return object Config object for this wiki
	 */
	public static function getMWConfigForCacheing( $wikiDBname, $wgConf ) {
		# Get configuration from SiteConfiguration object
		require "wmf-config/InitialiseSettings.php";

		# Collect all the dblist tags associated with this wiki
		$wikiTags = [];
		foreach ( self::DB_LISTS as $tag ) {
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
	}

	public static function readFromSerialisedCache( $cacheFile ) {
		$cacheRecord = @file_get_contents( $cacheFile );
		if ( $cacheRecord !== false ) {
			return unserialize( $cacheRecord );
		}
		return null;
	}
}
