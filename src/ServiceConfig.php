<?php

namespace Wikimedia\MWConfig;

/**
 * Caching wrapper for the remote service host/port lists, and the realm and DC
 * configuration.
 */
class ServiceConfig {
	private static $instance;
	private $realm;
	private $datacenter;
	private $services;

	/**
	 * @return ServiceConfig
	 */
	public static function getInstance() {
		if ( !self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	private function __construct() {
		// Use include instead of require to reduce the risk of a recursive fatal
		// error in hhvm-fatal-error.php
		$env = include __DIR__ . '/../wmf-config/env.php';
		$this->realm = $env['realm'];
		$this->datacenter = $env['dc'];
		if ( $this->realm === 'labs' ) {
			$this->services = include __DIR__ . '/../wmf-config/LabsServices.php';
		} else {
			$this->services = include __DIR__ . '/../wmf-config/ProductionServices.php';
		}
	}

	/**
	 * Get an array of service configurations for each known datacenter within
	 * the current realm
	 *
	 * @return array
	 */
	public function getAllServices() {
		return $this->services;
	}

	/**
	 * Get an array of service configurations for the current datacenter
	 *
	 * @return array
	 */
	public function getLocalServices() {
		return $this->services[$this->datacenter];
	}

	/**
	 * Get a single service configuration within the local datacenter
	 *
	 * @param string $serviceName
	 * @return mixed
	 */
	public function getLocalService( $serviceName ) {
		return $this->services[$this->datacenter][$serviceName];
	}

	/**
	 * Get the services for a given datacenter
	 *
	 * @param string $datacenter
	 * @return array
	 */
	public function getServices( $datacenter ) {
		return $this->services[$datacenter];
	}

	/**
	 * Get the current realm, may be "production" or "labs"
	 *
	 * @return string
	 */
	public function getRealm() {
		return $this->realm;
	}

	/**
	 * Get the current datacenter, may be "eqiad" or "codfw"
	 *
	 * @return string
	 */
	public function getDatacenter() {
		return $this->datacenter;
	}
}
