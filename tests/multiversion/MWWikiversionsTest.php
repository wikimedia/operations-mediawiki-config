<?php

class MWWikiversionsTest extends PHPUnit\Framework\TestCase {

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
