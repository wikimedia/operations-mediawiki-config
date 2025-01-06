<?php
// WARNING: This file is publicly viewable on the web. Do not put private data here.

// filebackend.php holds the configuration for MediaWiki file backends and file repos.
// See also <https://www.mediawiki.org/wiki/Manual:$wgFileBackends> and
// <https://www.mediawiki.org/wiki/Manual:$wgForeignFileRepos>.
//
// This for PRODUCTION.
//
// Effective load order:
// - multiversion
// - mediawiki/DefaultSettings.php
// - wmf-config/*Services.php
// - wmf-config/InitialiseSettings.php
// - wmf-config/logging.php
// - wmf-config/filebackend.php [THIS FILE]
//
// Included from: wmf-config/CommonSettings.php.
//

// Inline comments are often used for noting the task(s) associated with specific configuration
// or for explaining the configuration settings, and requiring comments to be on their own line
// would reduce readability for this file
// phpcs:disable MediaWiki.WhiteSpace.SpaceBeforeSingleLineComment.NewLineComment

global $wmgSwiftConfig;
// Common OpenStack Swift backend convenience variables
$wmgSwiftBigWikis = [ // DO NOT change without proper migration first
	'commonswiki', 'dewiki', 'enwiki', 'fiwiki', 'frwiki', 'hewiki', 'huwiki', 'idwiki',
	'itwiki', 'jawiki', 'rowiki', 'ruwiki', 'thwiki', 'trwiki', 'ukwiki', 'zhwiki'
];
$wmgSwiftShardLocal = in_array( $wgDBname, $wmgSwiftBigWikis ) ? 2 : 0; // shard levels
$wmgSwiftShardCommon = in_array( 'commonswiki', $wmgSwiftBigWikis ) ? 2 : 0; // shard levels

if ( $wmgRealm === 'labs' ) {
	$redisLockServers = [ 'rdb1' ];
	$commonsUrl = "https://commons.wikimedia.beta.wmflabs.org";
	$uploadUrl = "//upload.wikimedia.beta.wmflabs.org";
} else {
	$redisLockServers = [ 'rdb1', 'rdb2', 'rdb3' ];
	$commonsUrl = "https://commons.wikimedia.org";
	$uploadUrl = "//upload.wikimedia.org";
}

