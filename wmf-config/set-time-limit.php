<?php

/**
 * Configure timeouts. These should be slightly less than the Apache timeouts,
 * so that the slightly more informative PHP error message is delivered to the
 * user, and so that we can verify that PHP timeouts actually exist (T97192).
 */
function wmfSetTimeLimit() {
	if ( PHP_SAPI === 'cli' ) {
		// It should already be zero, and Maintenance.php should set it to zero
	} else {
		if ( defined( 'MEDIAWIKI_JOB_RUNNER' ) ) {
			$host = $_SERVER['HTTP_HOST'] ?? '';
			switch ( $host ) {
				case 'videoscaler.svc.eqiad.wmnet':
				case 'videoscaler.discovery.wmnet':
					set_time_limit( 86400 );
					break;

				default:
					set_time_limit( 1200 );
			}
		} elseif ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
			set_time_limit( 200 );
		} else {
			set_time_limit( 60 );
		}
	}
}

wmfSetTimeLimit();
