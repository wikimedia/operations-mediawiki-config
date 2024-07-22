<?php
require_once __DIR__ . '/defines.php';
require_once __DIR__ . '/MWMultiVersionException.php';
require_once __DIR__ . '/MWRealm.php';
require_once __DIR__ . '/MWWikiversions.php';

/**
 * Class to handle basic information related to what
 * version of MediaWiki is running on a wiki installation.
 *
 * Avoid setting environmental or globals variables here for OOP.
 */
class MWMultiVersion {

	public const SUFFIXES = [
		// For legacy reasons, wikipedias and specials both use the "wiki" suffix,
		// and for both the internal $site family will map to "wikipedia".
		'wikipedia' => 'wiki',
		'wiktionary',
		'wikiquote',
		'wikibooks',
		'wikinews',
		'wikisource',
		'wikiversity',
		'wikimedia',
		'wikivoyage',
	];

	/**
	 * Note that most wiki families are available as tags for free without
	 * needing a dblist to be maintained and read from disk, because their
	 * dbname suffix (as mapped in MWMultiVersion::SUFFIXES) already makes them
	 * available as SiteConfiguration tag in InitialiseSettings.php.
	 *
	 * @var string[]
	 */
	public const DB_LISTS = [
		// Expand computed dblists with `./multiversion/bin/expanddblist`.
		// When updating this list, run `composer manage-dblist update` afterwards.
		'wikipedia',
		'special',
		'private',
		'fishbowl',
		'closed',
		'flow',
		'flaggedrevs',
		'small',
		'skin-themes',
		'skin-themes-wikipedias-disabled',
		'legacy-vector',
		'medium',
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
		'modern-mainpage',
		'nowikidatadescriptiontaglines',
		'cirrussearch-big-indices',
		'rtl',
		'translate',
		'growthexperiments',
	];

	/** @var string[] */
	public const DB_LISTS_LABS = [
		'closed' => 'closed-labs',
		'flow' => 'flow-labs',
	];

	/**
	 * @var MWMultiVersion
	 */
	private static $instance;

	/**
	 * @var string
	 */
	private $db;

	/**
	 * @var null|false|string
	 */
	private $version;

	/**
	 * List of *.wikimedia.org subdomains that are chapter wikis
	 * @var array
	 */
	private $wikimediaSubdomains = [
		'ae',
		'am',
		'ar',
		'az',
		'bd',
		'be',
		'br',
		'ca',
		'cn',
		'co',
		'dk',
		'ec',
		'et',
		'fi',
		'ge',
		'gr',
		'hi',
		'id',
		'id-internal',
		'il',
		'mai',
		'mk',
		'mx',
		'ng',
		'nl',
		'noboard-chapters',
		'no',
		'nyc',
		'nz',
		'pa-us',
		'pl',
		'pt',
		'punjabi',
		'romd',
		'rs',
		'ru',
		'se',
		'tr',
		'ua',
		'uk',
		've',
		'wb',
	];

	/**
	 * To get an instance of this class, use the static helper methods.
	 * @see getInstanceForWiki
	 * @see getInstanceForUploadWiki
	 */
	private function __construct() {
	}

	private function __clone() {
	}

	/**
	 * Create an instance by explicit wiki ID.
	 *
	 * @param string $dbName
	 * @return MWMultiVersion
	 */
	public static function newFromDBName( $dbName ) {
		$m = new self();
		$m->db = $dbName;
		return $m;
	}

	private static function createInstance() {
		if ( isset( self::$instance ) ) {
			self::error( "MWMultiVersion instance already set!\n" );
		}
		self::$instance = new self;
		return self::$instance;
	}

