<?php
# WARNING: This file is publically viewable on the web. Do not put private data here.
#
# $Id$

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
	/* s3 */ 'DEFAULT' => array(
		'db39'    => 0,
		'db34'	  => 400,
		'db25'	  => 100, # snapshot host
		'db11'	  => 400,
	),
	's2' => array(
		'db13'	  => 0,
		'db30'	  => 200,
		'db24'	  => 100, # Snapshot host
		'db54'	  => 300,
	),
	's7' => array(
		'db37'  => 0,
		'db16'	=> 500,
		'db18'  => 500,  # 20110730 - is racking up ECC errors
		'db26'	=> 300, # Snapshot hsot
	),
	's4' => array(
		'db22'	 => 0,
		'db31'	 => 400,
		'db33'	 => 100, # Snapshot host
		'db51'	 => 400,
	),
	's5' => array(
		'db45'	 => 0,
		'db35'	 => 500,
		'db44'	 => 500, # snapshot host
		'db55'	 => 1000,
	),
	's6' => array(
		'db47'	   => 0,
		'db43'	   => 1000, # hw died 12/18/2011
		'db46'	   => 400, # snapshot host
		'db50'	   => 1000,
	),
	's1' => array(
		'db36'	  => 0,
		'db12'	  => 50, # special: watchlist, etc see groupLoadsByDB hardy - rebuild me
		'db32'	  => 50, # snapshot host
		'db38'	  => 400, # mysql hung, depooled, repooled with lower load. 20110326 -- mark
		'db52'	  => 400,
		'db53'    => 400,
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
			'db12' => 1,
		),
		'recentchangeslinked' => array(
			'db12' => 1,
		),
		'contributions' => array(
			'db12' => 1,
		),
		'dump' => array(
			'db12' => 1,
		),
	),
),

# Hosts settings
# Do not remove servers from this list ever
# Removing a server from this list does not remove the server from rotation,
# it just breaks the site horribly.
'hostsByName' => array(
	'thistle'  => '10.0.0.232', # do not remove or comment out
	'db4'	   => '10.0.0.237', # do not remove or comment out
	'db5'	   => '10.0.0.238', # do not remove or comment out
	'db7'	   => '10.0.0.240', # do not remove or comment out
	'db8'	   => '10.0.0.241', # do not remove or comment out
	'db11'	   => '10.0.6.21', # do not remove or comment out
	'db12'	   => '10.0.6.22', # do not remove or comment out
	'db13'	   => '10.0.6.23', # do not remove or comment out
	'db14'	   => '10.0.6.24', # do not remove or comment out
	'db15'	   => '10.0.6.25', # do not remove or comment out
	'db16'	   => '10.0.6.26', # do not remove or comment out
	'db17'	   => '10.0.6.27', # do not remove or comment out
	'db18'	   => '10.0.6.28', # do not remove or comment out
	'db19'	   => '10.0.6.29', # do not remove or comment out
	'db20'	   => '10.0.6.30', # do not remove or comment out
	'db21'	   => '10.0.6.31', # do not remove or comment out
	'db22'	   => '10.0.6.32', # do not remove or comment out
	'db23'	   => '10.0.6.33', # do not remove or comment out
	'db24'	   => '10.0.6.34', # do not remove or comment out
	'db25'	   => '10.0.6.35', # do not remove or comment out
	'db26'	   => '10.0.6.36', # do not remove or comment out
	'db27'	   => '10.0.6.37', # do not remove or comment out
	'db28'	   => '10.0.6.38', # do not remove or comment out
	'db29'	   => '10.0.6.39', # do not remove or comment out
	'db30'	   => '10.0.6.40', # do not remove or comment out
	'db31'	   => '10.0.6.41', # do not remove or comment out
	'db32'	   => '10.0.6.42', # do not remove or comment out
	'db33'	   => '10.0.6.43', # do not remove or comment out
	'db34'	   => '10.0.6.44', # do not remove or comment out
	'db35'	   => '10.0.6.45', # do not remove or comment out
	'db36'	   => '10.0.6.46', # do not remove or comment out
	'db37'	   => '10.0.6.47', # do not remove or comment out
	'db38'	   => '10.0.6.48', # do not remove or comment out
	'db39'	   => '10.0.6.49', # do not remove or comment out
	'db40'	   => '10.0.6.50', # do not remove or comment out # Parser cache
	'db42'	   => '10.0.6.52', # do not remove or comment out # Analytics - NOT FOR PROD
	'db43'	   => '10.0.6.53', # do not remove or comment out
	'db44'	   => '10.0.6.54', # do not remove or comment out
	'db45'	   => '10.0.6.55', # do not remove or comment out
	'db46'	   => '10.0.6.56', # do not remove or comment out
	'db47'	   => '10.0.6.57', # do not remove or comment out
	'db50'	   => '10.0.6.60', # do not remove or comment out
	'db51'	   => '10.0.6.61', # do not remove or comment out
	'db52'	   => '10.0.6.62', # do not remove or comment out
	'db53'	   => '10.0.6.63', # do not remove or comment out
	'db54'	   => '10.0.6.64', # do not remove or comment out
	'db55'	   => '10.0.6.65', # do not remove or comment out
	'db1001'	=> '10.64.0.5', # do not remove or comment out
	'db1002'	=> '10.64.0.6', # do not remove or comment out
	'db1003'	=> '10.64.0.7', # do not remove or comment out
	'db1004'	=> '10.64.0.8', # do not remove or comment out
	'db1005'	=> '10.64.0.9', # do not remove or comment out
	'db1006'	=> '10.64.0.10', # do not remove or comment out
	'db1007'	=> '10.64.0.11', # do not remove or comment out
	'db1017'	=> '10.64.16.6', # do not remove or comment out
	'db1018'	=> '10.64.16.7', # do not remove or comment out
	'db1019'	=> '10.64.16.8', # do not remove or comment out
	'db1020'	=> '10.64.16.9', # do not remove or comment out
	'db1021'	=> '10.64.16.10', # do not remove or comment out
	'db1022'	=> '10.64.16.11', # do not remove or comment out
	'db1024'	=> '10.64.16.13', # do not remove or comment out
	'db1033'	=> '10.64.16.22', # do not remove or comment out
	'db1034'	=> '10.64.16.23', # do not remove or comment out
	'db1035'	=> '10.64.16.24', # do not remove or comment out
	'db1038'	=> '10.64.16.27', # do not remove or comment out
	'db1039'	=> '10.64.16.28', # do not remove or comment out
	'db1040'	=> '10.64.16.29', # do not remove or comment out
	'db1041'	=> '10.64.16.30', # do not remove or comment out
	'db1047'	=> '10.64.16.36', # do not remove or comment out
),

