<?php
# Find out MediaWiki using MW_INSTALL_PATH environnement variable
# This is similar to MediaWiki Maintenance::__construct()
$mwFound = false;

global $IP;
if( strval( getenv( 'MW_INSTALL_PATH' ) ) !== '' ) {
	$IP = getenv( 'MW_INSTALL_PATH' );
	$mwFound = file_exists( $IP."/includes/DefaultSettings.php" );
}

if( !$mwFound ) {
	print <<<EOD

This test suite requires a checkout of a MediaWiki wmf branch with module
updated.

Specify your local checkout using the MW_INSTALL_PATH environement
variable:

  MW_INSTALL_PATH=~/project/mediawiki phpunit

EOD;
	exit(1);
}
# Avoid cluettering the global namespace
unset( $mwFound );

# Pretend we are a valid entry point
define( 'MEDIAWIKI', true );

// Load the shared utilities classes from here!
require_once( dirname( __FILE__ ) . "/DBList.php" );
require_once( dirname( __FILE__ ) . "/Provide.php" );
require_once( "$IP/includes/Init.php" );
require_once( "$IP/includes/AutoLoader.php" );
require_once( "$IP/includes/profiler/Profiler.php" );
require_once( "$IP/includes/Defines.php" );
require_once( "$IP/includes/DefaultSettings.php" );

$wgShowExceptionDetails = true;

### Additional files not in git

# PrivateSettings contains password and is not in git
touch( __DIR__ . "/../wmf-config/PrivateSettings.php" );
# Emulate PrivateSettings:
$wmfSwiftConfig = array(
	'authUrl' => 'http://localhost/',
	'user'    => 'noswiftuserconfigured',
	'key'     => 'noswiftkeyconfigured',
);
$wmgCaptchaSecret = null;
$wmgMFRemotePostFeedbackUsername = null;
$wmgMFRemotePostFeedbackPassword = null;

touch( __DIR__ . "/../wmf-config/checkers.php" );

### End of additional files not in git


# Fake multiversion class
class MWMultiVersion {

	public $db;
	private static $instance = null;

	public static function newFromDBName( $dbName ) {
		$m = new self();
		$m->db = $dbName;
		self::$instance = $m;
		return $m;
	}

	public static function getInstance() {
		if( is_null( self::$instance ) ) {
			throw new Exception( __METHOD__ . " has no instance. "
			   . "Initializize it with MWMultiVersion::newFromDBName( 'langcode' );"
			);
		}
		return self::$instance;
	}

	# Accessors

	function getDatabase() {
		return $this->db;
	}
	function getVersionNumber() {
		return "0.1-tests";
	}
	function getExtendedVersionNumber() {
		return "0.1-tests~extended";
	}
}
