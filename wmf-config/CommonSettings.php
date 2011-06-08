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
#define( 'DEBUG_LOG', true );


# useful tokens to search for:

# :SEARCH: - search settings
# :SLAVES: - database slaves
# :PROFILING:

# header("HTTP/1.0 500 Internal server error" );
# die("Wikipedia and its sister projects are experiencing technical problems. Please try again later. We apologise for the inconvenience, and thank you for your patience while developers are working to resolve the issue as soon as possible."); # temp to reduce load --brion

# -----------------

if( php_sapi_name() == 'cli' ) {
	# Override for sanity's sake.
	ini_set( 'display_errors', 1 );
	#error_reporting(E_ALL);
}
if( isset( $_SERVER['SERVER_ADDR'] ) ) {
  ini_set( 'error_append_string', ' (' . $_SERVER['SERVER_ADDR'] . ')' );
}

# Protection for unusual entry points
if ( !function_exists( 'wfProfileIn' ) ) {
	require( './includes/ProfilerStub.php' );
}
$fname = 'CommonSettings.php';
wfProfileIn( $fname );
wfProfileIn( "$fname-init" );

#----------------------------------------------------------------------
# Initialisation
/*
if ( defined( 'TESTWIKI' ) ) {
	$IP = "/home/wikipedia/common/php-1.17";
} else {
	$IP = "/usr/local/apache/common/php-1.17";
}
*/
//ini_set( "include_path", "$IP:$IP/includes:$IP/languages:$IP/templates:$IP/lib:$IP/extensions/wikihiero:/usr/local/lib/php:/usr/share/php" );
// Modernized BV -- 2009-7-14
set_include_path( "$IP:$IP/lib:/usr/local/lib/php:/usr/share/php" );

if( getenv( 'WIKIBACKUP' ) ) {
	// hack while normal ext is not enabled sitewide
	if( !function_exists( 'utf8_normalize' ) ) {
		dl('php_utfnormal.so');
	}
}

/*
$cluster = @file_get_contents( '/etc/cluster' );
if ( !$cluster ) {
	# Let's be nice until we're sure things are set up properly
	# die( "Invalid or missing /etc/cluster file on host {$_ENV['HOSTNAME']}\n" );
	$cluster = 'pmtpa';
}
$cluster = trim( $cluster );
 */
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
/*
if ( !$wgCommandLineMode && !defined( 'MW_NO_OUTPUT_BUFFER' ) && !$hatesSafari ) {
	ob_start("ob_gzhandler");
}*/

ini_set( 'memory_limit', 120 * 1024 * 1024 );

$DP = $IP;
                                                                           
wfProfileOut( "$fname-init" );
wfProfileIn( "$fname-host" );

# Determine domain and language
require_once( $IP . '/../wmf-config/MWMultiVersion.php' );
$multiVersion = new MWMultiVersion;
$siteInfo = array();
if ( (@$_SERVER['SCRIPT_NAME']) == '/w/thumb.php' && (@$_SERVER['SERVER_NAME']) == 'upload.wikimedia.org' ) {
	$siteInfo = $multiVersion->getUploadSiteInfo( $_SERVER['PATH_INFO'] );
} else {
	$siteInfo = $multiVersion->getSiteInfo( $_SERVER['SERVER_NAME'], $_SERVER['DOCUMENT_ROOT'] );
}
$site = $siteInfo['site'];
$lang = $siteInfo['lang'];
$wgDBname = $multiVersion->getDatabase( $site, $lang);

# Disabled, no IPv6 support, waste of a regex -- TS 20051207
/*
$ipv6 = false;
if (preg_match('/^[a-z]\.ipv6\./', $server)) {
	$ipv6 = true;
}*/


//changed for hetdeploy testing --pdhanda
$match = array();
if ( preg_match("/^[0-9.]*/", $wgVersion, $match) ) {
	$wgVersionDirectory = $match[0];
} else {
	$wgVersionDirectory = "1.17";
}

# Shutting eswiki down
#if ( $wgDBname == 'eswiki' && php_sapi_name() != 'cli' ) { die(); }

wfProfileOut( "$fname-host" );

# Initialise wgConf
wfProfileIn( "$fname-wgConf" );
require( "$IP/../wmf-config/wgConf.php" );

function wmfLoadInitialiseSettings( $conf ) {
	global $IP;
	$wgConf =& $conf; # b/c alias
	require( "$IP/../wmf-config/InitialiseSettings.php" );
}

wfProfileOut( "$fname-wgConf" );
wfProfileIn( "$fname-confcache" );

# Is this database listed in $cluster.dblist?
if ( array_search( $wgDBname, $wgLocalDatabases ) === false ){
	# No? Load missing.php
	if ( $wgCommandLineMode) {
		print "Database name $wgDBname is not listed in $cluster.dblist\n";
	} else {
		require( "$IP/../wmf-config/missing.php" );
	}
	exit;
}

# Try configuration cache

