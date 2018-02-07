<?php

class NocDblistTest extends PHPUnit_Framework_TestCase {

	private static function getDblists( $dir ) {
		$files = [];

		foreach ( scandir( $dir ) as $fname ) {
			if ( substr( $fname, -strlen( '.dblist' ) ) === '.dblist' ) {
				$files[] = $fname;
			}
		}

		return $files;
	}

	public function testNocDblists() {
		$common = dirname( dirname( __DIR__ ) );
		$dblistsDir = "$common/dblists/";
		$nocConfDir = "$common/docroot/noc/conf/dblists/";

		$existingLinks = self::getDblists( $nocConfDir );

		foreach ( $existingLinks as $fname ) {
			$linkDestination = readlink( $nocConfDir . $fname );
			$this->assertEquals( $linkDestination, '../../../dblists/' . $fname );
		}
		$expectedLinks = self::getDblists( $dblistsDir );

		$this->assertEquals( $expectedLinks, $existingLinks );
	}

}
