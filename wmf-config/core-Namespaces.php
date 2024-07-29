<?php

// Inline comments are often used for noting the task(s) associated with specific configuration
// and requiring comments to be on their own line would reduce readability for this file
// phpcs:disable MediaWiki.WhiteSpace.SpaceBeforeSingleLineComment.NewLineComment

return [

# wgNamespaceAliases @{
'wgNamespaceAliases' => [
	// defaults to aid when things are switched
	// Set default aliases for project_talk for all projects // T173070
	// set default short aliases for NS_PROJECT (T293839)
	'+wikibooks' => [
		'Wikibooks' => NS_PROJECT,
		'Wikibooks_talk' => NS_PROJECT_TALK,
		'WB' => NS_PROJECT,
	],
	'+wikinews' => [
		'Wikinews' => NS_PROJECT,
		'Wikinews_talk' => NS_PROJECT_TALK,
		'WN' => NS_PROJECT,
	],
	'+wikiquote' => [
		'Wikiquote' => NS_PROJECT,
		'Wikiquote_talk' => NS_PROJECT_TALK,
		'WQ' => NS_PROJECT,
	],
	'+wikisource' => [
		'Wikisource' => NS_PROJECT,
		'Wikisource_talk' => NS_PROJECT_TALK,
		'WS' => NS_PROJECT,
	],
	'+wikiversity' => [
		'Wikiversity' => NS_PROJECT,
		'Wikiversity_talk' => NS_PROJECT_TALK,
		'WV' => NS_PROJECT,
	],
	'+wikipedia' => [
		'Wikipedia' => NS_PROJECT,
		'Wikipedia_talk' => NS_PROJECT_TALK,
		'WP' => NS_PROJECT,
	],
	'+wiktionary' => [
		'Wiktionary' => NS_PROJECT,
		'Wiktionary_talk' => NS_PROJECT_TALK,
		'WT' => NS_PROJECT,
	],
	'+wikivoyage' => [
		'Wikivoyage' => NS_PROJECT,
		'Wikivoyage_talk' => NS_PROJECT_TALK,
		'WV' => NS_PROJECT,
	],
	'+altwiki' => [
		'ВП' => NS_PROJECT,
	],
	'+angwiki' => [ // T58634, T60711
		'Wikipaedia' => NS_PROJECT,
		'Wikipædiamotung' => NS_PROJECT_TALK,
		'Wikipaediamotung' => NS_PROJECT_TALK,
		'Wikipædia' => NS_PROJECT,
		'Wicipǣdia' => NS_PROJECT,
		'Wicipædia' => NS_PROJECT,
		'Wicipaedia' => NS_PROJECT,
		'Ƿikipǣdia' => NS_PROJECT,
		'Ƿikipædia' => NS_PROJECT,
		'Ƿikipaedia' => NS_PROJECT,
		'Ƿicipǣdia' => NS_PROJECT,
		'Ƿicipædia' => NS_PROJECT,
		'Ƿicipaedia' => NS_PROJECT,
		'Wicipǣdiamōtung' => NS_PROJECT_TALK,
		'Wicipædiamotung' => NS_PROJECT_TALK,
		'Wicipaediamotung' => NS_PROJECT_TALK,
		'Ƿikipǣdiamōtung' => NS_PROJECT_TALK,
		'Ƿikipædiamotung' => NS_PROJECT_TALK,
		'Ƿikipaediamotung' => NS_PROJECT_TALK,
		'Ƿicipǣdiamōtung' => NS_PROJECT_TALK,
		'Ƿicipædiamotung' => NS_PROJECT_TALK,
		'Ƿicipaediamotung' => NS_PROJECT_TALK,
	],
	'+angwiktionary' => [ // T58634, T60711
		'Wikiwordboc' => NS_PROJECT,
		'Wikiwordbocmotung' => NS_PROJECT_TALK,
		'Wiciwordbōc' => NS_PROJECT,
		'Wiciwordboc' => NS_PROJECT,
		'Ƿikiƿordbōc' => NS_PROJECT,
		'Ƿikiƿordboc' => NS_PROJECT,
		'Ƿiciƿordbōc' => NS_PROJECT,
		'Ƿiciƿordboc' => NS_PROJECT,
		'Wiciwordbōcmōtung' => NS_PROJECT_TALK,
		'Wiciwordbocmotung' => NS_PROJECT_TALK,
		'Ƿikiƿordbōcmōtung' => NS_PROJECT_TALK,
		'Ƿikiƿordbocmotung' => NS_PROJECT_TALK,
		'Ƿiciƿordbōcmōtung' => NS_PROJECT_TALK,
		'Ƿiciƿordbocmotung' => NS_PROJECT_TALK,
		'Æteaca' => 100,
		'Æteacmotung' => 101,
		'Aeteaca' => 100,
		'Aeteacmotung' => 101,
	],
	'+arbcom_ruwiki' => [
		'АК' => NS_PROJECT, // T264430
		'A' => NS_PROJECT, // T272292
		'AT' => NS_PROJECT_TALK, // T272292
		'ОА' => NS_PROJECT_TALK, // T272292
		'T' => NS_TEMPLATE, // T272292
		'Ш' => NS_TEMPLATE, // T272292
		'U' => NS_USER, // T272292
		'У' => NS_USER, // T272292
		'UT' => NS_USER_TALK, // T272292
		'ОУ' => NS_USER_TALK, // T272292
	],
	'+arwiki' => [
		'وب' => NS_PROJECT,
		'نو' => NS_PROJECT_TALK,
	],
	'+arwikinews' => [
		'وخ' => NS_PROJECT,
		'نو' => NS_PROJECT_TALK,
	],
	'+arwikisource' => [
		'Author' => 102,
		'Author_talk' => 103,
		'Page' => 104,
		'Page_talk' => 105,
		'وم' => NS_PROJECT,
		'نو' => NS_PROJECT_TALK,
	],
	'+arwikiversity' => [
		'وج' => NS_PROJECT
	],
	'+arzwiki' => [
		'Portal' => 100,
		'Portal_talk' => 101,
	],
	'+aswiki' => [
		'প্ৰকল্প' => NS_PROJECT,
		'প্ৰকল্প_আলোচনা' => NS_PROJECT_TALK,
		'Wikipedia_বার্তা' => NS_PROJECT_TALK,
		'CAT' => NS_CATEGORY,
		"বাটচ'ৰা" => 100,
		"বাটচ'ৰা_আলোচনা" => 101,
	],
	'+aswikisource' => [
		'Author' => 102, // T72464
		'Author_talk' => 103, // T72464
		'লিখক' => 102, // T72464
		'লিখক_আলোচনা' => 103, // T72464
	],
	'+astwiki' => [
		'Uiquipedia' => NS_PROJECT, // T94341
		'Uiquipedia_alderique' => NS_PROJECT_TALK, // T94341
	],
	'+astwiktionary' => [
		'Uiccionariu' => NS_PROJECT, // T99315
		'Uiccionariu_alderique' => NS_PROJECT_TALK,
	],
	'+azwiki' => [
		'VP' => NS_PROJECT, // T280577
		'QA' => 118, // T299332
		'Draft' => 118, // T299332
		'Draft_talk' => 119, // T299332
		'MZ' => NS_TALK, // T355041
		'FL' => NS_FILE, // T355041
		'FLM' => NS_FILE_TALK, // T355041
		'Ş' => NS_TEMPLATE, // T355041
		'ŞM' => NS_TEMPLATE_TALK, // T355041
		'MV' => NS_MEDIAWIKI, // T355041
		'MVM' => NS_MEDIAWIKI_TALK, // T355041
		'KAT' => NS_CATEGORY, // T355041
		'KATM' => NS_CATEGORY_TALK, // T355041
		'KMK' => NS_HELP, // T355041
		'KMKM' => NS_HELP_TALK, // T355041
		'İ' => NS_USER, // T355041
		'İM' => NS_USER_TALK, // T355041
		'MD' => 828, // T355041, module
		'MDM' => 829, // T355041, module_talk
		'POR' => 100, // T355041, portal
		'PORM' => 101, // T355041, portal_talk
	],
	'+azwikibooks' => [
		'Wikibooks_müzakirəsi' => NS_PROJECT_TALK, // T33068
		'VK' => NS_PROJECT, // T368237
	],
	'+azwikiquote' => [
		'VS' => NS_PROJECT, // T362645
	],
	'+azwikisource' => [
		'VikiMənbə' => NS_PROJECT, // T114002
		'VikiMənbə_müzakirəsi' => NS_PROJECT_TALK, // T114002
		'VM' => NS_PROJECT, // T365966
		'Portal' => 100,
		'Portal_talk' => 101,
	],
	'+azwiktionary' => [
		'VL' => NS_PROJECT, // T367264
	],
	'+bawiki' => [
		'Wikipedia_буйынса_фекерләшеү' => NS_PROJECT_TALK,
	],
	'+barwiki' => [
		'Portal_Diskussion' => 101, // T43962 - now "Portal Dischkrian"
	],
	'+bdwikimedia' => [
		'Workshop' => 100, // T195700
		'workshop_talk' => 101, // T195700
		'Meetup' => 102, // T195700
		'Meetup_talk' => 103, // T195700
		'Photowalk' => 104, // T205747, T351903
		'Photowalk_talk' => 105, // T205747, T351903
		'Program' => 106, // T351903
		'Program_talk' => 107, // T351903
	],
	'+betawikiversity' => [
		'Aptarimas' => NS_TALK,
		'Naudotojas' => NS_USER,
		'Naudotojo_aptarimas' => NS_USER_TALK,
		'Vikiversitetas' => NS_PROJECT,
		'Vikiversiteto_aptarimas' => NS_PROJECT_TALK,
		'Vaizdas' => NS_FILE,
		'Vaizdo_aptarimas' => NS_FILE_TALK,
		'MediaWiki_aptarimas' => NS_MEDIAWIKI_TALK,
		'Šablonas' => NS_TEMPLATE,
		'Šablono_aptarimas' => NS_TEMPLATE_TALK,
		'Pagalba' => NS_HELP,
		'Pagalbos_aptarimas' => NS_HELP_TALK,
		'Kategorija' => NS_CATEGORY,
		'Kategorijos_aptarimas' => NS_CATEGORY_TALK,
	], // T66080
	'+bewiki' => [
		'Portal' => 100,
		'Portal_talk' => 101,
		'ВП' => NS_PROJECT,
	],
	'+be_x_oldwiki' => [
		'ВП' => NS_PROJECT,
	],
	'+bewikisource' => [
		'ВК' => NS_PROJECT, // T43322
		'Author' => 102, // T43322
		'Author_talk' => 103,
	],
	'+bgwiki' => [
		'Draft' => 118, // T299224
		'Draft_talk' => 119,
	],
	'+bgwikisource' => [
		'Author' => 100,
		'Author_talk' => 101,
	],
	'+bhwiki' => [ // T155278
		'बिसेस' => NS_SPECIAL,
		'सदस्य' => NS_USER,
		'स' => NS_USER,
		'सदस्य' => NS_USER,
		'U' => NS_USER,
		'वि' => NS_PROJECT,
		'वि' => NS_PROJECT,
	],
	'+bjnwiki' => [
		'Wikipidia_pamandiran' => NS_PROJECT_TALK,
	],
	'+blkwiktionary' => [
		'Appendix' => 100,
		'Appendix_talk' => 101,
	],
	'+bnwiki' => [ // T318003
		'Portal' => 100,
		'Portal_talk' => 101,
		'WT' => NS_PROJECT_TALK,
		'Draft' => 118, // T350133
		'Draft_talk' => 119, // T350133
	],
	'+bnwikibooks' => [
		'Wikijunior' => 100,
		'wikijunior_talk' => 101,
		'Subject' => 102,
		'Subject_talk' => 103,
	],
	'+bnwikisource' => [ // T59134
		'Author' => 100, // T61901
		'Author_talk' => 101, // T61901
		'Portal' => 106, // T61901
		'Portal_talk' => 107, // T61901
	],
	'+bnwiktionary' => [ // T317745
		'Appendix' => 100,
		'Appendix_talk' => 101,
		'Rhymes' => 106,
		'Rhymes_talk' => 107,
		'Thesaurus' => 110,
		'Thesaurus_talk' => 111,
		'Citations' => 114,
		'Citations_talk' => 115,
	],
	'+bpywiki' => [
		'Portal' => 100,
		'Portal_talk' => 101,
	],
	'+brwiki' => [
		'Discussion_Wikipedia' => NS_PROJECT_TALK,
	],
	'+brwikisource' => [
		'Author' => 104,
		'Author_talk' => 105,
	],
	'+bswiki' => [
		'Razgovor_sa_korisnikom' => NS_USER_TALK, // T115812
		'Razgovor_s_Wikipediom' => NS_PROJECT_TALK, // T115812
		'Portal_talk' => 101, // T39226
	],
	'+bswiktionary' => [
		'Vikirječnik' => NS_PROJECT, // T159538
		'Razgovor_s_Vikirječnikom' => NS_PROJECT_TALK, // T159538
	],
	'+cawikisource' => [
		'Author' => 106,
		'Author_talk' => 107,
	],
	'+cewiki' => [ // T49574
		'Википедийа' => NS_PROJECT,
		'Википедийа_дийцаре' => NS_PROJECT_TALK,
	],
	'+ckbwiki' => [
		'وپ' => NS_PROJECT, // T53605
		'لو' => NS_PROJECT_TALK, // T71594
		'WT' => NS_PROJECT_TALK, // T71594
		'ب' => NS_USER, // T71594
		'U' => NS_USER, // T71594
		'لب' => NS_USER_TALK, // T71594
		'UT' => NS_USER_TALK, // T71594
		'پ' => NS_CATEGORY, // T71594
		'د' => NS_TEMPLATE, // T71594
		'T' => NS_TEMPLATE, // T71594
		'مو' => NS_MEDIAWIKI, // T71594
		'ی' => NS_HELP, // T71594
		'H' => NS_HELP, // T71594
		'وپر' => 102, // T54665
		'Wikiproject' => 102, // T54665
		'Wikiproject_talk' => 103, // T54665
		'Portal' => 100, // T71594
		'Portal_talk' => 101, // T71594
		'Draft' => 118, // T332470
		'Draft_talk' => 119, // T332470
	],
	'+commonswiki' => [
		'Museum' => 106,
		'Museum_talk' => 107,
		'COM' => NS_PROJECT, // T14600
		'CAT' => NS_CATEGORY, // T236352
	],
	'+cswiki' => [
		'Uživatel' => NS_USER,
		'Wikipedistka' => NS_USER,
		'Diskuse_s_uživatelem' => NS_USER_TALK,
		'Diskuse_s_wikipedistkou' => NS_USER_TALK,
		'Wikipedista_diskuse' => NS_USER_TALK,
		'Wikipedistka_diskuse' => NS_USER_TALK,
		'Portal' => 100,
		'Portal_talk' => 101,
		'Portál_diskuse' => 101,
		'Rejstřík_diskuse' => 103,
	],
	'+cswikibooks' => [
		'WK' => NS_PROJECT,
		'Wikibooks_diskuse' => NS_PROJECT_TALK,
	],
	'+cswikinews' => [
		'Redaktorka' => NS_USER,
		'Uživatel' => NS_USER,
		'Diskuse_s_redaktorkou' => NS_USER_TALK,
		'Diskuse_s_uživatelem' => NS_USER_TALK,
		'Redaktor_diskuse' => NS_USER_TALK,
		'Redaktorka_diskuse' => NS_USER_TALK,
		'WZ' => NS_PROJECT,
		'Wikinews_diskuse' => NS_PROJECT_TALK,
	],
	'+cswikiquote' => [
		'WC' => NS_PROJECT,
		'Wikiquote_diskuse' => NS_PROJECT_TALK,
		'Dílo_diskuse' => 101,
	],
	'+cswikisource' => [
		'WZ' => NS_PROJECT,
		'Wikisource_diskuse' => NS_PROJECT_TALK,
		'Author' => 100,
		'Author_talk' => 101,
		'Autor_diskuse' => 101,
		'Edice_diskuse' => 103, // T221697
	],
	'+cswikiversity' => [
		'Wikiversity_diskuse' => NS_PROJECT_TALK,
		'Forum' => 100,
		'Forum_talk' => 101,
		'Fórum_diskuse' => 101,
	],
	'+cswiktionary' => [
		'WS' => NS_PROJECT,
		'Wiktionary_diskuse' => NS_PROJECT_TALK,
		'Příloha_diskuse' => 101,
	],
	'+chapcomwiki' => [
		'Chapcom' => NS_PROJECT // T41482
	],
	'+cuwiki' => [
		'Шаблон' => NS_TEMPLATE,
		'Категория' => NS_CATEGORY,
		'Участник' => NS_USER,
		'Википє́дїꙗ' => NS_PROJECT,
		'Википє́дїѩ_бєсѣ́да' => NS_PROJECT_TALK,
		'Википєдїѩ_бєсѣ́да' => NS_PROJECT_TALK, // T123654
	],
	'+cywikisource' => [
		'Wicitestun' => NS_PROJECT,
		'Sgwrs_Wicitestun' => NS_PROJECT_TALK,
	],
	'+dawiki' => [
		'Portal_diskussion' => 101, // T9759
	],
	'+dawikisource' => [
		'Author' => 102, // T9796
		'Author_talk' => 103,
	],
	'+dewiki' => [
		'P' => 100,
		'PD' => 101,
		'WD' => NS_PROJECT_TALK,
		'BD' => NS_USER_TALK,
		'H' => NS_HELP,
		'HD' => NS_HELP_TALK,
	],
	'+dewikinews' => [
		'Comments' => 102,
		'Comments_talk' => 103,
	],
	'+dewikiquote' => [
		'BD' => NS_USER_TALK,
	],
	'+dewikivoyage' => [ // T53794
		'WD' => NS_PROJECT_TALK,
		'BD' => NS_USER_TALK,
		'H' => NS_HELP,
		'HD' => NS_HELP_TALK,
		'P' => 100, // Portal
		'PD' => 101, // Portal Diskussion
		'T' => 104, // Thema
		'TD' => 105, // Thema Diskussion
		'NR' => 106, // Nachrichten
		'ND' => 107, // Nachrichten Diskussion
		'News' => 106,
		'News_Diskussion' => 107,
	],
	'+dewiktionary' => [
		'WikiSaurus' => 104,
		'WikiSaurus_Diskussion' => 105,
		'BD' => NS_USER_TALK,
	],
	'+dvwiki' => [
		'Portal' => 100,
		'Portal_talk' => 101,
	],
	'elwiki' => [
		'ΒΠ' => NS_PROJECT, // T135383
	],
	'+elwikisource' => [
		'Βιβλιο' => 102,
		'Συζήτηση_βιβλίου' => 103,
		'Author' => 108, // T157187
		'Author_talk' => 109, // T157187
		'Portal' => 110, // T157187
		'Portal_talk' => 111, // T157187
	],
	'+enwiki' => [
		'WT' => NS_PROJECT_TALK, // T8313
		'TM' => NS_TEMPLATE, // T363757
	],
	'+enwikibooks' => [
		'WJ' => 110,
		'CAT' => NS_CATEGORY,
		'COOK' => 102,
		'SUB' => 112,
	],
	'+enwikinews' => [
		'CAT' => NS_CATEGORY,
	],
	'+enwikisource' => [
		'WT' => NS_PROJECT_TALK, // T44853
		'H' => NS_HELP, // T167563
	],
	'+enwiktionary' => [
		'WS' => 110, // Thesaurus
		'Wikisaurus' => 110, // T174264
		'Wikisaurus_talk' => 111,
		'CAT' => NS_CATEGORY, // T123187
		'T' => NS_TEMPLATE, // T123187
		'MOD' => 828, // T123187
		'AP' => 100, // T123187
		'RC' => 118, // T123187
	],
	'+eowiki' => [
		'VP' => NS_PROJECT,
	],
	'+eowiktionary' => [
		'Vikipediisto' => NS_USER, // T24426
		'Vikipediista_diskuto' => NS_USER_TALK, // T24426
	],
	'+eowikisource' => [
		'Auxtoro' => 102,
		'Auxtoro-Diskuto' => 103,
		'VF' => NS_PROJECT, // T340609
	],
	'+eswiki' => [
		'CAT' => NS_CATEGORY, // T183612
		'PR' => 102, // T183612
	],
	'+eswikibooks' => [
		'CAT' => NS_CATEGORY, // T216143
		'WL' => NS_PROJECT, // T216143
	],
	'+eswikisource' => [
		'A' => NS_HELP, // T240050
	],
	'+eswiktionary' => [
		'WN' => NS_PROJECT, // T119006
	],
	'+etwiki' => [
		'Portal' => 100,
		'Portal_talk' => 101,
		'Draft' => 102, // T223472
		'Draft_talk' => 103, // T223472
	],
	'+etwikisource' => [
		'Author' => 106,
		'Author_talk' => 107,
	],
	'+euwikisource' => [ // T193225
		'Author' => 106,
		'Author_talk' => 107,
	],
	'+extwiki' => [
		'Güiquipeya' => NS_PROJECT, // T337696
	],
	'+fawiki' => [
		'كاربر' => NS_USER,
		'بحث كاربر' => NS_USER_TALK,
		'بک' => NS_USER_TALK, // T213733
		'وپ' => NS_PROJECT,
		'ویکی پدیا' => NS_PROJECT,
		'ويكي پديا' => NS_PROJECT,
		'ويکي‌پديا' => NS_PROJECT,
		'بوپ' => NS_PROJECT_TALK, // T213733
		'بحث ویکی پدیا' => NS_PROJECT_TALK,
		'بحث ويکي پديا' => NS_PROJECT_TALK,
		'بحث ويکي‌پديا' => NS_PROJECT_TALK,
		'ره' => NS_HELP, // T329465
		'پیشنویس' => 118, // T92760
		'بحث_پیشنویس' => 119, // T92760
		'پيشنويس' => 118, // T92760
		'بحث_پيشنويس' => 119, // T92760
		'پیش_نویس' => 118, // T92760
		'بحث_پیش_نویس' => 119, // T92760
		'پیش_نویس' => 118, // T92760
		'بحث_پیش_نویس' => 119, // T92760
		'پيش_نويس' => 118, // T92760
		'بحث_پيش_نويس' => 119, // T92760
		'پيش‌نويس' => 118, // T92760
		'بحث_پيش‌نويس' => 119, // T92760
		'Draft' => 118, // T92760
		'Draft_talk' => 119, // T92760
		'پود' => 828, // T213733
	],
	'+fawikibooks' => [
		'وک' => NS_PROJECT,
		'ویکی‌نسک' => NS_PROJECT,
		'کتاب_آشپزی' => 102,
		'بحث_کتاب_آشپزی' => 103,
		'ویکی_کودک' => 110,
		'بحث_ویکی_کودک' => 111,
	],
	'+fawikinews' => [
		'وخ' => NS_PROJECT,
	],
	'+fawikisource' => [
		'Portal' => 100,
		'Portal_talk' => 101,
		'Author' => 102,
		'Author_talk' => 103,
		'پدیدآورنده' => 102,
		'گفتگو_پدیدآورنده' => 103,
		'ون' => NS_PROJECT,
		'گفتگوی_برگه' => 105,
		'گفتگوی_فهرست' => 107,
	],
	'+fawikivoyage' => [
		'وس' => NS_PROJECT,
		'عب' => 106,
	], // T73668
	'+fawikiquote' => [
		'وگ' => NS_PROJECT, // T156451
	],
	'+fawiktionary' => [
		'وو' => NS_PROJECT,
	],
	'+frwiki' => [
		'DW' => NS_PROJECT_TALK, // T153952
		'Discussion_Wikipedia' => NS_PROJECT_TALK,
		'Utilisatrice' => NS_USER,
		'Discussion_Utilisatrice' => NS_USER_TALK,
	],
	'+frwikibooks' => [
		'WL' => NS_PROJECT, // T37977
		'WJ' => 102, // T37977
		'Wikijunior_talk' => 103, // T37977
	],
	'+frwikisource' => [
		'Author' => 102,
		'Author_talk' => 103,
	],
	'+frwiktionary' => [
		'conj' => 116, // T262298
		'CVT' => 120, // T360989
	],
	'+fywiki' => [ // T334807
		'Portal' => 100,
		'Portal_talk' => 101,
	],
	'+ganwiki' => [
		'CAT' => NS_CATEGORY, // T355854
		'H' => NS_HELP, // T355854
		'T' => NS_TEMPLATE, // T355854
		'U' => NS_USER, // T355854
		'UT' => NS_USER_TALK, // T355854
		'WT' => NS_PROJECT_TALK, // T355854
	],
	'+glwiki' => [
		'Portal_talk' => 101, // T43213 (old namespace name)
	],
	'+guwiki' => [ // T255358
		'વિ' => NS_PROJECT,
		'સ' => NS_USER,
		'શ્રે' => NS_CATEGORY,
		'CAT' => NS_CATEGORY,
	],
	'+guwiktionary' => [
		'વિક્શનરી' => NS_PROJECT // T42776 (old namespace name)
	],
	'+guwikisource' => [
		'Audiobook' => 110, // T347189
		'Audiobook_talk' => 111, // T347189
	],
	'+hewiki' => [ // T164858
		'מי' => NS_SPECIAL,
		'ש' => NS_TALK,
		'מש' => NS_USER,
		'U' => NS_USER,
		'שמש' => NS_USER_TALK,
		'וק' => NS_PROJECT,
		'שוק' => NS_PROJECT_TALK,
		'קו' => NS_FILE,
		'שקו' => NS_FILE_TALK,
		'מו' => NS_MEDIAWIKI,
		'שמו' => NS_MEDIAWIKI_TALK,
		'תב' => NS_TEMPLATE,
		'שתב' => NS_TEMPLATE_TALK,
		'עז' => NS_HELP,
		'שעז' => NS_HELP_TALK,
		'קט' => NS_CATEGORY,
		'שקט' => NS_CATEGORY_TALK,
		'פ' => /* Portal */ 100,
		'שפ' => /* Portal talk */ 101,
		'טי' => /* Draft */ 118,
		'שטי' => /* Draft talk */ 119,
		'Draft' => 118, // T223472
		'Draft_talk' => 119, // T223472
		'י' => /* Module */ 828,
		'שי' => /* Module talk */ 829,
		'נ' => /* Topic (Flow) */ 2600,
	],
	'+hewikisource' => [
		'Author' => 108,
		'Author_talk' => 109,
		'באור' => 106,
		'שיחת_באור' => 107,

		// T286500
		'מי' => NS_SPECIAL,
		'ש' => NS_TALK,
		'מש' => NS_USER,
		'U' => NS_USER,
		'שמש' => NS_USER_TALK,
		'קו' => NS_FILE,
		'שקו' => NS_FILE_TALK,
		'מו' => NS_MEDIAWIKI,
		'שמו' => NS_MEDIAWIKI_TALK,
		'תב' => NS_TEMPLATE,
		'שתב' => NS_TEMPLATE_TALK,
		'קט' => NS_CATEGORY,
		'שקט' => NS_CATEGORY_TALK,
	],
	'+hewikinews' => [ // T349581
		'מי' => NS_SPECIAL,
		'ש' => NS_TALK,
		'U' => NS_USER,
		'מש' => NS_USER,
		'משת' => NS_USER,
		'שמש' => NS_USER_TALK,
		'שמשת' => NS_USER_TALK,
		'וק' => NS_PROJECT,
		'שוק' => NS_PROJECT_TALK,
		'וח' => NS_PROJECT,
		'שוח' => NS_PROJECT_TALK,
		'קו' => NS_FILE,
		'שקו' => NS_FILE_TALK,
		'מו' => NS_MEDIAWIKI,
		'שמו' => NS_MEDIAWIKI_TALK,
		'תב' => NS_TEMPLATE,
		'שתב' => NS_TEMPLATE_TALK,
		'עז' => NS_HELP,
		'שעז' => NS_HELP_TALK,
		'קט' => NS_CATEGORY,
		'תג' => NS_CATEGORY,
		'קטגוריה' => NS_CATEGORY,
		'שיחת_קטגוריה' => NS_CATEGORY_TALK,
		'שתג' => NS_CATEGORY_TALK,
		'שקט' => NS_CATEGORY_TALK,
		'פורטל' => /* Portal */ 100,
		'שיחת_פורטל' => /* Portal talk */ 101,
		'פ' => /* Portal */ 100,
		'שפ' => /* Portal talk */ 101,
		'מד' => /* Portal */ 100,
		'שמד' => /* Portal talk */ 101,
		'טי' => /* Draft */ 100,
		'שטי' => /* Draft talk */ 101,
		'Portal' => 100,
		'Portal_talk' => 101,
		'Draft' => 118,
		'Draft_talk' => 119,
		'י' => /* Module */ 828,
		'שי' => /* Module talk */ 829,
	],
	'+hiwiki' => [
		'वि' => NS_PROJECT,
		'विवा' => NS_PROJECT_TALK,
		'WPT' => NS_PROJECT_TALK,
		'U' => NS_USER,
		'UT' => NS_USER_TALK,
		'स' => NS_USER,
		'सवा' => NS_USER_TALK,
		'श्र' => NS_CATEGORY,
		'श्रवा' => NS_CATEGORY_TALK,
		'CT' => NS_CATEGORY_TALK,
		'सा' => NS_TEMPLATE,
		'सावा' => NS_TEMPLATE_TALK,
		'T' => NS_TEMPLATE,
		'मी' => NS_MEDIAWIKI,
		'मीवा' => NS_MEDIAWIKI_TALK,
		'P' => 100,
		'प्र' => 100,
		'प्रवा' => 101,
		'Portal' => 100, // T187286
		'Portal_talk' => 101,
	],
	'+hiwikibooks' => [ // T254012
		'वि' => NS_PROJECT,
		'विपु' => NS_PROJECT,
	],
	'+hiwikiversity' => [
		'Portal' => 100, // T172977
		'School' => 102, // T172977
		'Collection' => 106, // T172977
		'विकिविद्यालय' => NS_PROJECT, // T185347
		'Draft' => 118, // T187535
		'Draft_talk' => 119, // T187535
	],
	'+hrwiki' => [
		'WT' => NS_PROJECT_TALK, // T287024
		'RWP' => NS_PROJECT_TALK, // T287024
		'KT' => NS_CATEGORY, // T287024
		'CT' => NS_CATEGORY, // T287024
		'RKT' => NS_CATEGORY_TALK, // T287024
		'RCT' => NS_CATEGORY_TALK, // T287024
		'SUR' => NS_USER, // T287024
		'U' => NS_USER, // T287024
		'RSUR' => NS_USER_TALK, // T287024
		'RSS' => NS_USER_TALK, // T287024
		'DT' => NS_FILE, // T287024
		'RDT' => NS_FILE_TALK, // T287024
		'RF' => NS_FILE_TALK, // T287024
		'ME' => NS_MEDIAWIKI, // T287024
		'RME' => NS_MEDIAWIKI_TALK, // T287024
		'PM' => NS_HELP, // T287024
		'H' => NS_HELP, // T287024
		'RPM' => NS_HELP_TALK, // T287024
		'RH' => NS_HELP_TALK, // T287024
		'MD' => 828, // T287024
		'MOD' => 828, // T287024
		'RMD' => 829, // T287024
		'RMOD' => 829, // T287024
		'Draft' => 118, // T291755
		'Draft_talk' => 119, // T291755
	],
	'+hrwikisource' => [
		'Author' => 100,
		'Author_talk' => 101,
	],
	'+hsbwiktionary' => [
		'Wiktionary diskusija' => NS_PROJECT_TALK,
	],
	'+huwiki' => [
		'Portál_vita' => 101,
		'Draft' => 118, // T333083
		'Draft_talk' => 119, // T333083
	],
	'+huwikinews' => [
		'Wikihírek_vita' => NS_PROJECT_TALK,
		'Portál_vita' => 103,
	],
	'+huwikisource' => [
		'Author' => 100,
		'Author_talk' => 101,
	],
	'+huwiktionary' => [
		'Appendix' => 100, // T341926
		'Appendix_talk' => 101, // T341926
		'Függelék_vita' => 101, // T44505
		'Index_vita' => 103,
	],
	'+hywikibooks' => [ // T55162
		'Cookbook' => 102,
		'Cookbook_talk' => 103,
		'Wikijunior' => 110,
		'Wikijunior_talk' => 111,
	],
	'+hywikisource' => [
		'Author' => 100,
		'Author_talk' => 101,
	],
	'+idwikibooks' => [
		'Pembicaraan_Wikibooks' => NS_PROJECT_TALK, // T38156
	],
	'+idwikisource' => [
		'Author' => 100,
		'Author_talk' => 101,
		'Wikisumber' => NS_PROJECT, // T341173
		'Pembicaraan_Wikisumber' => NS_PROJECT_TALK, // T341173
	],
	'+ilowiki' => [
		'WT' => NS_PROJECT_TALK, // T40247
	],
	'+incubatorwiki' => [
		'I' => NS_PROJECT,
	],
	'+inhwiki' => [
		'Моартал' => 100, // T326089
		'Моарталах_къамаьл' => 101 // T326089
	],
	'+iswikisource' => [
		'Portal' => 100, // T46164
		'Portal_talk' => 101, // T46164
		'Author' => 102, // T46164
		'Author_talk' => 103, // T46164
	],
	'+iswiktionary' => [
		'Wikiorðabókspjall' => NS_PROJECT_TALK, // T9754
		'Thesaurus' => 110,
		'Thesaurus_talk' => 111,
	],
	'+itwiki' => [
		'CAT' => NS_CATEGORY, // T101274
		'DP' => 103, // T101274
		'DW' => NS_PROJECT_TALK, // T101274
		'T' => NS_TEMPLATE, // T101274
	],
	'+itwikibooks' => [
		'Portale' => 100,
		'Discussioni_portale' => 101,
		'Shelf' => 102,
		'Shelf_talk' => 103,
	],
	'+itwikisource' => [
		'Author' => 102,
		'Author_talk' => 103,
		'Work' => 112, // T93870
		'Work_talk' => 113,
	],
	'+itwikiversity' => [
		'Facoltà' => 100,
		'Discussioni_facoltà' => 101,
		'U' => NS_USER, // T158775
		'UT' => NS_USER_TALK, // T158775
		'DWV' => NS_PROJECT_TALK, // T158775
		'T' => NS_TEMPLATE, // T158775
		'DT' => NS_TEMPLATE_TALK, // T158775
		'H' => NS_HELP, // T158775
		'DH' => NS_HELP_TALK, // T158775
		'CAT' => NS_CATEGORY, // T158775
		'DCAT' => NS_CATEGORY_TALK, // T158775
		'DAREA' => 101, // T158775
		'DC' => 103, // T158775
		'MA' => 104, // T158775
		'DMA' => 105, // T158775
		'DIP' => 106, // T158775
		'DDIP' => 107, // T158775
	],
	'+itwiktionary' => [
		'WZ' => NS_PROJECT,
	],
	'+jawiki' => [
		'トーク' => NS_TALK,
		'利用者・トーク' => NS_USER_TALK,
		'Wikipedia・トーク' => NS_PROJECT_TALK,
		'ファイル・トーク' => NS_FILE_TALK,
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
		'モジュール・トーク' => 829, // T49933
	],
	'+jawikinews' => [
		"ポータル‐ノート" => 101,
	],
	'+jawikivoyage' => [ // T262155
		'トーク' => NS_TALK,
		'利用者・トーク' => NS_USER_TALK,
		'Wikivoyage・トーク' => NS_PROJECT_TALK,
		'ファイル・トーク' => NS_FILE_TALK,
		'MediaWiki・トーク' => NS_MEDIAWIKI_TALK,
		'テンプレート・トーク' => NS_TEMPLATE_TALK,
		'ヘルプ・トーク' => NS_HELP_TALK,
		'カテゴリ・トーク' => NS_CATEGORY_TALK,
		'WikiProject' => 102,
		'WikiProject talk' => 103,
		'プロジェクト‐ノート' => 103,
		'モジュール・トーク' => 829,
	],
	'+kawikiquote' => [
		'Wikiquote_განხილვა' => NS_PROJECT_TALK,
	],
	'+kawikisource' => [ // T363243
		'Author' => 102,
		'Author_talk' => 103,
	],
	'+kbdwiktionary' => [
		'Appendix' => 100,
		'Appendix_talk' => 101,
		'Index' => 102,
		'Index_talk' => 103,
	],
	'+kkwiki' => [
		'Joba' => 102, // T42794
		'Joba_talqılawı' => 103,
		'جوبا' => 102,
		'جوبا_تالقىلاۋى' => 103,
	],
	'+knwiki' => [
		'Draft' => 118, // T223472
		'Draft_talk' => 119, // T223472
		'Portal' => 100, // T355662
		'Portal_talk' => 101, // T355662
		'ಪೋರ್ಟಲ್' => 100, // T355662
		'ಪೋರ್ಟಲ್_ಚರ್ಚೆ' => 101, // T355662
		'ಕರಡು_ಚರ್ಚೆ' => 119, // T346583
		'ಮಾಡ್ಯೂಲ್_ಚರ್ಚೆ' => 829, // T346583
	],
	'+knwikisource' => [ // T39676
		'Portal' => 100,
		'Portal_talk' => 101,
		'Author' => 102,
		'Author_talk' => 103,
	],
	'+kowiki' => [
		'백' => NS_PROJECT,
		'백토' => NS_PROJECT_TALK,
		'사' => NS_USER, // T57909
		'사토' => NS_USER_TALK, // T57342
		'들머리' => 100, // T87528
		'들머리토론' => 101, // T87528
		'들' => 100,
		'들토' => 101,
		'프' => 102,
		'프토' => 103,
		'Draft' => 118, // T223472
		'Draft_talk' => 119, // T223472
	],
	'+kowikinews' => [
		'뉴' => NS_PROJECT,
		'뉴토' => NS_PROJECT_TALK,
	],
	'+kowikiquote' => [
		'인' => NS_PROJECT,
		'사' => NS_USER, // T255031
		'사토' => NS_USER_TALK, // T255031
	],
	'+kowikisource' => [
		'문' => NS_PROJECT, // T182487
		'문토' => NS_PROJECT_TALK, // T182487
		'사' => NS_USER, // T358508
		'사토' => NS_USER_TALK, // T358508
		'글쓴이' => 100,
		'글쓴이토론' => 101,
		'Author' => 100,
		'Author_talk' => 101,
		'Portal' => 102, // T71522
		'Portal_talk' => 103,
	],
	'+kowikiversity' => [
		'Wikiversity토론' => NS_PROJECT_TALK, // T46899
	],
	'+kowiktionary' => [
		'낱' => NS_PROJECT, // T58761
	],
	'+kswiki' => [
		'وپ' => NS_PROJECT, // T291740
		'وب' => NS_PROJECT_TALK,
	],
	'+kuwiktionary' => [
		'Wîkîferheng_gotûbêj' => NS_PROJECT_TALK, // T39524 (old namespace name)
		'Pêvek_nîqas' => 101, // T39524 (old namespace name)
		'Nimînok_nîqas' => 103, // T39524 (old namespace name)
		'Portal_nîqas' => 105, // T39524 (old namespace name)
		'WF' => NS_PROJECT, // T269319
	],
	'+kuwiki' => [
		'Portal_nîqaş' => 101, // T39521
	],
	'+kvwiki' => [
		'Обсуждение_Wikipedia' => NS_PROJECT_TALK,
	],
	'+lawiki' => [
		'Disputatio_Wikipedia' => NS_PROJECT_TALK,
	],
	'+lawikisource' => [
		'Author' => 102,
		'Author_talk' => 103,
	],
	'+lbwiktionary' => [
		'Wiktionary_Diskussioun' => NS_PROJECT_TALK,
	],
	'+lijwikisource' => [
		'Author' => 102, // T257672
		'Author_talk' => 103, // T257672
		'Work' => 104, // T257672
		'Work_talk' => 105, // T257672
	],
	'+lmowiktionary' => [
		'Appendix' => 100,
		'Appendix_talk' => 101,
	],
	'+ltwiki' => [
		'Wikipedia_aptarimas' => NS_PROJECT_TALK,
	],
	'+lvwiki' => [
		'VP' => NS_PROJECT, // T95106
	],
	'+maiwiki' => [
		'वि' => NS_PROJECT, // T125801
		'वि_वा' => NS_PROJECT_TALK, // T125801
		'CT' => NS_CATEGORY_TALK, // T125801
		'WT' => NS_PROJECT_TALK, // T125801
		'u' => NS_USER, // T125801
		'ut' => NS_USER_TALK, // T125801
		'प्र' => NS_USER, // T125801
		'प्र_वा' => NS_USER_TALK, // T125801
		'आ' => NS_TEMPLATE, // T125801
		'आ_वा' => NS_TEMPLATE_TALK, // T125801
	],
	'+mediawikiwiki' => [ // T349970
		'Extensions' => 102,
		'Extensions_talk' => 103,
		'Skins' => 106,
		'Skins_talk' => 107,
	],
	'+metawiki' => [ // T31129
		'R' => 202,
	],
	'+mlwiki' => [
		'വിക്കി' => NS_PROJECT,
		'വിക്കിസം' => NS_PROJECT_TALK,
		'Portal' => 100,
		'Portal_talk' => 101,
		'ഘ' => 828, // T56951
		'ഘസം' => 829, // T56951
		'Draft' => 118, // Draft - T362653
		'Draft_talk' => 119, // Draft - T362653
	],
	'+mlwikibooks' => [
		'വിക്കി‌‌_പുസ്തകശാല' => NS_PROJECT,
		'വിക്കി‌‌_പുസ്തകശാല_സംവാദം' => NS_PROJECT_TALK,
		'Wikibooks_talk' => NS_PROJECT_TALK,
		'Cookbook' => 100,
		'Cookbook_talk' => 101,
		'Subject' => 102,
		'Subject_talk' => 103,
		'വി' => NS_PROJECT,
		'വിസം' => NS_PROJECT_TALK,
		'ഉ' => NS_USER,
		'ഉസം' => NS_USER_TALK,
		'പ്ര' => NS_FILE,
		'പ്രസം' => NS_FILE_TALK,
		'ഫ' => NS_TEMPLATE,
		'ഫസം' => NS_TEMPLATE_TALK,
		'വ' => NS_CATEGORY,
		'വസം' => NS_CATEGORY_TALK,
		'സ' => NS_HELP,
		'സസം' => NS_HELP_TALK,
		'ഘ' => 828, // T56951
		'ഘസം' => 829, // T56951
	],
	'+mlwikiquote' => [
		'വിക്കി_ചൊല്ലുകൾ' => NS_PROJECT, // T40111
		'വിക്കി_ചൊല്ലുകൾ_സംവാദം' => NS_PROJECT_TALK, // T40111
		'ഘ' => 828, // T56951
		'ഘസം' => 829, // T56951
	],
	'+mlwikisource' => [
		'Author' => 100,
		'Author_talk' => 101,
		'Portal' => 102,
		'Portal_talk' => 103,
		'H' => NS_HELP, // T37712
		'Translation' => 114, // T154087
		'Translation_talk' => 115,
		'പ' => 114,
		'പസം' => 115,
		'ഘ' => 828, // T56951
		'ഘസം' => 829, // T56951
	],
	'+mlwiktionary' => [
		'വിക്കി‌‌_നിഘണ്ടു' => NS_PROJECT,
		'വിക്കി‌‌_നിഘണ്ടു_സംവാദം' => NS_PROJECT_TALK,
		'ഘ' => 828, // T56951
		'ഘസം' => 829, // T56951
	],
	'+mnwwiki' => [
		'Portal' => 100,
		'Portal_talk' => 101,
	],
	'mnwwiktionary' => [
		'Appendix' => 100, // T314023 , T341940
		'Appendix_talk' => 101, // T314023 , T341940
		'Rhymes' => 106, // T330689 , T341940
		'Rhymes_talk' => 107, // T330689 , T341940
		'Reconstruction' => 118, // T330689 , T341940
		'Reconstruction_talk' => 119, // T330689 , T341940
	],
	'+mrwiki' => [
		'विपी' => NS_PROJECT,
	],
	'+mrwikibooks' => [
		'Wikibooks_talk' => NS_PROJECT_TALK, // T73774
		'Wikibooks_चर्चा' => NS_PROJECT_TALK, // T73774
	],
	'+mrwikisource' => [
		'Portal' => 100,
		'Portal_talk' => 101,
		'Author' => 102,
		'Author_talk' => 103,
	],
	'+mswiki' => [
		'Portal_talk' => 101,
		'Perbualan_Portal' => 101,
	],
	'+mswikibooks' => [
		'Perbualan_Resipi' => 101,
	],
	'+mswikisource' => [ // T369047
		'Translation' => 114,
		'Translation_talk' => 115,
		'Author' => 102,
		'Author_talk' => 103,
	],
	'+mwlwiki' => [
		'Wikipedia_cumbersa' => NS_PROJECT_TALK,
		'BP' => NS_PROJECT, // T180052
	],
	'+mywiki' => [ // T352424
		'Portal' => 100,
		'Portal_talk' => 101,
		'Draft' => 118,
		'Draft_talk' => 119,
	],
	'+mywikisource' => [ // T371060
		'Portal' => 100,
		'Portal_talk' => 101,
		'Author' => 102,
		'Author_talk' => 103,
		'Translation' => 114,
		'Translation_talk' => 115,
	],
	'+mywiktionary' => [
		'Appendix' => 100, // T291146
		'Appendix_talk' => 101, // T291146
		'Rhymes' => 106, // T342516
		'Rhymes_talk' => 107, // T342516
		'Thesaurus' => 108, // T342516
		'Thesaurus_talk' => 109, // T342516
		'Reconstruction' => 110, // T342516
		'Reconstruction_talk' => 111, // T342516
		'Citations' => 112, // T342516
		'Citations_talk' => 113, // T342516
		'Concordance' => 116, // T342516
		'Concordance_talk' => 117, // T342516
	],
	'+mznwiki' => [
		'وپ' => NS_PROJECT,
		'Portal' => 100,
		'Portal_talk' => 101,
		'Wikipedia_گپ' => NS_PROJECT_TALK,
	],
	'+nahwiki' => [
		'Wikipedia_tēixnāmiquiliztli' => NS_PROJECT_TALK,
	],
	'+napwikisource' => [
		'Author' => 102, // T231880
		'Author_talk' => 103, // T231880
	],
	'+newiki' => [
		// Shorcuts per T89817
		'वि' => NS_PROJECT,
		'विवा' => NS_PROJECT_TALK,
		'CT' => NS_CATEGORY_TALK,
		'वि' => NS_PROJECT,
		'WT' => NS_PROJECT_TALK,
		'U' => NS_USER,
		'UT' => NS_USER_TALK,
		'प्र' => NS_USER,
		'प्रवा' => NS_USER,
		'ढाँ' => NS_TEMPLATE,
		'ढाँवा' => NS_TEMPLATE_TALK,
		'T' => NS_TEMPLATE,
		'विकिपीडिया' => NS_PROJECT, // T184865
		'विकिपीडिया_वार्ता' => NS_PROJECT_TALK, // T184865
		'Draft' => 118, // T223472
		'Draft_talk' => 119, // T223472
	],
	'+newikibooks' => [
		// Per T131754
		'Cookbook' => 102,
		'Cookbook_talk' => 103,
		'Wikijunior' => 104,
		'Wikijunior_talk' => 105,
		'Subject' => 106,
		'Subject_talk' => 107,
		'विपु' => NS_PROJECT,
		'विपु_वा' => NS_PROJECT_TALK,
		'प्र' => NS_USER,
		'प्र_वा' => NS_USER_TALK,
		'ढाँ' => NS_TEMPLATE,
		'ढाँ_वा' => NS_TEMPLATE_TALK,
		'श्रे' => NS_CATEGORY,
		'श्रे_वा' => NS_CATEGORY_TALK,
	],
	'+nlwiki' => [
		'H' => NS_HELP,
		'P' => 100,
	],
	'+nlwikisource' => [
		'Author' => 102,
		'Author_talk' => 103,
	],
	'+nowikibooks' => [
		'Cookbook' => 102,
		'Cookbook_talk' => 103,
	],
	'+nowikimedia' => [
		'Brukar' => NS_USER,
		'Brukardiskusjon' => NS_USER_TALK,
		'Wikimedia_Noreg' => NS_PROJECT,
		'Wikimedia_Norga' => NS_PROJECT,
	],
	'+nowikisource' => [
		'Author' => 102, // T9796
		'Author_talk' => 103,
	],
	'+nowiktionary' => [
		'Appendix' => 100,
		'Appendix_talk' => 101,
	],
	'+officewiki' => [ // T66976
		'OW' => NS_PROJECT,
	],
	'+orwiki' => [
		'WT' => NS_PROJECT_TALK, // T30257
	],
	'+outreachwiki' => [
		'Wikipedia' => NS_PROJECT,
		'Wikipedia_talk' => NS_PROJECT_TALK,
	],
	'+pawikisource' => [
		'Audiobook' => 112, // T343410
		'Audiobook_talk' => 113, // T343410
	],
	'+plwiki' => [ // T12064
		'Wikipedystka' => NS_USER,
		'Dyskusja_wikipedystki' => NS_USER_TALK,
	],
	'+plwikibooks' => [
		'Wikipedystka' => NS_USER,
		'Dyskusja_wikipedystki' => NS_USER_TALK,
	],
	'+plwikisource' => [
		'Author' => 104,
		'Author_talk' => 105,
		'Collection' => 124, // T154711
		'Collection_talk' => 125, // T154711
	],
	'+pmswikisource' => [
		'Page' => 102,
		'Page_talk' => 103,
		'Index' => 104,
		'Index_talk' => 105,
	],
	'+plwiktionary' => [
		'WS' => NS_PROJECT,
		'Wikipedysta' => NS_USER, // T202347
		'Wikipedystka' => NS_USER, // T202347
		'Dyskusja_wikipedysty' => NS_USER_TALK, // T202347
		'Dyskusja_wikipedystki' => NS_USER_TALK, // T202347
	],
	'+pswikivoyage' => [
		'وس' => NS_PROJECT, // T197507
	],
	'+ptwiki' => [
		'Utilizador' => NS_USER, // T29495
		'Utilizador_Discussão' => NS_USER_TALK, // T29495
		'Discussão_Portal' => 101,
		'Wikipedia_Discussão' => NS_PROJECT_TALK,
	],
	'+ptwikibooks' => [
		'Wikibooks_Discussão' => NS_PROJECT_TALK,
	],
	'+ptwikisource' => [
		'Author' => 102,
		'Author_talk' => 103,
		'Em_tradução' => 108,
		'Discussão_Em_tradução' => 109,
		'Discussão_em_tradução' => 109,
	],
	'+rowiki' => [
		'Discuţie_MediWiki' => NS_MEDIAWIKI_TALK,
		'Portal_talk' => 101, // T127607
		'WikiProject' => 102, // T127607
		'WikiProject_talk' => 103, // T127607
		'Code' => 108, // T127607
		'Code_talk' => 109, // T127607
		'Book' => 110, // T68530, T127607
		'Book_talk' => 111, // T127607
	],
	'+rowikibooks' => [
		'Discuţie_Wikibooks' => NS_PROJECT_TALK,
	],
	'+rowikinews' => [
		'Wikiştiri' => NS_PROJECT,
		'Discuţie_Wikiştiri' => NS_PROJECT_TALK,
		'Discuţie_MediWiki' => NS_MEDIAWIKI_TALK,
	],
	'+rowikisource' => [
		'Author' => 102,
		'Author_talk' => 103,
	],
	'+ruwiki' => [
		'У' => NS_USER, // T44511
		'U' => NS_USER, // T69844
		'ОУ' => NS_USER_TALK, // T69844
		'UT' => NS_USER_TALK, // T69844
		'Ш' => NS_TEMPLATE, // T44511
		'T' => NS_TEMPLATE, // T44511
		'К' => NS_CATEGORY, // T44511
		'ВП' => NS_PROJECT, // T14320
		'И' => 102,
		'ПРО' => 104, // T36124
		'АК' => 106, // T36527
		'Участница' => NS_USER, // T14320
		'Обсуждение_участницы' => NS_USER_TALK,
		'ОПРО' => 105, // T189277
		'Arbcom' => 106, // T36527
	],
	'+ruwikibooks' => [
		'ВУ' => NS_PROJECT,

		// Shorcuts - T127591
		'Ш' => NS_TEMPLATE,
		'T' => NS_TEMPLATE,
		'MOD' => 828,
		'М' => 828,
		'К' => NS_CATEGORY,
		'У' => NS_USER,
		'U' => NS_USER,
		'ОУ' => NS_USER_TALK,
		'Р' => 104,
		'П' => 100,
		'З' => 106,
		'С' => NS_HELP,
		'И' => 102,
	],
	'+ruwikinews' => [
		'ВикиНовости' => NS_PROJECT,
		'ВН' => NS_PROJECT,
		'П' => 100,
		'Обсуждение_ВикиНовостей' => NS_PROJECT_TALK,
	],
	'+ruwikisource' => [
		'ВТ' => NS_PROJECT, // T196719
		'У' => NS_USER, // T196719
		'ОУ' => NS_USER_TALK, // T196719
		'Ш' => NS_TEMPLATE, // T196719
		'К' => NS_CATEGORY, // T196719
		'М' => 828, // T196719

	],
	'ruwikiquote' => [
		'ВЦ' => NS_PROJECT,
		'У' => NS_USER, // T197058
		'U' => NS_USER, // T197058
		'ОУ' => NS_USER_TALK, // T197058
		'UT' => NS_USER_TALK, // T197058
		'Ш' => NS_TEMPLATE, // T197058
		'T' => NS_TEMPLATE, // T197058
		'К' => NS_CATEGORY, // T197058
		'М' => 828, // T197058
	],
	'+ruwiktionary' => [
		'Appendix' => 100,
		'Appendix_talk' => 101,
		'Concordance' => 102,
		'Concordance_talk' => 103,
		'Index' => 104,
		'Index_talk' => 105,
		'Rhymes' => 106,
		'Rhymes_talk' => 107,
		'У' => NS_USER, // T197565
		'U' => NS_USER, // T197565
		'ОУ' => NS_USER_TALK, // T197565
		'UT' => NS_USER_TALK, // T197565
		'ВС' => NS_PROJECT, // T197565
		'Ш' => NS_TEMPLATE, // T197565
		'T' => NS_TEMPLATE, // T197565
		'К' => NS_CATEGORY, // T197565
		'М' => 828, // T197565
	],
	'+sawiki' => [
		'WT' => NS_PROJECT_TALK,
		'सहाय्यस्य_प्रवेशद्वारम्' => 101, // T101634 - Portal talk
	],
	'+sawikiquote' => [
		'विकिसूक्तिःसम्भाषणम्' => NS_PROJECT_TALK, // T101634
	],
	'+sewiki' => [
		'Temasiidu' => 100, // T41206
		'Temasiidoságastallan' => 101, // T41206
	],
	'+shwiki' => [
		'RZ' => NS_TALK, // T346588
		'РЗ' => NS_TALK, // T346588
		'SZR' => NS_TALK, // T346588
		'СЗР' => NS_TALK, // T346588
		'SRZ' => NS_TALK, // T346588
		'СРЗ' => NS_TALK, // T346588
		'R' => NS_TALK, // T346588
		'Р' => NS_TALK, // T346588
		'KOR' => NS_USER, // T346588
		'КОР' => NS_USER, // T346588
		'SAR' => NS_USER, // T346588
		'САР' => NS_USER, // T346588
		'SUR' => NS_USER, // T346588
		'СУР' => NS_USER, // T346588
		'U' => NS_USER, // T346588
		'RKOR' => NS_USER_TALK, // T346588
		'РКОР' => NS_USER_TALK, // T346588
		'RSAR' => NS_USER_TALK, // T346588
		'РСАР' => NS_USER_TALK, // T346588
		'RSUR' => NS_USER_TALK, // T346588
		'РСУР' => NS_USER_TALK, // T346588
		'RSS' => NS_USER_TALK, // T346588
		'РСС' => NS_USER_TALK, // T346588
		'UT' => NS_USER_TALK, // T346588
		'RWP' => NS_PROJECT_TALK, // T346588
		'RVP' => NS_PROJECT_TALK, // T346588
		'РВП' => NS_PROJECT_TALK, // T346588
		'WT' => NS_PROJECT_TALK, // T346588
		'DT' => NS_FILE, // T346588
		'ДТ' => NS_FILE, // T346588
		'RDT' => NS_FILE_TALK, // T346588
		'РДТ' => NS_FILE_TALK, // T346588
		'ME' => NS_MEDIAWIKI, // T346588
		'МЕ' => NS_MEDIAWIKI, // T346588
		'RME' => NS_MEDIAWIKI_TALK, // T346588
		'РМЕ' => NS_MEDIAWIKI_TALK, // T346588
		'Š' => NS_TEMPLATE, // T346588
		'Ш' => NS_TEMPLATE, // T346588
		'Т' => NS_TEMPLATE, // T346588
		'RŠ' => NS_TEMPLATE_TALK, // T346588
		'РШ' => NS_TEMPLATE_TALK, // T346588
		'PM' => NS_HELP, // T346588
		'ПМ' => NS_HELP, // T346588
		'H' => NS_HELP, // T346588
		'RPM' => NS_HELP_TALK, // T346588
		'РПМ' => NS_HELP_TALK, // T346588
		'KAT' => NS_CATEGORY, // T346588
		'КАТ' => NS_CATEGORY, // T346588
		'KT' => NS_CATEGORY, // T346588
		'КТ' => NS_CATEGORY, // T346588
		'RKAT' => NS_CATEGORY_TALK, // T346588
		'РКАТ' => NS_CATEGORY_TALK, // T346588
		'RKT' => NS_CATEGORY_TALK, // T346588
		'РКТ' => NS_CATEGORY_TALK, // T346588
		'POR' => 100, // T346588
		'ПОР' => 100, // T346588
		'RPOR' => 101, // T346588
		'РПОР' => 101, // T346588
		'MD' => 828, // T346588
		'МД' => 828, // T346588
		'MOD' => 828, // T346588
		'МОД' => 828, // T346588
		'RMD' => 829, // T346588
		'РМД' => 829, // T346588
		'RMOD' => 829, // T346588
		'РМОД' => 829, // T346588
		'VP' => NS_PROJECT,
		'ВП' => NS_PROJECT,
		'Vikipedija' => NS_PROJECT,
		'Википедија' => NS_PROJECT,
		'Razgovor_Wikipedija' => NS_PROJECT_TALK,
		'Razgovor_o_Wikipedia' => NS_PROJECT_TALK,
		'Разговор_о_Википедији' => NS_PROJECT_TALK,
		'Портал' => 100, // NS_PORTAL
		'Разговор_о_порталу' => 101, // NS_PORTAL_TALK
	],
	'+shiwiki' => [
		'Portal' => 100, // T288909
		'Portal_talk' => 101,
	],
	'+shnwiki' => [
		'WT' => NS_PROJECT_TALK, // T210699
	],
	'+shnwikibooks' => [ // T327850
		'Cookbook' => 102,
		'Cookbook_talk' => 103,
		'Transwiki' => 108,
		'Transwiki_talk' => 109,
		'Wikijunior' => 110,
		'Wikijunior_talk' => 111,
		'Subject' => 112,
		'Subject_talk' => 113,
	],
	'+shnwiktionary' => [ // T330376
		'Portal' => 100,
		'Portal_talk' => 101,
		'Appendix' => 102,
		'Appendix_talk' => 103,
		'Rhymes' => 106,
		'Rhymes_talk' => 107,
		'Reconstruction' => 110,
		'Reconstruction_talk' => 111,
	],
	'+siwiki' => [
		'Portal' => 100,
		'Portal_talk' => 101,
	],
	'+skwiki' => [
		'Užívateľ' => NS_USER, // T143472
		'Diskusia_s_užívateľom' => NS_USER_TALK,
	],
	'+slwiki' => [
		'Draft' => 118, // T332351
		'Draft_talk' => 119, // T332351
	],
	'+sqwikinews' => [
		'WL' => NS_PROJECT,
	],
	'+srwiki' => [
		'VP' => NS_PROJECT, // T286396
		'ВП' => NS_PROJECT, // T286396
		'Vikipedija' => NS_PROJECT,
		'Draft' => 118, // T223472
		'Draft_talk' => 119, // T223472
	],
	'+svwikisource' => [
		'Author' => 106,
		'Author_talk' => 107,
	],
	'+suwikisource' => [ // T344314
		'Author' => 102,
		'Author_talk' => 103,
	],
	'+svwiktionary' => [
		'WT-diskussion' => NS_PROJECT_TALK,
		'KAT' => NS_CATEGORY, // T214329
	],
	'+swwiki' => [
		'Portal' => 100,
		'Portal_talk' => 101,
	],
	'+tawiki' => [
		'WT' => NS_PROJECT_TALK, // T126604
		'Draft' => 118, // T329248
		'Draft_talk' => 119, // T329248
	],
	'+tawikisource' => [
		'Author' => 102, // T165813
		'Author_talk' => 103, // T165813
	],
	'+testwiki' => [
		'WT' => NS_PROJECT_TALK,
	],
	'+tewikiquote' => [
		'Wikiquote_చర్చ' => NS_PROJECT_TALK, // T89332
	],
	'+tewikisource' => [
		'Author' => 102,
		'Author_talk' => 103,
		'పేజీ' => 104,
		'పేజీ_చర్చ' => 105,
	],
	'+tewiktionary' => [
		'Wiktionary_చర్చ' => NS_PROJECT_TALK, // T38533
	],
	'+thwiki' => [
		'H' => NS_HELP,
	], // T70108
	'+thwikibooks' => [
		'คุยเรื่องWikibooks' => NS_PROJECT_TALK,
		'Cookbook' => 104, // T251118
		'COOK' => 104, // T251118
		'CAT' => NS_CATEGORY, // T251118
		'H' => NS_HELP, // T251118
		'SUB' => 102, // T251118
		'Subject' => 102, // T48153
		'Subject_talk' => 103, // T48153
		'Wikijunior' => 106, // T251118
		'WJ' => 106, // T251118
	],
	'+thwikisource' => [
		'ผู้ประพันธ์' => 102, // T236640
		'คุยเรื่องผู้ประพันธ์' => 103, // T236640
		'H' => NS_HELP, // T251134
		'CAT' => NS_CATEGORY, // T251134
		'Portal' => 100, // T251134
		'P' => 100, // T251134
		'Author' => 102, // T251134
		'Translation' => 114, // T251134
	],
	'+thwiktionary' => [
		'คุยเรื่องWiktionary' => NS_PROJECT_TALK,
		'Rhymes' => 104,
		'Rhymes_talk' => 105,
	],
	'+tiwiki' => [ // T263840
		'ዊኪፔዲያ' => NS_PROJECT,
		'ዊኪፔዲያ_ምይይጥ' => NS_PROJECT_TALK,
		'ማዕጾ_ምይይጥ' => 101,
	],
	'+tiwiktionary' => [ // T263840
		'ዊኪ-መዝገበ-ቃላት_ምይይጥ' => NS_PROJECT_TALK,
	],
	'+trwiki' => [
		'T' => NS_TALK, // T265336
		'K' => NS_USER, // T265336
		'VP' => NS_PROJECT, // T265336
		'Ş' => NS_TEMPLATE, // T265336
		'Y' => NS_HELP, // T265336
		'KAT' => NS_CATEGORY, // T265336
		'P' => 100, // T265336
		'VPR' => 102, // T265336
	],
	'+trwikibooks' => [
		'VK' => NS_PROJECT,
		'VÇ' => 110,
		'KAT' => NS_CATEGORY,
		'KİT' => 112,

		// T266608
		'T' => NS_TALK,
		'K' => NS_USER,
		'Ş' => NS_TEMPLATE,
		'Y' => NS_HELP,
	],
	'+trwikisource' => [
		'Author' => 100,
		'Author_talk' => 101,
		'VikiKaynak' => NS_PROJECT, // T44721
		'VikiKaynak_tartışma' => NS_PROJECT_TALK,
		'Portal_talk' => 201,

		// T266606
		'T' => NS_TALK,
		'K' => NS_USER,
		'VK' => NS_PROJECT,
		'Ş' => NS_TEMPLATE,
		'Y' => NS_HELP,
		'KAT' => NS_CATEGORY,
		'P' => 200,
	],
	'+trwikiquote' => [ // T266605
		'T' => NS_TALK,
		'K' => NS_USER,
		'VS' => NS_PROJECT,
		'Ş' => NS_TEMPLATE,
		'Y' => NS_HELP,
		'KAT' => NS_CATEGORY,
	],
	'+trwiktionary' => [ // T266609
		'T' => NS_TALK,
		'K' => NS_USER,
		'VS' => NS_PROJECT,
		'Ş' => NS_TEMPLATE,
		'Y' => NS_HELP,
		'KAT' => NS_CATEGORY,
		'P' => 100,
		'A' => 106,
	],
	'+trwikivoyage' => [
		'T' => NS_TALK, // T272782
		'K' => NS_USER, // T272782
		'VG' => NS_PROJECT, // T272782
		'Ş' => NS_TEMPLATE, // T272782
		'Y' => NS_HELP, // T272782
		'KAT' => NS_CATEGORY, // T272782
	],
	'+ttwiki' => [
		'ВП' => NS_PROJECT,
	],
	'+ukwiki' => [
		'ВП' => NS_PROJECT,
	],
	'+ukwikibooks' => [
		'ВП' => NS_PROJECT,
	],
	'+ukwikiquote' => [
		'ВЦ' => NS_PROJECT,
	],
	'+ukwikinews' => [
		'ВікіНовини' => NS_PROJECT, // T50843
		'ВН' => NS_PROJECT, // T50843
		'Обговорення_ВікіНовини' => NS_PROJECT_TALK, // T50843
		'Д' => NS_HELP, // T50843
		'К' => NS_CATEGORY,
		'Comments' => 102, // T47333
		'Comments_talk' => 103,
		'Incubator' => 104,
		'І' => 104, // T50843
		'Incubator_talk' => 105,
	],
	'+ukwikisource' => [
		'ВД' => NS_PROJECT,
		'Обговорення_Wikisource' => NS_PROJECT_TALK, // T50308
		'Author' => 102, // T50308
		'Author_talk' => 103,
	],
	'+ukwikivoyage' => [
		'Portal' => 100,
		'Portal_talk' => 101,
		'ВМ' => NS_PROJECT,
		'К' => NS_CATEGORY,
		'П' => 100,
		'Д' => NS_HELP,
	],
	'+ukwiktionary' => [
		'ВС' => NS_PROJECT,
	],
	'+urwiki' => [
		'وپ' => NS_PROJECT, // T122045
		'ویکیپیڈیا' => NS_PROJECT,
		'ویکیپیڈیا_تبادلۂ_خیال' => NS_PROJECT_TALK,
		'Portal' => 100, // T21569
		'Portal_talk' => 101, // T21569
		'Draft' => 118, // T223472
		'Draft_talk' => 119, // T223472
	],
	'+urwiktionary' => [
		'ول' => NS_PROJECT, // T186393
		'تبادلۂ_خیال_ول' => NS_PROJECT_TALK, // T186393
		'تھیسارس' => 110, // T186393
		'تبادلۂ خیال تھیسارس' => 111, // T186393
		'تعمیر_نو' => 118, // T186393
		'تبادلۂ_خیال_تعمیر_نو' => 119, // T186393
		'Index' => 104,
		'Index_talk' => 105,
		'Rhymes' => 106,
		'Rhymes_talk' => 107,
		'Transwiki' => 108,
		'Transwiki_talk' => 109,
		'Thesaurus' => 110,
		'Thesaurus_talk' => 111,
		'Citations' => 114,
		'Citations_talk' => 115,
		'Sign_gloss' => 116,
		'Sign_gloss_talk' => 117,
		'Reconstruction' => 118,
		'Reconstruction_talk' => 119,
	],
	'+uzwiki' => [
		'VP' => NS_PROJECT, // T48534
		'VM' => NS_PROJECT_TALK, // T48534
	],
	'+vecwikisource' => [
		'Author' => 100,
		'Author_talk' => 101,
	],
	'+wawikisource' => [
		'Author' => 100, // T269431
		'Author_talk' => 101, // T269431
	],
	'+wikidata' => [
		'Item' => NS_MAIN,
		'Item_talk' => NS_TALK,
		'WD' => NS_PROJECT, // T43834
		'WT' => NS_PROJECT_TALK,
		'P' => 120, // T47079
		'L' => 146, // T195493
		'E' => 640, // T245529
	],
	'+wikifunctionswiki' => [
		'ZObject' => NS_MAIN,
		'ZObject_talk' => NS_TALK,
		'WF' => NS_PROJECT, // T342964
		'WT' => NS_PROJECT_TALK, // T342964
	],
	'+yiwiki' => [
		'וויקיפעדיע' => NS_PROJECT,
		'וויקיפעדיע_רעדן' => NS_PROJECT_TALK,
	],
	'+yiwikisource' => [
		'וויקיביבליאטעק' => NS_PROJECT,
		'וויקיביבליאטעק_רעדן' => NS_PROJECT_TALK
	],
	'+yiwiktionary' => [
		'וויקיווערטערבוך' => NS_PROJECT,
		'וויקיווערטערבוך_רעדן' => NS_PROJECT_TALK
	],
	'+yowiki' => [
		'Portal' => 100,
		'Portal_talk' => 101,
		'Book' => 108,
		'Book_talk' => 109,
	],
	'+vecwiki' => [
		'Immagine' => NS_FILE,
	],
	'+viwiki' => [
		'Chủ_đề' => 100,
		'Thảo_luận_Chủ_đề' => 101,
	],
	'+viwikibooks' => [
		'Subject' => 102,
		'Subject_talk' => 103,
		'Wikijunior' => 104,
		'Wikijunior_talk' => 105,
		'Cookbook' => 106,
		'Cookbook_talk' => 107,
	],
	'+viwikisource' => [
		'Portal' => 100,
		'Portal_talk' => 101,
		'Author' => 102,
		'Author_talk' => 103,
		'Translation' => 114, // T290691
		'Translation_talk' => 115,
	],
	'+yuewiktionary' => [ // T212678
		'Index' => 100,
		'Index_talk' => 101,
		'Appendix' => 102,
		'Appendix_talk' => 103,
		'T' => NS_TEMPLATE,
		'U' => NS_USER,
		'CAT' => NS_CATEGORY,
	],
	'+wuuwiki' => [
		// English aliases - T124389
		'T' => NS_TEMPLATE,

		// Wu aliases - T124389, T128354
		'媒体' => NS_MEDIA,
		'特殊' => NS_SPECIAL,
		'讨论' => NS_TALK,
		'用户' => NS_USER,
		'用户讨论' => NS_USER_TALK,
		'维基百科' => NS_PROJECT,
		'文件' => NS_FILE,
		'模板' => NS_TEMPLATE,
		'模板讨论' => NS_TEMPLATE_TALK,
		'帮助' => NS_HELP,
		'帮助讨论' => NS_HELP_TALK,
		'分类' => NS_CATEGORY,
		'模块' => 828, // NS_MODULE
	],
	'+zh_classicalwiki' => [
		'H' => NS_HELP, // T162547
		'WT' => NS_PROJECT_TALK, // T162547
		'T' => NS_TEMPLATE, // T162547
		'CAT' => NS_CATEGORY, // T162547
		'P' => 100, // T162547
		'Portal' => 100, // T162547
		'Portal_talk' => 101, // T162547
		'Draft' => 106, // T223472
		'Draft_talk' => 107, // T223472
	],
	'+zhwiki' => [
		'U' => NS_USER, // T183711
		'UT' => NS_USER_TALK, // T183711
		'维基百科' => NS_PROJECT,
		'維基百科' => NS_PROJECT,
		'WT' => NS_PROJECT_TALK,
		'维基百科讨论' => NS_PROJECT_TALK,
		'维基百科对话' => NS_PROJECT_TALK,
		'維基百科討論' => NS_PROJECT_TALK,
		'維基百科對話' => NS_PROJECT_TALK,
		'T' => NS_TEMPLATE,
		'H' => NS_HELP,
		'CAT' => NS_CATEGORY,
		'P' => 100,
		'主题' => 100, // T184866
		'主題' => 100, // T184866
		'主题讨论' => 101, // T184866
		'主题对话' => 101,
		'主題討論' => 101, // T184866
		'主題對話' => 101,
		'PJ' => 102, // T271612
		'WPJ' => 102, // T271612
		'专题' => 102, // T271612
		'專題' => 102, // T271612
		'维基专题' => 102, // T271612
		'維基專題' => 102, // T271612
		'PJT' => 103, // T271612
		'WPJT' => 103, // T271612
		'专题讨论' => 103, // T271612
		'专题对话' => 103, // T271612
		'專題討論' => 103, // T271612
		'專題對話' => 103, // T271612
		'维基专题讨论' => 103, // T271612
		'维基专题对话' => 103, // T271612
		'維基專題討論' => 103, // T271612
		'維基專題對話' => 103, // T271612
		'草稿' => 118, // T91223
	],
	'+zhwikibooks' => [
		'维基教科书' => NS_PROJECT,
		'維基教科書' => NS_PROJECT,
		'CAT' => NS_CATEGORY, // T46308
		'WJ' => 110, // T46308, Wikijunior
		'维基儿童' => 110, // T46308
		'維基兒童' => 110, // T46308
		'SB' => 112, // T46308, Subject
		'書架' => 112, // T46308
		'书架' => 112, // T46308
	],
	'+zhwikinews' => [
		'U' => NS_USER, // T266925
		'UT' => NS_USER_TALK, // T266925
		'T' => NS_TEMPLATE, // T266925
		'H' => NS_HELP, // T266925
		'CAT' => NS_CATEGORY, // T266925
		'频道' => 100, // T257101
		'頻道' => 100, // T257101
		'频道讨论' => 101,
		'頻道討論' => 101,
	],
	'+zhwikiquote' => [
		'U' => NS_USER, // T240428
		'UT' => NS_USER_TALK, // T240428
		'WT' => NS_PROJECT_TALK, // T181374
		'T' => NS_TEMPLATE, // T181374
		'H' => NS_HELP, // T181374
		'CAT' => NS_CATEGORY, // T181374
	],
	'+zhwikisource' => [
		'CAT' => NS_CATEGORY, // T230548
		'作者' => 102, // Author
		'作者讨论' => 103, // Author_talk
		'作者討論' => 103,
		'翻译' => 114, // T66127
		'翻譯' => 114, // T66127
		'翻译讨论' => 115, // T66127
		'翻譯討論' => 115, // T66127
	],
	'+zhwikiversity' => [
		'U' => NS_USER, // T202821
		'UT' => NS_USER_TALK, // T202821
		'维基学院' => NS_PROJECT, // T207544
		'維基學院' => NS_PROJECT, // T207544
		'WT' => NS_PROJECT_TALK, // T202821
		'维基学院讨论' => NS_PROJECT_TALK, // T207544
		'維基學院討論' => NS_PROJECT_TALK, // T207544
		'T' => NS_TEMPLATE, // T202821
		'H' => NS_HELP, // T202821
		'CAT' => NS_CATEGORY, // T202821
		'学部' => 100, // T201675
		'學部' => 100, // T201675
		'学部讨论' => 101, // T201675
		'學部討論' => 101, // T201675
		'学院' => 102, // T201675
		'學院' => 102, // T201675
		'系' => 102, // T201675
		'院系' => 102, // T212919
		'学院讨论' => 103, // T201675
		'學院討論' => 103, // T201675
		'系讨论' => 103, // T201675
		'系討論' => 103, // T201675
		'院系讨论' => 103, // T212919
		'院系討論' => 103, // T212919
		'学科' => 104, // T201675
		'學科' => 104, // T201675
		'学科讨论' => 105, // T201675
		'學科討論' => 105, // T201675
		'实验' => 106, // T212919
		'實驗' => 106, // T212919
		'实验讨论' => 107, // T212919
		'實驗討論' => 107, // T212919
		'课程' => 108, // T212919
		'課程' => 108, // T212919
		'课程讨论' => 109, // T212919
		'課程討論' => 109, // T212919
	],
	'+zhwikivoyage' => [
		'U' => NS_USER, // T198007
		'UT' => NS_USER_TALK, // T198007
		'维基导游' => NS_PROJECT, // T62122
		'維基導遊' => NS_PROJECT, // T62122
		'WT' => NS_PROJECT_TALK, // T198007
		'T' => NS_TEMPLATE, // T198007
		'H' => NS_HELP, // T198007
		'CAT' => NS_CATEGORY, // T198007
	],
	'+zhwiktionary' => [
		'T' => NS_TEMPLATE, // T286101
		'CAT' => NS_CATEGORY, // T286101
		'附录' => 100, // T257101
		'附錄' => 100, // T222024
		'附录讨论' => 101, // T257101
		'附錄討論' => 101, // T222024
		'韵部' => 106, // T241023
		'韻部' => 106, // T241023
		'韵部讨论' => 107, // T241023
		'韻部討論' => 107, // T241023
		'词库' => 110, // T309564
		'詞庫' => 110, // T309564
		'词库讨论' => 111, // T309564
		'詞庫討論' => 111, // T309564
		'引文' => 114, // T309564
		'引文讨论' => 115, // T309564
		'引文討論' => 115, // T309564
		'RC' => 118, // T286101
		'重构' => 118, // T286101
		'重構' => 118, // T286101
		'重建' => 118, // T286101
		'重构讨论' => 119, // T286101
		'重建讨论' => 119, // T286101
		'重構討論' => 119, // T286101
		'重建討論' => 119, // T286101
	],
	'+zh_yuewiki' => [
		'WT' => NS_PROJECT_TALK,
		'T' => NS_TEMPLATE,
		'H' => NS_HELP,
		'P' => 100,
		# 'PT' => 101,
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
	],
],
# @} end of wgNamespaceAliases

# wgNamespacesWithSubpages @{
'wgNamespacesWithSubpages' => [
	'default' => [
		NS_TALK /* 1 */ => true,
		NS_USER /* 2 */ => true,
		NS_USER_TALK /* 3 */ => true,
		NS_PROJECT /* 4 */ => true,
		NS_PROJECT_TALK /* 5 */ => true,
		NS_FILE_TALK /* 7 */ => true,
		NS_MEDIAWIKI /* 8 */ => true,
		NS_MEDIAWIKI_TALK /* 9 */ => true,
		NS_TEMPLATE /* 10 */ => true,
		NS_TEMPLATE_TALK /* 11 */ => true,
		NS_HELP /* 12 */ => true,
		NS_HELP_TALK /* 13 */ => true,
		NS_CATEGORY_TALK /* 15 */ => true,
		// extra namespaces usually want subpages
		100 => true,
		101 => true,
		102 => true,
		103 => true,
		104 => true,
		105 => true,
		106 => true,
		107 => true,
		108 => true,
		109 => true,
		110 => true,
		111 => true,
		112 => true,
		113 => true,
		114 => true,
		115 => true,
		116 => true,
		117 => true,
		118 => true,
		119 => true,
	],

	// Wikipedia @{
	'+arwiki' => [ 14 => 1 ], // T121985
	'+cswiki' => [ 14 => 1 ],
	'+dewiki' => [ 8 => 0 ],
	'+enwiki' => [ 8 => 0 ],
	'+eowiki' => [ 8 => 0 ], // 0 => 1 removed per T57563
	'+eswiki' => [ 8 => 0 ],
	'+frwiki' => [ 8 => 0 ],
	'+guwiki' => [ 8 => 0, 14 => 1 ],
	'+huwiki' => [ 8 => 0, 14 => 1, 118 => 0 ], // 118 added per T333083
	'+idwiki' => [ 8 => 0 ],
	'+itwiki' => [ 8 => 0, 14 => 0 ],
	'+kkwiki' => [ 8 => 0, 12 => 0 ],
	'+ltwiki' => [ 8 => 0 ],
	'+plwiki' => [ 8 => 0, 14 => 1 ],
	'+rmwiki' => [ 0 => 1 ],
	'+slwiki' => [ 8 => 0, 14 => 1 ],
	'+tenwiki' => [ 0 => 1 ],
	'+zh_yuewiki' => [ 8 => 0, 14 => 1 ],
	'+zh_min_nanwiki' => [ 8 => 0 ],
	// @}

	// Specials wiki @{
	'+apiportalwiki' => [ NS_MAIN => true ], // to enable subpage navigation
	'+arbcom_cswiki' => [ 0 => 1 ],
	'+arbcom_enwiki' => [ 0 => 1, 8 => 0 ],
	'+arbcom_nlwiki' => [ 0 => 1 ], // T147186
	'+auditcomwiki' => [ 0 => 1, 8 => 0 ],
	'+bewikimedia' => [ 0 => 1 ],
	'+boardwiki' => [ 0 => 1, 8 => 0 ],
	'+brwikimedia' => [ 0 => 1 ],
	'+cawikimedia' => [ 0 => 1 ], // T185436
	'+chairwiki' => [ 0 => 1, 8 => 0 ],
	'+chapcomwiki' => [ 0 => 1, 8 => 0 ],
	'+checkuserwiki' => [ 0 => 1 ],
	'+collabwiki' => [ 0 => 1 ],
	'+commonswiki' => [ 8 => 0 ],
	'+ecwikimedia' => [ 0 => 1 ],
	'+execwiki' => [ 0 => 1, 8 => 0 ],
	'+fdcwiki' => [ 0 => 1 ],
	'+foundationwiki' => [ 0 => 1, 8 => 0 ],
	'+gewikimedia' => [ 0 => 1 ],
	'+grwikimedia' => [ 0 => 1 ], // T245911
	'+grantswiki' => [ 0 => 1, 8 => 0 ],
	'+incubatorwiki' => [ 0 => 1, 14 => 1 ],
	'+internalwiki' => [ 0 => 1, 8 => 0 ],
	'+wikitech' => [ 0 => 1, 110 => 1, 498 => 1, 666 => 1 ],
	'+legalteamwiki' => [ 0 => 1 ],
	'+mediawikiwiki' => [ 0 => 1, 8 => 0, 14 => 1 ],
	'+metawiki' => [ 0 => 1, 8 => 0, 200 => 1, 201 => 1, 202 => 1, 203 => 1, 208 => 1, 209 => 1 ],
	'+movementroleswiki' => [ 0 => 1 ],
	'+nlwikimedia' => [ 0 => 1 ],
	'+nostalgiawiki' => [ 0 => 1, 8 => 0 ],
	'+nowikimedia' => [ 0 => 1 ],
	'+officewiki' => [ 0 => 1, 8 => 0 ], // T99860 et al.
	'+ombudsmenwiki' => [ 0 => 1 ],
	'+otrs_wikiwiki' => [ 0 => 1 ],
	'+outreachwiki' => [ 0 => 1 ],
	'+plwikimedia' => [ 0 => 1, 8 => 0, 14 => 1 ],
	'+ptwikimedia' => [ 0 => 1 ], // T362300
	'+romdwikimedia' => [ 0 => 1 ], // T318491
	'+rswikimedia' => [ 0 => 1, 8 => 0 ],
	'+ruwikimedia' => [ 0 => 1, 6 => 1, 7 => 0, 9 => 0, 14 => 1, 15 => 0 ],
	'+sewikimedia' => [ 0 => 1 ],
	'+stewardwiki' => [ 0 => 1, 6 => 1, 14 => 1 ],
	'+strategywiki' => [ 0 => 1 ],
	'+sysop_itwiki' => [ 0 => 1 ],
	'+techconductwiki' => [ 0 => 1 ], // T260350
	'+u4cwiki' => [ 0 => 1 ],
	'+uawikimedia' => [ 0 => 1 ], // T91185
	'+usabilitywiki' => [ 0 => 1 ],
	'+wikidata' => [ 121 => 1 ], // T146271
	'+wikimaniateamwiki' => [ 0 => 1, 8 => 0 ],

	'+wikimania' => [ 0 => 1, 8 => 0, ],
	// @}

	// Wikibooks @{
	'+wikibooks' => [ 0 => 1, 8 => 0 ],
	'+cswikibooks' => [ 0 => 1, 14 => 1 ],
	'+enwikibooks' => [ 0 => 1, 8 => 0, 14 => 1 ],
	'+plwikibooks' => [ 0 => 1, 8 => 0, 14 => 1 ],
	'+ptwikibooks' => [ 0 => 1, 8 => 0, 14 => 1 ],
	'+ukwikibooks' => [ 0 => 1, 8 => 0, 14 => 1 ],
	// @}

	// Wikisource @{
	'+wikisource' => [ 0 => 1, 8 => 0 ],
	'+cswikisource' => [ 14 => 1 ],
	'+napwikisource' => [ 250 => 1, 251 => 1 ], // T252755
	'+plwikisource' => [ 124 => 1, 125 => 1 ], // T154711
	'+sourceswiki' => [ 0 => 1 ], // T367674
	'+ukwikisource' => [ 250 => 1, 251 => 1 ], // T255930
	// @}

	// Wikiversity @{
	'+arwikiversity' => [ 0 => 1, 6 => 1 ],
	'+betawikiversity' => [ 0 => 1 ],
	'+cswikiversity' => [ 0 => 1, 14 => 1 ],
	'+dewikiversity' => [ 0 => 1, 8 => 0 ],
	'+enwikiversity' => [ 0 => 1, 8 => 0 ],
	'+eswikiversity' => [
		0 => 1, // T155498
	],
	'+fiwikiversity' => [ 0 => 1, 8 => 0 ],
	'+frwikiversity' => [ 0 => 1, 8 => 0, 112 => 1, 113 => 1 ],
	'+hiwikiversity' => [ 0 => 1 ], // T180913
	'+itwikiversity' => [ 0 => 1, 8 => 0, 14 => 0 ],
	'+kowikiversity' => [ 0 => 1 ], // T228481
	'+ptwikiversity' => [ 0 => 1 ],
	'+ruwikiversity' => [ 0 => 1, 6 => 1, 14 => 1 ],
	'+svwikiversity' => [ 0 => 1 ],
	'+zhwikiversity' => [ 0 => 1 ], // T202007
	// @}

	// Wikiquote
	'+cswikiquote' => [ 0 => 1, 14 => 1 ],
	'+plwikiquote' => [ 8 => 0, 14 => 1 ],

	// Wikinews
	'+cswikinews' => [ 0 => 1, 14 => 1 ],
	'+plwikinews' => [ 8 => 0, 14 => 1 ],

	// Wiktionary
	'+cswiktionary' => [ 0 => 1, 14 => 1 ],
	'+iswiktionary' => [ 0 => 1, 8 => 0, 14 => 1 ],
	'+ltwiktionary' => [ 8 => 0, 14 => 1 ],
	'+plwiktionary' => [ 8 => 0, 14 => 1 ],

	// Wikivoyage
	'+dewikivoyage' => [ 0 => 1, 100 => 0, 104 => 0 ],
	'+itwikivoyage' => [ 0 => 1, 100 => 0 ],

	// Wikimania
	'+wikimaniawiki' => [
		120 => 1, 122 => 1, 124 => 1, 126 => 1, 128 => 1, // T220950
		136 => 1, 138 => 1, 140 => 1, 142 => 2, // T347622
	],
],
# @} end of wgNamespacesWithSubpages

# wgExtraNamespaces @{
	// Recommended namespace numbers:
	// * 100 - Portal
	// * 102 - WikiProject
	// * 104 - Reference
	// * 114 - Translation
	// Wikis are sorted by family (type), and then alphabetically

'wgExtraNamespaces' => [
	// Meta wiki @{
	'metawiki' => [
		200 => 'Grants', // T24810
		201 => 'Grants_talk',
		202 => 'Research', // T30742
		203 => 'Research_talk',
		206 => 'Iberocoop', // T40398
		207 => 'Iberocoop_talk',
	],
	// @}

	# Wikimania wikis @{
	'wikimania2005wiki' => [
		100 => 'Internal',
		101 => 'Internal_talk',
	],
	'wikimania' => [
		100 => 'Proceedings',
		101 => 'Proceedings_talk',
	],
	'wikimaniawiki' => [
		100 => '2005',
		101 => '2005_talk',
		102 => '2006',
		103 => '2006_talk',
		104 => '2007',
		105 => '2007_talk',
		106 => '2008',
		107 => '2008_talk',
		108 => '2009',
		109 => '2009_talk',
		110 => '2010',
		111 => '2010_talk',
		112 => '2011',
		113 => '2011_talk',
		114 => '2012',
		115 => '2012_talk',
		116 => '2013',
		117 => '2013_talk',
		118 => '2014',
		119 => '2014_talk',
		120 => '2015',
		121 => '2015_talk',
		122 => '2016',
		123 => '2016_talk',
		124 => '2017',
		125 => '2017_talk',
		126 => '2018',
		127 => '2018_talk',
		128 => '2019',
		129 => '2019_talk',
		130 => '2020',
		131 => '2020_talk',
		132 => '2021',
		133 => '2021_talk',
		134 => '2022', // T295267
		135 => '2022_talk', // T295267
		136 => '2023', // T316928
		137 => '2023_talk', // T316928
		138 => '2024', // T332782
		139 => '2024_talk', // T332782
		140 => '2025', // T347622
		141 => '2025_talk', // T347622
		142 => '2026', // T347622
		143 => '2026_talk', // T347622
	],
	# @}

	# special wikis @{
	'arbcom_enwiki' => [
		100 => 'Case',
		101 => 'Case_talk',
		102 => 'Wpuser',
		103 => 'Wpuser_talk',
	],
	'arbcom_dewiki' => [
		100 => 'Anfrage',
		101 => 'Anfrage_Diskussion',
	],
	'bdwikimedia' => [
		100 => 'কর্মশালা', // Workshop - T195700
		101 => 'কর্মশালা_আলোচনা', // Workshop talk - T195700
		102 => 'আড্ডা', // Meetup - T195700
		103 => 'আড্ডা_আলোচনা', // Meetup talk - T195700
		104 => 'ফটোওয়াক', // Photowalk - T205747
		105 => 'ফটোওয়াক_আলোচনা', // Photowalk Talk - T205747
		106 => 'কার্যক্রম', // Program - T351903
		107 => 'কার্যক্রম_আলোচনা', // Program Talk - T351903
	],
	'boardwiki' => [
		100 => 'Resolution',
		101 => 'Resolution_talk',
	],
	'commonswiki' => [
		100 => 'Creator',
		101 => 'Creator_talk',
		102 => 'TimedText', # For video subtitles -- BV 2009-11-08
		103 => 'TimedText_talk',
		104 => 'Sequence',
		105 => 'Sequence_talk',
		106 => 'Institution',
		107 => 'Institution_talk',
	],
	'testcommonswiki' => [
		100 => 'Creator',
		101 => 'Creator_talk',
		102 => 'TimedText', # For video subtitles -- BV 2009-11-08
		103 => 'TimedText_talk',
		104 => 'Sequence',
		105 => 'Sequence_talk',
		106 => 'Institution',
		107 => 'Institution_talk',
	],
	'chapcomwiki' => [
		100 => 'Draft', // T66123
		101 => 'Draft_talk',
		102 => 'Affiliate', // T71549
		103 => 'Affiliate_talk',
	],
	'foundationwiki' => [
		100 => 'Resolution', // T132746
		101 => 'Resolution_talk', // T132746
		102 => 'Legal', // T206173
		103 => 'Legal_talk', // T206173
		104 => 'Minutes', // T206173
		105 => 'Minutes_talk', // T206173
		106 => 'Policy', // T206173
		107 => 'Policy_talk', // T206173
		108 => 'Endowment', // T347762
		109 => 'Endowment_talk', // T347762
		110 => 'Agenda', // T347822
		111 => 'Agenda_talk', // T347822
		112 => 'Archive', // T206173
		113 => 'Archive_talk', // T206173
		114 => 'Memory', // T348268
		115 => 'Memory_talk', // T348268
		116 => 'Committee', // T347822
		117 => 'Committee_talk', // T347822
	],
	'wikitech' => [
		110 => 'Obsolete',
		111 => 'Obsolete_talk',
		112 => 'OfficeIT', // T123383
		113 => 'OfficeIT_talk',
		// NS 114/115 reserved for 'Translation'
		116 => 'Tool', // T122865
		117 => 'Tool_talk', // T122865
		// TODO: rename this namespace (T275796)
		498 => 'Nova_Resource',
		499 => 'Nova_Resource_Talk',
		666 => 'Heira',
		667 => 'Heira_Talk',
	],
	'mediawikiwiki' => [
		100 => 'Manual',
		101 => 'Manual_talk',
		102 => 'Extension',
		103 => 'Extension_talk',
		104 => 'API',
		105 => 'API_talk',
		106 => 'Skin',
		107 => 'Skin_talk',
	],
	'nlwikimedia' => [
		100 => 'De_Wikiaan',
		101 => 'Overleg_De_Wikiaan',
	],
	'nowikimedia' => [
		100 => 'Prosjekt', // T181625
		101 => 'Prosjektdiskusjon', // T181625
	],
	'officewiki' => [
		100 => 'Report',
		101 => 'Report_talk',
		110 => 'Outreach',
		111 => 'Outreach_talk',
		112 => 'Archive',
		113 => 'Archive_talk',
	],
	'otrs_wikiwiki' => [
		100 => 'Response',
		101 => 'Response_talk'
	],
	'ruwikimedia' => [
		100 => 'Пресс-релизы', // T95110 (press releases)
		101 => 'Обсуждение_пресс-релизов',
		102 => 'Отчёты', // T95110 (reports)
		103 => 'Обсуждение_отчётов',
	],
	'sewikimedia' => [
		100 => 'Projekt',
		101 => 'Projektdiskussion',
	],
	'stewardwiki' => [ // T30773
		100 => 'Case',
		101 => 'Case_talk',
		102 => 'Archive',
		103 => 'Archive_talk',
		104 => 'Proposal',
		105 => 'Proposal_talk',
	],
	'strategywiki' => [
		106 => "Proposal",
		107 => "Proposal_talk",
	],
	'testwiki' => [
		100 => 'Test_namespace_1',
		101 => 'Test_namespace_1_talk',
		102 => 'Test_namespace_2',
		103 => 'Test_namespace_2_talk',
		118 => 'Draft', // T198143
		119 => 'Draft_talk',
	],
	'test2wiki' => [
		100 => 'Portal',
		101 => 'Portal_talk',
		102 => 'Author',
		103 => 'Author_talk',
		104 => 'Page',
		105 => 'Page_talk',
		106 => 'Index',
		107 => 'Index_talk',
	],
	'usabilitywiki' => [
		100 => 'Multimedia',
		101 => 'Multimedia_talk',
	],
	'wikidata' => [
		120 => 'Property',
		121 => 'Property_talk',
		122 => 'Query',
		123 => 'Query_talk',
	],
	# @} end of special wikis

	// Wikipedia @{
	'afwiki' => [
		100 => 'Portaal',
		101 => 'Portaalbespreking',
	],
	'alswiki' => [
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
	],
	'amwiki' => [
		100 => 'በር', // T30727
		101 => 'በር_ውይይት',
	],
	'anwiki' => [
		100 => 'Portal',
		101 => 'Descusión_Portal',
	],
	'arwiki' => [
		100 => 'بوابة',
		101 => 'نقاش_البوابة',
	],
	'arywiki' => [
		100 => 'قيسارية', // Portal, T291737
		101 => 'لمداكرة_د_لقيسارية', // Portal Talk, T291737
		118 => 'واساخ', // Draft, T291737
		119 => 'لمداكرة_د_لواساخ', // Draft Talk, T291737
	],
	'arzwiki' => [
		100 => 'بوابة',
		101 => 'مناقشة_بوابة',
	],
	'aswiki' => [
		100 => "ৱিকিচ'ৰা", // Portal
		101 => "ৱিকিচ'ৰা_আলোচনা", // Portal talk
	],
	'azwiki' => [
		100 => 'Portal',
		101 => 'Portal_müzakirəsi',
		118 => 'Qaralama', // Draft - T299332
		119 => 'Qaralama_müzakirəsi',
	],
	'azbwiki' => [
		100 => 'پوْرتال', // T106305
		101 => 'پوْرتال_دانیشیغی', // T106305
	],
	'bawiki' => [
		100 => 'Портал', // T44077 - Portal
		101 => 'Портал_буйынса_фекерләшеү',
		102 => 'Проект', // T44077 - Project
		103 => 'Проект_буйынса_фекерләшеү',
	],
	'barwiki' => [
		100 => 'Portal',
		101 => 'Portal_Dischkrian', // T43962 comment #7
		102 => 'Buach', // T43962 - Wikibooks alt.
		103 => 'Buach_Dischkrian',
		104 => 'Woat', // T43962 - Wiktionary alt.
		105 => 'Woat_Dischkrian',
		106 => 'Text', // T43962 - Wikisource alt.
		107 => 'Text_Dischkrian',
		108 => 'Spruch', // T43962 - Wikiquote alt.
		109 => 'Spruch_Dischkrian',
		110 => 'Nochricht', // T43962 - Wikinews alt.
		111 => 'Nochricht_Dischkrian',
	],
	'bewiki' => [
		100 => 'Партал', // Portal
		101 => 'Размовы_пра_партал', // Portal talk
	],
	'be_x_oldwiki' => [
		100 => 'Партал',
		101 => 'Абмеркаваньне_парталу',
	],
	'bgwiki' => [
		100 => 'Портал',
		101 => 'Портал_беседа',
		118 => 'Чернова', // T299224 - Draft
		119 => 'Чернова_беседа',
	],
	'bjnwiki' => [
		100 => 'Lawang', // T259429 Portal
		101 => 'Pamandiran_Lawang', // T259429 Portal talk
	],
	'bnwiki' => [
		100 => 'প্রবেশদ্বার',
		101 => 'প্রবেশদ্বার_আলোচনা',
		118 => 'খসড়া', // Draft - T350133
		119 => 'খসড়া_আলোচনা', // Draft talk - T350133
	],
	'bpywiki' => [
		100 => 'হমিলদুৱার', // Portal
		101 => 'হমিলদুৱার_য়্যারী', // Portal_talk
	],
	'bswiki' => [
		100 => 'Portal', // T39226
		101 => 'Razgovor_o_portalu', // T39226
	],
	'cawiki' => [
		100 => 'Portal', 101 => 'Portal_Discussió',
		102 => 'Viquiprojecte', 103 => 'Viquiprojecte_Discussió',
	],
	'cewiki' => [
		NS_PROJECT_TALK => 'Википедин_дийцаре', // T49574
		100 => 'Ков',
		101 => 'Ков_дийцаре',
	],
	'ckbwiki' => [
		100 => 'دەروازە',
		101 => 'لێدوانی_دەروازە',
		102 => 'ویکیپرۆژە', // T54665
		103 => 'لێدوانی_ویکیپرۆژە', // T54665
		118 => 'ڕەشنووس', // T332470
		119 => 'لێدوانی_ڕەشنووس', // T332470
	],
	'cswiki' => [
		NS_USER => 'Wikipedista', # language default set back in wgNamespaceAliases
		NS_USER_TALK => 'Diskuse_s_wikipedistou', # language default set back in wgNamespaceAliases
		100 => 'Portál',
		101 => 'Diskuse_k_portálu',
		102 => 'Rejstřík',
		103 => 'Diskuse_k_rejstříku',
	],
	'cywiki' => [
		100 => 'Porth', // T29684
		101 => 'Sgwrs_Porth', // T29684
	],
	'dawiki' => [
		NS_PROJECT_TALK   => 'Wikipedia-diskussion', // T29902
		NS_MEDIAWIKI_TALK => 'MediaWiki-diskussion', // T29902
		NS_HELP_TALK      => 'Hjælp-diskussion', // T29902
		100 => 'Portal',
		101 => 'Portaldiskussion',
		102 => 'Artikeldata',
		103 => 'Artikeldatadiskussion'
	],
	'+dagwiki' => [
		104 => 'Salima', // T289911
		105 => 'Salima_yɛltɔɣa',
	],
	'dewiki' => [
		100 => 'Portal',
		101 => 'Portal_Diskussion'
	],
	'diqwiki' => [
		100 => 'Portal', // T133702
		101 => 'Portal_vaten'
	],
	'dvwiki' => [
		NS_PROJECT_TALK => 'ވިކިޕީޑިއާ_ޚިޔާލު', // T50075
		100 => 'ނެރު',
		101 => 'ނެރު_ޚިޔާލު',
	],
	'elwiki' => [
		100 => 'Πύλη',
		101 => 'Συζήτηση_πύλης'
	],
	'enwiki' => [
		100 => 'Portal',
		101 => 'Portal_talk',
		# 106 => 'Table',
		# 107 => 'Table_talk',
		// 108, 109 were used for Book namespace, deleted as part of T285766
		118 => 'Draft', // T59569
		119 => 'Draft_talk',
	],
	'eowiki' => [
		100 => 'Portalo',
		101 => 'Portala_diskuto',
		102 => 'Projekto',
		103 => 'Projekta_diskuto',
	],
	'eswiki' => [
		100 => 'Portal',
		101 => 'Portal_discusión',
		102 => 'Wikiproyecto',
		103 => 'Wikiproyecto_discusión',
		104 => 'Anexo', // T11304
		105 => 'Anexo_discusión',
	],
	'etwiki' => [
		100 => 'Portaal',
		101 => 'Portaali_arutelu',
		102 => 'Mustand', // Draft - T166887
		103 => 'Mustandi_arutelu',
	],
	'euwiki' => [
		100 => 'Atari', // portal
		101 => 'Atari_eztabaida',
		102 => 'Wikiproiektu',
		103 => 'Wikiproiektu_eztabaida',
		104 => 'Txikipedia', // T191396
		105 => 'Txikipedia_eztabaida', // T191396
		106 => 'Zerrenda', // T209834 - List
		107 => 'Zerrenda_eztabaida', // T209834 - List talk
	],
	'fawiki' => [
		100 => 'درگاه',
		101 => 'بحث_درگاه',
		# 102 => 'کتاب', // Book - T302957
		# 103 => 'بحث_کتاب', // Book talk - T302957
		118 => 'پیش‌نویس', // T92760
		119 => 'بحث_پیش‌نویس', // T92760
	],
	'fiwiki' => [
		100 => 'Teemasivu',
		101 => 'Keskustelu_teemasivusta',
		102 => 'Metasivu',
		103 => 'Keskustelu_metasivusta',
		104 => 'Kirja',
		105 => 'Keskustelu_kirjasta',
		106 => 'Wikiprojekti', // T156621
		107 => 'Keskustelu_wikiprojektista', // T156621
	],
	'frrwiki' => [ // Per T40023
		102 => 'Seite',
		103 => 'Seite_Diskussion',
		104 => 'Index',
		105 => 'Index_Diskussion',
		106 => 'Text',
		107 => 'Text_Diskussion',
	],
	'frwiki' => [
		100 => 'Portail',
		101 => 'Discussion_Portail',
		102 => 'Projet',
		103 => 'Discussion_Projet',
		104 => 'Référence',
		105 => 'Discussion_Référence',
	],
	'glwiki' => [
		100 => 'Portal',
		101 => 'Conversa_portal', // T43213
		102 => 'Libro',
		103 => 'Conversa_libro',
	],
	'hewiki' => [
		100 => 'פורטל',
		101 => 'שיחת_פורטל',
		108 => 'ספר', // Book
		109 => 'שיחת_ספר', // Book talk
		118 => 'טיוטה', // Draft - T86329
		119 => 'שיחת_טיוטה',
	],
	'hiwiki' => [
		100 => 'प्रवेशद्वार',
		101 => 'प्रवेशद्वार_वार्ता',
	],
	'hrwiki' => [
		100 => 'Portal',
		101 => 'Razgovor_o_portalu',
		102 => 'Dodatak',
		103 => 'Razgovor_o_dodatku',
		118 => 'Nacrt', // T268740 - Draft
		119 => 'Razgovor_o_nacrtu', // T268740 - Draft_talk
	],
	'huwiki' => [
		100 => 'Portál',
		101 => 'Portálvita',
		118 => 'Cikkjelölt', // T333083
		119 => 'Cikkjelöltvita', // T333083
	],
	'hywiki' => [
		100 => 'Պորտալ',
		101 => 'Պորտալի_քննարկում',
		102 => 'Վիքինախագիծ', // T259987
		103 => 'Վիքինախագծի_քննարկում',
		118 => 'Սևագիր', // T259987
		119 => 'Սևագրի_քննարկում',
	],
	'iawiki' => [
		100 => 'Portal',
		101 => 'Discussion_Portal',
		102 => 'Appendice',
		103 => 'Discussion_Appendice',
	],
	'idwiki' => [
		100 => 'Portal',
		101 => 'Pembicaraan_Portal'
	],
	'inhwiki' => [
		100 => 'Моартал',
		101 => 'Моарталах_къамаьл'
	], // T326089
	'iswiki' => [
		100 => 'Gátt',
		101 => 'Gáttaspjall'
	],
	'itwiki' => [
		100 => 'Portale',
		101 => 'Discussioni_portale',
		102 => 'Progetto',
		103 => 'Discussioni_progetto',
		118 => 'Bozza', // T280289
		119 => 'Discussioni_bozza' // T280289
	],
	'jawiki' => [
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
		829 => 'モジュール‐ノート', // T49933
	],
	'jamwiki' => [
		100 => 'Puotal', // T135479
		101 => 'Puotal_diskoshan',
	],
	'jvwiki' => [
		100 => 'Gapura', // T252343 - Portal (Talk)
		101 => 'Parembugan_Gapura',
	],
	'kawiki' => [
		100 => 'პორტალი',
		101 => 'პორტალი_განხილვა',
	],
	'kabwiki' => [
		100 => 'Awwur', // T77017 - Portal
		101 => 'Amyannan_uwwur',
		102 => 'Asenfaṛ', // T77017 - Project
		103 => 'Amyannan_usenfaṛ',
	],
	'kkwiki' => [
		100 => 'Портал', // Portal
		101 => 'Портал_талқылауы',
		102 => 'Жоба', // T42794 - WikiProject
		103 => 'Жоба_талқылауы',
	],
	'knwiki' => [
		100 => 'ಹೆಬ್ಬಾಗಿಲು', // Portal - T355662
		101 => 'ಹೆಬ್ಬಾಗಿಲು_ಚರ್ಚೆಪುಟ', // Portal_talk
		118 => 'ಕರಡು', // T129052 - Draft
		119 => 'ಕರಡು_ಚರ್ಚೆಪುಟ', // T129052, T255337
		829 => 'ಮಾಡ್ಯೂಲ್_ಚರ್ಚೆಪುಟ', // T346583
	],
	'kowiki' => [
		100 => '포털', // T87528
		101 => '포털토론', // T87528
		102 => '위키프로젝트', // T29651
		103 => '위키프로젝트토론', // T29651
		118 => '초안', // T92798
		119 => '초안토론', // T92798
	],
	'kuwiki' => [
		100 => 'Portal',
		101 => 'Gotûbêja_portalê', // T39521
		NS_PROJECT_TALK => 'Gotûbêja_Wîkîpediyayê', // T39521
	],
	'kwwiki' => [
		100 => 'Porth',
		101 => 'Keskows_Porth',
	],
	'ladwiki' => [
		100 => 'Portal', // T161843
		101 => 'Diskusyón_de_Portal', // T161843
	],
	'lawiki' => [
		100 => 'Porta',
		101 => 'Disputatio_Portae',
	],
	'liwiki' => [
		100 => 'Portaol',
		101 => 'Euverlèk_portaol',
	],
	'lmowiki' => [
		100 => 'Portal', // T8920
		101 => 'Ciciarada_Portal', // T8920, T338621
	],
	'lrcwiki' => [
		100 => 'پورتال', // T102026
		101 => 'چأک_چئنە_پورتال', // T102026
	],
	'ltwiki' => [
		100 => 'Vikisritis',
		101 => 'Vikisrities_aptarimas',
		102 => 'Vikiprojektas',
		103 => 'Vikiprojekto_aptarimas',
		104 => 'Sąrašas',
		105 => 'Sąrašo_aptarimas',
	],
	'lvwiki' => [
		100 => 'Portāls',
		101 => 'Portāla_diskusija',
		102 => 'Vikiprojekts',
		103 => 'Vikiprojekta_diskusija',
	],
	'mkwiki' => [
		100 => 'Портал',
		101 => 'Разговор_за_Портал',
	],
	'mlwiki' => [
		100 => 'കവാടം',
		101 => 'കവാടത്തിന്റെ_സംവാദം',
		118 => 'കരട്', // Draft - T362653
		119 => 'കരട്_സംവാദം', // Draft - T362653
	],
	'minwiki' => [
		100 => 'Portal', // Portal
		101 => 'Diskusi_Portal', // Portal talk
	],
	'mnwwiki' => [
		100 => 'ပါင်မုက်', // Portal
		101 => 'ပါင်မုက်_ဓရီုကျာ', // Portal talk
	],
	'mrwiki' => [
		100 => 'दालन',
		101 => 'दालन_चर्चा',
	],
	'mtwiki' => [
		100 => 'Portal',
		101 => 'Diskussjoni_portal',
	],
	'mswiki' => [
		100 => 'Portal',
		101 => 'Perbincangan_portal',
	],
	'mwlwiki' => [
		100 => 'Portal', // T180052
		101 => 'Cumbersa_portal', // T180052
	],
	'mywiki' => [ // T352424
		100 => 'မုခ်ဝ', // Portal
		101 => 'မုခ်ဝ_ဆွေးနွေးချက်', // Portal_talk
		118 => 'စာမူကြမ်း', // Draft
		119 => 'စာမူကြမ်း_ဆွေးနွေးချက်', // Draft_talk
	],
	'mznwiki' => [
		100 => 'پورتال', # Portal
		101 => 'پورتال_گپ', # Portal talk
		102 => 'پروژه', // T350397
		103 => 'پروژه_گپ',
	],
	'ndswiki' => [
		100 => 'Portal',
		101 => 'Portal_Diskuschoon'
	],
	'newiki' => [
		100 => 'पोर्टल', // T130108
		101 => 'पोर्टल_वार्ता',
		118 => 'मस्यौदा', // T184157
		119 => 'मस्यौदा_वार्ता',
	],
	'newwiki' => [
		100 => 'दबू',
		101 => 'दबू_खँलाबँला',
	],
	'niawiki' => [ // T277671
		100 => 'Portal',
		101 => 'Huhuo_portal',
	],
	'nlwiki' => [
		100 => 'Portaal',
		101 => 'Overleg_portaal'
	],
	'nnwiki' => [
		100 => 'Tema',
		101 => 'Temadiskusjon',
	],
	'nowiki' => [
		100 => 'Portal',
		101 => 'Portaldiskusjon',
	],
	'ocwiki' => [
		100 => 'Portal',
		101 => 'Discussion_Portal',
		102 => 'Projècte',
		103 => 'Discussion_Projècte',
	],
	'orwiki' => [ // T60453
		100 => 'ପୋର୍ଟାଲ',
		101 => 'ପୋର୍ଟାଲ_ଆଲୋଚନା',
	],
	'pawiki' => [
		100 => 'ਫਾਟਕ', // T120936
		101 => 'ਫਾਟਕ_ਗੱਲ-ਬਾਤ',
	],
	'pflwiki' => [
		100 => 'Portal', // T44990
		101 => 'Portal_Diskussion',
		102 => 'Buch', // 102-111: T54671
		103 => 'Buch_Dischbediere',
		104 => 'Wort',
		105 => 'Wort_Dischbediere',
		106 => 'Text',
		107 => 'Text_Dischbediere',
		108 => 'Spruch',
		109 => 'Spruch_Dischbediere',
		110 => 'Nochricht',
		111 => 'Nochricht_Dischbediere',
	],
	'plwiki' => [
		NS_PROJECT_TALK => 'Dyskusja_Wikipedii',
		NS_USER => 'Wikipedysta',
		# Lower case w in wikipedysty as per T12064
		NS_USER_TALK => 'Dyskusja_wikipedysty',
		100 => 'Portal',
		101 => 'Dyskusja_portalu',
		102 => 'Wikiprojekt',
		103 => 'Dyskusja_wikiprojektu',
	],
	'pswiki' => [
		100 => 'تانبه',
		101 => 'د_تانبې_خبرې_اترې', // T119510
	],
	'ptwiki' => [
		NS_USER => 'Usuário(a)', // T29495
		NS_USER_TALK => 'Usuário(a)_Discussão',
		100 => 'Portal',
		101 => 'Portal_Discussão',
		104 => 'Livro',
		105 => 'Livro_Discussão',
	],
	'roa_tarawiki' => [
		100 => 'Portale',
		101 => "'Ngazzaminde_d'u_Portale",
	],
	'rowiki' => [
		100 => 'Portal',
		101 => 'Discuție_Portal', // T350739
		102 => 'Proiect', // T127607
		103 => 'Discuție_Proiect', // T127607, T350739
		108 => 'Cod', // T44690 - Code, T127607
		109 => 'Discuție_Cod', // T127607
		110 => 'Carte', // T68530, T127607
		111 => 'Discuție_Carte', // T127607, T350739
	],
	'ruwiki' => [
		100 => 'Портал',
		101 => 'Обсуждение_портала',
		102 => 'Инкубатор', // Incubator
		103 => 'Обсуждение_Инкубатора', // Incubator talk
		104 => 'Проект', // T36124 - Project
		105 => 'Обсуждение_проекта', // T36124 - Project talk
		106 => 'Арбитраж', // T36527 - Arbcom
		107 => 'Обсуждение_арбитража',
	],
	'sawiki' => [
		100 => 'प्रवेशद्वारम्', // T78394 - Portal
		101 => 'प्रवेशद्वारसम्भाषणम्', // T78394, T101634
	],
	'scnwiki' => [
		100 => 'Purtali',
		101 => 'Discussioni_purtali',
		102 => 'Pruggettu',
		103 => 'Discussioni_pruggettu',
	],
	'scowiki' => [
		100 => 'Portal', // T68107
		101 => 'Portal_talk',
		102 => 'Define', // T247172
		103 => 'Define_talk',
	],
	'sdwiki' => [
		100 => 'باب', // T186943 - Portal
		101 => 'باب_بحث', // T186943 - Portal talk
	],
	'sewiki' => [
		100 => 'Portála', // T41206
		101 => 'Portálaságastallan', // T41206
	],
	'shwiki' => [
		100 => 'Portal', // T32928
		101 => 'Razgovor_o_portalu', // T32928
		118 => 'Nacrt', // T327864
		119 => 'Razgovor_o_nacrtu', // T327864
	],
	'shiwiki' => [
		100 => 'Aggur', // T288909 - Portal
		101 => 'Amsawal_n_waggur', // T288909 - Portal_talk
	],
	'shnwiki' => [ 100 => 'ၵိူၼ်ႇတူ', 101 => 'တွၼ်ႈဢုပ်ႇ_ၵိူၼ်ႇတူ' ], // T212992
	'siwiki' => [ 100 => 'ද්වාරය', 101 => 'ද්වාරය_සාකච්ඡාව' ], // T8435, T26936
	'skwiki' => [
		NS_USER => 'Redaktor', // T143472
		NS_USER_TALK => 'Diskusia_s_redaktorom',
		100 => 'Portál',
		101 => 'Diskusia_k_portálu',
	],
	'slwiki' => [
		100 => 'Portal',
		101 => 'Pogovor_o_portalu',
		118 => 'Osnutek', // Draft, T332351
		119 => 'Pogovor_o_osnutku', // Draft_talk, T332351
	],
	'sowiki' => [ 100 => 'Portal', 101 => 'Portal_talk' ], // T51600
	'sqwiki' => [ 100 => 'Portal', 101 => 'Portal_diskutim' ],
	'srwiki' => [
		100 => 'Портал',
		101 => 'Разговор_о_порталу',
		118 => 'Нацрт', // T214428
		119 => 'Разговор_о_нацрту', // T214428
	],
	'suwiki' => [ 100 => 'Portal', 101 => 'Obrolan_portal' ], // T10156
	'svwiki' => [ 100 => 'Portal', 101 => 'Portaldiskussion' ],
	'swwiki' => [
		100 => 'Lango',
		101 => 'Majadiliano_ya_lango',
		102 => 'Wikichanzo', // T158041
		103 => 'Majadiliano_ya_Wikichanzo',
	],
	'tawiki' => [
		100 => 'வலைவாசல்',
		101 => 'வலைவாசல்_பேச்சு',
		118 => 'வரைவு', // T329248
		119 => 'வரைவு_பேச்சு', // T329248
	],
	'tewiki' => [ 100 => 'వేదిక', 101 => 'వేదిక_చర్చ', ],
	'tgwiki' => [
		100 => 'Портал',
		101 => 'Баҳси_портал',
		102 => 'Лоиҳа', // T137200 - Project
		103 => 'Баҳси_Лоиҳа',
	],
	'thwiki' => [
		100 => 'สถานีย่อย',
		101 => 'คุยเรื่องสถานีย่อย',
		108 => 'หนังสือ', // T216322, Book
		109 => 'คุยเรื่องหนังสือ', // Book talk
		118 => 'ฉบับร่าง', // T216322, Draft
		119 => 'คุยเรื่องฉบับร่าง', // Draft talk
	],
	'tiwiki' => [
		100 => 'ማዕጾ', // T259295 Portal
		101 => 'ምይይጥ_ማዕጾ', // T259295, T263840 Portal talk
	],
	'tlwiki' => [
		100 => 'Portada', // Portal
		101 => 'Usapang_Portada', // Portal talk
	],
	'trwiki' => [
		100 => 'Portal',
		101 => 'Portal_tartışma',
		102 => 'Vikiproje', // Wikiproject, T166102
		103 => 'Vikiproje_tartışma', // Wikiproject talk
	],
	'ttwiki' => [
		100 => 'Портал',
		101 => 'Портал_бәхәсе',
	],
	'udmwiki' => [
		NS_PROJECT_TALK => 'Википедия_сярысь_вераськон', // T49820
	],
	'ukwiki' => [ 100 => 'Портал', 101 => 'Обговорення_порталу' ],
	'urwiki' => [
		100 => 'باب', // Portal - T21569
		101 => 'تبادلۂ_خیال_باب', // Portal talk - T21569
		102 => 'کتاب', // Book - T109505
		103 => 'تبادلۂ_خیال_کتاب', // Book talk - T109505
		118 => 'مسودہ', // Draft - T119308
		119 => 'تبادلۂ_خیال_مسودہ',
	],
	'uzwiki' => [
		100 => 'Portal', // T40840 (Portal)
		101 => 'Portal_munozarasi', // T40840
		102 => 'Loyiha', // T40840 (Project)
		103 => 'Loyiha_munozarasi', // T40840
	],
	'vecwiki' => [
		100 => 'Portałe',
		101 => 'Discussion_portałe',
		102 => 'Projeto',
		103 => 'Discussion_projeto',
	],
	'viwiki' => [
		100 => 'Cổng_thông_tin',
		101 => 'Thảo_luận_Cổng_thông_tin'
	],
	'wuuwiki' => [
		// Override MediaWiki default namespace names for "wuu",
		// same than for "yue", see T124389.
		NS_MEDIA => 'Media',
		NS_SPECIAL => 'Special',
		NS_TALK => 'Talk',
		NS_USER => 'User',
		NS_USER_TALK => 'User_talk',
		NS_PROJECT_TALK => '$1_talk',
		NS_FILE => 'File',
		NS_FILE_TALK => 'File_talk',
		NS_MEDIAWIKI_TALK => 'MediaWiki_talk',
		NS_TEMPLATE => 'Template',
		NS_TEMPLATE_TALK => 'Template_talk',
		NS_HELP => 'Help',
		NS_HELP_TALK => 'Help_talk',
		NS_CATEGORY => 'Category',
		NS_CATEGORY_TALK => 'Category_talk',

		// Extra namespaces
		100 => 'Portal', // T124389
		101 => 'Portal_talk',
	],
	'yiwiki' => [
		100 => 'פארטאל',
		101 => 'פארטאל_רעדן',
	],
	'yowiki' => [
		100 => 'Èbúté',
		101 => 'Ọ̀rọ̀_èbúté',
		108 => 'Ìwé',
		109 => 'Ọ̀rọ̀_ìwé',
	],
	'zhwiki' => [
		100 => 'Portal',
		101 => 'Portal_talk',
		102 => 'WikiProject', // T271612
		103 => 'WikiProject_talk', // T271612
		118 => 'Draft', // T91223
		119 => 'Draft_talk', // T91223
	],
	'zh_classicalwiki' => [
		100 => '門',
		101 => '議',
		106 => '稿', // Draft, T163655
		107 => '酌稿', // Draft talk, T163655
	],
	'zh_min_nanwiki' => [ 100 => 'Portal', 101 => 'Portal_talk' ],
	'zh_yuewiki' => [
		# Override MediaWiki default namespace names for "yue".
		NS_MEDIA => 'Media',
		NS_SPECIAL => 'Special',
		NS_TALK => 'Talk',
		NS_USER => 'User',
		NS_USER_TALK => 'User_talk',
		NS_PROJECT_TALK => '$1_talk',
		NS_FILE => 'File',
		NS_FILE_TALK => 'File_talk',
		NS_MEDIAWIKI_TALK => 'MediaWiki_talk',
		NS_TEMPLATE => 'Template',
		NS_TEMPLATE_TALK => 'Template_talk',
		NS_HELP => 'Help',
		NS_HELP_TALK => 'Help_talk',
		NS_CATEGORY => 'Category',
		NS_CATEGORY_TALK => 'Category_talk',
		# Additional namespaces.
		100 => 'Portal',
		101 => 'Portal_talk',
	],
	// @} end of Wikipedia

	// Wikisource wikis @{
	// Please reserve NS 114 for 'Translation', thanks!

	'sourceswiki' => [
		108 => 'Author',
		109 => 'Author_talk',
	],
	'azwikisource' => [
		100 => 'Portal',
		101 => 'Portal_müzakirəsi',
		102 => 'Müəllif', // Author
		103 => 'Müəllif_müzakirəsi', // Author talk
	],
	'arwikisource' => [
		100 => 'بوابة',
		101 => 'نقاش_البوابة',
		102 => 'مؤلف',
		103 => 'نقاش_المؤلف',
	],
	'aswikisource' => [
		102 => 'লেখক', // T72464 - Author
		103 => 'লেখক_আলোচনা',
	],
	'banwikisource' => [
		102 => 'Pangawi', // T284389 - Author
		103 => 'Pabligbagan_Pangawi', // T284389 - Author talk
	],
	'bewikisource' => [
		102 => 'Аўтар', // Author
		103 => 'Размовы_пра_аўтара',
	],
	'bgwikisource' => [
		100 => 'Автор',
		101 => 'Автор_беседа',
	],
	'bnwikisource' => [
		100 => 'লেখক', // Author
		101 => 'লেখক_আলাপ', // Author talk
		106 => 'প্রবেশদ্বার', // Portal
		107 => 'প্রবেশদ্বার_আলাপ',
		108 => 'প্রকাশক', // T199028 - Publisher
		109 => 'প্রকাশক_আলোচনা', // T199028 - Publisher talk
		110 => 'রচনা', // T210472 - Work
		111 => 'রচনা_আলাপ', // T210472 - Work talk
		114 => 'অনুবাদ', // T114623 - Translation
		115 => 'অনুবাদ_আলোচনা',
	],
	'brwikisource' => [
		104 => 'Oberour',
		105 => 'Kaozeadenn_oberour',
	],
	'cawikisource' => [
		# 100, 101 reserved for Portal
		106 => 'Autor', // T29898
		107 => 'Autor_Discussió', // T29898
	],
	'cswikisource' => [
		100 => 'Autor',
		101 => 'Diskuse_k_autorovi',
		102 => 'Edice', // T221697
		103 => 'Diskuse_k_edici', // T221697
	],
	'dawikisource' => [
		102 => 'Forfatter', // T9796
		103 => 'Forfatterdiskussion',
	],
	'elwikisource' => [
		108 => 'Συγγραφέας', // T78793 (Author)
		109 => 'Συζήτηση_συγγραφέα',
		110 => 'Πύλη', // T157187 (Portal)
		111 => 'Συζήτηση_πύλης', // T157187 (Portal talk)
	],
	'enwikisource' => [
		100 => 'Portal',
		101 => 'Portal_talk',
		102 => 'Author',
		103 => 'Author_talk',
		114 => 'Translation', // T52007
		115 => 'Translation_talk', // T52007
	],
	'eowikisource' => [
		102 => 'Aŭtoro', // Author
		103 => 'Aŭtoro-Diskuto', // Author talk
	],
	'eswikisource' => [
		100 => 'Portal', // Portal, T164195
		101 => 'Portal_discusión', // Portal talk, T164195
		106 => 'Autor', // Author, T164195
		107 => 'Autor_discusión', // Author talk, T164195
	],
	'etwikisource' => [
		106 => 'Autor',
		107 => 'Autori_arutelu',
	],
	'euwikisource' => [ // T193225 (Author namespaces)
		106 => 'Egilea',
		107 => 'Egilea_eztabaida',
	],
	'fawikisource' => [
		100 => 'درگاه', // Portal
		101 => 'بحث_درگاه', // Portal talk
		102 => 'پدیدآورنده', // Author
		103 => 'بحث_پدیدآورنده', // Author talk
	],
	'frwikisource' => [
		100 => 'Transwiki',
		101 => 'Discussion_Transwiki',
		102 => 'Auteur',
		103 => 'Discussion_Auteur',
		106 => 'Portail',
		107 => 'Discussion_Portail',
	],
	'fywiki' => [
		100 => 'Tema', // T334807
		101 => 'Tema_oerlis', // T334807
	],
	'glwikisource' => [
		102 => 'Autor', // T134041
		103 => 'Conversa_autor',
	],
	'guwikisource' => [
		108 => 'સર્જક', // Author
		109 => 'સર્જક_ચર્ચા',
		110 => 'શ્રાવ્યપુસ્તક', // T347189 - Audiobook
		111 => 'શ્રાવ્યપુસ્તક_ચર્ચા', // T347189 - Audiobook_talk
	],
	'hiwikisource' => [
		100 => 'लेखक', // T218155 - Author
		101 => 'लेखक_वार्ता', // T218155 - Author talk
		114 => 'अनुवाद', // T218155 - Translation
		115 => 'अनुवाद_वार्ता', // T218155 - Translation talk
	],
	'hewikisource' => [
		# 100 => 'קטע', // Removed per T298430
		# 101 => 'שיחת_קטע',
		106 => 'ביאור',
		107 => 'שיחת_ביאור',
		108 => 'מחבר',
		109 => 'שיחת_מחבר',
		110 => 'תרגום',
		111 => 'שיחת_תרגום',
		116 => 'מקור', // T66353
		117 => 'שיחת_מקור', // T66353
	],
	'hrwikisource' => [
		100 => 'Autor',
		101 => 'Razgovor_o_autoru',
	],
	'huwikisource' => [
		100 => 'Szerző',
		101 => 'Szerző_vita',
	],
	'hywikisource' => [
		100 => 'Հեղինակ',
		101 => 'Հեղինակի_քննարկում',
		102 => 'Պորտալ',
		103 => 'Պորտալի_քննարկում',
	],
	'idwikisource' => [
		100 => 'Pengarang',
		101 => 'Pembicaraan_Pengarang',
		106 => 'Portal',
		107 => 'Pembicaraan_Portal',
	],
	'iswikisource' => [
		100 => 'Gátt', // T46164 - Portal
		101 => 'Gáttarspjall', // T46164
		102 => 'Höfundur', // T46164 - Author
		103 => 'Höfundarspjall', // T46164
	],
	'itwikisource' => [
		102 => 'Autore',
		103 => 'Discussioni_autore',
		104 => 'Progetto',
		105 => 'Discussioni_progetto',
		106 => 'Portale',
		107 => 'Discussioni_portale',
		112 => 'Opera', // T93870
		113 => 'Discussioni_opera',
	],
	'jawikisource' => [
		102 => '作者', // T126914 - Author
		103 => '作者・トーク',
	],
	'jvwikisource' => [
		102 => 'Panganggit', // T286241 - Author
		103 => 'Parembugan_Panganggit', // T286241 - Author talk
	],
	'kawikisource' => [ // T363243
		102 => 'ავტორი',
		103 => 'ავტორის_განხილვა',
	],
	'knwikisource' => [ // T39676
		100 => 'ಸಂಪುಟ', // Portal
		101 => 'ಸಂಪುಟ_ಚರ್ಚೆ',
		102 => 'ಕರ್ತೃ', // Author
		103 => 'ಕರ್ತೃ_ಚರ್ಚೆ',
	],
	'kowikisource' => [
		100 => '저자',
		101 => '저자토론',
		102 => '포털', // T71522
		103 => '포털토론',
		114 => '번역', // T183836, Translation
		115 => '번역토론', // T183836, Traslation talk
	],
	'lawikisource' => [
		102 => 'Scriptor',
		103 => 'Disputatio_Scriptoris',
	],
	'lijwikisource' => [
		102 => 'Aotô', // T257672
		103 => 'Discuscion_aotô', // T257672
		104 => 'Œuvia', // T257672
		105 => 'Discuscion_œuvia', // T257672
	],
	'mkwikisource' => [
		102 => 'Автор',
		103 => 'Разговор_за_автор',
	],
	'mlwikisource' => [
		100 => 'രചയിതാവ്',
		101 => 'രചയിതാവിന്റെ_സംവാദം',
		102 => 'കവാടം',
		103 => 'കവാടത്തിന്റെ_സംവാദം',
		114 => 'പരിഭാഷ', // T154087 - Translate
		115 => 'പരിഭാഷയുടെ_സംവാദം',
	],
	'mrwikisource' => [
		100 => 'दालन', // Portal
		101 => 'दालन_चर्चा', // Portal talk
		102 => 'साहित्यिक', // Author
		103 => 'साहित्यिक_चर्चा', // Author talk
	],
	'mswikisource' => [ // T369047
		102 => 'Pengarang', // Author
		103 => 'Perbincangan_pengarang', // Author talk
		114 => 'Terjemahan', // Translation
		115 => 'Perbincangan_terjemahan', // Translation talk
	],
	'mswikisource' => [ // T371060
		100 => 'မုခ်ဝ', // Portal
		101 => 'မုခ်ဝ_ဆွေးနွေးချက်', // Portal talk
		102 => 'စာရေးသူ', // Author
		103 => 'စာရေးသူ_ဆွေးနွေးချက်', // Author talk
		114 => 'ဘာသာပြန်', // Translation
		115 => 'ဘာသာပြန်_ဆွေးနွေးချက်', // Translation talk
	],
	'napwikisource' => [
		102 => 'Autore', // Author
		103 => 'Autore_chiàcchiera', // Author talk
		108 => 'Opera', // Work
		109 => 'Opera_chiàcchiera', // Work talk
		114 => 'Traduzzione', // T239547, Translation
		115 => 'Traduzzione_chiàcchiera', // Translation talk
	],
	'nlwikisource' => [
		100 => 'Hoofdportaal',
		101 => 'Overleg_hoofdportaal',
		102 => 'Auteur',
		103 => 'Overleg_auteur',
	],
	'nowikisource' => [
		102 => 'Forfatter',
		103 => 'Forfatterdiskusjon',
	],
	'pawikisource' => [
		114 => 'ਅਨੁਵਾਦ', // T179807 - Translation
		115 => 'ਅਨੁਵਾਦ_ਗੱਲ-ਬਾਤ', // T179807
		100 => 'ਲੇਖਕ', // T226959 - Author
		101 => 'ਲੇਖਕ_ਗੱਲ-ਬਾਤ', // T226959 - Author talk
		106 => 'ਪੋਰਟਲ', // T226959 - Portal
		107 => 'ਪੋਰਟਲ_ਗੱਲ-ਬਾਤ', // T226959 - Portal talk
		108 => 'ਪ੍ਰਕਾਸ਼ਕ', // T226959 - Publisher
		109 => 'ਪ੍ਰਕਾਸ਼ਕ_ਗੱਲ-ਬਾਤ', // T226959 - Publisher talk
		110 => 'ਲਿਖਤ', // T226959 - Work
		111 => 'ਲਿਖਤ_ਗੱਲ-ਬਾਤ', // T226959 - Work talk
		112 => 'ਆਡੀਓਬੁਕ', // T343410 - Audiobook
		113 => 'ਆਡੀਓਬੁਕ_ਗੱਲ-ਬਾਤ', // T343410 - Audiobook_talk
	],
	'plwikisource' => [
		NS_USER => 'Wikiskryba',
		NS_USER_TALK => 'Dyskusja_wikiskryby',
		NS_PROJECT_TALK => 'Dyskusja_Wikiźródeł',
		104 => 'Autor',
		105 => 'Dyskusja_autora',
		124 => 'Kolekcja', // T154711
		125 => 'Dyskusja_kolekcji', // T154711
	],
	'pmswikisource' => [
		102 => 'Pàgina', // Page
		103 => 'Discussion_ëd_la_pàgina', // Page talk
		104 => 'Tàula', // Index
		105 => 'Discussion_ëd_la_tàula', // Index talk
	],
	'ptwikisource' => [
		100 => 'Portal',
		101 => 'Portal_Discussão',
		102 => 'Autor',
		103 => 'Autor_Discussão',
		108 => 'Em_Tradução',
		109 => 'Discussão_Em_Tradução',
		110 => 'Anexo',
		111 => 'Anexo_Discussão',
	],
	'rowikisource' => [
		102 => 'Autor',
		103 => 'Discuție_Autor',
	],
	'sawikisource' => [
		100 => 'प्रवेशद्वारम्', // T235343 - Portal
		101 => 'प्रवेशद्वारसम्भाषणम्', // T235343 - Portal talk
		102 => 'लेखकः', // T214553 - Author
		103 => 'लेखकसम्भाषणम्', // T214553 - Author talk
		108 => 'श्रव्यम्', // T282970 - Audio
		109 => 'श्रव्यसम्भाषणम्', // T282970 - Audio talk
	],
	'skwikisource' => [
		102 => 'Autor', // T122175
		103 => 'Diskusia_k_autorovi',
	],
	'srwikisource' => [
		100 => 'Аутор',
		101 => 'Разговор_о_аутору',
		102 => 'Додатак', // T39742
		103 => 'Разговор_о_додатку', // T39742
	],
	'suwikisource' => [ // T344314
		102 => 'Pangarang',
		103 => 'Obrolan_Pangarang',
	],
	'svwikisource' => [
		106 => 'Författare',
		107 => 'Författardiskussion',
	],
	'tawikisource' => [
		102 => 'ஆசிரியர்', // T165813 - Author
		103 => 'ஆசிரியர்_பேச்சு', // T165813 - Author talk
	],
	'tewikisource' => [
		100 => 'ద్వారము', // Portal
		101 => 'ద్వారము_చర్చ',
		102 => 'రచయిత', // Author
		103 => 'రచయిత_చర్చ',
	],
	'thwikisource' => [
		100 => 'สถานีย่อย', // Portal, T216322
		101 => 'คุยเรื่องสถานีย่อย', // Portal talk, T216322
		102 => 'ผู้สร้างสรรค์', // Author, T216322 and T236640
		103 => 'คุยเรื่องผู้สร้างสรรค์', // Author talk, T216322 and T236640
		114 => 'งานแปล', // Translation, T216322
		115 => 'คุยเรื่องงานแปล', // Translation talk, T216322
	],
	'trwikisource' => [
		100 => 'Kişi',
		101 => 'Kişi_tartışma',
		200 => 'Portal',
		201 => 'Portal_tartışma',
	],
	'ukwikisource' => [ // T50308
		NS_PROJECT_TALK => 'Обговорення_Вікіджерел',
		102 => 'Автор', // Author
		103 => 'Обговорення_автора',
		116 => 'Архів', // T270627 - Archive
		117 => 'Обговорення_архіву', // T270627 - Archive talk
	],
	'vecwikisource' => [
		100 => 'Autor',
		101 => 'Discussion_autor',
	],
	'viwikisource' => [
		100 => 'Chủ_đề',
		101 => 'Thảo_luận_Chủ_đề',
		102 => 'Tác_gia',
		103 => 'Thảo_luận_Tác_gia',
		114 => 'Biên_dịch', // T290691 - Translate
		115 => 'Thảo_luận_Biên_dịch',
	],
	'wawikisource' => [
		100 => 'Oteur', // T269431 - Author
		101 => 'Oteur_copene', // T269431 - Author talk
	],
	'zhwikisource' => [ // Added on 2009-03-19 per T17722
		100 => 'Portal', // T230294
		101 => 'Portal_talk', // T230294
		102 => 'Author',
		103 => 'Author_talk',
		108 => 'Transwiki', // T42474
		109 => 'Transwiki_talk',
		114 => 'Translation', // T66127
		115 => 'Translation_talk', // T66127
	],
	# @} end of wikisource wikis

	// Wiktionary @{
	'angwiktionary' => [ // T58634
		100 => 'Ætēaca',
		101 => 'Ætēacmōtung',
	],
	'azwiktionary' => [
		100 => 'Əlavə', // Appendix - T143851
		101 => 'Əlavə_müzakirəsi',
	],
	'bgwiktionary' => [
		100 => 'Словоформи',
		101 => 'Словоформи_беседа'
	],
	'blkwiktionary' => [
		100 => 'သွုပ်ဆုဲင်ꩻ',
		101 => 'သွုပ်ဆုဲင်ꩻ_အိုင်ကိုမ်ဒေါ့ꩻရီ',
	],
	'brwiktionary' => [
		100 => 'Stagadenn', // Appendix
		101 => 'Kaozeadenn_Stagadenn', // Appendix talk
	],
	'bnwiktionary' => [
		100 => 'পরিশিষ্ট', // Appendix - T317745
		101 => 'পরিশিষ্ট_আলোচনা', // Appendix_talk - T317745
		106 => 'ছন্দ', // Rhymes - T317424
		107 => 'ছন্দ_আলোচনা', // Rhymes talk - T317424
		110 => 'থিসরাস', // Thesaurus - T317424
		111 => 'থিসরাস_আলোচনা', // Thesaurus talk - T317424
		114 => 'উদ্ধৃতি', // Citations - T317424
		115 => 'উদ্ধৃতি_আলোচনা', // Citations talk - T317424
	],
	'bswiktionary' => [
		100 => 'Portal',
		101 => 'Razgovor_o_Portalu',
		102 => 'Indeks',
		103 => 'Razgovor_o_Indeksu',
		104 => 'Dodatak',
		105 => 'Razgovor_o_Dodatku',
	],
	'cswiktionary' => [
		100 => 'Příloha',
		101 => 'Diskuse_k_příloze',
	],
	'cywiktionary' => [
		100 => 'Atodiad',
		101 => 'Sgwrs_Atodiad',
		102 => 'Odliadur',
		103 => 'Sgwrs_Odliadur',
		104 => 'WiciSawrws',
		105 => 'Sgwrs_WiciSawrws',
	],
	'dewiktionary' => [
		102 => 'Verzeichnis',
		103 => 'Verzeichnis_Diskussion',
		104 => 'Thesaurus',
		105 => 'Thesaurus_Diskussion',
		106 => 'Reim', // T45830
		107 => 'Reim_Diskussion',
		108 => 'Flexion', // T76357
		109 => 'Flexion_Diskussion',
		110 => 'Rekonstruktion', // T256242
		111 => 'Rekonstruktion_Diskussion', // T256242
	],
	'dvwiktionary' => [
		NS_PROJECT_TALK => 'ވިކިރަދީފު_ޚިޔާލު', // T48846
	],
	'elwiktionary' => [
		100 => 'Παράρτημα',
		101 => 'Συζήτηση_παραρτήματος',
	],
	'enwiktionary' => [
		100 => 'Appendix', // T8476
		101 => 'Appendix_talk',
		# 102 => 'Concordance', // Removed per T354813
		# 103 => 'Concordance_talk', // Removed per T354813
		# 104 => 'Index', // Removed per T344816
		# 105 => 'Index_talk',
		106 => 'Rhymes',
		107 => 'Rhymes_talk',
		108 => 'Transwiki',
		109 => 'Transwiki_talk',
		110 => 'Thesaurus', // T174264
		111 => 'Thesaurus_talk',
		# 112 => 'WT', // Removed per T24104
		# 113 => 'WT_talk',
		114 => 'Citations',
		115 => 'Citations_talk',
		116 => 'Sign_gloss', // T26570
		117 => 'Sign_gloss_talk',
		118 => 'Reconstruction', // T119207
		119 => 'Reconstruction_talk',
	],
	'eowiktionary' => [
		NS_USER => 'Uzanto', // T24426
		NS_USER_TALK => 'Uzanta_diskuto',
		102 => 'Aldono', // T221525
		103 => 'Aldono-Diskuto',
	],
	'eswiktionary' => [
		100 => 'Apéndice',
		101 => 'Apéndice_Discusión',
	],
	'fawiktionary' => [
		100 => "پیوست",
		101 => "بحث_پیوست",
	],
	'fiwiktionary' => [
		100 => 'Liite', // T13672
		101 => 'Keskustelu_liitteestä',
	],
	'frwiktionary' => [
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
		110 => 'Reconstruction', // T199631
		111 => 'Discussion_Reconstruction',
		112 => 'Tutoriel', // T242102
		113 => 'Discussion_Tutoriel',
		114 => 'Rime', // T262398
		115 => 'Discussion_Rime', // T262398
		116 => 'Conjugaison', // T262298
		117 => 'Discussion_Conjugaison', // T262298
		118 => 'Racine', // T263525
		119 => 'Discussion_Racine', // T263525
		120 => 'Convention', // T360989
		121 => 'Discussion_Convention', // T360989
	],
	'gawiktionary' => [
		100 => 'Aguisín',
		101 => 'Plé_aguisín',
	],
	'glwiktionary' => [
		100 => 'Apéndice',
		101 => 'Conversa_apéndice',
	],
	'gorwiktionary' => [
		100 => 'Indeks', // T326253
		101 => 'Lo\'iya_indeks', // T326253
	],
	'hewiktionary' => [
		100 => 'נספח',
		101 => 'שיחת_נספח'
	],
	'huwiktionary' => [
		100 => 'Függelék', // T44505 - Appendix
		101 => 'Függelékvita',
		102 => 'Index', // T44505
		103 => 'Indexvita',
	],
	'iawiktionary' => [
		102 => 'Appendice',
		103 => 'Discussion_Appendice',
	],
	'idwiktionary' => [
		100 => 'Indeks',
		101 => 'Pembicaraan_Indeks',
		102 => 'Lampiran',
		103 => 'Pembicaraan_Lampiran',
	],
	'iswiktionary' => [
		106 => 'Viðauki',
		107 => 'Viðaukaspjall',
		110 => 'Samheitasafn',
		111 => 'Samheitasafnsspjall',
	],
	'itwiktionary' => [
		100 => 'Appendice',
		101 => 'Discussioni_appendice',
	],
	'jawiktionary' => [
		100 => '付録', // Appendix
		101 => '付録・トーク', // Appendix talk
	],
	'kawiktionary' => [
		100 => 'ფორმაწარმოება', // T212956
		101 => 'ფორმაწარმოების_განხილვა', // T212956
	],
	'kbdwiktionary' => [
		100 => 'Гуэлъхьэ', // Appendix
		101 => 'Гуэлъхьэм_тепсэлъыхьын', // Appendix talk
		102 => 'Индекс', // Index
		103 => 'Индексым_тепсэлъыхьын', // Index talk
	],
	'kowiktionary' => [
		100 => '부록',
		101 => '부록_토론',
	],
	'kuwiktionary' => [
		NS_PROJECT_TALK => 'Gotûbêja_Wîkîferhengê', // T39524
		100 => 'Pêvek', // T30398
		101 => 'Gotûbêja_pêvekê', // T39524
		102 => 'Nimînok', // T30398
		103 => 'Gotûbêja_nimînokê', // T39524
		104 => 'Portal', // T30398
		105 => 'Gotûbêja_portalê', // T39524
		106 => 'Tewandin', // T224327
		107 => 'Gotûbêja_tewandinê',
		108 => 'Jinûvesazî', // T262046 - Reconstruction
		109 => 'Gotûbêja_jinûvesaziyê', // T262046 - Reconstruction talk
	],
	'lbwiktionary' => [
		100 => 'Annexen',
		101 => 'Annexen_Diskussioun',
	],
	'lmowiktionary' => [
		100 => 'Apendice',
		101 => 'Ciciarada_Apendice',
	],
	'ltwiktionary' => [
		100 => 'Sąrašas',
		101 => 'Sąrašo_aptarimas',
		102 => 'Priedas',
		103 => 'Priedo_aptarimas',
	],
	'lvwiktionary' => [
		100 => 'Pielikums',
		101 => 'Pielikuma_diskusija',
	],
	'mgwiktionary' => [
		100 => 'Rakibolana', # Portal
		101 => "Dinika_amin'ny_rakibolana",
	],
	'mnwwiktionary' => [
		100 => 'အဆက်လက္ကရဴ', // T314023 , T341940 - Appendix
		101 => 'အဆက်လက္ကရဴ_ဓရီုကျာ', // T314023 , T341940 - Appendix_talk
		106 => 'ကာရန်', // T330689 , T341940 - Rhymes
		107 => 'ကာရန်_ဓရီုကျာ', // T330689 , T341940 - Rhymes_talk
		118 => 'ဗီုပြင်သိုင်တၟိ', // T330689 , T341940 - Reconstruction
		119 => 'ဗီုပြင်သိုင်တၟိ_ဓရီုကျာ', // T330689 , T341940 - Reconstruction_talk
	],
	'mrwiktionary' => [
		104 => 'सूची',
		105 => 'सूची_चर्चा',
	],
	'mswiktionary' => [
		100 => 'Lampiran', // Appendix - T255391
		101 => 'Perbincangan_lampiran', // Appendix talk - T255391
		102 => 'Rima', // Rhymes - T255391
		103 => 'Perbincangan_rima', // Rhymes talk - T255391
		104 => 'Tesaurus', // Thesaurus - T255391
		105 => 'Perbincangan_tesaurus', // Thesaurus talk - T255391
		106 => 'Indeks', // Index - T255391
		107 => 'Perbincangan_indeks', // Index talk - T255391
		108 => 'Petikan', // Citation - T255391
		109 => 'Perbincangan_petikan', // Citation talk - T255391
		110 => 'Rekonstruksi', // Reconstruction - T255391
		111 => 'Perbincangan_rekonstruksi', // Reconstruction talk - T255391
		112 => 'Padanan_isyarat', // Sign gloss - T255391
		113 => 'Perbincangan_padanan_isyarat', // Sign gloss talk - T255391
		114 => 'Konkordans', // Concordance - T255391
		115 => 'Perbincangan_konkordans', // Concordance talk - T255391
	],
	'mywiktionary' => [
		100 => 'နောက်ဆက်တွဲ', // T178545, Appendix
		101 => 'နောက်ဆက်တွဲ_ဆွေးနွေးချက်',
		106 => 'ကာရန်များ', // T342516 ,Rhymes
		107 => 'ကာရန်များ_ဆွေးနွေးချက်', // T342516 ,Rhymes_talk
		108 => 'ဝေါဟာရပဒေသာ', // T342516 ,Thesaurus
		109 => 'ဝေါဟာရပဒေသာ_ဆွေးနွေးချက်', // T342516 ,Thesaurus_talk
		110 => 'ပြန်လည်တည်ဆောက်ခြင်း', // T342516 ,Reconstruction
		111 => 'ပြန်လည်တည်ဆောက်ခြင်း_ဆွေးနွေးချက်', // T342516 ,Reconstruction_talk
		112 => 'စာကိုးများ', // T342516 ,Citations
		113 => 'စာကိုးများ_ဆွေးနွေးချက်', // T342516 ,Citations_talk
		116 => 'ဝေါဟာရစာရင်း', // T342516 ,Concordance
		117 => 'ဝေါဟာရစာရင်း_ဆွေးနွေးချက်', // T342516 ,Concordance_talk
	],
	'ndswiktionary' => [ // T85122
		100 => 'Anhang',
		101 => 'Anhang_Diskuschoon',
		114 => 'Zitaten',
		115 => 'Zitaten_Diskuschoon',
	],
	'nowiktionary' => [
		100 => 'Tillegg',
		101 => 'Tilleggdiskusjon',
	],
	'ocwiktionary' => [
		100 => 'Annèxa',
		101 => 'Discussion_Annèxa',
	],
	'orwiktionary' => [
		102 => 'ସୂଚୀ', // T98584 - Index
		103 => 'ସୂଚୀ_ଆଲୋଚନା',
		114 => 'ଆଧାର', // T98584 - Citations
		115 => 'ଆଧାର_ଆଲୋଚନା',
	],
	'plwiktionary' => [
		NS_PROJECT_TALK => 'Wikidyskusja',
		NS_USER => 'Wikisłownikarz',
		NS_USER_TALK => 'Dyskusja_wikisłownikarza',
		100 => 'Aneks',
		101 => 'Dyskusja_aneksu',
		102 => 'Indeks',
		103 => 'Dyskusja_indeksu',
		104 => 'Portal',
		105 => 'Dyskusja_portalu',
	],
	'ptwiktionary' => [
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
	],
	'rowiktionary' => [
		100 => 'Portal',
		101 => 'Discuție_Portal',
		102 => 'Apendice',
		103 => 'Discuție_Apendice',
	],
	'ruwiktionary' => [
		100 => 'Приложение',
		101 => 'Обсуждение_приложения',
		102 => 'Конкорданс',
		103 => 'Обсуждение_конкорданса',
		104 => 'Индекс',
		105 => 'Обсуждение_индекса',
		106 => 'Рифмы',
		107 => 'Обсуждение_рифм',
	],
	'shnwiktionary' => [
		100 => 'ၵိူၼ်ႇတူ', // T330376
		101 => 'ဢုပ်ႇၵုမ်_ၵိူၼ်ႇတူ', // T330376
		102 => 'တွၼ်ႈၸပ်းႁၢင်', // T330376
		103 => 'ဢုပ်ႇၵုမ်_တွၼ်ႈၸပ်းႁၢင်', // T330376
		106 => 'တူၼ်းၸၢပ်ႈလႅပ်ႈ', // T330376
		107 => 'ဢုပ်ႇၵုမ်_တူၼ်းၸၢပ်ႈလႅပ်ႈ', // T330376
		110 => 'ၶိုၼ်းၵေႃႇသၢင်ႈ', // T330376
		111 => 'ဢုပ်ႇၵုမ်_ၶိုၼ်းၵေႃႇသၢင်ႈ', // T330376
	],
	'skwiktionary' => [
		100 => 'Príloha', // T148563
		101 => 'Diskusia_k_prílohe', // T148563
	],
	'srwiktionary' => [
		100 => 'Портал',
		101 => 'Разговор_о_порталу',
		102 => 'Додатак', // T216343
		103 => 'Разговор_о_додатку', // T216343
	],
	'svwiktionary' => [ // T9933
		102 => 'Appendix',
		103 => 'Appendixdiskussion',
		104 => 'Rimord',
		105 => 'Rimordsdiskussion',
		106 => 'Transwiki',
		107 => 'Transwikidiskussion',
	],
	'thwiktionary' => [
		100 => 'ภาคผนวก', // Appendix - T114458
		101 => 'คุยเรื่องภาคผนวก', // Appendix talk
		102 => 'ดัชนี', // Index - T114458
		103 => 'คุยเรื่องดัชนี', // Index Talk
		104 => 'สัมผัส', // Rhymes - T291761
		105 => 'คุยเรื่องสัมผัส', // Rhymes_talk - T291761
		110 => 'อรรถาภิธาน', // Thesaurus - T198585
		111 => 'คุยเรื่องอรรถาภิธาน', // Thesaurus_talk
	],
	'trwiktionary' => [
		100 => 'Portal',
		101 => 'Portal_tartışma',
		102 => 'YeniKurum', // START T248734 - Reconstruction (Talk)
		103 => 'YeniKurum_tartışma',
		104 => 'Ek', // Appendix (Talk)
		105 => 'Ek_tartışma',
		106 => 'Alıntılar', // Citations (Talk)
		107 => 'Alıntılar_tartışma', // END T248734
	],
	'ukwiktionary' => [
		// T15791
		100 => 'Додаток', // appendix
		101 => 'Обговорення_додатка', // appendix talk
		102 => 'Індекс', // index
		103 => 'Обговорення_індексу', // index talk
	],
	'viwiktionary' => [
		100 => 'Phụ_lục', // Appendix - T298289
		101 => 'Thảo_luận_Phụ_lục', // Appendix talk
	],
	'wawiktionary' => [
		100 => 'Rawete', // T185289
		101 => 'Rawete_copene', // T185289
		102 => 'Sourdant', // T185289
		103 => 'Sourdant_copene', // T185289
		104 => 'Motyince', // T185289
		105 => 'Motyince_copene', // T185289
	],
	'urwiktionary' => [
		100 => 'ضمیمہ', // T186393 - Appendix
		101 => 'تبادلۂ_خیال_ضمیمہ', // T186393 - Appendix talk
		102 => 'کلید', // T186393 - Concordance
		103 => 'تبادلۂ_خیال_کلید', // T186393 - Concordance talk
		104 => 'اشاریہ', // T186393 - Index
		105 => 'تبادلۂ_خیال_اشاریہ', // T186393 - Index talk
		106 => 'قوافی', // T186393 - Rhymes
		107 => 'تبادلۂ_خیال_قوافی', // T186393 - Rhymes talk
		108 => 'ماورائے_ویکی', // T186393 - Transwiki
		109 => 'تبادلۂ_خیال_ماورائے_ویکی', // T186393 - Transwiki talk
		110 => 'مترادفات', // T186393 - Thesaurus
		111 => 'تبادلۂ_خیال_مترادفات', // T186393 - Thesaurus talk
		114 => 'حوالہ_جات', // T186393 - Citations
		115 => 'تبادلۂ_خیال_حوالہ_جات', // T186393 - Citations talk
		116 => 'فرہنگ_اشارات', // T186393 - Sign gloss
		117 => 'تبادلۂ_خیال_فرہنگ_اشارات', // T186393 - Sign gloss talk
		118 => 'تعمیرنو', // T186393 - Reconstruction
		119 => 'تبادلۂ_خیال_تعمیرنو', // T186393 - Reconstruction talk
	],
	'yuewiktionary' => [
		100 => '索引', // Index
		101 => '索引討論', // Index talk
		102 => '附錄', // Appendix
		103 => '附錄討論', // Appendix talk
		104 => '重構', // Reconstruction - T258913
		105 => '重構傾偈', // Reconstruction talk - T258913
	],
	'zhwiktionary' => [
		100 => 'Appendix', // T31641 - appendix
		101 => 'Appendix_talk', // T31641 - appendix talk
		102 => 'Transwiki', // T42474
		103 => 'Transwiki_talk',
		106 => 'Rhymes', // T241023
		107 => 'Rhymes_talk', // T241023
		110 => 'Thesaurus', // T309564
		111 => 'Thesaurus_talk', // T309564
		114 => 'Citations', // T309564
		115 => 'Citations_talk', // T309564
		118 => 'Reconstruction', // T286101
		119 => 'Reconstruction_talk', // T286101
	],
	// @} end of Wiktionary

	// Wikibooks @{
	'azwikibooks' => [
		102 => 'Resept', // Recipe
		103 => 'Resept_müzakirəsi', // Recipe talk
		110 => 'Vikiuşaq', // Wikijunior, T33067
		111 => 'Vikiuşaq_müzakirəsi',
	],
	'bnwikibooks' => [
		100 => 'উইকিশৈশব',
		101 => 'উইকিশৈশব_আলাপ',
		102 => 'বিষয়',
		103 => 'বিষয়_আলাপ',
		104 => 'রন্ধনপ্রণালী', // Cookbook, T203534
		105 => 'রন্ধনপ্রণালী_আলোচনা', // Cookbooks talk, T203534
	],
	'cawikibooks' => [
		100 => 'Portal', // T93811
		101 => 'Portal_Discussió', // T93811
		102 => 'Viquiprojecte',
		103 => 'Viquiprojecte_Discussió',
	],
	'cywikibooks' => [
		102 => 'Silff_lyfrau',
		103 => 'Sgwrs_Silff_lyfrau'
	],
	'dewikibooks' => [
		102 => 'Regal',
		103 => 'Regal_Diskussion'
	],
	'enwikibooks' => [
		102 => 'Cookbook',
		103 => 'Cookbook_talk',
		108 => 'Transwiki',
		109 => 'Transwiki_talk',
		110 => 'Wikijunior',
		111 => 'Wikijunior_talk',
		112 => 'Subject',
		113 => 'Subject_talk',
	],
	/*
	'eswikibooks' => [
		//102 => 'Wikiversidad',            // T42838 (ns removed)
		//103 => 'Wikiversidad_Discusión',
	],
	*/
	'fawikibooks' => [
		102 => 'کتاب‌آشپزی',
		103 => 'بحث_کتاب‌آشپزی',
		110 => 'ویکی‌کودک',
		111 => 'بحث_ویکی‌کودک',
		112 => 'موضوع',
		113 => 'بحث_موضوع',
	],
	'frwikibooks' => [
		100 => 'Transwiki',
		101 => 'Discussion_Transwiki',
		102 => 'Wikijunior', // T37977
		103 => 'Discussion_Wikijunior', // T37977
	],
	'hewikibooks' => [
		100 => 'שער', # portal
		101 => 'שיחת_שער', # portal talk
		// Skip 102 and 103, reserved for wikiproject
		104 => 'מדף', # bookshelf
		105 => 'שיחת_מדף', # bookshelf talk
	],
	'hiwikibooks' => [
		102 => 'रसोई', // Cookbook, T173398
		103 => 'रसोई_वार्ता', // Cookbooks talk, T173398
		106 => 'विषय', // Subject, T133440
		107 => 'विषय_चर्चा', // Subject talk, T133440
	],
	'hywikibooks' => [ // T55162
		102 => 'Եփութուխ', // cookbook
		103 => 'Եփութուխի_քննարկում', // cookbook talk
		110 => 'Վիքիփոքրիկ', // wikijunior
		111 => 'Վիքիփոքրիկի_քննարկում', // wikijunior talk
	],
	'idwikibooks' => [
		100 => 'Resep', // T9124
		101 => 'Pembicaraan_Resep',
		102 => 'Wisata',
		103 => 'Pembicaraan_Wisata',
	],
	'itwikibooks' => [
		100 => 'Progetto', // T10408
		101 => 'Discussioni_progetto', // T13938
		102 => 'Ripiano', // T13937
		103 => 'Discussioni_ripiano',
		# Wikiversità deleted, T12287
		# 102 => 'Wikiversità', // T9354
		# 103 => 'Discussioni_Wikiversità',
	],
	'jawikibooks' => [
		100 => 'Transwiki',
		101 => 'Transwiki‐ノート',
	],
	'kawikibooks' => [
		104 => 'თარო', # bookshelf
		105 => 'თარო_განხილვა', # bookshelf talk
	],
	'kuwikibooks' => [
		NS_PROJECT_TALK => 'Gotûbêja_Wîkîpirtûkê', // T39522
	],
	'mlwikibooks' => [
		100 => 'പാചകപുസ്തകം', // Cookbook
		101 => 'പാചകപുസ്തകസം‌വാദം',
		102 => 'വിഷയം', // Subject
		103 => 'വിഷയസം‌വാദം',
	],
	'mswikibooks' => [
		100 => 'Resipi',
		101 => 'Perbincangan_resipi',
	],
	'newikibooks' => [
		102 => 'पाकपुस्तक', // T131754, Cookbook
		103 => 'पाकपुस्तक_वार्ता', // T131754, Cookbook talk
		104 => 'बालपुस्तक', // T131754, Wikijunior
		105 => 'बालपुस्तक_वार्ता', // T131754, Wikijunior talk
		106 => 'विषय', // T131754, Subject
		107 => 'विषय_र्ता', // T131754, Subject talk
	],
	'nlwikibooks' => [
		102 => 'Transwiki',
		103 => 'Overleg_transwiki',
		104 => 'Wikijunior',
		105 => 'Overleg_Wikijunior',
	],
	'nowikibooks' => [
		102 => 'Kokebok', // Cookbook, T274265
		103 => 'Kokebok-diskusjon', // Cookbook talk, T274265
	],
	'plwikibooks' => [
		NS_PROJECT_TALK => 'Dyskusja_Wikibooks',
		NS_USER => 'Wikipedysta',
		NS_USER_TALK => 'Dyskusja_wikipedysty',
		104 => 'Wikijunior',
		105 => 'Dyskusja_Wikijuniora',
	],
	'rowikibooks' => [
		100 => 'Raft',
		101 => 'Discuţie_Raft',
		102 => 'Wikijunior',
		103 => 'Discuţie_Wikijunior',
		104 => 'Carte_de_bucate',
		105 => 'Discuţie_Carte_de_bucate',
	],
	'ruwikibooks' => [
		100 => 'Полка',
		101 => 'Обсуждение_полки',
		102 => 'Импортировано',
		103 => 'Обсуждение_импортированного',
		104 => 'Рецепт',
		105 => 'Обсуждение_рецепта',
		106 => 'Задача',
		107 => 'Обсуждение_задачи',
	],
	'shnwikibooks' => [
		102 => 'ပပ်ႉတူမ်ႈႁုင်', // T327850, Cookbook
		103 => 'ဢုပ်ႇၵုမ်_ပပ်ႉတူမ်ႈႁုင်', // T327850, Cookbook talk
		108 => 'ထရၼ်ႇသ်ဝီႇၶီႇ', // T327850, Transwiki
		109 => 'ဢုပ်ႇၵုမ်_ထရၼ်ႇသ်ဝီႇၶီႇ', // T327850, Transwiki talk
		110 => 'ဝီႇၶီႇလုၵ်ႈဢွၼ်ႇ', // T327850, Wikijunior
		111 => 'ဢုပ်ႇၵုမ်_ဝီႇၶီႇလုၵ်ႈဢွၼ်ႇ', // T327850, Wikijunior talk
		112 => 'ပၢႆး', // T327850, Subject
		113 => 'ဢုပ်ႇၵုမ်_ပၢႆး', // T327850, Subject talk
	],
	'siwikibooks' => [
		112 => 'විෂයය',
		113 => 'විෂයය_සාකච්ඡාව',
		114 => 'කණිෂ්ඨ_විකි',
		115 => 'කණිෂ්ඨ_විකි_සාකච්ඡාව',
	],
	'srwikibooks' => [
		102 => 'Кувар',
		103 => 'Разговор_о_кувару'
	],
	'thwikibooks' => [
		102 => 'หัวเรื่อง', // T48153
		103 => 'คุยเรื่องหัวเรื่อง', // T48153
		104 => 'ตำราอาหาร', // Cookbook, T216322
		105 => 'คุยเรื่องตำราอาหาร', // Cookbook talk, T216322
		106 => 'วิกิเยาวชน', // Wikijunior, T216322
		107 => 'คุยเรื่องวิกิเยาวชน', // Wikijunior talk, T216322
	],
	'tlwikibooks' => [
		100 => 'Pagluluto',
		101 => 'Usapang_pagluluto',
		102 => 'Wikijunior', // Wikijunior - T274976
		103 => 'Usapang_Wikijunior', // Wikijunior talk - T274976
	],
	'trwikibooks' => [
		100 => 'Yemek',
		101 => 'Yemek_tartışma',
		110 => 'Vikiçocuk',
		111 => 'Vikiçocuk_tartışma',
		112 => 'Kitaplık',
		113 => 'Kitaplık_tartışma',
	],
	'ukwikibooks' => [
		100 => 'Полиця',
		101 => 'Обговорення_полиці',
		102 => 'Рецепт',
		103 => 'Обговорення_рецепта',
	],
	'viwikibooks' => [
		102 => 'Chủ_đề', // Subject
		103 => 'Thảo_luận_Chủ_đề',
		104 => 'Trẻ_em', // Wikijunior
		105 => 'Thảo_luận_Trẻ_em', // Wikijunior talk
		106 => 'Nấu_ăn', // Cookbook
		107 => 'Thảo_luận_Nấu_ăn', // Cookbook talk
	],
	'zhwikibooks' => [
		100 => 'Transwiki', // T42474
		101 => 'Transwiki_talk', // T42474
		110 => 'Wikijunior', // T46308
		111 => 'Wikijunior_talk', // T46308
		112 => 'Subject', // T46308
		113 => 'Subject_talk', // T46308
	],
	// @} end of Wikibooks

	// Wikinews @{
	'arwikinews' => [
		100 => 'بوابة',
		101 => 'نقاش_البوابة',
		102 => 'تعليقات',
		103 => 'نقاش_التعليقات',
	],
	'bgwikinews' => [
		102 => 'Мнения',
		103 => 'Мнения_беседа',
	],
	'cawikinews' => [
		100 => 'Transwiki',
		101 => 'Transwiki_talk',
		102 => 'Secció',
		103 => 'Secció_Discussió',
	],
	'cswikinews' => [
		NS_USER => 'Redaktor', # language default set back in wgNamespaceAliases
		NS_USER_TALK => 'Diskuse_s_redaktorem', # language default set back in wgNamespaceAliases
	],
	'dewikinews' => [
		100 => 'Portal',
		101 => 'Portal_Diskussion',
		102 => 'Meinungen',
		103 => 'Meinungen_Diskussion'
	],
	'elwikinews' => [
		102 => 'Σχόλια', // Comments
		103 => 'Συζήτηση_σχολίων', // Comment talk
	],
	'enwikinews' => [
		100 => 'Portal',
		101 => 'Portal_talk',
		102 => 'Comments',
		103 => 'Comments_talk'
	],
	'eswikinews' => [
		100 => 'Comentarios',
		101 => 'Comentarios_Discusión',
	],
	'fawikinews' => [
		100 => 'درگاه',
		101 => 'بحث_درگاه',
		102 => 'نظرها',
		103 => 'بحث_نظرها',
	],
	'frwikinews' => [
		102 => 'Transwiki',
		103 => 'Discussion_Transwiki',
		104 => 'Page',
		105 => 'Discussion_Page',
		106 => 'Dossier',
		107 => 'Discussion_Dossier',
	],
	'hewikinews' => [
		100 => 'מדור', // Portal - T349581
		101 => 'שיחת_מדור',
		118 => 'טיוטה', // Draft - T349581
		119 => 'שיחת_טיוטה',
		NS_CATEGORY => 'תגית', // overriding default from MessagesHe.php - T349581
		NS_CATEGORY_TALK => 'שיחת_תגית',
	],
	'huwikinews' => [
		102 => 'Portál',
		103 => 'Portálvita',
	],
	'itwikinews' => [
		100 => 'Portale',
		101 => 'Discussioni_portale',
	],
	'jawikinews' => [
		100 => "ポータル",
		101 => "ポータル・トーク",
		108 => '短信',
		109 => '短信‐ノート',
	],
	'nowikinews' => [
		100 => 'Kommentarer',
		101 => 'Kommentarer-diskusjon',
		106 => 'Portal',
		107 => 'Portal-diskusjon',
	],
	'plwikinews' => [
		NS_PROJECT_TALK => 'Dyskusja_Wikinews',
		NS_USER => 'Wikireporter',
		NS_USER_TALK => 'Dyskusja_wikireportera',
		100 => 'Portal',
		101 => 'Dyskusja_portalu'
	],
	'ptwikinews' => [
		// ID 102 and 103 were used before (T285163), do not re-use
		100 => 'Portal',
		101 => 'Portal_Discussão',
		104 => 'Transwiki',
		105 => 'Transwiki_Discussão',
		110 => 'Colaboração', // T94894
		111 => 'Colaboração_Discussão',
	],
	'ruwikinews' => [
		100 => 'Портал',
		101 => 'Обсуждение_портала',
		102 => 'Комментарии',
		103 => 'Обсуждение_комментариев',
	],
	'sqwikinews' => [
		100 => 'Portal',
		101 => 'Portal_diskutim',
		102 => 'Komentet',
		103 => 'Komentet_diskutim',
	],
	'srwikinews' => [
		100 => 'Портал',
		101 => 'Разговор_о_порталу',
	],
	'svwikinews' => [
		100 => 'Portal',
		101 => 'Portaldiskussion',
	],
	'tawikinews' => [
		100 => 'வலைவாசல்', // PPortal
		101 => 'வலைவாசல்_பேச்சு',
	],
	'trwikinews' => [
		100 => 'Portal',
		101 => 'Portal_tartışma',
		106 => 'Yorum',
		107 => 'Yorum_tartışma',
	],
	'ukwikinews' => [
		102 => 'Коментарі', // T47333 - Comments
		103 => 'Обговорення_коментарів',
		104 => 'Інкубатор', // T47333 - Incubator
		105 => 'Обговорення_інкубатора',
	],
	'zhwikinews' => [
		100 => 'Portal', // T257101
		101 => 'Portal_talk', // T257101
	],
	// @} end of Wikinews

	// Wikiquote @{
	'cswikiquote' => [
		100 => 'Dílo',
		101 => 'Diskuse_k_dílu',
	],
	'dewikiquote' => [
		100 => 'Portal',
		101 => 'Portal_Diskussion',
	],
	'enwikiquote' => [
		118 => 'Draft', // T355195
		119 => 'Draft_talk', // T355195
	],
	'frwikiquote' => [
		100 => 'Portail',
		101 => 'Discussion_Portail',
		102 => 'Projet',
		103 => 'Discussion_Projet',
		104 => 'Référence',
		105 => 'Discussion_Référence',
		108 => 'Transwiki',
		109 => 'Discussion_Transwiki',
	],
	'hewikiquote' => [
		100 => 'פורטל',
		101 => 'שיחת_פורטל'
	],
	'itwikiquote' => [
		100 => 'Portale', // T185232
		101 => 'Discussioni_portale',
	],
	'kuwikiquote' => [
		NS_PROJECT_TALK => 'Gotûbêja_Wîkîgotinê', // T39523
	],
	'liwikiquote' => [ // T14240
		100 => 'Portaol',
		101 => 'Euverlèk_portaol'
	],
	'plwikiquote' => [
		NS_PROJECT_TALK => 'Dyskusja_Wikicytatów',
	],
	'skwikiquote' => [
		100 => 'Deň', // T46052
		101 => 'Diskusia_ku_dňu',
	],
	'zhwikiquote' => [ // T42474
		100 => 'Transwiki',
		101 => 'Transwiki_talk',
	],
	// @} end of Wikiquote

	// Wikiversity @{
	'arwikiversity' => [
		100 => 'مدرسة',
		101 => 'نقاش_المدرسة',
		102 => 'بوابة',
		103 => 'نقاش_البوابة',
		104 => 'موضوع',
		105 => 'نقاش_الموضوع',
		106 => 'مجموعة',
		107 => 'نقاش_المجموعة',
	],
	'cswikiversity' => [
		100 => 'Fórum',
		101 => 'Diskuse_k_fóru',
	],
	'dewikiversity' => [
		106 => 'Kurs',
		107 => 'Kurs_Diskussion',
		108 => 'Projekt',
		109 => 'Projekt_Diskussion',
	],
	'elwikiversity' => [
		100 => 'Σχολή',
		101 => 'Συζήτηση_Σχολής',
		102 => 'Τμήμα',
		103 => 'Συζήτηση_Τμήματος',
	],
	'enwikiversity' => [
		100 => 'School',
		101 => 'School_talk',
		102 => 'Portal',
		103 => 'Portal_talk',
		104 => 'Topic',
		105 => 'Topic_talk',
		106 => 'Collection',
		107 => 'Collection_talk',
		118 => 'Draft', // T184957
		119 => 'Draft_talk',
	],
	'frwikiversity' => [
		102 => 'Projet',
		103 => 'Discussion_Projet',
		104 => 'Recherche', // T31015
		105 => 'Discussion_Recherche',
		106 => 'Faculté',
		107 => 'Discussion_Faculté',
		108 => 'Département',
		109 => 'Discussion_Département',
		110 => 'Transwiki',
		111 => 'Discussion_Transwiki',
	],
	'hiwikiversity' => [
		100 => 'प्रवेशद्वार', // T168765, Portal
		101 => 'प्रवेशद्वार_वार्ता', // T168765
		102 => 'विद्यालय', // T168765, School
		103 => 'विद्यालय_वार्ता', // T168765
		104 => 'विषय', // T168765, Topic
		105 => 'विषय_वार्ता', // T168765
		106 => 'संग्रह', // T168765, Collection
		107 => 'संग्रह_वार्ता', // T168765
		118 => 'निर्माणाधीन', // T187535, Draft
		119 => 'निर्माणाधीन_वार्ता', // T187535, Draft talk
	],
	'itwikiversity' => [
		100 => 'Area',
		101 => 'Discussioni_area',
		102 => 'Corso',
		103 => 'Discussioni_corso',
		104 => 'Materia',
		105 => 'Discussioni_materia',
		106 => 'Dipartimento',
		107 => 'Discussioni_dipartimento',
	],
	'jawikiversity' => [
		100 => 'School',
		101 => 'School‐ノート',
		102 => 'Portal',
		103 => 'Portal‐ノート',
		104 => 'Topic',
		105 => 'Topic‐ノート',
		110 => 'Transwiki',
		111 => 'Transwiki‐ノート',
	],
	'kowikiversity' => [
		NS_PROJECT_TALK => '위키배움터토론', // T46899
		102 => '포털', // T46899
		103 => '포털토론', // T46899
	],
	'ruwikiversity' => [
		100 => 'Портал', // T293545, Portal
		101 => 'Обсуждение_портала', // T293545, Portal talk
		102 => 'Факультет', // T293545, Faculty
		103 => 'Обсуждения_факультета', // T293545, Faculty talk
	],
	'svwikiversity' => [
		100 => 'Portal',
		101 => 'Portaldiskussion',
	],
	'zhwikiversity' => [
		100 => 'Portal', // T201675
		101 => 'Portal_talk', // T201675
		102 => 'School', // T201675
		103 => 'School_talk', // T201675
		104 => 'Subject', // T201675
		105 => 'Subject_talk', // T201675
		106 => 'Experiment', // T212919
		107 => 'Experiment_talk', // T212919
		108 => 'Lesson', // T212919
		109 => 'Lesson_talk', // T212919
	],
	// @} end of Wikiversity

	// Wikivoyage @{
	'dewikivoyage' => [
		100 => 'Portal',
		101 => 'Portal_Diskussion',
		102 => 'Wahl',
		103 => 'Wahl_Diskussion',
		104 => 'Thema',
		105 => 'Thema_Diskussion',
		106 => 'Nachrichten',
		107 => 'Nachrichten_Diskussion',
	],
	'fawikivoyage' => [
		100 => 'درگاه', // Portal - T113593
		101 => 'بحث_درگاه', // Portal talk - T113593
		106 => 'عبارت‌نامه', // Phrasebook - T73382
		107 => 'بحث_عبارت‌نامه', // Phrasebook talk - T73382
	],
	'hewikivoyage' => [
		108 => 'ספר', // Book
		109 => 'שיחת_ספר', // Book talk
		110 => 'מפה', // Map
		111 => 'שיחת_מפה',
	],
	'itwikivoyage' => [
		100 => 'Portale',
		101 => 'Discussioni_portale',
		// 102 and 103 (Elezione) deleted per T47636
		// 104 and 105 (Tematica) deleted per T298315
		// 106 and 107 (Notizie) deleted per T47636
	],
	'jawikivoyage' => [
		# Override MediaWiki default namespace names for "ja" - T262155
		NS_TALK => 'ノート',
		NS_USER_TALK => '利用者・ノート',
		NS_PROJECT_TALK => 'Wikivoyage・ノート',
		NS_FILE_TALK => 'ファイル・ノート',
		NS_MEDIAWIKI_TALK => 'MediaWiki・ノート',
		NS_TEMPLATE_TALK => 'テンプレート・ノート',
		NS_HELP_TALK => 'ヘルプ・ノート',
		NS_CATEGORY_TALK => 'カテゴリ・ノート',
		# Additional namespaces
		// 102 and 103 used because they're the same as in jawiki
		102 => 'プロジェクト',
		103 => 'プロジェクト・ノート', // T262155
		829 => 'モジュール・ノート', // T262155
	],
	'plwikivoyage' => [
		NS_PROJECT_TALK => 'Dyskusja_Wikipodróży',
	],
	'ukwikivoyage' => [
		100 => 'Портал', // Portal
		101 => 'Обговорення_порталу', // Portal talk
	],
	// @} end of Wikivoyage

],
# @} end of wgExtraNamespaces

'wgNamespaceRobotPolicies' => [
	'arwiki' => [
		NS_USER => 'noindex,nofollow', // T371470
	],
	'azwiki' => [
		118 => 'noindex,nofollow', // Draft - T299332
		119 => 'noindex,nofollow',
	],
	'bgwiki' => [
		118 => 'noindex,nofollow', // Draft - T299224
		119 => 'noindex,nofollow', // Draft_talk - T299224
	],
	'bnwiki' => [
		NS_USER => 'noindex,follow', // T286152
		NS_USER_TALK => 'noindex,follow',
		118 => 'noindex,nofollow', // Draft - T350133
		119 => 'noindex,nofollow', // Draft_talk - T350133
	],
	'ckbwiki' => [
		NS_TALK => 'noindex,follow',
		NS_USER => 'noindex,follow',
		NS_USER_TALK => 'noindex,follow',
		NS_PROJECT_TALK => 'noindex,follow',
		NS_FILE_TALK => 'noindex,follow',
		NS_MEDIAWIKI_TALK => 'noindex,follow',
		NS_TEMPLATE_TALK => 'noindex,follow',
		NS_HELP_TALK => 'noindex,follow',
		NS_CATEGORY_TALK => 'noindex,follow',
		101 => 'noindex,follow', // portal talk
		103 => 'noindex,follow', // wikiproject talk
		829 => 'noindex,follow', // module talk
		118 => 'noindex,nofollow', // draft
		119 => 'noindex,nofollow', // draft talk
	], // T73663, T332470
	'cswiki' => [
		NS_USER => 'noindex,follow', // T125068
		NS_USER_TALK => 'noindex,follow',
	],
	'dewiki' => [
		NS_TALK => 'noindex,follow',
		NS_USER => 'noindex,follow', // T38181
		NS_USER_TALK => 'noindex,follow',
		NS_PROJECT_TALK => 'noindex,follow',
		NS_FILE_TALK => 'noindex,follow',
		NS_MEDIAWIKI_TALK => 'noindex,follow',
		NS_TEMPLATE_TALK => 'noindex,follow',
		NS_HELP_TALK => 'noindex,follow',
		NS_CATEGORY_TALK => 'noindex,follow',
		101 => 'noindex,follow', // portal talk
		829 => 'noindex,follow', // module talk
	],
	'dawiki' => [
		NS_USER => 'noindex,follow',
		NS_USER_TALK => 'noindex,follow',
		NS_SPECIAL => 'noindex,follow',
	],
	'enwiki' => [
		NS_USER => 'noindex,follow', // T104797
		NS_USER_TALK => 'noindex,follow',
		118 => 'noindex,nofollow', // draft
		119 => 'noindex,nofollow', // draft talk
	],
	'enwikiquote' => [
		118 => 'noindex,nofollow', // draft, T355195
	],
	'eswiki' => [ // T233562
		NS_TALK => 'noindex,follow',
		NS_USER => 'noindex,follow',
		NS_USER_TALK => 'noindex,follow',
		NS_PROJECT_TALK => 'noindex,follow',
		NS_FILE_TALK => 'noindex,follow',
		NS_MEDIAWIKI_TALK => 'noindex,follow',
		NS_TEMPLATE_TALK => 'noindex,follow',
		NS_HELP_TALK => 'noindex,follow',
		NS_CATEGORY_TALK => 'noindex,follow',
		101 => 'noindex,follow',
		103 => 'noindex,follow',
		105 => 'noindex,follow',
		829 => 'noindex,follow',
	],
	'fawiki' => [
		NS_USER => 'noindex,nofollow', // T299363
		NS_USER_TALK => 'noindex,nofollow', // T299363
		118 => 'noindex,nofollow', // draft, T92760
		119 => 'noindex,nofollow', // draft talk, T92760
	],
	'foundationwiki' => [ // T288763
		112 => 'noindex,follow', // archives
		113 => 'noindex,follow', // archives talk
	],
	'frwiki' => [
		NS_USER => 'noindex,follow',
		NS_USER_TALK => 'noindex,follow',
	],
	'hewiki' => [
		NS_USER => 'noindex,follow', // T18247
		118 => 'noindex,nofollow', // Draft - T86329
		119 => 'noindex,nofollow',
	],
	'hewikinews' => [
		118 => 'noindex,nofollow', // Draft - T349581
		119 => 'noindex,nofollow',
	],
	'hewikisource' => [
		NS_TALK => 'noindex,follow',
		NS_USER => 'noindex,follow',
		NS_USER_TALK => 'noindex,follow',
		NS_PROJECT_TALK => 'noindex,follow',
		NS_FILE_TALK => 'noindex,follow',
		NS_MEDIAWIKI_TALK => 'noindex,follow',
		NS_TEMPLATE_TALK => 'noindex,follow',
		NS_HELP_TALK => 'noindex,follow',
		NS_CATEGORY_TALK => 'noindex,follow',
		104 => 'noindex,nofollow', // עמוד
		105 => 'noindex,nofollow', // שיחת_עמוד
		112 => 'noindex,nofollow', // מפתח
		113 => 'noindex,nofollow', // שיחת_מפתח
		116 => 'noindex,nofollow', // מקור
		117 => 'noindex,nofollow', // שיחת_מקור
	],
	'hrwiki' => [
		NS_USER => 'noindex,follow', // T284384
		NS_USER_TALK => 'noindex,follow', // T284384
		118 => 'noindex,nofollow', // draft, T284384
		119 => 'noindex,nofollow', // draft talk, T284384
	],
	'huwiki' => [
		118 => 'noindex,nofollow', // draft, T333083
		119 => 'noindex,nofollow', // draft talk, T333083
	],
	'hywiki' => [
		NS_USER => 'noindex,follow', // T257112
		NS_USER_TALK => 'noindex,follow', // T257112
		118 => 'noindex,follow', // T260804
		119 => 'noindex,follow', // T260804
	],
	'iswiki' => [ // T231179
		NS_TALK => 'noindex',
		NS_USER => 'noindex',
		NS_USER_TALK => 'noindex',
		NS_PROJECT_TALK => 'noindex',
		NS_FILE_TALK => 'noindex',
		NS_MEDIAWIKI_TALK => 'noindex',
		NS_TEMPLATE_TALK => 'noindex',
		NS_HELP_TALK => 'noindex',
		NS_CATEGORY_TALK => 'noindex',
	],
	'itwiki' => [
		NS_USER => 'noindex,follow', // T107992
		NS_USER_TALK => 'noindex,follow', // T314165
		118 => 'noindex,nofollow', // T280289
		119 => 'noindex,nofollow', // T280289
	],
	'knwiki' => [
		118 => 'noindex,nofollow', // Draft - T341958
		119 => 'noindex,nofollow', // T341958
	],
	'kowiki' => [
		NS_USER => 'noindex,follow', // T121301
		118 => 'noindex,nofollow', // Draft - T92798
		119 => 'noindex,nofollow', // T92798
	],
	'mlwiki' => [ // Draft - T362653
		118 => 'noindex,nofollow',
		119 => 'noindex,nofollow',
	],
	'mywiki' => [ // T352424
		118 => 'noindex,nofollow', // Draft
		119 => 'noindex,nofollow', // Draft_talk
	],
	'nlwiki' => [
		NS_USER => 'noindex,follow', // T245787
		NS_USER_TALK => 'noindex,follow', // T245787
	],
	'ptwiki' => [
		NS_USER => 'noindex,follow', // T185660
	],
	'ruwiki' => [
		102 => 'noindex,follow',
		103 => 'noindex,follow',
		104 => 'noindex,follow',
		105 => 'noindex,follow',
		106 => 'noindex,follow',
		107 => 'noindex,follow',
	],
	'shwiki' => [
		NS_USER => 'noindex,follow', // T346589
		NS_USER_TALK => 'noindex,follow', // T346589
		118 => 'noindex,nofollow', // draft, T346589
		119 => 'noindex,nofollow', // draft talk, T346589
	],
	'srwiki' => [
		NS_USER => 'noindex,follow', // T248860
		NS_USER_TALK => 'noindex,follow', // T248860
		118 => 'noindex,nofollow', // draft, T248860
		119 => 'noindex,nofollow', // draft talk, T248860
	],
	'tawiki' => [
		118 => 'noindex,nofollow', // T329248
		119 => 'noindex,nofollow', // T329248
	],
	'thwiki' => [
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
		101 => 'noindex,follow',
		109 => 'noindex,nofollow', // T253574
		118 => 'noindex,nofollow', // T253574
		119 => 'noindex,nofollow', // T253574
		829 => 'noindex,nofollow', // T253574
	],
	'thwikibooks' => [
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
		103 => 'noindex, follow',
		105 => 'noindex, follow',
		107 => 'noindex, follow',
		829 => 'noindex, follow',
	],
	'thwiktionary' => [
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
		101 => 'noindex, follow',
		103 => 'noindex, follow',
		105 => 'noindex, follow',
		829 => 'noindex, follow',
	],
	'thwikiquote' => [
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
		829 => 'noindex, follow',
	],
	'thwikisource' => [
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
		101 => 'noindex, follow',
		103 => 'noindex, follow',
		115 => 'noindex, follow',
		251 => 'noindex, follow',
		253 => 'noindex, follow',
		829 => 'noindex, follow',
	],
	'trwiki' => [
		NS_USER => 'noindex,follow', // T255538
		NS_USER_TALK => 'noindex,follow', // T255538
	],
	'uawikimedia' => [
		NS_USER => 'noindex,follow', // T122732
	],
	'ukwiki' => [
		NS_USER => 'noindex,follow', // T98926
	],
	'wikidatawiki' => [ // T181525
		NS_USER => 'noindex,follow',
		NS_USER_TALK => 'noindex,follow',
	],
	'zh_classicalwiki' => [
		106 => 'noindex,nofollow', // T163655
		107 => 'noindex,nofollow', // T163655
	],
	'zhwiki' => [
		NS_USER => 'noindex,nofollow', // T231982
		NS_USER_TALK => 'noindex,nofollow', // T231982
		118 => 'noindex,nofollow', // T91223
		119 => 'noindex,nofollow',
	],
],
# @} end of ROBOT

# wgNamespaceProtection @{
'wgNamespaceProtection' => [
	'default' => [
		NS_MEDIAWIKI => [ 'editinterface' ],
	],
	'+apiportalwiki' => [
		NS_MAIN	  => [ 'edit-docs' ],
		NS_PROJECT   => [ 'edit-docs' ],
		// Local uploads are disabled...
		NS_FILE	  => [ 'edit-docs' ],
		NS_TEMPLATE  => [ 'edit-docs' ],
		NS_HELP	  => [ 'edit-docs' ],
		NS_CATEGORY  => [ 'edit-docs' ],
		NS_USER	  => [ 'edit-docs' ], // T259568
	],
	'+cswiki' => [
		NS_FILE      => [ 'editinterface' ],
		NS_FILE_TALK => [ 'editinterface' ],
	],
	'+cswikibooks' => [
		NS_FILE      => [ 'editinterface' ],
		NS_FILE_TALK => [ 'editinterface' ],
	],
	'+cswikinews' => [
		NS_FILE      => [ 'editinterface' ],
		NS_FILE_TALK => [ 'editinterface' ],
	],
	'+cswikiquote' => [
		NS_FILE      => [ 'editinterface' ],
		NS_FILE_TALK => [ 'editinterface' ],
	],
	'+cswikisource' => [
		NS_FILE      => [ 'editinterface' ],
		NS_FILE_TALK => [ 'editinterface' ],
	],
	'+cswikiversity' => [
		NS_FILE      => [ 'editinterface' ],
		NS_FILE_TALK => [ 'editinterface' ],
	],
	'+cswiktionary' => [
		NS_FILE      => [ 'editinterface' ],
		NS_FILE_TALK => [ 'editinterface' ],
	],
	'+eswiki' => [
		NS_FILE => [ 'editinterface' ],
		NS_FILE_TALK => [ 'editinterface' ],
		828 => [ 'autoconfirmed' ], // T55558 - Module:
	],
	'+eswikibooks' => [
		828 => [ 'autoconfirmed' ], // T202555 - Module:
	],
	'+foundationwiki' => [
		NS_MAIN => [ 'edit-legal' ],
		NS_PROJECT => [ 'edit-legal' ],
		NS_FILE => [ 'edit-legal' ],
		NS_CATEGORY => [ 'edit-legal' ],
		NS_HELP => [ 'edit-legal' ],
		NS_TEMPLATE => [ 'edit-legal' ],
		100 => [ 'edit-legal' ], // Resolution
		102 => [ 'edit-legal' ], // Legal
		104 => [ 'edit-legal' ], // Minutes
		106 => [ 'edit-legal' ], // Policy
		108 => [ 'edit-legal' ], // T347762 - Endowment
		110 => [ 'edit-legal' ], // T347822 - Agenda
		112 => [ 'edit-legal' ], // Archive
		114 => [ 'edit-legal' ], // T348268 - Memory
		116 => [ 'edit-legal' ], // T347822 - Committee
		710 => [ 'edit-legal' ], // TimedText
		828 => [ 'edit-legal' ], // Module
	],
	'+metawiki' => [
		866 => [ 'autoconfirmed' ], // T238723 - CNBanner:
	],
	'+ptwiki' => [
		NS_FILE => [ 'autoconfirmed' ],
	],
	'ruwiki' => [
		106 => [ 'autoconfirmed' ],
	],
	'+wikidatawiki' => [
		120 => [ 'autoconfirmed' ], // Property namespace per T254280
		122 => [ 'query-update' ], // Query namespace per T51001
	],
	'+wikitech' => [
		666 => [ 'editprotected' ], // Block editing of former Hiera: namespace.
	],
],
# @} end of wgNamespaceProtection

# wgNamespacesToBeSearchedDefault @{
'wgNamespacesToBeSearchedDefault' => [
	'default' => [ 0 => 1 ], // T230797
	'+arwikisource' => [ 102 => 1 ],
	'+aswikisource' => [ 104 => 1, 106 => 1 ], // T45129
	'+bgwiki' => [ 100 => 1 ],
	'+bgwikisource' => [ 100 => 1 ],
	'+bnwikisource' => [
		// T178041 - portal, author, translation and T199028 - Publisher
		100 => 1,
		106 => 1,
		108 => 1,
		114 => 1,

		110 => 1, // T258982 - Work
	],
	'+brwikisource' => [ 100 => 1, 104 => 1 ],
	'+cawikisource' => [ 104 => 1, 106 => 1 ],
	'+cswiki' => [ 100 => 1, 101 => 0, 102 => 1 ],
	'+commonswiki' => [ 6 => 1, 12 => 1, 14 => 1, 100 => 1, 106 => 1 ],
	'+testcommonswiki' => [ 6 => 1, 12 => 1, 14 => 1, 100 => 1, 106 => 1 ],
	'+dawikisource' => [ 102 => 1, 106 => 1 ],
	'+dewikisource' => [ 102 => 1, 104 => 1 ],
	'+dewikiversity' => [ 106 => 1, 108 => 1 ],
	'+dewikivoyage' => [ 6 => 1, 14 => 1, 100 => 1, 104 => 1, 106 => 1 ],
	'+elwikisource' => [ 102 => 1, 108 => 1 ],
	'+enwikibooks' => [ 4 => 1, 102 => 1, 110 => 1, 112 => 1 ], // T176906
	'+enwikinews' => [ 14 => 1, ], // T87522
	'+enwikisource' => [ 100 => 1, 102 => 1, 106 => 1, 114 => 1 ], // T52007, T167511
	'+eowiktionary' => [ 102 => 1 ], // T237792
	'+eswiki' => [ 100 => 1, 104 => 1 ],
	'+eswikisource' => [ 104 => 1 ],
	'+etwikisource' => [ 104 => 1, 106 => 1 ],
	'+fawikibooks' => [ 102 => 1, 110 => 1 ], // T176908
	'+fawikisource' => [ 102 => 1 ],
	'+foundationwiki' => [ 100 => 1, 102 => 1, 104 => 1, 106 => 1, 108 => 1, 110 => 1, 114 => 1, 116 => 1 ], // T206173, T347762, T347822, T348268
	'+frrwiki' => [ 102 => 1, 104 => 1, 106 => 1 ], // T40023
	'+frwikisource' => [ 102 => 1, 112 => 1 ],
	'+frwikiversity' => [ 104 => 1, 106 => 1, 108 => 1 ],
	'+frwiktionary' => [ 100 => 1, 106 => 1, 110 => 1, ], // T94698, T205198
	'+hewikisource' => [ 100 => 1, 106 => 1, 108 => 1, 110 => 1, 112 => 1 ], // T176907
	'+hewiktionary' => [ 14 => 1 ],
	'+hrwikisource' => [ 100 => 1, 104 => 1 ],
	'+huwikisource' => [ 100 => 1, 106 => 1 ],
	'+hywikisource' => [ 100 => 1, 106 => 1 ],
	'+idwikibooks' => [ 100 => 1, 102 => 1 ],
	'+idwikisource' => [ 100 => 1, 102 => 1 ],
	'+iswikisource' => [ 100 => 1, 102 => 1 ], // T46164
	'+itwikisource' => [ 102 => 1, 110 => 1 ],
	'+itwikiversity' => [ 100 => 1, 102 => 1, 104 => 1 ], // T114932
	'+itwikivoyage' => [ 10 => 1, 828 => 1 ], // T358456
	'+kowikisource' => [ 100 => 1, 114 => 1 ], // T183836 for 114
	'+lawikisource' => [ 102 => 1, 106 => 1 ],
	'+wikitech' => [ 12 => 1, 116 => 1, 498 => 1 ],
	'+ltwiki' => [ 100 => 1 ],
	'+mediawikiwiki' => [ 12 => 1, 100 => 1, 102 => 1, 104 => 1, 106 => 1 ], // T85807
	'+metawiki' => [ 12 => 1, 200 => 1, 202 => 1 ],
	'+mlwikisource' => [ 100 => 1, 104 => 1 ],
	'+nlwikisource' => [ 102 => 1 ],
	'+nlwikivoyage' => [ 6 => 1, 14 => 1, 100 => 1, 104 => 1, 106 => 1 ],
	'+nowikimedia' => [ 100 => 1 ], // T181625
	'+nowikisource' => [ 102 => 1, 106 => 1 ],
	'+otrs_wikiwiki' => [ 100 => 1 ], // T266917
	'+pawikisource' => [ 100 => 1, 110 => 1, 114 => 1, 250 => 1, 252 => 1 ], // T287887
	'+plwiktionary' => [ 100 => 1, 102 => 1 ],
	'+plwikisource' => [ 102 => 1, 104 => 1, 124 => 1, ], // T154711
	'+ptwikisource' => [ 102 => 1, 104 => 1 ],
	'+rowikisource' => [ 102 => 1, 106 => 1 ], // T31190
	'+ruwikibooks' => [ 104 => 1 ], // Recipe, T341708
	'+ruwikisource' => [ 106 => 1 ],
	'+ruwikivoyage' => [ 6 => 1, 14 => 1, 100 => 1, 104 => 1, 106 => 1 ],
	'+sewikimedia' => [ 100 => 1 ], // T48882
	'+slwikisource' => [ 104 => 1 ],
	'+sourceswiki' => [ 106 => 1 ],
	'+svwikisource' => [ 106 => 1, 108 => 1 ],
	'+svwikivoyage' => [ 6 => 1, 14 => 1, 100 => 1, 104 => 1, 106 => 1 ],
	'+strategywiki' => [ 106 => 1, 107 => 1 ], // T22514
	'+tewikisource' => [ 102 => 1, 106 => 1 ],
	'+thwikibooks' => [ 4 => 1, 102 => 1, 104 => 1, 106 => 1 ], // T308373
	'+thwikisource' => [ 100 => 1, 102 => 1, 114 => 1, 252 => 1 ], // T275280
	'+tlwikibooks' => [ 100 => 1 ],
	'+trwikisource' => [ 100 => 1 ],
	'+ukwikinews' => [ 14 => 1 ], // T51335
	'+ukwikisource' => [ 102 => 1, 114 => 1, 116 => 1, 252 => 1 ], // T52561, T53684, T270627
	'+vecwikisource' => [ 100 => 1, 104 => 1 ],
	'+viwikibooks' => [ 102 => 1, 104 => 1, 106 => 1 ],
	'+viwikisource' => [ 102 => 1, 106 => 1 ],
	'+testwikidatawiki' => [ 120 => 1 ], // Search properties by default
	'+wikidatawiki' => [ 120 => 1 ], // Search properties by default
	'+wikimaniawiki' => [
		136 => 1, // 2023, T316928
		138 => 1, // 2024, T332782
	],
	'+zhwikisource' => [
		100 => 1, // T308393, Portal
		102 => 1, 106 => 1, 114 => 1, // T66127
	],
],
# @} end of wgNamespacesToBeSearchedDefault

// Note that changing this for wikis with CirrusSearch will remove pages in the
// affected namespace from search results until a full reindex is completed.
'wgContentNamespaces' => [
	'default' => [ NS_MAIN ],
	'+arwikisource' => [ 102 ],
	'+aswikisource' => [ 102 ], // T45129, T72464
	'+bgwikisource' => [ 100 ],
	'+bnwikisource' => [ 100 ],
	'+bnwikibooks' => [ 104 ], // T236840
	'+brwikisource' => [ 104 ],
	'+cawikisource' => [ 106 ],
	'+cswikiquote' => [ 100 ],
	'+cswikisource' => [ 100 ],
	'+dawikisource' => [ 102 ],
	'+dewikiversity' => [ 106 ], // T93071
	'+enwikibooks' => [ 102, 110 ],
	'+enwikisource' => [ 102, 114 ], // T52007
	'+eswiki' => [ 104 ], // T41866
	'+etwikisource' => [ 106 ],
	'+euwiki' => [ 104 ], // T191396
	'+fawikibooks' => [ 102, 110 ], // T76663
	'+fawikisource' => [ 102 ],
	'+frrwiki' => [ 106 ], // T40023
	'+frwikisource' => [ 102 ],
	'+frwikiversity' => [ 104 ], // T125948
	'+frwiktionary' => [
		106, // T97228
		100, 110, 114, 116, 118, // T270821
	],
	'+hewikisource' => [ 100, 106, 108, 110 ], // T98709
	'+hrwiki' => [ 102 ], // T42732
	'+hrwikisource' => [ 100 ],
	'+huwikisource' => [ 100 ],
	'+hywikisource' => [ 100 ],
	'+idwikibooks' => [ 100, 102 ], // T4282
	'+idwikisource' => [ 100 ],
	'+itwikisource' => [ 102 ],
	'+itwikivoyage' => [ 100 ],
	'+kowikisource' => [ 100, 114 ], // T183836 for 114
	'+lawikisource' => [ 102 ],
	'+ltwiki' => [ 104 ], // T144118
	'+mediawikiwiki' => [ 100, 102, 104, 106 ], // Manuals, extensions, Api & skin - T86391
	'+metawiki' => [ NS_HELP ], // T45687
	'+mlwikisource' => [ 100 ],
	'+nlwikisource' => [ 102 ],
	'+nowikisource' => [ 102 ],
	'+nowikibooks' => [ 102 ], // T274265
	'+plwikisource' => [ 104, 124 ], // T154711
	'+ptwikisource' => [ 102 ],
	'+rowikisource' => [ 102 ], // Follow-up for T31190
	'+srwikibooks' => [ 102, ], // T17282
	'+srwikisource' => [ 100 ],
	'+svwikisource' => [ 106 ],
	'+tewikisource' => [ 102 ],
	'+thwikibooks' => [ 104, 106 ], // T308376
	'+thwikisource' => [ 102, 114 ], // T275282
	'+trwikibooks' => [ 100, 110, ],
	'+trwikisource' => [ 100 ],
	'+ukwikibooks' => [ 102 ], // T310940
	'+ukwikisource' => [ 102, 116 ], // T52561, T53684
	'+vecwikisource' => [ 100 ],
	'+viwikibooks' => [ 104, 106 ],
	'+viwikisource' => [ 102 ],
	'+wikitech' => [ NS_HELP, 116 ], // Tools - T122865
	'+zhwikisource' => [ 102, 114 ], // T66127
	'+zhwikiversity' => [ 100, 102, 104, 106, 108 ], // T201675, T212919 (106, 108)
	'+dewikivoyage' => [ 104 ],
	'+commonswiki' => [ 6 ], // T167077
	'+testwikidatawiki' => [
		120, // Property (T321282)
		146, // So that Lexeme is indexed in the content index (Cirrus)
		640, // EntitySchema (T368010), should also be in the content index (Cirrus)
	],
	'+wikidatawiki' => [
		120, // Property (T321282)
		146, // So that Lexeme is indexed in the content index (Cirrus)
		640, // EntitySchema (T368010), should also be in the content index (Cirrus)
	],
],

// When adding entries to this array, consider running the DiscussionTools maintenance script to
// index existing comments with signatures in its permalink database. Use --current unless you want
// to index old revisions too, which would take forever on large wikis and isn't very beneficial.
// For example:
// extensions/DiscussionTools/maintenance/persistRevisionThreadItems.php --current --namespace 102
'wgExtraSignatureNamespaces' => [
	// Namespaces not listed in $wgContentNamespaces are not necessary here
	// for core, but reinforcing default config doesn't harm.
	'default' => [ NS_PROJECT, NS_HELP ],

	// Wikis primarily used for discussion and coordination.
	// They often have discussions, including their "village pump" equivalent, in the main namespace.
	'+incubatorwiki' => [ NS_MAIN ],
	'+labswiki' => [ NS_MAIN ],
	'+labtestwiki' => [ NS_MAIN ],
	'+mediawikiwiki' => [ NS_MAIN ],
	'+metawiki' => [ NS_MAIN ],
	'+outreachwiki' => [ NS_MAIN ],
	'+wikimania' => [ NS_MAIN ],
	'+wikimedia' => [ NS_MAIN ],
	'+private' => [ NS_MAIN ],

	'+dewiki' => [ 100 ], // T145619 - Portal
	'+dewikivoyage' => [ 102 ], // T119420
	'+fiwiki' => [ 106 ], // T224215
	'+frwiki' => [ 102 ], // T127688 - Projet
	'+hywiki' => [ 102 ], // T261550 - WikiProject
	'+itwiki' => [ 102 ],
	'+nowikimedia' => [ 100 ], // T181625 - Prosjekt
	'+plwiki' => [ NS_USER, 100, 102 ], // T133978; 100 -> Portal, 102 -> Wikiproject
	'+ruwiki' => [ 104, 106 ], // T125509 and T213049
	'+ruwikinews' => [ 102 ], // T132241 - Комментарии
	'+sewikimedia' => [ 100 ], // T175363 - Projekt
	'+trwiki' => [ 102 ], // T166522 - Vikiproje
	'+wikimaniawiki' => [ 128 ], // T221062
],

'wmgAllowRobotsControlInAllNamespaces' => [
	// If true, allows to use __NOINDEX__ and __INDEX__ everywhere,
	// even in content namespaces (see $wgContentNamespaces).
	'default' => false,
	'metawiki' => true, // T150245
],

'wmgExemptFromUserRobotsControlExtra' => [
	// When wmgAllowRobotsControlInAllNamespaces is false (the default),
	// __NOINDEX__ and __INDEX__ will be ignored for these namepaces,
	// as well as for namespaces in $wgContentNamespaces.
	'default' => [],
	'azwiki' => [ 118, 119 ], // draft and draft talk - T299332
	'bgwiki' => [ 118, 119 ], // draft and draft talk - T299224
	'bnwiki' => [ 118, 119 ], // draft and draft talk - T350133
	'ckbwiki' => [ 118, 119 ], // draft and draft talk - T332470
	'enwiki' => [ 118, 119 ], // draft and draft talk
	'enwikiquote' => [ 118 ], // draft - T355195
	'eswiki' => [ 1, 2, 3, 5, 7, 9, 11, 13, 15, 101, 103, 105, 829 ], // all the default index namespaces - T355033
	'fawiki' => [ 118, 119 ], // draft and draft talk - T299850
	'hewiki' => [ 118, 119 ], // draft and draft talk - T86329
	'huwiki' => [ 118, 119 ], // draft and draft talk - T333083
	'itwiki' => [ 2, 3, 118, 119 ], // user and user talk - T314165, draft and draft talk - T280289
	'kowiki' => [ 118, 119 ], // draft and draft talk - T92798
	'mlwiki' => [ 118, 119 ], // draft and draft talk - T362653
	'tawiki' => [ 118, 119 ], // draft and draft talk - T329248
	'thwiki' => [ 118, 119 ], // draft and draft talk - T252959
	'zhwiki' => [ 2, 3, 118, 119 ], // user and user talk - T288947, draft and draft talk - T91223
],

# wgMetaNamespace @{
'wgMetaNamespace' => [
	// Defaults
	'wikipedia' => 'Wikipedia',
	'wikibooks' => 'Wikibooks',
	'wikidata' => 'Wikidata',
	'wikifunctionswiki' => 'Wikifunctions',
	'wikimania' => 'Wikimania',
	'wikimedia' => 'Wikimedia', // chapters
	'wikinews' => 'Wikinews',
	'wikiquote' => 'Wikiquote',
	'wikisource' => 'Wikisource',
	'wikiversity' => 'Wikiversity',
	'wikivoyage' => 'Wikivoyage',
	'wiktionary' => 'Wiktionary',

	// Wikis (alphabetical by DB name)
	'abwiki' => 'Авикипедиа',
	'advisorywiki' => 'Project',
	'adywiki' => 'Википедие', // T125501
	'aewikimedia' => 'Wikimedia_UAE',
	'amwiki' => 'ውክፔዲያ',
	'altwiki' => 'Википедия',
	'anwiktionary' => 'Biquizionario', // T130599
	'angwiki' => 'Wikipǣdia', // T58634
	'angwikisource' => 'Wicifruma',
	'angwiktionary' => 'Wikiwordbōc', // T58634
	'anpwiki' => 'विकिपीडिया',
	'arbcom_cswiki' => 'Projekt', // T151731
	'arbcom_dewiki' => 'Project',
	'arbcom_enwiki' => 'Project',
	'arbcom_fiwiki' => 'Project',
	'arbcom_nlwiki' => 'Project',
	'arbcom_ruwiki' => 'Арбитраж', // T262812
	'arcwiki' => 'ܘܝܩܝܦܕܝܐ',
	'arwiki' => 'ويكيبيديا', // T6672
	'arwikibooks' => 'ويكي_الكتب',
	'arwikinews' => 'ويكي_الأخبار',
	'arwikiquote' => 'ويكي_الاقتباس',
	'arwikisource' => 'ويكي_مصدر',
	'arwikiversity' => 'ويكي_الجامعة',
	'arwiktionary' => 'ويكاموس',
	'arywiki' => 'ويكيپيديا',
	'arzwiki' => 'ويكيبيديا',
	'astwiktionary' => 'Wikcionariu', // T99315
	'aswiki' => 'ৱিকিপিডিয়া',
	'aswikiquote' => 'ৱিকিউদ্ধৃতি',
	'aswikisource' => 'ৱিকিউৎস', // T45129
	'atjwiki' => 'Wikipetcia', // T167714
	'auditcomwiki' => 'Project',
	'avwiki' => 'Википедия', // T155321
	'awawiki' => 'विकिपीडिया', // T251371
	'aywiki' => 'Wikipidiya',
	'azwiki' => 'Vikipediya',
	'azwikibooks' => 'Vikikitab', // T33068
	'azwikimedia' => 'Vikimedia',
	'azwikisource' => 'Vikimənbə',
	'azwikiquote' => 'Vikisitat',
	'azwiktionary' => 'Vikilüğət', // T128296
	'azbwiki' => 'ویکی‌پدیا', // T106305
	'bawiki' => 'Википедия', // T43167
	'bawikibooks' => 'Викидәреслек', // T173471
	'banwiki' => 'Wikipédia',
	'bat_smgwiki' => 'Vikipedėjė',
	'bclwiktionary' => 'Wiksyunaryo',
	'bclwikiquote' => 'Wikisambit',
	'bdwikimedia' => 'উইকিমিডিয়া_বাংলাদেশ',
	'be_x_oldwiki' => 'Вікіпэдыя',
	'bewiki' => 'Вікіпедыя',
	'bewikibooks' => 'Вікікнігі', // T212665
	'bewikiquote' => 'Вікіцытатнік', // T196230
	'bewikisource' => 'Вікікрыніцы',
	'bewiktionary' => 'Вікіслоўнік', // T175950
	'bgwiki' => 'Уикипедия',
	'bgwikibooks' => 'Уикикниги',
	'bgwikinews' => 'Уикиновини',
	'bgwikiquote' => 'Уикицитат',
	'bgwikisource' => 'Уикиизточник',
	'bgwiktionary' => 'Уикиречник',
	'bhwiki' => 'विकिपीडिया',
	'bjnwiki' => 'Wikipidia',
	'bjnwikiquote' => 'Wikipapadah', // T350235
	'bjnwiktionary' => 'Wikikamus',
	'blkwiki' => 'ဝီခီပီးဒီးယား',
	'blkwiktionary' => 'ဝိစ်သိဉ်နရီ',
	'bnwiki' => 'উইকিপিডিয়া',
	'bnwikibooks' => 'উইকিবই',
	'bnwikiquote' => 'উইকিউক্তি',
	'bnwikisource' => 'উইকিসংকলন',
	'bnwikivoyage' => 'উইকিভ্রমণ',
	'bnwiktionary' => 'উইকিঅভিধান',
	'boardgovcomwiki' => 'Wikipedia', // This was set up incorrectly.
	'boardwiki' => 'Project',
	'bpywiki' => 'উইকিপিডিয়া',
	'brwikiquote' => 'Wikiarroud',
	'brwikisource' => 'Wikimammenn',
	'brwiktionary' => 'Wikeriadur',
	'bswikibooks' => 'Wikiknjige',
	'bswikinews' => 'Wikivijesti', // pre-emptive
	'bswikiquote' => 'Wikicitati',
	'bswikisource' => 'Wikizvor',
	'bswiktionary' => 'Wikirječnik', // T159538
	'btmwiktionary' => 'Wikikamus',
	'bxrwiki' => 'Википеэди', // T43877
	'cawiki' => "Viquipèdia",
	'cawikibooks' => 'Viquillibres',
	'cawikinews' => 'Viquinotícies',
	'cawikiquote' => 'Viquidites',
	'cawikisource' => 'Viquitexts',
	'cawiktionary' => 'Viccionari',
	'cewiki' => 'Википеди', // T49574
	'chairwiki' => 'Project',
	'chapcomwiki' => 'Affcom', // T41482
	'checkuserwiki' => 'Project', // T30781
	'ckbwiki' => 'ویکیپیدیا',
	'ckbwiktionary' => 'ویکیفەرھەنگ',
	'collabwiki' => 'Project',
	'commonswiki' => 'Commons',
	'testcommonswiki' => 'Commons',
	'crhwiki' => 'Vikipediya',
	'csbwiki' => 'Wiki',
	'csbwiktionary' => 'Wikisłowôrz', // T354114
	'cswiki' => 'Wikipedie',
	'cswikibooks' => 'Wikiknihy',
	'cswikinews' => 'Wikizprávy',
	'cswikiquote' => 'Wikicitáty',
	'cswikiversity' => 'Wikiverzita',
	'cswikisource' => 'Wikizdroje',
	'cswikivoyage' => 'Wikicesty',
	'cswiktionary' => 'Wikislovník',
	'cuwiki' => 'Википєдїꙗ',
	'cvwiki' => 'Википеди',
	'cywiki' => 'Wicipedia',
	'cywikibooks' => 'Wicilyfrau',
	'cywikisource' => 'Wicidestun',
	'cywiktionary' => 'Wiciadur',
	'donatewiki' => 'Donate',
	'dsbwiki' => 'Wikipedija',
	'diqwiktionary' => 'Wikiqısebend',
	'dtywiki' => 'विकिपिडिया', // T161529
	'dvwiki' => 'ވިކިޕީޑިއާ', // T50075
	'dvwiktionary' => 'ވިކިރަދީފު', // T48846
	'elwiki' => 'Βικιπαίδεια',
	'elwikibooks' => 'Βικιβιβλία',
	'elwikinews' => 'Βικινέα',
	'elwikiquote' => 'Βικιφθέγματα',
	'elwikisource' => 'Βικιθήκη',
	'elwikiversity' => 'Βικιεπιστήμιο',
	'elwikivoyage' => 'Βικιταξίδια',
	'elwiktionary' => 'Βικιλεξικό',
	'eowiki' => 'Vikipedio',
	'eowikibooks' => 'Vikilibroj',
	'eowikinews' => 'Vikinovaĵoj',
	'eowikiquote' => 'Vikicitaro',
	'eowikisource' => 'Vikifontaro',
	'eowikivoyage' => 'Vikivojaĝo',
	'eowiktionary' => 'Vikivortaro',
	'eswikibooks' => 'Wikilibros',
	'eswikinews' => 'Wikinoticias',
	'eswikiversity' => 'Wikiversidad',
	'eswikivoyage' => 'Wikiviajes', // T44933
	'eswiktionary' => 'Wikcionario',
	'etwiki' => 'Vikipeedia',
	'etwikibooks' => 'Vikiõpikud',
	'etwikisource' => 'Vikitekstid',
	'etwikiquote' => 'Vikitsitaadid',
	'etwiktionary' => 'Vikisõnastik',
	'euwikisource' => 'Wikiteka', // T189465
	'execwiki' => 'Project',
	'extwiki' => 'Güiquipedia',
	'fawiki' => 'ویکی‌پدیا',
	'fawikibooks' => 'ویکی‌کتاب', // T60655
	'fawikinews' => 'ویکی‌خبر',
	'fawikiquote' => 'ویکی‌گفتاورد',
	'fawikisource' => 'ویکی‌نبشته',
	'fawikivoyage' => 'ویکی‌سفر', // T73382
	'fawiktionary' => 'ویکی‌واژه',
	'fdcwiki' => 'Project', // T106188
	'fiwikibooks' => 'Wikikirjasto',
	'fiwikinews' => 'Wikiuutiset',
	'fiwikiquote' => 'Wikisitaatit',
	'fiwikisource' => 'Wikiaineisto',
	'fiwikiversity' => 'Wikiopisto',
	'fiwikivoyage' => 'Wikimatkat', // T151570
	'fiwiktionary' => 'Wikisanakirja',
	'foundationwiki' => 'Wikimedia',
	'fowikisource' => 'Wikiheimild',
	'fonwiki' => 'Wikipedya', // T347939
	'frpwiki' => 'Vouiquipèdia',
	'frwiki' => 'Wikipédia',
	'frwikibooks' => 'Wikilivres',
	'frwikiversity' => 'Wikiversité',
	'frwiktionary' => 'Wiktionnaire',
	'furwiki' => 'Vichipedie',
	'fywiki' => 'Wikipedy',
	'fywiktionary' => 'Wikiwurdboek', // T202769
	'gawiki' => 'Vicipéid',
	'gagwiki' => 'Vikipediya',
	'gawikibooks' => 'Vicíleabhair',
	'gawikiquote' => 'Vicísliocht',
	'gawiktionary' => 'Vicífhoclóir',
	'gcrwiki' => 'Wikipédja',
	'gdwiki' => 'Uicipeid',
	'gewikimedia' => 'ვიკიმედია', // T236389
	'gnwiki' => 'Vikipetã',
	'gomwiki' => 'विकिपीडिया', // T96468
	'gomwiktionary' => 'विक्शनरी', // T249506
	'gorwiki' => 'Wikipedia', // T189109
	'grantswiki' => 'Project',
	'gorwiktionary' => 'Wikikamus',
	'gucwiki' => 'Wikipeetia',
	'gurwiki' => 'Wikipiidiya',
	'guwiki' => 'વિકિપીડિયા',
	'guwikiquote' => 'વિકિસૂક્તિ', // T121853
	'guwikisource' => 'વિકિસ્રોત',
	'guwiktionary' => 'વિકિકોશ', // T122407
	'guwwikiquote' => 'Wikihoyidọ',
	'guwwiktionary' => 'Wikiwezẹhomẹ',
	'guwwikinews' => 'Wikilinlin',
	'hewiki' => 'ויקיפדיה',
	'hewikibooks' => 'ויקיספר',
	'hewikinews' => 'ויקיחדשות',
	'hewikiquote' => 'ויקיציטוט',
	'hewikisource' => 'ויקיטקסט',
	'hewikivoyage' => 'ויקימסע',
	'hewiktionary' => 'ויקימילון',
	'hifwiktionary' => 'Sabdkosh', // T173643
	'hiwiki' => 'विकिपीडिया',
	'hiwikibooks' => 'विकिपुस्तक', // T254012
	'hiwikimedia' => 'विकिमीडिया', // T188366
	'hiwikisource' => 'विकिस्रोत',
	'hiwikiversity' => 'विकिविश्वविद्यालय',
	'hiwikivoyage' => 'विकियात्रा', // T173013
	'hiwikiquote' => 'विकिसूक्ति', // T126185
	'hiwiktionary' => 'विक्षनरी',
	'hrwiki' => 'Wikipedija',
	'hrwikibooks' => 'Wikiknjige',
	'hrwikiquote' => 'Wikicitat',
	'hrwikisource' => 'Wikizvor',
	'hrwiktionary' => 'Wječnik',
	'hsbwiki' => 'Wikipedija',
	'hsbwiktionary' => 'Wikisłownik', // T43328
	'htwiki' => 'Wikipedya',
	'htwikisource' => 'Wikisòrs',
	'huwiki' => 'Wikipédia',
	'huwikibooks' => 'Wikikönyvek',
	'huwikinews' => 'Wikihírek',
	'huwikiquote' => 'Wikidézet',
	'huwikisource' => 'Wikiforrás',
	'huwiktionary' => 'Wikiszótár',
	'hywiki' => 'Վիքիպեդիա',
	'hywwiki' => 'Ուիքիփետիա', // T212597
	'hywikibooks' => 'Վիքիգրքեր',
	'hywikiquote' => 'Վիքիքաղվածք',
	'hywikisource' => 'Վիքիդարան',
	'hywiktionary' => 'Վիքիբառարան',
	'iawiktionary' => 'Wiktionario',
	'idwikibooks' => 'Wikibuku',
	'idwikimedia' => 'Wikimedia_Indonesia',
	'idwikiquote' => 'Wikikutip', // T341177
	'idwikisource' => 'Wikisumber', // T341173
	'idwiktionary' => 'Wikikamus', // T341173
	'iegcomwiki' => 'Project',
	'igwikiquote' => 'Wikikwotu',
	'ilwikimedia' => 'ויקימדיה',
	'incubatorwiki' => 'Incubator',
	'inhwiki' => 'Википеди',
	'internalwiki' => 'Project',
	'iowiki' => 'Wikipedio',
	'iowiktionary' => 'Wikivortaro',
	'iswikibooks' => 'Wikibækur',
	'iswikiquote' => 'Wikivitnun',
	'iswikisource' => 'Wikiheimild',
	'iswiktionary' => 'Wikiorðabók',
	'itwikinews' => 'Wikinotizie',
	'itwikiversity' => 'Wikiversità',
	'itwiktionary' => 'Wikizionario',
	'iuwiki' => 'ᐅᐃᑭᐱᑎᐊ',
	'jamwiki' => 'Wikipidia',
	'jawikinews' => 'ウィキニュース',
	'jbowiki' => 'uikipedi\'as', // T118067
	'jvwiki' => 'Wikipédia', // T287437
	'jvwikisource' => 'Wikisumber',
	'jvwiktionary' => 'Wikisastra', // T287437
	'kawiki' => 'ვიკიპედია',
	'kawikibooks' => 'ვიკიწიგნები',
	'kawikiquote' => 'ვიკიციტატა',
	'kawikisource' => 'ვიკიწყარო', // T363243
	'kawiktionary' => 'ვიქსიკონი',
	'kbdwiki' => 'Уикипедиэ',
	'kbdwiktionary' => 'Википсалъалъэ',
	'kbpwiki' => 'Wikipediya', // T160868
	'kcgwiki' => 'Wukipedia',
	'kcgwiktionary' => 'Swánga̱lyiatwuki',
	'kkwiki' => 'Уикипедия',
	'kkwikibooks' => 'Уикикітап',
	'kkwikiquote' => 'Уикидәйек',
	'kkwiktionary' => 'Уикисөздік',
	'kmwiki' => 'វិគីភីឌា',
	'knwiki' => 'ವಿಕಿಪೀಡಿಯ',
	'knwikiquote' => 'ವಿಕಿಕೋಟ್', // T318318
	'knwikisource' => 'ವಿಕಿಸೋರ್ಸ್', // T110806
	'knwiktionary' => 'ವಿಕ್ಷನರಿ', // T318318
	'koiwiki' => 'Википедия',
	'kowiki' => '위키백과',
	'kowikinews' => '위키뉴스',
	'kowikibooks' => '위키책',
	'kowikiquote' => '위키인용집',
	'kowikisource' => '위키문헌',
	'kowiktionary' => '위키낱말사전',
	'kowikiversity' => '위키배움터', // T46899
	'krcwiki' => 'Википедия',
	'kswiki' => 'وِکیٖپیٖڈیا', // T289752
	'kswiktionary' => 'وِکیٖلۄغَتھ', // T289767
	'kuwiki' => 'Wîkîpediya',
	'kuwikibooks' => 'Wîkîpirtûk',
	'kuwikiquote' => 'Wîkîgotin',
	'kuwiktionary' => 'Wîkîferheng',
	'kvwiki' => 'Википедия',
	'kywiki' => 'Википедия', // T309866
	'wikitech' => 'Wikitech',
	'lawiki' => 'Vicipaedia',
	'lawikibooks' => 'Vicilibri',
	'lawikiquote' => 'Vicicitatio',
	'lawikisource' => 'Vicifons',
	'lawiktionary' => 'Victionarium',
	'ladwiki' => 'Vikipedya',
	'lbwiktionary' => 'Wiktionnaire',
	'lbewiki' => 'Википедия',
	'legalteamwiki' => 'Project',
	'lezwiki' => 'Википедия',
	'lfnwiki' => 'Vicipedia', // T183561
	'liwikibooks' => 'Wikibeuk',
	'liwikinews' => 'Wikinuujs',
	'liwikisource' => 'Wikibrónne',
	'lijwikisource' => 'Wikivivàgna',
	'lmowiktionary' => 'Wikizzionari',
	'loginwiki' => 'Project',
	'lowiki' => 'ວິກິພີເດຍ',
	'lrcwiki' => 'ڤیکیپئدیا', // T102026
	'ltgwiki' => 'Vikipedeja',
	'ltwiki' => 'Vikipedija',
	'ltwikisource' => 'Vikišaltiniai',
	'ltwiktionary' => 'Vikižodynas',
	'lvwiki' => 'Vikipēdija',
	'lvwiktionary' => 'Vikivārdnīca', // T170065
	'madwiki' => 'Wikipèḍia',
	'maiwiki' => 'विकिपिडिया', // T74346
	'mdfwiki' => 'Википедиесь',
	'mediawikiwiki' => 'Project',
	'metawiki' => 'Meta',
	'mhrwiki' => 'Википедий',
	'minwiktionary' => 'Wikikato',
	'mkwiki' => 'Википедија',
	'mkwiktionary' => 'Викиречник', // T140566
	'mkwikimedia' => 'Викимедија',
	'mlwiki' => 'വിക്കിപീഡിയ',
	'mlwikibooks' => 'വിക്കിപാഠശാല',
	'mlwikiquote' => 'വിക്കിചൊല്ലുകൾ',
	'mlwikisource' => 'വിക്കിഗ്രന്ഥശാല',
	'mlwiktionary' => 'വിക്കിനിഘണ്ടു',
	'mniwiki' => 'ꯋꯤꯀꯤꯄꯦꯗꯤꯌꯥ',
	'mniwiktionary' => 'ꯋꯤꯛꯁꯟꯅꯔꯤ',
	'mnwwiki' => 'ဝဳကဳပဳဒဳယာ',
	'mnwwiktionary' => 'ဝိက်ရှေန်နရဳ',
	'movementroleswiki' => 'Wikipedia', // This was set up incorrectly.
	'mrjwiki' => 'Википеди',
	'mrwiki' => 'विकिपीडिया',
	'mrwikibooks' => 'विकिबुक्स', // T73774
	'mrwikisource' => 'विकिस्रोत',
	'mrwiktionary' => 'विक्शनरी',
	'mswikibooks' => 'Wikibuku', // T368003
	'mswikisource' => 'Wikisumber', // T369047
	'mswiktionary' => 'Wikikamus', // T366549
	'mtwiki' => 'Wikipedija',
	'mtwiktionary' => 'Wikizzjunarju',
	'mwlwiki' => 'Biquipédia',
	'myvwiki' => 'Википедиясь',
	'mywiki' => 'ဝီကီပီးဒီးယား',
	'mywiktionary' => 'ဝစ်ရှင်နရီ',
	'mznwiki' => 'ویکی‌پدیا',
	'nahwiki' => 'Huiquipedia',
	'newiki' => 'विकिपिडिया', // T184865
	'newikibooks' => 'विकिपुस्तक', // T124881
	'newwiki' => 'विकिपिडिया',
	'newiktionary' => 'विक्सनरी', // T129768
	'ngwikimedia' => 'Project', // T240771
	'nlwiktionary' => 'WikiWoordenboek',
	'nlwikinews' => 'Wikinieuws', // T172211
	'nostalgiawiki' => 'Wikipedia',
	'nowikibooks' => 'Wikibøker',
	'nowikinews' => 'Wikinytt',
	'nowikisource' => 'Wikikilden',
	'nqowiki' => 'ߥߞߌߔߘߋߞߎ',
	'nvwiki' => 'Wikiibíídiiya',
	'nycwikimedia' => 'Wikimedia', // T31273
	'ocwiki' => 'Wikipèdia', // T9123
	'ocwikibooks' => 'Wikilibres',
	'ocwiktionary' => 'Wikiccionari',
	'officewiki' => 'OfficeWiki', // T66976
	'olowiki' => 'Wikipedii', // T146612
	'ombudsmenwiki' => 'Project',
	'orwiki' => 'ଉଇକିପିଡ଼ିଆ',
	'orwikisource' => 'ଉଇକିପାଠାଗାର', // T73875
	'orwiktionary' => 'ଉଇକିଅଭିଧାନ', // T94142
	'oswiki' => 'Википеди',
	'otrs_wikiwiki' => 'Project',
	'outreachwiki' => 'Wikimedia',
	'pa_uswikimedia' => 'Project',
	'pawiki' => 'ਵਿਕੀਪੀਡੀਆ',
	'pawikisource' => 'ਵਿਕੀਸਰੋਤ', // T149522
	'plwikiquote' => 'Wikicytaty',
	'plwikisource' => 'Wikiźródła',
	'plwikivoyage' => 'Wikipodróże',
	'plwiktionary' => 'Wikisłownik',
	'pnbwiki' => 'وکیپیڈیا',
	'pnbwiktionary' => 'وکشنری',
	'pntwiki' => 'Βικιπαίδεια',
	'pswiki' => 'ويکيپېډيا',
	'pswikibooks' => 'ويکيتابونه',
	'pswikivoyage' => 'ويکيسفر',
	'pswiktionary' => 'ويکيسيند',
	'ptwiki' => 'Wikipédia',
	'ptwikibooks' => 'Wikilivros',
	'ptwikinews' => 'Wikinotícias',
	'ptwikiversity' => 'Wikiversidade',
	'ptwiktionary' => 'Wikcionário',
	'qualitywiki' => 'Project',
	'quwiki' => 'Wikipidiya', // T355129
	'rmywiki' => 'Vikipidiya',
	'rowikibooks' => 'Wikimanuale',
	'rowikinews' => 'Wikiștiri',
	'rowikiquote' => 'Wikicitat',
	'rowiktionary' => 'Wikționar',
	'rswikimedia' => 'Викимедија',
	'ruewiki' => 'Вікіпедія',
	'ruwiki' => 'Википедия',
	'ruwikibooks' => 'Викиучебник',
	'ruwikimedia' => 'Викимедиа',
	'ruwikinews' => 'Викиновости',
	'ruwikiquote' => 'Викицитатник',
	'ruwikisource' => 'Викитека',
	'ruwikiversity' => 'Викиверситет',
	'ruwiktionary' => 'Викисловарь',
	'sawiki' => 'विकिपीडिया',
	'sawikibooks' => 'विकिपुस्तकानि', // T101634
	'sawikiquote' => 'विकिसूक्तिः',
	'sawikisource' => 'विकिस्रोतः', // T101634
	'sawiktionary' => 'विकिशब्दकोशः', // T101634
	'sahwiki' => 'Бикипиэдьийэ',
	'sahwikisource' => 'Бикитиэкэ',
	'satwiki' => 'ᱣᱤᱠᱤᱯᱤᱰᱤᱭᱟ', // T211294
	'scnwiktionary' => 'Wikizziunariu',
	'sdwiki' => 'وڪيپيڊيا', // T186943
	'searchcomwiki' => 'SearchCom',
	'shwiki' => 'Wikipedija', // T332614
	'shiwiki' => 'Wikipidya',
	'shnwiki' => 'ဝီႇၶီႇၽီးတီးယႃး',
	'shnwikibooks' => 'ဝီႇၶီႇပပ်ႉ',
	'shnwiktionary' => 'ဝိၵ်ႇသျိၼ်ႇၼရီႇ',
	'shnwikivoyage' => 'ဝီႇၶီႇဝွႆးဢဵတ်ႇꩡ်',
	'shywiktionary' => 'Wikasegzawal',
	'siwiki' => 'විකිපීඩියා',
	'siwikibooks' => 'විකිපොත්',
	'siwiktionary' => 'වික්ෂනරි',
	'skwiki' => 'Wikipédia',
	'skwikibooks' => 'Wikiknihy',
	'skwikiquote' => 'Wikicitáty',
	'skwikisource' => 'Wikizdroje',
	'skwiktionary' => 'Wikislovník',
	'skrwiki' => 'وکیپیڈیا',
	'skrwiktionary' => 'وکشنری',
	'slwiki' => 'Wikipedija',
	'slwikibooks' => 'Wikiknjige',
	'slwikiquote' => 'Wikinavedek',
	'slwikisource' => 'Wikivir',
	'slwiktionary' => 'Wikislovar',
	'slwikiversity' => 'Wikiverza',
	'sourceswiki' => 'Wikisource',
	'spcomwiki' => 'Spcom',
	'specieswiki' => 'Wikispecies',
	'sqwikinews' => 'Wikilajme',
	'srwiki' => 'Википедија',
	'srwikibooks' => 'Викикњиге',
	'srwikinews' => 'Викиновости', // T226315
	'srwikiquote' => 'Викицитат', // T111247
	'srwikisource' => 'Викизворник',
	'srwiktionary' => 'Викиречник',
	'stewardwiki' => 'Project',
	'strategywiki' => 'Strategic_Planning',
	'suwikisource' => 'Wikipabukon', // T344314
	'szlwiki' => 'Wikipedyjo',
	'szywiki' => 'Wikipitiya',
	'tawiki' => 'விக்கிப்பீடியா',
	'tawikibooks' => 'விக்கிநூல்கள்',
	'tawikinews' => 'விக்கிசெய்தி',
	'tawikisource' => 'விக்கிமூலம்',
	'tawiktionary' => 'விக்சனரி',
	'tawikiquote' => 'விக்கிமேற்கோள்',
	'taywiki' => 'Wikibitia',
	'tcywiki' => 'ವಿಕಿಪೀಡಿಯ', // T140898
	'tewiki' => 'వికీపీడియా',
	'tewikiquote' => 'వికీవ్యాఖ్య', // T89332
	'tewikisource' => 'వికీసోర్స్',
	'tewiktionary' => 'విక్షనరీ',
	'tgwiki' => 'Википедиа',
	'thwiki' => 'วิกิพีเดีย',
	'thwikibooks' => 'วิกิตำรา',
	'thwiktionary' => 'วิกิพจนานุกรม',
	'thwikinews' => 'วิกิข่าว',
	'thwikiquote' => 'วิกิคำคม',
	'thwikisource' => 'วิกิซอร์ซ',
	'tiwiki' => 'ዊኪፐድያ', // T263840
	'tiwiktionary' => 'ዊኪ-መዝገበ-ቃላት',
	'tkwiki' => 'Wikipediýa',
	'tkwiktionary' => 'Wikisözlük',
	'tlywiki' => 'Vikipediá', // T345316
	'transitionteamwiki' => 'Project',
	'trwiki' => 'Vikipedi',
	'trwikibooks' => 'Vikikitap',
	'trwikinews' => 'Vikihaber',
	'trwikiquote' => 'Vikisöz',
	'trwikisource' => 'Vikikaynak', // T44721
	'trwikivoyage' => 'Vikigezgin',
	'trwiktionary' => 'Vikisözlük',
	'trvwiki' => 'Wikipidiya',
	'ttwiki' => 'Википедия',
	'ttwiktionary' => 'Викисүзлек', // T211312
	'tyvwiki' => 'Википедия', // T51328
	'uawikimedia' => 'Вікімедіа',
	'udmwiki' => 'Википедия', // T49820
	'ukwiki' => 'Вікіпедія',
	'ukwikibooks' => 'Вікіпідручник',
	'ukwikinews' => 'Вікіновини', // T50843
	'ukwikiquote' => 'Вікіцитати',
	'ukwikisource' => 'Вікіджерела', // T50308
	'ukwikivoyage' => 'Вікімандри',
	'ukwiktionary' => 'Вікісловник',
	'urwiki' => 'ویکیپیڈیا',
	'urwikibooks' => 'ویکی_کتب', // T214290
	'urwikiquote' => 'ویکی_اقتباس', // T214290
	'urwiktionary' => 'ویکی_لغت', // T214290
	'usabilitywiki' => 'Wikipedia',
	'uzwiki' => 'Vikipediya',
	'uzwikibooks' => 'Vikikitob',
	'uzwikiquote' => 'Vikiiqtibos',
	'uzwiktionary' => 'Vikilug‘at',
	'vecwiktionary' => 'Wikisionario',
	'vepwiki' => 'Vikipedii',
	'vewikimedia' => 'Asociación',
	'votewiki' => 'Project',
	'vowiki' => 'Vükiped',
	'vowikibooks' => 'Vükibuks',
	'vowiktionary' => 'Vükivödabuk',
	'wawiktionary' => 'Wiccionaire', // T181782
	'wawikisource' => 'Wikisourd', // T269431
	'wikimaniateamwiki' => 'WikimaniaTeam',
	'xmfwiki' => 'ვიკიპედია',
	'yiwiki' => 'װיקיפּעדיע',
	'yiwikisource' => 'װיקיביבליאָטעק',
	'yiwiktionary' => 'װיקיװערטערבוך',
	'yuewiktionary' => '維基辭典',
	'zghwiki' => 'ⵡⵉⴽⵉⴱⵉⴷⵢⴰ', // T350241
	'zh_min_nanwikisource' => 'Wiki_Tô·-su-kóan',
	'zh_classicalwiki' => '維基大典',
],
# @} end of wgMetaNamespace

# wgMetaNamespaceTalk @{
'wgMetaNamespaceTalk' => [
	'adywiki' => 'Википедием_и_тегущыӀэн', // T125501
	'angwiki' => 'Wikipǣdiamōtung', // T58634
	'angwiktionary' => 'Wikiwordbōcmōtung', // T58634
	'altwiki' => 'Википедияти_шӱӱжери',
	'arbcom_cswiki' => 'Diskuse_k_projektu', // T151731
	'arbcom_ruwiki' => 'Обсуждение_арбитража', // T262812
	'aswiki' => 'ৱিকিপিডিয়া_বাৰ্তা',
	'aswikisource' => 'ৱিকিউৎস_বাৰ্তা', // T45129, T226027
	'aywiki' => 'Wikipidiyan_Aruskipäwi',
	'azbwiki' => 'ویکی‌پدیا_دانیشیغی', // T106305
	'azwikimedia' => 'Müzakirə',
	'bat_smgwiki' => 'Vikipedėjės_aptarėms',
	'bawikibooks' => 'Викидәреслек_буйынса_фекерләшеү', // T173471
	'bdwikimedia' => 'উইকিমিডিয়া_বাংলাদেশ_আলোচনা',
	'bewikisource' => 'Размовы_пра_Вікікрыніцы',
	'bewiktionary' => 'Размовы_пра_Вікіслоўнік', // T175950
	'blkwiktionary' => 'ဝိစ်သိဉ်နရီ_အိုင်ကိုမ်ဒေါ့ꩻရီ',
	'bswiki' => 'Razgovor_o_Wikipediji', // T115812 and T231654
	'bswiktionary' => 'Razgovor_s_Wikirječnikom',
	'btmwiktionary' => 'Pembicaraan_Wikikamus',
	'cuwiki' => 'Википєдїѩ_бєсѣда', // T123654
	'cswikivoyage' => 'Diskuse_k_Wikicestám',
	'elwikinews' => 'Βικινέα_συζήτηση',
	'elwikiversity' => 'Συζήτηση_Βικιεπιστημίου',
	'elwiktionary' => 'Συζήτηση_βικιλεξικού',
	'eowiki' => 'Vikipedia_diskuto',
	'eowikinews' => 'Vikinovaĵoj_diskuto',
	'eowikisource' => 'Vikifontaro_diskuto',
	'fatwiki' => 'Wikipedia_nkɔmbɔdzibea',
	'fiwikiversity' => 'Keskustelu_Wikiopistosta',
	'fiwikivoyage' => 'Keskustelu_Wikimatkoista', // T151570
	'gewikimedia' => 'განხილვა', // T236389
	'gagwiki' => 'Vikipediyanın_laflanması',
	'gorwiki' => 'Lo\'iya_Wikipedia', // T189109
	'grwikimedia' => 'Συζήτηση_Wikimedia', // T245911
	'guwikiquote' => 'વિકિસૂક્તિની_ચર્ચા', // T121853
	'guwikisource' => 'વિકિસ્રોત_ચર્ચા',
	'guwwikinews' => 'Wikilinlin_hodidọ',
	'hifwiktionary' => 'Sabdkosh_ke_baat', // T173643
	'hiwikibooks' => 'विकिपुस्तक_वार्ता', // T254012
	'hiwikimedia' => 'विकिमीडिया_वार्ता', // T188366
	'hiwikivoyage' => 'विकियात्रा_वार्ता', // T173013
	'hrwikisource' => 'Razgovor_o_Wikizvoru',
	'hsbwiktionary' => 'Diskusija_k_Wikisłownikej', // T43328
	'huwikinews' => 'Wikihírek-vita',
	'iswiktionary' => 'Wikiorðabókarspjall',
	'jamwiki' => 'Wikipidia_diskoshan',
	'jbowiki' => 'casnu_la_.uikipedi\'as.', // T118067
	'kbdwiki' => 'Уикипедиэм_и_тепсэлъыхьыгъуэ',
	'kbdwiktionary' => 'Википсалъалъэм_тепсэлъыхьын',
	'kbpwiki' => 'Wikipediya_ndɔnjɔɔlɩyɛ', // T160868
	'kcgwiktionary' => 'A̱lyiat_Swánga̱lyiatwuki',
	'knwikisource' => 'ವಿಕಿಸೋರ್ಸ್_ಚರ್ಚೆ', // T110806
	'kmwiki' => 'ការពិភាក្សាអំពីវិគីភីឌា',
	'koiwiki' => 'Баитам_Википедия_йылiсь',
	'kowikinews' => '위키뉴스토론',
	'kowikiquote' => '위키인용집토론',
	'kvwiki' => 'Википедия_донъялӧм',
	'kywiki' => 'Википедияны_талкуулоо', // T309866
	'lawikisource' => 'Disputatio_Vicifontis',
	'lezwiki' => 'Википедия_веревирд_авун',
	'lfnwiki' => 'Vicipedia_Discute', // T183561
	'lowiki' => 'ສົນທະນາກ່ຽວກັບວິກິພີເດຍ',
	'ltgwiki' => 'Vikipedejis_sprīža',
	'ltwiki' => 'Vikipedijos_aptarimas',
	'ltwiktionary' => 'Vikižodyno_aptarimas',
	'lvwiki' => 'Vikipēdijas_diskusija',
	'lvwiktionary' => 'Vikivārdnīcas_diskusija', // T170065
	'mrjwiki' => 'Википедим_кӓнгӓшӹмӓш',
	'mrwikibooks'	=> 'विकिबुक्स_चर्चा', // T73774
	'mznwiki' => 'ویکی‌پدیا_گپ', // T32752 T85383
	'newiki' => 'विकिपिडिया_वार्ता', // T184865
	'newwiki' => 'विकिपिडिया_खँलाबँला',
	'ngwikimedia' => 'Project_talk', // T240771
	'noboard_chapterswikimedia' => 'Wikimedia-diskusjon',
	'nsowiki' => 'Dipolelo_tša_Wikipedia',
	'olowiki' => 'Wikipedien_paginat', // T146612
	'orwikisource'  => 'ଉଇକିପାଠାଗାର_ଆଲୋଚନା', // T73875
	'pnbwiktionary' => 'گل_ات',
	'pntwiki' => 'Βικιπαίδεια_καλάτσεμαν',
	'ptwikimedia' => 'Discussão_Wikimedia',
	'ruewiki' => 'Діскузія_ку_Вікіпедії',
	'ruwikibooks' => 'Обсуждение_Викиучебника',
	'ruwikimedia' => 'Обсуждение_Викимедиа',
	'ruwikiversity' => 'Обсуждение_Викиверситета',
	'sawikiquote' => 'विकिसूक्तिसम्भाषणम्', // T101634
	'satwiki' => 'ᱣᱤᱠᱤᱯᱤᱰᱤᱭᱟ_ᱨᱚᱲ', // T211294
	'sahwiki' => 'Бикипиэдьийэ_ырытыыта',
	'sahwikisource' => 'Бикитиэкэ_Ырытыы',
	'sdwiki' => 'وڪيپيڊيا_بحث', // T186943
	'shwiki' => 'Razgovor_o_Wikipediji', // T332614
	'shnwiktionary' => 'လွင်ႈဢုပ်ႇဢူဝ်း_ဝိၵ်ႇသျိၼ်ႇၼရီႇ',
	'siwiki' => 'විකිපීඩියා_සාකච්ඡාව',
	'siwikibooks' => 'විකිපොත්_සාකච්ඡාව',
	'siwiktionary' => 'වික්ෂනරි_සාකච්ඡාව',
	'skwikibooks' => 'Diskusia_k_Wikiknihám',
	'skwikisource' => 'Diskusia_k_Wikizdrojom',
	'slwikiversity' => 'Pogovor_o_Wikiverzi',
	'srwiki' => 'Разговор_о_Википедији',
	'srwikibooks' => 'Разговор_о_викикњигама',
	'srwikinews' => 'Разговор_о_Викиновостима', // T226315
	'srwikiquote' => 'Разговор_о_Викицитату', // T111247
	'srwikisource' => 'Разговор_о_Викизворнику',
	'srwiktionary' => 'Разговор_о_викиречнику',
	'svwikiversity' => 'Wikiversitydiskussion',
	'tawiki' => 'விக்கிப்பீடியா_பேச்சு',
	'tawikinews' => 'விக்கிசெய்தி_பேச்சு',
	'tawikisource' => 'விக்கிமூலம்_பேச்சு',
	'tawiktionary' => 'விக்சனரி_பேச்சு',
	'tewikiquote' => 'వికీవ్యాఖ్య_చర్చ', // T89332
	'thwikibooks' => 'คุยเรื่องวิกิตำรา', // T42717
	'thwikiquote' => 'คุยเรื่องวิกิคำคม',
	'tiwiki' => 'ምይይጥ_ዊኪፐድያ', // T263840
	'tiwiktionary' => 'ምይይጥ_ዊኪ-መዝገበ-ቃላት', // T263840
	'thwiktionary' => 'คุยเรื่องวิกิพจนานุกรม', // T42717
	'tlwikiquote' => 'Wikikawikaan',
	'tyvwiki' => 'Википедия_дугайында_сүмелел', // T51328
	'uawikimedia' => 'Обговорення_Вікімедіа',
	'ukwiktionary' => 'Обговорення_Вікісловника',
	'ukwikinews' => 'Обговорення_Вікіновин', // T50843
	'vepwiki' => 'Paginad_Vikipedii',
	'vewikimedia' => 'Asociación_discusión',
	'wawiktionary' => 'Wiccionaire_copene', // T181782
	'wikifunctionswiki' => 'Wikifunctions_talk',
	'xmfwiki' => 'ვიკიპედია_სხუნუა',
],
# @} end of wgMetaNamespaceTalk

];
