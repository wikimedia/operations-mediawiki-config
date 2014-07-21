<?php
# WARNING: This file is publically viewable on the web. Do not put private data here.

# NOTE: this file is now only included for wikis in flaggedrevs.dblist
# It's set up this way to allow a cron job on terbium to easily determine a
# list of wikis it needs to run updateStats.php on

$path = "$IP/extensions/FlaggedRevs/FlaggedRevs.php";
include( $path );

///////////////////////////////////////
// Common configuration
// DO NOT CHANGE without hard-coding these values into the relevant wikis first.
$wgFlaggedRevsNamespaces[] = 828; // NS_MODULE
$wgFlaggedRevTags = array(
	'accuracy' => array( 'levels' => 2, 'quality' => 2, 'pristine' => 4 ),
);
$wgFlagRestrictions = array(
	'accuracy' => array( 'review' => 1, 'autoreview' => 1 ),
);
$wgGroupPermissions['autoconfirmed']['movestable'] = true; // bug 14166

$wmfStandardAutoPromote = $wgFlaggedRevsAutopromote; // flaggedrevs defaults
$wgFlaggedRevsAutopromote = false;

$wgGroupPermissions['sysop']['stablesettings'] = false; // -aaron 3/20/10

$wgEnableValidationStatisticsUpdates = false;

# Rights for Bureaucrats (b/c)
if ( !in_array( 'reviewer', $wgAddGroups['bureaucrat'] ) ) {
	$wgAddGroups['bureaucrat'][] = 'reviewer'; // promote to full reviewers
}
if ( !in_array( 'reviewer', $wgRemoveGroups['bureaucrat'] ) ) {
	$wgRemoveGroups['bureaucrat'][] = 'reviewer'; // demote from full reviewers
}

///////////////////////////////////////
// Wiki-specific configurations

if ( $wgDBname == 'alswiki' ) {
	$wgFlaggedRevsOverride = false;
	$wgGroupPermissions['sysop']['stablesettings'] = true; // -aaron 3/20/10
}

elseif ( $wgDBname == 'arwiki' ) {
	$wgFlaggedRevsWhitelist = array( 'الص�?حة_الرئيسية' );
	$wgFlaggedRevsNamespaces = array_merge( $wgFlaggedRevsNamespaces, array( 100, 104 ) ); // bug 19332
}

elseif ( $wgDBname == 'bewiki' ) {
	$wgFlaggedRevsOverride = false;
	$wgFlaggedRevsNamespaces[] = NS_CATEGORY;
	$wgFlaggedRevTags['accuracy']['levels'] = 1;
	$wgGroupPermissions['autoeditor']['autoreview'] = true;
	$wgGroupPermissions['autoeditor']['autoconfirmed'] = true;
	$wgGroupPermissions['sysop']['stablesettings'] = true;
}
elseif ( $wgDBname == 'bnwiki' ) { // http://bugzilla.wikimedia.org/show_bug.cgi?id=28717
	$wgFlaggedRevsNamespaces = array( NS_MAIN, NS_PROJECT );
	# Show only on a per-page basis
	$wgFlaggedRevsOverride = false;
	# We have only one tag with one level
	$wgFlaggedRevTags = array(
		'status' => array( 'levels' => 1, 'quality' => 2, 'pristine' => 3 ),
	);
	# Restrict autoconfirmed to flagging semi-protected
	$wgFlagRestrictions = array(
		'status' => array( 'review' => 1, 'autoreview' => 1 ),
	);
	# Restriction levels for auto-review/review rights
	$wgFlaggedRevsRestrictionLevels = array( '', 'autoconfirmed', 'review' );
	# Use flag "protection" levels
	$wgFlaggedRevsProtection = true;
	# Use current templates/files
	$wgFlaggedRevsHandleIncludes = FR_INCLUDES_CURRENT;
	# Group permissions for autoconfirmed
	$wgGroupPermissions['autoconfirmed']['autoreview'] = true;
		# WP:FPPR trial quota
	$wgFlaggedRevsProtectQuota = 2000;

	# Group permissions for sysops
	$wgGroupPermissions['sysop']['review']         = true;
	$wgGroupPermissions['sysop']['stablesettings'] = true;
	# Use 'reviewer' group
	$wgAddGroups['sysop'][] = 'reviewer';
	$wgRemoveGroups['sysop'][] = 'reviewer';
	# Remove 'editor' group
	unset( $wgGroupPermissions['editor'] );
	$wgAddGroups['sysop'] = array_diff( $wgAddGroups['sysop'], array( 'editor' ) );
	$wgRemoveGroups['sysop'] = array_diff( $wgRemoveGroups['sysop'], array( 'editor' ) );
}

elseif ( $wgDBname == 'cewiki' ) { // based on ruwiki settings

	// Bug 56408
	$wgFlaggedRevsNamespaces = array( NS_MAIN, NS_FILE, NS_TEMPLATE, NS_CATEGORY, 100, 828 );

	$wgFlaggedRevTags['accuracy']['levels'] = 3; // Is this needed?
	$wgFlaggedRevsOverride = false;

	$wgGroupPermissions['autoeditor']['autoreview'] = true;
	$wgGroupPermissions['autoeditor']['autoconfirmed'] = true;

	$wgGroupPermissions['sysop']['stablesettings'] = true;
}

