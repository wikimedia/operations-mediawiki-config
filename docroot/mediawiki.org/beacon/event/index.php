<?php

require_once __DIR__ . '/../../../../multiversion/MWMultiVersion.php';
require MWMultiVersion::getMediaWiki( 'includes/WebStart.php' );

use MediaWiki\Extension\EventBus\EventBus;
use MediaWiki\Extension\EventLogging\Libs\Legacy\EventLoggingLegacyConverter;

/**
 * Expects that the incoming HTTP query string is a 'qson' event (URL encoded JSON string).
 * This event will be parsed, converted and sent along via EventLogging::submit.
 */

// NOTE: As of 2024-01, the only legacy EventLogging schema this needs to support is
// MediaWikiPingback.  Details about this can be found at https://phabricator.wikimedia.org/T323828.
// In summary, MediaWikiPingback does not use the EventLogging MediaWiki extension to produce events.
// MediaWikiPingback instrument collects data from 3rd party MediaWiki installs,
// and we cannot force those 3rd parties to upgrade to newer versions of MediaWiki
// that produce events directly to eventgate-analytics-external.
// (See https://gerrit.wikimedia.org/r/c/mediawiki/core/+/938271/ ).
//
// The MediaWikiPingback instrument is configured to send events directly to mediawiki.org, so
// we only need to handle legacy conversion of events from mediawiki.org.
//
// Once we are confident that there are sufficiently few remaining 3rd party MediaWiki installs
// out there that send events using this legacy endpoint, we can remove this endpoint and related
// code (EventLogging extension's EventLoggingLegacyConverter) entirely.

try {
	// Return 204 ASAP, just like /beacon/event handled by varnish used to.
	http_response_code( 204 );

	// Attempt to convert the legacy event from the HTTP request query string
	$convertedEvent = EventLoggingLegacyConverter::fromHttpRequest();

	$streamName = $convertedEvent['meta']['stream'] ?? null;
	if ( !$streamName ) {
		throw new RuntimeException(
			'Cannot submit event: event must have stream name set in  meta.stream field.'
		);
	}

	// NOTE: Here we use EventBus (via 'EventBus.EventBusFactory' MW Service)
	// directly to send the event, rather than 'EventLogging.EventSubmitter'.
	// EventLogging.EventSubmitter is a EventBusEventSubmitter in production,
	// which wraps the EventBus->send call in a DeferredUpdate.
	// Because this /beacon/event/index.php file is a custom PHP endpoint,
	// DeferredUpdates are not initialized to execute.  We don't really
	// want this send() call to be in a DeferredUpdate anyway, as we have already
	// closed the HTTP response and can just send the event now.
	EventBus::getInstanceForStream( $streamName )->send( $convertedEvent );
} catch ( Throwable $e ) {
	trigger_error(
		'EventLoggingLegacyConverter: Failed proxying legacy EventLogging event query string ' .
			'to WMF Event Platform JSON: ' . get_class( $e ) . ': ' . $e->getMessage(),
		E_USER_ERROR
	);
}
