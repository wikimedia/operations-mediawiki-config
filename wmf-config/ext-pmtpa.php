<?php

# WARNING: This file is publically viewable on the web.
# Do not put private data here.

#######################################################################
#
# This file list extension includes and configuration on production
#
#######################################################################

if( $cluster === 'pmtpa' ) {   # safeguard

	include_once( $IP . '/extensions/CheckUser/CheckUser.php' );
	$wgCheckUserForceSummary = $wmgCheckUserForceSummary;



} # safeguard
