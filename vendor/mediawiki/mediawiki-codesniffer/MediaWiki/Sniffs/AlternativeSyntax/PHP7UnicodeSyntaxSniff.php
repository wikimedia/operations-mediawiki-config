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

/**
 * Prevent usage of the new PHP 7 unicode escape syntax
 *
 * @see https://wiki.php.net/rfc/unicode_escape
 */
class PHP7UnicodeSyntaxSniff implements Sniff {
	/**
	 * @inheritDoc
	 */
	public function register() {
		return [
			T_CONSTANT_ENCAPSED_STRING,
			T_DOUBLE_QUOTED_STRING,
			T_HEREDOC
		];
	}

	/**
	 * @param File $phpcsFile
	 * @param int $stackPtr The current token index.
	 * @return void
	 */
	public function process( File $phpcsFile, $stackPtr ) {
		$tokens = $phpcsFile->getTokens();
		$info = $tokens[$stackPtr];
		if ( $info['code'] === T_CONSTANT_ENCAPSED_STRING && $info['content'][0] === "'" ) {
			// Single quoted string
			return;
		}

		$matched = preg_match( '/\\\u\{[0-9A-Fa-f]*\}/', $info['content'] );
		if ( $matched ) {
			$phpcsFile->addError(
				'PHP 7 Unicode escape syntax not allowed',
				$stackPtr,
				'NotAllowed'
			);
		}
	}
}
