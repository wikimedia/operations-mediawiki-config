<?php

if ( !defined( 'MEDIAWIKI' ) ) exit( 1 );

include( "$IP/extensions/PoolCounter/PoolCounterClient.php" );

$wgPoolCountClientConf = array(
	'servers' => array(
		'10.0.0.21',
		'10.0.0.22'
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
		'maxqueue' => 400
	),
);