/* DC-specific Swift backend config */
foreach ( $wmgDatacenters as $specificDC ) {
	// backend config for wiki's local repo
	$wgFileBackends[] = [
		'class' => SwiftFileBackend::class,
		'name' => "local-swift-{$specificDC}",
		'wikiId' => "{$site}-{$lang}",
		'lockManager' => 'redisLockManager',
		'swiftAuthUrl' => $wmgAllServices[$specificDC]['mediaSwiftAuth'],
		'swiftStorageUrl' => $wmgAllServices[$specificDC]['mediaSwiftStore'],
		'swiftUser' => $wmgSwiftConfig[$specificDC]['user'],
		'swiftKey' => $wmgSwiftConfig[$specificDC]['key'],
		'swiftTempUrlKey' => $wmgSwiftConfig[$specificDC]['tempUrlKey'],
		'shardViaHashLevels' => [
			'local-public' =>
				[ 'levels' => $wmgSwiftShardLocal, 'base' => 16, 'repeat' => 1 ],
			'local-thumb' =>
				[ 'levels' => $wmgSwiftShardLocal, 'base' => 16, 'repeat' => 1 ],
			'local-temp' =>
				[ 'levels' => $wmgSwiftShardLocal, 'base' => 16, 'repeat' => 1 ],
			'local-transcoded' =>
				[ 'levels' => $wmgSwiftShardLocal, 'base' => 16, 'repeat' => 1 ],
			'local-deleted' =>
				[ 'levels' => $wmgSwiftShardLocal, 'base' => 36, 'repeat' => 0 ],
		],
		'parallelize' => 'implicit',
		'cacheAuthInfo' => true,
		// When used by FileBackendMultiWrite, read from this cluster if it's the local one
		'readAffinity' => ( $specificDC === $wmgDatacenter ),
		'readUsers' => [ $wmgSwiftConfig[$specificDC]['thumborUser'] ],
		'writeUsers' => [ $wmgSwiftConfig[$specificDC]['thumborUser'] ],
		'secureReadUsers' => [ $wmgSwiftConfig[$specificDC]['thumborPrivateUser'] ],
		'secureWriteUsers' => [ $wmgSwiftConfig[$specificDC]['thumborPrivateUser'] ],
		'connTimeout' => 10,
		// T226979
		'reqTimeout' => 900,
		'canShellboxGetTempUrl' => true,
	];

	// backend config for wiki's access to shared repo
	$wgFileBackends[] = [
		'class' => SwiftFileBackend::class,
		'name' => "shared-swift-{$specificDC}",
		'wikiId' => "wikipedia-commons",
		'lockManager' => 'redisLockManager',
		'swiftAuthUrl' => $wmgAllServices[$specificDC]['mediaSwiftAuth'],
		'swiftStorageUrl' => $wmgAllServices[$specificDC]['mediaSwiftStore'],
		'swiftUser' => $wmgSwiftConfig[$specificDC]['user'],
		'swiftKey' => $wmgSwiftConfig[$specificDC]['key'],
		'swiftTempUrlKey' => $wmgSwiftConfig[$specificDC]['tempUrlKey'],
		'shardViaHashLevels' => [
			'local-public' =>
				 [ 'levels' => $wmgSwiftShardCommon, 'base' => 16, 'repeat' => 1 ],
			'local-thumb' =>
				[ 'levels' => $wmgSwiftShardCommon, 'base' => 16, 'repeat' => 1 ],
			'local-temp' =>
				[ 'levels' => $wmgSwiftShardCommon, 'base' => 16, 'repeat' => 1 ],
			'local-transcoded' =>
				[ 'levels' => $wmgSwiftShardCommon, 'base' => 16, 'repeat' => 1 ],
		],
		'parallelize' => 'implicit',
		'cacheAuthInfo' => true,
		// When used by FileBackendMultiWrite, read from this cluster if it's the local one
		'readAffinity' => ( $specificDC === $wmgDatacenter ),
		'readUsers' => [ $wmgSwiftConfig[$specificDC]['thumborUser'] ],
		'writeUsers' => [ $wmgSwiftConfig[$specificDC]['thumborUser'] ],
		'secureReadUsers' => [ $wmgSwiftConfig[$specificDC]['thumborPrivateUser'] ],
		'secureWriteUsers' => [ $wmgSwiftConfig[$specificDC]['thumborPrivateUser'] ],
		'connTimeout' => 10,
		// T226979
		'reqTimeout' => 900,
		'canShellboxGetTempUrl' => true,
	];

	// backend config for wiki's access to shared files
	$wgFileBackends[] = [
		'class' => SwiftFileBackend::class,
		'name' => "global-swift-{$specificDC}",
		'wikiId' => "global-data",
		'lockManager' => 'redisLockManager',
		'swiftAuthUrl' => $wmgAllServices[$specificDC]['mediaSwiftAuth'],
		'swiftStorageUrl' => $wmgAllServices[$specificDC]['mediaSwiftStore'],
		'swiftUser' => $wmgSwiftConfig[$specificDC]['user'],
		'swiftKey' => $wmgSwiftConfig[$specificDC]['key'],
		'swiftTempUrlKey' => $wmgSwiftConfig[$specificDC]['tempUrlKey'],
		'shardViaHashLevels' => [],
		'parallelize' => 'implicit',
		'cacheAuthInfo' => true,
		// When used by FileBackendMultiWrite, read from this cluster if it's the local one
		'readAffinity' => ( $specificDC === $wmgDatacenter ),
		'readUsers' => [ $wmgSwiftConfig[$specificDC]['thumborUser'] ],
		'writeUsers' => [ $wmgSwiftConfig[$specificDC]['thumborUser'] ],
		'secureReadUsers' => [ $wmgSwiftConfig[$specificDC]['thumborPrivateUser'] ],
		'secureWriteUsers' => [ $wmgSwiftConfig[$specificDC]['thumborPrivateUser'] ],
		'connTimeout' => 10,
		// T226979
		'reqTimeout' => 900,
		'canShellboxGetTempUrl' => true,
	];

	// backend config for wiki's access to shared test repo
	$wgFileBackends[] = [
		'class' => SwiftFileBackend::class,
		'name' => "shared-testwiki-swift-{$specificDC}",
		'wikiId' => "wikipedia-test",
		'lockManager' => 'redisLockManager',
		'swiftAuthUrl' => $wmgAllServices[$specificDC]['mediaSwiftAuth'],
		'swiftStorageUrl' => $wmgAllServices[$specificDC]['mediaSwiftStore'],
		'swiftUser' => $wmgSwiftConfig[$specificDC]['user'],
		'swiftKey' => $wmgSwiftConfig[$specificDC]['key'],
		'swiftTempUrlKey' => $wmgSwiftConfig[$specificDC]['tempUrlKey'],
		'parallelize' => 'implicit',
		'cacheAuthInfo' => true,
		// When used by FileBackendMultiWrite, read from this cluster if it's the local one
		'readAffinity' => ( $specificDC === $wmgDatacenter ),
		'readUsers' => [ $wmgSwiftConfig[$specificDC]['thumborUser'] ],
		'writeUsers' => [ $wmgSwiftConfig[$specificDC]['thumborUser'] ],
		'secureReadUsers' => [ $wmgSwiftConfig[$specificDC]['thumborPrivateUser'] ],
		'secureWriteUsers' => [ $wmgSwiftConfig[$specificDC]['thumborPrivateUser'] ],
		'connTimeout' => 10,
		// T226979
		'reqTimeout' => 900,
		'canShellboxGetTempUrl' => true,
	];
}
/* end DC-specific Swift backend config */

