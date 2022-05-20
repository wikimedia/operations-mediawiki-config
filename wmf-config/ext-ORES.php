<?php
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
]
];
