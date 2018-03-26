<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

# This file is for the default permissions and custom permissions
# of the AbuseFilter extension.
#
# NOTE: Included for all wikis.
#
# Load tree:
#  |-- wmf-config/CommonSettings.php
#      |
#      `-- wmf-config/abusefilter.php
#

$wgGroupPermissions['*']['abusefilter-view'] = true;
$wgGroupPermissions['*']['abusefilter-log'] = true;
$wgGroupPermissions['autoconfirmed']['abusefilter-log-detail'] = true; // T44012

// give to sysop to make sure it is always available to them, also on wikis where autoconfirmed does not have this right
$wgGroupPermissions['sysop']['abusefilter-log-detail'] = true;
$wgGroupPermissions['sysop']['abusefilter-modify'] = true;

// Provides access to private information - T160357
// Only CheckUsers/Stewards/Staff and Ombudsmen allowed.
$wgGroupPermissions['sysop']['abusefilter-private'] = false;
$wgGroupPermissions['sysop']['abusefilter-private-log'] = false;

// T160357 - Log accessing private abusefilter details
$wgAbuseFilterPrivateLog = true;

// T160357 - Allow those with CheckUser right to access AbuseLog private information on WMF projects and its log
// Only CheckUsers/Stewards/Staff and Ombudsmen allowed
$wgGroupPermissions['checkuser']['abusefilter-private'] = true;
$wgGroupPermissions['checkuser']['abusefilter-private-log'] = true;

// Disable some potentially dangerous actions
$wgAbuseFilterActions = [
	'block' => false,
	'rangeblock' => false,
	'degroup' => false,
];

// T66255 - Enable logging to irc.wikimedia.org by default
$wgAbuseFilterNotifications = "udp";

// T113164 Change default AbuseFilter IP block duration to not indefinite
$wgAbuseFilterAnonBlockDuration = '1 week';

$wgAbuseFilterSlowFilterRuntimeLimit = 800;

