<?php

use Wikimedia\MWConfig\Noc\ConfigFile;

class ConfigFileTest extends PHPUnit\Framework\TestCase {
	/** @var ConfigFile|null */
	protected $conf = null;

	protected function setUp(): void {
		parent::setUp();
		$txtFiles = [
			'wmf-config/CommonSettings.php',
			'docroot/noc/README.md'
		];
		$plainFiles = [ 'langlist', 'src/Noc/ConfigFile.php' ];
		$dirs = [ 'dblists' ];
		$this->conf = new ConfigFile( $txtFiles, $plainFiles, $dirs );
	}

	public function provideDiskPathByUrl() {
		return [
			[ '/conf/CommonSettings.php.txt', 'wmf-config/CommonSettings.php' ],
			[ '/conf/README.md.txt', 'docroot/noc/README.md' ],
			[ '/conf/langlist', 'langlist' ],
			[ '/conf/dblists/s1.dblist', 'dblists/s1.dblist' ]
		];
	}

	/**
	 * @covers \Wikimedia\MWConfig\Noc\ConfigFile::getDiskPathByUrl
	 *
	 * @dataProvider provideDiskPathByUrl
	 */
	public function testGetDiskPathByUrl( $url, $relativePath ) {
		$absPath = realpath( ConfigFile::ROOT . $relativePath );
		$this->assertEquals(
			$absPath,
			$this->conf->getDiskPathByUrl( $url )
		);
	}

	public function provideDiskPathByLabel() {
		return [
			[ 'CommonSettings.php', 'wmf-config/CommonSettings.php' ],
			[ 'README.md', 'docroot/noc/README.md' ],
			[ 'langlist', 'langlist' ],
			[ 'dblists/s1.dblist', 'dblists/s1.dblist' ]
		];
	}

	/**
	 * @covers \Wikimedia\MWConfig\Noc\ConfigFile::getDiskPathByLabel
	 *
	 * @dataProvider provideDiskPathByLabel
	 */
	public function testGetDiskPathByLabel( $label, $relativePath ) {
		$absPath = realpath( ConfigFile::ROOT . $relativePath );
		$this->assertEquals(
			$absPath,
			$this->conf->getDiskPathByLabel( $label )
		);
	}

	public function provideRouteFromLabel() {
		return [
			[ 'CommonSettings.php', '/conf/CommonSettings.php.txt' ],
			[ 'README.md', '/conf/README.md.txt' ],
			[ 'langlist', '/conf/langlist' ],
			[ 'dblists/s1.dblist', '/conf/dblists/s1.dblist' ]
		];
	}

	/**
	 * @covers \Wikimedia\MWConfig\Noc\ConfigFile::getRouteFromLabel
	 *
	 * @dataProvider provideRouteFromLabel
	 */
	public function testGetRouteFromLabel( $label, $expectedRoute ) {
		$this->assertEquals(
			$expectedRoute,
			$this->conf->getRouteFromLabel( $label )
		);
	}

	/**
	 * @covers \Wikimedia\MWConfig\Noc\ConfigFile::getConfigRoutes
	 */
	public function testGetConfigRoutes() {
		$this->assertCount( 4, array_keys( $this->conf->getConfigRoutes() ) );
	}

	/**
	 * @covers \Wikimedia\MWConfig\Noc\ConfigFile::getDblistRoutes
	 */
	public function testGetDblistRoutes() {
		foreach ( $this->conf->getDblistRoutes() as $label => $url ) {
			$this->assertSame( 0, strpos( $label, 'dblists/' ) );
			$this->assertEquals( $url, "/conf/$label" );
		}
	}

	/**
	 * @covers \Wikimedia\MWConfig\Noc\ConfigFile::getRepoPath
	 */
	public function testGetRepoPath() {
		// This is pretty silly to test, in abstract. Let's just make sure
		// The behaviour stays the same.
		$path = realpath( ConfigFile::ROOT . 'docroot/noc/README.md' );
		$this->assertEquals( 'docroot/noc/README.md', $this->conf->getRepoPath( $path ) );
	}
}
