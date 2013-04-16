<?php
# WARNING: This file is publically viewable on the web. Do not put private data here.

# This file is for the default permissions and custom permissions of the AbuseFilter extension.
# You must also set wmgUseAbuseFilter in InitialiseSettings.php
# This file is referenced from an include in CommonSettings.php

$wgAbuseFilterStyleVersion = "9-1";

// Pretty open on test for.... testing
$wgGroupPermissions['*']['abusefilter-view'] = true;
$wgGroupPermissions['*']['abusefilter-log'] = true;

$wgGroupPermissions['autoconfirmed']['abusefilter-log-detail'] = true; // bug 42012
$wgGroupPermissions['sysop']['abusefilter-log-detail'] = true; // to make sure it is always assigned, also on wikis where autoconfirmed does not have this right

$wgGroupPermissions['sysop']['abusefilter-modify'] = true;

# leaks IP addresses according to Werdna [TS]
$wgGroupPermissions['sysop']['abusefilter-private'] = false;

// Disable some potentially dangerous actions during testing
$wgAbuseFilterAvailableActions = array_diff(
		$wgAbuseFilterAvailableActions,
		array( 'block', 'rangeblock', 'degroup' ) );

// bug 29922 Prevent anyone being given the abusefilter-private right by removing it
$wgAvailableRights = array_diff( $wgAvailableRights, array( 'abusefilter-private' ) );

