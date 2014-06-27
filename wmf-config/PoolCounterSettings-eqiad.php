<?php

if ( !defined( 'MEDIAWIKI' ) ) exit( 1 );

include( "$IP/extensions/PoolCounter/PoolCounterClient.php" );

$wgPoolCountClientConf = array(
	'servers' => array(
		'10.64.0.179',
		'10.64.16.152'
	),
	'timeout' => 0.5
);

$wgPoolCounterConf = array(
	'ArticleView' => array(
		'class' => 'PoolCounter_Client',
		'timeout' => 15,
		'workers' => 2,
		'maxqueue' => 100
	),
	'LuceneSearchRequest' => array(
		'class' => 'PoolCounter_Client',
		'timeout' => 15,
		'workers' => 78,
		'maxqueue' => 600
	),
	'CirrusSearch-Search' => array(
		'class' => 'PoolCounter_Client',
		'timeout' => 15,
		'workers' => 432,
		'maxqueue' => 600,
	),
	// Regex searches are much heavier then regular searches so we limit the
	// concurrent number.
	'CirrusSearch-Regex' => array(
		'class' => 'PoolCounter_Client',
		'timeout' => 60,
		'workers' => 2,
		'maxqueue' => 20,
	),
	'FileRender' => array(
		'class' => 'PoolCounter_Client',
		'timeout' => 8,
		'workers' => 2,
		'maxqueue' => 100
	),
	'FileRenderExpensive' => array(
		'class' => 'PoolCounter_Client',
		'timeout' => 8,
		'workers' => 2,
		'slots' => 8,
		'maxqueue' => 100
	),
);
