<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.
#
# Included by PhpAutoPrepend-labs.php before any other wmf-config or mediawiki file.
# Uses no predefined state, other than plain PHP.
#
# Exposes:
# - $wmgProfiler (used by StartProfile-labs.php)

global $wmgProfiler;
$wmgProfiler = [];

/**
 * File overview:
 *
 * - Parse X-Wikimedia-Debug options.
 * - Enable request profiling.
 * - One-off profile to stdout (via MediaWiki).
 *
 * Other profiler features not yet available in Beta Cluster (T180761).
 */

if ( ini_get( 'hhvm.stats.enable_hot_profiler' ) ) {
	/**
	 * Parse X-Wikimedia-Debug options.
	 *
	 * See https://wikitech.wikimedia.org/wiki/X-Wikimedia-Debug
	 */
	$xwd = false;
	if ( isset( $_SERVER['HTTP_X_WIKIMEDIA_DEBUG'] ) ) {
		$xmd = [];

		// This is passed as query parameter instead of header attribute,
		// but is nonetheless considered part of X-Wikimedia-Debug and must
		// only be enabled when X-Wikimedia-Debug is also enabled, due to caching.
		if ( isset( $_GET['forceprofile'] ) ) {
			$xmd['forceprofile'] = true;
		}
	}

	/**
	 * Enable request profiling
	 */
	$xhprofFlags = XHPROF_FLAGS_NO_BUILTINS;
	if ( isset( $xwd['forceprofile'] ) ) {
		// Enable Xhprof now instead of waiting for MediaWiki to start it later.
		// This ensures a balanced and complete call graph. (T180183)
		xhprof_enable( $xhprofFlags );
	}

	/**
	 * One-off profile to stdout.
	 *
	 * MediaWiki's Profiler class can output raw profile data directly to the output
	 * of a web response (web), or in stdout (CLI).
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

	unset( $xhprofFlags, $xwd );
}
