<?php
// We don't want direct calls.
require_once __DIR__ . '/../../../src/Noc/utils.php';

require_once __DIR__ . '/../../../src/Noc/ConfigFile.php';

/**
 * Compute the allowed url -> file path
 * @return \Wikimedia\MWConfig\Noc\ConfigFile
 */
function wmfLoadRoutes() {
	/**
	 * Add here file that you want to show in the interface of noc.wikimedia.org
	 */
	// files we want to serve from basename($file).txt
	$nocConfigFilesTxt = [
		'wmf-config/CommonSettings-labs.php',
		'wmf-config/CommonSettings.php',
		'wmf-config/InitialiseSettings-labs.php',
		'wmf-config/InitialiseSettings.php',
		'wmf-config/ProductionServices.php',
		'wmf-config/PoolCounterSettings.php',
		'wmf-config/abusefilter.php',
		'wmf-config/CirrusSearch-common.php',
		'wmf-config/CirrusSearch-labs.php',
		'wmf-config/CirrusSearch-production.php',
		'wmf-config/core-Namespaces.php',
		'wmf-config/core-Permissions.php',
		'wmf-config/db-production.php',
		'wmf-config/db-sections.php',
		'wmf-config/db-labs.php',
		'wmf-config/EnWikiContactPages.php',
		'wmf-config/ext-Babel.php',
		'wmf-config/ext-CirrusSearch.php',
		'wmf-config/ext-EventLogging.php',
		'wmf-config/ext-EventStreamConfig.php',
		'wmf-config/ext-GrowthExperiments.php',
		'wmf-config/ext-ORES.php',
		'wmf-config/FeaturedFeedsWMF.php',
		'wmf-config/filebackend.php',
		'wmf-config/flaggedrevs.php',
		'wmf-config/import.php',
		'wmf-config/interwiki.php',
		'wmf-config/interwiki-labs.php',
		'wmf-config/MetaContactPages.php',
		'wmf-config/LabsServices.php',
		'wmf-config/liquidthreads.php',
		'wmf-config/logging.php',
		'wmf-config/logos.php',
		'wmf-config/mc.php',
		'wmf-config/mc-labs.php',
		'wmf-config/throttle.php',
		'wmf-config/throttle-analyze.php',
		'wmf-config/reverse-proxy.php',
		'wmf-config/reverse-proxy-labs.php',
		'wmf-config/Wikibase.php',
		'wmf-config/ZhWikiContactPages.php',
	];

	// Create non-txt symlink from mediawiki-config
	// raw views should use txt for consistent behaviour in browsers
	// (not triggering a download instead of a view, and rendering as plain text).
	// Entries here can either be just paths relative to the root of the repository
	// or arrays of path => url it we want some specific mapping.
	$nocConfigFilesPlain = [
		'debug.json',
		'fc-list',
		'langlist',
		'langlist-labs',
		'wikiversions.json',
		'wikiversions-labs.json',
		'wmf-config/extension-list',
		[ 'logos/config.yaml' => 'logos-config.yaml' ],
	];

	// directories we want to serve the contents of as text files
	$nocConfigDirs = [ 'dblists' ];

	$fileserv = new \Wikimedia\MWConfig\Noc\ConfigFile( $nocConfigFilesTxt, $nocConfigFilesPlain, $nocConfigDirs );
	return $fileserv;
}
