<?php

# WARNING: This file is publically viewable on the web. Do not put private data here.

#
# This file hold the configuration for NFS and Swift files backends
# for LABS
#

# Whenever the timeline extension is provided an empty string as a file
# backend, it will gracefully fallback to a FSFileBackend pointing to
# "{$wgUploadDirectory}/timeline"
if ( isset( $wgTimelineSettings ) ) {
	$wgTimelineSettings->fileBackend = '';
}

// labs does not have swift yet as of 20120525 -- hashar

$wgLocalFileRepo = array(
		'class' => 'LocalRepo',
		'name' => 'local',
		'directory' => $wgUploadDirectory,
		'url' => $wgUploadBaseUrl ? $wgUploadBaseUrl . $wgUploadPath : $wgUploadPath,
		'scriptDirUrl' => $wgScriptPath,
		'hashLevels' => 2,
		'thumbScriptUrl' => $wgThumbnailScriptPath,
		'transformVia404' => true,
		'initialCapital' => $wgCapitalLinks,
		'deletedDir' => "/data/project/upload7/private/archive/$site/$lang",
		'deletedHashLevels' => 3,
		'abbrvThreshold'    => 160,
		'isPrivate'	    => $wmgPrivateWiki
);

# New commons settings
if ( $wgDBname != 'commonswiki' ) {
	$wgForeignFileRepos[] = array(
		'class'            => 'ForeignDBViaLBRepo',
		'name'             => 'shared',
		'directory'        => '/data/project/upload7/wikipedia/commons',
		'url'              => "//upload.beta.wmflabs.org/wikipedia/commons",
		'hashLevels'       => 2,
		'thumbScriptUrl'   => false,
		'transformVia404'  => true,
		'hasSharedCache'   => true,
		'descBaseUrl'      => "//commons.wikimedia.beta.wmflabs.org/wiki/File:",
		'scriptDirUrl'     => "//commons.wikimedia.beta.wmflabs.org/w",
		'fetchDescription' => true,
		'wiki'             => 'commonswiki',
		'initialCapital'   => true,
		'abbrvThreshold'   => 160,
	);
}

# not sure what to use for the basePath; experimenting with the value below
$wgFileBackends[] = array(
	'name'           => 'gwtoolset-backend',
	'class'          => 'FSFileBackend',
	'lockManager'    => 'nullLockManager',
	'fileMode'       => 0644,
	'basePath'       => "/data/project/upload7/private/gwtoolset/$site/$lang"
);
