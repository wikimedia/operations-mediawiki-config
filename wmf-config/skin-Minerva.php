<?php

// Inline comments are often used for noting the task(s) associated with specific configuration
// and requiring comments to be on their own line would reduce readability for this file
// phpcs:disable MediaWiki.WhiteSpace.SpaceBeforeSingleLineComment.NewLineComment

/**
 * Configuration for the Minerva skin.
 */

return [
	// T210553, T210554
	'wgMinervaPageIssuesNewTreatment' => [
		'default' => [
			'base' => true,
			'loggedin' => true,
			'amc' => true,
		],
	],

	'wgMinervaDonateLink' => [
		'default' => [
			'base' => false,
			'loggedin' => false,
			'amc' => false,
		],
	],

	'wgMinervaDonateBanner' => [
		'default' => [
			'base' => true,
			'loggedin' => false,
			'amc' => false,
		],
	],

	'wgMinervaHistoryInPageActions' => [
		'default' => [
			'base' => false,
			'loggedin' => true,
			'amc' => true,
		],
		// T290812
		'ptwikinews' => [
			'base' => true,
			'loggedin' => true,
			'amc' => true,
		],
	],

	'wgMinervaAdvancedMainMenu' => [
		'default' => [
			'base' => false,
			'loggedin' => false,
			'amc' => true,
		],
		// T290812
		'ptwikinews' => [
			'base' => true,
			'loggedin' => true,
			'amc' => true,
		],
	],

	'wgMinervaPersonalMenu' => [
		'default' => [
			'base' => false,
			'beta' => false,
			'loggedin' => true,
			'amc' => true,
		],
		// T290812
		'ptwikinews' => [
			'base' => true,
			'loggedin' => true,
			'amc' => true,
		],
	],

	'wgMinervaOverflowInPageActions' => [
		'default' => [
			'base' => false,
			'loggedin' => true,
			'amc' => true,
		],
		// T290812
		'ptwikinews' => [
			'base' => true,
			'loggedin' => true,
			'amc' => true,
		],
	],

	'wgMinervaShowCategories' => [
		'default' => [
			'base' => false,
			'beta' => false,
			'loggedin' => false,
			'amc' => true,
		],
		// T365323, T290812
		'mobile-anon-categories' => [
			'base' => true,
			'loggedin' => true,
			'amc' => true,
		],
	],

	'wgMinervaTalkAtTop' => [
		// Blocked on T54165
		'default' => [
			'base' => false,
			'beta' => false,
			'loggedin' => true,
			'amc' => true,
		],
		'mobile-anon-talk' => [
			'base' => true,
			'beta' => true,
			'loggedin' => true,
			'amc' => true,
		],
	],

	// T183665
	'wgMinervaAlwaysShowLanguageButton' => [
		'default' => true,
		'mediawikiwiki' => false,
		'wikidatawiki' => false,
	],
];
