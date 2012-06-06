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

$wgHooks['FileTransformed'][] = 'wmfOnFileTransformed';
$wgHooks['LocalFilePurgeThumbnails'][] = 'wmfOnLocalFilePurgeThumbnails';

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
	global $wgUseSquid, $site, $lang;

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
		$urls = array();
		wfProfileIn( __METHOD__ . '-list' );
		foreach ( $list as $relFile ) {
			$ops[] = array( 'op' => 'delete', 'src' => "{$thumbDir}/{$relFile}" );
			$urls[] = ( $archiveName !== false )
				? $file->getArchiveThumbUrl( $archiveName, $relFile )
				: $file->getThumbUrl( $relFile );
		}
		wfProfileOut( __METHOD__ . '-list' );
		wfProfileIn( __METHOD__ . '-purge' );
		$status = $backend->doQuickOperations( $ops );
		if ( !$status->isOK() ) {
			wfDebugLog( 'swiftThumb', "Could not delete all thumbnails from listing." .
				"Site: `{$site}` Lang: `{$lang}` ThumbRel: `{$thumbRel}/`" );
		}
		wfProfileOut( __METHOD__ . '-purge' );
		if ( $wgUseSquid ) {
			SquidUpdate::purge( $urls );
		}
	}

	return true;
}