	/**
	 * Create an instance by HTTP host name.
	 *
	 * Use this for all web requests, except those rewritten from
	 * upload.wikimedia.org to /w/thumb.php.
	 *
	 * @param string $serverName HTTP host name from `$_SERVER['SERVER_NAME']`.
	 * @return MWMultiVersion
	 */
	public static function initializeForWiki( $serverName ) {
		$instance = self::createInstance();
		$instance->setSiteInfoForWiki( $serverName );
		return $instance;
	}

	/**
	 * Create an instance for upload.wikimedia.org requests to /w/thumb_handler.php.
	 *
	 * For example:
	 * <https://upload.wikimedia.org/wikipedia/commons/thumb/8/84/Example.svg/240px-Example.svg.png>
	 *
	 * TODO: As of 2022, this might be obsolete. Even before Thumbor, it
	 * seems swift-rewrite.py had already been routing requests in a way
	 * that wouldn't satisfy this condition.
	 *
	 * @param string $pathInfo CGI path info, from `$_SERVER['PATH_INFO']`.
	 * @return MWMultiVersion
	 */
	public static function initializeForUploadWiki( $pathInfo ) {
		$instance = self::createInstance();
		$instance->setSiteInfoForUploadWiki( $pathInfo );
		return $instance;
	}

	/**
	 * Create an instance for sso.wikimedia.org requests.
	 *
	 * For example:
	 * <https://sso.wikimedia.org/en.wikipedia.org/wiki/Special:Userlogin>
	 *
	 * @param ?string $requestUri CGI path info, from `$_SERVER['REQUEST_URI']`.
	 * @return MWMultiVersion
	 */
	public static function initializeForSsoDomain( $requestUri ) {
		$instance = self::createInstance();
		$instance->setSiteInfoForSsoDomain( $requestUri );
		return $instance;
	}

	/**
	 * Create an instance by `--wiki` CLI parameter.
	 *
	 * This is used by MWScript.php and the `mwscript` command for
	 * running maintenance scripts.
	 *
	 * @return MWMultiVersion
	 */
	public static function initializeForMaintenance() {
		$instance = self::createInstance();
		$instance->setSiteInfoForMaintenance();
		return $instance;
	}

	/**
	 * @todo remove once all scripts have been migrated
	 * not to use CommandLineInc
	 *
	 * @return MWMultiVersion
	 */
	public static function initializeForMaintenanceOld() {
		$instance = self::createInstance();
		$instance->setSiteInfoForMaintenanceOld();
		return $instance;
	}

	/**
	 * Create an instance by explicit wiki ID.
	 *
	 * @param string $dbName
	 * @return MWMultiVersion
	 */
	public static function initializeFromDBName( $dbName ) {
		$instance = self::createInstance();
		$instance->db = $dbName;
		return $instance;
	}

	/**
	 * Get the previously created singleton for the current wiki.
	 *
	 * @return MWMultiVersion|null
	 */
	public static function getInstance() {
		return self::$instance;
	}

	/**
	 * Destroy the singleton instance.
	 *
	 * Use this to let a subsequent call create a new instance.
	 *
	 * This MUST NOT be used outside command-line or test contexts,
	 * and will exit with a fatal error in that case.
	 */
	public static function destroySingleton() {
		if ( PHP_SAPI !== 'cli' ) {
			self::error( 'Must not destroy singleton instance when used ' .
				'with non-CLI interface' );
		}
		self::$instance = null;
	}

