<?php

# WARNING: This file is publically viewable on the web. Do not put private data here.

# This file is referenced from CommonSettings.php and included
# when $wmgUseCodeReview is true
# Implemented following bug 23494

$wgCodeReviewDeferredPaths['MediaWiki'] = array(
	'%^/trunk/extensions/Semantic%',
	'%^/tags/extensions/Semantic%',
	// '%^/trunk/extensions/SocialProfile%', # hashar 20110327; disabled again, he keeps setting new wanting review demon 20110406
	'%^/trunk/WikiWord%',
	'%^/trunk/tools/editor_trends%', # hashar - 20110327
	'%^/trunk/tools/WikiSnaps%', # hashar - 20110327
	'%^/trunk/extensions/Maps%',
	'%^/trunk/extensions/Validator%',
	'%^/trunk/mockups%', # UI designing. hashar - 20111125
	'%^/trunk/debs%', # Debian packages. Will move to git anyway. hashar - 20111125
	'%^/trunk/extensions/AddThis%', # G+, Facebook, Twitter links. hashar - 20111129
	'%^/trunk/extensions/SemanticMediaWiki%',
	'%^/trunk/tools/wsor%', # hashar - 20120215
);

$importantPaths = array(
	'/trunk/phase3',
);

$wgCodeReviewFixmePerPath['MediaWiki'] = $importantPaths;
$wgCodeReviewNewPerPath['MediaWiki'] = $importantPaths;
