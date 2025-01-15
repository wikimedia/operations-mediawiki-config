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

// Inline comments are often used for noting the task(s) associated with specific configuration
// and requiring comments to be on their own line would reduce readability for this file
// phpcs:disable MediaWiki.WhiteSpace.SpaceBeforeSingleLineComment.NewLineComment

$wgGroupPermissions['autoconfirmed']['abusefilter-log-detail'] = true; // T44012

// Provides access to private information - T160357
// Only CheckUsers/Stewards/Staff and Ombudsmen allowed.
$wgGroupPermissions['sysop']['abusefilter-privatedetails'] = false;
$wgGroupPermissions['sysop']['abusefilter-privatedetails-log'] = false;

// T160357 - Log accessing private abusefilter details
$wgAbuseFilterLogPrivateDetailsAccess = true;

// T160357 - Allow those with CheckUser right to access AbuseLog private information on WMF projects and its log
// Only CheckUsers/Stewards/Staff and Ombudsmen allowed
$wgGroupPermissions['checkuser']['abusefilter-privatedetails'] = true;
$wgGroupPermissions['checkuser']['abusefilter-privatedetails-log'] = true;

// T369610 - AbuseFilter's extension.json gives the right to view protected vars logs to sysop
// in order to support third-party wiki use cases. WMF wikis will prefer to give this right
// to the `checkuser` group in order to maintain parity with other privileged logs.
$wgGroupPermissions['sysop']['abusefilter-protected-vars-log'] = false;
$wgGroupPermissions['checkuser']['abusefilter-protected-vars-log'] = true;

// Disable some potentially dangerous actions
$wgAbuseFilterActions = [
	'block' => false,
	'rangeblock' => false,
	'degroup' => false,
];

// T66255 - Enable logging to irc.wikimedia.org by default
$wgAbuseFilterNotifications = "udp";

// Enable wgAbuseFilterNotificationsPrivate by default for WMF wikis - T266298
$wgAbuseFilterNotificationsPrivate = true;

// T113164 Change default AbuseFilter IP block duration to not indefinite
$wgAbuseFilterAnonBlockDuration = '1 week';

$wgAbuseFilterSlowFilterRuntimeLimit = 800;

// Setting this to true should be non-controversial, but many wikis don't assign it to sysops.
// Also, several wikis assign this right although no reversible actions are enabled.
$wgGroupPermissions['sysop']['abusefilter-revert'] = false;

// T309609 — Set default AbuseFilter condition limit to 2000
$wgAbuseFilterConditionLimit = 2000;

