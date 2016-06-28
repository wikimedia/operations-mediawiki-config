<?php
# WARNING: This file is publically viewable on the web.
#          Do not put private data here.

if( $wmfRealm == 'labs' ) { # safe guard
	# Database configuration files for the beta labs

	$wgDBTableOptions = "ENGINE=InnoDB, DEFAULT CHARSET=binary";

	$wgLBFactoryConf = [

		#Requires 'sectionsByDB', 'sectionLoads', 'serverTemplate'

		'class' => 'LBFactoryMulti',

		# Everyone to DEFAULT
		'sectionsByDB' => [],

		'sectionLoads' => [
			'DEFAULT' => [
				'deployment-db1'     => 0,
				'deployment-db2'     => 400,
			],
		],

		'serverTemplate' => [
			'dbname'	  => $wgDBname,
			'user'		  => $wgDBuser,
			'password'	  => $wgDBpassword,
			'type'		  => 'mysql',
			'flags'		  => DBO_DEFAULT,
			'max lag'	  => 300, // 5 minutes
		],

		'hostsByName' => [
			'deployment-db1'  => '10.68.16.193', # deployment-db1.eqiad.wmflabs
			'deployment-db2'  => '10.68.17.94', # deployment-db2.eqiad.wmflabs
		],

		'externalLoads' => [
			'cluster1' => [
				'10.68.16.193' => 1, # deployment-db1.eqiad.wmflabs, master
				'10.68.17.94' => 3 , # deployment-db2.eqiad.wmflabs
			],

			'flow_cluster1' => [
				'10.68.16.193' => 1, # deployment-db1.eqiad.wmflabs, master
				'10.68.17.94' => 3 , # deployment-db2.eqiad.wmflabs
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

	# No parser cache in beta yet
	$wmgParserCacheDBs = [];

} # end safe guard
