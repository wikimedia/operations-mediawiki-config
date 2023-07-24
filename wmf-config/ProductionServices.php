<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

# ProductionServices.php statically defines all service hostnames/ips
# for any service used by MediaWiki, divided by datacenter.
#
# This can be included on app servers even in contexts where MediaWiki is not
# initialised (for example, PhpAutoPrepend.php and /etc/php7/fatal-error.php).
#
# This MUST NOT assume any global variables or constants from MediaWiki, nor
# multiversion. Only plain PHP built-ins may be used.
#
# This for PRODUCTION.
#
# Effective load order:
# - *nothing*
# - wmf-config/ProductionServices.php [THIS FILE]
#
# Included from: ../src/ServiceConfig.php
#
# DO NOT ADD new services below without asking SRE to set up a service proxy for it first.
# See T244843 for the rationale. All proxies that are setup can be found at:
# operations/puppet.git:/hieradata/common/profile/services_proxy/envoy.yaml
#

// Inline comments are often used for noting the service associated with specific configuration
// and requiring comments to be on their own line would reduce readability for this file
// phpcs:disable MediaWiki.WhiteSpace.SpaceBeforeSingleLineComment.NewLineComment

$common = [
	// XHGui is the on-demand profiler, backed by MariaDB.
	// The username and password are set in PrivateSettings.php.
	// Set to null to disable XHGui.
	'xhgui-pdo' => 'mysql:host=m2-master.eqiad.wmnet;dbname=xhgui',

	// https://wikitech.wikimedia.org/wiki/WikimediaDebug#Request_profiling
	'excimer-ui-url' => 'https://performance.wikimedia.org/excimer/',
	'excimer-ui-server' => 'https://performance.discovery.wmnet/excimer/',

	// ArcLamp (formerly known as Xenon) is the sampling profiler
	// pipeline.  Frames from the Excimer extension will be sent to
	// Redis on this host.
	//
	// Profile collection is not active-active (but is consumed by
	// pipelines in both data centers).
	'xenon' => '10.64.16.88', # arclamp1001.eqiad.wmnet

	// Statsd is not active-active.
	'statsd' => '10.64.16.81', # statsd.eqiad.wmnet, now resolving to graphite1005.eqiad.wmnet

	// Logstash is not active-active.
	'logstash' => [
		'10.2.2.36', # logstash.svc.eqiad.wmnet
	],

	// IRC (broadcast RCFeed for irc.wikimedia.org)
	// Not active-active.
	'irc' => [
		'208.80.155.105', # irc1001.wikimedia.org
		'208.80.155.120', # irc1002.wikimedia.org
		'208.80.153.62',  # irc2001.wikimedia.org
		'208.80.153.73',  # irc2002.wikimedia.org
	],

	// Automatic dc-local discovery
	'parsoid' => 'http://localhost:6002/w/rest.php',
	'mathoid' => 'http://localhost:6003',
	'eventgate-analytics' => 'http://localhost:6004',
	'eventgate-analytics-external' => 'http://localhost:6013',
	'eventgate-main' => 'http://localhost:6005',
	'cxserver' => 'http://localhost:6015',
	'restbase' => 'http://localhost:6011',
	'sessionstore' => 'http://localhost:6006',
	'echostore' => 'http://localhost:6007',
	'push-notifications' => 'http://localhost:6012',
	'image-suggestion' => 'http://localhost:6030',
	'linkrecommendation' => 'http://localhost:6029',
	'shellbox' => 'http://localhost:6024',
	'shellbox-constraints' => 'http://localhost:6025',
	'shellbox-media' => 'http://localhost:6026',
	'shellbox-syntaxhighlight' => 'http://localhost:6027',
	'shellbox-timeline' => 'http://localhost:6028',
	// Points back to MediaWiki for $wgLocalHTTPProxy
	'mwapi' => 'http://localhost:6501',

	// cloudelastic only exists in eqiad.

	'cloudelastic-chi' => [
		[ // forwarded to https://cloudelastic.wikimedia.org:9243/
			'host' => 'localhost',
			'transport' => 'Http',
			'port' => 6105,
		],
	],
	'cloudelastic-omega' => [
		[ // forwarded to https://cloudelastic.wikimedia.org:9443/
			'host' => 'localhost',
			'transport' => 'Http',
			'port' => 6106,
		],
	],
	'cloudelastic-psi' => [
		[ // forwarded to https://cloudelastic.wikimedia.org:9643/
			'host' => 'localhost',
			'transport' => 'Http',
			'port' => 6107,
		],
	],

	// Wikifunctions back-end service; currently in Beta Cluster only.
	'wikifunctions-orchestrator' => 'NOT YET DEFINED',
];

