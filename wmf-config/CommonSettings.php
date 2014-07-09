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
	print "No MWMultiVersion instance initialized! MWScript.php wrapper not used?\n";
	exit(1);
}
$multiVersion = MWMultiVersion::getInstance();

set_include_path( "$IP:/usr/local/lib/php:/usr/share/php" );

if ( getenv( 'WIKIBACKUP' ) && !function_exists( 'utf8_normalize' ) ) {
	dl( 'php_utfnormal.so' );
}

### Determine realm and cluster we are on #############################
# $cluster is an historical variable used for the WMF MW conf
$cluster = 'eqiad';

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
		$cluster = 'eqiad';
		break;
}
### End /Determine realm and cluster we are on/ ########################

### List of some service hostnames
# 'meta'    : meta wiki for user editable content
# 'upload'  : hostname where files are hosted
# 'wikidata': hostname for the data repository
# 'bits'    : for load.php and js/css assets
# Whenever all realms/datacenters should use the same host, do not use
# $wmfHostnames but use the hardcoded hostname instead. A good example are the
# spam blacklists hosted on meta.wikimedia.org which you will surely want to
# reuse.
$wmfHostnames = array();
switch( $wmfRealm ) {
case 'labs':
	$wmfHostnames['bits']     = 'bits.beta.wmflabs.org';
	$wmfHostnames['meta']     = 'meta.wikimedia.beta.wmflabs.org';
	$wmfHostnames['test']     = 'test.wikimedia.beta.wmflabs.org';
	$wmfHostnames['upload']   = 'upload.beta.wmflabs.org';
	$wmfHostnames['wikidata'] = 'wikidata.beta.wmflabs.org';
	break;
case 'production':
default:
	$wmfHostnames['bits']   = 'bits.wikimedia.org';
	$wmfHostnames['meta']   = 'meta.wikimedia.org';
	$wmfHostnames['test']   = 'test.wikipedia.org';
	$wmfHostnames['upload'] = 'upload.wikimedia.org';
	$wmfHostnames['wikidata'] = 'www.wikidata.org';
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
	# deployment-bastion.eqiad.wmflabs
	$wmfUdp2logDest = '10.68.16.58:8420';
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

# Try configuration cache

$filename = "/tmp/mw-cache-$wmfVersionNumber/conf-$wgDBname";
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
wfProfileOut( "$fname-confcache" );
if ( !$globals ) {
	wfProfileIn( "$fname-recache-settings" );
	# Get configuration from SiteConfiguration object
	require( "$wmfConfigDir/InitialiseSettings.php" );

	$wikiTags = array();
	foreach ( array( 'private', 'fishbowl', 'special', 'closed', 'flaggedrevs', 'small', 'medium',
			'large', 'wikimania', 'wikidata', 'wikidataclient', 'mediaviewer', 'visualeditor',
			'visualeditor-default', 'echowikis', 'commonsuploads', 'nonbetafeatures' ) as $tag ) {
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

# Extensions disabled when running under HHVM due to compatibility issues.
if ( $wmfRealm === 'labs' && defined( 'HHVM_VERSION' ) ) {
	$wmgUseWikibaseRepo = false;
	$wmgUseWikibaseClient = false;
	$wmgUseMultimediaViewer = false;
}

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
	$wgExtensionAssetsPath = "//{$wmfHostnames['test']}/w/static-$wmfVersionNumber/extensions";
	$wgStyleSheetPath = "//{$wmfHostnames['test']}/w/static-$wmfVersionNumber/skins";
	$wgResourceBasePath = "//{$wmfHostnames['test']}/w/static-$wmfVersionNumber"; // This means resources will be requested from /w/static-VERSION/resources
} else {
	$wgExtensionAssetsPath = "//{$wmfHostnames['bits']}/static-$wmfVersionNumber/extensions";
	$wgStyleSheetPath = "//{$wmfHostnames['bits']}/static-$wmfVersionNumber/skins";
	$wgResourceBasePath = "//{$wmfHostnames['bits']}/static-$wmfVersionNumber"; // This means resources will be requested from /static-VERSION/resources
}

$wgStylePath = $wgStyleSheetPath;
$wgArticlePath = "/wiki/$1";

$wgScriptPath  = '/w';
$wgLocalStylePath = "$wgScriptPath/static-$wmfVersionNumber/skins";
$wgScript           = $wgScriptPath . '/index.php';
$wgRedirectScript	= $wgScriptPath . '/redirect.php';
$wgInternalServer = $wgCanonicalServer;

if ( $wgDBname !== 'testwiki' && !defined( 'HHVM_VERSION' ) && isset( $_SERVER['SERVER_NAME'] ) ) {
	// Make testing JS/skin changes easy by not running load.php through bits for testwiki
	// Avoid bits on *.beta-hhvm.wmflabs.org as well, since the VCL code for
	// URL rewriting doesn't support having multiple domain suffixes.
	$wgLoadScript = "//{$wmfHostnames['bits']}/{$_SERVER['SERVER_NAME']}/load.php";
}

$wgCacheDirectory = '/tmp/mw-cache-' . $wmfVersionNumber;
$wgGitInfoCacheDirectory = "$IP/cache/gitinfo";

// Whether addWiki.php should send email
$wmgAddWikiNotify = true;

// Comment out the following lines to get the old-style l10n caching -- TS 2011-02-22
$wgLocalisationCacheConf['storeDirectory'] = "$IP/cache/l10n";
$wgLocalisationCacheConf['manualRecache'] = true;

// Bug 27320: skip MessageBlobStore::clear(); handle via refreshMessageBlobs.php instead
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

$wgSessionName = $wgDBname . 'Session';

$pcTemplate = array( 'type' => 'mysql',
	'dbname' => 'parsercache',
	'user' => $wgDBuser,
	'password' => $wgDBpassword,
	'flags' => 0,
);

$pcServers = array();
foreach ( $wmgParserCacheDBs as $host ) {
	$pcServers[] = array( 'host' => $host ) + $pcTemplate;
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

require( getRealmSpecificFilename( "$wmfConfigDir/session.php" ) );

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

include( $IP . '/extensions/PagedTiffHandler/PagedTiffHandler.php' );
$wgTiffUseTiffinfo = true;
$wgTiffMaxMetaSize = 1048576;

$wgMaxImageArea = 5e7; // 50MP
$wgMaxAnimatedGifArea = 5e7; // 50MP

$wgFileExtensions = array_merge( $wgFileExtensions, $wmgFileExtensions );

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

#######################################################################
# Squid Configuration
#######################################################################

$wgUseSquid = true;
$wgUseESI   = false;

require( getRealmSpecificFilename( "$wmfConfigDir/squid.php" ) );

$wgBlockOpenProxies = false;

if( $wmfRealm == 'production' ) {
	$wgUDPProfilerHost = '10.64.0.18';  // tungsten
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
		'*.wikivoyage.org',
		'www.mediawiki.org',
		'm.mediawiki.org',
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
	);
}

wfProfileOut( "$fname-misc1" );
wfProfileIn( "$fname-skin-include1" );

if ( file_exists( "$IP/skins/Modern/Modern.php" ) ) {
	require_once "$IP/skins/Modern/Modern.php";
}

if ( file_exists( "$IP/skins/CologneBlue/CologneBlue.php" ) ) {
	require_once "$IP/skins/CologneBlue/CologneBlue.php";
}

wfProfileIn( "$fname-skin-include1" );
wfProfileIn( "$fname-ext-include1" );

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

	if ( file_exists( '/usr/bin/ploticus' ) ) {
		$wgTimelineSettings->ploticusCommand = '/usr/bin/ploticus';
	}

	$wgTimelineSettings->epochTimestamp = '20130101000000';
}

putenv( "GDFONTPATH=/usr/local/apache/common/fonts" );

if ( $wmgUseWikiHiero ) {
	include( $IP . '/extensions/wikihiero/wikihiero.php' );
}

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

if ( $wmgUseCite ) {
	require( $IP . '/extensions/Cite/Cite.php' );
	require( $IP . '/extensions/Cite/SpecialCite.php' );
}

if ( $wmgUseInputBox ) {
	include( $IP . '/extensions/InputBox/InputBox.php' );
}

if ( $wmgUseImageMap ) {
	include( $IP . '/extensions/ImageMap/ImageMap.php' );
}

if ( $wmgUseGeSHi ) {
	include( $IP . '/extensions/SyntaxHighlight_GeSHi/SyntaxHighlight_GeSHi.php' );
}

if ( $wmgUseDoubleWiki ) {
	include( $IP . '/extensions/DoubleWiki/DoubleWiki.php' );
}

if ( $wmgUsePoem ) {
	include( $IP . '/extensions/Poem/Poem.php' );
}

if ( $wmgUseUnicodeConverter ) {
	include( $IP . '/extensions/UnicodeConverter/UnicodeConverter.php' );
}

// Per-wiki config for Flagged Revisions
if ( $wmgUseFlaggedRevs ) {
	include( "$wmfConfigDir/flaggedrevs.php" );
}
#Adding Flaggedrevs rights so that they are available for globalgroups/staff rights - JRA 2013-07-22
$wgAvailableRights[] = 'stablesettings';
$wgAvailableRights[] = 'review';
$wgAvailableRights[] = 'unreviewedpages';
$wgAvailableRights[] = 'movestable';
$wgAvailableRights[] = 'validate';
// So enwiki's protection rights can be assigned to global groups
$wgAvailableRights[] = 'templateeditor';
// Adding Flow's rights so that they are available for global groups/staff rights
$wgAvailableRights[] = 'flow-edit-post';
$wgAvailableRights[] = 'flow-suppress';
$wgAvailableRights[] = 'flow-hide';
$wgAvailableRights[] = 'flow-delete';

if ( $wmgUseCategoryTree ) {
	require( $IP . '/extensions/CategoryTree/CategoryTree.php' );
	$wgCategoryTreeDynamicTag = true;
	$wgCategoryTreeCategoryPageMode = $wmgCategoryTreeCategoryPageMode;
	$wgCategoryTreeCategoryPageOptions = $wmgCategoryTreeCategoryPageOptions;
}

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
	$wgLogSpamBlacklistHits = true;
}

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
	$wgFFmpegThreads = 2;

	// Minimum size for an embed video player
	$wgMinimumVideoPlayerSize = $wmgMinimumVideoPlayerSize;

	// Enable low-res Theora transcodes for fallback players on slow machines
	// Put them at the beginning of the array to keep ordering the way
	// the popup player expects, so we pick the right WebM size in most
	// cases.
	//
	// See https://bugzilla.wikimedia.org/61760
	//
	array_unshift( $wgEnabledTranscodeSet, WebVideoTranscode::ENC_OGV_360P );
	array_unshift( $wgEnabledTranscodeSet, WebVideoTranscode::ENC_OGV_160P );
}

