<?php

class NocDblistTest extends PHPUnit_Framework_TestCase {
	public function testNocDblists () {
		$common = dirname( dirname( __DIR__ ) );
		$dblistsDir =  "$common/dblists/";
		$nocConfDir = "$common/docroot/noc/conf/";

		$existingLinks = [];
		foreach ( scandir( $nocConfDir ) as $fname ) {
			if ( substr( $fname, -strlen( '.dblist' ) ) === '.dblist' ) {
				$linkDestination = readlink( $nocConfDir . $fname );
				$this->assertEquals( $linkDestination, '../../../dblists/' . $fname );
				$existingLinks[] = substr( $linkDestination, strlen( '../../../dblists/' ) );
			}
		}
		$expectedLinks = array_values( array_diff( scandir( $dblistsDir ), [ '.', '..' ] ) );

		$this->assertEquals( $expectedLinks, $existingLinks );
	}
}
