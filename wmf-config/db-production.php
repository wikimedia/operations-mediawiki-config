<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

if ( !defined( 'DBO_DEFAULT' ) ) {
	define( 'DBO_DEFAULT', 16 );
}

# $wgReadOnly = "Wikimedia Sites are currently read-only during maintenance, please try again soon.";

# LOOKING FOR $wmgOldExtTemplate ?  It no longer lives in the PHP configs.
# Instead try https://noc.wikimedia.org/dbconfig/eqiad.json (see 'es1')
# and https://noc.wikimedia.org/dbconfig/codfw.json
# For more info see also https://wikitech.wikimedia.org/wiki/dbctl

# If a wiki's section is different across datacenters, use a ternary to vary:
# 'xxwiki' => $wmgDatacenter === 'codfw' ? 's1': 's2',
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

	# s5: dewiki and some other wikis
	'altwiki'       => 's5',
	'amiwiki'       => 's5',
	'anpwiki'       => 's5',
	'apiportalwiki' => 's5',
	'arbcom_ruwiki' => 's5',
	'aswikiquote'   => 's5',
	'avkwiki'       => 's5',
	'azwikimedia'   => 's5',
	'banwikisource' => 's5',
	'bbcwiki'       => 's5',
	'bclwiktionary' => 's5',
	'bclwikiquote'  => 's5',
	'bjnwikiquote'  => 's5',
	'bjnwiktionary' => 's5',
	'blkwiki'       => 's5',
	'blkwiktionary' => 's5',
	'bnwikiquote'   => 's5',
	'btmwiktionary' => 's5',
	'cebwiki'       => 's5',
	'ckbwiktionary' => 's5',
	'dagwiki'       => 's5',
	'dewiki'        => 's5',
	'dgawiki'       => 's5',
	'diqwiktionary' => 's5',
	'enwikivoyage'  => 's5',
	'eowikivoyage'  => 's5',
	'fatwiki'       => 's5',
	'fonwiki'       => 's5',
	'gorwiktionary' => 's5',
	'gpewiki'       => 's5',
	'gucwiki'       => 's5',
	'gurwiki'       => 's5',
	'guwwiki'       => 's5',
	'guwwiktionary' => 's5',
	'guwwikiquote'  => 's5',
	'guwwikinews'   => 's5',
	'igwikiquote'   => 's5',
	'igwiktionary'  => 's5',
	'kbdwiktionary' => 's5',
	'kcgwiki'       => 's5',
	'kcgwiktionary' => 's5',
	'jawikivoyage'  => 's5',
	'jvwikisource'  => 's5',
	'lldwiki'       => 's5',
	'lmowiktionary' => 's5',
	'mniwiki'       => 's5',
	'mniwiktionary' => 's5',
	'mnwwiktionary' => 's5',
	'niawiki'       => 's5',
	'niawiktionary' => 's5',
	'madwiki'       => 's5',
	'mgwiktionary'  => 's5',
	'mhwiktionary'  => 's5',
	'muswiki'       => 's5',
	'pcmwiki'       => 's5',
	'pwnwiki'       => 's5',
	'skrwiki'       => 's5',
	'skrwiktionary' => 's5',
	'shnwikibooks'  => 's5',
	'shnwikivoyage' => 's5',
	'shwiki'        => 's5',
	'shiwiki'       => 's5',
	'smnwiki'       => 's5',
	'srwiki'        => 's5',
	'suwikisource'  => 's5',
	'taywiki'       => 's5',
	'thankyouwiki'  => 's5',
	'tlwikiquote'   => 's5',
	'tlywiki'       => 's5',
	'trwikivoyage'  => 's5',
	'trvwiki'       => 's5',
	'vewikimedia'   => 's5',
	'wikifunctionswiki'  => 's5',
	'wawikisource'  => 's5',
	'zghwiki'       => 's5',

	# s6: large wikis + wikitech
	'frwiki'       => 's6',
	'jawiki'       => 's6',
	'labswiki'     => 's6',
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

	# Wikitech test wiki
	'labtestwiki'  => 's11',
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
#
# LOOKING FOR THE LOAD LISTS?  They no longer live in the PHP configs.
# Instead try https://noc.wikimedia.org/db.php?dc=eqiad,
# https://noc.wikimedia.org/db.php?dc=codfw,
# https://noc.wikimedia.org/dbconfig/eqiad.json
# https://noc.wikimedia.org/dbconfig/codfw.json
# For more info see also https://wikitech.wikimedia.org/wiki/dbctl

