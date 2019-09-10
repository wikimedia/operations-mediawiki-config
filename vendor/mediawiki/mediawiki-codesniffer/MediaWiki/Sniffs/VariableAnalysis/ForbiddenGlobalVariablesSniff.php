<?php
/**
 * Detect use of discouraged global variables.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @file
 */

namespace MediaWiki\Sniffs\VariableAnalysis;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class ForbiddenGlobalVariablesSniff implements Sniff {

	/**
	 * Forbidden globals
	 */
	private $forbiddenGlobals = [
		'$parserMemc',
		'$wgTitle',
	];

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

		$globalLine = 0;
		$globalVariables = [];

		for ( $i = $scopeOpener; $i < $scopeCloser; $i++ ) {
			if ( $tokens[$i]['code'] === T_GLOBAL ) {
				$globalLine = $tokens[$i]['line'];
			} elseif ( $tokens[$i]['code'] === T_VARIABLE && $tokens[$i]['line'] === $globalLine ) {
				$globalVariables[] = [ $tokens[$i]['content'], $i ];
			}
		}
		foreach ( $globalVariables as $global ) {
			if ( in_array( $global[0], $this->forbiddenGlobals ) ) {
				$phpcsFile->addWarning(
					"Global {$global[0]} should not be used.",
					$global[1],
					'ForbiddenGlobal' . $global[0]
				);
			}
		}
	}
}
