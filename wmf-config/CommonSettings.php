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
#       |-- wmf-config/etcd.php
#       |-- wmf-config/InitialiseSettings.php
#       |-- private/PrivateSettings.php
#       |-- wmf-config/logging.php
#       |-- wmf-config/redis.php
#       |-- wmf-config/filebackend.php
#       |-- wmf-config/db-*.php
#       |-- wmf-config/mc.php
#       |
#       `-- (main stuff in CommonSettings.php)
#

use MediaWiki\Auth\AuthenticationResponse;
use MediaWiki\Logger\LoggerFactory;
use MediaWiki\MediaWikiServices;
use MediaWiki\User\UserIdentity;
use Wikimedia\MWConfig\ServiceConfig;
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
require_once __DIR__ . '/etcd.php';
require_once __DIR__ . '/../multiversion/MWConfigCacheGenerator.php';

// - This file must be included by MediaWiki via our LocalSettings.php file.
//   Determined by MediaWiki core having initialised the $IP variable.
// - MediaWiki must be included via a multiversion-aware entry point
//   (e.g. WMF's "w/index.php", or MWScript).
//   That entry point must have initialised the MWMultiVersion singleton and
//   decided which wiki we are on (based on hostname or --wiki CLI arg).
//   Note that getInstance does not (and may not) lazy-create this instance,
//   it returns null if it hasn't been setup yet.
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
switch ( $wmfRealm ) {
case 'labs':
	$wmgHostnames['meta']          = 'meta.wikimedia.beta.wmflabs.org';
	$wmgHostnames['test']          = 'test.wikipedia.beta.wmflabs.org';
	$wmgHostnames['upload']        = 'upload.wikimedia.beta.wmflabs.org';
	$wmgHostnames['wikidata']      = 'wikidata.beta.wmflabs.org';
	$wmgHostnames['wikifunctions'] = 'wikifunctions.beta.wmflabs.org';
	break;
case 'production':
default:
	$wmgHostnames['meta']          = 'meta.wikimedia.org';
	$wmgHostnames['test']          = 'test.wikipedia.org';
	$wmgHostnames['upload']        = 'upload.wikimedia.org';
	$wmgHostnames['wikidata']      = 'www.wikidata.org';
	$wmgHostnames['wikifunctions'] = 'www.wikifunctions.org';
	break;
}

$wgDBname = $multiVersion->getDatabase();
$wgDBuser = 'wikiuser'; // Don't rely on MW default (T46251)
$wgSQLMode = null;

$wmfConfigDir = "$IP/../wmf-config";

$wmgVersionNumber = $multiVersion->getVersionNumber();

# Used further down this file for per-wiki SiteConfiguration caching:
# - MUST be in /tmp to allow atomic rename from elsewhere in /tmp.
# - MUST vary by MediaWiki branch (aka MWMultiversion deployment version).
#
# Also used by MediaWiki core for various caching purposes, and may
# be shared between wikis (e.g. does not need to vary by wgDBname).
$wgCacheDirectory = '/tmp/mw-cache-' . $wmgVersionNumber;

$wmgServerGroup = $_SERVER['SERVERGROUP'] ?? '';

# Whether MediaWiki is running on Kubernetes, intended for config
# that needs to differ during the migration. On dedicated servers,
# SERVERGROUP is set by Puppet in profile::mediawiki::httpd, in
# Kubernetes pods, it's set by configuring the php.servergroup
# Helm value.
$wmfUsingKubernetes = strpos( $wmgServerGroup, 'kube-' ) === 0;

# Get all the service definitions
$wmfAllServices = ServiceConfig::getInstance()->getAllServices();

# Shorthand when we have no master-replica situation to keep into account
$wmfLocalServices = $wmfAllServices[$wmfDatacenter];

# The list of datacenters known to this realm
$wmfDatacenters = ServiceConfig::getInstance()->getDatacenters();

if ( getenv( 'WMF_MAINTENANCE_OFFLINE' ) ) {
	// Prepare just enough configuration to allow
	// rebuildLocalisationCache.php and mergeMessageFileList.php to
	// run to completion without complaints.

	$wmfEtcdLastModifiedIndex = "wmfEtcdLastModifiedIndex uninitialized due to WMF_MAINTENANCE_OFFLINE";
	$wgReadOnly = "In read-only mode because WMF_MAINTENANCE_OFFLINE is set";
	$wmfMasterDatacenter = ServiceConfig::getInstance()->getDatacenter();
	$wmfMasterServices = $wmfAllServices[$wmfMasterDatacenter];
	$wmfDbconfigFromEtcd = [
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

} else {
	$etcdConfig = wmfSetupEtcd( $wmfLocalServices['etcd'] );
	$wmfEtcdLastModifiedIndex = $etcdConfig->getModifiedIndex();

	$wgReadOnly = $etcdConfig->get( "$wmfDatacenter/ReadOnly" );

	$wmfMasterDatacenter = $etcdConfig->get( 'common/WMFMasterDatacenter' );
	$wmfMasterServices = $wmfAllServices[$wmfMasterDatacenter];

	// Database load balancer config (sectionLoads, groupLoadsBySection, …)
	// This is later merged into $wgLBFactoryConf by wmfEtcdApplyDBConfig().
	// See also <https://wikitech.wikimedia.org/wiki/Dbctl>
	$wmfDbconfigFromEtcd = $etcdConfig->get( "$wmfDatacenter/dbconfig" );

	unset( $etcdConfig );
}

$wmfUdp2logDest = $wmfLocalServices['udp2log'];
if ( $wgDBname === 'testwiki' || $wgDBname === 'test2wiki' ) {
	$wgDebugLogFile = "udp://{$wmfUdp2logDest}/testwiki";
} else {
	$wgDebugLogFile = '/dev/null';
}

$wgConf = new SiteConfiguration;
$wgConf->suffixes = MWMultiVersion::SUFFIXES;
$wgConf->wikis = MWWikiversions::readDbListFile( $wmfRealm === 'labs' ? 'all-labs' : 'all' );
$wgConf->fullLoadCallback = 'wmfLoadInitialiseSettings';

/**
 * @param SiteConfiguration $conf
 */
function wmfLoadInitialiseSettings( $conf ) {
	global $wmfConfigDir, $wmfRealm;
	require_once "$wmfConfigDir/InitialiseSettings.php";
	$settings = wmfGetVariantSettings();

	if ( $wmfRealm == 'labs' ) {
		// Beta Cluster overrides
		require_once "$wmfConfigDir/InitialiseSettings-labs.php";
		$settings = wmfApplyLabsOverrideSettings( $settings );
	}

	$conf->settings = $settings;
}

$wgLocalDatabases = $wgConf->getLocalDatabases();

// Do not add wikimedia.org, because of other sites under that domain (such as codereview-proxy.wikimedia.org)
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
	'www.wikidata.org',
	'meta.wikimedia.org', // Presumably needed to load meta spam list. Any others?
	'commons.wikimedia.org',
	'www.mediawiki.org',
];

# Is this database listed in dblist?
# Note: must be done before calling $multiVersion functions other than getDatabase().
if ( array_search( $wgDBname, $wgLocalDatabases ) === false ) {
	# No? Load missing.php
	if ( $wgCommandLineMode ) {
		print "Database name $wgDBname is not listed in dblist\n";
	} else {
		require "$wmfConfigDir/missing.php";
	}
	exit;
}

# Determine domain and language and the directories for this instance
list( $site, $lang ) = $wgConf->siteFromDB( $wgDBname );

# Try configuration cache
$confCacheFileName = "conf2-$wgDBname.json";
$confActualMtime = max(
	filemtime( "$wmfConfigDir/InitialiseSettings.php" ),
	filemtime( "$wmfConfigDir/logos.php" ),
	filemtime( "$IP/includes/Defines.php" )
);
$globals = Wikimedia\MWConfig\MWConfigCacheGenerator::readFromStaticCache(
	$wgCacheDirectory . '/' . $confCacheFileName, $confActualMtime
);

if ( !$globals ) {
	# Get configuration from SiteConfiguration object
	wmfLoadInitialiseSettings( $wgConf );

	$globals = Wikimedia\MWConfig\MWConfigCacheGenerator::getMWConfigForCacheing(
		$wgDBname, $site, $lang, $wgConf, $wmfRealm
	);

	$confCacheObject = [ 'mtime' => $confActualMtime, 'globals' => $globals ];

	# Save cache if the grace period expired.
	# We define the grace period as the opcache revalidation frequency + 1
	# in order to ensure we don't incur in race conditions when saving the values.
	# See T236104
	$minTime = $confActualMtime + intval( ini_get( 'opcache.revalidate_freq' ) );
	if ( time() > $minTime ) {
		Wikimedia\MWConfig\MWConfigCacheGenerator::writeToStaticCache(
			$wgCacheDirectory, $confCacheFileName, $confCacheObject
		);
	}
}
unset( $confCacheFileName, $confActualMtime, $confCacheObject );

extract( $globals );

# -------------------------------------------------------------------------
# Settings common to all wikis

# Private settings such as passwords, that shouldn't be published
# Needs to be before db.php
require "$wmfConfigDir/../private/PrivateSettings.php";

require "$wmfConfigDir/logging.php";
require "$wmfConfigDir/redis.php";
require "$wmfConfigDir/filebackend.php";
require "$wmfConfigDir/mc.php";
if ( $wmfRealm === 'labs' ) {
	// Beta Cluster overrides
	require "$wmfConfigDir/mc-labs.php";
}
# db-*.php needs $wgDebugDumpSql so should be loaded after logging.php
if ( $wmfRealm === 'labs' ) {
	require "$wmfConfigDir/db-labs.php";
} else {
	require "$wmfConfigDir/db-{$wmfDatacenter}.php";
}

# Override certain settings in command-line mode
# This must be after InitialiseSettings.php is processed (T197475)
if ( PHP_SAPI === 'cli' ) {
	$wgShowExceptionDetails = true;

	# APC not available in CLI mode
	$wgLanguageConverterCacheType = CACHE_NONE;
}

if ( XWikimediaDebug::getInstance()->hasOption( 'readonly' ) ) {
	$wgReadOnly = 'X-Wikimedia-Debug';
}
$wgAllowedCorsHeaders[] = 'X-Wikimedia-Debug';

// In production, read the database loadbalancer config from etcd.
// See https://wikitech.wikimedia.org/wiki/Dbctl
// This must be called after db-{eqiad,codfw}.php has been loaded!
// It overwrites a few sections of $wgLBFactoryConf with data from etcd.
wmfEtcdApplyDBConfig();

// labtestwiki is a one-off test server, using a wmcs-managed database.  Cut
// etcd out of the loop entirely for this one.
$wgLBFactoryConf['sectionLoads']['s11'] = [ 'clouddb2001-dev' => 1 ];
$wgLBFactoryConf['hostsByName']['clouddb2001-dev'] = '10.192.20.11';

// Set $wgProfiler to the value provided by PhpAutoPrepend.php
if ( isset( $wmgProfiler ) ) {
	$wgProfiler = $wmgProfiler;
}

# Disallow web request DB transactions slower than this
$wgMaxUserDBWriteDuration = 3;
# Activate read-only mode for bots when lag is getting high.
# This should be lower than 'max lag' in the LBFactory conf.
$wgAPIMaxLagThreshold = 3;

# Allow different memory_limit settings for Parsoid/PHP servers (T236833)
# keep in sync with php7-fatal-error.php in operations/puppet
# and with wmf-config/logging.php
if ( $wmgServerGroup === 'parsoid' ) {
	ini_set( 'memory_limit', $wmgMemoryLimitParsoid );
} else {
	ini_set( 'memory_limit', $wmgMemoryLimit );
}

# Change calls to wfShellWikiCmd() to use MWScript.php wrapper
$wgHooks['wfShellWikiCmd'][] = 'MWMultiVersion::onWfShellMaintenanceCmd';

setlocale( LC_ALL, 'en_US.UTF-8' );

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

