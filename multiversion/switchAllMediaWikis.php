<?php

error_reporting( E_ALL );

require_once( __DIR__ . '/defines.php' );
require_once( __DIR__ . '/MWWikiversions.php' );
require_once( __DIR__ . '/MWRealm.php' );

/*
 * This script switches all wikis in a .dblist file running one MediaWiki
 * version to another MediaWiki version. Since this only changes the wikiversions.json
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
	$jsonPath = getRealmSpecificFilename( MULTIVER_CDB_DIR_HOME . '/wikiversions.json' );

	$argsValid = false;
	if ( count( $argv ) === 2 ) {
		$newVersion = $argv[1]; // e.g. "php-X.XX"
		if ( preg_match( '/^php-(\d+\.\d+wmf\d+|master)$/', $newVersion ) ) {
			$argsValid = true;
		}
		$dbListName = $argv[2]; // e.g. "all.dblist"
	}

	if ( !$argsValid ) {
		print "Usage: switchAllMediaWikis php-X.XXwmfX <name>.dblist\n";
		exit( 1 );
	}

	if ( !is_dir( "$common/$newVersion" ) ) {
		print "The directory `$common/$newVersion` does not exist.\n";
		exit( 1 );
	}

	# Read in .dblist file into an array with dbnames as keys...
	$dbList = MWWikiversions::readDbListFile( "$common/$dbListName" );

	# Get all the wikiversion rows in wikiversions.json...
	$versionRows = MWWikiversions::readWikiVersionsFile( $jsonPath );

	# Go through all the rows and do the replacements...
	foreach ( $dbList as $dbName ) {
		$versionRows[$dbName] = $newVersion;
	}

	MWWikiversions::writeWikiVersionsFile( $jsonPath, $newWikiVersions );
	echo "Updated $jsonPath.\n";

	# Rebuild wikiversions.cdb...
	$retVal = 1;
	passthru( "cd $common/multiversion && ./refreshWikiversionsCDB", $retVal );
	( $retVal == 0 ) or exit( 1 );

	$numSwitched = count( $dbList );
	echo "Switched $numSwitched wikis to $newVersion.\n";
}
