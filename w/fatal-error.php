<?php

/**
 * For intentionally causing fatal errors in production, to confirm error/logging behavior.
 *
 * Usage: browse to a url of the form:
 *   https://test.wikipedia.org/w/fatal-error.php?password=foo&action=nomethod&postsend=no
 *
 * Allowed values for "postsend" are "yes" and "no".  "yes" causes the fatal error to be
 * executed after output is sent to the browser, so it is expected that error information
 * will not be displayed on the page.
 *
 * See the ALLOWED_ACTIONS constant below for allowed values for "action".
 */

require_once __DIR__ . '/../multiversion/MWMultiVersion.php';
require MWMultiVersion::getMediaWiki( 'includes/WebStart.php', 'mediawiki' );
require_once __DIR__ . '/../private/FatalErrorSettings.php';

define( 'ALLOWED_ACTIONS', [
	'noerror', 'nomethod', 'oom', 'timeout', 'segfault',
] );

CauseFatalError::go();

/**
 * Implementing as a class helps avoid conflicts in an already well-populated global namespace.
 */
class CauseFatalError {
	/**
	 * Checks request parameters and (if possible) performs the requested action
	 */
	public static function go() {
		$mediawiki = new MediaWiki();
		$request = RequestContext::getMain()->getRequest();

		global $fatalErrorPassword;
		$password = $request->getVal( 'password', '' );
		if ( !isset( $fatalErrorPassword ) ) {
			echo "Error: password not found in file FatalErrorSettings.php.";
			return;
		}
		if ( !hash_equals( $fatalErrorPassword, $password ) ) {
			echo "Error: password not recognized.";
			return;
		}

		$action = $request->getVal( 'action', 'noerror' );
		$postSend = $request->getVal( 'postsend', 'no' );

		$paramsOkay = true;
		$checkActionResult = static::checkParam( $action, 'action', ALLOWED_ACTIONS );
		if ( $checkActionResult !== true ) {
			echo "{$checkActionResult}";
			$paramsOkay = false;
		}

		$checkPostSendResult = static::checkParam( $postSend, 'postsend', [ 'yes', 'no' ] );
		if ( $checkPostSendResult !== true ) {
			echo "{$checkPostSendResult}";
			$paramsOkay = false;
		}

		if ( !$paramsOkay ) {
			return;
		}

		$actionMethod = __CLASS__ . '::do' . ucFirst( $action );
		if ( !is_callable( $actionMethod ) ) {
			// Because the action parameter has already been validated against possible values,
			// this is a really just a sanity check and should never actually occur.
			echo "Action method \"{$actionMethod}\" does not exist.  Unable to proceed.";
			return;
		}

		if ( $postSend === 'yes' ) {
			DeferredUpdates::addCallableUpdate( $actionMethod );
			$mediawiki->doPostOutputShutdown( 'normal' );
		} else {
			$actionMethod();
		}
	}

	/**
	 * Checks parameters for safety and validity.  Specific to this script, and therefore makes some
	 * assumptions that would not be appropriate in a general-purpose function.
	 *
	 * @param $paramVal mixed the value that was received
	 * @param $paramName string the parameter name (used for constructing error messages)
	 * @param $allowedValues array all allowed values for this parameter
	 * @return mixed true on success, or an error message suitable for display to the user on error
	 */
	private static function checkParam( $paramVal, $paramName, $allowedValues ) {
		$ret = true;

		if ( !in_array( $paramVal, $allowedValues, true ) ) {
			$ret = "Unrecognized value for parameter {$paramName}.  Allowed values are:\n";
			$ret .= "<ul>\n";
			foreach ( $allowedValues as $allowedVal ) {
				if ( strlen( $allowedVal ) > 0 ) {
					$ret .= "<li>$allowedVal</li>\n";
				}
			}
			$ret .= "</ul>\n";
		}

		return $ret;
	}

	/**
	 * Causes no error.  Useful as a baseline.
	 */
	public static function doNoerror() {
		echo "No error was generated.<br />";
	}

	/**
	 * Intentionally calls a function that does not exist
	 */
	public static function doNomethod() {
		thisFunctionIntentionallyDoesNotExist();
	}

	/**
	 * Exhausts available memory by generating an enormous string
	 */
	public static function doOom() {
		$str = 'x';
		while ( true ) {
			$str = str_repeat( $str, 1024 );
		}
	}

	/**
	 * Times out via infinite loop
	 */
	public static function doTimeout() {
		while ( true ) {
			// Loop body intentionally left empty
		}
	}

	/**
	 * Segfault via infinite recursive invocation of a userland callback by an internal function
	 * See http://nikic.github.io/2017/04/14/PHP-7-Virtual-machine.html#function-calls
	 */
	public static function doSegfault() {
		array_map( __METHOD__, [ 0 ] );
	}
}
