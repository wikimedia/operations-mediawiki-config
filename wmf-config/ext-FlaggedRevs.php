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

// DO NOT CHANGE the default without hard-coding the value into the relevant wikis first.
'wgFlaggedRevsTags' => [
	'default' => [ 'accuracy' => [ 'levels' => 2 ] ],
	'bewiki' => [ 'accuracy' => [ 'levels' => 1 ] ],
	'bnwiki' => [ 'status' => [ 'levels' => 1 ] ],
	'bswiki' => [ 'status' => [ 'levels' => 1 ] ], // T158662
	'cewiki' => [ 'accuracy' => [ 'levels' => 3 ] ],
	'ckbwiki' => [ 'status' => [ 'levels' => 1 ] ],
	'dewiki' => [ 'accuracy' => [ 'levels' => 1 ] ],
	'dewiktionary' => [ 'accuracy' => [ 'levels' => 1 ] ],
	'enwiki' => [ 'status' => [ 'levels' => 1 ] ],
	'enwikibooks' => [ 'accuracy' => [ 'levels' => 1 ] ], // T428329
	'eowiki' => [ 'accuracy' => [ 'levels' => 1 ] ],
	'fawiki' => [ 'status' => [ 'levels' => 1 ] ],
	'fiwiki' => [ 'accuracy' => [ 'levels' => 3 ] ],
	'hewikisource' => [ 'accuracy' => [ 'levels' => 4 ] ],
	'hiwiki' => [ 'status' => [ 'levels' => 1 ] ],
	'iawiki' => [ 'accuracy' => [ 'levels' => 1 ] ],
	'kawiki' => [ 'accuracy' => [ 'levels' => 1 ] ],
	'plwiki' => [ 'accuracy' => [ 'levels' => 1 ] ], // T45617, T50043
	'ptwiki' => [ 'status' => [ 'levels' => 1 ] ], // T56828
	'ruwiki' => [ 'accuracy' => [ 'levels' => 1 ] ],
	'test2wiki' => [ 'accuracy' => [ 'levels' => 1 ] ],
	'ukwiki' => [ 'accuracy' => [ 'levels' => 1 ] ], // T434252
	'vecwiki' => [ 'accuracy' => [ 'levels' => 3 ] ],
],

];
