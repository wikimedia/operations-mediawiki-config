<?php
/**
 * Intentionally cause a fatal or slow response in production, to test
 * error handling, logging, and caching behavior.
 *
 * This works both via mwdebug, and directly on production MediaWiki clusters.
 * It is tolerated in production because the endpoint requires a private token
 * obtained via the deployment shell.
 *
 * Usage:
 *
 *   Browse to a URL like so
 *   https://test.wikipedia.org/w/fatal-error.php?password=foo&action=nomethod
 *
 * Options:
 *
 * - action (Required) See $allowedActions below for allowed values for "action".
 *
 * - from (Optional) By default, the action is performed directly during the main
 *   part of the response. For error and timeout actions, you can set "from"
 *   to one of "postsend", "shutdown", or "destruct" to cause the fatal error
 *   after the output is sent to the browser. It is expected that these errors
 *   will not be displayed on the page, but instead be available via Logstash,
 *   mwlog, local error logs, etc.
 *
 * Local development:
 *
 * Note that from=postsend requires MediaWiki, and patches changing this
 * should be tested on mwdebug instead. Others can be developed locally:
 *
 * - Comment out the three require_once statements below,
 *   and put `$fatalErrorPassword = 'local';` there instead.
 * - Run `php -S localhost:4000` in the repo.
 * - Navigate to http://localhost:4000/w/fatal-error.php?password=local&action=cache-slow-swr
 */

// This file is reached with multiple paths due to symbolic links for the doc root folders,
// all of the duplicate class names are only from this file being processed multiple times
// phpcs:disable Generic.Classes.DuplicateClassName.Found

define( 'MW_ENTRY_POINT', 'fatal-error' );

require_once __DIR__ . '/../multiversion/MWMultiVersion.php';
require_once MWMultiVersion::getMediaWiki( 'includes/WebStart.php' );
require_once __DIR__ . '/../private/FatalErrorSettings.php';

CauseFatalError::go();

/**
 * Implementing as a class helps avoid conflicts in an already well-populated global namespace.
 */
// phpcs:ignore MediaWiki.Files.ClassMatchesFilename.NotMatch
class CauseFatalError {
	/** @var string[] */
	private static $allowedActions = [
		'noerror',
		'exception',
		'nomethod',
		'oom',
		'timeout',
		'segfault',
		'coredump',
		// Test request coalescing (ignores 'from')
		'cache',
		'cache-slow',
		'cache-slow-swr',
	];
	/** @var string[] */
	private static $allowedFrom = [
		'main',
		'postsend',
		'shutdown',
		'destruct',
	];

	/**
	 * Checks request parameters and (if possible) performs the requested action
	 */
	public static function go() {
		// This global should probably be renamed but that requires coordination with
		// deployment to the private file that sets it
		// phpcs:ignore MediaWiki.NamingConventions.ValidGlobalName.allowedPrefix
		global $fatalErrorPassword;
		$password = $_GET['password'] ?? '';
		if ( !isset( $fatalErrorPassword ) ) {
			echo "Error: password not found in file FatalErrorSettings.php.";
			return;
		}
		if ( !hash_equals( $fatalErrorPassword, $password ) ) {
			echo "Error: password not recognized.";
			return;
		}

		$action = $_GET['action'] ?? 'noerror';
		$from = $_GET['from'] ?? 'main';

		$paramsOkay = true;
		$checkActionResult = static::checkParam( $action, 'action', self::$allowedActions );
		if ( $checkActionResult !== true ) {
			echo "{$checkActionResult}";
			$paramsOkay = false;
		}

		if ( strpos( $action, 'cache' ) === 0 && $from !== 'main' ) {
			echo "Error: 'from' parameter not valid on cache action.";
			return;
		}

		$checkFromResult = static::checkParam( $from, 'from', self::$allowedFrom );
		if ( $checkFromResult !== true ) {
			echo "{$checkFromResult}";
			$paramsOkay = false;
		}

		if ( !$paramsOkay ) {
			return;
		}

		// foo > Foo,         foo-bar > FooBar
		// foo > self::doFoo, foo-bar > self::doFooBar
		$actionCamel = preg_replace_callback(
			'/(?:-|^)([a-z])/',
			fn ( $m ) => strtoupper( $m[1] ),
			$action
		);
		$actionMethod = __CLASS__ . '::do' . $actionCamel;

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
			$mediawiki = new MediaWiki();
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
		// 6 is the value of SIGABRT
		posix_kill( posix_getpid(), 6 );
	}

	public static function doCache() {
		header( 'Cache-Control: max-age=30' );
		header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
		print 'And the winner is ' . random_int( 1e5, 1e6 ) . "\n";
	}

	public static function doCacheSlow() {
		sleep( 10 );

		header( 'Cache-Control: max-age=30' );
		header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
		print 'And the winner is ' . random_int( 1e5, 1e6 ) . "\n";
	}

	public static function doCacheSlowSwr() {
		sleep( 10 );

		header( 'Cache-Control: max-age=30, stale-while-revalidate=60' );
		header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
		print 'And the winner is ' . random_int( 1e5, 1e6 ) . "\n";
	}
}

// phpcs:ignore Generic.Files.OneObjectStructurePerFile.MultipleFound
class CauseFatalErrorFromLateDestruct {
	/**
	 * Keep self-reference to singleton so that destructor does not run during the
	 * main or postsend stages, but later, as part of the shutdown.
	 * Without this, the destructor would run implicitly at the end of CauseFatalError::go(),
	 * which would behave no different than from=main.
	 *
	 * @var CauseFatalErrorFromLateDestruct|null
	 */
	public static $instance;

	/** @var callable callback to invoke upon shutdown */
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
