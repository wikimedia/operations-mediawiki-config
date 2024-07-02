<?php

// Inline comments are often used for noting the task(s) associated with specific configuration
// and requiring comments to be on their own line would reduce readability for this file
// phpcs:disable MediaWiki.WhiteSpace.SpaceBeforeSingleLineComment.NewLineComment

/**
 * Configuration for the Minerva skin.
 */

return [
	'wgMinervaApplyKnownTemplateHacks' => [
		// Temporarily enabled for cached HTML
		'default' => true,
		// [[phab:T361589]]
		'frwiki' => false,
	],
	// T210553, T210554
	'wgMinervaPageIssuesNewTreatment' => [
		'default' => [
			"base" => true,
		],
	],

	'wgMinervaDonateLink' => [
		'default' => [
			'base' => true,
		],
		// T360783
		'nowikimedia' => [
			'base' => false,
		],
	],

	'wgMinervaNightMode' => [
		'default' => [
			'base' => false,
			'loggedin' => false,
			'amc' => true,
		],
		'skin-themes' => [
			'base' => true,
			'loggedin' => true,
			'amc' => true,
		],
		'testwiki' => [
			'base' => true,
			'loggedin' => true,
		],
	],
	'wmgMinervaNightModeQueryString' => [
		// T359183
		'default' => [
			// Removal tickets: T366372, T366520, T366371
			'action' => 'edit|submit|diff|history',
			// Removal ticket: T366371
			'diff' => '.*'
		],
		// Allow us to reliably test all pages here.
		'testwiki' => [],
	],
	'wmgMinervaNightModeExcludeNamespaces' => [
		// Initially night mode is disabled on everything except article page (T359183)
		'default' => [
			// File pages (T357575)
			-2, 6,
			// Talk pages (T365509)
			1, 3, 5, 7, 9, 11, 13, 15,
			// Project namespace (T366366)
			4,
			// MediaWiki namespace (T366368)
			8,
			// The following is not intended to be complete, but should cover most untested namespaces
			// that are needed for the first launch. The namespaces are documented at:
			// https://www.mediawiki.org/wiki/Extension_default_namespaces
			// LQT
			90, 91, 92, 93,
			// Portal
			100, 101,
			// Wikidata
			120, 121, 122, 123, 124, 125, 146, 147,
			// Meta wiki
			200, 201, 202, 203, 206, 207,
			// ProofReadPage
			250, 251, 252, 253,
			// UploadWizard
			460, 461,
			// EventLogging
			470, 471,
			// Scribunto
			828, 829
		],
		// Allow us to reliably test all pages here.
		'testwiki' => [],
	],
	'wmgMinervaNightModeExcludeTitles' => [
		'default' => [
			// Homepage (T357699)
			"Special:Homepage",
			// Notifications (T358405)
			"Special:Notifications",
			// Hieroglyphs (T366384)
			"Special:Hieroglyphs",
			// Growth home pages (T366376)
			"Special:EnrollAsMentor",
			"Special:EditGrowthConfig",
			"Special:MentorDashboard",
			// Discussions tool special pages (T366524)
			"Special:DiscussionToolsDebug",
			"Special:FindComment",
			// ORES (T366379)
			"Special:ORESModels",
			// T367827
			"Special:Allmessages",
			// T367826
			"Special:Protectedpages",
			// Changelist / diff pages (T366371)
			"Special:Contributions",
			"Special:ComparePages",
			"Special:Recentchanges",
			"Special:Recentchangeslinked",
			"Special:PendingChanges",
			"Special:Watchlist",
			// New pages feed (T365071)
			"Special:NewPagesFeed",
		],
		'testwiki' => [ 'Banana' ],
	],
	'wgMinervaHistoryInPageActions' => [
		'default' => [
			'base' => false,
			'loggedin' => true,
		],
		// T290812
		'ptwikinews' => [
			'base' => true,
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
		],
	],

	'wgMinervaOverflowInPageActions' => [
		'default' => [
			'base' => false,
			'beta' => false,
			'loggedin' => true,
			'amc' => true,
		],
		// T290812
		'ptwikinews' => [
			'base' => true,
		],
	],

	'wgMinervaShowCategories' => [
		'default' => [
			'base' => false,
			'beta' => false,
			'loggedin' => false,
			'amc' => true,
		],
		// T365323
		'enwiktionary' => [
			'base' => true,
		],
		// T290812
		'ptwikinews' => [
			'base' => true,
		],
	],

	'wgMinervaTalkAtTop' => [
		// Blocked on T54165
		'default' => [
			'base' => false,
			'beta' => false,
			'loggedin' => true,
		],
		'mobile-anon-talk' => [
			'base' => true,
			'beta' => true,
			'loggedin' => true,
		],
		// T290812
		'ptwikinews' => [
			'base' => true,
		],
	],

	'wgMinervaEnableSiteNotice' => [
		'default' => false,
		'closed' => true, // T261357
		'arwiki' => true,
		'arwikisource' => true, // T356460
		'azwiki' => true, // T352621
		'bnwiki' => true, // T299529
		'bnwikibooks' => true, // T319317
		'bnwikiquote' => true, // T337683
		'bnwiktionary' => true, // T328630
		'kowiki' => true, // T172630
		'ptwikinews' => true, // T332813
		'pawiki' => true, // T366434
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
