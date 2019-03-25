<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.
#
# Included by PhpAutoPrepend.php BEFORE any other wmf-config or mediawiki file.
# MUST NOT use any predefined state, only plain PHP.
#
# Exposes:
# - $wmgProfiler (used by CommonSettings.php)

use Wikimedia\MWConfig\XWikimediaDebug;

require_once __DIR__ . '/arclamp.php';
require_once __DIR__ . '/../src/XWikimediaDebug.php';

/**
 * Set up the profiler
 * @param array $options Associative array of options:
 *   - redis-host: The host used for Xenon events
 *   - redis-port: The port used for Xenon events
 *   - redis-timeout: The redis socket timeout
 *   - use-xhgui: True to use XHGui saver via MongoDB
 *   - xhgui-conf: The configuration array to pass to Xhgui_Saver::factory
 *   - excimer-production-period: The sampling period for production profiling
 *   - excimer-single-period: The sampling period for Excimer forceprofile
 */
function wmfSetupProfiler( $options ) {
	global $wmgProfiler;

	$wmgProfiler = [];

	/**
	 * File overview:
	 *
	 * - Parse X-Wikimedia-Debug options.
	 * - Enable request profiling.
	 * - One-off profile to stdout (via MediaWiki).
	 * - One-off profile to XHGui.
	 * - Sampling profiler for live traffic.
	 */

	if ( ini_get( 'hhvm.stats.enable_hot_profiler' ) ) {
		wmfSetupXhprof( $options );
		wmfSetupArcLamp( $options );
	}
	if ( extension_loaded( 'tideways_xhprof' ) ) {
		wmfSetupTideways( $options );
	}
	if ( extension_loaded( 'excimer' ) ) {
		wmfSetupExcimer( $options );
	}
}

/**
 * Set up the HHVM flavour of xhprof
 *
 * @param array $options
 */
