<?php
require_once( dirname( __FILE__ ) . '/defines.php' );
require_once( dirname( __FILE__ ) . '/MWRealm.php' );

/**
 * Class to handle basic information related to what
 * version of MediaWiki is running on a wiki installation.
 *
 * Avoid setting environmental or globals variables here for OOP.
 */
class MWMultiVersion {
	/**
	 * @var MWMultiVersion
	 */
	private static $instance;

	/**
	 * @var string
	 */
	private $db;
	/**
	 * @var string
	 */
	private $version;
	/**
	 * @var string
	 */
	private $extVersion;
	/**
	 * @var bool
	 */
	private $versionLoaded = false;

	/**
	 * To get an inststance of this class, use the static helper methods.
	 * @see getInstanceForWiki
	 * @see getInstanceForUploadWiki
	 */
	private function __construct() {}
	private function __clone() {}

	/**
	 * Create a multiversion object based on a dbname
	 * @param $dbName string
	 * @return MWMultiVersion object for this wiki
	 */
	public static function newFromDBName( $dbName ) {
		$m = new self();
		$m->db = $dbName;
		return $m;
	}

	/**
	 * Create the singleton version instance
	 * @return MWMultiVersion object for this wiki
	 */
	private static function createInstance() {
		if ( isset( self::$instance ) ) {
			self::error( "MWMultiVersion instance already set!\n" );
		}
		self::$instance = new self;
		return self::$instance;
	}

	/**
	 * Initialize and get the singleton instance of MWMultiVersion.
	 * Use this for all web hits except to /w/thumb.php on upload.wikmedia.org.
	 * @param $serverName the ServerName for this wiki -- $_SERVER['SERVER_NAME']
	 * @param $docRoot the DocumentRoot for this wiki -- $_SERVER['DOCUMENT_ROOT']
	 * @return MWMultiVersion object for this wiki
	 */
	public static function initializeForWiki( $serverName, $docRoot ) {
		$instance = self::createInstance();
		$instance->setSiteInfoForWiki( $serverName, $docRoot );
		return $instance;
	}

	/**
	 * Initialize and get the singleton instance of MWMultiVersion.
	 * Use this for web hits to /w/thumb.php on upload.wikmedia.org.
	 * @param $pathInfo the PathInfo -- $_SERVER['PATH_INFO']
	 * @return MWMultiVersion object for the wiki derived from the pathinfo
	 */
	public static function initializeForUploadWiki( $pathInfo ) {
		$instance = self::createInstance();
		$instance->setSiteInfoForUploadWiki( $pathInfo );
		return $instance;
	}

	/**
	 * Initialize and get the singleton instance of MWMultiVersion.
	 * Use this for PHP CLI hits to maintenance scripts.
	 * @return MWMultiVersion object for the wiki derived from --wiki CLI parameter
	 */
	public static function initializeForMaintenance() {
		$instance = self::createInstance();
		$instance->setSiteInfoForMaintenance();
		return $instance;
	}

	/**
	 * Get the singleton instance of MWMultiVersion that was previously initialized
	 * @return MWMultiVersion|null version object for the wiki
	 */
	public static function getInstance() {
		return self::$instance;
	}

