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
	 * - 'sampleprofiler': One-off sampled profile to stdout.
	 *    This cannot be used if 'forceprofile' or 'profile' is enabled.
	 *    This is experimental and currently only on Beta Cluster (see T176916).
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

	if ( isset( $xwd['sampleprofiler'] )
		&& ( isset( $xwd['forceprofile'] ) || isset( $xwd['profile'] ) )
	) {
		// Mutually exclusive because XHProf can not be simultaneously started
		// in both sampling and full mode.
		print 'The X-Wikimedia-Debug "sampleprofiler" attribute cannot be used with "profile" or "forceprofile"';
		print "\n";
		exit( 1 );
	}

	if ( isset( $xwd['sampleprofiler'] ) ) {
		xhprof_sample_enable();
		register_shutdown_function( function () {
			// Note: xhprof_sample_disable() returns data in a different format than
			// xhprof_disable(). This makes sense given there is no graph and no per-call
			// metrics in sampling mode, but important to remember.
			//
			// The xhprof_sample_disable() function returns an array of strings
			// keyed by a micro timestamp. The string values are captured stacks
			// with each frame description separated by `==>`.
			//
			// Example:
			//
			// ```
			// [
			// "1526998006.300000": "main()==>{internal}==>run_init::/srv/mediawiki/php-master/load.php==>ResourceLoader::respond>array_map@1",
			// ]
			// ```

			// Prepare the data for Wikimedia's flame graph pipeline.
			// TODO: Document the Redis-to-FlameGraph service.
			// - Use the format required by FlameGraph.pl (semicolon as frame separator)
			// - Prepend entrypoint and request method as outer frame.
			//   This is similar to the Xenon logic in profiler.php.
			$stackPrefix = implode( ';', [
				basename( $_SERVER['SCRIPT_NAME'] ),
				'{' . $_SERVER['REQUEST_METHOD'] . '}'
			] ) . ';';
			$stacks = [];
			foreach ( xhprof_sample_disable() as $frame ) {
				$strStack = $stackPrefix . strtr( $frame, [ '==>' => ';' ] );
				if ( !isset( $stacks[$strStack] ) ) {
					$stacks[$strStack] = 0;
				}
				$stacks[$strStack] += 1;
			}
			// Send stacks to s/Redis/HTML output/
			echo "<!--\n";
			foreach ( $stacks as $stack => $count ) {
				// Escape, just in case
				echo strtr( "$stack $count\n", [ '>' => '&gt;' ] );
			}
			echo "\n-->\n";
		} );
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
