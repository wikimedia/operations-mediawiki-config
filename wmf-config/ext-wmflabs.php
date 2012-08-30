<?php

# WARNING: This file is publically viewable on the web.
# Do not put private data here.

#######################################################################
#
# This file list extension includes and configuration on beta
#
#######################################################################

if( $cluster === 'wmflabs' ) {   # safeguard

// Deployed by Petrb as a part of review, this extension is scheduled for review and deployment on enwiki
// do not remove this extension, without contacting me :P
if ($wgDBname == "enwiki") {
	include ("$IP/extensions/OnlineStatusBar/OnlineStatusBar.php");
}


} # safeguard