/* Common multiwrite backend config */
$isEqiadMaster = ( $wmgMasterDatacenter == 'eqiad' );
$isCodfwMaster = ( $wmgMasterDatacenter == 'codfw' );
$localMultiWriteFileBackend = [
	'class' => FileBackendMultiWrite::class,
	'name' => 'local-multiwrite',
	'wikiId' => "{$site}-{$lang}",
	'lockManager' => 'redisLockManager',
	// DO NOT change the master backend unless it is fully trusted or autoRsync is off
	'backends' => [
		[ 'template' => 'local-swift-eqiad', 'isMultiMaster' => $isEqiadMaster ],
	],
	// read-after-update for assets
	'replication' => 'sync',
	// (size & sha1)
	'syncChecks' => ( 1 | 4 ),
	'autoResync' => 'conservative',
];
$sharedMultiwriteFileBackend = [
	'class' => FileBackendMultiWrite::class,
	'name' => 'shared-multiwrite',
	'wikiId' => "wikipedia-commons",
	'lockManager' => 'redisLockManager',
	// DO NOT change the master backend unless it is fully trusted or autoRsync is off
	'backends' => [
		[ 'template' => 'shared-swift-eqiad', 'isMultiMaster' => $isEqiadMaster ],
	],
	// read-after-update for assets
	'replication' => 'sync',
	// (size & sha1)
	'syncChecks' => ( 1 | 4 ),
];
$globalMultiWriteFileBackend = [
	'class' => FileBackendMultiWrite::class,
	'name' => 'global-multiwrite',
	'wikiId' => "global-data",
	'lockManager' => 'redisLockManager',
	// DO NOT change the master backend unless it is fully trusted or autoRsync is off
	'backends' => [
		[ 'template' => 'global-swift-eqiad', 'isMultiMaster' => $isEqiadMaster ],
	],
	// read-after-update for assets
	'replication' => 'sync',
	// (size & sha1)
	'syncChecks' => ( 1 | 4 ),
];
$sharedTestwikiMultiWriteFileBackend = [
	'class' => FileBackendMultiWrite::class,
	'name' => 'shared-testwiki-multiwrite',
	'wikiId' => "wikipedia-test",
	'lockManager' => 'redisLockManager',
	// DO NOT change the master backend unless it is fully trusted or autoRsync is off
	'backends' => [
		[ 'template' => 'shared-testwiki-swift-eqiad', 'isMultiMaster' => $isEqiadMaster ],
	],
	// read-after-update for assets
	'replication' => 'sync',
	// (size & sha1)
	'syncChecks' => ( 1 | 4 ),
];

if ( in_array( 'codfw', $wmgDatacenters ) ) {
	$localMultiWriteFileBackend['backends'][] = [ 'template' => 'local-swift-codfw', 'isMultiMaster' => $isCodfwMaster ];
	$sharedMultiwriteFileBackend['backends'][] = [ 'template' => 'shared-swift-codfw', 'isMultiMaster' => $isCodfwMaster ];
	$globalMultiWriteFileBackend['backends'][] = [ 'template' => 'global-swift-codfw', 'isMultiMaster' => $isCodfwMaster ];
	$sharedTestwikiMultiWriteFileBackend['backends'][] = [ 'template' => 'shared-testwiki-swift-codfw', 'isMultiMaster' => $isCodfwMaster ];
}
$wgFileBackends[] = $localMultiWriteFileBackend;
$wgFileBackends[] = $sharedMultiwriteFileBackend;
$wgFileBackends[] = $globalMultiWriteFileBackend;
$wgFileBackends[] = $sharedTestwikiMultiWriteFileBackend;

