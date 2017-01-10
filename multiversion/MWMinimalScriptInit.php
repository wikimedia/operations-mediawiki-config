<?php
call_user_func( function () {
	$user = posix_getpwuid( posix_geteuid() );
	if ( PHP_SAPI !== 'cli' || $user['name'] !== getenv( 'MEDIAWIKI_WEB_USER' ) ) {
		fprintf( STDERR, "Bad user or SAPI.\n" );
		die( 1 );
	}
} );
require_once __DIR__ . '/MWMultiVersion.php';
@require_once MWMultiVersion::getMediaWiki( 'maintenance/commandLine.inc', getenv( 'MW_WIKI' ) ?: 'testwiki' );
