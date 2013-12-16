<?php

# WARNING: This file is publically viewable on the web.
# Do not put private data here.

#######################################################################
#
# This file list extension includes and configuration on beta
#
#######################################################################

if( $wmgRealm === 'labs' ) {   # safeguard

// Deployed by Petrb as a part of review.
// This extension is scheduled for review and deployment on enwiki
// Do not remove this extension, without contacting Petr Bena
if ($wgDBname == "enwiki") {
	# commented by hashar on 20121116, cause message cache issue on
	# beta with the magic word it introduces.
	# Been disabled by a live hack since last week at least.
	//include ("$IP/extensions/OnlineStatusBar/OnlineStatusBar.php");
}


} # safeguard
