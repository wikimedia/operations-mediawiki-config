<?php

$wgPoolCountClientConf = array(
	'servers' => array(
		'10.68.19.179', # poolcounter.deployment-prep.eqiad.wmflabs
	),
	'timeout' => 0.5,
	'connect_timeout' => 0.01 # Connect timeout is 10 ms
);