function wmfSetupXhprof( $options ) {
	$xwd = XWikimediaDebug::getInstance();
	$profileToStdout = $xwd->hasOption( 'forceprofile' );
	$profileToXhgui = $xwd->hasOption( 'profile' ) && !empty( $options['use-xhgui'] );

	// This is passed as query parameter instead of header attribute,
	// but is nonetheless considered part of X-Wikimedia-Debug and must
	// only be enabled when X-Wikimedia-Debug is also enabled, due to caching.
	if ( $xwd->isHeaderPresent() && isset( $_GET['forceprofile'] ) ) {
		$profileToStdout = true;
	}

	/**
	 * Enable request profiling
	 *
	 * We can only enable XHProf once, and the first call controls the flags.
	 * Later calls are ignored. Therefore, always use the same flags.
	 *
	 * - XHPROF_FLAGS_NO_BUILTINS: Used by MediaWiki and by XHGui.
	 *   Doesn't modify output format, but makes output more concise.
	 *
	 * - XHPROF_FLAGS_CPU: Only used by XHGui only.
	 *   Adds 'cpu' keys to profile entries.
	 *
	 * - XHPROF_FLAGS_MEMORY: Only used by XHGui only.
	 *   Adds 'mu' and 'pmu' keys to profile entries.
	 */
	$xhprofFlags = XHPROF_FLAGS_CPU | XHPROF_FLAGS_MEMORY | XHPROF_FLAGS_NO_BUILTINS;
	if ( $profileToStdout
		|| PHP_SAPI === 'cli'
		|| $profileToXhgui
	) {
		// Enable Xhprof now instead of waiting for MediaWiki to start it later.
		// This ensures a balanced and complete call graph. (T180183)
		xhprof_enable( $xhprofFlags );

		/**
		 * One-off profile to stdout.
		 *
		 * For web: Set X-Wikimedia-Debug (to bypass cache) and query param 'forceprofile=1'.
		 * For CLI: Set CLI option '--profiler=text'.
		 *
		 * https://wikitech.wikimedia.org/wiki/X-Wikimedia-Debug#Plaintext_request_profile
		 */
		if ( $profileToStdout || PHP_SAPI === 'cli' ) {
			global $wmgProfiler;
			$wmgProfiler = [
				'class'  => 'ProfilerXhprof',
				'flags'  => $xhprofFlags,
				'output' => 'text',
			];
		}

		/**
		 * One-off profile to XHGui.
		 *
		 * Set X-Wikimedia-Debug header with 'profile' attribute to instrument a web request
		 * with XHProf and save the profile to XHGui's MongoDB.
		 *
		 * To find the profile in XHGui, either browse "Recent", or use wgRequestId value
		 * from the mw.config data in the HTML web response, e.g. by running the
		 * `mw.config.get('wgRequestId')` snippet in JavaScript. Then look up as follows:
		 *
		 * https://performance.wikimedia.org/xhgui/?url=WShdaQpAIHwAAF9HkX4AAAAW
		 *
		 * See https://performance.wikimedia.org/xhgui/
		 * See https://wikitech.wikimedia.org/wiki/X-Wikimedia-Debug#Request_profiling
		 */
		if ( $profileToXhgui ) {

			/**
			 * The following classes from composer packages are needed to submit
			 * profiles to XHGui:
			 *
			 * - MongoDate
			 * - Xhgui_Util
			 * - Xhgui_Saver::factory
			 *   - MongoClient
			 *   - MongoCollection
			 *   - Xhgui_Saver_Mongo
			 * - Xhgui_Saver_Mongo::save
			 *     - Xhgui_Saver_Mongo::getLastProfilingId
			 *       - MongoId
			 *     - MongoCollection::insert
			 *
			 * Upstream XHGui recommends using alcaeus/mongo-php-adapter, which is a library
			 * that provides an interface compatible with PHP5's ext-mongo on top of either
			 * ext-mongo itself (PHP5.3+) or ext-mongodb (PHP5.5+ and PHP7).
			 * The problem is, we can't use mongo-php-adapter because HHVM supports neither
			 * of the PHP extensions, and also neither WMF PHP5 nor PHP7 servers have either
			 * of the PHP extensions installed. Instead we use "mongofill", which is a
			 * plain PHP implementation originally written to support HHVM, but also works
			 * fine on PHP5 and PHP7.s
			 */
			require_once __DIR__ . '/../vendor/autoload.php';

			// XHGui save callback
			$saveCallback = function () use ( $options ) {
				$data = [ 'profile' => xhprof_disable() ];

				$sec  = $_SERVER['REQUEST_TIME'];
				$usec = $_SERVER['REQUEST_TIME_FLOAT'] - $sec;

				// Fake the URL to have a prefix of the request ID, that way it can
				// be easily found through XHGui through a predictable search URL
				// that looks for the request ID.
				$reqId = WebRequest::getRequestId();
				// Create a simplified url with just script name and 'action' query param
				$qs = isset( $_GET['action'] ) ? ( '?action=' . $_GET['action'] ) : '';
				$url = '//' . $reqId . $_SERVER['SCRIPT_NAME'] . $qs;

				// Create sanitized copies of $_SERVER, $_ENV, and $_GET that are
				// appropiate for exposing publicly to the web.
				// This intentionally omits 'REQUEST_URI' (added later)
				$keyWhitelist = array_flip( [
					'HTTP_HOST', 'HTTP_X_WIKIMEDIA_DEBUG', 'REQUEST_METHOD',
					'REQUEST_START_TIME', 'REQUEST_TIME', 'REQUEST_TIME_FLOAT',
					'SERVER_ADDR', 'SERVER_NAME', 'THREAD_TYPE', 'action'
				] );
				$server = array_intersect_key( $_SERVER, $keyWhitelist );
				$env = array_intersect_key( $_ENV, $keyWhitelist );
				$get = array_intersect_key( $_GET, $keyWhitelist );

				// Add unique ID
				$server['UNIQUE_ID'] = $reqId;
				$env['UNIQUE_ID'] = $reqId;

				// Add hostname as web server name
				// (SERVER_NAME is e.g. wikipedia.org, SERVER_ADDR is the LVS service name,
				// e.g. appservers.svc)
				$env['HOSTNAME'] = wfHostname();

				// Re-insert scrubbed URL as REQUEST_URL:
				$server['REQUEST_URI'] = $url;
				$env['REQUEST_URI'] = $url;

				$data['meta'] = [
					'url'              => $url,
					'SERVER'           => $server,
					'get'              => $get,
					'env'              => $env,
					'simple_url'       => Xhgui_Util::simpleUrl( $url ),
					'request_ts'       => new MongoDate( $sec ),
					'request_ts_micro' => new MongoDate( $sec, $usec ),
					'request_date'     => date( 'Y-m-d', $sec ),
				];

				Xhgui_Saver::factory( $options['xhgui-conf'] )->save( $data );
			};

			// Use a nested register_postsend_function() function, so that the profile
			// includes MediaWiki's post-send DeferredUpdates as well.
			// The postsend functions are FIFO, and because this code runs before MediaWiki
			// we become the first. By using nesting, we become the last instead.
			register_postsend_function( function () use ( $saveCallback ) {
				register_postsend_function( $saveCallback );
			} );
		}
	}
}

