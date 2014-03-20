$trademark = array(
	'type' => 'multiselect',
	'label-message' => 'contactpage-license-abuse-selectmark',
	'options-messages' => array(
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
	),
	'required' => true,
);

$wgContactConfig['requestlicense'] = array(
	'RecipientUser' => 'Reedy',
	'SenderEmail' => 'user@email.com',
	'SenderName' => 'User Email',
	'RequireDetails' => true,
	'IncludeIP' => false,
	'AdditionalFields' => array(
		'Username' => array(
			'type' => 'text',
			'label-message' => 'contactpage-license-request-username',
		),
		'Site' => array(
			'type' => 'text',
			'label-message' => 'contactpage-license-request-relevantsite',
		),
		'Group' => array(
			'type' => 'text',
			'label-message' => 'contactpage-license-request-group',
		),
		'Title' => array(
			'type' => 'text',
			'label-message' => 'contactpage-license-request-title',
		),
		'Org' => array(
			'type' => 'text',
			'label-message' => 'contactpage-license-request-organization',
			'help-messages' => array( 'contactpage-license-request-use-note' ),
		),
		'OrgType' => array(
			'type' => 'text',
			'label-message' => 'contactpage-license-request-organization-type',
		),
		'ProposedUse' => array(
			'type' => 'selectorother',
			'label-message' => 'contactpage-license-request-use-proposed',
			'options-messages' => array(
				'contactpage-license-request-use-online' => 'online',
				'contactpage-license-request-use-book' => 'book',
				'contactpage-license-request-use-print' => 'print',
				'contactpage-license-request-use-tv' => 'tv',
			),
			'default' => 'online',
			'required' => true,
		),

		'Description' => array(
			'label-message' => 'contactpage-license-request-description',
			'type' => 'textarea',
			'rows' => 10,
			'cols' => 80,
			'required' => true,
		),

		'Trademark' => $trademark
	)
);

$wgContactConfig['licenseabuse'] = array(
	'RecipientUser' => 'Reedy',
	'SenderEmail' => 'user@email.com',
	'SenderName' => 'User Email',
	'RequireDetails' => true,
	'IncludeIP' => false,
	'AdditionalFields' => array(
		'Description' => array(
			'label-message' => 'contactpage-license-abuse-description',
			'type' => 'textarea',
			'rows' => 5,
			'cols' => 80,
			'required' => true,
		),
		'Location' => array(
			'label-message' => 'contactpage-license-abuse-location',
			'type' => 'textarea',
			'rows' => 5,
			'cols' => 80,
			'required' => true,
		),
		'TheirContact' => array(
			'label-message' => 'contactpage-license-abuse-theircontact',
			'type' => 'textarea',
			'rows' => 5,
			'cols' => 80,
			'required' => true,
		),
		'YourContact' => array(
			'label-message' => 'contactpage-license-abuse-yourcontact',
			'type' => 'textarea',
			'rows' => 5,
			'cols' => 80,
			'required' => true,
		),
		'TradeMark' => $trademark
	)
);

unset( $trademark );
