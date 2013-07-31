<?php
# our text squid in beta labs gets forwarded requests
# from the ssl terminator, to 127.0.0.1, so adding that
# address to the NoPurge list so that XFF headers for it
# will be stripped; purge requests should get to it
# on the address in the SquidServers list
$wgSquidServersNoPurge = array( '127.0.0.1',
	'10.4.1.133',  # deployment-cache-text1
);

# The beta cluster does not have multicast, hence we route the purge
# requests directly to the varnish instances.  They have varnishhtcpd listening
# on UDP port 4827.
$wgHTCPRouting = array(
	'|^https?://upload\.beta\.wmflabs\.org|' => array(
		'host' => '10.4.0.211',  # deployment-cache-upload04
		'port' => 4827,
	),

	# Fallback  (text+mobile)
	'' => array(
		array(
			'host' => '10.4.1.133',  # deployment-cache-text1
			'port' => 4827,
		),
		array(
			'host' => '10.4.1.82',  # deployment-cache-mobile01
			'port' => 4827,
		),
	),
);
