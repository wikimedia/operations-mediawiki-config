<?php
/**
 * Initialization code for HHVM worker threads.
 *
 * WARNING: This result of running this file is *cached* by HHVM.
 *
 * HHVM is configured to run this file at the beginning of each worker
 * thread through the `hhvm.server.request_init_document` setting.
 *
 * The result of executing this PHP file is cached by taking a snapshot
 * of the VM state. At the start of each request, the state is restored
 * restored to that of the snapshot. That means, this file CAN expose
 * variables, functions and other state to the "real" run-time for
 * web requests, however, there are two important caveats:
 *
 * 1) This file itself does not have access to request-specific
 *    data such as $_GET, $_POST, $_COOKIE and (most of) $_SERVER,
 *    because it runs before that state is created and does not
 *    re-run for each request.
 * 2) This file is efffectively cached. Unlike other PHP files
 *    executed by HHVM/PHP, this file is not ensured to be re-run
 *    when it changes on-disk. To be safe, one should restart HHVM
 *    between deploying a change to this file and depending on the
 *    state elsewhere. Or (better yet), never depend on the state
 *    created by this file.
 *
 * @see https://github.com/facebook/hhvm/blob/HHVM-3.22.0/hphp/doc/server.documents
 */
if ( !defined( 'HHVM_VERSION' ) ) {
	return;
}

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

set_error_handler( function ( $errno, $message, $file, $line, $context, /* nonstandard! */ $backtrace ) {
	syslog( LOG_ERR, json_encode( [
		'message'   => $message,
		'file'      => $file,
		'line'      => $line,
		'context'   => $context,
		'backtrace' => $backtrace,
	], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
	return false;
}, E_FATAL );
