<?php
// Only allow viewing of files listed in filelist.php

use function Wikimedia\MWConfig\Noc\getParsedRequestUrl;
use function Wikimedia\MWConfig\Noc\wmfNocHeader;

require_once __DIR__ . '/filelist.php';
require_once __DIR__ . '/../../../src/Noc/utils.php';

$confFiles = wmfLoadRoutes();
$parsedUrl = getParsedRequestUrl();
$uriPath = $parsedUrl['path'] ?? '';
// We have a query string. Redirect to the canonical url.
if ( $parsedUrl['query'] ?? false ) {
	wmfNocHeader( 'HTTP/1.1 302 Found' );
	wmfNocHeader( 'Location: ' . $uriPath );
	echo "Redirect to $uriPath\n";
	return;
}
$fileName = $confFiles->getDiskPathByUrl( $uriPath );
if ( !$fileName || !file_exists( $fileName ) ) {
	wmfNocHeader( "HTTP/1.1 404 Not Found" );
	echo "File not found.\n";
	return;
}

$lastModified = date( 'D, d M Y H:m:s T', filemtime( $fileName ) );
// Cache the value for 5 minutes.
wmfNocHeader( 'Cache-control: max-age: 300, s-maxage: 300, must-revalidate' );
wmfNocHeader( 'Last-Modified: ' . $lastModified );
wmfNocHeader( 'Content-type: text/plain' );
echo file_get_contents( $fileName );
