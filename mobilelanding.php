<?php
putenv( "MW_LANG=en" ); // notify MWMultiVersion

include '/apache/common/w/MWVersion.php';
include getMediaWiki( 'includes/WebStart.php' );

$code = '302';
$state = ZeroBanner\PageRenderingHooks::getState();
$redirect = $state->getLandingRedirect();
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
header( 'Vary: X-Forwarded-Proto,X-CS' );
header( 'Content-Type: text/html; charset=utf-8' );
