<?php

namespace Wikimedia\MWConfig;

use MWMultiVersion;
use MWWikiversions;

require_once __DIR__ . '/MWMultiVersion.php';
require_once __DIR__ . '/MWWikiversions.php';
require_once __DIR__ . '/../src/StaticSiteConfiguration.php';

/**
 * Wrapper for config caching code.
 */
class MWConfigCacheGenerator {
	# When updating list please run ./docroot/noc/createTxtFileSymlinks.sh
	# Expand computed dblists with ./multiversion/bin/expanddblist

	// Note that most wiki families are available as tags for free without
	// needing a dblist to be maintained and read from disk, because their
	// dbname suffix (as mapped in MWMultiVersion::SUFFIXES) already makes them
	// available as tag for InitialiseSettings.php.
	public static $dbLists = [
		'wikipedia',
		'special',
		'private',
		'fishbowl',
		'closed',
		'desktop-improvements',
		'flow',
		'flaggedrevs',
		'small',
		'medium',
		'large',
		'wikimania',
		'wikidata',
		'wikibaserepo',
		'wikidataclient',
		'wikidataclient-test',
		'visualeditor-nondefault',
		'commonsuploads',
		'lockeddown',
		'group0',
		'group1',
		'nonglobal',
		'wikitech',
		'nonecho',
		'mobile-anon-talk',
		'nowikidatadescriptiontaglines',
		'cirrussearch-big-indices',
	];

	private static $labsDbLists = [
		'closed' => 'closed-labs',
		'flow' => 'flow-labs',
	];

	/**
	 * Create a MultiVersion config object for a wiki
	 *
	 * @param string $wikiDBname The wiki's database name, e.g. 'enwiki' or  'zh_min_nanwikisource'
	 * @param object $site The wiki's site type family, e.g. 'wikipedia' or 'wikisource'
	 * @param object $lang The wiki's MediaWiki language code, e.g. 'en' or 'zh-min-nan'
	 * @param object $wgConf The global MultiVersion wgConf object
	 * @param string $realm Realm, e.g. 'production' or 'labs'
	 * @return object The wiki's config object
	 */
	public static function getMWConfigForCacheing( $wikiDBname, $site, $lang, $wgConf, $realm = 'production' ) {
		# Collect all the dblist tags associated with this wiki
		$wikiTags = [];

		$dbLists = array_combine( self::$dbLists, self::$dbLists );
		if ( $realm === 'labs' ) {
			// Replace some lists with labs-specific versions
			$dbLists = array_merge( $dbLists, self::$labsDbLists );
		}
		foreach ( $dbLists as $tag => $fileName ) {
			$dblist = MWWikiversions::readDbListFile( $fileName );
			if ( in_array( $wikiDBname, $dblist ) ) {
				$wikiTags[] = $tag;
			}
		}

		$dbSuffix = ( $site === 'wikipedia' ) ? 'wiki' : $site;
		$confParams = [
			'lang' => $lang,
			'site' => $site,
		];

		// Add a per-language tag as well
		$wikiTags[] = $wgConf->get( 'wgLanguageCode', $wikiDBname, $dbSuffix, $confParams, $wikiTags );
		$globals = $wgConf->getAll( $wikiDBname, $dbSuffix, $confParams, $wikiTags );

		return $globals;
	}

	/**
	 * Create a MultiVersion config object for a wiki
	 *
	 * @param string $wikiDBname The wiki's database name, e.g. 'enwiki' or  'zh_min_nanwikisource'
	 * @param array $config A 2D array of setting -> wiki -> values
	 * @param string $realm Realm, e.g. 'production' or 'labs'
	 * @return array The wiki's config array
	 */
	public static function getCachableMWConfig( $wikiDBname, $config, $realm = 'production' ) {
		# Collect all the dblist tags associated with this wiki
		$wikiTags = [];

		$dbLists = array_combine( self::$dbLists, self::$dbLists );
		if ( $realm === 'labs' ) {
			// Replace some lists with labs-specific versions
			$dbLists = array_merge( $dbLists, self::$labsDbLists );

			require_once __DIR__ . "../../src/defines.php";
			require_once __DIR__ . "../../wmf-config/InitialiseSettings-labs.php";
			$config = wmfApplyOverrideSettings( $config );
		}

		foreach ( $dbLists as $tag => $fileName ) {
			$dblist = MWWikiversions::readDbListFile( $fileName );

			if ( in_array( $wikiDBname, $dblist ) ) {
				$wikiTags[] = $tag;
			}
		}

		$conf = new StaticSiteConfiguration();
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

		// Add a per-language tag as well
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

	private $staticConfigs = [];

	/**
	 * Read a static cached MultiVersion object from disc
	 *
	 * @param string $confCacheFile The full filepath for the wiki's cached config object
	 * @param string $confActualMtime The expected mtime for the cached config object
	 * @return object|null The wiki's config object, or null if not yet cached or stale
	 */
	public static function readFromStaticCache( $confCacheFile, $confActualMtime ) {
		// Ignore file warnings (file may be inaccessible, or deleted in a race)
		$cacheRecord = @file_get_contents( $confCacheFile );

		if ( $cacheRecord !== false ) {
			// TODO: Use JSON_THROW_ON_ERROR with a try/catch once production is running PHP 7.3.
			$staticCacheObject = json_decode( $cacheRecord, /* assoc */ true );

			if ( json_last_error() === JSON_ERROR_NONE ) {
				// Ignore non-array and array offset warnings (file may be in an older format)
				if ( ( $staticCacheObject['mtime'] ?? null ) === $confActualMtime ) {
					return $staticCacheObject['globals'];
				}
			} else {
				// Something went wrong; raise an error
				trigger_error( "Config cache failure: Static decoding failed", E_USER_ERROR );
			}
		}

		// Reached if the file doesn't exist yet, can't be read, was out of date, or was corrupt.
		return null;
	}

	/**
	 * Write a static MultiVersion object to disc cache
	 *
	 * @param string $cacheDir The filepath for cached multiversion config storage
	 * @param string $cacheShard The filename for the cached multiversion config object
	 * @param object $configObject The config object for this wiki
	 */
	public static function writeToStaticCache( $cacheDir, $cacheShard, $configObject ) {
		@mkdir( $cacheDir );
		$tmpFile = tempnam( '/tmp/', $cacheShard );

		$staticCacheObject = json_encode(
			$configObject,
			// TODO: Use JSON_THROW_ON_ERROR with a try/catch once production is running PHP 7.3.
			JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
		) . "\n";

		if ( $tmpFile ) {
			if ( json_last_error() !== JSON_ERROR_NONE ) {
				// Something went wrong; for safety, don't write anything, and raise an error
				trigger_error( "Config cache failure: Static encoding failed", E_USER_ERROR );
			} else {
				if ( file_put_contents( $tmpFile, $staticCacheObject ) ) {
					if ( rename( $tmpFile, $cacheDir . '/' . $cacheShard ) ) {
						// Rename succeded; no need to clean up temp file
						return;
					}
				}
			}
			// T136258: Rename failed, write failed, or data wasn't cacheable; clean up temp file
			unlink( $tmpFile );
		}
	}

}