// Custom permissions
switch ( $wgDBname ) {
	case 'arwiki':
		$wgGroupPermissions['sysop']['abusefilter-modify'] = false;
		$wgGroupPermissions['sysop']['abusefilter-view-private'] = false;
		$wgGroupPermissions['sysop']['abusefilter-log-private'] = false;
		$wgGroupPermissions['abusefilter']['abusefilter-modify'] = true;
		$wgGroupPermissions['*']['abusefilter-log-detail'] = true;
		$wgGroupPermissions['checkuser']['abusefilter-view-private'] = true;
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
		break;
	case 'bnwiki':
		$wgAbuseFilterActions['block'] = true; // T361852
		$wgAbuseFilterBlockDuration = '3 days'; // T361852
		$wgAbuseFilterAnonBlockDuration = '12 hours'; // T361852
		break;
	case 'cawiki':
		$wgGroupPermissions['sysop']['abusefilter-modify'] = false;
		$wgGroupPermissions['abusefilter']['abusefilter-modify'] = true;
		$wgGroupPermissions['abusefilter']['abusefilter-modify-restricted'] = true; // T50457
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
		$wgGroupPermissions['sysop']['abusefilter-revert'] = true;
		$wgGroupPermissions['sysop']['abusefilter-log-private'] = true;
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
		$wgGroupPermissions['abusefilter-helper']['abusefilter-view-private'] = true; // T175684
		break;
	case 'enwikibooks':
		$wgGroupPermissions['*']['abusefilter-view'] = false;
		$wgGroupPermissions['*']['abusefilter-log'] = false;
		$wgAbuseFilterNotifications = false;
		$wgGroupPermissions['autoconfirmed']['abusefilter-view'] = true;
		$wgGroupPermissions['autoconfirmed']['abusefilter-log'] = true;
		$wgGroupPermissions['autoconfirmed']['abusefilter-log-detail'] = true; // T383332
		$wgAbuseFilterActions['block'] = true; // T273864
		break;
	case 'enwikinews':
		$wgAbuseFilterActions['block'] = true; // T57868
		break;
	case 'enwikisource':
		$wgAbuseFilterActions['block'] = true; // T231750
		$wgGroupPermissions['sysop']['abusefilter-modify'] = false;
		$wgGroupPermissions['sysop']['abusefilter-view-private'] = false;
		$wgGroupPermissions['sysop']['abusefilter-log-private'] = false;
		$wgGroupPermissions['abusefilter']['abusefilter-modify'] = true;
		$wgGroupPermissions['abusefilter']['abusefilter-modify-restricted'] = true; // T231750
		break;
	case 'eswiki':
		$wgGroupPermissions['autoconfirmed']['abusefilter-log-detail'] = false; // T44012
		$wgGroupPermissions['user']['abusefilter-log'] = true;
		$wgGroupPermissions['sysop']['abusefilter-revert'] = true;
		$wgGroupPermissions['abusefilter']['abusefilter-modify'] = true; // T262174
		$wgGroupPermissions['abusefilter']['abusefilter-log-detail'] = true; // T262174
		$wgGroupPermissions['abusefilter']['abusefilter-log-private'] = true; // T262174
		$wgGroupPermissions['abusefilter']['abusefilter-revert'] = true; // T284797
		$wgGroupPermissions['abusefilter']['abusefilter-view-private'] = true; // T262174
		$wgGroupPermissions['abusefilter']['managechangetags'] = true; // T285167
		$wgGroupPermissions['abusefilter']['oathauth-enable'] = true; // T262174
		$wgAbuseFilterActions['block'] = true; // T284797
		$wgAbuseFilterBlockDuration = '24 hours'; // T284797
		$wgAbuseFilterAnonBlockDuration = '24 hours'; // T284797
		break;
	case 'eswikibooks':
		$wgGroupPermissions['*']['abusefilter-view'] = false;
		$wgGroupPermissions['*']['abusefilter-log'] = false;
		$wgGroupPermissions['autoconfirmed']['abusefilter-view'] = true;
		$wgGroupPermissions['autoconfirmed']['abusefilter-log'] = true;
		$wgGroupPermissions['autoconfirmed']['abusefilter-log-detail'] = false; // T44012
		$wgGroupPermissions['sysop']['abusefilter-revert'] = true;
		$wgAbuseFilterActions['block'] = true;
		$wgAbuseFilterBlockDuration = 'indefinite'; // T96669
		$wgAbuseFilterAnonBlockDuration = '31 hours'; // T96669
		break;
	case 'eswikinews': // T236730
		$wgAbuseFilterActions['block'] = true;
		$wgAbuseFilterAnonBlockDuration = '31 hours';
		$wgAbuseFilterBlockDuration = '31 hours';
		$wgGroupPermissions['sysop']['abusefilter-revert'] = true;
		break;
	case 'eswikiquote': // T177760, T177761
		$wgGroupPermissions['sysop']['abusefilter-revert'] = true;
		$wgAbuseFilterActions['block'] = true;
		$wgAbuseFilterBlockDuration = '1 day';
		$wgAbuseFilterAnonBlockDuration = '1 day';
		break;
	case 'eswiktionary':
		$wgGroupPermissions['*']['abusefilter-log-detail'] = true;
		$wgGroupPermissions['abusefilter']['abusefilter-modify'] = true;
		$wgGroupPermissions['sysop']['abusefilter-revert'] = true;
		$wgAbuseFilterActions['block'] = true;
		$wgAbuseFilterActions['blockautopromote'] = false;
		$wgAbuseFilterBlockDuration = '2 days';
		$wgAbuseFilterAnonBlockDuration = '2 days';
		break;
	case 'eswikivoyage': // T64321
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
		$wgGroupPermissions['eliminator']['abusefilter-log'] = true;
		$wgGroupPermissions['eliminator']['abusefilter-log-detail'] = true;
		$wgGroupPermissions['eliminator']['abusefilter-view'] = true;
		$wgGroupPermissions['sysop']['abusefilter-log'] = true;
		$wgGroupPermissions['sysop']['abusefilter-log-private'] = true;
		$wgGroupPermissions['sysop']['abusefilter-view'] = true;
		$wgGroupPermissions['sysop']['abusefilter-revert'] = true;
		$wgAbuseFilterActions = [ 'rangeblock' => true ];
		$wgAbuseFilterAnonBlockDuration = '1 week'; // T87317
		$wgAbuseFilterBlockDuration = '2 weeks'; // T167562
		$wgAbuseFilterNotifications = false;
		break;
	case 'fawikiquote': // T178227
		$wgAbuseFilterActions = [ 'rangeblock' => true ];
		$wgAbuseFilterBlockDuration = '2 weeks';
		$wgAbuseFilterAnonBlockDuration = '1 week';
		break;
	case 'fiwiki': // T59395
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
		$wgGroupPermissions['sysop']['abusefilter-view-private'] = false;
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
		$wgGroupPermissions['sysop']['abusefilter-view-private'] = false;
		$wgGroupPermissions['sysop']['abusefilter-log-private'] = false;
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
		$wgGroupPermissions['sysop']['abusefilter-view-private'] = false;
		$wgGroupPermissions['sysop']['abusefilter-log-private'] = false;
		$wgGroupPermissions['*']['abusefilter-log-detail'] = true;
		break;
	case 'hrwiki':
		$wgAbuseFilterActions['block'] = true; // T270997
		$wgAbuseFilterBlockDuration = 'indefinite'; // T270997
		$wgAbuseFilterAnonBlockDuration = '2 days'; // T270997
		break;
	case 'itwiki':
		$wgGroupPermissions['*']['abusefilter-view'] = false;
		$wgGroupPermissions['*']['abusefilter-log'] = false;
		$wgGroupPermissions['autoconfirmed']['abusefilter-log-detail'] = false;
		$wgGroupPermissions['sysop']['abusefilter-log-detail'] = true;
		$wgGroupPermissions['sysop']['abusefilter-view'] = true;
		$wgGroupPermissions['sysop']['abusefilter-log'] = true;
		$wgGroupPermissions['sysop']['abusefilter-revert'] = true;
		$wgGroupPermissions['autoconfirmed']['abusefilter-log'] = true;
		$wgGroupPermissions['autoconfirmed']['abusefilter-view'] = true;
		$wgAbuseFilterActions['block'] = true; // T30153
		$wgAbuseFilterBlockDuration = '4 hours';
		$wgAbuseFilterAnonBlockDuration = '4 hours';
		$wgAbuseFilterNotifications = false;
		break;
	case 'itwikibooks': // T202808
		$wgAbuseFilterActions['block'] = true;
		$wgAbuseFilterBlockDuration = '1 day';
		$wgAbuseFilterAnonBlockDuration = '1 day';
		break;
	case 'itwikinews':
		$wgAbuseFilterActions['block'] = true;
		$wgAbuseFilterBlockDuration = '1 day';
		$wgAbuseFilterAnonBlockDuration = '1 day';
		break;
	case 'itwikiquote':
		$wgGroupPermissions['*']['abusefilter-log-detail'] = true;
		break;
	case 'itwikiversity': // T328194
		$wgAbuseFilterActions['block'] = true;
		$wgAbuseFilterBlockDuration = '1 day';
		$wgAbuseFilterAnonBlockDuration = '1 day';
		break;
	case 'itwiktionary': // T199783
		$wgAbuseFilterActions['block'] = true;
		$wgAbuseFilterBlockDuration = '1 day';
		$wgAbuseFilterAnonBlockDuration = '1 day';
		break;
	case 'jawiki':
		$wgGroupPermissions['sysop']['abusefilter-modify'] = false;
		$wgGroupPermissions['sysop']['abusefilter-view-private'] = false;
		$wgGroupPermissions['sysop']['abusefilter-log-private'] = false;
		$wgGroupPermissions['abusefilter']['abusefilter-modify'] = true;
		$wgGroupPermissions['*']['abusefilter-log-detail'] = false;
		$wgGroupPermissions['autoconfirmed']['abusefilter-log-detail'] = false; // T44012
		$wgGroupPermissions['abusefilter']['abusefilter-log-detail'] = true;
		$wgGroupPermissions['sysop']['abusefilter-revert'] = true;
		break;
	case 'labswiki': // wikitech.wikimedia.org
		$wgGroupPermissions['sysop']['abusefilter-revert'] = true;
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
		$wgGroupPermissions['sysop']['abusefilter-view-private'] = false;
		$wgGroupPermissions['sysop']['abusefilter-log-private'] = false;
		break;
	case 'mediawikiwiki':
		$wgGroupPermissions['autoconfirmed']['abusefilter-log-detail'] = false; // T44012
		$wgAbuseFilterActions['block'] = true;
		$wgAbuseFilterAnonBlockDuration = '3 months'; // T72828
		break;
	case 'metawiki':
		$wgGroupPermissions['*']['abusefilter-log-detail'] = true;
		$wgGroupPermissions['sysop']['abusefilter-revert'] = true; // T76270
		$wgGroupPermissions['sysop']['abusefilter-modify-global'] = true; // T192722
		$wgGroupPermissions['steward']['abusefilter-modify-global'] = true; // T150752
		$wgGroupPermissions['steward']['abusefilter-privatedetails'] = true; // T160357
		$wgAbuseFilterActions['block'] = true; // T54681
		$wgAbuseFilterAnonBlockDuration = '31 hours'; // T76270
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
		$wgGroupPermissions['sysop']['abusefilter-view-private'] = false;
		$wgGroupPermissions['autoconfirmed']['abusefilter-view'] = true;
		$wgGroupPermissions['autoconfirmed']['abusefilter-log'] = true;
		$wgGroupPermissions['abusefilter']['abusefilter-modify'] = true;
		$wgGroupPermissions['abusefilter']['abusefilter-modify-restricted'] = true;
		$wgGroupPermissions['abusefilter']['abusefilter-revert'] = true;
		$wgGroupPermissions['checkuser']['abusefilter-log-private'] = true; // T370605
		break;
	case 'nowiki':
		$wgGroupPermissions['sysop']['abusefilter-modify'] = false;
		$wgGroupPermissions['sysop']['abusefilter-view-private'] = false;
		$wgGroupPermissions['sysop']['abusefilter-log-private'] = false;
		$wgGroupPermissions['abusefilter']['abusefilter-modify'] = true;
		$wgGroupPermissions['autoconfirmed']['abusefilter-log-detail'] = false; // T44012
		break;
	case 'plwiki':
		$wgGroupPermissions['*']['abusefilter-view'] = false;
		$wgGroupPermissions['autoconfirmed']['abusefilter-view'] = true;
		$wgGroupPermissions['sysop']['abusefilter-modify'] = false;
		$wgGroupPermissions['sysop']['abusefilter-view-private'] = false;
		$wgGroupPermissions['abusefilter']['abusefilter-modify'] = true;
		$wgGroupPermissions['abusefilter']['abusefilter-modify-restricted'] = true; // T224308
		$wgGroupPermissions['autoconfirmed']['abusefilter-log-detail'] = false; // T44012
		$wgAbuseFilterActions['block'] = true; // T224617
		$wgAbuseFilterBlockDuration = '2 hours'; // T224617
		$wgAbuseFilterAnonBlockDuration = '2 hours'; // T224617
		break;
	case 'ptwiktionary':
		$wgGroupPermissions['sysop']['abusefilter-revert'] = true;
		$wgGroupPermissions['autoconfirmed']['abusefilter-log-detail'] = false; // T44012
		$wgAbuseFilterActions['block'] = true; // T134779
		$wgAbuseFilterAnonBlockDuration = '3 days'; // T134779
		break;
	case 'rowiki':
		$wgGroupPermissions['sysop']['abusefilter-modify'] = false;
		$wgGroupPermissions['sysop']['abusefilter-view-private'] = false;
		$wgGroupPermissions['sysop']['abusefilter-log-private'] = false;
		$wgGroupPermissions['abusefilter']['abusefilter-modify'] = true;
		$wgGroupPermissions['*']['abusefilter-log-detail'] = true;
		$wgGroupPermissions['sysop']['abusefilter-revert'] = true;
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
	case 'shwiki':
		$wgAbuseFilterActions['block'] = true; // T345513
		$wgAbuseFilterBlockDuration = '1 day'; // T345513
		$wgAbuseFilterAnonBlockDuration = '1 day'; // T345513
		break;
	case 'srwiki':
		$wgAbuseFilterActions['block'] = true; // T349727
		break;
	case 'testwiki':
		$wgAbuseFilterActions['block'] = true;
		$wgAbuseFilterAnonBlockDuration = '24 hours';
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
		$wgAbuseFilterActions['block'] = true; // T161960
		$wgAbuseFilterBlockDuration = 'indefinite'; // T161960
		$wgAbuseFilterAnonBlockDuration = '24 hours';
		break;
	case 'trwikiquote':
		$wgAbuseFilterActions['block'] = true; // T315736
		$wgAbuseFilterBlockDuration = 'indefinite'; // T315736
		$wgAbuseFilterAnonBlockDuration = '72 hours'; // T315736
		break;
	case 'ukwiki':
		$wgGroupPermissions['*']['abusefilter-log'] = false;
		$wgGroupPermissions['*']['abusefilter-view'] = false;
		$wgGroupPermissions['autoconfirmed']['abusefilter-view'] = true;
		$wgGroupPermissions['autoconfirmed']['abusefilter-log'] = true;
		$wgAbuseFilterActions['blockautopromote'] = false;
		$wgAbuseFilterActions['block'] = true; // T89379
		$wgAbuseFilterBlockDuration = '2 hours';    // T89379
		$wgAbuseFilterAnonBlockDuration = '2 hours';
		$wgAbuseFilterNotifications = false;
		break;
	case 'ukwikivoyage':
		$wgAbuseFilterActions['block'] = true; // T275271
		break;
	case 'urwiki':
		$wgGroupPermissions['abusefilter']['abusefilter-modify'] = true; // T47643
		break;
	case 'wikidatawiki':
		$wgAbuseFilterActions['block'] = true; // T59681
		$wgAbuseFilterBlockDuration = 'indefinite'; // T59681
		$wgAbuseFilterAnonBlockDuration = '3 months'; // T59681
		break;
	case 'zhwiki':
		$wgAbuseFilterActions['block'] = true; // T210364
		$wgAbuseFilterBlockDuration = '24 hours'; // T210364
		$wgAbuseFilterAnonBlockDuration = '24 hours'; // T210364
		$wgGroupPermissions['abusefilter-helper']['abusefilter-log-private'] = true; // T344398
		$wgGroupPermissions['abusefilter-helper']['abusefilter-view-private'] = true; // T344398
		$wgGroupPermissions['rollbacker']['abusefilter-log-private'] = true; // T39676
		break;
	case 'zhwikibooks':
		$wgAbuseFilterActions['block'] = true; // T330026
		$wgAbuseFilterBlockDuration = '24 hours'; // T330026
		$wgAbuseFilterAnonBlockDuration = '24 hours'; // T330026
		break;
	case 'zhwikiquote':
		$wgAbuseFilterActions['block'] = true; // T330026
		$wgAbuseFilterBlockDuration = '24 hours'; // T330026
		$wgAbuseFilterAnonBlockDuration = '24 hours'; // T330026
		break;
	case 'zhwikiversity':
		$wgAbuseFilterActions['block'] = true; // T307007
		$wgAbuseFilterBlockDuration = '24 hours'; // T307007
		$wgAbuseFilterAnonBlockDuration = '24 hours'; // T307007
		break;
	case 'zhwikivoyage':
		$wgAbuseFilterActions['block'] = true; // T353604
		$wgAbuseFilterBlockDuration = '24 hours'; // T353604
		$wgAbuseFilterAnonBlockDuration = '24 hours'; // T353604
		break;
	case 'zh_yuewiki':
		$wgAbuseFilterActions['block'] = true; // T270567
		$wgAbuseFilterBlockDuration = '31 hours'; // T270567
		$wgAbuseFilterAnonBlockDuration = '31 hours'; // T270567
		$wgGroupPermissions['*']['abusefilter-log-detail'] = true;
		$wgGroupPermissions['sysop']['abusefilter-revert'] = true;
		$wgGroupPermissions['abusefilter']['abusefilter-modify'] = true;
		$wgGroupPermissions['sysop']['abusefilter-modify'] = false;
		$wgGroupPermissions['sysop']['abusefilter-view-private'] = false;
		$wgGroupPermissions['sysop']['abusefilter-log-private'] = false;
		break;
// Please add new wikis in their correct place in alphabetical order!
}

if ( $wmgRealm === 'labs' ) {
	// T103060
	$wgAbuseFilterActions['block'] = true;
	$wgAbuseFilterBlockDuration = 'indefinite';
	$wgAbuseFilterAnonBlockDuration = '48 hours';
	$wgAbuseFilterLogIP = false; // Prevent the collection of IP addresses - T188862
	$wgAbuseFilterLogIPMaxAge = 1; // Purge data older than 1 second (hack to clean all data)
}
