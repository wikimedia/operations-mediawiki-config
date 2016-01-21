<?php
# WARNING: This file is publically viewable on the web. Do not put private data here.

if ( !defined( 'DBO_DEFAULT' ) ) {
	define( 'DBO_DEFAULT', 16 );
}

#$wgReadOnly = "Wikimedia Sites are currently read-only during maintenance, please try again soon.";

$wmgParserCacheDBs = array(
#	'10.64.16.156',  # pc1001 (down for maintenance)
	'10.64.16.157',  # pc1002
	'10.64.16.158',  # pc1003
);

$wmgOldExtTemplate = array(
	'10.64.0.7'    => 1, # es1012
	'10.64.32.185' => 1, # es1016
	'10.64.48.115' => 1, # es1018
);

$wgLBFactoryConf = array(

'class' => 'LBFactoryMulti',

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
	'wikidatawiki' => 's5',

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

	'labswiki' => 'silver',
	'labtestwiki' => 'labtestweb2001',
),

# Load lists
#
# Masters should be in slot [0].
#
# All servers for which replication lag matters should be in the load
# list, not commented out, because otherwise maintenance scripts such
# as compressOld.php won't wait for those servers when they lag.
#
# Conversely, all servers which are down or do not replicate should be
# removed, not set to load zero, because there are certain situations
# when load zero servers will be used, such as if the others are lagged.
# Servers which are down should be removed to avoid a timeout overhead
# per invocation.

'sectionLoads' => array(
	's1' => array(
		'db1052' => 0,   # 2.8TB  96GB
		'db1053' => 0,   # 2.8TB  96GB, vslow, dump
		'db1051' => 50,  # 2.8TB  96GB, watchlist, recentchanges, contributions, logpager
		'db1055' => 50,  # 2.8TB  96GB, watchlist, recentchanges, contributions, logpager
		'db1057' => 200, # 2.8TB  96GB
		'db1065' => 100, # 2.8TB 160GB, api
		'db1066' => 100, # 2.8TB 160GB, api
		'db1072' => 500, # 2.8TB 160GB
		'db1073' => 500, # 2.8TB 160GB
	),
	's2' => array(
		'db1024' => 0,   # 1.4TB  64GB
		'db1021' => 0,   # 1.4TB  64GB, vslow, dump
		'db1036' => 0,   # 1.4TB  64GB, watchlist, recentchanges, contributions, logpager
		'db1018' => 200, # 1.4TB  64GB
		'db1054' => 200, # 2.8TB  96GB, api
		'db1060' => 200, # 2.8TB  96GB, api
		'db1063' => 400, # 2.8TB 128GB
		'db1067' => 500, # 2.8TB 160GB
	),
	/* s3 */ 'DEFAULT' => array(
		'db1038' => 0,   # 1.4TB  64GB
		'db1027' => 0,   # 1.4TB  64GB, vslow, dump
		'db1015' => 100, # 1.4TB  64GB, watchlist, recentchanges, contributions, logpager
		'db1035' => 500, # 1.4TB  64GB
		'db1044' => 500, # 1.4TB  64GB
	),
	's4' => array(
		'db1040' => 0,   # 1.4TB  64GB
		'db1042' => 0,   # 1.4TB  64GB, vslow, dump
		'db1019' => 0,   # 1.4TB  64GB, watchlist, recentchanges, contributions, logpager
		'db1056' => 100, # 2.8TB  96GB, api
		'db1059' => 100, # 2.8TB  96GB, api
		'db1064' => 500, # 2.8TB 160GB
		'db1068' => 500, # 2.8TB 160GB
	),
	's5' => array(
		'db1058' => 0,   # 2.8TB  96GB
		'db1049' => 0,   # 2.8TB  64GB, vslow, dump
		'db1026' => 0,   # 1.4TB  64GB, watchlist, recentchanges, contributions, logpager
		'db1045' => 50,  # 1.4TB  64GB, api
		'db1070' => 500, # 2.8TB 160GB
		'db1071' => 500, # 2.8TB 160GB
	),
	's6' => array(
		'db1023' => 0,   # 1.4TB  64GB
		'db1022' => 200, # 1.4TB  64GB
		'db1030' => 0,   # 1.4TB  64GB, vslow, dump
		'db1037' => 0,   # 1.4TB  64GB, watchlist, recentchanges, contributions, logpager
		'db1050' => 400, # 2.8TB  64GB
		'db1061' => 500, # 2.8TB 128GB
	),
	's7' => array(
		'db1033' => 0,   # 1.4TB  64GB,
		'db1028' => 0,   # 1.4TB  64GB, vslow, dump
		'db1034' => 0,   # 1.4TB  64GB, watchlist, recentchanges, contributions, logpager
		'db1041' => 200, # 1.4TB  64GB
		'db1039' => 300, # 1.4TB  64GB
		'db1062' => 500, # 2.8TB 128GB
	),
	'silver' => array(
		'silver' => 100,   # I have no idea if this is right
	),
	'labtestweb2001' => array(
		'labtestweb2001' => 100,   # I have no idea if this is right
	),
),

