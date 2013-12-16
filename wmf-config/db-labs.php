<?php
# WARNING: This file is publically viewable on the web.
#          Do not put private data here.

if( $wmgRealm == 'labs' ) { # safe guard
	# Database configuration files for the beta labs

	$wgDBTableOptions   = "ENGINE=InnoDB, DEFAULT CHARSET=binary";

	$wgLBFactoryConf = array(

		#Requires 'sectionsByDB', 'sectionLoads', 'serverTemplate'

		'class' => 'LBFactory_Multi',

		'sectionsByDB' => array(
			'enwikivoyage' => 's2',
			'dewikivoyage' => 's2',
			'itwikivoyage' => 's2',
			'svwikivoyage' => 's2',
			'frwikivoyage' => 's2',
			'nlwikivoyage' => 's2',
			'ruwikivoyage' => 's2',

			'wikidatawiki' => 's2',
		),

		'sectionLoads' => array(
			'DEFAULT' => array(
				'db1'     => 0,
			),
			's2' => array(
				'db2'	  => 0,
			)
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
			'db1'  => '10.4.0.53',   # deployment-sql
			'db2'  => '10.4.0.248',  # deployment-sql02
		),
	);

	# No parser cache in beta yet
	$wmgParserCacheDBs = array();

} # end safe guard
