<?php

$wgPoolCountClientConf = array(
	'servers' => array(
		'10.64.0.179',
		'10.64.16.152'
	),
	'timeout' => 0.5
);

# Enable connect_timeout for testwiki
if ($wgDBname == 'testwiki' ) {
	$wgPoolCountClientConf['connect_timeout'] = 0.01;
}
