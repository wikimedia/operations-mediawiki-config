<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

if ( !defined( 'DBO_DEFAULT' ) ) {
	define( 'DBO_DEFAULT', 16 );
}

# $wgReadOnly = "Wikimedia Sites are currently read-only during maintenance, please try again soon.";

$wmgParserCacheDBs = [
	'pc1' => '10.192.0.104',  # pc2007, A1 4.4TB 256GB # pc1
	'pc2' => '10.192.16.35',  # pc2008, B3 4.4TB 256GB # pc2
	'pc3' => '10.192.32.10',  # pc2009, C1 4.4TB 256GB # pc3
	# 'spare' => '10.192.48.14',  # pc2010, D3 4.4TB 256GB # spare host. Use it to replace any of the above if needed
];

$wmgOldExtTemplate = [
	'10.192.16.171' => 1, # es2011, B1 11TB 128GB
	'10.192.32.129' => 1, # es2012, C1 11TB 128GB
	'10.192.48.40'  => 1, # es2013, D1 11TB 128GB
];

$wgLBFactoryConf = [

'class' => 'LBFactoryMulti',

'secret' => $wgSecretKey,

'sectionsByDB' => [
	# s1: enwiki
	'enwiki'       => 's1',

	# s2: large wikis
	'bgwiki'       => 's2',
	'bgwiktionary' => 's2',
	'cswiki'       => 's2',
	'enwikiquote'  => 's2',
	'enwiktionary' => 's2',
	'eowiki'       => 's2',
	'fiwiki'       => 's2',
	'idwiki'       => 's2',
	'itwiki'       => 's2',
	'nlwiki'       => 's2',
	'nowiki'       => 's2',
	'plwiki'       => 's2',
	'ptwiki'       => 's2',
	'svwiki'       => 's2',
	'thwiki'       => 's2',
	'trwiki'       => 's2',
	'zhwiki'       => 's2',

	# s3 (default)

	# s4: commons
	'commonswiki'     => 's4',
	'testcommonswiki' => 's4',

	# s5: dewiki and wikidata
	'cebwiki'      => 's5',
	'dewiki'       => 's5',
	'enwikivoyage' => 's5',
	'mgwiktionary' => 's5',
	'shwiki'       => 's5',
	'srwiki'       => 's5',

	# s6: large wikis
	'frwiki'       => 's6',
	'jawiki'       => 's6',
	'ruwiki'       => 's6',

	# s7: large wikis, centralauth
	'eswiki'       => 's7',
	'huwiki'       => 's7',
	'hewiki'       => 's7',
	'ukwiki'       => 's7',
	'frwiktionary' => 's7',
	'metawiki'     => 's7',
	'arwiki'       => 's7',
	'centralauth'  => 's7',
	'cawiki'       => 's7',
	'viwiki'       => 's7',
	'fawiki'       => 's7',
	'rowiki'       => 's7',
	'kowiki'       => 's7',

	# s8: wikidata
	'wikidatawiki' => 's8',

	# labs-related wikis
	'labswiki'     => 'wikitech',
	'labtestwiki'  => 'wikitech',
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
		'db2048'      => 0,   # A1 2.9TB 160GB, master
		'db2055'      => 50,  # D6 3.3TB 160GB, dump (inactive), vslow, api # candidate master
		'db2071'      => 50,  # A6 3.6TB 512GB, api
		'db2072'      => 300, # B6 3.6TB 512GB, # master for sanitarium db2094
		'db2088:3311' => 100,  # D1 3.3TB 512GB # rc, log: s1 and s2
		'db2085:3311' => 100,  # A5 3.3TB 512GB # rc, log: s1 and s8
		'db2092'      => 300, # B8 3.6TB 512GB, api
		'db2103'      => 300, # A3 5.2TB 512GB # future candidate master
		'db2112'      => 300, # C1 5.2TB 512GB # future master
		'db2116'      => 300, # C6 5.2TB 512GB
		'db2130'      => 300, # D6 5.2TB 512GB
	],
	's2' => [
		'db2107'      => 0, # B1 5.2TB 512GB # master
		'db2088:3312' => 150, # D1 3.3TB 512GB # rc, log: s1 and s2
		'db2091:3312' => 150, # A8 3.3TB 512GB # rc, log: s2 and s4
		'db2104'      => 500, # A5 5.2TB 512GB, api # candidate master
		'db2108'      => 100, # B3 5.2TB 512GB # vslow, dump
		'db2125'      => 500, # C1 5.2TB 512GB # api
		'db2126'      => 200, # C5 5.2TB 512GB # api # master for sanitarium db2095
	],
	/* s3 */ 'DEFAULT' => [
		'db2105'      => 0, # A6 5.2TB 512GB # master
		'db2074'      => 400, # D6 3.3TB 512GB # rc,log # master for sanitarium db2094
		'db2109'      => 300, # B5 5.2TB 512GB # vslow, dump
		'db2127'      => 500, # C6 5.2TB 512GB # candidate master
	],
	's4' => [
		'db2090'      => 0,   # C5 3.3TB 512GB, master
		'db2073'      => 1,   # C6 3.3TB 512GB, vslow, dump # master for sanitarium db2095
		'db2084:3314' => 150, # D6 3.3TB 512GB # rc, log: s4 and s5
		'db2091:3314' => 150, # A8 3.3TB 512GB # rc, log: s2 and s4
		'db2106'      => 100, # A8 5.2TB 512GB, api
		'db2110'      => 300, # B6 5.2TB 512GB, api # candidate master
		'db2119'      => 500, # D3 5.2TB 512GB
	],
	's5' => [
		'db2123'      => 0, # B3 5.2TB 512GB # master
		'db2075'      => 100, # A1 3.3TB 512GB # api
		'db2084:3315' => 100, # D6 3.3TB 512GB # rc, log: s4 and s5
		'db2089:3315' => 100, # A3 3.3TB 512GB # rc, log: s5 and s6
		'db2111'      => 500, # B6 5.2TB 512GB
		'db2113'      => 1, # C3 5.2TB 512GB # dump (inactive), vslow # candidate master
		'db2128'      => 300, # D1 5.2TB 512GB # api # master for sanitarium db2094
	],
	's6' => [
		'db2129'      => 0, # D5 5.2TB 512GB # master
		'db2076'      => 300, # B1 3.3TB 512GB # master for sanitarium db2095 # api
		'db2087:3316' => 50, # C1 3.3TB 512GB # rc, log: s6 and s7
		'db2089:3316' => 50, # A3 3.3TB 512GB # rc, log: s6 and s5(s8)
		'db2114'      => 300, # C5 5.2TB 512GB # candidate master # api
		'db2117'      => 200, # D1 5.2TB 512GB # vslow, dump (inactive)
		'db2124'      => 500, # B6 5.2TB 512GB
	],
	's7' => [
		'db2118'      => 0, # D3 5.2TB 512GB, # master
		# 'db2047'      => 0,   # C6 2.9TB 160GB, old master # to be decommissioned
		# 'db2054'      => 200, # D6 2.9TB 160GB, # to be decommissioned
		# 'db2061'      => 200, # D6 3.3TB 160GB, # to be decommissioned
		# 'db2068'      => 200, # D6 3.3TB 160GB  # to be decommissioned
		'db2077'      => 300, # C1 3.3TB 512GB # api # master for sanitarium db2095
		'db2086:3317' => 150, # B1 3.3TB 512GB # rc, log: s7 and s8
		'db2087:3317' => 150, # C1 3.3TB 512GB # rc, log: s6 and s7
		'db2120'      => 100, # D5 5.2TB 512GB # vslow, dump (inactive)
		'db2121'      => 300, # A5 5.2TB 512GB # api # candidate master
		'db2122'      => 500, # A6 5.2TB 512GB
	],
	's8' => [
		'db2079'      => 0,  # A5 3.3TB 512GB, # master
		'db2080'      => 200,  # C5 3.3TB 512GB, api # candidate master
		'db2081'      => 100,  # A6 3.3TB 512GB, api
		'db2082'      => 1, # B6 3.3TB 512GB # vslow,dump # master for sanitarium db2094
		'db2083'      => 500, # C6 3.3TB 512GB
		'db2085:3318' => 100,   # A5 3.3TB 512GB # rc, log: s1 and s8
		'db2086:3318' => 100,   # B1 3.3TB 512GB # rc, log: s7 and s8
	],

	'wikitech' => [
		'db2070' => 1, # C5, 3.3TB 160GB read only
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
			'conds' => [ 'shard' => 's1', 'datacenter' => $wmfMasterDatacenter ]
		],
		'useGTIDs' => true
	],
	's2' => [
		'lagDetectionMethod' => 'pt-heartbeat',
		'lagDetectionOptions' => [
			'conds' => [ 'shard' => 's2', 'datacenter' => $wmfMasterDatacenter ]
		],
		'useGTIDs' => true
	],
	'DEFAULT' /* s3 */  => [
		'lagDetectionMethod' => 'pt-heartbeat',
		'lagDetectionOptions' => [
			'conds' => [ 'shard' => 's3', 'datacenter' => $wmfMasterDatacenter ]
		],
		'useGTIDs' => true
	],
	's4' => [
		'lagDetectionMethod' => 'pt-heartbeat',
		'lagDetectionOptions' => [
			'conds' => [ 'shard' => 's4', 'datacenter' => $wmfMasterDatacenter ]
		],
		'useGTIDs' => true
	],
	's5' => [
		'lagDetectionMethod' => 'pt-heartbeat',
		'lagDetectionOptions' => [
			'conds' => [ 'shard' => 's5', 'datacenter' => $wmfMasterDatacenter ]
		],
		'useGTIDs' => true
	],
	's6' => [
		'lagDetectionMethod' => 'pt-heartbeat',
		'lagDetectionOptions' => [
			'conds' => [ 'shard' => 's6', 'datacenter' => $wmfMasterDatacenter ]
		],
		'useGTIDs' => true
	],
	's7' => [
		'lagDetectionMethod' => 'pt-heartbeat',
		'lagDetectionOptions' => [
			'conds' => [ 'shard' => 's7', 'datacenter' => $wmfMasterDatacenter ]
		],
		'useGTIDs' => true
	],
	's8' => [
		'lagDetectionMethod' => 'pt-heartbeat',
		'lagDetectionOptions' => [
			'conds' => [ 'shard' => 's8', 'datacenter' => $wmfMasterDatacenter ]
		],
		'useGTIDs' => true
	],
],

