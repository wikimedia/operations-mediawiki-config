<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

# This file holds the CirrusSearch configuration which is specific
# to the 'production' realm.
#
# NOTE: Included for all wikis.
#
# Load tree:
#  |-- wmf-config/CommonSettings.php
#      |
#      `-- wmf-config/CirrusSearch-common.php
#          |
#          `-- wmf-config/CirrusSearch-production.php
#

$wgCirrusSearchClusters = [
	'dnsdisc-chi' => $wmgLocalServices['search-chi-dnsdisc'] + [ 'group' => 'chi', 'replica' => 'dnsdisc' ],
	'eqiad-chi' => $wmgAllServices['eqiad']['search-chi'] + [ 'group' => 'chi', 'replica' => 'eqiad' ],
	'codfw-chi' => $wmgAllServices['codfw']['search-chi'] + [ 'group' => 'chi', 'replica' => 'codfw' ],
	'cloudelastic-chi' => $wmgAllServices['eqiad']['cloudelastic-chi'] + [ 'group' => 'chi', 'replica' => 'cloudelastic' ],

	'dnsdisc-psi' => $wmgLocalServices['search-psi-dnsdisc'] + [ 'group' => 'psi', 'replica' => 'dnsdisc' ],
	'eqiad-psi' => $wmgAllServices['eqiad']['search-psi'] + [ 'group' => 'psi', 'replica' => 'eqiad' ],
	'codfw-psi' => $wmgAllServices['codfw']['search-psi'] + [ 'group' => 'psi', 'replica' => 'codfw' ],
	'cloudelastic-psi' => $wmgAllServices['eqiad']['cloudelastic-psi'] + [ 'group' => 'psi', 'replica' => 'cloudelastic' ],

	'dnsdisc-omega' => $wmgLocalServices['search-omega-dnsdisc'] + [ 'group' => 'omega', 'replica' => 'dnsdisc' ],
	'eqiad-omega' => $wmgAllServices['eqiad']['search-omega'] + [ 'group' => 'omega', 'replica' => 'eqiad' ],
	'codfw-omega' => $wmgAllServices['codfw']['search-omega'] + [ 'group' => 'omega', 'replica' => 'codfw' ],
	'cloudelastic-omega' => $wmgAllServices['eqiad']['cloudelastic-omega'] + [ 'group' => 'omega', 'replica' => 'cloudelastic' ],

	'eqiad-semanticsearch' => $wmgLocalServices['semanticsearch-test'] + [ 'group' => 'semanticsearch-test', 'replica' => 'semanticsearch-test' ],
];

if ( $wmgPrivateWiki ) {
	unset(
		$wgCirrusSearchClusters['cloudelastic-chi'],
		$wgCirrusSearchClusters['cloudelastic-psi'],
		$wgCirrusSearchClusters['cloudelastic-omega']
	);
}

// wgCirrusSearchExtraIndexes is set in CirrusSearch-common.php
if ( isset( $wgCirrusSearchExtraIndexes[NS_FILE] ) ) {
	$wgCirrusSearchExtraIndexes[NS_FILE] = [ 'chi:commonswiki_file' ];
}

// The default configuration is a single-cluster configuration, expand
// that here into the necessary multi-cluster config
$wgCirrusSearchShardCount = [
	'eqiad' => $wmgCirrusSearchShardCount,
	'codfw' => $wmgCirrusSearchShardCount,
	'cloudelastic' => $wmgCirrusSearchShardCount,
];

// CirrusSearch internal user allowed to bypass cirrusbuilddoc PoolCounter protection (T401220)
// (This user is declared in the private settings)
$wgCirrusSearchStreamingUpdaterUsername = 'CirrusSearch Streaming Updater';

$wgCirrusSearchDefaultSemanticProfile = 'default_semantic';
$wgCirrusSearchFullTextQueryBuilderProfiles['default_semantic'] = [
	'builder_class' => \CirrusSearch\Query\SemanticSearchQueryBuilder::class,
	// prevent from being selected from api parameters like srqdprofile
	'undocumented' => true,
	'settings' => [
		'nested_field' => 'passage_chunk_embedding',
		'vector_field' => 'knn',
		'source_fields' => [ 'section', 'text' ],
		'anchor_field' => 'section',
		'snippet_field' => 'text',
		'k' => 30,
		'score_mode' => 'max',
	],
];

$wgCirrusSearchPoolCounterRules = [
	// T442824: search requests federated by WDQS (mwapi). First, so that
	// their regex and deepcat queries also count against this pool.
	[
		'pool' => 'CirrusSearch-WDQS',
		'headers' => [
			'user-agent' => '/^Wikidata Query Service/',
			// Reserve this pool for WMF owned WDQS setups.
			// This works because WDQS does go through the edge
			// (via the web-proxy) to hit the search endpoint.
			'x-trusted-request' => '/^A$/'
		]
	],
	// Regex and deepcat are expensive, even for automated requests.
	[ 'pool' => 'CirrusSearch-ExpensiveFullText', 'syntax' => [ 'deepcategory', 'regex' ] ],
	// T405482: deep web scraping, web requests without cookies.
	[
		'pool' => 'CirrusSearch-Automated',
		'entry_point' => [ 'index' ],
		'missing_headers' => [ 'cookie' ],
		'headers' => [
			// E & F includes most logged-out traffic
			// D is for bots that identify with a compliant UA and are thus rate-limited
			'x-trusted-request' => '/^E|F$/'
		],
		'min_depth' => 100,
	],
	// Public cloud clients and suspected resi proxy, replaces wgCirrusSearchAutomationHeaderRegexes.
	// risky if likely_resiproxy has many false positives
	[
		'pool' => 'CirrusSearch-Automated',
		'headers' => [
			'x-provenance' => '(?i)(^|;)(cloud=|datacenter=true)/',
			// E & F includes most logged-out traffic
			// D is for bots that identify with a compliant UA and are thus rate-limited
			'x-trusted-request' => '/^E|F$/'
		]
	],
	// Deep pagination is expensive.
	[ 'pool' => 'CirrusSearch-ExpensiveFullText', 'min_depth' => 1000 ],
	[ 'pool' => 'CirrusSearch-Prefix', 'syntax' => [ 'prefix', 'wikibase_prefix' ] ],
	[ 'pool' => 'CirrusSearch-MoreLike', 'syntax' => [ 'more_like' ] ],
	[ 'pool' => 'CirrusSearch-Semantic', 'syntax' => [ 'semantic' ] ],
	// No match: CirrusSearch-Search.
];
