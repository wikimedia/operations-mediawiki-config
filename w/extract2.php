<?php
$lang = 'meta';
putenv( "MW_LANG={$lang}" ); // notify MWMultiVersion

require_once __DIR__ . '/../multiversion/MWMultiVersion.php';
require MWMultiVersion::getMediaWiki( 'includes/WebStart.php' );

$allowed_templates = array(
	'Www.wikimedia.org_template',
	'Www.wikipedia.org_template',
	'Www.wikinews.org_template',
	'Www.wiktionary.org_template',
	'Www.wikiquote.org_template',
	'Www.wikiversity.org_template',
	'Www.wikibooks.org_template',
	'Www.wikivoyage.org_template',
	'API_listing_template',
);

$template = $wgRequest->getText( 'template', 'Www.wikipedia.org_template' );
if ( !in_array( $template, $allowed_templates ) ) {
	header( 'Content-Type: text/plain; charset=utf-8' );
	echo 'Invalid parameters...';
	exit;
}

$article = new Article( Title::newFromText( $template ) );
$rawHtml = (string)$article->getPage()->getContent();

$lastmod = gmdate( 'D, j M Y H:i:s', wfTimestamp( TS_UNIX, $wgArticle->getTouched() ) ) . ' GMT';
header( 'Content-Type: text/html; charset=utf-8' );
header( 'Cache-Control: s-maxage=3600, must-revalidate, max-age=0' );
header( "Last-modified: $lastmod" );
echo $rawHtml;
