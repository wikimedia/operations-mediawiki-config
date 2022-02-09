<?php
/**
 * Base class for test cases that need to load $wgConf settings.
 *
 * @license GPL-2.0-or-later
 * @author Erik Bernhardson <ebernhardson@wikimedia.org>
 * @copyright Copyright © 2015, Erik Bernhardson <ebernhardson@wikimedia.org>
 * @file
 */

abstract class WgConfTestCase extends PHPUnit\Framework\TestCase {

	protected $globals = [];
	protected $globalsToUnset = [];

	protected function restoreGlobals() {
		foreach ( $this->globals as $key => $value ) {
			$GLOBALS[$key] = $value;
		}
		$this->globals = [];

		foreach ( $this->globalsToUnset as $key => $_ ) {
			unset( $GLOBALS[$key] );
		}
		$this->globalsToUnset = [];
	}

	/**
	 * @param array $pairs
	 */
	protected function setGlobals( array $pairs ) {
		foreach ( $pairs as $key => $value ) {
			// only store original value on first call within a test
			if (
				!array_key_exists( $key, $this->globals ) &&
				!array_key_exists( $key, $this->globalsToUnset )
			) {
				if ( !array_key_exists( $key, $GLOBALS ) ) {
					$this->globalsToUnset[$key] = true;
				} else {
					$this->globals[$key] = $GLOBALS[$key];
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
	public function __destruct() {
		if ( !empty( $this->globals ) || !empty( $this->globalsToUnset ) ) {
			throw new Exception(
				__CLASS__ . ": setGlobals() used without restoreGlobals().\n" .
				"Mangled globals:\n" . var_export( $this->globals, true ) .
				"Created globals:\n" . var_export( $this->globalsToUnset, true )
			);
		}
	}

	/**
	 * Load $wgConf from InitialiseSettings.php
	 *
	 * Example usage:
	 *
	 *     $wgLocaltimezone = $this->loadWgConf( 'production' )->settings['wgLocaltimezone'];
	 *
	 * @param string $wmgRealm Realm to use for example: 'labs' or 'production'
	 * @return Wikimedia\MWConfig\StaticSiteConfiguration
	 */
	final protected function loadWgConf( $wmgRealm ) {
		$wmfConfigDir = __DIR__ . "/../wmf-config";

		// Needed for InitialiseSettings.php
		$this->setGlobals( [
			'wmfUdp2logDest' => 'localhost',
			'wmfDatacenter' => 'unittest',
			'wmfMasterDatacenter' => 'unittest',
			'wmfConfigDir' => $wmfConfigDir,
		] );

		// Needed for TestServices.php
		$this->setGlobals( [
			'wmgHostnames' => null,
			'wmfAllServices' => null,
			'wmfLocalServices' => null,
			'wmfMasterServices' => null,
		] );
		require __DIR__ . '/data/TestServices.php';

		$wgConf = new Wikimedia\MWConfig\StaticSiteConfiguration;
		$wgConf->suffixes = MWMultiVersion::SUFFIXES;
		$wgConf->wikis = MWWikiversions::readDbListFile( $wmgRealm === 'labs' ? 'all-labs' : 'all' );
		$wgConf->settings = wmfGetVariantSettings();

		// Make sure globals are restored, else they will be serialized on each
		// test run which slow the test run dramatically.
		$this->restoreGlobals();
		return $wgConf;
	}

}
