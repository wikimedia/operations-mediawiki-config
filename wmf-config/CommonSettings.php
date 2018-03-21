<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

# CommonSettings.php is the main configuration file of the WMF cluster.
# This file contains settings common to all (or many) WMF wikis.
# For per-wiki configuration, see InitialiseSettings.php.
#
# This for PRODUCTION.
#
# Effective load order:
# - multiversion
# - mediawiki/DefaultSettings.php
# - wmf-config/InitialiseSettings.php
# - wmf-config/CommonSettings.php [THIS FILE]
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
#       |
#       `-- (main stuff in CommonSettings.php) [THIS FILE]
#

use MediaWiki\Logger\LoggerFactory;

# Godforsaken hack to work around problems with the reverse proxy caching changes...
#
# To minimize damage on fatal PHP errors, output a default no-cache header
# It will be overridden in cases where we actually specify caching behavior.
#
# More modern PHP versions will send a 500 result code on fatal error,
# at least sometimes, but what we're running will send a 200.
if ( PHP_SAPI != 'cli' ) {
	header( "Cache-control: no-cache" );
}

// Clobber any value in $_SERVER['SERVER_SOFTWARE'] other than Apache, so that
// IEUrlExtension::haveUndecodedRequestUri() always thinks we're running Apache.
// Otherwise, the absense of 'Apache' from $_SERVER['SERVER_SOFTWARE'] causes it
// to distrust REQUEST_URI, which leads to incorrect behavior.
if ( isset( $_SERVER['SERVER_SOFTWARE'] ) ) {
	$_SERVER['SERVER_SOFTWARE'] = 'Apache';
}

if ( PHP_SAPI === 'cli' ) {
	# Override for sanity's sake. Log errors to stderr.
	ini_set( 'display_errors', 'stderr' );
}
if ( isset( $_SERVER['SERVER_ADDR'] ) ) {
	ini_set( 'error_append_string', ' (' . $_SERVER['SERVER_ADDR'] . ')' );
}

# ----------------------------------------------------------------------
# Initialisation

# Get the version object for this Wiki (must be set by now, along with $IP)
if ( !class_exists( 'MWMultiVersion' ) ) {
	print "No MWMultiVersion instance initialized! MWScript.php wrapper not used?\n";
	exit( 1 );
}
$multiVersion = MWMultiVersion::getInstance();

set_include_path( "$IP:/usr/local/lib/php:/usr/share/php" );

### List of some service hostnames
# 'meta'    : meta wiki for user editable content
# 'upload'  : hostname where files are hosted
# 'wikidata': hostname for the data repository
# Whenever all realms/datacenters should use the same host, do not use
# $wmfHostnames but use the hardcoded hostname instead. A good example are the
# spam blacklists hosted on meta.wikimedia.org which you will surely want to
# reuse.
$wmfHostnames = [];
switch ( $wmfRealm ) {
case 'labs':
	$wmfHostnames['meta']     = 'meta.wikimedia.beta.wmflabs.org';
	$wmfHostnames['test']     = 'test.wikimedia.beta.wmflabs.org';
	$wmfHostnames['upload']   = 'upload.beta.wmflabs.org';
	$wmfHostnames['wikidata'] = 'wikidata.beta.wmflabs.org';
	break;
case 'production':
default:
	$wmfHostnames['meta']   = 'meta.wikimedia.org';
	$wmfHostnames['test']   = 'test.wikipedia.org';
	$wmfHostnames['upload'] = 'upload.wikimedia.org';
	$wmfHostnames['wikidata'] = 'www.wikidata.org';
	break;
}

# This must be set *after* the DefaultSettings.php inclusion
$wgDBname = $multiVersion->getDatabase();

# Better have the proper username (T46251)
$wgDBuser = 'wikiuser';

# wmf-config directory (in common/)
$wmfConfigDir = "$IP/../wmf-config";

# Include all the service definitions
# TODO: only include if in production, set up beta separately
switch ( $wmfRealm ) {
case 'labs':
	require "$wmfConfigDir/LabsServices.php";
	break;
case 'production':
default:
	require "$wmfConfigDir/ProductionServices.php";
}

# Shorthand when we have no master-slave situation to keep into account
$wmfLocalServices = $wmfAllServices[$wmfDatacenter];

# Configuration from etcd (sets $wmfMasterDatacenter, $wgReadOnly and wmfEtcdLastModifiedIndex)
require "$wmfConfigDir/etcd.php";
wmfEtcdConfig();

$wmfMasterServices = $wmfAllServices[$wmfMasterDatacenter];

# Must be set before InitialiseSettings.php:
$wmfUdp2logDest = $wmfLocalServices['udp2log'];

# Initialise wgConf
require "$wmfConfigDir/wgConf.php";
/**
 * @param $conf
 */
function wmfLoadInitialiseSettings( $conf ) {
	global $wmfConfigDir;
	require "$wmfConfigDir/InitialiseSettings.php";
}

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
# Note: $wgLocalDatabases set in wgConf.php.
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

$wmgVersionNumber = $multiVersion->getVersionNumber();

# Try configuration cache

$filename = "/tmp/mw-cache-$wmgVersionNumber/conf-$wgDBname";
if ( defined( 'HHVM_VERSION' ) ) {
	$filename .= '-hhvm';
}

$globals = false;
if ( @filemtime( $filename ) >= filemtime( "$wmfConfigDir/InitialiseSettings.php" ) ) {
	$cacheRecord = @file_get_contents( $filename );
	if ( $cacheRecord !== false ) {
		$globals = unserialize( $cacheRecord );
	}
}

if ( !$globals ) {
	# Get configuration from SiteConfiguration object
	require "$wmfConfigDir/InitialiseSettings.php";

	# Collect all the dblist tags associated with this wiki
	$wikiTags = [];
	# When updating list please run ./docroot/noc/createTxtFileSymlinks.sh
	# Expand computed dblists with ./multiversion/bin/expanddblist
	foreach ( [ 'private', 'fishbowl', 'special', 'closed', 'flow', 'flaggedrevs', 'small', 'medium',
			'large', 'wikimania', 'wikidata', 'wikidataclient', 'visualeditor-nondefault',
			'commonsuploads', 'nonbetafeatures', 'group0', 'group1', 'group2', 'wikipedia', 'nonglobal',
			'wikitech', 'nonecho', 'mobilemainpagelegacy',
			'wikipedia-cyrillic', 'wikipedia-e-acute', 'wikipedia-devanagari',
			'wikipedia-english',
			'nowikidatadescriptiontaglines',
			'related-articles-footer-blacklisted-skins',
			'top6-wikipedia', 'rtl',
			'pp_stage0', 'pp_stage1'
		] as $tag ) {
		$dblist = MWWikiversions::readDbListFile( $tag );
		if ( in_array( $wgDBname, $dblist ) ) {
			$wikiTags[] = $tag;
		}
	}

	$dbSuffix = ( $site === 'wikipedia' ) ? 'wiki' : $site;
	$confParams = [
		'lang'    => $lang,
		'docRoot' => $_SERVER['DOCUMENT_ROOT'],
		'site'    => $site,
	];
	// Add a per-language tag as well
	$wikiTags[] = $wgConf->get( 'wgLanguageCode', $wgDBname, $dbSuffix, $confParams, $wikiTags );
	$globals = $wgConf->getAll( $wgDBname, $dbSuffix, $confParams, $wikiTags );

	# Save cache
	@mkdir( '/tmp/mw-cache-' . $wmgVersionNumber );
	$tmpFile = tempnam( '/tmp/', "conf-$wmgVersionNumber-$wgDBname" );
	if ( $tmpFile && file_put_contents( $tmpFile, serialize( $globals ) ) ) {
		if ( !rename( $tmpFile, $filename ) ) {
			// T136258: Rename failed, cleanup temp file
			unlink( $tmpFile );
		};
	}
}

extract( $globals );

# -------------------------------------------------------------------------
# Settings common to all wikis

# Private settings such as passwords, that shouldn't be published
# Needs to be before db.php
require "$wmfConfigDir/../private/PrivateSettings.php";

$wgMemCachedServers = [];

require "$wmfConfigDir/logging.php";
require "$wmfConfigDir/redis.php";

if ( isset( $_SERVER['HTTP_X_WIKIMEDIA_DEBUG'] ) && preg_match( '/\breadonly\b/i', $_SERVER['HTTP_X_WIKIMEDIA_DEBUG'] ) ) {
	$wgReadOnly = 'X-Wikimedia-Debug';
}

if ( $wmfRealm === 'labs' ) {
	require "$wmfConfigDir/db-labs.php";
	require "$wmfConfigDir/mc-labs.php";
} else {
	require "$wmfConfigDir/mc.php";
	require "$wmfConfigDir/db-{$wmfDatacenter}.php";
}

# Disallow web request DB transactions slower than this
$wgMaxUserDBWriteDuration = 3;
# Activate read-only mode for bots when lag is getting high.
# This should be lower than 'max lag' in the LBFactory conf.
$wgAPIMaxLagThreshold = 3;

ini_set( 'memory_limit', $wmgMemoryLimit );

# Change calls to wfShellWikiCmd() to use MWScript.php wrapper
$wgHooks['wfShellWikiCmd'][] = 'MWMultiVersion::onWfShellMaintenanceCmd';

setlocale( LC_ALL, 'en_US.UTF-8' );

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

if ( $wmgReduceStartupExpiry ) {
	$wgResourceLoaderMaxage['unversioned'] = [ 'server' => 30, 'client' => 30 ];
}

// Cache version key for ResourceLoader client-side module store
// - Bumped to fix breakage due to old /static/$branchName/ urls still
// being cached after the switch to /w/static.php (T134368).
// - Bumped to fix broken SVG embedding being cached (T176884)
$wgResourceLoaderStorageVersion .= '-3';

$wgCacheDirectory = '/tmp/mw-cache-' . $wmgVersionNumber;
$wgGitInfoCacheDirectory = "$IP/cache/gitinfo";

// @var string|bool: E-mail address to send notifications to, or false to disable notifications.
$wmgAddWikiNotify = "newprojects@lists.wikimedia.org";

// Comment out the following lines to get the old-style l10n caching -- TS 2011-02-22
$wgLocalisationCacheConf['storeDirectory'] = "$IP/cache/l10n";
$wgLocalisationCacheConf['manualRecache'] = true;

// T29320: skip MessageBlobStore::clear(); handle via refreshMessageBlobs.php instead
$wgHooks['LocalisationCacheRecache'][] = function ( $cache, $code, &$allData, &$purgeBlobs = true ) {
	$purgeBlobs = false;
	return true;
};

// Add some useful config data to query=siteinfo
$wgHooks['APIQuerySiteInfoGeneralInfo'][] = function ( $module, &$data ) {
	global $wmfMasterDatacenter;
	global $wmfEtcdLastModifiedIndex;
	$data['wmf-config'] = [
		'wmfMasterDatacenter' => $wmfMasterDatacenter,
		'wmfEtcdLastModifiedIndex' => $wmfEtcdLastModifiedIndex,
	];
};

