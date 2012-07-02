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

//before you remove this, log somewhere why you did it
//--Petrb
//Commented until a dedicated wiki is created.
//require_once ("$IP/extensions/OnlineStatusBar/OnlineStatusBar.php");

$wgMaxShellMemory = 30000000;

# temporary extensions
# ========================================================================

if ($wgDBname == "commonswiki" || $wgDBname == 'enwiki') {
	// requested by Eloquence
	include ("$IP/extensions/MwEmbedSupport/MwEmbedSupport.php");
	include ("$IP/extensions/TimedMediaHandler/TimedMediaHandler.php");

	$wgMwEmbedModuleConfig[ 'MediaWiki.ApiProviders' ] =  array(
	"commons" => array(
		'url' => '//commons.wikimedia.beta.wmflabs.org/w/api.php'
	));
}

# Transcoding
if ( file_exists( '/etc/wikimedia-transcoding' ) ) {
	# configuration file for video transcoding provided by TimedMediaHandler

	//transcoding environment needs more resources
	//to create video derivatives(TimedMediaHandler)
	$wgTranscodeBackgroundTimeLimit = 3600 * 4;
	$wgMaxShellMemory = 3000000;
	$wgMaxShellTime = 3600 * 4;
	$wgMaxShellFileSize = 100*102400; //1GB
}

} # end safeguard
