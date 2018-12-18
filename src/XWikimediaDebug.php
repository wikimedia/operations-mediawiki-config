<?php

namespace Wikimedia\MWConfig;

/**
 * Parse X-Wikimedia-Debug options.
 *
 * See https://wikitech.wikimedia.org/wiki/X-Wikimedia-Debug
 *
 * The header options are:
 * - 'forceprofile': One-off profile to stdout.
 * - 'profile': One-off profile to XHGui.
 * - 'readonly': (See wmf-config/CommonSettings.php).
 * - 'log': (See wmf-config/logging.php).
 */
class XWikimediaDebug {
	private static $instance;
	private $options;
	private $headerPresent;

	/**
	 * @return XWikimediaDebug
	 */
	public static function getInstance() {
		if ( !self::$instance ) {
			self::$instance = new self( $_SERVER['HTTP_X_WIKIMEDIA_DEBUG'] ?? null );
		}
		return self::$instance;
	}

	/**
	 * @param string|null $header
	 */
	public function __construct( $header ) {
		$this->options = [];
		if ( $header === null ) {
			$this->headerPresent = false;
			return;
		}
		$this->headerPresent = true;
		$tokens = preg_split( '/;\s*/', $header );
		foreach ( $tokens as $token ) {
			$eqParts = explode( '=', $token, 2 );
			if ( count( $eqParts ) === 2 ) {
				$optName = $eqParts[0];
				$optValue = $eqParts[1];
			} else {
				$optName = $token;
				$optValue = true;
			}
			$this->options[$optName] = $optValue;
		}
	}

	/**
	 * Returns true if an option is present in the header, false otherwise.
	 *
	 * @param string $optName
	 * @return bool
	 */
	public function hasOption( $optName ) {
		return isset( $this->options[$optName] );
	}

	/**
	 * Return the value of an option, or null if the option was not present
	 * in the header, or true if it was given in the header as a boolean option
	 * with no equals sign.
	 *
	 * @param string $optName
	 * @return mixed
	 */
	public function getOption( $optName ) {
		return $this->options[$optName] ?? null;
	}

	/**
	 * Test if the X-Wikimedia-Debug request header was present
	 *
	 * @return bool
	 */
	public function isHeaderPresent() {
		return $this->headerPresent;
	}
}
