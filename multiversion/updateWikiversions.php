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
function updateWikiversions() {
	global $argv;
	$common = MEDIAWIKI_STAGING_DIR;
	$jsonPath = getRealmSpecificFilename( MEDIAWIKI_STAGING_DIR . '/wikiversions.json' );

	if ( count( $argv ) !== 3 ) {
		print "Usage: updateWikiversions <name>.dblist php-X.XXwmfX\n";
		exit( 1 );
	}

	$dbListName = basename( $argv[1], '.dblist' );
	$dbList = MWWikiversions::readDbListFile( "$common/dblists/$dbListName.dblist" );

	$newVersion = $argv[2];
	if ( !preg_match( '/^php-(\d+\.\d+wmf\d+|master)$/', $newVersion ) || !is_dir( "$common/$newVersion" ) ) {
		print "Invalid version specifier: $newVersion\n";
		exit( 1 );
	}

	if ( file_exists( $jsonPath ) ) {
		$versionRows = MWWikiversions::readWikiVersionsFile( $jsonPath );
	} else {
		if ( $dbListName !== 'all' ) {
			echo "No $jsonPath file and not invoked with 'all'. Cowardly refusing to act.\n";
			exit( 1 );
		}
		echo "$jsonPath not found -- rebuilding from scratch!\n";
		$versionRows = array();
	}

	$inserted = 0;
	$migrated = 0;

	foreach ( $dbList as $dbName ) {
		if ( !isset( $versionRows[$dbName] ) ) {
			$inserted++;
		} else {
			$migrated++;
		}
		$versionRows[$dbName] = $newVersion;
	}

	$total = count( $versionRows );
	ksort( $versionRows );

	MWWikiversions::writeWikiVersionsFile( $jsonPath, $versionRows );
	echo "Updated $jsonPath: $inserted inserted, $migrated migrated.\n";
}
