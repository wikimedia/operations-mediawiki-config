<?php
# WARNING: This file is publically viewable on the web. Do not put private data here.

# NOTE: this file is loaded early on in WebStart.php, so be careful with globals.

# Non-logged profiling for debugging
if ( isset( $_REQUEST['forceprofile'] ) ) {
	$wgProfiler['class'] = 'ProfilerStandard';
	$wgProfiler['output'] = 'text';
# Profiling hack for test2 wiki (not sampled, but shouldn't distort too much)
} elseif ( isset( $_SERVER['HTTP_HOST'] ) && $_SERVER['HTTP_HOST'] === 'test2.wikipedia.org' ) {
	$wgProfiler['class'] = 'ProfilerStandard';
	$wgProfiler['output'] = 'udp';
	$wgProfiler['profileID'] = 'test2';
# Normal case: randomly (or not) selected for logged profiling sample
} elseif ( PHP_SAPI !== 'cli' && $wmfDatacenter == 'eqiad' ) {
	$wgProfiler['class'] = 'ProfilerStandard';
	$wgProfiler['output'] = 'udp';
	$wgProfiler['sampling'] = 50;
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
	$wgProfiler['class'] = 'ProfilerStandard';
	$wgProfiler['output'] = 'udp';
	$coreGit = new GitInfo( $IP );
	$wgProfiler['profileID'] = $coreGit->getHeadSHA1() ?: 'labs';
}
