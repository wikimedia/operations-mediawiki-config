<?php
# WARNING: This file is publically viewable on the web. Do not put private data here.

# This file hold the CirrusSearch configuration which is common to all realms,
# ie settings should apply to both the production cluster and the beta
# cluster.
# If you ever want to stick there an IP address, you should use the per realm
# specific files CirrusSearch-labs.php and CirrusSearch-production.php

# See: https://wikitech.wikimedia.org/wiki/Search
#
# Contact Wikimedia operations or platform engineering for more details.

$wgSearchType = 'CirrusSearch';

if ( $wmgUseClusterJobqueue ) {
	# The secondary update job has a delay of a few seconds to make sure that Elasticsearch
	# has completed a refresh cycle between when the data that the job needs is added and
	# when the job is run.
	$wgJobTypeConf['cirrusSearchIncomingLinkCount'] = [ 'checkDelay' => true ] +
		$wgJobTypeConf['default'];
}

# Set up the the default cluster to send queries to,
# and the list of clusters to write to.
if ( $wmgCirrusSearchDefaultCluster === 'local' ) {
	$wgCirrusSearchDefaultCluster = $wmfDatacenter;
} else {
	$wgCirrusSearchDefaultCluster = $wmgCirrusSearchDefaultCluster;
}
$wgCirrusSearchWriteClusters = $wmgCirrusSearchWriteClusters;

# Enable user testing
$wgCirrusSearchUserTesting = $wmgCirrusSearchUserTesting;

# BM25 A/B test, enabled only on enwiki to avoid conflicts with
# with TextCat language detection
# UserTesting requires that a var exists in $GLOBALS before setting it
# All extra vars needed to customize rescore weights. These must be defined
# at the top level so textcat can still attempt to fetch them when building
# an other-wiki query.
$wgCirrusSearchPageViewsW = 1.0;
$wgCirrusSearchPageViewsK = 1.0;
$wgCirrusSearchPageViewsA = 1.0;
$wgCirrusSearchIncLinksW = 1.0;
$wgCirrusSearchIncLinksK = 1.0;
$wgCirrusSearchIncLinksA = 1.0;
$wgCirrusSearchIncLinksAloneW = 1.0;
$wgCirrusSearchIncLinksAloneK = 1.0;
$wgCirrusSearchIncLinksAloneA = 1.0;

