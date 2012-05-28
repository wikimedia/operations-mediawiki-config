<?php

# WARNING: This file is publically viewable on the web.
# Do not put private data here.

# This file hold configuration statement overriding CommonSettings.php
# Should not be loaded on production

if( $cluster == 'wmflabs' ) {  # safe guard

// test wiki
if ( $wgDBname == 'testwiki' ) {
	        $wgDebugToolbar = true;
	        $wgProfiler['class'] = 'ProfilerSimpleText';
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

} # end safeguard
