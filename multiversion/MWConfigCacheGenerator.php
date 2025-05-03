<?php

namespace Wikimedia\MWConfig;

use MWMultiVersion;
use SiteConfiguration;

require_once __DIR__ . '/MWMultiVersion.php';

/**
 * Wrapper for loading and interpreting wmf-config files.
 */
class MWConfigCacheGenerator {

	/**
	 * Compute a wiki's config globals.
	 *
	 * This is called on every request in wmf-config/CommonSettings.php
	 *
	 * @param string $dbName Database name, e.g. 'enwiki' or 'zh_min_nanwikisource'
	 * @param SiteConfiguration $siteConfiguration The wgConf object
	 * @param string $realm Realm, e.g. 'production' or 'labs'
	 * @return array
	 */
	public static function getConfigGlobals(
		string $dbName,
		SiteConfiguration $siteConfiguration,
		string $realm = 'production'
	): array {
		[ $site, $lang ] = $siteConfiguration->siteFromDB( $dbName );
		$dbSuffix = ( $site === 'wikipedia' ) ? 'wiki' : $site;
		$confParams = [
			'lang' => $lang,
			'site' => $site,
		];

		// Collect all the dblist tags associated with this wiki
		// Add a per-language tag as well
		$wikiTags = MWMultiversion::getTagsForWiki( $dbName, $realm );
		$wikiTags[] = $siteConfiguration->get( 'wgLanguageCode', $dbName, $dbSuffix, $confParams, $wikiTags );

		return $siteConfiguration->getAll( $dbName, $dbSuffix, $confParams, $wikiTags );
	}

	/**
	 * Override or add site settings as needed for non-production realms.
	 *
	 * This depends on 'wmfGetOverrideSettings' having been declared by a
	 * `InitialiseSettings-*.php` file.
	 *
	 * TODO: Once InitialiseSettings is array-returning, this can be injected
	 * by callers instead.
	 *
	 * @param array[] $settings wgConf-style settings array from IntialiseSettings.php
	 * @return array
	 */
	private static function applyOverrides( array $settings ): array {
		$overrides = wmfGetOverrideSettings();
		foreach ( $overrides as $key => $value ) {
			if ( substr( $key, 0, 1 ) == '-' ) {
				// Settings prefixed with - are completely overriden
				$settings[substr( $key, 1 )] = $value;
			} elseif ( isset( $settings[$key] ) ) {
				$settings[$key] = array_merge( $settings[$key], $value );
			} else {
				$settings[$key] = $value;
			}
		}

		return $settings;
	}

	/**
	 * Return static configuration
	 *
	 * @param string $realm Realm, e.g. 'production' or 'labs'
	 * @return array
	 */
	public static function getStaticConfig( string $realm = 'production' ): array {
		$configDir = __DIR__ . '/../wmf-config';
		// Use of direct addition instead of for loop in array is
		// intentional and done for performance reasons.
		$settings =
			( require $configDir . '/logos.php' ) +
			( require $configDir . '/InitialiseSettings.php' ) +
			( require $configDir . '/core-Namespaces.php' ) +
			( require $configDir . '/core-Permissions.php' ) +
			( require $configDir . '/ext-ORES.php' ) +
			( require $configDir . '/ext-CirrusSearch.php' ) +
			( require $configDir . '/ext-EventLogging.php' ) +
			( require $configDir . '/ext-EventStreamConfig.php' ) +
			( require $configDir . '/ext-GrowthExperiments.php' ) +
			( require $configDir . '/skin-Minerva.php' );

		if ( $realm !== 'production' ) {
			// Override for Beta Cluster and other realms.
			// Ref: InitialiseSettings-labs.php
			require_once $configDir . "/InitialiseSettings-$realm.php";
			$settings = self::applyOverrides( $settings );
		}

		return $settings;
	}

}
