<?php
# WARNING: This file is publically viewable on the web. Do not put private data here.

#######################################################################
# CommonSettings.php is the main configuration file of the WMF cluster.
# It is included by LocalSettings.php
#
# This file contains settings common to all or many WMF wikis.
# Per-wiki configuration is done in InitialiseSettings.php (included
# into this file a little way down).
#######################################################################

use MediaWiki\Logger\LoggerFactory;

# Godforsaken hack to work around problems with the Squid caching changes...
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
	exit(1);
}
$multiVersion = MWMultiVersion::getInstance();

set_include_path( "$IP:/usr/local/lib/php:/usr/share/php" );

# Master datacenter
# The datacenter from which we serve traffic.
$wmfMasterDatacenter = 'eqiad';

### List of some service hostnames
# 'meta'    : meta wiki for user editable content
# 'upload'  : hostname where files are hosted
# 'wikidata': hostname for the data repository
# Whenever all realms/datacenters should use the same host, do not use
# $wmfHostnames but use the hardcoded hostname instead. A good example are the
# spam blacklists hosted on meta.wikimedia.org which you will surely want to
# reuse.
$wmfHostnames = [];
switch( $wmfRealm ) {
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
switch( $wmfRealm ) {
case 'labs':
	require "$wmfConfigDir/LabsServices.php";
	break;
case 'production':
default:
	require "$wmfConfigDir/ProductionServices.php";
}

# Must be set before InitialiseSettings.php:
$wmfUdp2logDest = $wmfLocalServices['udp2log'];

# Initialise wgConf
require( "$wmfConfigDir/wgConf.php" );
function wmfLoadInitialiseSettings( $conf ) {
	global $wmfConfigDir;
	require( "$wmfConfigDir/InitialiseSettings.php" );
}

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
	// 'wikimedia.org' // Removed 2008-09-30 by brion -- breaks codereview-proxy.wikimedia.org
	'meta.wikimedia.org', // Presumably needed to load meta spam list. Any others?
	'commons.wikimedia.org',
];

# Is this database listed in dblist?
# Note: $wgLocalDatabases set in wgConf.php.
# Note: must be done before calling $multiVersion functions other than getDatabase().
if ( array_search( $wgDBname, $wgLocalDatabases ) === false ) {
	# No? Load missing.php
	if ( $wgCommandLineMode ) {
		print "Database name $wgDBname is not listed in dblist\n";
	} else {
		require( "$wmfConfigDir/missing.php" );
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
	require( "$wmfConfigDir/InitialiseSettings.php" );

	$wikiTags = [];
	foreach ( [ 'private', 'fishbowl', 'special', 'closed', 'flow', 'flaggedrevs', 'small', 'medium',
			'large', 'wikimania', 'wikidata', 'wikidataclient', 'visualeditor-default',
			'commonsuploads', 'nonbetafeatures', 'group0', 'group1', 'group2', 'wikipedia', 'nonglobal',
			'wikitech', 'nonecho', 'mobilemainpagelegacy', 'clldefault'
		] as $tag ) {
		$dblist = MWWikiversions::readDbListFile( $tag );
		if ( in_array( $wgDBname, $dblist ) ) {
			$wikiTags[] = $tag;
		}
	}

	$dbSuffix = ( $site === 'wikipedia' ) ? 'wiki' : $site;
	$globals = $wgConf->getAll( $wgDBname, $dbSuffix,
		[
			'lang'    => $lang,
			'docRoot' => $_SERVER['DOCUMENT_ROOT'],
			'site'    => $site,
			'stdlogo' => "//{$wmfHostnames['upload']}/$site/$lang/b/bc/Wiki.png" ,
		], $wikiTags );

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
require( "$wmfConfigDir/PrivateSettings.php" );

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
$wgMaxUserDBWriteDuration = 5;
# Activate read-only mode for bots when lag is getting high.
# This should be lower than 'max lag' in the LBFactory conf.
$wgAPIMaxLagThreshold = 5;

ini_set( 'memory_limit', $wmgMemoryLimit );

# Rewrite commands given to wfShellWikiCmd() to use Het-Deploy
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
//   being cached after the switch to /w/static.php (T134368).
$wgResourceLoaderStorageVersion .= '-2';

$wgCacheDirectory = '/tmp/mw-cache-' . $wmgVersionNumber;
$wgGitInfoCacheDirectory = "$IP/cache/gitinfo";

// @var string|bool: E-mail address to send notifications to, or false to disable notifications.
$wmgAddWikiNotify = "newprojects@lists.wikimedia.org";

// Comment out the following lines to get the old-style l10n caching -- TS 2011-02-22
$wgLocalisationCacheConf['storeDirectory'] = "$IP/cache/l10n";
$wgLocalisationCacheConf['manualRecache'] = true;

// T29320: skip MessageBlobStore::clear(); handle via refreshMessageBlobs.php instead
$wgHooks['LocalisationCacheRecache'][] = function( $cache, $code, &$allData, &$purgeBlobs = true ) {
	$purgeBlobs = false;
	return true;
};

$wgFileStore['deleted']['directory'] = "/mnt/upload7/private/archive/$site/$lang";

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
$wgUseGzip = true;
$wgRCMaxAge = 30 * 86400;

$wgTmpDirectory     = '/tmp';

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
		],
	],
	'replication' => 'async'
];

$wgSessionsInObjectCache = true;
session_name( $lang . 'wikiSession' );

if ( $wgDBname === 'labswiki' ) {
	$wgMessageCacheType = 'memcached-pecl';
	$wgCookieDomain = "wikitech.wikimedia.org"; // TODO: Is this really necessary?
}

if ( $wgDBname === 'labtestwiki' ) {
	$wgMessageCacheType = 'memcached-pecl';
	$wgCookieDomain = "labtestwikitech.wikimedia.org"; // TODO: Is this really necessary?
}

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
	foreach ( [ 'bureaucrat', 'sysop', 'checkuser', 'oversight' ] as $group ) {
		$wgPasswordPolicy['policies'][$group]['MinimalPasswordLength'] = 8;
		$wgPasswordPolicy['policies'][$group]['MinimumPasswordLengthToLogin'] = 1;
		$wgPasswordPolicy['policies'][$group]['PasswordCannotBePopular'] = 10000;
	}

	$wgPasswordPolicy['policies']['bot']['MinimalPasswordLength'] = 1;
}

