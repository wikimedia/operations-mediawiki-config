<?php

namespace Wikimedia\MWConfig;

/**
 * A simple container of the response of a DNS SRV query.
 */
class DNSSRVRecord {
	/** @var string */
	private $hostname;
	/** @var int */
	private $port;
	/** @var int */
	private $priority;
	/** @var int */
	private $weight;
	/** @var int */
	private $expires;

	public function __construct( string $hostname, int $port, int $priority, int $weight, int $ttl ) {
		$this->hostname = $hostname;
		$this->port = $port;
		$this->priority = $priority;
		$this->weight = $weight;
		$this->expires = time() + $ttl;
	}

	/**
	 * Given a DNS response, return an array of DNSSRVRecord objects.
	 * @param array $dns_response
	 * @return DNSSRVRecord[]
	 */
	public static function fromResponse( array $dns_response ) {
		$records = [];
		foreach ( $dns_response as $record ) {
			$records[] = new DNSSRVRecord(
				$record['target'],
				$record['port'],
				$record['pri'],
				$record['weight'],
				$record['ttl']
			);
		}
		return $records;
	}

	/**
	 * Check if the record has expired
	 *
	 * @return bool
	 */
	public function isExpired() {
		return $this->expires < time();
	}

	/**
	 * Get the hostname the record points to
	 *
	 * @return string
	 */
	public function getHostname() {
		return $this->hostname;
	}

	/**
	 * Get the label for the db instance
	 *
	 * @return string
	 */
	public function getInstanceLabel() {
		return $this->hostname . ':' . $this->port;
	}

	/**
	 * Get the weight for the db instance
	 *
	 * @return int
	 */
	public function getWeight() {
		return $this->weight;
	}

	/**
	 * Get the IP:port for the db instance
	 *
	 * @return string
	 */
	public function getIpPort() {
		return $this->getIp() . ':' . $this->port;
	}

	/**
	 * Get the IP for the db instance.
	 *
	 * @throws \RuntimeException if the hostname cannot be resolved
	 * @return string
	 */
	protected function getIp() {
		$ipaddr = $this->getHostByName( $this->hostname );
		if ( $ipaddr === $this->hostname ) {
			throw new \RuntimeException( "Failed to resolve {$this->hostname}" );
		}
		return $ipaddr;
	}

	/**
	 * Get the IP for the db instance.
	 * This method is protected so it can be mocked in tests.
	 *
	 * @param string $hostname
	 * @return string
	 */
	protected function getHostByName( $hostname ) {
		return gethostbyname( $hostname );
	}
}
