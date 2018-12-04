<?php

require_once __DIR__ . '/../multiversion/MWMultiVersion.php';
require MWMultiVersion::getMediaWiki( 'includes/WebStart.php', 'mediawiki' );
require_once __DIR__ . '/../private/FatalErrorSettings.php';

define( 'ALLOWED_ACTIONS', [
	'noerror', 'nomethod', 'oom', 'timeout', 'segfault',
]);

/**
 * Checks parameters for safety and validity.  Specific to this script, and therefore makes some
 * assumptions that would not be appropriate in a general-purpose function.
 *
 * @param $paramVal mixed the value that was received
 * @param $paramName string the parameter name (used for constructing error messages)
 * @param $allowedValues array all allowed values for this parameter
 * @return mixed true on success, or an error message suitable for display to the user on error
 */
function checkParam( $paramVal, $paramName, $allowedValues ) {
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
function doNoerror() {
	echo "No error was generated.<br />";
}

/**
 * Intentionally calls a function that does not exist
 */
function doNomethod() {
	thisFunctionIntentionallyDoesNotExist();
}

/**
 * Exhausts available memory by generating an enormous string
 */
function doOom() {
	$str = 'x';
	while ( true ) {
		$str = str_repeat( $str, 1024 );
	}
}

/**
 * Times out via infinite loop
 */
function doTimeout() {
	while ( true ) {
		// Loop body intentionally left empty
	}
}

/**
 * Segfault via infinite recursive invocation of a userland callback by an internal function
 * See http://nikic.github.io/2017/04/14/PHP-7-Virtual-machine.html#function-calls
 */
function doSegfault() {
	function foo() {
		array_map( 'foo', [0] );
	}
	foo();
}

$mediawiki = new MediaWiki();
$request = RequestContext::getMain()->getRequest();

$password = $request->getVal( 'password', '' );
if ( !isset( $fatalErrorPassword ) ) {
	echo "Error: password not found in file FatalErrorSettings.php.";
	exit;
}
if ( $password !== $fatalErrorPassword ) {
	echo "Error: password not recognized.";
	exit;
}

$action = $request->getVal( 'action', 'noerror' );
$postSend = $request->getVal( 'postsend', 'no' );

$paramsOkay = true;
$checkActionResult = checkParam( $action, 'action', ALLOWED_ACTIONS );
if ( $checkActionResult !== true ) {
	echo "{$checkActionResult}";
	$paramsOkay = false;
}

$checkPostSendResult = checkParam( $postSend, 'postsend', [ 'yes', 'no' ] );
if ( $checkPostSendResult !== true ) {
	echo "{$checkPostSendResult}";
	$paramsOkay = false;
}

if ( !$paramsOkay ) {
	exit;
}

$actionFnName = 'do' . ucFirst( $action );
if ( !function_exists( $actionFnName ) ) {
	echo "Action function \"{$actionFnName}\" does not exist.  Unable to proceed.";
	exit;
}

if ( $postSend  === 'yes' ) {
	DeferredUpdates::addCallableUpdate( $actionFnName );
	$mediawiki->doPostOutputShutdown( 'normal' );
} else {
	$actionFnName();
}
