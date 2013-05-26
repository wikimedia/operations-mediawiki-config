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
#  'dbname' => a $wgDBname or array of dbnames to compare to
#             (eg. enwiki, metawiki, frwikibooks, eswikiversity)
#             (default: any project)

# Initialize the array. Append to that array to add a throttle
$wmgThrottlingExceptions = array();

## Add throttling definitions below.

$wmfThrottlingExceptions[] = array( // Wikimedia Hackathon Amsterdam 2013
	'from' => '2013-05-26T00:00 +1:00',
	'to' => '2013-05-27T00:00 +1:00',
	'IP' => array( '77.170.89.227', '213.127.161.46' ),
	'value' => 70,
);

$wmfThrottlingExceptions[] = array( // Editing session at the NIH - http://www.ncbi.nlm.nih.gov/
	'from' => '2013-05-28T08:00 -7:00',
	'to' => '2013-05-30T20:00 -7:00',
	'IP' => array( '156.40.3.1' ),
	'dbname' => array( 'enwiki', 'commonswiki' ),
	'value' => 100, // 50 to 100 accounts
);

## Add throttling definitions above.

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

