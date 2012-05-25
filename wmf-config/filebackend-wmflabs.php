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
		'deletedDir' => "/mnt/upload6/private/archive/$site/$lang",
		'deletedHashLevels' => 3,
// TODO: Thumbdir?
//	'thumbDir' => str_replace( '/mnt/upload6', '/mnt/thumbs', "$wgUploadDirectory/thumb" ),
);

# New commons settings
if ( $wgDBname != 'commonswiki' ) {
	$wgForeignFileRepos[] = array(
		'class'            => 'ForeignDBViaLBRepo',
		'name'             => 'shared',
		'directory'        => '/mnt/upload6/wikipedia/commons',
		'url'              => "$urlprotocol//upload.beta.wmflabs.org/wikipedia/commons",
		'hashLevels'       => 2,
		'thumbScriptUrl'   => "$urlprotocol//commons.wikimedia.beta.wmflabs.org/w/thumb.php",
		'transformVia404'  => false,
		'hasSharedCache'   => true,
		'descBaseUrl'      => "$urlprotocol//commons.wikimedia.beta.wmflabs.org/wiki/File:",
		'scriptDirUrl'     => "$urlprotocol//commons.wikimedia.beta.wmflabs.org/w",
		'fetchDescription' => true,
		'wiki'             => 'commonswiki',
		'initialCapital'   => true,
		'thumbDir'         => '/mnt/upload6/wikipedia/commons/thumb',
	);

	# FIXME: should probably be in InitialiseSettings.php:
	$wgDefaultUserOptions['watchcreations'] = 1;
}
$wgForeignFileRepos[] = array(
   'class'                   => 'ForeignAPIRepo',
   'name'                    => 'wikimediacommons',
   'apibase'                 => 'http://commons.wikimedia.org/w/api.php',
   'hashLevels'              => 2,
   'fetchDescription'        => true,
   'descriptionCacheExpiry'  => 43200,
   'apiThumbCacheExpiry'     => 86400,
);


