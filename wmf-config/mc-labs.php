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

// Beta Cluster: Experimentally turn on CacheReaper.
// This ensures page-related cache purges are performed,
// even if they got lost somehow, by scanning the recent changes
// table from a job.
$wgEnableWANCacheReaper = true;

} # end safe guard
