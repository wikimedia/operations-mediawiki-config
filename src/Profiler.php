<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.
#
# Included by PhpAutoPrepend.php BEFORE any other wmf-config or mediawiki file.
# MUST NOT use any predefined state, only plain PHP.
#
# Exposes:
# - $wmgProfiler (used by CommonSettings.php)

namespace Wikimedia\MWConfig;

use ExcimerProfiler;
use PDO;
use Redis;

require_once __DIR__ . '/XWikimediaDebug.php';

class Profiler {
	/**
	 * Start any profilers if enabled for this process.
	 *
	 * @param array $options Associative array of options:
	 *   - redis-host: The host used for Xenon events
	 *   - redis-port: The port used for Xenon events
	 *   - redis-timeout: The redis socket timeout
	 *   - xhgui-conf: [optional] The configuration array to pass to XhguiSaverPdo
	 *     - pdo.connect: connection string for PDO (e.g. `mysql:host=mydbhost;dbname=xhgui`)
	 *     - pdo.table: table name within the xhgui database where the profiles are stored.
	 *   - statsd: [optional] The host address for StatsD messages
	 */
	public static function setup( array $options ): void {
		global $wmgProfiler;

		$wmgProfiler = [];

		if ( extension_loaded( 'tideways_xhprof' ) ) {
			// Used for XHGui or inline profile.
			// No-op unless enabled via WikimediaDebug (web) or --profile (CLI).
			self::tidewaysSetup( $options );
		}

		if ( PHP_SAPI !== 'cli' && extension_loaded( 'excimer' ) ) {
			// Used for unconditional sampling of production web requests.
			self::excimerSetup( $options );
		}
	}

