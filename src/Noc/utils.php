<?php
namespace Wikimedia\MWConfig\Noc;

/**
 * Utility functions used across noc.wikimedia.org
 * @param bool $strip_php
 * @return array
 */
 function getWikiVersions( $strip_php = false ) {
	$wikiversions = json_decode( file_get_contents( __DIR__ . '/../../wikiversions.json' ), true );
	$inuse = array_unique( array_values( $wikiversions ) );
	sort( $inuse );

	if ( $strip_php ) {
		return array_map( fn( $v ) => str_replace( 'php-', '', $v ), $inuse );
	}
	return $inuse;
 }

/**
 * @param string $header
 */
function wmfNocHeader( $header ): void {
	// Don't emit headers in unit tests
	if ( PHP_SAPI !== 'cli' ) {
		header( $header );
	}
}

/**
 * Check if apcu is enabled
 */
function hasApcu(): bool {
	return ( ini_get( 'apc.enabled' ) && ( ( PHP_SAPI !== 'cli' ) || ini_get( 'apc.enable_cli' ) ) );
}

/**
 * Extract the url parts from the request
 */
function getParsedRequestUrl(): array {
	$https = $_SERVER['HTTPS'] ?? 'off';
	if ( $https == 'on' ) {
		$url = 'https://';
	} else {
		$url = 'http://';
	}
	$url .= $_SERVER['HTTP_HOST'];
	$url .= $_SERVER['REQUEST_URI'];
	return parse_url( $url );
}
