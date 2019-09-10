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

	/**
	 * A list of global variable prefixes allowed.
	 *
	 * @var array
	 */
	public $allowedPrefixes = [ 'wg' ];

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

				// Determine if a simple error message can be used

				if ( count( $this->allowedPrefixes ) === 1 ) {
					// Skip '$' and forge a valid global variable name
					$expected = '$' . $this->allowedPrefixes[0] . ucfirst( substr( $globalName, 1 ) );

					// Build message telling you the allowed prefix
					$msg = 'Global variable "%s" is lacking \''
						. $this->allowedPrefixes[0]
						. '\' prefix. Should be "%s".';
				// If there are no prefixes specified, don't do anything
				} elseif ( $this->allowedPrefixes === [] ) {
					return;
				} else {
					// Build a list of forged valid global variable names
					$expected = 'one of "$'
						. implode( ucfirst( substr( $globalName, 1 ) . '", "$' ), $this->allowedPrefixes )
						. ucfirst( substr( $globalName, 1 ) )
						. '"';

					// Build message telling you which prefixes are allowed
					$msg = 'Global variable "%s" is lacking an allowed prefix (one of \''
						. implode( '\', \'', $this->allowedPrefixes )
						. '\'). Should be "%s"';
				}

				// Verify global is prefixed with an allowed prefix
				if ( !in_array( substr( $globalName, 1, 2 ), $this->allowedPrefixes ) ) {
					$phpcsFile->addError(
						$msg,
						$stackPtr,
						'allowedPrefix',
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
