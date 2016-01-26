<?php
# WARNING: This file is publically viewable on the web. Do not put private data here.

#######################################################################
# LabsServices.php should include all the service hostnames/ips
# for any service used in labs
#
# It is included by CommonSettings.php
#
#######################################################################

$wmfAllServices = array();

$wmfAllServices['eqiad'] = array(
	'udp2log' => 'deployment-fluorine.eqiad.wmflabs:8420',
	'statsd' => 'labmon1001.eqiad.wmnet',
	'search' => array(
		'deployment-elastic05',
		'deployment-elastic06',
		'deployment-elastic07',
		'deployment-elastic08',
	),
	'ocg' => 'http://deployment-pdf01:8000',
	'urldownloader' => 'http://deployment-urldownloader.deployment-prep.eqiad.wmflabs:8080',
	'parsoid' => 'http://10.68.16.120:8000',
	'mathoid' => 'http://deployment-mathoid.eqiad.wmflabs:10042',
	'eventlogging' => 'udp://deployment-eventlogging03.eqiad.wmflabs:8421', # eventlog1001.eqiad.wmnet
	'eventbus' => 'http://deployment-eventlogging04.deployment-prep.eqiad.wmflabs:8085',
);

### Logstash
$wmfAllServices['eqiad']['logstash'] = array(
	'10.68.16.147', // deployment-logstash2.deployment-prep.eqiad.wmflabs
);

### Analytics Kafka cluster (not present in labs)
$wmfAllServices['eqiad']['kafka'] = array(
);

### IRC (not present in labs)
$wmfAllServices['eqiad']['irc'] = null;

### Restbase
$wmfAllServices['eqiad']['restbase'] = 'http://10.68.17.189:7231'; // deployment-restbase02.deployment-prep.eqiad.wmflabs

# Shorthand when we have no master-slave situation to keep into account
$wmfLocalServices = $wmfAllServices[$wmfDatacenter];

$wmfMasterServices = $wmfAllServices[$wmfMasterDatacenter];
