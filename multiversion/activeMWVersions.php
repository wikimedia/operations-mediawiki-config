<?php
error_reporting( 0 );

require_once( __DIR__ . '/defines.php' );
require_once( __DIR__ . '/MWRealm.php' );
require_once( __DIR__ . '/MWWikiversions.php' );

/*
 * Returns an array of all active MW versions (e.g. "x.xx").
 * Versions are read from /usr/local/apache/common-local/wikiversions.cdb.
 *
 * Given --home, versions are instead read from /a/common/wikiversions.cdb.
 * Given --withdb, each item in the list will be appended with '=' followed by
 * 		the DB name of *some* wiki that uses that version. Used to run maintenance scripts.
 * Given --extended, versions will include any extra suffix specified in wikiversions.cdb.
 * 		This may result in more items being listed than without.
 * Given --report, error messages would be displayed if this dies.
 *
 * @return array
 */
function getActiveWikiVersions() {
	global $argv;
	$options = $argv; // copy
	array_shift( $options ); // first item is this file

	if ( in_array( '--home', $options ) ) {
		$datPath = getRealmSpecificFilename( MULTIVER_CDB_DIR_HOME . '/wikiversions.dat' );
	} else {
		$datPath = getRealmSpecificFilename( MULTIVER_CDB_DIR_APACHE . '/wikiversions.dat' );
	}

	# Get all the wikiversion rows in wikiversions.dat...
	try {
		$versionRows = MWWikiversions::readWikiVersionsFile( $datPath );
	} catch( Exception $e ) {
		if ( in_array( '--report', $options ) ) {
			throw $e; // show error
		} else {
			die( 1 ); // silent death
		}
	}

	$result = $activeVersions = array();
	foreach ( $versionRows as $row ) {
		list( $dbName, $version, $extVersion, $comment ) = $row;
		if ( $extVersion !== '*' && in_array( '--extended', $options ) ) {
			$version .= "-$extVersion";
		}
		if ( !isset( $activeVersions[$version] ) ) { // already listed?
			$activeVersions[$version] = 1;

			$version = substr( $version, 4 ); // remove 'php-'
			if ( in_array( '--withdb', $options ) ) {
				$result[] = "{$version}={$dbName}";
			} else {
				$result[] = "{$version}";
			}
		}

	}

	return $result;
}
