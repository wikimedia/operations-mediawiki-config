<?php
/**
 * Configuration for [[:meta:Special:Contact/affcomusergroup]]
 *
 * The following css should be included on metawiki as well to style the form:
 *
 * body.page-Special_Contact_affcomusergroup .firstHeading {
 *   background-color: #2f6fab;
 *   color: #fff;
 *   padding: .5em;
 *   text-align: center;
 * }
 * #contactpage-affcomusergroup {
 *   width: inherit;
 * }
 * #contactpage-affcomusergroup .mw-ui-radio label,
 * #contactpage-affcomusergroup > div > :nth-child(1) label,
 * #contactpage-affcomusergroup > div > :nth-child(2) label {
 *   font-weight: bold;
 * }
 * #contactpage-affcomusergroup > div > :nth-child(3),
 * #contactpage-affcomusergroup > div > :nth-child(4) {
 *   display: none;
 * }
 * #contactpage-affcomusergroup label > ul {
 *   list-style: none;
 *   padding: 0;
 *   margin: 0;
 * }
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

$wgContactConfig['affcomusergroup'] = array(
	'RecipientUser' => 'Usergroups',
	'SenderName' => 'User group contact form on ' . $wgSitename,
	'SenderEmail' => null,
	'RequireDetails' => true,
	'IncludeIP' => false,
	'DisplayFormat' => 'vform',
	'AdditionalFields' => array(
		'GroupName' => array(
			'label-message' => 'contactpage-affcom-user-group-name-label',
			'contactpage-email-label' => 'Group name',
			'type' => 'text'
		),
		'GroupDescription' => array(
			'label-message' => 'contactpage-affcom-user-group-description-label',
			'contactpage-email-label' => 'Group description',
			'type' => 'textarea',
			'rows' => 10

		),
		'GroupWikiPage' => array(
			'label-message' => 'contactpage-affcom-user-group-wikipage-label',
			'contactpage-email-label' => 'Group wiki page',
			'type' => 'text'
		),
		'GroupLocation' => array(
			'label-message' => 'contactpage-affcom-user-group-location-label',
			'contactpage-email-label' => 'Group location',
			'type' => 'text'
		),
		'GroupLeaders' => array(
			'label-message' => 'contactpage-affcom-user-group-leaders-label',
			'contactpage-email-label' => 'Active Wikimedians',
			'type' => 'textarea',
			'rows' => 10
		),
		'GroupLogo' => array(
			'label-message' => 'contactpage-affcom-user-group-logo-label',
			'contactpage-email-label' => 'Logo',
			'type' => 'radio',
			'options-messages' => array(
				'contactpage-affcom-user-group-logo-community' => 'Wikimedia community',
				'contactpage-affcom-user-group-logo-affiliate' => 'Wikipedia affiliate'
			)
		),
		'Rules' => array(
			'label-message' => 'contactpage-affcom-user-group-rules-label',
			'type' => 'info',
		),
		'Terms' => array(
			'label-message' => 'contactpage-affcom-user-group-terms-label',
			'contactpage-email-label' => 'Terms',
			'type' => 'check',
			'required' => true,
			'validation-callback' => function ( $value ) { return !!$value; }
		)
	)
);
