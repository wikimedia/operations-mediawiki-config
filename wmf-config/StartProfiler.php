<?php
// WARNING: This file is publically viewable on the web. Do not put private
// data here.

// NOTE: this file is loaded early on in WebStart.php, so be careful with
// globals.

if ( ini_get( 'hhvm.stats.enable_hot_profiler' ) ) {
	// Single-request profiling, via 'forceprofile=1' (web) or '--profiler=text' (CLI).
	if (
		( isset( $_GET['forceprofile'] ) && isset( $_SERVER['HTTP_X_WIKIMEDIA_DEBUG'] ) )
		|| PHP_SAPI === 'cli'
	) {
		$wgProfiler = array(
			'class'  => 'ProfilerXhprof',
			'flags'  => XHPROF_FLAGS_NO_BUILTINS,
			'output' => 'text',
		);
	}

	// If HTTP_FORCE_LOCAL_XHPROF is set in the shell environment,
	// profile all requests from localhost.
	if (
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
	}
}

if ( extension_loaded( 'xenon' ) && ini_get( 'hhvm.xenon.period' ) ) {
	register_shutdown_function( function () {
		// Function names that should be excluded from the trace.
		$omit = array( 'include', '{closure}' );

		$data = HH\xenon_get_data();

		if ( empty( $data ) ) {
			return;
		}

		// Collate stack samples and fold into single lines.
		// This is the format expected by FlameGraph.
		$stacks = array();

		foreach ( $data as $sample ) {
			$stack = array();

			if ( empty( $sample['phpStack'] ) ) {
				continue;
			}

			foreach( $sample['phpStack'] as $frame ) {
				$func = $frame['function'];
				if ( $func !== end( $stack ) && !in_array( $func, $omit ) ) {
					$stack[] = $func;
				}
			}

			if ( count( $stack ) ) {
				$strStack = implode( ';', array_reverse( $stack ) );
				if ( !isset( $stacks[$strStack] ) ) {
					$stacks[$strStack] = 0;
				}
				$stacks[$strStack] += 1;
			}
		}

		$redis = new Redis();
		if ( $redis->connect( 'fluorine.eqiad.wmnet', 6379, 0.1 ) ) {
			foreach ( $stacks as $stack => $count ) {
				$redis->publish( 'xenon', "$stack $count" );
			}
		}
	} );
}
