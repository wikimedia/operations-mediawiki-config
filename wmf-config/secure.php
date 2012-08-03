<?php
# WARNING: This file is publically viewable on the web. Do not put private data here.

if ( $secure ) {
	// Override the produced URLs with the https://secure.wikimedia.org interface...

	$wgInternalServer = $wgServer; // The real domain
	// Expand $wgInternalServer if needed. We don't have wfExpandUrl() available though
	if ( substr( $wgInternalServer, 0, 2 ) == '//' ) {
		$wgInternalServer = "http:$wgInternalServer";
	}
	$wgCanonicalServer = $wgInternalServer; // http for now. May become https one day
	$wgServer = 'https://secure.wikimedia.org';

	// Instead of subdomains we'll use an extended path.
	// This will let us use a single signed certificate.
	$wgScriptPath     = "/$site/$lang$wgScriptPath";
	$wgScript         = "/$site/$lang$wgScript";
	$wgArticlePath    = "/$site/$lang$wgArticlePath";
	$wgRedirectScript = "/$site/$lang$wgRedirectScript";
	$wgLoadScript = "$wgScriptPath/load.php";

	// For img_auth.php
	if ( $wgUploadPath == '/w/img_auth.php' ) {
		$wgUploadPath = "/$site/$lang$wgUploadPath";
		// Fix $wgLocalFileRepo (already set by CommonSettings.php)
		$wgLocalFileRepo['url'] = $wgUploadPath;
	}
	if ( $wgThumbnailScriptPath == '/w/thumb.php' ) {
		$wgThumbnailScriptPath = "/$site/$lang$wgThumbnailScriptPath";
		$wgLocalFileRepo['thumbScriptUrl'] = $wgThumbnailScriptPath;
	}

	// Restrict cookies
	$wgCookieDomain = 'secure.wikimedia.org';
	$wgCookiePath = "/$site/$lang/";
	$wgCookieSecure = true;
	session_set_cookie_params( 3600, $wgCookiePath, $wgCookieDomain, $wgCookieSecure );

	// Stylesheets can be loaded through the global shared path.
	// Note that favicon.ico and robots.txt will be shared, oh wells.

	$wgStyleSheetPath = $wgStylePath = '/skins-' . $wmfVersionNumber;
	$wgExtensionAssetsPath = '//bits.wikimedia.org/static-' . $wmfVersionNumber . '/extensions;

	// Uploads will come from http://upload.wikimedia.org/ anyway...
	if ( $wgDBname == 'internalwiki' ) {
		// FIXME: special-case hack
		$wgUploadPath = 'https://secure.wikimedia.org/wikipedia/internal/w/img_auth.php';
	}

	// Normalize upload navigation links...
	// https://bugzilla.wikimedia.org/show_bug.cgi?id=10843
	if ( $wgUploadNavigationUrl ) {
		if ( $wgUploadNavigationUrl[0] == '/' ) {
			// For local sites
			$wgUploadNavigationUrl = "/$site/$lang$wgUploadNavigationUrl";
		} else {
			// Commons special-case
			$wgUploadNavigationUrl =
				preg_replace(
					'/^http:\/\/commons\.wikimedia\.org/',
					'https://secure.wikimedia.org/wikipedia/commons',
					$wgUploadNavigationUrl );
		}
	}

	// Fake HTTPS mode
	$_SERVER['HTTPS'] = 'on';

	// Make sure the parser cache isn't clobbered
	$wgRenderHashAppend = ':https=1';

	// Make sure the sidebar cache isn't clobbered
	$wgEnableSidebarCache = false;

	// Disable the pretty links for Serbian variants, as they won't work
	$wgVariantArticlePath = false;

	// Make sure the squid caches are purged properly
	$wgHooks['GetInternalURL'][] = 'fixupSquidUrl';
	// And canonical URLs too. May need to be tweaked if $wgCanonicalServer changes to https
	$wgHooks['GetCanonicalURL'][] = 'fixupSquidUrl';
	$wgHooks['GetLocalURL'][] = 'fixupSquidUrl';
	// Fix up URLs in IRC
	$wgHooks['IRCLineURL'][] = 'fixupIRCURL';

	function fixupUrl( $url ) {
		// $wgInternalServer / $wgCanonicalServer set the hostname but we need to change the paths too.
		return preg_replace(
			'!^(http://.*?)/.*?/.*?/(.*)$!',
			'$1/$2',
			$url
		);
	}

	function fixupSquidUrl( &$title, &$url, $query ) {
		$url = fixupUrl ( $url );
		return true;
	}

	function fixupIRCURL( &$url, &$query ) {
		$url = fixupUrl( $url );
		return true;
	}
}

