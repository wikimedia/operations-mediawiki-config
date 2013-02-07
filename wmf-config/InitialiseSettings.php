<?php
/**
 * vim: set sw=4 ts=4 noet foldmarker=@{,@} foldmethod=marker:
 * - recognize folding markers just like DefaultSettings.php
 * You need to use 'set modeline' to activate this feature.
 * za to toggle a fold
 * zA to toggle folds recursively
 * zR to open them all
 * zM close all folds
*/

# When editing Devanagari text, some editor/terminal combinations (vim +
# xterm/kconsole) cause problems.  Try an X editor instead if this happens
# (gvim works well for me) - river
# rev 73224
# öäü
# WARNING: This file is publically viewable on the web. Do not put private data here.

# Globals set in CommonSettings.php for use in settings values
global $wmfUdp2logDest, $wmfDatacenter, $wmfRealm, $wmfConfigDir, $wgConf;

$wgConf->settings = array(

// For live conversion of old revisions:
'wgLegacyEncoding' => array(
	'enwiki' => 'windows-1252',
	'dawiki' => 'windows-1252',
	'svwiki' => 'windows-1252',
	'nlwiki' => 'windows-1252',

	'dawiktionary' => 'windows-1252',
	'svwiktionary' => 'windows-1252',

	'default' => false,
),

'wgCapitalLinks' => array(
	'default' => true,
	'jbowiki' => false,
	'wiktionary' => false,
),

'wmgEnableLandingCheck' => array(
	'default' => false,
	'donatewiki' => true,
	'testwiki' => true,
	'foundationwiki' => true,
),

'wmgEnableFundraiserLandingPage' => array(
	'default' => false,
	'testwiki' => true,
	'donatewiki' => true,
),

'wgCompressRevisions' => array(
	'default' => true,
),

'wgDisableCounters' => array(
	'default' => true,
),

'wgInterwikiMagic' => array (
	'default' => true,
	'metawiki'      => false,
),

# wgLanguageCode @{
'wgLanguageCode' => array(
	'default' => '$lang',
	'special' => 'en', # overridden below by some wikis
	'advisorywiki' => 'en',
	'alswiki' => 'gsw', # FIXME: rename these wikis
	'alswiktionary' => 'gsw', # FIXME: rename these wikis
	'alswikibooks' => 'gsw', # to rename
	'alswikiquote' => 'gsw', # to rename
	'arbcom_dewiki' => 'de',
	'arbcom_enwiki' => 'en',
	'arbcom_nlwiki' => 'nl',
	'arbcom_fiwiki' => 'fi',
	'arwikimedia'	=> 'es',
	'auditcomwiki' => 'en',
	'bat_smgwiki' => 'sgs', # to rename
	'bdwikimedia' => 'bn',
	'bewikimedia' => 'en',
	'be_x_oldwiki' => 'be-tarask', # to rename
	'betawikiversity' => 'en',
	'brwikimedia' => 'pt-br',
	'chairwiki' => 'en',
	'chapcomwiki' => 'en',
	'checkuserwiki' => 'en',
	'collabwiki' => 'en',
	'cowikimedia'   => 'es',
	'crhwiki' => 'crh-latn',
	'de_labswikimedia' => 'de',
	'dkwikimedia' => 'da',
	'donatewiki' => 'en',
	'en_labswikimedia' => 'en',
	'execwiki' => 'en',
	'fdcwiki' => 'en',
	'fiu_vrowiki' => 'vro', # to rename
	'flaggedrevs_labswikimedia' => 'en',
	'grantswiki' => 'en',
	'ilwikimedia' => 'he',
	'incubatorwiki' => 'en', # mixed
	'mkwikimedia' => 'mk',
	'mxwikimedia' => 'es',
	'noboard_chapterswikimedia' => 'no',
	'nowiki' => 'nb',
	'nycwikimedia' => 'en',
	'nzwikimedia' => 'en',
	'officewiki' => 'en',
	'otrs_wikiwiki' => 'en',
	'pa_uswikimedia' => 'en',
	'qualitywiki' => 'en',
	'readerfeedback_labswikimedia' => 'en',
	'roa_rupwiki' => 'rup', # to rename
	'roa_rupwiktionary' => 'rup', # to rename
	'rswikimedia' => 'sr',
	'searchcomwiki' => 'en',
	'sewikimedia' => 'sv',
	'simplewiki' => 'en',
	'stewardwiki' => 'en',
	'strategywiki' => 'en',
	'tenwiki' => 'en',
	'uawikimedia' => 'uk',
	'ukwikimedia'   => 'en-gb',
	'usabilitywiki'     => 'en',
	'wikimania2008wiki' => 'en',
	'wikimania2009wiki' => 'en',
	'wikimania2010wiki' => 'en',
	'wikimania2011wiki' => 'en',
	'wikimania2012wiki' => 'en',
	'wikimania2013wiki' => 'en',
	'wikimaniateamwiki' => 'en',
	'zh_classicalwiki' => 'lzh', # to rename
	'zh_min_nanwiki' => 'nan', # to rename
	'zh_min_nanwiktionary' => 'nan', # to rename
	'zh_min_nanwikibooks' => 'nan', # to rename
	'zh_min_nanwikiquote' => 'nan', # to rename
	'zh_yuewiki' => 'yue', # to rename
),
# @} end of wgLanguageCode

'wgLocalInterwiki' => array(
	'default' => '$lang',
	'mediawikiwiki' => 'mw',
),

# wgLocaltimezone @{
'wgLocaltimezone' => array(
	'alswiki' => 'Europe/Berlin',
	'arbcom_dewiki' => 'Europe/Berlin',
	'arbcom_fiwiki' => 'Europe/Helsinki',
	'arbcom_nlwiki' => 'Europe/Berlin',
	'alswiktionary' => 'Europe/Berlin',
	'aswikisource' => 'Asia/Kolkata', // Bug 43129
	'barwiki' => 'Europe/Berlin',
	'bat_smgwiki' => 'Europe/Vilnius',
	'be_x_oldwiki' => 'Europe/Minsk',
	'bewikisource' => 'Europe/Minsk',
	'bjnwiki' => 'Asia/Bangkok',
	'brwikisource' => 'Europe/Paris',
	'bswiki' => 'Europe/Berlin',
	'cawiki' => 'Europe/Berlin',
	'cawiktionary' => 'Europe/Berlin',
	'cawikibooks' => 'Europe/Berlin',
	'cawikiquote' => 'Europe/Berlin',
	'cawikisource' => 'Europe/Berlin',
	'cawikinews' => 'Europe/Berlin',
	'csbwiki' => 'Europe/Warsaw',
	'csbwiktionary' => 'Europe/Warsaw',
	'dawiki' => 'Europe/Berlin',
	'dawikibooks' => 'Europe/Berlin',
	'dewiki' => 'Europe/Berlin',
	'dewikinews' => 'Europe/Berlin',
	'dewiktionary' => 'Europe/Berlin',
	'dewikibooks' => 'Europe/Berlin',
	'dewikiquote' => 'Europe/Berlin',
	'dewikisource' => 'Europe/Berlin',
	'dewikivoyage' => 'Europe/Berlin', // Bug 41992
	'dewikiversity' => 'Europe/Berlin',
	'dsbwiki' => 'Europe/Berlin',
	'elwikinews' => 'Europe/Athens',
	'eowikisource' => 'UTC',
	'etwiki'      => 'Europe/Tallinn',
	'etwikibooks'  => 'Europe/Tallinn',
	'etwikimedia'  => 'Europe/Tallinn',
	'etwikiquote'  => 'Europe/Tallinn',
	'etwikisource' => 'Europe/Tallinn',
	'etwiktionary' => 'Europe/Tallinn',
	'fawikinews' => 'Asia/Tehran',
	'fiwiki' => 'Europe/Helsinki',
	'fiwikimedia' => 'Europe/Helsinki',
	'frrwiki' => 'Europe/Berlin',
	'frwiki' => 'Europe/Paris',
	'frwikibooks' => 'Europe/Paris',
	'frwikiquote' => 'Europe/Paris',
	'fywiki' => 'Europe/Berlin',
	'gagwiki' => 'Europe/Istanbul',
	'guwiki' => 'Asia/Kolkata',
	'guwikisource' => 'Asia/Kolkata',
	'hewiki' => 'Asia/Tel_Aviv',
	'hewikibooks' => 'Asia/Tel_Aviv',
	'hewikiquote' => 'Asia/Tel_Aviv',
	'hewikisource' => 'Asia/Tel_Aviv',
	'hewiktionary' => 'Asia/Tel_Aviv',
	'hewikinews' => 'Asia/Tel_Aviv',
	'ilwikimedia' => 'Asia/Tel_Aviv',
	'itwikivoyage' => 'Europe/Rome', // Bug 41992
	'hrwiki' => 'Europe/Berlin',
	'hrwikibooks' => 'Europe/Zagreb',
	'hrwikiquote' => 'Europe/Berlin',
	'hrwikisource' => 'Europe/Zagreb',
	'hrwiktionary' => 'Europe/Berlin',
	'hsbwiki' => 'Europe/Berlin',
	'huwiki' => 'Europe/Berlin',
	'huwikibooks' => 'Europe/Berlin',
	'huwikinews' => 'Europe/Berlin',
	'huwikiquote' => 'Europe/Berlin',
	'huwikisource' => 'Europe/Berlin',
	'huwiktionary' => 'Europe/Berlin',
	'itwiki' => 'Europe/Berlin',
	'itwikinews' => 'Europe/Berlin',
	'itwikiversity' => 'Europe/Berlin',
	'itwikiquote' => 'Europe/Berlin',
	'itwikibooks' => 'Europe/Berlin',
	'itwikisource' => 'Europe/Berlin',
	'itwiktionary' => 'Europe/Berlin',
	'kbdwiki' => 'Europe/Moscow',
	'kkwiki' => 'Asia/Almaty',
	'kkwiktionary' => 'Asia/Almaty',
	'kkwikibooks' => 'Asia/Almaty',
	'kkwikiquote' => 'Asia/Almaty',
	'koiwiki' => 'Asia/Yekaterinburg',
	'kowiki' => 'Asia/Seoul',
	'kowikinews' => 'Asia/Seoul',
	'kowikiquote' => 'Asia/Seoul',
	'kowikisource' => 'Asia/Seoul',
	'kowikiversity' => 'Asia/Seoul',
	'kshwiki' => 'Europe/Berlin',
	'lezwiki' => 'Europe/Moscow',
	'liwiki' => 'Europe/Berlin',
	'ltgwiki' => 'Europe/Riga',
	'ltwiki' => 'Europe/Vilnius',
	'lmowiki' => 'Europe/Rome',
	'lvwiki' => 'Europe/Riga', // Bug 43592
	'mkwiki' => 'Europe/Berlin',
	'mkwikibooks' => 'Europe/Berlin',
	'mkwikimedia' => 'Europe/Berlin',
	'mkwikisource' => 'Europe/Berlin',
	'mkwiktionary' => 'Europe/Berlin',
	'minwiki' => 'Asia/Jakarta',
	'mrjwiki' => 'Europe/Moscow',
	'mrwiki' => 'Asia/Kolkata',
	'mrwikisource' => 'Asia/Kolkata',
	'ndswiki' => 'Europe/Berlin',
	'ndswiktionary' => 'Europe/Berlin',
	'ndswikibooks' => 'Europe/Berlin',
	'ndswikiquote' => 'Europe/Berlin',
	'nds_nlwiki' => 'Europe/Berlin',
	'nlwiki' => 'Europe/Berlin',
	'nlwikimedia' => 'Europe/Berlin',
	'nlwikibooks' => 'Europe/Berlin',
	'nlwikiquote' => 'Europe/Berlin',
	'nlwikisource' => 'Europe/Berlin',
	'nlwikivoyage' => 'Europe/Amsterdam', // Bug 41992
	'nlwiktionary' => 'Europe/Berlin',
	'nnwiki' => 'Europe/Berlin',
	'nnwikiquote' => 'Europe/Berlin',
	'nnwiktionary' => 'Europe/Berlin',
	'nowikimedia' => 'Europe/Berlin',
	'nowiki' => 'Europe/Berlin',
	'nowikibooks' => 'Europe/Berlin',
	'nowikiquote' => 'Europe/Berlin',
	'nowiktionary' => 'Europe/Berlin',
	'nowikinews' => 'Europe/Berlin',
	'nowikisource' => 'Europe/Berlin',
	'nsowiki' => 'Africa/Johannesburg',
	'pdcwiki' => 'America/New_York',
	'pflwiki' => 'Europe/Berlin',
	'plwiki' => 'Europe/Warsaw',
	'plwikisource' => 'Europe/Warsaw',
	'plwiktionary' => 'Europe/Warsaw',
	'plwikiquote' => 'Europe/Warsaw',
	'plwikibooks' => 'Europe/Warsaw',
	'plwikinews' => 'Europe/Warsaw',
	'plwikimedia' => 'Europe/Warsaw',
	'plwikivoyage' => 'Europe/Warsaw',
	'pnbwiki' => 'Asia/Karachi',
	'pnbwiktionary' => 'Asia/Karachi',
	'rmwiki' => 'Europe/Berlin',
	'rowiki' => 'Europe/Chisinau',
	'rowikisource' => 'Europe/Chisinau',
	'rowikivoyage' => 'Europe/Bucharest',
	'ruwikivoyage' => 'Europe/Moscow', // Bug 41992
	'ruewiki'	=> 'Europe/Berlin',
	'sawikiquote' => 'Asia/Kolkata',
	'sahwikisource' => 'Asia/Yakutsk',
	'scwiki' => 'Europe/Berlin',
	'scnwiki' => 'Europe/Berlin',
	'sewikimedia' => 'Europe/Berlin',
	'shwiki' => 'Europe/Berlin',
	'slwiki' => 'Europe/Berlin',
	'slwikiquote' => 'Europe/Berlin',
	'slwikiversity' => 'Europe/Ljubljana',
	'sqwiki'	=> 'Europe/Berlin',
	'sqwikinews'	=> 'Europe/Berlin',
	'srwiki' => 'Europe/Berlin',
	'srwikibooks' => 'Europe/Berlin',
	'srwikiquote' => 'Europe/Berlin',
	'srwiktionary' => 'Europe/Berlin',
	'srwikisource' => 'Europe/Berlin',
	'svwiki' => 'Europe/Berlin',
	'svwiktionary' => 'Europe/Berlin',
	'svwikiquote' => 'Europe/Berlin',
	'svwikibooks' => 'Europe/Berlin',
	'svwikisource' => 'Europe/Berlin',
	'svwikinews' => 'Europe/Berlin',
	'svwikiversity' => 'Europe/Berlin',
	'svwikivoyage' => 'Europe/Stockholm', // Bug 41992
	'szlwiki' => 'Europe/Warsaw',
	'thwiki' => 'Asia/Bangkok',
	'thwikibooks' => 'Asia/Bangkok',
	'thwikisource' => 'Asia/Bangkok',
	'thwiktionary' => 'Asia/Bangkok',
	'thwikiquote' => 'Asia/Bangkok',
	'trwikimedia' => 'Europe/Istanbul',
	'vecwiki' => 'Europe/Berlin',
	'vecwikisource' => 'Europe/Berlin',
	'vepwiki' => 'Europe/Moscow',
	'xmfwiki'		=> 'Asia/Tbilisi',
),
# @} end of wgLocaltimezone

# wgLogo @{
'wgLogo' => array(
	# Defaults
	'default'	   => '//upload.wikimedia.org/wikipedia/commons/d/d6/Wikipedia-logo-v2-en.png',
	'wikibooks'	 => '//upload.wikimedia.org/wikipedia/commons/4/49/Wikibooks-logo-en-noslogan.png',
	'wikinews'	  => '$stdlogo', // '/upload/b/bc/Wiki.png',
	'wikiquote'	 => '//upload.wikimedia.org/wikiquote/en/b/bc/Wiki.png',
	'wikisource'	=> '//upload.wikimedia.org/wikipedia/sources/b/bc/Wiki.png',
	'wikiversity'       => '$stdlogo',
	'wiktionary'	=> '//upload.wikimedia.org/wikipedia/commons/thumb/e/ec/Wiktionary-logo.svg/135px-Wiktionary-logo.svg.png',
	'wikivoyage'	=> '//upload.wikimedia.org/wikipedia/commons/d/da/WikivoyageLogoSmall.png',

	# Wikis (alphabetical by DB name)
	'abwiki'		=> '$stdlogo',
	'acewiki'		=> '$stdlogo',
	'advisorywiki'		=> '//upload.wikimedia.org/wikipedia/meta/7/7d/Wikimediaboard-logo135px.png',
	'afwiki'		=> '//upload.wikimedia.org/wikipedia/commons/d/d8/Wikipedia-logo-v2-af.png',
	'afwiktionary'		=> '$stdlogo',
	'alswiki'		=> '//upload.wikimedia.org/wikipedia/commons/2/26/Wikipedia-logo-v2-als.png',
	'amwiki'		=> '//upload.wikimedia.org/wikipedia/commons/9/98/Wikipedia-logo-v2-am.png', // bug 40285
	'amwiktionary'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/f/fd/Wiktionary-logo-am.png/135px-Wiktionary-logo-am.png', // bug 43240
	'angwiki'		=> '$stdlogo',
	'angwikibooks'		=> '$stdlogo',
	'angwikiquote'		=> '$stdlogo',
	'angwiktionary'		=> '$stdlogo',
	'anwiki'		=> '$stdlogo',
	'anwiktionary'		=> '$stdlogo',
	'arbcom_dewiki'		=> '$stdlogo',
	'arbcom_nlwiki'		=> '//upload.wikimedia.org/wikipedia/commons/9/9b/Arbcom_nl_logo.png',
	'arcwiki'		=> '$stdlogo',
	'arwiki'		=> '//upload.wikimedia.org/wikipedia/commons/1/1e/Wikipedia-logo-v2-ar.png',
	'arwikibooks'		=> '$stdlogo',
	'arwikimedia'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/b/ba/Wikimedia_Argentina_logo.svg/135px-Wikimedia_Argentina_logo.svg.png',
	'arwikiquote'		=> '$stdlogo',
	'arwikisource'		=> '$stdlogo',
	'arwikiversity'		=> '//upload.wikimedia.org/wikipedia/commons/5/5c/Wikiversity-logo-ar-small.png',
	'arwiktionary'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/b/be/WiktionaryAr.svg/135px-WiktionaryAr.svg.png',
	'arzwiki'		=> '//upload.wikimedia.org/wikipedia/commons/3/3b/Wikipedia-logo-v2-arz.png',
	'aswiki'		=> '$stdlogo',
	'aswikisource'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/b/b1/Wikisource-logo-as-3.svg/135px-Wikisource-logo-as-3.svg.png', // Bug 43129
	'astwiki'		=> '//upload.wikimedia.org/wikipedia/commons/f/fd/Wikipedia-logo-v2-ast.png', // bug 40285
	'astwiktionary'		=> '$stdlogo',
	'avwiki'		=> '$stdlogo',
	'aywiki'		=> '$stdlogo',
	'azwiki'		=> '$stdlogo',
	'azwikibooks'		=> '$stdlogo',
	'azwikiquote'		=> '$stdlogo',
	'azwikisource'		=> '$stdlogo',
	'azwiktionary'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/7/7f/Wiktionary-logo-az.png/135px-Wiktionary-logo-az.png', // bug 43240
	'bawiki'		=> '$stdlogo',
	'barwiki'		=> '//upload.wikimedia.org/wikipedia/commons/d/dc/Wikipedia-logo-v2-bar.png', // bug 40285
	'bat_smgwiki'		=> '$stdlogo',
	'bclwiki'		=> '$stdlogo',
	'bdwikimedia'		=> '//upload.wikimedia.org/wikipedia/commons/3/3d/Wikimedia_Bangladesh_site_logo.png',
	'bewiki'		=> '$stdlogo',
	'bewikimedia'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/4/40/Wikimedia_Belgium_logo.png/135px-Wikimedia_Belgium_logo.png',
	'bewikisource'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/b/b1/Wiksource-logo-be.png/135px-Wiksource-logo-be.png',
	'bewiktionary'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/5/54/Wiktionary-logo-be.png/135px-Wiktionary-logo-be.png', // bug 43240
	'be_x_oldwiki'		=> '//upload.wikimedia.org/wikipedia/commons/0/08/Wikipedia-logo-v2-be-x-old.png',
	'bgwiki'		=> '//upload.wikimedia.org/wikipedia/commons/c/cf/Wikipedia-logo-v2-bg.png',
	'bgwikibooks'		=> '$stdlogo',
	'bgwikiquote'		=> '$stdlogo',
	'bgwikisource'		=> '//upload.wikimedia.org/wikipedia/commons/d/d0/Wikisource-logo-bg.png',
	'bgwiktionary'		=> '$stdlogo',
	'bhwiki'		=> '$stdlogo',
	'bjnwiki'		=> '//upload.wikimedia.org/wikipedia/commons/1/14/Lambang_Wikipidia_Bahasa_Banjar.png',
	'bmwiki'		=> '//upload.wikimedia.org/wikipedia/commons/2/27/Wikipedia-logo-v2-bm.png', // bug 40285
	'bnwiki'		=> '//upload.wikimedia.org/wikipedia/commons/6/6f/Wikipedia-logo-v2-bn.png',
	'bnwikibooks'		=> '//upload.wikimedia.org/wikipedia/commons/7/70/Wikibooks-logo-bn.png',
	'bnwikisource'		=> '$stdlogo',
	'bnwiktionary'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/a/a9/Wiktionary-logo-bn.svg/135px-Wiktionary-logo-bn.svg.png', // bug 43240
	'boardwiki'		=> '//upload.wikimedia.org/wikipedia/meta/7/7d/Wikimediaboard-logo135px.png',
	'bowiki'		=> '$stdlogo',
	'bpywiki'		=> '//upload.wikimedia.org/wikipedia/commons/d/dd/Wikipedia-logo-v2-bpy.png', // bug 40285
	'brwiki'		=> '//upload.wikimedia.org/wikipedia/commons/1/15/Wikipedia-logo-v2-br.png',
	'brwikimedia'		=> '$stdlogo',
	'brwikisource'		=> '//upload.wikimedia.org/wikipedia/commons/9/97/Wikisource-logo-br.png',
	'brwiktionary'		=> '$stdlogo',
	'bswiki'		=> '//upload.wikimedia.org/wikipedia/commons/d/dd/Wikipedia-logo-v2-bs.png',
	'bswikinews'		=> '//upload.wikimedia.org/wikipedia/commons/b/b1/Bs_wikinews_logo.png',
	'bswikiquote'		=> '$stdlogo',
	'bswiktionary'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/f/ff/Wiktionary-logo-bs.png/135px-Wiktionary-logo-bs.png', // bug 43240
	'bugwiki'		=> '//upload.wikimedia.org/wikipedia/commons/5/59/Wikipedia-logo-v2-bug.png',
	'bxrwiki'		=> '$stdlogo',
	'cawiki'		=> '//upload.wikimedia.org/wikipedia/commons/3/34/Wikipedia-logo-v2-ca.png',
	'cawikibooks'		=> '$stdlogo',
	'cawikinews'		=> '//upload.wikimedia.org/wikipedia/commons/a/a4/Wikinews-logo-ca-small.png',
	'cawikiquote'		=> '$stdlogo',
	'cawikisource'		=> '$stdlogo',
	'cawiktionary'		=> '$stdlogo',
	'cbk_zamwiki'		=> '$stdlogo',
	'cdowiki'		=> '//upload.wikimedia.org/wikipedia/commons/0/08/Wikipedia-logo-v2-cdo.png', // bug 40285
	'cebwiki'		=> '//upload.wikimedia.org/wikipedia/commons/5/56/Wikipedia-logo-v2-ceb.png', // bug 40285
	'cewiki'		=> '$stdlogo',
	'chairwiki'		=> '//upload.wikimedia.org/wikipedia/meta/7/7d/Wikimediaboard-logo135px.png',
	'chapcomwiki'		=> '//upload.wikimedia.org/wikipedia/meta/1/1d/Wikimediachapcom-logo135px.png',
	'checkuserwiki'		=> '//upload.wikimedia.org/wikipedia/commons/1/10/Wikimedia_CheckUser_wiki_logo.png', // Bug 28785
	'chwiki'		=> '//upload.wikimedia.org/wikipedia/commons/b/bc/Wikipedia-logo-v2-ch.png', // bug 40285
	'chrwiki'		=> '//upload.wikimedia.org/wikipedia/commons/9/98/Wikipedia-logo-chr.png', // Bug 37327
	'ckbwiki'		=> '$stdlogo',
	'collabwiki'		=> '//upload.wikimedia.org/wikipedia/meta/5/5d/Collab.png',
	'cowiki'		=> '//upload.wikimedia.org/wikipedia/commons/6/6f/Wikipedia-logo-v2-co.png', // bug 40285
	'cowikimedia'		=> '//upload.wikimedia.org/wikipedia/commons/6/6b/Wikimedia-Colombia-logo.png',
	'cowiktionary'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/8/83/Wiktionary-logo-co.png/135px-Wiktionary-logo-co.png', // bug 43240
	'commonswiki'		=> '//upload.wikimedia.org/wikipedia/commons/7/79/Wiki-commons.png',
	'crhwiki'		=> '$stdlogo',
	'crwiki'		=> '//upload.wikimedia.org/wikipedia/commons/0/0a/Wikipedia-logo-cr.png',
	'csbwiki'		=> '//upload.wikimedia.org/wikipedia/commons/7/71/Wikipedia-logo-v2-csb.png', // bug 40285
	'csbwiktionary'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/0/00/Wiktionary-logo-csb.png/135px-Wiktionary-logo-csb.png', // bug 43240
	'cswiki'		=> '$stdlogo',
	'cswikibooks'		=> '$stdlogo',
	'cswikiquote'		=> '//upload.wikimedia.org/wikiquote/cs/b/bc/Wiki.png',
	'cswikisource'		=> '$stdlogo',
	'cswiktionary'		=> '$stdlogo',
	'cuwiki'		=> '$stdlogo',
	'cvwiki'		=> '$stdlogo',
	'cywiki'		=> '//upload.wikimedia.org/wikipedia/commons/1/13/Wikipedia-logo-v2-cy.png',
	'cywiktionary'		=> '$stdlogo',
	'dawiki'		=> '//upload.wikimedia.org/wikipedia/commons/3/3e/Wikipedia-logo-v2-da.png',
	'dawiktionary'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/8/82/Wiktionary-logo-da.png/135px-Wiktionary-logo-da.png', // bug 43240
	'de_labswikimedia'		=> '//upload.wikimedia.org/wikipedia/meta/7/79/Wikimedia_Labs_logo.png',
	'dewiki'		=> '//upload.wikimedia.org/wikipedia/commons/e/ec/Wikipedia-logo-v2-de.png',
	'dewikibooks'		=> '//upload.wikimedia.org/wikibooks/de/b/bc/Wiki.png',
	'dewikiquote'		=> '//upload.wikimedia.org/wikipedia/commons/1/1a/Wikiquote-logo-en.png',
	'dewikisource'		=> '$stdlogo',
	'dewiktionary'		=> '$stdlogo',
	'diqwiki'		=> '//upload.wikimedia.org/wikipedia/commons/e/e1/Wikipedia-logo-v2-diq.png',
	'diqwiktionary'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/6/69/Wiktionary-logo-diq.png/135px-Wiktionary-logo-diq.png', // bug 43240
	'dkwikimedia'		=> '//upload.wikimedia.org/wikimedia/dk/b/bc/Wiki.png',
	'donatewiki'		=> '//upload.wikimedia.org/wikipedia/foundation/9/9a/Wikimediafoundation-logo.png',
	'dsbwiki'		=> '//upload.wikimedia.org/wikipedia/commons/6/6b/Wikipedia-logo-v2-dsb.png',
	'dvwiki'		=> '//upload.wikimedia.org/wikipedia/commons/9/9c/Wikipedia-logo-v2-dv.png', // bug 40285
	'dzwiki'		=> '$stdlogo',
	'elwiki'		=> '//upload.wikimedia.org/wikipedia/commons/d/df/Wikipedia-logo-v2-el.png',
	'elwikibooks'		=> '$stdlogo',
	'elwikinews'		=> '//upload.wikimedia.org/wikipedia/commons/0/0e/WikiNews-Logo-el.png',
	'elwikiquote'		=> '$stdlogo',
	'elwikisource'		=> '$stdlogo',
	'elwiktionary'		=> '$stdlogo',
	'en_labswikimedia'		=> '//upload.wikimedia.org/wikipedia/meta/7/79/Wikimedia_Labs_logo.png',
	'enwiki'		=> '$stdlogo',
	'enwikibooks'		=> '//upload.wikimedia.org/wikibooks/en/b/bc/Wiki.png',
	'enwiktionary'		=> '$stdlogo',
	'eowiki'		=> '$stdlogo',
	'eowikibooks'		=> '$stdlogo',
	'eowikinews'		=> '//upload.wikimedia.org/wikipedia/commons/7/76/Wikinews-logo-eo.png',
	'eowikiquote'		=> '//upload.wikimedia.org/wikiquote/eo/b/bc/Wiki.png', // bug 43879
	'eowikisource'		=> '//upload.wikimedia.org/wikipedia/commons/5/54/Wikisource-logo-eo-small.png',
	'eowiktionary'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/2/2c/Wiktionary-logo-eo.png/135px-Wiktionary-logo-eo.png', // bug 43240
	'eswiki'		=> '//upload.wikimedia.org/wikipedia/commons/8/8c/Wikipedia-logo-v2-es.png',
	'eswikibooks'		=> '//upload.wikimedia.org/wikipedia/commons/0/05/Wikibooks-logo-es.png',
	'eswikinews'		=> '//upload.wikimedia.org/wikipedia/commons/e/ed/Wikinews-logo-es-peq.png',
	'eswiktionary'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/c/c3/WiktionaryEs.svg/135px-WiktionaryEs.svg.png',
	'etwiki'		=> '//upload.wikimedia.org/wikipedia/commons/4/41/Wikipedia-logo-v2-et.png',
	'etwikibooks'		=> '//upload.wikimedia.org/wikibooks/et/b/bc/Wiki.png',
	'etwikimedia'		=> '$stdlogo',
	'etwikiquote'		=> '//upload.wikimedia.org/wikiquote/et/b/bc/Wiki.png',
	'etwikisource'		=> '$stdlogo',
	'etwiktionary'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/3/34/Wiktionary-logo-et.png/135px-Wiktionary-logo-et.png', // bug 43240
	'etwiktionary'		=> '//upload.wikimedia.org/wiktionary/et/b/bc/Wiki.png',
	'euwiki'		=> '//upload.wikimedia.org/wikipedia/commons/0/01/Wikipedia-logo-v2-eu.png',
	'euwiktionary'		=> '$stdlogo',
	'execwiki'		=> '//upload.wikimedia.org/wikipedia/meta/d/d6/Wikimedia-execwiki-logo.png',
	'extwiki'		=> '$stdlogo',
	'fawiki'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/f/fb/Wikipedia-logo-v2-fa.svg/135px-Wikipedia-logo-v2-fa.svg.png',
	'fawikibooks'		=> '$stdlogo',
	'fawikinews'		=> '//upload.wikimedia.org/wikipedia/commons/3/36/Fawikinews.png',
	'fawikiquote'		=> '$stdlogo',
	'fawikisource'		=> '//upload.wikimedia.org/wikipedia/commons/1/11/Wikisource_fa.png',
	'fawiktionary'		=> '$stdlogo',
	'ffwiki'		=> '$stdlogo',
	'fiwiki'		=> '//upload.wikimedia.org/wikipedia/commons/7/7f/Wikipedia-logo-v2-fi.png',
	'fiwikibooks'		=> '$stdlogo',
	'fiwikimedia'		=> '$stdlogo',
	'fiwikisource'		=> '$stdlogo',
	'fiwiktionary'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/8/8b/Wiktionary-logo-fi.png/135px-Wiktionary-logo-fi.png', // bug 43240
	'fiu_vrowiki'		=> '$stdlogo',
	'flaggedrevs_labswikimedia'		=> '//upload.wikimedia.org/wikipedia/meta/7/79/Wikimedia_Labs_logo.png',
	'fowiki'		=> '//upload.wikimedia.org/wikipedia/commons/7/77/Wikipedia-logo-v2-fo.png', // bug 40285
	'fowiktionary'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/0/0d/Wiktionary-logo-fo.png/135px-Wiktionary-logo-fo.png', // bug 43240
	'foundationwiki'		=> '//upload.wikimedia.org/wikipedia/foundation/9/9a/Wikimediafoundation-logo.png',
	'frwiki'		=> '//upload.wikimedia.org/wikipedia/commons/5/5a/Wikipedia-logo-v2-fr.png',
	'frwikisource'		=> '$stdlogo',
	'frwiktionary'		=> '$stdlogo',
	'frpwiki'		=> '//upload.wikimedia.org/wikipedia/commons/a/ab/Wikipedia-logo-v2-frp.png',
	'frrwiki'		=> '$stdlogo',
	'furwiki'		=> '$stdlogo',
	'fywiki'		=> '//upload.wikimedia.org/wikipedia/commons/d/da/Wikipedia-logo-v2-fy.png', // bug 40285
	'fywikibooks'		=> '$stdlogo',
	'fywiktionary'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/f/fe/Wiktionary-logo-fy.png/135px-Wiktionary-logo-fy.png', // bug 43240
	'gagwiki'		=> '//upload.wikimedia.org/wikipedia/commons/4/4e/Wikipedia-logo-v2-gag.png',
	'gawiki'		=> '$stdlogo',
	'gawiktionary'		=> '$stdlogo',
	'ganwiki'		=> '//upload.wikimedia.org/wikipedia/commons/d/d5/Wikipedia-logo-gan.png',
	'gdwiki'		=> '$stdlogo',
	'gdwiktionary'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/6/6b/Wiktionary-logo-gd.png/135px-Wiktionary-logo-gd.png', // bug 43240
	'glkwiki'		=> '$stdlogo',
	'glwiki'		=> '//upload.wikimedia.org/wikipedia/gl/b/bc/Wiki.png',
	'glwiktionary'		=> '$stdlogo',
	'gnwiki'		=> '$stdlogo',
	'gotwiki'		=> '$stdlogo',
	'guwiki'		=> '$stdlogo',
	'guwikisource'		=> '//upload.wikimedia.org/wikipedia/commons/c/ca/Wikisource-gu-Rekha-large.png',
	'guwiktionary'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/f/f2/Wiktionary-logo-gu.png/135px-Wiktionary-logo-gu.png', // bug 43240
	'gvwiki'		=> '$stdlogo',
	'gvwiktionary'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/6/65/Wiktionary-logo-gv.png/135px-Wiktionary-logo-gv.png', // bug 43240
	'hakwiki'		=> '$stdlogo',
	'hawwiki'		=> '//upload.wikimedia.org/wikipedia/commons/d/d4/Wikipedia-logo-v2-haw.png', // bug 40285
	'hewiki'		=> '//upload.wikimedia.org/wikipedia/he/b/bc/Wiki.png',
	'hewikibooks'		=> '$stdlogo',
	'hewikiquote'		=> '$stdlogo',
	'hewikisource'		=> '$stdlogo',
	'hewiktionary'		=> '$stdlogo',
	'hifwiki'		=> '$stdlogo', # 27361
	'hiwiki'		=> '$stdlogo',
	'hiwiktionary'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/b/bc/Wiktionary-logo-hi.png/135px-Wiktionary-logo-hi.png', // bug 43240
	'hrwiki'		=> '$stdlogo',
	'hrwikibooks'		=> '$stdlogo',
	'hrwikiquote'		=> '$stdlogo',
	'hsbwiki'		=> '//upload.wikimedia.org/wikipedia/commons/e/e7/Wikipedia-logo-v2-hsb.png',
	'hsbwiktionary'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/d/db/Wiktionary-logo-hsb.png/135px-Wiktionary-logo-hsb.png', // bug 43240
	'htwiki'		=> '//upload.wikimedia.org/wikipedia/commons/4/49/Wikipedia-logo-v2-ht.png', // bug 40285
	'huwiki'		=> '//upload.wikimedia.org/wikipedia/commons/d/d7/Wikipedia-logo-v2-hu.png',
	'huwikibooks'		=> '$stdlogo',
	'huwikinews'		=> '//upload.wikimedia.org/wikipedia/commons/6/6d/Wikinews-logo-hu-135px.png',
	'huwikiquote'		=> '$stdlogo',
	'huwikisource'		=> '$stdlogo',
	'huwiktionary'		=> '$stdlogo',
	'hywiki'		=> '$stdlogo',
	'hywikiquote'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/d/d1/Wikiquote-logo-hy.svg/150px-Wikiquote-logo-hy.svg.png',
	'hywikisource'		=> '$stdlogo',
	'hywiktionary'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/d/db/Wiktionary-logo-hy.png/135px-Wiktionary-logo-hy.png', // bug 43240
	'iawiki'		=> '//upload.wikimedia.org/wikipedia/commons/6/68/Wikipedia-logo-v2-ia.png',
	'iawiktionary'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/c/cc/Wiktionary-logo-ia.png/135px-Wiktionary-logo-ia.png', // bug 43240
	'idwiki'		=> '//upload.wikimedia.org/wikipedia/id/f/f3/Wikipedia-logo-v2-id.png',
	'idwikibooks'		=> '//upload.wikimedia.org/wikibooks/id/3/34/Wikibooks-logo-id.png',
	'idwiktionary'		=> '$stdlogo',
	'iewiktionary'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/b/bd/Wiktionary-logo-ie.png/135px-Wiktionary-logo-ie.png', // bug 43240
	'igwiki'		=> '$stdlogo',
	'ikwiktionary'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/7/78/Wiktionary-logo-ik.png/135px-Wiktionary-logo-ik.png', // bug 43240
	'ilwikimedia'		=> '$stdlogo',
	'ilowiki'		=> '//upload.wikimedia.org/wikipedia/commons/5/57/Wikipedia-logo-v2-ilo.png', // bug 40285
	'incubatorwiki'		=> '$stdlogo',
	'internalwiki'		=> '//upload.wikimedia.org/wikipedia/meta/a/a2/Wikimediainernal-logo135px.png',
	'iowiki'		=> '$stdlogo',
	'iowiktionary'		=> '$stdlogo',
	'iswiki'		=> '//upload.wikimedia.org/wikipedia/commons/c/c4/Wikipedia-logo-v2-is.png', // bug 40285
	'iswikibooks'		=> '$stdlogo',
	'iswikiquote'		=> '$stdlogo',
	'iswikisource'		=> '$stdlogo',
	'iswiktionary'		=> '$stdlogo',
	'itwiki'		=> '//upload.wikimedia.org/wikipedia/commons/c/c5/Wikipedia-logo-v2-it.png',
	'itwikibooks'		=> '$stdlogo',
	'itwiktionary'		=> '$stdlogo',
	'iuwiki'		=> '//upload.wikimedia.org/wikipedia/commons/e/e4/Wikipedia-logo-v2-iu.png', // bug 40285
	'jawiki'		=> '//upload.wikimedia.org/wikipedia/commons/a/ad/Wikipedia-logo-v2-ja.png',
	'jbowiki'		=> '$stdlogo',
	'jbowiktionary'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/7/7e/Wiktionary-logo-jbo.png/135px-Wiktionary-logo-jbo.png', // bug 43240
	'jvwiki'		=> '//upload.wikimedia.org/wikipedia/commons/c/cc/Wikipedia-logo-v2-jv.png', // bug 40285
	'jvwiktionary'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/1/1d/Wiktionary-logo-jv.png/135px-Wiktionary-logo-jv.png', // bug 43240
	'kaawiki'		=> '$stdlogo',
	'kabwiki'		=> '$stdlogo',
	'kawiki'		=> '//upload.wikimedia.org/wikipedia/commons/5/52/Wikipedia-logo-v2-ka.png',
	'kawikibooks'		=> '$stdlogo',
	'kawikiquote'		=> '$stdlogo',
	'kawiktionary'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/7/7e/Wiktionary-logo-ka_copy.png/135px-Wiktionary-logo-ka_copy.png',
	'kbdwiki'		=> '//upload.wikimedia.org/wikipedia/commons/b/b0/Wikipedia-logo-v2-kbd.png',
	'kkwiki'		=> '$stdlogo',
	'kkwiktionary'		=> '//upload.wikimedia.org/wiktionary/kk/b/bc/Wiki.png',
	'klwiki'		=> '$stdlogo',
	'kmwiki'		=> '$stdlogo',
	'kmwiktionary'		=> '$stdlogo',
	'knwiki'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/e/e1/Wikipedia-logo-v2-kn.svg/135px-Wikipedia-logo-v2-kn.svg.png', # bug 27657
	'knwiktionary'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/7/71/Wiktionary-logo-kn.svg/135px-Wiktionary-logo-kn.svg.png', # 29380
	'koiwiki'		=> '//upload.wikimedia.org/wikipedia/commons/b/b4/Wikipedia-logo-v2-koi.png',
	'kowiki'		=> '$stdlogo',
	'kowikinews'		=> '//upload.wikimedia.org/wikipedia/commons/3/37/Wikinews-logo-ko-135px.png',
	'kowikiquote'		=> '//upload.wikimedia.org/wikiquote/ko/b/bc/Wiki.png', # bug 27548
	'kowikisource'		=> '//upload.wikimedia.org/wikisource/ko/b/bc/Wiki.png', # bug 38543
	'kowikiversity'		=> '//upload.wikimedia.org/wikipedia/commons/b/b2/Wikiversity-logo-ko.svg',
	'kowiktionary'		=> '$stdlogo',
	'krcwiki'		=> '$stdlogo',
	'kshwiki'		=> '//upload.wikimedia.org/wikipedia/commons/7/75/Wikipedia-logo-v2-ksh.png',
	'kswiki'		=> '$stdlogo',
	'kuwiki'		=> '//upload.wikimedia.org/wikipedia/commons/e/e3/Wikipedia-logo-v2-ku.png',
	'kuwikiquote'		=> '$stdlogo',
	'kuwiktionary'		=> '$stdlogo',
	'kvwiki'		=> '//upload.wikimedia.org/wikipedia/commons/a/ae/Wikipedia-logo-v2-kv.png', // bug 40285
	'kwwiki'		=> '$stdlogo',
	'kwwiktionary'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/9/9d/Wiktionary-logo-kw.png/135px-Wiktionary-logo-kw.png', // bug 43240
	'kywiki'		=> '$stdlogo',
	'kywiktionary'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/d/d2/Wiktionary-logo-ky.png/135px-Wiktionary-logo-ky.png', // bug 43240
	'ladwiki'		=> '$stdlogo',
	'lawiki'		=> '$stdlogo',
	'lawikisource'		=> '$stdlogo',
	'lawiktionary'		=> '$stdlogo',
	'lbewiki'		=> '//upload.wikimedia.org/wikipedia/commons/9/95/Wikipedia-logo-v2-lbe.png', // bug 40285
	'lbwiki'		=> '//upload.wikimedia.org/wikipedia/commons/9/9f/Wikipedia-logo-v2-lb.png',
	'lbwiktionary'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/f/fe/Wiktionary-logo-lb.png/135px-Wiktionary-logo-lb.png',
	'lezwiki'		=> '//upload.wikimedia.org/wikipedia/commons/3/3d/Wikipedia-logo-v2-lez.png',
	'liwiki'		=> '$stdlogo',
	'liwiktionary'		=> '$stdlogo',
	'liwiktionary'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/f/f6/Wiktionary-logo-li.png/135px-Wiktionary-logo-li.png', // bug 43240
	'lmowiki'		=> '//upload.wikimedia.org/wikipedia/commons/f/ff/Wikipedia-logo-v2-lmo.png', // bug 40285
	'lnwiki'		=> '//upload.wikimedia.org/wikipedia/ln/b/bc/Wiki.png',
	'wikidatawiki'		=> '//upload.wikimedia.org/wikipedia/commons/e/e4/Wikidata-logo-en-135px.png',
	'wikimaniawiki'		=> '//upload.wikimedia.org/wikipedia/wikimania/b/bc/Wiki.png', // back compat
	'lowiki'		=> '$stdlogo',
	'ltgwiki'		=> '//upload.wikimedia.org/wikipedia/commons/7/77/Wikipedia-logo-v2-ltg.png',
	'ltwiki'		=> '$stdlogo',
	'ltwikibooks'		=> '//upload.wikimedia.org/wikibooks/lt/2/20/Wikibooks-logo-lt.png',
	'ltwiktionary'		=> '$stdlogo',
	'lvwiki'		=> '$stdlogo',
	'lvwiktionary'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/8/8f/Wiktionary-logo-lv.png/135px-Wiktionary-logo-lv.png', // bug 43240
	'map_bmswiki'		=> '$stdlogo',
	'mdfwiki'		=> '$stdlogo',
	'mediawikiwiki'		=> '$stdlogo',
	'metawiki'		=> '$stdlogo',
	'mgwiki'		=> '$stdlogo',
	'mgwiktionary'		=> '$stdlogo',
	'mhrwiki'		=> '$stdlogo',
	'minwiki'		=> '//upload.wikimedia.org/wikipedia/commons/f/f2/Wikipedia-logo-v2-min.svg',
	'miwiki'		=> '//upload.wikimedia.org/wikipedia/commons/b/b6/Wikipedia-logo-v2-mi.png', // bug 40285
	'miwiktionary'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/1/10/Wiktionary-logo-mi.png/135px-Wiktionary-logo-mi.png', // bug 43240
	'mkwiki'		=> '//upload.wikimedia.org/wikipedia/commons/a/a6/Wikipedia-logo-v2-mk.png',
	'mkwikimedia'		=> '//upload.wikimedia.org/wikipedia/meta/3/38/Wikimedia_Macedonia_logo_small_mk.png',
	'mkwikisource'		=> '$stdlogo',
	'mlwiki'		=> '//upload.wikimedia.org/wikipedia/commons/e/e2/Wikipedia-logo-v2-ml.png',
	'mlwikibooks'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/e/eb/Wikibooks-logo-ml.svg/135px-Wikibooks-logo-ml.svg.png', // Bug 43111
	'mlwikiquote'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/a/a7/Wikiquote-logo-ml.svg/135px-Wikiquote-logo-ml.svg.png', // Bug 42771
	'mlwikisource'		=> '$stdlogo',
	'mlwiktionary'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/a/a9/Wiktionary-logo-ml.svg/135px-Wiktionary-logo-ml.svg.png', // Bug 41785
	'mnwiki'		=> '$stdlogo',
	'mnwiktionary'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/1/10/Wiktionary-logo-mn.png/135px-Wiktionary-logo-mn.png', // bug 43240
	'mrjwiki'		=> '//upload.wikimedia.org/wikipedia/commons/b/b6/Wikipedia-logo-v2-mrj.png',
	'mrwiki'		=> '$stdlogo',
	'mrwiktionary'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Wiktionary-logo-mr.png/135px-Wiktionary-logo-mr.png', // bug 43240
	'mswiki'		=> '//upload.wikimedia.org/wikipedia/commons/e/ee/Wikipedia-logo-v2-ms.png',
	'mswikibooks'		=> '$stdlogo',
	'mswiktionary'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/8/88/Wiktionary-logo-ms.png/135px-Wiktionary-logo-ms.png', // bug 43240
	'mtwiki'		=> '//upload.wikimedia.org/wikipedia/commons/9/9b/Wikipedia-logo-v2-mt.png',
	'mtwiktionary'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/f/f7/Wiktionary-logo-mt.png/135px-Wiktionary-logo-mt.png', // bug 43240
	'mwlwiki'		=> '//upload.wikimedia.org/wikipedia/commons/1/10/Wikipedia-logo-v2-mwl.png', // bug 40285
	'mxwikimedia'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/f/fa/Wikimedia_Mexico.svg/135px-Wikimedia_Mexico.svg.png',
	'mywiki'		=> '$stdlogo',
	'mywiktionary'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/9/96/Wiktionary-logo-my.svg/135px-Wiktionary-logo-my.svg.png', // bug 43240
	'myvwiki'		=> '$stdlogo',
	'mznwiki'		=> '$stdlogo',
	'nahwiki'		=> '$stdlogo',
	'nawiki'		=> '//upload.wikimedia.org/wikipedia/commons/4/46/Wikipedia-logo-v2-na.png', // bug 40285
	'napwiki'		=> '//upload.wikimedia.org/wikipedia/commons/c/cb/Wikipedia-logo-v2-nap.png', // bug 40285
	'ndswiki'		=> '//upload.wikimedia.org/wikipedia/commons/9/95/Wikipedia-logo-nds.png',
	'ndswiktionary'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/6/65/WiktionaryNds.svg/135px-WiktionaryNds.svg.png',
	'nds_nlwiki'		=> '$stdlogo',
	'newiki'		=> '$stdlogo',
	'newiktionary'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/4/4e/Wiktionary-logo-ne.png/135px-Wiktionary-logo-ne.png', // bug 43240
	'newwiki'		=> '//upload.wikimedia.org/wikipedia/commons/1/18/Wikipedia-logo-v2-new.png', // bug 40285
	'nlwiki'		=> '//upload.wikimedia.org/wikipedia/commons/a/ae/Wikipedia-logo-v2-nl.png',
	'nlwikimedia'		=> '$stdlogo',
	'nlwikiquote'		=> '$stdlogo',
	'nlwiktionary'		=> '//upload.wikimedia.org/wiktionary/nl/2/26/WiktNL1.png',
	'nnwiki'		=> '//upload.wikimedia.org/wikipedia/commons/4/41/Wikipedia-logo-v2-nn.png',
	'nnwikiquote'		=> '//upload.wikimedia.org/wikiquote/nn/b/bc/Wiki.png', # bug 27555
	'nnwiktionary'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/0/09/Wiktionary-logo-nn.png/135px-Wiktionary-logo-nn.png', // bug 43240
	'noboard_chapterswikimedia'		=> '$stdlogo',
	'nowiki'		=> '$stdlogo',
	'nowikibooks'		=> '$stdlogo',
	'nowikimedia'		=> '$stdlogo',
	'nowiktionary'		=> '$stdlogo',
	'nostalgiawiki'		=> '//upload.wikimedia.org/wikipedia/meta/3/32/Wiki_orig_logo.png',
	'novwiki'		=> '$stdlogo',
	'nsowiki'		=> '//upload.wikimedia.org/wikipedia/commons/0/00/Wikipedia-logo-v2-tn.png',
	'nvwiki'		=> '//upload.wikimedia.org/wikipedia/commons/3/3e/Wikipedia-logo-v2-nv.png', // bug 40285
	'nycwikimedia'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/7/70/Wikimedia_New_York_City_logo.svg/135px-Wikimedia_New_York_City_logo.svg.png',
	'nzwikimedia'		=> '$stdlogo',
	'ocwiki'		=> '//upload.wikimedia.org/wikipedia/commons/7/74/Wikipedia-logo-v2-oc.png',
	'ocwikibooks'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/e/e3/Wikibooks-logo-oc.png/135px-Wikibooks-logo-oc.png',
	'ocwiktionary'		=> '$stdlogo',
	'officewiki'		=> '//upload.wikimedia.org/wikipedia/meta/e/ed/WikimediaOFFICE-logo135px.png',
	'orwiki'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/9/98/Wikipedia-logo-v2-or.svg/132px-Wikipedia-logo-v2-or.svg.png', # bug 27704
	'orwiktionary'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/1/14/Wiktionary-logo-or.svg/135px-Wiktionary-logo-or.svg.png',
	'oswiki'		=> '$stdlogo',
	'otrs_wikiwiki'		=> '//upload.wikimedia.org/wikipedia/commons/7/7c/Wiki-otrs.png',
	'outreachwiki'		=> '//upload.wikimedia.org/wikipedia/commons/9/9f/Wikimedia_Outreach.png',
	'pawiki'		=> '$stdlogo',
	'pawiktionary'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/a/ac/Wiktionary-logo-pa.png/135px-Wiktionary-logo-pa.png', // bug 43240
	'pamwiki'		=> '//upload.wikimedia.org/wikipedia/commons/7/7d/Wikipedia-logo-v2-pam.png', // bug 40285
	'pa_uswikimedia'		=> '//upload.wikimedia.org/wikimedia/pa-us/b/bc/Wiki.png',
	'pcdwiki'		=> '$stdlogo',
	'pdcwiki'		=> '//upload.wikimedia.org/wikipedia/commons/5/55/Wikipedia-logo-v2-pdc.png',
	'pflwiki'		=> '//upload.wikimedia.org/wikipedia/commons/5/50/Wikipedia-logo-v2-pfl.png',
	'pihwiki'		=> '//upload.wikimedia.org/wikipedia/commons/7/72/Wikipedia-logo-v2-pih.png', // bug 40285
	'piwiki'		=> '//upload.wikimedia.org/wikipedia/commons/9/91/Wikipedia-logo-v2-pi.png',
	'plwiki'		=> '//upload.wikimedia.org/wikipedia/commons/a/ad/Wikipedia-logo-v2-pl.png',
	'plwikimedia'		=> '$stdlogo',
	'plwikiquote'		=> '$stdlogo',
	'plwikisource'		=> '$stdlogo',
	'plwikivoyage'		=> '//upload.wikimedia.org/wikipedia/commons/3/39/Wikivoyage-logo-pl.png',
	'plwiktionary'		=> '$stdlogo',
	'pmswiki'		=> '//upload.wikimedia.org/wikipedia/commons/b/b8/Wikipedia-logo-v2-pms.png',
	'pnbwiki'		=> '$stdlogo',
	'pnbwiktionary'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/e/ec/Wiktionary-logo.svg/135px-Wiktionary-logo.svg.png',
	'pntwiki'		=> '//upload.wikimedia.org/wikipedia/pnt/b/bc/Wiki.png',
	'pswiki'		=> '$stdlogo',
	'pswiktionary'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/4/4c/Wiktionary-logo-ps.png/135px-Wiktionary-logo-ps.png', // bug 43240
	'ptwiki'		=> '//upload.wikimedia.org/wikipedia/commons/c/c3/Wikipedia-logo-v2-pt.png',
	'ptwikibooks'		=> '$stdlogo',
	'ptwikinews'		=> '//upload.wikimedia.org/wikipedia/commons/f/fa/Imagem-Wiki.png',
	'ptwiktionary'		=> '$stdlogo',
	'qualitywiki'		=> '$stdlogo',
	'quwiki'		=> '$stdlogo',
	'quwiktionary'		=> '$stdlogo',
	'readerfeedback_labswikimedia'		=> '//upload.wikimedia.org/wikipedia/meta/7/79/Wikimedia_Labs_logo.png',
	'rmwiki'		=> '$stdlogo',
	'rmywiki'		=> '$stdlogo',
	'roa_rupwiki'		=> '//upload.wikimedia.org/wikipedia/commons/d/d9/Wikipedia-logo-v2-roa-rup.png', // bug 40285
	'roa_rupwiktionary'		=> '//upload.wikimedia.org/wikipedia/commons/d/df/Wiktionary-logo-roa-rup.png/135px-Wiktionary-logo-roa-rup.png', // bug 43240
	'roa_tarawiki'		=> '//upload.wikimedia.org/wikipedia/commons/3/3b/Wikipedia-logo-v2-roa-tara.png',
	'rowiki'		=> '$stdlogo',
	'rowikibooks'		=> '$stdlogo',
	'rowikinews'		=> '//upload.wikimedia.org/wikipedia/commons/1/12/Wikinews-logo-ro.png',
	'rowikiquote'		=> '$stdlogo',
	'rowiktionary'		=> '$stdlogo',
	'rswikimedia'		=> '$stdlogo',
	'ruewiki'		=> '//upload.wikimedia.org/wikipedia/commons/e/e0/Wikipedia-logo-v2-rue.png',
	'ruwiki'		=> '//upload.wikimedia.org/wikipedia/commons/f/f6/Wikipedia-logo-v2-ru.png',
	'ruwikibooks'		=> '$stdlogo',
	'ruwikimedia'		=> '$stdlogo',
	'ruwikiquote'		=> '$stdlogo',
	'ruwikisource'		=> '$stdlogo',
	'ruwikiversity'		=> '//upload.wikimedia.org/wikiversity/ru/b/bc/Wiki.png',
	'ruwiktionary'		=> '$stdlogo',
	'ru_sibwiki'		=> '$stdlogo',
	'sahwiki'		=> '//upload.wikimedia.org/wikipedia/commons/d/d0/Wikipedia-logo-v2-sah.png',
	'sahwikisource'		=> '//upload.wikimedia.org/wikipedia/commons/3/3d/Wikisource-logo-sah.png',
	'sawiki'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/2/2b/Wikipedia-logo-v2-sa.svg/135px-Wikipedia-logo-v2-sa.svg.png', # bug 27661
	'sawikiquote'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/e/e3/Sanskrit_Wikiquote_logo.svg/135px-Sanskrit_Wikiquote_logo.svg.png', // bug 43812
	'sawikisource'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/c/c3/Wikisource-logo-sa.svg/135px-Wikisource-logo-sa.svg.png',
	'sawiktionary'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/6/6e/Wiktionary-logo-sa.png/135px-Wiktionary-logo-sa.png', // bug 43240
	'scwiki'		=> '$stdlogo',
	'scnwiki'		=> '//upload.wikimedia.org/wikipedia/commons/7/77/Wikipedia-logo-v2-scn.png', // bug 40285
	'scnwiktionary'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/e/ee/Wiktionary-logo-scn.png/135px-Wiktionary-logo-scn.png', // bug 43240
	'scowiki'		=> '$stdlogo',
	'sdwiki'		=> '$stdlogo',
	'sdwikinews'		=> '//upload.wikimedia.org/wikipedia/commons/4/48/Sd_wikinews_logo.png',
	'sdwiktionary'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/3/39/Wiktionary-logo-sd.png/135px-Wiktionary-logo-sd.png', // bug 43240
	'searchcomwiki'		=> '//upload.wikimedia.org/wikipedia/meta/a/a2/Wikimediainernal-logo135px.png',
	'sewiki'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/1/1a/Wikipedia-logo-v2-se-example-4.svg/135px-Wikipedia-logo-v2-se-example-4.svg.png',
	'sewikimedia'		=> '$stdlogo',
	'sgwiki'		=> '//upload.wikimedia.org/wikipedia/commons/f/f9/Wiki-logo-sg.png',
	'sgwiktionary'		=> '//upload.wikimedia.org/wiktionary/sg/b/bc/Wiki.png',
	'shwiki'		=> '$stdlogo',
	'shwiktionary'		=> '$stdlogo',
	'siwiki'		=> '$stdlogo',
	'siwikibooks'		=> '//upload.wikimedia.org/wikipedia/commons/1/1c/Wikibooks-logo-si.png',
	'siwiktionary'		=> '//upload.wikimedia.org/wikipedia/commons/f/ff/WiktionarySi.png',
	'simplewiki'		=> '$stdlogo',
	'simplewiktionary'		=> '$stdlogo',
	'skwiki'		=> '//upload.wikimedia.org/wikipedia/commons/c/cb/Wikipedia-logo-v2-sk.png',
	'skwikiquote'		=> '$stdlogo',
	'skwiktionary'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Wiktionary-logo-sk.png/135px-Wiktionary-logo-sk.png', // bug 43240
	'slwiki'		=> '//upload.wikimedia.org/wikipedia/commons/d/dc/Wikipedia-logo-v2-sl.png',
	'slwikibooks'		=> '$stdlogo',
	'slwikiquote'		=> '$stdlogo',
	'slwikisource'		=> '$stdlogo',
	'slwikiversity'		=> '//upload.wikimedia.org/wikipedia/commons/4/4f/Sl-wikiversity-org.png',
	'slwiktionary'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/c/c5/Wiktionary-logo-sl.png/135px-Wiktionary-logo-sl.png', // bug 43240
	'smwiki'		=> '$stdlogo',
	'sowiki'		=> '//upload.wikimedia.org/wikipedia/commons/b/bb/Wikipedia-logo-v2-so.png', // bug 40285
	'sourceswiki'		=> '$stdlogo',
	'spcomwiki'		=> '//upload.wikimedia.org/wikipedia/meta/9/93/Wikimediaspcom-logo135px.png',
	'specieswiki'		=> '//upload.wikimedia.org/wikipedia/commons/b/b2/Wikispecies-logo-en.png',
	'sqwiki'		=> '//upload.wikimedia.org/wikipedia/commons/7/76/Wikipedia-logo-v2-sq.png',
	'sqwikibooks'		=> '$stdlogo',
	'sqwikinews'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/a/a9/Wikinews-logo-sq.svg/135px-Wikinews-logo-sq.svg.png', # bug 28114
	'sqwikiquote'		=> '$stdlogo',
	'sqwiktionary'		=> '$stdlogo',
	'srwiki'		=> '$stdlogo',
	'srwikibooks'		=> '$stdlogo',
	'srwikinews'		=> '//upload.wikimedia.org/wikipedia/commons/e/e4/Wikinews-logo-sr22.png', // temp for event 2009-08-07, later replaced
	'srwikiquote'		=> '$stdlogo',
	'srwikisource'		=> '$stdlogo',
	'srwiktionary'		=> '$stdlogo',
	'stewardwiki'		=> '//upload.wikimedia.org/wikipedia/commons/9/96/Steward_wiki_logo_3.png', # bug 37700
	'stqwiki'		=> '//upload.wikimedia.org/wikipedia/commons/8/8f/Wikipedia-logo-v2-stq.png', // bug 40285
	'strategywiki'		=> '$stdlogo',
	'suwiki'		=> '//upload.wikimedia.org/wikipedia/commons/2/21/Wikipedia-logo-v2-su.png', // bug 40285
	'suwiktionary'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/6/6e/Wiktionary-logo-su.png/135px-Wiktionary-logo-su.png', // bug 43240
	'svwiki'		=> '//upload.wikimedia.org/wikipedia/commons/d/d9/Wikipedia-logo-v2-sv.png',
	'svwiktionary'		=> '//upload.wikimedia.org/wiktionary/sv/b/bc/Wiki.png',
	'swwiki'		=> '$stdlogo',
	'swwiktionary'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/c/c8/Wiktionary-logo-sw.png/135px-Wiktionary-logo-sw.png', // bug 43240
	'szlwiki'		=> '//upload.wikimedia.org/wikipedia/szl/b/bc/Wiki.png',
	'tawiki'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/8/80/Wikipedia-logo-v2-ta.svg/135px-Wikipedia-logo-v2-ta.svg.png', # 27826
	'tawikibooks'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/4/47/Tamil-wiki-books.png/135px-Tamil-wiki-books.png', # bug 31862
	'tawikinews'		=> '//upload.wikimedia.org/wikipedia/commons/c/cf/WikiNews-Logo-ta.png',
	'tawikiquote'		=> '//upload.wikimedia.org/wikiquote/ta/thumb/b/bc/Wiki.png/135px-Wiki.png',
	'tawikisource'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/8/80/Wikisource-logo-ta-new.svg/121px-Wikisource-logo-ta-new.svg.png',
	'tawiktionary'		=> '$stdlogo',
	'tewiki'		=> '//upload.wikimedia.org/wikipedia/commons/f/f2/Wikipedia-logo-v2-te.png',
	'tewiktionary'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/e/e0/Wiktionary-logo-te.png/135px-Wiktionary-logo-te.png', // bug 43240
	'tenwiki'		=> '$stdlogo',
	'testwiki'		=> '$stdlogo',
	'tetwiki'		=> '$stdlogo',
	'tgwiki'		=> '$stdlogo',
	'thwiki'		=> '//upload.wikimedia.org/wikipedia/commons/3/3b/Wikipedia-logo-v2-th.png',
	'thwikibooks'		=> '//upload.wikimedia.org/wikipedia/commons/9/9b/Wikibooks-logo-th.png',
	'thwikinews'		=> '//upload.wikimedia.org/wikipedia/commons/a/a1/Wikinews-logo-th-135px.png',
	'thwikiquote'		=> '//upload.wikimedia.org/wikipedia/commons/7/7c/Wikiquote-logo-th.png',
	'thwikisource'		=> '//upload.wikimedia.org/wikipedia/commons/9/97/Wikisource-logo-th.png',
	'thwiktionary'		=> '//upload.wikimedia.org/wikipedia/commons/8/84/Wiktionary-logo-th.png',
	'tkwiki'		=> '$stdlogo',
	'tkwiktionary'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Wiktionary-logo-tk.png/135px-Wiktionary-logo-tk.png', // bug 43240
	'tlwiki'		=> '//upload.wikimedia.org/wikipedia/commons/6/69/Wikipedia-logo-v2-tl.png',
	'tnwiki'		=> '//upload.wikimedia.org/wikipedia/commons/0/00/Wikipedia-logo-v2-tn.png', // bug 40285
	'towiki'		=> '//upload.wikimedia.org/wikipedia/$lang/9/96/Wiki-to.png',
	'tpiwiki'		=> '//upload.wikimedia.org/wikipedia/commons/3/37/Wikipedia-logo-v2-tpi.png', # bug 27240
	'trwiki'		=> '//upload.wikimedia.org/wikipedia/commons/6/66/Wikipedia-logo-v2-tr.png',
	'trwikibooks'		=> '$stdlogo',
	'trwikimedia'		=> '$stdlogo',
	'trwikiquote'		=> '$stdlogo',
	'trwikisource'		=> '$stdlogo',
	'trwiktionary'		=> '$stdlogo',
	'tswiki'		=> '//upload.wikimedia.org/wikipedia/commons/9/9a/Wikipedia-logo-v2-ts.svg',
	'ttwiki'		=> '$stdlogo',
	'ttwikibooks'		=> '$stdlogo',
	'ttwiktionary'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/5/5d/Wiktionary-logo-tt.png/135px-Wiktionary-logo-tt.png', // bug 43240
	'tywiki'		=> '$stdlogo',
	'uawikimedia'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/7/7b/Wikimedia-UA-logo.svg/135px-Wikimedia-UA-logo.svg.png',
	'udmwiki'		=> '$stdlogo',
	'ugwiki'		=> '$stdlogo',
	'ugwiktionary'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/3/3b/Wiktionary-logo-ug.png/135px-Wiktionary-logo-ug.png', // bug 43240
	'ukwiki'		=> '//upload.wikimedia.org/wikipedia/commons/a/a6/Wikipedia-logo-v2-uk.png',
	'ukwikibooks'		=> '$stdlogo',
	'ukwikimedia'		=> '$stdlogo',
	'ukwikiquote'		=> '//upload.wikimedia.org/wikipedia/commons/9/95/Wikiquote-logo-uk.png',
	'ukwiktionary'		=> '$stdlogo',
	'urwiki'		=> '$stdlogo',
	'urwiktionary'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/9/95/Wiktionary-logo-ur.png/135px-Wiktionary-logo-ur.png', // bug 43240
	'usabilitywiki'		=> '//upload.wikimedia.org/wikipedia/commons/1/1e/Wikimedia_Usability_Initiative_Logo.png',
	'uzwiki'		=> '//upload.wikimedia.org/wikipedia/commons/0/04/Wikipedia-logo-v2-uz.png', // Bug 37699
	'uzwiktionary'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/b/bc/Wiktionary-logo-uz.svg/135px-Wiktionary-logo-uz.svg.png', // bug 43240
	'vecwiki'		=> '$stdlogo',
	'vecwikisource'		=> '//upload.wikimedia.org/wikipedia/sources/b/bc/Wiki.png',
	'vewikimedia'		=> '$stdlogo',
	'vepwiki'		=> '//upload.wikimedia.org/wikipedia/commons/f/f7/Wikipedia-logo-v2-vep.png',
	'viwiki'		=> '//upload.wikimedia.org/wikipedia/commons/0/01/Wikipedia-logo-v2-vi.png',
	'viwikibooks'		=> '$stdlogo', // Bug 37661
	'viwikiquote'		=> '$stdlogo',
	'viwikisource'		=> '$stdlogo',
	'viwiktionary'		=> '$stdlogo',
	'viwiktionary'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/7/74/Wiktionary-logo-vi.svg/135px-Wiktionary-logo-vi.svg.png', // bug 43240
	'vlswiki'		=> '//upload.wikimedia.org/wikipedia/commons/2/25/Wikipedia-logo-v2-vls.png', // bug 40285
	'vowiki'		=> '/images/wiki-$lang.png',
	'vowiktionary'		=> '$stdlogo',
	'wawiki'		=> '//upload.wikimedia.org/wikipedia/commons/e/e6/Wikipedia-logo-v2-wa.png', // bug 40285
	'wawiktionary'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/e/e6/Wiktionary-logo-wa.png/135px-Wiktionary-logo-wa.png', // bug 43240
	'warwiki'		=> '//upload.wikimedia.org/wikipedia/commons/8/81/Wikipedia-logo-v2-war.png', // bug 40285
	'wg_enwiki'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/7/75/Wikimedia_Community_Logo.svg/120px-Wikimedia_Community_Logo.svg.png',
	'wikimania2005wiki'		=> '//upload.wikimedia.org/wikipedia/wikimania/b/bc/Wiki.png',
	'wikimania2006wiki'		=> '//upload.wikimedia.org/wikipedia/wikimania2006/c/c0/Mania-logo.gif',
	'wikimania2007wiki'		=> '//upload.wikimedia.org/wikipedia/wikimania2006/c/c0/Mania-logo.gif',
	'wikimania2008wiki'		=> '//upload.wikimedia.org/wikipedia/wikimania2006/c/c0/Mania-logo.gif',
	'wikimania2009wiki'		=> '//upload.wikimedia.org/wikipedia/wikimania2006/c/c0/Mania-logo.gif',
	'wikimania2010wiki'		=> '//upload.wikimedia.org/wikipedia/wikimania2006/c/c0/Mania-logo.gif',
	'wikimania2011wiki'		=> '//upload.wikimedia.org/wikipedia/wikimania2006/c/c0/Mania-logo.gif',
	'wikimania2012wiki'		=> '//upload.wikimedia.org/wikipedia/wikimania2006/c/c0/Mania-logo.gif',
	'wikimania2013wiki'		=> '//upload.wikimedia.org/wikipedia/wikimania2006/c/c0/Mania-logo.gif',
	'wikimaniateamwiki'		=> '//wikimaniateam.wikimedia.org/w/img_auth.php/b/bc/Wiki.png',
	'eswikivoyage'	=> '//upload.wikimedia.org/wikipedia/commons/thumb/3/3c/Wikivoyage-logo-es.svg/135px-Wikivoyage-logo-es.svg.png',
	'wowiki'		=> '//upload.wikimedia.org/wikipedia/commons/4/41/Wikipedia-logo-v2-wo.png', // bug 40285
	'wowiktionary'		=> '$stdlogo',
	'wuuwiki'		=> '$stdlogo',
	'xmfwiki'		=> '//upload.wikimedia.org/wikipedia/commons/thumb/a/a0/Wikipedia-logo-v2-xmf.svg/135px-Wikipedia-logo-v2-xmf.svg.png',
	'yiwiki'		=> '$stdlogo',
	'yiwikisource'		=> '$stdlogo',
	'yiwiktionary'		=> '$stdlogo',
	'yowiki'		=> '//upload.wikimedia.org/wikipedia/commons/a/a7/Wikipedia-logo-v2-yo.png',
	'zh_classicalwiki'		=> '$stdlogo',
	'zhwiki'		=> '$stdlogo',
	'zhwikibooks'		=> '$stdlogo',
	'zhwikiquote'		=> '$stdlogo',
	'zhwikisource'		=> '//upload.wikimedia.org/wikipedia/commons/4/4b/Wikisource-logo-zh.png',
	'zhwiktionary'		=> '$stdlogo',
	'zh_min_nanwiki'		=> '$stdlogo',
	'zh_min_nanwikisource'		=> '//upload.wikimedia.org/wikisource/zh-min-nan/b/bc/Wiki.png',
	'zh_min_nanwiktionary'		=> '//upload.wikimedia.org/wikipedia/commons/a/a3/Wiktionary-logo-zh-min-nan.png/135px-Wiktionary-logo-zh-min-nan.png', // bug 43240
	'zh_yuewiki'		=> '//upload.wikimedia.org/wikipedia/commons/8/89/Wikipedia-logo-v2-zh-yue.png',
),
# @} end of wgLogo

// Wikis which have uploading disabled, if you list a wiki as false here make sure to make an entry for $wgUploadNavigationUrl as well
# wgEnableUpload @{
'wgEnableUploads' => array(
	'default' => true,

	// projects
	'wikinews' => false,

	// wikis:
	'arwikinews' => true,
	'cswikinews' => true,
	'dewikinews' => true,
	'enwikinews' => true,
	'enwikiquote' => false, # http://en.wikiquote.org/wiki/Wikiquote_talk:Image_use_policy
	'eswiki' => false, # bug 6408
	'eswikibooks' => false, # Bug 18865
	'eswikiquote' => false, #  bug 9728
	'eswikivoyage' => false, // Bug 42933
	'euwiki' => false, # 28609
	'fawikinews'  => true,  # bug 26565
	'hewikinews' => true,
	'itwikinews' => true,
	'itwikiquote' => false, # bug 12012
	'jawikisource' => false, # bug 3572
	'jawiktionary'  => false, # bug 11775
	'kowikinews'	=> true, # 24877
	'nowikinews' => true,
	'ndswiki' => false, # http://mail.wikipedia.org/pipermail/wikitech-l/2005-October/032136.html
	'outreachwiki' => false,
	'plwikinews' => true,
	'plwikiquote' => false,
	'ptwikivoyage' => false, // Bug 42933
	'ptwiktionary'  => false, # bug 14193
	'ruwikiquote' => false,
	'specieswiki'	=> false,
	'svwiki' => false, # bug 11954
	'svwikiversity' => false, # bug 26037
	'trwikinews' => true, # bug 20215
	'vowiki' => false, # bug 13740
	'xmfwiki' => false,
	'zhwikinews' => false,

	'wikidatawiki' => false,
),
# @} end of wgEnableUpload

# wgUploadNavigationUrl @{
'wgUploadNavigationUrl' => array(
	'default' => false,

	// projects
	'wikinews' => '//commons.wikimedia.org/wiki/Special:Upload',

	// Use relative paths for same-wiki links so the SSL converter can tweak them correctly
	'incubatorwiki' => '/wiki/Special:MyLanguage/Incubator:Upload',

	'aswiki'	=> '/wiki/ৱিকিপিডিয়া:ফাইল_আপল’ড_বিশেষজ্ঞ',
	'bgwiki'	=> '/wiki/MediaWiki:Uploadtext', // Bug 14836
	'bnwiki'	=> '/wiki/উইকিপিডিয়া:আপলোড',
	'bswiki'	=> '/wiki/Wikipedia:Upload', // Bug 12044
	'cswiki'	=> '//commons.wikimedia.org/wiki/Special:UploadWizard?uselang=cs',
	'cswikibooks'   => '//commons.wikimedia.org/wiki/Special:UploadWizard?uselang=cs',
	'cswikinews'    => '//commons.wikimedia.org/wiki/Special:UploadWizard?uselang=cs',
	'cswikiquote'   => '//commons.wikimedia.org/wiki/Special:UploadWizard?uselang=cs',
	'cswikisource'  => '//commons.wikimedia.org/wiki/Special:UploadWizard?uselang=cs',
	'cswikiversity' => '//commons.wikimedia.org/wiki/Special:UploadWizard?uselang=cs',
	'cswiktionary'  => '//commons.wikimedia.org/wiki/Special:UploadWizard?uselang=cs',
	'dawiki'        => '//commons.wikimedia.org/wiki/Special:UploadWizard?uselang=da&campaign=dk', // Bug 37662
	'dewikisource'  => '//commons.wikimedia.org/wiki/Special:Upload?uselang=de',
	'enwiki'        => '/wiki/Wikipedia:File_Upload_Wizard', // Bug 42263
	'enwikibooks'   => '//commons.wikimedia.org/wiki/Commons:Upload',
	'enwikinews'    => false,
	'enwikiquote'   => '//commons.wikimedia.org/wiki/Special:Upload',
	'enwikivoyage'  => '//en.wikivoyage.org/wiki/Wikivoyage:Upload_file',
	'enwiktionary'  => '//commons.wikimedia.org/wiki/Special:Upload',
	'eswiki'        => '//commons.wikimedia.org/wiki/Special:UploadWizard?uselang=es',
	'eswikibooks'   => '//commons.wikimedia.org/wiki/Commons:Upload/es?uselang=es', // '/wiki/Commons:Upload/es',
	'eswikivoyage'  => '//commons.wikimedia.org/wiki/Special:UploadWizard?uselang=es', // Bug 42933
	'eswiktionary'  => '//commons.wikimedia.org/wiki/Special:Upload?uselang=es',
	'euwiki'	=> '//commons.wikimedia.org/wiki/Commons:Upload/eu?uselang=eu',
	'frwiki'	=> '/wiki/Aide:Importer_un_fichier',
	'hewikinews'    => false,
	'hiwiki'	=> '/wiki/विकिपीडिया:अपलोड',
	'hrwiki'	    => '/wiki/Wikipedija:Upload',
	'huwiktionary'  => '//commons.wikimedia.org/wiki/Commons:Upload/hu',
	'iswiktionary'  => '/wiki/Wikior%C3%B0ab%C3%B3k:Hla%C3%B0a_inn_skr%C3%A1',
	'itwikinews'    => false,
	'jawiki'	=> '/wiki/Wikipedia:%E3%83%95%E3%82%A1%E3%82%A4%E3%83%AB%E3%81%AE%E3%82%A2%E3%83%83%E3%83%97%E3%83%AD%E3%83%BC%E3%83%89',
	'jawikisource'  => '//commons.wikimedia.org/wiki/Special:Upload',
	'lawiki'	=> '//commons.wikimedia.org/wiki/Special:Upload?uselang=la', // Bug 13190
	'mlwiki'	    => '/wiki/വിക്കിപീഡിയ:അപ്‌ലോഡ്',
	'ndswiki'       => '//commons.wikimedia.org/wiki/Special:Upload',
	'nlwiki'	=> '//commons.wikimedia.org/wiki/Commons:Upload/nl?uselang=nl',
	'nlwikisource'   => '//commons.wikimedia.org/wiki/Commons:Upload/nl?uselang=nl',
	'nlwikiquote'   => '//commons.wikimedia.org/wiki/Commons:Upload/nl?uselang=nl',
	'nlwiktionary'  => '//commons.wikimedia.org/wiki/Commons:Upload/nl?uselang=nl',
	'plwiki'        => '//pl.wikipedia.org/wiki/Wikipedia:Prześlij_plik', // Bug 42579
	'plwikinews'    => false,
	'plwikiquote'   => '//commons.wikimedia.org/wiki/Special:Upload?uselang=pl',
	'ptwiki'	=> '/wiki/Wikipedia:Carregar_ficheiro',
	'ptwikibooks'   => '//commons.wikimedia.org/wiki/Commons:Upload/pt?uselang=pt',
	'ptwikivoyage'  => '//commons.wikimedia.org/wiki/Special:UploadWizard?uselang=pt', // Bug 42934
	'rowiki'	    => '/wiki/Wikipedia:Trimite_fi%C5%9Fier',
	'ruwikinews'    => '//commons.wikimedia.org/w/index.php?title=Special:Upload&uselang=ru',
	'specieswiki'   => '//commons.wikimedia.org/wiki/Special:Upload',
	'thwiki'	=> '/wiki/%E0%B8%A7%E0%B8%B4%E0%B8%81%E0%B8%B4%E0%B8%9E%E0%B8%B5%E0%B9%80%E0%B8%94%E0%B8%B5%E0%B8%A2:%E0%B8%AD%E0%B8%B1%E0%B8%9B%E0%B9%82%E0%B8%AB%E0%B8%A5%E0%B8%94',
	'zhwiki'	=> '/wiki/Project:%E4%B8%8A%E4%BC%A0',
	'zhwikinews'    => '//commons.wikimedia.org/wiki/Commons:Upload/zh-hans',
	'zh_yuewiki'    => '/wiki/Project:%E4%B8%8A%E8%BC%89',


),
# @} end of wgUploadNavigationUrl


'wmgMathFileBackend' => array(
	'default' => 'global-multiwrite'
),

'wmgMathPath' => array(
	'default' => '//upload.wikimedia.org/math',
),

'wmgUseScore' => array(
	'default' => false,
	'test2wiki' => true,
),

'wmgScoreFileBackend' => array(
	'default' => 'global-multiwrite'
),

'wmgScorePath' => array(
	'default' => '//upload.wikimedia.org/score'
),

'wgMiserMode' => array(
	'default' => true, // Slaves aren't fast enough to generate all special pages all the time.
),

'wgQueryCacheLimit' => array(
	'default' => 5000,
	'enwiki' => 1000, // safe to raise?
	'dewiki' => 2000, // safe to raise?
),

'wgArticlePath' => array(
	'default' => '/wiki/$1',
),

# wgServer @{
'wgServer' => array(
	'advisorywiki' => '//advisory.wikimedia.org',
	'arbcom_dewiki'    => '//arbcom.de.wikipedia.org',
	'arbcom_enwiki' => '//arbcom.en.wikipedia.org',
	'arbcom_fiwiki' => '//arbcom.fi.wikipedia.org',
	'arbcom_nlwiki'    => '//arbcom.nl.wikipedia.org',
	'arwikimedia'	=> '//ar.wikimedia.org',
	'auditcomwiki' => '//auditcom.wikimedia.org',
	'boardgovcomwiki' => '//boardgovcom.wikimedia.org',
	'boardwiki'    => '//board.wikimedia.org',
	'brwikimedia' => '//br.wikimedia.org',
	'chairwiki' => '//chair.wikimedia.org',
	'chapcomwiki' => '//chapcom.wikimedia.org',
	'checkuserwiki' => '//checkuser.wikimedia.org',
	'collabwiki' => '//collab.wikimedia.org',
	'commonswiki'   => '//commons.wikimedia.org',
	'default'       => '//$lang.wikipedia.org',
	'donatewiki'       => '//donate.wikimedia.org',
	'de_labswikimedia' => '//de.labs.wikimedia.org',
	'elwikinews' => '//el.wikinews.org',
	'en_labswikimedia' => '//en.labs.wikimedia.org',
	'execwiki' => '//exec.wikimedia.org',
	'fdcwiki' => '//fdc.wikimedia.org',
	'flaggedrevs_labswikimedia'  => '//flaggedrevs.labs.wikimedia.org',
	'foundationwiki' => '//wikimediafoundation.org',
	'grantswiki'    => '//grants.wikimedia.org',
	'incubatorwiki' => '//incubator.wikimedia.org',
	'internalwiki'     => '//internal.wikimedia.org',
	'liquidthreads_labswikimedia' => '//liquidthreads.labs.wikimedia.org',
	'mediawikiwiki' => '//www.mediawiki.org',
	'metawiki'      => '//meta.wikimedia.org',
	'movementroleswiki'    => '//movementroles.wikimedia.org',
	'mxwikimedia'	=> '//mx.wikimedia.org',
	'noboard_chapterswikimedia'   => '//noboard.chapters.wikimedia.org',
	'nomcom'    => '//nomcom.wikimedia.org',
	'nycwikimedia'  => '//nyc.wikimedia.org', // http://bugzilla.wikimedia.org/show_bug.cgi?id=29273
	'officewiki' => '//office.wikimedia.org',
	'otrs_wikiwiki' => '//otrs-wiki.wikimedia.org',
	'outreachwiki' => '//outreach.wikimedia.org',
	'pa_uswikimedia' => '//pa.us.wikimedia.org',
	'qualitywiki' => '//quality.wikimedia.org',
	'quotewiki'     => '//wikiquote.org',
	'readerfeedback_labswikimedia' => '//readerfeedback.labs.wikimedia.org',
	'searchcomwiki' => '//searchcom.wikimedia.org',
	'sourceswiki'   => '//wikisource.org',
	'spcomwiki' => '//spcom.wikimedia.org',
	'specieswiki' => '//species.wikimedia.org',
	'stewardwiki'       => '//steward.wikimedia.org',
	'strategywiki'  => '//strategy.wikimedia.org',
	'tenwiki' => '//ten.wikipedia.org',
	'testwiki' => '//test.wikipedia.org',
	'usabilitywiki' => '//usability.wikimedia.org',
	'vewikimedia' => '//ve.wikimedia.org',
	'wg_enwiki' => '//wg.en.wikipedia.org',
	'wikibooks'     => '//$lang.wikibooks.org',
	'wikidatawiki' => '//www.wikidata.org',
	'wikimania2005wiki' => '//wikimania2005.wikimedia.org',
	'wikimania2006wiki' => '//wikimania2006.wikimedia.org',
	'wikimania2007wiki' => '//wikimania2007.wikimedia.org',
	'wikimania2008wiki' => '//wikimania2008.wikimedia.org',
	'wikimania2009wiki' => '//wikimania2009.wikimedia.org',
	'wikimania2010wiki' => '//wikimania2010.wikimedia.org',
	'wikimania2011wiki' => '//wikimania2011.wikimedia.org',
	'wikimania2012wiki' => '//wikimania2012.wikimedia.org',
	'wikimania2013wiki' => '//wikimania2013.wikimedia.org',
	'wikimaniateamwiki' => '//wikimaniateam.wikimedia.org',
	'wikimaniawiki' => '//wikimania.wikimedia.org', // back compat
	'wikimedia'     => '//$lang.wikimedia.org',
	'wikinews'      => '//$lang.wikinews.org',
	'wikiquote'     => '//$lang.wikiquote.org',
	'wikisource'    => '//$lang.wikisource.org',
	'wikiversity'   => '//$lang.wikiversity.org',
	'wikivoyage'    => '//$lang.wikivoyage.org',
	'wiktionary'    => '//$lang.wiktionary.org',
),
# @} end of wgServer

// This is the same as wgServer but with a protocol, so if wgServer is //foo.com this must be http://foo.com
# wgCanonicalServer @{
'wgCanonicalServer' => array(
	'advisorywiki' => 'http://advisory.wikimedia.org',
	'arbcom_dewiki'    => 'http://arbcom.de.wikipedia.org',
	'arbcom_enwiki' => 'http://arbcom.en.wikipedia.org',
	'arbcom_fiwiki' => 'http://arbcom.fi.wikipedia.org',
	'arbcom_nlwiki'    => 'http://arbcom.nl.wikipedia.org',
	'arwikimedia'	=> 'http://ar.wikimedia.org',
	'auditcomwiki' => 'http://auditcom.wikimedia.org',
	'boardgovcomwiki' => 'http://boardgovcom.wikimedia.org',
	'boardwiki'    => 'http://board.wikimedia.org',
	'brwikimedia' => 'http://br.wikimedia.org',
	'chairwiki' => 'http://chair.wikimedia.org',
	'chapcomwiki' => 'http://chapcom.wikimedia.org',
	'checkuserwiki' => 'http://checkuser.wikimedia.org',
	'collabwiki' => 'http://collab.wikimedia.org',
	'commonswiki'   => 'http://commons.wikimedia.org',
	'default'       => 'http://$lang.wikipedia.org',
	'de_labswikimedia' => 'http://de.labs.wikimedia.org',
	'donatewiki'   => 'http://donate.wikimedia.org',
	'elwikinews' => 'http://el.wikinews.org',
	'en_labswikimedia' => 'http://en.labs.wikimedia.org',
	'execwiki' => 'http://exec.wikimedia.org',
	'fdcwiki' => 'http://fdc.wikimedia.org',
	'flaggedrevs_labswikimedia'  => 'http://flaggedrevs.labs.wikimedia.org',
	'foundationwiki' => 'http://wikimediafoundation.org',
	'grantswiki'    => 'http://grants.wikimedia.org',
	'incubatorwiki' => 'http://incubator.wikimedia.org',
	'internalwiki'     => 'http://internal.wikimedia.org',
	'liquidthreads_labswikimedia' => 'http://liquidthreads.labs.wikimedia.org',
	'mediawikiwiki' => 'http://www.mediawiki.org',
	'metawiki'      => 'http://meta.wikimedia.org',
	'movementroleswiki'    => 'http://movementroles.wikimedia.org',
	'mxwikimedia'	=> 'http://mx.wikimedia.org',
	'noboard_chapterswikimedia'   => 'http://noboard.chapters.wikimedia.org',
	'nomcom'    => 'http://nomcom.wikimedia.org',
	'nycwikimedia'  => 'http://nyc.wikimedia.org', // http://bugzilla.wikimedia.org/show_bug.cgi?id=29273
	'officewiki' => 'http://office.wikimedia.org',
	'otrs_wikiwiki' => 'http://otrs-wiki.wikimedia.org',
	'outreachwiki' => 'http://outreach.wikimedia.org',
	'pa_uswikimedia' => 'http://pa.us.wikimedia.org',
	'qualitywiki' => 'http://quality.wikimedia.org',
	'quotewiki'     => 'http://wikiquote.org',
	'readerfeedback_labswikimedia' => 'http://readerfeedback.labs.wikimedia.org',
	'searchcomwiki' => 'http://searchcom.wikimedia.org',
	'sourceswiki'   => 'http://wikisource.org',
	'spcomwiki' => 'http://spcom.wikimedia.org',
	'specieswiki' => 'http://species.wikimedia.org',
	'stewardwiki'       => 'http://steward.wikimedia.org',
	'strategywiki'  => 'http://strategy.wikimedia.org',
	'tenwiki' => 'http://ten.wikipedia.org',
	'testwiki' => 'http://test.wikipedia.org',
	'usabilitywiki' => 'http://usability.wikimedia.org',
	'uzwiki' => 'https://uz.wikipedia.org', // https://bugzilla.wikimedia.org/43466
	'vewikimedia' => 'http://ve.wikimedia.org',
	'wg_enwiki' => 'http://wg.en.wikipedia.org',
	'wikibooks'     => 'http://$lang.wikibooks.org',
	'wikidatawiki' => 'http://www.wikidata.org',
	'wikimania2005wiki' => 'http://wikimania2005.wikimedia.org',
	'wikimania2006wiki' => 'http://wikimania2006.wikimedia.org',
	'wikimania2007wiki' => 'http://wikimania2007.wikimedia.org',
	'wikimania2008wiki' => 'http://wikimania2008.wikimedia.org',
	'wikimania2009wiki' => 'http://wikimania2009.wikimedia.org',
	'wikimania2010wiki' => 'http://wikimania2010.wikimedia.org',
	'wikimania2011wiki' => 'http://wikimania2011.wikimedia.org',
	'wikimania2012wiki' => 'http://wikimania2012.wikimedia.org',
	'wikimania2013wiki' => 'http://wikimania2013.wikimedia.org',
	'wikimaniateamwiki' => 'http://wikimaniateam.wikimedia.org',
	'wikimaniawiki' => 'http://wikimania.wikimedia.org', // back compat
	'wikimedia'     => 'http://$lang.wikimedia.org',
	'wikinews'      => 'http://$lang.wikinews.org',
	'wikiquote'     => 'http://$lang.wikiquote.org',
	'wikisource'    => 'http://$lang.wikisource.org',
	'wikiversity'   => 'http://$lang.wikiversity.org',
	'wikivoyage'    => 'http://$lang.wikivoyage.org',
	'wiktionary'    => 'http://$lang.wiktionary.org',
),
# @} end of wgCanonicalServer

'wgEnableCanonicalServerLink' => array(
	'uzwiki' => true, // https://bugzilla.wikimedia.org/43466
),

# wgSitename @{
'wgSitename' => array(
	// Site defaults
	'default'       => 'Wikipedia',
	'wikibooks'     => 'Wikibooks',
	'wikiquote'     => 'Wikiquote',
	'wiktionary'    => 'Wiktionary',
	'wikiversity'   => 'Wikiversity',
	'wikinews'      => 'Wikinews',
	'wikisource'    => 'Wikisource',
	'wikivoyage'    => 'Wikivoyage',

	// Wikis, alphabetically by DB name
	'abwiki'	=> 'Авикипедиа',
	'advisorywiki' => 'Advisory Board',
	'angwikisource' => 'Wicifruma',
	'amwiki'	=> 'ውክፔዲያ',
	'arbcom_dewiki' => 'Arbitration Committee',
	'arbcom_enwiki' => 'Arbitration Committee',
	'arbcom_fiwiki' => 'Arbitration Committee',
	'arbcom_nlwiki' => 'Arbitration Committee',
	'arcwiki'		=> 'ܘܝܩܝܦܕܝܐ',
	'arwiki'	=> 'ويكيبيديا',
	'arwikibooks'   => 'ويكي_الكتب',
	'arwikimedia'	=> 'Wikimedia Argentina',
	'arwikinews'    => 'ويكي_الأخبار',
	'arwikiquote'   => 'ويكي_الاقتباس',
	'arwikisource'  => 'ويكي_مصدر',
	'arwikiversity' => 'ويكي الجامعة',
	'arwiktionary'  => 'ويكاموس',
	'arzwiki'		=> 'ويكيبيديا',
	'astwiki'       => 'Uiquipedia',
	'astwiktionary' => 'Uiccionariu',
	'aswiki'         => 'অসমীয়া ৱিকিপিডিয়া',
	'aswikisource'	=> 'ৱিকিউৎস', // Bug 43129
	'auditcomwiki'  => 'Audit Committee',
	'azwiki'	=> 'Vikipediya',
	'azwikiquote'   => 'Vikisitat',
	'azwikisource'  => 'VikiMənbə',
	'bawiki'        => 'Википедия', // Bug 41167
	'bdwikimedia'   => 'উইকিমিডিয়া বাংলাদেশ',
	'be_x_oldwiki'  => 'Вікіпэдыя',
	'bewiki'	=> 'Вікіпедыя',
	'bewikimedia'   => 'Wikimedia Belgium',
	'bewikisource'  => 'Вікікрыніцы',
	'bewiktionary'  => 'Вікіслоўнік',
	'bgwiki'	=> 'Уикипедия',
	'bgwikibooks'   => 'Уикикниги',
	'bgwikinews'    => 'Уикиновини',
	'bgwikiquote'   => 'Уикицитат',
	'bgwikisource'  => 'Уикиизточник',
	'bgwiktionary'  => 'Уикиречник',
	'bhwiki'		=> 'विकिपीडिया',
	'bjnwiki'		=> 'Wikipidia',
	'bnwiki'		=> 'উইকিপিডিয়া',
	'bnwikibooks'   => 'উইকিবই',
	'bnwikisource'	=> 'উইকিসংকলন',
	'bnwiktionary'  => 'উইকিঅভিধান',
	'boardgovcomwiki'	=> "Board Governance Committee",
	'boardwiki'     => 'Board',
	'bpywiki' => 'উইকিপিডিয়া',
	'brwikimedia'   => 'Wikimedia Brasil',
	'brwikiquote'   => 'Wikiarroud',
	'brwikisource'  => 'Wikimammenn',
	'brwiktionary'  => 'Wikeriadur',
	'bswiki'	=> 'Wikipedia',
	'bswikibooks'   => 'Wikiknjige',
	'bswikinews'    => 'Wikivijesti', // pre-emptive
	'bswikiquote'   => 'Wikicitati',
	'bswikisource'  => 'Wikiizvor', // pre-emptive
	'bswikisource'  => 'Wikizvor',
	'bswiktionary'  => 'Vikirječnik',
	'cawiki'	=> "Viquip\xc3\xa8dia",
	'cawikibooks'   => 'Viquillibres',
	'cawikinews' => 'Viquinotícies',
	'cawikiquote'  => 'Viquidites',
	'cawikisource'  => 'Viquitexts',
	'cawiktionary'  => 'Viccionari',
	'ckbwiki'       => 'ویکیپیدیا',
	'cewiki'		=> 'Википедийа',
	'chairwiki'     => 'Wikimedia Board Chair',
	'chapcomwiki'   => 'Chapcom', // ?
	'checkuserwiki'     => 'CheckUser Wiki',
	'chywiki'       => 'Tsétsêhéstâhese Wikipedia',
	'collabwiki'    => 'Collab',
	'commonswiki'   => 'Wikimedia Commons',
	'cowikimedia'   => 'Wikimedia Colombia',
	'crhwiki'	=> 'Vikipediya',
	'cswiki'	=> 'Wikipedie',
	'cswikibooks'   => 'Wikiknihy',
	'cswikinews'    => 'Wikizprávy',
	'cswikiquote'   => 'Wikicitáty',
	'cswikisource'  => 'Wikizdroje',
	'cswiktionary'  => 'Wikislovník',
	'cswikiversity' => 'Wikiverzita',
	'cuwiki'	=> 'Википєдїꙗ',
	'cvwiki'	=> 'Википеди',
	'cywiki'	=> 'Wicipedia',
	'cywikibooks'   => 'Wicilyfrau',
	'cywikisource'  => 'Wicitestun',
	'cywiktionary'  => 'Wiciadur',
	'dawikibooks'   => 'Wikibooks',
	'de_labswikimedia' => 'Wikimedia Labs',
	'dkwikimedia'   => 'Wikimedia Danmark',
	'donatewiki'    => 'Donate',
	'dsbwiki'       => 'Wikipedija',
	'elwiki'	=> 'Βικιπαίδεια',
	'elwikibooks'   => 'Βικιβιβλία',
	'elwikinews'    => 'Βικινέα',
	'elwikiquote'   => 'Βικιφθέγματα',
	'elwikisource'  => 'Βικιθήκη',
	'elwikiversity' => 'Βικιεπιστήμιο',
	'elwiktionary'	=> 'Βικιλεξικό',
	'en_labswikimedia' => 'Wikimedia Labs',
	'eowiki'	=> 'Vikipedio',
	'eowikibooks'   => 'Vikilibroj',
	'eowikinews'    => 'Vikinovaĵoj',
	'eowikiquote'   => 'Vikicitaro',
	'eowikisource'  => 'Vikifontaro',
	'eowiktionary'  => 'Vikivortaro',
	'eswikibooks'   => 'Wikilibros',
	'eswikinews'    => 'Wikinoticias',
	'eswikiversity' => 'Wikiversidad',
	'eswikivoyage'	=> 'Wikiviajes', // Bug 42933
	'eswiktionary'  => 'Wikcionario',
	'etwiki'	=> 'Vikipeedia',
	'etwikibooks'   => 'Vikiõpikud',
	'etwikimedia'   => 'Wikimedia Eesti',
	'etwikisource'  => 'Vikitekstid',
	'etwikiquote'   => 'Vikitsitaadid',
	'etwiktionary'  => 'Vikisõnastik',
	'execwiki'      => 'Wikimedia Executive',
	'extwiki'       => 'Güiquipeya',
	'fawiki'	=> 'ویکی‌پدیا',
	'fawikibooks'   => 'ویکی‌نسک',
	'fawikinews'	=> 'ویکی‌خبر',
	'fawikiquote'   => 'ویکی‌گفتاورد',
	'fawikisource'  => 'ویکی‌نبشته',
	'fawiktionary'  => 'ویکی‌واژه',
	'fdcwiki' => 'Wikimedia FDC',
	'fiwikibooks'   => 'Wikikirjasto',
	'fiwikimedia'   => 'Wikimedia Suomi',
	'fiwikinews'    => 'Wikiuutiset',
	'fiwikiquote'   => 'Wikisitaatit',
	'fiwikisource'  => 'Wikiaineisto',
	'fiwikiversity' => 'Wikiopisto',
	'fiwiktionary'  => 'Wikisanakirja',
	'foundationwiki' => 'Wikimedia Foundation',
	'fowikisource'  => 'Wikiheimild',
	'frpwiki'		=> 'Vouiquipèdia',
	'frwiki'	=> 'Wikipédia',
	'frwikiversity' => 'Wikiversité',
	'frwiktionary'  => 'Wiktionnaire',
	'furwiki'       => 'Vichipedie',
	'fywiki'	=> 'Wikipedy',
	'ganwiki'       => '維基百科',
	'gawiki'	=> 'Vicipéid',
	'gagwiki'	    => 'Vikipediya',
	'gawikibooks'   => 'Vicíleabhair',
	'gawikiquote'   => 'Vicísliocht',
	'gawiktionary'  => 'Vicífhoclóir',
	'gdwiki'	    => 'Uicipeid',
	'gnwiki'		=> 'Vikipetã',
	'grantswiki'    => 'Wikimedia Foundation Grants Discussion',
	'guwiki'	=> 'વિકિપીડિયા',
	'guwikisource'  => 'વિકિસ્રોત',
	'guwiktionary'  => 'વિક્શનરી', // Bug 40776
	'hewiki'	=> "ויקיפדיה",
	'hewikibooks'   => "ויקיספר",
	'hewikinews'    => 'ויקיחדשות',
	'hewikiquote'   => 'ויקיציטוט',
	'hewikisource'  => "ויקיטקסט",
	'hewiktionary'  => "ויקימילון",
	'hiwiki'	=> 'विकिपीडिया',
	'hiwiktionary'  => 'विक्षनरी',
	'hrwiki'	=> 'Wikipedija',
	'hrwikiquote'   => 'Wikicitat',
	'hrwikisource'  => 'Wikizvor',
	'hsbwiki'       => 'Wikipedija',
	'hsbwiktionary' => 'Wikisłownik', // Bug 41328
	'htwiki'	=> 'Wikipedya',
	'htwikisource'  => 'Wikisòrs',
	'huwiki'	=> 'Wikipédia',
	'huwikibooks'   => 'Wikikönyvek',
	'huwikinews'    => 'Wikihírek',
	'huwikiquote'   => 'Wikidézet',
	'huwikisource'  => 'Wikiforrás',
	'huwiktionary'  => 'Wikiszótár',
	'hywiki'	=> 'Վիքիպեդիա',
	'hywikibooks'   => 'Վիքիգրքեր',
	'hywikiquote'   => 'Վիքիքաղվածք',
	'hywikisource'	=> 'Վիքիդարան',
	'hywiktionary'  => 'Վիքիբառարան',
	'iawiktionary'	=> 'Wiktionario',
	'idwikibooks'   => 'Wikibuku',
	'ilwikimedia'   => 'ויקימדיה',
	'incubatorwiki' => 'Wikimedia Incubator',
	'internalwiki'  => 'Internal',
	'iowiki'	=> 'Wikipedio',
	'iowiktionary'  => 'Wikivortaro',
	'iswikibooks'	=> 'Wikibækur',
	'iswikiquote'   => 'Wikivitnun',
	'iswikisource'  => 'Wikiheimild',
	'iswiktionary'  => 'Wikiorðabók',
	'itwikibooks'   => 'Wikibooks',
	'itwikinews'    => 'Wikinotizie',
	'itwikiquote'   => 'Wikiquote',
	'itwikiversity' => 'Wikiversità',
	'itwiktionary'  => 'Wikizionario',
	'iuwiki'        => 'ᐅᐃᑭᐱᑎᐊ',
	'jawikinews'    => "ウィキニュース",
	'jawikiversity' => 'ウィキバーシティ',
	'kawiki'	=> 'ვიკიპედია',
	'kawikibooks'   => 'ვიკიწიგნები',
	'kawikiquote' => 'ვიკიციტატა',
	'kawiktionary'  => 'ვიქსიკონი',
	'kbdwiki'	=> 'Уикипедиэ',
	'kkwiki'	=> 'Уикипедия',
	'kkwikibooks'	=> 'Уикикітап',
	//'kkwikinews'    => 'Уикихабар',
	'kkwikiquote'   => 'Уикидәйек',
	//'kkwikisource'  => 'Уикиқайнар',
	'kkwiktionary'  => 'Уикисөздік',
	'kmwiki'	=> 'វិគីភីឌា',
	'knwiki'	=> 'ವಿಕಿಪೀಡಿಯ',
	'knwiktionary'  => 'ವಿಕ್ಷನರಿ',
	'koiwiki'	=> 'Википедия',
	'kowiki'	=> '위키백과',
	'kowikinews'    => '위키뉴스',
	'kowikibooks'	=> '위키책',
	'kowikiquote'	=> '위키인용집',
	'kowikisource'  => '위키문헌',
	'kowikiversity' => '위키배움터',
	'kowiktionary'  => '위키낱말사전',
	'krcwiki'	   => 'Википедия',
	'kuwiki'	=> 'Wîkîpediya',
	'lawiki'	=> 'Vicipaedia',
	'lawikibooks'   => 'Vicilibri',
	'lawikiquote'   => 'Vicicitatio',
	'lawiktionary'  => 'Victionarium',
	'ladwiki'       => 'Vikipedya',
	'lbwiktionary'  => 'Wiktionnaire',
	'lbewiki'	=> 'Википедия',
	'lezwiki'	=> 'Википедия',
	'liwikibooks'   => 'Wikibeuk',
	'liwikisource'  => 'Wikibrónne',
	'lowiki'	=> 'ວິກິພີເດຍ',
	'ltgwiki'	=> 'Vikipedeja',
	'ltwiki'	=> 'Vikipedija',
	'ltwikisource'  => 'Vikišaltiniai',
	'ltwiktionary'	=> 'Vikižodynas',
	'lvwiki'	=> 'Vikipēdija',
	'mdfwiki'       => 'Википедиесь',
	'mediawikiwiki' => 'MediaWiki',
	'metawiki'      => 'Meta',
	'mhrwiki'       => 'Википедий',
	'mkwiki'	=> 'Википедија',
	'mkwikimedia'   => 'Викимедија Македонија',
	'mlwiki'	=> 'വിക്കിപീഡിയ',
	'mlwikibooks'   => 'വിക്കിപാഠശാല',
	'mlwikiquote'   => 'വിക്കിചൊല്ലുകൾ',
	'mlwikisource'	=> 'വിക്കിഗ്രന്ഥശാല',
	'mlwiktionary' => 'വിക്കിനിഘണ്ടു',
	'movementroleswiki' => 'Movement Roles',
	'mnwiki'	=> 'Википедиа',
	'mrjwiki'		=> 'Википеди',
	'mrwiki'		=> 'विकिपीडिया',
	'mrwikisource'  => 'विकिस्रोत',
	'mtwiki'	=> 'Wikipedija',
	'mtwiktionary'  => 'Wikizzjunarju',
	'mwlwiki'		=> 'Biquipédia',
	'mxwikimedia'	=> 'Wikimedia México',
	'myvwiki'       => 'Википедиясь',
	'mznwiki'	=> 'ویکی‌پدیا',
	'nahwiki'       => 'Huiquipedia',
	'nds_nlwiki'    => 'Wikipedie',
	'newiki'		=> 'विकिपीडिया',
	'nlwiktionary'  => 'WikiWoordenboek',
	'noboard_chapterswikimedia'   => 'Wikimedia Norway Internal Board',
	'nowikibooks'   => 'Wikibøker',
	'nowikimedia' => 'Wikimedia Norge',
	'nowikinews'    => 'Wikinytt',
	'nowikisource'  => 'Wikikilden',
	'nycwikimedia'  => 'Wikimedia New York City', // http://bugzilla.wikimedia.org/show_bug.cgi?id=29273
	'ocwiki'	=> 'Wikipèdia', # http://bugzilla.wikimedia.org/show_bug.cgi?id=7123
	'ocwikibooks'	=> 'Wikilibres',
	'ocwiktionary'  => 'Wikiccionari',
	'officewiki' => 'Wikimedia Office',
	'orwiki' => 'ଉଇକିପିଡ଼ିଆ',
	'oswiki' 	    => 'Википеди',
	'otrs_wikiwiki' => 'OTRS Wiki',
	'outreachwiki' => 'Outreach Wiki',
	'pa_uswikimedia' => 'Wikimedia Pennsylvania',
	'pawiki'	=> 'ਵਿਕੀਪੀਡੀਆ',
	'plwikimedia'   => 'Wikimedia',
	'plwikiquote'   => 'Wikicytaty',
	'plwikisource'  => 'Wikiźródła',
	'plwikivoyage'  => 'Wikipodróże',
	'plwiktionary'  => 'Wikisłownik',
	'pnbwiktionary' => 'وکشنری',
	'pntwiki'       => 'Βικιπαίδεια',
	'pswiki'		=> 'ويکيپېډيا' ,
	'pswikibooks'	=> 'ويکيتابونه' ,
	//'pswikiquote'	=> 'ويکيوراشه' ,
	'pswiktionary'	=> 'ويکيسيند' ,
	'ptwiki'	=> 'Wikipédia',
	'ptwikibooks'   => 'Wikilivros',
	'ptwikinews'    => 'Wikinotícias',
	'ptwikiversity' => 'Wikiversidade',
	'ptwiktionary'  => 'Wikcionário',
	'qualitywiki' => 'Wikimedia Quality',
	'rmywiki'       => 'Vikipidiya',
	'rowikibooks'	=> 'Wikimanuale',
	'rowikinews'    => 'Wikiștiri',
	'rowikiquote'   => 'Wikicitat',
	'rowiktionary' => 'Wikționar',
	'rswikimedia' => 'Викимедија',
	'ruewiki'		=> 'Вікіпедія',
	'ruwiki'	=> 'Википедия',
	'ruwikibooks'   => 'Викиучебник',
	'ruwikimedia'   => 'Викимедиа',
	'ruwikinews'    => 'Викиновости',
	'ruwikiquote'   => 'Викицитатник',
	'ruwikisource'  => 'Викитека',
	'ruwikiversity' => 'Викиверситет',
	'ruwiktionary'  => 'Викисловарь',
	'sawiki'		=> 'विकिपीडिया',
	'sawikiquote'   => 'विकिसूक्तिः',
	'sahwiki'       => 'Бикипиэдьийэ',
	'sahwikisource' => 'Бикитиэкэ',
	'scnwiktionary' => 'Wikizziunariu',
	'searchcomwiki' => 'Search Committee',
	'siwiki'	=> 'විකිපීඩියා, නිදහස් විශ්වකෝෂය',
	'skwiki'	=> 'Wikipédia',
	'skwikiquote'   => 'Wikicitáty',
	'skwiktionary'  => 'Wikislovník',
	'slwiki'	=> 'Wikipedija',
	'slwikibooks'   => 'Wikiknjige',
	'slwikiquote'   => 'Wikinavedek',
	'slwikisource'  => 'Wikivir',
	'slwiktionary'  => 'Wikislovar',
	'slwikiversity' => 'Wikiverza',
	'sourceswiki'   => 'Wikisource',
	'spcomwiki'     => 'Spcom',
	'specieswiki'   => 'Wikispecies',
	'sqwikinews'	=> 'Wikilajme',
	'srwiki'	=> 'Википедија',
	'srwikibooks'   => 'Викикњиге',
	'srwikinews'    => 'Викивести',
	'srwikisource'  => 'Викизворник',
	'strategywiki'  => 'Strategic Planning',
	'stewardwiki'       => 'Steward Wiki',
	'tawiki'		=> 'விக்கிப்பீடியா',
	'tawikibooks' => 'விக்கிநூல்கள்',
	'tawikinews'	=> 'விக்கிசெய்தி',
	'tawiktionary'	=> 'விக்சனரி',
	'tawikiquote'   => 'விக்கிமேற்கோள்',
	'tewiki'	=> 'వికీపీడియా',
	'tawikisource'  => 'விக்கிமூலம்',
	'tewiktionary'  => 'విక్షనరీ',
	'tenwiki'       => 'Wikipedia 10',
	'tgwiki'	=> 'Википедиа',
	'thwiki'	=> 'วิกิพีเดีย',
	'thwikiquote'   => 'วิกิคำคม',
	'thwikisource'  => 'วิกิซอร์ซ',
	'tkwiki'	=> 'Wikipediýa',
	'tkwiktionary'	=> 'Wikisözlük',
	'trwiki'	=> 'Vikipedi',
	'trwikibooks'   => 'Vikikitap',
	'trwikimedia'   => 'Wikimedia Türkiye',
	'trwikinews'    => 'Vikihaber',
	'trwikiquote'   => 'Vikisöz',
	'trwikisource'  => 'Vikikaynak', // Bug 42721
	'trwiktionary'  => 'Vikisözlük',
	'uawikimedia'   => 'Вікімедіа_Україна',
	'ukwiki'	=> 'Вікіпедія',
	'ukwikibooks'	=> 'Вікіпідручник',
	'ukwikimedia'   => 'Wikimedia UK', # Andrew 2009-04-27 Private request of current board member
	'ukwikinews'    => 'ВікіНовини',
	'ukwikiquote'   => 'Вікіцитати',
	'ukwikisource'  => 'Вікіджерела',
	'ukwiktionary'  => 'Вікісловник',
	'urwiki'	=> 'ویکیپیڈیا', // Bug 42155
	'urwikibooks' => 'وکی کتب',
	'urwikiquote'   => 'وکی اقتباسات',
	'urwiktionary'  => 'وکی لغت',
	'usabilitywiki'     => 'Wikimedia Usability Initiative',
	'uzwiki'	=> 'Vikipediya',
	'uzwikibooks'	=> 'Vikikitob',
	'uzwikiquote'	=> 'Vikiiqtibos',
	//'uzwikisource'	=> 'Vikimanba',
	'uzwiktionary'	=> 'Vikilug‘at',
	'vepwiki'       => 'Vikipedii',
	'vewikimedia'   => 'Wikimedia Venezuela',
	'vowiki'	=> 'Vükiped',
	'vowikibooks'	=>	'Vükibuks',
	'vowiktionary'	=> 'Vükivödabuk',
	'wg_enwiki'     => 'Wikipedia Working Group',
	'wikidatawiki' => 'Wikidata',
	'wikimania2005wiki' => 'Wikimania',
	'wikimania2006wiki' => 'Wikimania',
	'wikimania2007wiki' => 'Wikimania',
	'wikimania2008wiki' => 'Wikimania',
	'wikimania2009wiki' => 'Wikimania',
	'wikimania2010wiki' => 'Wikimania',
	'wikimania2011wiki' => 'Wikimania',
	'wikimania2012wiki' => 'Wikimania',
	'wikimania2013wiki' => 'Wikimania',
	'wikimaniateamwiki' => 'WikimaniaTeam',
	'wikimaniawiki' => 'Wikimania', // back compat
	'wikimedia'     => 'Wikimedia',
	'xmfwiki'		=> 'ვიკიპედია',
	'yiwiki'	=> 'װיקיפּעדיע',
	'yiwikisource'  => 'װיקיביבליאָטעק',
	'yiwiktionary'  => 'װיקיװערטערבוך',
	'zh-min-nanwikisource' => 'Wiki Tô·-su-kóan',
	'zh_classicalwiki' => '維基大典',
	'zh_yuewiki'    => '維基百科',
),
# @} end of wgSitename

'wgMaxNameChars' => array(
	'default' => 64,
),

'wgSiteSupportPage' => array(
	'default' => '//wikimediafoundation.org/fundraising',
	'dewiki' => '//wikimediafoundation.org/wiki/Spenden',
	'frwiki' => '//wikimediafoundation.org/wiki/Faites_un_don',
	'frwikibooks' => '//wikimediafoundation.org/wiki/Faites_un_don',
	'frwiktionary' => '//wikimediafoundation.org/wiki/Faites_un_don',
),

'wgUploadPath' => array(
	'default' => '//upload.wikimedia.org/$site/$lang',
	'private' => '/w/img_auth.php',
	'wikimania2005wiki' => '//upload.wikimedia.org/wikipedia/wikimania', // back compat
	'commonswiki' => '//upload.wikimedia.org/wikipedia/commons',
	'metawiki' => '//upload.wikimedia.org/wikipedia/meta',
	'testwiki' => '//upload.wikimedia.org/wikipedia/test',
),

'wgUploadDirectory' => array(
	# Using upload7 since Oct 2012
	 'default'      => '/mnt/upload7/$site/$lang',
	 'private' => '/mnt/upload7/private/$lang',

	 'wikimania2005wiki' => '/mnt/upload7/wikipedia/wikimania', // back compat
	 'otrs_wikiwiki' => '/mnt/upload7/private/otrs_wiki',
	 'execwiki' => '/mnt/upload7/private/execwiki',
),

# wgMetaNamespace @{
'wgMetaNamespace' => array(
	// Defaults
	'default'       => 'Wikipedia',
	'wikimedia'     => 'Wikimedia', // chapters
	'wikinews'      => 'Wikinews',
	'wikiquote'     => 'Wikiquote',
	'wikisource'    => 'Wikisource',
	'wikiversity'   => 'Wikiversity',
	'wikivoyage'    => 'Wikivoyage',
	'wiktionary'    => 'Wiktionary',

	// Wikis (alphabetical by DB name)
	'abwiki'		=> 'Авикипедиа',
	'advisorywiki'  => 'Project',
	'amwiki'	=> 'ውክፔዲያ',
	'angwikisource' => 'Wicifruma',
	'arbcom_dewiki' => 'Project',
	'arbcom_enwiki' => 'Project',
	'arbcom_fiwiki' => 'Project',
	'arbcom_nlwiki' => 'Project',
	'arcwiki'		=> 'ܘܝܩܝܦܕܝܐ',
	'arwiki'	=> 'ويكيبيديا', # bug 4672
	'arwikibooks'   => 'ويكي_الكتب',
	'arwikimedia'	=> 'Wikimedia',
	'arwikinews'    => 'ويكي_الأخبار',
	'arwikiquote'   => 'ويكي_الاقتباس',
	'arwikisource'  => 'ويكي_مصدر',
	'arwikiversity' => 'ويكي_الجامعة',
	'arwiktionary'  => 'ويكاموس',
	'arzwiki'		=> 'ويكيبيديا',
	'astwiki'       => 'Uiquipedia',
	'astwiktionary' => 'Uiccionariu',
	'aswiki'	=> 'ৱিকিপিডিয়া',
	'aswikisource'	=> 'ৱিকিউৎস', // Bug 43129
	'auditcomwiki'  => 'Project',
	'aywiki'	=> 'Wikipidiya',
	'azwiki'	=> 'Vikipediya',
	'azwikisource'  => 'VikiMənbə',
	'azwikiquote'   => 'Vikisitat',
	'bawiki'        => 'Википедия', // Bug 41167
	'bat_smgwiki'	=> 'Vikipedėjė',
        'bdwikimedia'   => 'উইকিমিডিয়া_বাংলাদেশ',
	'be_x_oldwiki'  => 'Вікіпэдыя',
	'bewiki'	=> 'Вікіпедыя',
	'bewikisource'  => 'Вікікрыніцы',
	'bgwiki'	=> 'Уикипедия',
	'bgwikibooks'   => 'Уикикниги',
	'bgwikinews'    => 'Уикиновини',
	'bgwikiquote'   => 'Уикицитат',
	'bgwikisource'  => 'Уикиизточник',
	'bgwiktionary'  => 'Уикиречник',
	'bhwiki'		=> 'विकिपीडिया',
	'bjnwiki'		=> 'Wikipidia',
	'bnwiki'	=> 'উইকিপিডিয়া',
	'bnwikibooks'   => 'উইকিবই',
	'bnwikisource'  => 'উইকিসংকলন',
	'bnwiktionary'  => 'উইকিঅভিধান',
	'boardwiki'     => 'Project',
	'bpywiki'       => 'উইকিপিডিয়া',
	'brwikimedia'   => 'Wikimedia',
	'brwikiquote'   => 'Wikiarroud',
	'brwikisource'  => 'Wikimammenn',
	'brwiktionary'  => 'Wikeriadur',
	'bswiki'	=> 'Wikipedia',
	'bswikibooks'   => 'Wikiknjige',
	'bswikinews'    => 'Wikivijesti', // pre-emptive
	'bswikiquote'   => 'Wikicitati',
	'bswikisource'  => 'Wikiizvor', // pre-emptive
	'bswikisource'  => 'Wikizvor',
	'bswiktionary'  => 'Vikirječnik',
	'bxrwiki'	=> 'Википеэди', // Bug 41877
	'cawiki'	=> "Viquip\xc3\xa8dia",
	'cawikibooks'   => 'Viquillibres',
	'cawikinews'    => 'Viquinotícies',
	'cawikiquote'   => 'Viquidites',
	'cawikisource'  => 'Viquitexts',
	'cawiktionary'  => 'Viccionari',
	'cewiki'		=> 'Википедийа',
	'chairwiki'     => 'Project',
	'chapcomwiki'   => 'Chapcom', // ?
	'checkuserwiki' => 'Project', // Bug 28781
	'ckbwiki'       => 'ویکیپیدیا',
	'collabwiki'    => 'Project',
	'commonswiki'   => 'Commons',
	'cowikimedia'   => 'Wikimedia',
	'crhwiki'		=> 'Vikipediya',
	'csbwiki'       => 'Wiki',
	'cswiki'	=> 'Wikipedie',
	'cswikibooks'   => 'Wikiknihy',
	'cswikinews'    => 'Wikizprávy',
	'cswikiquote'   => 'Wikicitáty',
	'cswikiversity' => 'Wikiverzita',
	'cswikisource'  => 'Wikizdroje',
	'cswiktionary'  => 'Wikislovník',
	'cuwiki'	=> 'Википєдїꙗ',
	'cvwiki'	=> 'Википеди',
	'cywiki'	=> 'Wicipedia',
	'cywikibooks'   => 'Wicilyfrau',
	'cywikisource'  => 'Wicitestun',
	'cywiktionary'  => 'Wiciadur',
	'dawikibooks'   => 'Wikibooks',
	'de_labswikimedia' => 'Wikibooks',
	'dkwikimedia'   => 'Wikimedia',
	'donatewiki'    => 'Donate',
	'dsbwiki'       => 'Wikipedija',
	'elwiki'	=> 'Βικιπαίδεια',
	'elwikibooks'   => 'Βικιβιβλία',
	'elwikinews'    => 'Βικινέα',
	'elwikiquote'   => 'Βικιφθέγματα',
	'elwikisource'  => 'Βικιθήκη',
	'elwikiversity' => 'Βικιεπιστήμιο',
	'elwiktionary'  => 'Βικιλεξικό',
	'en_labswikimedia' => 'Wikibooks',
	'eowiki'	=> 'Vikipedio',
	'eowikibooks'   => 'Vikilibroj',
	'eowikinews'    => 'Vikinovaĵoj',
	'eowikiquote'   => 'Vikicitaro',
	'eowikisource'  => 'Vikifontaro',
	'eowiktionary'  => 'Vikivortaro',
	'eswikibooks'   => 'Wikilibros',
	'eswikinews'    => 'Wikinoticias',
	'eswikiversity' => 'Wikiversidad',
	'eswikivoyage'	=> 'Wikiviajes', // Bug 42933
	'eswiktionary'  => 'Wikcionario',
	'etwiki'	=> 'Vikipeedia',
	'etwikibooks'   => 'Vikiõpikud',
	'etwikimedia'   => 'Wikimedia',
	'etwikisource'  => 'Vikitekstid',
	'etwikiquote'   => 'Vikitsitaadid',
	'etwiktionary'  => 'Vikisõnastik',
	'execwiki'      => 'Project',
	'extwiki'       => 'Güiquipeya',
	'fawiki'	=> 'ویکی‌پدیا',
	'fawikibooks'   => 'ویکی‌نسک',
	'fawikinews'	=> 'ویکی‌خبر',
	'fawikiquote'   => 'ویکی‌گفتاورد',
	'fawikisource'  => 'ویکی‌نبشته',
	'fawiktionary'  => 'ویکی‌واژه',
	'fiwikibooks'   => 'Wikikirjasto',
	'fiwikinews'    => 'Wikiuutiset',
	'fiwikiquote'   => 'Wikisitaatit',
	'fiwikisource'  => 'Wikiaineisto',
	'fiwikiversity' => 'Wikiopisto',
	'fiwiktionary'  => 'Wikisanakirja',
	'foundationwiki' => 'Wikimedia',
	'fowikisource'  => 'Wikiheimild',
	'frpwiki'		=> 'Vouiquipèdia',
	'frwiki'	=> 'Wikipédia',
	'frwikibooks'   => 'Wikilivres',
	'frwikiversity' => 'Wikiversité',
	'frwiktionary'  => 'Wiktionnaire',
	'furwiki'       => 'Vichipedie',
	'fywiki'	=> 'Wikipedy',
	'gawiki'	=> 'Vicipéid',
	'gagwiki'	    => 'Vikipediya',
	'gawikibooks'   => 'Vicíleabhair',
	'gawikiquote'   => 'Vicísliocht',
	'gawiktionary'  => 'Vicífhoclóir',
	'gdwiki'	    => 'Uicipeid',
	'gnwiki'		=> 'Vikipetã',
	'grantswiki'    => 'Project',
	'guwiki'	=> 'વિકિપીડિયા',
	'guwikisource'  => 'વિકિસ્રોત',
	'guwiktionary'  => 'વિક્શનરી', // Bug 40776
	'hewiki'	=> "ויקיפדיה",
	'hewikibooks'   => "ויקיספר",
	'hewikinews'    => 'ויקיחדשות',
	'hewikiquote'   => 'ויקיציטוט',
	'hewikisource'  => "ויקיטקסט",
	'hewiktionary'  => "ויקימילון",
	'hiwiki'	=> 'विकिपीडिया',
	'hiwiktionary'  => 'विक्षनरी',
	'hrwiki'	=> 'Wikipedija',
	'hrwikibooks'   => 'Wikiknjige',
	'hrwikiquote'   => 'Wikicitat',
	'hrwikisource'  => 'Wikizvor',
	'hrwiktionary'  => 'Wječnik',
	'hsbwiki'       => 'Wikipedija',
	'hsbwiktionary' => 'Wikisłownik', // Bug 41328
	'htwiki'	=> 'Wikipedya',
	'htwikisource'  => 'Wikisòrs',
	'huwiki'	=> 'Wikipédia',
	'huwikibooks'   => 'Wikikönyvek',
	'huwikinews'    => 'Wikihírek',
	'huwikiquote'   => 'Wikidézet',
	'huwikisource'  => 'Wikiforrás',
	'huwiktionary'  => 'Wikiszótár',
	'hywiki'	=> 'Վիքիպեդիա',
	'hywikibooks'   => 'Վիքիգրքեր',
	'hywikiquote'   => 'Վիքիքաղվածք',
	'hywikisource'	=> 'Վիքիդարան',
	'hywiktionary'  => 'Վիքիբառարան',
	'iawiktionary'	=> 'Wiktionario',
	'idwikibooks'   => 'Wikibuku',
	'ilwikimedia'   => 'ויקימדיה',
	'incubatorwiki' => 'Incubator',
	'internalwiki'  => 'Project',
	'iowiki'	=> 'Wikipedio',
	'iowiktionary'  => 'Wikivortaro',
	'iswikibooks'   => 'Wikibækur',
	'iswikiquote'   => 'Wikivitnun',
	'iswikisource'  => 'Wikiheimild',
	'iswiktionary'  => 'Wikiorðabók',
	'itwikibooks'   => 'Wikibooks',
	'itwikinews'    => 'Wikinotizie',
	'itwikiversity' => 'Wikiversità',
	'itwiktionary'  => 'Wikizionario',
	'iuwiki'        => 'ᐅᐃᑭᐱᑎᐊ',
	'jawikinews'    => "ウィキニュース",
	'kawiki'	=> 'ვიკიპედია',
	'kawikibooks'   => 'ვიკიწიგნები',
	'kawikiquote'   => 'ვიკიციტატა',
	'kawiktionary'  => 'ვიქსიკონი',
	'kbdwiki'		=> 'Уикипедиэ',
	'kkwiki'	=> 'Уикипедия',
	'kkwikibooks'   => 'Уикикітап',
	//'kkwikinews'    => 'Уикихабар',
	'kkwikiquote'   => 'Уикидәйек',
	//'kkwikisource'  => 'Уикиқайнар',
	'kkwiktionary'  => 'Уикисөздік',
	'kmwiki'		=> 'វិគីភីឌា',
	'knwiki'		=> 'ವಿಕಿಪೀಡಿಯ',
	'koiwiki'		=> 'Википедия',
	'kowiki'	=> '위키백과',
	'kowikinews'	=> '위키뉴스',
	'kowikibooks'	=> '위키책',
	'kowikiquote'	=> '위키인용집',
	'kowikisource'  => '위키문헌',
	'kowiktionary'  => '위키낱말사전',
	'krcwiki'	    => 'Википедия',
	'kuwiki'	=> 'Wîkîpediya',
	'kuwiktionary'  => 'Wîkîferheng',
	'kvwiki'	=> 'Википедия',
	'lawiki'	=> 'Vicipaedia',
	'lawikibooks'   => 'Vicilibri',
	'lawikiquote'   => 'Vicicitatio',
	'lawikisource'  => 'Vicifons',
	'lawiktionary'  => 'Victionarium',
	'ladwiki'	=> 'Vikipedya',
	'lbwiktionary'  => 'Wiktionnaire',
	'lbewiki'       => 'Википедия',
	'lezwiki'	=> 'Википедия',
	'liwikibooks'   => 'Wikibeuk',
	'liwikisource'  => 'Wikibrónne',
	'lowiki'		=> 'ວິກິພີເດຍ',
	'ltgwiki'		=> 'Vikipedeja',
	'ltwiki'		=> 'Vikipedija',
	'ltwikisource'  => 'Vikišaltiniai',
	'ltwiktionary'	=> 'Vikižodynas',
	'lvwiki'		=> 'Vikipēdija',
	'mdfwiki'       => 'Википедиесь',
	'mediawikiwiki' => 'Project',
	'metawiki'      => 'Meta',
	'mhrwiki'       => 'Википедий',
	'mkwiki'	=> 'Википедија',
	'mkwikimedia'   => 'Викимедија',
	'mlwiki'	=> 'വിക്കിപീഡിയ',
	'mlwikibooks'   => 'വിക്കിപാഠശാല',
	'mlwikiquote'   => 'വിക്കിചൊല്ലുകൾ',
	'mlwikisource'	=> 'വിക്കിഗ്രന്ഥശാല',
	'mlwiktionary'  => 'വിക്കിനിഘണ്ടു',
	'mrjwiki'		=> 'Википеди',
	'mrwiki'		=> 'विकिपीडिया',
	'mrwikisource'  => 'विकिस्रोत',
	'mrwiktionary'  => 'विक्शनरी',
	'mtwiki'	=> 'Wikipedija',
	'mtwiktionary'  => 'Wikizzjunarju',
	'mwlwiki'		=> 'Biquipédia',
	'mxwikimedia'	=> 'Wikimedia',
	'myvwiki'       => 'Википедиясь',
	'mznwiki'	=> 'ویکی‌پدیا',
	'nahwiki'       => 'Huiquipedia',
	'nds_nlwiki'    => 'Wikipedie',
	'newiki'		=> 'विकिपीडिया',
	'newwiki'       => 'विकिपिडिया',
	'nlwiktionary'  => 'WikiWoordenboek',
	'noboard_chapterswikimedia'   => 'Wikimedia',
	'nowikibooks'   => 'Wikibøker',
	'nowikimedia' => 'Wikimedia',
	'nowikinews'    => 'Wikinytt',
	'nowikisource'  => 'Wikikilden',
	'nvwiki'	=> 'Wikiibíídiiya',
	'nycwikimedia'  => 'Wikimedia', // http://bugzilla.wikimedia.org/show_bug.cgi?id=29273
	'ocwiki'	=> 'Wikipèdia', # http://bugzilla.wikimedia.org/show_bug.cgi?id=7123
	'ocwikibooks'   => 'Wikilibres',
	'ocwiktionary'  => 'Wikiccionari',
	'officewiki'    => 'Project',
	'orwiki' => 'ଉଇକିପିଡ଼ିଆ',
	'oswiki'	=> 'Википеди',
	'otrs_wikiwiki' => 'Project',
	'outreachwiki'  => 'Wikimedia',
	'pa_uswikimedia' => 'Project',
	'pawiki'	=> 'ਵਿਕੀਪੀਡੀਆ',
	'plwikimedia'   => 'Wikimedia',
	'plwikiquote'   => 'Wikicytaty',
	'plwikisource'  => 'Wikiźródła',
	'plwikivoyage'  => 'Wikipodróże',
	'plwiktionary'  => 'Wikisłownik',
	'pnbwiktionary' => 'وکشنری',
	'pntwiki'       => 'Βικιπαίδεια',
	'ptwiki'	=> 'Wikipédia',
	'pswiki'		=> 'ويکيپېډيا' ,
	'pswikibooks'	=> 'ويکيتابونه' ,
	//'pswikiquote'	=> 'ويکيوراشه' ,
	'pswiktionary'	=> 'ويکيسيند' ,
	'ptwikibooks'   => 'Wikilivros',
	'ptwikinews'    => 'Wikinotícias',
	'ptwikiversity' => 'Wikiversidade',
	'ptwiktionary'  => 'Wikcionário',
	'qualitywiki'   => 'Project',
	'rmywiki'       => 'Vikipidiya',
	'rowikibooks'	=> 'Wikimanuale',
	'rowikinews'    => 'Wikiștiri',
	'rowikiquote'   => 'Wikicitat',
	'rowiktionary' => 'Wikționar',
	'rswikimedia'   => 'Викимедија',
	'ruewiki'		=> 'Вікіпедія',
	'ruwiki'	=> 'Википедия',
	'ruwikibooks'   => 'Викиучебник',
	'ruwikimedia'   => 'Викимедиа',
	'ruwikinews'    => 'Викиновости',
	'ruwikiquote'   => 'Викицитатник',
	'ruwikisource'  => 'Викитека',
	'ruwikiversity' => 'Викиверситет',
	'ruwiktionary'  => 'Викисловарь',
	'sawiki'		=> 'विकिपीडिया',
	'sawikiquote'   => 'विकिसूक्तिः',
	'sahwiki'       => 'Бикипиэдьийэ',
	'sahwikisource' => 'Бикитиэкэ',
	'scnwiktionary' => 'Wikizziunariu',
	'searchcomwiki' => 'SearchCom',
	'siwiki'		=> 'විකිපීඩියා',
	'siwikibooks'	=> 'විකිපොත්',
	'siwiktionary'	=> 'වික්ෂනරි',
	'skwiki'	=> 'Wikipédia',
	'skwikiquote'   => 'Wikicitáty',
	'skwiktionary'  => 'Wikislovník',
	'slwiki'	=> 'Wikipedija',
	'slwikibooks'   => 'Wikiknjige',
	'slwikiquote'   => 'Wikinavedek',
	'slwikisource'  => 'Wikivir',
	'slwiktionary'  => 'Wikislovar',
	'slwikiversity' => 'Wikiverza',
	'sourceswiki'   => 'Wikisource',
	'spcomwiki'     => 'Spcom',
	'specieswiki'   => 'Wikispecies',
	'sqwikinews'	=> 'Wikilajme',
	'srwiki'	=> 'Википедија',
	'srwikibooks'   => 'Викикњиге',
	'srwikinews'    => 'Викивести',
	'srwikisource'  => 'Викизворник',
	'srwiktionary'  => "Викиречник",
	'stewardwiki'   => 'Project',
	'strategywiki'  => 'Strategic_Planning',
	'szlwiki'       => 'Wikipedyjo',
	'tawiki'	=> 'விக்கிப்பீடியா',
	'tawikibooks'	=> 'விக்கிநூல்கள்',
	'tawikinews'	=> 'விக்கிசெய்தி',
	'tawikisource'  => 'விக்கிமூலம்',
	'tawiktionary'	=> 'விக்சனரி',
	'tawikiquote'   => 'விக்கிமேற்கோள்',
	'tewiki'	=> 'వికీపీడియా',
	'tewiktionary'  => 'విక్షనరీ',
	'tgwiki'	=> 'Википедиа',
	'thwiki'	=> 'วิกิพีเดีย',
	'thwikibooks'   => 'วิกิตำรา',
	'thwiktionary'  => 'วิกิพจนานุกรม',
	'thwikinews'    => 'วิกิข่าว',
	'thwikiquote'   => 'วิกิคำคม',
	'thwikisource'  => 'วิกิซอร์ซ',
	'tkwiki'	=> 'Wikipediýa',
	'tkwiktionary'	=> 'Wikisözlük',
	'trwiki'	=> 'Vikipedi',
	'trwikibooks'   => 'Vikikitap',
	'trwikinews'    => 'Vikihaber',
	'trwikiquote'   => 'Vikisöz',
	'trwikisource'  => 'Vikikaynak', // Bug 42721
	'trwiktionary'  => 'Vikisözlük',
	'ttwiki'	=> 'Википедия',
	'uawikimedia'   => 'Вікімедіа',
	'ukwiki'	=> 'Вікіпедія',
	'ukwikibooks'	=> 'Вікіпідручник',
	'ukwikinews'    => 'ВікіНовини',
	'ukwikiquote'   => 'Вікіцитати',
	'ukwiktionary'  => 'Вікісловник',
	'urwiki'	=> 'منصوبہ',
        'urwikibooks'   => 'وکی_کتب',
        'urwikiquote'   => 'وکی_اقتباسات',
        'urwiktionary'  => 'وکی_لغت',
	'uzwiki'	=> 'Vikipediya',
	'uzwikibooks'	=> 'Vikikitob',
	'uzwikiquote'   => 'Vikiiqtibos',
	//'uzwikisource'	=> 'Vikimanba',
	'uzwiktionary'	=> 'Vikilug‘at',
	'vepwiki'      => 'Vikipedii',
	'vowiki'	=> 'Vükiped',
	'vowikibooks'	=>	'Vükibuks',
	'vowiktionary'	=> 'Vükivödabuk',
	'vowiktionary'  => 'Vükivödabuk',
	'wikibooks'     => 'Wikibooks',
	'wikimania2005wiki' => 'Wikimania',
	'wikimania2006wiki' => 'Wikimania',
	'wikimania2007wiki' => 'Wikimania',
	'wikimania2008wiki' => 'Wikimania',
	'wikimania2009wiki' => 'Wikimania',
	'wikimania2010wiki' => 'Wikimania',
	'wikimania2011wiki' => 'Wikimania',
	'wikimania2012wiki' => 'Wikimania',
	'wikimania2013wiki' => 'Wikimania',
	'wikimaniateamwiki' => 'WikimaniaTeam',
	'wikimaniawiki' => 'Wikimania', // back compat
	'xmfwiki'		=> 'ვიკიპედია',
	'yiwiki'	=> 'װיקיפּעדיע',
	'yiwikisource'  => 'װיקיביבליאָטעק',
	'yiwiktionary'  => 'װיקיװערטערבוך',
	'zh-min-nanwikisource' => 'Wiki_Tô·-su-kóan',
	'zh_classicalwiki' => '維基大典',

	'wikidatawiki' => 'Wikidata',

),
# @} end of wgMetaNamespace


# wgMetaNamespaceTalk @{
'wgMetaNamespaceTalk' => array(
	'arwikiversity' => 'نقاش_ويكي_الجامعة',
	'aswiki' => 'ৱিকিপিডিয়া_বাৰ্তা',
	'aswikisource' => 'ৱিকিউ স_বাৰ্তা', // Bug 43129
	'aywiki' => 'Wikipidiyan_Aruskipäwi',
	'bawiki' => 'Википедия_буйынса_фекерләшеү', // Bug 41167
	'bat_smgwiki' => 'Vikipedėjės_aptarėms',
        'bdwikimedia'   => 'উইকিমিডিয়া_বাংলাদেশ_আলোচনা',
	'bewikisource'  => 'Размовы_пра_Вікікрыніцы',
	'brwikisource'	=> 'Kaozeadenn_Wikimammenn',
	'cuwiki' => 'Википєдїѩ_бєсѣ́да',
	'cowikimedia'   => 'Wikimedia_discusión',
	'elwikinews'    => 'Βικινέα_συζήτηση',
	'elwikiversity' => 'Συζήτηση_Βικιεπιστημίου',
	'elwiktionary' => 'Συζήτηση_βικιλεξικού',
	'eowiki' => 'Vikipedia_diskuto',
	'eowikinews'	=> 'Vikinovaĵoj_diskuto',
	'eowikisource'  => 'Vikifontaro_diskuto',
	'fawikinews' => 'بحث_ویکی‌خبر',
	'fawikisource' => 'بحث_ویکی‌نبشته',
	'fiwikiversity' => 'Keskustelu_Wikiopistosta',
	'gagwiki'		=> 'Vikipediyanın_laflanması',
	'guwikisource' => 'વિકિસ્રોત_ચર્ચા',
	'hrwikisource' => 'Razgovor_o_Wikizvoru',
	'hsbwiktionary' => 'Diskusija k Wikisłownikej', // Bug 41328
	'huwikinews' => 'Wikihírek-vita',
	'iswiktionary' => 'Wikiorðabókarspjall',
	'kbdwiki'		=> 'Уикипедиэм_и_тепсэлъыхьыгъуэ',
	'kmwiki'		=> 'ការពិភាក្សាអំពីវិគីភីឌា',
	'koiwiki'		=> 'Баитам_Википедия_йылiсь',
	'kowikinews'	=> '위키뉴스토론',
	'kowikiquote'	=> '위키인용집토론',
	'kvwiki'	=> 'Википедия_донъялӧм',
	'lawikisource' => 'Disputatio_Vicifontis',
	'ladwiki'		=> 'Diskusyón_de_Vikipedya',
	'lbwiktionary'  => 'Wiktionnaire_Diskussioun',
	'lezwiki'	=> 'Википедия_веревирд_авун',
	'liwikibooks'	=> 'Euverlèk_Wikibeuk',
	'lowiki'		=> 'ສົນທະນາກ່ຽວກັບວິກິພີເດຍ',
	'ltgwiki'		=> 'Vikipedejis_sprīža',
	'ltwiki'		=> 'Vikipedijos_aptarimas',
	'ltwiktionary' => 'Vikižodyno_aptarimas',
	'lvwiki'		=> 'Vikipēdijas_diskusija',
	'mrjwiki'		=> 'Википедим_кӓнгӓшӹмӓш',
	'mtwiki'	=> 'Diskussjoni_Wikipedija',
	'mtwiktionary'  => 'Diskussjoni_Wikizzjunarju',
	'mxwikimedia'	=> 'Wikimedia_discusión',
        'mznwiki'       => 'ویکی_پدیا_گپ',
	'newiki'		=> 'विकिपीडिया_वार्ता',
	'newwiki' => 'विकिपिडिया_खँलाबँला',
	'noboard_chapterswikimedia' => 'Wikimedia-diskusjon',
	'nsowiki'	=> 'Dipolelo_tša_Wikipedia',
	'nycwikimedia'  => 'Wikimedia_talk',
	'pnbwiktionary' => 'گل_ات',
	'pntwiki'       => 'Βικιπαίδεια_καλάτσεμαν',
	'ptwiki'	=> 'Wikipédia_Discussão',
	'ptwikibooks' => 'Wikilivros_Discussão',
	'ruewiki'		=> 'Діскузія_ку_Вікіпедії',
	'ruwikibooks' => 'Обсуждение_Викиучебника',
	'ruwikimedia' => 'Обсуждение_Викимедиа',
	'ruwikiversity' => 'Обсуждение_Викиверситета',
	'sahwiki' => 'Бикипиэдьийэ_ырытыыта',
	'sahwikisource' => 'Бикитиэкэ_Ырытыы',
	'scnwiktionary' => 'Discussioni_Wikizziunariu',
	'siwiki'		=> 'විකිපීඩියා_සාකච්ඡාව',
	'siwikibooks'	=> 'විකිපොත්_සාකච්ඡාව',
	'siwiktionary'	=> 'වික්ෂනරි_සාකච්ඡාව',
	'slwikiversity' => 'Pogovor_o_Wikiverzi',
	'sqwikinews'	=> 'Wikilajme_diskutim',
	'srwiki' => 'Разговор_о_Википедији',
	'srwikibooks' => 'Разговор_о_викикњигама',
	'srwikinews' => 'Разговор_о_Викивестима',
	'srwikisource' => 'Разговор_о_Викизворнику',
	'srwiktionary' => 'Разговор_о_викиречнику',
	'svwikiversity' => 'Wikiversitydiskussion',
	'tawiki'		=> 'விக்கிப்பீடியா_பேச்சு',
	'tawikinews'	=> 'விக்கிசெய்தி_பேச்சு',
	'tawikisource'  => 'விக்கிமூலம்_பேச்சு',
	'tawiktionary'	=> 'விக்சனரி_பேச்சு',
	'thwikibooks'   => 'คุยเรื่องวิกิตำรา', // Bug 40717
	'thwikiquote'   => 'คุยเรื่องวิกิคำคม',
	'thwiktionary'  => 'คุยเรื่องวิกิพจนานุกรม', // Bug 40717
	'trwikinews'    => 'Vikihaber_tartışma',
	'uawikimedia'   => 'Обговорення_Вікімедіа',
	'ukwiktionary' => 'Обговорення_Вікісловника',
	'vepwiki'      => 'Paginad_Vikipedii',
	'xmfwiki'		=> 'ვიკიპედია_სხუნუა',
),
# @} end of wgMetaNamespaceTalk

# wgNamespaceAliases @{
'wgNamespaceAliases' => array(
	// defaults to aid when things are switched
	'+wikibooks' => array( 'Wikibooks' => NS_PROJECT ),
	'+wikinews' => array( 'Wikinews' => NS_PROJECT ),
	'+wikiquote' => array( 'Wikiquote' => NS_PROJECT ),
	'+wikisource' => array( 'Wikisource' => NS_PROJECT ),
	'+wikiversity' => array( 'Wikiversity' => NS_PROJECT ),
	'+wikipedia' => array( 'Wikipedia' => NS_PROJECT ),
	'+wiktionary' => array( 'Wiktionary' => NS_PROJECT ),
	'+wikivoyage' => array(
		'Wikivoyage' => NS_PROJECT,
		'WV' => NS_PROJECT,
	),
	'+abwiki' => array(
		'Wikipedia' => NS_PROJECT,
	),
	'+amwiki' => array(
		 100	<= 'በር',
	),
	'+arcwiki' => array(
		'Wikipedia' => NS_PROJECT,
	),
	'+arwiki' => array(
		'وب' => NS_PROJECT,
		'نو' => NS_PROJECT_TALK,
	),
	'+arwikinews' => array(
		'وخ' => NS_PROJECT,
		'نو' => NS_PROJECT_TALK,
	),
	'+arwikisource' => array(
		'Author' => 102,
		'Author_talk' => 103,
		'Page' => 104,
		'Page_talk' => 105,
		'وم' =>   NS_PROJECT,
		'نو' => NS_PROJECT_TALK,
	),
	'+arwikiversity' => array(
		'وج' => NS_PROJECT
	),
	'+arzwiki' => array(
		'Portal' => 100,
		'Portal_talk' => 101,
	),
	'+aswiki' => array(
		'Wikipedia' => NS_PROJECT,
		'Wikipedia_talk' => NS_PROJECT_TALK,
		'প্ৰকল্প' => NS_PROJECT,
		'প্ৰকল্প_আলোচনা' => NS_PROJECT_TALK,
		'Wikipedia_বার্তা' => NS_PROJECT_TALK,
		'WP' => NS_PROJECT,
		'CAT' => NS_CATEGORY,
		"বাটচ'ৰা" => 100,
		"বাটচ'ৰা_আলোচনা" => 101,
	),
	'aswikisource' => array(
		'Page' => 104,        // Bug 43129
		'Page_talk' => 105,
		'Index' => 106,       // Bug 43129
		'Index_talk' => 107,
	),
	'+azwikiquote' => array(
		'Wikiquote'      => NS_PROJECT,
		'Wikiquote_talk' => NS_PROJECT_TALK,
	),
	'+azwikisource' => array(
		'Portal' => 100,
		'Portal_talk' => 101,
	),
	'+bawiki' => array(
		'Wikipedia' => NS_PROJECT,                         // Bug 41167
		'Wikipedia_буйынса_фекерләшеү' => NS_PROJECT_TALK,
	),
	'+barwiki' => array(
		'Portal_Diskussion' => 101, // Bug 41962 - now "Portal Dischkrian"
	),
	'+bewiki' => array(
		'Portal' => 100,
		'Portal_talk' => 101,
		'ВП' => NS_PROJECT,
	),
	'+be_x_oldwiki' => array(
		'ВП' => NS_PROJECT,
	),
	'+bewikisource' => array(
		'ВК' => NS_PROJECT,     // Bug 41322
		'Author' => 102,        // Bug 41322
		'Author_talk' => 103,
		'Page' => 104,          // Bug 41322
		'Page_talk' => 105,
		'Index' => 106,         // Bug 41322
		'Index_talk' => 107,
	),
	'+bgwikisource' => array(
		'Author' => 100,
		'Author_talk' => 101,
	),
	'+bjnwiki' => array(
		'Wikipidia_pamandiran' => NS_PROJECT_TALK,
	),
	'+bnwiki' => array(
		'Portal' => 100,
		'Portal_talk' => 101,
		'കവാടം' => 100,
		'കവാടത്തിന്റെ_സംവാദം' => 101,
		'Wikipedia' => NS_PROJECT,
		'WP' => NS_PROJECT,
		'WT' => NS_PROJECT_TALK,
		'വിഭാഗം' => NS_CATEGORY,
		'വിഭാഗത്തിന്റെ_സംവാദം' => NS_CATEGORY_TALK,
	),
	'+bnwikibooks' => array(
			'WB' => NS_PROJECT,
			'Wikibooks' => NS_PROJECT,
		'Wikijunior' => 100,
		'wikijunior_talk' => 101,
		'Subject' => 102,
		'Subject_talk' => 103,
	),
	'+bpywiki' => array(
		'Portal' => 100,
		'Portal_talk' => 101,
	),
	'+brwiki' => array( 'Discussion_Wikipedia' => NS_PROJECT_TALK, ),
	'+brwikiquote' => array( 'Wikiquote' => NS_PROJECT ),
	'+brwikisource' => array(
			'Index' => 100,
			'Index_talk' => 101,
			'Page' => 102,
			'Page_talk' => 103,
			'Author' => 104,
			'Author_talk' => 105,
	),
	'+brwiktionary' => array( 'Wiktionary' => NS_PROJECT, 'Wiktionary_talk' => NS_PROJECT_TALK ),
	'+cawiki' => array(
		'Usuària' => NS_USER,
		'Usuària_discussió' => NS_USER_TALK,
	),
	'+bswiki' => array(
		'Portal_talk' => 101, // Bug 37226
	),
	'+cawikisource' => array(
		'Page' => 102,
		'Page_talk' => 103,
		'Index' => 104,
		'Index_talk' => 105,
		'Author' => 106,
		'Author_talk' => 107,
	),
	'+cewiki' => array( 'Wikipedia' => NS_PROJECT, ),
	'+commonswiki' => array(
		'Museum' => 106,
		'Museum_talk' => 107,
		'COM' => NS_PROJECT, // http://bugzilla.wikimedia.org/show_bug.cgi?id=12600
	),
	'+cswiki' => array(
		'Uživatel'		=> NS_USER,	 # language default which is overriden in wgExtraNamespaces
		'Wikipedistka'	    => NS_USER,	 # female complement to project default
		'Diskuse_s_uživatelem'    => NS_USER_TALK,    # language default which is overriden in wgExtraNamespaces
		'Diskuse_s_wikipedistkou' => NS_USER_TALK,    # female complement to project default
		'Wikipedista_diskuse'     => NS_USER_TALK,    # pre r65112 project default backward compatibility
		'Wikipedistka_diskuse'    => NS_USER_TALK,    # female complement to pre r65112 style project default
		'WP'		      => NS_PROJECT,      # cs & en shortcut
		'Portal'		  => 100,	     # en default
		'Portal_talk'	     => 101,	     # en default
		'Portál_diskuse'	  => 101,	     # pre r65112 style backward compatibility
		'Rejstřík_diskuse'	=> 103,	     # pre r65112 style backward compatibility
	),
	'+cswikibooks' => array(
		'WB'		      => NS_PROJECT,      # en shortcut
		'WK'		      => NS_PROJECT,      # cs shortcut
		'Wikibooks'	       => NS_PROJECT,      # pre project name localization backward compatibility
		'Wikibooks_diskuse'       => NS_PROJECT_TALK, # pre project name localization backward compatibility cs generic
		'Wikibooks_talk'	  => NS_PROJECT_TALK, # pre project name localization backward compatibility en generic
	),
	'+cswikinews' => array(
		'Redaktorka'	      => NS_USER,	 # female complement to project default
		'Uživatel'		=> NS_USER,	 # language default which is overriden in wgExtraNamespaces
		'Diskuse_s_redaktorkou'   => NS_USER_TALK,    # female complement to project default
		'Diskuse_s_uživatelem'    => NS_USER_TALK,    # language default which is overriden in wgExtraNamespaces
		'Redaktor_diskuse'	=> NS_USER_TALK,    # pre r65112 project default backward compatibility
		'Redaktorka_diskuse'      => NS_USER_TALK,    # female complement to pre r65112 style project default
		'WN'		      => NS_PROJECT,      # en shortcut
		'WZ'		      => NS_PROJECT,      # cs shortcut
		'Wikinews'		=> NS_PROJECT,      # pre project name localization backward compatibility
		'Wikinews_diskuse'	=> NS_PROJECT_TALK, # pre project name localization backward compatibility cs generic
		'Wikinews_talk'	   => NS_PROJECT_TALK, # pre project name localization backward compatibility en generic
		'Portal'		  => 100,	     # en default
		'Portal_talk'	     => 101,	     # en default
		'Portál_diskuse'	  => 101,	     # pre r65112 style backward compatibility
	),
	'+cswikiquote' => array(
		'WC'		      => NS_PROJECT,      # cs shortcut
		'WQ'		      => NS_PROJECT,      # en shortcut
		'Wikiquote'	       => NS_PROJECT,      # pre project name localization backward compatibility
		'Wikiquote_diskuse'       => NS_PROJECT_TALK, # pre project name localization backward compatibility cs generic
		'Wikiquote_talk'	  => NS_PROJECT_TALK, # pre project name localization backward compatibility en generic
	 ),
	'+cswikisource' => array(
		'WS'		      => NS_PROJECT,      # en shortcut
		'WZ'		      => NS_PROJECT,      # cs shortcut
		'Wikisource'	      => NS_PROJECT,      # pre project name localization backward compatibility
		'Wikisource_diskuse'      => NS_PROJECT_TALK, # pre project name localization backward compatibility cs generic
		'Wikisource_talk'	 => NS_PROJECT_TALK, # pre project name localization backward compatibility en generic
		'Author'		  => 100,	     # en default
		'Author_talk'	     => 101,	     # en default
		'Autor_diskuse'	   => 101,	     # pre r65112 style backward compatibility
	),
	'+cswikiversity' => array(
		'WV'		      => NS_PROJECT,      # cs & en shortcut
		'Wikiversity'	     => NS_PROJECT,      # pre project name localization backward compatibility
		'Wikiversity_diskuse'     => NS_PROJECT_TALK, # pre project name localization backward compatibility cs generic
		'Wikiversity_talk'	=> NS_PROJECT_TALK, # pre project name localization backward compatibility en generic
		'Forum'		   => 100,	     # en default
		'Forum_talk'	      => 101,	     # en default
		'Fórum_diskuse'	   => 101,	     # pre r65112 style backward compatibility
	),
	'+cswiktionary' => array(
		'WS'		      => NS_PROJECT,      # cs shortcut
		'WT'		      => NS_PROJECT,      # en shortcut
		'Wiktionary'	      => NS_PROJECT,      # pre project name localization backward compatibility
		'Wiktionary_diskuse'      => NS_PROJECT_TALK, # pre project name localization backward compatibility cs generic
		'Wiktionary_talk'	 => NS_PROJECT_TALK, # pre project name localization backward compatibility en generic
		'Příloha_diskuse' => 101, # pre r65122-style backwards compatibility
	),
	'+cuwiki' => array(
		'Шаблон' => NS_TEMPLATE,
		'Категория' => NS_CATEGORY,
		'Участник' => NS_USER,
		'Википє́дїꙗ' => NS_PROJECT,
		'Википє́дїѩ_бєсѣ́да' => NS_PROJECT_TALK,

	),
	'+dawiki' => array(
		'WP' => NS_PROJECT, # 27998
		'Portal_diskussion' => 101, // changed http://bugzilla.wikimedia.org/show_bug.cgi?id=7759
	),
	'+dawikisource' => array(
		'Author' => 102, // http://bugzilla.wikimedia.org/show_bug.cgi?id=7796
		'Author_talk' => 103,
		'Page' => 104,
		'Page_talk' => 105,
		'Index' => 106,
		'Index_talk' => 107,
	),
	'+dewiki' => array(
		'WP' => NS_PROJECT,
		'P' => 100,
		'PD' => 101,
		'WD' => 5,
		'BD' => 3,
		'H' => NS_HELP,
		'HD' => NS_HELP_TALK,
	),
	'+dewikinews' => array(
		'Comments' => 102,
		'Comments_talk' => 103,
	),
	'+dewikiquote' => array(
		'WQ' => NS_PROJECT,
		'BD' => NS_USER_TALK,
	),
	'+dewikisource' => array(
		'WS' => 4,
		'Page' => 102,
		'Page_talk' => 103,
		'Index' => 104,
		'Index_talk' => 105,
	),
	'+dewikivoyage' => array(
		'News' => 106,
		'News_Diskussion' => 107,
	),
	'+dewiktionary' => array(
		'WT' => NS_PROJECT,
		'WikiSaurus' => 104,
		'WikiSaurus_Diskussion' => 105,
		'BD' => NS_USER_TALK,
	),
	'+dvwiki' => array(
		'Portal' => 100,
		'Portal_talk' => 101,
	),
	'+elwikisource' => array(
		'Page' => 100,
		'Page_talk' => 101,
		'Index' => 102,
		'Index_talk' => 103,
	),
	'+enwiki' => array(
	 // bug 6313
		'WP' => NS_PROJECT,
		'WT' => NS_PROJECT_TALK,
	),
	'+enwikibooks' => array(
		'WB' => 4,
		'WJ' => 110,
		'CAT' => 14,
		'COOK' => 102,
		'SUB' => 112,
	),
	'+enwikinews' => array(
			'WN' => NS_PROJECT,
		'CAT' => NS_CATEGORY,
	),
	'+enwikisource' => array(
		'WS' => NS_PROJECT,       // Bug 42853
		'WT' => NS_PROJECT_TALK,  // Bug 42853
	),
	'+enwikiversity' => array(
		'WV' => NS_PROJECT,
	),
	'+enwiktionary' => array(
			'WS' => 110, // Wikisaurus
			'WT' => NS_PROJECT,
	),
	'+eowiki' => array(
			'VP' => NS_PROJECT,
	),
	'+eowiktionary' => array(
		'Vikipediisto' => NS_USER,		   # bug 22426
		'Vikipediista_diskuto' => NS_USER_TALK,     # bug 22426
	),
	'+eswikisource' => array(
		'Auxtoro' => 102,
		'Auxtoro-Diskuto' => 103,
		'Page' => 104,
		'Page_talk' => 105,
		'Index' => 106,
		'Index_talk' => 107,
	),
	'+etwiki' => array(
		'Portal' => 100,
		'Portal_talk' => 101,
	),
	'+etwikisource' => array(
			'Page' => 102,
			'Page_talk' => 103,
			'Index' => 104,
			'Index_talk' => 105,
			'Author' => 106,
			'Author_talk' => 107,
	),
	'+fawiki' => array(
				'وپ' => NS_PROJECT,
				'Book' => 102,
				'Book_talk' => 103,
				'كتاب' => 102,
				'بحث_كتاب' => 103,
	),
	'+fawikinews' => array(
				'وخ' => NS_PROJECT,
	),
	'+fawikisource' => array(
		'Portal' => 100,
		'Portal_talk' => 101,
		'Author' => 102,
		'Author_talk' => 103,
		'پدیدآورنده' => 102,
		'گفتگو_پدیدآورنده' => 103,
		'Page' => 104,
		'Page_talk' => 105,
		'Index' => 106,
		'Index_talk' => 107,
		'ون' => NS_PROJECT,
	),
	'+fawiktionary' => array(
		'وو' => NS_PROJECT,
	),
	'+fiwiki' => array(
		'WP' => NS_PROJECT,
	),
	'+frrwiki' => array( // Bug 38023
		'Page' => 102,
		'Page_talk' => 103,
		'Index_talk' => 105,
	),
	'+frwiki' => array(
		'Wikipedia' => NS_PROJECT,
		'WP' => NS_PROJECT,
		'Discussion_Wikipedia' => NS_PROJECT_TALK,
		'Utilisatrice' => NS_USER,
		'Discussion_Utilisatrice' => NS_USER_TALK,
	),
	'+frwikibooks' => array(
		'WL' => NS_PROJECT, # bug 35977
		'WJ' => 102, # bug 35977
		'Wikijunior_talk' => 103, # bug 35977
	),
	'+frwikinews' => array(
		'WN' => NS_PROJECT,
	),
	'+frwikisource' => array(
		'Author' => 102,
		'Author_talk' => 103,
		'Page' => 104,
		'Page_talk' => 105,
		'Index' => 112,
		'Index_talk' => 113,
	),
	'+frwiktionary' => array(
		'WT' => NS_PROJECT,
	),
	'+gdwiki' => array(
			'Wikipedia' => NS_PROJECT,
			'Wikipedia_talk' => NS_PROJECT_TALK,
	),
	'+glwiki' => array(
		'Portal_talk' => 101, // Bug 41213 (old namespace name)
	),
	'+guwiktionary' => array(
		'વિક્શનરી' => NS_PROJECT // Bug 40776 (old namespace name)
	),
	'+hewikisource' => array(
		'Page' => 104,
		'Page_talk' => 105,
		'Author' => 108,
		'Author_talk' => 109,
		'Index' => 112,
		'Index_talk' => 113,
		'באור' => 106,
		'שיחת_באור' => 107,
	),
	'+hiwiki' => array(
	   'वि' => NS_PROJECT,
	   'विवा' => NS_PROJECT_TALK,
	   'Wikipedia' => NS_PROJECT,
	   'WP' => NS_PROJECT,
	   'WPT' => NS_PROJECT_TALK,
	   'U' => NS_USER,
	   'UT' => NS_USER_TALK,
	   'स' => NS_USER,
	   'सवा' => NS_USER_TALK,
	   'श्र' => NS_CATEGORY,
	   'श्रवा' => NS_CATEGORY_TALK,
	   'C' => NS_CATEGORY,
	   'CT' => NS_CATEGORY_TALK,
	   'सा' => NS_TEMPLATE,
	   'सावा' => NS_TEMPLATE_TALK,
	   'T' => NS_TEMPLATE,
	   'मी' => NS_MEDIAWIKI,
	   'मीवा' => NS_MEDIAWIKI_TALK,
	   'P' => 100,
	   'प्र' => 100,
	   'प्रवा' => 101,
	),
	'+hrwikisource' => array(
		'Author' => 100,
		'Author_talk' => 101,
		'Page' => 102,
		'Page_talk' => 103,
		'Index' => 104,
		'Index_talk' => 105,
	),
	'+hsbwiktionary' => array(
		'Wiktionary' => NS_PROJECT, // Bug 41328 (old namespace)
		'Wiktionary diskusija' => NS_PROJECT_TALK,
	),
	'+htwiki' => array(
		'Wikipedia' => NS_PROJECT,
		'Wikipedia_talk' => NS_PROJECT_TALK,
	),
	'+huwiki' => array(
		'Portál_vita' => 101,
	),
	'+huwikinews' => array(
		'Wikihírek_vita' => NS_PROJECT_TALK,
		'Portál_vita' => 103,
	),
	'+huwikisource' => array(
		'Author' => 100,
		'Author_talk' => 101,
		'Page' => 104,
		'Page_talk' => 105,
		'Index' => 106,
		'Index_talk' => 107,
	),
	'+hywikisource' => array(
		'Author' => 100,
		'Author_talk' => 101,
		'Page' => 104,
		'Page_talk' => 105,
		'Index' => 106,
		'Index_talk' => 107,
	),
	'+idwikibooks' => array(
		'Wikibooks'	       => NS_PROJECT,      # backcompat for shell req bug 36156
		'Pembicaraan_Wikibooks'	  => NS_PROJECT_TALK, # backcompat for shell req bug 36156
	),
	'+idwikisource' => array(
		'Author' => 100,
		'Author_talk' => 101,
		'Index' => 102,
		'Index_talk' => 103,
		'Page' => 104,
		'Page_talk' => 105,
	),
	'+ilowiki' => array(
		'WP' => NS_PROJECT, // bug 38247
		'WT' => NS_PROJECT_TALK, // bug 38247
	),
	'+iowiki' => array(
		'Wikipedia' => NS_PROJECT,
		'Wikipedia_talk' => NS_PROJECT_TALK,
	),
	'+incubatorwiki' => array(
		'I' => NS_PROJECT,
	),
	'+iswiktionary' => array(
		'Wiktionary' => NS_PROJECT,
		'Wikiorðabókspjall' => 5, // changed http://bugzilla.wikimedia.org/show_bug.cgi?id=7754
		'Thesaurus' => 110,
		'Thesaurus_talk' => 111,
	),
	'+itwiki' => array(
		'WP' => NS_PROJECT, // 15116
	),
	'+itwikibooks' => array(
		'Portale' => 100,
		'Discussioni_portale' => 101,
		'Shelf' => 102,
		'Shelf_talk' => 103,
		'WB' => NS_PROJECT,
	),
	'+itwikisource' => array(
		'Author' => 102,
		'Author_talk' => 103,
		'Page' => 108,
		'Page_talk' => 109,
		'Index' => 110,
		'Index_talk' => 111,
	),
	'+itwiktionary' => array(
		'WZ' => NS_PROJECT,
	),
	'+iuwiki' => array(
		'Wikipedia' => NS_PROJECT,
		'Wikipedia_talk' => NS_PROJECT_TALK,
	),
	'+jawiki' => array(
		'トーク' => NS_TALK,
		'利用者・トーク' => NS_USER_TALK,
		'Wikipedia・トーク' => NS_PROJECT_TALK,
		'ファイル・トーク' => NS_IMAGE_TALK,
		'MediaWiki・トーク' => NS_MEDIAWIKI_TALK,
		'テンプレート' => NS_TEMPLATE,
		'テンプレート・トーク' => NS_TEMPLATE_TALK,
		'ヘルプ' => NS_HELP,
		'ヘルプ・トーク' => NS_HELP_TALK,
		'カテゴリ' => NS_CATEGORY,
		'カテゴリ・トーク' => NS_CATEGORY_TALK,
		"ポータル‐ノート" => 101,
		'Portal‐ノート' => 101,
		'Portal・トーク' => 101,
				'プロジェクト・トーク' => 103,
	),
	'+jawikinews' => array(
		"ポータル‐ノート" => 101,
	),
	'+jawikiversity' => array(
		 'Wikiversity_talk' => NS_PROJECT_TALK,
	),
	'+kawikiquote' => array(
		'Wikiquote' => 4,
		'Wikiquote_განხილვა' => 5,
	),
	'+kkwiki' => array(
		'Joba' => 102,           // Bug 40794
		'Joba_talqılawı' => 103,
		'جوبا' => 102,
		'جوبا_تالقىلاۋى' => 103,
	),
	'+knwiki' => array(
		'Wikipedia' => NS_PROJECT,
		'Wikipedia_talk' => NS_PROJECT_TALK,
	),
	'+knwikisource' => array( // Bug 37676
		'Portal' => 100,
		'Portal_talk' => 101,
		'Author' => 102,
		'Author_talk' => 103,
		'Page' => 104,
		'Page_talk' => 105,
		'Index' => 106,
		'Index_talk' => 107,
	),
	'+kowiki' => array(
		'백' => NS_PROJECT,
		'백토' => NS_PROJECT_TALK,
		'Portal' => 100,
		'Portal_talk' => 101,
		'들' => 100,
		'들토' => 101,
		'프' => 102,
		'프토' => 103,
	),
	'+kowikinews' => array(
		'뉴'   =>  NS_PROJECT,
		'뉴토'  => NS_PROJECT_TALK,
	),
	'+kowikiquote' => array(
		'인' => NS_PROJECT,
	),
	'+kowikisource' => array(
		'Wikisource' => NS_PROJECT,
		'Wikisource_talk' => NS_PROJECT_TALK,
		'글쓴이' => 100,
		'글쓴이토론' => 101,
		'Author' => 100,
		'Author_talk' => 101,
	),
	'+kowiktionary' => array(
		'Wikisaurus' => 110,
		'Wikisaurus_talk' => 111,
	),
	'+kuwiktionary' => array(
		'Wîkîferheng_gotûbêj' => NS_PROJECT_TALK, // Bug 37524 (old namespace name)
		'Pêvek_nîqas' => 101,   // Bug 37524 (old namespace name)
		'Nimînok_nîqas' => 103, // Bug 37524 (old namespace name)
		'Portal_nîqas' => 105,  // Bug 37524 (old namespace name)
	),
	'+kuwiki' => array(
		'Portal_nîqaş' => 101, // Bug 37521
	),
	'+kvwiki' => array(
		'Wikipedia' => NS_PROJECT,
		'Обсуждение_Wikipedia' => NS_PROJECT_TALK,
	),
	'+lawiki' => array( 'Disputatio_Wikipedia' => NS_PROJECT_TALK ),
	'+lawikisource' => array(
		'Author' => 102,
		'Author_talk' => 103,
		'Page' => 104,
		'Page_talk' => 105,
		'Index' => 106,
		'Index_talk' => 107,
		'Wikisource' => 4,
	),
	'+lbwiktionary' => array(
		'Wiktionary' => 4,
		'Wiktionary_Diskussioun' => 5,
	),
	'+ltwiki' => array( 'Wikipedia' => NS_PROJECT, 'Wikipedia_aptarimas' => NS_PROJECT_TALK ),
	'+lvwiki' => array( 'Wikipedia' => NS_PROJECT ),
	'+metawiki' => array( 'R' => 202 ), // http://bugzilla.wikimedia.org/show_bug.cgi?id=29129
	'+mlwiki' => array(
		'വിക്കി' => NS_PROJECT,
		'വിക്കിസം' => NS_PROJECT_TALK,
		'Wikipedia' => NS_PROJECT,
		'WP' => NS_PROJECT,
		'Portal' => 100,
		'Portal_talk' => 101,
	),
	'+mlwikibooks' => array(
		'വിക്കി‌‌_പുസ്തകശാല' => NS_PROJECT,
		'വിക്കി‌‌_പുസ്തകശാല_സംവാദം' => NS_PROJECT_TALK,
		'Wikibooks' => NS_PROJECT,
		'Wikibooks_talk' => NS_PROJECT_TALK,
		'Cookbook' => 100,
		'Cookbook_talk' => 101,
		'Subject' => 102,
		'Subject_talk' => 103,
		'വി' => NS_PROJECT,
		'വിസം' => NS_PROJECT_TALK,
		'ഉ' => NS_USER,
		'ഉസം' => NS_USER_TALK,
		'പ്ര' => NS_IMAGE,
		'പ്രസം' => NS_IMAGE_TALK,
		'ഫ' => NS_TEMPLATE,
		'ഫസം' => NS_TEMPLATE_TALK,
		'വ' => NS_CATEGORY,
		'വസം' => NS_CATEGORY_TALK,
		'സ' => NS_HELP,
		'സസം' => NS_HELP_TALK,
	),
	'+mlwikiquote' => array(
		'വിക്കി_ചൊല്ലുകൾ'	      => NS_PROJECT, // bug 38111
		'വിക്കി_ചൊല്ലുകൾ_സംവാദം'	      => NS_PROJECT_TALK, // bug 38111
	),
	'+mlwikisource' => array(
		'Author'	      => 100,
		'Author_talk' => 101,
		'Portal'	      => 102,
		'Portal_talk' => 103,
		'Index'		       => 104,
		'Index_talk'  => 105,
		'Page'			=> 106,
		'Page_talk'	   => 107,
		'Wikisource' => NS_PROJECT,
		'WS' => NS_PROJECT,
		'H' => NS_HELP, # bug 35712
	),
	'+mlwiktionary' => array(
		'Wiktionary' => NS_PROJECT,
		'വിക്കി‌‌_നിഘണ്ടു' => NS_PROJECT,
		'വിക്കി‌‌_നിഘണ്ടു_സംവാദം' => NS_PROJECT_TALK,
	),
	'+mrwiki' => array(
		'Wikipedia'   => NS_PROJECT,
		'विपी'		=> NS_PROJECT,
	),
	'+mrwikisource' => array(
		'Portal' => 100,
		'Portal_talk' => 101,
		'Author' => 102,
		'Author_talk' => 103,
		'Page' => 104,
		'Page_talk' => 105,
		'Index' => 106,
		'Index_talk' => 107,
	),
	'+mswiki' => array(
		'Portal_talk' => 101,
	),
	'+mwlwiki' => array(
		'Wikipedia' => NS_PROJECT,
		'Wikipedia_cumbersa' => NS_PROJECT_TALK,
	),
	'+mznwiki' => array(
		'وپ' => NS_PROJECT,
		'Portal' => 100,
		'Portal_talk' => 101,
		'Wikipedia' => 4,
		'Wikipedia_گپ' => 5,
	),
	'+nahwiki' => array(
		'Wikipedia' => NS_PROJECT,
		'Wikipedia_tēixnāmiquiliztli' => NS_PROJECT_TALK,
	),
	'+ndswiki' => array(
		'WP' => NS_PROJECT,
	),
	'+nds_nlwiki' => array( 'Wikipedia' => NS_PROJECT ),
	'+newiki' => array( 'Wikipedia' => NS_PROJECT ),
	'+nlwiki' => array(
		 'WP' => NS_PROJECT,
		 'H' => NS_HELP,
		 'P' => 100,
	),
	'+nlwikisource' => array(
		'Author' => 102,
		'Author_talk' => 103,
	),
	'+nnwiki' => array(
		 'WP' => NS_PROJECT,
	),
	'+nowiki' => array(
		'WP' => NS_PROJECT, // bug 41467
	),
	'+nowikimedia' => array(
		'Brukar' => NS_USER,
		'Brukardiskusjon' => NS_USER_TALK,
		'Fil' => NS_IMAGE,
		'Fildiskusjon' => NS_IMAGE_TALK,
		'Wikimedia_Noreg' => NS_PROJECT,
		'Wikimedia_Norga' => NS_PROJECT,
	),
	'+nowikisource' => array(
		'Author' => 102, // http://bugzilla.wikimedia.org/show_bug.cgi?id=7796
		'Author_talk' => 103,
		'Page' => 104,
		'Page_talk' => 105,
		'Index' => 106,
		'Index_talk' => 107,
	),
	'+nowiktionary' => array(
		'Appendix' => 100,
		'Appendix_talk' => 101,
	),
	'+orwiki' => array(
		'Wikipedia' => NS_PROJECT,
		'Wikipedia_talk' => NS_PROJECT_TALK,
		'WP' => NS_PROJECT, // http://bugzilla.wikimedia.org/show_bug.cgi?id=28257
		'WT' => NS_PROJECT_TALK, // http://bugzilla.wikimedia.org/show_bug.cgi?id=28257
	),
	'+outreachwiki' => array(
			'Wikipedia' => NS_PROJECT,
			'Wikipedia_talk' => NS_PROJECT_TALK,
	),
	'+plwiki' => array(
		// http://bugzilla.wikimedia.org/show_bug.cgi?id=10064
		'Wikipedystka' => NS_USER,
		'Dyskusja_wikipedystki' => NS_USER_TALK,
		'WP' => NS_PROJECT,
	),
	'+plwikibooks' => array(
		'Wikipedystka' => NS_USER,
		'Dyskusja_wikipedystki' => NS_USER_TALK,
		'WB' => NS_PROJECT,
	),
	'+plwikisource' => array(
		'WS' => NS_PROJECT,
		'Page' => 100,
		'Page_talk' => 101,
		'Index' => 102,
		'Index_talk' => 103,
		'Author' => 104,
		'Author_talk' => 105,
	),
	'+plwiktionary' => array(
		'WS' => NS_PROJECT,
	),
	'+ptwiki' => array(
		'Utilizador' => NS_USER,  # bug 27495
		'Utilizador_Discussão' => NS_USER_TALK, # bug 27495
		'Discussão_Portal' => 101,
		'WP' => NS_PROJECT, # 27728
		'Wikipedia'	=> NS_PROJECT,
		'Wikipedia_Discussão' => NS_PROJECT_TALK,
	),
	'+ptwikibooks' => array(
		'Wikibooks' => NS_PROJECT,
		'Wikibooks_Talk' => NS_PROJECT_TALK,
		'Wikibooks_Discussão' => NS_PROJECT_TALK,
	),
	'+ptwikisource' => array(
		'Author' => 102,
		'Author_talk' => 103,
		'Index' => 104,
		'Index_talk' => 105,
		'Page' => 106,
		'Page_talk' => 107,
		'Em_tradução' => 108,
		'Discussão_Em_tradução' => 109,
		'Discussão_em_tradução' => 109,
	),
	'+rowiki' => array(
		'Discuţie' => 	NS_TALK,
		'Discuţie_Utilizator' => 	NS_USER_TALK,
		'Discuţie_Wikipedia' => 	NS_PROJECT_TALK,
		'Fişier' => 	NS_FILE,
		'Imagine' => 	NS_FILE,
		'Discuţie_Fişier' => 	NS_FILE_TALK,
		'Discuţie_Format' => 	NS_TEMPLATE_TALK,
		'Discuţie_MediWiki' => 	NS_MEDIAWIKI_TALK,
		'Discuţie_Ajutor' => 	NS_HELP_TALK,
		'Discuţie_Categorie' => 	NS_CATEGORY_TALK,
		'Discuţie_Portal' => 101,
		'Discuţie_Proiect' => 103,
	),
	'+rowikinews' => array(
		'Discuţie' => 	NS_TALK,
		'Discuţie_Utilizator' => 	NS_USER_TALK,
		'Wikiştiri' => NS_PROJECT,
		'Wikinews' => NS_PROJECT,
		'Discuţie_Wikiştiri' => 	NS_PROJECT_TALK,
		'Fişier' => 	NS_FILE,
		'Imagine' => 	NS_FILE,
		'Discuţie_Fişier' => 	NS_FILE_TALK,
		'Discuţie_Format' => 	NS_TEMPLATE_TALK,
		'Discuţie_MediWiki' => 	NS_MEDIAWIKI_TALK,
		'Discuţie_Ajutor' => 	NS_HELP_TALK,
		'Discuţie_Categorie' => 	NS_CATEGORY_TALK,
	),
	'+rowikibooks' => array(
		'Wikibooks' => NS_PROJECT,
		'Discuţie_Wikibooks' => NS_PROJECT_TALK,
	),
	'+rowikisource' => array(
		'Author' => 102,
		'Author_talk' => 103,
		'Page' => 104,
		'Page_talk' => 105,
		'Index' => 106,
		'Index_talk' => 107,
	),
	'+ruwiki' => array(
		'У' => NS_USER,           // Bug 42511
		'Ш' => NS_TEMPLATE,       // Bug 42511
		'T' => NS_TEMPLATE,       // Bug 42511
		'К' => NS_CATEGORY,       // Bug 42511
		'ВП' => NS_PROJECT,       // Bug 12320
		'И' => 102,
		'ПРО' => 104,             // Bug 34124
		'АК' => 106,              // Bug 34527

		'Участница' => NS_USER,                    // Bug 12320
		'Обсуждение_участницы' => NS_USER_TALK,
		'Incubator' => 102,
		'Incubator_talk' => 103,
		'Wikiproject' => 104,                      // Bug 34124
		'Wikiproject_talk' => 105,
		'Arbcom' => 106,                           // Bug 34527
	),
	'+ruwikibooks' => array(
		'ВУ' => NS_PROJECT,
	),
	'+ruwikinews' => array(
		'ВикиНовости' => NS_PROJECT,
		'ВН' => NS_PROJECT,
		'П' => 100,
		'Обсуждение_ВикиНовостей' => NS_PROJECT_TALK,
	),
		'ruwikiquote' => array(
				'ВЦ' => NS_PROJECT,
		),
	'+ruwikisource' => array(
		'Page' => 104,
		'Page_talk' => 105,
		'Index' => 106,
		'Index_talk' => 107,
	),
	'+ruwiktionary' => array(
		'Appendix' => 100,
		'Appendix_talk' => 101,
		'Concordance' => 102,
		'Concordance_talk' => 103,
		'Index' => 104,
		'Index_talk' => 105,
		'Rhymes' => 106,
		'Rhymes_talk' => 107,
	),
	'+sawiki' => array(
		'WP' => NS_PROJECT,
		'WT' => NS_PROJECT_TALK,
		'Wikipedia' => NS_PROJECT,
		'Wikipedia_talk' => NS_PROJECT_TALK,
	),
	'+scnwiktionary' => array(
		'Wiktionary' => NS_PROJECT,
	),
	'+sewiki' => array(
		'WP' => NS_PROJECT,                //Bug 40436
		'Temasiidu' => 100,                //Bug 39206
		'Temasiidoságastallan' => 101,     //Bug 39206
	),
	'+simplewiki' => array(
		'WP' => NS_PROJECT,
	),
	'+simplewikiquote' => array(
		'WQ' => NS_PROJECT,
	),
	'+simplewiktionary' => array(
		'WT' => NS_PROJECT,
	),
	'+siwiki' => array(
		'Wikipedia' => NS_PROJECT,
		'Wikipedia_talk' => NS_PROJECT_TALK,
		'Portal'	      => 100,
		'Portal_talk' => 101,
	),
	'+siwikibooks' => array(
		'Wikibooks' => NS_PROJECT,
		'Wikibooks_talk' => NS_PROJECT_TALK,
	),
	'+siwiktionary' => array(
		'Wiktionary' => NS_PROJECT,
		'Wiktionary_talk' => NS_PROJECT_TALK,
	),
	'+slwikisource' => array(
		'Page' => 100,
		'Page_talk' => 101,
		'Index' => 104,
		'Index_talk' => 105,
	),
	'+sourceswiki' => array(
		'Page' => 104,
		'Page_talk' => 105,
		'Index' => 106,
		'Index_talk' => 107,
	),
	'+sqwikinews' => array(
		'WL' => NS_PROJECT,
	),
	'+srwiki' => array(
		'Vikipedija' => NS_PROJECT,
	),
	'+svwiki' => array(
		'WP' => NS_PROJECT,
	),
	'+svwikinews' => array(
		'WN' => NS_PROJECT,
	),
	'+svwikisource' => array(
		'Page' => 104,
		'Page_talk' => 105,
		'Author' => 106,
		'Author_talk' => 107,
		'Index' => 108,
		'Index_talk' => 109,
	),
	'+svwiktionary' => array(
		'WT' => NS_PROJECT,
		'WT-diskussion' => NS_PROJECT_TALK,
	),
	'+swwiki' => array(
		'Portal' => 100,
		'Portal_talk' => 101,
	),
	'+tawiki' => array(
		 'Wikipedia' => NS_PROJECT,
		 'Wikipedia_talk' => NS_PROJECT_TALK,
	),
	'+testwiki' => array(
		'WP' => NS_PROJECT,
		'WT' => NS_PROJECT_TALK,
	 ),
	'+tewiki' => array(
		 'Wikipedia' => NS_PROJECT,
		 'Wikipedia_talk' => NS_PROJECT_TALK,
	),
	'+tewikisource' => array(
		'Author' => 102,
		'Author_talk' => 103,
		'Page' => 104,
		'Page_talk' => 105,
		'Index' => 106,
		'Index_talk' => 107,
				'పేజీ' => 104,
				'పేజీ_చర్చ' => 105,
	),
	'+tewiktionary' => array(
		 'Wiktionary' => NS_PROJECT, # backcompat for shell req bug 36533
		 'Wiktionary_చర్చ' => NS_PROJECT_TALK, # backcompat for shell req bug 36533
	),
	'+thwikibooks' => array(
		'Wikibooks' => NS_PROJECT,                  // Bug 40717
		'คุยเรื่องWikibooks' => NS_PROJECT_TALK,
	),
	'+thwiktionary' => array(
		'Wiktionary' => NS_PROJECT,                 // Bug 40717
		'คุยเรื่องWiktionary' => NS_PROJECT_TALK,
	),
	'+tkwiktionary' => array(
		'Wiktionary' => NS_PROJECT,
	),
	'+trwikibooks' => array(
		'VK' => NS_PROJECT,
		'VÇ' => 110,
		'KAT' => NS_CATEGORY,
				'KİT' => 112,
	),
	'+trwikisource' => array(
		'Author' => 100,
		'Author_talk' => 101,
		'VikiKaynak' => NS_PROJECT, // Bug 42721
		'Oluşturuluyor VikiKaynak tartışma' => NS_PROJECT_TALK,
	),
	'+ttwiki' => array(
		'WP' => NS_PROJECT,
		'ВП' => NS_PROJECT,
		'Wikipedia' => NS_PROJECT,
		'Wikipedia_talk' => NS_PROJECT_TALK,
	),
	'+ukwiki' => array(
		'ВП' => NS_PROJECT,
	),
	'+ukwikibooks' => array(
		'ВП'  => NS_PROJECT,
	),
	'+ukwikiquote' => array(
		'ВЦ' => NS_PROJECT,
	),
	'+ukwikisource' => array(
		'ВД' => NS_PROJECT,
	),
	'+ukwiktionary' => array(
		'Wiktionary' => NS_PROJECT,
		'ВС' => NS_PROJECT,
	),
	'+urwiki' => array(
		'Portal' => 100, // Bug 19569
		'Portal_talk' => 101, // Bug 19569
	),
	'+vecwikisource' => array(
		'Author' => 100,
		'Author_talk' => 101,
		'Page' => 102,
		'Page_talk' => 103,
		'Index'      => 104,
		'Index_talk' => 105,
	),
	'+wikidatawiki' => array(
		'WD' => NS_PROJECT,      // Bug 41834
		'WT' => NS_PROJECT_TALK,
	),
	'+yiwiki' => array(
		'וויקיפעדיע' => NS_PROJECT,
		'וויקיפעדיע_רעדן' => NS_PROJECT_TALK,
	),
	'+yiwikisource' => array(
		'וויקיביבליאטעק' => NS_PROJECT,
		'וויקיביבליאטעק_רעדן' => NS_PROJECT_TALK
	),
	'+yiwiktionary' => array(
		'וויקיווערטערבוך' => NS_PROJECT,
		'וויקיווערטערבוך_רעדן' => NS_PROJECT_TALK
	),
	'+yiwikinews' => array(
		'וויקינייעס' => NS_PROJECT,
		'וויקינייעס_רעדן' => NS_PROJECT_TALK
	),
	'+yowiki' => array(
			'Portal' => 100,
			'Portal_talk' => 101,
			'Book' => 108,
			'Book_talk' => 109,
	),
	'+vecwiki' => array(
		'WP' => NS_PROJECT,
		'Immagine' => NS_IMAGE,
	),
	'+viwikibooks' => array(
		'Subject' => 102,
		'Subject_talk' => 103,
		'Wikijunior' => 104,
		'Wikijunior_talk' => 105,
		'Cookbook' => 106,
		'Cookbook_talk' => 107,
	),
	'+viwikisource' => array(
	   'Portal'     => 100,
	   'Portal_talk'    => 101,
	   'Author'     => 102,
	   'Author_talk'    => 103,
	   'Page'       => 104,
	   'Page_talk'  => 105,
	   'Index'      => 106,
	   'Index_talk' => 107,
	),
	'+vowiki' => array(
		'Wikipedia' => NS_PROJECT,
		'Wikipedia_talk' => NS_PROJECT_TALK,
	),
	'+vowiktionary' => array(
		'Wiktionary' => NS_PROJECT,
		'Wiktionary_talk' => NS_PROJECT_TALK,
	),
	'+zh_classicalwiki' => array(
		'Wikipedia' => NS_PROJECT,
		'Wikipedia_talk' => NS_PROJECT,
	),
	'+zhwiki' => array(
		'维基百科' => NS_PROJECT,
		'維基百科' => NS_PROJECT,
		'WP' => NS_PROJECT,
		'维基百科讨论' => NS_PROJECT_TALK,
		'维基百科对话' => NS_PROJECT_TALK,
		'維基百科討論' => NS_PROJECT_TALK,
		'維基百科對話' => NS_PROJECT_TALK,
		'T' => NS_TEMPLATE,
		'WT' => NS_PROJECT_TALK,
		'CAT' => NS_CATEGORY,
		'H' => NS_HELP,
		'P' => 100,
	),
	'+zhwikibooks' => array(
		'维基教科书' => NS_PROJECT,
		'維基教科書' => NS_PROJECT,
		'WB' => NS_PROJECT,
	),
	'+zhwikisource' => array(
		'作者' => 102, // Author
		'作者讨论' => 103, // Author talk
		'作者討論' => 103,
	),
	'+zh_yuewiki' => array(
		'WP' => NS_PROJECT,
		'WT' => NS_PROJECT_TALK,
		'T' => NS_TEMPLATE,
		'H' => NS_HELP,
		'P' => 100,
		# 'PT' => 101,
		'Wikipedia_talk' => NS_PROJECT_TALK,
		'MediaWiki_talk' => NS_MEDIAWIKI_TALK,
		# Aliases for MediaWiki core "yue" namespace names.
		'媒體' => NS_MEDIA,
		'特別' => NS_SPECIAL,
		'傾偈' => NS_TALK,
		'用戶' => NS_USER,
		'用戶傾偈' => NS_USER_TALK,
		'$1_傾偈' => NS_PROJECT_TALK,
		'文件' => NS_FILE,
		'文件傾偈' => NS_FILE_TALK,
		'MediaWiki_傾偈' => NS_MEDIAWIKI_TALK,
		'模' => NS_TEMPLATE,
		'模傾偈' => NS_TEMPLATE_TALK,
		'幫手' => NS_HELP,
		'幫手傾偈' => NS_HELP_TALK,
		'分類' => NS_CATEGORY,
		'分類傾偈' => NS_CATEGORY_TALK,
	),
),
# @} end of wgNamespaceAliases

# wgNamespacesWithSubpages @{
'wgNamespacesWithSubpages' => array(
	'default' => array(
		NS_TALK			/* 1 */  => true,
		NS_USER			/* 2 */  => true,
		NS_USER_TALK		/* 3 */  => true,
		NS_PROJECT		/* 4 */  => true,
		NS_PROJECT_TALK		/* 5 */  => true,
		NS_FILE_TALK		/* 7 */  => true,
		NS_MEDIAWIKI		/* 8 */  => true,
		NS_MEDIAWIKI_TALK	/* 9 */  => true,
		NS_TEMPLATE		/* 10 */ => true,
		NS_TEMPLATE_TALK	/* 11 */ => true,
		NS_HELP			/* 12 */ => true,
		NS_HELP_TALK		/* 13 */ => true,
		NS_CATEGORY_TALK	/* 15 */ => true,
		// extra namespaces usually want subpages
		100               => true,
		101               => true,
		102               => true,
		103               => true,
		104               => true,
		105               => true,
		106               => true,
		107               => true,
		108               => true,
		109               => true,
		110               => true,
		111               => true,
	),

	// Wikipedia @{
	'+cswiki' => array( 6 => 0, 14 => 1 ),
	'+dewiki' => array( 6 => 0, 8 => 0, 10 => 0 ),
	'+enwiki' => array( 6 => 0, 8 => 0 ),
	'+eowiki' => array( 0 => 1, 6 => 0, 8 => 0 ),
	'+eswiki' => array( 6 => 0, 8 => 0 ),
	'+frwiki' => array( 6 => 0, 8 => 0 ),
	'+guwiki' => array( 6 => 0, 8 => 0, 14 => 1 ),
	'+huwiki' => array( 6 => 0, 8 => 0, 14 => 1 ),
	'+idwiki' => array( 6 => 0, 8 => 0 ),
	'+itwiki' => array( 6 => 0, 8 => 0, 14 => 0 ),
	'+kkwiki' => array( 6 => 0, 8 => 0, 12 => 0 ),
	'+ltwiki' => array( 6 => 0, 8 => 0 ),
	'+plwiki' => array( 6 => 0, 8 => 0, 14 => 1 ),
	'+rmwiki' => array( 0 => 1 ),
	'+slwiki' => array( 6 => 0, 8 => 0, 14 => 1 ),
	'+tenwiki' => array( 0 => 1 ),
	'+zh_yuewiki' => array( 6 => 0, 8 => 0, 14 => 1 ),
	'+zh_min_nanwiki' => array( 6 => 0, 8 => 0, 10 => 0 ),
	// @}

	// Specials wiki @{
	'+arbcom_enwiki' => array( 0 => 1, 6 => 0, 8 => 0, 10 => 0 ),
	'+auditcomwiki' => array( 0 => 1, 6 => 0, 8 => 0, 10 => 0 ),
	'+bewikimedia' => array( 0 => 1 ),
	'+boardwiki' => array( 0 => 1, 6 => 0, 8 => 0, 10 => 0 ),
	'+brwikimedia' => array( 0 => 1 ),
	'+chairwiki' => array( 0 => 1, 6 => 0, 8 => 0, 10 => 0 ),
	'+chapcomwiki' => array( 0 => 1, 6 => 0, 8 => 0, 10 => 0 ),
	'+checkuserwiki' => array( 0 => 1 ),
	'+collabwiki' => array( 0 => 1 ),
	'+commonswiki' => array( 6 => 0, 8 => 0 ),
	'+execwiki' => array( 0 => 1, 6 => 0, 8 => 0, 10 => 0 ),
	'+foundationwiki' => array( 0 => 1, 6 => 0, 8 => 0 ),
	'+grantswiki' => array( 0 => 1, 6 => 0, 8 => 0, 10 => 0 ),
	'+incubatorwiki' => array( 0 => 1, 6 => 0, 14 => 1 ),
	'+internalwiki' => array( 0 => 1, 6 => 0, 8 => 0, 10 => 0 ),
	'+mediawikiwiki' => array( 0 => 1, 6 => 0, 8 => 0, 14 => 1 ),
	'+metawiki' => array( 0 => 1, 6 => 0, 8 => 0, 200 => 1, 201 => 1, 202 => 1, 203 => 1 ),
	'+movementroleswiki' => array( 0 => 1 ),
	'+nlwikimedia' => array( 0 => 1 ),
	'+nostalgiawiki' => array( 0 => 1, 6 => 0, 8 => 0, 10 => 0 ),
	'+nowikimedia' => array( 0 => 1 ),
	'+officewiki' => array( 0 => 1, 6 => 0, 8 => 0, 10 => 0 ),
	'+otrs_wikiwiki' => array( 0 => 1 ),
	'+outreachwiki' => array( 0 => 1 ),
	'+plwikimedia' => array( 0 => 1, 6 => 0, 8 => 0, 10 => 0, 14 => 1 ),
	'+rswikimedia' => array( 0 => 1, 6 => 0, 8 => 0, 10 => 0 ),
	'+ruwikimedia' => array( 0 => 1,  6 => 1, 7 => 0, 9 => 0, 14 => 1, 15 => 0 ),
	'+sewikimedia' => array( 0 => 1 ),
	'+stewardwiki' => array( 0 => 1, 6 => 1, 14 => 1 ),
	'+strategywiki' => array( 0 => 1 ),
	'+ukwikimedia' => array( 0 => 1, 6 => 0, 8 => 0, 10 => 0 ),
	'+usabilitywiki' => array( 0 => 1 ),
	'+wikimaniateamwiki' => array( 0 => 1, 6 => 0, 8 => 0, 10 => 0 ),
	'+wikimania2006wiki' => array( 0 => 1, 6 => 0, 8 => 0, 10 => 0 ),
	'+wikimania2007wiki' => array( 0 => 1, 6 => 0, 8 => 0, 10 => 0 ),
	'+wikimania2008wiki' => array( 0 => 1, 6 => 0, 8 => 0, 10 => 0 ),
	'+wikimania2009wiki' => array( 0 => 1, 6 => 0, 8 => 0, 10 => 0 ),
	'+wikimania2010wiki' => array( 0 => 1, 6 => 0, 8 => 0, 10 => 0 ),
	'+wikimania2011wiki' => array( 0 => 1, 6 => 0, 8 => 0 ),
	'+wikimania2012wiki' => array( 0 => 1, 6 => 0, 8 => 0 ),
	'+wikimania2013wiki' => array( 0 => 1, 6 => 0, 8 => 0 ),
	// @}

	// Wikibooks @{
	'+wikibooks' => array( 0 => 1, 6 => 0, 8 => 0, 10 => 0 ),
	'+cswikibooks' => array( 0 => 1, 6 => 0, 14 => 1 ),
	'+enwikibooks' => array( 0 => 1, 6 => 0, 8 => 0, 14 => 1 ),
	'+eswikibooks' => array( 0 => 1, 6 => 0, 8 => 0 ),
	'+idwikibooks' => array( 0 => 1, 6 => 0, 8 => 0, 10 => 0 ),
	'+itwikibooks' => array( 0 => 1, 6 => 0, 8 => 0 ),
	'+plwikibooks' => array( 0 => 1, 6 => 0, 8 => 0, 14 => 1 ),
	'+ptwikibooks' => array( 0 => 1, 6 => 0, 8 => 0, 14 => 1 ),
	'+siwikibooks' => array( 112 => 1, 113 => 1, 114 => 1, 115 => 1 ),
	'+ukwikibooks' => array( 0 => 1, 6 => 0, 8 => 0, 14 => 1 ),
	// @}

	// Wikisource @{
	'+wikisource' => array( 0 => 1, 6 => 0, 8 => 0 ),
	'+cswikisource' => array( 14 => 1 ),
	'+huwikisource' => array( 10 => 0 ),
	'+nowikisource' => array( 10 => 0 ),
	'+ruwikisource' => array( 10 => 0 ),
	'+trwikisource' => array( 10 => 0 ),
	'+viwikisource' => array( 10 => 0 ),
	'+zhwikisource' => array( 10 => 0 ),
	// @}

	// Wikiversity @{
	'+arwikiversity' => array( 0 => 1, 6 => 1, 10 => 0 ),
	'+betawikiversity' => array( 0 => 1, 10 => 0 ),
	'+cswikiversity' => array( 0 => 1, 6 => 0, 14 => 1 ),
	'+dewikiversity' => array( 0 => 1, 6 => 0, 8 => 0, 10 => 0 ),
	'+enwikiversity' => array( 0 => 1, 6 => 0, 8 => 0, 10 => 0 ),
	'+fiwikiversity' => array( 0 => 1, 6 => 0, 8 => 0, 10 => 0 ),
	'+frwikiversity' => array( 0 => 1, 6 => 0, 8 => 0, 10 => 0, 112 => 1, 113 => 1 ),
	'+itwikiversity' => array( 0 => 1, 6 => 0, 8 => 0, 14 => 0 ),
	'+ptwikiversity' => array( 0 => 1, 10 => 0 ),
	'+ruwikiversity' => array( 0 => 1, 6 => 1, 14 => 1 ),
	'+svwikiversity' => array( 0 => 1 ),
	// @}

	// Wikiquote
	'+cswikiquote' => array( 0 => 1, 6 => 0, 14 => 1 ),
	'+plwikiquote' => array( 6 => 0, 8 => 0, 14 => 1 ),

	// Wikinews
	'+cswikinews' => array( 0 => 1, 6 => 0, 14 => 1 ),
	'+plwikinews' => array( 6 => 0, 8 => 0, 14 => 1 ),

	// Wiktionary
	'+cswiktionary' => array( 0 => 1, 6 => 0, 14 => 1 ),
	'+iswiktionary' => array( 0 => 1, 6 => 0, 8 => 0, 14 => 1 ),
	'+ltwiktionary' => array( 6 => 0, 8 => 0, 14 => 1 ),
	'+plwiktionary' => array( 6 => 0, 8 => 0, 14 => 1 ),

	// Labs
	'+en_labswikimedia' => array( 0 => 1, 6 => 0, 8 => 0, 10 => 0 ),
	'+de_labswikimedia' => array( 0 => 1, 6 => 0, 8 => 0, 10 => 0 ),

	// Wikivoyage
	'+dewikivoyage' => array( 0 => 1, 100 => 0, 104 => 0 ),
	'+itwikivoyage' => array( 0 => 1, 100 => 0, 104 => 0 ),
),
# @} end of wgNamespacesWithSubpages

# wgProofreadPageNamespaceIds @{
'wgProofreadPageNamespaceIds' => array(
	'sourceswiki' => array(
		'page' => 104,
		'index' => 106,
	),
	'enwikisource' => array(
		'page' => 104,
		'index' => 106,
	),
	'arwikisource' => array(
		'page' => 104,
		'index' => 106,
	),
	'aswikisource' => array( // Bug 43129
		'page' => 104,
		'index' => 106,
	),
	'bewikisource' => array(
		'page' => 104,
		'index' => 106,
	),
	'bnwikisource' => array(
		'page' => 104,
		'index' => 102,
	),
	'brwikisource' => array(
		'page' => 102,
		'index' => 100,
	),
	'cawikisource' => array(
		'page' => 102,
		'index' => 104,
	),
	'dawikisource' => array(
		'page' => 104,
		'index' => 106,
	),
	'dewikisource' => array(
		'page' => 102,
		'index' => 104,
	),
	'elwikisource' => array(
		'page' => 100,
		'index' => 102,
	),
	'eowikisource' => array(
		'page' => 104,
		'index' => 106,
	),
	'eswikisource' => array(
		'page' => 102,
		'index' => 104,
	),
	'etwikisource' => array(
		'page' => 102,
		'index' => 104,
	),
	'fawikisource' => array(
		'page' => 104,
		'index' => 106,
	),
	'frwikisource' => array(
		'page' => 104,
		'index' => 112,
	),
	'frrwiki' => array(
		'page' => 102,
		'index' => 104
	),
	'guwikisource' => array(
		'page' => 104,
		'index' => 106
	),
	'hewikisource' => array(
		'page' => 104,
		'index' => 112,
	),
	'hrwikisource' => array(
		'page' => 102,
		'index' => 104,
	),
	'huwikisource' => array(
		'page' => 104,
		'index' => 106,
	),
	 'hywikisource' => array(
		'page' => 104,
		'index' => 106,
	),
	'idwikisource' => array(
		'page' => 104,
		'index' => 102,
	),
	'itwikisource' => array(
		'page' => 108,
		'index' => 110,
	),
	'knwikisource' => array(
		'page' => 104,
		'index' => 106,
	),
	'lawikisource' => array(
		'page' => 104,
		'index' => 106,
	),
	'mlwikisource' => array(
		'page' => 106,
		'index' => 104,
	),
	'mrwikisource' => array(
		'page' => 104,
		'index' => 106,
	),
	'nlwikisource' => array(
		'page' => 104,
		'index' => 106
	),
	'nowikisource' => array(
		'page' => 104,
		'index' => 106
	),
	'plwikisource' => array(
		'page' => 100,
		'index' => 102,
	),
	'ptwikisource' => array(
		'page' => 106,
		'index' => 104,
	),
	'rowikisource' => array(
		'page' => 104,
		'index' => 106,
	),
	'ruwikisource' => array(
		'page' => 104,
		'index' => 106,
	),
	'sawikisource' => array(
		'page' => 104,
		'index' => 106,
	),
	'slwikisource' => array(
		'page' => 100,
		'index' => 104,
	),

	'svwikisource' => array(
		'page' => 104,
		'index' => 108,
	),
	'tewikisource' => array(
		'page' => 104,
		'index' => 106,
	),
	'vecwikisource' => array(
		'page' => 102,
		'index' => 104,
	),
	'viwikisource' => array(
		'page' => 104,
		'index' => 106,
	),
	'zhwikisource' => array(
		'page' => 104,
		'index' => 106,
	),
),
# @} end of wgProofreadPageNamespaceIds

# wgUseDynamicDates @{
'wgUseDynamicDates' => array(
	'default' => false,
	'metawiki'  => true,
	'enwikiquote' => true,
	'enwiktionary' => true,
	'enwikibooks' => true,
	'enwikinews' => true,
	'commonswiki' => true,
	'testwiki' => true,
),
# @} end of wgUseDynamicDates

'wgRawHtml' => array(
	'default' => false,
	'donatewiki' => true,
	'foundationwiki' => true,
	'internalwiki' => true,
	'collabwiki' => true, // bug 29269
),

# wgWhitelistRead @{
'wgWhitelistRead' => array(
	'private' => array( 'Main Page', 'Special:Userlogin', 'Special:Userlogout', '-', 'MediaWiki:Monobook.css', 'MediaWiki:Monobook.js' ),
	'grantswiki' => array( 'Main Page', 'Special:Userlogin', 'Special:Userlogout' ),
	'wikimaniawiki' => array( 'Main Page', 'Special:Userlogin', 'Special:Userlogout', // back compat
		'-',
		'MediaWiki:Wikimania.js',
		'MediaWiki:Wikimania.css',
		'Image:Citymap frankfurt.png',
		'Accomodation',
		'Accommodation',
		'Remote participation',
		'Competitions',
		'Accommodation/Ar',
		'Accueil',
		'Activités',
		'Alloggio',
		'Alvoko por kontribuoj',
		'Anreise',
		'Artikelen gevraagd',
		'Begleitprogramm',
		'Calendar',
		'Call for Papers',
		'Call for papers',
		'Call for papers/de',
		'Call for papers/fr',
		'Competencias',
		'Competities',
		'Competitions',
		'Competizioni',
		'Compétitions',
		'Conferencistas',
		'Contact',
		'Contact/fr',
		'Contact/nl',
		'Contactos',
		'Contatti',
		'Coordinators',
		'Demandu aliĝilon',
		'Eventi sociali',
		'Eventoj',
		'Eventos sociales',
		'Fun and Games',
		'Gazetara servo',
		'Gvidiloj',
		'Hacking Days',
		'Hacking Days/ar',
		'Hacking Days/de',
		'Hacking Days/eo',
		'Hacking Days/es',
		'Hacking Days/fr',
		'Hacking Days/it',
		'Hacking Days/nl',
		'Hacking Days/pl',
		'Hackings Days-It',
		'Hackings Days/it',
		'Hauptseite',
		'Hoofdpagina',
		'Hospedaje',
		'Hébergement',
		'Intervenants',
		'Konferencejo',
		'Konkursoj',
		'Konkursy',
		'Kontakt',
		'Kontaktoj',
		'Kontakty',
		'Lieu',
		'Localidad',
		'Locatie',
		'Location',
		'Location/ar',
		'Logxado',
		'Lokatie',
		'Loĝejoj',
		'Luogo',
		'Main Page',
		'Mecenatoj',
		'Miejsce konferencji',
		'Noclegi',
		'Oproep voor Inzendingen',
		'Oratori',
		'Pagina principale',
		'Patrocinantes',
		'Pers',
		'Portada',
		'Prasa',
		'Prelegontoj',
		'Prensa',
		'Press',
		'Presse/de',
		'Presse/fr',
		'Program',
		'Programa',
		'Programm',
		'Programm/de',
		'Programma',
		'Programma/nl',
		'Programme',
		'Programme/fr',
		'Programo',
		'Redner',
		'Richiesta di documenti',
		'Social events',
		'Sociale activiteiten',
		'Solicitud de trabajos',
		'Speakers',
		'Speakers/ar',
		'Special thanks',
		'Sponsor/It',
		'Sponsoren',
		'Sponsors',
		'Sponsors/fr',
		'Sponsors/nl',
		'Sponsorzy',
		'Spotkania towarzyskie',
		'Sprekers',
		'Stampa',
		'Strona główna',
		'Transport',
		'Transport/fr',
		'Transport/pl',
		'Transporte',
		'Transportoj',
		'Trasporti',
		'Unterkunft',
		'Veranstaltungsort',
		'Verblijf',
		'Vervoer',
		'Wettbewerbe',
		'Wykładowcy',
		'Zgłoszenie prezentacji',
		'Ĉefpaĝo',
		'コンテスト',
		'ハッキング・デイズ',
		'プログラム',
		'メインページ',
		'交流行事',
		'交通機関',
		'報道',
		'宿泊施設',
		'後援',
		'発表募集要項',
		'発表申し込み受付',
		'発表者',
		'連絡',
		'Transport/Ar',
		'Accomodation/Ar',
		'Main Page/Ar',
		'首页',
		'黑客日',
		'位置',
		'住宿',
		'讲演者',
		'交通',
		'Informacja o wizach',
		'Visa/es',
		'Special thanks',
		'Frankfurt map',
		'Visa information',
		'Visa/fr',
		'Template:Navigation',
		'Template:NavigationDe',
		'FAQ',
		'FAQ/de',
		'FAQ/es',
		'FAQ/fr',
		'FAQ/it',
		'FAQ/nl',
		'FAQ/pl',
		'Informazioni sul visto',
		'FAQIt',
		'Ringraziamenti',
		'Coordinatori',
		'Visa information/Ar',
		'Contact/Ar',
		'Call for papers/Ar',
		'Contact/Zh',
		'Visa/Zh',
		'Call for papers/Zh',
		'Visum',
		'Приёмная',
		'Template:IntroRu',
		'Template:BrowseRu',
		'Template:OrganisationRu',
		'Template:Mini-calendarRu',
		'Reservation policy',
		"Conditions de réservation",
		'Condizioni per la prenotazione',
		'User talk:Aphaia/Public'
	),
	'wikimania2005wiki' => array( 'Main Page', 'Special:Userlogin', 'Special:Userlogout', // back compat
		'-',
		'MediaWiki:Wikimania.js',
		'MediaWiki:Wikimania.css',
		'Image:Citymap frankfurt.png',
		'Accomodation',
		'Accommodation',
		'Remote participation',
		'Competitions',
		'Accommodation/Ar',
		'Accueil',
		'Activités',
		'Alloggio',
		'Alvoko por kontribuoj',
		'Anreise',
		'Artikelen gevraagd',
		'Begleitprogramm',
		'Calendar',
		'Call for Papers',
		'Call for papers',
		'Call for papers/de',
		'Call for papers/fr',
		'Competencias',
		'Competities',
		'Competitions',
		'Competizioni',
		'Compétitions',
		'Conferencistas',
		'Contact',
		'Contact/fr',
		'Contact/nl',
		'Contactos',
		'Contatti',
		'Coordinators',
		'Demandu aliĝilon',
		'Eventi sociali',
		'Eventoj',
		'Eventos sociales',
		'Fun and Games',
		'Gazetara servo',
		'Gvidiloj',
		'Hacking Days',
		'Hacking Days/ar',
		'Hacking Days/de',
		'Hacking Days/eo',
		'Hacking Days/es',
		'Hacking Days/fr',
		'Hacking Days/it',
		'Hacking Days/nl',
		'Hacking Days/pl',
		'Hackings Days-It',
		'Hackings Days/it',
		'Hauptseite',
		'Hoofdpagina',
		'Hospedaje',
		'Hébergement',
		'Intervenants',
		'Konferencejo',
		'Konkursoj',
		'Konkursy',
		'Kontakt',
		'Kontaktoj',
		'Kontakty',
		'Lieu',
		'Localidad',
		'Locatie',
		'Location',
		'Location/ar',
		'Logxado',
		'Lokatie',
		'Loĝejoj',
		'Luogo',
		'Main Page',
		'Mecenatoj',
		'Miejsce konferencji',
		'Noclegi',
		'Oproep voor Inzendingen',
		'Oratori',
		'Pagina principale',
		'Patrocinantes',
		'Pers',
		'Portada',
		'Prasa',
		'Prelegontoj',
		'Prensa',
		'Press',
		'Presse/de',
		'Presse/fr',
		'Program',
		'Programa',
		'Programm',
		'Programm/de',
		'Programma',
		'Programma/nl',
		'Programme',
		'Programme/fr',
		'Programo',
		'Redner',
		'Richiesta di documenti',
		'Social events',
		'Sociale activiteiten',
		'Solicitud de trabajos',
		'Speakers',
		'Speakers/ar',
		'Special thanks',
		'Sponsor/It',
		'Sponsoren',
		'Sponsors',
		'Sponsors/fr',
		'Sponsors/nl',
		'Sponsorzy',
		'Spotkania towarzyskie',
		'Sprekers',
		'Stampa',
		'Strona główna',
		'Transport',
		'Transport/fr',
		'Transport/pl',
		'Transporte',
		'Transportoj',
		'Trasporti',
		'Unterkunft',
		'Veranstaltungsort',
		'Verblijf',
		'Vervoer',
		'Wettbewerbe',
		'Wykładowcy',
		'Zgłoszenie prezentacji',
		'Ĉefpaĝo',
		'コンテスト',
		'ハッキング・デイズ',
		'プログラム',
		'メインページ',
		'交流行事',
		'交通機関',
		'報道',
		'宿泊施設',
		'後援',
		'発表募集要項',
		'発表申し込み受付',
		'発表者',
		'連絡',
		'Transport/Ar',
		'Accomodation/Ar',
		'Main Page/Ar',
		'首页',
		'黑客日',
		'位置',
		'住宿',
		'讲演者',
		'交通',
		'Informacja o wizach',
		'Visa/es',
		'Special thanks',
		'Frankfurt map',
		'Visa information',
		'Visa/fr',
		'Template:Navigation',
		'Template:NavigationDe',
		'FAQ',
		'FAQ/de',
		'FAQ/es',
		'FAQ/fr',
		'FAQ/it',
		'FAQ/nl',
		'FAQ/pl',
		'Informazioni sul visto',
		'FAQIt',
		'Ringraziamenti',
		'Coordinatori',
		'Visa information/Ar',
		'Contact/Ar',
		'Call for papers/Ar',
		'Contact/Zh',
		'Visa/Zh',
		'Call for papers/Zh',
		'Visum',
		'Приёмная',
		'Template:IntroRu',
		'Template:BrowseRu',
		'Template:OrganisationRu',
		'Template:Mini-calendarRu',
		'Reservation policy',
		"Conditions de réservation",
		'Condizioni per la prenotazione',
		'User talk:Aphaia/Public'
	),
	'ilwikimedia' => array(
		'-',
		'מדיה ויקי:Monobook.css',
		'מדיה ויקי:Monobook.js',
		'מדיה ויקי:Common.css',
		'Main Page',
		'אודות ויקימדיה ישראל',
		'אודות ויקימדיה ישראל/ניווט שפות',
		'אודות ויקימדיה ישראל/אנגלית',
		'אודות ויקימדיה ישראל/ערבית',
		'אודות ויקימדיה ישראל/רוסית',
		'בעלי תפקידים',
		'אודות ויקימדיה ישראל/ניווט שפות',
		'בעלי תפקידים/אנגלית',
		'בעלי תפקידים/ערבית',
		'בעלי תפקידים/רוסית',
		'יצירת קשר',
		'יצירת קשר/ניווט שפות',
		'יצירת קשר/אנגלית',
		'יצירת קשר/ערבית',
		'יצירת קשר/רוסית',
		'לוח',
		'לוח אירועים',
		'לוח מודעות',
		'עמוד ראשי',
		'עמוד ראשי/ניווט שפות',
		'עמוד ראשי/English',
		'עמוד ראשי/العربية',
		'עמוד ראשי/Русский',
		'קרן ויקימדיה',
		'קרן ויקימדיה/ניווט שפות',
		'קרן ויקימדיה/אנגלית',
		'קרן ויקימדיה/ערבית',
		'קרן ויקימדיה/רוסית',
		'תקנון העמותה',
		'תקנון העמותה/ניווט שפות',
		'תקנון העמותה/אנגלית',
		'תקנון העמותה/ערבית',
		'תקנון העמותה/רוסית',
		'תרומה לוויקימדיה ישראל',
		'תרומה לוויקימדיה ישראל/ניווט שפות',
		'תרומה לוויקימדיה ישראל/אנגלית',
		'תרומה לוויקימדיה ישראל/ערבית',
		'תרומה לוויקימדיה ישראל/רוסית',
# More added 2007-07-25
		'משתמש:Deroravi',
		'משתמש:DrorK',
		'משתמש:Harel',
		'משתמש:Itzik',
		'משתמש:Rotemdanzig',
		'משתמש:Shayakir',
		'משתמש:עוזי ו.',
		'משתמש:עידן ד',
		'משתמש:עמית אבידן',
		'לוח אירועים/Русский',
		'לוח אירועים/English',
		'לוח אירועים/ערבית',
		'ויקימדיה:הצטרפות לעמותה',
		'מדוע כדאי להיות חבר בעמותה',
		'שאלות ותשובות לגבי חברות בעמותה',
		'ויקימדיה:פרויקטים',
		'יצירת קשר',
		'משפטית',
		'מדיניות_הפרטיות',
		'ויקימדיה:הודעות_לעיתונות',
		'ויקימדיה:פרוייקטים',
		'הצטרפות_לעמותה',
		'משתמש:עידן_ד',
		'משתמש:עוזי_ו',
		# More added by bug 11639
		'ויקימדיה:תקנון העמותה',
		'ויקימדיה:חדר עיתונות',
		'ויקימדיה:חוק זכויות יוצרים',
		'ויקימדיה:הודעות לעיתונות/התנגדות לחוק',
		'זכויות יוצרים',
		'ויקימדיה:למה להירשם לעמותה? המדריך',
		'לוויקיפד',
		'ויקימדיה:הודעות לעיתונות/הצלחה לעמותה',
		'במאבק זכויות יוצרים',
		'ויקימדיה:הודעות לעיתונות/כנס ויקימאניה',
		'ויקימדיה:הודעות לעיתונות/שיתוף פעולה studytv',
		'קטגוריה:הודעות לעיתונות',
		'ויקימדיה:לוח אירועים',
		'ויקימדיה:בעלי תפקידים',
		'ויקימדיה:לוח מודעות',
		'ויקימדיה:אודות ויקימדיה ישראל',
		'ויקימדיה:תרומה לוויקימדיה ישראל',
		'ויקימדיה:יצירת קשר',
		'ויקימדיה:קרן ויקימדיה',
		'משתמש:Costello',
	),
),
# @} end of wgWhitelistRead

'wgSkipSkin' => array(
	'default' => 'wikimania',
	'wikimaniawiki' => ''
),
'wgSkipSkins' => array(
	'default' => array( 'htmldump', 'monobookcbt', 'drsport' ),
	'dawiki' => array( 'htmldump', 'monobookcbt' ),
	'testwiki' => array( 'htmldump', 'monobookcbt', 'drsport' ),
),

'wgAutoConfirmAge' => array(
	'default' => 4 * 3600 * 24, // 4 days to pass isNewbie()

	# http://de.wikibooks.org/wiki/Wikibooks:Meinungsbilder/_Verschiebefunktion_f%C3%BCr_neue_Nutzer
	# TS 2007-02-06
	'dewikibooks' => 7 * 86400,
	'zhwiki' => 7 * 3600 * 24, // https://bugzilla.wikimedia.org/show_bug.cgi?id=14624
	'fishbowl' => 0, // No need
	'tenwiki' => 0, // bug 26554
),

'wgAutoConfirmCount' => array(
	'default' => 0,
	'arwiki' => 50, // Bug 12123
	'enwiki' => 10, // Bug 14191
	'eswiki' => 50, // Bug 13261
	'fawiki' => 10, // Bug 43630
	'itwiktionary' => 10, // Bug 22274
	'jawiki' => 10, // Bug 40270
	'plwiki' => 10,
	'ptwiki' => 10, // Bug 27954
	'ruwiki' => 15, // Bug 41831
	'simplewiki' => 10,
	'zhwiki' => 50, // Bug 14624
	'zh_yuewiki' => 10, // Bug 30538
),

# wgRestrictionLevels @{
'wgRestrictionLevels' => array(
	'default' => array( '', 'autoconfirmed', 'sysop' ), // semi-protection level on
	'ptwiki' => array( '', 'autoconfirmed', 'autoreviewer', 'sysop' ), // http://bugzilla.wikimedia.org/show_bug.cgi?id=39652
),
# @} end of wgRestrictionLevels

'wgSiteNotice' => array(
	'default' => '',
),

'wmgPrivateWiki' => array(
	'default' => false,
	'private' => true // make files private and such
),

'wmgUseDismissableSiteNotice' => array(
	'default' => true,
),

'wmgUseCentralNotice' => array(
	'advisorywiki' => false, // Per Bug # 25519
	'default' => true,
	'fishbowl' => false, // Per bug 17718 Disable CentralNotice on private/fishbowl wikis
	'fiwikimedia' => false, // bug 17718
	'metawiki' => true, // Central interface
	'private' => false, // :D
	'qualitywiki' => false,
	'testwiki' => true,
	'ukwikimedia' => false,  // Per bug 17718 Disable CentralNotice on private/fishbowl wikis
	'simplewiki' => true,
),

'wmgCentralNoticeLoader' => array(
	'default' => true, // *gulp* -- bv 2008-11-03
	'testwiki' => true,
	'metawiki' => true,
),

'wmgUseContributionReporting' => array(
	'default' => false,
	'testwiki' => true,
	'foundationwiki' => true,
),

'wmgSetNoticeHideBannersExpiration' => array(
	'default' => true,
),

// For CentralNotice project pickers
'wmgNoticeProject' => array(
	'advisorywiki' => 'wikimedia',
	'default' => '$site',
	'commonswiki' => 'commons',
	'foundationwiki' => 'wikimedia',
	'incubatorwiki' => 'wikimedia',
	'mediawikiwiki' => 'wikimedia',
	'metawiki' => 'meta',
	'outreachwiki' => 'wikimedia',
	'usabilitywiki' => 'wikimedia',
	'strategywiki' => 'wikimedia',
	'sourceswiki' => 'wikisource',
	'specieswiki' => 'wikispecies',
	'testwiki' => 'test',
	'wikimania2008wiki' => 'wikimedia',
	'wikimania2009wiki' => 'wikimedia',
	'wikimania2010wiki' => 'wikimedia',
	'wikimania2011wiki' => 'wikimedia',
	'wikimania2012wiki' => 'wikimedia',
	'wikimania2013wiki' => 'wikimedia',
	'wikidatawiki' => 'wikidata',
	'wikivoyage' => 'wikivoyage',
),

'wgReadOnly' => array(
	'default' => null,
),

'wgReadOnlyFile' => array(
	'default' => false,
),

# wgTranslateNumerals @{
'wgTranslateNumerals' => array(
	'default' => true,

	'arwiki' => false,
	'arwiktionary' => false, // bug 33758
	'aswiki' => false, # bug 31371
	# bug 3442 -ævar
	'arwikibooks' => false,
	'arwikiquote' => false,
	'arwiktionary' => false,
	'arwikisource' => false,
	'arwikiversity' => false,
	'arwikinews' => false,
	'hiwiki' => false, // http://bugzilla.wikimedia.org/show_bug.cgi?id=29279
	'tewiki' => false, # by request -- maybe remove from file
	'mlwiki' => false, # 2005-01-03, by dubious request on Ts's talk page -ævar
	'tewikibooks' => false,
	'tewikiquote' => false,
	'tewiktionary' => false,
),
# @} end of wgTranslateNumerals

'wgDebugLogFile' => array(
	'default' => '',
	'testwiki' => "udp://$wmfUdp2logDest/testwiki",
	'test2wiki' => "udp://$wmfUdp2logDest/test2wiki",
),

# wgDebugLogGroups @{
'wgDebugLogGroups' => array(
	'default' => array(
		'UserThrottle' => "udp://$wmfUdp2logDest/UserThrottle",
		'cite' => "udp://$wmfUdp2logDest/cite", // to see how it's used;) -ævar
		'thumbnail' => "udp://$wmfUdp2logDest/thumbnail",
		'jobqueue' => "udp://$wmfUdp2logDest/jobqueue/web",
		'slow-parse' => "udp://$wmfUdp2logDest/slow-parse",
		'exception' => "udp://$wmfUdp2logDest/exception",
		'session' => "udp://$wmfUdp2logDest/session",
		'logging' => "udp://$wmfUdp2logDest/logging",
		'SimpleAntiSpam' => "udp://$wmfUdp2logDest/spam",
		'AntiBot' => "udp://$wmfUdp2logDest/spam",
		'SpamBlacklistHit' => "udp://$wmfUdp2logDest/spam",
		'SpamRegex' => "udp://$wmfUdp2logDest/spam",
		'OutputBuffer' => "udp://$wmfUdp2logDest/buffer",
		'exec' => "udp://$wmfUdp2logDest/exec",
		'ExternalStoreDB' => "udp://$wmfUdp2logDest/ExternalStoreDB",
		'runJobs' => "udp://$wmfUdp2logDest/runJobs",
		'es-hit' => "udp://$wmfUdp2logDest/es-hit",
		'poolcounter' => "udp://$wmfUdp2logDest/poolcounter",
		'lc-recache' => "udp://$wmfUdp2logDest/lc-recache",
		'generated-pp-node-count' => "udp://$wmfUdp2logDest/generated-pp-node-count",
		'api' => "udp://$wmfUdp2logDest/api",
		'SQLBagOStuff' => "udp://$wmfUdp2logDest/sql-bagostuff",

		'UploadBlacklist' => "udp://$wmfUdp2logDest/upload-blacklist",
		'FileOperation' => "udp://$wmfUdp2logDest/filebackend-ops",
		'SwiftBackend' => "udp://$wmfUdp2logDest/swift-backend", // -aaron 5/15/12
		'updateTranstagOnNullRevisions' => "udp://$wmfUdp2logDest/updateTranstagOnNullRevisions",
		'json-hack' => "udp://$wmfUdp2logDest/json-hack", // Max 2012-06-27
		'redis' => "udp://$wmfUdp2logDest/redis", // -asher 2012/10/12
		'memcached-serious' => "udp://$wmfUdp2logDest/memcached-serious", // -aaron 2012/10/24
		'404' => "udp://$wmfUdp2logDest/four-oh-four",
		'resourceloader' => "udp://$wmfUdp2logDest/resourceloader",
		'wikibase' => "udp://$wmfUdp2logDest/wikibase",
		'oai' => "udp://$wmfUdp2logDest/oai",
		'captcha' => "udp://$wmfUdp2logDest/captcha",
		'ExternalStoreDB' => "udp://$wmfUdp2logDest/external",
		'query' => "udp://$wmfUdp2logDest/botquery",
		'antispoof' => "udp://$wmfUdp2logDest/antispoof",
		'badpass' => "udp://$wmfUdp2logDest/badpass",
		'ts_badpass' => "udp://$wmfUdp2logDest/ts_badpass",
		'EventLogging'  => "udp://$wmfUdp2logDest/eventlogging",
	),

	// To measure the # of articles on enwiki during the <million => >million transition
	'+testwiki' => array(
		'articles' => "udp://$wmfUdp2logDest/articles/testwiki",
		'imagemove' => "udp://$wmfUdp2logDest/imagemove",
	),
),
# @} end of wgDebugLogGroup

'wgOverrideSiteFeed' => array(
	'testwiki' => array(
		'rss' => 'http://www.example.org/feedstuff.rss',
	),
	'enwikinews' => array(
		'atom' => 'http://en.wikinews.org/w/index.php?title=Special:NewsFeed&feed=atom&categories=Published&notcategories=No%20publish%7CArchived%7CAutoArchived%7Cdisputed&namespace=0&count=30&hourcount=124&ordermethod=categoryadd&stablepages=only',
	),
	'fawikinews' => array(
		'atom' => 'http://fa.wikinews.org/w/index.php?title=Special:NewsFeed&feed=atom&categories=%D9%85%D9%86%D8%AA%D8%B4%D8%B1%D8%B4%D8%AF%D9%87&notcategories=%D9%85%D8%B1%D9%88%D8%B1|%D9%85%D8%B1%D9%88%D8%B1_%D9%81%D9%88%D8%B1%DB%8C|%D8%B5%D9%81%D8%AD%D9%87%E2%80%8C%D9%87%D8%A7%DB%8C_%D9%86%D8%A7%D9%85%D8%B2%D8%AF_%D8%AD%D8%B0%D9%81_%D8%B3%D8%B1%DB%8C%D8%B9&namespace=0&count=30&hourcount=124&ordermethod=categoryadd&stablepages=only',
		'rss' => 'http://fa.wikinews.org/w/index.php?title=Special:NewsFeed&feed=rss&categories=%D9%85%D9%86%D8%AA%D8%B4%D8%B1%D8%B4%D8%AF%D9%87&notcategories=%D9%85%D8%B1%D9%88%D8%B1|%D9%85%D8%B1%D9%88%D8%B1_%D9%81%D9%88%D8%B1%DB%8C|%D8%B5%D9%81%D8%AD%D9%87%E2%80%8C%D9%87%D8%A7%DB%8C_%D9%86%D8%A7%D9%85%D8%B2%D8%AF_%D8%AD%D8%B0%D9%81_%D8%B3%D8%B1%DB%8C%D8%B9&namespace=0&count=30&hourcount=124&ordermethod=categoryadd&stablepages=only'
	),
),


// Recommended namespace numbers:
//    100 Portal
//    102 WikiProject
//    104 Reference
# wgExtraNamespaces @{
'wgExtraNamespaces' => array(
	// Meta wiki @{
	'metawiki' => array(
		200 => 'Grants',    // Bug 22810
		201 => 'Grants_talk',
		202 => 'Research',  // Bug 28742
		203 => 'Research_talk',
		204 => 'Participation',
		205 => 'Participation_talk',
		206 => 'Iberocoop', // Bug 38398
		207 => 'Iberocoop_talk',
	),
	// @}

	# wikimania wikis @{
	'wikimaniawiki' => array( // back compat
		100 => 'Internal',
		101 => 'Internal_talk',
	),
	'wikimania2005wiki' => array(
		100 => 'Internal',
		101 => 'Internal_talk',
	),
	'wikimania2006wiki' => array(
		100 => 'Proceedings',
		101 => 'Proceedings_talk',
	),
	'wikimania2007wiki' => array(
		100 => 'Proceedings',
		101 => 'Proceedings_talk',
	),
	'wikimania2008wiki' => array(
		100 => 'Proceedings',
		101 => 'Proceedings_talk',
	),
	'wikimania2009wiki' => array(
		100 => 'Proceedings',
		101 => 'Proceedings_talk',
	),
	'wikimania2010wiki' => array(
		100 => 'Proceedings',
		101 => 'Proceedings_talk',
	),
	'wikimania2011wiki' => array(
		100 => 'Proceedings',
		101 => 'Proceedings_talk',
	),
	'wikimania2012wiki' => array(
		100 => 'Proceedings',
		101 => 'Proceedings_talk',
	),
	'wikimania2013wiki' => array(
		100 => 'Proceedings',
		101 => 'Proceedings_talk',
	),
	# @}

	#  specials wikis @{
	'arbcom_enwiki' => array(
		100 => 'Case',
		101 => 'Case_talk',
		102 => 'Wpuser',
		103 => 'Wpuser_talk',
	),
	'arbcom_dewiki' => array(
		100 => 'Anfrage',
		101 => 'Anfrage_Diskussion',
	),
	'commonswiki' => array(
		100 => 'Creator',
		101 => 'Creator_talk',
		102 => 'TimedText', # For video subtitles -- BV 2009-11-08
		103 => 'TimedText_talk',
		104 => 'Sequence',
		105 => 'Sequence_talk',
		106 => 'Institution',
		107 => 'Institution_talk',
	),
	'nlwikimedia' => array(
		100 => 'De_Wikiaan',
		101 => 'Overleg_De_Wikiaan',
	),
	'boardwiki' => array(
		100 => 'Resolution',
		101 => 'Resolution_talk',
	),
	'incubatorwiki' => array(
	),
	'officewiki' => array(
		100 => 'Report',
		101 => 'Report_talk',
		110 => 'Outreach',
		111 => 'Outreach_talk',
		112 => 'Archive',
		113 => 'Archive_talk',
	),
	'sewikimedia' => array(
		100 => 'Projekt',
		101 => 'Projektdiskussion',
	),
	'stewardwiki' => array( // http://bugzilla.wikimedia.org/show_bug.cgi?id=28773
		100 => 'Case',
		101 => 'Case_talk',
		102 => 'Archive',
		103 => 'Archive_talk',
		104 => 'Proposal',
		105 => 'Proposal_talk',
	),
	'testwiki' => array(
		100 => "Hilfe",	       # German
		101 => "Hilfe_Diskussion",
		102 => "Aide",		 # French
		103 => "Discussion_Aide",
		104  => "Hjælp",	     # Danish
		105  => "Hjælp_diskussion",
		106 => "Helpo",	       # Esperanto
		107 => "Helpa_diskuto",
		108 => "Hjälp",	      # Swedish
		109 => "Hjälp_diskussion",
		110 => "Ayuda",	       # Spanish
		111 => "Ayuda_Discusión",
		112 => "Aiuto",	       # Italian
		113 => "Discussioni_aiuto",
		114 => "ヘルプ",	   # Japanese
		115 => "ヘルプ‐ノート",
		116 => "NL_Help",	     # Dutch
		117 => "Overleg_help",
		118 => "Pomoc",	       # Polish
		119 => "Dyskusja_pomocy",
		120 => "Ajuda",	       # Portuguese
		121 => "Ajuda_Discussão",
		122 => "CA_Ajuda",	    # Valencian
		123 => "CA_Ajuda_Discussió",
		124 => "Hjelp",	       # Norsk
		125 => "Hjelp_diskusjon",
		126 => '帮助',	  # Chinese
		127 => '帮助_对话',
		128 => 'Помощь',      # Russian
		129 => 'Помощь_Дискуссия',
		130 => 'Pomoč',	    # Slovenian
		131 => 'Pogovor_o_pomoči',
		132 => 'مساعدة', # Arabic
		133 => 'نقاش_المساعدة',
	),
	'test2wiki' => array(
		100 => 'Portal',
		101 => 'Portal_talk',
		102 => 'Author',
		103 => 'Author_talk',
		104 => 'Page',
		105 => 'Page_talk',
		106 => 'Index',
		107 => 'Index_talk',
	),
	'strategywiki' => array(
		106 => "Proposal",
		107 => "Proposal_talk",
	),
	'usabilitywiki' => array(
		100 => 'Multimedia',
		101 => 'Multimedia_talk',
	),
	'mediawikiwiki' => array(
		100 => 'Manual',
		101 => 'Manual_talk',
		102 => 'Extension',
		103 => 'Extension_talk',
		104 => 'API',
		105 => 'API_talk',
		106 => 'Skin',
		107 => 'Skin_talk',
	),
	# @} end of special wikis

	// Wikipedia @{
	'afwiki' => array(
		100 => 'Portaal',
		101 => 'Portaalbespreking',
	   ),
	'alswiki' => array(
		100 => 'Portal',
		101 => 'Portal_Diskussion',
		102 => 'Buech',
		103 => 'Buech_Diskussion',
		104 => 'Wort',
		105 => 'Wort_Diskussion',
		106 => 'Text',
		107 => 'Text_Diskussion',
		108 => 'Spruch',
		109 => 'Spruch_Diskussion',
		110 => 'Nochricht',
		111 => 'Nochricht_Diskussion',
	),
	'amwiki' => array(
		100 => 'በር',  # 28727
		101 => 'በር_ውይይት',
	),
	'anwiki' => array(
		100 => 'Portal',
		101 => 'Descusión_Portal',
	),
	'arwiki' => array(
	100 => 'بوابة',
	101 => 'نقاش_البوابة',
	104 => 'ملحق',
	105 => 'نقاش_الملحق',
	),
	'arzwiki' => array(
		100 => 'بوابة',
		101 => 'مناقشة_بوابة',
	),
	'aswiki' => array(
			100 => "ৱিকিচ'ৰা", // Portal
			101 => "ৱিকিচ'ৰা_আলোচনা",// Portal talk
	),
	'azwiki' => array(
		100 => 'Portal',
		101 => 'Portal_müzakirəsi',
	),
	'azwikisource' => array(
		100 => 'Portal',
		101 => 'Portal_müzakirəsi',
		102 => 'Müəllif', // Author
		103 => 'Müəllif_müzakirəsi', // Author talk

	),
	'bawiki' => array(
		100 => 'Портал',                     // Bug 42077 - Portal
		101 => 'Портал_буйынса_фекерләшеү',
		102 => 'Проект',                     // Bug 42077 - Project
		103 => 'Проект_буйынса_фекерләшеү',
	),
	'barwiki' => array(
		100 => 'Portal',
		101 => 'Portal_Dischkrian',     // Bug 41962 comment #7
		102 => 'Buach',                 // Bug 41962 - Wikibooks alt.
		103 => 'Buach_Dischkrian',
		104 => 'Woat',                  // Bug 41962 - Wiktionary alt.
		105 => 'Woat_Dischkrian',
		106 => 'Text',                  // Bug 41962 - Wikisource alt.
		107 => 'Text_Dischkrian',
		108 => 'Spruch',                // Bug 41962 - Wikiquote alt.
		109 => 'Spruch_Dischkrian',
		110 => 'Nochricht',             // Bug 41962 - Wikinews alt.
		111 => 'Nochricht_Dischkrian',
	),
	'bewiki' => array(
		100 => 'Партал', // Portal
		101 => 'Размовы_пра_партал', // Portal talk
	),
	'be_x_oldwiki' => array(
		100 => 'Партал',
		101 => 'Абмеркаваньне_парталу',
	),
	'bgwiki' => array(
		100 => 'Портал',
		101 => 'Портал_беседа',
	),
	'bnwiki' => array(
		100 => 'প্রবেশদ্বার',
		101 => 'প্রবেশদ্বার_আলোচনা',
	),
	'bnwiktionary' => array(
		100 => 'উইকিসরাস',
		101 => 'উইকিসরাস_আলোচনা',
	),
	'bpywiki' => array(
		100 => 'হমিলদুৱার', // Portal
		101 => 'হমিলদুৱার_য়্যারী', // Portal_talk
	),
	'brwiktionary' => array(
	   100 => 'Stagadenn', // Appendix
	   101 => 'Kaozeadenn_Stagadenn', // Appendix talk
	),
	'bswiki' => array(
		100 => 'Portal', // Bug 37226
		101 => 'Razgovor_o_portalu', // Bug 37226
	),
	'cawiki' => array(
		100 => 'Portal', 101 => 'Portal_Discussió',
		102 => 'Viquiprojecte', 103 => 'Viquiprojecte_Discussió',
	),
	'cewiki' => array(
		100 => 'Ков',
		101 => 'Ков_дийцаре',
	),
	'ckbwiki' => array(
	100 => 'دەروازە',
	101 => 'لێدوانی_دەروازە',
	),
	'cswiki' => array(
		NS_USER      => 'Wikipedista',	    # language default set back in wgNamespaceAliases
		NS_USER_TALK => 'Diskuse_s_wikipedistou', # language default set back in wgNamespaceAliases
		100	  => 'Portál',
		101	  => 'Diskuse_k_portálu',
		102	  => 'Rejstřík',
		103	  => 'Diskuse_k_rejstříku',
	),
	'cswikisource' => array(
		100	  => 'Autor',
		101	  => 'Diskuse_k_autorovi',
	),
	'cywiki' => array(
		100     => 'Porth', # 27684
		101     => 'Sgwrs_Porth', # 27684
	),
	'cywikisource' => array(
		104 => 'Tudalen',          //Bug 39264 (Page)
		105 => 'Sgwrs_Tudalen',    //Bug 39264
		106 => 'Indecs',           //Bug 39264 (Index)
		107 => 'Sgwrs_Indecs',     //Bug 39264
	),
	'dawiki' => array(
		NS_PROJECT_TALK   => 'Wikipedia-diskussion', # bug 27902
		NS_MEDIAWIKI_TALK => 'MediaWiki-diskussion', # bug 27902
		NS_HELP_TALK      => 'Hjælp-diskussion',     # bug 27902
		100 => 'Portal',
		101 => 'Portaldiskussion',
		102 => 'Artikeldata',
		103 => 'Artikeldatadiskussion'
	),
	'dewiki' => array(
		100 => 'Portal',
		101 => 'Portal_Diskussion'
	),
	'dewikivoyage' => array(
		100 => 'Portal',
		101 => 'Portal_Diskussion',
		102 => 'Wahl',
		103 => 'Wahl_Diskussion',
		104 => 'Thema',
		105 => 'Thema_Diskussion',
		106 => 'Nachrichten',
		107 => 'Nachrichten_Diskussion',
	),
	'itwikivoyage' => array(
		100 => 'Portale',
		101 => 'Discussioni_portale',
		102 => 'Elezione',
		103 => 'Discussioni_elezione',
		104 => 'Tematica',
		105 => 'Discussioni_tematica',
		106 => 'Notizie',
		107 => 'Discussioni_notizie',
	),
	'elwiki' => array(
		100 => 'Πύλη',
		101 => 'Συζήτηση_πύλης'
	),
	'elwikinews' => array(
		102 => 'Σχόλια', // Comments
		103 => 'Συζήτηση_σχολίων', // Comment talk
	),
	'enwiki' => array(
		100 => 'Portal',
		101 => 'Portal_talk',
		# 106 => 'Table',
		# 107 => 'Table_talk',
		108 => 'Book',
		109 => 'Book_talk',
	),
	'eowiki' => array(
		100 => 'Portalo',
		101 => 'Portala_diskuto',
		102 => 'Projekto',
		103 => 'Projekta_diskuto',
	),
	'eowiktionary' => array(
		NS_USER => 'Uzanto',	# bug 22426
		NS_USER_TALK => 'Uzanta_diskuto',
	),
	'eswiki' => array(
		100 => 'Portal',
		101 => 'Portal_Discusión',
		102 => 'Wikiproyecto',
		103 => 'Wikiproyecto_Discusión',
		104 => 'Anexo', # http://bugzilla.wikimedia.org/show_bug.cgi?id=9304
		105 => 'Anexo_Discusión',
	),
	'etwiki' => array(
		100 => 'Portaal',
		101 => 'Portaali_arutelu',
	),
	'euwiki' => array(
		100 => 'Atari', // portal
		101 => 'Atari_eztabaida',
		102 => 'Wikiproiektu',
		103 => 'Wikiproiektu_eztabaida',
	),
	'fawiki' => array(
		100 => 'درگاه',
		101 => 'بحث_درگاه',
		102 => 'کتاب',
		103 => 'بحث_کتاب',
	),
	'fiwiki' => array(
		100 => 'Teemasivu',
		101 => 'Keskustelu_teemasivusta',
		102 => 'Metasivu',
		103 => 'Keskustelu_metasivusta',
		104 => 'Kirja',
		105 => 'Keskustelu_kirjasta',
	),
	'frrwiki' => array( // Per bug 38023
		102 => 'Seite',
		103 => 'Seite_Diskussion',
		104 => 'Index',
		105 => 'Index_Diskussion',
		106 => 'Text',
		107 => 'Text_Diskussion',
	),
	'frwiki' => array(
		100 => 'Portail',
		101 => 'Discussion_Portail',
		102 => 'Projet',
		103 => 'Discussion_Projet',
		104 => 'Référence',
		105 => 'Discussion_Référence',
	),
	'glwiki' => array(
		100 => 'Portal',
		101 => 'Conversa_portal', // Bug 41213
		102 => 'Libro',
		103 => 'Conversa_libro',
	),
	'hewiki' => array(
		100 => 'פורטל',
		101 => 'שיחת_פורטל',
		108 => 'ספר', // Book
		109 => 'שיחת_ספר', // Book talk
	),
	'hiwiki' => array(
		100 => 'प्रवेशद्वार',
		101 => 'प्रवेशद्वार_वार्ता',
	),
	'hrwiki' => array(
		100 => 'Portal',
		101 => 'Razgovor_o_portalu',
		102 => 'Dodatak',
		103 => 'Razgovor_o_dodatku'
	),
	'huwiki' => array(
		100 => 'Portál',
		101 => 'Portálvita',
	),
	'hywiki' => array(
		100 => 'Պորտալ',
		101 => 'Պորտալի_քննարկում',
	),
	'iawiki' => array(
		100 => 'Portal',
		101 => 'Discussion_Portal',
		102 => 'Appendice',
		103 => 'Discussion_Appendice',
	),
	'iawiktionary' => array(
		102 => 'Appendice',
		103 => 'Discussion_Appendice',
	),
	'idwiki' => array(
		100 => 'Portal',
		101 => 'Pembicaraan_Portal'
	),
	'iswiki' => array(
		100 => 'Gátt',
		101 => 'Gáttaspjall'
	),
	'itwiki' => array(
		100 => 'Portale',
		101 => 'Discussioni_portale',
		102 => 'Progetto',
		103 => 'Discussioni_progetto',
		),
	'jawiki' => array(
		NS_TALK => "ノート",
		NS_USER_TALK => "利用者‐会話",
		NS_PROJECT_TALK => "Wikipedia‐ノート",
		NS_FILE_TALK => "ファイル‐ノート",
		NS_MEDIAWIKI_TALK => "MediaWiki‐ノート",
		NS_TEMPLATE => "Template",
		NS_TEMPLATE_TALK => "Template‐ノート",
		NS_HELP => "Help",
		NS_HELP_TALK => "Help‐ノート",
		NS_CATEGORY => "Category",
		NS_CATEGORY_TALK => "Category‐ノート",
		100 => 'Portal',
		101 => 'Portal‐ノート',
		102 => 'プロジェクト',
		103 => 'プロジェクト‐ノート',
	),
	'jawiktionary' => array(
		100 => '付録', // Appendix
		101 => '付録・トーク', // Appendix talk
	),
	'kawiki' => array(
		100 => 'პორტალი',
		101 => 'პორტალი_განხილვა',
	),
	'kkwiki' => array(
		100 => 'Портал',	    // Portal
		101 => 'Портал_талқылауы',
		102 => 'Жоба',              // Bug 40794 - WikiProject
		103 => 'Жоба_талқылауы',
	),
	'knwikisource' => array( // Bug 37676
		100 => 'ಸಂಪುಟ',         // Portal
		101 => 'ಸಂಪುಟ_ಚರ್ಚೆ',
		102 => 'ಕರ್ತೃ',         // Author
		103 => 'ಕರ್ತೃ_ಚರ್ಚೆ',
		104 => 'ಪುಟ',         // Page
		105 => 'ಪುಟ_ಚರ್ಚೆ',
		106 => 'ಪರಿವಿಡಿ',         // Index
		107 => 'ಪರಿವಿಡಿ_ಚರ್ಚೆ',
	),
	'kowiki' => array(
		100 => '들머리',
		101 => '들머리토론',
		102 => '위키프로젝트', # 27651
		103 => '위키프로젝트토론', # 27651
	),
	'kuwiki' => array(
		100 => 'Portal',
		101 => 'Gotûbêja_portalê', // Bug 37521
		NS_PROJECT_TALK => 'Gotûbêja_Wîkîpediyayê', // Bug 37521
	),
	'kuwikibooks' => array(
		NS_PROJECT => 'Wîkîpirtûk', // Bug 37522
		NS_PROJECT_TALK => 'Gotûbêja_Wîkîpirtûkê', // Bug 37522
	),
	'kuwikiquote' => array(
		NS_PROJECT => 'Wîkîgotin', // Bug 37523
		NS_PROJECT_TALK => 'Gotûbêja_Wîkîgotinê', // Bug 37523
	),
	'kwwiki' => array(
		100 => 'Porth',
		101 => 'Keskows_Porth',
	),
	'lawiki' => array(
		100 => 'Porta',
		101 => 'Disputatio_Portae',
	),
	'liwiki' => array(
		100 => 'Portaol',
		101 => 'Euverlèk_portaol',
	),
	'lmowiki' => array(
		100 => 'Portal',
		101 => 'Descüssiú_Portal',
		102 => 'Purtaal',
		103 => 'Descüssiun_Purtaal',
	),
	'ltwiki' => array(
		100 => 'Vikisritis',
		101 => 'Vikisrities_aptarimas',
		102 => 'Vikiprojektas',
		103 => 'Vikiprojekto_aptarimas',
		104 => 'Sąrašas',
		105 => 'Sąrašo_aptarimas' ,
	),
	'lvwiki' => array(
		100 => 'Portāls',
		101 => 'Portāla_diskusija',
		102 => 'Vikiprojekts',
		103 => 'Vikiprojekta_diskusija',
	),
	'lvwiktionary' => array(
		100 => 'Pielikums',
		101 => 'Pielikuma_diskusija',
	),
	'mkwikisource' => array(
		   102 => 'Автор',
		   103 => 'Разговор_за_автор',
	),
	'mlwiki' => array(
		100 => 'കവാടം',
		101 => 'കവാടത്തിന്റെ_സംവാദം'
	),
	'minwiki' => array(
		100 => 'Portal', // Portal
		101 => 'Diskusi_Portal', // Portal talk
	),
	'mlwikisource' => array(
		100 => 'രചയിതാവ്',
		101 => 'രചയിതാവിന്റെ_സംവാദം',
		102 => 'കവാടം',
		103 => 'കവാടത്തിന്റെ_സംവാദം',
		104 => 'സൂചിക',
		105 => 'സൂചികയുടെ_സംവാദം',
		106 => 'താൾ',
		107 => 'താളിന്റെ_സംവാദം',
	),
	'mrwiki' => array(
		100 => 'दालन',
		101 => 'दालन_चर्चा',
	),
	'mrwikisource' => array(
		100 => 'दालन', // Portal
		101 => 'दालन_चर्चा', // Portal talk
		102 => 'साहित्यिक', // Author
		103 => 'साहित्यिक_चर्चा', // Author talk
		104 => 'पान', // Page
		105 => 'पान_चर्चा', // Page talk
		106 => 'अनुक्रमणिका', // Index
		107 => 'अनुक्रमणिका_चर्चा', // Index talk
	),
	'mrwiktionary' => array(
		104 => 'सूची',
		105 => 'सूची_चर्चा',
	),
	'mtwiki' => array(
		100 => 'Portal',
		101 => 'Diskussjoni_portal',
	),
	'mswiki' => array(
		100 => 'Portal',
		101 => 'Perbualan_Portal',
	),
	'mznwiki' => array(
		100 => 'پورتال', # Portal
		101 => 'پورتال_گپ', # Portal talk
	),

	'ndswiki' => array(
		100 => 'Portal',
		101 => 'Portal_Diskuschoon'
	),
	'newwiki' => array(
		100 => 'दबू',
		101 => 'दबू_खँलाबँला',
	),
	'nlwiki' => array(
		100 => 'Portaal',
		101 => 'Overleg_portaal'
	),
	'nnwiki' => array(
		100 => 'Tema',
		101 => 'Temadiskusjon',
	),
	'nowiki' => array(
		100 => 'Portal',
		101 => 'Portaldiskusjon',
	),
	'nowiktionary' => array(
		100 => 'Tillegg',
		101 => 'Tilleggdiskusjon',
	),
	'ocwiki' => array(
		100 => 'Portal',
		101 => 'Discussion_Portal',
		102 => 'Projècte',
		103 => 'Discussion_Projècte',
	),
	'otrs_wikiwiki' => array(
		100 => 'Response',
		101 => 'Response_talk'
	),
	'pflwiki' => array(
		100 => 'Portal',             // Bug 42990
		101 => 'Portal_Diskussion',
	),
	'plwiki' => array(
		NS_PROJECT_TALK => 'Dyskusja_Wikipedii',
		NS_USER => 'Wikipedysta',
		# Lower case w in wikipedysty as per bug #10064
		NS_USER_TALK => 'Dyskusja_wikipedysty',
		100 => 'Portal',
		101 => 'Dyskusja_portalu',
		102 => 'Wikiprojekt',
		103 => 'Dyskusja_wikiprojektu',
	),
	'plwikivoyage' => array(
		NS_PROJECT_TALK => 'Dyskusja_Wikipodróży',
	),
	'ptwiki' => array(
	  NS_USER => 'Usuário(a)', # bug 27495
	  NS_USER_TALK => 'Usuário(a)_Discussão',
	  100 => 'Portal',
	  101 => 'Portal_Discussão',
	  102 => 'Anexo',
	  103 => 'Anexo_Discussão',
	  104 => 'Livro',
	  105 => 'Livro_Discussão',
	),
	'roa_tarawiki' => array(
		100 => 'Portale',
		101 => "'Ngazzaminde_d'u_Portale",
	),
	'rowiki' => array(
		100 => 'Portal',
		101 => 'Discuție_Portal',
		102 => 'Proiect',
		103 => 'Discuție_Proiect',
		108 => 'Cod',                    // Bug 42690 - Code
		109 => 'Discuție_Cod',
	),
	'rowikisource' => array(
		102 => 'Autor',
		103 => 'Discuție_Autor',
		104 => 'Pagină',
		105 => 'Discuție_Pagină',
		106 => 'Index',
		107 => 'Discuție_Index',
	),
	'ruwiki' => array(
		100 => 'Портал',
		101 => 'Обсуждение_портала',
		102 => 'Инкубатор', // Incubator
		103 => 'Обсуждение_Инкубатора', // Incubator talk
		104 => 'Проект', // Bug 34124 - Project
		105 => 'Обсуждение_проекта', // Bug 34124 - Project talk
		106 => 'Арбитраж', // Bug 34527 - Arbcom
		107 => 'Обсуждение_арбитража',
	),
	'scnwiki' => array(
		100 => 'Purtali',
		101 => 'Discussioni_purtali',
		102 => 'Pruggettu',
		103 => 'Discussioni_pruggettu',
	),
	'sewiki' => array(
		100 => 'Portála',              //Bug 39206
		101 => 'Portálaságastallan',   //Bug 39206
	),
	'shwiki' => array( 100 => 'Portal', 101 => 'Razgovor_o_portalu' ), # bug 30928
	'siwiki' => array( 100 => 'ද්වාරය', 101 => 'ද්වාරය_සාකච්ඡාව' ), # bug 6435, 24936
	'skwiki' => array( 100 => 'Portál', 101 => 'Diskusia_k_portálu' ),
	'skwikiquote' => array(
		100 => 'Deň',             // Bug 44052
		101 => 'Diskusia_ku_dňu',
	),
	'slwiki' => array( 100 => 'Portal', 101 => 'Pogovor_o_portalu' ),
	'sqwiki' => array( 100 => 'Portal', 101 => 'Portal_diskutim' ),
	'srwiki' => array(
		100 => 'Портал',
		101 => 'Разговор_о_порталу',
	),
	'suwiki' => array( 100 => 'Portal', 101 => 'Obrolan_portal' ), # bug 8156
	'svwiki' => array( 100 => 'Portal', 101 => 'Portaldiskussion' ),
	'svwikiversity' => array (
		100 => 'Portal',
		101 => 'Portaldiskussion',
	),
	'swwiki' => array(
		100 => 'Lango',
		101 => 'Majadiliano_ya_lango',
	),
	'tawiki' => array( 100 => 'வலைவாசல்', 101 => 'வலைவாசல்_பேச்சு', ),
	'tewiki' => array( 100 => 'వేదిక', 101 => 'వేదిక_చర్చ', ),
	'tgwiki' => array(
		100 => 'Портал',
		101 => 'Баҳси_портал',
	 ),
	'thwiki' => array(
		100 => 'สถานีย่อย',
		101 => 'คุยเรื่องสถานีย่อย',
	),
	'tlwiki' => array(
		100 => 'Portada', // Portal
		101 => 'Usapang_Portada', // Portal talk
	),
	'trwiki' => array(
		100 => 'Portal',
		101 => 'Portal_tartışma'
	),
	'trwiktionary' => array(
	100 => 'Portal',
	101 => 'Portal_tartışma',
	),
	'ttwiki' => array(
	100 => 'Портал',
	101 => 'Портал_бәхәсе',
	),
	'ukwiki' => array( 100 => 'Портал', 101 => 'Обговорення_порталу' ),
	'urwiki' => array(
		100 => 'باب', // Portal - bug 19569
		101 => 'تبادلۂ_خیال_باب', // Portal talk - bug 19569
	),
	'uzwiki' => array (
		100 => 'Portal',                     //Bug 38840 (Portal)
		101 => 'Portal_munozarasi',          //Bug 38840
		102 => 'Loyiha',                     //Bug 38840 (Project)
		103 => 'Loyiha_munozarasi',          //Bug 38840
	),
	'vecwiki' => array(
		100 => 'Portałe',
		101 => 'Discussion_portałe',
		102 => 'Projeto',
		103 => 'Discussion_projeto',
	),
	'viwiki' => array( 100 => 'Chủ_đề', 101 => 'Thảo_luận_Chủ_đề' ),
	'wuuwiki' => array(
		100 => 'Transwiki',
		101 => 'Transwiki_talk',
	),
	'yiwiki' => array(
		100 => 'פארטאל',
		101 => 'פארטאל_רעדן',
	),
	'yowiki' => array(
		100 => 'Èbúté',
		101 => 'Ọ̀rọ̀_èbúté',
		108 => 'Ìwé',
		109 => 'Ọ̀rọ̀_ìwé',
	),
	'zhwiki' => array( 100 => 'Portal', 101 => 'Portal_talk' ),
	'zh_classicalwiki' => array( 100 => '門', 101 => '議' ),
	'zh_min_nanwiki' => array( 100 => 'Portal', 101 => 'Portal_talk' ),
	'zh_yuewiki' => array(
		# Override MediaWiki default namespace names for "yue".
		NS_MEDIA	    => 'Media',
		NS_SPECIAL	  => 'Special',
		NS_TALK	     => 'Talk',
		NS_USER	     => 'User',
		NS_USER_TALK	=> 'User_talk',
		NS_PROJECT_TALK     => '$1_talk',
		NS_FILE	     => 'File',
		NS_FILE_TALK	=> 'File_talk',
		NS_MEDIAWIKI_TALK   => 'MediaWiki_talk',
		NS_TEMPLATE	 => 'Template',
		NS_TEMPLATE_TALK    => 'Template_talk',
		NS_HELP	     => 'Help',
		NS_HELP_TALK	=> 'Help_talk',
		NS_CATEGORY	 => 'Category',
		NS_CATEGORY_TALK    => 'Category_talk',
		# Additional namespaces.
		100 => 'Portal',
		101 => 'Portal_talk',
	),
	// @} end of Wikipedia

	// Wikisource wikis @{
	'sourceswiki' => array(
		104 => 'Page',
		105 => 'Page_talk',
		106 => 'Index',
		107 => 'Index_talk',
		108 => 'Author',
		109 => 'Author_talk',
	),
	'enwikisource' => array(
		100 => 'Portal',
		101 => 'Portal_talk',
		102 => 'Author',
		103 => 'Author_talk',
		104 => 'Page', // http://fr.wikisource.org/wiki/Wikisource:Scriptorium#Page:_Namespace
		105 => 'Page_talk',
		106 => 'Index',
		107 => 'Index_talk',
	),
	'arwikisource' => array(
		100 => 'بوابة',
		101 => 'نقاش_البوابة',
		102 => 'مؤلف',
		103 => 'نقاش_المؤلف',
		104 => 'صفحة',
		105 => 'نقاش_الصفحة',
		106 => 'فهرس',
		107 => 'نقاش_الفهرس',
	),
	'aswikisource' => array(
		104 => 'পৃষ্ঠা',       // Bug 43129 - Page
		105 => 'পৃষ্ঠা_আলোচনা',
		106 => 'সূচী',       // Bug 43129 - Index
		107 => 'সূচী_আলোচনা',
	),
	'bewikisource' => array(
		102 => 'Аўтар', // Author
		103 => 'Размовы_пра_аўтара',
		104 => 'Старонка', // Page
		105 => 'Размовы_пра_старонку',
		106 => 'Індэкс', // Index
		107 => 'Размовы_пра_індэкс',
	),
	'bgwikisource' => array(
		100 => 'Автор',
		101 => 'Автор_беседа',
	),
	'bnwikisource' => array(
		100 => 'লেখক', // Author
		101 => 'লেখক_আলাপ', // Author talk

		102 => 'নির্ঘণ্ট', // Index
		103 => 'নির্ঘণ্ট_আলাপ',
		104 => 'পাতা', // Page
		105 => 'পাতা_আলাপ',
		106 => 'প্রবেশদ্বার', // Portal
		107 => 'প্রবেশদ্বার_আলাপ',
	),
	'brwikisource' => array(
		100 => 'Meneger',
		101 => 'Kaozeadenn_meneger',
		102 => 'Pajenn',
		103 => 'Kaozeadenn_pajenn',
		104 => 'Oberour',
		105 => 'Kaozeadenn_oberour',
	),
	'cawikisource' => array(
		# 100, 101 reserved for Portal
		102 => 'Pàgina', // http://bugzilla.wikimedia.org/show_bug.cgi?id=15784
		103 => 'Pàgina_Discussió',
		104 => 'Llibre',
		105 => 'Llibre_Discussió',
		106 => 'Autor', # 27898
		107 => 'Autor_Discussió',  # 27898
	),
	'dawikisource' => array(
		102 => 'Forfatter', // http://bugzilla.wikimedia.org/show_bug.cgi?id=7796
		103 => 'Forfatterdiskussion',
		104 => 'Side', // bug 24440
		105 => 'Sidediskussion',
		106 => 'Indeks',
		107 => 'Indeksdiskussion',
	),
	'dewikisource' => array(
		102 => 'Seite', // http://bugzilla.wikimedia.org/show_bug.cgi?id=11101
		103 => 'Seite_Diskussion',
		104 => 'Index', // http://bugzilla.wikimedia.org/show_bug.cgi?id=11101
		105 => 'Index_Diskussion',
	),
	'elwikisource' => array(
		100 => 'Σελίδα',
		101 => 'Συζήτηση_σελίδας',
		102 => 'Βιβλίο',
		103 => 'Συζήτηση_βιβλίου',
	),
	'eowikisource' => array(
		102 => 'Aŭtoro', // Author
		103 => 'Aŭtoro-Diskuto', // Author talk
		104 => 'Paĝo',
		105 => 'Paĝo-Diskuto',
		106 => 'Indekso',
		107 => 'Indekso-Diskuto',
	),
	'eswikisource' => array(
		102 => 'Página', // http://bugzilla.wikimedia.org/show_bug.cgi?id=15775
		103 => 'Página_Discusión',
		104 => 'Índice',
		105 => 'Índice_Discusión',
	),
	'etwikisource' => array(
		102 => 'Lehekülg',
		103 => 'Lehekülje_arutelu',
		104 => 'Register',
		105 => 'Registri_arutelu',
		106 => 'Autor',
		107 => 'Autori_arutelu',
	),
	'fawikisource' => array(
		100 => 'درگاه', // Portal
		101 => 'بحث_درگاه', // Portal talk
		102 => 'پدیدآورنده', // Author
		103 => 'گفتگو_پدیدآورنده', // Author talk
		104 => 'برگه', // Page
		105 => 'گفتگوی_برگه', // Page talk
		106 => 'فهرست', // Index
		107 => 'گفتگوی_فهرست', // Index talk
	),
	'fiwikisource' => array(
		104 => 'Sivu',                      // Bug 40759 (page)
		105 => 'Keskustelu_sivusta',        // Bug 40759
		106 => 'Hakemisto',                 // Bug 40759 (index)
		107 => 'Keskustelu_hakemistosta',   // Bug 40759
	),
	'frwikisource' => array(
		100 => 'Transwiki',
		101 => 'Discussion_Transwiki',
		102 => 'Auteur',
		103 => 'Discussion_Auteur',
		104 => 'Page', // http://fr.wikisource.org/wiki/Wikisource:Scriptorium#Page:_Namespace
		105 => 'Discussion_Page',
		106 => 'Portail',
		107 => 'Discussion_Portail',
		112 => 'Livre',
		113 => 'Discussion_Livre',
	),
	'guwikisource' => array(
		104 => 'પૃષ્ઠ', // Page
		105 => 'પૃષ્ઠ_ચર્ચા',
		106 => 'સૂચિ', // Index "list"
		107 => 'સૂચિ_ચર્ચા',
		108 => 'સર્જક', // Author
		109 => 'સર્જક_ચર્ચા',
	),
	'hewikisource' => array(
		100 => 'קטע',
		101 => 'שיחת_קטע',
		104 => 'עמוד',
		105 => 'שיחת_עמוד',
		106 => 'ביאור',
		107 => 'שיחת_ביאור',
		108 => 'מחבר',
		109 => 'שיחת_מחבר',
		110 => 'תרגום',
		111 => 'שיחת_תרגום',
		112 => 'מפתח',
		113 => 'שיחת_מפתח',
	),
	'hrwikisource' => array(
		100 => 'Autor',
		101 => 'Razgovor_o_autoru',
		102 => 'Stranica',
		103 => 'Razgovor_o_stranici',
		104 => 'Sadržaj',
		105 => 'Razgovor_o_sadržaju',
	),
	'huwikisource' => array(
		100 => 'Szerző',
		101 => 'Szerző_vita',
		104 => 'Oldal',
		105 => 'Oldal_vita',
		106 => 'Index',
		107 => 'Index_vita',
	),
	 'hywikisource' => array(
	   100 => 'Հեղինակ',
	   101 => 'Հեղինակի_քննարկում',
	   102 => 'Պորտալ',
	   103 => 'Պորտալի_քննարկում',
	   104 => 'Էջ',
	   105 => 'Էջի_քննարկում',
	   106 => 'Ինդեքս',
	   107 => 'Ինդեքսի_քննարկում',
	),
	'idwikisource' => array(
		100 => 'Pengarang',
		101 => 'Pembicaraan_Pengarang',
		102 => 'Indeks',
		103 => 'Pembicaraan_Indeks',
		104 => 'Halaman',
		105 => 'Pembicaraan_Halaman',
		106 => 'Portal',
		107 => 'Pembicaraan_Portal',
	),
	'itwikisource' => array(
		102 => 'Autore',
		103 => 'Discussioni_autore',
		104 => 'Progetto',
		105 => 'Discussioni_progetto',
		106 => 'Portale',
		107 => 'Discussioni_portale',
		108 => 'Pagina',
		109 => 'Discussioni_pagina',
		110 => 'Indice',
		111 => 'Discussioni_indice',
	),
	'kowikisource' => array(
		100 => '저자',
		101 => '저자토론',
	),
	'lawikisource' => array(
		102 => 'Scriptor',
		103 => 'Disputatio_Scriptoris',
		104 => 'Pagina',
		105 => 'Disputatio_Paginae',
		106 => 'Liber',
		107 => 'Disputatio_Libri',
	),
	'mkwiki' => array(
		100 => 'Портал',
		101 => 'Разговор_за_Портал',
	),

	'nlwikisource' => array(
		100 => 'Hoofdportaal',
		101 => 'Overleg_hoofdportaal',
		102 => 'Auteur',
		103 => 'Overleg_auteur',
		104 => 'Pagina',         // Bug 37482
		105 => 'Overleg_pagina', // Bug 37482
		106 => 'Index',          // Bug 37482
		107 => 'Overleg_index',  // Bug 37482
	),
	'nowikisource' => array(
		102 => 'Forfatter',
		103 => 'Forfatterdiskusjon',
		104 => 'Side',
		105 => 'Sidediskusjon',
		106 => 'Indeks',
		107 => 'Indeksdiskusjon',
	),
	'plwikiquote' => array(
		NS_PROJECT_TALK => 'Dyskusja_Wikicytatów',
	),
	'plwikisource' => array(
		NS_USER => 'Wikiskryba',
		NS_USER_TALK => 'Dyskusja_wikiskryby',
		NS_PROJECT_TALK => 'Dyskusja_Wikiźródeł',
		100 => 'Strona',
		101 => 'Dyskusja_strony',
		102 => 'Indeks',
		103 => 'Dyskusja_indeksu',
	104 => 'Autor',
	105 => 'Dyskusja_autora',
	),
	'ptwikisource' => array(
		100 => 'Portal',
		101 => 'Portal_Discussão',
		102 => 'Autor',
		103 => 'Autor_Discussão',
	   104 => 'Galeria',
	   105 => 'Galeria_Discussão',
	   106 => 'Página',
	   107 => 'Página_Discussão',
	   108 => 'Em_Tradução',
	   109 => 'Discussão_Em_Tradução',
	   110 => 'Anexo',
	   111 => 'Anexo_Discussão',
	),
	'ruwikisource' => array(
		104 => 'Страница',
		105 => 'Обсуждение_страницы',
		106 => 'Индекс',
		107 => 'Обсуждение_индекса',
	),
	'sawikisource' => array(
		104 => 'पुटम्', // Page
		105 => 'पुटसंवाद', // Page talk
		106 => 'अनुक्रमणिका', // Index
		107 => 'अनुक्रमणिकासंवाद', // Index talk
	),
	'slwikisource' => array(
		100 => 'Stran',
		101 => 'Pogovor_o_strani',
		104 => 'Kazalo',
		105 => 'Pogovor_o_kazalu',
	),
	'srwikisource' => array(
		100 => 'Аутор',
		101 => 'Разговор_о_аутору',
		102 => 'Додатак', // Bug 37742
		103 => 'Разговор_о_додатку', // Bug 37742
	),
	'svwikisource' => array(
		104 => 'Sida',
		105 => 'Siddiskussion',
		106 => 'Författare',
		107 => 'Författardiskussion',
		108 => 'Index',
		109 => 'Indexdiskussion',
	),
	'tewikisource' => array(
		100 => 'ద్వారము', // Portal
		101 => 'ద్వారము_చర్చ',
		102 => 'రచయిత', // Author
		103 => 'రచయిత_చర్చ',
		104 => 'పుట', // Page
		105 => 'పుట_చర్చ', // Page_talk
		106 => 'సూచిక', // Index
		107 => 'సూచిక_చర్చ',
	),
	'trwikisource' => array(
		100 => 'Kişi',
		101 => 'Kişi_tartışma',
	),
	'vecwikisource' => array(
		100 => 'Autor',
		101 => 'Discussion_autor',
		102 => 'Pagina',
		103 => 'Discussion_pagina',
		104 => 'Indice',
		105 => 'Discussion_indice',
	),
	'viwikisource' => array(
		100 => 'Chủ_đề',
		101 => 'Thảo_luận_Chủ_đề',
		102 => 'Tác_gia',
		103 => 'Thảo_luận_Tác_gia',
		104 => 'Trang',
		105 => 'Thảo_luận_Trang',
		106 => 'Mục_lục',
		107 => 'Thảo_luận_Mục_lục'
	),
	'zhwikisource' => array(  // Added on 2009-03-19 per bug 15722
		102 => 'Author',
		103 => 'Author_talk',
		104 => 'Page',
		105 => 'Page_talk',
		106 => 'Index',
		107 => 'Index_talk',
		108 => 'Transwiki', # Bug 40474
		109 => 'Transwiki_talk',
	),
	# @} end of wikisource wikis

	// Wiktionary @{
	'bgwiktionary' => array(
		100 => 'Словоформи',
		101 => 'Словоформи_беседа'
	),
	'bswiktionary' => array(
		100 => 'Portal',
		101 => 'Razgovor_o_Portalu',
		102 => 'Indeks',
		103 => 'Razgovor_o_Indeksu',
		104 => 'Dodatak',
		105 => 'Razgovor_o_Dodatku',
	),
	'cswiktionary' => array(
		100 => 'Příloha',
		101 => 'Diskuse_k_příloze',
	),
	'cywiktionary' => array(
		100 => 'Atodiad',
		101 => 'Sgwrs_Atodiad',
		102 => 'Odliadur',
		103 => 'Sgwrs_Odliadur',
		104 => 'WiciSawrws',
		105 => 'Sgwrs_WiciSawrws',
	),
	'dewiktionary' => array(
		102 => 'Verzeichnis',
		103 => 'Verzeichnis_Diskussion',
		104 => 'Thesaurus',
		105 => 'Thesaurus_Diskussion',
		106 => 'Reim', // Bug 43830
		107 => 'Reim_Diskussion',
	),
	'elwiktionary' => array(
		100 => 'Παράρτημα',
		101 => 'Συζήτηση_παραρτήματος',
	),
	'enwiktionary' => array(
		100 => 'Appendix',
		101 => 'Appendix_talk',
		102 => 'Concordance',
		103 => 'Concordance_talk',
		104 => 'Index',
		105 => 'Index_talk',
		106 => 'Rhymes',
		107 => 'Rhymes_talk',
		108 => 'Transwiki',
		109 => 'Transwiki_talk',
		110 => 'Wikisaurus',
		111 => 'Wikisaurus_talk',
		# 112 => 'WT', # Removed these in favor of aliases --catrope 1/18/2010
		# 113 => 'WT_talk',
		114 => 'Citations',
		115 => 'Citations_talk',
		116 => 'Sign_gloss',
		117 => 'Sign_gloss_talk',
	),

	'eswiktionary' => array(
		100 => 'Apéndice',
		101 => 'Apéndice_Discusión',
	),
	'fawiktionary' => array(
		100 => "پیوست",
		101 => "بحث_پیوست",
	),
	'fiwiktionary' => array(
		100 => 'Liite', // http://bugzilla.wikimedia.org/show_bug.cgi?id=11672
		101 => 'Keskustelu_liitteestä',
	),
	'frwiktionary' => array(
		100 => 'Annexe',
		101 => 'Discussion_Annexe',
		102 => 'Transwiki',
		103 => 'Discussion_Transwiki',
		104 => 'Portail',
		105 => 'Discussion_Portail',
		106 => 'Thésaurus',
		107 => 'Discussion_Thésaurus',
		108 => 'Projet',
		109 => 'Discussion_Projet',
	),
	'gawiktionary' => array(
		100 => 'Aguisín',
		101 => 'Plé_aguisín',
	),
	'glwiktionary' => array(
		100 => 'Apéndice',
		101 => 'Conversa_apéndice',
	),
	'hewiktionary' => array(
		100 => 'נספח',
		101 => 'שיחת_נספח'
	),
	'idwiktionary' => array(
		100 => 'Indeks',
		101 => 'Pembicaraan_Indeks',
	),
	'iswiktionary' => array(
		106 => 'Viðauki',
		107 => 'Viðaukaspjall',
		110 => 'Samheitasafn',
		111 => 'Samheitasafnsspjall',
	),
	'itwiktionary' => array(
		100 => 'Appendice',
		101 => 'Discussioni_appendice',
	),
	'kowiktionary' => array(
		100 => '부록',
		101 => '부록_토론',
		110 => '미주알고주알',
		111 => '미주알고주알_토론',
	),
	'kuwiktionary' => array(
		NS_PROJECT_TALK => 'Gotûbêja_Wîkîferhengê', // Bug 37524
		100 => 'Pêvek', // Bug 28398
		101 => 'Gotûbêja_pêvekê', // Bug 37524
		102 => 'Nimînok', // Bug 28398
		103 => 'Gotûbêja_nimînokê', // Bug 37524
		104 => 'Portal', //Bug 28398
		105 => 'Gotûbêja_portalê', // Bug 37524
	),
	'lbwiktionary' => array(
		100 => 'Annexen',
		101 => 'Annexen_Diskussioun',
	),
	'ltwiktionary' => array(
		100 => 'Sąrašas',
		101 => 'Sąrašo_aptarimas',
		102 => 'Priedas',
		103 => 'Priedo_aptarimas',
	),
	'mgwiktionary' => array(
		100 => 'Rakibolana', # Portal
		101 => "Dinika_amin'ny_rakibolana",
	),
	'ocwiktionary' => array(
		100 => 'Annèxa',
		101 => 'Discussion_Annèxa',
	),
	'plwiktionary' => array(
		NS_PROJECT_TALK => 'Wikidyskusja',
		NS_USER => 'Wikipedysta',
		NS_USER_TALK => 'Dyskusja_wikipedysty',
		100 => 'Aneks',
		101 => 'Dyskusja_aneksu',
		102 => 'Indeks',
		103 => 'Dyskusja_indeksu',
		104 => 'Portal',
		105 => 'Dyskusja_portalu',
	),
	'ptwiktionary' => array(
		100 => 'Apêndice',
		101 => 'Apêndice_Discussão',
		102 => 'Vocabulário',
		103 => 'Vocabulário_Discussão',
		104 => 'Rimas',
		105 => 'Rimas_Discussão',
		106 => 'Portal',
		107 => 'Portal_Discussão',
		108 => 'Citações',
		109 => 'Citações_Discussão',
	),
	'rowiktionary' => array(
		100 => 'Portal',
		101 => 'Discuție_Portal',
		102 => 'Apendice',
		103 => 'Discuție_Apendice',
	),
	'ruwiktionary' => array(
		100 => 'Приложение',
		101 => 'Обсуждение_приложения',
		102 => 'Конкорданс',
		103 => 'Обсуждение_конкорданса',
		104 => 'Индекс',
		105 => 'Обсуждение_индекса',
		106 => 'Рифмы',
		107 => 'Обсуждение_рифм',
	),
	'srwiktionary' => array(
		100 => 'Портал',
		101 => 'Разговор_о_порталу'
	),
	'svwiktionary' => array(    # http://bugzilla.wikimedia.org/show_bug.cgi?id=7933
		102 => 'Appendix',
		103 => 'Appendixdiskussion',
		104 => 'Rimord',
		105 => 'Rimordsdiskussion',
		106 => 'Transwiki',
		107 => 'Transwikidiskussion',
	),
	'ukwiktionary' => array(
		// bug 13791
		100 => 'Додаток', // appendix
		101 => 'Обговорення_додатка', // appendix talk
		102 => 'Індекс', // index
		103 => 'Обговорення_індексу', // index talk
	),
	'zhwiktionary' => array(
		100 => '附录', // bug 29641 - appendix
		101 => '附录讨论', // bug 29641 - appendix talk
		102 => 'Transwiki', # Bug 40474
		103 => 'Transwiki_talk',
	),
	// @} end of Wiktionary

	// Wikibooks @{
	'azwikibooks' => array(
		102 => 'Resept', // Recipe
		103 => 'Resept_müzakirəsi', // Recipe talk
		104 => 'Vikikitab', // Wikibooks
		105 => 'Vikikitab_müzakirəsi', // Wikibooks talk
		110 => 'Vikiuşaq', // Wikijunior, bug 31067
		111 => 'Vikiuşaq_müzakirəsi',
	),
	'bnwikibooks' => array(
		100 => 'উইকিশৈশব',
		101 => 'উইকিশৈশব_আলাপ',
		102 => 'বিষয়',
		103 => 'বিষয়_আলাপ',
	),
	'cawikibooks' => array(
		102 => 'Viquiprojecte',
		103 => 'Viquiprojecte_Discussió'
	),
	'cywikibooks' => array(
		102 => 'Silff_lyfrau',
		103 => 'Sgwrs_Silff_lyfrau'
	),
	'dewikibooks' => array(
		102 => 'Regal',
		103 => 'Regal_Diskussion'
	),
	'enwikibooks' => array(
		102 => 'Cookbook',
		103 => 'Cookbook_talk',
		108 => 'Transwiki',
		109 => 'Transwiki_talk',
		110 => 'Wikijunior',
		111 => 'Wikijunior_talk',
		112 => 'Subject',
		113 => 'Subject_talk',
	),
	/*
	'eswikibooks' => array(
		//102 => 'Wikiversidad',            // Bug 40838 (ns removed)
		//103 => 'Wikiversidad_Discusión',
	),
	*/
	'frwikibooks' => array(
		100 => 'Transwiki',
		101 => 'Discussion_Transwiki',
		102 => 'Wikijunior', # bug 35977
		103 => 'Discussion_Wikijunior', # bug 35977
	),
	'hewikibooks' => array(
		100 => 'שער', # portal
		101 => 'שיחת_שער', # portal talk
		// Skip 102 and 103, reserved for wikiproject
		104 => 'מדף', # bookshelf
		105 => 'שיחת_מדף', # bookshelf talk
	),
	'idwikibooks' => array(
		100 => 'Resep', # http://bugzilla.wikimedia.org/show_bug.cgi?id=7124
		101 => 'Pembicaraan_Resep',
		102 => 'Wisata',
		103 => 'Pembicaraan_Wisata',
	),
	'itwikibooks' => array(
		100 => 'Progetto', # http://bugzilla.wikimedia.org/show_bug.cgi?id=8408
		101 => 'Discussioni_progetto', # http://bugzilla.wikimedia.org/show_bug.cgi?id=11938
		102 => 'Ripiano', # http://bugzilla.wikimedia.org/show_bug.cgi?id=11937
		103 => 'Discussioni_ripiano',
		# Wikiversità deleted, http://bugzilla.wikimedia.org/show_bug.cgi?id=10287
		# 102 => 'Wikiversità', # http://bugzilla.wikimedia.org/show_bug.cgi?id=7354
		# 103 => 'Discussioni_Wikiversità',
	),
	'jawikibooks' => array(
		100 => 'Transwiki',
		101 => 'Transwiki‐ノート',
	),
	'kawikibooks' => array(
		104 => 'თარო', # bookshelf
		105 => 'თარო_განხილვა', # bookshelf talk
	),
	'mlwikibooks' => array(
		100 => 'പാചകപുസ്തകം',		// Cookbook
		101 => 'പാചകപുസ്തകസം‌വാദം',
		102 => 'വിഷയം',				// Subject
		103 => 'വിഷയസം‌വാദം',
	),
	'mswikibooks' => array(
		100 => 'Resipi',
		101 => 'Perbualan_Resipi',
	),
	'nlwikibooks' => array(
		102 => 'Transwiki',
		103 => 'Overleg_transwiki',
		104 => 'Wikijunior',
		105 => 'Overleg_Wikijunior',
	),
	'plwikibooks' => array(
		NS_PROJECT_TALK => 'Dyskusja_Wikibooks',
		NS_USER => 'Wikipedysta',
		NS_USER_TALK => 'Dyskusja_wikipedysty',
		104 => 'Wikijunior',
		105 => 'Dyskusja_Wikijuniora',
	),
	'rowikibooks' => array(
		100 => 'Raft',
		101 => 'Discuţie_Raft',
		102 => 'Wikijunior',
		103 => 'Discuţie_Wikijunior',
		104 => 'Carte_de_bucate',
		105 => 'Discuţie_Carte_de_bucate',
	),
	'ruwikibooks' => array(
		100 => 'Полка',
		101 => 'Обсуждение_полки',
		102 => 'Импортировано',
		103 => 'Обсуждение_импортированного',
		104 => 'Рецепт',
		105 => 'Обсуждение_рецепта',
		106 => 'Задача',
		107 => 'Обсуждение_задачи',
	),
	'siwikibooks' => array(
		112 => 'විෂයය',
		113 => 'විෂයය_සාකච්ඡාව',
		114 => 'කණිෂ්ඨ_විකි',
		115 => 'කණිෂ්ඨ_විකි_සාකච්ඡාව',
	),
	'srwikibooks' => array(
		102 => 'Кувар',
		103 => 'Разговор_о_кувару'
	),

	'tlwikibooks' => array(
		100 => 'Pagluluto',
		101 => 'Usapang_pagluluto',
	),
	'trwikibooks' => array(
		100 => 'Yemek',
		101 => 'Yemek_tartışma',
		110 => 'Vikiçocuk',
		111 => 'Vikiçocuk_tartışma',
		112 => 'Kitaplık',
		113 => 'Kitaplık_tartışma',
	),
	'ukwikibooks' => array(
		100 => 'Полиця',
		101 => 'Обговорення_полиці',
		102 => 'Рецепт',
		103 => 'Обговорення_рецепта',
	),
	'viwikibooks' => array(
		102 => 'Chủ_đề', // Subject
		103 => 'Thảo_luận_Chủ_đề',
		104 => 'Trẻ_em', // Wikijunior
		105 => 'Thảo_luận_Trẻ_em', // Wikijunior talk
		106 => 'Nấu_ăn', // Cookbook
		107 => 'Thảo_luận_Nấu_ăn', // Cookbook talk
	),
	# Bug 40474
	'zhwikibooks' => array(
		100 => 'Transwiki',
		101 => 'Transwiki_talk',
	),
	// @} end of wikibooks

	// Wikinews @{
	'arwikinews' => array(
		100 => 'بوابة',
		101 => 'نقاش_البوابة',
		102 => 'تعليقات',
		103 => 'نقاش_التعليقات',
	),
	'bgwikinews' => array(
		102 => 'Мнения',
		103 => 'Мнения_беседа',
	),
	'cawikinews' => array(
		100 => 'Transwiki',
		101 => 'Transwiki_talk',
		102 => 'Secció',
		103 => 'Secció_Discussió',
	),
	'cswikinews' => array(
		NS_USER      => 'Redaktor',	       # language default set back in wgNamespaceAliases
		NS_USER_TALK => 'Diskuse_s_redaktorem',   # language default set back in wgNamespaceAliases
		100	  => 'Portál',
		101	  => 'Diskuse_k_portálu',
	),
	'dewikinews' => array(
		100 => 'Portal',
		101 => 'Portal_Diskussion',
		102 => 'Meinungen',
		103 => 'Meinungen_Diskussion'
	),
	'dvwiki' => array(
		100 => 'ނެރު',
		101 => 'ނެރު_ޚ_ޔާލު',
	),
	'enwikinews' => array(
		100 => 'Portal',
		101 => 'Portal_talk',
		102 => 'Comments',
		103 => 'Comments_talk'
	),
	'eswikinews' => array(
		100 => 'Comentarios',
		101 => 'Comentarios_Discusión',
	),
	'fawikinews' => array(
		100 => 'درگاه',
		101 => 'بحث_درگاه',
		102 => 'نظرها',
		103 => 'بحث_نظرها',
	),
	'frwikinews' => array(
		102 => 'Transwiki',
		103 => 'Discussion_Transwiki',
		104 => 'Page',
		105 => 'Discussion_Page',
		106 => 'Dossier',
		107 => 'Discussion_Dossier',
	),
	'hewikinews' => array( 100 => 'פורטל', 101 => 'שיחת_פורטל' ),
	'huwikinews' => array(
		102 => 'Portál',
		103 => 'Portálvita',
	),
	'itwikinews' => array(
		100 => 'Portale',
		101 => 'Discussioni_portale',
	),
	'jawikinews' => array(
		100 => "ポータル",
		101 => "ポータル・トーク",
		108 => '短信',
		109 => '短信‐ノート',
	 ),
	 'nowikinews' => array(
		100 => 'Kommentarer',
		101 => 'Kommentarer-diskusjon',
		106 => 'Portal',
		107 => 'Portal-diskusjon',
	 ),
	'ptwikinews' => array(
		100 => 'Portal',
		101 => 'Portal_Discussão',
		102 => 'Efeméride',
		103 => 'Efeméride_Discussão',
		104 => 'Transwiki',
		105 => 'Transwiki_Discussão',
	 ),
	'plwikinews' => array(
		NS_PROJECT_TALK => 'Dyskusja_Wikinews',
		NS_USER => 'Wikireporter',
		NS_USER_TALK => 'Dyskusja_wikireportera',
		100 => 'Portal',
		101 => 'Dyskusja_portalu'
	),
	'ruwikinews' => array(
		100 => 'Портал',
		101 => 'Обсуждение_портала',
		102 => 'Комментарии',
		103 => 'Обсуждение_комментариев',
	),
	'sqwikinews' => array(
		100 => 'Portal',
		101 => 'Portal_diskutim',
		102 => 'Komentet',
		103 => 'Komentet_diskutim',
	),
	'srwikinews' => array(
		102 => 'Коментар',
		103 => 'Разговор_о_коментару',
	),
	'svwikinews' => array(
		100 => 'Portal',
		101 => 'Portaldiskussion',
	),
	'tawikinews' => array(
		100 => 'வலைவாசல்', // PPortal
		101 => 'வலைவாசல்_பேச்சு',
	),
	'trwikinews' => array(
		100 => 'Portal',
		101 => 'Portal_tartışma',
		106 => 'Yorum',
		107 => 'Yorum_tartışma',
	),
	'zhwikinews' => array(
		100 => '频道',
		101 => '频道_talk'
	),
	// @} end of wikinews

	// Wikiquote @{
	'dewikiquote' => array(
		100 => 'Portal',
		101 => 'Portal_Diskussion',
	),
	'frwikiquote' => array(
		100 => 'Portail',
		101 => 'Discussion_Portail',
		102 => 'Projet',
		103 => 'Discussion_Projet',
		104 => 'Référence',
		105 => 'Discussion_Référence',
		108 => 'Transwiki',
		109 => 'Discussion_Transwiki',
	),
	'hewikiquote' => array(
		100 => 'פורטל',
		101 => 'שיחת_פורטל'
	),
	'liwikiquote' => array(	// bug 12240
		100 => 'Portaol',
		101 => 'Euverlèk_portaol'
	),
	# Bug 40474
	'zhwikiquote' => array(
		100 => 'Transwiki',
		101 => 'Transwiki_talk',
	),
	// @} end of wikiquote

	// Wikiversity @{
	'arwikiversity' => array(
		100 => 'مدرسة',
		101 => 'نقاش_المدرسة',
		102 => 'بوابة',
		103 => 'نقاش_البوابة',
		104 => 'موضوع',
		105 => 'نقاش_الموضوع',
		106 => 'مجموعة',
		107 => 'نقاش_المجموعة',
	),
	'cswikiversity' => array(
		100	  => 'Fórum',
		101	  => 'Diskuse_k_fóru',
	),
	'dewikiversity' => array(
		106 => 'Kurs',
		107 => 'Kurs_Diskussion',
		108 => 'Projekt',
		109 => 'Projekt_Diskussion',
	),
	'elwikiversity' => array(
		100 => 'Σχολή',
		101 => 'Συζήτηση_Σχολής',
		102 => 'Τμήμα',
		103 => 'Συζήτηση_Τμήματος',
	),
	'enwikiversity' => array(
		100 => 'School',
		101 => 'School_talk',
		102 => 'Portal',
		103 => 'Portal_talk',
		104 => 'Topic',
		105 => 'Topic_talk',
		106 => 'Collection',
		107 => 'Collection_talk',
	),
	'itwikiversity' => array(
		100 => 'Facoltà',
		101 => 'Discussioni_facoltà',
		102 => 'Corso',
		103 => 'Discussioni_corso',
		104 => 'Materia',
		105 => 'Discussioni_materia',
		106 => 'Dipartimento',
		107 => 'Discussioni_dipartimento',
	),
	'frwikiversity' => array(
		102 => 'Projet',
		103 => 'Discussion_Projet',
		104 => 'Recherche', // http://bugzilla.wikimedia.org/show_bug.cgi?id=29015
		105 => 'Discussion_Recherche',
		106 => 'Faculté',
		107 => 'Discussion_Faculté',
		108 => 'Département',
		109 => 'Discussion_Département',
		110 => 'Transwiki',
		111 => 'Discussion_Transwiki',
	),
	'jawikiversity' => array(
		100 => 'School',
		101 => 'School‐ノート',
		102 => 'Portal',
		103 => 'Portal‐ノート',
		104 => 'Topic',
		105 => 'Topic‐ノート',
		110 => 'Transwiki',
		111 => 'Transwiki‐ノート',
	),
	// @} end of wikiversity

),
# @} end of wgExtraNamespaces

'wgAccountCreationThrottle' => array(
	'default' => 6, // previously 10
	'private' => 0, // disable for wikis with sysop-only account creation
	'fishbowl' => 0,

	'hewiki' => 4,
	'hewikibooks' => 4,
	'hewikinews' => 4,
	'hewikiquote' => 4,
	'hewikisource' => 4,
	'hewiktionary' => 4,
	'idwiki' => 0,
),

'wgDefaultSkin' => array(
	'default' => 'vector',
	'nostalgiawiki' => 'nostalgia',
),

# wgForceUIMsgAsContentMsg @{
'wgForceUIMsgAsContentMsg' => array(
	'default' => array(),
	'arwiki' => array( 'licenses', 'uploadtext', ),
	'bewikimedia' => array( 'sitenotice', 'anonnotice' ),
	'commonswiki' => array(
		// Sidebar
		'mainpage',
		'portal-url',
		'sitesupport-url',
		'village pump-url',
		'welcome-url',

		// Other
		'contact-url',
		'privacypage',
		'aboutpage',
		'disclaimerpage',
		'copyright',
		'recentchangestext',
		'licenses',
		'upload-url',
		'helppage', # https://bugzilla.wikimedia.org/show_bug.cgi?id=5925
		'license-header',
		'filedesc',
		'filestatus',
		'filesource',
	),
	'enwiki' => array(
		'licenses', // for upload form variants hack, 2007-02-22
	),
	'enwikiversity' => array(
		'licenses', 'uploadtext',
	),
	'mlwiki' => array( // https://bugzilla.wikimedia.org/show_bug.cgi?id=11538
		'licenses',
	),
	'mediawikiwiki' => array( // Requested by ialex on IRC
		'mw-mainpage-url',
		'mw-cat-browser-url',
		'mw-portal-url',
		'mw-help-url',
		'mw-manual-url',
		'mw-faq-url',
		'mw-download-url',
		'mw-extensions-url',
		'mw-bugzilla-url',
		'mw-downloadfromsvn-url',
		'mw-irc-url',
		'mw-mailing lists-url',
	),
	'metawiki' => array(
		'mainpage', // https://bugzilla.wikimedia.org/show_bug.cgi?id=16701
	),
	'nowikimedia' => array(
		'mainpage', // https://bugzilla.wikimedia.org/show_bug.cgi?id=16729
	),
	'tenwiki' => array(
		'mainpage',
		'organize-url',
		'share-url',
		'design-url',
		'ideas-url',
		'faq-url',
	),
	'viwiki' => array(
		'licenses',
	),
	'wikidatawiki' => array(
		'mainpage',
		'portal-url',
		'villagepump-url',
		'helppage',
	),
	'wikimaniawiki' => array( 'currentevents-url', 'portal-url' ), // back compat
	'wikimania2005wiki' => array( 'currentevents-url', 'portal-url' ),
	'wikimania2006wiki' => array( 'currentevents-url', 'portal-url' ),
	'wikimania2007wiki' => array( 'currentevents-url', 'portal-url' ),
	'wikimania2008wiki' => array( 'currentevents-url', 'portal-url' ),
#	'wikimania2009wiki' => array('currentevents-url','portal-url'),
	'wikimania2009wiki' => array(
		'registration-url',
		'schedule-url',
		'volunteers-url',
		'press-url',
		'sponsors-url',
		'FAQ-url',
		'IRC-url',
		'contact-url',
		'Site Map-url',
		'index-url',
		'cfp-url',
		'local-url',
	),

	'wikimania2010wiki' => array(
		'pagetitle',
		'pagetitle-view-mainpage',
		'Volunteers-url',
		'Attendees-url',
		'Press-url',
		'Sponsors-url',
		'Questions-url',
		'FAQ-url',
		'Site Map-url',
		'Registration-url',
		'Schedule-url',
		'IRC-url',
		'Contact-url',
	),

	'wikimania2011wiki' => array(
		'pagetitle',
		'pagetitle-view-mainpage',
		'Volunteers-url',
		'Attendees-url',
		'Press-url',
		'Sponsors-url',
		'Questions-url',
		'FAQ-url',
		'Site Map-url',
		'Registration-url',
		'Schedule-url',
		'IRC-url',
		'Contact-url',
		'Accommodation-url',
		'Venue-url',
		'LocalInformation-url',
		'Mainpage-url',
		'Program-url',
	),
	'wikimania2012wiki' => array(
		'pagetitle',
		'pagetitle-view-mainpage',
		'Volunteers-url',
		'Attendees-url',
		'Press-url',
		'Sponsors-url',
		'Questions-url',
		'FAQ-url',
		'Site Map-url',
		'Registration-url',
		'Schedule-url',
		'IRC-url',
		'Contact-url',
	),
	'wikimania2013wiki' => array(
		'pagetitle',
		'pagetitle-view-mainpage',
		'Volunteers-url',
		'Attendees-url',
		'Press-url',
		'Sponsors-url',
		'Questions-url',
		'FAQ-url',
		'Site Map-url',
		'Registration-url',
		'Schedule-url',
		'IRC-url',
		'Contact-url',
	),
	'incubatorwiki' => array(
	   'sidebar-mainpage-url',
	   'sidebar-help-url',
	   'sidebar-sitesupport-url',
	),
	'specieswiki' => array(
		'mainpage',
	),
	'zhwiki' => array(
		'pagetitle-view-mainpage',
	),
),
# @}

# wgUseRCPatrol @{
'wgUseRCPatrol' => array(

	'default' => false, # See [[Wikipedia:Village pump (news)]], 09:47, Jan 6, 2005

	// Special wiki
	'commonswiki' => true, # Bug 22834
	'metawiki' => true, // http://bugzilla.wikimedia.org/show_bug.cgi?id=4747
	'testwiki' => true,

	'alswiki' => true, // http://bugzilla.wikimedia.org/show_bug.cgi?id=4543
	'arwikisource' => true,
	'bgwiki' => true,
	'bnwiki' => true, // http://bugzilla.wikimedia.org/show_bug.cgi?id=11042
	'cawiki' => true,
	'dawiki' => true, // wegge asked in #wikimedia-tech 2006-03-01
	'dawikiquote' => true, // http://bugzilla.wikimedia.org/show_bug.cgi?id=13246
	'dewikibooks' => true, // http://bugzilla.wikimedia.org/show_bug.cgi?id=7041
	'dewikiquote' => true, // http://bugzilla.wikimedia.org/show_bug.cgi?id=10290
	'dewikiversity' => true,
	'dewiktionary' => true, // http://bugzilla.wikimedia.org/show_bug.cgi?id=4542
	'enwikisource' => true, // http://bugzilla.wikimedia.org/show_bug.cgi?id=5475
	'enwikivoyage' => true, // http://bugzilla.wikimedia.org/show_bug.cgi?id=43908
	'enwiktionary' => true, // http://bugzilla.wikimedia.org/show_bug.cgi?id=7248
	'eswiktionary' => true, // http://bugzilla.wikimedia.org/show_bug.cgi?id=7953
	'fawikinews' => true,
	'frwiki' => true, // http://bugzilla.wikimedia.org/show_bug.cgi?id=7269
	'frwikibooks' => true, // http://bugzilla.wikimedia.org/show_bug.cgi?id=21517
	'frwikisource' => true, // yannf asked in irc, 2006-07-25
	'frwikiversity' => true,
	'frwiktionary' => true, // http://bugzilla.wikimedia.org/show_bug.cgi?id=21517
	'fywiki' => true, // http://bugzilla.wikimedia.org/show_bug.cgi?id=10584
	'hewiki' => true, // https://bugzilla.wikimedia.org/show_bug.cgi?id=5232
	'hewikibooks' => true, // Bug 27918
	'hewikiquote' => true,
	'hiwiki' => true,
	'hrwiki' => true, // by request of stemd from #wikipedia-hr 2007-05-15 -jeronim
	'idwiki' => true, // http://bugzilla.wikimedia.org/show_bug.cgi?id=6042
	'itwiki' => true,
	'itwikinews' => true,
	'itwikiversity' => true,
	'itwikibooks' => true, // http://bugzilla.wikimedia.org/show_bug.cgi?id=9159
	'itwikiquote' => true, // http://bugzilla.wikimedia.org/show_bug.cgi?id=12826
	'itwiktionary' => true, // http://bugzilla.wikimedia.org/show_bug.cgi?id=11424
	'kshwiki' => true, // http://bugzilla.wikimedia.org/show_bug.cgi?id=8798
	'mlwiki' => true,
	'ndswiki' => true,
	'ndswiktionary' => true, // http://bugzilla.wikimedia.org/show_bug.cgi?id=4437
	'nlwiki' => true,
	'nlwikibooks' => true, // http://bugzilla.wikimedia.org/show_bug.cgi?id=7443
	'nlwikiquote' => true, // bug 12005
	'nnwiki' => true,
	'nowiki' => true, // http://bugzilla.wikimedia.org/show_bug.cgi?id=5549
	'nowikibooks' => true, // http://bugzilla.wikimedia.org/show_bug.cgi?id=10486
	'plwikiquote' => true, // http://bugzilla.wikimedia.org/show_bug.cgi?id=4697
	'ptwiki' => true, // https://bugzilla.wikimedia.org/show_bug.cgi?id=4500
	'ptwikibooks' => true, // https://bugzilla.wikimedia.org/show_bug.cgi?id=30136
	'rowiki' => true, // https://bugzilla.wikimedia.org/show_bug.cgi?id=28192
	'ruwikiversity' => true,
	'sawiki' => true, // bug 33200
	'sawikisource' => true, // bug 34769
	'siwiki' => true,
	'srwiki' => true, // req by dungodung in #wikimedia-tech, 2008-02-25
	'srwikisource' => true, // #25963
	'viwiki' => true, // http://bugzilla.wikimedia.org/show_bug.cgi?id=5060
	'wikidatawiki' => true, // bug 41907
	'zhwiktionary' => true, // http://bugzilla.wikimedia.org/show_bug.cgi?id=5596

	'test2wiki' => true, // request by Krinkle for testing
	'wikivoyage' => true, // requested by Erik
),
# @}

'wgUseNPPatrol' => array(
	'default' => true, // brion 2007-11-16
	'huwiki' => false, // Bug 19241
	'ruwiki' => false, // Bug 31650
	'sqwiki' => false, // Bug 25822
	'ukwiki' => false, // Bug 33273
),

# wgNoFollow... @{
'wgNoFollowLinks' => array(
	'default' => true,
	'fishbowl' => false, // not a spam measure here... https://bugzilla.wikimedia.org/show_bug.cgi?id=14105
),

'wgNoFollowNsExceptions' => array(
	'default' => array(),
),

'wgNoFollowDomainExceptions' => array(
	'default' => array(
		# Original list 20111110 - bug 32309
		'mediawiki.org',
		'wikibooks.org',
		'wikimediafoundation.org',
		'wikimedia.org',
		'wikinews.org',
		'wikipedia.org',
		'wikiquote.org',
		'wikisource.org',
		'wikiversity.org',
		'wiktionary.org',
		'wikivoyage.org',
		'wikidata.org',
	),
),
# @}

'wgEnableDnsBlacklist'     => array(
	'default'       => false,
	'thwiki'	=> true,
	'thwiktionary'  => true,
	'thwikiquote'   => true,
	'thwikibooks'   => true,
	'thwikisource'  => true,
	'enwikinews'    => true,
),

'wgCountCategorizedImagesAsUsed'    => array(
	'default'    => false,
	'commonswiki' => true,
),

'wgExternalStores' => array(
	 'default' => array( 'DB' ),
),

'wgRC2UDPAddress' => array(
	'default' => '208.80.152.178', // pmtpa: ekrem
	'private' => false,
),

'wgRC2UDPPort' => array(
	'default' => 9390,
),

'wgRC2UDPPrefix' => array(
	'default'  => "#\$lang.\$site\t",
	'donatewiki'  => "#donate.wikimedia.org\t",
	'foundationwiki' => "#wikimediafoundation.org\t",
	'metawiki'      => "#meta.wikimedia\t",
	'sourceswiki'   => "#wikisource\t",
	'commonswiki'   => "#commons.wikimedia\t",
	'grantswiki'    => "#grants.wikimedia\t",
	'specieswiki'   => "#species.wikimedia\t",
	'strategywiki' => "#strategy.wikimedia\t",
	'wikimaniawiki' => "#wikimania.wikimedia\t",
	'wikimania2005wiki' => "#wikimania.wikimedia\t",
	'wikimania2006wiki' => "#wikimania2006.wikimedia\t",
	'wikimania2007wiki' => "#wikimania2007.wikimedia\t",
	'wikimania2008wiki' => "#wikimania2008.wikimedia\t",
	'wikimania2009wiki' => "#wikimania2009.wikimedia\t",
	'incubatorwiki' => "#incubator.wikimedia\t",
	'usabilitywiki' => "#usability.wikimedia\t",
),

# wgNamespacesToBeSearchedDefault @{
'wgNamespacesToBeSearchedDefault' => array(
	'default' => array( 0 => 1, 1 => 0,  2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0, 9 => 0, 10 => 0, 11 => 0, 12 => 0, 13 => 0 ),
	'+arwikisource' => array( 102 => 1 ),
	'+aswikisource' => array( 104 => 1, 106 => 1 ), // bug 43129
	'+bgwiki' => array( 100 => 1, 101 => 0 ),
	'+bgwikisource' => array( 100 => 1 ),
	'+brwikisource' => array( 100 => 1, 104 => 1 ),
	'+cawikisource' => array( 104 => 1, 106 => 1 ),
	'+cswiki' => array( 100 => 1, 101 => 0, 102 => 1 ),
	'+cswikisource' => array( 100 => 1 ),
	'+commonswiki' => array( 6 => 1, 12 => 1, 14 => 1, 100 => 1, 106 => 1 ),
	'+cswikinews' => array( 14 => 0, 15 => 0, 100 => 1, 101 => 0 ),
	'+cswikisource' => array( 100 => 1, 101 => 0 ),
	'+cswikiversity' => array( 14 => 0, 15 => 0, 100 => 0, 101 => 0 ),
	'+dawikisource' => array( 102 => 1, 106 => 1 ),
	'+dewikisource' => array( 102 => 1, 104 => 1 ),
	'+dewikiversity' => array( 106 => 1, 108 => 1 ),
	'+dewikivoyage' => array( 6 => 1, 14 => 1, 100 => 1, 104 => 1, 106 => 1 ),
	'+elwikisource' => array( 102 => 1 ),
	'+enwikibooks' => array( 4 => 1, 112 => 1 ),
	'+enwikinews' => array( 100 => 1, 101 => 0 ),
	'+enwikisource' => array( 102 => 1, 106 => 1 ),
	'+eswiki' => array( 100 => 1, 104 => 1 ),
	'+eswikisource' => array( 104 => 1 ),
	'+etwikisource' => array( 104 => 1, 106 => 1 ),
	'+fawikisource' => array( 102 => 1 ),
	'+frrwiki' => array( 102 => 1, 104 => 1, 106 => 1 ), // bug 38023
	'+frwikisource' => array( 102 => 1, 112 => 1 ),
	'+frwikiversity' => array( 104 => 1, 106 => 1, 108 => 1 ),
	'+hewikisource' => array( 108 => 1, 112 => 1 ),
	'+hewiktionary' => array( 14 => 1 ),
	'+hrwikisource' => array( 100 => 1, 104 => 1 ),
	'+huwikisource' => array( 100 => 1, 106 => 1 ),
	'+hywikisource' => array( 100 => 1, 106 => 1 ),
	'+idwikibooks' => array( 100 => 1, 102 => 1 ),
	'+idwikisource' => array( 100 => 1, 102 => 1 ),
	'+itwikisource' => array( 102 => 1, 110 => 1 ),
	'+itwikivoyage' => array( 100 => 1, 104 => 1, 106 => 1 ),
	'+kowikisource' => array( 100 => 1 ),
	'+lawikisource' => array( 102 => 1, 106 => 1 ),
	'+ltwiki' => array( 100 => 1 ),
	'+mediawikiwiki' => array( 12 => 1, 100 => 1, 102 => 1 ),
	'+metawiki' => array( 12 => 1, 202 => 1 ),
	'+mlwikisource' => array( 100 => 1, 104 => 1 ),
	'+nlwikisource' => array( 102 => 1 ),
	'+nlwikivoyage' => array( 6 => 1, 14 => 1, 100 => 1, 104 => 1, 106 => 1 ),
	'+nowikisource' => array( 102 => 1, 106 => 1 ),
	'+plwiktionary' => array( 100 => 1, 102 => 1 ),
	'+plwikisource' => array( 102 => 1, 104 => 1 ),
	'+ptwiki' => array( 102 => 1 ),
	'+ptwikisource' => array( 102 => 1, 104 => 1 ),
	'+rowikisource' => array( 102 => 1, 106 => 1 ), // bug 29190
	'+ruwiki' => array( 104 => 0, 105 => 0, 106 => 0, 107 => 0 ),
	'+ruwikisource' => array( 106 => 1 ),
	'+ruwikivoyage' => array( 6 => 1, 14 => 1, 100 => 1, 104 => 1, 106 => 1 ),
	'+slwikisource' => array( 104 => 1 ),
	'+sourceswiki' => array( 106 => 1 ),
	'+svwikisource' => array( 106 => 1, 108 => 1 ),
	'+svwikivoyage' => array( 6 => 1, 14 => 1, 100 => 1, 104 => 1, 106 => 1 ),
	'+strategywiki' => array( 106 => 1, 107 => 1 ), // bug 20514
	'+tewikisource' => array( 102 => 1, 106 => 1 ),
	'+tlwikibooks' => array( 100 => 1, 101 => 0 ),
	'+trwikisource' => array( 100 => 1 ),
	'+vecwikisource' => array( 100 => 1, 104 => 1 ),
	'+viwikibooks' => array( 102 => 1, 104 => 1, 106 => 1 ),
	'+viwikisource' => array( 102 => 1, 106 => 1 ),
	'+zhwikisource' => array( 102 => 1, 106 => 1 ),
),
# @} end of wgNamespacesToBeSearchedDefault

# wgRateLimits @{
'wgRateLimits' => array(
	// Limit new accounts to 2 moves per 90 seconds, and anons to 3 edits per 30 seconds
	'default' => array(
		'move' => array(
			'newbie' => array( 2, 120 ),
			# To limit high-rate move page attacks on smaller wikis
			# Newbie limit was trivially avoided by a patient vandal
			'user' => array( 8, 60 ),
		),
		'edit' => array(
			// 8 ed./min per each non-autoconfirmed, or group thereof from same IP
			'ip'     => array( 8, 60 ),
			'newbie' => array( 8, 60 ),
		),
		'mailpassword' => array(
			// 5 password reminders per hour per IP
			'ip'     => array( 5, 3600 ),
		),
		// 100 emails per hour
		'emailuser' => array(
			'ip' => array( 100, 3600 ),
			'user' => array( 200, 86400 ),
		),
		// For expanded rollback permissions...
		'rollback' => array(
			'user' => array( 10, 60 ), // was array( 5, 60 ), -- brion 2008-05-15
			'newbie' => array( 5, 120 ),
			// practicality has won out over paranoia on enwiki, raising from 20 to 100 -- TS 2008-05-21
			'rollbacker' => array( 100, 60 ),
		),
	),
),
# @} end of wgRateLimits

'wgRateLimitLog' => array(
	'default' => "udp://$wmfUdp2logDest/limiter",
),

'wgRateLimitsExcludedIPs' => array(
	// see also wmf-config/throttle.php
	// exemptions for outreach events should go in that file
	// note that account creation is not the only bottleneck
	'default' => array(),
),

'wgEmailAuthentication' => array(
	'default' => true,
	'private' => false, // disable for wikis with account approval
	'fishbowl' => false,
),

# wgBlockAllowsUTEdit @{
'wgBlockAllowsUTEdit' => array(
	'default' => true, // http://bugzilla.wikimedia.org/show_bug.cgi?id=28288
	'itwiki' => false, // requested by Brownout on #wikimedia-tech and http://bugzilla.wikimedia.org/show_bug.cgi?id=9073
),
# @} end of wgBlockAllowsUTEdit

# wgBlockDisablesLogin @{
'wgBlockDisablesLogin' => array(
	'default' => false,
	'arbcom_dewiki' => true, // # 23357
	'arbcom_nlwiki' => true, // # 22630
	'auditcomwiki' => true, // #23231
	'boardwiki' => true, // #23231
	'chairwiki' => true, // #23231
	'checkuserwiki' => true,
	'collabwiki' => true, // #23231
	'chapcomwiki' => true, // # 22319
	'execwiki' => true, // # 22319
	'fdcwiki' => true,
	'internalwiki' => true, // #23231
	'noboard_chapterswikimedia' => true,
	'officewiki' => true, // #23231
	'otrs_wikiwiki' => true, // # 22319
	'stewardwiki' => true,
	'wikimaniateamwiki' => true, // # 22319
),
# @}

# groupOverrides @{
'groupOverrides' => array(
	// Note: don't change the default setting here, because it won't take
	// effect for the wikis which have overrides below. Instead change the
	// default in groupOverrides2
	// IMPORTANT: don't forget to use a '+' sign in front of the wiki name
	// when needed, such as for wikis that are tagged (private/fishbowl/closed...)
	'default' => array(),

	// Whitelist read wikis
	'private' => array(
		'*' => array(
			'read' => false,
			'edit' => false,
			'createaccount' => false
		),
		'user' => array(
			'move' => true,
			'upload' => true,
			'autoconfirmed' => true,
			'reupload' => true,
			'skipcaptcha' => true,
			'collectionsaveascommunitypage' => true,
			'collectionsaveasuserpage' => true,
		),
		'inactive' => array(
		  // for show only
		),
	),
	'wikimaniawiki' => array( '*' => array( // back compat
		'read' => false,
		'edit' => false,
		'createaccount' => false
	) ),
	'wikimania2012wiki' => array(
		'*' => array( 'edit' => false ),
		'*' => array( 'createpage' => false ),
	),
	'wikimania2013wiki' => array(
		'*' => array( 'createpage' => false ),
	),
	// Fishbowls
	'fishbowl' => array(
		'*' => array(
			'edit' => false,
			'createaccount' => false
		),
		'user' => array(
	 	'move' => true,
		 	'upload' => true,
		 	'autoconfirmed' => true,
		 	'reupload' => true,
		 	'skipcaptcha' => true,
		 	'collectionsaveascommunitypage' => true,
			'collectionsaveasuserpage' => true,
	),
	'inactive' => array(
		  // for show only
		),
	),

	'wikivoyage' => array (
		'bureaucrat' => array(
			'usermerge' => true
		)
	),

	// Read-only (except stewards)
	'closed' => array(
		'*' => array(
		'edit' => false,
		'createaccount' => false,
		),
		'user' => array(
			'edit' => false,
			'move' => false,
			'move-rootuserpages' => false,
			'move-subpages' => false,
			'upload' => false,
		),
		'autoconfirmed' => array(
			'upload' => false,
			'move' => false,
		),
		'sysop' => array(
			'block' => false,
			'delete' => false,
			'undelete' => false,
			'import' => false,
			'move' => false,
			'move-subpages' => false,
			'move-rootuserpages' => false,
			'patrol' => false,
			'protect' => false,
			'rollback' => false,
			'trackback' => false,
			'upload' => false,
			'movefile' => false,
		),
		'steward' => array(
			'edit' => true,
			'move' => true,
			'delete' => true,
			'upload' => true,
		),
	),

	// Account creation required
	'nlwikimedia' => array( '*' => array(
		'edit' => false,
	) ),
	'fiwikimedia' => array( '*' => array(
		'edit' => false,
	) ),
	'trwikimedia' => array( '*' => array(
		'edit' => false,
	) ),

	// Miscellaneous
	'+arbcom_enwiki' => array(
		'bureaucrat' => array( 'disableaccount' => true ),
	),
	'arwiki' => array(
		'autoconfirmed' => array( 'patrol' => true ),
		'rollbacker' => array( 'rollback' => true ),
	),
	'arwikisource' => array(
		'autopatrolled' => array( 'autopatrol' => true ),
		'patroller' => array( 'patrol' => true ),
	),
	'bnwiki' => array(
		'user' => array( 'patrol' => false ),
		'autoconfirmed' => array( 'patrol' => false ),
		'sysop' => array( 'patrol' => true ),
		'autopatrolled' => array( 'autopatrol' => true ), // http://bugzilla.wikimedia.org/show_bug.cgi?id=28717
		'rollbacker' => array( 'rollback' => true ), // http://bugzilla.wikimedia.org/show_bug.cgi?id=28717
		'reviewer' => array( 'patrol' => true ), // http://bugzilla.wikimedia.org/show_bug.cgi?id=28717
		'flood' => array( 'bot' => true ), // http://bugzilla.wikimedia.org/show_bug.cgi?id=28717
	),
	'bgwiki' => array(
		'user' => array( 'upload' => false, ),
		'autoconfirmed' => array( 'upload' => false, ),
		'autopatrolled' => array( 'autopatrol' => true, ),
		'patroller' => array( 'patrol' => true, 'autopatrol' => true, 'rollback' => true, ),
		'sysop' => array( 'upload' => true, 'autopatrol' => false, ),
	),
	'bswiki' => array(
		'patroller' => array( 'patrol' => true ),
		'autopatrolled' => array( 'autopatrol' => true ),
		'rollbacker' => array( 'rollback' => true ),
	),
	'cawiki' => array(
		'user' => array( 'patrol' => false ),
		'autoconfirmed' => array( 'patrol' => true ),
		'autopatrolled' => array( 'autopatrol' => true ),
		'sysop' => array( 'patrol' => true ),
		'rollbacker' => array( 'rollback' => true ),
	),
	'+checkuserwiki'  => array( // http://bugzilla.wikimedia.org/show_bug.cgi?id=28781
		'autoconfirmed' => array(
			'autoconfirmed' => false,
			'reupload' => false,
			'upload' => false,
			'move' => false,
			'collectionsaveasuserpage' => false,
			'collectionsaveascommunitypage' => false,
			'skipcaptcha' => false,
		),
		'confirmed' => array(
			'autoconfirmed' => false,
			'reupload' => false,
			'upload' => false,
			'move' => false,
			'collectionsaveasuserpage' => false,
			'collectionsaveascommunitypage' => false,
			'skipcaptcha' => false,
		),
		'accountcreator' => array( 'noratelimit' => false, ),
		'bureaucrat' => array( 'createaccount' => true, ),
		'sysop' => array( 'createaccount' => false, ),
	),
	'ckbwiki' => array(
		'*' => array( 'createpage' => false ),
	),
	'cswiki' => array(
		'autoconfirmed' => array( 'upload' => false, ),
		'autopatrolled' => array( 'autopatrol' => true, ),
		'bot' => array( 'ipblock-exempt' => true, ), // Bug 42720
		'user'	  => array( 'upload' => false, ),
	),
	'cswikibooks' => array(
		'user' => array( 'upload' => false ),
		'autoconfirmed' => array( 'upload' => false ),
	),
	'cswikinews' => array(
		'user'	  => array( 'upload' => false, ),
		'autoconfirmed' => array( 'upload' => false, ),
	),
	'cswikiquote' => array(
		'user' => array( 'upload' => false, ),
		'autoconfirmed'  => array( 'upload' => false, ),
		'autopatrolled' => array( 'autopatrol' => true, ),
	),
	'cswikisource' => array(
		'user'	  => array( 'upload' => false ),
		'autoconfirmed' => array( 'upload' => false ),
		'autopatrolled' => array( 'autopatrol' => true, ),
	),
	'cswikiversity' => array(
		'user'	  => array( 'upload' => false ),
		'autoconfirmed' => array( 'upload' => false ),
	),
	'cswiktionary' => array(
		'autopatrolled' => array( 'autopatrol' => true, ),
		'user'	  => array( 'upload' => false, ),
		'autoconfirmed' => array( 'upload' => false, ),
	),
	'commonswiki' => array(
		'user' => array(
			'move' => false, // requested by Bdka on #wikimedia-tech, 2006-05-04
			'upload' => true, // exception for https://bugzilla.wikimedia.org/show_bug.cgi?id=12556
		),
		'rollbacker' => array( 'rollback' => true ),
		'confirmed' => array( 'upload' => true, 'patrol' => true ),
		'patroller' => array( 'autopatrol' => true, 'patrol' => true, 'abusefilter-log-detail' => true ),
		'autopatrolled' => array ( 'autopatrol' => true ),
		'filemover' => array( 'movefile' => true ),
		'OTRS-member' => array( 'autopatrol' => true ),
		'Image-reviewer' => array( 'autopatrol' => true, 'upload_by_url' => true ),
		'sysop' => array( 'upload_by_url' => true ),
	),
	'dawiki' => array(
		'patroller'		=> array( 'patrol' => true, 'autopatrol' => true, 'rollback' => true, ),
		'autopatrolled'	=> array( 'autopatrol' => true, ),
	),
	'dewiki' => array(
		'user' => array(
			'move' => false, // Report by sansculotte on #mediawiki, TS 2004-10-15
			'upload' => false, // http://bugzilla.wikimedia.org/show_bug.cgi?id=12391
		),
		'autoconfirmed' => array(
			'upload' => true, // http://bugzilla.wikimedia.org/show_bug.cgi?id=12391
		),
		'editor' => array(
			'rollback' => true, // per DaBPunkt's request, 2008-05-07
		),
	),
	'dewikibooks' => array(
		'user' => array( 'move' => false ), // Request by DaBPunkt on #wikimedia-tech, JF 2007-01-27
	),
	'dewikiquote' => array(
		'sysop' => array( 'importupload' => true, ),
	),
	'dewikiversity' => array(
		'user'			=> array( 'patrol' => false, ),
		'autoconfirmed'	=> array( 'patrol' => false, ),
		'sysop'			=> array( 'patrol' => true, ),
	),
	'dewiktionary' => array(
		'sysop' => array( 'importupload' => true, ),
	),
	'+donatewiki' => array(
		'user' => array( 'editinterface' => true ),
		'flood' => array( 'bot' => true ),
	),
	'elwiktionary' => array(
		'interface_editor' => array( 'editinterface' => true ),
		'autopatrolled' => array( 'autopatrol' => true ), // http://bugzilla.wikimedia.org/show_bug.cgi?id=28612
	),
	'enwiki' => array(
		'*' => array( 'createpage' => false ),
		'user' => array( 'move' => false ), // autoconfirmed only
		'autoconfirmed' => array( 'patrol' => true ), // http://bugzilla.wikimedia.org/show_bug.cgi?id=12007
		'founder' => array( 'userrights' => true ),
		'rollbacker' => array( 'rollback' => true ),
		'accountcreator' => array(
			'override-antispoof' => true,
			'tboverride' => true,
			// also in some exemption lists'
		),
		'confirmed' => array( 'upload' => true, 'patrol' => true ),
		'autoreviewer' => array( 'autopatrol' => true ),
		'researcher' => array( 'browsearchive' => true, 'deletedhistory' => true, 'apihighlimits' => true ),
		'reviewer' => array( 'patrol' => true ),
		'filemover' => array( 'movefile' => true ), // bug 27927
		'bot' => array( 'ipblock-exempt' => true ), // bug 28914
		'oversight' => array( 'browsearchive' => true, 'deletedhistory' => true, 'deletedtext' => true ), // bug 28465
		'checkuser' => array( 'browsearchive' => true, 'deletedhistory' => true, 'deletedtext' => true ), // bug 28465
		'bureaucrat' => array( 'move-subpages' => true, 'suppressredirect' => true, 'tboverride' => true, ),
	),
	'enwikibooks' => array(
		// 'rollbacker' 	=> array( 'rollback' => true ),
		// 'patroller'		=> array( 'patrol' => true, 'autopatrol' => true ),
		'flood' 		=> array( 'bot' => true ),
		'uploader'      => array( 'upload' => true, 'reupload' => true ),
		'autoconfirmed' => array( 'upload' => false, 'reupload' => false ),
	),
	'enwikinews' => array(
		'flood' => array( 'bot' => true ),
	),
	'enwikiquote' => array(
		'user' => array( 'move' => false ), // autoconfirmed only, per request due to wow 2007-05-08
	),
	'enwikisource' => array(
		'autoconfirmed' => array( 'patrol' => true ), // http://bugzilla.wikimedia.org/show_bug.cgi?id=12355
		'autopatrolled' => array( 'autopatrol' => true ), # Bug 18307
		'flood' => array( 'bot' => true ), // https://bugzilla.wikimedia.org/show_bug.cgi?id=36863
	),
	'enwikiversity' => array(
		'user' => array( 'move' => false ), // autoconfirmed only
	),
	'enwikivoyage' => array(
		'autopatrolled' => array( 'autopatrol' => true ),
		'bureaucrat' => array( 'usermerge' => true ),
		'patroller' => array( 'patrol' => true, 'rollback' => true ),
	),
	// http://bugzilla.wikimedia.org/show_bug.cgi?id=5033
	'enwiktionary' => array(
		'user' => array(
			'upload' => false,
			'move' => false, // requested in beer parlour, 13 june 2006
		),
		'autoconfirmed' => array( 'upload' => false ),
		'autopatrolled' => array( 'autopatrol' => true ),
		'flood' => array( 'bot' => true ),
		'patroller' => array( 'patrol' => true ),
		'rollbacker' => array( 'rollback' => true ),
		'sysop' => array( 'upload' => true )
	),
	'eswiki' => array(
		'rollbacker' => array( 'rollback' => true ),
		'*' => array( 'patrolmarks' => true ),
		'autopatrolled' => array( 'autopatrol' => true ),
		'patroller' => array( 'patrol' => true, 'autopatrol' => true ),
	),
	'eswikibooks' => array(
		'*'     => array( 'createpage' => false, 'createtalk' => false, ),
		'flood' => array( 'bot' => true ),
		'rollbacker' => array( 'rollback' => true ),
	),
	'eswikinews' => array(
		'bot' => array( 'editprotected' => true ),
		'editprotected' => array( 'editprotected' => true ),
		'flood' => array( 'bot' => true ),
	),
	// http://bugzilla.wikimedia.org/show_bug.cgi?id=7152
	'eswiktionary' => array(
		'user' => array( 'upload' => false ),
		'autoconfirmed' => array( 'upload' => false ),
		'sysop' => array( 'upload' => true ),
		'patroller' => array( 'patrol' => true ),
		'rollbacker' => array( 'rollback' => true ),
		'autopatrolled' => array( 'autopatrol' => true ),
	),
	'fawiki' => array(
		'*' => array( 'createpage' => false ), # 27195
		'patroller' => array( 'patrol' => true ),
		'rollbacker' => array( 'rollback' => true ),
		'autopatrol' => array( 'autopatrol' => true ), // http://bugzilla.wikimedia.org/show_bug.cgi?id=29007
	),
	'fawikinews' => array(
		'rollbacker' => array( 'rollback' => true ),
		'patroller' => array( 'patrol' => true ),
	),
	'fiwiki' => array(
		'patroller' => array( 'patrol' => true ),
		'rollbacker' => array( 'rollback' => true ),
		// https://bugzilla.wikimedia.org/show_bug.cgi?id=19561:
		'arbcom' => array( 'deletedhistory' => true, 'deletedtext' => true, 'undelete' => true ),
	 ),
	'+foundationwiki' => array(
		'user' => array( 'editinterface' => true ),
		'flood' => array( 'bot' => true ),
	),
	'frwiki' => array(
		'user' => array( 'move' => false ), // requested by hashar
		'autopatrolled' => array( 'patrol' => true, 'autopatrol' => true ), // bug 8904,21078
		'checkuser' => array( 'browsearchive' => true, 'deletedhistory' => true, 'deletedtext' => true ), // bug 21044
	),
	'frwikibooks' => array(
		'patroller' => array( 'patrol' => true, 'autopatrol' => true ),
	),
	'frwikinews' => array(
		'flood' => array( 'bot' => true ),
	),
	'frwikisource' => array(
		'patroller' => array( 'patrol' => true, 'autopatrol' => true, 'rollback' => true, ),
		'autopatrolled' => array( 'autopatrol' => true ),
	),
	'frwikiversity' => array(
		'patroller' => array( 'patrol' => true, 'autopatrol' => true ),
	),
	'frwiktionary' => array(
		'patroller' => array( 'patrol' => true, 'autopatrol' => true, 'rollback' => true, ),
		'autopatrolled' => array( 'autopatrol' => true ),
		'botadmin' => array( 'autopatrol' => true, 'autoconfirmed' => true, 'suppressredirect' => true, 'nominornewtalk' => true, 'noratelimit' => true, 'skipcaptcha' => true, 'apihighlimits' => true, 'writeapi' => true,
'bot' => true, 'createaccount' => true, 'import' => true, 'patrol' => true, 'protect' => true, 'editusercss' => true, 'edituserjs' => true, 'editinterface' => true, 'browsearchive' => true, 'movefile' => true, 'move' => true,
'move-rootuserpages' => true, 'undelete' => true, 'rollback' => true, 'delete' => true, 'deleterevision' => true, 'reupload' => true
			),
	),
	'gawiki' => array(
		'rollbacker' => array( 'rollback' => true ),
	),
	'guwiki' => array(
		'autoconfirmed' => array( 'upload' => false, 'reupload' => false ),
		'confirmed' => array( 'upload' => false, 'reupload' => false ),
		'user' => array( 'reupload-own' => false ),
	),
	'guwikisource' => array(
		'user' => array( 'upload' => false ), // Bug 36345
		'sysop' => array( 'upload' => true ), // Bug 36345
	),
	'hewiki' => array(
		'user' => array( 'move' => false, 'upload' => true, ),
		'sysop' => array( 'deleterevision' => true, 'abusefilter-modify-restricted' => true, ),
		'patroller' => array( 'patrol' => true, 'autopatrol' => true, 'unwatchedpages' => true, 'rollback' => true, ),
		'autopatrolled' => array( 'autopatrol' => true ),
		'editinterface' => array( 'abusefilter-hidden-log' => true, 'abusefilter-hide-log' => true, 'abusefilter-log' => true, 'abusefilter-log-detail' => true,
			'abusefilter-modify'  => true, 'abusefilter-modify-restricted' => true, 'abusefilter-revert' => true, 'abusefilter-view' => true,
			'abusefilter-view-private' => true, 'editinterface' => true, 'editusercssjs' => true, 'import' => true, 'tboverride' => true
		),
	),
	'hewikibooks' => array(
		'patroller' => array( 'patrol' => true, 'patrolmarks' => true ),  # bug 27918
		'autopatrolled' => array( 'autopatrol' => true ), # bug 27918
	),
	'hewikiquote' => array(
		'autopatrolled' => array( 'autopatrol' => true ),
	),
	'hiwiki' => array(
		'reviewer' => array ( 'patrol' => true, 'autopatrol' => true ),
		'autopatrolled' => array( 'autopatrol' => true ),
		'sysop' => array( 'validate' => true ),
	),
	'hrwiki' => array(
		'patroller' => array( 'patrol' => true, 'autopatrol' => true, 'rollback' => true ),
		'autopatrolled' => array( 'autopatrol' => true ),
	),
	'huwiki' => array(
		'editor' => array( 'noratelimit' => true ),
		'sysop' => array( 'deleterevision' => true ),
		'user' => array( 'upload' => true ), // http://bugzilla.wikimedia.org/show_bug.cgi?id=28576
		'confirmed' => array( 'upload' => true ), // http://bugzilla.wikimedia.org/show_bug.cgi?id=28576
	),
	'idwiki' => array(
		'*' => array( 'createpage' => false ),
		'rollbacker' => array( 'rollback' => true ), // bug 33508
	),
	'incubatorwiki' => array(
		'bureaucrat' => array( 'upload' => true ),
		'*' => array( 'upload' => false ),
		'user' => array( 'upload' => false ),
		'autoconfirmed' => array( 'upload' => false, 'reupload' => false ),
		'sysop' => array( 'upload' => false ),
		'test-sysop' => array(
			'delete' => true,
			'undelete' => true,
			'deletedhistory' => true,
			'block' => true,
			'blockemail' => true,
			'rollback' => true,
		),
		'translator' => array( 'editinterface' => true, ),
	),
	'iswiki' => array(
		'sysop' => array(
			'renameuser' => true,
		)
	),
	'iswikibooks' => array(
		'user' => array( 'upload' => false ), // http://bugzilla.wikimedia.org/show_bug.cgi?id=11318
		'autoconfirmed' => array( 'upload' => false ),
		'sop' => array( 'upload' => true ),
	),
	'iswikiquote' => array(
		'user' => array( 'upload' => false ), // http://bugzilla.wikimedia.org/show_bug.cgi?id=11317
		'autoconfirmed' => array( 'upload' => false ),
		'sysop' => array( 'upload' => true ),
	),
	'iswiktionary' => array(
		'user' => array( 'upload' => false ), // http://bugzilla.wikimedia.org/show_bug.cgi?id=11187
		'autoconfirmed' => array( 'upload' => false ),
		'sysop' => array( 'upload' => true ),
	),
	// http://bugzilla.wikimedia.org/show_bug.cgi?id=5836
	// http://bugzilla.wikimedia.org/show_bug.cgi?id=11326
	'itwiki' => array(
		'user'	=> array( 'upload' => false, 'move' => false ),
		'autoconfirmed' => array( 'patrol' => true, 'upload' => true ),
		'flood' => array( 'bot' => true, ),
		'rollbacker' => array( 'rollback' => true, 'autopatrol' => true, ),
		'autopatrolled' => array( 'autopatrol' => true ),
	),
	'itwikisource' => array(
		'flood' => array( 'bot' => true ), // http://bugzilla.wikimedia.org/show_bug.cgi?id=36600
	),
	'itwikiversity' => array( 'autoconfirmed' => array( 'patrol' => true ) ),
	'itwikibooks' => array(
		'user' => array( 'patrol' => false, 'move' => false ),
		'autoconfirmed' => array( 'move' => true, 'patrol' => true ),
		'autopatrolled' => array( 'autopatrol' => true, ),
		'patroller' => array( 'autopatrol' => true, 'rollback' => true, ),
		'flooder' => array( 'bot' => true, ), // Bug 39569
	 ),
	'itwikinews' => array( 'autoconfirmed' => array( 'patrol' => true ) ),
	'itwikiquote' => array(
		'autoconfirmed' => array( 'patrol' => true ),
		'sysop' => array( 'autopatrol' => true ),
	),
	'itwikivoyage' => array(
		'autopatrolled' => array( 'autopatrol' => true ), // Bug 43327
		'sysop' => array( 'upload' => true ), // Bug 43524
	),
	'itwiktionary' => array(
		'user' => array( 'upload' => false ),
		'autoconfirmed' => array( 'patrol' => true, 'upload' => false ),
		'sysop' => array( 'upload' => true ),
		'patroller' => array( 'patrol' => true, 'autopatrol' => true, 'rollback' => true ),
		'autopatrolled' => array( 'autopatrol' => true ),
		'import' => array( 'suppressredirect' => true, ),
		'transwiki' => array( 'suppressredirect' => true, ),
		'flood' => array( 'bot' => true, ), // Bug 39306
	),
	'jawiki' => array(
		'autoconfirmed' => array( 'patrol' => true ), // Requested by bug 13055
		'rollbacker' => array(
			'rollback' => true,
			'markbotedits' => true,
			'unwatchedpages' => true,
			'ipblock-exempt' => true,
			'autopatrol' => true,
			'noratelimit' => true,
		),
		'eliminator' => array(
			'delete' => true,
			'nuke' => true,
			'undelete' => true,
			'deletedhistory' => true,
			'deletedtext' => true,
			'browsearchive' => true,
			'deleterevision' => true,
			'suppressredirect' => true,
			'deletelogentry' => true, // Bug 40521
			'unwatchedpages' => true,
			'ipblock-exempt' => true,
			'noratelimit' => true,
		),
		'interface_editor' => array(
			'editinterface' => true,
			'editprotected' => true,
			'edituserjs' => true,
			'editusercss' => true,
			'delete' => true,
			'suppressredirect' => true,
			'ipblock-exempt' => true,
		),
	),
	'jawikinews' => array(
		'user' => array( 'move' => false ), // autoconfirmed only, per request by britty 2007-05-16
	 ),
	'jawikiversity' => array(
		'user' => array( 'upload' => false ),
		'autoconfirmed' => array( 'upload' => false ),
		'sysop' => array( 'upload' => true ),
	),
	'kawiki' => array(
		'editor' => array( 'rollback' => true ),
	),
	'kowiki' => array(
		'rollbacker' => array( 'rollback' => true ),
	),
	'lawiki' => array(
		'user' => array( 'upload' => false ),
		'autoconfirmed' => array( 'upload' => false ),
		'sysop' => array( 'upload' => true ),
	),
	'mediawikiwiki' => array(
		'user' => array( 'move' => false ),
		'autoreview' => array( 'autopatrol' => true ),
		'coder' => array( 'autopatrol' => true ),
		'editor' => array( 'autopatrol' => true ),
		'reviewer' => array( 'autopatrol' => true ),
	),

	'metawiki' => array(
		'user' => array( 'move' => false ), // sigh. tired of this shit. brion -2007-01-10
		'steward' => array( 'userrights-interwiki' => true ), // new steward stuff, yay 2007-12-27
		'flood' => array( 'bot' => true ),
		'autopatrolled' => array( 'autopatrol' => true ),
	),
	'mkwiki' => array(
		'autopatrolled' => array( 'autopatrol' => true ),
		'patroller' => array( 'patrol' => true, 'autopatrol' => true, 'rollback' => true ),
		'autoreviewed' => array( 'autoreview' => true ),
	),
	'mlwiki' => array(
		'autopatrolled' => array( 'autopatrol' => true ),
		'patroller' => array( 'patrol' => true ),
		'rollbacker' => array( 'rollback' => true ),
		'botadmin' => array(
			'blockemail' => true, 'block' => true, 'ipblock-exempt' => true, 'proxyunbannable' => true, 'protect' => true, 'createaccount' => true, 'deleterevision' => true, 'delete' => true, 'globalblock-whitelist' => true, 'editusercss' => true, 'edituserjs' => true,
			'autoconfirmed' => true, 'editinterface' => true, 'autopatrol' => true, 'import' => true, 'centralnotice-admin' => true, 'patrol' => true, 'markbotedits' => true, 'nuke' => true, 'abusefilter-modify' => true, 'movefile' => true, 'move' => true, 'move-subpages' => true,
			'move-rootuserpages' => true, 'noratelimit' => true, 'suppressredirect' => true, 'reupload-shared' => true, 'override-antispoof' => true, 'tboverride' => true, 'reupload' => true, 'skipcaptcha' => true, 'rollback' => true, 'browsearchive' => true, 'unblockself' => true,
			'undelete' => true, 'upload' => true, 'upload_by_url' => true, 'apihighlimits' => true, 'unwatchedpages' => true, 'deletedhistory' => true, 'deletedtext' => true, 'abusefilter-log-detail' => true,
		),
	),
	'mlwikisource' => array(
		'patroller' => array( 'patrol' => true ),
		'autopatrolled' => array( 'autopatrol' => true ),
	),
	'mlwiktionary' => array(
		'botadmin' => array(
			'blockemail' => true, 'block' => true, 'ipblock-exempt' => true, 'proxyunbannable' => true, 'protect' => true, 'createaccount' => true, 'deleterevision' => true, 'delete' => true, 'globalblock-whitelist' => true, 'editusercss' => true, 'edituserjs' => true,
			'autoconfirmed' => true, 'editinterface' => true, 'autopatrol' => true, 'import' => true, 'centralnotice-admin' => true, 'patrol' => true, 'markbotedits' => true, 'nuke' => true, 'abusefilter-modify' => true, 'movefile' => true, 'move' => true, 'move-subpages' => true,
			'move-rootuserpages' => true, 'noratelimit' => true, 'suppressredirect' => true, 'reupload-shared' => true, 'override-antispoof' => true, 'tboverride' => true, 'reupload' => true, 'skipcaptcha' => true, 'rollback' => true, 'browsearchive' => true, 'unblockself' => true,
			'undelete' => true, 'upload' => true, 'upload_by_url' => true, 'apihighlimits' => true, 'unwatchedpages' => true, 'deletedhistory' => true, 'deletedtext' => true, 'abusefilter-log-detail' => true,
		),
	),
	'nlwiki' => array(
		'autoconfirmed' => array( 'patrol' => true  ),
		'checkuser' => array( 'deletedhistory' => true, 'deletedtext' => true, 'browsearchive' => true ),
		'arbcom' => array( 'deletedhistory' => true, 'deletedtext' => true, 'browsearchive' => true ),
		'rollbacker' => array( 'rollback' => true ),
	),
	'nlwiktionary' => array( 'user' => array( 'patrol' => true ) ),
	'nlwikivoyage' => array(
		'autopatrolled' => array(
			'autopatrol' => true, // bug 44082
			'patrol' => true, // bug 44082
		),
	),
	'nlwikibooks' => array( 'user' => array( 'patrol' => true ) ),
	// 'nlwikinews' => array( 'user' => array( 'patrol' => true ) ),
	'nnwiki' => array(
		'autopatrolled' => array( 'autopatrol' => true ),
		'patroller' => array( 'autopatrol' => true, 'patrol' => true, 'rollback' => true ),
	),
	'nowiki' => array(
		'patroller' => array( 'patrol' => true, 'autopatrol' => true, 'rollback' => true, 'unwatchedpages' => true, 'suppressredirect' => true, ),
		'autopatrolled' => array( 'autopatrol' => true, 'unwatchedpages' => true ),
	),
	'nowikibooks' => array(
		'user' => array( 'patrol' => false ),
		'autoconfirmed' => array( 'patrol' => false ),
		'sysop' => array( 'patrol' => true ),
		'patroller' => array( 'patrol' => true, 'autopatrol' => true, 'rollback' => true, 'markbotedits' => true ),
		'autopatrolled' => array( 'autopatrol' => true ),
	),
	'nowikimedia' => array( '*' => array(
		'edit' => false,
	) ),

	'+officewiki' => array(
		'communityapps' => array( 'view-community-applications' => true ),
	),
	'orwiki' => array(
		'rollbacker' => array( 'rollback' => true ),
		'sysop' => array( 'import' => true ),
	),
	// http://bugzilla.wikimedia.org/show_bug.cgi?id=6303
	'plwiki' => array(
		'user' => array( 'upload' => false ),
		'autoconfirmed' => array( 'upload' => true ),
		'editor' => array( 'rollback' => true, 'patrolmarks' => true ), // https://bugzilla.wikimedia.org/show_bug.cgi?id=20154
		'flood' => array( 'bot' => true ), // https://bugzilla.wikimedia.org/show_bug.cgi?id=20155
		'sysop' => array( 'deleterevision' => true ),
	),
	'plwikiquote' =>  array(
		'patroller' => array( 'patrol' => true, 'autopatrol' => true, ), // http://bugzilla.wikimedia.org/show_bug.cgi?id=28479
	),
	'plwikisource' => array(
		'editor' => array( 'patrolmarks' => true ),
	),
	'plwiktionary' => array(
		'editor' => array( 'patrolmarks' => true ),
	),
	// http://bugzilla.wikimedia.org/show_bug.cgi?id=9024 , 10362
	'ptwiki' => array(
		'autoconfirmed' => array( 'patrol' => true, 'abusefilter-log-detail' => true ),
		'bot' => array( 'autoreviewer' => true, ),
		'confirmed' => array( 'patrol' => true ),
		'autoreviewer' => array( 'autopatrol' => true, 'autoreviewer' => true ),
		'eliminator' => array(
			'browsearchive' => true,
			'delete' => true,
			'nuke' => true,
			'undelete' => true,
			'deletedhistory' => true,
			'deletedtext' => true,
			'autopatrol' => true,
			'suppressredirect' => true,
			'autopatrol' => true,
			'autoreviewer' => true
		),
		'rollbacker' => array(
			'rollback' => true,
			'unwatchedpages' => true,
			'block' => true, // https://bugzilla.wikimedia.org/show_bug.cgi?id=35261
		), // https://bugzilla.wikimedia.org/show_bug.cgi?id=27563
		'user' => array(
			'move' => false,
			'move-rootuserpages' => false,
		),
		'sysop' => array( 'autoreviewer' => true ),
		'bureaucrat' => array(
			'move-rootuserpages' => true,
			'autopatrol' => true,
			'autoreviewer' => true
		),
		'interface_editor' => array( // Bug 39905
			'editinterface' => true,
			'edituserjs' => true,
			'editusercss' => true,
		),
	),
	'quwiki' => array(
	 	'rollbacker' => array( 'rollback' => true ),
	),
	'rowiki' => array(
	 	'sysop' => array( 'autopatrol' => true, 'patrol' => true, ),
	 	'patroller' => array( 'patrol' => true, ),
	 	'autopatrolled' => array( 'autopatrol' => true, ),
	),
	// http://bugzilla.wikimedia.org/show_bug.cgi?id=12334
	'ruwiki' => array(
		'*' => array( 'patrolmarks' => true, ),
		'user' => array( 'upload' => false ),
		'autoconfirmed' => array( 'upload' => false ),
		'rollbacker' => array( 'rollback' => true ),
		'uploader' => array( 'upload' => true ),
		'closer' => array( 'delete' => true, 'suppressredirect' => true,  'upload' => true, ),
		'filemover' => array( 'movefile' => true, 'suppressredirect' => true, 'upload' => true, ), // bug 30984
		'suppressredirect' => array( 'suppressredirect' => true, ), // bug 38408
		'sysop' => array( 'upload' => true, ),
	),
	'ruwikiquote' => array(
		'autoeditor' => array( 'autoreview' => true, 'autoconfirmed' => true ),
	),
	'ruwikisource' => array(
		'autoeditor' => array( 'autoreview' => true, 'autoconfirmed' => true ),
		'rollbacker' => array( 'rollback' => true, 'suppressredirect' => true ),
		'flood' => array( 'bot' => true ),
	),
	'ruwikiversity' => array(
		'patroller' => array( 'patrol' => true, 'autopatrol' => true ),
	),
	'sawiki' => array(
		'autopatrolled' => array( 'autopatrol' => true ),
	),
	'sawikisource' => array(
		'autopatrolled' => array( 'autopatrol' => true ),
	),
	'scowiki' => array(
		'rollbacker' => array( 'rollback' => true ),
		'autopatrolled' => array( 'autopatrol' => true, ),
	),
	'sewikimedia' => array(
		'*' => array(
			'edit' => false,
			'editallpages' => false,
		),
		'user' => array(
			'upload' => false,
			'editallpages' => true, // Bug 39671
		),
		'sysop' => array(
			'upload' => false,
			'editallpages' => true,
		),
		'medlem' => array(
			'move' => true,
			'move-subpages' => true,
			'read' => true,
			'edit' => true,
			'createpage' => true,
			'createtalk' => true,
			'upload' => true,
			'reupload' => true,
			'reupload-shared' => true,
			'minoredit' => true,
			'purge' => true,
			'editallpages' => true,
		),
	),
	'simplewiki' => array(
		'flood' => array( 'bot' => true ),
		'rollbacker' => array( 'rollback' => true, ),
		# bug 27875 (see comment 4)
		'patroller' => array( 'patrol' => true, 'autopatrol' => true ),
	),
	// 'simplewikibooks' => array(
		// 'flood' => array( 'bot' => true ),
		// 'rollbacker' => array( 'rollback' => true, ),
	// ),
	'simplewiktionary' => array(
		'rollbacker' => array( 'rollback' => true, ),
		'autopatrolled' => array( 'patrol' => true, 'autopatrol' => true ),
	),
	'siwiki' => array(
		'rollbacker'    => array( 'rollback' => true ),
		'autopatrolled' => array( 'autopatrol' => true ),
	),
	'skwiki' => array(
		'rollbacker' => array( 'rollback' => true ),
	),
	'srwiki' => array(
		'user' => array( 'upload' => false ),
		'autoconfirmed' => array( 'upload' => true ),
		'patroller' => array( 'patrol' => true ),
		'autopatrolled' => array( 'autopatrol' => true ),
		'rollbacker' => array( 'rollback' => true ),
		'flood' => array( 'bot' => true ),
	),
	'srwikisource' => array(
		'patroller' => array( 'patrol' => true ),
		'autopatrolled' => array( 'autopatrol' => true ),
	),
	'+stewardwiki' => array(
		'bureaucrat' => array( 'userrights' => true ), // http://bugzilla.wikimedia.org/show_bug.cgi?id=28773
	),
	'svwiki' => array(
		'autoconfirmed' => array( 'patrol' => true ),
		'rollbacker'	=> array( 'rollback' => true, 'autopatrol' => true ),
	),
	'svwikisource' => array( // Bug 28614 & 36895
		'autopatrolled' => array( 'autopatrol' => true, 'suppressredirect' => true, 'upload' => true, 'reupload' => true ),
		'autoconfirmed' => array( 'upload' => false, 'reupload' => false ),
		'confirmed' => array( 'upload' => false, 'reupload' => false ),
	),
	'svwiktionary' => array(
		'user' => array( 'upload' => false ),
		'autoconfirmed' => array( 'upload' => false ),
	),
	'tawiki' => array(
		'nocreate' => array( 'createpage' => false, ),
	),
	'testwiki' => array(
		'accountcreator' => array( 'override-antispoof' => true, 'tboverride' => true ),
		'filemover' => array( 'movefile' => true ), // bug 30121
		'user' => array(
			'upload_by_url' => true, // For wider testing
			'upload' => true, // Exception to bug 12556, used for testing of upload tools
		),
		'sysop' => array( 'deleterevision' => true ),
		'reviewer' => array(
			'stablesettings' => true,
		),
		'researcher' => array( 'browsearchive' => true, 'deletedhistory' => true, 'apihighlimits' => true ),
		'rollbacker' => array( 'rollback' => true ),
	),
	'test2wiki' => array(
		'user' => array(
			'upload_by_url' => true, // For testing of Flickr uploading via UploadWizard
			'upload' => true,
		),
	),
	'trwiki' => array(
		'autoreview' => array(
			'autopatrol' => true, // Bug 38690
		),
		'checkuser' => array(
			'browsearchive' => true, // Bug 38690
			'deletedhistory' => true, // Bug 38690
			'deletedtext' => true, // Bug 38690
		),
		'oversight' => array(
			'browsearchive' => true, // Bug 38690
			'deletedhistory' => true, // Bug 38690
			'deletedtext' => true, // Bug 38690
		),
		'patroller' => array(
			'autoconfirmed' => true, // Bug 38690
			'autopatrol' => true, // Bug 38690
			'autoreview' => true, // Bug 38690
			'patrol' => true,
			'review' => true, // Bug 38690
			'rollback' => true, // Bug 38690
			'unreviewedpages' => true, // Bug 38690
		),
		'sysop' => array(
			'review' => true, // Bug 38690
			'unreviewedpages' => true, // Bug 38690
		),
		'technician' => array(
			'abusefilter-log-detail' => true, // Bug 38690
			'abusefilter-modify' => true, // Bug 38690
			'apihighlimits' => true, // Bug 38690
			'editinterface' => true, // Bug 38690
			'editusercss' => true, // Bug 38690
			'edituserjs' => true, // Bug 38690
			'noratelimit' => true, // Bug 38690
		),
	),
	'ukwiki' => array(
		'patroller' => array( 'patrol' => true, 'autopatrol' => true, ),
		'rollbacker' => array( 'rollback' => true ),
	),
	'ukwiktionary' => array(
		'autoeditor' => array( 'autoreview' => true ),
	),
	'ukwikimedia' => array(
		'sysop' => array( 'importupload' => true ), // https://bugzilla.wikimedia.org/show_bug.cgi?id=17167
	),
	'vecwiki' => array(
		'flood' => array( 'bot' => true ),
	),
	'viwiki' => array(
		'rollbacker' => array( 'rollback' => true ),
		'flood' => array( 'bot' => true ),
	),
	'wikidatawiki' => array(
		'autopatrolled' => array(
			'autopatrol' => true, // bug 41907
			'patrol' => true, // bug 42052
		),
	),
	// due to mass vandalism complaint, 2006-04-11
	'zhwiki' => array(
		// '*' => array( 'createpage' => false ),  # re-enabled createpage priv according to bug 25142, then redisabled *sigh*, then reenabled \o/
		'rollbacker' => array( 'rollback' => true, 'abusefilter-log-private' => true ), # Bugs 16988 & 37679
		'patroller' => array( 'patrol' => true, 'autopatrol' => true ),
		'autoreviewer' => array( 'autopatrol' => true ),
		'flood' => array( 'bot' => true ),
	),

	'zhwikinews' => array(
		'rollbacker' => array( 'rollback' => true ), # 27268
		'user' => array( 'upload' => false, 'reupload' => false ),
		'autoconfirmed' => array( 'upload' => false, 'reupload' => false ),
		'sysop' => array( 'upload' => true, 'reupload' => true ) ),

	// http://bugzilla.wikimedia.org/show_bug.cgi?id=5836
	'zhwiktionary' => array( 'bot' => array( 'patrol' => true ) ),
	'zh_yuewiki' => array(
		'autoconfirmed' => array( 'patrol' => true ),
		'rollbacker' => array( 'rollback' => true ),
		'autoreviewer' => array( 'autopatrol' => true ),
		'confirmed' => array( 'upload' => true, 'patrol' => true ),
	),

	'+strategywiki' => array( 'flood' => array( 'bot' => true ) ),
),
# @} end of groupOverrides

# groupOverrides2 @{
'groupOverrides2' => array(
	'default' => array(
		'sysop' => array(
			'importupload' => false,
			'suppressredirect' => true, // http://meta.wikimedia.org/w/index.php?title=Wikimedia_Forum&oldid=1371655#Gives_sysops_to_.22suppressredirect.22_right
			'noratelimit' => true,
			'deleterevision' => true,
			'deletelogentry' => true,
		),
		'bot' => array(
			'noratelimit' => true,
		),
		'accountcreator' => array(
			'noratelimit' => true,
		),
		'bureaucrat' => array(
			'noratelimit' => true,
		),
		'steward' => array(
			'noratelimit' => true,
		),
		// 'rollback' => array( 'rollback' => true, ),
		'import' => array( 'importupload' => true, 'import' => true ),
		'transwiki' => array( 'import' => true ),
		'user' => array(
			'reupload-shared' => false,
			'reupload' => false,
			'upload' => false, // https://bugzilla.wikimedia.org/show_bug.cgi?id=12556
			'reupload-own' => true,
			'move' => false, // http://bugzilla.wikimedia.org/show_bug.cgi?id=12071
			'move-subpages' => false, // for now...
			'movefile' => false, // r93871 CR
		),
		'autoconfirmed' => array(
			'reupload' => true,
			'upload' => true, // https://bugzilla.wikimedia.org/show_bug.cgi?id=12556
			'move' => true,
			'collectionsaveasuserpage' => true,
			'collectionsaveascommunitypage' => true,
		),
		// Deployed to all wikis by Andrew, 2009-04-28
		'ipblock-exempt' => array(
			'ipblock-exempt' => true,
			'torunblocked' => true,
		),

		# So that I can rename users with more than 6800 edits -- TS
		# Removed as obsolete -- 2009-03-05 BV
		# 'developer' => array( 'siteadmin' => true ),

		# To allow for inline log suppression -- 2009-01-29 -- BV
		'oversight' => array(
			'deleterevision' => true,
			'deletelogentry' => true,
			'hideuser' => true, // was forgotten. added 2009-03-05 -- BV
			'suppressrevision' => true,
			'suppressionlog' => true,
			'abusefilter-hide-log' => true, // Andrew, 2010-08-28
			'abusefilter-hidden-log' => true, // Andrew, 2010-08-28
		),
	),
),
# @} end of wgGroupOverrides2

# wgAddGroups @{
'wgAddGroups' => array(
	// The '+' in front of the DB name means 'add to the default'. It saves us duplicating
	// changes to the default across all overrides --Andrew 2009-04-28
	'default' => array(
		'bureaucrat' => array( 'sysop', 'bureaucrat', 'bot' ),
		'sysop' => array( 'ipblock-exempt' ),
	),
	'+testwiki' => array(
		'bureaucrat' => array( 'researcher' ),
		'sysop' => array( 'filemover', 'rollbacker', 'accountcreator', 'confirmed' ),
		# 'user' => array( 'editor', 'reviewer' ),
	),
	// ******************************************************************
	'+arwiki' => array(
		'bureaucrat' => array( 'import', 'reviewer', 'abusefilter' ),
		'sysop' => array( 'uploader', 'reviewer', 'confirmed', 'rollbacker', 'abusefilter', ),
	),
	'+arwikisource' => array(
		'sysop' => array( 'patroller', 'autopatrolled', ),
	),
	'+bewiki' => array(
		'sysop' => array( 'autoeditor' ),
	),
	'+be_x_oldwiki' => array(
		'sysop' => array( 'abusefilter' ),
	),
	'+bgwiki' => array(
		'bureaucrat' => array( 'patroller' ),
		'sysop' => array( 'autopatrolled' ),
	),
	'+bnwiki' => array(
		'sysop' => array( 'autopatrolled', 'rollbacker', 'flood' ),
		'bureaucrat' => array( 'import' ),
	),
	'+bswiki' => array(
		'bureaucrat' => array( 'patroller', 'autopatrolled', 'rollbacker' )
	),
	'+cawiki' => array(
		'sysop' => array( 'rollbacker', 'autopatrolled', 'abusefilter', ),
	),
	'+checkuserwiki' => array(
		'bureaucrat' => array( 'accountcreator', 'import', 'transwiki', 'user', 'autoconfirmed', 'ipblock-exempt', ),
	),
	'+cswiki' => array(
		'bureaucrat' => array( 'autopatrolled' ),
	),
	'+cswikiquote' => array(
		'bureaucrat' => array( 'autopatrolled' ),
	),
	'+cswikisource' => array(
		'bureaucrat' => array( 'autopatrolled' ),
	),
	'+cswiktionary' => array(
		'bureaucrat' => array( 'autopatrolled' ),
	),
	'+commonswiki' => array(
		'bureaucrat' => array( 'ipblock-exempt', 'OTRS-member' ),
		'checkuser'  => array( 'ipblock-exempt' ),
		'sysop' => array( 'rollbacker', 'confirmed', 'patroller', 'autopatrolled', 'filemover', 'Image-reviewer'  ),
		'steward' => array( 'OTRS-member' ),
		'Image-reviewer' => array( 'Image-reviewer' ),
	),
	'+dawiki' => array(
		'sysop' => array( 'patroller', 'autopatrolled' ),
	),
	'+donatewiki' => array(
		'sysop' => array( 'inactive', 'flood' ),
		'bureaucrat' => array( 'import', 'transwiki', 'inactive' ),
	),
	'+elwiktionary' => array(
		'bureaucrat' => array( 'interface_editors' ),
		'sysop' => array( 'autopatrolled' ),
	),
	'+enwiki' => array(
		'bureaucrat' => array( 'accountcreator' ),
		'sysop' => array( 'abusefilter', 'accountcreator', 'autoreviewer', 'confirmed', 'filemover', 'reviewer', 'rollbacker' ),
	),
	'+enwikibooks' => array(
		'sysop' => array( 'transwiki', 'uploader' ),
		'bureaucrat' => array( 'flood' ),
	),
	'+enwikinews' => array(
		'bureaucrat' => array ( 'flood' ),
		'sysop' => array ( 'flood' ),
	),
	'+enwikivoyage' => array(
		'sysop' => array( 'autopatrolled' ),
		'bureaucrat' => array( 'patroller' ),
	),
	'+eswiki' => array(
		'bureaucrat' => array( 'rollbacker', 'confirmed' ),
		'sysop' => array( 'rollbacker', 'autopatrolled', 'patroller' ),
	),
	'+eswikibooks' => array(
		'bureaucrat' => array( 'flood' ),
		'sysop' => array( 'flood', 'rollbacker' ),
	),
	'+eswikinews' => array(
		'bureaucrat' => array( 'editprotected', 'flood', ),
	),
	'+eswiktionary' => array(
		'bureaucrat' => array( 'autopatrolled', 'patroller', 'rollbacker' ),
	),
	'+enwikisource' => array(
		'bureaucrat' => array( 'autopatrolled', 'flood' ), # https://bugzilla.wikimedia.org/show_bug.cgi?id=36863
		'sysop' => array( 'abusefilter', 'autopatrolled' ),
	),
	'+enwiktionary' => array(
		'sysop' => array( 'autopatrolled', 'flood', 'patroller', 'rollbacker' ),
	),
	'+fawiki' => array(
		'bureaucrat' => array( 'patroller' ),
		'sysop' => array( 'rollbacker', 'autopatrol' ),
	),
	'+fawikinews' => array(
		'sysop' => array( 'rollbacker', 'patroller' ),
	),
	'+fiwiki' => array(
		'bureaucrat' => array( 'arbcom' ),
		'sysop' => array( 'rollbacker' ),
	),
	'+foundationwiki' => array(
		'sysop' => array( 'inactive', 'flood' ),
		'bureaucrat' => array( 'import', 'transwiki', 'inactive' ),
	),
	'+frwiki' => array(
		'bureaucrat' => array( 'abusefilter', 'accountcreator' ),
	),
	'+frwikibooks' => array(
		'bureaucrat' => array( 'abusefilter' ),
		'sysop' => array( 'patroller' ),
	),
	'+frwikisource' => array(
		'sysop' => array( 'patroller', 'autopatrolled' ),
	),
	'+frwikiversity' => array(
		'sysop' => array( 'patroller' ),
	),
	'+frwiktionary' => array(
		'bureaucrat' => array( 'accountcreator', 'import', 'patroller', 'transwiki', 'autopatrolled', 'confirmed', 'abusefilter', 'botadmin' ),
	),
	'+gawiki' => array(
		'sysop' => array( 'rollbacker' ),
	),
	'+hewiki' => array(
		'sysop' => array( 'patroller', 'autopatrolled', 'accountcreator' ),
		'bureaucrat' => array( 'editinterface' ),
	),
	'+hewikibooks' => array(
		'sysop' => array( 'patroller', 'autopatrolled' ),
	),
	'+hewikiquote' => array(
		'sysop' => array( 'autopatrolled' ),
	),
	'+hiwiki' => array(
		'sysop' => array( 'abusefilter', 'autopatrolled', 'reviewer' ),
	),
	'+hrwiki' => array(
		'bureaucrat' => array( 'patroller', 'autopatrolled' ),
		'sysop' => array( 'patroller', 'autopatrolled' ),
	),
	'+idwiki' => array(
		'sysop' => array( 'rollbacker' ),
		'bureaucrat' => array( 'rollbacker' ),
	),
	'+incubatorwiki' => array(
		'bureaucrat' => array( 'test-sysop', 'translator', 'import' ),
	),
	'+itwiki' => array(
		'bureaucrat' => array( 'rollbacker' ),
		'sysop' => array( 'autopatrolled', 'flood', ),
	),
	'+itwikibooks' => array(
		'sysop' => array( 'autopatrolled', 'patroller' ),
	),
	'+itwikivoyage' => array(
		'sysop' => array( 'autopatrolled' ),          // Bug 43327
		'bureaucrat' => array ( 'autopatrolled' ),
	),
	'+itwiktionary' => array(
		'sysop' => array( 'patroller', 'autopatrolled' ),
	),
	'+jawiki' => array(
		'sysop' => array( 'abusefilter' ),
		'bureaucrat' => array( 'rollbacker', 'eliminator', 'interface_editor' ),
	),
	'+kawiki' => array(
		'sysop' => array( 'trusted' ),
	),
	'+kowiki' => array(
		'sysop' => array( 'rollbacker' ),
	),
	'+ltwiki' => array(
		'sysop' => array ( 'abusefilter' ),
	),
	'+ltwiktionary' => array(
		'sysop' => array( 'abusefilter' ),
	),
	'+mediawikiwiki' => array(
		'bureaucrat' => array( 'transwiki', 'coder', 'import', 'svnadmins' ),
		'coder' => array( 'coder' ),
	),
	'+metawiki' => array(
		'bureaucrat' => array( 'ipblock-exempt', ),
		'checkuser'  => array( 'ipblock-exempt' ),
		'sysop'      => array( 'autopatrolled' ),
	),
	'+mkwiki' => array(
		'bureaucrat' => array( 'patroller', 'autopatrolled', 'autoreviewed' ),
	),
	'+mlwiki' => array(
		'sysop' => array( 'patroller', 'autopatrolled', 'rollbacker' ),
		'bureaucrat' => array( 'botadmin' ),
	),
	'+mlwikisource' => array(
		'sysop' => array( 'patroller', 'autopatrolled' ),
	),
	'+mlwiktionary' => array(
		'bureaucrat' => array( 'botadmin' ),
	),
	'+nlwiki' => array(
		'bureaucrat' => array( 'abusefilter', 'arbcom', 'rollbacker' ),
	),
	'+nlwikibooks' => array(
		'sysop' => array( 'abusefilter' ),
	),
	'+nnwiki' => array(
		'bureaucrat' => array( 'autopatrolled', 'patroller' ),
		'sysop' => array( 'autopatrolled', 'patroller' ),
	),
	'+nlwikivoyage' => array(
		'bureaucrat' => array( 'autopatrolled' ), // bug 44082
	),
	'+noboard_chapterswikimedia' => array(
		'bureaucrat' => array( 'import', 'transwiki' ),
	),
	'+nowiki' => array(
		'bureaucrat' => array( 'patroller', 'autopatrolled', 'accountcreator' ),
		'sysop' => array( 'patroller', 'autopatrolled', 'abusefilter', 'transwiki' ),
	),
	'+nowikibooks' => array(
		'bureaucrat' => array( 'autopatrolled', 'patroller' ),
		'sysop' => array( 'autopatrolled', 'patroller' ),
	),
	'+officewiki' => array(
		'bureaucrat' => array( 'import', 'transwiki', 'communityapps' ),
	),
	'+orwiki' => array(
		'sysop' => array( 'rollbacker' ),
	),
	'+otrs_wikiwiki' => array(
		'bureaucrat' => array( 'import', 'transwiki' ),
	),
	'+plwiki' => array(
		'bureaucrat' => array( 'abusefilter', 'flood' ),
	),
	'+plwikiquote' => array(
	 'sysop' => array( 'patroller' ), // http://bugzilla.wikimedia.org/show_bug.cgi?id=28479
	),
	'+ptwikinews' => array(
		'sysop' => array( 'reviewer' ),
	),
	'+private' => array( // Cary made me do it! --Andrew 2009-05-01
		'bureaucrat' => array( 'inactive' ),
	),
	'+ptwiki' => array(
		'bureaucrat' => array( 'eliminator', 'confirmed', 'autoreviewer', 'interface_editor' ), // Bug 39905
		'sysop' => array( 'rollbacker', 'autoreviewer', 'confirmed' ),
	),
	'+quwiki' => array(
		'sysop' => array( 'rollbacker' ),
	),
	'+rowiki' => array( // bug 26634 --hashar 2011-01-14
		'sysop' => array( 'autopatrolled' ),
		'bureaucrat' => array( 'abusefilter', 'patroller' ),
	),
	'+ruwiki' => array(
		'sysop' => array( 'rollbacker', 'autoeditor', 'uploader', 'closer', 'filemover', 'suppressredirect' ),
	),
	'+ruwikiquote' => array(
		'sysop' => array( 'autoeditor' )
	),
	'+ruwikisource' => array(
		'bureaucrat' => array( 'autoeditor', 'rollbacker' ),
		'sysop' => array( 'autoeditor', 'rollbacker', 'abusefilter', 'flood' ),
	),
	'+sawiki' => array(
		'sysop' => array( 'autopatrolled' ),
	),
	'+sawikisource' => array(
		'sysop' => array( 'autopatrolled' ),
	),
	'+scowiki' => array(
		'sysop' => array( 'rollbacker', 'autopatrolled', )
	),
	'+sewikimedia' => array(
		'bureaucrat' => array( 'medlem' ),
		'sysop' => array( 'medlem' ),
	),
	'+simplewiki' => array(
		'bureaucrat' => array( 'rollbacker', 'transwiki', 'patroller' ),
		'sysop' => array( 'rollbacker', 'flood', 'patroller' ),
	),
	'+siwiki' => array(
		'sysop' => array( 'rollbacker', 'accountcreator', 'abusefilter', 'autopatrolled', 'confirmed', 'reviewer' ),
	),
	'+simplewiktionary' => array(
		'sysop' => array( 'rollbacker', 'autopatrolled' ),
	),
	'+skwiki' => array(
		'sysop' => array( 'rollbacker' ),
	),
	'+sqwiki' => array(
		'bureaucrat' => array( 'editor' ),
		'sysop' => array( 'reviewer' ),
	),
	'+srwiki' => array(
		'bureaucrat' => array( 'patroller', 'autopatrolled', 'rollbacker', 'flood' ),
		'sysop' => array( 'rollbacker' ),
	),
	'+srwikisource' => array(
		'bureaucrat' => array( 'patroller', 'autopatrolled', ),
	),
	'+stewardwiki' => array(
		'bureaucrat' => array( 'import', 'transwiki', 'user', 'autoconfirmed', 'ipblock-exempt' ),
	),
	'+svwiki' => array(
		'sysop' => array( 'rollbacker' ),
	),
	'+svwikisource' => array(
		'sysop' => array( 'autopatrolled', ),
	),
	'+tawiki' => array(
		'bureaucrat' => array( 'nocreate' ),
	),
	'+trwiki' => array(
		'sysop' => array( 'patroller' ), // bureaucrat -> sysop, Bug 38690
		'bureaucrat' => array( 'technician' ), // Bug 39690
	),
	'+ukwiki' => array(
		'sysop' => array( 'patroller', 'rollbacker' ),
	),
	'+ukwiktionary' => array(
		'bureaucrat' => array( 'autoeditor' ),
		'sysop' => array( 'autoeditor' ),
	),
	'+urwiki' => array(
		'bureaucrat' => array( 'import', 'confirmed' ), // Bug 42737
		'sysop' => array( 'confirmed' ), // Bug 42737
	),
	'+viwiki' => array(
		'sysop' => array( 'rollbacker', 'flood' ),
		'bureaucrat' => array( 'flood' ),
	),
	'+vecwiki' => array(
		'sysop' => array( 'flood' ),
	),
	'+wikidatawiki' => array(
		'sysop' => array( 'autopatrolled' ), // bug 41907
	),
	'+zhwiki' => array(
		'bureaucrat' => array( 'flood' ),
		'sysop' => array( 'patroller', 'rollbacker', 'autoreviewer', 'confirmed' ),
	),
	'+zhwikinews' => array(
		'sysop' => array( 'rollbacker' ),
	),
	'+zh_yuewiki' => array(
		'sysop' => array( 'abusefilter', 'rollbacker', 'autoreviewer', 'confirmed' ),
	),
),
# @} end of wgAddGroups

# wgRemoveGroups @{
'wgRemoveGroups' => array(
	// The '+' in front of the DB name means 'add to the default'. It saves us duplicating
	// changes to the default across all overrides --Andrew 2009-04-28
	'default' => array(
		'bureaucrat' => array( 'bot' ),
		'sysop' => array( 'ipblock-exempt' ),
	),
	'+testwiki' => array(
		'bureaucrat' => array( 'sysop', 'researcher', 'translationadmin' ),
		'sysop' => array( 'filemover', 'rollbacker', 'accountcreator', 'confirmed' ),
		# 'user' => array( 'editor', 'reviewer' ),
	),
	// ******************************************************************
	'+arbcom_enwiki' => array(
	  'bureaucrat' => array( 'sysop', 'bureaucrat' ),
	),
	'+arbcom_fiwiki' => array(
		'bureaucrat' => array( 'sysop', 'bureaucrat' ),
	),
	'+arwiki' => array(
		'bureaucrat' => array( 'import', 'reviewer', 'abusefilter' ),
		'sysop' => array( 'uploader', 'reviewer', 'confirmed', 'rollbacker', 'abusefilter', 'patroller', 'autopatrolled', ),
	),
	'+arwikisource' => array(
		'sysop' => array( 'patroller', 'autopatrolled', ),
	),
	'+bawiki' => array(
		'bureaucrat' => array( 'sysop' ),
	),
	'+bewiki' => array(
		'sysop' => array( 'autoeditor' ),
	),
	'+be_x_oldwiki' => array(
		'sysop' => array( 'abusefilter' ),
	),
	'+bgwiki' => array(
		'bureaucrat' => array( 'patroller' ),
		'sysop' => array( 'autopatrolled' ),
	),
	'+bnwiki' => array(
		'sysop' => array( 'autopatrolled', 'rollbacker', 'flood' ),
		'bureaucrat' => array( 'import' ),
	),
	'+bswiki' => array(
		'bureaucrat' => array( 'patroller', 'autopatrolled', 'rollbacker' )
	),
	'+cawiki' => array(
		'sysop' => array( 'rollbacker', 'autopatrolled', 'abusefilter', ),
	),
	'+checkuserwiki' => array(
		 'bureaucrat' => array( 'sysop', 'accountcreator', 'import', 'transwiki', 'user', 'autoconfirmed', 'ipblock-exempt',  'bureaucrat', ),
	),
	'+commonswiki' => array(
		'bureaucrat' => array( 'ipblock-exempt', 'OTRS-member' ),
		'checkuser' => array( 'ipblock-exempt' ),
		'sysop' => array( 'rollbacker', 'confirmed', 'patroller', 'autopatrolled', 'filemover', 'Image-reviewer' ),
		'steward' => array( 'OTRS-member' ),
	),
	'+cswiki' => array(
		'bureaucrat' => array( 'autopatrolled' ),
	),
	'+cswikiquote' => array(
		'bureaucrat' => array( 'autopatrolled' ),
	),
	'+cswikisource' => array(
		'bureaucrat' => array( 'autopatrolled' ),
	),
	'+cswiktionary' => array(
		'bureaucrat' => array( 'autopatrolled' ),
	),
	'+dawiki' => array(
		'sysop' => array( 'patroller', 'autopatrolled' ),
	),
	'+donatewiki' => array(
		'sysop' => array( 'inactive', 'confirmed', 'flood' ),
		'bureaucrat' => array( 'sysop', 'bureaucrat', 'import', 'transwiki', 'inactive', 'confirmed' ),
	),
	'+elwiktionary' => array(
		'bureaucrat' => array( 'interface_editors' ),
		'sysop' => array( 'autopatrolled' ),
	),
	'+enwiki' => array(
		'bureaucrat' => array( 'ipblock-exempt', 'accountcreator', 'sysop' ),
		'sysop' => array( 'rollbacker', 'accountcreator', 'abusefilter', 'autoreviewer', 'confirmed', 'reviewer', 'filemover' ),
	),
	'+enwikibooks' => array(
		'sysop' => array( 'transwiki', 'uploader', ),
		'bureaucrat' => array( 'flood' ),
	),
	'+enwikinews' => array(
		'bureaucrat' => array ( 'flood', 'sysop' ),
		'sysop' => array ( 'flood' ),
	),
	'+enwikivoyage' => array(
		'sysop' => array( 'autopatrolled' ),
		'bureaucrat' => array( 'sysop', 'patroller' ),
	),
	'+enwikisource' => array(
		'bureaucrat' => array( 'autopatrolled', 'flood' ), # https://bugzilla.wikimedia.org/show_bug.cgi?id=36863
		'sysop' => array( 'abusefilter', 'autopatrolled', ),
	),
	'+eswiki' => array(
		'bureaucrat' => array( 'rollbacker', 'confirmed' ),
		'sysop' => array( 'rollbacker', 'autopatrolled', 'patroller'  ),
	),
	'+eswikibooks' => array(
		'bureaucrat' => array( 'flood' ),
		'sysop' => array( 'flood', 'rollbacker' ),
	),
	'+eswikinews' => array(
		'bureaucrat' => array( 'editprotected', 'flood', ),
	),
	'+eswiktionary' => array(
		'bureaucrat' => array( 'autopatrolled', 'patroller', 'rollbacker' ),
	),
	'+enwiktionary' => array(
		'bureaucrat' => array( 'sysop', 'bureaucrat', ),
		'sysop' => array( 'autopatrolled', 'patroller', 'rollbacker', 'flood' ),
	),
	'+fawiki' => array(
		'bureaucrat' => array( 'patroller' ),
		'sysop' => array( 'rollbacker', 'autopatrol' ),
	),
	'+fawikinews' => array(
		'sysop' => array( 'rollbacker', 'patroller', ),
	),
	'+fiwiki' => array(
		'bureaucrat' => array( 'sysop', 'bureaucrat', 'arbcom' ),
		'sysop' => array( 'rollbacker', ),
	),
	'+fiwikimedia' => array(
		'bureaucrat' => array( 'sysop', 'bureaucrat' ),
	),
	'+foundationwiki' => array(
		'sysop' => array( 'inactive', 'confirmed', 'flood' ),
		'bureaucrat' => array( 'sysop', 'bureaucrat', 'import', 'transwiki', 'inactive', 'confirmed' ),
	),
	'+frwiki' => array(
		'bureaucrat' => array( 'abusefilter', 'sysop', 'accountcreator' ),
	),
	'+frwikibooks' => array(
		'bureaucrat' => array( 'abusefilter' ),
		'sysop' => array( 'patroller' ),
	),
	'+frwikisource' => array(
		'sysop' => array( 'patroller', 'autopatrolled' ),
	),
	'+frwikiversity' => array(
		'sysop' => array( 'patroller' ),
	),
	'+frwiktionary' => array(
		'bureaucrat' => array( 'accountcreator', 'import', 'patroller', 'transwiki', 'autopatrolled', 'confirmed', 'abusefilter', 'botadmin' ),
	),
	'+gawiki' => array(
		'sysop' => array( 'rollbacker' ),
	),
	'+hewiki' => array(
		'sysop' => array( 'patroller', 'autopatrolled', 'accountcreator' ),
		'bureaucrat' => array( 'editinterface' ),
	),
	'+hewikibooks' => array(
		'sysop' => array( 'patroller', 'autopatrolled' ),
	),
	'+hewikiquote' => array(
		'sysop' => array( 'autopatrolled' ),
	),
	'+hiwiki' => array(
		'sysop' => array( 'abusefilter', 'autopatrolled', 'reviewer' ),
	),
	'+hiwiktionary' => array(
			'bureaucrat' => array( 'sysop', ),
	),
	'+hrwiki' => array(
		'bureaucrat' => array( 'patroller', 'autopatrolled' ),
		'sysop' => array( 'patroller', 'autopatrolled' ),
	),
	'+huwiki' => array(
		'bureaucrat' => array( 'confirmed' ),
	),
	'+idwiki' => array(
		'sysop' => array( 'rollbacker' ),
		'bureaucrat' => array( 'rollbacker' ),
	),
	'+incubatorwiki' => array(
		'bureaucrat' => array( 'test-sysop', 'translator', 'import', 'translationadmin' ),
	),
	'+itwiki' => array(
		'bureaucrat' => array( 'rollbacker', 'autopatrolled' ),
		'sysop' => array( 'flood', ),
	),
	'+itwikibooks' => array(
		'sysop'	 => array( 'autopatrolled', 'patroller', 'flooder' ), // Flooder added per bug 39569
	),
	'+itwikisource' => array(
		'bureaucrat' => array( 'flood' ), // Bug 36600
	),
	'+itwikivoyage' => array(
		'bureaucrat' => array ( 'autopatrolled' ),    // Bug 43327
	),
	'+itwiktionary' => array(
		'sysop' => array( 'patroller', 'autopatrolled', 'flood' ), // Flood added per bug 39306
	),
	'+jawiki' => array(
		'sysop' => array( 'abusefilter' ),
		'bureaucrat' => array( 'rollbacker', 'eliminator', 'interface_editor' ),
	),
	'+kawiki' => array(
		'sysop' => array( 'trusted' ),
	),
	'+kowiki' => array(
		'sysop' => array( 'rollbacker' ),
	),
	'+ltwiki' => array(
		'sysop' => array ( 'abusefilter' ),
	),
	'+ltwiktionary' => array(
		'sysop' => array( 'abusefilter' ),
	),
	'+mediawikiwiki' => array(
		'bureaucrat' => array( 'transwiki', 'import', 'coder', 'svnadmins' ),
	),
	'+metawiki' => array(
		'bureaucrat' => array( 'sysop', 'bureaucrat', 'ipblock-exempt', 'flood', 'translationadmin' ), // https://bugzilla.wikimedia.org/show_bug.cgi?id=37198
		'checkuser'  => array( 'ipblock-exempt' ),
		'sysop'      => array( 'autopatrolled' ),
	),
	'+mkwiki' => array(
		'bureaucrat' => array( 'patroller', 'autopatrolled', 'autoreviewed' ),
	),
	'+mlwiki' => array(
		'sysop' => array( 'patroller', 'autopatrolled', 'rollbacker' ),
		'bureaucrat' => array( 'botadmin' ),
	),
	'+mlwikisource' => array(
		'sysop' => array( 'patroller', 'autopatrolled' ),
	),
	'+mlwiktionary' => array(
		'bureaucrat' => array( 'botadmin' ),
	),
	'+nlwiki' => array(
		'bureaucrat' => array( 'abusefilter', 'arbcom', 'rollbacker' ),
	),
	'+nlwikibooks' => array(
		'sysop' => array( 'abusefilter' ),
	),
	'+nlwikivoyage' => array(
		'bureaucrat' => array( 'autopatrolled' ), // bug 44082
	),
	'+nnwiki' => array(
		'bureaucrat' => array( 'autopatrolled', 'patroller' ),
		'sysop' => array( 'autopatrolled' ),
	),
	'+noboard_chapterswikimedia' => array(
		'bureaucrat' => array( 'sysop', 'bureaucrat', 'import', 'transwiki' ),
	),
	'+nowiki' => array(
		'bureaucrat' => array( 'patroller', 'autopatrolled', 'transwiki', 'accountcreator' ),
		'sysop' => array( 'autopatrolled', 'abusefilter' ),
	),
	'+nowikibooks' => array(
		'bureaucrat' => array( 'bureaucrat', 'sysop', 'autopatrolled', 'patroller' ),
		'sysop' => array( 'autopatrolled', 'patroller' ),
	),
	'+officewiki' => array(
		'bureaucrat' => array( 'sysop', 'bureaucrat', 'import', 'transwiki', 'communityapps' ),
	),
	'+orwiki' => array(
		'sysop' => array( 'rollbacker' ),
	),
	'+otrs_wikiwiki' => array(
		'bureaucrat' => array( 'sysop', 'bureaucrat', 'import', 'transwiki' ),
	),
	'+outreachwiki' => array(
		'bureaucrat' => array( 'sysop', 'bureaucrat', 'autopatrolled', 'import' ),
	),
	'+pa_uswikimedia' => array(
		'bureaucrat' => array( 'sysop' ),
	),
	'+ptwiki' => array(
		'bureaucrat' => array( 'eliminator', 'confirmed', 'autoreviewer', 'interface_editor' ), // Bug 39905
		'sysop' => array( 'rollbacker', 'autoreviewer', 'confirmed' ),
	),
	'+ptwikinews' => array(
		'sysop' => array( 'reviewer' ),
	),
	'+quwiki' => array(
		'sysop' => array( 'rollbacker' ),
	),
	'+rowiki' => array( // bug 26634 --hashar 2011-01-14
		'bureaucrat' => array( 'abusefilter', 'patroller' ),
		'sysop' => array( 'autopatrolled' ),
	),
	'+ruwiki' => array(
		'bureaucrat' => array( 'sysop' ),
		'sysop' => array( 'rollbacker', 'autoeditor', 'uploader', 'closer', 'filemover', 'suppressredirect' ),
	),
	'+ruwikiquote' => array(
		'sysop' => array( 'autoeditor' )
	),
	'+ruwikisource' => array(
		'bureaucrat' => array( 'sysop', 'bureaucrat', 'autoeditor', 'rollbacker' ),
		'sysop' => array( 'autoeditor', 'rollbacker', 'abusefilter', 'flood' ),
	),
	'+sawiki' => array(
		'sysop' => array( 'autopatrolled' ),
	),
	'+sawikisource' => array(
		'sysop' => array( 'autopatrolled' ),
	),
	'+scowiki' => array(
		'sysop' => array( 'rollbacker', 'autopatrolled', )
	),
	'+sewikimedia' => array(
		'bureaucrat' => array( 'sysop', 'bureaucrat', 'medlem' ),
	),
	'+siwiki' => array(
		'sysop' => array( 'rollbacker', 'accountcreator', 'abusefilter', 'autopatrolled', 'confirmed', 'reviewer', ),
	),
	'+simplewiki' => array(
		'bureaucrat' => array( 'flood', 'rollbacker', 'sysop', 'import', 'transwiki', 'patroller' ),
		'sysop' => array( 'rollbacker', 'flood', 'patroller' ),
	),
	'+simplewiktionary' => array(
		'bureaucrat' => array( 'sysop' ),
		'sysop' => array( 'rollbacker', 'autopatrolled' ),
	),
	'+skwiki' => array(
		'sysop' => array( 'rollbacker' ),
	),
	'+stewardwiki' => array(
		'bureaucrat' => array( 'bureaucrat', 'sysop', 'import', 'transwiki', 'user', 'ipblock-exempt' ),
	),
	'+sqwiki' => array(
		'bureaucrat' => array( 'editor' ),
		'sysop' => array( 'reviewer' ),
	),
	'+srwiki' => array(
		'bureaucrat' => array( 'patroller', 'rollbacker', 'autopatrolled', 'flood' ),
		'sysop' => array( 'rollbacker', ),
	),
	'+srwikisource' => array(
		'bureaucrat' => array( 'patroller', 'autopatrolled' ),
	),
	'+strategywiki' => array(
		'bureaucrat' => array( 'flood' ),
	),
	'+svwiki' => array(
		'sysop' => array( 'rollbacker', ),
	),
	'+svwikisource' => array(
		'sysop' => array( 'autopatrolled', ),
	),
	'+tawiki' => array(
		'bureaucrat' => array( 'nocreate' ),
	),
	'+trwiki' => array(
		'sysop' => array( 'patroller' ), // bureaucrat -> sysop, Bug 38690
		'bureaucrat' => array( 'technician' ), // Bug 39690
	),
	'+ukwiki' => array(
		'sysop' => array( 'patroller', 'rollbacker' ),
	),
	'+ukwikimedia' => array(
		'bureaucrat' => array( 'sysop' ),
	),
	'+ukwiktionary' => array(
		'bureaucrat' => array( 'autoeditor' ),
		'sysop' => array( 'autoeditor' ),
	),
	'+urwiki' => array(
		'bureaucrat' => array( 'import', 'confirmed' ), // Bug 42737
		'sysop' => array( 'confirmed' ), // Bug 42737
	),
	'+viwiki' => array(
		'sysop' => array( 'rollbacker', 'flood' ),
		'bureaucrat' => array( 'flood' ),
	),
	'+vecwiki' => array(
		'sysop' => array( 'flood' ),
	),
	'+wikidatawiki' => array(
		'sysop' => array( 'autopatrolled' ), // bug 41907
		'bureaucrat' => array( 'translationadmin' ), // bug 44395
	),
	'+wikimaniateamwiki' => array(
		'bureaucrat' => array( 'sysop', 'bureaucrat', 'autopatrolled', 'import' ),
	),
	'+zhwiki' => array(
		'bureaucrat' => array( 'flood' ),
		'sysop' => array( 'patroller', 'rollbacker', 'autoreviewer', 'confirmed', 'flood' ),
	),
	'+zhwikinews' => array(
		'sysop' => array( 'rollbacker' ),
	),
	'+zh_yuewiki' => array(
		'sysop' => array( 'abusefilter', 'rollbacker', 'autoreviewer', 'confirmed' ),
	),
	'+plwiki' => array(
		'bureaucrat' => array( 'abusefilter', 'flood' ),
	),
	'+plwikiquote' => array(
	'sysop' => array( 'patroller' ), // http://bugzilla.wikimedia.org/show_bug.cgi?id=28479
	),
	'+private' => array(	// Cary	made me	do it! --Andrew	2009-05-01
		'bureaucrat' =>	array( 'inactive' ),
	),

),
# @} end of wgRemoveGroups

// Sorted by database names
# wgImportSources @{
'wgImportSources' => array(
	// Generic project entries

	// Note: When adding a specific wiki you have to duplicate these generic sources
	// otherwise they would be overwritten by the specific wiki entry
	'wikisource' => array( 'meta', 'commons' ),
	'wikiversity' => array( 'meta', 'b', 'incubator' ),

	// Specific wikis
	'alswiki'   => array( 'de', 'wikt', 'b', 'q', 'en', 'fr', 'it', 'b:de' ),
	'alswikibooks' => array( 'b:de', 'w' ),
	'arbcom_enwiki' => array( 'meta', 'w' ),
	'arbcom_dewiki' => array( 'w', 'w:de', ),
	'arwikibooks' => array( 'w' ),
	'arwikinews' => array( 'w' ),
	'arwikisource' => array( 'w' ),
	'arwikiversity' => array( 'w', 'b', 'n', 'q', 's', 'wikt' ),
	'arzwiki' => array( 'incubator', 'en', 'ar', 'es', 'fr', ),
	'betawikiversity' => array( 'meta', 'b', 'en', 'incubator', 'de', 'wikipedia:lt' ),
	'bewikimedia' => array( 'meta' ),
	'bnwiki' => array ( 'en' ), # Bug 34791
	'brwikimedia' => array( 'pt', 'meta', 'pt:w', 'pt:wikt', 'pt:s', 'pt:n', 'pt:q', 'pt:b', 'pt:v', 'commons' ),
	'bnwikisource' => array( 'OldWikisource', 'w' ),
	'cawikibooks' => array( 'w', 's', ),
	'cawikinews' => array( 'w', 'wikt', 'q', 'b', 's', 'v', 'fr', 'en', 'de', 'es', 'pt', 'it', 'commons', 'meta', 'eo' ),
	'cawikiquote' =>  array( 'ca' ),
	'cawikisource' => array( 'w', 'b', ),
	'cawiktionary' => array( 'w', ),
	'chywiki' => array( 'en', 'fr', ),
	'commonswiki' => array( 'meta', 'en', 'de', 'b:en' ),
	'cowikimedia' => array( 'meta', 'w:es', 'b:es', 'v:es', 'e:en' ),
	'crhwiki' => array( 'incubator' ),
	'crwiki' => array( 'en', 'fr'  ),
	'cswiki'	 => array( 'b', 'n', 'q', 's', 'v', 'wikt' ),
	'cswikibooks'    => array( 'n', 'q', 's', 'v', 'w', 'wikt' ),
	'cswikinews'     => array( 'b', 'q', 's', 'v', 'w', 'wikt', 'incubator' ),
	'cswikiquote'    => array( 'b', 'n', 's', 'v', 'w', 'wikt' ),
	'cswikisource'   => array( 'b', 'n', 'q', 'v', 'w', 'wikt', 'oldwikisource' ),
	'cswikiversity'  => array( 'b', 'n', 'q', 's', 'w', 'wikt', 'betawikiversity' ),
	'cswiktionary'   => array( 'b', 'n', 'q', 's', 'v', 'w' ),
	'donatewiki' => array( 'meta', 'foundation' ),
	'dewiki' => array( 'w', 'wikt', 'da', 'el', 'en', 'es', 'fr', 'hu', 'it', 'ja', 'nl', 'no', 'pl', 'pt', 'ru', 'sv', 'tr', 'uk', 'zh' ),
	'dewikibooks' => array( 'w', 'wikipedia', 'en', 'v' ),
	'dewikiquote' => array( 'w', 'wikt', 's', 'b', 'v', 'en', 'la', ),
	'dewikisource' => array( 'oldwikisource' ),
	'dewikiversity' => array( 'w', 'meta', 'b', 'incubator' ), # Bug 38159
	'dewiktionary' => array( 'w', 'en' ),
	'dewikivoyage' => array( 'w', 'en', 'meta' ),
	'dsbwiki' => array( 'incubator', ),
	'elwiki' => array( 'en', 'de', 'v', 's', 'wikt', 'b', 'q', 'fr', 'meta', 'commons', 'it',  ),
	'elwikibooks' => array( 'en', 'w:en', 'w', 'v', 'q', 's', 'meta', 'wikt' ),
	'elwikisource' => array( 'w', 'v' ),
	'elwikiversity' => array( 'w', 'wikt', 'b', 'q', 's', 'betawikiversity', 'en', 'fr', 'de', 'es', 'it', 'w:fr', 'w:de', 'fi', 'commons', 'w:en' ),
	'elwiktionary' => array( 'w', 'en', 'fr' ),
	'en_labswikimedia' => array( 'wikipedia' ),
	'enwiki' => array( 'meta', 'nost', 'de', 'es', 'fr', 'it', 'pl', 'outreachwiki' ),
	'enwiktionary' => array(
		'w', 'b', 's', 'q', 'v', 'n', 'commons', 'aa', 'ab', 'af', 'ak', 'als', 'am', 'an', 'ang', 'ar', 'as', 'ast', 'av', 'ay', 'az', 'ba', 'be', 'bg', 'bh', 'bi', 'bm', 'bn', 'bo', 'br', 'bs',
		'ca', 'ch', 'chr', 'co', 'cr', 'cs', 'csb', 'cy', 'da', 'de', 'dv', 'dz', 'el', 'eo', 'es', 'et', 'eu', 'fa', 'fi', 'fj', 'fo', 'fr', 'fy', 'ga', 'gd', 'gl', 'gn', 'gu', 'gv', 'ha', 'he',
		'hi', 'hr', 'hsb', 'hu', 'hy', 'ia', 'id', 'ie', 'ik', 'io', 'is', 'it', 'iu', 'ja', 'jbo', 'jv', 'ka', 'kk', 'kl', 'km', 'kn', 'ko', 'ks', 'ku', 'kw', 'ky', 'la', 'lb', 'li', 'ln', 'lo', 'lt', 'lv',
		'mg', 'mh', 'mi', 'mk', 'ml', 'mn', 'mo', 'mr', 'ms', 'mt', 'my', 'na', 'nah', 'nds', 'ne', 'nl', 'nn', 'no', 'oc', 'om', 'or', 'pa', 'pi', 'pl', 'ps', 'pt', 'qu', 'rm', 'rn', 'ro',
		'roa-rup', 'ru', 'rw', 'sa', 'sc', 'scn', 'sd', 'sg', 'sh', 'si', 'simple', 'sk', 'sl', 'sm', 'sn', 'so', 'sq', 'sr', 'ss', 'st', 'su', 'sv', 'sw', 'ta', 'te', 'tg', 'th', 'ti', 'tk', 'tl', 'tn', 'to',
		'tpi', 'tr', 'ts', 'tt', 'tw', 'ug', 'uk', 'ur', 'uz', 'vi', 'vo', 'wa', 'wo', 'xh', 'yi', 'yo', 'za', 'zh', 'zh-min-nan', 'zu'
	),
	'enwikisource' => array( 'w', 'OldWikisource', 'b', 'commons', 'q', ),
	'enwikibooks' => array( 'w', 's', 'q', 'v', 'wikt', 'n', 'meta', 'simple', 'species' ),
	'enwikiversity' => array( 'betawikiversity', 'w', 'b', 'q', 's' ),
	'eowikisource' => array( 'meta', 'commons', 'b:eo' ), // Bug 43659
	'eswiki' => array( 'b', 'q', 'v', 'wikt', 'n', 's', 'en' ),
	'eswikibooks' => array( 'w', 'w:en', 'en', 'meta' ),
	'eswikiversity' => array( 'w:es', 'b', 'meta' ),
	'eswiktionary' => array( 'w', 'w:en', 'w:fr', 'w:de', 'en', 'fr', 'de', ), # Bug 8202
	'etwiki' => array( 'b', 'q', 's', 'wikt' ),
	'etwikisource' => array( 'w' ),
	'etwiktionary' => array( 'w' ),
	'etwikimedia' => array( 'w', 'meta' ),
	'extwiki' => array( 'incubator' ),
	'fdcwiki' => array( 'meta' ),
	'fiwikisource' => array( 'OldWikisource', 'w', 'b' ),
	'fiwikiversity' => array( 'b', 'q', 'n', 's', 'wikt', 'w', 'betawikiversity' ),
	'fiwikimedia' => array( 'w', 'meta' ),
	'foundationwiki' => array( 'meta' ),
	'frrwiki' => array( 'en', 'de', 'nds', 's:de', 'oldwikisource' ), # s:de and oldwikisource per bug 38023
	'frwiki' => array(
		'meta', 'commons', 'species', 'q', 'af', 'ak', 'ar', 'ang', 'ast', 'gn', 'id', 'zhminnan', 'bg', 'br', 'ca', 'cs', 'co', 'da', 'de', 'et', 'el', 'en', 'es', 'eo', 'fa', 'fy', 'ga', 'gl', 'gu', 'hy',
		'hi', 'he', 'hsb', 'hr', 'io', 'ia', 'ie', 'is', 'it', 'kk', 'csb', 'sw', 'ko', 'ku', 'la', 'lt', 'li', 'lo', 'hu', 'ml', 'nl', 'ja', 'no', 'oc', 'nds', 'pl', 'pt', 'ro', 'ru', 'scn', 'simple', 'sk',
		'sl', 'sq', 'st', 'sr', 'fi', 'sv', 'ta', 'te', 'vi', 'th', 'tt', 'tr', 'uk', 'ur', 'vo', 'zh', 'w', 'n', 's', 'wikt', 'v',
	), # Bug 28024 and bug 22663 --pdhanda
	'frwikisource' => array(
		'meta', 'commons', 'species', 'q', 'b', 'af', 'ak', 'ar', 'ang', 'ast', 'gn', 'id', 'zhminnan', 'bg', 'br', 'ca', 'cs', 'co', 'da', 'de', 'et', 'el', 'en', 'es', 'eo', 'fa', 'fy', 'ga', 'gl', 'gu', 'hy', 'hi',
		'he', 'hsb', 'hr', 'io', 'ia', 'ie', 'is', 'it', 'kk', 'csb', 'sw', 'ko', 'ku', 'la', 'lt', 'li', 'lo', 'hu', 'ml', 'nl', 'ja', 'no', 'oc', 'nds', 'pl', 'pt', 'ro', 'ru', 'scn', 'simple', 'sk', 'sl', 'sq',
		'st', 'sr', 'fi', 'sv', 'ta', 'te', 'vi', 'th', 'tt', 'tr', 'uk', 'ur', 'vo', 'zh', 'w', 'n', 's', 'wikt', 'v',
	), # Bug 28024 and bug 22663 --pdhanda
	'frwikibooks' => array(
		'meta', 'commons', 'species', 'q', 'af', 'ak', 'ar', 'ang', 'ast', 'gn', 'id', 'zhminnan', 'bg', 'br', 'ca', 'cs', 'co', 'da', 'de', 'et', 'el', 'en', 'es', 'eo', 'fa', 'fy', 'ga', 'gl', 'gu',
		'hy', 'hi', 'he', 'hsb', 'hr', 'io', 'ia', 'ie', 'is', 'it', 'kk', 'csb', 'sw', 'ko', 'ku', 'la', 'lt', 'li', 'lo', 'hu', 'ml', 'nl', 'ja', 'no', 'oc', 'nds', 'pl', 'pt', 'ro', 'ru', 'scn', 'simple',
		'sk', 'sl', 'sq', 'st', 'sr', 'fi', 'sv', 'ta', 'te', 'vi', 'th', 'tt', 'tr', 'uk', 'ur', 'vo', 'zh', 'w', 'n', 's', 'wikt', 'v',
	),  # Bug 28024 and bug 22663 --pdhanda
	'frwikinews' => array( 'w', 'en', 'de', 'es', 'it', 'b', 'wikt', 'v', 's', 'q', 'ca', 'pt', 'species', 'commons', 'meta', 'w:pl', 'pl' ),
	'frwikiquote' => array( 'w', 'wikt', 's', 'b', 'n', 'v', 'meta', 'en' ),
	'frwikiversity' => array(
		'meta', 'commons', 'species', 'q', 'af', 'ak', 'ar', 'ang', 'ast', 'gn', 'id', 'zhminnan', 'bg', 'br', 'ca', 'cs', 'co', 'da', 'de', 'et', 'el', 'en', 'es', 'eo', 'fa', 'fy', 'ga', 'gl', 'gu', 'hy',
		'hi', 'he', 'hsb', 'hr', 'io', 'ia', 'ie', 'is', 'it', 'kk', 'csb', 'sw', 'ko', 'ku', 'la', 'lt', 'li', 'lo', 'hu', 'ml', 'nl', 'ja', 'no', 'oc', 'nds', 'pl', 'pt', 'ro', 'ru', 'scn', 'simple', 'sk',
		'sl', 'sq', 'st', 'sr', 'fi', 'sv', 'ta', 'te', 'vi', 'th', 'tt', 'tr', 'uk', 'ur', 'vo', 'zh', 'w', 'n', 's', 'wikt', 'b',
	),  # Bug 28024 and bug 22663 --pdhanda
	'frwiktionary' => array(
		'meta', 'commons', 'species', 'q', 'af', 'ak', 'ar', 'ang', 'ast', 'gn', 'id', 'zhminnan', 'bg', 'br', 'ca', 'cs', 'co', 'da', 'de', 'et', 'el', 'en', 'es', 'eo', 'fa', 'fy', 'ga', 'gl', 'gu', 'hy', 'hi',
		'he', 'hsb', 'hr', 'io', 'ia', 'ie', 'is', 'it', 'kk', 'csb', 'sw', 'ko', 'ku', 'la', 'lt', 'li', 'lo', 'hu', 'ml', 'nl', 'ja', 'no', 'oc', 'nds', 'pl', 'pt', 'ro', 'ru', 'scn', 'simple', 'sk', 'sl', 'sq',
		'st', 'sr', 'fi', 'sv', 'ta', 'te', 'vi', 'th', 'tt', 'tr', 'uk', 'ur', 'vo', 'zh', 'w', 'n', 's', 'b', 'v',
	),  # Bug 28024 and bug 22663 --pdhanda
	'ganwiki' => array( 'incubator' ),
	'gdwiki' => array( 'en', 'de', 'ga', 'gv' ),
	'glwiktionary' => array( 'w', 's', 'q', 'b' ),
	'glwikibooks' => array( 'w', 'wikt', 's', 'q' ),
	'glwikisource' => array( 'w', 'wikt', 'q', 'b' ),
	'glwikiquote' => array( 'w', 'wikt', 's', 'b' ),
	'glwiki' => array( 'wikt', 's', 'q', 'b' ),
	'guwiki' => array( 'en' ), // Bug 37511
	'hakwiki' => array( 'incubator' ),
	'hewiki' => array( 'wikt', 'q', 'b', 's', 'n', 'commons' ),
	'hewikibooks' => array( 'w', 'wikt', 'q', 's', 'n' ),
	'hewikinews' => array( 'w', 'wikt', 'q', 'b', 's' ),
	'hewikiquote' => array( 'w', 'wikt', 'b', 's', 'n' ),
	'hewiktionary' => array( 'w', 'q', 'b', 's', 'n' ),
	'hewikisource' => array( 'w', 'wikt', 'q', 'b', 'n' ),
	'hifwiki' => array( 'incubator' ),
	'hsbwiktionary' => array( 'incubator', 'w' ),
	'huwikinews' => array( 'incubator' ),
	'idwiki' => array( 'ms' ), # bug 16033
	'idwikibooks' => array( 'w' ),
	'idwikiquote' => array( 'w' ),
	'idwikisource' => array( 'w' ),
	'idwiktionary' => array( 'w' ),
	'incubatorwiki' => array( 'meta', 'commons', 'w', 'w:de', 'w:fr', 'w:es', 'w:pt', 'w:nl', 'w:ru', 'wikt:en', 'b:en',  ),
	'iswiki' => array( 'en', 'da', 'no', 'de' ),
	'iswiktionary' => array( 'w' ),
	'itwiki' => array( 'b', 'q', 's', 'wikt', 'n', 'v', 'de', 'es', 'en', 'fr', 'meta' ),
	'itwikibooks' => array( 'w', 'wikt', 's', 'q', 'n', 'v', 'en' ),
	'itwikinews' => array( 'w', 'wikt', 'b', 's', 'q', 'v', 'w:en', 'commons', 'en' ),
	'itwikiquote' => array( 'w', 'wikt', 's', 'b', 'n', 'v' ),
	'itwikisource' => array( 'w', 'b', 'wikt', 'q', 'n', 'v' ),
	'itwikiversity' => array( 'w', 'b', 'wikt', 's', 'q', 'n' ),
	'itwikivoyage' => array ( 'w', 'q', 's', 'n', 'wikt', 'b', 'v', 'species',
	                          'commons', 'd', 'mw', 'meta', 'foundation', 'w:ca',
	                          'w:de', 'w:en', 'w:es', 'w:fr', 'w:he', 'w:hu',
	                          'w:nl', 'w:pl', 'w:pt', 'w:ru', 's:fr', 'n:en',
	                          'wikt:en', 'de', 'en', 'es', 'fr', 'nl', 'ru', 'sv', ), // Bug 43310
	//TODO: add 'el' to itwikivoyage import sources (now in Incubator) when el.wikivoyage is live
	'itwiktionary' => array( 'w', 'b', 's', 'q', 'n', 'v', 'en', 'fr', 'sc' ),
	'jawikibooks' => array( 'w', 'wikt', 'q', 's', 'v', 'en' ),
	'jawikiversity' => array( 'betawikiversity', 'w', 'wikt', 'en', 'b', 'q', 's', 'n' ),
	'jawiktionary' => array( 'w' ),
	'kaawiki' => array( 'incubator' ),
	//'khmwiktionary' => array( 'en', 'fr', 'lm', 'th' ),
	'kmwiki' => array( 'en', 'be', 'simple' ),
	'knwiki' => array( 'en' ),
	'kowikiquote' => array( 'w' ),
	'lawikibooks' => array( 'w' ),
	'lawikisource' => array( 'ca', 'de', 'el', 'en', 'es', 'fr', 'it', 'pt', 'ro', 'w', 'b', 'oldwikisource' ),
	'lbwiktionary' => array( 'w:fr', 'fr', 'w:en', 'en', 'w', 'meta' ),
	'liwikinews' => array( 'incubator' ),
	'lmowiki' => array( 'ca', 'en', 'fr' ), # bug 8319
	'mdfwiki' => array( 'incubator' ),
	'mediawikiwiki' => array( 'meta', 'w:en', 'usability' ),
	'metawiki' => array( 'commons', 'foundation', 'w', 'cs', 'fr', 'strategy' ),
	'mgwiktionary' => array( 'fr', 'io', 'en', 'w', ),
	'mkwikimedia' => array( 'meta', 'w'  ),
	'mlwiki' => array( 'en' ),
	'mlwikisource' => array( 'w', 'en', 'w:en', ),
	'mlwikiquote' => array( 'en', 'w:en', 'w' ),
	'mswiki' => array( 'id' ), # bug 16033
	'mswiktionary' => array( 'w' ),
	'mxwikimedia' => array( 'meta', 'w:es', 'b:es', 'v:es', 'w:en', ),
	'myvwiki' => array( 'incubator' ),
	'ndswiki' => array( 'en', 'de', 'nl', 'nds-nl', 'wikt'  ),
	'nlwikibooks' => array( 'w' ),
	'nlwikisource' => array( 'w', 'wikt', 'q', 'b', 'n', 'nl', 'de', 'en' ),
	'nlwiktionary' => array( 'w', 'en', 'fr' ),
	'nowiki' => array( 's', 'q', 'nn', 'da', 'sv', 'nl', 'en', 'de' ),
	'nowikimedia' => array( 'meta', 'w', 'w:nn', 'w:se' ),
	'nowikibooks' => array( 'sv', 'da', 'en', 'w', 'w:nn', 'w:sv', 'w:da', 'w:en' ),
	'nowikinews' => array( 'sv' ),
	'nowikiquote' => array( 'w', 'en', 'nn' ),
	'nowikisource' => array( 'w' ),
	'nowiktionary' => array( 'w' ),
	'nycwikimedia' => array( 'm:w', 'meta' ),
	'orwiki' => array( 'en', 'commons' ),
	'otrs_wikiwiki' => array( 'meta' ),
	'outreachwiki'  => array( 'w:en', 'w:de', 'w:fr', 'w:pl', 'w:it', 'w:ja', 'w:es', 'w:nl', 'w:pt', 'w:ru', 'w:sv', 'commons', 'foundation', 'm', ),
	'pa_uswikimedia' => array( 'meta', 'en' ),
	'pflwiki' => array( 'de', 'fr', 'als', 'pdc', 'en' ),
	'ptwikisource' => array( 'w', 'b' ),
	'plwiki' => array( 's', 'b', 'q', 'n', 'wikt' ),
	'plwikibooks' => array( 'w', 's' ), # bug 8546
	'plwikisource' => array( 'w', 'b', 'q', 'n', 'wikt', 'oldwikisource' ),
	'plwiktionary' => array( 'w' ),
	'pntwiki'   => array( 'en', 'el', 'elwikiversity', 'incubator' ),
	'ptwikibooks' => array( 'w', 'wikt', 's', 'q', 'n', 'v', 'en', 'es', 'fr', 'it', 'de', 'ru', 'w:en' ),
	'ptwikinews' => array( 'w', 'wikt', 's', 'v', 'b', 'q', 'meta', 'commons', 'it', 'fr', 'ca', 'en', 'es', 'de', ),
	'ptwikiversity' => array( 'incubator', 'w', 'b', ),
	'rowikibooks' => array( 'w', 's' ),
	'ruwikibooks' => array( 'w', 's', ),
	'ruwikimedia' => array( 'meta', 'foundation', 'r', ),
	'ruwikiversity' => array( 'w', 'wikt', 'q', 'b', 's', 'n', 'betawikiversity' ),
	'sahwiki' => array( 'incubator' ),
	'sawiki' => array( 'en' ),
	'sawikisource' => array( 'oldwikisource' ),
	'sawiktionary' => array( 'en' ),
	'scnwiki' => array( 'it', 'en', 'fr' ),
	'scnwiktionary' => array( 'w', 'it', 'en', 'fr' ),
	'scowiki' => array( 'en', 'simple' ),
	'sewiki' => array( 'en', 'fi', 'no', 'nn', 'sv' ), // Bug 43411
	'sewikimedia' => array( 'w:sv' ),
	'simplewiki' => array( 'en', 'wikt', 'b', 'q' ),
	'simplewiktionary' => array( 'en', 'w', 'b', 'q' ),
	// 'simplewikibooks' => array( 'en', 'w', 'wikt', 'q'),
	'simplewikiquote' => array( 'en', 'w', 'wikt', 'b' ),

	'sourceswiki' => array(
		'ang', 'ar', 'az', 'bg', 'bn', 'bs', 'ca', 'cs', 'cy',
		'da', 'de', 'el', 'en', 'es', 'et', 'fa', 'fi', 'fo',
		'fr', 'gl', 'he', 'hr', 'ht', 'hu', 'hy', 'id', 'is',
		'it', 'ja', 'kn', 'ko', 'la', 'lt', 'mk', 'ml', 'nl',
		'no', 'pl', 'pt', 'ro', 'ru', 'sa', 'sk', 'sl', 'sr',
		'sv', 'ta', 'te', 'th', 'tr', 'uk', 'vi', 'yi', 'zh',
		'meta', 'commons',
	),
	'srnwiki' => array( 'incubator' ),
	'srwiki' => array( 'wikt' ),
	'srwiktionary' => array( 'w' ),
	'srwikinews' => array( 'no' ),
	'srwikisource' => array( 'w' ),
	'sswiki' => array( 've', 'st', 'zu', 'xh', 'af', 'en', 'es' ),
	'sswiktionary' => array( 'w', 'en', 'af' ),
	'stqwiki' => array( 'incubator' ),
	'svwikibooks' => array( 'w', 's', 'betawikiversity' ),
	'svwikisource' => array( 'w' ),
	'svwikiversity' => array( 'w', 'b', 's', 'n', 'en', 'betawikiversity' ),
	'svwiktionary' => array( 'w', 'meta' ),
	'szlwiki' => array( 'incubator' ),
	'tawikisource' => array( 'oldwikisource', 'w', 'b' ),
	'tewiki' => array( 'en', 'commons' ), // Bug 41880
	'testwiki' => array( 'de', 'en', 'es', 'fr', 'ja', 'commons', 'meta', 'incubator', 'strategy' ),
	'test2wiki' => array( 'en', 'cs' ),
	'tetwiki' => array( 'en', 'de', 'pt' ),
	'tewikisource' => array( 'w', 'b' ),
	'tpiwiki' => array( 'en', 'simple', 'wikt:en', 'commons' ),
	'trwikimedia' => array( 'w', 'meta' ),
	'ukwikimedia' => array( 'm' ),

	#  vi languages : bug 7854
	'vecwiki'      => array( 'it' ),
	'viwiki'       => array( 'wikt', 'b', 's', 'q' ),
	'viwiktionary' => array( 'w', 'b', 's', 'q' ),
	'viwikibooks'  => array( 'en', 'w', 'wikt', 's', 'q', 'fr', 'it' ), # fr and it: bug 37457
	'viwikisource' => array( 'w', 'wikt', 'b', 'q' ),
	'viwikiquote'  => array( 'w', 'wikt', 'b', 's' ),

	'wikidatawiki' => array( 'meta', 'commons', 'en', 'de', 'fr', 'es' ),
	'wikimania2012wiki' => array( 'en', 'meta', 'wm2011' ),
	'wikimania2013wiki' => array( 'en', 'meta', 'wm2011', 'wm2012' ),
	'wuuwiki' => array( 'en', 'th', 'fr', 'zh', ),

	'zhwikiquote' => array( 'w', 'b', 'wikt', 's', 'meta', 'commons' ),
	'zhwiktionary' => array( 'w', 'b', 'q', 's', 'meta', 'commons' ),
	'zhwikibooks' => array( 'w', 'wikt', 'q', 's', 'meta', 'commons' ),
	'zhwikisource' => array( 'w', 'b', 'q', 'wikt', 'meta', 'commons' ),
),
# @} end of wgImportSources

'wgImportTargetNamespace' => array(
	'default' => null,
	'enwiktionary' => 108,
	'enwikibooks' => 108,
	'frwiktionary' => 102,
	'frwikisource' => 100,
	'frwikibooks' => 100, // Transwiki
	'nlwikibooks' => 102,
	'frwikinews' => 102, // Transwiki
	'frwikiversity' => 110, // Transwiki
	'frwikiquote' => 108, // transwiki
	// Added zh wikis per bug 40474
	'zhwiktionary' => 102, // Transwiki
	'zhwikibooks' => 100, // Transwiki
	'zhwikiquote' => 100, // Transwiki
	'zhwikisource' => 108, // Transwiki
),

# SVG related @{
'wgSVGConverter' => array(
	'default' => 'rsvg-secure',
),
'wgSVGConverterPath' => array(
	'default' => '/usr/bin',
),
'wgSVGMaxSize' => array(
	'default' => 2048, // 1024's a bit low?
),
# @} end of SVG related

'wgFeedCacheTimeout' => array(
	'default' => 60,
	'enwiki' => 15,
),

'wgExportAllowHistory' => array( 'default' => true ),
'wgExportMaxHistory' => array(
	'default' => 1000, # changed from 100 -- brion 2008-07-10
),

'wgParserCacheExpireTime' => array(
	'default' => 86400 * 365,
),

# Captcha ...
# Cleaned up old commented out wikis. -- hashar 20110930
# @{
'wmgEnableCaptcha' => array(
	'default' => true,

	// private wikis don't need it
	'private' => false,
	'fishbowl' => false,
),

'wmgEmergencyCaptcha' => array(
	'default' => false,
	'ptwiki' => true, // 2008-01-25 by brion; reports of bot attack via cary
	'ptwikinews' => true, // 2011-12-08 by laner; reports of major vandalism by open proxies
),
# @} end of Captcha

'wgWantedPagesThreshold' => array(
	'default' => 2,
),

# Cleaned up old commented wikis -- hashar 20110830
# wgEnotifUserTalk @{
'wgEnotifUserTalk' => array(
	'default' => true,
),
# @} end of wgEnotifUserTalk

# #wgEnotifWatchlist @{
'wgEnotifWatchlist' => array(
	'default' => true,
),
# @} wgEnotifWatchlist

'wgEnotifMinorEdits' => array(
	'default' => false,
	'hiwiki' => true,
	'ptwikibooks' => true,
	'sourceswiki' => true,
),

'wgJobRunRate' => array(
	'default' => 0,
),

'wgMaxArticleSize' => array(
	# Increased from 1024 to 2000 to alleviate problems with template expansion
	# limits in AfD archives -- TS
	'default' => 2000,
),

'wgUseAjax' => array(
	'default' => false,
),

'wgEnableSidebarCache' => array(
	'default' => true,
),

'wgAllowTitlesInSVG' => array(
	'default' => true,
),

'wgIgnoreImageErrors' => array(
	'default' => true, // keeps temporary errors from messing the cached output
),

'wgThumbnailScriptPath' => array(
	'default' => false,
	'private' => '/w/thumb.php',
),

'wgGenerateThumbnailOnParse' => array(
	'default' => false,
),

'wgUnwatchedPageThreshold' => array(
	'default' => 30, // Default value of https://toolserver.org/~mzmcbride/watcher/
),

# ROBOT @{
'wgNamespaceRobotPolicies' => array(
	'dewiki' => array(
		NS_TALK => 'noindex,follow',
		NS_USER => 'noindex,follow', // Bug 36181
		NS_USER_TALK => 'noindex,follow',
		NS_PROJECT_TALK => 'noindex,follow',
		NS_IMAGE_TALK => 'noindex,follow',
		NS_MEDIAWIKI_TALK => 'noindex,follow',
		NS_TEMPLATE_TALK => 'noindex,follow',
		NS_HELP_TALK => 'noindex,follow',
		NS_CATEGORY_TALK => 'noindex,follow',
	),
	'dawiki' => array(
		NS_USER => 'noindex,follow',
		NS_USER_TALK => 'noindex,follow',
		NS_SPECIAL => 'noindex,follow',
	),
	'enwiki' => array(
		NS_USER_TALK => 'noindex,follow',
	),
	'frwiki' => array(
		NS_USER      => 'noindex,follow',
		NS_USER_TALK => 'noindex,follow',
	),
	'hewiki' => array(
		# https://bugzilla.wikimedia.org/show_bug.cgi?id=16247
		NS_USER => 'noindex,follow',
	),
	'ruwiki' => array(
		102 => 'noindex,follow',
		103 => 'noindex,follow',
		104 => 'noindex,follow',
		105 => 'noindex,follow',
		106 => 'noindex,follow',
		107 => 'noindex,follow'
	),
	'thwiki' => array(
		NS_TALK => 'noindex,follow',
		NS_USER => 'noindex,follow',
		NS_USER_TALK => 'noindex,follow',
		NS_PROJECT_TALK => 'noindex,follow',
		NS_FILE => 'noindex,follow',
		NS_FILE_TALK => 'noindex,follow',
		NS_MEDIAWIKI_TALK => 'noindex,follow',
		NS_TEMPLATE_TALK => 'noindex,follow',
		NS_HELP_TALK => 'noindex,follow',
		NS_CATEGORY_TALK => 'noindex,follow',
		100 => 'noindex,follow',
		101 => 'noindex,follow',
	),
	'thwikibooks' => array(
		NS_TALK => 'noindex,follow',
		NS_USER => 'noindex,follow',
		NS_USER_TALK => 'noindex,follow',
		NS_PROJECT_TALK => 'noindex,follow',
		NS_FILE => 'noindex,follow',
		NS_FILE_TALK => 'noindex,follow',
		NS_MEDIAWIKI_TALK => 'noindex,follow',
		NS_TEMPLATE_TALK => 'noindex,follow',
		NS_HELP_TALK => 'noindex,follow',
		NS_CATEGORY_TALK => 'noindex,follow',
	),
	'thwiktionary' => array(
		NS_TALK => 'noindex,follow',
		NS_USER => 'noindex,follow',
		NS_USER_TALK => 'noindex,follow',
		NS_PROJECT_TALK => 'noindex,follow',
		NS_FILE => 'noindex,follow',
		NS_FILE_TALK => 'noindex,follow',
		NS_MEDIAWIKI_TALK => 'noindex,follow',
		NS_TEMPLATE_TALK => 'noindex,follow',
		NS_HELP_TALK => 'noindex,follow',
		NS_CATEGORY_TALK => 'noindex,follow',
	),
	'thwikiquote' => array(
		NS_TALK => 'noindex,follow',
		NS_USER => 'noindex,follow',
		NS_USER_TALK => 'noindex,follow',
		NS_PROJECT_TALK => 'noindex,follow',
		NS_FILE => 'noindex,follow',
		NS_FILE_TALK => 'noindex,follow',
		NS_MEDIAWIKI_TALK => 'noindex,follow',
		NS_TEMPLATE_TALK => 'noindex,follow',
		NS_HELP_TALK => 'noindex,follow',
		NS_CATEGORY_TALK => 'noindex,follow',
	),
	'thwikisource' => array(
		NS_TALK => 'noindex,follow',
		NS_USER => 'noindex,follow',
		NS_USER_TALK => 'noindex,follow',
		NS_PROJECT_TALK => 'noindex,follow',
		NS_FILE => 'noindex,follow',
		NS_FILE_TALK => 'noindex,follow',
		NS_MEDIAWIKI_TALK => 'noindex,follow',
		NS_TEMPLATE_TALK => 'noindex,follow',
		NS_HELP_TALK => 'noindex,follow',
		NS_CATEGORY_TALK => 'noindex,follow',
	),
),

'wgArticleRobotPolicies' => array(
	'testwiki' => array(
		'Not indexed' => 'noindex,follow',
	),
	'enwiki' => array(
		# IRC request from amidaniel, 2007-06-01 TS
		'Wikipedia:Requests for arbitration/Badlydrawnjeff/Evidence' => 'noindex,follow',
		'Wikipedia:Requests for arbitration/Badlydrawnjeff/Workshop' => 'noindex,follow',
	),
	'rowiki' => array(
		'Wikipedia:Pagini de şters' => 'noindex,follow',
		'Discuţie Wikipedia:Pagini de şters' => 'noindex,follow',
	),
),
# @} end of ROBOT

'wgLoginLanguageSelector' => array(
	'default' => true,

	'enwiki' => false,

	# same as default as of 20110119 -- hashar
	'metawiki' => true,
	'commonswiki' => true,
	'mediawikiwiki' => true,
	'sourceswiki' => true,
	'specieswiki' => true,
	'incubatorwiki' => true,
),

'wgSpamRegex' => array(
	# added dvd thingy against many spamming idiots 2007-05-02 river
	# Removed, 2007-05-15 -- TS
	'default' => array(
	   '/overflow\s*:\s*auto\s*;\s*height\s*:|<div[^>]*font-size[^>]*font-color:\s*transparent[^>]*>/i',
	   '/avril\.on\.nimp\.org/i', // http://en.wikipedia.org/wiki/Special:Contributions/Hochitup
	   '/\.on\.nimp\.org/i', // per MrZ-man 2008-11-02 -- brion
	   // bug 15063, these won't last:
	   '/<TR>(<TD BGCOLOR=["\']?#......["\']?>(\.|We|are|Anonymous|)+<\/TD>){20,}<\/TR>/i',
	   '/<table>.+?(<td bgcolor.+?){400,}.+?<\/table>/i',
	   // Weird thingy http://en.wikipedia.org/w/index.php?title=Hellboy:_Sword_of_Storms&oldid=245477898&diff=prev
	   '/<span onmouseover="_tipon/',
	   // Reported on id.wikipedia.org 2008-12-17 Tim
	   '/FIELD_MESSAGE_/',
	   // Plaintext link spam https://bugzilla.wikimedia.org/show_bug.cgi?id=16597
	   '/[wｗ]{3}[\.．][aＡａ][nｎ][oｏ]ｎ[tＴ][aａ][lｌ][kｋ][\.．][cｃ][oｏ][mｍ]/ui',
	),
	# Multiple requests on IRC -- TS 2006-07-11
	'commonswiki' => '/overflow\s*:\s*auto\s*;\s*height\s*:|kryptonazi|freizeit-diktator/i',
	# yoniDeBest on IRC -- TS 2007-04-16
	# removed upon yoni's request 'hewiki' => '/kazach/i',
),


# DJVU @{
'wgDjvuDump' => array(
	'default' => '/usr/bin/djvudump',
),

'wgDjvuRenderer' => array(
	'default' => '/usr/bin/ddjvu',
),

'wgDjvuTxt' => array(
	'default' => '/usr/bin/djvutxt',
),
# @}

'wgRestrictDisplayTitle' => array(
	'default' => true,
	'donatewiki' => false,
	'aswiki' => false,
	'bewikimedia' => false,
	'bnwiki' => false,
	'bnwiktionary' => false,
	'bnwikisource' => false,
	'bnwikibooks' => false,
	'bdwikimedia' => false,
	'bpywiki' => false,
	'enwikibooks' => false,
	'rmwiki' => false,
	'wikimania2009wiki' => false,
	'wikimania2010wiki' => false,
	'wikimania2011wiki' => false,
	'wikimania2012wiki' => false,
	'wikimania2013wiki' => false,
	'zhwiki'  => false,
	'foundationwiki' => false,
),

'wmgPrivateWikiUploads' => array(
	'default' => false,
	'donatewiki' => true, // whee restricted site
	'private' => true,
	'foundationwiki' => true, // whee restricted site
),

# CATEGORY TREE EXTENSION @{
'wgCategoryTreeDynamicTag' => array(
	'default' => true,
),

// CT_MODE_CATEGORIES		 0
// CT_MODE_PAGES		10
// CT_MODE_ALL			20
'wgCategoryTreeCategoryPageMode' => array(
	'default' => 0,
	'hewiki' => 10,
),

'wgCategoryTreeCategoryPageOptions' => array(
	'hewiki' => array( 'mode' => 10 /*CT_MODE_PAGES*/ ), # bug 11776
),

# @} end of CATEGORY TREE EXTENSION

'wgContentNamespaces' => array(
	'default' => array( NS_MAIN ),
	'+arwiki' => array( 104 ), // bug 20623
	'+arwikisource' => array( 102, 104 ),
	'+aswikisource' => array( 104, 106 ), // Bug 43129
	'+bgwikisource' => array( 100 ),
	'+bnwikisource' => array( 100 ),
	'+brwikisource' => array( 100, 102, 104 ),
	'+cawikisource' => array( 102, 104, 106 ),
	'+cswikisource' => array( 100 ),
	'+dawikisource' => array( 102, 104, 106 ),
	'+dewikisource' => array( 102, 104 ),
	'+elwikisource' => array( 100, 102 ),
	'+enwikibooks' => array( 102, 110 ),
	'+enwikisource' => array( 102, 104, 106 ),
	'+eswiki' => array( 104 ), // Bug 39866
	'+eswikisource' => array( 102, 104 ),
	'+etwikisource' => array( 102, 104, 106 ),
	'+fawikisource' => array( 102, 104 ),
	'+frrwiki' => array( 102, 104, 106 ), // bug 38023
	'+frwikisource' => array( 102, 104, 112 ),
	'+hewikisource' => array( 104, 108, 112 ),
	'+hrwiki' => array( 102 ), // Bug 40732
	'+hrwikisource' => array( 100, 102, 104 ),
	'+huwikisource' => array( 100, 104, 106 ),
	'+hywikisource' => array( 100, 104, 106 ),
	'+idwikibooks' => array( 100, 102 ), // Bug 2282
	'+idwikisource' => array( 100, 102, 104 ),
	'+itwikisource' => array( 102, 108, 110 ),
	'+kowikisource' => array( 100 ),
	'+lawikisource' => array( 102, 104, 106 ),
	'+mediawikiwiki' => array( 100, 102 ), // Manuals & extensions
	'+metawiki' => array( NS_HELP ), // Bug 43687
	'+mlwikisource' => array( 100, 104, 106 ),
	'+nlwikisource' => array( 102 ),
	'+nowikisource' => array( 102, 104, 106 ),
	'+plwikisource' => array( 100, 102, 104 ),
	'+ptwiki' => array( 102 ),
	'+ptwikisource' => array( 102, 104, 106 ),
	'+rowikisource' => array( 102, 104, 106 ), // Follow-up for bug 29190
	'+ruwikisource' => array( 104, 106 ),
	'+slwikisource' => array( 100, 104 ),
	'+sourceswiki' => array( 104, 106 ),
	'+srwikibooks' => array( 102, ), // Bug 15282
	'+srwikisource' => array( 100 ),
	'+svwikisource' => array( 104, 106, 108 ),
	'+tewikisource' => array( 102, 104, 106 ),
	'+trwikibooks' => array( 100, 110, ),
	'+trwikisource' => array( 100 ),
	'+vecwikisource' => array( 100, 102, 104 ),
	'+viwikibooks' => array( 104, 106 ),
	'+viwikisource' => array( 102, 104, 106 ),
	'+zhwikisource' => array( 102, 104, 106 ),
	'+dewikivoyage' => array( 104 ),
	'+itwikivoyage' => array( 104 ),
),

'wgRevisionCacheExpiry' => array(
	'default' => 86400 * 7,  // set back to 0 if this breaks
	'testwiki' => 3600,
),

'wgShowExceptionDetails' => array(
	'default' => false,
	'testwiki' => true,
	'test2wiki' => true,
),


# below seems to be for extensions

# EXTENSIONS @{
'wmgUseProofreadPage' => array(
	'default' => false,
	'wikisource' => true,
	'sourceswiki' => true, // FIXME: Why isn't this part of wikisource?
	'test2wiki' => true,
	'frrwiki' => true, // bug 38023
),

'wmgUseDPL' => array(
	// controls loading of DynamicPageList extension
	'default' => false,
	'bswiki' => true, # 8240
	'dewikisource' => true, # 8563
	'dewiktionary' => true,
	'enwikisource' => true, # 8563
	'enwiktionary' => true,
	'eswiktionary' => true, # 7952
	'frrwiki' => true, # 38023
	'hewikisource' => true, # 8563
	'incubatorwiki' => true,
	'iswiktionary' => true, # 7952
	'metawiki' => true,
	'ptwiki' => true, # bug 35308
	'otrs_wikiwiki' => true,
	'srwiki' => true,
	'strategywiki' => true,
	'viwiktionary' => true, # bug 8886
	'wikibooks' => true,
	'wikimania2009wiki' => true,
	'wikimania2010wiki' => true,
	'wikimania2011wiki' => true,
	'wikimania2012wiki' => true,
	'wikimania2013wiki' => true,
	'wikinews' => true,
	'wikiquote' => true,
	'wikisource' => true,  # 12423
	'wikiversity' => true,
),

'wmgUseSpecialNuke' => array(
	'default' => true,
),

'wmgUseLabeledSectionTransclusion' => array(
	// controls loading of LabeledSectionTransclusion extension
	'default' => false,
	'wikisource' => true,
	// ---------------
	'cswiktionary' => true, // 22139
	'dewikiversity' => true, // Per Bug 17773
	'enwiktionary' => true,
	'frrwiki' => true, // Bug 38023
	'frwiki' => true,
	'itwikibooks' => true, // Bug 38287
	'mediawikiwiki' => true, // requested by guillom
	'metawiki' => true,
	'outreachwiki' => true, // bug 29238
	'sourceswiki' => true,
	'testwiki' => false,
	'test2wiki' => true,
),

'wmgUseQuiz' => array(
	// controls loading of Quiz extension
	'default' => false,
	'wikinews' => true, // various wanting to play with it 2007-09
	'wikiversity' => true,
	// -------------- normal wikis ----------
	'dewikibooks' => true,
	'enwikibooks' => true,
	'enwikinews' => true,
	'frwiktionary' => true,
	'iswikibooks' => true,
	'itwikibooks' => true,
	'nlwikibooks' => true,
	'plwikibooks' => true,
	'ptwikibooks' => true,
	'hiwiki' => true,
	'svwiki' => true,
	'viwikibooks' => true, // Bug 38361
	'zhwiki' => true,
),

'wmgUseGadgets' => array(
	'default' => true, // set 2007-12-17 by brion
),

# @} end of EXTENSIONS

# wgVariantArticlePath @{
'wgVariantArticlePath' => array(
	'srwiki' => '/$2/$1',
	'srwiktionary' => '/$2/$1',
	'srwikibooks' => '/$2/$1',
	'srwikiquote' => '/$2/$1',
	//'srwikiversity' => '/$2/$1',
	'srwikinews' => '/$2/$1',
	'srwikisource' => '/$2/$1',
	'zhwiki' => '/$2/$1',
	'zhwiktionary' => '/$2/$1',
	'zhwikisource' => '/$2/$1',
	'zhwikibooks' => '/$2/$1',
	'zhwikiquote' => '/$2/$1',
	'zhwikinews' => '/$2/$1',
	//'zhwikiversity' => '/$2/$1',
),
# @} end of wgVariantArticlePath

# Disable all the query pages that take more than about 15 minutes to update
# wgDisableQueryPageUpdate @{
'wgDisableQueryPageUpdate' => array(
	'+enwiki' => array(
		'Lonelypages',
		'Mostcategories',
		'Mostlinkedcategories',
		'Mostlinkedtemplates',
		'Uncategorizedcategories',
		'Wantedtemplates',
	),
	'default' => array(
		'Ancientpages',
		'Deadendpages',
		'Fewestrevisions',
		'Mostlinked',
		'Mostrevisions',
		'Wantedpages',
	),
	'small' => array(),
	'+frwiki' => array(
		'Mostcategories',
	),
	'+wikidatawiki' => array(
		'Lonelypages ',
		'Uncategorizedpages',
		'Unwatchedpages',
		'Withoutinterwiki',
	),
),
# @} end of wgDisableQueryPageUpdate

'wgShowHostnames' => array(
	'default' => true,
),

# wgNamespaceProtection @{
'wgNamespaceProtection' => array(
	'default' => array(
		NS_MEDIAWIKI => array( 'editinterface' ),
	),
	'+cswiki' => array(
		NS_FILE      => array( 'editinterface' ),
		NS_FILE_TALK => array( 'editinterface' ),
	),
	'+cswikibooks' => array(
		NS_FILE      => array( 'editinterface' ),
		NS_FILE_TALK => array( 'editinterface' ),
	),
	'+cswikinews' => array(
		NS_FILE      => array( 'editinterface' ),
		NS_FILE_TALK => array( 'editinterface' ),
	),
	'+cswikiquote' => array(
		NS_FILE      => array( 'editinterface' ),
		NS_FILE_TALK => array( 'editinterface' ),
	),
	'+cswikisource' => array(
		NS_FILE      => array( 'editinterface' ),
		NS_FILE_TALK => array( 'editinterface' ),
	),
	'+cswikiversity' => array(
		NS_FILE      => array( 'editinterface' ),
		NS_FILE_TALK => array( 'editinterface' ),
	),
	'+cswiktionary' => array(
		NS_FILE      => array( 'editinterface' ),
		NS_FILE_TALK => array( 'editinterface' ),
	),
	'+eswiki' => array(
		NS_IMAGE => array( 'editinterface' ),
		NS_IMAGE_TALK => array( 'editinterface' ),
	),
	'+ptwiki' => array(
		NS_IMAGE => array( 'autoconfirmed' ),
	),
	'ruwiki' => array(
		106 => array( 'autoconfirmed' ),
	),
	'sewikimedia' => array(
		NS_MAIN => array( 'editallpages' ),
		NS_TALK => array( 'editallpages' ),
		NS_USER => array( 'editallpages' ),
		NS_USER_TALK => array( 'editallpages' ),
		NS_PROJECT => array( 'editallpages' ),
		NS_PROJECT_TALK => array( 'editallpages' ),
		NS_IMAGE => array( 'editallpages' ),
		NS_IMAGE_TALK => array( 'editallpages' ),
		NS_MEDIAWIKI => array( 'editallpages' ),
		NS_MEDIAWIKI_TALK => array( 'editallpages' ),
		NS_TEMPLATE => array( 'editallpages' ),
		NS_TEMPLATE_TALK => array( 'editallpages' ),
		NS_HELP => array( 'editallpages' ),
		NS_HELP_TALK => array( 'editallpages' ),
		NS_CATEGORY => array( 'editallpages' ),
		NS_CATEGORY_TALK => array( 'editallpages' ),
		100 => array( 'editallpages' ),
		101 => array( 'editallpages' ),
	),
),
# @} end of wgNamespaceProtection

'wgParserConf' => array(
	'default' => array( 'class' => 'Parser', 'preprocessorClass' => 'Preprocessor_DOM' ),
	// Workaround for the issue discussed at https://oc.wikipedia.org/wiki/Discussion_Utilizaire:Midom -- TS
	'ocwiki' => array( 'class' => 'Parser', 'preprocessorClass' => 'Preprocessor_Hash' ),
),

# wgFavicon @{
'wgFavicon' => array(
	#Wiktionary
	'arwiktionary'		=> '//bits.wikimedia.org/favicon/piece.ico',
	'cowiktionary'		=> '//bits.wikimedia.org/favicon/piece.ico',
	'dewiktionary'		=> '//bits.wikimedia.org/favicon/piece.ico',
	'elwiktionary'		=> '//bits.wikimedia.org/favicon/piece.ico',
	'eswiktionary'		=> '//bits.wikimedia.org/favicon/piece.ico', // Bug 40186
	'etwiktionary'		=> '//bits.wikimedia.org/favicon/piece.ico',
	'fawiktionary'		=> '//bits.wikimedia.org/favicon/piece.ico',
	'fiwiktionary'		=> '//bits.wikimedia.org/favicon/piece.ico',
	'frwiktionary'		=> '//bits.wikimedia.org/favicon/piece.ico',
	'itwiktionary'		=> '//bits.wikimedia.org/favicon/piece.ico',
	'kkwiktionary'		=> '//bits.wikimedia.org/favicon/piece.ico',
	'kmwiktionary'		=> '//bits.wikimedia.org/favicon/piece.ico',
	'kowiktionary'		=> '//bits.wikimedia.org/favicon/piece.ico',
	'liwiktionary'		=> '//bits.wikimedia.org/favicon/piece.ico',
	'ltwiktionary'		=> '//bits.wikimedia.org/favicon/piece.ico',
	'mswiktionary'		=> '//bits.wikimedia.org/favicon/piece.ico',
	'ndswiktionary'		=> '//bits.wikimedia.org/favicon/piece.ico',
	'nlwiktionary'		=> '//bits.wikimedia.org/favicon/piece.ico',
	'ocwiktionary'		=> '//bits.wikimedia.org/favicon/piece.ico',
	'plwiktionary'		=> '//bits.wikimedia.org/favicon/piece.ico',
	'pnbwiktionary'		=> '//bits.wikimedia.org/favicon/piece.ico',
	'ptwiktionary'		=> '//bits.wikimedia.org/favicon/piece.ico',
	'scnwiktionary'		=> '//bits.wikimedia.org/favicon/piece.ico',
	'simplewiktionary'		=> '//bits.wikimedia.org/favicon/piece.ico',
	'siwiktionary'		=> '//bits.wikimedia.org/favicon/wiktionary/si.ico', // Bug 38036
	'sqwiktionary'		=> '//bits.wikimedia.org/favicon/piece.ico',
	'svwiktionary'		=> '//bits.wikimedia.org/favicon/piece.ico',
	'thwiktionary'		=> '//bits.wikimedia.org/favicon/piece.ico',
	'trwiktionary'		=> '//bits.wikimedia.org/favicon/piece.ico',
	'ukwiktionary'		=> '//bits.wikimedia.org/favicon/piece.ico',
	'viwiktionary'		=> '//bits.wikimedia.org/favicon/piece.ico',
	'wowiktionary'		=> '//bits.wikimedia.org/favicon/piece.ico',
	'yiwiktionary'		=> '//bits.wikimedia.org/favicon/piece.ico',
	'zhwiktionary'		=> '//bits.wikimedia.org/favicon/piece.ico',
	#Closed Wiktionaries
	'aawiktionary'		=> '//bits.wikimedia.org/favicon/piece.ico',
	'abwiktionary'		=> '//bits.wikimedia.org/favicon/piece.ico',
	'akwiktionary'		=> '//bits.wikimedia.org/favicon/piece.ico',
	'alswiktionary'		=> '//bits.wikimedia.org/favicon/piece.ico',
	'aswiktionary'		=> '//bits.wikimedia.org/favicon/piece.ico',
	'avwiktionary'		=> '//bits.wikimedia.org/favicon/piece.ico',
	'bawiktionary'		=> '//bits.wikimedia.org/favicon/piece.ico',
	'bhwiktionary'		=> '//bits.wikimedia.org/favicon/piece.ico',
	'biwiktionary'		=> '//bits.wikimedia.org/favicon/piece.ico',
	'bmwiktionary'		=> '//bits.wikimedia.org/favicon/piece.ico',
	'bowiktionary'		=> '//bits.wikimedia.org/favicon/piece.ico',
	'chwiktionary'		=> '//bits.wikimedia.org/favicon/piece.ico',
	'crwiktionary'		=> '//bits.wikimedia.org/favicon/piece.ico',
	'dzwiktionary'		=> '//bits.wikimedia.org/favicon/piece.ico',
	'mhwiktionary'		=> '//bits.wikimedia.org/favicon/piece.ico',
	'mowiktionary'		=> '//bits.wikimedia.org/favicon/piece.ico',
	'piwiktionary'		=> '//bits.wikimedia.org/favicon/piece.ico',
	'rmwiktionary'		=> '//bits.wikimedia.org/favicon/piece.ico',
	'rnwiktionary'		=> '//bits.wikimedia.org/favicon/piece.ico',
	'scwiktionary'		=> '//bits.wikimedia.org/favicon/piece.ico',
	'snwiktionary'		=> '//bits.wikimedia.org/favicon/piece.ico',
	'tlhwiktionary'		=> '//bits.wikimedia.org/favicon/piece.ico',
	'tokiponawiktionary'		=> '//bits.wikimedia.org/favicon/piece.ico',
	'towiktionary'		=> '//bits.wikimedia.org/favicon/piece.ico',
	'twwiktionary'		=> '//bits.wikimedia.org/favicon/piece.ico',
	'xhwiktionary'		=> '//bits.wikimedia.org/favicon/piece.ico',
	'yowiktionary'		=> '//bits.wikimedia.org/favicon/piece.ico',
	'zawiktionary'		=> '//bits.wikimedia.org/favicon/piece.ico',
	#Other Wiktionaries
	'amwiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'aywiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'azwiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'bewiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'bnwiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'bswiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'chrwiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'csbwiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'dawiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'dvwiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'eowiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'fiwiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'fjwiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'fowiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'fywiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'gdwiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'gnwiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'guwiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'gvwiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'hawiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'hiwiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'hrwiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'hsbwiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'hywiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'iawiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'iewiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'ikwiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'iuwiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'jawiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'jjvwiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'klwiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'kswiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'kwwiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'kywiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'lnwiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'lowiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'lvwiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'miwiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'mkwiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'mlwiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'mnwiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'mrwiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'mtwiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'mywiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'nahwiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'nawiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'newiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'nnwiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'omwiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'pawiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'pswiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'roa_rupwiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'rwwiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'sawiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'sdwiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'skwiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'slwiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'smwiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'sowiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'sswiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'stwiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'suwiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'swwiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'tewiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'tgwiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'tiwiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'tkwiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'tlwiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'tnwiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'ttswiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'ugwiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'urwiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'uzwiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'wawiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',
	'zh_min_nzuwiktionary' 		=> '//bits.wikimedia.org/favicon/piece.ico',

	#Other
	'testwiki'	 	=> '//bits.wikimedia.org/favicon/black-globe.ico',
	'wikimania2005wiki' 	=> '//bits.wikimedia.org/favicon/wikimania.ico',
	'wikimania2006wiki' 	=> '//bits.wikimedia.org/favicon/wikimania.ico',
	'wikimania2007wiki' 	=> '//bits.wikimedia.org/favicon/wikimania.ico',
	'wikimania2008wiki' 	=> '//bits.wikimedia.org/favicon/wikimania.ico',
	'wikimania2009wiki' 	=> '//bits.wikimedia.org/favicon/wikimania.ico',
	'wikimania2010wiki' 	=> '//bits.wikimedia.org/favicon/wikimania.ico',
	'wikimania2011wiki' 	=> '//bits.wikimedia.org/favicon/wikimania.ico',
	'wikimania2012wiki' 	=> '//bits.wikimedia.org/favicon/wikimania.ico',
	'wikimania2013wiki'     => '//bits.wikimedia.org/favicon/wikimania.ico',
	'wikimaniateamwiki' 	=> '//bits.wikimedia.org/favicon/wikimania.ico',
	# nl and ar, co, fa, it, ko, lt, scn, sv, tr, uk, vi, yi, simple and fi

),
# @} end of wgFavicon

# wmgAutopromoteExtraGroups @{
'wmgAutopromoteExtraGroups' => array(
	'default' => false,
	'testwiki' => array(
		'patroller' => array( '&',
				array( APCOND_EDITCOUNT, 10 ),
				array( APCOND_AGE, 100 * 86400 ), // 100 days * seconds in a day
		),
	),
	'enwiki' => array(
		'accountcreator' => array( '&',
			array( APCOND_ISIP, '167.165.53.93' ),
		 ),
	),
	'fiwiki' => array(
		'patroller' => array( '&',
				array( APCOND_EDITCOUNT, 1000 ),
				array( APCOND_AGE, 100 * 86400 ),
		),
	),
	'frwiki' => array(
	   'autopatrolled' => array( '&',
				array( APCOND_EDITCOUNT, 500 ),
				array( APCOND_AGE, 90 * 86400 ),
	   ),
	),
	'ruwikiversity' => array(
		'patroller' => array( '&',
			array( APCOND_EDITCOUNT, 1000 ),
		),
	),
),
# @} end of wmgAutopromoteExtraGroups

'wmgAutopromoteOnceonEdit' => array(
	'default' => array(),
),

'wmgAutopromoteOnceonView' => array(
	'default' => array(),
	'ruwiki' => array(
		'uploader' => array( '&',
			array( APCOND_EDITCOUNT, 20 ),
			array( APCOND_AGE, 14 * 86400 ),
		),
	),
),

'wgAutopromoteOnceLogInRC' => array(
	'default' => true,
	'ruwiki' => false,
),

'wmgExtraImplicitGroups' => array(
	'default' => false,
	'frwiki'  => array( 'autopatrolled', ),
),

'wgDeleteRevisionsLimit' => array(
	'default' => 5000,
),

'wgAppleTouchIcon' => array(
	'default' => false, // default iPhone/iPod Touch bookmark icon

	// Let the secure versions shine through with their own icons as well, set it explicitly
	'wiki' => '//$lang.$site.org/apple-touch-icon.png',
	'wiktionary' => '//$lang.$site.org/apple-touch-icon.png', // http://upload.wikimedia.org/wiktionary/en/4/48/Wiktionary-iphone_logo.png

	'testwiki' => '//upload.wikimedia.org/wikipedia/test/apple-touch-icon.png',
	'enwikinews' => '//upload.wikimedia.org/wikipedia/commons/4/43/Apple-touch-icon.png',
	'mediawikiwiki' => '//www.mediawiki.org/Apple-touch-icon.png',
	'usabilitywiki' => '//usability.wikimedia.org/UsabilityWiki-AppleTouch-Icon.png',
	'wikimania2005wiki' => '//$lang.$site.org/apple-touch-icon.png',
	'officewiki' => '//$lang.$site.org/apple-touch-icon.png',
	'foundationwiki' => '//$lang.$site.org/apple-touch-icon.png',
	'commonswiki' => '//$lang.$site.org/apple-touch-icon.png',
	'metawiki' => '//$lang.$site.org/apple-touch-icon.png',
),

'wgUserEmailUseReplyTo' => array(
	'default' => false, // http://bugzilla.wikimedia.org/show_bug.cgi?id=12655
),

'wgStatsMethod' => array(
	'default' => 'udp',
),

'wmgUseTitleKey' => array(
	'default' => true, // enable/disable TitleKey prefix search case-insensitivity ext
),

'wgUseLocalMessageCache' => array(
	'default' => true,
),

# CENTRAL AUTH @{
'wmgUseCentralAuth' => array(
	'default' => true,
	'testwiki' => true,
	'private' => false,
	'fishbowl' => false,
),

'wmgCentralAuthLoginIcon' => array(
	'default' => false,
	'wiki' => '/usr/local/apache/common/images/sul/wikipedia.png',
	'wikibooks' => '/usr/local/apache/common/images/sul/wikibooks.png',
	'wikinews' => '/usr/local/apache/common/images/sul/wikinews.png',
	'wikiquote' => '/usr/local/apache/common/images/sul/wikiquote.png',
	'wikisource' => '/usr/local/apache/common/images/sul/wikisource.png',
	'sourceswiki' => '/usr/local/apache/common/images/sul/wikisource.png',
	'wikiversity' => '/usr/local/apache/common/images/sul/wikiversity.png',
	'wiktionary' => '/usr/local/apache/common/images/sul/wiktionary.png',
	'metawiki' => '/usr/local/apache/common/images/sul/meta.png',
	'commonswiki' => '/usr/local/apache/common/images/sul/commons.png',
	'mediawikiwiki' => '/usr/local/apache/common/images/sul/mediawiki.png',
	'wikidatawiki' => '/usr/local/apache/common/images/sul/wikidata.png',
	'specieswiki' => '/usr/local/apache/common/images/sul/wikispecies.png',
	'incubatorwiki' => '/usr/local/apache/common/images/sul/incubatorwiki.png',
	'wikivoyage' => '/usr/local/apache/common/images/sul/wikivoyage.png',
	'wikimania2013wiki' => '/usr/local/apache/common/images/sul/wikimania.png',

	'brwikimedia' => '/usr/local/apache/common/images/sul/foundation.png',
),

'wmgCentralAuthAutoLoginWikis' => array(
	'default' => array(
		'.wikipedia.org' => 'enwiki',
		'meta.wikimedia.org' => 'metawiki',
		'.wiktionary.org' => 'enwiktionary',
		'.wikibooks.org' => 'enwikibooks',
		'.wikiquote.org' => 'enwikiquote',
		'.wikisource.org' => 'enwikisource',
		'commons.wikimedia.org' => 'commonswiki',
		'.wikinews.org' => 'enwikinews',
		'.wikiversity.org' => 'enwikiversity',
		'.mediawiki.org' => 'mediawikiwiki',
		'.wikidata.org' => 'wikidatawiki',
		'species.wikimedia.org' => 'specieswiki',
		'incubator.wikimedia.org' => 'incubatorwiki',
		'.wikivoyage.org' => 'enwikivoyage',
	),
	'+ptwiki' => array( 'br.wikimedia.org' => 'brwikimedia' ),
	'+ptwikibooks' => array( 'br.wikimedia.org' => 'brwikimedia' ),
	'+ptwikinews' => array( 'br.wikimedia.org' => 'brwikimedia' ),
	'+ptwikiquote' => array( 'br.wikimedia.org' => 'brwikimedia' ),
	'+ptwikisource' => array( 'br.wikimedia.org' => 'brwikimedia' ),
	'+ptwikiversity' => array( 'br.wikimedia.org' => 'brwikimedia' ),
	'+ptwikivoyage' => array( 'br.wikimedia.org' => 'brwikimedia' ),

),

# @}

'wgEnableMWSuggest' => array(
	'default' => true,
	'private' => false,
),

'wgCookieHttpOnly' => array(
	'default' => true,
),

'wmgUseDualLicense' => array(
	'default' => false,
	// Add new wikis  for dual-license here...
	'acewiki' => true,
	'arbcom_dewiki'    =>  true,
	'arbcom_nlwiki'    =>  true,
	'arbcom_fiwiki'    => true,
	'arwikimedia'	=> true,
	'arwikiversity'	=> true,
	'arzwiki' => true,
	'bewikimedia' => true,
	'bewikisource' => true,
	'bjnwiki' => true,
	'boardgovcomwiki' => true,
	'brwikimedia' => true,
	'brwikisource' => true,
	'checkuserwiki' => true,
	'ckbwiki' => true,
	'cswikinews' => true,
	'cswikiversity' => true,
	'cowikimedia' => true,
	'dkwikimedia' => true,
	'donatewiki' => true,
	'elwikinews' => true,
	'eowikinews' => true,
	'eowikisource' => true,
	'etwikimedia' => true,
	'extwiki' => true,
	'fawikinews' => true,
	'fiwikimedia' => true,
	'frrwiki' => true,
	'gagwiki' => true,
	'ganwiki' => true,
	'guwikisource' => true,
	'huwikinews' => true,
	'jawikiversity' => true,
	'hifwiki' => true,
	'kaawiki' => true,
	'kbdwiki' => true,
	'koiwiki' => true,
	'kowikiversity' => true,
	'lezwiki' => true,
	'liwikibooks' => true,
	'liwikisource' => true,
	'ltgwiki' => true,
	'mdfwiki' => true,
	'mhrwiki' => true,
	'minwiki' => true,
	'movementroleswiki' => true,
	'mrjwiki' => true,
	'mwlwiki' => true,
	'mxwikimedia' => true,
	'myvwiki' => true,
	'noboard_chapterswikimedia' => true,
	# 'nomcomwiki'    =>  true,
	'nsowiki' => true,
	'nycwikimedia' => true,
	'pcdwiki' => true,
	'pflwiki' => true,
	'plwikivoyage' => true,
	'pnbwiki' => true,
	'ptwikiversity' => true,
	'rowikivoyage' => true,
	'ruewiki'		=> true,
	'nowikimedia'   =>  true,
	'ruwikimedia' => true,
	'ruwikiversity' => true,
	'sawikiquote' => true,
	'sahwiki' => true,
	'sahwikisource' => true,
	'sawikisource' => true,
	'sewikimedia' => true,
	'slwikiversity' => true,
	'sqwikinews' => true,
	'stewardwiki' => true,
	'strategywiki' => true,
	'svwikiversity' => true,
	'szlwiki' => true,
	'srnwiki' => true,
	'tenwiki' => true,
	'trwikimedia' => true,
	'trwikinews' => true,
	'uawikimedia'   => true,
	'ukwikimedia'   => true,
	'usabilitywiki' =>  true,
	'vewikimedia' => true,
	'wikidatawiki' => true,
	'wikimania2009wiki' => true,
	'wikimania2010wiki' => true,
	'wikimania2011wiki' => true,
	'wikimania2012wiki' => true,
	'wikimania2013wiki' => true,
	'xmfwiki' => true,

	'wikivoyage' => true,
),

'wgEnableNewpagesUserFilter' => array(
	'default' => true,
	'frwiki' => false,
	'nlwiki' => false,
	'svwiki' => false,
),


'wgEnableWriteAPI' => array(
	'default' => true,
),

'wgAPIMaxResultSize' => array(
	'default' => 12582912, // 12 MB; temporary while I figure out what the deal with those overlarge revisions is --Roan
),

'wmgUseCollection' => array(
	// PDF generation / PediaPress stuff
	'default' => false,
	'testwiki' => true,
	'test2wiki' => true,
	'alswiki' => true, # Bug 18517
	'arwiki' => true, # bug 33828
	'bawiki' => true, // Bug 40879
	'bpywiki' => true, # Bug 18517
	'cawiki' => true, # Bug 21174
	'cswiki' => true, # Bug 20436
	'cswikiversity' => true, # Bug 21077
	'bnwiki' => true, # https://bugzilla.wikimedia.org/show_bug.cgi?id=20338
	'dawiki' => true, # 22444
	'de_labswikimedia' => true,
	'dewiki' => true, # live test on german - 2009-01-26
	'dewikiversity' => true, # Bug 37898
	'dsbwiki' => true,
	'elwiki' => true, # bug 22924
	'en_labswikimedia' => true,
	'enwiki' => true, # 2009-02-26 bv whee :D
	'enwikiversity' => true,
	'eowiki' => true, # 23434
	'eswiki' => true, # 2009-02-18
	'etwiki' => true,
	'euwiki' => true, # bug 28292
	'fawiki' => true, # bug 26319
	'fiwiki' => true,
	'frrwiki' => true, # Bug 38023
	'frwiki' => true, # 2009-02-18
	'frwikiversity' => true, # Bug 16178
	'foundationwiki' => true,
	'gdwiki' => true,
	'glwiki' => true,
	'hewiki' => true,
	'hewiktionary' => true,
	'hiwiki' => true,
	'hrwiki' => true, # Bug 19013
	'hsbwiki' => true,
	'huwiki' => true, # Bug 21005
	'idwiki' => true, # 18430
	'iswiki' => true, // Bug 43760
	'itwiki' => true,
	'kawiki' => true,
	'kkwiki' => true,
	'kmwiki' => true, // bug 30680
	'kowiki' => true,
	'mediawikiwiki' => true, # Bug 29228
	'metawiki' => true, // Bug 18058 on 2009-03-19
	'mkwiki' => true, # bug 37345
	'mswiki' => true, # Bug 20402
	'nlwiki' => true, # 2009-02-18
	'nnwiki' => true, # 2009-05-05
	'nowiki' => true, # 2009-05-05
	'outreachwiki' => true, # 28495
	'plwiki' => true, # 2009-02-18
	'ptwiki' => true, # 2009-02-18
	'ptwikiversity' => true, # 18367
	'rmwiki' => true,
	'rowiki' => true, # 28347
	'ruwiki' => true, # 22330
	'ruwikiversity' => true,
	'sewikimedia' => true, # Bug 38988
	'simplewiki' => true, # 2009-02-18
	'skwiki' => true, # bug 22038
	'slwiki' => true,
	'sourceswiki' => true,
	'sqwiki' => true, # bug 26074
	'srwiki' => true, # Per Brion on 2009-04-03
	'strategywiki' => true, # 2009-11-07, Bug 21361
	'svwiki'    => true, # 2009-03-24 Bug 18023
	'tawiki' => true,
	'tewiki' => true,
	'thwiki'    => true,
	'trwiki'	=> true, # Bug 22791
	'ukwiki'    => true, # 23437
	'urwiki' => true, # Bug 41563
	'viwiki' => true, # 2009-12-28 bug 21566
	'wikibooks' => true, # on for all wikibooks! 2008-10-23
	'wikisource' => true, # 2009-02-24
	'wikinews' => true,
	'wikivoyage' => true,
),

'wmgCollectionPortletForLoggedInUsersOnly' => array(
	'default' => false,
),

'wmgCollectionArticleNamespaces' => array(
	'default' => array( NS_MAIN, NS_TALK, NS_USER, NS_USER_TALK, NS_PROJECT, NS_PROJECT_TALK, NS_MEDIAWIKI, NS_MEDIAWIKI_TALK, NS_HELP, NS_HELP_TALK, 100, 101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111 ),
	'metawiki' => array( NS_MAIN, NS_TALK, NS_USER, NS_USER_TALK, NS_PROJECT, NS_PROJECT_TALK, NS_MEDIAWIKI, NS_MEDIAWIKI_TALK, NS_HELP, NS_HELP_TALK, 100, 101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 200 ),
),

'wmgCollectionHierarchyDelimiter' => array(
	'default'  => false, // false == don't overwrite the extension's default settings
	'wikibooks' => '/',
),

'wmgCollectionPortletFormats' => array(
	'default' => array( 'rl' ),
	'tawiki' => array( 'odf' ),       // Bug 37154
	'tawikibooks' => array( 'odf' ),  // Bug 37154
	'tawikisource' => array( 'odf' ), // Bug 37154
	'tawikinews' => array( 'odf' ),   // Bug 37154
	'guwikibooks' => array( 'odf' ),  // Bug 37384
	'guwikisource' => array( 'odf' ), // Bug 37384
	'mlwikibooks' => array( 'odf' ),  // Bug 37672
	'mlwikisource' => array( 'odf' ), // Bug 37672
	'newikibooks' => array( 'odf' ), // Bug 37374
	'newiktionary' => array( 'odf' ), // Bug 37374
	'urwiki' => array  ( 'odf' ),     // Bug 41714
	'urwikisource' => array ( 'odf' ),// Bug 41714
),

'wmgCollectionUseEpub' => array(
	'default' => true,
),

'wmgUseSpamBlacklist' => array(
	'default' => true,
	'private' => false,
	'fishbowl' => false, // not needed, private editing...
),

'wgAllowImageMoving' => array(
	'default' => true, // brion 2009-09-21
	'testwiki' => true,
	'usabilitywiki' => true,
),

'wgHandheldStyle' => array(
	// Set MonoBook's handheld stylesheet to use Chick's style
	// in place of big fat nothin'.
	// New Kindle should support it; Opera Mini in "mobile mode" should also see it.
	// iPhone and Opera Mini in pretty mode will ignore it unless wgHandheldForIPhone is also turned on.
	'default' => 'chick/main.css',
),

'wmgUseNewUserMessage' => array(
	'default' => false,
	'arwiki' => true,
	'arwikisource' => true,
	'commonswiki' => true,
	'enwikinews' => true,
	'guwiki' => true, // Bug 40872
	'guwikisource' => true, // Bug 40872
	'hiwiki' => true,
	'incubatorwiki' => true,
	'kowiki' => true,
	'ladwiki' => true, // https://bugzilla.wikimedia.org/show_bug.cgi?id=30221
	'lvwiki' => true,
	'metawiki' => true,
	'mlwiki' => true, // bug 36595
	'mrwiki' => true,
	'mrwikisource' => true,
	'ndswiki' => true,
	'ptwiktionary' => true, // bug 44412
	'rowiki' => true,
	'rowikinews' => true,
	'rowikisource' => true, // http://bugzilla.wikimedia.org/show_bug.cgi?id=28307
	'ruwikiversity' => true,
	'strategywiki' => true,
	'thwiki' => true, // http://bugzilla.wikimedia.org/show_bug.cgi?id=28689
	'thwiktionary' => true, // bug 31600
	'thwikisource' => true, // bug 31600
	'thwikibooks' => true, // bug 31600
	'thwikiquote' => true, // bug 31600
	'zhwiki' => true, // bug 30362
),

'wmgNewUserMessageOnAutoCreate' => array(
	'default' => false,
	'arwiki' => true,
	'arwikisource' => true,
	'commonswiki' => true,
	'enwikinews' => true,
	'guwiki' => true, // Bug 40872
	'guwikisource' => true, // Bug 40872
	'hiwiki' => true,
	'incubatorwiki' => true,
	'lvwiki' => true,
	'metawiki' => true,
	'mlwiki' => true,
	'ndswiki' => true,
	'rowiki' => true,
	'thwiki' => true,
	'thwiktionary' => true, // bug 31600
	'thwikisource' => true, // bug 31600
	'thwikibooks' => true, // bug 31600
	'thwikiquote' => true, // bug 31600
),

'wmgNewUserSuppressRC' => array(
	'default' => true,
),

'wmgNewUserMinorEdit' => array(
	'default' => true,
	'arwiki' => false,
	'incubatorwiki' => false,
),

'wmgApplyGlobalBlocks' => array(
	'default' => true,
	'private' => false,
	'fishbowl' => false,
	'metawiki' => false,
),

'wmgUseFlaggedRevs' => array(
	'default' => false,
	# To update this dblist:
	#   * ADD THE TABLES TO THE DATABASE
	#	e.g.mwscript sql.php ruwikinews php-1.18/extensions/FlaggedRevs/schema/mysql/FlaggedRevs.sql
	#   * edit flaggedrevs.dblist (or the corresponding file for the current realm)
	#   * touch InitialiseSettings.php to invalidate the cache
	#   * sync-common-all
	#   * press enter
	'flaggedrevs' => true,
),

'wgMaximumMovedPages' => array(
	'default' => 100,
	'enwikibooks' => 500,
	'frwikisource' => 500,
	'incubatorwiki' => 500,
),

'wgGroupsAddToSelf' => array(
	'metawiki' => array( 'sysop' => array( 'flood', 'translationadmin' ) ), // Bug 37198
	'enwikibooks' => array( 'sysop' => array( 'flood' ) ),
	'enwikinews' => array( 'sysop' => array( 'flood' ) ),
	'enwikisource' => array( 'sysop' => array( 'flood' ) ), //Bug 36863
	'frwikinews' => array( 'sysop' => array( 'flood' ) ),
	'itwikibooks' => array( 'sysop' => array( 'flooder' ) ), // Bug 39569
	'itwikisource' => array( 'sysop' => array( 'flood' ) ), // Bug 36600
	'itwiktionary' => array( 'sysop' => array( 'flood' ) ), // Bug 39306
	'simplewiki' => array( 'sysop' => array( 'flood' ) ),
	'srwiki' => array( 'sysop' => array( 'flood' ) ),
	'plwiki' => array( 'sysop' => array( 'flood' ) ), // Bug 20155
	'strategywiki' => array( 'sysop' => array( 'flood' ), ),
	'zhwiki' => array( 'sysop' => array( 'flood' ) ),
),
'wgGroupsRemoveFromSelf' => array(
	'metawiki' => array( 'sysop' => array( 'flood', 'translationadmin' ) ), // https://bugzilla.wikimedia.org/show_bug.cgi?id=37198
	'enwikibooks' => array( 'sysop' => array( 'flood' ) ),
	'enwikinews' => array( 'sysop' => array( 'flood' ) ),
	'enwikisource' => array( 'sysop' => array( 'flood' ) ), # https://bugzilla.wikimedia.org/show_bug.cgi?id=36863
	'frwikinews' => array( 'sysop' => array( 'flood' ) ),
	'frwiktionary' => array( 'botadmin' => array( 'botadmin' ) ),
	'itwikisource' => array( 'sysop' => array( 'flood' ) ), // https://bugzilla.wikimedia.org/show_bug.cgi?id=36600
	'mlwiki' => array( 'botadmin' => array( 'botadmin' ) ),
	'mlwiktionary' => array( 'botadmin' => array( 'botadmin' ) ),
	'simplewiki' => array( 'sysop' => array( 'flood' ) ),
	'plwiki' => array( 'flood' => array( 'flood' ) ), // https://bugzilla.wikimedia.org/show_bug.cgi?id=20155 , bug 21238
	'strategywiki' => array( 'sysop' => array( 'flood' ) ),
	'srwiki' => array( 'sysop' => array( 'flood' ) ),
	'zhwiki' => array( 'sysop' => array( 'flood' ) ),
),

'wgEnableAPI' => array(
	'default' => true,
),

'wmgUseCodeReview' => array(
	'default' => false,
	'mediawikiwiki' => true,
),

'wgSquidMaxage' => array(
	'default' => 2678400, // 31 days seems about right
	'foundationwiki' => 3600, // template links may be funky
),

# abuse filter @{
'wmgUseAbuseFilter' => array(
	'default' => true,
),
// emergency disable: shut off filters tagging >5% of posts on a total of at least 25 posts per day
'wmgAbuseFilterEmergencyDisableThreshold' => array(
	'default' => array(
		'default' => 0.05,
		'feedback' => 0.20,
	),
),
'wmgAbuseFilterEmergencyDisableCount' => array(
	'default' => array(
		'default' => 2,
		'feedback' => 25,
	),
	'enwiki' => array(
		'default' => 25,
		'feedback' => 50,
	),
),
'wmgAbuseFilterEmergencyDisableAge' => array(
	'default' => array(
		'default' => 86400,
	),
),
# @}

'wgThumbLimits' => array(
	'default' => array( 120, 150, 180, 200, 220, 250, 300 ),
	'+frwiki' => array( 460, 620 ),
	'+itwikiquote' => array( 360 ),
	'svwiki' => array( 120, 200, 250, 300, 360 ),
),
'wmgThumbsizeIndex' => array(
	'default' => 4,
	'svwiki' => 2, // bug 16739
),

'wgTorTagChanges' => array(
	'default' => false, // pending discussion
	'testwiki' => true,
),

'wmgContactPageConf' => array(
	'default' => false,

	// bug 15624
	'nlwiki' => array(
		'wgContactUser' => 'WikiAdmin',
		'wgContactSenderName' => 'Contactformulier op nl-Wikipedia',
		'wgContactIncludeIP' => true,
	),
),

'wgUseContactPageFundraiser' => array(
	'default' => false,
	'donatewiki' => true,
	'foundationwiki' => true,
	'testwiki' => true,
),

'wgCopyUploadProxy' => array(
	'default' => 'url-downloader.wikimedia.org:8080',
),
'wgAllowCopyUploads' => array(
	'default' => false,
	'testwiki' => true,
	'test2wiki' => true,
	'commonswiki' => true,
),
'wgCopyUploadsFromSpecialUpload' => array(
	'default' => false,
),
// For Flickr uploads via UploadWizard - 2012-11-20
'wgCopyUploadsDomains' => array(
	'default' => array( '*.flickr.com', '*.staticflickr.com', ),
	'+testwiki' => array( 'upload.wikimedia.org' ),
),

'wgMaxUploadSize' => array(
	 // Only affects URL uploads; web uploads are enforced by PHP.
	 'default' => 1024 * 1024 * 500, // 500 MB
	 'ptwiki'  => 1024 * 500, // 500 KB - https://bugzilla.wikimedia.org/23186
),

'wmgUseCommunityVoice' => array (
	'default' => false,
	'usabilitywiki' => true,
),

'wmgUsePdfHandler' => array (
	'default' => true, // brion -- 2009-08-25
),

'wmgUseUsabilityInitiative' => array (
	'default' => true,
),
'wmgUsabilityEnforce' => array(
	'default' => true,
),
'wmgUseCollapsibleNav' => array(
	'default'	=> true,
	'outreachwiki'	=> false,
	'ukwikimedia'	=> false,
),

'wmgEnableVector' => array(
	'default' => true,
),

'wmgUserDailyContribs' => array(
	'default'	=> true, // For clicktracking usability stuff
),

'wgVectorUseIconWatch' => array(
	'default'	=> true,
),

'wgMaxMsgCacheEntrySize' => array(
	'default' => 1024,
),

'wgUseXVO' => array(
	'default' => true,
),

# FEEDBACK @{
'wmgUseReaderFeedback' => array(
	// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
	// Setting this requires some extra tables
	// Install extensions/ReaderFeedback/ReaderFeedback.sql
	// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
	'default'			=> false,
	#'en_labswikimedia'		=> true,	# disabled for AFTv5 testing per Erik --Roan
	'enwikibooks'			=> true,
	'enwikinews'			=> true,
	'huwiki'			=> true,
	'readerfeedback_labswikimedia'	=> true,
	'ruwikinews'			=> true,
	'strategywiki'			=> true,
//	'testwiki'			=> true,
	'trwikinews'			=> true,
),

'wmgFeedbackNamespaces' => array(
	'default'	=> array( NS_MAIN ),
	'enwikibooks'	=> array( NS_MAIN, 102, 110 ),
	'strategywiki'	=> array( 106 ),
),
'wmgFeedbackTags' => array(
	'default' => false, // use default
	'huwiki' => array(
		'reliability'  => 3,
		'completeness' => 2,
		'npov'	 => 2,
		'presentation' => 1
	),
	'strategywiki' => array(
		'priority'     => 1,
		'impact'       => 1,
		'feasibility'  => 1,
		'desirability' => 1,
	),
	'readerfeedback_labswikimedia' => array(
		'usefulness'   => 3,
		'presentation' => 3,
		'neutrality'   => 3,
	),
),
'wmgFeedbackSizeThreshhold' => array(
	'default'	  => 15,
),
# @} end of FEEDBACK

'wmgUseLocalisationUpdate' => array(
	'default' => true,
),

'wgOldChangeTagsIndex' => array(
	'default' => false, // For compat with index on changetags table
	'arwiki' => true,
	'commonswiki' => true,
	'cswiki' => true,
	'dewiki' => true,
	'elwiki' => true,
	'enwiki' => true,
	'enwikisource' => true,
	'enwiktionary' => true,
	'eswiki' => true,
	'etwiki' => true,
	'fawiki' => true,
	'fiwiki' => true,
	'frwiki' => true,
	'hewiki' => true,
	'huwiki' => true,
	'idwiki' => true,
	'itwiki' => true,
	'jawiki' => true,
	'ltwiki' => true,
	'mrwiki' => true,
	'nlwiki' => true,
	'plwiki' => true,
	'ptwiki' => true,
	'rowiki' => true,
	'ruwiki' => true,
	'simplewiki' => true,
	'svwiki' => true,
	'trwiki' => true,
	'ukwiki' => true,
	'zhwiki' => true,
),

'wgHtml5' => array(
	'default' => true,
),

'wgVectorShowVariantName' => array(
	'default' => false,
	'zhwiki' => true, // bug 23531
	'srwiki' => true,
),

'wmgUseLiquidThreads' => array(
//
//  !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
//  Needs schema changes before it can be enabled. Ask Andrew for details
//  Better, don't add wikis here unless you are Andrew or he knows what you are doing.
//  !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
//
	'default' => false,
	'testwiki' => true, // Expanding out to testwiki -- Andrew 2009-10-13
	 // -------------------
	'enwikinews' => true, // Per bug 21956 -- Andrew 2009-02-09
	'enwiktionary' => true, // Bug 23417 -- ariel 2010-16-05
	'fiwikimedia' => true, // Bug 37778
	'huwiki' => true, // Bug 22909 -- Andrew 2010-09-06
	'liquidthreads_labswikimedia' => true,
	'mediawikiwiki' => true,
	'officewiki' => true, // Erik requested, by mail.
	'ptwikibooks' => true, // Bug 24143 -- Andrew 2010-08-16
	'strategywiki' => true, // Philippe says it's cool -- Andrew 2009-10-13
	'sewikimedia' => true, // Bug 24377 -- Andrew 2010-08-28
	'svwikisource' => true, // Bug 23220 -- Andrew 2010-08-28
	'test2wiki' => true,
	'wikimania2010wiki' => true, // Erik requested, by mail
	'wikimania2011wiki' => true, // Erik requested, by mail
),

'wmgLiquidThreadsOptIn' => array(
	'default' => true, // New wikis by default are opt-in per-page LiquidThreads
	// ---------
	'strategywiki' => false,
),

'wmgLQTUserControlNamespaces' => array(
	'default' => null,
	'enwikinews' => array( 102 ), // Comments namespace
),

'wmgClickTracking' => array(
	# Note: Special:ClickTracking is disabled in CommonSettings.php
	# Disabling due to CR r58099 -- TS 2010-06-03
	'default' => false,
	'en_labswikimedia' => true, // ArticleFeedbackv5 needs this
	'dewiki' => true, // ArticleFeedbackv5 needs this
	'enwiki' => true, // VectorSectionEditLinks needs this
	'eswiki' => true, // ArticleFeedback needs this
	'eswikinews' => true, // ArticleFeedback needs this
	'hiwiki' => true, // ArticleFeedback needs this
	'huwiki' => true, // ArticleFeedback needs this
	'metawiki' => true, // ArticleFeedback needs this
	'mlwiki' => true, // ArticleFeedback needs this
	'ptwiki' => true, // ArticleFeedback needs this
	'ptwikibooks' => true, // ArticleFeedback needs this
	'srwiki' => true, // ArticleFeedback needs this
	'testwiki' => true,
	'test2wiki' => true,
	'zhwiki' => true, // ArticleFeedback needs this
),

'wmgClickTrackThrottle' => array(
	'default' => -1
),

'wmgDonationInterface' => array(
	'default' => false,
	'testwiki' => true,
	'foundationwiki' => true,
	'donatewiki' => true,
),

'wmgUseGlobalUsage' => array(
	'default' => true, # Enabled on all PUBLIC wikis
	'closed' => false,
	'private' => false,
),

'wgDisabledVariants' => array(
	'default' => array(),
	'zhwiki' => array( 'zh-mo', 'zh-my' ),
	'zhwikibooks' => array( 'zh-mo', 'zh-my' ),
	'zhwikinews' => array( 'zh-mo', 'zh-my' ),
	'zhwikiquote' => array( 'zh-mo', 'zh-my' ),
	'zhwikisource' => array( 'zh-cn', 'zh-hk', 'zh-mo', 'zh-my', 'zh-sg', 'zh-tw' ),
	'zhwiktionary' => array( 'zh-cn', 'zh-hk', 'zh-mo', 'zh-my', 'zh-sg', 'zh-tw' ),
),

'wmgUseWikimediaLicenseTexts' => array(
	'default' => false,
	'commonswiki' => true,
),

'wmgUseAPIRequestLog' => array(
	'default' => false,
),

'wgSearchSuggestCacheExpiry' => array(
	'default' => 86400,
),

'wgDisableHardRedirects' => array(
	'default' 	 => true,
	'donatewiki' => false,
	'foundationwiki' => false,
),

'wmgUseLivePreview' => array(
	'default' => false,
	'testwiki' => true,
),

'wgUseEmailCapture' => array(
	'default' => false,
	'testwiki' => true,
	'enwiki' => true,
),

'wgRevokePermissions' => array(
	'tawiki' => array(
		'nocreate' => array( 'createpage' => true ),
	),
),

'wgArticleCountMethod' => array(
	'default' => 'link',
	'cswikinews' => 'any',        // Bug 43525
	'enwikibooks' => 'comma',     // Bug 27256
	'ptwikibooks' => 'comma',     // Bug 27256
	'guwikisource' => 'any',      // Bug 40173
),

'wgUseContributionTracking' => array(
	'default' => false,
	'donatewiki' => true,
	'foundationwiki' => true,
	'testwiki' => true,
),

'wmgUseUploadWizard' => array(
	'default' => false,
	'testwiki' => true,
	'test2wiki' => true,
	'commonswiki' => true,
	'donatewiki' => true,
	'foundationwiki' => true,
),

'wmgUseMwEmbedSupport' => array(
	'default' => true,
),

// NOTE: TMH *requires* MwEmbedSupport to function
'wmgUseTimedMediaHandler' => array(
	'default' => true,
),

'wmgEnableLocalTimedText' => array(
	'default' => false,

	'commonswiki' => true,
	'enwiki' => true,
	'test2wiki' => true,
	'testwiki' => true,
),

'wmgUseVisualEditor' => array(
	'default' => false,
	'testwiki' => true,
	'mediawikiwiki' => true,
	'enwiki' => true, // dark launch
),

'wmgVisualEditorParsoidPrefix' => array(
	'default' => '', // This MUST be configured per-wiki for VE to work correctly
	'testwiki' => 'test',
	'mediawikiwiki' => 'mw',
	'enwiki' => 'en',
),

'wmgVisualEditorNamespaces' => array(
	'default' => array( NS_MAIN, NS_USER ),
	'testwiki' => array(), // NS_VISUALEDITOR added in CommonSettings.php
	'mediawikiwiki' => array(), // NS_VISUALEDITOR added in CommonSettings.php
),

// For wikis that used VisualEditor before 2012-11-12
'wmgUseVisualEditorNamespace' => array(
	'default' => false,
	'testwiki' => true,
	'mediawikiwiki' => true,
),

'wmgVisualEditorHide' => array(
	'default' => false,
	'testwiki' => true, // pre-2012-11-12 install
	'mediawikiwiki' => true, // pre-2012-11-12 install
),

'wmgVisualEditorDefault' => array(
	'default' => false,
	'testwiki' => true, // pre-2012-11-12 install
	'mediawikiwiki' => true // pre-2012-11-12 install
),

'wmgUseRSSExtension' => array(
	'default' => false,
	'foundationwiki' => true,
	'mediawikiwiki' => true,
	'uawikimedia' => true,
),
'wmgRSSAllowedFeeds' => array(
	'default' => array(),
	'uawikimedia' => array( 'http://wikimediaukraine.wordpress.com/feed/' ),
),

'wmgUseDoubleWiki' => array(
	'default' => false,
	'wikisource' => true,
	'sourceswiki' => true,
	'frwiktionary' => true,
	'frwikiversity' => true,
	'fawiki' => true,
	'testwiki' => true,
),

'wgLogAutocreatedAccounts' => array(
	'default' => false,
	'test2wiki' => true,
),

'wgCategoryCollation' => array(
	'default' => 'uppercase',
	'mediawikiwiki' => 'uca-default',
	'ptwiki' => 'uca-default', # bug 35632
	'ptwikibooks' => 'uca-default', # bug 43185
	'iswiktionary' => 'identity', # bug 30722
),

'wmgVectorSectionEditLinks' => array(
	'default' => false,
	'testwiki' => true,
),

'wmgUseArticleFeedback' => array(
	'default' => false,
	'testwiki' => true,
	'en_labswikimedia' => true,
	'enwiki' => true,
	'eswiki' => true,
	'eswikinews' => true,
	'hiwiki' => true,
	'huwiki' => true,
	'metawiki' => true,
	'ptwiki' => true,
	'ptwikibooks' => true,
	'srwiki' => true,
	'zhwiki' => true,
),
'wmgArticleFeedbackCategories' => array(
	'default' => array(),
	'en_labswikimedia' => array( 'Article Feedback Pilot', 'Article Feedback', 'Article Feedback Additional Articles' ),
	'testwiki' => array( 'Article Feedback Pilot', 'Article Feedback', 'Article Feedback Additional Articles' ),
	'enwiki' => array( 'Article Feedback Pilot', 'Article Feedback', 'Article Feedback Additional Articles' ),
	'hiwiki' => array( 'गुणवत्ता आकलन अतिरिक्त लेख', 'गुणवत्ता आकलन', 'आकलन', 'Article Feedback Pilot', 'Article Feedback', 'Article Feedback Additional Articles' ),
	'huwiki' => array( 'ArticleFeedback teszt' ),
	'metawiki' => array( 'Translation/Fundraising 2011/all' ),
	'ptwiki' => array( '!Páginas que podem ser avaliadas pelos leitores' ),
),
'wmgArticleFeedbackBlacklistCategories' => array(
	'default' => array( 'Article Feedback Blacklist' ),
	'en_labswikimedia' => array( 'Article Feedback Blacklist', 'Article Feedback 5', 'Article Feedback 5 Additional Articles' ),
	'testwiki' => array( 'Article Feedback Blacklist', 'Article Feedback 5', 'Article Feedback 5 Additional Articles' ),
	'enwiki' => array( 'Article Feedback Blacklist', 'Article Feedback 5', 'Article Feedback 5 Additional Articles' ),
	'eswiki' => array( 'Wikipedia:Desambiguación', 'Wikipedia:Exclusiones de la evaluación de artículos' ),
	'hiwiki' => array( 'No Feedback', 'आकलन रहित लेख' ),
	'ptwiki' => array( '!Páginas que não podem ser avaliadas pelos leitores' ),
	'ptwikibooks' => array( 'Páginas que não podem ser avaliadas pelos leitores' ),
	'srwiki' => array( 'Чланци изузети од оцењивања' ),
	'zhwiki' => array( '消歧义', '条目删除候选', '怀疑侵犯版权页面', '快速删除候选' , '禁止索引的页面' ),
),
'wmgArticleFeedbackLotteryOdds' => array(
	'default' => 0,
	'testwiki' => 90,
	'en_labswikimedia' => 100,
	'enwiki' => 90,
	'eswiki' => 100,
	'eswikinews' => 100,
	'hiwiki' => 100,
	'ptwiki' => 100,
	'ptwikibooks' => 100,
	'srwiki' => 100,
	'zhwiki' => 100,
),
'wmgArticleFeedbackDashboard' => array(
	'default' => false,
	'testwiki' => true,
	'enwiki' => true,
	'eswiki' => true,
	'eswikinews' => true,
	'hiwiki' => true,
	'metawiki' => true,
	'ptwiki' => true,
	'ptwikibooks' => true,
	'srwiki' => true,
	'zhwiki' => true,
),
'wmgArticleFeedbackNamespaces' => array(
	'default' => false, // $wgContentNamespaces
	'eswiki' => array( NS_MAIN ),
	'eswikinews' => array( NS_MAIN ),
	'hiwiki' => array( NS_MAIN, 100 /*NS_PORTAL*/ ),
	'ptwiki' => array( NS_MAIN, NS_HELP, 102 /* Anexo */ ),
	'ptwikibooks' => array( NS_MAIN, NS_HELP, NS_PROJECT ),
),
'wmgArticleFeedbackRatingTypes' => array(
	'default' => false, // use defaults
	'metawiki' => array( 5 => 'accurate', 4 => 'wellwritten', 6 => 'errorfree' ),
),
'wmgUseArticleFeedbackv5' => array(
	'default' => false,
	// 'en_labswikimedia' => true, // Disabled due to out of date schema --Reedy
	'dewiki' => true, // Bug 42693
	'enwiki' => true,
	'testwiki' => true,
),
'wmgArticleFeedbackv5LotteryOdds' => array(
	'default' => 0,
	'testwiki' => array( NS_MAIN => 10, NS_HELP => 100 ),
	'dewiki' => array( NS_MAIN => 0.1 ), // ~ 1500 articles out of 1.5 mio in total
	'enwiki' => array( NS_MAIN => 10, NS_HELP => 100 ),
),
'wmgArticleFeedbackv5Namespaces' => array(
	'default' => array( NS_MAIN ),
	'dewiki' => array( NS_MAIN ),
	'enwiki' => array( NS_MAIN, NS_HELP, NS_PROJECT ),
	'testwiki' => array( NS_MAIN, NS_HELP, NS_PROJECT ),
),
'wmgArticleFeedbackv5Categories' => array(
	'default' => array(),
	'dewiki' => array(
			'Wikipedia:Artikel-Feedback Grundstock',
			'Wikipedia:Artikel-Feedback/Artikelgrundstock',
			'Wikipedia:Artikel-Feedback/Artikel des Tages oder Schon gewusst',
			'Wikipedia:Artikel-Feedback/Zusätzliche Artikel',
		),
	'en_labswikimedia' => array( 'Article_Feedback_5', 'Article_Feedback_5_Additional_Articles' ),
	'enwiki' => array( 'Article_Feedback_5', 'Article_Feedback_5_Additional_Articles' ),
	'testwiki' => array( 'Article_Feedback_5', 'Article_Feedback_5_Additional_Articles' ),
),
'wmgArticleFeedbackv5BlacklistCategories' => array(
	'default' => array(),
	'dewiki' => array( 'Wikipedia:Artikel-Feedback_Ausschlussliste', 'Wikipedia:Artikel-Feedback/Ausschlussliste' ),
	'en_labswikimedia' => array( 'Article_Feedback_Blacklist' ),
	'enwiki' => array( 'Article_Feedback_Blacklist' ),
	'testwiki' => array( 'Article_Feedback_Blacklist' ),
),
'wmgArticleFeedbackv5OversightEmails' => array(
	'default' => 'aft@wikimedia.org',
	'dewiki' => 'oversight-de-wp@wikimedia.org',
	'enwiki' => 'oversight-en-wp@wikipedia.org',
	#'enwiki' => 'roan@wikimedia.org',
),
'wmgArticleFeedbackv5OversightEmailHelp' => array(
	'default' => 'http://en.wikipedia.org/wiki/Wikipedia:Article_Feedback_Tool/Version_5/Help/Feedback_page_Oversighters',
	'dewiki' => 'http://de.wikipedia.org/wiki/Wikipedia:Artikel-Feedback/Umgang_mit_Feedback', # Could be changed once a separate page exists
),
'wmgArticleFeedbackv5AutoHelp' => array(
	'default' => '//en.wikipedia.org/wiki/Wikipedia:Article_Feedback_Tool/Version_5/Help',
	'dewiki' => '//de.wikipedia.org/wiki/Wikipedia:Artikel-Feedback',
),
'wmgArticleFeedbackv5LearnToEdit' => array(
	'default' => '//en.wikipedia.org/wiki/Wikipedia:Tutorial',
	'dewiki' => '//de.wikipedia.org/wiki/Wikipedia:Tutorial',
),
'wmgArticleFeedbackv5AbuseFiltering' => array(
	'default' => false,
	'testwiki' => true,
	'dewiki' => true,
	'enwiki' => true,
),
'wmgArticleFeedbackv5CTABuckets' => array(
	'default' => array(
		'buckets' => array(
			'0' => 100, // display nothing
			'1' => 0, // display "Enticement to edit"
			'2' => 0, // display "Learn more"
			'3' => 0, // display "Take a survey"
			'4' => 0, // display "Sign up or login"
			'5' => 0, // display "View feedback"
			'6' => 0, // display "Visit Teahouse"
		),
		// This version number is added to all tracking event names, so that
		// changes in the software don't corrupt the data being collected. Bump
		// this when you want to start a new "experiment".
		'version' => 1,
		// Users may constantly be rebucketed, giving them new CTAs each time.
		'expires' => 0,
		// Track the event of users being bucketed - so we can be sure the odds
		// worked out right. [LATER - depends on UDP logging being set up]
		'tracked' => false,
	),
	'dewiki' => array(
		'buckets' => array(
			'0' => 0, // display nothing
			'1' => 50, // display "Enticement to edit"
			'2' => 0, // display "Learn more"
			'3' => 0, // display "Take a survey"
			'4' => 50, // display "Sign up or login"
			'5' => 0, // display "View feedback"
			'6' => 0, // display "Visit Teahouse"
		),
		// This version number is added to all tracking event names, so that
		// changes in the software don't corrupt the data being collected. Bump
		// this when you want to start a new "experiment".
		'version' => 1,
		// Users may constantly be rebucketed, giving them new CTAs each time.
		'expires' => 0,
		// Track the event of users being bucketed - so we can be sure the odds
		// worked out right. [LATER - depends on UDP logging being set up]
		'tracked' => false,
	),
	'enwiki' => array(
		'buckets' => array(
			'0' => 0, // display nothing
			'1' => 49, // display "Enticement to edit"
			'2' => 0, // display "Learn more"
			'3' => 0, // display "Take a survey"
			'4' => 50, // display "Sign up or login"
			'5' => 0, // display "View feedback"
			'6' => 1, // display "Visit Teahouse"
		),
		// This version number is added to all tracking event names, so that
		// changes in the software don't corrupt the data being collected. Bump
		// this when you want to start a new "experiment".
		'version' => 6,
		// Users may constantly be rebucketed, giving them new CTAs each time.
		'expires' => 0,
		// Track the event of users being bucketed - so we can be sure the odds
		// worked out right. [LATER - depends on UDP logging being set up]
		'tracked' => false,
	),
),

'wmgUsePoolCounter' => array(
	'default' => true,
),

'wmgUseNarayam' => array(
	'default' => false,
	'amwiki' => true, // Bug 41460
	'amwikiquote' => true, // Bug 41460
	'amwiktionary' => true, // Bug 41460
	'aswiki' => true, // Bug 32042
	'aswikisource' => true, // Bug 43129
	'betawikiversity' => true, // Bug 41912
	'bnwiki' => true, // Bug 40366
	'bnwikibooks' => true, // Bug 40366
	'bnwikisource' => true, // Bug 36104
	'commonswiki' => true, // Bug 32619
	'guwiki' => true, // Bug 33423
	'guwikisource' => true, // Bug 35138
	'guwiktionary' => true, // bug 37365
	'guwikiquote' => true, // bug 37385
	'hiwiki' => true, // Bug 35436
	'hiwiktionary' => true, // Bug 41015
	'kawiki' => true, // Bug 39432
	'knwiki' => true, // Bug 34516
	'knwikisource' => true, // Bug 37456
	'mlwikiquote' => true, // Bug 27949
	'mlwikibooks' => true, // Bug 27949
	'mlwiktionary' => true, // Bug 27949
	'mlwikisource' => true, // Bug 27949
	'mlwiki' => true, // Bug 27949
	'mrwiki' => true, // Bug 32669
	'mrwikibooks' => true, // Bug 39200
	'mrwikiquote' => true, // Bug 39200
	'mrwiktionary' => true, // Bug 39200
	'mrwikisource' => true, // Bug 34454
	'orwiki' => true, // Bug 31814
	'orwiktionary' => true, // Bug 31857
	'pawiki' => true, // Bug 32516
	'sawiki' => true, // Bug 29515
	'sawikibooks' => true, // Bug 29515
	'sawikisource' => true, // Bug 29515
	'sawiktionary' => true, // Bug 29515
	'siwiki' => true, // Bug 31372
	'siwikibooks' => true, // Bug 31372
	'siwiktionary' => true, // Bug 31372
	'tawiki' => true, // Bug 29798
	'tawikibooks' => true, // Bug 29798
	'tawikinews' => true, // Bug 31142
	'tawikiquote' => true, // Bug 31142
	'tawikisource' => true, // Bug 29798
	'tawiktionary' => true, // Bug 31142
	'tewiki' => true, // Bug 33480
	'tewikisource' => true, // Bug 37336
	'tewiktionary' => true, // Bug 37336
),
'wmgNarayamEnabledByDefault' => array(
	'default' => false, // Note Narayam default is true
	'commonswiki' => false, // Bug 32619
	'amwiki' => true, // Bug 41460
	'amwikiquote' => true, // Bug 41460
	'amwiktionary' => true, // Bug 41460
	'kawiki' => true, // Bug 39432
	'knwiki' => true, // Bug 34591
	'orwiki' => true, // Bug 31859
	'orwiktionary' => true, // Bug 31859
),
'wmgNarayamUseBetaMapping' => array(
	'default' => false,
	'betawikiversity' => true, // Bug 41912 (as incubator.)
),

'wmgUseWebFonts' => array(
	'default' => false,
	'amwiki' => true,
	'amwiktionary' => true, // Bug 34700
	'amwikiquote' => true, // Bug 34700
	'arwiki' => true, // 41359
	'arwikibooks' => true, // 41359
	'arwikinews' => true, // 41359
	'arwikiquote' => true, // 41359
	'arwikisource' => true, // 41359
	'arwikiversity' => true, // 41359
	'arwiktionary' => true, // 41359
	'aswiki' => true,
	'aswikibooks' => true,
	'aswikisource' => true, // Bug 43129
	'aswiktionary' => true,
	'betawikiversity' => true, // Bug 41912 (as incubator.)
	'bnwiki' => true,
	'bnwikibooks' => true,
	'bnwiktionary' => true,
	'bpywiki' => true, // Bug 33368
	'bugwiki' => true, // Bug 34550
	'enwikisource' => true,
	'enwiktionary' => true, // Bug 43834
	//'fawiki' => true, // Bug 42280 - some bugs to fix per comment 6
	'frwikisource' => true, // Bug 35328
	'guwiki' => true,
	'guwikibooks' => true,
	'guwikiquote' => true,
	'guwikisource' => true,
	'guwiktionary' => true,
	'hiwiki' => true,
	'hiwikibooks' => true,
	'hiwikiquote' => true,
	'hiwiktionary' => true,
	'knwiki' => true,
	'knwikibooks' => true,
	'knwikiquote' => true,
	'knwikisource' => true,
	'knwiktionary' => true,
	'mrwiki' => true,
	'mrwikibooks' => true,
	'mrwikiquote' => true,
	'mrwikisource' => true, // Bug 35426
	'mrwiktionary' => true,
	'mywiki' => true, // Bug 34817
	'mywiktionary' => true,
	'newiki' => true,
	'newikibooks' => true,
	'newiktionary' => true,
	'orwiki' => true,
	'orwiktionary' => true,
	'pawiki' => true,
	'pawikibooks' => true,
	'pawiktionary' => true,
	'sawiki' => true,
	'sawikibooks' => true,
	'sawiktionary' => true,
	'sawikisource' => true, // Bug 34159
	'tewiki' => true,
	'tewikibooks' => true,
	'tewikiquote' => true,
	'tewikisource' => true,
	'tewiktionary' => true,
),
'wmgWebFontsEnabledByDefault' => array(
	'default' => true,

),
'wmgUseGoogleNewsSitemap' => array(
	'default' => false,
	'wikinews' => true,
	'testwiki' => true,
),
'wmgGNSMfallbackCategory' => array(
	'default' => 'Published',
	'elwikinews' => 'Δημοσιευμένα',
	'fawikinews' => 'منتشرشده',
),
'wmgGNSMcommentNamespace' => array(
	'default' => true,
	'elwikinews' => true,
	'fawikinews' => 102,
),

'wmgUseCLDR' => array(
	'default' => true,
	'testwiki' => true,
),

'wgParserCacheType' => array(
	# To disable the MySQL parser cache, uncomment the following line,
	# and comment out the one after that
	#'default' => CACHE_MEMCACHED,
	'default' => 'mysql-multiwrite',
),

'wgLanguageConverterCacheType' => array(
	'default' => CACHE_ACCEL,
),

'wmgPFEnableStringFunctions' => array(
	'default' => false,
	'donatewiki' => true,
),

'wgUploadMissingFileUrl' => array(
	'default' => false,
	'incubatorwiki' => '//commons.wikimedia.org/wiki/Special:Upload',
	'nlwiki' => '//commons.wikimedia.org/wiki/Special:Upload?uselang=nl',
	'nlwikisource' => '//commons.wikimedia.org/wiki/Special:Upload?uselang=nl',
	'nlwikiquote' => '//commons.wikimedia.org/wiki/Special:Upload?uselang=nl',
	'nlwiktionary' => '//commons.wikimedia.org/wiki/Special:Upload?uselang=nl',
	'ptwikibooks' => '//commons.wikimedia.org/wiki/Special:Upload/pt?uselang=pt',
),

// DO NOT DISABLE WITHOUT CONTACTING PHILIPPE / LEGAL!
// Installed by Andrew, 2011-04-26
'wmgUseDisableAccount' => array(
	'default' => false,
	'arbcom_enwiki' => true,
	'checkuserwiki' => true,
	'stewardwiki' => true,
),

'wmgCheckUserForceSummary' => array(
	'default' => false,
	'enwiki' => true,
),

'wmgUseIncubator' => array(
	'default' => false,
	'incubatorwiki' => true,
),

'wmgUseWikiLove' => array(
	'default' => false,
	'arwiki' => true,
	'commonswiki' => true,
	'enwiki' => true,
	'fawiki' => true, // bug 33541
	'fawiktionary' => true, // bug 33541
	'hewiki' => true,
	'hiwiki' => true,
	'huwiki' => true, // bug 30417
	'incubatorwiki' => true, // bug 31209
	'iswiki' => true, // Bug 43760
	'itwikiquote' => true, // bug 40628
	'itwikivoyage' => true, // bug 43274
	'jawiki' => true, // bug 32312
	'mediawikiwiki' => true,
	'mkwiki' => true, // bug 31831
	'mlwiki' => true, // bug 30500
	'nowiki' => true, // bug 30794
	'officewiki' => true,
	'orwiki' => true, // bug 31172
	'ptwiki' => true, // bug 31178
	'sewikimedia' => true, // bug 30491
	'siwiki' => true, // bug 33485
	'srwiki' => true, // bug 35913
	'svwiki' => true, // bug 30756
	'svwikinews' => true, // bug 31754
	'testwiki' => true,
	'trwiki' => true, // bug 38227
	'urwiki' => true, // bug 40848
	'zhwiki' => true, // bug 30362
),
'wmgWikiLoveDefault' => array(
	'default' => true,
),

'wmgUseEditPageTracking' => array(
	'default' => false,
	'testwiki' => true,
	'enwiki' => true,

),
'wmgUseMath' => array(
	'default' => true, // moved from MW core
),

'wmgUseMarkAsHelpful' => array(
	'default' => false,
	'testwiki' => true,
	'enwiki' => true,
	# 'incubatorwiki' => true,
	# 'nlwiki' => true,
	# 'sewikimedia' => true,
),

'wmgUseMoodBar' => array(
	'default' => false,
	'testwiki' => true,
	'frwikisource' => true, // bug 34618
	'incubatorwiki' => true, // bug 32417
	'itwikivoyage' => true, // bug 43979
	'nlwiki' => true, // bug 32202
	'sewikimedia' => true, // bug 32757
//	'tawiki' => true, // bug 34560 // Disable due to bug 34615
),
'wmgMoodBarInfoUrl' => array(
	'default' => '//www.mediawiki.org/wiki/MoodBar',
	'frwikisource' => '//fr.wikisource.org/wiki/Wikisource:MoodBar',
	'itwikivoyage' => '//it.wikivoyage.org/wiki/Wikivoyage:MoodBar',
	'nlwiki' => '//nl.wikipedia.org/wiki/Help:Feedback',
),
'wmgMoodBarCutoffTime' => array(
	'default' => '20110725221004',
	'frwikisource' => '20110304202000',
),
'wmgMoodBarEnableTooltip' => array(
	'default' => true,
	'testwiki' => false,
	'test2wiki' => false,
),
'wmgMobileFrontend' => array(
	'default' => true,
	'wikivoyage' => true, // In testing prior to mobile domain setup
	'wikidatawiki' => false, // Disabled due to lack of mobile domain setup
),

'wmgZeroRatedMobileAccess' => array(
	'default' => true,
),

'wmgUseSubPageList3' => array(
	'default' => false,
	'testwiki' => true,
	'wikiversity' => true,
	'cswiktionary' => true, // bug 38903
),

'wmgShowHiddenCats' => array(
	'default' => false,
	'commonswiki' => true,
),

'wmgMobileFrontendLogo' => array(
	'default' => '//upload.wikimedia.org/wikipedia/commons/8/84/W_logo_for_Mobile_Frontend.gif',
	'wikinews' => '//upload.wikimedia.org/wikipedia/commons/thumb/2/24/Wikinews-logo.svg/35px-Wikinews-logo.svg.png',
	'wiktionary' => '//upload.wikimedia.org/wikipedia/commons/thumb/b/b4/Wiktionary-logo-en.png/35px-Wiktionary-logo-en.png',
	'wikibooks' => '//upload.wikimedia.org/wikipedia/commons/thumb/f/fa/Wikibooks-logo.svg/35px-Wikibooks-logo.svg.png',
	'wikiversity' => '//upload.wikimedia.org/wikipedia/commons/thumb/9/91/Wikiversity-logo.svg/35px-Wikiversity-logo.svg.png',
	'specieswiki' => '//upload.wikimedia.org/wikipedia/commons/thumb/4/4f/Wikispecies-logo.png/35px-Wikispecies-logo.png',
	'commonswiki' => '//upload.wikimedia.org/wikipedia/commons/4/42/22x35px-Commons-logo.png',
	'wikiquote' => '//upload.wikimedia.org/wikipedia/commons/thumb/f/fa/Wikiquote-logo.svg/35px-Wikiquote-logo.svg.png',
	'metawiki' => '//upload.wikimedia.org/wikipedia/commons/2/23/22x35px-Wikimedia_Community_Logo.png',
	'foundationwiki' => '//upload.wikimedia.org/wikipedia/commons/8/81/35x22px-Wikimedia-logo.png',
	'wikisource' => '//upload.wikimedia.org/wikipedia/commons/thumb/f/fa/Wikisource-logo.svg/35px-Wikisource-logo.svg.png',
),
'wmgMobileUrlTemplate' => array(
	'default' => '%h0.m.%h1.%h2',
	'foundationwiki' => '',
	'commonswiki' => '',
	'mediawikiwiki' => 'm.%h1.%h2',
	'test2wiki' => '',
),

'wmgZeroDisableImages' => array(
		'default' => 1,
),

'wmgMFRemovableClasses' => array(
	'default' => array( 'table.metadata',
			   '.metadata mbox-small',
			   '.metadata plainlinks ambox ambox-content',
			   '.metadata plainlinks ambox ambox-move',
			   '.metadata plainlinks ambox ambox-style', ),
),
'wmgMFCustomLogos' => array(
	'default' => array(
		'site' => 'wikipedia',
		'logo' => '//upload.wikimedia.org/wikipedia/commons/5/54/Mobile_W_beta_light.png'
	),
	'enwiki' => array(
		'site' => 'wikipedia',
		'logo' => '//upload.wikimedia.org/wikipedia/commons/5/54/Mobile_W_beta_light.png',
		'copyright' => '{wgExtensionAssetsPath}/MobileFrontend/stylesheets/common/images/logo-copyright-en.png'
	),
	'testwiki' => array(
		'site' => 'wikipedia',
		'logo' => '//upload.wikimedia.org/wikipedia/commons/5/54/Mobile_W_beta_light.png',
		// {wgExtensionAssetsPath} will get replaced with $wgExtensionAssetsPath in CustomSettings.php
		'copyright' => '{wgExtensionAssetsPath}/MobileFrontend/stylesheets/common/images/logo-copyright-en.png'
	),
),
'wmgMFEnableDesktopResources' => array(
	'default' => true,
),
'wmgMFPhotoUploadEndpoint' => array(
	'default' => '//commons.wikimedia.org/w/api.php',
	'test2wiki' => '',
),
'wmgMFForceSecureLogin' => array(
	'default' => true,
),
'wmgMFNearby' => array(
	'default' => true, // Does not gets enabled on wikis w/o GeoData anyway
),

'wgExtraGenderNamespaces' => array(
	'default' => array(),
	'cswiki' => array(
		NS_USER => array( 'male' => 'Wikipedista', 'female' => 'Wikipedistka' ),
		NS_USER_TALK => array( 'male' => 'Diskuse_s_wikipedistou', 'female' => 'Diskuse_s_wikipedistkou' ),
	),
	'plwiki' => array(
		NS_USER => array( 'male' => 'Wikipedysta', 'female' => 'Wikipedystka' ),
		NS_USER_TALK => array( 'male' => 'Dyskusja_wikipedysty', 'female' => 'Dyskusja_wikipedystki' )
	),
	'plwikinews' => array(
		NS_USER => array( 'male' => 'Wikireporter', 'female' => 'Wikireporterka' ),
		NS_USER_TALK => array( 'male' => 'Dyskusja_wikireportera', 'female' => 'Dyskusja_wikireporterki' ),
	),
	'plwiktionary' => array(
		NS_USER => array( 'male' => 'Wikipedysta', 'female' => 'Wikipedystka' ),
		NS_USER_TALK => array( 'male' => 'Dyskusja_wikipedysty', 'female' => 'Dyskusja_wikipedystki' ),
	),
	'ptwiki' => array(
		NS_USER => array( 'male' => 'Usuário', 'female' => 'Usuária' ),
		NS_USER_TALK => array( 'male' => 'Usuário_Discussão', 'female' => 'Usuária_Discussão' ),
	),
	'test2wiki' => array( NS_USER => array( 'male' => 'Male_user', 'female' => 'Female_user' ) ),
),

'wmgUseBabel' => array(
	'default' => true,
),
'wmgBabelCategoryNames' => array(
	'default' => array(
		'0' => false,
		'1' => false,
		'2' => false,
		'3' => false,
		'4' => false,
		'5' => false,
		'N' => false,
	),
	'cawiki' => array(
		'0' => false,
		'1' => 'Usuaris %code%-1',
		'2' => 'Usuaris %code%-2',
		'3' => 'Usuaris %code%-3',
		'4' => 'Usuaris %code%-4',
		'5' => false,
		'N' => 'Usuaris %code%-N',
	),
	'commonswiki' => array(
		'0' => 'User %code%-0',
		'1' => 'User %code%-1',
		'2' => 'User %code%-2',
		'3' => 'User %code%-3',
		'4' => 'User %code%-4',
		'5' => false,
		'N' => 'User %code%-N',
	),
	'dewiki' => array(
		'0' => false,
		'1' => 'User %code%-1',
		'2' => 'User %code%-2',
		'3' => 'User %code%-3',
		'4' => 'User %code%-4',
		'5' => false,
		'N' => 'User %code%-M',
	),
	'enwiki' => array(
		'0' => false,
		'1' => 'User %code%-1',
		'2' => 'User %code%-2',
		'3' => 'User %code%-3',
		'4' => 'User %code%-4',
		'5' => 'User %code%-5',
		'N' => 'User %code%-N',
	),
	'enwiktionary' => array(
		'0' => false,
		'1' => 'User %code%-1',
		'2' => 'User %code%-2',
		'3' => 'User %code%-3',
		'4' => 'User %code%-4',
		'5' => false,
		'N' => 'User %code%-N',
	),
	'enwikiquote' => array(
		'0' => false,
		'1' => 'User %code%-1',
		'2' => 'User %code%-2',
		'3' => 'User %code%-3',
		'4' => 'User %code%-4',
		'5' => 'User %code%-5',
		'N' => 'User %code%-N',
	),
	'enwikisource' => array(
		'0' => false,
		'1' => 'User %code%-1',
		'2' => 'User %code%-2',
		'3' => 'User %code%-3',
		'4' => 'User %code%-4',
		'5' => false,
		'N' => 'User %code%-N',
	),
	'enwikinews' => array(
		'0' => false,
		'1' => 'User %code%-1',
		'2' => 'User %code%-2',
		'3' => 'User %code%-3',
		'4' => 'User %code%-4',
		'5' => false,
		'N' => 'User %code%-N',
	),
	'enwikiversity' => array(
		'0' => 'User %code%-0',
		'1' => 'User %code%-1',
		'2' => 'User %code%-2',
		'3' => 'User %code%-3',
		'4' => 'User %code%-4',
		'5' => false,
		'N' => 'User %code%-N',
	),
	'eswiki' => array(
		'0' => false,
		'1' => 'Wikipedia:Wikipedistas con nivel básico de %wikiname%',
		'2' => 'Wikipedia:Wikipedistas con nivel intermedio de %wikiname%',
		'3' => 'Wikipedia:Wikipedistas con nivel avanzado de %wikiname%',
		'4' => 'Wikipedia:Wikipedistas con nivel experto de %wikiname%',
		'5' => 'Wikipedia:Wikipedistas con nivel profesional de %wikiname%',
		'N' => 'Wikipedia:Wikipedistas con nivel nativo de %wikiname%',
	),
	'eswikibooks' => array(
		'0' => false,
		'1' => false,
		'2' => false,
		'3' => false,
		'4' => false,
		'5' => false,
		'N' => false,
	),
	'fiwiki' => array(
		'0' => false,
		'1' => 'User %code%-1',
		'2' => 'User %code%-2',
		'3' => 'User %code%-3',
		'4' => 'User %code%-4',
		'5' => false,
		'N' => 'User %code%-N',
	),
	'fowiki' => array(
		'0' => false,
		'1' => 'Brúkari %code%-1',
		'2' => 'Brúkari %code%-2',
		'3' => 'Brúkari %code%-3',
		'4' => 'Brúkari %code%-4',
		'5' => 'Brúkari %code%-5',
		'N' => 'Brúkari %code%-N',
	), // Bug 37401
	'frwiki' => array(
		'0' => 'Utilisateur %code%-0',
		'1' => 'Utilisateur %code%-1',
		'2' => 'Utilisateur %code%-2',
		'3' => 'Utilisateur %code%-3',
		'4' => 'Utilisateur %code%-4',
		'5' => false,
		'N' => 'Utilisateur %code%-M',
	),
	'frwikiquote' => array( // Bug 39472
		'0' => 'Utilisateur %code%-0',
		'1' => 'Utilisateur %code%-1',
		'2' => 'Utilisateur %code%-2',
		'3' => 'Utilisateur %code%-3',
		'4' => 'Utilisateur %code%-4',
		'5' => 'Utilisateur %code%-5',
		'N' => 'Utilisateur %code%-M',
	),
	'frwikisource' => array(
		'0' => 'Utilisateurs %code%-0',
		'1' => 'Utilisateurs %code%-1',
		'2' => 'Utilisateurs %code%-2',
		'3' => 'Utilisateurs %code%-3',
		'4' => 'Utilisateurs %code%-4',
		'5' => false,
		'N' => 'Utilisateurs %code%-M',
	),
	'frwiktionary' => array(
		'0' => 'Utilisateurs %code%-0',
		'1' => 'Utilisateurs %code%-1',
		'2' => 'Utilisateurs %code%-2',
		'3' => 'Utilisateurs %code%-3',
		'4' => 'Utilisateurs %code%-4',
		'5' => false,
		'N' => 'Utilisateurs %code%-M',
	),
	'ilowiki' => array( // Bug 37981
		'0' => false,
		'1' => 'Agar-aramat %code%-1',
		'2' => 'Agar-aramat %code%-2',
		'3' => 'Agar-aramat %code%-3',
		'4' => 'Agar-aramat %code%-4',
		'5' => 'Agar-aramat %code%-5',
		'N' => 'Agar-aramat %code%-N',
	),
	'incubatorwiki' => array(
		'0' => 'Users:By language:%code%-0',
		'1' => 'Users:By language:%code%-1',
		'2' => 'Users:By language:%code%-2',
		'3' => 'Users:By language:%code%-3',
		'4' => 'Users:By language:%code%-4',
		'5' => 'Users:By language:%code%-5',
		'N' => 'Users:By language:%code%-N',
	),
	'iswiki' => array(
		'0' => 'Notandi %code%-0',
		'1' => 'Notandi %code%-1',
		'2' => 'Notandi %code%-2',
		'3' => 'Notandi %code%-3',
		'4' => 'Notandi %code%-4',
		'5' => 'Notandi %code%-5',
		'N' => 'Notandi %code%-M',
	),
	'itwiki' => array(
		'0' => 'Utenti %code%-0',
		'1' => 'Utenti %code%-1',
		'2' => 'Utenti %code%-2',
		'3' => 'Utenti %code%-3',
		'4' => 'Utenti %code%-4',
		'5' => false,
		'N' => 'Utenti %code%-M',
	),
	'itwiktionary' => array(
		'0' => 'Utenti %code%-0',
		'1' => 'Utenti %code%-1',
		'2' => 'Utenti %code%-2',
		'3' => 'Utenti %code%-3',
		'4' => 'Utenti %code%-4',
		'5' => false,
		'N' => 'Utenti %code%-M',
	),
	'itwikibooks' => array(
		'0' => 'Utenti %code%-0',
		'1' => 'Utenti %code%-1',
		'2' => 'Utenti %code%-2',
		'3' => 'Utenti %code%-3',
		'4' => 'Utenti %code%-4',
		'5' => false,
		'N' => 'Utenti %code%-M',
	),
	'itwikinews' => array(
		'0' => 'Utenti %code%-0',
		'1' => 'Utenti %code%-1',
		'2' => 'Utenti %code%-2',
		'3' => 'Utenti %code%-3',
		'4' => 'Utenti %code%-4',
		'5' => false,
		'N' => 'Utenti %code%-M',
	),
	'itwikiquote' => array(
		'0' => 'Utenti %code%-0',
		'1' => 'Utenti %code%-1',
		'2' => 'Utenti %code%-2',
		'3' => 'Utenti %code%-3',
		'4' => 'Utenti %code%-4',
		'5' => false,
		'N' => 'Utenti %code%-M',
	),
	'itwikisource' => array(
		'0' => 'Utenti %code%-0',
		'1' => 'Utenti %code%-1',
		'2' => 'Utenti %code%-2',
		'3' => 'Utenti %code%-3',
		'4' => 'Utenti %code%-4',
		'5' => false,
		'N' => 'Utenti %code%-M',
	),
	'itwikiversity' => array(
		'0' => 'Utenti %code%-0',
		'1' => 'Utenti %code%-1',
		'2' => 'Utenti %code%-2',
		'3' => 'Utenti %code%-3',
		'4' => 'Utenti %code%-4',
		'5' => false,
		'N' => 'Utenti %code%-M',
	),
	'itwikivoyage' => array(
		'0' => 'Utenti %code%-0',
		'1' => 'Utenti %code%-1',
		'2' => 'Utenti %code%-2',
		'3' => 'Utenti %code%-3',
		'4' => 'Utenti %code%-4',
		'5' => false,
		'N' => 'Utenti %code%-M',
	),
	'jawiki' => array(
		'0' => false,
		'1' => 'User %code%-1',
		'2' => 'User %code%-2',
		'3' => 'User %code%-3',
		'4' => 'User %code%-4',
		'5' => false,
		'N' => 'User %code%-N',
	),
	'lawiki' => array(
		'0' => false,
		'1' => 'Usores %code%-1',
		'2' => 'Usores %code%-2',
		'3' => 'Usores %code%-3',
		'4' => 'Usores %code%-4',
		'5' => false,
		'N' => 'Usores %code%-N',
	),
	'nlwiki' => array(
		'0' => false,
		'1' => 'Wikipedia:Gebruiker %code%-1',
		'2' => 'Wikipedia:Gebruiker %code%-2',
		'3' => 'Wikipedia:Gebruiker %code%-3',
		'4' => 'Wikipedia:Gebruiker %code%-4',
		'5' => false,
		'N' => 'Wikipedia:Gebruiker %code%-M',
	),
	'nowiki' => array(
		'0' => 'Bruker %code%-0',
		'1' => 'Bruker %code%-1',
		'2' => 'Bruker %code%-2',
		'3' => 'Bruker %code%-3',
		'4' => 'Bruker %code%-4',
		'5' => false,
		'N' => 'Bruker %code%-N',
	),
	'oswiki' => array(
		'0' => 'User %code%-0',
		'1' => 'User %code%-1',
		'2' => 'User %code%-2',
		'3' => 'User %code%-3',
		'4' => 'User %code%-4',
		'5' => 'User %code%-5',
		'N' => 'User %code%-N',
	),
	'plwiki' => array(
		'0' => false,
		'1' => 'User %code%-1',
		'2' => 'User %code%-2',
		'3' => 'User %code%-3',
		'4' => 'User %code%-4',
		'5' => false,
		'N' => 'User %code%-N',
	),
	'ptwiki' => array(
		'0' => false,
		'1' => 'Usuário %code%-1',
		'2' => 'Usuário %code%-2',
		'3' => 'Usuário %code%-3',
		'4' => false,
		'5' => false,
		'N' => 'Usuário %code%',
	),
	'ptwiktionary' => array(
		'0' => '!Usuário %code%-0',
		'1' => '!Usuário %code%-1',
		'2' => '!Usuário %code%-2',
		'3' => '!Usuário %code%-3',
		'4' => '!Usuário %code%-4',
		'5' => '!Usuário %code%-5',
		'N' => '!Usuário %code%-M',
	),
	'ruwiki' => array(
		'0' => 'User %code%-0',
		'1' => 'User %code%-1',
		'2' => 'User %code%-2',
		'3' => 'User %code%-3',
		'4' => 'User %code%-4',
		'5' => 'User %code%-5',
		'N' => 'User %code%-N',
	),
	'specieswiki' => array(
		'0' => false,
		'1' => 'User %code%-1',
		'2' => 'User %code%-2',
		'3' => 'User %code%-3',
		'4' => 'User %code%-4',
		'5' => false,
		'N' => 'User %code%-N',
	),
	'svwiki' => array(
		'0' => 'Användare %code%-0',
		'1' => 'Användare %code%-1',
		'2' => 'Användare %code%-2',
		'3' => 'Användare %code%-3',
		'4' => 'Användare %code%-4',
		'5' => false,
		'N' => 'Användare %code%-N',
	),
	'svwiktionary' => array( // Bug 42288
		'0' => 'Wiktionary:Användare %code%-0',
		'1' => 'Wiktionary:Användare %code%-1',
		'2' => 'Wiktionary:Användare %code%-2',
		'3' => 'Wiktionary:Användare %code%-3',
		'4' => 'Wiktionary:Användare %code%-4',
		'5' => false,
		'N' => 'Wiktionary:Användare %code%-N',
	),
	'tlwiki' => array(
		'0' => false,
		'1' => 'User %code%-1',
		'2' => 'User %code%-2',
		'3' => 'User %code%-3',
		'4' => 'User %code%-4',
		'5' => 'User %code%-5',
		'N' => 'User %code%-N',
	),
	'ukwiki' => array(
		'0' => 'User %code%-0',
		'1' => 'User %code%-1',
		'2' => 'User %code%-2',
		'3' => 'User %code%-3',
		'4' => 'User %code%-4',
		'5' => false,
		'N' => 'User %code%-N',
	),
	'vowiktionary' => array(
		'0' => 'Geban %code%-0',
		'1' => 'Geban %code%-1',
		'2' => 'Geban %code%-2',
		'3' => 'Geban %code%-3',
		'4' => 'Geban %code%-4',
		'5' => 'Geban %code%-5',
		'N' => 'Geban %code%-N',
	),
	'zhwiktionary' => array(
		'0' => false,
		'1' => '%wikiname%(初级)使用者',
		'2' => '%wikiname%(中级)使用者',
		'3' => '%wikiname%(高级)使用者',
		'4' => '%wikiname%(近母语)使用者',
		'5' => false,
		'N' => '%wikiname%(母语)使用者',
	),
	'zhwiki' => array(
		'0' => '%code%-0_使用者',
		'1' => '%code%-1_使用者',
		'2' => '%code%-2_使用者',
		'3' => '%code%-3_使用者',
		'4' => '%code%-4_使用者',
		'5' => false,
		'N' => '%code%_母语使用者',
	),
	'zhwikibooks' => array(
		'0' => '%code%-0_使用者',
		'1' => '%code%-1_使用者',
		'2' => '%code%-2_使用者',
		'3' => '%code%-3_使用者',
		'4' => '%code%-4_使用者',
		'5' => false,
		'N' => '%code%_母语使用者',
	),
	'zhwikinews' => array(
		'0' => '%code%-0_使用者',
		'1' => '%code%-1_使用者',
		'2' => '%code%-2_使用者',
		'3' => '%code%-3_使用者',
		'4' => '%code%-4_使用者',
		'5' => false,
		'N' => '%code%_母语使用者',
	),
	'zhwikiquote' => array(
		'0' => '%code%-0_使用者',
		'1' => '%code%-1_使用者',
		'2' => '%code%-2_使用者',
		'3' => '%code%-3_使用者',
		'4' => '%code%-4_使用者',
		'5' => false,
		'N' => '%code%_母语使用者',
	),
	'zhwikiquote' => array(
		'0' => '%code%-0_使用者',
		'1' => '%code%-1_使用者',
		'2' => '%code%-2_使用者',
		'3' => '%code%-3_使用者',
		'4' => '%code%-4_使用者',
		'5' => false,
		'N' => '%code%_母语使用者',
	),
	'zhwikisource' => array(
		'0' => '%code%-0_使用者',
		'1' => '%code%-1_使用者',
		'2' => '%code%-2_使用者',
		'3' => '%code%-3_使用者',
		'4' => '%code%-4_使用者',
		'5' => false,
		'N' => '%code%_母语使用者',
	),

),
'wmgBabelMainCategory' => array(
	'default' => false,
	'cawiki' => 'Usuaris %code%',
	'checkuserwiki' => 'User %code%',
	'commonswiki' => 'User %code%',
	'enwiki' => 'User %code%',
	'enwikibooks' => 'User %code%',
	'enwiktionary' => 'User %code%',
	'enwikiquote' => 'User %code%',
	'enwikisource' => 'User %code%',
	'enwikinews' => 'User %code%',
	'enwikiversity' => 'User %code%',
	'eswiki' => 'Wikipedia:Wikipedistas con conocimientos de %wikiname%',
	'fiwiki' => 'User %code%',
	'fowiki' => 'Brúkari %code%', // Bug 37401
	'frwiki' => 'Utilisateur %code%',
	'frwikiquote' => 'Utilisateur %code%', // Bug 39472
	'frwikisource' => 'Utilisateurs %code%',
	'frwiktionary' => 'Utilisateurs %code%',
	'ilowiki' => 'Agar-aramat %code%', // Bug 37981
	'incubatorwiki' => 'Users:By language:%code%',
	'iswiki' => 'Notandi %code%',
	'itwiki' => 'Utenti %code%',
	'itwiktionary' => 'Utenti %code%',
	'itwikinews' => 'Utenti %code%',
	'itwikibooks' => 'Utenti %code%',
	'itwikiquote' => 'Utenti %code%',
	'itwikisource' => 'Utenti %code%',
	'itwikiversity' => 'Utenti %code%',
	'itwikivoyage' => 'Utenti %code%',
	'jawiki' => 'User %code%',
	'lawiki' => 'Usores %code%',
	'metawiki' => 'User %code%',
	'nowiki' => 'Bruker %code%',
	'oswiki' => 'User %code%',
	'otrs_wikiwiki' => 'User %code%',
	'plwiki' => 'User %code%',
	'ptwiki' => 'Usuários %code%',
	'ruwiki' => 'User %code%',
	'specieswiki' => 'User %code%',
	'svwiki' => 'Användare %code%',
	'svwiktionary' => 'Wiktionary:Användare %code%', // Bug 42288
	'tlwiki' => 'User %code%',
	'ukwiki' => 'User %code%',
	'wikidatawiki' => 'User %code%',
	'zhwiktionary' => '%wikiname%使用者',
	'zhwiki' => '%code%_使用者',
	'zhwikibooks' => '%code%_使用者',
	'zhwikinews' => '%code%_使用者',
	'zhwikiquote' => '%code%_使用者',
	'zhwikisource' => '%code%_使用者',
),
'wmgBabelDefaultLevel' => array(
	'default' => 'N',
),
'wmgBabelUseUserLanguage' => array(
	'default' => false,
	'commonswiki' => true,
	'incubatorwiki' => true,
),

'wmgUseTranslate' => array(
	'default' => false,
	'bewikimedia' => true, // bug 37391
	'brwikimedia' => true, // bug 44054
	'incubatorwiki' => true, // bug 34213
	'mediawikiwiki' => true,
	'metawiki' => true,
	'outreachwiki' => true,
	'testwiki' => true,
	'wikidatawiki' => true, // bug 41585
	'wikimania2012wiki' => true, // bug 34120
	'wikimania2013wiki' => true, // bug 36477
),
'wmgTranslateWorkflowStates' => array(
	'default' => false,
	'metawiki' => array(
		'progress' => array( 'color' => 'E00' ),
		'needs-updating' => array( 'color' => 'FFBF00' ),
		'proofreading' => array( 'color' => 'FFBF00' ),
		'ready' => array( 'color' => 'FF0' ),
		'published' => array( 'color' => 'AEA' ),
	),
	'wikidatawiki' => array(
		'progress' => array( 'color' => 'E00' ),
		'needs-updating' => array( 'color' => 'FFBF00' ),
		'updating' => array( 'color' => 'FFBF00' ),
		'proofreading' => array( 'color' => 'FFBF00' ),
		'ready' => array( 'color' => 'FF0' ),
		'published' => array( 'color' => 'AEA' ),
	),
),

'wmgTranslateRcFilterDefault' => array(
	'default' => 'filter',
	'wikidatawiki' => 'noaction',
),

'wmgUseTranslationMemory' => array(
	'default' => true,
	'testwiki' => true,
),
'wmgUseTranslationNotifications' => array(
	'default' => false,
	'testwiki' => true,
	'brwikimedia' => true, // Bug 44054
	'incubatorwiki' => true,
	'mediawikiwiki' => true,
	'metawiki' => true,
	'wikidatawiki' => true, // bug 41585
	'wikimania2012wiki' => true,
	'wikimania2013wiki' => true,
),

'wmgUseVipsTest' => array(
	'default' => false,
	'test2wiki' => true,
),

'wmgUseApiSandbox' => array(
	'default' => true,
),

'wmgUseShortUrl' => array(
	'default' => false,
	'hiwiki' => true,
	'orwiki' => true,
	'tawiki' => true,
	'tawikibooks' => true,
	'tawikinews' => true,
	'tawikiquote' => true,
	'tawikisource' => true,
	'tawiktionary' => true,
	'testwiki' => true,
	'test2wiki' => true,
),

'wmgUseFeaturedFeeds' => array(
	'default' => true,
),
'wmgFeaturedFeedsDefaults' => array(
	'default' => array(
		'limit' => 10,
		'inUserLanguage' => false,
	),
	'frwikisource' => array( // Bug 39919
		'limit' => 70,
		'inUserLanguage' => false,
	),
),
'wmgDisplayFeedsInSidebar' => array(
	'default' => false,
	'testwiki' => true,
),

'wmgReduceStartupExpiry' => array(
	'default' => false,
),

'wmgMemoryLimit' => array(
	'default' => 175 * 1024 * 1024, // 175MB

	# 200MB - Extra for zh wikis for converter tables
	'zhwiki' => 200 * 1024 * 1024,
	'zhwikibooks' => 200 * 1024 * 1024,
	'zhwikinews' => 200 * 1024 * 1024,
	'zhwikiquote' => 200 * 1024 * 1024,
	'zhwikisource' => 200 * 1024 * 1024,
	'zhwiktionary' => 200 * 1024 * 1024,
),
'wgMaxGeneratedPPNodeCount' => array(
	'default' => 1500000,
),

'wgEnableJavaScriptTest' => array(
	'default' => false,
	'test2wiki' => true,
),

'wmgEnablePageTriage' => array(
	'default' => false,
	'testwiki' => true,
	'test2wiki' => true,
	'enwiki' => true,
),
'wmgPageTriageEnableCurationToolbar' => array(
	'default' => false,
	'testwiki' => true,
	'test2wiki' => true,
	'enwiki' => true,
),

'wmgEnableInterwiki' => array(
	'default' => true,
),

'wmgEnableRandomRootPage' => array(
	'default' => true,
	'wiki' => false,
),

'wmgUseLastModified' => array(
	'default' => false,
//	'testwiki' => true,
//	'enwiki' => true,
),

'wmgUseE3Experiments' => array(
	'default' => false,
	'testwiki' => true,
	'test2wiki' => true,
	'enwiki' => true,
),

'wmgUseGettingStarted' => array(
	'default' => false,
	'testwiki' => true,
	'test2wiki' => true,
	'enwiki' => true,
),
'wmgUseGuidedTour' => array(
	'default' => false,
	'testwiki' => true,
	'test2wiki' => true,
	'enwiki' => true,
),
'wmgUseEducationProgram' => array(
	'default' => false,
	'test2wiki' => true,
	'enwiki' => true,
),

'wmgUseWikimediaShopLink' => array(
	'default' => false,
	'testwiki' => true,
	'test2wiki' => true,
	'enwiki' => true,
),

# *** DO NOT disable this extension if it causes an overload via API ***
# or you will break a lot of pages where its parser function is used. Instead,
# disable its API modules in CommonSettings.php or below
'wmgEnableGeoData' => array(
	'default' => false,
	'wiki' => true,
	'wikivoyage' => true,
),
'wmgEnableGeoSearch' => array(
	'default' => true,
),
'wmgGeoDataUpdatesViaJob' => array(
	'default' => false,
),

'wmgUseEcho' => array(
	'default' => false,
	'testwiki' => true,
	'test2wiki' => true,
	'mediawikiwiki' => true,
),
'wmgEchoEnableEmailBatch' => array(
	'default' => true,
),
'wmgEchoEmailFooterAddress' => array(
	'default' => 'Wikimedia Foundation, 149 New Montgomery St., 3rd Fl., San Francisco, CA 94105.',
),

'wmgUseScribunto' => array(
	'default' => false,
	//'testwiki' => true,
	'test2wiki' => true,
	'mediawikiwiki' => true,
),

'wmgUseSubpageSortkey' => array(
	'default' => false,
	'enwikibooks' => true,
	'ptwikibooks' => true,
),
'wmgSubpageSortkeyByNamespace' => array(
	'default' => array(),
	'enwikibooks' => array( // Bug 22911
		NS_MAIN => '1..',
		102 => '1..', // Cookbook
		110 => '1..', // Wikijunior
	),
	'ptwikibooks' => array( // Bug 22911
		NS_MAIN => '1..',
	),
),

'wmgUseCORS' => array(
	'default' => true,
	'private' => false,
),

'wmgUseMicroDesign' => array(
	'default' => false,
	'testwiki' => true,
	'enwiki' => true,
	'itwikivoyage' => true,
	'mlwiki' => true, // Bug 44634
),

'wmgEnablePostEdit' => array(
	'default' => false,
	'testwiki' => true,
	'test2wiki' => true,
	'astwiki' => true,
	'azwiki' => true,
	'be_x_oldwiki' => true,
	'bgwiki' => true,
	'dewiki' => true,
	'dsbwiki' => true,
	'enwiki' => true,
	'eowiki' => true,
	'eswiki' => true,
	'fiwiki' => true,
	'frwiki' => true,
	'glwiki' => true,
	'hewiki' => true,
	'hsbwiki' => true,
	'idwiki' => true,
	'itwiki' => true,
	'itwikivoyage' => true, // Bug 43273
	'jawiki' => true,
	'kowiki' => true,
	'lbwiki' => true,
	'mkwiki' => true,
	'mlwiki' => true, // Bug 43532
	'mswiki' => true,
	'nlwiki' => true,
	'plwiki' => true,
	'pmswiki' => true,
	'ptwiki' => true,
	'siwiki' => true,
	'svwiki' => true,
	'tewiki' => true,
	'trwiki' => true,
	'ukwiki' => true,
	'viwiki' => true,
),

'wmgDisableUnmergedEdits' => array(
	'default' => false,
	'wikivoyage' => false,
),

// DO NOT USE. Not currently branched
'wmgUseReplaceText' => array(
	'default' => false,
	//'wikivoyage' => true,
),

'wmgUseGeoCrumbs' => array(
	'default' => false,
	'incubator' => true, // bug 44725
	'wikivoyage' => true,
),

'wmgUseCalendar' => array(
	'default' => false,
	'wikivoyage' => true,
),

'wmgUseMapSources' => array(
	'default' => false,
	'wikivoyage' => true,
),

'wmgUseSlippyMap' => array(
	'default' => false,
	'wikivoyage' => false,
),

'wmgUseCreditsSource' => array(
	'default' => false,
	'wikivoyage' => true,
),

'wgMaxCredits' => array(
	'default' => 0,
	'wikivoyage' => 10,
),

'wmgUseListings' => array(
	'default' => false,
	'wikivoyage' => true,
),

'wmgUseTocTree' => array(
	'default' => false,
	'wikivoyage' => true,
),

'wmgUseInsider' => array(
	'default' => false,
	'wikivoyage' => true,
),

'wmgUseRelatedArticles' => array(
	'default' => false,
	'wikivoyage' => true,
),

'wmgUseRelatedSites' => array(
	'default' => false,
	'wikivoyage' => true,
),

'wmgUseUserMerge' => array(
	'default' => false,
	'wikivoyage' => true,
),

'wmgUseEventLogging' => array(
	'default' => true,
),

'wmgRelatedSitesPrefixes' => array(
	'default' => array(),
	'wikivoyage' => array(
		'wikipedia',
		'wikitravel',
		'dmoz',
		'citizendium',
		'gen',
		'commons',
		'tech',
		'assoc',
		'wmc',
		'wtp'
	),
),

// @todo: settings for RelatedSites, might need to be revised ;)
'wmgExtraLanguageNames' => array(
	'default' => array(),
	'wikivoyage' => array(
		'wikitravel' => 'Wikitravel',
		'wikipedia' => 'Wikipedia',
		'WikiPedia' => 'Wikipedia',
		'citizendium' => 'Citizendium',
		'dmoz' => 'Open Directory',
		'Radreise-Wiki' => 'Radreise-Wiki',
		'rezepte' => 'Rezepte-Wiki',
		'commons' => 'Wikimedia Commons',
		'wmc' => 'Wikimedia Commons',
		'wtp' => 'Wikitravel Press',
		'gen' => 'General',
		'tech' => 'Technical',
		'assoc' => 'Association',
		'ldbwiki' => 'Location Database Wiki',
	),
),

'wgContentHandlerUseDB' => array(
	'default' => false,
	'wikidatawiki' => true,
),

'wmgUseUniversalLanguageSelector' => array(
	'default' => false,
	'bewikimedia' => true,
	'incubatorwiki' => true,
	'mediawikiwiki' => true,
	'metawiki' => true,
	'outreachwiki' => true,
	'testwiki' => true,
	'wikidatawiki' => true,
	'wikimania2012wiki' => true,
	'wikimania2013wiki' => true,
),

'wmgUseWikibaseRepo' => array(
	'default' => false,
	'wikidatawiki' => true,
),

'wmgUseWikibaseClient' => array(
	'default' => false,
	'test2wiki' => true,
	'hewiki' => true,
	'huwiki' => true,
	'itwiki' => true,
),

'wmgWikibaseClientSettings' => array(
	'default' => array(
		'namespaces' => array( NS_MAIN, NS_TALK, NS_USER, NS_USER_TALK, NS_PROJECT,
			NS_PROJECT_TALK, NS_FILE, NS_FILE_TALK, NS_MEDIAWIKI, NS_MEDIAWIKI_TALK,
			NS_TEMPLATE, NS_TEMPLATE_TALK, NS_HELP, NS_HELP_TALK, NS_CATEGORY, NS_CATEGORY_TALK
		),
	),
	'test2wiki' => array(
		'siteGlobalID' => 'enwiki'
	),
	'hewiki' => array(
		'namespaces' => array( NS_MAIN, NS_TALK, NS_USER, NS_USER_TALK, NS_PROJECT,
			NS_PROJECT_TALK, NS_FILE, NS_FILE_TALK, NS_MEDIAWIKI, NS_MEDIAWIKI_TALK,
			NS_TEMPLATE, NS_TEMPLATE_TALK, NS_HELP, NS_HELP_TALK, NS_CATEGORY,
			NS_CATEGORY_TALK, 100, 101, 108, 109 ),
		'sortPrepend' => array( 'en' ),
	),
	'huwiki' => array(
		'namespaces' => array( NS_MAIN, NS_TALK, NS_USER, NS_USER_TALK, NS_PROJECT,
			NS_PROJECT_TALK, NS_FILE, NS_FILE_TALK, NS_MEDIAWIKI, NS_MEDIAWIKI_TALK,
			NS_TEMPLATE, NS_TEMPLATE_TALK, NS_HELP, NS_HELP_TALK, NS_CATEGORY,
			NS_CATEGORY_TALK, 100, 101 ),
		'sortPrepend' => array( 'en' ),
	),
	'itwiki' => array(
		'namespaces' => array( NS_MAIN, NS_TALK, NS_USER, NS_USER_TALK, NS_PROJECT,
			NS_PROJECT_TALK, NS_FILE, NS_FILE_TALK, NS_MEDIAWIKI, NS_MEDIAWIKI_TALK,
			NS_TEMPLATE, NS_TEMPLATE_TALK, NS_HELP, NS_HELP_TALK, NS_CATEGORY,
			NS_CATEGORY_TALK, 100, 101, 102, 103 ),
	),
),

'wmgUseTemplateSandbox' => array(
	'default' => true,
),

'wmgUsePageImages' => array(
	'default' => true,
),

'wmgUseTorBlock' => array(
	'default' => true,
	'private' => false,
),

'wmgUseSearchExtraNS' => array(
	'default' => false,
),

'wmgUseSecurePoll' => array(
	'default' => false,
	'enwiki' => true,
	'metawiki' => true,
),

'wgUrlProtocols' => array(
	'default' => array(
		'http://',
		'https://',
		'ftp://',
		'irc://',
		'ircs://',  // @bug 28503
		'gopher://',
		'telnet://', // Well if we're going to support the above.. -ævar
		'nntp://', // @bug 3808 RFC 1738
		'worldwind://',
		'mailto:',
		'news:',
		'svn://',
		'git://',
		'mms://',
		'//', // for protocol-relative URLs
	),
	'+wikivoyage' => array( 'tel://' ),
),
'wgUseTidy' => array(
	'default' => true,
	'foundationwiki' => false,
	'collabwiki' => false,
	'donatewiki' => false,
)
);


### WMF Labs override #####
if ( $wmfRealm == 'labs' ) {
	require( "$wmfConfigDir/InitialiseSettings-labs.php" );
	wmfLabsOverrideSettings();
}
