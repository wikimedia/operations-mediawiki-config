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

require_once __DIR__ . '/profiler.php';

wmfSetupProfiler( [
	'redis-host' => 'deployment-fluorine02.deployment-prep.eqiad.wmflabs',
	'redis-port' => 6379,
	'redis-timeout' => 1.0,
	'use-xhgui' => false, // Not yet available in Beta Cluster, T180761
] );

require __DIR__ . '/set-time-limit.php';
