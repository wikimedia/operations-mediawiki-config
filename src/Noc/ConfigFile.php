<?php

namespace Wikimedia\MWConfig\Noc;

require_once __DIR__ . '/utils.php';

class ConfigFile {
	/** @var array */
	private $routes;

	private const CACHE_KEY = 'org.wikimedia.noc::conf::routes';
	public const ROOT = __DIR__ . '/../../';

	/**
	 * Initialize the class.
	 *
	 * If the routes are not in cache, load them from the parameters.
	 * Each of the parameters is, alternatively:
	 * - A relative path in the repo
	 * - Only for the $plain parameter, optionally an array of [route => path]
	 *   entries where route is the url relative to /conf/,
	 *   and path is the relative path in the repository to the file.
	 * Once the files are loaded, save them in cache.
	 *
	 * @param array $txt The list of files to serve with a ".txt" extension
	 * @param array $plain The list of files to serve with the provided path
	 * @param array $dir The list of directories to serve the files for
	 *
	 */
	public function __construct( $txt, $plain, $dir ) {
		$this->routes = [];
		$cacheAvailable = hasApcu();
		// try to load routes from cache.
		$cached = $cacheAvailable ? apcu_fetch( self::CACHE_KEY ) : false;
		// The files on disk, or indeed in opcache,
		// are immutable on kubernetes.
		// They aren't on premises at the moment on the
		// maintenance server, so we will need to remove the TTL once we've migrated.
		if ( $cached ) {
			$this->routes = $cached;
		} else {
			$this->loadPaths( $txt, 'txt' );
			$this->loadPaths( $plain );
			$this->loadDir( $dir );
			if ( $cacheAvailable ) {
				$this->cache();
			}
		}
	}

	/**
	 * Get the disk path for a uri, or false in case of failure.
	 *
	 * @param string $uriPath the full uri path
	 * @return string|false
	 */
	public function getDiskPathByUrl( $uriPath ) {
		return $this->routes[$uriPath]["path"] ?? false;
	}

	/**
	 * Get the disk path for a label, or false in case of failure.
	 *
	 * @param string $label
	 * @return string|false
	 */
	public function getDiskPathByLabel( $label ) {
		foreach ( $this->routes as $r => $info ) {
			if ( $info['label'] == $label ) {
				return $info['path'];
			}
		}
		return false;
	}

	/**
	 * Get the route for a label, or false in case of failure.
	 *
	 * @param string $label
	 * @return string|false
	 */
	public function getRouteFromLabel( $label ) {
		foreach ( $this->routes as $r => $info ) {
			if ( $info['label'] == $label ) {
				return $r;
			}
		}
		return false;
	}

	/**
	 * Get the path for a file, relative to the git root.
	 *
	 * @param string $path
	 * @return string|false
	 */
	public function getRepoPath( $path ) {
		return str_replace( realpath( self::ROOT ) . '/', '', $path );
	}

	/**
	 * Find all non-dblist files, return them as label => route pairs.
	 */
	public function getConfigRoutes(): array {
		$output = [];
		foreach ( $this->routes as $route => $info ) {
			$label = $info['label'];
			if ( !$this->isDbList( $label ) ) {
				$output[$label] = $route;
			}
		}
		return $output;
	}

	/**
	 * Find all dblist files, return them as label => route pairs.
	 */
	public function getDblistRoutes(): array {
		$output = [];
		foreach ( $this->routes as $route => $info ) {
			$label = $info['label'];
			if ( $this->isDbList( $label ) ) {
				$output[$label] = $route;
			}
		}
		return $output;
	}

	/**
	 * Load paths from disk.
	 *
	 * @param array $paths array of associative arrays/strings. See the documentation for __construct()
	 * @param string $type type of file.
	 */
	private function loadPaths( $paths, $type = 'plain' ): void {
		foreach ( $paths as $path ) {
			// For convenience, convert 1:1 mappings to arrays
			if ( !is_array( $path ) ) {
				$path = [ $path => $path ];
			}
			foreach ( $path as $p => $r ) {
				switch ( $type ) {
					case 'plain':
						// dblists/s1.list => [
						//	'conf/dblists/s1.list' =>
						//		['path' => $REPO/dblists/s1.list', 'label' => 'dblists/s1.list']
						// ]
						$route = '/conf/' . $r;
						$this->routes[$route] = [ 'path' => realpath( self::ROOT . $p ), 'label' => $p ];
						break;
					case 'txt':
						// wmf-config/CommonSettings.php => [
						//	'conf/CommonSettings.php.txt' =>
						//		['path' => $REPO/wmf-config/CommonSettings.php', 'label' => 'CommonSettings.php']
						// ]
						$label = basename( $r );
						$route = '/conf/' . $label . '.txt';
						$this->routes[$route] = [ 'path' => realpath( self::ROOT . $p ), 'label' => $label ];
						break;
				}
			}
		}
	}

	/**
	 * Load paths for all files in a directory.
	 *
	 * @param string[] $dirs a list of directories (relative to the repo root) to load.
	 */
	private function loadDir( $dirs ): void {
		$realRoot = realpath( self::ROOT );
		foreach ( $dirs as $dir ) {
			$realDir = realpath( self::ROOT . $dir );
			// we want all the files in the format $dir/file, so strip the root dir from the glob
			$paths = array_map( fn( $path ) => str_replace( $realRoot . '/', '', $path ), glob( $realDir . '/*' ) );
			$this->loadPaths( $paths );
		}
	}

	/**
	 * Save the routes table in apcu with a ttl depending on the opcache settings.
	 */
	private function cache(): bool {
		// check if opcache is set to not revalidate. In that case, cache forever.
		// Otherwise, cache for 1 minute. We really don't need a higher resolution here.
		$conf = opcache_get_configuration()["directives"] ?? [];
		if ( $conf["opcache.enable"] ?? false && !( $conf["opcache.validate_timestamps"] ?? true ) ) {
			$ttl = 0;
		} else {
			$ttl = 60;
		}
		return apcu_store( self::CACHE_KEY, $this->routes, $ttl );
	}

	/**
	 * Check if a path is a dblist.
	 *
	 * @param string $path any path
	 */
	private function isDbList( $path ): bool {
		return ( strpos( $path, 'dblists/' ) !== false );
	}
}
