<?php

# WARNING: This file is publically viewable on the web. Do not put private data here.

#######################################################################
# This file is the main and first configuration file of the WMF cluster.
# It is included by LocalSettings.php
#
#######################################################################


# Godforsaken hack to work around problems with the Squid caching changes...
#
# To minimize damage on fatal PHP errors, output a default no-cache header
# It will be overridden in cases where we actually specify caching behavior.
#
# More modern PHP versions will send a 500 result code on fatal erorr,
# at least sometimes, but what we're running will send a 200.
if( PHP_SAPI != 'cli' ) {
	header( "Cache-control: no-cache" );
}

# Try to control stuff:
# define( 'DEBUG_LOG', true );

# useful tokens to search for:

# :SEARCH: - search settings

# -----------------

if ( PHP_SAPI == 'cli' ) {
	# Override for sanity's sake.
	ini_set( 'display_errors', 1 );
	# error_reporting(E_ALL);
}
if ( isset( $_SERVER['SERVER_ADDR'] ) ) {
	ini_set( 'error_append_string', ' (' . $_SERVER['SERVER_ADDR'] . ')' );
}

# Protection for unusual entry points
if ( !function_exists( 'wfProfileIn' ) ) {
	require( './includes/ProfilerStub.php' );
}

$fname = 'CommonSettings.php';
wfProfileIn( $fname );
wfProfileIn( "$fname-init" );

# ----------------------------------------------------------------------
# Initialisation

# Get the version object for this Wiki (must be set by now, along with $IP)
if ( !class_exists( 'MWMultiVersion' ) ) {
	die( "No MWMultiVersion instance initialized! MWScript.php wrapper not used?\n" );
}
$multiVersion = MWMultiVersion::getInstance();

set_include_path( "$IP:/usr/local/lib/php:/usr/share/php" );

if ( getenv( 'WIKIBACKUP' ) ) {
	// hack while normal ext is not enabled sitewide
	if ( !function_exists( 'utf8_normalize' ) ) {
		dl( 'php_utfnormal.so' );
	}
}

### Determine realm and cluster we are on #############################
# $cluster is an historical variable used for the WMF MW conf
$cluster = 'pmtpa';

# $wmfRealm should be the realm as puppet understand it.
# The possible values as of June 2012 are:
#  - labs
#  - production
$wmfRealm   = 'production';

# Puppet provision the realm in /etc/wikimedia-realm
if( file_exists( '/etc/wikimedia-realm' ) ) {
	$wmfRealm = trim( file_get_contents( '/etc/wikimedia-realm' ) );
}

# Set cluster based on realm
switch( $wmfRealm ) {
	case 'labs':
		$cluster = 'wmflabs';
		break;
	case 'production':
	default:
		$wmfRealm = 'production';
		$cluster = 'pmtpa';
		break;
}
### End /Determine realm and cluster we are on/ ########################

### List of some service hostnames
# 'meta'   : meta wiki for user editable content
# 'upload' : hostname where files are hosted
# TODO: 'bits'
# Whenever all realms/datacenters should use the same host, do not use
# $wmfHostnames but use the hardcoded hostname instead. A good example are the
# spam blacklists hosted on meta.wikimedia.org which you will surely want to
# reuse.
$wmfHostnames = array();
switch( $wmfRealm ) {
case 'labs':
	$wmfHostnames['meta']   = 'meta.wikimedia.beta.wmflabs.org';
	$wmfHostnames['upload'] = 'upload.beta.wmflabs.org';
	break;
case 'production':
default:
	$wmfHostnames['meta']   = 'meta.wikimedia.org';
	$wmfHostnames['upload'] = 'upload.wikimedia.org';
	break;
}

# Load site configuration
include( "$IP/includes/DefaultSettings.php" );

$DP = $IP;

wfProfileOut( "$fname-init" );
wfProfileIn( "$fname-host" );

# This must be set *after* the DefaultSettings.php inclusion
$wgDBname = $multiVersion->getDatabase();

# Better have the proper username (bug 44251)
$wgDBuser = 'wikiuser';

# wmf-config directory (in common/)
$wmfConfigDir = "$IP/../wmf-config";

wfProfileOut( "$fname-host" );

# Must be set before InitialiseSettings.php:
switch( $wmfRealm ) {
case 'production':
	# fluorine (nfs1 while fluorine is down)
	$wmfUdp2logDest = '10.64.0.21:8420';
	break;
case 'labs':
	# deployment-bastion hosts the udp2log daemon
	$wmfUdp2logDest = '10.4.0.58:8420';
	break;
default:
	$wmfUdp2logDest = '127.0.0.1:8420';
}

# Initialise wgConf
wfProfileIn( "$fname-wgConf" );
require( "$wmfConfigDir/wgConf.php" );
function wmfLoadInitialiseSettings( $conf ) {
	global $wmfConfigDir;
	require( "$wmfConfigDir/InitialiseSettings.php" );
}
wfProfileOut( "$fname-wgConf" );

wfProfileIn( "$fname-confcache" );

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
$wmfVersionNumber = $multiVersion->getVersionNumber();
$wmfExtendedVersionNumber = $multiVersion->getExtendedVersionNumber();

# Try configuration cache

$filename = "/tmp/mw-cache-$wmfVersionNumber/conf-$wgDBname";
$globals = false;
if ( @filemtime( $filename ) >= filemtime( "$wmfConfigDir/InitialiseSettings.php" ) ) {
	$cacheRecord = @file_get_contents( $filename );
	if ( $cacheRecord !== false ) {
		$globals = unserialize( $cacheRecord );
	}
}
wfProfileOut( "$fname-confcache" );
if ( !$globals ) {
	wfProfileIn( "$fname-recache-settings" );
	# Get configuration from SiteConfiguration object
	require( "$wmfConfigDir/InitialiseSettings.php" );

	$wikiTags = array();
	foreach ( array( 'private', 'fishbowl', 'special', 'closed', 'flaggedrevs', 'small', 'medium', 'large', 'wikimania', 'wikidataclient' ) as $tag ) {
		$dblist = array_map( 'trim', file( getRealmSpecificFilename( "$IP/../$tag.dblist" ) ) );
		if ( in_array( $wgDBname, $dblist ) ) {
			$wikiTags[] = $tag;
		}
	}

	$dbSuffix = ( $site === 'wikipedia' ) ? 'wiki' : $site;
	$globals = $wgConf->getAll( $wgDBname, $dbSuffix,
		array(
			'lang'    => $lang,
			'docRoot' => $_SERVER['DOCUMENT_ROOT'],
			'site'    => $site,
			'stdlogo' => "//{$wmfHostnames['upload']}/$site/$lang/b/bc/Wiki.png" ,
		), $wikiTags );

	# Save cache
	$oldUmask = umask( 0 );
	@mkdir( '/tmp/mw-cache-' . $wmfVersionNumber, 0777 );
	$file = fopen( $filename, 'w' );
	if ( $file ) {
		fwrite( $file, serialize( $globals ) );
		fclose( $file );
		@chmod( $file, 0666 );
	}
	umask( $oldUmask );
	wfProfileOut( "$fname-recache-settings" );
}
wfProfileIn( "$fname-misc1" );

extract( $globals );

# -------------------------------------------------------------------------
# Settings common to all wikis

# Private settings such as passwords, that shouldn't be published
# Needs to be before db.php
require( "$wmfConfigDir/PrivateSettings.php" );

# Cluster-dependent files for database and memcached
require( getRealmSpecificFilename( "$wmfConfigDir/db.php" ) );
require( getRealmSpecificFilename( "$wmfConfigDir/mc.php" ) );

ini_set( 'memory_limit', $wmgMemoryLimit );

# Rewrite commands given to wfShellWikiCmd() to use Het-Deploy
$wgHooks['wfShellWikiCmd'][] = 'MWMultiVersion::onWfShellMaintenanceCmd';

setlocale( LC_ALL, 'en_US.UTF-8' );

unset( $wgStylePath );
unset( $wgStyleSheetPath );

// New URL scheme
if ( $wgDBname == 'testwiki' ) {
	// Make testing skin/JS changes easier
	$wgExtensionAssetsPath = "//test.wikipedia.org/w/static-$wmfVersionNumber/extensions";
	$wgStyleSheetPath = "//test.wikipedia.org/w/static-$wmfVersionNumber/skins";
	$wgResourceBasePath = "//test.wikipedia.org/w/static-$wmfVersionNumber"; // This means resources will be requested from /w/static-VERSION/resources
} else {
	$wgExtensionAssetsPath = "//bits.wikimedia.org/static-$wmfVersionNumber/extensions";
	$wgStyleSheetPath = "//bits.wikimedia.org/static-$wmfVersionNumber/skins";
	$wgResourceBasePath = "//bits.wikimedia.org/static-$wmfVersionNumber"; // This means resources will be requested from /static-VERSION/resources
}

# For labs, override settings just above. This need to be done before
# extensions so we can not use CommonSettings-labs.php
if( $wmfRealm == 'labs' ) {
	# Base path:
	$wgResourceBasePath    = "//bits.beta.wmflabs.org/static-master";

	# Assets:
	$wgExtensionAssetsPath = $wgResourceBasePath . "/extensions";
	$wgStyleSheetPath      = $wgResourceBasePath . "/skins";

}

$wgStylePath = $wgStyleSheetPath;
$wgArticlePath = "/wiki/$1";

$wgScriptPath  = '/w';
$wgLocalStylePath = "$wgScriptPath/static-$wmfVersionNumber/skins";
$wgStockPath = '/images';
$wgScript           = $wgScriptPath . '/index.php';
$wgRedirectScript	= $wgScriptPath . '/redirect.php';
$wgInternalServer = $wgCanonicalServer;

if ( $wmfRealm == 'production' && $wgDBname != 'testwiki' && isset( $_SERVER['SERVER_NAME'] ) ) {
	// Make testing JS/skin changes easy by not running load.php through bits for testwiki
	$wgLoadScript = "//bits.wikimedia.org/{$_SERVER['SERVER_NAME']}/load.php";
}

$wgCacheDirectory = '/tmp/mw-cache-' . $wmfVersionNumber;

// Whether addWiki.php should send email
$wmgAddWikiNotify = true;

// Comment out the following lines to get the old-style l10n caching -- TS 2011-02-22
$wgLocalisationCacheConf['storeDirectory'] = "$IP/cache/l10n";
$wgLocalisationCacheConf['manualRecache'] = true;

$wgFileStore['deleted']['directory'] = "/mnt/upload7/private/archive/$site/$lang";

# used for mysql/search settings
$tmarray = getdate( time() );
$hour = $tmarray['hours'];
$day = $tmarray['wday'];

$wgEmergencyContact = 'noc@wikipedia.org';

if ( defined( 'DEBUG_LOG' ) && $wgDBname == 'aawiki' ) {
	$wgMemCachedDebug = true;
	$wgDebugLogFile = "udp://$wmfUdp2logDest/debug15";
	$wgDebugDumpSql = true;
}

$wgDBerrorLog = "udp://$wmfUdp2logDest/dberror";
$wgDBerrorLogTZ = 'UTC';

if ( !isset( $wgLocaltimezone ) ) {
	$wgLocaltimezone = 'UTC';
}
# Ugly hack warning! This needs smoothing out.
if ( $wgLocaltimezone ) {
	$oldtz = getenv( 'TZ' );
	putenv( "TZ=$wgLocaltimezone" );
	$wgLocalTZoffset = date( 'Z' ) / 60;
	putenv( "TZ=$oldtz" );
}

$wgShowIPinHeader = false;
$wgUseGzip = true;
$wgRCMaxAge = 30 * 86400;

$wgUseTeX = true;
$wgTmpDirectory     = '/tmp';

$wgSQLMode = null;

# Object cache and session settings

$pcTemplate = array( 'type' => 'mysql',
	'dbname' => 'parsercache',
	'user' => $wgDBuser,
	'password' => $wgDBpassword,
	'flags' => 0,
);

if ($wmfDatacenter == 'eqiad') {
	# pc1001, pc1002, pc1003
	foreach ( array( '10.64.16.156', '10.64.16.157', '10.64.16.158' ) as $host ) {
		$pcServers[] = array( 'host' => $host ) + $pcTemplate;
	}
} else {
	# pc1, pc2, pc3
	foreach ( array( '10.0.0.221', '10.0.0.222', '10.0.0.223' ) as $host ) {
		$pcServers[] = array( 'host' => $host ) + $pcTemplate;
	}
}

$wgObjectCaches['mysql-multiwrite'] = array(
	'class' => 'MultiWriteBagOStuff',
	'caches' => array(
		0 => $wgObjectCaches['memcached-pecl'],
		1 => array(
			'class' => 'SqlBagOStuff',
			'servers' => $pcServers,
			'purgePeriod' => 0,
			'tableName' => 'pc',
			'shards' => 256,
		),
	)
);

$sessionRedis = array(
	'pmtpa' => array(
		'10.0.12.1', # mc1
		'10.0.12.2', # mc2
		'10.0.12.3', # mc3
		'10.0.12.4', # mc4
		'10.0.12.5', # mc5
		'10.0.12.6', # mc6
		'10.0.12.7', # mc7
		'10.0.12.8', # mc8
		'10.0.12.9', # mc9
		'10.0.12.10', # mc10
		'10.0.12.11', # mc11
		'10.0.12.12', # mc12
		'10.0.12.13', # mc13
		'10.0.12.14', # mc14
		'10.0.12.15', # mc15
		'10.0.12.16', # mc16
	),
	'eqiad' => array(
		'10.64.0.180', # mc1001
		'10.64.0.181', # mc1002
		'10.64.0.182', # mc1003
		'10.64.0.183', # mc1004
		'10.64.0.184', # mc1005
		'10.64.0.185', # mc1006
		'10.64.0.186', # mc1007
		'10.64.0.187', # mc1008
		'10.64.0.188', # mc1009
		'10.64.0.189', # mc1010
		'10.64.0.190', # mc1011
		'10.64.0.191', # mc1012
		'10.64.0.192', # mc1013
		'10.64.0.193', # mc1014
		'10.64.0.194', # mc1015
		'10.64.0.195', # mc1016
	),
);