elseif ( $wgDBname == 'ckbwiki') {
	# Namespaces
	$wgFlaggedRevsNamespaces = array( NS_MAIN, NS_PROJECT, NS_HELP, NS_TEMPLATE, NS_CATEGORY, NS_FILE, 100, 102, 828 );
	# Show only on a per-page basis
	$wgFlaggedRevsOverride = false;
	# We have only one tag with one level
	$wgFlaggedRevTags = array(
		'status' => array( 'levels' => 1, 'quality' => 2, 'pristine' => 3 ),
	);
	# Restrict autoconfirmed to flagging semi-protected
	$wgFlagRestrictions = array(
		'status' => array( 'review' => 1, 'autoreview' => 1 ),
	);
	# Restriction levels for autoconfirmed, autopatrol and review rights
	$wgFlaggedRevsRestrictionLevels = array( '', 'autoconfirmed', 'autopatrol', 'review' );
	# Use flag "protection" levels
	$wgFlaggedRevsProtection = true;
	# Use current templates/files
	$wgFlaggedRevsHandleIncludes = FR_INCLUDES_CURRENT;
	# Trial quota
	$wgFlaggedRevsProtectQuota = 2000;

	# User groups permissions
	$wgGroupPermissions['autoconfirmed']['autoreview'] = true;
	$wgGroupPermissions['reviewer']['autopatrol'] = true;
	$wgGroupPermissions['reviewer']['patrol'] = true;
	$wgGroupPermissions['reviewer']['unwatchedpages'] = true;
	$wgGroupPermissions['sysop']['review'] = true;
	$wgGroupPermissions['sysop']['validate'] = true;
	$wgGroupPermissions['sysop']['stablesettings'] = true;

	# Remove editor and autoreview user groups
	unset( $wgGroupPermissions['editor'], $wgGroupPermissions['autoreview'] );
	$wgAddGroups['sysop'] = array_diff( $wgAddGroups['sysop'], array( 'editor', 'autoreview' ) );
	$wgRemoveGroups['sysop'] = array_diff( $wgRemoveGroups['sysop'], array( 'editor', 'autoreview' ) );
}

elseif ( $wgDBname == 'testwiki' && false ) {
	// Disabled temporarily, give testwiki enwiki's settings instead --Roan May 7 2012
	$wgGroupsAddToSelf['*'][] = 'editor';
	$wgGroupsAddToSelf['*'][] = 'reviewer';
	$wgGroupsRemoveFromSelf['*'][] = 'editor';
	$wgGroupsRemoveFromSelf['*'][] = 'reviewer';
	$wgFlaggedRevsAutopromote = $wmfStandardAutoPromote;
}

elseif ( $wgDBname == 'test2wiki' ) {
    $wgFlaggedRevsNamespaces[] = NS_CATEGORY;
    $wgFlaggedRevTags['accuracy']['levels'] = 1;

    $wgFlaggedRevsAutopromote = $wmfStandardAutoPromote;
    $wgFlaggedRevsAutopromote['edits'] = 300;
    $wgFlaggedRevsAutopromote['recentContentEdits'] = 5;
    $wgFlaggedRevsAutopromote['editComments'] = 30;

    $wgFlaggedRevsAutoconfirm = array(
        'days'                => 30, # days since registration
        'edits'               => 50, # total edit count
        'spacing'             => 3, # spacing of edit intervals
        'benchmarks'          => 7, # how many edit intervals are needed?
        'excludeLastDays'     => 2, # exclude the last X days of edits from edit counts
        // Either totalContentEdits reqs OR totalCheckedEdits requirements needed
        'totalContentEdits'   => 150, # $wgContentNamespaces edits OR...
        'totalCheckedEdits'   => 50, # ...Edits before the stable version of pages
        'uniqueContentPages'  => 8, # $wgContentNamespaces unique pages edited
        'editComments'        => 20, # how many edit comments used?
        'email'               => false, # user must be emailconfirmed?
        'neverBlocked'        => true, # Can users that were blocked be promoted?
    );

    $wgGroupPermissions['sysop']['stablesettings'] = true; // -aaron 3/20/10
}

elseif ( $wgDBname == 'cawikinews' ) {
	$wgGroupPermissions['sysop']['stablesettings'] = true; // Bug 34135
	$wgFlaggedRevsNamespaces[] = 102; // Bug 34135
}

