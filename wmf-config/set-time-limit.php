<?php

/**
 * Configure timeouts. These should be slightly less than the Apache timeouts,
 * so that the slightly more informative PHP error message is delivered to the
 * user, and so that we can verify that PHP timeouts actually exist (T97192).
 */
function wmfSetTimeLimit() {
	if ( PHP_SAPI === 'cli' ) {
		// It should already be zero, and Maintenance.php should set it to zero
		return;
	}
	$host = isset( $_SERVER['HTTP_HOST'] ) ? $_SERVER['HTTP_HOST'] : '';
	switch ( $host ) {
		case 'videoscaler.svc.eqiad.wmnet':
		case 'videoscaler.svc.codfw.wmnet':
		case 'videoscaler.discovery.wmnet':
			$limit = 86400;
			break;

		case 'jobrunner.svc.eqiad.wmnet':
		case 'jobrunner.svc.codfw.wmnet':
		case 'jobrunner.discovery.wmnet':
			$limit = 1200;
			break;

		default:
			if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
				$limit = 200;
			} else {
				$limit = 60;
			}
	}
	if ( extension_loaded( 'excimer' ) ) {
		static $timer;
		$timer = new ExcimerTimer;
		$timer->setInterval( $limit );
		$timer->setCallback( function () use ( $limit ) {
			throw new WMFTimeoutException(
				"the execution time limit of $limit seconds was exceeded"
			);
		} );
		$timer->start();
	} else {
		set_time_limit( $limit );
	}
}

class WMFTimeoutException extends RuntimeException {
}

wmfSetTimeLimit();