$filename = "/tmp/mw-cache-$wgVersionDirectory/conf-$wgDBname";
$globals = false;
if ( @filemtime( $filename ) >= filemtime( "$IP/../wmf-config/InitialiseSettings.php" ) ) {
	$cacheRecord = @file_get_contents( $filename );
	if ( $cacheRecord !== false ) {
		$globals = unserialize( $cacheRecord );
	}
}
wfProfileOut( "$fname-confcache" );
if ( !$globals ) {
	wfProfileIn( "$fname-recache-settings" );
	# Get configuration from SiteConfiguration object
	require( "$IP/../wmf-config/InitialiseSettings.php" );
	
	$wikiTags = array();
	foreach ( array( 'private', 'fishbowl', 'special', 'closed', 'flaggedrevs', 'readonly', 'switchover-jun30' ) as $tag ) {
		$dblist = array_map( 'trim', file( "$IP/../$tag.dblist" ) );
		if ( in_array( $wgDBname, $dblist ) ) {
			$wikiTags[] = $tag;
		}
	}

	$globals = $wgConf->getAll( $wgDBname, $dbSuffix, 
		array(
			'lang'    => $lang,
			'docRoot' => $_SERVER['DOCUMENT_ROOT'],
			'site'    => $site,
			'stdlogo' => "http://upload.wikimedia.org/$site/$lang/b/bc/Wiki.png" 
		), $wikiTags );
	
	# Save cache
	$oldUmask = umask( 0 );
	@mkdir( '/tmp/mw-cache-' . $wgVersionDirectory, 0777 );
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


#-------------------------------------------------------------------------
# Settings common to all wikis

# Private settings such as passwords, that shouldn't be published
# Needs to be before db.php
require( "$IP/../wmf-config/PrivateSettings.php" );

# Cluster-dependent files for database and memcached
require( "$IP/../wmf-config/db.php" );
require("$IP/../wmf-config/mc.php");


setlocale( LC_ALL, 'en_US.UTF-8' );

unset( $wgStylePath );
unset( $wgStyleSheetPath );
#$wgStyleSheetPath = '/w/skins-1.17';
if ( $wgDBname == 'testwiki' ) {
	// Make testing skin/JS changes easier
	$wgExtensionAssetsPath = 'http://test.wikipedia.org/w/extensions-' . $wgVersionDirectory;
	$wgStyleSheetPath = 'http://test.wikipedia.org/w/skins-' . $wgVersionDirectory;

} else {
	$wgExtensionAssetsPath = 'http://bits.wikimedia.org/w/extensions-' . $wgVersionDirectory;
	$wgStyleSheetPath = 'http://bits.wikimedia.org/skins-' . $wgVersionDirectory;
}
$wgStylePath = $wgStyleSheetPath;
$wgArticlePath = "/wiki/$1";

$wgScriptPath  = '/w';
$wgLocalStylePath = "$wgScriptPath/skins-$wgVersionDirectory";
$wgStockPath = '/images';
$wgScript           = $wgScriptPath.'/index.php';
$wgRedirectScript	= $wgScriptPath.'/redirect.php';
if ( $wgDBname != 'testwiki' ) {
	// Make testing JS/skin changes easy by not running load.php through bits for testwiki
	$wgLoadScript = "http://bits.wikimedia.org/{$_SERVER['SERVER_NAME']}/load.php";
}

# Very wrong place for NFS access - brought the site down -- domas - 2009-01-27 

#if ( ! is_dir( $wgUploadDirectory ) && !$wgCommandLineMode ) { 
#	@mkdir( $wgUploadDirectory, 0777 ); 
#}

$wgFileStore['deleted']['directory'] = "/mnt/upload6/private/archive/$site/$lang";

if ( $cluster == 'yaseo' ) {
	$wgSharedThumbnailScriptPath = 'http://commons.wikimedia.org/w/thumb.php';
	$wgUploadPath = '/upload';
	$wgUploadDirectory = "/mnt/upload/$site/$lang";
	$wgFileStore['deleted']['directory'] = "/mnt/upload/private/archive/$site/$lang";
}

# used for mysql/search settings
$tmarray = getdate(time());
$hour = $tmarray['hours'];
$day = $tmarray['wday'];

$wgEmergencyContact = 'noc@wikipedia.org';

# HTCP multicast squid purging
$wgHTCPMulticastAddress = '239.128.0.112';
$wgHTCPMulticastTTL = 2;

if( defined( 'DEBUG_LOG' ) ) {
	if ( $wgDBname == 'aawiki' ) {
		$wgMemCachedDebug=true;
		$wgDebugLogFile = 'udp://10.0.5.8:8420/debug15';
		$wgDebugDumpSql = true;
	}
}

$wgDBerrorLog = 'udp://10.0.5.8:8420/dberror';
$wgCheckDBSchema = false;

if(!isset($wgLocaltimezone)) $wgLocaltimezone = 'UTC';
# Ugly hack warning! This needs smoothing out.
if($wgLocaltimezone) {
	$oldtz = getenv('TZ');
	putenv("TZ=$wgLocaltimezone");
	$wgLocalTZoffset = date('Z') / 60;
	putenv("TZ=$oldtz");
}


$wgShowIPinHeader = false;
$wgUseGzip = true;
$wgRCMaxAge = 30*86400;

$wgUseTeX = true;
$wgTexvc = "/usr/local/bin/texvc";
$wgTmpDirectory     = '/tmp';
$wgLegalTitleChars = "+ %!\"$&'()*,\\-.\\/0-9:;=?@A-Z\\\\^_`a-z~\\x80-\\xFF";

$wgSQLMode = null;

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
#$wgCookieDomain = ".wikipedia.org";
#ini_set('session.name', "{$lang}wikiSession" );
session_name( $lang.'wikiSession' );
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
# Site notices
#$wgSiteNotice = "All Wikimedia projects will be down for approximately 30 minutes for a server configuration change around 18:00 UTC on 11 June. We apologise for the inconvenience.";
#$wgSiteNotice = "Servers will be rebooted for operating system upgrades circa 12:00 UTC.";
#$wgSiteNotice = "Servers are being rebooted for operating system upgrades. May be offline for a bit.";
#$wgSiteNotice = "The databases are being moved to a less crash-prone server until the new machine is debugged. We'll be offline for probably an hour or two to get a consistent and complete backup transferred.";
#$wgSiteNotice = "Completing transfer of database to less crash-prone server; will be offline for a few minutes.";
#$wgSiteNotice = "Database should be working, but some files are still being transferred.";
#$wgSiteNotice = "The wiki will be locked starting in a few minutes"; # to 
#move the databases back to a faster machine. Downtime may be a few
#hours, but things will be much faster after that!";
#$wgSiteNotice = "The database is offline for a while from 05:00 UTC
#to make a clean copy for transfer to our new servers. Sorry for the inconvenience;
#we should be back online around 06:15 UTC.";
#$wgSiteNotice = "The database is read-only and using an older copy while some serious problems are fixed, sorry for the inconvenience this may cause.";
#$wgSiteNotice = "<div name=\"fundraising\" id=\"fundraising\" align=\"center\">'''Wikimedia Fundraising Drive 2004'''. Help us raise $50,000. See [http://wikimediafoundation.org/wiki/fundraising our fundraising page] for details.<br />
#[http://meta.wikimedia.org/wiki/Fundraising_site_notice Information on this message] - [{{SERVER}}{{localurl:MediaWiki:Sitenotice|action=edit}} Edit this message]</div>";
#$wgSiteNotice = "-";
#$wgSiteNotice = "The site will be read only for 20 minutes for a server restart starting at the next hour time - 11:00 UTC";
#$wgSiteNotice = "The site will be read-only in 5-15 minutes for an undetermined period of time while database recovery is performed";
#$wgSiteNotice = "The site will be unavailable for about 30 minutes between 07:00 and 09:00 UTC to switch master database server.";
#$wgSiteNotice = '<span dir="ltr">There is some kind of problem with the image server currently. The issue is being investigated.</span>'; # hashar
#$wgSiteNotice = "There is a problem with the image storage. We are in the process of fixing it.";
/* </important notice> */

# Not CLI, see http://bugs.php.net/bug.php?id=47540
if ( php_sapi_name() != 'cli' ) {
	ignore_user_abort(true);
}

$wgUseImageResize               = true;
$wgUseImageMagick               = true;
$wgImageMagickConvertCommand    = '/usr/bin/convert';
$wgSharpenParameter = '0x0.8'; # for IM>6.5, bug 24857

# Strict checking is still off for now, but added
# .txt and .mht to the blacklist.
# -- brion 2004-09-23
# Someone has obviously turned it on, look, the line to disable it is commented out: -- TS
#$wgStrictFileExtensions = false;
$wgFileBlacklist[] = 'txt';
$wgFileBlacklist[] = 'mht';
# $wgFileBlacklist[] = 'pdf';
$wgFileExtensions[] = 'xcf';
# Disabling this for now -- brion - 2007-08-21
#if( $wgDBname != 'commonswiki' ) {
#    $wgFileExtensions[] = 'xls';
#}
$wgFileExtensions[] = 'pdf';
$wgFileExtensions[] = 'mid';
#$wgFileExtensions[] = 'sxw'; # OOo writer       # -- disabling these as obsolete -- brion 2008-02-05
#$wgFileExtensions[] = 'sxi'; # OOo presentation
#$wgFileExtensions[] = 'sxc'; # OOo spreadsheet
#$wgFileExtensions[] = 'sxd'; # OOo drawing
$wgFileExtensions[] = 'ogg'; # Ogg audio & video
$wgFileExtensions[] = 'ogv'; # Ogg audio & video
$wgFileExtensions[] = 'svg';
$wgFileExtensions[] = 'djvu'; # DjVu images/documents

if ( /* use PagedTiffHandler */ true ) { 
	include( $IP.'/extensions/PagedTiffHandler/PagedTiffHandler.php' );
	$wgTiffUseTiffinfo = true;
} else {
	$wgFileExtensions[] = 'tif';
	$wgFileExtensions[] = 'tiff';
}

if( $wgDBname == 'foundationwiki' ) { # per cary on 2010-05-11
   $wgFileExtensions[] = 'otf';
   $wgFileExtensions[] = 'ai';
}

if ( $wgDBname == 'outreachwiki' ) { # Per Frank, bug 25106
	$wgFileExtensions[] = 'sla';
}

if( $wmgPrivateWikiUploads ) {
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

	# Wikimedia servers
	# ------------------------------------------------
	
	# pmtpa external
	# Note that all pmtpa squids must have an external address here, for the 
	# benefit of the yaseo apaches.

	'208.80.152.162',	# singer (secure)

	'208.80.152.119',	# eiximenis (testing)
	
	'208.80.152.11',	# sq1
	'208.80.152.12',	# sq2
	'208.80.152.13',	# sq3
	'208.80.152.14',	# sq4
	'208.80.152.15',	# sq5
	'208.80.152.16',	# sq6
	'208.80.152.17',	# sq7
	'208.80.152.18',	# sq8
	'208.80.152.19',	# sq9
	'208.80.152.20',	# sq10
	'208.80.152.21',	# sq11
	'208.80.152.22',	# sq12
	'208.80.152.23',	# sq13
	'208.80.152.24',	# sq14
	'208.80.152.25',	# sq15
	'208.80.152.26',	# sq16
	'208.80.152.27',	# sq17
	'208.80.152.28',	# sq18
	'208.80.152.29',	# sq19
	'208.80.152.30',	# sq20
	'208.80.152.31',	# sq21
	'208.80.152.32',	# sq22
	'208.80.152.33',	# sq23
	'208.80.152.34',	# sq24
	'208.80.152.35',	# sq25
	'208.80.152.36',	# sq26
	'208.80.152.37',	# sq27
	'208.80.152.38',	# sq28
	'208.80.152.39',	# sq29
	'208.80.152.40',	# sq30
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

);

# IP addresses that aren't proxies, regardless of what the other sources might say
$wgProxyWhitelist = array(
	'68.124.59.186',
	'202.63.61.242',
	'62.214.230.86',
	'217.94.171.96',

	# True Internet
	# (Thai ISP, used by Waerth)
#	'203.144.143.2',
#	'203.144.143.3',
#	'203.144.143.6',

);

# Default:
#$wgSquidMaxage = 2678400;

# Purge site message:
#$wgSquidMaxage = 2678400;
#$wgSquidMaxage = 3600;

# Special:AskSQL
$wgLogQueries = true;
$wgSqlLogFile = $wgUploadDirectory.'/sqllog';


$wgBlockOpenProxies = false;

$wgDebugLogGroups['UploadBlacklist'] = 'udp://10.0.5.8:8420/upload-blacklist';

$wgDebugLogGroups['404'] = 'udp://10.0.5.8:8420/four-oh-four';


# Don't allow users to redirect other users' talk pages
# Disabled because interwiki redirects are turned off, so it's not needed
#include( "$IP/Filter-ext_redir.php" );

#include("outage.php");
#declareOutage( "2004-05-12", "02:00", "03:00" );
/*
$d = getdate();
$hour = $d['hours'];
if ( $lang == "en" && $hour >= 13 && $hour < 18 ) {
	$wgUseWatchlistCache = true;
	$wgWLCacheTimeout = 3600;
}
*/
## For attaching licensing metadata to pages, and displaying an
## appropriate copyright notice / icon. GNU Free Documentation
## License and Creative Commons licenses are supported so far.
$wgEnableCreativeCommonsRdf = false;

if( $site == 'wikinews' ) {
	#$wgRightsPage = "";# Set to the title of a wiki page that describes your license/copyright
	$wgRightsUrl = 'http://creativecommons.org/licenses/by/2.5/';
	$wgRightsText = 'Creative Commons Attribution 2.5';
	$wgRightsIcon = 'http://creativecommons.org/images/public/somerights20.png';
}  elseif ( $wgDBname == 'huwikinews') {
	$wgRightsUrl = 'http://creativecommons.org/licenses/by/3.0/';
	$wgRightsText = 'Creative Commons Attribution 3.0 Unported';
	$wgRightsIcon = 'http://creativecommons.org/images/public/somerights20.png';
}  else {
	# Set 2009-06-22 -- BV
	$wgRightsUrl = 'http://creativecommons.org/licenses/by-sa/3.0/';
	$wgRightsText = 'Creative Commons Attribution-Share Alike 3.0 Unported';
	$wgRightsIcon = 'http://creativecommons.org/images/public/somerights20.png';
}

# FIXME: Turned off tidy for testing. --brion 2004-01-19
# turned it on again to see if this is the culprit --gwicke 2005-01-20
# Anthere had problems with the quarto page on meta which contained broken markup
# Turned off for performance reasons experimentally -- TS 2005-04-20
#   -- Didn't seem to make any difference
# Turned off, to investigate cluster trouble -- AV Mon Nov 14 21:45:49 UTC 2005
# Investigated cluster trouble, tidy seems to have been unrelated to them -- TS
$wgUseTidy = true;

# Profiling, typically at 100 rate
#Caution: moderate profiling rates can write 1.5GB per day each to the slow query and binary logs.
# If the master is low on disk space, you'll need to empty the slow query log
# PLEASE TELL JAMES OR WHOEVER IS LOOKING AFTER THE DATABASE SERVERS IF YOU TURN ON PROFILING
# :PROFILING:

/*
//if ( isset( $_REQUEST['wpSave'] ) ) {
	if ( $wgDBname == 'enwiki' || $wgDBname == 'dewiki' ) {
		$wgProfiling = true;
		$wgProfileToDatabase = true;
		$wgProfileSampleRate = 500;
	}
	if ( $wgDBname == 'frwiki' ) {
		$wgProfiling = true;
		$wgProfileToDatabase = true;
		$wgProfileSampleRate = 100;
	}
	if ( $wgDBname == 'testwiki' ) {
		$wgProfiling = true;
		$wgProfileToDatabase = true;
		$wgProfileSampleRate = 1;
	}
//	$wgProfileSampleRate /= 50;
//}
*/
/*
if ($wgDBname == 'enwiki' || $wgDBname == 'commonswiki' ) {
    $wgProfiling = true;
    $wgProfilerType = 'SimpleUDP';
    $wgProfileSampleRate = 1;
}*/
$wgUDPProfilerHost = '208.80.152.161';  # spence
$wgAggregateStatsID = 'all';

// $wgProfiler is set in index.php
if ( isset( $wgProfiler ) ) {
	$wgProfiling = true;
	$wgProfileToDatabase = true;
	$wgProfileSampleRate = 1;
}


wfProfileOut( "$fname-misc1" );
wfProfileIn( "$fname-ext-include1" );

include($IP.'/extensions/timeline/Timeline.php');
include($IP.'/extensions/wikihiero/wikihiero.php');

// if ( $wgDBname == 'testwiki' ) {
	// $wgTimelineSettings->fontFile = 'unifont-5.1.20080907.ttf';
// }
if ( $wgDBname == 'testwiki' || $wgDBname == 'mlwiki' ) {
	// FreeSansWMF has been generated from FreeSans and FreeSerif by using this script with fontforge:
	// Open("FreeSans.ttf");
	// MergeFonts("FreeSerif.ttf");
	// SetFontNames("FreeSans-WMF", "FreeSans WMF", "FreeSans WMF Regular", "Regular", "");
	// Generate("FreeSansWMF.ttf", "", 4 );
	$wgTimelineSettings->fontFile = 'FreeSansWMF.ttf';
}


include( $IP.'/extensions/SiteMatrix/SiteMatrix.php' );
// Config for sitematrix
$wgSiteMatrixFile = '/apache/common/langlist';
$wgSiteMatrixClosedSites = "$IP/../closed.dblist";
$wgSiteMatrixPrivateSites = "$IP/../private.dblist";
$wgSiteMatrixFishbowlSites = "$IP/../fishbowl.dblist";

include( $IP.'/extensions/CharInsert/CharInsert.php' );
include( $IP.'/extensions/CheckUser/CheckUser.php' );
include( $IP.'/extensions/ParserFunctions/ParserFunctions.php' );
$wgMaxIfExistCount = 500; // obs
$wgExpensiveParserFunctionLimit = 500;

// <ref> and <references> tags -ævar, 2005-12-23
require( $IP.'/extensions/Cite/Cite.php' );

// psuedobotinterface -ævar, 2005-12-25
//require( $IP.'/extensions/Filepath/SpecialFilepath.php' ); // obsolete 2008-02-12

# Inputbox extension for searching or creating articles
include( $IP.'/extensions/InputBox/InputBox.php' );

include( $IP.'/extensions/ExpandTemplates/ExpandTemplates.php' );
// include( $IP.'/extensions/PicturePopup/PicturePopup.php' ); // extension deleted in december 2007...

include( $IP.'/extensions/ImageMap/ImageMap.php' );
include( $IP.'/extensions/SyntaxHighlight_GeSHi/SyntaxHighlight_GeSHi.php' );

// Experimental side-by-side comparison extension for wikisource. enabled brion 2006-01-13
if( $wmgUseDoubleWiki ) {
	    include( $IP.'/extensions/DoubleWiki/DoubleWiki.php' );
}

# Poem
include( $IP.'/extensions/Poem/Poem.php' );

if ( $wgDBname == 'testwiki' ) {
	include( $IP.'/extensions/UnicodeConverter/UnicodeConverter.php' );
}

// Per-wiki config for Flagged Revisions
if ( $wmgUseFlaggedRevs ) {
	include( $IP.'/../wmf-config/flaggedrevs.php');
}

$wgUseAjax = true;
$wgCategoryTreeDynamicTag = true;
require( $IP.'/extensions/CategoryTree/CategoryTree.php' );


if ( $wmgUseProofreadPage ) {
//if ( $wgDBname == 'frwikisource' || $wgDBname == 'enwikisource' || $wgDBname == 'ptwikisource' ) {
	include( $IP . '/extensions/ProofreadPage/ProofreadPage.php' );
    include( $IP . '/../wmf-config/proofreadpage.php');
}
if( $wmgUseLST ) {
	include( $IP . '/extensions/LabeledSectionTransclusion/lst.php' );
}

if( $wmgUseSpamBlacklist ) {
	include( $IP.'/extensions/SpamBlacklist/SpamBlacklist.php' );
}

include( $IP.'/extensions/UploadBlacklist/UploadBlacklist.php' );
# disabled by Domas. reenabling without consulting will end up on wrath and torture
include( $IP.'/extensions/TitleBlacklist/TitleBlacklist.php' );

$wgTitleBlacklistSources = array(
	array(
		'type' => TBLSRC_URL,
		'src'  => 'http://meta.wikimedia.org/w/index.php?title=Title_blacklist&action=raw&tb_ver=1',
	),
);

#if ( $site == 'wikiversity' || $wgDBname == 'ptwikibooks' || $wgDBname == 'itwikibooks' || $wgDBname == 'enwikinews' ) {
if( $wmgUseQuiz ) {
   include( "$IP/extensions/Quiz/Quiz.php" );
}

if ( $wmgUseGadgets ) {
	include( "$IP/extensions/Gadgets/Gadgets.php" );
}

// if( $wgAutomaticGroups ) {
// include( "$IP/extensions/AutomaticGroups/AutomaticGroups.php" );
//}

include( $IP.'/extensions/OggHandler/OggHandler.php' );
$wgOggThumbLocation = '/usr/local/bin/oggThumb';
// you can keep the filename the same and use maintenance/purgeList.php
$wgCortadoJarFile = 'http://upload.wikimedia.org/jars/cortado.jar'; 

include( $IP.'/extensions/AssertEdit/AssertEdit.php' );

if ( $wgDBname == 'foundationwiki' ) {
	include( "$IP/extensions/FormPreloadPostCache/FormPreloadPostCache.php" );
	include( "$IP/extensions/SkinPerPage/SkinPerPage.php" );
	include( "$IP/extensions/skins/Schulenburg/Schulenburg.php" );
	include( "$IP/extensions/skins/Tomas/Tomas.php" );
	include( "$IP/extensions/skins/Donate/Donate.php" );
	
	include( "$IP/extensions/ContributionReporting/ContributionReporting.php" );
	include( "$IP/../wmf-config/reporting-setup.php");
	
	include( "$IP/extensions/ContactPageFundraiser/ContactPage.php" );
	$wgContactUser = 'Storiescontact';
	$wgUseTidy = false;
	
	$wgAllowedTemplates = array(
        	'enwiki_00','enwiki_01','enwiki_02','enwiki_03',
        	'enwiki_04','enwiki_05', 'donate','2009_Notice1',
        	'2009_Notice1_b', '2009_EM1Notice','2009_EM1Notice_b','2009_Notice11',
		'2009_Notice10','2009_Notice14','2009_Notice15','2009_Notice17',
		'2009_Notice17_g','2009_Notice18','2009_Notice18_g','2009_Notice21_g',
		'2009_Notice22','2009_Notice22_g','2009_Notice30','2009_Notice31',
		'2009_Notice32','2009_Notice33','2009_Notice34','2009_Notice30_g',
		'2009_Notice30_EML', 'Notice30_EML','2009_Notice35','2009_Notice36',
		'2009_Notice36_g','2009_Notice37','2009_Notice38','2009_Notice39',
		'2009_Notice40','2009_Notice30_bold','2009_Yandex1','2009_Notice41',
		'2009_Notice42','2009_Notice43','2009_Notice44','2009_Notice45',
		'2009_Notice47','2009_Notice46','2009_Notice48','2009_Craig_Appeal1',
		'2009_Jimmy_Appeal1','2009_Jimmy_Appeal3','2009_Jimmy_Appeal4','2009_Jimmy_Appeal5',
		'2009_Jimmy_Appeal7','2009_Jimmy_Appeal8','2009_Jimmy_Appeal9','2009_Notice49',
		'2009_Notice51','2009_ThankYou1','2009_ThankYou2','2010_testing1',
		'2010_testing1B','2010_testing2','2010_testing2B','2010_testing3',
		'2010_testing3B','2010_testing4','2010_testing4B','2010_testing5',
		'2010_testing5_anon','2010_testing6','2010_testing6_anon','2010_testing7',
		'2010_testing7_anon','2010_testing8','2010_testing8_anon','2010_testing9',
		'2010_testing9_anon','2010_testing10','2010_testing10_anon','2010_testing11', 
		'2010_testing11_anon','2010_testing12','2010_testing12_anon','2010_testing13', 
		'2010_testing13_anon','2010_testing14','2010_testing14_anon','2010_testing15', 
		'2010_testing15_anon','2010_testing16','2010_testing17','2010_testing18',
		'2010_testing15_anon','2010_testing16','2010_testing17','2010_testing18',
		'2010_testing19','2010_testing20','2010_testing21','2010_testing22',
		'2010_testing23','2010_testing24','2010_testing25','2010_testing26',
		'2010_testing23','2010_testing24','2010_testing25','2010_testing26',
		'2010_testing23','2010_testing24','2010_testing25','2010_testing26',
		'2010_testing27','2010_testing28','2010_testing29','2010_testing30',
		'2010_testing31','2010_testing32','2010_testing33','2010_testing34',
		'2010_testing35','2010_testing36','2010_testing37','2010_testing38',
		'2010_testing39','2010_testing40','2010_testing41','2010_testing42',
		'2010_testing43','2010_testing44','2010_testing44_twostep','2010_testing45',
		'2010_testing46','2010_testing47','2010_testing48','2010_testing49',
		'2010_testing50','2010_testing51','2010_testing52','2010_testing53',
		'2010_testing54','2010_testing55','2010_fr_testing1','2010_fr_testing5',
		'2010_fr_testing3','2010_fr_testing4','2010_de_testing1','2010_de_testing2',
		'2010_de_testing3','2010_de_testing4','2010_en_testing1',',2010_en_testing2',
		'2010_en_testing3','2010_en_testing4','2010_en_testing5','2010_en_testing6',
		'2010_en_testing7','2010_en_testing8','2010_en_testing9','2010_en_testing10',
		'2010_en_testing11','2010_en_testing12','2010_en_testing13','2010_en_testing14',
		'2010_en_testing15','2010_en_testing16','2010_en_testing17','2010_en_testing18',
		'2010_en_testing19','2010_en_testing20','2010_en_testing21','2010_en_testing22',
		'2010_en_testing23','2010_en_testing24','2010_en_testing25','2010_en_testing26',
		'2010_en_testing27','2010_en_testing28','2010_en_testing29','2010_en_testing30',
		'2010_en_testing31','2010_en_testing32','2010_en_testing33','2010_en_testing34',
		'2010_en_testing35','2010_en_testing36','2010_en_testing37','2010_en_testing38',
		'2010_en_testing39','2010_en_testing40',

	);

	$wgAllowedSupport = array(
        	'Support', 'Support2', 'ChangeWorld', 'FiveFacts',
		'Craig_Appeal', 'Appeal', 'Appeal2', 'Global_Support',
		'2010_Landing_1','2010_Landing_2','2010_Landing_3', '2010_Landing_4',
		'2010_Landing_5','2010_Landing_6','2010_Landing_7','2010_Landing_8',
		'2010_Landing_9','cc1','cc2','cc3','cc4','cc5','cc6','cc7','cc8','cc9',
		'cc10','cc11','cc12','cc13','cc14','cc15','Appeal3','Appeal4','Appeal5',
		'Appeal6','Appeal7','Appeal8','Appeal9','Appeal10','Appeal11','Appeal12',
		'Appeal13','Appeal14','Appeal16','Appeal18','Appeal20','cc15',
	);

	$wgAllowedPaymentMethod = array(
        	'cc','pp'
	);


}

if ( $wgDBname == 'mediawikiwiki' ) {
	include( "$IP/extensions/ExtensionDistributor/ExtensionDistributor.php" );
	$wgExtDistTarDir = '/mnt/upload6/ext-dist';
	$wgExtDistTarUrl = 'http://upload.wikimedia.org/ext-dist';
	$wgExtDistWorkingCopy = '/mnt/upload6/private/ExtensionDistributor/mw-snapshot';
	$wgExtDistRemoteClient = '208.80.152.165:8430';

	$wgExtDistBranches = array(
		'trunk' => array(
			'tarLabel' => 'trunk',
			'msgName' => 'extdist-current-version',
		),
		/**
		 * If you add a branch here, you must also check it out into the working copy directory.
		 * If you add it here without doing that, the extension will break.
		 */
		'branches/REL1_17' => array(
			'tarLabel' => 'MW1.17',
			'name' => '1.17.x',
		),
		'branches/REL1_16' => array(
			'tarLabel' => 'MW1.16',
			'name' => '1.16.x',
		),
		'branches/REL1_15' => array(
			'tarLabel' => 'MW1.15',
			'name' => '1.15.x',
		),
		'branches/REL1_14' => array(
			'tarLabel' => 'MW1.14',
			'name' => '1.14.x',
		),
		'branches/REL1_13' => array(
			'tarLabel' => 'MW1.13',
			'name' => '1.13.x',
		),
		'branches/REL1_12' => array(
			'tarLabel' => 'MW1.12',
			'name' => '1.12.x',
		),
		'branches/REL1_11' => array(
			'tarLabel' => 'MW1.11',
			'name' => '1.11.x',
		),
		/**
		 * If you delete a branch, you must also delete it from the working copy
		 */
		'branches/REL1_10' => array(
			'tarLabel' => 'MW1.10',
			'name' => '1.10.x',
		),
	);
}

include( $IP.'/extensions/GlobalBlocking/GlobalBlocking.php' );
$wgGlobalBlockingDatabase = 'centralauth';
$wgApplyGlobalBlocks = $wmgApplyGlobalBlocks;

include( $IP.'/extensions/TrustedXFF/TrustedXFF.php' );
$wgTrustedXffFile = "$IP/cache/trusted-xff.cdb";

if ( $wmgContactPageConf ) {
	include( $IP.'/extensions/ContactPage/ContactPage.php' );
	extract( $wmgContactPageConf );
}

include( $IP.'/extensions/SecurePoll/SecurePoll.php' );

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
}

