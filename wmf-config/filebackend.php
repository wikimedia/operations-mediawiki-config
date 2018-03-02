<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

# This file hold the configuration for the file backends.
#
# NOTE: Included based on wmgUseClusterFileBackend (all wikis, except wikitech)
#
# Load tree:
#  |-- wmf-config/CommonSettings.php
#      |
#      `-- wmf-config/filebackend.php
#

global $wmfSwiftConfig;
// Common OpenStack Swift backend convenience variables
$wmfSwiftBigWikis = [ # DO NOT change without proper migration first
	'commonswiki', 'dewiki', 'enwiki', 'fiwiki', 'frwiki', 'hewiki', 'huwiki', 'idwiki',
	'itwiki', 'jawiki', 'rowiki', 'ruwiki', 'thwiki', 'trwiki', 'ukwiki', 'zhwiki'
];
$wmfSwiftShardLocal = in_array( $wgDBname, $wmfSwiftBigWikis ) ? 2 : 0; // shard levels
$wmfSwiftShardCommon = in_array( 'commonswiki', $wmfSwiftBigWikis ) ? 2 : 0; // shard levels

if ( $wmfRealm === 'labs' ) {
	$datacenters = [ 'eqiad' ];
	$redisLockServers = [ 'rdb1', 'rdb2' ];
	$commonsUrl = "//commons.wikimedia.beta.wmflabs.org";
	$uploadUrl = "//upload.beta.wmflabs.org";
} else {
	$datacenters = [ 'eqiad', 'codfw' ];
	$redisLockServers = [ 'rdb1', 'rdb2', 'rdb3' ];
	$commonsUrl = "https://commons.wikimedia.org";
	$uploadUrl = "//upload.wikimedia.org";
}