if ( $wgDBname === 'enwiki' ) {
	$wgCirrusSearchUserTesting['bm25'] = [
		'sampleRate' => 0,
		'globals' => [
			'wgCirrusSearchBoostTemplates' => [],
			'wgCirrusSearchRescoreProfiles' => $wgCirrusSearchRescoreProfiles + [
				'wsum_inclinks' => [
					'supported_namespaces' => 'all',
					'rescore' => [
						[
							'window' => 8192,
							'window_size_override' => 'CirrusSearchFunctionRescoreWindowSize',
							'query_weight' => 1.0,
							'rescore_query_weight' => 1.0,
							'score_mode' => 'total',
							'type' => 'function_score',
							'function_chain' => 'wsum_inclinks'
						],
						[
							'window' => 8192,
							'window_size_override' => 'CirrusSearchFunctionRescoreWindowSize',
							'query_weight' => 1.0,
							'rescore_query_weight' => 1.0,
							'score_mode' => 'multiply',
							'type' => 'function_score',
							'function_chain' => 'optional_chain'
						],
					],
				],
				'wsum_inclinks_pv' => [
					'supported_namespaces' => 'content',
					'fallback_profile' => 'wsum_inclinks',
					'rescore' => [
						[
							'window' => 8192,
							'window_size_override' => 'CirrusSearchFunctionRescoreWindowSize',
							'query_weight' => 1.0,
							'rescore_query_weight' => 1.0,
							'score_mode' => 'total',
							'type' => 'function_score',
							'function_chain' => 'wsum_inclinks_pv'
						],
						[
							'window' => 8192,
							'window_size_override' => 'CirrusSearchFunctionRescoreWindowSize',
							'query_weight' => 1.0,
							'rescore_query_weight' => 1.0,
							'score_mode' => 'multiply',
							'type' => 'function_score',
							'function_chain' => 'optional_chain'
						],
					],
				],
			],
			'wgCirrusSearchRescoreFunctionScoreChains' => $wgCirrusSearchRescoreFunctionScoreChains + [
				'wsum_inclinks' => [
					'functions' => [
						[
							'type' => 'satu',
							'weight' => [
								'value' => 1.2,
								'config_override' => 'CirrusSearchIncLinksAloneW',
								'uri_param_override' => 'cirrusIncLinksAloneW',
							],
							'params' => [
								'field' => 'incoming_links',
								'k' => [
									'value' => 10,
									'config_override' => 'CirrusSearchIncLinksAloneK',
									'uri_param_override' => 'cirrusIncLinksAloneK',
								],
								'a' => [
									'value' => 1,
									'config_override' => 'CirrusSearchIncLinksAloneA',
									'uri_param_override' => 'cirrusIncLinksAloneA',
								]
							],
						],
					],
				],
				'wsum_inclinks_pv' => [
					'score_mode' => 'sum',
					'boost_mode' => 'sum',
					'functions' => [
						[
							'type' => 'satu',
							'weight' => [
								'value' => 1.8,
								'config_override' => 'CirrusSearchPageViewsW',
								'uri_param_override' => 'cirrusPageViewsW',
							],
							'params' => [
								'field' => 'popularity_score',
								'k' => [
									'value' => 0.0000007,
									'config_override' => 'CirrusSearchPageViewsK',
									'uri_param_override' => 'cirrusPageViewsK',
								],
								'a' => [
									'value' => 1,
									'config_override' => 'CirrusSearchPageViewsA',
									'uri_param_override' => 'cirrusPageViewsA',
								],
							],
						],
						[
							'type' => 'satu',
							'weight' => [
								'value' => 0.6,
								'config_override' => 'CirrusSearchIncLinksW',
								'uri_param_override' => 'cirrusIncLinkssW',
							],
							'params' => [
								'field' => 'incoming_links',
								'k' => [
									'value' => 10,
									'config_override' => 'CirrusSearchIncLinksK',
									'uri_param_override' => 'cirrusIncLinksK',
								],
								'a' => [
									'value' => 1,
									'config_override' => 'CirrusSearchIncLinksA',
									'uri_param_override' => 'cirrusIncLinksA',
								],
							],
						],
					],
				],
			],
			'wgCirrusSearchFullTextQueryBuilderProfiles' => $wgCirrusSearchFullTextQueryBuilderProfiles + [
				'perfield_builder' => [
					'builder_class' => \CirrusSearch\Query\FullTextSimpleMatchQueryBuilder::class,
					'settings' => [
						'default_min_should_match' => '1',
						'default_query_type' => 'most_fields',
						'default_stem_weight' => 3.0,
						'fields' => [
							'title' => 0.3,
							'redirect.title' => [
								'boost' => 0.27,
								'in_dismax' => 'redirects_or_shingles'
							],
							'suggest' => [
								'is_plain' => true,
								'boost' => 0.20,
								'in_dismax' => 'redirects_or_shingles',
							],
							'category' => 0.05,
							'heading' => 0.05,
							'text' => [
								'boost' => 0.6,
								'in_dismax' => 'text_and_opening_text',
							],
							'opening_text' => [
								'boost' => 0.5,
								'in_dismax' => 'text_and_opening_text',
							],
							'auxiliary_text' => 0.05,
							'file_text' => 0.5,
						],
						'phrase_rescore_fields' => [
							// very low (don't forget it's multiplied by 10 by default)
							// Use the all field to avoid loading positions on another field,
							// score is roughly the same when used on text
							'all' => 0.03,
							'all.plain' => 0.05,
						],
					],
				],
			],
		],
		'buckets' => [
			// Prod settings on eqiad
			// nDCG@5 0.2772 (enwiki scores excluded)
			'control' => [
				'trigger' => 'bm25:control',
				'globals' => [
					'wgCirrusSearchFullTextQueryBuilderProfile' => 'default',
					'wgCirrusSearchExtraBackendLatency' => 30000,
				],
			],
			// BM25+allfield and QueryString, inclinks as a sum
			// nDCG@5 0.2689 (enwiki scores excluded)
			'bm25_allfield' => [
				'trigger' => 'bm25:allfield',
				'globals' => [
					'wgCirrusSearchDefaultCluster' => 'codfw',
					'wgCirrusSearchFullTextQueryBuilderProfile' => 'default',
					'wgCirrusSearchPhraseSuggestReverseField' => [
						'build' => true,
						'use' => false,
					],
					'wgCirrusSearchIgnoreOnWikiBoostTemplates' => true,
					// set only here because only needed for reindexing
					'wgCirrusSearchSimilarityProfile' => [
						'similarity' => [
							'arrays' => [
								'type' => 'BM25',
								'k1' => 1.2,
								'b' => 0.3,
							],
							'text' => [
								'type' => 'BM25',
								'k1' => 1.2,
								'b' => 0.75,
							],
						],
						'fields' => [
							'__default__' => 'text',
							'category' => 'arrays',
							'heading' => 'arrays',
							'redirect.title' => 'arrays',
							'suggest' => 'arrays',
						],
					],
					'wgCirrusSearchRescoreProfile' => 'wsum_inclinks',
					'wgCirrusSearchIncLinksAloneW' => 1.3,
					'wgCirrusSearchIncLinksAloneK' => 30,
					'wgCirrusSearchIncLinksAloneA' => 0.7,
				]
			],
			// BM25, perfield and SimpleMatch Query builder, inclinks as a sum
			// nDCG@5 0.3371 (enwiki scores excluded)
			'bm25_inclinks' => [
				'trigger' => 'bm25:inclinks',
				'globals' => [
					'wgCirrusSearchDefaultCluster' => 'codfw',
					'wgCirrusSearchFullTextQueryBuilderProfile' => 'perfield_builder',
					'wgCirrusSearchIgnoreOnWikiBoostTemplates' => true,
					'wgCirrusSearchPhraseSuggestReverseField' => [
						'build' => true,
						'use' => false,
					],
					'wgCirrusSearchRescoreProfile' => 'wsum_inclinks',
					'wgCirrusSearchIncLinksAloneW' => 6.5,
					'wgCirrusSearchIncLinksAloneK' => 30,
					'wgCirrusSearchIncLinksAloneA' => 0.7,
				]
			],
			// BM25, perfield and SimpleMatch Query builder, inclinks+pop score as a sum
			// nDCG@5 0.3368 (enwiki scores excluded)
			'bm25_inclinks_pv' => [
				'trigger' => 'bm25:inclinks_pv',
				'globals' => [
					'wgCirrusSearchDefaultCluster' => 'codfw',
					'wgCirrusSearchFullTextQueryBuilderProfile' => 'perfield_builder',
					'wgCirrusSearchIgnoreOnWikiBoostTemplates' => true,
					'wgCirrusSearchPhraseSuggestReverseField' => [
						'build' => true,
						'use' => false,
					],
					'wgCirrusSearchRescoreProfile' => 'wsum_inclinks_pv',
					'wgCirrusSearchPageViewsW' => 1.5,
					'wgCirrusSearchPageViewsK' => 8E-6,
					'wgCirrusSearchPageViewsA' => 0.8,
					'wgCirrusSearchIncLinksW' => 5.0,
					'wgCirrusSearchIncLinksK' => 30,
					'wgCirrusSearchIncLinksA' => 0.7,
					'wgCirrusSearchIncLinksAloneW' => 6.5,
					'wgCirrusSearchIncLinksAloneK' => 30,
					'wgCirrusSearchIncLinksAloneA' => 0.7,
				]
			],
			// BM25, perfield and SimpleMatch Query builder, inclinks+pop score as a sum
			// nDCG@5 0.3368 (enwiki scores excluded)
			// Reverse field enabled for DYM
			'bm25_inclinks_pv_rev' => [
				'trigger' => 'bm25:inclinks_pv_rev',
				'globals' => [
					'wgCirrusSearchDefaultCluster' => 'codfw',
					'wgCirrusSearchFullTextQueryBuilderProfile' => 'perfield_builder',
					'wgCirrusSearchPhraseSuggestReverseField' => [
						'build' => true,
						'use' => true,
					],
					'wgCirrusSearchIgnoreOnWikiBoostTemplates' => true,
					'wgCirrusSearchPageViewsW' => 1.5,
					'wgCirrusSearchPageViewsK' => 8E-6,
					'wgCirrusSearchPageViewsA' => 0.8,
					'wgCirrusSearchIncLinksW' => 5.0,
					'wgCirrusSearchIncLinksK' => 30,
					'wgCirrusSearchIncLinksA' => 0.7,
					'wgCirrusSearchRescoreProfile' => 'wsum_inclinks_pv',
					'wgCirrusSearchIncLinksAloneW' => 6.5,
					'wgCirrusSearchIncLinksAloneK' => 30,
					'wgCirrusSearchIncLinksAloneA' => 0.7,
				],
			],
		],
	];
}