// Mobile redirect
if ( $wmgUseWikimediaMobile && !$secure ) {
	include( "$IP/extensions/WikimediaMobile/WikimediaMobile.php" );
	$wgWikimediaMobileUrl = "http://$lang.m.$site.org/wiki";
	$wgWikimediaMobileVersion .= '.2';
}

// PoolCounter
if ( $wmgUsePoolCounter ) {
	include( "$IP/../wmf-config/PoolCounterSettings.php" );
}

wfProfileOut( "$fname-ext-include1" );
wfProfileIn( "$fname-misc2" );

/*
# Per site additional spam blacklists
# -- hashar Sat Jan  6 21:27:21 UTC 2007
switch( $wgDBname ) {
	'frwiki':
#		$wgSpamBlacklistFiles[] = 'http://fr.wikipedia.org/w/index.php?title=<INSERT_TITLE>&action=raw&sb_ver=1';
	break;
	default:

}
*/

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


if( file_exists( '/usr/bin/ploticus' ) ) {
	$wgTimelineSettings->ploticusCommand = '/usr/bin/ploticus';
} else {
	// Back-compat for Fedora boxes
	$wgTimelineSettings->ploticusCommand = '/usr/local/bin/pl';
}
$wgTimelineSettings->epochTimestamp = '20110206135500'; // fixed font setting
putenv("GDFONTPATH=/usr/local/apache/common/fonts");