'serverTemplate' => array(
	'dbname'	  => $wgDBname,
	'user'		  => $wgDBuser,
	'password'	  => $wgDBpassword,
	'type'		  => 'mysql',
	'flags'		  => DBO_DEFAULT,
	'max lag'	  => 10, // should be safely less than $wgCdnReboundPurgeDelay
	'variables'   => array(
		'innodb_lock_wait_timeout' => 15
	)
),

'groupLoadsBySection' => array(
	's1' => array(
		'watchlist' => array(
			'db1051' => 1,
			'db1055' => 1,
		),
		'recentchanges' => array(
			'db1051' => 1,
			'db1055' => 1,
		),
		'recentchangeslinked' => array(
			'db1051' => 1,
			'db1055' => 1,
		),
		'contributions' => array(
			'db1051' => 1,
			'db1055' => 1,
		),
		'logpager' => array(
			'db1051' => 1,
			'db1055' => 1,
		),
		'dump' => array(
			'db1053' => 1,
		),
		'vslow' => array(
			'db1053' => 1,
		),
		'api' => array(
			'db1065' => 1,
			'db1066' => 1,
		),
	),
	's2' => array(
		'vslow' => array(
			'db1021' => 1,
		),
		'dump' => array(
			'db1021' => 1,
		),
		'api' => array(
			'db1054' => 1,
			'db1060' => 1,
		),
		'watchlist' => array(
			'db1036' => 1,
		),
		'recentchanges' => array(
			'db1036' => 1,
		),
		'recentchangeslinked' => array(
			'db1036' => 1,
		),
		'contributions' => array(
			'db1036' => 1,
		),
		'logpager' => array(
			'db1036' => 1,
		),
	),
	/* s3 */ 'DEFAULT' => array(
		'vslow' => array(
			'db1027' => 1,
		),
		'dump' => array(
			'db1027' => 1,
		),
		'watchlist' => array(
			'db1015' => 1,
		),
		'recentchanges' => array(
			'db1015' => 1,
		),
		'recentchangeslinked' => array(
			'db1015' => 1,
		),
		'contributions' => array(
			'db1015' => 1,
		),
		'logpager' => array(
			'db1015' => 1,
		),
	),
	's4' => array(
		'vslow' => array(
			'db1042' => 1,
		),
		'dump' => array(
			'db1042' => 1,
		),
		'api' => array(
			'db1056' => 1,
			'db1059' => 1,
		),
		'watchlist' => array(
			'db1019' => 1,
		),
		'recentchanges' => array(
			'db1019' => 1,
		),
		'recentchangeslinked' => array(
			'db1019' => 1,
		),
		'contributions' => array(
			'db1019' => 1,
		),
		'logpager' => array(
			'db1019' => 1,
		),
	),
	's5' => array(
		'vslow' => array(
			'db1049' => 1,
		),
		'dump' => array(
			'db1049' => 1,
		),
		'api' => array(
			'db1045' => 1,
		),
		'watchlist' => array(
			'db1026' => 1,
		),
		'recentchanges' => array(
			'db1026' => 1,
		),
		'recentchangeslinked' => array(
			'db1026' => 1,
		),
		'contributions' => array(
			'db1026' => 1,
		),
		'logpager' => array(
			'db1026' => 1,
		),
	),
	's6' => array(
		'vslow' => array(
			'db1030' => 1,
		),
		'dump' => array(
			'db1030' => 1,
		),
		'watchlist' => array(
			'db1037' => 1,
		),
		'recentchanges' => array(
			'db1037' => 1,
		),
		'recentchangeslinked' => array(
			'db1037' => 1,
		),
		'contributions' => array(
			'db1037' => 1,
		),
		'logpager' => array(
			'db1037' => 1,
		),
	),
	's7' => array(
		'vslow' => array(
			'db1028' => 1,
		),
		'dump' => array(
			'db1028' => 1,
		),
		'watchlist' => array(
			'db1034' => 1,
		),
		'recentchanges' => array(
			'db1034' => 1,
		),
		'recentchangeslinked' => array(
			'db1034' => 1,
		),
		'contributions' => array(
			'db1034' => 1,
		),
		'logpager' => array(
			'db1034' => 1,
		),
	),
),


