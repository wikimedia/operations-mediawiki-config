<?php
require_once __DIR__ . '/../../../multiversion/MWMultiVersion.php';
require MWMultiVersion::getMediaWiki( 'includes/WebStart.php', 'enwiki' );

global $wgServer;

$target = '/wiki/Main_Page';
$urlParts = explode( '.', $wgServer );
$redirect = '' .
	(
		( count( $urlParts ) !== 3 || $urlParts[0] === '//m' )
			? $wgServer
			: $urlParts[0] . '.m.' . $urlParts[1] . '.' . $urlParts[2]
	) . $target;

$message = HttpStatus::getMessage( '302' );
header( 'Cache-Control: public, s-maxage=900, max-age=900' );
header( "HTTP/1.1 $code $message" );
header( 'Vary: X-Forwarded-Proto,X-CS,X-Subdomain,Accept-Language' );
header( 'Content-Type: text/html; charset=utf-8' );
header( 'Location: ' . $redirect );
