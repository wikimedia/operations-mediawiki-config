<?php
error_reporting( 0 );

require_once( __DIR__ . '/defines.php' );
require_once( __DIR__ . '/MWRealm.php' );
require_once( __DIR__ . '/Cdb.php' );

/**
 * This script prints the MW version associated with a specified wikidb.
 */
if ( count( $argv ) < 2 ) {
	print "Usage: getMWVersion <dbname> \n";
	exit( 1 );
}
/**
 * Prints the MW version associated with a specified wikidb (as listed e.g. in all.dblist).
 * @param $dbName string
 * @return string MW code version (e.g. "php-x.xx" or "php-trunk")
 */
function getWikiVersion( $dbName ) {
	$cdbFilename = getRealmSpecificFilename(
		MULTIVER_CDB_DIR_APACHE . '/wikiversions.cdb'
	);
	try {
		$db = CdbReader::open( $cdbFilename );
	} catch ( CdbException $e ) {}

	if ( $db ) {
		$version = $db->get( "ver:$dbName" );
		$db->close();
		if ( $version !== false ) {
			return $version; // found version entry
		}
		print "$cdbFilename has no version entry for `$dbName`.\n";
		exit( 1 );
	}
	print "Unable to open $cdbFilename.\n";
	exit( 1 );
}

echo getWikiVersion( $argv[1] ) . "\n";
