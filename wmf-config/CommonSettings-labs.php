<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

# This file holds common overrides specific to Beta Cluster.
# For per-wiki overrides, see InitialiseSettings-labs.php.
#
# This for BETA and MUST NOT be loaded for production.
#
# Effective load order:
# - multiversion
# - mediawiki/DefaultSettings.php
# - wmf-config/InitialiseSettings.php
# - wmf-config/InitialiseSettings-labs.php
# - wmf-config/CommonSettings.php
# - wmf-config/CommonSettings-labs.php [THIS FILE]
#
# Load tree:
# - multiversion
# - mediawiki/index.php
# - mediawiki/WebStart.php
# - mediawiki/Setup.php
# - mediawiki/DefaultSettings.php
# - mediawiki/LocalSettings.php
#   `-- wmf-config/CommonSettings.php
#       |
#       |-- wmf-config/InitialiseSettings.php
#       |   `-- wmf-config/InitialiseSettings-labs.php
#       |
#       |-- (main stuff in CommonSettings.php)
#       |
#       `-- wmf-config/CommonSettings-labs.php [THIS FILE]
#

if ( $wmfRealm == 'labs' ) { # safe guard
if ( file_exists( '/etc/wmflabs-instancename' ) ) {
	$wgOverrideHostname = trim( file_get_contents( '/etc/wmflabs-instancename' ) );
}

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

if ( $wmgUseAbuseFilter) {
	$wgAbuseFilterLogIP = false; // Prevent the collection of IP addresses
	$wgAbuseFilterLogIPMaxAge = 2629746; // Purge data each 30 days, not 90
}

if ( $wmgUseFlow ) {
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

if ( $wmgUseFileExporter ) {
	// On Beta don't bother enabling beta feature mode.
	$wgFileExporterBetaFeature = false;
	$wgFileExporterTarget = 'https://commons.wikimedia.beta.wmflabs.org/wiki/Special:ImportFile';
}

if ( $wmgUseContentTranslation ) {
	$wgContentTranslationSiteTemplates['cx'] = 'https://cxserver-beta.wmflabs.org/v1';
	$wgContentTranslationSiteTemplates['cookieDomain'] = false;
	$wgContentTranslationTranslateInTarget = false;
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
	// $wgVERPsecret = ''; // This was set in PrivateSettings.php by Legoktm
	$wgBounceHandlerCluster = false;
	$wgBounceHandlerSharedDB = false;
	$wgBounceHandlerInternalIPs = [ '127.0.0.1', '::1', '10.68.17.78' ]; // deployment-mx.wmflabs.org
	$wgBounceHandlerUnconfirmUsers = true;
	$wgBounceRecordLimit = 5;
	$wgVERPdomainPart = 'beta.wmflabs.org';
}

if ( $wmgUseTimedMediaHandler ) {
	$wgMwEmbedModuleConfig[ 'MediaWiki.ApiProviders' ] = [
	"commons" => [
		'url' => '//commons.wikimedia.beta.wmflabs.org/w/api.php'
	] ];
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
if ( $wmgEnableTabularData && $wgDBname !== 'commonswiki' ) {
	$wgJsonConfigs['Tabular.JsonConfig']['remote']['url'] = 'https://commons.wikimedia.beta.wmflabs.org/w/api.php';
}

// Enable Map (GeoJSON) data namespace on Commons - T149548
if ( $wmgEnableMapData && $wgDBname !== 'commonswiki' ) {
	$wgJsonConfigs['Map.JsonConfig']['remote']['url'] = 'https://commons.wikimedia.beta.wmflabs.org/w/api.php';
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

if ( $wmgUseCollection ) {
	$wgCollectionPortletFormats[] = 'rdf2text';
	// Don't use production proxy to reach PediaPress
	$wgCollectionCommandToServeURL[ 'zip_post' ] = 'https://pediapress.com/wmfup/';
}

if ( $wmgUsePageImages ) {
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

if ( $wmgUseEcho && $wmgUseCentralAuth ) {
	$wgEchoSharedTrackingDB = 'wikishared';
	// Set cluster back to false, to override CommonSettings.php setting it to 'extension1'
	$wgEchoSharedTrackingCluster = false;
}

// Enabling thank-you-edit on beta for testing T128249. Still disabled in prod.
$wgEchoNotifications['thank-you-edit']['notify-type-availability']['web'] = true;

// Enabling article-reminder on beta for testing T166973. Still disabled in prod.
$wgAllowArticleReminderNotification = true;

if ( $wmgUseGraph ) {
	// **** THIS LIST MUST MATCH puppet/hieradata/labs/deployment-prep/common.yaml ****
	// See https://www.mediawiki.org/wiki/Extension:Graph#External_data
	$wgGraphAllowedDomains['http'] = [ 'wmflabs.org' ];
	$wgGraphAllowedDomains['https'][] = 'beta.wmflabs.org';
	$wgGraphAllowedDomains['wikirawupload'][] = 'upload.beta.wmflabs.org';
	$wgGraphAllowedDomains['wikidatasparql'][] = 'wdqs-test.wmflabs.org';
}

if ( $wmgUseORES ) {
	$wgOresBaseUrl = 'https://ores-beta.wmflabs.org/';
}

if ( $wmgUseUniversalLanguageSelector ) {
	$wgDefaultUserOptions['compact-language-links'] = 0;
}

if ( $wmgUseLoginNotify ) {
	$wgLoginNotifyAttemptsKnownIP = 10;
	$wgLoginNotifyAttemptsNewIP = 1;
}

$wgMessageCacheType = CACHE_ACCEL;

// Let Beta Cluster Commons do upload-from-URL from production Commons.
if ( $wgDBname == 'commonswiki' ) {
	$wgCopyUploadsDomains[] = 'upload.wikimedia.org';
}

// Test of new import source configuration on labs cluster
$wgImportSources = false;
include "$wmfConfigDir/import.php";
$wgHooks['ImportSources'][] = 'wmfImportSources';

// Reenable Preview and Changes tabs for wikieditor preview
$wgHiddenPrefs = array_diff( $wgHiddenPrefs, [ 'wikieditor-preview' ] );

$wgAuthManagerAutoConfig['preauth'][GuanacoProvider::class] = [
	'class' => GuanacoProvider::class,
	'sort' => 0,
];
class GuanacoProvider extends \MediaWiki\Auth\AbstractPreAuthenticationProvider {
	const EVILUA = 'Bawolff test';

	public function testUserForCreation( $user, $autocreate, array $options = [] ) {
		return $this->testUser( $user );
	}

	public function testForAccountCreation( $user, $creator, array $reqs ) {
		return $this->testUser( $user );
	}

	public function testUser( $user ) {
		$ua = $this->manager->getRequest()->getHeader( 'User-agent' );
		$logger = \MediaWiki\Logger\LoggerFactory::getInstance( 'badpass' );
		if ( $ua === self::EVILUA ) {
			$logger->info( 'Account creation prevented due to UA {name}', [
				'successful' => false,
				'name' => $user->getName(),
				'ua' => $ua,
			] );
			// To be misleading, claim its a throttle hit.
			// hopefully this will confuse attacker.
			$msg = wfMessage( 'acct_creation_throttle_hit' )->params( 6 )
				->durationParams( 86400 );
			return \StatusValue::newFatal( $msg );
		}

		$logger->info( 'Account creation allowed due to UA {name}', [
			'successful' => true,
			'name' => $user->getName(),
			'ua' => $ua,
		] );
		return \StatusValue::newGood();
	}
}

} # end safeguard
