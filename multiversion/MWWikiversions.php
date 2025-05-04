<?php
require_once __DIR__ . '/defines.php';
require_once __DIR__ . '/../src/WmfConfig.php';

use Wikimedia\MWConfig\WmfConfig;

/**
 * Helper class for reading the wikiversions.json file
 */
class MWWikiversions {
	/**
	 * @param string $srcPath Path to wikiversions.json
	 * @return array List of wiki version rows
	 */
	public static function readWikiVersionsFile( $srcPath ) {
		$data = file_get_contents( $srcPath );
		if ( $data === false ) {
			throw new Exception( "Unable to read $srcPath.\n" );
		}
		// Read the lines of the json file into an array...
		$verList = json_decode( $data, true );
		if ( !is_array( $verList ) || array_values( $verList ) === $verList ) {
			throw new Exception( "$srcPath did not decode to an associative array.\n" );
		}
		asort( $verList );
		return $verList;
	}

	/**
	 * @param string $path Path to wikiversions.php
	 * @param array $wikis Array of wikis [ dbname => version ]
	 */
	public static function writePHPWikiVersionsFile( $path, $wikis ) {
		$php = "<?php\nreturn " . var_export( $wikis, true ) . ";\n";

		if ( !file_put_contents( $path, $php, LOCK_EX ) ) {
			print "Unable to write to $path.\n";
			exit( 1 );
		}
	}

	/**
	 * @param string $path Path to wikiversions.json
	 * @param array $wikis Array of wikis [ dbname => version ]
	 */
	public static function writeWikiVersionsFile( $path, $wikis ) {
		$json = json_encode(
			$wikis,
			JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
		) . "\n";

		if ( !file_put_contents( $path, $json, LOCK_EX ) ) {
			print "Unable to write to $path.\n";
			exit( 1 );
		}
	}

	/**
	 * NOTE: Called at operations/puppet.git:/modules/scap/files/expanddblist.
	 *
	 * @param string $expr
	 * @return array
	 */
	public static function evalDbListExpression( $expr ) {
		return WmfConfig::evalDbExpressionForCli( $expr );
	}

	/**
	 * Get an array of DB names from a .dblist file.
	 *
	 * @param string $dblist
	 * @return string[]
	 */
	public static function readDbListFile( $dblist ) {
		return WmfConfig::readDbListFile( $dblist );
	}

	/**
	 * @return array List of wiki versions
	 */
	public static function getAvailableBranchDirs() {
		return glob( MEDIAWIKI_DEPLOYMENT_DIR . '/php-*', GLOB_ONLYDIR ) ?: [];
	}
}
