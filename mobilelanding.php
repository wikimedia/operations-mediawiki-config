<?php
$lang = 'en';
putenv( "MW_LANG={$lang}" ); // notify MWMultiVersion

include '/apache/common/w/MWVersion.php';
include getMediaWiki( 'includes/WebStart.php' );

$code = '302';
$message = HttpStatus::getMessage( $code );

$state = Extensions\ZeroRatedMobileAccess\PageRenderingHooks::getState();
$redirect = $state->getLandingRedirect();

//$lastmod = gmdate( 'D, j M Y H:i:s', wfTimestamp( TS_UNIX, $wgArticle->getTouched() ) ) . ' GMT';
//header( "Last-modified: $lastmod" );

header( 'Cache-Control: s-maxage=3600, must-revalidate, max-age=0' );
header( "HTTP/1.1 $code $message" );
header( 'Location: ' . $redirect );
header( 'Content-Type: text/html; charset=utf-8' );