/* DC-specific Swift backend config */
foreach ( $datacenters as $specificDC ) {
	$wgFileBackends[] = [ // backend config for wiki's local repo
		'class'              => 'SwiftFileBackend',
		'name'               => "local-swift-{$specificDC}",
		'wikiId'             => "{$site}-{$lang}",
		'lockManager'        => 'redisLockManager',
		'swiftAuthUrl'       => $wmfAllServices[$specificDC]['mediaSwiftAuth'],
		'swiftStorageUrl'    => $wmfAllServices[$specificDC]['mediaSwiftStore'],
		'swiftUser'          => $wmfSwiftConfig[$specificDC]['user'],
		'swiftKey'           => $wmfSwiftConfig[$specificDC]['key'],
		'swiftTempUrlKey'    => $wmfSwiftConfig[$specificDC]['tempUrlKey'],
		'shardViaHashLevels' => [
			'local-public'
				=> [ 'levels' => $wmfSwiftShardLocal, 'base' => 16, 'repeat' => 1 ],
			'local-thumb'
				=> [ 'levels' => $wmfSwiftShardLocal, 'base' => 16, 'repeat' => 1 ],
			'local-temp'
				=> [ 'levels' => $wmfSwiftShardLocal, 'base' => 16, 'repeat' => 1 ],
			'local-transcoded'
				=> [ 'levels' => $wmfSwiftShardLocal, 'base' => 16, 'repeat' => 1 ],
			'local-deleted'
				=> [ 'levels' => $wmfSwiftShardLocal, 'base' => 36, 'repeat' => 0 ]
		],
		'parallelize'        => 'implicit',
		'cacheAuthInfo'      => true,
		// When used by FileBackendMultiWrite, read from this cluster if it's the local one
		'readAffinity'       => ( $specificDC === $wmfDatacenter ),
		'readUsers'           => [ $wmfSwiftConfig[$specificDC]['thumborUser'] ],
		'writeUsers'          => [ $wmfSwiftConfig[$specificDC]['thumborUser'] ],
		'secureReadUsers'     => [ $wmfSwiftConfig[$specificDC]['thumborPrivateUser'] ],
		'secureWriteUsers'    => [ $wmfSwiftConfig[$specificDC]['thumborPrivateUser'] ]
	];
	$wgFileBackends[] = [ // backend config for wiki's access to shared repo
		'class'              => 'SwiftFileBackend',
		'name'               => "shared-swift-{$specificDC}",
		'wikiId'             => "wikipedia-commons",
		'lockManager'        => 'redisLockManager',
		'swiftAuthUrl'       => $wmfAllServices[$specificDC]['mediaSwiftAuth'],
		'swiftStorageUrl'    => $wmfAllServices[$specificDC]['mediaSwiftStore'],
		'swiftUser'          => $wmfSwiftConfig[$specificDC]['user'],
		'swiftKey'           => $wmfSwiftConfig[$specificDC]['key'],
		'swiftTempUrlKey'    => $wmfSwiftConfig[$specificDC]['tempUrlKey'],
		'shardViaHashLevels' => [
			'local-public'
				=> [ 'levels' => $wmfSwiftShardCommon, 'base' => 16, 'repeat' => 1 ],
			'local-thumb'
				=> [ 'levels' => $wmfSwiftShardCommon, 'base' => 16, 'repeat' => 1 ],
			'local-temp'
				=> [ 'levels' => $wmfSwiftShardCommon, 'base' => 16, 'repeat' => 1 ],
			'local-transcoded'
				=> [ 'levels' => $wmfSwiftShardCommon, 'base' => 16, 'repeat' => 1 ],
		],
		'parallelize'        => 'implicit',
		'cacheAuthInfo'      => true,
		// When used by FileBackendMultiWrite, read from this cluster if it's the local one
		'readAffinity'       => ( $specificDC === $wmfDatacenter ),
		'readUsers'           => [ $wmfSwiftConfig[$specificDC]['thumborUser'] ],
		'writeUsers'          => [ $wmfSwiftConfig[$specificDC]['thumborUser'] ],
		'secureReadUsers'     => [ $wmfSwiftConfig[$specificDC]['thumborPrivateUser'] ],
		'secureWriteUsers'    => [ $wmfSwiftConfig[$specificDC]['thumborPrivateUser'] ]
	];
	$wgFileBackends[] = [ // backend config for wiki's access to shared files
		'class'              => 'SwiftFileBackend',
		'name'               => "global-swift-{$specificDC}",
		'wikiId'             => "global-data",
		'lockManager'        => 'redisLockManager',
		'swiftAuthUrl'       => $wmfAllServices[$specificDC]['mediaSwiftAuth'],
		'swiftStorageUrl'    => $wmfAllServices[$specificDC]['mediaSwiftStore'],
		'swiftUser'          => $wmfSwiftConfig[$specificDC]['user'],
		'swiftKey'           => $wmfSwiftConfig[$specificDC]['key'],
		'swiftTempUrlKey'    => $wmfSwiftConfig[$specificDC]['tempUrlKey'],
		'shardViaHashLevels' => [
			'math-render'  => [ 'levels' => 2, 'base' => 16, 'repeat' => 0 ],
		],
		'parallelize'        => 'implicit',
		'cacheAuthInfo'      => true,
		// When used by FileBackendMultiWrite, read from this cluster if it's the local one
		'readAffinity'       => ( $specificDC === $wmfDatacenter ),
		'readUsers'           => [ $wmfSwiftConfig[$specificDC]['thumborUser'] ],
		'writeUsers'          => [ $wmfSwiftConfig[$specificDC]['thumborUser'] ],
		'secureReadUsers'     => [ $wmfSwiftConfig[$specificDC]['thumborPrivateUser'] ],
		'secureWriteUsers'    => [ $wmfSwiftConfig[$specificDC]['thumborPrivateUser'] ]
	];
	$wgFileBackends[] = [ // backend config for wiki's access to shared test repo
		'class'              => 'SwiftFileBackend',
		'name'               => "shared-testwiki-swift-{$specificDC}",
		'wikiId'             => "wikipedia-test",
		'lockManager'        => 'redisLockManager',
		'swiftAuthUrl'       => $wmfAllServices[$specificDC]['mediaSwiftAuth'],
		'swiftStorageUrl'    => $wmfAllServices[$specificDC]['mediaSwiftStore'],
		'swiftUser'          => $wmfSwiftConfig[$specificDC]['user'],
		'swiftKey'           => $wmfSwiftConfig[$specificDC]['key'],
		'swiftTempUrlKey'    => $wmfSwiftConfig[$specificDC]['tempUrlKey'],
		'parallelize'        => 'implicit',
		'cacheAuthInfo'      => true,
		// When used by FileBackendMultiWrite, read from this cluster if it's the local one
		'readAffinity'       => ( $specificDC === $wmfDatacenter ),
		'readUsers'           => [ $wmfSwiftConfig[$specificDC]['thumborUser'] ],
		'writeUsers'          => [ $wmfSwiftConfig[$specificDC]['thumborUser'] ],
		'secureReadUsers'     => [ $wmfSwiftConfig[$specificDC]['thumborPrivateUser'] ],
		'secureWriteUsers'    => [ $wmfSwiftConfig[$specificDC]['thumborPrivateUser'] ]
	];
}
/* end DC-specific Swift backend config */

