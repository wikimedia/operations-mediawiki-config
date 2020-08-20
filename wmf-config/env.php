<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.
#
# env.php statically exports the current realm and datacenter names.
#
# - This file MUST NOT have any side effects when included.
# - This file MAY may included in scripts on app servers before MediaWiki
#   is initialised. For example, fatal-error.php and profiler.php.
# - This file MUST NOT use any predefined state, only plain PHP.
#
# This both for PRODUCTION and for Beta Cluster.
#
# For MediaWiki, this is included in ../src/ServiceConfig.php
#

@$cluster = trim( file_get_contents( '/etc/wikimedia-cluster' ) ?: 'no-cluster-configured!' );
if ( $cluster === 'labs' ) {
	return [
		'realm' => 'labs',
		'dc' => 'eqiad',
	];
}

return [
	'realm' => 'production',
	'dc' => $cluster,
];