'groupLoadsByDB' => array(),

# Hosts settings
# Do not remove servers from this list ever
# Removing a server from this list does not remove the server from rotation,
# it just breaks the site horribly.
'hostsByName' => array(
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
	'db56'	   => '10.0.6.66', # do not remove or comment out
	'db57'	   => '10.0.6.67', # do not remove or comment out
	'db58'	   => '10.0.6.68', # do not remove or comment out
	'db59'	   => '10.0.6.69', # do not remove or comment out
	'db60'	   => '10.0.6.70', # do not remove or comment out
	'db63'	   => '10.0.6.73', # do not remove or comment out
	'db64'	   => '10.0.6.74', # do not remove or comment out
	'db65'	   => '10.0.6.75', # do not remove or comment out
	'db66'	   => '10.0.6.76', # do not remove or comment out
	'db68'	   => '10.0.6.78', # do not remove or comment out
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
	'db1015' => '10.64.0.19', #do not remove or comment out
	'db1017' => '10.64.16.6', #do not remove or comment out
	'db1018' => '10.64.16.7', #do not remove or comment out
	'db1019' => '10.64.16.8', #do not remove or comment out
	'db1020' => '10.64.16.9', #do not remove or comment out
	'db1021' => '10.64.16.10', #do not remove or comment out
	'db1022' => '10.64.16.11', #do not remove or comment out
	'db1023' => '10.64.16.12', #do not remove or comment out
	'db1024' => '10.64.16.13', #do not remove or comment out
	'db1026' => '10.64.16.15', #do not remove or comment out
	'db1027' => '10.64.16.16', #do not remove or comment out
	'db1028' => '10.64.16.17', #do not remove or comment out
	'db1030' => '10.64.16.19', #do not remove or comment out
	'db1033' => '10.64.16.22', #do not remove or comment out
	'db1034' => '10.64.16.23', #do not remove or comment out
	'db1035' => '10.64.16.24', #do not remove or comment out
	'db1036' => '10.64.16.25', #do not remove or comment out
	'db1037' => '10.64.16.26', #do not remove or comment out
	'db1038' => '10.64.16.27', #do not remove or comment out
	'db1039' => '10.64.16.28', #do not remove or comment out
	'db1040' => '10.64.16.29', #do not remove or comment out
	'db1041' => '10.64.16.30', #do not remove or comment out
	'db1042' => '10.64.16.31', #do not remove or comment out
	'db1043' => '10.64.16.32', #do not remove or comment out
	'db1044' => '10.64.16.33', #do not remove or comment out
	'db1045' => '10.64.16.34', #do not remove or comment out
	'db1047' => '10.64.16.36', #do not remove or comment out
	'db1049' => '10.64.16.144', #do not remove or comment out
	'db1050' => '10.64.16.145', #do not remove or comment out
	'db1051' => '10.64.32.21', #do not remove or comment out
	'db1052' => '10.64.32.22', #do not remove or comment out
	'db1053' => '10.64.32.23', #do not remove or comment out
	'db1054' => '10.64.32.24', #do not remove or comment out
	'db1055' => '10.64.32.25', #do not remove or comment out
	'db1056' => '10.64.32.26', #do not remove or comment out
	'db1057' => '10.64.32.27', #do not remove or comment out
	'db1058' => '10.64.32.28', #do not remove or comment out
	'db1059' => '10.64.32.29', #do not remove or comment out
	'db1060' => '10.64.32.30', #do not remove or comment out
	'db1061' => '10.64.48.14', #do not remove or comment out
	'db1062' => '10.64.48.15', #do not remove or comment out
	'db1063' => '10.64.48.16', #do not remove or comment out
	'db1064' => '10.64.48.19', #do not remove or comment out
	'db1065' => '10.64.48.20', #do not remove or comment out
	'db1066' => '10.64.48.21', #do not remove or comment out
	'db1067' => '10.64.48.22', #do not remove or comment out
	'db1068' => '10.64.48.23', #do not remove or comment out
	'db1070' => '10.64.48.25', #do not remove or comment out
	'db1071' => '10.64.48.26', #do not remove or comment out
	'db1072' => '10.64.48.27', #do not remove or comment out
	'db1073' => '10.64.48.28', #do not remove or comment out
	'virt1000' => '208.80.154.18', #do not remove or comment out
	'silver' => '208.80.154.136', #do not remove or comment out
        'labtestweb2001' => '208.80.153.14', #do not remove or comment out
),