$services = [
	'eqiad' => $common + [
		// each DC has its own urldownloader for latency reasons
		'urldownloader' => 'http://url-downloader.eqiad.wikimedia.org:8080',

		// logs are mirrored from eqiad -> codfw by mwlog hosts
		'udp2log' => '10.64.32.141:8420', # mwlog1002.eqiad.wmnet

		'upload' => 'ms-fe.svc.eqiad.wmnet',
		'mediaSwiftAuth' => 'https://ms-fe.svc.eqiad.wmnet/auth',
		'mediaSwiftStore' => 'https://ms-fe.svc.eqiad.wmnet/v1/AUTH_mw',

		'etcd' => [
			'host' => '_etcd-client-ssl._tcp.eqiad.wmnet',
			'protocol' => 'https'
		],

		'poolcounter' => [
			'10.64.0.151', # poolcounter1004.eqiad.wmnet
			'10.64.32.236', # poolcounter1005.eqiad.wmnet
		],

		// eqiad parsercache
		'parsercache-dbs' => [
			'pc1' => '10.64.0.57',   # pc1011, A1 8.8TB 512GB # pc1
			'pc2' => '10.64.16.65',  # pc1012, B1 8.8TB 512GB # pc2
			'pc3' => '10.64.32.163', # pc1013, C5 8.8TB 512GB # pc3
			# spare: '10.64.48.89',  # pc1014, D6 8.8TB 512GB
			# Use spare(s) to replace any of the above if needed
		],

		// LockManager Redis eqiad
		// This was hosted on redis_sessions which was phased out (T267581)
		// while now it is hosted on redis_misc (rdb* servers)
		'redis_lock' => [
			'rdb1' => '10.64.16.76:6381', # rdb1009 B8
			'rdb2' => '10.64.16.76:6382', # rdb1009 B8
			'rdb3' => '10.64.0.36:6381',  # rdb1011 A1
		],
		'search-chi' => [
			[ // forwarded to https://search.svc.eqiad.wmnet:9243/
				'host' => 'localhost',
				'transport' => CirrusSearch\Elastica\DeprecationLoggedHttp::class,
				'port' => 6102,
			]
		],
		'search-omega' => [
			[ // forwarded to https://search.svc.eqiad.wmnet:9443/
				'host' => 'localhost',
				'transport' => CirrusSearch\Elastica\DeprecationLoggedHttp::class,
				'port' => 6103,
			]
		],
		'search-psi' => [
			[ // forwarded to https://search.svc.eqiad.wmnet:9643/
				'host' => 'localhost',
				'transport' => CirrusSearch\Elastica\DeprecationLoggedHttp::class,
				'port' => 6104,
			]
		],
	],
	'codfw' => $common + [
		'urldownloader' => 'http://url-downloader.codfw.wikimedia.org:8080',

		// logs are mirrored from codfw -> eqiad by mwlog hosts
		'udp2log' => '10.192.32.9:8420', # mwlog2002.codfw.wmnet

		'upload' => 'ms-fe.svc.codfw.wmnet',
		'mediaSwiftAuth' => 'https://ms-fe.svc.codfw.wmnet/auth',
		'mediaSwiftStore' => 'https://ms-fe.svc.codfw.wmnet/v1/AUTH_mw',

		'etcd' => [
			'host' => '_etcd._tcp.codfw.wmnet',
			'protocol' => 'https'
		],

		'poolcounter' => [
			'10.192.0.132', # poolcounter2003.codfw.wmnet
			'10.192.16.129', # poolcounter2004.codfw.wmnet
		],

		// codfw parsercache
		'parsercache-dbs' => [
			'pc1' => '10.192.0.72',   # pc2011, A5 8.8TB 512GB # pc1
			'pc2' => '10.192.16.55',  # pc2012, B5 8.8TB 512GB # pc2
			'pc3' => '10.192.32.57',  # pc2013, C1 8.8TB 512GB # pc3
			# spare: '10.192.48.52',  # pc2014, D1 8.8TB 512GB
			# Use spare(s) to replace any of the above if needed
		],
		// LockManager Redis codfw
		'redis_lock' => [
			'rdb1' => '10.192.0.198:6381', # rdb2007 A5
			'rdb2' => '10.192.0.198:6382', # rdb2007 A5
			'rdb3' => '10.192.32.8:6381',  # rdb2009 C3
		],
		'search-chi' => [
			[ // forwarded to https://search.svc.codfw.wmnet:9243/
				'host' => 'localhost',
				'transport' => 'Http',
				'port' => 6202,
			]
		],
		'search-omega' => [
			[ // forwarded to https://search.svc.codfw.wmnet:9443/
				'host' => 'localhost',
				'transport' => 'Http',
				'port' => 6203,
			]
		],
		'search-psi' => [
			[ // forwarded to https://search.svc.codfw.wmnet:9643/
				'host' => 'localhost',
				'transport' => 'Http',
				'port' => 6204,
			]
		],
	],
];
unset( $common );
return $services;
