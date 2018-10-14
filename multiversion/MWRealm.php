<?php
require_once __DIR__ . '/MWWikiversions.php';

global $wmfCluster, $wmfDatacenter, $wmfRealm, $wmgRealm;

$wmfCluster = trim( file_get_contents( '/etc/wikimedia-cluster' ) );
if ( $wmfCluster === 'labs' ) {
	$wmfRealm = $wmgRealm = 'labs';
	$wmfDatacenter = 'eqiad';
} else {
	$wmfRealm = $wmgRealm = 'production';
	$wmfDatacenter = $wmfCluster;
}

/**
 * Get the filename for the current realm/datacenter.
 * The full path to the file is returned, not just the filename
 *
 * @deprecated Use explicit conditional instead, e.g. based on `$wmfRealm`.
 * @param string $filename Full path to file
 * @return string Full path to file to be used
 */
function getRealmSpecificFilename( $filename ) {
	return $filename;
}
