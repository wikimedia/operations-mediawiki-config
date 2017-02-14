<?php
// WARNING: This file is publically viewable on the web. Do not put private
// data here.

// NOTE: this file is loaded early on in WebStart.php, so be careful with
// globals.

$XWD = false;

// Parse X-Wikimedia-Header (if present) into an associative array.
if ( isset( $_SERVER['HTTP_X_WIKIMEDIA_DEBUG'] ) ) {
	parse_str( preg_replace( '/; ?/', '&', $_SERVER['HTTP_X_WIKIMEDIA_DEBUG'] ), $XWD );
}

if ( ini_get( 'hhvm.stats.enable_hot_profiler' ) ) {
	// Single-request profiling, via 'forceprofile=1' (web) or '--profiler=text' (CLI).
	if (
		( isset( $_GET['forceprofile'] ) && isset( $_SERVER['HTTP_X_WIKIMEDIA_DEBUG'] ) )
		|| PHP_SAPI === 'cli'
	) {
		$wgProfiler = [
			'class'  => 'ProfilerXhprof',
			'flags'  => XHPROF_FLAGS_NO_BUILTINS,
			'output' => 'text',
		];
	// If HTTP_FORCE_LOCAL_XHPROF is set in the shell environment,
	// profile all requests from localhost.
	} elseif (
		isset( $_SERVER['HTTP_FORCE_LOCAL_XHPROF'] )
		&& isset( $_SERVER['REMOTE_ADDR'] )
		&& $_SERVER['REMOTE_ADDR'] == '127.0.0.1'
		&& is_writable( '/tmp/xhprof' )
	) {
		xhprof_enable();
		register_shutdown_function( function() {
			$prof = xhprof_disable();
			$titleFormat = "%-75s %6s %13s %13s %13s\n";
			$format = "%-75s %6d %13.3f %13.3f %13.3f%%\n";
			$out = sprintf( $titleFormat, 'Name', 'Calls', 'Total', 'Each', '%' );
			if ( empty( $prof['main()']['wt'] ) ) {
				return;
			}
			$total = $prof['main()']['wt'];
			uksort( $prof, function( $a, $b ) use ( $prof ) {
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
	} else {
		// 1:1000 request profiling

		// $wgProfiler = array(
		// 	'class'    => 'ProfilerXhprof',
		// 	'exclude'  => array( 'section.*', 'run_init*' ),
		// 	'flags'    => ( XHPROF_FLAGS_CPU | XHPROF_FLAGS_MEMORY | XHPROF_FLAGS_NO_BUILTINS ),
		// 	'output'   => 'stats',
		// 	'prefix'   => 'xhprof',
		// 	'sampling' => 10000,
		// );
	}
}

if ( extension_loaded( 'xenon' ) && ini_get( 'hhvm.xenon.period' ) ) {
	register_shutdown_function( function () {
		$data = HH\xenon_get_data();

		if ( empty( $data ) ) {
			return;
		}


		$entryPoint = basename( $_SERVER['SCRIPT_NAME'] );

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

if (
	ini_get( 'hhvm.stats.enable_hot_profiler' ) &&
	!empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) &&
	( isset( $XWD['profile'] ) || mt_rand( 1, 100000 ) === 1 )
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