'externalLoads' => array(
	# Recompressed stores
	'rc1' => $wmgOldExtTemplate,

	# Former Ubuntu dual-purpose stores
	'cluster3' => $wmgOldExtTemplate,
	'cluster4' => $wmgOldExtTemplate,
	'cluster5' => $wmgOldExtTemplate,
	'cluster6' => $wmgOldExtTemplate,
	'cluster7' => $wmgOldExtTemplate,
	'cluster8' => $wmgOldExtTemplate,
	'cluster9' => $wmgOldExtTemplate,
	'cluster10' => $wmgOldExtTemplate,
	'cluster20' => $wmgOldExtTemplate,
	'cluster21' => $wmgOldExtTemplate,

	# Clusters required for T24624
	'cluster1' => $wmgOldExtTemplate,
	'cluster2' => $wmgOldExtTemplate,

	# Old dedicated clusters
	'cluster22' => $wmgOldExtTemplate,
	'cluster23' => $wmgOldExtTemplate,

	# es2
	'cluster24' => array(
		'10.64.32.184' => 1, # es1015, master
		'10.64.0.6'    => 3, # es1011
		'10.64.16.186' => 3, # es1013
	),
	# es3
	'cluster25' => array(
		'10.64.48.116' => 1, # es1019, master
		'10.64.16.187' => 3, # es1014
		'10.64.48.114' => 3, # es1017
	),
	# ExtensionStore shard1 - initially for AFTv5
	'extension1' => array(
		'10.64.16.18' => 10, # db1029
		'10.64.16.20' => 20, # db1031 snapshot host
	),
),

'masterTemplateOverrides' => array(
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
#	's3'	   => 'Brief Database Maintenance in progress, please try again in 3 minutes',
),

);

$wgDefaultExternalStore = array(
	'DB://cluster24',
	'DB://cluster25',
);

# $wgLBFactoryConf['readOnlyBySection']['s2'] =
# $wgLBFactoryConf['readOnlyBySection']['s2a'] =
# 'Emergency maintenance, need more servers up, new estimate ~18:30 UTC';
