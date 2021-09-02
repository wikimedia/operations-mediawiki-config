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

$common = [
	// XHGui is the on-demand profiler, backed by MariaDB.  The
	// username and password are set in PrivateSettings.php.
	'xhgui-pdo' => 'mysql:host=m2-master.eqiad.wmnet;dbname=xhgui',

	// This refers to the old, MongoDB-based XHGui backend, which
	// has been replaced by xhgui-pdo (T180761).
	'xhgui' => null,

	// ArcLamp (formerly known as Xenon) is the sampling profiler
	// pipeline.  Frames from the Excimer extension will be sent to
	// Redis on this host.
	//
	// Profile collection is not active-active (but is consumed by
	// pipelines in both data centers).
	'xenon' => '10.64.32.141', # mwlog1002.eqiad.wmnet

	// Statsd is not active-active.
	'statsd' => '10.64.16.149', # statsd.eqiad.wmnet, now resolving to graphite1004.eqiad.wmnet

	// EventLogging is not active-active.
	'eventlogging' => 'udp://10.64.32.167:8421', # eventlog1001.eqiad.wmnet

	// Logstash is not active-active.
	'logstash' => [
		'10.2.2.36', # logstash.svc.eqiad.wmnet
	],

	// IRC (broadcast RCFeed for irc.wikimedia.org)
	// Not active-active.
	'irc' => [
		'208.80.155.105', # irc1001.wikimedia.org
		'208.80.153.62',  # irc2001.wikimedia.org
	],

	// Automatic dc-local discovery
	'parsoid' => 'http://localhost:6002/w/rest.php',
	'mathoid' => 'http://localhost:6003',
	'eventgate-analytics' => 'http://localhost:6004',
	'eventgate-analytics-external' => 'http://localhost:6013',
	'eventgate-main' => 'http://localhost:6005',
	'cxserver' => 'http://localhost:6015',
	'electron' => 'http://pdfrender.discovery.wmnet:5252',
	'restbase' => 'http://localhost:6011',
	'sessionstore' => 'http://localhost:6006',
	'echostore' => 'http://localhost:6007',
	'push-notifications' => 'http://localhost:6012',
	'linkrecommendation' => 'https://linkrecommendation.discovery.wmnet:4005',
	'shellbox' => 'http://localhost:6024',
	'shellbox-constraints' => 'http://localhost:6025',

	// cloudelastic only exists in eqiad. No load balancer is available due to
	// the part of the network that they live in so each host is enumerated

	// WARNING: psi and omega have their ports "mixed up", see https://phabricator.wikimedia.org/T262630
	'cloudelastic-chi' => [
		[ // forwarded to https://cloudelastic.wikimedia.org:9243/
			'host' => 'localhost',
			'transport' => 'Http',
			'port' => 6105,
		],
	],
	'cloudelastic-psi' => [
		[ // forwarded to https://cloudelastic.wikimedia.org:9443/
			'host' => 'localhost',
			'transport' => 'Http',
			'port' => 6107,
		],
	],
	'cloudelastic-omega' => [
		[ // forwarded to https://cloudelastic.wikimedia.org:9643/
			'host' => 'localhost',
			'transport' => 'Http',
			'port' => 6106,
		],
	]
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
			'host' => '_etcd._tcp.eqiad.wmnet',
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

			// Old pc hosts, to be decommed:
			# '10.64.48.174', # pc1010, D3 4.4TB 256GB
			# '10.64.0.180',  # pc1007, A6 4.4TB 256GB
			# '10.64.16.20',  # pc1008, B8 4.4TB 256GB
			# '10.64.32.29',  # pc1009, C3 4.4TB 256GB
		],

		// LockManager Redis
		'redis_lock' => [
			'rdb1' => '10.64.0.65',   # mc1040 A2
			'rdb2' => '10.64.32.153', # mc1048 C2
			'rdb3' => '10.64.48.91',  # mc1052 D4
		],
		'search-chi' => [
			[ // forwarded to https://search.svc.eqiad.wmnet:9243/
				'host' => 'localhost',
				'transport' => 'Http',
				'port' => 6102,
			]
		],
		'search-psi' => [
			[ // forwarded to https://search.svc.eqiad.wmnet:9643/
				'host' => 'localhost',
				'transport' => 'Http',
				'port' => 6104,
			]
		],
		'search-omega' => [
			[ // forwarded to https://search.svc.eqiad.wmnet:9443/
				'host' => 'localhost',
				'transport' => 'Http',
				'port' => 6103,
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

			// Old pc hosts, to be decommed:
			# '10.192.48.14',  # pc2010, D3 4.4TB 256GB
			# '10.192.16.35',  # pc2008, B3 4.4TB 256GB
			# '10.192.32.10',  # pc2009, C1 4.4TB 256GB
		],

		'redis_lock' => [
			'rdb1' => '10.192.32.162', # mc2030 C5
			'rdb2' => '10.192.0.86',   # mc2022 A8
			'rdb3' => '10.192.48.78',  # mc2034 D4
		],
		'search-chi' => [
			[ // forwarded to https://search.svc.codfw.wmnet:9243/
				'host' => 'localhost',
				'transport' => 'Http',
				'port' => 6202,
			]
		],
		'search-psi' => [
			[ // forwarded to https://search.svc.codfw.wmnet:9643/
				'host' => 'localhost',
				'transport' => 'Http',
				'port' => 6204,
			]
		],
		'search-omega' => [
			[ // forwarded to https://search.svc.codfw.wmnet:9443/
				'host' => 'localhost',
				'transport' => 'Http',
				'port' => 6203,
			]
		],
	],
];
unset( $common );
return $services;
