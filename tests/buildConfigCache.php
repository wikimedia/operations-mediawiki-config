<?php

/*
require_once 'multiversion/MWConfigCacheGenerator.php';
require_once 'multiversion/MWWikiversions.php';
require_once 'wmf-config/wgConf.php';*/

$wmfConfigDir = "wmf-config";

// $wikiversions = MWWikiversions::readWikiVersionsFile( '../wikiversions.json' );

$wikis = [ "aawiki" => "php-1.34.0-wmf.1", "aawikibooks" => "php-1.34.0-wmf.3" ];

foreach ($wikis as $wgDBname => $wmgVersionNumber) {

	$_SERVER['SERVER_NAME'] = 'aa.wikipedia.org';
	$_SERVER['REQUEST_ADDR'] = 'heya';
	$_SERVER['HTTP_X_FORWARDED_FOR'] = 'wow';
	$_SERVER['REQUEST_URI'] = 'gosh';

	require 'w/index.php';

	// MEDIAWIKI_DEPLOYMENT_DIR


	$cacheFilename = "conf-$wgDBname";
	if ( defined( 'HHVM_VERSION' ) ) {
		$cacheFilename .= '-hhvm';
	}

	MWConfigCacheGenerator::writeToStaticCache(
		$wmfConfigDir . '/config-cache/' . '$wmgVersionNumber',
		$cacheFilename . '.json',
		MWConfigCacheGenerator::getMWConfigForCacheing( $wgDBname, $wgConf )
	);
}
