<?php
# WARNING: This file is publically viewable on the web. Do not put private data here.

require('db-common.php')

$wmgOldExtTemplate = [
	'10.64.0.7'    => 1, # es1012, A2 11TB 128GB
	'10.64.32.185' => 1, # es1016, C2 11TB 128GB
	'10.64.48.115' => 1, # es1018, D1 11TB 128GB
];

$wgLBFactoryConf = [

'class' => 'LBFactoryMulti',

'sectionsByDB' => $sectionsByDB,

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
		'db1067' => 0,   # D1 2.8TB 160GB, old master
		'db1051' => 1,  # B3 2.8TB  96GB, watchlist, recentchanges, contributions, logpager
		'db1055' => 1,   # C2 2.8TB  96GB, watchlist, recentchanges, contributions, logpager
		'db1065' => 0,   # D1 2.8TB 160GB, vslow, dump, master for sanitarium
		'db1066' => 50,  # D1 2.8TB 160GB, api
		'db1073' => 50,  # B3 2.8TB 160GB, api
		'db1080' => 300, # A2 3.6TB 512GB, api
		'db1083' => 500, # B1 3.6TB 512GB
		'db1089' => 500, # C3 3.6TB 512GB
		'db1105:3311' => 1,   # C3 3.6TB 512GB # rc, log: s1 and s2
	],
	's2' => [
		'db1054' => 0,   # A3 2.8TB  96GB, master
		'db1021' => 0,   # B1 1.4TB  64GB, vslow, dump
		'db1060' => 1,   # C2 2.8TB  96GB, api #master for sanitarium #T153743
		'db1074' => 300, # A2 3.6TB 512GB, api
		'db1076' => 500, # B1 3.6TB 512GB
		'db1090' => 500, # C3 3.6TB 512GB
		'db1101' => 1,   # C2 3.6TB 512GB, watchlist, recentchanges, contributions, logpager, old master 2
		'db1103:3312' => 1,  # A3 3.6TB 512GB # rc, log: s2 and s4
		'db1105:3312' => 1,   # C3 3.6TB 512GB # rc, log: s1 and s2
	],
	/* s3 */ 'DEFAULT' => [
		'db1075' => 0,   # A2 3.6TB 512GB, master
		'db1044' => 0,   # B2 1.4TB  64GB, #Temporary master for db1095 - new sanitarium #T150802
		'db1072' => 0,  # B2 2.8TB 160GB, vslow, dump, old master
		'db1077' => 400, # B1 3.6TB 512GB, watchlist, recentchanges, contributions, logpager
		'db1078' => 500, # C3 3.6TB 512GB
	],
	's4' => [
		'db1068' => 0,   # D1 2.8TB 160GB, master
		'db1053' => 1,   # A2 2.8TB  96GB, watchlist, recentchanges, contributions, logpager
		'db1056' => 1,   # C2 2.8TB  96GB, watchlist, recentchanges, contributions, logpager
		'db1064' => 0,   # D1 2.8TB 160GB, vslow, dump #Master for db1095 - new sanitarium
		'db1081' => 300, # A2 3.6TB 512GB, api
		'db1084' => 500, # B1 3.6TB 512GB
		'db1091' => 500, # D2 3.6TB 512GB
		'db1097' => 1,   # D1 3.6TB 512GB, api, old master
		'db1103:3314' => 1,  # A3 3.6TB 512GB # rc, log: s2 and s4
	],
	's5' => [
		'db1070' => 1,   # D1 2.8TB 160GB, master
		'db1082' => 300, # A2 3.6TB 512GB, api
		'db1087' => 500, # C2 3.6TB 512GB
		'db1096' => 1,   # A6 3.6TB 512GB, watchlist, recentchanges, contributions, logpager
		'db1099' => 1,   # B2 3.6TB 512GB, watchlist, recentchanges, contributions, logpager
		# 'db1100' => 1,   # C2 3.6TB 512GB, old master #T174569
	],
	's6' => [
		'db1061' => 0,   # C3 2.8TB 128GB, master
		'db1030' => 0,   # B1 1.4TB  64GB, vslow, dump
		'db1085' => 300, # B3 3.6TB 512GB, api #master for db1102 (sanitarium 3) - T153743
		'db1088' => 500, # C2 3.6TB 512GB
		'db1093' => 500, # D2 3.6TB 512GB
		'db1098' => 1,   # B5 3.6TB 512GB, watchlist, recentchanges, contributions, logpager
	],
	's7' => [
		'db1062' => 0,   # D4 2.8TB 128GB, master
		'db1034' => 1,   # B2 1.4TB  64GB, watchlist, recentchanges, contributions, logpager
		# 'db1039' => 0,   # B2 1.4TB  64GB
		'db1069' => 0,   # D1 2.8TB 160GB, vslow, dump, old master
		'db1079' => 300, # A2 3.6TB 512GB, api #master for db1102 (sanitarium 3)
		'db1086' => 500, # B3 3.6TB 512GB, api
		'db1094' => 500, # D2 3.6TB 512GB
	],
	's8' => [
		'db1071' => 1,   # D1 2.8TB 160GB, master
		'db1092' => 500, # D2 3.6TB 512GB
		# 'db1104' => 100,  # B3 3.6TB 512GB #T174569
		# 'db1106' => 300,  # D3 3.6TB 512GB #T174569

	],
	'silver' => [
		'silver' => 1,
	],
	'labtestweb2001' => [
		'labtestweb2001' => 1,
	],
],