// Cache to hold user sessions in production:
$wgObjectCaches['sessions'] = array(
	'class'   => 'RedisBagOStuff',
	'servers' => $sessionRedis[$wmfDatacenter],
);

// Override for beta:
if( $wmfRealm == 'labs' ) {
	$wgObjectCaches['sessions'] = $wgObjectCaches['memcached-pecl'];
}

// Use the cache setup above and configure sessions caching
$wgSessionCacheType = 'sessions';
$wgSessionsInObjectCache = true;
session_name( $lang . 'wikiSession' );

# Not CLI, see http://bugs.php.net/bug.php?id=47540
if ( PHP_SAPI != 'cli' ) {
	ignore_user_abort( true );
} else {
	$wgShowExceptionDetails = true;
}

$wgUseImageResize               = true;
$wgUseImageMagick               = true;
$wgImageMagickConvertCommand    = '/usr/bin/convert';
$wgSharpenParameter = '0x0.8'; # for IM>6.5, bug 24857

$wgFileBlacklist[] = 'txt';
$wgFileBlacklist[] = 'mht';
$wgFileExtensions[] = 'xcf';
$wgFileExtensions[] = 'pdf';
$wgFileExtensions[] = 'mid';
$wgFileExtensions[] = 'ogg'; # Ogg audio & video
$wgFileExtensions[] = 'ogv'; # Ogg audio & video
$wgFileExtensions[] = 'svg';
$wgFileExtensions[] = 'djvu'; # DjVu images/documents

include( $IP . '/extensions/PagedTiffHandler/PagedTiffHandler.php' );
$wgTiffUseTiffinfo = true;
$wgTiffMaxMetaSize = 1048576;

$wgMaxImageArea = 5e7; // 50MP
$wgMaxAnimatedGifArea = 5e7; // 50MP

if ( $wgDBname == 'foundationwiki' ) { # per cary on 2010-05-11
	$wgFileExtensions[] = 'otf';
	$wgFileExtensions[] = 'ai';
} elseif ( $wgDBname == 'outreachwiki' ) { # Per Frank, bug 25106
	$wgFileExtensions[] = 'sla';
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
	$wgFileExtensions[] = 'mp3'; // for Jay for fundraising bits
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
		array( 'application/zip' ) );
}

# Hack for rsvg broken by security patch
$wgSVGConverters['rsvg-broken'] = '$path/rsvg-convert -w $width -h $height -o $output < $input';
# This converter will only work when rsvg has a suitable security patch
$wgSVGConverters['rsvg-secure'] = '$path/rsvg-convert --no-external-files -w $width -h $height -o $output $input';

$wgAllowUserJs = true;
$wgAllowUserCss = true;
$wgSecureLogin = false;

#######################################################################
# Squid Configuration
#######################################################################

$wgUseSquid = true;
$wgUseESI   = false;

switch ( $wmfRealm ) {
case 'production':
	# HTCP multicast squid purging
	$wgHTCPMulticastAddress = '239.128.0.112';
	$wgHTCPMulticastTTL = 8;

	# As of 2005-04-08, this is empty
	# Squids are purged by HTCP multicast, currently relayed to paris via udpmcast on larousse
	$wgSquidServers = array();

	# Accept XFF from these proxies
	$wgSquidServersNoPurge = array(
		'208.80.152.162',	# singer (secure)

		# Text
		# pmtpa
		'208.80.152.43',	# sq33, API
		'208.80.152.44',	# sq34, API
		'208.80.152.46',	# sq36, API
		'208.80.152.47',	# sq37
		'208.80.152.69',	# sq59
		'208.80.152.70',	# sq60
		'208.80.152.71',	# sq61
		'208.80.152.72',	# sq62
		'208.80.152.73',	# sq63
		'208.80.152.74',	# sq64
		'208.80.152.75',	# sq65
		'208.80.152.76',	# sq66
		'208.80.152.81',	# sq71
		'208.80.152.82',	# sq72
		'208.80.152.83',	# sq73
		'208.80.152.84',	# sq74
		'208.80.152.85',	# sq75
		'208.80.152.86',	# sq76
		'208.80.152.87',	# sq77
		'208.80.152.88',	# sq78

		# eqiad
		'10.64.0.123',	# cp1001, API
		'10.64.0.124',	# cp1002, API
		'10.64.0.125',	# cp1003, API
		'10.64.0.126',	# cp1004, API
		'10.64.0.127',	# cp1005, API
		'10.64.0.128',	# cp1006
		'10.64.0.129',	# cp1007
		'10.64.0.130',	# cp1008
		'10.64.0.131',	# cp1009
		'10.64.0.132',	# cp1010
		'10.64.0.133',	# cp1011
		'10.64.0.134',	# cp1012
		'10.64.0.135',	# cp1013
		'10.64.0.136',	# cp1014
		'10.64.0.137',	# cp1015
		'10.64.0.138',	# cp1016
		'10.64.0.139',	# cp1017
		'10.64.0.140',	# cp1018
		'10.64.0.141',	# cp1019
		'10.64.0.142',	# cp1020

		# esams
		'91.198.174.33',	# knsq23
		'91.198.174.34',	# knsq24
		'91.198.174.35',	# knsq25
		'91.198.174.36',	# knsq26
		'91.198.174.37',	# knsq27
		'91.198.174.38',	# knsq28
		'91.198.174.39',	# knsq29
		'91.198.174.41',	# amssq31
		'91.198.174.42',	# amssq32
		'91.198.174.43',	# amssq33
		'91.198.174.44',	# amssq34
		'91.198.174.45',	# amssq35
		'91.198.174.46',	# amssq36
		'91.198.174.47',	# amssq37
		'91.198.174.48',	# amssq38
		'91.198.174.49',	# amssq39
		'91.198.174.50',	# amssq40
		'91.198.174.51',	# amssq41
		'91.198.174.52',	# amssq42
		'91.198.174.53',	# amssq43
		'91.198.174.54',	# amssq44
		'91.198.174.55',	# amssq45
		'91.198.174.56',	# amssq46

		# SSL
		'208.80.152.16',	# ssl1
		'208.80.152.17',	# ssl2
		'208.80.152.18',	# ssl3
		'208.80.152.19',	# ssl4

		'208.80.154.133',	# ssl1001
		'208.80.154.134',	# ssl1002
		'208.80.154.9',		# ssl1003
		'208.80.154.8',		# ssl1004

		'91.198.174.102',	# ssl3001
		'91.198.174.103',	# ssl3002
		'91.198.174.104', 	# ssl3003
		'91.198.174.105',	# ssl3004
		'91.198.174.106',	# ssl3005
		'91.198.174.107',	# ssl3006

		# bits
		# Not needed, but listed for completeness
		# pmtpa
		'208.80.152.77',	# sq67
		'208.80.152.78',	# sq68

		# eqiad
		'208.80.154.62',	# arsenic
		'208.80.154.143',	# niobium

		# esams
		'91.198.174.100', 	# cp3001
		'91.198.174.100',	# cp3002
		'91.198.174.89',	# cp3019
		'91.198.174.90',	# cp3020
		'91.198.174.91',	# cp3021
		'91.198.174.92',	# cp3022

		# Upload
		# pmtpa
		'208.80.152.51',	# sq41
		'208.80.152.52',	# sq42
		'208.80.152.53',	# sq43
		'208.80.152.54',	# sq44
		'208.80.152.55',	# sq45
		'208.80.152.58',	# sq48
		'208.80.152.59',	# sq49
		'208.80.152.60',	# sq50
		'208.80.152.61',	# sq51
		'208.80.152.62',	# sq52
		'208.80.152.63',	# sq53
		'208.80.152.64',	# sq54
		'208.80.152.65',	# sq55
		'208.80.152.66',	# sq56
		'208.80.152.67',	# sq57
		'208.80.152.68',	# sq58
		'208.80.152.89',	# sq79
		'208.80.152.90',	# sq80
		'208.80.152.91',	# sq81
		'208.80.152.92',	# sq82
		'208.80.152.93',	# sq83
		'208.80.152.94',	# sq84
		'208.80.152.95',	# sq85
		'208.80.152.96',	# sq86

		# eqiad
		'10.64.0.143',	# cp1021
		'10.64.0.144',	# cp1022
		'10.64.0.145',	# cp1023
		'10.64.0.146',	# cp1024
		'10.64.0.147',	# cp1025
		'10.64.0.148',	# cp1026
		'10.64.0.149',	# cp1027
		'10.64.0.150',	# cp1028
		'10.64.0.151',	# cp1029
		'10.64.0.152',	# cp1030
		'10.64.0.153',	# cp1031
		'10.64.0.154',	# cp1032
		'10.64.0.155',	# cp1033
		'10.64.0.156',	# cp1034
		'10.64.0.157',	# cp1035
		'10.64.0.158',	# cp1036

		# esams
		'91.198.174.26',	# knsq16
		'91.198.174.27',	# knsq17
		'91.198.174.28',	# knsq18
		'91.198.174.29',	# knsq19
		'91.198.174.30',	# knsq20
		'91.198.174.31',	# knsq21
		'91.198.174.32',	# knsq22
		'91.198.174.57',	# amssq47
		'91.198.174.58',	# amssq48
		'91.198.174.59',	# amssq49
		'91.198.174.60',	# amssq50
		'91.198.174.61',	# amssq51
		'91.198.174.62',	# amssq52
		'91.198.174.63',	# amssq53
		'91.198.174.64',	# amssq54
		'91.198.174.65',	# amssq55
		'91.198.174.66',	# amssq56
		'91.198.174.67',	# amssq57
		'91.198.174.68',	# amssq58
		'91.198.174.69',	# amssq59
		'91.198.174.70',	# amssq60
		'91.198.174.71',	# amssq61
		'91.198.174.72',	# amssq62

		# Mobile
		# eqiad
		'10.64.0.169',		# cp1041
		'10.64.0.170',		# cp1042
		'208.80.154.53',	# cp1043
		'208.80.154.54',	# cp1044

		# OTHERS - Currently unused..?
		'10.64.0.159',	# cp1037
		'10.64.0.160',	# cp1038
		'10.64.0.161',	# cp1039
		'10.64.0.162',	# cp1040

		'208.80.154.20',	# cp1061
		'208.80.154.21',	# cp1062
		'208.80.154.22',	# cp1063
		'208.80.154.23',	# cp1064
		'208.80.154.24',	# cp1065
		'208.80.154.25',	# cp1066
		'208.80.154.26',	# cp1067
		'208.80.154.27',	# cp1068
		'208.80.154.28',	# cp1069
		'208.80.154.29',	# cp1070
		'208.80.154.30',	# cp1071
		'208.80.154.31',	# cp1072
		'208.80.154.32',	# cp1073
		'208.80.154.33',	# cp1074
		'208.80.154.34',	# cp1075
		'208.80.154.35',	# cp1076
		'208.80.154.36',	# cp1077
		'208.80.154.37',	# cp1078
		'208.80.154.38',	# cp1079
		'208.80.154.39',	# cp1080
		'208.80.154.40',	# cp1081
	);

	# IP addresses that aren't proxies, regardless of what the other sources might say
	$wgProxyWhitelist = array(
		'68.124.59.186',
		'202.63.61.242',
		'62.214.230.86',
		'217.94.171.96',
	);

	break;

case 'labs':
	$wgSquidServers = array( '10.4.0.17' );
	$wgSquidServersNoPurge = array( '10.4.0.17' );

	break;
}

$wgBlockOpenProxies = false;

if ( $site == 'wikinews' ) {
	# $wgRightsPage = "";# Set to the title of a wiki page that describes your license/copyright
	$wgRightsUrl = "//creativecommons.org/licenses/by/2.5/";
	$wgRightsText = 'Creative Commons Attribution 2.5';
	$wgRightsIcon = "//creativecommons.org/images/public/somerights20.png";
}  elseif ( $wgDBname == 'huwikinews' ) {
	$wgRightsUrl = "//creativecommons.org/licenses/by/3.0/";
	$wgRightsText = 'Creative Commons Attribution 3.0 Unported';
	$wgRightsIcon = "//creativecommons.org/images/public/somerights20.png";
}  else {
	# Set 2009-06-22 -- BV
	$wgRightsUrl = "//creativecommons.org/licenses/by-sa/3.0/";
	$wgRightsText = 'Creative Commons Attribution-Share Alike 3.0 Unported';
	$wgRightsIcon = "//creativecommons.org/images/public/somerights20.png";
}

if( $wmfRealm == 'production' ) {
	$wgUDPProfilerHost = '10.0.6.30'; # professor
	$wgAggregateStatsID = $wgVersion;
}

// $wgProfiler is set in index.php
if ( isset( $wgProfiler ) ) {
	$wgProfiling = true;
	$wgProfileToDatabase = true;
	$wgProfileSampleRate = 1;
}

