<?php
# WARNING: This file is publically viewable on the web. Do not put private data here.

# Default permissions and custom permissions of the ProofReadPage extension
# You must also set wmgUseProofreadPage in InitialiseSettings.php
# This file is referenced from an include in CommonSettings.php

if ( $wgDBname == 'dewikisource' ) {
	$wgGroupPermissions['*']['pagequality'] = true; # 27516
} elseif ( $wgDBname == 'enwikisource' || $wgDBname == 'svwikisource' ) {
	$wgDefaultUserOptions['proofreadpage-showheaders'] = 1;
}

