<?php

define( 'MW_PHPUNIT_TEST', true );

// Load the shared utilities classes from here!
require_once __DIR__ . '/../multiversion/MWWikiversions.php';
require_once __DIR__ . '/../multiversion/MWMultiVersion.php';
require_once __DIR__ . '/../multiversion/MWConfigCacheGenerator.php';
require_once __DIR__ . '/data/MWDefines.php';
require_once __DIR__ . '/data/SiteConfiguration.php';
require_once __DIR__ . '/../src/ClusterConfig.php';
require_once __DIR__ . '/../src/Noc/EtcdCachedConfig.php';
require_once __DIR__ . '/../src/Noc/ConfigFile.php';
require_once __DIR__ . '/../src/XWikimediaDebug.php';
require_once __DIR__ . '/../wmf-config/InitialiseSettings.php';
require_once __DIR__ . '/../wmf-config/InitialiseSettings-labs.php';
require_once __DIR__ . '/DBList.php';
require_once __DIR__ . '/WgConfTestCase.php';
