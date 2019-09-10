<?php
/**
 * Sniff to warn when keywords are used as functions, such as:
 * Pass: clone $obj
 * Fail: clone( $obj )
 * Pass: require 'path/to/file.php';
 * Fail: require( 'path/to/file' );
 *
 * Covers:
 * * clone
 * * require
 * * require_once
 * * include
 * * include_once
 */

namespace MediaWiki\Sniffs\ExtraCharacters;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class ParenthesesAroundKeywordSniff implements Sniff {

	/**
	 * @inheritDoc
	 */
	public function register() {
		return [
			T_CLONE,
			T_REQUIRE,
			T_REQUIRE_ONCE,
			T_INCLUDE,
			T_INCLUDE_ONCE,
		];
	}

	/**
	 * @param File $phpcsFile
	 * @param int $stackPtr The current token index.
	 * @return void
	 */
	public function process( File $phpcsFile, $stackPtr ) {
		$tokens = $phpcsFile->getTokens();
		$nextToken = $tokens[$stackPtr + 1];
		$nextSecondToken = $tokens[$stackPtr + 2];

		if (
			(
				$nextToken['code'] === T_WHITESPACE &&
				$nextSecondToken['code'] === T_OPEN_PARENTHESIS
			) ||
			$nextToken['code'] === T_OPEN_PARENTHESIS
		) {
			$fix = $phpcsFile->addFixableWarning(
				$tokens[$stackPtr]['content'] . ' keyword must not be used as a function.',
				$stackPtr + 1,
				'ParenthesesAroundKeywords' );

			if ( $fix ) {
				if ( $nextToken['code'] === T_OPEN_PARENTHESIS ) {
					if ( $nextSecondToken['code'] === T_WHITESPACE ) {
						$phpcsFile->fixer->replaceToken( $stackPtr + 1, '' );
					} else {
						// Ensure the both tokens are not mangled together without space
						$phpcsFile->fixer->replaceToken( $stackPtr + 1, ' ' );
					}
					$closer = $nextToken['parenthesis_closer'];
					$phpcsFile->fixer->replaceToken( $closer, '' );
				} else {
					$phpcsFile->fixer->replaceToken( $stackPtr + 2, '' );
					$closer = $nextSecondToken['parenthesis_closer'];
					$phpcsFile->fixer->replaceToken( $closer, '' );
					if ( $tokens[$stackPtr + 3]['code'] === T_WHITESPACE ) {
						$phpcsFile->fixer->replaceToken( $stackPtr + 3, '' );
					}
				}
			}
		}
	}
}