	/**
	 * Create an instance for a web request, based on $_SERVER properties.
	 * @param ?string $serverName HTTP host name from `$_SERVER['SERVER_NAME']`.
	 * @param ?string $scriptName HTTP script name from `$_SERVER['SCRIPT_NAME']`.
	 * @param ?string $pathInfo CGI path info, from `$_SERVER['PATH_INFO']`.
	 * @param ?string $requestUri CGI request URI, from `$_SERVER['REQUEST_URI']`.
	 * @return MWMultiVersion
	 */
	public static function initializeFromServerData( $serverName, $scriptName, $pathInfo, $requestUri ) {
		if ( $scriptName === '/w/thumb.php'
			&& ( $serverName === 'upload.wikimedia.org' || $serverName === 'upload.wikimedia.beta.wmflabs.org' )
		) {
			// Upload URL hit (to upload.wikimedia.org rather than wiki of origin)...
			return self::initializeForUploadWiki( $pathInfo );
		} elseif ( $serverName === 'sso.wikimedia.org' || $serverName === 'sso.wikimedia.beta.wmflabs.org' ) {
			// SSO URL hit. The condition here must match the one in CommonSettings.php where $wmgPathPrefix is set.
			return self::initializeForSsoDomain( $requestUri );
		} else {
			// Regular URL hit (wiki of origin)...
			return self::initializeForWiki( $serverName );
		}
	}

	/**
	 * Initialize object state by mapping an HTTP hostname to a wiki ID.
	 *
	 * @param string $serverName
	 */
	private function setSiteInfoForWiki( $serverName ) {
		$matches = [];

		$staticMappings = [
			// Production
			'api.wikimedia.org' => 'apiportal',
			'test.wikidata.org' => 'testwikidata',
			'test-commons.wikimedia.org' => 'testcommons',
			'www.mediawiki.org' => 'mediawiki',
			'www.wikidata.org' => 'wikidata',
			'wikisource.org' => 'sources',
			'wikitech.wikimedia.org' => 'labs',
			'labtestwikitech.wikimedia.org' => 'labtest',
			'affcom.wikimedia.org' => 'chapcom',
			'be-tarask.wikipedia.org' => 'be_x_old',
			'ee.wikimedia.org' => 'et',
			'vrt-wiki.wikimedia.org' => 'otrs_wiki',
			'ombuds.wikimedia.org' => 'ombudsmen',
			'www.wikifunctions.org' => 'wikifunctions',
			'wikipedia-pl-sysop.wikimedia.org' => 'sysop_pl',
			'wikipedia-it-arbcom.wikimedia.org' => 'arbcom_it',

			// Labs
			'api.wikimedia.beta.wmflabs.org' => 'apiportal',
			'beta.wmflabs.org' => 'deployment',
			'wikidata.beta.wmflabs.org' => 'wikidata',
			'wikifunctions.beta.wmflabs.org' => 'wikifunctions',
		];

		$lang = null;
		$site = "wikipedia";
		if ( isset( $staticMappings[$serverName] ) ) {
			$lang = $staticMappings[$serverName];
			if ( $serverName === 'ee.wikimedia.org' ) {
				$site = "wikimedia";
			}
		} elseif ( strpos( $serverName, 'wmflabs' ) !== false
			|| strpos( $serverName, 'wmcloud' ) !== false
		) {
			if (
				preg_match( '/^([^.]+)\.([^.]+)\.beta\.(wmflabs|wmcloud)\.org$/', $serverName, $matches )
			) {
				// http://en.wikipedia.beta.wmflabs.org/ or http://en.wikipedia.beta.wmcloud.org/
				$lang = $matches[1];
				if ( $matches[2] === 'wikimedia' ) {
					# Beta uses 'wiki' as a DB suffix for WikiMedia databases
					# Eg 'login.wikimedia.beta.wmflabs.org' => 'loginwiki'
					$site = 'wikipedia';
				} else {
					$site = $matches[2];
				}
			} else {
				self::error( "Invalid host name ($serverName).\n", 400 );
			}
		} elseif ( preg_match( '/^(.*)\.([a-z]+)\.org$/', $serverName, $matches ) ) {
			$lang = $matches[1];
			if ( $matches[2] !== 'wikimedia'
				|| ( $matches[2] === 'wikimedia' && in_array(
					$lang,
					$this->wikimediaSubdomains
				) ) ) {
				// wikimedia (non chapters) sites stay as wiki
				$site = $matches[2];
			}
		} else {
			$ip = @$_SERVER['REQUEST_ADDR'];
			$xff = @$_SERVER['HTTP_X_FORWARDED_FOR'];
			$request = @$_SERVER['REQUEST_URI'];
			self::error( "Invalid host name (server: $serverName, request: $request, ip: $ip, xff: $xff).\n", 400 );
		}
		$this->loadDBFromSite( $site, $lang );
	}