// CORS (cross-domain AJAX, bug 20814)
// This lists the domains that are accepted as *origins* of CORS requests
// DO NOT add domains here that aren't WMF wikis unless you really know what you're doing
if ( $wmgUseCORS ) {
	$wgCrossSiteAJAXdomains = array(
		'*.wikipedia.org',
		'*.wikinews.org',
		'*.wiktionary.org',
		'*.wikibooks.org',
		'*.wikiversity.org',
		'*.wikisource.org',
		'wikisource.org',
		'*.wikiquote.org',
		'*.wikidata.org',
		'wikidata.org',
		'www.mediawiki.org',
		'wikimediafoundation.org',
		'advisory.wikimedia.org',
		'auditcom.wikimedia.org',
		'boardgovcom.wikimedia.org',
		'board.wikimedia.org',
		'chair.wikimedia.org',
		'chapcom.wikimedia.org',
		'checkuser.wikimedia.org',
		'collab.wikimedia.org',
		'commons.wikimedia.org',
		'donate.wikimedia.org',
		'exec.wikimedia.org',
		'grants.wikimedia.org',
		'incubator.wikimedia.org',
		'internal.wikimedia.org',
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
	);
}

wfProfileOut( "$fname-misc1" );
wfProfileIn( "$fname-ext-include1" );

include( $IP . '/extensions/timeline/Timeline.php' );
if ( $wgDBname == 'testwiki' || $wgDBname == 'mlwiki' ) {
	// FreeSansWMF has been generated from FreeSans and FreeSerif by using this script with fontforge:
	// Open("FreeSans.ttf");
	// MergeFonts("FreeSerif.ttf");
	// SetFontNames("FreeSans-WMF", "FreeSans WMF", "FreeSans WMF Regular", "Regular", "");
	// Generate("FreeSansWMF.ttf", "", 4 );
	$wgTimelineSettings->fontFile = 'FreeSansWMF.ttf';
}
$wgTimelineSettings->fileBackend = 'local-multiwrite';

if ( file_exists( '/usr/bin/ploticus' ) ) {
	$wgTimelineSettings->ploticusCommand = '/usr/bin/ploticus';
}

$wgTimelineSettings->epochTimestamp = '20120101000000';
putenv( "GDFONTPATH=/usr/local/apache/common/fonts" );

include( $IP . '/extensions/wikihiero/wikihiero.php' );

include( $IP . '/extensions/SiteMatrix/SiteMatrix.php' );

// Config for sitematrix
$wgSiteMatrixFile = '/apache/common/langlist';
$wgSiteMatrixClosedSites = array_map( 'trim', file( getRealmSpecificFilename( "$IP/../closed.dblist" ) ) );
$wgSiteMatrixPrivateSites = array_map( 'trim', file( getRealmSpecificFilename( "$IP/../private.dblist" ) ) );
$wgSiteMatrixFishbowlSites = array_map( 'trim', file( getRealmSpecificFilename( "$IP/../fishbowl.dblist" ) ) );

include( $IP . '/extensions/CharInsert/CharInsert.php' );

include( $IP . '/extensions/ParserFunctions/ParserFunctions.php' );
$wgMaxIfExistCount = 500; // obs
$wgExpensiveParserFunctionLimit = 500;

// <ref> and <references> tags -ævar, 2005-12-23
require( $IP . '/extensions/Cite/Cite.php' );
require( $IP . '/extensions/Cite/SpecialCite.php' );

# Inputbox extension for searching or creating articles
include( $IP . '/extensions/InputBox/InputBox.php' );

include( $IP . '/extensions/ExpandTemplates/ExpandTemplates.php' );

include( $IP . '/extensions/ImageMap/ImageMap.php' );
include( $IP . '/extensions/SyntaxHighlight_GeSHi/SyntaxHighlight_GeSHi.php' );

// Experimental side-by-side comparison extension for wikisource. enabled brion 2006-01-13
if ( $wmgUseDoubleWiki ) {
	include( $IP . '/extensions/DoubleWiki/DoubleWiki.php' );
}

# Poem
include( $IP . '/extensions/Poem/Poem.php' );

if ( $wgDBname == 'testwiki' ) {
	include( $IP . '/extensions/UnicodeConverter/UnicodeConverter.php' );
}

// Per-wiki config for Flagged Revisions
if ( $wmgUseFlaggedRevs ) {
	include( "$wmfConfigDir/flaggedrevs.php" );
}

$wgUseAjax = true;
$wgCategoryTreeDynamicTag = true;
require( $IP . '/extensions/CategoryTree/CategoryTree.php' );
$wgCategoryTreeDisableCache = false;

if ( $wmgUseProofreadPage ) {
	include( $IP . '/extensions/ProofreadPage/ProofreadPage.php' );
	include( "$wmfConfigDir/proofreadpage.php" );
}
if ( $wmgUseLabeledSectionTransclusion ) {
	include( $IP . '/extensions/LabeledSectionTransclusion/lst.php' );
}

if ( $wmgUseSpamBlacklist ) {
	include( $IP . '/extensions/SpamBlacklist/SpamBlacklist.php' );
	$wgBlacklistSettings = array(
		'spam' => array(
			'files' => array(
				'http://meta.wikimedia.org/w/index.php?title=Spam_blacklist&action=raw&sb_ver=1'
			),
		),
	);
}

include( $IP . '/extensions/UploadBlacklist/UploadBlacklist.php' );
include( $IP . '/extensions/TitleBlacklist/TitleBlacklist.php' );

$wgTitleBlacklistSources = array(
	array(
		'type' => TBLSRC_URL,
		'src'  => "//meta.wikimedia.org/w/index.php?title=Title_blacklist&action=raw&tb_ver=1",
	),
);

if ( $wmgUseQuiz ) {
	include( "$IP/extensions/Quiz/Quiz.php" );
}

if ( $wmgUseGadgets ) {
	include( "$IP/extensions/Gadgets/Gadgets.php" );
}


if ( $wmgUseMwEmbedSupport ) {
	require_once( "$IP/extensions/MwEmbedSupport/MwEmbedSupport.php" );
}

if ( $wmgUseTimedMediaHandler ) {
	require_once( "$IP/extensions/TimedMediaHandler/TimedMediaHandler.php" );
	$wgTimedTextForeignNamespaces = array( 'commonswiki' => 102 );
	if ( $wgDBname == 'commonswiki' ) {
		$wgTimedTextNS = 102;
	}
	//overwrite enabling of local TimedText namespace
	$wgEnableLocalTimedText = $wmgEnableLocalTimedText;

	//enable transcoding on all wikis that allow uploads
	$wgEnableTranscode = $wgEnableUploads;

	$wgOggThumbLocation = false; // use ffmpeg for performance
	// $wgOggThumbLocation = '/usr/bin/oggThumb';

	//tmh1/2 have 12 cores and need lots of shared memory
	//for avconv / ffmpeg2theora
	$wgTranscodeBackgroundMemoryLimit = 4 * 1024 * 1024; // 4GB
}

include( $IP . '/extensions/AssertEdit/AssertEdit.php' );

if ( $wgUseContactPageFundraiser ) {
	include( "$IP/extensions/ContactPageFundraiser/ContactPage.php" );
	$wgContactUser = 'Storiescontact';
}

if ( $wgDBname == 'foundationwiki' ) {
	include( "$IP/extensions/FormPreloadPostCache/FormPreloadPostCache.php" );
	include( "$IP/extensions/SkinPerPage/SkinPerPage.php" );
	include( "$IP/extensions/skins/Schulenburg/Schulenburg.php" );
	include( "$IP/extensions/skins/Tomas/Tomas.php" );
	include( "$IP/extensions/skins/Donate/Donate.php" );

	$wgAllowedTemplates = array(
		'enwiki_00', 'enwiki_01', 'enwiki_02', 'enwiki_03',
		'enwiki_04', 'enwiki_05', 'donate', '2009_Notice1',
		'2009_Notice1_b', '2009_EM1Notice', '2009_EM1Notice_b', '2009_Notice11',
		'2009_Notice10', '2009_Notice14', '2009_Notice15', '2009_Notice17',
		'2009_Notice17_g', '2009_Notice18', '2009_Notice18_g', '2009_Notice21_g',
		'2009_Notice22', '2009_Notice22_g', '2009_Notice30', '2009_Notice31',
		'2009_Notice32', '2009_Notice33', '2009_Notice34', '2009_Notice30_g',
		'2009_Notice30_EML', 'Notice30_EML', '2009_Notice35', '2009_Notice36',
		'2009_Notice36_g', '2009_Notice37', '2009_Notice38', '2009_Notice39',
		'2009_Notice40', '2009_Notice30_bold', '2009_Yandex1', '2009_Notice41',
		'2009_Notice42', '2009_Notice43', '2009_Notice44', '2009_Notice45',
		'2009_Notice47', '2009_Notice46', '2009_Notice48', '2009_Craig_Appeal1',
		'2009_Jimmy_Appeal1', '2009_Jimmy_Appeal3', '2009_Jimmy_Appeal4', '2009_Jimmy_Appeal5',
		'2009_Jimmy_Appeal7', '2009_Jimmy_Appeal8', '2009_Jimmy_Appeal9', '2009_Notice49',
		'2009_Notice51', '2009_ThankYou1', '2009_ThankYou2', '2010_testing1',
		'2010_testing1B', '2010_testing2', '2010_testing2B', '2010_testing3',
		'2010_testing3B', '2010_testing4', '2010_testing4B', '2010_testing5',
		'2010_testing5_anon', '2010_testing6', '2010_testing6_anon', '2010_testing7',
		'2010_testing7_anon', '2010_testing8', '2010_testing8_anon', '2010_testing9',
		'2010_testing9_anon', '2010_testing10', '2010_testing10_anon', '2010_testing11',
		'2010_testing11_anon', '2010_testing12', '2010_testing12_anon', '2010_testing13',
		'2010_testing13_anon', '2010_testing14', '2010_testing14_anon', '2010_testing15',
		'2010_testing15_anon', '2010_testing16', '2010_testing17', '2010_testing18',
		'2010_testing15_anon', '2010_testing16', '2010_testing17', '2010_testing18',
		'2010_testing19', '2010_testing20', '2010_testing21', '2010_testing22',
		'2010_testing23', '2010_testing24', '2010_testing25', '2010_testing26',
		'2010_testing23', '2010_testing24', '2010_testing25', '2010_testing26',
		'2010_testing23', '2010_testing24', '2010_testing25', '2010_testing26',
		'2010_testing27', '2010_testing28', '2010_testing29', '2010_testing30',
		'2010_testing31', '2010_testing32', '2010_testing33', '2010_testing34',
		'2010_testing35', '2010_testing36', '2010_testing37', '2010_testing38',
		'2010_testing39', '2010_testing40', '2010_testing41', '2010_testing42',
		'2010_testing43', '2010_testing44', '2010_testing44_twostep', '2010_testing45',
		'2010_testing46', '2010_testing47', '2010_testing48', '2010_testing49',
		'2010_testing50', '2010_testing51', '2010_testing52', '2010_testing53',
		'2010_testing54', '2010_testing55', '2010_fr_testing1', '2010_fr_testing5',
		'2010_fr_testing3', '2010_fr_testing4', '2010_de_testing1', '2010_de_testing2',
		'2010_de_testing3', '2010_de_testing4', '2010_en_testing1', ',2010_en_testing2',
		'2010_en_testing3', '2010_en_testing4', '2010_en_testing5', '2010_en_testing6',
		'2010_en_testing7', '2010_en_testing8', '2010_en_testing9', '2010_en_testing10',
		'2010_en_testing11', '2010_en_testing12', '2010_en_testing13', '2010_en_testing14',
		'2010_en_testing15', '2010_en_testing16', '2010_en_testing17', '2010_en_testing18',
		'2010_en_testing19', '2010_en_testing20', '2010_en_testing21', '2010_en_testing22',
		'2010_en_testing23', '2010_en_testing24', '2010_en_testing25', '2010_en_testing26',
		'2010_en_testing27', '2010_en_testing28', '2010_en_testing29', '2010_en_testing30',
		'2010_en_testing31', '2010_en_testing32', '2010_en_testing33', '2010_en_testing34',
		'2010_en_testing35', '2010_en_testing36', '2010_en_testing37', '2010_en_testing38',
		'2010_en_testing39', '2010_en_testing40',
	);

	$wgAllowedSupport = array(
		'Support', 'Support2', 'ChangeWorld', 'FiveFacts',
		'Craig_Appeal', 'Appeal', 'Appeal2', 'Global_Support',
		'2010_Landing_1', '2010_Landing_2', '2010_Landing_3', '2010_Landing_4',
		'2010_Landing_5', '2010_Landing_6', '2010_Landing_7', '2010_Landing_8',
		'2010_Landing_9', 'cc1', 'cc2', 'cc3', 'cc4', 'cc5', 'cc6', 'cc7', 'cc8', 'cc9',
		'cc10', 'cc11', 'cc12', 'cc13', 'cc14', 'cc15', 'Appeal3', 'Appeal4', 'Appeal5',
		'Appeal6', 'Appeal7', 'Appeal8', 'Appeal9', 'Appeal10', 'Appeal11', 'Appeal12',
		'Appeal13', 'Appeal14', 'Appeal16', 'Appeal18', 'Appeal20', 'cc15',
	);

	$wgAllowedPaymentMethod = array(
		'cc', 'pp'
	);
}

if ( $wmgUseContributionReporting ) {
	include( "$IP/extensions/ContributionReporting/ContributionReporting.php" );
	include( "$wmfConfigDir/reporting-setup.php" );
}

if ( $wmgPFEnableStringFunctions ) {
	$wgPFEnableStringFunctions = true;
}

if ( $wgDBname == 'mediawikiwiki' ) {
	include( "$IP/extensions/ExtensionDistributor/ExtensionDistributor.php" );
	$wgExtDistListFile = 'https://gerrit.wikimedia.org/mediawiki-extensions.txt';
	$wgExtDistArchiveAPI = 'https://api.github.com/repos/wikimedia/mediawiki-extensions-$EXT/tarball/$REF';
	$wgExtDistProxy = 'url-downloader.wikimedia.org:8080';

	// When changing the Snapshot Refs please change the corresponding
	// extension distributor messages for mediawiki.org in WikimediaMessages.i18n.php too
	$wgExtDistSnapshotRefs = array(
		'master',
		'REL1_20',
		'REL1_19',
	);
}

