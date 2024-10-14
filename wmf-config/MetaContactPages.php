<?php
/**
 * Configuration for [[:meta:Special:Contact/affcomusergroup]]
 *
 * @see T95789
 */

$wgHooks['ContactForm'][] = static function (
	&$to, $reply, &$subject, &$text, $par, $data
) {
	if ( $par === 'affcomusergroup' ) {
		$subject = "User group request: {$data['GroupName']}";
	}

	if ( $par === 'affcomchapthorg' ) {
		$subject = "Chapter/Thematic organization request: {$data['GroupName']}";
	}
	return true;
};

$wgContactConfig['affcomusergroup'] = [
	'RecipientUser' => 'Usergroups',
	'SenderName' => 'User group contact form on ' . $wgSitename,
	'SenderEmail' => null,
	'RequireDetails' => true,
	'MustBeLoggedIn' => true,
	'IncludeIP' => false,
	'RLStyleModules' => [
		'ext.wikimediamessages.contactpage',
	],
	'AdditionalFields' => [
		'GroupName' => [
			'label-message' => 'contactpage-affcom-user-group-name-label',
			'contactpage-email-label' => 'Group name',
			'type' => 'text'
		],
		'GroupDescription' => [
			'label-message' => 'contactpage-affcom-user-group-description-label',
			'contactpage-email-label' => 'Group description',
			'type' => 'textarea',
			'rows' => 10

		],
		'GroupWikiPage' => [
			'label-message' => 'contactpage-affcom-user-group-wikipage-label',
			'contactpage-email-label' => 'Group wiki page',
			'type' => 'text'
		],
		'GroupLocation' => [
			'label-message' => 'contactpage-affcom-user-group-location-label',
			'contactpage-email-label' => 'Group location',
			'type' => 'text'
		],
		'GroupLeaders' => [
			'label-message' => 'contactpage-affcom-user-group-leaders-label',
			'contactpage-email-label' => 'Active Wikimedians',
			'type' => 'textarea',
			'rows' => 10
		],
		'GroupLogo' => [
			'label-message' => 'contactpage-affcom-user-group-logo-label',
			'contactpage-email-label' => 'Logo',
			'type' => 'radio',
			'options-messages' => [
				'contactpage-affcom-user-group-logo-community' => 'Wikimedia community',
				'contactpage-affcom-user-group-logo-affiliate' => 'Wikipedia affiliate'
			]
		],
		'Rules' => [
			'label-message' => 'contactpage-affcom-user-group-rules-label',
			'type' => 'info',
		],
		'Terms' => [
			'label-message' => 'contactpage-affcom-user-group-terms-label',
			'contactpage-email-label' => 'Terms',
			'type' => 'check',
			'required' => true,
			'validation-callback' => static function ( $value ) {
				return (bool)$value;
			}
		]
	]
];

/**
 * Configuration for [[:meta:Special:Contact/affcomchapthorg]]
 *
 * @see T298024
 */