	/**
	 * Initialize object state from an upload.wikimedia.org request path.
	 *
	 * @param string $pathInfo
	 */
	private function setSiteInfoForUploadWiki( $pathInfo ) {
		$pathBits = explode( '/', $pathInfo );
		if ( count( $pathBits ) < 3 ) {
			self::error( "Invalid file path info (pathinfo=" . $pathInfo . "), can't determine language.\n" );
		}
		[ , $site, $lang ] = $pathBits;
		$this->loadDBFromSite( $site, $lang );
	}

	/**
	 * Initialize object state from an sso.wikimedia.org request path.
	 *
	 * @param ?string $requestUri
	 * @return void
	 */
	private function setSiteInfoForSsoDomain( $requestUri ) {
		$pathBits = explode( '/', $requestUri, 3 );
		if ( count( $pathBits ) < 3 ) {
			self::error( "Invalid request URI (requestUri=" . $requestUri . "), can't determine language.\n" );
		}
		[ , $serverName, ] = $pathBits;
		$this->setSiteInfoForWiki( $serverName );
	}

	/**
	 * Returns true if the supplied version string has an acceptable format.
	 *
	 * @param string $version
	 * @return bool
	 */
	private function validVersion( $version ) {
		// Examples of expected inputs: 1.43.0-wmf.3, master, next, branch_cut_pretest
		// Rules:
		// * Must begin with alphanum.
		// * Remaining chars can be alphanum, dash or dot
		return preg_match( "/^[0-9a-z][0-9a-z.-]*$/i", $version );
	}

	/**
	 * Initialize object state from CLI `--wiki` parameter.
	 *
	 * This code is based on how MediaWiki's Maintenance.php script reads arguments.
	 */
	private function setSiteInfoForMaintenance() {
		global $argv;

		$dbname = getenv( 'MW_WIKI' ) ?: '';

		# The --wiki param must the second argument to avoid
		# any "options with args" ambiguity (see Maintenance.php).
		if ( isset( $argv[2] ) ) {
			if ( $argv[2] === '--wiki' ) {
				// "script.php --wiki dbname"
				$dbname = $argv[3] ?? '';
			} elseif ( substr( $argv[2], 0, 7 ) === '--wiki=' ) {
				// "script.php --wiki=dbname"
				$dbname = substr( $argv[2], 7 );
			} elseif ( substr( $argv[2], 0, 2 ) !== '--' ) {
				// "script.php dbname"
				$dbname = $argv[2];
				$argv[2] = '--wiki=' . $dbname;
			}
		}

		if ( $dbname === '' ) {
			self::error( "Usage: mwscript scriptName.php --wiki=dbname\n" );
		}

		if ( isset( $argv[3] ) && $argv[3] === '--force-version' ) {
			if ( !isset( $argv[4] ) ) {
				self::error( "--force-version must be followed by a version number\n" );
			}
			$version = $argv[4];
			if ( !self::validVersion( $version ) ) {
				self::error( "Invalid version format passed to --force-version: '$version'\n" );
			}
			$this->version = "php-" . $version;

			# Delete the flag and its parameter so it won't be passed on to the
			# maintenance script.
			unset( $argv[4] );
			unset( $argv[3] );

			# Reindex
			$argv = array_values( $argv );
		}

		$this->db = $dbname;
	}