// ParserCache expire time temporarily reduced to 21 days (T280605)
$wgParserCacheExpireTime = 86400 * 21;
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
	switch ( $_SERVER['HTTP_HOST'] ?? '' ) {
		case 'videoscaler.svc.eqiad.wmnet':
		case 'videoscaler.svc.codfw.wmnet':
		case 'videoscaler.discovery.wmnet':
			$wgRequestTimeLimit = 86400;
			break;

		case 'jobrunner.svc.eqiad.wmnet':
		case 'jobrunner.svc.codfw.wmnet':
		case 'jobrunner.discovery.wmnet':
			$wgRequestTimeLimit = 1200;
			break;

		default:
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

# ######################################################################
# Legal matters
# ######################################################################

$wgRightsIcon = '//creativecommons.org/images/public/somerights20.png';

# ######################################################################
# ResourceLoader settings
# ######################################################################

unset( $wgStylePath );

$wgInternalServer = $wgCanonicalServer;
$wgArticlePath = '/wiki/$1';

$wgScriptPath  = '/w';
$wgScript = "{$wgScriptPath}/index.php";
$wgRedirectScript = "{$wgScriptPath}/redirect.php";
$wgLoadScript = "{$wgScriptPath}/load.php";

// Don't include a hostname in $wgResourceBasePath and friends
// - Goes wrong otherwise on mobile web (T106966, T112646)
// - Improves performance by leveraging HTTP/2
// - $wgLocalStylePath MUST be relative
// Apache rewrites /w/resources, /w/extensions, and /w/skins to /w/static.php (T99096)
$wgResourceBasePath = '/w';
$wgExtensionAssetsPath = "{$wgResourceBasePath}/extensions";
$wgStylePath = "{$wgResourceBasePath}/skins";
$wgLocalStylePath = $wgStylePath;

// Deprecated
$wgIncludeLegacyJavaScript = true;

$wgResourceLoaderMaxQueryLength = 5000;

// Cache version key for ResourceLoader client-side module store
// - Bumped to fix breakage due to old /static/$branchName/ urls still
// being cached after the switch to /w/static.php (T134368).
// - Bumped to fix broken SVG embedding being cached (T176884)
$wgResourceLoaderStorageVersion .= '-3';

$wgGitInfoCacheDirectory = "$IP/cache/gitinfo";

// @var string|bool: E-mail address to send notifications to, or false to disable notifications.
$wmgAddWikiNotify = "newprojects@lists.wikimedia.org";

$wgLocalisationCacheConf['storeClass'] = LCStoreCDB::class;
$wgLocalisationCacheConf['storeDirectory'] = "$IP/cache/l10n";
$wgLocalisationCacheConf['manualRecache'] = true;

// Add some useful config data to query=siteinfo
$wgHooks['APIQuerySiteInfoGeneralInfo'][] = static function ( $module, &$data ) {
	global $wmfMasterDatacenter;
	global $wmfEtcdLastModifiedIndex;
	global $wmgCirrusSearchDefaultCluster;
	global $wgCirrusSearchDefaultCluster;
	$data['wmf-config'] = [
		'wmfMasterDatacenter' => $wmfMasterDatacenter,
		'wmfEtcdLastModifiedIndex' => $wmfEtcdLastModifiedIndex,
		'wmgCirrusSearchDefaultCluster' => $wmgCirrusSearchDefaultCluster,
		'wgCirrusSearchDefaultCluster' => $wgCirrusSearchDefaultCluster,
	];
};

$wgEmergencyContact = 'noc@wikipedia.org';

$wgShowIPinHeader = false;
$wgRCMaxAge = 30 * 86400;

$wgTmpDirectory = '/tmp';

# Object cache and session settings

$wgSessionName = $wgDBname . 'Session';

$pcServers = [];
foreach ( $wmfLocalServices['parsercache-dbs'] as $tag => $host ) {
	$pcServers[$tag] = [
		'type' => 'mysql',
		'host' => $host,
		'dbname' => 'parsercache',
		'user' => $wgDBuser,
		'password' => $wgDBpassword,
		'flags' => 0,
	];
}

$wgObjectCaches['mysql-multiwrite'] = [
	'class' => 'MultiWriteBagOStuff',
	'caches' => [
		0 => [
			'factory' => [ 'ObjectCache', 'getInstance' ],
			'args' => [ 'mcrouter-with-onhost-tier' ]
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
	'url' => "{$wmfLocalServices['sessionstore']}/sessions/v1/",
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
	'url' => "{$wmfLocalServices['echostore']}/echoseen/v1/",
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

session_name( $lang . 'wikiSession' );

// Use PBKDF2 for password hashing (T70766)
$wgPasswordDefault = 'pbkdf2';
// This needs to be increased as allowable by server performance
$wgPasswordConfig['pbkdf2'] = [
	'class' => 'Pbkdf2Password',
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
if ( $wgDBname === 'labswiki' || $wgDBname === 'labtestwiki' ) {
	$wgPasswordPolicy['policies']['default']['MinimalPasswordLength'] = [
		'value' => 10,
		'suggestChangeOnLogin' => true,
		'forceChange' => true,
	];
} else {
	foreach ( $wmgPrivilegedGroups as $group ) {
		// On non-SUL wikis this is the effective password policy. On SUL wikis, it will be overridden
		// in the PasswordPoliciesForUser hook, but still needed for Special:PasswordPolicies
		if ( $group === 'user' ) {
			$group = 'default'; // For e.g. private and fishbowl wikis; covers 'user' in password policies
		}
		$wgPasswordPolicy['policies'][$group] = array_merge( $wgPasswordPolicy['policies'][$group] ?? [],
			$wmgPrivilegedPolicy );
	}
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
		return true;
	};
}

if ( $wmgDisableAccountCreation ) {
	$wgGroupPermissions['*']['createaccount'] = false;
	$wgGroupPermissions['*']['autocreateaccount'] = true;
}

# ######################################################################
# Server security settings
# ######################################################################

$wgShellRestrictionMethod = 'firejail';

$wgUseImageMagick               = true;
$wgImageMagickConvertCommand    = '/usr/local/bin/mediawiki-firejail-convert';
$wgSharpenParameter = '0x0.8'; # for IM>6.5, T26857

if ( $wmgUsePagedTiffHandler ) {
	wfLoadExtension( 'PagedTiffHandler' );
	$wgTiffUseTiffinfo = true;
	$wgTiffMaxMetaSize = 1048576;
	if ( $wmgUsePagedTiffHandlerShellbox && $wmfLocalServices['shellbox-media'] ) {
		// Route pagedtiffhandler to the Shellbox named "shellbox-media".
		$wgShellboxUrls['pagedtiffhandler'] = $wmfLocalServices['shellbox-media'];
		// $wgShellboxSecretKey set in PrivateSettings.php
	}
}

$wgMaxImageArea = 10e7; // 100MP
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

$wgStatsdServer = $wmfLocalServices['statsd'];

$wgUseCdn = true;
if ( $wmfRealm === 'production' ) {
	require "$wmfConfigDir/reverse-proxy.php";
} elseif ( $wmfRealm === 'labs' ) {
	$wgStatsdMetricPrefix = 'BetaMediaWiki';
	require "$wmfConfigDir/reverse-proxy-staging.php";
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
		'et.wikimedia.org',
		'et.m.wikimedia.org',
		'fi.wikimedia.org',
		'fi.m.wikimedia.org',
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

// Enable GWToolset's right
$wgAvailableRights[] = 'gwtoolset';

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

// Checkuser
$wgGrantPermissions['checkuser']['checkuser'] = true;
$wgGrantPermissions['checkuser']['checkuser-log'] = true;
// Categorize additional groups defined above.
// Corresponding messages are mwoauth-grant-* in WikimediaMessages.
$wgGrantPermissionGroups['checkuser'] = 'administration';

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

if ( $wmgUseTimeline ) {
	wfLoadExtension( 'timeline' );
	$wgTimelineFontDirectory = '/srv/mediawiki/fonts';
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
	$wgShellboxUrls['easytimeline'] = $wmfLocalServices['shellbox-timeline'];
}

// TODO: This should be handled by LocalServices, not here.
$wgCopyUploadProxy = ( $wmfRealm !== 'labs' ) ? $wmfLocalServices['urldownloader'] : false;
$wgUploadThumbnailRenderHttpCustomHost = $wmgHostnames['upload'];
$wgUploadThumbnailRenderHttpCustomDomain = $wmfLocalServices['upload'];
if ( $wmgUseLocalHTTPProxy ) {
	$wgLocalHTTPProxy = $wmfLocalServices['mwapi'] ?? false;
}

if ( $wmgUseWikiHiero ) {
	wfLoadExtension( 'wikihiero' );
}

wfLoadExtension( 'SiteMatrix' );

// Config for sitematrix
$wgSiteMatrixFile = ( $wmfRealm === 'labs' ) ? "$IP/../langlist-labs" : "$IP/../langlist";

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

$wgSiteMatrixClosedSites = MWWikiversions::readDbListFile( 'closed' );
$wgSiteMatrixPrivateSites = MWWikiversions::readDbListFile( 'private' );
$wgSiteMatrixFishbowlSites = MWWikiversions::readDbListFile( 'fishbowl' );
$wgSiteMatrixNonGlobalSites = MWWikiversions::readDbListFile( 'nonglobal' );

if ( $wmgUseCharInsert ) {
	wfLoadExtension( 'CharInsert' );
}

if ( $wmgUseParserFunctions ) {
	wfLoadExtension( 'ParserFunctions' );
}
$wgExpensiveParserFunctionLimit = 500;

if ( $wmgUseCite ) {
	wfLoadExtension( 'Cite' );
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
	if ( $wmgUseSyntaxHighlightShellbox && $wmfLocalServices['shellbox-syntaxhighlight'] ) {
		// Route syntaxhighlight to the Shellbox named "shellbox-syntaxhighlight".
		$wgShellboxUrls['syntaxhighlight'] = $wmfLocalServices['shellbox-syntaxhighlight'];
		// $wgShellboxSecretKey set in PrivateSettings.php
		$wgPygmentizePath = '/usr/bin/pygmentize';
	}
}

if ( $wmgUseDoubleWiki ) {
	wfLoadExtension( 'DoubleWiki' );
}

if ( $wmgUsePoem ) {
	wfLoadExtension( 'Poem' );
}

// Per-wiki config for Flagged Revisions
if ( $wmgUseFlaggedRevs ) {
	// Include load of extension, and its config.
	include "$wmfConfigDir/flaggedrevs.php";
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
	// overwrite enabling of local TimedText namespace
	$wgEnableLocalTimedText = $wmgEnableLocalTimedText;

	// enable transcoding on all wikis that allow uploads
	$wgEnableTranscode = $wgEnableUploads;

	$wgEnabledTranscodeSet = [
		// Keep the old WebM VP8 flat-file transcodes enabled
		// while we transition, or playback of un-upgraded files
		// will revert to original high-res sources.
		// Disable them again once T200747 batch process complete.
		'160p.webm' => true,
		'240p.webm' => true,
		'360p.webm' => true,
		'480p.webm' => true,
		'720p.webm' => true,
		'1080p.webm' => true,

		// Enable the WebM VP9 flat-file transcodes
		'120p.vp9.webm' => true,
		'180p.vp9.webm' => true,
		'240p.vp9.webm' => true,
		'360p.vp9.webm' => true,
		'480p.vp9.webm' => true,
		'720p.vp9.webm' => true,
		'1080p.vp9.webm' => true,
		'1440p.vp9.webm' => true,
		'2160p.vp9.webm' => true,
	];

	$wgOggThumbLocation = false; // use ffmpeg for performance

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

	// The type of HTML5 player to use
	$wgTmhWebPlayer = $wmgTmhWebPlayer;
}

if ( $wmgUseUploadsLink ) {
	wfLoadExtension( 'UploadsLink' );
}

if ( $wmgUseUrlShortener ) {
	wfLoadExtension( 'UrlShortener' );
	$wgUrlShortenerTemplate = '/$1';
	$wgUrlShortenerServer = 'https://w.wiki';
	$wgUrlShortenerDBCluster = 'extension1';
	$wgUrlShortenerDBName = 'wikishared';
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
		'*.mediawiki.org',
	];
	$wgUrlShortenerEnableSidebar = false;
	$wgGroupPermissions['sysop']['urlshortener-manage-url'] = false;
	$wgGroupPermissions['sysop']['urlshortener-view-log'] = false;

	// Never ever change this config
	// Changing it would change target of all short urls
	$wgUrlShortenerIdSet = '23456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz$';
}

if ( $wmgPFEnableStringFunctions ) {
	$wgPFEnableStringFunctions = true;
}

if ( $wgDBname === 'mediawikiwiki' ) {
	wfLoadExtension( 'ExtensionDistributor' );
	$wgExtDistAPIConfig = [
		'class' => 'GerritExtDistProvider',
		'apiUrl' => 'https://gerrit.wikimedia.org/r/projects/mediawiki%2F$TYPE%2F$EXT/branches',
		'tarballUrl' => 'https://extdist.wmflabs.org/dist/$TYPE/$EXT-$REF-$SHA.tar.gz',
		'tarballName' => '$EXT-$REF-$SHA.tar.gz',
		'repoListUrl' => 'https://gerrit.wikimedia.org/r/projects/?b=master&p=mediawiki/$TYPE/',
		'sourceUrl' => 'https://gerrit.wikimedia.org/r/mediawiki/$TYPE/$EXT.git',
	];

	// Current stable release
	$wgExtDistDefaultSnapshot = 'REL1_37';

	// Current development snapshot
	// $wgExtDistCandidateSnapshot = 'REL1_38';

	// Available snapshots
	$wgExtDistSnapshotRefs = [
		'master',
		'REL1_37',
		'REL1_36',
		'REL1_35',
	];

	// Use Graphite for popular list
	$wgExtDistGraphiteRenderApi = 'https://graphite.wikimedia.org/render';
}

if ( $wmgUseGlobalBlocking ) {
	wfLoadExtension( 'GlobalBlocking' );
	$wgGlobalBlockingDatabase = 'centralauth';
	$wgApplyGlobalBlocks = $wmgApplyGlobalBlocks;
	$wgGlobalBlockingBlockXFF = true; // Apply blocks to IPs in XFF (T25343)
}

wfLoadExtension( 'TrustedXFF' );
$wgTrustedXffFile = "$wmfConfigDir/trusted-xff.php";

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
		include "$wmfConfigDir/MetaContactPages.php";
		$wgContactConfig['stewards'] = [ // T98625
			'RecipientUser' => 'Wikimedia Stewards',
			'SenderEmail' => $wmgNotificationSender,
			'RequireDetails' => true,
			'IncludeIP' => true,
			'AdditionalFields' => [
				'Text' => [
					'label-message' => 'emailmessage',
					'type' => 'textarea',
					'rows' => 20,
					'required' => true
				],
				'Disclaimer' => [
					'label-message' => 'contactpage-stewards-disclaimer-label',
					'type' => 'info'
				]
			]
		];
	}
}

if ( $wmgUseSecurePoll ) {
	wfLoadExtension( 'SecurePoll' );

	$wgSecurePollUseNamespace = $wmgSecurePollUseNamespace;
	$wgSecurePollScript = 'auth-api.php';
	$wgHooks['SecurePoll_JumpUrl'][] = static function ( $page, &$url ) use ( $site, $lang ) {
		$url = wfAppendQuery( $url, [ 'site' => $site, 'lang' => $lang ] );
		return true;
	};
	$wgSecurePollCreateWikiGroups = [
		'securepollglobal' => 'securepoll-dblist-securepollglobal'
	];
	// T173393 - This is number of days after the election ends, not
	// number of days after the vote was cast. Lower to 60 days so that
	// overall time retained is not > 90 days.
	$wgSecurePollKeepPrivateInfoDays = 60;

	// T209802 - gpg2 is untested and evidently broken
	$wgSecurePollGPGCommand = 'gpg1';

	if ( wfHostName() === 'mwmaint1002' ) {
		$wgSecurePollShowErrorDetail = true;
	}
}

// PoolCounter
if ( $wmgUsePoolCounter ) {
	include "$wmfConfigDir/PoolCounterSettings.php";
}

if ( $wmgUseSecureLinkFixer ) {
	wfLoadExtension( 'SecureLinkFixer' );
}

if ( $wmgUseScore ) {
	wfLoadExtension( 'Score' );
	$wgScoreSafeMode = true;

	if ( $wmgUseScoreShellbox && $wmfLocalServices['shellbox'] ) {
		// Route score to the Shellbox named "shellbox".
		$wgShellboxUrls['score'] = $wmfLocalServices['shellbox'];
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

# Default address gets rejected by some mail hosts
$wgPasswordSender = 'wiki@wikimedia.org';

# e-mailing password based on e-mail address (T36386)
$wgPasswordResetRoutes['email'] = true;

if ( $wgDBname === 'labswiki' || $wgDBname === 'labtestwiki' ) {
	$wgUseInstantCommons = true;
}

if ( $wgDBname === 'nostalgiawiki' ) {
	# Link back to current version from the archive funhouse
	if ( ( isset( $_REQUEST['title'] ) && ( $title = $_REQUEST['title'] ) )
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

$wgFooterIcons['copyright']['copyright'] = '<a href="https://wikimediafoundation.org/">' .
	'<img src="' . $wmgWikimediaIcon['1x'] . '" ' .
		'srcset="' .
			$wmgWikimediaIcon['1.5x'] . ' 1.5x, ' .
			$wmgWikimediaIcon['2x'] . ' 2x' .
		'" ' .
		'width="88" height="31" alt="Wikimedia Foundation" loading="lazy" /></a>';

# :SEARCH:

# All wikis are special and get Cirrus :)
# Must come *AFTER* PoolCounterSettings.php
wfLoadExtension( 'Elastica' );
wfLoadExtension( 'CirrusSearch' );
include "$wmfConfigDir/CirrusSearch-common.php";

$wgInvalidateCacheOnLocalSettingsChange = false;

$wgEnableUserEmail = true;
$wgNoFollowLinks = true; // In case the MediaWiki default changed, T44594

# XFF log for incident response
$wgExtensionFunctions[] = static function () {
	if (
		isset( $_SERVER['REQUEST_METHOD'] )
		&& $_SERVER['REQUEST_METHOD'] === 'POST'
		// T129982
		&& $_SERVER['HTTP_HOST'] !== 'jobrunner.discovery.wmnet'
	) {
		$uri = ( ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] ) ? 'https://' : 'http://' ) .
			$_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

		$logger = LoggerFactory::getInstance( 'xff' );
		$logger->info( "{date}\t{uri}\t{xff}, {remoteaddr}\t{wpSave}",
			[
				'date' => gmdate( 'r' ),
				'uri' => $uri,
				'xff' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '',
				'remoteaddr' => $_SERVER['REMOTE_ADDR'],
				'wpSave' => ( isset( $_REQUEST['wpSave'] ) && $_REQUEST['wpSave'] ) ? 'save' : '',
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
		// T280886 wm-codeofconduct-url currently points to
		// https://meta.wikimedia.org/wiki/Special:MyLanguage/Universal_Code_of_Conduct
		// but may need updating to point to
		// https://foundation.wikimedia.org/wiki/Special:MyLanguage/Universal_Code_of_Conduct
		return;
		// $urlKey = 'wm-codeofconduct-url';
		// $msgKey = 'wm-codeofconduct';
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

wfLoadExtension( 'Renameuser' );
$wgGroupPermissions['bureaucrat']['renameuser'] = $wmgAllowLocalRenameuser;

if ( $wmgUseSpecialNuke ) {
	wfLoadExtension( 'Nuke' );
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

	$wgGroupPermissions['sysop'] = array_merge(
		$wgGroupPermissions['sysop'],
		[
			'editinterface' => false,
		]
	);
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

if ( $wmfRealm == 'labs' ) {
	$wgHTTPTimeout = 10;
}
if ( $wgRequestTimeLimit ) {
	// Set the maximum HTTP client timeout equal to the current request timeout (T245170)
	$wgHTTPMaxTimeout = $wgHTTPMaxConnectTimeout = $wgRequestTimeLimit;
}

// TODO: This is a no-op with $wgForceHTTPS enabled
// Remove once enabled everywhere. – T256095
$wgHiddenPrefs[] = 'prefershttps'; // T91352, T102245

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
	$wgCaptchaStorageClass = 'CaptchaCacheStore';
	$wgCaptchaClass = 'FancyCaptcha';
	$wgCaptchaWhitelist =
		'#^(https?:)?//([.a-z0-9-]+\\.)?((wikimedia|wikipedia|wiktionary|wikiquote|wikibooks|wikisource|wikispecies|mediawiki|wikinews|wikiversity|wikivoyage|wikidata|wmflabs)\.org'
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

	if ( $wgDBname === 'labswiki' || $wgDBname === 'labtestwiki' ) {
		$wgCaptchaTriggers['addurl'] = false;
	}

	# akosiaris 20180306. contact pages in metawiki are being abused by bots
	if ( $wgDBname === 'metawiki' ) {
		$wgCaptchaTriggers['contactpage'] = true;
	}
}

if ( extension_loaded( 'wikidiff2' ) ) {
	$wgDiff = false;
}

if ( $wmfRealm === 'labs' ) {
	$wgInterwikiCache = require "$wmfConfigDir/interwiki-labs.php";
} else {
	$wgInterwikiCache = require "$wmfConfigDir/interwiki.php";
}

// Username spoofing / mixed-script / similarity check detection
wfLoadExtension( 'AntiSpoof' );

// For transwiki import
ini_set( 'user_agent', 'Wikimedia internal server fetcher (noc@wikimedia.org' );
$wgHTTPImportTimeout = 50; // T155209

// CentralAuth
if ( $wmgUseCentralAuth ) {
	wfLoadExtension( 'CentralAuth' );

	// Enable cross-origin session cookies (T252236).
	if ( $wmgEnableCrossOriginSessions ) {
		$wgCookieSameSite = 'None';
		$wgUseSameSiteLegacyCookies = true;
	}

	$wgCentralAuthDryRun = false;
	$wgCentralAuthCookies = true;

	$wgCentralAuthUseEventLogging = true;
	$wgCentralAuthPreventUnattached = true;

	// Optimisation: Avoid overhead of computing localised urls
	// See https://phabricator.wikimedia.org/T189966#5436482
	$wgCentralAuthCookiesP3P = "CP=\"See $wgCanonicalServer/wiki/Special:CentralAutoLogin/P3P for more info.\"";

	foreach ( $wmfLocalServices['irc'] as $address ) {
		$wgCentralAuthRC[] = [
			'formatter' => 'IRCColourfulCARCFeedFormatter',
			'uri' => "udp://$address:$wmgRC2UDPPort/#central\t",
		];
	}

	switch ( $wmfRealm ) {
	case 'production':
		// Production cluster
		$wmgSecondLevelDomainRegex = '/^\w+\.\w+\./';
		$wgCentralAuthAutoLoginWikis = $wmgCentralAuthAutoLoginWikis;
		$wgCentralAuthLoginWiki = 'loginwiki';
		break;

	case 'labs':
		// wmflabs beta cluster
		$wmgSecondLevelDomainRegex = '/^\w+\.\w+\.\w+\.\w+\./';
		$wgCentralAuthAutoLoginWikis = [
			'.wikipedia.beta.wmflabs.org' => 'enwiki',
			'.wikisource.beta.wmflabs.org' => 'enwikisource',
			'.wikibooks.beta.wmflabs.org' => 'enwikibooks',
			'.wikiversity.beta.wmflabs.org' => 'enwikiversity',
			'.wikiquote.beta.wmflabs.org' => 'enwikiquote',
			'.wikinews.beta.wmflabs.org' => 'enwikinews',
			'.wikivoyage.beta.wmflabs.org' => 'enwikivoyage',
			'.wiktionary.beta.wmflabs.org' => 'enwiktionary',
			'meta.wikimedia.beta.wmflabs.org' => 'metawiki',
			'deployment.wikimedia.beta.wmflabs.org' => 'deploymentwiki',
			'commons.wikimedia.beta.wmflabs.org' => 'commonswiki',
			$wmgHostnames['wikidata'] => 'wikidatawiki',
			'api.wikimedia.beta.wmflabs.org' => 'apiportalwiki',
			$wmgHostnames['wikifunctions'] => 'wikifunctionswiki',
		];
		$wgCentralAuthLoginWiki = 'loginwiki';
		break;
	}

	if ( preg_match( $wmgSecondLevelDomainRegex, strrev( $wgServer ), $m ) ) {
		$wmgSecondLevelDomain = strrev( $m[0] );
	} else {
		$wmgSecondLevelDomain = false;
	}
	unset( $wmgSecondLevelDomainRegex );

	# Don't autologin to self
	if ( isset( $wgCentralAuthAutoLoginWikis[$wmgSecondLevelDomain] ) ) {
		unset( $wgCentralAuthAutoLoginWikis[$wmgSecondLevelDomain] );
		$wgCentralAuthCookieDomain = $wmgSecondLevelDomain;
	} elseif ( $wgDBname === 'commonswiki' && isset( $wgCentralAuthAutoLoginWikis["commons$wmgSecondLevelDomain"] ) ) {
		unset( $wgCentralAuthAutoLoginWikis["commons$wmgSecondLevelDomain"] );
		$wgCentralAuthCookieDomain = "commons$wmgSecondLevelDomain";
	} elseif ( $wgDBname === 'metawiki' ) {
		unset( $wgCentralAuthAutoLoginWikis["meta$wmgSecondLevelDomain"] );
		$wgCentralAuthCookieDomain = "meta$wmgSecondLevelDomain";
	} else {
		# Don't set 2nd-level cookies for *.wikimedia.org, insecure
		$wgCentralAuthCookieDomain = '';
	}
	$wgCentralAuthLoginIcon = $wmgCentralAuthLoginIcon;
	$wgCentralAuthAutoNew = true;

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

	// Let's give it another try
	$wgCentralAuthCreateOnView = true;

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
	if ( isset( $wgAuthManagerAutoConfig['primaryauth'][\MediaWiki\Auth\LocalPasswordPrimaryAuthenticationProvider::class] ) ) {
		$wgAuthManagerAutoConfig['primaryauth'][\MediaWiki\Auth\LocalPasswordPrimaryAuthenticationProvider::class]['args'][0]['loginOnly'] = true;
	}
}

if ( $wmgUseApiFeatureUsage ) {
	wfLoadExtension( 'ApiFeatureUsage' );
	$wgApiFeatureUsageQueryEngineConf = [
		'class' => 'ApiFeatureUsageQueryEngineElastica',
		'serverList' => $wmfLocalServices['search-chi'],
	];
}

// taking it live 2006-12-15 brion
wfLoadExtension( 'DismissableSiteNotice' );
$wgDismissableSiteNoticeForAnons = true; // T59732
$wgMajorSiteNoticeID = '2';

/**
 * Get an array of groups (in $wmgPrivilegedGroups) that $username is part of
 *
 * @param UserIdentity $user
 * @return array Any elevated/privileged groups the user is a member of
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

// log suspicious or sensitive login attempts
$wgHooks['AuthManagerLoginAuthenticateAudit'][] = static function ( $response, $user, $username ) {
	$guessed = false;
	if ( !$user && $username ) {
		$user = MediaWikiServices::getInstance()
			->getUserIdentityLookup()
			->getUserIdentityByName( $username );
		$guessed = true;
	}
	if ( !$user || !in_array( $response->status,
		[ AuthenticationResponse::PASS, AuthenticationResponse::FAIL ], true )
	) {
		return;
	}

	global $wgRequest;
	$headers = function_exists( 'apache_request_headers' ) ? apache_request_headers() : [];
	$successful = $response->status === AuthenticationResponse::PASS;
	$privGroups = wmfGetPrivilegedGroups( $user );

	$channel = $successful ? 'goodpass' : 'badpass';
	if ( $privGroups ) {
		$channel .= '-priv';
	}
	$logger = LoggerFactory::getInstance( $channel );
	$verb = $successful ? 'succeeded' : 'failed';

	$logger->info( "Login $verb for {priv} {name} from {clientip} - {xff} - {ua} - {geocookie}: {messagestr}", [
		'successful' => $successful,
		'groups' => implode( ', ', $privGroups ),
		'priv' => ( $privGroups ? 'elevated' : 'normal' ),
		'name' => $user->getName(),
		'clientip' => $wgRequest->getIP(),
		'xff' => @$headers['X-Forwarded-For'],
		'ua' => @$headers['User-Agent'],
		'guessed' => $guessed,
		'msgname' => $response->message ? $response->message->getKey() : '-',
		'messagestr' => $response->message ? $response->message->inLanguage( 'en' )->text() : '',
		'geocookie' => $wgRequest->getCookie( 'GeoIP', '' ),
	] );
};

// log sysop password changes
$wgHooks['ChangeAuthenticationDataAudit'][] = static function ( $req, $status ) {
	global $wgRequest;
	$user = MediaWikiServices::getInstance()
		->getUserIdentityLookup()
		->getUserIdentityByName( $req->username );
	$status = Status::wrap( $status );
	if ( $req instanceof \MediaWiki\Auth\PasswordAuthenticationRequest ) {
		$privGroups = wmfGetPrivilegedGroups( $user );
		$priv = ( $privGroups ? 'elevated' : 'normal' );
		if ( $priv === 'elevated' ) {
			$headers = function_exists( 'apache_request_headers' ) ? apache_request_headers() : [];

			$logger = LoggerFactory::getInstance( 'badpass' );
			$logger->info( 'Password change in prefs for {priv} {name}: {status} - {clientip} - {xff} - {ua} - {geocookie}', [
				'name' => $user->getName(),
				'groups' => implode( ', ', $privGroups ),
				'priv' => $priv,
				'status' => $status->isGood() ? 'ok' : $status->getWikiText( null, null, 'en' ),
				'clientip' => $wgRequest->getIP(),
				'xff' => @$headers['X-Forwarded-For'],
				'ua' => @$headers['User-Agent'],
				'geocookie' => $wgRequest->getCookie( 'GeoIP', '' ),
			] );
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

switch ( $wmfRealm ) {
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
	if ( $wmfRealm === 'production' && $wgDBname === 'testwiki' ) {
		$wgCentralSelectedBannerDispatcher = "//test.wikipedia.org/w/index.php?title=Special:BannerLoader";

		// No caching for banners on testwiki, so we can develop them there a bit faster - NeilK 2012-01-16
		// Never set this to zero on a highly trafficked wiki, there are server-melting consequences
		$wgNoticeBannerMaxAge = 0;
	} else {
		$wgCentralSelectedBannerDispatcher = "//{$wmgHostnames['meta']}/w/index.php?title=Special:BannerLoader";
	}
	// Relative URL which is hardcoded to HTTP 204 in Varnish config.
	$wgCentralBannerRecorder = "{$wgServer}/beacon/impression";

	// Allow only these domains to access CentralNotice data through the reporter
	$wgNoticeReporterDomains = 'https://donate.wikimedia.org';

	$wgCentralDBname = 'metawiki';
	$wgNoticeInfrastructure = false;
	$wgCentralNoticeAdminGroup = false;
	if ( $wmfRealm == 'production' && $wgDBname === 'testwiki' ) {
		// test.wikipedia.org has its own central database:
		$wgCentralDBname = 'testwiki';
		$wgNoticeInfrastructure = true;
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

	// Set fundraising banners to use HTTPS on foundation wiki
	$wgNoticeFundraisingUrl = 'https://donate.wikimedia.org/wiki/Special:LandingCheck';

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
	// www.pages04.net is used by Wikimedia Fundraising to enable 'remind me later' banner functionality, which submits email addresses to our email campaign vendor
	$wgCentralNoticeContentSecurityPolicy = "script-src 'unsafe-eval' blob: 'self' meta.wikimedia.org *.wikimedia.org *.wikipedia.org *.wikinews.org *.wiktionary.org *.wikibooks.org *.wikiversity.org *.wikisource.org wikisource.org *.wikiquote.org *.wikidata.org *.wikivoyage.org *.mediawiki.org 'unsafe-inline'; default-src 'self' data: blob: upload.wikimedia.org https://commons.wikimedia.org meta.wikimedia.org *.wikimedia.org *.wikipedia.org *.wikinews.org *.wiktionary.org *.wikibooks.org *.wikiversity.org *.wikisource.org wikisource.org *.wikiquote.org *.wikidata.org *.wikivoyage.org *.mediawiki.org wikimedia.org www.pages04.net; style-src 'self' data: blob: upload.wikimedia.org https://commons.wikimedia.org meta.wikimedia.org *.wikimedia.org *.wikipedia.org *.wikinews.org *.wiktionary.org *.wikibooks.org *.wikiversity.org *.wikisource.org wikisource.org *.wikiquote.org *.wikidata.org *.wikivoyage.org *.mediawiki.org wikimedia.org 'unsafe-inline';";
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
		return true;
	};
}

if ( $wgDBname === 'enwiki' ) {
	// T59569, T105118
	//
	// If it's an anonymous user creating a page in the English Wikipedia
	// Draft namespace, tell TitleQuickPermissions to abort the normal
	// checkQuickPermissions checks.  This lets anonymous users create a page in this
	// namespace, even though they don't have the general 'createpage' right.
	//
	// It does not affect other checks from getUserPermissionsErrorsInternal
	// (e.g. protection and blocking).
	//
	// This used to apply to Persian Wikipedia as well but that was disabled in T291018
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
		'zip_post' => "{$wmfLocalServices['urldownloader']}|https://pediapress.com/wmfup/",
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

wfLoadExtension( 'AdvancedSearch' );

# Various system to allow/prevent flooding
# (including exemptions for scheduled outreach events)
require "$wmfConfigDir/throttle.php";
require "$wmfConfigDir/throttle-analyze.php";

if ( $wmgUseNewUserMessage ) {
	wfLoadExtension( 'NewUserMessage' );
	$wgNewUserSuppressRC = true;
	$wgNewUserMinorEdit = $wmgNewUserMinorEdit;
	$wgNewUserMessageOnAutoCreate = $wmgNewUserMessageOnAutoCreate;
}

if ( $wmgUseCodeReview ) {
	wfLoadExtension( 'CodeReview' );

	$wgGroupPermissions['user']['codereview-add-tag'] = false;
	$wgGroupPermissions['user']['codereview-remove-tag'] = false;
	$wgGroupPermissions['user']['codereview-post-comment'] = false;
	$wgGroupPermissions['user']['codereview-set-status'] = false;
	$wgGroupPermissions['user']['codereview-link-user'] = false;
	$wgGroupPermissions['user']['codereview-signoff'] = false;
	$wgGroupPermissions['user']['codereview-associate'] = false;

	$wgCodeReviewRepoStatsCacheTime = 24 * 60 * 60;
	$wgCodeReviewMaxDiffPaths = 100;

	// Delist the deprecated special page.
	$wgCodeReviewListSpecialPage = false;
}

# AbuseFilter
wfLoadExtension( 'AbuseFilter' );
include "$wmfConfigDir/abusefilter.php";
if ( $wmgUseGlobalAbuseFilters ) {
	$wgAbuseFilterCentralDB = $wmgAbuseFilterCentralDB;
	$wgAbuseFilterIsCentral = ( $wgDBname === $wgAbuseFilterCentralDB );
}

# PdfHandler
if ( $wmgUsePdfHandler ) {
	wfLoadExtension( 'PdfHandler' );
	if ( $wmgUsePdfHandlerShellbox && $wmfLocalServices['shellbox-media'] ) {
		// Route pdfhandler to the Shellbox named "shellbox-media".
		$wgShellboxUrls['pdfhandler'] = $wmfLocalServices['shellbox-media'];
		// $wgShellboxSecretKey set in PrivateSettings.php
	} else {
		$wgPdfProcessor = '/usr/local/bin/mediawiki-firejail-ghostscript';
		$wgPdfPostProcessor = '/usr/local/bin/mediawiki-firejail-convert';
	}
}

# WikiEditor
wfLoadExtension( 'WikiEditor' );
$wgDefaultUserOptions['usebetatoolbar'] = 1;

# LocalisationUpdate
# wfLoadExtension( 'LocalisationUpdate' );
$wgLocalisationUpdateDirectory = "/var/lib/l10nupdate/caches/cache-$wmgVersionNumber";
$wgLocalisationUpdateRepository = 'local';
$wgLocalisationUpdateRepositories['local'] = [
	'mediawiki' => '/var/lib/l10nupdate/mediawiki/core/%PATH%',
	'extension' => '/var/lib/l10nupdate/mediawiki/extensions/%NAME%/%PATH%',
	'skin' => '/var/lib/l10nupdate/mediawiki/skins/%NAME%/%PATH%',
];

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
	require_once "$wmfConfigDir/liquidthreads.php";
}

if ( $wmgUseGlobalUsage ) {
	wfLoadExtension( 'GlobalUsage' );
	$wgGlobalUsageDatabase = 'commonswiki';
	$wgGlobalUsageSharedRepoWiki = 'commonswiki';
	$wgGlobalUsagePurgeBacklinks = true;
}

if ( $wmgUseLivePreview ) {
	$wgDefaultUserOptions['uselivepreview'] = 1;
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
$wgDefaultUserOptions['showhiddencats'] = $wmgShowHiddenCats;

$wgDefaultUserOptions['watchcreations'] = true;

// Temporary override: WMF is not hardcore enough to enable this.
// See T37785, T38316, T47022 about it.
if ( $wmgWatchlistDefault ) {
	$wgDefaultUserOptions['watchdefault'] = 1;
} else {
	$wgDefaultUserOptions['watchdefault'] = 0;
}

if ( $wmgWatchMoves ) {
	$wgDefaultUserOptions['watchmoves'] = 1;
} else {
	$wgDefaultUserOptions['watchmoves'] = 0;
}

if ( $wmgWatchRollback ) {
	$wgDefaultUserOptions['watchrollback'] = 1;
} else {
	$wgDefaultUserOptions['watchrollback'] = 0;
}

$wgDefaultUserOptions['enotifminoredits'] = $wmgEnotifMinorEditsUserDefault;
$wgDefaultUserOptions['enotifwatchlistpages'] = 0;

if ( $wmgEnhancedRecentChanges ) {
	$wgDefaultUserOptions['usenewrc'] = 1;
} else {
	$wgDefaultUserOptions['usenewrc'] = 0;
}

if ( $wmgEnhancedWatchlist ) {
	$wgDefaultUserOptions['extendwatchlist'] = 1;
} else {
	$wgDefaultUserOptions['extendwatchlist'] = 0;
}

if ( $wmgForceEditSummary ) {
	$wgDefaultUserOptions['forceeditsummary'] = 1;
} else {
	$wgDefaultUserOptions['forceeditsummary'] = 0;
}

if ( $wmgShowWikidataInWatchlist ) {
	$wgDefaultUserOptions['wlshowwikibase'] = 1;
} else {
	$wgDefaultUserOptions['wlshowwikibase'] = 0;
}

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
		'licensing' => [
			'thirdParty' => [
				'licenseGroups' => [
					[
						// This should be a list of all CC licenses we can reasonably expect to find around the web
						'head' => 'mwe-upwiz-license-cc-head',
						'subhead' => 'mwe-upwiz-license-cc-subhead',
						'licenses' => [
							'cc-by-sa-4.0',
							'cc-by-sa-3.0',
							'cc-by-sa-2.5',
							'cc-by-4.0',
							'cc-by-3.0',
							'cc-by-2.5',
							'cc-zero'
						]
					],
					[
						// n.b. as of April 2011, Flickr still uses CC 2.0 licenses.
						// The White House also has an account there, hence the Public Domain US Government license
						'head' => 'mwe-upwiz-license-flickr-head',
						'subhead' => 'mwe-upwiz-license-flickr-subhead',
						'prependTemplates' => [ 'flickrreview' ],
						'licenses' => [
							'cc-by-sa-2.0',
							'cc-by-2.0',
							'pd-usgov',
						]
					],
					[
						'head' => 'mwe-upwiz-license-public-domain-usa-head',
						'subhead' => 'mwe-upwiz-license-public-domain-usa-subhead',
						'licenses' => [
							'pd-us',
							'pd-old-70-expired',
							'pd-art',
						]
					],
					[
						'head' => 'mwe-upwiz-license-usgov-head',
						'licenses' => [
							'pd-usgov',
							'pd-usgov-nasa'
						]
					],
					[
						'head' => 'mwe-upwiz-license-custom-head',
						'special' => 'custom',
						'licenses' => [ 'custom' ],
					],
					[
						'head' => 'mwe-upwiz-license-none-head',
						'licenses' => [ 'none' ]
					],
				],
			],
		],
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
		'comment' => 'Cross-wiki upload from $HOST',
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

if ( $wmgUseGWToolset ) {
	wfLoadExtension( 'GWToolset' );
	$wgGWTFileBackend = 'local-multiwrite';

	// extra throttling until the image scalers are more robust
	$wgGWToolsetConfigOverrides['mediafile_job_throttle_default'] = 5; // 5 files per batch
	$wgJobBackoffThrottling['gwtoolsetUploadMetadataJob'] = 5 / 3600; // 5 batches per hour
}

if ( $wmgUseMultimediaViewer ) {
	wfLoadExtension( 'MultimediaViewer' );
}

if ( $wmgUsePopups ) {
	wfLoadExtension( 'Popups' );

	if ( $wmgPopupsReferencePreviews ) {
		$wgPopupsReferencePreviews = true;
		$wgPopupsReferencePreviewsBetaFeature = false;
	}
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
		'url' => $wmfLocalServices['restbase'],
		'domain' => $wgCanonicalServer,
		'forwardCookies' => false,
		'parsoidCompat' => false
	];
}

if ( $wmgUseParsoid ) {
	$wmgParsoidURL = $wmfLocalServices['parsoid'];

	$wgVirtualRestConfig['modules']['parsoid'] = [
		'url' => $wmgParsoidURL,
		'prefix' => $wgDBname, // The wiki prefix to use; deprecated
		'domain' => $wgCanonicalServer,
		'forwardCookies' => $wmgParsoidForwardCookies,
		'restbaseCompat' => false
	];
}

if ( $wmgUseCollection ) {
	$wgVirtualRestConfig['modules']['electron'] = [
		'url' => $wmfLocalServices['electron'],
		'options' => [
			'accessKey' => $wmgElectronSecret, // set in private repo
		],
	];
}

if ( $wmgUseVisualEditor ) {
	wfLoadExtension( 'VisualEditor' );

	// RESTBase connection configuration is done by $wmfUseRestbaseVRS above.
	// Parsoid connection configuration is done by $wmgUseParsoid above.
	// At least one of these should be set if you want to use Visual Editor.
	$wgVisualEditorParsoidAutoConfig = false;

	// RESTBase connection configuration
	if ( $wmgVisualEditorAccessRestbaseDirectly ) {
		$wgVisualEditorRestbaseURL = "/api/rest_v1/page/html/";
		$wgVisualEditorFullRestbaseURL = "/api/rest_";
		// There should be no lossy switching in RESTBase mode, but just in case
		$wgVisualEditorAllowLossySwitching = false;
	} else {
		if ( $wgDBname === 'labswiki' || $wgDBname === 'labtestwiki' ) {
			// (T241961) Just for wikitech, load the Parsoid extension and
			// use VisualEditor auto-config. (Yes, this is a bit of a hack,
			// and it exposes Parsoid's REST API, but ensures that Parsoid
			// 'extension' code is kept in sync with core.)
			wfLoadExtension( 'Parsoid', "$IP/vendor/wikimedia/parsoid/extension.json" );
			// Note that wgVirtualRestConfig['modules']['parsoid'] isn't set as wmgUseParsoid is false
			$wgVisualEditorParsoidAutoConfig = true;
		}
		// Prefer lossy switching (dirty diffs) to not being able to switch editors
		$wgVisualEditorAllowLossySwitching = true;
	}

	// Tab configuration
	if ( $wmgVisualEditorUseSingleEditTab ) {
		$wgVisualEditorUseSingleEditTab = true;
		$wgVisualEditorSingleEditTabSwitchTime = $wmgVisualEditorSingleEditTabSwitchTime;
	}
	if ( $wmgVisualEditorIsSecondaryEditor ) {
		if ( !$wmgVisualEditorUseSingleEditTab ) {
			$wgVisualEditorTabPosition = 'after';
		}
		$wgDefaultUserOptions['visualeditor-editor'] = 'wikitext';
	} else {
		$wgDefaultUserOptions['visualeditor-editor'] = 'visualeditor';
	}
	if ( $wmgVisualEditorEnableWikitext ) {
		$wgVisualEditorEnableWikitext = true;
		$wgDefaultUserOptions['visualeditor-newwikitext'] = true;
	}

	if ( $wmgVisualEditorEnableVisualSectionEditing ) {
		$wgVisualEditorEnableVisualSectionEditing = $wmgVisualEditorEnableVisualSectionEditing;
	}

	// Namespace configuration
	$wgVisualEditorAvailableNamespaces = $wmgVisualEditorAvailableNamespaces;
	if ( !isset( $wgVisualEditorAvailableNamespaces ) ) {
		$wgVisualEditorAvailableNamespaces = []; // Set null to be an empty array to avoid fatals
	}

	// User access configuration
	if ( $wmgVisualEditorDefault ) {
		$wgDefaultUserOptions['visualeditor-enable'] = 1;
	} else {
		$wgDefaultUserOptions['visualeditor-enable'] = 0;
		$wgVisualEditorEnableBetaFeature = true;
	}
	if ( $wmgVisualEditorTransitionDefault ) {
		$wgVisualEditorTransitionDefault = true;
	}
	if ( $wmgVisualEditorAllowExternalLinkPaste ) {
		$wgVisualEditorAllowExternalLinkPaste = true;
	}
	// T52000 - to remove once roll-out is complete.
	if ( $wmgVisualEditorDisableForAnons ) {
		$wgVisualEditorDisableForAnons = true;
	}

	// Feedback configuration
	if ( $wmgVisualEditorConsolidateFeedback ) {
		$wgVisualEditorFeedbackAPIURL = 'https://www.mediawiki.org/w/api.php';
		$wgVisualEditorFeedbackTitle = 'VisualEditor/Feedback';
		$wgVisualEditorSourceFeedbackTitle = '2017 wikitext editor/Feedback';
	}

	// Citoid
	wfLoadExtension( 'Citoid' );

	// Move the citation button from the primary toolbar into the "other" group
	if ( $wmgCiteVisualEditorOtherGroup ) {
		$wgCiteVisualEditorOtherGroup = true;
	}

	// Enable the wikitext mode Beta Feature for opt-in
	$wgVisualEditorEnableWikitextBetaFeature = true;

	// Enable the diff page visual diff Beta Feature for opt-in
	$wgVisualEditorEnableDiffPageBetaFeature = true;

	if ( $wmgVisualEditorSuggestedValues ) {
		$wgVisualEditorTransclusionDialogSuggestedValues = true;
	}

	// Temporary feature flags for changes to the descriptions in the transclusion
	// dialog, see T271800 and T286765.  Null coalesce is to prevent transient
	// failure during initial config flag deployment.
	if ( $wmgVisualEditorTransclusionDialogNewSidebarFeatures ?? false ) {
		$wgVisualEditorTransclusionDialogInlineDescriptions = true;
		$wgVisualEditorTransclusionDialogNewSidebar = true;
	}
}

if ( $wmgUseTemplateData ) { // T61702 - 2015-07-20
	// TemplateData enabled for all wikis - 2014-09-29
	wfLoadExtension( 'TemplateData' );
	// TemplateData GUI enabled for all wikis - 2014-11-06
	$wgTemplateDataUseGUI = true;

	// TemplateWizard enabled for all TemplateData wikis – T202545
	wfLoadExtension( 'TemplateWizard' );

	if ( $wmgTemplateDataSuggestedValues ) {
		$wgTemplateDataSuggestedValuesEditor = true;
	}
}

if ( $wmgTemplateSearchImprovements ) {
	$wgVisualEditorTemplateSearchImprovements = true;
	$wgTemplateWizardTemplateSearchImprovements = true;
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
	$wmincClosedWikis = $wgSiteMatrixClosedSites;
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

# Mobile related configuration
wfLoadExtension( 'MobileFrontend' );
wfLoadSkin( 'MinervaNeue' );

$wgMFMobileHeader = 'X-Subdomain';
if ( !$wmgEnableGeoData ) {
	$wgMFNearby = false;
}

$wgHooks['EnterMobileMode'][] = static function () {
	global $wgCentralAuthCookieDomain, $wgHooks, $wgIncludeLegacyJavaScript;

	// Disable loading of legacy wikibits in the mobile web experience
	$wgIncludeLegacyJavaScript = false;

	// Hack for T49647
	if ( $wgCentralAuthCookieDomain == 'commons.wikimedia.org' ) {
		$wgCentralAuthCookieDomain = 'commons.m.wikimedia.org';
	} elseif ( $wgCentralAuthCookieDomain == 'meta.wikimedia.org' ) {
		$wgCentralAuthCookieDomain = 'meta.m.wikimedia.org';
	}

	// Better hack for T49647
	$wgHooks['WebResponseSetCookie'][] = static function ( &$name, &$value, &$expire, &$options ) {
		if ( isset( $options['domain'] ) ) {
			if ( $options['domain'] == 'commons.wikimedia.org' ) {
				$options['domain'] = 'commons.m.wikimedia.org';
			} elseif ( $options['domain'] == 'meta.wikimedia.org' ) {
				$options['domain'] = 'meta.m.wikimedia.org';
			}
		}
	};

	return true;
};

$wgMFNearbyRange = $wgMaxGeoSearchRadius;

// Turn on volunteer recruitment
$wgMFEnableJSConsoleRecruitment = true;

// Brute-force bandwidth optimization by stripping srcset (T119797)
$wgMFStripResponsiveImages = true;

if ( $wmgMFDefaultEditor ) {
	$wgMFDefaultEditor = $wmgMFDefaultEditor;
}

# MUST be after MobileFrontend initialization
if ( $wmgEnableTextExtracts ) {
	wfLoadExtension( 'TextExtracts' );
}

if ( $wmgUseSubPageList3 ) {
	wfLoadExtension( 'SubPageList3' );
}

// Serve 'Powered by MediaWiki' badge from /static/images instead of
// $wgResourceBasePath so we can set far-future expires.
$wgFooterIcons['poweredby']['mediawiki']['src'] =
	   $wmgPoweredByMediaWikiIcon['1x'];
$wgFooterIcons['poweredby']['mediawiki']['srcset'] =
	   $wmgPoweredByMediaWikiIcon['1.5x'] . ' 1.5x, ' .
	   $wmgPoweredByMediaWikiIcon['2x'] . ' 2x';

if ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' ) {
	$wgCookieSecure = true;
	$_SERVER['HTTPS'] = 'on'; // Fake this so MW goes into HTTPS mode
}
$wgVaryOnXFP = true;

$wgCookieExpiration = 30 * 86400;
$wgExtendedLoginCookieExpiration = 365 * 86400;

if ( $wmgUseMath ) {
	wfLoadExtension( 'Math' );

	if ( $wmgUseMathML && $wmgUseRestbaseVRS ) {
		$wgDefaultUserOptions['math'] = 'mathml';
	}

	// This variable points to non-WMF servers by default.
	// Prevent accidental use.
	$wgMathLaTeXMLUrl = null;
	$wgMathMathMLUrl = $wmfLocalServices['mathoid'];
	// Increase the number of concurrent connections made to RESTBase
	$wgMathConcurrentReqs = 150;

	// Set up $wgMathFullRestbaseURL - similar to VE RESTBase config above
	// HACK: $wgServerName is not available yet at this point, it's set by Setup.php
	// so use a hook
	$wgExtensionFunctions[] = static function () {
		global $wgServerName, $wgMathFullRestbaseURL, $wmfRealm;

		$wgMathFullRestbaseURL = $wmfRealm === 'production'
			? 'https://wikimedia.org/api/rest_'  // T136205
			: "//$wgServerName/api/rest_";
	};
}

if ( $wmgUseBabel ) {
	wfLoadExtension( 'Babel' );
	$wgBabelCategoryNames = $wmgBabelCategoryNames;
	$wgBabelMainCategory = $wmgBabelMainCategory;
	$wgBabelDefaultLevel = $wmgBabelDefaultLevel;
	$wgBabelUseUserLanguage = $wmgBabelUseUserLanguage;

	$wgBabelUseDatabase = true;
	if ( $wmgUseCentralAuth ) {
		$wgBabelCentralDb = 'metawiki';
		$wgBabelCentralApi = 'https://meta.wikimedia.org/w/api.php';
	}
}

if ( $wmgUseBounceHandler ) {
	wfLoadExtension( 'BounceHandler' );
	// $wmgVERPsecret is set in PrivateSettings.php
	$wgVERPsecret = $wmgVERPsecret;
	$wgVERPdomainPart = 'wikimedia.org';
	$wgBounceHandlerUnconfirmUsers = true;
	$wgBounceRecordLimit = 5;
	$wgBounceHandlerCluster = 'extension1';
	$wgBounceHandlerSharedDB = 'wikishared';
	$wgBounceHandlerInternalIPs = [
		'208.80.154.76',              # mx1001
		'2620:0:861:3:208:80:154:76', # mx1001
		'208.80.153.45',              # mx2001
		'2620:0:860:2:208:80:153:45', # mx2001
	];
}

if ( $wmgUseTranslate ) {
	wfLoadExtension( 'Translate' );

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
	$wgExtraLanguageNames['qqq'] = 'Message documentation'; # No linguistic content. Used for documenting messages

	$wgPageLanguageUseDB = true; // T153209

	$wgTranslateTranslationServices = [];
	if ( $wmgUseTranslationMemory ) {
		// Switch to 'eqiad' or 'codfw' if you plan to bring down
		// the elastic cluster equals to $wmfDatacenter
		$wgTranslateTranslationDefaultService = $wmfDatacenter;

		// If the downtime is long (> 10mins) consider disabling
		// mirroring in this var to avoid logspam about ttm updates
		// then plan to refresh this index via ttmserver-export when
		// it's back up.
		// NOTE: these settings are also used for the labs cluster
		// where codfw may not be available
		$wgTranslateClustersAndMirrors = [
			'eqiad' => isset( $wmfAllServices['codfw']['search-chi'] ) ? [ 'codfw' ] : [],
			'codfw' => isset( $wmfAllServices['eqiad']['search-chi'] ) ? [ 'eqiad' ] : [],
		];
		foreach ( $wgTranslateClustersAndMirrors as $cluster => $mirrors ) {
			if ( !isset( $wmfAllServices[$cluster]['search-chi'] ) ) {
				continue;
			}
			$wgTranslateTranslationServices[$cluster] = [
				'type' => 'ttmserver',
				'class' => 'ElasticSearchTTMServer',
				'shards' => 1,
				'replicas' => 1,
				'index' => $wmgTranslateESIndex,
				'cutoff' => 0.65,
				'use_wikimedia_extra' => true,
				'config' => [
					'servers' => array_map( static function ( $hostConfig ) {
						if ( is_array( $hostConfig ) ) {
							return $hostConfig;
						}
						return [
							'host' => $hostConfig,
							'port' => 9243,
							'transport' => 'Https',
						];
					}, $wmfAllServices[$cluster]['search-chi'] ),
				],
				'mirrors' => $mirrors,
			];
		}
		unset( $wgTranslateClustersAndMirrors );
	} else {
		$wgTranslateTranslationDefaultService = false;
	}

	$wgTranslateWorkflowStates = $wmgTranslateWorkflowStates;
	$wgTranslateRcFilterDefault = $wmgTranslateRcFilterDefault;

	$wgTranslateUsePreSaveTransform = true; // T39304

	$wgEnablePageTranslation = true;
	$wgTranslateDelayedMessageIndexRebuild = true;

	// Deprecated language codes
	$wgTranslateDisabledTargetLanguages = [
		'*' => [
			'gan-hans' => 'Translate in gan please.',
			'gan-hant' => 'Translate in gan please.',

			'ike-cans' => 'Translate in iu please.',
			'ike-latn' => 'Translate in iu please.',

			'kk-cyrl' => 'Translate in kk please.',
			'kk-latn' => 'Translate in kk please.',
			'kk-arab' => 'Translate in kk please.',
			'kk-kz'   => 'Translate in kk please.',
			'kk-tr'   => 'Translate in kk please.',
			'kk-cn'   => 'Translate in kk please.',

			'ku-latn' => 'Translate in ku please.',
			'ku-arab' => 'Translate in ku please.',

			'shi-tfng' => 'Translate in shi please.',
			'shi-latn' => 'Translate in shi please.',

			'sr-ec' => 'Translate in sr please.',
			'sr-el' => 'Translate in sr please.',

			'tg-latn' => 'Translate in tg please.',

			'zh-hans' => 'Translate in zh please.',
			'zh-hant' => 'Translate in zh please.',
			'zh-cn' => 'Translate in zh please.',
			'zh-hk' => 'Translate in zh please.',
			'zh-mo' => 'Translate in zh please.',
			'zh-my' => 'Translate in zh please.',
			'zh-sg' => 'Translate in zh please.',
			'zh-tw' => 'Translate in zh please.',
		],
	];

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
		'host' => $wmfLocalServices['cxserver'],
		'timeout' => 3,
	];
}

if ( $wmgUseTranslationNotifications ) {
	wfLoadExtension( 'TranslationNotifications' );
	$wgTranslationNotificationsContactMethods['talkpage-elsewhere'] = true;
}

if ( $wmgUseFundraisingTranslateWorkflow ) {
	wfLoadExtension( 'FundraisingTranslateWorkflow' );
}

if ( $wmgUseVips ) {
	wfLoadExtension( 'VipsScaler' );
	$wgVipsOptions = [
		[
			'conditions' => [
				'mimeType' => 'image/png',
				'minArea' => 2e7,
			],
		],
		[
			'conditions' => [
				'mimeType' => 'image/tiff',
				'minShrinkFactor' => 1.2,
				'minArea' => 5e7,
			],
			'sharpen' => [ 'sigma' => 0.8 ],
		],
	];

}

if ( $wmgUseShortUrl ) {
	wfLoadExtension( 'ShortUrl' );
	$wgShortUrlTemplate = "/s/$1";
}

if ( $wmgUseFeaturedFeeds ) {
	wfLoadExtension( 'FeaturedFeeds' );
	require_once "$wmfConfigDir/FeaturedFeedsWMF.php";
}

$wgDisplayFeedsInSidebar = $wmgDisplayFeedsInSidebar;

if ( $wmgEnablePageTriage ) {
	wfLoadExtension( 'PageTriage' );
	$wgAddGroups['bureaucrat'][] = 'copyviobot'; // T206731
	$wgRemoveGroups['bureaucrat'][] = 'copyviobot'; // T206731
}

if ( $wmgEnableInterwiki ) {
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

# GWToolset
$wgJobTypesExcludedFromDefaultQueue[] = 'gwtoolsetUploadMetadataJob';
$wgJobTypesExcludedFromDefaultQueue[] = 'gwtoolsetUploadMediafileJob';
$wgJobTypesExcludedFromDefaultQueue[] = 'gwtoolsetGWTFileBackendCleanupJob';

if ( $wmgUseEducationProgram ) {
	$wgExtraNamespaces[446] = 'Education_Program';
	$wgExtraNamespaces[447] = 'Education_Program_talk';
	$wgNamespacesWithSubpages[447] = true;
}

if ( $wmgUseWikimediaShopLink ) {
	/**
	 * @param Skin $skin
	 * @param array $sidebar
	 * @return bool
	 */
	$wgHooks['SkinBuildSidebar'][] = static function ( $skin, &$sidebar ) {
		$sidebar['navigation'][] = [
			'text'  => $skin->msg( 'wikimediashoplink-linktext' )->text(),
			'href'  => '//shop.wikimedia.org',
			'title' => $skin->msg( 'wikimediashoplink-link-tooltip' )->text(),
			'id'    => 'n-shoplink',
		];
		return true;
	};
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

	// This is intentionally loaded *before* the GlobalPreferences extension (below).
	wfLoadExtension( 'Echo' );

	// Common settings, never varied
	$wgEchoPerUserBlacklist = true;
	$wgEchoMaxMentionsInEditSummary = 5;

	// Eventlogging for Schema:EchoMail
	$wgEchoEventLoggingSchemas['EchoMail']['enabled'] = true;
	// Eventlogging for Schema:EchoInteraction
	$wgEchoEventLoggingSchemas['EchoInteraction']['enabled'] = true;

	$wgEchoEnableEmailBatch = $wmgEchoEnableEmailBatch;
	$wgEchoEmailFooterAddress = $wmgEchoEmailFooterAddress;
	$wgEchoNotificationIcons['site']['url'] = $wmgEchoSiteNotificationIconUrl;

	# Outgoing from and reply to address for Echo notifications extension
	$wgNotificationSender = $wmgNotificationSender;
	$wgNotificationSenderName = $wgSitename;
	$wgNotificationReplyName = 'No Reply';

	// Define the cluster database, false to use main database
	$wgEchoCluster = $wmgEchoCluster;

	// Whether to use job queue to process web and email notifications
	$wgEchoUseJobQueue = $wmgEchoUseJobQueue;

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

	// Push notifications
	$wgEchoEnablePush = $wmgEchoEnablePush ?? false;
	$wgEchoPushServiceBaseUrl = "{$wmfLocalServices['push-notifications']}/v1/message";
	$wgEchoPushMaxSubscriptionsPerUser = 10;

	// Set up the push notifier type if push is enabled.
	// If/when this is promoted to all wikis, this config can be moved directly into extension.json
	// along with the original notifier types ('web' and 'email').
	if ( $wgEchoEnablePush ) {
		$wgEchoNotifiers['push'] = [ 'EchoPush\\PushNotifier', 'notifyWithPush' ];
		$wgDefaultNotifyTypeAvailability['push'] = true;
		$wgNotifyTypeAvailabilityByCategory['system']['push'] = false;
		$wgNotifyTypeAvailabilityByCategory['system-noemail']['push'] = false;
	}

	// Limit the 'push-subscription-manager' group to Meta-Wiki only (T261625)
	$wgExtensionFunctions[] = static function () {
		global $wgGroupPermissions, $wgDBname;
		if ( $wgDBname !== 'metawiki' ) {
			unset( $wgGroupPermissions['push-subscription-manager'] );
		}
	};
}

// Wikitech specific settings
if ( $wgDBname === 'labswiki' || $wgDBname === 'labtestwiki' ) {
	$wgEmailConfirmToEdit = true;
	$wgEnableCreativeCommonsRdf = true;

	// Don't depend on other DB servers
	$wgDefaultExternalStore = false;

	$wgGroupPermissions['contentadmin'] = $wgGroupPermissions['sysop'];
	$wgGroupPermissions['contentadmin']['editinterface'] = false;
	$wgGroupPermissions['contentadmin']['tboverride'] = false;
	$wgGroupPermissions['contentadmin']['titleblacklistlog'] = false;
	$wgGroupPermissions['contentadmin']['override-antispoof'] = false;
	$wgGroupPermissions['contentadmin']['createaccount'] = false;

	if ( $wgDBname === 'labtestwiki' ) {
		# We don't want random strangers playing on this wiki
		$wgGroupPermissions['*']['createaccount'] = false;
	}

	// These are somehow not added as they are assigned to 'sysop' in the respective extension.json
	$wgGroupPermissions['contentadmin']['nuke'] = true;
	$wgGroupPermissions['contentadmin']['massmessage'] = true;
	$wgGroupPermissions['contentadmin']['spamblacklistlog'] = true;

	// Grant AbuseFilter permissions regular sysops have - T242593
	$wgGroupPermissions['contentadmin']['abusefilter-log-detail'] = true;
	$wgGroupPermissions['contentadmin']['abusefilter-log-private'] = true;
	$wgGroupPermissions['contentadmin']['abusefilter-modify'] = true;
	$wgGroupPermissions['contentadmin']['abusefilter-modify-restricted'] = true;
	$wgGroupPermissions['contentadmin']['abusefilter-view-private'] = true;

	$wgMessageCacheType = 'memcached-pecl';

	if ( $wgDBname === 'labswiki' ) {
		$wgCookieDomain = "wikitech.wikimedia.org"; // TODO: Is this really necessary?
	} elseif ( $wgDBname === 'labtestwiki' ) {
		$wgCookieDomain = "labtestwikitech.wikimedia.org"; // TODO: Is this really necessary?
	}

	// Some settings specific to wikitech's extensions
	include "$wmfConfigDir/wikitech.php";

	# wgReadOnly is set by etcdConfig using datacenter-global configs.
	#  since wikitech and labtestwikitech use their own databases, their
	#  $wgReadOnly shouldn't be determined by that.
	$wgReadOnly = null;
}

if ( $wmgUseThanks ) {
	wfLoadExtension( 'Thanks' );
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
		$wgFlowEditorList = [ 'visualeditor', 'none' ];
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
	$wgFlowMaintenanceMode = $wmgFlowMaintenanceMode;

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
}

if ( $wmgUseCodeEditorForCore || $wmgUseScribunto ) {
	wfLoadExtension( 'CodeEditor' );
	$wgCodeEditorEnableCore = $wmgUseCodeEditorForCore;
	if ( $wgDBname === 'metawiki' ) {
		$wgHooks['CodeEditorGetPageLanguage'][] = static function ( Title $title, &$lang ) {
			if ( preg_match(
				'/^(API listing|Www\.wik(imedia|ipedia|inews|tionary|iquote|iversity|ibooks|ivoyage)\.org) template(\/temp)?$/',
				$title->getPrefixedText()
			) ) {
				$lang = 'html';
			}
		};
	}
}

if ( $wmgUseScribunto ) {
	wfLoadExtension( 'Scribunto' );
	$wgScribuntoUseGeSHi = true;
	$wgScribuntoUseCodeEditor = true;
	$wgScribuntoGatherFunctionStats = true;  // ori, 29-Oct-2015
	$wgScribuntoSlowFunctionThreshold = 0.99;

	$wgScribuntoDefaultEngine = 'luasandbox';
	$wgScribuntoEngineConf['luasandbox']['cpuLimit'] = 10;
	$wgScribuntoEngineConf['luasandbox']['maxLangCacheSize'] = 200; // see T85461#3100878
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

if ( $wmgUseListings ) {
	wfLoadExtension( 'Listings' );
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

	$wgRelatedArticlesLoggingBucketSize = 0;
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

if ( $wmgUseUserMerge ) {
	wfLoadExtension( 'UserMerge' );
	// Don't let users get deleted outright (T69789)
	$wgUserMergeEnableDelete = false;
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

	if ( $wgDBname === $wgEventLoggingDBname ) {
		wfLoadExtension( 'CodeEditor' );
		$wgCodeEditorEnableCore = $wmgUseCodeEditorForCore; // For safety's sake
	}

	// Depends on EventLogging
	if ( $wmgUseCampaigns ) {
		wfLoadExtension( 'Campaigns' );
	}

	// Depends on EventLogging
	if ( $wmgUseWikimediaEvents ) {
		wfLoadExtension( 'WikimediaEvents' );
		$wgWMEStatsdBaseUri = '/beacon/statsv';
		if ( $wgDBname === 'testwiki' ) {
			$wgGroupPermissions['data-qa']['perform-data-qa'] = true; // T276515
		}
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
	if ( $wmgUseCodeEditorForCore || $wmgUseScribunto ) {
		$wgULSNoImeSelectors[] = '.ace_editor textarea';
	}
	if ( $wmgUseTranslate && $wmgULSPosition === 'personal' ) {
		$wgTranslatePageTranslationULS = true;
	}

	$wgULSEventLogging = true;

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

	// T76200: Public URL for cxserver instance
	$wgContentTranslationSiteTemplates['cx'] = '//cxserver.wikimedia.org/v1';

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
	$wgCognateDb = 'cognate_' . $wmgUseCognate;
	$wgCognateCluster = 'extension1';
	$wgCognateNamespaces = [ 0 ];
}

if ( $wmgUseInterwikiSorting ) {
	$wgInterwikiSortingInterwikiSortOrders = include "$wmfConfigDir/InterwikiSortOrders.php";
	wfLoadExtension( 'InterwikiSorting' );
}

if ( $wmgUseWikibaseRepo || $wmgUseWikibaseClient || $wmgUseWikibaseMediaInfo ) {
	include "$wmfConfigDir/Wikibase.php";
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

if ( $wmgUseGraph ) {
	wfLoadExtension( 'JsonConfig' );
}

if ( $wmgEnableJsonConfigDataMode ) {
	// Safety: before extension.json, these values were initialized by JsonConfig.php
	if ( !isset( $wgJsonConfigModels ) ) {
		$wgJsonConfigModels = [];
	}
	if ( !isset( $wgJsonConfigs ) ) {
		$wgJsonConfigs = [];
	}

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
	];

	$wgJsonConfigModels['Map.JsonConfig'] = 'JsonConfig\JCMapDataContent';
	$wgJsonConfigs['Map.JsonConfig'] = [
		'namespace' => 486,
		'nsName' => 'Data',
		// page name must end in ".map", and contain at least one symbol
		'pattern' => '/.\.map$/',
		'license' => 'CC0-1.0',
		'isLocal' => false,
	];

	// Enable Tabular data namespace on Commons - T148745
	// Enable Map (GeoJSON) data namespace on Commons - T149548
	// TODO: Consider whether this hard-coding to Commons is appropriate
	if ( $wgDBname === 'commonswiki' ) {
		// Ensure we have a stable cross-wiki title resolution
		// See JCSingleton::parseTitle()
		$wgJsonConfigInterwikiPrefix = "meta";

		$wgJsonConfigs['Tabular.JsonConfig']['store'] = true;
		$wgJsonConfigs['Map.JsonConfig']['store'] = true;
	} else {
		$wgJsonConfigInterwikiPrefix = "commons";

		$wgJsonConfigs['Tabular.JsonConfig']['remote'] = [
			'url' => 'https://commons.wikimedia.org/w/api.php'
		];
		$wgJsonConfigs['Map.JsonConfig']['remote'] = [
			'url' => 'https://commons.wikimedia.org/w/api.php'
		];
	}
}

// Enable Config:Dashiki: sub-namespace on meta.wikimedia.org - T156971
if ( $wmgEnableDashikiData ) {
	// Dashiki sub-namespace Config:Dashiki: is configured in extension.json
	wfLoadExtension( 'Dashiki' );
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

	if ( $wmgUseGraphWithJsonNamespace ) {
		$wgJsonConfigModels['Json.JsonConfig'] = null;
		$wgJsonConfigs['Json.JsonConfig'] = [
			'namespace' => 486,
			'nsName' => 'Data',
			'isLocal' => true,
			'pattern' => '/^Json:./',
		];
	}
}

if ( $wmgUseOAuth ) {
	wfLoadExtension( 'OAuth' );
	if ( in_array( $wgDBname, [ 'labswiki', 'labtestwiki' ] ) ) {
		// Wikitech and its testing variant use local OAuth tables
		$wgMWOAuthCentralWiki = false;
	} else {
		$wgMWOAuthCentralWiki = 'metawiki';
		$wgMWOAuthSharedUserSource = 'CentralAuth';
	}
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

	$wgGroupPermissions['sysop']['oathauth-disable-for-user'] = false;
	$wgGroupPermissions['sysop']['oathauth-view-log'] = false;
	$wgGroupPermissions['sysop']['oathauth-verify-user'] = false; // T209749

	if ( $wmgUseCentralAuth ) {
		$wgOATHAuthAccountPrefix = 'Wikimedia';
		$wgOATHAuthDatabase = 'centralauth';
	}

	if ( $wmgUseWebAuthn ) {
		wfLoadExtension( 'WebAuthn' );
	}
}

if ( $wmgUseMediaModeration ) {
	if ( $wmfRealm === 'production' ) {
		$wgMediaModerationHttpProxy = $wmfLocalServices['urldownloader'];
	}

	wfLoadExtension( 'MediaModeration' );
	// This relies on configuration in PrivateSettings.php:
	// - $wgMediaModerationPhotoDNASubscriptionKey
	// - $wgMediaModerationRecipientList
	$wgMediaModerationFrom = 'no-reply@wikimedia.org';
}

if ( $wmgUseORES ) {
	wfLoadExtension( 'ORES' );
	$wgOresBaseUrl = 'http://localhost:6010/';
	$wgOresFrontendBaseUrl = 'https://ores.wikimedia.org/';
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
		// 10.0.0.0/8 is handled below, don't add it here.
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
		'10.0.0.0/10',
		'10.64.0.0/14',
		// 10.68.0.0/16 is allowed
		'10.69.0.0/16',
		'10.70.0.0/15',
		'10.72.0.0/13',
		'10.80.0.0/12',
		'10.96.0.0/11',
		'10.128.0.0/9',
		// 172.16.0.0/16 is allowed
		'172.17.0.0/16',
		'172.18.0.0/15',
		'172.20.0.0/14',
		'172.24.0.0/13',
	] );
} else {
	// Cloud VPS users shouldn't be editing anonymously on most wikis, so we
	// can block anonymous edits from the whole private ranges.
	$wgSoftBlockRanges[] = '10.0.0.0/8';
	$wgSoftBlockRanges[] = '172.16.0.0/12';
	// Cloud VPS VMs with floating/public addresses
	$wgSoftBlockRanges[] = '185.15.56.0/24';
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
	return true;
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
	}
	// T239288 - CheckUser logs pertaining to spam blacklist logged actions appear redacted
	$wgCheckUserLogAdditionalRights[] = 'spamblacklistlog';
}

if ( $wmgUseIPInfo ) {
	wfLoadExtension( 'IPInfo' );
}

// T39211
$wgUseCombinedLoginLink = false;

if ( $wmgUseRC2UDP ) {
	if ( $wmgRC2UDPPrefix === false ) {
		$matches = null;
		if ( preg_match( '/^(https?:)?\/\/(.+)\.org$/', $wgServer, $matches ) && isset( $matches[2] ) ) {
			$wmgRC2UDPPrefix = "#{$matches[2]}\t";
		}
	}

	foreach ( $wmfLocalServices['irc'] as $i => $address ) {
		$wgRCFeeds["irc$i"] = [
			'formatter' => 'IRCColourfulRCFeedFormatter',
			'uri' => "udp://$address:$wmgRC2UDPPort/$wmgRC2UDPPrefix",
			'add_interwiki_prefix' => false,
			'omit_bots' => false,
		];
	}
}

// Confirmed can do anything autoconfirmed can.
// T277704, T275334: Extension function would be a more nature place to put this code to,
// but doing so is not reliable as of 2021-03-18. If you are here to put this into an extension
// function, see also T213003.
$wgHooks['MediaWikiServices'][] = static function () {
	global $wgGroupPermissions;

	$wgGroupPermissions['confirmed'] = $wgGroupPermissions['autoconfirmed'];
	$wgGroupPermissions['confirmed']['skipcaptcha'] = true;
};

$wgDefaultUserOptions['watchlistdays'] = $wmgWatchlistNumberOfDaysShow;

if ( $wmgUseWikidataPageBanner ) {
	wfLoadExtension( 'WikidataPageBanner' );
}

if ( $wmgUseQuickSurveys ) {
	wfLoadExtension( 'QuickSurveys' );
}

if ( $wmgUseEventBus ) {
	wfLoadExtension( 'EventBus' );

	// For analytics purposes, we forward the X-Client-IP header to eventgate.
	// eventgate will use this to set a default http.client_ip in event data when relevant.
	// https://phabricator.wikimedia.org/T288853
	$wgEventServices = [
		'eventgate-analytics' => [
			'url' => "{$wmfLocalServices['eventgate-analytics']}/v1/events?hasty=true",
			'timeout' => 11,
			'x_client_ip_forwarding_enabled' => true,
		],
		'eventgate-analytics-external' => [
			'url' => "{$wmfLocalServices['eventgate-analytics-external']}/v1/events?hasty=true",
			'timeout' => 11,
			'x_client_ip_forwarding_enabled' => true,
		],
		'eventgate-main' => [
			'url' => "{$wmfLocalServices['eventgate-main']}/v1/events",
			'timeout' => 62, // envoy overall req timeout + 1
		]
	];

	$wgRCFeeds['eventbus'] = [
		'formatter' => '\\MediaWiki\\Extension\\EventBus\\Adapters\\RCFeed\\EventBusRCFeedFormatter',
		'class' => '\\MediaWiki\\Extension\\EventBus\\Adapters\\RCFeed\\EventBusRCFeedEngine',
	];

	if ( $wmgUseClusterJobqueue ) {
		$wgJobTypeConf['default'] = [
			'class' => '\\MediaWiki\\Extension\\EventBus\\Adapters\\JobQueue\\JobQueueEventBus',
			'readOnlyReason' => false
		];
	}

	if ( $wmgServerGroup === 'jobrunner' || $wmgServerGroup === 'videoscaler' ) {
		$wgEventBusEnableRunJobAPI = true;
	} else {
		$wgEventBusEnableRunJobAPI = false;
	}
}

if ( $wmgUseCapiunto ) {
	wfLoadExtension( 'Capiunto' );
}

if ( $wmgUseKartographer ) {
	wfLoadExtension( 'Kartographer' );
	$wgKartographerMapServer = 'https://maps.wikimedia.org';
}

if ( $wmgUsePageViewInfo ) {
	wfLoadExtension( 'PageViewInfo' );
}

if ( $wgDBname === 'foundationwiki' ) {
	// Foundationwiki has raw html enabled. Attempt to prevent people
	// from accidentally violating the privacy policy with external scripts.
	// Note, we need all WMF domains in here due to Special:HideBanners
	// being loaded as an image from various domains on donation thank you
	// pages.
	$wgHooks['BeforePageDisplay'][] = static function ( $out, $skin ) {
		$resp = $out->getRequest()->response();
		$cspHeader = "default-src *.wikimedia.org *.wikipedia.org *.wiktionary.org *.wikisource.org *.wikibooks.org *.wikiversity.org *.wikiquote.org *.wikinews.org www.mediawiki.org www.wikidata.org *.wikivoyage.org data: blob: 'self'; script-src *.wikimedia.org 'unsafe-inline' 'unsafe-eval' 'self'; style-src  *.wikimedia.org data: 'unsafe-inline' 'self'; report-uri /w/api.php?action=cspreport&format=none&reportonly=1&source=wmfwiki&";
		$resp->header( "X-Webkit-CSP-Report-Only: $cspHeader" );
		$resp->header( "X-Content-Security-Policy-Report-Only: $cspHeader" );
		$resp->header( "Content-Security-Policy-Report-Only: $cspHeader" );
	};
}

if ( $wmgUseDynamicSidebar ) {
	wfLoadExtension( 'DynamicSidebar' );
}

if ( $wmgUse3d ) {
	wfLoadExtension( '3D' );
	$wgTrustedMediaFormats[] = 'application/sla';
	$wg3dProcessor = [ '/usr/bin/xvfb-run', '-a', '-s', '-ac -screen 0 1280x1024x24' ,'/srv/deployment/3d2png/deploy/src/3d2png.js' ];

	if ( $wmgUseMultimediaViewer ) {
		$wgMediaViewerExtensions['stl'] = 'mmv.3d';
	}

	if ( $wmgUpload3d ) {
		$wgFileExtensions[] = 'stl';
	}
}

if ( $wmgUseReadingLists ) {
	$wgReadingListsMaxEntriesPerList = 5000;
	wfLoadExtension( 'ReadingLists' );
}

if ( $wmgUseGlobalPreferences && $wmgUseCentralAuth ) {
	// This is intentionally loaded *after* the Echo extension (above).
	wfLoadExtension( 'GlobalPreferences' );
}

if ( $wmgUseCongressLookup ) {
	wfLoadExtension( 'CongressLookup' );
}

if ( $wmgUseWikisource ) {
	// Intentionally loaded *after* the Collection extension above.
	wfLoadExtension( 'Wikisource' );
}

if ( $wmgUseGrowthExperiments ) {
	wfLoadExtension( 'GrowthExperiments' );

	if ( !$wmgGEFeaturesMayBeAvailableToNewcomers ) {
		// Disable welcome survey
		$wgWelcomeSurveyExperimentalGroups = [
			'exp2_target_specialpage' => [ 'percentage' => 0 ],
			'exp2_target_popup' => [ 'percentage' => 0 ],
			'exp1_group2' => [ 'percentage' => 0 ],
		];
		// Disable all other Growth features
		$wgGEHomepageNewAccountEnablePercentage = 0;
	}

	// POC API, allowed until 2022-03-31. See T294362.
	$wgGEImageRecommendationServiceUrl = 'https://image-suggestion-api.wmcloud.org';
	if ( $wmfRealm !== 'labs' ) {
		$wgGEImageRecommendationServiceHttpProxy = $wmfLocalServices['urldownloader'];
	}
	$wgGELinkRecommendationServiceUrl = $wmfLocalServices['linkrecommendation'];
}

if ( $wmgUseWikiLambda && $wmfRealm === 'labs' ) {
	wfLoadExtension( 'WikiLambda' );

	$wgWikiLambdaOrchestratorLocation = $wmfLocalServices['wikifunctions-orcehstrator'];
	$wgWikiLambdaEvaluatorLocation = "http://{$wmfLocalServices['wikifunctions-evaluator']}/1/v1/evaluate";
	$wgWikiLambdaWikiAPILocation = 'https://wikifunctions.beta.wmflabs.org/w/api.php';
}

if ( PHP_SAPI === 'cli' ) {
	// Needed for the "abstracts" XML dumps. We only load it for PHP from the CLI.
	// Must be loaded explicitly here so that autoloading works when specifying
	// its PHP class name as CLI option to the maintenance script.
	wfLoadExtension( 'ActiveAbstract' );
}

if ( $wmgUseCSPReportOnly || $wmgUseCSPReportOnlyHasSession || $wmgUseCSP ) {
	// Temporary global whitelist for origins used by trusted
	// opt-in scripts, until a per-user ability for this exists.
	// T207900#4846582
	$wgCSPFalsePositiveUrls = array_merge( $wgCSPFalsePositiveUrls, [
		'https://cvn.wmflabs.org' => true,
		'https://tools.wmflabs.org/intuition/' => true,
		'https://intuition.toolforge.org/' => true,
	] );

	$wgExtensionFunctions[] = static function () {
		global $wgCSPReportOnlyHeader, $wmgUseCSPReportOnly, $wgCommandLineMode,
			$wmgApprovedContentSecurityPolicyDomains, $wmgUseCSP, $wgCSPHeader;
		if ( !$wmgUseCSPReportOnly && !$wmgUseCSP ) {
			// This means that $wmgUseCSPReportOnlyHasSession
			// is set, so only logged in users should trigger this.
			if (
				defined( 'MW_NO_SESSION' ) ||
				$wgCommandLineMode ||
				!MediaWiki\Session\SessionManager::getGlobalSession()->isPersistent()
			) {
				// There is no session, so don't set CSP, as we care more
				// about actual users, and this allows quick revert since
				// not cached in varnish
				return;
			}
		}

		if ( $wmgUseCSPReportOnly ) {
			$wgCSPReportOnlyHeader['useNonces'] = false;
			$wgCSPReportOnlyHeader['includeCORS'] = false;
			$wgCSPReportOnlyHeader['default-src'] = array_merge(
				$wmgApprovedContentSecurityPolicyDomains,
				[
					// Needed for Math. Remove when/if math is fixed.
					'wikimedia.org',
				]
			);
			$wgCSPReportOnlyHeader['script-src'] = $wmgApprovedContentSecurityPolicyDomains;
		}

		if ( $wmgUseCSP ) {
			$wgCSPHeader['useNonces'] = false;
			$wgCSPHeader['includeCORS'] = false;
			$wgCSPHeader['default-src'] = array_merge(
				$wmgApprovedContentSecurityPolicyDomains,
				[
					// Needed for Math. Remove when/if math is fixed.
					'wikimedia.org',
				]
			);
			$wgCSPHeader['script-src'] = $wmgApprovedContentSecurityPolicyDomains;
		}
	};
}

if ( $wmfRealm === 'labs' ) {
	require "$wmfConfigDir/CommonSettings-labs.php";
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

if ( $wmgUseMachineVision ) {
	if ( $wmfRealm === 'production' ) {
		$wgMachineVisionHttpProxy = $wmfLocalServices['urldownloader'];
	}

	wfLoadExtension( 'MachineVision' );
	$wgNotifyTypeAvailabilityByCategory['machinevision']['email'] = false;
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

if ( $wmgUseNearbyPages ) {
	wfLoadExtension( 'NearbyPages' );
}

// This is a temporary hack for hooking up Parsoid/PHP with MediaWiki
// This is just the regular check out of parsoid in that week's vendor
$parsoidDir = "$IP/vendor/wikimedia/parsoid";
$wgParsoidSettings = [
	'useSelser' => true,
	'linting' => true,
	'nativeGalleryEnabled' => false,  // T214649
];
if ( $wmgServerGroup === 'parsoid' && wfHostName() === 'scandium' ) {
	// Scandium has its own special check out of parsoid for testing.
	$parsoidDir = __DIR__ . "/../../parsoid-testing";
	// Override settings specific to round-trip testing on scandium
	require_once "$parsoidDir/tests/RTTestSettings.php";
}

wfLoadExtension( 'Parsoid', "$parsoidDir/extension.json" );
unset( $parsoidDir );
// End of temporary hack for hooking up Parsoid/PHP with MediaWiki

# Temporary, until T112147 is done
# Assign everything assigned to suppressors to oversighters
# and delete suppress group.
$wgExtensionFunctions[] = static function () {
	global $wgGroupPermissions;
	if ( isset( $wgGroupPermissions['suppress'] ) ) {
		$wgGroupPermissions['oversight'] += $wgGroupPermissions['suppress'];
		unset( $wgGroupPermissions['suppress'] );
	}
};

// To enable media tags at only some of the wikis, see T266067
$wgSoftwareTags = array_merge( $wgSoftwareTags, $wmgAdditionalSoftwareTags );

class ClosedWikiProvider extends \MediaWiki\Auth\AbstractPreAuthenticationProvider {
	/**
	 * @param User $user
	 * @param bool $autocreate
	 * @param array $options
	 * @return \StatusValue
	 */
	public function testUserForCreation( $user, $autocreate, array $options = [] ) {
		$logger = \MediaWiki\Logger\LoggerFactory::getInstance( 'authentication' );
		$logger->info( 'Running ClosedWikiProvider for {name}', [
			'name' => $user->getName()
		] );
		if ( $user->getId() ) { // User already exists, do not block authentication
			$logger->info( 'User {name} passed ClosedWikiProvider check, account already exists', [
				'name' => $user->getName()
			] );
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
			return \StatusValue::newGood();
		}
		$logger->error(
			'Account autocreation denied for {name} by ClosedWikiProvider', [
				'name' => $user->getName()
			]
		);
		return \StatusValue::newFatal( 'authmanager-autocreate-noperm' );
	}
}

if (
	in_array( $wgDBname, MWWikiversions::readDbListFile( 'closed' ) ) &&
	$wmgUseCentralAuth
) {
	$wgAuthManagerAutoConfig['preauth'][\ClosedWikiProvider::class] = [
		'class' => \ClosedWikiProvider::class,
		'sort' => 0,
	];
}

# THIS MUST BE AFTER ALL EXTENSIONS ARE INCLUDED
#
# REALLY ... we're not kidding here ... NO EXTENSIONS AFTER

if ( !defined( 'MW_NO_EXTENSION_MESSAGES' ) ) {
	require "$wmfConfigDir/ExtensionMessages-$wmgVersionNumber.php";
}
