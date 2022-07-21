<?php

use Wikimedia\MWConfig\ServiceConfig;

require_once __DIR__ . '/../src/ServiceConfig.php';

global $wmgDatacenter, $wmgRealm;

$serviceConfig = ServiceConfig::getInstance();

$wmgRealm = $serviceConfig->getRealm();
$wmgDatacenter = $serviceConfig->getDatacenter();

unset( $serviceConfig );

// End /Determine realm and datacenter we are on/