/**
 * Set up Tideways XHProf. This code is mostly duplicated from wmfSetupXhprof,
 * with the idea that we can delete the former once migration to PHP 7 is
 * complete.
 */
function wmfSetupTideways( $options ) {
	$xwd = XWikimediaDebug::getInstance();
	$profileToStdout = $xwd->hasOption( 'forceprofile' );
	$profileToXhgui = $xwd->hasOption( 'profile' ) && !empty( $options['use-xhgui'] );

	// This is passed as query parameter instead of header attribute,
	// but is nonetheless considered part of X-Wikimedia-Debug and must
	// only be enabled when X-Wikimedia-Debug is also enabled, due to caching.
	if ( $xwd->isHeaderPresent() && isset( $_GET['forceprofile'] ) ) {
		$profileToStdout = true;
	}

	/**
	 * Enable request profiling
	 *
	 * We can only enable Tideways once, and the first call controls the flags.
	 * Later calls are ignored. Therefore, always use the same flags.
	 *
	 * - TIDEWAYS_XHPROF_FLAGS_NO_BUILTINS: Used by MediaWiki and by XHGui.
	 *   Doesn't modify output format, but makes output more concise.
	 *
	 * - TIDEWAYS_XHPROF_FLAGS_CPU: Only used by XHGui only.
	 *   Adds 'cpu' keys to profile entries.
	 *
	 * - TIDEWAYS_XHPROF_FLAGS_MEMORY: Only used by XHGui only.
	 *   Adds 'mu' and 'pmu' keys to profile entries.
	 */
	$xhprofFlags = TIDEWAYS_XHPROF_FLAGS_CPU | TIDEWAYS_XHPROF_FLAGS_MEMORY | TIDEWAYS_XHPROF_FLAGS_NO_BUILTINS;
	if ( $profileToStdout
		|| PHP_SAPI === 'cli'
		|| $profileToXhgui
	) {
		// Enable Tideways now instead of waiting for MediaWiki to start it later.
		// This ensures a balanced and complete call graph. (T180183)
		tideways_xhprof_enable( $xhprofFlags );

		/**
		 * One-off profile to stdout.
		 *
		 * For web: Set X-Wikimedia-Debug (to bypass cache) and query param 'forceprofile=1'.
		 * For CLI: Set CLI option '--profiler=text'.
		 *
		 * https://wikitech.wikimedia.org/wiki/X-Wikimedia-Debug#Plaintext_request_profile
		 */
		if ( $profileToStdout || PHP_SAPI === 'cli' ) {
			global $wmgProfiler;
			$wmgProfiler = [
				'class'  => 'ProfilerXhprof',
				'flags'  => $xhprofFlags,
				'output' => 'text',
			];
		}

		/**
		 * One-off profile to XHGui.
		 *
		 * Set X-Wikimedia-Debug header with 'profile' attribute to instrument a web request
		 * with XHProf and save the profile to XHGui's MongoDB.
		 *
		 * To find the profile in XHGui, either browse "Recent", or use wgRequestId value
		 * from the mw.config data in the HTML web response, e.g. by running the
		 * `mw.config.get('wgRequestId')` snippet in JavaScript. Then look up as follows:
		 *
		 * https://performance.wikimedia.org/xhgui/?url=WShdaQpAIHwAAF9HkX4AAAAW
		 *
		 * See https://performance.wikimedia.org/xhgui/
		 * See https://wikitech.wikimedia.org/wiki/X-Wikimedia-Debug#Request_profiling
		 */
		if ( $profileToXhgui ) {
			// XHGui save callback
			$saveCallback = function () use ( $options ) {
				$data = [ 'profile' => tideways_xhprof_disable() ];

				// Classes from the packages perftools/xhgui-collector and
				// mongofill/mongofill are required to save profiling data
				// to XHGui. Once we drop HHVM support, we can use
				// alcaeus/mongo-php-adapter with ext-mongodb instead of
				// mongofill/mongofill, as recommended by upstream. They
				// provide the same classes.
				require_once __DIR__ . '/../vendor/autoload.php';

				$sec  = $_SERVER['REQUEST_TIME'];
				$usec = $_SERVER['REQUEST_TIME_FLOAT'] - $sec;

				// Fake the URL to have a prefix of the request ID, that way it can
				// be easily found through XHGui through a predictable search URL
				// that looks for the request ID.
				$reqId = $_SERVER['UNIQUE_ID'] ?? '';
				// Create a simplified url with just script name and 'action' query param
				$qs = isset( $_GET['action'] ) ? ( '?action=' . $_GET['action'] ) : '';
				$url = '//' . $reqId . $_SERVER['SCRIPT_NAME'] . $qs;

				// Create sanitized copies of $_SERVER, $_ENV, and $_GET that are
				// appropiate for exposing publicly to the web.
				// This intentionally omits 'REQUEST_URI' (added later)
				$keyWhitelist = array_flip( [
					'HTTP_HOST', 'HTTP_X_WIKIMEDIA_DEBUG', 'REQUEST_METHOD',
					'REQUEST_START_TIME', 'REQUEST_TIME', 'REQUEST_TIME_FLOAT',
					'SERVER_ADDR', 'SERVER_NAME', 'THREAD_TYPE', 'action'
				] );
				$server = array_intersect_key( $_SERVER, $keyWhitelist );
				$env = array_intersect_key( $_ENV, $keyWhitelist );
				$get = array_intersect_key( $_GET, $keyWhitelist );

				// Add unique ID
				$server['UNIQUE_ID'] = $reqId;
				$env['UNIQUE_ID'] = $reqId;

				// Add hostname as web server name
				// (SERVER_NAME is e.g. wikipedia.org, SERVER_ADDR is the LVS service name,
				// e.g. appservers.svc)
				$env['HOSTNAME'] = posix_uname()['nodename'];

				// Re-insert scrubbed URL as REQUEST_URL:
				$server['REQUEST_URI'] = $url;
				$env['REQUEST_URI'] = $url;

				$data['meta'] = [
					'url'              => $url,
					'SERVER'           => $server,
					'get'              => $get,
					'env'              => $env,
					'simple_url'       => Xhgui_Util::simpleUrl( $url ),
					'request_ts'       => new MongoDate( $sec ),
					'request_ts_micro' => new MongoDate( $sec, $usec ),
					'request_date'     => date( 'Y-m-d', $sec ),
				];

				Xhgui_Saver::factory( $options['xhgui-conf'] )->save( $data );
			};

			// Register the callback as a shutdown_function, so that the profile
			// includes MediaWiki's post-send DeferredUpdates as well.
			// Zend PHP doesn't have register_postsend_function so we can't really make
			// this happen earlier.
			register_shutdown_function( $saveCallback );
		}
	}
}