include( $IP . '/extensions/GlobalBlocking/GlobalBlocking.php' );
$wgGlobalBlockingDatabase = 'centralauth';
$wgApplyGlobalBlocks = $wmgApplyGlobalBlocks;

include( $IP . '/extensions/TrustedXFF/TrustedXFF.php' );
if ( function_exists( 'dba_open' ) && file_exists( "$wmfConfigDir/trusted-xff.cdb" ) ) {
	$wgTrustedXffFile = "$wmfConfigDir/trusted-xff.cdb";
}

if ( $wmgContactPageConf ) {
	include( $IP . '/extensions/ContactPage/ContactPage.php' );
	extract( $wmgContactPageConf );
}

if ( $wmgUseSecurePoll ) {
	include( $IP . '/extensions/SecurePoll/SecurePoll.php' );

	$wgHooks['SecurePoll_JumpUrl'][] = 'wmfSecurePollJumpUrl';

	function wmfSecurePollJumpUrl( $page, &$url ) {
		global $site, $lang;

		$url = wfAppendQuery( $url, array( 'site' => $site, 'lang' => $lang ) );
		return true;
	}
}

// Email capture
// NOTE: Must be BEFORE ArticleFeedback
if ( $wgUseEmailCapture ) {
	include( "$IP/extensions/EmailCapture/EmailCapture.php" );
	$wgEmailCaptureAutoResponse['from'] = 'improve@wikimedia.org';
}

// PoolCounter
if ( $wmgUsePoolCounter ) {
	include( getRealmSpecificFilename( "$wmfConfigDir/PoolCounterSettings.php" ) );
}

if ( $wmgUseScore ) {
	include( "$IP/extensions/Score/Score.php" );
	$wgScoreFileBackend = $wmgScoreFileBackend;
	$wgScorePath = $wmgScorePath;
}

wfProfileOut( "$fname-ext-include1" );
wfProfileIn( "$fname-misc2" );

# Upload spam system
// SHA-1 hashes of blocked files:
# FIXME should check file size too
$ubUploadBlacklist = array(
	// Goatse:
	'aebbf277146e497c036937d3c3d6d0cac49a37a8', // 20050901082002!Patoo.jpg
	// Spam:
	'7740dab676725bcf6ea58b03b231aa4ec6c7ff34', // Austriaflaggemodern.jpg
	'1f1c44af6ee4f6e4b6cb48b892e625fa52238bd1', // Nostalgieplattenspielerei.jpg
	'e6eb4549756b88e2c69171ffbd278be51c3e2bfe', // Patioboy.jpg
	'eeb9b16edb9b5e9c58f47a558589e7eb970f32c0', // Shoessss.jpg, 73464736474847367.jpg
	'14e4858e63b008a7e087f2b90d3f57c021ab0f78', // Vacuumbigmell.jpg
	'f989e303ef505c4706db42d5cdad67841042e2b9', // 998_pre_1.jpg
	// Ass pus:
	'27979159b13b819d1bf62e1071a0c2a54b373ed5', // Squish.png
	'7176aeddf3d7d8aada785721773ffeb7ee7b292e', // 20050905221505!Linguistics_stub.png *
	'27979159b13b819d1bf62e1071a0c2a54b373ed5', // 20050905235133!Leaf.png
	'bb3acc61413ef813453a4b0c0198e30b2cd8fcf9', // Kitty100.jpg
	'855e55c4925644aeaef262ef25dd00815761c076', // Wikipedia-logo-100px
	'203bc24e5291e543779201734c49cfd88fcb2445', // Wikipodia-logo.png
	'14d2a0c0f3081815d04493f72ab5970c51422bc7', // Bung.jpg
	'3c610bc87d0ba49467c6f2d3cfba4b3321f6b351', // Blue_morpho_butterfly_300x271.png
	'7176aeddf3d7d8aada785721773ffeb7ee7b292e', // 20050905235450!Blue_morpho_butterfly_300x271.png
	'7a7f9d7ef52ed8967cb6b0171ef8d45e2a0c68b9', // Leaf.png
	'1ecfaf883c4130e1827290ad063158d0037631e6', // Wikimedia-button1.png
	'1c73d6596685175a8af6b08508468252c4dff8e2', // Windbuchencom.jpg
	'203bc24e5291e543779201734c49cfd88fcb2445', // Leaf.png
	'95d825bcf01ca3e553f4175dd7238ff12ba1d153', // 20050915055251!New_Orleans_Survivor_Flyover.jpg
	'bbd292d917d7fa7dec9a524de77ca39bd8cdf738', // 20050915060435!New_Orleans_Survivor_Flyover.jpg

	// Some singnet guy
	'bed74eef04f5b54884dc650679e5688c7c1f74cb', // Peniscut.jpg
);

$wgHiddenPrefs[] = 'realname';

# Default address gets rejected by some mail hosts
$wgPasswordSender = 'wiki@wikimedia.org';

# e-mailing password based on e-mail address (bug 34386)
$wgPasswordResetRoutes['email'] = true;

# Cluster-dependent files for file backend
require( getRealmSpecificFilename( "$wmfConfigDir/filebackend.php" ) );

# Cluster-dependent files for job queue and job queue aggregator
require( getRealmSpecificFilename( "$wmfConfigDir/jobqueue.php" ) );

if( $wgDBname != 'commonswiki' ) {
	$wgDefaultUserOptions['watchcreations'] = 1;
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
	$wgDefaultUserOptions['highlightbroken'] = 0;

	// Nostalgia skin
	include( "$IP/extensions/Nostalgia/Nostalgia.php" );
} else {
	$wgHooks['BeforePageDisplay'][] = function( &$out, $skin ) use ( $wgDBname ) {
		$badSkinName = $skin->getSkinName();
		if ( in_array( $badSkinName, array( 'chick', 'simple', 'myskin', 'nostalgia', 'standard' ) ) ) {
			$metaPage = $wgDBname == 'metawiki' ? 'Turning off outdated skins' : 'meta:Turning off outdated skins';
			$removeDate = $out->getLang()->date( '2013-04-15 00:00:00' );
			$out->prependHTML( '<span class="warning">' . $out->msg( 'wikimedia-oldskin-removal',
				$out->msg( "skinname-$badSkinName" )->text(), $removeDate, $metaPage )->parse() . '</span>' );
		}
		return true;
	};
}

$wgUseHashTable = true;

$wgCopyrightIcon = '<a href="//wikimediafoundation.org/"><img src="//bits.wikimedia.org/images/wikimedia-button.png" width="88" height="31" alt="Wikimedia Foundation"/></a>';

# For Special:Cite, we only want it on wikipedia (but can't count on $site),
# not on these fakers.
$wgLanguageCodeReal = $wgLanguageCode;
# Fake it up
if ( in_array( $wgLanguageCode, array( 'commons', 'meta', 'sources', 'species', 'foundation', 'nostalgia', 'mediawiki' ) ) ) {
	$wgLanguageCode = 'en';
}

$wgDisableCounters     = true;

wfProfileOut( "$fname-misc2" );

# This is overridden in the Lucene section below
$wgDisableTextSearch   = true;
$wgDisableSearchUpdate = true;

# :SEARCH:
# Better make sure the global setting is enabled
$wgUseLuceneSearch = true;
if ( $wgUseLuceneSearch ) {
	wfProfileIn( "$fname-lucene" );
	include( "$wmfConfigDir/lucene-common.php" );
	wfProfileOut( "$fname-lucene" );
}

// Case-insensitive title prefix search extension
// Load this _after_ Lucene so Lucene's prefix search can be used
// when available (for OpenSearch suggestions and AJAX search mode)
// But note we still need TitleKey for "go" exact matches and similar.
if ( $wmgUseTitleKey ) {
	include "$IP/extensions/TitleKey/TitleKey.php";
}

wfProfileIn( "$fname-misc3" );

// Various DB contention settings
$wgUseDumbLinkUpdate = false;
$wgAntiLockFlags = ALF_NO_LINK_LOCK | ALF_NO_BLOCK_LOCK;
# $wgAntiLockFlags = ALF_PRELOAD_LINKS | ALF_PRELOAD_EXISTENCE;
if ( in_array( $wgDBname, array( 'testwiki', 'test2wiki', 'mediawikiwiki', 'commonswiki' ) ) ) {
	$wgSiteStatsAsyncFactor = 1;
}

# Deferred update still broken
$wgMaxSquidPurgeTitles = 500;

$wgInvalidateCacheOnLocalSettingsChange = false;

// General Cache Epoch:
$wgCacheEpoch = '20120908000000';

$wgThumbnailEpoch = '20120101000000';

# OAI repository for update server
include( $IP . '/extensions/OAI/OAIRepo.php' );
$oaiAgentRegex = '/experimental/';
$oaiAuth = true; # broken... squid? php config? wtf
$oaiAudit = true;
$oaiAuditDatabase = 'oai';
$oaiChunkSize = 40;

$wgEnableUserEmail = true;

# XFF log for vandal tracking
function wfLogXFF() {
	global $wmfUdp2logDest;
	if ( ( @$_SERVER['REQUEST_METHOD'] ) == 'POST' ) {
		$uri = ( $_SERVER['HTTPS'] ? 'https://' : 'http://' ) .
			$_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

		wfErrorLog(
			gmdate( 'r' ) . "\t" .
			"$uri\t" .
			"{$_SERVER['HTTP_X_FORWARDED_FOR']}, {$_SERVER['REMOTE_ADDR']}\t" .
			( $_REQUEST['wpSave'] ? 'save' : '' ) . "\n",
			"udp://$wmfUdp2logDest/xff"
		);
	}
}
$wgExtensionFunctions[] = 'wfLogXFF';

// bug 24313, turn off minordefault on enwiki
if ( $wgDBname == 'enwiki' ) {
	$wgHiddenPrefs[] = 'minordefault';
}

if ( $wmgUseFooterContactLink ) {
	$wgHooks['SkinTemplateOutputPageBeforeExec'][] = function ( $sk, &$tpl ) {
		$contactLink = Html::element( 'a', array( 'href' => $sk->msg( 'contact-url' )->escaped() ),
			$sk->msg( 'contact' )->text() );
		$tpl->set( 'contact', $contactLink );
		$tpl->data['footerlinks']['places'][] = 'contact';
		return true;
	};
}

// bug 33186: turn off incomplete feature action=imagerotate
$wgAPIModules['imagerotate'] = 'ApiDisabled';


wfProfileOut( "$fname-misc3" );
wfProfileIn( "$fname-ext-include2" );

if ( $wmgUseDPL ) {
	include( $IP . '/extensions/intersection/DynamicPageList.php' );
}

include( $IP . '/extensions/Renameuser/Renameuser.php' );

if ( $wmgUseSpecialNuke ) {
	include( $IP . '/extensions/Nuke/Nuke.php' );
}

include( "$IP/extensions/AntiBot/AntiBot.php" );
$wgAntiBotPayloads = array(
	'default' => array( 'log', 'fail' ),
);

if ( $wmgUseTorBlock ) {
	include( "$IP/extensions/TorBlock/TorBlock.php" );
	$wgTorLoadNodes = false;
	$wgTorIPs = array( '91.198.174.232', '208.80.152.2', '208.80.152.134' );
	$wgTorAutoConfirmAge = 90 * 86400;
	$wgTorAutoConfirmCount = 100;
	$wgTorDisableAdminBlocks = false;
	$wgTorTagChanges = false;
	$wgGroupPermissions['user']['torunblocked'] = false;
}

if ( $wmgUseRSSExtension ) {
	include( "$IP/extensions/RSS/RSS.php" );
	# $wgRSSProxy = 'url-downloader.wikimedia.org:8080';
	$wgRSSUrlWhitelist = $wmgRSSUrlWhitelist;
}

wfProfileOut( "$fname-ext-include2" );
wfProfileIn( "$fname-misc4" );

$wgActions['credits'] = false;

# Process group overrides

$wgGroupPermissions['steward'   ]['userrights'] = true;
$wgGroupPermissions['bureaucrat']['userrights'] = false;

$wgGroupPermissions['sysop']['bigdelete'] = false; // quick hack

foreach ( $groupOverrides2 as $group => $permissions ) {
	if ( !array_key_exists( $group, $wgGroupPermissions ) ) {
		$wgGroupPermissions[$group] = array();
	}
	$wgGroupPermissions[$group] = $permissions + $wgGroupPermissions[$group];
}

foreach ( $groupOverrides as $group => $permissions ) {
	if ( !array_key_exists( $group, $wgGroupPermissions ) ) {
		$wgGroupPermissions[$group] = array();
	}
	$wgGroupPermissions[$group] = $permissions + $wgGroupPermissions[$group];
}

$wgAutopromote = array(
	'autoconfirmed' => array( '&',
		array( APCOND_EDITCOUNT, $wgAutoConfirmCount ),
		array( APCOND_AGE, $wgAutoConfirmAge ),
	),
);

if ( is_array( $wmgAutopromoteExtraGroups ) ) {
	$wgAutopromote += $wmgAutopromoteExtraGroups;
}

$wgAutopromoteOnce = array(
	'onEdit' => $wmgAutopromoteOnceonEdit,
	'onView' => $wmgAutopromoteOnceonView,
);

if ( is_array( $wmgExtraImplicitGroups ) ) {
	$wgImplicitGroups = array_merge( $wgImplicitGroups, $wmgExtraImplicitGroups );
}

if ( $wmfRealm == 'labs' ) {
	$wgHTTPTimeout = 10;
}

$wgProxyList = "$wmfConfigDir/mwblocker.log";

