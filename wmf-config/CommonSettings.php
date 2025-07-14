<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

# CommonSettings.php is the main configuration file of the WMF cluster.
# This file contains settings common to all (or many) WMF wikis.
# For per-wiki configuration, see InitialiseSettings.php.
#
# This is for PRODUCTION.
#
# Effective load order:
# - multiversion
# - mediawiki/DefaultSettings.php
# - wmf-config/InitialiseSettings.php
# - wmf-config/CommonSettings.php [THIS FILE]
#
# Full load tree:
# - multiversion
# - mediawiki/index.php (or other entry point)
# - mediawiki/WebStart.php
# - mediawiki/Setup.php
# - mediawiki/DefaultSettings.php
# - mediawiki/LocalSettings.php
#   `-- wmf-config/CommonSettings.php [THIS FILE]
#       |-- wmf-config/*Services.php
#       |-- wmf-config/InitialiseSettings.php
#       |-- private/PrivateSettings.php
#       |-- wmf-config/logging.php
#       |-- wmf-config/filebackend.php
#       |-- wmf-config/db-*.php
#       |-- wmf-config/mc.php
#       |
#       `-- (main stuff in CommonSettings.php)
#

// Inline comments are often used for noting the task(s) associated with specific configuration
// and requiring comments to be on their own line would reduce readability for this file
// phpcs:disable MediaWiki.WhiteSpace.SpaceBeforeSingleLineComment.NewLineComment

use MediaWiki\Auth\AbstractPreAuthenticationProvider;
use MediaWiki\Auth\AuthenticationResponse;
use MediaWiki\Auth\LocalPasswordPrimaryAuthenticationProvider;
use MediaWiki\Auth\PasswordAuthenticationRequest;
use MediaWiki\Content\FallbackContentHandler;
use MediaWiki\Extension\ApiFeatureUsage\ApiFeatureUsageQueryEngineElastica;
use MediaWiki\Extension\CentralAuth\RCFeed\IRCColourfulCARCFeedFormatter;
use MediaWiki\Extension\CentralAuth\User\CentralAuthUser;
use MediaWiki\Extension\ConfirmEdit\FancyCaptcha\FancyCaptcha;
use MediaWiki\Extension\ConfirmEdit\Store\CaptchaCacheStore;
use MediaWiki\Extension\EventBus\Adapters\JobQueue\JobQueueEventBus;
use MediaWiki\Extension\EventBus\Adapters\RCFeed\EventBusRCFeedEngine;
use MediaWiki\Extension\EventBus\Adapters\RCFeed\EventBusRCFeedFormatter;
use MediaWiki\Extension\ExtensionDistributor\Providers\GerritExtDistProvider;
use MediaWiki\Extension\Notifications\Push\PushNotifier;
use MediaWiki\Html\Html;
use MediaWiki\Json\FormatJson;
use MediaWiki\Logger\LoggerFactory;
use MediaWiki\MediaWikiServices;
use MediaWiki\Session\SessionManager;
use MediaWiki\Title\Title;
use MediaWiki\User\UserIdentity;
use Wikimedia\MWConfig\ClusterConfig;
use Wikimedia\MWConfig\DBRecordCache;
use Wikimedia\MWConfig\ServiceConfig;
use Wikimedia\MWConfig\WmfConfig;
use Wikimedia\MWConfig\XWikimediaDebug;

# Godforsaken hack to work around problems with the reverse proxy caching changes...
#
# To minimize damage on fatal PHP errors, output a default no-cache header
# It will be overridden in cases where we actually specify caching behavior.
#
# More modern PHP versions will send a 500 result code on fatal error,
# at least sometimes, but what we're running will send a 200.
if ( PHP_SAPI !== 'cli' ) {
	header( "Cache-control: no-cache" );
}

# ----------------------------------------------------------------------
# Initialisation

require_once __DIR__ . '/../src/XWikimediaDebug.php';
require_once __DIR__ . '/../src/ServiceConfig.php';
require_once __DIR__ . '/../src/ClusterConfig.php';
require_once __DIR__ . '/../src/DBRecordCache.php';
require_once __DIR__ . '/../src/etcd.php';
require_once __DIR__ . '/../src/WmfConfig.php';

// Past this point we know:
//
// - This file must be included by MediaWiki via our LocalSettings.php file.
//   Determined by MediaWiki core having initialised the $IP variable.
//
// - MediaWiki must be included via a multiversion-aware entry point
//   (e.g. WMF's "w/index.php", or MWScript).
//   That entry point must have initialised the MWMultiVersion singleton and
//   decided which wiki we are on (based on hostname or --wiki CLI arg).
//   Note that getInstance does not (and may not) lazy-create this instance,
//   it returns null if it hasn't been setup yet.
//
// - The wiki ID must be determined by MWMultiVersion::getMediaWiki, as called
//   in the entry point (e.g. based on server name or --wiki). MWMultiVersion
//   asserts that it is known in wikiversions.json, and otherwise bails
//   by rendering `missing.php`.
//
//   We can naturally only reach here (wmf-config/CommonSettings.php) if the
//   wiki was known, the MW version was known, and MW core then loaded
//   this file via LocalSettings.php.
//
$multiVersion = class_exists( 'MWMultiVersion' ) ? MWMultiVersion::getInstance() : null;
if ( !isset( $IP ) || !$multiVersion ) {
	print "No MWMultiVersion instance initialized! MWScript.php wrapper not used?\n";
	exit( 1 );
}

$includePaths = [ $IP, '/usr/local/lib/php', '/usr/share/php' ];
if ( is_readable( "$IP/vendor/composer/include_paths.php" ) ) {
	$includePaths = array_merge(
		require "$IP/vendor/composer/include_paths.php",
		$includePaths
	);
}
set_include_path( implode( PATH_SEPARATOR, $includePaths ) );

### List of some service hostnames
# 'meta'    : meta wiki for user editable content
# 'upload'  : hostname where files are hosted
# 'wikidata': hostname for the data repository
# Whenever all realms/datacenters should use the same host, do not use
# $wmgHostnames but use the hardcoded hostname instead. A good example are the
# spam blacklists hosted on meta.wikimedia.org which you will surely want to
# reuse.
$wmgHostnames = [];
switch ( $wmgRealm ) {
	case 'labs':
		$wmgHostnames['meta']          = 'meta.wikimedia.beta.wmcloud.org';
		$wmgHostnames['auth']          = 'auth.wikimedia.beta.wmcloud.org';
		$wmgHostnames['test']          = 'test.wikipedia.beta.wmcloud.org';
		$wmgHostnames['upload']        = 'upload.wikimedia.beta.wmcloud.org';
		$wmgHostnames['wikidata']      = 'www.wikidata.beta.wmcloud.org';
		break;
	case 'production':
	default:
		$wmgHostnames['meta']          = 'meta.wikimedia.org';
		$wmgHostnames['auth']          = 'auth.wikimedia.org';
		$wmgHostnames['test']          = 'test.wikipedia.org';
		$wmgHostnames['upload']        = 'upload.wikimedia.org';
		$wmgHostnames['wikidata']      = 'www.wikidata.org';
		$wmgHostnames['wikifunctions'] = 'www.wikifunctions.org';
		break;
}

$wgDBname = $multiVersion->getDatabase();
$wgDBuser = 'wikiuser'; // Don't rely on MW default (T46251)
$wgSQLMode = null;

$wmgVersionNumber = $multiVersion->getVersionNumber();

# Used further down this file for per-wiki SiteConfiguration caching:
# - MUST be in /tmp to allow atomic rename from elsewhere in /tmp.
# - MUST vary by MediaWiki branch (aka MWMultiversion deployment version).
#
# Also used by MediaWiki core for various caching purposes, and may
# be shared between wikis (e.g. does not need to vary by wgDBname).
$wgCacheDirectory = '/tmp/mw-cache-' . $wmgVersionNumber;

# Get all the service definitions
$wmgAllServices = ServiceConfig::getInstance()->getAllServices();

# Shorthand when we have no master-replica situation to keep into account
$wmgLocalServices = $wmgAllServices[$wmgDatacenter];

# The list of datacenters known to this realm
$wmgDatacenters = ServiceConfig::getInstance()->getDatacenters();

if ( getenv( 'WMF_MAINTENANCE_OFFLINE' ) ) {
	// Prepare just enough configuration to allow
	// rebuildLocalisationCache.php and mergeMessageFileList.php to
	// run to completion without complaints.

	$wmgEtcdLastModifiedIndex = "wmgEtcdLastModifiedIndex uninitialized due to WMF_MAINTENANCE_OFFLINE";
	$wgReadOnly = "In read-only mode because WMF_MAINTENANCE_OFFLINE is set";
	$wmgMasterDatacenter = ServiceConfig::getInstance()->getDatacenter();
	$wmgMasterServices = $wmgAllServices[$wmgMasterDatacenter];
	$wmgLocalDbConfig = [
		'readOnlyBySection' => null,
		'groupLoadsBySection' => [
			'DEFAULT' => [
				'' => [
				   'WMF_MAINTENANCE_OFFLINE_placeholder' => 0
				],
			],
		],
		'hostsByName' => null,
		'sectionLoads' => [],
		'externalLoads' => [],
	];
	$wmgRemoteMasterDbConfig = null;
	$wmgLBFactoryConfigCallback = null;
} else {
	$etcdConfig = wmfSetupEtcd( $wmgLocalServices['etcd'] );

	$wmgEtcdLastModifiedIndex = $etcdConfig->getModifiedIndex();
	$wgReadOnly = $etcdConfig->get( "$wmgDatacenter/ReadOnly" );
	$wmgMasterDatacenter = $etcdConfig->get( 'common/WMFMasterDatacenter' );
	$wmgMasterServices = $wmgAllServices[$wmgMasterDatacenter];

	// Database load balancer config (sectionLoads, groupLoadsBySection, …)
	// This is later merged into $wgLBFactoryConf by wmfApplyEtcdDBConfig().
	// See also <https://wikitech.wikimedia.org/wiki/Dbctl>
	$wmgLocalDbConfig = $etcdConfig->get( "$wmgDatacenter/dbconfig" );
	if ( $wmgDatacenter !== $wmgMasterDatacenter ) {
		$wmgRemoteMasterDbConfig = $etcdConfig->get( "$wmgMasterDatacenter/dbconfig" );
	} else {
		$wmgRemoteMasterDbConfig = null;
	}

	// Define a callback for use by LBFactory::autoReconfigure().
	// This allows long-running scripts to take into account changes to the database config (T298485).
	// The callback below does not support data center switches, but does support
	// read-only flags and changes to replica weights. In particular, it allows a replica
	// to be taken out of rotation.
	if ( PHP_SAPI === 'cli' ) {
		$wmgLBFactoryConfigCallback = static function () use ( $wmgLocalServices, $wmgDatacenter ) {
			// NOTE: Don't re-use the existing $etcdConfig, the entire point of this
			//       callback is that the we want to re-load it to see if it has changed.
			$etcdConfig = wmfSetupEtcd( $wmgLocalServices['etcd'] );
			$dbConfigFromEtcd = $etcdConfig->get( "$wmgDatacenter/dbconfig" );
			$lbFactoryConf = [];
			wmfApplyEtcdDBConfig( $dbConfigFromEtcd, $lbFactoryConf );
			$lbFactoryConf['class'] = 'LBFactoryMulti';
			// On dumps, we use a different set of databases (T382947)
			if ( ClusterConfig::getInstance()->isDumps() ) {
				DBRecordCache::getInstance()->repopulateDbConf( $lbFactoryConf );
			}
			return $lbFactoryConf;
		};
	} else {
		$wmgLBFactoryConfigCallback = null;
	}

	unset( $etcdConfig );

	# ######################################################################
	# StatsD/Metrics Settings
	# ######################################################################
	$wgStatsFormat = 'dogstatsd';
	# On kubernetes, use the exporter service where available, not the pod sidecar.
	# When it is the case, the kubernetes api will populate the environment variable
	# STATSD_EXPORTER_PROMETHEUS_SERVICE_HOST with the service IP.
	$statsHost = $_SERVER['STATSD_EXPORTER_PROMETHEUS_SERVICE_HOST'] ?? 'localhost';
	$wgStatsTarget = "udp://$statsHost:9125";
	$wgStatsdServer = $wmgLocalServices['statsd'];
}

$wmgUdp2logDest = $wmgLocalServices['udp2log'];
if ( $wgDBname === 'testwiki' ) {
	$wmgExtraLogFile = "udp://{$wmgUdp2logDest}/testwiki";
} else {
	$wmgExtraLogFile = '/dev/null';
}

$wgConf = new SiteConfiguration;
$wgConf->suffixes = WmfConfig::SUFFIXES;
$wgConf->wikis = WmfConfig::readDbListFile( $wmgRealm === 'labs' ? 'all-labs' : 'all' );
$wgConf->settings = WmfConfig::getStaticConfig( $wmgRealm );

$wgLocalDatabases = $wgConf->getLocalDatabases();

// Do not add wikimedia.org, because of other non-MediaWiki sites under that domain
// Do not add wikidata.org, because of query.wikidata.org
// If a non-MediaWiki site gets into this list, MediaWiki will not be able to
// make HTTP requests to it.
// At a minimum, all SUL-linked wikis must be included here for cross-wiki HTTP
// requests to work (GlobalUserPage, cross-wiki notifications, etc.).
// TODO: add remaining MediaWiki sites
$wgLocalVirtualHosts = [
	'wikipedia.org',
	'wiktionary.org',
	'wikiquote.org',
	'wikibooks.org',
	'wikiquote.org',
	'wikinews.org',
	'wikisource.org',
	'wikiversity.org',
	'wikivoyage.org',
	'www.mediawiki.org',
	'www.wikidata.org',
	'test.wikidata.org',
	'api.wikimedia.org',
	'ae.wikimedia.org',
	'ar.wikimedia.org',
	'az.wikimedia.org',
	'bd.wikimedia.org',
	'be.wikimedia.org',
	'br.wikimedia.org',
	'ca.wikimedia.org',
	'co.wikimedia.org',
	'commons.wikimedia.org',
	'dk.wikimedia.org',
	'ee.wikimedia.org',
	'fi.wikimedia.org',
	'foundation.wikimedia.org',
	'incubator.wikimedia.org',
	'login.wikimedia.org',
	'meta.wikimedia.org',
	'mk.wikimedia.org',
	'mx.wikimedia.org',
	'nl.wikimedia.org',
	'no.wikimedia.org',
	'nyc.wikimedia.org',
	'outreach.wikimedia.org',
	'pl.wikimedia.org',
	'pt.wikimedia.org',
	'ru.wikimedia.org',
	'se.wikimedia.org',
	'species.wikimedia.org',
	'auth.wikimedia.org',
	'test-commons.wikimedia.org',
	'tr.wikimedia.org',
	'ua.wikimedia.org',
	've.wikimedia.org',
	'wikimania.wikimedia.org',
	'wikipedia-it-arbcom.wikimedia.org',
	'wikipedia-zh-arbcom.wikimedia.org',
	'wikitech.wikimedia.org',
	'www.wikifunctions.org',
	'm.wikifunctions.org',
];

$globals = WmfConfig::getConfigGlobals(
	$wgDBname,
	$wgConf,
	$wmgRealm
);

// phpcs:ignore MediaWiki.Usage.ForbiddenFunctions.extract
extract( $globals );

# Determine legacy site/lang pair for the current wiki
[ $site, $lang ] = $wgConf->siteFromDB( $wgDBname );

# -------------------------------------------------------------------------
# Settings common to all wikis

# Private settings such as passwords, that shouldn't be published
# Needs to be before db.php
require __DIR__ . '/../private/PrivateSettings.php';

require __DIR__ . '/logging.php';
$wgMWLoggerDefaultSpi = [
	'class' => \MediaWiki\Logger\MonologSpi::class,
	'args' => [ wmfGetLoggingConfig() ],
];
wmfApplyDebugLoggingHacks();

require __DIR__ . '/filebackend.php';
require __DIR__ . '/mc.php';
if ( $wmgRealm === 'labs' ) {
	// Beta Cluster overrides
	require __DIR__ . '/mc-labs.php';
}
# db-*.php needs $wgDebugDumpSql so should be loaded after logging.php.
# Ref: db-production.php or db-labs.php
require __DIR__ . "/db-$wmgRealm.php";

# Override certain settings in command-line mode
# This must be after InitialiseSettings.php is processed (T197475)
if ( PHP_SAPI === 'cli' ) {
	$wgShowExceptionDetails = true;
}

if ( XWikimediaDebug::getInstance()->hasOption( 'readonly' ) ) {
	$wgReadOnly = 'X-Wikimedia-Debug';
}
$wgAllowedCorsHeaders[] = 'X-Wikimedia-Debug';

// The parsercache section-to-server mapping. Must be defined before calls to
// wmfApplyEtcdDBConfig.
$wmgPCServers = $wmgLocalServices['parsercache-dbs'];

// In production, read the database loadbalancer config and parsercache
// section-to-server mapping from etcd.
// See https://wikitech.wikimedia.org/wiki/Dbctl
// This must be called after db-production.php has been loaded!
// It overwrites a few sections of $wgLBFactoryConf with data from etcd.
// In labs, the relevant key exists in etcd, but does not contain real data.
// Only do this in production.
if ( $wmgRealm === 'production' ) {
	require __DIR__ . "/db-sections.php";

	wmfApplyEtcdDBConfig( $wmgLocalDbConfig, $wgLBFactoryConf );
	// Add the config callback
	$wgLBFactoryConf['configCallback'] = $wmgLBFactoryConfigCallback;

	// CentralAuth DB lives on s7 since it was created prior to x1
	$wgLBFactoryConf['sectionsByDB']['centralauth'] = 's7';

	$wgLBFactoryConf['loadMonitor']['class'] = '\Wikimedia\Rdbms\LoadMonitor';
	// Disable LoadMonitor in CLI, it doesn't provide much value in CLI.
	if ( PHP_SAPI === 'cli' ) {
		$wgLBFactoryConf['loadMonitor']['class'] = '\Wikimedia\Rdbms\LoadMonitorNull';
	}
	// T360930
	$wgLBFactoryConf['loadMonitor']['maxConnCount'] = 350;

	// Dumps use their own set of databases
	if ( ClusterConfig::getInstance()->isDumps() ) {
		DBRecordCache::getInstance()->repopulateDbConf( $wgLBFactoryConf );
	}
}

// Set $wgProfiler to the value provided by PhpAutoPrepend.php
if ( isset( $wmgProfiler ) ) {
	$wgProfiler = $wmgProfiler;
}

# Disallow web request DB transactions slower than this
$wgMaxUserDBWriteDuration = 3;

# Activate read-only mode for bots when lag is getting high.
# This should be lower than 'max lag' in the LBFactory conf.
$wgAPIMaxLagThreshold = 3;

# Set the max memory used.
ini_set( 'memory_limit', $wmgMemoryLimit );

# Change calls to wfShellWikiCmd() to use MWScript.php wrapper
$wgHooks['wfShellWikiCmd'][] = 'MWMultiVersion::onWfShellMaintenanceCmd';

setlocale( LC_ALL, 'en_US.UTF-8' );

# Only enable OpenTelemetry tracing on Kubernetes.  It is not (yet?) supported on bare metal.
if ( ClusterConfig::getInstance()->isK8s() ) {
	$wgOpenTelemetryConfig = [
		'serviceName' => 'mediawiki',
		'endpoint' => 'http://main-opentelemetry-collector.opentelemetry-collector.svc.cluster.local:4318/v1/traces',
		'samplingProbability' => 0, # Never initiate, just follow Envoy's decision
	];
}

# ######################################################################
# Revision backend settings
# ######################################################################

$wgCompressRevisions = true;

$wgExternalStores = [ 'DB' ];

# ######################################################################
# Performance settings and restrictions
# ######################################################################

// Replicas aren't fast enough to generate all special pages all the time.
$wgMiserMode = true;

$wgQueryCacheLimit = 5000;

$wgParserCacheExpireTime = 86400 * 30;
// Override talk pages to have 10 days only (T280605)
$wgDiscussionToolsTalkPageParserCacheExpiry = 86400 * 10;

// Old revision parser cache expire in 1 hour
$wgOldRevisionParserCacheExpireTime = 3600;

// This feature would vastly increase the size of the CDN cache, and increase
// MW appserver load.
$wgULSLanguageDetection = false;

/**
 * Configure PHP request timeouts. These should be slightly less than the Apache
 * timeouts, so that the slightly more informative PHP error message is
 * delivered to the user, and so that we can verify that PHP timeouts actually
 * exist (T97192).
 */
if ( PHP_SAPI === 'cli' ) {
	// Should always be unlimited, this is probably redundant
	$wgRequestTimeLimit = 0;
} elseif ( XWikimediaDebug::getInstance()->hasOption( 'shorttimeout' ) ) {
	// To probe for excimer-related memory corruption e.g. T293568
	$wgRequestTimeLimit = 2;
} else {
	// Videoscalers have a 1 day timeout.
	// Jobrunners have 20 minutes.
	// Everything else has 60 seconds for GETs and 200s for POSTs
	if ( ClusterConfig::getInstance()->isAsync() ) {
		if ( strpos( $_SERVER['HTTP_HOST'] ?? '', 'videoscaler.' ) === 0 || strpos( $_SERVER['HTTP_HOST'] ?? '', 'shellbox-video.' ) === 0 ) {
			$wgRequestTimeLimit = 86400;
		} else {
			$wgRequestTimeLimit = 1200;
		}
	} else {
		if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
			$wgRequestTimeLimit = 200;
		} else {
			$wgRequestTimeLimit = 60;
		}
	}
}

# ######################################################################
# Account- and notifications-related settings
# ######################################################################

$wgSecureLogin = true;

$wgMaxNameChars = 85;

// Turn this on so UserMailer::send() will be able to send both text and html email
$wgAllowHTMLEmail = true;

$wgEnotifUserTalk = true;

$wgEnotifWatchlist = true;

// Keep this true; it's just whether the feature is available at all, not the default
// setting. T142727
$wgEnotifMinorEdits = true;

// Use the same expiry for core sessions as for CentralAuth sessions (T313496)
$wgObjectCacheSessionExpiry = 86400;

# ######################################################################
# Anti-abuse settings
# ######################################################################

$wgEnableUserEmailMuteList = true;

if ( $wmgUseGlobalPreferences ) {
	// Allow global preferences for email-blacklist and echo-notifications
	// to be auto-set where it is overridden
	$wgGlobalPreferencesAutoPrefs = [
		'email-blacklist',
		'echo-notifications-blacklist'
	];
}

// T355034 new block_target schema
$wgBlockTargetMigrationStage = SCHEMA_COMPAT_NEW;

# ######################################################################
# Legal matters
# ######################################################################

$wgRightsIcon = '//creativecommons.org/images/public/somerights20.png';

# ######################################################################
# ResourceLoader settings
# ######################################################################

// The extra path segment used on auth.wikimedia.org, which is a shared domain
// used by all SUL wikis. (T363695) E.g. when the current request is for
// https://auth.wikimedia.org/enwiki/wiki/Special:Userlogin,
// this will be '/enwiki'. Empty when the current request is not
// for auth.wikimedia.org (which also allows it to be used later in this file
// as a feature flag).
$wmgSharedDomainPathPrefix = '';

// This must match the condition at MWMultiVersion::initializeFromServerData()
if ( @$_SERVER['SERVER_NAME'] === $wmgHostnames['auth']
	|| getenv( 'MW_USE_SHARED_DOMAIN' )
) {
	// Fail hard if the current wiki is not a SUL wiki - it's not a security
	// risk since their normal authentication is applied, but extra defense in
	// depth never hurts.
	// Further defense in depth will happen in the CentralAuth extension.
	if ( !$wmgUseCentralAuth ) {
		print "auth.wikimedia.org can only be used for SUL wikis\n";
		exit( 1 );
	}

	// Do this before updating $wgScriptPath and $wgCanonicalServer when we are
	// on auth.wikimedia.org. We want ResourceLoader to use the standard URL
	// regardless to avoid an unnecessary cache split.
	$wgLoadScript = "{$wgCanonicalServer}{$wgScriptPath}/load.php";
	$wmgSharedDomainPathPrefix = "/$wgDBname";

	// Override $wgServer, $wgCanonicalServer, and (below) $wgArticlePath,
	// $wgScriptPath and $wgResourceBasePath for auth.wikimedia.org which might
	// be using the configuration of any SUL wiki.
	//
	// Note that we don't override $wgConf->settings[...][$wgDBname].
	// If anything uses that, chances are it's for some sort of external link for
	// which it's better to use the canonical domain name of the current wiki.
	$wgServer = '//' . $wmgHostnames['auth'];
	$wgCanonicalServer = 'https://' . $wmgHostnames['auth'];

	// This cache can't be split based on $wmgSharedDomainPathPrefix (T383916).
	// It's not critical given the expected low number of views on the auth domain.
	$wgEnableSidebarCache = false;
} else {
	$wgLoadScript = "{$wgScriptPath}/load.php";
}

