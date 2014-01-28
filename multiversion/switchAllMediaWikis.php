#!/usr/bin/env php
<?php

error_reporting( E_ALL );

require_once( __DIR__ . '/defines.php' );
require_once( __DIR__ . '/MWWikiversions.php' );
require_once( __DIR__ . '/MWRealm.php' );

/*
 * This script switches all wikis in a .dblist file running one MediaWiki
 * version to another MediaWiki version. Since this only changes the wikiversions.dat
 * and wikiversions.cdb files on tin, they will still need to be synced to push
 * the upgrade/downgrade to the apaches.
 *
 * The first argument is the old version, typically of the format "php-X.XXwmfX".
 * If "all" is given, then all wikis will be switched over.
 * The second argument is the new version, typically of the format "php-X.XXwmfX".
 * The third argument is the name of a .dblist file under the common/ dir.
 *
 * @return void
 */
function switchAllMediaWikis() {
	global $argv;
	$common = MULTIVER_COMMON_HOME;
	$datPath = getRealmSpecificFilename( MULTIVER_CDB_DIR_HOME . '/wikiversions.dat' );

	$argsValid = false;
	if ( count( $argv ) >= 4 ) {
		$oldVersion = $argv[1]; // e.g. "php-X.XX"
		$newVersion = $argv[2]; // e.g. "php-X.XX"
		if ( preg_match( '/^php-(\d+\.\d+wmf\d+|master)$/', $newVersion ) ) {
			$argsValid = true;
		}
		$dbListName = $argv[3]; // e.g. "all.dblist"
	}

	if ( !$argsValid ) {
		print "Usage: switchAllMediaWikis php-X.XXwmfX php-X.XXwmfX <name>.dblist\n";
		exit( 1 );
	}

	if ( !is_dir( "$common/$newVersion" ) ) {
		print "The directory `$common/$newVersion` does not exist.\n";
		exit( 1 );
	}

	# Read in .dblist file into an array with dbnames as keys...
	$dbList = MWWikiversions::readDbListFile( "$common/$dbListName" );

	# Get all the wikiversion rows in wikiversions.dat...
	$versionRows = MWWikiversions::readWikiVersionsFile( $datPath );

	$count = 0;
	$newWikiVersionsData = "";
	# Go through all the rows and do the replacements...
	foreach ( $versionRows as $row ) {
		list( $dbName, $version, $extVersion, $comment ) = $row;
		if ( isset( $dbList[$dbName] ) // wiki is in the .dblist file
			&& ( $version === $oldVersion || $oldVersion === 'all' ) )
		{
			# Change the row and add it to the list
			$newWikiVersionsData .= MWWikiversions::lineFromRow(
				array( $dbName, $newVersion, $extVersion, $comment )
			);
			++$count;
		} else {
			# Just add the row back to the list
			$newWikiVersionsData .= MWWikiversions::lineFromRow( $row );
		}
		$newWikiVersionsData .= "\n";
	}

	# Backup old wikiversions.dat...
	$retVal = 1;
	passthru( "cd $common/multiversion && ./backupWikiversions", $retVal );
	( $retVal == 0 ) or exit( 1 );

	# Update wikiversions.dat...
	if ( !file_put_contents( $datPath, $newWikiVersionsData, LOCK_EX ) ) {
		print "Unable to write to $datPath.\n";
		exit( 1 );
	}
	echo "Updated $datPath.\n";

	# Rebuild wikiversions.cdb...
	$retVal = 1;
	passthru( "cd $common/multiversion && ./refreshWikiversionsCDB", $retVal );
	( $retVal == 0 ) or exit( 1 );

	echo "Re-configured $count wiki(s) from $oldVersion to $newVersion.\n";
	echo "The DB list contained " . count( $dbList ) . " wiki(s).\n";
}
