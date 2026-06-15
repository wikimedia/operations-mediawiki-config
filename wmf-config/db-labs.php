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
				'deployment-db11' => 0,
				'deployment-db15' => 400,
				'deployment-db16' => 400,
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
			// deployment-db11.deployment-prep.eqiad1.wikimedia.cloud, master
			'deployment-db11' => '172.16.5.150:3306',
			// deployment-db15.deployment-prep.eqiad1.wikimedia.cloud
			'deployment-db15' => '172.16.20.82:3306',
			// deployment-db16.deployment-prep.eqiad1.wikimedia.cloud
			'deployment-db16' => '172.16.16.61:3306',
		],

		'externalLoads' => [
			'cluster1' => [
				// deployment-db11.deployment-prep.eqiad1.wikimedia.cloud, master
				'172.16.5.150:3306' => 1,
				// deployment-db15.deployment-prep.eqiad1.wikimedia.cloud
				'172.16.20.82:3306' => 3,
				// deployment-db16.deployment-prep.eqiad1.wikimedia.cloud
				'172.16.16.61:3306' => 3,
			],

			'flow_cluster1' => [
				// deployment-db11.deployment-prep.eqiad1.wikimedia.cloud, master
				'172.16.5.150:3306' => 1,
				// deployment-db15.deployment-prep.eqiad1.wikimedia.cloud
				'172.16.20.82:3306' => 3,
				// deployment-db16.deployment-prep.eqiad1.wikimedia.cloud
				'172.16.16.61:3306' => 3,
			],

			'extension1' => [
				// deployment-db11.deployment-prep.eqiad1.wikimedia.cloud, master
				'172.16.5.150:3306' => 1,
				// deployment-db15.deployment-prep.eqiad1.wikimedia.cloud
				'172.16.20.82:3306' => 3,
				// deployment-db16.deployment-prep.eqiad1.wikimedia.cloud
				'172.16.16.61:3306' => 3,
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
// end safe guard
}
