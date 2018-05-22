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
	 *
	 * - 'forceprofile': One-off profile to stdout.
	 * - 'profile': One-off profile to XHGui. – Unavailable on Beta Cluster (T180761)
	 * - 'readonly': (See wmf-config/CommonSettings.php).
	 * - 'log': (See wmf-config/logging.php).
	 * - 'sampleprofiler': Configure XHProf (if enabled via `profile` or `forceprofile`)
	 *    to use sampled profiling instead of full profiling. – This is experimental
	 *    and only on Beta Cluster currently, as part of determining whether it can
	 *    be used as replacement for HHVM Xenon, for use on  all prod requests.
	 *    See T176916 for more information.
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
		if ( isset( $xwd['sampleprofiler'] ) ) {
			// XXX:
			// We enable it here so that the profiler starts immediately instead of
			// only when MediaWiki later instantiates ProfilerXhprof. Normally that
			// means we call xhprof_enable twice. XHProf internally ignores the
			// second call if the profiler was already started.
			// The sampling experiment could break this because it means we're
			// starting early in sampling mode, but then later MediaWiki's ProfilerXhprof
			// will call the regular xhprof_enable (it doesn't support sampling).
			// This *should* be fine because both of these functions go through
			// the same code path that simply ignores the call of any profiler started,
			// regardless of different flags or sampling mode.
			// Additionally, it *should* also be the case that letting MediaWiki
			// collect the data via xhprof_disable (instead of xhprof_sample_disable()
			// magically works because they are both the same function internally.
			xhprof_sample_enable();
		} else {
			xhprof_enable( $xhprofFlags );
		}

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
