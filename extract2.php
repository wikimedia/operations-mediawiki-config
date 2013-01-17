<?php
header("Content-Type: text/html; charset=utf-8");
$lang = "meta";
putenv( "MW_LANG={$lang}" ); // notify MWMultiVersion
define( "MEDIAWIKI", true );
#include_once("CommonSettings.php");
#include_once("/apache/common/wmf-deployment/includes/ProfileStub.php" );
#include_once("/apache/common/wmf-deployment/includes/Defines.php" );
#include_once("/apache/common/wmf-deployment/wmf-config/CommonSettings.php");
#include_once("/apache/common/wmf-deployment/includes/Setup.php");

#include_once("/apache/common/wmf-deployment/includes/WebStart.php");

include "/apache/common/live-1.5/MWVersion.php";
include getMediaWiki("includes/WebStart.php");

$allowed_portals = array(
	"Www.wikipedia.org_portal",
	"Www.wikinews.org_portal",
	"Www.wiktionary.org_portal",
	"Www.wikiquote.org_portal",
	"Www.wikimedia.org_portal",
	"Www.wikiversity.org_portal",
	"Www.wikibooks.org_portal",
	"Secure.wikimedia.org_portal",
	"Www.wikivoyage.org_portal",
);
$allowed_templates = array(
	"Www.wikipedia.org_template",
	"Www.wikinews.org_template",
	"Www.wiktionary.org_template",
	"Www.wikiquote.org_template",
	"Www.wikimedia.org_template",
	"Www.wikiversity.org_template",
	"Www.wikibooks.org_template",
	"Secure.wikimedia.org_template",
	"Www.wikivoyage.org_template",
);

$useportal = $wgRequest->getText( 'title', 'Www.wikipedia.org_portal' );
$usetemplate = $wgRequest->getText( 'template', 'Www.wikipedia.org_template' );
if (!in_array($useportal, $allowed_portals) || !in_array($usetemplate, $allowed_templates)) {
	header("Content-Type", "text/plain; charset=US-ASCII");
	echo "sorry...";
	exit;
}

$wgTitle = Title::newFromText( $useportal );
$wgArticle = new Article( $wgTitle );
$mainText = $wgOut->parse( $wgArticle->getContent( false ) );

$templateTitle = Title::newFromText( $usetemplate );
$templateArticle = new Article( $templateTitle );
$templateText = $templateArticle->getContent( false );
$text = str_replace( '$1', $mainText, $templateText );

$lastmod = gmdate( 'D, j M Y H:i:s', wfTimestamp(TS_UNIX, max( $wgArticle->getTouched(), $templateArticle->getTouched() ) ) ) . ' GMT';
header( 'Cache-Control: s-maxage=3600, must-revalidate, max-age=0' );
header( "Last-modified: $lastmod" );
echo $text;

