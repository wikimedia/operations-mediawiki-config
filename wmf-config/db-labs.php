<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

// safe guard
if ( $wmgRealm == 'labs' ) {
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
				'deployment-db09' => 400,
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
			'useGTIDs'    => true,
		],

		'hostsByName' => [
			// deployment-db07.deployment-prep.eqiad1.wikimedia.cloud, master
			'deployment-db07' => '172.16.3.206:3306',
			// deployment-db09.deployment-prep.eqiad1.wikimedia.cloud
			'deployment-db09' => '172.16.0.238:3306',
		],

		'externalLoads' => [
			'cluster1' => [
				// deployment-db07.deployment-prep.eqiad1.wikimedia.cloud, master
				'172.16.3.206:3306' => 1 ,
				// deployment-db09.deployment-prep.eqiad1.wikimedia.cloud
				'172.16.0.238:3306'  => 3 ,
			],

			'flow_cluster1' => [
				// deployment-db07.deployment-prep.eqiad1.wikimedia.cloud, master
				'172.16.3.206:3306' => 1 ,
				// deployment-db09.deployment-prep.eqiad1.wikimedia.cloud
				'172.16.0.238:3306'  => 3 ,
			],

			'extension1' => [
				// deployment-db07.deployment-prep.eqiad1.wikimedia.cloud, master
				'172.16.3.206:3306' => 1 ,
				// deployment-db09.deployment-prep.eqiad1.wikimedia.cloud
				'172.16.0.238:3306'  => 3 ,
			],

			'extension2' => [
				// deployment-db07.deployment-prep.eqiad1.wikimedia.cloud, master
				'172.16.3.206:3306' => 1 ,
				// deployment-db09.deployment-prep.eqiad1.wikimedia.cloud
				'172.16.0.238:3306'  => 3 ,
			]
		],

		'templateOverridesByCluster' => [
			'cluster1' => [ 'blobs table' => 'blobs1' ],
			'flow_cluster1' => [ 'blobs table' => 'blobs_flow1' ],
		],
	];

	$wgDefaultExternalStore = [
		'DB://cluster1',
	];

// end safe guard
}
