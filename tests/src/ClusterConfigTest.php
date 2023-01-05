<?php
use Wikimedia\MWConfig\ClusterConfig;

class ClusterConfigTest extends PHPUnit\Framework\TestCase {
	public static function provider() {
		yield 'simple server' => [
			'appserver', []
		];
		yield 'kubernetes' => [
			'kube-test', [ 'k8s', 'test' ]
		];
		yield 'test jobrunner' => [
			'jobrunner-debug', [ 'async', 'test' ]
		];
		yield 'test parsoid on k8s' => [
			'kube-parsoid-test', [ 'k8s', 'parsoid', 'test' ]
		];
		yield 'appserver canary on k8s' => [
			'kube-wiki-canary', [ 'k8s', 'canary' ]
		];
		yield 'Empty variable' => [
			null, []
		];
	}

	/**
	 * @covers ClusterConfig
	 *
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
		$this->assertSame( in_array( 'test', $expected_traits ), $toTest->isTest() );
		$this->assertSame( in_array( 'canary', $expected_traits ), $toTest->isCanary() );
	}
}
