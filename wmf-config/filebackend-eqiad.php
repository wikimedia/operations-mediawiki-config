<?php

# WARNING: This file is publically viewable on the web. Do not put private data here.

/* Eqiad Swift backend config */
$wgFileBackends[] = array( // backend config for wiki's local repo
	'class'              => 'SwiftFileBackend',
	'name'               => 'local-swift-eqiad',
	'wikiId'             => "{$site}-{$lang}",
	'lockManager'        => 'redisLockManager',
	'swiftAuthUrl'       => $wmfSwiftEqiadConfig['authUrl'],
	'swiftUser'          => $wmfSwiftEqiadConfig['user'],
	'swiftKey'           => $wmfSwiftEqiadConfig['key'],
	'swiftTempUrlKey'    => $wmfSwiftEqiadConfig['tempUrlKey'],
	'shardViaHashLevels' => array(
		'local-public'     => array( 'levels' => $wmfSwiftShardLocal, 'base' => 16, 'repeat' => 1 ),
		'local-thumb'      => array( 'levels' => $wmfSwiftShardLocal, 'base' => 16, 'repeat' => 1 ),
		'local-temp'       => array( 'levels' => $wmfSwiftShardLocal, 'base' => 16, 'repeat' => 1 ),
		'local-transcoded' => array( 'levels' => $wmfSwiftShardLocal, 'base' => 16, 'repeat' => 1 ),
		'local-deleted'    => array( 'levels' => $wmfSwiftShardLocal, 'base' => 36, 'repeat' => 0 )
	),
	'parallelize'        => 'implicit',
	'cacheAuthInfo'      => true
);
$wgFileBackends[] = array( // backend config for wiki's access to shared repo
	'class'              => 'SwiftFileBackend',
	'name'               => 'shared-swift-eqiad',
	'wikiId'             => "wikipedia-commons",
	'lockManager'        => 'redisLockManager',
	'swiftAuthUrl'       => $wmfSwiftEqiadConfig['authUrl'],
	'swiftUser'          => $wmfSwiftEqiadConfig['user'],
	'swiftKey'           => $wmfSwiftEqiadConfig['key'],
	'swiftTempUrlKey'    => $wmfSwiftEqiadConfig['tempUrlKey'],
	'shardViaHashLevels' => array(
		'local-public'     => array( 'levels' => $wmfSwiftShardCommon, 'base' => 16, 'repeat' => 1 ),
		'local-thumb'      => array( 'levels' => $wmfSwiftShardCommon, 'base' => 16, 'repeat' => 1 ),
		'local-temp'       => array( 'levels' => $wmfSwiftShardCommon, 'base' => 16, 'repeat' => 1 ),
		'local-transcoded' => array( 'levels' => $wmfSwiftShardCommon, 'base' => 16, 'repeat' => 1 ),
	),
	'parallelize'        => 'implicit',
	'cacheAuthInfo'      => true
);
$wgFileBackends[] = array( // backend config for wiki's access to shared files
	'class'              => 'SwiftFileBackend',
	'name'               => 'global-swift-eqiad',
	'wikiId'             => "global-data",
	'lockManager'        => 'redisLockManager',
	'swiftAuthUrl'       => $wmfSwiftEqiadConfig['authUrl'],
	'swiftUser'          => $wmfSwiftEqiadConfig['user'],
	'swiftKey'           => $wmfSwiftEqiadConfig['key'],
	'swiftTempUrlKey'    => $wmfSwiftEqiadConfig['tempUrlKey'],
	'shardViaHashLevels' => array(
		'math-render'  => array( 'levels' => 2, 'base' => 16, 'repeat' => 0 ),
	),
	'parallelize'        => 'implicit',
	'cacheAuthInfo'      => true
);
/* end Eqiad Swift backend config */

/* Multiwrite backend config */
$wgFileBackends[] = array(
	'class'       => 'FileBackendMultiWrite',
	'name'        => 'local-multiwrite',
	'wikiId'      => "{$site}-{$lang}",
	'lockManager' => 'redisLockManager',
	'backends'    => array(
		# DO NOT change the master backend unless it is fully trusted or autoRsync is off
		array( 'template' => 'local-swift-eqiad', 'isMultiMaster' => true ),
	),
	'syncChecks'  => ( 1 | 4 ), // (size & sha1)
	'autoResync'  => 'conservative' // bug 39221
);
$wgFileBackends[] = array(
	'class'       => 'FileBackendMultiWrite',
	'name'        => 'shared-multiwrite',
	'wikiId'      => "wikipedia-commons",
	'lockManager' => 'redisLockManager',
	'backends'    => array(
		# DO NOT change the master backend unless it is fully trusted or autoRsync is off
		array( 'template' => 'shared-swift-eqiad', 'isMultiMaster' => true ),
	),
	'syncChecks'  => ( 1 | 4 ), // (size & sha1)
	'autoResync'  => 'conservative' // bug 39221
);
$wgFileBackends[] = array(
	'class'       => 'FileBackendMultiWrite',
	'name'        => 'global-multiwrite',
	'wikiId'      => "global-data",
	'lockManager' => 'redisLockManager',
	'backends'    => array(
		# DO NOT change the master backend unless it is fully trusted or autoRsync is off
		array( 'template' => 'global-swift-eqiad', 'isMultiMaster' => true ),
	),
	'syncChecks'  => ( 1 | 4 ), // (size & sha1)
	'autoResync'  => 'conservative'
);
/* end multiwrite backend config */

$wgLockManagers[] = array(
	'name'         => 'redisLockManager',
	'class'        => 'RedisLockManager',
	'lockServers'  => array(
		'rdb1' => '10.64.0.180',
		'rdb2' => '10.64.0.181',
		'rdb3' => '10.64.0.182'
	),
	'srvsByBucket' => array(
		0 => array( 'rdb1', 'rdb2', 'rdb3' )
	),
	'redisConfig'  => array(
		'connectTimeout' => 2,
		'password'       => $wmgRedisPassword
	)
);
