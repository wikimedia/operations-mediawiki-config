<?php
# WARNING: This file is publically viewable on the web. Do not put private data here.

# This file holds the MediaWiki CirrusSearch configuration which is specific
# to the 'production' realm.
# It should be loaded AFTER CirrusSearch-common.php

$wgCirrusSearchServers = array(
	'10.2.2.30', # search.svc.eqiad.wmnet
);

$wgCirrusSearchConnectionAttempts = 3;
