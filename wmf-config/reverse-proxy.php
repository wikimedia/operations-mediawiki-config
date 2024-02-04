<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

// Inline comments are used for noting the proxies that are associated with IP addresses
// and requiring comments to be on their own line would reduce readability for this file
// phpcs:disable MediaWiki.WhiteSpace.SpaceBeforeSingleLineComment.NewLineComment

# Accept XFF from these proxies
$wgCdnServersNoPurge = [
	// This list includes all hosts which are trusted by MediaWiki in
	// X-Forwarded-For headers, including the caches and appservers.
	// TODO: generate this from Puppet's network module or Netbox
	// directly.
	//
	// Only add private ranges here, public are not required *except* for
	// eqiad/codfw becaues of the cloudweb* hosts.

	## eqiad
	# public
	'208.80.154.0/26', # public1-a-eqiad
	'2620:0:861:1::/64', # public1-a-eqiad
	'208.80.154.128/26', # public1-b-eqiad
	'2620:0:861:2::/64', # public1-b-eqiad
	'208.80.154.64/26',	# public1-c-eqiad
	'2620:0:861:3::/64', # public1-c-eqiad
	'208.80.155.96/27',	# public1-d-eqiad
	'2620:0:861:4::/64', # public1-d-eqiad
	# private
	'10.64.0.0/22', # private1-a-eqiad
	'2620:0:861:101::/64', # private1-a-eqiad
	'10.64.16.0/22', # private1-b-eqiad
	'2620:0:861:102::/64', # private1-b-eqiad
	'10.64.32.0/22', # private1-c-eqiad
	'2620:0:861:103::/64', # private1-c-eqiad
	'10.64.48.0/22', # private1-d-eqiad
	'2620:0:861:107::/64', # private1-d-eqiad
	'10.64.130.0/24', # private1-e1-eqiad
	'2620:0:861:109::/64', # private1-e1-eqiad
	'10.64.131.0/24', # private1-e2-eqiad
	'2620:0:861:10a::/64', # private1-e2-eqiad
	'10.64.132.0/24', # private1-e3-eqiad
	'2620:0:861:10b::/64', # private1-e3-eqiad
	'10.64.133.0/24', # private1-e4-eqiad
	'2620:0:861:10c::/64', # private1-e4-eqiad
	'10.64.134.0/24', # private1-f1-eqiad
	'2620:0:861:10d::/64', # private1-f1-eqiad
	'10.64.135.0/24', # private1-f2-eqiad
	'2620:0:861:10e::/64', # private1-f2-eqiad
	'10.64.136.0/24', # private1-f3-eqiad
	'2620:0:861:10f::/64', # private1-f3-eqiad
	'10.64.137.0/24', # private1-f4-eqiad
	'2620:0:861:110::/64', # private1-f4-eqiad

	## codfw
	# public
	'208.80.153.0/27', # public1-a-codfw
	'2620:0:860:1::/64', # public1-a-codfw
	'208.80.153.32/27', # public1-b-codfw
	'2620:0:860:2::/64', # public1-b-codfw
	'208.80.153.64/27', # public1-c-codfw
	'2620:0:860:3::/64', # public1-c-codfw
	'208.80.153.96/27', # public1-d-codfw
	'2620:0:860:4::/64', # public1-d-codfw
	# private
	'10.192.0.0/22', # private1-a-codfw
	'2620:0:860:101::/64', # private1-a-codfw
	'10.192.16.0/22', # private1-b-codfw
	'2620:0:860:102::/64', # private1-b-codfw
	'10.192.32.0/22', # private1-c-codfw
	'2620:0:860:103::/64', # private1-c-codfw
	'10.192.48.0/22', # private1-d-codfw
	'2620:0:860:104::/64', # private1-d-codfw

	## esams
	'2a02:ec80:300:100::/56', # private esams aggregate
	'10.80.0.0/16', # esams private/mgmt

	## ulsfo
	'10.128.0.0/24', # private1-ulsfo
	'2620:0:863:101::/64', # private1-ulsfo

	## eqsin private1
	'10.132.0.0/24',
	'2001:df2:e500:101::/64',

	## drmrs
	'10.136.0.0/24', # private1-b12-drmrs
	'2a02:ec80:600:101::/64', # private1-b12-drmrs
	'10.136.1.0/24', # private1-b13-drmrs
	'2a02:ec80:600:102::/64', # private1-b13-drmrs
];

# IP addresses that aren't proxies, regardless of what the other sources might say
$wgProxyWhitelist = [
	'68.124.59.186',
	'202.63.61.242',
	'62.214.230.86',
	'217.94.171.96',
];

# Secondary purges to deal with DB replication lag
$wgCdnReboundPurgeDelay = 11; // should be safely more than $wgLBFactoryConf 'max lag'