'groupLoadsBySection' => [
	's1' => [
		'watchlist' => [
			'db2088:3311' => 1,
			'db2085:3311' => 1,
		],
		'recentchanges' => [
			'db2088:3311' => 1,
			'db2085:3311' => 1,
		],
		'recentchangeslinked' => [
			'db2088:3311' => 1,
			'db2085:3311' => 1,
		],
		'contributions' => [
			'db2088:3311' => 1,
			'db2085:3311' => 1,
		],
		'logpager' => [
			'db2088:3311' => 1,
			'db2085:3311' => 1,
		],
		'dump' => [
			'db2055' => 1,
		],
		'vslow' => [
			'db2055' => 1,
		],
		'api' => [
			'db2055' => 1,
			'db2071' => 10,
			'db2092' => 3,
		],
	],
	's2' => [
		'watchlist' => [
			'db2088:3312' => 1,
			'db2091:3312' => 1,
		],
		'recentchanges' => [
			'db2088:3312' => 1,
			'db2091:3312' => 1,
		],
		'recentchangeslinked' => [
			'db2088:3312' => 1,
			'db2091:3312' => 1,
		],
		'contributions' => [
			'db2088:3312' => 1,
			'db2091:3312' => 1,
		],
		'logpager' => [
			'db2088:3312' => 1,
			'db2091:3312' => 1,
		],
		'dump' => [
			'db2108' => 1,
		],
		'vslow' => [
			'db2108' => 1,
		],
		'api' => [
			'db2104' => 1,
			'db2125' => 1,
			'db2126' => 3,
		],
	],
	/* s3 */ 'DEFAULT' => [
		'dump' => [
			'db2109' => 1,
		],
		'vslow' => [
			'db2109' => 1,
		],
		'watchlist' => [
			'db2074' => 1,
		],
		'recentchanges' => [
			'db2074' => 1,
		],
		'recentchangeslinked' => [
			'db2074' => 1,
		],
		'contributions' => [
			'db2074' => 1,
		],
		'logpager' => [
			'db2074' => 1,
		],
	],
	's4' => [
		'watchlist' => [
			'db2084:3314' => 1,
			'db2091:3314' => 1,
		],
		'recentchanges' => [
			'db2084:3314' => 1,
			'db2091:3314' => 1,
		],
		'recentchangeslinked' => [
			'db2084:3314' => 1,
			'db2091:3314' => 1,
		],
		'contributions' => [
			'db2084:3314' => 1,
			'db2091:3314' => 1,
		],
		'logpager' => [
			'db2084:3314' => 1,
			'db2091:3314' => 1,
		],
		'dump' => [
			'db2073' => 1,
		],
		'vslow' => [
			'db2073' => 1,
		],
		'api' => [
			'db2106' => 3,
			'db2110' => 1,
		],
	],
	's5' => [
		'watchlist' => [
			'db2084:3315' => 1,
			'db2089:3315' => 1,
		],
		'recentchanges' => [
			'db2084:3315' => 1,
			'db2089:3315' => 1,
		],
		'recentchangeslinked' => [
			'db2084:3315' => 1,
			'db2089:3315' => 1,
		],
		'contributions' => [
			'db2084:3315' => 1,
			'db2089:3315' => 1,
		],
		'logpager' => [
			'db2084:3315' => 1,
			'db2089:3315' => 1,
		],
		'dump' => [
			'db2113' => 1,
		],
		'vslow' => [
			'db2113' => 1,
		],
		'api' => [
			'db2075' => 3,
			'db2128' => 1,
		],
	],
	's6' => [
		'watchlist' => [
			'db2087:3316' => 1,
			'db2089:3316' => 1,
		],
		'recentchanges' => [
			'db2087:3316' => 1,
			'db2089:3316' => 1,
		],
		'recentchangeslinked' => [
			'db2087:3316' => 1,
			'db2089:3316' => 1,
		],
		'contributions' => [
			'db2087:3316' => 1,
			'db2089:3316' => 1,
		],
		'logpager' => [
			'db2087:3316' => 1,
			'db2089:3316' => 1,
		],
		'dump' => [
			'db2117' => 1,
		],
		'vslow' => [
			'db2117' => 1,
		],
		'api' => [
			'db2076' => 1,
			'db2114' => 1,
		],
	],
	's7' => [
		'watchlist' => [
			'db2086:3317' => 1,
			'db2087:3317' => 1,
		],
		'recentchanges' => [
			'db2086:3317' => 1,
			'db2087:3317' => 1,
		],
		'recentchangeslinked' => [
			'db2086:3317' => 1,
			'db2087:3317' => 1,
		],
		'contributions' => [
			'db2086:3317' => 1,
			'db2087:3317' => 1,
		],
		'logpager' => [
			'db2086:3317' => 1,
			'db2087:3317' => 1,
		],
		'dump' => [
			'db2120' => 1,
		],
		'vslow' => [
			'db2120' => 1,
		],
		'api' => [
			'db2077' => 1,
			'db2121' => 1,
		],
	],
	's8' => [
		'watchlist' => [
			'db2085:3318' => 1,
			'db2086:3318' => 1,
		],
		'recentchanges' => [
			'db2085:3318' => 1,
			'db2086:3318' => 1,
		],
		'recentchangeslinked' => [
			'db2085:3318' => 1,
			'db2086:3318' => 1,
		],
		'contributions' => [
			'db2085:3318' => 1,
			'db2086:3318' => 1,
		],
		'logpager' => [
			'db2085:3318' => 1,
			'db2086:3318' => 1,
		],
		'dump' => [
			'db2082' => 1,
		],
		'vslow' => [
			'db2082' => 1,
		],
		'api' => [
			'db2080' => 1,
			'db2081' => 3,
		],
	],
],

