<?php
/**
 * PHPUnit testcase for $wgConf testing.
 *
 * @license GPLv2 or later
 * @author Erik Bernhardson <ebernhardson@wikimedia.org>
 * @copyright Copyright © 2015, Erik Bernhardson <ebernhardson@wikimedia.org>
 * @file
 */

require_once __DIR__ . '/SiteConfiguration.php';

class WgConfTestCase extends PHPUnit_Framework_TestCase {

	protected $globals = array();

	protected function restoreGlobals() {
		foreach ( $this->globals as $key => $value ) {
			$GLOBALS[$key] = $value;
		}
		$this->globals = array();
	}

	protected function setGlobals( $pairs, $value = null ) {
		if ( is_string( $pairs ) ) {
			$pairs = array( $pairs => $value );
		}
		foreach ( $pairs as $key => $value ) {
			// only set value in $this->globals on first call
			if ( !array_key_exists( $key, $this->globals ) ) {
				if ( isset( $GLOBALS[$key] ) ) {
					// break any object references
					try {
						$this->globals[$key] = unserialize( serialize( $GLOBALS[$key] ) );
					} catch ( \Exception $e ) {
						$this->globals[$key] = $GLOBALS[$key];
					}
				} else {
					$this->globals[$key] = null;
				}
			}
			$GLOBALS[$key] = $value;
		}
	}

	/**
	 * Make sure global scope is left in a sane state.
	 *
	 * data providers have to mess up with the global scope in order to load
	 * $wgConf.  If one forget to restore the globals from the data provider,
	 * the global scope is saved/restored for each tests.  That slows down the
	 * test run dramatically.
	 *
	 * We have to ensure global scope has been restored BEFORE the test run,
	 * the only way to achieve that is when leaving the data provider scope.
	 */
	function __destruct() {
		if ( ! empty( $this->globals ) ) {
			throw new Exception( __CLASS__ . ': setGlobals() used without restoreGlobals()' );
		}
	}

	/**
	 * Load $wgConf from InitialiseSettings.php
	 *
	 * Example usage:
	 *
	 *     $wgLogoHD = $this->loadWgConf( 'production' )->settings['wgLogoHD'];
	 *
	 * @param string $wmfRealm Realm to use for example: 'labs' or 'production'
	 * @return SiteConfiguration
	 */
	final protected function loadWgConf( $wmfRealm ) {
		// Variables required for wgConf.php
		$wmfConfigDir = __DIR__ . "/../wmf-config";

		require "{$wmfConfigDir}/wgConf.php";

		// InitialiseSettings.php explicitly declares these as global, so we must too
		$this->setGlobals( array(
			'wmfUdp2logDest' => 'localhost',
			'wmfDatacenter' => 'unittest',
			'wmfMasterDatacenter' => 'unittest',
			'wmfRealm' => $wmfRealm,
			'wmfConfigDir' => $wmfConfigDir,
			'wgConf' => $wgConf,
		) );

		require __DIR__ . '/TestServices.php';
		require "{$wmfConfigDir}/InitialiseSettings.php";

		$ret = $wgConf;
		// Make sure globals are restored, else they will be serialized on each
		// test run which slow the test run dramatically.
		$this->restoreGlobals();
		return $ret;
	}

}
