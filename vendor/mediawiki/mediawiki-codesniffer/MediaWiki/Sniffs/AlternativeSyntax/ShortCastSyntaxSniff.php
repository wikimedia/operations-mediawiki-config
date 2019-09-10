<?php
/**
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

namespace MediaWiki\Sniffs\AlternativeSyntax;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class ShortCastSyntaxSniff implements Sniff {
	/**
	 * @inheritDoc
	 */
	public function register() {
		return [
			T_BOOL_CAST,
			T_INT_CAST,
		];
	}

	/**
	 * @param File $phpcsFile
	 * @param int $stackPtr Position of error
	 * @return void
	 */
	public function process( File $phpcsFile, $stackPtr ) {
		$token = $phpcsFile->getTokens()[$stackPtr];

		// The short (bool) and (int) are fine, while the longer (boolean) and (integer) aren't.
		if ( strlen( $token['content'] ) <= 6 ) {
			return;
		}

		if ( $token['code'] === T_BOOL_CAST ) {
			$fix = $phpcsFile->addFixableWarning(
				'Type-cast should use short "bool" form',
				$stackPtr,
				'NotShortBoolForm'
			);
			if ( $fix ) {
				$phpcsFile->fixer->replaceToken( $stackPtr, '(bool)' );
			}
		} else {
			$fix = $phpcsFile->addFixableWarning(
				'Type-cast should use short "int" form',
				$stackPtr,
				'NotShortIntForm'
			);
			if ( $fix ) {
				$phpcsFile->fixer->replaceToken( $stackPtr, '(int)' );
			}
		}
	}

}