	/**
	 * This is like setSiteInfoForMaintenanceOld but for old maint scripts using
	 *  CommandLineInc
	 * TODO: Remove once all of them have been migrated.
	 */
	private function setSiteInfoForMaintenanceOld() {
		global $argv;
		$dbname = getenv( 'MW_WIKI' ) ?: '';
		# The --wiki param must the second argument to avoid
		# any "options with args" ambiguity (see Maintenance.php).
		if ( isset( $argv[1] ) ) {
			if ( $argv[1] === '--wiki' ) {
				// "script.php --wiki dbname"
				$dbname = $argv[2] ?? '';
			} elseif ( substr( $argv[1], 0, 7 ) === '--wiki=' ) {
				// "script.php --wiki=dbname"
				$dbname = substr( $argv[1], 7 );
			} elseif ( substr( $argv[1], 0, 2 ) !== '--' ) {
				// "script.php dbname"
				$dbname = $argv[1];
				$argv[1] = '--wiki=' . $dbname;
			}
		}
		if ( $dbname === '' ) {
			self::error( "Usage: mwscript scriptName.php --wiki=dbname\n" );
		}
		if ( isset( $argv[2] ) && $argv[2] === '--force-version' ) {
			if ( !isset( $argv[3] ) ) {
				self::error( "--force-version must be followed by a version number" );
			}
			$this->version = "php-" . $argv[3];
			# Delete the flag and its parameter so it won't be passed on to the
			# maintenance script.
			unset( $argv[3] );
			unset( $argv[2] );
			# Reindex
			$argv = array_values( $argv );
		}
		$this->db = $dbname;
	}

	/**
	 * Initialize object state from a legacy site-lang pair.
	 *
	 * @param string $site
	 * @param string $lang
	 */
	private function loadDBFromSite( $site, $lang ) {
		$dbSuffix = $site === 'wikipedia' ? 'wiki' : $site;
		$this->db = str_replace( "-", "_", $lang . $dbSuffix );
	}

	/**
	 * Get the DB name for this wiki
	 *
	 * @return string
	 */
	public function getDatabase() {
		return $this->db;
	}

	/**
	 * Handle the wfShellWikiCmd hook.
	 *
	 * This converts shell commands like "php $IP/maintenance/foo.php" into
	 * commands that use the "MWScript.php" wrapper, for example:
	 * "php /srv/mediawiki-staging/multiversion/MWScript.php maintenance/foo.php"
	 *
	 * @param string &$script
	 * @param array &$params
	 * @param array &$options
	 * @return bool
	 */
	public static function onWfShellMaintenanceCmd( &$script, array &$params, array &$options ) {
		global $IP;
		if ( strpos( $script, "{$IP}/" ) === 0 ) {
			$script = substr( $script, strlen( "{$IP}/" ) );
		}
		$options['wrapper'] = __DIR__ . '/MWScript.php';
		return true;
	}

	/**
	 * Lazy initialize `$this->version` after the wiki ID has been mapped in `$this->db`.
	 */
	private function loadVersionInfo() {
		global $wmgRealm;

		if ( $this->version !== null ) {
			return;
		}

		// Load the realm-specific wikiversions file,
		// such as wikiversions-labs.php or wikiversions-dev.php
		$phpFilename = $wmgRealm === 'production' ? 'wikiversions.php' : "wikiversions-$wmgRealm.php";
		$phpFilename = dirname( __DIR__ ) . '/' . $phpFilename;

		// This intentionally tolerates absence by using `include` instead of `require`
		$wikiversions = include $phpFilename;
		if ( $wikiversions === false ) {
			self::error( "Unable to open $phpFilename.\n" );
		}
		if ( !is_array( $wikiversions ) ) {
			self::error( "$phpFilename did not return an array as expected.\n" );
		}

		$version = $wikiversions[$this->db] ?? false;

		if ( $version && strpos( $version, 'php-' ) !== 0 ) {
			self::error( "$phpFilename entry must start with `php-` (got `$version`).\n" );
		}

		if ( $version !== false ) {
			// At this point we know there is an entry in wikiversions for the
			// wiki.  If FORCE_MW_VERSION is set in the environment, we want to
			// use that version instead of the one from wikiversions.
			$force_version = getenv( 'FORCE_MW_VERSION' ) ?: '';
			if ( $force_version ) {
				if ( !self::validVersion( $force_version ) ) {
					self::error( "Invalid version format in FORCE_MW_VERSION: '$force_version'\n" );
				}
				$this->version = "php-$force_version";
				return;
			}
		}

		$this->version = $version;
	}

