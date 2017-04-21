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

	public static function tearDownAfterClass() {
		parent::tearDownAfterClass();
		if ( $this->globals ) {
			throw new Exception( __CLASS__ . ': setGlobals() used without restoreGlobals()' );
		}
	}

}
