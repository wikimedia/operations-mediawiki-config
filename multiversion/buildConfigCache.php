<?php

require_once __DIR__ . '/MWConfigCacheGenerator.php';
require_once __DIR__ . '/MWWikiversions.php';

require_once __DIR__ . '../../vendor/autoload.php';
require_once __DIR__ . "../../src/defines.php";

$wmfConfigDir = __DIR__ . '/../wmf-config';

global $wmfRealm, $wmfDatacenter;

$wmfRealm = 'production';
$wmfDatacenter = 'eqiad';

require_once "{$wmfConfigDir}/InitialiseSettings.php";
$settings['production'] = wmfGetVariantSettings();

require_once "{$wmfConfigDir}/InitialiseSettings-labs.php";
$settings['labs'] = wmfApplyLabsOverrideSettings( $settings['production'] );

foreach ( [ 'production', 'labs' ] as $realm ) {
	$config = $settings[$realm];

	$wikiversionsFile = ( $realm === 'labs' ) ? 'wikiversions-labs.json' : 'wikiversions.json';

	$wikiversions = MWWikiversions::readWikiVersionsFile( $wikiversionsFile );

	foreach ( $wikiversions as $wgDBname => $wmgVersionNumber ) {

		$megaConfig[$realm][$wgDBname] = Wikimedia\MWConfig\MWConfigCacheGenerator::getCachableMWConfig(
			$wgDBname, $config, $realm
		);

		Wikimedia\MWConfig\MWConfigCacheGenerator::writeToStaticCache(
			$wmfConfigDir . "/config-cache",
			"conf-$realm-$wgDBname.json",
			$megaConfig[$realm][$wgDBname]
		);
	}

}
