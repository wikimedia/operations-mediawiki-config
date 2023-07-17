<?php
/**
 * Fetch MediaWiki's dbconfig without loading all of MediaWiki
 *
 * Done this way to avoid loading all of MediaWiki and doing various ugly tricks.
 * While this duplicates code no doubt, it's easier to manage/follow as I expect
 * we won't change datastore as often as MediaWiki's code might change.
 *
 */

// Time to store data in APCU
define( 'CACHE_TTL', 120 );

// APCU cache key format
define( 'CACHE_KEY_FMT', 'wmf::dbconfig::%s' );

// Structure of an SRV record
define( 'SRV_RECORD_FMT', '_etcd-client-ssl._tcp.%s.' );

/**
 *  Get the etcd servers from the domain.
 *
 * @param string $domain
 * @return array
 */
function wmfGetEtcdServers( $domain ) {
	$result = [];
	$srv_record = sprintf( SRV_RECORD_FMT, $domain );
	$response = dns_get_record( $srv_record, DNS_SRV );
	if ( !$response ) {
		return $result;
	}

	foreach ( $response as $record ) {
		$result[] = [
			'host' => $record['target'],
			'port' => (int)$record['port'],
		];
	}
	return $result;
}

/**
 *  Fetch the database configuration, return it as an associative array.
 * @param string $dc
 * @param string $domain
 * @return array
 */
function wmfGetDbConfig( $dc, $domain ) {
	// result container
	$dbConfig = [];

	// Cache hit
	$cache_key = sprintf( CACHE_KEY_FMT, $dc );
	$cached = apcu_fetch( $cache_key );
	if ( $cached ) {
		return $cached;
	}

	// SRV dns resolution
	// If we got no SRV records, return an empty config.
	// This wouldn't be ok in other contexts, but it's fine for this case.
	$servers = wmfGetEtcdServers( $domain );
	if ( $servers == [] ) {
		return $dbConfig;
	}

	// Cycle through the etcd servers; return as soon as we get a proper response.
	$uri_path = sprintf( "/v2/keys/conftool/v1/mediawiki-config/%s/dbconfig", $dc );
	foreach ( $servers as $server ) {
		$etcdUri = sprintf( "https://%s:%d%s", $server['host'], $server['port'], $uri_path );
		$ch = curl_init( $etcdUri );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		$response = curl_exec( $ch );
		if ( $response ) {
			$data = json_decode( $response, true );
			// Please note: while "false" and 0 are valid json values we could decode,
			// it would still be not valid for our purposes
			if ( !$data ) {
				continue;
			}
			$dbConfigRecord = json_decode( $data['node']['value'], true );
			if ( !$dbConfigRecord ) {
				continue;
			}
			$dbConfig = $dbConfigRecord['val'];
			// Store the value for 2 minutes
			apcu_store( $cache_key, $dbConfig, CACHE_TTL );
			return $dbConfig;
		}
	}
	// Nothing was found. Return an empty array.
	return $dbConfig;
}

// If called directly, return the content as json
$matches = [];
if ( $_SERVER['HTTP_HOST'] == 'noc.wikimedia.org' && preg_match( '/^\/dbconfig\/(\w+).json/', $_SERVER['REQUEST_URI'], $matches ) ) {
	// Get the environment like we'd do in MediaWiki
	$env = require '../../wmf-config/env.php';
	$dc = $matches[1];

	if ( !in_array( $dc, $env['dcs'] ) ) {
		http_response_code( 404 );
		exit( "Not found" );
	}
	$srvDomain = sprintf( '%s.wmnet', $env['dc'] );
	$dbConfig = wmfGetDbConfig( $dc, $srvDomain );
	header( "Content-type: application/json" );
	print_r( json_encode( $dbConfig ) );
}
