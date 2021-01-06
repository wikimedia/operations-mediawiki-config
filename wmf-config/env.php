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
$mockWmgClusterFile = $GLOBALS['mockWmgClusterFile'] ?? '/etc/wikimedia-cluster';

@$cluster = trim( file_get_contents( $mockWmgClusterFile ) ?: 'no-cluster-configured!' );
if ( $cluster === 'labs' ) {
	return [
		'realm' => 'labs',
		'dc' => 'eqiad',
		'dcs' => [ 'eqiad' ],
		'servicesFile' => __DIR__ . '/LabsServices.php',
	];
}
if ( $cluster === 'dev' ) {
	return [
		'realm' => 'dev',
		'dc' => 'dev',
		'dcs' => [ 'dev' ],
		'servicesFile' => __DIR__ . '/DevServices.php',
	];
}

return [
	'realm' => 'production',
	'dc' => $cluster,
	'dcs' => [ 'eqiad', 'codfw' ],
	'servicesFile' => __DIR__ . '/ProductionServices.php',
];