	/**
	 * Whether the mapped wiki ID is not a known wiki in wikiversions.php.
	 *
	 * @return bool
	 */
	public function isMissing() {
		$this->loadVersionInfo();
		return ( $this->version === false );
	}

	/**
	 * Get the version directory name for the current wiki ID.
	 *
	 * @return string Version directory name, e.g. "php-X.XX" or "php-master".
	 */
	public function getVersion() {
		$this->loadVersionInfo();
		if ( $this->version === false ) {
			self::error( "no version entry for `{$this->db}`.\n" );
		}
		return $this->version;
	}

	/**
	 * Get the short version name for the current wiki ID
	 *
	 * Do NOT use this to determine paths to MediaWiki directories and such.
	 * Use only for display purposes or in cache keys.
	 *
	 * TODO: Consider deprecating in favour of getVersion() for simplicity,
	 * this is a redundant concept.
	 *
	 * @return string Version number, e.g. "x.xx" or "master".
	 */
	public function getVersionNumber() {
		$this->loadVersionInfo();
		if ( $this->version === false ) {
			self::error( "no version entry for `{$this->db}`.\n" );
		}
		// strip "php-"
		return substr( $this->version, 4 );
	}

	/**
	 * Print error and exit PHP process.
	 *
	 * @param string $msg Error to show to the client
	 * @param int $httpError HTTP header error code
	 * @return void
	 */
	private static function error( $msg, $httpError = 500 ) {
		if ( defined( 'MW_PHPUNIT_TEST' ) ) {
			throw new MWMultiVersionException( $msg );
		}

		$msg = (string)$msg;
		if ( PHP_SAPI !== 'cli' ) {
			$msg = htmlspecialchars( $msg );
			switch ( $httpError ) {
				case 400:
					$httpMsg = 'Bad Request';
					break;
				case 500:
				default:
					$httpMsg = 'Internal server error';
					break;
			}
			header( "HTTP/1.1 $httpError $httpMsg" );
		}
		echo $msg;
		if ( $httpError >= 500 ) {
			trigger_error( $msg, E_USER_ERROR );
		}
		exit( 1 );
	}

	/**
	 * Get the location of the correct version of a MediaWiki web entry point.
	 *
	 * This works based on environmental variables, such as the server name,
	 * as given by CGI (php-fpm).
	 *
	 * This must be called from web requests. For CLI, use getMediaWikiCli.
	 *
	 * If the wiki doesn't exist, then missing.php will
	 * be rendered and flushed to stdout and the process exited.
	 *
	 * If the wiki exists, this function also has these responsibilities:
	 *
	 * - Set the $IP global variable (path to MediaWiki).
	 * - Set the MW_INSTALL_PATH environmental variable.
	 * - Change PHP's current directory to the chosen MediaWiki install path.
	 *
	 * @param string $file File path (relative to MediaWiki dir)
	 * @param string|null $wiki Force the Wiki ID rather than detecting it
	 * @return string Absolute file path with proper MW location
	 */
	public static function getMediaWiki( $file, $wiki = null ) {
		global $IP;

		if ( $wiki === null ) {
			$scriptName = @$_SERVER['SCRIPT_NAME'];
			$serverName = @$_SERVER['SERVER_NAME'];
			$pathInfo = @$_SERVER['PATH_INFO'];
			$requestUri = @$_SERVER['REQUEST_URI'];
			$multiVersion = self::initializeFromServerData( $serverName, $scriptName, $pathInfo, $requestUri );
		} else {
			$multiVersion = self::initializeFromDBName( $wiki );
		}

		// Wiki doesn't exist, yet?
		if ( $multiVersion->isMissing() ) {
			// same hack as CommonSettings.php
			header( 'Cache-control: no-cache' );
			include __DIR__ . '/missing.php';
			exit;
		}

		// Get the MediaWiki version running on this wiki...
		$version = $multiVersion->getVersion();

		// Get the correct MediaWiki path based on this version...
		$IP = MEDIAWIKI_DEPLOYMENT_DIR . "/$version";

		chdir( $IP );
		putenv( "MW_INSTALL_PATH=$IP" );

		return "$IP/$file";
	}

