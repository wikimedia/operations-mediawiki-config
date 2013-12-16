<?php
define( "MEDIAWIKI", true );

require_once( './MWVersion.php' );
require getMediaWiki( 'includes/WebStart.php' );

$wgTitle = Title::newFromText( 'Mediawiki:robots.txt' );
$wgArticle = new Article( $wgTitle, 0 );

header( 'Content-Type: text/plain; charset=utf-8' );
header( 'X-Article-ID: ' . $wgArticle->getID() );
header( 'X-Language: ' . $lang );
header( 'X-Site: ' . $site );
header( 'Vary: X-Subdomain' );

$robotsfile = '/usr/local/apache/common/robots.txt';
$robots = fopen( $robotsfile, 'rb' );
$text = '';

$zeroRated = isset( $_SERVER['HTTP_X_SUBDOMAIN'] ) && $_SERVER['HTTP_X_SUBDOMAIN'] === 'ZERO';

header( 'Cache-Control: s-maxage=3600, must-revalidate, max-age=0' );

$dontIndex = "User-agent: *\nDisallow: /\n";

if ( $zeroRated ) {
	echo $dontIndex;
} elseif ( $wgArticle->getID() != 0 ) {
	$text =  $wgArticle->getContent( false ) ;
	$lastmod = gmdate( 'D, j M Y H:i:s', wfTimestamp( TS_UNIX,  $wgArticle->getTouched() ) ) . ' GMT';
	header( "Last-modified: $lastmod" );
} elseif( $wmgRealm == 'labs' ) {
	echo $dontIndex;
} else {
	$stats = fstat( $robots );

	$lastmod = gmdate( 'D, j M Y H:i:s', $stats['mtime'] ) . ' GMT';
	header( "Last-modified: $lastmod" );
	header( "Content-Length: " . filesize( $robotsfile ) );
}
fpassthru( $robots );

echo "#\n#\n#----------------------------------------------------------#\n#\n#\n#\n" . $text;
