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

// stream recent changes to redis
$wgRCFeeds['redis'] = array(
	'formatter' => 'JSONRCFeedFormatter',
	'uri'       => "redis://deployment-stream.eqiad.wmflabs:6379/rc.$wgDBname",
);

$wgProfiler['udphost'] = 'labmon1001.eqiad.wmnet';

$wgDebugTimestamps = true;

$wmgAddWikiNotify = false;

$wgLocalVirtualHosts = array(
	'wikipedia.beta.wmflabs.org',
	'wiktionary.beta.wmflabs.org',
	'wikibooks.beta.wmflabs.org',
	'wikiquote.beta.wmflabs.org',
	'wikinews.beta.wmflabs.org',
	'wikisource.beta.wmflabs.org',
	'wikiversity.beta.wmflabs.org',
	'wikivoyage.beta.wmflabs.org',
	'meta.wikimedia.beta.wmflabs.org',
	'commons.wikimedia.beta.wmflabs.org',
);

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
	$wgMWOAuthCentralWiki = 'metawiki';

	// T61141 - Return this to true and reset secrets once SSL works in labs
	$wgMWOAuthSecureTokenTransfer = false;
}

if ( $wmgEnableInterwiki ) {
	require_once "$IP/extensions/Interwiki/Interwiki.php";
	$wgInterwikiViewOnly = true;
}

if ( $wmgUseMultimediaViewer ) {
	require_once "$IP/extensions/MultimediaViewer/MultimediaViewer.php";
	$wgMediaViewerNetworkPerformanceSamplingFactor = $wmgMediaViewerNetworkPerformanceSamplingFactor;
}


if ( $wmgUseFlow ) {
	$wgFlowParsoidURL = $wmgParsoidURL; // Re-link now it's been set to a new value
}

if ( $wgDBname === 'enwiki' || $wgDBname === 'cawiki' ) {
	$wgExtraNamespaces += array(
		190 => 'Flow_test',
		191 => 'Flow_test_talk',
	);

	$wgNamespacesWithSubpages += array(
		190 => true,
		191 => true,
	);

	$wgNamespaceContentModels[ 191 ] = CONTENT_MODEL_FLOW_BOARD;
}

if ( $wmgUseContentTranslation ) {
	$wgContentTranslationSiteTemplates['cx'] = 'https://cxserver-beta.wmflabs.org/v1';
	$wgContentTranslationRESTBase['url'] = 'https://rest.wikimedia.org';
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
	$wgCentralAuthUseSlaves = true;
}

// Labs override for GlobalCssJs
if ( $wmgUseGlobalCssJs && $wmgUseCentralAuth ) {
	// Load from betalabs metawiki
	$wgResourceLoaderSources['metawiki'] = array(
		'apiScript' => '//meta.wikimedia.beta.wmflabs.org/w/api.php',
		'loadScript' => '//meta.wikimedia.beta.wmflabs.org/w/load.php',
	);
}

if ( $wmgUseGlobalUserPage && $wmgUseCentralAuth ) {
	// Labs override
	$wgGlobalUserPageAPIUrl = 'http://meta.wikimedia.beta.wmflabs.org/w/api.php';
	$wgGlobalUserPageDBname = 'metawiki';
}

