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
	$wgProfiler['output'] = [ 'text' ];
}

if ( file_exists( '/etc/wmflabs-instancename' ) ) {
	$wgOverrideHostname = trim( file_get_contents( '/etc/wmflabs-instancename' ) );
}

// stream recent changes to redis
$wgRCFeeds['redis'] = [
	'formatter' => 'JSONRCFeedFormatter',
	'uri'       => "redis://deployment-stream.eqiad.wmflabs:6379/rc.$wgDBname",
];

$wgProfiler['udphost'] = 'labmon1001.eqiad.wmnet';

$wgDebugTimestamps = true;

$wmgAddWikiNotify = false;

$wgLocalVirtualHosts = [
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
];

# Attempt to auto block users using faulty servers
# See also http://www.us.sorbs.net/general/using.shtml
$wgEnableDnsBlacklist = true;
$wgDnsBlacklistUrls   = [
	'proxies.dnsbl.sorbs.net.',
];

if ( $wmgUseOAuth ) {
	$wgMWOAuthCentralWiki = 'metawiki';
}

if ( $wmgUseFlow ) {
	// Override CommonSettings.php, which has:
	// $wgFlowExternalStore = $wgDefaultExternalStore;
	$wgFlowExternalStore = [
		'DB://flow_cluster1',
	];
}

if ( $wgDBname === 'enwiki' || $wgDBname === 'cawiki' ) {
	$wgExtraNamespaces += [
		190 => 'Flow_test',
		191 => 'Flow_test_talk',
	];

	$wgNamespacesWithSubpages += [
		190 => true,
		191 => true,
	];

	$wgNamespaceContentModels[ 191 ] = CONTENT_MODEL_FLOW_BOARD;
}

if ( $wmgUseContentTranslation ) {
	$wgContentTranslationSiteTemplates['cx'] = 'https://cxserver-beta.wmflabs.org/v1';
	$wgContentTranslationTranslateInTarget = false;
}

if ( $wmgUseCentralNotice ) {
	$wgCentralPagePath = "//meta.wikimedia.beta.wmflabs.org/w/index.php";
	$wgCentralSelectedBannerDispatcher = "//meta.wikimedia.beta.wmflabs.org/w/index.php?title=Special:BannerLoader";
	$wgCentralBannerRecorder = "//meta.wikimedia.beta.wmflabs.org/w/index.php?title=Special:RecordImpression";
}

if ( $wmgUseCentralAuth ) {
	$wgCentralAuthUseSlaves = true;
}

// Labs override for GlobalCssJs
if ( $wmgUseGlobalCssJs && $wmgUseCentralAuth ) {
	// Load from betalabs metawiki
	$wgResourceLoaderSources['metawiki'] = [
		'apiScript' => '//meta.wikimedia.beta.wmflabs.org/w/api.php',
		'loadScript' => '//meta.wikimedia.beta.wmflabs.org/w/load.php',
	];
}

if ( $wmgUseGlobalUserPage && $wmgUseCentralAuth ) {
	// Labs override
	$wgGlobalUserPageAPIUrl = 'https://meta.wikimedia.beta.wmflabs.org/w/api.php';
	$wgGlobalUserPageDBname = 'metawiki';
}

