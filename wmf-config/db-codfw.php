<?php
# WARNING: This file is publically viewable on the web. Do not put private data here.

if ( !defined( 'DBO_DEFAULT' ) ) {
	define( 'DBO_DEFAULT', 16 );
}

#$wgReadOnly = "Wikimedia Sites are currently read-only during maintenance, please try again soon.";

$wmgParserCacheDBs = array(
	'10.192.16.170', # pc2004
	'10.192.32.128', # pc2005
	'10.192.48.39',  # pc2006
);

$wmgOldExtTemplate = array(
	'10.192.0.25'  => 1, # es2001
	'10.192.0.26'  => 1, # es2002
	'10.192.16.27' => 1, # es2003
	'10.192.16.28' => 1, # es2004
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
		'db2016' => 0,   # master
		'db2034' => 50,  # rc, log
		'db2042' => 50,  # rc, log
		'db2048' => 400,
		'db2055' => 200, # dump, vslow
		'db2062' => 50,  # api
		'db2069' => 50,  # api
		'db2070' => 500,
	),
	's2' => array(
		'db2017' => 0,   # master
		'db2035' => 50,  # rc, log
		'db2041' => 50,  # api
		'db2049' => 400,
		'db2056' => 200, # dump, vslow
		'db2063' => 50,  # api
		'db2064' => 500,
	),
	/* s3 */ 'DEFAULT' => array(
		'db2018' => 0,   # master
		'db2036' => 50,  # rc, log
		'db2043' => 100, # dump, vslow
		'db2050' => 100, # api
		'db2057' => 500,
	),
	's4' => array(
		'db2019' => 0,   # master
		'db2037' => 50,  # rc, log
		'db2044' => 50,  # rc, log
		'db2051' => 100, # api
		'db2058' => 200, # dump, vslow
		'db2065' => 500,
	),
	's5' => array(
		'db2023' => 0,   # master
		'db2038' => 50,  # rc, log
		'db2045' => 400,
		'db2052' => 200, # dump, vslow
		'db2059' => 100, # api
		'db2066' => 500,
	),
	's6' => array(
		'db2028' => 0,   # master
		'db2039' => 50,  # rc, log
		'db2046' => 400,
		'db2053' => 200, # dump, vslow
		'db2060' => 100, # api
		'db2067' => 500,
	),
	's7' => array(
		'db2029' => 0,   # master
		'db2040' => 100, # rc, log
		'db2047' => 400,
		'db2054' => 200, # dump, vslow
		'db2061' => 100, # api
		'db2068' => 500,
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
			'db2034' => 1,
			'db2042' => 1,
		),
		'recentchanges' => array(
			'db2034' => 1,
			'db2042' => 1,
		),
		'recentchangeslinked' => array(
			'db2034' => 1,
			'db2042' => 1,
		),
		'contributions' => array(
			'db2034' => 1,
			'db2042' => 1,
		),
		'logpager' => array(
			'db2034' => 1,
			'db2042' => 1,
		),
		'dump' => array(
			'db2055' => 1,
		),
		'vslow' => array(
			'db2055' => 1,
		),
		'api' => array(
			'db2062' => 1,
			'db2069' => 1,
		),
	),
	's2' => array(
		'watchlist' => array(
			'db2035' => 1,
		),
		'recentchanges' => array(
			'db2035' => 1,
		),
		'recentchangeslinked' => array(
			'db2035' => 1,
		),
		'contributions' => array(
			'db2035' => 1,
		),
		'logpager' => array(
			'db2035' => 1,
		),
		'dump' => array(
			'db2056' => 1,
		),
		'vslow' => array(
			'db2056' => 1,
		),
		'api' => array(
			'db2041' => 1,
			'db2063' => 1,
		),
	),
	/* s3 */ 'DEFAULT' => array(
		'watchlist' => array(
			'db2036' => 1,
		),
		'recentchanges' => array(
			'db2036' => 1,
		),
		'recentchangeslinked' => array(
			'db2036' => 1,
		),
		'contributions' => array(
			'db2036' => 1,
		),
		'logpager' => array(
			'db2036' => 1,
		),
		'dump' => array(
			'db2043' => 1,
		),
		'vslow' => array(
			'db2043' => 1,
		),
		'api' => array(
			'db2050' => 1,
		),
	),
	's4' => array(
		'watchlist' => array(
			'db2037' => 1,
			'db2044' => 1,
		),
		'recentchanges' => array(
			'db2037' => 1,
			'db2044' => 1,
		),
		'recentchangeslinked' => array(
			'db2037' => 1,
			'db2044' => 1,
		),
		'contributions' => array(
			'db2037' => 1,
			'db2044' => 1,
		),
		'logpager' => array(
			'db2037' => 1,
			'db2044' => 1,
		),
		'dump' => array(
			'db2058' => 1,
		),
		'vslow' => array(
			'db2058' => 1,
		),
		'api' => array(
			'db2051' => 1,
		),
	),
	's5' => array(
		'watchlist' => array(
			'db2038' => 1,
		),
		'recentchanges' => array(
			'db2038' => 1,
		),
		'recentchangeslinked' => array(
			'db2038' => 1,
		),
		'contributions' => array(
			'db2038' => 1,
		),
		'logpager' => array(
			'db2038' => 1,
		),
		'dump' => array(
			'db2052' => 1,
		),
		'vslow' => array(
			'db2052' => 1,
		),
		'api' => array(
			'db2059' => 1,
		),
	),
	's6' => array(
		'watchlist' => array(
			'db2039' => 1,
		),
		'recentchanges' => array(
			'db2039' => 1,
		),
		'recentchangeslinked' => array(
			'db2039' => 1,
		),
		'contributions' => array(
			'db2039' => 1,
		),
		'logpager' => array(
			'db2039' => 1,
		),
		'dump' => array(
			'db2053' => 1,
		),
		'vslow' => array(
			'db2053' => 1,
		),
		'api' => array(
			'db2060' => 1,
		),
	),
	's7' => array(
		'watchlist' => array(
			'db2040' => 1,
		),
		'recentchanges' => array(
			'db2040' => 1,
		),
		'recentchangeslinked' => array(
			'db2040' => 1,
		),
		'contributions' => array(
			'db2040' => 1,
		),
		'logpager' => array(
			'db2040' => 1,
		),
		'dump' => array(
			'db2054' => 1,
		),
		'vslow' => array(
			'db2054' => 1,
		),
		'api' => array(
			'db2061' => 1,
		),
	),
),

