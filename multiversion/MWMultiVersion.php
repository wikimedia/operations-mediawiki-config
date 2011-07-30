<?php
/**
 * Class to handle basic information related to what
 * version of MediaWiki is running on a wiki installation
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
	 * To get an inststance of this class, use the statuc helper methods.
	 * @see getInstanceForWiki
	 * @see getInstanceForUploadWiki
	 */
	private function __construct() {}
	private function __clone() {}

	/**
	 * Create the version instance
	 * @return MWMultiVersion object for this wiki
	 */
	private static function createInstance() {
		if ( isset( self::$instance ) ) {
			die( "MWMultiVersion instance already set!\n" );
		}
		self::$instance = new self;
		return self::$instance;
	}

	/**
	 * Factory method to get an instance of MWMultiVersion.
	 * Use this for all wikis except calls to /w/thumb.php on upload.wikmedia.org.
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
	 * Factory method to get an instance of MWMultiVersion used
	 * for calls to /w/thumb.php on upload.wikmedia.org.
	 * @param $pathInfo the PathInfo -- $_SERVER['PATH_INFO']
	 * @return MWMultiVersion object for the wiki derived from the pathinfo
	 */
	public static function initializeForUploadWiki( $pathInfo ) {
		$instance = self::createInstance();
		$instance->setSiteInfoForUploadWiki( $pathInfo );
		return $instance;
	}

	/**
	 * Factory method to get an instance of MWMultiVersion
	 * via maintenance scripts since they need to set site and lang.
	 * @return MWMultiVersion object for the wiki derived from --wiki CLI parameter
	 */
	public static function initializeForMaintenance() {
		$instance = self::createInstance();
		$instance->setSiteInfoForMaintenance();
		return $instance;
	}

	/**
	 * Get the instance of MWMultiVersion that was previously initialized
	 * @return MWMultiVersion|null version object for the wiki
	 */
	public static function getInstance() {
		return self::$instance;
	}

	/**
	 * Derives site and lang from the parameters and sets $site and $lang on the instance
	 * @param $serverName the ServerName for this wiki -- $_SERVER['SERVER_NAME']
	 * @param $docRoot the DocumentRoot for this wiki -- $_SERVER['DOCUMENT_ROOT']
	 * @return void
	 */
	private function setSiteInfoForWiki( $serverName, $docRoot ) {
		$secure = getenv( 'MW_SECURE_HOST' );
		$matches = array();
		if ( $secure ) {
			if ( !preg_match('/^([^.]+)\.([^.]+)\./', $secure, $matches ) ) {
				die( "Invalid hostname.\n" );
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
			$site = "wikipedia";
			if ( getenv( 'MW_LANG' ) ) {
				# Language forced from some hacky script like extract2.php
				$lang = getenv( 'MW_LANG' );
			} elseif ( preg_match( '/^(?:\/usr\/local\/apache\/|\/home\/wikipedia\/)(?:htdocs|common\/docroot)\/([a-z]+)\.org/', $docRoot, $matches ) ) {
				$site = $matches[1];
				if ( preg_match( '/^(.*)\.' . preg_quote( $site ) . '\.org$/', $serverName, $matches ) ) {
					$lang = $matches[1];
					// For some special subdomains, like pa.us
					$lang = str_replace( '.', '-', $lang );
				} else if ( preg_match( '/^(.*)\.prototype\.wikimedia\.org$/', $serverName, $matches ) ) {
					$lang = $matches[1];
				} else {
					die( "Invalid host name ($serverName), can't determine language.\n" );
				}
			} elseif ( preg_match( "/^\/usr\/local\/apache\/(?:htdocs|common\/docroot)\/([a-z0-9\-_]*)$/", $docRoot, $matches ) ) {
				$site = "wikipedia";
				$lang = $matches[1];
			} else {
				die( "Invalid host name (docroot=" . $docRoot . "), can't determine language.\n" );
			}
		}
		$this->loadDBFromSite( $site, $lang );
	}

	/**
	 * Derives site and lang from the parameter and sets $site and $lang on the instance
	 * @param $pathInfo the PathInfo -- $_SERVER['PATH_INFO']
	 * @return void
	 */
	private function setSiteInfoForUploadWiki( $pathInfo ) {
		$pathBits = explode( '/', $pathInfo );
		if ( count( $pathBits ) < 3 ) {
			die( "Invalid file path info (pathinfo=" . $pathInfo . "), can't determine language.\n" );
		}
		$site = $pathBits[1];
		$lang = $pathBits[2];
		$this->loadDBFromSite( $site, $lang );
	}

	/**
	 * Gets the site and lang from the --wiki argument.
	 * This code reflects how Maintenance.php reads arguments.
	 * @return void
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
		} elseif ( in_array( $argv[0], self::wikilessScripts() ) ) {
			# For addwiki.php, the DB doesn't yet exist, and for nextJobDB.php
			# we don't care what DB we use. Assumme aawiki as Maintenance.php does.
			$dbname = 'aawiki';
			$argv = array_merge( array( $argv[0], "--wiki=$dbname" ), array_slice( $argv, 1 ) );
		}

		if ( $dbname === '' ) {
			die( "--wiki must be the first parameter.\n" );
		}

		$this->db = $dbname;
		putenv( 'MW_DBNAME=' . $dbname );
	}

	/*
	 * Return a list of scripts that don't need a --wiki param (assume aawiki)
	 * @return Array
	 */
	private static function wikilessScripts() {
		return array( 'addwiki.php', 'nextJobDB.php', 'mergeMessageFileList.php' );
	}

	/**
	 * Load the DB from the site and lang for this wiki
	 * @param $site string
	 * @param $lang string
	 * @return void
	 */
	private function loadDBFromSite( $site, $lang ) {
		if ( $site === "wikipedia" ) {
			$dbSuffix = "wiki";
		} else {
			$dbSuffix = $site;
		}
		$this->db = str_replace( "-", "_", $lang . $dbSuffix );
		putenv( 'MW_DBNAME=' . $this->db );
	}

	/**
	 * Get the DB name for this wiki
	 * @return String the database name
	 */
	public function getDatabase() {
		return $this->db;
	}

	/**
	 * Get the space-seperated list of version params for this wiki.
	 * The first item is the MW version and the optional second item
	 * an extra version parameter to use for builds and caches.
	 * @return void
	 */
	private function loadVersionInfo() {
		if ( $this->versionLoaded ) {
			return;
		}
		$this->versionLoaded = true;

		$db = dba_open( '/usr/local/apache/common/wikiversions.cdb', 'r', 'cdb' );
		if ( $db ) {
			$version = dba_fetch( "ver:{$this->db}", $db );
			if ( $version === false ) {
				die( "wikiversions.cdb has no version entry for `$db`.\n" );
			} elseif ( strpos( $version, 'php-' ) !== 0 ) {
				die( "wikiversions.cdb version entry does not start with `php-` (got `$version`).\n" );
			}
			$extraVersion = dba_fetch( "ext:{$this->db}", $db );
			if ( $extraVersion === false ) {
				die( "wikiversions.cdb has no extra version entry for `$db`.\n" );
			}
		} else {
			//trigger_error( "Unable to open wikiversions.cdb. Assuming php-1.17", E_USER_ERROR );
			$version = 'php-1.17';
			$extraVersion = '';
		}
		dba_close( $db );

		$this->version = $version;
		$this->extVersion = $extraVersion;
	}

	/**
	 * Get the version as specified in a cdb file located
	 * at /usr/local/apache/common/wikiversions.cdb.
	 * Result is of the form "php-X.XX" or "php-trunk".
	 * @return String the version directory for this wiki
	 */
	public function getVersion() {
		$this->loadVersionInfo();
		return $this->version;
	}

	/**
	 * Get the version number as specified in a cdb file located
	 * at /usr/local/apache/common/wikiversions.cdb. Do not use this
	 * to determine the path to cache or binary files, only the core MW code.
	 * @return String the version number for this wiki (e.g. "x.xx" or "trunk")
	 */
	public function getVersionNumber() {
		$this->loadVersionInfo();
		return substr( $this->version, 4 ); // remove "php-"
	}

	/*
	 * Get the version number to use for building caches & binaries for this wiki.
	 * Like getVersionNumber() but may have a dash with another string appended.
	 * Some wikis may share core MW versions but be using different extension versions.
	 * We need to keep the caches and binary builds separate for such wikis.
	 * @return String
	 */
	public function getExtendedVersionNumber() {
		$this->loadVersionInfo();
		$ver = $this->getVersionNumber();
		if ( $this->extVersion !== '' ) {
			$ver .= "-{$this->extVersion}";
		}
		return $ver;
	}
}
