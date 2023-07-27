<?php

class TestableCachedConfig extends Wikimedia\MWConfig\Noc\EtcdCachedConfig {
	/** @var string[] */
	private $responses = [];

	/** @var string[] */
	public $calls = [];

	/** @var string[] */
	public $dns_names = [];

	/**
	 * Get one instance of the etcdcachedconfig
	 * @return TestableCachedConfig|null
	 */
	public static function getInstance() {
		if ( !self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * overload the download function
	 * @param string $uri
	 * @return string[]
	 */
	protected function download( $uri ) {
		$this->calls[] = $uri;
		return array_pop( $this->responses );
	}

	/**
	 * Set the download results
	 * @param string[] $results
	 */
	public function setDownloadResults( $results ) {
		$this->responses = $results;
	}

	/**
	 * @param string $name
	 * @return array|false
	 */
	protected function resolveSrv( $name ) {
		// Allow simulating an error
		if ( strpos( $name, 'exception' ) !== false ) {
			return false;
		} else {
			return [ [ 'target' => 'test.local', 'port' => '2379' ] ];
		}
	}

	public function resetCalls(): void {
		$this->calls = [];
	}
}
