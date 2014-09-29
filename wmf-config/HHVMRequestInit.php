<?php
/**
 * Initialization code for HHVM.
 *
 * We configure HHVM to run this file at the very beginning of each
 * request by setting the path to this file as the value of HHVM's
 * `hhvm.server.request_init_document` setting.
 */
if ( !defined( 'HHVM_VERSION' ) ) return;

/**
 * Set up fatal error handler.
 *
 * HHVM can call a user handler on catchable fatal errors, unlike PHP5.
 * The code below registers a fatal error handler that logs to syslog.
 * rsyslog on the application servers is configured to forward errors
 * to the MediaWiki log aggregator.
 *
 * For this to work, `hhvm.error_handling.call_user_handler_on_fatals`
 * must be set to true in HHVM's php.ini.
 *
 * See:
 *  https://github.com/facebook/hhvm/blob/ab67da/hphp/runtime/base/runtime-error.h#L57-63
 *  https://github.com/facebook/hhvm/blob/ab67da/hphp/runtime/base/execution-context.cpp#L779-805
 */
define( 'E_FATAL', E_ERROR | ( 1 << 24 ) );

set_error_handler( function( $errno, $message, $file, $line, $context, /* nonstandard! */ $backtrace ) {
	syslog( LOG_ERR, json_encode( array(
		'message'   => $message,
		'file'      => $file,
		'line'      => $line,
		'context'   => $context,
		'backtrace' => $backtrace,
	), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
	return false;
}, E_FATAL );
