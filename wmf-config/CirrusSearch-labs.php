<?php
# WARNING: This file is publically viewable on the web. Do not put private data here.

# This file holds the MediaWiki CirrusSearch configuration which is specific
# to the 'labs' realm which in most of the cases means the beta cluster.
# It should be loaded AFTER CirrusSearch-common.php

# default host for mwsuggest backend
$wgCirrusSearchServers = array( 'deployment-es0', 'deployment-es1', 'deployment-es2', 'deployment-es3' );
