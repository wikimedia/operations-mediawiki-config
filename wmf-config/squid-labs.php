<?php

$wgSquidServers = array(
	'10.4.0.17',   # deployment-squid
	'10.4.0.51',   # deployment-cache-bits03
	'10.4.1.133',  # deployment-cache-text1
	'10.4.0.214',  # deployment-cache-upload03
);

# our text squid in beta labs gets forwarded requests
# from the ssl terminator, to 127.0.0.1, so adding that
# address to the NoPurge list so that XFF headers for it
# will be stripped; purge requests should get to it
# on the address in the SquidServers list
$wgSquidServersNoPurge = array( '127.0.0.1' );

# The beta cluster does not have multicast, hence we route the purge
# requests directly to the varnish instances.  They have varnishhtcpd listening
# on UDP port 4827.
$wgHTCPMulticastRouting = array(
	'|^https?://upload\.beta\.wmflabs\.org|' => array(
		'host' => '10.4.0.214',  # deployment-cache-upload03
		'port' => 4827,
	),

	# Fallback??
	'' => array(
		'host' => '10.4.1.133',  # deployment-cache-text1
		'port' => 4827,
	),
);