'externalLoads' => array(
	# Recompressed stores
	'rc1' => array(
		# '10.0.0.225' => 1, #es1
		'10.0.0.226' => 1, # es2
		'10.0.0.227' => 1, # es3
		'10.0.0.228' => 1, # es4
	),

	# Ubuntu dual-purpose stores
	'cluster3' => array(
		# '10.0.0.225' => 1, #es1
		'10.0.0.226' => 1, # es2
		'10.0.0.227' => 1, # es3
		'10.0.0.228' => 1, # es4
	),
	'cluster4' => array(
		# '10.0.0.225' => 1, #es1
		'10.0.0.226' => 1, # es2
		'10.0.0.227' => 1, # es3
		'10.0.0.228' => 1, # es4
	),
	'cluster5' => array(
		# '10.0.0.225' => 1, #es1
		'10.0.0.226' => 1, # es2
		'10.0.0.227' => 1, # es3
		'10.0.0.228' => 1, # es4
	),
	'cluster6' => array(
		# '10.0.0.225' => 1, #es1
		'10.0.0.226' => 1, # es2
		'10.0.0.227' => 1, # es3
		'10.0.0.228' => 1, # es4
	),
	'cluster7' => array(
		# '10.0.0.225' => 1, #es1
		'10.0.0.226' => 1, # es2
		'10.0.0.227' => 1, # es3
		'10.0.0.228' => 1, # es4
	),
	'cluster8' => array(
		# '10.0.0.225' => 1, #es1
		'10.0.0.226' => 1, # es2
		'10.0.0.227' => 1, # es3
		'10.0.0.228' => 1, # es4
	),
	'cluster9' => array(
		# '10.0.0.225' => 1, #es1
		'10.0.0.226' => 1, # es2
		'10.0.0.227' => 1, # es3
		'10.0.0.228' => 1, # es4
	),
	'cluster10' => array(
		# '10.0.0.225' => 1, #es1
		'10.0.0.226' => 1, # es2
		'10.0.0.227' => 1, # es3
		'10.0.0.228' => 1, # es4
	),

	'cluster20' => array(
		# '10.0.0.225' => 1, #es1
		'10.0.0.226' => 1, # es2
		'10.0.0.227' => 1, # es3
		'10.0.0.228' => 1, # es4
	),
	'cluster21' => array(
		# '10.0.0.225' => 1, #es1
		'10.0.0.226' => 1, # es2
		'10.0.0.227' => 1, # es3
		'10.0.0.228' => 1, # es4
	),

	# Dedicated server stores
	'cluster22' => array(
		'10.0.0.227' => 1, # es3
		# '10.0.0.248' => 1, # ms3
		# '10.0.0.249' => 3, # ms2
		# '10.0.0.250' => 3, # ms1
		# '10.0.0.225' => 3, #es1
		'10.0.0.226' => 3, # es2
		'10.0.0.228' => 3, # es4
	),

	# Clusters required for bug 22624
	'cluster1' => array(
		# '10.0.0.225' => 1, #es1
		'10.0.0.226' => 1, # es2
		'10.0.0.227' => 1, # es3
		'10.0.0.228' => 1, # es4
	),
	'cluster2' => array(
		# '10.0.0.225' => 1, #es1
		'10.0.0.226' => 1, # es2
		'10.0.0.227' => 1, # es3
		'10.0.0.228' => 1, # es4
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
),

# This key must exist for the master switch script to work
'readOnlyBySection' => array(
# 'DEFAULT' => 'Emergency maintenance in progress',
#	's1'	   => 'Maintenance in progress, please try again in 5 minutes',
#	's4'	   => 'Brief Database Maintenance in progress, please try again in 3 minutes',
),

);

$wgDefaultExternalStore = array(
	'DB://cluster22',
);
$wgMasterWaitTimeout = 2;
$wgDBAvgStatusPoll = 30000;

# $wgLBFactoryConf['readOnlyBySection']['s2'] =
# $wgLBFactoryConf['readOnlyBySection']['s2a'] =
# 'Emergency maintenance, need more servers up, new estimate ~18:30 UTC';

if ( $wgDBname === 'testwiki' ) {
	$wgLBFactoryConf['serverTemplate']['max threads'] = 300;
}
