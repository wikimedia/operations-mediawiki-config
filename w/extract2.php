<?php
require_once __DIR__ . '/../multiversion/MWMultiVersion.php';
require MWMultiVersion::getMediaWiki( 'includes/WebStart.php', 'metawiki' );

$wgTitle = Title::newFromText( 'API_listing_template' );
$article = new Article( $wgTitle );
$rawHtml = $article->getPage()->getContent()->getNativeData();

$lastmod = gmdate( 'D, j M Y H:i:s', wfTimestamp( TS_UNIX, $article->getTouched() ) ) . ' GMT';
header( 'Content-Type: text/html; charset=utf-8' );
header( 'Cache-Control: s-maxage=3600, must-revalidate, max-age=0' );
header( "Last-modified: $lastmod" );
echo $rawHtml;