# Turn off leading wildcard matches, they are a very slow and inefficient query
$wgCirrusSearchAllowLeadingWildcard = false;

# Turn off the more accurate but slower search mode.  It is most helpful when you
# have many small shards.  We don't do that in production and we could use the speed.
$wgCirrusSearchMoreAccurateScoringMode = false;

# Raise the refresh interval to save some CPU at the cost of being slightly less realtime.
$wgCirrusSearchRefreshInterval = 30;

# Limit the number of states generated by wildcard queries (500 will allow about 20 wildcards)
$wgCirrusSearchQueryStringMaxDeterminizedStates = 500;

# Lower the regex timeouts - the defaults are too high in an environment with reverse proxies.
$wgCirrusSearchSearchShardTimeout[ 'regex' ] = '40s';
$wgCirrusSearchClientSideSearchTimeout[ 'regex' ] = 80;

# Set the backoff for Cirrus' job that reacts to template changes - slow and steady
# will help prevent spikes in Elasticsearch load.
// $wgJobBackoffThrottling['cirrusSearchLinksUpdate'] = 5;  -- disabled, Ori 3-Dec-2015
# Also engage a delay for the Cirrus job that counts incoming links to pages when
# pages are newly linked or unlinked.  Too many link count queries at once could flood
# Elasticsearch.
// $wgJobBackoffThrottling['cirrusSearchIncomingLinkCount'] = 1; -- disabled, Ori 3-Dec-2015

