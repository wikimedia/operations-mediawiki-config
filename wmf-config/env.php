<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.
#
# This file returns an array with the following keys:
#
# - realm:         The name of the realm.
# - dc:            The name of the local datacenter.
# - dcs:           A list of all datacenters within the realm.
# - servicesFile:  The path of the PHP file containing the realm's service definitions.
#
# This file is for PRODUCTION, and all other realms.
#
# - This file MUST NOT have any side effects when included.
# - This file MAY may be included in scripts on app servers before MediaWiki
#   is initialised. For example, PhpAutoPrepend.php and profiler.php.
# - This file MUST NOT use any predefined state, only plain PHP.
#
# For MediaWiki, this file is included via ../src/ServiceConfig.php.

// In PRODUCTION, /etc/wikimedia-cluster contains the name
// of the local datacenter (e.g. eqiad, codfw, etc).  For any other
// realms (e.g., labs), /etc/wikimedia-cluster contains the name of the realm.

// For mediawiki container image builds, WMF_DATACENTER will be set to
// 'eqiad' by the image building script.
$dc = getenv( 'WMF_DATACENTER' );
if ( !$dc ) {
	$dcFile = $GLOBALS['mockWmgClusterFile'] ?? '/etc/wikimedia-cluster';
	@$dc = trim( file_get_contents( $dcFile ) ?: 'no-cluster-configured!' );
}

if ( $dc === 'labs' ) {
	return [
		'realm' => 'labs',
		'dc' => 'eqiad',
		'dcs' => [ 'eqiad' ],
		'servicesFile' => __DIR__ . '/LabsServices.php',
	];
}

return [
	'realm' => 'production',
	'dc' => $dc,
	'dcs' => [ 'eqiad', 'codfw' ],
	'servicesFile' => __DIR__ . '/ProductionServices.php',
];
