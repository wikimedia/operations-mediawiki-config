<?php
/*
 * WARNING: This file is publically viewable on the web. Do not put private data here.
 * Feed settings for WMF projects
 */

$wgHooks['FeaturedFeeds::getFeeds'][] = 'wfFeaturedFeedsWMF_getFeeds';

/**
 * @param $feeds array
 * @return bool
 */
function wfFeaturedFeedsWMF_getFeeds( &$feeds ) {
	global $wgConf, $wmgFeaturedFeedsOverrides;
	list( $site, $lang ) = $wgConf->siteFromDB( wfGetDB( DB_SLAVE )->getDBname() );
	$media = [
		'potd' => [ // Picture Of The Day
			'page' => 'ffeed-potd-page',
			'title' => 'ffeed-potd-title',
			'short-title' => 'ffeed-potd-short-title',
			'description' => 'ffeed-potd-desc',
			'entryName' => 'ffeed-potd-entry',
		],
		'motd' => [ // Media Of The Day
			'page' => 'ffeed-motd-page',
			'title' => 'ffeed-motd-title',
			'short-title' => 'ffeed-motd-short-title',
			'description' => 'ffeed-motd-desc',
			'entryName' => 'ffeed-motd-entry',
		],
	];
	switch ( $site ) {
		case 'wikipedia':
			$feeds += $media;
			if ( $lang == 'commons' ) {
				$feeds['potd']['inUserLanguage'] = $feeds['motd']['inUserLanguage'] = true;
			} elseif ( $lang == 'meta' ) {
				$feeds['technews'] = [ // Tech News bulletin
					'page' => 'ffeed-technews-page',
					'title' => 'ffeed-technews-title',
					'short-title' => 'ffeed-technews-short-title',
					'description' => 'ffeed-technews-desc',
					'entryName' => 'ffeed-technews-entry',
					];
			} else {
				$feeds += [
					'featured' => [
						'page' => 'ffeed-featured-page',
						'title' => 'ffeed-featured-title',
						'short-title' => 'ffeed-featured-short-title',
						'description' => 'ffeed-featured-desc',
						'entryName' => 'ffeed-featured-entry',
					],
					'good' => [
						'page' => 'ffeed-good-page',
						'title' => 'ffeed-good-title',
						'short-title' => 'ffeed-good-short-title',
						'description' => 'ffeed-good-desc',
						'entryName' => 'ffeed-good-entry',
					],
					'onthisday' => [
						'page' => 'ffeed-onthisday-page',
						'title' => 'ffeed-onthisday-title',
						'short-title' => 'ffeed-onthisday-short-title',
						'description' => 'ffeed-onthisday-desc',
						'entryName' => 'ffeed-onthisday-entry',
					],
					'dyk' => [ // Did you know?
						'page' => 'ffeed-dyk-page',
						'title' => 'ffeed-dyk-title',
						'short-title' => 'ffeed-dyk-short-title',
						'description' => 'ffeed-dyk-desc',
						'entryName' => 'ffeed-dyk-entry',
					],
				];
			}
			break;
		case 'wikiquote':
			$feeds['qotd'] = [ // Quote of the Day
				'page' => 'ffeed-qotd-page',
				'title' => 'ffeed-qotd-title',
				'short-title' => 'ffeed-qotd-short-title',
				'description' => 'ffeed-qotd-desc',
				'entryName' => 'ffeed-qotd-entry',
			];
			break;
		case 'wikisource':
			$feeds['featuredtexts'] = [ // Featured Text
				'page' => 'ffeed-featuredtexts-page',
				'title' => 'ffeed-featuredtexts-title',
				'short-title' => 'ffeed-featuredtexts-short-title',
				'description' => 'ffeed-featuredtexts-desc',
				'entryName' => 'ffeed-featuredtexts-entry',
			];
			break;
		case 'wiktionary':
			$feeds['wotd'] = [ // Word of the Day
				'page' => 'ffeed-wotd-page',
				'title' => 'ffeed-wotd-title',
				'short-title' => 'ffeed-wotd-short-title',
				'description' => 'ffeed-wotd-desc',
				'entryName' => 'ffeed-wotd-entry',
			];
			$feeds['fwotd'] = [ // Foreign Word of the Day
				'page' => 'ffeed-fwotd-page',
				'title' => 'ffeed-fwotd-title',
				'short-title' => 'ffeed-fwotd-short-title',
				'description' => 'ffeed-fwotd-desc',
				'entryName' => 'ffeed-fwotd-entry',
			];
			break;
	}
	foreach ( $wmgFeaturedFeedsOverrides as $feedName => $overrides ) {
		if ( isset( $feeds[$feedName] ) ) {
			$feeds[$feedName] = $overrides + $feeds[$feedName];
		}
	}
	return true;
}
