<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.
#
# This file holds overrides specific to Beta Cluster.
#
# This is for BETA only. It must not be loaded in production.
#

if ( $wmfRealm == 'labs' ) {  # safe guard

// Beta Cluster: Increase timeout to 500ms (in microseconds)
$wgObjectCaches['mcrouter']['timeout'] = 0.5 * 1e6;

// Beta Cluster: Experimentally enable the on-host tier for WAN cache
$wgWANObjectCaches['wancache-main-mcrouter']['onHostRoutingPrefix'] = "/$wmfDatacenter/mw-with-onhost-tier/";

} # end safe guard
