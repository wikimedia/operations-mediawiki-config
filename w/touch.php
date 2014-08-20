<?php
define( "MEDIAWIKI", true );

require( "/usr/local/apache/common/w/MWVersion.php" );
require( getMediaWiki( "includes/WebStart.php" ) );

function faviconShowError( $html ) {
	header( 'Content-Type: text/html; charset=utf-8' );
	echo "<html><body>$html</body></html>\n";
}

function streamAppleTouch() {
	global $wgAppleTouchIcon;
	wfResetOutputBuffers();
	if ( $wgAppleTouchIcon === false ) {
		# That's not very helpful, that's where we are already
		header( 'HTTP/1.1 404 Not Found' );
		faviconShowError( "\$wgAppleTouchIcon is configured incorrectly, " .
			"it must be set to something other than false \n" );
		return;
	}

	$req = RequestContext::getMain()->getRequest();
	if ( $req->getHeader( 'X-Favicon-Loop' ) !== false ) {
		header( 'HTTP/1.1 500 Internal Server Error' );
		faviconShowError( "Proxy forwarding loop detected" );
		return;
	}

	$url = wfExpandUrl( $wgAppleTouchIcon, PROTO_INTERNAL );
	$client = MWHttpRequest::factory( $url );
	$client->setHeader( 'X-Favicon-Loop', '1' );

	$status = $client->execute();
	if ( !$status->isOK() ) {
		header( 'HTTP/1.1 500 Internal Server Error' );
		faviconShowError( htmlspecialchars( "Failed to fetch URL \"$url\"" ) );
		return;
	}

	$content = $client->getContent();
	header( 'Content-Length: ' . strlen( $content ) );
	header( 'Content-Type: ' . $client->getResponseHeader( 'Content-Type' ) );
	header( 'Cache-Control: public' );
	header( 'Expires: ' . gmdate( 'r', time() + 86400 ) );
	echo $content;
}

streamAppleTouch();
