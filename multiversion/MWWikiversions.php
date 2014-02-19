<?php
require_once( __DIR__ . '/defines.php' );
require_once( __DIR__ . '/FormatJson.php' );

/**
 * Helper class for reading the wikiversions.json file
 */
class MWWikiversions {
	/**
	 * @param $srcPath string Path to wikiversions.json
	 * @return Array List of wiki version rows
	 */
	public static function readWikiVersionsFile( $srcPath ) {
		$data = file_get_contents( $srcPath );
		if ( $data === false ) {
			throw new Exception( "Unable to read $srcPath.\n" );
		}
		// Read the lines of the json file into an array...
		$verList = FormatJson::decode( $data, true );
		if ( !count( $verList ) ) {
			throw new Exception( "Empty table in $srcPath.\n" );
		}
		return $verList;
	}

	/**
	 * @param string $path Path to wikiversions.json
	 * @param array $wikis Array of wikis array( dbname => version )
	 */
	public static function writeWikiVersionsFile( $path, $wikis ) {
		$json = FormatJson::encode( $wikis, true );

		if ( !file_put_contents( $path, $json, LOCK_EX ) ) {
			print "Unable to write to $path.\n";
			exit( 1 );
		}
	}

	/**
	 * Get an array of DB names from a .dblist file.
	 *
	 * @param $srcPath string
	 * @return Array (DB name => position in list)
	 */
	public static function readDbListFile( $srcPath ) {
		$data = file_get_contents( $srcPath );
		if ( $data === false ) {
			throw new Exception( "Unable to read $srcPath.\n" );
		}
		return array_flip( array_filter( explode( "\n", $data ) ) );
	}
}
