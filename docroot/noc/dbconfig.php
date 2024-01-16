<?php
/**
 * Fetch MediaWiki's dbconfig without loading all of MediaWiki
 *
 * Done this way to avoid loading all of MediaWiki and doing various ugly tricks.
 * While this duplicates code no doubt, it's easier to manage/follow as I expect
 * we won't change datastore as often as MediaWiki's code might change.
 *
 */
require_once __DIR__ . '/../../src/Noc/EtcdCachedConfig.php';

if ( $_SERVER['HTTP_HOST'] == 'noc.wikimedia.org' && preg_match( '/^\/dbconfig\/(\w+).json/', $_SERVER['REQUEST_URI'], $matches ) ) {
	// Get the environment like we'd do in MediaWiki
	$env = require '../../wmf-config/env.php';
	$dc = $matches[1];

	if ( !in_array( $dc, $env['dcs'] ) ) {
		http_response_code( 404 );
		exit( "Not found" );
	}
	$dbConfig = \Wikimedia\MWConfig\Noc\EtcdCachedConfig::getInstance()->getValue( $dc . '/dbconfig' );
	header( "Content-type: application/json" );
	print_r( json_encode( $dbConfig ) );
}
