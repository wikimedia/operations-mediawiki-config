<?php
/**
 * This file was copied from PHP_CodeSniffer before being modified
 * File: Standards/PEAR/Sniffs/Commenting/FunctionCommentSniff.php
 * From repository: https://github.com/squizlabs/PHP_CodeSniffer
 *
 * Parses and verifies the doc comments for functions.
 *
 * PHP version 5
 *
 * @category PHP
 * @package PHP_CodeSniffer
 * @author Greg Sherwood <gsherwood@squiz.net>
 * @author Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2014 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD-3-Clause
 * @link http://pear.php.net/package/PHP_CodeSniffer
 */

namespace MediaWiki\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

class FunctionCommentSniff implements Sniff {

	/**
	 * Standard class methods that
	 * don't require documentation
	 *
	 * @var array
	 */
	private $skipStandardMethods = [
		'__toString', '__destruct',
		'__sleep', '__wakeup',
		'__clone'
	];

	/**
	 * Mapping for swap short types
	 *
	 * @var array
	 */
	private static $shortTypeMapping = [
		'boolean' => 'bool',
		'boolean[]' => 'bool[]',
		'integer' => 'int',
		'integer[]' => 'int[]',
	];

	/**
	 * @inheritDoc
	 */
	public function register() {
		return [ T_FUNCTION ];
	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param File $phpcsFile The file being scanned.
	 * @param int $stackPtr The position of the current token in the stack passed in $tokens.
	 *
	 * @return void
	 */
	public function process( File $phpcsFile, $stackPtr ) {
		if ( substr( $phpcsFile->getFilename(), -8 ) === 'Test.php' ) {
			// Don't check documentation for test cases
			return;
		}

		$funcName = $phpcsFile->getDeclarationName( $stackPtr );
		if ( $funcName === null || in_array( $funcName, $this->skipStandardMethods ) ) {
			// Don't require documentation for an obvious method
			return;
		}

		// Identify the visibility of the function
		$methodProps = $phpcsFile->getMethodProperties( $stackPtr );
		if ( $methodProps['scope'] === 'private' ) {
			// Don't check documentation for private functions
			return;
		}

		$tokens = $phpcsFile->getTokens();
		$find = Tokens::$methodPrefixes;
		$find[] = T_WHITESPACE;
		$commentEnd = $phpcsFile->findPrevious( $find, ( $stackPtr - 1 ), null, true );
		if ( $tokens[$commentEnd]['code'] === T_COMMENT ) {
			// Inline comments might just be closing comments for
			// control structures or functions instead of function comments
			// using the wrong comment type. If there is other code on the line,
			// assume they relate to that code.
			$prev = $phpcsFile->findPrevious( $find, ( $commentEnd - 1 ), null, true );
			if ( $prev !== false && $tokens[$prev]['line'] === $tokens[$commentEnd]['line'] ) {
				$commentEnd = $prev;
			}
		}
		if ( $tokens[$commentEnd]['code'] !== T_DOC_COMMENT_CLOSE_TAG
			&& $tokens[$commentEnd]['code'] !== T_COMMENT
		) {
			// Don't require documentation for functions with no parameters, except getters
			if ( substr( $funcName, 0, 3 ) === 'get' || $phpcsFile->getMethodParameters( $stackPtr ) ) {
				$phpcsFile->addError(
					'Missing function doc comment',
					$stackPtr,
					'MissingDocumentation' . ucfirst( $methodProps['scope'] )
				);
			}
			$phpcsFile->recordMetric( $stackPtr, 'Function has doc comment', 'no' );
			return;
		} else {
			$phpcsFile->recordMetric( $stackPtr, 'Function has doc comment', 'yes' );
		}
		if ( $tokens[$commentEnd]['code'] === T_COMMENT ) {
			$phpcsFile->addError( 'You must use "/**" style comments for a function comment',
			$stackPtr, 'WrongStyle' );
			return;
		}
		if ( $tokens[$commentEnd]['line'] !== ( $tokens[$stackPtr]['line'] - 1 ) ) {
			$error = 'There must be no blank lines after the function comment';
			$phpcsFile->addError( $error, $commentEnd, 'SpacingAfter' );
		}
		$commentStart = $tokens[$commentEnd]['comment_opener'];
		$skipDoc = false;
		foreach ( $tokens[$commentStart]['comment_tags'] as $tag ) {
			$tagText = $tokens[$tag]['content'];
			if ( $tagText === '@see' ) {
				// Make sure the tag isn't empty.
				$string = $phpcsFile->findNext( T_DOC_COMMENT_STRING, $tag, $commentEnd );
				if ( $string === false || $tokens[$string]['line'] !== $tokens[$tag]['line'] ) {
					$error = 'Content missing for @see tag in function comment';
					$phpcsFile->addError( $error, $tag, 'EmptySees' );
				}
			} elseif ( $tagText === '@inheritDoc' ) {
				$skipDoc = true;
			} elseif ( $tagText === '@inheritdoc' ) {
				$skipDoc = true;
				$error = 'Incorrect capitalization of @inheritDoc';
				$fix = $phpcsFile->addFixableError( $error, $tag, 'LowercaseInheritDoc' );
				if ( $fix ) {
					$phpcsFile->fixer->replaceToken( $tag, "@inheritDoc" );
				}
			} elseif ( $tagText === '@deprecated' ) {
				// No need to validate deprecated functions
				$skipDoc = true;
			}
		}

		$this->validateDocSyntax( $phpcsFile, $commentStart, $commentEnd );

		if ( $skipDoc ) {
			// Don't need to validate anything else
			return;
		}

		$this->processReturn( $phpcsFile, $stackPtr, $commentStart );
		$this->processThrows( $phpcsFile, $commentStart );
		$this->processParams( $phpcsFile, $stackPtr, $commentStart );
	}

	/**
	 * Process the return comment of this function comment.
	 *
	 * @param File $phpcsFile The file being scanned.
	 * @param int $stackPtr The position of the current token in the stack passed in $tokens.
	 * @param int $commentStart The position in the stack where the comment started.
	 */
	protected function processReturn( File $phpcsFile, $stackPtr, $commentStart ) {
		$tokens = $phpcsFile->getTokens();
		// Return if no scope_opener.
		if ( !isset( $tokens[$stackPtr]['scope_opener'] ) ) {
			return;
		}

		// Skip constructors
		if ( $phpcsFile->getDeclarationName( $stackPtr ) === '__construct' ) {
			return;
		}

		$endFunction = $tokens[$stackPtr]['scope_closer'];
		$found = false;
		for ( $i = $endFunction - 1; $i > $stackPtr; $i-- ) {
			$token = $tokens[$i];
			if ( isset( $token['scope_condition'] ) && (
				$tokens[$token['scope_condition']]['code'] === T_CLOSURE ||
				$tokens[$token['scope_condition']]['code'] === T_FUNCTION
			) ) {
				// Skip to the other side of the closure/inner function and continue
				$i = $token['scope_condition'];
				continue;
			}
			if ( $token['code'] === T_RETURN ) {
				if ( isset( $tokens[$i + 1] ) && $tokens[$i + 1]['code'] === T_SEMICOLON ) {
					// This is a `return;` so it doesn't need documentation
					continue;
				}
				$found = true;
				break;
			}
		}

		if ( !$found ) {
			return;
		}

		$return = null;
		foreach ( $tokens[$commentStart]['comment_tags'] as $tag ) {
			$tagContent = $tokens[$tag]['content'];
			if ( $tagContent === '@return' || $tagContent === '@returns' ) {
				if ( $return !== null ) {
					$error = 'Only 1 @return tag is allowed in a function comment';
					$phpcsFile->addError( $error, $tag, 'DuplicateReturn' );
					return;
				}
				if ( $tagContent === '@returns' ) {
					$error = 'Use @return tag in function comment instead of @returns';
					$fix = $phpcsFile->addFixableError( $error, $tag, 'PluralReturns' );
					if ( $fix ) {
						$phpcsFile->fixer->replaceToken( $tag, '@return' );
					}
				}
				$return = $tag;
			}
		}
		if ( $return !== null ) {
			$retTypeSpacing = $return + 1;
			if ( $tokens[$retTypeSpacing]['code'] === T_DOC_COMMENT_WHITESPACE ) {
				$expectedSpaces = 1;
				$currentSpaces = strlen( $tokens[$retTypeSpacing]['content'] );
				if ( $currentSpaces !== $expectedSpaces ) {
					$data = [
						$expectedSpaces,
						$currentSpaces,
					];
					$fix = $phpcsFile->addFixableWarning(
						'Expected %s spaces before return type; %s found',
						$retTypeSpacing,
						'SpacingBeforeReturnType',
						$data
					);
					if ( $fix ) {
						$phpcsFile->fixer->replaceToken( $retTypeSpacing, ' ' );
					}
				}
			}
			$retType = $return + 2;
			$content = $tokens[$retType]['content'];
			if ( empty( $content ) || $tokens[$retType]['code'] !== T_DOC_COMMENT_STRING ) {
				$error = 'Return type missing for @return tag in function comment';
				$phpcsFile->addError( $error, $return, 'MissingReturnType' );
			}
			// The first word of the return type is the actual type
			$exploded = explode( ' ', $content, 2 );
			$type = $exploded[0];
			$comment = isset( $exploded[1] ) ? $exploded[1] : null;
			$fixType = false;
			// Check for unneeded punctation
			$matches = [];
			if ( preg_match( '/^(.*)((?:(?![\[\]_{}])\p{P})+)$/', $type, $matches ) ) {
				$fix = $phpcsFile->addFixableError(
					'Return type should not end with punctuation "%s"',
					$retType,
					'NotPunctuationReturn',
					[ $matches[2] ]
				);
				$type = $matches[1];
				if ( $fix ) {
					$fixType = true;
				}
			}
			$matches = [];
			if ( preg_match( '/^([{\[]+)(.*)([\]}]+)$/', $type, $matches ) ) {
				$error = 'Expected parameter type not wrapped in parenthesis; %s and %s found';
				$data = [
					$matches[1], $matches[3]
				];
				$fix = $phpcsFile->addFixableError(
					$error,
					$retType,
					'NotParenthesisReturnType',
					$data
				);
				$type = $matches[2];
				if ( $fix ) {
					$fixType = true;
				}
			}
			// Check the type for short types
			$explodedType = explode( '|', $type );
			foreach ( $explodedType as $index => $singleType ) {
				if ( isset( self::$shortTypeMapping[$singleType] ) ) {
					$newType = self::$shortTypeMapping[$singleType];
					// grep: NotShortIntReturn, NotShortIntArrayReturn,
					// NotShortBoolReturn, NotShortBoolArrayReturn
					$code = 'NotShort' . ucfirst( str_replace( '[]', 'Array', $newType ) ) . 'Return';
					$fix = $phpcsFile->addFixableError(
						'Short type of "%s" should be used for @return tag',
						$retType,
						$code,
						[ $newType ]
					);
					if ( $fix ) {
						$explodedType[$index] = $newType;
						$fixType = true;
					}
				}
			}
			// Check spacing after type
			if ( $comment !== null ) {
				$expectedSpaces = 1;
				$currentSpaces = strspn( $comment, ' ' ) + 1;
				if ( $currentSpaces !== $expectedSpaces ) {
					$data = [
						$expectedSpaces,
						$currentSpaces,
					];
					$fix = $phpcsFile->addFixableWarning(
						'Expected %s spaces after return type; %s found',
						$retType,
						'SpacingAfterReturnType',
						$data
					);
					if ( $fix ) {
						$fixType = true;
						$comment = substr( $comment, $currentSpaces - 1 );
					}
				}
			}
			if ( $fixType ) {
				$phpcsFile->fixer->replaceToken(
					$retType,
					implode( '|', $explodedType ) . ( $comment !== null ? ' ' . $comment : '' )
				);
			}
		} else {
			$error = 'Missing @return tag in function comment';
			$phpcsFile->addError( $error, $tokens[$commentStart]['comment_closer'], 'MissingReturn' );
		}
	}

	/**
	 * Process any throw tags that this function comment has.
	 *
	 * @param File $phpcsFile The file being scanned.
	 * @param int $commentStart The position in the stack where the comment started.
	 */
	protected function processThrows( File $phpcsFile, $commentStart ) {
		$tokens = $phpcsFile->getTokens();
		foreach ( $tokens[$commentStart]['comment_tags'] as $tag ) {
			$tagContent = $tokens[$tag]['content'];
			if ( $tagContent !== '@throws' && $tagContent !== '@throw' ) {
				continue;
			}
			if ( $tagContent === '@throw' ) {
				$error = 'Use @throws tag in function comment instead of @throw';
				$fix = $phpcsFile->addFixableError( $error, $tag, 'SingularThrow' );
				if ( $fix ) {
					$phpcsFile->fixer->replaceToken( $tag, '@throws' );
				}
			}
			$exception = null;
			$comment = null;
			if ( $tokens[( $tag + 2 )]['code'] === T_DOC_COMMENT_STRING ) {
				$matches = [];
				preg_match( '/([^\s]+)(?:\s+(.*))?/', $tokens[( $tag + 2 )]['content'], $matches );
				$exception = $matches[1];
				if ( isset( $matches[2] ) ) {
					$comment = $matches[2];
				}
			}
			if ( $exception === null ) {
				$error = 'Exception type missing for @throws tag in function comment';
				$phpcsFile->addError( $error, $tag, 'InvalidThrows' );
			} else {
				// Check for unneeded parenthesis on exceptions
				$matches = [];
				if ( preg_match( '/^([{\[]+)(.*)([\]}]+)$/', $exception, $matches ) ) {
					$error = 'Expected parameter type not wrapped in parenthesis; %s and %s found';
					$data = [
						$matches[1], $matches[3]
					];
					$fix = $phpcsFile->addFixableError(
						$error,
						$tag,
						'NotParenthesisException',
						$data
					);
					if ( $fix ) {
						$phpcsFile->fixer->replaceToken(
							$tag + 2,
							$matches[2] . ( $comment === null ? '' : ' ' . $comment )
						);
					}
				}
			}
		}
	}

	/**
	 * Process the function parameter comments.
	 *
	 * @param File $phpcsFile The file being scanned.
	 * @param int $stackPtr The position of the current token in the stack passed in $tokens.
	 * @param int $commentStart The position in the stack where the comment started.
	 */
	protected function processParams( File $phpcsFile, $stackPtr, $commentStart ) {
		$tokens = $phpcsFile->getTokens();
		$params = [];
		$maxType = 0;
		$maxVar = 0;
		foreach ( $tokens[$commentStart]['comment_tags'] as $pos => $tag ) {
			$tagContent = $tokens[$tag]['content'];

			if ( $tagContent === '@params' ) {
				$error = 'Use @param tag in function comment instead of @params';
				$fix = $phpcsFile->addFixableError( $error, $tag, 'PluralParams' );
				if ( $fix ) {
					$phpcsFile->fixer->replaceToken( $tag, '@param' );
				}
			} elseif ( $tagContent === '@param[in]' || $tagContent === '@param[out]' ||
				$tagContent === '@param[in,out]'
			) {
				$error = 'Use @param tag in function comment instead of %s';
				$fix = $phpcsFile->addFixableError( $error, $tag, 'DirectionParam', [ $tagContent ] );
				if ( $fix ) {
					$phpcsFile->fixer->replaceToken( $tag, '@param' );
				}
			} elseif ( $tagContent !== '@param' ) {
				continue;
			}

			$paramSpace = 0;
			$type = '';
			$typeSpace = 0;
			$var = '';
			$varSpace = 0;
			$comment = '';
			$commentFirst = '';
			if ( $tokens[( $tag + 1 )]['code'] === T_DOC_COMMENT_WHITESPACE ) {
				$paramSpace = strlen( $tokens[( $tag + 1 )]['content'] );
			}
			if ( $tokens[( $tag + 2 )]['code'] === T_DOC_COMMENT_STRING ) {
				$matches = [];
				preg_match( '/([^$&.]+)(?:((?:\.\.\.)?(?:\$|&)[^\s]+)(?:(\s+)(.*))?)?/',
					$tokens[( $tag + 2 )]['content'], $matches );
				$typeLen = strlen( $matches[1] );
				$type = trim( $matches[1] );
				$typeSpace = ( $typeLen - strlen( $type ) );
				$typeLen = strlen( $type );
				if ( $typeLen > $maxType ) {
					$maxType = $typeLen;
				}
				if ( isset( $matches[2] ) ) {
					$var = $matches[2];
					$varLen = strlen( $var );
					if ( $varLen > $maxVar ) {
						$maxVar = $varLen;
					}
					if ( isset( $matches[4] ) ) {
						$varSpace = strlen( $matches[3] );
						$commentFirst = $matches[4];
						$comment = $commentFirst;
						// Any strings until the next tag belong to this comment.
						if ( isset( $tokens[$commentStart]['comment_tags'][( $pos + 1 )] ) ) {
							$end = $tokens[$commentStart]['comment_tags'][( $pos + 1 )];
						} else {
							$end = $tokens[$commentStart]['comment_closer'];
						}
						for ( $i = ( $tag + 3 ); $i < $end; $i++ ) {
							if ( $tokens[$i]['code'] === T_DOC_COMMENT_STRING ) {
								$comment .= ' ' . $tokens[$i]['content'];
							}
						}
					}
				} else {
					$error = 'Missing parameter name';
					$phpcsFile->addError( $error, $tag, 'MissingParamName' );
				}
			} else {
				$error = 'Missing parameter type';
				$phpcsFile->addError( $error, $tag, 'MissingParamType' );
			}
			$isVariadicArg = substr( $var, -4 ) === ',...';
			if ( $isVariadicArg ) {
				// Variadic args sometimes part of the argument list,
				// sometimes not. Remove the variadic indicator from the doc name to
				// compare it against the real name, when it is part of the argument list.
				// If it is not part of the argument list,
				// the name of the extra paremter will not be checked.
				// This does not take care for the php5.6 ...$var feature
				$var = substr( $var, 0, -4 );
			}
			$params[] = [
				'tag' => $tag,
				'type' => $type,
				'var' => $var,
				'variadic_arg' => $isVariadicArg,
				'comment' => $comment,
				'comment_first' => $commentFirst,
				'param_space' => $paramSpace,
				'type_space' => $typeSpace,
				'var_space' => $varSpace,
			];
		}
		$realParams = $phpcsFile->getMethodParameters( $stackPtr );
		$foundParams = [];
		// We want to use ... for all variable length arguments, so added
		// this prefix to the variable name so comparisons are easier.
		foreach ( $realParams as $pos => $param ) {
			if ( $realParams[$pos]['pass_by_reference'] === true ) {
				$realParams[$pos]['name'] = '&' . $realParams[$pos]['name'];
			}
			if ( $param['variable_length'] === true ) {
				$realParams[$pos]['name'] = '...' . $realParams[$pos]['name'];
			}
		}
		foreach ( $params as $pos => $param ) {
			if ( $param['var'] === '' ) {
				continue;
			}
			// Check number of spaces before type (after @param)
			$spaces = 1;
			if ( $param['param_space'] !== $spaces ) {
				$error = 'Expected %s spaces before parameter type; %s found';
				$data = [
					$spaces,
					$param['param_space'],
				];
				$fix = $phpcsFile->addFixableWarning( $error, $param['tag'], 'SpacingBeforeParamType', $data );
				if ( $fix ) {
					$phpcsFile->fixer->replaceToken( ( $param['tag'] + 1 ), str_repeat( ' ', $spaces ) );
				}
			}
			// Check for unneeded punctation on parameter type
			$matches = [];
			if ( preg_match( '/^([{\[]+)(.*)([\]}]+)$/', $param['type'], $matches ) ) {
				$error = 'Expected parameter type not wrapped in parenthesis; %s and %s found';
				$data = [
					$matches[1], $matches[3]
				];
				$fix = $phpcsFile->addFixableError(
					$error,
					$param['tag'],
					'NotParenthesisParamType',
					$data
				);
				if ( $fix ) {
					$this->replaceParamComment(
						$phpcsFile,
						$param,
						[ 'type' => $matches[2] ]
					);
				}
			}
			// Check number of spaces after the type.
			// $spaces = ( $maxType - strlen( $param['type'] ) + 1 );
			$spaces = 1;
			if ( $param['type_space'] !== $spaces ) {
				$error = 'Expected %s spaces after parameter type; %s found';
				$data = [
					$spaces,
					$param['type_space'],
				];
				$fix = $phpcsFile->addFixableWarning( $error, $param['tag'], 'SpacingAfterParamType', $data );
				if ( $fix ) {
					$this->replaceParamComment(
						$phpcsFile,
						$param,
						[ 'type_space' => $spaces ]
					);
				}
			}
			$var = $param['var'];
			// Check for unneeded punctation
			$matches = [];
			if ( preg_match( '/^(.*?)((?:(?![\[\]_{}])\p{P})+)$/', $var, $matches ) ) {
				$fix = $phpcsFile->addFixableError(
					'Param name should not end with punctuation "%s"',
					$param['tag'],
					'NotPunctuationParam',
					[ $matches[2] ]
				);
				$var = $matches[1];
				if ( $fix ) {
					$this->replaceParamComment(
						$phpcsFile,
						$param,
						[ 'var' => $var ]
					);
				}
			}
			// Make sure the param name is correct.
			if ( isset( $realParams[$pos] ) ) {
				$realName = $realParams[$pos]['name'];
				if ( $realName !== $var ) {
					$code = 'ParamNameNoMatch';
					$data = [
						$var,
						$realName,
					];
					$error = 'Doc comment for parameter %s does not match ';
					if ( strcasecmp( $var, $realName ) === 0 ) {
						$error .= 'case of ';
						$code = 'ParamNameNoCaseMatch';
					}
					$error .= 'actual variable name %s';
					$phpcsFile->addError( $error, $param['tag'], $code, $data );
				}
			} elseif ( !$param['variadic_arg'] ) {
				// We must have an extra parameter comment.
				$error = 'Superfluous parameter comment';
				$phpcsFile->addError( $error, $param['tag'], 'ExtraParamComment' );
			}
			$foundParams[] = $var;
			// Check the short type of boolean and integer
			$explodedType = explode( '|', $param['type'] );
			$fixType = false;
			foreach ( $explodedType as $index => $singleType ) {
				if ( isset( self::$shortTypeMapping[$singleType] ) ) {
					$newType = self::$shortTypeMapping[$singleType];
					// grep: NotShortIntParam, NotShortIntArrayParam,
					// NotShortBoolParam, NotShortBoolArrayParam
					$code = 'NotShort' . ucfirst( str_replace( '[]', 'Array', $newType ) ) . 'Param';
					$fix = $phpcsFile->addFixableError(
						'Short type of "%s" should be used for @param tag',
						$param['tag'],
						$code,
						[ $newType ]
					);
					if ( $fix ) {
						$explodedType[$index] = $newType;
						$fixType = true;
					}
				}
			}
			if ( $fixType ) {
				$this->replaceParamComment(
					$phpcsFile,
					$param,
					[ 'type' => implode( '|', $explodedType ) ]
				);
			}
			if ( $param['comment'] === '' ) {
				continue;
			}
			// Check number of spaces after the var name.
			// $spaces = ( $maxVar - strlen( $param['var'] ) + 1 );
			$spaces = 1;
			if ( $param['var_space'] !== $spaces &&
				ltrim( $param['comment'] ) !== ''
			) {
				$error = 'Expected %s spaces after parameter name; %s found';
				$data = [
					$spaces,
					$param['var_space'],
				];
				$fix = $phpcsFile->addFixableWarning( $error, $param['tag'], 'SpacingAfterParamName', $data );
				if ( $fix ) {
					$this->replaceParamComment(
						$phpcsFile,
						$param,
						[ 'var_space' => $spaces ]
					);
				}
			}
		}
		$realNames = [];
		foreach ( $realParams as $realParam ) {
			$realNames[] = $realParam['name'];
		}
		// Report missing comments.
		$diff = array_diff( $realNames, $foundParams );
		foreach ( $diff as $neededParam ) {
			$error = 'Doc comment for parameter "%s" missing';
			$data = [ $neededParam ];
			$phpcsFile->addError( $error, $commentStart, 'MissingParamTag', $data );
		}
	}

	/**
	 * Replace a @param comment
	 *
	 * @param File $phpcsFile The file being scanned.
	 * @param array $param Array of the @param
	 * @param array $fixParam Array with fixes to @param. Only provide keys to replace
	 */
	protected function replaceParamComment( File $phpcsFile, array $param, array $fixParam ) {
		// Use the old value for unchanged keys
		$fixParam += $param;

		// Build the new line
		$content = $fixParam['type'];
		$content .= str_repeat( ' ', $fixParam['type_space'] );
		$content .= $fixParam['var'];
		if ( $fixParam['variadic_arg'] ) {
			$content .= ',...';
		}
		$content .= str_repeat( ' ', $fixParam['var_space'] );
		$content .= $fixParam['comment_first'];
		$phpcsFile->fixer->replaceToken( ( $fixParam['tag'] + 2 ), $content );
	}

	/**
	 * Check the doc syntax like start or end tags
	 *
	 * @param File $phpcsFile The file being scanned.
	 * @param int $commentStart The position in the stack where the comment started.
	 * @param int $commentEnd The position in the stack where the comment ended.
	 */
	protected function validateDocSyntax( File $phpcsFile, $commentStart, $commentEnd ) {
		$tokens = $phpcsFile->getTokens();
		$isMultiLineDoc = ( $tokens[$commentStart]['line'] !== $tokens[$commentEnd]['line'] );

		// Start token should exact /**
		if ( $tokens[$commentStart]['code'] === T_DOC_COMMENT_OPEN_TAG &&
			$tokens[$commentStart]['content'] !== '/**'
		) {
			$error = 'Comment open tag must be \'/**\'';
			$fix = $phpcsFile->addFixableError( $error, $commentStart, 'SyntaxOpenTag' );
			if ( $fix ) {
				$phpcsFile->fixer->replaceToken( $commentStart, '/**' );
			}
		}
		// Calculate the column to align all doc stars. Use column of /**, add 1 to skip char /
		$columnDocStar = $tokens[$commentStart]['column'] + 1;
		$prevLineDocStar = $tokens[$commentStart]['line'];

		for ( $i = $commentStart; $i <= $commentEnd; $i++ ) {
			$initialStarChars = 0;

			// Star token should exact *
			if ( $tokens[$i]['code'] === T_DOC_COMMENT_STAR ) {
				if ( $tokens[$i]['content'] !== '*' ) {
					$error = 'Comment star must be \'*\'';
					$fix = $phpcsFile->addFixableError( $error, $i, 'SyntaxDocStar' );
					if ( $fix ) {
						$phpcsFile->fixer->replaceToken( $i, '*' );
					}
				}
				// Multi stars in a line are parsed as a new token
				$initialStarChars = strspn( $tokens[$i + 1]['content'], '*' );
				if ( $initialStarChars > 0 ) {
					$error = 'Comment star must be a single \'*\'';
					$fix = $phpcsFile->addFixableError( $error, $i, 'SyntaxMultiDocStar' );
					if ( $fix ) {
						$phpcsFile->fixer->replaceToken(
							$i + 1,
							substr( $tokens[$i + 1]['content'], $initialStarChars )
						);
					}
				}
			}

			// Ensure whitespace after /** or *
			if ( ( $tokens[$i]['code'] === T_DOC_COMMENT_OPEN_TAG ||
				$tokens[$i]['code'] === T_DOC_COMMENT_STAR ) &&
				$tokens[$i + 1]['length'] > 0
			) {
				$commentStarSpacing = $i + 1;
				$expectedSpaces = 1;
				// ignore * removed by SyntaxMultiDocStar and count spaces after that
				$currentSpaces = strspn(
					$tokens[$commentStarSpacing]['content'], ' ', $initialStarChars
				);
				$error = null;
				$code = null;
				if ( $isMultiLineDoc && $currentSpaces < $expectedSpaces ) {
					// be relax for multiline docs, because some line breaks in @param can
					// have more than one space after a doc star
					$error = 'Expected at least %s spaces after doc star; %s found';
					$code = 'SpacingDocStar';
				} elseif ( !$isMultiLineDoc && $currentSpaces !== $expectedSpaces ) {
					$error = 'Expected %s spaces after doc star on single line; %s found';
					$code = 'SpacingDocStarSingleLine';
				}
				if ( $error !== null && $code !== null ) {
					$data = [
						$expectedSpaces,
						$currentSpaces,
					];
					$fix = $phpcsFile->addFixableError(
						$error,
						$commentStarSpacing,
						$code,
						$data
					);
					if ( $fix ) {
						if ( $currentSpaces > $expectedSpaces ) {
							// Remove whitespace
							$content = $tokens[$commentStarSpacing]['content'];
							$phpcsFile->fixer->replaceToken(
								$commentStarSpacing,
								substr( $content, 0, $expectedSpaces - $currentSpaces )
							);
						} else {
							// Add whitespace
							$phpcsFile->fixer->addContent(
								$i, str_repeat( ' ', $expectedSpaces )
							);
						}
					}
				}
			}

			if ( !$isMultiLineDoc ) {
				continue;
			}

			// Ensure one whitespace before @param/@return
			if ( $tokens[$i]['code'] === T_DOC_COMMENT_TAG &&
				$tokens[$i]['line'] === $tokens[$i - 1]['line']
			) {
				$commentTagSpacing = $i - 1;
				$expectedSpaces = 1;
				$currentSpaces = strspn( strrev( $tokens[$commentTagSpacing]['content'] ), ' ' );
				if ( $expectedSpaces !== $currentSpaces ) {
					$data = [
						$expectedSpaces,
						$tokens[$i]['content'],
						$currentSpaces,
					];
					$fix = $phpcsFile->addFixableError(
						'Expected %s spaces before %s; %s found',
						$commentTagSpacing,
						'SpacingDocTag',
						$data
					);
					if ( $fix ) {
						if ( $currentSpaces > $expectedSpaces ) {
							// Remove whitespace
							$content = $tokens[$commentTagSpacing]['content'];
							$phpcsFile->fixer->replaceToken(
								$commentTagSpacing,
								substr( $content, 0, $expectedSpaces - $currentSpaces )
							);
						} else {
							// Add whitespace
							$phpcsFile->fixer->addContentBefore(
								$i, str_repeat( ' ', $expectedSpaces )
							);
						}
					}
				}

				continue;
			}

			// Ensure aligned * or */ for multiline comments
			if ( ( $tokens[$i]['code'] === T_DOC_COMMENT_STAR ||
				$tokens[$i]['code'] === T_DOC_COMMENT_CLOSE_TAG ) &&
				$tokens[$i]['column'] !== $columnDocStar &&
				$tokens[$i]['line'] !== $prevLineDocStar
			) {
				if ( $tokens[$i]['code'] === T_DOC_COMMENT_STAR ) {
					$error = 'Comment star tag not aligned with open tag';
					$code = 'SyntaxAlignedDocStar';
				} else {
					$error = 'Comment close tag not aligned with open tag';
					$code = 'SyntaxAlignedDocClose';
				}
				$fix = $phpcsFile->addFixableError( $error, $i, $code );
				if ( $fix ) {
					$columnOff = $columnDocStar - $tokens[$i]['column'];
					if ( $columnOff < 0 ) {
						$tokenBefore = $i - 1;
						// Ensure to remove only whitespaces
						if ( $tokens[$tokenBefore]['code'] === T_DOC_COMMENT_WHITESPACE ) {
							$columnOff = max( $columnOff, $tokens[$tokenBefore]['length'] * -1 );
							// remove whitespaces
							$phpcsFile->fixer->replaceToken(
								$tokenBefore,
								substr( $tokens[$tokenBefore]['content'], 0, $columnOff )
							);
						}
					} else {
						// Add whitespaces
						$phpcsFile->fixer->addContentBefore( $i, str_repeat( ' ', $columnOff ) );
					}
				}
				$prevLineDocStar = $tokens[$i]['line'];
			}
		}

		// End token should exact */
		if ( $tokens[$commentEnd]['code'] === T_DOC_COMMENT_CLOSE_TAG &&
			$tokens[$commentEnd]['content'] !== '*/'
		) {
			$error = 'Comment close tag must be \'*/\'';
			$fix = $phpcsFile->addFixableError( $error, $commentEnd, 'SyntaxCloseTag' );
			if ( $fix ) {
				$phpcsFile->fixer->replaceToken( $commentEnd, '*/' );
			}
		}

		// For multi line comments the closing tag must have it own line
		if ( $isMultiLineDoc ) {
			$prev = $commentEnd - 1;
			$prevNonWhitespace = $phpcsFile->findPrevious(
				[ T_DOC_COMMENT_WHITESPACE ], $prev, null, true
			);
			if ( $tokens[$prevNonWhitespace]['line'] === $tokens[$commentEnd]['line'] ) {
				$firstWhitespaceOnLine = $phpcsFile->findFirstOnLine(
					[ T_DOC_COMMENT_WHITESPACE ], $prevNonWhitespace
				);
				$error = 'Comment close tag should have own line';
				$fix = $phpcsFile->addFixableError( $error, $commentEnd, 'CloseTagOwnLine' );
				if ( $fix ) {
					$phpcsFile->fixer->beginChangeset();
					$phpcsFile->fixer->addNewline( $prev );
					// Copy the indent of the previous line to the new line
					$phpcsFile->fixer->addContent(
						$prev, $tokens[$firstWhitespaceOnLine]['content']
					);
					$phpcsFile->fixer->endChangeset();
				}
			}
		} else {
			// Ensure a whitespace before the token
			$commentCloseSpacing = $commentEnd - 1;
			$expectedSpaces = 1;
			$currentSpaces = strspn( strrev( $tokens[$commentCloseSpacing]['content'] ), ' ' );
			if ( $currentSpaces !== $expectedSpaces ) {
				$data = [
					$expectedSpaces,
					$currentSpaces,
				];
				$fix = $phpcsFile->addFixableError(
					'Expected %s spaces before close comment tag on single line; %s found',
					$commentCloseSpacing,
					'SpacingSingleLineCloseTag',
					$data
				);
				if ( $fix ) {
					if ( $currentSpaces > $expectedSpaces ) {
						// Remove whitespace
						$content = $tokens[$commentCloseSpacing]['content'];
						$phpcsFile->fixer->replaceToken(
							$commentCloseSpacing, substr( $content, 0, $expectedSpaces - $currentSpaces )
						);
					} else {
						// Add whitespace
						$phpcsFile->fixer->addContentBefore(
							$commentEnd, str_repeat( ' ', $expectedSpaces )
						);
					}
				}
			}
		}
	}
}
