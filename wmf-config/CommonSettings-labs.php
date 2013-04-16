<?php

# WARNING: This file is publically viewable on the web.
# Do not put private data here.

# This file hold configuration statement overriding CommonSettings.php
# Should not be loaded on production

if( $wmfRealm == 'labs' ) {  # safe guard

	include( "logging-labs.php" );

// test wiki
if ( $wgDBname == 'testwiki' ) {
	        $wgDebugToolbar = true;
	        $wgProfiler['class'] = 'ProfilerSimpleText';
}

if( file_exists( '/etc/wmflabs-instancename' ) ) {
	$wgOverrideHostname = trim( file_get_contents( '/etc/wmflabs-instancename' ) );
}

$wgDebugTimestamps=true;

$wmgAddWikiNotify = false;

# see r110254 and bug 33746
$wgPreloadJavaScriptMwUtil = true;

if ( $wmfUseArticleCreationWorkflow ) {
	require_once( "$IP/extensions/ArticleCreationWorkflow/ArticleCreationWorkflow.php" );
	$wgArticleCreationBucketConfig['buckets']['off'] = 0;
}

# Attempt to auto block users using faulty servers
# See also http://www.us.sorbs.net/general/using.shtml
$wgEnableDnsBlacklist = true;
$wgDnsBlacklistUrls   = array(
	'proxies.dnsbl.sorbs.net.',
);

//before you remove this, log somewhere why you did it
//--Petrb
//Commented until a dedicated wiki is created.
//require_once ("$IP/extensions/OnlineStatusBar/OnlineStatusBar.php");

// So that people can easily test the captchas without making accounts -- Platonides
$wgGroupPermissions['autoconfirmed']['skipcaptcha'] = false;


# temporary extensions
# ========================================================================

if ( $wmgUseTimedMediaHandler ) {
	$wgMwEmbedModuleConfig[ 'MediaWiki.ApiProviders' ] =  array(
	"commons" => array(
		'url' => '//commons.wikimedia.beta.wmflabs.org/w/api.php'
	));
	$wgEnableTranscode = true; //enable transcoding on labs
}

if ($wgDBname == "testwiki") {
	$wgCaptchaDirectory = '/mnt/upload7/private/captcha/random';
}

// Use InstantCommons for testing
if ( $wgDBname == "enwikivoyage" || $wgDBname == "dewikivoyage" ) {
	$wgUseInstantCommons = true;
}

# Backends:
if( $wmgUseMath ) {
	$wgMathFileBackend = false;
}

} # end safeguard
