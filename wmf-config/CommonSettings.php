<?php

# WARNING: This file is publically viewable on the web. Do not put private data here.

# Godforsaken hack to work around problems with the Squid caching changes...
#
# To minimize damage on fatal PHP errors, output a default no-cache header
# It will be overridden in cases where we actually specify caching behavior.
#
# More modern PHP versions will send a 500 result code on fatal erorr,
# at least sometimes, but what we're running will send a 200.
header( "Cache-control: no-cache" );

# Try to control stuff:
# define( 'DEBUG_LOG', true );

# useful tokens to search for:

# :SEARCH: - search settings

# -----------------

if ( php_sapi_name() == 'cli' ) {
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

set_include_path( "$IP:$IP/lib:/usr/local/lib/php:/usr/share/php" );

if ( getenv( 'WIKIBACKUP' ) ) {
	// hack while normal ext is not enabled sitewide
	if ( !function_exists( 'utf8_normalize' ) ) {
		dl( 'php_utfnormal.so' );
	}
}

$cluster = 'pmtpa';

# Load site configuration
include( "$IP/includes/DefaultSettings.php" );

# Safari bug with URLs ending in ".gz" and gzip encoding
# http://bugzilla.wikimedia.org/show_bug.cgi?id=4635
$hatesSafari = isset( $_SERVER['PATH_INFO'] ) &&
	strtolower( substr( $_SERVER['PATH_INFO'], -3 ) ) == '.gz';
if ( $hatesSafari ) {
	$wgDisableOutputCompression = true;
}

$DP = $IP;

wfProfileOut( "$fname-init" );
wfProfileIn( "$fname-host" );

$secure = getenv( 'MW_SECURE_HOST' );
# This must be set *after* the DefaultSettings.php inclusion
$wgDBname = $multiVersion->getDatabase();

# wmf-config directory (in common/)
$wmfConfigDir = "$IP/../wmf-config";

wfProfileOut( "$fname-host" );

# Initialise wgConf
wfProfileIn( "$fname-wgConf" );
require( "$wmfConfigDir/wgConf.php" );
function wmfLoadInitialiseSettings( $conf ) {
	global $wmfConfigDir, $wgConf;
	# $wgConf =& $conf; # b/c alias
	require( "$wmfConfigDir/InitialiseSettings.php" );
}
wfProfileOut( "$fname-wgConf" );

wfProfileIn( "$fname-confcache" );

# Is this database listed in $cluster.dblist?
# Note: $wgLocalDatabases set in wgConf.php.
# Note: must be done before calling $multiVersion functions other than getDatabase().
if ( array_search( $wgDBname, $wgLocalDatabases ) === false ) {
	# No? Load missing.php
	if ( $wgCommandLineMode ) {
		print "Database name $wgDBname is not listed in $cluster.dblist\n";
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
	foreach ( array( 'private', 'fishbowl', 'special', 'closed', 'flaggedrevs', 'readonly' ) as $tag ) {
		$dblist = array_map( 'trim', file( "$IP/../$tag.dblist" ) );
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
			'stdlogo' => "//upload.wikimedia.org/$site/$lang/b/bc/Wiki.png" ,
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
require( "$wmfConfigDir/db.php" );
require( "$wmfConfigDir/mc.php" );

ini_set( 'memory_limit', $wmgMemoryLimit );

# Protocol settings for urls
$urlprotocol = "";

setlocale( LC_ALL, 'en_US.UTF-8' );

unset( $wgStylePath );
unset( $wgStyleSheetPath );
# $wgStyleSheetPath = '/w/skins-1.17';
if ( version_compare( $wgVersion, '1.20wmf2', '>=' ) ) {
	// New URL scheme
	if ( $wgDBname == 'testwiki' ) {
		// Make testing skin/JS changes easier
		$wgExtensionAssetsPath = "$urlprotocol//test.wikipedia.org/w/static-$wmfVersionNumber/extensions";
		$wgStyleSheetPath = "$urlprotocol//test.wikipedia.org/w/static-$wmfVersionNumber/skins";
		$wgResourceBasePath = "$urlprotocol//test.wikipedia.org/w/static-$wmfVersionNumber"; // This means resources will be requested from /w/static-VERSION/resources
	} else {
		$wgExtensionAssetsPath = "$urlprotocol//bits.wikimedia.org/static-$wmfVersionNumber/extensions";
		$wgStyleSheetPath = "$urlprotocol//bits.wikimedia.org/static-$wmfVersionNumber/skins";
		$wgResourceBasePath = "$urlprotocol//bits.wikimedia.org/static-$wmfVersionNumber"; // This means resources will be requested from /static-VERSION/resources
	}
} else {
	// Old URL scheme
	if ( $wgDBname == 'testwiki' ) {
		// Make testing skin/JS changes easier
		$wgExtensionAssetsPath = "$urlprotocol//test.wikipedia.org/w/extensions-$wmfVersionNumber";
		$wgStyleSheetPath = "$urlprotocol//test.wikipedia.org/w/skins-$wmfVersionNumber";
		$wgResourceBasePath = "$urlprotocol//test.wikipedia.org/w/resources-$wmfVersionNumber"; // This means resources will be requested from /w/resources-VERSION/resources
	} else {
		$wgExtensionAssetsPath = "$urlprotocol//bits.wikimedia.org/w/extensions-$wmfVersionNumber";
		$wgStyleSheetPath = "$urlprotocol//bits.wikimedia.org/skins-$wmfVersionNumber";
		$wgResourceBasePath = "$urlprotocol//bits.wikimedia.org/resources-$wmfVersionNumber"; // This means resources will be requested from /resources-VERSION/resources
	}
}

$wgStylePath = $wgStyleSheetPath;
$wgArticlePath = "/wiki/$1";

$wgScriptPath  = '/w';
$wgLocalStylePath = "$wgScriptPath/skins-$wmfVersionNumber";
$wgStockPath = '/images';
$wgScript           = $wgScriptPath . '/index.php';
$wgRedirectScript	= $wgScriptPath . '/redirect.php';
$wgInternalServer = $wgCanonicalServer;
if ( $wgDBname != 'testwiki' && isset( $_SERVER['SERVER_NAME'] ) ) {
	// Make testing JS/skin changes easy by not running load.php through bits for testwiki
	$wgLoadScript = "$urlprotocol//bits.wikimedia.org/{$_SERVER['SERVER_NAME']}/load.php";
}

$wgCacheDirectory = '/tmp/mw-cache-' . $wmfVersionNumber;

// Comment out the following lines to get the old-style l10n caching -- TS 2011-02-22
$wgLocalisationCacheConf['storeDirectory'] = "$IP/cache/l10n";
$wgLocalisationCacheConf['manualRecache'] = true;

# Very wrong place for NFS access - brought the site down -- domas - 2009-01-27

# if ( ! is_dir( $wgUploadDirectory ) && !$wgCommandLineMode ) {
#	@mkdir( $wgUploadDirectory, 0777 );
# }

$wgFileStore['deleted']['directory'] = "/mnt/upload6/private/archive/$site/$lang";

# used for mysql/search settings
$tmarray = getdate( time() );
$hour = $tmarray['hours'];
$day = $tmarray['wday'];

$wgEmergencyContact = 'noc@wikipedia.org';

# HTCP multicast squid purging
$wgHTCPMulticastAddress = '239.128.0.112';
$wgHTCPMulticastTTL = 8;

if ( defined( 'DEBUG_LOG' ) ) {
	if ( $wgDBname == 'aawiki' ) {
		$wgMemCachedDebug = true;
		$wgDebugLogFile = 'udp://10.0.5.8:8420/debug15';
		$wgDebugDumpSql = true;
	}
}

$wgDBerrorLog = 'udp://10.0.5.8:8420/dberror';
$wgCheckDBSchema = false;

if ( !isset( $wgLocaltimezone ) ) $wgLocaltimezone = 'UTC';
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
$wgTexvc = "/usr/local/apache/uncommon/$wmfVersionNumber/bin/texvc";
$wgTmpDirectory     = '/tmp';
$wgLegalTitleChars = "+ %!\"$&'()*,\\-.\\/0-9:;=?@A-Z\\\\^_`a-z~\\x80-\\xFF";

$wgSQLMode = null;

# TEMP HACK for bug 31187 --roan
$wgResourceLoaderValidateJS = false;

# EMERGENCY OPTIMIZATION OPTIONS

$wgMiserMode = true;
# wgMiserMode now in the configuration object
# enabled to test DB load balancing -- TS 2004-06-22

# This is overridden in the Lucene section below
$wgDisableTextSearch   = true;
$wgDisableSearchUpdate = true;
$wgDisableCounters     = true;

# $wgSiteSupportPage = "http://wikimediafoundation.org/fundraising";
# $wgDisableUploads = false;

# Is this safe??
# $wgCookieDomain = ".wikipedia.org";
# ini_set('session.name', "{$lang}wikiSession" );
session_name( $lang . 'wikiSession' );
$wgSessionsInMemcached = true;

# Enable subpages in the meta space
$wgNamespacesWithSubpages[4] = 1;
# And namespace 101, which is probably a talk namespace of some description
$wgNamespacesWithSubpages[101] = 1;

/* <important notice>
 *
 * When you add a sitenotice make sure to wrap it in <span dir=ltr></span>,
 * otherwise it'll format badly on RTL wikis -ævar
 */

# Not CLI, see http://bugs.php.net/bug.php?id=47540
if ( php_sapi_name() != 'cli' ) {
	ignore_user_abort( true );
}

$wgUseImageResize               = true;
$wgUseImageMagick               = true;
$wgImageMagickConvertCommand    = '/usr/bin/convert';
$wgSharpenParameter = '0x0.8'; # for IM>6.5, bug 24857

# Strict checking is still off for now, but added
# .txt and .mht to the blacklist.
# -- brion 2004-09-23
# Someone has obviously turned it on, look, the line to disable it is commented out: -- TS
# $wgStrictFileExtensions = false;
$wgFileBlacklist[] = 'txt';
$wgFileBlacklist[] = 'mht';
# $wgFileBlacklist[] = 'pdf';
$wgFileExtensions[] = 'xcf';
$wgFileExtensions[] = 'pdf';
$wgFileExtensions[] = 'mid';
# $wgFileExtensions[] = 'sxw'; # OOo writer       # -- disabling these as obsolete -- brion 2008-02-05
# $wgFileExtensions[] = 'sxi'; # OOo presentation
# $wgFileExtensions[] = 'sxc'; # OOo spreadsheet
# $wgFileExtensions[] = 'sxd'; # OOo drawing
$wgFileExtensions[] = 'ogg'; # Ogg audio & video
$wgFileExtensions[] = 'ogv'; # Ogg audio & video
$wgFileExtensions[] = 'svg';
$wgFileExtensions[] = 'djvu'; # DjVu images/documents

include( $IP . '/extensions/PagedTiffHandler/PagedTiffHandler.php' );
$wgTiffUseTiffinfo = true;

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

# Squid!
$wgUseSquid = true;
$wgUseESI = false;

# As of 2005-04-08, this is empty
# Squids are purged by HTCP multicast, currently relayed to paris via udpmcast on larousse
$wgSquidServers = array();

# Accept XFF from these proxies
$wgSquidServersNoPurge = array(
	'208.80.152.162',	# singer (secure)

	'208.80.152.41',	# sq31
	'208.80.152.42',	# sq32
	'208.80.152.43',	# sq33
	'208.80.152.44',	# sq34
	'208.80.152.45',	# sq35
	'208.80.152.46',	# sq36
	'208.80.152.47',	# sq37
	'208.80.152.48',	# sq38
	'208.80.152.49',	# sq39
	'208.80.152.50',	# sq40
	'208.80.152.51',	# sq41
	'208.80.152.52',	# sq42
	'208.80.152.53',	# sq43
	'208.80.152.54',	# sq44
	'208.80.152.55',	# sq45
	'208.80.152.56',	# sq46
	'208.80.152.57',	# sq47
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
	'208.80.152.69',	# sq59
	'208.80.152.70',	# sq60
	'208.80.152.71',	# sq61
	'208.80.152.72',	# sq62
	'208.80.152.73',	# sq63
	'208.80.152.74',	# sq64
	'208.80.152.75',	# sq65
	'208.80.152.76',	# sq66
	'208.80.152.77',	# sq67
	'208.80.152.78',	# sq68
	'208.80.152.79',	# sq69
	'208.80.152.80',	# sq70
	'208.80.152.81',	# sq71
	'208.80.152.82',	# sq72
	'208.80.152.83',	# sq73
	'208.80.152.84',	# sq74
	'208.80.152.85',	# sq75
	'208.80.152.86',	# sq76
	'208.80.152.87',	# sq77
	'208.80.152.88',	# sq78
	'208.80.152.89',	# sq79
	'208.80.152.90',	# sq80
	'208.80.152.91',	# sq81
	'208.80.152.92',	# sq82
	'208.80.152.93',	# sq83
	'208.80.152.94',	# sq84
	'208.80.152.95',	# sq85
	'208.80.152.96',	# sq86

	'10.64.0.123',	# cp1001
	'10.64.0.124',	# cp1002
	'10.64.0.125',	# cp1003
	'10.64.0.126',	# cp1004
	'10.64.0.127',	# cp1005
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
	'208.80.154.31',	# cp1021
	'208.80.154.32',	# cp1022
	'208.80.154.33',	# cp1023
	'208.80.154.34',	# cp1024
	'208.80.154.35',	# cp1025
	'208.80.154.36',	# cp1026
	'208.80.154.37',	# cp1027
	'208.80.154.38',	# cp1028
	'208.80.154.39',	# cp1029
	'208.80.154.40',	# cp1030
	'208.80.154.41',	# cp1031
	'208.80.154.42',	# cp1032
	'208.80.154.43',	# cp1033
	'208.80.154.44',	# cp1034
	'208.80.154.45',	# cp1035
	'208.80.154.46',	# cp1036
	'208.80.154.47',	# cp1037
	'208.80.154.48',	# cp1038
	'208.80.154.49',	# cp1039
	'208.80.154.50',	# cp1040
	'208.80.154.51',	# cp1041
	'208.80.154.52',	# cp1042
	'208.80.154.53',	# cp1043
	'208.80.154.54',	# cp1044

	'208.80.152.16',	# ssl1
	'208.80.152.17',	# ssl2
	'208.80.152.18',	# ssl3
	'208.80.152.19',	# ssl4

	'208.80.154.133',	# ssl1001
	'208.80.154.134',	# ssl1002
	'208.80.154.9',		# ssl1003
	'208.80.154.8',		# ssl1004

	'91.198.174.11',	# knsq1
	'91.198.174.12',	# knsq2
	'91.198.174.13',	# knsq3
	'91.198.174.14',	# knsq4
	'91.198.174.15',	# knsq5
	'91.198.174.16',	# knsq6
	'91.198.174.17',	# knsq7
	'91.198.174.18',	# knsq8
	'91.198.174.19',	# knsq9
	'91.198.174.20',	# knsq10
	'91.198.174.21',	# knsq11
	'91.198.174.22',	# knsq12
	'91.198.174.23',	# knsq13
	'91.198.174.24',	# knsq14
	'91.198.174.25',	# knsq15
	'91.198.174.26',	# knsq16
	'91.198.174.27',	# knsq17
	'91.198.174.28',	# knsq18
	'91.198.174.29',	# knsq19
	'91.198.174.30',	# knsq20
	'91.198.174.31',	# knsq21
	'91.198.174.32',	# knsq22
	'91.198.174.33',	# knsq23
	'91.198.174.34',	# knsq24
	'91.198.174.35',	# knsq25
	'91.198.174.36',	# knsq26
	'91.198.174.37',	# knsq27
	'91.198.174.38',	# knsq28
	'91.198.174.39',	# knsq29
	'91.198.174.40',	# knsq30

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

	'91.198.174.102',	# ssl3001
	'91.198.174.103',	# ssl3002
	'91.198.174.104', 	# ssl3003
	'91.198.174.105',	# ssl3004
	'91.198.174.106',	# ssl3005
	'91.198.174.107',	# ssl3006
);

# IP addresses that aren't proxies, regardless of what the other sources might say
$wgProxyWhitelist = array(
	'68.124.59.186',
	'202.63.61.242',
	'62.214.230.86',
	'217.94.171.96',
);

# Default:
# $wgSquidMaxage = 2678400;

# Purge site message:
# $wgSquidMaxage = 2678400;
# $wgSquidMaxage = 3600;

# Special:AskSQL
$wgLogQueries = true;
$wgSqlLogFile = $wgUploadDirectory . '/sqllog';

$wgBlockOpenProxies = false;

$wgDebugLogGroups['UploadBlacklist'] = 'udp://10.0.5.8:8420/upload-blacklist';
$wgDebugLogGroups['bug27452'] = 'udp://10.0.5.8:8420/bug27452';
$wgDebugLogGroups['swiftThumb'] = 'udp://10.0.5.8:8420/swift-thumb'; // -aaron 1/30/12
$wgDebugLogGroups['FileOperation'] = 'udp://10.0.5.8:8420/filebackend-ops';

$wgDebugLogGroups['404'] = 'udp://10.0.5.8:8420/four-oh-four';

# Don't allow users to redirect other users' talk pages
# Disabled because interwiki redirects are turned off, so it's not needed
# include( "$IP/Filter-ext_redir.php" );

# # For attaching licensing metadata to pages, and displaying an
# # appropriate copyright notice / icon. GNU Free Documentation
# # License and Creative Commons licenses are supported so far.
$wgEnableCreativeCommonsRdf = false;

if ( $site == 'wikinews' ) {
	# $wgRightsPage = "";# Set to the title of a wiki page that describes your license/copyright
	$wgRightsUrl = "$urlprotocol//creativecommons.org/licenses/by/2.5/";
	$wgRightsText = 'Creative Commons Attribution 2.5';
	$wgRightsIcon = "$urlprotocol//creativecommons.org/images/public/somerights20.png";
}  elseif ( $wgDBname == 'huwikinews' ) {
	$wgRightsUrl = "$urlprotocol//creativecommons.org/licenses/by/3.0/";
	$wgRightsText = 'Creative Commons Attribution 3.0 Unported';
	$wgRightsIcon = "$urlprotocol//creativecommons.org/images/public/somerights20.png";
}  else {
	# Set 2009-06-22 -- BV
	$wgRightsUrl = "$urlprotocol//creativecommons.org/licenses/by-sa/3.0/";
	$wgRightsText = 'Creative Commons Attribution-Share Alike 3.0 Unported';
	$wgRightsIcon = "$urlprotocol//creativecommons.org/images/public/somerights20.png";
}

# FIXME: Turned off tidy for testing. --brion 2004-01-19
# turned it on again to see if this is the culprit --gwicke 2005-01-20
# Anthere had problems with the quarto page on meta which contained broken markup
# Turned off for performance reasons experimentally -- TS 2005-04-20
#   -- Didn't seem to make any difference
# Turned off, to investigate cluster trouble -- AV Mon Nov 14 21:45:49 UTC 2005
# Investigated cluster trouble, tidy seems to have been unrelated to them -- TS
$wgUseTidy = true;

$wgUDPProfilerHost = '10.0.6.30'; # professor
$wgAggregateStatsID = $wgVersion;

// $wgProfiler is set in index.php
if ( isset( $wgProfiler ) ) {
	$wgProfiling = true;
	$wgProfileToDatabase = true;
	$wgProfileSampleRate = 1;
}

wfProfileOut( "$fname-misc1" );
wfProfileIn( "$fname-ext-include1" );

include( $IP . '/extensions/timeline/Timeline.php' );
include( $IP . '/extensions/wikihiero/wikihiero.php' );

if ( $wgDBname == 'testwiki' || $wgDBname == 'mlwiki' ) {
	// FreeSansWMF has been generated from FreeSans and FreeSerif by using this script with fontforge:
	// Open("FreeSans.ttf");
	// MergeFonts("FreeSerif.ttf");
	// SetFontNames("FreeSans-WMF", "FreeSans WMF", "FreeSans WMF Regular", "Regular", "");
	// Generate("FreeSansWMF.ttf", "", 4 );
	$wgTimelineSettings->fontFile = 'FreeSansWMF.ttf';
}

include( $IP . '/extensions/SiteMatrix/SiteMatrix.php' );
// Config for sitematrix
$wgSiteMatrixFile = '/apache/common/langlist';
$wgSiteMatrixClosedSites = "$IP/../closed.dblist";
$wgSiteMatrixPrivateSites = "$IP/../private.dblist";
$wgSiteMatrixFishbowlSites = "$IP/../fishbowl.dblist";

include( $IP . '/extensions/CharInsert/CharInsert.php' );
include( $IP . '/extensions/CheckUser/CheckUser.php' );
$wgCheckUserForceSummary = $wmgCheckUserForceSummary;

include( $IP . '/extensions/ParserFunctions/ParserFunctions.php' );
$wgMaxIfExistCount = 500; // obs
$wgExpensiveParserFunctionLimit = 500;

// <ref> and <references> tags -ævar, 2005-12-23
require( $IP . '/extensions/Cite/Cite.php' );

// psuedobotinterface -ævar, 2005-12-25
// require( $IP.'/extensions/Filepath/SpecialFilepath.php' ); // obsolete 2008-02-12

# Inputbox extension for searching or creating articles
include( $IP . '/extensions/InputBox/InputBox.php' );

include( $IP . '/extensions/ExpandTemplates/ExpandTemplates.php' );
// include( $IP.'/extensions/PicturePopup/PicturePopup.php' ); // extension deleted in december 2007...

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
if ( $wmgUseLST ) {
	include( $IP . '/extensions/LabeledSectionTransclusion/lst.php' );
}

if ( $wmgUseSpamBlacklist ) {
	include( $IP . '/extensions/SpamBlacklist/SpamBlacklist.php' );
}

include( $IP . '/extensions/UploadBlacklist/UploadBlacklist.php' );
# disabled by Domas. reenabling without consulting will end up on wrath and torture
# if ( $wgDBname !== 'kwwiktionary' ) {
	include( $IP . '/extensions/TitleBlacklist/TitleBlacklist.php' );
# }

$wgTitleBlacklistSources = array(
	array(
		'type' => TBLSRC_URL,
		'src'  => "$urlprotocol//meta.wikimedia.org/w/index.php?title=Title_blacklist&action=raw&tb_ver=1",
	),
);

if ( $wmgUseQuiz ) {
   include( "$IP/extensions/Quiz/Quiz.php" );
}

if ( $wmgUseGadgets ) {
	include( "$IP/extensions/Gadgets/Gadgets.php" );
}

include( $IP . '/extensions/OggHandler/OggHandler.php' );
$wgOggThumbLocation = '/usr/bin/oggThumb';
// you can keep the filename the same and use maintenance/purgeList.php
$wgCortadoJarFile = "$urlprotocol//upload.wikimedia.org/jars/cortado.jar";

include( $IP . '/extensions/AssertEdit/AssertEdit.php' );

if ( $wgDBname == 'collabwiki' ) {
	$wgUseTidy = false;
}

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

	$wgUseTidy = false;

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

if ( $wgDBname == 'donatewiki' ) {
	$wgUseTidy = false;
}

if ( $wmgPFEnableStringFunctions ) {
	$wgPFEnableStringFunctions = true;
}

if ( $wgDBname == 'mediawikiwiki' ) {
	include( "$IP/extensions/ExtensionDistributor/ExtensionDistributor.php" );
	$wgExtDistTarDir = '/mnt/upload6/ext-dist';
	$wgExtDistTarUrl = "$urlprotocol//upload.wikimedia.org/ext-dist";
	$wgExtDistWorkingCopy = '/mnt/upload6/private/ExtensionDistributor/mw-snapshot';
	$wgExtDistRemoteClient = '208.80.152.165:8430';

	$wgExtDistBranches = array(
		'trunk' => array(
			'tarLabel' => 'trunk',
			'msgName' => 'extdist-current-version',
			'vcs' => 'svn',
		),
		/**
		 * If you add a branch here, you must also check it out into the working copy directory.
		 * If you add it here without doing that, the extension will break.
		 */
		/*'branches/REL1_19' => array(
			'tarLabel' => 'MW1.19',
			'name' => '1.19.x',
			'vcs' => 'svn',
		),*/
		'branches/REL1_18' => array(
			'tarLabel' => 'MW1.18',
			'name' => '1.18.x',
			'vcs' => 'svn',
		),
		'branches/REL1_17' => array(
			'tarLabel' => 'MW1.17',
			'name' => '1.17.x',
			'vcs' => 'svn',
		),
		'branches/REL1_16' => array(
			'tarLabel' => 'MW1.16',
			'name' => '1.16.x',
			'vcs' => 'svn',
		),
		'branches/REL1_15' => array(
			'tarLabel' => 'MW1.15',
			'name' => '1.15.x',
			'vcs' => 'svn',
		),
		'branches/REL1_14' => array(
			'tarLabel' => 'MW1.14',
			'name' => '1.14.x',
			'vcs' => 'svn',
		),
		'branches/REL1_13' => array(
			'tarLabel' => 'MW1.13',
			'name' => '1.13.x',
			'vcs' => 'svn',
		),
		'branches/REL1_12' => array(
			'tarLabel' => 'MW1.12',
			'name' => '1.12.x',
			'vcs' => 'svn',
		),
		'branches/REL1_11' => array(
			'tarLabel' => 'MW1.11',
			'name' => '1.11.x',
			'vcs' => 'svn',
		),
		/**
		 * If you delete a branch, you must also delete it from the working copy
		 */
		'branches/REL1_10' => array(
			'tarLabel' => 'MW1.10',
			'name' => '1.10.x',
			'vcs' => 'svn',
		),
	);
}

include( $IP . '/extensions/GlobalBlocking/GlobalBlocking.php' );
$wgGlobalBlockingDatabase = 'centralauth';
$wgApplyGlobalBlocks = $wmgApplyGlobalBlocks;

include( $IP . '/extensions/TrustedXFF/TrustedXFF.php' );
$wgTrustedXffFile = "$IP/cache/trusted-xff.cdb";

if ( $wmgContactPageConf ) {
	include( $IP . '/extensions/ContactPage/ContactPage.php' );
	extract( $wmgContactPageConf );
}

include( $IP . '/extensions/SecurePoll/SecurePoll.php' );

$wgHooks['SecurePoll_JumpUrl'][] = 'wmfSecurePollJumpUrl';

function wmfSecurePollJumpUrl( $page, &$url ) {
	global $site, $lang;

	$url = wfAppendQuery( $url, array( 'site' => $site, 'lang' => $lang ) );
	return true;
}

// Email capture
// NOTE: Must be BEFORE ArticleFeedback
if ( $wgUseEmailCapture ) {
	include( "$IP/extensions/EmailCapture/EmailCapture.php" );
	$wgEmailCaptureAutoResponse['from'] = 'improve@wikimedia.org';
}

// PoolCounter
if ( $wmgUsePoolCounter ) {
	include( "$wmfConfigDir/PoolCounterSettings.php" );
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


if ( file_exists( '/usr/bin/ploticus' ) ) {
	$wgTimelineSettings->ploticusCommand = '/usr/bin/ploticus';
} else {
	// Back-compat for Fedora boxes
	$wgTimelineSettings->ploticusCommand = '/usr/local/bin/pl';
}
$wgTimelineSettings->epochTimestamp = '20110206135500'; // fixed font setting
putenv( "GDFONTPATH=/usr/local/apache/common/fonts" );

$wgAllowRealName = false;
$wgSysopRangeBans = true;
$wgSysopUserBans = true;

# Log IP addresses in the recentchanges table
$wgPutIPinRC = true;

$wgUploadSizeWarning = false;

# Default address gets rejected by some mail hosts
$wgPasswordSender = 'wiki@wikimedia.org';

/* NFS backend config */
$wgFileBackends[] = array( // backend config for wiki's local repo
	'class'          => 'FSFileBackend',
	'name'           => 'local-NFS',
	'wikiId'         => "{$site}-{$lang}",
	'lockManager'    => 'nullLockManager', # LocalFile uses FOR UPDATE
	'fileJournal'    => array( 'class' => 'DBFileJournal', 'wiki' => $wgDBname ),
	'fileMode'       => 0644,
	'containerPaths' => array(
		"local-public"  => $wgUploadDirectory,
		"local-thumb"   => str_replace( '/mnt/upload6', '/mnt/thumbs', "$wgUploadDirectory/thumb" ),
		"local-deleted" => "/mnt/upload6/private/archive/$site/$lang",
		"local-temp"    => "$wgUploadDirectory/temp",
	)
);
$wgFileBackends[] = array( // backend config for wiki's access to shared repo
	'class'          => 'FSFileBackend',
	'name'           => 'shared-NFS',
	'wikiId'         => "wikipedia-commons",
	'lockManager'    => 'nullLockManager', # just thumbnails
	'fileJournal'    => array( 'class' => 'DBFileJournal', 'wiki' => 'commonswiki' ),
	'fileMode'       => 0644,
	'containerPaths' => array(
		"shared-public"  => "/mnt/upload6/wikipedia/commons",
		"shared-thumb"   => "/mnt/thumbs/wikipedia/commons/thumb",
		"shared-temp"    => "/mnt/upload6/wikipedia/commons/temp",
	)
);
/* end NFS backend config */

/* OpenStack Swift backend config */
$wmfSwiftBigWikis = array( # DO NOT change without proper migration first
	'commonswiki', 'dewiki', 'enwiki', 'fiwiki', 'frwiki', 'hewiki', 'huwiki', 'idwiki',
	'itwiki', 'jawiki', 'rowiki', 'ruwiki', 'thwiki', 'trwiki', 'ukwiki', 'zhwiki'
);
$wmfSwiftShardLocal = in_array( $wgDBname, $wmfSwiftBigWikis ) ? 2 : 0;
$wmfSwiftShardCommon = in_array( 'commonswiki', $wmfSwiftBigWikis ) ? 2 : 0;
$wgFileBackends[] = array( // backend config for wiki's local repo
	'class'              => 'SwiftFileBackend',
	'name'               => 'local-swift',
	'wikiId'             => "{$site}-{$lang}",
	'lockManager'        => 'nullLockManager', // LocalFile uses FOR UPDATE
	'swiftAuthUrl'       => $wmfSwiftConfig['authUrl'],
	'swiftUser'          => $wmfSwiftConfig['user'],
	'swiftKey'           => $wmfSwiftConfig['key'],
	'shardViaHashLevels' => array(
		'local-public'  => array( 'levels' => $wmfSwiftShardLocal, 'base' => 16, 'repeat' => 1 ),
		'local-thumb'   => array( 'levels' => $wmfSwiftShardLocal, 'base' => 16, 'repeat' => 1 ),
		'local-temp'    => array( 'levels' => $wmfSwiftShardLocal, 'base' => 16, 'repeat' => 1 ),
		'local-deleted' => array( 'levels' => $wmfSwiftShardLocal, 'base' => 36, 'repeat' => 0 )
	)
);
$wgFileBackends[] = array( // backend config for wiki's access to shared repoloo
	'class'              => 'SwiftFileBackend',
	'name'               => 'shared-swift',
	'wikiId'             => "wikipedia-commons",
	'lockManager'        => 'nullLockManager', // just thumbnails
	'swiftAuthUrl'       => $wmfSwiftConfig['authUrl'],
	'swiftUser'          => $wmfSwiftConfig['user'],
	'swiftKey'           => $wmfSwiftConfig['key'],
	'shardViaHashLevels' => array(
		'shared-public'  => array( 'levels' => $wmfSwiftShardCommon, 'base' => 16, 'repeat' => 1 ),
		'shared-thumb'   => array( 'levels' => $wmfSwiftShardCommon, 'base' => 16, 'repeat' => 1 ),
		'shared-temp'    => array( 'levels' => $wmfSwiftShardCommon, 'base' => 16, 'repeat' => 1 ),
		'shared-deleted' => array( 'levels' => $wmfSwiftShardCommon, 'base' => 36, 'repeat' => 0 )
	)
);
/* end Swift backend config */

# More detailed repo/backend config --aaron
if ( 1 || in_array( $wgDBname, array( 'testwiki', 'test2wiki', 'mediawikiwiki', 'commonswiki', 'enwiki' ) ) ) {
	$wgLocalFileRepo = array(
			'class'             => 'LocalRepo',
			'name'              => 'local',
			'backend'           => 'local-NFS',
			'url'               => $wgUploadBaseUrl ? $wgUploadBaseUrl . $wgUploadPath : $wgUploadPath,
			'scriptDirUrl'      => $wgScriptPath,
			'hashLevels'        => 2,
			'thumbScriptUrl'    => $wgThumbnailScriptPath,
			'transformVia404'   => true,
			'initialCapital'    => $wgCapitalLinks,
			'deletedHashLevels' => 3,
	);
	if ( $wgDBname != 'commonswiki' ) {
		$wgForeignFileRepos[] = array(
			'class'            => 'ForeignDBViaLBRepo',
			'name'             => 'shared',
			'backend'          => 'shared-NFS',
			'url'              => "$urlprotocol//upload.wikimedia.org/wikipedia/commons",
			'hashLevels'       => 2,
			'thumbScriptUrl'   => false,
			'transformVia404'  => true,
			'hasSharedCache'   => true,
			'descBaseUrl'      => "$urlprotocol//commons.wikimedia.org/wiki/File:",
			'scriptDirUrl'     => "$urlprotocol//commons.wikimedia.org/w",
			'fetchDescription' => true,
			'wiki'             => 'commonswiki',
			'initialCapital'   => true
		);
		$wgDefaultUserOptions['watchcreations'] = 1;
	}
} else { // old way
	$wgLocalFileRepo = array(
		'class' => 'LocalRepo',
		'name' => 'local',
		'directory' => $wgUploadDirectory,
		'url' => $wgUploadBaseUrl ? $wgUploadBaseUrl . $wgUploadPath : $wgUploadPath,
		'scriptDirUrl' => $wgScriptPath,
		'hashLevels' => 2,
		'thumbScriptUrl' => $wgThumbnailScriptPath,
		'transformVia404' => true,
		'initialCapital' => $wgCapitalLinks,
		'deletedDir' => "/mnt/upload6/private/archive/$site/$lang",
		'deletedHashLevels' => 3,
		'thumbDir' => str_replace( '/mnt/upload6', '/mnt/thumbs', "$wgUploadDirectory/thumb" ),
	);
	if ( $wgDBname != 'commonswiki' ) {
		$wgForeignFileRepos[] = array(
			'class'            => 'ForeignDBViaLBRepo',
			'name'             => 'shared',
			'directory'        => '/mnt/upload6/wikipedia/commons',
			'url'              => "$urlprotocol//upload.wikimedia.org/wikipedia/commons",
			'hashLevels'       => 2,
			'thumbScriptUrl'   => false,
			'transformVia404'  => true,
			'hasSharedCache'   => true,
			'descBaseUrl'      => "$urlprotocol//commons.wikimedia.org/wiki/File:",
			'scriptDirUrl'     => "$urlprotocol//commons.wikimedia.org/w",
			'fetchDescription' => true,
			'wiki'             => 'commonswiki',
			'initialCapital'   => true,
			'thumbDir'         => '/mnt/thumbs/wikipedia/commons/thumb',
		);
		$wgDefaultUserOptions['watchcreations'] = 1;
	}
}

if ( $wgDBname == 'nostalgiawiki' ) {
	# Link back to current version from the archive funhouse
	# wgSiteNotice should use $urlprotocol when wikipedia domain has https enabled
	if ( ( isset( $_REQUEST['title'] ) && ( $title = $_REQUEST['title'] ) )
		|| ( isset( $_SERVER['PATH_INFO'] )  && ( $title = substr( $_SERVER['PATH_INFO'], 1 ) ) ) ) {
		if ( preg_match( '/^(.*)\\/Talk$/', $title, $matches ) ) {
			$title = 'Talk:' . $matches[1];
		}
		$wgSiteNotice = "[http://en.wikipedia.org/wiki/" .
			htmlspecialchars( urlencode( $title ) ) .
		' See the current version of this page on Wikipedia]';
	} else {
		$wgSiteNotice = "[http://en.wikipedia.org/ See current Wikipedia]";
	}
	$wgDefaultUserOptions['highlightbroken'] = 0;
}

$wgUseHashTable = true;

$wgCopyrightIcon = '<a href="' . $urlprotocol . '//wikimediafoundation.org/"><img src="' . $urlprotocol . '//bits.wikimedia.org/images/wikimedia-button.png" width="88" height="31" alt="Wikimedia Foundation"/></a>';

# For Special:Cite, we only want it on wikipedia (but can't count on $site),
# not on these fakers.
$wgLanguageCodeReal = $wgLanguageCode;
# Fake it up
if ( in_array( $wgLanguageCode, array( 'commons', 'meta', 'sources', 'species', 'foundation', 'nostalgia', 'mediawiki' ) ) ) {
	$wgLanguageCode = 'en';
}

# :SEARCH:
$wgUseLuceneSearch = true;

wfProfileOut( "$fname-misc2" );


if ( $wgUseLuceneSearch ) {
	wfProfileIn( "$fname-lucene" );
	include( "$wmfConfigDir/lucene.php" );
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

$wgUseDumbLinkUpdate = false;
$wgAntiLockFlags = ALF_NO_LINK_LOCK | ALF_NO_BLOCK_LOCK;
# had been using
# $wgUseDumbLinkUpdate = true;
# $wgAntiLockFlags = ALF_PRELOAD_LINKS | ALF_PRELOAD_EXISTENCE;

# $wgSquidFastPurge = true;
# Deferred update still broken
$wgMaxSquidPurgeTitles = 500;

$wgInvalidateCacheOnLocalSettingsChange = false;

// General Cache Epoch:
$wgCacheEpoch = '20110101000000';

$wgThumbnailEpoch = '20110101000000';

# OAI repository for update server
include( $IP . '/extensions/OAI/OAIRepo.php' );
$oaiAgentRegex = '/experimental/';
$oaiAuth = true; # broken... squid? php config? wtf
$oaiAudit = true;
$oaiAuditDatabase = 'oai';
$wgDebugLogGroups['oai'] = 'udp://10.0.5.8:8420/oai';
$oaiChunkSize = 40;

$wgEnableUserEmail = true;

# XFF log for vandal tracking
function wfLogXFF() {
	if ( ( @$_SERVER['REQUEST_METHOD'] ) == 'POST' ) {
		$uri = ( $_SERVER['HTTPS'] ? 'https://' : 'http://' ) .
			$_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		wfErrorLog(
			gmdate( 'r' ) . "\t" .
			"$uri\t" .
			"{$_SERVER['HTTP_X_FORWARDED_FOR']}, {$_SERVER['REMOTE_ADDR']}\t" .
			( $_REQUEST['wpSave'] ? 'save' : '' ) . "\n",
			'udp://10.0.5.8:8420/xff' );
	}
}
$wgExtensionFunctions[] = 'wfLogXFF';

// bug 24313, turn off minordefault on enwiki
if ( $wgDBname == 'enwiki' ) {
	$wgHiddenPrefs[] = 'minordefault';
}


wfProfileOut( "$fname-misc3" );
wfProfileIn( "$fname-ext-include2" );

# Experimental category intersection plugin.
# Enabling on an exerimental basis for Wikinews only,
# 2005-03-30 brion
# if( $site == 'wikinews' || $site == 'wikiquote' ||
#    $wgDBname == 'metawiki' ||
#    $wgDBname == 'enwiktionary' || $wgDBname == 'dewiktionary' ||
#    $site == 'wikibooks' ||
#    $wgDBname == 'srwiki' ) {
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

include( "$IP/extensions/TorBlock/TorBlock.php" );
$wgTorLoadNodes = false;
$wgTorIPs = array( '91.198.174.232', '208.80.152.2', '208.80.152.134' );
$wgTorAutoConfirmAge = 90 * 86400;
$wgTorAutoConfirmCount = 100;
$wgTorDisableAdminBlocks = false;
$wgTorTagChanges = false;
$wgGroupPermissions['user']['torunblocked'] = false;

if ( $wmgUseRSSExtension ) {
	include( "$IP/extensions/RSS/RSS.php" );
	# $wgRSSProxy = 'url-downloader.wikimedia.org:8080';
	$wgRSSAllowedFeeds = $wmgRSSAllowedFeeds;
}

wfProfileOut( "$fname-ext-include2" );
wfProfileIn( "$fname-misc4" );

$wgDisabledActions = array( 'credits' );

# Process group overrides

# makesysop permission removed, https://bugzilla.wikimedia.org/show_bug.cgi?id=23081
# $wgGroupPermissions['steward'   ]['makesysop' ] = true;
# $wgGroupPermissions['bureaucrat']['makesysop' ] = true;

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

$wgGroupPermissions['confirmed'] = $wgGroupPermissions['autoconfirmed'];
$wgGroupPermissions['confirmed']['skipcaptcha'] = true;

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

$wgLegacySchemaConversion = true;

# $wgReadOnly = '5 min DB server maintenance...';
# $wgReadOnly = 'Read-only during network issues';


if ( $cluster != 'pmtpa' ) {
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

// Vandal checks
require( "$wmfConfigDir/checkers.php" );

// Experimental ScanSet extension
if ( $wgDBname == 'enwikisource' ) {
	require( $IP . '/extensions/ScanSet/ScanSet.php' );
	$wgScanSetSettings = array(
		'baseDirectory' => '/mnt/upload6/wikipedia/commons/scans',
		'basePath' => "$urlprotocol//upload.wikimedia.org/wikipedia/commons/scans",
	);
}

// 2005-11-27 Special:Cite for Wikipedia -Ævar
// FIXME: This should really be done with $wmgUseSpecialCite in InitialiseSettings.php
if (   ( $site == 'wikipedia' && $wgLanguageCodeReal == $wgLanguageCode )
	 || $site == 'wikisource'
	 || ( $site == 'wikiversity' && $wgLanguageCode == 'en' )
	 || ( $site == 'wikibooks' && $wgLanguageCode == 'it' )
	 || ( $site == 'wiktionary' && $wgLanguageCode == 'it' ) ) {
		 require( $IP . '/extensions/Cite/SpecialCite.php' );
}

// Added throttle for account creations on zh due to mass registration attack 2005-12-16
// might be useful elesewhere. --brion
// disabled temporarily due to tugela bug -- Tim

if ( false /*$lang == 'zh' || $lang == 'en'*/ ) {
	require( "$IP/extensions/UserThrottle/UserThrottle.php" );
	$wgGlobalAccountCreationThrottle = array(
/*
		'min_interval' => 30,   // Hard minimum time between creations (default 5)
		'soft_time'    => 300, // Timeout for rolling count
		'soft_limit'   => 5,  // 5 registrations in five minutes (default 10)
*/
		'min_interval' => 0,   // Hard minimum time between creations (default 5)
		'soft_time'    => 60, // Timeout for rolling count (default 5 minutes)
		'soft_limit'   => 2,  // 2 registrations in one minutes (default 10)
	);
}

// Customize URL handling for secure.wikimedia.org HTTPS logins
if ( $secure ) {
	require( "$wmfConfigDir/secure.php" );
} elseif ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' ) {
	// New HTTPS service on regular URLs
	$wgInternalServer = $wgServer; // Keep this as HTTP for IRC notifications (bug 29925)
	$wgServer = preg_replace( '/^http:/', 'https:', $wgServer );
} else {
	# For non-SSL hosts...
	if ( $wgDBname != 'testwiki' ) {
#		$wgStyleSheetPath = 'http://upload.wikimedia.org/skins';
	}
}

if ( isset( $_REQUEST['captchabypass'] ) && $_REQUEST['captchabypass'] == $wmgCaptchaPassword ) {
	$wmgEnableCaptcha = false;
}

if ( $wmgEnableCaptcha ) {
	require( "$IP/extensions/ConfirmEdit/ConfirmEdit.php" );
	require( "$IP/extensions/ConfirmEdit/FancyCaptcha.php" );
	$wgGroupPermissions['autoconfirmed']['skipcaptcha'] = true;
#	$wgCaptchaTriggers['edit'] = true;
	$wgCaptchaSecret = $wmgCaptchaSecret;
	$wgCaptchaDirectory = '/mnt/upload6/private/captcha';
	$wgCaptchaDirectoryLevels = 3;
	$wgCaptchaStorageClass = 'CaptchaCacheStore';
	$wgCaptchaClass = 'FancyCaptcha';
	$wgCaptchaWhitelist = '#^(https?:)?//([.a-z0-9-]+\\.)?((wikimedia|wikipedia|wiktionary|wikiquote|wikibooks|wikisource|wikispecies|mediawiki|wikimediafoundation|wikinews|wikiversity)\.org|dnsstuff\.com|completewhois\.com|wikimedia\.de|toolserver\.org)(/|$)#i';
	$wgCaptchaWhitelistIP = array( '91.198.174.0/24' ); # toolserver (bug 23982)
	$wgDebugLogGroups["captcha"] = "udp://10.0.5.8:8420/captcha";
	/**
	 * Possibly a broken spambot, or a spambot being tested before a real run.
	 * Hitting lots of wikis in late June 2006
	 */
	$wgCaptchaRegexes[] = '/\b\d{22,28}\b/';
	/**
	 * Somebody's been repeatedly hitting some user talk pages and other arts
	 * with this; February 2007
	 */
	$wgCaptchaRegexes[] = '/\{\{indefblockeduser\}\}/';
	// copyvio bot on be.wikipedia
	if ( $lang == 'be' ) {
		$wgCaptchaRegexes[] = '/\[\[Катэгорыя:Архітэктура\]\]/';
	}
	if ( $wgDBname == 'testwiki' ) {
		$wgCaptchaTriggers['create'] = true;
	}
	// Mystery proxy bot
	// http://en.wikipedia.org/w/index.php?title=Killamanjaro&diff=prev&oldid=168037317
	$wgCaptchaRegexes[] = '/^[a-z0-9]{5,}$/m';

	// 'XRumer' spambot
	// adds non-real links
	// http://meta.wikimedia.org/wiki/User:Cometstyles/XRumer
	// http://meta.wikimedia.org/wiki/User:Jorunn/tracks
	// (added 2008-05-08 -- brion)
	$wgCaptchaRegexes[] = '/<a +href/i';

	// https://bugzilla.wikimedia.org/show_bug.cgi?id=14544
	// 2008-07-05
	$wgCaptchaRegexes[] = '/\b(?i:anontalk\.com)\b/';

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

if ( function_exists( 'dba_open' ) && file_exists( "$IP/cache/interwiki.cdb" ) ) {
	$wgInterwikiCache = "$IP/cache/interwiki.cdb";
}

$wgDebugLogGroups["ExternalStoreDB"] = "udp://10.0.5.8:8420/external";

# if( $wgDBname == 'frwikiquote' ||
if ( $wgDBname == 'sep11wiki' ) {
  $wgSiteNotice = @file_get_contents( $wgReadOnlyFile );
}

# testing enotif via job queue, river 2007-05-10
# turning this off since it's currently so lagged it's horrible -- brion 2009-01-20
# turning back on while investigating other probs
$wgEnotifUseJobQ = true;

// Quick hack for Makesysop vs enwiki
// (Slightly slower hack now [TS])
if ( isset( $sectionLoads ) ) {
	$wgAlternateMaster = array( 'DEFAULT' => $dbHostsByName[key( $sectionLoads['DEFAULT'] )] );
	foreach ( $sectionsByDB as $db => $section ) {
		$wgAlternateMaster[$db] = $dbHostsByName[key( $sectionLoads[$section] )];
	}
}

$wgDebugLogGroups["query"] = "udp://10.0.5.8:8420/botquery";

// Username spoofing / mixed-script / similarity check detection
include $IP . '/extensions/AntiSpoof/AntiSpoof.php';
// $wgAntiSpoofAccounts = false; // log only for now
$wgDebugLogGroups['antispoof'] = 'udp://10.0.5.8:8420/antispoof';

// For transwiki import
ini_set( 'user_agent', 'Wikimedia internal server fetcher (noc@wikimedia.org' );

// CentralAuth
if ( $wmgUseCentralAuth ) {
	include "$IP/extensions/CentralAuth/CentralAuth.php";
	$wgCentralAuthDryRun = false;
	# unset( $wgGroupPermissions['*']['centralauth-merge'] );
	# $wgGroupPermissions['sysop']['centralauth-merge'] = true;
	$wgCentralAuthCookies = true;

	# Broken -- TS
	$wgCentralAuthUDPAddress = $wgRC2UDPAddress;
	$wgCentralAuthNew2UDPPrefix = "#central\t";

	# Determine second-level domain
	if ( preg_match( '/^\w+\.\w+\./', strrev( $wgServer ), $m ) ) {
		$wmgSecondLevelDomain = strrev( $m[0] );
	} else {
		$wmgSecondLevelDomain = false;
	}
	$wgCentralAuthAutoLoginWikis = array(
		'.wikipedia.org' => 'enwiki',
		'meta.wikimedia.org' => 'metawiki',
		'.wiktionary.org' => 'enwiktionary',
		'.wikibooks.org' => 'enwikibooks',
		'.wikiquote.org' => 'enwikiquote',
		'.wikisource.org' => 'enwikisource',
		'commons.wikimedia.org' => 'commonswiki',
		'.wikinews.org' => 'enwikinews',
		'.wikiversity.org' => 'enwikiversity',
		'.mediawiki.org' => 'mediawikiwiki',
		'species.wikimedia.org' => 'specieswiki',
		'incubator.wikimedia.org' => 'incubatorwiki',
	);
	# Don't autologin to self
	if ( isset( $wgCentralAuthAutoLoginWikis[$wmgSecondLevelDomain] ) ) {
		unset( $wgCentralAuthAutoLoginWikis[$wmgSecondLevelDomain] );
		$wgCentralAuthCookieDomain = $wmgSecondLevelDomain;
	} elseif ( $wgDBname == 'commonswiki' ) {
		unset( $wgCentralAuthAutoLoginWikis['commons.wikimedia.org'] );
		$wgCentralAuthCookieDomain = 'commons.wikimedia.org';
	} elseif ( $wgDBname == 'metawiki' ) {
		unset( $wgCentralAuthAutoLoginWikis['meta.wikimedia.org'] );
		$wgCentralAuthCookieDomain = 'meta.wikimedia.org';
	} else {
		# Don't set 2nd-level cookies for *.wikimedia.org, insecure
		$wgCentralAuthCookieDomain = '';
	}
	$wgCentralAuthLoginIcon = $wmgCentralAuthLoginIcon;
	$wgCentralAuthAutoNew = true;

	$wgHooks['CentralAuthWikiList'][] = 'wmfCentralAuthWikiList';
	function wmfCentralAuthWikiList( &$list ) {
		global $wgLocalDatabases, $IP;
		$privateWikis = array_map( 'trim', file( "$IP/../private.dblist" ) );
		$fishbowlWikis = array_map( 'trim', file( "$IP/../fishbowl.dblist" ) );
		$closedWikis = array_map( 'trim', file( "$IP/../closed.dblist" ) );
		$list = array_diff( $wgLocalDatabases,
			$privateWikis, $fishbowlWikis, $closedWikis );
		return true;
	}

	// Let's give it another try
	$wgCentralAuthCreateOnView = true;

	// Enable global sessions for secure.wikimedia.org
	if ( $secure ) {
		$wgCentralAuthCookies = true;
		$wgCentralAuthCookieDomain = 'secure.wikimedia.org';

		$wgCentralAuthCookiePrefix = 'centralauth_';

		// Don't log in to the insecure URLs
		$wgCentralAuthAutoLoginWikis = array();
	}
}

// taking it live 2006-12-15 brion
if ( $wmgUseDismissableSiteNotice ) {
	require( "$IP/extensions/DismissableSiteNotice/DismissableSiteNotice.php" );
}
$wgMajorSiteNoticeID = '2';

$wgHooks['LoginAuthenticateAudit'][] = 'logBadPassword';
$wgDebugLogGroups['badpass'] = 'udp://10.0.5.8:8420/badpass';
$wgDebugLogGroups['ts_badpass'] = 'udp://10.0.5.8:8420/ts_badpass';
$wgHooks['PrefsEmailAudit'][] = 'logPrefsEmail';
$wgHooks['PrefsPasswordAudit'][] = 'logPrefsPassword';

function logBadPassword( $user, $pass, $retval ) {
	$headers = apache_request_headers();

	if ( $user->isAllowed( 'delete' ) && $retval != LoginForm::SUCCESS ) {
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
			$user->getName() . "' from " . wfGetIP() .
			# " - " . serialize( apache_request_headers() )
			" - " . @$headers['X-Forwarded-For'] .
			' - ' . @$headers['User-Agent'] .
			''
			 );
	}

	# Looking for broken bot on toolserver -river 2007-10-13
	if ( $retval != LoginForm::SUCCESS
		&& @strpos( @$headers['X-Forwarded-For'], "91.198.174.201" ) !== false )
	{
		wfDebugLog( 'ts_badpass', "bad login for '" . $user->getName() . "' - "
			. @$headers['User-Agent'] );
	}

	return true;
}

function logPrefsEmail( $user, $old, $new ) {
	if ( $user->isAllowed( 'delete' ) ) {
		$headers = apache_request_headers();
		wfDebugLog( 'badpass', "Email changed in prefs for sysop '" .
			$user->getName() .
			"' from '$old' to '$new'" .
			" - " . wfGetIP() .
			# " - " . serialize( apache_request_headers() )
			" - " . @$headers['X-Forwarded-For'] .
			' - ' . @$headers['User-Agent'] .
			'' );
	}
	return true;
}

function logPrefsPassword( $user, $pass, $status ) {
	if ( $user->isAllowed( 'delete' ) ) {
		$headers = apache_request_headers();
		wfDebugLog( 'badpass', "Password change in prefs for sysop '" .
			$user->getName() .
			"': $status" .
			" - " . wfGetIP() .
			# " - " . serialize( apache_request_headers() )
			" - " . @$headers['X-Forwarded-For'] .
			' - ' . @$headers['User-Agent'] .
			'' );
	}
	return true;
}

if ( file_exists( '/etc/wikimedia-image-scaler' ) ) {
	$wgMaxShellMemory = 300000; // temp was 200M
}
$wgMaxShellTime = 50; // so it times out before PHP and curl and squid
$wgImageMagickTempDir = '/a/magick-tmp';

// Banner notice system
if ( $wmgUseCentralNotice ) {
	include "$IP/extensions/CentralNotice/CentralNotice.php";

	// new settings for secure server support
	if ( $secure ) {
		# Don't load the JS from an insecure source!
		$wgCentralPagePath = 'https://secure.wikimedia.org/wikipedia/meta/w/index.php';
	} elseif ( $wgDBname == 'testwiki' ) {
		$wgCentralPagePath = "$urlprotocol//test.wikipedia.org/w/index.php";
	} else {
		$wgCentralPagePath = "$urlprotocol//meta.wikimedia.org/w/index.php";
	}

	$wgNoticeProject = $wmgNoticeProject;

	if ( $wgDBname == 'testwiki' ) {
		$wgCentralDBname = 'testwiki';
	} else {
		$wgCentralDBname = 'metawiki';
	}

	if( $wgDBname == 'testwiki' || $wgDBname == 'enwiki' ) {
		$wgNoticeBanner_Harvard2011['enable'] = true;
		$wgNoticeBanner_Harvard2011['salt'] = "42";
	}

	$wgCentralNoticeLoader = $wmgCentralNoticeLoader;

	# Wed evening -- all on!
	$wgNoticeTimeout = 3600;
	$wgNoticeServerTimeout = 3600; // to let the counter update
	$wgNoticeCounterSource = $urlprotocol . '//wikimediafoundation.org/wiki/Special:ContributionTotal' .
		'?action=raw' .
		'&start=20101112000000' . // FY 10-11
		'&fudgefactor=660000';   // fudge for pledged donations not in CRM

	if ( $wgDBname == 'metawiki' || $wgDBname == 'testwiki' ) {
		$wgNoticeInfrastructure = true;
	} else {
		$wgNoticeInfrastructure = false;
	}

	// Set fundraising banners to use HTTPS on foundatoin wiki
	$wgNoticeFundraisingUrl = 'https://wikimediafoundation.org/wiki/Special:LandingCheck';

	// No caching for banners on testwiki, so we can develop them there a bit faster - NeilK 2012-01-16
	// Never set this to zero on a highly trafficked wiki, there are server-melting consequences
	if ( $wgDBname == 'testwiki' ) {
		$wgNoticeBannerMaxAge = 0;
	}
}

// Set CentralNotice banner hide cookie; Needs to be enabled for all wikis that display banners ~awjr 2011-11-07
if ( $wmgSetNoticeHideBannersExpiration && $wmgUseCentralNotice ) {
	$wgNoticeHideBannersExpiration = 1327971600; // expire fundraising cookie on January 31, 2012
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
// Enabling just to log to udp://10.0.5.8:8420/spam
include "$IP/extensions/SimpleAntiSpam/SimpleAntiSpam.php";

if ( $wmgUseCollection ) {
	// PediaPress / PDF generation
	include "$IP/extensions/Collection/Collection.php";
	# $wgPDFServer = 'http://bindery.wikimedia.org/cgi-bin/pdfserver.py';
	# $wgCollectionMWServeURL = 'http://bindery.wikimedia.org/cgi-bin/mwlib.cgi';
	# $wgCollectionMWServeURL = 'http://bindery.wikimedia.org:8080/mw-serve/';
	# $wgCollectionMWServeURL = 'http://erzurumi.wikimedia.org:8080/mw-serve/';
	$wgCollectionMWServeURL = "http://pdf1.wikimedia.org:8080/mw-serve/";

	// MediaWiki namespace is not a good default
	$wgCommunityCollectionNamespace = NS_PROJECT;

	// Allow collecting Help pages
	$wgCollectionArticleNamespaces[] = NS_HELP;

	// Sidebar cache doesn't play nice with this
	$wgEnableSidebarCache = false;

	$wgCollectionFormats = array(
		'rl' => 'PDF',
		'odf' => 'OpenDocument Text',
		'zim' => 'openZIM',
	);

	# GFDL is long gone, we use CC-BY-SA 3.0 nowaday. See bug 32513
	//$wgLicenseURL = "http://en.wikipedia.org/w/index.php?title=Wikipedia:Text_of_the_GNU_Free_Documentation_License&action=raw";
	$wgLicenseURL = "http://creativecommons.org/licenses/by-sa/3.0/";

	$wgCollectionPortletForLoggedInUsersOnly = $wmgCollectionPortletForLoggedInUsersOnly;
	$wgCollectionArticleNamespaces = $wmgCollectionArticleNamespaces;

	if ( $wmgCollectionHierarchyDelimiter ) {
		$wgCollectionHierarchyDelimiter = $wmgCollectionHierarchyDelimiter;
	}
}

// Testing internally
include "$wmfConfigDir/secret-projects.php";

function efRaiseThrottle() {
	global $wgAccountCreationThrottle;
	//if ( in_array( wfGetIP(), array( '192.114.7.2', '192.114.7.39' ) ) ) {
	if ( strpos( wfGetIP(), '212.150.215.' ) === 0 ) {
		$wgAccountCreationThrottle = 300;
	}
}
if ( $wgDBname == 'hewiki' && time() >= strtotime( '2012-05-01T10:00 +0:00' ) && time() <= strtotime( '2012-05-01T15:00 +0:00' ) ) {
	$wgExtensionFunctions[] = 'efRaiseThrottle';
}


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
	$wgCodeReviewSharedSecret = $wmgCodeReviewSharedSecret;
	$wgCodeReviewCommentWatcherEmail = 'mediawiki-codereview@lists.wikimedia.org';
	$wgCodeReviewRepoStatsCacheTime = 60 * 60; // 1 hour, default is 6

	$wgCodeReviewMaxDiffPaths = 100;
}

if ( $wmgUseAbuseFilter ) {
	include "$IP/extensions/AbuseFilter/AbuseFilter.php";
	include( "$wmfConfigDir/abusefilter.php" );
}

if ( $wmgUseCommunityVoice == true ) {
	include ( "$IP/extensions/ClientSide/ClientSide.php" );
	include ( "$IP/extensions/CommunityVoice/CommunityVoice.php" );
}

if ( $wmgUsePdfHandler == true ) {
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

	if ( $wmgClickTracking ) {
		require "$IP/extensions/ClickTracking/ClickTracking.php";

		$wgClickTrackThrottle = $wmgClickTrackThrottle;
		$wgClickTrackingLog = 'udp://208.80.152.184:8421/' . $wgDBname; // 208.80.152.184 = emery
		$wgClickTrackingDatabase = false;
		# Disable Special:ClickTracking, not secure yet (as of r59230)
		unset( $wgSpecialPages['ClickTracking'] );
		# Remove the clicktracking permission because people see it in ListGroupRights and wonder what it does
		unset( $wgGroupPermissions['sysop']['clicktrack'] );
	}

	if ( $wmgVectorEditSectionLinks ) {
		$wgVectorFeatures['sectioneditlinks'] = array( 'global' => false, 'user' => true );
		$wgVectorSectionEditLinksBucketTest = true;
		$wgVectorSectionEditLinksLotteryOdds = 1;
		$wgVectorSectionEditLinksExperiment = 2;
	}
}

if ( $wmgClickTracking && $wmgCustomUserSignup ) {
	include "$IP/extensions/CustomUserSignup/CustomUserSignup.php";
}

if ( !$wmgEnableVector ) {
	$wgSkipSkins[] = 'vector';
}

if ( $wmgUseReaderFeedback ) {
	require_once( "$IP/extensions/ReaderFeedback/ReaderFeedback.php" );
	$wgFeedbackStylePath = "$wgExtensionAssetsPath/ReaderFeedback";
	$wgFeedbackNamespaces = $wmgFeedbackNamespaces;
	if ( $wmgFeedbackTags ) {
		$wgFeedbackTags = $wmgFeedbackTags;
	}
	$wgFeedbackSizeThreshhold = $wmgFeedbackSizeThreshhold;
}

if ( $wmgUseLocalisationUpdate ) {
	require_once( "$IP/extensions/LocalisationUpdate/LocalisationUpdate.php" );
	$wgLocalisationUpdateDirectory = dirname( $IP ) . "/php-$wmfExtendedVersionNumber/cache/l10n";
}

if ( $wmgEnableLandingCheck ) {
	require_once(  "$IP/extensions/LandingCheck/LandingCheck.php" );

	$wgPriorityCountries = array( 'FR', 'DE', 'GB', 'CH', 'SY', 'IR', 'CU' );
	// $wgLandingCheckPriorityURLBase = "//wikimediafoundation.org/wiki/Special:LandingCheck";
	// $wgLandingCheckNormalURLBase = "//donate.wikimedia.org/wiki/Special:LandingCheck";
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
	$wgArticleFeedbackv5Categories = $wmgArticleFeedbackv5Categories;
	$wgArticleFeedbackv5BlacklistCategories = $wmgArticleFeedbackv5BlacklistCategories;
	$wgArticleFeedbackv5DashboardCategory = array( 'Article_Feedback_5', 'Article_Feedback_5_Additional_Articles' );
	$wgArticleFeedbackv5OversightEmails = $wmgArticleFeedbackv5OversightEmails;

	$wgArticleFeedbackv5DisplayBuckets['version'] = 1;

	foreach ( array( 'afttest', 'afttest-hide', 'rollbacker', 'reviewer', 'sysop', 'oversight' ) as $group ) {
		$wgGroupPermissions[$group]['aftv5-hide-feedback'] = true;
		$wgGroupPermissions[$group]['aftv5-see-hidden-feedback'] = true;
	}

	foreach ( array( 'afttest', 'oversight' ) as $group ) {
		$wgGroupPermissions[$group]['aftv5-delete-feedback'] = true;
		$wgGroupPermissions[$group]['aftv5-see-deleted-feedback'] = true;
	}

	// user groups allowed to feature
	$wgGroupPermissions['sysop']['aftv5-feature-feedback']      = true;
	$wgGroupPermissions['rollbacker']['aftv5-feature-feedback'] = true;
	$wgGroupPermissions['oversight']['aftv5-feature-feedback']  = true;
	$wgGroupPermissions['autoconfirmed']['aftv5-feature-feedback']  = true;
	$wgGroupPermissions['afttest-hide']['aftv5-feature-feedback']  = true;
	$wgGroupPermissions['afttest']['aftv5-feature-feedback']  = true;

	$wgArticleFeedbackv5SelectedCTA = 1;
	$wgArticleFeedbackv5AbuseFiltering = $wmgArticleFeedbackv5AbuseFiltering;
}

# if ( $wgDBname == 'testwiki' ) {
#   $wgDebugLogFile = '/tmp/debuglog_tmp.txt';
# }


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
$wgDebugLogGroups['block_joker_mail'] = 'udp://10.0.5.8:8420/jokermail';

function wmfBlockJokerEmails( &$to, &$from, &$subject, &$text ) {
	$blockedAddresses = array( 'the4joker@gmail.com', 'testaccount@werdn.us', 'randomdude5555@gmail.com', 'siyang.li@yahoo.com', 'johnnywiki@gmail.com', 'wikifreedomfighter@googlemail.com' );
	if ( in_array( $from->address, $blockedAddresses ) ) {
		wfDebugLog( 'block_joker_mail', "Blocked attempted email from " . $from->toString() .
					" to " . $to->address . " with subject " . $subject . "\n" );
		return false;
	}
	return true;
}

# $wgReadOnly = "Emergency database maintenance, will be back to full shortly.";
# $wgSiteNotice = "<div style='text-align: center; background: #f8f4f0; border: solid 1px #988; font-size: 90%; padding: 4px'>Software updates are being applied to Wikimedia sites; there may be some brief interruption as the servers update.</div>";
# $wgSiteNotice = "<div style='text-align: center; background: #f8f4f0; border: solid 1px #988; font-size: 90%; padding: 4px'>Software updates are being applied to Wikimedia sites; we're shaking out a few remaining issues.</div>";

// ContributionTracking for handling PayPal redirects
if ( $wgUseContributionTracking ) {
	include( "$IP/extensions/ContributionTracking/ContributionTracking.php" );
	include( "$wmfConfigDir/contribution-tracking-setup.php" );
	$wgContributionTrackingPayPalIPN = "https://civicrm.wikimedia.org/fundcore_gateway/paypal";
	$wgContributionTrackingPayPalRecurringIPN = "https://civicrm.wikimedia.org/IPNListener_Recurring.php";
}

if ( $wmgUseUploadWizard ) {
	require_once( "$IP/extensions/UploadWizard/UploadWizard.php" );
	# Do not change $wgUploadStashScalerBaseUrl to a protocol-relative URL. This is how UploadStash fetches previews from our scaler, behind
	# the scenes, that it then streams to the client securely (much like img_auth.php). -- neilk, 2011-09-12
	$wgUploadStashScalerBaseUrl = "$urlprotocol//upload.wikimedia.org/$site/$lang/thumb/temp";
	$wgUploadWizardConfig = array(
		# 'debug' => true,
		'disableResourceLoader' => false,
		'autoCategory' => 'Uploaded with UploadWizard',
		// If Special:UploadWizard again experiences unexplained slowness loading JavaScript (spinner on intial load spinning forever)
		// set fallbackToAltUploadForm to true.
		'altUploadForm' => 'Special:Upload', # Set by demon, 2011-05-10 per neilk

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
}

if ( $wmgUseVisualEditor ) {
	require_once( "$IP/extensions/VisualEditor/VisualEditor.php" );
}

if ( $wmgUseNarayam ) {
	require_once( "$IP/extensions/Narayam/Narayam.php" );
	$wgNarayamEnabledByDefault = $wmgNarayamEnabledByDefault;
	$wgNarayamUseBetaMapping = $wmgNarayamUseBetaMapping;
}

if ( $wmgUseWebFonts ) {
	require_once( "$IP/extensions/WebFonts/WebFonts.php" );
}

if ( $wmgUseGoogleNewsSitemap ) {
	include( "$IP/extensions/GoogleNewsSitemap/GoogleNewsSitemap.php" );
	$wgGNSMfallbackCategory = $wmgGNSMfallbackCategory;
	$wgGNSMcommentNamespace = $wmgGNSMcommentNamespace;
}

if ( $wmgUseCLDR ) {
	require_once( "$IP/extensions/cldr/cldr.php" );
}

$wgObjectCaches['mysql-multiwrite'] = array(
	'class' => 'MultiWriteBagOStuff',
	'caches' => array(
		0 => array(
			'factory' => 'ObjectCache::newMemcached',
		),
		1 => array(
			'class' => 'SqlBagOStuff',
			'server' => array(
				'host' => '10.0.6.50', # db40
				'dbname' => 'parsercache',
				'user' => $wgDBuser,
				'password' => $wgDBpassword,
				'type' => 'mysql',
				'flags' => 0,
			),
			'purgePeriod' => 0,
			'tableName' => 'pc',
			'shards' => 256,
		),
	)
);

# APC not available in CLI mode
if ( php_sapi_name() === 'cli' ) {
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
	$wmincClosedWikis = "$IP/../closed.dblist";
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

if ( $wmgUseMarkAsHelpful ) {
	require_once( "$IP/extensions/MarkAsHelpful/MarkAsHelpful.php" );
	$wgMarkAsHelpfulType = array( 'mbresponse' );
}

if ( $wmgUseMoodBar ) {
	require_once( "$IP/extensions/MoodBar/MoodBar.php" );
	$wgMoodBarCutoffTime = $wmgMoodBarCutoffTime;
	$wgMoodBarConfig['privacyUrl'] = "$urlprotocol//wikimediafoundation.org/wiki/Feedback_policy";
	$wgMoodBarConfig['feedbackDashboardUrl'] = '//en.wikipedia.org/wiki/Special:FeedbackDashboard';

	$wgMoodBarConfig['infoUrl'] = "$urlprotocol$wmgMoodBarInfoUrl";
}
$wgAvailableRights[] = 'moodbar-admin'; // To allow global groups to include this right -AG

if ( $wmgMobileFrontend ) {
	require_once( "$IP/extensions/MobileFrontend/MobileFrontend.php" );
	$wgMFRemotePostFeedback = true;
	$wgMFRemotePostFeedbackUrl = "http://www.mediawiki.org/w/api.php";
	$wgMFRemotePostFeedbackUsername = "mobilefrontend";
	$wgMFRemotePostFeedbackPassword = "mobilefrontend!";
	$wgMFRemotePostFeedbackArticle = "MobileFrontendFeedback";
	$wgMFFeedbackFallbackURL = 'http://en.m.wikipedia.org/wiki/Wikipedia:Contact_us';

	$wgHooks['MobileFrontendOverrideFeedbackLinks'][] = 'MobileFrontendFeedbackConfig';
	function MobileFrontendFeedbackConfig() {
        	global $wgLanguageCode, $wgDBname, $wgMFFeedbackLinks;

        	$infoEmails = array(
                	'af',
                	'ar',
                	'ca',
                	'cs',
                	'da',
                	'de',
                	'el',
                	//'en', Ommitted on purpose
                	'es',
                	'et',
                	'fa',
                	'fi',
			'fr',
			'he',
                	'hi',
                	'hr',
                	'hu',
                	'it',
                	'ja',
                	'ko',
                	'ml',
                	'nds',
                	'nl',
                	'no',
                	'pl',
                	'pt',
                	'ro',
                	'ru',
                	'sk',
                	'sr',
                	'sv',
                	'tr',
                	'vi',
                	'zh',
        	);

        	$lang = ( in_array( $wgLanguageCode, $infoEmails ) ) ? $wgLanguageCode : 'en';
        	$msgOpts = array( 'language' => $lang );
        	/** Get email subjects **/
        	$subjectPreface = "[Mobile feedback] ";
        	$generalSubject = $subjectPreface . wfMsgExt( 'mobile-frontend-leave-feedback-general-link-text', $msgOpts );
        	$articlePersonalSubject = $subjectPreface . wfMsgExt( 'mobile-frontend-leave-feedback-article-personal-link-text', $msgOpts );
        	$articleFactualSubject = $subjectPreface . wfMsgExt( 'mobile-frontend-leave-feedback-article-factual-link-text', $msgOpts );
        	$articleOtherSubject = $subjectPreface . wfMsgExt( 'mobile-frontend-leave-feedback-article-other-link-text', $msgOpts );
		
		/** Build links **/
        	$emailStub = "info-$lang";
		
		// factual error link
		if ( $wgDBname == 'enwiki' ) {
                	$articleFactualLink = "http://en.m.wikipedia.org/wiki/Wikipedia:Contact_us/Article_problem/Factual_error";
        	} else {
                	$articleFactualLink = "mailto:$emailStub@wikimedia.org?subject=$articleFactualSubject";
        	}

		// all other links - only en uses suffix routing
        	$generalLink = "mailto:$emailStub@wikimedia.org?subject=$generalSubject";
		if ( $lang == 'en' ) {
        		$articlePersonalLink = "mailto:$emailStub-q@wikimedia.org?subject=$articlePersonalSubject";
        		$articleOtherLink = "mailto:$emailStub-c@wikimedia.org?subject=$articleOtherSubject";
		} else {
        		$articlePersonalLink = "mailto:$emailStub@wikimedia.org?subject=$articlePersonalSubject";
        		$articleOtherLink = "mailto:$emailStub@wikimedia.org?subject=$articleOtherSubject";
		}	

        	$wgMFFeedbackLinks = array(
                	'General' => $generalLink, // General feedback
                	'ArticlePersonal' => $articlePersonalLink, // Regarding me, a person, or a company I work for
                	'ArticleFactual' => $articleFactualLink, // Regarding a factual error
                	'ArticleOther' => $articleOtherLink, // Regarding another problem
        	);
        	return true;
	}

	if ( $wmgMobileFrontendLogo ) {
		$wgMobileFrontendLogo = $wmgMobileFrontendLogo;
	}
	if ( $wmgMFRemovableClasses ) {
		$wgMFRemovableClasses = $wmgMFRemovableClasses;
	}
	$wgMFCustomLogos = $wmgMFCustomLogos;
	$wgMobileResourceVersion = 1336433685;
}

// If a URL template is set for MobileFrontend, use it.
if ( $wmgMobileUrlTemplate ) {
	$wgMobileUrlTemplate = $wmgMobileUrlTemplate;
}

if ( $wmgZeroRatedMobileAccess ) {
	require_once( "$IP/extensions/ZeroRatedMobileAccess/ZeroRatedMobileAccess.php" );
}

if ( $wmgZeroDisableImages ) {
	if ( isset( $_SERVER['X-Carrier'] ) && $_SERVER['X-Carrier'] == 'DIGI' ) {
		$wgZeroDisableImages = $wmgZeroDisableImages;
	}
}

if ( $wmgUseSubPageList3 ) {
	include( "$IP/extensions/SubPageList3/SubPageList3.php" );
}

$wgFooterIcons["poweredby"]["mediawiki"]["url"] = "//www.mediawiki.org/";
if ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' ) {
	$wgCookieSecure = true;
	$_SERVER['HTTPS'] = 'on'; // Fake this so MW goes into HTTPS mode
}
$wgVaryOnXFPForAPI = $wgVaryOnXFP = true;

if ( $wmgUseMath ) {
	require_once( "$IP/extensions/Math/Math.php" ); // Math move out from core in MW 1.18
	$wgTexvc = "/usr/local/apache/uncommon/$wmfVersionNumber/bin/texvc"; // override default
	if ( $wgDBname === 'hewiki' ) {
		$wgDefaultUserOptions['math'] = 0;
	}
	$wgUseMathJax = true;
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

	$wgTranslateTasks = array(
		'view'                 => 'ViewMessagesTask',
		'untranslated'         => 'ViewUntranslatedTask',
		'acceptqueue'          => 'AcceptQueueMessagesTask',
		'reviewall'            => 'ReviewAllMessagesTask',
		// Makes no sense as import is not enabled
		//'export-as-po'         => 'ExportasPoMessagesTask',
	);

	$wgTranslateWorkflowStates = $wmgTranslateWorkflowStates;

	$wgEnablePageTranslation = true;

	$wgTranslateBlacklist = array(
		'*' => array( 'en' => 'English is the source language.', ),
	);

	$wgTranslateEC = array();

	unset( $wgSpecialPages['FirstSteps'] );
	unset( $wgSpecialPages['ManageMessageGroups'] );
	unset( $wgSpecialPages['ImportTranslations'] );
	unset( $wgSpecialPages['TranslationStats'] );

	$wgAddGroups['bureaucrat'][] = 'translationadmin';
}

if ( $wmgUseTranslationNotifications ) {
	require_once( "$IP/extensions/TranslationNotifications/TranslationNotifications.php" );

	$wgTranslationNotificationsContactMethods['talkpage-elsewhere'] = true;
}

if ( $wmgUseContest ) {
	require_once( "$IP/extensions/Contest/Contest.php" );

	$egContestSettings['mailSender'] = 'codingchallenge@wikimedia.org';
	$egContestSettings['mailSenderName'] = 'Wikimedia Coding Challenge Team';
	$egContestSettings['contestDeletionEnabled'] = false;

	$wgGroupPermissions['sysop'        ]['contestjudge'] = false;
	$wgGroupPermissions['sysop'        ]['contestadmin'] = false;

	$wgGroupPermissions['*'            ]['contestparticipant'] = true;
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
	$wgShortUrlPrefix = $wmgShortUrlPrefix;
}

if ( $wmgUseCongressLookup ) {
	require_once( "$IP/extensions/CongressLookup/CongressLookup.php" );
}

if ( $wmgCongressLookupBlackOnWhite ) {
	$wgCongressLookupBlackOnWhite = true;
}

if ( $wmgUseFeaturedFeeds ) {
	require_once( "$IP/extensions/FeaturedFeeds/FeaturedFeeds.php" );
	require_once( "$IP/extensions/FeaturedFeeds/FeaturedFeedsWMF.php" );
}

// $wgDisplayFeedsInSidebar from FeaturedFeeds defaults to true in the install file
if ( $wmgDisplayFeedsInSidebar === false ) {
	$wgDisplayFeedsInSidebar = false;
}

# Temporary code to purge swift thumbnails --aaron 2/1/2012
require( "$wmfConfigDir/swift.php" );

if ( $wmgReduceStartupExpiry ) {
	$wgResourceLoaderMaxage['unversioned'] = array( 'server' => 30, 'client' => 30 );
}

if ( $wmgEnablePageTriage ) {
	require_once( "$IP/extensions/PageTriage/PageTriage.php" );
}

# THIS MUST BE AFTER ALL EXTENSIONS ARE INCLUDED
#
# REALLY ... we're not kidding here ... NO EXTENSIONS AFTER

require( "$wmfConfigDir/ExtensionMessages-$wmfExtendedVersionNumber.php" );

wfProfileOut( "$fname-misc5" );
wfProfileOut( $fname );