	/**
	 * Derives site and lang from the parameters and sets $site and $lang on the instance
	 * @param $serverName the ServerName for this wiki -- $_SERVER['SERVER_NAME']
	 * @param $docRoot the DocumentRoot for this wiki -- $_SERVER['DOCUMENT_ROOT']
	 */
	private function setSiteInfoForWiki( $serverName, $docRoot ) {
		# The old secure.wikimedia.org gateway, set a specific env variable for us
		# to react differently.
		$secure = getenv( 'MW_SECURE_HOST' );

		$matches = array();
		if ( $secure ) {
			// secure.wikimedia.org

			if ( !preg_match('/^([^.]+)\.([^.]+)\./', $secure, $matches ) ) {
				self::error( "Invalid hostname.\n" );
			}
			$lang = $matches[1];
			$site = $matches[2];

			// @TODO: move/use some special case dblist?
			$idioSyncratics = array( "commons", "grants", "sources", "wikimania",
				"wikimania2006", "foundation", "meta" );
			if ( in_array( $lang, $idioSyncratics ) ) {
				$site = "wikipedia";
			}
		} else {
			// Most requests ;)

			$site = "wikipedia";
			if ( getenv( 'MW_LANG' ) ) {
				# Language forced from some hacky script like extract2.php
				$lang = getenv( 'MW_LANG' );
			} elseif ( preg_match( '/^(?:\/srv\/deployment\/mediawiki\/)(?:htdocs|common\/docroot)\/([a-z]+)\.org/', $docRoot, $matches ) ) {
				# This is the poor man / hacky routing engine for WMF cluster
				$site = $matches[1];
				if ( preg_match( '/^(.*)\.' . preg_quote( $site ) . '\.org$/', $serverName, $matches ) ) {
					$lang = $matches[1];
					// For some special subdomains, like pa.us
					$lang = str_replace( '.', '-', $lang );
				} else if ( preg_match( '/^(.*)\.prototype\.wikimedia\.org$/', $serverName, $matches ) ) {
					// http://en.prototype.wikimedia.org/
					$lang = $matches[1];
				} else if ( preg_match( '/^([^.]+)\.[^.]+\.beta\.wmflabs\.org$/', $serverName, $matches ) ) {
					// http://en.wikipedia.beta.wmflabs.org/
					$lang = $matches[1];
				} else {
					self::error( "Invalid host name ($serverName), can't determine language.\n" );
				}
			} elseif ( preg_match( "/^\/srv\/deployment\/mediawiki\/(?:htdocs|common\/docroot)\/([a-z0-9\-_]*)$/", $docRoot, $matches ) ) {
				$site = "wikipedia";
				$lang = $matches[1];
			} else {
				self::error( "Invalid host name (docroot=" . $docRoot . "), can't determine language.\n" );
			}
		}
		$this->loadDBFromSite( $site, $lang );
	}

	/**
	 * Derives site and lang from the parameter and sets $site and $lang on the instance
	 * @param $pathInfo the PathInfo -- $_SERVER['PATH_INFO']
	 */
	private function setSiteInfoForUploadWiki( $pathInfo ) {
		$pathBits = explode( '/', $pathInfo );
		if ( count( $pathBits ) < 3 ) {
			self::error( "Invalid file path info (pathinfo=" . $pathInfo . "), can't determine language.\n" );
		}
		$site = $pathBits[1];
		$lang = $pathBits[2];
		$this->loadDBFromSite( $site, $lang );
	}

	/**
	 * Gets the site and lang from the --wiki argument.
	 * This code reflects how Maintenance.php reads arguments.
	 */
	private function setSiteInfoForMaintenance() {
		global $argv;

		$dbname = '';
		# The --wiki param must the second argument to to avoid
		# any "options with args" ambiguity (see Maintenance.php).
		if ( isset( $argv[1] ) && $argv[1] === '--wiki' ) {
			$dbname = isset( $argv[2] ) ? $argv[2] : ''; // "script.php --wiki dbname"
		} elseif ( isset( $argv[1] ) && substr( $argv[1], 0, 7 ) === '--wiki=' ) {
			$dbname = substr( $argv[1], 7 ); // "script.php --wiki=dbname"
		} elseif ( isset( $argv[1] ) && substr( $argv[1], 0, 2 ) !== '--' ) {
			$dbname = $argv[1]; // "script.php dbname"
		}

		if ( $dbname === '' ) {
			self::error( "--wiki must be the first parameter.\n" );
		}

		$this->db = $dbname;
	}

	/**
	 * Load the DB from the site and lang for this wiki
	 * @param $site string
	 * @param $lang string
	 */
	private function loadDBFromSite( $site, $lang ) {
		if ( $site === "wikipedia" ) {
			$dbSuffix = "wiki";
		} else {
			$dbSuffix = $site;
		}
		$this->db = str_replace( "-", "_", $lang . $dbSuffix );
	}

	/**
	 * Get the DB name for this wiki
	 * @return String the database name
	 */
	public function getDatabase() {
		return $this->db;
	}

