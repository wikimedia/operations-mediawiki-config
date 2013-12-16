<?php
# WARNING: This file is publically viewable on the web. Do not put private data here.

# NOTE: this file is loaded early on in WebStart.php, so be careful with globals.

# Non-logged profiling for debugging
if ( isset( $_REQUEST['forceprofile'] ) ) {
	$wgProfiler['class'] = 'ProfilerSimpleText';
# Non-logged profiling for debugging
} elseif ( isset( $_REQUEST['forcetrace'] ) ) {
	$wgProfiler['class'] = 'ProfilerSimpleTrace';
# Profiling hack for test2 wiki (not sampled, but shouldn't distort too much)
} elseif ( isset( $_SERVER['HTTP_HOST'] ) && $_SERVER['HTTP_HOST'] === 'test2.wikipedia.org' ) {
	$wgProfiler['class'] = 'ProfilerSimpleUDP';
	$wgProfiler['profileID'] = 'test2';
# Normal case: randomly (or not) selected for logged profiling sample
} elseif ( PHP_SAPI !== 'cli' && $wmgDatacenter == 'eqiad' && ( mt_rand() % 50 ) == 0 ) {
	$wgProfiler['class'] = 'ProfilerSimpleUDP';
	// $IP is something like '/usr/local/apache/common-local/php-1.19'
	$version = str_replace( 'php-', '', basename( $IP ) );
	if ( strpos( $_SERVER['REQUEST_URI'], '/w/thumb.php' ) !== false ) {
		$wgProfiler['profileID'] = "thumb-$version";
	} else {
		$wgProfiler['profileID'] = $version;
	}
} elseif ( $wmgRealm === 'labs' ) {
	$wgProfiler['class'] = 'ProfilerSimpleUDP';
	$coreGit = new GitInfo( $IP );
	$wgProfiler['profileID'] = $coreGit->getHeadSHA1() ?: 'labs';
}
