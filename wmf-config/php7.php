<?php
/**
 * PHP7-specific settings we might need to keep separated
 * from the main configuration until the transition is completed
 */
// Open logs and set the syslog.ident to a sensible value on php-fpm
// See https://phabricator.wikimedia.org/T211184 for a discussion
if ( PHP_SAPI === 'fpm-fcgi' ) {
	openlog( 'php7.2-fpm', LOG_ODELAY, LOG_DAEMON );
}
