<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

# DevServices.php statically defines all service hostnames/ips
# for any service used by MediaWiki in development.
#
# Included from: ../src/ServiceConfig.php
#

# Returns an array mapping datacenter names to a mapping of services
# for a given datacenter.
return [ 'dev' => [
				   'udp2log' => '',
				   'redis_lock' => '',
				   'etcd' => 'db:2379',
				   'mediaSwiftAuth' => '',
				   'mediaSwiftStore' => '',
				   'sessionstore' => '',
				   'echostore' => '',
				   'statsd' => '',
				   'irc' => '',
				   'upload' => '',
				   'poolcounter' => '',
				   'urldownloader' => '',
				   'search-chi' => '',
				   'restbase' => '',
				   'parsoid' => '',
				   'electron' => '',
				   'mathoid' => '',
				   'eventgate-analytics' => '',
				   'eventgate-analytics-external' => '',
				   'eventgate-main' => '',
				  ]
	   ];
