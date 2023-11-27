<?php

// Inline comments are often used for noting the task(s) associated with specific configuration
// and requiring comments to be on their own line would reduce readability for this file
// phpcs:disable MediaWiki.WhiteSpace.SpaceBeforeSingleLineComment.NewLineComment

/**
 * Event Stream Configuration via the EventStreamConfig extension.
 * https://wikitech.wikimedia.org/wiki/Event_Platform/Stream_Configuration
 */

return [

// Stream config default settings.
// The EventStreamConfig extension will add these
// settings to each entry in wgEventStreams if
// the entry does not already have the setting.
'wgEventStreamsDefaultSettings' => [
	'default' => [
		'topic_prefixes' => [ 'eqiad.', 'codfw.' ],
		// Canary events are produced into streams
		// for ingestion monitoring purposes.
		// Absence of these events either means
		// that canary event production is failing, or
		// another component of the event pipeline is failing.
		// Without canary events, we cannot differentiate between
		// an idle stream and a broken pipeline.
		// This is explicitly disabled for MW state change (EventBus) streams
		// until https://phabricator.wikimedia.org/T266798 is done.
		'canary_events_enabled' => true,
		// By default, all events should be imported into Hadoop.
		// The analytics hadoop ingestion consumer (Gobblin) will
		// look for streams to ingest using these settings.
		// Each stream in a job should have similar volumes to allow
		// the job to scale properly and not cause stream ingestion starvation.
		// The default job_name is event_default.
		// Override this if the stream should be imported by a different job,
		// or disabled altogether.
		'consumers' => [
			'analytics_hadoop_ingestion' => [
				'job_name' => 'event_default',
				'enabled' => true,
			],
		],
	],
],

/*
 * Event stream configuration. A list of stream configurations.
 * Each item must have a 'stream' setting of either the specific
 * stream name or a regex patterns to matching stream names.
 * Each item must also at minimum include the schema_title of the
 * JSONSchema that events in the stream must conform to.
 * This is used by the EventStreamConfig extension
 * to allow for remote configuration of streams.  It
 * is used by the EventLogging extension to vary e.g.
 * sample rate that browsers use when producing events,
 * as well as the eventgate-analytics-external service
 * to dynamically add event streams that it should accept
 * and validate.
 * See https://gerrit.wikimedia.org/r/plugins/gitiles/mediawiki/extensions/EventStreamConfig/#mediawiki-config
 */
'wgEventStreams' => [
	'default' => [

		/**
		 * == eventgate-analytics-external streams ==
		 * These are produced by EventLogging
		 * as well as other instrumentation sources.
		 *
		 * EventLogging 'legacy' streams start with eventlogging_.
		 * https://phabricator.wikimedia.org/T238230
		 * These schemas have been migrated from metawiki to the schemas/event/secondary repository at
		 * https://schema.wikimedia.org/#!/secondary/jsonschema/analytics/legacy
		 *
		 * We've configured default topic_prefixes, but legacy eventlogging_* topics
		 * do not use topic prefixes.  Explicitly set topic_prefixes to null to disable
		 * implicit topic prefixing by the EventStreamConfig extension.
		 *
		 * eventgate-analytics-external requests stream config at startup as well
		 * as dynamically at runtime.  eventgate-analytics-external caches its stream
		 * configs temporarily, so any changes you make here will eventually be
		 * picked up.  When eventgate-analytics-external sees a stream it doesn't
		 * have in cache, it will request its configs immediately.
		 */
		'eventlogging_CentralNoticeBannerHistory' => [
			'schema_title' => 'analytics/legacy/centralnoticebannerhistory',
			'topic_prefixes' => null,
			'destination_event_service' => 'eventgate-analytics-external',
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					'job_name' => 'eventlogging_legacy',
					'enabled' => true,
				],
			]
		],
		'eventlogging_CentralNoticeImpression' => [
			'schema_title' => 'analytics/legacy/centralnoticeimpression',
			'topic_prefixes' => null,
			'destination_event_service' => 'eventgate-analytics-external',
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					'job_name' => 'eventlogging_legacy',
					'enabled' => true,
				],
			]
		],
		'eventlogging_CodeMirrorUsage' => [
			'schema_title' => 'analytics/legacy/codemirrorusage',
			'topic_prefixes' => null,
			'destination_event_service' => 'eventgate-analytics-external',
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					'job_name' => 'eventlogging_legacy',
					'enabled' => true,
				],
			]
		],
		'eventlogging_ContentTranslationAbuseFilter' => [
			'schema_title' => 'analytics/legacy/contenttranslationabusefilter',
			'topic_prefixes' => null,
			'destination_event_service' => 'eventgate-analytics-external',
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					'job_name' => 'eventlogging_legacy',
					'enabled' => true,
				],
			]
		],
		'eventlogging_CpuBenchmark' => [
			'schema_title' => 'analytics/legacy/cpubenchmark',
			'topic_prefixes' => null,
			'destination_event_service' => 'eventgate-analytics-external',
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					'job_name' => 'eventlogging_legacy',
					'enabled' => true,
				],
			]
		],
		'eventlogging_DesktopWebUIActionsTracking' => [
			'schema_title' => 'analytics/legacy/desktopwebuiactionstracking',
			'topic_prefixes' => null,
			'destination_event_service' => 'eventgate-analytics-external',
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					'job_name' => 'eventlogging_legacy',
					'enabled' => true,
				],
			]
		],
		'eventlogging_EditAttemptStep' => [
			'schema_title' => 'analytics/legacy/editattemptstep',
			'topic_prefixes' => null,
			'destination_event_service' => 'eventgate-analytics-external',
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					'job_name' => 'eventlogging_legacy',
					'enabled' => true,
				],
			]
		],
		'eventlogging_FeaturePolicyViolation' => [
			'schema_title' => 'analytics/legacy/featurepolicyviolation',
			'topic_prefixes' => null,
			'destination_event_service' => 'eventgate-analytics-external',
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					'job_name' => 'eventlogging_legacy',
					'enabled' => true,
				],
			]
		],
		'eventlogging_HelpPanel' => [
			'schema_title' => 'analytics/legacy/helppanel',
			'topic_prefixes' => null,
			'destination_event_service' => 'eventgate-analytics-external',
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					'job_name' => 'eventlogging_legacy',
					'enabled' => true,
				],
			]
		],
		'eventlogging_HomepageModule' => [
			'schema_title' => 'analytics/legacy/homepagemodule',
			'topic_prefixes' => null,
			'destination_event_service' => 'eventgate-analytics-external',
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					'job_name' => 'eventlogging_legacy',
					'enabled' => true,
				],
			]
		],
		'eventlogging_HomepageVisit' => [
			'schema_title' => 'analytics/legacy/homepagevisit',
			'topic_prefixes' => null,
			'destination_event_service' => 'eventgate-analytics-external',
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					'job_name' => 'eventlogging_legacy',
					'enabled' => true,
				],
			]
		],
		'eventlogging_InukaPageView' => [
			'schema_title' => 'analytics/legacy/inukapageview',
			'topic_prefixes' => null,
			'destination_event_service' => 'eventgate-analytics-external',
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					'job_name' => 'eventlogging_legacy',
					'enabled' => true,
				],
			]
		],
		'eventlogging_KaiOSAppFirstRun' => [
			'schema_title' => 'analytics/legacy/kaiosappfirstrun',
			'topic_prefixes' => null,
			'destination_event_service' => 'eventgate-analytics-external',
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					'job_name' => 'eventlogging_legacy',
					'enabled' => true,
				],
			]
		],
		'eventlogging_KaiOSAppFeedback' => [
			'schema_title' => 'analytics/legacy/kaiosappfeedback',
			'topic_prefixes' => null,
			'destination_event_service' => 'eventgate-analytics-external',
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					'job_name' => 'eventlogging_legacy',
					'enabled' => true,
				],
			]
		],
		'eventlogging_LandingPageImpression' => [
			'schema_title' => 'analytics/legacy/landingpageimpression',
			'topic_prefixes' => null,
			'destination_event_service' => 'eventgate-analytics-external',
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					'job_name' => 'eventlogging_legacy',
					'enabled' => true,
				],
			]
		],
		'eventlogging_MobileWebUIActionsTracking' => [
			'schema_title' => 'analytics/legacy/mobilewebuiactionstracking',
			'topic_prefixes' => null,
			'destination_event_service' => 'eventgate-analytics-external',
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					'job_name' => 'eventlogging_legacy',
					'enabled' => true,
				],
			]
		],
		'eventlogging_NavigationTiming' => [
			'schema_title' => 'analytics/legacy/navigationtiming',
			'topic_prefixes' => null,
			'destination_event_service' => 'eventgate-analytics-external',
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					'job_name' => 'eventlogging_legacy',
					'enabled' => true,
				],
			]
		],
		'eventlogging_NewcomerTask' => [
			'schema_title' => 'analytics/legacy/newcomertask',
			'topic_prefixes' => null,
			'destination_event_service' => 'eventgate-analytics-external',
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					'job_name' => 'eventlogging_legacy',
					'enabled' => true,
				],
			]
		],
		'eventlogging_PaintTiming' => [
			'schema_title' => 'analytics/legacy/painttiming',
			'topic_prefixes' => null,
			'destination_event_service' => 'eventgate-analytics-external',
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					'job_name' => 'eventlogging_legacy',
					'enabled' => true,
				],
			]
		],
		'eventlogging_PrefUpdate' => [
			'schema_title' => 'analytics/legacy/prefupdate',
			'topic_prefixes' => null,
			'destination_event_service' => 'eventgate-analytics-external',
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					'job_name' => 'eventlogging_legacy',
					'enabled' => true,
				],
			]
		],
		'eventlogging_WikipediaPortal' => [
			'schema_title' => 'analytics/legacy/wikipediaportal',
			'topic_prefixes' => null,
			'destination_event_service' => 'eventgate-analytics-external',
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					'job_name' => 'eventlogging_legacy',
					'enabled' => true,
				],
			]
		],
		'eventlogging_QuickSurveyInitiation' => [
			'schema_title' => 'analytics/legacy/quicksurveyinitiation',
			'topic_prefixes' => null,
			'destination_event_service' => 'eventgate-analytics-external',
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					'job_name' => 'eventlogging_legacy',
					'enabled' => true,
				],
			]
		],
		'eventlogging_QuickSurveysResponses' => [
			'schema_title' => 'analytics/legacy/quicksurveysresponses',
			'topic_prefixes' => null,
			'destination_event_service' => 'eventgate-analytics-external',
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					'job_name' => 'eventlogging_legacy',
					'enabled' => true,
				],
			]
		],
		'eventlogging_ReferencePreviewsBaseline' => [
			'schema_title' => 'analytics/legacy/referencepreviewsbaseline',
			'topic_prefixes' => null,
			'destination_event_service' => 'eventgate-analytics-external',
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					'job_name' => 'eventlogging_legacy',
					'enabled' => true,
				],
			]
		],
		'eventlogging_ReferencePreviewsCite' => [
			'schema_title' => 'analytics/legacy/referencepreviewscite',
			'topic_prefixes' => null,
			'destination_event_service' => 'eventgate-analytics-external',
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					'job_name' => 'eventlogging_legacy',
					'enabled' => true,
				],
			]
		],
		'eventlogging_ReferencePreviewsPopups' => [
			'schema_title' => 'analytics/legacy/referencepreviewspopups',
			'topic_prefixes' => null,
			'destination_event_service' => 'eventgate-analytics-external',
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					'job_name' => 'eventlogging_legacy',
					'enabled' => true,
				],
			]
		],
		'eventlogging_SaveTiming' => [
			'schema_title' => 'analytics/legacy/savetiming',
			'topic_prefixes' => null,
			'destination_event_service' => 'eventgate-analytics-external',
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					'job_name' => 'eventlogging_legacy',
					'enabled' => true,
				],
			]
		],
		'eventlogging_SearchSatisfaction' => [
			'schema_title' => 'analytics/legacy/searchsatisfaction',
			'topic_prefixes' => null,
			'destination_event_service' => 'eventgate-analytics-external',
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					'job_name' => 'eventlogging_legacy',
					'enabled' => true,
				],
			]
		],
		'eventlogging_ServerSideAccountCreation' => [
			'schema_title' => 'analytics/legacy/serversideaccountcreation',
			'topic_prefixes' => null,
			'destination_event_service' => 'eventgate-analytics-external',
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					'job_name' => 'eventlogging_legacy',
					'enabled' => true,
				],
			]
		],
		'eventlogging_SpecialInvestigate' => [
			'schema_title' => 'analytics/legacy/specialinvestigate',
			'topic_prefixes' => null,
			'destination_event_service' => 'eventgate-analytics-external',
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					'job_name' => 'eventlogging_legacy',
					'enabled' => true,
				],
			]
		],
		'eventlogging_SpecialMuteSubmit' => [
			'schema_title' => 'analytics/legacy/specialmutesubmit',
			'topic_prefixes' => null,
			'destination_event_service' => 'eventgate-analytics-external',
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					'job_name' => 'eventlogging_legacy',
					'enabled' => true,
				],
			]
		],
		'eventlogging_SuggestedTagsAction' => [
			'schema_title' => 'analytics/legacy/suggestedtagsaction',
			'topic_prefixes' => null,
			'destination_event_service' => 'eventgate-analytics-external',
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					'job_name' => 'eventlogging_legacy',
					'enabled' => true,
				],
			]
		],
		'eventlogging_TemplateDataApi' => [
			'schema_title' => 'analytics/legacy/templatedataapi',
			'topic_prefixes' => null,
			'destination_event_service' => 'eventgate-analytics-external',
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					'job_name' => 'eventlogging_legacy',
					'enabled' => true,
				],
			]
		],
		'eventlogging_TemplateDataEditor' => [
			'schema_title' => 'analytics/legacy/templatedataeditor',
			'topic_prefixes' => null,
			'destination_event_service' => 'eventgate-analytics-external',
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					'job_name' => 'eventlogging_legacy',
					'enabled' => true,
				],
			]
		],
		'eventlogging_TemplateWizard' => [
			'schema_title' => 'analytics/legacy/templatewizard',
			'topic_prefixes' => null,
			'destination_event_service' => 'eventgate-analytics-external',
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					'job_name' => 'eventlogging_legacy',
					'enabled' => true,
				],
			]
		],
		'eventlogging_Test' => [
			'schema_title' => 'analytics/legacy/test',
			'topic_prefixes' => null,
			'destination_event_service' => 'eventgate-analytics-external',
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					'job_name' => 'eventlogging_legacy',
					'enabled' => true,
				],
			]
		],
		'eventlogging_TranslationRecommendationUserAction' => [
			'schema_title' => 'analytics/legacy/translationrecommendationuseraction',
			'topic_prefixes' => null,
			'destination_event_service' => 'eventgate-analytics-external',
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					'job_name' => 'eventlogging_legacy',
					'enabled' => true,
				],
			]
		],
		'eventlogging_TranslationRecommendationUIRequests' => [
			'schema_title' => 'analytics/legacy/translationrecommendationuirequests',
			'topic_prefixes' => null,
			'destination_event_service' => 'eventgate-analytics-external',
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					'job_name' => 'eventlogging_legacy',
					'enabled' => true,
				],
			]
		],
		'eventlogging_TranslationRecommendationAPIRequests' => [
			'schema_title' => 'analytics/legacy/translationrecommendationapirequests',
			'topic_prefixes' => null,
			'destination_event_service' => 'eventgate-analytics-external',
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					'job_name' => 'eventlogging_legacy',
					'enabled' => true,
				],
			]
		],
		'eventlogging_TwoColConflictConflict' => [
			'schema_title' => 'analytics/legacy/twocolconflictconflict',
			'topic_prefixes' => null,
			'destination_event_service' => 'eventgate-analytics-external',
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					'job_name' => 'eventlogging_legacy',
					'enabled' => true,
				],
			]
		],
		'eventlogging_TwoColConflictExit' => [
			'schema_title' => 'analytics/legacy/twocolconflictexit',
			'topic_prefixes' => null,
			'destination_event_service' => 'eventgate-analytics-external',
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					'job_name' => 'eventlogging_legacy',
					'enabled' => true,
				],
			]
		],
		'eventlogging_UniversalLanguageSelector' => [
			'schema_title' => 'analytics/legacy/universallanguageselector',
			'topic_prefixes' => null,
			'destination_event_service' => 'eventgate-analytics-external',
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					'job_name' => 'eventlogging_legacy',
					'enabled' => true,
				],
			]
		],
		'eventlogging_VirtualPageView' => [
			'schema_title' => 'analytics/legacy/virtualpageview',
			'topic_prefixes' => null,
			'destination_event_service' => 'eventgate-analytics-external',
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					'job_name' => 'eventlogging_legacy',
					'enabled' => true,
				],
			]
		],
		'eventlogging_VisualEditorFeatureUse' => [
			'schema_title' => 'analytics/legacy/visualeditorfeatureuse',
			'topic_prefixes' => null,
			'destination_event_service' => 'eventgate-analytics-external',
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					'job_name' => 'eventlogging_legacy',
					'enabled' => true,
				],
			]
		],
		'eventlogging_VisualEditorTemplateDialogUse' => [
			'schema_title' => 'analytics/legacy/visualeditortemplatedialoguse',
			'topic_prefixes' => null,
			'destination_event_service' => 'eventgate-analytics-external',
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					'job_name' => 'eventlogging_legacy',
					'enabled' => true,
				],
			]
		],
		'eventlogging_WikibaseTermboxInteraction' => [
			'schema_title' => 'analytics/legacy/wikibasetermboxinteraction',
			'topic_prefixes' => null,
			'destination_event_service' => 'eventgate-analytics-external',
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					'job_name' => 'eventlogging_legacy',
					'enabled' => true,
				],
			]
		],
		'eventlogging_WikidataCompletionSearchClicks' => [
			'schema_title' => 'analytics/legacy/wikidatacompletionsearchclicks',
			'topic_prefixes' => null,
			'destination_event_service' => 'eventgate-analytics-external',
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					'job_name' => 'eventlogging_legacy',
					'enabled' => true,
				],
			]
		],
		'eventlogging_WMDEBannerEvents' => [
			'schema_title' => 'analytics/legacy/wmdebannerevents',
			'topic_prefixes' => null,
			'destination_event_service' => 'eventgate-analytics-external',
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					'job_name' => 'eventlogging_legacy',
					'enabled' => true,
				],
			]
		],
		'eventlogging_WMDEBannerInteractions' => [
			'schema_title' => 'analytics/legacy/wmdebannerinteractions',
			'topic_prefixes' => null,
			'destination_event_service' => 'eventgate-analytics-external',
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					'job_name' => 'eventlogging_legacy',
					'enabled' => true,
				],
			]
		],
		'eventlogging_WMDEBannerSizeIssue' => [
			'schema_title' => 'analytics/legacy/wmdebannersizeissue',
			'topic_prefixes' => null,
			'destination_event_service' => 'eventgate-analytics-external',
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					'job_name' => 'eventlogging_legacy',
					'enabled' => true,
				],
			]
		],
		/*
		 * === Streams for testing Event Platform-based instruments ===
		 */
		'test.instrumentation' => [
			'schema_title' => 'analytics/test',
			'destination_event_service' => 'eventgate-analytics-external',
		],
		// For verifying sampling:
		'test.instrumentation.sampled' => [
			'schema_title' => 'analytics/test',
			'destination_event_service' => 'eventgate-analytics-external',
			'sample' => [
				'rate' => 0.5,
				'unit' => 'session',
			],
		],
		/*
		 * === Streams for Event Platform-based analytics instruments ===
		 */
		'mediawiki.client.session_tick' => [
			'schema_title' => 'analytics/session_tick',
			'destination_event_service' => 'eventgate-analytics-external',
			'sample' => [
				'unit' => 'session',
				'rate' => 0.1,
			],
		],
		'app_session' => [
			'schema_title' => 'analytics/mobile_apps/app_session',
			'destination_event_service' => 'eventgate-analytics-external',
		],
		'app_donor_experience' => [
			'schema_title' => 'analytics/mobile_apps/app_interaction',
			'destination_event_service' => 'eventgate-analytics-external',
		],
		'app_patroller_experience' => [
			'schema_title' => 'analytics/mobile_apps/app_interaction',
			'destination_event_service' => 'eventgate-analytics-external',
		],
		'ios.edit_history_compare' => [
			'schema_title' => 'analytics/mobile_apps/ios_edit_history_compare',
			'destination_event_service' => 'eventgate-analytics-external',
		],
		'ios.notification_interaction' => [
			'schema_title' => 'analytics/mobile_apps/ios_notification_interaction',
			'destination_event_service' => 'eventgate-analytics-external',
		],
		'ios.talk_page_interaction' => [
			'schema_title' => 'analytics/mobile_apps/ios_talk_page_interaction',
			'destination_event_service' => 'eventgate-analytics-external',
		],
		'ios.setting_action' => [
			'schema_title' => 'analytics/mobile_apps/ios_setting_action',
			'destination_event_service' => 'eventgate-analytics-external',
		],
		'ios.sessions' => [
			'schema_title' => 'analytics/mobile_apps/ios_sessions',
			'destination_event_service' => 'eventgate-analytics-external',
		],
		'ios.login_action' => [
			'schema_title' => 'analytics/mobile_apps/ios_login_action',
			'destination_event_service' => 'eventgate-analytics-external',
		],
		'ios.search' => [
			'schema_title' => 'analytics/mobile_apps/ios_search',
			'destination_event_service' => 'eventgate-analytics-external',
		],
		'ios.user_history' => [
			'schema_title' => 'analytics/mobile_apps/ios_user_history',
			'destination_event_service' => 'eventgate-analytics-external',
		],
		'ios.reading_lists' => [
			'schema_title' => 'analytics/mobile_apps/ios_reading_lists',
			'destination_event_service' => 'eventgate-analytics-external',
		],
		'ios.navigation_events' => [
			'schema_title' => 'analytics/mobile_apps/ios_navigation_events',
			'destination_event_service' => 'eventgate-analytics-external',
		],
		'ios.watchlists' => [
			'schema_title' => 'analytics/mobile_apps/ios_watchlists',
			'destination_event_service' => 'eventgate-analytics-external',
		],
		'ios.suggested_edits_alt_text_prototype' => [
			'schema_title' => 'analytics/mobile_apps/app_interaction',
			'destination_event_service' => 'eventgate-analytics-external',
		],
		'android.user_contribution_screen' => [
			'schema_title' => 'analytics/mobile_apps/android_user_contribution_screen',
			'destination_event_service' => 'eventgate-analytics-external',
		],
		'android.notification_interaction' => [
			'schema_title' => 'analytics/mobile_apps/android_notification_interaction',
			'destination_event_service' => 'eventgate-analytics-external',
		],
		'android.image_recommendation_interaction' => [
			'schema_title' => 'analytics/mobile_apps/android_image_recommendation_interaction',
			'destination_event_service' => 'eventgate-analytics-external',
		],
		'android.daily_stats' => [
			'schema_title' => 'analytics/mobile_apps/android_daily_stats',
			'destination_event_service' => 'eventgate-analytics-external',
		],
		'android.customize_toolbar_interaction' => [
			'schema_title' => 'analytics/mobile_apps/android_customize_toolbar_interaction',
			'destination_event_service' => 'eventgate-analytics-external',
		],
		'android.article_toolbar_interaction' => [
			'schema_title' => 'analytics/mobile_apps/android_article_toolbar_interaction',
			'destination_event_service' => 'eventgate-analytics-external',
		],
		'android.edit_history_interaction' => [
			'schema_title' => 'analytics/mobile_apps/android_edit_history_interaction',
			'destination_event_service' => 'eventgate-analytics-external',
		],
		'android.app_session' => [
			'schema_title' => 'analytics/mobile_apps/android_app_session',
			'destination_event_service' => 'eventgate-analytics-external',
		],
		'android.create_account_interaction' => [
			'schema_title' => 'analytics/mobile_apps/android_create_account_interaction',
			'destination_event_service' => 'eventgate-analytics-external',
		],
		'android.install_referrer_event' => [
			'schema_title' => 'analytics/mobile_apps/android_install_referrer_event',
			'destination_event_service' => 'eventgate-analytics-external',
		],
		'android.breadcrumbs_event' => [
			'schema_title' => 'analytics/mobile_apps/android_breadcrumbs_event',
			'destination_event_service' => 'eventgate-analytics-external',
		],
		'android.app_appearance_settings_interaction' => [
			'schema_title' => 'analytics/mobile_apps/android_app_appearance_settings_interaction',
			'destination_event_service' => 'eventgate-analytics-external',
		],
		'android.article_link_preview_interaction' => [
			'schema_title' => 'analytics/mobile_apps/android_article_link_preview_interaction',
			'destination_event_service' => 'eventgate-analytics-external',
		],
		'android.article_page_scroll_interaction' => [
			'schema_title' => 'analytics/mobile_apps/android_article_page_scroll_interaction',
			'destination_event_service' => 'eventgate-analytics-external',
		],
		'android.article_toc_interaction' => [
			'schema_title' => 'analytics/mobile_apps/android_article_toc_interaction',
			'destination_event_service' => 'eventgate-analytics-external',
		],
		'android.find_in_page_interaction' => [
			'schema_title' => 'analytics/mobile_apps/android_find_in_page_interaction',
			'destination_event_service' => 'eventgate-analytics-external',
		],
		'android.image_recommendation_event' => [
			'schema_title' => 'analytics/mobile_apps/android_image_recommendation_event',
			'destination_event_service' => 'eventgate-analytics-external',
		],
		'android.reading_list_interaction' => [
			'schema_title' => 'analytics/mobile_apps/android_reading_list_interaction',
			'destination_event_service' => 'eventgate-analytics-external',
		],
		// See https://phabricator.wikimedia.org/T330355
		'android.metrics_platform.article_link_preview_interaction' => [
			'schema_title' => 'analytics/mediawiki/client/metrics_event',
			'destination_event_service' => 'eventgate-analytics-external',
			'producers' => [
				'metrics_platform_client' => [
					'events' => [ 'android.metrics_platform.article_link_preview_interaction' ],
					'provide_values' => [
						'agent_app_install_id',
						'agent_client_platform',
						'agent_client_platform_family'
					],
				],
			],
			'sample' => [
				'unit' => 'session',
				'rate' => 1,
			],
		],
		'android.metrics_platform.article_toc_interaction' => [
			'schema_title' => 'analytics/mediawiki/client/metrics_event',
			'destination_event_service' => 'eventgate-analytics-external',
			'producers' => [
				'metrics_platform_client' => [
					'events' => [ 'android.metrics_platform.article_toc_interaction' ],
					'provide_values' => [
						'agent_app_install_id',
						'agent_client_platform',
						'agent_client_platform_family'
					],
				],
			],
			'sample' => [
				'unit' => 'session',
				'rate' => 1,
			],
		],
		'android.metrics_platform.article_toolbar_interaction' => [
			'schema_title' => 'analytics/mediawiki/client/metrics_event',
			'destination_event_service' => 'eventgate-analytics-external',
			'producers' => [
				'metrics_platform_client' => [
					'events' => [ 'android.metrics_platform.article_toolbar_interaction' ],
					'provide_values' => [
						'agent_app_install_id',
						'agent_client_platform',
						'agent_client_platform_family'
					],
				],
			],
			'sample' => [
				'unit' => 'session',
				'rate' => 1,
			],
		],
		'android.metrics_platform.find_in_page_interaction' => [
			'schema_title' => 'analytics/mediawiki/client/metrics_event',
			'destination_event_service' => 'eventgate-analytics-external',
			'producers' => [
				'metrics_platform_client' => [
					'events' => [ 'android.metrics_platform.find_in_page_interaction' ],
					'provide_values' => [
						'agent_app_install_id',
						'agent_client_platform',
						'agent_client_platform_family'
					],
				],
			],
			'sample' => [
				'unit' => 'session',
				'rate' => 1,
			],
		],
		'mediawiki.mediasearch_interaction' => [
			'schema_title' => 'analytics/mediawiki/mediasearch_interaction',
			'destination_event_service' => 'eventgate-analytics-external',
		],
		'mediawiki.searchpreview' => [
			'schema_title' => 'analytics/mediawiki/searchpreview',
			'destination_event_service' => 'eventgate-analytics-external',
		],
		'mediawiki.structured_task.article.link_suggestion_interaction' => [
			'schema_title' => 'analytics/mediawiki/structured_task/article/link_suggestion_interaction',
			'destination_event_service' => 'eventgate-analytics-external',
		],
		'mediawiki.structured_task.article.image_suggestion_interaction' => [
			'schema_title' => 'analytics/mediawiki/structured_task/article/image_suggestion_interaction',
			'destination_event_service' => 'eventgate-analytics-external',
		],
		'mediawiki.pref_diff' => [
			'schema_title' => 'analytics/pref_diff',
			'destination_event_service' => 'eventgate-analytics-external',
		],
		'mediawiki.skin_diff' => [
			'schema_title' => 'analytics/pref_diff',
			'destination_event_service' => 'eventgate-analytics-external',
		],
		'mediawiki.content_translation_event' => [
			'schema_title' => 'analytics/mediawiki/content_translation_event',
			'destination_event_service' => 'eventgate-analytics-external',
		],
		'mediawiki.reading_depth' => [
			'schema_title' => 'analytics/mediawiki/web_ui_reading_depth',
			'destination_event_service' => 'eventgate-analytics-external',
		],
		'mediawiki.web_ab_test_enrollment' => [
			'schema_title' => 'analytics/mediawiki/web_ab_test_enrollment',
			'destination_event_service' => 'eventgate-analytics-external',
		],
		'mediawiki.web_ui_scroll' => [
			'schema_title' => 'analytics/mediawiki/web_ui_scroll',
			'destination_event_service' => 'eventgate-analytics-external',
		],
		'mediawiki.web_ui_scroll_migrated' => [
			'schema_title' => 'analytics/product_metrics/web/base',
			'destination_event_service' => 'eventgate-analytics-external',
			'producers' => [
				'metrics_platform_client' => [
					// The following is derived from:
					//
					// * https://github.com/wikimedia/mediawiki-extensions-WikimediaEvents/blob/master/modules/ext.wikimediaEvents/webUIScroll.js#L29-L31
					// * https://github.com/wikimedia/mediawiki-extensions-WikimediaEvents/blob/master/modules/ext.wikimediaEvents/webUIScroll.js#L33
					// * https://github.com/wikimedia/mediawiki-extensions-WikimediaEvents/blob/master/modules/ext.wikimediaEvents/webCommon.js#L33-L38
					'provide_values' => [
						'performer_is_bot',
						'mediawiki_database',
						'mediawiki_skin',
						'performer_session_id',
						'page_id',
						'performer_is_logged_in',
					],
				],
			],
			'sample' => [
				'unit' => 'pageview',
				'rate' => 1,
			],
		],
		'mediawiki.ipinfo_interaction' => [
			'schema_title' => 'analytics/mediawiki/ipinfo_interaction',
			'destination_event_service' => 'eventgate-analytics-external',
		],
		'wd_propertysuggester.client_side_property_request' => [
			'schema_title' => 'analytics/mediawiki/wd_propertysuggester/client_side_property_request',
			'destination_event_service' => 'eventgate-analytics-external',
		],
		'wd_propertysuggester.server_side_property_request' => [
			'schema_title' => 'analytics/mediawiki/wd_propertysuggester/server_side_property_request',
			'destination_event_service' => 'eventgate-analytics-external',
		],
		'mediawiki.mentor_dashboard.visit' => [
			'schema_title' => 'analytics/mediawiki/mentor_dashboard/visit',
			'destination_event_service' => 'eventgate-analytics-external',
		],
		'mediawiki.mentor_dashboard.personalized_praise' => [
			'schema_title' => 'analytics/mediawiki/mentor_dashboard/personalized_praise',
			'destination_event_service' => 'eventgate-analytics-external',
		],
		'mediawiki.mentor_dashboard.interaction' => [
			'schema_title' => 'analytics/mediawiki/mentor_dashboard/interaction',
			'destination_event_service' => 'eventgate-analytics-external',
		],
		'mediawiki.welcomesurvey.interaction' => [
			'schema_title' => 'analytics/mediawiki/welcomesurvey/interaction',
			'destination_event_service' => 'eventgate-analytics-external'
		],
		'mediawiki.editgrowthconfig' => [
			'schema_title' => 'analytics/mediawiki/editgrowthconfig',
			'destination_event_service' => 'eventgate-analytics-external',
		],
		'mediawiki.accountcreation_block' => [
			'schema_title' => 'analytics/mediawiki/accountcreation/block',
			'destination_event_service' => 'eventgate-analytics-external',
		],
		'mediawiki.editattempt_block' => [
			'schema_title' => 'analytics/mediawiki/editattemptsblocked',
			'destination_event_service' => 'eventgate-analytics-external',
		],
		[
			'stream' => 'mediawiki.talk_page_edit',
			'schema_title' => 'analytics/mediawiki/talk_page_edit',
			'destination_event_service' => 'eventgate-analytics-external',
		],
		'mwcli.command_execute' => [
			'schema_title' => 'analytics/mwcli/command_execute',
			'destination_event_service' => 'eventgate-analytics-external',
		],

		// See https://phabricator.wikimedia.org/T309013
		'mediawiki.edit_attempt' => [
			'schema_title' => 'analytics/mediawiki/client/metrics_event',
			'destination_event_service' => 'eventgate-analytics-external',
			'producers' => [
				'metrics_platform_client' => [
					'events' => [ 'eas.' ],

					// The following are the values common to all client-side EditAttemptStep
					// instruments that the Metrics Platform Clients can provide.
					// https://gerrit.wikimedia.org/g/mediawiki/extensions/WikimediaEvents/+/89e90362161d97ae55abc87db40236e7e3722fec/modules/ext.wikimediaEvents/editAttemptStep.js#260
					'provide_values' => [
						'agent_client_platform_family',
						'page_id',
						'page_title',
						'page_namespace',
						'page_revision_id',
						'mediawiki_version',
						'mediawiki_is_debug_mode',
						'mediawiki_database',
						'performer_is_logged_in',
						'performer_id',
						'performer_session_id',
						'performer_pageview_id',
						'performer_edit_count',
					],

					'curation' => [

						// Ignore events from the PHP Metrics Platform
						// Client until it has been tested on the Beta
						// Cluster.
						//
						// See https://phabricator.wikimedia.org/T309985
						'agent_client_platform' => [
							'not_equals' => 'mediawiki_php',
						],
					],
				],
			],
			'sample' => [
				'unit' => 'pageview',
				'rate' => 1,
			],
		],

		// See https://phabricator.wikimedia.org/T326212
		'mediawiki.special_diff_interactions' => [
			'schema_title' => 'analytics/mediawiki/client/metrics_event',
			'destination_event_service' => 'eventgate-analytics-external',
			'producers' => [
				'metrics_platform_client' => [
					'events' => [
						'specialDiff.click.', // T326214, T326216
					],
					'provide_values' => [
						'mediawiki_skin',
						'mediawiki_version',
						'mediawiki_is_production',
						'mediawiki_database',
						'performer_is_logged_in',
						'performer_groups',
						'performer_edit_count_bucket',
						'performer_registration_dt',
					],
				],
			],
			'sample' => [
				'unit' => 'pageview',
				'rate' => 1,
			],
		],

		// Wikistories streams
		'mediawiki.wikistories_consumption_event' => [
			'schema_title' => 'analytics/mediawiki/wikistories_consumption_event',
			'destination_event_service' => 'eventgate-analytics-external',
		],
		'mediawiki.wikistories_contribution_event' => [
			'schema_title' => 'analytics/mediawiki/wikistories_contribution_event',
			'destination_event_service' => 'eventgate-analytics-external',
		],

		// GeoIP mapping experiments (T332024)
		'development.network.probe' => [
			'schema_title' => 'development/network/probe',
			'destination_event_service' => 'eventgate-analytics-external',
		],

		/*
		 * == eventgate-logging-external streams ==
		 * These are produced to the Kafka logging clusters for ingestion into logstash.
		 * These streams disable canary event production.
		 * See: https://phabricator.wikimedia.org/T287789
		 * eventgate-logging-external only requests its stream configs on service startup,
		 * so if you modify something here, eventgate-logging-external will need a restart.
		 */
		'mediawiki.client.error' => [
			'schema_title' => 'mediawiki/client/error',
			'destination_event_service' => 'eventgate-logging-external',
			'canary_events_enabled' => false,
		],
		'kaios_app.error' => [
			'schema_title' => 'mediawiki/client/error',
			'destination_event_service' => 'eventgate-logging-external',
			'canary_events_enabled' => false,
		],
		'w3c.reportingapi.network_error' => [
			'schema_title' => 'w3c/reportingapi/network_error',
			'destination_event_service' => 'eventgate-logging-external',
			'canary_events_enabled' => false,
		],

		/*
		 * == eventgate-analytics streams ==
		 * These streams are produced to Kafka jumbo-eqiad from internal producers.
		 * eventgate-analytics only requests its stream configs on service startup,
		 * so if you modify something here, eventgate-analytics will need a restart.
		 */
		'api-gateway.request' => [
			'schema_title' => 'api-gateway/request',
			'destination_event_service' => 'eventgate-analytics',
		],
		'mediawiki.api-request' => [
			'schema_title' => 'mediawiki/api/request',
			'destination_event_service' => 'eventgate-analytics',
		],
		'mediawiki.cirrussearch-request' => [
			'schema_title' => 'mediawiki/cirrussearch/request',
			'destination_event_service' => 'eventgate-analytics',
		],
		'wdqs-internal.sparql-query' => [
			'schema_title' => 'sparql/query',
			'destination_event_service' => 'eventgate-analytics',
		],
		'wdqs-external.sparql-query' => [
			'schema_title' => 'sparql/query',
			'destination_event_service' => 'eventgate-analytics',
		],
		'wcqs-external.sparql-query' => [
			'schema_title' => 'sparql/query',
			'destination_event_service' => 'eventgate-analytics',
		],
		'/^swift\.(.+\.)?upload-complete$/' => [
			'schema_title' => 'swift/upload/complete',
			'destination_event_service' => 'eventgate-analytics',
			// canary events will not work for regex streams.
			'canary_events_enabled' => false,
		],

		/*
		 * == eventgate-main streams ==
		 * These streams are produced to Kafka main-eqiad and main-codfw.
		 * These streams disable canary event production.
		 * See: https://phabricator.wikimedia.org/T287789
		 * eventgate-main only requests its stream configs on service startup,
		 * so if you modify something here, eventgate-main will need a restart.
		 */
		'/^mediawiki\\.job\\..+/' => [
			'schema_title' => 'mediawiki/job',
			'destination_event_service' => 'eventgate-main',
			'canary_events_enabled' => false,
			'consumers' => [
				// Don't ingest mediawiki.job events into Hadoop.
				// They are not well schemead and not very useful
				'analytics_hadoop_ingestion' => [
					'enabled' => false,
				],
			],
		],
		'mediawiki.centralnotice.campaign-change' => [
			'schema_title' => 'mediawiki/centralnotice/campaign/change',
			'destination_event_service' => 'eventgate-main',
			'canary_events_enabled' => false,
		],
		'mediawiki.centralnotice.campaign-create' => [
			'schema_title' => 'mediawiki/centralnotice/campaign/create',
			'destination_event_service' => 'eventgate-main',
			'canary_events_enabled' => false,
		],
		'mediawiki.centralnotice.campaign-delete' => [
			'schema_title' => 'mediawiki/centralnotice/campaign/delete',
			'destination_event_service' => 'eventgate-main',
			'canary_events_enabled' => false,
		],
		'mediawiki.cirrussearch.page_rerender.v1' => [
			'schema_title' => 'mediawiki/cirrussearch/page_rerender',
			'destination_event_service' => 'eventgate-main',
			'message_key_fields' => [
				'wiki_id' => 'wiki_id',
				'page_id' => 'page_id',
			],
		],
		'mediawiki.page-create' => [
			'schema_title' => 'mediawiki/revision/create',
			'destination_event_service' => 'eventgate-main',
			'canary_events_enabled' => false,
		],
		'mediawiki.page-delete' => [
			'schema_title' => 'mediawiki/page/delete',
			'destination_event_service' => 'eventgate-main',
			'canary_events_enabled' => false,
		],
		'mediawiki.page-links-change' => [
			'schema_title' => 'mediawiki/page/links-change',
			'destination_event_service' => 'eventgate-main',
			'canary_events_enabled' => false,
		],
		'mediawiki.page-move' => [
			'schema_title' => 'mediawiki/page/move',
			'destination_event_service' => 'eventgate-main',
			'canary_events_enabled' => false,
		],
		'mediawiki.page-properties-change' => [
			'schema_title' => 'mediawiki/page/properties-change',
			'destination_event_service' => 'eventgate-main',
			'canary_events_enabled' => false,
		],
		'mediawiki.page-restrictions-change' => [
			'schema_title' => 'mediawiki/page/restrictions-change',
			'destination_event_service' => 'eventgate-main',
			'canary_events_enabled' => false,
		],
		'mediawiki.page-suppress' => [
			'schema_title' => 'mediawiki/page/delete',
			'destination_event_service' => 'eventgate-main',
			'canary_events_enabled' => false,
		],
		'mediawiki.page-undelete' => [
			'schema_title' => 'mediawiki/page/undelete',
			'destination_event_service' => 'eventgate-main',
			'canary_events_enabled' => false,
		],
		'mediawiki.recentchange' => [
			'schema_title' => 'mediawiki/recentchange',
			'destination_event_service' => 'eventgate-main',
			'canary_events_enabled' => false,
		],
		'mediawiki.revision-create' => [
			'schema_title' => 'mediawiki/revision/create',
			'destination_event_service' => 'eventgate-main',
			'canary_events_enabled' => false,
		],
		'mediawiki.revision-score' => [
			'schema_title' => 'mediawiki/revision/score',
			'destination_event_service' => 'eventgate-main',
			// This stream is derived and created by change-propagation,
			// and Hadoop is the main consumer.  Emit canary events.
			'canary_events_enabled' => true,
		],
		'mediawiki.revision-score-test' => [
			'schema_title' => 'mediawiki/revision/score',
			'destination_event_service' => 'eventgate-main',
			// This stream is a subset of the revision-score one,
			// and the events will be emitted by
			// the Lift Wing platform. More info in T301878.
			// Emit canary events.
			'canary_events_enabled' => true,
		],
		'mediawiki.revision_score_goodfaith' => [
			'schema_title' => 'mediawiki/revision/score',
			'destination_event_service' => 'eventgate-main',
			// This stream is a subset of the revision-score one,
			// and the events will be emitted by
			// the Lift Wing platform. More info in T317768.
			// Emit canary events.
			'canary_events_enabled' => true,
		],
		'mediawiki.revision_score_damaging' => [
			'schema_title' => 'mediawiki/revision/score',
			'destination_event_service' => 'eventgate-main',
			// This stream is a subset of the revision-score one,
			// and the events will be emitted by
			// the Lift Wing platform. More info in T317768.
			// Emit canary events.
			'canary_events_enabled' => true,
		],
		'mediawiki.revision_score_reverted' => [
			'schema_title' => 'mediawiki/revision/score',
			'destination_event_service' => 'eventgate-main',
			// This stream is a subset of the revision-score one,
			// and the events will be emitted by
			// the Lift Wing platform. More info in T317768.
			// Emit canary events.
			'canary_events_enabled' => true,
		],
		'mediawiki.revision_score_articlequality' => [
			'schema_title' => 'mediawiki/revision/score',
			'destination_event_service' => 'eventgate-main',
			// This stream is a subset of the revision-score one,
			// and the events will be emitted by
			// the Lift Wing platform. More info in T317768.
			// Emit canary events.
			'canary_events_enabled' => true,
		],
		'mediawiki.revision_score_draftquality' => [
			'schema_title' => 'mediawiki/revision/score',
			'destination_event_service' => 'eventgate-main',
			// This stream is a subset of the revision-score one,
			// and the events will be emitted by
			// the Lift Wing platform. More info in T317768.
			// Emit canary events.
			'canary_events_enabled' => true,
		],
		'mediawiki.revision_score_articletopic' => [
			'schema_title' => 'mediawiki/revision/score',
			'destination_event_service' => 'eventgate-main',
			// This stream is a subset of the revision-score one,
			// and the events will be emitted by
			// the Lift Wing platform. More info in T317768.
			// Emit canary events.
			'canary_events_enabled' => true,
		],
		'mediawiki.revision_score_drafttopic' => [
			'schema_title' => 'mediawiki/revision/score',
			'destination_event_service' => 'eventgate-main',
			// This stream is a subset of the revision-score one,
			// and the events will be emitted by
			// the Lift Wing platform. More info in T317768.
			// Emit canary events.
			'canary_events_enabled' => true,
		],
		'mediawiki.page_outlink_topic_prediction_change.v1' => [
			'schema_title' => 'mediawiki/page/prediction_classification_change',
			'destination_event_service' => 'eventgate-main',
			'canary_events_enabled' => true,
		],
		'mediawiki.revision-tags-change' => [
			'schema_title' => 'mediawiki/revision/tags-change',
			'destination_event_service' => 'eventgate-main',
			'canary_events_enabled' => false,
		],
		'mediawiki.revision-visibility-change' => [
			'schema_title' => 'mediawiki/revision/visibility-change',
			'destination_event_service' => 'eventgate-main',
			'canary_events_enabled' => false,
		],
		'mediawiki.user-blocks-change' => [
			'schema_title' => 'mediawiki/user/blocks-change',
			'destination_event_service' => 'eventgate-main',
			'canary_events_enabled' => false,
		],
		'resource_change' => [
			'schema_title' => 'resource_change',
			'destination_event_service' => 'eventgate-main',
			'canary_events_enabled' => false,
		],
		'resource-purge' => [
			'schema_title' => 'resource_change',
			'destination_event_service' => 'eventgate-main',
			'canary_events_enabled' => false,
		],
		'change-prop.transcludes.resource-change' => [
			'schema_title' => 'resource_change',
			'destination_event_service' => 'eventgate-main',
			'canary_events_enabled' => false,
		],
		'mediawiki.revision-recommendation-create' => [
			'schema_title' => 'mediawiki/revision/recommendation-create',
			'destination_event_service' => 'eventgate-main',
			// This stream is new enough that consumers are aware of canary events.
		],
		'mediawiki.image_suggestions_feedback' => [
			'schema_title' => 'mediawiki/page/image-suggestions-feedback',
			'destination_event_service' => 'eventgate-main',
		],
		'maps.tiles_change' => [
			'schema_title' => 'maps/tiles_change',
			'destination_event_service' => 'eventgate-main',
		],

		// mediawiki.page_change stream.
		// This stream is using major API versioning.
		// https://wikitech.wikimedia.org/wiki/Event_Platform/Stream_Configuration#Stream_versioning
		// https://phabricator.wikimedia.org/T311129
		'mediawiki.page_change.v1' => [
			'schema_title' => 'mediawiki/page/change',
			# When producing this stream to kafka, use a message key
			# like { wiki_id: X, page_id: Y }.  X and Y will be
			# obtained from the message value at wiki_id and page.page_id.
			# See also: https://phabricator.wikimedia.org/T318846
			'message_key_fields' => [
				'wiki_id' => 'wiki_id',
				'page_id' => 'page.page_id',
			],
			'destination_event_service' => 'eventgate-main',
		],
		// Declare version 1 of
		// mediawiki.page_content_change stream.
		// This stream uses the mediawiki/page/change schema
		// but includes content bodies in content slots.
		// It is produced by a streaming enrichment pipeline,
		// (not via MediaWiki EventBus).
		// https://wikitech.wikimedia.org/wiki/MediaWiki_Event_Enrichment
		'mediawiki.page_content_change.v1' => [
			'schema_title' => 'mediawiki/page/change',
			// Even though this stream will not be produced via EventGate,
			// we need to set an event service, so that the ProduceCanaryEvents
			// monitoring job can produce events through EventGate.
			// page_content_change is produced directly to Kafka jumbo-eqiad,
			// so we need to use an eventgate that also produces to jumbo-eqiad.
			// We use eventgate-analytics-external.
			'destination_event_service' => 'eventgate-analytics-external',
		],
		// This stream will be used by the streaming enrichment pipeline
		// to emit error events encountered during enrichment.
		// These events can be used if backfilling of the failed enrichment
		// is desired later.
		// This follows the naming convention of <job_name>.error
		'mw_page_content_change_enrich.error' => [
			'schema_title' => 'error',
			'canary_events_enabled' => false,
		],

		/*
		 * == WDQS Streaming Updater (Flink) output streams ==
		 * Note that Flink does not produce these through eventgate,
		 * it produces them directly to Kafka.
		 */
		'rdf-streaming-updater.lapsed-action' => [
			'schema_title' => 'rdf_streaming_updater/lapsed_action',
			'destination_event_service' => 'eventgate-main',
		],
		'rdf-streaming-updater.state-inconsistency' => [
			'schema_title' => 'rdf_streaming_updater/state_inconsistency',
			'destination_event_service' => 'eventgate-main',
		],
		'rdf-streaming-updater.fetch-failure' => [
			'schema_title' => 'rdf_streaming_updater/fetch_failure',
			'destination_event_service' => 'eventgate-main',
		],
		// reconciliation stream produced from a spark job via eventgate
		'rdf-streaming-updater.reconcile' => [
			'schema_title' => 'rdf_streaming_updater/reconcile',
			'destination_event_service' => 'eventgate-main',
		],
		/*
		 * == Search Update Pipeline (Flink) output streams ==
		 * Note that Flink does not produce these through eventgate,
		 * it produces them directly to Kafka.
		 */
		'cirrussearch.update_pipeline.update.rc0' => [
			'schema_title' => 'development/cirrussearch/update_pipeline/update',
			'destination_event_service' => 'eventgate-main',
			// TODO: re-enable canary events once the schema is stabilized
			'canary_events_enabled' => false,
		],
		'cirrussearch.update_pipeline.fetch_error.rc0' => [
			'schema_title' => 'development/cirrussearch/update_pipeline/fetch_error',
			'destination_event_service' => 'eventgate-main',
			// TODO: re-enable canary events once the schema is stabilized
			'canary_events_enabled' => false,
		],

		// Temporary analytics for Kartographer Nearby feature
		// See T315972
		'mediawiki.maps_interaction' => [
			'schema_title' => 'analytics/mediawiki/maps/interaction',
			'destination_event_service' => 'eventgate-analytics-external',
		],

		/*
		 * == Streams needed for eventgate functionality ==
		 * EventGate instances some streams defined for error logging and monitoring:
		 *
		 * - <event_service_name>.test.event
		 * 		Used for the k8s readinessProbe. An eventgate instance will not be considered
		 * 		ready until it accepts a POST of a test/event.
		 * 		See: https://gerrit.wikimedia.org/r/plugins/gitiles/operations/deployment-charts/+/refs/heads/ * master/charts/eventgate/templates/deployment.yaml#60
		 * 		test/event schema: https://schema.wikimedia.org/repositories//primary/jsonschema/test/event/latest
		 *
		 * - <event_service_name>.error.valiidation
		 * 		EventGate produce error events to their configured error_stream.
		 * 		They default to using streams named after their service/app name,
		 * 		e.g. 'eventgate-analytics-external.error.validation'
		 * 		See: https://gerrit.wikimedia.org/r/plugins/gitiles/operations/deployment-charts/+/refs/heads/ * master/charts/eventgate/templates/_config.yaml#51
		 *   	error schema: https://schema.wikimedia.org/repositories/primary/jsonschema/error/latest
		 */

		// eventgate test.event streams
		'eventgate-logging-external.test.event' => [
			'schema_title' => 'test/event',
			'destination_event_service' => 'eventgate-logging-external',
		],
		'eventgate-analytics-external.test.event' => [
			'schema_title' => 'test/event',
			'destination_event_service' => 'eventgate-analytics-external',
		],
		'eventgate-analytics.test.event' => [
			'schema_title' => 'test/event',
			'destination_event_service' => 'eventgate-analytics',
		],
		'eventgate-main.test.event' => [
			'schema_title' => 'test/event',
			'destination_event_service' => 'eventgate-main',
		],
		// eventgate error.validation streams
		'eventgate-logging-external.error.validation' => [
			'schema_title' => 'error',
			'destination_event_service' => 'eventgate-logging-external',
			'canary_events_enabled' => false,
		],
		'eventgate-analytics-external.error.validation' => [
			'schema_title' => 'error',
			'destination_event_service' => 'eventgate-analytics-external',
			'canary_events_enabled' => false,
		],
		'eventgate-analytics.error.validation' => [
			'schema_title' => 'error',
			'destination_event_service' => 'eventgate-analytics',
			'canary_events_enabled' => false,
		],
		'eventgate-main.error.validation' => [
			'schema_title' => 'error',
			'destination_event_service' => 'eventgate-main',
			'canary_events_enabled' => false,
		],

		'inuka.wiki_highlights_experiment' => [
			'schema_title' => 'analytics/external/wiki_highlights_experiment',
			'destination_event_service' => 'eventgate-analytics-external',
		],

		// (T336722) Wikifunctions-specific stream
		'wikifunctions.ui' => [
			'schema_title' => 'analytics/mediawiki/client/metrics_event',
			'destination_event_service' => 'eventgate-analytics-external',
			'producers' => [
				'metrics_platform_client' => [
					'events' => [
						'wf.ui.',
					],
					'provide_values' => [
						'agent_client_platform_family',
						'page_id',
						'page_title',
						'page_revision_id',
						'performer_is_logged_in',
						'performer_id',
						'performer_name',
						'performer_session_id',
						'performer_pageview_id',
						'performer_language',
						'performer_language_variant',
						'performer_edit_count',
						'performer_edit_count_bucket',
						'performer_groups',
						'performer_is_bot',
					],
				],
			],
			'sample' => [
				'unit' => 'pageview',
				'rate' => 1,
			],
		],

	],
],

];
