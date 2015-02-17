<?php

# WARNING: This file is publically viewable on the web.
# Do not put private data here.

# This file hold configuration statement overriding CommonSettings.php
# Should not be loaded on production

if ( $wmfRealm == 'labs' ) {  # safe guard

// test wiki
if ( $wgDBname == 'testwiki' ) {
	$wgDebugToolbar = true;
	$wgProfiler['class'] = 'ProfilerXhprof';
	$wgProfiler['output'] = array( 'text' );
}

if ( file_exists( '/etc/wmflabs-instancename' ) ) {
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
	require_once "$IP/extensions/ArticleCreationWorkflow/ArticleCreationWorkflow.php";
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
	$wgMWOAuthCentralWiki = 'deploymentwiki';  # bug 57403

	// Bug 59141 - Return this to true and reset secrets once SSL works in labs
	$wgMWOAuthSecureTokenTransfer = false;
}

if ( $wmgUseMultimediaViewer ) {
	require_once "$IP/extensions/MultimediaViewer/MultimediaViewer.php";
	$wgNetworkPerformanceSamplingFactor = $wmgNetworkPerformanceSamplingFactor;
}

if ( $wmgUseVectorBeta ) {
	require_once "$IP/extensions/VectorBeta/VectorBeta.php";
	$wgVectorBetaPersonalBar = $wmgVectorBetaPersonalBar;
	$wgVectorBetaWinter = $wmgVectorBetaWinter;
}

if ( $wmgUseParsoid ) {
	$wmgParsoidURL = 'http://10.68.16.145'; // deployment-parsoidcache01.eqiad
	$wgParsoidCacheServers = array ( 'http://10.68.16.145' ); // deployment-parsoidcache01.eqiad
}

if ( $wmgUseVisualEditor ) {
	$wgVisualEditorParsoidURL = $wmgParsoidURL; // Re-link now it's been set to a new value
	$wgVisualEditorParsoidReportProblemURL = 'http://10.4.0.33/_bugs/'; // parsoid-spof
}

if ( $wmgUseCitoid ) {
	require_once "$IP/extensions/Citoid/Citoid.php";
	// Citoid extension for VisualEditor pointing at the WMF Labs URL for now
	$wgCitoidServiceUrl = 'http://citoid.wmflabs.org/api';
}

if ( $wmgUseFlow ) {
	$wgFlowParsoidURL = $wmgParsoidURL; // Re-link now it's been set to a new value
}

if ( $wmgUseContentTranslation ) {
	$wgContentTranslationSiteTemplates['cx'] = 'https://cxserver-beta.wmflabs.org';
	$wgContentTranslationTranslateInTarget = false;
}

if ( $wmgUseCentralNotice ) {
	$wgCentralGeoScriptURL = false;

	$wgCentralPagePath = "//meta.wikimedia.beta.wmflabs.org/w/index.php";
	$wgCentralSelectedBannerDispatcher = "//meta.wikimedia.beta.wmflabs.org/w/index.php?title=Special:BannerLoader";
	$wgCentralBannerRecorder = "//meta.wikimedia.beta.wmflabs.org/w/index.php?title=Special:RecordImpression";
	$wgCentralDBname = 'metawiki';
}

require_once "$IP/extensions/MobileApp/MobileApp.php";

if ( $wmgUseCentralAuth ) {
	$wgCentralAuthEnableUserMerge = true;
}

// Labs override for GlobalCssJs
if ( $wmgUseGlobalCssJs && $wmgUseCentralAuth ) {
	// Load from betalabs metawiki
	$wgResourceLoaderSources['metawiki'] = array(
		'apiScript' => '//meta.wikimedia.beta.wmflabs.org/w/api.php',
		'loadScript' => '//bits.beta.wmflabs.org/meta.wikimedia.beta.wmflabs.org/load.php',
	);
}

if ( $wmgUseGlobalUserPage && $wmgUseCentralAuth ) {
	// Labs override
	$wgGlobalUserPageAPIUrl = 'http://meta.wikimedia.beta.wmflabs.org/w/api.php';
	$wgGlobalUserPageDBname = 'metawiki';
}

if ( $wmgUseApiFeatureUsage ) {
	require_once "$IP/extensions/ApiFeatureUsage/ApiFeatureUsage.php";
	$wgApiFeatureUsageQueryEngineConf = array(
		'class' => 'ApiFeatureUsageQueryEngineElastica',
		'serverList' => array(
			'deployment-elastic05',
			'deployment-elastic06',
			'deployment-elastic07',
			'deployment-elastic08',
		),
	);
}

// Labs override for BounceHandler
if ( $wmgUseBounceHandler ) {
	//$wgVERPsecret = ''; // This was set in PrivateSettings.php by Legoktm
	$wgBounceHandlerCluster = false;
	$wgBounceHandlerSharedDB = false;
	$wgBounceHandlerInternalIPs = array( '127.0.0.1', '::1', '10.68.17.78' ); //deployment-mx.wmflabs.org
	$wgBounceHandlerUnconfirmUsers = true;
	$wgBounceRecordLimit = 5;
	$wgVERPdomainPart = 'beta.wmflabs.org';
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

// Use InstantCommons on all betawikis except commonswiki
if ( $wgDBname != 'commonswiki' ) {
	$wgUseInstantCommons = true;
}

# Backends:
if ( $wmgUseMath ) {
	$wgMathFileBackend = false;
	$wgMathDirectory   = '/data/project/upload7/math';
	$wgMathMathMLUrl = 'http://deployment-mathoid.eqiad.wmflabs:10042';
}

if ( $wmgUseScore ) {
	$wgScoreFileBackend = false;
	$wgScoreDirectory = '/data/project/upload7/score';
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
	// Use the beta/labs OCG service
	$wgCollectionMWServeURL = 'http://deployment-pdf01:8000';
	$wgCollectionFormats[ 'rdf2text' ] = 'TXT';
	$wgCollectionPortletFormats[] = 'rdf2text';
	// Don't use production proxy to reach PediaPress
	$wgCollectionCommandToServeURL[ 'zip_post' ] = 'https://pediapress.com/wmfup/';
}

if ( $wmgUsePageImages ) {
	require_once "$IP/extensions/PageImages/PageImages.php";
	$wgPageImagesExpandOpenSearchXml = $wmgPageImagesExpandOpenSearchXml;
	$wgPageImagesBlacklist[] = array(
		'type' => 'db',
		'page' => 'MediaWiki:Pageimages-blacklist',
		'db' => 'commonswiki',
	);
}

if ( $wmgUseFundraisingTranslateWorkflow ) {
	include "$IP/extensions/FundraisingTranslateWorkflow/FundraisingTranslateWorkflow.php";
}

if ( $wmgUseSentry ) {
	require_once( "$IP/extensions/Sentry/Sentry.php" );
	$wgSentryDsn = $wmgSentryDsn;
}

// Experimental
$wgGadgetsCaching = false;

$wgAjaxEditStash = true;

} # end safeguard
