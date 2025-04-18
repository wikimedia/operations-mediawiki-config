<?php

// Inline comments are often used for noting the task(s) associated with specific configuration
// and requiring comments to be on their own line would reduce readability for this file
// phpcs:disable MediaWiki.WhiteSpace.SpaceBeforeSingleLineComment.NewLineComment

return [

'wmgUseGrowthExperiments' => [
	'default' => false,
	'growthexperiments' => true,
],

'wmgGEActiveExperiment' => [
	'default' => false,
	'testwiki' => 'surfacing-structured-task', // T386739
	'arzwiki' => 'surfacing-structured-task',
	'enwiki' => 'no-link-recommendation',
	'eswiki' => 'surfacing-structured-task',
	'fawiki' => 'surfacing-structured-task',
	'frwiki' => 'surfacing-structured-task',
	'idwiki' => 'surfacing-structured-task',
	'ptwiki' => 'surfacing-structured-task',
],

'wgGEImageRecommendationApiHandler' => [
	'default' => 'production',
],

'wgGEHelpPanelHelpDeskTitle' => [
	'default' => '',
],

'wgGEHelpPanelViewMoreTitle' => [
	'default' => '',
],

'wgGEHelpPanelLinks' => [
	'default' => [],
],

'wgGELevelingUpFeaturesEnabled' => [
	'default' => true,
],

'wgGEApiQueryGrowthTasksLookaheadSize' => [
	'default' => 10,
],

'wgGERefreshUserImpactDataMaintenanceScriptEnabled' => [
	'default' => true,
],

'wgGEHomepageSuggestedEditsEnabled' => [
	'default' => true,
	'frwiktionary' => false,
],

'wgGENewcomerTasksImageRecommendationsEnabled' => [
	'default' => false,
	'testwiki' => true,
	'arwiki' => true,
	'bnwiki' => true,
	'cswiki' => true,
	'elwiki' => true,
	'eswiki' => true,
	'fawiki' => true,
	'frwiki' => true,
	'idwiki' => true,
	'plwiki' => true,
	'ptwiki' => true,
	'rowiki' => true,
	'trwiki' => true,
	'viwiki' => true,
	'zhwiki' => true,
],

'wgGENewcomerTasksSectionImageRecommendationsEnabled' => [
	'default' => false,
	'testwiki' => true,
	'arwiki' => true,
	'bnwiki' => true,
	'cswiki' => true,
	'elwiki' => true,
	'eswiki' => true,
	'fawiki' => true,
	'frwiki' => true,
	'idwiki' => true,
	'plwiki' => true,
	'ptwiki' => true,
	'rowiki' => true,
	'trwiki' => true,
	'viwiki' => true,
	'zhwiki' => true,
],

'wgGENewcomerTasksLinkRecommendationsEnabled' => [
	'default' => false,
	'testwiki' => true,
	'abwiki' => true,
	'acewiki' => true,
	'adywiki' => true,
	'afwiki' => true,
	'alswiki' => true,
	'amwiki' => true,
	'anwiki' => true,
	'angwiki' => true,
	'arcwiki' => true,
	'arwiki' => true,
	'arywiki' => true,
	'arzwiki' => true,
	'aswiki' => false, // T344319
	'astwiki' => true,
	'atjwiki' => true,
	'avwiki' => true,
	'aywiki' => true,
	'azwiki' => true,
	'azbwiki' => true,
	'bawiki' => true,
	'banwiki' => true,
	'barwiki' => true,
	'bat_smgwiki' => true,
	'bclwiki' => true,
	'bewiki' => true,
	'be_x_oldwiki' => true,
	'bgwiki' => true,
	'bhwiki' => true,
	'biwiki' => true,
	'bjnwiki' => true,
	'bmwiki' => true,
	'bnwiki' => true,
	'bpywiki' => true,
	'brwiki' => true,
	'bswiki' => true,
	'bugwiki' => true,
	'bxrwiki' => true,
	'cawiki' => true,
	'cbk_zamwiki' => true,
	'cdowiki' => true,
	'cewiki' => true,
	'cebwiki' => true,
	'chwiki' => true,
	'chrwiki' => true,
	'chywiki' => true,
	'ckbwiki' => true,
	'cowiki' => true,
	'crwiki' => true,
	'crhwiki' => true,
	'cswiki' => true,
	'csbwiki' => true,
	'cuwiki' => true,
	'cvwiki' => true,
	'cywiki' => true,
	'dawiki' => true,
	'dewiki' => true,
	'dinwiki' => true,
	'diqwiki' => false, // T304551
	'dsbwiki' => true,
	'dvwiki' => false, // T304551
	'eewiki' => true,
	'emlwiki' => true,
	'enwiki' => true, // T308144
	'elwiki' => true,
	'eowiki' => true,
	'eswiki' => true,
	'etwiki' => true,
	'euwiki' => true,
	'extwiki' => true,
	'fawiki' => true,
	'ffwiki' => true,
	'fiwiki' => true,
	'fiu_vrowiki' => true,
	'fjwiki' => true,
	'fowiki' => true,
	'frwiki' => true,
	'frpwiki' => true,
	'frrwiki' => true,
	'furwiki' => true,
	'gawiki' => true,
	'gagwiki' => true,
	'ganwiki' => false, // T344319
	'gcrwiki' => true,
	'gdwiki' => true,
	'glwiki' => true,
	'glkwiki' => true,
	'gnwiki' => true,
	'gomwiki' => true,
	'gorwiki' => true,
	'gotwiki' => true,
	'guwiki' => true,
	'gvwiki' => true,
	'hawiki' => true,
	'hakwiki' => true,
	'hawwiki' => true,
	'hewiki' => true,
	'hiwiki' => true,
	'hifwiki' => true,
	'hrwiki' => true,
	'hsbwiki' => true,
	'htwiki' => true,
	'huwiki' => true,
	'hywiki' => true,
	'iawiki' => true,
	'idwiki' => true,
	'iewiki' => true,
	'igwiki' => true,
	'ikwiki' => true,
	'ilowiki' => true,
	'inhwiki' => true,
	'iowiki' => true,
	'iswiki' => true,
	'itwiki' => true,
	'iuwiki' => true,
	'jamwiki' => true,
	'jbowiki' => true,
	'jvwiki' => true,
	'kawiki' => true,
	'kaawiki' => true,
	'kabwiki' => true,
	'kbdwiki' => true,
	'kbpwiki' => true,
	'kgwiki' => true,
	'kiwiki' => true,
	'kkwiki' => true,
	'klwiki' => true,
	'kmwiki' => true,
	'knwiki' => true,
	'kowiki' => true,
	'koiwiki' => true,
	'krcwiki' => false, // T344319
	'kswiki' => true,
	'kshwiki' => true,
	'kuwiki' => true,
	'kvwiki' => true,
	'kwwiki' => true,
	'kywiki' => true,
	'lawiki' => true,
	'ladwiki' => true,
	'lbwiki' => true,
	'lbewiki' => true,
	'lezwiki' => true,
	'lfnwiki' => true,
	'lgwiki' => true,
	'liwiki' => true,
	'lijwiki' => true,
	'lmowiki' => true,
	'lnwiki' => true,
	'lowiki' => true,
	'ltwiki' => true,
	'ltgwiki' => true,
	'lvwiki' => true,
	'maiwiki' => true,
	'map_bmswiki' => true,
	'mdfwiki' => true,
	'mgwiki' => true,
	'mhrwiki' => true,
	'miwiki' => true,
	'minwiki' => true,
	'mkwiki' => true,
	'mlwiki' => true,
	'mnwiki' => true,
	'mrwiki' => true,
	'mrjwiki' => true,
	'mswiki' => true,
	'mtwiki' => true,
	'mwlwiki' => true,
	'myvwiki' => true,
	'mznwiki' => true,
	'nahwiki' => true,
	'napwiki' => true,
	'ndswiki' => true,
	'nds_nlwiki' => true,
	'newiki' => true,
	'newwiki' => true,
	'nlwiki' => true,
	'nnwiki' => true,
	'novwiki' => false, // T308138#9090826
	'nowiki' => true,
	'nqowiki' => true,
	'nrmwiki' => false, // T308138#9090826
	'nsowiki' => true,
	'nvwiki' => false, // T308138#9090826
	'nywiki' => true,
	'ocwiki' => true,
	'olowiki' => true,
	'omwiki' => true,
	'orwiki' => true,
	'oswiki' => true,
	'pawiki' => true,
	'pagwiki' => true,
	'pamwiki' => true,
	'papwiki' => true,
	'pcdwiki' => true,
	'pdcwiki' => true,
	'pflwiki' => true,
	'piwiki' => false, // T308138#8708597
	'pihwiki' => true,
	'plwiki' => true,
	'pmswiki' => true,
	'pnbwiki' => true,
	'pntwiki' => true,
	'pswiki' => true,
	'ptwiki' => true,
	'quwiki' => true,
	'rmwiki' => true,
	'rmywiki' => true,
	'rnwiki' => true,
	'rowiki' => true,
	'roa_rupwiki' => true,
	'roa_tarawiki' => true,
	'ruwiki' => true,
	'ruewiki' => true,
	'rwwiki' => true,
	'sawiki' => true,
	'sahwiki' => true,
	'satwiki' => true,
	'scwiki' => true,
	'scnwiki' => true,
	'scowiki' => true,
	'sdwiki' => true,
	'sewiki' => true,
	'sgwiki' => true,
	'shwiki' => true,
	'siwiki' => true,
	'simplewiki' => true,
	'skwiki' => true,
	'slwiki' => true,
	'smwiki' => true,
	'sowiki' => true,
	'sqwiki' => true,
	'srwiki' => true,
	'srnwiki' => true,
	'sswiki' => true,
	'stwiki' => true,
	'stqwiki' => true,
	'suwiki' => true,
	'svwiki' => true,
	'swwiki' => true,
	'szlwiki' => true,
	'tawiki' => true,
	'tcywiki' => true,
	'tewiki' => true,
	'tetwiki' => true,
	'tgwiki' => true,
	'thwiki' => true,
	'tkwiki' => true,
	'tlwiki' => true,
	'tnwiki' => true,
	'towiki' => true,
	'tpiwiki' => true,
	'trwiki' => true,
	'tswiki' => true,
	'ttwiki' => true,
	'tumwiki' => true,
	'twwiki' => true,
	'tywiki' => true,
	'tyvwiki' => true,
	'udmwiki' => true,
	'ugwiki' => true,
	'ukwiki' => true,
	'uzwiki' => true,
	'vewiki' => true,
	'vecwiki' => true,
	'vepwiki' => true,
	'viwiki' => true,
	'vlswiki' => true,
	'vowiki' => true,
	'wawiki' => true,
	'warwiki' => true,
	'wowiki' => true,
	'xalwiki' => true,
	'xhwiki' => true,
	'xmfwiki' => true,
	'yiwiki' => true,
	'yowiki' => true,
	'zawiki' => true,
	'zeawiki' => true,
	'zh_min_nanwiki' => true,
	'zuwiki' => true,
],

'wgGEImageRecommendationServiceUseTitles' => [
	'default' => false,
	'testwiki' => true,
],

'wgGESurfacingStructuredTasksEnabled' => [
	'default' => false,
	'testwiki' => true,
	'arzwiki' => true,
	'eswiki' => true,
	'fawiki' => true,
	'frwiki' => true,
	'idwiki' => true,
	'ptwiki' => true,
],

'wgGESurfacingStructuredTasksReadModeUpdateEnabled' => [
	// Only enable in production after aligning with ServiceOps about T378536
	'default' => false,
],

'wgGELinkRecommendationsFrontendEnabled' => [
	'default' => false,
	'testwiki' => true,
	'abwiki' => true,
	'acewiki' => true,
	'adywiki' => true,
	'afwiki' => true,
	'alswiki' => true,
	'amwiki' => true,
	'anwiki' => true,
	'angwiki' => true,
	'arcwiki' => true,
	'arwiki' => true,
	'arywiki' => true,
	'arzwiki' => true,
	'aswiki' => false, // T344319
	'astwiki' => true,
	'atjwiki' => true,
	'avwiki' => true,
	'aywiki' => true,
	'azwiki' => true,
	'azbwiki' => true,
	'bawiki' => true,
	'banwiki' => true,
	'barwiki' => true,
	'bat_smgwiki' => true,
	'bclwiki' => true,
	'bewiki' => true,
	'be_x_oldwiki' => true,
	'bgwiki' => true,
	'bhwiki' => true,
	'biwiki' => true,
	'bjnwiki' => true,
	'bmwiki' => true,
	'bnwiki' => true,
	'brwiki' => true,
	'bswiki' => true,
	'bxrwiki' => true,
	'cawiki' => true,
	'cbk_zamwiki' => true,
	'cdowiki' => true,
	'cewiki' => true,
	'cebwiki' => true,
	'chwiki' => false, // T304550
	'chrwiki' => true,
	'chywiki' => true,
	'ckbwiki' => true,
	'cowiki' => true,
	'crwiki' => false, // T304550
	'crhwiki' => true,
	'cswiki' => true,
	'csbwiki' => true,
	'cuwiki' => true,
	'cvwiki' => true,
	'cywiki' => true,
	'dawiki' => true,
	'dewiki' => true,
	'dinwiki' => true,
	'dsbwiki' => true,
	'eewiki' => true,
	'elwiki' => true,
	'enwiki' => true,
	'emlwiki' => true,
	'eowiki' => true,
	'eswiki' => true,
	'etwiki' => true,
	'euwiki' => true,
	'extwiki' => true,
	'fawiki' => true,
	'ffwiki' => true,
	'fiwiki' => true,
	'fiu_vrowiki' => true,
	'fjwiki' => true,
	'fowiki' => true,
	'frwiki' => true,
	'frpwiki' => true,
	'frrwiki' => true,
	'furwiki' => true,
	'gawiki' => true,
	'gagwiki' => true,
	'ganwiki' => false, // T344319
	'gcrwiki' => true,
	'gdwiki' => true,
	'glwiki' => true,
	'glkwiki' => true,
	'gnwiki' => true,
	'gomwiki' => true,
	'gorwiki' => true,
	'gotwiki' => true,
	'guwiki' => true,
	'gvwiki' => true,
	'hawiki' => true,
	'hakwiki' => true,
	'hawwiki' => true,
	'hewiki' => true,
	'hiwiki' => true,
	'hifwiki' => true,
	'hrwiki' => true,
	'hsbwiki' => true,
	'htwiki' => true,
	'huwiki' => true,
	'hywiki' => true,
	'iawiki' => true,
	'idwiki' => true,
	'iewiki' => true,
	'igwiki' => true,
	'ikwiki' => false, // T308134#8840607
	'ilowiki' => true,
	'inhwiki' => true,
	'iowiki' => true,
	'iswiki' => true,
	'itwiki' => true,
	'iuwiki' => true,
	'jamwiki' => true,
	'jbowiki' => false, // T308134#8840607
	'jvwiki' => true,
	'kawiki' => true,
	'kaawiki' => true,
	'kabwiki' => true,
	'kbdwiki' => true,
	'kbpwiki' => true,
	'kgwiki' => false, // T308135#9047447
	'kiwiki' => true,
	'kkwiki' => true,
	'klwiki' => false, // T308135#9047447
	'kmwiki' => true,
	'knwiki' => true,
	'kowiki' => true,
	'koiwiki' => true,
	'krcwiki' => false, // T344319
	'kswiki' => true,
	'kshwiki' => true,
	'kuwiki' => true,
	'kvwiki' => true,
	'kwwiki' => true,
	'kywiki' => true,
	'lawiki' => true,
	'ladwiki' => true,
	'lbwiki' => true,
	'lbewiki' => true,
	'lezwiki' => true,
	'lfnwiki' => true,
	'lgwiki' => true,
	'liwiki' => true,
	'lijwiki' => true,
	'lmowiki' => true,
	'lnwiki' => true,
	'lowiki' => true,
	'ltwiki' => false, // T308136#9084329
	'ltgwiki' => true,
	'lvwiki' => true,
	'maiwiki' => true,
	'map_bmswiki' => true,
	'mdfwiki' => true,
	'mgwiki' => true,
	'mhrwiki' => true,
	'miwiki' => true,
	'minwiki' => true,
	'mkwiki' => true,
	'mlwiki' => true,
	'mnwiki' => true,
	'mrwiki' => true,
	'mrjwiki' => true,
	'mswiki' => true,
	'mtwiki' => true,
	'mwlwiki' => true,
	'myvwiki' => true,
	'mznwiki' => true,
	'nawiki' => false, // T308137#9086803
	'nahwiki' => true,
	'napwiki' => true,
	'ndswiki' => true,
	'nds_nlwiki' => true,
	'newiki' => true,
	'newwiki' => true,
	'nlwiki' => true,
	'nnwiki' => true,
	'novwiki' => false, // T308138#9090826
	'nowiki' => true,
	'nqowiki' => true,
	'nrmwiki' => false, // T308138#9090826
	'nsowiki' => true,
	'nvwiki' => false, // T308138#9090826
	'nywiki' => true,
	'ocwiki' => true,
	'olowiki' => true,
	'omwiki' => true,
	'orwiki' => true,
	'oswiki' => true,
	'pawiki' => true,
	'pagwiki' => true,
	'pamwiki' => true,
	'papwiki' => true,
	'pcdwiki' => true,
	'pdcwiki' => true,
	'pflwiki' => true,
	'piwiki' => false, // T308138#8708597
	'pihwiki' => true,
	'plwiki' => true,
	'pmswiki' => true,
	'pnbwiki' => true,
	'pntwiki' => true,
	'pswiki' => true,
	'ptwiki' => true,
	'quwiki' => true,
	'rmwiki' => true,
	'rmywiki' => true,
	'rnwiki' => true,
	'rowiki' => true,
	'roa_rupwiki' => true,
	'roa_tarawiki' => true,
	'ruwiki' => true,
	'ruewiki' => true,
	'rwwiki' => true,
	'sawiki' => true,
	'sahwiki' => true,
	'satwiki' => true,
	'scwiki' => true,
	'scnwiki' => true,
	'scowiki' => true,
	'sdwiki' => true,
	'sewiki' => true,
	'sgwiki' => true,
	'shwiki' => true,
	'siwiki' => true,
	'simplewiki' => true,
	'skwiki' => true,
	'slwiki' => true,
	'smwiki' => true,
	'sowiki' => true,
	'sqwiki' => true,
	'srwiki' => true,
	'srnwiki' => true,
	'sswiki' => true,
	'stwiki' => true,
	'stqwiki' => true,
	'suwiki' => true,
	'svwiki' => true,
	'swwiki' => true,
	'szlwiki' => true,
	'tawiki' => true,
	'tcywiki' => true,
	'tewiki' => true,
	'tetwiki' => true,
	'tgwiki' => true,
	'thwiki' => true,
	'tkwiki' => true,
	'tlwiki' => true,
	'tnwiki' => true,
	'towiki' => true,
	'tpiwiki' => true,
	'trwiki' => true,
	'tswiki' => true,
	'ttwiki' => true,
	'tumwiki' => true,
	'twwiki' => true,
	'tywiki' => true,
	'tyvwiki' => true,
	'udmwiki' => true,
	'ugwiki' => true,
	'ukwiki' => true,
	'uzwiki' => true,
	'vewiki' => true,
	'vecwiki' => true,
	'vepwiki' => true,
	'viwiki' => true,
	'vlswiki' => true,
	'vowiki' => true,
	'wawiki' => true,
	'warwiki' => true,
	'wowiki' => true,
	'xalwiki' => true,
	'xhwiki' => true,
	'xmfwiki' => true,
	'yiwiki' => true,
	'yowiki' => true,
	'zawiki' => true,
	'zeawiki' => true,
	'zh_min_nanwiki' => true,
	'zuwiki' => true,
],

'wgGEDeveloperSetup' => [
	'default' => false,
	'testwiki' => true,
],

'wgGELinkRecommendationsUseEventGate' => [
	'default' => true,
],

'wgGELinkRecommendationServiceWikiIdMasquerade' => [
	'default' => null,
	'testwiki' => 'simplewiki',
],

'wgGELinkRecommendationsRefreshByIteratingThroughAllTitles' => [
	'default' => false,
	'arzwiki' => true,
	'cswiki' => true,
	'eswiki' => true,
	'fawiki' => true,
	'frwiki' => true,
	'idwiki' => true,
	'ptwiki' => true,
],

'wgGENewcomerTasksTopicType' => [
	'default' => 'ores',
],

'wgGEMentorshipEnabled' => [
	// overriden in community configuration for mentorship-enabled wikis
	'default' => false,
],

'wgGEMentorshipNewAccountEnablePercentage' => [
	'default' => 100,
	'eswiki' => 50, // T285235
],

'wgGEMentorshipReassignMenteesBatchSize' => [
	'default' => 5000,
],

'wgGEHomepageDefaultVariant' => [
	'default' => 'control',
],

'wgGEMentorDashboardEnabled' => [
	'default' => true,
],

'wgGEMentorDashboardEnabledModules' => [
	'default' => [
		'mentee-overview' => true,
		'mentor-tools' => true,
		'resources' => true,
		'personalized-praise' => false,
	],
	'+testwiki' => [
		'personalized-praise' => true,
	],
	'+arwiki' => [
		'personalized-praise' => true,
	],
	'+bnwiki' => [
		'personalized-praise' => true,
	],
	'+cswiki' => [
		'personalized-praise' => true,
	],
	'+eswiki' => [
		'personalized-praise' => true,
	],
	'+frwiki' => [
		'personalized-praise' => true,
	],
],

'wgGEPersonalizedPraiseNotificationsEnabled' => [
	'default' => true,
],

'wgGEPersonalizedPraiseBackendEnabled' => [
	'default' => false,
	'arwiki' => true,
	'bnwiki' => true,
	'cswiki' => true,
	'eswiki' => true,
	'frwiki' => true,
	'testwiki' => true,
],

'wgGETopicsMatchModeEnabled' => [
	'default' => false,
	// Enable Growth's feature topic match mode (T305399).
	// Will be removed after it's deployed to all wikis (T305408).
	'testwiki' => true,
	'arwiki' => true,
	'bnwiki' => true,
	'cswiki' => true,
	'eswiki' => true,
	'fawiki' => true,
	'ptwiki' => true,
	'trwiki' => true
],

'wgGECampaigns' => [
	'default' => [
		'thankyoupage-2023' => [
			'skipWelcomeSurvey' => true,
			'signupPageTemplate' => 'hero',
			'signupPageTemplateParameters' => [
				'showBenefitsList' => 'desktop',
				'messageKey' => 'thankyoupage',
			],
			'pattern' => '/^(typage|fundraising)-\w+-\w+-\d+$/',
		],
	],
],

'wgGECampaignTopics' => [
	'default' => []
],

'wgGERestbaseUrl' => [
	'default' => false,
],

'wgGEUseCommunityConfigurationExtension' => [
	'default' => true,
],

'wgGELinkRecommendationMinimumTasksPerTopic' => [
	'default' => 500,
	'arzwiki' => 2000,
	'eswiki' => 2000,
	'fawiki' => 2000,
	'frwiki' => 2000,
	'idwiki' => 2000,
	'ptwiki' => 2000,
],

];