if ( $wgDBname == 'foundationwiki' ) {
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

if ( $wmgPFEnableStringFunctions ) {
	$wgPFEnableStringFunctions = true;
}

if ( $wgDBname == 'mediawikiwiki' ) {
	include( "$IP/extensions/ExtensionDistributor/ExtensionDistributor.php" );
	$wgExtDistListFile = 'https://gerrit.wikimedia.org/mediawiki-extensions.txt';
	$wgExtDistArchiveAPI = 'https://api.github.com/repos/wikimedia/mediawiki-extensions-$EXT/tarball/$REF';
	$wgExtDistProxy = 'url-downloader.wikimedia.org:8080';

	// When changing the Snapshot Refs please change the corresponding
	// extension distributor messages for mediawiki.org in WikimediaMessages/i18n/wikimedia/*.json too
	$wgExtDistSnapshotRefs = array(
		'master',
		'REL1_23',
		'REL1_22',
		'REL1_21',
		'REL1_20',
		'REL1_19',
	);
}

include( $IP . '/extensions/GlobalBlocking/GlobalBlocking.php' );
$wgGlobalBlockingDatabase = 'centralauth';
$wgApplyGlobalBlocks = $wmgApplyGlobalBlocks;
$wgGlobalBlockingBlockXFF = $wmgUseXFFBlocks;

include( $IP . '/extensions/TrustedXFF/TrustedXFF.php' );
if ( function_exists( 'dba_open' ) && file_exists( "$wmfConfigDir/trusted-xff.cdb" ) ) {
	$wgTrustedXffFile = "$wmfConfigDir/trusted-xff.cdb";
}

if ( $wmgUseContactPage ) {
	include( $IP . '/extensions/ContactPage/ContactPage.php' );
	$wgContactConfig['default'] = array_merge( $wgContactConfig['default'], $wmgContactPageConf );
}

if ( $wmgUseSecurePoll ) {
	include( $IP . '/extensions/SecurePoll/SecurePoll.php' );

	$wgSecurePollScript = 'auth-api.php';
	$wgHooks['SecurePoll_JumpUrl'][] = function( $page, &$url ) {
		global $site, $lang;

		$url = wfAppendQuery( $url, array( 'site' => $site, 'lang' => $lang ) );
		return true;
	};
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

$wgHiddenPrefs[] = 'realname';

# Default address gets rejected by some mail hosts
$wgPasswordSender = 'wiki@wikimedia.org';

# e-mailing password based on e-mail address (bug 34386)
$wgPasswordResetRoutes['email'] = true;

# Cluster-dependent files for file backend
require( getRealmSpecificFilename( "$wmfConfigDir/filebackend.php" ) );

# Cluster-dependent files for job queue and job queue aggregator
require( getRealmSpecificFilename( "$wmfConfigDir/jobqueue.php" ) );

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
	require_once "$IP/skins/Nostalgia/Nostalgia.php";
}

$wgUseHashTable = true;

$wgCopyrightIcon = '<a href="//wikimediafoundation.org/"><img src="//' . $wmfHostnames['bits'] . '/images/wikimedia-button.png" width="88" height="31" alt="Wikimedia Foundation"/></a>';

# For Special:Cite, we only want it on wikipedia (but can't count on $site),
# not on these fakers.
$wgLanguageCodeReal = $wgLanguageCode;
# Fake it up
if ( in_array( $wgLanguageCode, array( 'commons', 'meta', 'sources', 'species', 'foundation', 'nostalgia', 'mediawiki', 'login' ) ) ) {
	$wgLanguageCode = 'en';
}

$wgDisableCounters     = true;

wfProfileOut( "$fname-misc2" );

# :SEARCH:

# Better make sure the global setting is enabled
$wgUseLuceneSearch = $wmfRealm != 'labs';
if ( $wgUseLuceneSearch ) {
	wfProfileIn( "$fname-lucene" );
	include( "$wmfConfigDir/lucene-common.php" );
	wfProfileOut( "$fname-lucene" );
}
# New wikis are special and get Cirrus :)
if ( $wmgUseCirrus || $wmgUseCirrusAsAlternative ) {
	wfProfileIn( "$fname-CirrusSearch" );
	# Cirrus uses SearchUpdate, turn it back on
	include( "$wmfConfigDir/CirrusSearch-common.php" );
	wfProfileOut( "$fname-CirrusSearch" );
}

// Case-insensitive title prefix search extension
// Load this _after_ Lucene so Lucene's prefix search can be used
// when available (for OpenSearch suggestions and AJAX search mode)
// But note we still need TitleKey for "go" exact matches and similar.
// CirrusSearch, on the other hand, has absorbed its functionality so
// for CirrusSearch wikis it is no longer required.
if ( $wmgUseTitleKey && !$wmgUseCirrus ) {
	include "$IP/extensions/TitleKey/TitleKey.php";
}

wfProfileIn( "$fname-misc3" );

// Various DB contention settings
$wgAntiLockFlags = ALF_NO_LINK_LOCK | ALF_NO_BLOCK_LOCK;
# $wgAntiLockFlags = ALF_PRELOAD_LINKS | ALF_PRELOAD_EXISTENCE;
if ( in_array( $wgDBname, array( 'testwiki', 'test2wiki', 'mediawikiwiki', 'commonswiki' ) ) ) {
	$wgSiteStatsAsyncFactor = 1;
}

# Deferred update still broken
$wgMaxSquidPurgeTitles = 500;

$wgInvalidateCacheOnLocalSettingsChange = false;

// General Cache Epoch
$wgCacheEpoch = '20130101000000';

$wgThumbnailEpoch = '20130101000000';

# OAI repository for update server
include( $IP . '/extensions/OAI/OAIRepo.php' );
$oaiAgentRegex = '/experimental/';
$oaiAuth = true;
$oaiAuditDatabase = 'oai';
$oaiChunkSize = 40;

$wgEnableUserEmail = true;
$wgNoFollowLinks = true; // In case the MediaWiki default changed, bug 42594

# XFF log for vandal tracking
$wgExtensionFunctions[] = function() {
	global $wmfUdp2logDest, $wgRequest;
	if ( isset( $_SERVER['REQUEST_METHOD'] ) && $_SERVER['REQUEST_METHOD'] === 'POST' ) {
		$uri = ( $_SERVER['HTTPS'] ? 'https://' : 'http://' ) .
			$_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

		wfErrorLog(
			gmdate( 'r' ) . "\t" .
			"$uri\t" .
			"{$_SERVER['HTTP_X_FORWARDED_FOR']}, {$_SERVER['REMOTE_ADDR']}\t" .
			( $_REQUEST['wpSave'] ? 'save' : '' ) . "\n",
			"udp://$wmfUdp2logDest/xff"
		);
		if ( $wgRequest->getIP() === '127.0.0.1' ) {
			wfErrorLog(
				gmdate( 'r' ) . "\t" .
				wfHostname() .
				"\t{$_SERVER['HTTP_X_FORWARDED_FOR']}, {$_SERVER['REMOTE_ADDR']}\t" .
				WebRequest::detectProtocol(),
				"udp://$wmfUdp2logDest/localhost"
			);
		}
	}
};

// bug 24313, turn off minordefault on enwiki
if ( $wgDBname == 'enwiki' ) {
	$wgHiddenPrefs[] = 'minordefault';
}

if ( $wmgUseFooterContactLink ) {
	$wgHooks['SkinTemplateOutputPageBeforeExec'][] = function( $sk, &$tpl ) {
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
	$wgTorBlockProxy = 'url-downloader.wikimedia.org:8080';
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

if ( $wgDBname == 'loginwiki' ) {
	$wgGroupPermissions['*'] = array(
		'read' => true,
		'centralauth-autoaccount' => true,
	);
	$wgGroupPermissions['user'] = array(
		'read' => true,
	);
	$wgGroupPermissions['autoconfirmed'] = array(
		'read' => true,
	);

	unset( $wgGroupPermissions['import'] );
	unset( $wgGroupPermissions['transwiki'] );

	$wgGroupPermissions['sysop'] = array_merge(
		$wgGroupPermissions['sysop'],
		array(
			'editinterface' => false,
			'editusercss' => false,
			'edituserjs' => false,
		)
	);
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
}

wfProfileOut( "$fname-misc4" );
wfProfileIn( "$fname-misc5" );

$wgBrowserBlackList[] = '/^Lynx/';

if ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' ) {
	// New HTTPS service on regular URLs
	$wgInternalServer = $wgServer; // Keep this as HTTP for IRC notifications (bug 29925)
	$wgServer = preg_replace( '/^http:/', 'https:', $wgServer );
}

// Disable redirects to HTTPS for clients in some countries
$wgHooks['CanIPUseHTTPS'][] = function( $ip, &$canDo ) {
	global $wmgHTTPSBlacklistCountries;

	if ( !function_exists( 'geoip_country_code_by_name' ) ) {
		return true;
	}
	// geoip_country_code_by_name() gives a warning for IPv6 addresses, possibly does DNS resolution
	if ( !IP::isIPv4( $ip ) ) {
		return true;
	}

	$country = geoip_country_code_by_name( $ip );
	if ( in_array( $country, $wmgHTTPSBlacklistCountries ) ) {
		$canDo = false;
	}
	return true;
};

if ( isset( $_REQUEST['captchabypass'] ) && $_REQUEST['captchabypass'] == $wmgCaptchaPassword ) {
	$wmgEnableCaptcha = false;
}

if ( $wmgEnableCaptcha ) {
	require( "$IP/extensions/ConfirmEdit/ConfirmEdit.php" );
	require( "$IP/extensions/ConfirmEdit/FancyCaptcha.php" );
	$wgGroupPermissions['autoconfirmed']['skipcaptcha'] = true;
	if ( $wmfRealm !== 'labs' ) {
		$wgCaptchaFileBackend = 'global-multiwrite';
		// Disable addurl captcha trigger on beta labs -- KartikMistry
		if ( $wgDBname == 'cawiki' || $wgDBname == 'eswiki' ) {
			$wgCaptchaTriggers['addurl'] = false;
		}
	}
	# $wgCaptchaTriggers['edit'] = true;
	$wgCaptchaSecret = $wmgCaptchaSecret;
	$wgCaptchaDirectory = '/mnt/upload7/private/captcha';
	$wgCaptchaDirectoryLevels = 3;
	$wgCaptchaStorageClass = 'CaptchaCacheStore';
	$wgCaptchaClass = 'FancyCaptcha';
	$wgCaptchaWhitelist =
		'#^(https?:)?//([.a-z0-9-]+\\.)?((wikimedia|wikipedia|wiktionary|wikiquote|wikibooks|wikisource|wikispecies|mediawiki|wikimediafoundation|wikinews|wikiversity|wikivoyage|wikidata|wmflabs)\.org'
		. '|dnsstuff\.com|completewhois\.com|wikimedia\.de|toolserver\.org)([?/\#]|$)#i';
	$wgCaptchaWhitelistIP = array( '91.198.174.0/24' ); # toolserver (bug 23982)

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
	$wgCentralAuthSilentLogin = $wmgCentralAuthSilentLogin;
	$wgCentralAuthUseOldAutoLogin = !$wgCentralAuthSilentLogin;
	$wgCentralAuthUseEventLogging = $wmgCentralAuthUseEventLogging;

	if( $wmfRealm == 'production' ) {
		$wgCentralAuthRC[] = array(
			'formatter' => 'IRCColourfulCARCFeedFormatter',
			'uri' => "udp://$wmgRC2UDPAddress:$wmgRC2UDPPort/#central\t",
		);
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
		$wgCentralAuthAutoLoginWikis = array(
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
			$wmfHostnames['wikidata'] => 'wikidatawiki',
			'ee-prototype.wikipedia.beta.wmflabs.org' => 'ee_prototypewiki',
		);
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

	$wgHooks['CentralAuthWikiList'][] = function( &$list ) {
		global $wgLocalDatabases, $IP, $wgSiteMatrixPrivateSites,
			$wgSiteMatrixFishbowlSites, $wgSiteMatrixClosedSites;

		$list = array_diff(
			$wgLocalDatabases,
			$wgSiteMatrixPrivateSites,
			$wgSiteMatrixFishbowlSites,
			$wgSiteMatrixClosedSites
		);
		return true;
	};

	// Let's give it another try
	$wgCentralAuthCreateOnView = true;

	// Attempt to attach unattached accounts by password on login
	$wgCentralAuthAutoMigrate = true;
}

// Config for GlobalCssJs
// Only enable on CentralAuth wikis
// Note that config in CommonSettings-labs.php
// will override this on beta labs-- legoktm 04/17/14
if ( $wmgUseGlobalCssJs && $wmgUseCentralAuth ) {
	require_once( "$IP/extensions/GlobalCssJs/GlobalCssJs.php" );

	// Disable site-wide global css/js
	$wgUseGlobalSiteCssJs = false;

	// Setup test2wiki as central wiki
	$wgResourceLoaderSources['test2wiki'] = array(
		'apiScript' => '//test2.wikipedia.org/w/api.php',
		'loadScript' => '//bits.wikimedia.org/test2.wikipedia.org/load.php',
	);

	$wgGlobalCssJsConfig = array(
		'wiki' => 'test2wiki',
		'source' => 'test2wiki',
	);
}


// taking it live 2006-12-15 brion
if ( $wmgUseDismissableSiteNotice ) {
	require( "$IP/extensions/DismissableSiteNotice/DismissableSiteNotice.php" );
}
$wgMajorSiteNoticeID = '2';

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

		wfDebugLog( 'badpass', "$bit for sysop '" .
			$user->getName() . "' from " . $wgRequest->getIP() .
			# " - " . serialize( apache_request_headers() )
			" - " . @$headers['X-Forwarded-For'] .
			' - ' . @$headers['User-Agent'] .
			''
			 );
	}
	return true;
};

$wgHooks['PrefsEmailAudit'][] = function( $user, $old, $new ) {
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
};

$wgHooks['PrefsPasswordAudit'][] = function( $user, $pass, $status ) {
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
};

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

	// Rely on GeoIP cookie for geolocation
	$wgCentralGeoScriptURL = false;

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
	$wgNoticeRunMessageIndexRebuildJobImmediately = false;

	// Bug 49905
	$wgNoticeUseLanguageConversion = true;

	// *** Hide Cookies ***
	// A little bit of historical breadcrumbs:
	// In 2012 we expired cookies on 2012-12-26, then everyone had
	// a two week expiration until 2013-01-22 whereupon we introduced
	// a year long expiration. For the 2013 fundraiser starting
	// 2013-12-02 we're now using a 10 month expiration.
	$wgNoticeCookieShortExpiry = 1209600; // 2 weeks
	$wgNoticeCookieLongExpiry = 25920000; // 10 months

	// Bug 16821
	// Updates made here also need to be reflected in
	// wikimediafoundation.org/wiki/Template:HideBanners
	$wgNoticeHideUrls = array(
		'//en.wikipedia.org/wiki/Special:HideBanners',
		'//meta.wikimedia.org/wiki/Special:HideBanners',
		'//commons.wikimedia.org/wiki/Special:HideBanners',
		'//species.wikimedia.org/wiki/Special:HideBanners',
		'//en.wikibooks.org/wiki/Special:HideBanners',
		'//en.wikiquote.org/wiki/Special:HideBanners',
		'//en.wikisource.org/wiki/Special:HideBanners',
		'//en.wikinews.org/wiki/Special:HideBanners',
		'//en.wikiversity.org/wiki/Special:HideBanners',
		'//www.mediawiki.org/wiki/Special:HideBanners',
	);
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
			$result = array( 'cant-delete-main-page' );
			return false;
		}
		return true;
	};

	// Bug 57569
	//
	// If it's an anonymous user creating a page in the English Wikipedia Draft
	// namespace, tell TitleQuickPermissions to abort the normal checkQuickPermissions
	// checks.  This lets anonymous users create a page in this namespace, even though
	// they don't have the general 'createpage' right.
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
	$wgCollectionMWServeURL = "http://pdf2.wikimedia.org:8080/mw-serve/";

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

	$wgGroupPermissions['user']['codereview-add-tag'] = false;
	$wgGroupPermissions['user']['codereview-remove-tag'] = false;
	$wgGroupPermissions['user']['codereview-post-comment'] = false;
	$wgGroupPermissions['user']['codereview-set-status'] = false;
	$wgGroupPermissions['user']['codereview-link-user'] = false;
	$wgGroupPermissions['user']['codereview-signoff'] = false;
	$wgGroupPermissions['user']['codereview-associate'] = false;
	$wgGroupPermissions['svnadmins']['repoadmin'] = false;

	$wgCodeReviewRepoStatsCacheTime = 24 * 60 * 60;
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

