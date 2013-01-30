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

$wmfThrottlingExceptions[] = array( // UCSF event at medical school
	'from'   => '2013-01-07T16:00 +0:00',
	'to'     => '2013-01-12T02:00 +0:00',
	'IP'     => array(
	             '205.154.255.252',
		     '64.54.222.227',
                     //Some IPs will be added here Monday
	            ),
	'dbname' => array( 'enwiki', 'commonswiki' ),
	'value'  => 200,                          // 100 to 150 participants expected
);
// Wikipedia Summit Pune, https://bugzilla.wikimedia.org/show_bug.cgi?id=43856
$wmfThrottlingExceptions[] = array(
	'from'   => '2013-01-12T10:00 +5:30',
	'to'     => '2013-01-14T17:00 +5:30',
	'IP'     => array( '14.140.227.67', '14.140.227.68' ),
	'dbname' => array( 'enwiki', 'commonswiki' ),
	'value'  => 50,
);

// Israel Edithon https://bugzilla.wikimedia.org/show_bug.cgi?id=44506
$wmfThrottlingExceptions[] = array(
	'from'   => '2013-01-30T00:00 +2:00',
	'to'     => '2013-02-01T00:00 +2:00',
	'IP'     => '212.25.84.200',
	'dbname' => array( 'frwiki', ),
	'value'  => 50,
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