// New deployment 2008-05-03
// --brion
elseif ( $wgDBname == 'dewiki' ) {
	$wgFlaggedRevsNamespaces[] = NS_CATEGORY;
	$wgFlaggedRevTags['accuracy']['levels'] = 1;

	$wgFlaggedRevsAutopromote = $wmfStandardAutoPromote;
	$wgFlaggedRevsAutopromote['edits'] = 300;
	$wgFlaggedRevsAutopromote['recentContentEdits'] = 5;
	$wgFlaggedRevsAutopromote['editComments'] = 30;

	$wgFlaggedRevsAutoconfirm = array(
		'days'                => 30, # days since registration
		'edits'               => 50, # total edit count
		'spacing'             => 3, # spacing of edit intervals
		'benchmarks'          => 7, # how many edit intervals are needed?
		'excludeLastDays'     => 2, # exclude the last X days of edits from edit counts
		// Either totalContentEdits reqs OR totalCheckedEdits requirements needed
		'totalContentEdits'   => 150, # $wgContentNamespaces edits OR...
		'totalCheckedEdits'   => 50, # ...Edits before the stable version of pages
		'uniqueContentPages'  => 8, # $wgContentNamespaces unique pages edited
		'editComments'        => 20, # how many edit comments used?
		'email'               => false, # user must be emailconfirmed?
		'neverBlocked'        => true, # Can users that were blocked be promoted?
	);

	$wgGroupPermissions['sysop']['stablesettings'] = true; // -aaron 3/20/10
}

elseif ( $wgDBname == 'dewikiquote' ) {
	$wgFlaggedRevsOverride = false;
}

elseif ( $wgDBname == 'dewiktionary' ) {
	$wgFlaggedRevsOverride = false;
	$wgFlaggedRevTags['accuracy']['levels'] = 1;
	$wgFlaggedRevsNamespaces = array_merge( $wgFlaggedRevsNamespaces, array( 102, 104, 106 ) ); // bug 65316

	$wgFlaggedRevsAutoconfirm = array( // Bug 44103
		'days'                => 60,
		'totalContentEdits'   => 250,
		'totalCheckedEdits'   => 50,
		'excludeLastDays'     => 2,
		'uniqueContentPages'  => 50,
		'neverBlocked'        => true,
		'edits' => 1,
		'editComments' => 1,
		'spacing' => 1,
		'benchmarks' => 1,
		'email' => false
	);
}

// Temporarily give testwiki enwiki's settings instead, for testing PageTriage --Roan May 7
elseif ( $wgDBname == 'enwiki' || $wgDBname == 'testwiki' ) {

	$wgFlaggedRevsNamespaces = array( NS_MAIN, NS_PROJECT );
	# Show only on a per-page basis
	$wgFlaggedRevsOverride = false;
	# We have only one tag with one level
	$wgFlaggedRevTags = array(
		'status' => array( 'levels' => 1, 'quality' => 2, 'pristine' => 3 ),
	);
	# Restrict autoconfirmed to flagging semi-protected
	$wgFlagRestrictions = array(
		'status' => array( 'review' => 1, 'autoreview' => 1 ),
	);
	# Restriction levels for auto-review/review rights
	$wgFlaggedRevsRestrictionLevels = array( '', 'autoconfirmed', 'review' );
	# Use flag "protection" levels
	$wgFlaggedRevsProtection = true;
	# Use current templates/files
	$wgFlaggedRevsHandleIncludes = FR_INCLUDES_CURRENT;
	# Group permissions for autoconfirmed
	$wgGroupPermissions['autoconfirmed']['autoreview'] = true;
	# WP:FPPR trial quota
	$wgFlaggedRevsProtectQuota = 2000;

	# Group permissions for sysops
	$wgGroupPermissions['sysop']['review']         = true;
	$wgGroupPermissions['sysop']['stablesettings'] = true;
	# Use 'reviewer' group
	$wgAddGroups['sysop'][] = 'reviewer';
	$wgRemoveGroups['sysop'][] = 'reviewer';
	# Remove 'editor' group
	unset( $wgGroupPermissions['editor'] );
	$wgAddGroups['sysop'] = array_diff( $wgAddGroups['sysop'], array( 'editor' ) );
	$wgRemoveGroups['sysop'] = array_diff( $wgRemoveGroups['sysop'], array( 'editor' ) );
}

elseif ( $wgDBname == 'enwikibooks' ) {
	$wgFlaggedRevsOverride = false;
	// Cookbook, WikiJunior
	$wgFlaggedRevsNamespaces = array_merge( $wgFlaggedRevsNamespaces, array( 102, 110 ) );
	$wgFlaggedRevTags = array(
		'value' => array( 'levels' => 3, 'quality' => 2, 'pristine' => 3 )
	);

	$wgSimpleFlaggedRevsUI = false;

	$wgFlaggedRevsAutopromote = array(
		  'days' => 30,
		  'edits' => 100,
		  'spacing' => 2,
		  'benchmarks' => 8,
		  'recentContentEdits' => 5,
		  'totalContentEdits' => 50,
		  'uniqueContentPages' => 10,
		  'editComments' => 50,
	) + $wmfStandardAutoPromote;

	$wgGroupPermissions['editor']['rollback'] = true;
	$wgGroupPermissions['sysop']['review'] = true;
	$wgGroupPermissions['sysop']['stablesettings'] = true;
	$wgGroupPermissions['sysop']['validate'] = true;

	unset( $wgGroupPermissions['reviewer'] );

	$wgFeedbackAge = 180 * 24 * 3600;
	$wgFeedbackSizeThreshhold = 5;
}
elseif ( $wgDBname == 'elwikinews' ) {
    $wgFlaggedRevsAutoReviewNew = false;
    $wgFlaggedRevsNamespaces = array_merge( $wgFlaggedRevsNamespaces, array( NS_CATEGORY, 100 ) );
    $wgGroupPermissions['editor']['rollback'] = true;
    $wgGroupPermissions['editor']['autoreview'] = false;
    $wgGroupPermissions['sysop']['stablesettings'] = true;
    $wgGroupPermissions['sysop']['autoreview'] = false;


    $wgFeedbackNamespaces = array( NS_MAIN );

    unset( $wgGroupPermissions['reviewer'] );
}

