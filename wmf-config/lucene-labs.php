<?php
# WARNING: This file is publically viewable on the web. Do not put private data here.

# This file hold the MediaWiki lucene configuration which is specific
# to the 'labs' realm which in most of the cases means the beta cluster.
# It should be loaded AFTER lucene-common.php

# default host for mwsuggest backend
$wgEnableLucenePrefixSearch = true;
$wgLucenePrefixHost = '10.4.1.81';  # deployment-search01.pmtpa.wmflabs

$wgLucenePort = 8123;
$wgLuceneHost = '10.4.1.81';  # deployment-search01.pmtpa.wmflabs
