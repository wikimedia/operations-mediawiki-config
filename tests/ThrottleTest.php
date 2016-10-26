<?php

require_once __DIR__ . '/../wmf-config/throttle.php';

class ThrottleTest extends PHPUnit_Framework_TestCase {

	public function testThrottlingExceptionsKeys () {
		global $wmgThrottlingExceptions;

		$validParameters = self::getThrottlingExceptionsValidParameters();

		foreach ( $wmgThrottlingExceptions as $rule ) {
			foreach ( $rule as $key => $value ) {
					$this->assertContains(
						$key, $validParameters,
						"Invalid parameter in a throttle rule detected: $key"
					);
			}
		}
	}

	protected static function getThrottlingExceptionsValidParameters () {
		return [
			'from',
			'to',
			'IP', // temporary — we can now get rid of this case tolerance
			'ip',
			'range',
			'dbname',
			'value',
		];
	}
}