/* Common multiwrite backend config */
$localMultiWriteFileBackend = [
	'class'       => 'FileBackendMultiWrite',
	'name'        => 'local-multiwrite',
	'wikiId'      => "{$site}-{$lang}",
	'lockManager' => 'redisLockManager',
	# DO NOT change the master backend unless it is fully trusted or autoRsync is off
	'backends'    => [
		[ 'template' => 'local-swift-eqiad', 'isMultiMaster' => true ],
	],
	'replication' => 'sync', // read-after-update for assets
	'syncChecks'  => ( 1 | 4 ), // (size & sha1)
	'autoResync'  => 'conservative'
];
$sharedMultiwriteFileBackend = [
	'class'       => 'FileBackendMultiWrite',
	'name'        => 'shared-multiwrite',
	'wikiId'      => "wikipedia-commons",
	'lockManager' => 'redisLockManager',
	# DO NOT change the master backend unless it is fully trusted or autoRsync is off
	'backends'    => [
		[ 'template' => 'shared-swift-eqiad', 'isMultiMaster' => true ],
	],
	'replication' => 'sync', // read-after-update for assets
	'syncChecks'  => ( 1 | 4 ), // (size & sha1)
];
$globalMultiWriteFileBackend = [
	'class'       => 'FileBackendMultiWrite',
	'name'        => 'global-multiwrite',
	'wikiId'      => "global-data",
	'lockManager' => 'redisLockManager',
	# DO NOT change the master backend unless it is fully trusted or autoRsync is off
	'backends'    => [
		[ 'template' => 'global-swift-eqiad', 'isMultiMaster' => true ],
	],
	'replication' => 'sync', // read-after-update for assets
	'syncChecks'  => ( 1 | 4 ) // (size & sha1)
];
$sharedTestwikiMultiWriteFileBackend = [
	'class'       => 'FileBackendMultiWrite',
	'name'        => 'shared-testwiki-multiwrite',
	'wikiId'      => "wikipedia-test",
	'lockManager' => 'redisLockManager',
	# DO NOT change the master backend unless it is fully trusted or autoRsync is off
	'backends'    => [
		[ 'template' => 'shared-testwiki-swift-eqiad', 'isMultiMaster' => true ],
	],
	'replication' => 'sync', // read-after-update for assets
	'syncChecks'  => ( 1 | 4 ), // (size & sha1)
];

if ( in_array( 'codfw', $datacenters ) ) {
	$localMultiWriteFileBackend['backends'][] = [ 'template' => 'local-swift-codfw' ];
	$sharedMultiwriteFileBackend['backends'][] = [ 'template' => 'shared-swift-codfw' ];
	$globalMultiWriteFileBackend['backends'][] = [ 'template' => 'global-swift-codfw' ];
	$sharedTestwikiMultiWriteFileBackend['backends'][] = [ 'template' => 'shared-testwiki-swift-codfw' ];
}
$wgFileBackends[] = $localMultiWriteFileBackend;
$wgFileBackends[] = $sharedMultiwriteFileBackend;
$wgFileBackends[] = $globalMultiWriteFileBackend;
$wgFileBackends[] = $sharedTestwikiMultiWriteFileBackend;

/* end multiwrite backend config */

