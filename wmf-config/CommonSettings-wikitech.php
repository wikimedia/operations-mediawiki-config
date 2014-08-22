<?php

# WARNING: This file is publically viewable on the web.
# Do not put private data here.

# This file hold configuration statement overriding CommonSettings.php
# Should not be loaded on production

if( $wmfRealm == 'wikitech' ) {  # safe guard
	if ( file_exists( "$wmfConfigDir/extension-list-wikitech" ) ) {
		$wgExtensionEntryPointListFiles[] = "$wmfConfigDir/extension-list-wikitech";
	}
} # end safeguard
