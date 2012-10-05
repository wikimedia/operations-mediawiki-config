<?php

# WARNING: This file is publically viewable on the web. Do not put private data here.

#
# This file hold the configuration for NFS and Swift files backends
# for production.

/* NFS backend config */
$wgFileBackends[] = array( // backend config for wiki's local repo
	'class'          => 'FSFileBackend',
	'name'           => 'local-NFS',
	'wikiId'         => "{$site}-{$lang}",
	'lockManager'    => 'nullLockManager', # LocalFile uses FOR UPDATE
	'fileJournal'    => array( 'class' => 'DBFileJournal', 'wiki' => $wgDBname ),
	'fileMode'       => 0644,
	'containerPaths' => array(
		"local-public"    => $wgUploadDirectory,
		"local-thumb"     => str_replace( '/mnt/upload6', '/mnt/thumbs', "$wgUploadDirectory/thumb" ),
		"local-deleted"   => "/mnt/upload6/private/archive/$site/$lang",
		"local-temp"      => "$wgUploadDirectory/temp",
		"timeline-render" => "$wgUploadDirectory/timeline"
	)
);
$wgFileBackends[] = array( // backend config for wiki's access to shared repo
	'class'          => 'FSFileBackend',
	'name'           => 'shared-NFS',
	'wikiId'         => "wikipedia-commons",
	'lockManager'    => 'nullLockManager', # just thumbnails
	'fileJournal'    => array( 'class' => 'DBFileJournal', 'wiki' => 'commonswiki' ),
	'fileMode'       => 0644,
	'containerPaths' => array(
		"local-public"  => "/mnt/upload6/wikipedia/commons",
		"local-thumb"   => "/mnt/thumbs/wikipedia/commons/thumb",
		"local-temp"    => "/mnt/upload6/wikipedia/commons/temp",
	)
);
$wgFileBackends[] = array( // backend config for wiki's access to global files
	'class'          => 'FSFileBackend',
	'name'           => 'global-NFS',
	'wikiId'         => "global-data",
	'lockManager'    => 'nullLockManager',
	'fileMode'       => 0644,
	'containerPaths' => array(
		"math-render" => "/mnt/upload6/math", // see $wgMathDirectory
	)
);
/* end NFS backend config */

/* OpenStack Swift backend config */
$wmfSwiftBigWikis = array( # DO NOT change without proper migration first
	'commonswiki', 'dewiki', 'enwiki', 'fiwiki', 'frwiki', 'hewiki', 'huwiki', 'idwiki',
	'itwiki', 'jawiki', 'rowiki', 'ruwiki', 'thwiki', 'trwiki', 'ukwiki', 'zhwiki'
);
$wmfSwiftShardLocal = in_array( $wgDBname, $wmfSwiftBigWikis ) ? 2 : 0;
$wmfSwiftShardCommon = in_array( 'commonswiki', $wmfSwiftBigWikis ) ? 2 : 0;
$wgFileBackends[] = array( // backend config for wiki's local repo
	'class'              => 'SwiftFileBackend',
	'name'               => 'local-swift',
	'wikiId'             => "{$site}-{$lang}",
	'lockManager'        => 'nullLockManager', // LocalFile uses FOR UPDATE
	'fileJournal'        => array( 'class' => 'DBFileJournal', 'wiki' => $wgDBname ),
	'swiftAuthUrl'       => $wmfSwiftConfig['authUrl'],
	'swiftUser'          => $wmfSwiftConfig['user'],
	'swiftKey'           => $wmfSwiftConfig['key'],
	'shardViaHashLevels' => array(
		'local-public'  => array( 'levels' => $wmfSwiftShardLocal, 'base' => 16, 'repeat' => 1 ),
		'local-thumb'   => array( 'levels' => $wmfSwiftShardLocal, 'base' => 16, 'repeat' => 1 ),
		'local-temp'    => array( 'levels' => $wmfSwiftShardLocal, 'base' => 16, 'repeat' => 1 ),
		'local-deleted' => array( 'levels' => $wmfSwiftShardLocal, 'base' => 36, 'repeat' => 0 )
	),
	'parallelize'        => 'implicit',
	'cacheAuthInfo'      => true
);
$wgFileBackends[] = array( // backend config for wiki's access to shared repo
	'class'              => 'SwiftFileBackend',
	'name'               => 'shared-swift',
	'wikiId'             => "wikipedia-commons",
	'lockManager'        => 'nullLockManager', // just thumbnails
	'fileJournal'        => array( 'class' => 'DBFileJournal', 'wiki' => 'commonswiki' ),
	'swiftAuthUrl'       => $wmfSwiftConfig['authUrl'],
	'swiftUser'          => $wmfSwiftConfig['user'],
	'swiftKey'           => $wmfSwiftConfig['key'],
	'shardViaHashLevels' => array(
		'local-public'  => array( 'levels' => $wmfSwiftShardCommon, 'base' => 16, 'repeat' => 1 ),
		'local-thumb'   => array( 'levels' => $wmfSwiftShardCommon, 'base' => 16, 'repeat' => 1 ),
		'local-temp'    => array( 'levels' => $wmfSwiftShardCommon, 'base' => 16, 'repeat' => 1 ),
	),
	'parallelize'        => 'implicit',
	'cacheAuthInfo'      => true
);
$wgFileBackends[] = array( // backend config for wiki's access to shared files
	'class'              => 'SwiftFileBackend',
	'name'               => 'global-swift',
	'wikiId'             => "global-data",
	'lockManager'        => 'nullLockManager',
	'swiftAuthUrl'       => $wmfSwiftConfig['authUrl'],
	'swiftUser'          => $wmfSwiftConfig['user'],
	'swiftKey'           => $wmfSwiftConfig['key'],
	'shardViaHashLevels' => array(
		'math-render'  => array( 'levels' => 2, 'base' => 16, 'repeat' => 0 ),
	),
	'parallelize'        => 'implicit',
	'cacheAuthInfo'      => true
);
/* end Swift backend config */


