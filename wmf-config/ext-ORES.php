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
	'default' => true,
],
'wgOresLiftWingAddHostHeader' => [
	'default' => true,
],
'wgOresModels' => [
	'default' => [
		'revertrisk-language-agnostic' => [ 'enabled' => false ],
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
		'revertrisk-language-agnostic' => [ 'enabled' => true ],
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
			// https://ores.wikimedia.org/v3/scores/arwiki?models=damaging&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.997''"
			'likelygood' => [ 'min' => 0, 'max' => 0.167 ], // 'maximum recall @ precision >= 0.997'
			// https://ores.wikimedia.org/v3/scores/arwiki?models=damaging&model_info=statistics.thresholds.true."''maximum filter_rate @ recall >= 0.9''"
			'maybebad' => [ 'min' => 0.154, 'max' => 1 ], // 'maximum filter_rate @ recall >= 0.9'
			// https://ores.wikimedia.org/v3/scores/arwiki?models=damaging&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.43''"
			'likelybad' => [ 'min' => 0.496, 'max' => 1 ], // 'maximum recall @ precision >= 0.43'
			'verylikelybad' => false,
		],
		// goodfaith is disabled for arwiki (T192498, T193905)
	],
	'bswiki' => [
		'damaging' => [
			// https://ores.wikimedia.org/v3/scores/bswiki?models=damaging&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.995''"
			'likelygood' => [ 'min' => 0, 'max' => 0.201 ], // 'maximum recall @ precision >= 0.995'
			// https://ores.wikimedia.org/v3/scores/bswiki?models=damaging&model_info=statistics.thresholds.true."''maximum filter_rate @ recall >= 0.9''"
			'maybebad' => [ 'min' => 0.134, 'max' => 1 ], // 'maximum filter_rate @ recall >= 0.9'
			// https://ores.wikimedia.org/v3/scores/bswiki?models=damaging&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.6''"
			'likelybad' => [ 'min' => 0.345, 'max' => 1 ], // 'maximum recall @ precision >= 0.6'
			// https://ores.wikimedia.org/v3/scores/bswiki?models=damaging&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.80''"
			'verylikelybad' => [ 'min' => 0.562, 'max' => 1 ], // 'maximum recall @ precision >= 0.80'
		],
		'goodfaith' => [
			// https://ores.wikimedia.org/v3/scores/bswiki?models=goodfaith&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.995''"
			'likelygood' => [ 'min' => 0.999, 'max' => 1 ], // 'maximum recall @ precision >= 0.995'
			// https://ores.wikimedia.org/v3/scores/bswiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.15''"
			'maybebad' => [ 'min' => 0, 'max' => 0.999 ], // 'maximum recall @ precision >= 0.15'
			// https://ores.wikimedia.org/v3/scores/bswiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.6''"
			'likelybad' => [ 'min' => 0, 'max' => 0.685 ], // 'maximum recall @ precision >= 0.6'
			// https://ores.wikimedia.org/v3/scores/bswiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.80''"
			'verylikelybad' => false, // 'maximum recall @ precision >= 0.80'
		],
	],
	'cawiki' => [
		'damaging' => [
			// https://ores.wikimedia.org/v3/scores/cawiki?models=damaging&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.995''"
			'likelygood' => [ 'min' => 0, 'max' => 0.536 ], // 'maximum recall @ precision >= 0.995'
			// https://ores.wikimedia.org/v3/scores/cawiki?models=damaging&model_info=statistics.thresholds.true."''maximum filter_rate @ recall >= 0.9''"
			'maybebad' => [ 'min' => 0.157, 'max' => 1 ], // 'maximum filter_rate @ recall >= 0.9'
			// https://ores.wikimedia.org/v3/scores/cawiki?models=damaging&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.6''"
			'likelybad' => [ 'min' => 0.728, 'max' => 1 ], // 'maximum recall @ precision >= 0.6'
			// //https://ores.wikimedia.org/v3/scores/cawiki?models=damaging&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.9''"
			'verylikelybad' => [ 'min' => 0.922, 'max' => 1 ], // 'maximum recall @ precision >= 0.9'
		],
		'goodfaith' => [
			// https://ores.wikimedia.org/v3/scores/cawiki?models=goodfaith&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.998''"
			'likelygood' => [ 'min' => 0.648, 'max' => 1 ], // 'maximum recall @ precision >= 0.998'
			// https://ores.wikimedia.org/v3/scores/cawiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.15''"
			'maybebad' => [ 'min' => 0, 'max' => 0.967 ], // 'maximum recall @ precision >= 0.15'
			// https://ores.wikimedia.org/v3/scores/cawiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.6''"
			'likelybad' => [ 'min' => 0, 'max' => 0.266 ], // 'maximum recall @ precision >= 0.6'
			// https://ores.wikimedia.org/v3/scores/cawiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.9''"
			'verylikelybad' => [ 'min' => 0, 'max' => 0.081 ], // 'maximum recall @ precision >= 0.9'
		],
	],
	'cswiki' => [
		'damaging' => [
			// https://ores.wikimedia.org/v3/scores/cswiki?models=damaging&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.995''"
			'likelygood' => [ 'min' => 0, 'max' => 0.048 ], // 'maximum recall @ precision >= 0.995'
			// https://ores.wikimedia.org/v3/scores/cswiki?models=damaging&model_info=statistics.thresholds.true."''maximum filter_rate @ recall >= 0.9''"
			'maybebad' => [ 'min' => 0.056, 'max' => 1 ], // 'maximum filter_rate @ recall >= 0.9'
			// https://ores.wikimedia.org/v3/scores/cswiki?models=damaging&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.6''"
			'likelybad' => [ 'min' => 0.596, 'max' => 1 ], // 'maximum recall @ precision >= 0.6'
			// https://ores.wikimedia.org/v3/scores/cswiki?models=damaging&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.98''"
			'verylikelybad' => [ 'min' => 0.91, 'max' => 1 ], // 'maximum recall @ precision >= 0.98'
		],
		'goodfaith' => [
			// https://ores.wikimedia.org/v3/scores/cswiki?models=goodfaith&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.995''"
			'likelygood' => [ 'min' => 0.704, 'max' => 1 ], // 'maximum recall @ precision >= 0.995'
			// https://ores.wikimedia.org/v3/scores/cswiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum filter_rate @ recall >= 0.9''"
			'maybebad' => [ 'min' => 0, 'max' => 0.95 ], // 'maximum filter_rate @ recall >= 0.9'
			// https://ores.wikimedia.org/v3/scores/cswiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.6''"
			'likelybad' => [ 'min' => 0, 'max' => 0.481 ], // 'maximum recall @ precision >= 0.6'
			// https://ores.wikimedia.org/v3/scores/cswiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.98''"
			'verylikelybad' => [ 'min' => 0, 'max' => 0.118 ], // 'maximum recall @ precision >= 0.98'
		],
	],
	'enwiki' => [
		'damaging' => [
			// https://ores.wikimedia.org/v3/scores/enwiki?models=damaging&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.99''"
			'likelygood' => [ 'min' => 0, 'max' => 0.301 ], // 'maximum recall @ precision >= 0.99'
			// https://ores.wikimedia.org/v3/scores/enwiki?models=damaging&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.15''"
			'maybebad' => [ 'min' => 0.149, 'max' => 1 ], // 'maximum recall @ precision >= 0.15'
			// https://ores.wikimedia.org/v3/scores/enwiki?models=damaging&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.45''"
			'likelybad' => [ 'min' => 0.629, 'max' => 1 ], // 'maximum recall @ precision >= 0.45'
			// https://ores.wikimedia.org/v3/scores/enwiki?models=damaging&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.9''"
			'verylikelybad' => [ 'min' => 0.944, 'max' => 1 ], // 'maximum recall @ precision >= 0.9'
		],
		'goodfaith' => [
			// https://ores.wikimedia.org/v3/scores/enwiki?models=goodfaith&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.99''"
			'likelygood' => [ 'min' => 0.777, 'max' => 1 ], // 'maximum recall @ precision >= 0.99'
			// https://ores.wikimedia.org/v3/scores/enwiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.15''"
			'maybebad' => [ 'min' => 0, 'max' => 0.925 ], // 'maximum recall @ precision >= 0.15'
			// https://ores.wikimedia.org/v3/scores/enwiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.6''"
			'likelybad' => [ 'min' => 0, 'max' => 0.353 ], // 'maximum recall @ precision >= 0.6'
			// https://ores.wikimedia.org/v3/scores/enwiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.9''"
			'verylikelybad' => [ 'min' => 0, 'max' => 0.065 ], // 'maximum recall @ precision >= 0.9'
		],
	],
	'eswiki' => [
		'damaging' => [
			// https://ores.wikimedia.org/v3/scores/eswiki?models=damaging&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.995''"
			'likelygood' => [ 'min' => 0, 'max' => 0.204 ], // 'maximum recall @ precision >= 0.995'
			// https://ores.wikimedia.org/v3/scores/eswiki?models=damaging&model_info=statistics.thresholds.true."''maximum filter_rate @ recall >= 0.9''"
			'maybebad' => [ 'min' => 0.533, 'max' => 1 ],
			// https://ores.wikimedia.org/v3/scores/eswiki?models=damaging&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.6''"
			'likelybad' => [ 'min' => 0.847, 'max' => 1 ], // 'maximum recall @ precision >= 0.6'
			// https://ores.wikimedia.org/v3/scores/eswiki?models=damaging&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.9''"
			'verylikelybad' => [ 'min' => 0.963, 'max' => 1 ], // 'maximum recall @ precision >= 0.9'
		],
		'goodfaith' => [

			// https://ores.wikimedia.org/v3/scores/eswiki?models=goodfaith&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.995''"
			'likelygood' => [ 'min' => 0.997, 'max' => 1 ], // 'maximum recall @ precision >= 0.995'
			'maybebad' => false,
			'likelybad' => false,
			// https://ores.wikimedia.org/v3/scores/eswiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.98''"
			'verylikelybad' => [ 'min' => 0, 'max' => 0.452 ], // 'maximum recall @ precision >= 0.98'
		],
	],
	'eswikibooks' => [
		'damaging' => [
			// https://ores.wikimedia.org/v3/scores/eswikibooks?models=damaging&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.995''"
			'likelygood' => [ 'min' => 0, 'max' => 0.115 ], // 'maximum recall @ precision >= 0.995'
			'maybebad' => false,
			// https://ores.wikimedia.org/v3/scores/eswikibooks?models=damaging&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.6''"
			'likelybad' => [ 'min' => 0.669, 'max' => 1 ], // 'maximum recall @ precision >= 0.6'
			// https://ores.wikimedia.org/v3/scores/eswikibooks?models=damaging&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.9''"
			'verylikelybad' => [ 'min' => 0.94, 'max' => 1 ], // 'maximum recall @ precision >= 0.9'
		],
		'goodfaith' => [
			// https://ores.wikimedia.org/v3/scores/eswikibooks?models=goodfaith&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.99''"
			'likelygood' => [ 'min' => 0.999, 'max' => 1 ], // 'maximum recall @ precision >= 0.99'
			'maybebad' => false,
			// https://ores.wikimedia.org/v3/scores/eswikibooks?models=goodfaith&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.6''"
			'likelybad' => [ 'min' => 0, 'max' => 0.998 ], // 'maximum recall @ precision >= 0.6'
			// https://ores.wikimedia.org/v3/scores/eswikibooks?models=goodfaith&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.9''"
			'verylikelybad' => [ 'min' => 0, 'max' => 0.0 ], // 'maximum recall @ precision >= 0.9'
		],
	],
	'eswikiquote' => [
		'damaging' => [
			// https://ores.wikimedia.org/v3/scores/eswikiquote?models=damaging&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.99''"
			'likelygood' => [ 'min' => 0, 'max' => 0.221 ], // 'maximum recall @ precision >= 0.99'
			// https://ores.wikimedia.org/v3/scores/eswikiquote?models=damaging&model_info=statistics.thresholds.true."''maximum filter_rate @ recall >= 0.9''"
			'maybebad' => [ 'min' => 0.24, 'max' => 1 ], // 'maximum filter_rate @ recall >= 0.9'
			// https://ores.wikimedia.org/v3/scores/eswikiquote?models=damaging&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.6''"
			'likelybad' => [ 'min' => 0.709, 'max' => 1 ], // 'maximum recall @ precision >= 0.6'
			// https://ores.wikimedia.org/v3/scores/eswikiquote?models=damaging&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.98''"
			'verylikelybad' => [ 'min' => 0.961, 'max' => 1 ], // 'maximum recall @ precision >= 0.98'
		],
		'goodfaith' => [
			// https://ores.wikimedia.org/v3/scores/eswikiquote?models=goodfaith&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.98''"
			'likelygood' => [ 'min' => 0.986, 'max' => 1 ], // 'maximum recall @ precision >= 0.98'
			// https://ores.wikimedia.org/v3/scores/eswikiquote?models=goodfaith&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.15''"
			'maybebad' => [ 'min' => 0, 'max' => 0.999 ], // 'maximum recall @ precision >= 0.15'
			// https://ores.wikimedia.org/v3/scores/eswikiquote?models=goodfaith&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.6''"
			'likelybad' => [ 'min' => 0, 'max' => 0.934 ], // 'maximum recall @ precision >= 0.6'
			'verylikelybad' => false,
		],
	],
	'etwiki' => [
		'damaging' => [
			// https://ores.wikimedia.org/v3/scores/etwiki?models=damaging&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.995''"
			'likelygood' => [ 'min' => 0, 'max' => 0.212 ], // 'maximum recall @ precision >= 0.995'
			// https://ores.wikimedia.org/v3/scores/etwiki?models=damaging&model_info=statistics.thresholds.true."''maximum filter_rate @ recall >= 0.9''"
			'maybebad' => [ 'min' => 0.136, 'max' => 1 ], // 'maximum filter_rate @ recall >= 0.9'
			// https://ores.wikimedia.org/v3/scores/etwiki?models=damaging&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.6''"
			'likelybad' => [ 'min' => 0.599, 'max' => 1 ], // 'maximum recall @ precision >= 0.6'
			// https://ores.wikimedia.org/v3/scores/etwiki?models=damaging&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.98''"
			'verylikelybad' => [ 'min' => 0.927, 'max' => 1 ], // 'maximum recall @ precision >= 0.98'
		],
		'goodfaith' => [
			// https://ores.wikimedia.org/v3/scores/etwiki?models=goodfaith&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.995''"
			'likelygood' => [ 'min' => 0.716, 'max' => 1 ], // 'maximum recall @ precision >= 0.995'
			// https://ores.wikimedia.org/v3/scores/etwiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum filter_rate @ recall >= 0.9''"
			'maybebad' => [ 'min' => 0, 'max' => 0.943 ], // 'maximum filter_rate @ recall >= 0.9'
			// https://ores.wikimedia.org/v3/scores/etwiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.6''"
			'likelybad' => [ 'min' => 0, 'max' => 0.624 ], // 'maximum recall @ precision >= 0.6'
			// https://ores.wikimedia.org/v3/scores/etwiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.9''"
			'verylikelybad' => [ 'min' => 0, 'max' => 0.258 ], // 'maximum recall @ precision >= 0.9'
		],
	],
	'fawiki' => [
		'damaging' => [
			// https://ores.wikimedia.org/v3/scores/fawiki?models=damaging&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.995''"
			'likelygood' => [ 'min' => 0, 'max' => 0.413 ], // 'maximum recall @ precision >= 0.995'
			// https://ores.wikimedia.org/v3/scores/fawiki?models=damaging&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.15''"
			'maybebad' => [ 'min' => 0.027, 'max' => 1 ], // 'maximum recall @ precision >= 0.15'
			'likelybad' => false,
			// https://ores.wikimedia.org/v3/scores/fawiki?models=damaging&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.6''"
			'verylikelybad' => [ 'min' => 0.948, 'max' => 1 ], // 'maximum recall @ precision >= 0.6'
		],
		'goodfaith' => [
			// https://ores.wikimedia.org/v3/scores/fawiki?models=goodfaith&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.995''"
			'likelygood' => [ 'min' => 0.673, 'max' => 1 ], // 'maximum recall @ precision >= 0.995'
			// https://ores.wikimedia.org/v3/scores/fawiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.15''"
			'maybebad' => [ 'min' => 0, 'max' => 0.854 ], // 'maximum recall @ precision >= 0.15'
			// https://ores.wikimedia.org/v3/scores/fawiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.45''"
			'likelybad' => [ 'min' => 0, 'max' => 0.258 ], // 'maximum recall @ precision >= 0.45'
			// https://ores.wikimedia.org/v3/scores/fawiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.75''"
			'verylikelybad' => [ 'min' => 0, 'max' => 0.102 ], // 'maximum recall @ precision >= 0.75'
		],
	],
	'fiwiki' => [
		'damaging' => [
			// https://ores.wikimedia.org/v3/scores/fiwiki?models=damaging&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.995''"
			'likelygood' => [ 'min' => 0, 'max' => 0.2069739 ], // 'maximum recall @ precision >= 0.995'
			// https://ores.wikimedia.org/v3/scores/fiwiki?models=damaging&model_info=statistics.thresholds.true."''maximum filter_rate @ recall >= 0.9''"
			'maybebad' => [ 'min' => 0.2502732, 'max' => 1 ], // 'maximum filter_rate @ recall >= 0.9'
			// https://ores.wikimedia.org/v3/scores/fiwiki?models=damaging&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.6''"
			'likelybad' => [ 'min' => 0.8106125, 'max' => 1 ], // 'maximum recall @ precision >= 0.6'
			// https://ores.wikimedia.org/v3/scores/fiwiki?models=damaging&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.9''"
			'verylikelybad' => [ 'min' => 0.9242385, 'max' => 1 ], // 'maximum recall @ precision >= 0.9'
		],
		'goodfaith' => [
			// https://ores.wikimedia.org/v3/scores/fiwiki?models=goodfaith&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.995''"
			'likelygood' => false, // 'maximum recall @ precision >= 0.995'
			// https://ores.wikimedia.org/v3/scores/fiwiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum filter_rate @ recall >= 0.9''"
			'maybebad' => [ 'min' => 0, 'max' => 1.0 ], // 'maximum filter_rate @ recall >= 0.9'
			// https://ores.wikimedia.org/v3/scores/fiwiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.6''"
			'likelybad' => [ 'min' => 0, 'max' => 0.737 ], // 'maximum recall @ precision >= 0.6'
			// https://ores.wikimedia.org/v3/scores/fiwiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.9''"
			'verylikelybad' => false, // 'maximum recall @ precision >= 0.9'
		],
	],
	'frwiki' => [
		'damaging' => [
			// https://ores.wikimedia.org/v3/scores/frwiki?models=damaging&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.995''"
			'likelygood' => [ 'min' => 0, 'max' => 0.092 ], // 'maximum recall @ precision >= 0.995'
			// https://ores.wikimedia.org/v3/scores/frwiki?models=damaging&model_info=statistics.thresholds.true."''maximum filter_rate @ recall >= 0.9''"
			'maybebad' => [ 'min' => 0.08, 'max' => 1 ], // 'maximum filter_rate @ recall >= 0.9'
			// https://ores.wikimedia.org/v3/scores/frwiki?models=damaging&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.6''"
			'likelybad' => [ 'min' => 0.77, 'max' => 1 ], // 'maximum recall @ precision >= 0.6'
			// https://ores.wikimedia.org/v3/scores/frwiki?models=damaging&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.85''"
			'verylikelybad' => [ 'min' => 0.837, 'max' => 1 ], // 'maximum recall @ precision >= 0.85'
		],
		'goodfaith' => [
			// https://ores.wikimedia.org/v3/scores/frwiki?models=goodfaith&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.995''"
			'likelygood' => [ 'min' => 0.778, 'max' => 1 ], // 'maximum recall @ precision >= 0.995'
			// https://ores.wikimedia.org/v3/scores/frwiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum filter_rate @ recall >= 0.9''"
			'maybebad' => [ 'min' => 0, 'max' => 0.96 ], // 'maximum filter_rate @ recall >= 0.9'
			// https://ores.wikimedia.org/v3/scores/frwiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.6''"
			'likelybad' => [ 'min' => 0, 'max' => 0.291 ], // 'maximum recall @ precision >= 0.6'
			// https://ores.wikimedia.org/v3/scores/frwiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.9''"
			'verylikelybad' => [ 'min' => 0, 'max' => 0.112 ], // 'maximum recall @ precision >= 0.9'
		],
	],
	'hewiki' => [
		'damaging' => [
			// https://ores.wikimedia.org/v3/scores/hewiki?models=damaging&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.995''"
			'likelygood' => [ 'min' => 0, 'max' => 0.003 ], // 'maximum recall @ precision >= 0.995'
			'maybebad' => false,
			// https://ores.wikimedia.org/v3/scores/hewiki?models=damaging&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.45''"
			'likelybad' => [ 'min' => 0.203, 'max' => 1 ], // 'maximum recall @ precision >= 0.45'
			// https://ores.wikimedia.org/v3/scores/hewiki?models=damaging&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.98''"
			'verylikelybad' => [ 'min' => 0.766, 'max' => 1 ], // 'maximum recall @ precision >= 0.98'
		],
		'goodfaith' => [
			// https://ores.wikimedia.org/v3/scores/hewiki?models=goodfaith&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.995''"
			'likelygood' => [ 'min' => 1.0, 'max' => 1 ], // 'maximum recall @ precision >= 0.995'
			'maybebad' => false,
			// https://ores.wikimedia.org/v3/scores/hewiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.45''"
			'likelybad' => [ 'min' => 0, 'max' => 0.947 ], // 'maximum recall @ precision >= 0.45'
			// https://ores.wikimedia.org/v3/scores/hewiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.99''"
			'verylikelybad' => [ 'min' => 0, 'max' => 0.001 ], // 'maximum recall @ precision >= 0.99'
		],
	],
	'huwiki' => [
		'damaging' => [
			// https://ores.wikimedia.org/v3/scores/huwiki?models=damaging&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.999''"
			'likelygood' => [ 'min' => 0, 'max' => 0.047 ], // 'maximum recall @ precision >= 0.999'
			// https://ores.wikimedia.org/v3/scores/huwiki?models=damaging&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.15''"
			'maybebad' => [ 'min' => 0.073, 'max' => 1 ], // 'maximum recall @ precision >= 0.15'
			// https://ores.wikimedia.org/v3/scores/huwiki?models=damaging&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.6''"
			'likelybad' => [ 'min' => 0.768, 'max' => 1 ], // 'maximum recall @ precision >= 0.6'
			// https://ores.wikimedia.org/v3/scores/huwiki?models=damaging&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.9''"
			'verylikelybad' => [ 'min' => 0.873, 'max' => 1 ], // 'maximum recall @ precision >= 0.9'
		],
		'goodfaith' => [
			// https://ores.wikimedia.org/v3/scores/huwiki?models=goodfaith&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.999''"
			'likelygood' => [ 'min' => 0.951, 'max' => 1 ], // 'maximum recall @ precision >= 0.999'
			// https://ores.wikimedia.org/v3/scores/huwiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.15''"
			'maybebad' => [ 'min' => 0, 'max' => 0.921 ], // 'maximum recall @ precision >= 0.15'
			// https://ores.wikimedia.org/v3/scores/huwiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.6''"
			'likelybad' => [ 'min' => 0, 'max' => 0.264 ], // 'maximum recall @ precision >= 0.6'
			// https://ores.wikimedia.org/v3/scores/huwiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.85''"
			'verylikelybad' => [ 'min' => 0, 'max' => 0.165 ], // 'maximum recall @ precision >= 0.85'
		],
	],
	'itwiki' => [
		'damaging' => [
			// https://ores.wikimedia.org/v3/scores/itwiki?models=damaging&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.995''"
			'likelygood' => [ 'min' => 0, 'max' => 0.146 ], // 'maximum recall @ precision >= 0.995'
			// https://ores.wikimedia.org/v3/scores/itwiki?models=damaging&model_info=statistics.thresholds.true."''maximum filter_rate @ recall >= 0.9''"
			'maybebad' => [ 'min' => 0.147, 'max' => 1 ], // 'maximum filter_rate @ recall >= 0.9'
			// https://ores.wikimedia.org/v3/scores/itwiki?models=damaging&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.45''"
			'likelybad' => [ 'min' => 0.67, 'max' => 1 ], // 'maximum recall @ precision >= 0.45'
			// https://ores.wikimedia.org/v3/scores/itwiki?models=damaging&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.75''"
			'verylikelybad' => [ 'min' => 0.835, 'max' => 1 ], // 'maximum recall @ precision >= 0.75'
		],
		'goodfaith' => [
			// https://ores.wikimedia.org/v3/scores/itwiki?models=goodfaith&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.995''"
			'likelygood' => [ 'min' => 0.846, 'max' => 1 ], // 'maximum recall @ precision >= 0.995'
			// https://ores.wikimedia.org/v3/scores/itwiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.15''"
			'maybebad' => [ 'min' => 0, 'max' => 0.856 ], // 'maximum recall @ precision >= 0.15'
			// https://ores.wikimedia.org/v3/scores/itwiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.6''"
			'likelybad' => [ 'min' => 0, 'max' => 0.347 ], // 'maximum recall @ precision >= 0.6'
			// https://ores.wikimedia.org/v3/scores/itwiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.9''"
			'verylikelybad' => [ 'min' => 0, 'max' => 0.163 ], // 'maximum recall @ precision >= 0.9'
		],
	],
	'kowiki' => [
		'damaging' => [
			// https://ores.wikimedia.org/v3/scores/kowiki?models=damaging&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.99''"
			'likelygood' => [ 'min' => 0, 'max' => 0.28 ], // 'maximum recall @ precision >= 0.99'
			// https://ores.wikimedia.org/v3/scores/kowiki?models=damaging&model_info=statistics.thresholds.true."''maximum filter_rate @ recall >= 0.9''"
			'maybebad' => [ 'min' => 0.155, 'max' => 1 ], // 'maximum filter_rate @ recall >= 0.9'
			// https://ores.wikimedia.org/v3/scores/kowiki?models=damaging&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.45''"
			'likelybad' => [ 'min' => 0.694, 'max' => 1 ], // 'maximum recall @ precision >= 0.45'
			// https://ores.wikimedia.org/v3/scores/kowiki?models=damaging&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.75''"
			'verylikelybad' => [ 'min' => 0.831, 'max' => 1 ], // 'maximum recall @ precision >= 0.75'
		],
		'goodfaith' => [
			// https://ores.wikimedia.org/v3/scores/kowiki?models=goodfaith&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.99''"
			'likelygood' => [ 'min' => 0.61, 'max' => 1 ], // 'maximum recall @ precision >= 0.99'
			// https://ores.wikimedia.org/v3/scores/kowiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.15''"
			'maybebad' => [ 'min' => 0, 'max' => 0.62 ], // 'maximum recall @ precision >= 0.15'
			// https://ores.wikimedia.org/v3/scores/kowiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.6''"
			'likelybad' => [ 'min' => 0, 'max' => 0.254 ], // 'maximum recall @ precision >= 0.6'
			'verylikelybad' => false,
		],
	],
	'lvwiki' => [
		'damaging' => [
			// https://ores.wikimedia.org/v3/scores/lvwiki?models=damaging&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.995''"
			'likelygood' => [ 'min' => 0, 'max' => 0.487 ], // 'maximum recall @ precision >= 0.995'
			// https://ores.wikimedia.org/v3/scores/lvwiki?models=damaging&model_info=statistics.thresholds.true."''maximum filter_rate @ recall >= 0.9''"
			'maybebad' => [ 'min' => 0.253, 'max' => 1 ], // 'maximum filter_rate @ recall >= 0.9'
			// https://ores.wikimedia.org/v3/scores/lvwiki?models=damaging&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.6''"
			'likelybad' => [ 'min' => 0.807, 'max' => 1 ], // 'maximum recall @ precision >= 0.6'
			// https://ores.wikimedia.org/v3/scores/lvwiki?models=damaging&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.9''"
			'verylikelybad' => [ 'min' => 0.902, 'max' => 1 ], // 'maximum recall @ precision >= 0.9'
		],
		'goodfaith' => [
			// https://ores.wikimedia.org/v3/scores/lvwiki?models=goodfaith&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.997''"
			'likelygood' => [ 'min' => 0.999, 'max' => 1 ], // 'maximum recall @ precision >= 0.997'
			// https://ores.wikimedia.org/v3/scores/lvwiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.15''"
			'maybebad' => [ 'min' => 0, 'max' => 0.999 ], // 'maximum recall @ precision >= 0.15'
			// https://ores.wikimedia.org/v3/scores/lvwiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.6''"
			'likelybad' => [ 'min' => 0, 'max' => 0.953 ], // 'maximum recall @ precision >= 0.6'
			// https://ores.wikimedia.org/v3/scores/lvwiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.9''"
			'verylikelybad' => [ 'min' => 0, 'max' => 0.007 ], // 'maximum recall @ precision >= 0.9'
		],
	],
	'nlwiki' => [
		'damaging' => [
			// https://ores.wikimedia.org/v3/scores/nlwiki?models=damaging&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.99''"
			'likelygood' => [ 'min' => 0, 'max' => 0.505 ], // 'maximum recall @ precision >= 0.99'
			// https://ores.wikimedia.org/v3/scores/nlwiki?models=damaging&model_info=statistics.thresholds.true."''maximum filter_rate @ recall >= 0.9''"
			'maybebad' => [ 'min' => 0.299, 'max' => 1 ], // 'maximum filter_rate @ recall >= 0.9'
			// https://ores.wikimedia.org/v3/scores/nlwiki?models=damaging&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.6''"
			'likelybad' => [ 'min' => 0.828, 'max' => 1 ], // 'maximum recall @ precision >= 0.6'
			// https://ores.wikimedia.org/v3/scores/nlwiki?models=damaging&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.9''"
			'verylikelybad' => [ 'min' => 0.959, 'max' => 1 ], // 'maximum recall @ precision >= 0.9'
		],
		'goodfaith' => [
			// https://ores.wikimedia.org/v3/scores/nlwiki?models=goodfaith&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.995''"
			'likelygood' => [ 'min' => 0.657, 'max' => 1 ], // 'maximum recall @ precision >= 0.995'
			// https://ores.wikimedia.org/v3/scores/nlwiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum filter_rate @ recall >= 0.9''"
			'maybebad' => [ 'min' => 0, 'max' => 0.724 ], // 'maximum filter_rate @ recall >= 0.9'
			// https://ores.wikimedia.org/v3/scores/nlwiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.6''"
			'likelybad' => [ 'min' => 0, 'max' => 0.322 ], // 'maximum recall @ precision >= 0.6'
			// https://ores.wikimedia.org/v3/scores/nlwiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.9''"
			'verylikelybad' => [ 'min' => 0, 'max' => 0.065 ], // 'maximum recall @ precision >= 0.9'
		],
	],
	'plwiki' => [
		'damaging' => [
			// https://ores.wikimedia.org/v3/scores/plwiki?models=damaging&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.995''"
			'likelygood' => [ 'min' => 0, 'max' => 0.039 ], // 'maximum recall @ precision >= 0.995'
			'maybebad' => false,
			// https://ores.wikimedia.org/v3/scores/plwiki?models=damaging&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.75''"
			'likelybad' => [ 'min' => 0.913, 'max' => 1 ], // 'maximum recall @ precision >= 0.75'
			// https://ores.wikimedia.org/v3/scores/plwiki?models=damaging&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.98''"
			'verylikelybad' => [ 'min' => 0.927, 'max' => 1 ], // 'maximum recall @ precision >= 0.98'
		],
		'goodfaith' => [

			// https://ores.wikimedia.org/v3/scores/plwiki?models=goodfaith&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.995''"
			'likelygood' => [ 'min' => 0.925, 'max' => 1 ], // 'maximum recall @ precision >= 0.995'
			'maybebad' => false,
			'likelybad' => false,
			// https://ores.wikimedia.org/v3/scores/plwiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.98''"
			'verylikelybad' => [ 'min' => 0, 'max' => 0.228 ], // 'maximum recall @ precision >= 0.98'
		],
	],
	'ptwiki' => [
		'damaging' => [
			// https://ores.wikimedia.org/v3/scores/ptwiki?models=damaging&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.99''"
			'likelygood' => [ 'min' => 0, 'max' => 0.331 ], // 'maximum recall @ precision >= 0.99'
			// https://ores.wikimedia.org/v3/scores/ptwiki?models=damaging&model_info=statistics.thresholds.true."''maximum filter_rate @ recall >= 0.9''"
			'maybebad' => [ 'min' => 0.315, 'max' => 1 ], // 'maximum filter_rate @ recall >= 0.9'
			// https://ores.wikimedia.org/v3/scores/ptwiki?models=damaging&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.6''"
			'likelybad' => [ 'min' => 0.843, 'max' => 1 ], // 'maximum recall @ precision >= 0.6'
			// https://ores.wikimedia.org/v3/scores/ptwiki?models=damaging&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.9''"
			'verylikelybad' => [ 'min' => 0.942, 'max' => 1 ], // 'maximum recall @ precision >= 0.9'
		],
		'goodfaith' => [
			// https://ores.wikimedia.org/v3/scores/ptwiki?models=goodfaith&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.995''"
			'likelygood' => [ 'min' => 0.843, 'max' => 1 ], // 'maximum recall @ precision >= 0.995'
			// https://ores.wikimedia.org/v3/scores/ptwiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum filter_rate @ recall >= 0.9''"
			'maybebad' => [ 'min' => 0, 'max' => 0.751 ], // 'maximum filter_rate @ recall >= 0.9'
			// https://ores.wikimedia.org/v3/scores/ptwiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.6''"
			'likelybad' => [ 'min' => 0, 'max' => 0.225 ], // 'maximum recall @ precision >= 0.6'
			// https://ores.wikimedia.org/v3/scores/ptwiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.9''"
			'verylikelybad' => [ 'min' => 0, 'max' => 0.071 ], // 'maximum recall @ precision >= 0.9'
		],
	],
	'rowiki' => [
		'damaging' => [
			// https://ores.wikimedia.org/v3/scores/rowiki?models=damaging&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.995''"
			'likelygood' => [ 'min' => 0, 'max' => 0.232 ], // 'maximum recall @ precision >= 0.995'
			// https://ores.wikimedia.org/v3/scores/rowiki?models=damaging&model_info=statistics.thresholds.true."''maximum filter_rate @ recall >= 0.9''"
			'maybebad' => [ 'min' => 0.262, 'max' => 1 ], // 'maximum filter_rate @ recall >= 0.9'
			// https://ores.wikimedia.org/v3/scores/rowiki?models=damaging&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.6''"
			'likelybad' => [ 'min' => 0.847, 'max' => 1 ], // 'maximum recall @ precision >= 0.6'
			// https://ores.wikimedia.org/v3/scores/rowiki?models=damaging&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.75''"
			'verylikelybad' => [ 'min' => 0.909, 'max' => 1 ], // 'maximum recall @ precision >= 0.75'
		],
		'goodfaith' => [
			// HACK: use recall-based likelygood threshold because it has a higher precision than even precision=0.995
			// https://ores.wikimedia.org/v3/scores/rowiki?models=goodfaith&model_info=statistics.thresholds.true."''maximum filter_rate @ recall >= 0.9''"
			'likelygood' => [ 'min' => 0.89, 'max' => 1 ], // 'maximum filter_rate @ recall >= 0.9'
			// https://ores.wikimedia.org/v3/scores/rowiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum filter_rate @ recall >= 0.9''"
			'maybebad' => [ 'min' => 0, 'max' => 0.83 ], // 'maximum filter_rate @ recall >= 0.9'
			// https://ores.wikimedia.org/v3/scores/rowiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.6''"
			'likelybad' => [ 'min' => 0, 'max' => 0.152 ], // 'maximum recall @ precision >= 0.6'
			// https://ores.wikimedia.org/v3/scores/rowiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.75''"
			'verylikelybad' => [ 'min' => 0, 'max' => 0.07 ], // 'maximum recall @ precision >= 0.75'
		],
	],
	'ruwiki' => [
		'damaging' => [
			// https://ores.wikimedia.org/v3/scores/ruwiki?models=damaging&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.99''"
			'likelygood' => [ 'min' => 0, 'max' => 0.474 ], // 'maximum recall @ precision >= 0.99'
			// https://ores.wikimedia.org/v3/scores/ruwiki?models=damaging&model_info=statistics.thresholds.true."''maximum filter_rate @ recall >= 0.9''"
			'maybebad' => [ 'min' => 0.36, 'max' => 1 ], // 'maximum filter_rate @ recall >= 0.9'
			// https://ores.wikimedia.org/v3/scores/ruwiki?models=damaging&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.45''"
			'likelybad' => [ 'min' => 0.784, 'max' => 1 ], // 'maximum recall @ precision >= 0.45'
			// https://ores.wikimedia.org/v3/scores/ruwiki?models=damaging&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.75''"
			'verylikelybad' => [ 'min' => 0.9, 'max' => 1 ], // 'maximum recall @ precision >= 0.75'
		],
		'goodfaith' => [
			// https://ores.wikimedia.org/v3/scores/ruwiki?models=goodfaith&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.995''"
			'likelygood' => [ 'min' => 0.758, 'max' => 1 ], // 'maximum recall @ precision >= 0.995'
			// https://ores.wikimedia.org/v3/scores/ruwiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.15''"
			'maybebad' => [ 'min' => 0, 'max' => 0.752 ], // 'maximum recall @ precision >= 0.15'
			// https://ores.wikimedia.org/v3/scores/ruwiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.45''"
			'likelybad' => [ 'min' => 0, 'max' => 0.216 ], // 'maximum recall @ precision >= 0.45'
			// https://ores.wikimedia.org/v3/scores/ruwiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.75''"
			'verylikelybad' => [ 'min' => 0, 'max' => 0.018 ], // 'maximum recall @ precision >= 0.75'
		],
	],
	'simplewiki' => [
		// Same as enwiki
		'damaging' => [
			// https://ores.wikimedia.org/v3/scores/simplewiki?models=damaging&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.99''"
			'likelygood' => [ 'min' => 0, 'max' => 0.301 ], // 'maximum recall @ precision >= 0.99'
			// https://ores.wikimedia.org/v3/scores/simplewiki?models=damaging&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.15''"
			'maybebad' => [ 'min' => 0.149, 'max' => 1 ], // 'maximum recall @ precision >= 0.15'
			// https://ores.wikimedia.org/v3/scores/simplewiki?models=damaging&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.45''"
			'likelybad' => [ 'min' => 0.629, 'max' => 1 ], // 'maximum recall @ precision >= 0.45'
			// https://ores.wikimedia.org/v3/scores/simplewiki?models=damaging&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.9''"
			'verylikelybad' => [ 'min' => 0.944, 'max' => 1 ], // 'maximum recall @ precision >= 0.9'
		],
		'goodfaith' => [
			// https://ores.wikimedia.org/v3/scores/simplewiki?models=goodfaith&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.99''"
			'likelygood' => [ 'min' => 0.777, 'max' => 1 ], // 'maximum recall @ precision >= 0.99'
			// https://ores.wikimedia.org/v3/scores/simplewiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.15''"
			'maybebad' => [ 'min' => 0, 'max' => 0.925 ], // 'maximum recall @ precision >= 0.15'
			// https://ores.wikimedia.org/v3/scores/simplewiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.6''"
			'likelybad' => [ 'min' => 0, 'max' => 0.353 ], // 'maximum recall @ precision >= 0.6'
			// https://ores.wikimedia.org/v3/scores/simplewiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.9''"
			'verylikelybad' => [ 'min' => 0, 'max' => 0.065 ], // 'maximum recall @ precision >= 0.9'
		],
	],
	'sqwiki' => [
		'damaging' => [
			// HACK: use recall-based likelygood threshold because it has a higher precision than even precision=0.995
			// https://ores.wikimedia.org/v3/scores/sqwiki?models=damaging&model_info=statistics.thresholds.false."''maximum filter_rate @ recall >= 0.9''"
			'likelygood' => [ 'min' => 0, 'max' => 0.083 ], // 'maximum filter_rate @ recall >= 0.9'
			// https://ores.wikimedia.org/v3/scores/sqwiki?models=damaging&model_info=statistics.thresholds.true."''maximum filter_rate @ recall >= 0.9''"
			'maybebad' => [ 'min' => 0.099, 'max' => 1 ], // 'maximum filter_rate @ recall >= 0.9'
			// https://ores.wikimedia.org/v3/scores/sqwiki?models=damaging&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.6''"
			'likelybad' => [ 'min' => 0.793, 'max' => 1 ], // 'maximum recall @ precision >= 0.6'
			// https://ores.wikimedia.org/v3/scores/sqwiki?models=damaging&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.9''"
			'verylikelybad' => [ 'min' => 0.91, 'max' => 1 ], // 'maximum recall @ precision >= 0.9'
		],
		'goodfaith' => [
			// HACK: use recall-based likelygood threshold because it has a higher precision than even precision=0.995
			// https://ores.wikimedia.org/v3/scores/sqwiki?models=goodfaith&model_info=statistics.thresholds.true."''maximum filter_rate @ recall >= 0.9''"
			'likelygood' => [ 'min' => 0.919, 'max' => 1 ], // 'maximum filter_rate @ recall >= 0.9'
			// https://ores.wikimedia.org/v3/scores/sqwiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.15''"
			'maybebad' => [ 'min' => 0, 'max' => 0.938 ], // 'maximum recall @ precision >= 0.15'
			// https://ores.wikimedia.org/v3/scores/sqwiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.45''"
			'likelybad' => [ 'min' => 0, 'max' => 0.266 ], // 'maximum recall @ precision >= 0.45'
			// https://ores.wikimedia.org/v3/scores/sqwiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.9''"
			'verylikelybad' => [ 'min' => 0, 'max' => 0.104 ], // 'maximum recall @ precision >= 0.9'
		],
	],
	'srwiki' => [
		'damaging' => [
			// https://ores.wikimedia.org/v3/scores/srwiki?models=damaging&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.999''"
			'likelygood' => [ 'min' => 0, 'max' => 0.078 ], // 'maximum recall @ precision >= 0.999'
			// https://ores.wikimedia.org/v3/scores/srwiki?models=damaging&model_info=statistics.thresholds.true."''maximum filter_rate @ recall >= 0.9''"
			'maybebad' => [ 'min' => 0.03, 'max' => 1 ], // 'maximum filter_rate @ recall >= 0.9'
			// https://ores.wikimedia.org/v3/scores/srwiki?models=damaging&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.45''"
			'likelybad' => [ 'min' => 0.366, 'max' => 1 ], // 'maximum recall @ precision >= 0.45'
			// https://ores.wikimedia.org/v3/scores/srwiki?models=damaging&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.75''"
			'verylikelybad' => [ 'min' => 0.827, 'max' => 1 ], // 'maximum recall @ precision >= 0.75'
		],
		// goodfaith is disabled for srwiki (T197012)
	],
	'svwiki' => [
		'damaging' => [
			// https://ores.wikimedia.org/v3/scores/svwiki?models=damaging&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.995''"
			'likelygood' => [ 'min' => 0, 'max' => 0.491 ], // 'maximum recall @ precision >= 0.995'
			// https://ores.wikimedia.org/v3/scores/svwiki?models=damaging&model_info=statistics.thresholds.true."''maximum filter_rate @ recall >= 0.9''"
			'maybebad' => [ 'min' => 0.14, 'max' => 1 ], // 'maximum filter_rate @ recall >= 0.9'
			// https://ores.wikimedia.org/v3/scores/svwiki?models=damaging&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.75''"
			'likelybad' => [ 'min' => 0.8, 'max' => 1 ], // 'maximum recall @ precision >= 0.75'
			// https://ores.wikimedia.org/v3/scores/svwiki?models=damaging&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.98''"
			'verylikelybad' => [ 'min' => 0.95, 'max' => 1 ], // 'maximum recall @ precision >= 0.98'
		],
		'goodfaith' => [
			// https://ores.wikimedia.org/v3/scores/svwiki?models=goodfaith&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.999''"
			'likelygood' => [ 'min' => 0.983, 'max' => 1 ], // 'maximum recall @ precision >= 0.999'
			// https://ores.wikimedia.org/v3/scores/svwiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum filter_rate @ recall >= 0.9''"
			'maybebad' => [ 'min' => 0, 'max' => 0.92 ], // 'maximum filter_rate @ recall >= 0.9'
			// https://ores.wikimedia.org/v3/scores/svwiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.6''"
			'likelybad' => [ 'min' => 0, 'max' => 0.62 ], // 'maximum recall @ precision >= 0.6'
			// https://ores.wikimedia.org/v3/scores/svwiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.9''"
			'verylikelybad' => [ 'min' => 0, 'max' => 0.226 ], // 'maximum recall @ precision >= 0.9'
		],
	],
	'testwiki' => [
		// Same as enwiki
		'damaging' => [
			'likelygood' => [ 'min' => 0, 'max' => 0.301 ],
			'maybebad' => [ 'min' => 0.149, 'max' => 1 ],
			'likelybad' => [ 'min' => 0.629, 'max' => 1 ],
			'verylikelybad' => [ 'min' => 0.944, 'max' => 1 ]
		],
		'goodfaith' => [
			'likelygood' => [ 'min' => 0.777, 'max' => 1 ],
			'maybebad' => [ 'min' => 0, 'max' => 0.925 ],
			'likelybad' => [ 'min' => 0, 'max' => 0.353 ],
			'verylikelybad' => [ 'min' => 0, 'max' => 0.065 ],
		],
	],
	'trwiki' => [
		'damaging' => [
			// https://ores.wikimedia.org/v3/scores/trwiki?models=damaging&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.995''"
			'likelygood' => [ 'min' => 0, 'max' => 0.164 ], // 'maximum recall @ precision >= 0.995'
			// https://ores.wikimedia.org/v3/scores/trwiki?models=damaging&model_info=statistics.thresholds.true."''maximum filter_rate @ recall >= 0.9''"
			'maybebad' => [ 'min' => 0.208, 'max' => 1 ], // 'maximum filter_rate @ recall >= 0.9'
			// https://ores.wikimedia.org/v3/scores/trwiki?models=damaging&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.45''"
			'likelybad' => [ 'min' => 0.806, 'max' => 1 ], // 'maximum recall @ precision >= 0.45'
			// https://ores.wikimedia.org/v3/scores/trwiki?models=damaging&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.75''"
			'verylikelybad' => [ 'min' => 0.901, 'max' => 1 ], // 'maximum recall @ precision >= 0.75'
		],
		'goodfaith' => [
			// https://ores.wikimedia.org/v3/scores/trwiki?models=goodfaith&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.995''"
			'likelygood' => [ 'min' => 0.859, 'max' => 1 ], // 'maximum recall @ precision >= 0.995'
			// https://ores.wikimedia.org/v3/scores/trwiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum filter_rate @ recall >= 0.9''"
			'maybebad' => [ 'min' => 0, 'max' => 0.838 ], // 'maximum filter_rate @ recall >= 0.9'
			// https://ores.wikimedia.org/v3/scores/trwiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.45''"
			'likelybad' => [ 'min' => 0, 'max' => 0.275 ], // 'maximum recall @ precision >= 0.45'
			// https://ores.wikimedia.org/v3/scores/trwiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.75''"
			'verylikelybad' => [ 'min' => 0, 'max' => 0.145 ], // 'maximum recall @ precision >= 0.75'
		],
	],
	'ukwiki' => [
		'damaging' => [
			// https://ores.wikimedia.org/v3/scores/ukwiki?models=damaging&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.997''"
			'likelygood' => [ 'min' => 0, 'max' => 0.147 ], // 'maximum recall @ precision >= 0.997'
			// https://ores.wikimedia.org/v3/scores/ukwiki?models=damaging&model_info=statistics.thresholds.true."''maximum filter_rate @ recall >= 0.9''"
			'maybebad' => [ 'min' => 0.122, 'max' => 1 ], // 'maximum filter_rate @ recall >= 0.9'
			// https://ores.wikimedia.org/v3/scores/ukwiki?models=damaging&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.45''"
			'likelybad' => [ 'min' => 0.745, 'max' => 1 ], // 'maximum recall @ precision >= 0.45'
			'verylikelybad' => false,
		],
		'goodfaith' => [
			// https://ores.wikimedia.org/v3/scores/ukwiki?models=goodfaith&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.998''"
			'likelygood' => [ 'min' => 0.87, 'max' => 1 ], // 'maximum recall @ precision >= 0.998'
			// https://ores.wikimedia.org/v3/scores/ukwiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.15''"
			'maybebad' => [ 'min' => 0, 'max' => 0.777 ], // 'maximum recall @ precision >= 0.15'
			// https://ores.wikimedia.org/v3/scores/ukwiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.45''"
			'likelybad' => [ 'min' => 0, 'max' => 0.301 ], // 'maximum recall @ precision >= 0.45'
			'verylikelybad' => false,
		],
	],
	'wikidatawiki' => [
		'damaging' => [
			// https://ores.wikimedia.org/v3/scores/wikidatawiki?models=damaging&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.995''"
			'likelygood' => [ 'min' => 0, 'max' => 0.279 ], // 'maximum recall @ precision >= 0.995'
			'maybebad' => false,
			// https://ores.wikimedia.org/v3/scores/wikidatawiki?models=damaging&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.75''"
			'likelybad' => [ 'min' => 0.385, 'max' => 1 ], // 'maximum recall @ precision >= 0.75'
			// https://ores.wikimedia.org/v3/scores/wikidatawiki?models=damaging&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.9''"
			'verylikelybad' => [ 'min' => 0.929, 'max' => 1 ], // 'maximum recall @ precision >= 0.9'
		],
		'goodfaith' => [
			// https://ores.wikimedia.org/v3/scores/wikidatawiki?models=goodfaith&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.995''"
			'likelygood' => [ 'min' => 0.987, 'max' => 1 ], // 'maximum recall @ precision >= 0.995'
			'maybebad' => false,
			// https://ores.wikimedia.org/v3/scores/wikidatawiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.6''"
			'likelybad' => [ 'min' => 0, 'max' => 0.997 ], // 'maximum recall @ precision >= 0.6'
			// https://ores.wikimedia.org/v3/scores/wikidatawiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.9''"
			'verylikelybad' => false, // 'maximum recall @ precision >= 0.9'
		],
	],
	'zhwiki' => [
		'damaging' => [
			// https://ores.wikimedia.org/v3/scores/zhwiki?models=damaging&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.99''"
			'likelygood' => [ 'min' => 0, 'max' => 0.027 ], // 'maximum recall @ precision >= 0.99'
			// https://ores.wikimedia.org/v3/scores/zhwiki?models=damaging&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.15''"
			'maybebad' => [ 'min' => 0.036, 'max' => 1 ], // 'maximum recall @ precision >= 0.15'
			// https://ores.wikimedia.org/v3/scores/zhwiki?models=damaging&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.6''"
			'likelybad' => [ 'min' => 0.432, 'max' => 1 ], // 'maximum recall @ precision >= 0.6'
			'verylikelybad' => false,
		],
		'goodfaith' => [
			// https://ores.wikimedia.org/v3/scores/zhwiki?models=goodfaith&model_info=statistics.thresholds.true."''maximum recall @ precision >= 0.99''"
			'likelygood' => [ 'min' => 0.964, 'max' => 1 ], // 'maximum recall @ precision >= 0.99'
			// https://ores.wikimedia.org/v3/scores/zhwiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.15''"
			'maybebad' => [ 'min' => 0, 'max' => 0.968 ], // 'maximum recall @ precision >= 0.15'
			// https://ores.wikimedia.org/v3/scores/zhwiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.45''"
			'likelybad' => [ 'min' => 0, 'max' => 0.913 ], // 'maximum recall @ precision >= 0.45'
			// https://ores.wikimedia.org/v3/scores/zhwiki?models=goodfaith&model_info=statistics.thresholds.false."''maximum recall @ precision >= 0.75''"
			'verylikelybad' => [ 'min' => 0, 'max' => 0.382 ], // 'maximum recall @ precision >= 0.75'
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
];
