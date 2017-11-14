<?php
# WARNING: This file is publically viewable on the web. Do not put private data here.

require 'db-common.php';

$wmgOldExtTemplate = [
	'10.192.16.171' => 1, # es2011, B1 11TB 128GB
	'10.192.32.129' => 1, # es2012, C1 11TB 128GB
	'10.192.48.40'  => 1, # es2013, D1 11TB 128GB
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
		'db2048' => 0,   # C6 2.9TB 160GB, master
		# 'db2016' => 0,   # B6 2.9TB  96GB, old master
		'db2034' => 50,  # A5 2.9TB 160GB, rc, log
		'db2042' => 50,  # C6 2.9TB 160GB, rc, log
		'db2055' => 50,  # D6 3.3TB 160GB, dump (inactive), vslow, api
		'db2062' => 50,  # B5 3.3TB 160GB, api # mariadb 10.1
		'db2069' => 50,  # D6 3.3TB 160GB, api
		'db2070' => 400, # C5 3.3TB 160GB
		'db2071' => 50,  # A6 3.6TB 512GB, api
		'db2072' => 500, # B6 3.6TB 512GB, # mariadb 10.1
		'db2088:3311' => 1, # D1 3.3TB 512GB # rc, log: s1 and s2
		'db2092:3311' => 1, # B8 3.3TB 512GB # rc, log: s1 and s3
	],
	's2' => [
		'db2017' => 0,   # B6 2.9TB  96GB, master
		'db2035' => 50,  # C6 2.9TB 160GB, rc, log
		'db2041' => 100, # C6 2.9TB 160GB, api
		'db2049' => 400, # C6 2.9TB 160GB,
		'db2056' => 50,  # D6 3.3TB 160GB, dump (inactive), vslow #innodb compressed
		'db2063' => 100, # D6 3.3TB 160GB, api
		'db2064' => 400, # D6 3.3TB 160GB
		'db2088:3312' => 1, # D1 3.3TB 512GB # rc, log: s1 and s2
		'db2091:3312' => 1, # A8 3.3TB 512GB # rc, log: s2 and s4
	],
	/* s3 */ 'DEFAULT' => [
		'db2018' => 0,   # B6 2.9TB  96GB, master
		'db2036' => 50,  # C6 2.9TB 160GB, rc, log
		'db2043' => 50,  # C6 2.9TB 160GB, dump (inactive), vslow
		'db2050' => 150, # C6 2.9TB 160GB, api
		'db2057' => 400, # D6 3.3TB 160GB
		'db2074' => 400, # D6 3.3TB 512GB # InnoDB compressed
		'db2085:3313' => 1, # A5 3.3TB 512GB # rc, log: s3 and s5(s8)
		'db2092:3313' => 1, # B8 3.3TB 512GB # rc, log: s1 and s3
	],
	's4' => [
		'db2051' => 0,   # B8 2.9TB 160GB, master
		# 'db2019' => 0, # B6 2.9TB  96GB, old master
		'db2037' => 50,  # C6 2.9TB 160GB, rc, log
		'db2044' => 50,  # C6 2.9TB 160GB, rc, log
		'db2058' => 50,  # D6 3.3TB 160GB, dump (inactive), vslow
		'db2065' => 200, # D6 3.3TB 160GB, api
		'db2073' => 400, # C6 3.3TB 512GB # Compressed InnoDB
		'db2084:3314' => 1, # D6 3.3TB 512GB # rc, log: s4 and s5
		'db2091:3314' => 1, # A8 3.3TB 512GB # rc, log: s2 and s4
	],
	's5' => [
		'db2023' => 0,   # B6 2.9TB  96GB, master
		# 'db2038' => 50,  # C6 2.9TB 160GB, rc, log #T178359
		'db2045' => 400, # C6 2.9TB 160GB #temporary rc
		'db2052' => 50,  # D6 2.9TB 160GB, dump (inactive), vslow
		'db2059' => 100, # D6 3.3TB 160GB, api
		'db2066' => 400, # D6 3.3TB 160GB
		'db2075' => 400, # A1 3.3TB 512GB # Compressed InnoDB
		'db2084:3315' => 1, # D6 3.3TB 512GB # rc, log: s4 and s5
		'db2085:3315' => 1, # A5 3.3TB 512GB # rc, log: s3 and s5(s8)
		'db2086:3315' => 1, # B1 3.3TB 512GB # rc, log: s5 and s7
		'db2089:3315' => 1, # A3 3.3TB 512GB # rc, log: s6 and s5(s8)
	],
	's6' => [
		'db2028' => 0,   # B6  2.9TB  96GB, master
		'db2039' => 50,  # C6 2.9TB 160GB, rc, log
		'db2046' => 400, # C6 2.9TB 160GB
		'db2053' => 100, # D6 2.9TB 160GB, dump (inactive), vslow
		'db2060' => 100, # D6 3.3TB 160GB, api
		'db2067' => 400, # D6 3.3TB 160GB
		'db2076' => 400, # B1 3.3TB 512GB
		'db2087:3316' => 1, # C1 3.3TB 512GB # rc, log: s6 and s7
		'db2089:3316' => 1, # A3 3.3TB 512GB # rc, log: s6 and s5(s8)
	],
	's7' => [
		'db2029' => 0,   # B6 2.9TB  96GB, master
		'db2040' => 200, # C6 2.9TB 160GB, rc, log
		'db2047' => 400, # C6 2.9TB 160GB,
		'db2054' => 200, # D6 2.9TB 160GB, dump (inactive), vslow
		'db2061' => 200, # D6 3.3TB 160GB, api
		'db2068' => 300, # D6 3.3TB 160GB
		'db2077' => 400, # C1 3.3TB 512GB
		'db2086:3317' => 1, # B1 3.3TB 512GB # rc, log: s5 and s7
		'db2087:3317' => 1, # C1 3.3TB 512GB # rc, log: s6 and s7
	],
	's8' => [
		# 'db2079' => 400, # A5 3.3TB 512GB # Compressed InnoDB #T170662
		# 'db2080' => 400, # C5 3.3TB 512GB # Compressed InnoDB #T170662
		# 'db2081' => 400, # A6 3.3TB 512GB # Compressed InnoDB #T170662
		# 'db2082' => 400, # B6 3.3TB 512GB # Compressed InnoDB #T170662
		# 'db2083' => 400, # C6 3.3TB 512GB # Compressed InnoDB #T170662
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
			'db2034' => 1,
			'db2042' => 1,
			'db2088:3311' => 1,
			'db2092:3311' => 1,
		],
		'recentchanges' => [
			'db2034' => 1,
			'db2042' => 1,
			'db2088:3311' => 1,
			'db2092:3311' => 1,
		],
		'recentchangeslinked' => [
			'db2034' => 1,
			'db2042' => 1,
			'db2088:3311' => 1,
			'db2092:3311' => 1,
		],
		'contributions' => [
			'db2034' => 1,
			'db2042' => 1,
			'db2088:3311' => 1,
			'db2092:3311' => 1,
		],
		'logpager' => [
			'db2034' => 1,
			'db2042' => 1,
			'db2088:3311' => 1,
			'db2092:3311' => 1,
		],
		'dump' => [
			'db2055' => 1,
		],
		'vslow' => [
			'db2055' => 1,
		],
		'api' => [
			'db2055' => 1,
			'db2062' => 1,
			'db2069' => 1,
			'db2071' => 5,
		],
	],
	's2' => [
		'watchlist' => [
			'db2035' => 1,
			'db2088:3312' => 1,
			'db2091:3312' => 1,
		],
		'recentchanges' => [
			'db2035' => 1,
			'db2088:3312' => 1,
			'db2091:3312' => 1,
		],
		'recentchangeslinked' => [
			'db2035' => 1,
			'db2088:3312' => 1,
			'db2091:3312' => 1,
		],
		'contributions' => [
			'db2035' => 1,
			'db2088:3312' => 1,
			'db2091:3312' => 1,
		],
		'logpager' => [
			'db2035' => 1,
			'db2088:3312' => 1,
			'db2091:3312' => 1,
		],
		'dump' => [
			'db2056' => 1,
		],
		'vslow' => [
			'db2056' => 1,
		],
		'api' => [
			'db2041' => 1,
			'db2063' => 1,
		],
	],
	/* s3 */ 'DEFAULT' => [
		'watchlist' => [
			'db2036' => 1,
			'db2085:3313' => 1,
			'db2092:3313' => 1,
		],
		'recentchanges' => [
			'db2036' => 1,
			'db2085:3313' => 1,
			'db2092:3313' => 1,
		],
		'recentchangeslinked' => [
			'db2036' => 1,
			'db2085:3313' => 1,
			'db2092:3313' => 1,
		],
		'contributions' => [
			'db2036' => 1,
			'db2085:3313' => 1,
			'db2092:3313' => 1,
		],
		'logpager' => [
			'db2036' => 1,
			'db2085:3313' => 1,
			'db2092:3313' => 1,
		],
		'dump' => [
			'db2043' => 1,
		],
		'vslow' => [
			'db2043' => 1,
		],
		'api' => [
			'db2050' => 1,
		],
	],
	's4' => [
		'watchlist' => [
			'db2037' => 1,
			'db2044' => 1,
			'db2084:3314' => 1,
			'db2091:3314' => 1,
		],
		'recentchanges' => [
			'db2037' => 1,
			'db2044' => 1,
			'db2084:3314' => 1,
			'db2091:3314' => 1,
		],
		'recentchangeslinked' => [
			'db2037' => 1,
			'db2044' => 1,
			'db2084:3314' => 1,
			'db2091:3314' => 1,
		],
		'contributions' => [
			'db2037' => 1,
			'db2044' => 1,
			'db2084:3314' => 1,
			'db2091:3314' => 1,
		],
		'logpager' => [
			'db2037' => 1,
			'db2044' => 1,
			'db2084:3314' => 1,
			'db2091:3314' => 1,
		],
		'dump' => [
			'db2058' => 1,
		],
		'vslow' => [
			'db2058' => 1,
		],
		'api' => [
			'db2065' => 1,
		],
	],
	's5' => [
		'watchlist' => [
			'db2045' => 1,
			'db2084:3315' => 1,
			'db2085:3315' => 1,
			'db2086:3315' => 1,
			'db2089:3315' => 1,
		],
		'recentchanges' => [
			'db2045' => 1,
			'db2084:3315' => 1,
			'db2085:3315' => 1,
			'db2086:3315' => 1,
			'db2089:3315' => 1,
		],
		'recentchangeslinked' => [
			'db2045' => 1,
			'db2084:3315' => 1,
			'db2085:3315' => 1,
			'db2086:3315' => 1,
			'db2089:3315' => 1,
		],
		'contributions' => [
			'db2045' => 1,
			'db2084:3315' => 1,
			'db2085:3315' => 1,
			'db2086:3315' => 1,
			'db2089:3315' => 1,
		],
		'logpager' => [
			'db2045' => 1,
			'db2084:3315' => 1,
			'db2085:3315' => 1,
			'db2086:3315' => 1,
			'db2089:3315' => 1,
		],
		'dump' => [
			'db2052' => 1,
		],
		'vslow' => [
			'db2052' => 1,
		],
		'api' => [
			'db2059' => 1,
		],
	],
	's6' => [
		'watchlist' => [
			'db2039' => 1,
			'db2087:3316' => 1,
			'db2089:3316' => 1,
		],
		'recentchanges' => [
			'db2039' => 1,
			'db2087:3316' => 1,
			'db2089:3316' => 1,
		],
		'recentchangeslinked' => [
			'db2039' => 1,
			'db2087:3316' => 1,
			'db2089:3316' => 1,
		],
		'contributions' => [
			'db2039' => 1,
			'db2087:3316' => 1,
			'db2089:3316' => 1,
		],
		'logpager' => [
			'db2039' => 1,
			'db2087:3316' => 1,
			'db2089:3316' => 1,
		],
		'dump' => [
			'db2053' => 1,
		],
		'vslow' => [
			'db2053' => 1,
		],
		'api' => [
			'db2060' => 1,
		],
	],
	's7' => [
		'watchlist' => [
			'db2040' => 1,
			'db2086:3317' => 1,
			'db2087:3317' => 1,
		],
		'recentchanges' => [
			'db2040' => 1,
			'db2086:3317' => 1,
			'db2087:3317' => 1,
		],
		'recentchangeslinked' => [
			'db2040' => 1,
			'db2086:3317' => 1,
			'db2087:3317' => 1,
		],
		'contributions' => [
			'db2040' => 1,
			'db2086:3317' => 1,
			'db2087:3317' => 1,
		],
		'logpager' => [
			'db2040' => 1,
			'db2086:3317' => 1,
			'db2087:3317' => 1,
		],
		'dump' => [
			'db2054' => 1,
		],
		'vslow' => [
			'db2054' => 1,
		],
		'api' => [
			'db2061' => 1,
		],
	],
	's8' => [
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
		'dump' => [
		],
		'vslow' => [
		],
		'api' => [
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
		'10.192.48.41'  => 1, # es2016, D1 11TB 128GB, master
		'10.192.0.141'  => 3, # es2014, A1 11TB 128GB
		'10.192.32.130' => 3, # es2015, C1 11TB 128GB
	],
	# es3
	'cluster25' => [
		'10.192.16.172' => 1, # es2018, B6 11TB 128GB, master
		'10.192.0.142'  => 3, # es2017, A6 11TB 128GB
		'10.192.48.42'  => 3, # es2019, D6 11TB 128GB
	],
	# ExtensionStore shard1
	'extension1' => [
		'10.192.32.4' => 1, # db2033, C6 3.5TB 160GB, master
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
# Also keep these read only messages if codfw is not the active dc, to prevent accidental writes
# getting trasmmitted from codfw to eqiad when the master dc is eqiad.
'readOnlyBySection' => [
	's1'      => 'This request is served by a passive datacenter. If you see this something is really wrong.',
	's2'      => 'This request is served by a passive datacenter. If you see this something is really wrong.',
	'DEFAULT' => 'This request is served by a passive datacenter. If you see this something is really wrong.', # s3
	's4'      => 'This request is served by a passive datacenter. If you see this something is really wrong.',
	's5'      => 'This request is served by a passive datacenter. If you see this something is really wrong.',
	's6'      => 'This request is served by a passive datacenter. If you see this something is really wrong.',
	's7'      => 'This request is served by a passive datacenter. If you see this something is really wrong.',
],

];

$wmgParserCacheDBs = [
	'10.64.0.12'   => '10.192.16.170', # pc2004, B5 2.4TB 256GB
	'10.64.32.72'  => '10.192.32.128', # pc2005, C5 2.4TB 256GB
	'10.64.48.128' => '10.192.48.39',  # pc2006, D5 2.4TB 256GB
];
