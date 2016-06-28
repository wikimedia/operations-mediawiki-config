<?php
/**
 * Configuration for [[:meta:Special:Contact/affcomusergroup]]
 *
 * @see T95789
 */

$wgHooks['ContactForm'][] = function (
	&$to, $reply, &$subject, &$text, $par, $data
) {
	if ( $par === 'affcomusergroup' ) {
		$subject = "User group request: {$data['GroupName']}";
	}
	return true;
};

$wgContactConfig['affcomusergroup'] = [
	'RecipientUser' => 'Usergroups',
	'SenderName' => 'User group contact form on ' . $wgSitename,
	'SenderEmail' => null,
	'RequireDetails' => true,
	'IncludeIP' => false,
	'DisplayFormat' => 'vform',
	'RLStyleModules' => [
		'ext.wikimediamessages.contactpage.affcomusergroup',
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
			'validation-callback' => function ( $value ) { return !!$value; }
		]
	]
];
