<?php
use Wikimedia\MWConfig\ClusterConfig;

/**
 * @covers \Wikimedia\MWConfig\ClusterConfig
 */
class ClusterConfigTest extends PHPUnit\Framework\TestCase {
	public static function provider() {
		yield 'simple server' => [
			'appserver', []
		];
		yield 'kubernetes' => [
			'kube-mw-debug', [ 'k8s', 'debug' ]
		];
		yield 'api appserver' => [
			'api_appserver', [ 'api' ]
		];
		yield 'jobrunner on k8s' => [
			'kube-mw-jobrunner', [ 'async', 'k8s' ]
		];
		yield 'api-int on k8s' => [
			'kube-mw-api-int', [ 'api', 'k8s', 'api-int' ]
		];
		yield 'api-ext on k8s' => [
			'kube-mw-api-ext', [ 'api', 'k8s' ]
		];
		yield 'mw-web on k8s' => [
			'kube-mw-web', [ 'k8s' ]
		];
		yield 'parsoid' => [
			'parsoid', [ 'parsoid' ]
		];
		yield 'appserver canary on k8s' => [
			'kube-wiki-canary', [ 'k8s', 'canary' ]
		];
		yield 'parsoid on k8s' => [
			'kube-mw-parsoid', [ 'k8s', 'parsoid' ]
		];
		yield 'Empty variable' => [
			null, []
		];
	}

	/**
	 * @dataProvider provider
	 */
	public function testTraits( $servergroup, $expected_traits ) {
		ClusterConfig::reset();
		// Monkey-patch the $_SERVER variable
		$_SERVER['SERVERGROUP'] = $servergroup;
		$toTest = ClusterConfig::getInstance();
		unset( $_SERVER['SERVERGROUP'] );
		$this->assertSame( $servergroup ?? '', $toTest->getCluster() );
		$this->assertSame( in_array( 'k8s', $expected_traits ), $toTest->isK8s() );
		$this->assertSame( in_array( 'async', $expected_traits ), $toTest->isAsync() );
		$this->assertSame( in_array( 'parsoid', $expected_traits ), $toTest->isParsoid() );
		$this->assertSame( in_array( 'debug', $expected_traits ), $toTest->isDebug() );
		$this->assertSame( in_array( 'api', $expected_traits ), $toTest->isApi() );
		$this->assertSame( in_array( 'api-int', $expected_traits ), $toTest->isInternalApi() );
		$this->assertSame( in_array( 'canary', $expected_traits ), $toTest->isCanary() );
	}

	public static function provideHostname() {
		yield 'Debug server 1001' => [ 'mwdebug1001', true ];
		yield 'Debug server 1002' => [ 'mwdebug1002', true ];
		yield 'Not debug server' => [ 'mwnotdibugserver1000', false ];
	}

	/**
	 * @dataProvider provideHostname
	 */
	public function testHostname( $hostname, $expected ) {
		ClusterConfig::reset();

		// Ugly but we want to hack this in for testing. We can't use
		// $_SERVER because php_uname( 'n' ) may not be the same as
		// $_SERVER['SERVER_NAME'].
		$clusterConfig = ClusterConfig::getInstance();
		$mockClusterConfig = new ReflectionClass( $clusterConfig );
		$property = $mockClusterConfig->getProperty( 'hostname' );
		$property->setValue( $clusterConfig, $hostname );

		$this->assertSame( $expected, $clusterConfig->isDebug() );
	}
}
