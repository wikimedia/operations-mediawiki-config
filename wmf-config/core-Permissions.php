<?php

// Inline comments are often used for noting the task(s) associated with specific configuration
// and requiring comments to be on their own line would reduce readability for this file
// phpcs:disable MediaWiki.WhiteSpace.SpaceBeforeSingleLineComment.NewLineComment

return [

# groupOverrides @{
'groupOverrides' => [
	// Note: don't change the default setting here, because it won't take
	// effect for the wikis which have overrides below. Instead change the
	// default in groupOverrides2
	// IMPORTANT: don't forget to use a '+' sign in front of the wiki name
	// when needed, such as for wikis that are tagged (private/fishbowl/closed...)
	'default' => [],

	'commonsuploads' => [
		'user' => [
			'upload' => false,
			'reupload' => false,
			'reupload-own' => false,
			'reupload-shared' => false,
		],
		'autoconfirmed' => [ // Not set in core
			'upload' => false,
			'reupload' => false,
			'reupload-own' => false,
			'reupload-shared' => false,
		],
		'sysop' => [
			// Implicit, in core defaults: 'upload' => true,
			'reupload' => true,
			'reupload-own' => true,
			'reupload-shared' => true,
		],
	],

	// Read-only (except stewards)
	'closed' => [
		'*' => [
			'edit' => false,
			'createaccount' => false,
			'autocreateaccount' => true,
		],
		'user' => [
			'edit' => false,
			'move' => false,
			'move-rootuserpages' => false,
			'move-subpages' => false,
			'upload' => false,
		],
		'autoconfirmed' => [
			'upload' => false,
			'move' => false,
		],
		'sysop' => [
			'block' => false,
			'delete' => false,
			'undelete' => false,
			'import' => false,
			'move' => false,
			'move-subpages' => false,
			'move-rootuserpages' => false,
			'patrol' => false,
			'protect' => false,
			'editprotected' => false,
			'rollback' => false,
			'trackback' => false,
			'upload' => false,
			'movefile' => false,
		],
		'steward' => [
			'edit' => true,
			'move' => true,
			'delete' => true,
			'upload' => true,
		],
	],

	// Account creation required
	'nlwikimedia' => [
		'*' => [ 'edit' => false, ]
	],
	'fiwikimedia' => [
		'*' => [ 'edit' => false, ]
	],
	'trwikimedia' => [
		'*' => [ 'edit' => false, ]
	],
	'sewikimedia' => [
		'*' => [ 'edit' => false, ]
	],

	'+apiportalwiki' => [
		'*' => [ // T259657
			'edit-docs' => false,
			'move' => false,
			'move-subpages' => false,
			'createpage' => false,
			'edit' => false,
			'createtalk' => false,
		],
		'user' => [
			'edit' => true, // limited by $wgNamespaceProtection
		],
		'docseditor' => [
			'edit-docs' => true,
			'move' => true,
			'move-subpages' => true,
			'createpage' => true,
			'createtalk' => true,
		],
		'sysop' => [
			'edit-docs' => true,
			'move' => true,
			'move-subpages' => true,
			'createpage' => true,
		],
	],
	'+arbcom_ruwiki' => [
		'arbcom' => [ // T274844
			'block' => true,
			'createaccount' => true,
			'delete' => true,
			'move-categorypages' => true,
			'move-subpages' => true,
			'move-rootuserpages' => true,
			'suppressredirect' => true,
			'tboverride' => true,
			'skipcaptcha' => true,
			'rollback' => true,
		],
	],
	'+arwiki' => [
		'*' => [ 'patrolmarks' => true ], // T257106
		'autoconfirmed' => [ 'patrol' => true ],
		'editor' => [
			'movefile' => true, // T131249
			'upload' => true, // T142450
			'reupload' => true,
			'reupload-own' => true,
			'reupload-shared' => true,
			'autopatrol' => true, // T167071
			'editautoreviewprotected' => true,
			'editeditorprotected' => true, // T321111
			'enrollasmentor' => true, // T310905
		],
		'rollbacker' => [ 'rollback' => true ],

		// Uploads are restricted to a uploader group - T142450
		'user' => [
			'upload' => false,
			'reupload' => false,
			'reupload-own' => false,
			'reupload-shared' => false,
		],
		'autoconfirmed' => [
			'upload' => false,
			'reupload' => false,
			'reupload-own' => false,
			'reupload-shared' => false,
		],
		'uploader' => [
			'upload' => true,
			'reupload' => true,
			'reupload-own' => true,
			'reupload-shared' => true,
		],
		'reviewer' => [
			'upload' => true,
			'reupload' => true,
			'reupload-own' => true,
			'reupload-shared' => true,
			'autopatrol' => true, // T167071
			'editautoreviewprotected' => true,
			'editeditorprotected' => true, // T321111
		],
		'autoreview' => [
			'autopatrol' => true, // T167071
			'editautoreviewprotected' => true,
		],
		'bot' => [
			'editautoreviewprotected' => true,
			'editeditorprotected' => true, // T321111
		],
		'sysop' => [
			'editautoreviewprotected' => true,
			'editeditorprotected' => true, // T321111
		],
		'extendedmover' => [ // T326434
			'move' => true,
			'move-subpages' => true,
			'delete-redirect' => true,
			'suppressredirect' => true,
			'tboverride' => true,
		],
	],
	'+arwikibooks' => [
		'rollbacker' => [ 'rollback' => true ], // T185720
	],
	'+arwikiquote' => [
		'rollbacker' => [ 'rollback' => true ], // T189732
	],
	'+arwikisource' => [
		'user' => [ 'pagequality-validate' => false ], // T354503
		'autopatrolled' => [ 'autopatrol' => true ],
		'patroller' => [
			'autopatrol' => true,
			'pagequality-validate' => true, // T354503
			'patrol' => true
		],
		'rollbacker' => [ 'rollback' => true ],
		'sysop' => [ 'pagequality-validate' => true ] // T354503
	],
	'+arwikiversity' => [
		'rollbacker' => [ 'rollback' => true ], // T188633
	],
	'+arwiktionary' => [
		'autopatrolled' => [ 'autopatrol' => true ],
		'rollbacker' => [ 'rollback' => true ],
	],
	'+arwikinews' => [
		'rollbacker' => [ 'rollback' => true ], // T189206
	],
	'+arzwiki' => [
		'rollbacker' => [ 'rollback' => true ], // T258100
		'autopatrolled' => [ 'autopatrol' => true ], // T260761
		'patroller' => [
			'patrol' => true,
			'autopatrol' => true,
		], // T262218
	],
	'+azwiki' => [
		'user' => [ 'move-categorypages' => false ], // T303752
		'autoconfirmed' => [ 'move' => false ], // T303752
		'autopatrolled' => [ 'autopatrol' => true ], // T196488
		'patroller' => [
			'patrol' => true,
			'autopatrol' => true,
		], // T196488, T244338
		'rollbacker' => [ 'rollback' => true ], // T215200
		'bot' => [ 'extendedconfirmed' => true ], // T281860
		'extendedconfirmed' => [ 'extendedconfirmed' => true ], // T281860
		'sysop' => [ 'extendedconfirmed' => true ], // T281860
		'pagemover' => [
			'move' => true,
			'move-categorypages' => true
		], // T303752
		'filemover' => [ 'movefile' => true ], // T304968
	],
	'+azwikibooks' => [
		'autopatrolled' => [ 'autopatrol' => true ], // T231493
	],
	'+azwikiquote' => [
		'autopatrolled' => [ 'autopatrol' => true ], // T300435
	],
	'+azwikisource' => [
		'autopatrolled' => [ 'autopatrol' => true ], // T229371
	],
	'+azwiktionary' => [
		'autopatrolled' => [ 'autopatrol' => true ], // T227208
	],
	'+azbwiki' => [
		'autopatrolled' => [ 'autopatrol' => true, ], // T109755
		'interface-editor' => [ // T109755
			'editinterface' => true, // T109755
			'editsitejson' => true,
			'tboverride' => true, // T109755
		],
		'patroller' => [ 'patrol' => true, ], // T109755
		'rollbacker' => [ 'rollback' => true, ], // T109755
		'transwiki' => [ 'importupload' => true, ], // T109755
	],
	'+betawikiversity' => [
		'test-sysop' => [ // T240438
			'block' => true,
			'blockemail' => true,
			'delete' => true,
			'undelete' => true,
			'deletedhistory' => true,
			'autopatrol' => true,
			'suppressredirect' => true,
			'rollback' => true,
		],
	],
	'+bnwiki' => [
		'patroller' => [ 'patrol' => true ], // T308945
		'autopatrolled' => [ 'autopatrol' => true ], // T30717
		'rollbacker' => [ 'rollback' => true ], // T30717
		'reviewer' => [
			'patrol' => true, // T30717
			'suppressredirect' => true, // T265169
		],
		'flood' => [ 'bot' => true ], // T30717
		'filemover' => [
			'movefile' => true, // T58103
			'suppressredirect' => true, // T233137
		],
	],
	'+bnwikibooks' => [
		'autopatrolled' => [ 'autopatrol' => true ], // T296640
		'patroller' => [
			'patrol' => true,
			'rollback' => true,
			'suppressredirect' => true,
		], // T296640
	],
	'+bnwikiquote' => [
		'autopatrolled' => [
			'autopatrol' => true, // T335829
		],
		'patroller' => [
			'patrol' => true, // T335829
			'rollback' => true, // T335829
			'suppressredirect' => true, // T335829
		],
	],
	'+bnwikisource' => [
		'autopatrolled' => [ 'autopatrol' => true ], // T199475
		'flood' => [ 'bot' => true ], // T129087
		'filemover' => [ 'movefile' => true ], // T129087
	],
	'+bnwikivoyage' => [
		'autopatrolled' => [
			'autopatrol' => true, // T296637
		],
		'patroller' => [
			'patrol' => true, // T296637
			'rollback' => true, // T296637
			'suppressredirect' => true, // T296637
		],
	],
	'+bnwiktionary' => [
		'autopatrolled' => [
			'autopatrol' => true, // T298187
		],
		'patroller' => [
			'patrol' => true, // T298187
			'rollback' => true, // T298187
			'suppressredirect' => true, // T298187
		],
	],
	'+bgwiki' => [
		'autopatrolled' => [ 'autopatrol' => true, ],
		'patroller' => [
			'patrol' => true,
			'autopatrol' => true,
			'rollback' => true,
		],
		'sysop' => [ 'autopatrol' => false, 'extendedconfirmed' => true ],
		'extendedconfirmed' => [ 'extendedconfirmed' => true ], // T269709
	],
	'+brwikimedia' => [ // T65345
		'autopatrolled' => [ 'autopatrol' => true, ],
	],
	'+bswiki' => [
		'rollbacker' => [ 'rollback' => true ],
		'flood' => [
			'bot' => true,
			'autopatrol' => false,
		], // T52425, T158662
		'sysop' => [
			'autopatrol' => false,
			'patrol' => false,
		], // T158662
	],
	'+cawiki' => [
		'autoconfirmed' => [ 'patrol' => true ],
		'autopatrolled' => [ 'autopatrol' => true ],
		'rollbacker' => [ 'rollback' => true ],
	],
	'+cawikinews' => [
		'flood' => [ 'bot' => true ], // T98576
	],
	'+cewiki' => [
		'rollbacker' => [ 'rollback' => true ], // T128205
		'suppressredirect' => [ // T128205
			'suppressredirect' => true,
			'move-categorypages' => true,
		],

		// Uploads are restricted to a uploader group - T129005
		'user' => [
			'upload' => false,
			'reupload' => false,
			'reupload-own' => false,
			'reupload-shared' => false,
		],
		'autoconfirmed' => [
			'upload' => false,
			'reupload' => false,
			'reupload-own' => false,
			'reupload-shared' => false,
		],
		'uploader' => [
			'upload' => true,
			'reupload' => true,
			'reupload-own' => true,
			'reupload-shared' => true,
		],
	],
	'+checkuserwiki' => [ // T30781
		'autoconfirmed' => [
			'autoconfirmed' => false,
			'editsemiprotected' => false,
			'reupload' => false,
			'upload' => false,
			'move' => false,
			'collectionsaveasuserpage' => false,
			'collectionsaveascommunitypage' => false,
			'skipcaptcha' => false,
		],
		'accountcreator' => [ 'noratelimit' => false, ],
		'bureaucrat' => [ 'createaccount' => true, ],
		'sysop' => [ 'createaccount' => false, ],
	],
	'+ckbwiki' => [
		'*' => [ 'createpage' => false ], // T25592
		'autopatrolled' => [
			'autopatrol' => true,
			'editautopatrolprotected' => true,
		], // T53328
		'rollbacker' => [ 'rollback' => true ], // T53312
		'autoconfirmed' => [ 'patrolmarks' => true ], // T56118
		'sysop' => [ 'importupload' => true ], // T54633
		'uploader' => [
			'upload' => true,
			'reupload' => true,
			'reupload-own' => true, // T54725
			'movefile' => true,
		], // T53232
		'interface-editor' => [
			'editinterface' => true, // T54866
			'editsitejson' => true,
			'apihighlimits' => true, // T67348
			'noratelimit' => true, // T67348
		],
		'flood' => [ 'bot' => true ], // T53803
		'botadmin' => [
			'protect' => true,
			'editprotected' => true,
			'delete' => true,
			'bigdelete' => true,
			'undelete' => true,
			'block' => true,
			'ipblock-exempt' => true,
			'rollback' => true, // T67348
		], // T54578
		'suppressredirect' => [ 'suppressredirect' => true ], // T69278
		'patroller' => [ 'patrol' => true ], // T285221
	],
	'+cswiki' => [
		'accountcreator' => [ // T254927
			'tboverride-account' => true,
			'override-antispoof' => true,
		],
		'autopatrolled' => [
			'autopatrol' => true,
			'patrolmarks' => true, // T354004
		],
		'bot' => [
			'ipblock-exempt' => true, // T44720
			'extendedconfirmed' => true, // T316283
			'changetags' => true, // T330383
		],
		'arbcom' => [ // T63418
			'browsearchive' => true,
			'deletedhistory' => true,
			'deletedtext' => true,
			'abusefilter-log-detail' => true,
			'abusefilter-view-private' => true, // T174357
			'abusefilter-log-private' => true, // T174357
		],
		'extendedconfirmed' => [ 'extendedconfirmed' => true ], // T316283
		'rollbacker' => [ 'rollback' => true, ], // T126931
		'patroller' => [ // T126931
			'patrol' => true,
			'unwatchedpages' => true,
		],
		'sysop' => [
			'extendedconfirmed' => true, // T316283
			'changetags' => true, // T330383
		],
		'user' => [ 'changetags' => false ], // T330383
	],
	'+cswikinews' => [
		'autopatrolled' => [ 'autopatrol' => true, ],
	],
	'+cswikiquote' => [
		'autopatrolled' => [ 'autopatrol' => true, ],
	],
	'+cswikisource' => [
		'autopatrolled' => [ 'autopatrol' => true, ],
	],
	'+cswiktionary' => [
		'autopatrolled' => [ 'autopatrol' => true, ],
	],
	'+commonswiki' => [
		'user' => [
			'changetags' => false, // T134196
			'upload' => true, // exception for T14556
			'upload_by_url' => true, // T251474
			'move-rootuserpages' => false, // T236359
		],
		'rollbacker' => [ 'rollback' => true ],
		'patroller' => [ // T214003
			'autopatrol' => true,
			'patrol' => true,
			'abusefilter-log-detail' => true,
			'mass-upload' => true, // T226217
			'move-rootuserpages' => true, // T236359
			'editautopatrolprotected' => true, // T357298
		],
		'autopatrolled' => [ // T214003
			'autopatrol' => true,
			'mass-upload' => true, // T226217
			'move-rootuserpages' => true, // T236359
			'editautopatrolprotected' => true, // T357298
		],
		'filemover' => [
			'movefile' => true,
			'suppressredirect' => true, // T236348
		],
		'image-reviewer' => [
			'autopatrol' => true,
			'patrol' => true, // T183835
			'mass-upload' => true, // T226217
			'move-rootuserpages' => true, // T236359
			'editautopatrolprotected' => true, // T357298
		],
		'sysop' => [
			'changetags' => true, // T134196
			'templateeditor' => true, // T227420
			'editautopatrolprotected' => true, // T357298
		],
		'bot' => [
			'changetags' => true,
			'move-rootuserpages' => true, // T236359
		], // T134196, T145010
		'translationadmin' => [ 'noratelimit' => true, ], // T155162
		'templateeditor' => [
			'templateeditor' => true,
			'tboverride' => true,
		], // T227420
		'interface-admin' => [ 'editcontentmodel' => true, ], // T320752
	],
	'+dawiki' => [
		'patroller' => [
			'patrol' => true,
			'autopatrol' => true,
			'rollback' => true,
		],
		'autopatrolled' => [ 'autopatrol' => true, ],
	],
	'+dawiktionary' => [
		'autopatrolled' => [ 'autopatrol' => true, ], // T86062
	],
	'+dawikiquote' => [
		'autopatrolled' => [ 'autopatrol' => true, ], // T88591
	],
	'+dewiki' => [
		'*' => [ 'patrolmarks' => true, ], // T100682
		'bot' => [
			'editeditorprotected' => true, // T94368
			'ipblock-exempt' => true, // T332759
		],
		'editor' => [
			'rollback' => true, // per DaBPunkt's request, 2008-05-07
			'editeditorprotected' => true, // T94368
		],
		'noratelimit' => [ // T59819
			'autoreview' => true,
			'noratelimit' => true,
		],
		'sysop' => [ 'editeditorprotected' => true, ], // T94368
	],
	'+dewikiquote' => [
		'sysop' => [ 'importupload' => true, ],
	],
	'+dewikivoyage' => [
		'autopatrolled' => [ 'autopatrol' => true, ], // T67495
	],
	'+dewiktionary' => [
		'sysop' => [
			'importupload' => true,
			'editeditorprotected' => true,
			'editautoreviewprotected' => true,
		], // T216885
		'bot' => [
			'editeditorprotected' => true,
			'editautoreviewprotected' => true,
		], // T216885
		'editor' => [
			'editeditorprotected' => true,
			'editautoreviewprotected' => true,
		], // T216885
		'autoreview' => [ 'editautoreviewprotected' => true, ] // T216885
	],
	'+donatewiki' => [
		'user' => [
			'editinterface' => true,
			'editsitejson' => true,
		],
		'flood' => [ 'bot' => true ],
	],
	'+dtywiki' => [
		'autopatrolled' => [ 'autopatrol' => true ], // T176709
	],
	'+elwiki' => [
		'bot' => [
			'extendedconfirmed' => true, // T306241
		],
		'extendedconfirmed' => [
			'extendedconfirmed' => true, // T306241
		],
		'rollbacker' => [ // T257745
			'rollback' => true,
			'suppressredirect' => true,
		],
		'sysop' => [
			'extendedconfirmed' => true, // T306241
		],
	],
	'+elwiktionary' => [
		'interface-editor' => [
			'editinterface' => true,
			'editsitejson' => true,
		],
		'autopatrolled' => [ 'autopatrol' => true ], // T30612
		'rollbacker' => [ // T255569
			'rollback' => true,
			'suppressredirect' => true,
		],
	],
	'+enwiki' => [
		'*' => [
			'createpage' => false, // See P2059
			'createpagemainns' => false,
		],
		'user' => [
			'collectionsaveasuserpage' => true, // T48944
			'changetags' => false, // T97013
			'createpagemainns' => false,
			'move-categorypages' => false, // T219261
		],
		// The group exists as a courtesy, see T334692
		'founder' => [ 'read' => true ],
		'rollbacker' => [ 'rollback' => true ],
		'abusefilter-helper' => [ 'spamblacklistlog' => true ], // T175684
		'accountcreator' => [
			'override-antispoof' => true,
			'tboverride-account' => true,
			// also in some exemption lists'
		],
		'autoreviewer' => [ 'autopatrol' => true ],
		'eventcoordinator' => [ 'noratelimit' => true ], // T193075
		'researcher' => [
			'browsearchive' => true,
			'deletedhistory' => true,
			'apihighlimits' => true,
			'deletedtext' => true, // T253420
		],
		'filemover' => [ 'movefile' => true ], // T29927
		'bot' => [
			'ipblock-exempt' => true, // T30914
			'changetags' => true, // T97013
			'extendedconfirmed' => true, // T126607
			'move-categorypages' => true, // T219261
		],
		'suppress' => [
			'browsearchive' => true,
			'deletedhistory' => true,
			'deletedtext' => true,
			'abusefilter-view-private' => true,
		], // T30465, T119446
		'checkuser' => [
			'browsearchive' => true,
			'deletedhistory' => true,
			'deletedtext' => true,
			'abusefilter-view-private' => true,
		], // T30465, T119446
		'bureaucrat' => [
			'move-subpages' => true,
			'suppressredirect' => true,
			'tboverride' => true,
		],
		'templateeditor' => [
			'templateeditor' => true,
			'tboverride' => true,
			'editcontentmodel' => true, // T253081
		], // T57432
		'sysop' => [
			'templateeditor' => true, // T57432
			'changetags' => true, // T97013
			'extendedconfirmed' => true, // T126607
			'autopatrol' => false, // T297058
		],
		'massmessage-sender' => [ 'massmessage' => true, ], // T60962
		'extendedconfirmed' => [ 'extendedconfirmed' => true ], // T126607
		'extendedmover' => [ // T133981
			'suppressredirect' => true,
			'move-subpages' => true,
			'move' => true,
			'tboverride' => true, // T209753
			'move-categorypages' => true, // T219261
			'delete-redirect' => true, // T278131
		],
		'patroller' => [ 'patrol' => true ], // T149019
		'autoconfirmed' => [ 'collectionsaveascommunitypage' => false ], // T283523
	],
	'+enwikibooks' => [
		'autoreview' => [ 'autopatrol' => true ], // T278300
		'editor' => [
			'autopatrol' => true, // T172561
			'suppressredirect' => true, // T268849
		],
		'flood' => [ 'bot' => true ],
		'sysop' => [ 'importupload' => true ], // T278683
		'uploader' => [
			'upload' => true,
			'reupload' => true,
		],
	],
	'+enwikinews' => [
		'flood' => [ 'bot' => true ],
	],
	'+enwikiquote' => [
		'rollbacker' => [ 'rollback' => true ], // T310950
	],
	'+enwikisource' => [
		'autoconfirmed' => [
			'patrol' => true, // T14355
			'upload_by_url' => true, // T294447
		],
		'autopatrolled' => [ 'autopatrol' => true ], // T20307
		'flood' => [ 'bot' => true ], // T38863
		'upload-shared' => [ // T285130
			'reupload-shared' => true,
		],
	],
	'+enwikiversity' => [
		'sysop' => [ 'nuke' => false ], // T113109
		'bureaucrat' => [ 'nuke' => true ], // T113109
		'curator' => [ // T113109
			'createaccount' => true,
			'delete' => true,
			'editprotected' => true,
			'import' => true,
			'ipblock-exempt' => true,
			'mergehistory' => true,
			'move-categorypages' => true,
			'movefile' => true,
			'move' => true,
			'move-subpages' => true,
			'protect' => true,
			'rollback' => true,
			'suppressredirect' => true,
			'upload' => true,
		],
	],
	'+enwikivoyage' => [
		'autopatrolled' => [ 'autopatrol' => true ],
		'patroller' => [
			'patrol' => true,
			'rollback' => true,
		],
		'templateeditor' => [ 'templateeditor' => true, ], // T198056
		'sysop' => [ 'templateeditor' => true ],
	],
	// T7033
	'+enwiktionary' => [
		'autopatrolled' => [
			'autopatrol' => true,
			'editautopatrolprotected' => true,
		], // T296580
		'extendedmover' => [
			'move' => true,
			'move-subpages' => true,
			'suppressredirect' => true,
			'tboverride' => true,
		], // T212662
		'flood' => [ 'bot' => true ],
		'patroller' => [ 'patrol' => true ],
		'rollbacker' => [ 'rollback' => true ],
		'sysop' => [ 'templateeditor' => true ], // T148007
		'templateeditor' => [ 'templateeditor' => true ], // T148007
	],
	'+eswiki' => [
		'autoconfirmed' => [ 'collectionsaveascommunitypage' => false ], // T163767
		'rollbacker' => [
			'rollback' => true,
			'abusefilter-log-detail' => true, // T70319
		],
		'*' => [ 'patrolmarks' => true ],
		'autopatrolled' => [ 'autopatrol' => true ],
		'patroller' => [
			'patrol' => true,
			'autopatrol' => true,
			'abusefilter-log-detail' => true, // T70319
		],
		'flood' => [ 'bot' => true ], // T50682
		'sysop' => [ 'templateeditor' => true ], // T330470
		'templateeditor' => [
			'templateeditor' => true,
			'tboverride' => true,
			'editcontentmodel' => true,
		], // T330470
		'botadmin' => [
			'editsemiprotected' => true, // T342484
			'abusefilter-bypass-blocked-external-domains' => true, // T342484
			'sboverride' => true, // T342484
			'writeapi' => true, // T342484
			'autopatrol' => true, // T342484
			'nominornewtalk' => true, // T342484
			'noratelimit' => true, // T342484
			'autoconfirmed' => true, // T342484
			'skipcaptcha' => true, // T342484
			'bot' => true, // T342484
			'suppressredirect' => true, // T342484
			'apihighlimits' => true, // T342484
			'block' => true, // T342484
			'blockemail' => true, // T342484
		], // T342484
	],
	'+eswikibooks' => [
		'*' => [
			'createpage' => false,
			'createtalk' => false,
		],
		'flood' => [ 'bot' => true ],
		'rollbacker' => [ 'rollback' => true ],
		'autopatrolled' => [ 'autopatrol' => true ], // T93371
		'patroller' => [
			'patrol' => true,
			'autopatrol' => true,
		], // T93371
	],
	'+eswikinews' => [
		'bot' => [ 'editprotected' => true ],
		'editprotected' => [
			'editprotected' => true,
			'editsemiprotected' => true,
		],
		'flood' => [ 'bot' => true ],
	],
	'+eswikiquote' => [ // T64911
		'rollbacker' => [ 'rollback' => true, ],
		'autopatrolled' => [ 'autopatrol' => true, ],
		'patroller' => [
			'patrol' => true,
			'autopatrol' => true,
		],
	],
	'+eswikisource' => [
		'autopatrolled' => [ 'autopatrol' => true ], // T69557
	],
	'+eswikivoyage' => [
		'rollbacker' => [
			'rollback' => true,
			'autopatrol' => true,
		], // T46285, T49325
		'patroller' => [
			'patrol' => true,
			'autopatrol' => true,
		], // T46285, T49325
		'autopatrolled' => [ 'autopatrol' => true, ], // T57665
	],
	'+eswiktionary' => [
		'patroller' => [ 'patrol' => true ],
		'rollbacker' => [ 'rollback' => true ],
		'autopatrolled' => [ 'autopatrol' => true ],
	],
	'+etwiki' => [
		'autopatrolled' => [
			'autopatrol' => true,
			'editautopatrolprotected' => true,
		], // T150852
	],
	'+euwiki' => [
		'sysop' => [ 'flow-create-board' => true ], // T190500
	],
	'+fawiki' => [
		'*' => [ 'createpage' => false ], // T29195
		'user' => [
			'move-categorypages' => false, // T67728
			'move-rootuserpages' => false, // T299847
		],
		'bot' => [
			'move-categorypages' => true, // T67728
			'extendedconfirmed' => true, // T241904
			'ipblock-exempt' => true, // T241904
		],
		'patroller' => [
			'patrol' => true,
			'move-categorypages' => true, // T67728
			'unwatchedpages' => true, // T300126
		],
		'rollbacker' => [ 'rollback' => true ], // T25233
		'autopatrolled' => [ // T144699
			'autopatrol' => true, // T31007
			'move-categorypages' => true, // T67728
			'patrolmarks' => true, // T303269
		],
		'image-reviewer' => [
			'movefile' => true, // T66532
			'delete' => true, // T73229
		],
		'botadmin' => [
			'block' => true, // T71411
			'delete' => true, // T71411
			'editprotected' => true, // T71411
			'ipblock-exempt' => true, // T71411
			'mergehistory' => true, // T71411
			'protect' => true, // T71411
			'undelete' => true, // T71411
			'extendedconfirmed' => true,
			'deleterevision' => true, // T277358
		],
		'templateeditor' => [
			'editprotected' => true, // T74146
			'tboverride' => true, // T74146
		],
		'eliminator' => [
			'block' => true, // T87558
			'delete' => true, // T87558
			'deleterevision' => true, // T87558
			'mergehistory' => true, // T87558
			'protect' => true, // T87558
			'suppressredirect'  => true, // T87558
			'deletedtext'       => true, // T135370
			'deletedhistory' => true, // T135725
			'extendedconfirmed' => true, // T140839
			'patrol' => true, // T176553
			'move-categorypages' => true, // T176553
			'rollback' => true, // T176553
			'autopatrol' => true, // T176553
			'upload' => true, // T176553
			'reupload' => true, // T176553
			'autoreview' => true, // T176553
			'autoreviewrestore' => true, // T176553
			'validate' => true, // T176553
			'review' => true, // T176553
			'flow-delete' => true, // T299223
			'unwatchedpages' => true, // T300126
		],
		'sysop' => [ 'extendedconfirmed' => true, ],
		'extendedconfirmed' => [ 'extendedconfirmed' => true, ], // T140839
		'extendedmover' => [
			'suppressredirect' => true, // T299038
			'move-subpages' => true, // T299038
			'move' => true, // T299038
			'tboverride' => true, // T299038
			'move-categorypages' => true, // T299038
			'delete-redirect' => true, // T299038
		],
		'autoconfirmed' => [ 'collectionsaveascommunitypage' => false ], // T303173
	],
	'+fawikibooks' => [
		'autopatrolled' => [ 'autopatrol' => true ], // T111024
		'patroller' => [ 'patrol' => true ], // T111024
		'rollbacker' => [ 'rollback' => true ], // T111024
	],
	'+fawikiquote' => [
		'autopatrolled' => [ 'autopatrol' => true ], // T156163
		'rollbacker' => [ 'rollback' => true ], // T156163
	],
	'+fawikinews' => [
		'rollbacker' => [ 'rollback' => true ],
		'patroller' => [ 'patrol' => true ],
	],
	'+fawikivoyage' => [
		'autopatrolled' => [ 'autopatrol' => true ], // T73760
		'flood' => [ 'bot' => true ], // T73760
		'patroller' => [ 'patrol' => true ], // T73760
		'rollbacker' => [ 'rollback' => true ], // T73760
		'sysop' => [ 'importupload' => true ], // T73681
	],
	'+fawiktionary' => [
		'autopatrolled' => [ 'autopatrol' => true ], // T85381
		'patroller' => [ 'patrol' => true ], // T85381
		'rollbacker' => [ 'rollback' => true ], // T85381
	],
	'+fawikisource' => [
		'autopatrolled' => [ 'autopatrol' => true ], // T187662
		'patroller' => [ 'patrol' => true ], // T187662
		'rollbacker' => [ 'rollback' => true ], // T161946
	],
	'+fiwiki' => [
		'patroller' => [ 'patrol' => true ],
		'rollbacker' => [ 'rollback' => true ],
		// T21561:
		'arbcom' => [
			'deletedhistory' => true,
			'deletedtext' => true,
			'undelete' => true,
		],
		'editor' => [
			'patrol' => true,
			'autopatrol' => true,
			'editautoreviewprotected' => true, // T347069
		], // T144817
		'reviewer' => [
			'patrol' => true, // T144817
			'autopatrol' => true, // T144817
			'stablesettings' => true, // T149987
			'editautoreviewprotected' => true, // T347069
		],
		'autoreview' => [
			'autopatrol' => true, // T144817
			'editautoreviewprotected' => true, // T347069
		],
		'bot' => [
			'editautoreviewprotected' => true, // T347069
		],
		'sysop' => [
			'editautoreviewprotected' => true, // T347069
		],
	],
	'+fiwiktionary' => [
		'rollbacker' => [ // T323063
			'rollback' => true,
		],
	],
	'+foundationwiki' => [
		'*' => [
			'edit' => false,
		],
		'user' => [
			'upload' => false,
			'reupload' => false,
			'reupload-own' => false,
			'reupload-shared' => false,
		],
		'autoconfirmed' => [
			'upload' => false,
			'reupload' => false,
			'reupload-own' => false,
			'reupload-shared' => false,
		],
		'translationadmin' => [
			'edit-legal' => true, // T346187
		],
		'editor' => [
			'edit-legal' => true,
			'edit' => true,
			'autoconfirmed' => true,
			'editsemiprotected' => true,
			'skipcaptcha' => true,
			'editinterface' => true,
			'editsitejson' => true,
			'upload' => true,
			'reupload' => true,
			'reupload-own' => true,
			'pagetranslation' => true, // T297396
			'translate-manage' => true, // T297396
		],
		'flood' => [ 'bot' => true ],
	],
	'+frwiki' => [
		'user' => [ 'changetags' => false ], // requested by hashar, T98629
		'bot' => [
			'changetags' => true,
			'editextendedsemiprotected' => true,
		], // T98629, T131109
		'autopatrolled' => [
			'patrol' => true, // T10904
			'autopatrol' => true, // T23078
			'movefile' => true, // T87145
			'changetags' => true, // T98629
			'editextendedsemiprotected' => true // T131109
		],
		'rollbacker' => [
			'rollback' => true, // T170780
			'unwatchedpages' => true // T285334
		],
		'checkuser' => [
			'browsearchive' => true,
			'deletedhistory' => true,
			'deletedtext' => true,
		], // T23044
		'sysop' => [ 'editextendedsemiprotected' => true ], // T131109
	],
	'+frwikibooks' => [
		'patroller' => [
			'patrol' => true,
			'autopatrol' => true,
		],
	],
	'+frwikinews' => [
		'flood' => [ 'bot' => true ],
		'trusteduser' => [
			'autoreview' => true,
			'unreviewedpages' => true,
		], // T90979
		'facilitator' => [ // T90979
			'autoreview' => true,
			'review' => true,
			'validate' => true,
			'unreviewedpages' => true,
			'import' => true,
		],
	],
	'frwikisource' => [
		'patroller' => [
			'patrol' => true,
			'autopatrol' => true,
			'rollback' => true,
		],
		'autopatrolled' => [ 'autopatrol' => true ],
	],
	'+frwikiversity' => [
		'patroller' => [
			'patrol' => true,
			'autopatrol' => true,
		],
	],
	'frwikivoyage' => [
		'patroller' => [
			'patrol' => true,
			'autopatrol' => true,
		],
	],
	'+frwiktionary' => [
		'patroller' => [
			'patrol' => true,
			'autopatrol' => true,
			'rollback' => true,
		],
		'autopatrolled' => [ 'autopatrol' => true ],
		'botadmin' => [
			'apihighlimits' => true,
			'autoconfirmed' => true,
			'autopatrol' => true,
			'bot' => true,
			'browsearchive' => true,
			'createaccount' => true,
			'delete' => true,
			'deleterevision' => true,
			'editinterface' => true,
			'editprotected' => true,
			'editsemiprotected' => true,
			'editsitejson' => true,
			'import' => true,
			'move' => true,
			'move-rootuserpages' => true,
			'movefile' => true,
			'nominornewtalk' => true,
			'noratelimit' => true,
			'patrol' => true,
			'protect' => true,
			'reupload' => true,
			'rollback' => true,
			'skipcaptcha' => true,
			'suppressredirect' => true,
			'undelete' => true,
			'writeapi' => true,
		],
	],
	'+gawiki' => [
		'rollbacker' => [ 'rollback' => true ],
	],
	'ganwiki' => [
		'transwiki' => [ 'suppressredirect' => true ], // T354850
	],
	'+guwiki' => [ // T119787
		'rollbacker' => [ 'rollback' => true ],
		'autopatrolled' => [ 'autopatrol' => true ],
	],
	'+hewiki' => [
		'user' => [
			'upload' => true,
			'move-rootuserpages' => false,
		],
		'sysop' => [ 'templateeditor' => true, ], // T102466
		'patroller' => [
			'patrol' => true,
			'autopatrol' => true,
			'editautopatrolprotected' => true,
			'unwatchedpages' => true,
			'rollback' => true,
			'move-rootuserpages' => true,
		],
		'autopatrolled' => [
			'autopatrol' => true,
			'unwatchedpages' => true,
			'editautopatrolprotected' => true,
		],
		'interface-admin' => [ // T200698
			'abusefilter-log' => true,
			'abusefilter-log-detail' => true,
			'abusefilter-modify' => true,
			'abusefilter-modify-restricted' => true,
			'abusefilter-revert' => true,
			'abusefilter-view' => true,
			'abusefilter-view-private' => true,
			'import' => true,
			'tboverride' => true,
			'templateeditor' => true,
		],
		'checkuser' => [
			'deletedhistory' => true,
			'deletedtext' => true,
			'abusefilter-log-detail' => true,
		],
		'templateeditor' => [
			'templateeditor' => true,
		], // T296769
	],
	'+hewikibooks' => [
		'patroller' => [
			'patrol' => true, // T29918
			'autopatrol' => true, // T62305
			'rollback' => true, // T62305
			'unwatchedpages' => true, // T73193
		],
		'autopatrolled' => [ 'autopatrol' => true ], // T29918
	],
	'+hewikisource' => [ // T275076
		'autoreview' => [ 'editautoreviewprotected' => true ],
		'bot' => [ 'editautoreviewprotected' => true ],
		'reviewer' => [ 'rollback' => true, 'editautoreviewprotected' => true ], // T274796
		'editor' => [ 'editautoreviewprotected' => true ],
		'sysop' => [ 'editautoreviewprotected' => true ],
	],
	'+hewikiquote' => [
		'autopatrolled' => [ 'autopatrol' => true ],
	],
	'+hewikivoyage' => [
		'autopatrolled' => [ 'autopatrol' => true ], // T52377
		'sysop' => [ 'importupload' => true ], // T61601
	],
	'+hewiktionary' => [
		'patroller' => [
			'patrol' => true,
			'autopatrol' => true,
			'unwatchedpages' => true,
			'rollback' => true,
		], // T75197
		'autopatrolled' => [
			'autopatrol' => true,
			'unwatchedpages' => true,
		], // T75197
	],
	'+hewikinews' => [ // T140544
		'autopatrolled' => [ 'autopatrol' => true ],
		'patroller' => [
			'patrol' => true,
			'autopatrol' => true,
			'rollback' => true,
			'unwatchedpages' => true,
		],
	],
	'+hiwiki' => [
		'rollbacker' => [
			'rollback' => true, // T56589
			'autoreviewrestore' => true, // T252986
		],
		'filemover' => [ 'movefile' => true ], // T56589
		'reviewer' => [
			'patrol' => true,
			'autopatrol' => true,
		],
		'autopatrolled' => [
			'autopatrol' => true,
			'move' => true,
		], // T164239
		'sysop' => [ 'validate' => true ],
		'templateeditor' => [
			'templateeditor' => true,
			'editprotected' => true,
		], // T120342
		'autoconfirmed' => [ 'move' => false ] // T164239
	],
	'+hiwikiversity' => [
		'autopatrolled' => [
			'autopatrol' => true,
			'patrolmarks' => true,
		], // T179251, T192427
	],
	'+hrwiki' => [
		'patroller' => [
			'patrol' => true,
			'autopatrol' => true,
			'rollback' => true,
		],
		'autopatrolled' => [ 'autopatrol' => true ],
		'user' => [
			'changetags' => false, // T270996
		],
		'bot' => [
			'changetags' => true, // T270996
		],
		'sysop' => [
			'changetags' => true, // T270996
		],
		'flood' => [ 'bot' => true ], // T276560
	],
	'+huwiki' => [
		'bot' => [ 'edittrustedprotected' => true ], // T194568
		'trusted' => [ 'edittrustedprotected' => true ], // T194568
		'editor' => [
			'noratelimit' => true,
			'edittrustedprotected' => true,
		], // T194568
		'sysop' => [
			'templateeditor' => true,
			'edittrustedprotected' => true,
		], // T74055, T194568
		'templateeditor' => [ 'templateeditor' => true ], // T74055
		'interface-editor' => [
			'editinterface' => true,
			'editsitejson' => true,
		], // T109408
	],
	'idwiki' => [
		'*' => [ 'createpage' => false ],
		'rollbacker' => [ 'rollback' => true ], // T35508
	],
	'+incubatorwiki' => [
		'bureaucrat' => [ 'upload' => true ],
		'sysop' => [ 'upload' => false ],
		'test-sysop' => [
			'delete' => true,
			'undelete' => true,
			'deletedhistory' => true,
			'block' => true,
			'blockemail' => true,
			'rollback' => true,
		],
		'translator' => [
			'editinterface' => true,
			'editsitejson' => true,
		],
	],
	// T7836
	// T13326
	'+itwiki' => [
		'autoconfirmed' => [ 'patrolmarks' => true ], // T268734
		'flood' => [ 'bot' => true, ],
		'rollbacker' => [
			'rollback' => true,
			'autopatrol' => true,
			'patrol' => true, // T261587
			'editautopatrolprotected' => true, // T308917
		],
		'autopatrolled' => [
			'autopatrol' => true,
			'patrol' => true, // T261587
			'editautopatrolprotected' => true, // T308917
		],
		'mover' => [ // T102770
			'movefile' => true,
			'move-subpages' => true,
			'suppressredirect' => true
		],
		'bot' => [
			'setmentor' => true, // T307005
			'editautopatrolprotected' => true, // T308917
			'changetags' => true, // T331051
		],
		'botadmin' => [ // T220915
			'apihighlimits' => true,
			'autoconfirmed' => true,
			'autopatrol' => true,
			'bot' => true,
			'browsearchive' => true,
			'delete' => true,
			'deleterevision' => true,
			'editprotected' => true,
			'editsemiprotected' => true,
			'ipblock-exempt' => true,
			'massmessage' => true,
			'nominornewtalk' => true,
			'noratelimit' => true,
			'oathauth-enable' => true,
			'override-antispoof' => true,
			'protect' => true,
			'skipcaptcha' => true,
			'suppressredirect' => true,
			'tboverride' => true,
			'writeapi' => true,
			'editautopatrolprotected' => true, // T308917
			'changetags' => true, // T331051
			'abusefilter-bypass-blocked-external-domains' => true, // T355694
		],
		'sysop' => [
			'editautopatrolprotected' => true, // T308917
			'changetags' => true, // T331051
		],
		'user' => [ 'changetags' => false ], // T331051
	],
	'+itwikisource' => [
		'flood' => [ 'bot' => true ], // T38600
	],
	'+itwikiversity' => [
		'autoconfirmed' => [ 'patrol' => true ],
		'autopatrolled' => [ 'autopatrol' => true ], // T114930
		'patroller' => [
			'autopatrol' => true,
			'rollback' => true,
			'suppressredirect' => true,
			'markbotedits' => true, // T309750
		], // T114930 and T207300
		'flood' => [ 'bot' => true, ], // T114930
	],
	'itwikibooks' => [
		'user' => [ 'patrol' => false ],
		'autoconfirmed' => [ 'patrol' => true ],
		'autopatrolled' => [ 'autopatrol' => true, ],
		'patroller' => [
			'autopatrol' => true,
			'rollback' => true,
		],
		'flood' => [ 'bot' => true, ], // T41569
	],
	'+itwikinews' => [
		'autoconfirmed' => [ 'patrol' => true ],
		'rollbacker' => [
			'rollback' => true,
			'autopatrol' => true,
		], // T142571
		'autopatrolled' => [ 'autopatrol' => true ], // T142571
		'closer' => [ 'protect' => true, 'editprotected' => true ], // T257927
	],
	'itwikiquote' => [
		'autoconfirmed' => [ 'patrol' => true ],
		'autopatrolled' => [ 'autopatrol' => true ], // T64200
		'sysop' => [ 'autopatrol' => true ],
	],
	'+itwikivoyage' => [
		'autopatrolled' => [ 'autopatrol' => true, ], // T45327, T45524
		'patroller' => [
			'patrol' => true,
			'autopatrol' => true,
			'rollback' => true,
		], // T47638
	],
	'+itwiktionary' => [
		'autoconfirmed' => [ 'patrol' => true ],
		'patroller' => [
			'patrol' => true,
			'autopatrol' => true,
			'rollback' => true,
		],
		'autopatrolled' => [ 'autopatrol' => true ],
		'import' => [ 'suppressredirect' => true, ],
		'transwiki' => [
			'suppressredirect' => true,
			'mergehistory' => true,
		],
		'flood' => [ 'bot' => true, ], // T41306
	],
	'+jawiki' => [
		'user' => [ 'changetags' => false ], // T344150
		'abusefilter' => [ 'changetags' => true ], // T344150
		'autoconfirmed' => [ 'patrol' => true ], // T15055
		'rollbacker' => [ 'rollback' => true ], // T258339
		'eliminator' => [
			'browsearchive' => true,
			'delete' => true,
			'deletedhistory' => true,
			'deletedtext' => true,
			'deletelogentry' => true, // T42521
			'deleterevision' => true,
			'ipblock-exempt' => true,
			'mergehistory' => true, // T131751
			'movefile' => true, // T72007
			'noratelimit' => true,
			'nuke' => true,
			'suppressredirect' => true,
			'undelete' => true,
			'unwatchedpages' => true,
		],
		'interface-admin' => [ // T222018
			'delete' => true,
			'editprotected' => true,
			'editsemiprotected' => true,
			'ipblock-exempt' => true,
			'suppressredirect' => true,
			'extendedconfirmed' => true, // T249820
			'changetags' => true, // T344150
		],
		'sysop' => [
			'extendedconfirmed' => true, // T249820
			'changetags' => true, // T344150
		],
		'bot' => [
			'extendedconfirmed' => true, // T249820
			'changetags' => true, // T344150
		],
		'extendedconfirmed' => [ 'extendedconfirmed' => true ], // T249820
	],
	'+jawiktionary' => [
		'autopatrolled' => [
			'autopatrol' => true,
			'patrol' => true,
		], // T63366
	],
	'+kawiki' => [
		'editor' => [ 'rollback' => true ],
	],
	'+kkwiki' => [
		'rollbacker' => [ 'rollback' => true ], // T130215
	],
	'+knwiki' => [
		'flood' => [ 'bot' => true ], // T322472
	],
	'+kowiki' => [
		'rollbacker' => [ 'rollback' => true ],
		'uploader' => [ // T85621
			'upload' => true,
			'reupload' => true,
			'reupload-own' => true,
			'reupload-shared' => true,
			'movefile' => true,
		],
		'user' => [ 'reupload-own' => false ], // T85621
		'autopatrolled' => [ 'autopatrol' => true ], // T130808
		'bot' => [ 'extendedconfirmed' => true, ], // T184675
		'extendedconfirmed' => [ 'extendedconfirmed' => true, ], // T184675
		'sysop' => [ 'extendedconfirmed' => true, ], // T184675
	],
	'+kswiki' => [
		'rollbacker' => [ 'rollback' => true ], // T286789
		'uploader' => [ // T305320
			'upload' => true,
			'reupload' => true,
			'reupload-own' => true,
			'reupload-shared' => true,
		],
	],
	'+ladwiki' => [
		'flood' => [ 'bot' => true ], // T131527
	],
	'pswiki' => [
		'interface-editor' => [ // T133472
			'editinterface' => true,
			'editsitejson' => true,
		],
	],
	'wikitech' => [ // contentadmin is handled in CommonSettings, not here
		'*' => [
			'edit' => false,
			'createaccount' => true,
		],
		'autopatrolled' => [ 'autopatrol' => true ],
		'flood' => [ 'bot' => true ],
		'oathauth' => [ 'oathauth-api-all' => true ], // T153487
		'oauthadmin' => [ 'autopatrol' => true ],
	],
	'+legalteamwiki' => [ // T63222
		'accountcreator' => [ 'noratelimit' => false ],
		'bureaucrat' => [ 'createaccount' => true ],
		'sysop' => [ 'createaccount' => false ],
	],
	'+lijwiki' => [
		'rollbacker' => [ // T256109
			'rollback' => true,
			'autopatrol' => true,
			'patrol' => true,
		],
		'mover' => [ // T256109
			'suppressredirect' => true,
			'movefile' => true,
			'move-subpages' => true,
		],
	],
	'+lmowiktionary' => [
		'extendedmover' => [ // T327340
			'move' => true,
			'move-subpages' => true,
			'suppressredirect' => true,
			'delete-redirect' => true,
			'tboverride' => true,
		]
	],
	'+lvwiki' => [
		'autopatrolled' => [
			'autopatrol' => true,
			'editautopatrolprotected' => true,
		], // T72441
		'patroller' => [
			'autopatrol' => true,
			'patrol' => true,
		], // T72441
		'flood' => [ 'bot' => true ], // T121238
	],
	'+maiwiki' => [
		'autopatrolled' => [ 'autopatrol' => true ], // T89346
		'patroller' => [ 'patrol' => true ], // T118934
		'rollbacker' => [ 'rollback' => true ], // T118934
	],
	'+mediawikiwiki' => [
		'*' => [
			'flow-hide' => false, // T245780
			'flow-edit-title' => false, // T328097
		],
		'autoconfirmed' => [
			'flow-hide' => true, // T245780
			'flow-edit-title' => true, // T328097
		],
		'user' => [ 'flow-create-board' => true, ],
		'autopatrolled' => [ 'autopatrol' => true, ],
		'uploader' => [ // T217523
			'upload' => true,
			'reupload' => true,
			'reupload-own' => true,
			'reupload-shared' => true,
		],
		'translationadmin' => [ 'autopatrol' => true, ],
	],
	'+metawiki' => [
		'autopatrolled' => [ 'autopatrol' => true, ], // T27160
		'bot' => [ 'changetags' => true ], // T283625
		'event-organizer' => [ // T356070
			'campaignevents-enable-registration' => true,
			'campaignevents-organize-events' => true,
			'campaignevents-email-participants' => true,
		],
		'centralnoticeadmin' => [ // gerrit:50196, T142123
			'autopatrol' => true,
			'banner-protect' => true,
			'centralnotice-admin' => true,
			'editinterface' => true,
			'protect' => true, // T209873
		],
		'flood' => [ 'bot' => true, ], // T17176
		'global-renamer' => [ // T142123, T71651
			'autopatrol' => true,
			'centralauth-rename' => true,
		],
		'massmessage-sender' => [ // T59611, T142123
			'autopatrol' => true,
			'massmessage' => true,
		],
		'oauthadmin' => [ 'autopatrol' => true, ], // T142123
		'patroller' => [ // T176079
			'autopatrol' => true,
			'patrol' => true,
			'rollback' => true,
		],
		'steward' => [
			'centralauth-rename' => true,
			'userrights-interwiki' => true,
			'globalgroupmembership' => true, // T207531
			'globalgrouppermissions' => true, // T207531
			'centralauth-lock' => true, // T208035
			'centralauth-suppress' => true, // T302675
			'centralauth-unmerge' => true, // T208035
			'globalblock' => true, // T208035
			'urlshortener-manage-url' => true, // T133109
			'urlshortener-view-log' => true, // T221073
			'oathauth-verify-user' => true, // T251447
			'oathauth-view-log' => true,
		],
		'sysop' => [ 'changetags' => true ], // T283625
		'translationadmin' => [
			'autopatrol' => true, // T142123
			'banner-protect' => true,
			'editcontentmodel' => true, // T311587
		],
		'uploader' => [ // T52287
			'upload' => true,
			'reupload' => true,
			'reupload-own' => true,
		],
		'user' => [
			'changetags' => false, // T283625
			'campaignevents-enable-registration' => false, // Temporary, T316227
			'campaignevents-organize-events' => false,
			'campaignevents-email-participants' => false,
		],
		'wmf-officeit' => [ // T106724, T142123, T254372
			'autopatrol' => true,
			'centralauth-lock' => true,
			'centralauth-rename' => true,
			'createaccount' => true,
			'noratelimit' => true,
			'tboverride' => true,
		],
		'wmf-supportsafety' => [ // T136046, T136864, T142123
			'autopatrol' => true,
			'centralauth-lock' => true,
			'centralauth-suppress' => true, // T302675
			'centralauth-rename' => true,
			'editcontentmodel' => true, // TODO: remove when a change for T85847 is deployed
			'globalblock' => true,
			'massmessage' => true,
			'userrights' => true,
			'userrights-interwiki' => true,
			'oathauth-disable-for-user' => true,
			'oathauth-verify-user' => true,
			'oathauth-view-log' => true,
		],
	],
	'+mkwiki' => [
		'autopatrolled' => [ 'autopatrol' => true ],
		'patroller' => [
			'patrol' => true,
			'autopatrol' => true,
			'rollback' => true,
		],
		'autoreviewed' => [ 'autoreview' => true ],
	],
	'+mlwiki' => [
		'autopatrolled' => [ 'autopatrol' => true ],
		'patroller' => [ 'patrol' => true ],
		'rollbacker' => [ 'rollback' => true ],
		'botadmin' => [
			'blockemail' => true,
			'block' => true,
			'ipblock-exempt' => true,
			'protect' => true,
			'editprotected' => true,
			'createaccount' => true,
			'deleterevision' => true,
			'delete' => true,
			'globalblock-whitelist' => true,
			'autoconfirmed' => true,
			'editsemiprotected' => true,
			'editinterface' => true,
			'editsitejson' => true,
			'autopatrol' => true,
			'import' => true,
			'patrol' => true,
			'markbotedits' => true,
			'nuke' => true,
			'abusefilter-modify' => true,
			'movefile' => true,
			'move' => true,
			'move-subpages' => true,
			'move-rootuserpages' => true,
			'noratelimit' => true,
			'suppressredirect' => true,
			'reupload-shared' => true,
			'override-antispoof' => true,
			'tboverride' => true,
			'reupload' => true,
			'skipcaptcha' => true,
			'rollback' => true,
			'browsearchive' => true,
			'undelete' => true,
			'upload' => true,
			'upload_by_url' => true,
			'apihighlimits' => true,
			'unwatchedpages' => true,
			'deletedhistory' => true,
			'deletedtext' => true,
			'abusefilter-log-detail' => true,
		],
	],
	'+mlwikisource' => [
		'patroller' => [ 'patrol' => true ],
		'autopatrolled' => [ 'autopatrol' => true ],
	],
	'+mrwiki' => [
		'rollbacker' => [ 'rollback' => true ], // T270864
	],
	'+mrwikisource' => [
		'patroller' => [
			'autopatrol' => true, // T269067
			'patrol' => true, // T269067
			'rollback' => true, // T269067
			'suppressredirect' => true, // T269067
		],
		'sysop' => [
			'templateeditor' => true, // T269067
		],
		'templateeditor' => [
			'editcontentmodel' => true, // T269067
			'tboverride' => true, // T269067
			'templateeditor' => true, // T269067
		],
	],
	'+mywiki' => [
		'autopatrolled' => [ 'autopatrol' => true, ], // T341026
		'patroller' => [
			'autopatrol' => true, // T341026
			'patrol' => true, // T341026
			'rollback' => true, // T341026
			'suppressredirect' => true, // T341026
		],
	],
	'+mznwiki' => [
		// Uploads are restricted to a uploader group - T187187
		'user' => [ 'reupload-own' => false, ],
		'uploader' => [
			'upload' => true,
			'reupload' => true,
			'reupload-own' => true,
			'reupload-shared' => true,
		],
	],
	'+newiki' => [
		'autopatrolled' => [ 'autopatrol' => true ], // T89816
		'rollbacker' => [
			'rollback' => true, // T90888
			'suppressredirect' => true, // T214012
		],
		'patroller' => [
			'patrol' => true, // T95101
			'move' => true, // T327114
			'move-subpages' => true, // T327114
			'move-categorypages' => true, // T327114
			'suppressredirect' => true, // T327114
			'delete-redirect' => true, // T327114
			'tboverride' => true, // T327114
		],
		'filemover' => [ 'movefile' => true ], // T95103
		'sysop' => [
			'templateeditor' => true, // T343257
		],
		'templateeditor' => [
			'templateeditor' => true,
			'editprotected' => true,
		], // T195557
		'flood' => [ 'bot' => true ], // T211181
		'transwiki' => [ 'oathauth-enable' => true ], // T214036
	],
	'+nlwiki' => [
		'autoconfirmed' => [ 'patrol' => true ],
		'bot' => [
			'move-categorypages' => true, // T161551
			'extendedconfirmed' => true, // T329642
		],
		'extendedconfirmed' => [ 'extendedconfirmed' => true ], // T329642
		'checkuser' => [
			'deletedhistory' => true,
			'deletedtext' => true,
			'browsearchive' => true,
			'block' => true, // T326355
		],
		'arbcom' => [
			'deletedhistory' => true,
			'deletedtext' => true,
			'browsearchive' => true,
		],
		'rollbacker' => [ 'rollback' => true ],
		'sysop' => [ 'extendedconfirmed' => true ], // T329642
		'user' => [ 'move-categorypages' => false ], // T161551
	],
	'+nlwiktionary' => [
		'user' => [ 'patrol' => true ],
	],
	'+nlwikivoyage' => [
		'autopatrolled' => [
			'autopatrol' => true,
			'patrol' => true,
		], // T46082
	],
	'+nlwikibooks' => [
		'user' => [ 'patrol' => true ],
	],
	'+nnwiki' => [
		'autopatrolled' => [ 'autopatrol' => true ],
		'patroller' => [
			'autopatrol' => true,
			'patrol' => true,
			'rollback' => true,
		],
	],
	'nowiki' => [
		'patroller' => [
			'patrol' => true,
			'autopatrol' => true,
			'rollback' => true,
			'unwatchedpages' => true,
			'suppressredirect' => true,
		],
		'autopatrolled' => [
			'autopatrol' => true,
			'unwatchedpages' => true,
		],
	],
	'+nowikibooks' => [
		'user' => [ 'patrol' => false ],
		'autoconfirmed' => [ 'patrol' => false ],
		'patroller' => [
			'patrol' => true,
			'autopatrol' => true,
			'rollback' => true,
			'markbotedits' => true,
		],
		'autopatrolled' => [ 'autopatrol' => true ],
	],
	'nowikimedia' => [
		'*' => [ 'edit' => false, ],
	],
	'+officewiki' => [
		'flood' => [ 'bot' => true ], // T86237
		'user' => [
			'massmessage' => true, // T66978
			'ipblock-exempt' => true, // T231943
		],
		'sysop' => [ 'importupload' => true, ], // T101663
		'securepoll' => [
			'securepoll-create-poll' => true,
			'editinterface' => true,
		],
	],
	'+sourceswiki' => [
		'flood' => [ 'bot' => true ], // T193350
	],
	'+ombudsmenwiki' => [
		'autoconfirmed' => [
			'autoconfirmed' => false,
			'editsemiprotected' => false,
			'reupload' => false,
			'upload' => false,
			'move' => false,
			'collectionsaveasuserpage' => false,
			'collectionsaveascommunitypage' => false,
			'skipcaptcha' => false,
		],
		'accountcreator' => [ 'noratelimit' => false, ],
		'bureaucrat' => [ 'createaccount' => true, ],
		'sysop' => [ 'createaccount' => false, ],
	],
	'+orwiki' => [
		'rollbacker' => [ 'rollback' => true ],
	],
	'pawiki' => [
		'patroller' => [ 'patrol' => true ], // T120369
		'autopatrolled' => [ 'autopatrol' => true ], // T120369
	],
	'+pawikisource' => [
		'user' => [
			'pagequality-validate' => false // T341428
		],
		'validator' => [
			'pagequality-validate' => true // T341428
		],
		'sysop' => [
			'pagequality-validate' => true // T341428#9045026
		]
	],
	// T8303
	'+plwiki' => [
		'editor' => [
			'rollback' => true,
			'patrolmarks' => true,
			'editor' => true,
			'editeditorprotected' => true,
		], // T22154 and T48990
		'flood' => [ 'bot' => true ], // T22155
		'sysop' => [ 'editor' => true ], // T48990
		'bot' => [ 'editor' => true ], // T48990
		'arbcom' => [ // T256572
			'abusefilter-log-detail' => true,
			'abusefilter-view-private' => true,
			'abusefilter-log-private' => true,
			'browsearchive' => true,
			'deletedhistory' => true,
			'deletedtext' => true,
		],
		'interface-admin' => [ 'editcontentmodel' => true, ], // T325819
	],
	'+plwikiquote' => [
		'patroller' => [
			'patrol' => true,
			'autopatrol' => true,
		], // T30479
	],
	'+plwikisource' => [
		'*' => [ 'pagequality' => true, ], // T212478
		'editor' => [
			'autopatrol' => true,
			'patrol' => true,
			'suppressredirect' => true,
		], // T72459, T212655
	],
	'+plwiktionary' => [
		'editor' => [ 'patrolmarks' => true ],
	],
	// T11024, T12362
	'+ptwiki' => [
		'autoconfirmed' => [
			'patrol' => true,
			'abusefilter-log-detail' => true,
			'move' => false, // T313802
		],
		'bot' => [
			'editautoreviewprotected' => true,
			'extendedconfirmed' => true, // T281926
			'move' => true, // T313802
		],
		'autoreviewer' => [
			'autopatrol' => true,
			'editautoreviewprotected' => true,
			'movefile' => true, // T161532
			'extendedconfirmed' => true, // T292912
			'move' => true, // T313802
		],
		'eliminator' => [
			'browsearchive' => true,
			'delete' => true,
			'nuke' => true,
			'undelete' => true,
			'deletedhistory' => true,
			'deletedtext' => true,
			'autopatrol' => true,
			'suppressredirect' => true,
			'editautoreviewprotected' => true,
			'movefile' => true, // T161532
			'move-rootuserpages' => true, // T205595
			'flow-delete' => true, // T283266
		],
		'rollbacker' => [
			'rollback' => true,
			'unwatchedpages' => true,
			'block' => true, // T37261
			'abusefilter-log-private' => true, // T237830
		], // T29563
		'user' => [ 'move-rootuserpages' => false, ],
		'sysop' => [
			'editautoreviewprotected' => true,
			'extendedconfirmed' => true, // T281926
		],
		'bureaucrat' => [
			'move-rootuserpages' => true,
			'autopatrol' => true,
			'editautoreviewprotected' => true
		],
		'interface-editor' => [ // T41905
			'editinterface' => true,
			'editsitejson' => true,
		],
		'flood' => [ 'bot' => true, ], // T228521
		'extendedconfirmed' => [
			'extendedconfirmed' => true, // T281860
			'move' => true, // T313802
		],
		'autoextendedconfirmed' => [
			'extendedconfirmed' => true, // T292915
			'move' => true, // T313802
		],
	],
	'+ptwikinews' => [
		'editprotected' => [
			'editprotected' => true,
			'editsemiprotected' => true,
		], // T162577
	],
	'+ptwikisource' => [ // T331762
		'user' => [ 'move-categorypages' => false ],
		'patroller' => [
			'patrol' => true,
			'move-categorypages' => true,
			'movefile' => true,
			'suppressredirect' => true,
		],
		'autopatrolled' => [
			'autopatrol' => true,
			'move-categorypages' => true,
			'movefile' => true,
		],
		'rollbacker' => [
			'rollback' => true,
			'move-categorypages' => true,
			'movefile' => true,
			'suppressredirect' => true,
		],
		'bot' => [ 'move-subpages' => true ],
	],
	'+ptwikivoyage' => [
		'autopatrolled' => [ 'autopatrol' => true, ], // T168981
	],
	'+quwiki' => [
		'rollbacker' => [ 'rollback' => true ],
	],
	'rowiki' => [
		'sysop' => [
			'extendedconfirmed' => true, // T254471
			'templateeditor' => true, // T63172
		],
		'patroller' => [
			'patrol' => true,
			'upwizcampaigns' => true, // T61242
			'rollback' => true, // T231099
			'deleterevision' => true, // T234051
		],
		'autopatrolled' => [
			'autopatrol' => true,
			'movefile' => true,
		], // T168192
		'templateeditor' => [ // T63172
			'templateeditor' => true,
			'editinterface' => true,
			'editsitejson' => true,
		],
		'extendedconfirmed' => [ // T254471
			'extendedconfirmed' => true
		],
	],
	'+ruewiki' => [
		'rollbacker' => [ 'rollback' => true ], // T264147
	],
	'+ruwiki' => [
		'arbcom' => [ // T51334
			'abusefilter-log-detail' => true,
			'browsearchive' => true,
			'deletedhistory' => true,
			'deletedtext' => true,
			'abusefilter-log-private' => true, // T336625
			'abusefilter-view-private' => true, // T336625
		],
		'autoconfirmed' => [ 'collectionsaveascommunitypage' => false, ], // T85780
		'autoreview' => [ 'editautoreviewprotected' => true ], // T337430
		'bot' => [
			'autopatrol' => false, // T154285
			'changetags' => true, // T136187
			'move-categorypages' => true, // T87230
			'editautoreviewprotected' => true, // T337430
		],
		'closer' => [
			'delete' => true,
			'move-categorypages' => true, // T68871
			'move-subpages' => true, // T76131
			'reupload' => true,
			'reupload-own' => true,
			'suppressredirect' => true,
			'upload' => true,
		],
		'editor' => [
			'editautoreviewprotected' => true, // T337430
			'move-categorypages' => true, // T341707
			'suppressredirect' => true, // T341707
		],
		'engineer' => [ // T144599, T190619, T355499
			'apihighlimits' => true,
			'editcontentmodel' => true,
			'editinterface' => true,
			'editprotected' => true,
			'editsitejson' => true,
			'edituserjson' => true,
			'jsonconfig-flush' => true,
			'move-categorypages' => true,
			'move-subpages' => true,
			'movefile' => true,
			'noratelimit' => true,
			'suppressredirect' => true,
			'tboverride' => true,

		],
		'filemover' => [ // T32984
			'move-categorypages' => true, // T68871
			'move-subpages' => true, // T76131
			'movefile' => true,
			'reupload' => true,
			'reupload-own' => true,
			'suppressredirect' => true,
			'upload' => true,
		],
		'uploader' => [ // T14334
			'upload' => true,
			'reupload-own' => true,
			'reupload' => true,
		],
		'user' => [ // T68871
			'changetags' => false, // T136187
			'move-categorypages' => false,
		],
		'rollbacker' => [ 'rollback' => true ],
		'suppressredirect' => [ // T40408, T68871
			'move-categorypages' => true,
			'suppressredirect' => true,
		],
		'sysop' => [
			'autopatrol' => false, // T154285
			'changetags' => true, // T136187
			'patrol' => false, // T154285
			'editautoreviewprotected' => true, // T337430
		],
	],
	'+ruwikinews' => [
		'autoconfirmed' => [ 'movestable' => false, ], // T201265
		'autoreview' => [
			'move-categorypages' => true,
			'movestable' => true, // T201265
		],
		'user' => [ 'move-categorypages' => false, ], // T201265
		'rollbacker' => [ 'rollback' => true ], // T201265
	],
	'+ruwikiquote' => [
		'autoeditor' => [
			'autoreview' => true,
			'autoconfirmed' => true,
			'editsemiprotected' => true,
		],
		'editor' => [
			'suppressredirect' => true, // T357241
		],
		'rollbacker' => [ 'rollback' => true ], // T200201
	],
	'+ruwikisource' => [
		'autoeditor' => [
			'autoreview' => true,
			'autoconfirmed' => true,
			'editsemiprotected' => true,
		],
		'rollbacker' => [ 'rollback' => true, 'suppressredirect' => true ],
		'flood' => [ 'bot' => true ],
	],
	'+ruwikiversity' => [
		'patroller' => [ 'patrol' => true, 'autopatrol' => true ],
	],
	'+ruwikivoyage' => [
		'autoconfirmed' => [ 'reupload-shared' => true ], // T116575
		'autopatrolled' => [ 'autopatrol' => true ], // T48915
		'rollbacker' => [ 'rollback' => true ], // T116143
		'patroller' => [ 'patrol' => true ], // T116143
		'filemover' => [
			'movefile' => true, // T116143
			'suppressredirect' => true, // T313614
		],
	],
	'+ruwiktionary' => [
		'rollbacker' => [ 'rollback' => true ], // T183655
		'editor' => [ 'suppressredirect' => true ], // T183719
		'autoreview' => [ 'suppressredirect' => true ], // T183719
	],
	'+sawiki' => [
		'autopatrolled' => [ 'autopatrol' => true ],
		'patroller' => [ 'patrol' => true ], // T117314
	],
	'+sawikisource' => [
		'autopatrolled' => [ 'autopatrol' => true ],
	],
	'+scowiki' => [
		'rollbacker' => [ 'rollback' => true ],
		'autopatrolled' => [ 'autopatrol' => true, ],
		'flood' => [ 'bot' => true, ], // T111753
	],
	'+sdwiki' => [
		'autopatrolled' => [ 'autopatrol' => true ], // T177141
	],
	'+simplewiki' => [
		'bot' => [ 'changetags' => true ], // T339124
		'flood' => [ 'bot' => true ],
		'rollbacker' => [ 'rollback' => true, ],
		// T29875 (see comment 4)
		'patroller' => [
			'patrol' => true,
			'autopatrol' => true,
		],
		'sysop' => [ 'changetags' => true ], // T339124
		'uploader' => [
			'upload' => true,
			'reupload' => true,
			'reupload-own' => true,
		], // T127826
		'user' => [ 'changetags' => false ], // T339124
	],
	'+simplewiktionary' => [
		'rollbacker' => [ 'rollback' => true, ],
		'autopatrolled' => [
			'patrol' => true,
			'autopatrol' => true,
		],
	],
	'shwiki' => [ // T52802
		'patroller' => [
			'patrol' => true,
			'editpatrolprotected' => true,
		], // T344306
		'autopatrolled' => [
			'autopatrol' => true,
			'patrolmarks' => true,
			'editautopatrolprotected' => true,
		], // T62818, T344306
		'rollbacker' => [
			'rollback' => true,
			'markbotedits' => true,
		], // T64462
		'filemover' => [
			'movefile' => true,
			'suppressredirect' => true,
		],
		'flood' => [ 'bot' => true ],
	],
	'+shwiktionary' => [
		'autopatrolled' => [ 'autopatrol' => true ],
	],
	'siwiki' => [
		'rollbacker' => [ 'rollback' => true ],
		'autopatrolled' => [ 'autopatrol' => true ],
	],
	'+skwiki' => [
		'rollbacker' => [ 'rollback' => true ],
	],
	'+specieswiki' => [ // T89147
		'autopatrolled' => [ 'autopatrol' => true ],
		'patroller' => [
			'patrol' => true,
			'autopatrol' => true,
		],
	],
	'+srwiki' => [ // T208633, T210000 and T213050
		'user' => [ 'move-categorypages' => false ],
		'autoconfirmed' => [ 'upload' => true ],
		'patroller' => [
			'patrol' => true,
			'movefile' => true,
			'suppressredirect' => true,
			'move-categorypages' => true,
		],
		'autopatrolled' => [
			'autopatrol' => true,
			'move-categorypages' => true,
			'editautopatrolprotected' => true
		],
		'rollbacker' => [
			'rollback' => true,
			'movefile' => true,
			'suppressredirect' => true,
			'move-categorypages' => true,
		],
		'flood' => [
			'bot' => true,
			'move-categorypages' => true,
		],
		'bot' => [ 'move-categorypages' => true ],
	],
	'+srwikibooks' => [ // T209250, T213824, T213825, T213826, T213827
		'user' => [ 'move-categorypages' => false ],
		'patroller' => [
			'patrol' => true,
			'move-categorypages' => true,
			'movefile' => true,
			'suppressredirect' => true,
		],
		'autopatrolled' => [
			'autopatrol' => true,
			'move-categorypages' => true,
			'movefile' => true,
			'editautopatrolprotected' => true,
		],
		'rollbacker' => [
			'rollback' => true,
			'move-categorypages' => true,
			'movefile' => true,
			'suppressredirect' => true,
		],
		'bot' => [ 'move-subpages' => true ],
	],
	'+srwikinews' => [ // T209251, T213679, T213680, T213681 and T213682
		'user' => [ 'move-categorypages' => false ],
		'patroller' => [
			'patrol' => true,
			'move-categorypages' => true,
			'movefile' => true,
			'suppressredirect' => true,
		],
		'autopatrolled' => [
			'autopatrol' => true,
			'move-categorypages' => true,
			'movefile' => true,
			'editautopatrolprotected' => true,
		],
		'rollbacker' => [
			'rollback' => true,
			'move-categorypages' => true,
			'movefile' => true,
			'suppressredirect' => true,
		],
		'bot' => [ 'move-subpages' => true ],
	],
	'+srwikisource' => [ // T206935, T213055, T213059, T213063, T213065
		'user' => [ 'move-categorypages' => false ],
		'patroller' => [
			'patrol' => true,
			'move-categorypages' => true,
			'movefile' => true,
			'suppressredirect' => true,
		],
		'autopatrolled' => [
			'autopatrol' => true,
			'move-categorypages' => true,
			'movefile' => true,
			'editautopatrolprotected' => true,
		],
		'rollbacker' => [
			'rollback' => true,
			'move-categorypages' => true,
			'movefile' => true,
			'suppressredirect' => true,
		],
		'bot' => [ 'move-subpages' => true ],
	],
	'+srwikiquote' => [ // T206936, T213684, T213685, T213686, T213687
		'user' => [ 'move-categorypages' => false ],
		'patroller' => [
			'patrol' => true,
			'move-categorypages' => true,
			'movefile' => true,
			'suppressredirect' => true,
		],
		'autopatrolled' => [
			'autopatrol' => true,
			'move-categorypages' => true,
			'movefile' => true,
			'editautopatrolprotected' => true,
		],
		'rollbacker' => [
			'rollback' => true,
			'move-categorypages' => true,
			'movefile' => true,
			'suppressredirect' => true,
		],
		'bot' => [ 'move-subpages' => true ],
	],
	'+srwiktionary' => [ // T209252, T213828, T213829, T213830 and T213832
		'user' => [ 'move-categorypages' => false ],
		'patroller' => [
			'patrol' => true,
			'move-categorypages' => true,
			'movefile' => true,
			'suppressredirect' => true,
		],
		'autopatrolled' => [
			'autopatrol' => true,
			'move-categorypages' => true,
			'movefile' => true,
			'editautopatrolprotected' => true,
		],
		'rollbacker' => [
			'rollback' => true,
			'move-categorypages' => true,
			'movefile' => true,
			'suppressredirect' => true,
		],
		'bot' => [ 'move-subpages' => true ],
	],
	'+stewardwiki' => [
		'bureaucrat' => [ 'userrights' => true ], // T30773
	],
	'+svwiki' => [
		'user' => [ 'move-rootuserpages' => false ], // T238842
		'autopatrolled' => [ 'autopatrol' => true ], // T161210
		'autoconfirmed' => [ 'patrol' => true ],
		'rollbacker' => [
			'rollback' => true,
			'autopatrol' => true,
		],
		'bot' => [ 'extendedconfirmed' => true ], // T279836
		'extendedconfirmed' => [ 'extendedconfirmed' => true ], // T279836
		'sysop' => [ 'extendedconfirmed' => true ], // T279836
	],
	'+svwikisource' => [ // T30614 & 36895
		'autopatrolled' => [
			'autopatrol' => true,
			'suppressredirect' => true,
			'upload' => true,
			'reupload' => true
		],
	],
	'+svwikivoyage' => [
		'autopatrolled' => [ 'autopatrol' => true ], // T93339
		'patroller' => [ 'patrol' => true ], // T93339
		'rollbacker' => [ 'rollback' => true ], // T93339
	],
	'+svwiktionary' => [
		'autopatrolled' => [ 'autopatrol' => true ], // T161919
	],
	'+swwiki' => [
		'*' => [ 'createpage' => false ], // T44894
	],
	'+tawiki' => [
		'autopatrolled' => [ 'autopatrol' => true ], // T95180
		'patroller' => [
			'patrol' => true,
			'autopatrol' => true,
			'abusefilter-log-detail' => true,
		], // T95180
		'rollbacker' => [ 'rollback' => true ], // T95180
	],
	'testwiki' => [
		'accountcreator' => [
			'override-antispoof' => true,
			'tboverride' => true,
		],
		'bot' => [ 'extendedconfirmed' => true ], // T302860
		'extendedconfirmed' => [ 'extendedconfirmed' => true ], // T302860
		'filemover' => [ 'movefile' => true ], // T32121
		'user' => [
			'upload_by_url' => true, // For testing of Flickr uploading via UploadWizard
			'upload' => true, // Exception to T14556, used for testing of upload tools
			'pagelang' => true, // testing of T69223
		],
		'templateeditor' => [
			'templateeditor' => true, // T61084
			'tboverride' => true,
			'editcontentmodel' => true, // T217499
		],
		'sysop' => [
			'deleterevision' => true,
			'extendedconfirmed' => true, // T302860
			'templateeditor' => true,
			'securepoll-create-poll' => true,
		],
		'researcher' => [
			'browsearchive' => true,
			'deletedhistory' => true,
			'apihighlimits' => true,
		],
		'rollbacker' => [ 'rollback' => true ],
		'centralnoticeadmin' => [
			'banner-protect' => true,
			'centralnotice-admin' => true,
			'editinterface' => true, // adding to allow CN access without local sysop JRA 2013-02-21
		],
		'patroller' => [ 'patrol' => true, ], // T354063
	],
	'test2wiki' => [
		'*' => [ 'createpagemainns' => false ],
		'autoreview' => [ 'autopatrol' => true, ], // T134491
		'editor' => [ 'patrol' => true, ], // T134491
		'user' => [
			'upload_by_url' => true,
			'upload' => true,
			'createpagemainns' => false,
		],
		'qa_automation' => [ // For browser tests, T60375 and T63799
			'block' => true,
			'flow-delete' => true,
			'flow-suppress' => true,
		],
	],
	'+thwiki' => [
		'autoconfirmed' => [
			'upload' => false, // T216615
			'reupload' => false, // T216615
		],
		'uploader' => [ // T216615
			'upload' => true,
			'reupload' => true,
			'reupload-shared' => true,
			'reupload-own' => true,
			'movefile' => true,
		],
		'patroller' => [ // T272149
			'patrol' => true,
			'autopatrol' => true,
		],
		'user' => [ 'reupload-own' => false, ], // T216615
		'sysop' => [ 'reupload-own' => true, ],
	],
	'+trwiki' => [
		'autoreview' => [ 'autopatrol' => true, ], // T40690
		'checkuser' => [
			'browsearchive' => true, // T40690
			'deletedhistory' => true, // T40690
			'deletedtext' => true, // T40690
		],
		'massmessage-sender' => [ 'massmessage' => true, ], // T147740
		'suppress' => [
			'browsearchive' => true, // T40690
			'deletedhistory' => true, // T40690
			'deletedtext' => true, // T40690
		],
		'patroller' => [
			'autoconfirmed' => true, // T40690
			'editsemiprotected' => true,
			'autopatrol' => true, // T40690
			'autoreview' => true, // T40690
			'patrol' => true,
			'review' => true, // T40690
			'rollback' => true, // T40690
			'unreviewedpages' => true, // T40690
			'movefile' => true, // T46587
		],
		'user' => [
			'changetags' => false, // T264508
		],
		'sysop' => [
			'changetags' => true, // T264508
			'review' => true,
			'unreviewedpages' => true,
		], // T40690
		'interface-editor' => [ // Renamed from 'technician' per T144638
			'abusefilter-log-detail' => true, // T40690
			'abusefilter-modify' => true, // T40690
			'apihighlimits' => true, // T40690
			'editinterface' => true, // T40690
			'editprotected' => true, // T247672
			'editsemiprotected' => true, // T247672
			'editsitejson' => true,
			'noratelimit' => true, // T40690
		],
	],
	'+trwikiquote' => [
		'editor' => [ // T122710
			'patrol' => true,
			'autopatrol' => true,
			'rollback' => true,
			'movefile' => true,
		],
		'autoreview' => [ 'autopatrol' => true ], // T122710
		'checkuser' => [
			'deletedtext' => true,
			'deletedhistory' => true,
		], // T122710
	],
	'+trwikivoyage' => [
		'rollbacker' => [ 'rollback' => true ], // T314678
	],
	'+ukwiki' => [
		'patroller' => [
			'patrol' => true,
			'autopatrol' => true,
		],
		'rollbacker' => [ 'rollback' => true ],
		'filemover' => [
			'movefile' => true,
			'suppressredirect' => true,
		], // T119636
		'flood' => [ 'bot' => true ], // T319243
	],
	'+ukwikivoyage' => [
		'autopatrolled' => [ 'autopatrol' => true ], // T56299
		'rollbacker' => [ 'rollback' => true ],
		'uploader' => [
			'upload' => true,
			'reupload' => true,
		],
	],
	'+ukwiktionary' => [
		'autoeditor' => [ 'autoreview' => true ],
	],
	'+urwiki' => [
		'eliminator' => [
			'block' => true,
			'delete' => true,
			'undelete' => true, // T185829
			'deleterevision' => true,
			'mergehistory' => true,
			'protect' => true,
			'editprotected' => true, // T281274
			'suppressredirect'  => true,
			'deletedhistory' => true
		], // T184607
		'accountcreator' => [
			'tboverride-account' => true,
			'override-antispoof' => true,
		], // T137888
		'filemover' => [ 'movefile' => true ], // T137888
		'interface-editor' => [
			'editinterface' => true,
			'editsitejson' => true,
		], // T120348
		'rollbacker' => [ 'rollback' => true ], // T47642
		'autopatrolled' => [ 'autopatrol' => true ], // T139302
		'massmessage-sender' => [ 'massmessage' => true, ], // T144701
		'extendedmover' => [
			'move' => true,
			'move-subpages' => true,
			'suppressredirect' => true,
			'tboverride' => true
		], // T211978
		'patroller' => [ 'patrol' => true ], // T301491
	],
	'+uzwiki' => [
		'rollbacker' => [ 'rollback' => true ], // T265509
		'eliminator' => [
			'delete' => true, // T302670
		],
		'patroller' => [ // T338826
			'autopatrol' => true,
			'patrol' => true,
		],
	],
	'+vecwiki' => [
		'flood' => [ 'bot' => true ],
	],
	'+viwiki' => [
		'eliminator' => [ // T70612, T294530
			'autopatrol' => true,
			'browsearchive' => true,
			'delete' => true,
			'deletelogentry' => true,
			'deletedhistory' => true,
			'deletedtext' => true,
			'deleterevision' => true,
			'mergehistory' => true,
			'movefile' => true,
			'move-subpages' => true,
			'nuke' => true,
			'patrol' => true,
			'protect' => true,
			'suppressredirect' => true,
			'rollback' => true,
			'undelete' => true,
			'extendedconfirmed' => true, // T215493
			'editautopatrolprotected' => true, // T303579
			'upload_by_url' => true, // T303577
		],
		'flood' => [ 'bot' => true ],
		'rollbacker' => [ 'rollback' => true ],
		'patroller' => [ 'patrol' => true ], // T48828
		'autopatrolled' => [
			'autopatrol' => true, // T48828
			'editautopatrolprotected' => true, // T303579
		],
		'sysop' => [
			'extendedconfirmed' => true, // T215493
			'templateeditor' => true, // T296154
			'upload_by_url' => true, // T303577
		],
		'extendedconfirmed' => [ 'extendedconfirmed' => true ], // T215493
		'bot' => [ 'extendedconfirmed' => true ], // T215493
		'templateeditor' => [
			'templateeditor' => true,
			'tboverride' => true,
			'editcontentmodel' => true
		], // T296154
		'uploader' => [ // T303577
			'upload' => true,
			'upload_by_url' => true,
		],
	],
	'+viwikibooks' => [
		'eliminator' => [ // T202207
			'autopatrol' => true,
			'delete' => true,
			'deletedhistory' => true,
			'deletedtext' => true,
			'deleterevision' => true,
			'patrol' => true,
			'protect' => true,
			'rollback' => true,
			'undelete' => true,
		],
	],
	'+viwikisource' => [
		'sysop' => [ 'flow-create-board' => true ], // T212929
	],
	'+viwiktionary' => [
		'rollbacker' => [ 'rollback' => true ], // T176979
	],
	// T74589 SecurePoll rights
	'+votewiki' => [
		'electcomm' => [
			'securepoll-create-poll' => true,
			'editinterface' => true,
		],
		'staffsupport' => [
			'securepoll-create-poll' => true,
			'editinterface' => true,
		],
		'electionadmin' => [
			'editinterface' => true,
			'securepoll-view-voter-pii' => true,
		],
	],
	'wikidata' => [
		'autoconfirmed' => [
			'patrol' => true,
			'autopatrol' => true,
			], // T58203
		'bot' => [
			'noratelimit' => false, // T258354
		],
		'rollbacker' => [
			'rollback' => true, // T47165
			'autopatrol' => true, // T57495
			'patrol' => true, // T57495
			'editsemiprotected' => true, // T57495
			'move' => true, // T57495
			'autoconfirmed' => true, // T57495
			'skipcaptcha' => true, // T57495
			'abusefilter-log-detail' => true, // T57495
			'suppressredirect' => true, // T58203
		],
		'propertycreator' => [
			'property-create' => true, // T48953
			'autopatrol' => true, // T57495
			'patrol' => true, // T57495
			'editsemiprotected' => true, // T57495
			'move' => true, // T57495
			'autoconfirmed' => true, // T57495
			'skipcaptcha' => true, // T57495
			'abusefilter-log-detail' => true, // T57495
		],
		'wikidata-staff' => [
			'abusefilter-log-detail' => true,
			'abusefilter-modify' => true,
			'abusefilter-modify-restricted' => true,
			'apihighlimits' => true,
			'autoconfirmed' => true,
			'autopatrol' => true,
			'block' => true,
			'blockemail' => true,
			'browsearchive' => true,
			'delete' => true,
			'deletedhistory' => true,
			'deletedtext' => true,
			'deletelogentry' => true,
			'deleterevision' => true,
			'globalblock-whitelist' => true,
			'editinterface' => true,
			'editprotected' => true,
			'editsemiprotected' => true,
			'editsitejson' => true,
			'import' => true,
			'ipblock-exempt' => true,
			'markbotedits' => true,
			'massmessage' => true,
			'mergehistory' => true,
			'move-subpages' => true,
			'noratelimit' => true,
			'nuke' => true,
			'override-antispoof' => true,
			'pagetranslation' => true,
			'patrol' => true,
			'property-create' => true,
			'protect' => true,
			'rollback' => true,
			'skipcaptcha' => true,
			'spamblacklistlog' => true,
			'suppressredirect' => true,
			'tboverride' => true,
			'titleblacklistlog' => true,
			'translate-import' => true,
			'translate-manage' => true,
			'undelete' => true,
			'unwatchedpages' => true,
			'pagelang' => true, // T337760
		], // T74459
		'user' => [
			'changetags' => false, // T303682
		],
		'sysop' => [
			'changetags' => true, // T303682
			'property-create' => true // T48953
		],
		'flood' => [ 'bot' => true ], // T50013
	],
	'+wikifunctionswiki' => [
		'*' => [
			// Temporarily during early, limited deployment, logged-out users can't do this
			'edit' => false,

			// In emergencies, you can set the line below to false to prevent logged-out users from running functions
			'wikilambda-execute' => true, // T349055
		],
		'user' => [
			// Temporarily during early, limited deployment, re-grant to logged-in users the standard
			// wikitext editing right that logged-out users can't do
			'edit' => true,

			// In emergencies, you can set the line below to false to prevent logged-in users from running functions
			'wikilambda-execute' => true,
			// In emergencies, you can set the line below to false to prevent logged-in users from running unsaved code
			'wikilambda-execute-unsaved-code' => true, // T349057
		],
		'autopatrolled' => [
			'autopatrol' => true // T343946
		],
		'functioneer' => [
			// Temporarily during early, limited deployment, Functioneers can't do this
			"wikilambda-create-type" => false,

			"autopatrol" => true, // T344085
		],
		'wikifunctions-staff' => [
			// Special staff-only user group for helping the community with Christmas-tree rights.
			'wikilambda-connect-implementation' => true,
			'wikilambda-connect-tester' => true,
			'wikilambda-create' => true,
			'wikilambda-create-boolean' => true,
			'wikilambda-create-function' => true,
			'wikilambda-create-implementation' => true,
			'wikilambda-create-language' => true,
			'wikilambda-create-predefined' => true,
			'wikilambda-create-programming' => true,
			'wikilambda-create-tester' => true,
			'wikilambda-create-type' => true,
			'wikilambda-create-unit' => true,
			'wikilambda-disconnect-implementation' => true,
			'wikilambda-disconnect-tester' => true,
			'wikilambda-edit' => true,
			'wikilambda-edit-argument-label' => true,
			'wikilambda-edit-attached-implementation' => true,
			'wikilambda-edit-attached-tester' => true,
			'wikilambda-edit-builtin-function' => true,
			'wikilambda-edit-boolean' => true,
			'wikilambda-edit-error-key-label' => true,
			'wikilambda-edit-implementation' => true,
			'wikilambda-edit-key-label' => true,
			'wikilambda-edit-language' => true,
			'wikilambda-edit-object-alias' => true,
			'wikilambda-edit-object-description' => true,
			'wikilambda-edit-object-label' => true,
			'wikilambda-edit-object-type' => true,
			'wikilambda-edit-predefined' => true,
			'wikilambda-edit-programming' => true,
			'wikilambda-edit-running-function' => true,
			'wikilambda-edit-running-function-definition' => true,
			'wikilambda-edit-tester' => true,
			'wikilambda-edit-type' => true,
			'wikilambda-edit-unit' => true,
			'wikilambda-edit-user-function' => true,
			'wikilambda-execute' => true,
			'wikilambda-execute-unsaved-code' => true,

			'autopatrol' => true, // T350028
		],
	],
	'+wikimaniawiki' => [
		// Uploads are restricted to sysop and an uploader group - T225505
		'uploader' => [
			'upload' => true,
			'reupload' => true,
			'reupload-own' => true,
		],
	],
	'+wuuwiki' => [
		'rollbacker' => [
			'rollback' => true,
			'autopatrol' => true,
			'patrol' => true,
		], // T116270
	],
	'+yowiki' => [
		'accountcreator' => [ 'noratelimit' => true, ], // T249487
		'rollbacker' => [ 'rollback' => true, ], // T249487
	],
	// due to mass vandalism complaint, 2006-04-11
	'+zhwiki' => [
		'*' => [ 'flow-hide' => false, ], // T264489
		'abusefilter-helper' => [ 'oathauth-enable' => true ], // T344398
		'autoconfirmed' => [
			'flow-hide' => true, // T264489
			'upload_by_url' => true, // T142991
		],
		'bot' => [ 'extendedconfirmed' => true ], // T287322
		'rollbacker' => [
			'rollback' => true, // T18988
			'suppressredirect' => true, // T201160
			'unwatchedpages' => true, // T219285
		],
		'patroller' => [
			'patrol' => true,
			'autopatrol' => true,
			'movefile' => true, // T195247
			'suppressredirect' => true, // T201160
			'unwatchedpages' => true, // T219285
		],
		'autoreviewer' => [
			'autopatrol' => true,
			'movefile' => true,
			'suppressredirect' => true, // T343711
		], // T195247
		'extendedconfirmed' => [
			'extendedconfirmed' => true, // T287322
			'movefile' => true, // T331691
		],
		'flood' => [ 'bot' => true ],
		'massmessage-sender' => [ 'massmessage' => true ], // T130814
		'filemover' => [ 'movefile' => true ], // T195247
		'eventparticipant' => [
			'autoconfirmed' => true, // T198167
			'upload' => true, // T198167
			'reupload' => true, // T198167
			'skipcaptcha' => true, // T198167
		],
		'transwiki' => [ 'suppressredirect' => true, ], // T250972
		'templateeditor' => [ 'templateeditor' => true, ], // T260012
		'ipblock-exempt-grantor' => [
			'noratelimit' => true, // T357991
		],
		'sysop' => [
			'templateeditor' => true, // T260012
			'extendedconfirmed' => true, // T287322
		],
	],
	'+zh_classicalwiki' => [
		'editor' => [ 'rollback' => true ], // T188064
	],
	'+zhwikibooks' => [
		'autoconfirmed' => [ 'suppressredirect' => true ], // T185182
		'flood' => [ 'bot' => true ], // T185182
	],
	'+zhwikinews' => [
		'autoconfirmed' => [ 'suppressredirect' => true, ], // T270023
		'rollbacker' => [ 'rollback' => true ], // T29268
		'flood' => [ 'bot' => true ], // T54546
	],
	'+zhwikiquote' => [ // T189289
		'autoconfirmed' => [ 'suppressredirect' => true ],
		'flood' => [ 'bot' => true ],
	],
	'+zhwikiversity' => [
		'patroller' => [ // T202599
			'patrol' => true,
			'autopatrol' => true,
			'rollback' => true,
			'suppressredirect' => true,
		],
		'autopatrolled' => [ 'autopatrol' => true ], // T202599
		'flood' => [ 'bot' => true ], // T202599
	],
	'+zhwikivoyage' => [ // T62328
		'autopatrolled' => [ 'autopatrol' => true ],
		'patroller' => [
			'patrol' => true,
			'rollback' => true,
			'suppressredirect' => true,
		], // T212272
	],
	'+zhwiktionary' => [ // T7836
		'bot' => [ 'patrol' => true ],
		'flood' => [ 'bot' => true ], // T187018
		'autoconfirmed' => [ 'suppressredirect' => true ], // T187018
		'templateeditor' => [ 'templateeditor' => true, ], // T286101
		'sysop' => [ 'templateeditor' => true ], // T286101
	],
	'+zh_yuewiki' => [
		'autoconfirmed' => [ 'patrol' => true ],
		'rollbacker' => [ 'rollback' => true ],
		'autoreviewer' => [ 'autopatrol' => true ],
	],
	'+strategywiki' => [
		'flood' => [ 'bot' => true ]
	],
],
# @} end of groupOverrides

# groupOverrides2 @{
'groupOverrides2' => [
	// IMPORTANT: don't forget to use a '+' sign in front of any group name
	// after 'default' or it will replace the defaults completely.
	'default' => [
		'sysop' => [
			'importupload' => false,
			'suppressredirect' => true, // http://meta.wikimedia.org/w/index.php?title=Wikimedia_Forum&oldid=1371655#Gives_sysops_to_.22suppressredirect.22_right
			'noratelimit' => true,
			'deleterevision' => true,
			'deletelogentry' => true,
			'editcontentmodel' => true,
			'unblockself' => false, # T150826
		],
		'bot' => [
			'noratelimit' => true,
		],
		'accountcreator' => [
			'noratelimit' => true,
		],
		'bureaucrat' => [
			'noratelimit' => true,
		],
		'steward' => [
			'noratelimit' => true,
			'bigdelete' => true,
			'centralauth-lock' => false, // T208035
			'centralauth-suppress' => false, // T302675
			'centralauth-unmerge' => false, // T208035
			'globalblock' => false, // T208035
		],
		'import' => [ 'importupload' => true, 'import' => true ],
		'transwiki' => [ 'import' => true ],
		'user' => [
			'reupload-shared' => false,
			'reupload' => false,
			'upload' => false, // T14556
			'reupload-own' => true,
			'move' => false, // T14071
			'move-subpages' => false, // for now...
			'movefile' => false, // r93871 CR
			'editcontentmodel' => false, // temp, pending T85847
		],
		'autoconfirmed' => [
			'reupload' => true,
			'upload' => true, // T14556
			'move' => true,
			'collectionsaveasuserpage' => true,
			'collectionsaveascommunitypage' => true,
		],
		// Deployed to all wikis by Andrew, 2009-04-28
		'ipblock-exempt' => [
			'ipblock-exempt' => true,
			'torunblocked' => true,
			'sfsblock-bypass' => true,
		],

		# To allow for inline log suppression -- 2009-01-29 -- BV
		'suppress' => [
			'deleterevision' => true,
			'deletelogentry' => true,
			'hideuser' => true, // was forgotten. added 2009-03-05 -- BV
			'suppressrevision' => true,
			'suppressionlog' => true,
			'abusefilter-hide-log' => true, // Andrew, 2010-08-28
			'abusefilter-hidden-log' => true, // Andrew, 2010-08-28
		],
	],

	// Whitelist read wikis
	'+private' => [
		'*' => [
			'read' => false,
			'edit' => false,
			'createaccount' => false
		],
		'user' => [
			'move' => true,
			'upload' => true,
			'autoconfirmed' => true,
			'editsemiprotected' => true,
			'reupload' => true,
			'skipcaptcha' => true,
			'collectionsaveascommunitypage' => true,
			'collectionsaveasuserpage' => true,
		],
		'inactive' => [
			// for show only
		],
	],

	// Fishbowls
	'+fishbowl' => [
		'*' => [
			'edit' => false,
			'createaccount' => false
		],
		'user' => [
		'move' => true,
			'upload' => true,
			'autoconfirmed' => true,
			'editsemiprotected' => true,
			'reupload' => true,
			'skipcaptcha' => true,
			'collectionsaveascommunitypage' => true,
			'collectionsaveasuserpage' => true,
		],
		'inactive' => [
			// for show only
		],
	],
],
# @} end of wgGroupOverrides2

# wgAddGroups @{
'wgAddGroups' => [
	// The '+' in front of the DB name means 'add to the default'. It saves us duplicating
	// changes to the default across all overrides --Andrew 2009-04-28
	'default' => [
		'bureaucrat' => [
			'accountcreator',
			'sysop',
			'interface-admin',
			'bureaucrat',
			'bot',
			'confirmed',
		],
		'sysop' => [ 'ipblock-exempt' ],
	],
	'+private' => [
		'bureaucrat' => [
			'import',
			'transwiki',
			'inactive',
		],
	],
	'+fishbowl' => [
		'bureaucrat' => [
			'import',
			'transwiki',
		],
	],
	'+testwiki' => [
		'bureaucrat' => [
			'researcher',
			'centralnoticeadmin',
			'flow-bot',
			'import',
			'transwiki',
		],
		'sysop' => [
			'filemover',
			'rollbacker',
			'accountcreator',
			'confirmed',
			'templateeditor',
			'extendedconfirmed', // T302860
			'patroller', // T354063
		],
	],
	'+test2wiki' => [
		'bureaucrat' => [
			'flow-bot',
			'import',
			'transwiki',
		],
		'qa_automation' => [ 'qa_automation' ], // For browser tests, T60375
	],
	// ******************************************************************
	'+apiportalwiki' => [ // T259569
		'bureaucrat' => [ 'docseditor' ],
	],
	'+arbcom_ruwiki' => [
		'bureaucrat' => [ 'arbcom' ]
	],
	'+arwiki' => [
		'bureaucrat' => [
			'import',
			'reviewer',
			'abusefilter',
		],
		'sysop' => [
			'uploader',
			'reviewer',
			'confirmed',
			'rollbacker',
			'abusefilter',
			'extendedmover', // T326434
		],
	],
	'+arwikibooks' => [
		'sysop' => [ 'rollbacker', ], // T185720
	],
	'+arwikinews' => [
		'sysop' => [ 'rollbacker', ], // T189206
	],
	'+arwikiquote' => [
		'sysop' => [ 'rollbacker', ], // T189732
	],
	'+arwikisource' => [
		'sysop' => [
			'patroller',
			'autopatrolled',
			'rollbacker',
		],
	],
	'+arwikiversity' => [
		'sysop' => [ 'rollbacker', ], // T188633
	],
	'+arwiktionary' => [
		'sysop' => [
			'autopatrolled',
			'rollbacker',
		],
	],
	'+arzwiki' => [
		'sysop' => [
			'rollbacker', // T258100
			'autopatrolled', // T260761
			'patroller', // T262218
		],
	],
	'+azbwiki' => [
		'bureaucrat' => [ 'interface-editor', ], // T109755
		'sysop' => [
			'abusefilter', // T109755
			'autopatrolled', // T109755
			'patroller', // T109755
			'rollbacker', // T109755
			'transwiki', // T109755
		],
	],
	'+azwiki' => [
		'sysop' => [
			'autopatrolled', // T196488
			'patroller', // T196488
			'rollbacker', // T215200
			'extendedconfirmed', // T281860
			'pagemover', // T303752
			'filemover', // T304968
		],
	],
	'+azwikibooks' => [
		'sysop' => [ 'autopatrolled', ], // T231493
	],
	'+azwikiquote' => [
		'sysop' => [ 'autopatrolled', ], // T300435
	],
	'+azwikisource' => [
		'sysop' => [ 'autopatrolled', ], // T229371
	],
	'+azwiktionary' => [
		'sysop' => [ 'autopatrolled', ], // T227208
	],
	'+betawikiversity' => [
		'bureaucrat' => [ 'test-sysop' ], // T240438
	],
	'+bewiki' => [
		'sysop' => [ 'autoeditor' ],
	],
	'+be_x_oldwiki' => [
		'sysop' => [ 'abusefilter' ],
	],
	'+bgwiki' => [
		'bureaucrat' => [ 'patroller' ],
		'sysop' => [
			'autopatrolled',
			'extendedconfirmed', // T269709
		],
	],
	'+bnwiki' => [
		'sysop' => [
			'autopatrolled',
			'filemover',
			'flood',
			'patroller', // T308945
			'rollbacker',
		],
		'bureaucrat' => [ 'import' ],
	],
	'+bnwikibooks' => [
		'sysop' => [
			'autopatrolled', // T296640
			'patroller', // T296640
		],
	],
	'+bnwikiquote' => [
		'sysop' => [
			'autopatrolled', // T335829
			'patroller', // T335829
		],
	],
	'+bnwikisource' => [
		'sysop' => [
			'autopatrolled',
			'filemover', // T129087
			'flood', // T129087
		],
	],
	'+bnwikivoyage' => [
		'sysop' => [
			'autopatrolled', // T296637
			'patroller', // T296637
		],
	],
	'+bnwiktionary' => [
		'sysop' => [
			'autopatrolled', // T298187
			'patroller', // T298187
		],
	],
	'+brwikimedia' => [
		'sysop' => [
			'autopatrolled', // T65345
			'confirmed', // T65345
			'translationadmin', // T60123
		],
	],
	'+bswiki' => [
		'sysop' => [ 'flood' ], // T52425
		'bureaucrat' => [
			'patroller',
			'autopatrolled',
			'rollbacker',
		],
	],
	'+cawiki' => [
		'sysop' => [
			'rollbacker',
			'autopatrolled',
			'abusefilter',
			'accountcreator', // T58570
		],
	],
	'+cawikinews' => [
		'sysop' => [
			'flood', // T98576
			'confirmed', // T138069
		],
	],
	'+cewiki' => [
		'sysop' => [
			'rollbacker', // T128205
			'suppressredirect', // T128205
			'uploader', // T129005
		],
	],
	'+ckbwiki' => [
		'sysop' => [
			'rollbacker', // T53312
			'autopatrolled', // T53328
			'uploader', // T53232
			'confirmed', // T53715
			'flood', // T53803
			'suppressredirect', // T69278
			'patroller', // T285221
		],
		'bureaucrat' => [
			'botadmin', // T54578
			'import', // T54633
			'transwiki', // T54633
			'interface-editor', // T54866
		],
	],
	'+commonswiki' => [
		'bureaucrat' => [
			'ipblock-exempt',
		],
		'checkuser' => [ 'ipblock-exempt' ],
		'sysop' => [
			'rollbacker',
			'confirmed',
			'patroller',
			'autopatrolled',
			'filemover',
			'image-reviewer',
			'upwizcampeditors',
			'templateeditor', // T227420
		],
		'image-reviewer' => [ 'image-reviewer' ],
	],
	'+cowikimedia' => [
		'sysop' => [
			'confirmed', // T300948
			'accountcreator', // T300948
		],
	],
	'+cswiki' => [
		'bureaucrat' => [
			'arbcom', // T63418
			'autopatrolled',
		],
		'sysop' => [
			'rollbacker', // T126931
			'patroller', // T126931
			'accountcreator', // T131684
			'confirmed',
			'extendedconfirmed', // T316283
		],
	],
	'+cswikinews' => [
		'bureaucrat' => [ 'autopatrolled' ],
	],
	'+cswikiquote' => [
		'bureaucrat' => [ 'autopatrolled' ],
	],
	'+cswikisource' => [
		'bureaucrat' => [ 'autopatrolled' ],
	],
	'+cswiktionary' => [
		'bureaucrat' => [ 'autopatrolled' ],
	],
	'+dawiki' => [
		'sysop' => [
			'patroller',
			'autopatrolled',
		],
	],
	'+dawikiquote' => [
		'sysop' => [ 'autopatrolled' ], // T88591
	],
	'+dawiktionary' => [
		'sysop' => [ 'autopatrolled' ], // T86062
	],
	'+dewiki' => [
		'bureaucrat' => [
			'noratelimit', // T59819
			'import', // T331921
		],
	],
	'+dewikivoyage' => [
		'sysop' => [ 'autopatrolled' ], // T67495
	],
	'+donatewiki' => [
		'sysop' => [
			'inactive',
			'flood',
		],
	],
	'+dtywiki' => [
		'sysop' => [
			'autopatrolled', // T176709
			'transwiki', // T174226
		],
	],
	'+elwiki' => [
		'sysop' => [ 'rollbacker', 'extendedconfirmed' ], // T257745, T306241
	],
	'+elwiktionary' => [
		'bureaucrat' => [ 'interface-editor' ],
		'sysop' => [
			'autopatrolled',
			'rollbacker', // T255569
		],
	],
	'+enwiki' => [
		'eventcoordinator' => [ 'confirmed' ], // T193075
		'sysop' => [
			'abusefilter',
			'abusefilter-helper', // T175684
			'accountcreator',
			'autoreviewer',
			'confirmed',
			'eventcoordinator', // T193075
			'filemover',
			'reviewer',
			'rollbacker',
			'templateeditor',
			'massmessage-sender',
			'extendedconfirmed', // T126607
			'extendedmover', // T133981
			'patroller', // T149019
		],
	],
	'+enwikibooks' => [
		'sysop' => [
			'import', // T278683
			'transwiki',
			'uploader',
			'flood', // T285594
		],
	],
	'+enwikinews' => [
		'bureaucrat' => [ 'flood' ],
		'sysop' => [ 'flood' ],
	],
	'+enwikiquote' => [
		'sysop' => [ 'rollbacker' ], // T310950
	],
	'+enwikisource' => [
		'bureaucrat' => [
			'autopatrolled',
			'flood', // T38863
		],
		'sysop' => [
			'abusefilter',
			'autopatrolled',
			'upload-shared',
		],
	],
	'+enwikiversity' => [
		'sysop' => [ 'curator' ], // T113109
		'bureaucrat' => [ 'import' ], // T294930
	],
	'+enwikivoyage' => [
		'sysop' => [
			'autopatrolled',
			'templateeditor',
			'patroller', // T222008
		],
		'bureaucrat' => [ 'patroller' ],
	],
	'+enwiktionary' => [
		'sysop' => [
			'autopatrolled',
			'flood',
			'patroller',
			'rollbacker',
			'templateeditor', // T148007
			'extendedmover', // T212662
		],
	],
	'+eswiki' => [
		'bureaucrat' => [
			'abusefilter', // T262174
			'rollbacker',
			'templateeditor', // T330470
			'botadmin', // T342484
		],
		'sysop' => [
			'rollbacker',
			'autopatrolled',
			'patroller',
		],
	],
	'+eswikibooks' => [
		'bureaucrat' => [ 'flood' ], // T93371
		'sysop' => [
			'rollbacker',
			'patroller', // T93371
			'autopatrolled', // T93371
		],
	],
	'+eswikinews' => [
		'bureaucrat' => [
			'editprotected',
			'flood',
		],
	],
	'+eswikiquote' => [
		'sysop' => [
			'autopatrolled',
			'confirmed', // T64911
			'patroller', // T64911
			'rollbacker', // T64911
		],
	],
	'+eswikisource' => [
		'sysop' => [ 'autopatrolled' ], // T69557
	],
	'+eswikivoyage' => [
		'sysop' => [
			'rollbacker', // T46285
			'patroller', // T46285
			'autopatrolled', // T57665
		],
	],
	'+eswiktionary' => [
		'bureaucrat' => [
			'autopatrolled',
			'patroller',
			'rollbacker',
		],
	],
	'+etwiki' => [
		'sysop' => [ 'autopatrolled' ], // T150852
	],
	'+fawiki' => [
		'bureaucrat' => [
			'image-reviewer', // T66532
			'botadmin', // T71411
			'templateeditor', // T74146
			'abusefilter', // T74502
			'eliminator', // T87558
		],
		'sysop' => [
			'rollbacker', // T25233
			'autopatrolled', // T31007, T144699
			'confirmed', // T87348
			'patroller', // T118847
			'extendedconfirmed', // T140839
			'eliminator', // T162396
			'extendedmover', // T299038
		]
	],
	'+fawikibooks' => [
		'sysop' => [
			'autopatrolled', // T111024
			'patroller', // T111024
			'rollbacker', // T111024
		],
	],
	'+fawikinews' => [
		'sysop' => [
			'rollbacker',
			'patroller',
		],
	],
	'+fawikiquote' => [
		'sysop' => [
			'autopatrolled', // T56951
			'rollbacker', // T56951
		],
	],
	'+fawikisource' => [
		'sysop' => [
			'autopatrolled', // T187662
			'patroller', // T187662
			'rollbacker', // T161946
		],
	],
	'+fawikivoyage' => [
		'sysop' => [
			'autopatrolled', // T73760
			'confirmed', // T73760
			'flood', // T73760
			'patroller', // T73760
			'rollbacker', // T73760
			'transwiki', // T73681
		],
	],
	'+fawiktionary' => [
		'sysop' => [
			'autopatrolled', // T85381
			'patroller', // T85381
			'rollbacker', // T85381
		],
	],
	'+fiwiki' => [
		'bureaucrat' => [ 'arbcom' ],
		'sysop' => [
			'rollbacker',
			'accountcreator', // T149986
		],
	],
	'+fiwiktionary' => [
		'sysop' => [
			'rollbacker', // T323063
		],
	],
	'+foundationwiki' => [
		'sysop' => [
			'flood',
			'editor',
		],
	],
	'+frwiki' => [
		'sysop' => [ 'rollbacker' ], // T170780
		'bureaucrat' => [
			'abusefilter',
			'rollbacker',
		],
	],
	'+frwikibooks' => [
		'bureaucrat' => [ 'abusefilter' ],
		'sysop' => [ 'patroller' ],
	],
	'+frwikinews' => [
		'sysop' => [
			'trusteduser', // T90979
			'facilitator', // T90979
		],
	],
	'+frwikisource' => [
		'sysop' => [
			'patroller',
			'autopatrolled'
		],
	],
	'+frwikiversity' => [
		'sysop' => [ 'patroller' ],
	],
	'+frwikivoyage' => [
		'sysop' => [ 'patroller' ],
	],
	'+frwiktionary' => [
		'bureaucrat' => [
			'patroller',
			'transwiki',
			'autopatrolled',
			'confirmed',
			'abusefilter',
			'botadmin',
		],
	],
	'+gawiki' => [
		'sysop' => [ 'rollbacker' ],
	],
	'+ganwiki' => [
		'sysop' => [ 'transwiki' ], // T354850
	],
	'+glwiki' => [
		'sysop' => [ 'confirmed' ], // T128948
	],
	'+guwiki' => [
		'sysop' => [
			'autopatrolled', // T119787
			'rollbacker', // T119787
			'transwiki', // T120346
		],
	],
	'+hewiki' => [
		'sysop' => [
			'patroller',
			'autopatrolled',
			'accountcreator',
			'templateeditor', // T296769
		],
	],
	'+hewikibooks' => [
		'sysop' => [
			'patroller',
			'autopatrolled',
		],
	],
	'+hewikinews' => [
		'sysop' => [
			'patroller', // T140544
			'autopatrolled', // T140544
		],
	],
	'+hewikiquote' => [
		'sysop' => [ 'autopatrolled' ],
	],
	'+hewikisource' => [
		'bureaucrat' => [
			'import', // T274796
			'transwiki', // T274796
		],
		'sysop' => [ 'reviewer' ], // T274796
	],
	'+hewikivoyage' => [
		'sysop' => [ 'autopatrolled' ], // T52377
	],
	'+hewiktionary' => [
		'sysop' => [
			'patroller',
			'autopatrolled',
		],
	],
	'+hiwiki' => [
		'sysop' => [
			'abusefilter', // T37355
			'autopatrolled', // T37355
			'reviewer', // T37355
			'rollbacker', // T56589
			'filemover', // T56589
			'templateeditor', // T120342
		],
	],
	'+hiwikiversity' => [
		'sysop' => [ 'autopatrolled' ], // T179251
	],
	'+hrwiki' => [
		'bureaucrat' => [
			'patroller',
			'autopatrolled',
		],
		'sysop' => [
			'patroller',
			'autopatrolled',
			'flood', // T276560
		],
	],
	'+huwiki' => [
		'bureaucrat' => [
			'templateeditor', // T74055
			'interface-editor', // T109408
		],
	],
	'+idwiki' => [
		'sysop' => [ 'rollbacker' ],
		'bureaucrat' => [ 'rollbacker' ],
	],
	'+idwikimedia' => [
		'sysop' => [ 'import' ], // T192726
	],
	'+id_internalwikimedia' => [
		'sysop' => [ 'import' ],
	],
	'+incubatorwiki' => [
		'bureaucrat' => [
			'import',
			'test-sysop',
			'translator',
		],
	],
	'+itwiki' => [
		'bureaucrat' => [
			'rollbacker',
			'botadmin', // T220915
			'mover', // T243503
		],
		'sysop' => [
			'accountcreator', // T63109
			'autopatrolled',
			'flood',
		],
	],
	'+itwikibooks' => [
		'sysop' => [
			'autopatrolled',
			'accountcreator', // T206447
			'confirmed', // T206447
			'patroller',
		],
		'accountcreator' => [ 'confirmed' ], // T206447
	],
	'+itwikinews' => [
		'sysop' => [
			'autopatrolled', // T142571
			'rollbacker', // T142571
		],
		'bureaucrat' => [ 'closer' ], // T257927
	],
	'+itwikiquote' => [
		'sysop' => [ 'autopatrolled' ], // T64200
	],
	'+itwikiversity' => [
		'sysop' => [
			'autopatrolled', // T114930
			'patroller', // T114930
			'flood', // T114930
			'accountcreator', // T158062
		],
	],
	'+itwikivoyage' => [
		'sysop' => [
			'autopatrolled', // T45327
			'patroller', // T47638
		],
	],
	'+itwiktionary' => [
		'sysop' => [
			'patroller',
			'autopatrolled',
		],
	],
	'+jawiki' => [
		'sysop' => [
			'abusefilter',
			'extendedconfirmed', // T249820
			'rollbacker', // T258339
		],
		'bureaucrat' => [ 'eliminator' ], // T258339
	],
	'+jawiktionary' => [
		'sysop' => [ 'autopatrolled' ], // T63366
	],
	'+kawiki' => [
		'sysop' => [ 'trusted' ],
	],
	'+kkwiki' => [
		'sysop' => [ 'rollbacker' ], // T130215
	],
	'+knwiki' => [
		'sysop' => [ 'flood' ], // T322472
	],
	'+kowiki' => [
		'sysop' => [
			'rollbacker',
			'confirmed',
			'uploader', // T85621
			'autopatrolled', // T130808
			'extendedconfirmed', // T184675
		],
	],
	'+kswiki' => [
		'sysop' => [ 'rollbacker', 'uploader' ], // T286789, T305320
	],
	'+ladwiki' => [
		'sysop' => [ 'flood' ], // T131527
		'bureaucrat' => [ 'flood' ], // T131527
	],
	'+legalteamwiki' => [
		'bureaucrat' => [ 'ipblock-exempt' ], // T63222
	],
	'+lijwiki' => [
		'sysop' => [
			'mover', // T256109
			'rollbacker', // T256109
		],
	],
	'+lmowiktionary' => [
		'sysop' => [ 'extendedmover' ], // T327340
	],
	'+ltwiki' => [
		'sysop' => [ 'abusefilter' ],
	],
	'+ltwiktionary' => [
		'sysop' => [ 'abusefilter' ],
	],
	'+lvwiki' => [
		'sysop' => [
			'autopatrolled', // T72441
			'patroller', // T72441
			'flood', // T121238
		],
	],
	'+maiwiki' => [
		'sysop' => [
			'autopatrolled', // T89346
			'patroller', // T118934
			'rollbacker', // T118934
			'import', // T99491
			'accountcreator', // T126950
		],
	],
	'+mediawikiwiki' => [
		'sysop' => [
			'autopatrolled',
			'uploader', // T217523
		],
		'bureaucrat' => [
			'autopatrolled',
			'transwiki',
			'import',
		],
	],
	'+metawiki' => [
		'bureaucrat' => [
			'centralnoticeadmin',
			'flood', // T48639
			'uploader',
		],
		'sysop' => [
			'autopatrolled',
			'event-organizer', // T356070
			'massmessage-sender', // T59611
			'patroller', // T176079
		],
	],
	'+mkwiki' => [
		'bureaucrat' => [
			'patroller',
			'autopatrolled',
			'autoreviewed',
		],
	],
	'+mlwiki' => [
		'sysop' => [
			'patroller',
			'autopatrolled',
			'rollbacker',
		],
		'bureaucrat' => [ 'botadmin' ],
	],
	'+mlwikisource' => [
		'sysop' => [
			'patroller',
			'autopatrolled',
		],
	],
	'+mrwiki' => [
		'sysop' => [ 'rollbacker' ], // T270864
	],
	'+mrwikisource' => [
		'sysop' => [
			'templateeditor', // T269067
			'patroller', // T269067
		],
	],
	'+mywiki' => [
		'sysop' => [
			'autopatrolled', // T341026
			'patroller', // T341026
		],
	],
	'+mznwiki' => [
		'sysop' => [ 'uploader' ],
	],
	'+newiki' => [
		'sysop' => [
			'autopatrolled', // T89816
			'rollbacker', // T90888
			'patroller', // T95101, T327114
			'abusefilter', // T95102
			'filemover', // T95103
			'import', // T100925
			'templateeditor', // T195557
			'flood', // T211181
			'transwiki', // T214036
		],
	],
	'+nlwiki' => [
		'bureaucrat' => [
			'abusefilter',
			'arbcom',
			'extendedconfirmed', // T329642
			'rollbacker',
		],
	],
	'+nlwikibooks' => [
		'sysop' => [ 'abusefilter' ],
	],
	'+nlwikivoyage' => [
		'bureaucrat' => [ 'autopatrolled' ], // T46082
	],
	'+nnwiki' => [
		'bureaucrat' => [
			'autopatrolled',
			'patroller',
		],
		'sysop' => [
			'autopatrolled',
			'patroller',
			'transwiki', // T231761
		],
	],
	'+nowiki' => [
		'bureaucrat' => [
			'patroller',
			'autopatrolled',
		],
		'sysop' => [
			'patroller',
			'autopatrolled',
			'abusefilter',
			'transwiki',
			'confirmed',
		],
	],
	'+nowikibooks' => [
		'bureaucrat' => [
			'sysop',
			'bureaucrat',
			'bot',
			'patroller',
			'autopatrolled',
		],
		'sysop' => [
			'autopatrolled',
			'patroller',
		],
	],
	'+nowikimedia' => [
		'sysop' => [ 'translationadmin' ], // T152490
	],
	'+nowiktionary' => [
		'sysop' => [ 'transwiki' ], // T172365
	],
	'+officewiki' => [
		'bureaucrat' => [
			'flood',
			'securepoll',
		],
	],
	'+orwiki' => [
		'sysop' => [ 'rollbacker' ],
	],
	'+pawiki' => [
		'sysop' => [
			'patroller', // T120369
			'autopatrolled', // T120369
			'transwiki', // T120369
		],
	],
	'+pawikisource' => [
		'sysop' => [ 'validator' ], // T341428
	],
	'+plwiki' => [
		'bureaucrat' => [
			'abusefilter',
			'flood',
			'arbcom', // T256572
		],
	],
	'+plwikiquote' => [
		'sysop' => [ 'patroller' ], // T30479
	],
	'+pswiki' => [
		'bureaucrat' => [ 'interface-editor' ], // T133472
	],
	'+ptwiki' => [
		'bureaucrat' => [
			'rollbacker', // T212735
			'eliminator',
			'autoreviewer',
			'interface-editor', // T41905
			'flood', // T228521
		],
		'sysop' => [
			'rollbacker',
			'autoreviewer',
			'confirmed',
			'accountcreator', // T65750
			'flood', // T228521
			'extendedconfirmed', // T281926
		],
	],
	'+ptwikinews' => [
		'sysop' => [
			'reviewer',
			'editprotected', // T162577
		],
	],
	'+ptwikisource' => [
		'sysop' => [ // T331762
			'patroller',
			'autopatrolled',
			'rollbacker',
		],
	],
	'+ptwikivoyage' => [
		'sysop' => [
			'accountcreator', // T292806
			'autopatrolled', // T168981
			'confirmed', // T292806
			'transwiki', // T292806
		],
	],
	'+quwiki' => [
		'sysop' => [ 'rollbacker' ],
	],
	'+rowiki' => [
		'sysop' => [
			'autopatrolled',
			'extendedconfirmed', // T254471
			'templateeditor', // T63172
			'patroller', // T231099
		],
		'bureaucrat' => [ 'abusefilter' ], // T28634
	],
	'+rswikimedia' => [
		'sysop' => [ 'import' ], // T109613
	],
	'+ruewiki' => [
		'sysop' => [ 'rollbacker' ], // T264147
	],
	'+ruwiki' => [
		'sysop' => [
			'rollbacker',
			'uploader',
			'closer',
			'filemover',
			'suppressredirect',
			'confirmed', // T334780
		],
		'bureaucrat' => [
			'arbcom', // T51334
			'engineer', // T144599
		],
	],
	'+ruwikinews' => [
		'sysop' => [ 'rollbacker' ], // T201265
	],
	'+ruwikiquote' => [
		'sysop' => [
			'autoeditor',
			'rollbacker', // T200201
		],
	],
	'+ruwikisource' => [
		'bureaucrat' => [
			'autoeditor',
			'rollbacker',
		],
		'sysop' => [
			'autoeditor',
			'rollbacker',
			'abusefilter',
			'flood',
		],
	],
	'+ruwikivoyage' => [
		'sysop' => [
			'autopatrolled', // T48915
			'rollbacker', // T116143
			'patroller', // T116143
			'filemover', // T116143
		],
	],
	'+ruwiktionary' => [
		'sysop' => [ 'rollbacker' ], // T183655
	],
	'+sawiki' => [
		'sysop' => [
			'autopatrolled',
			'patroller', // T117314
		],
	],
	'+sawikisource' => [
		'sysop' => [ 'autopatrolled' ],
	],
	'+scowiki' => [
		'sysop' => [
			'rollbacker',
			'autopatrolled',
			'flood', // T111753
		],
	],
	'+sdwiki' => [
		'sysop' => [ 'autopatrolled' ], // T177141
	],
	'+shwiki' => [
		'bureaucrat' => [
			'autopatrolled', // T52802
			'filemover', // T52802
			'patroller', // T52802
			'rollbacker', // T52802
			'flood', // T54273
		],
		'sysop' => [
			'autopatrolled', // T52802
			'filemover', // T52802
			'patroller', // T52802
			'rollbacker', // T52802
			'flood', // T54273
		],
	],
	'+shwiktionary' => [
		'sysop' => [ 'autopatrolled' ],
		'bureaucrat' => [ 'autopatrolled' ],
	],
	'+simplewiki' => [
		'bureaucrat' => [
			'rollbacker',
			'transwiki',
			'patroller',
		],
		'sysop' => [
			'rollbacker',
			'flood',
			'patroller',
			'uploader', // T127826
		],
	],
	'+simplewiktionary' => [
		'sysop' => [
			'rollbacker',
			'autopatrolled',
		],
	],
	'+siwiki' => [
		'sysop' => [
			'rollbacker',
			'accountcreator',
			'abusefilter',
			'autopatrolled',
			'confirmed',
			'reviewer',
		],
	],
	'+skwiki' => [
		'sysop' => [ 'rollbacker' ],
	],
	'+specieswiki' => [
		'sysop' => [
			'autopatrolled', // T89147
			'patroller', // T89147
		],
	],
	'+sqwiki' => [
		'bureaucrat' => [ 'editor' ],
		'sysop' => [ 'reviewer' ],
	],
	'+srwiki' => [
		'bureaucrat' => [
			'patroller',
			'autopatrolled',
			'rollbacker',
			'flood',
		],
		'sysop' => [ 'rollbacker' ],
	],
	'+srwikibooks' => [
		'bureaucrat' => [
			'patroller', // T209250
			'autopatrolled', // T209250
			'rollbacker', // T209250
		],
		'sysop' => [ 'rollbacker' ], // T209250
	],
	'+srwikinews' => [
		'bureaucrat' => [
			'patroller', // T209251
			'autopatrolled', // T209251
			'rollbacker', // T209251
		],
		'sysop' => [ 'rollbacker' ], // T209251
	],
	'+srwikiquote' => [
		'bureaucrat' => [
			'patroller', // T206936
			'autopatrolled', // T206936
			'rollbacker', // T206936
		],
		'sysop' => [ 'rollbacker' ], // T206936
	],
	'+srwikisource' => [
		'bureaucrat' => [
			'patroller',
			'autopatrolled',
			'rollbacker', // T206935
		],
		'sysop' => [ 'rollbacker' ], // T206935
	],
	'+srwiktionary' => [
		'bureaucrat' => [
			'patroller', // T209252
			'autopatrolled', // T209252
			'rollbacker', // T209252
		],
		'sysop' => [ 'rollbacker' ], // T209252
	],
	'+svwiki' => [
		'sysop' => [
			'rollbacker',
			'autopatrolled', // T161210
			'extendedconfirmed', // T279836
		],
	],
	'+svwikisource' => [
		'sysop' => [ 'autopatrolled', ],
	],
	'+svwikivoyage' => [
		'sysop' => [
			'autopatrolled', // T93339
			'patroller', // T93339
			'rollbacker', // T93339
		],
	],
	'+svwiktionary' => [
		'sysop' => [ 'autopatrolled', ], // T161919
	],
	'+tawiki' => [
		'bureaucrat' => [ 'nocreate' ],
		'sysop' => [
			'patroller', // T95180
			'rollbacker', // T95180
			'autopatrolled', // T95180
		],
	],
	'+trwiki' => [
		'sysop' => [
			'patroller', // T40690
			'massmessage-sender', // T147740
		],
		'bureaucrat' => [ 'interface-editor' ], // T41690
	],
	'+trwikivoyage' => [
		'sysop' => [ 'rollbacker' ], // T314678
	],
	'+thwiki' => [
		'sysop' => [
			'uploader', // T216615
			'patroller', // T272149
		],
	],
	'+ukwiki' => [
		'sysop' => [
			'patroller',
			'rollbacker',
			'accountcreator', // T104034
			'filemover', // T119636
			'flood', // T319243
		],
	],
	'+ukwikivoyage' => [
		'sysop' => [
			'rollbacker',
			'uploader',
			'autopatrolled', // T56229
		],
	],
	'+ukwiktionary' => [
		'bureaucrat' => [ 'autoeditor' ],
		'sysop' => [ 'autoeditor' ],
	],
	'+urwiki' => [
		'bureaucrat' => [
			'import', // T44737
			'abusefilter', // T47643
			'rollbacker', // T47643
			'massmessage-sender', // T144701
			'interface-editor', // T133564
			'eliminator', // T184607
		],
		'sysop' => [
			'confirmed', // T44737
			'abusefilter', // T47643
			'rollbacker', // T47643
			'accountcreator', // T137888
			'filemover', // T137888
			'autopatrolled', // T139302
			'massmessage-sender', // T144701
			'extendedmover', // T211978
			'transwiki', // T212612
			'patroller', // T301491
			'eliminator', // T307029
		],
	],
	'+uzwiki' => [
		'sysop' => [
			'rollbacker', // T265509
			'eliminator', // T302670
			'patroller', // T338826
		],
	],
	'+vecwiki' => [
		'sysop' => [ 'flood' ],
	],
	'+viwiki' => [
		'sysop' => [
			'rollbacker',
			'flood',
			'patroller', // T48828
			'autopatrolled', // T48828
			'extendedconfirmed', // T215493
			'templateeditor', // T296154
			'uploader', // T303577
		],
		'bureaucrat' => [
			'eliminator', // T70612
			'flood',
		],
	],
	'+viwikibooks' => [
		'bureaucrat' => [
			'eliminator', // T202207
		],
	],
	'+viwiktionary' => [
		'sysop' => [ 'rollbacker' ], // T176979
	],
	'+votewiki' => [
		'bureaucrat' => [
			'electcomm', // T74589
			'staffsupport', // T74589
		],
		'staffsupport' => [ 'electionadmin' ], // T74589
		'electcomm' => [ 'electionadmin' ], // T74589
	],
	'+wikidata' => [
		'sysop' => [
			'rollbacker', // T47165
			'confirmed', // T47124
			'propertycreator', // T48953
		],
		'bureaucrat' => [
			'flood', // T50013
			'wikidata-staff', // T74459
		],
		'wikidata-staff' => [ 'wikidata-staff', ], // T74459
	],
	'+wikifunctionswiki' => [
		'sysop' => [
			'confirmed', // T344261
			'autopatrolled', // T343946
			'functioneer', // T352495
		],
		'bureaucrat' => [ 'wikifunctions-staff' ],
		'wikifunctions-staff' => [ 'functioneer', 'functionmaintainer', 'sysop', 'bureaucrat', 'wikifunctions-staff' ],
	],
	'+wikimaniawiki' => [
		'sysop' => [ 'uploader' ], // T225505
	],
	'wikitech' => [
		'contentadmin' => [
			'autopatrolled',
			'ipblock-exempt',
		],
		'bureaucrat' => [
			'bot',
			'bureaucrat',
			'confirmed',
			'contentadmin',
			'interface-admin',
			'oathauth',
			'oauthadmin',
			'sysop',
			'transwiki',
		],
		'sysop' => [
			'autopatrolled',
			'ipblock-exempt',
		],
	],
	'+wuuwiki' => [
		'sysop' => [ 'rollbacker' ],
	],
	'+yowiki' => [
		'sysop' => [
			'accountcreator', // T249487
			'rollbacker', // T249487
		],
	],
	'+zhwiki' => [
		'accountcreator' => [ 'eventparticipant' ], // T198167
		'bureaucrat' => [ 'flood' ],
		'ipblock-exempt-grantor' => [ 'ipblock-exempt' ], // T357991
		'sysop' => [
			'abusefilter-helper', // T344398
			'patroller',
			'rollbacker',
			'autoreviewer',
			'confirmed',
			'massmessage-sender', // T130814
			'filemover', // T195247
			'eventparticipant', // T198167
			'transwiki', // T250972
			'templateeditor', // T260012
			'ipblock-exempt-grantor', // T357991
		],
	],
	'+zhwikibooks' => [
		'sysop' => [
			'flood', // T185182
			'transwiki', // T313657
		],
	],
	'+zhwikinews' => [
		'sysop' => [
			'rollbacker',
			'flood', // T54546
			'transwiki', // T273405
		],
	],
	'+zhwikiquote' => [
		'sysop' => [
			'flood', // T189289
			'transwiki', // T313657
		],
	],
	'+zhwikisource' => [
		'sysop' => [
			'transwiki', // T313657
		],
	],
	'+zhwikiversity' => [
		'sysop' => [
			'transwiki', // T201328
			'patroller', // T202599
			'autopatrolled', // T202599
			'flood', // T202599
			'confirmed', // T202599
		],
	],
	'+zhwikivoyage' => [
		'sysop' => [
			'autopatrolled', // T62328
			'confirmed', // T62085
			'patroller', // T62328
			'transwiki', // T62085
		],
	],
	'+zhwiktionary' => [
		'sysop' => [
			'flood', // T187018
			'templateeditor', // T286101
			'transwiki', // T313657
		],
	],
	'+zh_yuewiki' => [
		'sysop' => [
			'abusefilter',
			'rollbacker',
			'autoreviewer',
			'confirmed',
		],
	],
],
# @} end of wgAddGroups

# wgRemoveGroups @{
'wgRemoveGroups' => [
	// The '+' in front of the DB name means 'add to the default'. It saves us duplicating
	// changes to the default across all overrides --Andrew 2009-04-28
	'default' => [
		'bureaucrat' => [
			'accountcreator',
			'bot',
			'confirmed',
			'interface-admin',
		],
		'sysop' => [ 'ipblock-exempt' ],
	],
	'+private' => [
		'bureaucrat' => [
			'sysop',
			'bureaucrat',
			'import',
			'transwiki',
			'inactive',
		],
	],
	'+fishbowl' => [
		'bureaucrat' => [
			'sysop',
			'bureaucrat',
			'import',
			'transwiki',
		],
	],
	'+testwiki' => [
		'bureaucrat' => [
			'sysop',
			'researcher',
			'centralnoticeadmin',
			'flow-bot',
			'import',
			'transwiki',
		],
		'sysop' => [
			'filemover',
			'rollbacker',
			'accountcreator',
			'confirmed',
			'templateeditor',
			'extendedconfirmed', // T302860
			'patroller', // T354063
		],
	],
	'+test2wiki' => [
		'bureaucrat' => [
			'flow-bot',
			'sysop', // T131037
			'import',
			'transwiki',
		],
	],
	// ******************************************************************
	'+apiportalwiki' => [
		'bureaucrat' => [ 'docseditor' ], // T259569
	],
	'+arbcom_ruwiki' => [
		'bureaucrat' => [ 'arbcom' ]
	],
	'+arwiki' => [
		'bureaucrat' => [
			'import',
			'reviewer',
			'abusefilter',
		],
		'sysop' => [
			'uploader',
			'reviewer',
			'confirmed',
			'rollbacker',
			'abusefilter',
			'patroller',
			'autopatrolled',
			'extendedmover', // T326434
		],
	],
	'+arwikibooks' => [
		'sysop' => [ 'rollbacker', ], // T185720
	],
	'+arwikinews' => [
		'sysop' => [ 'rollbacker', ], // T189206
	],
	'+arwikiquote' => [
		'sysop' => [ 'rollbacker', ], // T189732
	],
	'+arwikisource' => [
		'sysop' => [
			'patroller',
			'autopatrolled',
			'rollbacker',
		],
	],
	'+arwikiversity' => [
		'sysop' => [ 'rollbacker', ], // T188633
	],
	'+arwiktionary' => [
		'sysop' => [
			'autopatrolled',
			'rollbacker',
		],
	],
	'+arzwiki' => [
		'sysop' => [
			'rollbacker', // T258100
			'autopatrolled', // T260761
			'patroller', // T262218
		],
	],
	'+azbwiki' => [
		'bureaucrat' => [
			'interface-editor', // T109755
		],
		'sysop' => [
			'abusefilter', // T109755
			'autopatrolled', // T109755
			'patroller', // T109755
			'rollbacker', // T109755
			'transwiki', // T109755
		],
	],
	'+azwiki' => [
		'sysop' => [
			'autopatrolled', // T196488
			'patroller', // T196488
			'rollbacker', // T215200
			'extendedconfirmed', // T281860
			'pagemover', // T303752
			'filemover', // T304968
		],
	],
	'+azwikibooks' => [
		'sysop' => [ 'autopatrolled', ], // T231493
	],
	'+azwikiquote' => [
		'sysop' => [ 'autopatrolled', ], // T300435
	],
	'+azwikisource' => [
		'sysop' => [ 'autopatrolled', ], // T229371
	],
	'+azwiktionary' => [
		'sysop' => [ 'autopatrolled', ], // T227208
	],
	'+bawiki' => [
		'bureaucrat' => [ 'sysop' ],
	],
	'+bdwikimedia' => [
		'bureaucrat' => [ 'sysop' ], // T251078
	],
	'+betawikiversity' => [
		'bureaucrat' => [ 'test-sysop' ], // T240438
	],
	'+bewiki' => [
		'sysop' => [ 'autoeditor' ],
	],
	'+be_x_oldwiki' => [
		'sysop' => [ 'abusefilter' ],
	],
	'+bgwiki' => [
		'bureaucrat' => [ 'patroller' ],
		'sysop' => [ 'autopatrolled' ],
	],
	'+bnwiki' => [
		'sysop' => [
			'autopatrolled',
			'filemover',
			'flood',
			'patroller', // T308945
			'rollbacker',
		],
		'bureaucrat' => [ 'import' ],
	],
	'+bnwikibooks' => [
		'sysop' => [
			'autopatrolled', // T296640
			'patroller', // T296640
		],
	],
	'+bnwikiquote' => [
		'sysop' => [
			'autopatrolled', // T335829
			'patroller', // T335829
		],
	],
	'+bnwikisource' => [
		'sysop' => [
			'autopatrolled',
			'filemover', // T129087
			'flood', // T129087
		],
	],
	'+bnwikivoyage' => [
		'sysop' => [
			'autopatrolled', // T296637
			'patroller', // T296637
		],
	],
	'+bnwiktionary' => [
		'sysop' => [
			'autopatrolled', // T298187
			'patroller', // T298187
		],
	],
	'+brwikimedia' => [
		'sysop' => [
			'translationadmin', // T60123
			'autopatrolled', // T65345
			'confirmed', // T65345
		],
		'bureaucrat' => [
			'sysop', // T65345
			'bureaucrat', // T65345
		],
	],
	'+bswiki' => [
		'sysop' => [ 'flood' ], // T52425
		'bureaucrat' => [
			'patroller',
			'autopatrolled',
			'rollbacker',
		],
	],
	'+cawiki' => [
		'sysop' => [
			'rollbacker',
			'autopatrolled',
			'abusefilter',
			'accountcreator', // T58570
		],
	],
	'+cawikinews' => [
		'sysop' => [
			'flood', // T98576
			'confirmed', // T138069
		],
	],
	'+cewiki' => [
		'sysop' => [
			'rollbacker', // T128205
			'suppressredirect', // T128205
			'uploader', // T129005
		],
	],
	'+ckbwiki' => [
		'sysop' => [
			'rollbacker', // T53312
			'autopatrolled', // T53328
			'uploader', // T53232
			'confirmed', // T53715
			'flood', // T53803
			'suppressredirect', // T69278
			'patroller', // T285221
		],
		'bureaucrat' => [
			'botadmin', // T54578
			'import', // T54633
			'transwiki', // T54633
			'interface-editor', // T54866
		],
	],
	'+commonswiki' => [
		'bureaucrat' => [
			'ipblock-exempt',
			'sysop', // T261481
		],
		'checkuser' => [ 'ipblock-exempt' ],
		'sysop' => [
			'rollbacker',
			'confirmed',
			'patroller',
			'autopatrolled',
			'filemover',
			'image-reviewer',
			'upwizcampeditors',
			'templateeditor', // T227420
		],
	],
	'+cowikimedia' => [
		'bureaucrat' => [
			'bureaucrat', // T300779
			'sysop', // T300779
		],
	],
	'+cswiki' => [
		'bureaucrat' => [
			'arbcom', // T63418
			'autopatrolled',
		],
		'sysop' => [
			'rollbacker', // T126931
			'patroller', // T126931
			'accountcreator', // T131684
			'confirmed', // T163206
			'extendedconfirmed', // T316283
		],
	],
	'+cswikinews' => [
		'bureaucrat' => [ 'autopatrolled' ],
	],
	'+cswikiquote' => [
		'bureaucrat' => [ 'autopatrolled' ],
	],
	'+cswikisource' => [
		'bureaucrat' => [ 'autopatrolled' ],
	],
	'+cswiktionary' => [
		'bureaucrat' => [ 'autopatrolled' ],
	],
	'+dawiki' => [
		'sysop' => [
			'patroller',
			'autopatrolled',
		],
	],
	'+dawikiquote' => [
		'sysop' => [ 'autopatrolled' ], // T88591
	],
	'+dawiktionary' => [
		'sysop' => [ 'autopatrolled' ], // T86062
	],
	'+dewiki' => [
		'bureaucrat' => [
			'noratelimit', // T59819
			'import', // T331921
			'sysop', // T331921
		],
	],
	'+dewikivoyage' => [
		'sysop' => [ 'autopatrolled' ], // T67495
	],
	'+donatewiki' => [
		'sysop' => [
			'inactive',
			'confirmed',
			'flood',
		],
		'bureaucrat' => [
			'inactive',
			'confirmed',
		],
	],
	'+dtywiki' => [
		'sysop' => [
			'autopatrolled', // T176709
			'transwiki', // T174226
		],
	],
	'+elwiki' => [
		'sysop' => [ 'rollbacker', 'extendedconfirmed' ], // T257745, T306241
	],
	'+elwiktionary' => [
		'bureaucrat' => [ 'interface-editor' ],
		'sysop' => [ 'autopatrolled', 'rollbacker' ], // rollbacker added in T255569
	],
	'+enwiki' => [
		'bureaucrat' => [ 'ipblock-exempt', 'sysop' ],
		'sysop' => [ 'rollbacker', 'accountcreator', 'abusefilter', 'abusefilter-helper', 'autoreviewer', 'confirmed', 'reviewer', 'filemover', 'templateeditor', 'massmessage-sender', 'extendedconfirmed', 'extendedmover', 'patroller', 'eventcoordinator' ], // T126607, T133981, T149019, T175684, T193075
	],
	'+enwikibooks' => [
		'sysop' => [ 'import', 'transwiki', 'uploader', 'flood' ], // T278683, T285594
	],
	'+enwikinews' => [
		'bureaucrat' => [ 'flood', 'sysop' ],
		'sysop' => [ 'flood' ],
	],
	'+enwikiquote' => [
		'sysop' => [ 'rollbacker' ], // T310950
	],
	'+enwikivoyage' => [
		'sysop' => [
			'autopatrolled',
			'templateeditor',
			'patroller' // T222008
		],
		'bureaucrat' => [ 'sysop', 'patroller' ],
	],
	'+enwikiversity' => [
		'sysop' => [ 'curator' ], // T113109
		'bureaucrat' => [ 'import' ], // T294930
	],
	'+enwikisource' => [
		'bureaucrat' => [ 'autopatrolled', 'flood' ], // T38863
		'sysop' => [ 'abusefilter', 'autopatrolled', 'upload-shared', ],
	],
	'+eswiki' => [
		'bureaucrat' => [ 'abusefilter', 'rollbacker', 'templateeditor', 'botadmin', ], // T262174, T330470, T342484
		'sysop' => [ 'rollbacker', 'autopatrolled', 'patroller' ],
	],
	'+eswikibooks' => [
		'bureaucrat' => [ 'flood' ],
		'sysop' => [ 'rollbacker', 'patroller', 'autopatrolled' ], // T111455
	],
	'+eswikinews' => [
		'bureaucrat' => [ 'editprotected', 'flood', ],
	],
	'+eswikiquote' => [
		'sysop' => [ 'autopatrolled', 'confirmed', 'patroller', 'rollbacker' ], // T64911
	],
	'+eswikisource' => [
		'sysop' => [ 'autopatrolled' ], // T69557
	],
	'+eswikivoyage' => [
		'sysop' => [ 'rollbacker', 'patroller', 'autopatrolled', ], // T46285, T57665
	],
	'+eswiktionary' => [
		'bureaucrat' => [ 'autopatrolled', 'patroller', 'rollbacker' ],
	],
	'+enwiktionary' => [
		'bureaucrat' => [ 'sysop' ], // bureaucrat removed per T44113
		'sysop' => [ 'autopatrolled', 'flood', 'patroller', 'rollbacker', 'templateeditor', 'extendedmover' ], // T148007, T212662
	],
	'+etwiki' => [
		'sysop' => [ 'autopatrolled' ], // T150852
	],
	'+fawiki' => [
		'bureaucrat' => [
			'image-reviewer', // T66532
			'botadmin', // T71411
			'templateeditor', // T74146
			'abusefilter', // T74502
			'eliminator', // T87558
			'sysop', // T140810
		],
		'sysop' => [
			'rollbacker', // T25233
			'autopatrolled', // T31007, T144699
			'confirmed', // T87348
			'patroller', // T118847
			'extendedconfirmed', // T140839
			'extendedmover', // T299038
		],
	],
	'+fawikibooks' => [
		'sysop' => [
			'autopatrolled', // T111024
			'patroller', // T111024
			'rollbacker', // T111024
		],
	],
	'+fawikiquote' => [
		'sysop' => [
			'autopatrolled', // T56951
			'rollbacker', // T56951
		],
	],
	'+fawikinews' => [
		'sysop' => [ 'rollbacker', 'patroller', ],
	],
	'+fawikivoyage' => [
		'sysop' => [
			'autopatrolled', // T73760
			'confirmed', // T73760
			'flood', // T73760
			'patroller', // T73760
			'rollbacker', // T73760
			'transwiki', // T73681
		],
	],
	'+fawiktionary' => [
		'sysop' => [ 'autopatrolled', 'patroller', 'rollbacker' ], // T85381
	],
	'+fawikisource' => [
		'sysop' => [
			'autopatrolled', // T187662
			'patroller', // T187662
			'rollbacker', // T161946
		],
	],
	'+fiwiki' => [
		'bureaucrat' => [ 'sysop', 'bureaucrat', 'arbcom' ],
		'sysop' => [ 'rollbacker', 'accountcreator' ], // T149986
	],
	'+fiwiktionary' => [
		'sysop' => [
			'rollbacker', // T323063
		],
	],
	'+fiwikimedia' => [
		'bureaucrat' => [ 'sysop', 'bureaucrat' ],
	],
	'+foundationwiki' => [
		'sysop' => [ 'confirmed', 'flood', 'editor' ],
	],
	'+frwiki' => [
		'sysop' => [ 'rollbacker' ], // T170780
		'bureaucrat' => [ 'abusefilter', 'sysop', 'rollbacker' ],
	],
	'+frwikibooks' => [
		'bureaucrat' => [ 'abusefilter' ],
		'sysop' => [ 'patroller' ],
	],
	'+frwikinews' => [
		'sysop' => [ 'trusteduser', 'facilitator' ], // T90979
	],
	'+frwikisource' => [
		'sysop' => [ 'patroller', 'autopatrolled' ],
	],
	'+frwikiversity' => [
		'sysop' => [ 'patroller' ],
	],
	'+frwikivoyage' => [
		'sysop' => [ 'patroller' ],
	],
	'+frwiktionary' => [ // T138972
		'bureaucrat' => [ 'patroller', 'transwiki', 'autopatrolled', 'confirmed', 'abusefilter', 'botadmin' ],
	],
	'+gawiki' => [
		'sysop' => [ 'rollbacker' ],
	],
	'+ganwiki' => [
		'sysop' => [ 'transwiki' ], // T354850
		'transwiki' => [ 'transwiki' ], // T354850
	],
	'+glwiki' => [ // T128948
		'sysop' => [ 'confirmed' ],
	],
	'+guwiki' => [
		'sysop' => [ 'autopatrolled', 'rollbacker', 'transwiki' ], // T119787, T119787, T120346
	],
	'+hewiki' => [
		'sysop' => [ 'patroller', 'autopatrolled', 'accountcreator', 'templateeditor' ], // T296769
	],
	'+hewikibooks' => [
		'sysop' => [ 'patroller', 'autopatrolled' ],
	],
	'+hewikiquote' => [
		'sysop' => [ 'autopatrolled' ],
	],
	'+hewiktionary' => [
		'sysop' => [ 'autopatrolled', 'patroller' ], // T140563
	],
	'+hewikivoyage' => [
		'sysop' => [ 'autopatrolled' ], // T52377
	],
	'+hewikinews' => [
		'sysop' => [ 'patroller', 'autopatrolled' ], // T140544
	],
	'+hewikisource' => [
		'bureaucrat' => [ 'import', 'transwiki' ], // T274796
		'sysop' => [ 'reviewer' ], // T274796
	],
	'+hiwiki' => [
		'sysop' => [ 'abusefilter', 'autopatrolled', 'reviewer', 'rollbacker', 'filemover', 'templateeditor' ], // T37355, T56589, T120342
	],
	'+hiwikiversity' => [
		'sysop' => [ 'autopatrolled' ], // T179251
	],
	'+hrwiki' => [ // T276560
		'bureaucrat' => [ 'patroller', 'autopatrolled', 'flood' ],
		'sysop' => [ 'patroller', 'autopatrolled', 'flood' ],
	],
	'+huwiki' => [
		'bureaucrat' => [ 'sysop', 'bureaucrat', 'templateeditor', 'interface-editor' ], // T74055, T109408
	],
	'+idwiki' => [
		'sysop' => [ 'rollbacker' ],
		'bureaucrat' => [ 'rollbacker' ],
	],
	'+idwikimedia' => [ // T192726
		'sysop' => [ 'import' ],
		'bureaucrat' => [ 'bureaucrat', 'sysop' ],
	],
	'+id_internalwikimedia' => [
		'sysop' => [ 'import' ],
	],
	'+incubatorwiki' => [
		'bureaucrat' => [ 'import', 'test-sysop', 'translator' ],
	],
	'+itwiki' => [
		'bureaucrat' => [ 'rollbacker', 'autopatrolled', 'mover', 'botadmin' ], // T55913, T102770, T220915
		'sysop' => [ 'accountcreator', 'flood', ], // T63109
	],
	'+itwikibooks' => [
		'sysop' => [ 'autopatrolled', 'accountcreator', 'confirmed', 'patroller', 'flood' ], // flood added per T41569
		'accountcreator' => [ 'confirmed' ], // T206447
	],
	'+itwikinews' => [
		'sysop' => [ 'autopatrolled', 'rollbacker' ], // T142571
		'bureaucrat' => [ 'closer' ], // T257927
	],
	'+itwikiquote' => [
		'bureaucrat' => [ 'autopatrolled' ], // T64200
	],
	'+itwikisource' => [
		'bureaucrat' => [ 'flood' ], // T38600
	],
	'+itwikivoyage' => [
		'sysop' => [ 'autopatrolled', 'patroller' ], // T45327 and T47638
	],
	'+itwiktionary' => [
		'sysop' => [ 'patroller', 'autopatrolled', 'flood' ], // Flood added per T41306
	],
	'+itwikiversity' => [
		'sysop' => [ 'autopatrolled', 'patroller', 'flood', 'accountcreator' ], // T114930, T158062
	],
	'+jawiki' => [
		'sysop' => [
			'abusefilter',
			'extendedconfirmed', // T249820
			'rollbacker' // T258339
		],
		'bureaucrat' => [ 'eliminator' ], // T258339
	],
	'+jawiktionary' => [
		'sysop' => [ 'autopatrolled' ], // T63366
	],
	'+kawiki' => [
		'sysop' => [ 'trusted' ],
	],
	'+kkwiki' => [
		'sysop' => [ 'rollbacker' ], // T130215
	],
	'+knwiki' => [ // T322472
		'sysop' => [ 'flood' ],
	],
	'+kowiki' => [
		'sysop' => [ 'rollbacker', 'confirmed', 'uploader', 'autopatrolled', 'extendedconfirmed' ], // T85621, T130808, T184675
	],
	'+kswiki' => [
		'sysop' => [ 'rollbacker', 'uploader' ], // T286789, T305320
	],
	'+ladwiki' => [
		'sysop' => [ 'flood' ], // T131527
		'bureaucrat' => [ 'flood' ], // T131527
	],
	'+legalteamwiki' => [ // T63222
		'bureaucrat' => [ 'ipblock-exempt' ],
	],
	'+lijwiki' => [ // T256109
		'sysop' => [ 'mover', 'rollbacker' ],
	],
	'+lmowiktionary' => [
		'sysop' => [ 'extendedmover' ], // T327340
	],
	'+ltwiki' => [
		'sysop' => [ 'abusefilter' ],
	],
	'+ltwiktionary' => [
		'sysop' => [ 'abusefilter' ],
	],
	'+lvwiki' => [
		'sysop' => [ 'autopatrolled', 'patroller', 'flood' ], // T72441, T121238
	],
	'+maiwiki' => [
		'sysop' => [ 'autopatrolled', 'patroller', 'rollbacker', 'import', 'accountcreator' ], // T89346, T99491, T118934, T126950
	],
	'+mediawikiwiki' => [
		'sysop' => [
			'autopatrolled',
			'uploader' // T217523
		],
		'bureaucrat' => [
			'autopatrolled',
			'transwiki',
			'import'
		],
	],
	'+metawiki' => [
		'bureaucrat' => [ 'flood', 'centralnoticeadmin', 'uploader' ], // T39198, T52287, T110674
		'sysop' => [ 'autopatrolled', 'event-organizer', 'massmessage-sender', 'patroller' ], // T356070, T59611, T176079
	],
	'+mkwiki' => [
		'bureaucrat' => [ 'patroller', 'autopatrolled', 'autoreviewed' ],
	],
	'+mlwiki' => [
		'sysop' => [ 'patroller', 'autopatrolled', 'rollbacker' ],
		'bureaucrat' => [ 'botadmin' ],
	],
	'+mlwikisource' => [
		'sysop' => [ 'patroller', 'autopatrolled' ],
	],
	'+mrwiki' => [
		'sysop' => [ 'rollbacker' ], // T270864
	],
	'+mrwikisource' => [
		'sysop' => [
			'templateeditor', // T269067
			'patroller', // T269067
		],
	],
	'+mywiki' => [
		'sysop' => [
			'autopatrolled', // T341026
			'patroller', // T341026
		],
	],
	'+mznwiki' => [
		'sysop' => [ 'uploader' ]
	],
	'+newiki' => [
		'sysop' => [
			'autopatrolled', // T89816, T148171
			'import', // T100925, T148171
			'patroller', // T95101, T148171, T327114
			'rollbacker', // T90888, T148171
			'templateeditor', // T195557
			'flood', // T211181
			'transwiki', // T214036
		],
		'bureaucrat' => [
			'abusefilter', // T95102
			'filemover', // T95103
		],
	],
	'+nlwiki' => [
		'bureaucrat' => [
			'abusefilter',
			'arbcom',
			'extendedconfirmed', // T329642
			'rollbacker',
		],
	],
	'+nlwikibooks' => [
		'sysop' => [ 'abusefilter' ],
	],
	'+nlwikivoyage' => [
		'bureaucrat' => [ 'autopatrolled' ], // T46082
	],
	'+nnwiki' => [
		'bureaucrat' => [ 'autopatrolled', 'patroller', 'transwiki' ], // T231761
		'sysop' => [ 'autopatrolled' ],
	],
	'+nowiki' => [
		'bureaucrat' => [ 'patroller', 'autopatrolled', 'transwiki' ],
		'sysop' => [ 'autopatrolled', 'abusefilter', 'confirmed' ],
	],
	'+nowikibooks' => [
		'bureaucrat' => [ 'bot', 'patroller', 'autopatrolled' ],
		'sysop' => [ 'autopatrolled', 'patroller' ],
	],
	'+nowiktionary' => [
		'sysop' => [ 'transwiki' ], // T172365
	],
	'+nowikimedia' => [
		'sysop' => [ 'translationadmin' ], // T152490
	],
	'+nycwikimedia' => [
		'bureaucrat' => [ 'sysop' ], // T226591
	],
	'+officewiki' => [
		'bureaucrat' => [ 'flood', 'securepoll' ],
	],
	'+sourceswiki' => [
		'bureaucrat' => [ 'flood' ], // T193350
	],
	'+orwiki' => [
		'sysop' => [ 'rollbacker' ],
	],
	'+outreachwiki' => [
		'bureaucrat' => [
			'sysop', 'autopatrolled', 'import',
		],
	],
	'+pawiki' => [
		'sysop' => [ 'patroller', 'autopatrolled', 'transwiki' ], // T120369
	],
	'+pawikisource' => [
		'sysop' => [ 'validator' ], // T341428
	],
	'+plwiki' => [
		'bureaucrat' => [ 'abusefilter', 'flood', 'arbcom' ], // T256572
	],
	'+plwikiquote' => [
		'sysop' => [ 'patroller' ], // T30479
	],
	'+pswiki' => [
		'bureaucrat' => [ 'interface-editor' ], // T133472
	],
	'+ptwiki' => [
		'bureaucrat' => [ 'rollbacker', 'eliminator', 'autoreviewer', 'interface-editor', 'bureaucrat', 'sysop', 'flood' ], // T41905, T65750, T107661, T212735, T228521
		'sysop' => [ 'rollbacker', 'autoreviewer', 'confirmed', 'accountcreator', 'flood', 'extendedconfirmed' ], // T65750, T228521, T281926
	],
	'+ptwikinews' => [
		'sysop' => [ 'reviewer', 'editprotected' ], // T162577
	],
	'+ptwikisource' => [ // T331762
		'sysop' => [ 'patroller', 'autopatrolled', 'rollbacker' ],
	],
	'+ptwikivoyage' => [
		'sysop' => [
			'accountcreator',
			'autopatrolled', // T168981
			'confirmed',
			'transwiki'
		], // T292806
	],
	'+quwiki' => [
		'sysop' => [ 'rollbacker' ],
	],
	'+rowiki' => [
		'bureaucrat' => [ 'abusefilter' ], // T28634
		'sysop' => [ // T63172, T231099, T63172
			'autopatrolled',
			'extendedconfirmed',
			'templateeditor',
			'patroller',
		],
	],
	'+ruewiki' => [
		'sysop' => [ 'rollbacker' ], // T264147
	],
	'+ruwiki' => [
		'bureaucrat' => [ // T51334, T144599
			'arbcom',
			'engineer',
			'sysop',
		],
		'sysop' => [
			'rollbacker',
			'uploader',
			'closer',
			'filemover',
			'suppressredirect',
			'confirmed', // T334780
		],
	],
	'+ruwikinews' => [
		'sysop' => [ 'rollbacker' ], // T201265
	],
	'+ruwikiquote' => [
		'sysop' => [ 'autoeditor', 'rollbacker' ], // T200201
	],
	'+ruwikisource' => [
		'bureaucrat' => [ 'autoeditor', 'rollbacker' ],
		'sysop' => [ 'autoeditor', 'rollbacker', 'abusefilter', 'flood' ],
	],
	'+ruwikivoyage' => [
		'sysop' => [ 'autopatrolled', 'rollbacker', 'patroller', 'filemover' ], // T48915 and T116143
	],
	'+ruwiktionary' => [
		'sysop' => [ 'rollbacker' ], // T183655
	],
	'+sawiki' => [
		'sysop' => [ 'autopatrolled', 'patroller' ], // T117314
	],
	'+sawikisource' => [
		'sysop' => [ 'autopatrolled' ],
	],
	'+scowiki' => [
		'sysop' => [ 'rollbacker', 'autopatrolled', 'flood', ] // T111753
	],
	'+sdwiki' => [
		'sysop' => [ 'autopatrolled' ], // T177141
	],
	'+sewikimedia' => [
		'bureaucrat' => [ 'sysop', 'bureaucrat' ],
	],
	'+shwiki' => [
		'bureaucrat' => [
			'autopatrolled', 'filemover', 'patroller', 'rollbacker', // T52802
			'flood', // T54273
		],
		'sysop' => [
			'filemover', 'patroller', 'rollbacker', // T52802
			'flood', // T54273
			'autopatrolled', // T325938
		],
	],
	'+shwiktionary' => [
		'sysop' => [ 'autopatrolled' ],
		'bureaucrat' => [ 'autopatrolled' ],
	],
	'+siwiki' => [
		'sysop' => [ 'rollbacker', 'accountcreator', 'abusefilter', 'autopatrolled', 'confirmed', 'reviewer', ],
	],
	'+simplewiki' => [
		'bureaucrat' => [ 'flood', 'rollbacker', 'sysop', 'import', 'transwiki', 'patroller' ],
		'sysop' => [ 'rollbacker', 'flood', 'patroller', 'uploader' ], // T127826
	],
	'+simplewiktionary' => [
		'bureaucrat' => [ 'sysop' ],
		'sysop' => [ 'rollbacker', 'autopatrolled' ],
	],
	'+skwiki' => [
		'sysop' => [ 'rollbacker' ],
	],
	'+specieswiki' => [
		'sysop' => [ 'autopatrolled', 'patroller' ], // T89147
	],
	'+sqwiki' => [
		'bureaucrat' => [ 'editor' ],
		'sysop' => [ 'reviewer' ],
	],
	'+srwiki' => [
		'bureaucrat' => [ 'patroller', 'rollbacker', 'autopatrolled', 'flood' ],
		'sysop' => [ 'rollbacker' ],
	],
	'+srwikibooks' => [ // T209250
		'bureaucrat' => [ 'patroller', 'rollbacker', 'autopatrolled' ],
		'sysop' => [ 'rollbacker' ],
	],
	'+srwikinews' => [ // T209251
		'bureaucrat' => [ 'patroller', 'autopatrolled', 'rollbacker' ],
		'sysop' => [ 'rollbacker' ],
	],
	'+srwikisource' => [
		'bureaucrat' => [ 'patroller', 'autopatrolled', 'rollbacker' ], // T206935
		'sysop' => [ 'rollbacker' ], // T206935
	],
	'+srwikiquote' => [ // T206936
		'bureaucrat' => [ 'patroller', 'rollbacker', 'autopatrolled' ],
		'sysop' => [ 'rollbacker' ],
	],
	'+srwiktionary' => [ // T209252
		'bureaucrat' => [ 'patroller', 'autopatrolled', 'rollbacker' ],
		'sysop' => [ 'rollbacker' ],
	],
	'+strategywiki' => [
		'bureaucrat' => [ 'flood' ],
	],
	'+svwiki' => [
		'sysop' => [ 'rollbacker', 'autopatrolled', 'extendedconfirmed' ], // T161210, T279836
	],
	'+svwikisource' => [
		'sysop' => [ 'autopatrolled', ],
	],
	'+svwikivoyage' => [
		'sysop' => [
			'autopatrolled', // T93339
			'patroller', // T93339
			'rollbacker', // T93339
		],
	],
	'+svwiktionary' => [
		'sysop' => [ 'autopatrolled', ], // T161919
	],
	'+tawiki' => [
		'bureaucrat' => [ 'nocreate' ],
		'sysop' => [ 'patroller', 'rollbacker', 'autopatrolled' ], // T95180
	],
	'+trwiki' => [
		'sysop' => [ 'patroller', 'massmessage-sender' ], // bureaucrat -> sysop, T40690, T147740
		'bureaucrat' => [ 'interface-editor' ], // T41690
	],
	'+trwikivoyage' => [
		'sysop' => [ 'rollbacker' ], // T314678
	],
	'+thwiki' => [
		'sysop' => [ 'uploader', 'patroller' ], // T216615, T272149
	],
	'+ukwiki' => [
		'sysop' => [
			'patroller',
			'rollbacker',
			'accountcreator', // T104034
			'filemover', // T119636
			'flood', // T319243
		],
	],
	'+ukwikivoyage' => [
		'sysop' => [ 'rollbacker', 'uploader', 'autopatrolled' ], // T56229
	],
	'+ukwiktionary' => [
		'bureaucrat' => [ 'autoeditor' ],
		'sysop' => [ 'autoeditor' ],
	],
	'+urwiki' => [
		'bureaucrat' => [
			'import', // T44737
			'abusefilter', // T47643
			'rollbacker', // T47643
			'massmessage-sender', // T144701
			'interface-editor', // T133564
			'eliminator', // T184607
		],
		'sysop' => [
			'confirmed', // T44737
			'accountcreator', // T137888
			'filemover', // T137888
			'autopatrolled', // T139302
			'massmessage-sender', // T144701
			'extendedmover', // T211978
			'transwiki', // T212612
			'patroller', // T301491
			'eliminator', // T307029
		],
	],
	'+uzwiki' => [
		'sysop' => [
			'rollbacker', // T265509
			'eliminator', // T302670
			'patroller', // T338826
		],
	],
	'+viwiki' => [
		'sysop' => [
			'rollbacker',
			'flood',
			'patroller', // T48828
			'autopatrolled', // T48828
			'extendedconfirmed', // T215493
			'templateeditor', // T296154
			'uploader', // T303577
		],
		'bureaucrat' => [
			'eliminator', // T70612
			'flood'
		],
	],
	'+viwikibooks' => [
		'bureaucrat' => [ 'eliminator', ], // T202207
	],
	'+viwiktionary' => [
		'sysop' => [ 'rollbacker' ], // T176979
	],
	'+vecwiki' => [
		'sysop' => [ 'flood' ],
	],
	// T74589
	'+votewiki' => [
		'bureaucrat' => [
			'electcomm',
			'staffsupport',
		],
		'staffsupport' => [ 'electionadmin' ],
		'electcomm' => [ 'electionadmin' ],
	],
	'+wikidata' => [
		'sysop' => [
			'rollbacker', // T47165
			'confirmed', // T47124
			'propertycreator', // T48953
		],
		'bureaucrat' => [
			'flood', // T50013
			'wikidata-staff', // T74459
		],
		'wikidata-staff' => [ 'wikidata-staff', ], // T74459
	],
	'+wikifunctionswiki' => [
		'sysop' => [
			'confirmed', // T344261
			'autopatrolled', // T343946
			'functioneer', // T352495
		],
		'bureaucrat' => [ 'wikifunctions-staff' ],
		'wikifunctions-staff' => [ 'functioneer', 'functionmaintainer', 'sysop', 'bureaucrat', 'wikifunctions-staff' ],
	],
	'+wikimaniawiki' => [
		'sysop' => [ 'uploader' ], // T225505
	],
	'+wikimaniateamwiki' => [
		'bureaucrat' => [ 'autopatrolled', ],
	],
	'wikitech' => [
		'contentadmin' => [
			'autopatrolled',
			'ipblock-exempt',
		],
		'bureaucrat' => [
			'bot',
			'confirmed',
			'contentadmin',
			'flood',
			'interface-admin',
			'oathauth',
			'oauthadmin',
			'sysop',
			'transwiki',
		],
		'sysop' => [
			'autopatrolled',
			'ipblock-exempt',
		],
	],
	'+wuuwiki' => [
		'sysop' => [ 'rollbacker' ],
	],
	'+yowiki' => [
		'sysop' => [ 'accountcreator', 'rollbacker', ], // T249487
	],
	'+zhwiki' => [
		'accountcreator' => [ 'eventparticipant' ], // T198167
		'bureaucrat' => [ 'flood' ],
		'sysop' => [
			'abusefilter-helper', // T344398
			'patroller',
			'rollbacker',
			'autoreviewer',
			'confirmed',
			'flood',
			'massmessage-sender', // T130814
			'filemover', // T195247
			'eventparticipant', // T198167
			'transwiki', // T250972
			'templateeditor', // T260012
			'ipblock-exempt-grantor', // T357991
		],
	],
	'+zhwikibooks' => [
		'sysop' => [
			'flood', // T185182
			'transwiki', // T313657
		],
	],
	'+zhwikinews' => [
		'sysop' => [
			'rollbacker',
			'flood', // T54546
			'transwiki', // T273405
		],
	],
	'+zhwikiquote' => [
		'sysop' => [
			'flood', // T189289
			'transwiki', // T313657
		],
	],
	'+zhwikisource' => [
		'sysop' => [
			'transwiki', // T313657
		],
	],
	'+zhwikiversity' => [
		'sysop' => [
			'transwiki', // T201328
			'patroller', // T202599
			'autopatrolled', // T202599
			'flood', // T202599
			'confirmed', // T202599
		],
	],
	'+zhwikivoyage' => [
		'sysop' => [
			'autopatrolled', // T62328
			'confirmed', // T62085
			'patroller', // T62328
			'transwiki', // T62085
		],
	],
	'+zhwiktionary' => [
		'sysop' => [
			'flood', // T187018
			'templateeditor', // T286101
			'transwiki', // T313657
		],
	],
	'+zh_yuewiki' => [
		'sysop' => [ 'abusefilter', 'rollbacker', 'autoreviewer', 'confirmed' ],
	],
],
# @} end of wgRemoveGroups

];