'groupLoadsBySection' => [
	's1' => [
		'watchlist' => [
			'db1051' => 2,
			'db1055' => 2,
			'db1105:3311' => 1,
		],
		'recentchanges' => [
			'db1051' => 1,
			'db1055' => 1,
			'db1105:3311' => 1,
		],
		'recentchangeslinked' => [
			'db1051' => 1,
			'db1055' => 1,
			# 'db1105:3311' => 1,
		],
		'contributions' => [
			'db1051' => 1,
			'db1055' => 1,
			# 'db1105:3311' => 1,
		],
		'logpager' => [
			'db1051' => 1,
			'db1055' => 1,
			# 'db1105:3311' => 1,
		],
		'dump' => [
			'db1065' => 1,
		],
		'vslow' => [
			'db1065' => 1,
		],
		'api' => [
			'db1080' => 1,
			'db1073' => 1,
			'db1066' => 1,
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
			'db1060' => 2,
			'db1074' => 1,
		],
		'watchlist' => [
			'db1101' => 2,
			'db1103:3312' => 2,
			'db1105:3312' => 1,
		],
		'recentchanges' => [
			'db1101' => 1,
			'db1103:3312' => 1,
			'db1105:3312' => 1,
		],
		'recentchangeslinked' => [
			'db1101' => 1,
			'db1103:3312' => 1,
			# 'db1105:3312' => 1,
		],
		'contributions' => [
			'db1101' => 1,
			'db1103:3312' => 1,
			# 'db1105:3312' => 1,
		],
		'logpager' => [
			'db1101' => 1,
			'db1103:3312' => 1,
			# 'db1105:3312' => 1,
		],
	],
	/* s3 */ 'DEFAULT' => [
		'vslow' => [
			'db1072' => 1,
		],
		'dump' => [
			'db1072' => 1,
		],
		'watchlist' => [
			'db1077' => 1,
		],
		'recentchanges' => [
			'db1077' => 1,
		],
		'recentchangeslinked' => [
			'db1077' => 1,
		],
		'contributions' => [
			'db1077' => 1,
		],
		'logpager' => [
			'db1077' => 1,
		],
	],
	's4' => [
		'vslow' => [
			'db1064' => 1,
		],
		'dump' => [
			'db1064' => 1,
		],
		'api' => [
			'db1081' => 1,
			'db1097' => 3,
		],
		'watchlist' => [
			'db1053' => 1,
			'db1056' => 1,
			'db1103:3314' => 1,
		],
		'recentchanges' => [
			'db1053' => 1,
			'db1056' => 1,
			'db1103:3314' => 1,
		],
		'recentchangeslinked' => [
			'db1053' => 1,
			'db1056' => 1,
			'db1103:3314' => 1,
		],
		'contributions' => [
			'db1053' => 1,
			'db1056' => 1,
			'db1103:3314' => 1,
		],
		'logpager' => [
			'db1053' => 1,
			'db1056' => 1,
			'db1103:3314' => 1,
		],
	],
	's5' => [
		'vslow' => [
			'db1070' => 1,
		],
		'dump' => [
			'db1070' => 1,
		],
		'api' => [
			'db1071' => 1,
			'db1082' => 1,
		],
		'watchlist' => [
			'db1096' => 1,
			'db1099' => 1,
		],
		'recentchanges' => [
			'db1096' => 1,
			'db1099' => 1,
		],
		'recentchangeslinked' => [
			'db1096' => 1,
			'db1099' => 1,
		],
		'contributions' => [
			'db1096' => 1,
			'db1099' => 1,
		],
		'logpager' => [
			'db1096' => 1,
			'db1099' => 1,
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
			'db1085' => 1,
		],
		'watchlist' => [
			'db1098' => 1,
		],
		'recentchanges' => [
			'db1098' => 1,
		],
		'recentchangeslinked' => [
			'db1098' => 1,
		],
		'contributions' => [
			'db1098' => 1,
		],
		'logpager' => [
			'db1098' => 1,
		],
	],
	's7' => [
		'vslow' => [
			'db1069' => 1,
		],
		'dump' => [
			'db1069' => 1,
		],
		'api' => [
			'db1079' => 100,
			'db1086' => 1,
		],
		'watchlist' => [
			'db1034' => 1,
		],
		'recentchanges' => [
			'db1034' => 1,
		],
		'recentchangeslinked' => [
			'db1034' => 1,
		],
		'contributions' => [
			'db1034' => 1,
		],
		'logpager' => [
			'db1034' => 1,
		],
	],
	's8' => [
		'vslow' => [
		],
		'dump' => [
		],
		'api' => [
		],
		'watchlist' => [
		],
		'recentchanges' => [
		],
		'recentchangeslinked' => [
		],
		'contributions' => [
		],
		'logpager' => [
		],
	],
],

