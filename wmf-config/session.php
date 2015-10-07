<?php

if ( gethostname() === 'mw1017' && defined( 'HHVM_VERSION' ) ) {
	$sessionRedis = array(
		'eqiad' => array( '/var/run/nutcracker/redis_eqiad.sock' ),
		'codfw' => array( '/var/run/nutcracker/redis_codfw.sock' ),
	);
} else {
	$sessionRedis = array(
		'eqiad' => array( '127.0.0.1:6380' ),
		'codfw' => array( '127.0.0.1:6380' ),
	);
}
