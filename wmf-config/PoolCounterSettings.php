<?php

if ( !defined( 'MEDIAWIKI' ) ) {
	exit( 1 );
}

wfLoadExtension( 'PoolCounter' );

$wgPoolCounterConf = [
	'ArticleView' => [
		'class' => 'PoolCounter_Client',
		'timeout' => 15,
		'workers' => 2,
		'maxqueue' => 100
	],
	'CirrusSearch-Search' => [
		'class' => 'PoolCounter_Client',
		'timeout' => 15,
		'workers' => 200,
		'maxqueue' => 600,
	],
	// Super common and mostly fast
	'CirrusSearch-Prefix' => [
		'class' => 'PoolCounter_Client',
		'timeout' => 15,
		'workers' => 32,
		'maxqueue' => 60,
	],
	// Super common and mostly fast, replaces Prefix (eventually)
	'CirrusSearch-Completion' => [
		'class' => 'PoolCounter_Client',
		'timeout' => 15,
		'workers' => 432,
		'maxqueue' => 600,
	],
	// Regex searches are much heavier then regular searches so we limit the
	// concurrent number.
	'CirrusSearch-Regex' => [
		'class' => 'PoolCounter_Client',
		'timeout' => 60,
		'workers' => 10,
		'maxqueue' => 20,
	],
	// These should be very very fast and reasonably rare
	'CirrusSearch-NamespaceLookup' => [
		'class' => 'PoolCounter_Client',
		'timeout' => 5,
		'workers' => 50,
		'maxqueue' => 200,
	],
	// These are very expensive and incredibly common at more than 5M per hour
	// before varnish caching. If the somehow the cache hit rate drops this
	// protects the cluster
	'CirrusSearch-MoreLike' => [
		'class' => 'PoolCounter_Client',
		'timeout' => 5,
		'workers' => 50,
		'maxqueue' => 200,
	],
	'FileRender' => [
		'class' => 'PoolCounter_Client',
		'timeout' => 8,
		'workers' => 2,
		'maxqueue' => 100
	],
	'FileRenderExpensive' => [
		'class' => 'PoolCounter_Client',
		'timeout' => 8,
		'workers' => 2,
		'slots' => 8,
		'maxqueue' => 100
	],
	'TranslateFetchTranslators' => [
		'class' => 'PoolCounter_Client',
		'timeout' => 8,
		'workers' => 1,
		'slots' => 16,
		'maxqueue' => 20,
	],
];

$wgPoolCountClientConf = [
	'servers' => $wmfLocalServices['poolcounter'],
	'timeout' => 0.5
];

# Enable connect_timeout for testwiki
if ( $wgDBname == 'testwiki' || $wmgDatacenter == 'codfw' || $wmgRealm == 'labs' ) {
	$wgPoolCountClientConf['connect_timeout'] = 0.01;
}
