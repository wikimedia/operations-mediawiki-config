<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

# No parser cache DBs.  Just using memcached
$wmgParserCacheDBs = [];

$wmgMainStashServers = [];

$wgDBTableOptions = "ENGINE=InnoDB, DEFAULT CHARSET=binary";

$wgLBFactoryConf = [

	# Requires 'sectionsByDB', 'sectionLoads', 'serverTemplate'

	'class' => 'LBFactoryMulti',

	# Everyone to DEFAULT
	'sectionsByDB' => [],

	'sectionLoads' => [
		'DEFAULT' => [
			'db' => 0,
		],
	],

	'serverTemplate' => [
		'dbname'	  => $wgDBname,
		'user'		  => $wgDBuser,
		'password'	  => $wgDBpassword,
		'type'		  => 'mysql',
		'flags'		  => DBO_DEFAULT | ( $wgDebugDumpSql ? DBO_DEBUG : 0 ),
		// 5 minutes
		'max lag'	  => 300,
	],

	'hostsByName' => [
		'db' => 'db:3306',
	],

	'externalLoads' => [
		'cluster1' => [
			'db:3306' => 1,
		],

		'flow_cluster1' => [
			'db:3306' => 1,
		],

		'extension1' => [
			'db:3306' => 1,
		],
	],

	'templateOverridesByCluster' => [
		'cluster1' => [ 'blobs table' => 'blobs1' ],
		'flow_cluster1' => [ 'blobs table' => 'blobs_flow1' ],
	],
];

$wgDefaultExternalStore = [
	'DB://cluster1',
];