elseif ( $wgDBname == 'enwikinews' ) {
	$wgFlaggedRevsAutoReviewNew = false; // https://bugzilla.wikimedia.org/show_bug.cgi?id=15639
	$wgFlaggedRevsNamespaces = array_merge( $wgFlaggedRevsNamespaces, array( NS_CATEGORY, 100 ) );
	$wgGroupPermissions['editor']['rollback'] = true; // https://bugzilla.wikimedia.org/show_bug.cgi?id=19815
	$wgGroupPermissions['editor']['autoreview'] = false; // https://bugzilla.wikimedia.org/show_bug.cgi?id=23948
	$wgGroupPermissions['sysop']['stablesettings'] = true; // -aaron 3/20/10
	$wgGroupPermissions['sysop']['autoreview'] = false; // https://bugzilla.wikimedia.org/show_bug.cgi?id=23948


	$wgFeedbackNamespaces = array( NS_MAIN ); // per Aaron 2008-10-06

	unset( $wgGroupPermissions['reviewer'] );
}

elseif ( $wgDBname == 'eowiki' ) {
	$wgFlaggedRevsOverride = false;
	$wgFlaggedRevTags['accuracy']['levels'] = 1;
	$wgFlaggedRevsAutopromote = $wmfStandardAutoPromote;
}

elseif ( $wgDBname == 'fawiki') {
	# Namespaces
	$wgFlaggedRevsNamespaces = array( NS_MAIN, NS_PROJECT, NS_HELP, NS_TEMPLATE, NS_CATEGORY, NS_FILE, 100, 102, 828 );
	# Show only on a per-page basis
	$wgFlaggedRevsOverride = false;
	# We have only one tag with one level
	$wgFlaggedRevTags = array(
		'status' => array( 'levels' => 1, 'quality' => 2, 'pristine' => 3 ),
	);
	# Restrict autoconfirmed to flagging semi-protected
	$wgFlagRestrictions = array(
		'status' => array( 'review' => 1, 'autoreview' => 1 ),
	);
	# Restriction levels for auto-review/review rights
	$wgFlaggedRevsRestrictionLevels = array( '', 'autoconfirmed', 'autoreview' );
	# Use flag "protection" levels
	$wgFlaggedRevsProtection = true;
	# Use current templates/files
	$wgFlaggedRevsHandleIncludes = FR_INCLUDES_CURRENT;
	# Trial quota
	$wgFlaggedRevsProtectQuota = 2000;

	# User groups permissions
	$wgGroupPermissions['autopatrol']['autoreview'] = true;
	$wgGroupPermissions['patroller']['autoreview'] = true;
	$wgGroupPermissions['patroller']['review'] = true;
	$wgGroupPermissions['patroller']['validate'] = true;
	$wgGroupPermissions['patroller']['unreviewedpages'] = true;
	$wgGroupPermissions['sysop']['review'] = true;
	$wgGroupPermissions['sysop']['validate'] = true;
	$wgGroupPermissions['sysop']['unreviewedpages'] = true;
	$wgGroupPermissions['sysop']['stablesettings'] = true;

	# Remove all user groups (editor, reviewer, autoreview)
	unset( $wgGroupPermissions['editor'], $wgGroupPermissions['reviewer'], $wgGroupPermissions['autoreview'] );
	$wgAddGroups['sysop'] = array_diff( $wgAddGroups['sysop'], array( 'editor', 'autoreview' ) );
	$wgRemoveGroups['sysop'] = array_diff( $wgRemoveGroups['sysop'], array( 'editor', 'autoreview' ) );
	$wgAddGroups['bureaucrat'] = array_diff( $wgAddGroups['bureaucrat'], array( 'reviewer' ) );
	$wgRemoveGroups['bureaucrat'] = array_diff( $wgRemoveGroups['bureaucrat'], array( 'reviewer' ) );
}

elseif ( $wgDBname == 'fawikinews' ) {
	$wgFlaggedRevsAutoReviewNew = false;
	$wgFlaggedRevsNamespaces = array_merge( $wgFlaggedRevsNamespaces, array( NS_CATEGORY, 100 ) );
	$wgGroupPermissions['editor']['rollback'] = true;
	$wgGroupPermissions['editor']['autoreview'] = false;
	$wgGroupPermissions['sysop']['stablesettings'] = true;
	$wgGroupPermissions['sysop']['autoreview'] = false;

	unset( $wgGroupPermissions['reviewer'] );
}

