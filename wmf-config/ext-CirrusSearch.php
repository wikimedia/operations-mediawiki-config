<?php

// Inline comments are often used for noting the task(s) associated with specific configuration
// and requiring comments to be on their own line would reduce readability for this file
// phpcs:disable MediaWiki.WhiteSpace.SpaceBeforeSingleLineComment.NewLineComment

return [

// NOTE: don't forget to update TTM default cluster via
// $wgTranslateTranslationDefaultService in CommonSettings.php if you plan to
// bring down a specific cluster.
'wgCirrusSearchDefaultCluster' => [
	'default' => $GLOBALS['wmgDatacenter'],
],
// Kept for BC with SRE tools that checks siteinfo (see APIQuerySiteInfoGeneralInfo in CommonSettings.php)
'wmgCirrusSearchDefaultCluster' => [
	'default' => 'local',
],

'wgCirrusSearchClusterOverrides' => [
	'default' => [],
],

'wgCirrusSearchWriteClusters' => [
	'default' => [ 'eqiad', 'codfw', 'cloudelastic' ],
	'private' => [ 'eqiad', 'codfw' ],
],

'wgCirrusSearchReplicaGroup' => [
	'default' => [
		'type' => 'roundrobin',
		'groups' => [ 'psi', 'omega' ]
	],
	'cirrussearch-big-indices' => 'chi',
],

'wgCirrusSearchPreferRecentDefaultDecayPortion' => [
	'default' => 0,
	'wikinews' => 0.6,
],

'wgCirrusSearchWeights' => [
	'default' => [],
	'commonswiki' => [
		'title' => 25.0,
	],
	'testcommonswiki' => [
		'title' => 25.0,
	],
],

'wgCirrusSimilarityProfiles' => [
	// Push all our similarity settings to default so that we can reuse them
	// on multiple wikis
	'default' => [
		'wikibase_similarity' => [
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
		]
	],
],

'wgCirrusSearchSimilarityProfile' => [
	'default' => 'wmf_defaults',
	"wikidata" => "wikibase_similarity",
	"testwikidatawiki" => "wikibase_similarity",
	"commonswiki" => "wikibase_similarity",
	"testwikidatawiki" => "wikibase_similarity",
],

'wgCirrusSearchRescoreProfile' => [
	'default' => 'wsum_inclinks',
	'commonswiki' => 'classic_noboostlinks',
	'testcommonswiki' => 'classic_noboostlinks',
	'enwiki' => 'mlr-1024rs',
	'arwiki' => 'mlr-1024rs',
	'dewiki' => 'mlr-1024rs',
	'fawiki' => 'mlr-1024rs',
	'fiwiki' => 'mlr-1024rs',
	'frwiki' => 'mlr-1024rs',
	'idwiki' => 'mlr-1024rs',
	'itwiki' => 'mlr-1024rs',
	'jawiki' => 'classic', // TODO: switch back to 'mlr-1024rs',
	'kowiki' => 'classic', // TODO: switch back to 'mlr-1024rs',
	'nlwiki' => 'mlr-1024rs',
	'nowiki' => 'mlr-1024rs',
	'plwiki' => 'mlr-1024rs',
	'ptwiki' => 'mlr-1024rs',
	'ruwiki' => 'mlr-1024rs',
	'svwiki' => 'mlr-1024rs',
	'viwiki' => 'mlr-1024rs',
	'zhwiki' => 'wsum_inclinks', // TODO: Switch back to 'mlr-1024rs',
],

'wgCirrusSearchFullTextQueryBuilderProfile' => [
	'default' => 'perfield_builder',
],

// Enable crossprocess search (side bar)
'wgCirrusSearchEnableCrossProjectSearch' => [
	'default' => false,
	'wikipedia' => true, // Activated on all wikipedias

	'frwikibooks' => true, // T251683
	'frwiktionary' => true, // T250724
	// italian wikis used to have the old sidebar on all sisterwikis
	// use the one there too.
	'itwiktionary' => true,
	'itwikibooks' => true,
	'itwikinews' => true,
	'itwikiquote' => true,
	'itwikisource' => true,
	'itwikiversity' => true,
	'itwikivoyage' => true,
],

// Tune crossproject ordering
'wgCirrusSearchCrossProjectOrder' => [
	'default' => 'recall',
	'enwiki' => 'wmf_enwiki', // T171803: 'wikt' always first, 'b' always last, others ordered by recall
],

// Define list of projects to block from CrossProject search
// (only effective if SiteMatrix implementation is being used)
'wgCirrusSearchCrossProjectSearchBlockList' => [
	'default' => [],
	// Block wikinews and wikiversity T163463
	'enwiki' => [ 'n', 'v' ],
],

// Define overridden interwiki prefixes
// Mostly to match what's done in WikimediaMaintenance/dumpInterwiki.php ( see $prefixRewrites )
'wgCirrusSearchInterwikiPrefixOverrides' => [
	'default' => [],
	'svwiki' => [ 's' => 'src' ],
],

// Show/Hide multimedia content in the crossproject
// search results sidebar
'wgCirrusSearchCrossProjectShowMultimedia' => [
	'default' => true,
	'enwiki' => false, // T163463, requested during RfC
	// Disable multimedia on italian non-wikipedias
	'itwiktionary' => false,
	'itwikibooks' => false,
	'itwikinews' => false,
	'itwikiquote' => false,
	'itwikisource' => false,
	'itwikiversity' => false,
	'itwikivoyage' => false,
],

'wgCirrusSearchCrossProjectProfiles' => [
	'default' => [
		// full text wikivoyage results are often irrelevant, filter the
		// search with title matches to improve relevance
		'voy' => [
			'ftbuilder' => 'perfield_builder_title_filter',
			'rescore' => 'wsum_inclinks',
		],
	],
	'+enwiki' => [
		// T185250
		'wikt' => [
			'ftbuilder' => 'perfield_builder_title_filter',
			'rescore' => 'wsum_inclinks',
		],
	],
],

'wgCirrusSearchIgnoreOnWikiBoostTemplates' => [
	'default' => false,
	// on wiki boost templates have to high boosts for enwiki
	'enwiki' => true,
],

'wgCirrusSearchLanguageWeight' => [
	'default' => [
		'user' => 0.0,
		'wiki' => 0.0,
	],
	'mediawikiwiki' => [
		'user' => 5.0,
		'wiki' => 2.5,
	],
],

'wgCirrusSearchInstantIndexNew' => [
	'default' => [],
],

'wgCirrusSearchRefreshInterval' => [
	'default' => 30,
],

// Changing this for a wiki
// requires an in place reindex.  Last full review 2014-07-01.  See
// https://wikitech.wikimedia.org/wiki/Search/New#Estimating_the_number_of_shards_required
// for estimation of new wikis.

// Optimal shard count requires making a tradeoff between a few competing factors.

// Each Elasticsearch shard is actually a Lucene index,
//  which requires some amount of file descriptors/disk usage, compute, and RAM.
// So, a higher shard count causes more overhead, due to resource contention as well as "fixed costs".

// Since Elasticsearch is designed to be shardable and robust,
// if a node drops out of the cluster, shards must rebalance across the remaining nodes
// (likewise for changes in instance count, etc).
// Shard rebalancing is rate-limited by network throughput,
// and thus excessively large shards can cause the cluster to be stuck
// "recovering" (rebalancing) for an unacceptable amount of time.

// Thus the optimal shard size is a balancing act between overhead (which is optimized via having larger shards),
// and rebalancing time (which is optimized via smaller, more numerous shards).
// Additionally, due to the problem of fragmentation,
// we also don't want a given shard to be too large a % of the available disk capacity.

// Currently (01/07/2020 DD/MM/YY), in most cases we don't want shards to exceed 50GB,
// and ideally they wouldn't be smaller than 10GB
// (but note that for small indices this is unavoidable).
// Once our Elasticsearch cluster has 10G networking, we can increase our desired shard size.
'wmgCirrusSearchShardCount' => [
	// Most wikis are too small to be worth sharding
	'default' => [ 'content' => 1, 'general' => 1, 'titlesuggest' => 1, 'archive' => 1 ],
	'arwiki' => [ 'content' => 5, 'general' => 4, 'titlesuggest' => 1, 'archive' => 1 ],
	'arwikisource' => [ 'content' => 2, 'general' => 1, 'titlesuggest' => 1, 'archive' => 1 ],
	'bgwiki' => [ 'content' => 2, 'general' => 1, 'titlesuggest' => 1, 'archive' => 1 ],
	'cawiki' => [ 'content' => 5, 'general' => 1, 'titlesuggest' => 1, 'archive' => 1 ],
	'cebwiki' => [ 'content' => 4, 'general' => 1, 'titlesuggest' => 2, 'archive' => 1 ],
	// Commons is special and has a 'file' index in addition to the regular ones.
	// We're sharding 'file' like it is a content index because searching it is
	// very very common.
	'commonswiki' => [ 'content' => 2, 'general' => 8, 'file' => 32, 'titlesuggest' => 1, 'archive' => 1 ],
	'testcommonswiki' => [ 'content' => 1, 'general' => 8, 'file' => 21, 'titlesuggest' => 1, 'archive' => 1 ],
	'cswiki' => [ 'content' => 3, 'general' => 1, 'titlesuggest' => 1, 'archive' => 1 ],
	'dawiki' => [ 'content' => 2, 'general' => 1, 'titlesuggest' => 1, 'archive' => 1 ],
	'dewiki' => [ 'content' => 9, 'general' => 8, 'titlesuggest' => 3, 'archive' => 1 ],
	'dewikisource' => [ 'content' => 3, 'general' => 1, 'titlesuggest' => 1, 'archive' => 1 ],
	'elwiki' => [ 'content' => 2, 'general' => 1, 'titlesuggest' => 1, 'archive' => 1 ],
	// These shards tend to be closer to our threshold of 50GB.
	// We shard enwiki more so that most servers have 2 enwiki shards.
	'enwiki' => [ 'content' => 15, 'general' => 21, 'titlesuggest' => 4, 'archive' => 1 ],
	'enwikinews' => [ 'content' => 1, 'general' => 4, 'titlesuggest' => 1, 'archive' => 1 ],
	'enwikisource' => [ 'content' => 7, 'general' => 1, 'titlesuggest' => 1, 'archive' => 1 ],
	'enwiktionary' => [ 'content' => 5, 'general' => 2, 'titlesuggest' => 2, 'archive' => 1 ],
	'eswiki' => [ 'content' => 7, 'general' => 6, 'titlesuggest' => 2, 'archive' => 1 ],
	'eswikisource' => [ 'content' => 2, 'general' => 1, 'titlesuggest' => 1, 'archive' => 1 ],
	'fawiki' => [ 'content' => 5, 'general' => 2, 'titlesuggest' => 2, 'archive' => 1 ],
	'fiwiki' => [ 'content' => 3, 'general' => 8, 'titlesuggest' => 1, 'archive' => 1 ],
	'frwiki' => [ 'content' => 7, 'general' => 8, 'titlesuggest' => 3, 'archive' => 1 ],
	'frwikisource' => [ 'content' => 7, 'general' => 1, 'titlesuggest' => 1, 'archive' => 1 ],
	'frwiktionary' => [ 'content' => 3, 'general' => 1, 'titlesuggest' => 1, 'archive' => 1 ],
	'hewiki' => [ 'content' => 3, 'general' => 2, 'titlesuggest' => 1, 'archive' => 1 ],
	'hewikisource' => [ 'content' => 2, 'general' => 1, 'titlesuggest' => 1, 'archive' => 1 ],
	'huwiki' => [ 'content' => 4, 'general' => 2, 'titlesuggest' => 1, 'archive' => 1 ],
	'hywiki' => [ 'content' => 2, 'general' => 1, 'titlesuggest' => 1, 'archive' => 1 ],
	'idwiki' => [ 'content' => 2, 'general' => 2, 'titlesuggest' => 1, 'archive' => 1 ],
	'incubatorwiki' => [ 'content' => 1, 'general' => 2, 'titlesuggest' => 1, 'archive' => 1 ],
	'itwiki' => [ 'content' => 7, 'general' => 8, 'titlesuggest' => 2, 'archive' => 1 ],
	'itwikisource' => [ 'content' => 2, 'general' => 1, 'titlesuggest' => 1, 'archive' => 1 ],
	'jawiki' => [ 'content' => 7, 'general' => 6, 'titlesuggest' => 2, 'archive' => 1 ],
	'kkwiki' => [ 'content' => 2, 'general' => 1, 'titlesuggest' => 1, 'archive' => 1 ],
	'kowiki' => [ 'content' => 4, 'general' => 1, 'titlesuggest' => 1, 'archive' => 1 ],
	'ltwiktionary' => [ 'content' => 2, 'general' => 1, 'titlesuggest' => 1, 'archive' => 1 ],
	'mswiki' => [ 'content' => 2, 'general' => 1, 'titlesuggest' => 1, 'archive' => 1 ],
	'metawiki' => [ 'content' => 1, 'general' => 8, 'titlesuggest' => 1, 'archive' => 1 ],
	'mgwiktionary' => [ 'content' => 4, 'general' => 1, 'titlesuggest' => 1, 'archive' => 1 ],
	'nlwiki' => [ 'content' => 7, 'general' => 4, 'titlesuggest' => 2, 'archive' => 1 ],
	'nowiki' => [ 'content' => 3, 'general' => 2, 'titlesuggest' => 1, 'archive' => 1 ],
	'plwiki' => [ 'content' => 7, 'general' => 3, 'titlesuggest' => 2, 'archive' => 1 ],
	'plwikisource' => [ 'content' => 2, 'general' => 1, 'titlesuggest' => 1, 'archive' => 1 ],
	'ptwiki' => [ 'content' => 7, 'general' => 5, 'titlesuggest' => 2, 'archive' => 1 ],
	'rowiki' => [ 'content' => 3, 'general' => 2, 'titlesuggest' => 1, 'archive' => 1 ],
	'ruwiki' => [ 'content' => 7, 'general' => 8, 'titlesuggest' => 3, 'archive' => 1 ],
	'ruwikinews' => [ 'content' => 4, 'general' => 4, 'titlesuggest' => 1, 'archive' => 1 ],
	'ruwikisource' => [ 'content' => 4, 'general' => 1, 'titlesuggest' => 1, 'archive' => 1 ],
	'ruwiktionary' => [ 'content' => 3, 'general' => 1, 'titlesuggest' => 1, 'archive' => 1 ],
	'shwiki' => [ 'content' => 4, 'general' => 1, 'titlesuggest' => 1, 'archive' => 1 ],
	'srwiki' => [ 'content' => 3, 'general' => 1, 'titlesuggest' => 1, 'archive' => 1 ],
	'svwiki' => [ 'content' => 7, 'general' => 2, 'titlesuggest' => 4, 'archive' => 1 ],
	'thwiki' => [ 'content' => 2, 'general' => 1, 'titlesuggest' => 1, 'archive' => 1 ],
	'trwiki' => [ 'content' => 2, 'general' => 2, 'titlesuggest' => 1, 'archive' => 1 ],
	'ukwiki' => [ 'content' => 7, 'general' => 2, 'titlesuggest' => 1, 'archive' => 1 ],
	'viwiki' => [ 'content' => 6, 'general' => 5, 'titlesuggest' => 1, 'archive' => 1 ],
	'wikidatawiki' => [ 'content' => 21, 'general' => 1, 'archive' => 1 ],
	'warwiki' => [ 'content' => 2, 'general' => 1, 'titlesuggest' => 1, 'archive' => 1 ],
	'zhwiki' => [ 'content' => 7, 'general' => 5, 'titlesuggest' => 2, 'archive' => 1 ],
	'zhwikisource' => [ 'content' => 5, 'general' => 1, 'titlesuggest' => 1, 'archive' => 1 ],
],

// Most wikis are fine with 0-2 replicas for all indexes
// some of the larger ones will want more replicas for content indexes
'wgCirrusSearchReplicas' => [
	// NOTE: cloudelastic is intentionally set to an "unsafe" mode with 1 replica (see T231517)
	'default' => [
		'eqiad' => [ 'content' => '0-2', 'general' => '0-2', 'titlesuggest' => '0-2', 'archive' => '0-2' ],
		'codfw' => [ 'content' => '0-2', 'general' => '0-2', 'titlesuggest' => '0-2', 'archive' => '0-2' ],
		'cloudelastic' => [ 'content' => '0-1', 'general' => '0-1', 'titlesuggest' => '0-1', 'archive' => '0-1' ],
	],
	'commonswiki' => [
		'eqiad' => [ 'content' => '0-2', 'general' => '0-2', 'titlesuggest' => '0-2', 'file' => '0-2', 'archive' => '0-2' ],
		'codfw' => [ 'content' => '0-2', 'general' => '0-2', 'titlesuggest' => '0-2', 'file' => '0-2', 'archive' => '0-2' ],
		'cloudelastic' => [ 'content' => '0-1', 'general' => '0-1', 'titlesuggest' => '0-1', 'file' => '0-1', 'archive' => '0-1' ],
	],
	'enwiki' => [
		'eqiad' => [ 'content' => '0-2', 'general' => '0-2', 'titlesuggest' => '0-3', 'archive' => '0-2' ],
		'codfw' => [ 'content' => '0-2', 'general' => '0-2', 'titlesuggest' => '0-3', 'archive' => '0-2' ],
		'cloudelastic' => [ 'content' => '0-1', 'general' => '0-1', 'titlesuggest' => '0-1', 'archive' => '0-1' ],
	],
	'dewiki' => [
		'eqiad' => [ 'content' => '0-3', 'general' => '0-2', 'titlesuggest' => '0-2', 'archive' => '0-2' ],
		'codfw' => [ 'content' => '0-3', 'general' => '0-2', 'titlesuggest' => '0-2', 'archive' => '0-2' ],
		'cloudelastic' => [ 'content' => '0-1', 'general' => '0-1', 'titlesuggest' => '0-1', 'archive' => '0-1' ],
	],
],

'wgCirrusSearchMaxShardsPerNode' => [
	'default' => [],
	'commonswiki' => [
		'eqiad' => [ 'file' => 4, 'general' => 2 ],
		'codfw' => [ 'file' => 4, 'general' => 2 ],
		'cloudelastic' => []
	],
	'dewiki' => [
		'eqiad' => [ 'content' => 2 ],
		'codfw' => [ 'content' => 2 ],
		'cloudelastic' => []
	],
	'wikidatawiki' => [
		'eqiad' => [ 'content' => 3 ],
		'codfw' => [ 'content' => 3 ],
		'cloudelastic' => []
	],
	'enwiktionary' => [
		'eqiad' => [ 'content' => 1, 'general' => 1 ],
		'codfw' => [ 'content' => 1, 'general' => 1 ],
		'cloudelastic' => []
	],
	'enwiki' => [
		'eqiad' => [ 'content' => 1, 'general' => 3 ],
		'codfw' => [ 'content' => 1, 'general' => 3 ],
		'cloudelastic' => []
	],
	'eswiki' => [
		'eqiad' => [ 'content' => 1 ],
		'codfw' => [ 'content' => 1 ],
		'cloudelastic' => []
	],
	'frwiki' => [
		'eqiad' => [ 'content' => 1 ],
		'codfw' => [ 'content' => 1 ],
		'cloudelastic' => []
	],
	'nlwiki' => [
		'eqiad' => [ 'content' => 1 ],
		'codfw' => [ 'content' => 1 ],
		'cloudelastic' => []
	],
	'ptwiki' => [
		'eqiad' => [ 'content' => 1 ],
		'codfw' => [ 'content' => 1 ],
		'cloudelastic' => []
	],
	'ruwiki' => [
		'eqiad' => [ 'content' => 1 ],
		'codfw' => [ 'content' => 1 ],
		'cloudelastic' => []
	],
	'zhwiki' => [
		'eqiad' => [ 'content' => 1 ],
		'codfw' => [ 'content' => 1 ],
		'cloudelastic' => []
	],
],

// Setup our custom index settings, only used at index
// creation time.
'wgCirrusSearchExtraIndexSettings' => [
	'default' => [
		// indexing slow log
		'indexing.slowlog.threshold.index.warn' => '10s',
		'indexing.slowlog.threshold.index.info' => '5s',
		'indexing.slowlog.threshold.index.debug' => '2s',
		'indexing.slowlog.threshold.index.trace' => -1,
		// query slow log
		'search.slowlog.threshold.query.warn' => '60s',
		'search.slowlog.threshold.query.info' => '10s',
		'search.slowlog.threshold.query.debug' => '5s',
		'search.slowlog.threshold.query.trace' => -1,
		// fetch slow log
		'search.slowlog.threshold.fetch.warn' => '10s',
		'search.slowlog.threshold.fetch.info' => '5s',
		'search.slowlog.threshold.fetch.debug' => '1s',
		'search.slowlog.threshold.fetch.trace' => '-1',
		// Number of merge threads to use. Use only 1 thread
		// (instead of 3) to avoid updates interfering with
		// actual searches
		'merge.scheduler.max_thread_count' => 1,
	],
	'+wikidata' => [
		// increase defaults to add language specific fields
		'index.mapping.total_fields.limit' => 5000,
	],
],

// Enable completion suggester on all wikis (except wikidata)
'wgCirrusSearchUseCompletionSuggester' => [
	'default' => 'yes',
	'wikidatawiki' => 'no',
	'labtestwiki' => 'no', // disable while T328289 gets fixed
],

// wgCirrusSearchCompletionSuggesterSubphrases @{
// Please verify mem usage on the cluster before adding new wiki here.
// NOTE: activate 'build' => true first then verify that the index has been
// updated with the new mapping then you can switch 'use' to true
'wgCirrusSearchCompletionSuggesterSubphrases' => [
	'default' => [
		'build' => false,
		'use' => false,
		'type' => 'anywords',
		'limit' => 10,
	],
	'wikisource' => [
		'build' => true,
		'use' => true,
		'type' => 'anywords',
		'limit' => 10,
	],
	'mediawikiwiki' => [
		'build' => true,
		'use' => true,
		'type' => 'anywords',
		'limit' => 10,
	],
	'wikitech' => [
		'build' => true,
		'use' => true,
		'type' => 'anywords',
		'limit' => 10,
	],
	'officewiki' => [
		'build' => true,
		'use' => true,
		'type' => 'anywords',
		'limit' => 10,
	],
],

// Default profile for autocomplete for when the completion suggester is enabled
'wgCirrusSearchCompletionSettings' => [
	'default' => 'fuzzy',
	'mediawikiwiki' => 'fuzzy-subphrases',
	'wikitech' => 'fuzzy-subphrases',
	'officewiki' => 'fuzzy-subphrases',
],

// Inject defaultsort in autocomplete suggestions served by the completion suggester
'wgCirrusSearchCompletionSuggesterUseDefaultSort' => [
	'default' => false,
	'mnwiki' => true, // T327878
],

// @} end of wgCirrusSearchCompletionSuggesterSubphrases

// Enable phrase suggester (did you mean) on all wikis (except wikidata)
'wgCirrusSearchEnablePhraseSuggest' => [
	'default' => true,
	'wikidatawiki' => false,
	'testwikidatawiki' => false
],

// wgCirrusSearchRecycleCompletionSuggesterIndex @{
// Recycle suggester indices for small wikis (less than 100MB store size)
'wgCirrusSearchRecycleCompletionSuggesterIndex' => [
	'default' => true,
	'enwiki' => false,
	'svwiki' => false,
	'enwiktionary' => false,
	'ruwiki' => false,
	'mgwiktionary' => false,
	'dewiki' => false,
	'frwiki' => false,
	'cebwiki' => false,
	'nlwiki' => false,
	'frwiktionary' => false,
	'eswiki' => false,
	'shwiki' => false,
	'itwiki' => false,
	'warwiki' => false,
	'jawiki' => false,
	'plwiki' => false,
	'fawiki' => false,
	'ptwiki' => false,
	'zhwiki' => false,
	'viwiki' => false,
	'ukwiki' => false,
	'zhwiktionary' => false,
	'arwiki' => false,
	'cawiki' => false,
	'ruwiktionary' => false,
	'srwiki' => false,
	'ruwikisource' => false,
	'enwikisource' => false,
	'shwiktionary' => false,
	'rowiki' => false,
	'eswiktionary' => false,
	'nowiki' => false,
	'specieswiki' => false,
	'idwiki' => false,
	'fiwiki' => false,
	'kowiki' => false,
	'huwiki' => false,
	'cswiki' => false,
	'ltwiktionary' => false,
],
// @} end of wgCirrusSearchRecycleCompletionSuggesterIndex

// Disable phrase rescore on queries with too many tokens.
// Bandaid for T169498, should be removed when a proper
// fix is determined
'wgCirrusSearchMaxPhraseTokens' => [
	'default' => 10,
],

// Configure ICU Folding, 'default': controlled by cirrus
// 'no': disable, 'yes': force
'wgCirrusSearchUseIcuFolding' => [
	'default' => 'default',
],

'wgCirrusSearchAllFields' => [
	'default' => [ 'build' => true, 'use' => true ],
],

'wgCirrusSearchNearMatchWeight' => [
	'default' => 10, // T257922
],

'wgCirrusSearchNamespaceWeights' => [
	'default' => [],
	'mediawikiwiki' => [ // T155142
		12 => 0.9,
		100 => 0.9,
		102 => 0.9,
		104 => 0.9,
		106 => 0.9,
	],
	'commonswiki' => [
		6 => 1.0,
	],
	'labswiki' => [
		12 => 1.0, // Put NS_HELP on equal footing with NS_MAIN
	],
	'metawiki' => [
		// T260569
		200 => 0.6, // grants
		202 => 0.6, // research
	],
	// Author namespace on wikisource should match NS_MAIN weight
	'arwikisource' => [ 102 => 1.0 ],
	'aswikisource' => [ 102 => 1.0 ],
	'bewikisource' => [ 102 => 1.0 ],
	'bgwikisource' => [ 100 => 1.0 ],
	'bnwikisource' => [ 100 => 1.0 ],
	'brwikisource' => [ 104 => 1.0 ],
	'cawikisource' => [ 106 => 1.0 ],
	'cswikisource' => [ 100 => 1.0 ],
	'dawikisource' => [ 102 => 1.0 ],
	'elwikisource' => [ 108 => 1.0 ],
	'enwikisource' => [ 102 => 1.0 ],
	'etwikisource' => [ 106 => 1.0 ],
	'euwikisource' => [ 106 => 1.0 ],
	'fawikisource' => [ 102 => 1.0 ],
	'frwikisource' => [ 102 => 1.0 ],
	'hewikisource' => [ 108 => 1.0 ],
	'hrwikisource' => [ 100 => 1.0 ],
	'huwikisource' => [ 100 => 1.0 ],
	'hywikisource' => [ 100 => 1.0 ],
	'idwikisource' => [ 100 => 1.0 ],
	'iswikisource' => [ 102 => 1.0 ],
	'itwikisource' => [ 102 => 1.0 ],
	'knwikisource' => [ 102 => 1.0 ],
	'kowikisource' => [ 100 => 1.0 ],
	'lawikisource' => [ 102 => 1.0 ],
	'lijwikisource' => [ 102 => 1.0 ],
	'mlwikisource' => [ 100 => 1.0 ],
	'mrwikisource' => [ 102 => 1.0 ],
	'napwikisource' => [ 102 => 1.0 ],
	'nlwikisource' => [ 102 => 1.0 ],
	'nowikisource' => [ 102 => 1.0 ],
	'plwikisource' => [ 104 => 1.0 ],
	'ptwikisource' => [ 102 => 1.0 ],
	'rowikisource' => [ 102 => 1.0 ],
	'svwikisource' => [ 106 => 1.0 ],
	'tewikisource' => [ 102 => 1.0 ],
	'thwikisource' => [ 102 => 1.0 ],
	'trwikisource' => [ 100 => 1.0 ],
	'ukwikisource' => [ 102 => 1.0 ],
	'vecwikisource' => [ 100 => 1.0 ],
	'viwikisource' => [ 102 => 1.0 ],
	'wawikisource' => [ 100 => 1.0 ],
	'zhwikisource' => [ 102 => 1.0 ],
],

// Enable the "Give us feedback" link after search results on enwiki
'wgCirrusSearchFeedbackLink' => [
	'default' => false,
],

// NOTE: When enabling a profile that embarks the phrase suggester
// make sure to enable it with wgCirrusSearchEnablePhraseSuggest
// and reindex the wiki
// Available profiles are in CirrusSearch/profiles/FallbackProfiles.config.php
// or defined in the config var wgCirrusSearchFallbackProfiles
'wgCirrusSearchFallbackProfile' => [
	'default' => 'phrase_suggest',
	// Nothing for wikidata
	'wikidatawiki' => 'none',
	'testwikidatawiki' => 'none',
	// PhraseSuggest & Language detection
	'eswiki' => 'phrase_suggest_and_language_detection',
	'itwiki' => 'phrase_suggest_and_language_detection',
	'jawiki' => 'phrase_suggest_and_language_detection',
	'nlwiki' => 'phrase_suggest_and_language_detection',
	'ptwiki' => 'phrase_suggest_and_language_detection',
	'ruwiki' => 'phrase_suggest_and_language_detection',
	// Glent M0 & PhraseSuggest & Language detection
	'dewiki' => 'phrase_suggest_glentM0_and_langdetect',
	'enwiki' => 'phrase_suggest_glentM0_and_langdetect',
	'frwiki' => 'phrase_suggest_glentM0_and_langdetect',
],

'wgCirrusSearchUserTesting' => [
	'default' => [
		// T219534
		'mlr-2020-test' => [
			'buckets' => [
				'control' => [
					'trigger' => 'control',
				],
				'mlr-2020-test' => [
					'trigger' => 'mlr-2020-test',
					'globals' => [
						'wgCirrusSearchRescoreProfile' => 'mlr-1024rs',
					],
				],
			],
		],
		# T246947
		'glent_m0' => [
			'buckets' => [
				'control' => [
					'trigger' => 'control',
					'globals' => [
						// 'wgCirrusSearchFallbackProfile' => 'phrase_suggest_and_language_detection',
					],
				],
				'glent_m0' => [
					'trigger' => 'glent_m0',
					'globals' => [
						'wgCirrusSearchFallbackProfile' => 'phrase_suggest_glentM0_and_langdetect',
					],
				],
			],
		],
		'T262612_glent_m01' => [
			'buckets' => [
				'control' => [
					'trigger' => 'control',
					'globals' => [
						// 'wgCirrusSearchFallbackProfile' => 'phrase_suggest_and_language_detection',
					],
				],
				'glent_m01' => [
					'trigger' => 'glent_m01',
					'globals' => [
						'wgCirrusSearchFallbackProfile' => 'phrase_suggest_glentM01_and_langdetect',
					],
				],
			],
		],
	],
],

'wgCirrusSearchLanguageDetectors' => [
	'default' => [],
	'dewiki' => [ 'textcat' => 'CirrusSearch\\LanguageDetector\\TextCat' ],
	'enwiki' => [ 'textcat' => 'CirrusSearch\\LanguageDetector\\TextCat' ],
	'eswiki' => [ 'textcat' => 'CirrusSearch\\LanguageDetector\\TextCat' ],
	'frwiki' => [ 'textcat' => 'CirrusSearch\\LanguageDetector\\TextCat' ],
	'itwiki' => [ 'textcat' => 'CirrusSearch\\LanguageDetector\\TextCat' ],
	'jawiki' => [ 'textcat' => 'CirrusSearch\\LanguageDetector\\TextCat' ],
	'nlwiki' => [ 'textcat' => 'CirrusSearch\\LanguageDetector\\TextCat' ],
	'ptwiki' => [ 'textcat' => 'CirrusSearch\\LanguageDetector\\TextCat' ],
	'ruwiki' => [ 'textcat' => 'CirrusSearch\\LanguageDetector\\TextCat' ],
],

// Enable interwiki search by language detection. The list of language
// detected and their corresponding wiki is defined by
// wgCirrusSearchLanguageToWikiMap and SiteMatrix.
// Note that if language detectors are enabled they will always run, this
// gates if the result of running is shown to the user (for AB test control
// bucket reasons).
// NOTE: be sure to enable a fallback profile that triggers language detection
//       see wgCirrusSearchFallbackProfile
'wgCirrusSearchEnableAltLanguage' => [
	'default' => false,
	'dewiki' => true,
	'enwiki' => true,
	'eswiki' => true,
	'frwiki' => true,
	'itwiki' => true,
	'jawiki' => true,
	'nlwiki' => true,
	'ptwiki' => true,
	'ruwiki' => true,
],

'wgCirrusSearchTextcatLanguages' => [
	'default' => [],
	'dewiki' => [
		'de', 'en', 'la', 'it', 'es', 'fr', 'zh', 'pl',
		'vi', 'el', 'ru', 'ar', 'hi', 'th', 'ko', 'ja',
	],
	'enwiki' => [
		'en', 'zh', 'es', 'ar', 'de', 'fa', 'fr', 'id',
		'pl', 'ru', 'vi', 'it', 'ja', 'pt', 'cs', 'bn',
		'hr', 'he', 'no', 'af', 'is', 'tl', 'th', 'hu',
		'ga', 'ko', 'uk', 'ur', 'hi', 'el', 'te', 'ka',
	],
	'eswiki' => [
		'es', 'en', 'la', 'ru', 'zh', 'pt', 'it', 'fr',
		'de', 'ar', 'ja',
	],
	'frwiki' => [
		'fr', 'en', 'ar', 'pt', 'de', 'es', 'ru', 'zh',
		'nl', 'pl', 'it', 'th', 'sv', 'la', 'is', 'hy',
		'hu', 'br', 'el', 'he', 'ko',
	],
	'itwiki' => [
		'it', 'en', 'de', 'ru', 'ar', 'zh', 'pl', 'el',
		'ko',
	],
	'jawiki' => [
		'ja', 'en', 'zh', 'ko', 'de', 'ar', 'he',
	],
	'nlwiki' => [
		'nl', 'en', 'fr', 'de', 'es', 'la', 'zh', 'pl',
		'ar', 'vi', 'pt', 'my', 'ko', 'hr', 'da', 'cs',
		'el', 'he', 'ja', 'ru',
	],
	'ptwiki' => [
		'pt', 'en', 'tl', 'ru', 'fr', 'he', 'ar', 'zh',
		'ko', 'el',
	],
	'ruwiki' => [
		'ru', 'en', 'uk', 'de', 'ka', 'hy', 'lv', 'ja',
		'fi', 'es', 'ar', 'he', 'zh',
	],
],

// Method to use when detecting the namespace prefix
// within queries:
// elastic: lookup against elastic (default)
// utr30: uses PHP Transliterator with UTR30 rule
// naise: uses PHP Transliterator with naive folding rules (unicode diacritics
// removal)
'wgCirrusSearchNamespaceResolutionMethod' => [
	'default' => 'utr30',
],

// List of languages detected by the short-text
// profiles.
// See: https://www.mediawiki.org/wiki/User:TJones_(WMF)/Notes/Language_Detection_Evaluation
// This includes also the lang codes that might be used by browsers in Accept-Language
# wgCirrusSearchLanguageToWikiMap @{
'wgCirrusSearchLanguageToWikiMap' => [
	'default' => [],
	'wikipedia' => [
		"ar" => "ar",
		"ay" => "ay", // not detected
		"az" => "az", // not detected
		"bg" => "bg",
		"bn" => "bn",
		"ca" => "ca",
		"cs" => "cs",
		"da" => "da",
		"de" => "de",
		"el" => "el",
		"en" => "en",
		"es" => "es",
		"et" => "et",
		"fa" => "fa",
		"fi" => "fi",
		"fil" => "tl", // unknown code, filipino? mapping to tagalog, maybe fijian?
		"fr" => "fr",
		"gu" => "gu",
		"he" => "he",
		"hi" => "hi",
		"hr" => "hr",
		"hu" => "hu",
		"id" => "id",
		"it" => "it",
		"ja" => "ja",
		"km" => "km", // not detected
		"ko" => "ko",
		"lt" => "lt",
		"lv" => "lv",
		"mk" => "mk",
		"ml" => "ml",
		"mr" => "mr", // not detected
		"ms" => "ms", // not detected
		"nb" => "no", // not detected but covered by no
		"nl" => "nl",
		"no" => "no",
		"pa" => "pa",
		"pl" => "pl",
		"pt" => "pt",
		"ro" => "ro", // very bad precision
		"ru" => "ru",
		"si" => "si",
		"sk" => "sk", // not detected
		"sl" => "sl", // not detected
		"sq" => "sq",
		"sv" => "sv",
		"sw" => "sw", // not detected
		"ta" => "ta",
		"te" => "te",
		"th" => "th",
		"tl" => "tl",
		"tr" => "tr",
		"uk" => "uk",
		"ur" => "ur",
		"vi" => "vi",
		"zh" => "zh", // not detected (browser header). Usually detected as zh-cn, zh-hk, zh-tw or zh-hans-cn, zh-hant-hk, zh-hant-tw
		"zh-cn" => "zh",
		"zh-hans-cn" => "zh",
		"zh-hant-hk" => "zh",
		"zh-hant-tw" => "zh",
		"zh-hk" => "zh",
		"zh-tw" => "zh"
	],
],
# @} end of wgCirrusSearchLanguageToWikiMap

// Enable archive for testwiki
'wgCirrusSearchIndexDeletes' => [
	'default' => true,
	'wikidatawiki' => false,
	'testwikidatawiki' => false,
],

'wgCirrusSearchEnableArchive' => [
	'default' => true,
	'wikidatawiki' => false,
	'testwikidatawiki' => false,
],

'wmgCirrusSearchMLRModelFallback' => [
	'default' => 'wsum_inclinks',
	'enwiki' => 'wsum_inclinks_pv',
],

'wmgCirrusSearchMLRModel' => [
	'default' => false,
	'enwiki' => [
		'mlr-1024rs' => [
			// Name of model stored in elasticsearch ltr plugin
			'model' => 'enwiki-20220421-20180215-query_explorer',
			// Number of results to score per-shard. Defaults to
			// 1024 if not provided.
			'window' => 448,
		],
	],
	'arwiki' => [
		'mlr-1024rs' => [ 'model' => 'arwiki-20220421-20180215-query_explorer' ],
	],
	'fawiki' => [
		'mlr-1024rs' => [ 'model' => 'fawiki-20220421-20180215-query_explorer' ],
	],
	/* TODO: re-enable once we have a model trained with BM25 features (T219534)
	'jawiki' => [
		'mlr-1024rs' => [ 'model' => 'jawiki-20220421-20180215-query_explorer' ],
	],
	*/
	'svwiki' => [
		'mlr-1024rs' => [ 'model' => 'svwiki-20220421-20180215-query_explorer' ],
	],
	'frwiki' => [
		'mlr-1024rs' => [ 'model' => 'frwiki-20220421-20180215-query_explorer' ],
	],
	'itwiki' => [
		'mlr-1024rs' => [ 'model' => 'itwiki-20220421-20180215-query_explorer' ],
	],
	'ptwiki' => [
		'mlr-1024rs' => [ 'model' => 'ptwiki-20220421-20180215-query_explorer' ],
	],
	'ruwiki' => [
		'mlr-1024rs' => [ 'model' => 'ruwiki-20220421-20180215-query_explorer' ],
	],
	'dewiki' => [
		'mlr-1024rs' => [ 'model' => 'dewiki-20220421-20180215-query_explorer' ],
	],
	'fiwiki' => [
		'mlr-1024rs' => [ 'model' => 'fiwiki-20220421-20180215-query_explorer' ],
	],
	'hewiki' => [
		'mlr-1024rs' => [ 'model' => 'hewiki-20220421-20180215-query_explorer' ],
	],
	'idwiki' => [
		'mlr-1024rs' => [ 'model' => 'idwiki-20220421-20180215-query_explorer' ],
	],
	/* TODO: re-enable once we have a model trained with the nori analyzer (T219534)
	'kowiki' => [
		'mlr-1024rs' => [ 'model' => 'kowiki-20220421-20180215-query_explorer' ],
	],
	*/
	'nlwiki' => [
		'mlr-1024rs' => [ 'model' => 'nlwiki-20220421-20180215-query_explorer' ],
	],
	'nowiki' => [
		'mlr-1024rs' => [ 'model' => 'nowiki-20220421-20180215-query_explorer' ],
	],
	'plwiki' => [
		'mlr-1024rs' => [ 'model' => 'plwiki-20220421-20180215-query_explorer' ],
	],
	'viwiki' => [
		'mlr-1024rs' => [ 'model' => 'viwiki-20220421-20180215-query_explorer' ],
	],
	'zhwiki' => [
		'mlr-1024rs' => [ 'model' => 'zhwiki-20220421-20180215-query_explorer' ],
	],
	'kowiki' => [
		'mlr-1024rs' => [ 'model' => 'kowiki-20220421-20180215-query_explorer' ],
	],
	/* TODO: re-enable once we have a model trained with BM25 features (T219534)
	'zhwiki' => [
		'mlr-1024rs' => [ 'model' => 'zhwiki-20220421-20180215-query_explorer' ],
	],
	*/
],

'wgCirrusSearchElasticQuirks' => [
	'default' => [
		'retry_on_conflict' => true,
	]
],

# Turn off leading wildcard matches, they are a very slow and inefficient query
'wgCirrusSearchAllowLeadingWildcard' => [
	'default' => false
],

# Turn off the more accurate but slower search mode.  It is most helpful when you
# have many small shards.  We don't do that in production and we could use the speed.
'wgCirrusSearchMoreAccurateScoringMode' => [
	'default' => false
],

# Limit the number of states generated by wildcard queries (500 will allow about 20 wildcards)
'wgCirrusSearchQueryStringMaxDeterminizedStates' => [
	'default' => 500
],

# Ban the hebrew plugin, it is unstable
'wgCirrusSearchBannedPlugins' => [
	'default' => [ 'elasticsearch-analysis-hebrew' ]
],

# Build and use an ngram index for faster regex matching
'wgCirrusSearchWikimediaExtraPlugin' => [
	'default' => [
		'regex' => [
			'build',
			'use',
		],
		'super_detect_noop' => true,
		'documentVersion' => true,
		'token_count_router' => true,
		'term_freq' => true
	]
],

# Enable the ores_articletopics field
'wgCirrusSearchWMFExtraFeatures' => [
	'default' => [
		'ores_articletopics' => [
			'build' => true,
			'use' => true,
		],
		'weighted_tags' => [
			'build' => true,
			'use' => true,
		]
	]
],

# Enable the "experimental" highlighter on all wikis
'wgCirrusSearchUseExperimentalHighlighter' => [
	'default' => true
],

'wgCirrusSearchOptimizeIndexForExperimentalHighlighter' => [
	'default' => true
],

// We had an incident of filling up the entire clusters redis instances after
// 6 hours, and problems with the new kafka (before we enabled compression)
// based job queue as well. Cut the time down to 2 hours, as this is not supposed
// to ride out a full-fledged outage, but paper over minor unavailabilities
// for cluster/network/etc maintenance.
'wgCirrusSearchDropDelayedJobsAfter' => [
	'default' => 60 * 60 * 2
],

// Commons is special
'wgCirrusSearchNamespaceMappings' => [
	'default' => [],
	'commonswiki' => [ NS_FILE => 'file' ],
],

// T94856 - makes searching difficult for locally uploaded files
// T76957 - doesn't make sense to have Commons files on foundationwiki search
'wgCirrusSearchExtraIndexes' => [
	'default' => [ NS_FILE => [ 'commonswiki_file' ] ],
	'commonswiki' => [], // Commons is special
	'officewiki' => [], // T94856 - makes searching difficult for locally uploaded files
	'foundationwiki' => [], // T76957 - doesn't make sense to have Commons files on foundationwiki search
],

'wgCirrusSearchExtraIndexBoostTemplates' => [
	'default' => [
		'commonswiki_file' => [
			'wiki' => 'commonswiki',
			'boosts' => [
				// Copied from https://commons.wikimedia.org/wiki/MediaWiki:Cirrussearch-boost-templates
				'Template:Assessments/commons/featured' => 2.5,
				'Template:Picture_of_the_day' => 1.5,
				'Template:Valued_image' => 1.75,
				'Template:Assessments' => 1.5,
				'Template:Quality_image' => 1.75,
			],
		],
	],
	'commonswiki' => [], // Commons is special
	'officewiki' => [], // T94856 - makes searching difficult for locally uploaded files
	'foundationwiki' => [], // T76957 - doesn't make sense to have Commons files on foundationwiki search
],

'wgCirrusSearchTextcatConfig' => [
	'default' => [
		'maxNgrams' => 9000,
		'maxReturnedLanguages' => 1,
		'resultsRatio' => 1.06,
		'minInputLength' => 3,
		'maxProportion' => 0.85,
		'langBoostScore' => 0.14,
		'numBoostedLangs' => 2,
	],
],

// Set the scoring method
'wgCirrusSearchCompletionDefaultScore' => [
	'default' => 'popqual'
],

// Aliases for filetype: search
'wgCirrusSearchFiletypeAliases' => [
	'default' => [
		"pdf" => "office",
		"ppt" => "office",
		"doc" => "office",
		"jpg" => "bitmap",
		"image" => "bitmap",
		"webp" => "bitmap",
		"mp3" => "audio",
		"svg" => "drawing"
	]
],

// Enable the new layout, FIXME: remove the old one
'wgCirrusSearchNewCrossProjectPage' => [
	'default' => true
],

// Display X results per crossproject
'wgCirrusSearchNumCrossProjectSearchResults' => [
	'default' => 1
],

// Load other project config via cirrus dump config API
'wgCirrusSearchFetchConfigFromApi' => [
	'default' => true
],

'wgCirrusSearchRescoreProfiles' => [
	'default' => []
],

// Set SPARQL endpoint for categories
'wgCirrusSearchCategoryEndpoint' => [
	'default' => 'https://query.wikidata.org/bigdata/namespace/categories/sparql'
],

// Our cluster often has issues completing master actions
// within the default 30s timeout.
'wgCirrusSearchMasterTimeout' => [
	'default' => '5m'
],

// Limit archive indices to our internal search clusters (and not cloudelastic)
'wgCirrusSearchPrivateClusters' => [
	'default' => [ 'eqiad', 'codfw' ]
],

# Lower the timeouts - the defaults are too high and allow to scan too many
# pages. 40s shard timeout for regex allowed to deep scan 9million pages for
# insource:/the/ on commons. Keep client timeout relatively high in comparaison,
# but not higher than 60sec as it's the max time allowed for GET requests.
# we really don't want to timeout the client before the shard retrieval (we may
# release the poolcounter before the end of the query on the backend)
'wgCirrusSearchSearchShardTimeout' => [
	'default' => [
		"comp_suggest" => "5s",
		"prefix" => "5s",
		"default" => "10s",
		"regex" => "15s",
	],
],

'wgCirrusSearchClientSideSearchTimeout' => [
	'default' => [
		"comp_suggest" => 10,
		"prefix" => 10,
		// GET requests timeout at 60s, give some room to treat request timeout (T216860)
		"default" => 40,
		"regex" => 50,
	],
],

'wgCirrusSearchCrossClusterSearch' => [
	'default' => true
],

'wgCirrusSearchSanityCheck' => [
	'default' => true
],

'wgCirrusSearchConnectionAttempts' => [
	'default' => 3
],

'wgCirrusSearchPoolCounterKey' => [
	'default' => '_elasticsearch',
	'enwiki' => '_elasticsearch_enwiki'
],

// cache morelike queries to ObjectCache for 24 hours
'wgCirrusSearchMoreLikeThisTTL' => [
	'default' => 86400
],

// Internal WDQS endpoint
'wgCirrusSearchCategoryEndpoint' => [
	'default' => 'http://localhost:6009/bigdata/namespace/categories/sparql'
],

'wgCirrusSearchCompletionBannedPageIds' => [
	'default' => [],
	'enwiktionary' => [ 1821 ],
],

'wgCirrusSearchDocumentSizeLimiterProfile' => [
	'default' => 'wmf_capped',
],

'wgCirrusSearchAutomationHeaderRegexes' => [
	'default' => [
		'x-public-cloud' => '/.*/',
	],
],

'wgCirrusSearchEnableIncomingLinkCounting' => [
	'default' => false,
],

'wgCirrusSearchDeduplicateInQuery' => [
	'default' => false,
],

'wgCirrusSearchDeduplicateInMemory' => [
	'default' => false,
],
'wgCirrusSearchUseEventBusBridge' => [
	'default' => false,
	// T352335: Initial cirrus updater deployment
	'commonswiki' => true,
	'frwiki' => true,
	'itwiki' => true,
	'testwiki' => true,
	'wikidatawiki' => true,
	// T351503: 2nd batch, expected additional topic size per broker: ~11GB, see spreadsheet
	'dewiki' => true,
	'frwiktionary' => true,
	'kuwiktionary' => true,
	// T351503: 3rd batch, expected additional topic size per broker: ~10GB, see spreadsheet
	'arwiki' => true,
	'eswiki' => true,
	'trwiki' => true,
	'ruwiktionary' => true,
	'bewiki' => true,
	'plwiki' => true,
	'zhwiki' => true,
	'ruwiki' => true,
	'cawiki' => true,
	'metawiki' => true,
	'viwiki' => true,
	'nlwiki' => true,
	'huwiki' => true,
	'thwiktionary' => true,
	'ruwikinews' => true,
	'shwiki' => true,
	'fawiki' => true,
	'jawiki' => true,
	'cebwiki' => true,
],
];
