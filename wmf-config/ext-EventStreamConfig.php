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

		// topic prefixes are used to compute the list of Kafka topics that compose a stream.
		// If the 'topics' are not explicilty set for a stream, then the topics will be
		// computed as these prefixes + the stream name.
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

		'producers' => [
			'eventgate' => [
				// If all of the following are true:
				// - the event's schema has the field specified in the
				//   enrich_fields_from_http_headers setting key
				// - the field is not already set in the event
				// - A HTTP header name is specified in the
				//   enrich_fields_from_http_headers setting value
				//   (false can be used to explicitly disable)
				// - The HTTP request header specified
				//   is set in the HTTP POST request to eventgate
				// Then the specified field will be set to the value
				// of the HTTP header in the POSTed event before it is produced to Kafka.
				'enrich_fields_from_http_headers' => [
					// set meta.request_id to value of x-request-id header
					'meta.request_id' => 'x-request-id',
					// set http.request_headers['user-agent']
					// to value of user-agent header.
					// See:
					// - https://phabricator.wikimedia.org/T382173
					// - https://schema.wikimedia.org/repositories//primary/jsonschema/fragment/http/current.yaml
					// TODO: Disable default user-agent collection: https://phabricator.wikimedia.org/T384964
					'http.request_headers.user-agent' => 'user-agent'
				],
			],
		],

		'consumers' => [
			// All events should be imported into Hadoop HDFS by default.
			// The analytics hadoop ingestion consumer (as of 2025, Gobblin) will
			// look for streams to ingest using these settings.
			// Each stream in a job should have similar volumes to allow
			// the job to scale properly and not cause stream ingestion starvation.
			// The default job_name is event_default.
			// Override this if the stream should be imported by a different job,
			// or disabled altogether.
			// Gobblin jobs are configured in analytics/refinery and deployed by puppet (systemd).
			// https://gerrit.wikimedia.org/r/plugins/gitiles/analytics/refinery/+/refs/heads/master/gobblin/jobs/
			'analytics_hadoop_ingestion' => [
				'job_name' => 'event_default',
				'enabled' => true,
			],
			// This consumer ingests raw HDFS JSON data into Hive tables.
			// As of 2025 this consumer is a WMF custom Spark job named Refine.
			'analytics_hive_ingestion' => [
				'enabled' => true,
			],
		],
	],
	'+private' => [
		'producers' => [
			// MediaWiki Eventbus extension is responsible for producing events from MediaWiki.
			// In general, we don't to produce events from private wikis (e.g. officewiki, etc.)
			// by default.
			// This can be manually enabled in some special cases with some overrided settings.
			'mediawiki_eventbus' => [
				'enabled' => false,
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
 *
 * For overriding this in the beta cluster please see
 * wmf-config/InitialiseSettings-labs.php
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
		'eventlogging_EditAttemptStep' => [
			'schema_title' => 'analytics/legacy/editattemptstep',
			'topic_prefixes' => null,
			'destination_event_service' => 'eventgate-analytics-external',
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					'job_name' => 'eventlogging_legacy',
					'enabled' => true,
				],
				'analytics_hive_ingestion' => [
					'enabled' => true,
					'spark_job_ingestion_scale' => 'medium',
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
				'analytics_hive_ingestion' => [
					'enabled' => false,
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
				'analytics_hive_ingestion' => [
					'enabled' => false,
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
				'analytics_hive_ingestion' => [
					'enabled' => false,
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
		'eventlogging_MediaWikiPingback' => [
			'schema_title' => 'analytics/legacy/mediawikipingback',
			'topic_prefixes' => null,
			'destination_event_service' => 'eventgate-analytics-external',
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					'job_name' => 'eventlogging_legacy',
					'enabled' => true,
				],
				'analytics_hive_ingestion' => [
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
				'analytics_hive_ingestion' => [
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
				'analytics_hive_ingestion' => [
					'enabled' => true,
					'spark_job_ingestion_scale' => 'medium',
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
				'analytics_hive_ingestion' => [
					'enabled' => true,
					'spark_job_ingestion_scale' => 'medium',
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
				'analytics_hive_ingestion' => [
					'enabled' => true,
					'spark_job_ingestion_scale' => 'large',
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
			'consumers' => [
				'analytics_hive_ingestion' => [
					'enabled' => true,
					'spark_job_ingestion_scale' => 'large',
				],
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
		'app_places_interaction' => [
			'schema_title' => 'analytics/mobile_apps/app_interaction',
			'destination_event_service' => 'eventgate-analytics-external',
		],
		'app_tabs_interaction' => [
			'schema_title' => 'analytics/mobile_apps/app_interaction',
			'destination_event_service' => 'eventgate-analytics-external',
		],
		'app_rabbit_holes' => [
			'schema_title' => 'analytics/mobile_apps/app_interaction',
			'destination_event_service' => 'eventgate-analytics-external',
		],
		'app_game_interaction' => [
			'schema_title' => 'analytics/mobile_apps/app_interaction',
			'destination_event_service' => 'eventgate-analytics-external',
		],
		'app_activity_tab' => [
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
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					'job_name' => 'event_default',
					'enabled' => true,
				],
				'analytics_hive_ingestion' => [
					'enabled' => true,
					'spark_job_ingestion_scale' => 'medium',
				],
			],
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
		'ios.edit_interaction' => [
			'schema_title' => 'analytics/mobile_apps/app_interaction',
			'destination_event_service' => 'eventgate-analytics-external',
		],
		'ios.article_link_interaction' => [
			'schema_title' => 'analytics/mobile_apps/ios_article_link_interaction',
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
			'consumers' => [
				'analytics_hive_ingestion' => [
					'enabled' => true,
					'spark_job_ingestion_scale' => 'medium',
				],
			],
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
			'consumers' => [
				'analytics_hive_ingestion' => [
					'enabled' => true,
					'spark_job_ingestion_scale' => 'medium',
				],
			],
		],
		'android.app_appearance_settings_interaction' => [
			'schema_title' => 'analytics/mobile_apps/android_app_appearance_settings_interaction',
			'destination_event_service' => 'eventgate-analytics-external',
		],
		'android.article_link_preview_interaction' => [
			'schema_title' => 'analytics/mobile_apps/android_article_link_preview_interaction',
			'destination_event_service' => 'eventgate-analytics-external',
			'consumers' => [
				'analytics_hive_ingestion' => [
					'enabled' => true,
					'spark_job_ingestion_scale' => 'medium',
				],
			],
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
		'android.product_metrics.article_link_preview_interaction' => [
			'schema_title' => 'analytics/product_metrics/app/base',
			'destination_event_service' => 'eventgate-analytics-external',
			'producers' => [
				'metrics_platform_client' => [
					'provide_values' => [
						'mediawiki_database',
						'page_id',
						'page_title',
						'page_namespace_id',
						'page_wikidata_qid',
						'page_content_language',
						'performer_is_logged_in',
						'performer_session_id',
						'performer_pageview_id',
						'performer_language_groups',
						'performer_language_primary',
						'performer_groups',
					],
				],
			],
			'sample' => [
				'unit' => 'device',
				'rate' => 1,
			],
			'consumers' => [
				'analytics_hive_ingestion' => [
					'enabled' => true,
					'spark_job_ingestion_scale' => 'medium',
				],
			],
		],
		'android.product_metrics.article_toc_interaction' => [
			'schema_title' => 'analytics/mobile_apps/product_metrics/android_article_toc_interaction',
			'destination_event_service' => 'eventgate-analytics-external',
			'producers' => [
				'metrics_platform_client' => [
					'provide_values' => [
						'mediawiki_database',
						'page_id',
						'page_title',
						'page_namespace_id',
						'page_content_language',
						'performer_is_logged_in',
						'performer_session_id',
						'performer_pageview_id',
						'performer_language_groups',
						'performer_language_primary',
						'performer_groups',
					],
				],
			],
			'sample' => [
				'unit' => 'device',
				'rate' => 1,
			],
		],
		'android.product_metrics.article_toolbar_interaction' => [
			'schema_title' => 'analytics/product_metrics/app/base',
			'destination_event_service' => 'eventgate-analytics-external',
			'producers' => [
				'metrics_platform_client' => [
					'provide_values' => [
						'mediawiki_database',
						'page_id',
						'page_title',
						'page_namespace_id',
						'page_content_language',
						'performer_is_logged_in',
						'performer_session_id',
						'performer_pageview_id',
						'performer_language_groups',
						'performer_language_primary',
						'performer_groups',
					],
				],
			],
			'sample' => [
				'unit' => 'device',
				'rate' => 1,
			],
			'consumers' => [
				'analytics_hive_ingestion' => [
					'enabled' => true,
					'spark_job_ingestion_scale' => 'medium',
				],
			],
		],
		'android.product_metrics.find_in_page_interaction' => [
			'schema_title' => 'analytics/mobile_apps/product_metrics/android_find_in_page_interaction',
			'destination_event_service' => 'eventgate-analytics-external',
			'producers' => [
				'metrics_platform_client' => [
					'provide_values' => [
						'mediawiki_database',
						'page_id',
						'page_title',
						'page_namespace_id',
						'page_content_language',
						'performer_is_logged_in',
						'performer_session_id',
						'performer_pageview_id',
						'performer_language_groups',
						'performer_language_primary',
						'performer_groups',
					],
				],
			],
			'sample' => [
				'unit' => 'device',
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
		'mediawiki.web_ui_actions' => [
			'schema_title' => 'analytics/mediawiki/product_metrics/web_ui_actions',
			'destination_event_service' => 'eventgate-analytics-external',
			'producers' => [
				'metrics_platform_client' => [
					'provide_values' => [
						'page_namespace_id',
						'performer_is_logged_in',
						'performer_session_id',
						'performer_pageview_id',
						'performer_edit_count_bucket',
						'performer_groups',
						'performer_is_bot',
						'mediawiki_skin',
						'mediawiki_database',
					],
				],
			],
			'sample' => [
				'unit' => 'session',
				'rate' => 0.2,
			],
			'consumers' => [
				'analytics_hive_ingestion' => [
					'enabled' => true,
					'spark_job_ingestion_scale' => 'medium',
				],
			],
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
		],
		'mediawiki.ipinfo_interaction' => [
			'schema_title' => 'analytics/mediawiki/ipinfo_interaction',
			'destination_event_service' => 'eventgate-analytics-external',
		],
		'mediawiki.ip_reputation.score' => [
			'schema_title' => 'analytics/mediawiki/ip_reputation/score',
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
		'mediawiki.accountcreation.account_conversion' => [
			'schema_title' => 'analytics/mediawiki/accountcreation/account_conversion',
			'destination_event_service' => 'eventgate-analytics-external',
		],
		'mediawiki.accountcreation_block' => [
			'schema_title' => 'analytics/mediawiki/accountcreation/block',
			'destination_event_service' => 'eventgate-analytics-external',
		],
		'mediawiki.accountcreation.login' => [
			'schema_title' => 'analytics/mediawiki/accountcreation/account_conversion',
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

		// See https://phabricator.wikimedia.org/T353798
		'mediawiki.reference_previews' => [
			'schema_title' => 'analytics/mediawiki/client/metrics_event',
			'destination_event_service' => 'eventgate-analytics-external',
			'producers' => [
				'metrics_platform_client' => [
					'events' => [
						'ext.cite.baseline'
					],
					'provide_values' => [
						'mediawiki_database',
						'mediawiki_skin',
						'page_namespace',
						'performer_edit_count_bucket',
						'performer_is_logged_in',
					],
				],
			],
		],

		// See https://phabricator.wikimedia.org/T370045
		'wikibase.client.interaction' => [
			'schema_title' => 'analytics/product_metrics/web/base',
			'destination_event_service' => 'eventgate-analytics-external',
			'producers' => [
				'metrics_platform_client' => [
					'provide_values' => [
						'mediawiki_database',
						'mediawiki_skin',
						'performer_is_logged_in',
					],
				],
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
		 * We'd like to enable canary events for these, but the logging cluster consumers
		 * would need to know to filter out the canary events first.
		 * eventgate-logging-external only requests its stream configs on service startup,
		 * so if you modify something here, eventgate-logging-external will need a restart.
		 */
		'mediawiki.client.error' => [
			'schema_title' => 'mediawiki/client/error',
			'destination_event_service' => 'eventgate-logging-external',
			'canary_events_enabled' => false,
			'producers' => [
				'eventgate' => [
					// Override the default so we collect user-agent
					// as well as some IP geocoding info.
					'enrich_fields_from_http_headers' => [
						'meta.request_id' => 'x-request-id',
						'http.request_headers.user-agent' => 'user-agent',
						// For eventgate-logging-external's incoming requests,
						// the CDN layer performs a GeoIP lookup and attaches a
						// bunch of data as request headers.
						'http.request_headers.x-geoip-isp' => 'x-geoip-isp',
						'http.request_headers.x-geoip-organization' => 'x-geoip-organization',
						'http.request_headers.x-geoip-as-number' => 'x-geoip-as-number',
						'http.request_headers.x-geoip-country' => 'x-geoip-country',
						'http.request_headers.x-geoip-subdivision' => 'x-geoip-subdivision',
					],
				],
			],
			'consumers' => [
				'analytics_hive_ingestion' => [
					'enabled' => false,
				],
			],
		],
		'kaios_app.error' => [
			'schema_title' => 'mediawiki/client/error',
			'destination_event_service' => 'eventgate-logging-external',
			'canary_events_enabled' => false,
			'producers' => [
				'eventgate' => [
					// Override the default so we collect user-agent
					// as well as some IP geocoding info.
					'enrich_fields_from_http_headers' => [
						'meta.request_id' => 'x-request-id',
						'http.request_headers.user-agent' => 'user-agent',
						// For eventgate-logging-external's incoming requests,
						// the CDN layer performs a GeoIP lookup and attaches a
						// bunch of data as request headers.
						'http.request_headers.x-geoip-isp' => 'x-geoip-isp',
						'http.request_headers.x-geoip-organization' => 'x-geoip-organization',
						'http.request_headers.x-geoip-as-number' => 'x-geoip-as-number',
						'http.request_headers.x-geoip-country' => 'x-geoip-country',
						'http.request_headers.x-geoip-subdivision' => 'x-geoip-subdivision',
					],
				],
			],
			'consumers' => [
				'analytics_hive_ingestion' => [
					'enabled' => false,
				],
			],
		],
		'w3c.reportingapi.network_error' => [
			'schema_title' => 'w3c/reportingapi/network_error',
			'destination_event_service' => 'eventgate-logging-external',
			'canary_events_enabled' => false,
			'producers' => [
				'eventgate' => [
					// Override the default so we collect user-agent
					// as well as some IP geocoding info.
					'enrich_fields_from_http_headers' => [
						'meta.request_id' => 'x-request-id',
						'http.request_headers.user-agent' => 'user-agent',
						// For eventgate-logging-external's incoming requests,
						// the CDN layer performs a GeoIP lookup and attaches a
						// bunch of data as request headers.
						'http.request_headers.x-geoip-isp' => 'x-geoip-isp',
						'http.request_headers.x-geoip-organization' => 'x-geoip-organization',
						'http.request_headers.x-geoip-as-number' => 'x-geoip-as-number',
						'http.request_headers.x-geoip-country' => 'x-geoip-country',
						'http.request_headers.x-geoip-subdivision' => 'x-geoip-subdivision',
					],
				],
			],
			'consumers' => [
				'analytics_hive_ingestion' => [
					'enabled' => false,
				],
			],
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
			'consumers' => [
				'analytics_hive_ingestion' => [
					'enabled' => true,
					'spark_job_ingestion_scale' => 'large',
				],
			],
		],
		'mediawiki.cirrussearch-request' => [
			'schema_title' => 'mediawiki/cirrussearch/request',
			'destination_event_service' => 'eventgate-analytics',
			'consumers' => [
				'analytics_hive_ingestion' => [
					'enabled' => true,
					'spark_job_ingestion_scale' => 'large',
				],
			],
		],
		'wdqs-internal.sparql-query' => [
			'schema_title' => 'sparql/query',
			'destination_event_service' => 'eventgate-analytics',
			'consumers' => [
				'analytics_hive_ingestion' => [
					'enabled' => true,
					'spark_job_ingestion_scale' => 'medium',
				],
			],
		],
		'wdqs-external.sparql-query' => [
			'schema_title' => 'sparql/query',
			'destination_event_service' => 'eventgate-analytics',
			'consumers' => [
				'analytics_hive_ingestion' => [
					'enabled' => true,
					'spark_job_ingestion_scale' => 'medium',
				],
			],
		],
		'wcqs-external.sparql-query' => [
			'schema_title' => 'sparql/query',
			'destination_event_service' => 'eventgate-analytics',
			'consumers' => [
				'analytics_hive_ingestion' => [
					'enabled' => true,
					'spark_job_ingestion_scale' => 'medium',
				],
			],
		],
		'/^swift\.(.+\.)?upload-complete$/' => [
			'schema_title' => 'swift/upload/complete',
			'destination_event_service' => 'eventgate-analytics',
			// canary events will not work for regex streams.
			'canary_events_enabled' => false,
			'consumers' => [
				// Don't ingest regex streams into Hadoop.
				'analytics_hadoop_ingestion' => [
					'enabled' => false,
				],
				'analytics_hive_ingestion' => [
					'enabled' => false,
				],
			],
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
				'analytics_hive_ingestion' => [
					'enabled' => false,
				],
			],
		],
		'mediawiki.centralnotice.campaign-change' => [
			'schema_title' => 'mediawiki/centralnotice/campaign/change',
			'destination_event_service' => 'eventgate-main',
		],
		'mediawiki.centralnotice.campaign-create' => [
			'schema_title' => 'mediawiki/centralnotice/campaign/create',
			'destination_event_service' => 'eventgate-main',
		],
		'mediawiki.centralnotice.campaign-delete' => [
			'schema_title' => 'mediawiki/centralnotice/campaign/delete',
			'destination_event_service' => 'eventgate-main',
		],
		'mediawiki.cirrussearch.page_rerender.v1' => [
			'schema_title' => 'mediawiki/cirrussearch/page_rerender',
			'destination_event_service' => 'eventgate-main',
			'message_key_fields' => [
				'wiki_id' => 'wiki_id',
				'page_id' => 'page_id',
			],
			'consumers' => [
				'analytics_hive_ingestion' => [
					'enabled' => true,
					'spark_job_ingestion_scale' => 'medium',
				],
			],
		],
		// mediawiki.cirrussearch.page_rerender stream for private wikis
		// https://phabricator.wikimedia.org/T346046
		'mediawiki.cirrussearch.page_rerender.private.v1' => [
			'schema_title' => 'mediawiki/cirrussearch/page_rerender',
			'destination_event_service' => 'eventgate-main',
			'message_key_fields' => [
				'wiki_id' => 'wiki_id',
				'page_id' => 'page_id',
			],
			'producers' => [
				'mediawiki_eventbus' => [
					'enabled' => false,
				],
			],
		],
		'mediawiki.page-create' => [
			'schema_title' => 'mediawiki/revision/create',
			'destination_event_service' => 'eventgate-main',
		],
		'mediawiki.page-delete' => [
			'schema_title' => 'mediawiki/page/delete',
			'destination_event_service' => 'eventgate-main',
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					'job_name' => 'event_default',
					'enabled' => true,
				],
				'analytics_hive_ingestion' => [
					'enabled' => true,
				],
			],
		],
		'mediawiki.page-links-change' => [
			'schema_title' => 'mediawiki/page/links-change',
			'destination_event_service' => 'eventgate-main',
		],
		'mediawiki.page-move' => [
			'schema_title' => 'mediawiki/page/move',
			'destination_event_service' => 'eventgate-main',
		],
		'mediawiki.page-properties-change' => [
			'schema_title' => 'mediawiki/page/properties-change',
			'destination_event_service' => 'eventgate-main',
			'consumers' => [
				'analytics_hive_ingestion' => [
					'enabled' => false,
				],
			],
		],
		'mediawiki.page-restrictions-change' => [
			'schema_title' => 'mediawiki/page/restrictions-change',
			'destination_event_service' => 'eventgate-main',
		],
		'mediawiki.page-suppress' => [
			'schema_title' => 'mediawiki/page/delete',
			'destination_event_service' => 'eventgate-main',
		],
		'mediawiki.page-undelete' => [
			'schema_title' => 'mediawiki/page/undelete',
			'destination_event_service' => 'eventgate-main',
		],
		'mediawiki.recentchange' => [
			'schema_title' => 'mediawiki/recentchange',
			'destination_event_service' => 'eventgate-main',
			'consumers' => [
				'analytics_hive_ingestion' => [
					'enabled' => false,
				],
			],
		],
		'mediawiki.revision-create' => [
			'schema_title' => 'mediawiki/revision/create',
			'destination_event_service' => 'eventgate-main',
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					'job_name' => 'event_default',
					'enabled' => true,
				],
				'analytics_hive_ingestion' => [
					'enabled' => true,
					'spark_job_ingestion_scale' => 'medium',
				],
			],
		],
		'mediawiki.revision-score' => [
			'schema_title' => 'mediawiki/revision/score',
			'destination_event_service' => 'eventgate-main',
		],
		'mediawiki.revision-score-test' => [
			'schema_title' => 'mediawiki/revision/score',
			'destination_event_service' => 'eventgate-main',
			// This stream is a subset of the revision-score one,
			// and the events will be emitted by
			// the Lift Wing platform. More info in T301878.
		],
		'mediawiki.revision_score_goodfaith' => [
			'schema_title' => 'mediawiki/revision/score',
			'destination_event_service' => 'eventgate-main',
			// This stream is a subset of the revision-score one,
			// and the events will be emitted by
			// the Lift Wing platform. More info in T317768.
		],
		'mediawiki.revision_score_damaging' => [
			'schema_title' => 'mediawiki/revision/score',
			'destination_event_service' => 'eventgate-main',
			// This stream is a subset of the revision-score one,
			// and the events will be emitted by
			// the Lift Wing platform. More info in T317768.
		],
		'mediawiki.revision_score_reverted' => [
			'schema_title' => 'mediawiki/revision/score',
			'destination_event_service' => 'eventgate-main',
			// This stream is a subset of the revision-score one,
			// and the events will be emitted by
			// the Lift Wing platform. More info in T317768.
		],
		'mediawiki.revision_score_articlequality' => [
			'schema_title' => 'mediawiki/revision/score',
			'destination_event_service' => 'eventgate-main',
			// This stream is a subset of the revision-score one,
			// and the events will be emitted by
			// the Lift Wing platform. More info in T317768.
		],
		'mediawiki.revision_score_draftquality' => [
			'schema_title' => 'mediawiki/revision/score',
			'destination_event_service' => 'eventgate-main',
			// This stream is a subset of the revision-score one,
			// and the events will be emitted by
			// the Lift Wing platform. More info in T317768.
		],
		'mediawiki.revision_score_articletopic' => [
			'schema_title' => 'mediawiki/revision/score',
			'destination_event_service' => 'eventgate-main',
			// This stream is a subset of the revision-score one,
			// and the events will be emitted by
			// the Lift Wing platform. More info in T317768.
		],
		'mediawiki.revision_score_drafttopic' => [
			'schema_title' => 'mediawiki/revision/score',
			'destination_event_service' => 'eventgate-main',
			// This stream is a subset of the revision-score one,
			// and the events will be emitted by
			// the Lift Wing platform. More info in T317768.
		],
		'mediawiki.page_prediction_change.rc0' => [
			'schema_title' => 'mediawiki/page/prediction_classification_change',
			'destination_event_service' => 'eventgate-main',
			'canary_events_enabled' => false,
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					'enabled' => false,
				],
				'analytics_hive_ingestion' => [
					'enabled' => false,
				],
			],
			// This stream is a testing stream for page-prediction-change events,
			// and the events will be emitted by the Lift Wing platform.
			// More info in T349919.
		],
		'mediawiki.article_country_prediction_change.v1' => [
			'schema_title' => 'mediawiki/page/prediction_classification_change',
			'destination_event_service' => 'eventgate-main',
		],
		'mediawiki.page_outlink_topic_prediction_change.v1' => [
			'schema_title' => 'mediawiki/page/prediction_classification_change',
			'destination_event_service' => 'eventgate-main',
			// The discussions in T382295#10518032 and T366273#10492412 suggest
			// renaming this stream to replace 'page' with 'article'.
		],
		'mediawiki.page_revert_risk_prediction_change.v1' => [
			'schema_title' => 'mediawiki/page/prediction_classification_change',
			'destination_event_service' => 'eventgate-main',
		],
		'mediawiki.revision-tags-change' => [
			'schema_title' => 'mediawiki/revision/tags-change',
			'destination_event_service' => 'eventgate-main',
		],
		'mediawiki.revision-visibility-change' => [
			'schema_title' => 'mediawiki/revision/visibility-change',
			'destination_event_service' => 'eventgate-main',
		],
		'mediawiki.user-blocks-change' => [
			'schema_title' => 'mediawiki/user/blocks-change',
			'destination_event_service' => 'eventgate-main',
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					'job_name' => 'event_default',
					'enabled' => true,
				],
				'analytics_hive_ingestion' => [
					'enabled' => true,
				],
			],
		],
		'resource_change' => [
			'schema_title' => 'resource_change',
			'destination_event_service' => 'eventgate-main',
			'canary_events_enabled' => false,
			'consumers' => [
				'analytics_hive_ingestion' => [
					'enabled' => true,
					'spark_job_ingestion_scale' => 'medium',
				],
			],
		],
		'resource-purge' => [
			'schema_title' => 'resource_change',
			'destination_event_service' => 'eventgate-main',
			'canary_events_enabled' => false,
			'consumers' => [
				'analytics_hive_ingestion' => [
					'enabled' => false,
				],
			],
		],
		'change-prop.transcludes.resource-change' => [
			'schema_title' => 'resource_change',
			'destination_event_service' => 'eventgate-main',
			'canary_events_enabled' => false,
			'consumers' => [
				'analytics_hive_ingestion' => [
					'enabled' => false,
				],
			],
		],
		'mediawiki.revision-recommendation-create' => [
			'schema_title' => 'mediawiki/revision/recommendation-create',
			'destination_event_service' => 'eventgate-main',
		],
		'mediawiki.image_suggestions_feedback' => [
			'schema_title' => 'mediawiki/page/image-suggestions-feedback',
			'destination_event_service' => 'eventgate-main',
		],
		'maps.tiles_change' => [
			'schema_title' => 'maps/tiles_change',
			'destination_event_service' => 'eventgate-main',
		],
		// We add the OS suffix to the stream name because we need
		// to identify what platform the maps backends are running on.
		// Every time an OS upgrade happens the tegola tiles cache
		// needs to be re-created, from a brand new Postgres cluster
		// (with a different version etc..).
		'maps.tiles_change_bookworm.v1' => [
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
			'consumers' => [
				'analytics_hive_ingestion' => [
					'enabled' => true,
					'spark_job_ingestion_scale' => 'medium',
				],
			],
		],
		// mediawiki.page_change stream for private wikis
		// https://phabricator.wikimedia.org/T346046
		'mediawiki.page_change.private.v1' => [
			'schema_title' => 'mediawiki/page/change',
			'message_key_fields' => [
				'wiki_id' => 'wiki_id',
				'page_id' => 'page.page_id',
			],
			'destination_event_service' => 'eventgate-main',
			'producers' => [
				'mediawiki_eventbus' => [
					'enabled' => false,
				],
			],
		],

		// Staging version of the mediawiki.page_change stream.
		// This stream is used for validation of https://phabricator.wikimedia.org/T391254
		'mediawiki.page_change.staging.v1' => [
			'schema_title' => 'mediawiki/page/change',
			# When producing this stream to kafka, use a message key
			# like { wiki_id: X, page_id: Y }.  X and Y will be
			# obtained from the message value at wiki_id and page.page_id.
			# See also: https://phabricator.wikimedia.org/T318846
			'message_key_fields' => [
				'wiki_id' => 'wiki_id',
				'page_id' => 'page.page_id',
			],
			'destination_event_service' => 'eventgate-analytics',
			'consumers' => [
				'analytics_hive_ingestion' => [
					'enabled' => true,
					'spark_job_ingestion_scale' => 'medium',
				],
			],
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
			// https://phabricator.wikimedia.org/T338231
			'message_key_fields' => [
				'wiki_id' => 'wiki_id',
				'page_id' => 'page.page_id',
			],
			// Even though this stream will not be produced via EventGate,
			// we need to set an event service, so that the ProduceCanaryEvents
			// monitoring job can produce events through EventGate.
			// page_content_change is produced directly to Kafka jumbo-eqiad,
			// so we need to use an eventgate that also produces to jumbo-eqiad.
			// We use eventgate-analytics-external.
			'destination_event_service' => 'eventgate-analytics-external',
			'consumers' => [
				'analytics_hive_ingestion' => [
					'enabled' => true,
					'spark_job_ingestion_scale' => 'large',
				],
			],
		],

		// This stream will be used by the streaming enrichment pipeline
		// These events can be used if backfilling of the failed enrichment
		// is desired later.
		// This follows the naming convention of <job_name>.error
		'mw_page_content_change_enrich.error' => [
			'schema_title' => 'error',
			'canary_events_enabled' => false,
		],

		// This stream uses the mediawiki/page/change schema.
		// It is produced by a PySpark job (T368755) that checks for
		// inconsistencies on a datalake table that consumes stream
		// 'mediawiki.page_content_change.v1'.
		// It is meant to be enriched by a dowstrean Flink app.
		// TODO: remove once mediawiki.content_history.reconcile.v1 streams
		// 	are released.
		'mediawiki.dump.revision_content_history.reconcile.rc0' => [
			'schema_title' => 'mediawiki/page/change',
			// https://phabricator.wikimedia.org/T338231
			'message_key_fields' => [
				'wiki_id' => 'wiki_id',
				'page_id' => 'page.page_id',
			],
			'destination_event_service' => 'eventgate-analytics',
			'canary_events_enabled' => false,
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					// we don't need these non-enriched events in Hadoop
					'enabled' => false,
				],
				'analytics_hive_ingestion' => [
					'enabled' => false,
				],
			],
		],
		// Enriched stream of the above produced by a Flink app (T368787)
		'mediawiki.dump.revision_content_history.reconcile.enriched.rc0' => [
			'schema_title' => 'mediawiki/page/change',
			// https://phabricator.wikimedia.org/T338231
			'message_key_fields' => [
				'wiki_id' => 'wiki_id',
				'page_id' => 'page.page_id',
			],
			'destination_event_service' => 'eventgate-analytics',
		],
		// This stream will be used by the above streaming enrichment pipeline
		// This follows the naming convention of <job_name>.error
		'mw_dump_rev_content_reconcile_enrich.error' => [
			'schema_title' => 'error',
			'canary_events_enabled' => false,
			'consumers' => [
				'analytics_hive_ingestion' => [
					'enabled' => false,
				],
			],
		],

		// This stream uses the mediawiki/page/change schema.
		// It is produced by a PySpark job (T368755) that checks for
		// inconsistencies on a datalake table that consumes stream
		// 'mediawiki.page_content_change.v1'.
		// It is meant to be enriched by a dowstrean Flink app.
		'mediawiki.content_history_reconcile.v1' => [
			'schema_title' => 'mediawiki/page/change',
			// https://phabricator.wikimedia.org/T338231
			'message_key_fields' => [
				'wiki_id' => 'wiki_id',
				'page_id' => 'page.page_id',
			],
			'destination_event_service' => 'eventgate-analytics',
			'canary_events_enabled' => false,
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					// we don't need these non-enriched events in Hadoop
					'enabled' => false,
				],
				'analytics_hive_ingestion' => [
					'enabled' => false,
				],
			],
		],
		// Enriched stream of the above produced by a Flink app (T368787)
		'mediawiki.content_history_reconcile_enriched.v1' => [
			'schema_title' => 'mediawiki/page/change',
			// https://phabricator.wikimedia.org/T338231
			'message_key_fields' => [
				'wiki_id' => 'wiki_id',
				'page_id' => 'page.page_id',
			],
			'destination_event_service' => 'eventgate-analytics',
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					'job_name' => 'event_default',
					'enabled' => true,
				],
				'analytics_hive_ingestion' => [
					'enabled' => true,
					'spark_job_ingestion_scale' => 'large',
				],
			],
		],
		// This stream will be used by the above streaming enrichment pipeline
		// This follows the naming convention of <job_name>.error
		'mw_content_history_reconcile_enrich.error' => [
			'schema_title' => 'error',
			'canary_events_enabled' => false,
			'consumers' => [
				'analytics_hive_ingestion' => [
					'enabled' => false,
				],
			],
		],

		/*
		 * == WDQS Streaming Updater (Flink) output streams ==
		 * Note that Flink does not produce these through eventgate,
		 * it produces them directly to Kafka.
		 *
		 * Do not enable canary events on mutation streams yet, we are not sure of
		 * the consequences on these topics that are populated by a "transactional"
		 * producer.
		 */
		'rdf-streaming-updater.mutation.v2' => [
			'schema_title' => 'mediawiki/wikibase/entity/rdf_change',
			'destination_event_service' => 'eventgate-main',
			'canary_events_enabled' => false,
			'topics' => [
				'eqiad.rdf-streaming-updater.mutation',
				'codfw.rdf-streaming-updater.mutation'
			],
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					'enabled' => true,
				],
				'analytics_hive_ingestion' => [
					'enabled' => true,
				],
			],
		],
		'rdf-streaming-updater.mutation-main.v2' => [
			'schema_title' => 'mediawiki/wikibase/entity/rdf_change',
			'destination_event_service' => 'eventgate-main',
			'canary_events_enabled' => false,
			'topics' => [
				'eqiad.rdf-streaming-updater.mutation-main',
				'codfw.rdf-streaming-updater.mutation-main'
			],
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					'enabled' => true,
				],
				'analytics_hive_ingestion' => [
					'enabled' => true,
				],
			],
		],
		'rdf-streaming-updater.mutation-scholarly.v2' => [
			'schema_title' => 'mediawiki/wikibase/entity/rdf_change',
			'destination_event_service' => 'eventgate-main',
			'canary_events_enabled' => false,
			'topics' => [
				'eqiad.rdf-streaming-updater.mutation-scholarly',
				'codfw.rdf-streaming-updater.mutation-scholarly'
			],
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					'enabled' => true,
				],
				'analytics_hive_ingestion' => [
					'enabled' => true,
				],
			],
		],
		'rdf-streaming-updater.mutation-staging.v2' => [
			'schema_title' => 'mediawiki/wikibase/entity/rdf_change',
			'destination_event_service' => 'eventgate-main',
			'canary_events_enabled' => false,
			'topics' => [
				'eqiad.rdf-streaming-updater.mutation-staging',
				'codfw.rdf-streaming-updater.mutation-staging'
			],
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					'enabled' => false,
				],
				'analytics_hive_ingestion' => [
					'enabled' => false,
				],
			],
		],
		'rdf-streaming-updater.mutation-main-staging.v2' => [
			'schema_title' => 'mediawiki/wikibase/entity/rdf_change',
			'destination_event_service' => 'eventgate-main',
			'canary_events_enabled' => false,
			'topics' => [
				'eqiad.rdf-streaming-updater.mutation-main-staging',
				'codfw.rdf-streaming-updater.mutation-main-staging'
			],
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					'enabled' => false,
				],
				'analytics_hive_ingestion' => [
					'enabled' => false,
				],
			],
		],
		'rdf-streaming-updater.mutation-scholarly-staging.v2' => [
			'schema_title' => 'mediawiki/wikibase/entity/rdf_change',
			'destination_event_service' => 'eventgate-main',
			'canary_events_enabled' => false,
			'topics' => [
				'eqiad.rdf-streaming-updater.mutation-scholarly-staging',
				'codfw.rdf-streaming-updater.mutation-scholarly-staging'
			],
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					'enabled' => false,
				],
				'analytics_hive_ingestion' => [
					'enabled' => false,
				],
			],
		],
		'mediainfo-streaming-updater.mutation.v2' => [
			'schema_title' => 'mediawiki/wikibase/entity/rdf_change',
			'destination_event_service' => 'eventgate-main',
			'canary_events_enabled' => false,
			'topics' => [
				'eqiad.mediainfo-streaming-updater.mutation',
				'codfw.mediainfo-streaming-updater.mutation'
			],
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					'enabled' => true,
				],
				'analytics_hive_ingestion' => [
					'enabled' => true,
				],
			],
		],
		'mediainfo-streaming-updater.mutation-staging.v2' => [
			'schema_title' => 'mediawiki/wikibase/entity/rdf_change',
			'destination_event_service' => 'eventgate-main',
			'canary_events_enabled' => false,
			'topics' => [
				'eqiad.mediainfo-streaming-updater.mutation-staging',
				'codfw.mediainfo-streaming-updater.mutation-staging'
			],
			'consumers' => [
				'analytics_hadoop_ingestion' => [
					'enabled' => false,
				],
				'analytics_hive_ingestion' => [
					'enabled' => false,
				],
			],
		],
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
		 * == Search Update Pipeline (Flink) streams ==
		 * Note that Flink does not produce these through eventgate,
		 * it produces them directly to Kafka.
		 */
		'mediawiki.cirrussearch.page_weighted_tags_change.v1' => [
			'schema_title' => 'mediawiki/cirrussearch/page_weighted_tags_change',
			'destination_event_service' => 'eventgate-main',
			'message_key_fields' => [
				'wiki_id' => 'wiki_id',
				'page_id' => 'page.page_id',
			],
		],
		'cirrussearch.update_pipeline.update.v1' => [
			'schema_title' => 'mediawiki/cirrussearch/update_pipeline/update',
			'destination_event_service' => 'eventgate-main',
			'message_key_fields' => [
				'wiki_id' => 'wiki_id',
				'page_id' => 'page_id',
			],
			'canary_events_enabled' => true,
			'consumers' => [
				'analytics_hive_ingestion' => [
					'enabled' => true,
					'spark_job_ingestion_scale' => 'medium',
				],
			],
		],
		'cirrussearch.update_pipeline.update.private.v1' => [
			'schema_title' => 'mediawiki/cirrussearch/update_pipeline/update',
			'destination_event_service' => 'eventgate-main',
			'message_key_fields' => [
				'wiki_id' => 'wiki_id',
				'page_id' => 'page_id',
			],
			'canary_events_enabled' => true,
		],
		'cirrussearch.update_pipeline.fetch_error.v1' => [
			'schema_title' => 'mediawiki/cirrussearch/update_pipeline/fetch_error',
			'destination_event_service' => 'eventgate-main',
			'canary_events_enabled' => true,
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
			'consumers' => [
				'analytics_hive_ingestion' => [
					'enabled' => false,
				],
			],
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
			'consumers' => [
				'analytics_hive_ingestion' => [
					'enabled' => false,
				],
			],
		],
		'eventgate-analytics-external.error.validation' => [
			'schema_title' => 'error',
			'destination_event_service' => 'eventgate-analytics-external',
			'canary_events_enabled' => true,
		],
		'eventgate-analytics.error.validation' => [
			'schema_title' => 'error',
			'destination_event_service' => 'eventgate-analytics',
			'canary_events_enabled' => false,
			'consumers' => [
				'analytics_hive_ingestion' => [
					'enabled' => false,
				],
			],
		],
		'eventgate-main.error.validation' => [
			'schema_title' => 'error',
			'destination_event_service' => 'eventgate-main',
			'canary_events_enabled' => true,
		],

		'inuka.wiki_highlights_experiment' => [
			'schema_title' => 'analytics/external/wiki_highlights_experiment',
			'destination_event_service' => 'eventgate-analytics-external',
		],
		// Instrument for IP auto-reveal (T387600)
		'mediawiki.product_metrics.checkuser_ip_auto_reveal_interaction' => [
			'schema_title' => 'analytics/product_metrics/web/base',
			'destination_event_service' => 'eventgate-analytics-external',
			'eventgate' => [
				'enrich_fields_from_http_headers' => [
					'http.request_headers.user-agent' => false,
				],
			],
			'producers' => [
				'metrics_platform_client' => [
					'provide_values' => [
						'performer_id',
						'performer_name',
						'performer_active_browsing_session_token',
						'performer_session_id',
						'agent_client_platform_family',
					],
				],
			],
		],
		// Instrument for the Incident Reporting System (T372823)
		'mediawiki.product_metrics.incident_reporting_system_interaction' => [
			'schema_title' => 'analytics/product_metrics/web/base',
			'destination_event_service' => 'eventgate-analytics-external',
			'producers' => [
				'mediawiki_eventbus' => [
					'event_service_name' => 'eventgate-analytics-external',
				],
				'metrics_platform_client' => [
					'provide_values' => [
						'page_id',
						'page_title',
						'page_namespace_id',
						'performer_language',
						'performer_language_variant',
						'performer_session_id',
						'performer_active_browsing_session_token',
						'performer_name',
					],
				],
			],
		],
		// Instrument for Special:CreateAccount (T394744)
		'mediawiki.product_metrics.special_create_account' => [
			'schema_title' => 'analytics/product_metrics/web/base',
			'destination_event_service' => 'eventgate-analytics-external',
			'producers' => [
				'mediawiki_eventbus' => [
					'event_service_name' => 'eventgate-analytics-external',
				],
				'metrics_platform_client' => [
					'provide_values' => [
						'agent_client_platform_family',
						'performer_language',
						'performer_language_variant',
						'performer_pageview_id',
						'performer_session_id',
						'performer_active_browsing_session_token',
					],
				],
			],
		],

		// Instrument for Special:Translate (T364460)
		'mediawiki.product_metrics.translate_extension' => [
			'schema_title' => 'analytics/product_metrics/web/translation',
			'destination_event_service' => 'eventgate-analytics-external',
			'producers' => [
				'metrics_platform_client' => [
					'provide_values' => [
						'mediawiki_database',
						'mediawiki_site_content_language',
						'mediawiki_site_content_language_variant',
						'page_content_language',
						'agent_client_platform',
						'agent_client_platform_family',
						'performer_session_id',
						'performer_active_browsing_session_token',
						'performer_name',
						'performer_is_bot',
						'performer_is_logged_in',
						'performer_edit_count_bucket',
						'performer_groups',
						'performer_registration_dt',
						'performer_is_temp',
						'performer_language',
						'performer_language_variant',
						'performer_pageview_id',
					],
				],
			],
			'sample' => [
				'unit' => 'pageview',
				'rate' => 1,
			],
		],
		// (T382147) MassDelete extension stream
		'mediawiki.product_metrics.ext_massdelete' => [
			'schema_title' => 'analytics/mediawiki/product_metrics/ext_massdelete',
			'destination_event_service' => 'eventgate-analytics-external',
			'producers' => [
				'metrics_platform_client' => [
					'provide_values' => [
						'mediawiki_database',
						'performer_session_id',
					],
				],
			],
		],
		// (T336722, T350497) Wikifunctions-specific stream
		'mediawiki.product_metrics.wikifunctions_ui' => [
			'schema_title' => 'analytics/mediawiki/product_metrics/wikilambda/ui_actions',
			'destination_event_service' => 'eventgate-analytics-external',
			'producers' => [
				'metrics_platform_client' => [
					'provide_values' => [
						'agent_client_platform_family',
						'page_id',
						'page_title',
						'page_revision_id',
						'performer_is_logged_in',
						'performer_id',
						'performer_name',
						'performer_session_id',
						'performer_active_browsing_session_token',
						'performer_pageview_id',
						'performer_language',
						'performer_language_variant',
						'performer_edit_count',
						'performer_edit_count_bucket',
						'performer_groups',
						'performer_is_bot'
					],
				],
			],
			'sample' => [
				'unit' => 'pageview',
				'rate' => 1,
			],
		],
		// (T356228, T360369) Stream to track the API of WikiLambda (the Wikifunctions extension)
		'mediawiki.product_metrics.wikilambda_api' => [
			'schema_title' => 'analytics/mediawiki/product_metrics/wikilambda/api',
			'destination_event_service' => 'eventgate-analytics-external',
			'producers' => [
				'metrics_platform_client' => [
					'provide_values' => [
						'agent_client_platform_family',
						'page_id',
						'page_revision_id',
						'page_title',
						'performer_active_browsing_session_token',
						'performer_edit_count',
						'performer_edit_count_bucket',
						'performer_groups',
						'performer_id',
						'performer_is_bot',
						'performer_is_logged_in',
						'performer_language',
						'performer_language_variant',
						'performer_name',
						'performer_pageview_id',
						'performer_session_id',
					],
				],
			],
			'sample' => [
				'unit' => 'pageview',
				'rate' => 1,
			],
		],
		// (T363685, T368028, T378565) MinT for Wikipedia Readers stream (Language & Product Localization)
		'mediawiki.product_metrics.translation_mint_for_readers' => [
			'schema_title' => 'analytics/product_metrics/web/translation',
			'destination_event_service' => 'eventgate-analytics-external',
			'producers' => [
				'eventgate' => [
					'enrich_fields_from_http_headers' => [
						'http.request_headers.user-agent' => false,
					],
				],
				'metrics_platform_client' => [
					'provide_values' => [
						'mediawiki_database',
						'mediawiki_site_content_language',
						'mediawiki_site_content_language_variant',
						'page_content_language',
						'agent_client_platform',
						'agent_client_platform_family',
						'performer_session_id',
						'performer_active_browsing_session_token',
						'performer_name',
						'performer_is_bot',
						'performer_is_logged_in',
						'performer_edit_count_bucket',
						'performer_groups',
						'performer_registration_dt',
						'performer_is_temp',
						'performer_language',
						'performer_language_variant',
						'performer_pageview_id',
					],
				],
			],
			'sample' => [
				'unit' => 'pageview',
				'rate' => 1,
			],
		],
		// (T397043, T397600) MinT for Wiki Readers: pagevisit instrumentation for experiment (Language & Product Localization)
		'mediawiki.product_metrics.translation_mint_for_readers.experiments' => [
			'schema_title' => 'analytics/product_metrics/web/translation',
			'destination_event_service' => 'eventgate-analytics-external',
			'producers' => [
				'eventgate' => [
					'enrich_fields_from_http_headers' => [
						// Don't collect the user agent
						'http.request_headers.user-agent' => false,
					],
					'use_edge_uniques' => true,
				],
				'metrics_platform_client' => [
					'provide_values' => [
						'mediawiki_database',
						'mediawiki_skin',
						'mediawiki_site_content_language',
						'mediawiki_site_content_language_variant',
						'page_content_language',
						'agent_client_platform',
						'agent_client_platform_family',
						'performer_session_id',
						'performer_active_browsing_session_token',
						'performer_is_logged_in',
						'performer_is_temp',
						'performer_language',
						'performer_language_variant',
						'performer_pageview_id',
					],
				],
			],
		],
		// (T365889) Stream to track Special:Homepage modules interactions (GrowthExperiments)
		'mediawiki.product_metrics.homepage_module_interaction' => [
			'schema_title' => 'analytics/product_metrics/web/base',
			'destination_event_service' => 'eventgate-analytics-external',
			'producers' => [
				'metrics_platform_client' => [
					'provide_values' => [
						'mediawiki_database',
						'mediawiki_site_content_language',
						'mediawiki_site_content_language_variant',
						'page_content_language',
						'agent_client_platform',
						'agent_client_platform_family',
						'performer_session_id',
						'performer_active_browsing_session_token',
						'performer_name',
						'performer_is_bot',
						'performer_is_logged_in',
						'performer_edit_count_bucket',
						'performer_groups',
						'performer_registration_dt',
						'performer_is_temp',
						'performer_language',
						'performer_language_variant',
						'performer_pageview_id',
						'performer_id',
					],
				],
			],
			'sample' => [
				'unit' => 'pageview',
				'rate' => 1,
			],
		],
		'mediawiki.product_metrics.growth_product_interaction' => [
			'schema_title' => 'analytics/product_metrics/web/base',
			'destination_event_service' => 'eventgate-analytics-external',
			'producers' => [
				'eventgate' => [
					'enrich_fields_from_http_headers' => [
						'http.request_headers.user-agent' => false,
					],
				],
			]
		],
		// (T386440) Instrument for the User Info Card
		'mediawiki.product_metrics.user_info_card_interaction' => [
			'schema_title' => 'analytics/product_metrics/web/base',
			'destination_event_service' => 'eventgate-analytics-external',
			'producers' => [
				'eventgate' => [
					'enrich_fields_from_http_headers' => [
						'http.request_headers.user-agent' => false,
					],
				],
				'metrics_platform_client' => [
					'provide_values' => [
						'agent_client_platform_family',
						'page_namespace',
						'page_namespace_id',
						'performer_active_browsing_session_token',
						'performer_edit_count_bucket',
						'performer_groups',
						'performer_language',
						'performer_language_variant',
						'performer_name',
						'performer_registration_dt',
						'performer_session_id',
						'mediawiki_skin',
						'mediawiki_database',
					],
				],
			],
		],
		// (T373967) App base stream configuration to support Metrics Platform's monotable
		'product_metrics.app_base' => [
			'schema_title' => 'analytics/product_metrics/app/base',
			'destination_event_service' => 'eventgate-analytics-external',
			'producers' => [
				'metrics_platform_client' => [
					'provide_values' => [],
				],
			],
		],

		// Web base stream configuration to support Experiment Platform's monotable.
		//
		// See T373967 for context.
		'product_metrics.web_base' => [
			'schema_title' => 'analytics/product_metrics/web/base',
			'destination_event_service' => 'eventgate-analytics-external',
			'producers' => [
				'metrics_platform_client' => [
					'provide_values' => [

						// Make platform (e.g. desktop or mobile) available as a dimension during analysis.
						'agent_client_platform',
						'agent_client_platform_family',

						// Make user authentication status available as a dimension during analysis.
						'performer_is_logged_in',
						'performer_is_temp',

						// Enable the calculation of the "click-through per page visit" generic metric.
						'performer_pageview_id',

						// The ClickThroughRateInstrument instrument uses this stream by default. Capture the "smart
						// session ID" contextual attribute so that analysts can calculate all three flavors of
						// click-through rate (see
						// https://meta.wikimedia.org/wiki/Research_and_Decision_Science/Data_glossary/Clickthrough_Rate#Metric_definitions).
						'performer_active_browsing_session_token',

						// Temporary: The first everyone experiment run by Web will use this stream. Make skin and
						// database (DBname) available as dimensions during analysis for the duration of that
						// experiment. See https://phabricator.wikimedia.org/T394457 and
						// https://phabricator.wikimedia.org/T394093 for detail.
						'mediawiki_skin',
						'mediawiki_database',
					],
				],
				'eventgate' => [
					'enrich_fields_from_http_headers' => [
						// Don't collect the user agent
						'http.request_headers.user-agent' => false,
					],
					'use_edge_uniques' => true,
				],
			],
		],
		// Stream configuration for PES1.3 Wikirun game: https://wikirun-game.toolforge.org/
		'product_metrics.web_base.wikrun_game' => [
			'schema_title' => 'analytics/product_metrics/web/base',
			'destination_event_service' => 'eventgate-analytics-external',
		],
		// (T389097) Stream to track Article Summaries.
		'product_metrics.web_base.article_summaries' => [
			'schema_title' => 'analytics/product_metrics/web/base',
			'destination_event_service' => 'eventgate-analytics-external',
			'producers' => [
				'metrics_platform_client' => [
					'provide_values' => [
						'mediawiki_database',
						'page_id',
						'page_title',
						'page_namespace_id',
						'mediawiki_skin',
						'page_content_language',
						'agent_client_platform',
						'agent_client_platform_family',
						'performer_session_id',
						'performer_name',
						'performer_is_bot',
						'performer_is_logged_in',
						'performer_is_temp',
					],
				],
			],
			'sample' => [
				'unit' => 'pageview',
				'rate' => 1,
			],
		],
		'analytics.haproxy_requestctl' => [
			'schema_title' => 'analytics/haproxy_requestctl',
			'destination_event_service' => 'eventgate-analytics',
		],
		// (T403255, T403259) Image browsing experiment by Reader Growth.
		//
		// Sampling unit and rates are set up in the
		// Metrics Platform Experimentation Lab (MPIC) at
		// https://mpic.wikimedia.org/experiment/fy2025-26-we3.1-image-browsing-ab-test
		'mediawiki.product_metrics.readerexperiments_imagebrowsing' => [
			'schema_title' => '/analytics/product_metrics/web/base',
			'destination_event_service' => 'eventgate-analytics-external',
			'producers' => [
				'metrics_platform_client' => [
					'provide_values' => [
						'agent_client_platform',
						'agent_client_platform_family',
						'mediawiki_database',
						'mediawiki_skin',
						'page_content_language',
						'page_namespace_id',
						'performer_is_bot',
						'performer_is_logged_in',
						'performer_is_temp',
						'performer_session_id',
					],
				],
			],
		],
	],
	'+legacy-vector' => [
		'mediawiki.web_ui_actions' => [
			'sample' => [
				'rate' => 0,
			],
		],
	],
	'+officewiki' => [
		'mediawiki.web_ui_actions' => [
			'sample' => [
				'rate' => 0,
			],
		],
	],
	'+private' => [
		// private wikis have wgEnableEventBus set to TYPE_JOB|TYPE_EVENT.  But,
		// wgEventStreamsDefaultSettings, we explicitly set mediawiki_eventbus.enabled => false
		// to prevent most TYPE_EVENT streams from being produced.  We don't want the regular
		// TYPE_EVENT streams from private wikis to be produced to the usual streams, as many
		// of those streams are public.
		// This is confusing, and would be simplified by https://phabricator.wikimedia.org/T370524.
		// See also:
		// - https://phabricator.wikimedia.org/T346046
		// - https://phabricator.wikimedia.org/T371433
		//
		'/^mediawiki\\.job\\..+/' => [
			'producers' => [
				'mediawiki_eventbus' => [
					'enabled' => true,
				],
			],
		],
		'mediawiki.page_change.private.v1' => [
			'producers' => [
				'mediawiki_eventbus' => [
					'enabled' => true,
				],
			],
		],
		'mediawiki.cirrussearch.page_rerender.private.v1' => [
			'producers' => [
				'mediawiki_eventbus' => [
					'enabled' => true,
				],
			],
		],
	],
	'+testwiki' => [
		'mediawiki.web_ui_actions' => [
			'sample' => [
				'rate' => 1,
			],
		],
	],
	'+enwiki' => [
		'mediawiki.web_ui_actions' => [
			'sample' => [
				'rate' => 0.01,
			],
		],
	],
],

];
