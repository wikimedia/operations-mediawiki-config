<?php
# WARNING: This file is publically viewable on the web. Do not put private data here.

# This file is for the default permissions and custom permissions of the AbuseFilter extension.
# You must also set wmgUseAbuseFilter in InitialiseSettings.php
# This file is referenced from an include in CommonSettings.php

$wgGroupPermissions['*']['abusefilter-view'] = true;
$wgGroupPermissions['*']['abusefilter-log'] = true;

$wgGroupPermissions['autoconfirmed']['abusefilter-log-detail'] = true; // bug 42012
// give to sysop to make sure it is always available to them, also on wikis where autoconfirmed does not have this right
$wgGroupPermissions['sysop']['abusefilter-log-detail'] = true;

$wgGroupPermissions['sysop']['abusefilter-modify'] = true;

// leaks IP addresses according to Werdna [TS]
$wgGroupPermissions['sysop']['abusefilter-private'] = false;

// Disable some potentially dangerous actions
$wgAbuseFilterAvailableActions = array_diff(
		$wgAbuseFilterAvailableActions,
		array( 'block', 'rangeblock', 'degroup' ) );

// bug 29922 - Prevent anyone being given the abusefilter-private right by removing it
$wgAvailableRights = array_diff( $wgAvailableRights, array( 'abusefilter-private' ) );

// bug 64255 - Enable logging to irc.wikimedia.org by default
$wgAbuseFilterNotifications = "udp";