elseif ( $wgDBname == 'fiwiki' ) {
	//$wgFlaggedRevTags = array( 'accuracy'=>2 );
	$wgFlaggedRevsAutoReview = true;
	$wgFlaggedRevsAutoReviewNew = true;
	$wgFlaggedRevsOverride = false;
	$wgSimpleFlaggedRevsUI = false;
	$wgGroupPermissions['sysop']['review'] = true;
	$wgGroupPermissions['sysop']['stablesettings'] = true;
	$wgGroupPermissions['sysop']['unreviewedpages'] = true;

	$wgFlaggedRevTags = array(
		'accuracy' => array( 'levels' => 3, 'quality' => 3, 'pristine' => 4 ),
	);
	$wgFlagRestrictions = array(
		'accuracy' => array( 'review' => 3, 'autoreview' => 2 ),
	);
}

elseif ( $wgDBname == 'frwikinews' ) {
	$wgFlaggedRevsNamespaces = array_merge( $wgFlaggedRevsNamespaces, array( 104, 106 ) );
	$wgFlaggedRevsAutopromote = $wmfStandardAutoPromote;
	$wgGroupPermissions['sysop']['stablesettings'] = true;
}

elseif ( $wgDBname == 'hewikisource' ) {
	$wgFlaggedRevsNamespaces = array_merge( $wgFlaggedRevsNamespaces, array( 100, 104, 106, 108, 110, 112 ) );
	$wgFlaggedRevTags = array( 'completeness' => 3, 'accuracy' => 3, 'formatting' => 3 );
	$wgFlaggedRevValues = 4;
	$wgFlaggedRevsAutoReviewNew = false;
	$wgFlagRestrictions = array(
		'completeness' => array( 'review' => 3, 'autoreview' => 3 ),
		'accuracy'     => array( 'review' => 3, 'autoreview' => 3 ),
		'formatting'   => array( 'review' => 3, 'autoreview' => 3 ),
	);
	$wgFlaggedRevsAutopromote = $wmfStandardAutoPromote;
	$wgGroupPermissions['sysop']['stablesettings'] = true; // -aaron 3/20/10
}
# bug 29911
elseif ( $wgDBname == 'hiwiki' ) {
	// # namespaces
	$wgFlaggedRevsNamespaces = array_merge( $wgFlaggedRevsNamespaces, array( NS_PROJECT, NS_CATEGORY, 100 ) ); # 100 = Portal
	# Show only on a per-page basis
	$wgFlaggedRevsOverride = false;
	# We have only one tag with one level
	$wgFlaggedRevTags = array(
		'status' => array( 'levels' => 1, 'quality' => 2, 'pristine' => 3 ),
	);
	# Restrict autoconfirmed to flagging semi-protected
	$wgFlagRestrictions = array(
		'status' => array( 'review' => 1, 'autoreview' => 1 ),
	);
	# Restriction levels for auto-review/review rights
	$wgFlaggedRevsRestrictionLevels = array( '', 'autoconfirmed', 'review', 'sysop' );
	# Use flag "protection" levels
	$wgFlaggedRevsProtection = true;
	# Use current templates/files
	$wgFlaggedRevsHandleIncludes = FR_INCLUDES_CURRENT;
	# Group permissions for autoconfirmed
	$wgGroupPermissions['autoconfirmed']['autoreview'] = true;

	# Group permissions for sysops
	$wgGroupPermissions['sysop']['review']       = true;
	$wgGroupPermissions['sysop']['stablesettings'] = true;

	# Group permissions for non-reviewers
	$wgGroupPermissions['bot']['autoreview'] = true;
	# Remove 'editor' group
	unset( $wgGroupPermissions['editor'] );
	$wgAddGroups['sysop'] = array_diff( $wgAddGroups['sysop'], array( 'editor' ) );
	$wgRemoveGroups['sysop'] = array_diff( $wgRemoveGroups['sysop'], array( 'editor' ) );
}

elseif ( $wgDBname == 'huwiki' ) {
	// # UI
	$wgFlaggedRevsLowProfile = false;

	// # namespaces
	$wgFlaggedRevsNamespaces = array_merge( $wgFlaggedRevsNamespaces, array( NS_CATEGORY, 100 ) ); # 100 = Portal
	$wgFeedbackNamespaces = array( NS_MAIN );

	// # levels
	$wgFlaggedRevsFeedbackTags = array( 'reliability' => 3, 'completeness' => 2, 'npov' => 2, 'presentation' => 1 );

	// # reviewers
	$wgGroupPermissions['editor']['rollback'] = true;
	$wgGroupPermissions['sysop']['validate'] = true;
	$wgGroupPermissions['sysop']['review'] = true;
	$wgGroupPermissions['sysop']['unreviewedpages'] = true;
	$wgGroupPermissions['sysop']['patrolmarks']     = true;
	$wgGroupPermissions['sysop']['stablesettings'] = true;
	unset( $wgGroupPermissions['reviewer'] );

	// # non-reviewers
	$wgGroupPermissions['trusted']['autoreview'] = true;
	$wgGroupPermissions['trusted']['autopatrol'] = true;
	$wgGroupPermissions['bot']['autoreview'] = true;

	$wgGroupPermissions['*']['feedback'] = true;

	// # rights management
	$wgAddGroups['bureaucrat'][] = 'editor';
	$wgRemoveGroups['bureaucrat'][] = 'editor';
	$wgAddGroups['bureaucrat'][] = 'trusted';
	$wgRemoveGroups['bureaucrat'][] = 'trusted';
	// # Normally admins promote/demote editors...not here
	if ( is_array( $wgAddGroups['sysop'] ) )
		unset( $wgAddGroups['sysop'][ array_search( 'editor', $wgAddGroups['sysop'] ) ] );
	if ( is_array( $wgRemoveGroups['sysop'] ) )
		unset( $wgRemoveGroups['sysop'][ array_search( 'editor', $wgRemoveGroups['sysop'] ) ] );
}

