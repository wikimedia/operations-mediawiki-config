<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.
#
# Included by PhpAutoPrepend.php BEFORE any other wmf-config or mediawiki file.
# MUST NOT use any predefined state, only plain PHP.
#
# Exposes:
# - $wmgProfiler (used by CommonSettings.php)

use Wikimedia\MWConfig\XWikimediaDebug;

require_once __DIR__ . '/../src/XWikimediaDebug.php';

/**
 * Set up the profiler
 * @param array $options Associative array of options:
 *   - redis-host: The host used for Xenon events
 *   - redis-port: The port used for Xenon events
 *   - redis-timeout: The redis socket timeout
 *   - use-xhgui: True to use XHGui saver
 *   - xhgui-conf: The configuration array to pass to Xhgui_Saver::factory
 *   - excimer-production-period: The sampling period for production profiling
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

	if ( extension_loaded( 'tideways_xhprof' ) ) {
		wmfSetupTideways( $options );
	}
	if ( extension_loaded( 'excimer' ) ) {
		wmfSetupExcimer( $options );
	}
}

/**
 * Set up Tideways XHProf.
 *
 * @param array $options
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
		 * with XHProf and save the profile to XHGui.
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
				// XHGui used to use MongoDB.  Even though we're now using MariaDB,
				// the MongoDate class from mongofill remains.  Despite its name,
				// there is no MongoDB-specific functionality in it.
				require_once __DIR__ . '/../lib/profiler-autoload.php';

				// These globals are set by private/PrivateSettings.php and may only be
				// read by wmf-config after MediaWiki is initialised.
				// The profiler is set up much earlier via PhpAutoPrepend, as such,
				// only materialise these globals during the save callback, not sooner.
				global $wmgXhguiDBuser, $wmgXhguiDBpassword;

				$data = [ 'profile' => tideways_xhprof_disable() ];
				$sec  = $_SERVER['REQUEST_TIME'];
				$usec = $_SERVER['REQUEST_TIME_FLOAT'] - $sec;

				// Fake the URL to have a prefix of the request ID, that way it can
				// be easily found through XHGui through a predictable search URL
				// that looks for the request ID.
				// Matches mediawiki/core: WebRequest::getRequestId (T253674).
				$reqId = $_SERVER['HTTP_X_REQUEST_ID'] ?? $_SERVER['UNIQUE_ID'] ?? null;

				// Create a simplified url with just script name and 'action' query param
				$qs = isset( $_GET['action'] ) ? ( '?action=' . $_GET['action'] ) : '';
				$url = '//' . $reqId . $_SERVER['SCRIPT_NAME'] . $qs;

				// Create sanitized copies of $_SERVER, $_ENV, and $_GET that are
				// appropiate for exposing publicly to the web.
				// This intentionally omits 'REQUEST_URI' (added later)
				$serverKeys = array_flip( [
					'HTTP_X_REQUEST_ID', 'UNIQUE_ID',
					'HTTP_HOST', 'HTTP_X_WIKIMEDIA_DEBUG', 'REQUEST_METHOD',
					'REQUEST_START_TIME', 'REQUEST_TIME', 'REQUEST_TIME_FLOAT',
					'SERVER_NAME'
				] );
				$getKeys = array_flip( [ 'action' ] );
				$server = array_intersect_key( $_SERVER, $serverKeys );
				$get = array_intersect_key( $_GET, $getKeys );
				$env = [];

				// Add hostname of current web server.
				// - Not SERVER_NAME which is usually identical to HTTP_HOST, e.g. wikipedia.org.
				// - Not SERVER_ADDR which the local IP address, e.g. 10.xx.xx.xx.
				// Get what we're looking for from uname.
				$server['HOSTNAME'] = php_uname( 'n' );

				// Re-insert scrubbed URL as REQUEST_URL:
				$server['REQUEST_URI'] = $url;

				$data['meta'] = [
					'url'              => $url,
					'SERVER'           => $server,
					'get'              => $get,
					'env'              => $env,
					'simple_url'       => $url,
					'request_ts'       => new MongoDate( $sec ),
					'request_ts_micro' => new MongoDate( $sec, $usec ),
					'request_date'     => date( 'Y-m-d', $sec ),
				];

				if ( !empty( $options['xhgui-conf']['pdo.connect'] )
					&& $wmgXhguiDBuser
					&& $wmgXhguiDBpassword
				) {
					$pdo = new PDO(
						$options['xhgui-conf']['pdo.connect'],
						$wmgXhguiDBuser,
						$wmgXhguiDBpassword
					);
					$saver = new Xhgui_Saver_Pdo( $pdo, $options['xhgui-conf']['pdo.table'] );
					$saver->save( $data );
				}
			};

			// Register the callback as a shutdown_function, so that the profile
			// includes MediaWiki's post-send DeferredUpdates as well.
			// FIXME: This doesn't actually capture MW's post-send work because
			// this callback is registered before MW is initialised, and the list
			// is FIFO. It still captures all the main work during the request
			// and pre-send work. On HHVM, we used a nested register_postsend_function().
			register_shutdown_function( $saveCallback );
		}
	}
}

/**
 * Set up Excimer for production and one-shot profiling
 *
 * @param array $options
 */
