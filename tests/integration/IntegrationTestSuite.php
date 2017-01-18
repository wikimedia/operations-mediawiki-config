<?php
class IntegrationTestSuite extends PHPUnit_Framework_TestSuite {

	const VER_PREVIOUS = 1;
	const VER_CURRENT = 2;

	/**
	 * @param $versionPointer either VER_CURRENT or VER_PREVIOUS
	 */
	function __construct( $versionPointer ) {
		parent::__construct();
		$facade = new File_Iterator_Facade;
		$files = $facade->getFilesAsArray(
			__DIR__,
			'Test.php'
		);
		require_once __DIR__ . '/../../php-1.29.0-wmf.7/includes/AutoLoader.php';
		$this->addTestFiles( $files );
	}

	public static function suite( $versionPointer ) {
		return new self( __CLASS__ . '_' . $versionPointer );
	}
}
