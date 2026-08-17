<?php

// Inline comments are often used for noting the task(s) associated with specific configuration
// and requiring comments to be on their own line would reduce readability for this file
// phpcs:disable MediaWiki.WhiteSpace.SpaceBeforeSingleLineComment.NewLineComment

/**
 * Per-wiki settings for the FlaggedRevs extension.
 *
 * See also wmf-config/flaggedrevs.php, which holds the global (non per-wiki)
 * configuration and loads the extension itself.
 */

return [

'wmgUseFlaggedRevs' => [
	'default' => false,
	'flaggedrevs' => true,
],

'wgFlaggedRevsOverride' => [
	'default' => true,
	'alswiki' => false,
	'bewiki' => false,
	'bnwiki' => false,
	'cewiki' => false,
	'ckbwiki' => false,
	'dewikiquote' => false,
	'dewiktionary' => false,
	'enwiki' => false,
	'enwikibooks' => false,
	'eowiki' => false,
	'fawiki' => false,
	'fiwiki' => false,
	'hiwiki' => false,
	'iawiki' => false,
	'iswiktionary' => false,
	'ptwiki' => false,
	'ptwikibooks' => false,
	'ruwiki' => false,
	'ukwiki' => false,
	'vecwiki' => false,
],

'wgFlaggedRevsProtection' => [
	'default' => false,
	'bnwiki' => true,
	'ckbwiki' => true,
	'enwiki' => true,
	'fawiki' => true,
	'hiwiki' => true,
	'ptwiki' => true,
	'idwiki' => true,
],

'wgSimpleFlaggedRevsUI' => [
	'default' => true,
	'enwikibooks' => false,
	'fiwiki' => false,
	'ptwikibooks' => false,
	'ruwiki' => false,
],

'wgFlaggedRevsHandleIncludes' => [
	// FR_INCLUDES_CURRENT = 0
	'default' => 2,
	'bnwiki' => 0,
	'ckbwiki' => 0,
	'enwiki' => 0,
	'enwikibooks' => 0, // T410330
	'fawiki' => 0,
	'hiwiki' => 0,
	'ptwiki' => 0,
	'ruwikisource' => 0,
],

'wgFlaggedRevsAutoReview' => [
	'default' => 3,
	'hewikisource' => 1,
],

'wgFlaggedRevsLowProfile' => [
	'default' => true,
	'huwiki' => false,
],

];
