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
	// To update this data, use contrib/generate-reverse-proxy.py
	// with an up-to-date Puppet clone.

	## eqiad
	'10.64.0.0/22', # private1-a-eqiad
	'2620:0:861:101::/64', # private1-a-eqiad
	'10.64.16.0/22', # private1-b-eqiad
	'2620:0:861:102::/64', # private1-b-eqiad
	'10.64.32.0/22', # private1-c-eqiad
	'2620:0:861:103::/64', # private1-c-eqiad
	'10.64.133.0/24', # private1-c2-eqiad
	'2620:0:861:10c::/64', # private1-c2-eqiad
	'10.64.141.0/24', # private1-c3-eqiad
	'2620:0:861:113::/64', # private1-c3-eqiad
	'10.64.169.0/24', # private1-c4-eqiad
	'2620:0:861:119::/64', # private1-c4-eqiad
	'10.64.171.0/24', # private1-c5-eqiad
	'2620:0:861:131::/64', # private1-c5-eqiad
	'10.64.173.0/24', # private1-c6-eqiad
	'2620:0:861:133::/64', # private1-c6-eqiad
	'10.64.175.0/24', # private1-c7-eqiad
	'2620:0:861:135::/64', # private1-c7-eqiad
	'10.64.48.0/22', # private1-d-eqiad
	'2620:0:861:107::/64', # private1-d-eqiad
	'10.64.177.0/24', # private1-d1-eqiad
	'2620:0:861:137::/64', # private1-d1-eqiad
	'10.64.179.0/24', # private1-d2-eqiad
	'2620:0:861:139::/64', # private1-d2-eqiad
	'10.64.181.0/24', # private1-d3-eqiad
	'2620:0:861:13b::/64', # private1-d3-eqiad
	'10.64.183.0/24', # private1-d4-eqiad
	'2620:0:861:13d::/64', # private1-d4-eqiad
	'10.64.185.0/24', # private1-d6-eqiad
	'2620:0:861:13f::/64', # private1-d6-eqiad
	'10.64.187.0/24', # private1-d7-eqiad
	'2620:0:861:142::/64', # private1-d7-eqiad
	'10.64.189.0/24', # private1-d8-eqiad
	'2620:0:861:144::/64', # private1-d8-eqiad
	'10.64.130.0/24', # private1-e1-eqiad
	'2620:0:861:109::/64', # private1-e1-eqiad
	'10.64.131.0/24', # private1-e2-eqiad
	'2620:0:861:10a::/64', # private1-e2-eqiad
	'10.64.132.0/24', # private1-e3-eqiad
	'2620:0:861:10b::/64', # private1-e3-eqiad
	'10.64.152.0/24', # private1-e5-eqiad
	'2620:0:861:120::/64', # private1-e5-eqiad
	'10.64.154.0/24', # private1-e6-eqiad
	'2620:0:861:122::/64', # private1-e6-eqiad
	'10.64.156.0/24', # private1-e7-eqiad
	'2620:0:861:124::/64', # private1-e7-eqiad
	'10.64.158.0/24', # private1-e8-eqiad
	'2620:0:861:126::/64', # private1-e8-eqiad
	'10.64.134.0/24', # private1-f1-eqiad
	'2620:0:861:10d::/64', # private1-f1-eqiad
	'10.64.135.0/24', # private1-f2-eqiad
	'2620:0:861:10e::/64', # private1-f2-eqiad
	'10.64.136.0/24', # private1-f3-eqiad
	'2620:0:861:10f::/64', # private1-f3-eqiad
	'10.64.160.0/24', # private1-f5-eqiad
	'2620:0:861:128::/64', # private1-f5-eqiad
	'10.64.162.0/24', # private1-f6-eqiad
	'2620:0:861:12a::/64', # private1-f6-eqiad
	'10.64.164.0/24', # private1-f7-eqiad
	'2620:0:861:12c::/64', # private1-f7-eqiad
	'10.64.166.0/24', # private1-f8-eqiad
	'2620:0:861:12e::/64', # private1-f8-eqiad

	## codfw
	'10.192.0.0/22', # private1-a-codfw
	'2620:0:860:101::/64', # private1-a-codfw
	'10.192.23.0/24', # private1-a2-codfw
	'2620:0:860:113::/64', # private1-a2-codfw
	'10.192.5.0/24', # private1-a3-codfw
	'2620:0:860:106::/64', # private1-a3-codfw
	'10.192.6.0/24', # private1-a4-codfw
	'2620:0:860:107::/64', # private1-a4-codfw
	'10.192.7.0/24', # private1-a5-codfw
	'2620:0:860:108::/64', # private1-a5-codfw
	'10.192.8.0/24', # private1-a6-codfw
	'2620:0:860:109::/64', # private1-a6-codfw
	'10.192.9.0/24', # private1-a7-codfw
	'2620:0:860:10a::/64', # private1-a7-codfw
	'10.192.10.0/24', # private1-a8-codfw
	'2620:0:860:10b::/64', # private1-a8-codfw
	'10.192.16.0/22', # private1-b-codfw
	'2620:0:860:102::/64', # private1-b-codfw
	'10.192.11.0/24', # private1-b2-codfw
	'2620:0:860:10c::/64', # private1-b2-codfw
	'10.192.12.0/24', # private1-b3-codfw
	'2620:0:860:10d::/64', # private1-b3-codfw
	'10.192.13.0/24', # private1-b4-codfw
	'2620:0:860:10e::/64', # private1-b4-codfw
	'10.192.14.0/24', # private1-b5-codfw
	'2620:0:860:10f::/64', # private1-b5-codfw
	'10.192.15.0/24', # private1-b6-codfw
	'2620:0:860:110::/64', # private1-b6-codfw
	'10.192.21.0/24', # private1-b7-codfw
	'2620:0:860:111::/64', # private1-b7-codfw
	'10.192.22.0/24', # private1-b8-codfw
	'2620:0:860:112::/64', # private1-b8-codfw
	'10.192.32.0/22', # private1-c-codfw
	'2620:0:860:103::/64', # private1-c-codfw
	'10.192.4.0/24', # private1-c1-codfw
	'2620:0:860:100::/64', # private1-c1-codfw
	'10.192.26.0/24', # private1-c2-codfw
	'2620:0:860:105::/64', # private1-c2-codfw
	'10.192.27.0/24', # private1-c3-codfw
	'2620:0:860:114::/64', # private1-c3-codfw
	'10.192.28.0/24', # private1-c4-codfw
	'2620:0:860:115::/64', # private1-c4-codfw
	'10.192.29.0/24', # private1-c5-codfw
	'2620:0:860:116::/64', # private1-c5-codfw
	'10.192.30.0/24', # private1-c6-codfw
	'2620:0:860:119::/64', # private1-c6-codfw
	'10.192.31.0/24', # private1-c7-codfw
	'2620:0:860:11a::/64', # private1-c7-codfw
	'10.192.48.0/22', # private1-d-codfw
	'2620:0:860:104::/64', # private1-d-codfw
	'10.192.36.0/24', # private1-d1-codfw
	'2620:0:860:11b::/64', # private1-d1-codfw
	'10.192.37.0/24', # private1-d2-codfw
	'2620:0:860:11c::/64', # private1-d2-codfw
	'10.192.38.0/24', # private1-d3-codfw
	'2620:0:860:11d::/64', # private1-d3-codfw
	'10.192.39.0/24', # private1-d4-codfw
	'2620:0:860:11e::/64', # private1-d4-codfw
	'10.192.40.0/24', # private1-d5-codfw
	'2620:0:860:11f::/64', # private1-d5-codfw
	'10.192.41.0/24', # private1-d6-codfw
	'2620:0:860:120::/64', # private1-d6-codfw
	'10.192.42.0/24', # private1-d7-codfw
	'2620:0:860:121::/64', # private1-d7-codfw
	'10.192.43.0/24', # private1-d8-codfw
	'2620:0:860:122::/64', # private1-d8-codfw
	'10.192.56.0/24', # private1-e1-codfw
	'2620:0:860:12b::/64', # private1-e1-codfw
	'10.192.44.0/24', # private1-e2-codfw
	'2620:0:860:123::/64', # private1-e2-codfw
	'10.192.57.0/24', # private1-e3-codfw
	'2620:0:860:12c::/64', # private1-e3-codfw
	'10.192.45.0/24', # private1-e4-codfw
	'2620:0:860:124::/64', # private1-e4-codfw
	'10.192.46.0/24', # private1-e5-codfw
	'2620:0:860:125::/64', # private1-e5-codfw
	'10.192.58.0/24', # private1-f1-codfw
	'2620:0:860:12d::/64', # private1-f1-codfw
	'10.192.47.0/24', # private1-f2-codfw
	'2620:0:860:126::/64', # private1-f2-codfw
	'10.192.59.0/24', # private1-f3-codfw
	'2620:0:860:12e::/64', # private1-f3-codfw
	'10.192.52.0/24', # private1-f4-codfw
	'2620:0:860:127::/64', # private1-f4-codfw

	## esams
	'10.80.0.0/24', # private1-bw27-esams
	'2a02:ec80:300:101::/64', # private1-bw27-esams
	'10.80.1.0/24', # private1-by27-esams
	'2a02:ec80:300:102::/64', # private1-by27-esams

	## ulsfo
	'10.128.0.0/24', # private1-ulsfo
	'2620:0:863:101::/64', # private1-ulsfo

	## eqsin
	'10.132.0.0/24', # private1-eqsin
	'2001:df2:e500:101::/64', # private1-eqsin

	## drmrs
	'10.136.0.0/24', # private1-b12-drmrs
	'2a02:ec80:600:101::/64', # private1-b12-drmrs
	'10.136.1.0/24', # private1-b13-drmrs
	'2a02:ec80:600:102::/64', # private1-b13-drmrs

	## magru
	'10.140.0.0/24', # private1-b3-magru
	'2a02:ec80:700:101::/64', # private1-b3-magru
	'10.140.1.0/24', # private1-b4-magru
	'2a02:ec80:700:102::/64', # private1-b4-magru

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
