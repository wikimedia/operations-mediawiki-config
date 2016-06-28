<?php

$trademark = [
	'type' => 'multiselect',
	'options-messages' => [
		'contactpage-wikimedia-trademark-globe' => 'wikiglobe',
		'contactpage-wikimedia-trademark-wikiwordmark' => 'wikipediawordmark',
		'contactpage-wikimedia-trademark-w' => 'stylizedw',
		'contactpage-wikimedia-trademark-foundation' => 'foundation',
		'contactpage-wikimedia-trademark-commons' => 'commons',
		'contactpage-wikimedia-trademark-incubator' => 'incubator',
		'contactpage-wikimedia-trademark-mediawiki' => 'mediawiki',
		'contactpage-wikimedia-trademark-wikiquote' => 'wikiquote',
		'contactpage-wikimedia-trademark-wikibooks' => 'wikibooks',
		'contactpage-wikimedia-trademark-wikimania' => 'wikimania',
		'contactpage-wikimedia-trademark-wikimedia' => 'wikimedia',
		'contactpage-wikimedia-trademark-wikinews' => 'wikinews',
		'contactpage-wikimedia-trademark-wikisource' => 'wikisource',
		'contactpage-wikimedia-trademark-wikispecies' => 'wikispecies',
		'contactpage-wikimedia-trademark-wikiversity' => 'wikiversity',
		'contactpage-wikimedia-trademark-wiktionary' => 'wiktionary',
		'contactpage-wikimedia-trademark-wikivoyage' => 'wikivoyage',
	],
	'required' => true,
];

$wgContactConfig['requestlicense'] = [
	'RecipientUser' => 'Trademarks (WMF)',
	'SenderEmail' => $wmgNotificationSender, // TODO: Replace with details submitted on form
	'SenderName' => 'Contact Page',
	'RequireDetails' => true,
	'IncludeIP' => false,
	'AdditionalFields' => [
		'Username' => [
			'type' => 'text',
			'label-message' => 'contactpage-license-request-username',
		],
		'Site' => [
			'type' => 'text',
			'label-message' => 'contactpage-license-request-relevantsite',
		],
		'Group' => [
			'type' => 'text',
			'label-message' => 'contactpage-license-request-group',
		],
		'Title' => [
			'type' => 'text',
			'label-message' => 'contactpage-license-request-title',
		],
		'Org' => [
			'type' => 'text',
			'label-message' => 'contactpage-license-request-organization',
		],
		'OrgType' => [
			'type' => 'text',
			'label-message' => 'contactpage-license-request-organization-type',
		],
		'ProposedUse' => [
			'type' => 'selectorother',
			'label-message' => 'contactpage-license-request-use-proposed',
			'options-messages' => [
				'contactpage-license-request-use-online' => 'online',
				'contactpage-license-request-use-book' => 'book',
				'contactpage-license-request-use-print' => 'print',
				'contactpage-license-request-use-tv' => 'tv',
			],
			'default' => 'online',
			'required' => true,
		],

		'Description' => [
			'label-message' => 'contactpage-license-request-description',
			'type' => 'textarea',
			'rows' => 10,
			'cols' => 80,
			'required' => true,
		],

		'Trademark' => [
			'label-message' => 'contactpage-license-request-selectmark',
		] + $trademark,

		'use-note' => [
			'type' => 'info',
			'help-messages' => [ 'contactpage-license-request-use-note' ],
		],
	]
];

$wgContactConfig['licenseabuse'] = [
	'RecipientUser' => 'WMF Trademark Abuse',
	'SenderEmail' => $wmgNotificationSender, // TODO: Replace with details submitted on form
	'SenderName' => 'Contact Page',
	'RequireDetails' => true,
	'IncludeIP' => false,
	'AdditionalFields' => [
		'Description' => [
			'label-message' => 'contactpage-license-abuse-description',
			'type' => 'textarea',
			'rows' => 5,
			'cols' => 80,
			'required' => true,
		],
		'Location' => [
			'label-message' => 'contactpage-license-abuse-location',
			'type' => 'textarea',
			'rows' => 5,
			'cols' => 80,
			'required' => true,
		],
		'TheirContact' => [
			'label-message' => 'contactpage-license-abuse-theircontact',
			'type' => 'textarea',
			'rows' => 5,
			'cols' => 80,
			'required' => true,
		],
		'YourContact' => [
			'label-message' => 'contactpage-license-abuse-yourcontact',
			'type' => 'textarea',
			'rows' => 5,
			'cols' => 80,
			'required' => true,
		],
		'TradeMark' => [
			'label-message' => 'contactpage-license-abuse-selectmark',
		] + $trademark,
	]
];

unset( $trademark );
