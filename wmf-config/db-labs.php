<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

if ( $wmgRealm == 'labs' ) { # safe guard
	# Database configuration files for the beta labs

	$wgDBTableOptions = "ENGINE=InnoDB, DEFAULT CHARSET=binary";

	$wgLBFactoryConf = [

		# Requires 'sectionsByDB', 'sectionLoads', 'serverTemplate'

		'class' => 'LBFactoryMulti',

		# Everyone to DEFAULT
		'sectionsByDB' => [],

		'sectionLoads' => [
			'DEFAULT' => [
				'deployment-db07' => 0,
				'deployment-db08' => 400,
			],
		],

		'serverTemplate' => [
			'dbname'	  => $wgDBname,
			'user'		  => $wgDBuser,
			'password'	  => $wgDBpassword,
			'type'		  => 'mysql',
			'flags'		  => DBO_DEFAULT | ( $wgDebugDumpSql ? DBO_DEBUG : 0 ),
			'max lag'	  => 300, // 5 minutes
			'useGTIDs'    => true,
		],

		'hostsByName' => [
			'deployment-db07' => '172.16.3.206:3306', # deployment-db07.deployment-prep.eqiad1.wikimedia.cloud, master
			'deployment-db08' => '172.16.6.39:3306',  # deployment-db08.deployment-prep.eqiad1.wikimedia.cloud
		],

		'externalLoads' => [
			'cluster1' => [
				'172.16.3.206:3306' => 1 , # deployment-db07.deployment-prep.eqiad1.wikimedia.cloud, master
				'172.16.6.39:3306'  => 3 , # deployment-db08.deployment-prep.eqiad1.wikimedia.cloud
			],

			'flow_cluster1' => [
				'172.16.3.206:3306' => 1 , # deployment-db07.deployment-prep.eqiad1.wikimedia.cloud, master
				'172.16.6.39:3306'  => 3 , # deployment-db08.deployment-prep.eqiad1.wikimedia.cloud
			],

			'extension1' => [
				'172.16.3.206:3306' => 1 , # deployment-db07.deployment-prep.eqiad1.wikimedia.cloud, master
				'172.16.6.39:3306'  => 3 , # deployment-db08.deployment-prep.eqiad1.wikimedia.cloud
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

} # end safe guard
