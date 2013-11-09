<?php

# WARNING: This file is publically viewable on the web. Do not put private data here.

#
# This file hold the configuration for the file backends
# for production.

$wmfFileJournalTTL = 365; // days

/* Common OpenStack Swift backend config */
$wmGSwiftBigWikis = array( # DO NOT change without proper migration first
	'commonswiki', 'dewiki', 'enwiki', 'fiwiki', 'frwiki', 'hewiki', 'huwiki', 'idwiki',
	'itwiki', 'jawiki', 'rowiki', 'ruwiki', 'thwiki', 'trwiki', 'ukwiki', 'zhwiki'
);
$wmgSwiftShardLocal = in_array( $wgDBname, $wmGSwiftBigWikis ) ? 2 : 0; // shard levels
$wmgSwiftShardCommon = in_array( 'commonswiki', $wmGSwiftBigWikis ) ? 2 : 0; // shard levels
/* end common Swift config */

/* Eqiad Swift backend config */
$wgFileBackends[] = array( // backend config for wiki's local repo
	'class'              => 'SwiftFileBackend',
	'name'               => 'local-swift-eqiad',
	'wikiId'             => "{$site}-{$lang}",
	'lockManager'        => 'nullLockManager', // LocalFile uses FOR UPDATE
	'fileJournal'        => array( 'class' => 'DBFileJournal', 'wiki' => $wgDBname, 'ttlDays' => $wmfFileJournalTTL ),
	'swiftAuthUrl'       => $wmfSwiftEqiadConfig['authUrl'],
	'swiftUser'          => $wmfSwiftEqiadConfig['user'],
	'swiftKey'           => $wmfSwiftEqiadConfig['key'],
	'swiftTempUrlKey'    => $wmfSwiftEqiadConfig['tempUrlKey'],
	'shardViaHashLevels' => array(
		'local-public'     => array( 'levels' => $wmgSwiftShardLocal, 'base' => 16, 'repeat' => 1 ),
		'local-thumb'      => array( 'levels' => $wmgSwiftShardLocal, 'base' => 16, 'repeat' => 1 ),
		'local-temp'       => array( 'levels' => $wmgSwiftShardLocal, 'base' => 16, 'repeat' => 1 ),
		'local-transcoded' => array( 'levels' => $wmgSwiftShardLocal, 'base' => 16, 'repeat' => 1 ),
		'local-deleted'    => array( 'levels' => $wmgSwiftShardLocal, 'base' => 36, 'repeat' => 0 )
	),
	'parallelize'        => 'implicit',
	'cacheAuthInfo'      => true
);
$wgFileBackends[] = array( // backend config for wiki's access to shared repo
	'class'              => 'SwiftFileBackend',
	'name'               => 'shared-swift-eqiad',
	'wikiId'             => "wikipedia-commons",
	'lockManager'        => 'nullLockManager', // just thumbnails
	'fileJournal'        => array( 'class' => 'DBFileJournal', 'wiki' => 'commonswiki', 'ttlDays' => $wmfFileJournalTTL ),
	'swiftAuthUrl'       => $wmfSwiftEqiadConfig['authUrl'],
	'swiftUser'          => $wmfSwiftEqiadConfig['user'],
	'swiftKey'           => $wmfSwiftEqiadConfig['key'],
	'swiftTempUrlKey'    => $wmfSwiftEqiadConfig['tempUrlKey'],
	'shardViaHashLevels' => array(
		'local-public'     => array( 'levels' => $wmgSwiftShardCommon, 'base' => 16, 'repeat' => 1 ),
		'local-thumb'      => array( 'levels' => $wmgSwiftShardCommon, 'base' => 16, 'repeat' => 1 ),
		'local-temp'       => array( 'levels' => $wmgSwiftShardCommon, 'base' => 16, 'repeat' => 1 ),
		'local-transcoded' => array( 'levels' => $wmgSwiftShardCommon, 'base' => 16, 'repeat' => 1 ),
	),
	'parallelize'        => 'implicit',
	'cacheAuthInfo'      => true
);
$wgFileBackends[] = array( // backend config for wiki's access to shared files
	'class'              => 'SwiftFileBackend',
	'name'               => 'global-swift-eqiad',
	'wikiId'             => "global-data",
	'lockManager'        => 'nullLockManager',
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

/* Tampa Swift backend config */
$wgFileBackends[] = array( // backend config for wiki's local repo
	'class'              => 'SwiftFileBackend',
	'name'               => 'local-swift',
	'wikiId'             => "{$site}-{$lang}",
	'lockManager'        => 'nullLockManager', // LocalFile uses FOR UPDATE
	'fileJournal'        => array( 'class' => 'DBFileJournal', 'wiki' => $wgDBname, 'ttlDays' => $wmfFileJournalTTL ),
	'swiftAuthUrl'       => $wmgSwiftConfig['authUrl'],
	'swiftUser'          => $wmgSwiftConfig['user'],
	'swiftKey'           => $wmgSwiftConfig['key'],
	'swiftTempUrlKey'    => $wmgSwiftConfig['tempUrlKey'],
	'shardViaHashLevels' => array(
		'local-public'     => array( 'levels' => $wmgSwiftShardLocal, 'base' => 16, 'repeat' => 1 ),
		'local-thumb'      => array( 'levels' => $wmgSwiftShardLocal, 'base' => 16, 'repeat' => 1 ),
		'local-temp'       => array( 'levels' => $wmgSwiftShardLocal, 'base' => 16, 'repeat' => 1 ),
		'local-transcoded' => array( 'levels' => $wmgSwiftShardLocal, 'base' => 16, 'repeat' => 1 ),
		'local-deleted'    => array( 'levels' => $wmgSwiftShardLocal, 'base' => 36, 'repeat' => 0 )
	),
	'parallelize'        => 'implicit',
	'cacheAuthInfo'      => true
);
$wgFileBackends[] = array( // backend config for wiki's access to shared repo
	'class'              => 'SwiftFileBackend',
	'name'               => 'shared-swift',
	'wikiId'             => "wikipedia-commons",
	'lockManager'        => 'nullLockManager', // just thumbnails
	'fileJournal'        => array( 'class' => 'DBFileJournal', 'wiki' => 'commonswiki', 'ttlDays' => $wmfFileJournalTTL ),
	'swiftAuthUrl'       => $wmgSwiftConfig['authUrl'],
	'swiftUser'          => $wmgSwiftConfig['user'],
	'swiftKey'           => $wmgSwiftConfig['key'],
	'swiftTempUrlKey'    => $wmgSwiftConfig['tempUrlKey'],
	'shardViaHashLevels' => array(
		'local-public'     => array( 'levels' => $wmgSwiftShardCommon, 'base' => 16, 'repeat' => 1 ),
		'local-thumb'      => array( 'levels' => $wmgSwiftShardCommon, 'base' => 16, 'repeat' => 1 ),
		'local-temp'       => array( 'levels' => $wmgSwiftShardCommon, 'base' => 16, 'repeat' => 1 ),
		'local-transcoded' => array( 'levels' => $wmgSwiftShardCommon, 'base' => 16, 'repeat' => 1 ),
	),
	'parallelize'        => 'implicit',
	'cacheAuthInfo'      => true
);
$wgFileBackends[] = array( // backend config for wiki's access to shared files
	'class'              => 'SwiftFileBackend',
	'name'               => 'global-swift',
	'wikiId'             => "global-data",
	'lockManager'        => 'nullLockManager',
	'swiftAuthUrl'       => $wmgSwiftConfig['authUrl'],
	'swiftUser'          => $wmgSwiftConfig['user'],
	'swiftKey'           => $wmgSwiftConfig['key'],
	'swiftTempUrlKey'    => $wmgSwiftConfig['tempUrlKey'],
	'shardViaHashLevels' => array(
		'math-render'  => array( 'levels' => 2, 'base' => 16, 'repeat' => 0 ),
	),
	'parallelize'        => 'implicit',
	'cacheAuthInfo'      => true
);
/* end Tampa Swift backend config */

/* Multiwrite backend config */
$wgFileBackends[] = array(
	'class'       => 'FileBackendMultiWrite',
	'name'        => 'local-multiwrite',
	'wikiId'      => "{$site}-{$lang}",
	'lockManager' => 'nullLockManager', # LocalFile uses FOR UPDATE
	'fileJournal' => array( 'class' => 'DBFileJournal', 'wiki' => $wgDBname, 'ttlDays' => $wmfFileJournalTTL ),
	'backends'    => array(
		# DO NOT change the master backend unless it is fully trusted or autoRsync is off
		array( 'template' => 'local-swift', 'isMultiMaster' => true ),
		array( 'template' => 'local-swift-eqiad', 'isMultiMaster' => false ),
	),
	'syncChecks'  => ( 1 | 4 ), // (size & sha1)
	'autoResync'  => 'conservative' // bug 39221
);
$wgFileBackends[] = array(
	'class'       => 'FileBackendMultiWrite',
	'name'        => 'shared-multiwrite',
	'wikiId'      => "wikipedia-commons",
	'lockManager' => 'nullLockManager', // just thumbnails
	'fileJournal' => array( 'class' => 'DBFileJournal', 'wiki' => 'commonswiki', 'ttlDays' => $wmfFileJournalTTL ),
	'backends'    => array(
		# DO NOT change the master backend unless it is fully trusted or autoRsync is off
		array( 'template' => 'shared-swift', 'isMultiMaster' => true ),
		array( 'template' => 'shared-swift-eqiad', 'isMultiMaster' => false ),
	),
	'syncChecks'  => ( 1 | 4 ), // (size & sha1)
	'autoResync'  => 'conservative' // bug 39221
);
$wgFileBackends[] = array(
	'class'       => 'FileBackendMultiWrite',
	'name'        => 'global-multiwrite',
	'wikiId'      => "global-data",
	'lockManager' => 'nullLockManager',
	'backends'    => array(
		# DO NOT change the master backend unless it is fully trusted or autoRsync is off
		array( 'template' => 'global-swift', 'isMultiMaster' => true ),
		array( 'template' => 'global-swift-eqiad', 'isMultiMaster' => false ),
	),
	'syncChecks'  => ( 1 | 4 ), // (size & sha1)
	'autoResync'  => 'conservative'
);
/* end multiwrite backend config */

$wgLocalFileRepo = array(
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
		'zones'             => array(
			'thumb' => array( 'handlerUrl' => "$wgScriptPath/thumb_handler.php" )
		),
		'abbrvThreshold'    => 160,
		'isPrivate'         => $wmgPrivateWiki
);
if ( $wgDBname != 'commonswiki' ) {
	$wgForeignFileRepos[] = array(
		'class'            => 'ForeignDBViaLBRepo',
		'name'             => 'shared',
		'backend'          => 'shared-multiwrite',
		'url'              => "//upload.wikimedia.org/wikipedia/commons",
		'hashLevels'       => 2,
		'thumbScriptUrl'   => false,
		'transformVia404'  => true,
		'hasSharedCache'   => true,
		'descBaseUrl'      => "//commons.wikimedia.org/wiki/File:",
		'scriptDirUrl'     => "//commons.wikimedia.org/w",
		'fetchDescription' => true,
		'wiki'             => 'commonswiki',
		'initialCapital'   => true,
		'zones'            => array( // actual swift containers have 'local-*'
			'public'  => array( 'container' => 'local-public' ),
			'thumb'   => array( 'container' => 'local-thumb' ),
			'temp'    => array( 'container' => 'local-temp' ),
			'deleted' => array( 'container' => 'local-deleted' )
		),
		'abbrvThreshold'   => 160
	);
}