'groupLoadsByDB' => [],

# Hosts settings
# Do not remove servers from this list ever
# Removing a server from this list does not remove the server from rotation,
# it just breaks the site horribly.
'hostsByName' => [
	'db1061' => '10.64.32.227', # do not remove or comment out
	'db1062' => '10.64.48.15', # do not remove or comment out
	'db1066' => '10.64.0.110', # do not remove or comment out
	'db1067' => '10.64.32.64', # do not remove or comment out
	'db1070' => '10.64.48.25', # do not remove or comment out
	'db1073' => '10.64.16.79', # do not remove or comment out
	'db1074' => '10.64.0.204', # do not remove or comment out
	'db1075' => '10.64.0.205', # do not remove or comment out
	'db1076' => '10.64.16.190', # do not remove or comment out
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
	'db1090:3312' => '10.64.32.116:3312', # do not remove or comment out
	'db1090:3317' => '10.64.32.116:3317', # do not remove or comment out
	'db1091' => '10.64.48.150', # do not remove or comment out
	'db1092' => '10.64.48.151', # do not remove or comment out
	'db1093' => '10.64.48.152', # do not remove or comment out
	'db1094' => '10.64.48.153', # do not remove or comment out
	'db1096:3315' => '10.64.0.163:3315', # do not remove or comment out
	'db1096:3316' => '10.64.0.163:3316', # do not remove or comment out
	'db1097:3314' => '10.64.48.11:3314', # do not remove or comment out
	'db1097:3315' => '10.64.48.11:3315', # do not remove or comment out
	'db1098:3316' => '10.64.16.83:3316', # do not remove or comment out
	'db1098:3317' => '10.64.16.83:3317', # do not remove or comment out
	'db1099:3311' => '10.64.16.84:3311', # do not remove or comment out
	'db1099:3318' => '10.64.16.84:3318', # do not remove or comment out
	'db1100' => '10.64.32.197', # do not remove or comment out
	'db1101:3317' => '10.64.32.198:3317', # do not remove or comment out
	'db1101:3318' => '10.64.32.198:3318', # do not remove or comment out
	'db1103:3312' => '10.64.0.164:3312', # do not remove or comment out
	'db1103:3314' => '10.64.0.164:3314', # do not remove or comment out
	'db1104' => '10.64.16.85', # do not remove or comment out
	'db1105:3311' => '10.64.32.222:3311', # do not remove or comment out
	'db1105:3312' => '10.64.32.222:3312', # do not remove or comment out
	'db1106' => '10.64.48.13', # do not remove or comment out
	'db1109' => '10.64.48.172', # do not remove or comment out
	'db1110' => '10.64.32.73', # do not remove or comment out
	'db1112' => '10.64.16.7', # do not remove or comment out
	'db1113:3315' => '10.64.16.11:3315', # do not remove or comment out
	'db1113:3316' => '10.64.16.11:3316', # do not remove or comment out
	'db1118' => '10.64.16.12', # do not remove or comment out
	'db1119' => '10.64.16.13', # do not remove or comment out
	'db1120' => '10.64.32.11', # do not remove or comment out
	'db1121' => '10.64.32.12', # do not remove or comment out
	'db1122' => '10.64.48.34', # do not remove or comment out
	'db1123' => '10.64.48.35', # do not remove or comment out
	'db1126' => '10.64.0.96', # do not remove or comment out
	'db1127' => '10.64.0.97', # do not remove or comment out
	'db1129' => '10.64.0.99', # do not remove or comment out
	'db1130' => '10.64.16.33', # do not remove or comment out
	'db1131' => '10.64.16.34', # do not remove or comment out
	'db1133' => '10.64.32.72', # do not remove or comment out
	'db1134' => '10.64.32.76', # do not remove or comment out
	'db1136' => '10.64.48.109', # do not remove or comment out
	'db1137' => '10.64.48.111', # do not remove or comment out
	'db1138' => '10.64.48.124', # do not remove or comment out
	'db2047' => '10.192.32.107', # do not remove or comment out
	'db2048' => '10.192.0.99', # do not remove or comment out
	'db2054' => '10.192.48.6', # do not remove or comment out
	'db2055' => '10.192.48.7', # do not remove or comment out
	'db2061' => '10.192.48.13', # do not remove or comment out
	'db2068' => '10.192.48.20', # do not remove or comment out
	'db2070' => '10.192.32.5', # do not remove or comment out
	'db2071' => '10.192.0.4', # do not remove or comment out
	'db2072' => '10.192.16.37', # do not remove or comment out
	'db2073' => '10.192.32.167', # do not remove or comment out
	'db2074' => '10.192.48.84', # do not remove or comment out
	'db2075' => '10.192.0.5', # do not remove or comment out
	'db2076' => '10.192.16.38', # do not remove or comment out
	'db2077' => '10.192.32.168', # do not remove or comment out
	'db2079' => '10.192.0.6', # do not remove or comment out
	'db2080' => '10.192.32.169', # do not remove or comment out
	'db2081' => '10.192.0.7', # do not remove or comment out
	'db2082' => '10.192.16.39', # do not remove or comment out
	'db2083' => '10.192.32.170', # do not remove or comment out
	'db2084:3314' => '10.192.48.86:3314', # do not remove or comment out
	'db2084:3315' => '10.192.48.86:3315', # do not remove or comment out
	'db2085:3311' => '10.192.0.8:3311', # do not remove or comment out
	'db2085:3318' => '10.192.0.8:3318', # do not remove or comment out
	'db2086:3318' => '10.192.16.40:3318', # do not remove or comment out
	'db2086:3317' => '10.192.16.40:3317', # do not remove or comment out
	'db2087:3316' => '10.192.32.171:3316', # do not remove or comment out
	'db2087:3317' => '10.192.32.171:3317', # do not remove or comment out
	'db2088:3311' => '10.192.48.87:3311', # do not remove or comment out
	'db2088:3312' => '10.192.48.87:3312', # do not remove or comment out
	'db2089:3315' => '10.192.0.9:3315', # do not remove or comment out
	'db2089:3316' => '10.192.0.9:3316', # do not remove or comment out
	'db2090' => '10.192.32.172', # do not remove or comment out
	'db2091:3312' => '10.192.0.10:3312', # do not remove or comment out
	'db2091:3314' => '10.192.0.10:3314', # do not remove or comment out
	'db2092' => '10.192.16.41', # do not remove or comment out
	'db2096' => '10.192.16.34', # do not remove or comment out
	'db2103' => '10.192.0.118', # do not remove or comment out
	'db2104' => '10.192.0.119', # do not remove or comment out
	'db2105' => '10.192.0.120', # do not remove or comment out
	'db2106' => '10.192.0.128', # do not remove or comment out
	'db2107' => '10.192.16.103', # do not remove or comment out
	'db2108' => '10.192.16.104', # do not remove or comment out
	'db2109' => '10.192.16.105', # do not remove or comment out
	'db2110' => '10.192.16.106', # do not remove or comment out
	'db2111' => '10.192.16.124', # do not remove or comment out
	'db2112' => '10.192.32.4', # do not remove or comment out
	'db2113' => '10.192.32.124', # do not remove or comment out
	'db2114' => '10.192.32.125', # do not remove or comment out
	'db2115' => '10.192.32.134', # do not remove or comment out
	'db2116' => '10.192.32.135', # do not remove or comment out
	'db2117' => '10.192.48.34', # do not remove or comment out
	'db2118' => '10.192.48.35', # do not remove or comment out
	'db2119' => '10.192.48.36', # do not remove or comment out
	'db2120' => '10.192.48.37', # do not remove or comment out
	'db2121' => '10.192.0.134', # do not remove or comment out
	'db2122' => '10.192.0.146', # do not remove or comment out
	'db2123' => '10.192.16.12', # do not remove or comment out
	'db2124' => '10.192.16.19', # do not remove or comment out
	'db2125' => '10.192.32.181', # do not remove or comment out
	'db2126' => '10.192.32.182', # do not remove or comment out
	'db2127' => '10.192.32.183', # do not remove or comment out
	'db2128' => '10.192.48.114', # do not remove or comment out
	'db2129' => '10.192.48.115', # do not remove or comment out
	'db2130' => '10.192.48.133', # do not remove or comment out
	'db2131' => '10.192.48.134', # do not remove or comment out
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
		'10.192.48.41'  => 1, # es2016, D1 11TB 128GB, master
		'10.192.0.141'  => 3, # es2014, A1 11TB 128GB
		'10.192.32.130' => 3, # es2015, C1 11TB 128GB
	],
	# es3
	'cluster25' => [
		'10.192.0.142'  => 1, # es2017, A6 11TB 128GB, master
		'10.192.16.172' => 3, # es2018, B6 11TB 128GB
		'10.192.48.42'  => 3, # es2019, D6 11TB 128GB
	],
	# ExtensionStore shard1
	'extension1' => [
		'10.192.16.34' => 0, # db2096, B4 3.5TB 512GB, master
		'10.192.32.134' => 1, # db2115, C6 5.2TB 512GB
		'10.192.48.134' => 1, # db2131, D8 5.2TB 512GB
	],
],

