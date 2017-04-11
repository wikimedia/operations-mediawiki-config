<?php

# WARNING: This file is publically viewable on the web.
# Do not put private data here.

# This file hold configuration statement overriding CommonSettings.php
# Should not be loaded on production

if ( $wgWMFRealm == 'labs' ) {  # safe guard

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

$wgWMFAddWikiNotify = false;

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

if ( $wgWMFUseOAuth ) {
	$wgMWOAuthCentralWiki = 'metawiki';
}

if ( $wgWMFUseFlow ) {
	// Override CommonSettings.php, which has:
	// $wgFlowExternalStore = $wgDefaultExternalStore;
	$wgFlowExternalStore = [
		'DB://flow_cluster1',
	];

	$wgExtraNamespaces += [
		190 => 'Flow_test',
		191 => 'Flow_test_talk',
	];

	$wgNamespacesWithSubpages += [
		190 => true,
		191 => true,
	];

	$wgNamespaceContentModels[ 191 ] = 'flow-board'; // CONTENT_MODEL_FLOW_BOARD
}

if ( $wgWMFUseContentTranslation ) {
	$wgContentTranslationSiteTemplates['cx'] = 'https://cxserver-beta.wmflabs.org/v1';
	$wgContentTranslationTranslateInTarget = false;
}

if ( $wgWMFUseCentralNotice ) {
	$wgCentralPagePath = "//meta.wikimedia.beta.wmflabs.org/w/index.php";
	$wgCentralSelectedBannerDispatcher = "//meta.wikimedia.beta.wmflabs.org/w/index.php?title=Special:BannerLoader";
	$wgCentralBannerRecorder = "//meta.wikimedia.beta.wmflabs.org/w/index.php?title=Special:RecordImpression";
}

if ( $wgWMFUseCentralAuth ) {
	$wgCentralAuthUseSlaves = true;
}

// Labs override for GlobalCssJs
if ( $wgWMFUseGlobalCssJs && $wgWMFUseCentralAuth ) {
	// Load from betalabs metawiki
	$wgResourceLoaderSources['metawiki'] = [
		'apiScript' => '//meta.wikimedia.beta.wmflabs.org/w/api.php',
		'loadScript' => '//meta.wikimedia.beta.wmflabs.org/w/load.php',
	];
}

if ( $wgWMFUseGlobalUserPage && $wgWMFUseCentralAuth ) {
	// Labs override
	$wgGlobalUserPageAPIUrl = 'https://meta.wikimedia.beta.wmflabs.org/w/api.php';
	$wgGlobalUserPageDBname = 'metawiki';
}

if ( $wgWMFUseUrlShortener ) {
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
if ( $wgWMFUseBounceHandler ) {
	// $wgVERPsecret = ''; // This was set in PrivateSettings.php by Legoktm
	$wgBounceHandlerCluster = false;
	$wgBounceHandlerSharedDB = false;
	$wgBounceHandlerInternalIPs = [ '127.0.0.1', '::1', '10.68.17.78' ]; // deployment-mx.wmflabs.org
	$wgBounceHandlerUnconfirmUsers = true;
	$wgBounceRecordLimit = 5;
	$wgVERPdomainPart = 'beta.wmflabs.org';
}

if ( $wgWMFUseTimedMediaHandler ) {
	$wgMwEmbedModuleConfig[ 'MediaWiki.ApiProviders' ] =  [
	"commons" => [
		'url' => '//commons.wikimedia.beta.wmflabs.org/w/api.php'
	]];
	$wgEnableTranscode = true; // enable transcoding on labs
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
if ( $wgWMFEnableTabularData && $wgDBname !== 'commonswiki' ) {
	$wgJsonConfigs['Tabular.JsonConfig']['remote']['url'] = 'https://commons.wikimedia.beta.wmflabs.org/w/api.php';
}

// Enable Map (GeoJSON) data namespace on Commons - T149548
if ( $wgWMFEnableMapData && $wgDBname !== 'commonswiki' ) {
	$wgJsonConfigs['Map.JsonConfig']['remote']['url'] = 'https://commons.wikimedia.beta.wmflabs.org/w/api.php';
}


if ( $wgWMFUseMath ) {
	$wgDefaultUserOptions[ 'math' ] = 'mathml';
}

// CORS (cross-domain AJAX, T22814)
// This lists the domains that are accepted as *origins* of CORS requests
// DO NOT add domains here that aren't WMF wikis unless you really know what you're doing
if ( $wgWMFUseCORS ) {
	$wgCrossSiteAJAXdomains = [
		'*.beta.wmflabs.org',
	];
}

// Temporary override to test T68699 on Beta Cluster.  Remove when in production.
$wgExtendedLoginCookieExpiration = 365 * 86400;

if ( file_exists( "$wgWMFConfigDir/extension-list-labs" ) ) {
	$wgExtensionEntryPointListFiles[] = "$wgWMFConfigDir/extension-list-labs";
}

if ( $wgWMFUseCollection ) {
	$wgCollectionPortletFormats[] = 'rdf2text';
	// Don't use production proxy to reach PediaPress
	$wgCollectionCommandToServeURL[ 'zip_post' ] = 'https://pediapress.com/wmfup/';
}

if ( $wgWMFUsePageImages ) {
	require_once "$IP/extensions/PageImages/PageImages.php";
	$wgPageImagesExpandOpenSearchXml = $wgWMFPageImagesExpandOpenSearchXml;
	$wgPageImagesBlacklist[] = [
		'type' => 'db',
		'page' => 'MediaWiki:Pageimages-blacklist',
		'db' => 'commonswiki',
	];
}

if ( $wgWMFUseQuickSurveys ) {
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
			"coverage" => 0,
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
			'coverage' => 0,
			'enabled' => true,
			'platforms' => [
				'desktop' => [ 'stable' ],
				'mobile' => [ 'stable', 'beta' ],
			],
		],
	];
}

if ( $wgWMFUseSentry ) {
	require_once( "$IP/extensions/Sentry/Sentry.php" );
	$wgSentryDsn = $wgWMFSentryDsn;
	$wgSentryLogPhpErrors = false;
}

if ( $wgWMFUseEcho && $wgWMFUseCentralAuth ) {
	$wgEchoSharedTrackingDB = 'wikishared';
	// Set cluster back to false, to override CommonSettings.php setting it to 'extension1'
	$wgEchoSharedTrackingCluster = false;
}

// Enabling thank-you-edit on beta for testing T128249. Still disabled in prod.
$wgEchoNotifications['thank-you-edit']['notify-type-availability']['web'] = true;

if ( $wgWMFUseGraph ) {
	// **** THIS LIST MUST MATCH puppet/hieradata/labs/deployment-prep/common.yaml ****
	// See https://www.mediawiki.org/wiki/Extension:Graph#External_data
	$wgGraphAllowedDomains['http'] = [ 'wmflabs.org' ];
	$wgGraphAllowedDomains['https'][] = 'beta.wmflabs.org';
	$wgGraphAllowedDomains['wikirawupload'][] = 'upload.beta.wmflabs.org';
	$wgGraphAllowedDomains['wikidatasparql'][] = 'wdqs-test.wmflabs.org';
}

if ( $wgWMFUseORES ) {
	wfLoadExtension( 'ORES' );
	$wgOresBaseUrl = 'https://ores-beta.wmflabs.org/';
}

if ( $wgWMFUseNewsletter ) {
	wfLoadExtension( 'Newsletter' );
}

if ( $wgWMFUsePerformanceInspector ) {
	wfLoadExtension( 'PerformanceInspector' );
}

if ( $wgWMFUseUniversalLanguageSelector ) {
	$wgDefaultUserOptions['compact-language-links'] = 0;
}

if ( $wgWMFUseEmailAuth ) {
	wfLoadExtension( 'EmailAuth' );
	// make it do something testable
	$wgHooks['EmailAuthRequireToken'][] = function (
		$user, &$verificationRequired, &$formMessage,
		&$subjectMessage, &$bodyMessage
	) {
		if ( preg_match( '/\+emailauth@/', $user->getEmail() ) ) {
			$verificationRequired = true;
			return false;
		}
	};
}

if ( $wgWMFUseLinter ) {
	wfLoadExtension( 'Linter' );
}

if ( $wgWMFUseCognate ) {
	wfLoadExtension( 'Cognate' );
	$wgCognateDb = 'cognate_' . $wgWMFUseCognate;
	$wgCognateCluster = 'extension1';
	$wgCognateNamespaces = [ 0 ];
}

if ( $wgWMFUseCollaborationKit ) {
	wfLoadExtension( 'CollaborationKit' );
}

if ( $wgWMFUseTimeless ) {
	// Test new Isarra responsive skin
	wfLoadSkin( 'Timeless' ); // T160643
}

if ( $wgWMFUseLoginNotify ) {
	wfLoadExtension( 'LoginNotify' );
	$wgLoginNotifyAttemptsKnownIP = 4;
}

$wgMessageCacheType = CACHE_ACCEL;

// Let Beta Cluster Commons do upload-from-URL from production Commons.
if ( $wgDBname == 'commonswiki' ) {
	$wgCopyUploadsDomains[] = 'upload.wikimedia.org';
}

// Test of new import source configuration on labs cluster
$wgImportSources = false;
include( "$wgWMFConfigDir/import.php" );
$wgHooks['ImportSources'][] = 'wmfImportSources';

// Reenable Preview and Changes tabs for wikieditor preview
$wgHiddenPrefs = array_diff ( $wgHiddenPrefs, [ 'wikieditor-preview' ] );

// MultimediaViewer is a dependency of 3d extension
if ( $wgWMFUse3d && $wgWMFUseMultimediaViewer ) {
	wfLoadExtension( '3D' );
	// Add 3d file type
	$wgFileExtensions[] = 'stl';
	$wgTrustedMediaFiles[] = 'application/sla';

	// Add 3d media viewer extension
	$wgMediaViewerExtensions['stl'] = 'mmv.3d';

	$wg3dProcessor = '/srv/deployment/3d2png/deploy/src/3d2png.js';
}

} # end safeguard
