<?php

# WARNING: This file is publically viewable on the web.
# Do not put private data here.

#######################################################################
#
# This file list extension includes and configuration on beta
#
#######################################################################

if( $realm === 'labs' ) {   # safeguard

// Deployed by Petrb as a part of review.
// This extension is scheduled for review and deployment on enwiki
// Do not remove this extension, without contacting Petr Bena
if ($wgDBname == "enwiki") {
	include ("$IP/extensions/OnlineStatusBar/OnlineStatusBar.php");
}


} # safeguard
