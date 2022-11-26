<?php
/**
 * Configuration for [[en:w:Special:Contact/arbcom-blockappeal]]
 *
 * @see T321447
 */

$wgHooks['ContactForm'][] = static function (
	&$to, $reply, &$subject, &$text, $par, $data
) {
	// Create subject line based on form type and data
	if ( $par === 'arbcom-blockappeal' ) {
		$subject = "[Block appeal] {$data['FromName']}";
	}

	return true;
};

$wgContactConfig['arbcom-blockappeal'] = [
	'RecipientUser' => 'Arbitration Committee',
	'SenderName' => 'Contact form on ' . $wgSitename,
	'SenderEmail' => null,
	'RequireDetails' => true,
	'MustBeLoggedIn' => false,
	'IncludeIP' => false,
	'RLStyleModules' => [
		'ext.wikimediamessages.contactpage',
	],
	'AdditionalFields' => [
		'AppealType' => [
			'label-message' => 'contactpage-arbcom-block-appeal-type',
			'contactpage-email-label' => 'Appeal type',
			'type' => 'radio',
			'options-messages' => [
				'contactpage-arbcom-block-appeal-type-oversight' => 'Oversight block',
				'contactpage-arbcom-block-appeal-type-checkuser' => 'Checkuser block',
				'contactpage-arbcom-block-appeal-type-enforcement' => 'Arbitration Enforcement block',
				'contactpage-arbcom-block-appeal-type-arbcom-block' => 'ArbCom block',
				'contactpage-arbcom-block-appeal-type-community' => 'Invalid community block',
				'contactpage-arbcom-block-appeal-type-nonpublic' => 'Involves non-public information',
				'contactpage-arbcom-block-appeal-type-other' => 'Other appeal',
			]
		],
		'AppealReason' => [
			'label-message' => 'contactpage-arbcom-block-appeal-reason',
			'contactpage-email-label' => 'Appeal reason',
			'type' => 'radio',
			'options-messages' => [
				'contactpage-arbcom-block-appeal-reason-incorrect' => 'Block was incorrect or broke policy',
				'contactpage-arbcom-block-appeal-reason-unnecessary' => 'Block is no longer necessary',
				'contactpage-arbcom-block-appeal-reason-other' => 'Other reason'
			]
		],
		'AppealLastEdit' => [
			'label-message' => 'contactpage-arbcom-block-appeal-lastedit',
			'contactpage-email-label' => 'Date of last edit',
			'type' => 'date',
		],
		'AppealSockDrawer' => [
			'label-message' => 'contactpage-arbcom-block-appeal-sockdrawer',
			'contactpage-email-label' => 'Other acounts',
			'type' => 'textarea',
			'rows' => 3
		],
		'AppealFirst' => [
			'label-message' => 'contactpage-arbcom-block-appeal-first',
			'contactpage-email-label' => 'First appeal?',
			'type' => 'radio',
			'options-messages' => [
				'contactpage-arbcom-block-appeal-first-yes' => 'Yes',
				'contactpage-arbcom-block-appeal-first-no' => 'No',
			]
		],
		'AppealPrior' => [
			'label-message' => 'contactpage-arbcom-block-appeal-prior',
			'contactpage-email-label' => 'Previous appeal venues',
			'type' => 'multiselect',
			'options-messages' => [
				'contactpage-arbcom-block-appeal-prior-email' => 'To ArbCom',
				'contactpage-arbcom-block-appeal-prior-onwiki' => 'On wiki',
				'contactpage-arbcom-block-appeal-prior-utrs' => 'UTRS',
			]
		],
		'AppealPriorLinks' => [
			'label-message' => 'contactpage-arbcom-block-appeal-prior-documentation',
			'contactpage-email-label' => 'Previous appeal documentation',
			'type' => 'textarea',
			'rows' => 3
		],
		'AppealBlockLog' => [
			'label-message' => 'contactpage-arbcom-block-appeal-block-reason',
			'contactpage-email-label' => 'Original blocking reason',
			'type' => 'textarea',
			'rows' => 3
		],
		'AppealPotentialEdits' => [
			'label-message' => 'contactpage-arbcom-block-appeal-potential-edits',
			'contactpage-email-label' => 'Potential edits to make if unblocked',
			'type' => 'textarea',
			'rows' => 3
		],
		'AppealChanges' => [
			'label-message' => 'contactpage-arbcom-block-appeal-changes',
			'contactpage-email-label' => 'Changes to editing going forward',
			'type' => 'textarea',
			'rows' => 3
		],
		'AppealFreeResponse' => [
			'label-message' => 'contactpage-arbcom-block-appeal-free-response',
			'contactpage-email-label' => 'Other information',
			'type' => 'textarea',
			'rows' => 3
		],
		'AppealDisclaimer' => [
			'label-message' => 'contactpage-arbcom-block-appeal-disclaimer',
			'type' => 'info'
		],
	]
];
