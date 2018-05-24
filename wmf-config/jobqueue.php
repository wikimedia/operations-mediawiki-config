<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

/** @var string $wmgRedisPassword From PrivateSettings.php */

$wmgRedisQueueBaseConfig = [
	'class' => 'JobQueueRedis',
	'redisConfig' => [
		'connectTimeout' => 0.300,
		'password' => $wmgRedisPassword,
		'compression' => 'gzip',
		# 'persistent' => defined( 'MEDIAWIKI_JOB_RUNNER' )
	],
	'daemonized' => true
];

/**
 * @param array $base
 * @param array $partitions
 * @return array
 */
function wmfRedisConfigByPartition( $base, $partitions ) {
	$result = [];
	foreach ( $partitions as $partition => $addr ) {
		$result[$partition] = [ 'redisServer' => $addr ] + $base;
	}
	return $result;
}

$jobQueueFederatedConfig = [
	'class' => 'JobQueueFederated',
	'configByPartition' => wmfRedisConfigByPartition(
		$wmgRedisQueueBaseConfig,
		$wmfLocalServices['jobqueue_redis']
	),
	'sectionsByWiki' => [], // default
	// Weights for partitions in use: use this to depool redis masters
	'partitionsBySection' => [
		'default' => array_fill_keys( array_keys( $wmfLocalServices['jobqueue_redis'] ), 50 )
	],
	'maxPartitionsTry' => 5 // always covers 2+ servers
];

if ( $wmgUseEventBus ) {
	$wgJobTypeConf['categoryMembershipChange'] =
		$wgJobTypeConf['deleteLinks'] =
		$wgJobTypeConf['EchoNotificationDeleteJob'] =
		$wgJobTypeConf['flaggedrevs_CacheUpdate'] =
		$wgJobTypeConf['htmlCacheUpdate'] =
		$wgJobTypeConf['LocalRenameUserJob'] =
		$wgJobTypeConf['MessageIndexRebuildJob'] =
		$wgJobTypeConf['ORESFetchScoreJob'] =
		$wgJobTypeConf['recentChangesUpdate'] =
		$wgJobTypeConf['RecordLintJob'] =
		$wgJobTypeConf['refreshLinks'] =
		$wgJobTypeConf['refreshLinksDynamic'] =
		$wgJobTypeConf['refreshLinksPrioritized'] =
		$wgJobTypeConf['updateBetaFeaturesUserCounts'] =
		$wgJobTypeConf['wikibase-addUsagesForPage'] =
		$wgJobTypeConf['wikibase-InjectRCRecords'] =
		$wgJobTypeConf['cdnPurge'] =
		$wgJobTypeConf['ChangeNotification'] =
		$wgJobTypeConf['CognateCacheUpdateJob'] =
		$wgJobTypeConf['CognateLocalJobSubmitJob'] =
		$wgJobTypeConf['globalUsageCachePurge'] =
		$wgJobTypeConf['GlobalUserPageLocalJobSubmitJob'] =
		$wgJobTypeConf['LocalGlobalUserPageCacheUpdateJob'] =
		$wgJobTypeConf['TranslateDeleteJob'] =
		$wgJobTypeConf['TranslateRenderJob'] =
		// Cross-wiki posting jobs
		$wgJobTypeConf['CentralAuthUnattachUserJob'] =
		$wgJobTypeConf['CentralAuthCreateLocalAccountJob'] =
		$wgJobTypeConf['BounceHandlerJob'] =
		$wgJobTypeConf['BounceHandlerNotificationJob'] =
		$wgJobTypeConf['securePollPopulateVoterList'] =
		$wgJobTypeConf['LocalSharedHelpPageCacheUpdateJob'] =
		$wgJobTypeConf['MassMessageSubmitJob'] =
			[ 'class' => 'JobQueueEventBus' ];
	// Cirrus search is an exception T189137
	$wgJobTypeConf['cirrusSearchCheckerJob'] =
		$wgJobTypeConf['cirrusSearchDeleteArchive'] =
		$wgJobTypeConf['cirrusSearchDeletePages'] =
		$wgJobTypeConf['cirrusSearchElasticaWrite'] =
		$wgJobTypeConf['cirrusSearchIncomingLinkCount'] =
		$wgJobTypeConf['cirrusSearchLinksUpdate'] =
		$wgJobTypeConf['cirrusSearchLinksUpdatePrioritized'] =
		$wgJobTypeConf['cirrusSearchMassIndex'] =
		$wgJobTypeConf['cirrusSearchOtherIndex'] = [
			'class' => 'JobQueueSecondTestQueue',
			'mainqueue' => $jobQueueFederatedConfig,
			'debugqueue' => [
				'class' => 'JobQueueEventBus'
			],
			'readonly' => isset( $wmgDisableCirrusSearchJobsInRedis ) && $wmgDisableCirrusSearchJobsInRedis,
		];
	$wgJobTypeConf['gwtoolsetUploadMediafileJob'] = // T192946
		$wgJobTypeConf['EchoNotificationJob'] = // T192945
		$wgJobTypeConf['webVideoTranscode'] = // T188947
		$wgJobTypeConf['webVideoTranscodePrioritized'] = [ // T188947
			'class' => 'JobQueueSecondTestQueue',
			'mainqueue' => $jobQueueFederatedConfig,
			'debugqueue' => [
				'class' => 'JobQueueEventBus'
			]
		];
	$wgJobTypeConf['default'] = [ 'class' => 'JobQueueEventBus' ];
} else {
	$wgJobTypeConf['default'] = $jobQueueFederatedConfig;
}

unset( $jobQueueFederatedConfig );

// Note: on server failure, this should be changed to any other redis server
$wgJobQueueAggregator = [
	'class' => 'JobQueueAggregatorRedis',
	'redisServers' => $wmfLocalServices['jobqueue_aggregator'],
	'redisConfig' => [
		'connectTimeout' => 0.5,
		'password' => $wmgRedisPassword,
	]
];

$wgJobSerialCommitThreshold = 0.100; // 100 ms
