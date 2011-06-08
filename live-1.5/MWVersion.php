<?php

function getMediaWiki( $file ) {
$dbname = $multiVersion->getDatabase( $siteInfo['site'], $siteInfo['lang']);
	$secure = getenv( 'MW_SECURE_HOST' );
    $host = $secure ? $secure : $_SERVER['HTTP_HOST'];
    
	require( dirname( __FILE__ ) . '/../wmf-config/MWMultiVersion.php' );
	$multiVersion = new MWMultiVersion;
	$siteInfo = array();
	if ( (@$_SERVER['SCRIPT_NAME']) == '/w/thumb.php' && (@$_SERVER['SERVER_NAME']) == 'upload.wikimedia.org' ) {
		$siteInfo = $multiVersion->getUploadSiteInfo( $_SERVER['PATH_INFO'] );
	} else {
		$siteInfo = $multiVersion->getSiteInfo( $_SERVER['SERVER_NAME'], $_SERVER['DOCUMENT_ROOT'] );
	}
	
	$version = $multiVersion->getVersion( $siteInfo['site'], $siteInfo['lang']);

	if ( $host == 'test.wikipedia.org' && !$secure &&
	!preg_match( '!thumb\.php!', $_SERVER['REQUEST_URI'] ) ) {
		define( 'TESTWIKI', 1 );
		// As horrible hack for NFS-less iamge scalers, use regular docroot for thumbs?
		#		$IP = '/home/wikipedia/common/php-1.5';
		$IP = "/home/wikipedia/common/$version";
	} else {
		$IP = "/usr/local/apache/common/$version";
	}

	chdir( $IP );
	putenv( "MW_INSTALL_PATH=$IP" );
	return "$IP/$file";
}

	
