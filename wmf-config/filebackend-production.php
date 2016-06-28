<?php

# WARNING: This file is publically viewable on the web. Do not put private data here.

#
# This file hold the configuration for the file backends for production.

// Common OpenStack Swift backend convenience variables
$wmfSwiftBigWikis = [ # DO NOT change without proper migration first
	'commonswiki', 'dewiki', 'enwiki', 'fiwiki', 'frwiki', 'hewiki', 'huwiki', 'idwiki',
	'itwiki', 'jawiki', 'rowiki', 'ruwiki', 'thwiki', 'trwiki', 'ukwiki', 'zhwiki'
];
$wmfSwiftShardLocal = in_array( $wgDBname, $wmfSwiftBigWikis ) ? 2 : 0; // shard levels
$wmfSwiftShardCommon = in_array( 'commonswiki', $wmfSwiftBigWikis ) ? 2 : 0; // shard levels

/* DC-specific Swift backend config */
foreach ( [ 'eqiad', 'codfw' ] as $specificDC ) {
	$wgFileBackends[] = [ // backend config for wiki's local repo
		'class'              => 'SwiftFileBackend',
		'name'               => "local-swift-{$specificDC}",
		'wikiId'             => "{$site}-{$lang}",
		'lockManager'        => 'redisLockManager',
		'swiftAuthUrl'       => $wmfSwiftConfig[$specificDC]['authUrl'],
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
		'readAffinity'       => ( $specificDC === $wmfDatacenter )
	];
	$wgFileBackends[] = [ // backend config for wiki's access to shared repo
		'class'              => 'SwiftFileBackend',
		'name'               => "shared-swift-{$specificDC}",
		'wikiId'             => "wikipedia-commons",
		'lockManager'        => 'redisLockManager',
		'swiftAuthUrl'       => $wmfSwiftConfig[$specificDC]['authUrl'],
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
		'readAffinity'       => ( $specificDC === $wmfDatacenter )
	];
	$wgFileBackends[] = [ // backend config for wiki's access to shared files
		'class'              => 'SwiftFileBackend',
		'name'               => "global-swift-{$specificDC}",
		'wikiId'             => "global-data",
		'lockManager'        => 'redisLockManager',
		'swiftAuthUrl'       => $wmfSwiftConfig[$specificDC]['authUrl'],
		'swiftUser'          => $wmfSwiftConfig[$specificDC]['user'],
		'swiftKey'           => $wmfSwiftConfig[$specificDC]['key'],
		'swiftTempUrlKey'    => $wmfSwiftConfig[$specificDC]['tempUrlKey'],
		'shardViaHashLevels' => [
			'math-render'  => [ 'levels' => 2, 'base' => 16, 'repeat' => 0 ],
		],
		'parallelize'        => 'implicit',
		'cacheAuthInfo'      => true,
		// When used by FileBackendMultiWrite, read from this cluster if it's the local one
		'readAffinity'       => ( $specificDC === $wmfDatacenter )
	];
	$wgFileBackends[] = [ // backend config for wiki's access to shared test repo
		'class'              => 'SwiftFileBackend',
		'name'               => "shared-testwiki-swift-{$specificDC}",
		'wikiId'             => "wikipedia-test",
		'lockManager'        => 'redisLockManager',
		'swiftAuthUrl'       => $wmfSwiftConfig[$specificDC]['authUrl'],
		'swiftUser'          => $wmfSwiftConfig[$specificDC]['user'],
		'swiftKey'           => $wmfSwiftConfig[$specificDC]['key'],
		'swiftTempUrlKey'    => $wmfSwiftConfig[$specificDC]['tempUrlKey'],
		'parallelize'        => 'implicit',
		'cacheAuthInfo'      => true,
		// When used by FileBackendMultiWrite, read from this cluster if it's the local one
		'readAffinity'       => ( $specificDC === $wmfDatacenter )
	];
}
/* end DC-specific Swift backend config */

