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
 * @return string Absolute file path with proper MW location
 */
function getMediaWiki( $file ) {
	global $IP;
	require_once( dirname( __FILE__ ) . '/MWMultiVersion.php' );

	$scriptName = @$_SERVER['SCRIPT_NAME'];
	$serverName = @$_SERVER['SERVER_NAME'];
	$documentRoot = @$_SERVER['DOCUMENT_ROOT'];

	# Upload URL hit (to upload.wikimedia.org rather than wiki of origin)...
	if ( $scriptName === '/w/thumb.php' && $serverName === 'upload.wikimedia.org' ) {
		$multiVersion = MWMultiVersion::initializeForUploadWiki( $_SERVER['PATH_INFO'] );
	# Regular URL hit (wiki of origin)...
	} else {
		$multiVersion = MWMultiVersion::initializeForWiki( $serverName, $documentRoot );
	}

	# Wiki doesn't exist yet?
	if ( $multiVersion->isMissing() ) {
		header( "Cache-control: no-cache" ); // same hack as CommonSettings.php
		include( MULTIVER_404SCRIPT_PATH );
		exit;
	}

	# Get the MediaWiki version running on this wiki...
	$version = $multiVersion->getVersion();

	# MW_SECURE_HOST set from secure gateway?
	$secure = getenv( 'MW_SECURE_HOST' );
	$host = $secure ? $secure : $_SERVER['HTTP_HOST'];

	$IP = MULTIVER_COMMON . "/$version";

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

	require_once( dirname( __FILE__ ) . '/MWMultiVersion.php' );
	$multiVersion = MWMultiVersion::getInstance();
	if( !$multiVersion ) {
		$multiVersion = MWMultiVersion::initializeForMaintenance();
	}

	# Get the MediaWiki version running on this wiki...
	$version = $multiVersion->getVersion();

	# Get the correct MediaWiki path based on this version...
	$IP = dirname( dirname( __FILE__ ) ) . "/$version";

	putenv( "MW_INSTALL_PATH=$IP" );

	return "$IP/$file";
}