$wgAllowRealName = false;
$wgSysopRangeBans = true;
$wgSysopUserBans = true;


#### To declare an outage:
#include("outage.php");
#declareOutage( "2005-01-03", "04:00", "08:00" );

# Log IP addresses in the recentchanges table
$wgPutIPinRC = true;

$wgUploadSizeWarning = false;

# Default address gets rejected by some mail hosts
$wgPasswordSender = 'wiki@wikimedia.org';

$wgLocalFileRepo = array(
	'class' => 'LocalRepo',
	'name' => 'local',
	'directory' => $wgUploadDirectory,
	'url' => $wgUploadBaseUrl ? $wgUploadBaseUrl . $wgUploadPath : $wgUploadPath,
	'hashLevels' => 2,
	'thumbScriptUrl' => $wgThumbnailScriptPath,
	'transformVia404' => true,
	'initialCapital' => $wgCapitalLinks,
	'deletedDir' => "/mnt/upload6/private/archive/$site/$lang",
	'deletedHashLevels' => 3,
        'thumbDir'         => str_replace( '/mnt/upload6', '/mnt/thumbs', "$wgUploadDirectory/thumb" ),
	);
# New commons settings
if( $wgDBname != 'commonswiki' ) {
	$wgForeignFileRepos[] = array( 
		'class'            => 'ForeignDBViaLBRepo',
		'name'             => 'shared',
		'directory'        => '/mnt/upload6/wikipedia/commons',
		'url'              => 'http://upload.wikimedia.org/wikipedia/commons',
		'hashLevels'       => 2,
		'thumbScriptUrl'   => false,
		'transformVia404'  => true,
		'hasSharedCache'   => true,
		'descBaseUrl'      => 'http://commons.wikimedia.org/wiki/File:',
		'scriptDirUrl'     => 'http://commons.wikimedia.org/w',
		'fetchDescription' => true,
		'wiki'             => 'commonswiki',
		'initialCapital'   => true,
		);
	$wgDefaultUserOptions['watchcreations'] = 1;
}

if($wgDBname == 'nostalgiawiki') {
	# Link back to current version from the archive funhouse
	if( ( isset( $_REQUEST['title'] ) && ( $title = $_REQUEST['title'] ) )
	    || (isset( $_SERVER['PATH_INFO'] )  && ( $title = substr( $_SERVER['PATH_INFO'], 1 ) ) ) ) {
		if( preg_match( '/^(.*)\\/Talk$/', $title, $matches ) ) {
			$title = 'Talk:' . $matches[1];
		}
		$wgSiteNotice = '[http://en.wikipedia.org/wiki/' .
			htmlspecialchars( urlencode( $title ) ) .
		' See the current version of this page on Wikipedia]';
	} else {
		$wgSiteNotice = '[http://en.wikipedia.org/ See current Wikipedia]';
	}
	$wgDefaultUserOptions['highlightbroken'] = 0;
}

$wgUseHashTable = true;

$wgCopyrightIcon = '<a href="http://wikimediafoundation.org/"><img src="http://bits.wikimedia.org/images/wikimedia-button.png" width="88" height="31" alt="Wikimedia Foundation"/></a>';

# For Special:Cite, we only want it on wikipedia (but can't count on $site),
# not on these fakers.
$wgLanguageCodeReal = $wgLanguageCode;
# Fake it up
if( $wgLanguageCode == 'commons' ||
    $wgLanguageCode == 'meta' ||
    $wgLanguageCode == 'sources' ||
    $wgLanguageCode == 'species' ||
    $wgLanguageCode == 'foundation' ||
    $wgLanguageCode == 'nostalgia' ||
    $wgLanguageCode == 'mediawiki'
    ) {
	$wgLanguageCode = 'en';
}

