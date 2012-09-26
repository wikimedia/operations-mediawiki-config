<?php

$wgHooks['BeforePageDisplay'][] = 'wfItalianWikipediaDisableScripts';

function wfItalianWikipediaDisableScripts() {
	global $wgRequest, $wgUser;

	// Don't allow anonymous users to do this, because the Set-Cookie headers
	// would mess up cacheable pages
	if ( $wgUser->isAnon() ) {
		return true;
	}

	$req = $wgRequest->getVal( 'killscripts836' );
	if ( $req === 'yes' ) {
		setcookie( 'itwiki-killscripts836', 'yes', time() + 30 * 86400, '/' );
	} elseif ( $req === 'no' ) {
		setcookie( 'itwiki-killscripts836', '', time() - 1, '/' );
	} else {
		if ( isset( $_COOKIE['itwiki-killscripts836'] )
			&& $_COOKIE['itwiki-killscripts836'] == 'yes' )
		{
			$GLOBALS['wgUseSiteJs'] = false;
			$GLOBALS['wgUseSiteCss'] = false;
			header( 'X-MW-Scripts-Disabled: yes' );
		}
	}
	return true;
}

