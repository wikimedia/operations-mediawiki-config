<?php
/**
 * Make sure lower camel function name.
 */

namespace MediaWiki\Sniffs\NamingConventions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class LowerCamelFunctionsNameSniff implements Sniff {

	// Magic methods.
	private static $magicMethods = [
		'__construct' => true,
		'__destruct' => true,
		'__call' => true,
		'__callstatic' => true,
		'__get' => true,
		'__set' => true,
		'__isset' => true,
		'__unset' => true,
		'__sleep' => true,
		'__wakeup' => true,
		'__tostring' => true,
		'__set_state' => true,
		'__clone' => true,
		'__invoke' => true,
		'__debuginfo' => true
	];

	// A list of non-magic methods with double underscore.
	private static $methodsDoubleUnderscore = [
		'__soapcall' => true,
		'__getlastrequest' => true,
		'__getlastresponse' => true,
		'__getlastrequestheaders' => true,
		'__getlastresponseheaders' => true,
		'__getfunctions' => true,
		'__gettypes' => true,
		'__dorequest' => true,
		'__setcookie' => true,
		'__setlocation' => true,
		'__setsoapheaders' => true
	];

	// Scope list.
	private static $scopeList = [
		T_CLASS => true,
		T_INTERFACE => true,
		T_TRAIT => true
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
		$functionContent = $phpcsFile->getDeclarationName( $stackPtr );
		if ( $functionContent === null ) {
			return;
		}

		$tokens = $phpcsFile->getTokens();
		$lowerFunctionName = strtolower( $functionContent );
		foreach ( $tokens[$stackPtr]['conditions'] as $scope => $code ) {
			if ( !isset( self::$scopeList[$code] ) ||
				isset( self::$methodsDoubleUnderscore[$lowerFunctionName] ) ||
				isset( self::$magicMethods[$lowerFunctionName] )
			) {
				continue;
			}

			$pos = strpos( $functionContent, '_' );
			$isTest = substr( $this->getClassName( $phpcsFile, $stackPtr ), -4 ) === 'Test' &&
				preg_match( '/^(test|provide)[A-Z]|\wProvider$/', $functionContent );
			if ( $pos !== false && !$isTest ||
				$functionContent[0] !== $lowerFunctionName[0]
			) {
				$error = 'Function name "%s" should use lower camel case.';
				$phpcsFile->addError(
					$error,
					$stackPtr,
					'FunctionName',
					[ $functionContent ]
				);
			}
		}
	}

	/**
	 * Gets the name of the class which the $functionPtr points into.
	 * The stack pointer must point to a function keyword.
	 * @param File $phpcsFile
	 * @param int $functionPtr Pointer to a function token inside the class.
	 * @return string|null
	 */
	private function getClassName( $phpcsFile, $functionPtr ) {
		$tokens = $phpcsFile->getTokens();
		$token = $tokens[$functionPtr];
		foreach ( $token['conditions'] as $ptr => $type ) {
			if ( $type === T_CLASS ) {
				return $phpcsFile->getDeclarationName( $ptr );
			}
		}
		return null;
	}
}
