<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.
#
# This file holds overrides specific to Beta Cluster.
#
# This is for BETA only. It must not be loaded in production.
#

if ( $wmgRealm == 'labs' ) {  # safe guard

// Beta Cluster: Increase timeout to 500ms (in microseconds)
$wgObjectCaches['mcrouter']['timeout'] = 0.5 * 1e6;

} # end safe guard
