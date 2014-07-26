<?php
/**
 * Get the location of the correct version of a MediaWiki web
 * entry-point file given environmental variables such as the server name.
 * This function should only be called on web views.
 *
 * If the wiki doesn't exist, then wmf-config/missing.php will
 * be included (and thus displayed) and PHP will exit.
 *
 * If it does, then this function also has some other effects:
 * (a) Sets the $IP global variable (path to MediaWiki)
 * (b) Sets the MW_INSTALL_PATH environmental variable
 * (c) Changes PHP's current directory to the directory of this file.
 *
 * @param $file string File path (relative to MediaWiki dir)
 * @param $wiki string Force the Wiki ID rather than detecting it
 * @return string Absolute file path with proper MW location
 */
function getMediaWiki( $file, $wiki = null ) {
	global $IP;
	require_once( __DIR__ . '/MWMultiVersion.php' );

	if ( $wiki == null ) {
		$scriptName = @$_SERVER['SCRIPT_NAME'];
		$serverName = @$_SERVER['SERVER_NAME'];
		# Upload URL hit (to upload.wikimedia.org rather than wiki of origin)...
		if ( $scriptName === '/w/thumb.php' && $serverName === 'upload.wikimedia.org' ) {
			$multiVersion = MWMultiVersion::initializeForUploadWiki( $_SERVER['PATH_INFO'] );
		# Regular URL hit (wiki of origin)...
		} else {
			$multiVersion = MWMultiVersion::initializeForWiki( $serverName );
		}
	} else {
		$multiVersion = MWMultiVersion::initializeFromDBName( $wiki );
	}

	# Wiki doesn't exist yet?
	if ( $multiVersion->isMissing() ) {
		header( "Cache-control: no-cache" ); // same hack as CommonSettings.php
		include( MULTIVER_404SCRIPT_PATH_APACHE );
		exit;
	}

	# Get the MediaWiki version running on this wiki...
	$version = $multiVersion->getVersion();

	# Get the correct MediaWiki path based on this version...
	$IP = MULTIVER_COMMON_APACHE . "/$version";

	chdir( $IP );
	putenv( "MW_INSTALL_PATH=$IP" );

	return "$IP/$file";
}

/**
 * Get the location of the correct version of a MediaWiki CLI
 * entry-point file given the --wiki parameter passed in.
 *
 * This also has some other effects:
 * (a) Sets the $IP global variable (path to MediaWiki)
 * (b) Sets the MW_INSTALL_PATH environmental variable
 * (c) Changes PHP's current directory to the directory of this file.
 *
 * @param $file string File path (relative to MediaWiki dir)
 * @return string Absolute file path with proper MW location
 */
function getMediaWikiCli( $file ) {
	global $IP;

	require_once( __DIR__ . '/MWMultiVersion.php' );
	$multiVersion = MWMultiVersion::getInstance();
	if( !$multiVersion ) {
		$multiVersion = MWMultiVersion::initializeForMaintenance();
	}
	if ( $multiVersion->getDatabase() === 'testwiki' ) {
		define( 'TESTWIKI', 1 );
	}

	# Get the MediaWiki version running on this wiki...
	$version = $multiVersion->getVersion();

	# Get the correct MediaWiki path based on this version...
	$IP = dirname( __DIR__ ) . "/$version";

	putenv( "MW_INSTALL_PATH=$IP" );

	return "$IP/$file";
}
