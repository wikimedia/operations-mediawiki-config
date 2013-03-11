<?php
define( "MEDIAWIKI", true );

include "/apache/common/w/MWVersion.php";
include getMediaWiki( "includes/WebStart.php" );

$wgTitle = Title::newFromText( 'Mediawiki:robots.txt' );
$wgArticle = new Article( $wgTitle, 0 );

header( 'Content-Type: text/plain; charset=utf-8' );
header( 'X-Article-ID: ' . $wgArticle->getID() );
header( 'X-Language: ' . $lang );
header( 'X-Site: ' . $site );

$robotsfile = "/apache/common/robots.txt";
$robots = fopen( $robotsfile, 'rb' );
$text = '';

header( 'Cache-Control: s-maxage=3600, must-revalidate, max-age=0' );

if ( $wgArticle->getID() != 0 ) {
	$text =  $wgArticle->getContent( false ) ;
	$lastmod = gmdate( 'D, j M Y H:i:s', wfTimestamp( TS_UNIX,  $wgArticle->getTouched() ) ) . ' GMT';
	header( "Last-modified: $lastmod" );
} elseif( $wmfRealm == 'labs' ) {
	echo "User-agent: *\nDisallow: /\n";
} else {
	$stats = fstat( $robots );

	$lastmod = gmdate( 'D, j M Y H:i:s', $stats['mtime'] ) . ' GMT';
	header( "Last-modified: $lastmod" );
	header( "Content-Length: " . filesize( $robotsfile ) );
}
fpassthru( $robots );

echo "#\n#\n#----------------------------------------------------------#\n#\n#\n#\n" . $text;