// Lock manager config must use the master datacenter
$wgLockManagers[] = [
	'name'         => 'redisLockManager',
	'class'        => 'RedisLockManager',
	'lockServers'  => $wmfMasterServices['redis_lock'],
	'srvsByBucket' => [
		0 => $redisLockServers
	],
	'redisConfig'  => [
		'connectTimeout' => 2,
		'readTimeout'    => 2,
		'password'       => $wmgRedisPassword
	]
];

$wgLocalFileRepo = [
	'class'             => 'LocalRepo',
	'name'              => 'local',
	'backend'           => 'local-multiwrite',
	'url'               => $wgUploadBaseUrl ? $wgUploadBaseUrl . $wgUploadPath : $wgUploadPath,
	'scriptDirUrl'      => $wgScriptPath,
	'hashLevels'        => 2,
	'thumbScriptUrl'    => $wgThumbnailScriptPath,
	'transformVia404'   => true,
	'initialCapital'    => $wgCapitalLinks,
	'deletedHashLevels' => 3,
	'abbrvThreshold'    => 160,
	'isPrivate'         => $wmgPrivateWiki,
	'thumbProxyUrl'     => $wmfSwiftConfig[$specificDC]['thumborUrl'] . '/' . $site . '/' . $lang . '/thumb/',
	'thumbProxySecret'  => $wmfSwiftConfig[$specificDC]['thumborSecret'],
	'zones'             => $wmgPrivateWiki
		? [
			'thumb' => [ 'url' => "$wgScriptPath/thumb_handler.php" ] ]
		: [],
];
// test2wiki uses testwiki as foreign file repo (e.g. local => testwiki => commons)
// Does not exist in labs.
if ( $wgDBname === 'test2wiki' ) {
	$wgForeignFileRepos[] = [
		'class'            => 'ForeignDBViaLBRepo',
		'name'             => 'testwikirepo',
		'backend'          => 'shared-testwiki-multiwrite',
		'url'              => "{$uploadUrl}/wikipedia/test",
		'hashLevels'       => 2,
		'thumbScriptUrl'   => false,
		'transformVia404'  => true,
		'hasSharedCache'   => true,
		'descBaseUrl'      => "https://test.wikipedia.org/wiki/File:",
		'scriptDirUrl'     => "https://test.wikipedia.org/w",
		'favicon'          => "/static/favicon/black-globe.ico",
		'fetchDescription' => true,
		'descriptionCacheExpiry' => 86400 * 7,
		'wiki'             => 'testwiki',
		'initialCapital'   => true,
		'zones'            => [ // actual swift containers have 'local-*'
			'public'  => [ 'container' => 'local-public' ],
			'thumb'   => [ 'container' => 'local-thumb' ],
			'temp'    => [ 'container' => 'local-temp' ],
			'deleted' => [ 'container' => 'local-deleted' ]
		],
		'abbrvThreshold'   => 160 /* Keep in sync with with local repo on testwiki or things break. */
	];
}
if ( $wgDBname != 'commonswiki' && $wgDBname != 'labswiki' ) {
	// Commons is local to commonswiki :)
	// wikitech uses $wgUseInstantCommons instead of db access.
	$wgForeignFileRepos[] = [
		'class'            => 'ForeignDBViaLBRepo',
		'name'             => 'shared',
		'backend'          => 'shared-multiwrite',
		'url'              => "{$uploadUrl}/wikipedia/commons",
		'hashLevels'       => 2,
		'thumbScriptUrl'   => false,
		'transformVia404'  => true,
		'hasSharedCache'   => true,
		'descBaseUrl'      => "{$commonsUrl}/wiki/File:",
		'scriptDirUrl'     => "{$commonsUrl}/w",
		'favicon'          => "/static/favicon/commons.ico",
		'fetchDescription' => true,
		'descriptionCacheExpiry' => 86400 * 7,
		'wiki'             => 'commonswiki',
		'initialCapital'   => true,
		'zones'            => [ // actual swift containers have 'local-*'
			'public'  => [ 'container' => 'local-public' ],
			'thumb'   => [ 'container' => 'local-thumb' ],
			'temp'    => [ 'container' => 'local-temp' ],
			'deleted' => [ 'container' => 'local-deleted' ]
		],
		'abbrvThreshold'   => 160 /* Keep in sync with with local repo on commons or things break. */
	];
}
