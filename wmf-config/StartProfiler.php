<?php
# WARNING: This file is publically viewable on the web. Do not put private data here.

# NOTE: this file is loaded early on in WebStart.php, so be careful with globals.

# Non-logged profiling for debugging
if ( @defined( $_REQUEST['forceprofile'] ) ) {
	require_once( $IP . '/includes/profiler/ProfilerSimpleText.php' );
	$wgProfiler = new ProfilerSimpleText( array() );
# Non-logged profiling for debugging
} elseif ( @defined( $_REQUEST['forcetrace'] ) ) {
	require_once( $IP . '/includes/profiler/ProfilerSimpleTrace.php' );
	$wgProfiler = new ProfilerSimpleTrace( array() );
# Profiling hack for test2 wiki (not sampled, but shouldn't distort too much)
} elseif ( @$_SERVER['HTTP_HOST'] === 'test2.wikipedia.org' ) {
	require_once( $IP . '/includes/profiler/ProfilerSimpleUDP.php' );
	$wgProfiler = new ProfilerSimpleUDP( array() );
	$wgProfiler->setProfileID( 'test2' );
# Normal case: randomly selected for logged profiling sample
} elseif ( PHP_SAPI == 'cli' || ( mt_rand( 0, 0x7fffffff ) % 50 ) == 0 ) {
	require_once( $IP . '/includes/profiler/ProfilerSimpleUDP.php' );
	$wgProfiler = new ProfilerSimpleUDP( array() );
	// $IP is something like '/usr/local/apache/common-local/php-1.19'
	$version = str_replace( 'php-', '', basename( $IP ) );
	if ( PHP_SAPI == 'cli' ) {
		$wgProfiler->setProfileID( "cli-$version" );
	} elseif ( strpos( @$_SERVER['REQUEST_URI'], '/w/thumb.php' ) !== false ) {
		$wgProfiler->setProfileID( "thumb-$version" );
	} else {
		$wgProfiler->setProfileID( $version );
	}
	# $wgProfiler->setMinimum(5 /* seconds */);
# WTF is this for?
} elseif ( defined( 'MW_FORCE_PROFILE' ) ) {
	require_once( $IP . '/includes/profiler/Profiler.php' );
	$wgProfiler = new Profiler( array() );
# Normal case: randomly not selected for logged profiling sample
} else {
	require_once( $IP . '/includes/profiler/ProfilerStub.php' );
}
