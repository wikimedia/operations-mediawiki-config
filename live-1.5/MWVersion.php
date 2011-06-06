<?php

function host2db() {
	if ( $_SERVER['SERVER_NAME'] == 'localhost') {
			$dbname = getenv( 'MW_DBNAME' );
			if ( !isset( $dbname ) ) {
				die( 'Unable to determine MW_DBNAME on localhost' );
			}
			return $dbname;
	}
	$secure = getenv( 'MW_SECURE_HOST' );
	if ( (@$_SERVER['SCRIPT_NAME']) == '/w/thumb.php' && (@$_SERVER['SERVER_NAME']) == 'upload.wikimedia.org' ) {
		$pathBits = explode( '/', $_SERVER['PATH_INFO'] );
		$site = $pathBits[1];
		$lang = $pathBits[2];
	} elseif (php_sapi_name() == 'cgi-fcgi') {
		if (!preg_match('/^([^.]+).([^.]+).*$/', $_SERVER['SERVER_NAME'], $m))
		die("invalid hostname");

		$lang = $m[1];
		$site = $m[2];

		if (in_array($lang, array("commons", "grants", "sources", "wikimania", "wikimania2006", "foundation", "meta")))
		$site = "wikipedia";
	} elseif( $secure ) {
		if (!preg_match('/^([^.]+).([^.]+).*$/', $secure, $m))
		die("invalid hostname");

		$lang = $m[1];
		$site = $m[2];

		if (in_array($lang, array("commons", "grants", "sources", "wikimania", "wikimania2006", "foundation", "meta")))
		$site = "wikipedia";
	} else {
		if ( !isset( $site ) ) {
			$site = "wikipedia";
			if ( !isset( $lang ) ) {
				$server = $_SERVER['SERVER_NAME'];
				$docRoot = $_SERVER['DOCUMENT_ROOT'];
				
				if ( preg_match( '/^(?:\/usr\/local\/apache\/|\/home\/wikipedia\/)(?:htdocs|common\/docroot)\/([a-z]+)\.org/', $docRoot, $matches ) ) {
					$site = $matches[1];
					if ( preg_match( '/^(.*)\.' . preg_quote( $site ) . '\.org$/', $server, $matches ) ) {
						$lang = $matches[1];
						// For some special subdomains, like pa.us
						$lang = str_replace( '.', '-', $lang );
					} else if ( preg_match( '/^(.*)\.prototype\.wikimedia\.org$/', $server, $matches ) ) {
						$lang = $matches[1];
					} else {
						die( "Invalid host name ($server), can't determine language" );
					}
				} elseif ( preg_match( "/^\/usr\/local\/apache\/(?:htdocs|common\/docroot)\/([a-z0-9\-_]*)$/", $docRoot, $matches ) ) {
					$site = "wikipedia";
					$lang = $matches[1];
				} else {
					die( "Invalid host name (docroot=" . $_SERVER['DOCUMENT_ROOT'] . "), can't determine language" );
				}
			}
		}
	}
	if ( $site == "wikipedia" ) {
		$dbSuffix = "wiki";
	} else {
		$dbSuffix = $site;
	}
	$dbname = str_replace( "-", "_", $lang . $dbSuffix );
	return $dbname;
}

function getMediaWiki( $file ) {
	$dbname = host2db( );

	$db = dba_open( '/usr/local/apache/common/wikiversions.db', 'r', 'cdb' );
	if ( $db ) {
		$version = dba_fetch( $dbname, $db );
	} else {
		//don't error for now
		//trigger_error( "Unable to open /usr/local/apache/common/wikiversions.db. Assuming php-1.17", E_USER_ERROR );
		$version = 'php-1.17';
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

	chdir( $IP );
	putenv( "MW_INSTALL_PATH=$IP" );
	return "$IP/$file";
}

?>
	
