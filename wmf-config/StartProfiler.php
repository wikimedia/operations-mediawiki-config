<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

# ######################################################################
# StartProfiler.php is where MediaWiki expects the $wgProfiler setting.
#
# This file is included by MediaWiki core's Setup.php. Aside from any
# `auto_prepend_file`, and the first few lines of /w entrypoint, and some
# of the WebStart.php file, nothing will have run yet.
#
# Specifically, StartProfiler runs *before* MediaWiki's Defines.php,
# DefaultSettings.php, and wmf-config CommonSettings or InitialiseSettings.
# ######################################################################

$wmfProfiler = [];

// profiler.php defines $wmfProfiler
require_once __DIR__ . '/profiler.php';

$wgProfiler = $wmfProfiler;
