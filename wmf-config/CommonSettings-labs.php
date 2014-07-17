<?php

# WARNING: This file is publically viewable on the web.
# Do not put private data here.

# This file hold configuration statement overriding CommonSettings.php
# Should not be loaded on production

if( $wmfRealm == 'labs' ) {  # safe guard
	include( "logging-labs.php" );

// test wiki
if ( $wgDBname == 'testwiki' ) {
	$wgDebugToolbar = true;
	$wgProfiler['class'] = 'ProfilerSimpleText';
}

if( file_exists( '/etc/wmflabs-instancename' ) ) {
	$wgOverrideHostname = trim( file_get_contents( '/etc/wmflabs-instancename' ) );
}

$wgDebugTimestamps = true;

$wmgAddWikiNotify = false;

# see r110254 and bug 33746
$wgPreloadJavaScriptMwUtil = true;

// Cache ResourceLoader modules in localStorage
// Experimental! See <https://gerrit.wikimedia.org/r/#/c/86867/>.
$wgResourceLoaderStorageEnabled = true;

if ( $wmgUseEventLogging ) {
	$wgEventLoggingFile = 'udp://deployment-eventlogging02.eqiad.wmflabs:8421/EventLogging';
}

if ( $wmfUseArticleCreationWorkflow ) {
	require_once( "$IP/extensions/ArticleCreationWorkflow/ArticleCreationWorkflow.php" );
	$wgArticleCreationBucketConfig['buckets']['off'] = 0;
}

# Attempt to auto block users using faulty servers
# See also http://www.us.sorbs.net/general/using.shtml
$wgEnableDnsBlacklist = true;
$wgDnsBlacklistUrls   = array(
	'proxies.dnsbl.sorbs.net.',
);

//before you remove this, log somewhere why you did it
//--Petrb
//Commented until a dedicated wiki is created.
//require_once ("$IP/extensions/OnlineStatusBar/OnlineStatusBar.php");

// the beta cluster uses a different filebackend than production
if ( $wmgUseGWToolset ) {
	$wgGWTFileBackend = 'gwtoolset-backend';
}

if ( $wmgUseOAuth ) {
	$wgMWOAuthCentralWiki = 'labswiki';  # bug 57403

	// Bug 59141 - Return this to true and reset secrets once SSL works in labs
	$wgMWOAuthSecureTokenTransfer = false;
}

if ( $wmgUseMultimediaViewer ) {
	require_once( "$IP/extensions/MultimediaViewer/MultimediaViewer.php" );
	$wgNetworkPerformanceSamplingFactor = $wmgNetworkPerformanceSamplingFactor;

	if ( $wmgMediaViewerBeta ) {
		$wgMediaViewerIsInBeta = true;
	}

	if ( $wmgMediaViewerLoggedIn ) {
		$wgEnableMediaViewerForLoggedInUsersOnly = true;
	}
}

if ( $wmgUseVectorBeta ) {
	require_once( "$IP/extensions/VectorBeta/VectorBeta.php" );
	$wgVectorBetaPersonalBar = $wmgVectorBetaPersonalBar;
	$wgVectorBetaWinter = $wmgVectorBetaWinter;
}

if ( $wmgUseParsoid ) {
	$wmgParsoidURL = 'http://10.68.16.145/'; // deployment-parsoidcache01.eqiad
	$wgParsoidCacheServers = array ( 'http://10.68.16.145' ); // deployment-parsoidcache01.eqiad
}

if ( $wmgUseVisualEditor ) {
	$wgVisualEditorParsoidURL = $wmgParsoidURL; // Re-link now it's been set to a new value
	$wgVisualEditorParsoidReportProblemURL = 'http://10.4.0.33/_bugs/'; // parsoid-spof
}

if ( $wmgUseFlow ) {
	$wgFlowParsoidURL = $wmgParsoidURL; // Re-link now it's been set to a new value
}

if ( $wmgUseContentTranslation ) {
	require_once( "$IP/extensions/ContentTranslation/ContentTranslation.php" );
	$wgContentTranslationServerURL = 'https://cxserver-beta.wmflabs.org';
	// Used for html2wikitext when publishing
	$wgContentTranslationParsoid = array(
		'url' => $wmgParsoidURL,
		'timeout' => 10000,
		'prefix' => $wgDBname,
	);
}

if ( $wmgUseCentralNotice ) {
	$wgCentralGeoScriptURL = false;
}

require_once( "$IP/extensions/MobileApp/MobileApp.php" );

// Config for GlobalCssJs
// Only enable on CentralAuth wikis
if ( $wmgUseGlobalCssJs && $wmgUseCentralAuth ) {
	require_once( "$IP/extensions/GlobalCssJs/GlobalCssJs.php" );

	// Disable site-wide global css/js
	$wgUseGlobalSiteCssJs = false;

	// Load from betalabs metawiki
	$wgResourceLoaderSources['metawiki'] = array(
		'apiScript' => '//meta.wikimedia.beta.wmflabs.org/w/api.php',
		'loadScript' => '//bits.beta.wmflabs.org/meta.wikimedia.beta.wmflabs.org/load.php',
	);

	$wgGlobalCssJsConfig = array(
		'wiki' => 'metawiki',
		'source' => 'metawiki',
	);
}

if ( $wmgUseTimedMediaHandler ) {
	$wgMwEmbedModuleConfig[ 'MediaWiki.ApiProviders' ] =  array(
	"commons" => array(
		'url' => '//commons.wikimedia.beta.wmflabs.org/w/api.php'
	));
	$wgEnableTranscode = true; //enable transcoding on labs
}

if ( $wgDBname == "testwiki" ) {
	$wgCaptchaDirectory = '/data/project/upload7/private/captcha/random';
} else {
	$wgCaptchaDirectory = '/data/project/upload7/private/captcha';
}

// Use InstantCommons for testing
if ( $wgDBname == "enwiki" || $wgDBname == 'eswiki' || $wgDBname == 'cawiki' ) {
	$wgUseInstantCommons = true;
}

# Backends:
if( $wmgUseMath ) {
	$wgMathFileBackend = false;
	$wgMathDirectory   = '/data/project/upload7/math';
}

// CORS (cross-domain AJAX, bug 20814)
// This lists the domains that are accepted as *origins* of CORS requests
// DO NOT add domains here that aren't WMF wikis unless you really know what you're doing
if ( $wmgUseCORS ) {
	$wgCrossSiteAJAXdomains = array(
		'*.beta.wmflabs.org',
	);
}

if ( file_exists( "$wmfConfigDir/extension-list-labs" ) ) {
	$wgExtensionEntryPointListFiles[] = "$wmfConfigDir/extension-list-labs";
}

if ( $wmgUseCollection ) {
	// MwLib (PediaPress PDF Generation) is still done on the production servers

	$wgCollectionFormats['rdf2latex'] = 'WMF PDF';
	$wgCollectionFormatToServeURL['rdf2latex'] = 'http://deployment-pdf01:8000';
	$wgCollectionPortletFormats[] = 'rdf2latex';
}

if ( $wmgUsePageImages ) {
	require_once( "$IP/extensions/PageImages/PageImages.php" );
	$wgPageImagesExpandOpenSearchXml = $wmgPageImagesExpandOpenSearchXml;
	$wgPageImagesBlacklist[] = array(
		'type' => 'db',
		'page' => 'MediaWiki:Pageimages-blacklist',
		'db' => 'commonswiki',
	);
}

if ( $wmgUseFundraisingTranslateWorkflow ) {
	include( "$IP/extensions/FundraisingTranslateWorkflow/FundraisingTranslateWorkflow.php" );
}

} # end safeguard
