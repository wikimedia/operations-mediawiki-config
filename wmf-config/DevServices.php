<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

# DevServices.php statically defines all service hostnames/ips
# for any service used by MediaWiki in development.
#
# Included from: ../src/ServiceConfig.php
#

# Returns an array mapping datacenter names to a mapping of services
# for a given datacenter.
return [
	'dev' => [
		'udp2log' => '127.0.0.1:1',
		'redis_lock' => '',
		'etcd' => [
			'host' => 'db:2379',
			'protocol' => 'https'
		],
		'mediaSwiftAuth' => '',
		'mediaSwiftStore' => '',
		'sessionstore' => '',
		'echostore' => '',
		'statsd' => '',
		'irc' => [],
		'upload' => '',
		'poolcounter' => '',
		'urldownloader' => '',
		'search-chi' => '',
		'restbase' => '',
		'parsercache-dbs' => [],
		'parsoid' => '',
		'electron' => '',
		'mathoid' => '',
		'eventgate-analytics' => '',
		'eventgate-analytics-external' => '',
		'eventgate-main' => '',
		'linkrecommendation' => '',
		'push-notifications' => '',
		'shellbox' => null,
		'shellbox-media' => null,
		'shellbox-syntaxhighlight' => null,
		'shellbox-timeline' => null,
		'xenon' => null,
		'xhgui' => null,
		'xhgui-pdo' => null,
	]
];
