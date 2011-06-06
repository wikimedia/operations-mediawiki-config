<?php

function host2db( $host ) {
	if( $host == 'wikimediafoundation.org' || $host == 'www.wikimediafoundation.org' )
		return 'foundationwiki';
	if ( $host == 'wikisource.org' || $host == 'www.wikisource.org' )
		return 'sourceswiki';
	if ( $host == 'mediawiki.org' || $host == 'www.mediawiki.org' )
		return 'mediawikiwiki';
	if( preg_match( '/^(.*)\.(.*)\.org$/', $host, $matches ) ) {
		list( $whole, $lang, $site ) = $matches;
		$lang = str_replace( '-', '_', $lang );
		switch( $site ) {
		case 'wikipedia':
		case 'wikimedia':
			return $lang . 'wiki';
		default:
			return $lang . $site;
		}
	}
	return null;
}

function wfIsConverted( $host ) {
	return true;

	/*
	$db = host2db( $host );

	#$all = array_map( 'trim', file( '/usr/local/apache/common/all.dblist' ) );
	#$started = array_map( 'trim', file( '/home/wikipedia/logs/conversion15start' ) );
	
	$converted = array_map( 'trim', file( '/usr/local/apache/common/1.17.dblist' ) );
	#$converted = array( 'test2wiki' );
	return in_array( $db, $converted );
	 */
}

function getMediaWiki( $file ) {
	$secure = getenv( 'MW_SECURE_HOST' );
	$host = $secure ? $secure : $_SERVER['HTTP_HOST'];
	$new = wfIsConverted( $host );
	$version = $new ? 'php-1.17' : 'wmf-deployment';
	if ( $new ) {
		define( 'ONE_SEVENTEEN', 1 );
	}

	if ( $host == 'test.wikipedia.org' && !$secure &&
			!preg_match( '!thumb\.php!', $_SERVER['REQUEST_URI'] ) ) {
		define( 'TESTWIKI', 1 );
		// As horrible hack for NFS-less iamge scalers, use regular docroot for thumbs?
#		$IP = '/home/wikipedia/common/php-1.5';
		$IP = "/home/wikipedia/common/$version";
	} else {
		$IP = "/usr/local/apache/common/$version";
	}
	#} elseif( $host == 'www.mediawiki.org' || $host == 'meta.wikimedia.org' ) {
	#	$IP = '/usr/local/apache/common/wmf-deployment';
	#} else {
	#	$IP = '/usr/local/apache/common/php-1.5';
	#}

	chdir( $IP );
	putenv( "MW_INSTALL_PATH=$IP" );
	return "$IP/$file";
}

?>
