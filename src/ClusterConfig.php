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
	// At the cost of being inelegant, I think it's simpler to have these lists here.
	private const TRAITS = [
		'async' => [ 'jobrunner', '-async' ],
		'canary' => [ '-canary' ],
		'k8s'   => [ 'kube-' ],
		'parsoid' => [ 'parsoid' ],
		'test' => [ '-test', 'debug' ],
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
	 * Is this cluster group a test environment?
	 *
	 * @return bool
	 */
	public function isTest() {
		return $this->hasTrait( 'test' );
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
