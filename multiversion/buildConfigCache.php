<?php

use Wikimedia\MWConfig\MWConfigCacheGenerator;

require_once __DIR__ . '/MWConfigCacheGenerator.php';
require_once __DIR__ . '/MWWikiversions.php';
require_once __DIR__ . '../../vendor/autoload.php';
require_once __DIR__ . "../../src/defines.php";

global $wmgRealm, $wmgDatacenter;

$wmgRealm = 'production';
$wmgDatacenter = 'eqiad';

$configDir = __DIR__ . '/../wmf-config';

require_once "{$configDir}/InitialiseSettings.php";
require_once "{$configDir}/InitialiseSettings-labs.php";

$settings['production'] = MWConfigCacheGenerator::getStaticConfig();
$settings['labs'] = MWConfigCacheGenerator::applyOverrides( $settings['production'] );

foreach ( [ 'production', 'labs' ] as $realm ) {
	$config = $settings[$realm];

	$wikiversionsFile = ( $realm === 'labs' ) ? 'wikiversions-labs.json' : 'wikiversions.json';

	$wikiversions = MWWikiversions::readWikiVersionsFile( $wikiversionsFile );

	foreach ( $wikiversions as $wgDBname => $wmgVersionNumber ) {

		$cachableConfig = MWConfigCacheGenerator::getCachableMWConfig(
			$wgDBname, $config, $realm
		);

		MWConfigCacheGenerator::writeToStaticCache(
			$configDir . "/config-cache",
			"conf-$realm-$wgDBname.json",
			$cachableConfig
		);
	}

}
