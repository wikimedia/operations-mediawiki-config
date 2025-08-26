<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.
#
# Included by PhpAutoPrepend.php BEFORE any other wmf-config or mediawiki file.
# MUST NOT use any predefined state, only plain PHP.
#
# Exposes:
# - $wmgProfiler (used by CommonSettings.php)

namespace Wikimedia\MWConfig;

use Exception;
use ExcimerProfiler;
use MediaWiki\Profiler\ProfilingContext;
use PDO;
use Redis;
use ReflectionClass;
use ReflectionException;
use Wikimedia\ExcimerUI\Client\ExcimerClient;

require_once __DIR__ . '/XWikimediaDebug.php';
require_once __DIR__ . '/ClusterConfig.php';
require_once __DIR__ . '/../lib/excimer-ui-client/src/ExcimerClient.php';

class Profiler {
	/**
	 * Start any profilers if enabled for this process.
	 *
	 * @param array $options Associative array of options:
	 *   - redis-host: The host used for Xenon events
	 *   - redis-port: The port used for Xenon events
	 *   - redis-timeout: The redis socket timeout
	 *   - statsd-host (string|null): StatsD host address (ip:port or hostname:port).
	 *   - excimer-ui-url (string|null): The url for Wikimedia\ExcimerUI\Client\ExcimerClient
	 *   - excimer-ui-server (string|null): The ingestionUrl for Wikimedia\ExcimerUI\Client\ExcimerClient
	 *   - xhgui-conf: [optional] The configuration array to pass to XhguiSaverPdo
	 *     - pdo.connect: connection string for PDO (e.g. `mysql:host=mydbhost;dbname=xhgui`)
	 *     - pdo.table: table name within the xhgui database where the profiles are stored.
	 */
	public static function setup( array $options ): void {
		global $wmgProfiler;

		$wmgProfiler = [];

		if ( extension_loaded( 'xhprof' ) ) {
			// Used for XHGui or inline profile.
			// No-op unless enabled via WikimediaDebug (web) or --profile (CLI).
			self::xhprofSetup( $options, 'xhprof' );
		}

		if ( PHP_SAPI !== 'cli' && extension_loaded( 'excimer' ) ) {
			// Used for unconditional sampling of production web requests.
			self::excimerSetup( $options );

			// Used for WikimediaDebug flamegraphs
			self::excimerDebugSetup( $options );
		}
	}

