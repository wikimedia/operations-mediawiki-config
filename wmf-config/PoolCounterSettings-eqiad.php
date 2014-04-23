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
	'TMHTransformFrame' => array(
		'class' => 'PoolCounter_Client',
		'timeout' => 15,
		'workers' => 1,
		'maxqueue' => 100
	),
	'downloadpdf' => array(
		'class' => 'PoolCounter_Client',
		'timeout' => 10,
		'workers' => 2,
		'maxqueue' => 5
	),
	'downloadtiff' => array(
		'class' => 'PoolCounter_Client',
		'timeout' => 10,
		'workers' => 2,
		'maxqueue' => 5
	),
	'GetLocalFileCopy' => array(
		'class' => 'PoolCounter_Client',
		'timeout' => 10,
		'workers' => 2,
		'maxqueue' => 5
	),
	'FileRender' => array(
		'class' => 'PoolCounter_Client',
		'timeout' => 8,
		'workers' => 2,
		'maxqueue' => 100
	),
);