require( "$IP/extensions/WikiEditor/WikiEditor.php" );

// Disable experimental things
$wgWikiEditorFeatures['preview'] =
	$wgWikiEditorFeatures['previewDialog'] =
	$wgWikiEditorFeatures['publish'] = array( 'global' => false, 'user' => true ); // Hidden from prefs view
$wgHiddenPrefs[] = 'wikieditor-preview';
$wgHiddenPrefs[] = 'wikieditor-previewDialog';
$wgHiddenPrefs[] = 'wikieditor-publish';

$wgDefaultUserOptions['usebetatoolbar'] = 1;
$wgDefaultUserOptions['usebetatoolbar-cgd'] = 1;

if ( $wmgUserDailyContribs ) {
	require "$IP/extensions/UserDailyContribs/UserDailyContribs.php";
}


if ( $wmgUseLocalisationUpdate ) {
	require_once( "$IP/extensions/LocalisationUpdate/LocalisationUpdate.php" );
	$wgLocalisationUpdateDirectory = "/var/lib/l10nupdate/cache-$wmfVersionNumber";
	$wgLocalisationUpdateRepository = 'local';
	$wgLocalisationUpdateRepositories['local'] = array(
		'mediawiki' => '/var/lib/l10nupdate/mediawiki/core/%PATH%',
		'extension' => '/var/lib/l10nupdate/mediawiki/extensions/%NAME%/%PATH%',
	);
}