'masterTemplateOverrides' => [],

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

# This key must exist for the master switch script to work, which means comment and uncomment
# the individual shards, but leave the 'readOnlyBySection' => [ ], alone.
#
# When going read only, please change the comment to something appropiate (like a brief idea
# of what is happening, with a wiki link for further explanation. Avoid linking to external
# infrastructure if possible (IRC, other webpages) or infrastructure not prepared to absorve
# large traffic (phabricator) because they tend to collapse. A meta page would be appropiate.
#
# Also keep these read only messages if codfw is not the active dc, to prevent accidental writes
# getting trasmmitted from codfw to eqiad when the master dc is eqiad.
'readOnlyBySection' => [
	# 's1'      => 'This request is served by a passive datacenter. If you see this something is really wrong.',
	# 's2'      => 'This request is served by a passive datacenter. If you see this something is really wrong.',
	# 'DEFAULT' => 'This request is served by a passive datacenter. If you see this something is really wrong.', # s3
	# 's4'      => 'This request is served by a passive datacenter. If you see this something is really wrong.',
	# 's5'      => 'This request is served by a passive datacenter. If you see this something is really wrong.',
	# 's6'      => 'This request is served by a passive datacenter. If you see this something is really wrong.',
	# 's7'      => 'This request is served by a passive datacenter. If you see this something is really wrong.',
	# 's8'      => 'This request is served by a passive datacenter. If you see this something is really wrong.',
],

];

$wgDefaultExternalStore = [
	'DB://cluster24',
	'DB://cluster25',
];