if ( getenv( 'WIKIDEBUG' ) ) {
	$wgDebugLogFile = '/tmp/wiki.log';
	$wgDebugDumpSql = true;
	$wgDebugLogGroups = array();
	foreach ( $wgDBservers as $key => $val ) {
		$wgDBserver[$key]['flags'] |= 1;// DBO_DEBUG;
	}
	foreach ( $wgExternalServers as $key => $val ) {
		foreach ( $val as $x => $y ) {
			$wgExternalServers[$key][$x]['flags'] |= 1;// DBO_DEBUG;
		}
	}
}

wfProfileOut( "$fname-misc4" );
wfProfileIn( "$fname-misc5" );

$wgBrowserBlackList[] = '/^Lynx/';

if ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' ) {
	// New HTTPS service on regular URLs
	$wgInternalServer = $wgServer; // Keep this as HTTP for IRC notifications (bug 29925)
	$wgServer = preg_replace( '/^http:/', 'https:', $wgServer );
}

if ( isset( $_REQUEST['captchabypass'] ) && $_REQUEST['captchabypass'] == $wmgCaptchaPassword ) {
	$wmgEnableCaptcha = false;
}

if ( $wmgEnableCaptcha ) {
	require( "$IP/extensions/ConfirmEdit/ConfirmEdit.php" );
	require( "$IP/extensions/ConfirmEdit/FancyCaptcha.php" );
	$wgGroupPermissions['autoconfirmed']['skipcaptcha'] = true;
	if ( $wmfRealm !== 'labs' ) {
		$wgCaptchaFileBackend = 'global-multiwrite';
	}
	# $wgCaptchaTriggers['edit'] = true;
	$wgCaptchaSecret = $wmgCaptchaSecret;
	$wgCaptchaDirectory = '/mnt/upload7/private/captcha';
	$wgCaptchaDirectoryLevels = 3;
	$wgCaptchaStorageClass = 'CaptchaCacheStore';
	$wgCaptchaClass = 'FancyCaptcha';
	$wgCaptchaWhitelist = '#^(https?:)?//([.a-z0-9-]+\\.)?((wikimedia|wikipedia|wiktionary|wikiquote|wikibooks|wikisource|wikispecies|mediawiki|wikimediafoundation|wikinews|wikiversity|wikivoyage|wikidata)\.org|dnsstuff\.com|completewhois\.com|wikimedia\.de|toolserver\.org)(/|$)#i';
	$wgCaptchaWhitelistIP = array( '91.198.174.0/24' ); # toolserver (bug 23982)
	if ( $wgDBname == 'testwiki' ) {
		$wgCaptchaTriggers['create'] = true;
	}

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
}

require( "$IP/extensions/Oversight/HideRevision.php" );
$wgGroupPermissions['oversight']['hiderevision'] = false;
// $wgGroupPermissions['oversight']['oversight'] = true;

if ( extension_loaded( 'wikidiff2' ) ) {
	$wgExternalDiffEngine = 'wikidiff2';
}

if ( function_exists( 'dba_open' ) && file_exists( "$wmfConfigDir/interwiki.cdb" ) ) {
	$wgInterwikiCache = "$wmfConfigDir/interwiki.cdb";
}

$wgEnotifUseJobQ = true;

// Username spoofing / mixed-script / similarity check detection
include $IP . '/extensions/AntiSpoof/AntiSpoof.php';

// For transwiki import
ini_set( 'user_agent', 'Wikimedia internal server fetcher (noc@wikimedia.org' );

// CentralAuth
if ( $wmgUseCentralAuth ) {
	include "$IP/extensions/CentralAuth/CentralAuth.php";

	$wgCentralAuthDryRun = false;
	# unset( $wgGroupPermissions['*']['centralauth-merge'] );
	# $wgGroupPermissions['sysop']['centralauth-merge'] = true;
	$wgCentralAuthCookies = true;

	$wgDisableUnmergedEditing = $wmgDisableUnmergedEdits;

	# Broken -- TS
	if( $wmfRealm == 'production' ) {
		$wgCentralAuthUDPAddress = $wgRC2UDPAddress;
		$wgCentralAuthNew2UDPPrefix = "#central\t";
	}

	switch ( $wmfRealm ) {
	case 'production':
		// Production cluster
		$wmgSecondLevelDomainRegex = '/^\w+\.\w+\./';
		$wgCentralAuthAutoLoginWikis = $wmgCentralAuthAutoLoginWikis;
		break;

	case 'labs':
		// wmflabs beta cluster
		$wmgSecondLevelDomainRegex = '/^\w+\.\w+\.\w+\.\w+\./';
		$wgCentralAuthAutoLoginWikis = array(
			'incubator.wikimedia.beta.wmflabs.org' => 'incubatorwiki',
			'.wikipedia.beta.wmflabs.org' => 'enwiki',
			'.wikisource.beta.wmflabs.org' => 'enwikisource',
			'.wikibooks.beta.wmflabs.org' => 'enwikibooks',
			'.wikiversity.beta.wmflabs.org' => 'enwikiversity',
			'.wikiquote.beta.wmflabs.org' => 'enwikiquote',
			'.wikinews.beta.wmflabs.org' => 'enwikinews',
			'.wiktionary.beta.wmflabs.org' => 'enwiktionary',
			'meta.wikimedia.beta.wmflabs.org' => 'metawiki',
			'deployment.wikimedia.beta.wmflabs.org' => 'labswiki',
			'test.wikimedia.beta.wmflabs.org' => 'testwiki',
			'commons.wikimedia.beta.wmflabs.org' => 'commonswiki',
			'ee-prototype.wikipedia.beta.wmflabs.org' => 'ee_prototypewiki',
		);
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

	$wgHooks['CentralAuthWikiList'][] = 'wmfCentralAuthWikiList';
	function wmfCentralAuthWikiList( &$list ) {
		global $wgLocalDatabases, $IP, $wgSiteMatrixPrivateSites,
			$wgSiteMatrixFishbowlSites, $wgSiteMatrixClosedSites;

		$list = array_diff(
			$wgLocalDatabases,
			$wgSiteMatrixPrivateSites,
			$wgSiteMatrixFishbowlSites,
			$wgSiteMatrixClosedSites
		);
		return true;
	}

	// Let's give it another try
	$wgCentralAuthCreateOnView = true;
}

// taking it live 2006-12-15 brion
if ( $wmgUseDismissableSiteNotice ) {
	require( "$IP/extensions/DismissableSiteNotice/DismissableSiteNotice.php" );
}
$wgMajorSiteNoticeID = '2';

$wgHooks['LoginAuthenticateAudit'][] = 'logBadPassword';
$wgHooks['PrefsEmailAudit'][] = 'logPrefsEmail';
$wgHooks['PrefsPasswordAudit'][] = 'logPrefsPassword';

function logBadPassword( $user, $pass, $retval ) {
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

		wfDebugLog( 'badpass', "$bit for sysop '" .
			$user->getName() . "' from " . $wgRequest->getIP() .
			# " - " . serialize( apache_request_headers() )
			" - " . @$headers['X-Forwarded-For'] .
			' - ' . @$headers['User-Agent'] .
			''
			 );
	}
	return true;
}

function logPrefsEmail( $user, $old, $new ) {
	if ( $user->isAllowed( 'delete' ) ) {
		global $wgRequest;
		$headers = apache_request_headers();

		wfDebugLog( 'badpass', "Email changed in prefs for sysop '" .
			$user->getName() .
			"' from '$old' to '$new'" .
			" - " . $wgRequest->getIP() .
			# " - " . serialize( apache_request_headers() )
			" - " . @$headers['X-Forwarded-For'] .
			' - ' . @$headers['User-Agent'] .
			'' );
	}
	return true;
}

function logPrefsPassword( $user, $pass, $status ) {
	if ( $user->isAllowed( 'delete' ) ) {
		global $wgRequest;
		$headers = apache_request_headers();

		wfDebugLog( 'badpass', "Password change in prefs for sysop '" .
			$user->getName() .
			"': $status" .
			" - " . $wgRequest->getIP() .
			# " - " . serialize( apache_request_headers() )
			" - " . @$headers['X-Forwarded-For'] .
			' - ' . @$headers['User-Agent'] .
			'' );
	}
	return true;
}

if ( file_exists( '/etc/wikimedia-image-scaler' ) ) {
	$wgMaxShellMemory = 400 * 1024;
	$wgMaxShellFileSize = 400 * 1024;
}
$wgMaxShellTime = 50; // so it times out before PHP and curl and squid

// Use a cgroup for shell execution.
// This will cause shell execution to fail if the cgroup is not installed.
// If some misc server doesn't have the cgroup installed, you can create it
// with: mkdir -p -m777 /sys/fs/cgroup/memory/mediawiki/job
$wgShellCgroup = '/sys/fs/cgroup/memory/mediawiki/job';

switch( $wmfRealm ) {
case 'production'  :
	$wgImageMagickTempDir = '/a/magick-tmp';
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

	// for banner loading
	if ( $wgDBname == 'testwiki' ) {
		$wgCentralPagePath = "//test.wikipedia.org/w/index.php";
		$wgCentralBannerDispatcher = "//test.wikipedia.org/wiki/Special:BannerRandom";
		$wgCentralBannerRecorder = "//test.wikipedia.org/wiki/Special:RecordImpression";
	} else {
		$wgCentralPagePath = "//{$wmfHostnames['meta']}/w/index.php";
		$wgCentralBannerDispatcher = "//{$wmfHostnames['meta']}/wiki/Special:BannerRandom";
		$wgCentralBannerRecorder = "//{$wmfHostnames['meta']}/wiki/Special:RecordImpression";
	}

	// Allow only these domains to access CentralNotice data through the reporter
	$wgNoticeReporterDomains = 'https://donate.wikimedia.org';

	$wgNoticeProject = $wmgNoticeProject;

	$wgCentralDBname = 'metawiki';
	if ( $wmfRealm == 'production' && $wgDBname == 'testwiki' ) {
		# test.wikipedia.org has its own central database:
		$wgCentralDBname = 'testwiki';
	}

	$wgCentralNoticeLoader = $wmgCentralNoticeLoader;

	# Wed evening -- all on!
	$wgNoticeTimeout = 3600;
	switch( $wmfRealm ) {
	case 'production':
		$wgNoticeServerTimeout = 3600; // to let the counter update
		$wgNoticeCounterSource = '//wikimediafoundation.org/wiki/Special:ContributionTotal' .
			'?action=raw' .
			'&start=20101112000000' . // FY 10-11
			'&fudgefactor=660000';   // fudge for pledged donations not in CRM
		break;
	}

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
	/* Hopefully we don't need to use this anymore
	$wgExtraNamespaces[866] = 'CNBanner';
	$wgNamespacesWithSubpages[866] = true;
	$wgTranslateMessageNamespaces[] = 866;

	$wgExtraNamespaces[867] = 'CNBanner_talk';
	$wgNamespacesWithSubpages[867] = true;
	*/

	$wgTranslateWorkflowStates['Centralnotice-tgroup'] = array(
		'new' => array( 'color' => 'FF0000' ), // red
		'needs_proofreading' => array( 'color' => '0000FF' ), // blue
		'ready' => array( 'color' => 'FFFF00' ), // yellow
		'published' => array(
			'color' => '00FF00', // green
			'right' => 'centralnotice-admin',
		),
	);
}

// Set CentralNotice banner hide cookie; Needs to be enabled for all wikis that display banners ~awjr 2011-11-07
if ( $wmgSetNoticeHideBannersExpiration && $wmgUseCentralNotice ) {
	// Expire the cookie on 2012-12-26. If this is in the past
	// Special:HideBanners will set it to 2 weeks from today.
	$wgNoticeHideBannersExpiration = 1356480000;
}

// Load our site-specific l10n extensions
include "$IP/extensions/WikimediaMessages/WikimediaMessages.php";
if ( $wmgUseWikimediaLicenseTexts ) {
	include "$IP/extensions/WikimediaMessages/WikimediaLicenseTexts.php";
}

function wfNoDeleteMainPage( &$title, &$user, $action, &$result ) {
	if ( $action !== 'delete' && $action !== 'move' ) {
		return true;
	}
	$main = Title::newMainPage();
	$mainText = $main->getPrefixedDBkey();
	if ( $mainText === $title->getPrefixedDBkey() ) {
		$result = array( 'cant-delete-main-page' );
		return false;
	}
	return true;
}

if ( $wgDBname == 'enwiki' ) {
	// Please don't interferew with our hundreds of wikis ability to manage themselves.
	// Only use this shitty hack for enwiki. Thanks.
	// -- brion 2008-04-10
	$wgHooks['getUserPermissionsErrorsExpensive'][] = 'wfNoDeleteMainPage';
}

// PHP language binding to make Swift accessible via cURL
include "$IP/extensions/SwiftCloudFiles/SwiftCloudFiles.php";

// Quickie extension that addsa  bogus field to edit form and whinges if it's filled out
// Might or might not do anything useful :D
// Enabling just to log to udp://$wmfUdp2logDest/spam
include "$IP/extensions/SimpleAntiSpam/SimpleAntiSpam.php";

if ( $wmgUseCollection ) {
	// PediaPress / PDF generation
	include "$IP/extensions/Collection/Collection.php";
	$wgCollectionMWServeURL = "http://pdf1.wikimedia.org:8080/mw-serve/";

	// MediaWiki namespace is not a good default
	$wgCommunityCollectionNamespace = NS_PROJECT;

	// Allow collecting Help pages
	$wgCollectionArticleNamespaces[] = NS_HELP;

	// Sidebar cache doesn't play nice with this
	$wgEnableSidebarCache = false;

	$wgCollectionFormats = array(
		'rl' => 'PDF',
	//	'epub' => 'EPUB', // disabling by default per reqest from tfinc 14 July 2012
		'odf' => 'ODT',
		'zim' => 'openZIM',
	);
	if ( $wmgCollectionUseEpub ) {
		$wgCollectionFormats[ 'epub' ] = 'EPUB';
	}

	$wgLicenseURL = "http://creativecommons.org/licenses/by-sa/3.0/";

	$wgCollectionPortletForLoggedInUsersOnly = $wmgCollectionPortletForLoggedInUsersOnly;
	$wgCollectionArticleNamespaces = $wmgCollectionArticleNamespaces;

	if ( $wmgCollectionHierarchyDelimiter ) {
		$wgCollectionHierarchyDelimiter = $wmgCollectionHierarchyDelimiter;
	}

	$wgCollectionPortletFormats = $wmgCollectionPortletFormats;
}

