<?php
require_once __DIR__ . '/../multiversion/defines.php';

header( 'Cache-Control: no-cache' );
header( 'Content-Type: application/json; charset=utf-8' );

$file = MEDIAWIKI_DEPLOYMENT_DIR . '/deployment-info.json';
if ( !is_readable( $file ) ) {
	http_response_code( 404 );
	echo "{}\n";
	return;
}

readfile( $file );
