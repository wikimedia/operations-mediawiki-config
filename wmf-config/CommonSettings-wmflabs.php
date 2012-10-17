<?php

# WARNING: This file is publically viewable on the web.
# Do not put private data here.

# This file hold configuration statement overriding CommonSettings.php
# Should not be loaded on production

if( $cluster == 'wmflabs' ) {  # safe guard

	include( "logging-wmflabs.php" );

// test wiki
if ( $wgDBname == 'testwiki' ) {
	        $wgDebugToolbar = true;
	        $wgProfiler['class'] = 'ProfilerSimpleText';
}

if( file_exists( '/etc/wmflabs-instancename' ) ) {
	$wgOverrideHostname = trim( file_get_contents( '/etc/wmflabs-instancename' ) );
}

$wgDebugTimestamps=true;

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

$wgMaxShellMemory = 30000000;


// Disable AFTv4 completely on beta labs --Chris McMahon

$wgArticleFeedbackLotteryOdds = 0; // Will turn off the voting for AFTv4 on all pages

// Enabling AFTv5 globally for testing purposes -- Chris McMahon
// Basically copy-pasted from http://www.mediawiki.org/w/index.php?title=Extension:ArticleFeedbackv5

// enable site-wide on 100% on all namespaces defined in $wgArticleFeedbackv5Namespaces
$wgArticleFeedbackv5LotteryOdds = 100;
$wgArticleFeedbackv5TalkPageLink = true;
$wgArticleFeedbackv5WatchlistLink = true;

$wgArticleFeedbackv5DisplayBuckets = array(
        'buckets' => array(
                '0'  => 0, // display nothing
                '1'   => 0, // display 1-step feedback form
//              '2'   => 0, // abandoned
//              '3' => 0, // abandoned
                '4'  => 0, // display encouragement to edit page
//              '5'  => 0, // abandoned
                '6'   => 100, // display 2-step feedback form
        ),
        'version' => 6,
        'expires' => 30,
        'tracked' => false,
);

$wgArticleFeedbackv5LinkBuckets = array(
        'buckets' => array(
                'X' => 100,
                'A' => 0,
                'B' => 0,
                'C' => 0,
                'D' => 0,
                'E' => 0,
                'F' => 0,
                'G' => 0,
                'H' => 0,
        ),
        'version' => 5,
        'expires' => 30,
        'tracked' => false
);

$wgArticleFeedbackv5CTABuckets = array(
        'buckets' => array(
                '0' => 0, // display nothing
                '1' => 40, // display "Enticement to edit"
                '2' => 10, // display "Learn more"
                '3' => 0, // display "Take a survey"
                '4' => 20, // display "Sign up or login"
                '5' => 20, // display "View feedback"
                '6' => 10, // display "Visit Teahouse"
        ),
        'version' => 4,
        'expires' => 0,
        'tracked' => false,
);

$wgArticleFeedbackv5MaxCommentLength = 400;



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
} # end safeguard
