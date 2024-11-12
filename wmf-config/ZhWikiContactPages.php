<?php
/**
 * Configuration for [[zh:w:Special:Contact/ipbe]]
 *
 * @see T359998
 */

$wgContactConfig['ipbe'] = [
	'RecipientUser' => 'IPBE-zh',
	'SenderName' => '中文維基百科聯絡表單',
	'RequireDetails' => true,
	'IncludeIP' => true,
	'MustBeLoggedIn' => false,
	'NameReadonly' => false,
	'EmailReadonly' => false,
	'SubjectReadonly' => true,
	'MustHaveEmail' => false,
	'AdditionalFields' => [
		'blockid' => [
			'label-message' => 'contactpage-ipbe-blockid',
			'type' => 'text',
			'required' => true,
		],
		'gfw' => [
			'type' => 'multiselect',
			'label-message' => 'contactpage-ipbe-gfw',
			'options-messages' => [
				'contactpage-ipbe-gfwyes' => 'ipbe-gfw'
			]
		],
		'accountname' => [
			'label-message' => 'contactpage-ipbe-accountname',
			'type' => 'text',
		],
		'reason' => [
			'label-message' => 'contactpage-ipbe-reason',
			'type' => 'textarea',
			'required' => true,
			'rows' => 7,
		],
		'page' => [
			'label-message' => 'contactpage-ipbe-page',
			'type' => 'text'
		],
		'editdesire' => [
			'label-message' => 'contactpage-ipbe-editdesire',
			'type' => 'textarea',
		],
	],
	'RLModules' => [],
	'RLStyleModules' => []
];
