<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.
#
# Included by PhpAutoPrepend-labs.php BEFORE any other wmf-config or mediawiki file.
# MUST NOT use any predefined state, only plain PHP.
#
# Exposes:
# - $wmgProfiler (used by CommonSettings.php)

require_once __DIR__ . '/arclamp.php';

global $wmgProfiler;

$wmgProfiler = [];

/**
 * File overview:
 *
 * - Parse X-Wikimedia-Debug options.
 * - Enable request profiling.
 * - One-off profile to stdout (via MediaWiki).
 * - <s>One-off profile to XHGui.</s> (Not yet availabe in Beta Cluster, T180761)
 * - Sampling profiler for live traffic.
 */

if ( ini_get( 'hhvm.stats.enable_hot_profiler' ) ) {
	/**
	 * Parse X-Wikimedia-Debug options.
	 *
	 * See https://wikitech.wikimedia.org/wiki/X-Wikimedia-Debug
	 *
	 * - 'forceprofile': One-off profile to stdout.
	 * - 'profile': One-off profile to XHGui. – Unavailable on Beta Cluster (T180761)
	 * - 'readonly': (See wmf-config/CommonSettings.php).
	 * - 'log': (See wmf-config/logging.php).
	 *
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
	}

	unset( $xwd, $xhprofFlags );
}

register_shutdown_function( function () {
	wmfArcLampFlush(
		// Redis host
		'deployment-fluorine02.deployment-prep.eqiad.wmflabs',
		// Redis port
		6379,
		// Redis timeout (for socket reads)
		1.0
	);
} );