$wgContactConfig['affcomchapthorg'] = [
	'RecipientUser' => 'Chapthorgs',
	'SenderName' => 'Contact Form on ' . $wgSitename,
	'SenderEmail' => null,
	'RequireDetails' => true,
	'IncludeIP' => false,
	'MustBeLoggedIn' => true,
	'RLStyleModules' => [
		'ext.wikimediamessages.contactpage'
	],
	'AdditionalFields' => [
		'ApplicationType' => [
			'label-message' => 'contactpage-affcom-chapter-thorg-application-type-label',
			'type' => 'select',
			'options-messages' => [
				'contactpage-affcom-chapter-thorg-application-chapter-status' => 'Application for Chapter status',
				'contactpage-affcom-chapter-thorg-application-thorg-status' => 'Application for Thematic Organization status'
			]
		],
		'GroupName' => [
			'label-message' => 'contactpage-affcom-chapter-thorg-group-name-label',
			'type' => 'text',
			'required' => true
		],
		'ApplicationSubPage' => [
			'label-message' => 'contactpage-affcom-chapter-thorg-application-subpage-label',
			'type' => 'text',
			'required' => true
		],
		'OrgBylaws' => [
			'label-message' => 'contactpage-affcom-chapter-thorg-bylaws-label',
			'type' => 'text'
		],
		'SelfAssessmentChecklist' => [
			'label-message' => 'contactpage-affcom-chapter-thorg-self-assessment-checklist-label',
			'type' => 'info'
		],
		'LegalStatus' => [
			'label-message' => 'contactpage-affcom-chapter-thorg-legal-status-label',
			'type' => 'check',
			'validation-callback' => static function ( $value ) {
				return (bool)$value;
			}
		],
		'GroupMission' => [
			'label-message' => 'contactpage-affcom-chapter-thorg-group-mission-label',
			'type' => 'check',
			'validation-callback' => static function ( $value ) {
				return (bool)$value;
			}
		],
		'Inclusivity' => [
			'label-message' => 'contactpage-affcom-chapter-thorg-inclusivity-label',
			'type' => 'check',
			'validation-callback' => static function ( $value ) {
				return (bool)$value;
			}
		],
		'StructureMemberRights' => [
			'label-message' => 'contactpage-affcom-chapter-thorg-structure-member-rights-label',
			'type' => 'check',
			'validation-callback' => static function ( $value ) {
				return (bool)$value;
			}
		],
		'BoardStructure' => [
			'label-message' => 'contactpage-affcom-chapter-thorg-board-structure-label',
			'type' => 'check',
			'validation-callback' => static function ( $value ) {
				return (bool)$value;
			}
		],
		'MeetingTerms' => [
			'label-message' => 'contactpage-affcom-chapter-thorg-meeting-terms-label',
			'type' => 'check',
			'validation-callback' => static function ( $value ) {
				return (bool)$value;
			}
		],
		'Elections' => [
			'label-message' => 'contactpage-affcom-chapter-thorg-elections-label',
			'type' => 'check',
			'validation-callback' => static function ( $value ) {
				return (bool)$value;
			}
		],
		'Representation' => [
			'label-message' => 'contactpage-affcom-chapter-thorg-representation-label',
			'type' => 'check',
			'validation-callback' => static function ( $value ) {
				return (bool)$value;
			}
		],
		'CodeOfConduct' => [
			'label-message' => 'contactpage-affcom-chapter-thorg-coc-label',
			'type' => 'check',
			'validation-callback' => static function ( $value ) {
				return (bool)$value;
			}
		],
		'Rules' => [
			'label-message' => 'contactpage-affcom-chapter-thorg-rules-label',
			'type' => 'info'
		],
		'Terms' => [
			'label-message' => 'contactpage-affcom-chapter-thorg-terms-label',
			'type' => 'check',
			'required' => true,
			'validation-callback' => static function ( $value ) {
				return (bool)$value;
			}
		]
	]
];

/**
 * Configuration for legal contact forms (license stuff)
 * Modifications: T303359
 */

$wgContactConfig['requestlicense'] = [
	'RecipientUser' => 'Trademarks (WMF)',
	// TODO: Replace with details submitted on form
	'SenderEmail' => $wgPasswordSender,
	'SenderName' => 'Contact Page',
	'RequireDetails' => true,
	'IncludeIP' => false,
	'AdditionalFields' => [
		'Group' => [
			'type' => 'text',
			'label-message' => 'contactpage-license-request-group',
		],
		'Description' => [
			'label-message' => 'contactpage-license-request-description',
			'type' => 'textarea',
			'rows' => 10,
			'required' => true,
		],
		'use-note' => [
			'type' => 'info',
			'help-messages' => [ 'contactpage-license-request-use-note' ],
		],
	]
];

/**
 * Configuration for signup form for [[:meta:Movement_communications_group]]
 *
 * @see T218363
 */