// Custom permissions
if ( $wgDBname == 'be_x_oldwiki' ) {
	$wgGroupPermissions['autoconfirmed']['abusefilter-log'] = true;
	$wgGroupPermissions['autoconfirmed']['abusefilter-log-detail'] = false; // bug 42012
	$wgGroupPermissions['abusefilter']['abusefilter-log-detail'] = true;
	$wgGroupPermissions['abusefilter']['abusefilter-modify'] = true;
	$wgGroupPermissions['abusefilter']['abusefilter-modify-restricted'] = true;
	$wgGroupPermissions['sysop']['abusefilter-modify-restricted'] = true;
	$wgGroupPermissions['autoconfirmed']['abusefilter-view'] = true;
} elseif ( $wgDBname == 'cawiki' ) {
	$wgGroupPermissions['sysop']['abusefilter-modify'] = false;
	$wgGroupPermissions['abusefilter']['abusefilter-modify'] = true;
	$wgGroupPermissions['sysop']['abusefilter-view-private'] = true;
	$wgGroupPermissions['*']['abusefilter-view'] = false;
	$wgGroupPermissions['user']['abusefilter-view'] = true;
	$wgGroupPermissions['autoconfirmed']['abusefilter-log-detail'] = true;
	$wgAbuseFilterAvailableActions[] = 'block';
	$wgAbuseFilterBlockDuration = '2 hours';

} elseif ( $wgDBname == 'cswiktionary' ) {
	$wgAbuseFilterNotifications = "udp";
	$wgAbuseFilterNotificationsPrivate = true;

} elseif ( $wgDBname == 'dewiki' ) {
	// Removed custom AbuseFilter settings per bug 18223 --Andrew 2009-03-29
	// Where on earth did this come from? --Andrew
	// Was from bug 17453, but doesn't belong here.
	// Per bug 19208 now they want it off, using an abusefilter filter to do it.
	// -- Brion 2009-07-13
	// $wgDefaultUserOptions ['forceeditsummary'] = 1;
	$wgGroupPermissions['autoconfirmed']['abusefilter-log-detail'] = true;
} elseif ( $wgDBname == 'elwiki' ) {
	$wgGroupPermissions['*']['abusefilter-log-detail'] = true;
	$wgGroupPermissions['sysop']['abusefilter-revert'] = true;

} elseif ( $wgDBname == 'enwiki' ) {
	$wgGroupPermissions['sysop']['abusefilter-modify'] = false;
	$wgGroupPermissions['abusefilter']['abusefilter-modify'] = true;
	$wgGroupPermissions['*']['abusefilter-log-detail'] = true;
	$wgGroupPermissions['sysop']['abusefilter-revert'] = true;
	$wgGroupPermissions['sysop']['abusefilter-view-private'] = true;
	$wgAbuseFilterNotifications = "udp";
	$wgAbuseFilterNotificationsPrivate = true; // bug 44045

} elseif ( $wgDBname == 'enwikisource' ) {
	$wgGroupPermissions['sysop']['abusefilter-modify'] = false;
	$wgGroupPermissions['abusefilter']['abusefilter-modify'] = true;
	$wgGroupPermissions['autoconfirmed']['abusefilter-log-detail'] = false; // bug 42012

} elseif ( $wgDBname == 'frwiki' || $wgDBname == 'frwikibooks' ) {
	// wikibooks by bug 26142
	//
	// !!! Please be careful if you edit these rules, they are shared
	// by two wikis. Create two sections, one per wiki, if needed. !!!
	$wgGroupPermissions['*']['abusefilter-view'] = false;
	$wgGroupPermissions['autoconfirmed']['abusefilter-log-detail'] = true;
	$wgGroupPermissions['autoconfirmed']['abusefilter-view'] = true;
	$wgGroupPermissions['sysop']['abusefilter-revert'] = true;
	$wgGroupPermissions['sysop']['abusefilter-modify'] = false;
	$wgGroupPermissions['abusefilter']['abusefilter-modify'] = true;
	$wgGroupPermissions['abusefilter']['abusefilter-revert'] = true;
	$wgGroupPermissions['autoconfirmed']['abusefilter-log-private'] = true; // Bug 38216

} elseif ( $wgDBname == 'eswiki' ) {
	$wgGroupPermissions['autoconfirmed']['abusefilter-log-detail'] = false; // bug 42012
	$wgGroupPermissions['user']['abusefilter-view'] = true;
	$wgGroupPermissions['user']['abusefilter-log'] = true;
	$wgGroupPermissions['sysop']['abusefilter-modify-restricted'] = true;
	$wgGroupPermissions['sysop']['abusefilter-revert'] = true;
} elseif ( $wgDBname == 'hewiki' ) {
	$wgGroupPermissions['*']['abusefilter-log-detail'] = true;
	$wgGroupPermissions['sysop']['abusefilter-revert'] = true;

} elseif ( $wgDBname == 'hiwiki' ) {
	$wgGroupPermissions['abusefilter']['abusefilter-modify'] = true;
	$wgGroupPermissions['sysop']['abusefilter-revert'] = true;
	$wgGroupPermissions['sysop']['abusefilter-modify'] = false;
	$wgGroupPermissions['*']['abusefilter-log-detail'] = true;
} elseif ( $wgDBname == 'itwiki' ) {
	$wgGroupPermissions['*']['abusefilter-view'] = false;
	$wgGroupPermissions['*']['abusefilter-log'] = false;
	$wgGroupPermissions['autoconfirmed']['abusefilter-log-detail'] = false;
	$wgGroupPermissions['sysop']['abusefilter-log-detail'] = true;
	$wgGroupPermissions['sysop']['abusefilter-view'] = true;
	$wgGroupPermissions['sysop']['abusefilter-log'] = true;
	$wgGroupPermissions['sysop']['abusefilter-revert'] = true;
	$wgGroupPermissions['sysop']['abusefilter-modify-restricted'] = true;
	$wgGroupPermissions['autoconfirmed']['abusefilter-log'] = true;
	$wgGroupPermissions['autoconfirmed']['abusefilter-view'] = true;
	$wgAbuseFilterAvailableActions[] = 'block';
	$wgAbuseFilterBlockDuration = '4 hours';
} elseif ( $wgDBname == 'jawiki' ) {
	$wgGroupPermissions['sysop']['abusefilter-modify'] = false;
	$wgGroupPermissions['abusefilter']['abusefilter-modify'] = true;
	$wgGroupPermissions['*']['abusefilter-log-detail'] = false;
	$wgGroupPermissions['autoconfirmed']['abusefilter-log-detail'] = false; // bug 42012
	$wgGroupPermissions['abusefilter']['abusefilter-log-detail'] = true;
	$wgGroupPermissions['sysop']['abusefilter-revert'] = true;
	$wgGroupPermissions['sysop']['abusefilter-view-private'] = true;

} elseif ( $wgDBname == 'ltwiki' ) {
	$wgGroupPermissions['*']['abusefilter-log-detail'] = true;
	$wgGroupPermissions['abusefilter']['abusefilter-modify'] = true;

} elseif ( $wgDBname == 'mrwiki' ) {
	// Bug 40611
	$wgGroupPermissions['autoconfirmed']['abusefilter-log-detail'] = true;

} elseif ( $wgDBname == 'nlwiki' ) {
	$wgGroupPermissions['*']['abusefilter-view'] = false;
	$wgGroupPermissions['*']['abusefilter-log'] = false;
	$wgGroupPermissions['sysop']['abusefilter-modify'] = false;
	$wgGroupPermissions['autoconfirmed']['abusefilter-view'] = true;
	$wgGroupPermissions['autoconfirmed']['abusefilter-log'] = true;
	$wgGroupPermissions['autoconfirmed']['abusefilter-log-detail'] = true;
	$wgGroupPermissions['abusefilter']['abusefilter-modify'] = true;
	$wgGroupPermissions['abusefilter']['abusefilter-modify-restricted'] = true;
	$wgGroupPermissions['abusefilter']['abusefilter-revert'] = true;

} elseif ( $wgDBname == 'nowiki' ) {
	$wgGroupPermissions['sysop']['abusefilter-modify'] = false;
	$wgGroupPermissions['abusefilter']['abusefilter-modify'] = true;
	$wgGroupPermissions['autoconfirmed']['abusefilter-log-detail'] = false; // bug 42012

} elseif ( $wgDBname == 'plwiki' ) {
	$wgGroupPermissions['*']['abusefilter-view'] = false;
	$wgGroupPermissions['autoconfirmed']['abusefilter-view'] = true;
	$wgGroupPermissions['sysop']['abusefilter-modify'] = false;
	$wgGroupPermissions['abusefilter']['abusefilter-modify'] = true;
	$wgGroupPermissions['autoconfirmed']['abusefilter-log-detail'] = false; // bug 42012

} elseif ( $wgDBname == 'ptwiktionary' ) {
	$wgGroupPermissions['user']['abusefilter-view'] = true;
	$wgGroupPermissions['user']['abusefilter-log'] = true;
	$wgGroupPermissions['sysop']['abusefilter-modify-restricted'] = true;
	$wgGroupPermissions['sysop']['abusefilter-revert'] = true;
	$wgGroupPermissions['autoconfirmed']['abusefilter-log-detail'] = false; // bug 42012
} elseif ( $wgDBname == 'ruwiki' ) {
	## Scaled back from sysop to autoconfirmed -- bug 17998 -- Andrew 2009-03-16
	## Taken back to * per the same bug reopened -- Andrew 2009-04-24
	$wgGroupPermissions['*']['abusefilter-log-detail'] = true;
} elseif ( $wgDBname == 'ruwikinews' ) {
	$wgGroupPermissions['*']['abusefilter-log-detail'] = true;
} elseif ( $wgDBname == 'rowiki' ) {
	$wgGroupPermissions['sysop']['abusefilter-modify'] = false;
	$wgGroupPermissions['abusefilter']['abusefilter-modify'] = true;
	$wgGroupPermissions['*']['abusefilter-log-detail'] = true;
	$wgGroupPermissions['sysop']['abusefilter-revert'] = true;
	$wgGroupPermissions['sysop']['abusefilter-view-private'] = true;

} elseif ( $wgDBname == 'ruwikisource' ) {
	$wgGroupPermissions['abusefilter']['abusefilter-modify'] = true;
	$wgGroupPermissions['abusefilter']['abusefilter-log-detail'] = true;
	$wgGroupPermissions['autoconfirmed']['abusefilter-log-detail'] = false; // bug 42012
} elseif ( $wgDBname == 'thwiki' ) {
	## http://bugzilla.wikimedia.org/show_bug.cgi?id=28502
	$wgGroupPermissions['*']['abusefilter-view'] = false;
	$wgGroupPermissions['autoconfirmed']['abusefilter-view'] = true;
	$wgGroupPermissions['*']['abusefilter-log'] = false;
	$wgGroupPermissions['autoconfirmed']['abusefilter-log'] = true;
	$wgGroupPermissions['autoconfirmed']['abusefilter-log-detail'] = true; // bug 46154
} elseif ( $wgDBname == 'zh_yuewiki' ) {
	$wgGroupPermissions['*']['abusefilter-log-detail'] = true;
	$wgGroupPermissions['sysop']['abusefilter-revert'] = true;
	$wgGroupPermissions['abusefilter']['abusefilter-modify'] = true;
	$wgGroupPermissions['sysop']['abusefilter-modify'] = false;

} elseif ( $wgDBname == 'ukwiki' ) {
	$wgGroupPermissions['*']['abusefilter-log'] = false;
	$wgGroupPermissions['*']['abusefilter-view'] = false;
	$wgGroupPermissions['autoconfirmed']['abusefilter-view'] = true;
	$wgGroupPermissions['autoconfirmed']['abusefilter-log'] = true;
	$wgGroupPermissions['autoconfirmed']['abusefilter-log-detail'] = true;
	$wgAbuseFilterAvailableActions = array_diff(
		$wgAbuseFilterAvailableActions,
		array( 'blockautopromote' )
	);

} elseif ( $wgDBname == 'zhwiki' ) {
	$wgGroupPermissions['autoconfirmed']['abusefilter-log-detail'] = true;
} elseif ( $wgDBname == 'itwikiquote' ) {
	$wgGroupPermissions['*']['abusefilter-log-detail'] = true;
} elseif ( $wgDBname == 'arwiki' ) {
	$wgGroupPermissions['sysop']['abusefilter-modify'] = false;
	$wgGroupPermissions['abusefilter']['abusefilter-modify'] = true;
	$wgGroupPermissions['*']['abusefilter-log-detail'] = true;
} elseif ( $wgDBname == 'eswikibooks' ) {
	$wgGroupPermissions['*']['abusefilter-view'] = false;
	$wgGroupPermissions['*']['abusefilter-log'] = false;
	$wgGroupPermissions['autoconfirmed']['abusefilter-view'] = true;
	$wgGroupPermissions['autoconfirmed']['abusefilter-log'] = true;
	$wgGroupPermissions['autoconfirmed']['abusefilter-log-detail'] = false; // bug 42012
	$wgGroupPermissions['sysop']['abusefilter-modify-restricted'] = true;
	$wgGroupPermissions['sysop']['abusefilter-revert'] = true;
	$wgGroupPermissions['sysop']['abusefilter-view-private'] = true;
	$wgAbuseFilterAvailableActions[] = 'block';
	$wgAbuseFilterBlockDuration = '24 hours';
} elseif ( $wgDBname == 'ltwiktionary' ) {
	$wgGroupPermissions['*']['abusefilter-log-detail'] = true;
	$wgGroupPermissions['abusefilter']['abusefilter-modify'] = true;
	$wgGroupPermissions['sysop']['abusefilter-modify'] = false;
} elseif ( $wgDBname == 'enwikibooks' ) {
	$wgAbuseFilterAvailableActions = array( 'flag', 'throttle', 'warn', 'disallow', 'blockautopromote', 'tag' );
	$wgGroupPermissions['*']['abusefilter-view'] = false;
	$wgGroupPermissions['*']['abusefilter-log'] = false;
	$wgGroupPermissions['autoconfirmed']['abusefilter-view'] = true;
	$wgGroupPermissions['autoconfirmed']['abusefilter-log'] = true;
	$wgGroupPermissions['autoconfirmed']['abusefilter-log-detail'] = false; // bug 42012
} elseif ( $wgDBname == 'frwiktionary' ) {
	$wgGroupPermissions['abusefilter']['abusefilter-log'] = true;
	$wgGroupPermissions['abusefilter']['abusefilter-view'] = true;
	$wgGroupPermissions['abusefilter']['abusefilter-log-detail'] = true;
	$wgGroupPermissions['abusefilter']['abusefilter-modify'] = true;
	$wgGroupPermissions['abusefilter']['abusefilter-revert'] = true;
	$wgGroupPermissions['abusefilter']['abusefilter-modify-restricted'] = true;
	$wgGroupPermissions['sysop']['abusefilter-revert'] = true;
	$wgGroupPermissions['sysop']['abusefilter-log'] = true;
	$wgGroupPermissions['sysop']['abusefilter-view'] = true;
	$wgGroupPermissions['sysop']['abusefilter-log-detail'] = false;
	$wgGroupPermissions['sysop']['abusefilter-modify'] = false;
	$wgGroupPermissions['autopatrolled']['abusefilter-log'] = true;
	$wgGroupPermissions['autopatrolled']['abusefilter-view'] = true;
	$wgGroupPermissions['patroller']['abusefilter-log'] = true;
	$wgGroupPermissions['patroller']['abusefilter-view'] = true;
	$wgGroupPermissions['*']['abusefilter-view'] = false;
	$wgGroupPermissions['*']['abusefilter-log'] = false;
	$wgGroupPermissions['autoconfirmed']['abusefilter-log-detail'] = false; // bug 42012
} elseif ( $wgDBname == 'metawiki' ) {
	$wgGroupPermissions['*']['abusefilter-log-detail'] = true;
} elseif ( $wgDBname == 'eswiktionary' ) {
	$wgGroupPermissions['*']['abusefilter-log-detail'] = true;

	$wgGroupPermissions['abusefilter']['abusefilter-modify'] = true;

	$wgGroupPermissions['sysop']['abusefilter-modify-restricted'] = true;
	$wgGroupPermissions['sysop']['abusefilter-revert'] = true;

	$wgAbuseFilterAvailableActions = array( 'flag', 'throttle', 'warn', 'disallow', 'tag', 'block' );

	$wgAbuseFilterBlockDuration = '2 days';
} elseif ( $wgDBname == 'eewiki' ) {
	$wgGroupPermissions['*']['abusefilter-view'] = false;
	$wgGroupPermissions['*']['abusefilter-log'] = false;
	$wgGroupPermissions['autoconfirmed']['abusefilter-view'] = true;
	$wgGroupPermissions['autoconfirmed']['abusefilter-log'] = true;
	$wgGroupPermissions['autoconfirmed']['abusefilter-log-detail'] = false; // bug 42012
	$wgGroupPermissions['sysop']['abusefilter-modify-restricted'] = true;
	$wgGroupPermissions['sysop']['abusefilter-revert'] = true;
	$wgGroupPermissions['sysop']['abusefilter-view-private'] = true;
	$wgAbuseFilterAvailableActions[] = 'block';
	$wgAbuseFilterBlockDuration = 'infinite';
} elseif ( $wgDBname == 'mediawikiwiki' ) {
	$wgGroupPermissions['autoconfirmed']['abusefilter-log-detail'] = false; // bug 42012
	$wgGroupPermissions['sysop']['abusefilter-modify-restricted'] = true;
	$wgAbuseFilterAvailableActions[] = 'block';
} elseif ( $wgDBname == 'wikidatawiki' ) {
	$wgAbuseFilterNotifications = "udp"; // bug 45083
	$wgAbuseFilterNotificationsPrivate = true; // bug 45083
} elseif ( $wgDBname == 'urwiki' ) {
	$wgGroupPermissions['abusefilter']['abusefilter-modify'] = true; // bug 45643
}
