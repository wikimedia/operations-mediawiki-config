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
