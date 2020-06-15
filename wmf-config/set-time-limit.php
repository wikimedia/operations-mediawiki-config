<?php

/**
 * Configure timeouts. These should be slightly less than the Apache timeouts,
 * so that the slightly more informative PHP error message is delivered to the
 * user, and so that we can verify that PHP timeouts actually exist (T97192).
 */
function wmfSetTimeLimit() {
	global $wmgTimeLimit;
	if ( PHP_SAPI === 'cli' ) {
		// The time limit should already be zero, and Maintenance.php should set it to zero
		$wmgTimeLimit = 0;
		return;
	}
	$host = isset( $_SERVER['HTTP_HOST'] ) ? $_SERVER['HTTP_HOST'] : '';
	switch ( $host ) {
		case 'videoscaler.svc.eqiad.wmnet':
		case 'videoscaler.svc.codfw.wmnet':
		case 'videoscaler.discovery.wmnet':
			$wmgTimeLimit = 86400;
			break;

		case 'jobrunner.svc.eqiad.wmnet':
		case 'jobrunner.svc.codfw.wmnet':
		case 'jobrunner.discovery.wmnet':
			$wmgTimeLimit = 1200;
			break;

		default:
			if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
				$wmgTimeLimit = 200;
			} else {
				$wmgTimeLimit = 60;
			}
	}
	if ( extension_loaded( 'excimer' ) ) {
		static $timer;
		$timer = new ExcimerTimer;
		$timer->setInterval( $wmgTimeLimit );
		$timer->setCallback( function () use ( $wmgTimeLimit ) {
			throw new WMFTimeoutException(
				"the execution time limit of $wmgTimeLimit seconds was exceeded"
			);
		} );
		$timer->start();
	} else {
		set_time_limit( $wmgTimeLimit );
	}
}

class WMFTimeoutException extends RuntimeException {
}

wmfSetTimeLimit();
