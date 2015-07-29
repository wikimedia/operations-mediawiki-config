<?php
# our text squid in beta labs gets forwarded requests
# from the ssl terminator, to 127.0.0.1, so adding that
# address to the NoPurge list so that XFF headers for it
# will be stripped; purge requests should get to it
# on the address in the SquidServers list

# The beta cluster does not have multicast, hence we route the purge
# requests directly to the varnish instances.  They have varnishhtcpd listening
# on UDP port 4827.

$wgSquidServersNoPurge = array( '127.0.0.1',
	'10.68.18.103',  # deployment-cache-text04
	'10.68.17.51',  # deployment-cache-upload02
	'10.68.16.12',  # deployment-cache-bits01
	'10.68.16.13',  # deployment-cache-mobile03
);
$wgHTCPRouting = array(
	'|^https?://upload\.beta\.wmflabs\.org|' => array(
		'host' => '10.68.17.51',  # deployment-cache-upload02
		'port' => 4827,
	),

	# Fallback  (text+mobile)
	'' => array(
		array(
			'host' => '10.68.18.103',  # deployment-cache-text04
			'port' => 4827,
		),
		array(
			'host' => '10.68.16.13',  # deployment-cache-mobile03
			'port' => 4827,
		),
	),
);
