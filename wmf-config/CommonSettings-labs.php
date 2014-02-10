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
	$wgEventLoggingFile = 'udp://deployment-eventlogging.pmtpa.wmflabs:8421/EventLogging';
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

// So that people can easily test the captchas without making accounts -- Platonides
$wgGroupPermissions['autoconfirmed']['skipcaptcha'] = false;

if ( $wmgEnableGeoData ) {
	$wgGeoDataSolrMaster = 'deployment-solr.pmtpa.wmflabs';
	$wgGeoDataSolrHosts = array(
		'deployment-solr.pmtpa.wmflabs' => 100,
	);
}

// the beta cluster uses a different filebackend than production
if ( $wmgUseGWToolset ) {
	$wgGWTFileBackend = 'gwtoolset-backend';
}

if ( $wmgUseOAuth ) {
	$wgMWOAuthCentralWiki = 'labswiki';  # bug 57403
}

if ( $wmgUseMultimediaViewer ) {
	require_once( "$IP/extensions/MultimediaViewer/MultimediaViewer.php" );
	$wgNetworkPerformanceSamplingFactor = 1;
}

if ( $wmgUseVectorBeta ) {
	require_once( "$IP/extensions/VectorBeta/VectorBeta.php" );
}

if ( $wmgUseVisualEditor ) {
	$wgVisualEditorParsoidURL = 'http://10.4.0.61/'; // deployment-parsoidcache3
	$wgParsoidCacheServers = array ( 'http://10.4.0.61' ); // deployment-parsoidcache3
	$wgVisualEditorParsoidReportProblemURL = 'http://10.4.0.33/_bugs/'; // parsoid-spof
}

require_once( "$IP/extensions/MobileApp/MobileApp.php" );

require_once( "$IP/extensions/Popups/Popups.php" );

# temporary extensions
# ========================================================================

if ( $wmgUseTimedMediaHandler ) {
	$wgMwEmbedModuleConfig[ 'MediaWiki.ApiProviders' ] =  array(
	"commons" => array(
		'url' => '//commons.wikimedia.beta.wmflabs.org/w/api.php'
	));
	$wgEnableTranscode = true; //enable transcoding on labs
}

if ( $wgDBname == "testwiki" ) {
	$wgCaptchaDirectory = '/data/project/upload7/private/captcha/random';
}

// Use InstantCommons for testing
if ( $wgDBname == "enwikivoyage" || $wgDBname == "dewikivoyage" || $wgDBname == "enwiki" ) {
	$wgUseInstantCommons = true;
}

# Backends:
if( $wmgUseMath ) {
	$wgMathFileBackend = false;
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

} # end safeguard
