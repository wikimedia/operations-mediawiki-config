<?php

use Wikimedia\MWConfig\WmfConfig;

require_once __DIR__ . '/../multiversion/MWWikiversions.php';
require_once __DIR__ . '/../src/WmfConfig.php';
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../tests/data/MWDefines.php';
require_once __DIR__ . '/../tests/data/SiteConfiguration.php';

global $wmgRealm, $wmgDatacenter;

$wmgRealm = 'production';
$wmgDatacenter = 'eqiad';

$configDir = __DIR__ . '/../wmf-config';

require_once "{$configDir}/InitialiseSettings.php";
require_once "{$configDir}/InitialiseSettings-labs.php";

$realms = [];
$realms['production'] = WmfConfig::getStaticConfig( 'production' );
$realms['labs'] = WmfConfig::getStaticConfig( 'labs' );

foreach ( [ 'production', 'labs' ] as $realm ) {

	$wikiversionsFile = ( $realm === 'labs' ) ? 'wikiversions-labs.json' : 'wikiversions.json';

	$wikiversions = MWWikiversions::readWikiVersionsFile( $wikiversionsFile );

	$config = new SiteConfiguration();
	$config->suffixes = WmfConfig::SUFFIXES;
	$config->wikis = WmfConfig::readDbListFile( $realm === 'labs' ? 'all-labs' : 'all' );
	$config->settings = $realms[$realm];

	foreach ( $wikiversions as $wgDBname => $wmgVersionNumber ) {
		$globals = WmfConfig::getConfigGlobals( $wgDBname, $config, $realm );

		// Reduce noise in diff when config settings are re-ordered,
		// either in the same file or by moving them from a different file.
		ksort( $globals );
		$globals = json_encode( $globals, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );

		file_put_contents(
			__DIR__ . "/data/config-cache/conf-$realm-$wgDBname.json",
			$globals
		);
	}

}