if ( $wmgUseUrlShortener ) {
	// Labs overrides
	$wgUrlShortenerReadOnly = false;
	$wgUrlShortenerServer = 'w-beta.wmflabs.org';
	$wgUrlShortenerDBCluster = false;
	$wgUrlShortenerDBName = 'wikishared';
	$wgUrlShortenerDomainsWhitelist = [
		'(.*\.)?wikipedia\.beta\.wmflabs\.org',
		'(.*\.)?wiktionary\.beta\.wmflabs\.org',
		'(.*\.)?wikibooks\.beta\.wmflabs\.org',
		'(.*\.)?wikinews\.beta\.wmflabs\.org',
		'(.*\.)?wikiquote\.beta\.wmflabs\.org',
		'(.*\.)?wikisource\.beta\.wmflabs\.org',
		'(.*\.)?wikiversity\.beta\.wmflabs\.org',
		'(.*\.)?wikivoyage\.beta\.wmflabs\.org',
		'(.*\.)?wikimedia\.beta\.wmflabs\.org',
		'(.*\.)?wikidata\.beta\.wmflabs\.org',
	];
	$wgUrlShortenerApprovedDomains = [
		'*.wikipedia.beta.wmflabs.org',
		'*.wiktionary.beta.wmflabs.org',
		'*.wikibooks.beta.wmflabs.org',
		'*.wikinews.beta.wmflabs.org',
		'*.wikiquote.beta.wmflabs.org',
		'*.wikisource.beta.wmflabs.org',
		'*.wikiversity.beta.wmflabs.org',
		'*.wikivoyage.beta.wmflabs.org',
		'*.wikimedia.beta.wmflabs.org',
		'*.wikidata.beta.wmflabs.org',
	];
}

// Labs override for BounceHandler
if ( $wmgUseBounceHandler ) {
	//$wgVERPsecret = ''; // This was set in PrivateSettings.php by Legoktm
	$wgBounceHandlerCluster = false;
	$wgBounceHandlerSharedDB = false;
	$wgBounceHandlerInternalIPs = [ '127.0.0.1', '::1', '10.68.17.78' ]; //deployment-mx.wmflabs.org
	$wgBounceHandlerUnconfirmUsers = true;
	$wgBounceRecordLimit = 5;
	$wgVERPdomainPart = 'beta.wmflabs.org';
}

if ( $wmgUseTimedMediaHandler ) {
	$wgMwEmbedModuleConfig[ 'MediaWiki.ApiProviders' ] =  [
	"commons" => [
		'url' => '//commons.wikimedia.beta.wmflabs.org/w/api.php'
	]];
	$wgEnableTranscode = true; //enable transcoding on labs
	$wgFFmpegLocation = '/usr/bin/ffmpeg'; // use new ffmpeg build w/ VP9 & Opus support
}

// Enable Flickr uploads on commons beta T86120
if ( $wgDBname == 'commonswiki' ) {
	$wgGroupPermissions['user']['upload'] = true;
    $wgGroupPermissions['user']['upload_by_url'] = true;
} else { // Use InstantCommons on all betawikis except commonswiki
	$wgUseInstantCommons = true;
}


// Enable Tabular data namespace on Commons - T148745
if ( $wgDBname == 'commonswiki' ) {
	// Safety: before extension.json, these values were initialized by JsonConfig.php
	if ( !isset( $wgJsonConfigModels ) ) {
		$wgJsonConfigModels = [];
	}
	if ( !isset( $wgJsonConfigs ) ) {
		$wgJsonConfigs = [];
	}
	// https://www.mediawiki.org/wiki/Extension:JsonConfig#Configuration
	$wgJsonConfigModels['Tabular.JsonConfig'] = 'JsonConfig\JCTabularContent';
	$wgJsonConfigs['Tabular.JsonConfig'] = [
		'namespace' => 486,
		'nsName' => 'Data',
		// page name must end in ".tab", and contain at least one symbol
		'pattern' => '/.\.tab$/',
		'store' => true,
	];
}


if ( $wmgUseMath ) {
	$wgDefaultUserOptions[ 'math' ] = 'mathml';
}

// CORS (cross-domain AJAX, T22814)
// This lists the domains that are accepted as *origins* of CORS requests
// DO NOT add domains here that aren't WMF wikis unless you really know what you're doing
if ( $wmgUseCORS ) {
	$wgCrossSiteAJAXdomains = [
		'*.beta.wmflabs.org',
	];
}

// Temporary override to test T68699 on Beta Cluster.  Remove when in production.
$wgExtendedLoginCookieExpiration = 365 * 86400;

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
	$wgPageImagesBlacklist[] = [
		'type' => 'db',
		'page' => 'MediaWiki:Pageimages-blacklist',
		'db' => 'commonswiki',
	];
}

