<?php
error_reporting( 0 );

require_once( __DIR__ . '/defines.php' );
require_once( __DIR__ . '/MWRealm.php' );
require_once( __DIR__ . '/vendor/autoload.php' );

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
	$phpFilename = getRealmSpecificFilename(
		MEDIAWIKI_DEPLOYMENT_DIR . '/wikiversions.php'
	);
	$wikiversions = include( $phpFilename );
	if ( !is_array( $wikiversions ) ) {
		print "Unable to open $phpFilename.\n";
		exit( 1 );
	}
	if ( empty( $wikiversions[$dbName] ) ) {
		print "$cdbFilename has no version entry for `$dbName`.\n";
		exit( 1 );
	}
	return $wikiversions[$dbName];
}
