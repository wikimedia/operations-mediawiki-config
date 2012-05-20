<?php
/**
 * Helper functions for Swift related image thumbnail purging.
 * The functions here should only be called after MediaWiki setup.
 *
 * The SwiftCloudFiles extensions must be installed.
 * $wmfSwiftConfig must reside in PrivateSettings.php. It should also
 * be extracted in CommonSettings.php to set any swift backend settings.
 *
 * This file belongs under wmf-config/ and should be included by CommonSettings.php.
 */

if ( in_array( $wgDBname, array( 'testwiki', 'test2wiki', 'mediawikiwiki', 'commonswiki' ) ) ) {
	$wgHooks['FileTransformed'][] = 'wmfOnFileTransformed';
	$wgHooks['LocalFilePurgeThumbnails'][] = 'wmfOnLocalFilePurgeThumbnails';
} else { // old hook
	$wgHooks['LocalFilePurgeThumbnails'][] = 'wmfPurgeBackendThumbCache';
}

/**
 * Handler for the LocalFilePurgeThumbnails hook.
 * To avoid excess inclusion of cloudfiles.php, a hook handler can be added
 * to CommonSettings.php which includes this file and calls this function.
 *
 * @param $file File
 * @param $archiveName string|false
 * @return true
 */
function wmfPurgeBackendThumbCache( File $file, $archiveName ) {
	global $site, $lang, $wgDBname; // CommonSettings.php

	// Get thumbnail dir relative to thumb zone
	if ( $archiveName !== false ) {
		$thumbRel = $file->getArchiveThumbRel( $archiveName ); // old version
	} else {
		$thumbRel = $file->getRel(); // current version
	}

	// Get the container for the thumb zone and delete the objects
	$container = wmfGetSwiftThumbContainer( $site, $lang, $wgDBname, "$thumbRel/" );
	if ( $container ) { // sanity
		try {
			$files = $container->list_objects( 0, NULL, "$thumbRel/" );
		} catch ( InvalidResponseException $e ) {
			$files = array();
			// Reported by Bidgee on IRC while uploading to commons
			// http://i638.photobucket.com/albums/uu105/Busabout/Error.png
			// -- hashar 20120216 - 09:25 UTC
			wfDebugLog( 'swiftThumb', "bug 34440 InvalidResponseException trying to list_objects." .
				" Message `{$e->getMessage()}`; Site: `{$site}` Lang: `{$lang}` ThumbRel: `{$thumbRel}/`" );
		}
		foreach ( $files as $file ) {
			try {
				if ( $file === '' ) {
					wfDebugLog( 'swiftThumb', "SyntaxException trying to delete object with empty name avoided." );
					continue;
				}
				$container->delete_object( $file );
			} catch ( NoSuchObjectException $e ) { // probably a race condition
				wfDebugLog( 'swiftThumb', "Could not delete `{$file}`; object does not exist." );
			}
		}
	}

	return true;
}

/**
 * Get the Swift thumbnail container for this wiki.
 *
 * @param $site string
 * @param $lang string
 * @param $dbName string
 * @param $relPath string Path relative to container
 * @return CF_Container|null
 */
function wmfGetSwiftThumbContainer( $site, $lang, $dbName, $relPath ) {
	global $wmfSwiftConfig; // from PrivateSettings.php

	$auth = new CF_Authentication(
		$wmfSwiftConfig['user'],
		$wmfSwiftConfig['key'],
		NULL,
		$wmfSwiftConfig['authUrl']
	);

	try {
		$auth->authenticate();
	} catch ( Exception $e ) {
		wfDebugLog( 'swiftThumb', "Could not establish a connection to Swift." );
		return null;
	}

	$conn = new CF_Connection( $auth );

	$wikiId = "{$site}-{$lang}";

	$wmfSwiftBigWikis = array( # DO NOT change without proper migration first
		'commonswiki', 'dewiki', 'enwiki', 'fiwiki', 'frwiki', 'hewiki', 'huwiki', 'idwiki',
		'itwiki', 'jawiki', 'rowiki', 'ruwiki', 'thwiki', 'trwiki', 'ukwiki', 'zhwiki'
	);

	// Get the full swift container name, including any shard suffix
	$name = "{$wikiId}-local-thumb";
	if ( in_array( $dbName, $wmfSwiftBigWikis ) ) {
		// Code stolen from FileBackend::getContainerShard()
		if ( preg_match( "!^(?:[^/]{2,}/)*[0-9a-f]/(?P<shard>[0-9a-f]{2})(?:/|$)!", $relPath, $m ) ) {
			$name .= '.' . $m['shard'];
		} else {
			throw new MWException( "Can't determine shard of path '$relPath' for '$wikiId'." );
		}
	}

	try {
		$container = $conn->get_container( $name );
	} catch ( NoSuchContainerException $e ) { // container not created yet
		$container = null;
		wfDebugLog( 'swiftThumb', "Could not access `{$name}`; container does not exist." );
	}

	return $container;
}


/**
 * @param $file File
 * @param $thumb MediaTransformOutput|null
 * @param $tmpThumbPath string FS path
 * @param $thumbPath string Storage path
 * @return true
 */
function wmfOnFileTransformed( File $file, $thumb, $tmpThumbPath, $thumbPath ) {
	global $site, $lang;

	$backend = FileBackendGroup::singleton()->get( 'local-swift' );
	// Get the equivalent swift storage path for the NFS one
	list( $b, $container, $path ) = FileBackend::splitStoragePath( $thumbPath );
	$swiftThumbPath = $backend->getRootStoragePath() . "/$container/$path";

	$status = $backend->prepare( array( 'dir' => dirname( $swiftThumbPath ) ) );
	$status->merge( $backend->store(
		array( 'src' => $tmpThumbPath, 'dst' => $swiftThumbPath ),
		array( 'nonJournaled' => 1, 'nonLocking' => 1, 'allowStale' => 1 ) ) );
	if ( !$status->isOK() ) {
		wfDebugLog( 'swiftThumb', "Could not store thumbnail." .
			"Site: `{$site}` Lang: `{$lang}` src: `{$tmpThumbPath}` dst: `{$swiftThumbPath}`" );
	}

	return true;
}

/**
 * @param $file File
 * @param $archiveName string|false
 * @return true
 */
function wmfOnLocalFilePurgeThumbnails( File $file, $archiveName ) {
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
		wfDebugLog( 'swiftThumb', "Could not get thumbnail listing." .
			"Site: `{$site}` Lang: `{$lang}` ThumbRel: `{$thumbRel}/`" );
	} else {
		$ops = array();
		wfProfileIn( __METHOD__ . '-list' );
		foreach ( $list as $relFile ) {
			$ops[] = array( 'op' => 'delete', 'src' => "{$thumbDir}/{$relFile}" );
		}
		wfProfileOut( __METHOD__ . '-list' );
		wfProfileIn( __METHOD__ . '-purge' );
		$status = $backend->doQuickOperations( $ops );
		if ( !$status->isOK() ) {
			wfDebugLog( 'swiftThumb', "Could not delete all thumbnails from listing." .
				"Site: `{$site}` Lang: `{$lang}` ThumbRel: `{$thumbRel}/`" );
		}
		wfProfileOut( __METHOD__ . '-purge' );
	}

	return true;
}
