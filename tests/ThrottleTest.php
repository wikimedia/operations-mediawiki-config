<?php

require_once __DIR__ . '/../wmf-config/throttle.php';

class ThrottleTest extends PHPUnit_Framework_TestCase {

	public static function provideThrottlingExceptions () {
		global $wmgThrottlingExceptions;
		$ret = [];
		foreach( $wmgThrottlingExceptions as $rule ) {
			$ret[] = [ $rule ];
		}
		return $ret;
	}

	/**
	 * @dataProvider provideThrottlingExceptions
	 */
	public function testThrottlingExceptionsKeys ( $rule ) {
		$validParameters = self::getThrottlingExceptionsValidParameters();

		foreach ( $rule as $key => $value ) {
				$this->assertContains(
					$key, $validParameters,
					"Invalid parameter in a throttle rule detected: $key"
				);
		}
	}

	/**
	 * @dataProvider provideThrottlingExceptions
	 */
	public function testIfThrottlingExceptionsContainTheRequiredParameters ( $rule ) {
		global $wmgThrottlingExceptions;

		$keys = array_keys( $rule );
		$this->assertContains( 'from', $keys, "Throttle rule required parameter missing: from" );
		$this->assertContains( 'to', $keys, "Throttle rule required parameter missing: to" );
	}

	/**
	 * @dataProvider provideThrottlingExceptions
	 */
	public function testIfThrottlingExceptionsDontContainBothRangeAndIP ( $rule ) {
		global $wmgThrottlingExceptions;

		$keys = array_keys( $rule );
		$this->assertFalse (
			in_array( 'IP', $keys ) &&
			in_array( 'range', $keys ),
			"Throttle rules can apply to range(s) or IP(s) but not both"
		);
	}

	/**
	 * @dataProvider provideThrottlingExceptions
	 */
	public function testThrottlingExceptionsValues ( $rule ) {
		global $wmgThrottlingExceptions;

		$validParameters = self::getThrottlingExceptionsValidParameters();

		foreach ( $rule as $key => $value ) {
			// Parses date
			// strtotime returns false when the string can't be parsed.
			if ( array_key_exists( 'from', $rule ) ) {
				$this->assertNotSame(
					false, strtotime( $rule['from'] ),
					"Invalid value in a throttle rule detected: from should be a valid date"
				 );
			}
			if ( array_key_exists( 'to', $rule ) ) {
				$this->assertNotSame(
					false, strtotime( $rule['to'] ),
					"Invalid value in a throttle rule detected: to should be a valid date"
				);
			}

			// Parses integer
			// We accept numeric integer and string representation of integers as digits.
			if ( array_key_exists( 'value', $rule ) ) {
				$this->assertTrue(
					is_int( $rule['value'] ) ||  ctype_digit( $rule['value'] ),
					"Invalid value in a throttle rule detected: range should be integer"
				);
			}

			// Parses IP and range
			// Should be a string or an array
			if ( array_key_exists( 'IP', $rule ) ) {
				$this->assertTrue(
					is_array( $rule['IP'] ) || is_string( $rule['IP'] ),
					"Invalid valud in a throttle rule detected: IP should be a string or an array"
				);
			}
			if ( array_key_exists( 'range', $rule ) ) {
				$this->assertTrue(
					is_array( $rule['range'] ) || is_string( $rule['range'] ),
					"Invalid valud in a throttle rule detected: range should be a string or an array"
				);
			}
		}
	}

	protected static function getThrottlingExceptionsValidParameters () {
		return [
			'from',
			'to',
			'IP',
			'range',
			'dbname',
			'value',
		];
	}
}
