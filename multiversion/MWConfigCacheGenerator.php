<?php

namespace Wikimedia\MWConfig;

use MWMultiVersion;
use SiteConfiguration;

require_once __DIR__ . '/MWMultiVersion.php';

/**
 * Wrapper for config caching code.
 */
class MWConfigCacheGenerator {

	/**
	 * Compute the config globals.
	 *
	 * @param string $dbName The wiki's database name, e.g. 'enwiki' or 'zh_min_nanwikisource'
	 * @param SiteConfiguration $siteConfiguration The global MultiVersion wgConf object
	 * @param string $realm Realm, e.g. 'production' or 'labs'
	 * @return array The wiki's config
	 */
	public static function getMWConfigForCacheing( $dbName, $siteConfiguration, $realm = 'production' ) {
		list( $site, $lang ) = $siteConfiguration->siteFromDB( $dbName );
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
	 * Compute the config globals for a wiki in a standalone way for testing.
	 *
	 * In production code, use getConfigGlobals() instead.
	 *
	 * This method will load InitialiseSettings (which requires Defines.php) and create
	 * a SiteConfiguration object (which requires SiteConfiguration.php). It is the responsibility
	 * of the caller to ensure those are loaded (either from MW or standalone from /tests/data).
	 *
	 * @param string $wikiDBname The wiki's database name, e.g. 'enwiki' or 'zh_min_nanwikisource'
	 * @param array $config A 2D array of setting -> wiki -> values
	 * @param string $realm Realm, e.g. 'production' or 'labs'
	 * @return array The wiki's config array
	 */
	public static function getCachableMWConfig( $wikiDBname, $config, $realm = 'production' ) {
		$conf = new SiteConfiguration();
		$conf->suffixes = MWMultiVersion::SUFFIXES;
		$conf->settings = $config;

		list( $site, $lang ) = $conf->siteFromDB( $wikiDBname );
		$dbSuffix = ( $site === 'wikipedia' ) ? 'wiki' : $site;
		$confParams = [
			'lang' => $lang,
			'site' => $site,
		];

		// Re-write dynamic values for $site and $lang to be static.
		foreach ( $conf->settings as $setting => $valueArray ) {
			foreach ( $valueArray as $selector => $value ) {
				if ( is_string( $value ) ) {
					if ( $site && strpos( $value, '$site' ) !== false ) {
						$value = str_replace( '$site', "$site", $value );
					}

					if ( $lang && strpos( $value, '$lang' ) !== false ) {
						$value = str_replace( '$lang', "$lang", $value );
					}

					if ( $value !== $conf->settings[ $setting ][ $selector ] ) {
						$conf->settings[ $setting ][ $selector ] = $value;
					}
				}
			}
		}

		// Collect all the dblist tags associated with this wiki
		// Add a per-language tag as well
		$wikiTags = MWMultiversion::getTagsForWiki( $wikiDBname, $realm );
		$wikiTags[] = $conf->get( 'wgLanguageCode', $wikiDBname, $dbSuffix, $confParams, $wikiTags );

		$settings = $conf->getAll( $wikiDBname, $dbSuffix, $confParams, $wikiTags );

		$instance = self::getInstance();
		$expandConfigResults = $instance->expandConfig( $wikiDBname );
		$settings = array_merge( $expandConfigResults, $settings );

		ksort( $settings );
		return $settings;
	}

	private function loadConfig() {
		$configFileDir = __DIR__ . '/../wmf-config/config/';
		$configFiles = scandir( $configFileDir );

		foreach ( $configFiles as $key => $filename ) {
			if ( substr( $filename, -5 ) === '.yaml' ) {
				$name = substr( $filename, 0, -5 );

				if ( !isset( $this->staticConfigs[ $name ] ) ) {
					$file = @file_get_contents( $configFileDir . $filename );
					// If the inheritance object is "blank" (e.g. only comments), fall back.
					$result = \Symfony\Component\Yaml\Yaml::parse( $file ) ?? [];
					$this->staticConfigs[ $name ] = $result;
				}
			}
		}
	}

	/**
	 * @param string $name name of the configuration source to handle
	 * @return array array of configuration sources to use, the current one and the sources
	 *   it inherits from
	 */
	private function getInheritanceTree( $name ) {
		if ( !array_key_exists( $name, $this->staticConfigs ) ) {
			throw new \Exception( "Couldn't find config file for '$name'." );
		}

		if ( !isset( $this->staticConfigs[ $name ]['inheritsFrom'] ) ) {
			return [ $name ];
		}

		$dependencies = $this->staticConfigs[ $name ]['inheritsFrom'];

		if ( is_string( $dependencies ) ) {
			return array_merge( [ $name ], $this->getInheritanceTree( $dependencies ) );
		}

		if ( is_array( $dependencies ) ) {

			$ret = [ $name ];

			for ( $i = count( $dependencies ) - 1; $i > -1; $i-- ) {
				$ret = array_merge( $ret, $this->getInheritanceTree( $dependencies[$i] ) );
			}

			return $ret;
		}

		throw new \Exception( "Bad 'inheritsFrom' value for '$name'." );
	}

	/**
	 * @param string $name name of the configuration source to handle
	 * @return array expanded configuration with inherited sources applied
	 */
	private function expandConfig( $name ) {
		$this->loadConfig( $name );

		$inheritanceStack = $this->getInheritanceTree( $name );

		$result = [];
		$tags = [];

		foreach ( $inheritanceStack as $config ) {
			$result = array_merge( $this->staticConfigs[ $config ], $result );

			$localTags = $this->staticConfigs[ $config ]['wikiTag'] ?? [];
			if ( isset( $localTags ) ) {
				if ( is_string( $localTags ) ) {
					$tags[] = $localTags;
					continue;
				}
				if ( is_array( $localTags ) ) {
					$tags = array_merge( $localTags, $tags );
					continue;
				}

				// Neither a string nor a tag.
				throw new \Exception( "Bad 'wikiTag' value for '$name'." );
			}
		}

		if ( !array_search( 'all', $inheritanceStack ) ) {
			throw new \Exception( "The '$name' configuration must inherit from 'all'." );
		}

		// Over-ride all values to those set by the 'all' config
		$result = array_merge( $this->staticConfigs[ 'all' ], $result );
		$result['wikiTag'] = $tags;

		// Don't dirty the config files with inheritance build information.
		unset( $result['inheritsFrom'] );

		return $result;
	}

	/**
	 * @var MWConfigCacheGenerator
	 */
	private static $instance;

	/**
	 * @return MWConfigCacheGenerator
	 */
	public static function getInstance() {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * @var array loaded static configuration
	 */
	private $staticConfigs = [];

	/**
	 * @param string $dbname Database name, e.g. 'enwiki' or 'zh_min_nanwikisource'
	 * @param \SiteConfiguration $siteConfiguration The wgConf object
	 * @param string $realm Realm, e.g. 'production' or 'labs'
	 * @return array
	 */
	public static function getConfigGlobals(
		string $dbname,
		\SiteConfiguration $siteConfiguration,
		string $realm = 'production'
	): array {
		return self::getMWConfigForCacheing(
			$dbname,
			$siteConfiguration,
			$realm
		);
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
			( require $configDir . '/ext-ORES.php' ) +
			( require $configDir . '/ext-CirrusSearch.php' );

		if ( $realm !== 'production' ) {
			// Override for Beta Cluster and other realms.
			// Ref: InitialiseSettings-labs.php
			require_once $configDir . "/InitialiseSettings-$realm.php";
			$settings = self::applyOverrides( $settings );
		}

		return $settings;
	}

}
