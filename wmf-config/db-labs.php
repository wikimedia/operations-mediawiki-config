<?php
# WARNING: This file is publically viewable on the web.
#          Do not put private data here.

if( $wmfRealm == 'labs' ) { # safe guard
	# Database configuration files for the beta labs

	$wgDBTableOptions = "ENGINE=InnoDB, DEFAULT CHARSET=binary";

	$wgLBFactoryConf = array(

		#Requires 'sectionsByDB', 'sectionLoads', 'serverTemplate'

		'class' => 'LBFactoryMulti',

		# Everyone to DEFAULT
		'sectionsByDB' => array(),

		# eqiad has a single server
		'sectionLoads' => array(
			'DEFAULT' => array(
				'db1'     => 0,
			),
		),

		'serverTemplate' => array(
			'dbname'	  => $wgDBname,
			'user'		  => $wgDBuser,
			'password'	  => $wgDBpassword,
			'type'		  => 'mysql',
			'flags'		  => DBO_DEFAULT,
			'max lag'	  => 30,
		),

		'hostsByName' => array(
			'db1'  => '10.68.16.193',   # deployment-db1.eqiad.wmflabs
		),
	);

	# No parser cache in beta yet
	$wmgParserCacheDBs = array();

} # end safe guard
