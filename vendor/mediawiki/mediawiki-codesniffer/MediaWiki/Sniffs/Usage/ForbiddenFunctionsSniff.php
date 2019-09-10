<?php
/**
 * Copyright (C) 2017 Kunal Mehta <legoktm@member.fsf.org>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

namespace MediaWiki\Sniffs\Usage;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

class ForbiddenFunctionsSniff implements Sniff {

	/**
	 * Function => Replacement
	 *
	 * @var string[]
	 */
	private $functions = [
		'is_integer' => 'is_int',
		'is_writeable' => 'is_writable',
		'join' => 'implode',
		'key_exists' => 'array_key_exists',
		'pos' => 'current',
		'sizeof' => 'count',
		'assert' => false,
		'extract' => false,
		// Deprecated in PHP 7.2
		'create_function' => false,
		'each' => false,
		'parse_str' => false,
		'mb_parse_str' => false,
	];

	/**
	 * Number of arguments to be forbidden
	 *
	 * @var int[]
	 */
	private $functionsArgCount = [
		'parse_str' => 1,
		'mb_parse_str' => 1,
	];

	/**
	 * @inheritDoc
	 */
	public function register() {
		return [ T_STRING ];
	}

	/**
	 * @param File $phpcsFile
	 * @param int $stackPtr The current token index.
	 * @return void
	 */
	public function process( File $phpcsFile, $stackPtr ) {
		$tokens = $phpcsFile->getTokens();

		// Check if the function is one of the bad ones
		$funcName = $tokens[$stackPtr]['content'];
		if ( !isset( $this->functions[$funcName] ) ) {
			return;
		}

		$ignore = [
			T_DOUBLE_COLON => true,
			T_OBJECT_OPERATOR => true,
			T_FUNCTION => true,
			T_CONST => true,
		];

		// Check to make sure it's a PHP function (not $this->, etc.)
		$prevToken = $phpcsFile->findPrevious( T_WHITESPACE, ( $stackPtr - 1 ), null, true );
		if ( isset( $ignore[$tokens[$prevToken]['code']] ) ) {
			return;
		}
		$nextToken = $phpcsFile->findNext( Tokens::$emptyTokens, $stackPtr + 1, null, true );
		if ( $tokens[$nextToken]['code'] !== T_OPEN_PARENTHESIS ) {
			return;
		}

		// Check argument count
		if ( isset( $this->functionsArgCount[$funcName] ) ) {
			$argCount = $this->argCount( $phpcsFile, $nextToken );
			if ( $argCount !== $this->functionsArgCount[$funcName] ) {
				// Nothing to replace
				return;
			}
		}

		$replacement = $this->functions[$funcName];
		if ( $replacement ) {
			$fix = $phpcsFile->addFixableWarning(
				"Use $replacement() instead of $funcName",
				$stackPtr,
				$funcName
			);
			if ( $fix ) {
				$phpcsFile->fixer->replaceToken( $stackPtr, $replacement );
			}
		} else {
			if ( isset( $this->functionsArgCount[$funcName] ) ) {
				$phpcsFile->addWarning(
					"%s should not be used with %s argument(s)",
					$stackPtr,
					$funcName,
					[ $funcName, $this->functionsArgCount[$funcName] ]
				);
			} else {
				$phpcsFile->addWarning(
					"$funcName should not be used",
					$stackPtr,
					$funcName
				);
			}
		}
	}

	/**
	 * Return the number of arguments between the $parenthesis as opener and its closer
	 * Ignoring commas between brackets to support nested argument lists
	 *
	 * @param File $phpcsFile
	 * @param int $parenthesis The parenthesis token index.
	 * @return int
	 */
	private function argCount( File $phpcsFile, $parenthesis ) {
		$tokens = $phpcsFile->getTokens();
		if ( !isset( $tokens[$parenthesis]['parenthesis_closer'] ) ) {
			return 0;
		}

		$end = $tokens[$parenthesis]['parenthesis_closer'];
		$next = $phpcsFile->findNext( Tokens::$emptyTokens, $parenthesis + 1, $end, true );
		$argCount = 0;

		if ( $next !== false ) {
			// Something found, there is at least one argument
			$argCount++;

			$searchTokens = [
				T_OPEN_CURLY_BRACKET,
				T_OPEN_SQUARE_BRACKET,
				T_OPEN_PARENTHESIS,
				T_COMMA
			];
			while ( $next !== false ) {
				switch ( $tokens[$next]['code'] ) {
					case T_OPEN_CURLY_BRACKET:
					case T_OPEN_SQUARE_BRACKET:
					case T_OPEN_PARENTHESIS:
						if ( isset( $tokens[$next]['parenthesis_closer'] ) ) {
							// jump to closing parenthesis to ignore commas between opener and closer
							$next = $tokens[$next]['parenthesis_closer'];
						}
						break;
					case T_COMMA:
						$argCount++;
						break;
				}

				$next = $phpcsFile->findNext( $searchTokens, $next + 1, $end );
			}
		}

		return $argCount;
	}
}
