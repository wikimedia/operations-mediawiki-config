<?php

require_once __DIR__ . '/../../../../src/ServiceConfig.php';
require_once __DIR__ . '/../../../../src/Legacy/EventLoggingLegacyConverter.php';

use Wikimedia\MWConfig\Legacy\EventLoggingLegacyConverter;
use Wikimedia\MWConfig\ServiceConfig;

/**
 * Expects that the incoming HTTP query string is a 'qson' event (URL encoded JSON string).
 * This event will be parsed, converted and posted to EVENT_INTAKE_URL env var,
 * or the local eventgate-analytics-external service.
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
// code (EventLoggingLegacyConverter) entirely.

$wmgServiceConfig = ServiceConfig::getInstance();

// Get the URL to which we should POST events.
if ( getenv( 'EVENT_INTAKE_URL' ) ) {
	$eventIntakeUrl = getenv( 'EVENT_INTAKE_URL' );
} elseif ( $wmgServiceConfig->getLocalService( 'eventgate-analytics-external' ) ) {
	$eventIntakeUrl = $wmgServiceConfig->getLocalService( 'eventgate-analytics-external' ) .
		'/v1/events?hasty=true';
}

if ( !isset( $eventIntakeUrl ) ) {
	throw new InvalidArgumentException(
		"Could not determine event intake url, must set one of EVENT_INTAKE_URL env var " .
		"or a 'eventgate-analytics-external' service in MediaWiki config."
	);
}

try {
	// Attempt to convert the legacy event from the HTTP request query string.
	$convertedEvent = EventLoggingLegacyConverter::fromHttpRequest();

	// NOTE: httpProxyPostJson will handle errors and set http_response_code and response body.
	EventLoggingLegacyConverter::httpProxyPostJson( $eventIntakeUrl, $convertedEvent );
} catch ( Throwable $e ) {
	trigger_error(
		'EventLoggingLegacyConverter: Failed proxying legacy EventLogging event query string ' .
			'to WMF Event Platform JSON: ' . get_class( $e ) . ': ' . $e->getMessage(),
		E_USER_ERROR
	);
}
