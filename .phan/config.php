<?php

$cfg = require __DIR__ . '/../vendor/mediawiki/mediawiki-phan-config/src/config-library.php';

$cfg['directory_list'] = [
	'multiversion',
	'rpc',
	'src',
	'wmf-config',
];

$cfg['ignore_undeclared_variables_in_global_scope'] = true;

$cfg['whitelist_issue_types'] = [
	'PhanPluginDuplicateArrayKey',
];

return $cfg;
