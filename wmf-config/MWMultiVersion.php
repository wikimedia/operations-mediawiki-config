<?php

class MWMultiVersion {

	function getSiteInfo( $serverName, $docRoot ) {
		$secure = getenv( 'MW_SECURE_HOST' );
		$matches = array();
		if (php_sapi_name() == 'cgi-fcgi') {
			if (!preg_match('/^([^.]+).([^.]+).*$/', $serverName, $matches))
			die("invalid hostname");
	
			$lang = $matches[1];
			$site = $$matches[2];
	
			if (in_array($lang, array("commons", "grants", "sources", "wikimania", "wikimania2006", "foundation", "meta")))
			$site = "wikipedia";
		} elseif( $secure ) {
			if (!preg_match('/^([^.]+).([^.]+).*$/', $secure, $$matches))
			die("invalid hostname");
	
			$lang = $$matches[1];
			$site = $$matches[2];
	
			if (in_array($lang, array("commons", "grants", "sources", "wikimania", "wikimania2006", "foundation", "meta")))
			$site = "wikipedia";
		} else {
			if ( !isset( $site ) ) {
				$site = "wikipedia";
				if ( !isset( $lang ) ) {	
					if ( preg_match( '/^(?:\/usr\/local\/apache\/|\/home\/wikipedia\/)(?:htdocs|common\/docroot)\/([a-z]+)\.org/', $docRoot, $matches ) ) {
						$site = $matches[1];
						if ( preg_match( '/^(.*)\.' . preg_quote( $site ) . '\.org$/', $serverName, $matches ) ) {
							$lang = $matches[1];
							// For some special subdomains, like pa.us
							$lang = str_replace( '.', '-', $lang );
						} else if ( preg_match( '/^(.*)\.prototype\.wikimedia\.org$/', $serverName, $matches ) ) {
							$lang = $matches[1];
						} else {
							die( "Invalid host name ($serverName), can't determine language" );
						}
					} elseif ( preg_match( "/^\/usr\/local\/apache\/(?:htdocs|common\/docroot)\/([a-z0-9\-_]*)$/", $docRoot, $matches ) ) {
						$site = "wikipedia";
						$lang = $matches[1];
					} elseif ( $siteName == 'localhost' ) {
						$lang = getenv( 'MW_LANG' );
					} else {
						die( "Invalid host name (docroot=" . $_SERVER['DOCUMENT_ROOT'] . "), can't determine language" );
					}
				}
			}
		}
		return array(
			'site' => $site,
			'lang' => $lang,
		);
		
	}
	
	function getUploadSiteInfo( $pathInfo) {
		$pathBits = explode( '/', $pathInfo );
		$site = $pathBits[1];
		$lang = $pathBits[2];
		return array(
			'site' => $site,
			'lang' => $lang,
		);
	}	
	
	function getDatabase( $site, $lang ) {
		$dbname = getenv( 'MW_DBNAME' );
		if ( strlen( $dbname ) == 0 ) {
			if ( $site == "wikipedia" ) {
				$dbSuffix = "wiki";
			} else {
				$dbSuffix = $site;
			}
			$dbname = str_replace( "-", "_", $lang . $dbSuffix );
			putenv( 'MW_DBNAME=' . $dbname );
		}
		return $dbname;
	
	}
	
	function getVersion( $site, $lang ) {
		$dbname = $this->getDatabase( $site, $lang );
		$db = dba_open( '/usr/local/apache/common/wikiversions.db', 'r', 'cdb' );
		if ( $db ) {
			$version = dba_fetch( $dbname, $db );
		} else {
			//don't error for now
			//trigger_error( "Unable to open /usr/local/apache/common/wikiversions.db. Assuming php-1.17", E_USER_ERROR );
			$version = 'php-1.17';
		}
		return $version;
	}

}

?>