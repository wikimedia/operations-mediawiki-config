<?php

$wgPoolCountClientConf = array(
	'servers' => $wmfLocalServices['poolcounter'],
	'timeout' => 0.5
);

# Enable connect_timeout for testwiki
if ( $wgDBname == 'testwiki' || $wmfDatacenter == 'codfw' ) {
	$wgPoolCountClientConf['connect_timeout'] = 0.01;
}
