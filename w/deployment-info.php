<?php
require_once __DIR__ . '/../multiversion/MWMultiVersion.php';

header( 'Cache-Control: no-cache' );

$file = MEDIAWIKI_DEPLOYMENT_DIR . '/deployment-info.json';
$info = is_readable( $file ) ? json_decode( (string)file_get_contents( $file ), true ) : null;
if ( !is_array( $info ) ) {
	header( 'Content-Type: application/json; charset=utf-8' );
	http_response_code( 404 );
	echo "{}\n";
	return;
}

// Resolve before any output. MWMultiVersion writes a plain text error and exits
// if the request host does not map to a wiki.
$multiVersion = MWMultiVersion::initializeFromServerData(
	$_SERVER['SERVER_NAME'] ?? null,
	$_SERVER['SCRIPT_NAME'] ?? null,
	$_SERVER['PATH_INFO'] ?? null,
	$_SERVER['REQUEST_URI'] ?? null
);
$info['dbname'] = null;
$info['branch'] = null;
if ( !$multiVersion->isMissing() ) {
	// wikiversions holds "php-master" or "php-X"; the core branch is "master" or "wmf/X".
	$version = $multiVersion->getVersionNumber();
	$info['dbname'] = $multiVersion->getDatabase();
	$info['branch'] = $version === 'master' ? $version : "wmf/$version";
}

header( 'Content-Type: application/json; charset=utf-8' );
echo json_encode( $info, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ), "\n";