function wmfSetupExcimer( $options ) {
	// Use a static variable to keep the object in scope until the end
	// of the request
	static $prodProf;

	$prodProf = new ExcimerProfiler;
	$prodProf->setEventType( EXCIMER_CPU );
	$prodProf->setPeriod( $options['excimer-production-period'] );
	// T176916
	$prodProf->setMaxDepth( 250 );
	$prodProf->setFlushCallback(
		function ( $log ) use ( $options ) {
			wmfExcimerFlushCallback( $log, $options );
		},
		1 );
	$prodProf->start();
}

/**
 * The callback for production profiling. This is called every time Excimer
 * collects a stack trace. The period is 60s, so there's no point waiting for
 * more samples to arrive before the end of the request, they probably won't.
 *
 * @param string $log
 * @param array $options
 */
function wmfExcimerFlushCallback( $log, $options ) {
	$error = null;
	$toobig = 0;
	try {
		$redis = new Redis();
		$ok = $redis->connect( $options['redis-host'], $options['redis-port'], $options['redis-timeout'] );
		if ( !$ok ) {
			$error = 'connect_error';
		} else {
			// Arc Lamp expects the first frame to be a PHP file.
			// This is used to group related traces for the same web entry point.
			// In most cases, this happens by default already. But at least for destructor
			// callbacks, this isn't the case on PHP 7.2. E.g. a line may be:
			// "LBFactory::__destruct;LBFactory::LBFactory::shutdown;… 1".
			$firstFrame = realpath( $_SERVER['SCRIPT_FILENAME'] ) . ';';
			$collapsed = $log->formatCollapsed();
			foreach ( explode( "\n", $collapsed ) as $line ) {
				if ( ( substr_count( $line, ';' ) + 1 ) >= 249 ) {
					// Stacks are separated by semi-colon, so +1 to get the frame count.
					// Anything size 249 or more, may be cut off (per setMaxDepth).
					// We discard those because depth limitation results in the early frames
					// (starting with the entry point) being omitted, which are the ones we need
					// for a flame graph (T176916)
					$toobig++;
					continue;
				}
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
		$error = 'exception';
	}

	if ( $error || $toobig ) {
		if ( !class_exists( Wikimedia\MWConfig\ServiceConfig::class ) ) {
			require_once __DIR__ . '/../src/ServiceConfig.php';
		}
		$dest = Wikimedia\MWConfig\ServiceConfig::getInstance()->getLocalService( 'statsd' );
		if ( $dest ) {
			$sock = socket_create( AF_INET, SOCK_DGRAM, SOL_UDP );
			if ( $error ) {
				$stat = "MediaWiki.arclamp_client_error.{$error}:1|c";
				@socket_sendto( $sock, $stat, strlen( $stat ), 0, $dest, 8125 );
			}
			if ( $toobig ) {
				$stat = "MediaWiki.arclamp_client_discarded.toobig:{$toobig}|c";
				@socket_sendto( $sock, $stat, strlen( $stat ), 0, $dest, 8125 );
			}
		}
	}
}
