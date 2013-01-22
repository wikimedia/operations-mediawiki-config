<?php
# WARNING: This file is publically viewable on the web. Do not put private data here.
#
# $Id$

if ( !defined( 'DBO_DEFAULT' ) ) {
	define( 'DBO_DEFAULT', 16 );
}

$wgLBFactoryConf = array(

'class' => 'LBFactory_Multi',

'sectionsByDB' => array(
	'enwiki' => 's1',

	# New master
	'bgwiki' => 's2',
	'bgwiktionary' => 's2',
	'cswiki' => 's2',
	'enwikiquote' => 's2',
	'enwiktionary' => 's2',
	'eowiki' => 's2',
	'fiwiki' => 's2',
	'idwiki' => 's2',
	'itwiki' => 's2',
	'nlwiki' => 's2',
	'nowiki' => 's2',
	'plwiki' => 's2',
	'ptwiki' => 's2',
	'svwiki' => 's2',
	'thwiki' => 's2',
	'trwiki' => 's2',
	'zhwiki' => 's2',

	'commonswiki' => 's4',

	'dewiki' => 's5',

	'frwiki' => 's6',
	'jawiki' => 's6',
	'ruwiki' => 's6',

	'eswiki' => 's7',
	'huwiki' => 's7',
	'hewiki' => 's7',
	'ukwiki' => 's7',
	'frwiktionary' => 's7',
	'metawiki' => 's7',
	'arwiki' => 's7',
	'centralauth' => 's7',
	'cawiki' => 's7',
	'viwiki' => 's7',
	'fawiki' => 's7',
	'rowiki' => 's7',
	'kowiki' => 's7',
),

# Load lists
#
# All servers which replicate the given databases should be in the load
# list, not commented out, because otherwise maintenance scripts such
# as compressOld.php won't wait for those servers when they lag.
#
# Conversely, all servers which are down or do not replicate should be
# removed, not set to load zero, because there are certain situations
# when load zero servers will be used, such as if the others are lagged.
# Servers which are down should be removed to avoid a timeout overhead
# per invocation.
#
'sectionLoads' => array(
	's1' => array(
		'db1017'    => 0, # 1.4TB
		'db1001'    => 400, # 1.4TB sas
		'db1042'    => 400, # 1.4TB sas
		'db1049'    => 500, # 2.8TB sas
		'db1050'    => 0, # snapshot 2.8TB sas
		'db1043'    => 100, # 1.4TB sas, special contrib, watchlist, etc
	),
	's2' => array(
		'db1034'    => 0,
		'db1002'    => 400,
		'db1009'    => 400,
		'db1018'    => 100, # snaphsot
	),
	/* s3 */ 'DEFAULT' => array(
		'db1019'    => 0,
		'db1003'    => 400,
		'db1010'    => 400,
		'db1035'    => 100, # snapshot
	),
	's4' => array(
		'db1038'   => 0,
		'db1004'   => 400,
		'db1011'   => 400,
		'db1020'   => 100, # snapshot
	),
	's5' => array(
		'db1039'   => 0,
		'db1005'   => 100, # snapshot
		'db1026'   => 400,
		'db1021'   => 400,
	),
	's6' => array(
		'db1006'   => 0,
		'db1022'   => 100, # snapshot
		'db1027'   => 400,
		'db1040'   => 400,
	),
	's7' => array(
		'db1041' => 0,
		'db1007' => 100, # snapshot
		'db1024' => 400,
		'db1028' => 400,
	),
),


'serverTemplate' => array(
	'dbname'	  => $wgDBname,
	'user'		  => $wgDBuser,
	'password'	  => $wgDBpassword,
	'type'		  => 'mysql',
	'flags'		  => DBO_DEFAULT,
	'max lag'	  => 30,
	# 'max threads' => 350, -- disabled TS
),

'groupLoadsBySection' => array(
	/*
	's2' => array(
		'QueryPage::recache' => array(
			'db8' => 100,
		)
	)*/
),


'groupLoadsByDB' => array(
	'enwiki' => array(
		'watchlist' => array(
			'db1043' => 1,
		),
		'recentchangeslinked' => array(
			'db1043' => 1,
		),
		'contributions' => array(
			'db1043' => 1,
		),
		'dump' => array(
			'db1043' => 1,
		),
	),
),

# Hosts settings
# Do not remove servers from this list ever
# Removing a server from this list does not remove the server from rotation,
# it just breaks the site horribly.
'hostsByName' => array(
	'db1001' => '10.64.0.5', #do not remove or comment out
	'db1002' => '10.64.0.6', #do not remove or comment out
	'db1003' => '10.64.0.7', #do not remove or comment out
	'db1004' => '10.64.0.8', #do not remove or comment out
	'db1005' => '10.64.0.9', #do not remove or comment out
	'db1006' => '10.64.0.10', #do not remove or comment out
	'db1007' => '10.64.0.11', #do not remove or comment out
	'db1009' => '10.64.0.13', #do not remove or comment out
	'db1010' => '10.64.0.14', #do not remove or comment out
	'db1011' => '10.64.0.15', #do not remove or comment out
	'db1017' => '10.64.16.6', #do not remove or comment out
	'db1018' => '10.64.16.7', #do not remove or comment out
	'db1019' => '10.64.16.8', #do not remove or comment out
	'db1020' => '10.64.16.9', #do not remove or comment out
	'db1021' => '10.64.16.10', #do not remove or comment out
	'db1022' => '10.64.16.11', #do not remove or comment out
	'db1024' => '10.64.16.13', #do not remove or comment out
	'db1026' => '10.64.16.15', #do not remove or comment out
	'db1027' => '10.64.16.16', #do not remove or comment out
	'db1028' => '10.64.16.17', #do not remove or comment out
	'db1034' => '10.64.16.23', #do not remove or comment out
	'db1035' => '10.64.16.24', #do not remove or comment out
	'db1038' => '10.64.16.27', #do not remove or comment out
	'db1039' => '10.64.16.28', #do not remove or comment out
	'db1040' => '10.64.16.29', #do not remove or comment out
	'db1041' => '10.64.16.30', #do not remove or comment out
	'db1042' => '10.64.16.31', #do not remove or comment out
	'db1043' => '10.64.16.32', #do not remove or comment out
	'db1047' => '10.64.16.36', #do not remove or comment out
	'db1049' => '10.64.16.144', #do not remove or comment out
	'db1050' => '10.64.16.145', #do not remove or comment out
),

'externalLoads' => array(
	# Recompressed stores
	'rc1' => array(
		'10.64.0.25' => 1, # es1001
		'10.64.16.40' => 1, # es1002
		'10.64.16.41' => 1, # es1003
		'10.64.16.42' => 1, # es1004
	),

	# Ubuntu dual-purpose stores
	'cluster3' => array(
		'10.64.0.25' => 1, # es1001
		'10.64.16.40' => 1, # es1002
		'10.64.16.41' => 1, # es1003
		'10.64.16.42' => 1, # es1004
	),
	'cluster4' => array(
		'10.64.0.25' => 1, # es1001
		'10.64.16.40' => 1, # es1002
		'10.64.16.41' => 1, # es1003
		'10.64.16.42' => 1, # es1004
	),
	'cluster5' => array(
		'10.64.0.25' => 1, # es1001
		'10.64.16.40' => 1, # es1002
		'10.64.16.41' => 1, # es1003
		'10.64.16.42' => 1, # es1004
	),
	'cluster6' => array(
		'10.64.0.25' => 1, # es1001
		'10.64.16.40' => 1, # es1002
		'10.64.16.41' => 1, # es1003
		'10.64.16.42' => 1, # es1004
	),
	'cluster7' => array(
		'10.64.0.25' => 1, # es1001
		'10.64.16.40' => 1, # es1002
		'10.64.16.41' => 1, # es1003
		'10.64.16.42' => 1, # es1004
	),
	'cluster8' => array(
		'10.64.0.25' => 1, # es1001
		'10.64.16.40' => 1, # es1002
		'10.64.16.41' => 1, # es1003
		'10.64.16.42' => 1, # es1004
	),
	'cluster9' => array(
		'10.64.0.25' => 1, # es1001
		'10.64.16.40' => 1, # es1002
		'10.64.16.41' => 1, # es1003
		'10.64.16.42' => 1, # es1004
	),
	'cluster10' => array(
		'10.64.0.25' => 1, # es1001
		'10.64.16.40' => 1, # es1002
		'10.64.16.41' => 1, # es1003
		'10.64.16.42' => 1, # es1004
	),

	'cluster20' => array(
		'10.64.0.25' => 1, # es1001
		'10.64.16.40' => 1, # es1002
		'10.64.16.41' => 1, # es1003
		'10.64.16.42' => 1, # es1004
	),
	'cluster21' => array(
		'10.64.0.25' => 1, # es1001
		'10.64.16.40' => 1, # es1002
		'10.64.16.41' => 1, # es1003
		'10.64.16.42' => 1, # es1004
	),

	# Dedicated server stores
	'cluster22' => array(
		'10.64.0.25' => 3, # es1001
		'10.64.16.40' => 1, # es1002
		'10.64.16.41' => 1, # es1003
		'10.64.16.42' => 1, # es1004
	),

	# Clusters required for bug 22624
	'cluster1' => array(
		'10.64.0.25' => 1, # es1001
		'10.64.16.40' => 1, # es1002
		'10.64.16.41' => 1, # es1003
		'10.64.16.42' => 1, # es1004
	),
	'cluster2' => array(
		'10.64.0.25' => 1, # es1001
		'10.64.16.40' => 1, # es1002
		'10.64.16.41' => 1, # es1003
		'10.64.16.42' => 1, # es1004
	),
	# Dedicated server stores
	# es1
	'cluster23' => array(
		'10.64.0.25' => 3, # es1001
		'10.64.16.40' => 3, # es1002
		'10.64.16.41' => 1, # es1003
		'10.64.16.42' => 3, # es1004
	),
	# es2
	'cluster24' => array(
		'10.64.16.153' => 1, # es1005
		'10.64.16.154' => 3, # es1006
		'10.64.32.17' => 3, # es1007
	),
	# es3
	'cluster25' => array(
		'10.64.32.18' => 1, # es1008
		'10.64.32.19' => 3, # es1009
		'10.64.32.20' => 3, # es1010
	),
),

'masterTemplateOverrides' => array(
	# The master generally has more threads running than the others
	'max threads' => 400,
),

'externalTemplateOverrides' => array(
	'flags' => 0, // No transactions
),

'templateOverridesByCluster' => array(
	'cluster1'	=> array( 'blobs table' => 'blobs_cluster1' ),
	'cluster2'	=> array( 'blobs table' => 'blobs_cluster2' ),
	'cluster3'	=> array( 'blobs table' => 'blobs_cluster3' ),
	'cluster4'	=> array( 'blobs table' => 'blobs_cluster4' ),
	'cluster5'	=> array( 'blobs table' => 'blobs_cluster5' ),
	'cluster6'	=> array( 'blobs table' => 'blobs_cluster6' ),
	'cluster7'	=> array( 'blobs table' => 'blobs_cluster7' ),
	'cluster8'	=> array( 'blobs table' => 'blobs_cluster8' ),
	'cluster9'	=> array( 'blobs table' => 'blobs_cluster9' ),
	'cluster10'	=> array( 'blobs table' => 'blobs_cluster10' ),
	'cluster20'	=> array( 'blobs table' => 'blobs_cluster20' ),
	'cluster21'	=> array( 'blobs table' => 'blobs_cluster21' ),
	'cluster22'	=> array( 'blobs table' => 'blobs_cluster22' ),
	'cluster23'	=> array( 'blobs table' => 'blobs_cluster23' ),
	'cluster24'	=> array( 'blobs table' => 'blobs_cluster24' ),
	'cluster25'	=> array( 'blobs table' => 'blobs_cluster25' ),
),

# This key must exist for the master switch script to work
'readOnlyBySection' => array(
#	'DEFAULT' => 'Brief Database Maintenance in progress, please try again in 3 minutes', #s3
#	's1'	   => 'Brief Database Maintenance in progress, please try again in 3 minutes',
#	's5'	   => 'Brief Database Maintenance in progress, please try again in 3 minutes',
),

);

$wgDefaultExternalStore = array(
	'DB://cluster24', 'DB://cluster25',
);
$wgMasterWaitTimeout = 2;
$wgDBAvgStatusPoll = 30000;

# $wgLBFactoryConf['readOnlyBySection']['s2'] =
# $wgLBFactoryConf['readOnlyBySection']['s2a'] =
# 'Emergency maintenance, need more servers up, new estimate ~18:30 UTC';

if ( $wgDBname === 'testwiki' ) {
	$wgLBFactoryConf['serverTemplate']['max threads'] = 300;
}
