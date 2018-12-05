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

// https://phabricator.wikimedia.org/T180183
require_once __DIR__ . '/profiler.php';

wmfSetupProfiler( [
	'redis-host' => 'mwlog1001.eqiad.wmnet',
	'redis-port' => 6379,
	'redis-timeout' => 0.1,
	'use-xhgui' => true,
	'xhgui-conf' => [
		'save.handler' => 'mongodb',
		'db.host'      => 'mongodb://tungsten.eqiad.wmnet:27017',
		'db.db'        => 'xhprof',
		'db.options'   => [],
	]
] );

require __DIR__ . '/set-time-limit.php';