include( "$IP/extensions/OpenSearchXml/OpenSearchXml.php" );

# Various system to allow/prevent flooding
# (including exemptions for scheduled outreach events)
require( "$wmfConfigDir/throttle.php" );

if ( $wmgUseNewUserMessage ) {
	include "$IP/extensions/NewUserMessage/NewUserMessage.php";
	$wgNewUserSuppressRC = $wmgNewUserSuppressRC;
	$wgNewUserMinorEdit = $wmgNewUserMinorEdit;
	$wgNewUserMessageOnAutoCreate = $wmgNewUserMessageOnAutoCreate;
}

if ( $wmgUseCodeReview ) {
	include "$IP/extensions/CodeReview/CodeReview.php";
	include( "$wmfConfigDir/codereview.php" );
	$wgSubversionProxy = 'http://codereview-proxy.wikimedia.org/index.php';

	$wgGroupPermissions['user']['codereview-add-tag'] = false;
	$wgGroupPermissions['user']['codereview-remove-tag'] = false;
	$wgGroupPermissions['user']['codereview-post-comment'] = false;
	$wgGroupPermissions['user']['codereview-set-status'] = false;
	$wgGroupPermissions['user']['codereview-link-user'] = false;
	$wgGroupPermissions['user']['codereview-signoff'] = false;
	$wgGroupPermissions['user']['codereview-associate'] = false;

	$wgGroupPermissions['user']['codereview-post-comment'] = true;
	$wgGroupPermissions['user']['codereview-signoff'] = true;

	$wgGroupPermissions['coder']['codereview-add-tag'] = true;
	$wgGroupPermissions['coder']['codereview-remove-tag'] = true;
	$wgGroupPermissions['coder']['codereview-set-status'] = true;
	$wgGroupPermissions['coder']['codereview-link-user'] = true;
	$wgGroupPermissions['coder']['codereview-signoff'] = true;
	$wgGroupPermissions['coder']['codereview-associate'] = true;

	$wgGroupPermissions['svnadmins']['repoadmin'] = true; // Default is stewards, but this has nothing to do with them

	$wgCodeReviewENotif = true; // let's experiment with this
	$wgCodeReviewCommentWatcherEmail = 'mediawiki-codereview@lists.wikimedia.org';
	$wgCodeReviewRepoStatsCacheTime = 60 * 60; // 1 hour, default is 6

	$wgCodeReviewMaxDiffPaths = 100;
}

if ( $wmgUseAbuseFilter ) {
	include "$IP/extensions/AbuseFilter/AbuseFilter.php";
	include( "$wmfConfigDir/abusefilter.php" );

	$wgAbuseFilterEmergencyDisableThreshold = $wmgAbuseFilterEmergencyDisableThreshold;
	$wgAbuseFilterEmergencyDisableCount = $wmgAbuseFilterEmergencyDisableCount;
	$wgAbuseFilterEmergencyDisableAge = $wmgAbuseFilterEmergencyDisableAge;
}

if ( $wmgUseCommunityVoice ) {
	include ( "$IP/extensions/ClientSide/ClientSide.php" );
	include ( "$IP/extensions/CommunityVoice/CommunityVoice.php" );
}

if ( $wmgUsePdfHandler ) {
	include ( "$IP/extensions/PdfHandler/PdfHandler.php" );
}

if ( $wmgUseUsabilityInitiative ) {

	$wgNavigableTOCCollapseEnable = true;
	$wgNavigableTOCResizable = true;
	require( "$IP/extensions/Vector/Vector.php" );

	require( "$IP/extensions/WikiEditor/WikiEditor.php" );

	// Uncomment this line for debugging only
	// if ( $wgDBname == 'testwiki' ) { $wgUsabilityInitiativeResourceMode = 'raw'; }
	// Disable experimental things
	$wgWikiEditorFeatures['templateEditor'] =
		$wgWikiEditorFeatures['preview'] =
		$wgWikiEditorFeatures['previewDialog'] =
		$wgWikiEditorFeatures['publish'] =
		$wgWikiEditorFeatures['templates'] =
		$wgVectorFeatures['collapsiblenav'] =
		$wgWikiEditorFeatures['highlight'] = array( 'global' => false, 'user' => true ); // Hidden from prefs view
	$wgVectorFeatures['simplesearch'] = array( 'global' => true, 'user' => false );
	$wgVectorFeatures['expandablesearch'] = array( 'global' => false, 'user' => false );
	$wgVectorUseSimpleSearch = true;
	// Enable EditWarning by default
	$wgDefaultUserOptions['useeditwarning'] = 1;
	$wgHiddenPrefs[] = 'usenavigabletoc';
	$wgHiddenPrefs[] = 'wikieditor-templates';
	$wgHiddenPrefs[] = 'wikieditor-template-editor';
	$wgHiddenPrefs[] = 'wikieditor-preview';
	$wgHiddenPrefs[] = 'wikieditor-previewDialog';
	$wgHiddenPrefs[] = 'wikieditor-publish';
	$wgHiddenPrefs[] = 'wikieditor-highlight';
	if ( $wmgUseCollapsibleNav ) {
		$wgDefaultUserOptions['vector-collapsiblenav'] = 1;
	} else {
		$wgHiddenPrefs[] = 'vector-collapsiblenav';
	}

	if ( $wmgUsabilityEnforce ) {
		$wgEditToolbarGlobalEnable = false;
		$wgDefaultUserOptions['usebetatoolbar'] = 1;
		$wgDefaultUserOptions['usebetatoolbar-cgd'] = 1;
	}

	// For Babaco... these are still experimental, won't be on by default
	$wgNavigableTOCUserEnable = true;
	$wgEditToolbarCGDUserEnable = true;

	if ( $wmgUserDailyContribs ) {
		require "$IP/extensions/UserDailyContribs/UserDailyContribs.php";
	}

	if ( $wmgVectorSectionEditLinks ) {
		$wgVectorFeatures['sectioneditlinks'] = array( 'global' => false, 'user' => true );
		$wgVectorSectionEditLinksBucketTest = true;
		$wgVectorSectionEditLinksLotteryOdds = 1;
		$wgVectorSectionEditLinksExperiment = 2;
	}
}

if ( !$wmgEnableVector ) {
	$wgSkipSkins[] = 'vector';
}

if ( $wmgUseLocalisationUpdate ) {
	require_once( "$IP/extensions/LocalisationUpdate/LocalisationUpdate.php" );
	$wgLocalisationUpdateDirectory = "/var/lib/l10nupdate/cache-$wmfExtendedVersionNumber";
}

if ( $wmgEnableLandingCheck ) {
	require_once(  "$IP/extensions/LandingCheck/LandingCheck.php" );

	$wgPriorityCountries = array(
		'FR', 'DE', 'CH', 'SY', 'IR', 'CU',
		// French Territories per WMFr email 2012-06-13
		'GP', 'MQ', 'GF', 'RE', 'YT', 'PM',
		'NC', 'PF', 'WF', 'BL', 'MF', 'TF',
	);
	$wgLandingCheckPriorityURLBase = "//wikimediafoundation.org/wiki/Special:LandingCheck";
	$wgLandingCheckNormalURLBase = "//donate.wikimedia.org/wiki/Special:LandingCheck";
}

if ( $wmgEnableFundraiserLandingPage ) {
	require_once( "$IP/extensions/FundraiserLandingPage/FundraiserLandingPage.php" );
}

if ( $wmgUseLiquidThreads ) {
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
}

if ( $wmgUseAPIRequestLog ) {
	$wgAPIRequestLog = "udp://locke.wikimedia.org:9000/$wgDBname";
}

if ( $wmgUseLivePreview ) {
	$wgDefaultUserOptions['uselivepreview'] = 1;
}

if ( $wmgUseArticleFeedback ) {
	require_once( "$IP/extensions/ArticleFeedback/ArticleFeedback.php" );
	$wgArticleFeedbackCategories = $wmgArticleFeedbackCategories;
	$wgArticleFeedbackBlacklistCategories = $wmgArticleFeedbackBlacklistCategories;
	$wgArticleFeedbackLotteryOdds = $wmgArticleFeedbackLotteryOdds;
	$wgArticleFeedbackTrackingVersion = 1;

	$wgArticleFeedbackTracking = array(
		'buckets' => array(
			'track' => 100,
			'ignore' => 0,
			// 'track'=>0, 'ignore' => 100
		),
		'version' => 10,
		'expires' => 30,
		'tracked' => false
	);
	$wgArticleFeedbackOptions = array(
		'buckets' => array(
			'show' => 100,
			'hide' => 0,
		),
		'version' => 8,
		'expires' => 30,
		'tracked' => false
	);
	$wgArticleFeedbackDashboard = $wmgArticleFeedbackDashboard;
	$wgArticleFeedbackNamespaces = $wmgArticleFeedbackNamespaces === false ? $wgContentNamespaces : $wmgArticleFeedbackNamespaces;

	if ( $wmgArticleFeedbackRatingTypes !== false ) {
		$wgArticleFeedbackRatingTypes = $wmgArticleFeedbackRatingTypes;
	}
}

if ( $wmgUseArticleFeedbackv5 ) {
	require_once( "$IP/extensions/ArticleFeedbackv5/ArticleFeedbackv5.php" );

	$wgArticleFeedbackv5Cluster = $wmgArticleFeedbackv5Cluster;
	$wgArticleFeedbackv5Categories = $wmgArticleFeedbackv5Categories;
	$wgArticleFeedbackv5BlacklistCategories = $wmgArticleFeedbackv5BlacklistCategories;
	$wgArticleFeedbackv5OversightEmails = $wmgArticleFeedbackv5OversightEmails;
	$wgArticleFeedbackv5OversightEmailHelp = $wmgArticleFeedbackv5OversightEmailHelp;
	$wgArticleFeedbackv5AutoHelp = $wmgArticleFeedbackv5AutoHelp;
	$wgArticleFeedbackv5LearnToEdit = $wmgArticleFeedbackv5LearnToEdit;
	$wgArticleFeedbackv5Namespaces = $wmgArticleFeedbackv5Namespaces;
	$wgArticleFeedbackv5LotteryOdds = $wmgArticleFeedbackv5LotteryOdds;
	$wgArticleFeedbackAutoArchiveEnabled = $wmgArticleFeedbackAutoArchiveEnabled;
	$wgArticleFeedbackAutoArchiveTtl = $wmgArticleFeedbackAutoArchiveTtl;
	$wgArticleFeedbackv5Watchlist = $wmgArticleFeedbackv5Watchlist;
	$wgArticleFeedbackv5ArticlePageLink = $wmgArticleFeedbackv5ArticlePageLink;

	// clear default permissions set in ArticleFeedbackv5.php
	foreach ( $wgGroupPermissions as $group => $permissions ) {
		foreach ( $wmgArticleFeedbackv5Permissions as $permission => $groups ) {
			if ( isset( $wgGroupPermissions[$group][$permission] ) ) {
				unset( $wgGroupPermissions[$group][$permission] );
			}
		}
	}

	// set permissions as defined for selected wiki
	foreach ( $wmgArticleFeedbackv5Permissions as $permission => $groups ) {
		foreach ( (array) $groups as $group ) {
			if ( isset( $wgGroupPermissions[$group] ) ) {
				$wgGroupPermissions[$group][$permission] = true;
			}
		}
	}

	// test groups
	$wgGroupPermissions['afttest'] = array(
		'aft-reader' => true,
		'aft-member' => true,
		'aft-editor' => true,
		'aft-monitor' => true,
		'aft-administrator' => false,
		'aft-oversighter' => false,
	);
	$wgGroupPermissions['afttest-hide'] = array(
		'aft-reader' => true,
		'aft-member' => true,
		'aft-editor' => true,
		'aft-monitor' => true,
		'aft-administrator' => true,
		'aft-oversighter' => true,
	);

	$wgArticleFeedbackv5AbuseFiltering = $wmgArticleFeedbackv5AbuseFiltering;
//	$wgArticleFeedbackv5CTABuckets = $wmgArticleFeedbackv5CTABuckets;
}

$wgDefaultUserOptions['thumbsize'] = $wmgThumbsizeIndex;
$wgDefaultUserOptions['showhiddencats'] = $wmgShowHiddenCats;

if ( $wgDBname == 'strategywiki' ) {
	require_once( "$IP/extensions/StrategyWiki/ActiveStrategy/ActiveStrategy.php" );
}

if ( $wgDBname == 'testwiki' || $wgDBname == 'foundationwiki' ) {
	require_once( "$IP/extensions/CommunityHiring/CommunityHiring.php" );
	$wgCommunityHiringDatabase = 'officewiki';
} elseif ( $wgDBname == 'officewiki' ) {
	require_once( "$IP/extensions/CommunityApplications/CommunityApplications.php" );
}

# # Hack to block emails from some idiot user who likes 'The Joker' --Andrew 2009-05-28
$wgHooks['EmailUser'][] = 'wmfBlockJokerEmails';

