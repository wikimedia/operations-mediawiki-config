<?php
define( 'MW_NO_SESSION', 1 );
require_once __DIR__ . '/../multiversion/MWMultiVersion.php';
require MWMultiVersion::getMediaWiki( 'includes/WebStart.php' );

use MediaWiki\MediaWikiServices;

/**
 * @param string $text
 */
function faviconShowError( $text ) {
	header( 'Content-Type: text/html; charset=utf-8' );
	echo "<!DOCTYPE html>\n<p>" . htmlspecialchars( $text ) . "</p>\n";
}

/**
 * Stream the favicon!
 */
function streamFavicon() {
	global $wgFavicon;
	wfResetOutputBuffers();
	if ( $wgFavicon === '/favicon.ico' ) {
		# That's not very helpful, that's where we are already
		header( 'HTTP/1.1 404 Not Found' );
		faviconShowError( '$wgFavicon is configured incorrectly, ' .
			'it must be set to something other than /favicon.ico' );
		return;
	}

	$req = RequestContext::getMain()->getRequest();
	if ( $req->getHeader( 'X-Favicon-Loop' ) !== false ) {
		header( 'HTTP/1.1 500 Internal Server Error' );
		faviconShowError( 'Proxy forwarding loop detected' );
		return;
	}

	$url = wfExpandUrl( $wgFavicon, PROTO_CANONICAL );
	$client = MediaWikiServices::getInstance()
		->getHttpRequestFactory()
		->create( $url );
	$client->setHeader( 'X-Favicon-Loop', '1' );

	$status = $client->execute();
	if ( !$status->isOK() ) {
		header( 'HTTP/1.1 500 Internal Server Error' );
		faviconShowError( "Failed to fetch URL \"$url\"" );
		return;
	}

	$content = $client->getContent();
	header( 'Content-Length: ' . strlen( $content ) );
	header( 'Content-Type: ' . $client->getResponseHeader( 'Content-Type' ) );
	header( 'Cache-Control: public' );
	header( 'Expires: ' . gmdate( 'r', time() + 86400 ) );
	echo $content;
}

streamFavicon();
