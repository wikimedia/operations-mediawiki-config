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
			"base" => true,
		],
	],

	'wgMinervaDonateLink' => [
		'default' => [
			'base' => true,
		],
	],

	'wgMinervaNightMode' => [
		'default' => [
			'base' => false,
			'loggedin' => false
		],
		'testwiki' => [
			'base' => true,
			'loggedin' => true,
		],
	],
	'wmgMinervaNightModeQueryString' => [
		// T359183
		'default' => [
			'action' => 'diff|info|protect|delete|undelete|action|history',
			'diff' => '*'
		],
		// Allow us to reliably test all pages here.
		'testwiki' => [],
	],
	'wmgMinervaNightModeExcludeNamespaces' => [
		// Initially night mode is disabled on everything except article page (T359183)
		'default' => [
			-2, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15,
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
			"Special:BrokenRedirects",
			"Special:DeadendPages",
			"Special:AncientPages",
			"Special:DoubleRedirects",
			"Special:LintErrors",
			"Special:LongPages",
			"Special:LonelyPages",
			"Special:UnconnectedPages",
			"Special:FewestRevisions",
			"Special:WithoutInterwiki",
			"Special:ProtectedPages",
			"Special:ProtectedTitles",
			"Special:ShortPages",
			"Special:UncategorizedCategories",
			"Special:UncategorizedFiles",
			"Special:UncategorizedPages",
			"Special:UncategorizedTemplates",
			"Special:UnusedCategories",
			"Special:UnusedFiles",
			"Special:UnusedTemplates",
			"Special:WantedCategories",
			"Special:WantedFiles",
			"Special:WantedPages",
			"Special:WantedTemplates",
			"Special:AllPages",
			"Special:PrefixIndex",
			"Special:Categories",
			"Special:CategoryTree",
			"Special:DisambiguationPages",
			"Special:EntityUsage",
			"Special:LinkSearch",
			"Special:DisambiguationPageLinks",
			"Special:PagesWithProp",
			"Special:PagesWithBadges",
			"Special:ListRedirects",
			"Special:Search",
			"Special:TrackingCategories",
			"Special:BotPasswords",
			"Special:ChangeCredentials",
			"Special:ChangeEmail",
			"Special:GlobalPreferences",
			"Special:GlobalRenameRequest",
			"Special:MergeAccount",
			"Special:Manage Two-factor authentication",
			"Special:OAuthManageMyGrants",
			"Special:Notifications",
			"Special:Preferences",
			"Special:RemoveCredentials",
			"Special:PasswordReset",
			"Special:ResetTokens",
			"Special:TopicSubscriptions",
			"Special:ActiveUsers",
			"Special:AutoblockList",
			"Special:BlockList",
			"Special:CreateAccount",
			"Special:EmailUser",
			"Special:CentralAuth",
			"Special:GlobalUsers",
			"Special:GlobalGroupPermissions",
			"Special:ListGrants",
			"Special:OAuthListConsumers",
			"Special:GlobalBlockList",
			"Special:GlobalUserRights",
			"Special:PasswordPolicies",
			"Special:Contributions",
			"Special:ListGroupRights",
			"Special:UserRights",
			"Special:ListUsers",
			"Special:EditRecovery",
			"Special:AbuseLog",
			"Special:NewFiles",
			"Special:NewPages",
			"Special:NewPagesFeed",
			"Special:RecentChanges",
			"Special:RecentChangesLinked",
			"Special:Log",
			"Special:Tags",
			"Special:ListFiles",
			"Special:GlobalUsage",
			"Special:ListDuplicatedFiles",
			"Special:MIMESearch",
			"Special:MediaStatistics",
			"Special:OrphanedTimedText",
			"Special:FileDuplicateSearch",
			"Special:Transcode statistics",
			"Special:Upload",
			"Special:VipsTest",
			"Special:ApiFeatureUsage",
			"Special:ApiSandbox",
			"Special:BookSources",
			"Special:AbuseFilter",
			"Special:ExpandTemplates",
			"Special:GadgetUsage",
			"Special:Gadgets",
			"Special:Statistics",
			"Special:AllMessages",
			"Special:TemplateSandbox",
			"Special:Hieroglyphs",
			"Special:Version",
			"Special:Interwiki",
			"Special:WikiSets",
			"Special:SiteMatrix",
			"Special:DeletePage",
			"Special:Diff",
			"Special:EditPage",
			"Special:NewSection",
			"Special:PageHistory",
			"Special:PageInfo",
			"Special:PermanentLink",
			"Special:ProtectPage",
			"Special:Purge",
			"Special:Random",
			"Special:RandomInCategory",
			"Special:RandomRedirect",
			"Special:RandomRootpage",
			"Special:Redirect",
			"Special:MostLinkedCategories",
			"Special:MostLinkedFiles",
			"Special:MostLinkedPages",
			"Special:MostTranscludedPages",
			"Special:MostCategories",
			"Special:MostInterwikis",
			"Special:MostRevisions",
			"Special:Book",
			"Special:ChangeContentModel",
			"Special:CiteThisPage",
			"Special:ComparePages",
			"Special:Export",
			"Special:PageAssessments",
			"Special:QrCode",
			"Special:UrlShortener",
			"Special:WhatLinksHere",
			"Special:BlockedExternalDomains",
			"Special:EditGrowthConfig",
			"Special:Impact",
			"Special:ManageMentors",
			"Special:MentorDashboard",
			"Special:NewcomerTasksInfo",
			"Special:ValidationStatistics",
			"Special:StablePages",
			"Special:PendingChanges",
			"Special:ContentTranslationStats",
			"Special:Contribute",
			"Special:CreateMassMessageList",
			"Special:DiscussionToolsDebug",
			"Special:FindComment",
			"Special:GlobalRenameProgress",
			"Special:MathWikibase",
			"Special:MathStatus",
			"Special:ORESModels",
			"Special:SecurePoll",
			"Special:ContentTranslation"
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
