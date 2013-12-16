<?php

// tweak to logs
//

if ( $wgCommandLineMode || PHP_SAPI == 'cli' ) {
	$wgDebugLogFile = "udp://$wmgUdp2logDest/cli";
} else {
	$wgDebugLogFile = "udp://$wmgUdp2logDest/web";
}

// udp2log logging for beta:
$wgDebugLogGroups['dnsblacklist'] = "udp://$wmgUdp2logDest/dnsblacklist";
$wgDebugLogGroups['exception-json'] = "udp://$wmgUdp2logDest/exception-json";
$wgDebugLogGroups['mwsearch'] = "udp://$wmgUdp2logDest/mwsearch";
$wgDebugLogGroups['squid'] = "udp://$wmgUdp2logDest/squid";

$wgUDPProfilerHost = 'deployment-fluoride.pmtpa.wmflabs';  // OL, 2013-11-14
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