'groupLoadsByDB' => [],

'serverTemplate' => $serverTemplate,

'templateOverridesBySection' => $templateOverridesBySection,

# Hosts settings
# Do not remove servers from this list ever
# Removing a server from this list does not remove the server from rotation,
# it just breaks the site horribly.
'hostsByName' => $hostsByName,

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
		'10.64.0.6'    => 0, # es1011, A2 11TB 128GB, master
		'10.64.16.186' => 1, # es1013, B1 11TB 128GB
		'10.64.32.184' => 1, # es1015, C2 11TB 128GB
	],
	# es3
	'cluster25' => [
		'10.64.16.187' => 0, # es1014, B1 11TB 128GB, master
		'10.64.48.114' => 1, # es1017, D1 11TB 128GB
		'10.64.48.116' => 1, # es1019, D8 11TB 128GB
	],
	# ExtensionStore shard1
	'extension1' => [
		'10.64.16.20' => 0, # db1031, B1 1.5TB 64GB, master
		'10.64.16.18' => 1, # db1029, B1 1.5TB 64GB
	],
],

'masterTemplateOverrides' => $masterTemplateOverrides,

'externalTemplateOverrides' => $externalTemplateOverrides,

'templateOverridesByCluster' => $templateOverridesByCluster,

# This key must exist for the master switch script to work, which means comment and uncomment
# the individual shards, but leave the 'readOnlyBySection' => [ ], alone.
#
# When going read only, please change the comment to something appropiate (like a brief idea
# of what is happening, with a wiki link for further explanation. Avoid linking to external
# infrastructure if possible (IRC, other webpages) or infrastructure not prepared to absorve
# large traffic (phabricator) because they tend to collapse. A meta page would be appropiate.
#
# Also keep these read only messages if eqiad is not the active dc, to prevent accidental writes
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

$wmgParserCacheDBs = [
	'10.64.0.12'   => '10.64.0.12',   # pc1004, A3 2.4TB 256GB
	'10.64.32.72'  => '10.64.32.72',  # pc1005, C7 2.4TB 256GB
	'10.64.48.128' => '10.64.48.128', # pc1006, D3 2.4TB 256GB
];

