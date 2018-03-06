<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.
#
# Will later be included with PHP from a auto_prepend_file before any wmf-config or MediaWiki file.
# Must not use predefined state, other than plain PHP.
#
# Exposes:
# - $wmgProfiler (used by StartProfile.php)

global $wmgProfiler;
$wmgProfiler = [];

/**
 * File overview:
 *
 * - Parse X-Wikimedia-Debug options.
 * - Enable request profiling.
 * - One-off profile to stdout (via MediaWiki).
 * - One-off profile to XHGui.
 * - Sampling profiler for production traffic.
 */

if ( ini_get( 'hhvm.stats.enable_hot_profiler' ) ) {
	/**
	 * Parse X-Wikimedia-Debug options.
	 *
	 * See https://wikitech.wikimedia.org/wiki/X-Wikimedia-Debug
	 */
	$xwd = false;
	if ( isset( $_SERVER['HTTP_X_WIKIMEDIA_DEBUG'] ) ) {
		$xwd = [];
		$matches = null;
		preg_match_all( '/;\s*(\w+)/', $_SERVER['HTTP_X_WIKIMEDIA_DEBUG'], $matches );
		if ( !empty( $matches[1] ) ) {
			$xwd = array_fill_keys( $matches[1], true );
		}
		unset( $matches );

		// This is passed as query parameter instead of header attribute,
		// but is nonetheless considered part of X-Wikimedia-Debug and must
		// only be enabled when X-Wikimedia-Debug is also enabled, due to caching.
		if ( isset( $_GET['forceprofile'] ) ) {
			$xwd['forceprofile'] = true;
		}
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
	if ( isset( $xwd['forceprofile'] )
		|| PHP_SAPI === 'cli'
		|| isset( $xwd['profile'] )
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
		if ( isset( $xwd['forceprofile'] ) || PHP_SAPI === 'cli' ) {
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
		if ( isset( $xwd['profile'] ) ) {
			register_postsend_function( function () {
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

				Xhgui_Saver::factory( [
					'save.handler' => 'mongodb',
					'db.host'      => 'mongodb://tungsten.eqiad.wmnet:27017',
					'db.db'        => 'xhprof',
					'db.options'   => [],
				] )->save( $data );
			} );
		}
	}

	unset( $xwd, $xhprofFlags );
}

/**
 * Sampling profiler for production traffic.
 *
 * If Xenon is enabled, register a shutdown callback to ask HHVM for
 * recently collected data. Will not end up yielding something every
 * request.
 *
 * Based on https://github.com/wikimedia/arc-lamp
 */
if ( extension_loaded( 'xenon' ) && ini_get( 'hhvm.xenon.period' ) ) {
	register_shutdown_function( function () {
		$data = HH\xenon_get_data();

		if ( empty( $data ) ) {
			return;
		}

		$entryPoint = basename( $_SERVER['SCRIPT_NAME'] );
		$reqMethod = '{' . $_SERVER['REQUEST_METHOD'] . '}';

		// Collate stack samples and fold into single lines.
		// This is the format expected by FlameGraph.
		$stacks = [];

		foreach ( $data as $sample ) {
			$stack = [];

			if ( empty( $sample['phpStack'] ) ) {
				continue;
			}

			foreach ( $sample['phpStack'] as $frame ) {
				if ( $frame['function'] === 'include' ) {
					// For file scope, just use the path as the name.
					$func = $frame['file'];
				} elseif ( $frame['function'] === '{closure}' && isset( $frame['line'] ) ) {
					// Annotate anonymous functions with their location in the
					// source code. Example: {closure:/path/to/file.php(123)}
					$func = "{closure:{$frame['file']}({$frame['line']})}";
				} else {
					$func = $frame['function'];
				}

				if ( $func !== end( $stack ) ) {
					$stack[] = $func;
				}
			}

			if ( count( $stack ) ) {
				// The last element is usually (but not always) the full file
				// path of the script name. We want things nice and consistent,
				// so we pop off the path if it is there, and push the basename
				// instead.
				if ( strpos( end( $stack ), $entryPoint ) !== false ) {
					array_pop( $stack );
				}
				$stack[] = $reqMethod;
				$stack[] = $entryPoint;

				$strStack = implode( ';', array_reverse( $stack ) );
				if ( !isset( $stacks[$strStack] ) ) {
					$stacks[$strStack] = 0;
				}
				$stacks[$strStack] += 1;
			}
		}

		$redis = new Redis();
		if ( $redis->connect( 'mwlog1001.eqiad.wmnet', 6379, 0.1 ) ) {
			foreach ( $stacks as $stack => $count ) {
				$redis->publish( 'xenon', "$stack $count" );
			}
		}
	} );
}
