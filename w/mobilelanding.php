<?php
putenv( 'MW_LANG=en' ); // notify MWMultiVersion

require_once __DIR__ . '/../multiversion/MWMultiVersion.php';
require MWMultiVersion::getMediaWiki( 'includes/WebStart.php' );

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

// note that the following will override any Cache-Control set earlier
// in extension code called above
header( 'Cache-Control: public, s-maxage=900, max-age=900' );
header( "HTTP/1.1 $code $message" );
if ( $redirect !== false ) {
	header( 'Location: ' . $redirect );
}
header( 'Vary: X-Forwarded-Proto,X-CS,X-Subdomain,Accept-Language' );
header( 'Content-Type: text/html; charset=utf-8' );
