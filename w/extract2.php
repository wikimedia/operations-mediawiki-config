<?php
$lang = 'meta';
putenv( "MW_LANG={$lang}" ); // notify MWMultiVersion

require_once __DIR__ . '/../multiversion/MWMultiVersion.php';
require MWMultiVersion::getMediaWiki( 'includes/WebStart.php' );

$wgTitle = Title::newFromText( 'API_listing_template' );
$rawHtml = (new Article( $wgTitle ))->getPage()->getContent()->getNativeData();

$lastmod = gmdate( 'D, j M Y H:i:s', wfTimestamp( TS_UNIX, $wgArticle->getTouched() ) ) . ' GMT';
header( 'Content-Type: text/html; charset=utf-8' );
header( 'Cache-Control: s-maxage=3600, must-revalidate, max-age=0' );
header( "Last-modified: $lastmod" );
echo $rawHtml;