// Override some settings while we are on the shared authentication domain
// (see T373737), as a security hardening measure. Note that these overrides
// might or might not get applied to the same wiki depending on what domain
// is used to access it, and so must not include anything that requires
// consistency between requests (e.g. due to caching).
if ( $wmgSharedDomainPathPrefix ) {
	// Disable on-wiki JS/CSS. This is mostly ineffective since these are mainly used by ResourceLoader, and load.php
	// is invoked via the canonical domain. (We rely on OutputPage::disallowUserJs() instead.) Still, can't hurt.
	$wgUseSiteCss = false;
	$wgUseSiteJs = false;
	$wgAllowUserCss = false;
	$wgAllowUserJs = false;
}

$wgInternalServer = $wgCanonicalServer;
$wgArticlePath = "{$wmgSharedDomainPathPrefix}/wiki/\$1";

$wgScriptPath  = "{$wmgSharedDomainPathPrefix}/w";
$wgScript = "{$wgScriptPath}/index.php";

// Don't include a hostname in $wgResourceBasePath and friends
// - Goes wrong otherwise on mobile web (T106966, T112646)
// - Improves performance by leveraging HTTP/2
// - $wgLocalStylePath MUST be relative
// Apache rewrites /w/resources, /w/extensions, and /w/skins to /w/static.php (T99096)
$wgResourceBasePath = "{$wmgSharedDomainPathPrefix}/w";
$wgExtensionAssetsPath = "{$wgResourceBasePath}/extensions";
$wgStylePath = "{$wgResourceBasePath}/skins";
$wgLocalStylePath = $wgStylePath;

$wgResourceLoaderMaxQueryLength = 5000;

$wgGitInfoCacheDirectory = "$IP/cache/gitinfo";

// @var string|bool: E-mail address to send notifications to, or false to disable notifications.
$wmgAddWikiNotify = "newprojects@lists.wikimedia.org";

$wgLocalisationCacheConf['storeClass'] = LCStoreCDB::class;
$wgLocalisationCacheConf['storeDirectory'] = "$IP/cache/l10n";
$wgLocalisationCacheConf['manualRecache'] = true;

// Add some useful config data to query=siteinfo
$wgHooks['APIQuerySiteInfoGeneralInfo'][] = static function ( $module, &$data ) {
	global $wmgMasterDatacenter, $wmgEtcdLastModifiedIndex;
	$data['wmf-config'] = [
		'wmfMasterDatacenter' => $wmgMasterDatacenter,
		'wmfEtcdLastModifiedIndex' => $wmgEtcdLastModifiedIndex,
	];
};

$wgEmergencyContact = 'noc@wikipedia.org';

# Default address gets rejected by some mail hosts.
# This email is used for more than just sending password resets, also e.g. Echo notifications
# and random contact forms.
$wgPasswordSender = 'wiki@wikimedia.org';

$wgRCMaxAge = 30 * 86400;

$wgTmpDirectory = '/tmp';

$wgOverrideUcfirstCharacters = include __DIR__ . '/UcfirstOverrides.php';

# Object cache and session settings

$wgSessionName = $wgDBname . 'Session';

$pcServers = [];
foreach ( $wmgPCServers as $tag => $host ) {
	$pcServers[$tag] = [
		'type' => 'mysql',
		'host' => $host,
		'dbname' => 'parsercache',
		'user' => $wgDBuser,
		'password' => $wgDBpassword,
		'flags' => 0,
		'connectTimeout' => 1,
		'receiveTimeout' => 5,
	];
}

if ( $wmgRealm === 'labs' ) {
	$wmgMainStashServers = [
		// deployment-db11.deployment-prep.eqiad1.wikimedia.cloud
		'ms1' => '172.16.5.150',
		// deployment-db14.deployment-prep.eqiad1.wikimedia.cloud
		'ms2' => '172.16.5.170',
	];
}
$mainStashServers = [];
foreach ( $wmgMainStashServers as $tag => $host ) {
	$mainStashServers[$tag] = [
		'type' => 'mysql',
		'host' => $host,
		'user' => $wgDBuser,
		'password' => $wgDBpassword,
		'dbname' => 'mainstash',
		'flags' => 0,
	];
}

$wgObjectCaches['mysql-multiwrite'] = [
	'class' => 'MultiWriteBagOStuff',
	'caches' => [
		0 => [
			'factory' => [ 'ObjectCache', 'getInstance' ],
			'args' => [ 'mcrouter' ]
		],
		1 => [
			'class' => 'SqlBagOStuff',
			'servers' => $pcServers,
			'purgePeriod' => 0,
			'tableName' => 'pc',
			'shards' => 256,
			'reportDupes' => false
		],
	],
	'replication' => 'async',
	'reportDupes' => false
];
$wgObjectCaches['kask-session'] = [
	'class' => 'RESTBagOStuff',
	'url' => "{$wmgLocalServices['sessionstore']}/sessions/v1/",
	'httpParams' => [
		'writeHeaders' => [
			'content-type' => 'application/octet-stream',
		],
		'writeMethod' => 'POST',
	],
	'serialization_type' => 'PHP',
	'hmac_key' => $wmgSessionStoreHMACKey,
	'extendedErrorBodyFields' => [ 'type', 'title', 'detail', 'instance' ]
];
$wgObjectCaches['kask-echoseen'] = [
	'class' => 'RESTBagOStuff',
	'url' => "{$wmgLocalServices['echostore']}/echoseen/v1/",
	'httpParams' => [
		'writeHeaders' => [
			'content-type' => 'application/octet-stream',
		],
		'writeMethod' => 'POST',
	],
	'serialization_type' => 'JSON',
	'extendedErrorBodyFields' => [ 'type', 'title', 'detail', 'instance' ],
	'reportDupes' => false,
];
$wgObjectCaches['db-mainstash'] = [
	'class' => 'SqlBagOStuff',
	'servers' => $mainStashServers,
	'tableName' => 'objectstash',
	'multiPrimaryMode' => true,
	'purgePeriod' => 100,
	'purgeLimit' => 1000,
	'reportDupes' => false,
	'dataRedundancy' => 2,
];

$wgPasswordDefault = 'E';

$wgPasswordConfig['E'] = [
	'class' => EncryptedPassword::class,
	'underlying' => 'argon2',
	'secrets' => [ $wmgPasswordSecretKey ],
	'cipher' => 'aes-256-cbc',
];
$wgPasswordConfig['argon2'] = [
	'class' => Argon2Password::class,
	'algo' => 'argon2id',
	'memory_cost' => 131072,
	'time_cost' => 4,
	'threads' => 4,
];
$wgPasswordConfig['EP'] = [
	'class' => EncryptedPassword::class,
	'underlying' => 'pbkdf2',
	'secrets' => [ $wmgPasswordSecretKey ],
	'cipher' => 'aes-256-cbc',
];
$wgPasswordConfig['BEP'] = [
	'class' => LayeredParameterizedPassword::class,
	'types' => [ 'B', 'EP' ],
];
$wgPasswordConfig['pbkdf2'] = [
	'class' => Pbkdf2PasswordUsingOpenSSL::class,
	'algo' => 'sha512',
	'cost' => '128000',
	'length' => '64',
];

// Temporary for T57420
$wgPasswordConfig['null'] = [ 'class' => InvalidPassword::class ];

// Password policies; see https://meta.wikimedia.org/wiki/Password_policy
//
// For global policies, see $wgCentralAuthGlobalPasswordPolicies below
$wmgPrivilegedPolicy = [
	'MinimalPasswordLength' => [ 'value' => 10, 'suggestChangeOnLogin' => true, 'forceChange' => true ],
	// With MinimumPasswordLengthToLogin, if the length of the password is <= the value
	// of the policy, the user will be forced to use Special:PasswordReset or similar
	// to be able to get into their account
	'MinimumPasswordLengthToLogin' => [ 'value' => 1 ],
	'PasswordNotInCommonList' => [ 'value' => true, 'suggestChangeOnLogin' => true ],
];
foreach ( $wmgPrivilegedGroups as $group ) {
	// On non-SUL wikis this is the effective password policy. On SUL wikis, it will be overridden
	// in the PasswordPoliciesForUser hook, but still needed for Special:PasswordPolicies
	if ( $group === 'user' ) {
		$group = 'default'; // For e.g. private and fishbowl wikis; covers 'user' in password policies
	}
	$wgPasswordPolicy['policies'][$group] = array_merge( $wgPasswordPolicy['policies'][$group] ?? [],
		$wmgPrivilegedPolicy );
}

$wgPasswordPolicy['policies']['default']['MinimalPasswordLength'] = [
	'value' => 8,
	'suggestChangeOnLogin' => false,
];

$wgPasswordPolicy['policies']['default']['PasswordNotInCommonList'] = [
	'value' => true,
	'suggestChangeOnLogin' => true,
];

// Enforce password policy when users login on other wikis; also for sensitive global groups
// FIXME does this just duplicate the global policy checks down in the main $wmgUseCentralAuth block?
if ( $wmgUseCentralAuth ) {
	$wgHooks['PasswordPoliciesForUser'][] = static function ( User $user, array &$effectivePolicy ) use ( $wmgPrivilegedPolicy ) {
		$privilegedGroups = wmfGetPrivilegedGroups( $user );
		if ( $privilegedGroups ) {
			$effectivePolicy = UserPasswordPolicy::maxOfPolicies( $effectivePolicy, $wmgPrivilegedPolicy );

			if ( in_array( 'staff', $privilegedGroups, true ) ) {
				$effectivePolicy['MinimumPasswordLengthToLogin'] = [
					'value' => 10,
				];
			}
		}
	};
}

if ( $wmgDisableAccountCreation ) {
	$wgGroupPermissions['*']['createaccount'] = false;
	$wgGroupPermissions['*']['autocreateaccount'] = true;
}

# ######################################################################
# Server security settings
# ######################################################################
// Disable firejail in kubernetes, where it would not work.
// Most shellouts, and any shellout depending on untrusted input in particular,
// should use shellbox.
$wgShellRestrictionMethod = ClusterConfig::getInstance()->isK8s() ? false : 'firejail';
$wgShellboxShell = '/bin/bash';

$wgUseImageMagick = true;
// Please note: neither command exists in the container, and this should
// never be called in production - still better to set it to something that could work on k8s.
$wgImageMagickConvertCommand = ClusterConfig::getInstance()->isK8s() ? '/usr/bin/convert' : '/usr/local/bin/mediawiki-firejail-convert';

$wgSharpenParameter = '0x0.8'; # for IM>6.5, T26857

if ( $wmgUsePagedTiffHandler ) {
	wfLoadExtension( 'PagedTiffHandler' );
	$wgTiffUseTiffinfo = true;
	$wgTiffMaxMetaSize = 1048576;
	// Force use of shellbox on mw on k8s.
	// We're not sending commons user traffic here so this can live for as long as needed
	// before we make upload-by-url asynchronous
	if ( !$wmgUsePagedTiffHandlerShellbox && ClusterConfig::getInstance()->isK8s() ) {
		$wmgUsePagedTiffHandlerShellbox = true;
	}
	if ( $wmgUsePagedTiffHandlerShellbox && $wmgLocalServices['shellbox-media'] ) {
		// Route pagedtiffhandler to the Shellbox named "shellbox-media".
		$wgShellboxUrls['pagedtiffhandler'] = $wmgLocalServices['shellbox-media'];
		// $wgShellboxSecretKey set in PrivateSettings.php
	}
}

// Use shellbox on k8s for handling djvu images.
if ( ClusterConfig::getInstance()->isK8s() && $wmgLocalServices['shellbox-media'] ) {
	$wgDjvuUseBoxedCommand = true;
	$wgShellboxUrls['djvu'] = $wmgLocalServices['shellbox-media'];
}

if ( $wmgUseTimedMediaHandlerShellbox && $wmgLocalServices['shellbox-video'] ) {
	$wgShellboxUrls['timedmediahandler'] = $wmgLocalServices['shellbox-video'];
}

// Disable $wgMaxImageArea checks, Thumbor uses a timeout instead
// of a size limit (T291014#7367570)
$wgMaxImageArea = false;
$wgMaxAnimatedGifArea = 10e7; // 100MP

$wgFileExtensions = array_merge( $wgFileExtensions, $wmgFileExtensions );
$wgProhibitedFileExtensions = array_merge( $wgProhibitedFileExtensions, $wmgProhibitedFileExtensions );

if ( isset( $wmgUploadStashMaxAge ) ) {
	$wgUploadStashMaxAge = $wmgUploadStashMaxAge;
}

if ( $wmgPrivateWikiUploads ) {
	# mav forced me to --midom
	$wgFileExtensions[] = 'ppt';

	# mav forced me as well!!! -- Tim
	$wgFileExtensions[] = 'doc';
	# adding since removed elsewhere now -- 2007-08-21 -- brion
	$wgFileExtensions[] = 'xls';
	# delphine made me do it!!!!! --brion
	$wgFileExtensions[] = 'eps';
	$wgFileExtensions[] = 'zip';

	# OpenOffice, hell if we're going to allow doc we may as well have these too -- Tim
	$wgFileExtensions[] = 'odf';
	$wgFileExtensions[] = 'odp';
	$wgFileExtensions[] = 'ods';
	$wgFileExtensions[] = 'odt';
	$wgFileExtensions[] = 'odg'; // OpenOffice Graphics
	$wgFileExtensions[] = 'ott'; // Templates

	# Temporary for office work :P
	$wgFileExtensions[] = 'wmv';
	$wgFileExtensions[] = 'dv';
	$wgFileExtensions[] = 'avi';
	$wgFileExtensions[] = 'mov';
	$wgFileExtensions[] = 'aif'; // "
	$wgFileExtensions[] = 'aiff'; // "

	# Because I hate having to find print drivers -- tomasz
	$wgFileExtensions[] = 'ppd';

	# InDesign & PhotoShop, Illustrator wanted for Chapters logo work
	$wgFileExtensions[] = 'indd';
	$wgFileExtensions[] = 'inx';
	$wgFileExtensions[] = 'psd';
	$wgFileExtensions[] = 'ai';

	# Pete made me --Roan
	$wgFileExtensions[] = 'omniplan';

	# Dia Diagrams files --fred.
	$wgFileExtensions[] = 'dia';

	# Font files (so we can upload Montserrat to donatewiki) --Roan
	$wgFileExtensions[] = 'woff';
	$wgFileExtensions[] = 'woff2';

	// To allow OpenOffice doc formats we need to not exclude zip files
	$wgMimeTypeExclusions = array_diff(
		$wgMimeTypeExclusions,
		[ 'application/zip' ]
	);
}

# ######################################################################
# SVG renderer settings
# ######################################################################

$wgSVGConverter = 'rsvg-secure';

$wgSVGConverterPath = '/usr/bin';

$wgSVGMaxSize = 4096; // 1024's a bit low?

# Hack for rsvg broken by security patch
$wgSVGConverters['rsvg-broken'] = '$path/rsvg-convert -w $width -h $height -o $output < $input';
# This converter will only work when rsvg has a suitable security patch
$wgSVGConverters['rsvg-secure'] = '$path/rsvg-convert --no-external-files -w $width -h $height -o $output $input';

# ######################################################################
# DJVU renderer settings
# ######################################################################

$wgDjvuDump = '/usr/bin/djvudump';

$wgDjvuRenderer = '/usr/bin/ddjvu';

$wgDjvuTxt = '/usr/bin/djvutxt';

# ######################################################################
# Reverse proxy Configuration
# ######################################################################

$wgUseCdn = true;
if ( $wmgRealm === 'production' ) {
	require __DIR__ . '/reverse-proxy.php';
} elseif ( $wmgRealm === 'labs' ) {
	$wgStatsdMetricPrefix = 'BetaMediaWiki';
	require __DIR__ . '/reverse-proxy-labs.php';
}

// CORS (cross-domain AJAX, T22814)
// This lists the domains that are accepted as *origins* of CORS requests
// DO NOT add domains here that aren't WMF wikis unless you really know what you're doing
if ( $wmgUseCORS ) {
	$wgCrossSiteAJAXdomains = [
		'*.wikipedia.org',
		'*.wikinews.org',
		'*.wiktionary.org',
		'*.wikibooks.org',
		'*.wikiversity.org',
		'*.wikisource.org',
		'wikisource.org',
		'*.wikiquote.org',
		'www.wikidata.org',
		'm.wikidata.org',
		'test.wikidata.org',
		'test.m.wikidata.org',
		'*.wikivoyage.org',
		'www.mediawiki.org',
		'm.mediawiki.org',
		'www.wikifunctions.org',
		'm.wikifunctions.org',
		'advisory.wikimedia.org',
		'advisory.m.wikimedia.org',
		'affcom.wikimedia.org',
		'api.wikimedia.org',
		'auditcom.wikimedia.org',
		'boardgovcom.wikimedia.org',
		'board.wikimedia.org',
		'chair.wikimedia.org',
		'checkuser.wikimedia.org',
		'checkuser.m.wikimedia.org',
		'collab.wikimedia.org',
		'commons.wikimedia.org',
		'commons.m.wikimedia.org',
		'test-commons.wikimedia.org',
		'test-commons.m.wikimedia.org',
		'donate.wikimedia.org',
		'exec.wikimedia.org',
		'foundation.wikimedia.org',
		'foundation.m.wikimedia.org',
		'grants.wikimedia.org',
		'incubator.wikimedia.org',
		'incubator.m.wikimedia.org',
		'internal.wikimedia.org',
		'login.wikimedia.org',
		'meta.wikimedia.org',
		'meta.m.wikimedia.org',
		'movementroles.wikimedia.org',
		'office.wikimedia.org',
		'office.m.wikimedia.org',
		'outreach.wikimedia.org',
		'outreach.m.wikimedia.org',
		'quality.wikimedia.org',
		'quality.m.wikimedia.org',
		'searchcom.wikimedia.org',
		'spcom.wikimedia.org',
		'species.wikimedia.org',
		'species.m.wikimedia.org',
		// auth.wikimedia.org omitted to keep its functionality minimal
		'steward.wikimedia.org',
		'steward.m.wikimedia.org',
		'strategy.wikimedia.org',
		'strategy.m.wikimedia.org',
		'usability.wikimedia.org',
		'usability.m.wikimedia.org',
		'vrt-wiki.wikimedia.org',
		'vrt-wiki.m.wikimedia.org',
		'wikimania.wikimedia.org',
		'wikimania.m.wikimedia.org',
		'wikimania????.wikimedia.org',
		'wikimania????.m.wikimedia.org',
		'wikimaniateam.wikimedia.org',
		'wikimaniateam.m.wikimedia.org',
		'wikitech.wikimedia.org',
		'am.wikimedia.org',
		'am.m.wikimedia.org',
		'ar.wikimedia.org',
		'ar.m.wikimedia.org',
		'bd.wikimedia.org',
		'bd.m.wikimedia.org',
		'be.wikimedia.org',
		'be.m.wikimedia.org',
		'br.wikimedia.org',
		'br.m.wikimedia.org',
		'ca.wikimedia.org',
		'ca.m.wikimedia.org',
		'cn.wikimedia.org',
		'cn.m.wikimedia.org',
		'co.wikimedia.org',
		'co.m.wikimedia.org',
		'dk.wikimedia.org',
		'dk.m.wikimedia.org',
		'ec.wikimedia.org',
		'ec.m.wikimedia.org',
		'ee.wikimedia.org',
		'ee.m.wikimedia.org',
		'et.wikimedia.org',
		'et.m.wikimedia.org',
		'fi.wikimedia.org',
		'fi.m.wikimedia.org',
		'ge.wikimedia.org',
		'ge.m.wikimedia.org',
		'hi.wikimedia.org',
		'hi.m.wikimedia.org',
		'id.wikimedia.org',
		'id.m.wikimedia.org',
		'il.wikimedia.org',
		'il.m.wikimedia.org',
		'mai.wikimedia.org',
		'mai.m.wikimedia.org',
		'mk.wikimedia.org',
		'mk.m.wikimedia.org',
		'mx.wikimedia.org',
		'mx.m.wikimedia.org',
		'nl.wikimedia.org',
		'nl.m.wikimedia.org',
		'noboard-chapters.wikimedia.org',
		'no.wikimedia.org',
		'no.m.wikimedia.org',
		'nyc.wikimedia.org',
		'nyc.m.wikimedia.org',
		'nz.wikimedia.org',
		'nz.m.wikimedia.org',
		'punjabi.wikimedia.org',
		'punjabi.m.wikimedia.org',
		'pa-us.wikimedia.org',
		'pa-us.m.wikimedia.org',
		'pl.wikimedia.org',
		'pl.m.wikimedia.org',
		'pt.wikimedia.org',
		'pt.m.wikimedia.org',
		'romd.wikimedia.org',
		'romd.m.wikimedia.org',
		'rs.wikimedia.org',
		'rs.m.wikimedia.org',
		'ru.wikimedia.org',
		'ru.m.wikimedia.org',
		'se.wikimedia.org',
		'se.m.wikimedia.org',
		'tr.wikimedia.org',
		'tr.m.wikimedia.org',
		'ua.wikimedia.org',
		'ua.m.wikimedia.org',
		'wb.wikimedia.org',
		'wb.m.wikimedia.org',
	];
}

wfLoadSkins( [ 'Vector', 'MonoBook', 'Modern', 'CologneBlue', 'Timeless' ] );

// Grants and rights
// Note these have to be visible on all wikis, not just the ones the
// extension is enabled on, for proper display in OAuth pages and such.

// Adding Flaggedrevs rights so that they are available for globalgroups/staff rights - JRA 2013-07-22
$wgAvailableRights[] = 'autoreview';
$wgAvailableRights[] = 'autoreviewrestore';
$wgAvailableRights[] = 'movestable';
$wgAvailableRights[] = 'review';
$wgAvailableRights[] = 'stablesettings';
$wgAvailableRights[] = 'unreviewedpages';
$wgAvailableRights[] = 'validate';
$wgGrantPermissions['editprotected']['movestable'] = true;

// So that protection rights can be assigned to global groups
$wgAvailableRights[] = 'templateeditor';
$wgAvailableRights[] = 'editeditorprotected';
$wgAvailableRights[] = 'editextendedsemiprotected';
$wgAvailableRights[] = 'extendedconfirmed';
$wgAvailableRights[] = 'editautoreviewprotected';
$wgAvailableRights[] = 'editautopatrolprotected';
$wgAvailableRights[] = 'edittrustedprotected';
$wgGrantPermissions['editprotected']['templateeditor'] = true;
$wgGrantPermissions['editprotected']['editeditorprotected'] = true;
$wgGrantPermissions['editprotected']['editextendedsemiprotected'] = true;
$wgGrantPermissions['editprotected']['extendedconfirmed'] = true;
$wgGrantPermissions['editprotected']['editautoreviewprotected'] = true;
$wgGrantPermissions['editprotected']['editautopatrolprotected'] = true;
$wgGrantPermissions['editprotected']['edittrustedprotected'] = true;
$wgGrantPermissions['editprotected']['edit-legal'] = true;

// Adding Flow's rights so that they are available for global groups/staff rights
$wgAvailableRights[] = 'flow-create-board';
$wgAvailableRights[] = 'flow-edit-post';
$wgAvailableRights[] = 'flow-suppress';
$wgAvailableRights[] = 'flow-hide';
$wgAvailableRights[] = 'flow-delete';

// Adding GrowthExperiments's rights
$wgAvailableRights[] = 'setmentor';
$wgAvailableRights[] = 'managementors';
$wgAvailableRights[] = 'enrollasmentor';

// Rights needed to interact with wikibase
$wgGrantPermissions['createeditmovepage']['property-create'] = true;
$wgGrantPermissions['editpage']['item-term'] = true;
$wgGrantPermissions['editpage']['item-merge'] = true;
$wgGrantPermissions['editpage']['property-term'] = true;
$wgGrantPermissions['editpage']['item-redirect'] = true;

// Extension:Newsletter, so that they are available for global groups --MA 2017.09.09
$wgAvailableRights[] = 'newsletter-create';
$wgAvailableRights[] = 'newsletter-delete';
$wgAvailableRights[] = 'newsletter-manage';
$wgAvailableRights[] = 'newsletter-restore';

// Enable a "viewdeletedfile" userright for [[m:Global deleted image review]] (T16801)
$wgAvailableRights[] = 'viewdeletedfile';
$wgHooks['TitleQuickPermissions'][] = static function ( Title $title, User $user, $action, &$errors, $doExpensiveQueries, $short ) {
	return ( !in_array( $action, [ 'deletedhistory', 'deletedtext' ] ) || !$title->inNamespaces( NS_FILE, NS_FILE_TALK ) || !$user->isAllowed( 'viewdeletedfile' ) );
};

// Assign "editautopatrolprotected" to sysops and bots, if autopatroller restriction level is enabled
if ( in_array( 'editautopatrolprotected', $wgRestrictionLevels ) ) {
	$wgGroupPermissions['sysop']['editautopatrolprotected'] = true;
	$wgGroupPermissions['bot']['editautopatrolprotected'] = true;
}

