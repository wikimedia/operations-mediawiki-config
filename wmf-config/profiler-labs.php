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
 * - One-off profile to stdout (via MediaWiki)
 *
 * Other profiler features not yet available in Beta Cluster (T180766).
 */

if ( ini_get( 'hhvm.stats.enable_hot_profiler' ) ) {
	/**
	 * One-off profile to stdout.
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

	}
}
