<?php
# WARNING: This file is publically viewable on the web. Do not put private data here.
#
# $Id$

# Seconday Load lists
#
# This file is not used by MW but documents
# slaves in other environments that need their
# schemas kept in sync.  It can be included to
# override the production db list by upgrade
# scripts.
#
$wgLBFactoryConf['sectionLoads'] = array(
	's1' => array(
		''     => 0,
		'db1001'     => 1, # secondary master -- DEAD
		'db1017'    => 1, # secondary master
		'db1033'      => 1, # snapshot
		'db1043'    => 1,
		'db1047'      => 1, # alaytics
		'db42'      => 1, # analytics
	),
	's2' => array(
        ''  => 0,
		'db1034'    => 1, # secondary master
		'db1002'    => 1,
		'db1018'    => 1, # snaphsot
	),
	's4' => array(
        ''  => 0,
		'db1038'   => 1, # secondary master
		'db1004'   => 1,
		'db1020'   => 1, # snapshot
	),
	's5' => array(
        ''  => 0,
        	'db1039'   => 1, # secondary master
		'db1005'   => 1, # snapshot
        	'db1021'   => 1,
	),
	/* s3 */ 'DEFAULT' => array(
        ''  => 0,
		'db1019'    => 1, # secondary master
		'db1003'    => 1,
		'db1035'    => 1, # snapshot
	),
	's6' => array(
        ''  => 0,
        	'db1006'   => 1, # secondary master
        	'db1022'   => 1, # snapshot
        	'db1040'   => 1,
	),
	's7' => array(
        ''  => 0,
		'db1041' => 1, # secondary master
		'db1007' => 1, # snapshot
		'db1024' => 1,
	),
);

