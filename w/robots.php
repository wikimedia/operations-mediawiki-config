<?php
require_once __DIR__ . '/../multiversion/MWMultiVersion.php';
require MWMultiVersion::getMediaWiki( 'includes/WebStart.php' );

$wgTitle = Title::newFromText( 'Mediawiki:robots.txt' );
$page = WikiPage::factory( $wgTitle );

header( 'Content-Type: text/plain; charset=utf-8' );
header( 'X-Article-ID: ' . $page->getId() );
header( 'X-Language: ' . $lang );
header( 'X-Site: ' . $site );
header( 'Vary: X-Subdomain' );

$robotsfile = '/srv/mediawiki/robots.txt';
$robots = fopen( $robotsfile, 'rb' );
$robotsfilestats = fstat( $robots );
$mtime = $robotsfilestats['mtime'];
$extratext = '';

$zeroRated = isset( $_SERVER['HTTP_X_SUBDOMAIN'] ) && $_SERVER['HTTP_X_SUBDOMAIN'] === 'ZERO';

header( 'Cache-Control: s-maxage=3600, must-revalidate, max-age=0' );

$dontIndex = "User-agent: *\nDisallow: /\n";

if ( $zeroRated ) {
	echo $dontIndex;
} elseif ( $page->exists() ) {
	$extratext = ContentHandler::getContentText( $page->getContent() ) ?: '';
	// Take last modified timestamp of page into account
	$mtime = max( $mtime, wfTimestamp( TS_UNIX, $page->getTouched() ) );
} elseif ( $wmgRealm == 'labs' ) {
	echo $dontIndex;
}

$lastmod = gmdate( 'D, j M Y H:i:s', $mtime ) . ' GMT';
header( "Last-modified: $lastmod" );

fpassthru( $robots );

echo "#\n#\n#----------------------------------------------------------#\n#\n#\n#\n";
echo $extratext;
