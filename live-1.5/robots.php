<?php
//$lang = "meta";
define( "MEDIAWIKI", true );
#include_once("CommonSettings.php");
#include_once("/apache/common/wmf-deployment/includes/ProfileStub.php" );
#include_once("/apache/common/wmf-deployment/includes/Defines.php" );
#include_once("/apache/common/wmf-deployment/wmf-config/CommonSettings.php");
#include_once("Setup.php");

include "/apache/common/live-1.5/MWVersion.php";
include getMediaWiki("includes/WebStart.php");

/*
// determine language code
$secure = getenv( 'MW_SECURE_HOST' );
if ( (@$_SERVER['SCRIPT_NAME']) == '/w/thumb.php' && (@$_SERVER['SERVER_NAME']) == 'upload.wikimedia.org' ) {
        $pathBits = explode( '/', $_SERVER['PATH_INFO'] );
        $site = $pathBits[1];
        $lang = $pathBits[2];
} elseif (php_sapi_name() == 'cgi-fcgi') {
        if (!preg_match('/^([^.]+).([^.]+).*$/', $_SERVER['SERVER_NAME'], $m))
                die("invalid hostname");

        $lang = $m[1];
        $site = $m[2];

        if (in_array($lang, array("commons", "grants", "sources", "wikimania", "wikimania2006", "foundation", "meta")))
                $site = "wikipedia";
} elseif( $secure ) {
        if (!preg_match('/^([^.]+).([^.]+).*$/', $secure, $m))
                die("invalid hostname");

        $lang = $m[1];
        $site = $m[2];

        if (in_array($lang, array("commons", "grants", "sources", "wikimania", "wikimania2006", "foundation", "meta")))
                $site = "wikipedia";
} else {
    if ( !isset( $site ) ) {
            $site = "wikipedia";
            if ( !isset( $lang ) ) {

                    $server = $_SERVER['SERVER_NAME'];
                    $docRoot = $_SERVER['DOCUMENT_ROOT'];
                    if ( preg_match( '/^\/usr\/local\/apache\/(?:htdocs|common\/docroot)\/([a-z]+)\.org/', $docRoot, $matches ) ) {
                            $site = $matches[1];
                            if ( preg_match( '/^(.*)\.' . preg_quote( $site ) . '\.org$/', $server, $matches ) ) {
                                    $lang = $matches[1];
                                    // For some special subdomains, like pa.us
                                    $lang = str_replace( '.', '-', $lang );
                            } else {
                                    die( "Invalid host name, can't determine language" );
                            }
                    } elseif ( preg_match( "/^\/usr\/local\/apache\/(?:htdocs|common\/docroot)\/([a-z0-9\-_]*)$/", $docRoot, $matches ) ) {
                            $site = "wikipedia";
                            $lang = $matches[1];
                    } else {
                            die( "Invalid host name (docroot=" . $_SERVER['DOCUMENT_ROOT'] . "), can't determine language" );
                    }
            }
    }
}
*/

$wgTitle = Title::newFromText( 'Mediawiki:robots.txt' );
$wgArticle = new Article( $wgTitle );

header( 'Content-Type: text/plain; charset=utf-8');
header( 'X-Article-ID: ' . $wgArticle->getID() );
header( 'X-Language: ' . $lang );
header( 'X-Site: ' . $site );

$robotsfile = "/apache/common/robots.txt";
$robots = fopen( $robotsfile, 'rb' );
$text='';

header( 'Cache-Control: s-maxage=3600, must-revalidate, max-age=0' );

if ( $wgArticle->getID() != 0 ) {
	$text =  $wgArticle->getContent( false ) ;
	$lastmod = gmdate( 'D, j M Y H:i:s', wfTimestamp(TS_UNIX,  $wgArticle->getTouched() ) ) . ' GMT';
	header( "Last-modified: $lastmod" );
} else {
	$stats = fstat( $robots );

	$lastmod = gmdate( 'D, j M Y H:i:s', $stats['mtime'] ) . ' GMT';
	header( "Last-modified: $lastmod" );
	header("Content-Length: " . filesize($robotsfile));
}
fpassthru( $robots );

echo "#\n#\n#----------------------------------------------------------#\n#\n#\n#\n" . $text;
?>