/**
 * Set up Excimer for production and one-shot profiling
 */
function wmfSetupExcimer( $options ) {
	// Use a static variable to keep the object in scope until the end
	// of the request
	static $prodProf;

	$prodProf = new ExcimerProfiler;
	$prodProf->setEventType( EXCIMER_CPU );
	$prodProf->setPeriod( $options['excimer-production-period'] );
	$prodProf->setMaxDepth( 30 );
	$prodProf->setFlushCallback(
		function ( $log ) use ( $options ) {
			wmfExcimerFlushCallback( $log, $options );
		},
		1 );
	$prodProf->start();

	if ( !extension_loaded( 'tideways_xhprof' )
		&& XWikimediaDebug::getInstance()->hasOption( 'forceprofile' )
	) {
		global $wmgProfiler;

		$cpuProf = new ExcimerProfiler;
		$cpuProf->setEventType( EXCIMER_CPU );
		$cpuProf->setPeriod( $options['excimer-single-period'] );
		$cpuProf->setMaxDepth( 100 );
		$cpuProf->start();

		$realProf = new ExcimerProfiler;
		$realProf->setEventType( EXCIMER_REAL );
		$realProf->setPeriod( $options['excimer-single-period'] );
		$realProf->setMaxDepth( 100 );
		$realProf->start();

		$wmgProfiler = [
			'class' => 'ProfilerExcimer',
			'cpu-profiler' => $cpuProf,
			'real-profiler' => $realProf,
			'output' => 'text',
		];
	}
}