'serverTemplate' => [
	'dbname'             => $wgDBname,
	'user'               => $wgDBuser,
	'password'           => $wgDBpassword,
	'type'               => 'mysql',
	'flags'              => DBO_DEFAULT | ( $wgDebugDumpSql ? DBO_DEBUG : 0 ),
	// should be safely less than $wgCdnReboundPurgeDelay
	'max lag'            => 6,
	'useGTIDs'           => true,
	'lagDetectionMethod' => 'pt-heartbeat',
	'variables'          => [
		'innodb_lock_wait_timeout' => 15
	],
],

'templateOverridesBySection' => [
	's1' => [
		'lagDetectionOptions' => [
			'conds' => [ 'shard' => 's1', 'datacenter' => $wmgMasterDatacenter ]
		],
	],
	's2' => [
		'lagDetectionOptions' => [
			'conds' => [ 'shard' => 's2', 'datacenter' => $wmgMasterDatacenter ]
		],
	],
	// Default is s3
	'DEFAULT' => [
		'lagDetectionOptions' => [
			'conds' => [ 'shard' => 's3', 'datacenter' => $wmgMasterDatacenter ]
		],
	],
	's4' => [
		'lagDetectionOptions' => [
			'conds' => [ 'shard' => 's4', 'datacenter' => $wmgMasterDatacenter ]
		],
	],
	's5' => [
		'lagDetectionOptions' => [
			'conds' => [ 'shard' => 's5', 'datacenter' => $wmgMasterDatacenter ]
		],
	],
	's6' => [
		'lagDetectionOptions' => [
			'conds' => [ 'shard' => 's6', 'datacenter' => $wmgMasterDatacenter ]
		],
	],
	's7' => [
		'lagDetectionOptions' => [
			'conds' => [ 'shard' => 's7', 'datacenter' => $wmgMasterDatacenter ]
		],
	],
	's8' => [
		'lagDetectionOptions' => [
			'conds' => [ 'shard' => 's8', 'datacenter' => $wmgMasterDatacenter ]
		],
	],
],

# LOOKING FOR GROUP LOADS?  They no longer live in the PHP configs.
# Instead try https://noc.wikimedia.org/dbconfig/eqiad.json
# and https://noc.wikimedia.org/dbconfig/codfw.json
# For more info see also https://wikitech.wikimedia.org/wiki/dbctl

'groupLoadsByDB' => [],

# LOOKING FOR HOSTS BY NAME?  They no longer live in the PHP configs.
# Instead try https://noc.wikimedia.org/dbconfig/eqiad.json
# and https://noc.wikimedia.org/dbconfig/codfw.json
# For more info see also https://wikitech.wikimedia.org/wiki/dbctl
'hostsByName' => [],

# LOOKING FOR EXTERNAL LOADS?  They no longer live in the PHP configs.
# Instead try https://noc.wikimedia.org/dbconfig/eqiad.json (see es1/es2/es3/x1)
# and https://noc.wikimedia.org/dbconfig/codfw.json
# For more info see also https://wikitech.wikimedia.org/wiki/dbctl
'externalLoads' => [],

'masterTemplateOverrides' => [
	'ssl' => $wmgDatacenter !== $wmgMasterDatacenter
],

'externalTemplateOverrides' => [
	// No transactions
	'flags' => $wgDebugDumpSql ? DBO_DEBUG : 0,
	// no pt-heartbeat
	'lagDetectionMethod' => 'Seconds_Behind_Master',
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
	'cluster24'	=> [ 'blobs table' => 'blobs_cluster24', 'is static' => true ],
	'cluster25'	=> [ 'blobs table' => 'blobs_cluster25', 'is static' => true ],
	'cluster26'	=> [ 'blobs table' => 'blobs_cluster26', 'is static' => true ],
	'cluster27'	=> [ 'blobs table' => 'blobs_cluster27', 'is static' => true ],
	'cluster28'	=> [ 'blobs table' => 'blobs_cluster28' ],
	'cluster29'	=> [ 'blobs table' => 'blobs_cluster29' ],
],

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
# LOOKING FOR READONLY SECTIONS?  They no longer live in the PHP configs.
# Instead try https://noc.wikimedia.org/dbconfig/eqiad.json
# and https://noc.wikimedia.org/dbconfig/codfw.json
# For more info see also https://wikitech.wikimedia.org/wiki/dbctl
],

];

$wgDefaultExternalStore = [
	'DB://cluster28',
	'DB://cluster29',
];