/* Common multiwrite backend config */
$wgFileBackends[] = [
	'class'       => 'FileBackendMultiWrite',
	'name'        => 'local-multiwrite',
	'wikiId'      => "{$site}-{$lang}",
	'lockManager' => 'redisLockManager',
	# DO NOT change the master backend unless it is fully trusted or autoRsync is off
	'backends'    => [
		[ 'template' => 'local-swift-eqiad', 'isMultiMaster' => true ],
		[ 'template' => 'local-swift-codfw' ]
	],
	'replication' => 'sync', // read-after-update for assets
	'syncChecks'  => ( 1 | 4 ), // (size & sha1)
	'autoResync'  => 'conservative'
];
$wgFileBackends[] = [
	'class'       => 'FileBackendMultiWrite',
	'name'        => 'shared-multiwrite',
	'wikiId'      => "wikipedia-commons",
	'lockManager' => 'redisLockManager',
	# DO NOT change the master backend unless it is fully trusted or autoRsync is off
	'backends'    => [
		[ 'template' => 'shared-swift-eqiad', 'isMultiMaster' => true ],
		[ 'template' => 'shared-swift-codfw' ],
	],
	'replication' => 'sync', // read-after-update for assets
	'syncChecks'  => ( 1 | 4 ), // (size & sha1)
];
$wgFileBackends[] = [
	'class'       => 'FileBackendMultiWrite',
	'name'        => 'global-multiwrite',
	'wikiId'      => "global-data",
	'lockManager' => 'redisLockManager',
	# DO NOT change the master backend unless it is fully trusted or autoRsync is off
	'backends'    => [
		[ 'template' => 'global-swift-eqiad', 'isMultiMaster' => true ],
		[ 'template' => 'global-swift-codfw' ],
	],
	'replication' => 'sync', // read-after-update for assets
	'syncChecks'  => ( 1 | 4 ) // (size & sha1)
];
$wgFileBackends[] = [
	'class'       => 'FileBackendMultiWrite',
	'name'        => 'shared-testwiki-multiwrite',
	'wikiId'      => "wikipedia-test",
	'lockManager' => 'redisLockManager',
	# DO NOT change the master backend unless it is fully trusted or autoRsync is off
	'backends'    => [
		[ 'template' => 'shared-testwiki-swift-eqiad', 'isMultiMaster' => true ],
		[ 'template' => 'shared-testwiki-swift-codfw' ],
	],
	'replication' => 'sync', // read-after-update for assets
	'syncChecks'  => ( 1 | 4 ), // (size & sha1)
];
/* end multiwrite backend config */

// Lock manager config must use the master datacenter
$wgLockManagers[] = [
	'name'         => 'redisLockManager',
	'class'        => 'RedisLockManager',
	'lockServers'  => $wmfMasterServices['redis_lock'],
	'srvsByBucket' => [
		0 => [ 'rdb1', 'rdb2', 'rdb3' ]
	],
	'redisConfig'  => [
		'connectTimeout' => 2,
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
	'zones'             => $wmgPrivateWiki
		? [
			'thumb' => [ 'url' => "$wgScriptPath/thumb_handler.php" ] ]
		: [],
];
// test2wiki uses testwiki as foreign file repo (e.g. local => testwiki => commons)
if ( $wgDBname === 'test2wiki' ) {
	$wgForeignFileRepos[] = [
		'class'            => 'ForeignDBViaLBRepo',
		'name'             => 'testwikirepo',
		'backend'          => 'shared-testwiki-multiwrite',
		'url'              => "//upload.wikimedia.org/wikipedia/test",
		'hashLevels'       => 2,
		'thumbScriptUrl'   => false,
		'transformVia404'  => true,
		'hasSharedCache'   => true,
		'descBaseUrl'      => "https://test.wikipedia.org/wiki/File:",
		'scriptDirUrl'     => "https://test.wikipedia.org/w",
		'favicon'          => "/static/favicon/black-globe.ico",
		'fetchDescription' => true,
		'descriptionCacheExpiry' => 86400,
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
if ( $wgDBname != 'commonswiki' ) {
	$wgForeignFileRepos[] = [
		'class'            => 'ForeignDBViaLBRepo',
		'name'             => 'shared',
		'backend'          => 'shared-multiwrite',
		'url'              => "//upload.wikimedia.org/wikipedia/commons",
		'hashLevels'       => 2,
		'thumbScriptUrl'   => false,
		'transformVia404'  => true,
		'hasSharedCache'   => true,
		'descBaseUrl'      => "https://commons.wikimedia.org/wiki/File:",
		'scriptDirUrl'     => "https://commons.wikimedia.org/w",
		'favicon'          => "/static/favicon/commons.ico",
		'fetchDescription' => true,
		'descriptionCacheExpiry' => 86400,
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
