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
	private $datacenters;
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
		$env = require __DIR__ . '/../wmf-config/env.php';
		$this->realm = $env['realm'];
		$this->datacenter = $env['dc'];
		$this->datacenters = $env['dcs'];
		// e.g. ProductionServices.php, LabsServices.php, DevServices.php
		$servicesFile = ucfirst( $this->realm ) . 'Services.php';
		$this->services = include __DIR__ . '/../wmf-config/' . $servicesFile;
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
	 * Get the current realm, may be "production", "labs", or "dev"
	 *
	 * @return string
	 */
	public function getRealm() {
		return $this->realm;
	}

	/**
	 * Get the current datacenter. For production, may be "eqiad" or "codfw"
	 *
	 * @return string
	 */
	public function getDatacenter() {
		return $this->datacenter;
	}

	/**
	 * Get the list of all datacenters for the current realm
	 *
	 * @return array
	 */
	public function getDatacenters() {
		return $this->datacenters;
	}
}
