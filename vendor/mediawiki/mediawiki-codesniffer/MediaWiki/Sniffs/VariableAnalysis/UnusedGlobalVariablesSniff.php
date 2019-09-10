<?php
/**
 * Detect unused MediaWiki global variable.
 * Unused global variables should be removed.
 */

namespace MediaWiki\Sniffs\VariableAnalysis;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class UnusedGlobalVariablesSniff implements Sniff {

	/**
	 * @inheritDoc
	 */
	public function register() {
		return [ T_FUNCTION ];
	}

	/**
	 * @param File $phpcsFile
	 * @param int $stackPtr The current token index.
	 * @return void
	 */
	public function process( File $phpcsFile, $stackPtr ) {
		$tokens = $phpcsFile->getTokens();
		if ( !isset( $tokens[$stackPtr]['scope_opener'] ) ) {
			// An interface or abstract function which doesn't have a body
			return;
		}
		$scopeOpener = ++$tokens[$stackPtr]['scope_opener'];
		$scopeCloser = $tokens[$stackPtr]['scope_closer'];

		$endOfGlobal = 0;
		$globalVariables = [];
		$otherVariables = [];
		$matches = [];
		$strVariables = [];

		for ( $i = $scopeOpener; $i < $scopeCloser; $i++ ) {
			if ( $tokens[$i]['code'] === T_GLOBAL ) {
				$endOfGlobal = $phpcsFile->findEndOfStatement( $i, T_COMMA );
			} elseif ( $tokens[$i]['code'] === T_VARIABLE ) {
				if ( $i < $endOfGlobal ) {
					$globalVariables[] = [ $tokens[$i]['content'], $i ];
				} else {
					$otherVariables[$tokens[$i]['content']] = null;
				}
			} elseif ( $tokens[$i]['code'] === T_DOUBLE_QUOTED_STRING
				|| $tokens[$i]['code'] === T_HEREDOC
			) {
				preg_match_all( '/\${?(\w+)/', $tokens[$i]['content'], $matches );
				$strVariables += array_flip( $matches[1] );
			}
		}

		foreach ( $globalVariables as $global ) {
			if ( !array_key_exists( $global[0], $otherVariables )
				&& !array_key_exists( ltrim( $global[0], '$' ), $strVariables )
			) {
				$phpcsFile->addWarning(
					'Global ' . $global[0] . ' is never used.',
					$global[1],
					'UnusedGlobal' . $global[0]
				);
			}
		}
	}
}