/**
 * The callback for production profiling. This is called every time Excimer
 * collects a stack trace. The period is 60s, so there's no point waiting for
 * more samples to arrive before the end of the request, they probably won't.
 */
function wmfExcimerFlushCallback( $log, $options ) {
	$redis = new Redis();
	try {
		$ok = $redis->connect( $options['redis-host'], $options['redis-port'], $options['redis-timeout'] );
		if ( !$ok ) {
			return;
		}

		// Arc Lamp expects the first frame to be a PHP file.
		// This is used to group related traces for the same web entry point.
		// In most cases, this happens by default already. But at least for destructor
		// callbacks, this isn't the case on PHP 7.2. E.g. a line may be:
		// "LBFactory::__destruct;LBFactory::LBFactory::shutdown;… 1".
		$firstFrame = realpath( $_SERVER['SCRIPT_FILENAME'] ) . ';';
		$collapsed = $log->formatCollapsed();
		foreach ( explode( "\n", $collapsed ) as $line ) {
			if ( $line === '' ) {
				// $collapsed ends with a line break
				continue;
			}
			// If the expected first frame isn't the entry point, prepend it.
			// This check includes the semicolon to avoid false positives.
			if ( substr( $line, 0, strlen( $firstFrame ) ) !== $firstFrame ) {
				$line = $firstFrame . $line;
			}
			$redis->publish( 'excimer', $line );
		}
	} catch ( Exception $e ) {
		// Known failure scenarios:
		//
		// - "RedisException: read error on connection"
		//   Each publish() in the above loop writes data to Redis and
		//   subsequently reads from the socket for Redis' response.
		//   If any socket read takes longer than $timeout, it throws (T206092).
		//   As of writing, this is rare (a few times per day at most),
		//   which is considered an acceptable loss in profile samples.

		// Write to log with low severity
		trigger_error( get_class( $e ) . ': ' . $e->getMessage(), E_USER_NOTICE );
	}
}