/* NFS-Swift multiwrite backend config */
$wgFileBackends[] = array(
	'class'       => 'FileBackendMultiWrite',
	'name'        => 'local-multiwrite',
	'wikiId'      => "{$site}-{$lang}",
	'lockManager' => 'nullLockManager', # LocalFile uses FOR UPDATE
	'fileJournal' => array( 'class' => 'DBFileJournal', 'wiki' => $wgDBname ),
	'backends'    => array(
		array( 'template' => 'local-NFS', 'isMultiMaster' => false ),
		array( 'template' => 'local-swift', 'isMultiMaster' => true )
	),
	'syncChecks'  => ( 1 | 4 ), // (size & sha1)
	'noPushQuickOps' => true,
	'noPushDirConts' => array( 'local-thumb', 'local-temp' ),
	'autoResync'  => true // bug 39221
);
$wgFileBackends[] = array(
	'class'       => 'FileBackendMultiWrite',
	'name'        => 'shared-multiwrite',
	'wikiId'      => "wikipedia-commons",
	'lockManager' => 'nullLockManager', // just thumbnails
	'fileJournal' => array( 'class' => 'DBFileJournal', 'wiki' => 'commonswiki' ),
	'backends'    => array(
		array( 'template' => 'shared-NFS', 'isMultiMaster' => false ),
		array( 'template' => 'shared-swift', 'isMultiMaster' => true ),
	),
	'syncChecks'  => ( 1 | 4 ), // (size & sha1)
	'noPushQuickOps' => true,
	'noPushDirConts' => array( 'local-thumb', 'local-temp' ),
	'autoResync'  => true // 39221
);
$wgFileBackends[] = array(
	'class'       => 'FileBackendMultiWrite',
	'name'        => 'global-multiwrite',
	'wikiId'      => "global-data",
	'lockManager' => 'nullLockManager',
	'backends'    => array(
		array( 'template' => 'global-NFS', 'isMultiMaster' => false ),
		array( 'template' => 'global-swift', 'isMultiMaster' => true ),
	),
	'syncChecks'  => ( 1 | 4 ) // (size & sha1)
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
		'abbrvThreshold'    => in_array( $wgDBname, array( 'testwiki' ) ) ? 160 : 255,
		'isPrivate'         => $wmgPrivateWiki
);
if ( $wgDBname != 'commonswiki' ) {
	$wgForeignFileRepos[] = array(
		'class'            => 'ForeignDBViaLBRepo',
		'name'             => 'shared',
		'backend'          => 'shared-multiwrite',
		'url'              => "$urlprotocol//upload.wikimedia.org/wikipedia/commons",
		'hashLevels'       => 2,
		'thumbScriptUrl'   => false,
		'transformVia404'  => true,
		'hasSharedCache'   => true,
		'descBaseUrl'      => "$urlprotocol//commons.wikimedia.org/wiki/File:",
		'scriptDirUrl'     => "$urlprotocol//commons.wikimedia.org/w",
		'fetchDescription' => true,
		'wiki'             => 'commonswiki',
		'initialCapital'   => true,
		'zones'            => array( // actual swift containers have 'local-*'
			'public'  => array( 'container' => 'local-public' ),
			'thumb'   => array( 'container' => 'local-thumb' ),
			'temp'    => array( 'container' => 'local-temp' ),
			'deleted' => array( 'container' => 'local-deleted' )
		),
		'abbrvThreshold'   => 255
	);
}
