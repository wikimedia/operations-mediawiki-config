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

# This gets overriden by db-sections.php
'sectionsByDB' => [],

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
	// bump threshold for circuit breaking
	'loadMonitor' => [ 'class' => '\Wikimedia\Rdbms\LoadMonitor', 'maxConnCount' => 500 ],
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
	'cluster28'	=> [ 'blobs table' => 'blobs_cluster28', 'is static' => true ],
	'cluster29'	=> [ 'blobs table' => 'blobs_cluster29', 'is static' => true ],
	'cluster30'	=> [ 'blobs table' => 'blobs_cluster30' ],
	'cluster31'	=> [ 'blobs table' => 'blobs_cluster31' ],
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
	'DB://cluster30',
	'DB://cluster31',
];
