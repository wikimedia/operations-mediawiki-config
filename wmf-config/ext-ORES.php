<?php

// Inline comments are often used for noting the task(s) associated with specific configuration
// and requiring comments to be on their own line would reduce readability for this file
// phpcs:disable MediaWiki.WhiteSpace.SpaceBeforeSingleLineComment.NewLineComment

return [
'wgOresWikiId' => [
	'default' => null,
	'test2wiki' => 'testwiki',
],
'wgOresUiEnabled' => [
	'default' => false,
	'arwiki' => true, // T192498
	'bswiki' => true, // T197010
	'cawiki' => true, // T192501
	'cswiki' => true, // T151611
	'enwiki' => true, // T140003
	'eswiki' => true, // T130279
	'eswikibooks' => true, // T145394
	'eswikiquote' => true, // T219160
	'etwiki' => true, // T159609
	'fawiki' => true, // T130211
	'fiwiki' => true, // T163011
	'frwiki' => true,
	'hewiki' => true, // T161621
	'huwiki' => true, // T192496
	'itwiki' => true, // T211032
	'kowiki' => true, // T161628
	'lvwiki' => true, // T192499
	'nlwiki' => true, // T139432
	'plwiki' => true, // T140005
	'ptwiki' => true, // T139692
	'rowiki' => true, // T170723
	'ruwiki' => true,
	'simplewiki' => true, // T182012
	'sqwiki' => true, // T170723
	'srwiki' => true, // T197012
	'svwiki' => true, // T174560
	'testwiki' => true, // T199913
	'test2wiki' => true, // T200412
	'trwiki' => true, // T139992
	'ukwiki' => true, // T256887
	'wikidatawiki' => true, // T130212
	'zhwiki' => true, // T225562
],
'wgOresUseLiftwing' => [
	'default' => false,
	'hewiki' => true, // T342115
	'eswikibooks' => true, // T342115
	'eswikiquote' => true, // T342115
	'itwiki' => true, // T342115
	'testwiki' => true, // T319170
],
'wgOresModels' => [
	'default' => [
		'damaging' => [ 'enabled' => true ],
		'goodfaith' => [ 'enabled' => true ],
		'reverted' => [ 'enabled' => false ],
		'articlequality' => [ 'enabled' => false, 'namespaces' => [ 0 ], 'cleanParent' => true ],
		'draftquality' => [ 'enabled' => false, 'namespaces' => [ 0 ], 'types' => [ 1 ] ],
	],
	'arwiki' => [
		'damaging' => [ 'enabled' => true ],
		// goodfaith is disabled for arwiki (T192498, T193905)
		'goodfaith' => [ 'enabled' => false ],
		'reverted' => [ 'enabled' => false ],
		'articlequality' => [ 'enabled' => false, 'namespaces' => [ 0 ], 'cleanParent' => true ],
		'draftquality' => [ 'enabled' => false, 'namespaces' => [ 0 ], 'types' => [ 1 ] ],
	],
	'enwiki' => [
		'damaging' => [ 'enabled' => true, 'excludeBots' => true ],
		'goodfaith' => [ 'enabled' => true, 'excludeBots' => true ],
		'reverted' => [ 'enabled' => false ],
		'articlequality' => [ 'enabled' => true, 'namespaces' => [ 0, 118 ], 'cleanParent' => true, 'keepForever' => true, ],
		'draftquality' => [ 'enabled' => true, 'namespaces' => [ 0, 118 ], 'types' => [ 1 ], 'excludeBots' => true, 'cleanParent' => true, 'keepForever' => true, ],
	],
	'euwiki' => [
		'damaging' => [ 'enabled' => false ],
		'goodfaith' => [ 'enabled' => false ],
		'reverted' => [ 'enabled' => false ],
		'articlequality' => [ 'enabled' => true, 'namespaces' => [ 0 ], 'cleanParent' => true ],
		'draftquality' => [ 'enabled' => false, 'namespaces' => [ 0 ], 'types' => [ 1 ] ],
	],
	'srwiki' => [
		'damaging' => [ 'enabled' => true ],
		// goodfaith is disabled for srwiki (T197012)
		'goodfaith' => [ 'enabled' => false ],
		'reverted' => [ 'enabled' => false ],
		'articlequality' => [ 'enabled' => false, 'namespaces' => [ 0 ], 'cleanParent' => true ],
		'draftquality' => [ 'enabled' => false, 'namespaces' => [ 0 ], 'types' => [ 1 ] ],
	],
	'testwiki' => [
		'damaging' => [ 'enabled' => true, 'excludeBots' => true ],
		'goodfaith' => [ 'enabled' => true, 'excludeBots' => true ],
		'reverted' => [ 'enabled' => false ],
		'articlequality' => [ 'enabled' => true, 'namespaces' => [ 0, 118 ], 'cleanParent' => true, 'keepForever' => true, ],
		'draftquality' => [ 'enabled' => true, 'namespaces' => [ 0, 118 ], 'types' => [ 1 ], 'excludeBots' => true, 'cleanParent' => true, 'keepForever' => true, ],
	],
	'test2wiki' => [
		'damaging' => [ 'enabled' => true, 'excludeBots' => true ],
		'goodfaith' => [ 'enabled' => true, 'excludeBots' => true ],
		'reverted' => [ 'enabled' => false ],
		// articlequality and draftquality are disabled until ORES is configured to allow these
		// for test2wiki. See T198997
		'articlequality' => [ 'enabled' => false, 'namespaces' => [ 0, 118 ], 'cleanParent' => true ],
		'draftquality' => [ 'enabled' => false, 'namespaces' => [ 0, 118 ], 'types' => [ 1 ], 'excludeBots' => true, 'cleanParent' => true ],
	],
	'zhwiki' => [
		'damaging' => [ 'enabled' => true ],
		'goodfaith' => [ 'enabled' => true ],
		'reverted' => [ 'enabled' => false ],
		'articlequality' => [ 'enabled' => false, 'namespaces' => [ 0 ], 'cleanParent' => true ],
		'draftquality' => [ 'enabled' => false, 'namespaces' => [ 0 ], 'types' => [ 1 ] ],
	],
],
'wgOresExcludeBots' => [
	'default' => true,
	'enwiki' => false,
	'euwiki' => false,
],
'wgOresFiltersThresholds' => [
	'default' => [],
	'arwiki' => [
		'damaging' => [
			'likelygood' => [ 'min' => 0, 'max' => 'maximum recall @ precision >= 0.997' ],
			// maybebad uses default
			'likelybad' => [ 'min' => 'maximum recall @ precision >= 0.45', 'max' => 1 ],
			'verylikelybad' => false,
		],
		// goodfaith is disabled for arwiki (T192498, T193905)
	],
	'bswiki' => [
		'damaging' => [
			// likelygood, maybebad and likelybad use default
			'verylikelybad' => [ 'min' => 'maximum recall @ precision >= 0.80', 'max' => 1 ],
		],
		'goodfaith' => [
			// likelygood uses default
			'maybebad' => [ 'min' => 0, 'max' => 'maximum recall @ precision >= 0.15' ],
			// likelybad uses default
			'verylikelybad' => [ 'min' => 0, 'max' => 'maximum recall @ precision >= 0.80' ],
		],
	],
	'cawiki' => [
		// damaging uses defaults for everything
		'goodfaith' => [
			'likelygood' => [ 'min' => 'maximum recall @ precision >= 0.998', 'max' => 1 ],
			'maybebad' => [ 'min' => 0, 'max' => 'maximum recall @ precision >= 0.15' ],
			// likelybad uses default
			'verylikelybad' => [ 'min' => 0, 'max' => 'maximum recall @ precision >= 0.9' ],
		],
	],
	'cswiki' => [
		'damaging' => [
			// likelygood, maybebad, likelybad use defaults
			'verylikelybad' => [ 'min' => 'maximum recall @ precision >= 0.98', 'max' => 1 ],
		],
		'goodfaith' => [
			// likelygood, maybebad, likelybad use defaults
			'verylikelybad' => [ 'min' => 0, 'max' => 'maximum recall @ precision >= 0.98' ],
		],
	],
	'enwiki' => [
		'damaging' => [
			'likelygood' => [ 'min' => 0, 'max' => 'maximum recall @ precision >= 0.99' ],
			'maybebad' => [ 'min' => 'maximum recall @ precision >= 0.15', 'max' => 1 ],
			'likelybad' => [ 'min' => 'maximum recall @ precision >= 0.45', 'max' => 1 ],
			// verylikelybad uses default
		],
		'goodfaith' => [
			'likelygood' => [ 'min' => 'maximum recall @ precision >= 0.99', 'max' => 1 ],
			'maybebad' => [ 'min' => 0, 'max' => 'maximum recall @ precision >= 0.15' ],
			// likelybad uses default
			'verylikelybad' => [ 'min' => 0, 'max' => 'maximum recall @ precision >= 0.9' ],
		],
	],
	'eswiki' => [
		// damaging uses defaults for everything
		'goodfaith' => [
			// likelygood uses default
			'maybebad' => false,
			'likelybad' => false,
			'verylikelybad' => [ 'min' => 0, 'max' => 'maximum recall @ precision >= 0.98' ],
		],
	],
	'eswikibooks' => [
		'damaging' => [
			// likelygood uses default
			'maybebad' => false,
			// likelybad, verylikelybad use defaults
		],
		'goodfaith' => [
			'likelygood' => [ 'min' => 'maximum recall @ precision >= 0.99', 'max' => 1 ],
			'maybebad' => false,
			// likelybad uses default
			'verylikelybad' => [ 'min' => 0, 'max' => 'maximum recall @ precision >= 0.9' ],
		],
	],
	'eswikiquote' => [
		'damaging' => [
			'likelygood' => [ 'min' => 0, 'max' => 'maximum recall @ precision >= 0.99' ],
			// maybebad uses default
			// likelybad uses default
			'verylikelybad' => [ 'min' => 'maximum recall @ precision >= 0.98', 'max' => 1 ],
		],
		'goodfaith' => [
			'likelygood' => [ 'min' => 'maximum recall @ precision >= 0.98', 'max' => 1 ],
			'maybebad' => [ 'min' => 0, 'max' => 'maximum recall @ precision >= 0.15' ],
			// likelybad uses default
			// verylikelybad uses default (disabled)
		],
	],
	'etwiki' => [
		'damaging' => [
			// likelygood, maybebad, likelybad use defaults
			'verylikelybad' => [ 'min' => 'maximum recall @ precision >= 0.98', 'max' => 1 ],
		],
		'goodfaith' => [
			// likelygood, maybebad, likelybad use defaults
			'verylikelybad' => [ 'min' => 0, 'max' => 'maximum recall @ precision >= 0.9' ],
		],
	],
	'fawiki' => [
		'damaging' => [
			// likelygood uses default
			'maybebad' => [ 'min' => 'maximum recall @ precision >= 0.15', 'max' => 1 ],
			'likelybad' => false,
			'verylikelybad' => [ 'min' => 'maximum recall @ precision >= 0.6', 'max' => 1 ],
		],
		'goodfaith' => [
			// likelygood uses default
			'maybebad' => [ 'min' => 0, 'max' => 'maximum recall @ precision >= 0.15' ],
			'likelybad' => [ 'min' => 0, 'max' => 'maximum recall @ precision >= 0.45' ],
			'verylikelybad' => [ 'min' => 0, 'max' => 'maximum recall @ precision >= 0.75' ],
		],
	],
	'fiwiki' => [
		// damaging uses defaults for everything
		'goodfaith' => [
			// likelygood, maybebad, likelybad use defaults
			'verylikelybad' => [ 'min' => 0, 'max' => 'maximum recall @ precision >= 0.9' ],
		],
	],
	'frwiki' => [
		'damaging' => [
			// likelygood, maybebad, likelybad use defaults
			'verylikelybad' => [ 'min' => 'maximum recall @ precision >= 0.85', 'max' => 1 ],
		],
		'goodfaith' => [
			// likelygood, maybebad, likelybad use defaults
			'verylikelybad' => [ 'min' => 0, 'max' => 'maximum recall @ precision >= 0.9' ],
		],
	],
	'hewiki' => [
		'damaging' => [
			// likelygood uses default
			'maybebad' => false,
			'likelybad' => [ 'min' => 'maximum recall @ precision >= 0.45', 'max' => 1 ],
			'verylikelybad' => [ 'min' => 'maximum recall @ precision >= 0.98', 'max' => 1 ],
		],
		'goodfaith' => [
			// likelygood uses default
			'maybebad' => false,
			'likelybad' => [ 'min' => 0, 'max' => 'maximum recall @ precision >= 0.45' ],
			'verylikelybad' => [ 'min' => 0, 'max' => 'maximum recall @ precision >= 0.99' ],
		],
	],
	'huwiki' => [
		'damaging' => [
			'likelygood' => [ 'min' => 0, 'max' => 'maximum recall @ precision >= 0.999' ],
			'maybebad' => [ 'min' => 'maximum recall @ precision >= 0.15', 'max' => 1 ],
			// likelybad and verylikelybad use defaults
		],
		'goodfaith' => [
			'likelygood' => [ 'min' => 'maximum recall @ precision >= 0.999', 'max' => 1 ],
			'maybebad' => [ 'min' => 0, 'max' => 'maximum recall @ precision >= 0.15' ],
			// likelybad uses default
			'verylikelybad' => [ 'min' => 0, 'max' => 'maximum recall @ precision >= 0.85' ],
		],
	],
	'itwiki' => [
		'damaging' => [
			// likelygood, maybebad use default
			'likelybad' => [ 'min' => 'maximum recall @ precision >= 0.45', 'max' => 1 ],
			'verylikelybad' => [ 'min' => 'maximum recall @ precision >= 0.75', 'max' => 1 ],
		],
		'goodfaith' => [
			// likelygood uses default
			'maybebad' => [ 'min' => 0, 'max' => 'maximum recall @ precision >= 0.15' ],
			// likelybad uses default
			'verylikelybad' => [ 'min' => 0, 'max' => 'maximum recall @ precision >= 0.9' ],
		],
	],
	'kowiki' => [
		'damaging' => [
			'likelygood' => [ 'min' => 0, 'max' => 'maximum recall @ precision >= 0.99' ],
			// maybebad uses default
			'likelybad' => [ 'min' => 'maximum recall @ precision >= 0.45', 'max' => 1 ],
			'verylikelybad' => [ 'min' => 'maximum recall @ precision >= 0.75', 'max' => 1 ],
		],
		'goodfaith' => [
			'likelygood' => [ 'min' => 'maximum recall @ precision >= 0.99', 'max' => 1 ],
			'maybebad' => [ 'min' => 0, 'max' => 'maximum recall @ precision >= 0.15' ],
			// likelybad uses default
			// verylikelybad uses default (disabled)
		],
	],
	'lvwiki' => [
		'damaging' => [
			'likelygood' => [ 'min' => 0, 'max' => 'maximum recall @ precision >= 0.995' ],
			// maybebad, likelybad and verylikelybad use defaults
		],
		'goodfaith' => [
			'likelygood' => [ 'min' => 'maximum recall @ precision >= 0.997', 'max' => 1 ],
			'maybebad' => [ 'min' => 0, 'max' => 'maximum recall @ precision >= 0.15' ],
			// likelybad uses default
			'verylikelybad' => [ 'min' => 0, 'max' => 'maximum recall @ precision >= 0.9' ],
		],
	],
	'nlwiki' => [
		'damaging' => [
			'likelygood' => [ 'min' => 0, 'max' => 'maximum recall @ precision >= 0.99' ],
			// maybebad, likelybad, verylikelybad use defaults
		],
		'goodfaith' => [
			// likelygood, maybebad, likelybad use defaults
			'verylikelybad' => [ 'min' => 0, 'max' => 'maximum recall @ precision >= 0.9' ],
		],
	],
	'plwiki' => [
		'damaging' => [
			// likelygood uses default
			'maybebad' => false,
			'likelybad' => [ 'min' => 'maximum recall @ precision >= 0.75', 'max' => 1 ],
			'verylikelybad' => [ 'min' => 'maximum recall @ precision >= 0.98', 'max' => 1 ],
		],
		'goodfaith' => [
			// likelygood uses default
			'maybebad' => false,
			'likelybad' => false,
			'verylikelybad' => [ 'min' => 0, 'max' => 'maximum recall @ precision >= 0.98' ],
		],
	],
	'ptwiki' => [
		'damaging' => [
			'likelygood' => [ 'min' => 0, 'max' => 'maximum recall @ precision >= 0.99' ],
			// maybebad, likelybad, verylikelybad use defaults
		],
		'goodfaith' => [
			// likelygood, maybebad, likelybad use defaults
			'verylikelybad' => [ 'min' => 0, 'max' => 'maximum recall @ precision >= 0.9' ],
		],
	],
	'rowiki' => [
		'damaging' => [
			// likelygood, maybebad, likelybad use defaults
			'verylikelybad' => [ 'min' => 'maximum recall @ precision >= 0.75', 'max' => 1 ],
		],
		'goodfaith' => [
			// HACK: use recall-based likelygood threshold because it has a higher precision than even precision=0.995
			'likelygood' => [ 'min' => 'maximum filter_rate @ recall >= 0.9', 'max' => 1 ],
			// maybebad, likelybad use defaults
			'verylikelybad' => [ 'min' => 0, 'max' => 'maximum recall @ precision >= 0.75' ],
		],
	],
	'ruwiki' => [
		'damaging' => [
			'likelygood' => [ 'min' => 0, 'max' => 'maximum recall @ precision >= 0.99' ],
			// maybebad uses default
			'likelybad' => [ 'min' => 'maximum recall @ precision >= 0.45', 'max' => 1 ],
			'verylikelybad' => [ 'min' => 'maximum recall @ precision >= 0.75', 'max' => 1 ],
		],
		'goodfaith' => [
			// likelygood uses default
			'maybebad' => [ 'min' => 0, 'max' => 'maximum recall @ precision >= 0.15' ],
			'likelybad' => [ 'min' => 0, 'max' => 'maximum recall @ precision >= 0.45' ],
			'verylikelybad' => [ 'min' => 0, 'max' => 'maximum recall @ precision >= 0.75' ],
		],
	],
	'simplewiki' => [
		// Same as enwiki
		'damaging' => [
			'likelygood' => [ 'min' => 0, 'max' => 'maximum recall @ precision >= 0.99' ],
			'maybebad' => [ 'min' => 'maximum recall @ precision >= 0.15', 'max' => 1 ],
			'likelybad' => [ 'min' => 'maximum recall @ precision >= 0.45', 'max' => 1 ],
			// verylikelybad uses default
		],
		'goodfaith' => [
			'likelygood' => [ 'min' => 'maximum recall @ precision >= 0.99', 'max' => 1 ],
			'maybebad' => [ 'min' => 0, 'max' => 'maximum recall @ precision >= 0.15' ],
			// likelybad uses default
			'verylikelybad' => [ 'min' => 0, 'max' => 'maximum recall @ precision >= 0.9' ],
		],
	],
	'sqwiki' => [
		'damaging' => [
			// HACK: use recall-based likelygood threshold because it has a higher precision than even precision=0.995
			'likelygood' => [ 'min' => 0, 'max' => 'maximum filter_rate @ recall >= 0.9' ],
			// maybebad, likelybad, verylikelybad use defaults
		],
		'goodfaith' => [
			// HACK: use recall-based likelygood threshold because it has a higher precision than even precision=0.995
			'likelygood' => [ 'min' => 'maximum filter_rate @ recall >= 0.9', 'max' => 1 ],
			'maybebad' => [ 'min' => 0, 'max' => 'maximum recall @ precision >= 0.15' ],
			'likelybad' => [ 'min' => 0, 'max' => 'maximum recall @ precision >= 0.45' ],
			'verylikelybad' => [ 'min' => 0, 'max' => 'maximum recall @ precision >= 0.9' ],
		],
	],
	'srwiki' => [
		'damaging' => [
			'likelygood' => [ 'min' => 0, 'max' => 'maximum recall @ precision >= 0.999' ],
			// maybebad uses default
			'likelybad' => [ 'min' => 'maximum recall @ precision >= 0.45', 'max' => 1 ],
			'verylikelybad' => [ 'min' => 'maximum recall @ precision >= 0.75', 'max' => 1 ],
		],
		// goodfaith is disabled for srwiki (T197012)
	],
	'svwiki' => [
		'damaging' => [
			// likelygood, maybebad use defaults
			'likelybad' => [ 'min' => 'maximum recall @ precision >= 0.75', 'max' => 1 ],
			'verylikelybad' => [ 'min' => 'maximum recall @ precision >= 0.98', 'max' => 1 ],
		],
		'goodfaith' => [
			'likelygood' => [ 'min' => 'maximum recall @ precision >= 0.999', 'max' => 1 ],
			// maybebad, likelybad use defaults
			'verylikelybad' => [ 'min' => 0, 'max' => 'maximum recall @ precision >= 0.9' ],
		],
	],
	'trwiki' => [
		'damaging' => [
			// likelygood, maybebad use defaults
			'likelybad' => [ 'min' => 'maximum recall @ precision >= 0.45', 'max' => 1 ],
			'verylikelybad' => [ 'min' => 'maximum recall @ precision >= 0.75', 'max' => 1 ],
		],
		'goodfaith' => [
			// likelygood, maybebad use defaults
			'likelybad' => [ 'min' => 0, 'max' => 'maximum recall @ precision >= 0.45' ],
			'verylikelybad' => [ 'min' => 0, 'max' => 'maximum recall @ precision >= 0.75' ],
		],
	],
	'ukwiki' => [
		'damaging' => [
			'likelygood' => [ 'min' => 0, 'max' => 'maximum recall @ precision >= 0.997' ],
			// maybebad uses default
			'likelybad' => [ 'min' => 'maximum recall @ precision >= 0.45', 'max' => 1 ],
			'verylikelybad' => false,
		],
		'goodfaith' => [
			'likelygood' => [ 'min' => 'maximum recall @ precision >= 0.998', 'max' => 1 ],
			'maybebad' => [ 'min' => 0, 'max' => 'maximum recall @ precision >= 0.15' ],
			'likelybad' => [ 'min' => 0, 'max' => 'maximum recall @ precision >= 0.45' ],
			// verylikelybad uses default
		],
	],
	'wikidatawiki' => [
		'damaging' => [
			// likelygood uses default
			'maybebad' => false,
			'likelybad' => [ 'min' => 'maximum recall @ precision >= 0.75', 'max' => 1 ],
			// verylikelybad uses defaults
		],
		'goodfaith' => [
			// likelygood uses default
			'maybebad' => false,
			// likelybad uses default
			'verylikelybad' => [ 'min' => 0, 'max' => 'maximum recall @ precision >= 0.9' ],
		],
	],
	'zhwiki' => [
		'damaging' => [
			'likelygood' => [ 'min' => 0, 'max' => 'maximum recall @ precision >= 0.99' ],
			'maybebad' => [ 'min' => 'maximum recall @ precision >= 0.15', 'max' => 1 ],
			// likelybad uses default
			'verylikelybad' => false,
		],
		'goodfaith' => [
			'likelygood' => [ 'min' => 'maximum recall @ precision >= 0.99', 'max' => 1 ],
			'maybebad' => [ 'min' => 0, 'max' => 'maximum recall @ precision >= 0.15' ],
			'likelybad' => [ 'min' => 0, 'max' => 'maximum recall @ precision >= 0.45' ],
			'verylikelybad' => [ 'min' => 0, 'max' => 'maximum recall @ precision >= 0.75' ],
		],
	],
],
'wmgOresDefaultSensitivityLevel' => [
	'default' => 'soft', // likelybad
	// One some wikis, there is no likelybad level for damaging ('likelybad' => false) above
	// Set a different value on those wikis to prevent exceptions (T165011)
	'eswiki' => 'softest', // verylikelybad
	'fawiki' => 'hard', // maybebad
],
'wgOresEnabledNamespaces' => [
	'default' => [],
	'wikidatawiki' => [ 0 => true, 120 => true ], // T139660
],
'wgOresModelVersions' => [
	'arwiki' => [
		'models' => [
			'articletopic' => [
				'version' => '1.3.0',
			],
			'damaging' => [
				'version' => '0.5.0',
			],
			'goodfaith' => [
				'version' => '0.5.0',
			],
		],
	],
	'bnwiki' => [
		'models' => [
			'reverted' => [
				'version' => '0.5.0',
			],
		],
	],
	'bswiki' => [
		'models' => [
			'damaging' => [
				'version' => '0.5.0',
			],
			'goodfaith' => [
				'version' => '0.5.0',
			],
		],
	],
	'cawiki' => [
		'models' => [
			'damaging' => [
				'version' => '0.5.1',
			],
			'goodfaith' => [
				'version' => '0.5.1',
			],
		],
	],
	'cswiki' => [
		'models' => [
			'articletopic' => [
				'version' => '1.3.0',
			],
			'damaging' => [
				'version' => '0.6.0',
			],
			'goodfaith' => [
				'version' => '0.6.0',
			],
		],
	],
	'dewiki' => [
		'models' => [
			'damaging' => [
				'version' => '0.5.1',
			],
			'goodfaith' => [
				'version' => '0.5.1',
			],
		],
	],
	'elwiki' => [
		'models' => [
			'reverted' => [
				'version' => '0.5.0',
			],
		],
	],
	'enwiki' => [
		'models' => [
			'articlequality' => [
				'version' => '0.9.2',
			],
			'articletopic' => [
				'version' => '1.3.0',
			],
			'damaging' => [
				'version' => '0.5.1',
			],
			'draftquality' => [
				'version' => '0.2.1',
			],
			'drafttopic' => [
				'version' => '1.3.0',
			],
			'goodfaith' => [
				'version' => '0.5.1',
			],
			'wp10' => [
				'version' => '0.9.2',
			],
		],
	],
	'enwiktionary' => [
		'models' => [
			'reverted' => [
				'version' => '0.5.0',
			],
		],
	],
	'eswiki' => [
		'models' => [
			'damaging' => [
				'version' => '0.5.0',
			],
			'goodfaith' => [
				'version' => '0.5.0',
			],
		],
	],
	'eswikibooks' => [
		'models' => [
			'damaging' => [
				'version' => '0.5.0',
			],
			'goodfaith' => [
				'version' => '0.5.0',
			],
		],
	],
	'eswikiquote' => [
		'models' => [
			'damaging' => [
				'version' => '0.5.0',
			],
			'goodfaith' => [
				'version' => '0.5.0',
			],
		],
	],
	'etwiki' => [
		'models' => [
			'damaging' => [
				'version' => '0.5.0',
			],
			'goodfaith' => [
				'version' => '0.5.0',
			],
		],
	],
	'euwiki' => [
		'models' => [
			'articlequality' => [
				'version' => '0.8.1',
			],
			'articletopic' => [
				'version' => '1.4.0',
			],
			'wp10' => [
				'version' => '0.8.1',
			],
		],
	],
	'fawiki' => [
		'models' => [
			'articlequality' => [
				'version' => '0.9.0',
			],
			'damaging' => [
				'version' => '0.5.0',
			],
			'goodfaith' => [
				'version' => '0.5.0',
			],
			'wp10' => [
				'version' => '0.9.0',
			],
		],
	],
	'fiwiki' => [
		'models' => [
			'damaging' => [
				'version' => '0.5.1',
			],
			'goodfaith' => [
				'version' => '0.5.1',
			],
		],
	],
	'frwiki' => [
		'models' => [
			'articlequality' => [
				'version' => '0.8.0',
			],
			'damaging' => [
				'version' => '0.5.0',
			],
			'goodfaith' => [
				'version' => '0.5.0',
			],
			'wp10' => [
				'version' => '0.8.0',
			],
		],
	],
	'frwikisource' => [
		'models' => [
			'pagelevel' => [
				'version' => '0.3.0',
			],
		],
	],
	'glwiki' => [
		'models' => [
			'articlequality' => [
				'version' => '0.8.0',
			],
			'reverted' => [
				'version' => '0.5.0',
			],
		],
	],
	'hewiki' => [
		'models' => [
			'damaging' => [
				'version' => '0.5.0',
			],
			'goodfaith' => [
				'version' => '0.5.0',
			],
		],
	],
	'hiwiki' => [
		'models' => [
			'damaging' => [
				'version' => '0.5.0',
			],
			'goodfaith' => [
				'version' => '0.5.0',
			],
		],
	],
	'hrwiki' => [
		'models' => [
			'reverted' => [
				'version' => '0.5.0',
			],
		],
	],
	'huwiki' => [
		'models' => [
			'articletopic' => [
				'version' => '1.4.0',
			],
			'damaging' => [
				'version' => '0.5.1',
			],
			'goodfaith' => [
				'version' => '0.5.1',
			],
		],
	],
	'hywiki' => [
		'models' => [
			'articletopic' => [
				'version' => '1.4.0',
			],
		],
	],
	'idwiki' => [
		'models' => [
			'reverted' => [
				'version' => '0.5.0',
			],
		],
	],
	'iswiki' => [
		'models' => [
			'reverted' => [
				'version' => '0.5.0',
			],
		],
	],
	'itwiki' => [
		'models' => [
			'damaging' => [
				'version' => '0.5.0',
			],
			'goodfaith' => [
				'version' => '0.5.0',
			],
		],
	],
	'jawiki' => [
		'models' => [
			'damaging' => [
				'version' => '0.5.0',
			],
			'goodfaith' => [
				'version' => '0.5.0',
			],
		],
	],
	'kowiki' => [
		'models' => [
			'articletopic' => [
				'version' => '1.3.0',
			],
			'damaging' => [
				'version' => '0.5.0',
			],
			'goodfaith' => [
				'version' => '0.5.0',
			],
		],
	],
	'lvwiki' => [
		'models' => [
			'damaging' => [
				'version' => '0.5.0',
			],
			'goodfaith' => [
				'version' => '0.5.0',
			],
		],
	],
	'nlwiki' => [
		'models' => [
			'articlequality' => [
				'version' => '0.9.0',
			],
			'damaging' => [
				'version' => '0.5.1',
			],
			'goodfaith' => [
				'version' => '0.5.1',
			],
		],
	],
	'nowiki' => [
		'models' => [
			'damaging' => [
				'version' => '0.5.1',
			],
			'goodfaith' => [
				'version' => '0.5.1',
			],
		],
	],
	'plwiki' => [
		'models' => [
			'damaging' => [
				'version' => '0.5.0',
			],
			'goodfaith' => [
				'version' => '0.5.0',
			],
		],
	],
	'ptwiki' => [
		'models' => [
			'articlequality' => [
				'version' => '0.8.0',
			],
			'damaging' => [
				'version' => '0.5.1',
			],
			'draftquality' => [
				'version' => '0.2.1',
			],
			'goodfaith' => [
				'version' => '0.5.1',
			],
		],
	],
	'rowiki' => [
		'models' => [
			'damaging' => [
				'version' => '0.5.1',
			],
			'goodfaith' => [
				'version' => '0.5.1',
			],
		],
	],
	'ruwiki' => [
		'models' => [
			'articlequality' => [
				'version' => '0.8.0',
			],
			'damaging' => [
				'version' => '0.5.0',
			],
			'goodfaith' => [
				'version' => '0.5.0',
			],
			'wp10' => [
				'version' => '0.8.0',
			],
		],
	],
	'simplewiki' => [
		'models' => [
			'articlequality' => [
				'version' => '0.9.2',
			],
			'articletopic' => [
				'version' => '1.3.0',
			],
			'damaging' => [
				'version' => '0.5.1',
			],
			'draftquality' => [
				'version' => '0.2.1',
			],
			'goodfaith' => [
				'version' => '0.5.1',
			],
			'wp10' => [
				'version' => '0.9.2',
			],
		],
	],
	'sqwiki' => [
		'models' => [
			'damaging' => [
				'version' => '0.5.0',
			],
			'goodfaith' => [
				'version' => '0.5.0',
			],
		],
	],
	'srwiki' => [
		'models' => [
			'articletopic' => [
				'version' => '1.4.0',
			],
			'damaging' => [
				'version' => '0.5.1',
			],
			'goodfaith' => [
				'version' => '0.5.1',
			],
		],
	],
	'svwiki' => [
		'models' => [
			'articlequality' => [
				'version' => '0.8.0',
			],
			'damaging' => [
				'version' => '0.5.1',
			],
			'goodfaith' => [
				'version' => '0.5.1',
			],
		],
	],
	'tawiki' => [
		'models' => [
			'reverted' => [
				'version' => '0.5.0',
			],
		],
	],
	'testwiki' => [
		'models' => [
			'articlequality' => [
				'version' => '0.9.2',
			],
			'articletopic' => [
				'version' => '1.3.0',
			],
			'damaging' => [
				'version' => '0.0.3',
			],
			'draftquality' => [
				'version' => '0.2.1',
			],
			'drafttopic' => [
				'version' => '1.3.0',
			],
			'goodfaith' => [
				'version' => '0.0.3',
			],
			'reverted' => [
				'version' => '0.0.3',
			],
			'wp10' => [
				'version' => '0.9.2',
			],
		],
	],
	'trwiki' => [
		'models' => [
			'articlequality' => [
				'version' => '0.8.0',
			],
			'damaging' => [
				'version' => '0.5.1',
			],
			'goodfaith' => [
				'version' => '0.5.1',
			],
			'wp10' => [
				'version' => '0.8.0',
			],
		],
	],
	'ukwiki' => [
		'models' => [
			'articlequality' => [
				'version' => '0.8.0',
			],
			'articletopic' => [
				'version' => '1.3.0',
			],
			'damaging' => [
				'version' => '0.5.1',
			],
			'goodfaith' => [
				'version' => '0.5.1',
			],
		],
	],
	'viwiki' => [
		'models' => [
			'articletopic' => [
				'version' => '1.4.0',
			],
			'reverted' => [
				'version' => '0.5.0',
			],
		],
	],
	'wikidatawiki' => [
		'models' => [
			'damaging' => [
				'version' => '0.5.0',
			],
			'goodfaith' => [
				'version' => '0.5.0',
			],
			'itemquality' => [
				'version' => '0.5.0',
			],
			'itemtopic' => [
				'version' => '1.2.0',
			],
		],
	],
	'zhwiki' => [
		'models' => [
			'damaging' => [
				'version' => '0.5.0',
			],
			'goodfaith' => [
				'version' => '0.5.0',
			],
		],
	]
],
'wgOresModelThresholds' => [
	'arwiki' =>
		[
			'models' =>
				[
					'damaging' =>
						[
							'statistics' =>
								[
									'thresholds' =>
										[
											'false' =>
												[
													0 =>
														[
															'!f1' => 0.258,
															'!precision' => 0.151,
															'!recall' => 0.879,
															'accuracy' => 0.892,
															'f1' => 0.942,
															'filter_rate' => 0.124,
															'fpr' => 0.121,
															'match_rate' => 0.876,
															'precision' => 0.997,
															'recall' => 0.892,
															'threshold' => 0.833,
														],
												],
											'true' =>
												[
													0 =>
														[
															'!f1' => 0.927,
															'!precision' => 0.998,
															'!recall' => 0.866,
															'accuracy' => 0.867,
															'f1' => 0.226,
															'filter_rate' => 0.85,
															'fpr' => 0.134,
															'match_rate' => 0.15,
															'precision' => 0.129,
															'recall' => 0.906,
															'threshold' => 0.154,
														],
													1 => null,
												],
										],
								],
						],
				],
		],
	'bswiki' =>
		[
			'models' =>
				[
					'damaging' =>
						[
							'statistics' =>
								[
									'thresholds' =>
										[
											'false' =>
												[
													0 =>
														[
															'!f1' => 0.606,
															'!precision' => 0.476,
															'!recall' => 0.831,
															'accuracy' => 0.97,
															'f1' => 0.984,
															'filter_rate' => 0.049,
															'fpr' => 0.169,
															'match_rate' => 0.951,
															'precision' => 0.995,
															'recall' => 0.974,
															'threshold' => 0.799,
														],
												],
											'true' =>
												[
													0 =>
														[
															'!f1' => 0.979,
															'!precision' => 0.997,
															'!recall' => 0.961,
															'accuracy' => 0.96,
															'f1' => 0.556,
															'filter_rate' => 0.937,
															'fpr' => 0.039,
															'match_rate' => 0.063,
															'precision' => 0.403,
															'recall' => 0.9,
															'threshold' => 0.134,
														],
													1 =>
														[
															'!f1' => 0.989,
															'!precision' => 0.99,
															'!recall' => 0.987,
															'accuracy' => 0.978,
															'f1' => 0.632,
															'filter_rate' => 0.969,
															'fpr' => 0.013,
															'match_rate' => 0.031,
															'precision' => 0.602,
															'recall' => 0.665,
															'threshold' => 0.345,
														],
													2 =>
														[
															'!f1' => 0.989,
															'!precision' => 0.98,
															'!recall' => 0.998,
															'accuracy' => 0.978,
															'f1' => 0.446,
															'filter_rate' => 0.989,
															'fpr' => 0.002,
															'match_rate' => 0.011,
															'precision' => 0.804,
															'recall' => 0.308,
															'threshold' => 0.562,
														],
												],
										],
								],
						],
					'goodfaith' =>
						[
							'statistics' =>
								[
									'thresholds' =>
										[
											'false' =>
												[
													0 =>
														[
															'!f1' => 0.985,
															'!precision' => 0.996,
															'!recall' => 0.975,
															'accuracy' => 0.971,
															'f1' => 0.565,
															'filter_rate' => 0.956,
															'fpr' => 0.025,
															'match_rate' => 0.044,
															'precision' => 0.428,
															'recall' => 0.828,
															'threshold' => 0.001,
														],
													1 =>
														[
															'!f1' => 0.991,
															'!precision' => 0.991,
															'!recall' => 0.991,
															'accuracy' => 0.982,
															'f1' => 0.602,
															'filter_rate' => 0.977,
															'fpr' => 0.009,
															'match_rate' => 0.023,
															'precision' => 0.601,
															'recall' => 0.603,
															'threshold' => 0.315,
														],
													2 => null,
												],
											'true' =>
												[
													0 =>
														[
															'!f1' => 0.583,
															'!precision' => 0.46,
															'!recall' => 0.798,
															'accuracy' => 0.974,
															'f1' => 0.987,
															'filter_rate' => 0.039,
															'fpr' => 0.202,
															'match_rate' => 0.961,
															'precision' => 0.995,
															'recall' => 0.978,
															'threshold' => 0.999,
														],
												],
										],
								],
						],
				],
		],
	'cawiki' =>
		[
			'models' =>
				[
					'damaging' =>
						[
							'statistics' =>
								[
									'thresholds' =>
										[
											'false' =>
												[
													0 =>
														[
															'!f1' => 0.581,
															'!precision' => 0.476,
															'!recall' => 0.746,
															'accuracy' => 0.98,
															'f1' => 0.99,
															'filter_rate' => 0.03,
															'fpr' => 0.254,
															'match_rate' => 0.97,
															'precision' => 0.995,
															'recall' => 0.984,
															'threshold' => 0.464,
														],
												],
											'true' =>
												[
													0 =>
														[
															'!f1' => 0.978,
															'!precision' => 0.998,
															'!recall' => 0.96,
															'accuracy' => 0.959,
															'f1' => 0.453,
															'filter_rate' => 0.943,
															'fpr' => 0.04,
															'match_rate' => 0.057,
															'precision' => 0.303,
															'recall' => 0.902,
															'threshold' => 0.157,
														],
													1 =>
														[
															'!f1' => 0.992,
															'!precision' => 0.993,
															'!recall' => 0.992,
															'accuracy' => 0.985,
															'f1' => 0.622,
															'filter_rate' => 0.98,
															'fpr' => 0.008,
															'match_rate' => 0.02,
															'precision' => 0.602,
															'recall' => 0.644,
															'threshold' => 0.728,
														],
													2 =>
														[
															'!f1' => 0.993,
															'!precision' => 0.987,
															'!recall' => 0.999,
															'accuracy' => 0.986,
															'f1' => 0.451,
															'filter_rate' => 0.994,
															'fpr' => 0.001,
															'match_rate' => 0.006,
															'precision' => 0.901,
															'recall' => 0.301,
															'threshold' => 0.922,
														],
												],
										],
								],
						],
					'goodfaith' =>
						[
							'statistics' =>
								[
									'thresholds' =>
										[
											'false' =>
												[
													0 =>
														[
															'!f1' => 0.961,
															'!precision' => 1.0,
															'!recall' => 0.925,
															'accuracy' => 0.926,
															'f1' => 0.275,
															'filter_rate' => 0.912,
															'fpr' => 0.075,
															'match_rate' => 0.088,
															'precision' => 0.16,
															'recall' => 0.974,
															'threshold' => 0.033,
														],
													1 =>
														[
															'!f1' => 0.994,
															'!precision' => 0.995,
															'!recall' => 0.994,
															'accuracy' => 0.989,
															'f1' => 0.63,
															'filter_rate' => 0.984,
															'fpr' => 0.006,
															'match_rate' => 0.016,
															'precision' => 0.602,
															'recall' => 0.661,
															'threshold' => 0.734,
														],
													2 =>
														[
															'!f1' => 0.995,
															'!precision' => 0.99,
															'!recall' => 0.999,
															'accuracy' => 0.99,
															'f1' => 0.485,
															'filter_rate' => 0.995,
															'fpr' => 0.001,
															'match_rate' => 0.005,
															'precision' => 0.905,
															'recall' => 0.332,
															'threshold' => 0.919,
														],
												],
											'true' =>
												[
													0 =>
														[
															'!f1' => 0.533,
															'!precision' => 0.385,
															'!recall' => 0.868,
															'accuracy' => 0.978,
															'f1' => 0.989,
															'filter_rate' => 0.033,
															'fpr' => 0.132,
															'match_rate' => 0.967,
															'precision' => 0.998,
															'recall' => 0.98,
															'threshold' => 0.648,
														],
												],
										],
								],
						],
				],
		],
	'cswiki' =>
		[
			'models' =>
				[
					'damaging' =>
						[
							'statistics' =>
								[
									'thresholds' =>
										[
											'false' =>
												[
													0 =>
														[
															'!f1' => 0.266,
															'!precision' => 0.155,
															'!recall' => 0.918,
															'accuracy' => 0.774,
															'f1' => 0.866,
															'filter_rate' => 0.264,
															'fpr' => 0.082,
															'match_rate' => 0.736,
															'precision' => 0.995,
															'recall' => 0.767,
															'threshold' => 0.952,
														],
												],
											'true' =>
												[
													0 =>
														[
															'!f1' => 0.888,
															'!precision' => 0.994,
															'!recall' => 0.802,
															'accuracy' => 0.807,
															'f1' => 0.294,
															'filter_rate' => 0.771,
															'fpr' => 0.198,
															'match_rate' => 0.229,
															'precision' => 0.175,
															'recall' => 0.9,
															'threshold' => 0.056,
														],
													1 =>
														[
															'!f1' => 0.98,
															'!precision' => 0.973,
															'!recall' => 0.987,
															'accuracy' => 0.962,
															'f1' => 0.491,
															'filter_rate' => 0.969,
															'fpr' => 0.013,
															'match_rate' => 0.031,
															'precision' => 0.601,
															'recall' => 0.415,
															'threshold' => 0.596,
														],
													2 =>
														[
															'!f1' => 0.978,
															'!precision' => 0.956,
															'!recall' => 1.0,
															'accuracy' => 0.956,
															'f1' => 0.039,
															'filter_rate' => 0.999,
															'fpr' => 0.0,
															'match_rate' => 0.001,
															'precision' => 1.0,
															'recall' => 0.02,
															'threshold' => 0.91,
														],
												],
										],
								],
						],
					'goodfaith' =>
						[
							'statistics' =>
								[
									'thresholds' =>
										[
											'false' =>
												[
													0 =>
														[
															'!f1' => 0.966,
															'!precision' => 0.998,
															'!recall' => 0.935,
															'accuracy' => 0.935,
															'f1' => 0.383,
															'filter_rate' => 0.917,
															'fpr' => 0.065,
															'match_rate' => 0.083,
															'precision' => 0.243,
															'recall' => 0.901,
															'threshold' => 0.05,
														],
													1 =>
														[
															'!f1' => 0.991,
															'!precision' => 0.989,
															'!recall' => 0.992,
															'accuracy' => 0.982,
															'f1' => 0.563,
															'filter_rate' => 0.98,
															'fpr' => 0.008,
															'match_rate' => 0.02,
															'precision' => 0.601,
															'recall' => 0.53,
															'threshold' => 0.519,
														],
													2 =>
														[
															'!f1' => 0.989,
															'!precision' => 0.979,
															'!recall' => 1.0,
															'accuracy' => 0.979,
															'f1' => 0.121,
															'filter_rate' => 0.999,
															'fpr' => 0.0,
															'match_rate' => 0.001,
															'precision' => 1.0,
															'recall' => 0.064,
															'threshold' => 0.882,
														],
												],
											'true' =>
												[
													0 =>
														[
															'!f1' => 0.526,
															'!precision' => 0.394,
															'!recall' => 0.792,
															'accuracy' => 0.968,
															'f1' => 0.983,
															'filter_rate' => 0.045,
															'fpr' => 0.208,
															'match_rate' => 0.955,
															'precision' => 0.995,
															'recall' => 0.972,
															'threshold' => 0.704,
														],
												],
										],
								],
						],
				],
		],
	'enwiki' =>
		[
			'models' =>
				[
					'damaging' =>
						[
							'statistics' =>
								[
									'thresholds' =>
										[
											'false' =>
												[
													0 =>
														[
															'!f1' => 0.337,
															'!precision' => 0.218,
															'!recall' => 0.742,
															'accuracy' => 0.9,
															'f1' => 0.946,
															'filter_rate' => 0.116,
															'fpr' => 0.258,
															'match_rate' => 0.884,
															'precision' => 0.99,
															'recall' => 0.906,
															'threshold' => 0.699,
														],
												],
											'true' =>
												[
													0 =>
														[
															'!f1' => 0.904,
															'!precision' => 0.994,
															'!recall' => 0.829,
															'accuracy' => 0.83,
															'f1' => 0.256,
															'filter_rate' => 0.805,
															'fpr' => 0.171,
															'match_rate' => 0.195,
															'precision' => 0.151,
															'recall' => 0.859,
															'threshold' => 0.149,
														],
													1 =>
														[
															'!f1' => 0.981,
															'!precision' => 0.981,
															'!recall' => 0.98,
															'accuracy' => 0.962,
															'f1' => 0.455,
															'filter_rate' => 0.965,
															'fpr' => 0.02,
															'match_rate' => 0.035,
															'precision' => 0.451,
															'recall' => 0.459,
															'threshold' => 0.629,
														],
													2 =>
														[
															'!f1' => 0.983,
															'!precision' => 0.968,
															'!recall' => 1.0,
															'accuracy' => 0.968,
															'f1' => 0.099,
															'filter_rate' => 0.998,
															'fpr' => 0.0,
															'match_rate' => 0.002,
															'precision' => 0.945,
															'recall' => 0.052,
															'threshold' => 0.944,
														],
												],
										],
								],
						],
					'goodfaith' =>
						[
							'statistics' =>
								[
									'thresholds' =>
										[
											'false' =>
												[
													0 =>
														[
															'!f1' => 0.91,
															'!precision' => 0.995,
															'!recall' => 0.838,
															'accuracy' => 0.839,
															'f1' => 0.265,
															'filter_rate' => 0.814,
															'fpr' => 0.162,
															'match_rate' => 0.186,
															'precision' => 0.156,
															'recall' => 0.882,
															'threshold' => 0.075,
														],
													1 =>
														[
															'!f1' => 0.985,
															'!precision' => 0.979,
															'!recall' => 0.992,
															'accuracy' => 0.971,
															'f1' => 0.46,
															'filter_rate' => 0.98,
															'fpr' => 0.008,
															'match_rate' => 0.02,
															'precision' => 0.604,
															'recall' => 0.372,
															'threshold' => 0.647,
														],
													2 =>
														[
															'!f1' => 0.984,
															'!precision' => 0.968,
															'!recall' => 1.0,
															'accuracy' => 0.968,
															'f1' => 0.072,
															'filter_rate' => 0.999,
															'fpr' => 0.0,
															'match_rate' => 0.001,
															'precision' => 0.923,
															'recall' => 0.037,
															'threshold' => 0.935,
														],
												],
											'true' =>
												[
													0 =>
														[
															'!f1' => 0.4,
															'!precision' => 0.276,
															'!recall' => 0.722,
															'accuracy' => 0.929,
															'f1' => 0.962,
															'filter_rate' => 0.086,
															'fpr' => 0.278,
															'match_rate' => 0.914,
															'precision' => 0.99,
															'recall' => 0.936,
															'threshold' => 0.777,
														],
												],
										],
								],
						],
				],
		],
	'eswiki' =>
		[
			'models' =>
				[
					'damaging' =>
						[
							'statistics' =>
								[
									'thresholds' =>
										[
											'false' =>
												[
													0 =>
														[
															'!f1' => 0.408,
															'!precision' => 0.258,
															'!recall' => 0.974,
															'accuracy' => 0.688,
															'f1' => 0.788,
															'filter_rate' => 0.417,
															'fpr' => 0.026,
															'match_rate' => 0.583,
															'precision' => 0.995,
															'recall' => 0.652,
															'threshold' => 0.796,
														],
												],
											'true' =>
												[
													0 =>
														[
															'!f1' => 0.871,
															'!precision' => 0.984,
															'!recall' => 0.781,
															'accuracy' => 0.794,
															'f1' => 0.491,
															'filter_rate' => 0.706,
															'fpr' => 0.219,
															'match_rate' => 0.294,
															'precision' => 0.338,
															'recall' => 0.9,
															'threshold' => 0.533,
														],
													1 =>
														[
															'!f1' => 0.95,
															'!precision' => 0.948,
															'!recall' => 0.952,
															'accuracy' => 0.911,
															'f1' => 0.59,
															'filter_rate' => 0.893,
															'fpr' => 0.048,
															'match_rate' => 0.107,
															'precision' => 0.6,
															'recall' => 0.581,
															'threshold' => 0.847,
														],
													2 =>
														[
															'!f1' => 0.949,
															'!precision' => 0.905,
															'!recall' => 0.998,
															'accuracy' => 0.905,
															'f1' => 0.262,
															'filter_rate' => 0.981,
															'fpr' => 0.002,
															'match_rate' => 0.019,
															'precision' => 0.904,
															'recall' => 0.153,
															'threshold' => 0.963,
														],
												],
										],
								],
						],
					'goodfaith' =>
						[
							'statistics' =>
								[
									'thresholds' =>
										[
											'false' =>
												[
													0 =>
														[
															'!f1' => 0.557,
															'!precision' => 0.411,
															'!recall' => 0.861,
															'accuracy' => 0.849,
															'f1' => 0.909,
															'filter_rate' => 0.231,
															'fpr' => 0.139,
															'match_rate' => 0.769,
															'precision' => 0.98,
															'recall' => 0.847,
															'threshold' => 0.548,
														],
												],
											'true' =>
												[
													0 =>
														[
															'!f1' => 0.947,
															'!precision' => 0.9,
															'!recall' => 1.0,
															'accuracy' => 0.901,
															'f1' => 0.184,
															'filter_rate' => 0.989,
															'fpr' => 0.0,
															'match_rate' => 0.011,
															'precision' => 1.0,
															'recall' => 0.101,
															'threshold' => 0.997,
														],
												],
										],
								],
						],
				],
		],
	'eswikibooks' =>
		[
			'models' =>
				[
					'damaging' =>
						[
							'statistics' =>
								[
									'thresholds' =>
										[
											'false' =>
												[
													0 =>
														[
															'!f1' => 0.494,
															'!precision' => 0.331,
															'!recall' => 0.97,
															'accuracy' => 0.776,
															'f1' => 0.856,
															'filter_rate' => 0.33,
															'fpr' => 0.03,
															'match_rate' => 0.67,
															'precision' => 0.995,
															'recall' => 0.751,
															'threshold' => 0.885,
														],
												],
											'true' =>
												[
													0 =>
														[
															'!f1' => 0.953,
															'!precision' => 0.979,
															'!recall' => 0.929,
															'accuracy' => 0.919,
															'f1' => 0.702,
															'filter_rate' => 0.841,
															'fpr' => 0.071,
															'match_rate' => 0.159,
															'precision' => 0.6,
															'recall' => 0.845,
															'threshold' => 0.669,
														],
													1 =>
														[
															'!f1' => 0.965,
															'!precision' => 0.938,
															'!recall' => 0.993,
															'accuracy' => 0.936,
															'f1' => 0.632,
															'filter_rate' => 0.939,
															'fpr' => 0.007,
															'match_rate' => 0.061,
															'precision' => 0.901,
															'recall' => 0.487,
															'threshold' => 0.94,
														],
												],
										],
								],
						],
					'goodfaith' =>
						[
							'statistics' =>
								[
									'thresholds' =>
										[
											'false' =>
												[
													0 =>
														[
															'!f1' => 0.966,
															'!precision' => 0.991,
															'!recall' => 0.943,
															'accuracy' => 0.94,
															'f1' => 0.722,
															'filter_rate' => 0.87,
															'fpr' => 0.057,
															'match_rate' => 0.13,
															'precision' => 0.601,
															'recall' => 0.905,
															'threshold' => 0.002,
														],
													1 =>
														[
															'!f1' => 0.973,
															'!precision' => 0.952,
															'!recall' => 0.996,
															'accuracy' => 0.95,
															'f1' => 0.614,
															'filter_rate' => 0.956,
															'fpr' => 0.004,
															'match_rate' => 0.044,
															'precision' => 0.91,
															'recall' => 0.463,
															'threshold' => 1.0,
														],
												],
											'true' =>
												[
													0 =>
														[
															'!f1' => 0.722,
															'!precision' => 0.601,
															'!recall' => 0.905,
															'accuracy' => 0.94,
															'f1' => 0.966,
															'filter_rate' => 0.13,
															'fpr' => 0.095,
															'match_rate' => 0.87,
															'precision' => 0.991,
															'recall' => 0.943,
															'threshold' => 0.999,
														],
												],
										],
								],
						],
				],
		],
	'eswikiquote' =>
		[
			'models' =>
				[
					'damaging' =>
						[
							'statistics' =>
								[
									'thresholds' =>
										[
											'false' =>
												[
													0 =>
														[
															'!f1' => 0.533,
															'!precision' => 0.377,
															'!recall' => 0.909,
															'accuracy' => 0.861,
															'f1' => 0.919,
															'filter_rate' => 0.21,
															'fpr' => 0.091,
															'match_rate' => 0.79,
															'precision' => 0.99,
															'recall' => 0.857,
															'threshold' => 0.779,
														],
												],
											'true' =>
												[
													0 =>
														[
															'!f1' => 0.922,
															'!precision' => 0.989,
															'!recall' => 0.863,
															'accuracy' => 0.867,
															'f1' => 0.541,
															'filter_rate' => 0.797,
															'fpr' => 0.137,
															'match_rate' => 0.203,
															'precision' => 0.386,
															'recall' => 0.901,
															'threshold' => 0.24,
														],
													1 =>
														[
															'!f1' => 0.963,
															'!precision' => 0.97,
															'!recall' => 0.956,
															'accuracy' => 0.933,
															'f1' => 0.645,
															'filter_rate' => 0.899,
															'fpr' => 0.044,
															'match_rate' => 0.101,
															'precision' => 0.601,
															'recall' => 0.695,
															'threshold' => 0.709,
														],
													2 =>
														[
															'!f1' => 0.963,
															'!precision' => 0.929,
															'!recall' => 1.0,
															'accuracy' => 0.93,
															'f1' => 0.329,
															'filter_rate' => 0.982,
															'fpr' => 0.0,
															'match_rate' => 0.018,
															'precision' => 0.982,
															'recall' => 0.198,
															'threshold' => 0.961,
														],
												],
										],
								],
						],
					'goodfaith' =>
						[
							'statistics' =>
								[
									'thresholds' =>
										[
											'false' =>
												[
													0 =>
														[
															'!f1' => 0.961,
															'!precision' => 0.986,
															'!recall' => 0.938,
															'accuracy' => 0.929,
															'f1' => 0.594,
															'filter_rate' => 0.89,
															'fpr' => 0.062,
															'match_rate' => 0.11,
															'precision' => 0.471,
															'recall' => 0.802,
															'threshold' => 0.001,
														],
													1 =>
														[
															'!f1' => 0.973,
															'!precision' => 0.976,
															'!recall' => 0.97,
															'accuracy' => 0.95,
															'f1' => 0.628,
															'filter_rate' => 0.929,
															'fpr' => 0.03,
															'match_rate' => 0.071,
															'precision' => 0.6,
															'recall' => 0.659,
															'threshold' => 0.066,
														],
												],
											'true' =>
												[
													0 =>
														[
															'!f1' => 0.628,
															'!precision' => 0.559,
															'!recall' => 0.716,
															'accuracy' => 0.945,
															'f1' => 0.971,
															'filter_rate' => 0.083,
															'fpr' => 0.284,
															'match_rate' => 0.917,
															'precision' => 0.98,
															'recall' => 0.961,
															'threshold' => 0.986,
														],
												],
										],
								],
						],
				],
		],
	'etwiki' =>
		[
			'models' =>
				[
					'damaging' =>
						[
							'statistics' =>
								[
									'thresholds' =>
										[
											'false' =>
												[
													0 =>
														[
															'!f1' => 0.379,
															'!precision' => 0.246,
															'!recall' => 0.827,
															'accuracy' => 0.929,
															'f1' => 0.962,
															'filter_rate' => 0.088,
															'fpr' => 0.173,
															'match_rate' => 0.912,
															'precision' => 0.995,
															'recall' => 0.932,
															'threshold' => 0.788,
														],
												],
											'true' =>
												[
													0 =>
														[
															'!f1' => 0.949,
															'!precision' => 0.997,
															'!recall' => 0.906,
															'accuracy' => 0.905,
															'f1' => 0.333,
															'filter_rate' => 0.884,
															'fpr' => 0.094,
															'match_rate' => 0.116,
															'precision' => 0.204,
															'recall' => 0.903,
															'threshold' => 0.136,
														],
													1 =>
														[
															'!f1' => 0.989,
															'!precision' => 0.986,
															'!recall' => 0.992,
															'accuracy' => 0.978,
															'f1' => 0.529,
															'filter_rate' => 0.979,
															'fpr' => 0.008,
															'match_rate' => 0.021,
															'precision' => 0.601,
															'recall' => 0.473,
															'threshold' => 0.599,
														],
													2 =>
														[
															'!f1' => 0.989,
															'!precision' => 0.978,
															'!recall' => 1.0,
															'accuracy' => 0.978,
															'f1' => 0.28,
															'filter_rate' => 0.996,
															'fpr' => 0.0,
															'match_rate' => 0.004,
															'precision' => 0.988,
															'recall' => 0.163,
															'threshold' => 0.927,
														],
												],
										],
								],
						],
					'goodfaith' =>
						[
							'statistics' =>
								[
									'thresholds' =>
										[
											'false' =>
												[
													0 =>
														[
															'!f1' => 0.974,
															'!precision' => 0.998,
															'!recall' => 0.95,
															'accuracy' => 0.949,
															'f1' => 0.363,
															'filter_rate' => 0.936,
															'fpr' => 0.05,
															'match_rate' => 0.064,
															'precision' => 0.227,
															'recall' => 0.906,
															'threshold' => 0.057,
														],
													1 =>
														[
															'!f1' => 0.994,
															'!precision' => 0.994,
															'!recall' => 0.993,
															'accuracy' => 0.988,
															'f1' => 0.619,
															'filter_rate' => 0.983,
															'fpr' => 0.007,
															'match_rate' => 0.017,
															'precision' => 0.606,
															'recall' => 0.633,
															'threshold' => 0.376,
														],
													2 =>
														[
															'!f1' => 0.995,
															'!precision' => 0.991,
															'!recall' => 0.999,
															'accuracy' => 0.99,
															'f1' => 0.583,
															'filter_rate' => 0.993,
															'fpr' => 0.001,
															'match_rate' => 0.007,
															'precision' => 0.909,
															'recall' => 0.429,
															'threshold' => 0.742,
														],
												],
											'true' =>
												[
													0 =>
														[
															'!f1' => 0.597,
															'!precision' => 0.521,
															'!recall' => 0.698,
															'accuracy' => 0.985,
															'f1' => 0.992,
															'filter_rate' => 0.021,
															'fpr' => 0.302,
															'match_rate' => 0.979,
															'precision' => 0.995,
															'recall' => 0.99,
															'threshold' => 0.716,
														],
												],
										],
								],
						],
				],
		],
	'fawiki' =>
		[
			'models' =>
				[
					'damaging' =>
						[
							'statistics' =>
								[
									'thresholds' =>
										[
											'false' =>
												[
													0 =>
														[
															'!f1' => 0.42,
															'!precision' => 0.279,
															'!recall' => 0.847,
															'accuracy' => 0.93,
															'f1' => 0.963,
															'filter_rate' => 0.09,
															'fpr' => 0.153,
															'match_rate' => 0.91,
															'precision' => 0.995,
															'recall' => 0.933,
															'threshold' => 0.587,
														],
												],
											'true' =>
												[
													0 =>
														[
															'!f1' => 0.912,
															'!precision' => 1.0,
															'!recall' => 0.839,
															'accuracy' => 0.843,
															'f1' => 0.274,
															'filter_rate' => 0.814,
															'fpr' => 0.161,
															'match_rate' => 0.186,
															'precision' => 0.159,
															'recall' => 0.995,
															'threshold' => 0.027,
														],
													1 =>
														[
															'!f1' => 0.985,
															'!precision' => 0.971,
															'!recall' => 0.999,
															'accuracy' => 0.971,
															'f1' => 0.051,
															'filter_rate' => 0.999,
															'fpr' => 0.001,
															'match_rate' => 0.001,
															'precision' => 0.604,
															'recall' => 0.027,
															'threshold' => 0.948,
														],
												],
										],
								],
						],
					'goodfaith' =>
						[
							'statistics' =>
								[
									'thresholds' =>
										[
											'false' =>
												[
													0 =>
														[
															'!f1' => 0.954,
															'!precision' => 0.998,
															'!recall' => 0.914,
															'accuracy' => 0.914,
															'f1' => 0.259,
															'filter_rate' => 0.9,
															'fpr' => 0.086,
															'match_rate' => 0.1,
															'precision' => 0.151,
															'recall' => 0.912,
															'threshold' => 0.146,
														],
													1 =>
														[
															'!f1' => 0.991,
															'!precision' => 0.985,
															'!recall' => 0.998,
															'accuracy' => 0.983,
															'f1' => 0.145,
															'filter_rate' => 0.997,
															'fpr' => 0.002,
															'match_rate' => 0.003,
															'precision' => 0.451,
															'recall' => 0.086,
															'threshold' => 0.742,
														],
													2 =>
														[
															'!f1' => 0.992,
															'!precision' => 0.984,
															'!recall' => 1.0,
															'accuracy' => 0.984,
															'f1' => 0.012,
															'filter_rate' => 1.0,
															'fpr' => 0.0,
															'match_rate' => 0.0,
															'precision' => 1.0,
															'recall' => 0.006,
															'threshold' => 0.898,
														],
												],
											'true' =>
												[
													0 =>
														[
															'!f1' => 0.302,
															'!precision' => 0.191,
															'!recall' => 0.718,
															'accuracy' => 0.945,
															'f1' => 0.971,
															'filter_rate' => 0.062,
															'fpr' => 0.282,
															'match_rate' => 0.938,
															'precision' => 0.995,
															'recall' => 0.949,
															'threshold' => 0.673,
														],
												],
										],
								],
						],
				],
		],
	'fiwiki' =>
		[
			'models' =>
				[
					'damaging' =>
						[
							'statistics' =>
								[
									'thresholds' =>
										[
											'false' =>
												[
													0 =>
														[
															'!f1' => 0.398,
															'!precision' => 0.254,
															'!recall' => 0.917,
															'accuracy' => 0.862,
															'f1' => 0.922,
															'filter_rate' => 0.179,
															'fpr' => 0.083,
															'match_rate' => 0.821,
															'precision' => 0.995,
															'recall' => 0.859,
															'threshold' => 0.7930261,
														],
												],
											'true' =>
												[
													0 =>
														[
															'!f1' => 0.929,
															'!precision' => 0.994,
															'!recall' => 0.871,
															'accuracy' => 0.873,
															'f1' => 0.412,
															'filter_rate' => 0.833,
															'fpr' => 0.129,
															'match_rate' => 0.167,
															'precision' => 0.267,
															'recall' => 0.9,
															'threshold' => 0.2502732,
														],
													1 =>
														[
															'!f1' => 0.978,
															'!precision' => 0.972,
															'!recall' => 0.984,
															'accuracy' => 0.958,
															'f1' => 0.516,
															'filter_rate' => 0.963,
															'fpr' => 0.016,
															'match_rate' => 0.037,
															'precision' => 0.602,
															'recall' => 0.452,
															'threshold' => 0.8106125,
														],
													2 =>
														[
															'!f1' => 0.977,
															'!precision' => 0.956,
															'!recall' => 0.999,
															'accuracy' => 0.956,
															'f1' => 0.214,
															'filter_rate' => 0.993,
															'fpr' => 0.001,
															'match_rate' => 0.007,
															'precision' => 0.902,
															'recall' => 0.122,
															'threshold' => 0.9242385,
														],
												],
										],
								],
						],
					'goodfaith' =>
						[
							'statistics' =>
								[
									'thresholds' =>
										[
											'false' =>
												[
													0 =>
														[
															'!f1' => null,
															'!precision' => null,
															'!recall' => 0.0,
															'accuracy' => 0.033,
															'f1' => 0.063,
															'filter_rate' => 0.0,
															'fpr' => 1.0,
															'match_rate' => 1.0,
															'precision' => 0.033,
															'recall' => 1.0,
															'threshold' => 0.0,
														],
													1 =>
														[
															'!f1' => 0.986,
															'!precision' => 0.982,
															'!recall' => 0.99,
															'accuracy' => 0.973,
															'f1' => 0.527,
															'filter_rate' => 0.975,
															'fpr' => 0.01,
															'match_rate' => 0.025,
															'precision' => 0.601,
															'recall' => 0.469,
															'threshold' => 0.263,
														],
													2 => null,
												],
											'true' =>
												[
													0 => null,
												],
										],
								],
						],
				],
		],
	'frwiki' =>
		[
			'models' =>
				[
					'damaging' =>
						[
							'statistics' =>
								[
									'thresholds' =>
										[
											'false' =>
												[
													0 =>
														[
															'!f1' => 0.178,
															'!precision' => 0.099,
															'!recall' => 0.871,
															'accuracy' => 0.768,
															'f1' => 0.865,
															'filter_rate' => 0.253,
															'fpr' => 0.129,
															'match_rate' => 0.747,
															'precision' => 0.995,
															'recall' => 0.765,
															'threshold' => 0.908,
														],
												],
											'true' =>
												[
													0 =>
														[
															'!f1' => 0.811,
															'!precision' => 0.996,
															'!recall' => 0.684,
															'accuracy' => 0.69,
															'f1' => 0.143,
															'filter_rate' => 0.667,
															'fpr' => 0.316,
															'match_rate' => 0.333,
															'precision' => 0.078,
															'recall' => 0.901,
															'threshold' => 0.08,
														],
													1 =>
														[
															'!f1' => 0.986,
															'!precision' => 0.977,
															'!recall' => 0.996,
															'accuracy' => 0.973,
															'f1' => 0.314,
															'filter_rate' => 0.99,
															'fpr' => 0.004,
															'match_rate' => 0.01,
															'precision' => 0.602,
															'recall' => 0.213,
															'threshold' => 0.77,
														],
													2 =>
														[
															'!f1' => 0.987,
															'!precision' => 0.974,
															'!recall' => 1.0,
															'accuracy' => 0.973,
															'f1' => 0.166,
															'filter_rate' => 0.997,
															'fpr' => 0.0,
															'match_rate' => 0.003,
															'precision' => 0.853,
															'recall' => 0.092,
															'threshold' => 0.837,
														],
												],
										],
								],
						],
					'goodfaith' =>
						[
							'statistics' =>
								[
									'thresholds' =>
										[
											'false' =>
												[
													0 =>
														[
															'!f1' => 0.909,
															'!precision' => 0.997,
															'!recall' => 0.835,
															'accuracy' => 0.836,
															'f1' => 0.191,
															'filter_rate' => 0.819,
															'fpr' => 0.165,
															'match_rate' => 0.181,
															'precision' => 0.107,
															'recall' => 0.903,
															'threshold' => 0.04,
														],
													1 =>
														[
															'!f1' => 0.99,
															'!precision' => 0.985,
															'!recall' => 0.995,
															'accuracy' => 0.981,
															'f1' => 0.427,
															'filter_rate' => 0.988,
															'fpr' => 0.005,
															'match_rate' => 0.012,
															'precision' => 0.609,
															'recall' => 0.329,
															'threshold' => 0.709,
														],
													2 =>
														[
															'!f1' => 0.99,
															'!precision' => 0.981,
															'!recall' => 1.0,
															'accuracy' => 0.981,
															'f1' => 0.225,
															'filter_rate' => 0.997,
															'fpr' => 0.0,
															'match_rate' => 0.003,
															'precision' => 0.942,
															'recall' => 0.128,
															'threshold' => 0.888,
														],
												],
											'true' =>
												[
													0 =>
														[
															'!f1' => 0.365,
															'!precision' => 0.238,
															'!recall' => 0.785,
															'accuracy' => 0.942,
															'f1' => 0.969,
															'filter_rate' => 0.071,
															'fpr' => 0.215,
															'match_rate' => 0.929,
															'precision' => 0.995,
															'recall' => 0.945,
															'threshold' => 0.778,
														],
												],
										],
								],
						],
				],
		],
	'hewiki' =>
		[
			'models' =>
				[
					'damaging' =>
						[
							'statistics' =>
								[
									'thresholds' =>
										[
											'false' =>
												[
													0 =>
														[
															'!f1' => 0.125,
															'!precision' => 0.067,
															'!recall' => 0.974,
															'accuracy' => 0.369,
															'f1' => 0.506,
															'filter_rate' => 0.675,
															'fpr' => 0.026,
															'match_rate' => 0.325,
															'precision' => 0.996,
															'recall' => 0.339,
															'threshold' => 0.997,
														],
												],
											'true' =>
												[
													0 =>
														[
															'!f1' => 0.973,
															'!precision' => 0.976,
															'!recall' => 0.97,
															'accuracy' => 0.949,
															'f1' => 0.48,
															'filter_rate' => 0.948,
															'fpr' => 0.03,
															'match_rate' => 0.052,
															'precision' => 0.455,
															'recall' => 0.509,
															'threshold' => 0.203,
														],
													1 =>
														[
															'!f1' => 0.977,
															'!precision' => 0.955,
															'!recall' => 1.0,
															'accuracy' => 0.955,
															'f1' => 0.044,
															'filter_rate' => 0.999,
															'fpr' => 0.0,
															'match_rate' => 0.001,
															'precision' => 1.0,
															'recall' => 0.022,
															'threshold' => 0.766,
														],
												],
										],
								],
						],
					'goodfaith' =>
						[
							'statistics' =>
								[
									'thresholds' =>
										[
											'false' =>
												[
													0 =>
														[
															'!f1' => 0.984,
															'!precision' => 0.983,
															'!recall' => 0.986,
															'accuracy' => 0.969,
															'f1' => 0.426,
															'filter_rate' => 0.975,
															'fpr' => 0.014,
															'match_rate' => 0.025,
															'precision' => 0.451,
															'recall' => 0.404,
															'threshold' => 0.053,
														],
													1 =>
														[
															'!f1' => 0.986,
															'!precision' => 0.972,
															'!recall' => 1.0,
															'accuracy' => 0.972,
															'f1' => 0.027,
															'filter_rate' => 1.0,
															'fpr' => 0.0,
															'match_rate' => 0.0,
															'precision' => 1.0,
															'recall' => 0.013,
															'threshold' => 0.999,
														],
												],
											'true' =>
												[
													0 =>
														[
															'!f1' => 0.291,
															'!precision' => 0.175,
															'!recall' => 0.848,
															'accuracy' => 0.883,
															'f1' => 0.936,
															'filter_rate' => 0.136,
															'fpr' => 0.152,
															'match_rate' => 0.864,
															'precision' => 0.995,
															'recall' => 0.884,
															'threshold' => 1.0,
														],
												],
										],
								],
						],
				],
		],
	'huwiki' =>
		[
			'models' =>
				[
					'damaging' =>
						[
							'statistics' =>
								[
									'thresholds' =>
										[
											'false' =>
												[
													0 =>
														[
															'!f1' => 0.165,
															'!precision' => 0.091,
															'!recall' => 0.922,
															'accuracy' => 0.898,
															'f1' => 0.946,
															'filter_rate' => 0.111,
															'fpr' => 0.078,
															'match_rate' => 0.889,
															'precision' => 0.999,
															'recall' => 0.898,
															'threshold' => 0.953,
														],
												],
											'true' =>
												[
													0 =>
														[
															'!f1' => 0.972,
															'!precision' => 0.998,
															'!recall' => 0.946,
															'accuracy' => 0.946,
															'f1' => 0.26,
															'filter_rate' => 0.938,
															'fpr' => 0.054,
															'match_rate' => 0.062,
															'precision' => 0.153,
															'recall' => 0.871,
															'threshold' => 0.073,
														],
													1 =>
														[
															'!f1' => 0.995,
															'!precision' => 0.992,
															'!recall' => 0.998,
															'accuracy' => 0.99,
															'f1' => 0.416,
															'filter_rate' => 0.994,
															'fpr' => 0.002,
															'match_rate' => 0.006,
															'precision' => 0.606,
															'recall' => 0.317,
															'threshold' => 0.768,
														],
													2 =>
														[
															'!f1' => 0.995,
															'!precision' => 0.99,
															'!recall' => 1.0,
															'accuracy' => 0.99,
															'f1' => 0.177,
															'filter_rate' => 0.999,
															'fpr' => 0.0,
															'match_rate' => 0.001,
															'precision' => 0.931,
															'recall' => 0.098,
															'threshold' => 0.873,
														],
												],
										],
								],
						],
					'goodfaith' =>
						[
							'statistics' =>
								[
									'thresholds' =>
										[
											'false' =>
												[
													0 =>
														[
															'!f1' => 0.981,
															'!precision' => 0.999,
															'!recall' => 0.963,
															'accuracy' => 0.963,
															'f1' => 0.259,
															'filter_rate' => 0.957,
															'fpr' => 0.037,
															'match_rate' => 0.043,
															'precision' => 0.153,
															'recall' => 0.841,
															'threshold' => 0.079,
														],
													1 =>
														[
															'!f1' => 0.997,
															'!precision' => 0.995,
															'!recall' => 0.998,
															'accuracy' => 0.993,
															'f1' => 0.439,
															'filter_rate' => 0.996,
															'fpr' => 0.002,
															'match_rate' => 0.004,
															'precision' => 0.618,
															'recall' => 0.341,
															'threshold' => 0.736,
														],
													2 =>
														[
															'!f1' => 0.997,
															'!precision' => 0.993,
															'!recall' => 1.0,
															'accuracy' => 0.993,
															'f1' => 0.215,
															'filter_rate' => 0.999,
															'fpr' => 0.0,
															'match_rate' => 0.001,
															'precision' => 0.878,
															'recall' => 0.123,
															'threshold' => 0.835,
														],
												],
											'true' =>
												[
													0 =>
														[
															'!f1' => 0.16,
															'!precision' => 0.088,
															'!recall' => 0.884,
															'accuracy' => 0.928,
															'f1' => 0.962,
															'filter_rate' => 0.078,
															'fpr' => 0.116,
															'match_rate' => 0.922,
															'precision' => 0.999,
															'recall' => 0.928,
															'threshold' => 0.951,
														],
												],
										],
								],
						],
				],
		],
	'itwiki' =>
		[
			'models' =>
				[
					'damaging' =>
						[
							'statistics' =>
								[
									'thresholds' =>
										[
											'false' =>
												[
													0 =>
														[
															'!f1' => 0.272,
															'!precision' => 0.16,
															'!recall' => 0.901,
															'accuracy' => 0.814,
															'f1' => 0.893,
															'filter_rate' => 0.217,
															'fpr' => 0.099,
															'match_rate' => 0.783,
															'precision' => 0.995,
															'recall' => 0.81,
															'threshold' => 0.854,
														],
												],
											'true' =>
												[
													0 =>
														[
															'!f1' => 0.893,
															'!precision' => 0.995,
															'!recall' => 0.81,
															'accuracy' => 0.814,
															'f1' => 0.272,
															'filter_rate' => 0.783,
															'fpr' => 0.19,
															'match_rate' => 0.217,
															'precision' => 0.16,
															'recall' => 0.901,
															'threshold' => 0.147,
														],
													1 =>
														[
															'!f1' => 0.979,
															'!precision' => 0.972,
															'!recall' => 0.986,
															'accuracy' => 0.959,
															'f1' => 0.353,
															'filter_rate' => 0.975,
															'fpr' => 0.014,
															'match_rate' => 0.025,
															'precision' => 0.452,
															'recall' => 0.29,
															'threshold' => 0.67,
														],
													2 =>
														[
															'!f1' => 0.982,
															'!precision' => 0.965,
															'!recall' => 0.999,
															'accuracy' => 0.964,
															'f1' => 0.187,
															'filter_rate' => 0.995,
															'fpr' => 0.001,
															'match_rate' => 0.005,
															'precision' => 0.768,
															'recall' => 0.106,
															'threshold' => 0.835,
														],
												],
										],
								],
						],
					'goodfaith' =>
						[
							'statistics' =>
								[
									'thresholds' =>
										[
											'false' =>
												[
													0 =>
														[
															'!f1' => 0.954,
															'!precision' => 0.995,
															'!recall' => 0.916,
															'accuracy' => 0.913,
															'f1' => 0.254,
															'filter_rate' => 0.902,
															'fpr' => 0.084,
															'match_rate' => 0.098,
															'precision' => 0.152,
															'recall' => 0.772,
															'threshold' => 0.144,
														],
													1 =>
														[
															'!f1' => 0.991,
															'!precision' => 0.985,
															'!recall' => 0.997,
															'accuracy' => 0.983,
															'f1' => 0.35,
															'filter_rate' => 0.992,
															'fpr' => 0.003,
															'match_rate' => 0.008,
															'precision' => 0.613,
															'recall' => 0.245,
															'threshold' => 0.653,
														],
													2 =>
														[
															'!f1' => 0.991,
															'!precision' => 0.983,
															'!recall' => 1.0,
															'accuracy' => 0.983,
															'f1' => 0.223,
															'filter_rate' => 0.997,
															'fpr' => 0.0,
															'match_rate' => 0.003,
															'precision' => 0.919,
															'recall' => 0.127,
															'threshold' => 0.837,
														],
												],
											'true' =>
												[
													0 =>
														[
															'!f1' => 0.268,
															'!precision' => 0.163,
															'!recall' => 0.763,
															'accuracy' => 0.92,
															'f1' => 0.958,
															'filter_rate' => 0.09,
															'fpr' => 0.237,
															'match_rate' => 0.91,
															'precision' => 0.995,
															'recall' => 0.923,
															'threshold' => 0.846,
														],
												],
										],
								],
						],
				],
		],
	'kowiki' =>
		[
			'models' =>
				[
					'damaging' =>
						[
							'statistics' =>
								[
									'thresholds' =>
										[
											'false' =>
												[
													0 =>
														[
															'!f1' => 0.236,
															'!precision' => 0.138,
															'!recall' => 0.8,
															'accuracy' => 0.799,
															'f1' => 0.885,
															'filter_rate' => 0.224,
															'fpr' => 0.2,
															'match_rate' => 0.776,
															'precision' => 0.99,
															'recall' => 0.799,
															'threshold' => 0.72,
														],
												],
											'true' =>
												[
													0 =>
														[
															'!f1' => 0.834,
															'!precision' => 0.994,
															'!recall' => 0.718,
															'accuracy' => 0.725,
															'f1' => 0.202,
															'filter_rate' => 0.694,
															'fpr' => 0.282,
															'match_rate' => 0.306,
															'precision' => 0.114,
															'recall' => 0.9,
															'threshold' => 0.155,
														],
													1 =>
														[
															'!f1' => 0.979,
															'!precision' => 0.969,
															'!recall' => 0.99,
															'accuracy' => 0.96,
															'f1' => 0.286,
															'filter_rate' => 0.982,
															'fpr' => 0.01,
															'match_rate' => 0.018,
															'precision' => 0.451,
															'recall' => 0.209,
															'threshold' => 0.694,
														],
													2 =>
														[
															'!f1' => 0.982,
															'!precision' => 0.965,
															'!recall' => 0.999,
															'accuracy' => 0.964,
															'f1' => 0.176,
															'filter_rate' => 0.995,
															'fpr' => 0.001,
															'match_rate' => 0.005,
															'precision' => 0.756,
															'recall' => 0.1,
															'threshold' => 0.831,
														],
												],
										],
								],
						],
					'goodfaith' =>
						[
							'statistics' =>
								[
									'thresholds' =>
										[
											'false' =>
												[
													0 =>
														[
															'!f1' => 0.965,
															'!precision' => 0.99,
															'!recall' => 0.942,
															'accuracy' => 0.934,
															'f1' => 0.235,
															'filter_rate' => 0.933,
															'fpr' => 0.058,
															'match_rate' => 0.067,
															'precision' => 0.151,
															'recall' => 0.529,
															'threshold' => 0.38,
														],
													1 =>
														[
															'!f1' => 0.991,
															'!precision' => 0.984,
															'!recall' => 0.998,
															'accuracy' => 0.982,
															'f1' => 0.237,
															'filter_rate' => 0.995,
															'fpr' => 0.002,
															'match_rate' => 0.005,
															'precision' => 0.608,
															'recall' => 0.147,
															'threshold' => 0.746,
														],
												],
											'true' =>
												[
													0 =>
														[
															'!f1' => 0.243,
															'!precision' => 0.159,
															'!recall' => 0.513,
															'accuracy' => 0.938,
															'f1' => 0.968,
															'filter_rate' => 0.062,
															'fpr' => 0.487,
															'match_rate' => 0.938,
															'precision' => 0.99,
															'recall' => 0.947,
															'threshold' => 0.61,
														],
												],
										],
								],
						],
				],
		],
	'lvwiki' =>
		[
			'models' =>
				[
					'damaging' =>
						[
							'statistics' =>
								[
									'thresholds' =>
										[
											'false' =>
												[
													0 =>
														[
															'!f1' => 0.597,
															'!precision' => 0.463,
															'!recall' => 0.84,
															'accuracy' => 0.967,
															'f1' => 0.983,
															'filter_rate' => 0.053,
															'fpr' => 0.16,
															'match_rate' => 0.947,
															'precision' => 0.995,
															'recall' => 0.971,
															'threshold' => 0.513,
														],
												],
											'true' =>
												[
													0 =>
														[
															'!f1' => 0.976,
															'!precision' => 0.997,
															'!recall' => 0.955,
															'accuracy' => 0.954,
															'f1' => 0.533,
															'filter_rate' => 0.93,
															'fpr' => 0.045,
															'match_rate' => 0.07,
															'precision' => 0.379,
															'recall' => 0.9,
															'threshold' => 0.253,
														],
													1 =>
														[
															'!f1' => 0.988,
															'!precision' => 0.989,
															'!recall' => 0.988,
															'accuracy' => 0.977,
															'f1' => 0.617,
															'filter_rate' => 0.969,
															'fpr' => 0.012,
															'match_rate' => 0.031,
															'precision' => 0.604,
															'recall' => 0.63,
															'threshold' => 0.807,
														],
													2 =>
														[
															'!f1' => 0.989,
															'!precision' => 0.979,
															'!recall' => 0.999,
															'accuracy' => 0.978,
															'f1' => 0.432,
															'filter_rate' => 0.991,
															'fpr' => 0.001,
															'match_rate' => 0.009,
															'precision' => 0.902,
															'recall' => 0.284,
															'threshold' => 0.902,
														],
												],
										],
								],
						],
					'goodfaith' =>
						[
							'statistics' =>
								[
									'thresholds' =>
										[
											'false' =>
												[
													0 =>
														[
															'!f1' => 0.984,
															'!precision' => 0.998,
															'!recall' => 0.971,
															'accuracy' => 0.97,
															'f1' => 0.563,
															'filter_rate' => 0.952,
															'fpr' => 0.029,
															'match_rate' => 0.048,
															'precision' => 0.411,
															'recall' => 0.892,
															'threshold' => 0.001,
														],
													1 =>
														[
															'!f1' => 0.991,
															'!precision' => 0.994,
															'!recall' => 0.989,
															'accuracy' => 0.983,
															'f1' => 0.655,
															'filter_rate' => 0.974,
															'fpr' => 0.011,
															'match_rate' => 0.026,
															'precision' => 0.601,
															'recall' => 0.72,
															'threshold' => 0.047,
														],
													2 =>
														[
															'!f1' => 0.992,
															'!precision' => 0.984,
															'!recall' => 0.999,
															'accuracy' => 0.984,
															'f1' => 0.427,
															'filter_rate' => 0.993,
															'fpr' => 0.001,
															'match_rate' => 0.007,
															'precision' => 0.917,
															'recall' => 0.278,
															'threshold' => 0.993,
														],
												],
											'true' =>
												[
													0 =>
														[
															'!f1' => 0.605,
															'!precision' => 0.463,
															'!recall' => 0.874,
															'accuracy' => 0.975,
															'f1' => 0.987,
															'filter_rate' => 0.041,
															'fpr' => 0.126,
															'match_rate' => 0.959,
															'precision' => 0.997,
															'recall' => 0.977,
															'threshold' => 0.999,
														],
												],
										],
								],
						],
				],
		],
	'nlwiki' =>
		[
			'models' =>
				[
					'damaging' =>
						[
							'statistics' =>
								[
									'thresholds' =>
										[
											'false' =>
												[
													0 =>
														[
															'!f1' => 0.513,
															'!precision' => 0.372,
															'!recall' => 0.826,
															'accuracy' => 0.92,
															'f1' => 0.957,
															'filter_rate' => 0.113,
															'fpr' => 0.174,
															'match_rate' => 0.887,
															'precision' => 0.99,
															'recall' => 0.925,
															'threshold' => 0.495,
														],
												],
											'true' =>
												[
													0 =>
														[
															'!f1' => 0.945,
															'!precision' => 0.994,
															'!recall' => 0.901,
															'accuracy' => 0.901,
															'f1' => 0.48,
															'filter_rate' => 0.86,
															'fpr' => 0.099,
															'match_rate' => 0.14,
															'precision' => 0.327,
															'recall' => 0.901,
															'threshold' => 0.299,
														],
													1 =>
														[
															'!f1' => 0.978,
															'!precision' => 0.977,
															'!recall' => 0.98,
															'accuracy' => 0.959,
															'f1' => 0.584,
															'filter_rate' => 0.952,
															'fpr' => 0.02,
															'match_rate' => 0.048,
															'precision' => 0.6,
															'recall' => 0.569,
															'threshold' => 0.828,
														],
													2 =>
														[
															'!f1' => 0.979,
															'!precision' => 0.96,
															'!recall' => 0.999,
															'accuracy' => 0.96,
															'f1' => 0.364,
															'filter_rate' => 0.987,
															'fpr' => 0.001,
															'match_rate' => 0.013,
															'precision' => 0.914,
															'recall' => 0.227,
															'threshold' => 0.959,
														],
												],
										],
								],
						],
					'goodfaith' =>
						[
							'statistics' =>
								[
									'thresholds' =>
										[
											'false' =>
												[
													0 =>
														[
															'!f1' => 0.961,
															'!precision' => 0.996,
															'!recall' => 0.929,
															'accuracy' => 0.928,
															'f1' => 0.469,
															'filter_rate' => 0.899,
															'fpr' => 0.071,
															'match_rate' => 0.101,
															'precision' => 0.317,
															'recall' => 0.9,
															'threshold' => 0.276,
														],
													1 =>
														[
															'!f1' => 0.986,
															'!precision' => 0.988,
															'!recall' => 0.984,
															'accuracy' => 0.973,
															'f1' => 0.64,
															'filter_rate' => 0.96,
															'fpr' => 0.016,
															'match_rate' => 0.04,
															'precision' => 0.606,
															'recall' => 0.678,
															'threshold' => 0.678,
														],
													2 =>
														[
															'!f1' => 0.987,
															'!precision' => 0.975,
															'!recall' => 0.999,
															'accuracy' => 0.975,
															'f1' => 0.467,
															'filter_rate' => 0.988,
															'fpr' => 0.001,
															'match_rate' => 0.012,
															'precision' => 0.903,
															'recall' => 0.315,
															'threshold' => 0.935,
														],
												],
											'true' =>
												[
													0 =>
														[
															'!f1' => 0.497,
															'!precision' => 0.347,
															'!recall' => 0.871,
															'accuracy' => 0.938,
															'f1' => 0.967,
															'filter_rate' => 0.089,
															'fpr' => 0.129,
															'match_rate' => 0.911,
															'precision' => 0.995,
															'recall' => 0.94,
															'threshold' => 0.657,
														],
												],
										],
								],
						],
				],
		],
	'plwiki' =>
		[
			'models' =>
				[
					'damaging' =>
						[
							'statistics' =>
								[
									'thresholds' =>
										[
											'false' =>
												[
													0 =>
														[
															'!f1' => 0.107,
															'!precision' => 0.057,
															'!recall' => 0.9,
															'accuracy' => 0.592,
															'f1' => 0.736,
															'filter_rate' => 0.429,
															'fpr' => 0.1,
															'match_rate' => 0.571,
															'precision' => 0.995,
															'recall' => 0.584,
															'threshold' => 0.961,
														],
												],
											'true' =>
												[
													0 =>
														[
															'!f1' => 0.987,
															'!precision' => 0.974,
															'!recall' => 1.0,
															'accuracy' => 0.973,
															'f1' => 0.06,
															'filter_rate' => 0.999,
															'fpr' => 0.0,
															'match_rate' => 0.001,
															'precision' => 0.796,
															'recall' => 0.031,
															'threshold' => 0.913,
														],
													1 =>
														[
															'!f1' => 0.987,
															'!precision' => 0.973,
															'!recall' => 1.0,
															'accuracy' => 0.973,
															'f1' => 0.047,
															'filter_rate' => 0.999,
															'fpr' => 0.0,
															'match_rate' => 0.001,
															'precision' => 1.0,
															'recall' => 0.024,
															'threshold' => 0.927,
														],
												],
										],
								],
						],
					'goodfaith' =>
						[
							'statistics' =>
								[
									'thresholds' =>
										[
											'false' =>
												[
													0 =>
														[
															'!f1' => 0.993,
															'!precision' => 0.985,
															'!recall' => 1.0,
															'accuracy' => 0.985,
															'f1' => 0.049,
															'filter_rate' => 1.0,
															'fpr' => 0.0,
															'match_rate' => 0.0,
															'precision' => 1.0,
															'recall' => 0.025,
															'threshold' => 0.772,
														],
												],
											'true' =>
												[
													0 =>
														[
															'!f1' => 0.188,
															'!precision' => 0.109,
															'!recall' => 0.703,
															'accuracy' => 0.909,
															'f1' => 0.952,
															'filter_rate' => 0.098,
															'fpr' => 0.297,
															'match_rate' => 0.902,
															'precision' => 0.995,
															'recall' => 0.912,
															'threshold' => 0.925,
														],
												],
										],
								],
						],
				],
		],
	'ptwiki' =>
		[
			'models' =>
				[
					'damaging' =>
						[
							'statistics' =>
								[
									'thresholds' =>
										[
											'false' =>
												[
													0 =>
														[
															'!f1' => 0.37,
															'!precision' => 0.233,
															'!recall' => 0.894,
															'accuracy' => 0.79,
															'f1' => 0.874,
															'filter_rate' => 0.264,
															'fpr' => 0.106,
															'match_rate' => 0.736,
															'precision' => 0.99,
															'recall' => 0.783,
															'threshold' => 0.669,
														],
												],
											'true' =>
												[
													0 =>
														[
															'!f1' => 0.87,
															'!precision' => 0.991,
															'!recall' => 0.775,
															'accuracy' => 0.784,
															'f1' => 0.365,
															'filter_rate' => 0.729,
															'fpr' => 0.225,
															'match_rate' => 0.271,
															'precision' => 0.229,
															'recall' => 0.9,
															'threshold' => 0.315,
														],
													1 =>
														[
															'!f1' => 0.968,
															'!precision' => 0.953,
															'!recall' => 0.983,
															'accuracy' => 0.939,
															'f1' => 0.443,
															'filter_rate' => 0.96,
															'fpr' => 0.017,
															'match_rate' => 0.04,
															'precision' => 0.601,
															'recall' => 0.351,
															'threshold' => 0.843,
														],
													2 =>
														[
															'!f1' => 0.966,
															'!precision' => 0.935,
															'!recall' => 1.0,
															'accuracy' => 0.934,
															'f1' => 0.103,
															'filter_rate' => 0.996,
															'fpr' => 0.0,
															'match_rate' => 0.004,
															'precision' => 0.901,
															'recall' => 0.055,
															'threshold' => 0.942,
														],
												],
										],
								],
						],
					'goodfaith' =>
						[
							'statistics' =>
								[
									'thresholds' =>
										[
											'false' =>
												[
													0 =>
														[
															'!f1' => 0.893,
															'!precision' => 0.992,
															'!recall' => 0.811,
															'accuracy' => 0.816,
															'f1' => 0.371,
															'filter_rate' => 0.768,
															'fpr' => 0.189,
															'match_rate' => 0.232,
															'precision' => 0.234,
															'recall' => 0.9,
															'threshold' => 0.249,
														],
													1 =>
														[
															'!f1' => 0.973,
															'!precision' => 0.965,
															'!recall' => 0.981,
															'accuracy' => 0.949,
															'f1' => 0.506,
															'filter_rate' => 0.956,
															'fpr' => 0.019,
															'match_rate' => 0.044,
															'precision' => 0.6,
															'recall' => 0.438,
															'threshold' => 0.775,
														],
													2 =>
														[
															'!f1' => 0.971,
															'!precision' => 0.945,
															'!recall' => 0.999,
															'accuracy' => 0.945,
															'f1' => 0.168,
															'filter_rate' => 0.994,
															'fpr' => 0.001,
															'match_rate' => 0.006,
															'precision' => 0.901,
															'recall' => 0.092,
															'threshold' => 0.929,
														],
												],
											'true' =>
												[
													0 =>
														[
															'!f1' => 0.336,
															'!precision' => 0.204,
															'!recall' => 0.941,
															'accuracy' => 0.776,
															'f1' => 0.865,
															'filter_rate' => 0.277,
															'fpr' => 0.059,
															'match_rate' => 0.723,
															'precision' => 0.995,
															'recall' => 0.765,
															'threshold' => 0.843,
														],
												],
										],
								],
						],
				],
		],
	'rowiki' =>
		[
			'models' =>
				[
					'damaging' =>
						[
							'statistics' =>
								[
									'thresholds' =>
										[
											'false' =>
												[
													0 =>
														[
															'!f1' => 0.452,
															'!precision' => 0.3,
															'!recall' => 0.915,
															'accuracy' => 0.89,
															'f1' => 0.939,
															'filter_rate' => 0.151,
															'fpr' => 0.085,
															'match_rate' => 0.849,
															'precision' => 0.995,
															'recall' => 0.889,
															'threshold' => 0.768,
														],
												],
											'true' =>
												[
													0 =>
														[
															'!f1' => 0.943,
															'!precision' => 0.994,
															'!recall' => 0.897,
															'accuracy' => 0.897,
															'f1' => 0.465,
															'filter_rate' => 0.857,
															'fpr' => 0.103,
															'match_rate' => 0.143,
															'precision' => 0.313,
															'recall' => 0.901,
															'threshold' => 0.262,
														],
													1 =>
														[
															'!f1' => 0.978,
															'!precision' => 0.971,
															'!recall' => 0.985,
															'accuracy' => 0.958,
															'f1' => 0.511,
															'filter_rate' => 0.964,
															'fpr' => 0.015,
															'match_rate' => 0.036,
															'precision' => 0.602,
															'recall' => 0.443,
															'threshold' => 0.847,
														],
													2 =>
														[
															'!f1' => 0.977,
															'!precision' => 0.957,
															'!recall' => 0.998,
															'accuracy' => 0.955,
															'f1' => 0.239,
															'filter_rate' => 0.991,
															'fpr' => 0.002,
															'match_rate' => 0.009,
															'precision' => 0.75,
															'recall' => 0.142,
															'threshold' => 0.909,
														],
												],
										],
								],
						],
					'goodfaith' =>
						[
							'statistics' =>
								[
									'thresholds' =>
										[
											'false' =>
												[
													0 =>
														[
															'!f1' => 0.955,
															'!precision' => 0.997,
															'!recall' => 0.916,
															'accuracy' => 0.916,
															'f1' => 0.392,
															'filter_rate' => 0.892,
															'fpr' => 0.084,
															'match_rate' => 0.108,
															'precision' => 0.25,
															'recall' => 0.901,
															'threshold' => 0.17,
														],
													1 =>
														[
															'!f1' => 0.986,
															'!precision' => 0.978,
															'!recall' => 0.994,
															'accuracy' => 0.973,
															'f1' => 0.386,
															'filter_rate' => 0.986,
															'fpr' => 0.006,
															'match_rate' => 0.014,
															'precision' => 0.6,
															'recall' => 0.285,
															'threshold' => 0.848,
														],
													2 =>
														[
															'!f1' => 0.986,
															'!precision' => 0.973,
															'!recall' => 0.999,
															'accuracy' => 0.972,
															'f1' => 0.18,
															'filter_rate' => 0.996,
															'fpr' => 0.001,
															'match_rate' => 0.004,
															'precision' => 0.751,
															'recall' => 0.102,
															'threshold' => 0.93,
														],
												],
											'true' =>
												[
													0 =>
														[
															'!f1' => 0.359,
															'!precision' => 0.223,
															'!recall' => 0.923,
															'accuracy' => 0.901,
															'f1' => 0.946,
															'filter_rate' => 0.125,
															'fpr' => 0.077,
															'match_rate' => 0.875,
															'precision' => 0.997,
															'recall' => 0.9,
															'threshold' => 0.89,
														],
												],
										],
								],
						],
				],
		],
	'ruwiki' =>
		[
			'models' =>
				[
					'damaging' =>
						[
							'statistics' =>
								[
									'thresholds' =>
										[
											'false' =>
												[
													0 =>
														[
															'!f1' => 0.387,
															'!precision' => 0.251,
															'!recall' => 0.847,
															'accuracy' => 0.856,
															'f1' => 0.919,
															'filter_rate' => 0.181,
															'fpr' => 0.153,
															'match_rate' => 0.819,
															'precision' => 0.99,
															'recall' => 0.857,
															'threshold' => 0.526,
														],
												],
											'true' =>
												[
													0 =>
														[
															'!f1' => 0.908,
															'!precision' => 0.993,
															'!recall' => 0.837,
															'accuracy' => 0.84,
															'f1' => 0.376,
															'filter_rate' => 0.797,
															'fpr' => 0.163,
															'match_rate' => 0.203,
															'precision' => 0.238,
															'recall' => 0.9,
															'threshold' => 0.36,
														],
													1 =>
														[
															'!f1' => 0.97,
															'!precision' => 0.965,
															'!recall' => 0.975,
															'accuracy' => 0.942,
															'f1' => 0.404,
															'filter_rate' => 0.956,
															'fpr' => 0.025,
															'match_rate' => 0.044,
															'precision' => 0.45,
															'recall' => 0.367,
															'threshold' => 0.784,
														],
													2 =>
														[
															'!f1' => 0.974,
															'!precision' => 0.95,
															'!recall' => 0.999,
															'accuracy' => 0.949,
															'f1' => 0.132,
															'filter_rate' => 0.995,
															'fpr' => 0.001,
															'match_rate' => 0.005,
															'precision' => 0.754,
															'recall' => 0.072,
															'threshold' => 0.9,
														],
												],
										],
								],
						],
					'goodfaith' =>
						[
							'statistics' =>
								[
									'thresholds' =>
										[
											'false' =>
												[
													0 =>
														[
															'!f1' => 0.922,
															'!precision' => 0.995,
															'!recall' => 0.859,
															'accuracy' => 0.859,
															'f1' => 0.256,
															'filter_rate' => 0.839,
															'fpr' => 0.141,
															'match_rate' => 0.161,
															'precision' => 0.151,
															'recall' => 0.85,
															'threshold' => 0.248,
														],
													1 =>
														[
															'!f1' => 0.985,
															'!precision' => 0.976,
															'!recall' => 0.994,
															'accuracy' => 0.97,
															'f1' => 0.251,
															'filter_rate' => 0.989,
															'fpr' => 0.006,
															'match_rate' => 0.011,
															'precision' => 0.452,
															'recall' => 0.173,
															'threshold' => 0.784,
														],
													2 =>
														[
															'!f1' => 0.986,
															'!precision' => 0.971,
															'!recall' => 1.0,
															'accuracy' => 0.971,
															'f1' => 0.004,
															'filter_rate' => 1.0,
															'fpr' => 0.0,
															'match_rate' => 0.0,
															'precision' => 1.0,
															'recall' => 0.002,
															'threshold' => 0.982,
														],
												],
											'true' =>
												[
													0 =>
														[
															'!f1' => 0.255,
															'!precision' => 0.15,
															'!recall' => 0.855,
															'accuracy' => 0.857,
															'f1' => 0.921,
															'filter_rate' => 0.163,
															'fpr' => 0.145,
															'match_rate' => 0.837,
															'precision' => 0.995,
															'recall' => 0.857,
															'threshold' => 0.758,
														],
												],
										],
								],
						],
				],
		],
	'simplewiki' =>
		[
			'models' =>
				[
					'damaging' =>
						[
							'statistics' =>
								[
									'thresholds' =>
										[
											'false' =>
												[
													0 =>
														[
															'!f1' => 0.337,
															'!precision' => 0.218,
															'!recall' => 0.742,
															'accuracy' => 0.9,
															'f1' => 0.946,
															'filter_rate' => 0.116,
															'fpr' => 0.258,
															'match_rate' => 0.884,
															'precision' => 0.99,
															'recall' => 0.906,
															'threshold' => 0.699,
														],
												],
											'true' =>
												[
													0 =>
														[
															'!f1' => 0.904,
															'!precision' => 0.994,
															'!recall' => 0.829,
															'accuracy' => 0.83,
															'f1' => 0.256,
															'filter_rate' => 0.805,
															'fpr' => 0.171,
															'match_rate' => 0.195,
															'precision' => 0.151,
															'recall' => 0.859,
															'threshold' => 0.149,
														],
													1 =>
														[
															'!f1' => 0.981,
															'!precision' => 0.981,
															'!recall' => 0.98,
															'accuracy' => 0.962,
															'f1' => 0.455,
															'filter_rate' => 0.965,
															'fpr' => 0.02,
															'match_rate' => 0.035,
															'precision' => 0.451,
															'recall' => 0.459,
															'threshold' => 0.629,
														],
													2 =>
														[
															'!f1' => 0.983,
															'!precision' => 0.968,
															'!recall' => 1.0,
															'accuracy' => 0.968,
															'f1' => 0.099,
															'filter_rate' => 0.998,
															'fpr' => 0.0,
															'match_rate' => 0.002,
															'precision' => 0.945,
															'recall' => 0.052,
															'threshold' => 0.944,
														],
												],
										],
								],
						],
					'goodfaith' =>
						[
							'statistics' =>
								[
									'thresholds' =>
										[
											'false' =>
												[
													0 =>
														[
															'!f1' => 0.91,
															'!precision' => 0.995,
															'!recall' => 0.838,
															'accuracy' => 0.839,
															'f1' => 0.265,
															'filter_rate' => 0.814,
															'fpr' => 0.162,
															'match_rate' => 0.186,
															'precision' => 0.156,
															'recall' => 0.882,
															'threshold' => 0.075,
														],
													1 =>
														[
															'!f1' => 0.985,
															'!precision' => 0.979,
															'!recall' => 0.992,
															'accuracy' => 0.971,
															'f1' => 0.46,
															'filter_rate' => 0.98,
															'fpr' => 0.008,
															'match_rate' => 0.02,
															'precision' => 0.604,
															'recall' => 0.372,
															'threshold' => 0.647,
														],
													2 =>
														[
															'!f1' => 0.984,
															'!precision' => 0.968,
															'!recall' => 1.0,
															'accuracy' => 0.968,
															'f1' => 0.072,
															'filter_rate' => 0.999,
															'fpr' => 0.0,
															'match_rate' => 0.001,
															'precision' => 0.923,
															'recall' => 0.037,
															'threshold' => 0.935,
														],
												],
											'true' =>
												[
													0 =>
														[
															'!f1' => 0.4,
															'!precision' => 0.276,
															'!recall' => 0.722,
															'accuracy' => 0.929,
															'f1' => 0.962,
															'filter_rate' => 0.086,
															'fpr' => 0.278,
															'match_rate' => 0.914,
															'precision' => 0.99,
															'recall' => 0.936,
															'threshold' => 0.777,
														],
												],
										],
								],
						],
				],
		],
	'sqwiki' =>
		[
			'models' =>
				[
					'damaging' =>
						[
							'statistics' =>
								[
									'thresholds' =>
										[
											'false' =>
												[
													0 =>
														[
															'!f1' => 0.344,
															'!precision' => 0.212,
															'!recall' => 0.909,
															'accuracy' => 0.9,
															'f1' => 0.946,
															'filter_rate' => 0.123,
															'fpr' => 0.091,
															'match_rate' => 0.877,
															'precision' => 0.997,
															'recall' => 0.9,
															'threshold' => 0.917,
														],
												],
											'true' =>
												[
													0 =>
														[
															'!f1' => 0.949,
															'!precision' => 0.997,
															'!recall' => 0.906,
															'accuracy' => 0.906,
															'f1' => 0.354,
															'filter_rate' => 0.883,
															'fpr' => 0.094,
															'match_rate' => 0.117,
															'precision' => 0.22,
															'recall' => 0.902,
															'threshold' => 0.099,
														],
													1 =>
														[
															'!f1' => 0.987,
															'!precision' => 0.978,
															'!recall' => 0.995,
															'accuracy' => 0.974,
															'f1' => 0.336,
															'filter_rate' => 0.989,
															'fpr' => 0.005,
															'match_rate' => 0.011,
															'precision' => 0.601,
															'recall' => 0.233,
															'threshold' => 0.793,
														],
													2 =>
														[
															'!f1' => 0.986,
															'!precision' => 0.973,
															'!recall' => 1.0,
															'accuracy' => 0.973,
															'f1' => 0.1,
															'filter_rate' => 0.998,
															'fpr' => 0.0,
															'match_rate' => 0.002,
															'precision' => 0.909,
															'recall' => 0.053,
															'threshold' => 0.91,
														],
												],
										],
								],
						],
					'goodfaith' =>
						[
							'statistics' =>
								[
									'thresholds' =>
										[
											'false' =>
												[
													0 =>
														[
															'!f1' => 0.936,
															'!precision' => 0.997,
															'!recall' => 0.882,
															'accuracy' => 0.882,
															'f1' => 0.26,
															'filter_rate' => 0.864,
															'fpr' => 0.118,
															'match_rate' => 0.136,
															'precision' => 0.153,
															'recall' => 0.877,
															'threshold' => 0.062,
														],
													1 =>
														[
															'!f1' => 0.988,
															'!precision' => 0.981,
															'!recall' => 0.995,
															'accuracy' => 0.976,
															'f1' => 0.265,
															'filter_rate' => 0.991,
															'fpr' => 0.005,
															'match_rate' => 0.009,
															'precision' => 0.465,
															'recall' => 0.186,
															'threshold' => 0.734,
														],
													2 =>
														[
															'!f1' => 0.988,
															'!precision' => 0.977,
															'!recall' => 1.0,
															'accuracy' => 0.977,
															'f1' => 0.041,
															'filter_rate' => 0.999,
															'fpr' => 0.0,
															'match_rate' => 0.001,
															'precision' => 0.906,
															'recall' => 0.021,
															'threshold' => 0.896,
														],
												],
											'true' =>
												[
													0 =>
														[
															'!f1' => 0.287,
															'!precision' => 0.172,
															'!recall' => 0.858,
															'accuracy' => 0.899,
															'f1' => 0.946,
															'filter_rate' => 0.118,
															'fpr' => 0.142,
															'match_rate' => 0.882,
															'precision' => 0.996,
															'recall' => 0.9,
															'threshold' => 0.919,
														],
												],
										],
								],
						],
				],
		],
	'srwiki' =>
		[
			'models' =>
				[
					'damaging' =>
						[
							'statistics' =>
								[
									'thresholds' =>
										[
											'false' =>
												[
													0 =>
														[
															'!f1' => 0.328,
															'!precision' => 0.205,
															'!recall' => 0.828,
															'accuracy' => 0.981,
															'f1' => 0.99,
															'filter_rate' => 0.023,
															'fpr' => 0.172,
															'match_rate' => 0.977,
															'precision' => 0.999,
															'recall' => 0.982,
															'threshold' => 0.922,
														],
												],
											'true' =>
												[
													0 =>
														[
															'!f1' => 0.986,
															'!precision' => 0.999,
															'!recall' => 0.974,
															'accuracy' => 0.973,
															'f1' => 0.276,
															'filter_rate' => 0.969,
															'fpr' => 0.026,
															'match_rate' => 0.031,
															'precision' => 0.163,
															'recall' => 0.902,
															'threshold' => 0.03,
														],
													1 =>
														[
															'!f1' => 0.997,
															'!precision' => 0.997,
															'!recall' => 0.996,
															'accuracy' => 0.994,
															'f1' => 0.49,
															'filter_rate' => 0.993,
															'fpr' => 0.004,
															'match_rate' => 0.007,
															'precision' => 0.451,
															'recall' => 0.536,
															'threshold' => 0.366,
														],
													2 =>
														[
															'!f1' => 0.998,
															'!precision' => 0.995,
															'!recall' => 1.0,
															'accuracy' => 0.995,
															'f1' => 0.305,
															'filter_rate' => 0.999,
															'fpr' => 0.0,
															'match_rate' => 0.001,
															'precision' => 0.751,
															'recall' => 0.191,
															'threshold' => 0.827,
														],
												],
										],
								],
						],
				],
		],
	'svwiki' =>
		[
			'models' =>
				[
					'damaging' =>
						[
							'statistics' =>
								[
									'thresholds' =>
										[
											'false' =>
												[
													0 =>
														[
															'!f1' => 0.626,
															'!precision' => 0.51,
															'!recall' => 0.81,
															'accuracy' => 0.976,
															'f1' => 0.987,
															'filter_rate' => 0.04,
															'fpr' => 0.19,
															'match_rate' => 0.96,
															'precision' => 0.995,
															'recall' => 0.98,
															'threshold' => 0.509,
														],
												],
											'true' =>
												[
													0 =>
														[
															'!f1' => 0.982,
															'!precision' => 0.997,
															'!recall' => 0.966,
															'accuracy' => 0.965,
															'f1' => 0.562,
															'filter_rate' => 0.944,
															'fpr' => 0.034,
															'match_rate' => 0.056,
															'precision' => 0.409,
															'recall' => 0.901,
															'threshold' => 0.14,
														],
													1 =>
														[
															'!f1' => 0.991,
															'!precision' => 0.987,
															'!recall' => 0.996,
															'accuracy' => 0.983,
															'f1' => 0.583,
															'filter_rate' => 0.984,
															'fpr' => 0.004,
															'match_rate' => 0.016,
															'precision' => 0.753,
															'recall' => 0.475,
															'threshold' => 0.8,
														],
													2 =>
														[
															'!f1' => 0.989,
															'!precision' => 0.978,
															'!recall' => 1.0,
															'accuracy' => 0.978,
															'f1' => 0.213,
															'filter_rate' => 0.997,
															'fpr' => 0.0,
															'match_rate' => 0.003,
															'precision' => 0.982,
															'recall' => 0.119,
															'threshold' => 0.95,
														],
												],
										],
								],
						],
					'goodfaith' =>
						[
							'statistics' =>
								[
									'thresholds' =>
										[
											'false' =>
												[
													0 =>
														[
															'!f1' => 0.987,
															'!precision' => 0.998,
															'!recall' => 0.976,
															'accuracy' => 0.975,
															'f1' => 0.558,
															'filter_rate' => 0.96,
															'fpr' => 0.024,
															'match_rate' => 0.04,
															'precision' => 0.404,
															'recall' => 0.902,
															'threshold' => 0.08,
														],
													1 =>
														[
															'!f1' => 0.993,
															'!precision' => 0.995,
															'!recall' => 0.991,
															'accuracy' => 0.987,
															'f1' => 0.653,
															'filter_rate' => 0.979,
															'fpr' => 0.009,
															'match_rate' => 0.021,
															'precision' => 0.601,
															'recall' => 0.715,
															'threshold' => 0.38,
														],
													2 =>
														[
															'!f1' => 0.994,
															'!precision' => 0.988,
															'!recall' => 0.999,
															'accuracy' => 0.988,
															'f1' => 0.493,
															'filter_rate' => 0.993,
															'fpr' => 0.001,
															'match_rate' => 0.007,
															'precision' => 0.902,
															'recall' => 0.339,
															'threshold' => 0.774,
														],
												],
											'true' =>
												[
													0 =>
														[
															'!f1' => 0.391,
															'!precision' => 0.246,
															'!recall' => 0.948,
															'accuracy' => 0.948,
															'f1' => 0.973,
															'filter_rate' => 0.068,
															'fpr' => 0.052,
															'match_rate' => 0.932,
															'precision' => 0.999,
															'recall' => 0.948,
															'threshold' => 0.983,
														],
												],
										],
								],
						],
				],
		],
	'testwiki' =>
		[
			'models' =>
				[
					'damaging' =>
						[
							'statistics' =>
								[
									'thresholds' =>
										[
											'false' =>
												[
													0 =>
														[
															'!f1' => 0.951,
															'!precision' => 0.907,
															'!recall' => 1.0,
															'accuracy' => 0.95,
															'f1' => 0.948,
															'filter_rate' => 0.54,
															'fpr' => 0.0,
															'match_rate' => 0.46,
															'precision' => 1.0,
															'recall' => 0.902,
															'threshold' => 0.6,
														],
												],
											'true' =>
												[
													0 =>
														[
															'!f1' => 0.962,
															'!precision' => 0.927,
															'!recall' => 1.0,
															'accuracy' => 0.96,
															'f1' => 0.957,
															'filter_rate' => 0.55,
															'fpr' => 0.0,
															'match_rate' => 0.45,
															'precision' => 1.0,
															'recall' => 0.918,
															'threshold' => 0.6,
														],
													1 =>
														[
															'!f1' => 0.948,
															'!precision' => 1.0,
															'!recall' => 0.902,
															'accuracy' => 0.95,
															'f1' => 0.951,
															'filter_rate' => 0.46,
															'fpr' => 0.098,
															'match_rate' => 0.54,
															'precision' => 0.907,
															'recall' => 1.0,
															'threshold' => 0.5,
														],
													2 =>
														[
															'!f1' => 0.948,
															'!precision' => 1.0,
															'!recall' => 0.902,
															'accuracy' => 0.95,
															'f1' => 0.951,
															'filter_rate' => 0.46,
															'fpr' => 0.098,
															'match_rate' => 0.54,
															'precision' => 0.907,
															'recall' => 1.0,
															'threshold' => 0.5,
														],
												],
										],
								],
						],
					'goodfaith' =>
						[
							'statistics' =>
								[
									'thresholds' =>
										[
											'false' =>
												[
													0 =>
														[
															'!f1' => 0.951,
															'!precision' => 0.907,
															'!recall' => 1.0,
															'accuracy' => 0.95,
															'f1' => 0.948,
															'filter_rate' => 0.54,
															'fpr' => 0.0,
															'match_rate' => 0.46,
															'precision' => 1.0,
															'recall' => 0.902,
															'threshold' => 0.6,
														],
													1 =>
														[
															'!f1' => 0.957,
															'!precision' => 1.0,
															'!recall' => 0.918,
															'accuracy' => 0.96,
															'f1' => 0.962,
															'filter_rate' => 0.45,
															'fpr' => 0.082,
															'match_rate' => 0.55,
															'precision' => 0.927,
															'recall' => 1.0,
															'threshold' => 0.5,
														],
												],
											'true' =>
												[
													0 =>
														[
															'!f1' => 0.962,
															'!precision' => 0.927,
															'!recall' => 1.0,
															'accuracy' => 0.96,
															'f1' => 0.957,
															'filter_rate' => 0.55,
															'fpr' => 0.0,
															'match_rate' => 0.45,
															'precision' => 1.0,
															'recall' => 0.918,
															'threshold' => 0.6,
														],
												],
										],
								],
						],
				],
		],
	'trwiki' =>
		[
			'models' =>
				[
					'damaging' =>
						[
							'statistics' =>
								[
									'thresholds' =>
										[
											'false' =>
												[
													0 =>
														[
															'!f1' => 0.38,
															'!precision' => 0.24,
															'!recall' => 0.918,
															'accuracy' => 0.852,
															'f1' => 0.916,
															'filter_rate' => 0.19,
															'fpr' => 0.082,
															'match_rate' => 0.81,
															'precision' => 0.995,
															'recall' => 0.848,
															'threshold' => 0.836,
														],
												],
											'true' =>
												[
													0 =>
														[
															'!f1' => 0.922,
															'!precision' => 0.994,
															'!recall' => 0.86,
															'accuracy' => 0.862,
															'f1' => 0.392,
															'filter_rate' => 0.822,
															'fpr' => 0.14,
															'match_rate' => 0.178,
															'precision' => 0.251,
															'recall' => 0.901,
															'threshold' => 0.208,
														],
													1 =>
														[
															'!f1' => 0.972,
															'!precision' => 0.964,
															'!recall' => 0.981,
															'accuracy' => 0.947,
															'f1' => 0.363,
															'filter_rate' => 0.967,
															'fpr' => 0.019,
															'match_rate' => 0.033,
															'precision' => 0.45,
															'recall' => 0.304,
															'threshold' => 0.806,
														],
													2 =>
														[
															'!f1' => 0.975,
															'!precision' => 0.951,
															'!recall' => 1.0,
															'accuracy' => 0.951,
															'f1' => 0.03,
															'filter_rate' => 0.999,
															'fpr' => 0.0,
															'match_rate' => 0.001,
															'precision' => 0.808,
															'recall' => 0.015,
															'threshold' => 0.901,
														],
												],
										],
								],
						],
					'goodfaith' =>
						[
							'statistics' =>
								[
									'thresholds' =>
										[
											'false' =>
												[
													0 =>
														[
															'!f1' => 0.926,
															'!precision' => 0.994,
															'!recall' => 0.867,
															'accuracy' => 0.868,
															'f1' => 0.387,
															'filter_rate' => 0.832,
															'fpr' => 0.133,
															'match_rate' => 0.168,
															'precision' => 0.246,
															'recall' => 0.9,
															'threshold' => 0.162,
														],
													1 =>
														[
															'!f1' => 0.974,
															'!precision' => 0.966,
															'!recall' => 0.983,
															'accuracy' => 0.951,
															'f1' => 0.356,
															'filter_rate' => 0.97,
															'fpr' => 0.017,
															'match_rate' => 0.03,
															'precision' => 0.45,
															'recall' => 0.295,
															'threshold' => 0.725,
														],
													2 =>
														[
															'!f1' => 0.977,
															'!precision' => 0.955,
															'!recall' => 0.999,
															'accuracy' => 0.955,
															'f1' => 0.069,
															'filter_rate' => 0.998,
															'fpr' => 0.001,
															'match_rate' => 0.002,
															'precision' => 0.76,
															'recall' => 0.036,
															'threshold' => 0.855,
														],
												],
											'true' =>
												[
													0 =>
														[
															'!f1' => 0.383,
															'!precision' => 0.243,
															'!recall' => 0.91,
															'accuracy' => 0.865,
															'f1' => 0.924,
															'filter_rate' => 0.173,
															'fpr' => 0.09,
															'match_rate' => 0.827,
															'precision' => 0.995,
															'recall' => 0.863,
															'threshold' => 0.859,
														],
												],
										],
								],
						],
				],
		],
	'ukwiki' =>
		[
			'models' =>
				[
					'damaging' =>
						[
							'statistics' =>
								[
									'thresholds' =>
										[
											'false' =>
												[
													0 =>
														[
															'!f1' => 0.282,
															'!precision' => 0.168,
															'!recall' => 0.882,
															'accuracy' => 0.899,
															'f1' => 0.946,
															'filter_rate' => 0.118,
															'fpr' => 0.118,
															'match_rate' => 0.882,
															'precision' => 0.997,
															'recall' => 0.899,
															'threshold' => 0.853,
														],
												],
											'true' =>
												[
													0 =>
														[
															'!f1' => 0.941,
															'!precision' => 0.998,
															'!recall' => 0.891,
															'accuracy' => 0.892,
															'f1' => 0.273,
															'filter_rate' => 0.874,
															'fpr' => 0.109,
															'match_rate' => 0.126,
															'precision' => 0.161,
															'recall' => 0.903,
															'threshold' => 0.122,
														],
													1 =>
														[
															'!f1' => 0.988,
															'!precision' => 0.983,
															'!recall' => 0.993,
															'accuracy' => 0.976,
															'f1' => 0.328,
															'filter_rate' => 0.987,
															'fpr' => 0.007,
															'match_rate' => 0.013,
															'precision' => 0.451,
															'recall' => 0.258,
															'threshold' => 0.745,
														],
												],
										],
								],
						],
					'goodfaith' =>
						[
							'statistics' =>
								[
									'thresholds' =>
										[
											'false' =>
												[
													0 =>
														[
															'!f1' => 0.966,
															'!precision' => 0.996,
															'!recall' => 0.938,
															'accuracy' => 0.935,
															'f1' => 0.25,
															'filter_rate' => 0.928,
															'fpr' => 0.062,
															'match_rate' => 0.072,
															'precision' => 0.15,
															'recall' => 0.74,
															'threshold' => 0.223,
														],
													1 =>
														[
															'!f1' => 0.992,
															'!precision' => 0.989,
															'!recall' => 0.996,
															'accuracy' => 0.985,
															'f1' => 0.318,
															'filter_rate' => 0.992,
															'fpr' => 0.004,
															'match_rate' => 0.008,
															'precision' => 0.451,
															'recall' => 0.246,
															'threshold' => 0.699,
														],
												],
											'true' =>
												[
													0 =>
														[
															'!f1' => 0.234,
															'!precision' => 0.135,
															'!recall' => 0.879,
															'accuracy' => 0.916,
															'f1' => 0.955,
															'filter_rate' => 0.095,
															'fpr' => 0.121,
															'match_rate' => 0.905,
															'precision' => 0.998,
															'recall' => 0.916,
															'threshold' => 0.87,
														],
												],
										],
								],
						],
				],
		],
	'wikidatawiki' =>
		[
			'models' =>
				[
					'damaging' =>
						[
							'statistics' =>
								[
									'thresholds' =>
										[
											'false' =>
												[
													0 =>
														[
															'!f1' => 0.821,
															'!precision' => 0.708,
															'!recall' => 0.976,
															'accuracy' => 0.93,
															'f1' => 0.957,
															'filter_rate' => 0.225,
															'fpr' => 0.024,
															'match_rate' => 0.775,
															'precision' => 0.995,
															'recall' => 0.921,
															'threshold' => 0.721,
														],
												],
											'true' =>
												[
													0 =>
														[
															'!f1' => 0.965,
															'!precision' => 0.994,
															'!recall' => 0.937,
															'accuracy' => 0.942,
															'f1' => 0.846,
															'filter_rate' => 0.789,
															'fpr' => 0.063,
															'match_rate' => 0.211,
															'precision' => 0.75,
															'recall' => 0.97,
															'threshold' => 0.385,
														],
													1 =>
														[
															'!f1' => 0.979,
															'!precision' => 0.978,
															'!recall' => 0.981,
															'accuracy' => 0.965,
															'f1' => 0.893,
															'filter_rate' => 0.839,
															'fpr' => 0.019,
															'match_rate' => 0.161,
															'precision' => 0.9,
															'recall' => 0.885,
															'threshold' => 0.929,
														],
												],
										],
								],
						],
					'goodfaith' =>
						[
							'statistics' =>
								[
									'thresholds' =>
										[
											'false' =>
												[
													0 =>
														[
															'!f1' => 0.948,
															'!precision' => 0.997,
															'!recall' => 0.904,
															'accuracy' => 0.914,
															'f1' => 0.746,
															'filter_rate' => 0.79,
															'fpr' => 0.096,
															'match_rate' => 0.21,
															'precision' => 0.601,
															'recall' => 0.983,
															'threshold' => 0.003,
														],
													1 => null,
												],
											'true' =>
												[
													0 =>
														[
															'!f1' => 0.785,
															'!precision' => 0.66,
															'!recall' => 0.97,
															'accuracy' => 0.932,
															'f1' => 0.96,
															'filter_rate' => 0.188,
															'fpr' => 0.03,
															'match_rate' => 0.812,
															'precision' => 0.995,
															'recall' => 0.926,
															'threshold' => 0.987,
														],
												],
										],
								],
						],
				],
		],
	'zhwiki' =>
		[
			'models' =>
				[
					'damaging' =>
						[
							'statistics' =>
								[
									'thresholds' =>
										[
											'false' =>
												[
													0 =>
														[
															'!f1' => 0.197,
															'!precision' => 0.111,
															'!recall' => 0.835,
															'accuracy' => 0.723,
															'f1' => 0.833,
															'filter_rate' => 0.304,
															'fpr' => 0.165,
															'match_rate' => 0.696,
															'precision' => 0.99,
															'recall' => 0.719,
															'threshold' => 0.973,
														],
												],
											'true' =>
												[
													0 =>
														[
															'!f1' => 0.894,
															'!precision' => 0.988,
															'!recall' => 0.816,
															'accuracy' => 0.815,
															'f1' => 0.252,
															'filter_rate' => 0.793,
															'fpr' => 0.184,
															'match_rate' => 0.207,
															'precision' => 0.151,
															'recall' => 0.773,
															'threshold' => 0.036,
														],
													1 =>
														[
															'!f1' => 0.983,
															'!precision' => 0.979,
															'!recall' => 0.986,
															'accuracy' => 0.966,
															'f1' => 0.545,
															'filter_rate' => 0.967,
															'fpr' => 0.014,
															'match_rate' => 0.033,
															'precision' => 0.603,
															'recall' => 0.497,
															'threshold' => 0.432,
														],
												],
										],
								],
						],
					'goodfaith' =>
						[
							'statistics' =>
								[
									'thresholds' =>
										[
											'false' =>
												[
													0 =>
														[
															'!f1' => 0.924,
															'!precision' => 0.991,
															'!recall' => 0.865,
															'accuracy' => 0.862,
															'f1' => 0.262,
															'filter_rate' => 0.845,
															'fpr' => 0.135,
															'match_rate' => 0.155,
															'precision' => 0.158,
															'recall' => 0.768,
															'threshold' => 0.032,
														],
													1 =>
														[
															'!f1' => 0.984,
															'!precision' => 0.988,
															'!recall' => 0.98,
															'accuracy' => 0.969,
															'f1' => 0.561,
															'filter_rate' => 0.96,
															'fpr' => 0.02,
															'match_rate' => 0.04,
															'precision' => 0.504,
															'recall' => 0.631,
															'threshold' => 0.087,
														],
													2 =>
														[
															'!f1' => 0.985,
															'!precision' => 0.97,
															'!recall' => 0.999,
															'accuracy' => 0.97,
															'f1' => 0.132,
															'filter_rate' => 0.997,
															'fpr' => 0.001,
															'match_rate' => 0.003,
															'precision' => 0.776,
															'recall' => 0.072,
															'threshold' => 0.618,
														],
												],
											'true' =>
												[
													0 =>
														[
															'!f1' => 0.313,
															'!precision' => 0.2,
															'!recall' => 0.722,
															'accuracy' => 0.899,
															'f1' => 0.946,
															'filter_rate' => 0.115,
															'fpr' => 0.278,
															'match_rate' => 0.885,
															'precision' => 0.99,
															'recall' => 0.905,
															'threshold' => 0.964,
														],
												],
										],
								],
						],
				],
		],
]
];