// Custom permissions
switch ( $wgDBname ) {
	case 'arwiki':
		$wgGroupPermissions['sysop']['abusefilter-modify'] = false;
		$wgGroupPermissions['abusefilter']['abusefilter-modify'] = true;
		$wgGroupPermissions['*']['abusefilter-log-detail'] = true;
		break;
	case 'be_x_oldwiki':
		$wgGroupPermissions['autoconfirmed']['abusefilter-log'] = true;
		$wgGroupPermissions['autoconfirmed']['abusefilter-log-detail'] = false; // bug 42012
		$wgGroupPermissions['abusefilter']['abusefilter-log-detail'] = true;
		$wgGroupPermissions['abusefilter']['abusefilter-modify'] = true;
		$wgGroupPermissions['abusefilter']['abusefilter-modify-restricted'] = true;
		$wgGroupPermissions['sysop']['abusefilter-modify-restricted'] = true;
		break;
	case 'cawiki':
		$wgGroupPermissions['sysop']['abusefilter-modify'] = false;
		$wgGroupPermissions['abusefilter']['abusefilter-modify'] = true;
		$wgGroupPermissions['abusefilter']['abusefilter-modify-restricted'] = true; // bug 48457
		$wgGroupPermissions['sysop']['abusefilter-view-private'] = true;
		$wgGroupPermissions['*']['abusefilter-view'] = false;
		$wgGroupPermissions['user']['abusefilter-view'] = true;
		$wgAbuseFilterAvailableActions[] = 'block';
		$wgAbuseFilterBlockDuration = '2 hours';
		break;
	case 'ckbwiki':
		$wgGroupPermissions['*']['abusefilter-view'] = false;
		$wgGroupPermissions['*']['abusefilter-log'] = false;
		$wgAbuseFilterNotifications = false;
		$wgGroupPermissions['autoconfirmed']['abusefilter-view'] = true;
		$wgGroupPermissions['autoconfirmed']['abusefilter-log'] = true;
		$wgGroupPermissions['interface_editor']['abusefilter-modify'] = true;
		$wgGroupPermissions['sysop']['abusefilter-modify-restricted'] = true;
		$wgGroupPermissions['sysop']['abusefilter-revert'] = true;
		$wgGroupPermissions['sysop']['abusefilter-view-private'] = true;
		$wgGroupPermissions['sysop']['abusefilter-log-private'] = true;
		break;
	case 'commonswiki':
		$wgGroupPermissions['sysop']['abusefilter-modify-restricted'] = true;
		break;
	case 'cswiktionary':
		$wgAbuseFilterNotificationsPrivate = true;
		break;
	case 'cswikinews':
		$wgAbuseFilterNotificationsPrivate = true;
		break;
	case 'cswikisource':
		$wgAbuseFilterNotificationsPrivate = true;
		break;
	case 'eewiki':
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
		$wgAbuseFilterNotifications = false;
		break;
	case 'elwiki':
		$wgGroupPermissions['*']['abusefilter-log-detail'] = true;
		$wgGroupPermissions['sysop']['abusefilter-revert'] = true;
		break;
	case 'enwiki':
		$wgGroupPermissions['sysop']['abusefilter-modify'] = false;
		$wgGroupPermissions['abusefilter']['abusefilter-modify'] = true;
		$wgGroupPermissions['*']['abusefilter-log-detail'] = true;
		$wgGroupPermissions['sysop']['abusefilter-revert'] = true;
		$wgGroupPermissions['sysop']['abusefilter-view-private'] = true;
		$wgAbuseFilterNotificationsPrivate = true; // bug 44045
		break;
	case 'enwikibooks':
		$wgAbuseFilterAvailableActions = array( 'flag', 'throttle', 'warn', 'disallow', 'blockautopromote', 'tag' );
		$wgGroupPermissions['*']['abusefilter-view'] = false;
		$wgGroupPermissions['*']['abusefilter-log'] = false;
		$wgAbuseFilterNotifications = false;
		$wgGroupPermissions['autoconfirmed']['abusefilter-view'] = true;
		$wgGroupPermissions['autoconfirmed']['abusefilter-log'] = true;
		$wgGroupPermissions['autoconfirmed']['abusefilter-log-detail'] = false; // bug 42012
		break;
	case 'enwikinews':
		$wgAbuseFilterAvailableActions[] = 'block'; // bug 55868
		break;
	case 'enwikisource':
		$wgGroupPermissions['sysop']['abusefilter-modify'] = false;
		$wgGroupPermissions['abusefilter']['abusefilter-modify'] = true;
		$wgGroupPermissions['autoconfirmed']['abusefilter-log-detail'] = false; // bug 42012
		break;
	case 'eswiki':
		$wgGroupPermissions['autoconfirmed']['abusefilter-log-detail'] = false; // bug 42012
		$wgGroupPermissions['user']['abusefilter-log'] = true;
		$wgGroupPermissions['sysop']['abusefilter-modify-restricted'] = true;
		$wgGroupPermissions['sysop']['abusefilter-revert'] = true;
		break;
	case 'eswikibooks':
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
		$wgAbuseFilterNotifications = false;
		break;
	case 'eswiktionary':
		$wgGroupPermissions['*']['abusefilter-log-detail'] = true;
		$wgGroupPermissions['abusefilter']['abusefilter-modify'] = true;
		$wgGroupPermissions['sysop']['abusefilter-modify-restricted'] = true;
		$wgGroupPermissions['sysop']['abusefilter-revert'] = true;
		$wgAbuseFilterAvailableActions = array( 'flag', 'throttle', 'warn', 'disallow', 'tag', 'block' );
		$wgAbuseFilterBlockDuration = '2 days';
		break;
	case 'eswikivoyage': // bug 62321
		$wgAbuseFilterAvailableActions[] = 'block';
		$wgAbuseFilterBlockDuration = '24 hours';
		break;
	case 'fawiki':
		$wgGroupPermissions['*']['abusefilter-log'] = false;
		$wgGroupPermissions['*']['abusefilter-view'] = false;
		$wgGroupPermissions['autoconfirmed']['abusefilter-log-detail'] = false;
		$wgGroupPermissions['patroller']['abusefilter-log'] = true;
		$wgGroupPermissions['patroller']['abusefilter-view'] = true;
		$wgGroupPermissions['patroller']['abusefilter-log-detail'] = true;
		$wgGroupPermissions['autopatrol']['abusefilter-log'] = true;
		$wgGroupPermissions['autopatrol']['abusefilter-view'] = true;
		$wgGroupPermissions['autopatrol']['abusefilter-log-detail'] = true;
		$wgGroupPermissions['sysop']['abusefilter-log'] = true;
		$wgGroupPermissions['sysop']['abusefilter-log-private'] = true;
		$wgGroupPermissions['sysop']['abusefilter-view'] = true;
		$wgGroupPermissions['sysop']['abusefilter-view-private'] = true;
		$wgGroupPermissions['sysop']['abusefilter-revert'] = true;
		$wgGroupPermissions['sysop']['abusefilter-modify-restricted'] = true;
		$wgAbuseFilterAvailableActions[] =  array( 'flag', 'throttle', 'warn', 'disallow', 'blockautopromote', 'block', 'degroup', 'tag', 'rangeblock' );
		$wgAbuseFilterAnonBlockDuration = '3 months';
		$wgAbuseFilterBlockDuration = 'indefinite';
		$wgAbuseFilterNotifications = false;
		break;
	case 'fiwiki': // bug 57395
		$wgGroupPermissions['sysop']['abusefilter-modify-restricted'] = true;
		$wgGroupPermissions['sysop']['abusefilter-revert'] = true;
		break;
	case 'frwiki':
	case 'frwikibooks': // wikibooks by bug 26142
		// !!! Please be careful if you edit these rules, they are shared
		// by two wikis. Create two sections, one per wiki, if needed. !!!
		$wgGroupPermissions['*']['abusefilter-view'] = false;
		$wgGroupPermissions['autoconfirmed']['abusefilter-view'] = true;
		$wgGroupPermissions['sysop']['abusefilter-revert'] = true;
		$wgGroupPermissions['sysop']['abusefilter-modify'] = false;
		$wgGroupPermissions['abusefilter']['abusefilter-modify'] = true;
		$wgGroupPermissions['abusefilter']['abusefilter-revert'] = true;
		$wgGroupPermissions['autoconfirmed']['abusefilter-log-private'] = true; // bug 38216
		break;
	case 'frwiktionary':
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
		$wgAbuseFilterNotifications = false;
		break;
	case 'hewiki':
		$wgGroupPermissions['*']['abusefilter-log-detail'] = true;
		$wgGroupPermissions['sysop']['abusefilter-revert'] = true;
		break;
	case 'hiwiki':
		$wgGroupPermissions['abusefilter']['abusefilter-modify'] = true;
		$wgGroupPermissions['sysop']['abusefilter-revert'] = true;
		$wgGroupPermissions['sysop']['abusefilter-modify'] = false;
		$wgGroupPermissions['*']['abusefilter-log-detail'] = true;
		break;
	case 'itwiki':
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
		$wgAbuseFilterNotifications = false;
		break;
	case 'itwikinews':
		$wgAbuseFilterAvailableActions[] = 'block';
		$wgAbuseFilterBlockDuration = '24 hours';
		break;
	case 'itwikiquote':
		$wgGroupPermissions['*']['abusefilter-log-detail'] = true;
		break;
	case 'jawiki':
		$wgGroupPermissions['sysop']['abusefilter-modify'] = false;
		$wgGroupPermissions['abusefilter']['abusefilter-modify'] = true;
		$wgGroupPermissions['*']['abusefilter-log-detail'] = false;
		$wgGroupPermissions['autoconfirmed']['abusefilter-log-detail'] = false; // bug 42012
		$wgGroupPermissions['abusefilter']['abusefilter-log-detail'] = true;
		$wgGroupPermissions['sysop']['abusefilter-revert'] = true;
		$wgGroupPermissions['sysop']['abusefilter-view-private'] = true;
		break;
	case 'ltwiki':
		$wgGroupPermissions['*']['abusefilter-log-detail'] = true;
		$wgGroupPermissions['abusefilter']['abusefilter-modify'] = true;
		break;
	case 'ltwiktionary':
		$wgGroupPermissions['*']['abusefilter-log-detail'] = true;
		$wgGroupPermissions['abusefilter']['abusefilter-modify'] = true;
		$wgGroupPermissions['sysop']['abusefilter-modify'] = false;
		break;
	case 'mediawikiwiki':
		$wgGroupPermissions['autoconfirmed']['abusefilter-log-detail'] = false; // bug 42012
		$wgGroupPermissions['sysop']['abusefilter-modify-restricted'] = true;
		$wgAbuseFilterAvailableActions[] = 'block';
		break;
	case 'metawiki':
		$wgGroupPermissions['*']['abusefilter-log-detail'] = true;
		$wgAbuseFilterAvailableActions[] = 'block'; // bug 52681
		break;
	case 'mrwiki':
		$wgGroupPermissions['autoconfirmed']['abusefilter-log-detail'] = true; // bug 40611
		break;
	case 'nlwiki':
		$wgGroupPermissions['*']['abusefilter-view'] = false;
		$wgGroupPermissions['*']['abusefilter-log'] = false;
		$wgGroupPermissions['sysop']['abusefilter-modify'] = false;
		$wgGroupPermissions['autoconfirmed']['abusefilter-view'] = true;
		$wgGroupPermissions['autoconfirmed']['abusefilter-log'] = true;
		$wgGroupPermissions['abusefilter']['abusefilter-modify'] = true;
		$wgGroupPermissions['abusefilter']['abusefilter-modify-restricted'] = true;
		$wgGroupPermissions['abusefilter']['abusefilter-revert'] = true;
		$wgAbuseFilterNotifications = false;
		break;
	case 'nowiki':
		$wgGroupPermissions['sysop']['abusefilter-modify'] = false;
		$wgGroupPermissions['abusefilter']['abusefilter-modify'] = true;
		$wgGroupPermissions['autoconfirmed']['abusefilter-log-detail'] = false; // bug 42012
		break;
	case 'plwiki':
		$wgGroupPermissions['*']['abusefilter-view'] = false;
		$wgGroupPermissions['autoconfirmed']['abusefilter-view'] = true;
		$wgGroupPermissions['sysop']['abusefilter-modify'] = false;
		$wgGroupPermissions['abusefilter']['abusefilter-modify'] = true;
		$wgGroupPermissions['autoconfirmed']['abusefilter-log-detail'] = false; // bug 42012
		break;
	case 'ptwiktionary':
		$wgGroupPermissions['sysop']['abusefilter-modify-restricted'] = true;
		$wgGroupPermissions['sysop']['abusefilter-revert'] = true;
		$wgGroupPermissions['autoconfirmed']['abusefilter-log-detail'] = false; // bug 42012
		break;
	case 'rowiki':
		$wgGroupPermissions['sysop']['abusefilter-modify'] = false;
		$wgGroupPermissions['abusefilter']['abusefilter-modify'] = true;
		$wgGroupPermissions['*']['abusefilter-log-detail'] = true;
		$wgGroupPermissions['sysop']['abusefilter-revert'] = true;
		$wgGroupPermissions['sysop']['abusefilter-view-private'] = true;
		break;
	case 'ruwiki':
		## Scaled back from sysop to autoconfirmed -- bug 17998 -- Andrew 2009-03-16
		## Taken back to * per the same bug reopened -- Andrew 2009-04-24
		$wgGroupPermissions['*']['abusefilter-log-detail'] = true;
		break;
	case 'ruwikinews':
		$wgGroupPermissions['*']['abusefilter-log-detail'] = true;
		break;
	case 'ruwikisource':
		$wgGroupPermissions['abusefilter']['abusefilter-modify'] = true;
		$wgGroupPermissions['abusefilter']['abusefilter-log-detail'] = true;
		$wgGroupPermissions['autoconfirmed']['abusefilter-log-detail'] = false; // bug 42012
		break;
	case 'thwiki':
		// bug 28502
		$wgGroupPermissions['*']['abusefilter-view'] = false;
		$wgGroupPermissions['autoconfirmed']['abusefilter-view'] = true;
		$wgGroupPermissions['*']['abusefilter-log'] = false;
		$wgGroupPermissions['autoconfirmed']['abusefilter-log'] = true;
		$wgGroupPermissions['autoconfirmed']['abusefilter-log-detail'] = true; // bug 46154
		$wgAbuseFilterNotifications = false;
		break;
	case 'ukwiki':
		$wgGroupPermissions['*']['abusefilter-log'] = false;
		$wgGroupPermissions['*']['abusefilter-view'] = false;
		$wgGroupPermissions['autoconfirmed']['abusefilter-view'] = true;
		$wgGroupPermissions['autoconfirmed']['abusefilter-log'] = true;
		$wgAbuseFilterAvailableActions = array_diff(
			$wgAbuseFilterAvailableActions,
			array( 'blockautopromote' )
		);
		$wgAbuseFilterNotifications = false;
		break;
	case 'urwiki':
		$wgGroupPermissions['abusefilter']['abusefilter-modify'] = true; // bug 45643
		break;
	case 'wikidatawiki':
		$wgAbuseFilterNotificationsPrivate = true; // bug 45083
		$wgAbuseFilterAvailableActions[] = 'block'; // bug 57681
		$wgAbuseFilterBlockDuration = 'indefinite'; // bug 57681
		$wgAbuseFilterAnonBlockDuration = '3 months'; // bug 57681
		$wgGroupPermissions['sysop']['abusefilter-modify-restricted'] = true; // bug 57681
		break;
	case 'zh_yuewiki':
		$wgGroupPermissions['*']['abusefilter-log-detail'] = true;
		$wgGroupPermissions['sysop']['abusefilter-revert'] = true;
		$wgGroupPermissions['abusefilter']['abusefilter-modify'] = true;
		$wgGroupPermissions['sysop']['abusefilter-modify'] = false;
		break;
// Please add new wikis in their correct place in alphabetical order!
}
