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
		if ( !is_array( $verList ) || array_values( $verList ) === $verList ) {
			throw new Exception( "$srcPath did not decode to an associative array.\n" );
		}
		asort( $verList );
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
	 * Evaluate a dblist expression.
	 *
	 * A dblist expression contains one or more dblist file names separated by '+' and '-'.
	 *
	 * @par Example:
	 * @code
	 *  %% all.dblist - wikipedia.dblist
	 * @endcode
	 *
	 * @param $expr string
	 * @return Array
	 */
	public static function evalDbListExpression( $expr ) {
		$expr = trim( strtok( $expr, "#\n" ), "% " );
		$tokens = preg_split( '/ *([-+]) */m', $expr, 0, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY );
		$result = self::readDbListFile( $tokens[0] );
		while ( ( $op = next( $tokens ) ) && ( $term = next( $tokens ) ) ) {
			$dbs = self::readDbListFile( $term );
			if ( $op === '+' ) {
				$result = array_unique( array_merge( $result, $dbs ) );
			} else if ( $op === '-' ) {
				$result = array_diff( $result, $dbs );
			}
		}
		sort( $result );
		return $result;
	}

	/**
	 * Get an array of DB names from a .dblist file.
	 *
	 * @param $srcPath string
	 * @return Array
	 */
	public static function readDbListFile( $srcPath ) {
		$data = file_get_contents( $srcPath );
		if ( $data === false ) {
			throw new Exception( "Unable to read $srcPath.\n" );
		} else if ( substr( $data, 0, 2 ) === '%%' ) {
			$dir = getcwd();
			chdir( dirname( $srcPath ) );
			$dbs = self::evalDbListExpression( $data );
			chdir( $dir );
			return $dbs;
		} else {
			return array_filter( explode( "\n", $data ) );
		}
	}
}
