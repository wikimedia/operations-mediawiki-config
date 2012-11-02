<?php
abstract class WgConfTestCase extends PHPUnit_Framework_TestCase {

	// databasename
	private $dbname;
	// $cluster / $realm. Aka pmtpa / wmflabs
	private $realm;

	// $cluster / $realm allowed values
	private $allowedRealms = array( 'production', 'labs' );

	function __construct( $name = null, array $data = array(), $dataName = '' ) {
		parent::__construct( $name, $data, $dataName );

		$this->backupGlobals = false;
		$this->backupStaticAttributes = false;
	}

	#### PUBLIC METHODS ###############################################

	/** helper to set $cluster / $realm */
	public function givenRealm( $realm ) {
		if( !in_array( $realm, $this->allowedRealms ) ) {
			throw new Exception( __METHOD__ . " given invalid realm '$realm', should be one of " . join( ', ', $this->allowedRealms ) );
		}
		$this->realm = $realm;
		return $this;
	}

	/** helper to set $wgDBname */
	public function givenDBname( $dbname ) {
		$this->dbname = $dbname;
		return $this;
	}

	public function assertGlobal( $global, $expectedvalue, $msg = '' ) {
		extract( $this->loadConf() );

		// Actually run the assertion
		$this->assertEquals(
			$expectedvalue
			, $$global
			, $msg
		);
	}

	#### PRIVATE METHODS ##############################################
	private function loadConf() {
		global $wgDBname, $cluster, $realm, $wgConf, $IP;

		MWMultiVersion::newFromDBName($this->dbname);
		$wgDBname = $this->dbname;
		$realm    = $this->realm;

		unset( $wgConf );  # safeguard

		# wmfConfigDir must be set before loading CommonSettings.php
		$wmfConfigDir = __DIR__ ."/../wmf-config" ;

		global $wmfSwiftConfig;
		global $wmgCaptchaSecret;
		global $wmgMFRemotePostFeedbackUsername;
		global $wmgMFRemotePostFeedbackPassword;

		require( "$wmfConfigDir/CommonSettings.php" );

		list( $site, $lang ) = $wgConf->siteFromDB( $wgDBname );
		$globals = $wgConf->getAll( $wgDBname, 'wiki', array(
			'lang'    => $lang,
			'docroot' => null,
			'site'    => $site,
			'stdlogo' => null,
		), $wikiTags );

		$this->assertEquals( $this->dbname, $wgDBname );
		$this->assertEquals( $this->realm , $realm    );

		return $globals;
	}
}