elseif ( $wgDBname == 'iawiki' ) {
	$wgFlaggedRevsOverride = false;
	$wgFlaggedRevTags['accuracy']['levels'] = 1;
}

elseif ( $wgDBname == 'iswiktionary' ) {
	$wgFlaggedRevsOverride = false;
}

elseif ( $wgDBname == 'kawiki' ) {
	$wgFlaggedRevsNamespaces[] = NS_CATEGORY;
	$wgFlaggedRevTags['accuracy']['levels'] = 1;
	$wgGroupPermissions['trusted']['autoreview'] = true;
}

elseif ( $wgDBname == 'metawiki' ) {
	$wgFlaggedRevsNamespaces = array ( 480 /* NS_ZERO */ );
	$wgFlaggedRevTags = array(
		'safeness' => array( 'levels' => 1, 'quality' => 2, 'pristine' => 3 ),
	);
	$wgFlagRestrictions = array( 'safeness' => array( 'review' => 1 ) );
	$wgFlaggedRevsAutopromote = false;
	unset( $wgGroupPermissions['editor'] );
	unset( $wgGroupPermissions['reviewer'] );
	$wgGroupPermissions['zeroadmin']['review'] = true;
	$wgGroupPermissions['zeroadmin']['autoreview'] = true;
}

elseif ( $wgDBname == 'plwiki' ) {
	// Bugs 43617, 48043 
	$wgFlaggedRevsNamespaces = array( NS_MAIN, NS_TEMPLATE, NS_CATEGORY, NS_HELP, 100, 828 );
	$wgFlaggedRevTags['accuracy']['levels'] = 1;

	$wgFlaggedRevsAutopromote = $wmfStandardAutoPromote;
	$wgFlaggedRevsAutopromote['days'] = 90;
	$wgFlaggedRevsAutopromote['edits'] = 500;
	$wgFlaggedRevsAutopromote['spacing'] = 3;
	$wgFlaggedRevsAutopromote['benchmarks'] = 15;
	$wgFlaggedRevsAutopromote['recentContentEdits'] = 5;
	$wgFlaggedRevsAutopromote['totalContentEdits'] = 500;
	$wgFlaggedRevsAutopromote['uniqueContentPages'] = 10;
	$wgFlaggedRevsAutopromote['editComments'] = 30;
	$wgFlaggedRevsAutopromote['userpageBytes'] = 100;
}

elseif ( $wgDBname == 'plwiktionary' ) {
	$wgFlaggedRevsNamespaces = array( NS_MAIN, NS_IMAGE, NS_TEMPLATE, 100, 102, 828 ); // bug 53373
}

elseif ( $wgDBname == 'ptwiki' ) { // bug 54828
	$wgFlaggedRevsNamespaces = array( NS_MAIN, NS_TEMPLATE, 102, 828 );
	# Show only on a per-page basis
	$wgFlaggedRevsOverride = false;
	# We have only one tag with one level
	$wgFlaggedRevTags = array(
		'status' => array( 'levels' => 1, 'quality' => 2, 'pristine' => 3 ),
	);
	# Restrict autoconfirmed to flagging semi-protected
	$wgFlagRestrictions = array(
		'status' => array( 'review' => 1, 'autoreview' => 1 ),
	);
	# Restriction levels for autoconfirmed rights
	$wgFlaggedRevsRestrictionLevels = array( '', 'autoconfirmed' );
	# Use flag "protection" levels
	$wgFlaggedRevsProtection = true;
	# Use current templates/files
	$wgFlaggedRevsHandleIncludes = FR_INCLUDES_CURRENT;

	# Protect quota
	$wgFlaggedRevsProtectQuota = 300;

	# Group permissions
	$wgGroupPermissions['autoconfirmed']['autoreview'] = true;
	$wgGroupPermissions['autoreviewer']['review'] = true;
	$wgGroupPermissions['autoreviewer']['validate'] = true;
	$wgGroupPermissions['rollbacker']['review'] = true;
	$wgGroupPermissions['rollbacker']['validate'] = true;
	$wgGroupPermissions['eliminator']['review'] = true;
	$wgGroupPermissions['eliminator']['validate'] = true;
	$wgGroupPermissions['bureaucrat']['review'] = true;
	$wgGroupPermissions['bureaucrat']['validate'] = true;
	$wgGroupPermissions['sysop']['review'] = true;
	$wgGroupPermissions['sysop']['validate'] = true;
	$wgGroupPermissions['sysop']['stablesettings'] = true;

	# Remove 'editor', 'reviewer' and 'autoreview' groups
	unset( $wgGroupPermissions['editor'], $wgGroupPermissions['reviewer'], $wgGroupPermissions['autoreview'] );
	$wgAddGroups['sysop'] = array_diff( $wgAddGroups['sysop'], array( 'editor', 'reviewer' ) );
	$wgRemoveGroups['sysop'] = array_diff( $wgRemoveGroups['sysop'], array( 'editor', 'reviewer' ) );
	$wgAddGroups['bureaucrat'] = array_diff( $wgAddGroups['bureaucrat'], array( 'reviewer' ) );
	$wgRemoveGroups['bureaucrat'] = array_diff( $wgRemoveGroups['bureaucrat'], array( 'reviewer' ) );
}

