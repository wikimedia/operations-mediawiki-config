<?php

# WARNING: This file is publically viewable on the web. Do not put private data here.

# This file is referenced from CommonSettings.php and included
# when $wmgUseCodeReview is true
# Implemented following bug 23494

//$wgCodeReviewDeferredPaths['MediaWiki'] = array();

$importantPaths = array(
	'/trunk/phase3',
);

$wgCodeReviewFixmePerPath['MediaWiki'] = $importantPaths;
$wgCodeReviewNewPerPath['MediaWiki'] = $importantPaths;
