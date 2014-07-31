<?php
# HTCP multicast squid purging
$wgHTCPRouting = array(
	'' => array(
		'host' => '239.128.0.112',
		'port' => 4827
	)
);
$wgHTCPMulticastTTL = 8;

# Accept XFF from these proxies
$wgSquidServersNoPurge = array(
	# Note: the general idea here is to cover infrastructure space
	# where e.g. Varnish and SSL servers could be located, but exclude
	# other misc subnets (e.g. labs, analytics).

	## eqiad
	'208.80.154.0/26',	# public1-a-eqiad
	'2620:0:861:1::/64',	# public1-a-eqiad
	'208.80.154.128/26',	# public1-b-eqiad
	'2620:0:861:2::/64',	# public1-b-eqiad
	'208.80.154.64/26',	# public1-c-eqiad
	'2620:0:861:3::/64',	# public1-c-eqiad
	'208.80.155.96/27',	# public1-d-eqiad
	'2620:0:861:4::/64',	# public1-d-eqiad
	'10.64.0.0/22',		# private1-a-eqiad
	'2620:0:861:101::/64',	# private1-a-eqiad
	'10.64.16.0/22',	# private1-b-eqiad
	'2620:0:861:102::/64',	# private1-b-eqiad
	'10.64.32.0/22',	# private1-c-eqiad
	'2620:0:861:103::/64',	# private1-c-eqiad
	'10.64.48.0/22',	# private1-d-eqiad
	'2620:0:861:107::/64',	# private1-d-eqiad

	## esams
	'91.198.174.0/25',	# public1-esams
	'2620:0:862:1::/64',	# public1-esams
	'10.20.0.0/24',		# private1-esams
	'2620:0:862:102::/64',	# private1-esams

	## ulsfo
	'10.128.0.0/24',	# private1-ulsfo
	'2620:0:863:101::/64',	# private1-ulsfo
	'10.2.4.26',		# mobile.svc.ulsfo.wmnet, appears in XFF
);

# IP addresses that aren't proxies, regardless of what the other sources might say
$wgProxyWhitelist = array(
	'68.124.59.186',
	'202.63.61.242',
	'62.214.230.86',
	'217.94.171.96',
);
