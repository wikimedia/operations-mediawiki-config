<?php
require_once __DIR__ . '/defines.php';

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
	 * Evaluate a dblist expression.
	 *
	 * A dblist expression contains one or more dblist file names separated by '+' and '-'.
	 *
	 * @par Example:
	 * @code
	 *  %% all.dblist - wikipedia.dblist
	 * @endcode
	 *
	 * @param string $expr
	 * @return array
	 */
	public static function evalDbListExpression( $expr ) {
		$expr = trim( strtok( $expr, "#\n" ), "% " );
		$tokens = preg_split( '/ +([-+&]) +/m', $expr, 0, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY );
		$result = self::readDbListFile( basename( $tokens[0], '.dblist' ) );
		// phpcs:ignore MediaWiki.ControlStructures.AssignmentInControlStructures.AssignmentInControlStructures
		// phpcs:ignore Generic.CodeAnalysis.AssignmentInCondition.FoundInWhileCondition
		while ( ( $op = next( $tokens ) ) && ( $term = next( $tokens ) ) ) {
			$dbs = self::readDbListFile( basename( $term, '.dblist' ) );
			if ( $op === '+' ) {
				$result = array_unique( array_merge( $result, $dbs ) );
			} elseif ( $op === '-' ) {
				$result = array_diff( $result, $dbs );
			} elseif ( $op === '&' ) {
				$result = array_intersect( $result, $dbs );
			}
		}
		sort( $result );
		return $result;
	}

	/**
	 * Get an array of DB names from a .dblist file.
	 *
	 * @param string $dblist
	 * @return string[]
	 */
	public static function readDbListFile( $dblist ) {
		$fileName = dirname( __DIR__ ) . '/dblists/' . $dblist . '.dblist';
		$lines = @file( $fileName, FILE_IGNORE_NEW_LINES );
		if ( $lines === false ) {
			throw new Exception( __METHOD__ . ": unable to read $dblist." );
		}

		$dbs = [];
		foreach ( $lines as $line ) {
			// Ignore empty lines and lines that are comments
			if ( $line !== '' && $line[0] !== '#' ) {
				$dbs[] = $line;
			}
		}
		return $dbs;
	}

	/**
	 * @return array<string,string[]>
	 */
	public static function getAllDbListsForCLI() {
		$lists = [];
		foreach ( glob( __DIR__ . '/../dblists/*.dblist' ) as $filename ) {
			$basename = basename( $filename, '.dblist' );
			$lists[$basename] = self::readDbListFile( $basename );
		}
		return $lists;
	}

	/**
	 * @return array List of wiki versions
	 */
	public static function getAvailableBranchDirs() {
		return glob( MEDIAWIKI_DEPLOYMENT_DIR . '/php-*', GLOB_ONLYDIR ) ?: [];
	}
}
