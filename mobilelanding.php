<?php
putenv( "MW_LANG=en" ); // notify MWMultiVersion

include '/apache/common/w/MWVersion.php';
include getMediaWiki( 'includes/WebStart.php' );

$code = '302';
$state = Extensions\ZeroRatedMobileAccess\PageRenderingHooks::getState();
$redirect = $state->getLandingRedirect();

// Temporary fix as it duplicates the same call in getLandingRedirect()
// The fix ensures that there is no relative redirects
$redirect = wfExpandUrl( $redirect );

$redirect = filter_var( $redirect, FILTER_VALIDATE_URL );
if ( $redirect === false ) {
	$code = '500';
}
$message = HttpStatus::getMessage( $code );

//$lastmod = gmdate( 'D, j M Y H:i:s', wfTimestamp( TS_UNIX, $wgArticle->getTouched() ) ) . ' GMT';
//header( "Last-modified: $lastmod" );

header( 'Cache-Control: s-maxage=3600, must-revalidate, max-age=0' );
header( "HTTP/1.1 $code $message" );
if ( $redirect !== false ) {
	header( 'Location: ' . $redirect );
}
header( 'Vary: X-Subdomain,X-CS,Cookie' );
header( 'Content-Type: text/html; charset=utf-8' );