	/**
	 * Get the location of the correct version of a MediaWiki CLI
	 * entry-point file given the `--wiki` parameter passed in.
	 *
	 * This also has some other effects:
	 * (a) Sets the $IP global variable (path to MediaWiki)
	 * (b) Sets the MW_INSTALL_PATH environmental variable
	 * (c) Changes PHP's current directory to the directory of this file.
	 *
	 * @param string $file File path (relative to MediaWiki dir or absolute)
	 * @param bool $useOld Whether the cli file is using CommandLineInc
	 * @return string Absolute file path with proper MW location
	 */
	public static function getMediaWikiCli( $file, $useOld = false ) {
		global $argv, $IP;

		$multiVersion = self::getInstance();
		if ( !$multiVersion ) {
			if ( $useOld ) {
				$multiVersion = self::initializeForMaintenanceOld();
			} else {
				$multiVersion = self::initializeForMaintenance();
			}
		}
		if ( $multiVersion->getDatabase() === 'testwiki' ) {
			define( 'TESTWIKI', 1 );
		}

		# Get the MediaWiki version running on this wiki...
		$version = $multiVersion->getVersion();

		# Get the correct MediaWiki path based on this version...
		$IP = dirname( __DIR__ ) . "/$version";
		// Make the script file path absolute.
		// Can't be done sooner as the version thus the actual $IP was not determined.
		$scriptIndex = $useOld ? 0 : 1;
		$scriptPath = $argv[$scriptIndex];
		if ( $scriptPath !== '' && $scriptPath[0] !== '/' && strpos( $scriptPath, '/' ) !== false ) {
			$argv[$scriptIndex] = "$IP/$scriptPath";
		}

		putenv( "MW_INSTALL_PATH=$IP" );

		if ( $file !== '' && $file[0] === '/' ) {
			return $file;
		} else {
			return "$IP/$file";
		}
	}

	/**
	 * Get a list of dblist names that contain a given wiki.
	 *
	 * This is for wmf-config array keys as interpreted by SiteConfiguration ($wgConf).
	 *
	 * @param string $dbName The wiki's database name, e.g. 'enwiki' or 'zh_min_nanwikisource'
	 * @param string $realm
	 * @return string[]
	 */
	public static function getTagsForWiki( string $dbName, string $realm = 'production' ): array {
		$dblistsIndex = require __DIR__ . '/../dblists-index.php';

		// Tolerate absence, e.g. for a labs-only wiki.
		// The structure is verified by DbListTest.
		// The freshness of the index is checked by composer buildDBLists/checkclean.
		$wikiTags = $dblistsIndex[$dbName] ?? [];

		// Replace some lists with labs-specific versions
		if ( $realm === 'labs' ) {
			foreach ( self::DB_LISTS_LABS as $tag => $fileName ) {
				// Unset any reference to the prod-specific version
				$wikiTags = array_values( array_diff( $wikiTags, [ $tag ] ) );
				$dblist = MWWikiversions::readDbListFile( $fileName );
				if ( in_array( $dbName, $dblist ) ) {
					$wikiTags[] = $tag;
				}
			}
		}

		return $wikiTags;
	}
}