	/**
	 * Set up XHProf.
	 *
	 * @param array $options
	 */
	private static function xhprofSetup( array $options ) {
		global $wmgProfiler;
		$xwd = XWikimediaDebug::getInstance();
		$profileToStdout = $xwd->hasOption( 'forceprofile' );
		$profileToXhgui = $xwd->hasOption( 'profile' ) && !empty( $options['xhgui-conf'] );

		$xhprofFlags = ( 0
			// Add 'cpu' keys to profile entries.
			| XHPROF_FLAGS_CPU
			// Add 'mu' and 'pmu' keys to profile entries.
			| XHPROF_FLAGS_MEMORY
			// Make output more concise (doesn't modify output format)
			| XHPROF_FLAGS_NO_BUILTINS
		);

		// For web requests with XWD "profile" or "forceprofile", start the profiler now.
		//
		// This ensures a balanced and more complete call graph (T180183). This is important for
		// web requests because we want to measure even the pre-MediaWiki setup (such as multiversion
		// and wmf-config) which is significant during low-latency requests.
		if ( $profileToStdout || $profileToXhgui ) {
			xhprof_enable( $xhprofFlags );

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

				$profile = xhprof_disable();
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
	 * Start Excimer sampling profiler in production.
	 *
	 * @param array $options
	 */
	private static function excimerSetup( $options ) {
		// Keep the object in scope until the end of the request
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

		$redisChannel = 'excimer';

		// The period is 60s, so there's no point waiting for more samples to arrive
		// before the end of the request, they probably won't.
		$cpuProf->setFlushCallback(
			static function ( $log ) use ( $options, $redisChannel ) {
				$logLines = explode( "\n", $log->formatCollapsed() );
				self::excimerFlushToArclamp( $logLines, $options, $redisChannel );
			},
			/* $maxSamples = */ 1 );
		$realProf->setFlushCallback(
			static function ( $log ) use ( $options, $redisChannel ) {
				$logLines = explode( "\n", $log->formatCollapsed() );
				self::excimerFlushToArclamp( $logLines, $options, $redisChannel . '-wall' );
				register_shutdown_function(
					[ self::class, 'excimerFlushToStatsd' ],
					$logLines,
					$options
				);
			},
			/* $maxSamples = */ 1 );

		$cpuProf->start();
		$realProf->start();
	}

	/**
	 * Set up Excimer for debug profiles
	 *
	 * @param array $options
	 * - excimer-ui-client (array|null) See Wikimedia\ExcimerUI\Client\ExcimerClient::setup
	 */
	private static function excimerDebugSetup( $options ) {
		$xwd = XWikimediaDebug::getInstance();
		if ( $xwd->hasOption( 'excimer' ) && $options['excimer-ui-server'] ) {
			$client = ExcimerClient::setup( [
				'url' => $options['excimer-ui-url'],
				'ingestionUrl' => $options['excimer-ui-server'],
				'activate' => 'always',
				'errorCallback' => static function ( $msg ) {
					trigger_error( $msg, E_USER_WARNING );
				}
			] );

			// Emitting a header this early is not universally safe, but should be fine
			// for debugging requests. Ideally we'd use the PHP built-in
			// header_register_callback(), except MediaWiki uses that already (discards ours),
			// or leverage MediaWiki' DeferredUpdate to schedule a PRESEND callback, except
			// Profiler.php runs before that is available.
			// We could use $wgHooks['SetupAfterCache'] in CommonSettings.php and call DeferredUpdate
			// from there, if this causes a problem.
			$publicLink = $client->getUrl();
			header( 'excimer-ui-link: ' . $publicLink );
		}
	}

	/**
	 * Flush callback, called any time Excimer samples a stack trace in production.
	 *
	 * @param string[] $logLines Result of ExcimerLog::formatCollapsed()
	 * @param array $options
	 * @param string $redisChannel
	 */
	public static function excimerFlushToArclamp( $logLines, $options, $redisChannel ) {
		$error = null;
		try {
			$redis = new Redis();
			$ok = $redis->connect(
				$options['redis-host'],
				$options['redis-port'],
				$options['redis-timeout']
			);
			if ( !$ok ) {
				$error = 'connect_error';
			} else {
				$firstFrame = realpath( $_SERVER['SCRIPT_FILENAME'] ) . ';';
				foreach ( $logLines as $line ) {
					if ( $line === '' ) {
						// formatCollapsed() ends with a line break
						continue;
					}

					// There are two ways a stack trace may be missing the first few frames:
					//
					// 1. Destructor callbacks, as of PHP 7.2, may be formatted as
					//    "LBFactory::__destruct;LBFactory::LBFactory::shutdown;… 1"
					// 2. Stack traces that are longer than the configured maxDepth, will be
					//    missing their top-most frames in favour of excimer_truncated (T176916)
					//
					// Arc Lamp requires the top frame to be the PHP entry point file.
					// If the first frame isn't the expected entry point, prepend it.
					// This check includes the semicolon to avoid false positives.
					if ( substr( $line, 0, strlen( $firstFrame ) ) !== $firstFrame ) {
						$line = $firstFrame . $line;
					}
					$redis->publish( $redisChannel, $line );
				}
			}
		} catch ( Exception $e ) {
			// Known failure scenarios:
			//
			// - "RedisException: Connection timed out" (T206092, T348756)
			//   Each publish() in the above loop writes data to Redis and
			//   subsequently reads from the socket for Redis' response.
			//   When a socket read takes longer than $timeout, php-redis throws.
			//   We catch these to avoid impacting the web response.
			//   As of Feb 2024, these is rare (a few per day) which is an acceptable
			//   loss for the milions of daily samples for flame graphs.
			$error = 'exception';
			if ( $e->getMessage() !== 'Connection timed out' ) {
				trigger_error( get_class( $e ) . ': ' . $e->getMessage(), E_USER_WARNING );
			}
		}

		if ( $error ) {
			// statsd format (graphite)
			self::sendMetric( "MediaWiki.arclamp_client_error.{$error}:1|c", $options['statsd-host'], 8125 );

			// dogstatsd format (prometheus)
			$normalizedError = self::normalizeTag( $error );
			self::sendMetric(
				"MediaWiki_arclamp_client_errors_total:1|c|#error:{$normalizedError}",
				$_SERVER['STATSD_EXPORTER_PROMETHEUS_SERVICE_HOST'] ?? 'localhost',
				9125
			);
		}
	}

	/**
	 * Production callback for recording profiling data in statsd
	 *
	 * This is called every time Excimer collects a stack trace
	 *
	 * @param string[] $logLines Result of ExcimerLog::formatCollapsed()
	 * @param array $options
	 */
	public static function excimerFlushToStatsd( $logLines, $options ) {
		$verb = $_SERVER['REQUEST_METHOD'] ?? '';
		$handler = class_exists( ProfilingContext::class )
			? ProfilingContext::singleton()->getHandlerMetricPrefix()
			: 'unknown';

		if ( $verb !== '' && $handler !== 'unknown' ) {
			foreach ( $logLines as $line ) {
				if ( $line === '' ) {
					// $collapsed ends with a line break
					continue;
				}
				$componentsInStack = [];
				foreach ( explode( ';', $line ) as $fname ) {
					$cname = self::excimerComponentFromMethod( $fname );
					if ( $cname !== null ) {
						$componentsInStack[$cname] = 1;
					}
				}

				// statsd format (graphite)
				self::sendMetric(
					"MediaWiki.arclamp_samples.$handler.$verb:1|c",
					$options['statsd-host'],
					8125
				);

				// dogstatsd format (prometheus)
				$normalizedHandler = self::normalizeTag( $handler );
				$normalizedVerb = self::normalizeTag( $verb );
				self::sendMetric( "mediawiki_arclamp_samples_total:1|c|"
					. "#handler:{$normalizedHandler}"
					. ",verb:{$normalizedVerb}",
					$_SERVER['STATSD_EXPORTER_PROMETHEUS_SERVICE_HOST'] ?? 'localhost',
					9125
				);

				foreach ( $componentsInStack as $cname => $hit ) {
					// statsd format (graphite)
					self::sendMetric(
						"MediaWiki.arclamp_samples_components.$handler.$verb.$cname:1|c",
						$options['statsd-host'],
						8125
					);
					// dogstatsd format (prometheus)
					$normalizedComponent = self::normalizeTag( $cname );
					self::sendMetric( "mediawiki_arclamp_samples_by_component_total:1|c|"
						. "#handler:{$normalizedHandler}"
						. ",verb:{$normalizedVerb}"
						. ",component:{$normalizedComponent}",
						$_SERVER['STATSD_EXPORTER_PROMETHEUS_SERVICE_HOST'] ?? 'localhost',
						9125
					);
				}
			}
		}
	}

	/**
	 * Get the component name from a class suitable for metrics
	 *
	 * @param string $fname Fully qualified caller name (e.g. from __METHOD__)
	 * @return string|null Metric name component (e.g. "core", "MySkin", "MyExtension")
	 */
	private static function excimerComponentFromMethod( $fname ) {
		$m = [];
		if ( !preg_match( '/^([a-zA-Z0-9_\\\\]+)::[a-zA-Z0-9_]+$/', $fname, $m ) ) {
			return null;
		}

		// Determine the class file path
		try {
			$path = ( new ReflectionClass( $m[1] ) )->getFileName();
			$component = self::excimerComponentFromPath( $path );
		} catch ( ReflectionException $e ) {
			$component = 'unknown';
		}

		return $component;
	}

	/**
	 * Get the component name from a class suitable for metrics
	 *
	 * @param string|false $path Fully qualified path name
	 * @return string|null Metric name component (e.g. "core", "MySkin", "MyExtension")
	 */
	private static function excimerComponentFromPath( $path ) {
		global $IP, $wgStyleDirectory, $wgExtensionDirectory;

		if ( $path === false ) {
			// Part of PHP core or PECL extension
			return 'other';
		}

		// Try to determine the component from the class file path.
		// Note that skins/extensions might use directories nested within $IP.
		foreach ( [
			  "$wgExtensionDirectory/" => 'ext_',
			  "$wgStyleDirectory/" => 'skin_',
			  "$IP/includes/libs/" => 'lib_',
			  "$IP/includes/" => 'core_',
			  "$IP/vendor/wikimedia/" => 'lib_',
		  ] as $baseDirectoryWithSlash => $basePrefix ) {
			$offset = strlen( $baseDirectoryWithSlash );
			// @todo: use str_starts_with() with PHP 8.0
			if ( substr( $path, 0, $offset ) === $baseDirectoryWithSlash ) {
				$pos = strpos( $path, '/', $offset );
				$name = ( $pos !== false )
					// Treat the relative subdirectory as the component. This ideally matches
					// the relative sub-namespace of Wikimedia\ and MediaWiki\ namespaced files.
					? substr( $path, $offset, $pos - $offset )
					// Placeholder to use for files that should be moved to a subdirectory
					: 'other';

				return $basePrefix . str_replace( '.', '_', $name );
			}
		}

		if ( substr( $path, 0, strlen( "$IP/vendor/" ) ) === "$IP/vendor/" ) {
			return 'lib_other';
		}

		return 'other';
	}

	/**
	 * Sends a metric to the stats host
	 *
	 * @param string $metric
	 * @param string|null $host
	 * @param int $port
	 * @return void
	 */
	private static function sendMetric( $metric, $host, $port ): void {
		if ( $host ) {
			$sock = socket_create( AF_INET, SOCK_DGRAM, SOL_UDP );
			$metric = trim( $metric ) . "\n";
			@socket_sendto( $sock, $metric, strlen( $metric ), 0, $host, $port );
			@socket_close( $sock );
		}
	}

	/**
	 * Normalizes tags to only alphanumerics and underscores.
	 * Strips duplicated and leading/trailing underscores.
	 *
	 * Note: We are not using /i (case-insensitive flag)
	 * or \d (digit character class escape) here because
	 * their behavior changes with respect to locale settings.
	 *
	 * @param string $tag
	 * @return string
	 */
	private static function normalizeTag( $tag ) {
		$tag = preg_replace( '/[^a-zA-Z0-9]+/', '_', $tag );
		return trim( $tag, '_' );
	}
}