# used for mysql/search settings
$tmarray = getdate( time() );
$hour = $tmarray['hours'];
$day = $tmarray['wday'];

$wgEmergencyContact = 'noc@wikipedia.org';

if ( !isset( $wgLocaltimezone ) ) {
	$wgLocaltimezone = 'UTC';
}

if ( $wgLocaltimezone !== 'UTC' ) {
	$wgLocalTZOffset = timezone_offset_get(
		timezone_open( $wgLocaltimezone ),
		date_create( 'now', timezone_open( 'UTC' ) )
	) / 60;
} else {
	$wgLocalTZOffset = 0;
}

$wgShowIPinHeader = false;
$wgRCMaxAge = 30 * 86400;

$wgTmpDirectory = '/tmp';

$wgSQLMode = null;

# Object cache and session settings

$wgSessionName = $wgDBname . 'Session';

$pcTemplate = [ 'type' => 'mysql',
	'dbname' => 'parsercache',
	'user' => $wgDBuser,
	'password' => $wgDBpassword,
	'flags' => 0,
];

$pcServers = [];
foreach ( $wmgParserCacheDBs as $tag => $host ) {
	$pcServers[$tag] = [ 'host' => $host ] + $pcTemplate;
}

$wgObjectCaches['mysql-multiwrite'] = [
	'class' => 'MultiWriteBagOStuff',
	'caches' => [
		0 => [
			'factory' => [ 'ObjectCache', 'getInstance' ],
			'args' => [ 'memcached-pecl' ]
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

$wgSessionsInObjectCache = true;
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

if ( $wgDBname === 'labswiki' || $wgDBname === 'labtestwiki' ) {
	$wgPasswordPolicy['policies']['default']['MinimalPasswordLength'] = 10;
} else {
	// See password policy RFC on meta
	// [[m:Requests_for_comment/Password_policy_for_users_with_certain_advanced_permissions]]
	foreach ( $wmgPrivilegedGroups as $group ) {
		if ( $group === 'user' ) {
			$group = 'default'; // Covers 'user' in password policies
		}

		$wgPasswordPolicy['policies'][$group]['MinimalPasswordLength'] = 8;
		$wgPasswordPolicy['policies'][$group]['MinimumPasswordLengthToLogin'] = 1;
		$wgPasswordPolicy['policies'][$group]['PasswordCannotBePopular'] = 10000;
	}
}

$wgPasswordPolicy['policies']['default']['PasswordCannotBePopular'] = 100;

// Enforce password policy when users login on other wikis
if ( $wmgUseCentralAuth ) {
	$wgHooks['PasswordPoliciesForUser'][] = function ( User $user, array &$effectivePolicy ) {
		$central = CentralAuthUser::getInstance( $user );
		if ( !$central->exists() ) {
			return true;
		}

		$privilegedPolicy = [
			'MinimalPasswordLength' => 8,
			'MinimumPasswordLengthToLogin' => 1,
			'PasswordCannotBePopular' => 10000,
		];

		if ( array_intersect(
			[ 'bureaucrat', 'sysop', 'checkuser', 'oversight', 'interface-editor', 'jseditor' ],
			$central->getLocalGroups()
		) ) {
			$effectivePolicy = UserPasswordPolicy::maxOfPolicies(
				$effectivePolicy,
				$privilegedPolicy
			);
			return true;
		}

		// Result should be cached by getLocalGroups() above
		try {
			$attachInfo = $central->queryAttached();
		} catch ( Exception $e ) {
			// Don't block login if we can't query attached (T119736)
			MWExceptionHandler::logException( $e );
			return true;
		}
		$enforceWikiGroups = [
			'centralnoticeadmin' => [ 'metawiki', 'testwiki' ],
			'templateeditor' => [ 'fawiki', 'rowiki' ],
			'botadmin' => [ 'frwiktionary', 'mlwiki', 'mlwikisource', 'mlwiktionary' ],
			'translator' => [ 'incubatorwiki' ],
			'wikidata-staff' => [ 'wikidata' ],
		];

		foreach ( $enforceWikiGroups as $group => $wikis ) {
			foreach ( $wikis as $wiki ) {
				if ( isset( $attachInfo[$wiki]['groups'] )
					&& in_array( $group, $attachInfo[$wiki]['groups'] ) ) {
					$effectivePolicy = UserPasswordPolicy::maxOfPolicies(
						$effectivePolicy,
						$privilegedPolicy
					);
					return true;
				}
			}
		}

		return true;
	};
}

// For global policies, see $wgCentralAuthGlobalPasswordPolicies below

$wgEnableBotPasswords = $wmgEnableBotPasswords;
$wgBotPasswordsCluster = $wmgBotPasswordsCluster;
$wgBotPasswordsDatabase = $wmgBotPasswordsDatabase;

if ( PHP_SAPI === 'cli' ) {
	$wgShowExceptionDetails = true;
	$wgShowDBErrorBacktrace = true;
}

$wgUseImageResize               = true;
$wgUseImageMagick               = true;
$wgImageMagickConvertCommand    = '/usr/local/bin/mediawiki-firejail-convert';
$wgSharpenParameter = '0x0.8'; # for IM>6.5, T26857

if ( $wmgUsePagedTiffHandler ) {
	wfLoadExtension( 'PagedTiffHandler' );
}
$wgTiffUseTiffinfo = true;
$wgTiffMaxMetaSize = 1048576;

$wgMaxImageArea = 10e7; // 100MP
$wgMaxAnimatedGifArea = 10e7; // 100MP

$wgFileExtensions = array_merge( $wgFileExtensions, $wmgFileExtensions );
$wgFileBlacklist = array_merge( $wgFileBlacklist, $wmgFileBlacklist );

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
	$wgFileExtensions[] = 'mp3'; // for Jay for fundraising files
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

	// To allow OpenOffice doc formats we need to not blacklist zip files
	$wgMimeTypeBlacklist = array_diff(
		$wgMimeTypeBlacklist,
		[ 'application/zip' ] );
}

# Hack for rsvg broken by security patch
$wgSVGConverters['rsvg-broken'] = '$path/rsvg-convert -w $width -h $height -o $output < $input';
if ( defined( 'HHVM_VERSION' ) ) {
	# Newer librsvg supports a sane security model by default and doesn't need our security patch
	$wgSVGConverters['rsvg-secure'] = '$path/rsvg-convert -u -w $width -h $height -o $output $input';

	// wikitech running on hhvm can use the standard setup.
	$wgSVGConverters['rsvg-wikitech'] = $wgSVGConverters['rsvg-secure'];
} else {
	# This converter will only work when rsvg has a suitable security patch
	$wgSVGConverters['rsvg-secure'] = '$path/rsvg-convert --no-external-files -w $width -h $height -o $output $input';

	// Legacy config for php5-based wikitech.  This (and everything to do with
	// rsvg-wikitech) can be removed when Silver is deprecated.
	$wgSVGConverters['rsvg-wikitech'] = '$path/rsvg-convert -w $width -h $height -o $output $input';
}
# ######################################################################
# Reverse proxy Configuration
# ######################################################################

$wgStatsdServer = $wmfLocalServices['statsd'];
if ( $wmfRealm === 'production' ) {
	if ( $wmgUseClusterSquid ) {
		$wgUseSquid = true;
		require "$wmfConfigDir/reverse-proxy.php";
	}
} elseif ( $wmfRealm === 'labs' ) {
	$wgStatsdMetricPrefix = 'BetaMediaWiki';
	if ( $wmgUseClusterSquid ) {
		$wgUseSquid = true;
		require "$wmfConfigDir/reverse-proxy-staging.php";
	}
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
		'*.wikivoyage.org',
		'www.mediawiki.org',
		'm.mediawiki.org',
		'wikimediafoundation.org',
		'advisory.wikimedia.org',
		'affcom.wikimedia.org',
		'auditcom.wikimedia.org',
		'boardgovcom.wikimedia.org',
		'board.wikimedia.org',
		'chair.wikimedia.org',
		'checkuser.wikimedia.org',
		'collab.wikimedia.org',
		'commons.wikimedia.org',
		'donate.wikimedia.org',
		'exec.wikimedia.org',
		'grants.wikimedia.org',
		'incubator.wikimedia.org',
		'internal.wikimedia.org',
		'login.wikimedia.org',
		'meta.wikimedia.org',
		'movementroles.wikimedia.org',
		'office.wikimedia.org',
		'otrs-wiki.wikimedia.org',
		'outreach.wikimedia.org',
		'quality.wikimedia.org',
		'searchcom.wikimedia.org',
		'spcom.wikimedia.org',
		'species.wikimedia.org',
		'steward.wikimedia.org',
		'strategy.wikimedia.org',
		'usability.wikimedia.org',
		'wikimania????.wikimedia.org',
		'wikimaniateam.wikimedia.org',
	];
}

wfLoadSkins( [ 'Vector', 'MonoBook', 'Modern', 'CologneBlue' ] );

if ( $wmgUseTimeless ) {
	wfLoadSkin( 'Timeless' ); // T154371
}

// The Print logo for Vector should use the same wordmark as Minerva
// This avoids duplicate config entries (T169732)
if (
	isset( $wgMinervaCustomLogos['copyright'] ) &&
	isset( $wgMinervaCustomLogos['copyright-height'] ) &&
	isset( $wgMinervaCustomLogos['copyright-width'] )
) {
	$wgVectorPrintLogo = [
		'width' => $wgMinervaCustomLogos['copyright-width'],
		'height' => $wgMinervaCustomLogos['copyright-height'],
		'url' => $wgMinervaCustomLogos['copyright'],
	];
}

// Grants and rights
// Note these have to be visible on all wikis, not just the ones the
// extension is enabled on, for proper display in OAuth pages and such.

// Adding Flaggedrevs rights so that they are available for globalgroups/staff rights - JRA 2013-07-22
$wgAvailableRights[] = 'autoreviewrestore';
$wgAvailableRights[] = 'stablesettings';
$wgAvailableRights[] = 'review';
$wgAvailableRights[] = 'unreviewedpages';
$wgAvailableRights[] = 'movestable';
$wgAvailableRights[] = 'validate';
$wgGrantPermissions['editprotected']['movestable'] = true;

// So that protection rights can be assigned to global groups
$wgAvailableRights[] = 'templateeditor';
$wgAvailableRights[] = 'editeditorprotected';
$wgAvailableRights[] = 'editextendedsemiprotected';
$wgAvailableRights[] = 'extendedconfirmed';
$wgGrantPermissions['editprotected']['templateeditor'] = true;
$wgGrantPermissions['editprotected']['editeditorprotected'] = true;
$wgGrantPermissions['editprotected']['editextendedsemiprotected'] = true;
$wgGrantPermissions['editprotected']['extendedconfirmed'] = true;

// Allow tboverride with editprotected, and tboverride-account with createaccount
$wgGrantPermissions['editprotected']['tboverride'] = true;
$wgGrantPermissions['createaccount']['tboverride-account'] = true;

// Adding Flow's rights so that they are available for global groups/staff rights
$wgAvailableRights[] = 'flow-create-board';
$wgAvailableRights[] = 'flow-edit-post';
$wgAvailableRights[] = 'flow-suppress';
$wgAvailableRights[] = 'flow-hide';
$wgAvailableRights[] = 'flow-delete';

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
$wgHooks['TitleQuickPermissions'][] = function ( Title $title, User $user, $action, &$errors, $doExpensiveQueries, $short ) {
	return ( !in_array( $action, [ 'deletedhistory', 'deletedtext' ] ) || !$title->inNamespaces( NS_FILE, NS_FILE_TALK ) || !$user->isAllowed( 'viewdeletedfile' ) );
};

if ( $wmgUseTimeline ) {
	include "$wmfConfigDir/timeline.php";
}
# Most probably only used by EasyTimeline which is conditionally included above
# but it is hard know whether there other use cases.
putenv( "GDFONTPATH=/srv/mediawiki/fonts" );

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

if ( $wmgUseGeSHi ) {
	wfLoadExtension( 'SyntaxHighlight_GeSHi' );
}

if ( $wmgUseDoubleWiki ) {
	wfLoadExtension( 'DoubleWiki' );
}

if ( $wmgUsePoem ) {
	wfLoadExtension( 'Poem' );
}

if ( $wmgUseUnicodeConverter ) {
	wfLoadExtension( 'UnicodeConverter' );
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

if ( $wmgUseFundraisingTranslateWorkflow ) {
	wfLoadExtension( 'FundraisingTranslateWorkflow' );
}

if ( $wmgUseGadgets ) {
	wfLoadExtension( 'Gadgets' );
	$wgGadgetsCacheType = CACHE_ACCEL;
}

if ( $wmgUseMwEmbedSupport ) {
	wfLoadExtension( 'MwEmbedSupport' );
}

if ( $wmgUseTimedMediaHandler ) {
	require_once "$IP/extensions/TimedMediaHandler/TimedMediaHandler.php";
	$wgTimedTextForeignNamespaces = [ 'commonswiki' => 102 ];
	if ( $wgDBname === 'commonswiki' ) {
		$wgTimedTextNS = 102;
	}
	// overwrite enabling of local TimedText namespace
	$wgEnableLocalTimedText = $wmgEnableLocalTimedText;

	// enable uploading MP3s
	$wgTmhEnableMp3Uploads = $wmgTmhEnableMp3Uploads;

	// enable transcoding on all wikis that allow uploads
	$wgEnableTranscode = $wgEnableUploads;

	$wgOggThumbLocation = false; // use ffmpeg for performance

	// tmh1/2 have 12 cores and need lots of shared memory
	// for avconv / ffmpeg2theora
	$wgTranscodeBackgroundMemoryLimit = 4 * 1024 * 1024; // 4GB
	$wgFFmpegThreads = 2;

	// HD transcodes of full-length films/docs/conference vids can
	// take several hours, and sometimes over 12. Bump up from default
	// 8 hour limit to 16 to avoid wasting the time we've already spent
	// when breaking these off.
	$wgTranscodeBackgroundTimeLimit = 16 * 3600;

	// ffmpeg tends to use about 175% CPU when threaded, so hits
	// say an 8-hour ulimit in 4-6 hours. This tends to cut
	// off very large files at very high resolution just before
	// they finish, wasting a lot of time.
	// Pad it back out so we don't waste that CPU time with a fail!
	$wgTranscodeBackgroundTimeLimit *= $wgFFmpegThreads;

	// Minimum size for an embed video player
	$wgMinimumVideoPlayerSize = $wmgMinimumVideoPlayerSize;

	// use new ffmpeg build w/ VP9 & Opus support
	$wgFFmpegLocation = '/usr/bin/ffmpeg';

	// The type of HTML5 player to use
	$wgTmhWebPlayer = $wmgTmhWebPlayer;

	// Enable the Beta Feature for trying out the new video player (see also the BF whitelist)
	$wgTmhUseBetaFeatures = true;
}

if ( $wmgUseUploadsLink ) {
	wfLoadExtension( 'UploadsLink' );
}

if ( $wmgUseUrlShortener ) {
	wfLoadExtension( 'UrlShortener' );
	$wgUrlShortenerTemplate = '/$1';
	$wgUrlShortenerServer = 'w.wiki';
	$wgUrlShortenerDBCluster = 'extension1';
	$wgUrlShortenerDBName = 'wikishared';
	$wgUrlShortenerDomainsWhitelist = [
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
	];
	$wgUrlShortenerReadOnly = true;
}

if ( $wmgPFEnableStringFunctions ) {
	$wgPFEnableStringFunctions = true;
}

if ( $wgDBname === 'mediawikiwiki' ) {
	wfLoadExtension( 'ExtensionDistributor' );
	$wgExtDistListFile = 'https://gerrit.wikimedia.org/mediawiki-extensions.txt';
	$wgExtDistAPIConfig = [
		'class' => 'GerritExtDistProvider',
		'apiUrl' => 'https://gerrit.wikimedia.org/r/projects/mediawiki%2F$TYPE%2F$EXT/branches',
		'tarballUrl' => 'https://extdist.wmflabs.org/dist/$TYPE/$EXT-$REF-$SHA.tar.gz',
		'tarballName' => '$EXT-$REF-$SHA.tar.gz',
		'repoListUrl' => 'https://gerrit.wikimedia.org/r/projects/?b=master&p=mediawiki/$TYPE/',
		'sourceUrl' => 'https://gerrit.wikimedia.org/r/mediawiki/$TYPE/$EXT.git',
	];

	// Current stable release
	$wgExtDistDefaultSnapshot = 'REL1_30';

	// Current development snapshot
	// $wgExtDistCandidateSnapshot = 'REL1_31';

	// Available snapshots
	$wgExtDistSnapshotRefs = [
		'master',
		'REL1_30',
		'REL1_29',
		'REL1_27',
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
	$wgHooks['SecurePoll_JumpUrl'][] = function ( $page, &$url ) {
		global $site, $lang;

		$url = wfAppendQuery( $url, [ 'site' => $site, 'lang' => $lang ] );
		return true;
	};
	$wgSecurePollCreateWikiGroups = [
		'securepollglobal' => 'securepoll-dblist-securepollglobal'
	];
}

// PoolCounter
if ( $wmgUsePoolCounter ) {
	include "$wmfConfigDir/PoolCounterSettings.php";
}

if ( $wmgUseScore ) {
	wfLoadExtension( 'Score' );
	$wgScoreSafeMode = false;
	$wgScoreFileBackend = $wmgScoreFileBackend;
	$wgScorePath = $wmgScorePath;
}

$wgHiddenPrefs[] = 'realname';

# Default address gets rejected by some mail hosts
$wgPasswordSender = 'wiki@wikimedia.org';

# e-mailing password based on e-mail address (T36386)
$wgPasswordResetRoutes['email'] = true;

if ( $wmgUseClusterFileBackend ) {
	# Cluster-dependent files for file backend
	require "{$wmfConfigDir}/filebackend.php";
} else {
	$wgUseInstantCommons = true;
}

if ( $wgDBname === 'labswiki' ) {
	$wgUseInstantCommons = true;
}

if ( $wmgUseClusterJobqueue ) {
	# Cluster-dependent files for job queue and job queue aggregator
	require $wmfRealm === 'labs'
		? "$wmfConfigDir/jobqueue-labs.php"
		: "$wmfConfigDir/jobqueue.php";
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
	'<img src="/static/images/wikimedia-button.png" ' .
		'srcset="' .
			'/static/images/wikimedia-button-1.5x.png 1.5x, ' .
			'/static/images/wikimedia-button-2x.png 2x' .
		'" ' .
		'width="88" height="31" alt="Wikimedia Foundation"/></a>';

# :SEARCH:

# All wikis are special and get Cirrus :)
# Must come *AFTER* PoolCounterSettings.php
wfLoadExtension( 'Elastica' );
require_once "$IP/extensions/CirrusSearch/CirrusSearch.php";
include "$wmfConfigDir/CirrusSearch-common.php";

// Various DB contention settings
if ( in_array( $wgDBname, [ 'testwiki', 'test2wiki', 'mediawikiwiki', 'commonswiki' ] ) ) {
	$wgSiteStatsAsyncFactor = 1;
}

$wgInvalidateCacheOnLocalSettingsChange = false;

// General Cache Epoch
$wgCacheEpoch = '20130601000000';

$wgThumbnailEpoch = '20130601000000';

$wgEnableUserEmail = true;
$wgNoFollowLinks = true; // In case the MediaWiki default changed, T44594

# XFF log for vandal tracking
$wgExtensionFunctions[] = function () {
	global $wmfUdp2logDest, $wgRequest;
	if (
		isset( $_SERVER['REQUEST_METHOD'] )
		&& $_SERVER['REQUEST_METHOD'] === 'POST'
		&& $wgRequest->getIP() !== '127.0.0.1'  # T129982
	) {
		$uri = ( ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] ) ? 'https://' : 'http://' ) .
			$_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$xff = isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : '';
		$logger = LoggerFactory::getInstance( 'xff' );
		// TODO: it would be nice to log this as actual structured data
		// instead of this ad-hoc tab delimited format
		$logger->info(
			gmdate( 'r' ) . "\t" .
			"$uri\t" .
			"$xff, {$_SERVER['REMOTE_ADDR']}\t" .
			( ( isset( $_REQUEST['wpSave'] ) && $_REQUEST['wpSave'] ) ? 'save' : '' )
		);
	}
};

// T26313, turn off minordefault on enwiki
if ( $wgDBname === 'enwiki' ) {
	$wgHiddenPrefs[] = 'minordefault';
}

if ( $wmgUseFooterContactLink ) {
	$wgHooks['SkinTemplateOutputPageBeforeExec'][] = function ( $sk, &$tpl ) {
		$contactLink = Html::element( 'a', [ 'href' => $sk->msg( 'contact-url' )->escaped() ],
			$sk->msg( 'contact' )->text() );
		$tpl->set( 'contact', $contactLink );
		$tpl->data['footerlinks']['places'][] = 'contact';
		return true;
	};
}
if ( $wmgUseFooterCodeOfConductLink ) {
	$wgHooks['SkinTemplateOutputPageBeforeExec'][] = function ( $sk, &$tpl ) {
		$contactLink = Html::element( 'a', [ 'href' => $sk->msg( 'wm-codeofconduct-url' )->escaped() ],
			$sk->msg( 'wm-codeofconduct' )->text() );
		$tpl->set( 'wm-codeofconduct', $contactLink );
		$tpl->data['footerlinks']['places'][] = 'wm-codeofconduct';
		return true;
	};
}

// T35186: turn off incomplete feature action=imagerotate
$wgAPIModules['imagerotate'] = 'ApiDisabled';

if ( $wmgUseDPL ) {
	wfLoadExtension( 'intersection' );
}

wfLoadExtension( 'Renameuser' );
$wgGroupPermissions['bureaucrat']['renameuser'] = $wmgAllowLocalRenameuser;

if ( $wmgUseSpecialNuke ) {
	wfLoadExtension( 'Nuke' );
}

if ( $wmgUseTorBlock ) {
	wfLoadExtension( 'TorBlock' );
	$wgTorLoadNodes = false;
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
	];
	$wgGroupPermissions['autoconfirmed'] = [
		'read' => true,
	];

	unset( $wgGroupPermissions['import'] );
	unset( $wgGroupPermissions['transwiki'] );

	$wgGroupPermissions['sysop'] = array_merge(
		$wgGroupPermissions['sysop'],
		[
			'editinterface' => false,
			'editusercss' => false,
			'edituserjs' => false,
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
		'#^(https?:)?//([.a-z0-9-]+\\.)?((wikimedia|wikipedia|wiktionary|wikiquote|wikibooks|wikisource|wikispecies|mediawiki|wikimediafoundation|wikinews|wikiversity|wikivoyage|wikidata|wmflabs)\.org'
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
	$wgExternalDiffEngine = 'wikidiff2';
	$wgDiff = false;
}

if ( $wmfRealm === 'labs' ) {
	$wgInterwikiCache = include_once "$wmfConfigDir/interwiki-labs.php";
} else {
	$wgInterwikiCache = include_once "$wmfConfigDir/interwiki.php";
}

$wgEnotifUseJobQ = true;

// Keep this true; it's just whether the feature is available at all, not the default
// setting. T142727
$wgEnotifMinorEdits = true;

// Username spoofing / mixed-script / similarity check detection
wfLoadExtension( 'AntiSpoof' );

// For transwiki import
ini_set( 'user_agent', 'Wikimedia internal server fetcher (noc@wikimedia.org' );
$wgHTTPImportTimeout = 50; // T155209

// CentralAuth
if ( $wmgUseCentralAuth ) {
	wfLoadExtension( 'CentralAuth' );

	$wgCentralAuthDryRun = false;
	$wgCentralAuthCookies = true;

	$wgCentralAuthUseEventLogging = true;
	$wgCentralAuthPreventUnattached = true;

	if ( $wmfRealm == 'production' ) {
		$wgCentralAuthRC[] = [
			'formatter' => 'IRCColourfulCARCFeedFormatter',
			'uri' => "udp://$wmgRC2UDPAddress:$wmgRC2UDPPort/#central\t",
		];
	}

	switch ( $wmfRealm ) {
	case 'production':
		// Production cluster
		$wmgSecondLevelDomainRegex = '/^\w+\.\w+\./';
		$wgCentralAuthAutoLoginWikis = $wmgCentralAuthAutoLoginWikis;
		$wgCentralAuthLoginWiki = $wmgCentralAuthLoginWiki;
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
			'test.wikimedia.beta.wmflabs.org' => 'testwiki',
			'commons.wikimedia.beta.wmflabs.org' => 'commonswiki',
			$wmfHostnames['wikidata'] => 'wikidatawiki',
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
		global $wgLocalDatabases, $IP, $wgSiteMatrixPrivateSites,
			$wgSiteMatrixFishbowlSites, $wgSiteMatrixClosedSites;

		$list = array_diff(
			$wgLocalDatabases,
			$wgSiteMatrixPrivateSites,
			$wgSiteMatrixFishbowlSites,
			$wgSiteMatrixClosedSites,
			MWWikiversions::readDbListFile( 'nonglobal' )
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
	if ( $wmfRealm === 'production' ) {
		$wgCentralAuthAutoCreateWikis[] = 'mediawikiwiki';
	}

	// Link global block blockers to user pages on Meta
	$wgCentralAuthGlobalBlockInterwikiPrefix = 'meta';

	// Require 8-byte password for staff. Set MinimumPasswordLengthToLogin
	// to 8 also, once staff have time to update.
	$wgCentralAuthGlobalPasswordPolicies['staff'] = [
		'MinimalPasswordLength' => 8,
		'MinimumPasswordLengthToLogin' => 1,
		'PasswordCannotMatchUsername' => true,
		'PasswordCannotBePopular' => PHP_INT_MAX,
	];

	// WMF Staff and two volunteers
	$wgCentralAuthGlobalPasswordPolicies['sysadmin'] = [
		'MinimalPasswordLength' => 8,
		'MinimumPasswordLengthToLogin' => 1,
		'PasswordCannotMatchUsername' => true,
		'PasswordCannotBePopular' => PHP_INT_MAX,
	];

	// See T104371
	$wgCentralAuthGlobalPasswordPolicies['steward'] = [
		'MinimalPasswordLength' => 8,
		'MinimumPasswordLengthToLogin' => 1,
		'PasswordCannotMatchUsername' => true,
	];

	// See [[m:Requests_for_comment/Password_policy_for_users_with_certain_advanced_permissions]]
	foreach ( [ 'global-sysop', 'global-interface-editor', 'wmf-researcher',
		'new-wikis-importer', 'ombudsman', 'founder' ] as $group
	) {
		$wgCentralAuthGlobalPasswordPolicies[$group] = [
			'MinimalPasswordLength' => 8,
			'MinimumPasswordLengthToLogin' => 1,
			'PasswordCannotMatchUsername' => true,
			'PasswordCannotBePopular' => 10000,
		];
	}

	$wgCentralAuthUseSlaves = true;
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
		'serverList' => $wmfLocalServices['search'],
	];
}

// taking it live 2006-12-15 brion
wfLoadExtension( 'DismissableSiteNotice' );
$wgDismissableSiteNoticeForAnons = true; // T59732
$wgMajorSiteNoticeID = '2';

/**
 * Get an array of groups (in $wmgPrivilegedGroups) that $username is part of
 *
 * @param string $username
 * @param User $user
 * @return array Any elevated/privileged groups the user is a member of
 */
function wfGetPrivilegedGroups( $username, $user ) {
	global $wmgUseCentralAuth, $wmgPrivilegedGroups;
	$groups = [];
	if ( $wmgUseCentralAuth && CentralAuthUser::getInstanceByName( $username )->exists() ) {
		$centralUser = CentralAuthUser::getInstanceByName( $username );
		$groups = array_intersect(
			array_merge(
				$wmgPrivilegedGroups,
				[ 'abusefilter-helper', 'founder', 'global-interface-editor', 'global-sysop', 'new-wikis-importer', 'ombudsman', 'staff', 'steward', 'sysadmin' ]
			),
			array_merge( $centralUser->getGlobalGroups(), $centralUser->getLocalGroups() )
		);
	} else {
		$groups = array_intersect( $wmgPrivilegedGroups, $user->getGroups() );
	}
	return $groups;
}

// log failed login attempts
$wgHooks['AuthManagerLoginAuthenticateAudit'][] = function ( $response, $user, $username ) {
	$guessed = false;
	if ( !$user && $username ) {
		$user = User::newFromName( $username );
		$guessed = true;
	}
	if ( $user && $response->status === \MediaWiki\Auth\AuthenticationResponse::FAIL ) {
		global $wgRequest;
		$headers = function_exists( 'apache_request_headers' ) ? apache_request_headers() : [];

		$privGroups = wfGetPrivilegedGroups( $username, $user );
		$logger = LoggerFactory::getInstance( 'badpass' );
		$logger->info( 'Login failed for {priv} {name} from {ip} - {xff} - {ua} - {geocookie}: {messagestr}', [
			'successful' => false,
			'groups' => implode( ', ', $privGroups ),
			'priv' => count( $privGroups ) ? 'elevated' : 'normal',
			'name' => $user->getName(),
			'ip' => $wgRequest->getIP(),
			'xff' => @$headers['X-Forwarded-For'],
			'ua' => @$headers['User-Agent'],
			'guessed' => $guessed,
			'messagestr' => $response->message->parse(),
			'geocookie' => $wgRequest->getCookie( 'GeoIP', '' ),
		] );
	}
};
// T150554 log successful attempts too
$wgHooks['AuthManagerLoginAuthenticateAudit'][] = function ( $response, $user, $username ) {
	if ( $response->status === \MediaWiki\Auth\AuthenticationResponse::PASS ) {
		global $wgRequest;
		$headers = function_exists( 'apache_request_headers' ) ? apache_request_headers() : [];

		$privGroups = wfGetPrivilegedGroups( $username, $user );
		$logger = LoggerFactory::getInstance( 'badpass' );
		$logger->info( 'Login succeeded for {priv} {name} from {ip} - {xff} - {ua} - {geocookie}', [
			'successful' => true,
			'groups' => implode( ', ', $privGroups ),
			'priv' => count( $privGroups ) ? 'elevated' : 'normal',
			'name' => $user->getName(),
			'ip' => $wgRequest->getIP(),
			'xff' => @$headers['X-Forwarded-For'],
			'ua' => @$headers['User-Agent'],
			'geocookie' => $wgRequest->getCookie( 'GeoIP', '' ),
		] );
	}
};

$wgHooks['PrefsEmailAudit'][] = function ( $user, $old, $new ) {
	if ( $user->isAllowed( 'delete' ) ) {
		global $wgRequest;
		$headers = function_exists( 'apache_request_headers' ) ? apache_request_headers() : [];

		$logger = LoggerFactory::getInstance( 'badpass' );
		$logger->info( "Email changed in prefs for sysop '" .
			$user->getName() .
			"' from '$old' to '$new'" .
			" - " . $wgRequest->getIP() .
			# " - " . serialize( $headers )
			" - " . @$headers['X-Forwarded-For'] .
			' - ' . @$headers['User-Agent']
		);
	}
	return true;
};

// log sysop password changes
$wgHooks['ChangeAuthenticationDataAudit'][] = function ( $req, $status ) {
	$user = User::newFromName( $req->username );
	$status = Status::wrap( $status );
	if ( $user->isAllowed( 'delete' ) && $req instanceof \MediaWiki\Auth\PasswordAuthenticationRequest ) {
		global $wgRequest;
		$headers = function_exists( 'apache_request_headers' ) ? apache_request_headers() : [];

		$logger = LoggerFactory::getInstance( 'badpass' );
		$logger->info( 'Password change in prefs for sysop {name}: {status} - {ip} - {xff} - {ua}', [
			'name' => $user->getName(),
			'status' => $status->isGood() ? 'ok' : $status->getWikiText( null, null, 'en' ),
			'ip' => $wgRequest->getIP(),
			'xff' => @$headers['X-Forwarded-For'],
			'ua' => @$headers['User-Agent'],
		] );
	}
};

if ( file_exists( '/etc/wikimedia-scaler' ) ) {
	$wgDisableOutputCompression = true;
}

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
case 'production'  :
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
	$wgCentralHost = "//{$wmfHostnames['meta']}";

	// Rely on GeoIP cookie for geolocation
	$wgCentralGeoScriptURL = false;

	// for banner loading
	if ( $wmfRealm === 'production' && $wgDBname === 'testwiki' ) {
		$wgCentralPagePath = "//test.wikipedia.org/w/index.php";
		$wgCentralSelectedBannerDispatcher = "//test.wikipedia.org/w/index.php?title=Special:BannerLoader";

		// No caching for banners on testwiki, so we can develop them there a bit faster - NeilK 2012-01-16
		// Never set this to zero on a highly trafficked wiki, there are server-melting consequences
		$wgNoticeBannerMaxAge = 0;
	} else {
		$wgCentralPagePath = "//{$wmfHostnames['meta']}/w/index.php";
		$wgCentralSelectedBannerDispatcher = "//{$wmfHostnames['meta']}/w/index.php?title=Special:BannerLoader";
	}
	// Relative URL which is hardcoded to HTTP 204 in Varnish config.
	$wgCentralBannerRecorder = "{$wgServer}/beacon/impression";

	// Allow only these domains to access CentralNotice data through the reporter
	$wgNoticeReporterDomains = 'https://donate.wikimedia.org';

	$wgCentralDBname = 'metawiki';
	$wgNoticeInfrastructure = false;
	if ( $wmfRealm == 'production' && $wgDBname === 'testwiki' ) {
		// test.wikipedia.org has its own central database:
		$wgCentralDBname = 'testwiki';
		$wgNoticeInfrastructure = true;
	} elseif ( $wgDBname === 'metawiki' ) {
		$wgNoticeInfrastructure = true;
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
	// wikimediafoundation.org/wiki/Template:HideBanners
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
}

// Load our site-specific l10n extension
wfLoadExtension( 'WikimediaMessages' );

if ( $wgDBname === 'enwiki' ) {
	// Please don't interfere with our hundreds of wikis ability to manage themselves.
	// Only use this shitty hack for enwiki. Thanks.
	// -- brion 2008-04-10
	$wgHooks['getUserPermissionsErrorsExpensive'][] = function ( &$title, &$user, $action, &$result ) {
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
	$wgHooks['TitleQuickPermissions'][] = function ( Title $title, User $user, $action, &$errors, $doExpensiveQueries, $short ) {
		return ( $action !== 'create' || $title->getNamespace() !== 118 || !$user->isAnon() );
	};
}

if ( $wmgUseCollection ) {
	// PediaPress / PDF generation
	include "$IP/extensions/Collection/Collection.php";
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

	$wgLicenseURL = "https://creativecommons.org/licenses/by-sa/3.0/";

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

if ( $wmgUseAdvancedSearch ) {
	wfLoadExtension( 'AdvancedSearch' );
}

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

if ( $wmgUsePdfHandler ) {
	wfLoadExtension( 'PdfHandler' );
	$wgPdfProcessor = '/usr/local/bin/mediawiki-firejail-ghostscript';
	$wgPdfPostProcessor = '/usr/local/bin/mediawiki-firejail-convert';
}

wfLoadExtension( 'WikiEditor' );

// Disable experimental things
$wgWikiEditorFeatures['preview'] =
	$wgWikiEditorFeatures['previewDialog'] =
	$wgWikiEditorFeatures['publish'] = [ 'global' => false, 'user' => true ]; // Hidden from prefs view
$wgHiddenPrefs[] = 'wikieditor-preview';
$wgHiddenPrefs[] = 'wikieditor-previewDialog';
$wgHiddenPrefs[] = 'wikieditor-publish';

$wgDefaultUserOptions['usebetatoolbar'] = 1;
$wgDefaultUserOptions['usebetatoolbar-cgd'] = 1;

# LocalisationUpdate
wfLoadExtension( 'LocalisationUpdate' );
$wgLocalisationUpdateDirectory = "/var/lib/l10nupdate/caches/cache-$wmgVersionNumber";
$wgLocalisationUpdateRepository = 'local';
$wgLocalisationUpdateRepositories['local'] = [
	'mediawiki' => '/var/lib/l10nupdate/mediawiki/core/%PATH%',
	'extension' => '/var/lib/l10nupdate/mediawiki/extensions/%NAME%/%PATH%',
	'skins' => '/var/lib/l10nupdate/mediawiki/skins/%NAME%/%PATH%',
];

if ( $wmgEnableLandingCheck ) {
	wfLoadExtension( 'LandingCheck' );

	$wgPriorityCountries = [
		// === Fundraising Chapers
		'DE', 'CH',

		// === Blacklisted countries
		'BY', 'CD', 'CI', 'CU', 'IQ', 'IR', 'KP', 'LB', 'LY', 'MM', 'SD', 'SO', 'SY', 'YE', 'ZW',
	];
	$wgLandingCheckPriorityURLBase = "//wikimediafoundation.org/wiki/Special:LandingCheck";
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

if ( $wmgUseSentry ) {
	require_once "$IP/extensions/Sentry/Sentry.php";
	$wgSentryDsn = $wmgSentryDsn;
	$wgSentryLogPhpErrors = false;
}

if ( $wmgUseTemplateStyles ) {
	wfLoadExtension( 'TemplateStyles' );
}

if ( $wmgUseLoginNotify && $wmgUseEcho ) {
	wfLoadExtension( 'LoginNotify' );
	$wgNotifyTypeAvailabilityByCategory['login-success']['web'] = false;

}

if ( $wmgUseCodeMirror ) {
	wfLoadExtension( 'CodeMirror' );
	$wgCodeMirrorBetaFeature = true;
}

$wgDefaultUserOptions['thumbsize'] = $wmgThumbsizeIndex;
$wgDefaultUserOptions['showhiddencats'] = $wmgShowHiddenCats;

$wgDefaultUserOptions['watchcreations'] = $wmgWatchPagesCreated;

// Temporary override: WMF is not hardcore enough to enable this.
// See T37785, T38316, T47022 about it.
if ( $wmgWatchlistDefault ) {
	$wgDefaultUserOptions['watchdefault'] = 1;
} else {
	$wgDefaultUserOptions['watchdefault'] = 0;
}

$wgDefaultUserOptions['enotifminoredits'] = $wmgEnotifMinorEditsUserDefault;
$wgDefaultUserOptions['enotifwatchlistpages'] = 0;

$wgDefaultUserOptions['usenewrc'] = 0;
$wgDefaultUserOptions['extendwatchlist'] = 0;

// ContributionTracking for handling PayPal redirects
if ( $wgUseContributionTracking ) {
	wfLoadExtension( 'ContributionTracking' );

	// the following variables will disable all donation forms and send users to a maintenance page
	$wgContributionTrackingFundraiserMaintenance = false;
	$wgContributionTrackingFundraiserMaintenanceUnsched = false;
}

if ( $wmgUseMassMessage ) {
	wfLoadExtension( 'MassMessage' );
}

if ( $wmgUseSandboxLink ) {
	wfLoadExtension( 'SandboxLink' );
}

if ( $wmgUseUploadWizard ) {
	wfLoadExtension( 'UploadWizard' );
	$wgUploadStashScalerBaseUrl = "//{$wmfHostnames['upload']}/$site/$lang/thumb/temp";
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
							'pd-old-70-1923',
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
		'licenses' => [
			'pd-old-70-1923' => [
				'msg' => 'mwe-upwiz-license-pd-old-70-1923',
				'templates' => [ 'PD-old-70-1923' ],
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
	require_once "$IP/extensions/GWToolset/GWToolset.php";
	$wgGWTFileBackend = 'local-multiwrite';
	$wgGWTFBMaxAge = '1 week';
	if ( $wmgUseClusterJobqueue ) {
		$wgJobTypeConf['gwtoolsetUploadMetadataJob'] = [ 'checkDelay' => true ] + $wgJobTypeConf['default'];
	}
	// extra throttling until the image scalers are more robust
	if ( class_exists( 'GWToolset\Config' ) ) {
		GWToolset\Config::$mediafile_job_throttle_default = 5; // 5 files per batch
	} else {
		$wgGWToolsetConfigOverrides['mediafile_job_throttle_default'] = 5;
	}
	$wgJobBackoffThrottling['gwtoolsetUploadMetadataJob'] = 5 / 3600; // 5 batches per hour
}

if ( $wmgUseMultimediaViewer ) {
	wfLoadExtension( 'MultimediaViewer' );
	$wgMediaViewerNetworkPerformanceSamplingFactor = $wmgMediaViewerNetworkPerformanceSamplingFactor;
	$wgMediaViewerDurationLoggingSamplingFactor = $wmgMediaViewerDurationLoggingSamplingFactor;
	$wgMediaViewerAttributionLoggingSamplingFactor = $wmgMediaViewerAttributionLoggingSamplingFactor;
	$wgMediaViewerDimensionLoggingSamplingFactor = $wmgMediaViewerDimensionLoggingSamplingFactor;
	$wgMediaViewerActionLoggingSamplingFactorMap = $wmgMediaViewerActionLoggingSamplingFactorMap;

	if ( isset( $wmgMediaViewerEnableByDefault ) ) {
		$wgMediaViewerEnableByDefault = $wmgMediaViewerEnableByDefault;
	}

	if ( isset( $wmgMediaViewerEnableByDefaultForAnonymous ) ) {
		$wgMediaViewerEnableByDefaultForAnonymous = $wmgMediaViewerEnableByDefaultForAnonymous;
	}

	if ( $wmgMediaViewerUseThumbnailGuessing ) {
		$wgMediaViewerUseThumbnailGuessing = true;
	}
}

if ( $wmgUsePopups || ( $wmgPopupsBetaFeature && $wmgUseBetaFeatures ) ) {
	wfLoadExtension( 'Popups' );

	// Make sure we don't enable as a beta feature if we are set to be enabled by default.
	$wgPopupsBetaFeature = $wmgPopupsBetaFeature && !$wmgUsePopups;
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
		'url' => $wgRestbaseServer,
		'domain' => $wgCanonicalServer,
		'forwardCookies' => false,
		'parsoidCompat' => false
	];
}

if ( $wmgUseParsoid ) {
	$wmgParsoidURL = $wmfLocalServices['parsoid'];

	// The wiki prefix to use
	$wgParsoidWikiPrefix = $wgDBname; // deprecated
	$wgVirtualRestConfig['modules']['parsoid'] = [
		'url' => $wmgParsoidURL,
		'prefix' => $wgDBname, // deprecated
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

	// RESTbase connection configuration
	if ( $wmgVisualEditorAccessRESTbaseDirectly ) {
		$wgVisualEditorRestbaseURL = "/api/rest_v1/page/html/";
		$wgVisualEditorFullRestbaseURL = "/api/rest_";
	}

	// Tab configuration
	if ( $wmgVisualEditorUseSingleEditTab ) {
		$wgVisualEditorUseSingleEditTab = true;
		$wgVisualEditorSingleEditTabSwitchTime = $wmgVisualEditorSingleEditTabSwitchTime;
		if ( $wmgVisualEditorSingleEditTabSecondaryEditor ) {
			$wgDefaultUserOptions['visualeditor-editor'] = 'wikitext';
		} else {
			$wgDefaultUserOptions['visualeditor-editor'] = 'visualeditor';
		}
		$wgDefaultUserOptions['T47877-buster'] = 1;
	} else {
		if ( $wmgVisualEditorSecondaryTabs ) {
			$wgVisualEditorTabPosition = 'after';
		}
	}
	if ( $wmgVisualEditorEnableWikitext ) {
		$wgVisualEditorEnableWikitext = true;
		$wgDefaultUserOptions['visualeditor-newwikitext'] = true;
	}

	// Namespace configuration
	$wgVisualEditorAvailableNamespaces = $wmgVisualEditorAvailableNamespaces;
	if ( !isset( $wgVisualEditorAvailableNamespaces ) ) {
		$wgVisualEditorAvailableNamespaces = []; // Set null to be an empty array to avoid fatals
	}

	// User access configuration
	if ( $wmgVisualEditorDefault ) {
		$wgDefaultUserOptions['visualeditor-enable'] = 1;
		$wgHiddenPrefs[] = 'visualeditor-enable'; // T50666
	} else {
		// Only show the beta-disable preference if the wiki is in 'beta'.
		$wgHiddenPrefs[] = 'visualeditor-betatempdisable';
	}
	if ( $wmgVisualEditorTransitionDefault ) {
		$wgVisualEditorTransitionDefault = true;
	}
	// T52000 - to remove once roll-out is complete.
	if ( $wmgVisualEditorDisableForAnons ) {
		$wgVisualEditorDisableForAnons = true;
	}

	// Feedback configuration
	if ( $wmgVisualEditorConsolidateFeedback ) {
		$wgVisualEditorFeedbackAPIURL = 'https://www.mediawiki.org/w/api.php';
		$wgVisualEditorFeedbackTitle = 'VisualEditor/Feedback';
	}

	// Enable for auto-created accounts
	if ( $wmgVisualEditorAutoAccountEnable ) {
		$wgVisualEditorAutoAccountEnable = true;
	}

	// Enable for a proportion of new accounts
	if ( $wmgVisualEditorNewAccountEnableProportion ) {
		$wgVisualEditorNewAccountEnableProportion = $wmgVisualEditorNewAccountEnableProportion;
	}
	// Enable for a proportion of non accounts ("IPs")
	if ( $wmgVisualEditorNonAccountEnableProportion ) {
		$wgVisualEditorNonAccountEnableProportion = $wmgVisualEditorNonAccountEnableProportion;
	}

	// Citoid
	wfLoadExtension( 'Citoid' );
	$wgCitoidServiceUrl = 'https://citoid.wikimedia.org/api';

	// Move the citation button from the primary toolbar into the "other" group
	if ( $wmgCiteVisualEditorOtherGroup ) {
		$wgCiteVisualEditorOtherGroup = true;
	}

	// Enable the wikitext mode Beta Feature for opt-in
	$wgVisualEditorEnableWikitextBetaFeature = true;

	// Enable the diff page visual diff Beta Feature for opt-in
	$wgVisualEditorEnableDiffPageBetaFeature = true;
}

if ( $wmgUseTemplateData ) { // T61702 - 2015-07-20
	// TemplateData enabled for all wikis - 2014-09-29
	wfLoadExtension( 'TemplateData' );
	// TemplateData GUI enabled for all wikis - 2014-11-06
	$wgTemplateDataUseGUI = true;
}

if ( $wmgUseGoogleNewsSitemap ) {
	wfLoadExtension( 'GoogleNewsSitemap' );
	$wgGNSMfallbackCategory = $wmgGNSMfallbackCategory;
	$wgGNSMcommentNamespace = $wmgGNSMcommentNamespace;
}

if ( $wmgUseCLDR ) {
	wfLoadExtension( 'cldr' );
}

# APC not available in CLI mode
if ( PHP_SAPI === 'cli' ) {
	$wgLanguageConverterCacheType = CACHE_NONE;
}

// DO NOT DISABLE WITHOUT CONTACTING PHILIPPE / LEGAL!
// Installed by Andrew, 2011-04-26
if ( $wmgUseDisableAccount ) {
	wfLoadExtension( 'DisableAccount' );
	$wgGroupPermissions['bureaucrat']['disableaccount'] = true;
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

if ( $wmgUseGuidedTour || $wmgUseGettingStarted ) {
	wfLoadExtension( 'GuidedTour' );
}

if ( $wmgUseMobileApp ) {
	wfLoadExtension( 'MobileApp' );
}

# Mobile related configuration

require "{$wmfConfigDir}/mobile.php";

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
	   '/static/images/poweredby_mediawiki_88x31.png';
$wgFooterIcons['poweredby']['mediawiki']['srcset'] =
	   '/static/images/poweredby_mediawiki_132x47.png 1.5x, ' .
	   '/static/images/poweredby_mediawiki_176x62.png 2x';

if ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' ) {
	$wgCookieSecure = true;
	$_SERVER['HTTPS'] = 'on'; // Fake this so MW goes into HTTPS mode
}
$wgVaryOnXFPForAPI = $wgVaryOnXFP = true;

$wgCookieExpiration = 30 * 86400;
$wgExtendedLoginCookieExpiration = 365 * 86400;

if ( $wmgUseMath ) {
	wfLoadExtension( 'Math' );

	$wgTexvc = '/usr/bin/texvc';
	$wgMathTexvcCheckExecutable = '/usr/bin/texvccheck';
	$wgMathCheckFiles = false;

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
	$wgExtensionFunctions[] = function () {
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
	require_once "$IP/extensions/Translate/Translate.php";

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
			'eqiad' => isset( $wmfAllServices['codfw']['search'] ) ? [ 'codfw' ] : [],
			'codfw' => isset( $wmfAllServices['eqiad']['search'] ) ? [ 'eqiad' ] : [],
		];
		foreach ( $wgTranslateClustersAndMirrors as $cluster => $mirrors ) {
			if ( !isset( $wmfAllServices[$cluster]['search'] ) ) {
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
					'servers' => array_map( function ( $host ) {
						return [
							'host' => $host,
							'port' => 9243,
							'transport' => 'Https',
						];
					}, $wmfAllServices[$cluster]['search'] ),
				],
				'mirrors' => $mirrors,
			];
		}
		unset( $wgTranslateClustersAndMirrors );
	}

	$wgTranslateWorkflowStates = $wmgTranslateWorkflowStates;
	$wgTranslateRcFilterDefault = $wmgTranslateRcFilterDefault;

	$wgTranslateUsePreSaveTransform = true; // T39304

	$wgEnablePageTranslation = true;
	$wgTranslateDelayedMessageIndexRebuild = true;

	// Deprecated language codes
	$wgTranslateBlacklist = [
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
		$wgHooks['TranslatePostInitGroups'][] = function ( &$cc ) {
			$id = 'wiki-translatable';
			$mg = new WikiMessageGroup( $id, 'translatable-messages' );
			$mg->setLabel( 'Interface' );
			$mg->setDescription( 'Messages used in the custom interface of this wiki' );
			$cc[$id] = $mg;
			return true;
		};
	};

	unset( $wgSpecialPages['FirstSteps'] );
	unset( $wgSpecialPages['ManageMessageGroups'] );
	unset( $wgSpecialPages['TranslationStats'] );

	$wgTranslateTranslationServices['Apertium'] = [
		'type' => 'cxserver',
		'host' => $wmfLocalServices['cxserver'],
		'timeout' => 3,
	];
}

if ( $wmgUseTranslationNotifications ) {
	wfLoadExtension( 'TranslationNotifications' );
	$wgNotificationUsername = 'Translation Notification Bot@Translation_Notification_Bot';
	$wgNotificationUserPassword = $wmgTranslationNotificationUserPassword;

	$wgTranslationNotificationsContactMethods['talkpage-elsewhere'] = true;
}

if ( $wmgUseVips ) {
	wfLoadExtension( 'VipsScaler' );
	$wgVipsThumbnailerHost = '10.2.1.21';
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
	wfLoadExtension( 'EducationProgram' );
	$wgEPSettings['dykCategory'] = $wmgEducationProgramDYKCat;
	$wgNamespaceProtection[/*EP_NS*/446] = [ 'ep-course' ]; // T112806 (security)
	$wgAddGroups['sysop'] = array_merge( $wgAddGroups['sysop'], [ 'eponline', 'epcampus', 'epinstructor', 'epcoordinator' ] ); // T163167 remove when  T123085  is resolved
	$wgRemoveGroups['sysop'] = array_merge( $wgRemoveGroups['sysop'], [ 'eponline', 'epcampus', 'epinstructor', 'epcoordinator' ] ); // T163167 remove  when  T123085  is resolved
}

if ( $wmgUseWikimediaShopLink ) {
	/**
	 * @param Skin $skin
	 * @param array $sidebar
	 * @return bool
	 */
	$wgHooks['SkinBuildSidebar'][] = function ( $skin, &$sidebar ) {
		$sidebar['navigation'][] = [
			'text'  => $skin->msg( 'wikimediashoplink-linktext' ),
			'href'  => '//shop.wikimedia.org',
			'title' => $skin->msg( 'wikimediashoplink-link-tooltip' ),
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
	wfLoadExtension( 'Echo' );

	if ( isset( $wgEchoConfig ) ) {
		// Eventlogging for Schema:EchoMail
		$wgEchoConfig['eventlogging']['EchoMail']['enabled'] = true;
		// Eventlogging for Schema:EchoInteraction
		$wgEchoConfig['eventlogging']['EchoInteraction']['enabled'] = true;
	} else {
		$wgEchoEventLoggingSchemas['EchoMail']['enabled'] = true;
		$wgEchoEventLoggingSchemas['EchoInteraction']['enabled'] = true;
	}

	$wgEchoEnableEmailBatch = $wmgEchoEnableEmailBatch;
	$wgEchoEmailFooterAddress = $wmgEchoEmailFooterAddress;
	$wgEchoNotificationIcons['site']['url'] = $wmgEchoSiteNotificationIconUrl;

	# Outgoing from and reply to address for Echo notifications extension
	$wgNotificationSender = $wmgNotificationSender;
	$wgNotificationSenderName = $wgSitename;
	$wgNotificationReplyName = 'No Reply';

	// Define the cluster database, false to use main database
	$wgEchoCluster = $wmgEchoCluster;

	// Allow for migration time functionality
	$wgRecentEchoInstall = true;

	// Whether to use job queue to process web and email notifications
	$wgEchoUseJobQueue = $wmgEchoUseJobQueue;

	// CentralAuth is extra check to be absolutely sure we don't enable on non-SUL
	// wikis.
	if ( $wmgUseCentralAuth && ( $wmgEchoUseCrossWikiBetaFeature || $wmgEchoCrossWikiByDefault ) ) {
		$wgEchoCrossWikiNotifications = true;
		// Whether to make the cross-wiki notifications beta feature available
		$wgEchoUseCrossWikiBetaFeature = $wmgEchoUseCrossWikiBetaFeature;
		if ( $wmgEchoCrossWikiByDefault ) {
			$wgDefaultUserOptions['echo-cross-wiki-notifications'] = 1;
		}
	}

	// Whether to show the footer notice
	$wgEchoShowFooterNotice = $wmgEchoShowFooterNotice;
	// URL to use in the footer notice
	$wgEchoFooterNoticeURL = $wmgEchoFooterNoticeURL;

	// Whether to make mention failure/success notifications available
	$wgEchoMentionStatusNotifications = $wmgEchoMentionStatusNotifications;

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
}

// Wikitech specific settings
if ( $wgDBname === 'labswiki' || $wgDBname === 'labtestwiki' ) {
	$wgEmailConfirmToEdit = true;
	$wgEnableCreativeCommonsRdf = true;

	// Don't depend on other DB servers
	$wgDefaultExternalStore = false;

	$wgGroupPermissions['contentadmin'] = $wgGroupPermissions['sysop'];
	$wgGroupPermissions['contentadmin']['editusercss'] = false;
	$wgGroupPermissions['contentadmin']['edituserjs'] = false;
	$wgGroupPermissions['contentadmin']['editinterface'] = false;
	$wgGroupPermissions['contentadmin']['tboverride'] = false;
	$wgGroupPermissions['contentadmin']['titleblacklistlog'] = false;
	$wgGroupPermissions['contentadmin']['override-antispoof'] = false;
	$wgGroupPermissions['contentadmin']['createaccount'] = false;

	// These are somehow not added as they are assigned to 'sysop' in the respective extension.json
	$wgGroupPermissions['contentadmin']['nuke'] = true;
	$wgGroupPermissions['contentadmin']['massmessage'] = true;
	$wgGroupPermissions['contentadmin']['spamblacklistlog'] = true;

	$wgMessageCacheType = 'memcached-pecl';

	if ( $wgDBname === 'labswiki' ) {
		$wgCookieDomain = "wikitech.wikimedia.org"; // TODO: Is this really necessary?
	} elseif ( $wgDBname === 'labtestwiki' ) {
		$wgCookieDomain = "labtestwikitech.wikimedia.org"; // TODO: Is this really necessary?
	}

	// Some settings specific to wikitech's extensions
	include "$wmfConfigDir/wikitech.php";
}

if ( $wmgUseThanks ) {
	wfLoadExtension( 'Thanks' );
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

	$wgFlowEventLogging = true;

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

if ( $wmgUseCodeEditorForCore || $wmgUseScribunto || $wmgZeroPortal ) {
	wfLoadExtension( 'CodeEditor' );
	$wgCodeEditorEnableCore = $wmgUseCodeEditorForCore;
	if ( $wgDBname === 'metawiki' ) {
		$wgHooks['CodeEditorGetPageLanguage'][] = function ( Title $title, &$lang ) {
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

	if ( defined( 'HHVM_VERSION' ) && !isset( $_REQUEST['forceprofile'] ) ) {
		// Disable Luasandbox's profiling feature unless 'forceprofile' is set.
		$wgScribuntoEngineConf['luasandbox']['profilerPeriod'] = false;
	}
}

if ( $wmgUseSubpageSortkey ) {
	wfLoadExtension( 'SubpageSortkey' );
	$wgSubpageSortkeyByNamespace = $wmgSubpageSortkeyByNamespace;
}

if ( $wmgUseGettingStarted ) {
	wfLoadExtension( 'GettingStarted' );
	$wgGettingStartedRedis = $wgObjectCaches['redis_master']['servers'][0];
	$wgGettingStartedRedisOptions['password'] = $wmgRedisPassword;
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
	$wgRelatedArticlesUseCirrusSearch = $wmgRelatedArticlesUseCirrusSearch;
	$wgRelatedArticlesOnlyUseCirrusSearch = false;
}

// Workaround for T142663 - override flat arrays
$wgExtensionFunctions[] = function () {
	global $wmgRelatedArticlesFooterWhitelistedSkins, $wgRelatedArticlesFooterWhitelistedSkins;

	$wgRelatedArticlesFooterWhitelistedSkins = $wmgRelatedArticlesFooterWhitelistedSkins;
};

if ( $wmgUseRelatedSites ) {
	wfLoadExtension( 'RelatedSites' );
}

if ( $wmgUseRevisionSlider ) {
	wfLoadExtension( 'RevisionSlider' );
}

if ( $wmgUseTwoColConflict ) {
	wfLoadExtension( 'TwoColConflict' );
}

if ( $wmgUseUserMerge ) {
	wfLoadExtension( 'UserMerge' );
	// Don't let users get deleted outright (T69789)
	$wgUserMergeEnableDelete = false;
}

if ( $wmgUseEventLogging ) {
	wfLoadExtension( 'EventLogging' );
	if ( $wgDBname === 'test2wiki' ) {
		// test2wiki has its own Schema: NS.
		$wgEventLoggingDBname = 'test2wiki';
		$wgEventLoggingSchemaApiUri = 'https://test2.wikipedia.org/w/api.php';
		$wgEventLoggingBaseUri = "{$wgServer}/beacon/dummy";
		$wgEventLoggingFile = "udp://$wmfUdp2logDest/EventLogging-$wgDBname";
	} else {
		// All other wikis reference metawiki.
		$wgEventLoggingBaseUri = $wgCanonicalServer . '/beacon/event';
		$wgEventLoggingDBname = 'metawiki';
		$wgEventLoggingFile = "{$wmfLocalServices['eventlogging']}/EventLogging";
		$wgEventLoggingSchemaApiUri = 'https://meta.wikimedia.org/w/api.php';
	}
	if ( $wgEventLoggingDBname === $wgDBname ) {
		// T47031
		$wgExtraNamespaces[470] = 'Schema';
		$wgExtraNamespaces[471] = 'Schema_talk';

		wfLoadExtension( 'CodeEditor' );
		$wgCodeEditorEnableCore = $wmgUseCodeEditorForCore; // For safety's sake
	}

	// Temporary hack for 'jsonschema' API module migration
	$wgEventLoggingSchemaIndexUri = $wgEventLoggingSchemaApiUri;

	// Depends on EventLogging
	if ( $wmgUseCampaigns ) {
		wfLoadExtension( 'Campaigns' );
	}

	// Depends on EventLogging
	if ( $wmgUseWikimediaEvents ) {
		wfLoadExtension( 'WikimediaEvents' );
		$wgWMEStatsdBaseUri = '/beacon/statsv';
		$wgWMETrackGeoFeatures = $wmgWMETrackGeoFeatures;
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
	if ( $wmgUseCodeEditorForCore || $wmgUseScribunto || $wmgZeroPortal ) {
		$wgULSNoImeSelectors[] = '.ace_editor textarea';
	}
	if ( $wmgUseTranslate && $wmgULSPosition === 'personal' ) {
		$wgTranslatePageTranslationULS = true;
	}

	$wgULSEventLogging = $wmgULSEventLogging;

	// Compact Language Links …

	// … as a beta feature (see T136677 for beta to stable)
	$wgULSCompactLanguageLinksBetaFeature = $wmgULSCompactLanguageLinksBetaFeature;

	// … as a stable feature
	$wgULSCompactLinksEnableAnon = $wmgULSCompactLinksEnableAnon;
	$wgULSCompactLinksForNewAccounts = $wmgULSCompactLinksForNewAccounts;
	$wgDefaultUserOptions['compact-language-links'] = 1;
}

if ( $wmgUsePerformanceInspector ) {
	wfLoadExtension( 'PerformanceInspector' );
}

if ( $wmgUseFileExporter ) {
	wfLoadExtension( 'FileExporter' );
}

if ( $wmgUseFileImporter ) {
	wfLoadExtension( 'FileImporter' );
	$wgFileImporterSourceSiteServices = [ $wmgUseFileImporter ];
}

if ( $wmgUseContentTranslation ) {
	wfLoadExtension( 'ContentTranslation' );

	// T76200: Public URL for cxserver instance
	$wgContentTranslationSiteTemplates['cx'] = '//cxserver.wikimedia.org/v1';

	$wgContentTranslationRESTBase = [
		'url' => $wgRestbaseServer,
		'domain' => $wgCanonicalServer,
		'forwardCookies' => false,
		'timeout' => 10000,
		'HTTPProxy' => false,
	];

	$wgContentTranslationTranslateInTarget = $wmgContentTranslationTranslateInTarget;

	$wgContentTranslationEventLogging = $wmgContentTranslationEventLogging;

	if ( $wmgContentTranslationCluster ) {
		$wgContentTranslationCluster = $wmgContentTranslationCluster;
	}

	$wgContentTranslationDatabase = 'wikishared';

	$wgContentTranslationCampaigns = $wmgContentTranslationCampaigns;

	$wgContentTranslationDefaultSourceLanguage = $wmgContentTranslationDefaultSourceLanguage;

	$wgContentTranslationCXServerAuth = [
		'algorithm' => 'HS256',
		// This is set in PrivateSettings.php
		'key' => $wmgContentTranslationCXServerAuthKey,
		'age' => '3600',
	];
}

if ( $wmgUseNewWikiDiff2Extension ) {
	$wgWikiDiff2MovedParagraphDetectionCutoff = 25;
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

if ( $wmgUseWikibaseRepo || $wmgUseWikibaseClient ) {
	include "$wmfConfigDir/Wikibase.php";
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

if ( $wmgZeroPortal || $wmgUseGraph || $wmgZeroBanner ) {
	wfLoadExtension( 'JsonConfig' );
}

if ( $wmgZeroPortal ) {
	wfLoadExtensions( [ 'ZeroBanner', 'ZeroPortal' ] );

	// zerowiki treats all logged-in users the same as anonymous, without giving them any extra rights
	// Only sysops and scripts get additional rights on zerowiki
	$zpUserRights = $wgGroupPermissions['user'];

	$wgGroupPermissions['*']['createtalk'] = false;
	$wgGroupPermissions['*']['createpage'] = false;
	$wgGroupPermissions['*']['writeapi'] = false;
	$wgGroupPermissions['user'] = $wgGroupPermissions['*'];

	// fixme: this should go into groupOverrides or groupOverrides2, with or without a '+'
	// 'sysop' => array( 'zero-edit', 'zero-script', 'zero-script-ips', 'jsonconfig-flush' ),
	// 'zeroscript' => array( 'zero-script', 'jsonconfig-flush' ),
	// 'zeroscriptips' => array( 'zero-script-ips', 'jsonconfig-flush' ),

	$wgGroupPermissions['sysop']['zero-edit'] = true;
	$wgGroupPermissions['sysop']['zero-script'] = true;
	$wgGroupPermissions['sysop']['zero-script-ips'] = true;
	$wgGroupPermissions['sysop']['jsonconfig-flush'] = true;
	$wgGroupPermissions['sysop'] = $wgGroupPermissions['sysop'] + $zpUserRights;

	$wgGroupPermissions['zeroscript']['zero-script'] = true;
	$wgGroupPermissions['zeroscript']['jsonconfig-flush'] = true;
	$wgGroupPermissions['zeroscript'] = $wgGroupPermissions['zeroscript'] + $zpUserRights;

	$wgGroupPermissions['zeroscriptips']['zero-script-ips'] = true;
	$wgGroupPermissions['zeroscriptips']['jsonconfig-flush'] = true;
	$wgGroupPermissions['zeroscriptips'] = $wgGroupPermissions['zeroscriptips'] + $zpUserRights;

	$wgGrantPermissions['zeroscript']['zero-script'] = true;
	$wgGrantPermissionGroups['zeroscript'] = 'administration';

	$wgZeroPortalImpersonateUser = 'Impersonator';

	// zerowiki needs to enable Common.css for restricted pages in order to
	// override default login page styling on Special:UserLogin
	$wgAllowSiteCSSOnRestrictedPages = true;

	unset( $zpUserRights );

	$wgUsersNotifiedOnAllChanges[] = 'ABaso(WMF)';
	$wgUsersNotifiedOnAllChanges[] = 'Dfoy';
	$wgUsersNotifiedOnAllChanges[] = 'Jhobs';
	$wgUsersNotifiedOnAllChanges[] = 'Yurik';
}

// Enable Tabular data namespace on Commons - T148745
// TODO: $wmgEnableMapData and $wmgEnableTabularData should probably be merged into one
if ( $wmgEnableTabularData ) {
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
		'license' => 'CC0-1.0',
		'isLocal' => false,
	];
	if ( $wgDBname === 'commonswiki' ) {
		// Ensure we have a stable cross-wiki title resolution
		// See JCSingleton::parseTitle()
		$wgJsonConfigInterwikiPrefix = "meta";
		$wgJsonConfigs['Tabular.JsonConfig']['store'] = true;
	} else {
		$wgJsonConfigInterwikiPrefix = "commons";
		$wgJsonConfigs['Tabular.JsonConfig']['remote'] = [
			'url' => 'https://commons.wikimedia.org/w/api.php'
		];
	}
	$wgJsonConfigEnableLuaSupport = true;
}

// Enable Map (GeoJSON) data namespace on Commons - T149548
// TODO: $wmgEnableMapData and $wmgEnableTabularData should probably be merged into one
if ( $wmgEnableMapData ) {
	// Safety: before extension.json, these values were initialized by JsonConfig.php
	if ( !isset( $wgJsonConfigModels ) ) {
		$wgJsonConfigModels = [];
	}
	if ( !isset( $wgJsonConfigs ) ) {
		$wgJsonConfigs = [];
	}
	// https://www.mediawiki.org/wiki/Extension:JsonConfig#Configuration
	$wgJsonConfigModels['Map.JsonConfig'] = 'JsonConfig\JCMapDataContent';
	$wgJsonConfigs['Map.JsonConfig'] = [
		'namespace' => 486,
		'nsName' => 'Data',
		// page name must end in ".map", and contain at least one symbol
		'pattern' => '/.\.map$/',
		'license' => 'CC0-1.0',
		'isLocal' => false,
	];
	if ( $wgDBname === 'commonswiki' ) {
		// Ensure we have a stable cross-wiki title resolution
		// See JCSingleton::parseTitle()
		$wgJsonConfigInterwikiPrefix = "meta";
		$wgJsonConfigs['Map.JsonConfig']['store'] = true;
	} else {
		$wgJsonConfigInterwikiPrefix = "commons";
		$wgJsonConfigs['Map.JsonConfig']['remote'] = [
			'url' => 'https://commons.wikimedia.org/w/api.php'
		];
	}
	$wgJsonConfigEnableLuaSupport = true;
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
			'wikimediafoundation.org',
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
	if ( in_array( $wgDBname, [ 'labswiki', 'labtestwiki', 'foundationwiki' ] ) ) {
		// Wikitech and its testing variant use local OAuth tables
		// WMF wiki uses OAuth tables of its own - T170301
		$wgMWOAuthCentralWiki = false;
	} else {
		$wgMWOAuthCentralWiki = 'metawiki';
		$wgMWOAuthSharedUserSource = 'CentralAuth';
	}
	$wgMWOAuthSecureTokenTransfer = true;

	if ( $wgMWOAuthCentralWiki === $wgDBname || $wgMWOAuthCentralWiki === false ) {
		// Management interfaces are available on the central wiki or wikis
		// that are using local OAuth tables
		$wgGroupPermissions['autoconfirmed']['mwoauthproposeconsumer'] = true;
		$wgGroupPermissions['autoconfirmed']['mwoauthupdateownconsumer'] = true;
		$wgGroupPermissions['oauthadmin']['mwoauthmanageconsumer'] = true;
		$wgOAuthGroupsToNotify = [ 'oauthadmin' ];
	}

	$wgHooks['OAuthReplaceMessage'][] = function ( &$msgKey ) {
		if ( $msgKey === 'mwoauth-form-privacypolicy-link' ) {
			$msgKey = 'wikimedia-oauth-privacy-link';
		}
		return true;
	};
}

if ( $wmgUsePetition ) {
	wfLoadExtension( 'Petition' );
}

// T15712
if ( $wmgUseJosa ) {
	wfLoadExtension( 'Josa' );
}

if ( $wmgUseParsoidBatchAPI ) {
	wfLoadExtension( 'ParsoidBatchAPI' );
}

if ( $wmgUseOATHAuth ) {
	wfLoadExtension( 'OATHAuth' );

	if ( $wmgOATHAuthDisableRight ) {
		$wgGroupPermissions['*']['oathauth-enable'] = false;
		foreach ( $wmgPrivilegedGroups as $group ) {
			if ( isset( $wgGroupPermissions[$group] ) ) {
				$wgGroupPermissions[$group]['oathauth-enable'] = true;
			}
		}
	}

	if ( $wmgUseCentralAuth ) {
		$wgOATHAuthAccountPrefix = 'Wikimedia';
		$wgOATHAuthDatabase = 'centralauth';
	}
}

if ( $wmgUseJADE ) {
	wfLoadExtension( 'JADE' );
}

if ( $wmgUseORES ) {
	wfLoadExtension( 'ORES' );
	$wgOresBaseUrl = 'http://ores.discovery.wmnet:8081/';
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
		'172.16.0.0/12', // Private
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
	$wgSquidServersNoPurge
);
if ( $wmgAllowLabsAnonEdits ) {
	// CI makes anonymous edits on some wikis, so don't block Labs (10.68.0.0/16).
	// The rest of 10.0.0.0/8 is ok to block.
	$wgSoftBlockRanges = array_merge( $wgSoftBlockRanges, [
		'10.0.0.0/10',
		'10.64.0.0/14',
		'10.69.0.0/16',
		'10.70.0.0/15',
		'10.72.0.0/13',
		'10.80.0.0/12',
		'10.96.0.0/11',
		'10.128.0.0/9',
	] );
} else {
	// Labs shouldn't be editing anonymously on most wikis, so we can block
	// anonymous edits from the whole /8.
	$wgSoftBlockRanges[] = '10.0.0.0/8';
}

// On Special:Version, link to useful release notes
$wgHooks['SpecialVersionVersionUrl'][] = function ( $wgVersion, &$versionUrl ) {
	$matches = [];
	preg_match( "/^(\d+\.\d+)(?:\.0-)?wmf\.?(\d+)?$/", $wgVersion, $matches );
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
	$wgCheckUserForceSummary = $wmgCheckUserForceSummary;
	if ( $wmgUseCentralAuth ) {
		// T128605
		// Only for CA wikis - will break stuff otherwise
		$wgCheckUserCAMultiLock = [
			'centralDB' => 'metawiki',
			'groups' => [ 'steward' ]
		];
	}
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

	$wgRCFeeds['default'] = [
		'formatter' => 'IRCColourfulRCFeedFormatter',
		'uri' => "udp://$wmgRC2UDPAddress:$wmgRC2UDPPort/$wmgRC2UDPPrefix",
		'add_interwiki_prefix' => false,
		'omit_bots' => false,
	];
}

// Confirmed can do anything autoconfirmed can.
$wgGroupPermissions['confirmed'] = $wgGroupPermissions['autoconfirmed'];
$wgGroupPermissions['confirmed']['skipcaptcha'] = true;

$wgImgAuthDetails = true;

$wgDefaultUserOptions['watchlistdays'] = $wmgWatchlistNumberOfDaysShow;

if ( $wmgUseWikidataPageBanner ) {
	wfLoadExtension( 'WikidataPageBanner' );
}

if ( $wmgUseQuickSurveys ) {
	wfLoadExtension( 'QuickSurveys' );
}

if ( $wmgUseEventBus ) {
	wfLoadExtension( 'EventBus' );
	$wgEventServiceUrl = "{$wmfLocalServices['eventbus']}/v1/events";

	// Configure RecentChange to send recentchange events to EventBus service.
	// Add a mapping from eventbus:// RCFeed URIs to the EventBusRCFeedEngine.
	$wgRCEngines['eventbus'] = 'EventBusRCFeedEngine';
	$wgRCFeeds['eventbus'] = [
		'formatter' => 'EventBusRCFeedFormatter',
		// Replace 'http://' in eventbus service endpoint with 'eventbus://'.
		// This is necessary so that the URI can properly map to an entry in
		// $wgRCEngines.  This hack can be removed after
		// https://gerrit.wikimedia.org/r/#/c/330833/ is merged.
		'uri' => str_replace( 'http://', 'eventbus://', $wgEventServiceUrl )
	];
}

if ( $wmgUseCapiunto ) {
	wfLoadExtension( 'Capiunto' );
}

if ( $wmgUseKartographer ) {
	wfLoadExtension( 'Kartographer' );
}

if ( $wmgUsePageViewInfo ) {
	wfLoadExtension( 'PageViewInfo' );
}

if ( $wmgUseCollaborationKit ) {
	wfLoadExtension( 'CollaborationKit' );
}

if ( $wgDBname === 'foundationwiki' ) {
	// Foundationwiki has raw html enabled. Attempt to prevent people
	// from accidentally violating the privacy policy with external scripts.
	// Note, we need all WMF domains in here due to Special:HideBanners
	// being loaded as an image from various domains on donation thank you
	// pages.
	$wgHooks['BeforePageDisplay'][] = function ( $out, $skin ) {
		$resp = $out->getRequest()->response();
		$cspHeader = "default-src *.wikimedia.org *.wikipedia.org *.wiktionary.org *.wikisource.org *.wikibooks.org *.wikiversity.org *.wikiquote.org *.wikinews.org www.mediawiki.org www.wikidata.org *.wikivoyage.org data: blob: 'self'; script-src *.wikimedia.org 'unsafe-inline' 'unsafe-eval' 'self'; style-src  *.wikimedia.org data: 'unsafe-inline' 'self'; report-uri /w/api.php?action=cspreport&format=none&reportonly=1&source=wmfwiki&";
		$resp->header( "X-Webkit-CSP-Report-Only: $cspHeader" );
		$resp->header( "X-Content-Security-Policy-Report-Only: $cspHeader" );
		$resp->header( "Content-Security-Policy-Report-Only: $cspHeader" );
	};
}

if ( $wmgUseParserMigration ) {
	wfLoadExtension( 'ParserMigration' );
	$wgParserMigrationTidiers = [
		[
			'driver' => 'RaggettInternalHHVM',
			'tidyConfigFile' => $wgTidyConf,
		],
		[
			'driver' => 'RemexHtml',
		],
	];
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
	wfLoadExtension( 'GlobalPreferences' );
}

if ( PHP_SAPI === 'cli' ) {
	wfLoadExtension( 'ActiveAbstract' );
}

if ( $wmfRealm === 'labs' ) {
	require "$wmfConfigDir/CommonSettings-labs.php";
}

# THIS MUST BE AFTER ALL EXTENSIONS ARE INCLUDED
#
# REALLY ... we're not kidding here ... NO EXTENSIONS AFTER

require "$wmfConfigDir/ExtensionMessages-$wmgVersionNumber.php";