function wmfBlockJokerEmails( &$to, &$from, &$subject, &$text ) {
	$blockedAddresses = array( 'the4joker@gmail.com', 'testaccount@werdn.us', 'randomdude5555@gmail.com', 'siyang.li@yahoo.com', 'johnnywiki@gmail.com', 'wikifreedomfighter@googlemail.com' );
	if ( in_array( $from->address, $blockedAddresses ) ) {
		wfDebugLog( 'block_joker_mail', "Blocked attempted email from " . $from->toString() .
					" to " . $to->address . " with subject " . $subject . "\n" );
		return false;
	}
	return true;
}


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

if ( $wmgUseUploadWizard ) {
	require_once( "$IP/extensions/UploadWizard/UploadWizard.php" );
	# Do not change $wgUploadStashScalerBaseUrl to a protocol-relative URL. This is how UploadStash fetches previews from our scaler, behind
	# the scenes, that it then streams to the client securely (much like img_auth.php). -- neilk, 2011-09-12
	$wgUploadStashScalerBaseUrl = "//{$wmfHostnames['upload']}/$site/$lang/thumb/temp";
	$wgUploadWizardConfig = array(
		# 'debug' => true,
		'disableResourceLoader' => false,
		'autoCategory' => 'Uploaded with UploadWizard',
		// If Special:UploadWizard again experiences unexplained slowness loading JavaScript (spinner on intial load spinning forever)
		// set fallbackToAltUploadForm to true.
		'altUploadForm' => 'Special:Upload', # Set by demon, 2011-05-10 per neilk
		'flickrApiUrl' => 'http://api.flickr.com/services/rest/?',
		// Normally we don't include API keys in CommonSettings, but this key
		// isn't private since it's used on the client-side, i.e. anyone can see
		// it in the outgoing AJAX requests to Flickr.
		'flickrApiKey' => 'e9d8174a79c782745289969a45d350e8',
	);

	$wgUploadWizardConfig['enableChunked'] = 'opt-in';

	if ( $wgDBname == 'testwiki' ) {
		$wgUploadWizardConfig['feedbackPage'] = 'Prototype_upload_wizard_feedback';
		$wgUploadWizardConfig['altUploadForm'] = 'Special:Upload';
		$wgUploadWizardConfig["missingCategoriesWikiText"] = '<p><span class="errorbox"><b>Hey, no categories?</b></span></p>';
		unset( $wgUploadWizardConfig['fallbackToAltUploadForm'] );
	} elseif ( $wgDBname == 'commonswiki' ) {
		$wgUploadWizardConfig['feedbackPage'] = 'Commons:Upload_Wizard_feedback'; # Set by neilk, 2011-11-01, per erik
		$wgUploadWizardConfig['altUploadForm'] = 'Commons:Upload';
		$wgUploadWizardConfig["missingCategoriesWikiText"] = "{{subst:unc}}";
		$wgUploadWizardConfig['blacklistIssuesPage'] = 'Commons:Upload_Wizard_blacklist_issues'; # Set by neilk, 2011-11-01, per erik
	} elseif ( $wgDBname == 'test2wiki' ) {
		$wgUploadWizardConfig['feedbackPage'] = 'Wikipedia:Upload_Wizard_feedback'; # Set by neilk, 2011-11-01, per erik
		$wgUploadWizardConfig['altUploadForm'] = 'Wikipedia:Upload';
		$wgUploadWizardConfig["missingCategoriesWikiText"] = "{{subst:unc}}";
		$wgUploadWizardConfig['blacklistIssuesPage'] = 'Wikipedia:Upload_Wizard_blacklist_issues'; # Set by neilk, 2011-11-01, per erik
	}

	// Needed to make UploadWizard work in IE, see bug 39877
	$wgApiFrameOptions = 'SAMEORIGIN';
}

if ( $wmgUseVisualEditor ) {
	require_once( "$IP/extensions/VisualEditor/VisualEditor.php" );
	$wmgVisualEditorParsoidHosts = array(
		'pmtpa' => '10.2.1.29', // parsoidcache.svc.pmtpa.wmnet
		'eqiad' => '10.2.2.29', // parsoidcache.svc.eqiad.wmnet
	);
	$wgVisualEditorParsoidURL = 'http://' . $wmgVisualEditorParsoidHosts[$wmfDatacenter] . ':6081';
	$wgVisualEditorParsoidPrefix = $wmgVisualEditorParsoidPrefix;
	$wgVisualEditorParsoidProblemReportURL = 'http://parsoid.wmflabs.org/_bugs/';
	$wgVisualEditorNamespaces = $wmgVisualEditorNamespaces;

	// VisualEditor namespace
	// This used to be in the VisualEditor extension but was removed there
	// We still need to be careful with double-defining NS_VISUALEDITOR though, for b/c
	if ( $wmgUseVisualEditorNamespace ) {
		if ( !defined( 'NS_VISUALEDITOR' ) ) {
			define( 'NS_VISUALEDITOR', 2500 );
		}
		if ( !defined( 'NS_VISUALEDITOR_TALK' ) ) {
			define( 'NS_VISUALEDITOR_TALK', 2501 );
		}
		$wgExtraNamespaces[NS_VISUALEDITOR] = 'VisualEditor';
		$wgExtraNamespaces[NS_VISUALEDITOR_TALK] = 'VisualEditor_talk';
		$wgVisualEditorNamespaces[] = NS_VISUALEDITOR;
	}
	if ( $wmgVisualEditorHide ) {
		$wgHiddenPrefs[] = 'visualeditor-enable';
	}
	if ( $wmgVisualEditorDefault ) {
		$wgDefaultUserOptions['visualeditor-enable'] = 1;
	}
}

if ( $wmgUseNarayam ) {
	require_once( "$IP/extensions/Narayam/Narayam.php" );
	$wgNarayamEnabledByDefault = $wmgNarayamEnabledByDefault;
	$wgNarayamUseBetaMapping = $wmgNarayamUseBetaMapping;
}

if ( $wmgUseWebFonts ) {
	require_once( "$IP/extensions/WebFonts/WebFonts.php" );
	$wgWebFontsEnabledByDefault = $wmgWebFontsEnabledByDefault;
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

if ( $wmgUseEditPageTracking ) {
	require_once( "$IP/extensions/EditPageTracking/EditPageTracking.php" );
	$wgEditPageTrackingRegistrationCutoff = '20110725221004';
}

if ( $wmgUseGuidedTour ) {
	require_once( "$IP/extensions/GuidedTour/GuidedTour.php" );
}

if ( $wmgUseMarkAsHelpful ) {
	require_once( "$IP/extensions/MarkAsHelpful/MarkAsHelpful.php" );
	$wgMarkAsHelpfulType = array( 'mbresponse' );
}

if ( $wmgUseMoodBar ) {
	require_once( "$IP/extensions/MoodBar/MoodBar.php" );
	$wgMoodBarCutoffTime = $wmgMoodBarCutoffTime;
	$wgMoodBarBlackoutInterval = array( '20120614000000,20120629000000' );
	$wgMoodBarConfig['privacyUrl'] = "//wikimediafoundation.org/wiki/Feedback_policy";
	$wgMoodBarConfig['feedbackDashboardUrl'] = "$wgServer/wiki/Special:FeedbackDashboard";

	$wgMoodBarConfig['infoUrl'] = $wmgMoodBarInfoUrl;
	$wgMoodBarConfig['enableTooltip'] = $wmgMoodBarEnableTooltip;
}
$wgAvailableRights[] = 'moodbar-admin'; // To allow global groups to include this right -AG

# Mobile related configuration

require( getRealmSpecificFilename( "$wmfConfigDir/mobile.php" ) );

if ( $wmgUseSubPageList3 ) {
	include( "$IP/extensions/SubPageList3/SubPageList3.php" );
}

$wgFooterIcons["poweredby"]["mediawiki"]["url"] = "//www.mediawiki.org/";
if ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' ) {
	$wgCookieSecure = true;
	$_SERVER['HTTPS'] = 'on'; // Fake this so MW goes into HTTPS mode
}
$wgVaryOnXFPForAPI = $wgVaryOnXFP = true;

$wgCookieExpiration = 30 * 86400;

if ( $wmgUseMath ) {
	require_once( "$IP/extensions/Math/Math.php" );
	$wgTexvc = "/usr/local/apache/uncommon/$wmfVersionNumber/bin/texvc";
	if ( $wgDBname === 'hewiki' ) {
		$wgDefaultUserOptions['math'] = 0;
	}
	$wgMathFileBackend = $wmgMathFileBackend;
	$wgMathDirectory   = '/mnt/upload7/math'; // just for sanity
	$wgMathPath        = $wmgMathPath;
	$wgUseMathJax      = true;
}

if ( $wmgUseBabel ) {
	require_once( "$IP/extensions/Babel/Babel.php" );
	$wgBabelCategoryNames = $wmgBabelCategoryNames;
	$wgBabelMainCategory = $wmgBabelMainCategory;
	$wgBabelDefaultLevel = $wmgBabelDefaultLevel;
	$wgBabelUseUserLanguage = $wmgBabelUseUserLanguage;
}

if ( $wmgUseTranslate ) {
	require_once( "$IP/extensions/Translate/Translate.php" );

	$wgGroupPermissions['*']['translate'] = true;
	$wgGroupPermissions['translationadmin']['pagetranslation'] = true;
	$wgGroupPermissions['translationadmin']['translate-manage'] = true;
	$wgGroupPermissions['user']['translate-messagereview'] = true;
	$wgGroupPermissions['user']['translate-groupreview'] = true;

	unset( $wgGroupPermissions['translate-proofr'] );
	unset( $wgAddGroups['translate-proofr'] );

	$wgTranslateDocumentationLanguageCode = 'qqq';
	$wgExtraLanguageNames['qqq']       = 'Message documentation'; # No linguistic content. Used for documenting messages

	$wgTranslateTranslationServices = array();
	if ( $wmgUseTranslationMemory ) {
		$wgTranslateTranslationServices['TTMServer'] = array(
			'type' => 'ttmserver',
			'class' => 'SolrTTMServer',
			'cutoff' => 0.60,
			'config' => array(
				'adapteroptions' => array(
					'host' => 'vanadium.eqiad.wmnet',
					'timeout' => 10,
				),
				'adapter' => 'Solarium_Client_Adapter_Curl',
			),
		);
	}

	$wgTranslateWorkflowStates = $wmgTranslateWorkflowStates;
	$wgTranslateRcFilterDefault = $wmgTranslateRcFilterDefault;

	unset( $wgTranslateTasks['export-as-po'] );
	unset( $wgTranslateTasks['export-as-file'] );
	unset( $wgTranslateTasks['optional'] );
	unset( $wgTranslateTasks['suggestions'] );

	$wgTranslateUsePreSaveTransform = true; # bug 37304

	$wgEnablePageTranslation = true;

	$wgTranslateBlacklist = array(
		'*' => array( 'en' => 'English is the source language.', ),
	);

	$wgTranslateEC = array();

	function addSidebarMessageGroup( $id ) {
		$mg = new WikiMessageGroup( $id, 'sidebar-messages' );
		$mg->setLabel( 'Sidebar' );
		$mg->setDescription( 'Messages used in the sidebar of this wiki' );
		return $mg;
	}

	if ( $wgDBname === 'wikimania2013wiki' ) {
		$wgTranslateCC['wiki-sidebar'] = 'addSidebarMessageGroup';
	}

	unset( $wgSpecialPages['FirstSteps'] );
	unset( $wgSpecialPages['ManageMessageGroups'] );
	unset( $wgSpecialPages['ImportTranslations'] );
	unset( $wgSpecialPages['TranslationStats'] );

	$wgAddGroups['bureaucrat'][] = 'translationadmin';
}

if ( $wmgUseTranslationNotifications ) {
	require_once( "$IP/extensions/TranslationNotifications/TranslationNotifications.php" );
	$wgNotificationUsername = 'Translation Notification Bot';
	$wgNotificationUserPassword = $wmgTranslationNotificationUserPassword;

	$wgTranslationNotificationsContactMethods['talkpage-elsewhere'] = true;
}

if ( $wmgUseVipsTest ) {
	include( "$IP/extensions/VipsScaler/VipsScaler.php" );
	include( "$IP/extensions/VipsScaler/VipsTest.php" );
	$wgVipsThumbnailerHost = '10.2.1.21';
}

