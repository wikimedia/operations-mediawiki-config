<?php

if ( !defined( 'MEDIAWIKI' ) ) exit( 1 );

include_once( "$IP/extensions/PoolCounter/PoolCounterClient.php" );

$wgPoolCountClientConf = array(
	'servers' => array(
		'208.80.152.174'
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
);

