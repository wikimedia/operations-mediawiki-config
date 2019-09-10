<?php
/**
 * Copyright (C) 2018 Kunal Mehta <legoktm@member.fsf.org>
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
 *
 */

namespace MediaWiki\Sniffs\PHP71Features;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * Prevent usage of nullable types
 *
 * @see https://wiki.php.net/rfc/nullable_types
 */
class NullableTypeSniff implements Sniff {
	/**
	 * @inheritDoc
	 */
	public function register() {
		return [ T_NULLABLE ];
	}

	/**
	 * @param File $phpcsFile
	 * @param int $stackPtr
	 * @return void
	 */
	public function process( File $phpcsFile, $stackPtr ) {
		$phpcsFile->addError(
			"Nullable type hints cannot be used",
			$stackPtr,
			'NotAllowed'
		);
	}
}
