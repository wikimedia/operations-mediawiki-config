<?php

class TimelineTest extends PHPUnit\Framework\TestCase {

	/**
	 * Ploticus strip the '.ttf' suffix from the font name and then fails
	 * to find the .ttf font in /fonts/. Make sure we strip the suffix both
	 * in the config and in the fonts files. T22825.
	 *
	 * @dataProvider wgTimelineFontFileValues
	 */
	public function testTimelineFontFileDoesNotHaveTtfSuffix( $filename ) {
		$this->assertStringEndsNotWith( '.ttf', $filename );
	}

	/**
	 * @dataProvider wgTimelineFontFileValues
	 */
	public function testTimelineFontFileEexists( $filename ) {
		$this->assertFileExists( __DIR__ . "/../fonts/" . $filename );
	}

	/**
	 * Parse wmf-config/timeline.php and find values for $wgTimelineFontFile
	 */
	public static function wgTimelineFontFileValues() {
		$testCases = [];
		$conf = file_get_contents( __DIR__ . '/../wmf-config/timeline.php' );
		$tokens = token_get_all( $conf );

		$foundVariable = false;
		foreach ( $tokens as $token ) {
			if ( is_array( $token )
				&& $token[0] == T_VARIABLE
				&& $token[1] == '$wgTimelineFontFile'
			) {
				$foundVariable = true;
				continue;
			}
			if ( !$foundVariable ) {
				// Skip until we find $wgTimelineFontFile
				continue;
			}

			// Skip ' = ' to reach the actual value being set
			if ( $token === '='
				|| is_array( $token ) && $token[0] == T_WHITESPACE
			) {
				continue;
			}

			if ( $token[0] !== T_CONSTANT_ENCAPSED_STRING ) {
				throw new Exception( 'Unexpected token type assigned to $wgTimelineFontFile: ' . token_name( $token[0] ) );
			}

			// Found assigned value
			$testCases[] = [ trim( $token[1], '\'' ) ];
			// Reset
			$foundVariable = false;
		}
		return $testCases;
	}

}