// Enforce password policy when users login on other wikis
if ( $wmgUseCentralAuth ) {
	$wgHooks['PasswordPoliciesForUser'][] = function( User $user, array &$effectivePolicy ) {
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
			[ 'bureaucrat', 'sysop', 'checkuser', 'oversight', 'interface-editor' ],
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
			'technician' => [ 'trwiki' ],
			'wikidata-staff' => [ 'wikidata' ],
		];

		foreach ( $enforceWikiGroups as $group => $wikis ) {
			foreach ( $wikis as $wiki ) {
				if ( isset( $attachInfo[$wiki]['groups'] )
					&& in_array( $group, $attachInfo[$wiki]['groups'] ) )
				{
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
}

$wgUseImageResize               = true;
$wgUseImageMagick               = true;
$wgImageMagickConvertCommand    = '/usr/local/bin/mediawiki-firejail-convert';
$wgSharpenParameter = '0x0.8'; # for IM>6.5, T26857

$wgFileBlacklist[] = 'txt';
$wgFileBlacklist[] = 'mht';

if ( $wmgUsePagedTiffHandler ) {
	include( $IP . '/extensions/PagedTiffHandler/PagedTiffHandler.php' );
}
$wgTiffUseTiffinfo = true;
$wgTiffMaxMetaSize = 1048576;

$wgMaxImageArea = 75e6; // 75MP
$wgMaxAnimatedGifArea = 75e6; // 75MP

$wgFileExtensions = array_merge( $wgFileExtensions, $wmgFileExtensions );

// Disable webp for now. T27397
$wgFileExtensions = array_values( array_diff( $wgFileExtensions, [ 'webp' ] ) );

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

	# Becausee I hate having to find print drivers -- tomasz
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
	// To allow OpenOffice doc formats we need to not blacklist zip files
	$wgMimeTypeBlacklist = array_diff(
		$wgMimeTypeBlacklist,
		[ 'application/zip' ] );
}

# Hack for rsvg broken by security patch
$wgSVGConverters['rsvg-broken'] = '$path/rsvg-convert -w $width -h $height -o $output < $input';
if ( defined( 'HHVM_VERSION' ) ) {
	# Newer librsvg supports a sane security model by default and doesn't need our security patch
	$wgSVGConverters['rsvg-secure'] = '$path/rsvg-convert -w $width -h $height -o $output $input';
} else {
	# This converter will only work when rsvg has a suitable security patch
	$wgSVGConverters['rsvg-secure'] = '$path/rsvg-convert --no-external-files -w $width -h $height -o $output $input';

	// Special config for wikitech which runs trusty (and therefore the new librsvg2-bin package),
	// but on PHP 5.5 (not HHVM)
	$wgSVGConverters['rsvg-wikitech'] = '$path/rsvg-convert -w $width -h $height -o $output $input';
}
#######################################################################
# Squid Configuration
#######################################################################

$wgStatsdServer = $wmfLocalServices['statsd'];
if ( $wmfRealm === 'production' ) {
	if ( $wmgUseClusterSquid ) {
		$wgUseSquid = true;
		require( "$wmfConfigDir/squid.php" );
	}
} elseif ( $wmfRealm === 'labs' ) {
	$wgStatsdMetricPrefix = 'BetaMediaWiki';
	if ( $wmgUseClusterSquid ) {
		$wgUseSquid = true;
		require( "$wmfConfigDir/squid-labs.php" );
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

// Grants and rights
// Note these have to be visible on all wikis, not just the ones the
// extension is enabled on, for proper display in OAuth pages and such.

// Adding Flaggedrevs rights so that they are available for globalgroups/staff rights - JRA 2013-07-22
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
$wgAvailableRights[] = 'flow-edit-post';
$wgAvailableRights[] = 'flow-suppress';
$wgAvailableRights[] = 'flow-hide';
$wgAvailableRights[] = 'flow-delete';
$wgAvailableRights[] = 'moodbar-admin'; // To allow global groups to include this right -AG

// Enable gather-hidelist for global user groups - JRA 4-1-2015 T94652
$wgAvailableRights[] = 'gather-hidelist';

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

// Enable a "viewdeletedfile" userright for [[m:Global deleted image review]] (T16801)
$wgAvailableRights[] = 'viewdeletedfile';
$wgHooks['TitleQuickPermissions'][] = function ( Title $title, User $user, $action, &$errors, $doExpensiveQueries, $short ) {
	return ( !in_array( $action, [ 'deletedhistory', 'deletedtext' ] ) || !$title->inNamespaces( NS_FILE, NS_FILE_TALK ) || !$user->isAllowed( 'viewdeletedfile' ) );
};

if ( $wmgUseTimeline ) {
	include( $IP . '/extensions/timeline/Timeline.php' );
	if ( $wgDBname == 'testwiki' || $wgDBname == 'mlwiki' ) {
		// FreeSansWMF has been generated from FreeSans and FreeSerif by using this script with fontforge:
		// Open("FreeSans.ttf");
		// MergeFonts("FreeSerif.ttf");
		// SetFontNames("FreeSans-WMF", "FreeSans WMF", "FreeSans WMF Regular", "Regular", "");
		// Generate("FreeSansWMF.ttf", "", 4 );
		$wgTimelineSettings->fontFile = 'FreeSansWMF.ttf';
	} elseif ( $lang == 'zh' ) {
		$wgTimelineSettings->fontFile = 'unifont-5.1.20080907.ttf';
	}
	$wgTimelineSettings->fileBackend = 'local-multiwrite';
	$wgTimelineSettings->ploticusCommand = '/usr/bin/ploticus';
	$wgTimelineSettings->epochTimestamp = '20130601000000';
}

putenv( "GDFONTPATH=/srv/mediawiki/fonts" );

if ( $wmgUseWikiHiero ) {
	include( $IP . '/extensions/wikihiero/wikihiero.php' );
}

include( $IP . '/extensions/SiteMatrix/SiteMatrix.php' );

// Config for sitematrix
$wgSiteMatrixFile = ( $wmfRealm === 'labs' ) ? "$IP/../langlist-labs" : "$IP/../langlist";
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
	include( "$wmfConfigDir/flaggedrevs.php" );
}

if ( $wmgUseCategoryTree ) {
	require( $IP . '/extensions/CategoryTree/CategoryTree.php' );
	$wgCategoryTreeCategoryPageMode = $wmgCategoryTreeCategoryPageMode;
	$wgCategoryTreeCategoryPageOptions = $wmgCategoryTreeCategoryPageOptions;
}

if ( $wmgUseProofreadPage ) {
	include( $IP . '/extensions/ProofreadPage/ProofreadPage.php' );
	if ( $wgDBname == 'dewikisource' ) {
		$wgGroupPermissions['*']['pagequality'] = true; # 27516
	} elseif ( $wgDBname == 'enwikisource' || $wgDBname == 'svwikisource' ) {
		$wgDefaultUserOptions['proofreadpage-showheaders'] = 1;
	}
}
if ( $wmgUseLabeledSectionTransclusion ) {
	wfLoadExtension( 'LabeledSectionTransclusion' );
}

if ( $wmgUseSpamBlacklist ) {
	include( $IP . '/extensions/SpamBlacklist/SpamBlacklist.php' );
	$wgBlacklistSettings = [
		'spam' => [
			'files' => [
				'https://meta.wikimedia.org/w/index.php?title=Spam_blacklist&action=raw&sb_ver=1'
			],
		],
	];
	$wgLogSpamBlacklistHits = true;
	$wgSpamBlacklistEventLogging = $wmgSpamBlacklistEventLogging;
}

include( $IP . '/extensions/TitleBlacklist/TitleBlacklist.php' );

$wgTitleBlacklistBlockAutoAccountCreation = false;

if ( $wmgUseGlobalTitleBlacklist ) {
	$wgTitleBlacklistSources = [
		'meta' => [
			'type' => TBLSRC_URL,
			'src'  => "https://meta.wikimedia.org/w/index.php?title=Title_blacklist&action=raw&tb_ver=1",
		],
	];

	$wgTitleBlacklistUsernameSources = $wmgTitleBlacklistUsernameSources;
}

if ( $wmgUseQuiz ) {
	include( "$IP/extensions/Quiz/Quiz.php" );
}

if ( $wmgUseFundraisingTranslateWorkflow ) {
	include( "$IP/extensions/FundraisingTranslateWorkflow/FundraisingTranslateWorkflow.php" );
}

if ( $wmgUseGadgets ) {
	include( "$IP/extensions/Gadgets/Gadgets.php" );
	$wgGadgetsCacheType = CACHE_ACCEL;
	$wgSpecialGadgetUsageActiveUsers = $wmgSpecialGadgetUsageActiveUsers;
}

if ( $wmgUseMwEmbedSupport ) {
	require_once( "$IP/extensions/MwEmbedSupport/MwEmbedSupport.php" );
}

if ( $wmgUseTimedMediaHandler ) {
	require_once( "$IP/extensions/TimedMediaHandler/TimedMediaHandler.php" );
	$wgTimedTextForeignNamespaces = [ 'commonswiki' => 102 ];
	if ( $wgDBname == 'commonswiki' ) {
		$wgTimedTextNS = 102;
	}
	//overwrite enabling of local TimedText namespace
	$wgEnableLocalTimedText = $wmgEnableLocalTimedText;

	//enable transcoding on all wikis that allow uploads
	$wgEnableTranscode = $wgEnableUploads;

	$wgOggThumbLocation = false; // use ffmpeg for performance

	//tmh1/2 have 12 cores and need lots of shared memory
	//for avconv / ffmpeg2theora
	$wgTranscodeBackgroundMemoryLimit = 4 * 1024 * 1024; // 4GB
	$wgFFmpegThreads = 2;

	// Minimum size for an embed video player
	$wgMinimumVideoPlayerSize = $wmgMinimumVideoPlayerSize;

	// use new ffmpeg build w/ VP9 & Opus support
	$wgFFmpegLocation = '/usr/bin/ffmpeg';

	// The type of HTML5 player to use
	$wgTmhWebPlayer = $wmgTmhWebPlayer;
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
		'wikidata\.org',
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
		'wikidata.org',
	];
	$wgUrlShortenerReadOnly = true;
}


if ( $wmgPFEnableStringFunctions ) {
	$wgPFEnableStringFunctions = true;
}

if ( $wgDBname == 'mediawikiwiki' ) {
	include( "$IP/extensions/ExtensionDistributor/ExtensionDistributor.php" );
	$wgExtDistListFile = 'https://gerrit.wikimedia.org/mediawiki-extensions.txt';
	$wgExtDistAPIConfig = [
		'class' => 'GerritExtDistProvider',
		'apiUrl' => 'https://gerrit.wikimedia.org/r/projects/mediawiki%2F$TYPE%2F$EXT/branches',
		'tarballUrl' => 'https://extdist.wmflabs.org/dist/$TYPE/$EXT-$REF-$SHA.tar.gz',
		'tarballName' => '$EXT-$REF-$SHA.tar.gz',
		'repoListUrl' => 'https://gerrit.wikimedia.org/r/projects/?p=mediawiki/$TYPE/',
	];

	// Current stable release
	$wgExtDistDefaultSnapshot = 'REL1_27';

	// Current development snapshot
	// $wgExtDistCandidateSnapshot = 'REL1_28';

	// When changing the Snapshot Refs please change the corresponding
	// extension distributor messages for mediawiki.org in JSON files
	// in WikimediaMessages/i18n/wikimedia/ too.
	$wgExtDistSnapshotRefs = [
		'master',
		'REL1_27',
		'REL1_26',
		'REL1_23',
	];

	// Use Graphite for popular list
	$wgExtDistGraphiteRenderApi = 'https://graphite.wikimedia.org/render';
}

if ( $wmgUseGlobalBlocking ) {
	include( $IP . '/extensions/GlobalBlocking/GlobalBlocking.php' );
	$wgGlobalBlockingDatabase = 'centralauth';
	$wgApplyGlobalBlocks = $wmgApplyGlobalBlocks;
	$wgGlobalBlockingBlockXFF = true; // Apply blocks to IPs in XFF (T25343)
}

include( $IP . '/extensions/TrustedXFF/TrustedXFF.php' );
$wgTrustedXffFile = "$wmfConfigDir/trusted-xff.cdb";

if ( $wmgUseContactPage ) {
	include( $IP . '/extensions/ContactPage/ContactPage.php' );
	$wgContactConfig['default'] = array_merge( $wgContactConfig['default'], $wmgContactPageConf );

	if ( $wgDBname === 'metawiki' ) {
		include( "$wmfConfigDir/LegalContactPages.php" );
		include( "$wmfConfigDir/AffComContactPages.php" );
		$wgContactConfig['stewards'] = [ // T98625
			'RecipientUser' => 'Wikimedia Stewards',
			'SenderEmail' => $wmgNotificationSender,
			'RequireDetails' => true,
			'IncludeIP' => true,
			'DisplayFormat' => 'vform',
			'AdditionalFields' => [
				'Text' => [
					'label-message' => 'emailmessage',
					'type' => 'textarea',
					'rows' => 20,
					'cols' => 80,
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
	include( $IP . '/extensions/SecurePoll/SecurePoll.php' );

	$wgSecurePollUseNamespace = $wmgSecurePollUseNamespace;
	$wgSecurePollScript = 'auth-api.php';
	$wgHooks['SecurePoll_JumpUrl'][] = function( $page, &$url ) {
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
	include( "$wmfConfigDir/PoolCounterSettings.php" );
}

if ( $wmgUseScore ) {
	include( "$IP/extensions/Score/Score.php" );
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
	require "{$wmfConfigDir}/filebackend-{$wmfRealm}.php";
} else {
	$wgUseInstantCommons = true;
}

if ( $wmgUseClusterJobqueue ) {
	# Cluster-dependent files for job queue and job queue aggregator
	require $wmfRealm === 'labs'
		? "$wmfConfigDir/jobqueue-labs.php"
		: "$wmfConfigDir/jobqueue.php";
}

if ( $wgDBname == 'nostalgiawiki' ) {
	# Link back to current version from the archive funhouse
	if ( ( isset( $_REQUEST['title'] ) && ( $title = $_REQUEST['title'] ) )
		|| ( isset( $_SERVER['PATH_INFO'] )  && ( $title = substr( $_SERVER['PATH_INFO'], 1 ) ) ) ) {
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
require_once( "$IP/extensions/Elastica/Elastica.php" );
require_once( "$IP/extensions/CirrusSearch/CirrusSearch.php" );
include( "$wmfConfigDir/CirrusSearch-common.php" );

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
$wgExtensionFunctions[] = function() {
	global $wmfUdp2logDest, $wgRequest;
	if ( isset( $_SERVER['REQUEST_METHOD'] ) && $_SERVER['REQUEST_METHOD'] === 'POST' ) {
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
		if ( $wgRequest->getIP() === '127.0.0.1' ) {
			$logger = LoggerFactory::getInstance( 'localhost' );
			// TODO: it would be nice to log this as actual structured data
			// instead of this ad-hoc tab delimited format
			$logger->info(
				gmdate( 'r' ) . "\t" .
				wfHostname() .
				"\t$xff, {$_SERVER['REMOTE_ADDR']}\t" .
				WebRequest::detectProtocol()
			);
		}
	}
};

// T26313, turn off minordefault on enwiki
if ( $wgDBname == 'enwiki' ) {
	$wgHiddenPrefs[] = 'minordefault';
}

if ( $wmgUseFooterContactLink ) {
	$wgHooks['SkinTemplateOutputPageBeforeExec'][] = function( $sk, &$tpl ) {
		$contactLink = Html::element( 'a', [ 'href' => $sk->msg( 'contact-url' )->escaped() ],
			$sk->msg( 'contact' )->text() );
		$tpl->set( 'contact', $contactLink );
		$tpl->data['footerlinks']['places'][] = 'contact';
		return true;
	};
}

// T35186: turn off incomplete feature action=imagerotate
$wgAPIModules['imagerotate'] = 'ApiDisabled';

if ( $wmgUseDPL ) {
	include( $IP . '/extensions/intersection/DynamicPageList.php' );
}

include( $IP . '/extensions/Renameuser/Renameuser.php' );
$wgGroupPermissions['bureaucrat']['renameuser'] = $wmgAllowLocalRenameuser;

if ( $wmgUseSpecialNuke ) {
	include( $IP . '/extensions/Nuke/Nuke.php' );
}

if ( $wmgUseTorBlock ) {
	include( "$IP/extensions/TorBlock/TorBlock.php" );
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
	include( "$IP/extensions/RSS/RSS.php" );
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

if ( $wgDBname == 'loginwiki' ) {
	$wgGroupPermissions['*'] = [
		'read' => true,
		'autocreateaccount' => true,
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

$wgProxyList = "$wmfConfigDir/../private/mwblocker.log";

$wgBrowserBlackList[] = '/^Lynx/';

$wgHiddenPrefs[] = 'prefershttps'; // T91352, T102245

if ( isset( $_REQUEST['captchabypass'] ) && $_REQUEST['captchabypass'] == $wmgCaptchaPassword ) {
	$wmgEnableCaptcha = false;
}

if ( $wmgEnableCaptcha ) {
	require( "$IP/extensions/ConfirmEdit/ConfirmEdit.php" );
	require( "$IP/extensions/ConfirmEdit/FancyCaptcha.php" );
	$wgGroupPermissions['autoconfirmed']['skipcaptcha'] = true;
	$wgCaptchaFileBackend = 'global-multiwrite';
	$wgCaptchaSecret = $wmgCaptchaSecret;
	$wgCaptchaDirectory = '/mnt/upload7/private/captcha';
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
}

if ( extension_loaded( 'wikidiff2' ) ) {
	$wgExternalDiffEngine = 'wikidiff2';
	$wgDiff = false;
}

$wgInterwikiCache = include_once( "$wmfConfigDir/interwiki.php" );

$wgEnotifUseJobQ = true;

// Username spoofing / mixed-script / similarity check detection
include $IP . '/extensions/AntiSpoof/AntiSpoof.php';

// For transwiki import
ini_set( 'user_agent', 'Wikimedia internal server fetcher (noc@wikimedia.org' );

// CentralAuth
if ( $wmgUseCentralAuth ) {
	include "$IP/extensions/CentralAuth/CentralAuth.php";

	$wgCentralAuthDryRun = false;
	$wgGroupPermissions['steward']['centralauth-rename'] = true;
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
	} elseif ( $wgDBname == 'commonswiki' && isset( $wgCentralAuthAutoLoginWikis["commons$wmgSecondLevelDomain"] ) ) {
		unset( $wgCentralAuthAutoLoginWikis["commons$wmgSecondLevelDomain"] );
		$wgCentralAuthCookieDomain = "commons$wmgSecondLevelDomain";
	} elseif ( $wgDBname == 'metawiki' ) {
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

	// temporary for testing -- legoktm 2015-07-02
	if ( $wgDBname === 'metawiki' ) {
		$wgCentralAuthEnableUserMerge = false;
	}

	// Only allow users with global accounts to login
	$wgCentralAuthStrict = true;

	// Create some local accounts as soon as the global registration happens
	$wgCentralAuthAutoCreateWikis = [ 'loginwiki', 'metawiki' ];
	if ( $wmfRealm === 'production' ) {
		$wgCentralAuthAutoCreateWikis[] = 'mediawikiwiki';
	}

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
	require_once( "$IP/extensions/GlobalCssJs/GlobalCssJs.php" );

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
	require_once "$IP/extensions/GlobalUserPage/GlobalUserPage.php";
	$wgGlobalUserPageAPIUrl = 'https://meta.wikimedia.org/w/api.php';
	$wgGlobalUserPageDBname = 'metawiki';
	$wgHooks['GlobalUserPageWikis'][] = 'wmfCentralAuthWikiList';
}

if ( $wmgUseApiFeatureUsage ) {
	require_once "$IP/extensions/Elastica/Elastica.php";
	require_once "$IP/extensions/ApiFeatureUsage/ApiFeatureUsage.php";
	$wgApiFeatureUsageQueryEngineConf = [
		'class' => 'ApiFeatureUsageQueryEngineElastica',
		'serverList' => $wmfLocalServices['search'],
	];
}

// taking it live 2006-12-15 brion
require( "$IP/extensions/DismissableSiteNotice/DismissableSiteNotice.php" );
$wgDismissableSiteNoticeForAnons = true; // T59732
$wgMajorSiteNoticeID = '2';

// pre-Authmanager code for logging failed login attempts
$wgHooks['LoginAuthenticateAudit'][] = function( $user, $pass, $retval ) {
	if ( $user->isAllowed( 'delete' ) && $retval != LoginForm::SUCCESS ) {
		global $wgRequest;
		$headers = apache_request_headers();

		switch( $retval ) {
		case LoginForm::WRONG_PASS:
		case LoginForm::EMPTY_PASS:
			$bit = 'Bad login attempt';
			break;
		case LoginForm::RESET_PASS:
			$bit = 'Login with temporary password';
			break;
		default:
			$bit = '???';
		}

		$logger = LoggerFactory::getInstance( 'badpass' );
		$logger->info( "$bit for sysop '" .
			$user->getName() . "' from " . $wgRequest->getIP() .
			# " - " . serialize( apache_request_headers() )
			" - " . @$headers['X-Forwarded-For'] .
			' - ' . @$headers['User-Agent']
		);
	}
	return true;
};

// log failed login attempts
$wgHooks['AuthManagerLoginAuthenticateAudit'][] = function( $response, $user, $username ) {
	$guessed = false;
	if ( !$user && $username ) {
		$user = User::newFromName( $username );
		$guessed = true;
	}
	if ( $user && $user->isAllowed( 'delete' ) && $response->status === \MediaWiki\Auth\AuthenticationResponse::FAIL ) {
		global $wgRequest;
		$headers = apache_request_headers();

		$logger = LoggerFactory::getInstance( 'badpass' );
		$logger->info( 'Login failed for sysop {name} from {ip} - {xff} - {ua}: {message}', [
			'name' => $user->getName(),
			'ip' => $wgRequest->getIP(),
			'xff' => @$headers['X-Forwarded-For'],
			'ua' => @$headers['User-Agent'],
			'guessed' => $guessed,
			'message' => $response->message->toString(),
		] );
	}
};

// Estimate users affected if we increase the minimum
// password length to 8 for privileged groups, i.e.
// T104370, T104371, T104372, T104373
$wgHooks['LoginAuthenticateAudit'][] = function( $user, $pass, $retval ) {
	global $wmgUseCentralAuth;
	if ( $retval == LoginForm::SUCCESS
		&& strlen( $pass ) < 8
	) {
		if ( $wmgUseCentralAuth ) {
			$central = CentralAuthUser::getInstance( $user );
			if ( $central->exists() && array_intersect(
				[ 'staff', 'sysadmin', 'steward', 'ombudsman', 'checkuser' ],
				array_merge(
					$central->getLocalGroups(),
					$central->getGlobalGroups()
				)
			) ) {
				$logger = LoggerFactory::getInstance( 'badpass' );
				$logger->info( "Login by privileged user '{$user->getName()}' with too short password" );
			}
		}
	}
	return true;
};

$wgHooks['PrefsEmailAudit'][] = function( $user, $old, $new ) {
	if ( $user->isAllowed( 'delete' ) ) {
		global $wgRequest;
		$headers = apache_request_headers();

		$logger = LoggerFactory::getInstance( 'badpass' );
		$logger->info( "Email changed in prefs for sysop '" .
			$user->getName() .
			"' from '$old' to '$new'" .
			" - " . $wgRequest->getIP() .
			# " - " . serialize( apache_request_headers() )
			" - " . @$headers['X-Forwarded-For'] .
			' - ' . @$headers['User-Agent']
		);
	}
	return true;
};

// pre-AuthManager code to log sysop password changes
$wgHooks['PrefsPasswordAudit'][] = function( $user, $pass, $status ) {
	if ( $user->isAllowed( 'delete' ) ) {
		global $wgRequest;
		$headers = apache_request_headers();

		$logger = LoggerFactory::getInstance( 'badpass' );
		$logger->info( "Password change in prefs for sysop '" .
			$user->getName() .
			"': $status" .
			" - " . $wgRequest->getIP() .
			# " - " . serialize( apache_request_headers() )
			" - " . @$headers['X-Forwarded-For'] .
			' - ' . @$headers['User-Agent']
		);
	}
	return true;
};

// log sysop password changes
$wgHooks['ChangeAuthenticationDataAudit'][] = function( $req, $status ) {
	$user = User::newFromName( $req->username );
	$status = Status::wrap( $status );
	if ( $user->isAllowed( 'delete' ) && $req instanceof \MediaWiki\Auth\PasswordAuthenticationRequest ) {
		global $wgRequest;
		$headers = apache_request_headers();

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

$wgMaxShellFileSize = 512 * 1024;
$wgMaxShellMemory = 1024 * 1024;
$wgMaxShellTime = 50;

// Use a cgroup for shell execution.
// This will cause shell execution to fail if the cgroup is not installed.
// If some misc server doesn't have the cgroup installed, you can create it
// with: mkdir -p -m777 /sys/fs/cgroup/memory/mediawiki/job
$wgShellCgroup = '/sys/fs/cgroup/memory/mediawiki/job';

switch( $wmfRealm ) {
case 'production'  :
	$wgImageMagickTempDir = '/tmp/magick-tmp';
	break;
case 'labs':
	$wgImageMagickTempDir = '/tmp/a/magick-tmp';
	break;
}

if ( $wmfRealm == 'labs' && file_exists( '/etc/wikimedia-transcoding' ) ) {
	require( "$wmfConfigDir/transcoding-labs.org" );
}

// Banner notice system
if ( $wmgUseCentralNotice ) {
	include "$IP/extensions/CentralNotice/CentralNotice.php";

	// for DNS prefetching
	$wgCentralHost = "//{$wmfHostnames['meta']}";

	// Rely on GeoIP cookie for geolocation
	$wgCentralGeoScriptURL = false;

	// for banner loading
	if ( $wgDBname == 'testwiki' ) {
		$wgCentralPagePath = "//test.wikipedia.org/w/index.php";
		$wgCentralSelectedBannerDispatcher = "//test.wikipedia.org/w/index.php?title=Special:BannerLoader";
	} else {
		$wgCentralPagePath = "//{$wmfHostnames['meta']}/w/index.php";
		$wgCentralSelectedBannerDispatcher = "//{$wmfHostnames['meta']}/w/index.php?title=Special:BannerLoader";
	}
	// Relative URL which is hardcoded to HTTP 204 in Varnish config.
	$wgCentralBannerRecorder = "{$wgServer}/beacon/impression";

	// Allow only these domains to access CentralNotice data through the reporter
	$wgNoticeReporterDomains = 'https://donate.wikimedia.org';

	$wgNoticeProject = $wmgNoticeProject;

	$wgCentralDBname = 'metawiki';
	if ( $wmfRealm == 'production' && $wgDBname == 'testwiki' ) {
		# test.wikipedia.org has its own central database:
		$wgCentralDBname = 'testwiki';
	}

	$wgCentralNoticeLoader = $wmgCentralNoticeLoader;

	$wgNoticeInfrastructure = false;
	if ( $wgDBname == 'metawiki' ) {
		$wgNoticeInfrastructure = true;
	}
	if( $wmfRealm == 'production' && $wgDBname == 'testwiki' ) {
		$wgNoticeInfrastructure = true;
	}

	// Set fundraising banners to use HTTPS on foundation wiki
	$wgNoticeFundraisingUrl = 'https://donate.wikimedia.org/wiki/Special:LandingCheck';

	// No caching for banners on testwiki, so we can develop them there a bit faster - NeilK 2012-01-16
	// Never set this to zero on a highly trafficked wiki, there are server-melting consequences
	if ( $wgDBname == 'testwiki' ) {
		$wgNoticeBannerMaxAge = 0;
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

// Load our site-specific l10n extensions
include "$IP/extensions/WikimediaMessages/WikimediaMessages.php";
if ( $wmgUseWikimediaLicenseTexts ) {
	include "$IP/extensions/WikimediaMessages/WikimediaLicenseTexts.php";
}

if ( $wgDBname == 'enwiki' ) {
	// Please don't interfere with our hundreds of wikis ability to manage themselves.
	// Only use this shitty hack for enwiki. Thanks.
	// -- brion 2008-04-10
	$wgHooks['getUserPermissionsErrorsExpensive'][] = function( &$title, &$user, $action, &$result ) {
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

if ( $wgDBname == 'enwiki' || $wgDBname == 'fawiki' ) {
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
	$wgCollectionMWServeURL = $wmfLocalServices['ocg'];
	// Use pediapress server for POD function (T73675)
	$wgCollectionCommandToServeURL = [
		'zip_post' => "{$wmfLocalServices['urldownloader']}|https://pediapress.com/wmfup/",
	];
	$wgCollectionPODPartners = [
		'pediapress' => [
			'name' => 'PediaPress',
			'url' => 'http://pediapress.com/',
			'posturl' => 'http://pediapress.com/api/collections/',
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
		// The following formats used the old mwlib renderer
		// which was shut down Oct 3, 2014.
		// They may eventually be reinstated when new OCG backends
		// are written for them.
	//	'epub' => 'EPUB',
	//	'odf' => 'ODT',
	//	'zim' => 'openZIM',
	//	'rl' => 'mwlib PDF', // replaced by [[:mw:OCG]] 29 Sep 2014
	];

	$wgLicenseURL = "http://creativecommons.org/licenses/by-sa/3.0/";

	$wgCollectionPortletForLoggedInUsersOnly = $wmgCollectionPortletForLoggedInUsersOnly;
	$wgCollectionArticleNamespaces = $wmgCollectionArticleNamespaces;
	$wgCollectionPortletFormats = $wmgCollectionPortletFormats;
}

# Various system to allow/prevent flooding
# (including exemptions for scheduled outreach events)
require( "$wmfConfigDir/throttle.php" );

if ( $wmgUseNewUserMessage ) {
	include "$IP/extensions/NewUserMessage/NewUserMessage.php";
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
}

# AbuseFilter
include "$IP/extensions/AbuseFilter/AbuseFilter.php";
include( "$wmfConfigDir/abusefilter.php" );
$wgAbuseFilterEmergencyDisableThreshold = $wmgAbuseFilterEmergencyDisableThreshold;
$wgAbuseFilterEmergencyDisableCount = $wmgAbuseFilterEmergencyDisableCount;
$wgAbuseFilterEmergencyDisableAge = $wmgAbuseFilterEmergencyDisableAge;

if ( $wmgUsePdfHandler ) {
	include ( "$IP/extensions/PdfHandler/PdfHandler.php" );
}

require( "$IP/extensions/WikiEditor/WikiEditor.php" );

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
	require_once(  "$IP/extensions/LandingCheck/LandingCheck.php" );

	$wgPriorityCountries = [
		// === Fundraising Chapers
		'DE', 'CH',

		// --- France and it's territories (per WMFr email 2012-06-13)
		//     Not a fundraising chapter in 2013+ due to FR regulations
		//'FR',
		//'GP', 'MQ', 'GF', 'RE', 'YT', 'PM',
                //'NC', 'PF', 'WF', 'BL', 'MF', 'TF',

		// === Blacklisted countries
                'BY', 'CD', 'CI', 'CU', 'IQ', 'IR', 'KP', 'LB', 'LY', 'MM', 'SD', 'SO', 'SY', 'YE', 'ZW',
	];
	$wgLandingCheckPriorityURLBase = "//wikimediafoundation.org/wiki/Special:LandingCheck";
	$wgLandingCheckNormalURLBase = "//donate.wikimedia.org/wiki/Special:LandingCheck";
}

if ( $wmgEnableFundraiserLandingPage ) {
	require_once( "$IP/extensions/FundraiserLandingPage/FundraiserLandingPage.php" );
}

if ( $wmgUseLiquidThreads || $wmgLiquidThreadsFrozen ) {
	require_once( "$wmfConfigDir/liquidthreads.php" );
}

if ( $wmgDonationInterface ) {
	// Regular DonationInterface should not be enabled on the WMF cluster.
	// So, only load i18n files for DonationInterface -awjrichards 1 November 2011
	require_once( "$IP/extensions/DonationInterface/donationinterface_langonly.php" );
}

if ( $wmgUseGlobalUsage ) {
	require_once( "$IP/extensions/GlobalUsage/GlobalUsage.php" );
	$wgGlobalUsageDatabase = 'commonswiki';
	$wgGlobalUsageSharedRepoWiki = 'commonswiki';
	$wgGlobalUsagePurgeBacklinks = true;
}

if ( $wmgUseLivePreview ) {
	$wgDefaultUserOptions['uselivepreview'] = 1;
}

$wgDefaultUserOptions['thumbsize'] = $wmgThumbsizeIndex;
$wgDefaultUserOptions['showhiddencats'] = $wmgShowHiddenCats;

if( $wgDBname == 'commonswiki' ) {
	$wgDefaultUserOptions['watchcreations'] = 0;
} else {
	$wgDefaultUserOptions['watchcreations'] = 1;
}

// Temporary override: WMF is not hardcore enough to enable this.
// See T37785, T38316, T47022 about it.
$wgDefaultUserOptions['watchdefault'] = 0;
$wgDefaultUserOptions['enotifwatchlistpages'] = 0;
$wgDefaultUserOptions['usenewrc'] = 0;
$wgDefaultUserOptions['extendwatchlist'] = 0;

# # Hack to block emails from some idiot user who likes 'The Joker' --Andrew 2009-05-28
$wgHooks['EmailUser'][] = function ( &$to, &$from, &$subject, &$text ) {
	$blockedAddresses = [ 'the4joker@gmail.com', 'randomdude5555@gmail.com', 'siyang.li@yahoo.com', 'johnnywiki@gmail.com', 'wikifreedomfighter@googlemail.com' ];
	return !in_array( $from->address, $blockedAddresses );
};

// ContributionTracking for handling PayPal redirects
if ( $wgUseContributionTracking ) {
	include( "$IP/extensions/ContributionTracking/ContributionTracking.php" );
	include( "$wmfConfigDir/contribution-tracking-setup.php" );
	$wgContributionTrackingPayPalIPN = "https://civicrm.wikimedia.org/fundcore_gateway/paypal";
	$wgContributionTrackingPayPalRecurringIPN = "https://civicrm.wikimedia.org/IPNListener_Recurring.php";
	$wgContributionTrackingUTMKey = true;

	// the following variables will disable all donation forms and send users to a maintenance page
	$wgContributionTrackingFundraiserMaintenance = false;
	$wgContributionTrackingFundraiserMaintenanceUnsched = false;
}

if ( $wmgUseMassMessage ) {
	require_once( "$IP/extensions/MassMessage/MassMessage.php" );
	$wgNamespacesToPostIn = $wmgNamespacesToPostIn;
	$wgAllowGlobalMessaging = $wmgAllowGlobalMessaging;
}

if ( $wmgUseSandboxLink ) {
	require_once "$IP/extensions/SandboxLink/SandboxLink.php";
}

if ( $wmgUseUploadWizard ) {
	require_once( "$IP/extensions/UploadWizard/UploadWizard.php" );
	$wgUploadStashScalerBaseUrl = "//{$wmfHostnames['upload']}/$site/$lang/thumb/temp";
	$wgUploadWizardConfig = [
		# 'debug' => true,
		'autoAdd' => [
			'categories' => [
				'Uploaded with UploadWizard',
			],
		],
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

	if ( $wgDBname == 'testwiki' ) {
		$wgUploadWizardConfig['feedbackPage'] = 'Prototype_upload_wizard_feedback';
		$wgUploadWizardConfig["missingCategoriesWikiText"] = '<p><span class="errorbox"><b>Hey, no categories?</b></span></p>';
		unset( $wgUploadWizardConfig['fallbackToAltUploadForm'] );
	} elseif ( $wgDBname == 'commonswiki' ) {
		$wgUploadWizardConfig['feedbackPage'] = 'Commons:Upload_Wizard_feedback'; # Set by neilk, 2011-11-01, per erik
		$wgUploadWizardConfig["missingCategoriesWikiText"] = "{{subst:unc}}";
		$wgUploadWizardConfig['blacklistIssuesPage'] = 'Commons:Upload_Wizard_blacklist_issues'; # Set by neilk, 2011-11-01, per erik
		$wgUploadWizardConfig['flickrBlacklistPage'] = 'User:FlickreviewR/bad-authors';
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
	require_once( "$IP/extensions/CommonsMetadata/CommonsMetadata.php" );
	$wgCommonsMetadataSetTrackingCategories = true;
	$wgCommonsMetadataForceRecalculate = $wmgCommonsMetadataForceRecalculate;
}

if ( $wmgUseGWToolset ) {
	require_once( "$IP/extensions/GWToolset/GWToolset.php" );
	$wgGWTFileBackend = 'local-multiwrite';
	$wgGWTFBMaxAge = '1 week';
	if ( $wmgUseClusterJobqueue ) {
		$wgJobTypeConf['gwtoolsetUploadMetadataJob'] = [ 'checkDelay' => true ] + $wgJobTypeConf['default'];
	}
	// extra throttling until the image scalers are more robust
	GWToolset\Config::$mediafile_job_throttle_default = 5; // 5 files per batch
	$wgJobBackoffThrottling['gwtoolsetUploadMetadataJob'] = 5 / 3600; // 5 batches per hour
}

if ( $wmgUseMultimediaViewer ) {
	require_once( "$IP/extensions/MultimediaViewer/MultimediaViewer.php" );
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

if ( $wmgUseImageMetrics ) {
	require_once( "$IP/extensions/ImageMetrics/ImageMetrics.php" );
	$wgImageMetricsSamplingFactor = $wmgImageMetricsSamplingFactor;
	$wgImageMetricsLoggedinSamplingFactor = $wmgImageMetricsLoggedinSamplingFactor;
	$wgImageMetricsCorsSamplingFactor = $wmgImageMetricsCorsSamplingFactor;
}

if ( $wmgUsePopups || ( $wmgPopupsBetaFeature && $wmgUseBetaFeatures ) ) {
	require_once( "$IP/extensions/Popups/Popups.php" );

	// Make sure we don't enable as a beta feature if we are set to be enabled by default.
	$wgPopupsBetaFeature = $wmgPopupsBetaFeature && !$wmgUsePopups;
	$wgPopupsExperiment = $wmgPopupsExperiment;
	$wgPopupsExperimentConfig = $wmgPopupsExperimentConfig;
}

if ( $wmgUseRestbaseUpdateJobs ) {
	require_once( "$IP/extensions/RestBaseUpdateJobs/RestBaseUpdateJobs.php" );
	$wgRestbaseServer = $wmgRestbaseServer;
}

if ( !isset( $wgVirtualRestConfig ) && ( $wmgUseRestbaseVRS || $wmgUseParsoid ) ) {
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
		'url' => $wmgRestbaseServer,
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

if ( $wmgUseVisualEditor ) {
	require_once( "$IP/extensions/VisualEditor/VisualEditor.php" );

	// RESTBase connection configuration is done by $wmfUseRestbaseVRS above.
	// Parsoid connection configuration is done by $wmgUseParsoid above.
	// At least one of these should be set if you want to use Visual Editor.

	// RESTbase connection configuration
	if ( $wmgVisualEditorAccessRESTbaseDirectly ) {
		// HACK: $wgServerName is not available yet at this point, it's set by Setup.php
		// so use a hook
		$wgExtensionFunctions[] = function () {
			global $wgVisualEditorRestbaseURL, $wgVisualEditorFullRestbaseURL;
			$wgVisualEditorRestbaseURL = "/api/rest_v1/page/html/";
			$wgVisualEditorFullRestbaseURL = "/api/rest_";
		};
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
	require_once "$IP/extensions/Citoid/Citoid.php";
	$wgCitoidServiceUrl = 'https://citoid.wikimedia.org/api';

	// Move the citation button from the primary toolbar into the "other" group
	if ( $wmgCiteVisualEditorOtherGroup ) {
		$wgCiteVisualEditorOtherGroup = true;
	}
}

if ( $wmgUseTemplateData ) { // T61702 - 2015-07-20
	// TemplateData enabled for all wikis - 2014-09-29
	require_once( "$IP/extensions/TemplateData/TemplateData.php" );
	// TemplateData GUI enabled for all wikis - 2014-11-06
	$wgTemplateDataUseGUI = true;
}

if ( $wmgUseGoogleNewsSitemap ) {
	include( "$IP/extensions/GoogleNewsSitemap/GoogleNewsSitemap.php" );
	$wgGNSMfallbackCategory = $wmgGNSMfallbackCategory;
	$wgGNSMcommentNamespace = $wmgGNSMcommentNamespace;
}

if ( $wmgUseCLDR ) {
	require_once( "$IP/extensions/cldr/cldr.php" );
}

# APC not available in CLI mode
if ( PHP_SAPI === 'cli' ) {
	$wgLanguageConverterCacheType = CACHE_NONE;
}

# Style version appendix
# Shouldn't be needed much in 1.17 due to ResourceLoader, but some legacy things still need it
$wgStyleVersion .= '-4';

// DO NOT DISABLE WITHOUT CONTACTING PHILIPPE / LEGAL!
// Installed by Andrew, 2011-04-26
if ( $wmgUseDisableAccount ) {
	require_once( "$IP/extensions/DisableAccount/DisableAccount.php" );
	$wgGroupPermissions['bureaucrat']['disableaccount'] = true;
}

if ( $wmgUseIncubator ) {
	require_once( "$IP/extensions/WikimediaIncubator/WikimediaIncubator.php" );
	$wmincClosedWikis = $wgSiteMatrixClosedSites;
}

if ( $wmgUseWikiLove ) {
	require_once( "$IP/extensions/WikiLove/WikiLove.php" );
	$wgWikiLoveLogging = true;
	if ( $wmgWikiLoveDefault ) {
		$wgDefaultUserOptions['wikilove-enabled'] = 1;
	}
}

if ( $wmgUseGuidedTour || $wmgUseGettingStarted ) {
	require_once( "$IP/extensions/GuidedTour/GuidedTour.php" );
}

if ( $wmgUseMoodBar ) {
	require_once( "$IP/extensions/MoodBar/MoodBar.php" );
	$wgMoodBarCutoffTime = $wmgMoodBarCutoffTime;
	$wgMoodBarBlackoutInterval = [ '20120614000000,20120629000000' ];
	$wgMoodBarConfig['privacyUrl'] = "//wikimediafoundation.org/wiki/Feedback_policy";
	$wgMoodBarConfig['feedbackDashboardUrl'] = "$wgServer/wiki/Special:FeedbackDashboard";

	$wgMoodBarConfig['infoUrl'] = $wmgMoodBarInfoUrl;
	$wgMoodBarConfig['enableTooltip'] = $wmgMoodBarEnableTooltip;
}

if ( $wmgUseMobileApp ) {
	require_once( "$IP/extensions/MobileApp/MobileApp.php" );
}

# Mobile related configuration

require "{$wmfConfigDir}/mobile.php";

# MUST be after MobileFrontend initialization
if ( $wmgEnableTextExtracts ) {
	require_once( "$IP/extensions/TextExtracts/TextExtracts.php" );
	if ( isset( $wgExtractsRemoveClasses ) ) {
		// Back-compat for pre-extension.json
		$wgExtractsRemoveClasses = array_merge( $wgExtractsRemoveClasses, $wmgExtractsRemoveClasses );
	} else {
		$wgExtractsRemoveClasses = $wmgExtractsRemoveClasses;
	}
	$wgExtractsExtendOpenSearchXml = $wmgExtractsExtendOpenSearchXml;
}

if ( $wmgUseSubPageList3 ) {
	include( "$IP/extensions/SubPageList3/SubPageList3.php" );
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

if ( $wmgUseMath ) {
	wfLoadExtension ( "Math" );

	$wgTexvc = '/usr/bin/texvc';
	$wgMathTexvcCheckExecutable = '/usr/bin/texvccheck';
	$wgMathCheckFiles = false;

	if ( $wgDBname === 'hewiki' ) {
		$wgDefaultUserOptions['math'] = 0;
	} elseif ( $wmgUseMathML && $wmgUseRestbaseVRS ) {
		$wgDefaultUserOptions['math'] = 'mathml';
	}
	$wgMathDirectory   = '/mnt/upload7/math'; // just for sanity
	$wgUseMathJax      = true;
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
	require_once( "$IP/extensions/Babel/Babel.php" );
	$wgBabelCategoryNames = $wmgBabelCategoryNames;
	$wgBabelMainCategory = $wmgBabelMainCategory;
	$wgBabelDefaultLevel = $wmgBabelDefaultLevel;
	$wgBabelUseUserLanguage = $wmgBabelUseUserLanguage;
}

if ( $wmgUseBounceHandler ) {
	require_once "$IP/extensions/BounceHandler/BounceHandler.php";
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
	require_once( "$IP/extensions/Translate/Translate.php" );

	$wgGroupPermissions['*']['translate'] = true;
	$wgGroupPermissions['translationadmin']['pagetranslation'] = true;
	$wgGroupPermissions['translationadmin']['translate-manage'] = true;
	$wgGroupPermissions['translationadmin']['translate-import'] = true; // T42341
	$wgGroupPermissions['user']['translate-messagereview'] = true;
	$wgGroupPermissions['user']['translate-groupreview'] = true;

	$wgTranslateDocumentationLanguageCode = 'qqq';
	$wgExtraLanguageNames['qqq'] = 'Message documentation'; # No linguistic content. Used for documenting messages

	// TODO: proper integration with new CirrusSearch config
	$wgTranslateExtensionDefaultCluster = 'eqiad';
	$wgTranslateTranslationServices = [];
	if ( $wmgUseTranslationMemory ) {
		$servers = array_map(
			function ( $v ) {
				if ( is_array( $v ) ) {
					return [ 'host' => $v['host'] ];
				} else {
					return [ 'host' => $v ];
				}
			},
			$wgCirrusSearchClusters[$wgTranslateExtensionDefaultCluster]
		);
		// Read only until renamed to 'TTMServer'
		$wgTranslateTranslationServices['TTMServer'] = [
			'type' => 'ttmserver',
			'class' => 'ElasticSearchTTMServer',
			'shards' => 1,
			'replicas' => 1,
			'index' => $wmgTranslateESIndex,
			'cutoff' => 0.65,
			'use_wikimedia_extra' => true,
			'config' => [
				'servers' => $servers,
			],
		];
	}

	$wgTranslateWorkflowStates = $wmgTranslateWorkflowStates;
	$wgTranslateRcFilterDefault = $wmgTranslateRcFilterDefault;

	unset( $wgTranslateTasks['export-as-file'] );
	unset( $wgTranslateTasks['optional'] );
	unset( $wgTranslateTasks['suggestions'] );

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

	// Can't translate to source language
	// TODO: figure out what to do once T37489 is fixed
	if ( $wgLanguageCode === 'en' ) {
		$wgTranslateBlacklist['*']['en'] = 'English is the source language.';
	}

	$wgTranslateEC = [];

	if ( $wgDBname === 'wikimania2013wiki' ) {
		$wgHooks['TranslatePostInitGroups'][] = function ( &$cc ) {
			$id = 'wiki-sidebar';
			$mg = new WikiMessageGroup( $id, 'sidebar-messages' );
			$mg->setLabel( 'Sidebar' );
			$mg->setDescription( 'Messages used in the sidebar of this wiki' );
			$cc[$id] = $mg;
			return true;
		};
	}

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
	require_once( "$IP/extensions/TranslationNotifications/TranslationNotifications.php" );
	$wgNotificationUsername = 'Translation Notification Bot@Translation_Notification_Bot';
	$wgNotificationUserPassword = $wmgTranslationNotificationUserPassword;

	$wgTranslationNotificationsContactMethods['talkpage-elsewhere'] = true;
}

if ( $wmgUseCleanChanges ) {
	$wgDefaultUserOptions['usenewrc'] = 1;
	require_once( "$IP/extensions/CleanChanges/CleanChanges.php" );
	$wgCCTrailerFilter = true;
}

if ( $wmgUseVips ) {
	include( "$IP/extensions/VipsScaler/VipsScaler.php" );
	include( "$IP/extensions/VipsScaler/VipsTest.php" );
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
	require_once( "$IP/extensions/ShortUrl/ShortUrl.php" );
	$wgShortUrlTemplate = "/s/$1";
}

if ( $wmgUseFeaturedFeeds ) {
	require_once( "$IP/extensions/FeaturedFeeds/FeaturedFeeds.php" );
	require_once( "$wmfConfigDir/FeaturedFeedsWMF.php" );
}

$wgDisplayFeedsInSidebar = $wmgDisplayFeedsInSidebar;

if ( $wmgEnablePageTriage ) {
	require_once( "$IP/extensions/PageTriage/PageTriage.php" );
	$wgPageTriageEnableCurationToolbar = $wmgPageTriageEnableCurationToolbar;
}

if ( $wmgEnableInterwiki ) {
	require_once( "$IP/extensions/Interwiki/Interwiki.php" );
	$wgInterwikiViewOnly = true;
}

# Avoid excessive CPU due to cache misses from rapid invalidations
$wgJobBackoffThrottling['htmlCacheUpdate'] = 20; // pages/sec per runner

# Job types to exclude from the default queue processing. Aka the very long
# one. That will exclude the types from any queries such as nextJobDB.php
# We have to set this for any project cause we usually run PHP script against
# the 'aawiki' database, but might as well run it against another name.

# Timed Media Handler:
$wgJobTypesExcludedFromDefaultQueue[] = 'webVideoTranscode';

# GWToolset
$wgJobTypesExcludedFromDefaultQueue[] = 'gwtoolsetUploadMetadataJob';
$wgJobTypesExcludedFromDefaultQueue[] = 'gwtoolsetUploadMediafileJob';
$wgJobTypesExcludedFromDefaultQueue[] = 'gwtoolsetGWTFileBackendCleanupJob';

if ( $wmgUseEducationProgram ) {
	require_once( "$IP/extensions/EducationProgram/EducationProgram.php" );
	$egEPSettings['dykCategory'] = $wmgEducationProgramDYKCat;
	$wgNamespaceProtection[EP_NS] = [ 'ep-course' ]; // T112806 (security)
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
	require_once( "$IP/extensions/GeoData/GeoData.php" );
	$wgGeoDataBackend = 'elastic';

	if ( !$wmgEnableGeoSearch ) {
		$wgAPIListModules['geosearch'] = 'ApiQueryDisabled';
	}

	$wgMaxCoordinatesPerPage = 2000;
	$wgMaxGeoSearchRadius = $wmgMaxGeoSearchRadius;
	$wgGeoDataDebug = $wmgGeoDataDebug;
}

if ( $wmgUseEcho ) {
	require_once( "$IP/extensions/Echo/Echo.php" );

	if ( $wmgUseClusterJobqueue ) {
		$wgJobTypeConf['MWEchoNotificationEmailBundleJob'] = [ 'checkDelay' => true ] + $wgJobTypeConf['default'];
	}

	// Eventlogging for Schema:EchoMail
	$wgEchoConfig['eventlogging']['EchoMail']['enabled'] = true;
	// Eventlogging for Schema:EchoInteraction
	$wgEchoConfig['eventlogging']['EchoInteraction']['enabled'] = true;

	$wgEchoEnableEmailBatch = $wmgEchoEnableEmailBatch;
	$wgEchoEmailFooterAddress = $wmgEchoEmailFooterAddress;
	if ( $wmgUseClusterJobqueue ) {
		$wgEchoBundleEmailInterval = $wmgEchoBundleEmailInterval;
	}
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

	// Enable tracking table only on SULed wikis
	if ( $wmgUseCentralAuth ) {
		$wgEchoSharedTrackingDB = 'wikishared';
		// Explicitly set this to 'extension1', because some wikis have $wgEchoCluster set to false
		$wgEchoSharedTrackingCluster = 'extension1';
	}

	// Temporarily disable thank-you-edit notifications (T128249)
	if ( isset( $wgDefaultNotifyTypeAvailability ) ) {
		$wgEchoNotifications['thank-you-edit']['notify-type-availability']['web'] = false;
	} else {
		// Backwards compatibility, remove after 1.27.0-wmf.22 is deployed everywhere
		$wgEchoDefaultNotificationTypes['thank-you-edit']['web'] = false;
	}

	// Default user options: subscriptions
	foreach ( $wmgEchoDefaultUserSubscriptions as $where => $notifications ) {
		foreach ( $notifications as $notification => $value ) {
			$option = 'echo-subscriptions-' . $where . '-' . $notification;
			$wgDefaultUserOptions[$option] = $value;
		}
	}

	$wgEchoBundleTransition = $wmgEchoTransition;
	$wgEchoSectionTransition = $wmgEchoTransition;
}

if ( $wmgUseThanks ) {
	require_once( "$IP/extensions/Thanks/Thanks.php" );
}

if ( $wmgUseFlow && $wmgUseParsoid ) {
	require_once( "$IP/extensions/Flow/Flow.php" );

	// Flow Parsoid - These are now specified directly as Flow-specific
	// configuration variables, though it currently uses the same Parsoid URL
	// as VisualEditor does.
	$wgFlowParsoidURL = $wmgParsoidURL;
	$wgFlowParsoidPrefix = $wgDBname;
	$wgFlowParsoidTimeout = 100;
	if ( $wmgParsoidForwardCookies ) {
		$wgFlowParsoidForwardCookies = true;
	}

	if ( $wmgUseVisualEditor ) {
		$wgFlowEditorList = [ 'visualeditor', 'none' ];
		$wgDefaultUserOptions['flow-editor'] = 'visualeditor';
	}

	foreach ( $wmgFlowNamespaces as $namespace ) {
		$wgNamespaceContentModels[$namespace] = CONTENT_MODEL_FLOW_BOARD;
	}
	// Requires that Parsoid is available for all wikis using Flow.
	$wgFlowContentFormat = 'html';

	if ( $wmgFlowEnglishNamespaceOnly ) {
		// HACK: Only use English namespace names, override the localized namespace names
		// This is needed for languages where the translation for Thread: (from LQT)
		// and Topic: (from Flow) are the same, like in Portuguese. In those cases we
		// make the Flow Topic: namespace only use the English name, so the translated name
		// will still point to the LQT namespace.
		$wgExtraNamespaces[NS_TOPIC] = 'Topic';
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
}

if ( $wmgUseDisambiguator ) {
	require_once( "$IP/extensions/Disambiguator/Disambiguator.php" );
}

if ( $wmgUseCodeEditorForCore || $wmgUseScribunto || $wmgZeroPortal ) {
	include_once( "$IP/extensions/CodeEditor/CodeEditor.php" );
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
	include( "$IP/extensions/Scribunto/Scribunto.php" );
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
	include( "$IP/extensions/SubpageSortkey/SubpageSortkey.php" );
	$wgSubpageSortkeyByNamespace = $wmgSubpageSortkeyByNamespace;
}

if ( $wmgUseGettingStarted ) {
	require_once( "$IP/extensions/GettingStarted/GettingStarted.php" );
	$wgGettingStartedRedis = $wgObjectCaches['redis_master']['servers'][0];
	$wgGettingStartedRedisOptions['password'] = $wmgRedisPassword;
	$wgGettingStartedCategoriesForTaskTypes = $wmgGettingStartedCategoriesForTaskTypes;
	$wgGettingStartedExcludedCategories = $wmgGettingStartedExcludedCategories;

	$wgGettingStartedRunTest = $wmgGettingStartedRunTest;
}

if ( $wmgUseGeoCrumbs ) {
	require_once( "$IP/extensions/GeoCrumbs/GeoCrumbs.php" );
}

if ( $wmgUseGeoCrumbs || $wmgUseInsider || $wmgUseRelatedSites ) {
	require_once( "$IP/extensions/CustomData/CustomData.php" );
}

if ( $wmgUseCalendar ) {
	wfLoadExtension( 'Calendar' );
}

if ( $wmgUseMapSources ) {
	require_once( "$IP/extensions/MapSources/MapSources.php" );
}

if ( $wmgUseSlippyMap ) {
	require_once( "$IP/extensions/OpenStreetMapSlippyMap/SlippyMap.php" );
}

if ( $wmgUseCreditsSource ) {
	require_once( "$IP/extensions/CreditsSource/CreditsSource.php" );
}

if ( $wmgUseListings ) {
	require_once( "$IP/extensions/Listings/Listings.php" );
}

if ( $wmgUseTocTree ) {
	require_once( "$IP/extensions/TocTree/TocTree.php" );
	$wgDefaultUserOptions['toc-floated'] = $wmgUseFloatedToc;
}

if ( $wmgUseInsider ) {
	require_once( "$IP/extensions/Insider/Insider.php" );
}

if ( $wmgUseRelatedArticles ) {
	wfLoadExtension( 'RelatedArticles' );
	$wgRelatedArticlesShowInSidebar = $wmgRelatedArticlesShowInSidebar;

	if ( $wmgRelatedArticlesShowInFooter ) {
		wfLoadExtension( 'Cards' );
		$wgRelatedArticlesShowInSidebar = false;
		$wgRelatedArticlesShowInFooter = true;
		$wgRelatedArticlesLoggingSamplingRate = 0.01;
		$wgRelatedArticlesUseCirrusSearch = true;
		$wgRelatedArticlesOnlyUseCirrusSearch = false;
	}
}

if ( $wmgUseRelatedSites ) {
	require_once( "$IP/extensions/RelatedSites/RelatedSites.php" );
	$wgRelatedSitesPrefixes = $wmgRelatedSitesPrefixes;
}

if ( $wmgUseRevisionSlider ) {
	wfLoadExtension( 'RevisionSlider' );
}

if ( $wmgUseUserMerge ) {
	require_once( "$IP/extensions/UserMerge/UserMerge.php" );
	// Don't let users get deleted outright (T69789)
	$wgUserMergeEnableDelete = false;
}

if ( $wmgUseEventLogging ) {
	require_once( "$IP/extensions/EventLogging/EventLogging.php" );
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

		include_once( "$IP/extensions/CodeEditor/CodeEditor.php" );
		$wgCodeEditorEnableCore = $wmgUseCodeEditorForCore; // For safety's sake
	}

	// Temporary hack for 'jsonschema' API module migration
	$wgEventLoggingSchemaIndexUri = $wgEventLoggingSchemaApiUri;

	// Extensions dependent on EventLogging
	if ( $wmgUseCampaigns ) {
		include_once( "$IP/extensions/Campaigns/Campaigns.php" );
	}

	include_once( "$IP/extensions/WikimediaEvents/WikimediaEvents.php" );
	$wgWMEStatsdBaseUri = '/beacon/statsv';
	$wgWMETrackGeoFeatures = $wmgWMETrackGeoFeatures;
}

if ( $wmgUseEventLogging && $wmgUseNavigationTiming ) {
	include_once( "$IP/extensions/NavigationTiming/NavigationTiming.php" );
	// Careful! The LOWER the value, the MORE requests will be logged. A
	// sampling factor of 1 means log every request. This should not be
	// lowered without careful coordination with ops.
	$wgNavigationTimingSamplingFactor = 1000;

	$wgPercentHHVM = 0;
}

include_once( "$IP/extensions/XAnalytics/XAnalytics.php" );

if ( $wmgUseUniversalLanguageSelector ) {
	require_once( "$IP/extensions/UniversalLanguageSelector/UniversalLanguageSelector.php" );
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

if ( $wmgUseContentTranslation ) {
	wfLoadExtension( 'ContentTranslation' );

	//T76200: Public URL for cxserver instance
	$wgContentTranslationSiteTemplates['cx'] = '//cxserver.wikimedia.org/v1';

	$wgContentTranslationRESTBase = [
		'url' => $wmgRestbaseServer,
		'domain' => $wgCanonicalServer,
		'forwardCookies' => false,
		'timeout' => 10000,
		'HTTPProxy' => false,
	];

	$wgContentTranslationTranslateInTarget = true;

	$wgContentTranslationTargetNamespace = $wmgContentTranslationTargetNamespace;

	$wgContentTranslationEventLogging = $wmgContentTranslationEventLogging;

	if ( $wmgContentTranslationCluster ) {
		$wgContentTranslationCluster = $wmgContentTranslationCluster;
	}

	$wgContentTranslationDatabase = 'wikishared';

	$wgContentTranslationCampaigns = $wmgContentTranslationCampaigns;

	$wgContentTranslationDefaultSourceLanguage = $wmgContentTranslationDefaultSourceLanguage;

	$wgContentTranslationEnableSuggestions = $wmgContentTranslationEnableSuggestions;

	$wgContentTranslationCXServerAuth = [
		'algorithm' => 'HS256',
		//This is set in PrivateSettings.php
		'key' => $wmgContentTranslationCXServerAuthKey,
		'age' => '3600',
	];
}

$wgExtensionEntryPointListFiles[] = "$IP/extensions/Wikidata/extension-list-wikidata";

if ( $wmgUseWikibaseRepo || $wmgUseWikibaseClient ) {
	if ( $wmgUseWikibaseRepo && $wmfRealm === 'labs' ) {
		// enable on beta only
		$wmgUseWikibasePropertySuggester = true;
	}

	include( "$wmfConfigDir/Wikibase.php" );
}

// Do not attempt to load SMW for l10n in beta.
if ( $wmfRealm != 'labs' ) {
	// Tell localization cache builder about extensions used in wikitech
	$wgExtensionEntryPointListFiles[] = "$wmfConfigDir/extension-list-wikitech";
}

if ( $wgDBname == 'labswiki' || $wgDBname === 'labtestwiki' ) {
	$wgEmailConfirmToEdit = true;
	$wgEnableCreativeCommonsRdf = true;

	// Don't depend on other DB servers
	$wgDefaultExternalStore = false;

	$wgGroupPermissions['contentadmin'] = $wgGroupPermissions['sysop'];
	$wgGroupPermissions['contentadmin']['editusercss'] = false;
	$wgGroupPermissions['contentadmin']['edituserjs'] = false;
	$wgGroupPermissions['contentadmin']['editrestrictedfield'] = false;
	$wgGroupPermissions['contentadmin']['editinterface'] = false;
	$wgGroupPermissions['contentadmin']['tboverride'] = false;
	$wgGroupPermissions['contentadmin']['titleblacklistlog'] = false;
	$wgGroupPermissions['contentadmin']['override-antispoof'] = false;
	$wgGroupPermissions['contentadmin']['createaccount'] = false;

	// Some settings specific to wikitech's extensions
	include( "$wmfConfigDir/wikitech.php" );
}

// put this here to ensure it is available for localisation cache rebuild
$wgWBClientSettings['repoSiteName'] = 'wikibase-repo-name';

if ( $wmgUseTemplateSandbox ) {
	require_once( "$IP/extensions/TemplateSandbox/TemplateSandbox.php" );
	if( $wmgUseScribunto ) {
		$wgTemplateSandboxEditNamespaces[] = NS_MODULE;
	}
}

if ( $wmgUsePageAssessments ) {
	wfLoadExtension( 'PageAssessments' );
}

if ( $wmgUsePageImages ) {
	require_once( "$IP/extensions/PageImages/PageImages.php" );
	$wgPageImagesExpandOpenSearchXml = $wmgPageImagesExpandOpenSearchXml;
	$wgPageImagesUseGalleries = $wmgPageImagesUseGalleries;
}

if ( $wmgUseSearchExtraNS ) {
	require_once( "$IP/extensions/SearchExtraNS/SearchExtraNS.php" );
	$wgSearchExtraNamespaces = $wmgSearchExtraNamespaces;
}

if ( $wmgUseGlobalAbuseFilters ) {
	$wgAbuseFilterCentralDB = $wmgAbuseFilterCentralDB;
	$wgAbuseFilterIsCentral = ( $wgDBname == $wgAbuseFilterCentralDB );
}

if ( $wmgZeroPortal ) {
	require_once( "$IP/extensions/JsonConfig/JsonConfig.php" );
	require_once( "$IP/extensions/ZeroBanner/ZeroBanner.php" );
	require_once( "$IP/extensions/ZeroPortal/ZeroPortal.php" );

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

if ( $wmgUseGraph ) {
	require_once( "$IP/extensions/JsonConfig/JsonConfig.php" );
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

	if ( $wmgUseGraphWithNamespace ) {
		$wgJsonConfigModels['Graph.JsonConfig'] = 'Graph\Content';
		$wgJsonConfigs['Graph.JsonConfig'] = [
			'namespace' => 484,
			'nsName' => 'Graph',
			'isLocal' => true,
		];
	}

	if ( $wmgUseGraphWithJsonNamespace ) {
		$wgJsonConfigModels['Json.JsonConfig'] = null;
		$wgJsonConfigs['Json.JsonConfig'] = [
			'namespace' => 486,
			'nsName' => 'Data',
			'isLocal' => true,
			'pattern' => '/^Json:./',
			// these two settings should be removed after deploying
			// https://gerrit.wikimedia.org/r/#/c/281327/
			'name' => 'Json',
			'isSubspace' => true,
		];
	}
}

if ( $wmgUseOAuth ) {
	require_once( "$IP/extensions/OAuth/OAuth.php" );
	if ( $wgDBname !== "labswiki" && $wgDBname !== 'labtestwiki' ) {
		$wgMWOAuthCentralWiki = 'metawiki';
		$wgMWOAuthSharedUserSource = 'CentralAuth';
	}
	$wgMWOAuthSecureTokenTransfer = true;

	$wgGroupPermissions['autoconfirmed']['mwoauthproposeconsumer'] = true;
	$wgGroupPermissions['autoconfirmed']['mwoauthupdateownconsumer'] = true;

	$wgHooks['OAuthReplaceMessage'][] = function( &$msgKey ) {
		if ( $msgKey === 'mwoauth-form-privacypolicy-link' ) {
			$msgKey = 'wikimedia-oauth-privacy-link';
		}
		return true;
	};

	$wgExtensionFunctions[] = function() {
		global $wgDBname, $wgMWOAuthCentralWiki, $wgGroupPermissions;
		if ( $wgDBname === $wgMWOAuthCentralWiki ) {
			// Only needed on the central wiki.
			$wgGroupPermissions['oauthadmin']['mwoauthmanageconsumer'] = true;
		}
	};
}

if ( $wmgUsePetition ) {
	require_once( "$IP/extensions/Petition/Petition.php" );
}

// T15712
if ( $wmgUseJosa ) {
	require_once( "$IP/extensions/Josa/Josa.php" );
}

if ( $wmgUseParsoidBatchAPI ) {
	wfLoadExtension( 'ParsoidBatchAPI' );
}

if ( $wmgUseOATHAuth ) {
	wfLoadExtension( 'OATHAuth' );
	// Roll this feature out to specific groups initially
	$wgGroupPermissions['*']['oathauth-enable'] = false;
	if ( $wmgUseCentralAuth ) {
		$wgOATHAuthDatabase = 'centralauth';
	}
}

if ( $wmgUseORES ) {
	wfLoadExtension( 'ORES' );
	$wgOresBaseUrl = 'https://ores.wikimedia.org/';
}

### End (roughly) of general extensions ########################

$wgApplyIpBlocksToXff = true;

// On Special:Version, link to useful release notes
$wgHooks['SpecialVersionVersionUrl'][] = function( $wgVersion, &$versionUrl ) {
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

$wgExemptFromUserRobotsControl = array_merge( $wgContentNamespaces, $wmgExemptFromUserRobotsControlExtra );

// additional "language names", adding to Names.php data
$wgExtraLanguageNames = $wmgExtraLanguageNames;


if ( $wmfRealm === 'labs' ) {
	require( "$wmfConfigDir/CommonSettings-labs.php" );
}

if ( $wmgUseCheckUser ) {
	include( $IP . '/extensions/CheckUser/CheckUser.php' );
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

	// RCStream / stream.wikimedia.org
	if ( $wmfRealm === 'production' ) {
		$wgRCFeeds['rcs1001'] = [
			'uri'       => "redis://rcs1001.eqiad.wmnet:6379/rc.$wgDBname",
			'formatter' => 'JSONRCFeedFormatter',
		];

		$wgRCFeeds['rcs1002'] = [
			'uri'       => "redis://rcs1002.eqiad.wmnet:6379/rc.$wgDBname",
			'formatter' => 'JSONRCFeedFormatter',
		];
	}
}

// Confirmed can do anything autoconfirmed can.
$wgGroupPermissions['confirmed'] = $wgGroupPermissions['autoconfirmed'];
$wgGroupPermissions['confirmed']['skipcaptcha'] = true;

$wgImgAuthDetails = true;

if ( $wmgUseWPB ) {
	wfLoadExtension( 'WikidataPageBanner' );
}

if ( $wmgUseQuickSurveys ) {
	wfLoadExtension( 'QuickSurveys' );
}

if ( $wmgUseEventBus ) {
	wfLoadExtension( 'EventBus' );
	$wgEventServiceUrl = "{$wmfMasterServices['eventbus']}/v1/events";
}

if ( $wmgUseCapiunto ) {
	require_once "$IP/extensions/Capiunto/Capiunto.php";
}

if ( $wmgUseKartographer ) {
	wfLoadExtension( 'Kartographer' );
}

# THIS MUST BE AFTER ALL EXTENSIONS ARE INCLUDED
#
# REALLY ... we're not kidding here ... NO EXTENSIONS AFTER

require( "$wmfConfigDir/ExtensionMessages-$wmgVersionNumber.php" );
