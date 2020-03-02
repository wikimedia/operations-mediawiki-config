<?php

namespace Wikimedia\MWConfig;

use MWWikiversions;

/**
 * Wrapper for config caching code.
 */
class MWConfigCacheGenerator {
	# When updating list please run ./docroot/noc/createTxtFileSymlinks.sh
	# Expand computed dblists with ./multiversion/bin/expanddblist

	public static $dbLists = [
		// First, the project families:
		'wikibooks',
		'wikimedia',
		'wikinews',
		'wikipedia',
		'wikiquote',
		'wikisource',
		'wikiversity',
		'wikivoyage',
		'wiktionary',
		'special',

		// Then, the custom lists for configuration:
		'private',
		'fishbowl',
		'closed',
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
		'nonbetafeatures',
		'group0',
		'group1',
		'group2',
		'nonglobal',
		'wikitech',
		'nonecho',
		'mobilemainpagelegacy',
		'wikipedia-cyrillic',
		'wikipedia-e-acute',
		'wikipedia-devanagari',
		'wikipedia-english',
		'nowikidatadescriptiontaglines',
		'top6-wikipedia',
		'rtl',
		'pp_stage0',
		'pp_stage1',
		'cirrussearch-big-indices',
	];

	public static $labsDbLists = [
		'flow-labs',
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

		$dbLists = self::$dbLists;
		if ( $realm === 'labs' ) {
			$dbLists = array_merge( $dbLists, self::$labsDbLists );
		}
		foreach ( $dbLists as $tag ) {
			$dblist = MWWikiversions::readDbListFile( $tag );
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

		$dbLists = self::$dbLists;
		if ( $realm === 'labs' ) {
			$dbLists = array_merge( $dbLists, self::$labsDbLists );

			// FIXME: Do we have a nicer way to get these Defines?
			require_once __DIR__ . "../../tests/Defines.php";
			require_once __DIR__ . "../../wmf-config/InitialiseSettings-labs.php";
			$config = wmfApplyLabsOverrideSettings( $config );
		}

		foreach ( $dbLists as $tag ) {
			$dblist = MWWikiversions::readDbListFile( $tag );

			if ( in_array( $wikiDBname, $dblist ) ) {
				$wikiTags[] = $tag;
			}
		}

		$instance = self::getInstance();
		$instance->settings = $config;

		list( $site, $lang ) = $instance->siteFromDB( $wikiDBname );

		$dbSuffix = ( $site === 'wikipedia' ) ? 'wiki' : $site;
		$confParams = [
			'lang' => $lang,
			'site' => $site,
		];

		// Re-write dynamic values for $site and $lang to be static.
		foreach ( $instance->settings as $setting => $valueArray ) {
			foreach ( $valueArray as $selector => $value ) {
				if ( is_string( $value ) ) {
					if ( $site && strpos( $value, '$site' ) !== false ) {
						$value = str_replace( '$site', "$site", $value );
					}

					if ( $lang && strpos( $value, '$lang' ) !== false ) {
						$value = str_replace( '$lang', "$lang", $value );
					}

					if ( $value !== $instance->settings[ $setting ][ $selector ] ) {
						$instance->settings[ $setting ][ $selector ] = $value;
					}
				}
			}
		}

		// Add a per-language tag as well
		$wikiTags[] = $instance->get( 'wgLanguageCode', $wikiDBname, $dbSuffix, $confParams, $wikiTags );
		$settings = $instance->getAll( $wikiDBname, $dbSuffix, $confParams, $wikiTags );

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
	 * Array of suffixes, for self::siteFromDB()
	 */
	private $suffixes = [
		// 'wikipedia',
		'wikipedia' => 'wiki',
		'wiktionary',
		'wikiquote',
		'wikibooks',
		'wikiquote',
		'wikinews',
		'wikisource',
		'wikiversity',
		'wikimedia',
		'wikivoyage',
	];

	/**
	 * Array of wikis, should be the same as $wgLocalDatabases
	 */
	private $wikis = [];

	/**
	 * The whole array of settings
	 */
	private $settings = [];

	/**
	 * Array of domains that are local and can be handled by the same server
	 *
	 * @deprecated since 1.25; use $wgLocalVirtualHosts instead.
	 */
	private $localVHosts = [];

	/**
	 * Optional callback to load full configuration data.
	 * @var string|array
	 */
	private $fullLoadCallback = null;

	/** Whether or not all data has been loaded */
	private $fullLoadDone = false;

	/**
	 * A callback function that returns an array with the following keys (all
	 * optional):
	 * - suffix: site's suffix
	 * - lang: site's lang
	 * - tags: array of wiki tags
	 * - params: array of parameters to be replaced
	 * The function will receive the SiteConfiguration instance in the first
	 * argument and the wiki in the second one.
	 * if suffix and lang are passed they will be used for the return value of
	 * self::siteFromDB() and self::$suffixes will be ignored
	 *
	 * @var string|array
	 */
	private $siteParamsCallback = null;

	/**
	 * Configuration cache for getConfig()
	 * @var array
	 */
	private $cfgCache = [];

	/**
	 * Retrieves a configuration setting for a given wiki.
	 * @param string $settingName ID of the setting name to retrieve
	 * @param string $wiki Wiki ID of the wiki in question.
	 * @param string|null $suffix The suffix of the wiki in question.
	 * @param array $params List of parameters. $.'key' is replaced by $value in all returned data.
	 * @param array $wikiTags The tags assigned to the wiki.
	 * @return mixed The value of the setting requested.
	 */
	private function get( $settingName, $wiki, $suffix = null, $params = [],
		$wikiTags = []
	) {
		$params = $this->mergeParams( $wiki, $suffix, $params, $wikiTags );
		return $this->getSetting( $settingName, $wiki, $params );
	}

	/**
	 * Really retrieves a configuration setting for a given wiki.
	 *
	 * @param string $settingName ID of the setting name to retrieve.
	 * @param string $wiki Wiki ID of the wiki in question.
	 * @param array $params Array of parameters.
	 * @return mixed The value of the setting requested.
	 */
	private function getSetting( $settingName, $wiki, array $params ) {
		$retval = null;
		if ( array_key_exists( $settingName, $this->settings ) ) {
			$thisSetting =& $this->settings[$settingName];
			do {
				// Do individual wiki settings
				if ( array_key_exists( $wiki, $thisSetting ) ) {
					$retval = $thisSetting[$wiki];
					break;
				} elseif ( array_key_exists( "+$wiki", $thisSetting ) && is_array( $thisSetting["+$wiki"] ) ) {
					$retval = $thisSetting["+$wiki"];
				}

				// Do tag settings
				foreach ( $params['tags'] as $tag ) {
					if ( array_key_exists( $tag, $thisSetting ) ) {
						if ( is_array( $retval ) && is_array( $thisSetting[$tag] ) ) {
							$retval = self::arrayMerge( $retval, $thisSetting[$tag] );
						} else {
							$retval = $thisSetting[$tag];
						}
						break 2;
					} elseif ( array_key_exists( "+$tag", $thisSetting ) && is_array( $thisSetting["+$tag"] ) ) {
						if ( $retval === null ) {
							$retval = [];
						}
						$retval = self::arrayMerge( $retval, $thisSetting["+$tag"] );
					}
				}
				// Do suffix settings
				$suffix = $params['suffix'];
				if ( $suffix !== null ) {
					if ( array_key_exists( $suffix, $thisSetting ) ) {
						if ( is_array( $retval ) && is_array( $thisSetting[$suffix] ) ) {
							$retval = self::arrayMerge( $retval, $thisSetting[$suffix] );
						} else {
							$retval = $thisSetting[$suffix];
						}
						break;
					} elseif ( array_key_exists( "+$suffix", $thisSetting )
						&& is_array( $thisSetting["+$suffix"] )
					) {
						if ( $retval === null ) {
							$retval = [];
						}
						$retval = self::arrayMerge( $retval, $thisSetting["+$suffix"] );
					}
				}

				// Fall back to default.
				if ( array_key_exists( 'default', $thisSetting ) ) {
					if ( is_array( $retval ) && is_array( $thisSetting['default'] ) ) {
						$retval = self::arrayMerge( $retval, $thisSetting['default'] );
					} else {
						$retval = $thisSetting['default'];
					}
					break;
				}
			} while ( false );
		}

		if ( $retval !== null && count( $params['params'] ) ) {
			foreach ( $params['params'] as $key => $value ) {
				$retval = $this->doReplace( '$' . $key, $value, $retval );
			}
		}
		return $retval;
	}

	/**
	 * Type-safe string replace; won't do replacements on non-strings
	 * private?
	 *
	 * @param string $from
	 * @param string $to
	 * @param string|array $in
	 * @return string|array
	 */
	private function doReplace( $from, $to, $in ) {
		if ( is_string( $in ) ) {
			return str_replace( $from, $to, $in );
		} elseif ( is_array( $in ) ) {
			foreach ( $in as $key => $val ) {
				$in[$key] = $this->doReplace( $from, $to, $val );
			}
			return $in;
		} else {
			return $in;
		}
	}

	/**
	 * Gets all settings for a wiki
	 * @param string $wiki Wiki ID of the wiki in question.
	 * @param string|null $suffix The suffix of the wiki in question.
	 * @param array $params List of parameters. $.'key' is replaced by $value in all returned data.
	 * @param array $wikiTags The tags assigned to the wiki.
	 * @return array Array of settings requested.
	 */
	private function getAll( $wiki, $suffix = null, $params = [], $wikiTags = [] ) {
		$params = $this->mergeParams( $wiki, $suffix, $params, $wikiTags );
		$localSettings = [];
		foreach ( $this->settings as $varname => $stuff ) {
			$append = false;
			$var = $varname;
			if ( substr( $varname, 0, 1 ) == '+' ) {
				$append = true;
				$var = substr( $varname, 1 );
			}

			$value = $this->getSetting( $varname, $wiki, $params );
			if ( $append && is_array( $value ) && is_array( $GLOBALS[$var] ) ) {
				$value = self::arrayMerge( $value, $GLOBALS[$var] );
			}
			if ( $value !== null ) {
				$localSettings[$var] = $value;
			}
		}
		return $localSettings;
	}

	/**
	 * Retrieves a configuration setting for a given wiki, forced to a boolean.
	 * @param string $setting ID of the setting name to retrieve
	 * @param string $wiki Wiki ID of the wiki in question.
	 * @param string|null $suffix The suffix of the wiki in question.
	 * @param array $wikiTags The tags assigned to the wiki.
	 * @return bool The value of the setting requested.
	 */
	private function getBool( $setting, $wiki, $suffix = null, $wikiTags = [] ) {
		return (bool)$this->get( $setting, $wiki, $suffix, [], $wikiTags );
	}

	/**
	 * Retrieves an array of local databases
	 *
	 * @return array
	 */
	private function &getLocalDatabases() {
		return $this->wikis;
	}

	/**
	 * Retrieves the value of a given setting, and places it in a variable passed by reference.
	 * @param string $setting ID of the setting name to retrieve
	 * @param string $wiki Wiki ID of the wiki in question.
	 * @param string $suffix The suffix of the wiki in question.
	 * @param array &$var Reference The variable to insert the value into.
	 * @param array $params List of parameters. $.'key' is replaced by $value in all returned data.
	 * @param array $wikiTags The tags assigned to the wiki.
	 */
	private function extractVar( $setting, $wiki, $suffix, &$var,
		$params = [], $wikiTags = []
	) {
		$value = $this->get( $setting, $wiki, $suffix, $params, $wikiTags );
		if ( $value !== null ) {
			$var = $value;
		}
	}

	/**
	 * Retrieves the value of a given setting, and places it in its corresponding global variable.
	 * @param string $setting ID of the setting name to retrieve
	 * @param string $wiki Wiki ID of the wiki in question.
	 * @param string|null $suffix The suffix of the wiki in question.
	 * @param array $params List of parameters. $.'key' is replaced by $value in all returned data.
	 * @param array $wikiTags The tags assigned to the wiki.
	 */
	private function extractGlobal( $setting, $wiki, $suffix = null,
		$params = [], $wikiTags = []
	) {
		$params = $this->mergeParams( $wiki, $suffix, $params, $wikiTags );
		$this->extractGlobalSetting( $setting, $wiki, $params );
	}

	/**
	 * @param string $setting
	 * @param string $wiki
	 * @param array $params
	 */
	private function extractGlobalSetting( $setting, $wiki, $params ) {
		$value = $this->getSetting( $setting, $wiki, $params );
		if ( $value !== null ) {
			if ( substr( $setting, 0, 1 ) == '+' && is_array( $value ) ) {
				$setting = substr( $setting, 1 );
				if ( is_array( $GLOBALS[$setting] ) ) {
					$GLOBALS[$setting] = self::arrayMerge( $GLOBALS[$setting], $value );
				} else {
					$GLOBALS[$setting] = $value;
				}
			} else {
				$GLOBALS[$setting] = $value;
			}
		}
	}

	/**
	 * Retrieves the values of all settings, and places them in their corresponding global variables.
	 * @param string $wiki Wiki ID of the wiki in question.
	 * @param string|null $suffix The suffix of the wiki in question.
	 * @param array $params List of parameters. $.'key' is replaced by $value in all returned data.
	 * @param array $wikiTags The tags assigned to the wiki.
	 */
	private function extractAllGlobals( $wiki, $suffix = null, $params = [],
		$wikiTags = []
	) {
		$params = $this->mergeParams( $wiki, $suffix, $params, $wikiTags );
		foreach ( $this->settings as $varName => $setting ) {
			$this->extractGlobalSetting( $varName, $wiki, $params );
		}
	}

	/**
	 * Return specific settings for $wiki
	 * See the documentation of self::$siteParamsCallback for more in-depth
	 * documentation about this function
	 *
	 * @param string $wiki
	 * @return array
	 */
	private function getWikiParams( $wiki ) {
		static $default = [
			'suffix' => null,
			'lang' => null,
			'tags' => [],
			'params' => [],
		];

		if ( !is_callable( $this->siteParamsCallback ) ) {
			return $default;
		}

		$ret = ( $this->siteParamsCallback )( $this, $wiki );
		# Validate the returned value
		if ( !is_array( $ret ) ) {
			return $default;
		}

		foreach ( $default as $name => $def ) {
			if ( !isset( $ret[$name] ) || ( is_array( $default[$name] ) && !is_array( $ret[$name] ) ) ) {
				$ret[$name] = $default[$name];
			}
		}

		return $ret;
	}

	/**
	 * Merge params between the ones passed to the function and the ones given
	 * by self::$siteParamsCallback for backward compatibility
	 * Values returned by self::getWikiParams() have the priority.
	 *
	 * @param string $wiki Wiki ID of the wiki in question.
	 * @param string $suffix The suffix of the wiki in question.
	 * @param array $params List of parameters. $.'key' is replaced by $value in
	 *   all returned data.
	 * @param array $wikiTags The tags assigned to the wiki.
	 * @return array
	 */
	private function mergeParams( $wiki, $suffix, array $params, array $wikiTags ) {
		$ret = $this->getWikiParams( $wiki );

		if ( $ret['suffix'] === null ) {
			$ret['suffix'] = $suffix;
		}

		$ret['tags'] = array_unique( array_merge( $ret['tags'], $wikiTags ) );

		$ret['params'] += $params;

		// Automatically fill that ones if needed
		if ( !isset( $ret['params']['lang'] ) && $ret['lang'] !== null ) {
			$ret['params']['lang'] = $ret['lang'];
		}
		if ( !isset( $ret['params']['site'] ) && $ret['suffix'] !== null ) {
			$ret['params']['site'] = $ret['suffix'];
		}

		return $ret;
	}

	/**
	 * Work out the site and language name from a database name
	 * @param string $wiki Wiki ID
	 *
	 * @return array
	 */
	private function siteFromDB( $wiki ) {
		// Allow override
		$def = $this->getWikiParams( $wiki );
		if ( $def['suffix'] !== null && $def['lang'] !== null ) {
			return [ $def['suffix'], $def['lang'] ];
		}

		$site = null;
		$lang = null;
		foreach ( $this->suffixes as $altSite => $suffix ) {
			if ( $suffix === '' ) {
				$site = '';
				$lang = $wiki;
				break;
			} elseif ( substr( $wiki, -strlen( $suffix ) ) == $suffix ) {
				$site = is_numeric( $altSite ) ? $suffix : $altSite;
				$lang = substr( $wiki, 0, strlen( $wiki ) - strlen( $suffix ) );
				break;
			}
		}
		$lang = str_replace( '_', '-', $lang );

		return [ $site, $lang ];
	}

	/**
	 * Get the resolved (post-setup) configuration of a potentially foreign wiki.
	 * For foreign wikis, this is expensive, and only works if maintenance
	 * scripts are setup to handle the --wiki parameter such as in wiki farms.
	 *
	 * @param string $wiki
	 * @param array|string $settings A setting name or array of setting names
	 * @return mixed|mixed[] Array if $settings is an array, otherwise the value
	 * @throws MWException
	 * @since 1.21
	 */
	private function getConfig( $wiki, $settings ) {
		global $IP;

		$multi = is_array( $settings );
		$settings = (array)$settings;
		if ( WikiMap::isCurrentWikiId( $wiki ) ) { // $wiki is this wiki
			$res = [];
			foreach ( $settings as $name ) {
				if ( !preg_match( '/^wg[A-Z]/', $name ) ) {
					throw new MWException( "Variable '$name' does start with 'wg'." );
				} elseif ( !isset( $GLOBALS[$name] ) ) {
					throw new MWException( "Variable '$name' is not set." );
				}
				$res[$name] = $GLOBALS[$name];
			}
		} else { // $wiki is a foreign wiki
			if ( isset( $this->cfgCache[$wiki] ) ) {
				$res = array_intersect_key( $this->cfgCache[$wiki], array_flip( $settings ) );
				if ( count( $res ) == count( $settings ) ) {
					return $multi ? $res : current( $res ); // cache hit
				}
			} elseif ( !in_array( $wiki, $this->wikis ) ) {
				throw new MWException( "No such wiki '$wiki'." );
			} else {
				$this->cfgCache[$wiki] = [];
			}
			$result = Shell::makeScriptCommand(
				"$IP/maintenance/getConfiguration.php",
				[
					'--wiki', $wiki,
					'--settings', implode( ' ', $settings ),
					'--format', 'PHP',
				]
			)
				// limit.sh breaks this call
				->limits( [ 'memory' => 0, 'filesize' => 0 ] )
				->execute();

			$data = trim( $result->getStdout() );
			if ( $result->getExitCode() || $data === '' ) {
				throw new MWException( "Failed to run getConfiguration.php: {$result->getStdout()}" );
			}
			$res = unserialize( $data );
			if ( !is_array( $res ) ) {
				throw new MWException( "Failed to unserialize configuration array." );
			}
			$this->cfgCache[$wiki] = $this->cfgCache[$wiki] + $res;
		}

		return $multi ? $res : current( $res );
	}

	/**
	 * Merge multiple arrays together.
	 * On encountering duplicate keys, merge the two, but ONLY if they're arrays.
	 * PHP's array_merge_recursive() merges ANY duplicate values into arrays,
	 * which is not fun
	 *
	 * @param array $array1
	 * @param array ...$arrays
	 *
	 * @return array
	 */
	private static function arrayMerge( array $array1, ...$arrays ) {
		$out = $array1;
		foreach ( $arrays as $array ) {
			foreach ( $array as $key => $value ) {
				if ( isset( $out[$key] ) && is_array( $out[$key] ) && is_array( $value ) ) {
					$out[$key] = self::arrayMerge( $out[$key], $value );
				} elseif ( !isset( $out[$key] ) || !$out[$key] && !is_numeric( $key ) ) {
					// Values that evaluate to true given precedence, for the
					// primary purpose of merging permissions arrays.
					$out[$key] = $value;
				} elseif ( is_numeric( $key ) ) {
					$out[] = $value;
				}
			}
		}

		return $out;
	}

	private function loadFullData() {
		if ( $this->fullLoadCallback && !$this->fullLoadDone ) {
			( $this->fullLoadCallback )( $this );
			$this->fullLoadDone = true;
		}
	}

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
				if ( @$staticCacheObject['mtime'] === $confActualMtime ) {
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