if ( $wmgEnableLandingCheck ) {
	require_once(  "$IP/extensions/LandingCheck/LandingCheck.php" );

	$wgPriorityCountries = array(
		// === Fundraising Chapers
		'DE', 'CH',

		// --- France and it's territories (per WMFr email 2012-06-13)
		//     Not a fundraising chapter in 2013+ due to FR regulations
		//'FR',
		//'GP', 'MQ', 'GF', 'RE', 'YT', 'PM',
                //'NC', 'PF', 'WF', 'BL', 'MF', 'TF',

		// === Blacklisted countries
                'BY', 'CD', 'CI', 'CU', 'IQ', 'IR', 'KP', 'LB', 'LY', 'MM', 'SD', 'SO', 'SY', 'YE', 'ZW',
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
	$wgGlobalUsageSharedRepoWiki = 'commonswiki';
	$wgGlobalUsagePurgeBacklinks = true;
}

if ( $wmgUseAPIRequestLog ) {
	$wgAPIRequestLog = "udp://locke.wikimedia.org:9000/$wgDBname";
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

// Temporary override: WMF is not hardcore enough to enable this. See bug 36316, 45022 about it.
$wgDefaultUserOptions['watchdefault'] = 0;
$wgDefaultUserOptions['enotifwatchlistpages'] = 0;

if ( $wgDBname == 'strategywiki' ) {
	require_once( "$IP/extensions/StrategyWiki/ActiveStrategy/ActiveStrategy.php" );
}

# # Hack to block emails from some idiot user who likes 'The Joker' --Andrew 2009-05-28
$wgHooks['EmailUser'][] = function ( &$to, &$from, &$subject, &$text ) {
	$blockedAddresses = array( 'the4joker@gmail.com', 'randomdude5555@gmail.com', 'siyang.li@yahoo.com', 'johnnywiki@gmail.com', 'wikifreedomfighter@googlemail.com' );
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

if ( $wmgUseUploadWizard ) {
	require_once( "$IP/extensions/UploadWizard/UploadWizard.php" );
	# Do not change $wgUploadStashScalerBaseUrl to a protocol-relative URL. This is how UploadStash fetches previews from our scaler, behind
	# the scenes, that it then streams to the client securely (much like img_auth.php). -- neilk, 2011-09-12
	$wgUploadStashScalerBaseUrl = "//{$wmfHostnames['upload']}/$site/$lang/thumb/temp";
	$wgUploadWizardConfig = array(
		# 'debug' => true,
		'disableResourceLoader' => false,
		'autoAdd' => array(
			'categories' => array(
				'Uploaded with UploadWizard',
			),
		),
		// If Special:UploadWizard again experiences unexplained slowness loading JavaScript (spinner on intial load spinning forever)
		'flickrApiUrl' => '//api.flickr.com/services/rest/?',
		// Normally we don't include API keys in CommonSettings, but this key
		// isn't private since it's used on the client-side, i.e. anyone can see
		// it in the outgoing AJAX requests to Flickr.
		'flickrApiKey' => 'e9d8174a79c782745289969a45d350e8',
		// Slowwwwwwww
		'campaignExpensiveStatsEnabled' => false,
	);

	$wgUploadWizardConfig['enableChunked'] = 'opt-in';
	$wgUploadWizardConfig['altUploadForm'] = $wmgAltUploadForm; // bug 33513

	if ( $wgDBname == 'testwiki' ) {
		$wgUploadWizardConfig['feedbackPage'] = 'Prototype_upload_wizard_feedback';
		$wgUploadWizardConfig["missingCategoriesWikiText"] = '<p><span class="errorbox"><b>Hey, no categories?</b></span></p>';
		unset( $wgUploadWizardConfig['fallbackToAltUploadForm'] );
	} elseif ( $wgDBname == 'commonswiki' ) {
		$wgUploadWizardConfig['feedbackPage'] = 'Commons:Upload_Wizard_feedback'; # Set by neilk, 2011-11-01, per erik
		$wgUploadWizardConfig["missingCategoriesWikiText"] = "{{subst:unc}}";
		$wgUploadWizardConfig['blacklistIssuesPage'] = 'Commons:Upload_Wizard_blacklist_issues'; # Set by neilk, 2011-11-01, per erik
		$wgUploadWizardConfig['flickrBlacklistPage'] = 'User:FlickreviewR/bad-authors';
	} elseif ( $wgDBname == 'test2wiki' ) {
		$wgUploadWizardConfig['feedbackPage'] = 'Wikipedia:Upload_Wizard_feedback'; # Set by neilk, 2011-11-01, per erik
		$wgUploadWizardConfig["missingCategoriesWikiText"] = "{{subst:unc}}";
		$wgUploadWizardConfig['blacklistIssuesPage'] = 'Wikipedia:Upload_Wizard_blacklist_issues'; # Set by neilk, 2011-11-01, per erik
	}

	// Needed to make UploadWizard work in IE, see bug 39877
	$wgApiFrameOptions = 'SAMEORIGIN';
} else {
	// If XFO wasn't specified due to UploadWizard, set it here
	$wgApiFrameOptions = $wmgApiFrameOptions;
}

if ( $wmgUseBetaFeatures ) {
	require_once( "$IP/extensions/BetaFeatures/BetaFeatures.php" );
	if ( $wmgBetaFeaturesWhitelist ) {
		$wgBetaFeaturesWhitelist = $wmgBetaFeaturesWhitelist;
	}
}

if ( $wmgUseCommonsMetadata ) {
	require_once( "$IP/extensions/CommonsMetadata/CommonsMetadata.php" );
}

if ( $wmgUseGWToolset ) {
	require_once( "$IP/extensions/GWToolset/GWToolset.php" );
	$wgGWTFileBackend = 'local-multiwrite';
	$wgGWTFBMaxAge = '1 week';
	$wgJobTypeConf['gwtoolsetUploadMetadataJob'] = array( 'checkDelay' => true ) + $wgJobTypeConf['default'];
	// extra throttling until the image scalers are more robust
	GWToolset\Config::$mediafile_job_throttle_default = 5; // 5 files per batch
	$wgJobBackoffThrottling['gwtoolsetUploadMetadataJob'] = 5 / 3600; // 5 batches per hour
}

if ( $wmgUseMultimediaViewer ) {
	require_once( "$IP/extensions/MultimediaViewer/MultimediaViewer.php" );
	$wgNetworkPerformanceSamplingFactor = $wmgNetworkPerformanceSamplingFactor;
	$wgMediaViewerDurationLoggingSamplingFactor = $wmgMediaViewerDurationLoggingSamplingFactor;
	$wgMediaViewerActionLoggingSamplingFactorMap = $wmgMediaViewerActionLoggingSamplingFactorMap;

	if ( $wmgMediaViewerBeta ) {
		$wgMediaViewerIsInBeta = true;
	}

	if ( $wmgMediaViewerLoggedIn ) {
		$wgEnableMediaViewerForLoggedInUsersOnly = true;
	}

	if ( $wmgMediaViewerUseThumbnailGuessing ) {
		$wgMediaViewerUseThumbnailGuessing = true;
	}

	if ( $wmgMediaViewerShowSurvey ) {
		$wgMediaViewerShowSurvey = $wmgMediaViewerShowSurvey;
	}
}

if ( $wmgUsePopups ) {
	require_once( "$IP/extensions/Popups/Popups.php" );
}

if ( $wmgUseVectorBeta ) {
	require_once( "$IP/extensions/VectorBeta/VectorBeta.php" );
	$wgVectorBetaPersonalBar = $wmgVectorBetaPersonalBar;
}

if ( $wmgUseParsoid ) {
	require_once( "$IP/extensions/Parsoid/php/Parsoid.php" );

	$wmgParsoidURL = 'http://10.2.2.29'; // parsoidcache.svc.eqiad.wmnet

	// The wiki prefix to use
	$wgParsoidWikiPrefix = $wgDBname;

	// List the parsoid cache servers to keep up to date.
	//
	// We target the load balancer in front of the front-end caches, which
	// will then pick one front-end. This works as we disabled caching in the
	// front-ends. The main reason for doing it this way is that request
	// coalescing in the backends does not work with req.hash_always_miss =
	// true.
	$wgParsoidCacheServers = array(
		'http://10.2.2.29', // parsoidcache.svc.eqiad.wmnet
	);

	// Load shedding knob, affects whether new Parsoid jobs are enqueued.
	// Set to something between 0 (process all updates) and 1 (skip all updates).
	$wgParsoidSkipRatio = 0;

	// Throttle rate of template updates by setting the number of tests per
	// job to something lowish, and limiting the maximum number of updates to
	// process per template edit
	$wgParsoidCacheUpdateTitlesPerJob = 10;
	$wgParsoidMaxBacklinksInvalidate = 500000;
}

if ( $wmgUseVisualEditor ) {
	require_once( "$IP/extensions/VisualEditor/VisualEditor.php" );

	$wgVisualEditorParsoidURL = $wmgParsoidURL;
	$wgVisualEditorParsoidPrefix = $wgParsoidWikiPrefix;
	$wgVisualEditorParsoidProblemReportURL = 'http://parsoid.wmflabs.org/_bugs/';

	if ( !$wmgVisualEditorInContentNamespaces ) {
		$wgVisualEditorNamespaces = array(); // Wipe out default set by VisualEditor.php
	}
	$wgVisualEditorNamespaces = array_merge( $wgVisualEditorNamespaces, $wmgVisualEditorNamespaces );

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
	if ( $wmgVisualEditorDefault ) {
		$wgDefaultUserOptions['visualeditor-enable'] = 1;
		$wgHiddenPrefs[] = 'visualeditor-enable'; // Bug 48666
	} else {
		// Only show the beta-disable preference if the wiki is in 'beta'.
		$wgHiddenPrefs[] = 'visualeditor-betatempdisable';
	}
	if ( $wmgVisualEditorExperimental ) {
		$wgDefaultUserOptions['visualeditor-enable-experimental'] = 1;
	}
	// Bug 50000 - to remove once roll-out is complete.
	if ( $wmgVisualEditorDisableForAnons ) {
		$wgVisualEditorDisableForAnons = true;
	}
	if ( $wmgVisualEditorShowBetaWelcome ) {
		$wgVisualEditorShowBetaWelcome = true;
	}
	if ( $wmgVisualEditorSecondaryTabs ) {
		$wgVisualEditorTabPosition = 'after';
	}
	if ( $wmgVisualEditorBetaInTab ) {
		$wgVisualEditorTabMessages['editappendix'] =
			$wgVisualEditorTabMessages['createappendix'] =
			$wgVisualEditorTabMessages['editsectionappendix'] = 'visualeditor-beta-appendix';
	}
	if ( $wmgVisualEditorParsoidForwardCookies ) {
		$wgVisualEditorParsoidForwardCookies = true;
	}
	if ( $wmgVisualEditorEnableTocWidget ) {
		$wgVisualEditorEnableTocWidget = true;
	}
}

if ( $wmgUseTemplateData ) {
	require_once( "$IP/extensions/TemplateData/TemplateData.php" );
}

if ( $wmgTemplateDataUseGUI ) {
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

if ( $wmgUseMobileApp ) {
	require_once( "$IP/extensions/MobileApp/MobileApp.php" );
}

# Mobile related configuration

require( getRealmSpecificFilename( "$wmfConfigDir/mobile.php" ) );

# MUST be after MobileFrontend initialization
if ( $wmgEnableTextExtracts ) {
	require_once( "$IP/extensions/TextExtracts/TextExtracts.php" );
	$wgExtractsRemoveClasses = array_merge( $wgExtractsRemoveClasses, $wmgExtractsRemoveClasses );
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

$wgCookieExpiration = 30 * 86400;

if ( $wmgUseMath ) {
	require_once( "$IP/extensions/Math/Math.php" );
	$wgTexvc = "/usr/local/apache/uncommon/bin/texvc";
	$wgMathTexvcCheckExecutable = "/usr/local/apache/uncommon/bin/texvccheck";
	if ( $wgDBname === 'hewiki' ) {
		$wgDefaultUserOptions['math'] = 0;
	}
	$wgMathFileBackend = $wmgMathFileBackend;
	$wgMathDirectory   = '/mnt/upload7/math'; // just for sanity
	$wgMathPath        = $wmgMathPath;
	$wgUseMathJax      = true;
	$wgMathDisableTexFilter = true; // severe performance regression; --Aaron
	// disable MW_MATH_MATHML and MW_MATH_SOURCe until mathoid table is created (bug 66492, 66587)
	$wgMathValidModes = array( MW_MATH_PNG );
	// These variables point to non-WMF servers by default.
	// Prevent accidental use.
	$wgMathMathMLUrl = $wgMathLaTeXMLUrl = null;
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
	$wgGroupPermissions['translationadmin']['translate-import'] = true; // bug 40341
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
					'host' => 'zinc.eqiad.wmnet',
					'timeout' => 10,
				),
				'adapter' => 'Solarium_Client_Adapter_Curl',
			),
		);
	}

	$wgTranslateWorkflowStates = $wmgTranslateWorkflowStates;
	$wgTranslateRcFilterDefault = $wmgTranslateRcFilterDefault;

	unset( $wgTranslateTasks['export-as-file'] );
	unset( $wgTranslateTasks['optional'] );
	unset( $wgTranslateTasks['suggestions'] );

	$wgTranslateUsePreSaveTransform = true; # bug 37304

	$wgEnablePageTranslation = true;

	$wgTranslateBlacklist = array(
		'*' => array(
			'en' => 'English is the source language.',

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
		),
	);

	$wgTranslateEC = array();

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

	// Caused apache overload 2013-08-11 TS
	unset( $wgSpecialPages['SupportedLanguages'] );
}

if ( $wmgUseTranslationNotifications ) {
	require_once( "$IP/extensions/TranslationNotifications/TranslationNotifications.php" );
	$wgNotificationUsername = 'Translation Notification Bot';
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
	$wgVipsOptions = array(
		array(
			'conditions' => array(
				'mimeType' => 'image/png',
				'minArea' => 2e7,
			),
		),
	);

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

# Similar to above but not for single template/file changes
$wgJobBackoffThrottling = array(
	# Avoid excessive CPU due to cache misses from rapid invalidations
	'htmlCacheUpdate' => 15 // 15 pages/sec per runner
);

# If a job runner takes too long to finish a job, assume it died and re-assign the job
$wgJobTypeConf['default']['claimTTL'] = 3600;

# Job types to exclude from the default queue processing. Aka the very long
# one. That will exclude the types from any queries such as nextJobDB.php
# We have to set this for any project cause we usually run PHP script against
# the 'aawiki' database, but might as well run it against another name.

# Timed Media Handler:
$wgJobTypesExcludedFromDefaultQueue[] = 'webVideoTranscode';
$wgJobTypeConf['webVideoTranscode'] = array( 'claimTTL' => 86400 ) + $wgJobTypeConf['default'];

# GWToolset
$wgJobTypesExcludedFromDefaultQueue[] = 'gwtoolsetUploadMetadataJob';
$wgJobTypesExcludedFromDefaultQueue[] = 'gwtoolsetUploadMediafileJob';
$wgJobTypesExcludedFromDefaultQueue[] = 'gwtoolsetGWTFileBackendCleanupJob';

# Slow Parsoid jobs
$wgJobTypesExcludedFromDefaultQueue[] = 'ParsoidCacheUpdateJobOnDependencyChange';

if ( $wmgUseEducationProgram ) {
	require_once( "$IP/extensions/EducationProgram/EducationProgram.php" );
	$egEPSettings['dykCategory'] = $wmgEducationProgramDYKCat;
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

if ( $wmgEnableGeoData
	&& ( $wmgUseCirrus || $wmgUseCirrusAsAlternative ) )
{
	require_once( "$IP/extensions/GeoData/GeoData.php" );
	$wgGeoDataBackend = 'elastic';

	# Data collection mode
	if ( !$wmgEnableGeoSearch ) {
		$wgAPIListModules['geosearch'] = 'ApiQueryDisabled';
	}

	# These modules have been intentionally disabled for the first phase of deployment
	if ( $wgDBname !== 'testwiki' ) {
		unset( $wgAPIListModules['geopages'] );
		unset( $wgAPIListModules['geopagesincategory'] );
	}
	$wgMaxCoordinatesPerPage = 2000;
	$wgMaxGeoSearchRadius = $wmgMaxGeoSearchRadius;
	$wgGeoDataDebug = $wmgGeoDataDebug;
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

	// Eventlogging for Schema:Echo
	$wgEchoConfig['eventlogging']['Echo']['enabled'] = true;
	$wgEchoConfig['eventlogging']['Echo']['revision'] = 7731316;
	// Eventlogging for Schema:EchoMail
	$wgEchoConfig['eventlogging']['EchoMail']['enabled'] = true;
	$wgEchoConfig['eventlogging']['EchoMail']['revision'] = 5467650;
	// Eventlogging for Schema:EchoInteraction
	$wgEchoConfig['eventlogging']['EchoInteraction']['enabled'] = true;
	$wgEchoConfig['eventlogging']['EchoInteraction']['revision'] = 5782287;

	// Cohort study time
	$wgEchoCohortInterval = $wmgEchoCohortInterval;

	$wgEchoEnableEmailBatch = $wmgEchoEnableEmailBatch;
	$wgEchoEmailFooterAddress = $wmgEchoEmailFooterAddress;
	$wgEchoBundleEmailInterval = $wmgEchoBundleEmailInterval;
	$wgEchoHelpPage = $wmgEchoHelpPage;
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
}

if ( $wmgUseThanks ) {
	require_once( "$IP/extensions/Thanks/Thanks.php" );
}

if ( $wmgUseFlow ) {
	require_once( "$IP/extensions/Flow/Flow.php" );
	require_once( "$IP/extensions/Mantle/Mantle.php" ); // needed for Flow frontend-rewrite

	// Flow Parsoid - same values as $wgVisualEditorParsoid*, but separated for
	// those wikis where $wmgUseVisualEditor is false
	$wgFlowParsoidURL = 'http://10.2.2.29'; // parsoidcache.svc.eqiad.wmnet
	$wgFlowParsoidPrefix = $wgDBname;
	$wgFlowParsoidTimeout = 100;

	$wgFlowEditorList = $wmgFlowEditorList;
	$wgFlowOccupyPages = $wmgFlowOccupyPages;
	// Requires that Parsoid is available for all wikis using Flow.
	$wgFlowContentFormat = 'html';

	$wgFlowParsoidURL = $wmgParsoidURL;

	$wgFlowDefaultWikiDb = $wmgFlowDefaultWikiDb;
	$wgFlowCluster = $wmgFlowCluster;
	$wgFlowExternalStore = $wgDefaultExternalStore;
	$wgFlowMaintenanceMode = $wmgFlowMaintenanceMode;
}

if ( $wmgUseDisambiguator ) {
	require_once( "$IP/extensions/Disambiguator/Disambiguator.php" );
}

if ( $wmgUseCodeEditorForCore || $wmgUseScribunto || $wmgZeroPortal ) {
	include_once( "$IP/extensions/CodeEditor/CodeEditor.php" );
	$wgCodeEditorEnableCore = $wmgUseCodeEditorForCore;
}

if ( $wmgUseScribunto ) {
	include( "$IP/extensions/Scribunto/Scribunto.php" );
	$wgScribuntoUseGeSHi = true;
	$wgScribuntoUseCodeEditor = true;

	if ( $wmfRealm === 'labs' && defined( 'HHVM_VERSION' ) ) {
		$wgScribuntoDefaultEngine = 'luastandalone';
		$wgScribuntoEngineConf['luastandalone']['luaPath'] = '/usr/bin/lua5.1';
	} else {
		$wgScribuntoDefaultEngine = 'luasandbox';
		$wgScribuntoEngineConf['luasandbox']['cpuLimit'] = 10;
	}
}

if ( $wmgUseSubpageSortkey ) {
	include( "$IP/extensions/SubpageSortkey/SubpageSortkey.php" );
	$wgSubpageSortkeyByNamespace = $wmgSubpageSortkeyByNamespace;
}

if ( $wmgUseGettingStarted ) {
	require_once( "$IP/extensions/GettingStarted/GettingStarted.php" );
	if ( !empty( $sessionRedis[$wmfDatacenter] ) ) {
		$wgGettingStartedRedis = $sessionRedis[$wmfDatacenter][0];
		$wgGettingStartedRedisOptions['password'] = $wmgRedisPassword;
	}
	$wgGettingStartedCategoriesForTaskTypes = $wmgGettingStartedCategoriesForTaskTypes;
	$wgGettingStartedExcludedCategories = $wmgGettingStartedExcludedCategories;
	$wgGettingStartedRunTest = $wmgGettingStartedRunTest;
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
	$wgDefaultUserOptions['toc-floated'] = $wmgUseFloatedToc;
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
		$wgEventLoggingSchemaApiUri = 'http://test2.wikipedia.org/w/api.php';
		$wgEventLoggingBaseUri = "//{$wmfHostnames['bits']}/dummy.gif";
		$wgEventLoggingFile = "udp://$wmfUdp2logDest/EventLogging-$wgDBname";
	} else {
		// All other wikis reference metawiki.
		$wgEventLoggingBaseUri = "//{$wmfHostnames['bits']}/event.gif";
		$wgEventLoggingDBname = 'metawiki';
		$wgEventLoggingFile = 'udp://10.64.21.123:8421/EventLogging';  // vanadium
		$wgEventLoggingSchemaApiUri = 'http://meta.wikimedia.org/w/api.php';
	}
	if ( $wgEventLoggingDBname === $wgDBname ) {
		// Bug 45031
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
}

if ( $wmgUseEventLogging && $wmgUseNavigationTiming ) {
	include_once( "$IP/extensions/NavigationTiming/NavigationTiming.php" );
	// Careful! The LOWER the value, the MORE requests will be logged. A
	// sampling factor of 1 means log every request. This should not be
	// lowered without careful coordination with ops.
	$wgNavigationTimingSamplingFactor = 1000;
}

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

	// Remove after ULS is re-deployed
	if ( $wmgUniversalLanguageSelectorDefault ) {
		$wgDefaultUserOptions['uls-enable'] = 1;
	}

	// Fetch fonts from stable URLs so that they're cached longer. This is to avoid
	// re-downloading of fonts for each new branch. But that only works for production,
	// not labs. If this variable is not set, $wgExtensionAssetsPath is used.
	if ( $wmfRealm === 'production' ) {
		$wgULSFontRepositoryBasePath = ( "//{$wmfHostnames['bits']}/static-current"
			. '/extensions/UniversalLanguageSelector/data/fontrepo/fonts/' );
	}

	$wgULSEventLogging = $wmgULSEventLogging;

	// Enable the compact language links Beta Feature
	if ( $wmgULSCompactLinks ) {
		$wgULSCompactLinks = true;
	}
}

// @note getRealmSpecificFilename only works with filenames with .suffix
// needs to be listed outside of use wikibase check below, as localisation cache
// might be build as "aawikibooks" or something that does not have Wikibase.
$wgExtensionEntryPointListFiles[] = "$IP/extensions/Wikidata/extension-list-wikidata";

if ( $wmgUseWikibaseRepo || $wmgUseWikibaseClient ) {
	if ( $wmgUseWikibaseRepo && $wmfRealm === 'labs' ) {
		// enable on beta only
		$wmgUseWikibasePropertySuggester = true;
	}

	include( "$wmfConfigDir/Wikibase.php" );
}

// put this here to ensure it is available for localisation cache rebuild
$wgWBClientSettings['repoSiteName'] = 'wikibase-repo-name';

if ( $wmgUseTranslate && $wmgUseTranslationMemory ) {
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
	$wgPageImagesExpandOpenSearchXml = $wmgPageImagesExpandOpenSearchXml;
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

	// 2014-7-1 this line can be deleted once ZeroPortal goes into prod - redundant
	$wgJsonConfigs['JsonZeroConfig']['store'] = true;

	$wgGroupPermissions['zeroadmin']['zero-edit'] = true;
	$wgGroupPermissions['zeroadmin']['zero-script'] = true;
	$wgGroupPermissions['zeroadmin']['zero-script-ips'] = true;
	$wgGroupPermissions['zeroadmin']['jsonconfig-flush'] = true;
	$wgGroupPermissions['zeroscript']['zero-script'] = true;
	$wgGroupPermissions['zeroscript']['jsonconfig-flush'] = true;
	$wgGroupPermissions['zeroscriptips']['zero-script-ips'] = true;
	$wgGroupPermissions['zeroscriptips']['jsonconfig-flush'] = true;
}


if ( $wmgUseAccountAudit ) {
	require_once( "$IP/extensions/AccountAudit/AccountAudit.php" );
}

if ( $wmgUseOAuth ) {
	require_once( "$IP/extensions/OAuth/OAuth.php" );
	$wgMWOAuthCentralWiki = 'mediawikiwiki';
	$wgMWOAuthSharedUserSource = 'CentralAuth';
	$wgMWOAuthSecureTokenTransfer = true;
	$wgGroupPermissions['oauthadmin']['mwoauthmanageconsumer'] = true;
	$wgGroupPermissions['autoconfirmed']['mwoauthproposeconsumer'] = true;
	$wgGroupPermissions['autoconfirmed']['mwoauthupdateownconsumer'] = true;

	$wgHooks['OAuthReplaceMessage'][] = function( &$msgKey ) {
		if ( $msgKey === 'mwoauth-form-privacypolicy-link' ) {
			$msgKey = 'wikimedia-oauth-privacy-link';
		}
		return true;
	};

	// Grants for other extensions' permissions.
	// Note these have to be visible on all wikis, not just the ones the
	// extension is enabled on, for proper display in OAuth pages.
	$wgMWOAuthGrantPermissions['checkuser']['checkuser'] = true;
	$wgMWOAuthGrantPermissions['checkuser']['checkuser-log'] = true;

	// Categorize additional groups defined above.
	// Corresponding messages are mwoauth-grant-* in WikimediaMessages.
	$wgMWOAuthGrantPermissionGroups['checkuser'] = 'administration';

	// Rights needed to interact with wikibase
	$wgMWOAuthGrantPermissions['createeditmovepage']['item-create'] = true;
	$wgMWOAuthGrantPermissions['createeditmovepage']['property-create'] = true;
	$wgMWOAuthGrantPermissions['editpage']['item-term'] = true;
	$wgMWOAuthGrantPermissions['editpage']['item-merge'] = true;
	$wgMWOAuthGrantPermissions['editpage']['property-term'] = true;
}

### End (roughly) of general extensions ########################

$wgApplyIpBlocksToXff = $wmgUseXFFBlocks;

// On Special:Version, link to useful release notes
$wgHooks['SpecialVersionVersionUrl'][] = function( $wgVersion, &$versionUrl ) {
	$matches = array();
	preg_match( "/(\d+\.\d+)wmf(\d+)/", $wgVersion, $matches );
	$versionUrl = "https://www.mediawiki.org/wiki/MediaWiki_{$matches[1]}/wmf{$matches[2]}";
	return false;
};

// bug 44617
if ( in_array( $wgDBname, array( 'wikidatawiki', 'testwikidatawiki' ) ) ) {
	$wgHooks['SkinCopyrightFooter'][] = function( $title, $type, &$msg, &$link, &$forContent ) {
		if ( $title->getNamespace() === NS_MAIN ) {
			$msg = 'Creative Commons Public Domain 1.0';
			$link = '//creativecommons.org/publicdomain/zero/1.0/';
		}
		return true;
	};
}

// Bug 54847
if ( $wmgBug54847 && $wmgUseCentralAuth ) {
	require( "$wmfConfigDir/Bug54847.php" );
}

$wgExemptFromUserRobotsControl = array_merge( $wgContentNamespaces, $wmgExemptFromUserRobotsControlExtra );

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

if ( $wmgUseRC2UDP ) {
	if ( $wmgRC2UDPPrefix === false ) {
		$matches = null;
		if ( preg_match( '/^\/\/(.+).org$/', $wgServer, $matches ) && isset( $matches[1] ) ) {
			$wmgRC2UDPPrefix = "#{$matches[1]}\t";
		}
	}

	$wgRCFeeds['default'] = array(
		'formatter' => 'IRCColourfulRCFeedFormatter',
		'uri' => "udp://$wmgRC2UDPAddress:$wmgRC2UDPPort/$wmgRC2UDPPrefix",
		'add_interwiki_prefix' => false,
		'omit_bots' => false,
	);

	// RCStream / stream.wikimedia.org
	if ( $wmfRealm === 'production' ) {
		$wgRCFeeds['rcs1001'] = array(
			'uri'       => "redis://rcs1001.eqiad.wmnet:6379/rc.$wgDBname",
			'formatter' => 'JSONRCFeedFormatter',
		);

		$wgRCFeeds['rcs1002'] = array(
			'uri'       => "redis://rcs1002.eqiad.wmnet:6379/rc.$wgDBname",
			'formatter' => 'JSONRCFeedFormatter',
		);
	}
}

// Confirmed can do anything autoconfirmed can.
$wgGroupPermissions['confirmed'] = $wgGroupPermissions['autoconfirmed'];
$wgGroupPermissions['confirmed']['skipcaptcha'] = true;

$wgImgAuthDetails = true;

if ( file_exists( "$wmfConfigDir/extension-list-$wmfVersionNumber" ) ) {
	// Version specific extension-list files
	//
	// If a new extension is added only in one MediaWiki version,
	// it should go in a version specific file, and be moved back into
	// the versionless file when said version becomes the "main" version,
	// and as such, all deployed versions of MediaWiki have this extension.
	//
	// If something is to be removed from newer versions, it should go in a
	// version specific file for the older version. A symlink of this file may
	// be created if it needs to cover multiple versions.
	// This file can then be deleted once this version of MediaWiki isn't in
	// production usage.
	$wgExtensionEntryPointListFiles[] = "$wmfConfigDir/extension-list-$wmfVersionNumber";
}

# THIS MUST BE AFTER ALL EXTENSIONS ARE INCLUDED
#
# REALLY ... we're not kidding here ... NO EXTENSIONS AFTER

require( "$wmfConfigDir/ExtensionMessages-$wmfVersionNumber.php" );

wfProfileOut( "$fname-misc5" );
wfProfileOut( $fname );