// Extension:StopForumSpam rights, so they can be assigned to global groups (T334856)
$wgAvailableRights[] = 'sfsblock-bypass';

# ######################################################################
# Logo settings
# ######################################################################

// Pieced together so that wikis can inherit their logo from their project/default
if ( isset( $wmgSiteLogo1x ) ) {
	$wgLogos = [
		'1x' => $wmgSiteLogo1x ?? null,
		'1.5x' => $wmgSiteLogo1_5x ?? null,
		'2x' => $wmgSiteLogo2x ?? null,
		'icon' => $wmgSiteLogoIcon ?? null,
		'wordmark' => $wmgSiteLogoWordmark ?? null,
		'tagline' => $wmgSiteLogoTagline ?? null,
		'variants' => $wmgSiteLogoVariants ?? null,
	];
}

// Max width modifications
$wgVectorMaxWidthOptions['exclude']['namespaces'] = $wmgVectorMaxWidthOptionsNamespaces;

# ######################################################################
# Installer
# ######################################################################

$wgInstallerInitialPages = [ [
	'titlemsg' => 'mainpage',
	'text' => <<<EOT
<div dir="ltr" lang="en" class="mw-content-ltr">
==This subdomain is reserved for the creation of a [[wikimedia:Our projects|{{InstallerOption: SiteGroupInEnglish}}]] in '''[[w:en:{{InstallerOption: LanguageNameInEnglish}}|{{InstallerOption: LanguageNameInEnglish}}]]''' language==

* Please '''do not start editing''' this new site. This site has a test project on the [[incubator:|Wikimedia Incubator]] (or on the [[betawikiversity:|Beta Wikiversity]] or on the [[oldwikisource:|Old Wikisource]]) and it will be imported to here.
* If you would like to help translating the interface to this language, please do not translate here, but go to [[translatewiki:|translatewiki.net]], a special wiki for translating the interface. That way everyone can use it on every wiki using the [[mw:|same software]].
* For information about how to edit and for other general help, see [[m:Help:Contents|Help on Wikimedia's Meta-Wiki]] or [[mw:Help:Contents|Help on MediaWiki.org]].

== Sister projects ==
<span class="plainlinks">
[//www.wikipedia.org Wikipedia] |
[//www.wiktionary.org Wiktionary] |
[//www.wikibooks.org Wikibooks] |
[//www.wikinews.org Wikinews] |
[//www.wikiquote.org Wikiquote] |
[//www.wikisource.org Wikisource] |
[//www.wikiversity.org Wikiversity] |
[//www.wikivoyage.org Wikivoyage] |
[//species.wikimedia.org Wikispecies] |
[//www.wikidata.org Wikidata] |
[//www.wikifunctions.org Wikifunctions] |
[//commons.wikimedia.org Commons]
</span>

See Wikimedia's [[m:|Meta-Wiki]] for the coordination of these projects.
</div>
EOT
] ];

# ######################################################################
# Extensions
# ######################################################################
// Yeah the next 3000 or so lines is for extension configuration except for the
// bits that aren't.

if ( $wmgUseTimeline ) {
	wfLoadExtension( 'timeline' );
	$wgTimelineFileBackend = 'local-multiwrite';
	// Filenames in Shellbox container with no .ttf suffix
	$wgTimelineFonts = [
		'freesans' => '/srv/app/fonts/FreeSans',
		// FreeSansWMF has been generated from FreeSans and FreeSerif by using this script with fontforge:
		// Open("FreeSans.ttf");
		// MergeFonts("FreeSerif.ttf");
		// SetFontNames("FreeSans-WMF", "FreeSans WMF", "FreeSans WMF Regular", "Regular", "");
		// Generate("FreeSansWMF.ttf", "", 4 );
		'freesanswmf' => '/srv/app/fonts/FreeSansWMF',
		'unifont' => '/srv/app/fonts/unifont'
		// TODO: add noto fonts
	];
	$wgTimelineFonts['default'] = $wgTimelineFonts[$wmgTimelineDefaultFont];
	// Route easytimeline to the Shellbox named "shellbox-timeline".
	$wgShellboxUrls['easytimeline'] = $wmgLocalServices['shellbox-timeline'];
}

// TODO: This should be handled by LocalServices, not here.
$wgCopyUploadProxy = ( $wmgRealm !== 'labs' ) ? $wmgLocalServices['urldownloader'] : false;
$wgUploadThumbnailRenderHttpCustomHost = $wmgHostnames['upload'];
$wgUploadThumbnailRenderHttpCustomDomain = $wmgLocalServices['upload'];
if ( $wmgUseLocalHTTPProxy || ClusterConfig::getInstance()->isK8s() ) {
	$wgLocalHTTPProxy = $wmgLocalServices['mwapi'] ?? false;
}

if ( $wmgUseWikiHiero ) {
	wfLoadExtension( 'wikihiero' );
}

wfLoadExtension( 'SiteMatrix' );

// Config for sitematrix
$wgSiteMatrixFile = ( $wmgRealm === 'labs' ) ? "$IP/../langlist-labs" : "$IP/../langlist";

$wgSiteMatrixSites = [
	'wiki' => [
		'name' => 'Wikipedia',
		'host' => 'www.wikipedia.org',
		'prefix' => 'w',
	],
	'wiktionary' => [
			'name' => 'Wiktionary',
			'host' => 'www.wiktionary.org',
			'prefix' => 'wikt',
	],
	'wikibooks' => [
			'name' => 'Wikibooks',
			'host' => 'www.wikibooks.org',
			'prefix' => 'b',
	],
	'wikinews' => [
			'name' => 'Wikinews',
			'host' => 'www.wikinews.org',
			'prefix' => 'n',
	],
	'wikiquote' => [
			'name' => 'Wikiquote',
			'host' => 'www.wikiquote.org',
			'prefix' => 'q',
	],
	'wikisource' => [
			'name' => 'Wikisource',
			'host' => 'www.wikisource.org',
			'prefix' => 's',
	],
	'wikiversity' => [
			'name' => 'Wikiversity',
			'host' => 'www.wikiversity.org',
			'prefix' => 'v',
	],
	'wikivoyage' => [
			'name' => 'Wikivoyage',
			'host' => 'www.wikivoyage.org',
			'prefix' => 'voy',
	],
];

$wgSiteMatrixClosedSites = WmfConfig::readDbListFile( 'closed' );
$wgSiteMatrixPrivateSites = WmfConfig::readDbListFile( 'private' );
$wgSiteMatrixFishbowlSites = WmfConfig::readDbListFile( 'fishbowl' );
$wgSiteMatrixNonGlobalSites = [];

// list of codex icons to use for interwiki (based on SiteMatrix) search results widget
// https://phabricator.wikimedia.org/T315269
$wgInterwikiLogoOverride = [
	'w' => 'logoWikipedia',
	'wikt' => 'logoWiktionary',
	'b' => 'logoWikibooks',
	'n' => 'logoWikinews',
	'q' => 'logoWikiquote',
	's' => 'logoWikisource',
	'v' => 'logoWikiversity',
	'voy' => 'logoWikivoyage',
];

if ( $wmgUseCharInsert ) {
	wfLoadExtension( 'CharInsert' );
}

if ( $wmgUseParserFunctions ) {
	wfLoadExtension( 'ParserFunctions' );
}
$wgExpensiveParserFunctionLimit = 500;

if ( $wmgUseCite ) {
	wfLoadExtension( 'Cite' );
	// T362771: Mentioned gadget conflicts with parts in both extensions so the value is needed in both
	if ( $wgPopupsConflictingNavPopupsGadgetName ) {
		$wgCiteReferencePreviewsConflictingNavPopupsGadgetName = $wgPopupsConflictingNavPopupsGadgetName;
	}
}

if ( $wmgUseCiteThisPage ) {
	wfLoadExtension( 'CiteThisPage' );
}

if ( $wmgUseInputBox ) {
	wfLoadExtension( 'InputBox' );
}

if ( $wmgUseImageMap ) {
	wfLoadExtension( 'ImageMap' );
}

if ( $wmgUseSyntaxHighlight ) {
	wfLoadExtension( 'SyntaxHighlight_GeSHi' );
	if ( $wmgUseSyntaxHighlightShellbox && $wmgLocalServices['shellbox-syntaxhighlight'] ) {
		// Route syntaxhighlight to the Shellbox named "shellbox-syntaxhighlight".
		$wgShellboxUrls['syntaxhighlight'] = $wmgLocalServices['shellbox-syntaxhighlight'];
		// $wgShellboxSecretKey set in PrivateSettings.php
		$wgPygmentizePath = '/srv/app/pygmentize';
	}
}

if ( $wmgUsePoem ) {
	wfLoadExtension( 'Poem' );
}

// Per-wiki config for Flagged Revisions
if ( $wmgUseFlaggedRevs ) {
	// Include load of extension, and its config.
	include __DIR__ . '/flaggedrevs.php';
}

if ( $wmgUseCategoryTree ) {
	wfLoadExtension( 'CategoryTree' );
}

if ( $wmgUseProofreadPage ) {
	wfLoadExtension( 'ProofreadPage' );
	if ( $wgDBname === 'dewikisource' ) {
		$wgGroupPermissions['*']['pagequality'] = true; # 27516
	}

	if ( $wmgProofreadPageShowHeaders ) {
		$wgDefaultUserOptions['proofreadpage-showheaders'] = 1;
	}

	// Wikisource requires special handling (disable fixed width on page and index namespaces)
	// See T300563#7665461 and T74525
	$wgVectorMaxWidthOptions['exclude']['namespaces'][] = $wgProofreadPageNamespaceIds['page'];
	// as well as T352162
	$wgVectorMaxWidthOptions['exclude']['namespaces'][] = $wgProofreadPageNamespaceIds['index'];

}

if ( $wmgUseLabeledSectionTransclusion ) {
	wfLoadExtension( 'LabeledSectionTransclusion' );
}

if ( $wmgUseSpamBlacklist ) {
	wfLoadExtension( 'SpamBlacklist' );
	$wgBlacklistSettings = [
		'email' => [
			'files' => [
				'https://meta.wikimedia.org/w/index.php?title=Email_blacklist&action=raw&sb_ver=1'
			],
		],
		'spam' => [
			'files' => [
				'https://meta.wikimedia.org/w/index.php?title=Spam_blacklist&action=raw&sb_ver=1'
			],
		],
	];
	$wgLogSpamBlacklistHits = true;
}

wfLoadExtension( 'TitleBlacklist' );
$wgTitleBlacklistBlockAutoAccountCreation = false;

if ( $wmgUseGlobalTitleBlacklist ) {
	$wgTitleBlacklistSources = [
		'meta' => [
			'type' => 'url',
			'src'  => "https://meta.wikimedia.org/w/index.php?title=Title_blacklist&action=raw&tb_ver=1",
		],
	];
}

if ( $wmgUseQuiz ) {
	wfLoadExtension( 'Quiz' );
}

if ( $wmgUseGadgets ) {
	wfLoadExtension( 'Gadgets' );
}

if ( $wmgUseTimedMediaHandler ) {
	wfLoadExtension( 'TimedMediaHandler' );

	$wgTimedTextForeignNamespaces = [ 'commonswiki' => 102 ];
	if ( $wgDBname === 'commonswiki' ) {
		$wgTimedTextNS = 102;
	}

	// enable transcoding on all wikis that allow uploads
	$wgEnableTranscode = $wgEnableUploads;

	// The default $wgEnabledTranscodeSet will include WebM VP9 flat file
	// transcodes from 240p to 2160p. Leave them enabled site-wide.
	//
	// Also enable a single WebM VP8 flat file for backwards compatibility.
	$wgEnabledTranscodeSet['360p.webm'] = true;

	// [T363966] pre-iOS 17.4 compat tracks: QuickTime files with old but
	// supported free codecs (MJPEG + MP3 and/or MPEG-4 Visual + MP3).
	//
	// As of October 2024 we don't yet have legal confirmation for MPEG-4
	// part 2 Visual codec but if this arrives in future we can flip them
	// on for higher quality.
	$wgEnabledTranscodeSet['144p.mjpeg.mov'] = true;
	$wgEnabledTranscodeSet['360p.mpeg4.mov'] = false;

	// [T373546] HLS VP9 and JPEG experiments are being disabled in 2024
	// as iOS 17.4 prefers the WebM tracks and iOS support of the JPEG
	// is flakier than the non-HLS version now enabled above.

	// Temporarilly disable 1440p and 2160p transcodes:
	// they're very slow to generate and we need to tune
	$wgEnabledTranscodeSet['1440p.vp9.webm'] = false;
	$wgEnabledTranscodeSet['2160p.vp9.webm'] = false;

	// tmh1/2 have 12 cores and need lots of shared memory
	// for ffmpeg, which mmaps large input files
	$wgTranscodeBackgroundMemoryLimit = 4 * 1024 * 1024; // 4GB

	// This allows using 2x the threads for VP9 encoding, but will
	// fail if running a too-old ffmpeg version.
	$wgFFmpegVP9RowMT = true;

	// VP9 encoding benefits from more threads; up to 4 for HD or
	// 8 when using row-based multithreading.
	//
	// Note compression of second pass is "spiky", alternating between
	// single-threaded and multithreaded portions, so you can somewhat
	// overcommit process threads per CPU thread.
	$wgFFmpegThreads = 8;

	// HD transcodes of full-length films/docs/conference vids can
	// take several hours, and sometimes over 12. Bump up from default
	// 8 hour limit to 16 to avoid wasting the time we've already spent
	// when breaking these off.
	// Then double that for VP9, which is more intense on the CPU.
	$wgTranscodeBackgroundTimeLimit = 32 * 3600;

	// ffmpeg tends to use about 175% CPU when dual-threaded, so hits
	// say an 8-hour ulimit in 4-6 hours. This tends to cut
	// off very large files at very high resolution just before
	// they finish, wasting a lot of time.
	// Pad it back out so we don't waste that CPU time with a fail!
	$wgTranscodeBackgroundTimeLimit *= $wgFFmpegThreads;

	// Minimum size for an embed video player
	$wgMinimumVideoPlayerSize = $wmgMinimumVideoPlayerSize;

	// use new ffmpeg build w/ VP9 & Opus support
	$wgFFmpegLocation = '/usr/bin/ffmpeg';

	// For MIDI to Ogg/MP3 conversion:
	// add Debian paths for fluidsynth and the sound font to use
	$wgTmhFluidsynthLocation = '/usr/bin/fluidsynth';
	$wgTmhSoundfontLocation = '/usr/share/sounds/sf2/FluidR3_GM.sf2';
}

if ( $wmgUseUploadsLink ) {
	wfLoadExtension( 'UploadsLink' );
}

// Set default search experience to MediaSearch (T297484)
if ( $wmgUseMediaSearch ) {
	$wgDefaultUserOptions['search-special-page'] = 'MediaSearch';
}

if ( $wmgUseUrlShortener ) {
	wfLoadExtension( 'UrlShortener' );
	$wgUrlShortenerTemplate = '/$1';
	$wgUrlShortenerServer = 'https://w.wiki';
	$wgVirtualDomainsMapping['virtual-urlshortener'] = [ 'cluster' => 'extension1', 'db' => 'wikishared' ];
	$wgUrlShortenerAllowedDomains = [
		'(.*\.)?wikipedia\.org',
		'(.*\.)?wiktionary\.org',
		'(.*\.)?wikibooks\.org',
		'(.*\.)?wikinews\.org',
		'(.*\.)?wikiquote\.org',
		'(.*\.)?wikisource\.org',
		'(.*\.)?wikiversity\.org',
		'(.*\.)?wikivoyage\.org',
		'(.*\.)?wikimedia\.org',
		'(.*\.)?wikidata\.org',
		'(.*\.)?wikifunctions\.org',
		'(.*\.)?mediawiki\.org',
	];
	$wgUrlShortenerApprovedDomains = [
		'*.wikipedia.org',
		'*.wiktionary.org',
		'*.wikibooks.org',
		'*.wikinews.org',
		'*.wikiquote.org',
		'*.wikisource.org',
		'*.wikiversity.org',
		'*.wikivoyage.org',
		'*.wikimedia.org',
		'*.wikidata.org',
		'*.wikifunctions.org',
		'*.mediawiki.org',
	];
	$wgGroupPermissions['sysop']['urlshortener-manage-url'] = false;
	$wgGroupPermissions['sysop']['urlshortener-view-log'] = false;

	// Never ever change this config
	// Changing it would change target of all short urls
	$wgUrlShortenerIdSet = '23456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz$';

	$wgUrlShortenerEnableQrCode = true; // T348487
}

if ( $wmgPFEnableStringFunctions ) {
	$wgPFEnableStringFunctions = true;
}

if ( $wgDBname === 'mediawikiwiki' ) {
	wfLoadExtension( 'ExtensionDistributor' );
	$wgExtDistAPIConfig = [
		'class' => GerritExtDistProvider::class,
		'apiUrl' => 'https://gerrit.wikimedia.org/r/projects/mediawiki%2F$TYPE%2F$EXT/branches',
		'tarballUrl' => 'https://extdist.wmflabs.org/dist/$TYPE/$EXT-$REF-$SHA.tar.gz',
		'tarballName' => '$EXT-$REF-$SHA.tar.gz',
		'repoListUrl' => 'https://gerrit.wikimedia.org/r/projects/?b=master&p=mediawiki/$TYPE/',
		'sourceUrl' => 'https://gerrit.wikimedia.org/r/mediawiki/$TYPE/$EXT.git',
		// Use the url-downloader proxy to reach out to gerrit. T340483
		'proxy' => $wgCopyUploadProxy,
	];

	// Current stable release
	$wgExtDistDefaultSnapshot = 'REL1_44';

	// Current development snapshot
	//$wgExtDistCandidateSnapshot = 'REL1_44';

	// Available snapshots
	$wgExtDistSnapshotRefs = [
		'master',
		'REL1_44',
		'REL1_43',
		'REL1_39',
	];

	// Use Graphite for popular list
	$wgExtDistGraphiteRenderApi = 'https://graphite.wikimedia.org/render';
}

// CentralAuth needed so that user CentralIds match
if ( $wmgUseCentralAuth && $wmgUseGlobalBlocking ) {
	wfLoadExtension( 'GlobalBlocking' );
	$wgVirtualDomainsMapping['virtual-globalblocking'] = [ 'db' => 'centralauth' ];
	$wgApplyGlobalBlocks = $wmgApplyGlobalBlocks;
	$wgGlobalBlockingBlockXFF = true; // Apply blocks to IPs in XFF (T25343)
	$wgGlobalBlockingCentralWiki = 'metawiki';
}

wfLoadExtension( 'TrustedXFF' );

if ( $wmgUseContactPage ) {
	wfLoadExtension( 'ContactPage' );
	$wgContactConfig = [];
	$wgContactConfig['default'] = [
		'RecipientUser' => null,
		'SenderEmail' => null,
		'SenderName' => 'Contact Form on ' . $wgSitename,
		'RequireDetails' => false,
		'IncludeIP' => false,
		'MustBeLoggedIn' => false,
		'RLModules' => [],
		'RLStyleModules' => [],
		'AdditionalFields' => [
			'Text' => [
				'label-message' => 'emailmessage',
				'type' => 'textarea',
				'rows' => 20,
				'required' => true,
			],
		],
	];

	$wgContactConfig['default'] = array_merge( $wgContactConfig['default'], $wmgContactPageConf );

	if ( $wgDBname === 'metawiki' ) {
		include __DIR__ . '/MetaContactPages.php';
	}
	if ( $wgDBname === 'enwiki' ) {
		include __DIR__ . '/EnWikiContactPages.php';
	}
	if ( $wgDBname === 'zhwiki' ) {
		include __DIR__ . '/ZhWikiContactPages.php';
	}
}

if ( $wmgUseSecurePoll ) {
	wfLoadExtension( 'SecurePoll' );

	$wgHooks['SecurePoll_JumpUrl'][] = static function ( $page, &$url ) use ( $site, $lang ) {
		$url = wfAppendQuery( $url, [ 'site' => $site, 'lang' => $lang ] );
	};
	$wgSecurePollCreateWikiGroups = [
		'securepollglobal' => 'securepoll-dblist-securepollglobal'
	];
	// T303135 / T287780
	$wgSecurePollExcludedWikis = [ 'labswiki', 'loginwiki' ];
	// T173393 - This is number of days after the election ends, not
	// number of days after the vote was cast. Lower to 60 days so that
	// overall time retained is not > 90 days.
	$wgSecurePollKeepPrivateInfoDays = 60;

	if ( strpos( ClusterConfig::getInstance()->getHostname(), 'mwmaint' ) === 0 ) {
		$wgSecurePollShowErrorDetail = true;
	}
}

// PoolCounter
if ( $wmgUsePoolCounter ) {
	include __DIR__ . '/PoolCounterSettings.php';
}

if ( $wmgUseSecureLinkFixer ) {
	wfLoadExtension( 'SecureLinkFixer' );
}

if ( $wmgUseScore ) {
	wfLoadExtension( 'Score' );
	$wgScoreSafeMode = true;

	if ( $wmgUseScoreShellbox && $wmgLocalServices['shellbox'] ) {
		// Route score to the Shellbox named "shellbox".
		$wgShellboxUrls['score'] = $wmgLocalServices['shellbox'];
		// $wgShellboxSecretKey set in PrivateSettings.php
		$wgScoreImageMagickConvert = '/usr/bin/convert';
	} else {
		# Emergency mode in which Score is disabled
		$wgScoreLilyPond = '/dev/null';
		$wgScoreDisableExec = true;
		$wgScoreLilyPondFakeVersion = '2.22.0';
	}

}

$wgHiddenPrefs[] = 'realname';

# e-mailing password based on e-mail address (T36386)
$wgPasswordResetRoutes['email'] = true;

if ( $wgDBname === 'nostalgiawiki' ) {
	# Link back to current version from the archive funhouse
	// phpcs:ignore MediaWiki.ControlStructures.AssignmentInControlStructures.AssignmentInControlStructures
	// phpcs:ignore Generic.CodeAnalysis.AssignmentInCondition.Found
	if ( ( isset( $_REQUEST['title'] ) && ( $title = $_REQUEST['title'] ) )
		// phpcs:ignore Generic.CodeAnalysis.AssignmentInCondition.Found
		|| ( isset( $_SERVER['PATH_INFO'] ) && ( $title = substr( $_SERVER['PATH_INFO'], 1 ) ) ) ) {
		if ( preg_match( '/^(.*)\\/Talk$/', $title, $matches ) ) {
			$title = 'Talk:' . $matches[1];
		}
		$wgSiteNotice = "[//en.wikipedia.org/wiki/" .
			htmlspecialchars( urlencode( $title ) ) .
		' See the current version of this page on Wikipedia]';
	} else {
		$wgSiteNotice = "[//en.wikipedia.org/ See current Wikipedia]";
	}

	// Nostalgia skin
	wfLoadSkin( 'Nostalgia' );
}

$wgFooterIcons['copyright']['copyright'] = [
	'url' => 'https://www.wikimedia.org/',
	'src' => '/static/images/footer/wikimedia.svg',
	'sources' => [
		[
			'media' => '(min-width: 500px)',
			'srcset' => $wmgWikimediaIcon,
			'width' => 84,
			'height' => 29,
		]
	],
	'width' => 25,
	'height' => 25,
	'alt' => 'Wikimedia Foundation',
	'lang' => 'en',
];

# :SEARCH:

# All wikis are special and get Cirrus :)
# Must come *AFTER* PoolCounterSettings.php
wfLoadExtension( 'Elastica' );
wfLoadExtension( 'CirrusSearch' );
include __DIR__ . '/CirrusSearch-common.php';

$wgInvalidateCacheOnLocalSettingsChange = false;

$wgAllowRawHtmlCopyrightMessages = false; // T375789

$wgEnableUserEmail = true;
$wgNoFollowLinks = true; // In case the MediaWiki default changed, T44594

# XFF log for incident response
$wgExtensionFunctions[] = static function () {
	if (
		isset( $_SERVER['REQUEST_METHOD'] )
		&& $_SERVER['REQUEST_METHOD'] === 'POST'
		// T129982
		&& $_SERVER['HTTP_HOST'] !== 'mw-jobrunner.discovery.wmnet:4448'
	) {
		$uri = ( ( $_SERVER['HTTPS'] ?? null ) ? 'https://' : 'http://' ) .
			$_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

		$logger = LoggerFactory::getInstance( 'xff' );
		$logger->info( "{date}\t{uri}\t{xff}, {remoteaddr}\t{wpSave}",
			[
				'date' => gmdate( 'r' ),
				'uri' => $uri,
				'xff' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '',
				'remoteaddr' => $_SERVER['REMOTE_ADDR'],
				'wpSave' => ( $_REQUEST['wpSave'] ?? null ) ? 'save' : '',
			]
		);
	}
};

// T26313, turn off minordefault on enwiki
if ( $wgDBname === 'enwiki' ) {
	$wgHiddenPrefs[] = 'minordefault';
}

$wgHooks['SkinAddFooterLinks'][] = static function ( $sk, $key, &$footerlinks )
	use ( $wmgUseFooterContactLink, $wmgUseFooterCodeOfConductLink, $wmgUseFooterTechCodeOfConductLink )
{
	if ( $key !== 'places' ) {
		return;
	}

	if ( $wmgUseFooterContactLink ) {
		$footerlinks['contact'] = Html::element(
			'a',
			[ 'href' => $sk->msg( 'contact-url' )->escaped() ],
			$sk->msg( 'contact' )->text()
		);
	}

	if ( $wmgUseFooterCodeOfConductLink ) {
		$urlKey = 'wm-codeofconduct-url';
		$msgKey = 'wm-codeofconduct';
	} elseif ( $wmgUseFooterTechCodeOfConductLink ) {
		$urlKey = 'wm-techcodeofconduct-url';
		$msgKey = 'wm-techcodeofconduct';
	}
	$footerlinks['wm-codeofconduct'] = Html::element(
		'a',
		[ 'href' => $sk->msg( $urlKey )->escaped() ],
		$sk->msg( $msgKey )->text()
	);
};

// T35186: turn off incomplete feature action=imagerotate
$wgAPIModules['imagerotate'] = 'ApiDisabled';

if ( $wmgUseDynamicPageList ) {
	wfLoadExtension( 'intersection' );
	$wgDLPMaxCacheTime = 604800;
}

$wgGroupPermissions['bureaucrat']['renameuser'] = $wmgAllowLocalRenameuser;

if ( $wmgUseSpecialNuke ) {
	wfLoadExtension( 'Nuke' );
	$wgNukeMaxAge = 90 * 86400; // T380846
}

if ( $wmgUseTorBlock ) {
	wfLoadExtension( 'TorBlock' );
	$wgTorIPs = [ '91.198.174.232', '208.80.152.2', '208.80.152.134' ];
	$wgTorAutoConfirmAge = 90 * 86400;
	$wgTorAutoConfirmCount = 100;
	$wgTorDisableAdminBlocks = false;
	$wgTorTagChanges = false;
	$wgGroupPermissions['user']['torunblocked'] = false;
	$wgTorBlockProxy = $wgCopyUploadProxy;
}

if ( $wmgUseRSSExtension ) {
	wfLoadExtension( 'RSS' );
	$wgRSSProxy = $wgCopyUploadProxy;
	$wgRSSUrlWhitelist = $wmgRSSUrlWhitelist;
}

if ( $wgMaxCredits === 0 ) {
	$wgActions['credits'] = false;
}

# Process group overrides

$wgGroupPermissions['steward'   ]['userrights'] = true;
$wgGroupPermissions['bureaucrat']['userrights'] = false;

$wgGroupPermissions['sysop']['bigdelete'] = false; // quick hack

foreach ( $groupOverrides2 as $group => $permissions ) {
	if ( !array_key_exists( $group, $wgGroupPermissions ) ) {
		$wgGroupPermissions[$group] = [];
	}
	$wgGroupPermissions[$group] = $permissions + $wgGroupPermissions[$group];
}

foreach ( $groupOverrides as $group => $permissions ) {
	if ( !array_key_exists( $group, $wgGroupPermissions ) ) {
		$wgGroupPermissions[$group] = [];
	}
	$wgGroupPermissions[$group] = $permissions + $wgGroupPermissions[$group];
}

if ( $wgDBname === 'loginwiki' ) {
	$wgGroupPermissions['*'] = [
		'read' => true,
		'autocreateaccount' => true,
		'editmyoptions' => true, // T158871
	];
	$wgGroupPermissions['user'] = [
		'read' => true,
		'writeapi' => true,
	];
	$wgGroupPermissions['autoconfirmed'] = [
		'read' => true,
		'writeapi' => true,
	];

	unset( $wgGroupPermissions['import'] );
	unset( $wgGroupPermissions['transwiki'] );

	$wgGroupPermissions['sysop']['editinterface'] = false;
}

$wgAutopromote = [
	'autoconfirmed' => [ '&',
		[ APCOND_EDITCOUNT, $wgAutoConfirmCount ],
		[ APCOND_AGE, $wgAutoConfirmAge ],
	],
];

if ( is_array( $wmgAutopromoteExtraGroups ) ) {
	$wgAutopromote += $wmgAutopromoteExtraGroups;
}

$wgAutopromoteOnce = [
	'onEdit' => $wmgAutopromoteOnceonEdit,
];

if ( is_array( $wmgExtraImplicitGroups ) ) {
	$wgImplicitGroups = array_merge( $wgImplicitGroups, $wmgExtraImplicitGroups );
}

if ( $wmgRealm == 'labs' ) {
	$wgHTTPTimeout = 10;
}
if ( $wgRequestTimeLimit ) {
	// Set the maximum HTTP client timeout equal to the current request timeout (T245170)
	$wgHTTPMaxTimeout = $wgHTTPMaxConnectTimeout = $wgRequestTimeLimit;
}

if ( isset( $_REQUEST['captchabypass'] ) && $_REQUEST['captchabypass'] == $wmgCaptchaPassword ) {
	$wmgEnableCaptcha = false;
}

if ( $wmgEnableCaptcha ) {
	wfLoadExtension( 'ConfirmEdit' );
	wfLoadExtension( 'ConfirmEdit/FancyCaptcha' );
	$wgGroupPermissions['autoconfirmed']['skipcaptcha'] = true;
	$wgCaptchaFileBackend = 'global-multiwrite';
	$wgCaptchaSecret = $wmgCaptchaSecret;
	$wgCaptchaDirectoryLevels = 3;
	$wgCaptchaStorageClass = CaptchaCacheStore::class;
	$wgCaptchaClass = FancyCaptcha::class;
	$wgCaptchaWhitelist =
		'#^(https?:)?//([.a-z0-9-]+\\.)?((wikimedia|wikipedia|wiktionary|wikiquote|wikibooks|wikisource|wikispecies|mediawiki|wikinews|wikiversity|wikivoyage|wikidata|wikifunctions|wmflabs)\.org'
		. '|dnsstuff\.com|completewhois\.com|wikimedia\.de)([?/\#]|$)#i';

	// 'XRumer' spambot
	// adds non-real links
	// http://meta.wikimedia.org/wiki/User:Cometstyles/XRumer
	// http://meta.wikimedia.org/wiki/User:Jorunn/tracks
	// (added 2008-05-08 -- brion)
	$wgCaptchaRegexes[] = '/<a +href/i';

	// For emergencies
	if ( $wmgEmergencyCaptcha ) {
		$wgCaptchaTriggers['edit'] = true;
		$wgCaptchaTriggers['create'] = true;
	}

	# akosiaris 20180306. contact pages in metawiki are being abused by bots
	if ( $wgDBname === 'metawiki' ) {
		$wgCaptchaTriggers['contactpage'] = true;
	}
}

if ( extension_loaded( 'wikidiff2' ) ) {
	$wgDiff = false;
}

if ( $wmgRealm === 'labs' ) {
	$wgInterwikiCache = require __DIR__ . '/interwiki-labs.php';
} else {
	$wgInterwikiCache = require __DIR__ . '/interwiki.php';
}

// Username spoofing / mixed-script / similarity check detection
wfLoadExtension( 'AntiSpoof' );

// For transwiki import
ini_set( 'user_agent', 'Wikimedia internal server fetcher (noc@wikimedia.org' );
$wgHTTPImportTimeout = 50; // T155209

// CentralAuth
if ( $wmgUseCentralAuth ) {
	wfLoadExtension( 'CentralAuth' );

	$wgVirtualDomainsMapping['virtual-centralauth'] = [ 'db' => 'centralauth' ];

	// Enable cross-origin session cookies (T252236).
	$wgCookieSameSite = 'None';

	$wgCentralAuthDryRun = false;
	$wgCentralAuthCookies = true;

	foreach ( $wmgLocalServices['irc'] as $address ) {
		$wgCentralAuthRC[] = [
			'formatter' => IRCColourfulCARCFeedFormatter::class,
			'uri' => "udp://$address:$wmgRC2UDPPort/#central\t",
		];
	}

	$wgCentralAuthLoginWiki = 'loginwiki';
	$wgCentralAuthAutoLoginWikis = $wmgCentralAuthAutoLoginWikis;
	$wgCentralAuthCookieDomain = $wmgCentralAuthCookieDomain;
	$wgCentralAuthSharedDomainCallback = static fn ( $dbname ) => "https://{$wmgHostnames['auth']}/$dbname";
	$wgCentralAuthLoginIcon = $wmgCentralAuthLoginIcon;
	$wgCentralAuthRestrictSharedDomain = true;

	// T363695: When using the shared auth.wikimedia.org domain, ignore normal cookie domain settings,
	// and use cookie names that are the same for every wiki.
	if ( $wmgSharedDomainPathPrefix ) {
		$wgCentralAuthCookieDomain = '';
		$wgCookiePrefix = 'auth';
		$wgSessionName = 'authSession';
		$wgWebAuthnNewCredsDisabled = false;

		// T395185: Enable sending client hints data on shared domain for all requests.
		// This domain has restricted actions only to the authentication workflow,
		// so we won't be collecting client hints data for actions that are not
		// supposed to be collected.
		$wgCheckUserClientHintsEnabled = true;
		$wgCheckUserAlwaysSetClientHintHeaders = true;
	}

	/**
	 * Helper method for fixing problems caused by changing cookie domain settings
	 * ($wgCookieDomain, $wgCentralAuthCookieDomain).
	 * Clears affected cookies on $oldCookieDomain every time an affected cookie is set.
	 * @param string $wiki ID of affected wiki
	 * @param string $type 'local' (after $wgCookieDomain change) or 'central' (after $wgCentralAuthCookieDomain change)
	 * @param string $oldCookieDomain The old value of $wgCookieDomain or $wgCentralAuthCookieDomain (or the empty
	 *   string if it was unset)
	 */
	function wmfClearOldSessionCookies( string $wiki, string $type, $oldCookieDomain ): void {
		// phpcs:ignore MediaWiki.Usage.DeprecatedGlobalVariables.Deprecated$wgHooks
		global $wgDBname, $wmgSharedDomainPathPrefix, $wgSessionName, $wgCookiePrefix, $wgHooks;
		// $wgCookiePrefix may not be set yet when this runs. It defaults to $wgDBname.
		$cookiePrefix = ( $wgCookiePrefix !== false && $wgCookiePrefix !== null ) ? $wgCookiePrefix : $wgDBname;
		$centralAuthCookies = [ 'centralauth_Session', 'centralauth_User', 'centralauth_Token', 'centralauth_LoggedOut' ];
		$localCookies = [ $wgSessionName, $cookiePrefix . 'UserID', $cookiePrefix . 'UserName' ];

		if ( $wgDBname !== $wiki
			// not needed on the shared domain
			|| $wmgSharedDomainPathPrefix
		) {
			return;
		}
		$cookies = ( $type === 'central' ) ? $centralAuthCookies : $localCookies;

		/**
		 * @param string &$name Cookie name passed to WebResponse::setcookie()
		 * @param string &$value Cookie value passed to WebResponse::setcookie()
		 * @param int|null &$expire Cookie expiration, as for PHP's setcookie()
		 * @param array &$options Options passed to WebResponse::setcookie()
		 * @return bool|void True or no return value to continue, or false to prevent setting of the cookie
		 * @return void
		 */
		$wgHooks['WebResponseSetCookie'][] = static function ( &$name, &$value, &$expire, &$options ) use ( $cookies, $oldCookieDomain ) {
			global $wmgCentralAuthWebResponseSetCookieRecurse;

			$realName = ( $options['prefix'] ?? '' ) . $name;
			if ( $oldCookieDomain
				&& class_exists( MobileContext::class )
				&& MobileContext::singleton()->usingMobileDomain()
			) {
				$oldCookieDomain = wmfMobileUrlCallback( $oldCookieDomain );
			}

			if ( in_array( $realName, $cookies, true )
				&& ( $options['domain'] ?? '' ) !== $oldCookieDomain
				&& !$wmgCentralAuthWebResponseSetCookieRecurse
			) {
				$webResponse = RequestContext::getMain()->getRequest()->response();
				$clearOptions = $options;
				$clearOptions['domain'] = $oldCookieDomain;

				$wmgCentralAuthWebResponseSetCookieRecurse = true;
				$webResponse->clearCookie( $name, $clearOptions );
				$wmgCentralAuthWebResponseSetCookieRecurse = false;
			}
		};
	}

	// Temporary fix for T389433, remove after 2026-04
	wmfClearOldSessionCookies( 'labswiki', 'local', 'wikitech.wikimedia.org' );

	/**
	 * This function is used for both the CentralAuthWikiList and
	 * GlobalUserPageWikis hooks.
	 *
	 * @param array &$list
	 * @return bool
	 */
	function wmfCentralAuthWikiList( &$list ) {
		global $wgLocalDatabases, $wgSiteMatrixPrivateSites,
			$wgSiteMatrixFishbowlSites, $wgSiteMatrixClosedSites,
			$wgSiteMatrixNonGlobalSites;

		$list = array_diff(
			$wgLocalDatabases,
			$wgSiteMatrixPrivateSites,
			$wgSiteMatrixFishbowlSites,
			$wgSiteMatrixClosedSites,
			$wgSiteMatrixNonGlobalSites
		);
		return false;
	}

	$wgHooks['CentralAuthWikiList'][] = 'wmfCentralAuthWikiList';

	// Attempt to attach unattached accounts by password on login
	$wgCentralAuthAutoMigrate = true;

	// Try to create global accounts if one doesn't exist and it's safe
	$wgCentralAuthAutoMigrateNonGlobalAccounts = true;

	// Enables Special:GlobalRenameRequest
	$wgCentralAuthEnableGlobalRenameRequest = true;

	// Only allow users with global accounts to login
	$wgCentralAuthStrict = true;

	// Create some local accounts as soon as the global registration happens
	$wgCentralAuthAutoCreateWikis = [ 'loginwiki', 'metawiki' ];

	// Link global block blockers to user pages on Meta
	$wgCentralAuthGlobalBlockInterwikiPrefix = 'meta';

	// See T104371 and [[m:Requests_for_comment/Password_policy_for_users_with_certain_advanced_permissions]]
	foreach ( $wmgPrivilegedGlobalGroups as $group ) {
		$wgCentralAuthGlobalPasswordPolicies[$group] = $wmgPrivilegedPolicy;
		if ( $group === 'staff' ) {
			// Require 10 byte password for staff.
			$wgCentralAuthGlobalPasswordPolicies[$group]['MinimumPasswordLengthToLogin'] = 10;
		}
	}

	// Check global rename log on meta for new accounts
	$wgCentralAuthOldNameAntiSpoofWiki = 'metawiki';
	$wgVirtualDomainsMapping['virtual-botpasswords'] = [ 'db' => 'metawiki' ];

	// Allows automatic account vanishing (for qualifying users)
	$wgCentralAuthAutomaticVanishPerformer = 'AccountVanishRequests';
	$wgCentralAuthRejectVanishUserNotification = 'AccountVanishRequests';
	$wgCentralAuthAutomaticVanishWiki = 'metawiki';

	// Configuration for guidance given to blocked users when requesting vanishing
	$wgCentralAuthBlockAppealWikidataIds = [ "Q13360396", "Q175291" ];
	$wgCentralAuthWikidataApiUrl = "https://www.wikidata.org/w/api.php";
	$wgCentralAuthFallbackAppealUrl = "https://en.wikipedia.org/wiki/Wikipedia:Appealing_a_block";
	$wgCentralAuthFallbackAppealTitle = "Wikipedia:Appealing a block";
}

// Config for GlobalCssJs
// Only enable on CentralAuth wikis
if ( $wmgUseGlobalCssJs && $wmgUseCentralAuth ) {
	wfLoadExtension( 'GlobalCssJs' );

	// Disable site-wide global css/js
	$wgUseGlobalSiteCssJs = false;

	// Setup metawiki as central wiki
	$wgResourceLoaderSources['metawiki'] = [
		'apiScript' => '//meta.wikimedia.org/w/api.php',
		'loadScript' => '//meta.wikimedia.org/w/load.php',
	];

	$wgGlobalCssJsConfig = [
		'wiki' => 'metawiki', // database name
		'source' => 'metawiki', // ResourceLoader source name
	];
}

if ( $wmgUseGlobalUserPage && $wmgUseCentralAuth ) {
	wfLoadExtension( 'GlobalUserPage' );
	$wgGlobalUserPageAPIUrl = 'https://meta.wikimedia.org/w/api.php';
	$wgGlobalUserPageDBname = 'metawiki';
	$wgHooks['GlobalUserPageWikis'][] = 'wmfCentralAuthWikiList';
}

if ( $wmgLocalAuthLoginOnly && $wmgUseCentralAuth ) {
	// T57420: prevent creation of local password records for SUL users
	if ( isset( $wgAuthManagerAutoConfig['primaryauth'][LocalPasswordPrimaryAuthenticationProvider::class] ) ) {
		$wgAuthManagerAutoConfig['primaryauth'][LocalPasswordPrimaryAuthenticationProvider::class]['args'][0]['loginOnly'] = true;
	}
}

if ( $wmgUseApiFeatureUsage ) {
	wfLoadExtension( 'ApiFeatureUsage' );
	$wgApiFeatureUsageQueryEngineConf = [
		'class' => ApiFeatureUsageQueryEngineElastica::class,
		'serverList' => $wmgLocalServices['search-chi-dnsdisc'],
	];
}

// taking it live 2006-12-15 brion
wfLoadExtension( 'DismissableSiteNotice' );
$wgDismissableSiteNoticeForAnons = true; // T59732
$wgMajorSiteNoticeID = '2';

/**
 * Get a list of "privileged" groups and global groups the user is part of.
 * Typically this means groups with powers comparable to admins and above
 * (block, delete, edit i18n messages etc).
 * On SUL wikis, this will take into account group memberships on any wiki,
 * not just the current one.
 *
 * @param UserIdentity $user
 * @return string[] Any elevated/privileged groups the user is a member of
 *
 * @note This method is also used in WikimediaEvents.
 */
function wmfGetPrivilegedGroups( $user ) {
	global $wmgUseCentralAuth, $wmgPrivilegedGroups, $wmgPrivilegedGlobalGroups;

	if ( $wmgUseCentralAuth && CentralAuthUser::getInstanceByName( $user->getName() )->exists() ) {
		$centralUser = CentralAuthUser::getInstanceByName( $user->getName() );
		try {
			$groups = array_intersect(
				array_merge( $wmgPrivilegedGroups, $wmgPrivilegedGlobalGroups ),
				array_merge( $centralUser->getGlobalGroups(), $centralUser->getLocalGroups() )
			);
		} catch ( Exception $e ) {
			// Don't block login if we can't query attached (T119736)
			MWExceptionHandler::logException( $e );
			$groups = array_merge(
				MediaWikiServices::getInstance()
					->getUserGroupManager()
					->getUserGroups( $user ),
				$centralUser->getGlobalGroups()
			);
		}
	} else {
		// use effective groups, as we set 'user' as privileged for private/fishbowl wikis
		$groups = array_intersect(
			$wmgPrivilegedGroups,
			MediaWikiServices::getInstance()
				->getUserGroupManager()
				->getUserEffectiveGroups( $user )
			);
	}
	return $groups;
}

$wgHooks['GetSecurityLogContext'][] = static function ( array $info, array &$context ) {
	/** @var WebRequest $request */
	$request = $info['request'];
	/** @var ?UserIdentity $user */
	$user = $info['user'] ?? null;

	$context += [
		'geocookie' => $request->getCookie( 'GeoIP', '' ),
	];
	if ( $user ) {
		$privilegedGroups = wmfGetPrivilegedGroups( $user );
		$context += [
			'user_is_privileged' => (bool)$privilegedGroups,
			'user_privileged_groups' => implode( ', ', $privilegedGroups ),
		];
	}
};

// log suspicious or sensitive login attempts
$wgHooks['AuthManagerLoginAuthenticateAudit'][] = static function ( $response, $user, $username ) {
	$guessed = false;
	if ( !$user && $username ) {
		$user = MediaWikiServices::getInstance()
			->getUserFactory()
			->newFromName( $username );
		$guessed = true;
	}
	if ( !$user || !in_array( $response->status,
		[ AuthenticationResponse::PASS, AuthenticationResponse::FAIL ], true )
	) {
		return;
	}

	global $wgRequest;
	$context = $wgRequest->getSecurityLogContext( $user );
	$privileged = $context['user_is_privileged'];
	$successful = $response->status === AuthenticationResponse::PASS;

	$channel = $successful ? 'goodpass' : 'badpass';
	if ( $privileged ) {
		$channel .= '-priv';
	}
	$logger = LoggerFactory::getInstance( $channel );
	$verb = $successful ? 'succeeded' : 'failed';

	$logger->info( "Login $verb for {priv} {user} from {clientIp} - {ua} - {geocookie}: {messagestr}", [
		'successful' => $successful,
		// Backwards compatibility
		'name' => $context['user'],
		// Backwards compatibility
		'clientip' => $context['clientIp'],
		'priv' => ( $privileged ? 'elevated' : 'normal' ),
		'guessed' => $guessed,
		'msgname' => $response->message ? $response->message->getKey() : '-',
		'messagestr' => $response->message ? $response->message->inLanguage( 'en' )->text() : '',
	] + $context );
};

// log sysop password changes
$wgHooks['ChangeAuthenticationDataAudit'][] = static function ( $req, $status ) {
	global $wgRequest;
	$user = MediaWikiServices::getInstance()
		->getUserIdentityLookup()
		->getUserIdentityByName( $req->username );
	$status = Status::wrap( $status );
	if ( $req instanceof PasswordAuthenticationRequest ) {
		$context = $wgRequest->getSecurityLogContext( $user );
		$privileged = $context['user_is_privileged'];
		if ( $privileged ) {
			$logger = LoggerFactory::getInstance( 'badpass' );
			$logger->info( 'Password change in prefs for {priv} {user}: {status} - {clientIp} - {ua} - {geocookie}', [
				// Backwards compatibility
				'name' => $context['user'],
				// Backwards compatibility
				'clientip' => $context['clientIp'],
				'priv' => ( $privileged ? 'elevated' : 'normal' ),
				'status' => $status->isGood() ? 'ok' : $status->getWikiText( null, null, 'en' ),
			] + $context );
		}
	}
};

// Passed to ulimit

$wgMaxShellFileSize = 512 * 1024; // Kilobytes
$wgMaxShellMemory = 1024 * 1024;  // Kilobytes
$wgMaxShellTime = 50;  // seconds

// Use a cgroup for shell execution.
// This will cause shell execution to fail if the cgroup is not installed.
// If some misc server doesn't have the cgroup installed, you can create it
// with: mkdir -p -m777 /sys/fs/cgroup/memory/mediawiki/job
$wgShellCgroup = '/sys/fs/cgroup/memory/mediawiki/job';

// Passed to Shellbox
// Videoscalers get 1d, others get 1min.
// This must be the same as the timeouts in shellbox deployment charts.
if ( ClusterConfig::getInstance()->isAsync() && ( strpos( $_SERVER['HTTP_HOST'] ?? '', 'videoscaler.' ) === 0 || strpos( $_SERVER['HTTP_HOST'] ?? '', 'shellbox-video.' ) === 0 ) ) {
	$wgMaxShellWallClockTime = 24 * 60 * 60;  // seconds
} else {
	$wgMaxShellWallClockTime = 60;  // seconds
}

switch ( $wmgRealm ) {
	case 'production':
		$wgImageMagickTempDir = '/tmp/magick-tmp';
		break;
	case 'labs':
		$wgImageMagickTempDir = '/tmp/a/magick-tmp';
		break;
}

// Banner notice system
if ( $wmgUseCentralNotice ) {
	wfLoadExtension( 'CentralNotice' );

	// for DNS prefetching
	$wgCentralHost = "//{$wmgHostnames['meta']}";

	// for banner loading
	if ( $wmgRealm === 'production' && $wgDBname === 'testwiki' ) {
		$wgCentralSelectedBannerDispatcher = "//test.wikipedia.org/w/index.php?title=Special:BannerLoader";

		// No caching for banners on testwiki, so we can develop them there a bit faster - NeilK 2012-01-16
		// Never set this to zero on a highly trafficked wiki, there are server-melting consequences
		$wgNoticeBannerMaxAge = 0;
	} else {
		$wgCentralSelectedBannerDispatcher = "//{$wmgHostnames['meta']}/w/index.php?title=Special:BannerLoader";
	}
	// Relative URL which is hardcoded to HTTP 204 in Varnish config.
	$wgCentralBannerRecorder = "{$wgServer}/beacon/impression";

	$wgCentralDBname = 'metawiki';
	$wgVirtualDomainsMapping['virtual-centralnotice'] = [
		'db' => 'metawiki'
	];
	$wgNoticeInfrastructure = false;
	$wgCentralNoticeAdminGroup = false;

	// ESI test; see T308799
	$wgCentralNoticeESITestString = '<!--esi <esi:include src="/esitest-fa8a495983347898/content" /> -->';

	if ( $wmgRealm == 'production' && $wgDBname === 'testwiki' ) {
		// test.wikipedia.org has its own central database:
		$wgCentralDBname = 'testwiki';
		$wgNoticeInfrastructure = true;
		$wgVirtualDomainsMapping['virtual-centralnotice'] = [
			'db' => 'testwiki'
		];
	} elseif ( $wgDBname === 'metawiki' ) {
		$wgNoticeInfrastructure = true;
	}
	if ( $wgNoticeInfrastructure ) {

		// List of available wiki groups within CentralNotice
		// (Only referenced from the infrastructure special pages.)
		$wgNoticeProjects = [
			"wikipedia",
			"wiktionary",
			"wikiquote",
			"wikibooks",
			"wikidata",
			"wikinews",
			"wikisource",
			"wikitech",
			"wikiversity",
			"wikivoyage",
			"wikimedia",
			"commons",
			"meta",
			"wikispecies",
			"test",
			"mediawiki",
		];

		$wgCentralNoticeMessageProtectRight = 'banner-protect';
	}

	// Enable the CentralNotice/Translate integration
	$wgNoticeUseTranslateExtension = true;

	// T51905
	$wgNoticeUseLanguageConversion = true;

	// *** Hide Cookies ***
	// A little bit of historical breadcrumbs:
	// In 2012 we expired cookies on 2012-12-26, then everyone had
	// a two week expiration until 2013-01-22 whereupon we introduced
	// a year long expiration. For the 2013 fundraiser starting
	// 2013-12-02 we're now using a 10 month expiration.
	// For the 2014 fundraiser it's 250 days, though we can change it
	// retroactively as the cookie value now has a create date and reason.
	// 'close' duration is used for the banner X button
	// 'donate' duration is used for cookie set on Thank You page
	$wgNoticeCookieDurations = [
		'close' => 604800, // 1 week
		'donate' => 21600000, // 250 days
	];

	// T18821
	// Updates made here also need to be reflected in
	// foundation.wikimedia.org/wiki/Template:HideBanners
	$wgNoticeHideUrls = [
		'//en.wikipedia.org/w/index.php?title=Special:HideBanners',
		'//meta.wikimedia.org/w/index.php?title=Special:HideBanners',
		'//commons.wikimedia.org/w/index.php?title=Special:HideBanners',
		'//species.wikimedia.org/w/index.php?title=Special:HideBanners',
		'//en.wikibooks.org/w/index.php?title=Special:HideBanners',
		'//en.wikiquote.org/w/index.php?title=Special:HideBanners',
		'//en.wikisource.org/w/index.php?title=Special:HideBanners',
		'//en.wikinews.org/w/index.php?title=Special:HideBanners',
		'//en.wikiversity.org/w/index.php?title=Special:HideBanners',
		'//www.mediawiki.org/w/index.php?title=Special:HideBanners',
	];

	// Emit CSP headers on banner previews. This can go away when full CSP
	// support (T135963) is deployed.
	// www.pages04.net and app.goacoustic.com are used by Wikimedia Fundraising to enable 'remind me later' banner functionality, which submits email addresses or phone numbers to our email campaign vendor
	$wgCentralNoticeContentSecurityPolicy = "script-src 'unsafe-eval' blob: 'self' meta.wikimedia.org *.wikimedia.org *.wikipedia.org *.wikinews.org *.wiktionary.org *.wikibooks.org *.wikiversity.org *.wikisource.org wikisource.org *.wikiquote.org *.wikidata.org *.wikifunctions.org *.wikivoyage.org *.mediawiki.org 'unsafe-inline'; default-src 'self' data: blob: upload.wikimedia.org https://commons.wikimedia.org meta.wikimedia.org *.wikimedia.org *.wikipedia.org *.wikinews.org *.wiktionary.org *.wikibooks.org *.wikiversity.org *.wikisource.org wikisource.org *.wikiquote.org *.wikidata.org *.wikifunctions.org *.wikivoyage.org *.mediawiki.org wikimedia.org www.pages04.net; style-src 'self' data: blob: upload.wikimedia.org https://commons.wikimedia.org meta.wikimedia.org *.wikimedia.org *.wikipedia.org *.wikinews.org *.wiktionary.org *.wikibooks.org *.wikiversity.org *.wikisource.org wikisource.org *.wikiquote.org *.wikidata.org *.wikifunctions.org *.wikivoyage.org *.mediawiki.org wikimedia.org 'unsafe-inline'; connect-src 'self' data: blob: upload.wikimedia.org https://commons.wikimedia.org meta.wikimedia.org *.wikimedia.org *.wikipedia.org *.wikinews.org *.wiktionary.org *.wikibooks.org *.wikiversity.org *.wikisource.org wikisource.org *.wikiquote.org *.wikidata.org *.wikifunctions.org *.wikivoyage.org *.mediawiki.org wikimedia.org www.pages04.net app.goacoustic.com;";
}

// Load our site-specific l10n extension
wfLoadExtension( 'WikimediaMessages' );

if ( $wgDBname === 'enwiki' ) {
	// Please don't interfere with our hundreds of wikis ability to manage themselves.
	// Only use this shitty hack for enwiki. Thanks.
	// -- brion 2008-04-10
	$wgHooks['getUserPermissionsErrorsExpensive'][] = static function ( &$title, &$user, $action, &$result ) {
		if ( $action !== 'delete' && $action !== 'move' ) {
			return true;
		}
		$main = Title::newMainPage();
		$mainText = $main->getPrefixedDBkey();
		if ( $mainText === $title->getPrefixedDBkey() ) {
			$result = [ 'cant-delete-main-page' ];
			return false;
		}
	};
}

if ( $wgDBname === 'enwiki' || $wgDBname === 'fawiki' ) {
	// T59569, T105118
	//
	// If it's an anonymous user creating a page in the English and Persian Wikipedia
	// Draft namespace, tell TitleQuickPermissions to abort the normal
	// checkQuickPermissions checks.  This lets anonymous users create a page in this
	// namespace, even though they don't have the general 'createpage' right.
	//
	// It does not affect other checks from getUserPermissionsErrorsInternal
	// (e.g. protection and blocking).
	//
	// Returning true tells it to proceed as normal in other cases.
	$wgHooks['TitleQuickPermissions'][] = static function ( Title $title, User $user, $action, &$errors, $doExpensiveQueries, $short ) {
		return ( $action !== 'create' || $title->getNamespace() !== 118 || !$user->isAnon() );
	};
}

if ( $wmgUseCollection ) {
	// PediaPress / PDF generation
	// Intentionally loaded *before* the Wikisource extension below.
	wfLoadExtension( 'Collection' );
	// Use pediapress server for POD function (T73675)
	$wgCollectionCommandToServeURL = [
		'zip_post' => "{$wmgLocalServices['urldownloader']}|https://pediapress.com/wmfup/",
	];
	$wgCollectionPODPartners = [
		'pediapress' => [
			'name' => 'PediaPress',
			'url' => 'https://pediapress.com/',
			'posturl' => 'https://pediapress.com/api/collections/',
			'infopagetitle' => 'coll-order_info_article',
		],
	];

	// MediaWiki namespace is not a good default
	$wgCommunityCollectionNamespace = NS_PROJECT;

	// Allow collecting Help pages
	$wgCollectionArticleNamespaces[] = NS_HELP;

	$wgCollectionFormats = [
		'rdf2latex' => 'PDF',
		'rdf2text' => 'TXT',
	];

	if ( !$wmgUseElectronPdfService ) {
		$wgCollectionShowRenderNotes[] = 'coll-rendering_finished_note_article_rdf2latex';
	}

	$wgCollectionPortletForLoggedInUsersOnly = $wmgCollectionPortletForLoggedInUsersOnly;
	$wgCollectionArticleNamespaces = $wmgCollectionArticleNamespaces;
	$wgCollectionPortletFormats = $wmgCollectionPortletFormats;
}

if ( $wmgUseElectronPdfService ) {
	wfLoadExtension( 'ElectronPdfService' );
}

if ( $wmgUseEmailAuth ) {
	wfLoadExtension( 'EmailAuth' );
}

wfLoadExtension( 'AdvancedSearch' );

# Various system to allow/prevent flooding
# (including exemptions for scheduled outreach events)
require __DIR__ . '/throttle.php';
require __DIR__ . '/throttle-analyze.php';

if ( $wmgUseNewUserMessage ) {
	wfLoadExtension( 'NewUserMessage' );
	$wgNewUserSuppressRC = true;
	$wgNewUserMinorEdit = $wmgNewUserMinorEdit;
	$wgNewUserMessageOnAutoCreate = $wmgNewUserMessageOnAutoCreate;
}

# AbuseFilter
wfLoadExtension( 'AbuseFilter' );
include __DIR__ . '/abusefilter.php';
if ( $wmgUseGlobalAbuseFilters ) {
	$wgAbuseFilterCentralDB = $wmgAbuseFilterCentralDB;
	$wgAbuseFilterIsCentral = ( $wgDBname === $wgAbuseFilterCentralDB );
}

# PdfHandler
if ( $wmgUsePdfHandler ) {
	wfLoadExtension( 'PdfHandler' );
	// Force use of shellbox on mw on k8s.
	// We're not sending commons user traffic here so this can live for as long as needed
	// before we make upload-by-url asynchronous
	if ( !$wmgUsePdfHandlerShellbox && ClusterConfig::getInstance()->isK8s() ) {
		$wmgUsePdfHandlerShellbox = true;
	}
	if ( $wmgUsePdfHandlerShellbox && $wmgLocalServices['shellbox-media'] ) {
		// Route pdfhandler to the Shellbox named "shellbox-media".
		$wgShellboxUrls['pdfhandler'] = $wmgLocalServices['shellbox-media'];
		// $wgShellboxSecretKey set in PrivateSettings.php
	} else {
		$wgPdfProcessor = '/usr/local/bin/mediawiki-firejail-ghostscript';
		$wgPdfPostProcessor = '/usr/local/bin/mediawiki-firejail-convert';
	}
}

# WikiEditor
wfLoadExtension( 'WikiEditor' );
$wgDefaultUserOptions['usebetatoolbar'] = 1;

if ( $wmgEnableLandingCheck ) {
	wfLoadExtension( 'LandingCheck' );

	$wgPriorityCountries = [
		// === Fundraising Chapers
		'DE', 'CH',

		// === Blacklisted countries
		'BY', 'CD', 'CI', 'CU', 'IQ', 'IR', 'KP', 'LB', 'LY', 'MM', 'SD', 'SO', 'SY', 'YE', 'ZW',
	];
	$wgLandingCheckPriorityURLBase = "//foundation.wikimedia.org/wiki/Special:LandingCheck";
	$wgLandingCheckNormalURLBase = "//donate.wikimedia.org/wiki/Special:LandingCheck";
}

if ( $wmgEnableFundraiserLandingPage ) {
	wfLoadExtension( 'FundraiserLandingPage' );
}

if ( $wmgUseLiquidThreads || $wmgLiquidThreadsFrozen ) {
	require_once __DIR__ . '/liquidthreads.php';

}

if ( $wmgUseGlobalUsage ) {
	wfLoadExtension( 'GlobalUsage' );
	$wgGlobalUsageDatabase = 'commonswiki';
	$wgGlobalUsageSharedRepoWiki = 'commonswiki';
	$wgGlobalUsagePurgeBacklinks = true;
}

wfLoadExtension( 'TemplateStyles' );
// allow protocol-relative URLs per T188760
$wgTemplateStylesAllowedUrls = [
	'audio' => [
		'<^(?:https:)?//upload\\.wikimedia\\.org/wikipedia/commons/>',
	],
	'image' => [
		'<^(?:https:)?//upload\\.wikimedia\\.org/wikipedia/commons/>',
	],
	'svg' => [
		'<^(?:https:)?//upload\\.wikimedia\\.org/wikipedia/commons/[^?#]*\\.svg(?:[?#]|$)>',
	],
	'font' => [],
	'namespace' => [ '<.>' ],
	'css' => [],
];

wfLoadExtension( 'CodeMirror' );

// Must be loaded BEFORE VisualEditor, or things will break
if ( $wmgUseArticleCreationWorkflow ) {
	wfLoadExtension( 'ArticleCreationWorkflow' );
}

$wgDefaultUserOptions['thumbsize'] = $wmgThumbsizeIndex;
$wgThumbnailSteps = [ 20, 40, 60, 120, 250, 330, 500, 960 ];
$wgDefaultUserOptions['showhiddencats'] = $wmgShowHiddenCats;

$wgDefaultUserOptions['watchcreations'] = true;

// Temporary override: WMF is not hardcore enough to enable this.
// See T37785, T38316, T47022 about it.
$wgDefaultUserOptions['watchdefault'] = (int)$wmgWatchlistDefault;
$wgDefaultUserOptions['watchmoves'] = (int)$wmgWatchMoves;
$wgDefaultUserOptions['watchrollback'] = (int)$wmgWatchRollback;

$wgDefaultUserOptions['enotifminoredits'] = $wmgEnotifMinorEditsUserDefault;
$wgDefaultUserOptions['enotifwatchlistpages'] = 0;

$wgDefaultUserOptions['usenewrc'] = (int)$wmgEnhancedRecentChanges;
$wgDefaultUserOptions['extendwatchlist'] = (int)$wmgEnhancedWatchlist;
$wgDefaultUserOptions['forceeditsummary'] = (int)$wmgForceEditSummary;
$wgDefaultUserOptions['wlshowwikibase'] = (int)$wmgShowWikidataInWatchlist;

if ( $wmgUseMassMessage ) {
	wfLoadExtension( 'MassMessage' );
}

if ( $wmgUseSandboxLink ) {
	wfLoadExtension( 'SandboxLink' );
}

if ( $wmgUseUploadWizard ) {
	wfLoadExtension( 'UploadWizard' );
	$wgUploadStashScalerBaseUrl = "//{$wmgHostnames['upload']}/$site/$lang/thumb/temp";
	$wgUploadWizardConfig = [
		# 'debug' => true,
		// Normally we don't include API keys in CommonSettings, but this key
		// isn't private since it's used on the client-side, i.e. anyone can see
		// it in the outgoing AJAX requests to Flickr.
		'flickrApiKey' => 'e9d8174a79c782745289969a45d350e8',
		// Slowwwwwwww
		'campaignExpensiveStatsEnabled' => false,
	];

	$wgUploadWizardConfig['enableChunked'] = 'opt-in';
	$wgUploadWizardConfig['altUploadForm'] = $wmgAltUploadForm; // T35513

	if ( $wgDBname === 'testwiki' ) {
		$wgUploadWizardConfig['feedbackPage'] = 'Prototype_upload_wizard_feedback';
		$wgUploadWizardConfig["missingCategoriesWikiText"] = '<p><span class="errorbox"><b>Hey, no categories?</b></span></p>';
		unset( $wgUploadWizardConfig['fallbackToAltUploadForm'] );
	} elseif ( $wgDBname === 'commonswiki' ) {
		$wgUploadWizardConfig['feedbackPage'] = 'Commons:Upload_Wizard_feedback'; # Set by neilk, 2011-11-01, per erik
		$wgUploadWizardConfig["missingCategoriesWikiText"] = "{{subst:unc}}";
		$wgUploadWizardConfig['flickrBlacklistPage'] = 'User:FlickreviewR/bad-authors';
		$wgUploadWizardConfig['customLicenseTemplate'] = 'Template:License_template_tag';
	}

	// Enable Structured Data captions on upload
	if ( $wmgUseWikibaseMediaInfo ) {
		$wgUploadWizardConfig['wikibase']['enabled'] = true;
		$wgUploadWizardConfig['wikibase']['statements'] = $wmgMediaInfoEnableUploadWizardStatements;
	}
}

if ( $wmgUseMediaSearch ) {
	wfLoadExtension( 'MediaSearch' );
}

if ( $wmgCustomUploadDialog ) {
	$wgUploadDialog = [
		'fields' => [
			'description' => true,
			'date' => true,
			'categories' => true,
		],
		'licensemessages' => [
			// Messages defined in WikimediaMessages: upload-form-label-own-work-message-commons,
			// upload-form-label-not-own-work-message-commons, upload-form-label-not-own-work-local-commons
			'local' => $wgDBname === 'commonswiki' ? 'commons' : 'generic-local',
			'foreign' => $wgDBname === 'commonswiki' ? 'commons' : 'generic-foreign',
		],
		'comment' => 'Uploaded while editing "$PAGENAME" on $HOST',
		'format' => [
			'filepage' => '== {{int:filedesc}} ==
{{Information
|description=$DESCRIPTION
|date=$DATE
|source=$SOURCE
|author=$AUTHOR
}}

== {{int:license-header}} ==
$LICENSE

$CATEGORIES
',
			'description' => '{{$LANGUAGE|1=$TEXT}}',
			'ownwork' => '{{own}}',
			'license' => '{{self|cc-by-sa-4.0}}',
			'uncategorized' => '{{subst:unc}}',
		],
	];
}

if ( $wmgUseBetaFeatures ) {
	wfLoadExtension( 'BetaFeatures' );
}

if ( $wmgUseCommonsMetadata ) {
	wfLoadExtension( 'CommonsMetadata' );
	$wgCommonsMetadataSetTrackingCategories = true;
	$wgCommonsMetadataForceRecalculate = $wmgCommonsMetadataForceRecalculate;
}

if ( $wmgUseMultimediaViewer ) {
	wfLoadExtension( 'MultimediaViewer' );
}

if ( $wmgUsePopups ) {
	wfLoadExtension( 'Popups' );
	/**
	 * Users registered after Popups launch on 16th August 2017
	 * will get page previews and references previews enabled by default.
	 * Any user registered before this date will get the value
	 * defined in $wgPopupsOptInDefaultState (defaults to "0").
	 */
	$wgConditionalUserOptions[ 'popups' ] = [
		[ '1', [ CUDCOND_AFTER, '20170816000000' ] ],
		[ $wgPopupsOptInDefaultState, [ CUDCOND_NAMED ] ],
	];
}

if ( $wmgUseLinter ) {
	wfLoadExtension( 'Linter' );
}

if ( !isset( $wgVirtualRestConfig ) && ( $wmgUseRestbaseVRS || $wmgUseParsoid || $wmgUseCollection ) ) {
	$wgVirtualRestConfig = [
		'modules' => [],
		'global' => [
			'domain' => $wgCanonicalServer,
			'timeout' => 360,
			'forwardCookies' => false,
			'HTTPProxy' => null,
		]
	];
}

if ( $wmgUseRestbaseVRS ) {
	$wgVirtualRestConfig['modules']['restbase'] = [
		'url' => $wmgLocalServices['restbase'],
		'domain' => $wgCanonicalServer,
		'forwardCookies' => false,
		'parsoidCompat' => false
	];
}

if ( $wmgUseParsoid ) {
	$wmgParsoidURL = $wmgLocalServices['parsoid'];

	$wgVirtualRestConfig['modules']['parsoid'] = [
		'url' => $wmgParsoidURL,
		'prefix' => $wgDBname, // The wiki prefix to use; deprecated
		'domain' => $wgCanonicalServer,
		'forwardCookies' => $wmgParsoidForwardCookies,
		'restbaseCompat' => false
	];
}

if ( $wmgUseVisualEditor ) {
	wfLoadExtension( 'VisualEditor' );

	// Tab configuration
	if ( $wmgVisualEditorIsSecondaryEditor ) {
		$wgDefaultUserOptions['visualeditor-editor'] = 'wikitext';
	} else {
		$wgDefaultUserOptions['visualeditor-editor'] = 'visualeditor';
	}
	if ( $wmgVisualEditorEnableWikitext ) {
		$wgDefaultUserOptions['visualeditor-newwikitext'] = true;
	}

	// Show identical preferences on all wikis, but keep the old beta feature config
	// for compatibility with preferences previously set by users (T335056)
	$wgVisualEditorEnableBetaFeature = !$wmgVisualEditorDefault;

	// Feedback configuration
	if ( $wmgVisualEditorConsolidateFeedback ) {
		$wgVisualEditorFeedbackAPIURL = 'https://www.mediawiki.org/w/api.php';
		$wgVisualEditorFeedbackTitle = 'VisualEditor/Feedback';
		$wgVisualEditorSourceFeedbackTitle = '2017 wikitext editor/Feedback';
	}

	// Citoid
	wfLoadExtension( 'Citoid' );

}

if ( $wmgUseTemplateData ) { // T61702 - 2015-07-20
	// TemplateData enabled for all wikis - 2014-09-29
	wfLoadExtension( 'TemplateData' );
	// TemplateData GUI enabled for all wikis - 2014-11-06
	$wgTemplateDataUseGUI = true;

	// TemplateWizard enabled for all TemplateData wikis – T202545
	wfLoadExtension( 'TemplateWizard' );
}

if ( $wmgUseGoogleNewsSitemap ) {
	wfLoadExtension( 'GoogleNewsSitemap' );
	$wgGNSMfallbackCategory = $wmgGNSMfallbackCategory;
	$wgGNSMcommentNamespace = $wmgGNSMcommentNamespace;
}

if ( $wmgUseCLDR ) {
	wfLoadExtension( 'cldr' );
}

if ( $wmgUseIncubator ) {
	wfLoadExtension( 'WikimediaIncubator' );
	// $wmincClosedWikis can be removed once Ib84b70e49e7beff750ccd04cfcca73ef6afce2d2
	// is in production.
	$wmincClosedWikis = $wgWmincClosedWikis = $wgSiteMatrixClosedSites;
}

if ( $wmgUseWikiLove ) {
	wfLoadExtension( 'WikiLove' );
	$wgWikiLoveLogging = true;
	$wgDefaultUserOptions['wikilove-enabled'] = 1;
}

if ( $wmgUseGuidedTour ) {
	wfLoadExtension( 'GuidedTour' );
}

if ( $wmgUseMobileApp ) {
	wfLoadExtension( 'MobileApp' );
}

wfLoadSkin( 'MinervaNeue' );

if ( $wmgUseLiquidThreads || $wmgLiquidThreadsFrozen ) {
	$wmgMinervaNightModeExcludeNamespaces[] = 90;
	$wmgMinervaNightModeExcludeNamespaces[] = 92;
}
$wgMinervaNightModeOptions['exclude']['querystring'] = $wmgMinervaNightModeQueryString;
$wgMinervaNightModeOptions['exclude']['namespaces'] = $wmgMinervaNightModeExcludeNamespaces;
$wgMinervaNightModeOptions['exclude']['pagetitles'] = $wmgMinervaNightModeExcludeTitles;
$wgVectorNightModeOptions = $wgMinervaNightModeOptions;

# Mobile-related configuration
if ( $wmgUseMobileFrontend ) {
	wfLoadExtension( 'MobileFrontend' );

	require_once 'MobileUrlCallback.php';
	$wgMobileUrlCallback = 'wmfMobileUrlCallback';

	$wgMFMobileHeader = 'X-Subdomain';
} else {
	// For sites without MobileFrontend, instead enable Vector's "responsive" state.
	$wgVectorResponsive = true;
}

// Increase font size on Vector 2022 from 14px to 16px
// for users registered after May 6nd 2024
// as well as anonymoues users.
$wgDefaultUserOptions['vector-font-size'] = 1;
$wgConditionalUserOptions['vector-font-size'] = [
	[ 1, [ CUDCOND_AFTER, '20240506000000' ] ],
	[ 0, [ CUDCOND_NAMED ] ]
];

$wgDefaultUserOptions['vector-theme'] = 'day';

// Turn on volunteer recruitment
$wgMFEnableJSConsoleRecruitment = true;

$wgMFUseWikibase = true;

# MUST be after MobileFrontend initialization
if ( $wmgEnableTextExtracts ) {
	wfLoadExtension( 'TextExtracts' );
}

if ( $wmgUseSubPageList3 ) {
	wfLoadExtension( 'SubPageList3' );
}

if ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' ) {
	$wgCookieSecure = true;
	$_SERVER['HTTPS'] = 'on'; // Fake this so MW goes into HTTPS mode
}
$wgVaryOnXFP = true;

$wgCookieExpiration = 30 * 86400;
$wgExtendedLoginCookieExpiration = 365 * 86400;

if ( $wmgUseMath ) {
	wfLoadExtension( 'Math' );
	if ( $wmgMathDefaultUserOptions ) {
		$wgDefaultUserOptions['math'] = $wmgMathDefaultUserOptions;
	}

	// This variable points to non-WMF servers by default.
	// Prevent accidental use.
	// Create LateXML database table before enabling LaTeXML T309686
	$wgMathLaTeXMLUrl = null;
	$wgMathMathMLUrl = $wmgLocalServices['mathoid'];
	// Increase the number of concurrent connections made to RESTBase
	$wgMathConcurrentReqs = 150;

	// Set up $wgMathFullRestbaseURL - similar to VE RESTBase config above
	// HACK: $wgServerName is not available yet at this point, it's set by Setup.php
	// so use a hook
	$wgExtensionFunctions[] = static function () {
		global $wgServerName, $wgMathFullRestbaseURL, $wmgRealm;

		$wgMathFullRestbaseURL = $wmgRealm === 'production'
			? 'https://wikimedia.org/api/rest_'  // T136205
			: "//$wgServerName/api/rest_";
	};
}

if ( $wmgUseBabel ) {
	wfLoadExtension( 'Babel' );

	if ( $wmgUseCentralAuth ) {
		$wgBabelCentralDb = 'metawiki';
	}
}

if ( $wmgUseBounceHandler ) {
	wfLoadExtension( 'BounceHandler' );
	// $wmgVERPsecret is set in PrivateSettings.php
	$wgVERPsecret = $wmgVERPsecret;
	$wgVERPdomainPart = 'wikimedia.org';
	$wgBounceHandlerUnconfirmUsers = true;
	$wgBounceRecordLimit = 5;
	$wgVirtualDomainsMapping['virtual-bouncehandler'] = [ 'cluster' => 'extension1', 'db' => 'wikishared' ];
	$wgBounceHandlerInternalIPs = [
		'208.80.154.76', # mx1001
		'2620:0:861:3:208:80:154:76', # mx1001
		'208.80.153.45', # mx2001
		'2620:0:860:2:208:80:153:45', # mx2001
		'208.80.155.102', # mx-in1001
		'2620:0:861:4:208:80:155:102', # mx-in1001
		'208.80.153.75', # mx-in2001
		'2620:0:860:3:208:80:153:75', # mx-in2001
	];
}

if ( $wmgUseTranslate ) {
	wfLoadExtension( 'Translate' );

	$wgVirtualDomainsMapping['virtual-translate'] = [
		'cluster' => 'extension1',
		'db' => false,
	];

	$wgGroupPermissions['*']['translate'] = true;
	$wgGroupPermissions['translationadmin']['pagetranslation'] = true;
	$wgGroupPermissions['translationadmin']['translate-manage'] = true;
	$wgGroupPermissions['translationadmin']['translate-import'] = true; // T42341
	$wgGroupPermissions['translationadmin']['pagelang'] = true; // T153209
	$wgGroupPermissions['user']['translate-messagereview'] = true;
	$wgGroupPermissions['user']['translate-groupreview'] = true;
	$wgGroupPermissions['sysop']['pagelang'] = true; // T153209
	$wgAddGroups['bureaucrat'][] = 'translationadmin'; // T178793
	$wgRemoveGroups['bureaucrat'][] = 'translationadmin'; // T178793
	$wgGroupsAddToSelf['sysop'][] = 'translationadmin'; // T178793
	$wgGroupsRemoveFromSelf['sysop'][] = 'translationadmin'; // T178793

	$wgTranslateDocumentationLanguageCode = 'qqq';

	$wgPageLanguageUseDB = true; // T153209

	$wgTranslateTranslationServices = [];
	if ( $wmgUseTranslationMemory ) {
		$wgTranslateTranslationDefaultService = 'default';
		$translateServices = [
			'default' => [
				// dnsdisc doesn't exist in the deployment-prep cluster
				'service' => $wmgLocalServices['search-chi-dnsdisc'] ?? $wmgAllServices['eqiad']['search-chi'],
				'writable' => false,
			],
			'eqiad' => [
				'service' => $wmgAllServices['eqiad']['search-chi'],
				'writable' => true,
			],
			'codfw' => [
				// codfw doesn't exist in the deployment-prep cluster
				'service' => $wmgAllServices['codfw']['search-chi'] ?? null,
				'writable' => true,
			],
		];
		foreach ( $translateServices as $service => $conf ) {
			if ( $conf['service'] === null ) {
				continue;
			}
			// see https://www.mediawiki.org/wiki/Help:Extension:Translate/Translation_memories#Configuration
			$wgTranslateTranslationServices[$service] = [
				'type' => 'ttmserver',
				'class' => 'ElasticSearchTTMServer',
				'shards' => 1,
				'replicas' => 1,
				'index' => $wmgTranslateESIndex,
				'cutoff' => 0.65,
				'writable' => $conf['writable'],
				'use_wikimedia_extra' => true,
				'config' => [
					'servers' => array_map( static function ( $hostConfig ) {
						if ( is_array( $hostConfig ) ) {
							// production services
							return $hostConfig;
						}
						// deployment-prep
						return [
							'host' => $hostConfig,
							'port' => 9243,
							'transport' => 'Https',
						];
					}, $conf['service'] ),
				],
			];
		}
		unset( $translateServices );
	} else {
		$wgTranslateTranslationDefaultService = false;
	}

	$wgTranslateWorkflowStates = $wmgTranslateWorkflowStates;
	$wgTranslateRcFilterDefault = $wmgTranslateRcFilterDefault;

	$wgTranslateUsePreSaveTransform = true; // T39304

	$wgEnablePageTranslation = true;

	if ( $wgDBname === 'commonswiki' ) {
		$wgTranslateMessageNamespaces[] = NS_MEDIAWIKI;
		$wgHooks['TranslatePostInitGroups'][] = static function ( &$cc ) {
			$id = 'wiki-translatable';
			$mg = new WikiMessageGroup( $id, 'translatable-messages' );
			$mg->setLabel( 'Interface' );
			$mg->setDescription( 'Messages used in the custom interface of this wiki' );
			$cc[$id] = $mg;
			return true;
		};
	}

	$wgSpecialPages['ManageMessageGroups'] = DisabledSpecialPage::getCallback( 'ManageMessageGroups' );
	$wgTranslateStatsProviders['registrations'] = null;

	$wgTranslateTranslationServices['Apertium'] = [
		'type' => 'cxserver',
		'host' => $wmgLocalServices['cxserver'],
		'timeout' => 3,
	];

	if ( $wmgTranslateUseMinT ) {
		$wgTranslateTranslationServices['MinT'] = [
			'type' => 'mint',
			'host' => $wmgLocalServices['cxserver'],
			'timeout' => 3,
		];
	}
}

if ( $wmgUseTranslationNotifications ) {
	wfLoadExtension( 'TranslationNotifications' );
	$wgTranslationNotificationsContactMethods['talkpage-elsewhere'] = true;
}

if ( $wmgUseFundraisingTranslateWorkflow ) {
	wfLoadExtension( 'FundraisingTranslateWorkflow' );
}

if ( $wmgUseShortUrl ) {
	wfLoadExtension( 'ShortUrl' );
	$wgShortUrlTemplate = "/s/$1";
}

if ( $wmgUseFeaturedFeeds ) {
	wfLoadExtension( 'FeaturedFeeds' );
	require_once __DIR__ . '/FeaturedFeedsWMF.php';
}

$wgDisplayFeedsInSidebar = false;

if ( $wmgEnablePageTriage ) {
	wfLoadExtension( 'PageTriage' );
	$wgAddGroups['bureaucrat'][] = 'copyviobot'; // T206731
	$wgRemoveGroups['bureaucrat'][] = 'copyviobot'; // T206731
}

// This extension is being moved to core (T33951)
if ( $wmgEnableInterwiki && !defined( 'MW_HAS_SPECIAL_INTERWIKI' ) ) {
	wfLoadExtension( 'Interwiki' );
	$wgInterwikiViewOnly = true;
}

# Avoid excessive CPU due to cache misses from rapid invalidations
$wgJobBackoffThrottling['htmlCacheUpdate'] = 50; // pages/sec per runner

# Job types to exclude from the default queue processing. Aka the very long
# one. That will exclude the types from any queries such as nextJobDB.php
# We have to set this for any project cause we usually run PHP script against
# the 'aawiki' database, but might as well run it against another name.

# Timed Media Handler:
$wgJobTypesExcludedFromDefaultQueue[] = 'webVideoTranscode';
$wgJobTypesExcludedFromDefaultQueue[] = 'webVideoTranscodePrioritized';

if ( $wmgUseEducationProgram ) {
	$wgExtraNamespaces[446] = 'Education_Program';
	$wgExtraNamespaces[447] = 'Education_Program_talk';
	$wgNamespacesWithSubpages[447] = true;
}

if ( $wmgEnableGeoData ) {
	wfLoadExtension( 'GeoData' );
	$wgGeoDataBackend = 'elastic';

	$wgMaxCoordinatesPerPage = 2000;
	$wgGeoDataDebug = true;
}

if ( $wmgUseEcho ) {
	// LoginNotify code loaded before Notifications
	wfLoadExtension( 'LoginNotify' );
	$wgNotifyTypeAvailabilityByCategory['login-success']['web'] = false;
	$wgLoginNotifyAttemptsNewIP = 3;
	$wgLoginNotifyUseSeenTable = true;
	$wgLoginNotifyUseCheckUser = false;
	// Less than 90 days per data retention guidelines, minus one bucket for rounding.
	$wgLoginNotifySeenExpiry = 80 * 86400;
	$wgLoginNotifySeenBucketSize = 8 * 86400;
	if ( $wmgUseCentralAuth ) {
		$wgLoginNotifyUseCentralId = true;
		$wgVirtualDomainsMapping['virtual-LoginNotify'] = [
			'cluster' => 'extension1',
			'db' => 'wikishared'
		];
	}

	// This is intentionally loaded *before* the GlobalPreferences extension (below).
	wfLoadExtension( 'Echo' );

	// Common settings, never varied
	$wgEchoPerUserBlacklist = true;
	$wgEchoMaxMentionsInEditSummary = 5;

	$wgEchoEnableEmailBatch = $wmgEchoEnableEmailBatch;
	$wgEchoEmailFooterAddress = $wmgEchoEmailFooterAddress;
	$wgEchoNotificationIcons['site']['url'] = $wmgEchoSiteNotificationIconUrl;

	// Define the cluster database, false to use main database
	$wgEchoCluster = $wmgEchoCluster;

	// CentralAuth is extra check to be absolutely sure we don't enable on non-SUL
	// wikis.
	if ( $wmgUseCentralAuth && $wmgEchoCrossWikiByDefault ) {
		$wgEchoCrossWikiNotifications = true;
		if ( $wmgEchoCrossWikiByDefault ) {
			$wgDefaultUserOptions['echo-cross-wiki-notifications'] = 1;
		}
	}

	// Whether to make mention failure/success notifications available
	$wgEchoMentionStatusNotifications = true;

	// Enable tracking table only on SULed wikis
	if ( $wmgUseCentralAuth ) {
		$wgEchoSharedTrackingDB = 'wikishared';
		// Explicitly set this to 'extension1', because some wikis have $wgEchoCluster set to false
		$wgEchoSharedTrackingCluster = 'extension1';
	}

	// Default user options: subscriptions
	foreach ( $wmgEchoDefaultUserSubscriptions as $where => $notifications ) {
		foreach ( $notifications as $notification => $value ) {
			$option = 'echo-subscriptions-' . $where . '-' . $notification;
			$wgDefaultUserOptions[$option] = $value;
		}
	}

	// Conditional defaults (T353225)
	// NOTE: testwiki has different conditional defaults start
	if ( in_array( $wgDBname, [ 'testwiki', 'loginwiki' ] ) ) {
		$startTimestamp = '20130501000000';
	} else {
		$startTimestamp = '20240208200000';
	}
	$wgConditionalUserOptions['echo-subscriptions-web-reverted'] = [
		[
			false,
			[ CUDCOND_AFTER, $startTimestamp ]
		]
	];
	$wgConditionalUserOptions['echo-subscriptions-web-article-linked'] =
		$wgConditionalUserOptions['echo-subscriptions-email-mention'] =
		$wgConditionalUserOptions['echo-subscriptions-email-article-linked'] = [
			[
				true,
				[ CUDCOND_AFTER, $startTimestamp ]
			]
		];
	unset( $startTimestamp );

	// Push notifications
	$wgEchoEnablePush = $wmgEchoEnablePush ?? false;
	$wgEchoPushServiceBaseUrl = "{$wmgLocalServices['push-notifications']}/v1/message";
	$wgEchoPushMaxSubscriptionsPerUser = 10;

	// Set up the push notifier type if push is enabled.
	// If/when this is promoted to all wikis, this config can be moved directly into extension.json
	// along with the original notifier types ('web' and 'email').
	if ( $wgEchoEnablePush ) {
		$wgEchoNotifiers['push'] = [ PushNotifier::class, 'notifyWithPush' ];
		$wgDefaultNotifyTypeAvailability['push'] = true;
		$wgNotifyTypeAvailabilityByCategory['system']['push'] = false;
		$wgNotifyTypeAvailabilityByCategory['system-noemail']['push'] = false;
	}

	// Limit the 'push-subscription-manager' group to Meta-Wiki only (T261625)
	$wgHooks['MediaWikiServices'][] = static function () {
		global $wgGroupPermissions, $wgDBname;
		if ( $wgDBname !== 'metawiki' ) {
			unset( $wgGroupPermissions['push-subscription-manager'] );
		}
	};
}

// Wikitech specific settings
if ( $wgDBname === 'labswiki' ) {
	$wgEmailConfirmToEdit = true;
}

if ( $wmgUseThanks ) {
	wfLoadExtension( 'Thanks' );

	if ( $wmgUseTranslate ) {
		$wgThanksAllowedLogTypes[] = 'pagetranslation';
	}
}

if ( $wmgUseEntitySchema ) {
	wfLoadExtension( 'EntitySchema' );
}

// Flow configuration

// We always set this variable, as it's required by the create table
// maintenance script. This allows to deploy Flow on non standard wikis.
$wgFlowDefaultWikiDb = $wmgFlowDefaultWikiDb;

if ( $wmgUseFlow && $wmgUseParsoid ) {
	wfLoadExtension( 'Flow' );

	// Flow Parsoid - These are now specified directly as Flow-specific
	// configuration variables, though it currently uses the same Parsoid URL
	// as VisualEditor does.
	$wgFlowParsoidURL = $wmgParsoidURL;
	$wgFlowParsoidPrefix = $wgDBname;
	$wgFlowParsoidTimeout = 50;
	if ( $wmgParsoidForwardCookies ) {
		$wgFlowParsoidForwardCookies = true;
	}

	if ( $wmgUseVisualEditor ) {
		$wgDefaultUserOptions['flow-editor'] = 'visualeditor';
	}

	foreach ( $wmgFlowNamespaces as $namespace ) {
		$wgNamespaceContentModels[$namespace] = 'flow-board'; // CONTENT_MODEL_FLOW_BOARD
	}
	// Requires that Parsoid is available for all wikis using Flow.
	$wgFlowContentFormat = 'html';

	if ( $wmgFlowEnglishNamespaceOnly ) {
		// HACK: Only use English namespace names, override the localized namespace names
		// This is needed for languages where the translation for Thread: (from LQT)
		// and Topic: (from Flow) are the same, like in Portuguese. In those cases we
		// make the Flow Topic: namespace only use the English name, so the translated name
		// will still point to the LQT namespace.
		$wgExtraNamespaces[2600] = 'Topic'; // NS_TOPIC
	}

	$wgFlowDefaultWikiDb = $wmgFlowDefaultWikiDb;
	$wgFlowCluster = $wmgFlowCluster;
	$wgFlowExternalStore = $wgDefaultExternalStore;

	if ( $wmgFlowAllowAutoconfirmedEdit ) {
		$wgGroupPermissions['autoconfirmed']['flow-edit-post'] = true;
	}

	$wgFlowEnableOptInBetaFeature = $wmgFlowEnableOptInBetaFeature;

	// On wikis that have Flow as a beta feature or in an entire namespace,
	// give sysops the right to create and move Flow boards
	if ( $wgFlowEnableOptInBetaFeature || $wmgFlowNamespaces ) {
		$wgGroupPermissions['sysop']['flow-create-board'] = true;
	}
}

if ( $wmgUseDisambiguator ) {
	wfLoadExtension( 'Disambiguator' );
}

if ( $wmgUseDiscussionTools ) {
	wfLoadExtension( 'DiscussionTools' );

	// The auto topic subscription feature is disabled by default for existing users, but
	// we enable it for new users (T294398).
	$wgDefaultUserOptions['discussiontools-autotopicsub'] = 0;
	$wgConditionalUserOptions['discussiontools-autotopicsub'] = [
		[
			1,
			[ CUDCOND_AFTER, '20230818000000' ]
		]
	];

	$wgConditionalUserOptions['echo-subscriptions-email-dt-subscription'] = [
		[
			true,
			[ CUDCOND_AFTER, '20230818000000' ]
		]
	];
}

wfLoadExtension( 'CodeEditor' );

if ( $wmgUseScribunto ) {
	wfLoadExtension( 'Scribunto' );
	$wgScribuntoUseGeSHi = true;
	$wgScribuntoUseCodeEditor = true;
	$wgScribuntoGatherFunctionStats = true;  // ori, 29-Oct-2015
	$wgScribuntoSlowFunctionThreshold = 0.99;

	$wgScribuntoDefaultEngine = 'luasandbox';
	$wgScribuntoEngineConf['luasandbox']['cpuLimit'] = 10;
	$wgScribuntoEngineConf['luasandbox']['maxLangCacheSize'] = 200; // see T85461#3100878
	$wgScribuntoEngineConf['luasandbox']['memoryLimit'] = $wmgScribuntoMemoryLimit;
	// Temporarily disable profiling, for T389734
	$wgScribuntoEngineConf['luasandbox']['profilerPeriod'] = false;
}

if ( $wmgUseSubpageSortkey ) {
	wfLoadExtension( 'SubpageSortkey' );
	$wgSubpageSortkeyByNamespace = $wmgSubpageSortkeyByNamespace;
}

if ( $wmgUseGeoCrumbs ) {
	wfLoadExtension( 'GeoCrumbs' );
}

if ( $wmgUseCalendar ) {
	wfLoadExtension( 'Calendar' );
}

if ( $wmgUseMapSources ) {
	wfLoadExtension( 'MapSources' );
}

if ( $wmgUseCreditsSource ) {
	wfLoadExtension( 'CreditsSource' );
}

if ( $wmgUseTocTree ) {
	wfLoadExtension( 'TocTree' );
	$wgDefaultUserOptions['toc-floated'] = $wmgUseFloatedToc;
}

if ( $wmgUseInsider ) {
	wfLoadExtension( 'Insider' );
}

if ( $wmgUseRelatedArticles ) {
	wfLoadExtension( 'RelatedArticles' );

	$wgRelatedArticlesOnlyUseCirrusSearch = false;
	$wgRelatedArticlesDescriptionSource = 'wikidata';
}

if ( $wmgUseRevisionSlider ) {
	wfLoadExtension( 'RevisionSlider' );
}

if ( $wmgUseTwoColConflict ) {
	wfLoadExtension( 'TwoColConflict' );
	$wgTwoColConflictBetaFeature = $wmgTwoColConflictBetaFeature;

	// Enable oversampled event tracking during limited study period (T249616)
	$wgTwoColConflictTrackingOversample = true;
}

if ( $wmgUseEventLogging ) {
	wfLoadExtension( 'EventLogging' );
	wfLoadExtension( 'EventStreamConfig' );

	// All wikis reference metawiki.
	$wgEventLoggingBaseUri = $wgCanonicalServer . '/beacon/event';
	$wgEventLoggingDBname = 'metawiki';
	$wgEventLoggingSchemaApiUri = 'https://meta.wikimedia.org/w/api.php';

	// For compat, also register the Schema NS on test2.wikipedia.org,
	// because this wiki used to be its own EventLogging repo (T196309)
	if ( $wgDBname === $wgEventLoggingDBname || $wgDBname === 'test2wiki' ) {
		// T47031
		$wgExtraNamespaces[470] = 'Schema';
		$wgExtraNamespaces[471] = 'Schema_talk';
	}

	// Depends on EventLogging
	if ( $wmgUseCampaigns ) {
		wfLoadExtension( 'Campaigns' );
	}

	// Depends on EventLogging
	if ( $wmgUseWikimediaEvents ) {
		wfLoadExtension( 'WikimediaEvents' );
		$wgWMEStatsdBaseUri = '/beacon/statsv';
		$wgWMEStatsBeaconUri = '/beacon/statsv';
		if ( $wgDBname === 'testwiki' ) {
			$wgGroupPermissions['data-qa']['perform-data-qa'] = true; // T276515
		}
		// On Beta cluster, this is null (see LabsServices.php), as IPoid doesn't
		// have a public facing API.
		$wgWikimediaEventsIPoidUrl = $wmgLocalServices['ipoid'];
	}

	// Depends on EventLogging
	if ( $wmgUseNavigationTiming ) {
		wfLoadExtension( 'NavigationTiming' );
	}
}

wfLoadExtension( 'XAnalytics' );

if ( $wmgUseUniversalLanguageSelector ) {
	wfLoadExtension( 'UniversalLanguageSelector' );
	$wgULSGeoService = false;
	$wgULSAnonCanChangeLanguage = false;
	$wgULSPosition = $wmgULSPosition;
	$wgULSIMEEnabled = $wmgULSIMEEnabled;
	$wgULSWebfontsEnabled = $wmgULSWebfontsEnabled;
	// For CodeEditor and Scribunto:
	$wgULSNoImeSelectors[] = '.ace_editor textarea';
	if ( $wmgUseTranslate && $wmgULSPosition === 'personal' ) {
		$wgTranslatePageTranslationULS = true;
	}

	// Compact Language Links …

	// … as a beta feature (see T136677 for beta to stable)
	$wgULSCompactLanguageLinksBetaFeature = $wmgULSCompactLanguageLinksBetaFeature;

	// … as a stable feature
	$wgDefaultUserOptions['compact-language-links'] = 1;
}

if ( $wmgUseFileExporter ) {
	wfLoadExtension( 'FileExporter' );

	if ( $wmgUseFileExporter !== true ) {
		$wgFileExporterTarget = $wmgUseFileExporter;
	}
}

if ( $wmgUseFileImporter ) {
	wfLoadExtension( 'FileImporter' );
	$wgFileImporterCommonsHelperServer = 'https://www.mediawiki.org';
	$wgFileImporterCommonsHelperBasePageName = 'Extension:FileImporter/Data/';
	$wgFileImporterCommonsHelperHelpPage = 'https://www.mediawiki.org/wiki/Extension:FileImporter/List_of_configured_wikis';
	$wgFileImporterSourceSiteServices = [ $wmgUseFileImporter ];
	// Temporary solution until we have a stable implementation for this
	// https://gerrit.wikimedia.org/r/440857
	$wgFileImporterInterWikiMap = [
		'test.wikipedia.org' => 'testwiki',
		'test2.wikipedia.org' => 'test2wiki',
		'mediawiki.org' => 'mw',
		'ar.wikipedia.org' => 'w:ar', // T196969
		'de.wikipedia.org' => 'w:de', // T196969
		'fa.wikipedia.org' => 'w:fa', // T196969
	];
	$wgFileImporterWikidataEntityEndpoint = 'https://www.wikidata.org/wiki/Special:EntityData/';
	$wgFileImporterWikidataNowCommonsEntity = 'Q5611625';
	$wgFileImporterSourceWikiDeletion = true;
	$wgFileImporterSourceWikiTemplating = true;

	// Temporarily enable for testing, see T228851
	if ( $wgDBname === 'testwiki' ) {
		$wgFileImporterWikidataEntityEndpoint = 'https://test.wikidata.org/wiki/Special:EntityData/';
		$wgFileImporterWikidataNowCommonsEntity = 'Q210317';
	}
}

if ( $wmgUseContentTranslation ) {
	wfLoadExtension( 'ContentTranslation' );

	$wgVirtualDomainsMapping['virtual-cx'] = [
		'cluster' => 'extension1',
		'db' => 'wikishared',
	];

	if ( $wmgRealm === 'production' && $wgDBname === 'testwiki' ) {
		unset( $wgVirtualDomainsMapping['virtual-cx'] );
	}

	// T76200: Public URL for cxserver instance
	$wgContentTranslationSiteTemplates['cx'] = 'https://cxserver.wikimedia.org/v1';

	$wgContentTranslationSiteTemplates['cookieDomain'] = '.wikipedia.org';

	$wgContentTranslationTranslateInTarget = $wmgContentTranslationTranslateInTarget;

	$wgContentTranslationUnmodifiedMTThresholdForPublish = $wmgContentTranslationUnmodifiedMTThresholdForPublish;

	if ( $wmgContentTranslationCluster ) {
		$wgContentTranslationCluster = $wmgContentTranslationCluster;
	}

	$wgContentTranslationCampaigns = $wmgContentTranslationCampaigns;

	$wgContentTranslationCXServerAuth = [
		'algorithm' => 'HS256',
		// This is set in PrivateSettings.php
		'key' => $wmgContentTranslationCXServerAuthKey,
		'age' => '3600',
	];

	if ( $wmgUseQuickSurveys ) {
		$wgQuickSurveysConfig[] = [
			'name' => 'Automatic Translation Feedback',
			'type' => 'internal',
			'embedElementId' => 'ax-translation-viewer-section-container',
			'confirmMsg' => 'ax-translation-view-feedback-confirm-title',
			'confirmDescription' => 'ax-translation-view-feedback-confirm-description',
			'privacyPolicy' => 'ax-translation-view-feedback-privacy-statement',
			'enabled' => true,
			'audience' => [
				// Specify an invalid pageId to avoid loading the "ext.quicksurveys.init" unless requested
				'pageIds' => [ -1111 ]
			],
			'coverage' => 0,
			'platforms' => [
				'desktop' => [ 'stable' ],
				'mobile' => [ 'stable' ]
			],
			'questions' => [
				[
					'name' => 'question-1',
					'layout' => 'single-answer',
					'question' => 'ax-translation-view-feedback-title',
					'answers' => [
						[ 'label' => 'ax-translation-view-feedback-positive' ],
						[ 'label' => 'ax-translation-view-feedback-negative' ]
					]
				],

				[
					'name' => 'positive-question-2',
					'dependsOn' => [
						[
							'question' => 'question-1',
							'answerIsOneOf' => [ 'ax-translation-view-feedback-positive' ]
						]
					],
					'layout' => 'multiple-answer',
					'question' => 'ax-translation-view-feedback-details-question',
					'answers' => [
						[ 'label' => 'ax-translation-view-feedback-positive-missing-information' ],
						[ 'label' => 'ax-translation-view-feedback-positive-translation-quality' ],
						[ 'label' => 'ax-translation-view-feedback-positive-quick-overview' ],
						[ 'label' => 'ax-translation-view-feedback-positive-technical-aspect' ],
					],
				],

				[
					'name' => 'negative-question-2',
					'dependsOn' => [
						[
							'question' => 'question-1',
							'answerIsOneOf' => [ 'ax-translation-view-feedback-negative' ]
						]
					],
					'layout' => 'multiple-answer',
					'question' => 'ax-translation-view-feedback-details-question',
					'answers' => [
						[ 'label' => 'ax-translation-view-feedback-negative-missing-information' ],
						[ 'label' => 'ax-translation-view-feedback-negative-translation-quality' ],
						[ 'label' => 'ax-translation-view-feedback-negative-quick-overview' ],
						[ 'label' => 'ax-translation-view-feedback-negative-technical-aspect' ]
					],
				],
			]
		];
	}
}

if ( $wmgUseExternalGuidance ) {
	wfLoadExtension( 'ExternalGuidance' );

	$wgExternalGuidanceMTReferrers = [
		'translate.google.com',
		'translate.googleusercontent.com'
	];

	$wgExternalGuidanceKnownServices = [
		'Google',
		'translate.google.com',
		'translate.googleusercontent.com'
	];
}

if ( $wmgUseCognate ) {
	wfLoadExtension( 'Cognate' );
	$wgVirtualDomainsMapping['virtual-cognate'] = [ 'cluster' => 'extension1', 'db' => 'cognate_wiktionary' ];
	$wgCognateNamespaces = [ 0 ];
}

if ( $wmgUseInterwikiSorting ) {
	$wgInterwikiSortingInterwikiSortOrders = include __DIR__ . '/InterwikiSortOrders.php';
	wfLoadExtension( 'InterwikiSorting' );
}

if ( $wmgUseWikibaseRepo || $wmgUseWikibaseClient || $wmgUseWikibaseMediaInfo ) {
	include __DIR__ . '/Wikibase.php';
}

// Turn off exact search match redirects
if ( $wmgDoNotRedirectOnSearchMatch ) {
	$wgSearchMatchRedirectPreference = true;
	$wgDefaultUserOptions['search-match-redirect'] = false;
}

// put this here to ensure it is available for localisation cache rebuild
$wgWBClientSettings['repoSiteName'] = 'wikibase-repo-name';

if ( $wmgUseTemplateSandbox ) {
	wfLoadExtension( 'TemplateSandbox' );
}

if ( $wmgUsePageAssessments ) {
	wfLoadExtension( 'PageAssessments' );
}

if ( $wmgUsePageImages ) {
	wfLoadExtension( 'PageImages' );
}

if ( $wmgUseSearchExtraNS ) {
	wfLoadExtension( 'SearchExtraNS' );
}

if ( $wmgUseJsonConfig ) {
	wfLoadExtension( 'JsonConfig' );

	if ( $wgDBname === 'testwiki' || $wgDBname === 'testcommonswiki' ) {
		// T379199 - temporary deployment of tracking tables on testcommonswiki
		$wgTrackGlobalJsonLinks = true;
		// cf T385917 - this test table is deprecated, may vanish in future
		$wgTrackGlobalJsonLinksNamespaces = true;

		// T387417 - beta has no testcommonswiki
		if ( $wmgRealm === 'production' ) {
			$wgVirtualDomainsMapping['virtual-globaljsonlinks'] = [
				'db' => 'testcommonswiki'
			];
		}
	} else {
		// T379689 - deployment to Commons for pages + x1 for globaljsonlinks
		$wgTrackGlobalJsonLinks = true;
		// T385917 - after deployment of patch-gjl_namespace_text.sql on x1.commonswiki,
		// set this to true to enable use of the new field.
		$wgTrackGlobalJsonLinksNamespaces = true;
		$wgVirtualDomainsMapping['virtual-globaljsonlinks'] = [
			'cluster' => 'extension1',
			'db' => 'commonswiki'
		];
	}
}

// Needed to handle deleted and old revisions on mediawikiwiki and collabwiki
// after changing JsonConfig configuration (T124748)
$wgContentHandlers['Json.JsonConfig'] = FallbackContentHandler::class;

if ( $wmgEnableJsonConfigDataMode ) {
	$wgJsonConfigEnableLuaSupport = true;

	// https://www.mediawiki.org/wiki/Extension:JsonConfig#Configuration
	$wgJsonConfigModels['Tabular.JsonConfig'] = 'JsonConfig\JCTabularContent';
	$wgJsonConfigs['Tabular.JsonConfig'] = [
		'namespace' => 486,
		'nsName' => 'Data',
		// page name must end in ".tab", and contain at least one symbol
		'pattern' => '/.\.tab$/',
		'license' => 'CC0-1.0',
		'isLocal' => false,
		// Per-wiki config defined in $wmgJsonConfigDataModeConfig
	] + $wmgJsonConfigDataModeConfig;

	$wgJsonConfigModels['Map.JsonConfig'] = 'JsonConfig\JCMapDataContent';
	$wgJsonConfigs['Map.JsonConfig'] = [
		'namespace' => 486,
		'nsName' => 'Data',
		// page name must end in ".map", and contain at least one symbol
		'pattern' => '/.\.map$/',
		'license' => 'CC0-1.0',
		'isLocal' => false,
		// Per-wiki config defined in $wmgJsonConfigDataModeConfig
	] + $wmgJsonConfigDataModeConfig;
}

// Enable Config:Dashiki: sub-namespace on meta.wikimedia.org - T156971
if ( $wmgEnableDashikiData && $wmgUseJsonConfig ) {
	// Dashiki sub-namespace Config:Dashiki: is configured in extension.json
	wfLoadExtension( 'Dashiki' );
}

// T369945
if ( $wmgUseChart ) {
	wfLoadExtension( 'Chart' );
	// set in ProductionServices.php / LabsServices.php
	$wgChartServiceUrl = $wmgLocalServices['chart-renderer'] . '/v1/chart/render';

	if ( $wmgEnableJsonConfigDataMode ) {
		// Set up chart pages with JsonConfig
		$wgJsonConfigModels['Chart.JsonConfig'] = 'MediaWiki\Extension\Chart\JCChartContent';
		$wgJsonConfigs['Chart.JsonConfig'] = [
			'namespace' => 486,
			'nsName' => 'Data',
			// page name must end in ".chart", and contain at least one symbol
			'pattern' => '/.\.chart$/',
			'license' => 'CC0-1.0',
			'isLocal' => false,
			// Per-wiki config defined in $wmgJsonConfigDataModeConfig
		] + $wmgJsonConfigDataModeConfig;
	}

	// Tabular data pages are already set up with JsonConfig through $wmgEnableJsonConfigDataMode
}

if ( $wmgUseGraph ) {
	wfLoadExtension( 'Graph' );

	// **** THIS LIST MUST MATCH puppet/hieradata/role/common/scb.yaml ****
	// See https://www.mediawiki.org/wiki/Extension:Graph#External_data
	//
	$wgGraphAllowedDomains = [
		'https' => [
			'mediawiki.org',
			'wikibooks.org',
			'wikidata.org',
			'wikimedia.org',
			'wikinews.org',
			'wikipedia.org',
			'wikiquote.org',
			'wikisource.org',
			'wikiversity.org',
			'wikivoyage.org',
			'wiktionary.org',
		],
		'wikirawupload' => [
			'upload.wikimedia.org',
		],
		'wikidatasparql' => [
			'query.wikidata.org',
		],
		'geoshape' => [
			'maps.wikimedia.org',
		],
	];
} elseif ( $wmgHideGraphTags ) {
	// Hide raw tags that are displayed due to T334895
	// Note this still uses messages from E:Graph, which are available
	// as long as it is in wmf-config/extension-list.
	$wgHooks['ParserFirstCallInit'][] = 'wmfAddGraphTagToHideRawUsage';
	$wgHooks['ParserAfterParse'][] = 'wmfInstrumentGraphDataSources';
	$wgTrackingCategories[] = 'graph-tracking-category';
	$wgTrackingCategories[] = 'graph-disabled-category';

	// Don't show "Insert graph" tool in VE
	$wgGraphShowInToolbar = false;

	function wmfAddGraphTagToHideRawUsage( Parser $parser ) {
		$parser->setHook( 'graph', 'wmfRenderEmptyGraphTag' );
	}

	/**
	 * @param ?string $input
	 * @param array $args
	 * @param Parser $parser
	 * @param PPFrame $frame
	 * @return string
	 */
	function wmfRenderEmptyGraphTag( $input, array $args, Parser $parser, PPFrame $frame ) {
		// Add tracking categories
		$parser->addTrackingCategory( 'graph-tracking-category' );
		$parser->addTrackingCategory( 'graph-disabled-category' );

		// Track data sources used by this graph
		$parseResult = FormatJson::parse(
			$input,
			FormatJson::TRY_FIXING | FormatJson::STRIP_COMMENTS
		);
		if ( $parseResult->isGood() ) {
			$parsed = $parseResult->getValue();
			$sources = [];
			foreach ( (array)( $parsed->data ?? [] ) as $dataEntry ) {
				$source = '';
				if ( isset( $dataEntry->url ) ) {
					$source = $dataEntry->url;
				} elseif ( isset( $dataEntry->values ) ) {
					$source = 'inline:';
					if ( is_array( $dataEntry->values ) ) {
						$source .= count( $dataEntry->values );
					} elseif ( is_string( $dataEntry->values ) ) {
						$source .= count( explode( "\n", $dataEntry->values ) );
					} else {
						$source .= 'unknown'; // T369600
					}
				}
				if ( isset( $dataEntry->transform ) ) {
					$source = "transformed:$source";
				}
				$sources[] = $source;
			}
			if ( $sources ) {
				$parser->getOutput()->appendExtensionData( 'graph-data-sources', implode( "\n", $sources ) );
			}
		}

		// Return the placeholder message, if there is one
		$msg = $parser->msg( 'graph-disabled' );
		if ( $msg->isDisabled() ) {
			return '';
		} else {
			return $msg->parseAsBlock();
		}
	}

	function wmfInstrumentGraphDataSources( Parser $parser ) {
		$graphData = $parser->getOutput()->getExtensionData( 'graph-data-sources' );
		if ( !$graphData ) {
			return;
		}
		$sources = array_keys( $graphData );
		$parser->getOutput()->setPageProperty( 'graph-data-sources', implode( "\n\n", $sources ) );
	}
}

if ( $wmgUseOAuth ) {
	wfLoadExtension( 'OAuth' );
	$wgMWOAuthCentralWiki = 'metawiki';
	$wgMWOAuthSharedUserSource = 'CentralAuth';
	$wgMWOAuthSecureTokenTransfer = true;
	$wgOAuth2GrantExpirationInterval = 'PT4H';
	$wgOAuth2RefreshTokenTTL = 'P365D';

	if ( $wgMWOAuthCentralWiki === $wgDBname || $wgMWOAuthCentralWiki === false ) {
		// Management interfaces are available on the central wiki or wikis
		// that are using local OAuth tables
		$wgGroupPermissions['user']['mwoauthproposeconsumer'] = true;
		$wgGroupPermissions['user']['mwoauthupdateownconsumer'] = true;
		$wgGroupPermissions['oauthadmin']['mwoauthmanageconsumer'] = true;
		$wgOAuthGroupsToNotify = [ 'oauthadmin' ];
	}
}

if ( $wmgUseOAuthRateLimiter ) {
	wfLoadExtension( 'OAuthRateLimiter' );
	$wgOAuthRateLimiterDefaultClientTier = 'default';
	// As defined in T246271
	$wgOAuthRateLimiterTierConfig = [
		// demo added for demoing/testing purposes
		'demo' => [
			'ratelimit' => [
				'requests_per_unit' => 10,
				'unit'  => 'HOUR'
			],
		],
		'default' => [
			'ratelimit' => [
				'requests_per_unit' => 5000,
				'unit'  => 'HOUR'
			],
		],
		'preferred' => [
			'ratelimit' => [
				'requests_per_unit' => 25000,
				'unit'  => 'HOUR'
			],
		],
		'internal' => [
			'ratelimit' => [
				'requests_per_unit' => 100000,
				'unit'  => 'HOUR'
			],
		],
		'wme' => [
			'ratelimit' => [
				'requests_per_unit' => 250000,
				'unit'  => 'HOUR'
			],
		],
		// More info: T345394
		'wikieducation' => [
			'ratelimit' => [
				'requests_per_unit' => 150000,
				'unit'  => 'HOUR'
			],
		],
	];
}

// T15712
if ( $wmgUseJosa ) {
	wfLoadExtension( 'Josa' );
}

if ( $wmgUseOATHAuth ) {
	wfLoadExtension( 'OATHAuth' );

	if ( $wmgOATHAuthDisableRight ) {
		$wgGroupPermissions['user']['oathauth-enable'] = false;
		foreach ( $wmgPrivilegedGroups as $group ) {
			if ( isset( $wgGroupPermissions[$group] ) ) {
				$wgGroupPermissions[$group]['oathauth-enable'] = true;
			}
		}
	}

	$wgOATHRequiredForGroups = [
		'interface-admin',
		'centralnoticeadmin',
		'checkuser', // T150898
		'suppress' // T150898
	];

	$wgGroupPermissions['sysop']['oathauth-disable-for-user'] = false;
	$wgGroupPermissions['sysop']['oathauth-view-log'] = false;
	$wgGroupPermissions['sysop']['oathauth-verify-user'] = false; // T209749

	if ( $wmgUseCentralAuth ) {
		$wgOATHAuthAccountPrefix = $wmgRealm === 'labs' ? 'Wikimedia Beta' : 'Wikimedia';
		$wgVirtualDomainsMapping['virtual-oathauth'] = [ 'db' => 'centralauth' ];
	}

	if ( $wmgUseWebAuthn ) {
		wfLoadExtension( 'WebAuthn' );
	}
}

if ( $wmgUseMediaModeration ) {
	if ( $wmgRealm === 'production' ) {
		$wgMediaModerationHttpProxy = $wmgLocalServices['urldownloader'];
	}
	$wgVirtualDomainsMapping['virtual-mediamoderation'] = [ 'cluster' => 'extension1', 'db' => false ];

	wfLoadExtension( 'MediaModeration' );
	// This relies on configuration in PrivateSettings.php:
	// - $wgMediaModerationPhotoDNASubscriptionKey
	// - $wgMediaModerationRecipientList
	$wgMediaModerationFrom = 'wiki@wikimedia.org';
}

if ( $wmgUseORES ) {
	wfLoadExtension( 'ORES' );
	$wgOresBaseUrl = 'http://localhost:6010/';
	$wgOresFrontendBaseUrl = 'https://ores.wikimedia.org/';
	$wgOresLiftWingBaseUrl = 'http://localhost:6031/';
	$wgDefaultUserOptions['oresDamagingPref'] =
		$wgDefaultUserOptions['rcOresDamagingPref'] =
		$wmgOresDefaultSensitivityLevel;

	// Backwards compatibility for upcoming config format change
	if ( isset( $wgOresFiltersThresholds['goodfaith']['good'] ) ) {
		$wgOresFiltersThresholds['goodfaith']['likelygood'] = $wgOresFiltersThresholds['goodfaith']['good'];
	}
	if ( isset( $wgOresFiltersThresholds['goodfaith']['bad'] ) ) {
		$wgOresFiltersThresholds['goodfaith']['likelybad'] = $wgOresFiltersThresholds['goodfaith']['bad'];
	}
}

if ( $wmgUseNewsletter ) {
	wfLoadExtension( 'Newsletter' );
}

if ( $wmgUseRealMe ) {
	wfLoadExtension( 'RealMe' );
}

if ( $wmgUseReportIncident ) {
	wfLoadExtension( 'ReportIncident' );
	// Allow ReportIncident to reach Zendesk in production (T380908)
	if ( $wmgRealm === 'production' ) {
		$wgReportIncidentZendeskHTTPProxy = $wmgLocalServices['urldownloader'];

		$wgReportIncidentZendeskUrl = 'https://wikimediats.zendesk.com';
	}
}

### End (roughly) of general extensions ########################

$wgApplyIpBlocksToXff = true;

// Soft-block private IPs and other shared resources.
// Note this has no effect on edits from traffic proxied through any of these
// IPs with a trusted XFF header, only edits where MediaWiki is actually going
// to attribute the edit (or other logged action) to one of these IPs, e.g.
// that it would show up on [[Special:Contributions/127.0.0.1]].
$wgSoftBlockRanges = array_merge(
	[
		// Ranges that aren't supposed to be publicly routable
		'0.0.0.0/8', // "This host on this network" (RFC 6890)
		'10.0.0.0/8', // Private
		'100.64.0.0/10', // "Shared address space" for internal routing (RFC 6598)
		'127.0.0.0/8', // Loopback
		'169.254.0.0/16', // Link local
		// 172.16.0.0/12 is handled below, don't add it here.
		'192.0.0.0/24', // IETF Protocol Assignments (RFC 6890)
		'192.0.2.0/24', // Documentation
		'192.168.0.0/16', // Private
		'198.18.0.0/15', // Benchmarking (RFC 2544)
		'198.51.100.0/24', // Documentation
		'203.0.113.0/24', // Documentation
		'240.0.0.0/4', // Reserved (RFC 1112)
		'::/128', // Unspecified address
		'::1/128', // Loopback
		'::ffff:0:0/96', // IPv4 mapped (these should be coming in as IPv4, not IPv6)
		'100::/64', // Discard-only address block
		'2001:2::/48', // Benchmarking
		'2001:db8::/32', // Documentation
		'2001:10::/28', // ORCHID (RFC 4843)
		'fc00::/7', // Local communications, not globally routable (RFC 4193)
		'fe80::/10', // Link-local

		// We shouldn't be getting edits via multicast either
		'224.0.0.0/4',
		'ff00::/8',
	],

	// Addresses used by WMF, people should log in to edit from them directly.
	$wgCdnServersNoPurge
);

if ( $wmgAllowLabsAnonEdits ) {
	// CI makes anonymous edits on some wikis, so don't block Cloud VPS
	// private addresses when this feature flag is true.
	$wgSoftBlockRanges = array_merge( $wgSoftBlockRanges, [
		// 172.16.0.0/16 is allowed
		'172.17.0.0/16',
		'172.18.0.0/15',
		'172.20.0.0/14',
		'172.24.0.0/13',
	] );
} else {
	// Cloud VPS users shouldn't be editing anonymously on most wikis, so we
	// can block anonymous edits from the whole private ranges.
	$wgSoftBlockRanges[] = '172.16.0.0/12';
	// Cloud VPS VMs with floating/public addresses
	$wgSoftBlockRanges[] = '185.15.56.0/24';
	// Cloud VPS IPv6 ranges
	$wgSoftBlockRanges[] = ' 2a02:ec80:a000::/48';

	// Cloud VPS is also exempt from autoblocks, so that someone abusing using a tool
	// does not get everyone else using that tool blocked.
	$wgAutoblockExemptions[] = '172.16.0.0/16';
	$wgAutoblockExemptions[] = '185.15.56.0/24';
	$wgAutoblockExemptions[] = '2a02:ec80:a000::/48';
}

// On Special:Version, link to useful release notes
$wgHooks['SpecialVersionVersionUrl'][] = static function ( $version, &$versionUrl ) {
	$matches = [];
	preg_match( "/^(\d+\.\d+)(?:\.0-)?wmf\.?(\d+)?$/", $version, $matches );
	if ( $matches ) {
		$versionUrl = "//www.mediawiki.org/wiki/MediaWiki_{$matches[1]}";
		if ( isset( $matches[2] ) ) {
			$versionUrl .= "/wmf.{$matches[2]}";
		}
		return false;
	}
};

if ( $wmgAllowRobotsControlInAllNamespaces ) {
	$wgExemptFromUserRobotsControl = [];
} else {
	$wgExemptFromUserRobotsControl = array_merge( $wgContentNamespaces, $wmgExemptFromUserRobotsControlExtra );
}

// additional "language names", adding to Names.php data
$wgExtraLanguageNames = $wmgExtraLanguageNames;

if ( $wmgUseCheckUser ) {
	wfLoadExtension( 'CheckUser' );
	if ( $wmgUseCentralAuth ) {
		// T128605
		// Only for CA wikis - will break stuff otherwise
		$wgCheckUserCAMultiLock = [
			'centralDB' => 'metawiki',
			'groups' => [ 'steward' ]
		];
		if ( $wmgUseGlobalBlocking ) {
			// Display the GlobalBlocking link to stewards when the GlobalBlocking extension is
			// loaded on the wiki. GlobalBlocking will only load if CentralAuth is also loaded.
			$wgCheckUserGBtoollink = [
				'centralDB' => 'metawiki',
				'groups' => [ 'steward' ],
			];
		}
	} else {
		// Disable central index for wikis which do not use CentralAuth, as it relies on central IDs
		// (which need CentralAuth)
		$wgCheckUserWriteToCentralIndex = false;
	}
	// Virtual domains config for CheckUser central index tables (T371724)
	$wgVirtualDomainsMapping['virtual-checkuser-global'] = [ 'cluster' => 'extension1', 'db' => 'wikishared' ];

	// Force redirect of all wikis' Special:GlobalContributions pages to meta's (T376612)
	$wgCheckUserGlobalContributionsCentralWikiId = 'metawiki';

	// UserInfoCard
	if ( $wgDBname === 'testwiki' ) {
		$wgConditionalUserOptions['checkuser-userinfocard-enable'] = [
			[ '1', [ CUDCOND_NAMED ] ]
		];
	}
}

if ( $wmgUseIPReputation ) {
	wfLoadExtension( 'IPReputation' );
	// Switch on in case of emergency. Non-sighted users
	// will be prevented from logging in.
	// Only re-enable if IPReputation's ConfirmEditHandler
	// does a more nuanced check of reputation data.
	$wgIPReputationEnableLoginCaptchaIfIPKnown = false;
}

// IP Masking / Temporary accounts

// Unless otherwise specified, temporary accounts are disabled and not known about.
$wgAutoCreateTempUser['enabled'] = false;
$wgAutoCreateTempUser['known'] = false;

if ( $wmgDisableIPMasking ) {
	// Temporary accounts were previously enabled, then disabled as an emergency measure.
	$wgAutoCreateTempUser['enabled'] = false;
	$wgAutoCreateTempUser['known'] = true;
} elseif ( $wmgEnableIPMasking ) {
	// Ensure temporary accounts behave the same on all wikis where they are enabled.
	$wgAutoCreateTempUser['enabled'] = true;
	$wgAutoCreateTempUser['known'] = true;

	if ( $wgDBname !== 'loginwiki' ) {
		$wgGroupPermissions['temp']['edit'] = true;
	}

	// T357586
	$wgImplicitGroups[] = 'temp';
}

if ( $wmgDisableIPMasking || $wmgEnableIPMasking ) {
	// Hide IP reveal on special pages where it is not useful or currently confusing (T379583)
	$wgCheckUserSpecialPagesWithoutIPRevealButtons[] = 'AbuseLog';
	if ( $wmgUseCentralAuth && $wmgUseGlobalBlocking ) {
		$wgCheckUserSpecialPagesWithoutIPRevealButtons[] = 'GlobalBlockList';
		$wgCheckUserSpecialPagesWithoutIPRevealButtons[] = 'MassGlobalBlock';
	}
}

// T393615
$wgCheckUserGroupRequirements = [
	'temporary-account-viewer' => [
		'edits' => 300,
		'age' => 86400 * 30 * 6,
		'exemptGroups' => [ 'steward' ],
		'reason' => 'checkuser-group-requirements-temporary-account-viewer',
	],
];

// Ensure no users can be crated that match temporary account names (T361021).
// This is used even if `$wgAutoCreateTempUser['enabled']` is false.
$wgAutoCreateTempUser['reservedPattern'] = '~2$1';

if ( $wmgUseCentralAuth ) {
	// Ensure users in certain local and global groups are automatically promoted to and demoted from
	// the global temporary account viewer group (T376315). Note that the global groups in the array
	// keys must not have local groups with the same name, and vice-versa.
	$wgCentralAuthAutomaticGlobalGroups = [
		'checkuser' => [ 'global-temporary-account-viewer' ], // local checkuser group
		'suppress' => [ 'global-temporary-account-viewer' ], // local suppress group
		'global-sysop' => [ 'global-temporary-account-viewer' ],
	];

	// If CentralAuth is installed, then use the centralauth provider to ensure that a new temporary account
	// uses a unique serial number across all wikis. This will have no effect if
	// `$wgAutoCreateTempUser['enabled']` is false.
	$wgAutoCreateTempUser['serialProvider'] = [
		'type' => 'centralauth',
		'numShards' => 8,
	];
} else {
	// If CentralAuth is not installed, then use the local provider.
	// This will have no effect if `$wgAutoCreateTempUser['enabled']` is false.
	$wgAutoCreateTempUser['serialProvider'] = [
		'type' => 'local',
		'numShards' => 8,
	];
}

// Add the year to the username to make it easier to identify the year the tmeporary account was created
// and identify based on the username if the temporary account has expired.
$wgAutoCreateTempUser['serialProvider']['useYear'] = true;

// We only need to match ~2$1 because the year will start with 2 for the foreseeable future
// and it prevents the need to rename users on production which start with ~ but not ~2 (T349507).
// This will have no effect if `$wgAutoCreateTempUser['enabled']` is false.
$wgAutoCreateTempUser['matchPattern'] = '~2$1';

// Start numbers at 1500 to avoid using any numbers defined in T337090 which are considered defamatory.
// This will have no effect if `$wgAutoCreateTempUser['enabled']` is false.
$wgAutoCreateTempUser['serialMapping'] = [ 'type' => 'readable-numeric', 'offset' => 1500 ];

// T39211
$wgUseCombinedLoginLink = false;

if ( $wmgUseRC2UDP ) {
	if ( $wmgRC2UDPPrefix === false ) {
		$matches = null;
		if ( preg_match( '/^(https?:)?\/\/(.+)\.org$/', $wgServer, $matches ) && isset( $matches[2] ) ) {
			$wmgRC2UDPPrefix = "#{$matches[2]}\t";
		}
	}

	foreach ( $wmgLocalServices['irc'] as $i => $address ) {
		$wgRCFeeds["irc$i"] = [
			'formatter' => 'IRCColourfulRCFeedFormatter',
			'uri' => "udp://$address:$wmgRC2UDPPort/$wmgRC2UDPPrefix",
			'add_interwiki_prefix' => false,
			'omit_bots' => false,
		];
	}
}

$wgDefaultUserOptions['watchlistdays'] = $wmgWatchlistNumberOfDaysShow;

if ( $wmgUseIPInfo ) {
	wfLoadExtension( 'IPInfo' );
	// n.b. if you are looking for this path on mwmaint or deployment servers, you will not find it.
	// It is only present on application servers. See
	// https://codesearch.wmcloud.org/search/?q=GeoIPInfo&files=&excludeFiles=&repos=#operations/puppet
	// for list of relevant sections of operations/puppet config.
	$wgIPInfoGeoLite2Prefix = '/usr/share/GeoIPInfo/GeoLite2-';

	// On wikis with temporary accounts, grant full access to members of the "temporary-account-viewer"
	// group provided by CheckUser to ensure that access to IP information reflects our policy (T375086).
	// On Beta Cluster wikis, which do not have CheckUser installed, this amounts to making the feature
	// admin-only.
	// Keep full access for autoconfirmed users on wikis where temporary accounts are not known
	// to avoid disruption.
	if ( $wgAutoCreateTempUser['known'] ) {
		if ( $wmgUseCheckUser ) {
			$wgGroupPermissions['temporary-account-viewer']['ipinfo'] = true;
			$wgGroupPermissions['temporary-account-viewer']['ipinfo-view-full'] = true;
		}
	} else {
		$wgGroupPermissions['autoconfirmed']['ipinfo'] = true;
		$wgGroupPermissions['autoconfirmed']['ipinfo-view-basic'] = true;
	}

	$wgGroupPermissions['sysop']['ipinfo'] = true;
	$wgGroupPermissions['sysop']['ipinfo-view-full'] = true;

	if ( $wmgUseCheckUser ) {
		$wgGroupPermissions['checkuser']['ipinfo'] = true;
		$wgGroupPermissions['checkuser']['ipinfo-view-full'] = true;
		$wgGroupPermissions['checkuser']['ipinfo-view-log'] = true;
	}

	$wgIPInfoIpoidUrl = $wmgLocalServices['ipoid'];
}

if ( $wmgUseWikidataPageBanner ) {
	wfLoadExtension( 'WikidataPageBanner' );
}

if ( $wmgUseQuickSurveys ) {
	wfLoadExtension( 'QuickSurveys' );
}

wfLoadExtension( 'EventBus' );

// For analytics purposes, we forward the X-Client-IP header to eventgate.
// eventgate will use this to set a default http.client_ip in event data when relevant.
// https://phabricator.wikimedia.org/T288853
//
// NOTE: if you change request timeout values here, you should
// also change eventgate timeout and prestop_sleep settings.
// - https://gerrit.wikimedia.org/r/plugins/gitiles/operations/deployment-charts/+/fd8492c76352ac48d07ebb8d950d3281b91181fa/charts/eventgate/values.yaml#59
// - https://phabricator.wikimedia.org/T349823
$wgEventServices = [
	'eventgate-analytics' => [
		'url' => "{$wmgLocalServices['eventgate-analytics']}/v1/events?hasty=true",
		'timeout' => 11,
		'x_client_ip_forwarding_enabled' => true,
	],
	'eventgate-analytics-external' => [
		'url' => "{$wmgLocalServices['eventgate-analytics-external']}/v1/events?hasty=true",
		'timeout' => 11,
		'x_client_ip_forwarding_enabled' => true,
	],
	'eventgate-main' => [
		'url' => "{$wmgLocalServices['eventgate-main']}/v1/events",
		'timeout' => 62, // envoy overall req timeout + 1
	]
];

$wgRCFeeds['eventbus'] = [
	'formatter' => EventBusRCFeedFormatter::class,
	'class' => EventBusRCFeedEngine::class,
];

$wgJobTypeConf['default'] = [
	'class' => JobQueueEventBus::class,
	'readOnlyReason' => false
];

$wgEventBusEnableRunJobAPI = ClusterConfig::getInstance()->isAsync();

if ( $wmgUseCapiunto ) {
	wfLoadExtension( 'Capiunto' );
}

if ( $wmgUseKartographer && $wmgUseJsonConfig ) {
	wfLoadExtension( 'Kartographer' );
	$wgKartographerMapServer = 'https://maps.wikimedia.org';

	if ( $wmgEnableGeoData && $wmgKartographerNearby ) {
		$wgKartographerNearby = true;
	}
}

if ( $wmgUsePageViewInfo ) {
	wfLoadExtension( 'PageViewInfo' );
	$wgPageViewInfoWikimediaEndpoint = $wmgLocalServices['rest-gateway'] . '/wikimedia.org/v1';
}

if ( $wgDBname === 'foundationwiki' ) {
	// Foundationwiki has raw html enabled. Attempt to prevent people
	// from accidentally violating the privacy policy with external scripts.
	// Note, we need all WMF domains in here due to Special:HideBanners
	// being loaded as an image from various domains on donation thank you
	// pages.
	$wgHooks['BeforePageDisplay'][] = static function ( $out, $skin ) {
		$resp = $out->getRequest()->response();
		$cspHeader = "default-src *.wikimedia.org *.wikipedia.org *.wiktionary.org *.wikisource.org *.wikibooks.org *.wikiversity.org *.wikiquote.org *.wikinews.org www.mediawiki.org www.wikidata.org *.wikifunctions.org *.wikivoyage.org data: blob: 'self'; script-src *.wikimedia.org 'unsafe-inline' 'unsafe-eval' 'self'; style-src  *.wikimedia.org data: 'unsafe-inline' 'self'; report-uri /w/api.php?action=cspreport&format=none&reportonly=1&source=wmfwiki&";
		$resp->header( "Content-Security-Policy-Report-Only: $cspHeader" );
	};
}

if ( $wmgUse3d ) {
	wfLoadExtension( '3D' );
	$wgTrustedMediaFormats[] = 'application/sla';
	$wg3dProcessor = [ '/usr/bin/xvfb-run', '-a', '-s', '-ac -screen 0 1280x1024x24', '/srv/deployment/3d2png/deploy/src/3d2png.js' ];

	if ( $wmgUseMultimediaViewer ) {
		$wgMediaViewerExtensions['stl'] = 'mmv.3d';
	}

	if ( $wmgUpload3d ) {
		$wgFileExtensions[] = 'stl';
	}
}

if ( $wmgUseReadingLists ) {
	wfLoadExtension( 'ReadingLists' );
	$wgVirtualDomainsMapping['virtual-readinglists'] = [ 'cluster' => 'extension1', 'db' => 'wikishared' ];
	$wgReadingListsMaxEntriesPerList = 5000;
	$wgReadingListAndroidAppDownloadLink = 'https://play.google.com/store/apps/details?id=org.wikipedia&referrer=utm_source%3DreadingLists';
	$wgReadingListiOSAppDownloadLink = 'https://apps.apple.com/app/apple-store/id324715238?pt=208305&ct=shared-reading-list-landing&mt=8';
}

if ( $wmgUseGlobalPreferences && $wmgUseCentralAuth ) {
	// This is intentionally loaded *after* the Echo extension (above).
	wfLoadExtension( 'GlobalPreferences' );
}

if ( $wmgUseWikisource ) {
	// Intentionally loaded *after* the Collection extension above.
	wfLoadExtension( 'Wikisource' );
	$wgWikisourceHttpProxy = $wgCopyUploadProxy;
}

if ( $wmgUseGrowthExperiments ) {
	wfLoadExtension( 'GrowthExperiments' );
	$wgVirtualDomainsMapping['virtual-growthexperiments'] = [ 'cluster' => 'extension1', 'db' => false ];

	// T298122 temporary fix while mobile-only quality gate gets removed
	$wgDefaultUserOptions['growthexperiments-addimage-desktop'] = 1;

	$wgGEImageRecommendationServiceUrl = $wmgLocalServices['image-suggestion'];
	$wgGELinkRecommendationServiceUrl = $wmgLocalServices['linkrecommendation'];

	// Ensure experiment conditional options are applied only in wikis where
	// the relevant experiment is enabled.
	if ( $wmgGEActiveExperiment === 'no-link-recommendation' ) {
		// Add Link experiment, T377631
		$wgConditionalUserOptions['growthexperiments-homepage-variant'] = [
			[ 'control',
				[ 'user-bucket-growth', 'no-link-recommendation', 20 ],
				[ CUDCOND_AFTER, '20250324000000' ],
			],
			[ 'control',
				[ 'user-bucket-growth', 'no-link-recommendation', 15 ],
				[ CUDCOND_AFTER, '20250220140000' ],
			],
			[ 'control',
				[ 'user-bucket-growth', 'no-link-recommendation', 10 ],
				[ CUDCOND_AFTER, '20250128090000' ],
			],
			[ 'control',
				[ 'user-bucket-growth', 'no-link-recommendation', 5 ],
				[ CUDCOND_AFTER, '20250107000000' ],
			],
			[ 'control',
				[ 'user-bucket-growth', 'no-link-recommendation', 2 ],
				[ CUDCOND_AFTER, '20241125000000' ],
			],
			[ 'no-link-recommendation',
				[ 'user-bucket-growth', 'no-link-recommendation', 100 ],
			],
		];
	} elseif ( $wmgGEActiveExperiment === 'surfacing-structured-task' ) {
		// Surfacing structured tasks experiment, T385903
		$wgConditionalUserOptions['growthexperiments-homepage-variant'] = [
			[ 'surfacing-structured-task',
				[ 'local-user-bucket-growth', 'surfacing-structured-task', 50 ],
			],
			[ 'control',
				[ 'local-user-bucket-growth', 'surfacing-structured-task', 100 ],
			],
		];
	} elseif ( $wmgGEActiveExperiment === 'get-started-notification' ) {
		// Get Started experiment, T394958
		$wgConditionalUserOptions['growthexperiments-homepage-variant'] = [
			[ 'get-started-notification',
				[ 'user-bucket-growth', 'get-started-notification', 50 ],
				[ CUDCOND_AFTER, '20250624000000' ],
			],
			[ 'control',
				[ 'user-bucket-growth', 'get-started-notification', 100 ],
			],
		];
	}
}

if ( $wmgUseWikiLambda ) {
	wfLoadExtension( 'WikiLambda' );

	$wgWikiLambdaObjectCache = 'mcrouter-wikifunctions';

	if ( $wgWikiLambdaEnableRepoMode ) {
		$wgWikiLambdaOrchestratorLocation = $wmgLocalServices['wikifunctions-orchestrator'];
		$wgWikiLambdaClientWikis = WmfConfig::readDbListFile( 'wikifunctionsclient' );
		$wgWikiLambdaPersistBackendCache = true;
	}
}

if ( $wmgUseWikistories ) {
	wfLoadExtension( 'Wikistories' );
}

if ( $wmgUseCSPReportOnly || $wmgUseCSPReportOnlyHasSession || $wmgUseCSP ) {
	// Temporary global whitelist for origins used by trusted
	// opt-in scripts, until a per-user ability for this exists.
	// T207900#4846582
	$wgCSPFalsePositiveUrls['https://cvn.wmflabs.org'] = true;
	$wgCSPFalsePositiveUrls['https://tools.wmflabs.org/intuition/'] = true;
	$wgCSPFalsePositiveUrls['https://intuition.toolforge.org/'] = true;

	$wgExtensionFunctions[] = static function () {
		global $wgCSPReportOnlyHeader, $wmgUseCSPReportOnly,
			$wmgApprovedContentSecurityPolicyDomains, $wmgUseCSP, $wgCSPHeader;
		if ( !$wmgUseCSPReportOnly && !$wmgUseCSP ) {
			// This means that $wmgUseCSPReportOnlyHasSession
			// is set, so only logged in users should trigger this.
			if (
				defined( 'MW_NO_SESSION' ) ||
				MW_ENTRY_POINT === 'cli' ||
				!SessionManager::getGlobalSession()->isPersistent()
			) {
				// There is no session, so don't set CSP, as we care more
				// about actual users, and this allows quick revert since
				// not cached in varnish
				return;
			}
		}

		$cspConfig = [
			'useNonces' => false,
			'includeCORS' => false,
			'default-src' => array_merge(
				$wmgApprovedContentSecurityPolicyDomains,
				[
					// Needed for Math. Remove when/if math is fixed.
					'wikimedia.org',
				]
			),
			'script-src' => $wmgApprovedContentSecurityPolicyDomains,
		];

		if ( $wmgUseCSPReportOnly ) {
			// $wgCSPReportOnlyHeader defaults to false, so setup an array for config
			$wgCSPReportOnlyHeader = $cspConfig;
		}

		if ( $wmgUseCSP ) {
			// $wgCSPHeader defaults to false, so setup an array for config
			$wgCSPHeader = $cspConfig;
		}
	};
}

if ( $wmgUseCampaignEvents ) {
	wfLoadExtension( 'CampaignEvents' );
	wfLoadExtension( 'WikimediaCampaignEvents' );
	$wgVirtualDomainsMapping['virtual-campaignevents'] = [
		'cluster' => 'extension1',
		'db' => $wmgCampaignEventsUseCentralDB ? 'wikishared' : false,
	];
	$wgCampaignEventsProgramsAndEventsDashboardInstance = 'production';
	if ( !$wmgCampaignEventsUseEventOrganizerGroup ) {
		// Unset the event-organizer group if not needed. Must be done after extension settings
		// have been merged and applied, and also not in an extension function due to T275334.
		// Note, redundant entries in wgAddGroups and wgRemoveGroups are harmless.
		$wgHooks['MediaWikiServices'][] = static function () {
			global $wgGroupPermissions;
			unset( $wgGroupPermissions['event-organizer'] );
		};
	}
	$wgWikimediaCampaignEventsSparqlEndpoint = 'http://localhost:6041/sparql';
}

// T361643
if ( $wmgUseAutoModerator ) {
	wfLoadExtension( 'AutoModerator' );
	$wgAutoModeratorLiftWingBaseUrl = 'https://inference.discovery.wmnet:30443/v1/models/';
	$wgAutoModeratorLiftWingAddHostHeader = true;
}

if ( $wmgRealm === 'labs' ) {
	require __DIR__ . '/CommonSettings-labs.php';
}

foreach ( $wgGroupPermissions as $group => $_ ) {
	if ( $group !== 'interface-admin' && (
		!empty( $wgGroupPermissions[$group]['editsitecss'] )
		|| !empty( $wgGroupPermissions[$group]['editsitejs'] )
		|| !empty( $wgGroupPermissions[$group]['editusercss'] )
		|| !empty( $wgGroupPermissions[$group]['edituserjs'] )
		|| !empty( $wgGroupPermissions[$group]['editmyuserjsredirect'] )
	) ) {
		// enforce that interace-admin is the only group that can edit non-own CSS/JS
		unset(
			$wgGroupPermissions[$group]['editsitecss'],
			$wgGroupPermissions[$group]['editsitejs'],
			$wgGroupPermissions[$group]['editusercss'],
			$wgGroupPermissions[$group]['edituserjs'],
			$wgGroupPermissions[$group]['editmyuserjsredirect']
		);
	}
}

if ( $wmgShowRollbackConfirmationDefaultUserOptions ) {
	$wgDefaultUserOptions['showrollbackconfirmation'] = 1;
}

if ( $wmgUseWikimediaEditorTasks ) {
	wfLoadExtension( 'WikimediaEditorTasks' );
}

// T283003: TheWikipediaLibrary requires GlobalPreferences and CentralAuth to be installed
if ( $wmgUseTheWikipediaLibrary && $wmgUseGlobalPreferences && $wmgUseCentralAuth ) {
	wfLoadExtension( 'TheWikipediaLibrary' );
}

if ( $wmgUseWikimediaApiPortal ) {
	wfLoadSkin( 'WikimediaApiPortal' );
}

if ( $wmgUseWikimediaApiPortalOAuth ) {
	wfLoadExtension( 'WikimediaApiPortalOAuth' );
}

if ( $wmgUseGlobalWatchlist ) {
	wfLoadExtension( 'GlobalWatchlist' );
}

if ( $wmgUseArticleSummaries ) {
	wfLoadExtension( 'ArticleSummaries' );
}

if ( $wmgUseNearbyPages ) {
	wfLoadExtension( 'NearbyPages' );
}

if ( $wmgUseImageSuggestions ) {
	wfLoadExtension( 'ImageSuggestions' );
}

if ( $wmgUseSearchVue ) {
	wfLoadExtension( 'SearchVue' );
}

if ( $wmgUseStopForumSpam ) {
	wfLoadExtension( 'StopForumSpam' );
	$wgSFSIPListLocation = 'https://www.stopforumspam.com/downloads/listed_ip_90_ipv46_all.gz';
	$wgSFSValidateIPListLocationMD5 = 'https://www.stopforumspam.com/downloads/listed_ip_90_ipv46_all.gz.md5';
	$wgSFSProxy = $wgCopyUploadProxy;
}

if ( $wmgUsePhonos ) {
	wfLoadExtension( 'Phonos' );
	// $wgPhonosApiKeyGoogle in PrivateSettings
	$wgPhonosEngine = 'google';
	$wgPhonosFileBackend = 'global-multiwrite';
	$wgPhonosApiProxy = $wgCopyUploadProxy;
}

if ( $wmgUsePageNotice ) {
	wfLoadExtension( 'PageNotice' );
}

if ( $wmgUseCommunityRequests ) {
	wfLoadExtension( 'CommunityRequests' );
}

// This is a temporary hack for hooking up Parsoid/PHP with MediaWiki
// This is just the regular check out of parsoid in that week's vendor
$parsoidDir = "$IP/vendor/wikimedia/parsoid";
$wgParsoidSettings = [
	'useSelser' => true,
	'linting' => true,
];

if ( ClusterConfig::getInstance()->isParsoid() ) {
	// Parsoid testing special case
	if ( ClusterConfig::getInstance()->getHostname() === 'parsoidtest1001' ) {
		// parsoidtest1001 has its own special check out of parsoid for testing.
		$parsoidDir = __DIR__ . "/../../parsoid-testing";
		// Override settings specific to round-trip testing on parsoidtest1001
		require_once "$parsoidDir/tests/RTTestSettings.php";
	}
	// Only load Parsoid extension (aka internal Parsoid REST API) on
	// Parsoid cluster
	wfLoadExtension( 'Parsoid', "$parsoidDir/extension.json" );
}

unset( $parsoidDir );
// End of temporary hack for hooking up Parsoid/PHP with MediaWiki

if ( $wmgUseParserMigration ) {
	wfLoadExtension( 'ParserMigration' );
}

// T350653
if ( $wmgEditRecoveryDefaultUserOptions ) {
	$wgDefaultUserOptions['editrecovery'] = 1;
}

// Community configuration
if ( $wmgUseCommunityConfiguration ) {
	wfLoadExtension( 'CommunityConfiguration' );
	$wgCommunityConfigurationFeedbackURL = 'https://www.mediawiki.org/wiki/Extension_talk:CommunityConfiguration';
	$wgCommunityConfigurationCommonsApiURL = 'https://commons.wikimedia.org/w/api.php';
}

if ( $wmgUseMetricsPlatform ) {
	wfLoadExtension( 'MetricsPlatform' );
	$wgMetricsPlatformInstrumentConfiguratorBaseUrl = $wmgLocalServices['mpic'];
}

if ( $wmgUseNetworkSession ) {
	// Note: users are defined in private repos
	wfLoadExtension( 'NetworkSession' );
	$wgNetworkSessionProviderAllowedUserRights = [ 'read' ];
	$wgNetworkSessionProviderCanAlwaysAutocreate = true;
}

// phpcs:ignore MediaWiki.Files.ClassMatchesFilename.NotMatch
class ClosedWikiProvider extends AbstractPreAuthenticationProvider {
	/**
	 * @param User $user
	 * @param bool $autocreate
	 * @param array $options
	 * @return StatusValue
	 */
	public function testUserForCreation( $user, $autocreate, array $options = [] ) {
		$logger = LoggerFactory::getInstance( 'authentication' );
		$logger->info( 'Running ClosedWikiProvider for {name}', [
			'name' => $user->getName()
		] );
		if ( $user->getId() ) { // User already exists, do not block authentication
			$logger->info( 'User {name} passed ClosedWikiProvider check, account already exists', [
				'name' => $user->getName()
			] );
			return StatusValue::newGood();
		}
		if ( $options['canAlwaysAutocreate'] ?? false ) {
			$logger->info(
				'User {name} passed ClosedWikiProvider check, provider can always create accounts',
				[
					'name' => $user->getName()
				]
			);
			return \StatusValue::newGood();
		}
		$central = CentralAuthUser::getInstance( $user );
		if (
			$central->hasGlobalPermission( 'createaccount' ) ||
			$central->hasGlobalPermission( 'autocreateaccount' )
		) {
			$logger->info(
				'User {name} passed ClosedWikiProvider check, has permissions',
				[
					'name' => $user->getName()
				]
			);
			// User can autocreate account per global permissions
			return StatusValue::newGood();
		}
		$logger->info(
			'Account autocreation denied for {name} by ClosedWikiProvider', [
				'name' => $user->getName()
			]
		);
		return StatusValue::newFatal( 'authmanager-autocreate-noperm' );
	}
}

if (
	in_array( $wgDBname, WmfConfig::readDbListFile( 'closed' ) ) &&
	$wmgUseCentralAuth
) {
	$wgAuthManagerAutoConfig['preauth'][ClosedWikiProvider::class] = [
		'class' => ClosedWikiProvider::class,
		'sort' => 0,
	];
}

$wgLogRestrictions = array_merge( $wgLogRestrictions, $wmgLogRestrictions );

// HACK for T370517: map this Codex message until this Codex i18n bug is fixed
$wgHooks['MessageCacheFetchOverrides'][] = static function ( &$keys ) {
	$keys['cdx-search-input-search-button-label'] = 'searchbutton';
};

# THIS MUST BE AFTER ALL EXTENSIONS ARE INCLUDED
#
# REALLY ... we're not kidding here ... NO EXTENSIONS AFTER

if ( !defined( 'MW_NO_EXTENSION_MESSAGES' ) ) {
	require __DIR__ . "/ExtensionMessages-$wmgVersionNumber.php";
}
