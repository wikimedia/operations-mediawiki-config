<?php

# WARNING: This file is publically viewable on the web.
# Do not put private data here.

# The helper functions takes an array of parameters:
#  'from'  => date/time to start raising account creation throttle
#  'to'    => date/time to stop
#
# Optional arguments can be added to set the value or restrict by client IP
# or project dbname. Options are:
#  'value'  => new value for $wgAccountCreationThrottle (default: 50)
#  'IP'     => client IP as given by wfGetIP() or array (default: any IP)
#  'range'  => alternatively, the client IP CIDR ranges or array (default: any range)
#  'dbname' => a $wgDBname or array of dbnames to compare to
#             (eg. enwiki, metawiki, frwikibooks, eswikiversity)
#             (default: any project)

# Initialize the array. Append to that array to add a throttle
$wmgThrottlingExceptions = array();

## Add throttling definitions below.

$wmfThrottlingExceptions[] = array( // bug 49176 it.wiki GLAM event
	'from'   => '2013-06-08T12:00 +0:00',
	'to'     => '2013-06-08T17:00 +0:00',
	'IP'     => '46.255.84.17',
	'dbname' => array( 'itwiki' ),
	'value'  => 50,

## Add throttling definitions above.

## Helper methods:

/**
 * Determines if an IP address is a list of CIDR a.b.c.d/n ranges.
 *
 * @param string $ip the IP to check
 * @param array $range the IP ranges, each element a range
 *
 * @return Boolean true if the specified adress belongs to the specified range; otherwise, false.
 */
function inCIDRRanges ( $ip, $ranges ) {
	foreach ( $ranges as $range ) {
		if ( inCIDRRange( $ip, $range ) ) {
			return true;
		}
	}
	return false;
}

/**
 * Determines if an IP address is a CIDR a.b.c.d/n range.
 *
 * @param string $ip the IP to check
 * @param string $range the IP range
 *
 * @return Boolean true if the specified adress belongs to the specified range; otherwise, false.
 */
function inCIDRRange ( $ip, $range ) {
	// Thanks to claudiu at cnixs dot com
	// http://php.net/manual/en/ref.network.php
	list( $net, $mask ) = explode( "/", $range );
	$ip_net = ip2long( $net );
	$ip_mask = ~((1 << (32 - $mask)) - 1);
	$ip_ip = ip2long ( $ip );
	$ip_ip_net = $ip_ip & $ip_mask;
	return ( $ip_ip_net == $ip_net );
}

# Will eventually raise value when MediaWiki is fully initialized:
$wgExtensionFunctions[] = 'efRaiseAccountCreationThrottle';

/**
 * Helper to easily add a throttling request.
 */
function efRaiseAccountCreationThrottle() {
	global $wmgThrottlingExceptions, $wgDBname;

	foreach ( $wmgThrottlingExceptions as $options ) {
		# Validate entry, skip when it does not apply to our case

		# 1) skip when it does not apply to our database name

		if( isset( $options['dbname'] ) ) {
			if ( is_array( $options['dbname'] ) ) {
				if ( !in_array( $wgDBname, $options['dbname'] ) ) {
					continue;
				}
			} elseif ( $wgDBname != $options['dbname'] ) {
				continue;
			}
		}

		# 2) skip expired entries
		$inTimeWindow = time() >= strtotime( $options['from'] )
				&& time() <= strtotime( $options['to'] );

		if( !$inTimeWindow ) {
			continue;
		}

		# 3) skip when throttle does not apply to the client IP
		if ( isset( $options['IP'] ) ) {
			if ( is_array( $options['IP'] ) && !in_array( wfGetIP(), $options['IP'] ) ) {
				continue;
			} elseif ( wfGetIP() != $options['IP'] ) {
				continue;
			}
		}
		if ( isset ( $options['range'] ) ) {
			if ( is_array( $options['range'] ) && !inCIDRRanges( wfGetIP(), $options['range'] ) ) {
				continue;
			} elseif ( !inCIDRRange( wfGetIP(), $options['range'] ) ) {
				continue;
			}
		}

		# Finally) set up the throttle value
		global $wgAccountCreationThrottle;
		if( isset( $options['value'] ) && is_numeric( $options['value'] ) ) {
			$wgAccountCreationThrottle = $options['value'];
		} else {
			$wgAccountCreationThrottle = 50; // Provide some sane default
		}
		return; # No point in proceeding to another entry
	}
}