// Custom permissions
switch ( $wgDBname ) {
	case 'arwiki':
		$wgGroupPermissions['sysop']['abusefilter-modify'] = false;
		$wgGroupPermissions['abusefilter']['abusefilter-modify'] = true;
		$wgGroupPermissions['*']['abusefilter-log-detail'] = true;
		break;
	case 'azbwiki': // T109755
		$wgGroupPermissions['abusefilter']['abusefilter-log'] = true;
		$wgGroupPermissions['abusefilter']['abusefilter-log-detail'] = true;
		$wgGroupPermissions['abusefilter']['abusefilter-modify'] = true;
		$wgGroupPermissions['abusefilter']['abusefilter-modify-restricted'] = true;
		$wgGroupPermissions['abusefilter']['abusefilter-revert'] = true;
		$wgGroupPermissions['abusefilter']['abusefilter-view'] = true;
		$wgGroupPermissions['abusefilter']['abusefilter-view-private'] = true;
		break;
	case 'be_x_oldwiki':
		$wgGroupPermissions['autoconfirmed']['abusefilter-log'] = true;
		$wgGroupPermissions['autoconfirmed']['abusefilter-log-detail'] = false; // T44012
		$wgGroupPermissions['abusefilter']['abusefilter-log-detail'] = true;
		$wgGroupPermissions['abusefilter']['abusefilter-modify'] = true;
		$wgGroupPermissions['abusefilter']['abusefilter-modify-restricted'] = true;
		$wgGroupPermissions['sysop']['abusefilter-modify-restricted'] = true;
		break;
	case 'cawiki':
		$wgGroupPermissions['sysop']['abusefilter-modify'] = false;
		$wgGroupPermissions['abusefilter']['abusefilter-modify'] = true;
		$wgGroupPermissions['abusefilter']['abusefilter-modify-restricted'] = true; // T50457
		$wgGroupPermissions['sysop']['abusefilter-view-private'] = true;
		$wgGroupPermissions['*']['abusefilter-view'] = false;
		$wgGroupPermissions['user']['abusefilter-view'] = true;
		$wgAbuseFilterActions['block'] = true;
		$wgAbuseFilterBlockDuration = '2 hours';
		$wgAbuseFilterAnonBlockDuration = '2 hours';
		break;
	case 'ckbwiki':
		$wgGroupPermissions['*']['abusefilter-view'] = false;
		$wgGroupPermissions['*']['abusefilter-log'] = false;
		$wgAbuseFilterNotifications = false;
		$wgGroupPermissions['autoconfirmed']['abusefilter-view'] = true;
		$wgGroupPermissions['autoconfirmed']['abusefilter-log'] = true;
		$wgGroupPermissions['interface-editor']['abusefilter-modify'] = true;
		$wgGroupPermissions['sysop']['abusefilter-modify-restricted'] = true;
		$wgGroupPermissions['sysop']['abusefilter-revert'] = true;
		$wgGroupPermissions['sysop']['abusefilter-view-private'] = true;
		$wgGroupPermissions['sysop']['abusefilter-log-private'] = true;
		break;
	case 'commonswiki':
		$wgAbuseFilterConditionLimit = 2000; // T132048
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
	case 'elwiki':
		$wgGroupPermissions['*']['abusefilter-log-detail'] = true;
		$wgGroupPermissions['sysop']['abusefilter-revert'] = true;
		break;
	case 'enwiki':
		$wgGroupPermissions['sysop']['abusefilter-modify'] = false;
		$wgGroupPermissions['abusefilter']['abusefilter-modify'] = true;
		$wgGroupPermissions['abusefilter']['changetags'] = true; // T97013
		$wgGroupPermissions['abusefilter']['managechangetags'] = true; // T141847
		$wgGroupPermissions['*']['abusefilter-log-detail'] = true;
		$wgGroupPermissions['sysop']['abusefilter-revert'] = true;
		$wgGroupPermissions['sysop']['abusefilter-view-private'] = true;
		$wgGroupPermissions['abusefilter-helper']['abusefilter-view-private'] = true; // T175684
		$wgAbuseFilterNotificationsPrivate = true; // T46045
		$wgAbuseFilterProfile = true;
		break;
	case 'enwikibooks':
		$wgGroupPermissions['*']['abusefilter-view'] = false;
		$wgGroupPermissions['*']['abusefilter-log'] = false;
		$wgAbuseFilterNotifications = false;
		$wgGroupPermissions['autoconfirmed']['abusefilter-view'] = true;
		$wgGroupPermissions['autoconfirmed']['abusefilter-log'] = true;
		$wgGroupPermissions['autoconfirmed']['abusefilter-log-detail'] = false; // T44012
		break;
	case 'enwikinews':
		$wgAbuseFilterActions['block'] = true; // T57868
		break;
	case 'enwikisource':
		$wgGroupPermissions['sysop']['abusefilter-modify'] = false;
		$wgGroupPermissions['abusefilter']['abusefilter-modify'] = true;
		$wgGroupPermissions['autoconfirmed']['abusefilter-log-detail'] = false; // T44012
		break;
	case 'eswiki':
		$wgGroupPermissions['autoconfirmed']['abusefilter-log-detail'] = false; // T44012
		$wgGroupPermissions['user']['abusefilter-log'] = true;
		$wgGroupPermissions['sysop']['abusefilter-modify-restricted'] = true;
		$wgGroupPermissions['sysop']['abusefilter-revert'] = true;
		$wgAbuseFilterProfile = true; // T152087
		break;
	case 'eswikibooks':
		$wgGroupPermissions['*']['abusefilter-view'] = false;
		$wgGroupPermissions['*']['abusefilter-log'] = false;
		$wgGroupPermissions['autoconfirmed']['abusefilter-view'] = true;
		$wgGroupPermissions['autoconfirmed']['abusefilter-log'] = true;
		$wgGroupPermissions['autoconfirmed']['abusefilter-log-detail'] = false; // T44012
		$wgGroupPermissions['sysop']['abusefilter-modify-restricted'] = true;
		$wgGroupPermissions['sysop']['abusefilter-revert'] = true;
		$wgGroupPermissions['sysop']['abusefilter-view-private'] = true;
		$wgAbuseFilterActions['block'] = true;
		$wgAbuseFilterBlockDuration = 'indefinite'; // T96669
		$wgAbuseFilterAnonBlockDuration = '31 hours'; // T96669
		$wgAbuseFilterNotificationsPrivate = true; // T147744
		$wgAbuseFilterProfile = true; // T190264
		break;
	case 'eswikiquote': // T177760, T177761
		$wgGroupPermissions['sysop']['abusefilter-modify-restricted'] = true;
		$wgGroupPermissions['sysop']['abusefilter-revert'] = true;
		$wgGroupPermissions['sysop']['abusefilter-view-private'] = true;
		$wgAbuseFilterActions['block'] = true;
		$wgAbuseFilterBlockDuration = '24 hours';
		$wgAbuseFilterAnonBlockDuration = '24 hours';
		$wgAbuseFilterNotificationsPrivate = true;
		$wgAbuseFilterProfile = true;
		break;
	case 'eswiktionary':
		$wgGroupPermissions['*']['abusefilter-log-detail'] = true;
		$wgGroupPermissions['abusefilter']['abusefilter-modify'] = true;
		$wgGroupPermissions['sysop']['abusefilter-modify-restricted'] = true;
		$wgGroupPermissions['sysop']['abusefilter-revert'] = true;
		$wgAbuseFilterActions['block'] = true;
		$wgAbuseFilterActions['blockautopromote'] = false;
		$wgAbuseFilterBlockDuration = '2 days';
		$wgAbuseFilterAnonBlockDuration = '2 days';
		break;
	case 'eswikivoyage': // T64321
		$wgGroupPermissions['sysop']['abusefilter-modify-restricted'] = true;
		$wgAbuseFilterActions['block'] = true;
		$wgAbuseFilterBlockDuration = '24 hours';
		$wgAbuseFilterAnonBlockDuration = '24 hours';
		break;
	case 'fawiki': // T71073 and T74502
		$wgGroupPermissions['*']['abusefilter-log'] = false;
		$wgGroupPermissions['*']['abusefilter-view'] = false;
		$wgGroupPermissions['autoconfirmed']['abusefilter-log-detail'] = false;
		$wgGroupPermissions['patroller']['abusefilter-log'] = true;
		$wgGroupPermissions['patroller']['abusefilter-view'] = true;
		$wgGroupPermissions['patroller']['abusefilter-log-detail'] = true;
		$wgGroupPermissions['autopatrolled']['abusefilter-log'] = true;
		$wgGroupPermissions['autopatrolled']['abusefilter-view'] = true;
		$wgGroupPermissions['autopatrolled']['abusefilter-log-detail'] = true;
		$wgGroupPermissions['abusefilter']['abusefilter-log'] = true;
		$wgGroupPermissions['abusefilter']['abusefilter-log-detail'] = true;
		$wgGroupPermissions['abusefilter']['abusefilter-log-private'] = true;
		$wgGroupPermissions['abusefilter']['abusefilter-view'] = true;
		$wgGroupPermissions['abusefilter']['abusefilter-view-private'] = true;
		$wgGroupPermissions['abusefilter']['abusefilter-revert'] = true;
		$wgGroupPermissions['abusefilter']['abusefilter-modify'] = true;
		$wgGroupPermissions['abusefilter']['abusefilter-modify-restricted'] = true;
		$wgGroupPermissions['sysop']['abusefilter-log'] = true;
		$wgGroupPermissions['sysop']['abusefilter-log-private'] = true;
		$wgGroupPermissions['sysop']['abusefilter-view'] = true;
		$wgGroupPermissions['sysop']['abusefilter-view-private'] = true;
		$wgGroupPermissions['sysop']['abusefilter-revert'] = true;
		$wgGroupPermissions['sysop']['abusefilter-modify-restricted'] = true;
		$wgAbuseFilterActions = [ 'rangeblock' => true ];
		$wgAbuseFilterAnonBlockDuration = '7 days'; // T87317
		$wgAbuseFilterBlockDuration = '14 days'; // T167562
		$wgAbuseFilterNotifications = false;
		break;
	case 'fawikiquote': // T178227
		$wgGroupPermissions['sysop']['abusefilter-modify-restricted'] = true;
		$wgAbuseFilterActions = [ 'rangeblock' => true ];
		$wgAbuseFilterBlockDuration = '14 days';
		$wgAbuseFilterAnonBlockDuration = '7 days';
		break;
	case 'fiwiki': // T59395
		$wgGroupPermissions['sysop']['abusefilter-modify-restricted'] = true;
		$wgGroupPermissions['sysop']['abusefilter-revert'] = true;
		break;
	case 'frwiki':
	case 'frwikibooks': // wikibooks by T28142
		// !!! Please be careful if you edit these rules, they are shared
		// by two wikis. Create two sections, one per wiki, if needed. !!!
		$wgGroupPermissions['*']['abusefilter-view'] = false;
		$wgGroupPermissions['autoconfirmed']['abusefilter-view'] = true;
		$wgGroupPermissions['sysop']['abusefilter-revert'] = true;
		$wgGroupPermissions['sysop']['abusefilter-modify'] = false;
		$wgGroupPermissions['abusefilter']['abusefilter-modify'] = true;
		$wgGroupPermissions['abusefilter']['abusefilter-revert'] = true;
		$wgGroupPermissions['autoconfirmed']['abusefilter-log-private'] = true; // T40216
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
		$wgGroupPermissions['autoconfirmed']['abusefilter-log-detail'] = false; // T44012
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
	case 'idwiki':
		$wgGroupPermissions['sysop']['abusefilter-modify-restricted'] = true; // T96542
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
		$wgAbuseFilterActions['block'] = true;
		$wgAbuseFilterBlockDuration = '4 hours';
		$wgAbuseFilterAnonBlockDuration = '4 hours';
		$wgAbuseFilterNotifications = false;
		break;
	case 'itwikinews':
		$wgAbuseFilterActions['block'] = true;
		$wgAbuseFilterBlockDuration = '24 hours';
		$wgAbuseFilterAnonBlockDuration = '24 hours';
		break;
	case 'itwikiquote':
		$wgGroupPermissions['*']['abusefilter-log-detail'] = true;
		break;
	case 'jawiki':
		$wgGroupPermissions['sysop']['abusefilter-modify'] = false;
		$wgGroupPermissions['abusefilter']['abusefilter-modify'] = true;
		$wgGroupPermissions['*']['abusefilter-log-detail'] = false;
		$wgGroupPermissions['autoconfirmed']['abusefilter-log-detail'] = false; // T44012
		$wgGroupPermissions['abusefilter']['abusefilter-log-detail'] = true;
		$wgGroupPermissions['sysop']['abusefilter-revert'] = true;
		$wgGroupPermissions['sysop']['abusefilter-view-private'] = true;
		break;
	case 'labswiki': // wikitech.wikimedia.org
		$wgGroupPermissions['sysop']['abusefilter-modify-restricted'] = true;
		$wgGroupPermissions['sysop']['abusefilter-revert'] = true;
		$wgGroupPermissions['sysop']['abusefilter-view-private'] = true;
		$wgAbuseFilterActions['block'] = true;
		$wgAbuseFilterBlockDuration = 'indefinite';
		$wgAbuseFilterAnonBlockDuration = '31 hours';
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
		$wgGroupPermissions['autoconfirmed']['abusefilter-log-detail'] = false; // T44012
		$wgGroupPermissions['sysop']['abusefilter-modify-restricted'] = true;
		$wgAbuseFilterActions['block'] = true;
		$wgAbuseFilterAnonBlockDuration = '3 months'; // T72828
		break;
	case 'metawiki':
		$wgGroupPermissions['*']['abusefilter-log-detail'] = true;
		$wgGroupPermissions['sysop']['abusefilter-modify-restricted'] = true; // T76270
		$wgGroupPermissions['sysop']['abusefilter-revert'] = true; // T76270
		$wgGroupPermissions['steward']['abusefilter-modify-global'] = true; // T150752
		$wgGroupPermissions['steward']['abusefilter-private'] = true; // T160357
		$wgAbuseFilterActions['block'] = true; // T54681
		$wgAbuseFilterAnonBlockDuration = '31 hours'; // T76270
		$wgAbuseFilterProfile = true; // T149901
		$wgAbuseFilterNotificationsPrivate = true; // T154358
		break;
	case 'mrwiki':
		$wgGroupPermissions['autoconfirmed']['abusefilter-log-detail'] = true; // T42611
		break;
	case 'newiki':
		$wgGroupPermissions['abusefilter']['abusefilter-modify'] = true; // T95102
		$wgGroupPermissions['abusefilter']['abusefilter-revert'] = true; // T95102
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
		$wgGroupPermissions['autoconfirmed']['abusefilter-log-detail'] = false; // T44012
		break;
	case 'plwiki':
		$wgGroupPermissions['*']['abusefilter-view'] = false;
		$wgGroupPermissions['autoconfirmed']['abusefilter-view'] = true;
		$wgGroupPermissions['sysop']['abusefilter-modify'] = false;
		$wgGroupPermissions['abusefilter']['abusefilter-modify'] = true;
		$wgGroupPermissions['autoconfirmed']['abusefilter-log-detail'] = false; // T44012
		break;
	case 'ptwiki':
		$wgAbuseFilterProfile = true;
		break;
	case 'ptwiktionary':
		$wgGroupPermissions['sysop']['abusefilter-modify-restricted'] = true;
		$wgGroupPermissions['sysop']['abusefilter-revert'] = true;
		$wgGroupPermissions['autoconfirmed']['abusefilter-log-detail'] = false; // T44012
		$wgAbuseFilterActions['block'] = true; // T134779
		$wgAbuseFilterAnonBlockDuration = '3 days'; // T134779
		break;
	case 'rowiki':
		$wgGroupPermissions['sysop']['abusefilter-modify'] = false;
		$wgGroupPermissions['abusefilter']['abusefilter-modify'] = true;
		$wgGroupPermissions['*']['abusefilter-log-detail'] = true;
		$wgGroupPermissions['sysop']['abusefilter-revert'] = true;
		$wgGroupPermissions['sysop']['abusefilter-view-private'] = true;
		break;
	case 'ruwiki':
		## Scaled back from sysop to autoconfirmed -- T19998 -- Andrew 2009-03-16
		## Taken back to * per the same bug reopened -- Andrew 2009-04-24
		$wgGroupPermissions['*']['abusefilter-log-detail'] = true;
		break;
	case 'ruwikinews':
		$wgGroupPermissions['*']['abusefilter-log-detail'] = true;
		break;
	case 'ruwikisource':
		$wgGroupPermissions['abusefilter']['abusefilter-modify'] = true;
		$wgGroupPermissions['abusefilter']['abusefilter-log-detail'] = true;
		$wgGroupPermissions['autoconfirmed']['abusefilter-log-detail'] = false; // T44012
		break;
	case 'thwiki':
		// T30502
		$wgGroupPermissions['*']['abusefilter-view'] = false;
		$wgGroupPermissions['autoconfirmed']['abusefilter-view'] = true;
		$wgGroupPermissions['*']['abusefilter-log'] = false;
		$wgGroupPermissions['autoconfirmed']['abusefilter-log'] = true;
		$wgGroupPermissions['autoconfirmed']['abusefilter-log-detail'] = true; // T48154
		$wgAbuseFilterNotifications = false;
		break;
	case 'trwiki':
		$wgGroupPermissions['interface-editor']['abusefilter-modify-restricted'] = true; // T161960
		$wgGroupPermissions['sysop']['abusefilter-modify-restricted'] = true; // T161960
		$wgAbuseFilterActions['block'] = true; // T161960
		$wgAbuseFilterBlockDuration = 'indefinite'; // T161960
		$wgAbuseFilterAnonBlockDuration = '24 hours';
		break;
	case 'ukwiki':
		$wgGroupPermissions['*']['abusefilter-log'] = false;
		$wgGroupPermissions['*']['abusefilter-view'] = false;
		$wgGroupPermissions['autoconfirmed']['abusefilter-view'] = true;
		$wgGroupPermissions['autoconfirmed']['abusefilter-log'] = true;
		$wgGroupPermissions['sysop']['abusefilter-modify-restricted'] = true; // T89379
		$wgAbuseFilterActions['blockautopromote'] = false;
		$wgAbuseFilterActions['block'] = true; // T89379
		$wgAbuseFilterBlockDuration = '2 hours';    // T89379
		$wgAbuseFilterAnonBlockDuration = '2 hours';
		$wgAbuseFilterNotifications = false;
		break;
	case 'urwiki':
		$wgGroupPermissions['abusefilter']['abusefilter-modify'] = true; // T47643
		break;
	case 'wikidatawiki':
		$wgAbuseFilterNotificationsPrivate = true; // T47083
		$wgAbuseFilterActions['block'] = true; // T59681
		$wgAbuseFilterBlockDuration = 'indefinite'; // T59681
		$wgAbuseFilterAnonBlockDuration = '3 months'; // T59681
		$wgGroupPermissions['sysop']['abusefilter-modify-restricted'] = true; // T59681
		break;
	case 'zhwiki':
		$wgGroupPermissions['sysop']['abusefilter-modify-restricted'] = true; // T73854
		break;
	case 'zh_yuewiki':
		$wgGroupPermissions['*']['abusefilter-log-detail'] = true;
		$wgGroupPermissions['sysop']['abusefilter-revert'] = true;
		$wgGroupPermissions['abusefilter']['abusefilter-modify'] = true;
		$wgGroupPermissions['sysop']['abusefilter-modify'] = false;
		break;
// Please add new wikis in their correct place in alphabetical order!
}

if ( $wmfRealm === 'labs' ) {
	// T103060
	$wgAbuseFilterActions['block'] = true;
	$wgAbuseFilterBlockDuration = 'indefinite';
	$wgAbuseFilterAnonBlockDuration = '48 hours';
	$wgAbuseFilterParserClass = 'AbuseFilterCachingParser';
	$wgAbuseFilterLogIP = false; // Prevent the collection of IP addresses - T188862
	$wgAbuseFilterLogIPMaxAge = 1; // Purge data older than 1 second (hack to clean all data)
}
