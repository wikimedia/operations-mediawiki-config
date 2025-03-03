<?php
if ( PHP_SAPI !== 'cli' && PHP_SAPI !== 'phpdbg' ) {
	echo "This script can only be run from the command line or via phpdbg.\n";
	exit( 1 );
}

use Wikimedia\MWConfig\ClusterConfig;

require_once __DIR__ . '/../src/ClusterConfig.php';
require_once __DIR__ . '/MWMultiVersion.php';

function wmfUsage() {
	global $argv;
	fwrite( STDERR, <<<EOT
Usage: php $argv[0] SCRIPT --wiki=WIKI <script args>

SCRIPT must be the relative (to MediaWiki) script file path.
If only a filename is given, it will be assumed to reside in /maintenance.

The --wiki option is required for most maintenance scripts.

EOT
	);
	exit( 1 );
}

/**
 * Waits for the mesh network to be available before allowing the script to be
 * executed.
 *
 * In some conditions, a script might be executed while the service mesh is
 * unavailable or unhealthy, which results in undefined behaviour for some
 * scripts. See T387208.
 *
 * The possibility of bypassing the check is offered for specific emergency situations
 * and/or to avoid checking for every single wiki in a recurring script like
 * foreachwiki.
 */
function wmfWaitForMesh() {
	global $wmgRealm;
	$skip = getenv( 'MESH_CHECK_SKIP' );
	// The service mesh is only used in production.
	// Furthermore, we're limiting ourselves to k8s-only. We will soon have completely dismissed the
	// non-k8s users of mwscript besides the deployment hosts.
	if ( $skip == "1" || $wmgRealm !== 'production' || !ClusterConfig::getInstance()->isK8s() ) {
		return;
	}

	$meshAvailable = false;

	$ch = curl_init();
	curl_setopt( $ch, CURLOPT_URL, 'http://localhost:9361/healthz' );
	// We set aggressive timeouts as it's localhost and it's a simple admin interface.
	// Random sampling in production yields sub-millisecond response times for this endpoint
	curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT_MS, 100 );
	curl_setopt( $ch, CURLOPT_TIMEOUT_MS, 200 );
	// We want to return the curl response and not display it.
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

	// We retry 20 times at increasing time intervals
	for ( $i = 0; $i < 20; $i++ ) {
		if ( curl_exec( $ch ) == "OK" && curl_getinfo( $ch, CURLINFO_HTTP_CODE ) == 200 ) {
			$meshAvailable = true;
			break;
		}
			// sleep 10 * i^2 milliseconds, so 10, 40, 90, 250... for a total
			// possible delay of 18 seconds
			usleep( 10000 * $i * $i );
	}

	curl_close( $ch );

	if ( !$meshAvailable ) {
		fwrite( STDERR, <<<EOT
The service mesh is unavailable, which can lead to unexpected results.

Therefore, the script will not be executed. If you are *very* sure your script will
not need the service mesh at all, you can run it again with MESH_CHECK_SKIP=1
EOT
		);
		exit( 1 );
	}
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
function wmfGetMWScriptWithArgs() {
	global $argv;
	if ( count( $argv ) < 2 ) {
		wmfUsage();
	}

	// Security check -- don't allow scripts to run as privileged users
	$gids = posix_getgroups();
	foreach ( $gids as $gid ) {
		$info = posix_getgrgid( $gid );
		if ( $info && in_array( $info['name'], [ 'sudo', 'wikidev', 'root' ] ) ) {
			fwrite( STDERR, "Cannot run a MediaWiki script as a user in the " .
				"group {$info['name']}\n" );
			fwrite( STDERR, <<<EOT
Maintenance scripts should generally be run using sudo -u www-data which
is available to all wikidev users.  Running a maintenance script as a
privileged user risks compromise of the user account.

You should run this script as the www-data user:

 sudo -u www-data <command>

EOT
			);
			exit( 1 );
		}
	}

	// The script file to run
	$relPath = $argv[1];
	// If no MW directory is given then assume this is a /maintenance script
	// run.php allows running maint scripts like Myextension:Foo
	if (
		strpos( $relPath, '/' ) === false &&
		strpos( $relPath, ':' ) === false &&
		substr_count( $relPath, "." ) < 2
	) {
		// convenience
		$relPath = "maintenance/$relPath";
	}

	// For some maintenance scripts we don't care what wiki DB is used...
	$wikilessScripts = [
		'maintenance/purgeList.php',
		'maintenance/purgeMessageBlobStore.php',
		'extensions/WikimediaMaintenance/dumpInterwiki.php',
		'extensions/WikimediaMaintenance/getJobQueueLengths.php',
		'extensions/WikimediaMaintenance/rebuildInterwiki.php',
		'extensions/WikimediaMaintenance/filebackend/setZoneAccess.php',
		'extensions/WikimediaMaintenance/purgeUrls.php',
		'maintenance/mctest.php',
		'maintenance/mcc.php',
	];

	// maint scripts using CommandLineInc and thus can't use run.php
	// mergeMessageFileList uses the new way but it's quite unusual as
	// it does a lot after class so it has to use the old way.
	$oldScripts = [
		'maintenance/mergeMessageFileList.php',
		'maintenance/storage/checkStorage.php',
		'maintenance/storage/recompressTracked.php',
		'maintenance/storage/testCompression.php',
		'maintenance/storage/trackBlobs.php',
		'extensions/WikimediaMaintenance/listDatabases.php',
		'extensions/WikimediaMaintenance/sanityCheck.php',
		'extensions/WikimediaMaintenance/storage/testRctComplete.php',
	];

	$scriptArgs = array_slice( $argv, 2 );

	// Dump argv if requested
	if ( ( $scriptArgs[0] ?? '' ) === '--mwscript-debug-dump-argv' ) {
		array_shift( $scriptArgs );
		$debugDump = true;
	} else {
		$debugDump = false;
	}

	// Add a --wiki param if none was given, if the script is wikiless
	// Maintenance.php will treat $argv[2] as the wiki if it doesn't start '-'
	if ( !count( $scriptArgs ) || !preg_match( '/^([^-]|--wiki(=|$))/', $scriptArgs[0] ) ) {
		if ( in_array( $relPath, $wikilessScripts ) ) {
			// Assume aawiki as Maintenance.php does.
			array_unshift( $scriptArgs, "--wiki=aawiki" );
		}
	}
	$useOld = in_array( $relPath, $oldScripts );
	$runPath = $useOld ? $relPath : 'maintenance/run.php';

	if ( $useOld ) {
		// Remove MWScript.php from $argv[0] and update $relPath
		$argv = array_merge( [ $relPath ], $scriptArgs );
	} else {
		// Replace MWScript.php in $argv[0] with run.php
		$argv = array_merge( [ $runPath, $relPath ], $scriptArgs );
	}

	$absPath = MWMultiVersion::getMediaWikiCli( $runPath, $useOld );
	if ( !file_exists( $absPath ) ) {
		fwrite( STDERR, "The MediaWiki script file \"{$absPath}\" does not exist.\n" );
		exit( 1 );
	}

	if ( !$useOld ) {
		// Use absolute path for run.php. argv[1] was updated by getMediaWikiCli.
		$argv[0] = $absPath;
	}

	if ( $debugDump ) {
		print "require $absPath\n";
		print_r( $argv );
		exit( 1 );
	}

	return $absPath;
}

// Run the script!
$scriptWithArgs = wmfGetMWScriptWithArgs();
wmfWaitForMesh();
require_once $scriptWithArgs;
