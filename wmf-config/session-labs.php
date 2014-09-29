<?php
$wgObjectCaches['sessions'] = array(
	'class'   => 'RedisBagOStuff',
	'servers' => array( '10.68.16.231' ), # deployment-redis02
);