# Ban the hebrew plugin, it is unstable
$wgCirrusSearchBannedPlugins[] = 'elasticsearch-analysis-hebrew';

# Build and use an ngram index for faster regex matching
$wgCirrusSearchWikimediaExtraPlugin = [
	'regex' => [
		'build',
		'use',
	],
	'super_detect_noop' => true,
	'id_hash_mod_filter' => true,
];

# Enable the "experimental" highlighter on all wikis
$wgCirrusSearchUseExperimentalHighlighter = true;
$wgCirrusSearchOptimizeIndexForExperimentalHighlighter = true;

# Setup the feedback link on Special:Search if enabled
$wgCirrusSearchFeedbackLink = $wmgCirrusSearchFeedbackLink;

# Settings customized per index.
$wgCirrusSearchShardCount = $wmgCirrusSearchShardCount;
$wgCirrusSearchReplicas = $wmgCirrusSearchReplicas;
$wgCirrusSearchMaxShardsPerNode = $wmgCirrusSearchMaxShardsPerNode;
$wgCirrusSearchPreferRecentDefaultDecayPortion = $wmgCirrusSearchPreferRecentDefaultDecayPortion;
$wgCirrusSearchBoostLinks = $wmgCirrusSearchBoostLinks;
$wgCirrusSearchWeights = array_merge( $wgCirrusSearchWeights, $wmgCirrusSearchWeightsOverrides );
$wgCirrusSearchPowerSpecialRandom = $wmgCirrusSearchPowerSpecialRandom;
$wgCirrusSearchAllFields = $wmgCirrusSearchAllFields;
$wgCirrusSearchNamespaceWeights = $wmgCirrusSearchNamespaceWeightOverrides +
	$wgCirrusSearchNamespaceWeights;

// We had an incident of filling up the entire clusters redis instances after
// 6 hours, half of that seems reasonable.
$wgCirrusSearchDropDelayedJobsAfter = 60 * 60 * 3;

// Enable cache warming for wikis with more than one shard.  Cache warming is good
// for smoothing out I/O spikes caused by merges at the cost of potentially polluting
// the cache by adding things that won't be used.