elseif ( $wgDBname == 'ptwikibooks' ) {
	// Sets the most recent version as shown
	$wgFlaggedRevsOverride = false;

	$wgFlaggedRevsNamespaces = array(NS_MAIN, NS_TEMPLATE, NS_HELP, NS_PROJECT, 828);

	$wgSimpleFlaggedRevsUI = false;
	$wgFlaggedRevComments = false;

	$wgFlaggedRevsAutopromote = array(
		'days' => 30, # days since registration
		'edits' => 100, # total edit count
		'excludeDeleted' => true, # exclude deleted edits from 'edits' count above?
		'spacing' => 2, # spacing of edit intervals
		'benchmarks' => 8, # how many edit intervals are needed?
		'recentContentEdits' => 5, # $wgContentNamespaces edits in recent changes
		'totalContentEdits' => 50,  # $wgContentNamespaces edits
		'uniqueContentPages' => 10, # $wgContentNamespaces unique pages edited
		'editComments' => 50, # how many edit comments used?
		'email' => true, # user must be emailconfirmed?
		'userpage' => false, # user must have a userpage?
		'uniqueIPAddress' => false, # If $wgPutIPinRC is true, users sharing IPs won't be promoted
		'neverBlocked' => true, # Can users that were blocked be promoted?
	) + $wmfStandardAutoPromote;

	$wgGroupPermissions['editor']['rollback'] = true;
	$wgGroupPermissions['sysop']['review'] = true;
	$wgGroupPermissions['sysop']['stablesettings'] = true;
	$wgGroupPermissions['sysop']['validate'] = true;
}

elseif ( $wgDBname == 'ptwikinews' ) {
	$wgFlaggedRevsAutopromote = $wmfStandardAutoPromote;
	$wgFlaggedRevsAutopromote['days'] = 30;
	$wgGroupPermissions['sysop']['stablesettings'] = true; // -aaron 3/20/10
}

elseif ( $wgDBname == 'ptwikisource' ) {
	$wgFlaggedRevsLowProfile = false;
	$wgFlaggedRevsNamespaces = array_merge( $wgFlaggedRevsNamespaces, array( 102, 104, 106, 108, 110 ) );
	$wgFlaggedRevTags['accuracy']['levels'] = 1;
	$wgFlaggedRevsAutopromote = $wmfStandardAutoPromote;
}

elseif ( $wgDBname == 'ruwiki' ) {

	// Bugs 37675, 47337
	$wgFlaggedRevsNamespaces = array( NS_MAIN, NS_FILE, NS_TEMPLATE, NS_CATEGORY, 100, 828 );

	$wgFlaggedRevTags['accuracy']['levels'] = 3; // Is this needed?
	$wgFlaggedRevsOverride = false;

	// https://bugzilla.wikimedia.org/show_bug.cgi?id=15478
	$wgGroupPermissions['autoeditor']['autoreview'] = true;
	$wgGroupPermissions['autoeditor']['autoconfirmed'] = true;

	$wgGroupPermissions['sysop']['stablesettings'] = true; // -aaron 3/20/10
}

elseif ( $wgDBname == 'ruwikinews' ) {
	$wgFlaggedRevsNamespaces = array( NS_MAIN, NS_CATEGORY, NS_TEMPLATE );
	$wgFlaggedRevsWhitelist = array( 'Main_Page' );
	$wgGroupPermissions['sysop']['stablesettings'] = true;
	$wgGroupPermissions['sysop']['review'] = true;
	unset($wgGroupPermissions['reviewer']);
}

elseif ( $wgDBname == 'ruwikiquote' ) {}

elseif ( $wgDBname == 'ruwiktionary' ) {
	$wgFlaggedRevsNamespaces = array_merge( $wgFlaggedRevsNamespaces, array( NS_PROJECT, NS_CATEGORY, 100, 104, 106 ) );
}

elseif ( $wgDBname == 'idwiki' ) {}

elseif ( $wgDBname == 'ruwikisource' ) {
	$wgFlaggedRevsNamespaces = array_merge( $wgFlaggedRevsNamespaces, array( NS_HELP, NS_PROJECT, 104, 106 ) );
	$wgFlaggedRevsAutopromote = $wmfStandardAutoPromote;

	$wgGroupPermissions['sysop']['stablesettings'] = true; // -aaron 3/20/10
}

