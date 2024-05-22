<?php

// Inline comments are often used for noting the task(s) associated with specific configuration
// and requiring comments to be on their own line would reduce readability for this file
// phpcs:disable MediaWiki.WhiteSpace.SpaceBeforeSingleLineComment.NewLineComment

/**
 * Configuration for the EventLogging extension.
 */

return [

// List of streams to register for use with the EventLogging extension.
// EventLogging will request the stream config (defined in wgEventStreams
// in ext-EventStreamConfig.php) for each of the stream names listed here.
'wgEventLoggingStreamNames' => [
	'default' => [
		'eventlogging_CentralNoticeBannerHistory',
		'eventlogging_CentralNoticeImpression',
		'eventlogging_ContentTranslationAbuseFilter',
		'eventlogging_CodeMirrorUsage',
		'eventlogging_CpuBenchmark',
		'eventlogging_DesktopWebUIActionsTracking',
		'eventlogging_EditAttemptStep',
		'eventlogging_HelpPanel',
		'eventlogging_HomepageModule',
		'eventlogging_HomepageVisit',
		'eventlogging_LandingPageImpression',
		'eventlogging_MobileWebUIActionsTracking',
		'eventlogging_NavigationTiming',
		'eventlogging_NewcomerTask',
		'eventlogging_PaintTiming',
		'eventlogging_PrefUpdate',
		'eventlogging_QuickSurveyInitiation',
		'eventlogging_QuickSurveysResponses',
		'eventlogging_ReferencePreviewsBaseline',
		'eventlogging_ReferencePreviewsCite',
		'eventlogging_ReferencePreviewsPopups',
		'eventlogging_SaveTiming',
		'eventlogging_ServerSideAccountCreation',
		'eventlogging_SpecialInvestigate',
		'eventlogging_SearchSatisfaction',
		'eventlogging_TemplateDataApi',
		'eventlogging_TemplateDataEditor',
		'eventlogging_TemplateWizard',
		'eventlogging_Test',
		'eventlogging_TwoColConflictConflict',
		'eventlogging_TwoColConflictExit',
		'eventlogging_UniversalLanguageSelector',
		'eventlogging_VirtualPageView',
		'eventlogging_VisualEditorFeatureUse',
		'eventlogging_VisualEditorTemplateDialogUse',
		'eventlogging_WikibaseTermboxInteraction',
		'eventlogging_WikidataCompletionSearchClicks',
		'eventlogging_WMDEBannerEvents',
		'eventlogging_WMDEBannerInteractions',
		'eventlogging_WMDEBannerSizeIssue',
		'mediawiki.client.session_tick',
		'mediawiki.content_translation_event',
		'mediawiki.talk_page_edit',
		'mediawiki.mediasearch_interaction',
		'mediawiki.searchpreview',
		'mediawiki.structured_task.article.link_suggestion_interaction',
		'mediawiki.structured_task.article.image_suggestion_interaction',
		'mediawiki.pref_diff',
		'mediawiki.skin_diff',
		'mediawiki.reading_depth',
		'mediawiki.web_ab_test_enrollment',
		'mediawiki.web_ui_scroll',
		'mediawiki.welcomesurvey.interaction',
		'test.instrumentation',
		'test.instrumentation.sampled',
		'wd_propertysuggester.client_side_property_request',
		'wd_propertysuggester.server_side_property_request',
		'mediawiki.mentor_dashboard.visit',
		'mediawiki.mentor_dashboard.personalized_praise',
		'mediawiki.mentor_dashboard.interaction',
		'mediawiki.ipinfo_interaction',
		'mediawiki.ip_reputation.score',
		'mediawiki.editgrowthconfig',
		'mediawiki.wikistories_consumption_event',
		'mediawiki.wikistories_contribution_event',
		'mediawiki.accountcreation_block',
		'mediawiki.editattempt_block',
		'mediawiki.maps_interaction',
		'development.network.probe',
		'wikifunctions.ui',
		'mediawiki.web_ui_actions',
		'mediawiki.web_ui_scroll_migrated',
		'mediawiki.reference_previews',
		'mediawiki.product_metrics.wikifunctions_ui',
		'mediawiki.product_metrics.wikilambda_api',
		'mediawiki.accountcreation.account_conversion',
		'mediawiki.accountcreation.login',
	],
],

// EventLogging JavaScript client code will POST events to this URI.
'wgEventLoggingServiceUri' => [
	'default' => 'https://intake-analytics.wikimedia.org/v1/events?hasty=true',
],

// Historically, EventLogging would register Schemas and revisions it used
// via the EventLoggingSchemas extension attribute like in
// https://gerrit.wikimedia.org/r/plugins/gitiles/mediawiki/extensions/WikimediaEvents/+/master/extension.json#51
// It also overrides these with the $wgEventLoggingSchemas global.
// While in the process of migrating legacy EventLogging events to event platform,
// use the wgEventLoggingSchemas to override the extension attribute for an incremental rollout.
// This will be removed from mediawiki-config once all schemas have been successfully migrated
// and the EventLoggingSchemas extension attributes are all set to Event Platform schema URIs.
// ONLY Legacy EventLogging schemas need to go here.  This is the switch that tells
// EventLogging to POST to EventGate rather than GET to EventLogging beacon.
// https://phabricator.wikimedia.org/T238230
'wgEventLoggingSchemas' => [
	'default' => [
	],
],

];