// Wikis with more then one shard or with multi-cluster configuration is a
// decent way of saying "wikis we expect will get some search traffic every
// few seconds".  In this commonet the term "cache" refers to all kinds of
// caches: the linux disk cache, Elasticsearch's filter cache, whatever.
if ( isset( $wgCirrusSearchShardCount['eqiad'] ) ) {
	$wgCirrusSearchMainPageCacheWarmer = true;
} else {
	$wgCirrusSearchMainPageCacheWarmer = ( $wgCirrusSearchShardCount['content'] > 1 );
}

// Enable concurrent search limits for specified abusive networks
$wgCirrusSearchForcePerUserPoolCounter = $wmgCirrusSearchForcePerUserPoolCounter;

// Commons is special
if ( $wgDBname == 'commonswiki' ) {
	$wgCirrusSearchNamespaceMappings[ NS_FILE ] = 'file';
	$wgCirrusSearchReplicaCount['file'] = 2;
} elseif ( $wgDBname == 'officewiki' || $wgDBname == 'foundationwiki' ) {
	// T94856 - makes searching difficult for locally uploaded files
	// T76957 - doesn't make sense to have Commons files on foundationwiki search
} else { // So is everyone else, for using commons
	$wgCirrusSearchExtraIndexes[ NS_FILE ] = [ 'commonswiki_file' ];
}

// Configuration for initial test deployment of inline interwiki search via
// language detection on the search terms.

$wgCirrusSearchWikiToNameMap = $wmgCirrusSearchWikiToNameMap;
$wgCirrusSearchLanguageToWikiMap = $wmgCirrusSearchLanguageToWikiMap;

$wgCirrusSearchEnableAltLanguage = $wmgCirrusSearchEnableAltLanguage;
$wgCirrusSearchLanguageDetectors = $wmgCirrusSearchLanguageDetectors;
$wgCirrusSearchTextcatLanguages = $wmgCirrusSearchTextcatLanguages;
$wgCirrusSearchTextcatModel = "$IP/vendor/wikimedia/textcat/LM-query";

$wgHooks['CirrusSearchMappingConfig'][] = function( array &$config, $mappingConfigBuilder ) {
	$config['page']['properties']['popularity_score'] = [
		'type' => 'double',
	];
};

// Set the scoring method
$wgCirrusSearchCompletionDefaultScore = 'popqual';

// PoolCounter needs to be adjusted to account for additional latency when default search
// is pointed at a remote datacenter. Currently this makes the assumption that it will either
// be eqiad or codfw which have ~40ms latency between them. Multiples are chosen using
// (p75 + cross dc latency)/p75
if ( $wgCirrusSearchDefaultCluster !== $wmfDatacenter ) {
	// prefix has p75 of ~30ms
	if ( isset( $wgPoolCounterConf[ 'CirrusSearch-Prefix' ] ) ) {
		$wgPoolCounterConf['CirrusSearch-Prefix']['workers'] *= 2;
	}
	// namespace has a p75 of ~15ms
	if ( isset( $wgPoolCounterConf['CirrusSearch-NamespaceLookup' ] ) ) {
		$wgPoolCounterConf['CirrusSearch-NamespaceLookup']['workers'] *= 3;
	}
	// completion has p75 of ~30ms
	if ( isset( $wgPoolCounterConf['CirrusSearch-Completion'] ) ) {
		$wgPoolCounterConf['CirrusSearch-Completion']['workers'] *= 2;
	}
}

// Enable completion suggester
$wgCirrusSearchUseCompletionSuggester = $wmgCirrusSearchUseCompletionSuggester;

// Configure sub-phrases completion
$wgCirrusSearchCompletionSuggesterSubphrases = $wmgCirrusSearchCompletionSuggesterSubphrases;

// Enable phrase suggester (did you mean)
$wgCirrusSearchEnablePhraseSuggest = $wmgCirrusSearchEnablePhraseSuggest;

// Configure ICU Folding
$wgCirrusSearchUseIcuFolding = $wmgCirrusSearchUseIcuFolding;

# Load per realm specific configuration, either:
# - CirrusSearch-labs.php
# - CirrusSearch-production.php
#
require "{$wmfConfigDir}/CirrusSearch-{$wmfRealm}.php";
