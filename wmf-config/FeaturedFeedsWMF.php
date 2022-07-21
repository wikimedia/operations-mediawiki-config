<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

# This file hold wmf-specific hooks for the FeaturedFeeds extension.
#
# Load tree:
#  |-- wmf-config/CommonSettings.php
#      |
#      `-- wmf-config/FeaturedFeedsWMF.php
#

$wgHooks['FeaturedFeeds::getFeeds'][] = static function ( &$feeds ) {
	global $wgConf, $wgDBname, $wmgFeaturedFeedsOverrides;
	list( $site, $lang ) = $wgConf->siteFromDB( $wgDBname );
	$media = [
		// Picture Of The Day
		'potd' => [
			'page' => 'ffeed-potd-page',
			'title' => 'ffeed-potd-title',
			'short-title' => 'ffeed-potd-short-title',
			'description' => 'ffeed-potd-desc',
			'entryName' => 'ffeed-potd-entry',
		],
		// Media Of The Day
		'motd' => [
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
				// T65596 - Metawiki Tech News bulletin
				$feeds['technews'] = [
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
					// Did you know?
					'dyk' => [
						'page' => 'ffeed-dyk-page',
						'title' => 'ffeed-dyk-title',
						'short-title' => 'ffeed-dyk-short-title',
						'description' => 'ffeed-dyk-desc',
						'entryName' => 'ffeed-dyk-entry',
					],
				];
			}
			if ( $lang == 'fr' ) {
				$feeds += [
					// T167617 - French Regards sur l'actualité de la Wikimedia bulletin
					'raw' => [
						'page' => 'ffeed-raw-page',
						'title' => 'ffeed-raw-title',
						'short-title' => 'ffeed-raw-short-title',
						'description' => 'ffeed-raw-desc',
						'entryName' => 'ffeed-raw-entry',
					],
					// T168005 - French Wikimag bulletin
					'wikimag' => [
						'page' => 'ffeed-wikimag-page',
						'title' => 'ffeed-wikimag-title',
						'short-title' => 'ffeed-wikimag-short-title',
						'description' => 'ffeed-wikimag-desc',
						'entryName' => 'ffeed-wikimag-entry',
					],
				];
			}
			break;
		case 'wikiquote':
			// Quote of the Day
			$feeds['qotd'] = [
				'page' => 'ffeed-qotd-page',
				'title' => 'ffeed-qotd-title',
				'short-title' => 'ffeed-qotd-short-title',
				'description' => 'ffeed-qotd-desc',
				'entryName' => 'ffeed-qotd-entry',
			];
			break;
		case 'wikisource':
			// Featured Text
			$feeds['featuredtexts'] = [
				'page' => 'ffeed-featuredtexts-page',
				'title' => 'ffeed-featuredtexts-title',
				'short-title' => 'ffeed-featuredtexts-short-title',
				'description' => 'ffeed-featuredtexts-desc',
				'entryName' => 'ffeed-featuredtexts-entry',
			];
			break;
		case 'wiktionary':
			// Word of the Day
			$feeds['wotd'] = [
				'page' => 'ffeed-wotd-page',
				'title' => 'ffeed-wotd-title',
				'short-title' => 'ffeed-wotd-short-title',
				'description' => 'ffeed-wotd-desc',
				'entryName' => 'ffeed-wotd-entry',
			];
			// Foreign Word of the Day
			$feeds['fwotd'] = [
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
};
