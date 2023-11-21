<?php

namespace Wikimedia\MWConfig;

/**
 * Manager for the different classes of clusters
 * Wikimedia runs in production.
 */
class ClusterConfig {
	/** @var ClusterConfig|null */
	private static $instance;
	/** @var string */
	private $cluster;
	/** @var string */
	private $hostname;

	// At the cost of being inelegant, I think it's simpler to have these lists here.
	private const TRAITS = [
		'async' => [ 'jobrunner', '-async' ],
		'canary' => [ '-canary' ],
		'k8s'   => [ 'kube-' ],
		'parsoid' => [ 'parsoid' ],
		'debug' => [ 'debug' ],
		'api'  => [ 'api_', 'api-' ],
	];

	/**
	 * Get the single instance of the class. Ugh I hate singletons.
	 * @return ClusterConfig
	 */
	public static function getInstance() {
		if ( !self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * Reset the singleton, only useful in tests. Do not use in production.
	 */
	public static function reset() {
		self::$instance = null;
	}

	private function __construct() {
		$this->cluster = $_SERVER['SERVERGROUP'] ?? '';
		$this->hostname = '';
	}

	/**
	 * Get the cluster name
	 *
	 * @return string
	 */
	public function getCluster() {
		return $this->cluster;
	}

	/**
	 * Are we running on kubernetes?
	 *
	 * @return bool
	 */
	public function isK8s() {
		return $this->hasTrait( 'k8s' );
	}

	/**
	 * Is this async processing?
	 *
	 * @return bool
	 */
	public function isAsync() {
		return $this->hasTrait( 'async' );
	}

	/**
	 * Does this cluster run parsoid?
	 *
	 * @return bool
	 */
	public function isParsoid() {
		return $this->hasTrait( 'parsoid' );
	}

	/**
	 * Is this cluster group a debug environment?
	 *
	 * @return bool
	 */
	public function isDebug() {
		return $this->hasTrait( 'debug' ) || $this->isDebugHost();
	}

	/**
	 * Checks to see if the current host is a debug host.
	 *
	 * @return bool
	 */
	private function isDebugHost() {
		return strpos( $this->getHostname(), 'debug' ) !== false;
	}

	/**
	 * Is this cluster instance a canary?
	 *
	 * @return bool
	 */
	public function isCanary() {
		return $this->hasTrait( 'canary' );
	}

	/**
	 * Is this cluster group an api cluster?
	 *
	 * @return bool
	 */
	public function isApi() {
		return $this->hasTrait( 'api' );
	}

	/**
	 * Lazily fetch and return the hostname.
	 *
	 * It will be cached once requested the first time.
	 * We are re-implementing wfHostname here because this
	 * class can be loaded before wfHostname is available.
	 *
	 * @return string
	 */
	public function getHostname() {
		if ( $this->hostname === '' ) {
			$this->hostname = php_uname( 'n' ) ?: 'unknown';
		}
		return $this->hostname;
	}

	/**
	 * Checks if the current cluster has a specific trait.
	 *
	 * @param string $trait
	 * @return bool
	 */
	private function hasTrait( string $trait ) {
		$traits = self::TRAITS[$trait] ?? [];
		foreach ( $traits as $trait ) {
			if ( $this->match( $trait ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Matches a specific label to the current cluster.
	 *
	 * @param string $label
	 * @return bool
	 */
	private function match( string $label ) {
		return ( strpos( $this->cluster, $label ) !== false );
	}
}
