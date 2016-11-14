<?php

# WARNING: This file is publiccally viewable on the web.
# Do not put private data here.

wfLoadExtension( 'timeline' );
$wgTimelineEpochTimestamp = '20130601000000';

if ( $wgDBname === 'testwiki' || $wgDBname === 'mlwiki' ) {
	// FreeSansWMF has been generated from FreeSans and FreeSerif by using this script with fontforge:
	// Open("FreeSans.ttf");
	// MergeFonts("FreeSerif.ttf");
	// SetFontNames("FreeSans-WMF", "FreeSans WMF", "FreeSans WMF Regular", "Regular", "");
	// Generate("FreeSansWMF.ttf", "", 4 );
	$wgTimelineEpochTimestamp = '20161201000000';
	$wgTimelineFontFile = 'FreeSansWMF';
} elseif ( $lang == 'zh' ) {
	$wgTimelineEpochTimestamp = '20161201000000';
	$wgTimelineFontFile = 'unifont-5.1.20080907';
}
$wgTimelineFileBackend = 'local-multiwrite';