	/**
	 * Set up Tideways XHProf.
	 *
	 * @param array $options
	 */
	private static function tidewaysSetup( $options ) {
		global $wmgProfiler;
		$xwd = XWikimediaDebug::getInstance();
		$profileToStdout = $xwd->hasOption( 'forceprofile' );
		$profileToXhgui = $xwd->hasOption( 'profile' ) && !empty( $options['xhgui-conf'] );

		// - TIDEWAYS_XHPROF_FLAGS_CPU:
		//   Adds 'cpu' keys to profile entries.
		//
		// - TIDEWAYS_XHPROF_FLAGS_MEMORY:
		//   Adds 'mu' and 'pmu' keys to profile entries.
		//
		// - TIDEWAYS_XHPROF_FLAGS_NO_BUILTINS:
		//   Doesn't modify output format, but makes output more concise.
		$xhprofFlags = TIDEWAYS_XHPROF_FLAGS_CPU | TIDEWAYS_XHPROF_FLAGS_MEMORY | TIDEWAYS_XHPROF_FLAGS_NO_BUILTINS;

		// For web requests with XWD "profile" or "forceprofile", start the profiler now.
		//
		// This ensures a balanced and more complete call graph (T180183). This is important for
		// web requests because we want to measure even the pre-MediaWiki setup (such as multiversion
		// and wmf-config) which is significant during low-latency requests.
		if ( $profileToStdout || $profileToXhgui ) {
			tideways_xhprof_enable( $xhprofFlags );

			// https://wikitech.wikimedia.org/wiki/X-Wikimedia-Debug#Plaintext_request_profile
			if ( $profileToStdout ) {
				$wmgProfiler = [
					'class' => 'ProfilerXhprof',
					'flags' => $xhprofFlags,
					// T247332
					'running' => true,
					'output' => 'text',
				];
			}

		// On the CLI, there is virtually no pre-MW setup to measure (multiversion is handled by
		// 'mwscript' in the the parent process), and we can't officially know the parsed MW CLI
		// args at this time, and we're generally not interested in the little bit of process setup
		// for profiling of long-running CLI scripts. Instead, on the CLI, MediaWiki will start the
		// profiler on its own based on $wmgProfiler.
		} elseif ( PHP_SAPI === 'cli' ) {
			$wmgProfiler = [
				'class' => 'ProfilerXhprof',
				'flags' => $xhprofFlags,
				'output' => 'text',
			];
		}

		// For web requests with XWD "profile" attribute set, instrument a web request
		// and save the profile to XHGui.
		if ( $profileToXhgui ) {
			// XHGui save callback
			$saveCallback = static function () use ( $options ) {
				require_once __DIR__ . '/../src/XhguiSaverPdo.php';

				// These globals are set by private/PrivateSettings.php and may only be
				// read by wmf-config after MediaWiki is initialised.
				// The profiler is set up much earlier via PhpAutoPrepend, as such,
				// only materialise these globals during the save callback, not sooner.
				global $wmgXhguiDBuser, $wmgXhguiDBpassword;

				$profile = tideways_xhprof_disable();
				if ( !isset( $profile['main()'] ) ) {
					// There isn't valid profile data to save (T271865).
					return;
				}

				$requestTimeFloat = explode( '.', sprintf( '%.6F', $_SERVER['REQUEST_TIME_FLOAT'] ) );
				$sec  = $requestTimeFloat[0];
				$usec = $requestTimeFloat[1] ?? 0;

				// Fake the URL to have a prefix of the request ID, that way it can
				// be easily found through XHGui through a predictable search URL
				// that looks for the request ID.
				// Matches mediawiki/core: WebRequest::getRequestId (T253674).
				$reqId = $_SERVER['HTTP_X_REQUEST_ID'] ?? $_SERVER['UNIQUE_ID'] ?? null;

				// Create a simplified url with just script name and 'action' query param
				$qs = isset( $_GET['action'] ) ? ( '?action=' . $_GET['action'] ) : '';
				$url = '//' . $reqId . $_SERVER['SCRIPT_NAME'] . $qs;

				// Create sanitized copies of $_SERVER, $_ENV, and $_GET that are
				// appropiate for exposing publicly to the web.
				// This intentionally omits 'REQUEST_URI' (added later)
				$serverKeys = array_flip( [
					'HTTP_X_REQUEST_ID', 'UNIQUE_ID',
					'HTTP_HOST', 'HTTP_X_WIKIMEDIA_DEBUG', 'REQUEST_METHOD',
					'REQUEST_START_TIME', 'REQUEST_TIME', 'REQUEST_TIME_FLOAT',
					'SERVER_NAME'
				] );
				$getKeys = array_flip( [ 'action' ] );
				$server = array_intersect_key( $_SERVER, $serverKeys );
				$get = array_intersect_key( $_GET, $getKeys );
				$env = [];

				// Add hostname of current web server.
				// - Not SERVER_NAME which is usually identical to HTTP_HOST, e.g. wikipedia.org.
				// - Not SERVER_ADDR which the local IP address, e.g. 10.xx.xx.xx.
				// Get what we're looking for from uname.
				$server['HOSTNAME'] = php_uname( 'n' );

				// Re-insert scrubbed URL as REQUEST_URL:
				$server['REQUEST_URI'] = $url;

				// Based on https://github.com/perftools/php-profiler/blob/v0.5.0/src/ProfilingData.php#L26
				$data = [
					'profile' => $profile,
					'meta' => [
						'url' => $url,
						'SERVER' => $server,
						'get' => $get,
						'env' => $env,
						'simple_url' => $url,
						'request_ts_micro' => [ 'sec' => $sec, 'usec' => $usec ],
					]
				];

				if ( !empty( $options['xhgui-conf']['pdo.connect'] )
					&& $wmgXhguiDBuser
					&& $wmgXhguiDBpassword
				) {
					$pdo = new PDO(
						$options['xhgui-conf']['pdo.connect'],
						$wmgXhguiDBuser,
						$wmgXhguiDBpassword
					);
					$saver = new XhguiSaverPdo( $pdo, $options['xhgui-conf']['pdo.table'] );
					$saver->save( $data );
				}
			};

			// Register the callback as a shutdown_function, so that the profile
			// includes MediaWiki's post-send DeferredUpdates as well.
			// FIXME: This doesn't actually capture MW's post-send work because
			// this callback is registered before MW is initialised, and the list
			// is FIFO. It still captures all the main work during the request
			// and pre-send work. On HHVM, we used a nested register_postsend_function().
			register_shutdown_function( $saveCallback );
		}
	}

