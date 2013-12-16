<?php

# WARNING: This file is publically viewable on the web.
# Do not put private data here.

#######################################################################
#
# This file list extension includes and configuration on production
#
#######################################################################

if( $wmgRealm === 'production' ) {   # safeguard

	include( $IP . '/extensions/CheckUser/CheckUser.php' );
	$wgCheckUserForceSummary = $wmgCheckUserForceSummary;



} # safeguard