# :SEARCH:
$wgUseLuceneSearch = true;

# Proposed emergency optimisation -- TS
/*
if ( time() < 1234195258 ) {
	$wgUseLuceneSearch = false;
}*/
	
wfProfileOut( "$fname-misc2" );


if( $wgUseLuceneSearch ) {
	wfProfileIn( "$fname-lucene" );
	include( $IP.'/../wmf-config/lucene.php' );
	wfProfileOut( "$fname-lucene" );
}

// Case-insensitive title prefix search extension
// Load this _after_ Lucene so Lucene's prefix search can be used
// when available (for OpenSearch suggestions and AJAX search mode)
// But note we still need TitleKey for "go" exact matches and similar.
if( $wmgUseTitleKey ) {
    include "$IP/extensions/TitleKey/TitleKey.php";
}

wfProfileIn( "$fname-misc3" );

$wgUseDumbLinkUpdate = false;
$wgAntiLockFlags = ALF_NO_LINK_LOCK | ALF_NO_BLOCK_LOCK;
# had been using
#$wgUseDumbLinkUpdate = true;
#$wgAntiLockFlags = ALF_PRELOAD_LINKS | ALF_PRELOAD_EXISTENCE;

#$wgSquidFastPurge = true;
# Deferred update still broken
$wgMaxSquidPurgeTitles = 500;

$wgInvalidateCacheOnLocalSettingsChange = false;

// General Cache Epoch:
$wgCacheEpoch = '20060419151500'; # broken thumbnails due to power failure

// Entries olders than the general wgCacheEpoch
#if( $lang == 'sr' ) $wgCacheEpoch = '20060311075933'; # watch/unwatch title corruption
#if( $wgDBname == 'frwikiquote' ) $wgCacheEpoch = '20060331191924'; # cleaned...

// Newer entries
if( $wgDBname == 'zhwiki' ) $wgCacheEpoch = '20060528093500'; # parser bug?
if( $wgDBname == 'lawikibooks' ) $wgCacheEpoch = '20060610090000'; # sidebar bug

#$wgThumbnailEpoch = '20060227114700'; # various rsvg and imagemagick fixes
$wgThumbnailEpoch = '20051227114700'; # various rsvg and imagemagick fixes

# OAI repository for update server
include( $IP.'/extensions/OAI/OAIRepo.php' );
$oaiAgentRegex = '/experimental/';
$oaiAuth = true; # broken... squid? php config? wtf
$oaiAudit = true;
$oaiAuditDatabase = 'oai';
$wgDebugLogGroups['oai'] = 'udp://10.0.5.8:8420/oai';

$wgEnableUserEmail = true;

# XFF log for vandal tracking
function wfLogXFF() {
	if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
		$uri = ( $_SERVER['HTTPS'] ? 'https://' : 'http://' ) . 
			$_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		wfErrorLog( 
			gmdate('r') . "\t" . 
			"$uri\t" .
			"{$_SERVER['HTTP_X_FORWARDED_FOR']}, {$_SERVER['REMOTE_ADDR']}\t" .
			($_REQUEST['wpSave'] ? 'save' : '') . "\n",
			'udp://10.0.5.8:8420/xff' );
	}
}
$wgExtensionFunctions[] = 'wfLogXFF';

wfProfileOut( "$fname-misc3" );
wfProfileIn( "$fname-ext-include2" );

# Experimental category intersection plugin.
# Enabling on an exerimental basis for Wikinews only,
# 2005-03-30 brion
#if( $site == 'wikinews' || $site == 'wikiquote' ||
#    $wgDBname == 'metawiki' ||
#    $wgDBname == 'enwiktionary' || $wgDBname == 'dewiktionary' ||
#    $site == 'wikibooks' ||
#    $wgDBname == 'srwiki' ) {
if( $wmgUseDPL ) {
    include( $IP.'/extensions/intersection/DynamicPageList.php' );
}

# 2005-10-12 -ævar
# 2010-01-22 hashar: disabled as per 16878
//include( $IP.'/extensions/CrossNamespaceLinks/SpecialCrossNamespaceLinks.php' );

#if ( $wgDBname == 'frwiki' || $wgDBname == 'testwiki' || $wgDBname == 'dewiki' ) {
#	include( $IP.'/BoardVoteInit.php' );
#}

include( $IP.'/extensions/Renameuser/SpecialRenameuser.php' );

#if( $wgDBname == 'metawiki' || $wgDBname == 'mediawikiwiki' || $wgDBname == 'chrwiki' || $wgDBname == 'pdcwiki' ) {
if ( $wmgUseSpecialNuke ) {
    // TODO: Update path
    include( $IP.'/extensions/Nuke/SpecialNuke.php' );
}

include( "$IP/extensions/AntiBot/AntiBot.php" );
$wgAntiBotPayloads = array(
	'default' => array( 'log', 'fail' ),
);

include( "$IP/extensions/TorBlock/TorBlock.php" );
$wgTorLoadNodes = false;
$wgTorIPs = array( '91.198.174.2', '208.80.152.2', '203.212.189.253' );
$wgTorAutoConfirmAge = 90 * 86400;
$wgTorAutoConfirmCount = 100;
$wgTorDisableAdminBlocks = false;
$wgTorTagChanges = false;
$wgGroupPermissions['user']['torunblocked'] = false;

if ( $wmgUseRSSExtension ) {
	include( "$IP/extensions/RSS/RSS.php" );
	#$wgRSSProxy = 'url-downloader.wikimedia.org:8080';
}

wfProfileOut( "$fname-ext-include2" );
wfProfileIn( "$fname-misc4" );

# 2005-09-30: Disabled for potential XSS issue -ævar
#include 'extensions/PageCSS/PageCSS.php';

$wgDisabledActions = array( 'credits' );
# $wgDisableHardRedirects = true; // moved to InitializeSettings.php

# category rss feed extension, sponsored by kennisnet
#include("extensions/catfeed/catfeed.php");
# why is there, AGAIN, no explanation of safety or limitation to known places?


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

if ( is_array( $wmgExtraImplicitGroups ) ) {
	$wgImplicitGroups = array_merge( $wgImplicitGroups, $wmgExtraImplicitGroups );
}

$wgLegacySchemaConversion = true;

#$wgReadOnly = '5 min DB server maintenance...';
#$wgReadOnly = 'Read-only during network issues';

# fcache stuff -kate
#$fcuse = array("metawiki");
#if (in_array($wgDBname, $fcuse) && file_exists('/mnt/fcache/fcache_is_mounted')) {
#    $wgUseFileCache = true;
#    /** Directory where the cached page will be saved */
#    $wgFileCacheDirectory = "/mnt/fcache/$wgDBname/";
#}

if ( $cluster != 'pmtpa' ) {
	$wgHTTPTimeout = 10;
}

/*
if( //isset( $_POST['action'] ) &&
	isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) &&
	strpos( $_SERVER['HTTP_X_FORWARDED_FOR'], '208.63.189.57' ) !== false ) {
	$wgDebugLogFile = "udp://10.0.5.8:8420/broken-editors";
	$wgDebugDumpSql = true;
	foreach( $wgDBservers as $key => $x ) {
		$wgDBservers[$key]['flags'] |= DBO_DEBUG;
	}
}
*/
/*
if ( $wgDBname == 'enwiki' ) {
	require( "$IP/extensions/CookieBlock/CookieBlock.php" );

	$wgCookieBlocks = array(
		# Test -- block Tim on aawiki for a day
		#'d0e2a4080155d22cc2340e64b9930689' => array( 37, 86400 ),
	);
}*/

// disabling so we don't worry about xmlrpc
// 2006-10-21
#include( $IP.'/extensions/MWBlocker/MWBlockerHook.php' );
#$mwBlockerHost = 'larousse';
#$mwBlockerPort = 8126;
##$wgProxyList = array_flip( array_map( 'trim', file( 'udp://10.0.5.8:8420/mwblocker' ) ) );
$wgProxyList = "$IP/../wmf-config/mwblocker.log";

if( getenv( 'WIKIDEBUG' ) ) {
	$wgDebugLogFile = '/tmp/wiki.log';
	$wgDebugDumpSql = true;
	$wgDebugLogGroups = array();
	foreach( $wgDBservers as $key => $val ) {
		$wgDBserver[$key]['flags'] |= 1;//DBO_DEBUG;
	}
	foreach( $wgExternalServers as $key => $val ) {
		foreach( $val as $x => $y ) {
			$wgExternalServers[$key][$x]['flags'] |= 1;//DBO_DEBUG;
		}
	}
}

wfProfileOut( "$fname-misc4" );
wfProfileIn( "$fname-misc5" );

//$wgDisableSearchContext = true;
// Turn off search text extracts for random visitors, put it on
// for editors. --brion 2005-11-09
// Turning it back on -- brion 2008-03-12
//$wgDisableSearchContext = !isset($_COOKIE["{$wgDBname}_session"]);

$wgBrowserBlackList[] = '/^Lynx/';

// Vandal checks
require( $IP.'/../wmf-config/checkers.php' );

// Experimental ScanSet extension
if ( $wgDBname == 'enwikisource' ) {
	require( $IP.'/extensions/ScanSet/ScanSet.php' );
	$wgScanSetSettings = array( 
		'baseDirectory' => '/mnt/upload6/wikipedia/commons/scans',
		'basePath' => 'http://upload.wikimedia.org/wikipedia/commons/scans',
	);
}

// 2005-11-27 Special:Cite for Wikipedia -Ævar
// FIXME: This should really be done with $wmgUseSpecialCite in InitialiseSettings.php
if (   ($site == 'wikipedia' && $wgLanguageCodeReal == $wgLanguageCode ) 
     || $site == 'wikisource' 
     || ( $site == 'wikiversity' && $wgLanguageCode == 'en' )
     || ( $site == 'wikibooks' && $wgLanguageCode == 'it' )
     || ( $site == 'wiktionary' && $wgLanguageCode == 'it' ) )
	     require( $IP.'/extensions/Cite/SpecialCite.php' );

