<?php

// Configure CirrusSearch settings for wikibase

// Cirrus usage for wbsearchentities is on
$wgWBRepoSettings['entitySearch']['useCirrus'] = true;

// T176903, T180169
$wgWBRepoSettings['entitySearch']['useStemming'] = [
	'ar' => [ 'index' => true, 'query' => true ],
	'bg' => [ 'index' => true, 'query' => true ],
	'ca' => [ 'index' => true, 'query' => true ],
	'ckb' => [ 'index' => true, 'query' => true ],
	'cs' => [ 'index' => true, 'query' => true ],
	'da' => [ 'index' => true, 'query' => true ],
	'de' => [ 'index' => true, 'query' => true ],
	'el' => [ 'index' => true, 'query' => true ],
	'en' => [ 'index' => true, 'query' => true ],
	'en-ca' => [ 'index' => true, 'query' => true ],
	'en-gb' => [ 'index' => true, 'query' => true ],
	'es' => [ 'index' => true, 'query' => true ],
	'eu' => [ 'index' => true, 'query' => true ],
	'fa' => [ 'index' => true, 'query' => true ],
	'fi' => [ 'index' => true, 'query' => true ],
	'fr' => [ 'index' => true, 'query' => true ],
	'ga' => [ 'index' => true, 'query' => true ],
	'gl' => [ 'index' => true, 'query' => true ],
	'he' => [ 'index' => true, 'query' => true ],
	'hi' => [ 'index' => true, 'query' => true ],
	'hu' => [ 'index' => true, 'query' => true ],
	'hy' => [ 'index' => true, 'query' => true ],
	'id' => [ 'index' => true, 'query' => true ],
	'it' => [ 'index' => true, 'query' => true ],
	'ja' => [ 'index' => true, 'query' => true ],
	'ko' => [ 'index' => true, 'query' => true ],
	'lt' => [ 'index' => true, 'query' => true ],
	'lv' => [ 'index' => true, 'query' => true ],
	'nb' => [ 'index' => true, 'query' => true ],
	'nl' => [ 'index' => true, 'query' => true ],
	'nn' => [ 'index' => true, 'query' => true ],
	'pl' => [ 'index' => true, 'query' => true ],
	'pt' => [ 'index' => true, 'query' => true ],
	'pt-br' => [ 'index' => true, 'query' => true ],
	'ro' => [ 'index' => true, 'query' => true ],
	'ru' => [ 'index' => true, 'query' => true ],
	'simple' => [ 'index' => true, 'query' => true ],
	'sv' => [ 'index' => true, 'query' => true ],
	'th' => [ 'index' => true, 'query' => true ],
	'tr' => [ 'index' => true, 'query' => true ],
	'uk' => [ 'index' => true, 'query' => true ],
	'zh' => [ 'index' => true, 'query' => true ],
];

// Properties to index
$wgWBRepoSettings['searchIndexProperties'] = $wmgWikibaseSearchIndexProperties;
// Statement boosting
$wgWBRepoSettings['entitySearch']['statementBoost'] = $wmgWikibaseSearchStatementBoosts;
// T163642, T99899
$wgWBRepoSettings['searchIndexTypes'] = [
	'string', 'external-id', 'wikibase-item', 'wikibase-property',
	'wikibase-lexeme', 'wikibase-form', 'wikibase-sense'
];
$wgWBRepoSettings['searchIndexPropertiesExclude'] = $wmgWikibaseSearchIndexPropertiesExclude;

// Prefix search query
$wgWBRepoSettings['entitySearch']['defaultPrefixProfile'] = 'wikibase_config_prefix_query';
// Prefix search rescore
$wgWBRepoSettings['entitySearch']['defaultPrefixRescoreProfile'] = 'wikibase_config_entity_weight';

// Fulltext search query
$wgWBRepoSettings['entitySearch']['fulltextSearchProfile'] = 'wikibase_config_fulltext_query';
// Fulltext search rescore
$wgWBRepoSettings['entitySearch']['defaultFulltextRescoreProfile'] = 'wikibase_config_phrase';

// Fine tuning of the completion search (main elastic query)
$wgWBRepoSettings['entitySearch']['prefixSearchProfiles'] = [
	'wikibase_config_prefix_query' => [
		'any' => 0.001,
		'lang-exact' => 2,
		'lang-folded' => 1.6,
		'lang-prefix' => 1.1,
		'space-discount' => 0.8,
		'fallback-exact' => 1.9,
		'fallback-folded' => 1.3,
		'fallback-prefix' => 0.4,
		'fallback-discount' => 0.9,
	],
	'wikibase_config_prefix_query_20181126' => [
		'tie-breaker' => 0.35,
		'any' => 0.4,
		'lang-exact' => 0.2,
		'lang-folded' => 0.1,
		'lang-prefix' => 0.6,
		'space-discount' => 1, // unused?
		// untuned, this initial run was only done for english which has no fallbacks
		'fallback-exact' => 1.9,
		'fallback-folded' => 1.3,
		'fallback-prefix' => 0.4,
		'fallback-discount' => 0.9,
	],
];

