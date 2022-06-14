<?php

class MWWikiversionsTest extends PHPUnit\Framework\TestCase {

	/**
	 * @coversNothing
	 */
	public function testWikiversionsFileComplete() {
		$wikiversions = MWWikiversions::readWikiVersionsFile( __DIR__ . '/../../wikiversions.json' );
		$allDbs = MWWikiversions::readDbListFile( 'all' );

		$missingVersionKeys = array_diff( $allDbs, array_keys( $wikiversions ) );
		$this->assertEquals( [], $missingVersionKeys );
	}

	/**
	 * @covers MWWikiversions::evalDbListExpression
	 */
	public function testEvalDbListExpression() {
		$allDbs = MWWikiversions::readDbListFile( 'all' );
		$allPrivateDbs = MWWikiversions::readDbListFile( 'private' );
		$exprDbs = MWWikiversions::evalDbListExpression( 'all - private' );
		$expectedDbs = array_diff( $allDbs, $allPrivateDbs );
		sort( $exprDbs );
		sort( $expectedDbs );
		$this->assertEquals( $exprDbs, $expectedDbs );
	}
}
