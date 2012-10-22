<?php
# WARNING: This file is publically viewable on the web.
#          Do not put private data here.

if( $cluster == 'wmflabs' ) { # safe guard
	# Database configuration files for the beta labs

	$wgLBFactoryConf = array(

		#Requires 'sectionsByDB', 'sectionLoads', 'serverTemplate'

		'class' => 'LBFactory_Multi',

		'sectionsByDB' => array(
			'enwikivoyage' => 's2',
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
			'db1'  => '10.4.0.53',
			'db2'  => '10.4.0.248'
		),
	);

} # end safe guard