// Fine tuning of the fulltext search (main elastic query)
$wgWBRepoSettings['entitySearch']['fulltextSearchProfiles'] = [
	'wikibase_config_fulltext_query' => [
		'builder_class' => '\Wikibase\Repo\Search\Elastic\EntityFullTextQueryBuilder',
		'settings' => [
			'any'               => 0.04,
			'lang-exact'        => 0.78,
			'lang-folded'       => 0.01,
			'lang-partial'      => 0.07,
			'fallback-exact'    => 0.38,
			'fallback-folded'   => 0.005,
			'fallback-partial'  => 0.03,
			'fallback-discount' => 0.1,
			'phrase' => [
				'all'           => 0.001,
				'all.plain'     => 0.01,
				'slop'          => 0,
			],
		]
	],
];

// Cirrus rescore settings
$wgWBRepoSettings['entitySearch']['rescoreProfiles'] = [
	'wikibase_config_entity_weight' => [
		'i18n_msg' => 'wikibase-rescore-profile-prefix',
		'supported_namespaces' => 'all',
		'rescore' => [
			[
				'window' => 8192,
				'window_size_override' => 'EntitySearchRescoreWindowSize',
				'query_weight' => 1.0,
				'rescore_query_weight' => 1.0,
				'score_mode' => 'total',
				'type' => 'function_score',
				'function_chain' => 'wikibase_config_entity_weight'
			]
		]
	],
	'wikibase_config_entity_weight_20181126' => [
		// TODO: Can this be excluded from being offered, and not need i18n?
		'i18n_msg' => 'wikibase-rescore-profile-prefix',
		'supported_namespaces' => 'all',
		'rescore' => [
			[
				'window' => 8192,
				'window_size_override' => 'EntitySearchRescoreWindowSize',
				'query_weight' => 1,
				'rescore_query_weight' => 0.9,
				'score_mode' => 'total',
				'type' => 'function_score',
				'function_chain' => 'wikibase_config_entity_weight_20181126'
			]
		]
	],
	// Fulltext profile with phrase scoring
	'wikibase_config_phrase' => [
		'i18n_msg' => 'wikibase-rescore-profile-fulltext',
		'supported_namespaces' => 'all',
		'rescore' => [
			// phrase rescore
			[
				'window' => 512,
				'window_size_override' => 'CirrusSearchPhraseRescoreWindowSize',
				'rescore_query_weight' => 10,
				'rescore_query_weight_override' => 'CirrusSearchPhraseRescoreBoost',
				'query_weight' => 1.0,
				'type' => 'phrase',
				// defaults: 'score_mode' => 'total'
			],
			[
				'window' => 8192,
				'window_size_override' => 'EntitySearchRescoreWindowSize',
				'query_weight' => 1.0,
				'rescore_query_weight' => 2.0,
				'score_mode' => 'total',
				'type' => 'function_score',
				'function_chain' => 'entity_weight_boost'
			],
		]
	]
];

// Cirrus rescore function chains
$wgWBRepoSettings['entitySearch']['rescoreFunctionChains'] = [
	'wikibase_config_entity_weight' => [
		'score_mode' => 'sum',
		'functions' => [
			[
				// Incoming links: k = 100, since it is normal to have a bunch of incoming links
				'type' => 'satu',
				'weight' => '0.6',
				'params' => [ 'field' => 'incoming_links', 'missing' => 0, 'a' => 1 , 'k' => 100 ]
			],
			[
				// Site links: k = 20, tens of sites is a lot
				'type' => 'satu',
				'weight' => '0.4',
				'params' => [ 'field' => 'sitelink_count', 'missing' => 0, 'a' => 2, 'k' => 20 ]
			],
			[
				'type' => 'term_boost',
				'weight' => 0.1,
				'params' => [
					// Will be replaced by $wmgWikibaseSearchStatementBoosts
					'statement_keywords' => '_statementBoost_',
				]
			]
		]
	],
	'wikibase_config_entity_weight_20181126' => [
		'score_mode' => 'sum',
		'functions' => [
			[
				// Incoming links: k = 100, since it is normal to have a bunch of incoming links
				'type' => 'satu',
				'weight' => '0.9',
				'params' => [ 'field' => 'incoming_links', 'missing' => 0, 'a' => 0.3 , 'k' => 400 ]
			],
			[
				// Site links: k = 20, tens of sites is a lot
				'type' => 'satu',
				'weight' => '0.15',
				'params' => [ 'field' => 'sitelink_count', 'missing' => 0, 'a' => 0.8, 'k' => 80 ]
			],
			[
				'type' => 'term_boost',
				'weight' => 0.1,
				'params' => [
					// Will be replaced by $wmgWikibaseSearchStatementBoosts
					'statement_keywords' => '_statementBoost_',
				]
			]
		]
	]
];

// Should we move this to cirrus profile management and alter
// Wikibase owns config instead of altering cirrus profile arrays?
$wgCirrusSearchSimilarityProfiles['wikibase_similarity'] = [
	'similarity' => [
		'default' => [
			'type' => 'BM25',
		],
		'descriptions' => [
			'type' => 'BM25',
		],
		// This is a bit verbose to redefine always the same settings
		// but the advantage is that you can re-tune and specialize
		// these on an existing index (requires closing the index).
		// "labels" here means the label + aliases
		'labels' => [
			'type' => 'BM25',
			'k1' => 1.2,
			'b' => 0.3,
		],
		// We consider all as being very similar to an array field
		// as it is a simple concatenation of all the item data
		'all' => [
			'type' => 'BM25',
			'k1' => 1.2,
			'b' => 0.3,
		]
	],
	'fields' => [
		'__default__' => 'default',
		'labels' => 'labels',
		'descriptions' => 'descriptions',
		'all' => 'all',
	]
];
