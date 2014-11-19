<?php
# WARNING: This file is publically viewable on the web. Do not put private data here.

# NOTE: this file is loaded early on in WebStart.php, so be careful with globals.

# Non-logged profiling for debugging
if ( isset( $_REQUEST['forceprofile'] ) ) {
	if ( class_exists( 'ProfilerOutput' ) ) {
		$wgProfiler['class'] = 'ProfilerStandard';
		$wgProfiler['output'] = 'text';
	} else {
		$wgProfiler['class'] = 'ProfilerSimpleText';
	}
# Profiling hack for test2 wiki (not sampled, but shouldn't distort too much)
} elseif ( isset( $_SERVER['HTTP_HOST'] ) && $_SERVER['HTTP_HOST'] === 'test2.wikipedia.org' ) {
	if ( class_exists( 'ProfilerOutput' ) ) {
		$wgProfiler['class'] = 'ProfilerStandard';
		$wgProfiler['output'] = 'udp';
	} else {
		$wgProfiler['class'] = 'ProfilerSimpleUDP';
	}
	$wgProfiler['profileID'] = 'test2';
# Normal case: randomly (or not) selected for logged profiling sample
} elseif ( PHP_SAPI !== 'cli' && $wmfDatacenter == 'eqiad' && ( mt_rand() % 50 ) == 0 ) {
	if ( class_exists( 'ProfilerOutput' ) ) {
		$wgProfiler['class'] = 'ProfilerStandard';
		$wgProfiler['output'] = 'udp';
	} else {
		$wgProfiler['class'] = 'ProfilerSimpleUDP';
	}
	// $IP is something like '/srv/mediawiki/php-1.19'
	$version = str_replace( 'php-', '', basename( $IP ) );
	if ( strpos( $_SERVER['REQUEST_URI'], '/w/thumb.php' ) !== false ) {
		$wgProfiler['profileID'] = "thumb-$version";
	} elseif ( strpos( $_SERVER['REQUEST_URI'], '/rpc/RunJobs.php' ) !== false ) {
		$wgProfiler['profileID'] = "runjobs-$version";
	} else {
		$wgProfiler['profileID'] = $version;
	}
} elseif ( $wmfRealm === 'labs' ) {
	if ( class_exists( 'ProfilerOutput' ) ) {
		$wgProfiler['class'] = 'ProfilerStandard';
		$wgProfiler['output'] = 'udp';
	} else {
		$wgProfiler['class'] = 'ProfilerSimpleUDP';
	}
	$coreGit = new GitInfo( $IP );
	$wgProfiler['profileID'] = $coreGit->getHeadSHA1() ?: 'labs';
}

if ( defined( 'HHVM_VERSION' )
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
