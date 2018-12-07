<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

/**
 * Initialisation code for all PHP processes.
 *
 * PHP is configured to execute this file before the main script, through
 * the `auto_prepend_file` setting. This can apply both to web request
 * and CLI processes.
 *
 * This is executed in the same run-time as the main script, which means
 * it CAN expose state, such as variables and constants.
 *
 * @see https://secure.php.net/manual/en/ini.core.php#ini.auto-prepend-file
 */
if ( !defined( 'HHVM_VERSION' ) ) {
	require_once __DIR__ . '/php7.php';
}

// https://phabricator.wikimedia.org/T180183
require_once __DIR__ . '/profiler.php';
require_once __DIR__ . '/../src/ServiceConfig.php';

$wmfServiceConfig = Wikimedia\MWConfig\ServiceConfig::getInstance();

wmfSetupProfiler( [
	'redis-host' => $wmfServiceConfig->getLocalService( 'xenon' ),
	'redis-port' => 6379,
	'redis-timeout' => $wmfServiceConfig->getRealm() === 'labs' ? 1 : 0.1,
	'use-xhgui' => !!$wmfServiceConfig->getLocalService( 'xhgui' ),
	'xhgui-conf' => [
		'save.handler' => 'mongodb',
		'db.host'      => $wmfServiceConfig->getLocalService( 'xhgui' ),
		'db.db'        => 'xhprof',
		'db.options'   => [],
	],
	'excimer-production-period' => 60,
	'excimer-single-period' => 0.01,
] );

require __DIR__ . '/set-time-limit.php';
