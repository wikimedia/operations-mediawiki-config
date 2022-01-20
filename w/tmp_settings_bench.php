<?php

/**
 * Benchmark SettingsBuilder YAML loading in production environment.
 * Temporary code that should be deleted after the benchmark is done.
 */

define( 'MW_ENTRY_POINT', 'tmp_settings_bench' );

require_once __DIR__ . '/../multiversion/MWMultiVersion.php';
require MWMultiVersion::getMediaWiki( 'includes/WebStart.php' );

global $IP;
$config = MediaWiki\MediaWikiServices::getInstance()->getMainConfig();
$stats = MediaWiki\MediaWikiServices::getInstance()->getStatsdDataFactory();

// 1. Benchmark loading YAML with SettingsBuilder.php.
if ( PHP_SAPI === 'cli' || !function_exists( 'apcu_fetch' ) ) {
	$cacheType = 'hash';
	$localCache = new HashBagOStuff;
} else {
	$cacheType = 'apcu';
	$localCache = new APCUBagOStuff;
}

$phonySettings = new MediaWiki\Settings\SettingsBuilder(
	$IP,
	ExtensionRegistry::getInstance(),
	new MediaWiki\Settings\Config\ArrayConfigBuilder(),
	new MediaWiki\Settings\Config\PhpIniSink(),
	$localCache
);

$start = microtime( true );
try {
	$phonySettings
		->loadFile( 'includes/config-schema.yaml' )
		->apply();
	$timing = 1000 * max( 0, microtime( true ) - $start );
	echo "YAML loaded in $timing\n";
	$stats->timing( "tmp_settings_load.${cacheType}_success", $timing );
} catch ( Throwable $e ) {
	MediaWiki\Logger\LoggerFactory::getInstance( 'SettingsBuilder' )
		->warning(
			'Failed to load config schema',
			[ 'exception' => $e, ]
		);
	$timing = 1000 * max( 0, microtime( true ) - $start );
	echo "YAML errored in $timing\n";
	$stats->timing( "tmp_settings_load.${cacheType}_failed", $timing );
}

// 2. Benchmark reloading settings from DefaultSettings.php
( static function () use ( $IP, $stats ) {
	$start = microtime( true );
	require "$IP/includes/DefaultSettings.php";
	$timing = 1000 * max( 0, microtime( true ) - $start );
	echo "PHP loaded in $timing\n";
	$stats->timing( "tmp_settings_load.old", $timing );
} )();

MediaWiki::emitBufferedStatsdData( $stats, $config );

echo "^._.^\n";
