<?php
// WARNING: This file is publically viewable on the web. Do not put private
// data here.

// NOTE: this file is loaded early on in WebStart.php, so be careful with
// globals.

$wmgUseXhprofProfiler = defined( 'HHVM_VERSION' )
	&& ini_get( 'hhvm.stats.enable_hot_profiler' );

if ( isset( $_REQUEST['forceprofile'] ) ) {
	// Non-logged profiling for debugging
	if ( $wmgUseXhprofProfiler ) {
		$wgProfiler['class'] = 'ProfilerXhprof';
		$wgProfiler['flags'] = XHPROF_FLAGS_NO_BUILTINS;
	} else {
		$wgProfiler['class'] = 'ProfilerStandard';
	}
	$wgProfiler['output'] = 'text';

} elseif ( isset( $_SERVER['HTTP_HOST'] )
	&& $_SERVER['HTTP_HOST'] === 'test2.wikipedia.org' )
{
	// Profiling hack for test2 wiki (not sampled, but shouldn't distort too
	// much)
	if ( $wmgUseXhprofProfiler ) {
		$wgProfiler['class'] = 'ProfilerXhprof';
		$wgProfiler['flags'] = XHPROF_FLAGS_CPU | XHPROF_FLAGS_MEMORY | XHPROF_FLAGS_NO_BUILTINS;
	} else {
		$wgProfiler['class'] = 'ProfilerStandard';
	}
	$wgProfiler['output'] = 'udp';
	$wgProfiler['profileID'] = 'test2';

} elseif ( false && $wmfDatacenter == 'eqiad' ) {
	// Normal case: randomly (or not) selected for logged profiling sample
	if ( $wmgUseXhprofProfiler ) {
		$wgProfiler['class'] = 'ProfilerXhprof';
		$wgProfiler['flags'] = XHPROF_FLAGS_CPU | XHPROF_FLAGS_MEMORY | XHPROF_FLAGS_NO_BUILTINS;
	} else {
		$wgProfiler['class'] = 'ProfilerStandard';
	}
	$wgProfiler['output'] = 'udp';
	$wgProfiler['sampling'] = 50;
	// $IP is something like '/srv/mediawiki/php-1.19'
	$version = str_replace( 'php-', '', basename( $IP ) );
	if ( PHP_SAPI === 'cli' ) {
		$wgProfiler['profileID'] = "cli-$version";
	} elseif ( strpos( $_SERVER['REQUEST_URI'], '/w/thumb.php' ) !== false ) {
		$wgProfiler['profileID'] = "thumb-$version";
	} elseif ( strpos( $_SERVER['REQUEST_URI'], '/rpc/RunJobs.php' ) !== false ) {
		$wgProfiler['profileID'] = "runjobs-$version";
	} else {
		$wgProfiler['profileID'] = $version;
	}

} elseif ( $wmfRealm === 'labs' ) {
	if ( $wmgUseXhprofProfiler ) {
		$wgProfiler['class'] = 'ProfilerXhprof';
		$wgProfiler['flags'] = XHPROF_FLAGS_CPU | XHPROF_FLAGS_MEMORY | XHPROF_FLAGS_NO_BUILTINS;
	} else {
		$wgProfiler['class'] = 'ProfilerStandard';
	}
	$wgProfiler['output'] = 'udp';
	$coreGit = new GitInfo( $IP );
	$wgProfiler['profileID'] = $coreGit->getHeadSHA1() ?: 'labs';
}

if ( $wmgUseXhprofProfiler
	&& isset( $_SERVER['HTTP_FORCE_LOCAL_XHPROF'] )
	&& isset( $_SERVER['REMOTE_ADDR'] )
	&& $_SERVER['REMOTE_ADDR'] == '127.0.0.1'
	&& is_writable( '/tmp/xhprof' ) )
{
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
