<?php
define( 'MW_NO_SESSION', 'warn' ); // TODO: Change 'warn' to 1 if stable.
require_once __DIR__ . '/../multiversion/MWMultiVersion.php';
require MWMultiVersion::getMediaWiki( 'includes/WebStart.php', 'metawiki' );

$article = new Article( Title::newFromText( 'API_listing_template' ) );
$rawHtml = $article->getPage()->getContent()->getNativeData();

$lastmod = gmdate(
		'D, j M Y H:i:s',
		wfTimestamp( TS_UNIX, $article->getPage()->getTouched() )
	) . ' GMT';
header( 'Content-Type: text/html; charset=utf-8' );
header( 'Cache-Control: s-maxage=3600, must-revalidate, max-age=0' );
header( "Last-modified: $lastmod" );
echo $rawHtml;
