<?php
/**
 * Verify MediaWiki global variable naming convention.
 * A global name must be prefixed with 'wg'.
 */

namespace MediaWiki\Sniffs\NamingConventions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class ValidGlobalNameSniff implements Sniff {

	/**
	 * http://php.net/manual/en/reserved.variables.argv.php
	 */
	private static $PHPReserved = [
		'$GLOBALS',
		'$_SERVER',
		'$_GET',
		'$_POST',
		'$_FILES',
		'$_REQUEST',
		'$_SESSION',
		'$_ENV',
		'$_COOKIE',
		'$php_errormsg',
		'$HTTP_RAW_POST_DATA',
		'$http_response_header',
		'$argc',
		'$argv'
	];

	public $ignoreList = [];

	/**
	 * @inheritDoc
	 */
	public function register() {
		return [ T_GLOBAL ];
	}

	/**
	 * @param File $phpcsFile
	 * @param int $stackPtr The current token index.
	 * @return void
	 */
	public function process( File $phpcsFile, $stackPtr ) {
		$tokens = $phpcsFile->getTokens();

		$nameIndex = $phpcsFile->findNext( T_VARIABLE, $stackPtr + 1 );
		$semicolonIndex = $phpcsFile->findNext( T_SEMICOLON, $stackPtr + 1 );

		while ( $nameIndex < $semicolonIndex ) {
			if ( $tokens[$nameIndex ]['code'] !== T_WHITESPACE
				&& $tokens[$nameIndex ]['code'] !== T_COMMA
			) {
				$globalName = $tokens[$nameIndex]['content'];

				if ( in_array( $globalName, $this->ignoreList ) ||
					in_array( $globalName, self::$PHPReserved )
				) {
					return;
				}

				// Skip '$' and forge a valid global variable name
				$expected = '$wg' . ucfirst( substr( $globalName, 1 ) );

				// Verify global is prefixed with wg
				if ( substr( $globalName, 1, 2 ) !== 'wg' ) {
					$phpcsFile->addError(
						'Global variable "%s" is lacking \'wg\' prefix. Should be "%s".',
						$stackPtr,
						'wgPrefix',
						[ $globalName, $expected ]
					);
				} else {
					// Verify global is probably CamelCase
					$val = ord( substr( $globalName, 3, 1 ) );
					if ( !( $val >= 65 && $val <= 90 ) && !( $val >= 48 && $val <= 57 ) ) {
						$phpcsFile->addError(
							'Global variable "%s" should use CamelCase: "%s"',
							$stackPtr,
							'CamelCase',
							[ $globalName, $expected ]
						);
					}
				}
			}
			$nameIndex++;
		}
	}
}