/* end multiwrite backend config */

// Lock manager config must use the master datacenter
$wgLockManagers[] = [
	'name' => 'redisLockManager',
	'class' => RedisLockManager::class,
	'lockServers' => $wmgMasterServices['redis_lock'],
	'srvsByBucket' => [
		0 => $redisLockServers
	],
	'redisConfig' => [
		'connectTimeout' => 2,
		'readTimeout' => 2,
		'password' => $wmgRedisPassword
	],
];

$wgLocalFileRepo = [
	'class' => LocalRepo::class,
	'name' => 'local',
	'backend' => 'local-multiwrite',
	'url' => $wgUploadBaseUrl ? $wgUploadBaseUrl . $wgUploadPath : $wgUploadPath,
	'scriptDirUrl' => $wgScriptPath,
	'hashLevels' => 2,
	'thumbScriptUrl' => $wgThumbnailScriptPath,
	'transformVia404' => true,
	'useJsonMetadata' => true,
	'useSplitMetadata' => true,
	'initialCapital' => $wgCapitalLinks,
	'deletedHashLevels' => 3,
	'abbrvThreshold' => 160,
	'isPrivate' => $wmgPrivateWiki,
	'thumbProxyUrl' => $wmgSwiftConfig[$wmgDatacenter]['thumborUrl'] . '/' . $site . '/' . $lang . '/thumb/',
	'thumbProxySecret' => $wmgSwiftConfig[$wmgDatacenter]['thumborSecret'],
	'zones' => $wmgPrivateWiki
		? [ 'thumb' => [ 'url' => "$wgScriptPath/thumb_handler.php" ] ]
		: [],
];

// test2wiki uses testwiki as foreign file repo (e.g. local => testwiki => commons)
// Does not exist in labs.
if ( $wgDBname === 'test2wiki' ) {
	$wgForeignFileRepos[] = [
		'class' => ForeignDBViaLBRepo::class,
		'name' => 'testwikirepo',
		'backend' => 'shared-testwiki-multiwrite',
		'url' => "{$uploadUrl}/wikipedia/test",
		'hashLevels' => 2,
		'thumbScriptUrl' => false,
		'transformVia404' => true,
		'hasSharedCache' => true,
		'descBaseUrl' => "https://test.wikipedia.org/wiki/File:",
		'scriptDirUrl' => "https://test.wikipedia.org/w",
		'favicon' => "/static/favicon/black-globe.ico",
		'fetchDescription' => true,
		'descriptionCacheExpiry' => 86400 * 7,
		'wiki' => 'testwiki',
		'initialCapital' => true,
		'zones' => [
			// actual swift containers have 'local-*'
			'public' => [ 'container' => 'local-public' ],
			'thumb' => [ 'container' => 'local-thumb' ],
			'temp' => [ 'container' => 'local-temp' ],
			'deleted' => [ 'container' => 'local-deleted' ],
		],
		// Keep in sync with the local repo on testwiki or things break.
		'abbrvThreshold' => 160,
	];
}
if ( $wgDBname != 'commonswiki' ) {
	// Commons is local to commonswiki :)
	// wikitech uses $wgUseInstantCommons instead of db access.
	$wgForeignFileRepos[] = [
		'class' => ForeignDBViaLBRepo::class,
		'name' => 'shared',
		'backend' => 'shared-multiwrite',
		'url' => "{$uploadUrl}/wikipedia/commons",
		'hashLevels' => 2,
		'thumbScriptUrl' => false,
		'transformVia404' => true,
		'hasSharedCache' => true,
		'descBaseUrl' => "{$commonsUrl}/wiki/File:",
		'scriptDirUrl' => "{$commonsUrl}/w",
		'favicon' => "/static/favicon/commons.ico",
		'fetchDescription' => true,
		'descriptionCacheExpiry' => 86400 * 7,
		'wiki' => 'commonswiki',
		'initialCapital' => true,
		'zones' => [
			// actual swift containers have 'local-*'
			'public' => [ 'container' => 'local-public' ],
			'thumb' => [ 'container' => 'local-thumb' ],
			'temp' => [ 'container' => 'local-temp' ],
			'deleted' => [ 'container' => 'local-deleted' ],
		],
		// Keep in sync with the local repo on commons or things break.
		'abbrvThreshold' => 160,
	];
}