	/**
	 * Set up Excimer for production
	 *
	 * @param array $options
	 */
	private static function excimerSetup( $options ) {
		// Use static variables to keep the objects in scope until the end
		// of the request
		static $cpuProf;
		static $realProf;

		$cpuProf = new ExcimerProfiler;
		$cpuProf->setEventType( EXCIMER_CPU );

		$realProf = new ExcimerProfiler;
		$realProf->setEventType( EXCIMER_REAL );

		$cpuProf->setPeriod( 60 );
		$realProf->setPeriod( 60 );

		// Limit the depth of stack traces to 250 (T176916)
		$cpuProf->setMaxDepth( 250 );
		$realProf->setMaxDepth( 250 );

		// The excimer-k8s definitions are temporary, to assist with migration
		// (T288165).  We unfortunately have to duplicate the logic for
		// $wmgUsingKubernetes from CommonSettings.php, since this file is loaded
		// before that one.
		// grep: excimer-k8s, excimer-wall, excimer-k8s-wall
		$redisChannel = 'excimer';
		if ( strpos( ( $_SERVER['SERVERGROUP'] ?? null ), 'kube-' ) === 0 ) {
			$redisChannel .= '-k8s';
		}

		$cpuProf->setFlushCallback(
			static function ( $log ) use ( $options, $redisChannel ) {
				self::excimerFlushCallback( $log, $options, $redisChannel );
			},
			/* $maxSamples = */ 1 );
		$realProf->setFlushCallback(
			static function ( $log ) use ( $options, $redisChannel ) {
				self::excimerFlushCallback( $log, $options, $redisChannel . '-wall' );
			},
			/* $maxSamples = */ 1 );

		$cpuProf->start();
		$realProf->start();
	}

	/**
	 * The callback for production profiling. This is called every time Excimer
	 * collects a stack trace. The period is 60s, so there's no point waiting for
	 * more samples to arrive before the end of the request, they probably won't.
	 *
	 * @param string $log
	 * @param array $options
	 * @param string $redisChannel
	 */
	public static function excimerFlushCallback( $log, $options, $redisChannel ) {
		$error = null;
		$toobig = 0;
		try {
			$redis = new Redis();
			$ok = $redis->connect( $options['redis-host'], $options['redis-port'], $options['redis-timeout'] );
			if ( !$ok ) {
				$error = 'connect_error';
			} else {
				// Arc Lamp expects the first frame to be a PHP file.
				// This is used to group related traces for the same web entry point.
				// In most cases, this happens by default already. But at least for destructor
				// callbacks, this isn't the case on PHP 7.2. E.g. a line may be:
				// "LBFactory::__destruct;LBFactory::LBFactory::shutdown;… 1".
				$firstFrame = realpath( $_SERVER['SCRIPT_FILENAME'] ) . ';';
				$collapsed = $log->formatCollapsed();
				foreach ( explode( "\n", $collapsed ) as $line ) {
					if ( ( substr_count( $line, ';' ) + 1 ) >= 249 ) {
						// Stacks are separated by semi-colon, so +1 to get the frame count.
						// Anything size 249 or more, may be cut off (per setMaxDepth).
						// We discard those because depth limitation results in the early frames
						// (starting with the entry point) being omitted, which are the ones we need
						// for a flame graph (T176916)
						$toobig++;
						continue;
					}
					if ( $line === '' ) {
						// $collapsed ends with a line break
						continue;
					}

					// If the expected first frame isn't the entry point, prepend it.
					// This check includes the semicolon to avoid false positives.
					if ( substr( $line, 0, strlen( $firstFrame ) ) !== $firstFrame ) {
						$line = $firstFrame . $line;
					}
					$redis->publish( $redisChannel, $line );
				}
			}
		} catch ( \Exception $e ) {
			// Known failure scenarios:
			//
			// - "RedisException: read error on connection"
			//   Each publish() in the above loop writes data to Redis and
			//   subsequently reads from the socket for Redis' response.
			//   If any socket read takes longer than $timeout, it throws (T206092).
			//   As of writing, this is rare (a few times per day at most),
			//   which is considered an acceptable loss in profile samples.
			$error = 'exception';
		}

		if ( $error || $toobig ) {
			$dest = $options['statsd'] ?? null;
			if ( $dest ) {
				$sock = socket_create( AF_INET, SOCK_DGRAM, SOL_UDP );
				if ( $error ) {
					$stat = "MediaWiki.arclamp_client_error.{$error}:1|c";
					@socket_sendto( $sock, $stat, strlen( $stat ), 0, $dest, 8125 );
				}
				if ( $toobig ) {
					$stat = "MediaWiki.arclamp_client_discarded.toobig:{$toobig}|c";
					@socket_sendto( $sock, $stat, strlen( $stat ), 0, $dest, 8125 );
				}
			}
		}
	}
}
