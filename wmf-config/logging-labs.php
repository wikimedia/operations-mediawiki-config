<?php

// tweak to logs
//

if ( $wgCommandLineMode || PHP_SAPI == 'cli' ) {
	$wgDebugLogFile = "udp://$wmfUdp2logDest/cli";
} else {
	$wgDebugLogFile = "udp://$wmfUdp2logDest/web";
}

// udp2log logging for beta:
$wgDebugLogGroups['CentralAuthVerbose'] = "udp://$wmfUdp2logDest/centralauth";
$wgDebugLogGroups['dnsblacklist'] = "udp://$wmfUdp2logDest/dnsblacklist";
$wgDebugLogGroups['exception-json'] = "udp://$wmfUdp2logDest/exception-json";
$wgDebugLogGroups['mwsearch'] = "udp://$wmfUdp2logDest/mwsearch";
$wgDebugLogGroups['squid'] = "udp://$wmfUdp2logDest/squid";

$wgUDPProfilerHost = 'deployment-fluoride.eqiad.wmflabs';  // OL, 2013-11-14
$wgAggregateStatsID = "$wgVersion-labs";

// Ugly code to create a random hash and put it in logs
// temporary --Petrb
$randomHash = $wgDBname . '-' . substr( md5(uniqid()), 0, 8 );
function insertToken($debug) {
	global $wgOut;
	$wgOut->addHTML( "<!-- Debug token: $randomHash //-->");
}
#insertToken($randomHash);
if ( $_SERVER['SCRIPT_NAME'] != "/w/index.php" ) {
	// skip
} else {
# insertToken($randomHash);
#	echo "<!-- Debug token: $randomHash $script //-->";
}

$wgDebugLogPrefix = $randomHash . ": ";