$wgContactConfig['movecomsignup'] = [
	'RecipientUser' => 'MoveCom-WMF',
	'SenderName' => 'Movement communications group signup form on ' . $wgSitename,
	'SenderEmail' => null,
	'RequireDetails' => true,
	'MustBeLoggedIn' => true,
	'IncludeIP' => false,
	'RLStyleModules' => [
		'ext.wikimediamessages.contactpage',
	],
	'AdditionalFields' => [
		'Username' => [
			'label-message' => 'contactpage-movecom-signup-username-label',
			'type' => 'text',
			'required' => true,
		],
		'Affiliation' => [
			'type' => 'selectorother',
			'label-message' => 'contactpage-movecom-signup-affiliation-label',
			'options-messages' => [
				'contactpage-movecom-signup-affiliation-affiliates' => 'affiliates',
				'contactpage-movecom-signup-affiliation-foundation' => 'foundation',
				'contactpage-movecom-signup-affiliation-group' => 'group',
				'contactpage-movecom-signup-affiliation-projects' => 'projects'
			],
			'required' => true,
		],
		'Affiliate' => [
			'label-message' => 'contactpage-movecom-signup-affiliate-label',
			'type' => 'text',
		],
		'Display' => [
			'label-message' => 'contactpage-movecom-signup-display-label',
			'type' => 'radio',
			'options-messages' => [
				'contactpage-movecom-signup-display-name' => 'Name',
				'contactpage-movecom-signup-display-username' => 'Username',
				'contactpage-movecom-signup-display-nameusername' => 'NameUsername'
			],
			'required' => true,
		],
		'Terms' => [
			'label-message' => 'contactpage-movecom-signup-terms-label',
			'type' => 'check',
			'required' => true,
			'validation-callback' => static function ( $value ) {
				return (bool)$value;
			}
		]
	]
];

/**
 * Configuration for contact form for [[:meta:Ombuds commission]]
 *
 * @see T271828
 */

$wgContactConfig['ombudscommission'] = [
	'RecipientUser' => 'Ombuds commission',
	'SenderEmail' => $wgPasswordSender,
	'RequireDetails' => true,
	'IncludeIP' => false,
	'AdditionalFields' => [
		'CaseExplanation' => [
			'label-message' => 'contactpage-ombudscommission-case-explanation',
			'type' => 'textarea',
			'rows' => 10,
			'required' => true
		],
		'RelevantLinks' => [
			'label-message' => 'contactpage-ombudscommission-relevant-links',
			'type' => 'textarea',
			'rows' => 10,
			'required' => false
		],
		'ViolationType' => [
			'label-message' => 'contactpage-ombudscommission-violation-type',
			'type' => 'textarea',
			'rows' => 10,
			'required' => true
		],
		'InvolvedUsers' => [
			'label-message' => 'contactpage-ombudscommission-involved-users',
			'type' => 'textarea',
			'rows' => 5,
			'required' => false
		],
		'AffectedAccounts' => [
			'label-message' => 'contactpage-ombudscommission-affected-accounts',
			'type' => 'textarea',
			'rows' => 5,
			'required' => false
		],
		'ProposedSolution' => [
			'label-message' => 'contactpage-ombudscommission-proposed-solution',
			'type' => 'textarea',
			'rows' => 10,
			'required' => false
		],
		'AdditionalInformation' => [
			'label-message' => 'contactpage-ombudscommission-additional-information',
			'type' => 'textarea',
			'rows' => 10,
			'required' => false
		],
		'Disclaimer' => [
			'label-message' => 'contactpage-ombudscommission-disclaimer-label',
			'type' => 'info'
		]
	]
];

/**
 * Configuration for contact form for account vanishing requests from mobile apps.
 *
 * @see T372828
 */

$wgContactConfig['accountvanishapps'] = [
	'Redirect' => 'https://meta.wikimedia.org/wiki/Special:GlobalVanishRequest'
];

/**
 * Configuration for https://meta.wikimedia.org/wiki/Special:Contact/Stewards
 *
 * @see T98625
 */
$wgContactConfig['stewards'] = [
	'RecipientUser' => 'Wikimedia Stewards',
	'SenderEmail' => $wgPasswordSender,
	'RequireDetails' => true,
	'IncludeIP' => true,
	'AdditionalFields' => [
		'Text' => [
			'label-message' => 'emailmessage',
			'type' => 'textarea',
			'rows' => 20,
			'required' => true
		],
		'Disclaimer' => [
			'label-message' => 'contactpage-stewards-disclaimer-label',
			'type' => 'info'
		]
	]
];
