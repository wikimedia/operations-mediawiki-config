<?php
/**
 * PHPUnit testcase for $wgConf testing.
 *
 * @license GPLv2 or later
 * @author Erik Bernhardson <ebernhardson@wikimedia.org>
 * @copyright Copyright © 2015, Erik Bernhardson <ebernhardson@wikimedia.org>
 * @file
 */

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

}