// Added throttle for account creations on zh due to mass registration attack 2005-12-16
// might be useful elesewhere. --brion
// disabled temporarily due to tugela bug -- Tim

if( false /*$lang == 'zh' || $lang == 'en'*/ ) {
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
if( $secure ) {
	require( "$IP/../wmf-config/secure.php" );
} elseif ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' ) {
	// New HTTPS service on regular URLs
	$wgServer = preg_replace( '/^http:/', 'https:', $wgServer );
} else {
	# For non-SSL hosts...
	if( $wgDBname != 'testwiki' ) {
#		$wgStyleSheetPath = 'http://upload.wikimedia.org/skins';
	}
}

if ( isset( $_REQUEST['captchabypass'] ) && $_REQUEST['captchabypass'] == $wmgCaptchaPassword ) { 
	$wmgEnableCaptcha = false;
}

if( $wmgEnableCaptcha ) {
	require( "$IP/extensions/ConfirmEdit/ConfirmEdit.php" );
	require( "$IP/extensions/ConfirmEdit/FancyCaptcha.php" );
	$wgGroupPermissions['autoconfirmed']['skipcaptcha'] = true;
#	$wgCaptchaTriggers['edit'] = true;
	$wgCaptchaSecret = $wmgCaptchaSecret;
	$wgCaptchaDirectory = '/mnt/upload6/private/captcha';
	$wgCaptchaDirectoryLevels = 3;
	$wgCaptchaStorageClass = 'CaptchaCacheStore';
	$wgCaptchaClass = 'FancyCaptcha';
	$wgCaptchaWhitelist = '#^https?://([.a-z0-9-]+\\.)?((wikimedia|wikipedia|wiktionary|wikiquote|wikibooks|wikisource|wikispecies|mediawiki|wikimediafoundation|wikinews|wikiversity)\.org|dnsstuff\.com|completewhois\.com|wikimedia\.de|toolserver\.org)(/|$)#i';
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
	if( $lang == 'be' ) {
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
	if( $wmgEmergencyCaptcha ) {
		$wgCaptchaTriggers['edit'] = true;
		$wgCaptchaTriggers['create'] = true;
	}
}



require( "$IP/extensions/Oversight/HideRevision.php" );
$wgGroupPermissions['oversight']['hiderevision'] = true;
$wgGroupPermissions['oversight']['oversight'] = true;

if ( extension_loaded( 'wikidiff2' ) ) {
	$wgExternalDiffEngine = 'wikidiff2';
}

if( function_exists( 'dba_open' ) && file_exists( "$IP/cache/interwiki.cdb" ) ) {
    $wgInterwikiCache = "$IP/cache/interwiki.cdb";
}

$wgDebugLogGroups["ExternalStoreDB"] = "udp://10.0.5.8:8420/external";

#if( $wgDBname == 'frwikiquote' || 
if( $wgDBname == 'sep11wiki' ) {
  $wgSiteNotice = @file_get_contents( $wgReadOnlyFile );
}

// Enable enotif for user talk if it's on for watchlist.
if( $wgEnotifWatchlist ) {
  $wgEnotifUserTalk = true;
} else {
  $wgShowUpdatedMarker = false; // not working right ?
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
//$wgAntiSpoofAccounts = false; // log only for now
$wgDebugLogGroups['antispoof'] = 'udp://10.0.5.8:8420/antispoof';

// For transwiki import
ini_set( 'user_agent', 'Wikimedia internal server fetcher (noc@wikimedia.org' );

// CentralAuth
if ( $wmgUseCentralAuth ) {
	include "$IP/extensions/CentralAuth/CentralAuth.php";
	$wgCentralAuthDryRun = false;
	#unset( $wgGroupPermissions['*']['centralauth-merge'] );
	#$wgGroupPermissions['sysop']['centralauth-merge'] = true;
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
	if( $secure ) {
		$wgCentralAuthCookies = true;
		$wgCentralAuthCookieDomain = 'secure.wikimedia.org';
		
		$wgCentralAuthCookiePrefix = 'centralauth_';
		
		// Don't log in to the insecure URLs
		$wgCentralAuthAutoLoginWikis = array();
	}
}

# Dismissable site notice demo
//if ( $wgDBname == 'testwiki' ) {
//	require( "$IP/sitenotice.php" );
//}

// taking it live 2006-12-15 brion
if( $wmgUseDismissableSiteNotice ) {
	require( "$IP/extensions/DismissableSiteNotice/DismissableSiteNotice.php" );
}
$wgMajorSiteNoticeID = '2';

#$wgSiteNotice = "<span class=\"ltr\">All community members are invited to give [[m:Board elections/2007/Candidates|Board Election Candidates]] their [[m:Board elections/2007/Endorsements|endorsements]].</span>";


//require( "$IP/extensions/UsernameBlacklist/UsernameBlacklist.php" );  //Removed per bug 15888

/*
function wfInitDonationLinkMessage() {
	global $wgMessageCache;
	$wgMessageCache->addMessage( 'sitenotice_link', '[http://wikimediafoundation.org/wiki/Wikimedia_thanks_Virgin_Unite Virgin Unite]' );
}
$wgExtensionFunctions[] = 'wfInitDonationLinkMessage';

*/

$wgHooks['LoginAuthenticateAudit'][] = 'logBadPassword';
$wgDebugLogGroups['badpass'] = 'udp://10.0.5.8:8420/badpass';
$wgDebugLogGroups['ts_badpass'] = 'udp://10.0.5.8:8420/ts_badpass';
$wgHooks['PrefsEmailAudit'][] = 'logPrefsEmail';
$wgHooks['PrefsPasswordAudit'][] = 'logPrefsPassword';

function logBadPassword( $user, $pass, $retval ) {
	$headers = apache_request_headers();

	if( $user->isAllowed( 'delete' ) && $retval != LoginForm::SUCCESS ) {
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
			#" - " . serialize( apache_request_headers() )
			" - " . @$headers['X-Forwarded-For'] .
			' - ' . @$headers['User-Agent'] .
			''
			 );
	}

	# Looking for broken bot on toolserver -river 2007-10-13
	if ($retval != LoginForm::SUCCESS
		&& @strpos(@$headers['X-Forwarded-For'], "91.198.174.201") !== false) 
	{
		wfDebugLog('ts_badpass', "bad login for '" . $user->getName() . "' - "
			. @$headers['User-Agent']);
	}

	return true;
}

function logPrefsEmail( $user, $old, $new ) {
	if( $user->isAllowed( 'delete' ) ) {
		$headers = apache_request_headers();
		wfDebugLog( 'badpass', "Email changed in prefs for sysop '" .
			$user->getName() .
			"' from '$old' to '$new'" .
			" - " . wfGetIP() .
			#" - " . serialize( apache_request_headers() )
			" - " . @$headers['X-Forwarded-For'] .
			' - ' . @$headers['User-Agent'] .
			'');
	}
	return true;
}

function logPrefsPassword( $user, $pass, $status ) {
	if( $user->isAllowed( 'delete' ) ) {
		$headers = apache_request_headers();
		wfDebugLog( 'badpass', "Password change in prefs for sysop '" .
			$user->getName() .
			"': $status" .
			" - " . wfGetIP() .
			#" - " . serialize( apache_request_headers() )
			" - " . @$headers['X-Forwarded-For'] .
			' - ' . @$headers['User-Agent'] .
			'');
	}
	return true;
}

if ( file_exists( '/etc/wikimedia-image-scaler' ) ) {
	$wgMaxShellMemory = 300000; // temp was 200M
}
$wgMaxShellTime = 50; // so it times out before PHP and curl and squid
$wgImageMagickTempDir = '/a/magick-tmp';

// Re-enable GIF scaling --catrope 2010-01-04
// Disabled again, apparently thumbnailed GIFs above the limits have only one frame,
//  should be unthumbnailed instead -- Andrew 2010-01-13
// Retrying with wgMaxAnimatedGifArea back at a sensible value. Andrew 2010-04-06
// if ( $wgDBname != 'testwiki' ) {
// 	$wgMediaHandlers['image/gif'] = 'BitmapHandler_ClientOnly';
// }


// Banner notice system
if( $wmgUseCentralNotice ) {
	include "$IP/extensions/CentralNotice/CentralNotice.php";
	
	// new settings for secure server support
	if( $secure ) {
        	# Don't load the JS from an insecure source!
        	$wgCentralPagePath = 'https://secure.wikimedia.org/wikipedia/meta/w/index.php';
    	} else {
        	$wgCentralPagePath = 'http://meta.wikimedia.org/w/index.php';
    	}

    	$wgNoticeProject = $wmgNoticeProject;
    	$wgCentralDBname = 'metawiki';

    	$wgCentralNoticeLoader = $wmgCentralNoticeLoader; 


	/*
	$wgNoticeTestMode = true;
	$wgNoticeEnabledSites = array(
		'test.wikipedia',
		'en.wikipedia', // sigh
		'en.*',
		'meta.*',
		'commons.*',
		# Wednesday
		'af.*',
		'ca.*',
		'de.*',
		'fr.*',
		'ja.*',
		'ru.*',
		'sv.*',
		'zh.*',
		'zh-yue.*',
	);
	*/

	# Wed evening -- all on!
	$wgNoticeTimeout = 3600;
	$wgNoticeServerTimeout = 3600; // to let the counter update
	$wgNoticeCounterSource = 'http://wikimediafoundation.org/wiki/Special:ContributionTotal' .
		'?action=raw' .
		'&start=20101112000000' . // FY 10-11
		'&fudgefactor=660000';   // fudge for pledged donations not in CRM
	
	if( $wgDBname == 'metawiki' ) {
		$wgNoticeInfrastructure = true;
	} else {
		$wgNoticeInfrastructure = false;
	}
}


// Load our site-specific l10n extensions
include "$IP/extensions/WikimediaMessages/WikimediaMessages.php";

if ( $wmgUseWikimediaLicenseTexts ) {
	include "$IP/extensions/WikimediaMessages/WikimediaLicenseTexts.php";
}
 

function wfNoDeleteMainPage( &$title, &$user, $action, &$result ) {
	global $wgMessageCache, $wgDBname;
	if ( $action !== 'delete' && $action !== 'move' ) {
		return true;
	}
	$main = Title::newMainPage();
	$mainText = $main->getPrefixedDBkey();
	if ( $mainText === $title->getPrefixedDBkey() ) {
		$wgMessageCache->addMessage( 'cant-delete-main-page', "You can't delete or move the main page." );
		$result = array( 'cant-delete-main-page' );
		return false;
	}
	return true;
}

if( $wgDBname == 'enwiki' ) {
	// Please don't interferew with our hundreds of wikis ability to manage themselves.
	// Only use this shitty hack for enwiki. Thanks.
	// -- brion 2008-04-10
	$wgHooks['getUserPermissionsErrorsExpensive'][] = 'wfNoDeleteMainPage';
}

// Quickie extension that addsa  bogus field to edit form and whinges if it's filled out
// Might or might not do anything useful :D
// Enabling just to log to udp://10.0.5.8:8420/spam
include "$IP/extensions/SimpleAntiSpam/SimpleAntiSpam.php";

if( $wmgUseCollection ) {
	// PediaPress / PDF generation
	include "$IP/extensions/Collection/Collection.php";
	#$wgPDFServer = 'http://bindery.wikimedia.org/cgi-bin/pdfserver.py';
	#$wgCollectionMWServeURL = 'http://bindery.wikimedia.org/cgi-bin/mwlib.cgi';
	#$wgCollectionMWServeURL = 'http://bindery.wikimedia.org:8080/mw-serve/';
	#$wgCollectionMWServeURL = 'http://erzurumi.wikimedia.org:8080/mw-serve/';
	$wgCollectionMWServeURL = 'http://pdf1.wikimedia.org:8080/mw-serve/';
	
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
	
	$wgLicenseURL = 'http://en.wikipedia.org/w/index.php?title=Wikipedia:Text_of_the_GNU_Free_Documentation_License&action=raw';
	
	$wgCollectionPortletForLoggedInUsersOnly = $wmgCollectionPortletForLoggedInUsersOnly;
	$wgCollectionArticleNamespaces = $wmgCollectionArticleNamespaces;
}

// Testing internally
include "$IP/../wmf-config/secret-projects.php";

/*
if ( $wgDBname == 'idwiki' ) {
	# Account creation throttle disabled for outreach event
	# Contact: Siska Doviana <serenity@gmail.com>
	if ( time() > strtotime( '2008-08-08T08:00 +7:00' )
	  && time() < strtotime( '2008-08-08T19:00 +7:00' ) )
	{
		$wgAccountCreationThrottle = 300;
	}
}
*/

/*
if ( $wgDBname == 'enwiki' ) {
	# Account creation throttle disabled for outreach event
	# Contact: fschulenburg@wikimedia.org
	if ( time() > strtotime( '2009-10-19T11:00 -7:00' )
		&& time() < strtotime( '2009-10-19T16:00 -7:00' ) )
	{
		$wgAccountCreationThrottle = 300;
	}
}
 */

if ( $wgDBname == 'elwiki' ) {
	# Account creation throttle disabled for editing workshop
	# Contact: ariel@wikimedia.org (irc: apergos)
	if ( time() > strtotime( '2011-05-16T17:00 +3:00' )
		&& time() < strtotime( '2011-05-16T20:00 +3:00') )
	{
		$wgAccountCreationThrottle = 40;
	}
}

# Account creation throttle raised for wiki week at UEM, see bug 21510
function efRaiseThrottleForUEM() {
	global $wgAccountCreationThrottle;
	if ( wfGetIP() == '193.147.239.254' &&
		time() > strtotime( '2009-11-30T08:00 +1:00' ) &&
		time() < strtotime( '2009-12-06T23:00 +1:00' ) )
	{
		$wgAccountCreationThrottle = 300;
	}
}
if ( $wgDBname == 'eswiki' )
	$wgExtensionFunctions[] = 'efRaiseThrottleForUEM';

if( $wmgUseNewUserMessage ) {
	include "$IP/extensions/NewUserMessage/NewUserMessage.php";
	$wgNewUserSuppressRC = $wmgNewUserSuppressRC;
	$wgNewUserMinorEdit = $wmgNewUserMinorEdit;
	$wgNewUserMessageOnAutoCreate = $wmgNewUserMessageOnAutoCreate;
}

if( $wmgUseCodeReview ) {
	include "$IP/extensions/CodeReview/CodeReview.php";
	include( $IP.'/../wmf-config/codereview.php');
	$wgSubversionProxy = 'http://codereview-proxy.wikimedia.org/index.php';
	
	$wgGroupPermissions['user']['codereview-add-tag'] = false;
	$wgGroupPermissions['user']['codereview-remove-tag'] = false;
	$wgGroupPermissions['user']['codereview-post-comment'] = false;
	$wgGroupPermissions['user']['codereview-set-status'] = false;
	$wgGroupPermissions['user']['codereview-link-user'] = false;
	$wgGroupPermissions['user']['codereview-signoff'] = false;
	$wgGroupPermissions['user']['codereview-associate'] = false;
	
	$wgGroupPermissions['user']['codereview-post-comment'] = true;
	
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
	$wgCodeReviewRepoStatsCacheTime = 60*60; // 1 hour, default is 6

	$wgCodeReviewMaxDiffPaths = 30;
}

if( $wmgUseAbuseFilter ) {
	include "$IP/extensions/AbuseFilter/AbuseFilter.php";
        include( $IP.'/../wmf-config/abusefilter.php');
}

if ($wmgUseCommunityVoice == true) {
 include ( "$IP/extensions/ClientSide/ClientSide.php" );
 include ( "$IP/extensions/CommunityVoice/CommunityVoice.php" );
}

if ($wmgUsePdfHandler == true) {
 include ("$IP/extensions/PdfHandler/PdfHandler.php" );
}

if ($wmgUseUsabilityInitiative) {

	$wgNavigableTOCCollapseEnable = true;
	$wgNavigableTOCResizable = true;
	require( "$IP/extensions/Vector/Vector.php" );

	require( "$IP/extensions/WikiEditor/WikiEditor.php" );

	// Uncomment this line for debugging only
	//if ( $wgDBname == 'testwiki' ) { $wgUsabilityInitiativeResourceMode = 'raw'; }
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
	if( $wgDBname == 'enwiki' ) {
		$wgHiddenPrefs[] = 'minordefault';
	}

	if ($wmgUsabilityPrefSwitch) {
		require_once( "$IP/extensions/PrefStats/PrefStats.php" );
	
		$wgPrefStatsTrackPrefs = array(
			'skin' => $wgDefaultSkin != 'vector' ? 'vector' : 'monobook',
			'usebetatoolbar' => $wmgUsabilityEnforce ? 0 : 1,
			'useeditwarning' => 0,
			'usenavigabletoc' => 1,
			'usebetatoolbar-cgd' => $wmgUsabilityEnforce ? 0 : 1,
		);
		
		require_once( "$IP/extensions/PrefSwitch/PrefSwitch.php" );
		$wgPrefSwitchGlobalOptOut = true;
		$wgPrefSwitchShowLinks = false;
	}

	
	if ($wmgUsabilityEnforce) {
		$wgEditToolbarGlobalEnable = false;
		$wgDefaultUserOptions['usebetatoolbar'] = 1;
		$wgDefaultUserOptions['usebetatoolbar-cgd'] = 1;

	}

	// For Babaco... these are still experimental, won't be on by default
	$wgNavigableTOCUserEnable = true;
	$wgEditToolbarCGDUserEnable = true;
	
	if( $wmgUserDailyContribs ) {
		require "$IP/extensions/UserDailyContribs/UserDailyContribs.php";
	}
	
	if( $wmgClickTracking ) {
		require "$IP/extensions/ClickTracking/ClickTracking.php";

		$wgClickTrackThrottle = $wmgClickTrackThrottle;
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

if ( $wmgClickTracking && $wmgCustomUserSignup ){
	include "$IP/extensions/CustomUserSignup/CustomUserSignup.php";
}

if (!$wmgEnableVector) {
	$wgSkipSkins[] = 'vector';
}

if ($wmgUseReaderFeedback) {
	require_once( "$IP/extensions/ReaderFeedback/ReaderFeedback.php" );
	$wgFeedbackStylePath = "$wgExtensionAssetsPath/ReaderFeedback";
	$wgFeedbackNamespaces = $wmgFeedbackNamespaces;
	if( $wmgFeedbackTags ) {
		$wgFeedbackTags = $wmgFeedbackTags;
	}
	$wgFeedbackSizeThreshhold = $wmgFeedbackSizeThreshhold;
}

if( $wmgUseLocalisationUpdate ) {
	require_once( "$IP/extensions/LocalisationUpdate/LocalisationUpdate.php" );
	// Set to a local checkout that's accessible when we run...
	$wgLocalisationUpdateSVNURL = "/home/wikipedia/l10n/trunk";
	$wgLocalisationUpdateDirectory = "$IP/cache/l10n";
}

if ( $wmgEnableLandingCheck ) { 
	require_once(  "$IP/extensions/LandingCheck/LandingCheck.php" ); 

	$wgPriorityCountries = array( 'AU', 'AT', 'FR', 'DE', 'HU', 'IL', 'IT', 
		'NL', 'RU', 'SE', 'CH', 'GB');
}

if ( $wmgUseLiquidThreads ) {
	require_once( "$IP/../wmf-config/liquidthreads.php" );
}

if ( $wmgUseFundraiserPortal ) {
	require "$IP/extensions/FundraiserPortal/FundraiserPortal.php";
	$wgExtensionFunctions[] = 'setupFundraiserPortal';
	function setupFundraiserPortal() {
		global $wgScriptPath; // SSL may change this after CommonSettings
		global $wgFundraiserPortalDirectory, $wgFundraiserPortalPath, $wgFundraiserImageUrl;
		$wgFundraiserPortalDirectory = "/mnt/upload6/portal";
		$wgFundraiserPortalPath = "http://upload.wikimedia.org/portal";
		$wgFundraiserImageUrl = "$wgScriptPath/extensions/FundraiserPortal/images";
	}
}

if ( $wmgDonationInterface ) {
	require_once( "$IP/extensions/DonationInterface/donate_interface/donate_interface.php" );
	require_once( "$IP/extensions/DonationInterface/payflowpro_gateway/payflowpro_gateway.php" );
//	require_once( "$IP/extensions/DonationInterface/paypal_gateway/paypal_gateway.php" ); # Pulling until Diana fixes ContribTracking Posts
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

if ( $wmgUseArticleAssessment ) {
	require_once( "$IP/extensions/PrefSwitch/PrefSwitch.php" );
	$wgPrefSwitchGlobalOptOut = true;                                                                                                                                   
	$wgPrefSwitchShowLinks = false;      
	require_once( "$IP/extensions/ArticleAssessmentPilot/ArticleAssessmentPilot.php" );

	$wgArticleAssessmentCategory = $wmgArticleAssessmentCategory;

	// WMF-specific tweaks for back compat with 1.16wmf4
	$wgSimpleSurveyJSPath = "$wgExtensionAssetsPath/PrefSwitch/PrefSwitch.js";
	$wgSimpleSurveyCSSPath = "$wgExtensionAssetsPath/PrefSwitch/PrefSwitch.css";
	$wgArticleAssessmentJUIPath = $wgArticleAssessmentJUIJSPath = "$wgExtensionAssetsPath/UsabilityInitiative/js/js2stopgap/jui.combined.min.js";
	$wgArticleAssessmentJUICSSPath = "$wgExtensionAssetsPath/UsabilityInitiative/css/vector/jquery-ui-1.7.2.css";
	$wgArticleAssessmentNeedJUICSS = true;

	// Enable this for testing only
	if ( $wgDBname == 'testwiki' ) $wgArticleAssessmentResourceMode = 'raw';
}

if ( $wmgUseArticleFeedback ) {
	require_once( "$IP/extensions/ArticleFeedback/ArticleFeedback.php" );
	$wgArticleFeedbackCategories = $wmgArticleFeedbackCategories;
	$wgArticleFeedbackLotteryOdds = $wmgArticleFeedbackLotteryOdds;
	$wgArticleFeedbackTrackingVersion = 1;

	$wgArticleFeedbackTracking = array(
		'buckets' => array(
			'track' => 10,
			'ignore' => 90,
			//'track'=>0, 'ignore' => 100	
		),
		'version' => 8,
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
}

#if ( $wgDBname == 'testwiki' ) {
#   $wgDebugLogFile = '/tmp/debuglog_tmp.txt';
#}


$wgDefaultUserOptions['thumbsize'] = $wmgThumbsizeIndex;

if ( $wgDBname == 'strategywiki' ) {
        require_once( "$IP/extensions/StrategyWiki/ActiveStrategy/ActiveStrategy.php" );
}

if ( $wgDBname == 'testwiki' || $wgDBname == 'foundationwiki' ) {
	require_once( "$IP/extensions/CommunityHiring/CommunityHiring.php" );
	$wgCommunityHiringDatabase = 'officewiki';
} elseif ( $wgDBname == 'officewiki' ) {
	require_once( "$IP/extensions/CommunityApplications/CommunityApplications.php" );
}

## Hack to block emails from some idiot user who likes 'The Joker' --Andrew 2009-05-28
$wgHooks['EmailUser'][] = 'wmfBlockJokerEmails';
$wgDebugLogGroups['block_joker_mail'] = 'udp://10.0.5.8:8420/jokermail';

function wmfBlockJokerEmails( &$to, &$from, &$subject, &$text ) {
	$blockedAddresses = array( 'the4joker@gmail.com', 'testaccount@werdn.us', 'randomdude5555@gmail.com', 'siyang.li@yahoo.com', 'johnnywiki@gmail.com', 'wikifreedomfighter@googlemail.com' );
	if ( in_array( $from->address, $blockedAddresses ) ) {
		wfDebugLog( 'block_joker_mail', "Blocked attempted email from ".$from->toString().
					" to ".$to->address." with subject ".$subject."\n" );
		return false;
	}
	return true;
}

#$wgEnableUploads = false;
#$wgUploadMaintenance = true; // temp disable delete/restore of images
#$wgSiteNotice = "Image uploads, deletes and restores are temporarily disabled while we upgrade our servers.  We expect to enable them again shortly.";
#if( $wgDBname == 'enwiki' ) {
#	$wgExtensionFunctions[] = 'logWtf';
#	function logWtf() {
#		wfDebugLog( 'wtf', $_SERVER['REQUEST_URI'] );
#	}
#}

#$wgReadOnly = "Emergency database maintenance, will be back to full shortly.";
#$wgSiteNotice = "<div style='text-align: center; background: #f8f4f0; border: solid 1px #988; font-size: 90%; padding: 4px'>Software updates are being applied to Wikimedia sites; there may be some brief interruption as the servers update.</div>";
#$wgSiteNotice = "<div style='text-align: center; background: #f8f4f0; border: solid 1px #988; font-size: 90%; padding: 4px'>Software updates are being applied to Wikimedia sites; we're shaking out a few remaining issues.</div>";

// Variable destinations for Donate link in sidebar. Currently only for test wiki
if ( $wgUseVariablePage ) {
	include( "$IP/extensions/VariablePage/VariablePage.php");
	$wgVariablePagePossibilities = array(
		'http://wikimediafoundation.org/wiki/WMFJA1/en' => 100,
	);

	$wgVariablePageShowSidebarLink = true;
	$wgVariablePageSidebarLinkQuery = array(
		'utm_source' => 'donate',
		'utm_medium' => 'sidebar',
		'utm_campaign' => 'spontaneous_donation'
	);
}

// ContributionTracking for handling PayPal redirects
if ( $wgUseContributionTracking ) {
	include( "$IP/extensions/ContributionTracking/ContributionTracking.php" );
	include( "$IP/../wmf-config/contribution-tracking-setup.php");
	$wgContributionTrackingPayPalIPN = "https://civicrm.wikimedia.org/fundcore_gateway/paypal";
	$wgContributionTrackingPayPalRecurringIPN = "https://civicrm.wikimedia.org/IPNListener_Recurring.php";
}

if ( $wmgUseUploadWizard ) {
	require_once( "$IP/extensions/UploadWizard/UploadWizard.php" );
	$wgUploadStashScalerBaseUrl = "http://upload.wikimedia.org/$site/$lang/thumb/temp";
	$wgUploadWizardConfig = array(
		#'debug' => true,
		'disableResourceLoader' => false,
		'autoCategory' => 'Uploaded with UploadWizard',
		// If Special:UploadWizard again experiences unexplained slowness loading JavaScript (spinner on intial load spinning forever) 
		// set fallbackToAltUploadForm to true.
		'fallbackToAltUploadForm' => false, # Set by neilk, 2011-05-17
		'altUploadForm' => 'Special:Upload', # Set by demon, 2011-05-10 per neilk
	);
	if ( $wgDBname == 'testwiki' ) {
		$wgUploadWizardConfig['feedbackPage'] = 'Prototype_upload_wizard_feedback';
		$wgUploadWizardConfig['altUploadForm'] = 'Special:Upload';
		unset( $wgUploadWizardConfig['fallbackToAltUploadForm'] );
	} else if ( $wgDBname == 'commonswiki' ) {
		$wgUploadWizardConfig['feedbackPage'] = 'Commons:Prototype_upload_wizard_feedback';
		$wgUploadWizardConfig['altUploadForm'] = 'Commons:Upload';
	}
}

if ( $wmgUseNarayam ) {
	require_once( "$IP/extensions/Narayam/Narayam.php" );
	$wgNarayamEnabledByDefault = false;
}

if ( $wmgUseGoogleNewsSitemap ) {
	include( "$IP/extensions/GoogleNewsSitemap/GoogleNewsSitemap.php" );
}

if ( $wmgUseCLDR ) {
	require_once( "$IP/extensions/cldr/cldr.php" );
}

#  Disable action=parse due to bug 25238 -- TS
#  ImageAnnotator disabled, reenabling parse on Commons --catrope
#if ( $wgDBname == 'commonswiki' ) {
#	$wgAPIModules['parse'] = 'ApiDisabled';
#}

if ( $wgDBname == 'ptwikibooks' ) {
	# Account creation throttle disabled for "Logística 2011" project from
	# [[FCT]] / [[New University of Lisbon]]
	$wgAccountCreationThrottle = 100;
}

$wgObjectCaches['ehcache-multiwrite'] = array(
	'class' => 'MultiWriteBagOStuff',
	'caches' => array(
		0 => array(
			'class' => 'EhcacheBagOStuff',
			'servers' => array(
				'10.0.6.50:8080',
			),
			'connectTimeout' => 0.5,
			'timeout' => 5,
		),
		1 => array(
			'factory' => 'ObjectCache::newMemcached',
		),
	)
);


# Style version appendix
# Shouldn't be needed much in 1.17 due to ResourceLoader, but some legacy things still need it
$wgStyleVersion .= '-2';

// DO NOT DISABLE WITHOUT CONTACTING PHILIPPE / LEGAL!
// Installed by Andrew, 2011-04-26
if ( $wmgUseDisableAccount ) {
	require_once( "$IP/extensions/DisableAccount/DisableAccount.php" );
	$wgGroupPermissions['bureaucrat']['disableaccount'] = true;
}

if ( $wmgUseIncubator ) {
	require_once( "$IP/extensions/WikimediaIncubator/WikimediaIncubator.php" );
}


# THIS MUST BE AFTER ALL EXTENSIONS ARE INCLUDED
#
# REALlY ... were not kidding here ... NO EXTENSIONS AFTER

require( "$IP/../wmf-config/ExtensionMessages.php" );

wfProfileOut( "$fname-misc5" );
wfProfileOut( $fname );
