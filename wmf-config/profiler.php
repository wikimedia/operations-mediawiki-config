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
 * 1. Parse X-Wikimedia-Header
 * 2. One-off profile to stdout (via MediaWiki)
 * 3. One-off profile to /tmp (from localhost)
 * 4. Sampling profiler for production traffic
 * 5. One-off profile to XHGui.
 */

/**
 * 1) Parse X-Wikimedia-Header
 *
 * If the X-Wikimedia-Header is present, parse it into an associative array.
 *
 * See https://wikitech.wikimedia.org/wiki/X-Wikimedia-Debug
 */
$XWD = false;
if ( isset( $_SERVER['HTTP_X_WIKIMEDIA_DEBUG'] ) ) {
	parse_str( preg_replace( '/; ?/', '&', $_SERVER['HTTP_X_WIKIMEDIA_DEBUG'] ), $XWD );
}

if ( ini_get( 'hhvm.stats.enable_hot_profiler' ) ) {
	/**
	 * 2) One-off profile to stdout
	 *
	 * MediaWiki's Profiler class can output raw profile data directly to the output
	 * of a web response (web), or in stdout (CLI).
	 *
	 * For web: Set X-Wikimedia-Debug (to bypass cache) and query param 'forceprofile=1'.
	 * For CLI: Set CLI option '--profiler=text'.
	 *
	 * See https://www.mediawiki.org/wiki/Manual:Profiling
	 */
	if (
		( isset( $_GET['forceprofile'] ) && isset( $_SERVER['HTTP_X_WIKIMEDIA_DEBUG'] ) )
		|| PHP_SAPI === 'cli'
	) {
		$wmgProfiler = [
			'class'  => 'ProfilerXhprof',
			'flags'  => XHPROF_FLAGS_NO_BUILTINS,
			'output' => 'text',
		];

	/**
	 * 3) One-off profile to /tmp
	 *
	 * When making requests to the local server using shell access,
	 * setting the 'Force-Local-XHProf: 1' header will write raw profile data
	 * directly to a local file in /tmp/xhprof/.
	 *
	 * Note: This is only allowed for requests within the same server.
	 */
	} elseif (
		isset( $_SERVER['HTTP_FORCE_LOCAL_XHPROF'] )
		&& isset( $_SERVER['REMOTE_ADDR'] )
		&& $_SERVER['REMOTE_ADDR'] == '127.0.0.1'
		&& is_writable( '/tmp/xhprof' )
	) {
		xhprof_enable();
		register_shutdown_function( function () {
			$prof = xhprof_disable();
			$titleFormat = "%-75s %6s %13s %13s %13s\n";
			$format = "%-75s %6d %13.3f %13.3f %13.3f%%\n";
			$out = sprintf( $titleFormat, 'Name', 'Calls', 'Total', 'Each', '%' );
			if ( empty( $prof['main()']['wt'] ) ) {
				return;
			}
			$total = $prof['main()']['wt'];
			uksort( $prof, function ( $a, $b ) use ( $prof ) {
				if ( $prof[$a]['wt'] < $prof[$b]['wt'] ) {
					return 1;
				} elseif ( $prof[$a]['wt'] > $prof[$b]['wt'] ) {
					return -1;
				} else {
					return 0;
				}
			} );

			foreach ( $prof as $name => $info ) {
				$out .= sprintf( $format, $name, $info['ct'], $info['wt'] / 1000,
					$info['wt'] / $info['ct'] / 1000,
					$info['wt'] / $total * 100 );
			}
			file_put_contents( '/tmp/xhprof/' . date( 'Y-m-d\TH:i:s' ) . '.prof', $out );
		} );
	}
}

/**
 * 4) Sampling profiler for production traffic
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

/**
 * 5) One-off profile to XHGui
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
if (
	ini_get( 'hhvm.stats.enable_hot_profiler' ) &&
	// Require X-Forwarded-For to ignore non-remote requests (e.g. PyBal)
	!empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) &&
	isset( $XWD['profile'] )
) {
	xhprof_enable( XHPROF_FLAGS_CPU | XHPROF_FLAGS_MEMORY | XHPROF_FLAGS_NO_BUILTINS );

	register_postsend_function( function () use ( $XWD ) {
		$data = [ 'profile' => xhprof_disable() ];

		$sec  = $_SERVER['REQUEST_TIME'];
		$usec = $_SERVER['REQUEST_TIME_FLOAT'] - $sec;

		$keyWhitelist = array_flip( [
			'HTTP_HOST', 'HTTP_X_WIKIMEDIA_DEBUG', 'REQUEST_METHOD',
			'REQUEST_START_TIME', 'REQUEST_TIME', 'REQUEST_TIME_FLOAT',
			'SERVER_ADDR', 'SERVER_NAME', 'THREAD_TYPE', 'action'
		] );

		// Create sanitized copies of $_SERVER, $_ENV, and $_GET:
		$server = array_intersect_key( $_SERVER, $keyWhitelist );
		$env = array_intersect_key( $_ENV, $keyWhitelist );
		$get = array_intersect_key( $_GET, $keyWhitelist );

		// Strip everything from the query string except 'action=' param:
		preg_match( '/action=[^&]+/', $_SERVER['REQUEST_URI'], $matches );
		$qs = $matches ? '?' . $matches[0] : '';
		$url = $_SERVER['SCRIPT_NAME'] . $qs;

		// If profiling was explicitly requested (via X-Wikimedia-Debug)
		// then include the unique request ID in the reported URL, to make
		// it easy for the person debugging to find the request in Xhgui.
		if ( $XWD && method_exists( 'WebRequest', 'getRequestId' ) ) {
			$reqId = WebRequest::getRequestId();
			$url = '//' . $reqId . $url;
			$env['UNIQUE_ID'] = $reqId;
			$server['UNIQUE_ID'] = $reqId;
		}

		// Include web server name (SERVER_NAME is e.g. wikipedia.org,
		// SERVER_ADDR is the LVS service name, e.g. appservers.svc)
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
