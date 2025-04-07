<?php

namespace Wikimedia\MWConfig;

require_once __DIR__ . '/DNSSRVRecord.php';

// container for the cache of DB records
class DBRecordCache {
	/** @var DBRecordCache|null */
	private static $instance;
	/** @var array */
	private $cache;
	/** @var string */
	public $srvRecordFmt = "_%s-analytics._tcp.eqiad.wmnet";

	/** @var string */
	private $defaultSectionName = 's3';

	/**
	 * Get the single instance of the cache
	 * @return DBRecordCache
	 */
	public static function getInstance() {
		if ( !self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	private function __construct() {
		$this->reset();
	}

	/**
	 * Reset the cache
	 */
	public function reset() {
		$this->cache = [];
	}

	public function setDefaultSectionName( $section ) {
		$this->defaultSectionName = $section;
	}

	/**
	 * Repopulate the DB configuration from the cache
	 * @param array &$lbFactoryConf
	 */
	public function repopulateDbConf( &$lbFactoryConf ) {
		foreach ( array_keys( $lbFactoryConf['sectionLoads'] ) as $section ) {
			// If the section is the default section, we need to use the default section name
			// when fetching the records from the cache, but we need to retain the label
			$sectionLabel = $section;
			if ( $section === 'DEFAULT' ) {
				$section = $this->defaultSectionName;
			}
			if ( !$this->needsUpdate( $section ) ) {
				// nothing changed, move to the next section
				continue;
			}
			// First let's add the new entries to the hostsByName list
			// Please note: we're not removing stale values, but the rate of updates
			// should be low to nonexistent, so we can afford to do this on every update.
			$dbs = $this->get( $section );
			foreach ( $dbs as $dbrecord ) {
				$lbFactoryConf['hostsByName'][$dbrecord->getInstanceLabel()] = $dbrecord->getIpPort();
			}

			// Create an array of load values for this section
			$serverLoads = [];
			foreach ( $dbs as $dbrecord ) {
				$serverLoads[$dbrecord->getInstanceLabel()] = $dbrecord->getWeight();
			}

			// Now, let's swap all the non-master servers in the sectionLoads array
			$master = array_key_first( $lbFactoryConf['sectionLoads'][$sectionLabel] );
			$lbFactoryConf['sectionLoads'][$sectionLabel] = [ $master => 0 ] + $serverLoads;

			// Finally let's repopulate the groupLoadsBySection array with dbstores only
			foreach ( $lbFactoryConf['groupLoadsBySection'][$sectionLabel] as $label => $load ) {
				$lbFactoryConf['groupLoadsBySection'][$sectionLabel][$label] = $serverLoads;
			}
		}
	}

	/**
	 * Get the DB record for a given section
	 * @param string $section
	 * @return \Wikimedia\MWConfig\DNSSRVRecord[]
	 */
	public function get( $section ) {
		$this->update( $section );
		return $this->cache[$section];
	}

	/**
	 * Check if the cache needs updating
	 * @param string $section
	 * @return bool
	 */
	public function needsUpdate( $section ) {
		// Return false if the records don't need updating
		if ( isset( $this->cache[$section] ) ) {
			$expired = false;
			// The TTL should be the same for every response in a single SRV query,
			// but let's check all in case it's not.
			foreach ( $this->cache[$section] as $record ) {
				if ( $record->isExpired() ) {
					$expired = true;
					break;
				}
			}
			if ( !$expired ) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Update the cache if needed and return true on change
	 *
	 * @param string $section
	 * @return bool
	 */
	public function update( $section ) {
		if ( !$this->needsUpdate( $section ) ) {
			return false;
		}
		try {
			$this->cache[$section] = $this->fetch( $section );
			return true;
		} catch ( Exception $e ) {
			// If we failed to fetch the record, return the cached one if it exists
			if ( isset( $this->cache[$section] ) ) {
				return false;
			}
			throw $e;
		}
	}

	/**
	 * Fetch the DB record for a given section
	 * @param string $section
	 * @return array
	 */
	protected function fetch( $section ) {
		$result = [];
		$dns_srv_record = sprintf( $this->srvRecordFmt, $section );
		$response = $this->resolveSRV( $dns_srv_record );
		if ( $response === false ) {
			throw new Exception( "Failed to get SRV record for $dns_srv_record" );
		}
		return DNSSRVRecord::fromResponse( $response );
	}

	/**
	 * Resolve a DNS SRV record
	 *
	 * Why this one-line method? Because it's easier to mock in tests.
	 * Sigh.
	 * @param string $query
	 * @return array
	 */
	protected function resolveSRV( $query ) {
		return dns_get_record( $query, DNS_SRV );
	}
}
