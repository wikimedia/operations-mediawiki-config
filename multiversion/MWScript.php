<?php
if ( php_sapi_name() !== 'cli' ) {
	die( "This script can only be run from the command line.\n" );
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
 * @return string Absolute MediaWiki script path
 */
function getMWScriptWithArgs() {
	global $argv;
	if ( count( $argv ) < 2 ) {
		die( "The MediaWiki script file path must be the first argument.\n" );
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

	# For addwiki.php, the wiki DB doesn't yet exist, and for some
	# other maintenance scripts we don't care what wiki DB is used...
	$wikiless = array(
		'maintenance/mctest.php',
		'maintenance/addwiki.php',
		'maintenance/nextJobDB.php',
		'maintenance/dumpInterwiki.php',
		'maintenance/rebuildInterwiki.php',
		'extensions/WikimediaMaintenance/addWiki.php', // 1.19
		'extensions/WikimediaMaintenance/dumpInterwiki.php', // 1.19
		'extensions/WikimediaMaintenance/getJobQueueLengths.php',
		'extensions/WikimediaMaintenance/rebuildInterwiki.php' // 1.19
	);

	# Check if a --wiki param was given...
	# Maintenance.php will treat $argv[1] as the wiki if it doesn't start '-'
	if ( !isset( $argv[1] ) || !preg_match( '/^([^-]|--wiki(=|$))/', $argv[1] ) ) {
		if ( in_array( $relFile, $wikiless ) ) {
			# Assumme aawiki as Maintenance.php does.
			$argv = array_merge( array( $argv[0], "--wiki=aawiki" ), array_slice( $argv, 1 ) );
		}
	}

	# MWScript.php should be in common/
	require_once( dirname( __FILE__ ) . '/MWVersion.php' );
	$file = getMediaWikiCli( $relFile );
	if ( !file_exists( $file ) ) {
		die( "The MediaWiki script file \"{$file}\" does not exist.\n" );
	}

	return $file;
}

# Run the script!
require_once( getMWScriptWithArgs() );
