<?php

# WARNING: This file is publically viewable on the web.
# # Do not put private data here.

// Reuse most of production settings
require_once( __DIR__ . '/mobile.php' );

if ( $wmgMobileFrontend ) {
	if ( $wmgZeroBanner ) {
		$wgZeroBannerClusterDomain = 'beta.wmflabs.org'; // need a better way to calc this
		if ( !$wmgZeroPortal ) {
			$wgJsonConfigs['JsonZeroConfig']['remote']['url'] = 'http://zero.wikimedia.beta.wmflabs.org/w/api.php';
		}
	}

	if ( $wmgUseGather ) {
		require_once "$IP/extensions/Gather/Gather.php";
	}
}

$wgMFForceSecureLogin = false;
$wgMFUseCentralAuthToken = $wmgMFUseCentralAuthToken;
$wgMFSpecialCaseMainPage = $wmgMFSpecialCaseMainPage;

$wgMFWikiDataEndpoint = $wmgMFWikiDataEndpoint;
$wgWikiBasePropertyConfig = $wmgWikiBasePropertyConfig;
$wgMFInfoboxConfig = $wmgMFInfoboxConfig;

$wgMFIsBrowseEnabled = true;
$wgMFBrowseTags = array(
	"Category:National_Basketball_Association_All-Stars" => "NBA All Stars",
	"Category:20th-century_American_politicians" => "American politicians",
	"Category:Object-oriented_programming_languages" => "object-oriented programming languages",
	"Category:Western_Europe" => "European states",
	"Category:American_female_pop_singers" => "American female pop singers",
	"Category:American_drama_television_series" => "American drama TV series",
	"Category:Modern_painters" => "modern painters",
	"Category:Landmarks_in_San_Francisco,_California" => "landmarks in San Francisco, California",
);
