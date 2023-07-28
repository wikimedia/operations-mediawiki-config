<?php
namespace Wikimedia\MWConfig\Noc;

require_once __DIR__ . '/utils.php';

class EtcdCachedConfig {
	/** Time to store data in APCU */
	private const CACHE_TTL = 120;

	/** APCU cache key format */
	private const CACHE_KEY_FMT = 'noc-wikimedia-org:dbconfig-etcd:%s';

	/** Structure of an SRV record */
	private const SRV_RECORD_FMT = '_etcd-client-ssl._tcp.%s.';

	/** @var EtcdCachedConfig|null */
	protected static $instance;

	/** @var string */
	private $domain;

	/** @var array */
	private $etcdServers;

	/** @var bool */
	private $apcuAvailable;

	/** @var array */
	private $localCache;

	protected function __construct() {
		$this->apcuAvailable = hasApcu();
		$env = require __DIR__ . '/../../wmf-config/env.php';
		$this->etcdServers = $this->getEtcdServers( sprintf( '%s.wmnet', $env['dc'] ) );
	}

	/**
	 * @return EtcdCachedConfig|null
	 */
	public static function getInstance() {
		self::$instance ??= new self();
		return self::$instance;
	}

	/**
	 * Fetch a key from etcd, return its value.
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function getValue( $key ) {
		// Cache hit?
		$cached = $this->getFromCache( $key );
		if ( $cached !== null ) {
			return $cached;
		}

		$data = null;
		$uriPath = sprintf( '/v2/keys/conftool/v1/mediawiki-config/%s', $key );
		foreach ( $this->etcdServers as $server ) {
			$data = $this->fetchFromEtcd( $uriPath, $server );
			if ( $data ) {
				break;
			}
		}

		if ( !$data || !array_key_exists( 'val', $data ) ) {
			return null;
		}
		$config = $data['val'];
		$this->setInCache( $key, $config );
		return $config;
	}

	/**
	 * Get the etcd servers from the domain.
	 *
	 * @param string $domain
	 * @return array
	 */
	private function getEtcdServers( $domain ) {
		$result = [];
		$srvRecord = sprintf( self::SRV_RECORD_FMT, $domain );
		$response = $this->resolveSrv( $srvRecord );
		if ( !$response ) {
			return $result;
		}

		foreach ( $response as $record ) {
			$result[] = [
				'host' => $record['target'],
				'port' => (int)$record['port'],
			];
		}
		return $result;
	}

	/**
	 * Fetch the data from etcd.
	 *
	 * @param string $uri
	 * @param string $server
	 * @return array|null
	 */
	private function fetchFromEtcd( $uri, $server ) {
		$etcdUri = sprintf( 'https://%s:%d%s', $server['host'], $server['port'], $uri );
		$response = $this->download( $etcdUri );
		if ( !$response ) {
			return null;
		}
		$data = json_decode( $response, true );
		// Please note: while "false" and 0 are valid json values we could decode,
		// it would still be not valid for our purposes
		if ( !$data ) {
			return null;
		}
		$configRecord = json_decode( $data['node']['value'] ?? '', true );
		if ( !$configRecord ) {
			return null;
		}
		return $configRecord;
	}

	/**
	 * Download from etcd. Can be overloaded in testing.
	 *
	 * @param string $uri
	 * @return mixed
	 */
	protected function download( $uri ) {
		$ch = curl_init( $uri );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		return curl_exec( $ch );
	}

	/**
	 * Resolve a SRV record. Can be overloaded in testing.
	 *
	 * @param string $srvRecord
	 * @return mixed
	 */
	protected function resolveSrv( $srvRecord ) {
		return dns_get_record( $srvRecord, DNS_SRV );
	}

	/**
	 * Get the content from cache, or return null
	 *
	 * @param string $key
	 * @return mixed|null
	 */
	private function getFromCache( $key ) {
		if ( !$this->apcuAvailable ) {
			$cached = $this->localCache[$key] ?? null;
			if ( $cached && ( $cached['timestamp'] >= time() ) ) {
				return $cached['value'];
			}
			return null;
		}
		$cache_key = sprintf( self::CACHE_KEY_FMT, $key );
		return apcu_fetch( $cache_key ) ?: null;
	}

	/**
	 * Store the value of the configuration in cache.
	 *
	 * @param string $key
	 * @param mixed $value
	 */
	private function setInCache( $key, $value ): void {
		if ( !$this->apcuAvailable ) {
			$this->localCache[$key] = [ 'value' => $value, 'timestamp' => time() + self::CACHE_TTL ];
			return;
		}
		$cache_key = sprintf( self::CACHE_KEY_FMT, $key );
		apcu_store( $cache_key, $value, self::CACHE_TTL );
	}

}
