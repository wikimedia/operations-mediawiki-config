<?php
/**
 * Get the location of the correct version of a MediaWiki web
 * entry-point file given environmental variables such as the server name.
 * This function should only be called on web views.
 *
 * If the wiki doesn't exist, then wmf-config/missing.php will
 * be included (and thus displayed) and PHP will exit.
 *
 * If it does, then this function also has some other effects:
 * (a) Sets the $IP global variable (path to MediaWiki)
 * (b) Sets the MW_INSTALL_PATH environmental variable
 * (c) Changes PHP's current directory to the directory of this file.
 *
 * @param $file string File path (relative to MediaWiki dir)
 * @param $wiki string Force the Wiki ID rather than detecting it
 * @return string Absolute file path with proper MW location
 */
function getMediaWiki( $file, $wiki = null ) {
	require_once( __DIR__ . '/MWMultiVersion.php' );
	require MWMultiVersion::getMediaWiki( $file, $wiki );
}

/**
 * Get the location of the correct version of a MediaWiki CLI
 * entry-point file given the --wiki parameter passed in.
 *
 * This also has some other effects:
 * (a) Sets the $IP global variable (path to MediaWiki)
 * (b) Sets the MW_INSTALL_PATH environmental variable
 * (c) Changes PHP's current directory to the directory of this file.
 *
 * @param $file string File path (relative to MediaWiki dir)
 * @return string Absolute file path with proper MW location
 */
function getMediaWikiCli( $file ) {
	require_once( __DIR__ . '/MWMultiVersion.php' );
	require MWMultiVersion::getMediaWikiCli( $file );
}