if ( $wmgUseQuickSurveys ) {
	$wgQuickSurveysRequireHttps = false;

	$wgQuickSurveysConfig = [
		[
			"name" => "drink-survey",
			"type" => "internal",
			"question" => "anne-survey-question",
			"answers" => [
				"anne-survey-answer-one",
				"anne-survey-answer-two",
				"anne-survey-answer-three",
				"anne-survey-answer-four"
			],
			"schema" => "QuickSurveysResponses",
			"enabled" => true,
			"coverage" => 0,
			"description" => "anne-survey-description",
			"platforms" => [
				"desktop" => [ "stable" ],
				"mobile" => [ "stable", "beta" ],
			],
		],
		[
			"name" => "internal example survey",
			"type" => "internal",
			"question" => "ext-quicksurveys-example-internal-survey-question",
			"answers" => [
				"ext-quicksurveys-example-internal-survey-answer-positive",
				"ext-quicksurveys-example-internal-survey-answer-neutral",
				"ext-quicksurveys-example-internal-survey-answer-negative",
			],
			"schema" => "QuickSurveysResponses",
			"enabled" => true,
			"coverage" => .5,
			"description" => "ext-quicksurveys-example-internal-survey-description",
			"platforms" => [
				"desktop" => [ "stable" ],
				"mobile" => [ "stable", "beta" ],
			],
		],
		[
			'name' => 'external example survey',
			'type' => 'external',
			"question" => "ext-quicksurveys-example-external-survey-question",
			"description" => "ext-quicksurveys-example-external-survey-description",
			"link" => "ext-quicksurveys-example-external-survey-link",
			"privacyPolicy" => "ext-quicksurveys-example-external-survey-privacy-policy",
			'coverage' => .5,
			'enabled' => true,
			'platforms' => [
				'desktop' => [ 'stable' ],
				'mobile' => [ 'stable', 'beta' ],
			],
		],
	];
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

// Enabling thank-you-edit on beta for testing T128249. Still disabled in prod.
$wgEchoNotifications['thank-you-edit']['notify-type-availability']['web'] = true;

if ( $wmgUseGraph ) {
	// **** THIS LIST MUST MATCH puppet/hieradata/labs/deployment-prep/common.yaml ****
	// See https://www.mediawiki.org/wiki/Extension:Graph#External_data
	$wgGraphAllowedDomains['http'] = [ 'wmflabs.org' ];
	$wgGraphAllowedDomains['wikirawupload'][] = 'upload.beta.wmflabs.org';
	$wgGraphAllowedDomains['wikidatasparql'][] = 'wdqs-test.wmflabs.org';
}

if ( $wmgUseORES ) {
	wfLoadExtension( 'ORES' );
	$wgOresWikiId = 'testwiki';
	$wgOresBaseUrl = 'https://ores-beta.wmflabs.org/';
}

if ( $wmgUseNewsletter ) {
	wfLoadExtension( 'Newsletter' );
}

if ( $wmgUsePerformanceInspector ) {
	wfLoadExtension( 'PerformanceInspector' );
}

if ( $wmgUseOATHAuth && $wmgUseCentralAuth ) {
	wfLoadExtension( 'OATHAuth' );
	$wgOATHAuthDatabase = 'centralauth';
	// Roll this feature out to specific groups initially
	$wgGroupPermissions['*']['oathauth-enable'] = false;
}

if ( $wmgUseUniversalLanguageSelector ) {
	$wgDefaultUserOptions['compact-language-links'] = 0;
}

$wgMessageCacheType = CACHE_ACCEL;

// Let Beta Cluster Commons do upload-from-URL from production Commons.
if ( $wgDBname == 'commonswiki' ) {
	$wgCopyUploadsDomains[] = 'upload.wikimedia.org';
}

// Test of new import source configuration on labs cluster
$wgImportSources = false;
include( "$wmfConfigDir/import.php" );
$wgHooks['ImportSources'][] = 'wmfImportSources';

} # end safeguard
