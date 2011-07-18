<?php
if ( php_sapi_name() !== 'cli' ) {
	exit; // sanity, script run via CLI
}

/**
 * Run a MediaWiki script based on the parameters (like --wiki) given to CLI.
 *
 * The first argument must be the relative (to MediaWiki) script file path.
 * If only a filename is given, it will be assumed to reside in /maintenance.
 * The second argument must be the --wiki parameter. This is to avoid
 * any "options with args" ambiguity (see Maintenance.php).
 *
 * When the actual script is run, $argv[0] (this file's name) will be not be kept.
 * Also, $argv[1] (the script path) will be changed to the script file name.
 * All other arguments will be preserved.
 *
 * @return void
 */
function runMWScript() {
	global $argv;
	if ( count( $argv ) < 2 ) {
		die( "The MediaWiki script file path must be the first argument." );
	}

	$relFile = $argv[1]; // the script file to run
	# If no MW directory is given then assume this is a /maintenance script
	if ( strpos( $relFile, '/' ) === false ) {
		$relFile = "maintenance/$relFile"; // convenience
	}

	# Remove effects of this wrapper from $argv...
	array_shift( $argv ); // remove this file's name from args
	# Code stolen from wfBasename() in GlobalFunctions.php :)
	if ( preg_match( "#([^/\\\\]*?)[/\\\\]*$#", $argv[0], $matches ) ) {
		$argv[0] = $matches[1]; // make first arg the script file name
	}

	require_once( './wmf-config/MWVersion.php' );
	$file = getMediaWikiCli( $relFile );
	if ( !file_exists( $file ) ) {
		die( "The MediaWiki script file \"{$file}\" does not exist." );
	}

	# Run the script! (for HipHip, we will need to shell out here)
	require_once( $file );
}

runMWScript();
