<?php
# WARNING: This file is publically viewable on the web. Do not put private data here.

#######################################################################
# LabsServices.php should include all the service hostnames/ips
# for any service used in labs
#
# It is included by CommonSettings.php
#
#######################################################################

$wmfAllServices = [];

$wmfAllServices['eqiad'] = [
	'udp2log' => 'deployment-fluorine.eqiad.wmflabs:8420',
	'statsd' => 'labmon1001.eqiad.wmnet',
	'search' => [
		// These MUST match the installed SSL certs
		'deployment-elastic05.deployment-prep.eqiad.wmflabs',
		'deployment-elastic06.deployment-prep.eqiad.wmflabs',
		'deployment-elastic07.deployment-prep.eqiad.wmflabs',
		'deployment-elastic08.deployment-prep.eqiad.wmflabs',
	],
	'ocg' => 'http://deployment-pdf01:8000',
	'urldownloader' => 'http://deployment-urldownloader.deployment-prep.eqiad.wmflabs:8080',
	'parsoid' => 'http://10.68.16.120:8000',
	'mathoid' => 'http://deployment-mathoid.eqiad.wmflabs:10042',
	'eventlogging' => 'udp://deployment-eventlogging03.eqiad.wmflabs:8421',
	'eventbus' => 'http://deployment-eventlogging04.deployment-prep.eqiad.wmflabs:8085',
	'upload' => false,
	'cxserver' => 'http://deployment-sca01.eqiad.wmflabs:8080',
	'irc' => 'irc.beta.wmflabs.org', // deployment-ircd
	'redis_lock' => [
		'memc04' => '10.68.23.25', // deployment-memc04.deployment-prep.eqiad.wmflabs
		'memc05' => '10.68.23.49', // deployment-memc05.deployment-prep.eqiad.wmflabs
	],
];

### Logstash
$wmfAllServices['eqiad']['logstash'] = [
	'10.68.16.147', // deployment-logstash2.deployment-prep.eqiad.wmflabs
];

### Analytics Kafka cluster (not present in labs)
$wmfAllServices['eqiad']['kafka'] = [
	'deployment-kafka02.deployment-prep.eqiad.wmflabs:9092',
	'deployment-kafka03.deployment-prep.eqiad.wmflabs:9092',
];

### Restbase
$wmfAllServices['eqiad']['restbase'] = 'http://10.68.17.189:7231'; // deployment-restbase02.deployment-prep.eqiad.wmflabs

### Poolcounter
$wmfAllServices['eqiad']['poolcounter'] = [
	'10.68.19.181', # deployment-poolcounter01.deployment-prep.eqiad.wmflabs
];


# Shorthand when we have no master-slave situation to keep into account
$wmfLocalServices = $wmfAllServices[$wmfDatacenter];

$wmfMasterServices = $wmfAllServices[$wmfMasterDatacenter];
