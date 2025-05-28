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

	'wgMinervaNightMode' => [
		'default' => [
			'base' => false,
			'loggedin' => true,
			'amc' => true,
		],
		'skin-themes' => [
			'base' => true,
			'loggedin' => true,
			'amc' => true,
		],
	],
	'wmgMinervaNightModeQueryString' => [
		'default' => [],
	],
	'wmgMinervaNightModeExcludeNamespaces' => [
		'default' => [],
	],
	'wmgMinervaNightModeExcludeTitles' => [
		'default' => [],
		'testwiki' => [ 'Banana' ],
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
			'beta' => false,
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

	'wgMinervaEnableSiteNotice' => [
		'default' => false,
		'closed' => true, // T261357
		'arwiki' => true,
		'arywiki' => true, // T391316
		'arwikisource' => true, // T356460
		'azwiki' => true, // T352621
		'bnwiki' => true, // T299529
		'bnwikibooks' => true, // T319317
		'bnwikiquote' => true, // T337683
		'bnwiktionary' => true, // T328630
		'kowiki' => true, // T172630
		'ptwikinews' => true, // T332813
		'pawiki' => true, // T366434
		'shwiki' => true, // T392334
		// WikidataPageBanner extensions must enable for Minerva support.
		// (T254391)
		'testwiki' => true,
		'test2wiki' => true,
		'wikivoyage' => true,
		'ruwikimedia' => true,
		'cawiki' => true,
		'euwiki' => true,
		'glwiki' => true,
		'knwiki' => true, // T346582
		'trwiki' => true,
		'wikifunctionswiki' => true, // T345463
		'newiki' => true, // T347814
	],

	// T183665
	'wgMinervaAlwaysShowLanguageButton' => [
		'default' => true,
		'mediawikiwiki' => false,
		'wikidatawiki' => false,
	],
];
