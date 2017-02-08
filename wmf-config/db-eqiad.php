<?php
# WARNING: This file is publically viewable on the web. Do not put private data here.

if ( !defined( 'DBO_DEFAULT' ) ) {
	define( 'DBO_DEFAULT', 16 );
}

# $wgReadOnly = "Wikimedia Sites are currently read-only during maintenance, please try again soon.";

$wmgParserCacheDBs = [
	'10.64.0.12'   => '10.64.0.12',   # pc1004
	'10.64.32.72'  => '10.64.32.72',  # pc1005
	'10.64.48.128' => '10.64.48.128', # pc1006
];

$wmgOldExtTemplate = [
	'10.64.0.7'    => 1, # es1012
	'10.64.32.185' => 1, # es1016
	'10.64.48.115' => 1, # es1018
];

$wgLBFactoryConf = [

'class' => 'LBFactoryMulti',

'sectionsByDB' => [
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
],

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
#
# Additionally, if a server should not to be lagged (for example,
# an api node, or a recentchanges node, set the load to at least 1.
# This will make the node be taken into account on the wait for lag
# function (the master is not included, as by definition has lag 0).

'sectionLoads' => [
	's1' => [
		'db1052' => 0,   # B3 2.8TB  96GB, master
#		'db1057' => 0,   # C2 2.8TB  96GB, old master
		'db1051' => 50,  # B3 2.8TB  96GB, watchlist, recentchanges, contributions, logpager
		'db1055' => 50,  # C2 2.8TB  96GB, watchlist, recentchanges, contributions, logpager
		'db1065' => 0,   # D1 2.8TB 160GB, vslow, dump, master for sanitarium
		'db1066' => 50,  # D1 2.8TB 160GB, api
		'db1072' => 50,  # B2 2.8TB 160GB, api
#		'db1073' => 50,  # B3 2.8TB 160GB, api #T156126
		'db1080' => 500, # A2 3.6TB 512GB
		'db1083' => 500, # B1 3.6TB 512GB
		'db1089' => 500, # C3 3.6TB 512GB
	],
	's2' => [
		'db1018' => 0,   # B1 1.4TB  64GB, master
#		'db1024' => 0,   # B1 1.4TB  64GB, old master
		'db1021' => 0,   # B1 1.4TB  64GB, vslow, dump
		'db1036' => 1,   # B2 1.4TB  64GB, watchlist, recentchanges, contributions, logpager
		'db1054' => 1,   # A3 2.8TB  96GB, api
		'db1060' => 1,   # C2 2.8TB  96GB, api
#		'db1063' => 0,   # D1 2.8TB 128GB
#		'db1067' => 0,   # D1 2.8TB 160GB
		'db1074' => 500, # A2 3.6TB 512GB
		'db1076' => 500, # B1 3.6TB 512GB
		'db1090' => 500, # C3 3.6TB 512GB
	],
	/* s3 */ 'DEFAULT' => [
		'db1075' => 0,   # A2 3.6TB 512GB, master
		'db1038' => 0,   # B2 1.4TB  64GB, vslow, dump, old master
		'db1015' => 1,   # A2 1.4TB  64GB, watchlist, recentchanges, contributions, logpager
#		'db1035' => 0,   # B2 1.4TB  64GB
#		'db1044' => 0,   # B2 1.4TB  64GB, #Temporary master for db1095 - new sanitarium #T150802
		'db1077' => 500, # B1 3.6TB 512GB
		'db1078' => 500, # C3 3.6TB 512GB
	],
	's4' => [
		'db1040' => 0,   # B2 1.4TB  64GB, master
		'db1053' => 1,   # A2 2.8TB  96GB, watchlist, recentchanges, contributions, logpager
		'db1056' => 1,   # C2 2.8TB  96GB, watchlist, recentchanges, contributions, logpager
		'db1059' => 1,   # C2 2.8TB  96GB, api
#		'db1064' => 0,   # D1 2.8TB 160GB, vslow, dump #Master for db1095
		'db1068' => 0,   # D1 2.8TB 160GB, #temp vslow, dump #T153743
		'db1081' => 300, # A2 3.6TB 512GB, #temp api #T153743
		'db1084' => 500, # B1 3.6TB 512GB
		'db1091' => 500, # D2 3.6TB 512GB
	],
	's5' => [
		'db1049' => 0,   # B1 2.8TB  64GB, master
#		'db1026' => 1,   # B1 1.4TB  64GB, watchlist, recentchanges, contributions, logpager
		'db1045' => 0,   # B2 1.4TB  64GB, vslow, dump
		'db1070' => 50,  # D1 2.8TB 160GB, api, old master
		'db1071' => 50,  # D1 2.8TB 160GB, api
		'db1082' => 1,   # A2 3.6TB 512GB, temp. wl, rc
		'db1087' => 500, # C2 3.6TB 512GB
		'db1092' => 500, # D2 3.6TB 512GB
	],
	's6' => [
		'db1050' => 0,   # B2 2.8TB  64GB, master
#		'db1023' => 0,   # B1 1.4TB  64GB, old master
		'db1022' => 1,   # B1 1.4TB  64GB, api
		'db1030' => 0,   # B1 1.4TB  64GB, vslow, dump
		'db1037' => 1,   # B2 1.4TB  64GB, watchlist, recentchanges, contributions, logpager
#		'db1061' => 0,   # D1 2.8TB 128GB
		'db1085' => 500, # B3 3.6TB 512GB
		'db1088' => 500, # C2 3.6TB 512GB
		'db1093' => 500, # D2 3.6TB 512GB
	],
	's7' => [
		'db1041' => 0,   # B2 1.4TB  64GB, master
#		'db1033' => 0,   # B2 1.4TB  64GB, old master
#		'db1028' => 0,   # B1 1.4TB  64GB, vslow, dump #T153300
		'db1034' => 1,   # B2 1.4TB  64GB, watchlist, recentchanges, contributions, logpager
		'db1039' => 0,   # B2 1.4TB  64GB, #temp vslow, dump #T153300
		'db1062' => 1,   # D1 2.8TB 128GB, watchlist, recentchanges, contributions, logpager
		'db1079' => 300, # A2 3.6TB 512GB, api
		'db1086' => 500, # B3 3.6TB 512GB, api
		'db1094' => 500, # D2 3.6TB 512GB
	],
	'silver' => [
		'silver' => 100,   # I have no idea if this is right
	],
	'labtestweb2001' => [
		'labtestweb2001' => 100,   # I have no idea if this is right
	],
],

'serverTemplate' => [
	'dbname'	  => $wgDBname,
	'user'		  => $wgDBuser,
	'password'	  => $wgDBpassword,
	'type'		  => 'mysql',
	'flags'		  => DBO_DEFAULT,
	'max lag'	  => 6, // should be safely less than $wgCdnReboundPurgeDelay
	'variables'   => [
		'innodb_lock_wait_timeout' => 15
	]
],

'templateOverridesBySection' => [
	's1' => [
		'lagDetectionMethod' => 'pt-heartbeat',
		'lagDetectionOptions' => [
			'conds' => [ 'shard' => 's1', 'datacenter' => $wmfMasterDatacenter ] ],
		'useGTIDs' => true
	],
	's2' => [
		'lagDetectionMethod' => 'pt-heartbeat',
		'lagDetectionOptions' => [
			'conds' => [ 'shard' => 's2', 'datacenter' => $wmfMasterDatacenter ] ],
		'useGTIDs' => true
	],
	'DEFAULT' /* s3 */  => [
		'lagDetectionMethod' => 'pt-heartbeat',
		'lagDetectionOptions' => [
			'conds' => [ 'shard' => 's3', 'datacenter' => $wmfMasterDatacenter ] ],
		'useGTIDs' => true
	],
	's4' => [
		'lagDetectionMethod' => 'pt-heartbeat',
		'lagDetectionOptions' => [
			'conds' => [ 'shard' => 's4', 'datacenter' => $wmfMasterDatacenter ] ],
		'useGTIDs' => true
	],
	's5' => [
		'lagDetectionMethod' => 'pt-heartbeat',
		'lagDetectionOptions' => [
			'conds' => [ 'shard' => 's5', 'datacenter' => $wmfMasterDatacenter ] ],
		'useGTIDs' => true
	],
	's6' => [
		'lagDetectionMethod' => 'pt-heartbeat',
		'lagDetectionOptions' => [
			'conds' => [ 'shard' => 's6', 'datacenter' => $wmfMasterDatacenter ] ],
		'useGTIDs' => true
	],
	's7' => [
		'lagDetectionMethod' => 'pt-heartbeat',
		'lagDetectionOptions' => [
			'conds' => [ 'shard' => 's7', 'datacenter' => $wmfMasterDatacenter ] ],
		'useGTIDs' => true
	],
],

'groupLoadsBySection' => [
	's1' => [
		'watchlist' => [
			'db1051' => 1,
			'db1055' => 1,
		],
		'recentchanges' => [
			'db1051' => 1,
			'db1055' => 1,
		],
		'recentchangeslinked' => [
			'db1051' => 1,
			'db1055' => 1,
		],
		'contributions' => [
			'db1051' => 1,
			'db1055' => 1,
		],
		'logpager' => [
			'db1051' => 1,
			'db1055' => 1,
		],
		'dump' => [
			'db1065' => 1,
		],
		'vslow' => [
			'db1065' => 1,
		],
		'api' => [
			'db1066' => 1,
			'db1072' => 1,
#			'db1073' => 1,
		],
	],
	's2' => [
		'vslow' => [
			'db1021' => 1,
		],
		'dump' => [
			'db1021' => 1,
		],
		'api' => [
			'db1054' => 1,
			'db1060' => 1,
		],
		'watchlist' => [
			'db1036' => 1,
		],
		'recentchanges' => [
			'db1036' => 1,
		],
		'recentchangeslinked' => [
			'db1036' => 1,
		],
		'contributions' => [
			'db1036' => 1,
		],
		'logpager' => [
			'db1036' => 1,
		],
	],
	/* s3 */ 'DEFAULT' => [
		'vslow' => [
			'db1038' => 1,
		],
		'dump' => [
			'db1038' => 1,
		],
		'watchlist' => [
			'db1015' => 1,
		],
		'recentchanges' => [
			'db1015' => 1,
		],
		'recentchangeslinked' => [
			'db1015' => 1,
		],
		'contributions' => [
			'db1015' => 1,
		],
		'logpager' => [
			'db1015' => 1,
		],
	],
	's4' => [
		'vslow' => [
			'db1068' => 1,
		],
		'dump' => [
			'db1068' => 1,
		],
		'api' => [
			'db1059' => 1,
			'db1081' => 1,
		],
		'watchlist' => [
			'db1053' => 1,
			'db1056' => 1,
		],
		'recentchanges' => [
			'db1053' => 1,
			'db1056' => 1,
		],
		'recentchangeslinked' => [
			'db1053' => 1,
			'db1056' => 1,
		],
		'contributions' => [
			'db1053' => 1,
			'db1056' => 1,
		],
		'logpager' => [
			'db1053' => 1,
			'db1056' => 1,
		],
	],
	's5' => [
		'vslow' => [
			'db1045' => 1,
		],
		'dump' => [
			'db1045' => 1,
		],
		'api' => [
			'db1070' => 1,
			'db1071' => 1,
		],
		'watchlist' => [
			'db1082' => 1,
		],
		'recentchanges' => [
			'db1082' => 1,
		],
		'recentchangeslinked' => [
			'db1082' => 1,
		],
		'contributions' => [
			'db1082' => 1,
		],
		'logpager' => [
			'db1082' => 1,
		],
	],
	's6' => [
		'vslow' => [
			'db1030' => 1,
		],
		'dump' => [
			'db1030' => 1,
		],
		'api' => [
			'db1022' => 1,
		],
		'watchlist' => [
			'db1037' => 1,
		],
		'recentchanges' => [
			'db1037' => 1,
		],
		'recentchangeslinked' => [
			'db1037' => 1,
		],
		'contributions' => [
			'db1037' => 1,
		],
		'logpager' => [
			'db1037' => 1,
		],
	],
	's7' => [
		'vslow' => [
			'db1039' => 1,
		],
		'dump' => [
			'db1039' => 1,
		],
		'api' => [
			'db1079' => 100,
			'db1086' => 1,
		],
		'watchlist' => [
			'db1034' => 1,
			'db1062' => 100,
		],
		'recentchanges' => [
			'db1034' => 1,
			'db1062' => 100,
		],
		'recentchangeslinked' => [
			'db1034' => 1,
			'db1062' => 100,
		],
		'contributions' => [
			'db1034' => 1,
			'db1062' => 100,
		],
		'logpager' => [
			'db1034' => 1,
			'db1062' => 100,
		],
	],
],


'groupLoadsByDB' => [],

# Hosts settings
# Do not remove servers from this list ever
# Removing a server from this list does not remove the server from rotation,
# it just breaks the site horribly.
'hostsByName' => [
	'db1001' => '10.64.0.5', # do not remove or comment out
	'db1009' => '10.64.0.13', # do not remove or comment out
	'db1011' => '10.64.0.15', # do not remove or comment out
	'db1015' => '10.64.0.19', # do not remove or comment out
	'db1018' => '10.64.16.7', # do not remove or comment out
	'db1020' => '10.64.16.9', # do not remove or comment out
	'db1021' => '10.64.16.10', # do not remove or comment out
	'db1022' => '10.64.16.11', # do not remove or comment out
	'db1023' => '10.64.16.12', # do not remove or comment out
	'db1024' => '10.64.16.13', # do not remove or comment out
	'db1026' => '10.64.16.15', # do not remove or comment out
	'db1028' => '10.64.16.17', # do not remove or comment out
	'db1030' => '10.64.16.19', # do not remove or comment out
	'db1033' => '10.64.16.22', # do not remove or comment out
	'db1034' => '10.64.16.23', # do not remove or comment out
	'db1035' => '10.64.16.24', # do not remove or comment out
	'db1036' => '10.64.16.25', # do not remove or comment out
	'db1037' => '10.64.16.26', # do not remove or comment out
	'db1038' => '10.64.16.27', # do not remove or comment out
	'db1039' => '10.64.16.28', # do not remove or comment out
	'db1040' => '10.64.16.29', # do not remove or comment out
	'db1041' => '10.64.16.30', # do not remove or comment out
	'db1043' => '10.64.16.32', # do not remove or comment out
	'db1044' => '10.64.16.33', # do not remove or comment out
	'db1045' => '10.64.16.34', # do not remove or comment out
	'db1047' => '10.64.16.36', # do not remove or comment out
	'db1049' => '10.64.16.144', # do not remove or comment out
	'db1050' => '10.64.16.145', # do not remove or comment out
	'db1051' => '10.64.16.76', # do not remove or comment out
	'db1052' => '10.64.16.77', # do not remove or comment out
	'db1053' => '10.64.0.87', # do not remove or comment out
	'db1054' => '10.64.0.206', # do not remove or comment out
	'db1055' => '10.64.32.25', # do not remove or comment out
	'db1056' => '10.64.32.26', # do not remove or comment out
	'db1057' => '10.64.32.27', # do not remove or comment out
	'db1059' => '10.64.32.29', # do not remove or comment out
	'db1060' => '10.64.32.30', # do not remove or comment out
	'db1061' => '10.64.48.14', # do not remove or comment out
	'db1062' => '10.64.48.15', # do not remove or comment out
	'db1063' => '10.64.48.16', # do not remove or comment out
	'db1064' => '10.64.48.19', # do not remove or comment out
	'db1065' => '10.64.48.20', # do not remove or comment out
	'db1066' => '10.64.48.21', # do not remove or comment out
	'db1067' => '10.64.48.22', # do not remove or comment out
	'db1068' => '10.64.48.23', # do not remove or comment out
	'db1070' => '10.64.48.25', # do not remove or comment out
	'db1071' => '10.64.48.26', # do not remove or comment out
	'db1072' => '10.64.16.39', # do not remove or comment out
	'db1073' => '10.64.16.79', # do not remove or comment out
	'db1074' => '10.64.0.204', # do not remove or comment out
	'db1075' => '10.64.0.205', # do not remove or comment out
	'db1076' => '10.64.16.190', # do not remove or comment out
	'db1077' => '10.64.16.191', # do not remove or comment out
	'db1078' => '10.64.32.136', # do not remove or comment out
	'db1079' => '10.64.0.91', # do not remove or comment out
	'db1080' => '10.64.0.92', # do not remove or comment out
	'db1081' => '10.64.0.93', # do not remove or comment out
	'db1082' => '10.64.0.94', # do not remove or comment out
	'db1083' => '10.64.16.101', # do not remove or comment out
	'db1084' => '10.64.16.102', # do not remove or comment out
	'db1085' => '10.64.16.103', # do not remove or comment out
	'db1086' => '10.64.16.104', # do not remove or comment out
	'db1087' => '10.64.32.113', # do not remove or comment out
	'db1088' => '10.64.32.114', # do not remove or comment out
	'db1089' => '10.64.32.115', # do not remove or comment out
	'db1090' => '10.64.32.116', # do not remove or comment out
	'db1091' => '10.64.48.150', # do not remove or comment out
	'db1092' => '10.64.48.151', # do not remove or comment out
	'db1093' => '10.64.48.152', # do not remove or comment out
	'db1094' => '10.64.48.153', # do not remove or comment out
	'db2001' => '10.192.0.4', # do not remove or comment out
	'db2002' => '10.192.0.5', # do not remove or comment out
	'db2003' => '10.192.0.6', # do not remove or comment out
	'db2004' => '10.192.0.7', # do not remove or comment out
	'db2005' => '10.192.0.8', # do not remove or comment out
	'db2006' => '10.192.0.9', # do not remove or comment out
	'db2007' => '10.192.0.10', # do not remove or comment out
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
	'db2034' => '10.192.0.87', # do not remove or comment out
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
	'virt1000' => '208.80.154.18', # do not remove or comment out
	'silver' => '208.80.154.136', # do not remove or comment out
        'labtestweb2001' => '208.80.153.14', # do not remove or comment out
],

'externalLoads' => [
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
	'cluster24' => [
		'10.64.0.6'    => 1, # es1011, master
		'10.64.16.186' => 1, # es1013
		'10.64.32.184' => 1, # es1015
	],
	# es3
	'cluster25' => [
		'10.64.16.187' => 1, # es1014, master
		'10.64.48.114' => 1, # es1017
		'10.64.48.116' => 1, # es1019
	],
	# ExtensionStore shard1 - initially for AFTv5
	'extension1' => [
		'10.64.16.20' => 10, # db1031, master
		'10.64.16.18' => 20, # db1029
	],
],

'masterTemplateOverrides' => [
],

'externalTemplateOverrides' => [
	'flags' => 0, // No transactions
],

'templateOverridesByCluster' => [
	'rc1'		=> [ 'is static' => true ],
	'cluster1'	=> [ 'blobs table' => 'blobs_cluster1', 'is static' => true ],
	'cluster2'	=> [ 'blobs table' => 'blobs_cluster2', 'is static' => true ],
	'cluster3'	=> [ 'blobs table' => 'blobs_cluster3', 'is static' => true ],
	'cluster4'	=> [ 'blobs table' => 'blobs_cluster4', 'is static' => true ],
	'cluster5'	=> [ 'blobs table' => 'blobs_cluster5', 'is static' => true ],
	'cluster6'	=> [ 'blobs table' => 'blobs_cluster6', 'is static' => true ],
	'cluster7'	=> [ 'blobs table' => 'blobs_cluster7', 'is static' => true ],
	'cluster8'	=> [ 'blobs table' => 'blobs_cluster8', 'is static' => true ],
	'cluster9'	=> [ 'blobs table' => 'blobs_cluster9', 'is static' => true ],
	'cluster10'	=> [ 'blobs table' => 'blobs_cluster10', 'is static' => true ],
	'cluster20'	=> [ 'blobs table' => 'blobs_cluster20', 'is static' => true ],
	'cluster21'	=> [ 'blobs table' => 'blobs_cluster21', 'is static' => true ],
	'cluster22'	=> [ 'blobs table' => 'blobs_cluster22', 'is static' => true ],
	'cluster23'	=> [ 'blobs table' => 'blobs_cluster23', 'is static' => true ],
	'cluster24'	=> [ 'blobs table' => 'blobs_cluster24' ],
	'cluster25'	=> [ 'blobs table' => 'blobs_cluster25' ],
],

# This key must exist for the master switch script to work
'readOnlyBySection' => [
#	'DEFAULT' => 'MediaWiki is in read-only mode for maintenance. Please try again in 3 minutes', # s3
#	's1'      => 'MediaWiki is in read-only mode for maintenance. Please try again in 3 minutes',
#	's2'      => 'MediaWiki is in read-only mode for maintenance. Please try again in 3 minutes',
#	's4'      => 'MediaWiki is in read-only mode for maintenance. Please try again in 3 minutes',
#	's5'      => 'MediaWiki is in read-only mode for maintenance. Please try again in 3 minutes',
#	's6'      => 'MediaWiki is in read-only mode for maintenance. Please try again in 3 minutes',
#	's7'      => 'MediaWiki is in read-only mode for maintenance. Please try again in 3 minutes',
],

];

$wgDefaultExternalStore = [
	'DB://cluster24',
	'DB://cluster25',
];

# $wgLBFactoryConf['readOnlyBySection']['s2'] =
# 'Scheduled maintenance, s2 wikis in read-only mode for a few minutes';
# $wgLBFactoryConf['readOnlyBySection']['s2a'] =
# 'Emergency maintenance, need more servers up, new estimate ~18:30 UTC';