if ( $wmgUseApiSandbox ) {
	require_once( "$IP/extensions/ApiSandbox/ApiSandbox.php" );
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

if ( $wmgReduceStartupExpiry ) {
	$wgResourceLoaderMaxage['unversioned'] = array( 'server' => 30, 'client' => 30 );
}

if ( $wmgEnablePageTriage ) {
	require_once( "$IP/extensions/PageTriage/PageTriage.php" );
	$wgPageTriageEnableCurationToolbar = $wmgPageTriageEnableCurationToolbar;
}

if ( $wmgEnableInterwiki ) {
	require_once( "$IP/extensions/Interwiki/Interwiki.php" );
	$wgInterwikiViewOnly = true;
}

if ( $wmgEnableRandomRootPage ) {
	require_once( "$IP/extensions/RandomRootPage/Randomrootpage.php" );
}

# Avoid excessive drops in squid hit rates
$wgMaxBacklinksInvalidate = 200000;

#
# If a job runner takes too long to finish a job, assume it died and re-assign the job
$wgJobTypeConf['default']['claimTTL'] = 3600;

#
# Job types to exclude from the default queue processing. Aka the very long
# one.  That will exclude the types from any queries such as nextJobDB.php
# We have to set this for any project cause we usually run PHP script against
# the 'aawiki' database, but might as well run it against another name.

# Timed Media Handler:
$wgJobTypesExcludedFromDefaultQueue[] = 'webVideoTranscode';
$wgJobTypeConf['webVideoTranscode'] = array( 'claimTTL' => 86400 ) + $wgJobTypeConf['default'];

if ( $wmgUseEducationProgram ) {
	require_once( "$IP/extensions/EducationProgram/EducationProgram.php" );
}

if ( $wmgUseWikimediaShopLink ) {
	require_once( "$IP/extensions/WikimediaShopLink/WikimediaShopLink.php" );
	$wgWikimediaShopEnableLink = true;
	$wgWikimediaShopShowLinkCountries = array(
		'US',
		'VI',
		'UM',
		'PR',
		'CA',
		'MX',
		'JP',
	);
	$wgWikimediaShopLinkTarget = '//shop.wikimedia.org';
}

if ( $wmgEnableGeoData ) {
	require_once( "$IP/extensions/GeoData/GeoData.php" );
	$wgGeoDataBackend = 'solr';
	$wgGeoDataSolrMaster = 'solr1001.eqiad.wmnet';
	$wgGeoDataSolrHosts = array(
		'solr1001.eqiad.wmnet' => 75, // master, put less read load on it
		'solr1002.eqiad.wmnet' => 100,
		'solr1003.eqiad.wmnet' => 100,
		//'solr1.pmtpa.wmnet' => 100,
		//'solr2.pmtpa.wmnet' => 100,
		//'solr3.pmtpa.wmnet' => 100,
	);

	# Data collection mode
	if ( !$wmgEnableGeoSearch ) {
		$wgAPIGeneratorModules['geosearch'] = 'ApiQueryDisabled';
		$wgAPIListModules['geosearch'] = 'ApiQueryDisabled';
	}
	$wgGeoDataUpdatesViaJob = $wmgGeoDataUpdatesViaJob;

	# These modules have been intentionally disabled for the first phase of deployment
	if ( $wgDBname !== 'testwiki' ) {
		unset( $wgAPIListModules['geopages'] );
		unset( $wgAPIListModules['geopagesincategory'] );
	}
	$wgMaxCoordinatesPerPage = 2000;
}

if ( $wmgUseEcho ) {
	require_once( "$IP/extensions/Echo/Echo.php" );

	$wgEchoDefaultNotificationTypes = array(
		'all' => array(
			'web' => true,
			'email' => true,
		),
	);

	$wgJobTypeConf['MWEchoNotificationEmailBundleJob'] = array( 'checkDelay' => true ) + $wgJobTypeConf['default'];

	$wgEchoConfig['eventlogging']['Echo']['enabled'] = true;
	$wgEchoConfig['eventlogging']['Echo']['revision'] = 5364744;
	$wgEchoEnableEmailBatch = $wmgEchoEnableEmailBatch;
	$wgEchoEmailFooterAddress = $wmgEchoEmailFooterAddress;
	$wgEchoBundleEmailInterval = $wmgEchoBundleEmailInterval;

	# Outgoing from and reply to address for Echo notifications extension
	$wgNotificationSender = $wmgNotificationSender;
	$wgNotificationSenderName = $wgSitename;
	$wgNotificationReplyName = 'No Reply';

	// Define the cluster database, false to use main database
	$wgEchoCluster = $wmgEchoCluster;
}

if ( $wmgUseThanks ) {
	require_once( "$IP/extensions/Thanks/Thanks.php" );
}

if ( $wmgUseScribunto ) {
	include( "$IP/extensions/CodeEditor/CodeEditor.php" );
	// Don't enable core functionality until it has been reviewed and approved
	$wgCodeEditorEnableCore = false;

	include( "$IP/extensions/Scribunto/Scribunto.php" );
	$wgScribuntoUseGeSHi = true;
	$wgScribuntoUseCodeEditor = true;
	$wgScribuntoDefaultEngine = 'luasandbox';
	$wgScribuntoEngineConf['luasandbox']['cpuLimit'] = 10;
}

if ( $wmgUseSubpageSortkey ) {
	include( "$IP/extensions/SubpageSortkey/SubpageSortkey.php" );
	$wgSubpageSortkeyByNamespace = $wmgSubpageSortkeyByNamespace;
}

if ( $wmgUseMicroDesign || $wmgUseVectorFooterCleanup ) {
	$wgVectorFeatures['footercleanup']['global'] = true;
}

if ( $wmgEnablePostEdit ) {
	require_once( "$IP/extensions/PostEdit/PostEdit.php" );
}

if ( $wmgUseGettingStarted ) {
	require_once( "$IP/extensions/GettingStarted/GettingStarted.php" );
	if ( !empty( $sessionRedis[$wmfDatacenter] ) ) {
		$wgGettingStartedRedis = $sessionRedis[$wmfDatacenter][0];
	}
	$wgGettingStartedExcludedCategories = $wmgGettingStartedExcludedCategories;
}

if ( $wmgUseReplaceText ) {
	require_once( "$IP/extensions/ReplaceText/ReplaceText.php" );
}

if ( $wmgUseGeoCrumbs ) {
	require_once( "$IP/extensions/GeoCrumbs/GeoCrumbs.php" );
}

if ( $wmgUseGeoCrumbs || $wmgUseInsider || $wmgUseRelatedArticles || $wmgUseRelatedSites ) {
	require_once( "$IP/extensions/CustomData/CustomData.php" );
}

if ( $wmgUseCalendar ) {
	require_once( "$IP/extensions/Calendar/Calendar.php" );
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
	$wgDefaultUserOptions['toc-floated'] = true;
}

if ( $wmgUseInsider ) {
	require_once( "$IP/extensions/Insider/Insider.php" );
}

if ( $wmgUseRelatedArticles ) {
	require_once( "$IP/extensions/RelatedArticles/RelatedArticles.php" );
}

if ( $wmgUseRelatedSites ) {
	require_once( "$IP/extensions/RelatedSites/RelatedSites.php" );
	$wgRelatedSitesPrefixes = $wmgRelatedSitesPrefixes;
}

if ( $wmgUseUserMerge ) {
	require_once( "$IP/extensions/UserMerge/UserMerge.php" );
}

if ( $wmgUseEventLogging ) {
	require_once( "$IP/extensions/EventLogging/EventLogging.php" );
	if ( $wgDBname === 'test2wiki' ) {
		// test2wiki has its own Schema: NS.
		$wgEventLoggingDBname = 'test2wiki';
		$wgEventLoggingSchemaIndexUri = 'http://test2.wikipedia.org/w/index.php';
		$wgEventLoggingBaseUri = '//bits.wikimedia.org/dummy.gif';
		$wgEventLoggingFile = "udp://$wmfUdp2logDest/EventLogging-$wgDBname";
	} else {
		// All other wikis reference metawiki.
		$wgEventLoggingBaseUri = '//bits.wikimedia.org/event.gif';
		$wgEventLoggingDBname = 'metawiki';
		$wgEventLoggingFile = 'udp://10.64.21.123:8421/EventLogging';  // vanadium
		$wgEventLoggingSchemaIndexUri = 'http://meta.wikimedia.org/w/index.php';
	}
	if ( $wgEventLoggingDBname === $wgDBname ) {
		// Bug 45031
		$wgExtraNamespaces[470] = 'Schema';
		$wgExtraNamespaces[471] = 'Schema_talk';

		include_once( "$IP/extensions/CodeEditor/CodeEditor.php" );
	}
}

if ( $wmgUseEventLogging && $wmgUseNavigationTiming ) {
	require_once( "$IP/extensions/NavigationTiming/NavigationTiming.php" );
	// Careful! The LOWER the value, the MORE requests will be logged. A
	// sampling factor of 1 means log every request. This should not be
	// lowered without careful coordination with ops.
	$wgNavigationTimingSamplingFactor = 5000;
}

if ( $wmgUseUniversalLanguageSelector ) {
	require_once( "$IP/extensions/UniversalLanguageSelector/UniversalLanguageSelector.php" );
	$wgULSGeoService = false;
	$wgULSEnableAnon = false;
}

if ( $wmgUseWikibaseRepo ) {
	require_once( "$IP/extensions/DataValues/DataValues.php" );
	require_once( "$IP/extensions/Diff/Diff.php" );
	require_once( "$IP/extensions/Wikibase/lib/WikibaseLib.php" );
	require_once( "$IP/extensions/Wikibase/repo/Wikibase.php" );

	$baseNs = 120;

	// Define the namespace indexes
	define( 'WB_NS_PROPERTY', $baseNs );
	define( 'WB_NS_PROPERTY_TALK', $baseNs + 1 );
	define( 'WB_NS_QUERY', $baseNs + 2 );
	define( 'WB_NS_QUERY_TALK', $baseNs + 3 );

	// Define the namespaces
	$wgExtraNamespaces[WB_NS_PROPERTY] = 'Property';
	$wgExtraNamespaces[WB_NS_PROPERTY_TALK] = 'Property_talk';
	$wgExtraNamespaces[WB_NS_QUERY] = 'Query';
	$wgExtraNamespaces[WB_NS_QUERY_TALK] = 'Query_talk';

	$wgWBSettings['dataTypes'] = array(
		'wikibase-item',
		'commonsMedia',
		'string',
	);

	$wgWBSettings['changesAsJson'] = true;

	// Assigning the correct content models to the namespaces
	$wgWBSettings['entityNamespaces'][CONTENT_MODEL_WIKIBASE_ITEM] = NS_MAIN;
	$wgWBSettings['entityNamespaces'][CONTENT_MODEL_WIKIBASE_PROPERTY] = WB_NS_PROPERTY;

	$wgWBSettings['idBlacklist'] = array( 1, 2, 3, 4, 5, 8, 13, 23, 24, 42, 80, 666, 1337, 1868, 1971, 2000, 2001, 2012, 2013 );

	$wgWBSettings['withoutTermSearchKey'] = false;

	$wgWBSettings['clientDbList'] = array_merge(
		array( 'test2wiki' => 'test2wiki' ),
		array_map( 'trim', file( getRealmSpecificFilename( "$IP/../wikipedia.dblist" ) ) )
	);

	$wgWBSettings['localClientDatabases'] = array_combine(
		$wgWBSettings['clientDbList'],
		$wgWBSettings['clientDbList']
	);

	$wgGroupPermissions['*']['property-create'] = false;
}

if ( $wmgUseWikibaseClient ) {
	require_once( "$IP/extensions/DataValues/DataValues.php" );
	require_once( "$IP/extensions/Diff/Diff.php" );
	require_once( "$IP/extensions/Wikibase/lib/WikibaseLib.php" );
	require_once( "$IP/extensions/Wikibase/client/WikibaseClient.php" );

	$wgWBSettings['changesDatabase'] = 'wikidatawiki';
	$wgWBSettings['repoDatabase'] = 'wikidatawiki';

	// to be safe, keeping this here although $wgDBname is default setting
	$wgWBSettings['siteGlobalID'] = $wgDBname;
	$wgWBSettings['repoUrl'] = '//www.wikidata.org';

	$wgWBSettings['repoNamespaces'] = array(
		'wikibase-item' => '',
		'wikibase-property' => 'Property'
	);

	$wgWBSettings['allowDataTransclusion'] = false;
	$wgWBSettings['enableSiteLinkWidget'] = false;

	foreach( $wmgWikibaseClientSettings as $setting => $value ) {
		$wgWBSettings[$setting] = $value;
	}
}

if ( ( $wmgUseTranslate && $wmgUseTranslationMemory ) || $wmgEnableGeoData ) {
	require_once( "$IP/extensions/Solarium/Solarium.php" );
}

if ( $wmgUseTemplateSandbox ) {
	require_once( "$IP/extensions/TemplateSandbox/TemplateSandbox.php" );
	if( $wmgUseScribunto ) {
		$wgTemplateSandboxEditNamespaces[] = NS_MODULE;
	}
}

if ( $wmgUsePageImages ) {
	require_once( "$IP/extensions/PageImages/PageImages.php" );
}

if ( $wmgUseSearchExtraNS ) {
	require_once( "$IP/extensions/SearchExtraNS/SearchExtraNS.php" );
	$wgSearchExtraNamespaces = $wmgSearchExtraNamespaces;
}

if ( $wmgUseGlobalAbuseFilters ) {
	$wgAbuseFilterCentralDB = 'metawiki';
	$wgAbuseFilterIsCentral = ( $wgDBname == $wgAbuseFilterCentralDB );
}

// On Special:Version, link to useful release notes
$wgHooks['SpecialVersionVersionUrl'][] = function( $wgVersion, &$versionUrl ) {
	$matches = array();
	preg_match( "/(\d+\.\d+)wmf(\d+)/", $wgVersion, $matches );
	$versionUrl = "https://www.mediawiki.org/wiki/MediaWiki_{$matches[1]}/wmf{$matches[2]}";
	return false;
};

// additional "language names", adding to Names.php data
$wgExtraLanguageNames = $wmgExtraLanguageNames;

if ( file_exists( "$wmfConfigDir/CommonSettings-$wmfRealm.php" ) ) {
	require( "$wmfConfigDir/CommonSettings-$wmfRealm.php" );
}

#### Per realm extensions

if ( file_exists( "$wmfConfigDir/ext-$wmfRealm.php" ) ) {
	require( "$wmfConfigDir/ext-$wmfRealm.php" );
}

// https://bugzilla.wikimedia.org/show_bug.cgi?id=37211
$wgUseCombinedLoginLink = false;

// Confirmed can do anything autoconfirmed can.
$wgGroupPermissions['confirmed'] = $wgGroupPermissions['autoconfirmed'];
$wgGroupPermissions['confirmed']['skipcaptcha'] = true;

# THIS MUST BE AFTER ALL EXTENSIONS ARE INCLUDED
#
# REALLY ... we're not kidding here ... NO EXTENSIONS AFTER

require( "$wmfConfigDir/ExtensionMessages-$wmfExtendedVersionNumber.php" );

wfProfileOut( "$fname-misc5" );
wfProfileOut( $fname );
