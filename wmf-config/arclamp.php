<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.
#
# Included by PhpAutoPrepend.php BEFORE any other wmf-config or MediaWiki file.
# MUST NOT use any predefined state, only plain PHP.
#
# This both for PRODUCTION and for Beta Cluster.
#

/**
 * Flush data from Xenon, the sampling profiler for production traffic.
 *
 * If Xenon is enabled, ask HHVM for recently collected data.
 * Will not actually yielding something every request, only at particular
 * intervals controlled by hhvm.xenon.period.
 *
 * Based on https://github.com/wikimedia/arc-lamp
 *
 * @param string $redisHost Redis hostname
 * @param int $redisPort Redis port
 * @param float $redisTimeout Redis timeout (for socket reads)
 */
function wmfArcLampFlush( $redisHost, $redisPort, $redisTimeout ) {
	if ( !extension_loaded( 'xenon' ) || !ini_get( 'hhvm.xenon.period' ) ) {
		return;
	}

	$data = HH\xenon_get_data();
	if ( !$data ) {
		return;
	}

	$entryPoint = basename( $_SERVER['SCRIPT_NAME'] );
	$reqMethod = '{' . $_SERVER['REQUEST_METHOD'] . '}';

	// Collate stack samples and fold into single lines,
	// in the format expected by FlameGraph.
	$stacks = [];
	foreach ( $data as $sample ) {
		if ( empty( $sample['phpStack'] ) ) {
			continue;
		}
		$stack = [];
		foreach ( $sample['phpStack'] as $frame ) {
			if ( $frame['function'] === 'include' ) {
				// For file scope, just use the path as the name.
				$func = $frame['file'];
			} elseif ( $frame['function'] === '{closure}' && isset( $frame['line'] ) ) {
				// Annotate anonymous functions with their location in the
				// source code. Example: {closure:/path/to/file.php(123)}
				$func = "{closure:{$frame['file']}({$frame['line']})}";
			} else {
				$func = $frame['function'];
			}

			if ( $func !== end( $stack ) ) {
				$stack[] = $func;
			}
		}
		if ( $stack ) {
			// The last element is usually (but not always) the full file
			// path of the script name. We want things nice and consistent,
			// so we pop off the path if it is there, and push the basename
			// instead.
			if ( strpos( end( $stack ), $entryPoint ) !== false ) {
				array_pop( $stack );
			}
			$stack[] = $reqMethod;
			$stack[] = $entryPoint;

			$strStack = implode( ';', array_reverse( $stack ) );
			if ( !isset( $stacks[$strStack] ) ) {
				$stacks[$strStack] = 0;
			}
			$stacks[$strStack] += 1;
		}
	}

	// Profiler instrumentation must be gentle.
	// No exception may affect the overall request process.
	try {
		$redis = new Redis();
		$ok = $redis->connect( $redisHost, $redisPort, $redisTimeout );
		if ( !$ok ) {
			return;
		}
		foreach ( $stacks as $stack => $count ) {
			$redis->publish( 'xenon', "$stack $count" );
		}
	} catch ( Exception $e ) {
		// Known failure scenarios:
		//
		// - "RedisException: read error on connection"
		//   Each publish() in the above loop writes data to Redis and
		//   subsequently reads from the socket for Redis' response.
		//   If any socket read takes longer than $timeout, it throws (T206092).
		//   As of writing, this is rare (a few times per day at most),
		//   which is considered an acceptable loss in profile samples.

		// Write to log with low severity
		trigger_error( get_class( $e ) . ': ' . $e->getMessage(), E_USER_NOTICE );
	}
}

function wmfSetupArcLamp( $options ) {
	register_shutdown_function( function () use ( $options ) {
		wmfArcLampFlush(
			$options['redis-host'],
			$options['redis-port'],
			$options['redis-timeout']
		);
	} );
}
