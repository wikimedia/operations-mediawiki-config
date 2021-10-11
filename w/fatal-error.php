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
 * See $allowedActions below for allowed values for "action".
 */

require_once __DIR__ . '/../multiversion/MWMultiVersion.php';
require MWMultiVersion::getMediaWiki( 'includes/WebStart.php' );
require_once __DIR__ . '/../private/FatalErrorSettings.php';

CauseFatalError::go();

/**
 * Implementing as a class helps avoid conflicts in an already well-populated global namespace.
 */
class CauseFatalError {
	private static $allowedActions = [
		'noerror', 'exception', 'nomethod', 'oom', 'timeout', 'segfault', 'coredump',
	];
	private static $allowedFrom = [
		'main', 'postsend', 'shutdown', 'destruct',
	];

	/**
	 * Checks request parameters and (if possible) performs the requested action
	 */
	public static function go() {
		$mediawiki = new MediaWiki();
		$request = RequestContext::getMain()->getRequest();

		global $fatalErrorPassword;
		$password = $request->getRawVal( 'password', '' );
		if ( !isset( $fatalErrorPassword ) ) {
			echo "Error: password not found in file FatalErrorSettings.php.";
			return;
		}
		if ( !hash_equals( $fatalErrorPassword, $password ) ) {
			echo "Error: password not recognized.";
			return;
		}

		$action = $request->getRawVal( 'action', 'noerror' );
		$from = $request->getRawVal( 'from', 'main' );

		$paramsOkay = true;
		$checkActionResult = static::checkParam( $action, 'action', self::$allowedActions );
		if ( $checkActionResult !== true ) {
			echo "{$checkActionResult}";
			$paramsOkay = false;
		}

		$checkFromResult = static::checkParam( $from, 'from', self::$allowedFrom );
		if ( $checkFromResult !== true ) {
			echo "{$checkFromResult}";
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

		// Lifetime of PHP engine execution (as of Jan 2021, for PHP 7.2)
		// 1. entry point.
		//    This starts from e.g. index.php or this script, and ends
		//    with MW-specific "postsend" behaviour which re-orders stuff
		//    within MW and instructs php-fpm to flush the response, but doesn't
		//    really affect the execution or call stack.
		//    Once all natural code execution has finished, the stack unwinds.
		// 2. shutdown callback(s), new stack for each.
		// 3. destructor callbacks(), new stack for each.

		if ( $from === 'postsend' ) {
			DeferredUpdates::addCallableUpdate( $actionMethod );
			$mediawiki->doPostOutputShutdown( 'normal' );
		} elseif ( $from === 'shutdown' ) {
			register_shutdown_function( $actionMethod );
		} elseif ( $from === 'destruct' ) {
			$obj = new CauseFatalErrorFromLateDestruct( static function () use ( $actionMethod ) {
				$actionMethod();
			} );
		} else {
			$actionMethod();
		}
	}

	/**
	 * Checks parameters for safety and validity.  Specific to this script, and therefore makes some
	 * assumptions that would not be appropriate in a general-purpose function.
	 *
	 * @param mixed $paramVal the value that was received
	 * @param string $paramName the parameter name (used for constructing error messages)
	 * @param array $allowedValues all allowed values for this parameter
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
	 * Throws an ordinary exception
	 */
	public static function doException() {
		throw new Exception( __METHOD__ );
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

	/**
	 * Try to do a core dump
	 */
	public static function doCoredump() {
		posix_setrlimit( POSIX_RLIMIT_CORE, (int)10e9, POSIX_RLIMIT_INFINITY );
		posix_kill( posix_getpid(), 6 /*SIGABRT*/ );
	}
}

// phpcs:ignore Generic.Files.OneObjectStructurePerFile.MultipleFound
class CauseFatalErrorFromLateDestruct {
	// Keep self-reference to singleton so that destructor does not run during the
	// main or postsend stages, but later, as part of the shutdown.
	// Without this, the destructor would run implicitly at the end of CauseFatalError::go(),
	// which would behave no different than from=main.
	public static $instance;

	private $fn;

	/**
	 * @param callable $fn
	 */
	public function __construct( callable $fn ) {
		$this->fn = $fn;

		self::$instance = $this;
	}

	public function __destruct() {
		( $this->fn )();
	}
}
