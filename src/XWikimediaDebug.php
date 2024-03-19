<?php

namespace Wikimedia\MWConfig;

/**
 * Parse X-Wikimedia-Debug options.
 *
 * See https://wikitech.wikimedia.org/wiki/X-Wikimedia-Debug
 *
 * The options are:
 * - 'backend': Select which debug server to use.
 * - 'forceprofile': One-off profile to stdout.
 * - 'profile': One-off profile to XHGui.
 * - 'readonly': (See wmf-config/CommonSettings.php).
 * - 'log': (See wmf-config/logging.php).
 * - 'shorttimeout': Set a short request timeout.
 * - 'expire': Ignore X-Wikimedia-Debug if the request is made after the given Unix timestamp.
 */
class XWikimediaDebug {
	/**
	 * How far expiry is allowed to be in the future.
	 * @see \WikimediaEvents\Special\SpecialWikimediaDebug::MAX_EXPIRY
	 */
	private const MAX_EXPIRY = 24 * 3600;

	/** @var XWikimediaDebug|null */
	private static $instance;
	/** @var array */
	private $options;

	/**
	 * @return XWikimediaDebug
	 */
	public static function getInstance() {
		if ( !self::$instance ) {
			$header = $_SERVER['HTTP_X_WIKIMEDIA_DEBUG'] ?? null;
			$cookie = $_COOKIE['X-Wikimedia-Debug'] ?? null;
			self::$instance = new self( $header, $cookie );
		}
		return self::$instance;
	}

	/**
	 * @param string|null $headerString Value of X-Wikimedia-Debug header
	 * @param string|null $cookieString Value of X-Wikimedia-Debug cookie
	 */
	public function __construct( $headerString, $cookieString ) {
		$optionString = $headerString ?? rawurldecode( $cookieString ?? '' );
		// It's easy to set the cookie and forget about it, so we require an explicit expiry date for it
		// and reject it if it's in the past or too far into the future.
		$requireExpiry = $headerString === null;

		$this->options = $options = [];
		if ( $optionString === '' ) {
			return;
		}

		$tokens = preg_split( '/;\s*/', $optionString );
		foreach ( $tokens as $token ) {
			$eqParts = explode( '=', $token, 2 );
			if ( count( $eqParts ) === 2 ) {
				$optName = $eqParts[0];
				$optValue = $eqParts[1];
			} else {
				$optName = $token;
				$optValue = true;
			}
			$options[$optName] = $optValue;
		}

		if ( $requireExpiry ) {
			$expire = $options['expire'] ?? 0;
			if ( $expire < $this->time() || $expire > $this->time() + self::MAX_EXPIRY ) {
				return;
			}
		}

		$this->options = $options;
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

	protected function time() {
		return time();
	}
}
