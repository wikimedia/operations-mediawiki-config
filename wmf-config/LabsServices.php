<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

# LabsServices.php statically defines all service hostnames/ips
# for any service used by MediaWiki in the Beta Cluster.
#
# Included from: ../src/ServiceConfig.php
#

return [
	'eqiad' => [
		'udp2log' => 'deployment-mwlog02.deployment-prep.eqiad1.wikimedia.cloud:8420',
		'xenon' => 'deployment-mwlog02.deployment-prep.eqiad1.wikimedia.cloud',
		'xhgui-pdo' => 'mysql:host=deployment-db11.deployment-prep.eqiad1.wikimedia.cloud;dbname=xhgui',
		'excimer-ui-url' => 'https://performance.wikimedia.beta.wmflabs.org/excimer/',
		'excimer-ui-server' => 'https://deployment-webperf21.deployment-prep.eqiad1.wikimedia.cloud/excimer/',
		'statsd' => 'prometheus-labmon.eqiad.wmnet',
		'search-chi' => [
			// These MUST match the installed SSL certs
			'deployment-elastic09.deployment-prep.eqiad1.wikimedia.cloud',
			'deployment-elastic10.deployment-prep.eqiad1.wikimedia.cloud',
			'deployment-elastic11.deployment-prep.eqiad1.wikimedia.cloud',
		],
		'search-psi' => [
			// Same as search-chi for now to match production services
			// These MUST match the installed SSL certs
			'deployment-elastic09.deployment-prep.eqiad1.wikimedia.cloud',
			'deployment-elastic10.deployment-prep.eqiad1.wikimedia.cloud',
			'deployment-elastic11.deployment-prep.eqiad1.wikimedia.cloud',
		],
		'search-omega' => [
			// Same as search-chi for now to match production services
			// These MUST match the installed SSL certs
			'deployment-elastic09.deployment-prep.eqiad1.wikimedia.cloud',
			'deployment-elastic10.deployment-prep.eqiad1.wikimedia.cloud',
			'deployment-elastic11.deployment-prep.eqiad1.wikimedia.cloud',
		],
		// cloudelastic is not duplicated in labs, it is a write-only cluster and multi-cluster
		// is sufficiently tested with the clusters above.
		'cloudelastic-chi' => null,
		'cloudelastic-psi' => null,
		'cloudelastic-omega' => null,
		// No parser cache DBs in beta yet
		'parsercache-dbs' => [],
		'urldownloader' => 'http://deployment-urldownloader03.deployment-prep.eqiad1.wikimedia.cloud:8080',
		'parsoid' => 'http://deployment-parsoid12.deployment-prep.eqiad1.wikimedia.cloud/w/rest.php',
		'mathoid' => 'http://deployment-docker-mathoid02.deployment-prep.eqiad1.wikimedia.cloud:10044',
		'eventgate-analytics' => 'http://deployment-eventgate-4.deployment-prep.eqiad1.wikimedia.cloud:8192',
		'eventgate-analytics-external' => 'http://deployment-eventgate-4.deployment-prep.eqiad1.wikimedia.cloud:8492',
		'eventgate-main' => 'http://deployment-eventgate-4.deployment-prep.eqiad1.wikimedia.cloud:8292',
		'upload' => 'deployment-ms-fe04.deployment-prep.eqiad1.wikimedia.cloud',
		'cxserver' => 'http://deployment-docker-cxserver01.eqiad.wmflabs:8080',

		'wikifunctions-orchestrator' => 'deployment-docker-wikifunctions01.deployment-prep.eqiad1.wikimedia.cloud:6254',

		'irc' => [ 'irc.svc.deployment-prep.eqiad1.wikimedia.cloud' ],
		'redis_lock' => [
			'rdb1' => 'deployment-rdb01.deployment-prep.eqiad1.wikimedia.cloud',
		],
		'etcd' => [
			'host' => '_etcd._tcp.svc.deployment-prep.eqiad1.wikimedia.cloud',
			'protocol' => 'https',
		],
		'mediaSwiftAuth' => 'http://deployment-ms-fe04.deployment-prep.eqiad1.wikimedia.cloud/auth',
		'mediaSwiftStore' => 'http://deployment-ms-fe04.deployment-prep.eqiad1.wikimedia.cloud/v1/AUTH_mw',
		'push-notifications' => 'http://deployment-push-notifications01.deployment-prep.eqiad1.wikimedia.cloud:8900',
		'linkrecommendation' => 'https://api.wikimedia.org/service/linkrecommendation',
		// No public API (T306349). Not actually used, we proxy via production
		// action API (cannot be configured here, as the domain depends on the current one).
		'image-suggestion' => null,
		// No public API.
		'ipoid' => null,
		// No Shellbox/k8s in beta cluster (T286298)
		'shellbox' => null,
		'shellbox-constraints' => 'https://shellbox.svc.deployment-prep.eqiad1.wikimedia.cloud',
		'shellbox-media' => null,
		'shellbox-syntaxhighlight' => null,
		'shellbox-timeline' => null,
		'shellbox-video' => 'https://shellbox-video.svc.deployment-prep.eqiad1.wikimedia.cloud',
		// No envoy in beta cluster?
		'mwapi' => null,

		### Logstash
		'logstash' => [
			'localhost',
		],

		### Restbase
		'restbase' => 'http://deployment-restbase04.deployment-prep.eqiad1.wikimedia.cloud:7231',

		### Rest Gateway. Same as Restbase for now, may change when we get true beta rest gateway
		'rest-gateway' => 'http://deployment-restbase04.deployment-prep.eqiad1.wikimedia.cloud:7231',

		### Poolcounter
		'poolcounter' => [
			'deployment-poolcounter06.deployment-prep.eqiad.wmflabs',
		],

		### Kask
		'sessionstore' => 'http://deployment-sessionstore04.deployment-prep.eqiad1.wikimedia.cloud:8080',
		'echostore' => 'http://deployment-echostore02.deployment-prep.eqiad1.wikimedia.cloud:8080',
	],
];
