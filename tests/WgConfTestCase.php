<?php
/**
 * Base class for test cases that need to load $wgConf settings.
 *
 * @license GPL-2.0-or-later
 * @author Erik Bernhardson <ebernhardson@wikimedia.org>
 * @copyright Copyright © 2015, Erik Bernhardson <ebernhardson@wikimedia.org>
 * @file
 */

use Wikimedia\MWConfig\WmfConfig;

abstract class WgConfTestCase extends PHPUnit\Framework\TestCase {

	/** @var array mapping string name to original value */
	protected static array $globals = [];

	/** @var bool[] mapping string name of the setting to the value true */
	protected static array $globalsToUnset = [];

	protected static function restoreGlobals() {
		foreach ( self::$globals as $key => $value ) {
			$GLOBALS[$key] = $value;
		}
		self::$globals = [];

		foreach ( self::$globalsToUnset as $key => $_ ) {
			unset( $GLOBALS[$key] );
		}
		self::$globalsToUnset = [];
	}

	private static function setGlobals( array $pairs ) {
		foreach ( $pairs as $key => $value ) {
			// only store original value on first call within a test
			if (
				!array_key_exists( $key, self::$globals ) &&
				!array_key_exists( $key, self::$globalsToUnset )
			) {
				if ( !array_key_exists( $key, $GLOBALS ) ) {
					self::$globalsToUnset[$key] = true;
				} else {
					self::$globals[$key] = $GLOBALS[$key];
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
		if ( !empty( self::$globals ) || !empty( self::$globalsToUnset ) ) {
			throw new Exception(
				__CLASS__ . ": setGlobals() used without restoreGlobals().\n" .
				"Mangled globals:\n" . var_export( self::$globals, true ) .
				"Created globals:\n" . var_export( self::$globalsToUnset, true )
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
	 * @param string $realm Realm to use for example: 'labs' or 'production'
	 * @return SiteConfiguration
	 */
	final protected static function loadWgConf( string $realm = 'production' ): SiteConfiguration {
		// Needed for InitialiseSettings.php
		self::setGlobals( [
			'wmgUdp2logDest' => 'localhost',
			'wmgDatacenter' => 'unittest',
			'wmgMasterDatacenter' => 'unittest',
		] );

		// Needed for TestServices.php
		self::setGlobals( [
			'wmgHostnames' => null,
			'wmgAllServices' => null,
			'wmgLocalServices' => null,
			'wmgMasterServices' => null,
		] );
		require __DIR__ . '/data/TestServices.php';

		$conf = new SiteConfiguration();
		$conf->suffixes = WmfConfig::SUFFIXES;
		$conf->wikis = WmfConfig::readDbListFile( $realm === 'labs' ? 'all-labs' : 'all' );
		$conf->settings = WmfConfig::getStaticConfig();

		// Make sure globals are restored, else they will be serialized on each
		// test run which slow the test run dramatically.
		self::restoreGlobals();
		return $conf;
	}

}
