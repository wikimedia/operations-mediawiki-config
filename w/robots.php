<?php
define( 'MW_NO_SESSION', 1 );
define( 'MW_ENTRY_POINT', 'static' );
require_once __DIR__ . '/../multiversion/MWMultiVersion.php';
require MWMultiVersion::getMediaWiki( 'includes/WebStart.php' );

use MediaWiki\MediaWikiServices;

$page = MediaWikiServices::getInstance()
	->getWikiPageFactory()
	->newFromTitle( Title::newFromText( 'MediaWiki:robots.txt' ) );

header( 'Content-Type: text/plain; charset=utf-8' );
header( 'X-Article-ID: ' . $page->getId() );
header( 'X-Language: ' . $lang );
header( 'X-Site: ' . $site );
header( 'Vary: X-Subdomain' );

$robotsfile = MEDIAWIKI_DEPLOYMENT_DIR . '/robots.txt';
$robots = fopen( $robotsfile, 'rb' );
$robotsfilestats = fstat( $robots );
$mtime = $robotsfilestats['mtime'];
$extratext = '';

header( 'Cache-Control: s-maxage=3600, must-revalidate, max-age=0' );

$dontIndex = "User-agent: *\nDisallow: /\n";

if ( $page->exists() ) {
	$content = $page->getContent();
	$extratext = ( $content instanceof TextContent ) ? $content->getText() : '';
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
