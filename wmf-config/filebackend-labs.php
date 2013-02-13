<?php

# WARNING: This file is publically viewable on the web. Do not put private data here.

#
# This file hold the configuration for NFS and Swift files backends
# for LABS
#

// labs does not have swift yet as of 20120525 -- hashar


$wgLocalFileRepo = array(
		'class' => 'LocalRepo',
		'name' => 'local',
		'directory' => $wgUploadDirectory,
		'url' => $wgUploadBaseUrl ? $wgUploadBaseUrl . $wgUploadPath : $wgUploadPath,
		'scriptDirUrl' => $wgScriptPath,
		'hashLevels' => 2,
		'thumbScriptUrl' => $wgThumbnailScriptPath,
		'transformVia404' => false,
		'initialCapital' => $wgCapitalLinks,
		'deletedDir' => "/mnt/upload7/private/archive/$site/$lang",
		'deletedHashLevels' => 3,
// TODO: Thumbdir?
//	'thumbDir' => str_replace( '/mnt/upload7', '/mnt/thumbs', "$wgUploadDirectory/thumb" ),
);

# New commons settings
if ( $wgDBname != 'commonswiki' ) {
	$wgForeignFileRepos[] = array(
		'class'            => 'ForeignDBViaLBRepo',
		'name'             => 'shared',
		'directory'        => '/mnt/upload7/wikipedia/commons',
		'url'              => "//upload.beta.wmflabs.org/wikipedia/commons",
		'hashLevels'       => 2,
		'thumbScriptUrl'   => "//commons.wikimedia.beta.wmflabs.org/w/thumb.php",
		'transformVia404'  => false,
		'hasSharedCache'   => true,
		'descBaseUrl'      => "//commons.wikimedia.beta.wmflabs.org/wiki/File:",
		'scriptDirUrl'     => "//commons.wikimedia.beta.wmflabs.org/w",
		'fetchDescription' => true,
		'wiki'             => 'commonswiki',
		'initialCapital'   => true,
		'thumbDir'         => '/mnt/upload7/wikipedia/commons/thumb',
	);
}

/*
$wgForeignFileRepos[] = array(
   'class'                   => 'ForeignAPIRepo',
   'name'                    => 'wikimediacommons',
   'apibase'                 => 'http://commons.wikimedia.org/w/api.php',
   'hashLevels'              => 2,
   'fetchDescription'        => true,
   'descriptionCacheExpiry'  => 43200,
   'apiThumbCacheExpiry'     => 86400,
);
*/

