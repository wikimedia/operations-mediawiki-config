<?php
# WARNING: This file is publically viewable on the web. Do not put private data here.
/*
 * Before altering the wgMemCachedServers array below, make sure you planned
 * your change. Memcached compute a hash of the data and given the hash
 * assign the value to one of the servers.
 * If you remove / comment / change order of servers, the hash will miss
 * and that can result in bad performance for the cluster !
 *
 * Hashar, based on dammit comments. Nov 28 2005.
 *
 */
$wgMainCacheType = CACHE_MEMCACHED;
if ( $wgDBname === 'testwiki' || $wgDBname === 'test2wiki' || $wgDBname === 'dewiki' || $wgDBname === 'zhwiki' ) { // temporary hack
	$wgMainCacheType = 'memcached-multiwrite';
}

$wgMemCachedPersistent = false;
$wgUseMemCached = true;
$wgMemCachedTimeout = 250000; # default is 100000
$wgMemCachedInstanceSize = 2000;

# Newer "mc*" servers (only use the pecl client with these).
# This does not use the "slot" system like the old setup, but
# rather a consistent hash based on key and server addresses,
# so the ordering of servers is not important. Additionally, the
# number of servers can grow/shrink without *too* much disruption.
$wgObjectCaches['memcached-pecl'] = array(
	'class'      => 'MemcachedPeclBagOStuff',
	'serializer' => 'igbinary',
	'servers'    => array(
		'10.0.12.1:11211',
		'10.0.12.2:11211',
		'10.0.12.3:11211',
		'10.0.12.4:11211',
		'10.0.12.5:11211',
		'10.0.12.6:11211',
		'10.0.12.7:11211',
		'10.0.12.8:11211',
		'10.0.12.9:11211',
		'10.0.12.10:11211',
		'10.0.12.11:11211',
		'10.0.12.12:11211',
		'10.0.12.13:11211',
		'10.0.12.14:11211',
		'10.0.12.15:11211',
		'10.0.12.16:11211',
	)
);

# Writes to both apache and mc* caches
$wgObjectCaches['memcached-multiwrite'] = array(
	'class'  => 'MultiWriteBagOStuff',
	'caches' => array(
		0 => $wgObjectCaches['memcached-pecl'] // mc* servers
		1 => array( 'factory' => 'ObjectCache::newMemcached' ), // apaches
	)
);

$wgMemCachedServers = array(
    # ACTIVE LIST
    # PLEASE MOVE SERVERS TO THE DOWN LIST RATHER THAN COMMENTING THEM OUT IN-PLACE
    # If a server goes down, you must replace its slot with another server
    # You can take a server from the spare list
    #
    # Please read http://wikitech.wikimedia.org/view/Memcached for more context

    # 2008 01 09 revision -- domas
    # 79 active nodes (a nice prime number), 7 spares

# SLOT      HOST
0 => '10.0.2.245:11000',
1 => '10.0.8.19:11000',
2 => '10.0.8.17:11000',
3 => '10.0.2.227:11000',
4 => '10.0.8.21:11000',
5 => '10.0.2.239:11000',
6 => '10.0.8.20:11000',
7 => '10.0.8.6:11000',
8 => '10.0.2.250:11000',
9 => '10.0.11.31:11000',
10 => '10.0.8.12:11000',
11 => '10.0.11.48:11000',
12 => '10.0.2.238:11000',
13 => '10.0.2.248:11000',
14 => '10.0.11.26:11000',
15 => '10.0.11.41:11000',
16 => '10.0.8.22:11000',
17 => '10.0.2.249:11000',
18 => '10.0.11.40:11000',
19 => '10.0.11.24:11000',
20 => '10.0.11.47:11000',
21 => '10.0.8.37:11000',
22 => '10.0.11.38:11000',
23 => '10.0.11.25:11000',
24 => '10.0.11.27:11000',
25 => '10.0.11.46:11000',
26 => '10.0.2.213:11000',
27 => '10.0.2.204:11000',
28 => '10.0.2.240:11000',
29 => '10.0.2.244:11000',
30 => '10.0.8.8:11000',
31 => '10.0.8.15:11000',
32 => '10.0.11.44:11000',
33 => '10.0.11.37:11000',
34 => '10.0.8.14:11000',
35 => '10.0.2.209:11000',
36 => '10.0.11.36:11000',
37 => '10.0.2.236:11000',
38 => '10.0.11.34:11000',
39 => '10.0.11.35:11000',
40 => '10.0.2.191:11000',
41 => '10.0.2.234:11000',
42 => '10.0.2.233:11000',
43 => '10.0.2.230:11000',
44 => '10.0.11.33:11000',
45 => '10.0.8.36:11000',
46 => '10.0.11.32:11000',
47 => '10.0.2.235:11000',
48 => '10.0.2.232:11000',
49 => '10.0.11.30:11000',
50 => '10.0.11.42:11000',
51 => '10.0.8.11:11000',
52 => '10.0.2.192:11000',
53 => '10.0.8.10:11000',
54 => '10.0.11.29:11000',
55 => '10.0.2.202:11000',
56 => '10.0.11.49:11000',
57 => '10.0.2.229:11000',
58 => '10.0.8.24:11000',
59 => '10.0.8.25:11000',
60 => '10.0.2.228:11000',
61 => '10.0.8.26:11000',
62 => '10.0.8.27:11000',
63 => '10.0.8.39:11000',
64 => '10.0.2.241:11000',
65 => '10.0.8.9:11000',
66 => '10.0.8.13:11000',
67 => '10.0.11.45:11000',
68 => '10.0.2.201:11000',
69 => '10.0.8.29:11000',
70 => '10.0.8.30:11000',
71 => '10.0.8.38:11000',
72 => '10.0.11.28:11000',
73 => '10.0.8.33:11000',
74 => '10.0.2.207:11000',
75 => '10.0.8.35:11000',
76 => '10.0.8.32:11000',
77 => '10.0.11.43:11000',
78 => '10.0.2.205:11000',

/**** SERVERS BEING ACTIVELY RELOCATED ****

/**** DOWN ****
XX => '10.0.2.2:11000',
XX => '10.0.8.31:11000',
XX => '10.0.11.39:11000',
XX => '10.0.2.231:11000',
XX => '10.0.8.16:11000',
XX => '10.0.2.206:11000',
XX => '10.0.2.251:11000',
XX => '10.0.2.203:11000',
0 => '10.0.8.18:11000',
9 => '10.0.2.208:11000',
XX => '10.0.2.237:11000',
13 => '10.0.2.212:11000',
40 => '10.0.2.211:11000',
72 => '10.0.2.226:11000',
56 => '10.0.8.23:11000',

***** SPARE ****

41 => '10.0.2.200:11000',
64 => '10.0.2.190:11000',
4 => '10.0.2.247:11000',
16 => '10.0.2.246:11000',
1 => '10.0.2.245:11000',
6 => '10.0.2.243:11000',
0 => '10.0.2.242:11000',
13 => '10.0.2.214:11000',
26 => '10.0.2.215:11000',
40 => '10.0.2.216:11000',
SS => '10.0.2.217:11000',
35 => '10.0.2.218:11000',
XX => '10.0.2.252:11000',
XX => '10.0.2.253:11000',

*************/

);

# vim: set sts=4 sw=4 et :
