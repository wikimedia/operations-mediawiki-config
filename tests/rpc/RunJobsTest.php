<?php
class RunJobsTest extends \PHPUnit_Framework_TestCase {
	protected $oldServer;

	protected function setUp() {
		parent::setUp();

		$this->oldServer = $_SERVER;
	}

	protected function tearDown() {
		$_SERVER = $this->oldServer;

		parent::tearDown();
	}

	/**
	 * @expectedException Exception
	 * @expectedExceptionCode 1
	 * @expectedExceptionMessage Only loopback requests are allowed
	 */
	function testRaiseAnExceptionFromNonLocalhost() {
		$_SERVER['REMOTE_ADDR'] = '192.0.2.42';
		require __DIR__ . '/../../rpc/RunJobs.php';
	}

	/**
	 * @dataProvider provideLocalhostIps
	 *
	 * @expectedException PHPUnit_Framework_Error_Notice
	 * @expectedExceptionMessage Undefined index: REQUEST_METHOD
	 */
	function testAcceptLocalhostRequest( $address ) {
		$_SERVER['REMOTE_ADDR'] = $address;
		require __DIR__ . '/../../rpc/RunJobs.php';
	}

	public function provideLocalhostIps() {
		return [
			'IPv4 loopback' => [ '127.0.0.1' ],
			'IPv6 loopback' => [ '0:0:0:0:0:0:0:1' ],
			'IPv6 loopback (short)' => [ '::1' ],
		];
	}
}
