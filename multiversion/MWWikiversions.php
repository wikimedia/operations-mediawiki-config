<?php
require_once( __DIR__ . '/defines.php' );

/**
 * Helper class for reading the wikiversions.dat file
 */
class MWWikiversions {
	/**
	 * @param $srcPath string Path to wikiversions.dat
	 * @return Array List of wiki version rows
	 */
	public static function readWikiVersionsFile( $srcPath ) {
		$data = file_get_contents( $srcPath );
		if ( $data === false ) {
			throw new Exception( "Unable to read $srcPath.\n" );
		}
		// Read the lines of the dat file into an array...
		$verList = explode( "\n", $data );
		if ( !count( $verList ) ) {
			throw new Exception( "Empty table in $srcPath.\n" );
		}
		// Convert each raw line into a row array...
		$result = array();
		foreach ( $verList as $lineNo => $line ) {
			$row = self::rowFromLine( $line, $lineNo );
			if ( is_array( $row ) ) {
				$result[] = $row;
			}
		}
		return $result;
	}

	/**
	 * Get a wiki version row from a line of wikiversions.dat
	 *
	 * @param $line string
	 * @param $lineNo integer Line # from wikiversions.dat
	 * @return Array|null (dbname, version, extended version, comment)
	 */
	public static function rowFromLine( $line, $lineNo ) {
		// Strip comments and ignore comment lines...
		$len = strcspn( $line, '#' );
		if ( $len === 0 ) {
			return null; // comment line or empty line
		}
		$row = substr( $line, 0, $len );
		$comment = trim( substr( $line, $len + 1 ) ); // exclude the '#'

		// Get the column values for this row...
		$items = explode( ' ', trim( $row ) ); // cleanup w/s
		if ( count( $items ) === 3 ) {
			list( $dbName, $version, $extVersion ) = $items;
		} elseif ( count( $items ) === 2 ) {
			list( $dbName, $version ) = $items;
			$extVersion = '*'; // none
		} else {
			throw new Exception( "Invalid row on line $lineNo ('$line').\n" );
		}

		return array( $dbName, $version, $extVersion, $comment );
	}

	/**
	 * @param $row Array Wiki version row
	 * @return string Line for wikiversions.dat
	 */
	public static function lineFromRow( array $row ) {
		list( $dbName, $version, $extVersion, $comment ) = $row;
		$line = "$dbName $version $extVersion";
		if ( $comment !== '' ) {
			$line .= " #$comment";
		}
		return $line;
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