if ( $wmgUseUrlShortener ) {
	// Labs overrides
	$wgUrlShortenerReadOnly = false;
	$wgUrlShortenerServer = 'w-beta.wmflabs.org';
	$wgUrlShortenerDBCluster = false;
	$wgUrlShortenerDBName = 'wikishared';
	$wgUrlShortenerDomainsWhitelist = array(
		'(.*\.)?wikipedia\.beta\.wmflabs\.org',
		'(.*\.)?wiktionary\.beta\.wmflabs\.org',
		'(.*\.)?wikibooks\.beta\.wmflabs\.org',
		'(.*\.)?wikinews\.beta\.wmflabs\.org',
		'(.*\.)?wikiquote\.beta\.wmflabs\.org',
		'(.*\.)?wikisource\.beta\.wmflabs\.org',
		'(.*\.)?wikiversity\.beta\.wmflabs\.org',
		'(.*\.)?wikivoyage\.beta\.wmflabs\.org',
		'(.*\.)?wikimedia\.beta\.wmflabs\.org',
		'wikidata\.beta\.wmflabs\.org',
	);
	$wgUrlShortenerApprovedDomains = array(
		'*.wikipedia.beta.wmflabs.org',
		'*.wiktionary.beta.wmflabs.org',
		'*.wikibooks.beta.wmflabs.org',
		'*.wikinews.beta.wmflabs.org',
		'*.wikiquote.beta.wmflabs.org',
		'*.wikisource.beta.wmflabs.org',
		'*.wikiversity.beta.wmflabs.org',
		'*.wikivoyage.beta.wmflabs.org',
		'*.wikimedia.beta.wmflabs.org',
		'wikidata.beta.wmflabs.org',
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
	$wgFFmpegLocation = '/usr/bin/ffmpeg'; // use new ffmpeg build w/ VP9 & Opus support
}

if ( $wgDBname == "testwiki" ) {
	$wgCaptchaDirectory = '/data/project/upload7/private/captcha/random';
} else {
	$wgCaptchaDirectory = '/data/project/upload7/private/captcha';
}

// Enable Flickr uploads on commons beta T86120
if ( $wgDBname == 'commonswiki' ) {
	$wgGroupPermissions['user']['upload'] = true;
    $wgGroupPermissions['user']['upload_by_url'] = true;
} else { // Use InstantCommons on all betawikis except commonswiki
	$wgUseInstantCommons = true;
}

# Backends:
if ( $wmgUseMath ) {
	$wgMathFileBackend = false;
	$wgMathDirectory   = '/data/project/upload7/math';
}

if ( $wmgUseScore ) {
	$wgScoreFileBackend = false;
	$wgScoreDirectory = '/data/project/upload7/score';
}

// CORS (cross-domain AJAX, T22814)
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

if ( $wmgUseQuickSurveys ) {
	$wgQuickSurveysRequireHttps = false;

	$wgQuickSurveysConfig = array(
		array(
			"name" => "drink-survey",
			"type" => "internal",
			"question" => "anne-survey-question",
			"answers" => array(
				"anne-survey-answer-one",
				"anne-survey-answer-two",
				"anne-survey-answer-three",
				"anne-survey-answer-four"
			),
			"schema" => "QuickSurveysResponses",
			"enabled" => true,
			"coverage" => 0,
			"description" => "anne-survey-description",
			"platforms" => array(
				"desktop" => array( "stable" ),
				"mobile" => array( "stable", "beta" ),
			),
		),
		array(
			"name" => "internal example survey",
			"type" => "internal",
			"question" => "ext-quicksurveys-example-internal-survey-question",
			"answers" => array(
				"ext-quicksurveys-example-internal-survey-answer-positive",
				"ext-quicksurveys-example-internal-survey-answer-neutral",
				"ext-quicksurveys-example-internal-survey-answer-negative",
			),
			"schema" => "QuickSurveysResponses",
			"enabled" => true,
			"coverage" => .5,
			"description" => "ext-quicksurveys-example-internal-survey-description",
			"platforms" => array(
				"desktop" => array( "stable" ),
				"mobile" => array( "stable", "beta" ),
			),
		),
		array(
			'name' => 'external example survey',
			'type' => 'external',
			"question" => "ext-quicksurveys-example-external-survey-question",
			"description" => "ext-quicksurveys-example-external-survey-description",
			"link" => "ext-quicksurveys-example-external-survey-link",
			"privacyPolicy" => "ext-quicksurveys-example-external-survey-privacy-policy",
			'coverage' => .5,
			'enabled' => true,
			'platforms' => array(
				'desktop' => array( 'stable' ),
				'mobile' => array( 'stable', 'beta' ),
			),
		),
	);
}

if ( $wmgUseSentry ) {
	require_once( "$IP/extensions/Sentry/Sentry.php" );
	$wgSentryDsn = $wmgSentryDsn;
	$wgSentryLogPhpErrors = false;
}

if ( $wmgUseEcho && $wmgUseCentralAuth ) {
	$wgEchoSharedTrackingDB = 'wikishared';
	// Set cluster back to false, to override CommonSettings.php setting it to 'extension1'
	$wgEchoSharedTrackingCluster = false;
}

if ( $wmgUseGraph ) {
	// **** THIS LIST MUST MATCH puppet/hieradata/labs/deployment-prep/common.yaml ****
	// See https://www.mediawiki.org/wiki/Extension:Graph#External_data
	$wgGraphAllowedDomains['http'] = array( 'wmflabs.org' );
	$wgGraphAllowedDomains['wikirawupload'][] = 'upload.beta.wmflabs.org';
	$wgGraphAllowedDomains['wikidatasparql'] = array(
		'query.wikidata.org',
		'wdqs-test.wmflabs.org'
	);
}

if ( $wmgUseKartographer ) {
	wfLoadExtension( 'Kartographer' );
	$wgKartographerMapServer = "https://maps.wikimedia.org";
}

if ( $wmgUseORES ) {
	wfLoadExtension( 'ORES' );
	$wgOresWikiId = 'testwiki';
	$wgOresBaseUrl = 'https://ores.wmflabs.org/';
}

if ( $wmgUseNewsletter ) {
	wfLoadExtension( 'Newsletter' );
}

if ( $wmgUseOATHAuth && $wmgUseCentralAuth ) {
	wfLoadExtension( 'OATHAuth' );
	$wgOATHAuthDatabase = 'centralauth';
	// Roll this feature out to specific groups initially
	$wgGroupPermissions['*']['oathauth-enable'] = false;
}

// Experimental
$wgGadgetsCaching = false;

$wgMessageCacheType = CACHE_ACCEL;

// Test of new import source configuration on labs cluster
$wgImportSources = false;
include( "$wmfConfigDir/import.php" );
$wgHooks['ImportSources'][] = 'wmfImportSources';

} # end safeguard
