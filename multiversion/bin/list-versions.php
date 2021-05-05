#!/usr/bin/env php
<?php
require_once dirname( __DIR__ ) . '/MWWikiversions.php';

$path = $argv[1] ?? 'wikiversions.json';

$wikiversions = MWWikiversions::readWikiVersionsFile( $path );

ksort( $wikiversions );

// List a unique set of versions in use (omitting the php- directory prefix)
foreach ( array_unique( $wikiversions ) as $wikidb => $version ) {
	print( $wikidb . "\t" . preg_replace( '/^php-/', '', $version, 1 ) . "\n" );
}