	/**
	 * Handler for the wfShellMaintenanceCmd hook.
	 * This converts shell commands like "php $IP/maintenance/foo.php" into
	 * commands that use the "MWScript.php" wrapper, for example:
	 * "php /srv/deployment/mediawiki/common/multiversion/MWScript.php maintenance/foo.php"
	 *
	 * @param &$script string
	 * @param &$params Array
	 * @param &$options Array
	 * @return boolean
	 */
	public static function onWfShellMaintenanceCmd( &$script, array &$params, array &$options ) {
		global $IP;
		if ( strpos( $script, "{$IP}/" ) === 0 ) {
			$script = substr( $script, strlen( "{$IP}/" ) );
			$options['wrapper'] = dirname( __FILE__ ) . '/MWScript.php';
		}
		return true;
	}

	/**
	 * Get the space-seperated list of version params for this wiki.
	 * The first item is the MW version and the optional second item
	 * an extra version parameter to use for builds and caches.
	 */
	private function loadVersionInfo() {
		if ( $this->versionLoaded ) {
			return;
		}
		$this->versionLoaded = true;

		$db = dba_open( MULTIVER_CDB_DIR . '/wikiversions.cdb', 'r', 'cdb' );
		if ( $db ) {
			$version = dba_fetch( "ver:{$this->db}", $db );
			if ( $version === false ) {
				$extraVersion = false;
			} else {
				$extraVersion = dba_fetch( "ext:{$this->db}", $db );
				if ( $extraVersion === false ) {
					self::error( "wikiversions.cdb has no extra version entry for `$db`.\n" );
				}
			}
			dba_close( $db );
		} else {
			self::error( "Unable to open wikiversions.cdb.\n" );
		}

		$this->version = $version;
		$this->extVersion = $extraVersion;
	}

	/**
	 * Sanity check that this wiki actually exists.
	 * @return bool
	 */
	private function assertNotMissing() {
		if ( $this->isMissing() ) {
			self::error( "wikiversions.cdb has no version entry for `{$this->db}`.\n" );
		}
	}

	/**
	 * Check if this wiki is *not* specified in a cdb file
	 * located at /srv/deployment/mediawiki/common/wikiversions.cdb.
	 * @return bool
	 */
	public function isMissing() {
		$this->loadVersionInfo();
		return ( $this->version === false );
	}

	/**
	 * Get the version as specified in a cdb file located
	 * at /srv/deployment/mediawiki/common/wikiversions.cdb.
	 * Result is of the form "php-X.XX" or "php-trunk".
	 * @return String the version directory for this wiki
	 */
	public function getVersion() {
		$this->loadVersionInfo();
		$this->assertNotMissing(); // caller should have checked isMissing()
		return $this->version;
	}

	/**
	 * Get the version number as specified in a cdb file located
	 * at /srv/deployment/mediawiki/common/wikiversions.cdb. Do not use this
	 * to determine the path to cache or binary files, only the core MW code.
	 * @return String the version number for this wiki (e.g. "x.xx" or "trunk")
	 */
	public function getVersionNumber() {
		$this->loadVersionInfo();
		$this->assertNotMissing(); // caller should have checked isMissing()
		return $this->version;
	}

	/**
	 * Get the version number to use for building caches & binaries for this wiki.
	 * Like getVersionNumber() but may have a dash with another string appended.
	 * Some wikis may share core MW versions but be using different extension versions.
	 * We need to keep the caches and binary builds separate for such wikis.
	 * @return String
	 */
	public function getExtendedVersionNumber() {
		$this->loadVersionInfo();
		$this->assertNotMissing(); // caller should have checked isMissing()
		$ver = $this->getVersionNumber();
		if ( $this->extVersion !== '' && $this->extVersion !== '*' ) {
			$ver .= "-{$this->extVersion}";
		}
		return $ver;
	}

	/**
	 * Error out with a die() message
	 * @param $msg String
	 * @return void
	 */
	private static function error( $msg ) {
		$msg = (string)$msg;
		if ( php_sapi_name() !== 'cli' ) {
			$msg = htmlspecialchars( $msg );
		}
		header( 'HTTP/1.1 500 Internal server error' );
		echo $msg;
		trigger_error( $msg, E_USER_ERROR );
		exit( 1 ); // sanity
	}
}
