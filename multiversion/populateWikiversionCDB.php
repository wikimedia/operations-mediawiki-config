<?php
if ( php_sapi_name() !== 'cli' ) {
	die( "This script can only be run from the command line.\n" );
}

error_reporting( E_ALL );

/*
 * Populate wikiversions.cdb file using all the items in all.dblist
 * as keys, each having a value of the specified MediaWiki version.
 * This means that all will be configured to run that version.
 *
 * The first argument is the version, typically of the format "php-X.XX".
 *
 * @return void
 */
function populateWikiversionsCDB() {
	global $argv;
	$common = '/home/wikipedia/common';

	$argsValid = false;
	if ( count( $argv ) >= 2 ) {
		$version = $argv[1]; // e.g. "php-X.XX"
		if ( preg_match( '/^php-(\d+\.\d+|trunk)$/', $version ) ) {
			$argsValid = true;
		}
	}

	if ( !$argsValid ) {
		die( "Usage: populateWikiVersionsCDB.php php-X.XX\n" );
	}

	$path = "$common/all.dblist";
	$dbList = explode( "\n", file_get_contents( $path ) );
	$dbList = array_filter( $dbList ); // remove whitespace entry
	if ( !count( $dbList ) ) {
		die( "Unable to read all.dblist." );
	}

	$wikiVersionList = '';
	foreach ( $dbList as $dbName ) {
		$wikiVersionList .= "$dbName $version\n";
	}

	$path = "$common/wikiversions.dat";
	if ( !file_put_contents( $path, $wikiVersionList ) ) {
		die( "Unable to write to wikiversions.dat.\n" );
	}

	$ret = 1; // failed by default?
	passthru( sprintf(
		"$common/multiversion/cdbmake-12.sh %s %s < $common/wikiversions.dat",
		"$common/wikiversions.cdb",
		"$common/wikiversions.dat.tmp"
	) );
	if ( $ret != 0 ) {
		die( "Unable to write to wikiversions.cdb.\n" );
	}
}

populateWikiversionsCDB();
