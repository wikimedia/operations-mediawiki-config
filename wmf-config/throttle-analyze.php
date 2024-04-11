<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

/**
 * Helper to easily add a throttling request.
 * See throttle.php for the format of $wmgThrottlingExceptions.
 */

use Wikimedia\IPUtils;

$wgExtensionFunctions[] = static function () {
	global $wmgThrottlingExceptions, $wgDBname, $wgRequest;

	foreach ( $wmgThrottlingExceptions as $options ) {
		# Validate entry, skip when it does not apply to our case

		# 1) skip when it does not apply to our database name

		if ( isset( $options['dbname'] ) && !in_array( $wgDBname, (array)$options['dbname'] ) ) {
			continue;
		}

		# 2) skip expired entries
		$inTimeWindow = time() >= strtotime( $options['from'] )
			&& time() <= strtotime( $options['to'] );

		if ( !$inTimeWindow ) {
			continue;
		}

		# 3) skip when throttle does not apply to the client IP
		$ip = $wgRequest->getIP();
		if ( isset( $options['IP'] ) && !in_array( $ip, (array)$options['IP'] ) ) {
			continue;
		}
		if ( isset( $options['range'] ) && !IPUtils::isInRanges( $ip, (array)$options['range'] ) ) {
			continue;
		}

		# Finally) set up the throttle value
		global $wgAccountCreationThrottle, $wgTempAccountCreationThrottle, $wgRateLimits, $wgGroupPermissions;
		if ( isset( $options['value'] ) && is_numeric( $options['value'] ) ) {
			$wgAccountCreationThrottle = [ [ 'count' => $options['value'], 'seconds' => 86400 ] ];
		} else {
			// Provide some sane default
			$wgAccountCreationThrottle = [ [ 'count' => 50, 'seconds' => 86400 ] ];
		}

		// Unlike AccountCreationThrottle, wgTempAccountCreationThrottle has a default
		// value of 6 accounts per day.
		if ( isset( $options['tempaccountvalue'] ) && is_numeric( $options['tempaccountvalue'] ) ) {
			$wgTempAccountCreationThrottle = [ [ 'count' => $options['tempaccountvalue'], 'seconds' => 86400 ] ];
		}

		$wgRateLimits['badcaptcha']['ip'] = [ 1000, 86400 ];
		$wgRateLimits['badcaptcha']['newbie'] = [ 1000, 86400 ];
		// T204583
		$wgGroupPermissions['user']['autoconfirmed'] = true;
		// T227487
		$wgGroupPermissions['*']['skipcaptcha'] = true;
		// No point in proceeding to another entry
		return;
	}
};