'groupLoadsByDB' => array(),

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
	'db2001' => '10.192.0.4', # do not remove or comment out
	'db2002' => '10.192.0.5', # do not remove or comment out
	'db2003' => '10.192.0.6', # do not remove or comment out
	'db2004' => '10.192.0.7', # do not remove or comment out
	'db2005' => '10.192.0.8', # do not remove or comment out
	'db2006' => '10.192.0.9', # do not remove or comment out
	'db2007' => '10.192.0.10', # do not remove or comment out
	'db2008' => '10.192.0.11', # do not remove or comment out
	'db2009' => '10.192.0.12', # do not remove or comment out
	'db2010' => '10.192.0.13', # do not remove or comment out
	'db2011' => '10.192.0.14', # do not remove or comment out
	'db2012' => '10.192.0.15', # do not remove or comment out
	'db2013' => '10.192.0.16', # do not remove or comment out
	'db2014' => '10.192.0.17', # do not remove or comment out
	'db2015' => '10.192.0.18', # do not remove or comment out
	'db2016' => '10.192.16.4', # do not remove or comment out
	'db2017' => '10.192.16.5', # do not remove or comment out
	'db2018' => '10.192.16.6', # do not remove or comment out
	'db2019' => '10.192.16.7', # do not remove or comment out
	'db2020' => '10.192.16.8', # do not remove or comment out
	'db2021' => '10.192.16.9', # do not remove or comment out
	'db2022' => '10.192.16.10', # do not remove or comment out
	'db2023' => '10.192.16.11', # do not remove or comment out
	'db2024' => '10.192.16.12', # do not remove or comment out
	'db2025' => '10.192.16.13', # do not remove or comment out
	'db2026' => '10.192.16.14', # do not remove or comment out
	'db2027' => '10.192.16.15', # do not remove or comment out
	'db2028' => '10.192.16.16', # do not remove or comment out
	'db2029' => '10.192.16.17', # do not remove or comment out
	'db2030' => '10.192.16.18', # do not remove or comment out
	'db2031' => '10.192.16.19', # do not remove or comment out
	'db2032' => '10.192.16.20', # do not remove or comment out
	'db2033' => '10.192.32.4', # do not remove or comment out
	'db2034' => '10.192.32.5', # do not remove or comment out
	'db2035' => '10.192.32.6', # do not remove or comment out
	'db2036' => '10.192.32.7', # do not remove or comment out
	'db2037' => '10.192.32.8', # do not remove or comment out
	'db2038' => '10.192.32.9', # do not remove or comment out
	'db2039' => '10.192.32.10', # do not remove or comment out
	'db2040' => '10.192.32.11', # do not remove or comment out
	'db2041' => '10.192.32.12', # do not remove or comment out
	'db2042' => '10.192.32.13', # do not remove or comment out
	'db2043' => '10.192.32.103', # do not remove or comment out
	'db2044' => '10.192.32.104', # do not remove or comment out
	'db2045' => '10.192.32.105', # do not remove or comment out
	'db2046' => '10.192.32.106', # do not remove or comment out
	'db2047' => '10.192.32.107', # do not remove or comment out
	'db2048' => '10.192.32.108', # do not remove or comment out
	'db2049' => '10.192.32.109', # do not remove or comment out
	'db2050' => '10.192.32.110', # do not remove or comment out
	'db2051' => '10.192.32.111', # do not remove or comment out
	'db2052' => '10.192.48.4', # do not remove or comment out
	'db2053' => '10.192.48.5', # do not remove or comment out
	'db2054' => '10.192.48.6', # do not remove or comment out
	'db2055' => '10.192.48.7', # do not remove or comment out
	'db2056' => '10.192.48.8', # do not remove or comment out
	'db2057' => '10.192.48.9', # do not remove or comment out
	'db2058' => '10.192.48.10', # do not remove or comment out
	'db2059' => '10.192.48.11', # do not remove or comment out
	'db2060' => '10.192.48.12', # do not remove or comment out
	'db2061' => '10.192.48.13', # do not remove or comment out
	'db2062' => '10.192.48.14', # do not remove or comment out
	'db2063' => '10.192.48.15', # do not remove or comment out
	'db2064' => '10.192.48.16', # do not remove or comment out
	'db2065' => '10.192.48.17', # do not remove or comment out
	'db2066' => '10.192.48.18', # do not remove or comment out
	'db2067' => '10.192.48.19', # do not remove or comment out
	'db2068' => '10.192.48.20', # do not remove or comment out
	'db2069' => '10.192.48.21', # do not remove or comment out
	'db2070' => '10.192.48.22', # do not remove or comment out
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
		'10.64.32.184' => 0, # es1015, master
		'10.192.0.28'  => 1, # es2006
		'10.192.0.27'  => 3, # es2005
		'10.192.0.29'  => 3, # es2007
	),
	# es3
	'cluster25' => array(
		'10.64.16.187' => 0, # es1019, master
		'10.192.16.29' => 1, # es2008
		'10.192.16.30' => 3, # es2009
		'10.192.16.31' => 3, # es2010
	),
	# ExtensionStore shard1 - initially for AFTv5
	'extension1' => array(
		'10.64.16.18' => 10, # db1029
		'10.192.0.12' => 20, # db2009
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
	'DEFAULT' => 'Brief Database Maintenance in progress, please try again in 3 minutes', #s3
	's1'	   => 'Brief Database Maintenance in progress, please try again in 3 minutes',
	's5'	   => 'Brief Database Maintenance in progress, please try again in 3 minutes',
	's3'	   => 'Brief Database Maintenance in progress, please try again in 3 minutes',
),

);

$wgDefaultExternalStore = array(
	'DB://cluster24',
	'DB://cluster25',
);

# $wgLBFactoryConf['readOnlyBySection']['s2'] =
# $wgLBFactoryConf['readOnlyBySection']['s2a'] =
# 'Emergency maintenance, need more servers up, new estimate ~18:30 UTC';