elseif ( $wgDBname == 'sqwiki' ) {
	// Bug 42782
	// @link https://bugzilla.wikimedia.org/show_bug.cgi?id=42782
	//
	// - Auto-promotion for registered users. When they reach 300 edits in 10 or more
	// unique articles with a maximum of 5% reverted edits in 60 days or more since
	// registration they must be auto-promoted to reviewer group.
	// - Auto-promotion for registered users. When they reach 100 edits in 10 or more
	// unique pages with a maximum of 5% reverted edits in 30 days or more since
	// registration they must be auto-promoted to autoreviewer (or autopatrolled)
	// group.
	$wgFlaggedRevsAutopromote = $wmfStandardAutoPromote;
	$wgFlaggedRevsAutopromote['days'] = 60; # days since registration
	$wgFlaggedRevsAutopromote['edits'] = 300; # total edit count
	$wgFlaggedRevsAutopromote['excludeDeleted'] = true; # exclude deleted edits from 'edits' count above?
	$wgFlaggedRevsAutopromote['spacing'] = 3; # spacing of edit intervals
	$wgFlaggedRevsAutopromote['benchmarks'] = 15; # how many edit intervals are needed?
	$wgFlaggedRevsAutopromote['recentContentEdits'] = 10; # $wgContentNamespaces edits in recent changes
	$wgFlaggedRevsAutopromote['uniqueContentPages'] = 10; # $wgContentNamespaces unique pages edited
	$wgFlaggedRevsAutopromote['neverBlocked'] = false; # user must be emailconfirmed?

	$wgFlaggedRevsAutoconfirm = array(
		'days'                => 30, # days since registration
		'edits'               => 100, # total edit count
		'spacing'             => 3, # spacing of edit intervals
		'benchmarks'          => 7, # how many edit intervals are needed?
		'excludeLastDays'     => 2, # exclude the last X days of edits from edit counts
		// Either totalContentEdits reqs OR totalCheckedEdits requirements needed
		'totalContentEdits'   => 150, # $wgContentNamespaces edits OR...
		'totalCheckedEdits'   => 50, # ...Edits before the stable version of pages
		'uniqueContentPages'  => 8, # $wgContentNamespaces unique pages edited
		'editComments'        => 20, # how many edit comments used?
		'email'               => false, # user must be emailconfirmed?
		'neverBlocked'        => true, # Can users that were blocked be promoted?
	);

	$wgGroupPermissions['sysop']['review'] = true;
	$wgGroupPermissions['sysop']['validate'] = true;
}

elseif ( $wgDBname == 'trwiki' ) {
	unset( $wgGroupPermissions['reviewer'] ); // Bug 38690
	$wgAddGroups['bureaucrat'] = array_diff( $wgAddGroups['bureaucrat'], array( 'reviewer' ) ); // Bug 38690
	$wgRemoveGroups['bureaucrat'] = array_diff( $wgRemoveGroups['bureaucrat'], array( 'reviewer' ) ); // Bug 38690

	unset( $wgGroupPermissions['editor'] ); // Bug 38690
	$wgAddGroups['sysop'] = array_diff( $wgAddGroups['sysop'], array( 'editor' ) ); // Bug 38690
	$wgRemoveGroups['sysop'] = array_diff( $wgRemoveGroups['sysop'], array( 'editor' ) ); // Bug 38690

	// Bug 44587:
	$wgFlaggedRevsNamespaces[] = 100 /* NS_PORTAL */;
	$wgFlaggedRevsNamespaces[] = NS_HELP;
}

elseif ( $wgDBname == 'trwikiquote' ) {
	unset( $wgGroupPermissions['reviewer'] );
}

elseif( $wgDBname == 'ukwiki' ) {
	$wgFlaggedRevValues = 1;
	$wgFlaggedRevsNamespaces = array_merge( $wgFlaggedRevsNamespaces, array( NS_MAIN, NS_FILE, NS_CATEGORY, NS_TEMPLATE ) );
	$wgFlaggedRevTags['accuracy']['levels'] = 3;
	$wgFlaggedRevsOverride = false;
	$wgGroupPermissions['sysop']['stablesettings'] = true;
}

elseif ( $wgDBname == 'ukwiktionary' ) {}

elseif ( $wgDBname == 'plwikisource' ) {
	$wgFlaggedRevsNamespaces = array_merge( $wgFlaggedRevsNamespaces, array( NS_CATEGORY, NS_HELP, 100, 102, 104 ) );

	$wgFlaggedRevsAutopromote = $wmfStandardAutoPromote;
	$wgFlaggedRevsAutopromote['edits'] = 100;
	$wgFlaggedRevsAutopromote['totalContentEdits'] = 100;
	$wgFlaggedRevsAutopromote['days'] = 14;

	$wgGroupPermissions['editor']['rollback'] = true;
}
elseif ( $wgDBname == 'vecwiki' ) {
        $wgFlaggedRevsNamespaces[] = NS_CATEGORY;
        $wgFlaggedRevTags['accuracy']['levels'] = 3; // Is this needed?
        $wgFlaggedRevsOverride = false;

        // https://bugzilla.wikimedia.org/show_bug.cgi?id=15478
        $wgGroupPermissions['autoeditor']['autoreview'] = true;
        $wgGroupPermissions['autoeditor']['autoconfirmed'] = true;

        $wgGroupPermissions['sysop']['stablesettings'] = true; // -aaron 3/20/10
}

elseif ( $wgDBname == 'zh_classicalwiki' ) {}


# All wikis...
$wgFlaggedRevsStatsAge = false;
