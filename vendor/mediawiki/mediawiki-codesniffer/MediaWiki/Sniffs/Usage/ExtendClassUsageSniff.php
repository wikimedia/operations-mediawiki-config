<?php
/**
 * Report warnings when unexpected function or variable used like.
 * Should use $this->msg() rather than wfMessage() on ContextSource extend.
 * Should use $this->getUser() rather than $wgUser() on ContextSource extend.
 * Should use $this->getRequest() rather than $wgRequest on ContextSource extend.
 */

namespace MediaWiki\Sniffs\Usage;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class ExtendClassUsageSniff implements Sniff {

	public static $msgMap = [
		T_FUNCTION => 'function',
		T_VARIABLE => 'variable'
	];

	public static $checkConfig = [
		// All extended class name.
		'extendsCls' => [
			'ContextSource' => true
		],
		// All details of usage need to be check.
		'checkList' => [
			// Extended class name.
			'ContextSource' => [
				[
					// The check content.
					'content' => 'wfMessage',
					// The content shows on report message.
					'msg_content' => 'wfMessage()',
					// The check content code.
					'code' => T_FUNCTION,
					// The expected content.
					'expect_content' => '$this->msg()',
					// The expected content code.
					'expect_code' => T_FUNCTION
				],
				[
					'content' => '$wgUser',
					'msg_content' => '$wgUser',
					'code' => T_VARIABLE,
					'expect_content' => '$this->getUser()',
					'expect_code' => T_FUNCTION
				],
				[
					'content' => '$wgRequest',
					'msg_content' => '$wgRequest',
					'code' => T_VARIABLE,
					'expect_content' => '$this->getRequest()',
					'expect_code' => T_FUNCTION
				]
			]
		]
	];

	/**
	 * @inheritDoc
	 */
	public function register() {
		return [
			T_CLASS
		];
	}

	/**
	 * @param File $phpcsFile
	 * @param int $stackPtr The current token index.
	 * @return void
	 */
	public function process( File $phpcsFile, $stackPtr ) {
		$extClsContent = $phpcsFile->findExtendedClassName( $stackPtr );
		if ( $extClsContent === false ) {
			// No extends token found
			return;
		}

		// Ignore namespace separator at the begin
		$extClsContent = ltrim( $extClsContent, '\\' );

		// Here should be replaced with a mechanism that check if
		// the base class is in the list of restricted classes
		if ( !isset( self::$checkConfig['extendsCls'][$extClsContent] ) ) {
			return;
		}

		$tokens = $phpcsFile->getTokens();
		$currToken = $tokens[$stackPtr];

		$extClsCheckList = self::$checkConfig['checkList'][$extClsContent];
		// Loop over all tokens of the class to check each function
		$i = $currToken['scope_opener'];
		$end = $currToken['scope_closer'];
		$eligableFunc = null;
		while ( $i !== false && $i < $end ) {
			$iToken = $tokens[$i];
			if ( $iToken['code'] === T_FUNCTION ) {
				$eligableFunc = null;
				// If this is a function, make sure it's eligible
				// (i.e. not static or abstract, and has a body).
				$methodProps = $phpcsFile->getMethodProperties( $i );
				$isStaticOrAbstract = $methodProps['is_static'] || $methodProps['is_abstract'];
				$hasBody = isset( $iToken['scope_opener'] )
					&& isset( $iToken['scope_closer'] );
				if ( !$isStaticOrAbstract && $hasBody ) {
					$funcNamePtr = $phpcsFile->findNext( T_STRING, $i );
					$eligableFunc = [
						'name' => $tokens[$funcNamePtr]['content'],
						'scope_start' => $iToken['scope_opener'],
						'scope_end' => $iToken['scope_closer']
					];
				}
			}

			if ( !empty( $eligableFunc )
				&& $i > $eligableFunc['scope_start']
				&& $i < $eligableFunc['scope_end']
			) {
				// Inside a eligable function,
				// check the current token against the checklist
				foreach ( $extClsCheckList as $key => $value ) {
					$condition = false;
					if ( $value['code'] === T_FUNCTION
						&& strcasecmp( $iToken['content'], $value['content'] ) === 0
					) {
						$condition = true;
					}
					if ( $value['code'] === T_VARIABLE
						&& $iToken['content'] === $value['content']
					) {
						$condition = true;
					}
					if ( $condition ) {
						$warning = 'Should use %s %s rather than %s %s .';
						$expectCodeMsg = self::$msgMap[$value['expect_code']];
						$codeMsg = self::$msgMap[$value['code']];
						$phpcsFile->addWarning(
							$warning,
							$i,
							'FunctionVarUsage',
							[ $expectCodeMsg, $value['expect_content'], $codeMsg, $value['msg_content'] ]
						);
					}
				}
			}
			// Jump to the next function
			if ( $eligableFunc === null
				|| $i >= $eligableFunc['scope_end']
			) {
				$start = $eligableFunc === null ? $i : $eligableFunc['scope_end'];
				$i = $phpcsFile->findNext( T_FUNCTION, $start + 1, $end );
				continue;
			}
			// Find next token to work with
			$i = $phpcsFile->findNext( [ T_STRING, T_VARIABLE, T_FUNCTION ], $i + 1, $end );
		}
	}
}
