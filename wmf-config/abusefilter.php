<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

# This file is for settings (including permissions) of the AbuseFilter extension.
#
# NOTE: Included for all wikis.
#
# Load tree:
#  |-- wmf-config/CommonSettings.php
#      |
#      `-- wmf-config/abusefilter.php
#

$wgGroupPermissions['autoconfirmed']['abusefilter-log-detail'] = true; // T44012
$wgGroupPermissions['oversight']['abusefilter-hide-log'] = true;
$wgGroupPermissions['oversight']['abusefilter-hidden-log'] = true;

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
$wgAbuseFilterRuntimeProfile = false;

// Disable dangerous actions for filters matching more than 2 actions, constituting
// more than >5% of the last actions, if the filter was modified in the last day.
$wgAbuseFilterEmergencyDisableThreshold['default'] = 0.05;
$wgAbuseFilterEmergencyDisableCount['default'] = 2;
$wgAbuseFilterEmergencyDisableAge['default'] = 86400;

$wgAbuseFilterParserClass = 'AbuseFilterParser';

// Global filters, currently enabled on small and medium wikis, plus some extras
$wmgUseGlobalAbuseFilters = false;
$wmgAbuseFilterCentralDB = 'metawiki';
$smallWikis = MWWikiversions::readDbListFile( 'small' );
$mediumWikis = MWWikiversions::readDbListFile( 'medium' );
if ( in_array( $wgDBname, $smallWikis ) | in_array( $wgDBname, $mediumWikis ) ) {
	$wmgUseGlobalAbuseFilters = true;
}

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
		$wgAbuseFilterEmergencyDisableThreshold['default'] = 0.30; // T87431
		$wgAbuseFilterEmergencyDisableCount['default'] = 25; // T87431
		$wgAbuseFilterRuntimeProfile = true;
		$wgGroupPermissions['sysop']['abusefilter-modify-restricted'] = true;
		$wgGroupPermissions['patroller']['abusefilter-log-detail'] = true;
		break;
	case 'cswiki':
		$wgGroupPermissions['arbcom']['abusefilter-log-detail'] = true;
		$wgGroupPermissions['arbcom']['abusefilter-view-private'] = true; // T174357
		$wgGroupPermissions['arbcom']['abusefilter-log-private'] = true; // T174357
		$wgGroupPermissions['engineer']['abusefilter-log-detail'] = true; // T203000
		$wgGroupPermissions['engineer']['abusefilter-modify'] = true; // T203000
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
	case 'dewiki':
		$wgAbuseFilterRuntimeProfile = true;
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
		$wgGroupPermissions['oversight']['abusefilter-view-private'] = true; // T119446
		$wgGroupPermissions['checkuser']['abusefilter-view-private'] = true; // T119446
		$wgAbuseFilterNotificationsPrivate = true; // T46045
		$wgAbuseFilterProfile = true;
		$wgAbuseFilterRuntimeProfile = true;
		$wgAbuseFilterEmergencyDisableCount['default'] = 25;
		break;
	case 'enwikibooks':
		$wmgUseGlobalAbuseFilters = true; // T78496
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
		$wgGroupPermissions['rollbacker']['abusefilter-log-detail'] = true; // T70319
		$wgGroupPermissions['patroller']['abusefilter-log-detail'] = true; // T70319
		$wgAbuseFilterProfile = true; // T152087
		$wgAbuseFilterRuntimeProfile = true;
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
		$wgAbuseFilterRuntimeProfile = true; // T190264
		$wgAbuseFilterEmergencyDisableThreshold['default'] = 0.30; // T145765
		$wgAbuseFilterEmergencyDisableCount['default'] = 10; // T145765
		break;
	case 'eswikiquote': // T177760, T177761
		$wgGroupPermissions['sysop']['abusefilter-modify-restricted'] = true;
		$wgGroupPermissions['sysop']['abusefilter-revert'] = true;
		$wgGroupPermissions['sysop']['abusefilter-view-private'] = true;
		$wgAbuseFilterActions['block'] = true;
		$wgAbuseFilterBlockDuration = '1 day';
		$wgAbuseFilterAnonBlockDuration = '1 day';
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
		$wgAbuseFilterBlockDuration = '1 day';
		$wgAbuseFilterAnonBlockDuration = '1 day';
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
		$wgAbuseFilterAnonBlockDuration = '1 week'; // T87317
		$wgAbuseFilterBlockDuration = '2 weeks'; // T167562
		$wgAbuseFilterNotifications = false;
		break;
	case 'fawikiquote': // T178227
		$wgGroupPermissions['sysop']['abusefilter-modify-restricted'] = true;
		$wgAbuseFilterActions = [ 'rangeblock' => true ];
		$wgAbuseFilterBlockDuration = '2 weeks';
		$wgAbuseFilterAnonBlockDuration = '1 week';
		break;
	case 'fiwiki': // T59395
		$wgGroupPermissions['sysop']['abusefilter-modify-restricted'] = true;
		$wgGroupPermissions['sysop']['abusefilter-revert'] = true;
		break;
	case 'frwiki':
		$wmgUseGlobalAbuseFilters = true; // T120568
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
		$wgGroupPermissions['sysop']['abusefilter-modify-restricted'] = true;
		$wgGroupPermissions['checkuser']['abusefilter-log-detail'] = true;
		// See T200698 for interface-admin rights
		$wgGroupPermissions['interface-admin']['abusefilter-log'] = true;
		$wgGroupPermissions['interface-admin']['abusefilter-log-detail'] = true;
		$wgGroupPermissions['interface-admin']['abusefilter-modify'] = true;
		$wgGroupPermissions['interface-admin']['abusefilter-modify-restricted'] = true;
		$wgGroupPermissions['interface-admin']['abusefilter-revert'] = true;
		$wgGroupPermissions['interface-admin']['abusefilter-view'] = true;
		$wgGroupPermissions['interface-admin']['abusefilter-view-private'] = true;
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
	case 'incubatorwiki':
		$wmgUseGlobalAbuseFilters = true;
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
		$wgAbuseFilterActions['block'] = true; // T30153
		$wgAbuseFilterBlockDuration = '4 hours';
		$wgAbuseFilterAnonBlockDuration = '4 hours';
		$wgAbuseFilterNotifications = false;
		$wgAbuseFilterProfile = true; // T190137
		$wgAbuseFilterRuntimeProfile = true;
		break;
	case 'itwikibooks': // T202808
		$wgGroupPermissions['sysop']['abusefilter-modify-restricted'] = true;
		$wgAbuseFilterActions['block'] = true;
		$wgAbuseFilterBlockDuration = '1 day';
		$wgAbuseFilterAnonBlockDuration = '1 day';
		break;
	case 'itwikinews':
		$wgAbuseFilterActions['block'] = true;
		$wgAbuseFilterBlockDuration = '1 day';
		$wgAbuseFilterAnonBlockDuration = '1 day';
		break;
	case 'itwiktionary': // T199783
		$wgGroupPermissions['sysop']['abusefilter-modify-restricted'] = true;
		$wgAbuseFilterActions['block'] = true;
		$wgAbuseFilterBlockDuration = '1 day';
		$wgAbuseFilterAnonBlockDuration = '1 day';
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
		$wmgUseGlobalAbuseFilters = false;
		break;
	case 'labtestwiki':
		$wmgUseGlobalAbuseFilters = false;
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
		$wmgUseGlobalAbuseFilters = true;
		$wgGroupPermissions['autoconfirmed']['abusefilter-log-detail'] = false; // T44012
		$wgGroupPermissions['sysop']['abusefilter-modify-restricted'] = true;
		$wgAbuseFilterActions['block'] = true;
		$wgAbuseFilterAnonBlockDuration = '3 months'; // T72828
		$wgAbuseFilterRuntimeProfile = true;
		break;
	case 'metawiki':
		$wmgUseGlobalAbuseFilters = true;
		$wgGroupPermissions['*']['abusefilter-log-detail'] = true;
		$wgGroupPermissions['sysop']['abusefilter-modify-restricted'] = true; // T76270
		$wgGroupPermissions['sysop']['abusefilter-revert'] = true; // T76270
		$wgGroupPermissions['sysop']['abusefilter-modify-global'] = true; // T192722
		$wgGroupPermissions['steward']['abusefilter-modify-global'] = true; // T150752
		$wgGroupPermissions['steward']['abusefilter-private'] = true; // T160357
		$wgAbuseFilterActions['block'] = true; // T54681
		$wgAbuseFilterAnonBlockDuration = '31 hours'; // T76270
		$wgAbuseFilterProfile = true; // T149901
		$wgAbuseFilterRuntimeProfile = true;
		$wgAbuseFilterNotificationsPrivate = true; // T154358
		$wgAbuseFilterEmergencyDisableThreshold['default'] = 0.30; // T173633
		$wgAbuseFilterEmergencyDisableCount['default'] = 25; // T173633
		break;
	case 'mlwiki':
		$wgGroupPermissions['botadmin']['abusefilter-modify'] = true;
		$wgGroupPermissions['botadmin']['abusefilter-log-detail'] = true;
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
		$wmgUseGlobalAbuseFilters = true; // T140395
		$wgAbuseFilterProfile = true;
		$wgAbuseFilterRuntimeProfile = true;
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
		$wgGroupPermissions['*']['abusefilter-log-detail'] = true; // T19998
		$wgGroupPermissions['arbcom']['abusefilter-log-detail'] = true; // T51334
		break;
	case 'ruwikinews':
		$wgGroupPermissions['*']['abusefilter-log-detail'] = true;
		break;
	case 'ruwikisource':
		$wgGroupPermissions['abusefilter']['abusefilter-modify'] = true;
		$wgGroupPermissions['abusefilter']['abusefilter-log-detail'] = true;
		$wgGroupPermissions['autoconfirmed']['abusefilter-log-detail'] = false; // T44012
		break;
	case 'specieswiki':
		$wmgUseGlobalAbuseFilters = true;
		break;
	case 'tawiki':
		$wgGroupPermissions['patroller']['abusefilter-log-detail'] = true; // T95180
		break;
	case 'testwiki':
		$wmgUseGlobalAbuseFilters = true;
		$wgAbuseFilterRuntimeProfile = true;
		break;
	case 'test2wiki':
		$wmgUseGlobalAbuseFilters = true;
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
		$wgGroupPermissions['interface-editor']['abusefilter-modify'] = true; // T40690
		$wgGroupPermissions['interface-editor']['abusefilter-log-detail'] = true; // T40690
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
		$wmgUseGlobalAbuseFilters = true;
		$wgAbuseFilterNotificationsPrivate = true; // T47083
		$wgAbuseFilterActions['block'] = true; // T59681
		$wgAbuseFilterBlockDuration = 'indefinite'; // T59681
		$wgAbuseFilterAnonBlockDuration = '3 months'; // T59681
		$wgGroupPermissions['sysop']['abusefilter-modify-restricted'] = true; // T59681
		$wgGroupPermissions['rollbacker']['abusefilter-log-detail'] = true; // T57495
		$wgGroupPermissions['propertycreator']['abusefilter-log-detail'] = true; // T57495
		$wgGroupPermissions['wikidata-staff']['abusefilter-log-detail'] = true;
		$wgGroupPermissions['wikidata-staff']['abusefilter-modify'] = true;
		$wgGroupPermissions['wikidata-staff']['abusefilter-modify-restricted'] = true;
		$wgAbuseFilterRuntimeProfile = true;
		break;
	case 'zhwiki':
		$wgGroupPermissions['rollbacker']['abusefilter-log-private'] = true; // T39679
		$wgGroupPermissions['rollbacker']['abusefilter-view-private'] = true; // T174978
		$wgGroupPermissions['sysop']['abusefilter-modify-restricted'] = true; // T73854
		$wgAbuseFilterProfile = true; // T190663
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
	// To help fight spam, makes rules maintained on deploymentwiki to be available on all beta wikis.
	$wmgAbuseFilterCentralDB = 'deploymentwiki';
	$wmgUseGlobalAbuseFilters = true;
}
