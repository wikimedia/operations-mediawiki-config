<?php

// Configure CirrusSearch settings for wikidata

// Prefix search query
$wgWBCSPrefixSearchProfile = 'wikibase_config_prefix_query';
// Prefix search rescore
$wgWBCSDefaultPrefixRescoreProfile = 'wikibase_config_entity_weight';

// Fulltext search query
$wgWBCSFulltextSearchProfile = 'wikibase_config_fulltext_query';
// Fulltext search rescore
$wgWBCSDefaultFulltextRescoreProfile = 'wikibase_config_phrase';

// Prefix search queries for the language selector (e.g. in Special:NewLexeme)
// Re-use the same query builder
$wgWBCSLanguageSelectorPrefixSearchProfile = "wikibase_config_prefix_query";
// Tune the rescore window to apply different statementBoosts
$wgWBCSLanguageSelectorRescoreProfile = "wikibase_config_language_selector";

// Fine tuning of the completion search (main elastic query)
$wgWBCSPrefixSearchProfiles = [
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
	'wikibase_config_prefix_query-en' => [
		'language-chain' => [ 'en' ],
		'en-exact' => 0.2,
		'en-folded' => 0.1,
		'en-prefix' => 0.6,
		'any' => 0.4,
		'tie-breaker' => 0.35,
	],
	'wikibase_config_prefix_query-de' => [
		'language-chain' => [ 'de', 'en' ],
		'de-exact' => 0.44,
		'de-folded' => 0.12,
		'de-prefix' => 0.41,
		'any' => 0.35,
		'en-exact' => 0.42,
		'en-folded' => 0.10,
		'en-prefix' => 0.33,
		'tie-breaker' => 0.13,
	],
	'wikibase_config_prefix_query-es' => [
		'language-chain' => [ 'es', 'en' ],
		'es-exact' => 0.90,
		'es-folded' => 0.61,
		'es-prefix' => 1.00,
		'any' => 0.90,
		'en-exact' => 0.33,
		'en-folded' => 0.46,
		'en-prefix' => 0.52,
		'tie-breaker' => 0.34,
	],
	'wikibase_config_prefix_query-fr' => [
		'language-chain' => [ 'fr', 'en' ],
		'fr-exact' => 0.71,
		'fr-folded' => 0.48,
		'fr-prefix' => 0.97,
		'any' => 0.08,
		'en-exact' => 0.47,
		'en-folded' => 0.60,
		'en-prefix' => 0.75,
		'tie-breaker' => 0.50,
	],
	'wikibase_config_prefix_query-202203-en' => [
		'language-chain' => [ 'en' ],
		'any' => 0.12,
		'en-exact' => 0.118,
		'en-folded' => 0.678,
		'en-prefix' => 0.721,
		'tie-breaker' => 0.801
	],
	'wikibase_config_prefix_query-202203-de' => [
		'language-chain' => [ 'de', 'en' ],
		'any' => 0.703,
		'de-exact' => 0.871,
		'de-folded' => 0.695,
		'de-prefix' => 0.266,
		'en-exact' => 0.407,
		'en-folded' => 0.439,
		'en-prefix' => 0.44,
		'tie-breaker' => 0.529
	],
	'wikibase_config_prefix_query-202203-es' => [
		'language-chain' => [ 'es', 'en' ],
		'any' => 0.108,
		'en-exact' => 0.712,
		'en-folded' => 0.101,
		'en-prefix' => 0.101,
		'es-exact' => 0.673,
		'es-folded' => 0.25,
		'es-prefix' => 0.636,
		'tie-breaker' => 0.807
	],
	'wikibase_config_prefix_query-202203-fr' => [
		'language-chain' => [ 'fr', 'en' ],
		'any' => 0.703,
		'en-exact' => 0.407,
		'en-folded' => 0.439,
		'en-prefix' => 0.44,
		'fr-exact' => 0.871,
		'fr-folded' => 0.695,
		'fr-prefix' => 0.266,
		'tie-breaker' => 0.529
	],
];

if ( !empty( $wmgUseWikibaseCirrusSearch ) && !empty( $wmgNewWikibaseCirrusSearch ) ) {
	$wmgBuilderClass = '\Wikibase\Search\Elastic\EntityFullTextQueryBuilder';
} else {
	$wmgBuilderClass = '\Wikibase\Repo\Search\Elastic\EntityFullTextQueryBuilder';
}

