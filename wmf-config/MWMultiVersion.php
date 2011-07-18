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

	private $db;
	private $site;
	private $lang;

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
			die( "MWMultiVersion instance already set!" );
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
	 * Factory method to get an instance of MWMultiVersion
	 * via maintenance scripts since they need to set site and lang.
	 * @return An MWMultiVersion object for the wiki
	 */
	public static function getInstance() {
		if ( !isset( self::$instance ) ) {
			die( "No MWMultiVersion instance initialized!" );
		}
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
				die( "invalid hostname" );
			}
			$this->lang = $matches[1];
			$this->site = $matches[2];

			// @TODO: move/use some special case dblist?
			$idioSyncratics = array( "commons", "grants", "sources", "wikimania",
				"wikimania2006", "foundation", "meta" );
			if ( in_array( $this->lang, $idioSyncratics ) ) {
				$this->site = "wikipedia";
			}
		} else {
			$this->site = "wikipedia";	
			if ( preg_match( '/^(?:\/usr\/local\/apache\/|\/home\/wikipedia\/)(?:htdocs|common\/docroot)\/([a-z]+)\.org/', $docRoot, $matches ) ) {
				$this->site = $matches[1];
				if ( preg_match( '/^(.*)\.' . preg_quote( $this->site ) . '\.org$/', $serverName, $matches ) ) {
					$this->lang = $matches[1];
					// For some special subdomains, like pa.us
					$this->lang = str_replace( '.', '-', $this->lang );
				} else if ( preg_match( '/^(.*)\.prototype\.wikimedia\.org$/', $serverName, $matches ) ) {
					$this->lang = $matches[1];
				} else {
					die( "Invalid host name ($serverName), can't determine language" );
				}
			} elseif ( preg_match( "/^\/usr\/local\/apache\/(?:htdocs|common\/docroot)\/([a-z0-9\-_]*)$/", $docRoot, $matches ) ) {
				$this->site = "wikipedia";
				$this->lang = $matches[1];
			} else {
				die( "Invalid host name (docroot=" . $docRoot . "), can't determine language." );
			}
		}
		$this->loadDBFromSite( $this->site, $this->lang );
	}

	/**
	 * Derives site and lang from the parameter and sets $site and $lang on the instance
	 * @param $pathInfo the PathInfo -- $_SERVER['PATH_INFO']
	 * @return void
	 */
	private function setSiteInfoForUploadWiki( $pathInfo ) {
		$pathBits = explode( '/', $pathInfo );
		if ( count( $pathBits ) < 3 ) {
			die( "Invalid file path info (pathinfo=" . $pathInfo . "), can't determine language." );
		}
		$this->site = $pathBits[1];
		$this->lang = $pathBits[2];
		$this->loadDBFromSite( $this->site, $this->lang );
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
		if ( substr( $argv[1], 0, 7 ) === '--wiki=' ) {
			$dbname = substr( $argv[1], 7 );
		} elseif ( $argv[0] === 'addwiki.php' ) {
			# Most scripts assume that the wiki already exists. addwiki.php is
			# obviously an exception. Go ahead and assumme aawiki as normal.
			$dbname = 'aawiki';
			$argv = array_merge( array( $argv[0], "--wiki=$dbname" ), array_slice( $argv, 1 ) );
		}

		if ( $dbname === '' ) {
			die( "--wiki must be the first parameter." );
		}

		$this->db = $dbname;
		putenv( 'MW_DBNAME=' . $dbname );
	}

	/**
	 * Load the DB from the site and lang for this wiki
	 * @param $site string
	 * @param $lang string
	 * @return void
	 */
	private function loadDBFromSite( $site, $lang ) {
		if ( $site == "wikipedia" ) {
			$dbSuffix = "wiki";
		} else {
			$dbSuffix = $site;
		}
		$this->db = str_replace( "-", "_", $lang . $dbSuffix );
		putenv( 'MW_DBNAME=' . $this->db );
	}

	/**
	 * Load the site and lang for this wiki from the db name
	 * @param $db string
	 * @param $conf SiteConfiguration object
	 * @return void
	 */
	private function loadSiteFromDB( $db, SiteConfiguration $conf ) {
		list( $this->site, $this->lang ) = $conf->siteFromDB( $db );
	}

	/**
	 * Get the site for this wiki
	 * @param $conf SiteConfiguration object
	 * @return String site. Eg: wikipedia, wikinews, wikiversity
	 */
	public function getSite( SiteConfiguration $conf ) {
		if ( $this->site === null ) {
			$this->loadSiteFromDB( $this->db, $conf );
		}
		return $this->site;
	}

	/**
	 * Get the lang for this wiki
	 * @param $conf SiteConfiguration object
	 * @return String lang Eg: en, de, ar, hi
	 */
	public function getLang( SiteConfiguration $conf ) {
		if ( $this->lang === null ) {
			$this->loadSiteFromDB( $this->db, $conf );
		}
		return $this->lang;
	}

	/**
	 * Get the DB name for this wiki
	 * @return String the database name
	 */
	public function getDatabase() {
		return $this->db;
	}

	/**
	 * Get the version as specified in a cdb file located in /usr/local/apache/common/wikiversions.db
	 * The key should be the dbname and the version should be the version directory for this wiki
	 * @return String the version wirectory for this wiki
	 */
	public function getVersion() {
		$db = dba_open( '/usr/local/apache/common/wikiversions.db', 'r', 'cdb' );
		if ( $db ) {
			$version = dba_fetch( $this->getDatabase(), $db );
		} else {
			//trigger_error( "Unable to open /usr/local/apache/common/wikiversions.db. Assuming php-1.17", E_USER_ERROR );
			$version = 'php-1.17';
		}
		return $version;
	}
}
