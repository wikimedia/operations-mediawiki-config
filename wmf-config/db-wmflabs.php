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
			'dewikivoyage' => 's2',
			'itwikivoyage' => 's2',
			'svwikivoyage' => 's2',
			'frwikivoyage' => 's2',
			'nlwikivoyage' => 's2',
			'ruwikivoyage' => 's2',
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
			'db1'  => 'deployment-sql',
			'db2'  => 'deployment-sql02',
		),
	);

} # end safe guard