// Fine tuning of the fulltext search (main elastic query)
$wgWBCSFulltextSearchProfiles = [
	'wikibase_config_fulltext_query' => [
		'builder_factory' => [ $wmgBuilderClass, 'newFromGlobals' ],
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
$wgWBCSRescoreProfiles = [
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
	// english tuning of wbsearchentities
	'wikibase_config_entity_weight-en' => [
		// TODO: Can this be excluded from being offered, and not need i18n?
		'i18n_msg' => 'wikibase-rescore-profile-prefix',
		'supported_namespaces' => 'all',
		'rescore' => [
			[
				'window' => 8192,
				'window_size_override' => 'EntitySearchRescoreWindowSize',
				'score_mode' => 'total',
				'type' => 'function_score',
				'function_chain' => 'wikibase_config_entity_weight',
				'function_chain_overrides' => [
					// incoming links satu
					'functions.0.params.a' => 0.3,
					'functions.0.params.k' => 400,
					'functions.0.weight' => 0.9,
					// sitelink satu
					'functions.1.params.a' => 0.8,
					'functions.1.params.k' => 80,
					'functions.1.weight' => 0.15,
				],
				'query_weight' => 1,
				'rescore_query_weight' => 0.9,
			]
		]
	],
	'wikibase_config_entity_weight-de' => [
		'i18n_msg' => 'wikibase-rescore-profile-prefix',
		'supported_namespaces' => 'all',
		'rescore' => [
			[
				'window' => 8192,
				'window_size_override' => 'EntitySearchRescoreWindowSize',
				'score_mode' => 'total',
				'type' => 'function_score',
				'function_chain' => 'wikibase_config_entity_weight',
				'function_chain_overrides' => [
					// incoming links satu
					'functions.0.params.a' => 0.31,
					'functions.0.params.k' => 350,
					'functions.0.weight' => 0.67,
					// sitelink satu
					'functions.1.params.a' => 0.36,
					'functions.1.params.k' => 400,
					'functions.1.weight' => 0.02,
				],
				'query_weight' => 0.85,
				'rescore_query_weight' => 0.84,
			]
		]
	],
	'wikibase_config_entity_weight-es' => [
		'i18n_msg' => 'wikibase-rescore-profile-prefix',
		'supported_namespaces' => 'all',
		'rescore' => [
			[
				'window' => 8192,
				'window_size_override' => 'EntitySearchRescoreWindowSize',
				'score_mode' => 'total',
				'type' => 'function_score',
				'function_chain' => 'wikibase_config_entity_weight',
				'function_chain_overrides' => [
					// incoming links satu
					'functions.0.params.a' => 0.26,
					'functions.0.params.k' => 480,
					'functions.0.weight' => 0.73,
					// sitelink satu
					'functions.1.params.a' => 1.72,
					'functions.1.params.k' => 171,
					'functions.1.weight' => 0.16,
				],
				'query_weight' => 0.28,
				'rescore_query_weight' => 0.98,
			]
		]
	],
	'wikibase_config_entity_weight-fr' => [
		'i18n_msg' => 'wikibase-rescore-profile-prefix',
		'supported_namespaces' => 'all',
		'rescore' => [
			[
				'window' => 8192,
				'window_size_override' => 'EntitySearchRescoreWindowSize',
				'score_mode' => 'total',
				'type' => 'function_score',
				'function_chain' => 'wikibase_config_entity_weight',
				'function_chain_overrides' => [
					// incoming links satu
					'functions.0.params.a' => 0.34,
					'functions.0.params.k' => 54,
					'functions.0.weight' => 0.77,
					// sitelink satu
					'functions.1.params.a' => 0.29,
					'functions.1.params.k' => 225,
					'functions.1.weight' => 0.32,
				],
				'query_weight' => 0.14,
				'rescore_query_weight' => 0.53,
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
	],
	'wikibase_config_entity_weight-202203-en' => [
		'i18n_msg' => 'wikibase-rescore-profile-prefix',
		'supported_namespaces' => 'all',
		'rescore' => [
			[
				'window' => 8192,
				'window_size_override' => 'EntitySearchRescoreWindowSize',
				'score_mode' => 'total',
				'type' => 'function_score',
				'function_chain' => 'wikibase_config_entity_weight',
				'function_chain_overrides' => [
					'functions.0.params.a' => 0.374,
					'functions.0.params.k' => 302.0,
					'functions.0.weight' => 0.561,
					'functions.1.params.a' => 9.84,
					'functions.1.params.k' => 384.0,
					'functions.1.weight' => 0.467
				],
				'query_weight' => 0.0743,
				'rescore_query_weight' => 0.708,
			],
		],
	],
	'wikibase_config_entity_weight-202203-de' => [
		'i18n_msg' => 'wikibase-rescore-profile-prefix',
		'supported_namespaces' => 'all',
		'rescore' => [
			[
				'window' => 8192,
				'window_size_override' => 'EntitySearchRescoreWindowSize',
				'score_mode' => 'total',
				'type' => 'function_score',
				'function_chain' => 'wikibase_config_entity_weight',
				'function_chain_overrides' => [
					'functions.0.params.a' => 0.587,
					'functions.0.params.k' => 216.0,
					'functions.0.weight' => 0.897,
					'functions.1.params.a' => 7.76,
					'functions.1.params.k' => 390.0,
					'functions.1.weight' => 0.716
				],
				'query_weight' => 0.1,
				'rescore_query_weight' => 0.706,
			],
		],
	],
	'wikibase_config_entity_weight-202203-es' => [
		'i18n_msg' => 'wikibase-rescore-profile-prefix',
		'supported_namespaces' => 'all',
		'rescore' => [
			[
				'window' => 8192,
				'window_size_override' => 'EntitySearchRescoreWindowSize',
				'score_mode' => 'total',
				'type' => 'function_score',
				'function_chain' => 'wikibase_config_entity_weight',
				'function_chain_overrides' => [
					'functions.0.params.a' => 0.973,
					'functions.0.params.k' => 397.0,
					'functions.0.weight' => 0.805,
					'functions.1.params.a' => 8.4,
					'functions.1.params.k' => 470.0,
					'functions.1.weight' => 0.781
				],
				'query_weight' => 0.118,
				'rescore_query_weight' => 0.421,
			],
		],
	],
	'wikibase_config_entity_weight-202203-fr' => [
		'i18n_msg' => 'wikibase-rescore-profile-prefix',
		'supported_namespaces' => 'all',
		'rescore' => [
			[
				'window' => 8192,
				'window_size_override' => 'EntitySearchRescoreWindowSize',
				'score_mode' => 'total',
				'type' => 'function_score',
				'function_chain' => 'wikibase_config_entity_weight',
				'function_chain_overrides' => [
					'functions.0.params.a' => 0.587,
					'functions.0.params.k' => 216.0,
					'functions.0.weight' => 0.897,
					'functions.1.params.a' => 7.76,
					'functions.1.params.k' => 390.0,
					'functions.1.weight' => 0.716
				],
				'query_weight' => 0.1,
				'rescore_query_weight' => 0.706,
			],
		],
	],
	// Language selector profile
	'wikibase_config_language_selector' => [
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
				// Reuse the "main" profile for entity weights
				// (incoming_links, sitelink_count and $wgWBCSStatementBoost boosts)
				'function_chain' => 'wikibase_config_entity_weight'
			],
			[
				// Append another rescore window to rescore based on the language "attributes" of the entity.
				// This will boost entities based on $wgWBCSLanguageSelectorStatementBoost
				'window' => 8192,
				'window_size_override' => 'EntitySearchRescoreWindowSize',
				'query_weight' => 1.0,
				'rescore_query_weight' => 1.0,
				'score_mode' => 'total',
				'type' => 'function_score',
				'function_chain' => 'wikibase_config_language_selector_language_boosts'
			]
		]
	],
];

// Cirrus rescore function chains
$wgWBCSRescoreFunctionChains = [
	'wikibase_config_entity_weight' => [
		'score_mode' => 'sum',
		'functions' => [
			[
				// Incoming links: k = 100, since it is normal to have a bunch of incoming links
				'type' => 'satu',
				'weight' => '0.6',
				'params' => [ 'field' => 'incoming_links', 'missing' => 0, 'a' => 1, 'k' => 100 ]
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
					// Will be replaced by $wgWBCSStatementBoost
					'statement_keywords' => '_statementBoost_',
				]
			]
		]
	],
];

$wgWBCSLanguageSelectorRescoreFunctionChains = [
	'wikibase_config_language_selector_language_boosts' => [
		// get the max score so that languages that are instances of
		// multiple of the boosted attributes do not get extra points.
		// Negative boosts should not be used here as they'll be
		// flipped to their inverse query with a positive boost.
		'score_mode' => 'max',
		'functions' => [
			[
				'type' => 'term_boost',
				'weight' => 0.1,
				'params' => [
					// Will be replaced by $wgWBCSLanguageSelectorStatementBoost
					'statement_keywords' => '_statementBoost_',
				]
			]
		]
	],
];
