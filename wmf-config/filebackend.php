<?php

# WARNING: This file is publically viewable on the web. Do not put private data here.

#
# This file hold the configuration for the file backends
# for production.

$wmfFileJournalTTL = 365; // days

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
	'fileJournal'        => array( 'class' => 'DBFileJournal', 'wiki' => $wgDBname, 'ttlDays' => $wmfFileJournalTTL ),
	'swiftAuthUrl'       => $wmfSwiftConfig['authUrl'],
	'swiftUser'          => $wmfSwiftConfig['user'],
	'swiftKey'           => $wmfSwiftConfig['key'],
	'swiftTempUrlKey'    => $wmfSwiftConfig['tempUrlKey'],
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
	'name'               => 'shared-swift',
	'wikiId'             => "wikipedia-commons",
	'lockManager'        => 'nullLockManager', // just thumbnails
	'fileJournal'        => array( 'class' => 'DBFileJournal', 'wiki' => 'commonswiki', 'ttlDays' => $wmfFileJournalTTL ),
	'swiftAuthUrl'       => $wmfSwiftConfig['authUrl'],
	'swiftUser'          => $wmfSwiftConfig['user'],
	'swiftKey'           => $wmfSwiftConfig['key'],
	'swiftTempUrlKey'    => $wmfSwiftConfig['tempUrlKey'],
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
	'name'               => 'global-swift',
	'wikiId'             => "global-data",
	'lockManager'        => 'nullLockManager',
	'swiftAuthUrl'       => $wmfSwiftConfig['authUrl'],
	'swiftUser'          => $wmfSwiftConfig['user'],
	'swiftKey'           => $wmfSwiftConfig['key'],
	'swiftTempUrlKey'    => $wmfSwiftConfig['tempUrlKey'],
	'shardViaHashLevels' => array(
		'math-render'  => array( 'levels' => 2, 'base' => 16, 'repeat' => 0 ),
	),
	'parallelize'        => 'implicit',
	'cacheAuthInfo'      => true
);
/* end Swift backend config */

/* Ceph rados+rgw backend config */
$wgFileBackends[] = array( // backend config for wiki's local repo
	'class'              => 'SwiftFileBackend',
	'name'               => 'local-ceph',
	'wikiId'             => "{$site}-{$lang}",
	'lockManager'        => 'nullLockManager', // LocalFile uses FOR UPDATE
	'fileJournal'        => array( 'class' => 'DBFileJournal', 'wiki' => $wgDBname, 'ttlDays' => $wmfFileJournalTTL ),
	'swiftAuthUrl'       => $wmfCephRgwConfig['authUrl'],
	'swiftUser'          => $wmfCephRgwConfig['user'],
	'swiftKey'           => $wmfCephRgwConfig['key'],
	'swiftTempUrlKey'    => $wmfCephRgwConfig['tempUrlKey'],
	'rgwS3AccessKey'     => $wmfCephRgwConfig['S3AccessKey'],
	'rgwS3SecretKey'     => $wmfCephRgwConfig['S3SecretKey'],
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
	'name'               => 'shared-ceph',
	'wikiId'             => "wikipedia-commons",
	'lockManager'        => 'nullLockManager', // just thumbnails
	'fileJournal'        => array( 'class' => 'DBFileJournal', 'wiki' => 'commonswiki', 'ttlDays' => $wmfFileJournalTTL ),
	'swiftAuthUrl'       => $wmfCephRgwConfig['authUrl'],
	'swiftUser'          => $wmfCephRgwConfig['user'],
	'swiftKey'           => $wmfCephRgwConfig['key'],
	'swiftTempUrlKey'    => $wmfCephRgwConfig['tempUrlKey'],
	'rgwS3AccessKey'     => $wmfCephRgwConfig['S3AccessKey'],
	'rgwS3SecretKey'     => $wmfCephRgwConfig['S3SecretKey'],
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
	'name'               => 'global-ceph',
	'wikiId'             => "global-data",
	'lockManager'        => 'nullLockManager',
	'swiftAuthUrl'       => $wmfCephRgwConfig['authUrl'],
	'swiftUser'          => $wmfCephRgwConfig['user'],
	'swiftKey'           => $wmfCephRgwConfig['key'],
	'swiftTempUrlKey'    => $wmfCephRgwConfig['tempUrlKey'],
	'rgwS3AccessKey'     => $wmfCephRgwConfig['S3AccessKey'],
	'rgwS3SecretKey'     => $wmfCephRgwConfig['S3SecretKey'],
	'shardViaHashLevels' => array(
		'math-render'  => array( 'levels' => 2, 'base' => 16, 'repeat' => 0 ),
	),
	'parallelize'        => 'implicit',
	'cacheAuthInfo'      => true
);
/* end Ceph rados+rgw backend config */

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
		array( 'template' => 'local-ceph', 'isMultiMaster' => false )
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
		array( 'template' => 'shared-ceph', 'isMultiMaster' => false )
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
		array( 'template' => 'global-ceph', 'isMultiMaster' => false )
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

// Make sure any thumbnails in swift but not ceph have the CDN get purged
$wgHooks['LocalFilePurgeThumbnails'][] = function( File $file, $archiveName ) {
	global $site, $lang;

	$backend = FileBackendGroup::singleton()->get( 'local-swift' );
	// Get thumbnail dir relative to thumb zone
	if ( $archiveName !== false ) {
		$thumbRel = $file->getArchiveThumbRel( $archiveName ); // old version
	} else {
		$thumbRel = $file->getRel(); // current version
	}
	$thumbDir = $backend->getRootStoragePath() . "/local-thumb/$thumbRel";

	$list = $backend->getFileList( array( 'dir' => $thumbDir ) );
	if ( $list === null ) {
		wfDebugLog( 'SwiftBackend', "Could not get thumbnail listing." .
			"Site: `{$site}` Lang: `{$lang}` ThumbRel: `{$thumbRel}/`" );
	} else {
		$urls = array();
		wfProfileIn( __METHOD__ . '-list' );
		foreach ( $list as $relFile ) {
			$urls[] = ( $archiveName !== false )
				? $file->getArchiveThumbUrl( $archiveName, $relFile )
				: $file->getThumbUrl( $relFile );
		}
		wfProfileOut( __METHOD__ . '-list' );
		wfProfileIn( __METHOD__ . '-purge' );
		SquidUpdate::purge( $urls );
		wfProfileOut( __METHOD__ . '-purge' );
	}

	return true;
};
