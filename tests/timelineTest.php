<?php

class timelineTest extends PHPUnit\Framework\TestCase {

	/**
	 * Ploticus strip the '.ttf' suffix from the font name and then fails
	 * to find the .ttf font in /fonts/. Make sure we strip the suffix both
	 * in the config and in the fonts files. T22825.
	 *
	 * @dataProvider wgTimelineFontFileValues
	 */
	function testTimelineFontFileDoesNotHaveTtfSuffix( $filename ) {
		$this->assertStringEndsNotWith( '.ttf', $filename );
	}

	/**
	 * @dataProvider wgTimelineFontFileValues
	 */
	function testTimelineFontFileEexists( $filename ) {
		$this->assertFileExists( __DIR__ . "/../fonts/" . $filename );
	}

	/**
	 * Parse wmf-config/timeline.php and find values for $wgTimelineFontFile
	 */
	public static function wgTimelineFontFileValues() {
		$testCases = [];
		$conf = file_get_contents( __DIR__ . '/../wmf-config/timeline.php' );
		$tokens = ( token_get_all( $conf ) );

		while ( $token = each( $tokens )[1] ) {
			# Skip until we find $wgTimelineFontFile
			if ( !(
					is_array( $token )
					&& $token[0] == T_VARIABLE
					&& $token[1] == '$wgTimelineFontFile'
			) ) {
				continue;
			}

			while ( $next_token = next( $tokens ) ) {
				# Skip ' = ' to reach the actual value being set
				if (
					$next_token == '='
					|| is_array( $next_token ) && $next_token[0] == T_WHITESPACE ) {
					continue;
				}
				break;
			}
			self::assertInternalType( 'array', $next_token );
			self::assertEquals(
				T_CONSTANT_ENCAPSED_STRING,
				$next_token[0],
				'Test suite expects $wgTimelineFontFile to be set to a string' );

			$testCases[] = [ trim( $next_token[1], '\'' ) ];
		}
		return $testCases;
	}

}
